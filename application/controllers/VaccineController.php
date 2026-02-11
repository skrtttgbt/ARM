<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VaccineController extends CI_Controller {
    
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

    public function __construct() {

        parent::__construct();
        
        // Load necessary models and libraries
        $this->load->model('Users');
        $this->load->model('Vaccines');
        $this->load->model('Vials');
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
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);

        if(isset($_POST['removeBarcode'])) {

            $this->session->unset_userdata('vaccine_barcode');
            redirect('vaccine/create');

        } else if(isset($_POST['checkBarcode'])) {

            $barcode = $this->input->post('barcode');

            if(!empty($barcode)) {

                if(!$this->vaccines->getVaccineByBarcode($barcode)) {

                    $this->session->set_userdata('vaccine_barcode', $barcode);

                } else {

                    $this->session->set_flashdata('barcode_error', 'Barcode is taken');
                    redirect('vaccine/create');
                }

            } else {
                $this->session->set_flashdata('barcode_error', 'Barcode is empty');
                redirect('vaccine/create');
            }
            
            $this->load->view('vaccine/create', $data);

        } else {

            $this->form_validation->set_rules('name', 'Vaccine Name', 'trim|required|min_length[2]');
            $this->form_validation->set_rules('description', 'Vaccine Description', 'trim|required|min_length[2]');
            $this->form_validation->set_rules('capacity', 'Vaccine Capacity', 'trim|required');
            $this->form_validation->set_rules('amount', 'Vaccine Amount', 'trim|required');

            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('vaccine/create', $data);
            }
            else
            {
                $this->vaccines->createVaccine();
                $this->session->set_flashdata('message', 'New vaccine is created.');
                redirect('vaccine');
            }

        }
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

        $this->form_validation->set_rules('name', 'Vaccine Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('description', 'Vaccine Description', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('capacity', 'Vaccine Capacity', 'trim|required');
        $this->form_validation->set_rules('amount', 'Vaccine Amount', 'trim|required');

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

    public function retreive($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $this->vaccines->removeVaccine($id);
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
        // Get vaccination history grouped by month for the last 6 months
        $this->load->model('Schedules');
        $this->load->model('Vials');
        
        $months = [];
        $vaccination_counts = [];
        
        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $year = substr($date, 0, 4);
            $month = substr($date, 5, 2);
            
            // Count completed vaccinations for this month
            $this->db->select('COUNT(*) as count');
            $this->db->from('schedules s');
            $this->db->join('vials v', 's.vial_id = v.id', 'inner');
            $this->db->where('s.status', 1); // Completed
            $this->db->where('YEAR(s.updated_at)', $year);
            $this->db->where('MONTH(s.updated_at)', $month);
            $result = $this->db->get()->row();
            
            $months[] = date('M Y', strtotime($date));
            $vaccination_counts[] = $result ? (int)$result->count : 0;
        }
        
        // Calculate trend using linear regression
        $n = count($vaccination_counts);
        $sum_x = 0;
        $sum_y = 0;
        $sum_xy = 0;
        $sum_x_squared = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $x = $i + 1; // Month index
            $y = $vaccination_counts[$i];
            $sum_x += $x;
            $sum_y += $y;
            $sum_xy += $x * $y;
            $sum_x_squared += $x * $x;
        }
        
        // Calculate slope and intercept for linear regression
        $slope = ($n * $sum_xy - $sum_x * $sum_y) / ($n * $sum_x_squared - $sum_x * $sum_x);
        $intercept = ($sum_y - $slope * $sum_x) / $n;
        
        // Predict next month value
        $next_month_index = $n + 1;
        $predicted_value = round($slope * $next_month_index + $intercept);
        $predicted_value = max(0, $predicted_value); // Ensure non-negative
        
        // Calculate additional stats
        $avg_monthly_usage = array_sum($vaccination_counts) / count($vaccination_counts);
        $current_inventory = $this->vials->getTotalVials();
        
        // Calculate suggested order amount
        $suggested_order = max(0, $predicted_value - $current_inventory);
        
        return [
            'months' => $months,
            'vaccination_counts' => $vaccination_counts,
            'predicted_next_month' => $predicted_value,
            'average_monthly_usage' => round($avg_monthly_usage, 2),
            'current_inventory' => $current_inventory,
            'suggested_order_amount' => $suggested_order,
            'trend_slope' => $slope
        ];
    }
}