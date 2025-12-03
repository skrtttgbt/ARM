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

        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

    }

    public function index() {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['patients'] = $this->patients->getPatients();


        $this->load->view('patient/index', $data);

    }

    public function create() {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);

        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('birthday', 'Birthday', 'trim|required|callback_valid_birthday');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'trim|required|min_length[11]|is_unique[patients.mobile]');
        $this->form_validation->set_rules('relationship', 'Relationship', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('account_number', 'Account Number', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('account_first_name', 'Member First Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('account_last_name', 'Member Last Name', 'trim|required|min_length[2]');

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
        if ($age < 1) {
            $this->form_validation->set_message('valid_birthday', 'You must be at least 1 year old.');
            return FALSE;
        }

        return TRUE;
    }

    public function archive() {
        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['patients'] = $this->patients->getArchives();

        $this->load->view('patient/archive', $data);
    }

    public function view($id) {
        
    }

    public function account_suspend($id) {

        $this->patients->actionSuspend($id);
        $this->session->set_flashdata('message', 'Patient has been suspended.');
		redirect('patient');

    }

    public function account_activate($id) {

        $this->patients->actionActivate($id);
        $this->session->set_flashdata('message', 'Patient has been reactivated.');
		redirect('patient/archive');

    }

}