<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MainController extends CI_Controller {

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
    public $db;
    public $form_validation;
    public $email;
    public $zend;
    public $patients;
    public $vaccines;
    public $vials;
    public $incidents;
    public $schedules;

    public function __construct() {
        parent::__construct();
        $this->load->model('users');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
    }

	public function index()
	{
		// Pass flashdata to the view
		$data = array();
		$data['login_error'] = $this->session->flashdata('login_error');
		$this->load->view('main/login', $data);
	}

    public function login() 
    {
		if(isset($_POST['loginBtn'])) {

            $username = $this->input->post('email');
            $password = $this->input->post('password');

            if(!empty($username) && !empty($password)) {

                if($this->users->checkLogin()) {

                    $this->session->set_userdata('user_id', $this->users->checkLogin());


                    redirect('dashboard');

                } else {

                    $this->session->set_flashdata('login_error', 'Incorrect email or password');
                    redirect('login');

                }

            } else {

                $this->session->set_flashdata('login_error', 'Email or password is empty.');
                redirect('login');

            }

        }

        // Pass flashdata to the view
        $data = array();
        $data['login_error'] = $this->session->flashdata('login_error');
        $this->load->view('main/login', $data);

    }

    public function logout()
    {
        $this->session->unset_userdata('user_id');
        redirect('login');
    }
}