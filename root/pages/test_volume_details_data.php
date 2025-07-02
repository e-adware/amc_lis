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
    $instrument = $_POST['instrument'];
    $time_per = $_POST['time_period'];
    $ward = $_POST['ward'];
    $priority = $_POST['priority'];

    list($start, $end) = explode('@@', $time_per);
    $startTime = $start . ":00"; // "00:00:00"
    $endTime = $end . ":59";     // "00:59:59"

    // $sql = "SELECT * FROM `patient_test_details` WHERE `date` BETWEEN '$f_date' AND '$t_date' ";
    $sql = "SELECT COUNT(a.`testid`) AS `total`, a.`testid`, b.`testname` FROM `patient_test_details` a, `testmaster` b, `uhid_and_opdid` c WHERE a.`date` BETWEEN '$f_date' AND '$t_date' ";
    if ($time_per) {
        $sql .= " AND a.`time` BETWEEN '$startTime' AND '$endTime'";
    }
    if ($sel_test) {
        $sql .= " AND a.`testid` = '$sel_test'";
    }
    if ($ward) {
        $sql .= "AND c.wardName = '$ward'";
    }
    if ($priority) {
        if ($priority == 1) {
            $sql .= "AND c.`urgent` = '0'";
        } else if ($priority == 2) {
            $sql .= "AND c.`urgent` = '1'";
        }
    }
    $ins_qry = "";
    if ($instrument) {
        if ($instrument == '999') {
            $ins_qry = " WHERE `instrument_id` = '0'";
        } else {
            $ins_qry = " WHERE `instrument_id` = '$instrument'";

        }
    }
    $sql .= " AND a.`testid` = b.`testid` AND a.`opd_id` = c.`opd_id` AND a.`opd_id` IN (SELECT DISTINCT opd_id FROM `testresults` $ins_qry) GROUP BY a.`testid` ORDER BY `total` DESC, `testname` ASC";
    $res = mysqli_query($link, $sql);
    // echo $sql;
    ?>
    <table class="table table-header">
        <thead>
            <tr>
                <th>#</th>
                <th>Test Name</th>
                <th>Total Tests</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $n = 1;
            $total = 0;
            while ($row = mysqli_fetch_array($res)) {
                $total += $row['total'];

                if ($instrument) {

                }
                ?>
                <tr>
                    <th><?php echo $n++ . "."; ?></th>
                    <td><?php echo $row['testname'] . " - " . $row['testid']; ?></td>
                    <td><?php echo $row['total']; ?></td>
                </tr>
            <?php }
            ?>
            <tr>
                <th style="text-align: right;" colspan="2">Total: </th>
                <th><?php echo $total; ?></th>
            </tr>
        </tbody>
    </table>
    <?php

}