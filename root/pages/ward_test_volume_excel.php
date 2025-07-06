<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
$date = date("Y-m-d");
$time = date("H:i:s");


$t_date = base64_decode($_GET['to_date']);
$f_date = base64_decode($_GET['from_date']);
$sel_test = base64_decode($_GET['sel_test']);
$time_per = base64_decode($_GET['time_period']);
$ward = base64_decode($_GET['ward']);
$priority = base64_decode($_GET['priority']);

$filename = "ward_test_volume_" . $f_date . "_to_" . $t_date . ".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename=' . $filename);

list($start, $end) = explode('@@', $time_per);
$startTime = $start . ":00";
$endTime = $end . ":59";

// Get ward names
// $ward_qry = "SELECT DISTINCT(`wardName`) FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `type` = 2";
$ward_qry = "SELECT DISTINCT(ward_name) FROM ward_master a, uhid_and_opdid b WHERE `date` BETWEEN '$f_date' AND '$t_date' AND a.id = b.ward";
if ($ward) {
    $ward_qry .= " AND a.`id` = '$ward'";
}
if ($time_per) {
    $ward_qry .= " AND b.`time` BETWEEN '$startTime' AND '$endTime'";
}
$ward_qry .= " ORDER BY a.`ward_name`";

$ward_names = [];
$ward_sql = mysqli_query($link, $ward_qry);
while ($row = mysqli_fetch_array($ward_sql)) {
    $ward_names[] = $row['ward_name'];
}

// Get test names and IDs
$test_qry = "SELECT DISTINCT a.`testid`, b.`testname` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid` = b.`testid` AND a.`date` BETWEEN '$f_date' AND '$t_date'";
if ($time_per) {
    $test_qry .= " AND a.`time` BETWEEN '$startTime' AND '$endTime'";
}
if ($sel_test) {
    $test_qry .= " AND a.`testid` = '$sel_test'";
}
$test_qry .= " ORDER BY b.`testname`";

$test_names = [];
$test_ids = [];
$test_sql = mysqli_query($link, $test_qry);
while ($row = mysqli_fetch_array($test_sql)) {
    $test_ids[] = $row['testid'];
    $test_names[] = $row['testname'];
}

$main_qry = "SELECT c.ward_name, a.`testid`, COUNT(a.`opd_id`) AS `count` FROM `patient_test_details` a, `uhid_and_opdid` b, `ward_master` c WHERE a.`opd_id` = b.`opd_id` AND a.`date` BETWEEN '$f_date' AND '$t_date' AND b.`ward` = c.`id`";

if ($time_per) {
    $main_qry .= " AND a.`time` BETWEEN '$startTime' AND '$endTime'";
}

if ($ward) {
    $main_qry .= " AND c.`id` = '$ward'";
}

if ($sel_test) {
    $main_qry .= " AND a.`testid` = '$sel_test'";
}

if ($priority) {
    if ($priority == 1) {
        $main_qry .= " AND b.`urgent` = '0'";
    } else if ($priority == 2) {
        $main_qry .= " AND b.`urgent` = '1'";
    }
}

$main_qry .= " GROUP BY c.`ward_name`, a.`testid`";

// Store counts in associative array
$counts = [];
$result_sql = mysqli_query($link, $main_qry);
while ($row = mysqli_fetch_array($result_sql)) {
    $counts[$row['ward_name']][$row['testid']] = $row['count'];
}

$colspan = 1;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        table {
            white-space: nowrap;
            font-family: Arial, sans-serif;
            font-size: 10pt;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 2px 5px;
            border: 1px solid #000;
        }

        th {
            background-color: #f2f2f2;
        }

        .highlight {
            background-color: rgb(177, 255, 195);
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="table-container">
        <table class="table table-responsive table-bordered align-middle text-nowrap">
            <colgroup>
                <col style="width: 150px;">
                <?php foreach ($test_names as $name): ?>
                    <col style="width: 100px;">
                <?php endforeach; ?>
                <col style="width: 100px;">
            </colgroup>
            <thead>
                <tr>
                    <th>Ward Name ↓ /Test Name →</th>
                    <?php foreach ($test_names as $testname): ?>
                        <th><?= $testname ?></th>
                        <?php $colspan++; ?>
                    <?php endforeach; ?>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ward_names as $ward_name): ?>
                    <tr>
                        <th><?= $ward_name ?></th>
                        <?php
                        $ward_total = 0;
                        foreach ($test_ids as $test_id) {
                            $count = $counts[$ward_name][$test_id] ?? 0;
                            $ward_total += $count;
                            $test_totals[$test_id] += $count;
                            if ($count > 0) {
                                echo "<td class='highlight'><div style='style='background-color:rgb(177, 255, 195); font-weight: bold;'>$count</div></td>";
                            } else {
                                echo "<td>$count</td>";
                            }
                        }
                        $grand_total += $ward_total;
                        echo "<td><strong>$ward_total</strong></td>";
                        ?>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th>Total:</th>
                    <?php
                    foreach ($test_ids as $test_id) {
                        $count = $test_totals[$test_id] ?? 0;
                        if ($count > 0) {
                            echo "<td class='highlight'><strong>$count</strong></td>";
                        } else {
                            echo "<td><strong>$count</strong></td>";
                        }
                    }
                    ?>
                    <td><strong><?= $grand_total ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>