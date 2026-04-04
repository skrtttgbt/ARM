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
        $this->load->model('Vaccines');
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

        if(isset($_POST['proceedTransaction'])) {
            $barcode = trim((string) $this->input->post('barcode'));

            if ($barcode === '') {
                $this->session->set_flashdata('barcode_error', 'Barcode is empty');
                redirect('schedule/proceed/' . $id);
                return;
            }

            $resolved = $this->resolveBarcodeDetails($barcode);

            if (!$resolved['success']) {
                $this->session->set_flashdata('barcode_error', $resolved['message']);
                redirect('schedule/proceed/' . $id);
                return;
            }

            $vaccine = $resolved['vaccine'];

            if ((int) $vaccine['quantity'] <= 0) {
                $this->session->set_flashdata('barcode_error', 'Available quantity is already zero.');
                redirect('schedule/proceed/' . $id);
                return;
            }

            $this->db->trans_start();
            $created_vial_id = $this->vials->createVialForVaccine((int) $session_id, (int) $vaccine['id']);
            $this->schedules->updateScheduleOngoingByVialId($id, $created_vial_id);
            $this->vaccines->deductQuantity($vaccine['id'], 1);

            $this->db->trans_complete();

            if (!$this->db->trans_status()) {
                $this->session->set_flashdata('barcode_error', 'Unable to complete the transaction right now.');
                redirect('schedule/proceed/' . $id);
                return;
            }

            $sms_message = sprintf(
                "Your vaccination schedule for %s has been completed using vaccine %s.",
                date('M j, Y', strtotime($schedule['schedule'])),
                $vaccine['name']
            );

            if (!$this->sendSmsNotification($patient['mobile'], $sms_message)) {
                log_message('error', 'Schedule proceed SMS notification failed for patient ID: ' . $patient['id']);
            }

            $this->session->set_flashdata('message', 'Schedule is now ongoing.');
            redirect('schedule');
            return;
        }

        $this->load->view('schedule/proceed', $data);
    }

    public function complete($id)
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $schedule = $this->schedules->getSchedule($id);
        if (!$schedule) {
            $this->session->set_flashdata('error', 'Schedule not found.');
            redirect('schedule');
            return;
        }

        $this->schedules->updateScheduleByCol($id, 'status', 1);
        $this->session->set_flashdata('message', 'Schedule is now completed.');
        redirect('schedule');
    }

    public function barcodeDetails($id)
    {
        if (!$this->session->userdata('user_id')) {
            return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $schedule = $this->schedules->getSchedule($id);
        if (!$schedule) {
            return $this->jsonResponse(['success' => false, 'message' => 'Schedule not found.'], 404);
        }

        $barcode = trim((string) $this->input->post('barcode'));
        if ($barcode === '') {
            return $this->jsonResponse(['success' => false, 'message' => 'Barcode is empty.'], 422);
        }

        $resolved = $this->resolveBarcodeDetails($barcode);
        if (!$resolved['success']) {
            return $this->jsonResponse(['success' => false, 'message' => $resolved['message']], 422);
        }

        $vaccine = $resolved['vaccine'];
        return $this->jsonResponse([
            'success' => true,
            'barcode' => $barcode,
            'can_proceed' => (int) $vaccine['quantity'] > 0,
            'message' => (int) $vaccine['quantity'] > 0 ? '' : 'Available quantity is already zero.',
            'vaccine' => [
                'id' => $vaccine['id'],
                'name' => $vaccine['name'],
                'type' => $vaccine['type'],
                'capacity' => $vaccine['capacity'],
                'amount' => $vaccine['amount'],
                'quantity' => $vaccine['quantity']
            ]
        ]);
    }

    private function sendSmsNotification($mobile, $message)
    {
        $mobile = normalize_ph_mobile($mobile);

        if ($mobile === '') {
            return false;
        }

        return send_unisms_sms($mobile, $message);
    }

    private function resolveBarcodeDetails($barcode)
    {
        $vaccine = $this->vaccines->getVaccineByBarcode($barcode);
        if (!$vaccine) {
            return ['success' => false, 'message' => 'Invalid barcode', 'vial' => null, 'vaccine' => null];
        }

        return ['success' => true, 'message' => '', 'vial' => null, 'vaccine' => $vaccine];
    }

    private function jsonResponse($payload, $status_code = 200)
    {
        return $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

}
