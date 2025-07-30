<?php
session_start();
include("../../includes/connection.php");
// require("../../includes/global.function.php");

include "../app/init.php";
try {
    $db = new Db_LoaderMS();
} catch (PDOException $e) {
    echo "<h4>Connection failed:<br/>" . $e->getMessage() . "</h4>";
    exit;
}
$date = date("Y-m-d");
$time = date("H:i:s");
$type = $_POST['type'];

$tTime = '00:00:01';
$fTime = '23:59:59';

$user = trim($_SESSION['emp_id']);


$fromDate = $_POST['dateF'];



if ($type == 'get_data') {
    $qc = $_POST['qc_id'];
    $qc_name = mysqli_fetch_array(mysqli_query($link, "SELECT `qc_name`, `sample_id` FROM `qc_master` WHERE `qc_id` = '$qc'"));

    $mapped_qry = mysqli_query($link, "SELECT * FROM `qc_mapping` WHERE `qc_id` = '$qc'");

    //<!----- Pull TestMaster For Debug -----!>
    // $testmaster_host = $db->setQuery("SELECT DISTINCT(equip_test) AS test_id, test_name AS testname, unit FROM tpl_patient_orders WHERE result_type='CONTROL';")->fetch_all();

    // foreach ($testmaster_host as $res) {
    //     mysqli_query($link, "INSERT INTO `qc_testmaster`(`test_id`, `testname`, `unit`) VALUES ('$res[equip_test]','$res[test_name]','$res[unit]')");
    // }

    $indice_id = "";
    while ($mapped = mysqli_fetch_array($mapped_qry)) {
        if ($indice_id) {
            $indice_id .= ", '" . $mapped['test_id'] . "'";
        } else {
            $indice_id = "'" . $mapped['test_id'] . "'";
        }
    }


    // echo "SELECT DISTINCT order_date FROM tpl_patient_orders WHERE sample_id = '$qc_name[sample_id]' AND equip_test IN ($indice_id) AND order_date BETWEEN '$fromDate $tTime' AND '$fromDate $fTime' AND result_type = 'CONTROL'";
    $check_map = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_mapping` WHERE `qc_id` = '$qc'"));

    if ($check_map) {
        $date = $db->setQuery("SELECT DISTINCT order_date FROM tpl_patient_orders WHERE sample_id = '$qc_name[sample_id]' AND equip_test IN ($indice_id) AND order_date BETWEEN '$fromDate $tTime' AND '$fromDate $fTime' AND sample_id='$qc_name[sample_id]'")->fetch_all();
    } else {
        echo "<h4 style='text-align: center;'>Parameters Not Mapped For This QC</h4>";
        exit();
    }
    // print_r($row);


    ?>
    <table class="table table-bordered table-striped ">
        <thead>
            <th>QC Report</th>

        </thead>
        <tbody>

            <?php $n = 1;
            foreach ($date as $r1) {
                $check = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_results` WHERE `order_date` = '$r1[order_date]' AND `qc_id` = '$qc'"));
                ?>
                <tr>
                    <th onclick="load_date_data('<?php echo $r1['order_date']; ?>')" style="cursor: pointer">
                        <?php echo date('d-m-Y', strtotime($r1['order_date'])) . " / " . date('h:i A', strtotime($r1['order_date'])); ?>
                        <?php if ($check) { ?>
                            <i style="font-size: 18px; color: #299c00;" class="icon-check"></i>
                        <?php } else {
                            echo "<i style='font-size: 18px; color: #9c0000;' class='icon-check-empty'></i>";
                        } ?>
                    </th>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php

}
if ($type == 'date_data') {
    $qc = $_POST['qc_id'];
    $order_date = $_POST['order_date'];
    $qc_name = mysqli_fetch_array(mysqli_query($link, "SELECT `qc_name`, `sample_id` FROM `qc_master` WHERE `qc_id` = '$qc'"));
    $mapped_qry = mysqli_query($link, "SELECT * FROM `qc_mapping` WHERE `qc_id` = '$qc'");

    $indice_id = "";
    while ($mapped = mysqli_fetch_array($mapped_qry)) {
        if ($indice_id) {
            $indice_id .= ", '" . $mapped['test_id'] . "'";
        } else {
            $indice_id = "'" . $mapped['test_id'] . "'";
        }
    }


    $check = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_results` WHERE `order_date` = '$order_date' AND `qc_id` = '$qc'"));
    $save_btn = "";
    $print_btn = "display: none";
    if ($check) {
        $save_btn = "disabled style='display: none;'";
        $print_btn = "display: ";
        $qry = mysqli_query($link, "SELECT * FROM qc_results WHERE `order_date` = '$order_date'");
    }


    ?>
    <div style="float: right;">
        <button class="btn btn-info btn-mini" onclick="generate_report()"><i class="icon-reply"></i> Back (Esc)</button>
        <button class="btn btn-primary btn-mini" style="<?php echo $print_btn; ?>"
            onclick="print_report('<?php echo $order_date; ?>')"><i class="icon-edit"></i>
            Print</button>
        <button class="btn btn-print btn-mini" style="<?php echo $print_btn; ?>"
            onclick="excel_report('<?php echo $order_date; ?>')"><i class="icon-file"></i>
            Export To Excel</button>
        <button class="btn btn-print btn-mini" <?php echo $save_btn; ?>
            onclick="save_report('<?php echo $qc_name['qc_name'] ?>', '<?php echo $order_date; ?>')"><i
                class="icon-save"></i> Save</button>
    </div>
    <input type="hidden" value="<?php echo $qc; ?>" id="qc_id" />
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <!-- <th>Indice ID</th> -->
                <th>Indice Name</th>
                <th style="width: 30%">Result <i style="float: right; margin-right: 20%;" class="icon-print"></i></th>
                <th>Range</th>
                <th>Unit</th>
                <th>Date</th>
            </tr>
        </thead>
        <?php

        if ($check) {
            // echo "SELECT * FROM qc_results WHERE `order_date` = '$order_date'";
            $lot = mysqli_fetch_array(mysqli_query($link, "SELECT `id` as `lot_id` FROM `qc_lot_master` WHERE `lot_no` = '$check[lot_no]' AND `qc_id` = '$qc'"));
            ?>

            <tbody>
                <?php
                $n = 1;

                while ($res = mysqli_fetch_array($qry)) {
                    $flag = "";
                    $print_res = "checked";

                    if ($res['flag'] != '0') {
                        $flag = " <i style='color: red;' class='icon-flag'></i>";

                    }
                    if ($res['print'] == 0) {
                        $print_res = "";

                    }
                    $range = mysqli_fetch_array(mysqli_query($link, "SELECT lower, upper FROM qc_baseline WHERE lot_id = '$lot[lot_id]' AND indice_id = '$res[indice_id]'"));
                    ?>
                    <tr>
                        <td><?php echo $n++; ?></td>
                        <td><?= $res['indice_name']; ?></td>
                        <td><input type="text" disabled value="<?= $res['result']; ?>" /> <?= $flag ?><input disabled
                                style="float: right; margin-right: 20%" type="checkbox" <?= $print_res ?> /></td>
                        <td><?= $range['lower'] . " - " . $range['upper']; ?></td>
                        <td><?= $res['unit']; ?></td>
                        <td><?= date('d-m-Y / h:i A', strtotime($res['order_date'])); ?></td>

                    </tr>
                <?php } ?>
            </tbody>



            <?php

        } else {
            $r1 = $db->setQuery("SELECT * FROM tpl_patient_orders WHERE sample_id = '$qc_name[sample_id]' AND equip_test IN ($indice_id) AND order_date = '$order_date'")->fetch_all();

            $lot = mysqli_fetch_array(mysqli_query($link, "SELECT `id` AS `lot_id` FROM `qc_lot_master` WHERE `qc_id` = '$qc'"));
            ?>
            <tbody>
                <?php
                $n = 1;
                foreach ($r1 as $row) {
                    $range = mysqli_fetch_array(mysqli_query($link, "SELECT lower, upper FROM qc_baseline WHERE `lot_id` = '$lot[lot_id]' AND indice_id = '$row[equip_test]'"));
                    $flag = "";
                    $resflag = 0;
                    $print_res = "checked";
                    if ($row['result_flag'] != '0') {
                        $flag = " <i style='color: red;' class='icon-flag'></i>";
                        $resflag = $row['result_flag'];
                        $print_res = "";
                    }
                    ?>
                    <tr>
                        <td><?php echo $n; ?></td>
                        <!-- <td><?php echo $row['equip_test']; ?></td> -->
                        <td><?php echo $row['test_name']; ?></td>
                        <td><input <?= $save_btn; ?> id="res_<?php echo $n; ?>" type="text" value="<?php echo $row['result']; ?>"
                                data-id="<?php echo $row['equip_test']; ?>" data-name="<?php echo $row['test_name']; ?>"
                                data-unit="<?php echo $row['unit']; ?>" /><?= $flag; ?>
                            <input type="hidden" class="flag" value="<?= $resflag; ?>" />
                            <input class="is_print" style="float: right; margin-right: 20%" value="1" type="checkbox" <?= $print_res; ?> />
                        </td>

                        <td><?= $range['lower'] . " - " . $range['upper']; ?></td>
                        <td><?php echo $row['unit']; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row['order_date'])) . " / " . date('h:i A', strtotime($row['order_date'])); ?>
                        </td>
                    </tr>
                    <?php
                    $n++;
                } ?>
            </tbody>
            <?php
        } ?>
    </table>

    <?php

}
if ($type == "save_qc") {
    $order_date = $_POST['order_date'];
    $qc_name = $_POST['qc_name'];
    $results = json_decode($_POST['results'], true); // decode results array

    $qc_id = mysqli_fetch_array(mysqli_query($link, "SELECT `qc_id` FROM `qc_master` WHERE `qc_name` = '$qc_name'"));
    if (!$qc_id) {
        echo "0";
        exit;
    }

    $lot_no = mysqli_fetch_array(mysqli_query($link, "SELECT `lot_no` FROM `qc_lot_master` WHERE `qc_id` = '$qc_id[qc_id]' AND `status` = '1'"));

    if (!$lot_no) {
        echo 0;
        exit;
    }

    foreach ($results as $row) {
        $indice_id = mysqli_real_escape_string($link, $row['indice_id']);
        $test_name = mysqli_real_escape_string($link, $row['test_name']);
        $result = mysqli_real_escape_string($link, $row['result']);
        $unit = mysqli_real_escape_string($link, $row['unit']);
        $flag = mysqli_real_escape_string($link, $row['flag']);
        $is_print = mysqli_real_escape_string($link, $row['is_print']);

        mysqli_query($link, "INSERT INTO `qc_results` (`qc_id`, `lot_no`, `order_date`, `indice_id`, `indice_name`, `result`, `flag`, `print`,`unit`, `date`, `time`, `user`) VALUES ('$qc_id[qc_id]', '$lot_no[lot_no]', '$order_date', '$indice_id', '$test_name', '$result', '$flag', '$is_print', '$unit', '$date', '$time', '$user')");

    }

    echo 1; // success
}
