<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProfileController extends CI_Controller {

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
    public $form_validation;
    public $db;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Users');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('phone');
    }

    public function index()
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $data['user_info'] = $this->users->getUser($user_id);

        if ($this->input->method() === 'post') {
            if ($this->input->post('updateProfile')) {
                $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|min_length[2]');
                $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|min_length[2]');
                $this->form_validation->set_rules('mobile', 'Mobile Number', 'trim|required|callback_valid_ph_mobile|callback_unique_user_mobile');

                if ($this->form_validation->run() !== FALSE) {
                    $update = [
                        'first_name' => $this->input->post('first_name'),
                        'last_name' => $this->input->post('last_name'),
                        'mobile' => normalize_ph_mobile($this->input->post('mobile')),
                        'updated_at' => time()
                    ];
                    $this->users->updateUser($user_id, $update);
                    $this->session->set_flashdata('message', 'Profile updated.');
                    redirect('profile');
                    return;
                }
            } elseif ($this->input->post('changePassword')) {
                $this->form_validation->set_rules('current_password', 'Current Password', 'trim|required');
                $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|min_length[6]');
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[new_password]');

                if ($this->form_validation->run() !== FALSE) {
                    $current = $this->input->post('current_password');
                    if (!$this->users->checkPassword($user_id, $current)) {
                        $this->session->set_flashdata('error', 'Current password is incorrect.');
                        redirect('profile');
                        return;
                    }
                    $this->users->updatePassword($user_id, $this->input->post('new_password'));
                    $this->session->set_flashdata('message', 'Password updated.');
                    redirect('profile');
                    return;
                }
            } elseif ($this->input->post('uploadPhoto')) {
                $config = [
                    'upload_path' => FCPATH . 'assets/avatar',
                    'allowed_types' => 'jpg|jpeg|png|gif',
                    'max_size' => 2048,
                    'encrypt_name' => TRUE
                ];

                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('profile_image')) {
                    $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                    redirect('profile');
                    return;
                }

                $file = $this->upload->data();
                $this->users->updateUser($user_id, ['image' => $file['file_name'], 'updated_at' => time()]);
                $this->session->set_flashdata('message', 'Profile picture updated.');
                redirect('profile');
                return;
            }
        }

        $this->load->view('main/profile', $data);
    }

    public function valid_ph_mobile($mobile) {
        if (!is_valid_ph_mobile($mobile)) {
            $this->form_validation->set_message('valid_ph_mobile', 'The {field} must be a valid PH mobile number (e.g., +639XXXXXXXXX).');
            return FALSE;
        }
        return TRUE;
    }

    public function unique_user_mobile($mobile) {
        $normalized = normalize_ph_mobile($mobile);
        if ($normalized === '') {
            return TRUE;
        }

        $user_id = $this->session->userdata('user_id');
        $exists = $this->db->where('mobile', $normalized)->where('id !=', $user_id)->count_all_results('users') > 0;
        if ($exists) {
            $this->form_validation->set_message('unique_user_mobile', 'The {field} must be unique.');
            return FALSE;
        }
        return TRUE;
    }

}
