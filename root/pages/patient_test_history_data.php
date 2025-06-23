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
    $hospital_id = trim($_POST['hospital_id']) ?? '';
    $pat_name = trim($_POST['pat_name']) ?? '';
    $barcode = trim($_POST['barcode']) ?? '';
    if (!empty($hospital_id) || !empty($pat_name) || !empty($barcode)) {
        $pat_qry = "SELECT a.`patient_id`, b.`opd_id`, b.`ipd_id`,b.`batch_no`, b.`iso_no`,c.`name`, b.testid, b.`date`, c.`dob`, c.`sex` FROM `uhid_and_opdid` a, `testresults` b, `patient_info` c WHERE a.`patient_id` = b.`patient_id` AND a.`patient_id` = c.`patient_id`";
        if ($hospital_id)
            $pat_qry .= " AND a.`patient_id` LIKE '$hospital_id%'";
        if ($barcode)
            $pat_qry .= " AND a.`opd_id` LIKE '$barcode'%";
        if ($pat_name) {
            $pat_qry .= " AND c.`name` LIKE '$pat_name%'";
        }
        $pat_qry .= " GROUP BY b.`testid`, b.`date` ORDER BY b.`date`";

        $pat_qry_sql = mysqli_query($link, $pat_qry);
        $pat_det = mysqli_fetch_array($pat_qry_sql);
        $test_count = mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`distinct_test_count`) AS `total_distinct_test_sum`, `date` FROM (SELECT COUNT(DISTINCT `testid`) AS `distinct_test_count`, `date` FROM `testresults` WHERE `patient_id` = '$pat_det[patient_id]' GROUP BY `date` ORDER BY `date`) AS `daily_counts`"));


        ?>
        <table class="table table-condensed table-bordered">
            <thead>
                <tr style="background-color: grey; color: white;">
                    <th><span class="head-text">Patient Name: <?= $pat_det['name']; ?></span></th>
                    <th><span class="head-text">Age/Sex:
                            <?= age_calculator($pat_det['dob']) . " / " . sex_full($pat_det['sex']); ?></span></th>
                    <th><span class="head-text">Hospital No.: <?= $pat_det['patient_id']; ?></span></th>
                    <th><span class="head-text">Total Tests: <?= $test_count['total_distinct_test_sum']; ?></span></th>
                </tr>
            </thead>
            <tbody>
                <?php $date_qry = mysqli_query($link, "SELECT DISTINCT `date` FROM `testresults` WHERE `patient_id` = '$pat_det[patient_id]' ORDER BY `date` DESC");
                while ($date_test = mysqli_fetch_array($date_qry)) {
                    ?>
                    <tr style="background-color: lightgrey;">
                        <td colspan="4">
                            <div style="margin-left: 10px;"><strong>Date:
                                    <?= date('d-m-Y', strtotime($date_test['date'])); ?></strong></div>
                        </td>
                    </tr>
                    <?php
                    $test_by_date_qry = mysqli_query($link, "SELECT DISTINCT(`testid`), `opd_id` FROM testresults WHERE `patient_id` = '$pat_det[patient_id]' AND `date` = '$date_test[date]'");
                    while ($test_by_date = mysqli_fetch_array($test_by_date_qry)) {
                        $testname = mysqli_fetch_array(mysqli_query($link, "SELECT `testname`, `type_id` FROM `testmaster` WHERE `testid` = '$test_by_date[testid]'"));
                        ?>
                        <tr>
                            <td colspan="4">
                                <div style="margin-left: 20px; cursor: pointer"
                                    onclick="print_report('<?= $pat_det['patient_id'] ?>', '<?= $test_by_date['opd_id'] ?>', '<?= $pat_det['ipd_id'] ?>', '<?= $pat_det['batch_no'] ?>', '<?= $pat_det['iso_no'] ?>', '<?= $test_by_date['testid'] ?>',  '<?= $testname['type_id'] ?>')">
                                    <i class="icon-caret-right"></i>
                                    <?= $testname['testname'] ?>
                                </div>
                            </td>
                        </tr>
                    <?php }
                } ?>
            </tbody>
        </table>
        <?php
    } else
        echo "Enter Patient Information";
}