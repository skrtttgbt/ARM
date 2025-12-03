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

        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

    }

    public function index() {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['admins'] = $this->users->getAdmins();

        $this->load->view('admin/index', $data);
    }

    public function create() 
    {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);

        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|required|min_length[2]|is_unique[users.email]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'trim|required|min_length[11]|is_unique[users.mobile]');

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

        $this->users->actionReset($id);
        $this->session->set_flashdata('message', 'Admin password has been reset. The new password will be sent to the admin’s mobile number.');
		redirect('admin');

    }

    public function account_suspend($id) {

        $this->users->actionSuspend($id);
        $this->session->set_flashdata('message', 'Admin account has been suspended. A notification has been sent to the admin’s mobile number.');
		redirect('admin');

    }

    public function account_activate($id) {

        $this->users->actionActivate($id);
        $this->session->set_flashdata('message', 'Admin account has been reactivated. A notification has been sent to the admin’s mobile number.');
		redirect('admin/archive');

    }

    public function archive() {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['admins'] = $this->users->getArchives();

        $this->load->view('admin/archive', $data);
    }

}