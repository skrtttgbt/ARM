<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patients extends CI_Model {

    public function getPatients()
	{
        $query = $this->db->where('deleted', 0)->get('patients');

        return $query->result_array();
	}
    
    public function getPatient($id) 
    {
        $query = $this->db->where('id', $id)->get('patients');

        return $query->row_array();
    }

    public function getPatientByCol($id, $col) 
    {
        $query = $this->db->where('id', $id)->get('patients');

        return $query->row_array()[$col];
    }

    public function getHeight($id) 
    {
        return $this->getPatientByCol($id, 'height');
    }

    public function getWeight($id) 
    {
        return $this->getPatientByCol($id, 'weight');
    }

    public function updatePatientByCol($id, $col, $val) 
    {
        $this->db->set($col, $val, false);
        $this->db->where('id', $id);
        
        return $this->db->update('patients');
    }

    public function updateHeight($id, $height) 
    {
        return $this->updatePatientByCol($id, 'height', $height);
    }

    public function updateWeight($id, $weight) 
    {
        return $this->updatePatientByCol($id, 'weight', $weight);
    }

    public function createPatient() {

        $date = date("F j, Y");
        $mobile = normalize_ph_mobile($this->input->post('mobile'));

        $type = $this->input->post('type');
        $relationship = $this->input->post('relationship');
        $account_first_name = $this->input->post('account_first_name');
        $account_last_name = $this->input->post('account_last_name');

        if ($type === 'Member') {
            $relationship = '';
            $account_first_name = '';
            $account_last_name = '';
        } else {
            if ($relationship === null) {
                $relationship = '';
            }
        }

        $data = array(
        'user_id' => $this->input->post('user_id'),
        'status' => 0,
        'patient_first_name' => $this->input->post('first_name'),
        'patient_last_name' => $this->input->post('last_name'),
        'gender' => $this->input->post('gender'),
        'birthday' => $this->input->post('birthday'),
        'height' => $this->input->post('height'),
        'weight' => $this->input->post('weight'),
        'address' => $this->input->post('address'),
        'mobile' => $mobile,
        'philhealth_type' => $type,
        'philhealth_relationship' => $relationship,
        'philhealth_no' => $this->input->post('account_number'),
        'philhealth_first_name' => $account_first_name,
        'philhealth_last_name' => $account_last_name,
        'deleted' => 0,
        'updated_at' => time(),
        'created_at' => $date
        );

        $message = "We've registered your information in our system.\n\n(Vax Safe Ramos)";
        send_unisms_sms($mobile, $message);

        return $this->db->insert('patients',$data);

    }

    public function actionSuspend($id) {

        $this->db->set('deleted', 1);
        $this->db->where('id', $id);

        return $this->db->update('patients');
    }

    public function actionActivate($id) {

        $this->db->set('deleted', 0);
        $this->db->where('id', $id);

        return $this->db->update('patients');
    }

    public function getArchives() {
        $query = $this->db->where('deleted', 1)->get('patients');

        return $query->result_array();
    }
    
    public function getTotalPatients() {
        $query = $this->db->where('deleted', 0)->get('patients');
        
        return $query->num_rows();
    }
}
