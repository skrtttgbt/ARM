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
        
        // Load necessary models and libraries
        $this->load->model('Users');
        $this->load->model('Incidents');
        $this->load->model('Patients');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');

    }

    public function index() 
    {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['incidents'] = $this->incidents->getIncidents();
        $this->load->view('incident/index', $data);
    }


    public function create($id = null) 
    {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        // If no ID is provided, redirect to patient list
        if ($id === null) {
            $this->session->set_flashdata('error', 'Please select a patient to create an incident.');
            redirect('patient');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['patient'] = $this->patients->getPatient($id);

        // If patient doesn't exist, redirect to patient list
        if (!$data['patient']) {
            $this->session->set_flashdata('error', 'Patient not found.');
            redirect('patient');
            return;
        }

        // Get previous incidents for this patient
        $data['previous_incidents'] = $this->incidents->getIncidentsByPatientId($id);

        $this->form_validation->set_rules('type', 'Animal Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('bite_date', 'Bite Date', 'trim|required');
        $this->form_validation->set_rules('bite_place', 'Bite Place', 'trim|required');
        $this->form_validation->set_rules('amount', 'Dose Amount', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('incident/create', $data);
        }
        else
        {
            $result = $this->incidents->createIncident();

            if (empty($result['created'])) {
                $this->session->set_flashdata('error', 'Incident could not be created.');
                redirect('incident/create/' . $id);
                return;
            }

            if (!empty($result['sms_sent'])) {
                $this->session->set_flashdata('message', 'Incident is created and schedule SMS notification was sent.');
            } else {
                $sms_error_message = 'Incident is created, but schedule SMS notification was not sent.';

                if (!empty($result['reason'])) {
                    if ($result['reason'] === 'invalid_mobile') {
                        $sms_error_message = 'Incident is created, but schedule SMS notification was not sent because the patient mobile number is invalid.';
                    } elseif ($result['reason'] === 'sms_failed') {
                        $sms_error_message = 'Incident is created, but schedule SMS notification was not sent because the UniSMS request failed.';
                    }
                }

                $this->session->set_flashdata('message', $sms_error_message);
            }

            redirect('incident');
        }
    }

    public function create_schedule($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['incident'] = $this->incidents->getIncident($id);
        $data['patient'] = $this->patients->getPatient($data['incident']['patient_id']);
        
        // Check if incident exists
        if (!$data['incident']) {
            $this->session->set_flashdata('error', 'Incident not found.');
            redirect('incident');
            return;
        }
        
        // Check if patient exists
        if (!$data['patient']) {
            $this->session->set_flashdata('error', 'Patient not found.');
            redirect('incident');
            return;
        }

        $this->form_validation->set_rules('sched_date', 'Scheduled Date', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('incident/create_schedule', $data);
        }
        else
        {
            $result = $this->incidents->createSchedule();

            if (empty($result['created'])) {
                $this->session->set_flashdata('error', 'Schedule could not be created.');
                redirect('incident/create_schedule/' . $id);
                return;
            }

            if (!empty($result['sms_sent'])) {
                $this->session->set_flashdata('message', 'Schedule is created and SMS notification was sent.');
            } else {
                $sms_error_message = 'Schedule is created, but SMS notification was not sent.';

                if (!empty($result['reason'])) {
                    if ($result['reason'] === 'invalid_mobile') {
                        $sms_error_message = 'Schedule is created, but SMS notification was not sent because the patient mobile number is invalid.';
                    } elseif ($result['reason'] === 'sms_failed') {
                        $sms_error_message = 'Schedule is created, but SMS notification was not sent because the UniSMS request failed.';
                    }
                }

                $this->session->set_flashdata('message', $sms_error_message);
            }

            redirect('schedule');
        }
    }

    public function complete($id)
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $incident = $this->incidents->getIncident($id);

        if (!$incident) {
            $this->session->set_flashdata('error', 'Incident not found.');
            redirect('incident');
            return;
        }

        $this->incidents->updateIncidentByCol($id, 'status', 1);
        $this->session->set_flashdata('message', 'Incident marked as completed.');
        redirect('incident');
    }

}
