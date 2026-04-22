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
        $data['vaccines'] = $this->vaccines->getVaccines();
        $data['forecast_data'] = $this->getVialForecastData();
        $data['vaccine_forecast_data'] = $this->getVaccineForecastData();
        $data['vaccine_archive_summary'] = $this->getVaccineArchiveSummary();
        $data['expiring_batches'] = $this->vaccines->getExpiringVaccines(5);
        $data['overdue_followups'] = $this->getOverdueFollowups();
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

    public function remind_overdue($incident_id = 0)
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        if ($this->input->method() !== 'post') {
            redirect('dashboard');
            return;
        }

        $this->load->helper('phone');
        $this->load->helper('unisms');

        $followup = $this->getOverdueFollowupByIncidentId((int) $incident_id);
        if (!$followup) {
            $this->session->set_flashdata('message', 'Overdue follow-up record not found.');
            redirect('dashboard');
            return;
        }

        $mobile = normalize_ph_mobile($followup['mobile']);
        if ($mobile === '') {
            $this->session->set_flashdata('message', 'Reminder not sent because the patient mobile number is invalid.');
            redirect('dashboard');
            return;
        }

        $message = $this->buildOverdueReminderMessage(
            $followup['patient_name'],
            $followup['last_completed_schedule'],
            (int) $followup['days_since_last_dose']
        );

        if (send_unisms_sms($mobile, $message)) {
            $this->session->set_flashdata('message', 'Reminder sent to ' . $followup['patient_name'] . '.');
        } else {
            $this->session->set_flashdata('message', 'Reminder could not be sent to ' . $followup['patient_name'] . '.');
        }

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

    private function buildOverdueReminderMessage($patient_name, $last_vaccination_date, $days_overdue)
    {
        return "Dear {$patient_name},\n\n"
            . "Our records show that your follow-up vaccination is overdue.\n"
            . "Your last completed vaccination was on {$last_vaccination_date}, and it has been {$days_overdue} days since your last dose.\n\n"
            . "Please return to the clinic as soon as possible for your next vaccination.\n"
            . "Clinic hours: 8:00 AM - 5:00 PM.";
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
        $vaccine_series = [];

        $cursor = $start_date;
        while ($cursor <= $current_month) {
            $date = $cursor->format('Y-m');
            $month_keys[] = $date;
            $month_labels[] = $cursor->format('M Y');
            $overall_actual[] = 0;

            $cursor = $cursor->modify('+1 month');
        }

        $vaccine_rows = $this->db
            ->select('id, name, barcode')
            ->from('vaccines')
            ->where('name IS NOT NULL', null, false)
            ->where('name <>', '')
            ->order_by('name', 'ASC')
            ->get()
            ->result_array();

        foreach ($vaccine_rows as $vaccine_row) {
            $vaccine_key = (string) ((int) $vaccine_row['id']);
            $vaccine_series[$vaccine_key] = [
                'label' => $this->formatVaccineDisplayLabel($vaccine_row['name'], $vaccine_row['barcode']),
                'data' => array_fill(0, count($month_keys), 0)
            ];
        }

        $this->db->select("
            DATE_FORMAT(s.schedule, '%Y-%m') AS month_key,
            va.id AS vaccine_id,
            va.name AS vaccine_name,
            va.barcode AS vaccine_barcode,
            COUNT(*) AS total_count
        ", false);
        $this->db->from('schedules s');
        $this->db->join('vials v', 's.vial_id = v.id', 'left');
        $this->db->join('vaccines va', 'v.vaccine_id = va.id', 'left');
        $this->db->where('s.status', 1);
        $this->db->where('s.schedule IS NOT NULL', null, false);
        $this->db->where('s.schedule <>', '');
        $this->db->where('va.name IS NOT NULL', null, false);
        $this->db->where('va.name <>', '');
        $this->db->group_by(["DATE_FORMAT(s.schedule, '%Y-%m')", 'va.id', 'va.name', 'va.barcode'], false);
        $rows = $this->db->get()->result_array();

        $index_by_key = array_flip($month_keys);
        foreach ($rows as $row) {
            if (!isset($index_by_key[$row['month_key']])) {
                continue;
            }

            $idx = $index_by_key[$row['month_key']];
            $count = (int) $row['total_count'];
            $vaccine_key = (string) ((int) $row['vaccine_id']);

            $overall_actual[$idx] += $count;

            if (!isset($vaccine_series[$vaccine_key])) {
                $vaccine_series[$vaccine_key] = [
                    'label' => $this->formatVaccineDisplayLabel($row['vaccine_name'], $row['vaccine_barcode']),
                    'data' => array_fill(0, count($month_keys), 0)
                ];
            }

            $vaccine_series[$vaccine_key]['data'][$idx] = $count;
        }

        $overall_prediction = $this->buildSarimaPredictionSeries($overall_actual, $forecast_months);

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

        $chart_vaccine_series = [];
        foreach ($vaccine_series as $series_meta) {
            $series = $series_meta['data'];
            $series[] = null;
            $chart_vaccine_series[] = [
                'label' => $series_meta['label'],
                'data' => $series
            ];
        }

        $monthly_prediction_rows = [];
        $prediction_years = [];
        $vials_per_patient = 1;

        foreach ($month_labels as $index => $label) {
            if ($index < $prediction_start_index || !isset($overall_prediction[$index]) || $overall_prediction[$index] === null) {
                continue;
            }

            $month_key = $month_keys[$index];
            $predicted_value = (int) $overall_prediction[$index];
            $prediction_year = substr($month_key, 0, 4);

            $monthly_prediction_rows[] = [
                'month_key' => $month_key,
                'month_label' => $label,
                'year' => $prediction_year,
                'predicted_total' => $predicted_value,
                'required_vials' => (int) ceil($predicted_value * $vials_per_patient)
            ];

            $prediction_years[$prediction_year] = true;
        }

        $next_month_prediction = (int) end($overall_prediction);
        $next_month_key = date('Y-m', strtotime('+1 month'));
        $next_month_year = substr($next_month_key, 0, 4);
        $next_month_required_vials = (int) ceil($next_month_prediction * $vials_per_patient);

        $monthly_prediction_rows[] = [
            'month_key' => $next_month_key,
            'month_label' => $next_month_label,
            'year' => $next_month_year,
            'predicted_total' => $next_month_prediction,
            'required_vials' => $next_month_required_vials
        ];

        $prediction_years[$next_month_year] = true;

        return [
            'predicted_next_month' => $next_month_prediction,
            'next_month_label' => $next_month_label,
            'chart_months' => $chart_months,
            'chart_all_vaccines' => array_merge($overall_actual, [null]),
            'chart_prediction' => $chart_prediction,
            'chart_vaccine_series' => $chart_vaccine_series,
            'monthly_prediction_rows' => $monthly_prediction_rows,
            'prediction_years' => array_keys($prediction_years),
            'patients_per_vial' => $vials_per_patient,
            'next_month_required_vials' => $next_month_required_vials
        ];
    }

    private function formatVaccineDisplayLabel($name, $barcode)
    {
        $name = trim((string) $name);
        $barcode = trim((string) $barcode);

        if ($name === '') {
            return $barcode !== '' ? 'Barcode: ' . $barcode : 'Unnamed Vaccine';
        }

        return $barcode !== '' ? $name . ' (' . $barcode . ')' : $name;
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

    private function getOverdueFollowups()
    {
        $rows = $this->db
            ->select("
                i.id AS incident_id,
                i.dose AS required_doses,
                p.id AS patient_id,
                p.patient_first_name,
                p.patient_last_name,
                p.mobile,
                MAX(CASE WHEN s.status = 1 THEN s.schedule ELSE NULL END) AS last_completed_schedule,
                SUM(CASE WHEN s.status = 1 THEN 1 ELSE 0 END) AS completed_doses
            ", false)
            ->from('incidents i')
            ->join('patients p', 'p.id = i.patient_id', 'left')
            ->join('schedules s', 's.incident_id = i.id', 'left')
            ->group_by([
                'i.id',
                'i.dose',
                'p.id',
                'p.patient_first_name',
                'p.patient_last_name',
                'p.mobile'
            ])
            ->get()
            ->result_array();

        $overdue_followups = [];
        $today = new DateTimeImmutable(date('Y-m-d'));

        foreach ($rows as $row) {
            $required_doses = (int) $row['required_doses'];
            $completed_doses = (int) $row['completed_doses'];
            $last_completed_schedule = trim((string) $row['last_completed_schedule']);

            if ($required_doses <= 1 || $completed_doses <= 0 || $completed_doses >= $required_doses || $last_completed_schedule === '') {
                continue;
            }

            $last_schedule_date = DateTimeImmutable::createFromFormat('Y-m-d', date('Y-m-d', strtotime($last_completed_schedule)));
            if (!$last_schedule_date) {
                continue;
            }

            $days_since_last_dose = (int) $last_schedule_date->diff($today)->format('%r%a');
            if ($days_since_last_dose < 4) {
                continue;
            }

            $overdue_followups[] = [
                'incident_id' => (int) $row['incident_id'],
                'patient_id' => (int) $row['patient_id'],
                'patient_name' => trim((string) $row['patient_first_name'] . ' ' . (string) $row['patient_last_name']),
                'mobile' => (string) $row['mobile'],
                'required_doses' => $required_doses,
                'completed_doses' => $completed_doses,
                'remaining_doses' => max($required_doses - $completed_doses, 0),
                'last_completed_schedule' => $last_schedule_date->format('M j, Y'),
                'days_since_last_dose' => $days_since_last_dose
            ];
        }

        usort($overdue_followups, function ($left, $right) {
            return $right['days_since_last_dose'] <=> $left['days_since_last_dose'];
        });

        return $overdue_followups;
    }

    private function getOverdueFollowupByIncidentId($incident_id)
    {
        foreach ($this->getOverdueFollowups() as $followup) {
            if ((int) $followup['incident_id'] === (int) $incident_id) {
                return $followup;
            }
        }

        return null;
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

        // SARIMA(3,1,1)(1,1,1,12) approximation:
        // w_t = (1 - B)(1 - B^12)y_t
        // w_t = phi1*w_(t-1) + phi2*w_(t-2) + phi3*w_(t-3) + Phi1*w_(t-12)
        //       + theta1*e_(t-1) + Theta1*e_(t-12)
        // y_t = y_(t-1) + y_(t-12) - y_(t-13) + w_t
        $ar = [0.45, 0.25, 0.10];
        $seasonal_ar = 0.30;
        $ma = 0.35;
        $seasonal_ma = 0.20;
        $min_prediction_index = $seasonal_period + 13;

        $prediction_series = [];
        $residuals = array_fill(0, $count, 0.0);
        $differenced_series = array_fill(0, $count, null);

        for ($index = $seasonal_period + 1; $index < $count; $index++) {
            $differenced_series[$index] = $incident_counts[$index]
                - $incident_counts[$index - 1]
                - $incident_counts[$index - $seasonal_period]
                + $incident_counts[$index - $seasonal_period - 1];
        }

        for ($index = 0; $index < $count; $index++) {
            if ($index < $min_prediction_index) {
                $prediction_series[] = null;
                continue;
            }

            $differenced_prediction =
                ($ar[0] * (float) $differenced_series[$index - 1]) +
                ($ar[1] * (float) $differenced_series[$index - 2]) +
                ($ar[2] * (float) $differenced_series[$index - 3]) +
                ($seasonal_ar * (float) $differenced_series[$index - $seasonal_period]) +
                ($ma * (float) $residuals[$index - 1]) +
                ($seasonal_ma * (float) $residuals[$index - $seasonal_period]);

            $predicted = $incident_counts[$index - 1]
                + $incident_counts[$index - $seasonal_period]
                - $incident_counts[$index - $seasonal_period - 1]
                + $differenced_prediction;

            $prediction_series[] = max(0, (int) round($predicted));
            $residuals[$index] = (float) $differenced_series[$index] - $differenced_prediction;
        }

        $series_for_forecast = $incident_counts;
        $forecast_differenced_series = $differenced_series;
        $forecast_residuals = $residuals;
        for ($i = 0; $i < $forecast_months; $i++) {
            $last_index = count($series_for_forecast) - 1;
            $next_index = $last_index + 1;

            $differenced_forecast =
                ($ar[0] * (float) $forecast_differenced_series[$next_index - 1]) +
                ($ar[1] * (float) $forecast_differenced_series[$next_index - 2]) +
                ($ar[2] * (float) $forecast_differenced_series[$next_index - 3]) +
                ($seasonal_ar * (float) $forecast_differenced_series[$next_index - $seasonal_period]) +
                ($ma * (float) $forecast_residuals[$next_index - 1]) +
                ($seasonal_ma * (float) $forecast_residuals[$next_index - $seasonal_period]);

            $forecast = $series_for_forecast[$next_index - 1]
                + $series_for_forecast[$next_index - $seasonal_period]
                - $series_for_forecast[$next_index - $seasonal_period - 1]
                + $differenced_forecast;

            $forecast = max(0, (int) round($forecast));
            $series_for_forecast[] = $forecast;
            $forecast_differenced_series[$next_index] = $differenced_forecast;
            $forecast_residuals[$next_index] = 0.0;
            $prediction_series[] = $forecast;
        }

        return $prediction_series;
    }
}
