<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
$date = date("Y-m-d");
$time = date("H:i:s");
$type = $_POST['type'];

$user = $_SESSION['emp_id'];
$user_level = $_SESSION['levelid'];


if ($type == 'load_data') {
    $t_date = $_POST['to_date'];
    $f_date = $_POST['from_date'];

    ?>

    <div id="no_print" style="margin-left: auto; padding: 4px 10px; text-align: right;">

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div><strong>Report Generated On : </strong> <?php echo date('d-M-y') . " / " . date('h:i A'); ?></div>
            <!-- <button
                onclick="exportTableToExcel('<?= $f_date; ?>','<?= $t_date; ?>','<?= $sel_test; ?>','<?= $priority; ?>', '<?= $time_per ?>', '<?= $ward; ?>')"
                class="btn btn-mini btn-warning"><i class="icon-file icon-large"></i> Excel</button> -->
        </div>
        <div class="table-container">
            <table class="table table-responsive table-bordered align-middle text-nowrap" id="resultTable">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th>S.No</th>
                        <th>Hospital No</th>
                        <th>Patient Name</th>
                        <th>Test/Report Name</th>
                        <!-- <th>Report Version</th> -->
                        <th>Authentication Level</th>
                        <th>Authenticated By (User ID)</th>
                        <th>Authenticated By (Full Name)</th>
                        <th>Authenticator Role/Designation</th>
                        <th>Date & Time of Authentication</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $count = 1;
                    // $main_qry = mysqli_query($link, "SELECT a.`patient_id` AS `main_pat_id`, a.`opd_id`, a.`date`, b.`barcode_id`, b.`testid`, b.`paramid`, b.`opd_id` FROM `uhid_and_opdid`a, `test_sample_result`b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` BETWEEN '$f_date' AND '$t_date'");
                
                    if ($user_level == 13) {
                        $main_qry = "SELECT a.patient_id,a.opd_id,a.testid, b.date, b.time FROM patient_test_details a, testresults b WHERE a.patient_id=b.patient_id AND a.opd_id=b.opd_id AND a.testid=b.testid AND b.doc='$user' AND a.date BETWEEN '$f_date' AND '$t_date' GROUP BY b.patient_id,b.opd_id,b.testid";
                    } else {
                        $main_qry = "SELECT a.patient_id,a.opd_id,a.testid, b.date, b.time FROM patient_test_details a, testresults b WHERE a.patient_id=b.patient_id AND a.opd_id=b.opd_id AND a.testid=b.testid AND (b.doc>0 OR b.main_tech>0) AND a.date BETWEEN '$f_date' AND '$t_date' GROUP BY b.patient_id,b.opd_id,b.testid";
                    }

                    $qry1 = mysqli_query($link, $main_qry);

                    while ($qry = mysqli_fetch_array($qry1)) {
                        $count_qry = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(DISTINCT `testid`) AS `edit_count` FROM `testresults_update` WHERE patient_id = '$qry[patient_id]' AND opd_id = '$qry[opd_id]' AND testid = '$qry[testid]'"));

                        $testname = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid` = '$qry[testid]'"));

                        $p_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name`,`hosp_no` FROM `patient_info` WHERE `patient_id` LIKE '$qry[patient_id]'"));

                        $auth_qry = mysqli_query($link, "SELECT DISTINCT `doc` FROM `testresults` WHERE `patient_id` LIKE '$qry[patient_id]' AND `opd_id` LIKE '$qry[opd_id]' AND `testid` = '$qry[testid]'");
                        $auth_level = "";
                        $name = "";
                        while ($auth = mysqli_fetch_array($auth_qry)) {
                            $auth_by = $auth['doc'];

                            if ($auth_by == '0') {
                                $auth = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT `main_tech` FROM `testresults` WHERE `patient_id` LIKE '$qry[patient_id]' AND opd_id LIKE '$qry[opd_id]' AND `testid` = '$qry[testid]'"));

                                $auth_by = $auth['main_tech'];
                                $auth_level = "Technical Validation";

                                $auth_name = mysqli_fetch_array(mysqli_query($link, "SELECT a.`name`, b.`name` AS `level_name` FROM `employee` a, level_master b WHERE a.emp_id = '$auth_by' AND a.`levelid` = b.`levelid`"));
                                $name .= $auth_name['name'] . "<br>";


                            } else {
                                $auth_level = "Clinical Authorization";

                                $auth_name = mysqli_fetch_array(mysqli_query($link, "SELECT a.`name`, b.`name` AS `level_name` FROM `employee` a, level_master b WHERE a.emp_id = '$auth_by' AND a.`levelid` = b.`levelid`"));

                                $name .= $auth_name['name'] . "<br>";

                            }

                        }







                        $fw = $bg = "";
                        if ($count_qry['edit_count'] > 0) {
                            // $bg = "background: rgba(251, 255, 200, 0.5);";
                            $fw = "font-weight: 600;";
                        }
                        ?>

                        <tr style="<?= $bg; ?>">
                            <td><?php echo $count++; ?></td>
                            <td><?php echo $p_info['hosp_no']; ?></td>
                            <td><?= $p_info['name']; ?></td>
                            <td><?= $testname['testname']; ?></td>
                            <!-- <td style="<?= $fw; ?>"><?= $count_qry['edit_count']; ?></td> -->
                            <td><?= $auth_level ?></td>
                            <td><?= $auth_by ?></td>
                            <td><?= $name; ?></td>
                            <td><?= $auth_name['level_name']; ?></td>
                            <td><?= date('d-m-Y h:i a', strtotime($qry['date'] . $qry['time'])); ?></td>
                        </tr>

                        <?php
                    }
                    ?>

                </tbody>
            </table>
        </div>

        <?php
}
