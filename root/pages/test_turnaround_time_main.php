<?php
session_start();
include("../../includes/connection.php");

$date = date("Y-m-d");
$time = date("H:i:s");

$ip_addr = $_SERVER["REMOTE_ADDR"];
$user = $_SESSION["user"];

$type = $_POST["type"];

if ($type == "tat_calculate") {
    $include_testid = isset($_POST["include_testid"]) ? $_POST["include_testid"] : [];
    $exclude_testid = isset($_POST["exclude_testid"]) ? $_POST["exclude_testid"] : [];
    $date1 = $_POST["date1"];
    $date2 = $_POST["date2"];
    $user = $_POST["user"];

    $include_testids = implode(",", $include_testid);
    $exclude_testids = implode(",", $exclude_testid);

    mysqli_query($link, "DELETE FROM `turn_around_time_calculation` WHERE `user`='$user' AND `ip_addr`='$ip_addr'");

    $str = "";
    if ($date1 && $date2) {
        $str = "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`,a.`date`,a.`time`,a.`testid` FROM `phlebo_sample` a, `testmaster` c 
                WHERE a.`testid` = c.`testid` 
                AND c.`category_id` = 1 AND a.`date` BETWEEN '$date1' AND '$date2' ";

        if (!empty($include_testids)) {
            $str .= " AND a.`testid` IN($include_testids)";
        }

        if (!empty($exclude_testids)) {
            $str .= " AND a.`testid` NOT IN($exclude_testids)";
        }
    }
    $str .= " GROUP BY a.patient_id,a.opd_id,a.ipd_id,a.batch_no,a.testid ORDER BY a.`slno` DESC";
    // echo $str;

    $qry = mysqli_query($link, $str);
    while ($data = mysqli_fetch_array($qry)) {
        $patient_id = $data["patient_id"];
        $opd_id = $data["opd_id"];
        $ipd_id = $data["ipd_id"];
        $batch_no = $data["batch_no"];
        $testid = $data["testid"];

        $reg_date = $data["date"];
        $reg_time = date("H:i:s", strtotime($data["time"]));
        $reg_date_time = $data["date"] . " " . date("H:i:s", strtotime($data["time"]));

        $approve_date = "0000-00-00";
        $approve_time = "00:00:00";
        $approve_date_time = "";
        //$doc_approve = mysqli_fetch_array(mysqli_query($link, "SELECT t_time, t_date, `d_time`, `d_date` FROM `approve_details` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `ipd_id` = '$ipd_id' AND `batch_no` = '$batch_no' AND `testid` = '$testid'"));

        $doc_approve = mysqli_fetch_array(mysqli_query($link, "SELECT `time`, `date` FROM `testresults` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `batch_no` = '$batch_no' AND `testid` = '$testid' order by `slno` desc limit 1"));

        if ($doc_approve && $doc_approve["date"] != "0000-00-00") {
            $approve_date = $doc_approve["date"];
            $approve_time = date("H:i:s", strtotime($doc_approve["time"]));
            $approve_date_time = $doc_approve["date"] . " " . $doc_approve["time"];
        }

        $datetime1 = new DateTime($reg_date_time);
        $datetime2 = new DateTime($approve_date_time);
        $interval = $datetime1->diff($datetime2);

        $tat_str = $interval->format('%h') . " Hours " . $interval->format('%i') . " Minutes";
        $tat_hour = $interval->format('%h');
        $tat_minutes = $interval->format('%i') + ($tat_hour * 60);

        // uhid_and_opdid
        $urgent = mysqli_fetch_array(mysqli_query($link, "SELECT `urgent` FROM `uhid_and_opdid` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id'"));
        $urgent_status = $urgent["urgent"];

        if ($approve_date_time) {

            $status = 0;
            if ($urgent_status == 0) {
                $test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `turn_around_time_routine` AS `tat_time` FROM `testmaster` WHERE `testid` = '$testid'"));
            } else {
                // urgent test
                $test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `turn_around_time_urgent` AS `tat_time` FROM `testmaster` WHERE `testid` = '$testid'"));
            }

            if ($tat_minutes > $test_info["tat_time"]) {
                $status = 2; // Exceeded
            } else {
                $status = 1; // Within
            }
            mysqli_query($link, "INSERT INTO `turn_around_time_calculation`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `reg_date`, `reg_time`, `d_date`, `d_time`, `tat_minutes`, `tat_str`, `status`, `urgent`, `user`, `ip_addr`) VALUES ('$patient_id', '$opd_id', '$ipd_id', '$batch_no', '$testid', '$reg_date', '$reg_time', '$approve_date', '$approve_time', '$tat_minutes', '$tat_str','$status','$urgent_status', '$user', '$ip_addr')");
        }
    }
}

