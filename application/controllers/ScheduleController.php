<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ScheduleController extends CI_Controller {

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
        // Load necessary models and libraries
        $this->load->model('Users');
        $this->load->model('Schedules');
        $this->load->model('Incidents');
        $this->load->model('Patients');
        $this->load->model('Vials');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');

    }

    public function index() {
        // Debug: Log that we're entering the index method
        log_message('debug', 'ScheduleController::index() called');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            log_message('debug', 'User not logged in, redirecting to login');
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');
        log_message('debug', 'User ID: ' . $session_id);

        $user_info = $this->users->getUser($session_id);
        log_message('debug', 'User info: ' . print_r($user_info, true));
        
        if (!$user_info) {
            log_message('error', 'User not found for ID: ' . $session_id);
            $this->session->set_flashdata('error', 'User not found.');
            redirect('login');
            return;
        }

        $schedules = $this->schedules->getTodaySchedule();
        log_message('debug', 'Schedules: ' . print_r($schedules, true));

        $data['user_info'] = $user_info;
        $data['schedules'] = $schedules;

        $this->load->view('schedule/index', $data);
    }

    public function future() {
        // Debug: Log that we're entering the future method
        log_message('debug', 'ScheduleController::future() called');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            log_message('debug', 'User not logged in, redirecting to login');
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');
        log_message('debug', 'User ID: ' . $session_id);

        $user_info = $this->users->getUser($session_id);
        log_message('debug', 'User info: ' . print_r($user_info, true));
        
        if (!$user_info) {
            log_message('error', 'User not found for ID: ' . $session_id);
            $this->session->set_flashdata('error', 'User not found.');
            redirect('login');
            return;
        }

        $schedules = $this->schedules->getTodaySchedule();
        log_message('debug', 'Schedules: ' . print_r($schedules, true));

        $data['user_info'] = $user_info;
        $data['schedules'] = $schedules;

        $this->load->view('schedule/future', $data);
    }

    public function proceed($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }
        
        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        
        // Check if schedule exists
        $schedule = $this->schedules->getSchedule($id);
        if (!$schedule) {
            $this->session->set_flashdata('error', 'Schedule not found.');
            redirect('schedule');
            return;
        }
        
        // Check if incident exists
        $incident = $this->incidents->getIncident($schedule['incident_id']);
        if (!$incident) {
            $this->session->set_flashdata('error', 'Incident not found.');
            redirect('schedule');
            return;
        }
        
        // Check if patient exists
        $patient = $this->patients->getPatient($incident['patient_id']);
        if (!$patient) {
            $this->session->set_flashdata('error', 'Patient not found.');
            redirect('schedule');
            return;
        }

        $data['schedule'] = $schedule;
        $data['incident'] = $incident;
        $data['patient'] = $patient;

        if(isset($_POST['sendNotif'])) {

            $mobile = normalize_ph_mobile($this->input->post('mobile'));
            if ($mobile === '') {
                $this->session->set_flashdata('error', 'Invalid mobile number.');
                redirect('schedule/proceed/' . $id);
            }

            $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
            $message = sprintf("you are next");
                
            $data2 = [
                'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
                'message' => $message,
                'phone_number' => $mobile
                ];
                
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded']);
            $response = curl_exec($ch);
            curl_close($ch);

            $this->session->set_flashdata('success', 'Notification has been sent.');
            redirect('schedule/proceed/' . $id);


        } else if(isset($_POST['checkBarcode'])) {

        
            $barcode = $this->input->post('barcode');

            if(!empty($barcode)) {

                if($this->vials->getVialByBarcode($barcode)) {


                    if($this->vials->getStock($barcode)) {
                        //$this->vials->activateVial($barcode);
                        $this->schedules->updateScheduleDone($id,$barcode);
                        $this->session->set_flashdata('message', 'Transaction complete');
                        redirect('schedule');
                    } else {
                        $this->session->set_flashdata('barcode_error', 'Vial is fully consumed');
                        redirect('schedule/proceed/' . $id);
                    }

                } else {

                    $this->session->set_flashdata('barcode_error', 'Invalid barcode');
                    redirect('schedule/proceed/' . $id);
                }

            } else {
                $this->session->set_flashdata('barcode_error', 'Barcode is empty');
                redirect('schedule/proceed/' . $id);
            }

        } else {
            $this->load->view('schedule/proceed', $data);
        }
    }

}
