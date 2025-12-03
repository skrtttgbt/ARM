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

        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

    }

    public function dashboard() 
    {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $this->load->view('main/dashboard', $data);

    }

}