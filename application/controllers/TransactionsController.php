<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransactionsController extends CI_Controller {
    
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
        
        // Load necessary models
        $this->load->model('Users');
        $this->load->model('Patients');
        $this->load->model('Vaccines');
        $this->load->model('Vials');
        $this->load->model('Incidents');
        $this->load->model('Schedules');
        
        // Load libraries
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function index() {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }
        
        $session_id = $this->session->userdata('user_id');
        $data['user_info'] = $this->users->getUser($session_id);
        
        // Get transaction logs - combining data from different tables
        $data['completed_schedules'] = $this->getCompletedSchedules();
        $data['recent_incidents'] = $this->getRecentIncidents();
        $data['recent_patients'] = $this->getRecentPatients();
        $data['recent_vaccinations'] = $this->getRecentVaccinations();
        
        $this->load->view('main/transaction', $data);
    }
    
    private function getCompletedSchedules() {
        // Get completed schedules (status = 1)
        $this->db->select('s.*, i.animal_type, p.patient_first_name, p.patient_last_name, u.first_name as user_fname, u.last_name as user_lname');
        $this->db->from('schedules s');
        $this->db->join('incidents i', 's.incident_id = i.id', 'left');
        $this->db->join('patients p', 'i.patient_id = p.id', 'left');
        $this->db->join('users u', 's.user_id = u.id', 'left');
        $this->db->where('s.status', 1); // Completed schedules
        $this->db->order_by('s.updated_at', 'DESC');
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }
    
    private function getRecentIncidents() {
        // Get recently created incidents
        $this->db->select('i.*, p.patient_first_name, p.patient_last_name, u.first_name as user_fname, u.last_name as user_lname');
        $this->db->from('incidents i');
        $this->db->join('patients p', 'i.patient_id = p.id', 'left');
        $this->db->join('users u', 'i.user_id = u.id', 'left');
        $this->db->order_by('i.created_at', 'DESC');
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }
    
    private function getRecentPatients() {
        // Get recently registered patients
        $this->db->select('p.*, u.first_name as user_fname, u.last_name as user_lname');
        $this->db->from('patients p');
        $this->db->join('users u', 'p.user_id = u.id', 'left');
        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }
    
    private function getRecentVaccinations() {
        // Get recent vaccine usage (vials assigned to schedules)
        $this->db->select('va.type as vaccine_type, s.schedule, p.patient_first_name, p.patient_last_name');
        $this->db->from('schedules s');
        $this->db->join('incidents i', 's.incident_id = i.id', 'inner');
        $this->db->join('patients p', 'i.patient_id = p.id', 'inner');
        $this->db->join('vials v', 's.vial_id = v.id', 'left');
        $this->db->join('vaccines va', 'v.vaccine_id = va.id', 'left');
        $this->db->where('s.status', 1); // Completed vaccinations
        $this->db->order_by('s.updated_at', 'DESC');
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }
}