if ($type == "patient_view") {
    $include_testid = isset($_POST["include_testid"]) ? $_POST["include_testid"] : [];
    $exclude_testid = isset($_POST["exclude_testid"]) ? $_POST["exclude_testid"] : [];
    $tat_minutes = $_POST["tat_minutes"];
    $intime = $_POST["intime"];
    $urgent = $_POST["urgent"];
    $user = $_POST["user"];

    $include_testids = implode(",", $include_testid);
    $exclude_testids = implode(",", $exclude_testid);

    $dist_pat_str = "SELECT DISTINCT `patient_id`, `opd_id`, `ipd_id`, `batch_no` 
                     FROM `turn_around_time_calculation` 
                     WHERE `ip_addr` = '$ip_addr'";

    if ($urgent != "") {
        $dist_pat_str .= " AND `urgent` = '$urgent'";
    }
    if ($intime != "") {
        $dist_pat_str .= " AND `status` = '$intime'";
    }

    if ($tat_minutes == 1) {
        $dist_pat_str .= " AND `tat_minutes` < 60";
    } else if ($tat_minutes > 1) {
        $dist_pat_str .= " AND `tat_minutes` >= '$tat_minutes'";
    }

    if (!empty($include_testids)) {
        $dist_pat_str .= " AND `testid` IN($include_testids)";
    }

    if (!empty($exclude_testids)) {
        $dist_pat_str .= " AND `testid` NOT IN($exclude_testids)";
    }

    // echo $dist_pat_str;

    $dist_pat_qry = mysqli_query($link, $dist_pat_str);
    ?>
    <table class="table table-bordered text-center" style="background-color: white;">
        <thead class="table_header_fix">
            <tr>
                <th>#</th>
                <th>Hospital No</th>
                <th>Name</th>
                <th>Test Name</th>
                <th>Sample Collection Time</th>
                <th>Report Release Time</th>
                <th>Turnaround Time</th>
                <th>Status (Within/Exceeded)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $n = 1;
            while ($dist_pat = mysqli_fetch_array($dist_pat_qry)) {
                $patient_id = $dist_pat["patient_id"];
                $opd_id = $dist_pat["opd_id"];
                $ipd_id = $dist_pat["ipd_id"];
                $batch_no = $dist_pat["batch_no"];


                $pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name`, `phone` FROM `patient_info` WHERE `patient_id` = '$patient_id'"));

                $str = "SELECT * FROM `turn_around_time_calculation` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `ipd_id` = '$ipd_id' AND `ip_addr` = '$ip_addr'";

                if ($tat_minutes == 1) {
                    $str .= " AND `tat_minutes` < 60";
                } else if ($tat_minutes > 1) {
                    $str .= " AND `tat_minutes` >= '$tat_minutes'";
                }

                if (!empty($include_testids)) {
                    $str .= " AND `testid` IN($include_testids)";
                }

                if (!empty($exclude_testids)) {
                    $str .= " AND `testid` NOT IN($exclude_testids)";
                }

                $i = 1;
                $qry = mysqli_query($link, $str);
                $num = mysqli_num_rows($qry);

                while ($data = mysqli_fetch_array($qry)) {
                    $test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `testname`, `turn_around_time_routine_str`, `turn_around_time_urgent` FROM `testmaster` WHERE `testid` = '$data[testid]'"));

                    $tat_max_time = $test_info["turn_around_time_urgent"];
                    $total_tat_minutes = $data["tat_minutes"];

                    //~ if ($total_tat_minutes > $tat_max_time) {
                    //~ $status = "Exceeded";
                    //~ } else {
                    //~ $status = "Within";
                    //~ }
                    $status = "";
                    if ($data['status'] == 1) {
                        $status = "Within";
                    }
                    if ($data['status'] == 2) {
                        $status = "Exceeded";
                    }

                    if ($i == 1) {
                        ?>
                        <tr>
                            <td><?php echo $n; ?></td>
                            <td><?php echo $patient_id; ?></td>
                            <td><?php echo $pat_info["name"]; ?></td>
                            <td><?php echo $test_info["testname"]; ?></td>
                            <td><?php echo date("d-m-Y", strtotime($data["reg_date"])) . " " . date("H:i A", strtotime($data["reg_time"])); ?>
                            </td>
                            <td><?php echo date("d-m-Y", strtotime($data["d_date"])) . " " . date("H:i A", strtotime($data["d_time"])); ?>
                            </td>
                            <td>
                                <?php echo $data["tat_str"]; ?>

                            </td>
                            <td><?php echo $status; ?></td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <td><?php echo $test_info["testname"]; ?></td>
                            <td><?php echo date("d-m-Y", strtotime($data["reg_date"])) . " " . date("H:i A", strtotime($data["reg_time"])); ?>
                            </td>
                            <td><?php echo date("d-m-Y", strtotime($data["d_date"])) . " " . date("H:i A", strtotime($data["d_time"])); ?>
                            </td>
                            <td>
                                <?php echo $data["tat_str"]; ?>
                            </td>
                            <td><?php echo $status; ?></td>
                        </tr>
                        <?php
                    }
                    //$i++;
                    $n++;
                }
            }
            ?>
        </tbody>
    </table>
    <?php
}
if ($type == "test_wise_summary_view") {
    $include_testid = isset($_POST["include_testid"]) ? $_POST["include_testid"] : [];
    $exclude_testid = isset($_POST["exclude_testid"]) ? $_POST["exclude_testid"] : [];
    $tat_minutes = $_POST["tat_minutes"];
    $urgent = $_POST["urgent"];
    $user = $_POST["user"];

    $include_testids = implode(",", $include_testid);
    $exclude_testids = implode(",", $exclude_testid);

    $dist_test_str = "SELECT `testid`, COUNT(*) as total_tests, 
                      SUM(CASE WHEN `status` = 1 THEN 1 ELSE 0 END) as within_tat, 
                      SUM(CASE WHEN `status` = 2 THEN 1 ELSE 0 END) as exceed_tat, 
                      SUM(`tat_minutes`) AS `total_tat_minutes`
                      FROM `turn_around_time_calculation` 
                      WHERE `ip_addr` = '$ip_addr'";

    if ($urgent != "") {
        $dist_test_str .= " AND `urgent` = '$urgent'";
    }

    if ($tat_minutes == 1) {
        $dist_test_str .= " AND `tat_minutes` < 60";
    } else if ($tat_minutes > 1) {
        $dist_test_str .= " AND `tat_minutes` >= '$tat_minutes'";
    }

    if (!empty($include_testids)) {
        $dist_test_str .= " AND `testid` IN($include_testids)";
    }

    if (!empty($exclude_testids)) {
        $dist_test_str .= " AND `testid` NOT IN($exclude_testids)";
    }

    $dist_test_str .= " GROUP BY `testid`";

    $dist_test_qry = mysqli_query($link, $dist_test_str);
    ?>
    <table class="table table-bordered text-center" style="background-color: white;">
        <thead class="table_header_fix">
            <tr>
                <th>#</th>
                <th>Test Name</th>
                <th>Total Samples</th>
                <th>Within TAT</th>
                <th>Exceeded TAT</th>
                <th>% Within TAT</th>
                <th>Average TAT</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $n = 1;
            while ($main_data = mysqli_fetch_array($dist_test_qry)) {
                $name = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid` = '{$main_data['testid']}'"));

                $total_tests = $main_data['total_tests'];
                $within_tat = $main_data['within_tat'];
                $exceed_tat = $main_data['exceed_tat'];
                $total_tat_minutes = $main_data['total_tat_minutes'];
                $percentage_within_tat = ($total_tests > 0) ? ($within_tat / $total_tests) * 100 : 0;
                $average_tat = $total_tat_minutes / $total_tests;
                $average_tat = floor($average_tat / 100) . " Hours " . ($average_tat % 60) . " Minutes";
                ?>
                <tr>
                    <td><?php echo $n++; ?></td>
                    <td><?php echo $name['testname']; ?></td>
                    <td><?php echo $total_tests; ?></td>
                    <td><?php echo $within_tat; ?></td>
                    <td><?php echo $exceed_tat; ?></td>
                    <td><?php echo number_format($percentage_within_tat, 2) . '%'; ?></td>
                    <td><?php echo $average_tat; ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
?>