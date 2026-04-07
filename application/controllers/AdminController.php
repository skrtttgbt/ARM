<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminController extends CI_Controller {
    
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
    public $form_validation;
    public $db;
    public $email;
    public $zend;
    public $patients;
    public $vaccines;
    public $vials;
    public $incidents;
    public $schedules;

    public function __construct() {

        parent::__construct();
        
        // Load necessary models and libraries
        $this->load->model('Users');
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
        $data['admins'] = $this->users->getAdmins();

        $this->load->view('admin/index', $data);
    }

    public function create() 
    {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);

        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|required|min_length[2]|is_unique[users.email]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'trim|required|callback_valid_ph_mobile|callback_unique_user_mobile');

        if ($this->form_validation->run() == FALSE)
		{
            $this->load->view('admin/create', $data);
		}
		else
		{
			$this->users->createUser();
            $this->session->set_flashdata('message', 'New admin account created.');
			redirect('admin');
		}
    }

    public function account_reset($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $this->users->actionReset($id);
        $this->session->set_flashdata('message', 'Admin password has been reset. The new password will be sent to the admin\'s mobile number.');
		redirect('admin');

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
            redirect('admin');
            return;
        }

        $archive_reason = $this->input->post('archive_reason', TRUE);

        $session_id = $this->session->userdata('user_id');

        $this->users->actionSuspend($id, $archive_reason, $session_id);
        $this->session->set_flashdata('message', 'Admin account has been suspended. A notification has been sent to the admin\'s mobile number.');
		redirect('admin');

    }

    public function account_activate($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $this->users->actionActivate($id);
        $this->session->set_flashdata('message', 'Admin account has been reactivated. A notification has been sent to the admin\'s mobile number.');
		redirect('admin/archive');

    }

    public function archive() {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['admins'] = $this->users->getArchives();

        $this->load->view('admin/archive', $data);
    }

    public function valid_ph_mobile($mobile) {
        if (!is_valid_ph_mobile($mobile)) {
            $this->form_validation->set_message('valid_ph_mobile', 'The {field} must be a valid PH mobile number. You can enter +639XXXXXXXXX, 09XXXXXXXXX, or the last 9 digits only.');
            return FALSE;
        }
        return TRUE;
    }

    public function unique_user_mobile($mobile) {
        $normalized = normalize_ph_mobile($mobile);
        if ($normalized === '') {
            return TRUE;
        }

        $exists = $this->db->where('mobile', $normalized)->count_all_results('users') > 0;
        if ($exists) {
            $this->form_validation->set_message('unique_user_mobile', 'The {field} must be unique.');
            return FALSE;
        }
        return TRUE;
    }

}
