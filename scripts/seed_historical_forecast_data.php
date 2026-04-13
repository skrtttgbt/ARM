<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli('localhost', 'root', '', 'petvax');
$db->set_charset('utf8mb4');

$defaultUserId = 9;
$doseOffsets = [0, 3, 7];
$seasonalPattern = [
    1 => 3,
    2 => 3,
    3 => 4,
    4 => 5,
    5 => 6,
    6 => 8,
    7 => 10,
    8 => 9,
    9 => 7,
    10 => 5,
    11 => 4,
    12 => 3,
];
$yearAdjustments = [
    2021 => 0,
    2022 => 2,
    2023 => 4,
];

function get_vaxirab_ratio_for_month($month)
{
    $ratios = [
        1 => 0.42,
        2 => 0.42,
        3 => 0.45,
        4 => 0.48,
        5 => 0.52,
        6 => 0.60,
        7 => 0.65,
        8 => 0.63,
        9 => 0.58,
        10 => 0.50,
        11 => 0.46,
        12 => 0.43,
    ];

    return isset($ratios[$month]) ? $ratios[$month] : 0.5;
}

function build_monthly_vaccine_sequence(array $vaccines, $month, $totalNeeded)
{
    if (count($vaccines) < 2) {
        return array_fill(0, max(0, (int) $totalNeeded), (int) reset($vaccines));
    }

    $vaxirabId = (int) $vaccines[0];
    $speedaId = (int) $vaccines[1];
    $ratio = get_vaxirab_ratio_for_month((int) $month);
    $sequence = [];
    $assignedVaxirab = 0;

    for ($i = 1; $i <= $totalNeeded; $i++) {
        $targetVaxirab = (int) round($i * $ratio);
        if ($assignedVaxirab < $targetVaxirab) {
            $sequence[] = $vaxirabId;
            $assignedVaxirab++;
        } else {
            $sequence[] = $speedaId;
        }
    }

    return $sequence;
}

$existingHistorical = (int) $db->query(
    "SELECT COUNT(*) AS total
     FROM incidents
     WHERE STR_TO_DATE(created_at, '%M %e, %Y') < '2024-01-01'"
)->fetch_assoc()['total'];

if ($existingHistorical > 0) {
    echo "Seed skipped: found {$existingHistorical} incident rows before 2024-01-01.\n";
    exit(0);
}

$patients = [];
$patientResult = $db->query("SELECT id, user_id FROM patients WHERE deleted = 0 ORDER BY id ASC");
while ($row = $patientResult->fetch_assoc()) {
    $patients[] = [
        'id' => (int) $row['id'],
        'user_id' => (int) $row['user_id'],
    ];
}

if (!$patients) {
    throw new RuntimeException('No active patients found. Cannot seed historical incidents.');
}

$vaccines = [];
$vaccineResult = $db->query("SELECT id FROM vaccines ORDER BY id ASC");
while ($row = $vaccineResult->fetch_assoc()) {
    $vaccines[] = (int) $row['id'];
}

if (!$vaccines) {
    throw new RuntimeException('No active vaccines found. Cannot seed historical vaccine usage.');
}

$insertIncident = $db->prepare(
    "INSERT INTO incidents
        (user_id, patient_id, dose, animal_type, bite_date, bite_site, status, updated_at, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$insertSchedule = $db->prepare(
    "INSERT INTO schedules
        (user_id, incident_id, vial_id, schedule, status, updated_at, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$insertVial = $db->prepare(
    "INSERT INTO vials
        (user_id, vaccine_id, status, updated_at, created_at)
     VALUES (?, ?, ?, ?, ?)"
);

$createdIncidents = 0;
$createdSchedules = 0;
$createdVials = 0;
$patientIndex = 0;
$vaccineIndex = 0;

$db->begin_transaction();

try {
    foreach ($yearAdjustments as $year => $adjustment) {
        foreach ($seasonalPattern as $month => $baseCount) {
            $incidentCount = $baseCount + $adjustment;
            $monthlyVaccineSequence = build_monthly_vaccine_sequence($vaccines, $month, $incidentCount * count($doseOffsets));
            $monthlyVaccineIndex = 0;

            for ($i = 0; $i < $incidentCount; $i++) {
                $patient = $patients[$patientIndex % count($patients)];
                $userId = $patient['user_id'] > 0 ? $patient['user_id'] : $defaultUserId;
                $patientId = $patient['id'];

                $day = min(28, 1 + (int) floor(($i * 27) / max(1, $incidentCount)));
                $baseDate = DateTimeImmutable::createFromFormat('Y-n-j', $year . '-' . $month . '-' . $day);
                if (!$baseDate) {
                    throw new RuntimeException('Failed to create base date.');
                }

                $biteDateLabel = $baseDate->format('F j, Y');
                $updatedAt = (string) $baseDate->getTimestamp();
                $animalType = ($i % 4 === 0) ? 'Cat' : 'Dog';
                $biteSite = ($i % 3 === 0) ? 'Leg' : (($i % 3 === 1) ? 'Arm' : 'Hand');
                $dose = 3;
                $incidentStatus = 1;

                $insertIncident->bind_param(
                    'iiisssiss',
                    $userId,
                    $patientId,
                    $dose,
                    $animalType,
                    $biteDateLabel,
                    $biteSite,
                    $incidentStatus,
                    $updatedAt,
                    $biteDateLabel
                );
                $insertIncident->execute();
                $incidentId = (int) $db->insert_id;
                $createdIncidents++;

                foreach ($doseOffsets as $offsetIndex => $offsetDays) {
                    $scheduleDate = $baseDate->modify('+' . $offsetDays . ' days');
                    $scheduleDateValue = $scheduleDate->format('Y-m-d');
                    $scheduleLabel = $scheduleDate->format('F j, Y');
                    $scheduleUpdatedAt = (string) $scheduleDate->getTimestamp();
                    $vaccineId = $monthlyVaccineSequence[$monthlyVaccineIndex % count($monthlyVaccineSequence)];

                    $vialStatus = 1;
                    $insertVial->bind_param(
                        'iiiss',
                        $userId,
                        $vaccineId,
                        $vialStatus,
                        $scheduleUpdatedAt,
                        $scheduleLabel
                    );
                    $insertVial->execute();
                    $vialId = (int) $db->insert_id;
                    $createdVials++;

                    $scheduleStatus = 1;
                    $insertSchedule->bind_param(
                        'iiisiss',
                        $userId,
                        $incidentId,
                        $vialId,
                        $scheduleDateValue,
                        $scheduleStatus,
                        $scheduleUpdatedAt,
                        $scheduleLabel
                    );
                    $insertSchedule->execute();
                    $createdSchedules++;

                    $vaccineIndex++;
                    $monthlyVaccineIndex++;
                }

                $patientIndex++;
            }
        }
    }

    $db->commit();

    echo "Historical forecast seed complete.\n";
    echo "Incidents created: {$createdIncidents}\n";
    echo "Schedules created: {$createdSchedules}\n";
    echo "Vials created: {$createdVials}\n";
} catch (Throwable $e) {
    $db->rollback();
    throw $e;
}
