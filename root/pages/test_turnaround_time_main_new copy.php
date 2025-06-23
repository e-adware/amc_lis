<?php
session_start();
include("../../includes/connection.php");


function median(array $numbers)
{
    sort($numbers); // Sort ascending
    $count = count($numbers);
    $middle = floor($count / 2);

    if ($count === 0) {
        return null; // No median for an empty array
    }

    // If even, average the two middle numbers
    if ($count % 2 === 0) {
        return ($numbers[$middle - 1] + $numbers[$middle]) / 2;
    } else {
        return $numbers[$middle];
    }
}


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


        //$doc_approve = mysqli_fetch_array(mysqli_query($link, "SELECT `time`, `date` FROM `testresults` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `batch_no` = '$batch_no' AND `testid` = '$testid' order by `slno` desc limit 1"));

        $doc_approve = mysqli_fetch_array(mysqli_query($link, "SELECT t_time, t_date, `d_time`, `d_date` FROM `approve_details` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `ipd_id` = '$ipd_id' AND `batch_no` = '$batch_no' AND `testid` = '$testid'"));


        if ($doc_approve) {
            $doc_approve["date"] = $doc_approve['d_date'] ?? $doc_approve['t_date'];
            $doc_approve["time"] = $doc_approve['d_time'] ?? $doc_approve['t_time'];
        }

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

if ($type == "overall_summary_view") {

    $user = $_POST["user"];
    $date1 = $_POST["date1"];
    $date2 = $_POST["date2"];

    ?>
    <table class="table table-bordered text-center" style="background-color: white;">
        <thead class="table_header_fix">
            <tr>
                <th>#</th>
                <th>Metric</th>
                <th>Value (Hours)- Routine</th>
                <th>Value (Hours)- Emergency</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $avg = mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`tat_minutes`) AS total_tat_minutes, COUNT(`testid`) AS total_tests FROM `turn_around_time_calculation` WHERE `user`='$user' AND `ip_addr`='$ip_addr'"));
            $total_tests = $avg['total_tests'];
            if ($total_tests > 0) {
                $avg_tats = $avg['total_tat_minutes'] / $total_tests;
                $hours = floor($avg_tats / 60);
                $minutes = $avg_tats % 60;
            } else {
                $hours = 0;
                $minutes = 0;
            }
            $avg_tat = $hours . " Hours " . $minutes . " Minutes";
            ?>

            <tr>
                <td>1</td>
                <td>Average TAT</td>
                <td><?php echo $avg_tat; ?></td>
                <td></td>
                <td>Mean time from Sample receive to Validation</td>
            </tr>
            <?php
            $tat_minutes_result = mysqli_query($link, "SELECT `tat_minutes` FROM `turn_around_time_calculation` WHERE `user`='$user' AND `ip_addr`='$ip_addr'");
            $tat_minutes_array = [];
            while ($row = mysqli_fetch_assoc($tat_minutes_result)) {
                $tat_minutes_array[] = (int) $row['tat_minutes'];

            }

            if (!empty($tat_minutes_array)) {
                $median_tat_minutes = median($tat_minutes_array);
                $median_hours = floor($median_tat_minutes / 60);
                $median_minutes = $median_tat_minutes % 60;
                $median_tat = "{$median_hours} Hours {$median_minutes} Minutes";
            } else {
                $median_tat = "0 Hours 0 Minutes";
            }
            ?>
            <tr>
                <td>2</td>
                <td>Median TAT</td>
                <td><?php echo $median_tat; ?></td>
                <td></td>
                <td>50% of tests completed within this time</td>
            </tr>

            <tr>
                <td>3</td>
                <td>90th Percentile TAT</td>
                <td></td>
                <td></td>
                <td>90% of tests completed within this time</td>
            </tr>
            <tr>
                <td>4</td>
                <td>Total Tests Included</td>
                <td></td>
                <td></td>
                <td>Number of tests matching filter criteria</td>
            </tr>
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