<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
$date = date("Y-m-d");
$time = date("H:i:s");
$type = $_POST['type'];

if ($type == 'load_data') {
    $t_date = $_POST['to_date'];
    $f_date = $_POST['from_date'];
    $sel_test = $_POST['sel_test'];
    $time_per = $_POST['time_period'];
    $ward = $_POST['ward'];
    $priority = $_POST['priority'];

    list($start, $end) = explode('@@', $time_per);
    $startTime = $start . ":00";
    $endTime = $end . ":59";

    // Get ward names
    $ward_qry = "SELECT DISTINCT(`wardName`) FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `type` = 2";
    if ($ward) {
        $ward_qry .= " AND `wardName` = '$ward'";
    }
    if ($time_per) {
        $ward_qry .= " AND `time` BETWEEN '$startTime' AND '$endTime'";
    }
    $ward_qry .= " ORDER BY `wardName`";

    $ward_names = [];
    $ward_sql = mysqli_query($link, $ward_qry);
    while ($row = mysqli_fetch_array($ward_sql)) {
        $ward_names[] = $row['wardName'];
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

    // Optimized query to get all counts at once
    $main_qry = "SELECT b.`wardName`, a.`testid`, COUNT(a.`opd_id`) AS `count`
        FROM `patient_test_details` a
        JOIN `uhid_and_opdid` b ON a.`opd_id` = b.`opd_id`
        WHERE a.`date` BETWEEN '$f_date' AND '$t_date'
        AND b.`type` = 2";

    if ($time_per) {
        $main_qry .= " AND a.`time` BETWEEN '$startTime' AND '$endTime'";
    }

    if ($ward) {
        $main_qry .= " AND b.`wardName` = '$ward'";
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

    $main_qry .= " GROUP BY b.`wardName`, a.`testid`";

    // Store counts in associative array
    $counts = [];
    $result_sql = mysqli_query($link, $main_qry);
    while ($row = mysqli_fetch_array($result_sql)) {
        $counts[$row['wardName']][$row['testid']] = $row['count'];
    }

    $colspan = 1;
    ?>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div><strong>Report Generated On : </strong> <?php echo date('d-M-y') . " / " . date('h:i A'); ?></div>
        <div id="no_print" style="margin-left: auto; padding: 4px 10px; text-align: right;">
            <button
                onclick="exportTableToExcel('<?= $f_date; ?>','<?= $t_date; ?>','<?= $sel_test; ?>','<?= $priority; ?>', '<?= $time_per ?>', '<?= $ward; ?>')"
                class="btn btn-mini btn-warning"><i class="icon-file icon-large"></i> Excel</button>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-responsive table-bordered align-middle text-nowrap">
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
                            $bg_color = $count ? "highlight" : "";
                            echo "<td class=\"$bg_color\">$count</td>";
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
                        $count = $test_totals[$test_id];
                        echo "<td><strong>$count</strong></td>";
                    }
                    ?>
                    <td><strong><?= $grand_total ?></strong></td>
                </tr>

            </tbody>
        </table>
    </div>
    <?php
}
