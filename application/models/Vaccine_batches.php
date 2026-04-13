<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vaccine_batches extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    public function ensureTableExists()
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

    public function addBatch($vaccine_id, $user_id, $quantity, $manufacture_date, $expiration_date)
    {
        $date = date("F j, Y");

        return $this->db->insert('vaccine_batches', array(
            'vaccine_id' => (int) $vaccine_id,
            'user_id' => (int) $user_id,
            'quantity_added' => (int) $quantity,
            'quantity_remaining' => (int) $quantity,
            'manufacture_date' => $manufacture_date,
            'expiration_date' => $expiration_date,
            'updated_at' => time(),
            'created_at' => $date
        ));
    }

    public function deductQuantity($vaccine_id, $quantity)
    {
        $remaining_to_deduct = (int) $quantity;

        if ($remaining_to_deduct <= 0) {
            return true;
        }

        $batches = $this->db
            ->where('vaccine_id', (int) $vaccine_id)
            ->where('quantity_remaining >', 0)
            ->order_by('expiration_date', 'ASC')
            ->order_by('id', 'ASC')
            ->get('vaccine_batches')
            ->result_array();

        foreach ($batches as $batch) {
            if ($remaining_to_deduct <= 0) {
                break;
            }

            $batch_remaining = (int) $batch['quantity_remaining'];
            if ($batch_remaining <= 0) {
                continue;
            }

            $deduct_from_batch = min($batch_remaining, $remaining_to_deduct);

            $this->db->set('quantity_remaining', max($batch_remaining - $deduct_from_batch, 0));
            $this->db->set('updated_at', time());
            $this->db->where('id', (int) $batch['id']);
            $this->db->update('vaccine_batches');

            $remaining_to_deduct -= $deduct_from_batch;
        }

        return $remaining_to_deduct === 0;
    }

    public function getNearestExpiryByVaccine()
    {
        $sql = "SELECT
                    vaccine_id,
                    MIN(expiration_date) AS nearest_expiration_date
                FROM vaccine_batches
                WHERE quantity_remaining > 0
                GROUP BY vaccine_id";

        $rows = $this->db->query($sql)->result_array();
        $result = array();

        foreach ($rows as $row) {
            $result[(int) $row['vaccine_id']] = $row['nearest_expiration_date'];
        }

        return $result;
    }

    public function getExpiringBatches($limit = 20)
    {
        $this->db->select('vb.*, v.name AS vaccine_name, v.barcode AS vaccine_barcode');
        $this->db->from('vaccine_batches vb');
        $this->db->join('vaccines v', 'v.id = vb.vaccine_id', 'left');
        $this->db->where('vb.quantity_remaining >', 0);
        $this->db->order_by('vb.expiration_date', 'ASC');
        $this->db->order_by('vb.id', 'ASC');
        $this->db->limit((int) $limit);

        return $this->db->get()->result_array();
    }

    public function getExpiringBatchesForVaccine($vaccine_id)
    {
        $this->db->where('vaccine_id', (int) $vaccine_id);
        $this->db->where('quantity_remaining >', 0);
        $this->db->order_by('expiration_date', 'ASC');
        $this->db->order_by('id', 'ASC');

        return $this->db->get('vaccine_batches')->result_array();
    }
}
