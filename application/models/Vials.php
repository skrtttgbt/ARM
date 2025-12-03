<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vials extends CI_Model {
    
    public function getVials()
	{
        $query = $this->db->where('deleted', 0)->get('vials');

        return $query->result_array();
	}

    public function getArchives()
	{
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

    public function getStock($code) {

        $vial_id = intval($code) - 1000000;

        $vial_query = $this->db->where('id', $vial_id)->get('vials');

        $vial = $vial_query->row_array();

        $checkUsege = $this->db->where('vial_id', $vial['id'])->get('schedules');

        $getDose_query = $this->db->where('id', $vial['vaccine_id'])->get('vaccines');

        $getDose = $getDose_query->row_array();

        if($checkUsege->num_rows() <= $getDose['dose']) {
            return true;
        } else {
            return false;
        }

    }

    public function createVial() {

        $date = date("F j, Y");

        $data = array(
        'user_id' => $this->input->post('user_id'),
        'vaccine_id' => $this->input->post('vaccine_id'),
        'status' => 0,
        'prod_date' => $this->input->post('prod_date'),
        'expi_date' => $this->input->post('expi_date'),
        'deleted' => 0,
        'updated_at' => time(),
        'created_at' => $date
        );

        return $this->db->insert('vials',$data);

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

}