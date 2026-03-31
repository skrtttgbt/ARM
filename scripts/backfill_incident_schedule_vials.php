<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli('localhost', 'root', '', 'tsug3');
$db->set_charset('utf8mb4');

$today = new DateTimeImmutable('2026-03-31');
$now_unix = (string) time();
$created_label = $today->format('F j, Y');
$dose_offsets = [0, 3, 7, 14, 28];

function parse_date_value($value)
{
    if ($value === null || $value === '') {
        return null;
    }

    $formats = ['Y-m-d', 'F j, Y', 'Y-m-d H:i:s', 'm/d/Y'];
    foreach ($formats as $format) {
        $date = DateTimeImmutable::createFromFormat($format, $value);
        if ($date instanceof DateTimeImmutable) {
            return $date;
        }
    }

    $timestamp = strtotime($value);
    if ($timestamp !== false) {
        return (new DateTimeImmutable())->setTimestamp($timestamp);
    }

    return null;
}

function schedule_date_for_index(DateTimeImmutable $base_date, $index, array $offsets)
{
    if (isset($offsets[$index])) {
        return $base_date->modify('+' . $offsets[$index] . ' days');
    }

    $extra_days = end($offsets) + (($index - count($offsets) + 1) * 7);
    return $base_date->modify('+' . $extra_days . ' days');
}

$vaccines = [];
$vaccine_result = $db->query("SELECT id FROM vaccines WHERE deleted = 0 ORDER BY id ASC");
while ($row = $vaccine_result->fetch_assoc()) {
    $vaccines[] = (int) $row['id'];
}

if (!$vaccines) {
    throw new RuntimeException('No active vaccines found. Cannot backfill schedules and vials.');
}

$incident_result = $db->query("SELECT id, user_id, dose, bite_date, created_at FROM incidents ORDER BY id ASC");

$insert_schedule = $db->prepare(
    "INSERT INTO schedules (user_id, incident_id, vial_id, schedule, status, updated_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$update_schedule = $db->prepare(
    "UPDATE schedules SET vial_id = ?, status = ?, updated_at = ? WHERE id = ?"
);
$insert_vial = $db->prepare(
    "INSERT INTO vials (user_id, vaccine_id, status, updated_at, created_at) VALUES (?, ?, ?, ?, ?)"
);
$update_incident_status = $db->prepare(
    "UPDATE incidents SET status = ?, updated_at = ? WHERE id = ?"
);

$created_schedules = 0;
$completed_schedules = 0;
$created_vials = 0;
$updated_incidents = 0;

$db->begin_transaction();

try {
    while ($incident = $incident_result->fetch_assoc()) {
        $incident_id = (int) $incident['id'];
        $user_id = (int) $incident['user_id'];
        $dose_count = max(1, (int) $incident['dose']);

        $base_date = parse_date_value($incident['bite_date']);
        if (!$base_date) {
            $base_date = parse_date_value($incident['created_at']);
        }
        if (!$base_date) {
            $base_date = $today;
        }

        $existing_schedules = [];
        $schedule_stmt = $db->prepare("SELECT id, vial_id, schedule, status FROM schedules WHERE incident_id = ? ORDER BY schedule ASC, id ASC");
        $schedule_stmt->bind_param('i', $incident_id);
        $schedule_stmt->execute();
        $schedule_result = $schedule_stmt->get_result();
        while ($schedule = $schedule_result->fetch_assoc()) {
            $existing_schedules[] = $schedule;
        }
        $schedule_stmt->close();

        for ($i = count($existing_schedules); $i < $dose_count; $i++) {
            $schedule_date = schedule_date_for_index($base_date, $i, $dose_offsets)->format('Y-m-d');
            $status = ($schedule_date <= $today->format('Y-m-d')) ? 1 : 0;
            $vial_id = 0;

            if ($status === 1) {
                $vaccine_id = $vaccines[($incident_id + $i) % count($vaccines)];
                $vial_status = 1;
                $vial_created_at = schedule_date_for_index($base_date, $i, $dose_offsets)->format('F j, Y');
                $insert_vial->bind_param('iiiss', $user_id, $vaccine_id, $vial_status, $now_unix, $vial_created_at);
                $insert_vial->execute();
                $vial_id = (int) $db->insert_id;
                $created_vials++;
            }

            $insert_schedule->bind_param(
                'iiisiss',
                $user_id,
                $incident_id,
                $vial_id,
                $schedule_date,
                $status,
                $now_unix,
                $created_label
            );
            $insert_schedule->execute();
            $created_schedules++;

            $existing_schedules[] = [
                'id' => $db->insert_id,
                'vial_id' => $vial_id,
                'schedule' => $schedule_date,
                'status' => $status
            ];
        }

        $all_completed = true;
        foreach ($existing_schedules as $index => $schedule) {
            $schedule_id = (int) $schedule['id'];
            $schedule_date = parse_date_value($schedule['schedule']);
            if (!$schedule_date) {
                continue;
            }

            $should_complete = $schedule_date->format('Y-m-d') <= $today->format('Y-m-d');
            $new_status = $should_complete ? 1 : 0;
            $vial_id = (int) $schedule['vial_id'];

            if ($should_complete && $vial_id <= 0) {
                $vaccine_id = $vaccines[($incident_id + $index) % count($vaccines)];
                $vial_status = 1;
                $vial_created_at = $schedule_date->format('F j, Y');
                $insert_vial->bind_param('iiiss', $user_id, $vaccine_id, $vial_status, $now_unix, $vial_created_at);
                $insert_vial->execute();
                $vial_id = (int) $db->insert_id;
                $created_vials++;
            }

            $update_schedule->bind_param('iisi', $vial_id, $new_status, $now_unix, $schedule_id);
            $update_schedule->execute();

            if ($should_complete) {
                $completed_schedules++;
            } else {
                $all_completed = false;
            }
        }

        $incident_status = $all_completed ? 1 : 0;
        $update_incident_status->bind_param('isi', $incident_status, $now_unix, $incident_id);
        $update_incident_status->execute();
        $updated_incidents++;
    }

    $db->commit();

    echo "Backfill complete.\n";
    echo "Schedules created: {$created_schedules}\n";
    echo "Schedules marked completed: {$completed_schedules}\n";
    echo "Vials created: {$created_vials}\n";
    echo "Incidents updated: {$updated_incidents}\n";
} catch (Throwable $e) {
    $db->rollback();
    throw $e;
}
