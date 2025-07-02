<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
$date = date("Y-m-d");
$time = date("H:i:s");
$type = $_POST['type'];

if ($type == 'load_data') {
    $fdate = $_POST['from_date'];
    $tdate = $_POST['to_date'];
    $ward = $_POST['ward'];
    $patient_id = $_POST['hospital_id'];

    $qry = "SELECT a.`patient_id`, a.`opd_id`, a.`wardName`, a.`date`, a.`time`, b.`name`, b.`sex`, b.`dob` FROM `uhid_and_opdid` a, `patient_info` b, `patient_test_details` c, `testmaster` d WHERE a.`date` BETWEEN '$fdate' AND '$tdate' AND a.`patient_id` = b.`patient_id` AND a.`opd_id`= c.`opd_id` AND a.`patient_id` =  c.`patient_id` AND c.`testid` = d.`testid`";

    if ($ward) {
        $qry .= " AND a.`wardName` = '$ward'";
    }
    if ($patient_id) {
        $qry .= " AND a.`patient_id` LIKE '$patient_id%'";
    }

    $qry .= " GROUP BY a.`patient_id`";
    $ward_det_qry = mysqli_query($link, $qry);

    ?>
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 2px 8px;">
        <div><strong>Report Generated On : </strong> <?php echo date('d-M-y') . " / " . date('h:i A'); ?></div>
        <div class="no_print">
            <button onclick="print_report('<?= $fdate; ?>','<?= $tdate; ?>','<?= $ward; ?>','<?= $patient_id; ?>')"
                class="btn btn-mini btn-print"><i class="icon-print icon-large"></i> Print</button>
        </div>
    </div>
    <table class="table table-condensed table-bordered">
        <thead>
            <tr class="head">
                <th>#</th>
                <th>Hospital No.</th>
                <th>Patient Name</th>
                <th>Age / Sex</th>
                <th>Ward Name</th>
                <th>Test Name</th>
                <th>Date / Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $n = 1;
            while ($ward_det = mysqli_fetch_array($ward_det_qry)) {
                // Safer SQL with escaped variables (assuming no prepared statements)
                $patient_id = mysqli_real_escape_string($link, $ward_det['patient_id']);
                $opd_id = mysqli_real_escape_string($link, $ward_det['opd_id']);

                $test_det_qry = mysqli_query($link, "
        SELECT b.`testname`
        FROM `patient_test_details` a
        INNER JOIN `testmaster` b ON a.`testid` = b.`testid`
        WHERE a.`patient_id` = '$patient_id'
        AND a.`opd_id` = '$opd_id'
    ");
                ?>
                <tr>
                    <th><?= $n++; ?></th>
                    <td><?= htmlspecialchars($ward_det['patient_id']); ?></td>
                    <td><?= htmlspecialchars($ward_det['name']); ?></td>
                    <td><?= age_calculator($ward_det['dob']) . " / " . sex_full($ward_det['sex']); ?></td>
                    <td><?= htmlspecialchars($ward_det['wardName']); ?></td>
                    <td><?php
                    $tests_list = [];
                    while ($tests = mysqli_fetch_array($test_det_qry)) {
                        $tests_list[] = htmlspecialchars($tests['testname']);
                    }
                    echo implode(', ', $tests_list);
                    ?>
                    </td>
                    <td><?= date('d-m-Y', strtotime($ward_det['date'])) . " / " . date('h:i A', strtotime($ward_det['time'])); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>


    </table>


    <?php

}