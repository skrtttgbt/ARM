<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardController extends CI_Controller {
    
    // Declare properties to avoid PHP 8.2 deprecation warnings
    public $benchmark;
    public $hooks;
    public $config;
    public $log;
    public $utf8;
    public $uri;
    public $router;
    public $output;
    public $security;
    public $input;
    public $lang;
    public $session;
    public $users;
    public $incidents;
    public $patients;
    public $form_validation;
    public $db;
    public $email;
    public $zend;
    public $vaccines;
    public $vials;
    public $schedules;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Users');
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function dashboard() 
    {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        // Load models
        $this->load->model('Patients');
        $this->load->model('Incidents');
        $this->load->model('Schedules');
        $this->load->model('Vaccines');
        $this->load->model('Vials');

        // Load the dashboard content
        $data['user_info'] = $this->users->getUser($session_id);
        $data['total_patients'] = $this->patients->getTotalPatients();
        $data['total_incidents'] = $this->incidents->getTotalIncidents();
        $data['total_schedules_today'] = $this->schedules->getTodaySchedulesCount();
        $data['total_vaccines'] = $this->vaccines->getTotalVaccines();
        $data['total_vials'] = $this->vials->getTotalVials();
                $data['forecast_data'] = $this->getVialForecastData();
                        $data['chart_data'] = $this->getChartData();
        
        $this->load->view('main/dashboard', $data);
    }

    public function send_reminders()
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        if ($this->input->method() !== 'post') {
            redirect('dashboard');
            return;
        }

        $this->load->model('Incidents');
        $this->load->model('Schedules');
        $this->load->model('Patients');
        $this->load->helper('phone');
        $this->load->helper('semaphore');
        $this->config->load('semaphore');

        $api_key = $this->config->item('semaphore_api_key');
        if (!$api_key) {
            $this->session->set_flashdata('message', 'Semaphore API key is not configured.');
            redirect('dashboard');
            return;
        }

        $today = date('Y-m-d');
        $sent_d2 = 0;
        $sent_d3 = 0;
        $skipped = 0;

        $this->db->select('i.id as incident_id, i.dose, i.reminder_d2_sent, i.reminder_d3_sent, p.mobile, p.patient_first_name, p.patient_last_name, MIN(s.schedule) as dose1_date');
        $this->db->from('incidents i');
        $this->db->join('schedules s', 's.incident_id = i.id AND s.status = 1', 'inner');
        $this->db->join('patients p', 'p.id = i.patient_id', 'inner');
        $this->db->group_by('i.id');
        $rows = $this->db->get()->result_array();

        foreach ($rows as $row) {
            if (empty($row['dose1_date'])) {
                $skipped++;
                continue;
            }

            $dose1_date = date('Y-m-d', strtotime($row['dose1_date']));
            $due_d2 = date('Y-m-d', strtotime($dose1_date . ' +3 days'));
            $due_d3 = date('Y-m-d', strtotime($due_d2 . ' +7 days'));

            $mobile = normalize_ph_mobile($row['mobile']);
            if ($mobile === '') {
                $skipped++;
                continue;
            }

            $patient_name = trim($row['patient_first_name'] . ' ' . $row['patient_last_name']);

            if ((int)$row['dose'] >= 2 && (int)$row['reminder_d2_sent'] === 0 && $today >= $due_d2) {
                $message = "Reminder: Your 2nd dose is due. Please visit the clinic. Patient: " . $patient_name;
                if (send_semaphore_sms($api_key, $mobile, $message)) {
                    $this->incidents->updateIncidentByCol($row['incident_id'], 'reminder_d2_sent', 1);
                    $sent_d2++;
                } else {
                    $skipped++;
                }
            }

            if ((int)$row['dose'] >= 3 && (int)$row['reminder_d3_sent'] === 0 && $today >= $due_d3) {
                $message = "Reminder: Your 3rd dose is due. Please visit the clinic. Patient: " . $patient_name;
                if (send_semaphore_sms($api_key, $mobile, $message)) {
                    $this->incidents->updateIncidentByCol($row['incident_id'], 'reminder_d3_sent', 1);
                    $sent_d3++;
                } else {
                    $skipped++;
                }
            }
        }

        $this->session->set_flashdata(
            'message',
            "Reminders sent. Dose 2: {$sent_d2}, Dose 3: {$sent_d3}, Skipped: {$skipped}."
        );
        redirect('dashboard');
    }
    
    private function getVialForecastData() {
        // Get total available vials
        $this->load->model('Vials');
        $total_vials = $this->vials->getTotalVials();
        
        // Get total scheduled vaccinations (pending and completed)
        $this->load->model('Schedules');
        $this->db->where_in('status', [0, 1]); // 0=pending, 1=completed (excluding 2=cancelled)
        $scheduled_count = $this->db->count_all_results('schedules');
        
        // Get completed schedules (already used vials)
        $this->db->where('status', 1); // completed schedules
        $completed_schedules = $this->db->count_all_results('schedules');
        
        // Calculate forecast data
        $used_vials = $completed_schedules;
        $available_vials = $total_vials - $used_vials;
        $pending_schedules = $scheduled_count - $completed_schedules;
        
        // Calculate projected shortage/excess
        $projected_shortage = max(0, $pending_schedules - $available_vials);
        $projected_excess = max(0, $available_vials - $pending_schedules);
        
        // Calculate percentage for visualization
        $usage_percentage = $total_vials > 0 ? round(($used_vials / $total_vials) * 100, 2) : 0;
        $availability_percentage = $total_vials > 0 ? round(($available_vials / $total_vials) * 100, 2) : 0;
        
        // Determine stock status
        $stock_status = 'adequate';
        if ($available_vials <= 10) {
            $stock_status = 'critical'; // Critical if 10 or fewer vials remain
        } elseif ($available_vials <= 30) {
            $stock_status = 'low'; // Low if 30 or fewer vials remain
        }
        
        return [
            'total_vials' => $total_vials,
            'used_vials' => $used_vials,
            'available_vials' => $available_vials,
            'pending_schedules' => $pending_schedules,
            'projected_shortage' => $projected_shortage,
            'projected_excess' => $projected_excess,
            'usage_percentage' => $usage_percentage,
            'availability_percentage' => $availability_percentage,
            'stock_status' => $stock_status
        ];
    }
    
    private function getChartData() {
        // Get monthly incident counts for the last 6 months
        $months = [];
        $incident_counts = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $year = substr($date, 0, 4);
            $month = substr($date, 5, 2);
            
            // Count incidents for this month
            $this->db->select('COUNT(*) as count');
            $this->db->from('incidents');
            $this->db->where('YEAR(created_at)', $year);
            $this->db->where('MONTH(created_at)', $month);
            $result = $this->db->get()->row();
            
            $months[] = date('M Y', strtotime($date));
            $incident_counts[] = $result ? (int)$result->count : 0;
        }
        
        // Get monthly vaccination counts for the last 6 months
        $vaccination_counts = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $year = substr($date, 0, 4);
            $month = substr($date, 5, 2);
            
            // Count completed schedules for this month
            $this->db->select('COUNT(*) as count');
            $this->db->from('schedules s');
            $this->db->join('vials v', 's.vial_id = v.id', 'inner');
            $this->db->where('s.status', 1); // Completed
            $this->db->where('YEAR(s.updated_at)', $year);
            $this->db->where('MONTH(s.updated_at)', $month);
            $result = $this->db->get()->row();
            
            $vaccination_counts[] = $result ? (int)$result->count : 0;
        }
        
        return [
            'months' => array_reverse($months),
            'incident_counts' => array_reverse($incident_counts),
            'vaccination_counts' => array_reverse($vaccination_counts)
        ];
    }
}
