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

    $qry = "SELECT a.`patient_id`, a.`opd_id`, a.`ward`, a.`date`, a.`time`, b.`name`, b.`sex`, b.`dob`, e.ward_name FROM `uhid_and_opdid` a, `patient_info` b, `patient_test_details` c, `testmaster` d, `ward_master` e WHERE a.`date` BETWEEN '$fdate' AND '$tdate' AND a.`patient_id` = b.`patient_id` AND a.`opd_id`= c.`opd_id` AND a.`patient_id` =  c.`patient_id` AND c.`testid` = d.`testid` AND a.`ward` = e.`id`";

    if ($ward) {
        $qry .= " AND e.`id` = '$ward'";
    }
    if ($patient_id) {
        $qry .= " AND a.`patient_id` LIKE '$patient_id%'";
    }

    $qry .= " GROUP BY a.`patient_id`";
    $ward_det_qry = mysqli_query($link, $qry);

    if ($_POST['mode'] == 'print') {

        if ($branch_id) {
            $company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$branch_id' limit 0,1 "));
            $cer = mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$branch_id' limit 0,1 "));
        } else {
            $company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
            $cer = mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` limit 0,1 "));
        }

        $signature = "For " . $company_info['name'];
        $phon = "";
        if ($company_info["phone1"])
            $phon .= $company_info["phone1"];
        if ($company_info["phone2"])
            $phon .= ", " . $company_info["phone2"];
        if ($company_info["phone3"])
            $phon .= ", " . $company_info["phone3"];

        $header2 = "                       " . $company_info["address"] . ", " . $company_info["city"] . ", " . $company_info["state"] . "-" . $company_info["pincode"];
        //$header3="     Phone Number(s): ".$phon." Email: ".$company_info["email"];
        $header3 = "     Phone Number(s): " . $phon;
        ?>
        <div class="row head_space">
            <div class="span2">
                <img src="../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png"
                    style="width:80px;margin-top:0px;margin-bottom:-70px;" />
            </div>
            <div class="span10 text-center" style="margin-left:10%; text-align: center;">
                <span style="font-size:12px;"><?php echo $page_head_line;
                ; ?></span>
                <h4>
                    <?php echo $company_info["name"]; ?><br>
                    <small>
                        <!--<?php echo $company_info["city"] . "-" . $company_info["pincode"] . ", " . $company_info["state"]; ?><br/>-->
                        <?php echo $company_info["address"]; ?>
                        <?php if ($company_info["phone1"]) { ?>
                            <br>
                            Contact: <?php echo $company_info["phone1"]; ?>
                        <?php } ?>
                        <?php echo $company_info["phone2"]; ?>

                        <?php if ($company_info["email"]) { ?>
                            <br>
                            Email: <?php echo $company_info["email"]; ?>
                        <?php } ?>
                    </small>
                </h4>
            </div>
        </div>
    <?php }



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
                    <td><?= htmlspecialchars($ward_det['ward_name']); ?></td>
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