<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MainController extends CI_Controller {

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
        // If form is submitted
        if(isset($_POST['resetBtn'])) {
            // Set validation rules
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            
            if ($this->form_validation->run() == FALSE) {
                // Validation failed, show form with errors
                $data = array();
                $data['error_message'] = validation_errors();
                $this->load->view('main/forgot_password', $data);
            } else {
                // Validation passed, check if email exists
                $email = $this->input->post('email');
                $user = $this->users->getUserByEmail($email);
                
                if($user) {
                    // User exists, generate reset token and send via SMS
                    if($this->users->resetPassword($email)) {
                        // Show success message
                        $data = array();
                        $data['success_message'] = 'Password reset instructions have been sent to your mobile phone.';
                        $this->load->view('main/forgot_password', $data);
                    } else {
                        // Password reset failed
                        $data = array();
                        $data['error_message'] = 'Failed to process password reset. Please try again.';
                        $this->load->view('main/forgot_password', $data);
                    }
                } else {
                    // User doesn't exist
                    $data = array();
                    $data['error_message'] = 'No account found with that email address.';
                    $this->load->view('main/forgot_password', $data);
                }
            }
        } else {
            // Show the forgot password form
            $this->load->view('main/forgot_password');
        }
    }

    public function reset_password() 
    {
        // Get token and email from GET parameters
        $token = $this->input->get('token');
        $email = $this->input->get('email');
        
        // If form is submitted
        if(isset($_POST['resetBtn'])) {
            // Get values from POST
            $token = $this->input->post('token');
            $email = $this->input->post('email');
            
            // Set validation rules
            $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm New Password', 'required|matches[new_password]');
            
            if ($this->form_validation->run() == FALSE) {
                // Validation failed, show form with errors
                $data = array();
                $data['token'] = $token;
                $data['email'] = $email;
                $data['error_message'] = validation_errors();
                $this->load->view('main/reset_password', $data);
            } else {
                // Validation passed, check reset token
                $new_password = $this->input->post('new_password');
                
                // Validate reset token and reset password
                if($this->users->resetPasswordWithToken($token, $new_password)) {
                    // Password reset successful
                    $this->load->view('main/password_reset_success');
                } else {
                    // Invalid reset token
                    $data = array();
                    $data['token'] = $token;
                    $data['email'] = $email;
                    $data['error_message'] = 'Invalid or expired reset token. Please request a new password reset.';
                    $this->load->view('main/reset_password', $data);
                }
            }
        } else {
            // Show the reset password form if we have a valid token
            if ($token && $email) {
                // Validate the token
                $user = $this->users->validateResetToken($token);
                if ($user && $user['email'] === $email) {
                    // Valid token, show the reset form
                    $data = array();
                    $data['token'] = $token;
                    $data['email'] = $email;
                    $this->load->view('main/reset_password', $data);
                } else {
                    // Invalid token, show error
                    $data = array();
                    $data['error_message'] = 'Invalid or expired reset token. Please request a new password reset.';
                    $this->load->view('main/forgot_password', $data);
                }
            } else {
                // No token, redirect to forgot password
                redirect('forgot_password');
            }
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('user_id');
        redirect('login');
    }
}