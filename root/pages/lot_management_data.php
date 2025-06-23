<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");


$date = date("Y-m-d");
$time = date("H:i:s");
$type = $_POST['type'];


if ($type == 'save_lot') {
    $qc_id = $_POST['qc_id'];
    $lot = $_POST['lot_no'];
    $control = $_POST['control'];
    $exp_date = $_POST['exp_date'];

    $check_qc = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_lot_master` WHERE `qc_id` = '$qc_id'"));
    if ($check_qc) {
        mysqli_query($link, "UPDATE `qc_lot_master` SET `status` = '0' WHERE `qc_id` = '$qc_id'");
    }
    if (mysqli_query($link, "INSERT INTO `qc_lot_master`(`qc_id`, `lot_no`, `control_name`, `exp_date`, `status`, `date`, `time`) VALUES ('$qc_id','$lot','$control','$exp_date','1','$date','$time')"))
        echo "1";
    else
        echo "0";
}

if ($type == 'load_lot') {
    $load_lot_qry = mysqli_query($link, "SELECT * FROM `qc_lot_master` WHERE `status` = '1'");
    ?>
    <table class="table table-striped table-bordered table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th>QC Name</th>
                <th>Lot No.</th>
                <th>Control Name</th>
                <th>Expiry Date</th>
                <th>Baseline Definition</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $n = 1;
            while ($load_lot = mysqli_fetch_array($load_lot_qry)) {
                ?>
                <tr>
                    <th><?php echo $n++; ?></th>
                    <td style="cursor: pointer;" onclick="edit_lot('<?php echo $load_lot['id']; ?>')">
                        <?php
                        $qc_name = mysqli_fetch_array(mysqli_query($link, "SELECT `qc_name` FROM `qc_master` WHERE `qc_id` = '$load_lot[qc_id]'"));
                        echo $qc_name['qc_name'];
                        ?>
                    </td>
                    <td style="cursor: pointer;" onclick="edit_lot('<?php echo $load_lot['id']; ?>')">
                        <?php echo $load_lot['lot_no']; ?>
                    </td>
                    <td style="cursor: pointer;" onclick="edit_lot('<?php echo $load_lot['id']; ?>')">
                        <?php echo $load_lot['control_name']; ?>
                    </td>
                    <td style="cursor: pointer;" onclick="edit_lot('<?php echo $load_lot['id']; ?>')">
                        <?php echo $load_lot['exp_date']; ?>
                    </td>
                    <td style="text-align: center"><button class="btn btn-mini btn-success"
                            onclick="base_def('<?php echo $load_lot['id']; ?>')"><i class="icon-edit"></i>
                            Edit</button></td>
                </tr>
                <?php
            } ?>
        </tbody>
    </table>
    <?php
}

if ($type == 'edit_lot') {
    $id = $_POST['id'];
    $lot_qry = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_lot_master` WHERE `id` = '$id'"));
    echo $lot_qry['qc_id'] . "@@" . $lot_qry['lot_no'] . "@@" . $lot_qry['control_name'] . "@@" . $lot_qry['exp_date'];
}

