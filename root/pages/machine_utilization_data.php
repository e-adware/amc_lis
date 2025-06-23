<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");

include "../app/init.php";
try {
    $db = new Db_LoaderMS();
} catch (PDOException $e) {
    echo "<h4>Connection failed:<br/>" . $e->getMessage() . "</h4>";
    exit;
}

$type = $_POST['type'];

if ($type == 'load_data') {
    $fdate = $_POST['fdate'] . ' 00:00:01';
    $tdate = $_POST['tdate'] . ' 23:59:59';

    $restype = $_POST['res_type'];

    $get_samp_qry = mysqli_query($link, "SELECT DISTINCT `sample_id` FROM `qc_master`");
    $qc_sample = "";
    while ($get_sample = mysqli_fetch_array($get_samp_qry)) {
        if ($qc_sample)
            $qc_sample .= ",'" . $get_sample['sample_id'] . "'";
        else {
            $qc_sample = "'" . $get_sample['sample_id'] . "'";
        }
    }
    $control_ctr = 0;
    $routine_ctr = 0;
    $no_order_ctr = 0;
    $no_mapping = 0;

    $routine_ids = [];
    ?>
    <table class="table table-codensed">
        <thead>
            <tr>
                <th>#</th>
                <th>Result Type</th>
                <th>Total Tests Done</th>
            </tr>
        </thead>
        <tbody><?php
        $qc_count_qry1 = "SELECT DISTINCT(equip_test) FROM tpl_patient_orders WHERE create_date BETWEEN '$fdate' AND '$tdate' AND (sample_id IN ($qc_sample))";
        $res_all = $db->setQuery($qc_count_qry1)->fetch_all();
        foreach ($res_all as $res) {
            $control_ctr++;
        }

        $qc_count_qry2 = "SELECT DISTINCT(equip_test) FROM tpl_patient_orders WHERE create_date BETWEEN '$fdate' AND '$tdate' AND sample_id NOT IN($qc_sample) AND result_type LIKE 'CONTROL'";
        $res_all = $db->setQuery($qc_count_qry2)->fetch_all();
        foreach ($res_all as $res) {
            $control_ctr++;
        }

        $routine_ctr_qry = "SELECT DISTINCT(sample_id) FROM tpl_patient_orders WHERE result_type LIKE 'ROUTINE' AND create_date BETWEEN '$fdate' AND '$tdate' and sample_id NOT IN ($qc_sample)";
        $routine_res = $db->setQuery($routine_ctr_qry)->fetch_all();
        foreach ($routine_res as $res) {
            $routine_ctr++;
            $routine_ids[] = "'" . $res['sample_id'] . "'"; // Quote each sample_id for SQL
        }
        if (!empty($routine_ids)) {
            $routine_ids_str = implode(',', $routine_ids);
            $not_in = " AND sample_id NOT IN (" . $qc_sample . "," . $routine_ids_str . ")";
        } else {
            $not_in = "";
        }

        $no_mapping_ctr = 0;
        $no_mapping_ctr_qry = "SELECT DISTINCT(sample_id) FROM tpl_patient_orders WHERE result_type LIKE 'NOMAPPING' AND create_date BETWEEN '$fdate' AND '$tdate' $not_in";

        $no_mapping_ctr_res = $db->setQuery($no_mapping_ctr_qry)->fetch_all();
        foreach ($no_mapping_ctr_res as $res) {
            $no_mapping_ctr++;
        }

        $no_order_ctr = 0;
        $no_order_ctr_qry = "SELECT DISTINCT(sample_id) FROM tpl_patient_orders WHERE result_type LIKE 'NO_ORDER' AND create_date BETWEEN '$fdate' AND '$tdate' $not_in";

        $no_order_ctr_res = $db->setQuery($no_order_ctr_qry)->fetch_all();
        foreach ($no_order_ctr_res as $res) {
            $no_order_ctr++;
        }

        ?>
            <tr>
                <th>1</th>
                <td>QC</td>
                <td><?= $control_ctr; ?></td>
            </tr>
            <tr>
                <th>2</th>
                <td>Routine Tests</td>
                <td><?= $routine_ctr; ?></td>
            </tr>
            <tr>
                <th>3</th>
                <td>No Mapping</td>
                <td><?= $no_mapping_ctr; ?></td>
            </tr>
            <tr>
                <th>4</th>
                <td>No Order</td>
                <td><?= $no_order_ctr; ?></td>
            </tr>
        </tbody>
    </table>

    <?php
}

