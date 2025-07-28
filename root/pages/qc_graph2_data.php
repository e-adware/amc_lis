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
    $qc = $_POST['qc_sel'];
    $indice = $_POST['indice_sel'];

    $qry = "SELECT * FROM `qc_results` WHERE `qc_id` = '$qc' AND `indice_id` = '$indice' AND flag = '0' AND `order_date` BETWEEN '$fromDate $tTime' AND '$toDate $fTime'";
    $qq = mysqli_query($link, $qry);
    $res = [];
    $date_arr = [];
    while ($q = mysqli_fetch_array($qq)) {
        $res[] = $q['result'];
        $date_arr[] = date('d-m-y', strtotime($q['order_date'])) . " " . date('h:i:s A', strtotime($q['order_date']));
    }
    $result = implode(",", $res);
    $date = implode(",", $date_arr);

    $rr = mysqli_fetch_array(mysqli_query($link, $qry));

    echo $result . "@@" . $date . "@@" . $rr['indice_name'];

}
if ($type == 'get_details') {
    $indice = $_POST['indice_sel'];
    $qc_sel = $_POST['qc_sel'];
    $mean = number_format($_POST['mean'], 2);
    $st_dev = number_format($_POST['st_dev'], 2);
    $cv = number_format($_POST['cv'], 2);


    $indice_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_results` WHERE `indice_id` = '$indice' AND flag = '0' AND `qc_id` = '$qc_sel'"));
    $qc_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_master` WHERE `qc_id` = '$indice_det[qc_id]'"));
    $fluid = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `qc_fluid` WHERE `id` = '$qc_det[fluid_id]'"));
    $instrument = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_instrument_master` WHERE `id` = '$qc_det[instrument_id]'"));
    $lot = mysqli_fetch_array(mysqli_query($link, "SELECT `control_name`, `id` FROM `qc_lot_master` WHERE `lot_no` = '$indice_det[lot_no]'"));

    ?>
    <table class="table table-condensed table-striped">
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
            <td colspan="5"><?php echo $mean; ?></td>
            <th style="display: none;">SD: </th>
            <td style="display: none;"><?php echo $st_dev; ?></td>
            <th style="display: none;">CV: </th>
            <td style="display: none;"><?php echo $cv; ?> %</td>
        </tr>
    </table>

    <?php
}