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

        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

    }

    public function index() {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['schedules'] = $this->schedules->getTodaySchedule();

        $this->load->view('schedule/index', $data);

    }

    public function future() {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['schedules'] = $this->schedules->getTodaySchedule();

        $this->load->view('schedule/future', $data);

    }

    public function proceed($id) {
        
        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['schedule'] = $this->schedules->getSchedule($id);
        $data['incident'] = $this->incidents->getIncident($data['schedule']['incident_id']);
        $data['patient'] = $this->patients->getPatient($data['incident']['patient_id']);

        if(isset($_POST['sendNotif'])) {

            $mobile = $this->input->post('mobile');

            $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
            $message = sprintf("you are next");
                
            $data2 = [
                'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
                'message' => $message,
                'phone_number' => "+" . $mobile
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
            

        }

        $this->load->view('schedule/proceed', $data);

    }
}