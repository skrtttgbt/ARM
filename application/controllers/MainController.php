<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MainController extends CI_Controller {
    private const OTP_RESEND_COOLDOWN = 60;

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
    public $patients;
    public $vaccines;
    public $vials;
    public $incidents;
    public $schedules;
    public $users;

    public function __construct() {
        parent::__construct();

        $this->load->model('Patients');
        $this->load->model('Vaccines');
        $this->load->model('Vials');
        $this->load->model('Incidents');
        $this->load->model('Schedules');
        $this->load->model('Users');
        $this->load->library('zend');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
    }

    public function index()
    {
        redirect('login');
    }

    public function login()
    {
        // If form is submitted
        if(isset($_POST['loginBtn'])) {

            // Set validation rules
            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run() == FALSE) {
                // Validation failed, show form with errors
                $data = array();
                $data['login_error'] = validation_errors();
                $this->load->view('main/login', $data);
            } else {
                // Validation passed, check credentials
                $id = $this->users->checkLogin();

                if($id) {
                    // Login successful, set session
                    $this->session->set_userdata('user_id', $id);
                    redirect('dashboard');

                } else {

                    $this->session->set_flashdata('login_error', 'Incorrect email or password');
                    redirect('login');

                }

            }

        }

        // Pass flashdata to the view
        $data = array();
        $data['login_error'] = $this->session->flashdata('login_error');
        $this->load->view('main/login', $data);

    }

    public function forgot_password() 
    {
        $data = array(
            'step' => 'request',
            'error_message' => '',
            'success_message' => '',
            'email' => '',
            'resend_seconds_remaining' => 0
        );

        if ($this->input->method() !== 'post') {
            $this->load->view('main/forgot_password', $data);
            return;
        }

        $action = (string) $this->input->post('action');
        $email = trim((string) $this->input->post('email'));
        $data['email'] = $email;
        $data['resend_seconds_remaining'] = $this->getOtpResendSecondsRemaining($email);

        if ($action === 'send_otp') {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

            if ($this->form_validation->run() === FALSE) {
                $data['error_message'] = validation_errors();
                $this->load->view('main/forgot_password', $data);
                return;
            }

            if ($data['resend_seconds_remaining'] > 0) {
                $data['step'] = 'verify';
                $data['error_message'] = 'Please wait ' . $data['resend_seconds_remaining'] . ' seconds before resending OTP.';
                $this->load->view('main/forgot_password', $data);
                return;
            }

            if ($this->users->createPasswordResetOtp($email)) {
                $data['step'] = 'verify';
                $this->setOtpResendCooldown($email);
                $data['resend_seconds_remaining'] = self::OTP_RESEND_COOLDOWN;
                $data['success_message'] = 'A reset OTP has been sent to the mobile number linked to this account.';
            } else {
                $data['error_message'] = 'Unable to send OTP. Please make sure the email exists and has a valid mobile number.';
            }

            $this->load->view('main/forgot_password', $data);
            return;
        }

        if ($action === 'verify_otp') {
            $data['step'] = 'verify';
            $otp = trim((string) $this->input->post('otp'));

            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('otp', 'OTP', 'required|numeric|exact_length[6]');
            $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm New Password', 'required|matches[new_password]');

            if ($this->form_validation->run() === FALSE) {
                $data['error_message'] = validation_errors();
                $this->load->view('main/forgot_password', $data);
                return;
            }

            if ($this->users->resetPasswordWithOtp($email, $otp, (string) $this->input->post('new_password'))) {
                $this->load->view('main/password_reset_success');
                return;
            }

            $data['error_message'] = 'Invalid or expired OTP. Please request a new code and try again.';
            $this->load->view('main/forgot_password', $data);
            return;
        }

        $data['error_message'] = 'Invalid password reset request.';
        $this->load->view('main/forgot_password', $data);
    }

    private function getOtpResendSecondsRemaining($email)
    {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            return 0;
        }

        $otp_resend_cooldowns = (array) $this->session->userdata('otp_resend_cooldowns');
        $last_sent_at = isset($otp_resend_cooldowns[$email]) ? (int) $otp_resend_cooldowns[$email] : 0;
        if ($last_sent_at <= 0) {
            return 0;
        }

        $seconds_remaining = self::OTP_RESEND_COOLDOWN - (time() - $last_sent_at);
        return max(0, $seconds_remaining);
    }

    private function setOtpResendCooldown($email)
    {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            return;
        }

        $otp_resend_cooldowns = (array) $this->session->userdata('otp_resend_cooldowns');
        $otp_resend_cooldowns[$email] = time();
        $this->session->set_userdata('otp_resend_cooldowns', $otp_resend_cooldowns);
    }

    public function reset_password() 
    {
        redirect('forgot_password');
    }

    public function logout()
    {
        $this->session->unset_userdata('user_id');
        redirect('login');
    }
}
