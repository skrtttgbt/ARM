<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vials extends CI_Model {

    private function hasDeletedColumn()
    {
        return $this->db->field_exists('deleted', 'vials');
    }
    
    public function getVials()
	{
        if ($this->hasDeletedColumn()) {
            $this->db->where('deleted', 0);
        }

        $query = $this->db->get('vials');

        return $query->result_array();
	}

    public function getArchives()
	{
        if (!$this->hasDeletedColumn()) {
            return [];
        }

        $query = $this->db->where('deleted', 1)->get('vials');

        return $query->result_array();
	}
    
    public function getVial($id) 
    {
        $query = $this->db->where('id', $id)->get('vials');

        return $query->row_array();
    }

    public function getVialByBarcode($code) 
    {
         // Ensure the code is numeric
        if (!is_numeric($code)) {
            return null;
        }

        // Convert barcode back to vial ID
        $vial_id = intval($code) - 1000000;

        if ($vial_id <= 0) {
            return null; // invalid barcode
        }

        $query = $this->db->where('id', $vial_id)->get('vials');

        return $query->row_array();
    }

    public function getVialByBarcodeVerified($code) 
    {
         // Ensure the code is numeric
        if (!is_numeric($code)) {
            return null;
        }

        // Convert barcode back to vial ID
        $vial_id = intval($code) - 1000000;

        if ($vial_id <= 0) {
            return null; // invalid barcode
        }

        $query = $this->db->where('id', $vial_id)->where('status', 1)->get('vials');

        return $query->row_array();
    }

    public function createVial() {

        $date = date("F j, Y");

        $data = array(
        'user_id' => $this->input->post('user_id'),
        'vaccine_id' => $this->input->post('vaccine_id'),
        'status' => 0,
        'updated_at' => time(),
        'created_at' => $date
        );

        if ($this->hasDeletedColumn()) {
            $data['deleted'] = 0;
        }

        if ($this->db->field_exists('prod_date', 'vials')) {
            $data['prod_date'] = $this->input->post('prod_date');
        }

        if ($this->db->field_exists('expi_date', 'vials')) {
            $data['expi_date'] = $this->input->post('expi_date');
        }

        return $this->db->insert('vials',$data);

    }

    public function createVialForVaccine($user_id, $vaccine_id)
    {
        $date = date("F j, Y");

        $data = array(
        'user_id' => $user_id,
        'vaccine_id' => $vaccine_id,
        'status' => 1,
        'updated_at' => time(),
        'created_at' => $date
        );

        if ($this->hasDeletedColumn()) {
            $data['deleted'] = 0;
        }

        if ($this->db->field_exists('prod_date', 'vials')) {
            $data['prod_date'] = '';
        }

        if ($this->db->field_exists('expi_date', 'vials')) {
            $data['expi_date'] = '';
        }

        $this->db->insert('vials', $data);

        return $this->db->insert_id();
    }

    public function activateVial($code) {


        if (!is_numeric($code)) {
            return null;
        }

        // Convert barcode back to vial ID
        $vial_id = intval($code) - 1000000;

        if ($vial_id <= 0) {
            return null; // invalid barcode
        }

        $this->db->set('status', 1);
        $this->db->where('id', $vial_id);

        return $this->db->update('vials');
        
    }
    
    public function getTotalVials() {
        if ($this->hasDeletedColumn()) {
            $this->db->where('deleted', 0);
        }

        $query = $this->db->get('vials');
        
        return $query->num_rows();
    }
}
