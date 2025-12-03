<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VaccineController extends CI_Controller {
    
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
    public $vaccines;
    public $form_validation;
    public $db;
    public $email;
    public $zend;
    public $patients;
    public $vials;
    public $incidents;
    public $schedules;

    public function __construct() {

        parent::__construct();

        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

    }

    public function index() {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['vaccines'] = $this->vaccines->getVaccines();

        $this->load->view('vaccine/index', $data);

    }

    public function archive() {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['vaccines'] = $this->vaccines->getArchives();

        $this->load->view('vaccine/archive', $data);

    }

    public function create() {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);

        if(isset($_POST['removeBarcode'])) {

            $this->session->unset_userdata('vaccine_barcode');
            redirect('vaccine/create');

        } else if(isset($_POST['checkBarcode'])) {

            $barcode = $this->input->post('barcode');

            if(!empty($barcode)) {

                if(!$this->vaccines->getVaccineByBarcode($barcode)) {

                    $this->session->set_userdata('vaccine_barcode', $barcode);

                } else {

                    $this->session->set_flashdata('barcode_error', 'Barcode is taken');
                    redirect('vaccine/create');
                }

            } else {
                $this->session->set_flashdata('barcode_error', 'Barcode is empty');
                redirect('vaccine/create');
            }
            
            $this->load->view('vaccine/create', $data);

        } else {

            $this->form_validation->set_rules('name', 'Vaccine Name', 'trim|required|min_length[2]');
            $this->form_validation->set_rules('description', 'Vaccine Description', 'trim|required|min_length[2]');
            $this->form_validation->set_rules('capacity', 'Vaccine Capacity', 'trim|required');
            $this->form_validation->set_rules('amount', 'Vaccine Amount', 'trim|required');

            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('vaccine/create', $data);
            }
            else
            {
                $this->vaccines->createVaccine();
                $this->session->set_flashdata('message', 'New vaccine is created.');
                redirect('vaccine');
            }

        }


        
    }

    public function view($id) {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['vaccine'] = $this->vaccines->getVaccine($id);

        $this->form_validation->set_rules('name', 'Vaccine Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('description', 'Vaccine Description', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('capacity', 'Vaccine Capacity', 'trim|required');
        $this->form_validation->set_rules('amount', 'Vaccine Amount', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('vaccine/view', $data);
        }
        else
        {
            $this->vaccines->updateVaccine($id);
            $this->session->set_flashdata('message', 'Vaccine is updated.');
            redirect('vaccine');
        }

    }

    public function analyze($id) {

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['vaccine'] = $this->vaccines->getVaccine($id);

        $this->load->view('vaccine/analyze', $data);
    }

    public function remove($id) {

        $this->vaccines->removeVaccine($id);
        $this->session->set_flashdata('message', 'Vaccine is removed.');
		redirect('vaccine');
    }

    public function retreive($id) {

        $this->vaccines->retreiveVaccine($id);
        $this->session->set_flashdata('message', 'Vaccine is retreive.');
		redirect('vaccine/archive');
    }


}