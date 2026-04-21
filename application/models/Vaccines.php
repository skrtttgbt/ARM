<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vaccines extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->ensureBatchTableExists();
        $this->ensureArchiveLogTableExists();
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

    private function ensureArchiveLogTableExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `vaccine_archive_logs` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `vaccine_id` INT(11) NOT NULL,
            `quantity_archived` INT(11) NOT NULL DEFAULT 0,
            `reason` VARCHAR(255) NOT NULL,
            `archived_by` INT(11) NOT NULL DEFAULT 0,
            `archived_at` INT(11) NOT NULL DEFAULT 0,
            `created_at` VARCHAR(50) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_vaccine_archive_logs_vaccine_id` (`vaccine_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->db->query($sql);

        if (!$this->db->field_exists('archived_at', 'vaccine_archive_logs')) {
            $this->db->query("ALTER TABLE `vaccine_archive_logs` ADD COLUMN `archived_at` INT(11) NOT NULL DEFAULT 0");
        }

        if (!$this->db->field_exists('created_at', 'vaccine_archive_logs')) {
            $this->db->query("ALTER TABLE `vaccine_archive_logs` ADD COLUMN `created_at` VARCHAR(50) NOT NULL DEFAULT ''");
        }
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
            'archived_by' => (int) $archived_by,
            'archived_at' => time(),
            'created_at' => date("F j, Y")
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

    public function getAuditTrailEntries()
    {
        $entries = array_merge(
            $this->getStockAuditEntries(),
            $this->getUsedAuditEntries(),
            $this->getArchiveAuditEntries()
        );

        usort($entries, function ($left, $right) {
            $left_time = isset($left['sort_timestamp']) ? (int) $left['sort_timestamp'] : 0;
            $right_time = isset($right['sort_timestamp']) ? (int) $right['sort_timestamp'] : 0;

            if ($left_time === $right_time) {
                return strcmp((string) $right['event_date'], (string) $left['event_date']);
            }

            return $right_time <=> $left_time;
        });

        return $entries;
    }

    private function getStockAuditEntries()
    {
        $rows = $this->db
            ->select('
                vb.id,
                vb.vaccine_id,
                vb.quantity_added,
                vb.manufacture_date,
                vb.expiration_date,
                vb.updated_at,
                vb.created_at,
                v.name AS vaccine_name,
                v.barcode AS vaccine_barcode,
                u.first_name,
                u.last_name
            ')
            ->from('vaccine_batches vb')
            ->join('vaccines v', 'v.id = vb.vaccine_id', 'left')
            ->join('users u', 'u.id = vb.user_id', 'left')
            ->order_by('vb.updated_at', 'DESC')
            ->get()
            ->result_array();

        $entries = [];
        foreach ($rows as $row) {
            $entries[] = [
                'event_type' => 'IN STOCK',
                'vaccine_id' => (int) $row['vaccine_id'],
                'vaccine_name' => (string) $row['vaccine_name'],
                'vaccine_barcode' => (string) $row['vaccine_barcode'],
                'quantity' => (int) $row['quantity_added'],
                'event_date' => (string) $row['created_at'],
                'event_timestamp' => (int) $row['updated_at'],
                'date_note' => 'Inserted / restocked',
                'details' => trim('Restocked by ' . $row['first_name'] . ' ' . $row['last_name']),
                'reference_date' => !empty($row['expiration_date']) ? date('M j, Y', strtotime($row['expiration_date'])) : '',
                'reference_label' => 'Expires',
                'sort_timestamp' => (int) $row['updated_at']
            ];
        }

        return $entries;
    }

    private function getUsedAuditEntries()
    {
        $rows = $this->db
            ->select('
                vi.id,
                vi.vaccine_id,
                vi.updated_at,
                vi.created_at,
                s.schedule,
                v.name AS vaccine_name,
                v.barcode AS vaccine_barcode
            ')
            ->from('vials vi')
            ->join('vaccines v', 'v.id = vi.vaccine_id', 'left')
            ->join('schedules s', 's.vial_id = vi.id', 'left')
            ->where('vi.status', 1)
            ->order_by('vi.updated_at', 'DESC')
            ->get()
            ->result_array();

        $entries = [];
        foreach ($rows as $row) {
            $schedule_note = !empty($row['schedule']) ? 'Vaccination schedule: ' . date('M j, Y', strtotime($row['schedule'])) : 'Barcode scanned and used';

            $entries[] = [
                'event_type' => 'USED',
                'vaccine_id' => (int) $row['vaccine_id'],
                'vaccine_name' => (string) $row['vaccine_name'],
                'vaccine_barcode' => (string) $row['vaccine_barcode'],
                'quantity' => 1,
                'event_date' => !empty($row['updated_at']) ? date('M j, Y g:i A', (int) $row['updated_at']) : (string) $row['created_at'],
                'event_timestamp' => (int) $row['updated_at'],
                'date_note' => 'Barcode scan date and time',
                'details' => $schedule_note,
                'reference_date' => '',
                'reference_label' => '',
                'sort_timestamp' => (int) $row['updated_at']
            ];
        }

        return $entries;
    }

    private function getArchiveAuditEntries()
    {
        $rows = $this->db
            ->select('
                val.id,
                val.vaccine_id,
                val.quantity_archived,
                val.reason,
                val.archived_at,
                val.created_at,
                v.name AS vaccine_name,
                v.barcode AS vaccine_barcode,
                u.first_name,
                u.last_name
            ')
            ->from('vaccine_archive_logs val')
            ->join('vaccines v', 'v.id = val.vaccine_id', 'left')
            ->join('users u', 'u.id = val.archived_by', 'left')
            ->order_by('val.archived_at', 'DESC')
            ->get()
            ->result_array();

        $entries = [];
        foreach ($rows as $row) {
            $event_type = $this->normalizeAuditReason($row['reason']);
            $actor_name = trim($row['first_name'] . ' ' . $row['last_name']);
            $details = $actor_name !== '' ? $event_type . ' by ' . $actor_name : $event_type;
            $event_date = !empty($row['archived_at'])
                ? date('M j, Y g:i A', (int) $row['archived_at'])
                : (!empty($row['created_at']) ? (string) $row['created_at'] : 'Date not recorded');

            $entries[] = [
                'event_type' => $event_type,
                'vaccine_id' => (int) $row['vaccine_id'],
                'vaccine_name' => (string) $row['vaccine_name'],
                'vaccine_barcode' => (string) $row['vaccine_barcode'],
                'quantity' => (int) $row['quantity_archived'],
                'event_date' => $event_date,
                'event_timestamp' => (int) $row['archived_at'],
                'date_note' => $event_type === 'EXPIRED' ? 'Archive date / expiry handling' : 'Archive date',
                'details' => $details,
                'reference_date' => '',
                'reference_label' => '',
                'sort_timestamp' => (int) $row['archived_at']
            ];
        }

        return $entries;
    }

    private function normalizeAuditReason($reason)
    {
        $normalized = strtolower(trim((string) $reason));

        if (in_array($normalized, array('damaged', 'damaged vial'), true)) {
            return 'DAMAGE';
        }

        if (in_array($normalized, array('expired', 'expired stock'), true)) {
            return 'EXPIRED';
        }

        if (in_array($normalized, array('recall', 'recall from supplier'), true)) {
            return 'RECALL';
        }

        return strtoupper((string) $reason);
    }
}
