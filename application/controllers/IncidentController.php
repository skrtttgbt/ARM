<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class IncidentController extends CI_Controller {
    
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
    public $incidents;
    public $patients;
    public $form_validation;
    public $db;
    public $email;
    public $zend;
    public $vaccines;
    public $vials;
    public $schedules;

    public function __construct() {

        parent::__construct();

        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

    }

    public function index() 
    {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['incidents'] = $this->incidents->getIncidents();
        $this->load->view('incident/index', $data);

    }


    public function create($id) 
    {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['patient'] = $this->patients->getPatient($id);

        $this->form_validation->set_rules('type', 'Animal Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('bite_date', 'Bite Date', 'trim|required');
        $this->form_validation->set_rules('bite_place', 'Bite Place', 'trim|required');
        $this->form_validation->set_rules('height', 'Height', 'trim|required');
        $this->form_validation->set_rules('weight', 'Weight', 'trim|required');
        $this->form_validation->set_rules('amount', 'Dose Amount', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('incident/create', $data);
        }
        else
        {
            $this->incidents->createIncident();
            $this->session->set_flashdata('message', 'Incident is created.');
            redirect('incident');
        }
    }

    public function create_schedule($id) {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['incident'] = $this->incidents->getIncident($id);
        $data['patient'] = $this->patients->getPatient($data['incident']['patient_id']);
        

        $this->form_validation->set_rules('sched_date', 'Scheduled Date', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('incident/create_schedule', $data);
        }
        else
        {
            $this->incidents->createSchedule();
            $this->session->set_flashdata('message', 'Schedule is created.');
            redirect('schedule');
        }
    }

}