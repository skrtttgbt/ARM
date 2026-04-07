<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PatientController extends CI_Controller {
    
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
    public $patients;
    public $form_validation;
    public $db;
    public $email;
    public $zend;
    public $vaccines;
    public $vials;
    public $incidents;
    public $schedules;

    public function __construct() {

        parent::__construct();
        
        // Load necessary models and libraries
        $this->load->model('Users');
        $this->load->model('Patients');
        $this->load->model('Incidents');
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
        $data['patients'] = $this->patients->getPatients();

        $this->load->view('patient/index', $data);
    }

    public function create() {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);

        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('birthday', 'Birthday', 'trim|required|callback_valid_birthday');
        $this->form_validation->set_rules('height', 'Height', 'trim|required|numeric');
        $this->form_validation->set_rules('weight', 'Weight', 'trim|required|numeric');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'trim|required|callback_valid_ph_mobile|callback_unique_patient_mobile');
        $this->form_validation->set_rules('relationship', 'Relationship', 'trim|callback_relationship_required_if_dependent');
        $this->form_validation->set_rules('account_number', 'Account Number', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('account_first_name', 'Member First Name', 'trim|callback_member_name_required_if_dependent');
        $this->form_validation->set_rules('account_last_name', 'Member Last Name', 'trim|callback_member_name_required_if_dependent');

        if ($this->form_validation->run() == FALSE)
		{
            $this->load->view('patient/create', $data);
		}
		else
		{
			$this->patients->createPatient();
            $this->session->set_flashdata('message', 'New patient account created.');
			redirect('patient');
		}

    }

    public function valid_birthday($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            $this->form_validation->set_message('valid_birthday', 'The {field} must be a valid date.');
            return FALSE;
        }

        $age = $d->diff(new DateTime())->y;
        if ($age < 18) {
            $this->form_validation->set_message('valid_birthday', 'This system is for adults only. Patient must be at least 18 years old.');
            return FALSE;
        }

        return TRUE;
    }

    public function archive() {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['patients'] = $this->patients->getArchives();

        $this->load->view('patient/archive', $data);
    }

    public function view($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }
        
        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['patient'] = $this->patients->getPatient($id);

        // If patient doesn't exist, redirect to patient list
        if (!$data['patient']) {
            $this->session->set_flashdata('error', 'Patient not found.');
            redirect('patient');
            return;
        }

        $this->load->view('patient/profile', $data);
    }

    public function history($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['patient'] = $this->patients->getPatient($id);
        $data['incidents'] = $this->incidents->getIncidentsByPatientId($id);

        // If patient doesn't exist, redirect to patient list
        if (!$data['patient']) {
            $this->session->set_flashdata('error', 'Patient not found.');
            redirect('patient');
            return;
        }

        $this->load->view('patient/history', $data);
    }

    public function account_suspend($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        if (strtoupper($this->input->method()) !== 'POST') {
            show_error('Invalid request method.', 405);
            return;
        }

        $this->form_validation->set_rules('archive_reason', 'Archive Reason', 'trim|required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('message', validation_errors('', ''));
            redirect('patient');
            return;
        }

        $archive_reason = $this->input->post('archive_reason', TRUE);
        $session_id = $this->session->userdata('user_id');

        $this->patients->actionSuspend($id, $archive_reason, $session_id);
        $this->session->set_flashdata('message', 'Patient has been suspended.');
		redirect('patient');

    }

    public function account_activate($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $this->patients->actionActivate($id);
        $this->session->set_flashdata('message', 'Patient has been reactivated.');
		redirect('patient/archive');

    }

    public function valid_ph_mobile($mobile) {
        if (!is_valid_ph_mobile($mobile)) {
            $this->form_validation->set_message('valid_ph_mobile', 'The {field} must be a valid PH mobile number (e.g., +639XXXXXXXXX).');
            return FALSE;
        }
        return TRUE;
    }

    public function relationship_required_if_dependent($relationship)
    {
        $type = $this->input->post('type');
        if ($type === 'Dependent' && trim((string)$relationship) === '') {
            $this->form_validation->set_message('relationship_required_if_dependent', 'The {field} is required when Account Type is Dependent.');
            return FALSE;
        }
        return TRUE;
    }

    public function member_name_required_if_dependent($value)
    {
        $type = $this->input->post('type');
        if ($type === 'Dependent' && strlen(trim((string) $value)) < 2) {
            $this->form_validation->set_message('member_name_required_if_dependent', 'The {field} is required when Account Type is Dependent and must be at least 2 characters.');
            return FALSE;
        }

        return TRUE;
    }

    public function unique_patient_mobile($mobile) {
        $normalized = normalize_ph_mobile($mobile);
        if ($normalized === '') {
            return TRUE;
        }

        $exists = $this->db->where('mobile', $normalized)->count_all_results('patients') > 0;
        if ($exists) {
            $this->form_validation->set_message('unique_patient_mobile', 'The {field} must be unique.');
            return FALSE;
        }
        return TRUE;
    }

}
