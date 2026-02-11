<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedules extends CI_Model {

    public function getSchedules()
	{
        $query = $this->db->get('schedules');

        return $query->result_array();
	}
    
    public function getSchedule($id) 
    {
        $query = $this->db->where('id', $id)->get('schedules');

        return $query->row_array();
    }

    public function getScheduleByCol($id, $col) 
    {
        $query = $this->db->where('id', $id)->get('schedules');

        return $query->row_array()[$col];
    }

    public function updateScheduleByCol($id, $col, $val) 
    {
        $this->db->set($col, $val, false);
        $this->db->where('id', $id);
        
        return $this->db->update('schedules');
    }


    public function getTodaySchedule()
    {
        $query = $this->db->get('schedules');

        return $query->result_array();
    }
    
    public function getTodaySchedulesCount() {
        $today = date('Y-m-d');
        $query = $this->db->where('DATE(schedule)', $today)->get('schedules');
        
        return $query->num_rows();
    }

    public function updateScheduleDone($id,$code) {

        $vial_id = intval($code) - 1000000;

        $this->db->set('vial_id', $vial_id);
        $this->db->set('status', 1);
        $this->db->where('id', $id);
        
        return $this->db->update('schedules');
        

    }

}