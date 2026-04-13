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
    public $vaccine_batches;

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
        $this->load->model('Vaccine_batches', 'vaccine_batches');

        // Load the dashboard content
        $data['user_info'] = $this->users->getUser($session_id);
        $data['total_patients'] = $this->patients->getTotalPatients();
        $data['total_incidents'] = $this->incidents->getTotalIncidents();
        $data['total_schedules_today'] = $this->schedules->getTodaySchedulesCount();
        $data['total_vaccines'] = $this->vaccines->getTotalVaccines();
        $data['total_vials'] = $this->vials->getTotalVials();
        $data['vaccines'] = $this->vaccines->getVaccines();
        $data['forecast_data'] = $this->getVialForecastData();
        $data['vaccine_forecast_data'] = $this->getVaccineForecastData();
        $data['vaccine_archive_summary'] = $this->getVaccineArchiveSummary();
        $data['expiring_batches'] = $this->vaccine_batches->getExpiringBatches(5);
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
        $this->load->helper('unisms');
        $this->config->load('unisms');

        $api_key = $this->config->item('unisms_api_key');
        if (!$api_key) {
            $this->session->set_flashdata('message', 'UniSMS API key is not configured.');
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

                if (send_unisms_sms($mobile, $message)) {
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

    private function getVaccineForecastData()
    {
        $start_date = new DateTimeImmutable('2021-01-01');
        $current_month = new DateTimeImmutable(date('Y-m-01'));
        $forecast_months = 1;
        $month_keys = [];
        $month_labels = [];
        $overall_actual = [];
        $vaxirab_actual = [];
        $speeda_actual = [];

        $cursor = $start_date;
        while ($cursor <= $current_month) {
            $date = $cursor->format('Y-m');
            $month_keys[] = $date;
            $month_labels[] = $cursor->format('M Y');
            $overall_actual[] = 0;
            $vaxirab_actual[] = 0;
            $speeda_actual[] = 0;

            $cursor = $cursor->modify('+1 month');
        }

        $this->db->select("
            DATE_FORMAT(s.schedule, '%Y-%m') AS month_key,
            COUNT(*) AS total_count,
            SUM(CASE WHEN va.name = 'VaxiRab N' THEN 1 ELSE 0 END) AS vaxirab_count,
            SUM(CASE WHEN va.name = 'SPEEDA' THEN 1 ELSE 0 END) AS speeda_count
        ", false);
        $this->db->from('schedules s');
        $this->db->join('vials v', 's.vial_id = v.id', 'left');
        $this->db->join('vaccines va', 'v.vaccine_id = va.id', 'left');
        $this->db->where('s.status', 1);
        $this->db->where('s.schedule IS NOT NULL', null, false);
        $this->db->where('s.schedule <>', '');
        $this->db->group_by("DATE_FORMAT(s.schedule, '%Y-%m')", false);
        $rows = $this->db->get()->result_array();

        $index_by_key = array_flip($month_keys);
        foreach ($rows as $row) {
            if (!isset($index_by_key[$row['month_key']])) {
                continue;
            }

            $idx = $index_by_key[$row['month_key']];
            $overall_actual[$idx] = (int) $row['total_count'];
            $vaxirab_actual[$idx] = (int) $row['vaxirab_count'];
            $speeda_actual[$idx] = (int) $row['speeda_count'];
        }

        $overall_prediction = $this->buildSarimaPredictionSeries($overall_actual, $forecast_months);
        $vaxirab_prediction = $this->buildSarimaPredictionSeries($vaxirab_actual, $forecast_months);
        $speeda_prediction = $this->buildSarimaPredictionSeries($speeda_actual, $forecast_months);

        $next_month_label = date('M Y', strtotime('+1 month'));
        $chart_months = $month_labels;
        $chart_months[] = $next_month_label;
        $prediction_start_index = 0;

        foreach ($month_keys as $index => $month_key) {
            if ($month_key >= '2022-01') {
                $prediction_start_index = $index;
                break;
            }
        }

        $chart_prediction = [];
        foreach ($overall_actual as $index => $value) {
            $chart_prediction[] = ($index >= $prediction_start_index && isset($overall_prediction[$index]))
                ? $overall_prediction[$index]
                : null;
        }
        $chart_prediction[] = end($overall_prediction);

        $chart_vaxirab = $vaxirab_actual;
        $chart_vaxirab[] = null;
        $chart_speeda = $speeda_actual;
        $chart_speeda[] = null;

        return [
            'predicted_next_month' => end($overall_prediction),
            'next_month_label' => $next_month_label,
            'chart_months' => $chart_months,
            'chart_prediction' => $chart_prediction,
            'chart_vaxirab' => $chart_vaxirab,
            'chart_speeda' => $chart_speeda
        ];
    }

    private function getVaccineArchiveSummary()
    {
        $used_query = $this->db->select('COUNT(*) AS total_used', false)->get('vials')->row_array();
        $used_total = isset($used_query['total_used']) ? (int) $used_query['total_used'] : 0;

        $log_query = $this->db->query("
            SELECT
                SUM(CASE WHEN LOWER(TRIM(reason)) IN ('damaged', 'damaged vial') THEN quantity_archived ELSE 0 END) AS damaged_total,
                SUM(CASE WHEN LOWER(TRIM(reason)) IN ('expired', 'expired stock') THEN quantity_archived ELSE 0 END) AS expired_total,
                SUM(CASE WHEN LOWER(TRIM(reason)) IN ('recall', 'recall from supplier') THEN quantity_archived ELSE 0 END) AS recall_total,
                SUM(CASE WHEN LOWER(TRIM(reason)) = 'inventory adjustment' THEN quantity_archived ELSE 0 END) AS inventory_adjustment_total
            FROM vaccine_archive_logs
        ")->row_array();

        return [
            'used_total' => $used_total,
            'damaged_total' => isset($log_query['damaged_total']) ? (int) $log_query['damaged_total'] : 0,
            'expired_total' => isset($log_query['expired_total']) ? (int) $log_query['expired_total'] : 0,
            'recall_total' => isset($log_query['recall_total']) ? (int) $log_query['recall_total'] : 0,
            'inventory_adjustment_total' => isset($log_query['inventory_adjustment_total']) ? (int) $log_query['inventory_adjustment_total'] : 0
        ];
    }
    
    private function getChartData() {
        $start_date = new DateTimeImmutable('2021-01-01');
        $current_month = new DateTimeImmutable(date('Y-m-01'));
        $forecast_months = 1;
        $months = [];
        $month_keys = [];
        $incident_counts = [];

        $cursor = $start_date;
        while ($cursor <= $current_month) {
            $date = $cursor->format('Y-m');
            $year = $cursor->format('Y');
            $month = $cursor->format('m');

            $this->db->select('COUNT(*) as count');
            $this->db->from('incidents');
            $this->db->where("STR_TO_DATE(created_at, '%M %e, %Y') IS NOT NULL", null, false);
            $this->db->where("YEAR(STR_TO_DATE(created_at, '%M %e, %Y')) = {$year}", null, false);
            $this->db->where("MONTH(STR_TO_DATE(created_at, '%M %e, %Y')) = {$month}", null, false);
            $result = $this->db->get()->row();

            $month_keys[] = $date;
            $months[] = $cursor->format('M Y');
            $incident_counts[] = $result ? (int)$result->count : 0;

            $cursor = $cursor->modify('+1 month');
        }

        $actual_series = $incident_counts;
        $prediction_series = $this->buildSarimaPredictionSeries($incident_counts, $forecast_months);
        $prediction_start_index = 0;

        foreach ($month_keys as $index => $month_key) {
            if ($month_key >= '2022-01') {
                $prediction_start_index = $index;
                break;
            }
        }

        $chart_prediction = [];
        foreach ($incident_counts as $index => $value) {
            $chart_prediction[] = ($index >= $prediction_start_index && isset($prediction_series[$index]))
                ? $prediction_series[$index]
                : null;
        }

        for ($i = 1; $i <= $forecast_months; $i++) {
            $future_date = date('Y-m', strtotime("+$i months"));
            $months[] = date('M Y', strtotime($future_date));
            $actual_series[] = null;
        }

        $chart_prediction[] = end($prediction_series);

        return [
            'months' => $months,
            'incident_counts' => $actual_series,
            'predicted_incident_counts' => $chart_prediction
        ];
    }

    private function buildSarimaPredictionSeries($incident_counts, $forecast_months)
    {
        $count = count($incident_counts);
        $seasonal_period = 12;

        if ($count === 0) {
            return array_fill(0, $forecast_months, 0);
        }

        if ($count <= $seasonal_period + 1) {
            $fallback = array_fill(0, $count - 1, null);
            $fallback[] = end($incident_counts);

            for ($i = 1; $i <= $forecast_months; $i++) {
                $fallback[] = (int) end($incident_counts);
            }

            return $fallback;
        }

        // SARIMA(0,1,0)(0,1,0,12): seasonal random-walk with first and seasonal differencing.
        // In-sample estimate: y_t = y_(t-1) + y_(t-12) - y_(t-13)
        $prediction_series = [];

        for ($index = 0; $index < $count; $index++) {
            if ($index < $seasonal_period + 1) {
                $prediction_series[] = null;
                continue;
            }

            $predicted = $incident_counts[$index - 1]
                + $incident_counts[$index - $seasonal_period]
                - $incident_counts[$index - $seasonal_period - 1];

            $prediction_series[] = max(0, (int) round($predicted));
        }

        $series_for_forecast = $incident_counts;
        for ($i = 0; $i < $forecast_months; $i++) {
            $last_index = count($series_for_forecast) - 1;
            $forecast = $series_for_forecast[$last_index]
                + $series_for_forecast[$last_index - $seasonal_period + 1]
                - $series_for_forecast[$last_index - $seasonal_period];

            $forecast = max(0, (int) round($forecast));
            $series_for_forecast[] = $forecast;
            $prediction_series[] = $forecast;
        }

        return $prediction_series;
    }
}
