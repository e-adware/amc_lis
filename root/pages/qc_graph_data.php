<?php
session_start();
include("../../includes/connection.php");
include "../app/init.php";

$date = date("Y-m-d");
$time = date("H:i:s");
$type = $_POST['type'];

$tTime = '00:00:01';
$fTime = '23:59:59';
$fromDate = $_POST['dateF'];
$toDate = $_POST['dateT'];

$user = trim($_SESSION['emp_id']);

if ($type == 'load_indice') {
    $qc_id = $_POST['qc_id'];
    $get_indice = mysqli_query($link, "SELECT a.`lot_no`, b.`indice_id`, b.`indice_name` FROM `qc_lot_master` a, `qc_results` b WHERE a.`qc_id` = '$qc_id' AND b.flag = '0' AND a.`lot_no` = b.`lot_no` AND b.`order_date` BETWEEN '$fromDate $tTime' AND '$toDate $fTime' AND a.`status` = '1' GROUP BY b.`indice_id` ORDER BY b.`indice_name`");

    // echo "SELECT a.`lot_no`, b.`indice_id`, b.`indice_name` FROM `qc_lot_master` a, `qc_results` b WHERE a.`qc_id` = '$qc_id' AND a.`lot_no` = b.`lot_no` AND b.`order_date` BETWEEN '$fromDate $tTime' AND '$toDate $fTime' AND a.`status` = '1' GROUP BY b.`indice_id` ORDER BY b.`indice_name`";

    echo "<option value='0'>Select Indice</option>";
    while ($indice = mysqli_fetch_array($get_indice)) {
        echo "<option value='" . $indice['indice_id'] . "'>" . $indice['indice_name'] . "</option>";
    }

    ?>


    <?php

}
if ($type == 'get_graph') {
    $result_qry = "SELECT a.`indice_name`, a.`result`, a.`lot_no`, b.`id` AS `lot_id`, a.`date`, a.`time`, a.`order_date` FROM `qc_results` a, `qc_lot_master` b WHERE a.`indice_id`='$_POST[indice_sel]' AND  a.flag = '0'  AND a.`order_date` BETWEEN '$fromDate $tTime' AND '$toDate $fTime' AND a.`lot_no` = b.`lot_no` AND a.`qc_id` = '$_POST[qc_sel]'";

    $rr = mysqli_fetch_array(mysqli_query($link, $result_qry));


    $mean_val = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_baseline` WHERE `lot_id` = '$rr[lot_id]' AND `indice_id` = '$_POST[indice_sel]'"));

    $res = mysqli_query($link, $result_qry);
    // $indice_result = "";
    // $date_time = "";
    $mean = $mean_val['mean'];
    $sd = $mean_val['sd'];
    $indice_res = [];
    $date_arr = [];

    while ($result = mysqli_fetch_assoc($res)) {
        $indice_res[] = $result['result'];
        $date_arr[] = date('d-m-y', strtotime($result['order_date'])) . " " . date('h:i:s A', strtotime($result['order_date']));
    }
    //echo json_encode($indice_res);
    $date = implode(",", $date_arr);

    $result = implode(",", $indice_res);
    // echo $result_qry;
    echo $result . "@@" . $date . "@@" . $mean . "@@" . $sd . "@@" . $rr['indice_name'];
}
if ($type == 'get_details') {
    $indice = $_POST['indice_sel'];
    $qc_sel = $_POST['qc_sel'];
    $indice_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_results` WHERE `indice_id` = '$indice' AND `qc_id` = '$qc_sel' AND flag = '0'"));
    $qc_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_master` WHERE `qc_id` = '$indice_det[qc_id]'"));
    $fluid = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `qc_fluid` WHERE `id` = '$qc_det[fluid_id]'"));
    $instrument = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_instrument_master` WHERE `id` = '$qc_det[instrument_id]'"));
    $lot = mysqli_fetch_array(mysqli_query($link, "SELECT `control_name`, `id` FROM `qc_lot_master` WHERE `lot_no` = '$indice_det[lot_no]'"));
    $baseline_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_baseline` WHERE `indice_id` = '$indice' AND `lot_id` = '$lot[id]'"));
    // echo "SELECT * FROM `qc_results` WHERE `indice_id` = '$indice' AND `qc_id` = '$qc_sel'";
    ?>
    <table class="table table-condensed">
        <tr>
            <th>Period: </th>
            <td colspan="5"><?php echo date('d-m-Y', strtotime($fromDate)) . " to " . date('d-m-Y', strtotime($toDate)); ?>
            </td>
        </tr>
        <tr>
            <th>Lot No: </th>
            <td><?php echo $indice_det['lot_no']; ?></td>
            <th>Control Name: </th>
            <td><?php echo $lot['control_name']; ?></td>
            <th>Fluid: </th>
            <td><?php echo $fluid['name']; ?></td>
        </tr>
        <tr>
            <th>Indice Name: </th>
            <td><?php echo $indice_det['indice_name']; ?></td>
            <th>QC Name: </th>
            <td><?php echo $qc_det['qc_name']; ?></td>
            <th>Instrument Name: </th>
            <td><?php echo $instrument['name']; ?></td>
        </tr>
        <tr>
            <th>Mean: </th>
            <td><?php echo $baseline_det['mean']; ?></td>
            <th>SD: </th>
            <td><?php echo $baseline_det['sd']; ?></td>
            <th>CV: </th>
            <td><?php echo $baseline_det['cv']; ?></td>
        </tr>
    </table>
    <?php
}