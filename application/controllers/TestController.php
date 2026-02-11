<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestController extends CI_Controller {

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
    public $schedules;
    public $incidents;
    public $patients;
    public $vials;
    public $db;
    public $form_validation;
    public $email;
    public $zend;
    public $vaccines;

    public function __construct() {
        parent::__construct();
        $this->load->model('Users');
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function index() {
        echo "Test controller is working!";
        
        // Test database connection through CodeIgniter
        $this->load->database();
        $query = $this->db->query("SELECT COUNT(*) as count FROM users");
        $row = $query->row();
        echo "<br>Number of users in database: " . $row->count;
    }

    public function session_test() {
        // Test session
        $this->session->set_userdata('test', 'session_working');
        echo "Session test: " . $this->session->userdata('test');
    }
}