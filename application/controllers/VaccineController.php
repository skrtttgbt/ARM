<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VaccineController extends CI_Controller {
    private const VIALS_PER_BOX = 3;
    private const PATIENTS_PER_VIAL = 3;
    private const PATIENTS_PER_BOX = self::VIALS_PER_BOX * self::PATIENTS_PER_VIAL;
    
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
    public $vaccines;
    public $form_validation;
    public $db;
    public $email;
    public $zend;
    public $patients;
    public $vials;
    public $incidents;
    public $schedules;
    public $vaccine_batches;

    public function __construct() {

        parent::__construct();
        
        // Load necessary models and libraries
        $this->load->model('Users');
        $this->load->model('Vaccines');
        $this->load->model('Vials');
        $this->load->model('Vaccine_batches', 'vaccine_batches');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');

    }

    public function index() {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['vaccines'] = $this->vaccines->getVaccines();
        $data['expiring_batches'] = $this->vaccine_batches->getExpiringBatches();

        $this->load->view('vaccine/index', $data);
    }

    public function archive() {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['vaccines'] = $this->vaccines->getArchives();

        $this->load->view('vaccine/archive', $data);
    }

    public function create() {
        redirect('vaccine');
    }

    public function view($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['vaccine'] = $this->vaccines->getVaccine($id);

        $this->form_validation->set_rules('type', 'Vaccine Type', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('name', 'Vaccine Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('description', 'Vaccine Description', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('capacity', 'Vaccine Capacity', 'trim|required');
        $this->form_validation->set_rules('amount', 'Vaccine Amount', 'trim|required');
        $this->form_validation->set_rules('quantity', 'Vaccine Quantity', 'trim|required|integer|greater_than_equal_to[0]');

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('vaccine/view', $data);
        }
        else
        {
            $this->vaccines->updateVaccine($id);
            $this->session->set_flashdata('message', 'Vaccine is updated.');
            redirect('vaccine');
        }
    }

    public function analyze($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['vaccine'] = $this->vaccines->getVaccine($id);

        $this->load->view('vaccine/analyze', $data);
    }

    public function remove($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $this->vaccines->removeVaccine($id);
        $this->session->set_flashdata('message', 'Vaccine is removed.');
		redirect('vaccine');
    }

    public function archiveVaccine($id) {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');
        $user_info = $this->users->getUser($session_id);

        if (!$user_info || (int) $user_info['level'] !== 0) {
            $this->session->set_flashdata('message', 'Only the super admin can archive vaccines.');
            redirect('vaccine');
            return;
        }

        $quantity_input = trim((string) $this->input->post('quantity'));
        $password = (string) $this->input->post('password');
        $archive_reason = trim((string) $this->input->post('archive_reason'));
        $vaccine = $this->vaccines->getVaccine($id);

        if (!$vaccine) {
            $this->session->set_flashdata('message', 'Vaccine was not found.');
            redirect('vaccine');
            return;
        }

        if ($quantity_input === '' || filter_var($quantity_input, FILTER_VALIDATE_INT) === false) {
            $this->session->set_flashdata('message', 'Quantity must be a whole number.');
            redirect('vaccine');
            return;
        }

        $quantity = (int) $quantity_input;

        if ($quantity <= 0) {
            $this->session->set_flashdata('message', 'Quantity must be greater than 0.');
            redirect('vaccine');
            return;
        }

        if (!isset($vaccine['quantity']) || $quantity > (int) $vaccine['quantity']) {
            $this->session->set_flashdata('message', 'Archive quantity cannot be greater than the available quantity.');
            redirect('vaccine');
            return;
        }

        if ($archive_reason === '') {
            $this->session->set_flashdata('message', 'Archive reason is required.');
            redirect('vaccine');
            return;
        }

        if ($password === '' || !$this->users->checkPassword($session_id, $password)) {
            $this->session->set_flashdata('message', 'Super admin password is incorrect.');
            redirect('vaccine');
            return;
        }

        $this->vaccines->archiveVaccine($id, $quantity, $archive_reason, $session_id);
        $this->session->set_flashdata('message', 'Vaccine quantity updated after archiving.');
        redirect('vaccine');
    }

    public function addQuantity($id) {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');
        $user_info = $this->users->getUser($session_id);

        if (!$user_info || (int) $user_info['level'] !== 0) {
            $this->session->set_flashdata('message', 'Only the super admin can add vaccine quantity.');
            redirect('vaccine');
            return;
        }

        $boxes = (int) $this->input->post('quantity');
        $manufacture_date = trim((string) $this->input->post('manufacture_date'));
        $expiration_date = trim((string) $this->input->post('expiration_date'));

        if ($boxes <= 0) {
            $this->session->set_flashdata('message', 'Boxes must be greater than 0.');
            redirect('vaccine');
            return;
        }

        if (!$this->isValidDateValue($manufacture_date) || !$this->isValidDateValue($expiration_date)) {
            $this->session->set_flashdata('message', 'Manufacture date and expiration date are required.');
            redirect('vaccine');
            return;
        }

        if (strtotime($expiration_date) <= strtotime($manufacture_date)) {
            $this->session->set_flashdata('message', 'Expiration date must be later than the manufacture date.');
            redirect('vaccine');
            return;
        }

        $patient_quantity = $boxes * self::PATIENTS_PER_BOX;

        $this->db->trans_start();
        $this->vaccines->addQuantity($id, $patient_quantity);
        $this->vaccine_batches->addBatch($id, $session_id, $patient_quantity, $manufacture_date, $expiration_date);
        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            $this->session->set_flashdata('message', 'Unable to add quantity right now.');
            redirect('vaccine');
            return;
        }

        $this->session->set_flashdata('message', 'Vaccine stock added successfully.');
        redirect('vaccine');
    }

    private function isValidDateValue($date)
    {
        if ($date === '') {
            return false;
        }

        $timestamp = strtotime($date);
        return $timestamp !== false && date('Y-m-d', $timestamp) === $date;
    }

    public function retreive($id) {
        return $this->retrieve($id);
    }

    public function retrieve($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');
        $user_info = $this->users->getUser($session_id);

        if (!$user_info || (int) $user_info['level'] !== 0) {
            $this->session->set_flashdata('message', 'Only the super admin can retrieve archived vaccines.');
            redirect('vaccine/archive');
            return;
        }

        $vaccine = $this->vaccines->getVaccine($id);
        if (!$vaccine || (int) $vaccine['deleted'] <= 0) {
            $this->session->set_flashdata('message', 'No archived quantity found for this vaccine.');
            redirect('vaccine/archive');
            return;
        }

        $this->vaccines->retreiveVaccine($id);
        $this->session->set_flashdata('message', 'Vaccine is retrieved.');
		redirect('vaccine');
    }
    
    public function forecast() {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');
        
        $data['user_info'] = $this->users->getUser($session_id);
        $data['forecast_data'] = $this->getVaccineForecastData();
        $data['vaccines'] = $this->vaccines->getVaccines();
        
        $this->load->view('vaccine/forecast', $data);
    }
    
    private function getVaccineForecastData() {
        $start_date = new DateTimeImmutable('2021-01-01');
        $current_month = new DateTimeImmutable(date('Y-m-01'));
        $forecast_months = 3;
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

        for ($i = 1; $i <= $forecast_months; $i++) {
            $future_key = date('Y-m', strtotime("+$i months"));
            $month_labels[] = date('M Y', strtotime($future_key . '-01'));
            $overall_actual[] = null;
            $vaxirab_actual[] = null;
            $speeda_actual[] = null;
        }

        $prediction_start_index = 0;
        foreach ($month_keys as $index => $month_key) {
            if ($month_key >= '2022-01') {
                $prediction_start_index = $index;
                break;
            }
        }

        $chart_prediction = [];
        foreach (array_slice($overall_prediction, 0, count($month_keys)) as $index => $value) {
            $chart_prediction[] = $index >= $prediction_start_index ? $value : null;
        }

        while (count($chart_prediction) < count($month_keys)) {
            $chart_prediction[] = null;
        }

        for ($i = count($month_keys); $i < count($overall_prediction); $i++) {
            $chart_prediction[] = $overall_prediction[$i];
        }

        return [
            'months' => $month_labels,
            'overall_actual' => $overall_actual,
            'overall_prediction' => $chart_prediction,
            'vaxirab_actual' => $vaxirab_actual,
            'vaxirab_prediction' => $vaxirab_prediction,
            'speeda_actual' => $speeda_actual,
            'speeda_prediction' => $speeda_prediction,
            'predicted_next_month' => end($overall_prediction),
            'average_monthly_usage' => round(array_sum(array_filter($overall_actual, 'is_numeric')) / max(1, count($month_keys)), 2),
            'vaxirab_total' => array_sum(array_filter($vaxirab_actual, 'is_numeric')),
            'speeda_total' => array_sum(array_filter($speeda_actual, 'is_numeric'))
        ];
    }

    private function buildSarimaPredictionSeries($series, $forecast_months)
    {
        $count = count($series);
        $seasonal_period = 12;

        if ($count === 0) {
            return array_fill(0, $forecast_months, 0);
        }

        if ($count <= $seasonal_period + 1) {
            $fallback = array_fill(0, max(0, $count - 1), null);
            $last_value = (int) end($series);
            $fallback[] = $last_value;

            for ($i = 1; $i <= $forecast_months; $i++) {
                $fallback[] = $last_value;
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
            $differenced_series[$index] = $series[$index]
                - $series[$index - 1]
                - $series[$index - $seasonal_period]
                + $series[$index - $seasonal_period - 1];
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

            $predicted = $series[$index - 1]
                + $series[$index - $seasonal_period]
                - $series[$index - $seasonal_period - 1]
                + $differenced_prediction;

            $prediction_series[] = max(0, (int) round($predicted));
            $residuals[$index] = (float) $differenced_series[$index] - $differenced_prediction;
        }

        $series_for_forecast = $series;
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
