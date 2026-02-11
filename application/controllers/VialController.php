<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'assets/barcode-master/src/Milon/Barcode/DNS1D.php';
require_once 'assets/barcode-master/src/Milon/Barcode/DNS2D.php';
use Milon\Barcode\DNS1D;
class VialController extends CI_Controller {
    
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
    public $vials;
    public $vaccines;
    public $form_validation;
    public $db;
    public $email;
    public $zend;
    public $patients;
    public $incidents;
    public $schedules;

    public function __construct() {

        parent::__construct();
        
        // Load necessary models and libraries
        $this->load->model('Users');
        $this->load->model('Vials');
        $this->load->model('Vaccines');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');

    }

    public function index() {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);
        $data['vials'] = $this->vials->getVials();

        $this->load->view('vial/index', $data);
    }

    public function create() {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);

        if(isset($_POST['removeBarcode'])) {

            $this->session->unset_userdata('vaccine_barcode');
            redirect('vial/create');

        } else if(isset($_POST['checkBarcode'])) {

            $barcode = $this->input->post('barcode');

            if(!empty($barcode)) {

                if($this->vaccines->getVaccineByBarcode($barcode)) {

                    $this->session->set_userdata('vaccine_barcode', $barcode);

                } else {

                    $this->session->set_flashdata('barcode_error', 'Invalid barcode');
                    redirect('vial/create');
                }

            } else {
                $this->session->set_flashdata('barcode_error', 'Barcode is empty');
                redirect('vial/create');
            }
            
            $this->load->view('vial/create', $data);

        } else {

            $this->form_validation->set_rules('prod_date', 'Production Date', 'trim|required');
            $this->form_validation->set_rules('expi_date', 'Expiration Date', 'trim|required');

            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('vial/create', $data);
            }
            else
            {
                $this->vials->createVial();
                $this->session->set_flashdata('message', 'New vial is created.');
                redirect('vial');
            }

        }
    }

    public function verify() {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $session_id = $this->session->userdata('user_id');

        $data['user_info'] = $this->users->getUser($session_id);

        if(isset($_POST['checkBarcode'])) {


            $barcode = $this->input->post('barcode');

            if(!empty($barcode)) {

                if($this->vials->getVialByBarcode($barcode)) {


                    $this->vials->activateVial($barcode);
                    $this->session->set_flashdata('success', 'Vial is now verified');
                    redirect('vial/verify');

                } else {

                    $this->session->set_flashdata('barcode_error', 'Invalid barcode');
                    redirect('vial/verify');
                }

            } else {
                $this->session->set_flashdata('barcode_error', 'Barcode is empty');
                redirect('vial/verify');
            }
            

        }

        $this->load->view('vial/verify', $data);
    }

    public function barcode($id)
    {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }
        
        $generator = new DNS1D();
        $generator->setStorPath(APPPATH . 'cache/'); // temporary storage for images

        // Output PNG directly
        header('Content-Type: image/png');
        echo base64_decode($generator->getBarcodePNG((1000000 + $id), "C128")); // C128 = Code128

    }

    public function barcodeDownload($id) {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $generator = new DNS1D();
        $generator->setStorPath(APPPATH . 'cache/');

        // Generate barcode PNG
        $barcode = base64_decode($generator->getBarcodePNG((1000000 + $id), "C128"));

        // Send headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="barcode_'.(1000000 + $id).'.png"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        echo $barcode;
        exit;
    }

}