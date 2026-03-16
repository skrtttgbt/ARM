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
            $should_send_d2 = (int)$row['dose'] >= 2 && (int)$row['reminder_d2_sent'] === 0 && $today >= $due_d2;
            $should_send_d3 = (int)$row['dose'] >= 3 && (int)$row['reminder_d3_sent'] === 0 && $today >= $due_d3;

            if ($should_send_d2 || $should_send_d3) {
                $message = $this->buildVaccinationReminderMessage(
                    $patient_name,
                    $due_d2,
                    ((int)$row['dose'] >= 3) ? $due_d3 : null
                );

                if (send_semaphore_sms($api_key, $mobile, $message)) {
                    if ($should_send_d2) {
                        $this->incidents->updateIncidentByCol($row['incident_id'], 'reminder_d2_sent', 1);
                        $sent_d2++;
                    }

                    if ($should_send_d3) {
                        $this->incidents->updateIncidentByCol($row['incident_id'], 'reminder_d3_sent', 1);
                        $sent_d3++;
                    }
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

    private function buildVaccinationReminderMessage($patient_name, $dose2_date, $dose3_date = null)
    {
        $message = "Dear {$patient_name},\n\n";
        $message .= "This is a friendly reminder about your upcoming vaccination schedule:\n\n";
        $message .= "- 2nd Dose: " . date('m/d/Y', strtotime($dose2_date)) . ", 8:00 AM - 5:00 PM\n";

        if (!empty($dose3_date)) {
            $message .= "- 3rd Dose/Booster: " . date('m/d/Y', strtotime($dose3_date)) . ", 8:00 AM - 5:00 PM\n\n";
        } else {
            $message .= "\n";
        }

        $message .= "Reminder: The clinic is open 8:00 AM - 5:00 PM only.";

        return $message;
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
        $history_months = 12;
        $forecast_months = 3;
        $months = [];
        $incident_counts = [];

        for ($i = $history_months - 1; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $year = substr($date, 0, 4);
            $month = substr($date, 5, 2);

            $this->db->select('COUNT(*) as count');
            $this->db->from('incidents');
            $this->db->where("STR_TO_DATE(created_at, '%M %e, %Y') IS NOT NULL", null, false);
            $this->db->where("YEAR(STR_TO_DATE(created_at, '%M %e, %Y')) = {$year}", null, false);
            $this->db->where("MONTH(STR_TO_DATE(created_at, '%M %e, %Y')) = {$month}", null, false);
            $result = $this->db->get()->row();

            $months[] = date('M Y', strtotime($date));
            $incident_counts[] = $result ? (int)$result->count : 0;
        }

        $predicted_values = $this->predictIncidentCounts($incident_counts, $forecast_months);
        $actual_series = $incident_counts;
        $prediction_series = array_fill(0, count($incident_counts) - 1, null);
        $prediction_series[] = end($incident_counts);

        for ($i = 1; $i <= $forecast_months; $i++) {
            $future_date = date('Y-m', strtotime("+$i months"));
            $months[] = date('M Y', strtotime($future_date));
            $actual_series[] = null;
            $prediction_series[] = $predicted_values[$i - 1];
        }

        return [
            'months' => $months,
            'incident_counts' => $actual_series,
            'predicted_incident_counts' => $prediction_series
        ];
    }

    private function predictIncidentCounts($incident_counts, $forecast_months)
    {
        $count = count($incident_counts);
        if ($count === 0) {
            return array_fill(0, $forecast_months, 0);
        }

        if ($count === 1) {
            return array_fill(0, $forecast_months, (int) $incident_counts[0]);
        }

        $sum_x = 0;
        $sum_y = 0;
        $sum_xy = 0;
        $sum_x2 = 0;

        foreach ($incident_counts as $index => $value) {
            $x = $index + 1;
            $sum_x += $x;
            $sum_y += $value;
            $sum_xy += $x * $value;
            $sum_x2 += $x * $x;
        }

        $denominator = ($count * $sum_x2) - ($sum_x * $sum_x);
        $slope = $denominator !== 0 ? (($count * $sum_xy) - ($sum_x * $sum_y)) / $denominator : 0;
        $intercept = ($sum_y - ($slope * $sum_x)) / $count;

        $predictions = [];
        for ($i = 1; $i <= $forecast_months; $i++) {
            $x = $count + $i;
            $predictions[] = max(0, (int) round($intercept + ($slope * $x)));
        }

        return $predictions;
    }
}
