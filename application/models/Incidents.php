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
        $patient_id = (int) $this->input->post('patient_id');
        $patient = $this->db->where('id', $patient_id)->get('patients')->row_array();
        $schedule_date = $this->input->post('bite_date');

        $data = array(
        'user_id' => $this->input->post('user_id'),
        'patient_id' => $patient_id,
        'dose' => $this->input->post('amount'),
        'animal_type' => $this->input->post('type'),
        'bite_date' => $schedule_date,
        'bite_site' => $this->input->post('bite_place'),
        'status' => 0,
        'updated_at' => time(),
        'created_at' => $date
        );

        $inserted = $this->db->insert('incidents', $data);
        if (!$inserted) {
            log_message('error', 'Create incident failed: insert into incidents table was unsuccessful for patient ID ' . $patient_id . '.');
            return array(
                'created' => false,
                'sms_sent' => false,
                'reason' => 'incident_insert_failed'
            );
        }

        $incident_id = (int) $this->db->insert_id();
        $incident = $this->getIncident($incident_id);
        $mobile = normalize_ph_mobile($patient['mobile'] ?? '');

        if ($mobile === '') {
            log_message('error', 'Create incident SMS failed: invalid or empty patient mobile for incident ID ' . $incident_id . '.');
            return array(
                'created' => true,
                'sms_sent' => false,
                'reason' => 'invalid_mobile'
            );
        }

        $message = $this->buildScheduleReminderMessage($patient, $incident, $schedule_date);
        $sms_sent = send_unisms_sms($mobile, $message);

        if (!$sms_sent) {
            log_message('error', 'Create incident SMS failed: UniSMS send failed for incident ID ' . $incident_id . ' and mobile ' . $mobile . '.');
        }

        return array(
            'created' => true,
            'sms_sent' => $sms_sent,
            'reason' => $sms_sent ? '' : 'sms_failed'
        );

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
        $schedule_date = $this->input->post('sched_date');
        $incident_id = (int) $this->input->post('incident_id');
        $incident = $this->getIncident($incident_id);
        $patient = $incident ? $this->db->where('id', $incident['patient_id'])->get('patients')->row_array() : null;
        $mobile = normalize_ph_mobile($patient['mobile'] ?? $this->input->post('mobile'));

        $data = array(
        'user_id' => $this->input->post('user_id'),
        'incident_id' => $incident_id,
        'vial_id' => 0,
        'schedule' => $schedule_date,
        'status' => 0,
        'updated_at' => time(),
        'created_at' => $date
        );

        $inserted = $this->db->insert('schedules', $data);
        if (!$inserted) {
            log_message('error', 'Create schedule failed: insert into schedules table was unsuccessful for incident ID ' . $incident_id . '.');
            return array(
                'created' => false,
                'sms_sent' => false,
                'reason' => 'schedule_insert_failed'
            );
        }

        if ($mobile === '') {
            log_message('error', 'Create schedule SMS failed: invalid or empty patient mobile for incident ID ' . $incident_id . '.');
            return array(
                'created' => true,
                'sms_sent' => false,
                'reason' => 'invalid_mobile'
            );
        }

        $message = $this->buildScheduleReminderMessage($patient, $incident, $schedule_date);
        $sms_sent = send_unisms_sms($mobile, $message);

        if (!$sms_sent) {
            log_message('error', 'Create schedule SMS failed: UniSMS send failed for incident ID ' . $incident_id . ' and mobile ' . $mobile . '.');
        }

        return array(
            'created' => true,
            'sms_sent' => $sms_sent,
            'reason' => $sms_sent ? '' : 'sms_failed'
        );

    }

    private function buildScheduleReminderMessage($patient, $incident, $schedule_date)
    {
        $patient_name = trim(($patient['patient_first_name'] ?? 'Patient') . ' ' . ($patient['patient_last_name'] ?? ''));
        if ($patient_name === '') {
            $patient_name = 'Patient';
        }

        $dose_count = (int) ($incident['dose'] ?? 0);
        $first_dose_date = date('Y-m-d', strtotime($schedule_date));
        $second_dose_date = date('m/d/Y', strtotime($first_dose_date . ' +3 days'));
        $third_dose_date = date('m/d/Y', strtotime($first_dose_date . ' +10 days'));

        $message = "Dear {$patient_name},\n\n";
        $message .= "This is a friendly reminder about your upcoming vaccination schedule:\n\n";

        if ($dose_count >= 2) {
            $message .= "- 2nd Dose: {$second_dose_date}, 8:00 AM - 5:00 PM\n";
        }

        if ($dose_count >= 3) {
            $message .= "- 3rd Dose/Booster: {$third_dose_date}, 8:00 AM - 5:00 PM\n\n";
        } else {
            $message .= "\n";
        }

        $message .= "Reminder: The clinic is open 8:00 AM - 5:00 PM only.\n\n";
        $message .= "NOTE: Please be advised that if the animal that bit/scratch you shows signs of illness or passes away within the 14-day observation period, you will need to return to our clinic for a 4th vaccination dose.";

        return $message;
    }
    
    public function getTotalIncidents() {
        $query = $this->db->get('incidents');
        
        return $query->num_rows();
    }
}
