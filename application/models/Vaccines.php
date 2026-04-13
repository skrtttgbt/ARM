<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vaccines extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->ensureBatchTableExists();
    }

    private function ensureBatchTableExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `vaccine_batches` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `vaccine_id` INT(11) NOT NULL,
            `user_id` INT(11) NOT NULL,
            `quantity_added` INT(11) NOT NULL DEFAULT 0,
            `quantity_remaining` INT(11) NOT NULL DEFAULT 0,
            `manufacture_date` DATE NOT NULL,
            `expiration_date` DATE NOT NULL,
            `updated_at` INT(11) NOT NULL,
            `created_at` VARCHAR(50) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_vaccine_batches_vaccine_id` (`vaccine_id`),
            KEY `idx_vaccine_batches_expiration_date` (`expiration_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->db->query($sql);
    }
    
    public function getVaccines()
	{
        $sql = "SELECT
                    v.*,
                    batch_stats.nearest_expiration_date,
                    COALESCE(usage_stats.used_count, 0) AS used_count
                FROM vaccines v
                LEFT JOIN (
                    SELECT vaccine_id, COUNT(*) AS used_count
                    FROM vials
                    GROUP BY vaccine_id
                ) usage_stats ON usage_stats.vaccine_id = v.id
                LEFT JOIN (
                    SELECT vaccine_id, MIN(expiration_date) AS nearest_expiration_date
                    FROM vaccine_batches
                    WHERE quantity_remaining > 0
                    GROUP BY vaccine_id
                ) batch_stats ON batch_stats.vaccine_id = v.id
                WHERE v.quantity > 0 OR v.deleted = 0";

        return $this->db->query($sql)->result_array();
	}

    public function getArchives()
	{
        $sql = "SELECT
                    v.*,
                    COALESCE(usage_stats.used_count, 0) AS used_count,
                    COALESCE(log_stats.damaged_count, 0) AS damaged_count,
                    COALESCE(log_stats.expired_count, 0) AS expired_count,
                    COALESCE(log_stats.recall_count, 0) AS recall_count,
                    COALESCE(log_stats.inventory_adjustment_count, 0) AS inventory_adjustment_count
                FROM vaccines v
                LEFT JOIN (
                    SELECT vaccine_id, COUNT(*) AS used_count
                    FROM vials
                    GROUP BY vaccine_id
                ) usage_stats ON usage_stats.vaccine_id = v.id
                LEFT JOIN (
                    SELECT
                        vaccine_id,
                        SUM(CASE WHEN LOWER(TRIM(reason)) IN ('damaged', 'damaged vial') THEN quantity_archived ELSE 0 END) AS damaged_count,
                        SUM(CASE WHEN LOWER(TRIM(reason)) IN ('expired', 'expired stock') THEN quantity_archived ELSE 0 END) AS expired_count,
                        SUM(CASE WHEN LOWER(TRIM(reason)) IN ('recall', 'recall from supplier') THEN quantity_archived ELSE 0 END) AS recall_count,
                        SUM(CASE WHEN LOWER(TRIM(reason)) = 'inventory adjustment' THEN quantity_archived ELSE 0 END) AS inventory_adjustment_count
                    FROM vaccine_archive_logs
                    GROUP BY vaccine_id
                ) log_stats ON log_stats.vaccine_id = v.id
                WHERE v.deleted > 0";

        return $this->db->query($sql)->result_array();
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

    public function getUsedDoseCount($id)
    {
        return (int) $this->db
            ->where('vaccine_id', $id)
            ->count_all_results('vials');
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

    public function archiveVaccine($id, $quantity, $archive_reason = '', $archived_by = null) {
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
        $updated = $this->db->update('vaccines');

        if (!$updated) {
            return false;
        }

        return $this->db->insert('vaccine_archive_logs', array(
            'vaccine_id' => (int) $id,
            'quantity_archived' => $archive_quantity,
            'reason' => $archive_reason,
            'archived_by' => (int) $archived_by
        ));
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
