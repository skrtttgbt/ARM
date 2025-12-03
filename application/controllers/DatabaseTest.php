<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DatabaseTest extends CI_Controller {

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
		$this->load->database();
		
		// Test database connection
		if ($this->db->conn_id) {
			echo "Database connection successful!<br>";
			
			// Test if we can query the users table
			$query = $this->db->query("SELECT COUNT(*) as count FROM users");
			if ($query) {
				$row = $query->row();
				echo "Users table has " . $row->count . " records.<br>";
			} else {
				echo "Failed to query users table.<br>";
			}
		} else {
			echo "Database connection failed!<br>";
			echo "Error: " . $this->db->error();
		}
	}
}