<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incidents extends CI_Model {

    public function getIncidents()
	{
        $query = $this->db->get('incidents');

        return $query->result_array();
	}
    
    public function getIncident($id) 
    {
        $query = $this->db->where('id', $id)->get('incidents');

        return $query->row_array();
    }
    
    public function getIncidentsByPatientId($patient_id) 
    {
        $query = $this->db->where('patient_id', $patient_id)->get('incidents');

        return $query->result_array();
    }

    public function getIncidentByCol($id, $col) 
    {
        $query = $this->db->where('id', $id)->get('incidents');

        return $query->row_array()[$col];
    }

    public function updateIncidentByCol($id, $col, $val) 
    {
        $this->db->set($col, $val, false);
        $this->db->where('id', $id);
        
        return $this->db->update('incidents');
    }

    public function createIncident() {

        $date = date("F j, Y");

        $data = array(
        'user_id' => $this->input->post('user_id'),
        'patient_id' => $this->input->post('patient_id'),
        'dose' => $this->input->post('amount'),
        'animal_type' => $this->input->post('type'),
        'bite_date' => $this->input->post('bite_date'),
        'bite_site' => $this->input->post('bite_place'),
        'status' => 0,
        'updated_at' => time(),
        'created_at' => $date
        );

        return $this->db->insert('incidents',$data);

    }

    public function checkSchedule($iid) {

        $query = $this->db->where('incident_id', $iid)->where('status', 0)->get('schedules');

        return $query->row_array();

    }

    public function countCompletedSchedule($iid) {

        $query = $this->db->where('incident_id', $iid)->where('status', 1)->get('schedules');

        return $query->num_rows();

    }

    public function createSchedule() {

        $date = date("F j, Y");
        $mobile = normalize_ph_mobile($this->input->post('mobile'));

        $data = array(
        'user_id' => $this->input->post('user_id'),
        'incident_id' => $this->input->post('incident_id'),
        'vial_id' => 0,
        'schedule' => $this->input->post('sched_date'),
        'status' => 0,
        'updated_at' => time(),
        'created_at' => $date
        );

        $message = sprintf("sched: %s", $this->input->post('sched_date'));
        send_unisms_sms($mobile, $message);

        return $this->db->insert('schedules',$data);

    }
    
    public function getTotalIncidents() {
        $query = $this->db->get('incidents');
        
        return $query->num_rows();
    }
}
