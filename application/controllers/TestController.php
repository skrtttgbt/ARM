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
    public $db;
    public $form_validation;
    public $email;
    public $zend;
    public $users;
    public $patients;
    public $vaccines;
    public $vials;
    public $incidents;
    public $schedules;

	public function index()
	{
		echo "Test controller is working!";
	}
	
	public function test_db()
	{
		$this->load->database();
		if ($this->db->conn_id) {
			echo "Database connection successful!";
		} else {
			echo "Database connection failed!";
		}
	}
}