<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli('localhost', 'root', '', 'petvax');
$db->set_charset('utf8mb4');

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

function build_monthly_vaccine_sequence($vaxirabId, $speedaId, $month, $totalNeeded)
{
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

$vaccinesByName = [];
$vaccineResult = $db->query("SELECT id, name FROM vaccines ORDER BY id ASC");
while ($row = $vaccineResult->fetch_assoc()) {
    $vaccinesByName[trim($row['name'])] = (int) $row['id'];
}

if (!isset($vaccinesByName['VaxiRab N'], $vaccinesByName['SPEEDA'])) {
    throw new RuntimeException('Both VaxiRab N and SPEEDA must exist before rebalancing.');
}

$vaxirabId = $vaccinesByName['VaxiRab N'];
$speedaId = $vaccinesByName['SPEEDA'];

$monthRows = $db->query("
    SELECT YEAR(schedule) AS yr, MONTH(schedule) AS mn, COUNT(*) AS total
    FROM schedules
    WHERE schedule >= '2021-01-01' AND schedule < '2024-01-01' AND status = 1 AND vial_id > 0
    GROUP BY YEAR(schedule), MONTH(schedule)
    ORDER BY YEAR(schedule), MONTH(schedule)
");

$updateVial = $db->prepare("UPDATE vials SET vaccine_id = ? WHERE id = ?");
$updatedRows = 0;

$db->begin_transaction();

try {
    while ($monthRow = $monthRows->fetch_assoc()) {
        $year = (int) $monthRow['yr'];
        $month = (int) $monthRow['mn'];

        $scheduleStmt = $db->prepare("
            SELECT s.id AS schedule_id, s.vial_id
            FROM schedules s
            WHERE YEAR(s.schedule) = ? AND MONTH(s.schedule) = ? AND s.schedule >= '2021-01-01' AND s.schedule < '2024-01-01' AND s.status = 1 AND s.vial_id > 0
            ORDER BY s.schedule ASC, s.id ASC
        ");
        $scheduleStmt->bind_param('ii', $year, $month);
        $scheduleStmt->execute();
        $scheduleResult = $scheduleStmt->get_result();

        $scheduleRows = [];
        while ($schedule = $scheduleResult->fetch_assoc()) {
            $scheduleRows[] = $schedule;
        }
        $scheduleStmt->close();

        $sequence = build_monthly_vaccine_sequence($vaxirabId, $speedaId, $month, count($scheduleRows));

        foreach ($scheduleRows as $index => $schedule) {
            $targetVaccineId = $sequence[$index];
            $vialId = (int) $schedule['vial_id'];

            $updateVial->bind_param('ii', $targetVaccineId, $vialId);
            $updateVial->execute();
            $updatedRows++;
        }
    }

    $db->commit();
    echo "Historical vaccine pattern rebalanced.\n";
    echo "Vial rows updated: {$updatedRows}\n";
} catch (Throwable $e) {
    $db->rollback();
    throw $e;
}
