<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vaccines extends CI_Model {
    
    public function getVaccines()
	{
        $query = $this->db->where('quantity >', 0)->get('vaccines');

        return $query->result_array();
	}

    public function getArchives()
	{
        $query = $this->db->where('deleted >', 0)->get('vaccines');

        return $query->result_array();
	}
    
    public function getVaccine($id) 
    {
        $query = $this->db->where('id', $id)->get('vaccines');

        return $query->row_array();
    }

    public function getVaccineByBarcode($code) 
    {
        $query = $this->db->where('barcode', $code)->get('vaccines');

        return $query->row_array();
    }

    public function getVaccineByCol($id, $col) 
    {
        $query = $this->db->where('id', $id)->get('vaccines');

        return $query->row_array()[$col];
    }

    public function updateVaccineByCol($id, $col, $val) 
    {
        $this->db->set($col, $val, false);
        $this->db->where('id', $id);
        
        return $this->db->update('vaccines');
    }

    public function addQuantity($id, $quantity)
    {
        $this->db->set('quantity', 'quantity + ' . (int) $quantity, false);
        $this->db->where('id', $id);

        return $this->db->update('vaccines');
    }

    public function deductQuantity($id, $quantity)
    {
        $this->db->set('quantity', 'GREATEST(quantity - ' . (int) $quantity . ', 0)', false);
        $this->db->where('id', $id);

        return $this->db->update('vaccines');
    }

    public function createVaccine() {

        $date = date("F j, Y");

        $data = array(
        'user_id' => $this->input->post('user_id'),
        'status' => 0,
        'type' => $this->input->post('type'),
        'barcode' => $this->input->post('barcode'),
        'name' => $this->input->post('name'),
        'description' => $this->input->post('description'),
        'capacity' => $this->input->post('capacity'),
        'amount' => $this->input->post('amount'),
        'quantity' => $this->input->post('quantity'),
        'manufacturer_company' => $this->input->post('manufacturer_company'),
        'manufacturer_location' => $this->input->post('manufacturer_location'),
        'importer_company' => $this->input->post('importer_company'),
        'importer_location' => $this->input->post('importer_location'),
        'dose_interval' => 0,
        'deleted' => 0,
        'updated_at' => time(),
        'created_at' => $date
        );

        return $this->db->insert('vaccines',$data);

    }

    public function updateVaccine($id) {

        $this->db->set("type", $this->input->post('type'));
        $this->db->set("name", $this->input->post('name'));
        $this->db->set("description", $this->input->post('description'));
        $this->db->set("capacity", $this->input->post('capacity'), false);
        $this->db->set("amount", $this->input->post('amount'), false);
        $this->db->set("quantity", $this->input->post('quantity'), false);
        $this->db->set("manufacturer_company", $this->input->post('manufacturer_company'));
        $this->db->set("manufacturer_location", $this->input->post('manufacturer_location'));
        $this->db->set("importer_company", $this->input->post('importer_company'));
        $this->db->set("importer_location", $this->input->post('importer_location'));
        $this->db->where('id', $id);
        
        return $this->db->update('vaccines');

    }

    public function getTransactionByYearMonthInfo($yr, $mn, $id){

        $ym    = $yr . '-' . str_pad($mn, 2, '0', STR_PAD_LEFT); // 2025-11

        $this->db->like('schedule', $ym, 'after'); // created_at starts with 2025-11
        $this->db->where('status', 1);
        $query = $this->db->get('schedules');

        $schedules = $query->result_array();
        $count = 0;
        if($schedules) {

            foreach($schedules as $schedule) {
                $this->db->reset_query();

                $queryVial = $this->db->where('id',$schedule['vial_id'])->get('vials');
                $vial = $queryVial->row_array();

                if($vial['vaccine_id'] == $id) {
                    $count++;
                }

            }

        }

        return $count;

    }

    public function removeVaccine($id) {
        $vaccine = $this->getVaccine($id);
        if (!$vaccine) {
            return false;
        }

        $this->db->set('deleted', (int) $vaccine['deleted'] + (int) $vaccine['quantity']);
        $this->db->set('quantity', 0);
        $this->db->where('id', $id);

        return $this->db->update('vaccines');
    }

    public function archiveVaccine($id, $quantity) {
        $vaccine = $this->getVaccine($id);
        if (!$vaccine) {
            return false;
        }

        $current_quantity = (int) $vaccine['quantity'];
        $archive_quantity = (int) $quantity;
        $remaining_quantity = max($current_quantity - $archive_quantity, 0);
        $archived_quantity = (int) $vaccine['deleted'] + $archive_quantity;

        $this->db->set('quantity', $remaining_quantity);
        $this->db->set('deleted', $archived_quantity);
        $this->db->where('id', $id);

        return $this->db->update('vaccines');
    }

    public function retreiveVaccine($id) {
        $vaccine = $this->getVaccine($id);
        if (!$vaccine) {
            return false;
        }

        $this->db->set('quantity', (int) $vaccine['quantity'] + (int) $vaccine['deleted']);
        $this->db->set('deleted', 0);
        $this->db->where('id', $id);

        return $this->db->update('vaccines');
    }
    
    public function getTotalVaccines() {
        $query = $this->db->where('quantity >', 0)->get('vaccines');
        
        return $query->num_rows();
    }
}
