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

    public function updatePatientByCol($id, $col, $val) 
    {
        $this->db->set($col, $val, false);
        $this->db->where('id', $id);
        
        return $this->db->update('patients');
    }

    public function createPatient() {

        $date = date("F j, Y");

        $data = array(
        'user_id' => $this->input->post('user_id'),
        'status' => 0,
        'patient_first_name' => $this->input->post('first_name'),
        'patient_last_name' => $this->input->post('last_name'),
        'gender' => $this->input->post('gender'),
        'birthday' => $this->input->post('birthday'),
        'address' => $this->input->post('address'),
        'mobile' => $this->input->post('mobile'),
        'philhealth_type' => $this->input->post('type'),
        'philhealth_relationship' => $this->input->post('relationship'),
        'philhealth_no' => $this->input->post('account_number'),
        'philhealth_first_name' => $this->input->post('account_first_name'),
        'philhealth_last_name' => $this->input->post('account_last_name'),
        'deleted' => 0,
        'updated_at' => time(),
        'created_at' => $date
        );

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
        $message = sprintf("We’ve registered your information in our system. \n\n(Vax Safe Ramos)");
            
        $data2 = [
            'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
            'message' => $message,
            'phone_number' => $this->input->post('mobile')
            ];
            
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        curl_close($ch);
            //echo $response;
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
}