if ($type == 'edit_baseline') {
    $id = $_POST['id'];
    $lot_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_lot_master` WHERE `id` = '$id'"));
    $qc_name = mysqli_fetch_array(mysqli_query($link, "SELECT `qc_name` FROM `qc_master` WHERE `qc_id` = '$lot_det[qc_id]'"));

    ?>
    <div>
        <p style="display: inline;">Edit Baseline For:
            <strong><?php echo $qc_name['qc_name']; ?></strong> Lot No.: <strong><?php echo $lot_det['lot_no']; ?></strong>
        </p>


        <button style="float:right; display: inline;" class="btn btn-mini btn-primary" onclick="load_lot_home()"><i
                class="icon-reply"></i>
            Back To
            List</button>
        <div style="text-align: center">
            <p>Enter <b>Lower</b> & <b>Upper</b> value and press <i>ENTER</i> to save.</p>
        </div>
    </div>
    <div id="reagent_list"></div>


    <?php
}

if ($type == 'load_baseline_list') {
    $id = $_POST['id'];
    $lot_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_lot_master` WHERE `id` = '$id'"));

    $get_indice_qry = mysqli_query($link, "SELECT * FROM `qc_mapping` WHERE `qc_id` = '$lot_det[qc_id]'");
    $get_indice_num = mysqli_num_rows($get_indice_qry);
    if ($get_indice_num > 0) {
        ?>
        <input type="hidden" id="lot_id" value="<?php echo $id; ?>" />
        <div style="max-height:500px;overflow-y:scroll; margin-top: 20px;">
            <table class="table table-bordered table-condensed table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Indice (Unit)</th>
                        <th>Lower</th>
                        <th>Upper</th>
                        <th>Mean</th>
                        <th>SD</th>
                        <th>CV(%)</th>
                    </tr>
                    <?php

                    $n = 1;
                    while ($get_indice = mysqli_fetch_array($get_indice_qry)) {
                        $get_val = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_baseline` WHERE `lot_id` = '$lot_det[id]' AND `indice_id` = '$get_indice[test_id]'"));
                        ?>
                        <tr class="row_<?php echo $get_indice['test_id']; ?>">
                            <th><?php echo $n++;
                            ?> </th>
                            <td>
                                <?php
                                $indice_name = mysqli_fetch_array(mysqli_query($link, "SELECT `testname`, `unit` FROM `qc_testmaster` WHERE `test_id` = '$get_indice[test_id]'"));
                                echo $indice_name['testname'] . " (" . $indice_name['unit'] . ")";
                                ?>
                            </td>
                            <td><input value="<?php echo $get_val['lower']; ?>" id="ind_lower_<?php echo $get_indice['test_id']; ?>"
                                    type="text" class="form-control lot_inp"
                                    onkeyup="calculate('<?php echo $get_indice['test_id']; ?>', event, this.value)" />
                            </td>
                            <td><input id="ind_upper_<?php echo $get_indice['test_id']; ?>" type="text"
                                    value="<?php echo $get_val['upper']; ?>" class="form-control lot_inp"
                                    onkeyup="calculate('<?php echo $get_indice['test_id']; ?>', event, this.value)" />
                            </td>
                            <td><input id="ind_mean_<?php echo $get_indice['test_id']; ?>" type="text" class="form-control lot_inp"
                                    value="<?php echo $get_val['mean']; ?>" readonly /></td>
                            <td><input id="ind_sd_<?php echo $get_indice['test_id']; ?>" type="text" class="form-control lot_inp"
                                    value="<?php echo $get_val['sd']; ?>" readonly /></td>
                            <td><input id="ind_cv_<?php echo $get_indice['test_id']; ?>" type="text" class="form-control lot_inp"
                                    value="<?php echo $get_val['cv']; ?>" readonly /></td>
                        </tr>


                        <?php
                    }
                    ?>
                </thead>
            </table>
        </div>
    <?php } else {
        echo "<h4 style='text-align:center;'>No indice mapped for this lot.</h4>";
    }

}
if ($type == 'save_baseline') {
    $indice_id = $_POST['indice_id'];
    $lot_id = $_POST['lot_id'];
    $lower = $_POST['lower'];
    $upper = $_POST['upper'];
    $mean = $_POST['mean'];
    $sd = $_POST['sd'];
    $cv = $_POST['cv'];

    if (mysqli_query($link, "DELETE FROM `qc_baseline` WHERE `lot_id` = '$lot_id' AND `indice_id` = '$indice_id'")) {
        if (mysqli_query($link, "INSERT INTO `qc_baseline`(`lot_id`, `indice_id`, `lower`, `upper`, `mean`, `sd`, `cv`, `date`, `time`) VALUES ('$lot_id','$indice_id','$lower','$upper','$mean','$sd','$cv','$date','$time')"))
            echo '1';
        else
            echo '0';
    }
}