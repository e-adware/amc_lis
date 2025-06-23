<?php
session_start();
include("../../includes/connection.php");

$date = date("Y-m-d");
$time = date("H:i:s");
$type = $_POST['type'];

$fromDate = $_POST['dateF'];
$toDate = $_POST['dateT'];

$user = trim($_SESSION['emp_id']);

if ($type == 'load_vaccu_list') {
    ?>
    <h5 style="display: inline-block">Vaccu usage report from: <?php echo date('d-m-Y', strtotime($fromDate)); ?> to
        <?php echo date('d-m-Y', strtotime($toDate)); ?>
    </h5>
    <div class="no-print" style="display: inline-block; float: right;">
        <button class="btn btn-mini btn-success" onclick="print_vaccu_list()"><i class="icon-print"></i> Print</button>
        <!-- <button class="btn btn-mini btn-info" onclick="export_vaccu_list()"><i class="icon-table"></i> Excel</button> -->
    </div>
    <table class="table table-condensed">
        <thead>
            <th>Vaccu Name</th>
            <th>Total Used</th>
        </thead>
        <tbody>
            <?php
            $fluride_id = "3, 10, 11, 34";

            $qry = "SELECT DISTINCT `vaccu_id` AS `fluride` FROM `patient_vaccu_details` WHERE `date` BETWEEN '$fromDate' AND '$toDate' AND `vaccu_id` IN ($fluride_id)";

            $vacc_qry = mysqli_query($link, $qry);
            $count = 0;
            while ($vacc = mysqli_fetch_array($vacc_qry)) {
                $count += mysqli_num_rows(mysqli_query($link, "SELECT * FROM `patient_vaccu_details` WHERE `vaccu_id` = '$vacc[fluride]' AND `date` BETWEEN '$fromDate' AND '$toDate' GROUP BY `opd_id`"));

                $v_name = mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `vaccu_master` WHERE `id` = '$vacc[fluride]'"));

            }
            if($count)
            {
            ?>

            <tr style="cursor: pointer;" onclick="load_patient_vaccu('fluride')">
                <td><?php echo "Fluride"; ?></td>
                <td><?php echo $count; ?></td>
            </tr>
            <?php
            }

            $qry = "SELECT DISTINCT `vaccu_id` FROM `patient_vaccu_details` WHERE `date` BETWEEN '$fromDate' AND '$toDate' AND `vaccu_id` NOT IN ($fluride_id)";
            $vacc_qry = mysqli_query($link, $qry);
            $count_all = 0;
            while ($vacc = mysqli_fetch_array($vacc_qry)) {
                $count_all = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `patient_vaccu_details` WHERE `vaccu_id` = '$vacc[vaccu_id]' AND `date` BETWEEN '$fromDate' AND '$toDate' GROUP BY `opd_id`"));
                $v_name = mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `vaccu_master` WHERE `id` = '$vacc[vaccu_id]'"));
                ?>
                <tr style="cursor: pointer;" onclick="load_patient_vaccu('<?php echo $vacc['vaccu_id']; ?>')">
                    <td><?php echo $v_name['type']; ?></td>
                    <td><?php echo $count_all; ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
if ($type == 'load_vacc_det') {
    $vaccu_id = $_POST['vacc_id'];
    if ($vaccu_id == 'fluride') {
        $fluride_id = "3,10,11,34";
        $qry = "SELECT * FROM `patient_vaccu_details` WHERE `date` BETWEEN '$fromDate' AND '$toDate' AND `vaccu_id` IN ($fluride_id) GROUP BY `opd_id` ORDER BY `opd_id`";
        $vacc_qry = mysqli_query($link, $qry);
        $vacc = mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `vaccu_master` WHERE `id` IN ($fluride_id)"));
        ?>
        <h5 style="display: inline-block">Detailed report for Vaccu: <?php echo $vacc['type']; ?></h5>
        <button class="btn btn-warning btn-mini" style="float: right" onclick="get_list()"><i class="icon-arrow-left"></i>
            Back</button>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th>Hospital No</th>
                    <!--<th>Bill No.</th>-->
                    <th>Patient Name</th>
                    <th>Test Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($pat = mysqli_fetch_array($vacc_qry)) {
                    $qq = "SELECT a.* , b.`testname`, c.`name`, c.`hosp_no` FROM `phlebo_sample` a, `testmaster` b, `patient_info` c WHERE a.`patient_id` = '$pat[patient_id]' AND a.`opd_id` = '$pat[opd_id]' AND a.`ipd_id` = '$pat[ipd_id]' AND a.`vaccu` = '$pat[vaccu_id]' AND c.`patient_id` = a.`patient_id` AND b.`testid` = a.`testid`";
                    $res = mysqli_fetch_array(mysqli_query($link, $qq));
                    ?>
                    <tr>
                        <td><?php echo $res['hosp_no']; ?></td>
                        <!--<td><?php echo $res['opd_id']; ?></td>-->
                        <td><?php echo $res['name']; ?></td>
                        <td><?php echo $res['testname']; ?></td>
                    </tr>
                    <?php
                } ?>
            </tbody>
        </table>
        <?php
    } else {
        $qry = "SELECT * FROM `patient_vaccu_details` WHERE `date` BETWEEN '$fromDate' AND '$toDate' AND `vaccu_id` =
        '$vaccu_id' GROUP BY `opd_id`";
        $vacc_qry = mysqli_query($link, $qry);
        $vacc = mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `vaccu_master` WHERE `id` = '$vaccu_id'"));
        ?>
        <h5 style="display: inline-block">Detailed report for Vaccu: <?php echo $vacc['type']; ?></h5>
        <button class="btn btn-warning btn-mini" style="float: right" onclick="get_list()"><i class="icon-arrow-left"></i>
            Back</button>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th>Hospital No</th>
                    <!--<th>Bill No.</th>-->
                    <th>Patient Name</th>
                    <th>Test Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($pat = mysqli_fetch_array($vacc_qry)) {
                    $qq = "SELECT a.* , b.`testname`, c.`name`, c.`hosp_no` FROM `phlebo_sample` a, `testmaster` b, `patient_info` c WHERE a.`patient_id` = '$pat[patient_id]' AND a.`opd_id` = '$pat[opd_id]' AND a.`ipd_id` = '$pat[ipd_id]' AND a.`vaccu` = '$pat[vaccu_id]' AND c.`patient_id` = a.`patient_id` AND b.`testid` = a.`testid`";
                    $res = mysqli_fetch_array(mysqli_query($link, $qq));
                    ?>
                    <tr>
                        <td><?php echo $res['hosp_no']; ?></td>
                        <!--<td><?php echo $res['opd_id']; ?></td>-->
                        <td><?php echo $res['name']; ?></td>
                        <td><?php echo $res['testname']; ?></td>
                    </tr>
                    <?php
                } ?>
            </tbody>
        </table>
        <?php
    }
}