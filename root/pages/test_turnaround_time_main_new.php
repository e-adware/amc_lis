<?php
session_start();
include("../../includes/connection.php");


function calculateMedian($arr)
{
    sort($arr); // Sort array in ascending order
    $count = count($arr);

    if ($count === 0) {
        return null; // Handle empty array case
    }

    $middleIndex = floor($count / 2);

    if ($count % 2) {
        // Odd number of elements
        return $arr[$middleIndex];
    } else {
        // Even number of elements
        return ($arr[$middleIndex - 1] + $arr[$middleIndex]) / 2;
    }
}


$date = date("Y-m-d");
$time = date("H:i:s");

$ip_addr = $_SERVER["REMOTE_ADDR"];
$user = $_SESSION["emp_id"];

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
        $str = "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`,a.`date`,a.`time`,a.`testid` FROM `testresults` a, `testmaster` c 
                WHERE a.`testid` = c.`testid` AND (a.`doc` > '0' OR a.`main_tech` > '0')
                AND c.`category_id` = 1 AND a.`date` BETWEEN '$date1' AND '$date2' ";

        if (!empty($include_testids)) {
            $str .= " AND a.`testid` IN($include_testids)";
        }

        if (!empty($exclude_testids)) {
            $str .= " AND a.`testid` NOT IN($exclude_testids)";
        }
        // $str .= " AND a.`testid`='21'";
    }
    $str .= " GROUP BY a.patient_id,a.opd_id,a.ipd_id,a.batch_no,a.testid ORDER BY a.`slno` DESC";
    // echo $str;
    // $i = 0;
    $qry = mysqli_query($link, $str);
    while ($data = mysqli_fetch_array($qry)) {
        $patient_id = $data["patient_id"];
        $opd_id = $data["opd_id"];
        $ipd_id = $data["ipd_id"];
        $batch_no = $data["batch_no"];
        $testid = $data["testid"];


        $phlebo_date_time = mysqli_fetch_array(mysqli_query($link, "SELECT `patient_id`, `opd_id`, `ipd_id`, `batch_no`, `date`, `time`, `testid` FROM `phlebo_sample` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `ipd_id` = '$ipd_id' AND `batch_no` = '$batch_no' AND `testid` = '$testid'"));

        $reg_date = $phlebo_date_time["date"];
        $reg_time = date("H:i:s", strtotime($phlebo_date_time["time"]));
        $reg_date_time = $phlebo_date_time["date"] . " " . date("H:i:s", strtotime($phlebo_date_time["time"]));

        $approve_date = "0000-00-00";
        $approve_time = "00:00:00";
        $approve_date_time = "";
        $doc_approve["date"] = "0000-00-00";
        $doc_approve["time"] = "00:00:00";
        $approve_date_time = "0000-00-00 00:00:00";


        // $doc_approve = mysqli_fetch_array(mysqli_query($link, "SELECT `time`, `date` FROM `testresults` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `batch_no` = '$batch_no' AND `testid` = '$testid' order by `slno` desc limit 1"));

        $doc_approve = mysqli_fetch_array(mysqli_query($link, "SELECT t_time, t_date, `d_time`, `d_date` FROM `approve_details` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `ipd_id` = '$ipd_id' AND `batch_no` = '$batch_no' AND `testid` = '$testid'"));

        // echo "SELECT t_time, t_date, `d_time`, `d_date` FROM `approve_details` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `ipd_id` = '$ipd_id' AND `batch_no` = '$batch_no' AND `testid` = '$testid'";

        if ($doc_approve) {
            if ($doc_approve['d_date'] != "0000-00-00" && $doc_approve['d_time'] != "00:00:00") {
                $doc_approve["date"] = $doc_approve['d_date'];
                $doc_approve["time"] = $doc_approve['d_time'];
            } else {
                $doc_approve["date"] = $doc_approve['t_date'];
                $doc_approve["time"] = $doc_approve['t_time'];
            }
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

        if ($doc_approve && $doc_approve["date"] != "0000-00-00") {

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

            // $i++;
        }
    }
    // echo $i;
}

if ($type == "overall_summary_view") {

    $user = mysqli_real_escape_string($link, $_POST["user"]);
    $date1 = $_POST["date1"];
    $date2 = $_POST["date2"];
    $ip_addr = $_SERVER['REMOTE_ADDR'];

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
            $avg_routine = mysqli_fetch_array(mysqli_query(
                $link,
                "SELECT SUM(`tat_minutes`) AS total_tat_minutes, COUNT(`testid`) AS total_tests 
             FROM `turn_around_time_calculation` 
             WHERE `user`='$user' AND `ip_addr`='$ip_addr' AND `status` = 1 AND `urgent` = 0"
            ));

            // echo "SELECT SUM(`tat_minutes`) AS total_tat_minutes, COUNT(`testid`) AS total_tests 
            //  FROM `turn_around_time_calculation` 
            //  WHERE `user`='$user' AND `ip_addr`='$ip_addr' AND `status` = 1 AND `urgent` = 0";
        
            $avg_emergency = mysqli_fetch_array(mysqli_query(
                $link,
                "SELECT SUM(`tat_minutes`) AS total_tat_minutes, COUNT(`testid`) AS total_tests 
             FROM `turn_around_time_calculation` 
             WHERE `user`='$user' AND `ip_addr`='$ip_addr' AND `status` = 1 AND `urgent` = 1"
            ));

            // echo "SELECT SUM(`tat_minutes`) AS total_tat_minutes, COUNT(`testid`) AS total_tests 
            //  FROM `turn_around_time_calculation` 
            //  WHERE `user`='$user' AND `ip_addr`='$ip_addr' AND `status` = 1 AND `urgent` = 1";
        
            $routine_avg_tat = "0 Hours 0 Minutes";
            $emergency_avg_tat = "0 Hours 0 Minutes";

            if ($avg_routine['total_tests'] > 0) {
                $avg_minutes = $avg_routine['total_tat_minutes'] / $avg_routine['total_tests'];
                $routine_avg_tat = floor($avg_minutes / 60) . " Hours " . ($avg_minutes % 60) . " Minutes";
            }

            if ($avg_emergency['total_tests'] > 0) {
                $avg_minutes = $avg_emergency['total_tat_minutes'] / $avg_emergency['total_tests'];
                $emergency_avg_tat = floor($avg_minutes / 60) . " Hours " . ($avg_minutes % 60) . " Minutes";
            }
            ?>
            <tr>
                <td>1</td>
                <td>Average TAT</td>
                <td><?php echo $routine_avg_tat; ?></td>
                <td><?php echo $emergency_avg_tat; ?></td>
                <td>Mean time from Sample receive to Validation</td>
            </tr>
            <?php
            $tat_result = mysqli_query(
                $link,
                "SELECT `tat_minutes`, `urgent` 
             FROM `turn_around_time_calculation` 
             WHERE `user`='$user' AND `ip_addr`='$ip_addr' AND `status` = 1 
             ORDER BY `tat_minutes` ASC"
            );

            // echo "SELECT `tat_minutes`, `urgent` 
            //  FROM `turn_around_time_calculation` 
            //  WHERE `user`='$user' AND `ip_addr`='$ip_addr' AND `status` = 1 
            //  ORDER BY `tat_minutes` ASC";
        
            $tat_data = [];
            while ($row = mysqli_fetch_array($tat_result)) {
                $tat_data[] = $row;
            }

            $total = count($tat_data);
            $half_count = floor($total * 0.5);
            $ninety_count = floor($total * 0.9);

            $sum_50_routine = $sum_50_emergency = $count_50_routine = $count_50_emergency = 0;
            $sum_90_routine = $sum_90_emergency = $count_90_routine = $count_90_emergency = 0;

            $fifty_perc_array_routine = [];
            $fifty_perc_array_emergency = [];
            $ninety_perc_array_routine = [];
            $ninety_perc_array_emergency = [];

            for ($i = 0; $i < $total; $i++) {
                $is_urgent = $tat_data[$i]['urgent'] == 1;
                $tat_min = (int) $tat_data[$i]['tat_minutes'];

                if ($i < $half_count) {
                    if ($is_urgent) {
                        $fifty_perc_array_emergency[] = $tat_min;

                    } else {
                        $fifty_perc_array_routine[] = $tat_min;
                    }
                }

                if ($i < $ninety_count) {
                    if ($is_urgent) {
                        $$ninety_perc_array_emergency[] = $tat_min;
                    } else {
                        $ninety_perc_array_routine[] = $tat_min;
                    }
                }
            }

            $routine_50_median = calculateMedian($fifty_perc_array_routine);
            $emergency_50_median = calculateMedian($fifty_perc_array_emergency);

            $routine_50_median_hours = floor($routine_50_median / 60);
            $routine_50_median_minutes = $routine_50_median % 60;

            $emergency_50_median_hours = floor($emergency_50_median / 60);
            $emergency_50_median_minutes = $emergency_50_median % 60;
            ?>
            <tr>
                <td>2</td>
                <td>Median TAT</td>
                <td><?php echo $routine_50_median_hours . " Hours " . $routine_50_median_minutes . " Minutes"; ?></td>
                <td><?php echo $emergency_50_median_hours . " Hours " . $emergency_50_median_minutes . " Minutes"; ?></td>
                <td>Average time of fastest 50% tests</td>
            </tr>
            <?php
            $routine_90_median = calculateMedian($ninety_perc_array_routine);
            $emergency_90_median = calculateMedian($ninety_perc_array_emergency);

            $routine_90_median_hours = floor($routine_90_median / 60);
            $routine_90_median_minutes = $routine_90_median % 60;

            $emergency_90_median_hours = floor($emergency_90_median / 60);
            $emergency_90_median_minutes = $emergency_90_median % 60;
            ?>
            <tr>
                <td>3</td>
                <td>90th Percentile TAT</td>
                <td><?php echo $routine_90_median_hours . " Hours " . $routine_90_median_minutes . " Minutes"; ?></td>
                <td><?php echo $emergency_90_median_hours . " Hours " . $emergency_90_median_minutes . " Minutes"; ?></td>
                <td>Average time of fastest 90% tests</td>
            </tr>
            <?php
            $test_summary = mysqli_fetch_array(mysqli_query(
                $link,
                "SELECT 
                SUM(CASE WHEN `urgent` = 0 THEN 1 ELSE 0 END) AS routine_tests, 
                SUM(CASE WHEN `urgent` = 1 THEN 1 ELSE 0 END) AS emergency_tests 
                FROM `turn_around_time_calculation` 
                WHERE `user` = '$user' 
                AND `ip_addr` = '$ip_addr'"
            ));

            // $total_tests = $test_summary['routine_tests'] + $test_summary['emergency_tests'];
            ?>
            <tr>
                <td>4</td>
                <td>Total Tests Included</td>
                <td><?php echo $test_summary['routine_tests']; ?></td>
                <td><?php echo $test_summary['emergency_tests']; ?></td>
                <td>Number of tests matching filter criteria</td>
            </tr>

        </tbody>
    </table>
    <?php
}



if ($type == "test_wise_summary_view") {
    $include_testid = isset($_POST["include_test"]) ? $_POST["include_test"] : 0;
    //$exclude_testid = isset($_POST["exclude_testid"]) ? $_POST["exclude_testid"] : [];
    $tat_minutes = $_POST["tat_minutes"];
    $urgent = $_POST["urgent"];
    $date1 = $_POST["date1"];
    $date2 = $_POST["date2"];
    $user = $_POST["user"];

    // $include_testids = implode(",", $include_testid);
    // $exclude_testids = implode(",", $exclude_testid);


    // $dist_test_str = "SELECT `testid`, COUNT(*) as total_tests, 
    //                   SUM(CASE WHEN `status` = 1 THEN 1 ELSE 0 END) as within_tat, 
    //                   SUM(CASE WHEN `status` = 2 THEN 1 ELSE 0 END) as exceed_tat, 
    //                   SUM(`tat_minutes`) AS `total_tat_minutes`
    //                   FROM `turn_around_time_calculation` 
    //                   WHERE `ip_addr` = '$ip_addr'";

    $dist_test_str = "SELECT `testid`, `reg_date`, reg_time, COUNT(*) as total_tests, 
                      SUM(`status` = '1') as within_tat, 
                      SUM(`status` = '2') as exceed_tat, 
                      SUM(`tat_minutes`) AS `total_tat_minutes`
                      FROM `turn_around_time_calculation` 
                      WHERE `ip_addr` = '$ip_addr' AND user = '$user'";

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

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div><strong>Report Printed On : </strong> <?php echo date('d-M-y') . " / " . date('h:i A'); ?></div>
        <div id="no_print" style="margin-left: auto;">
            <button class="btn btn-mini btn-primary" onclick="print_table();">
                <i class="icon-print icon-large"></i> Print
            </button>
            <button class="btn btn-mini btn-success"
                onclick="exportTableToExcel('<?= $date1 ?>', '<?= $date2 ?>', '<?= $urgent ?>', '<?= $include_testid ?>');">
                <i class="icon-file icon-large"></i> Excel
            </button>
        </div>
    </div>
    <table class="table table-bordered text-center" style="background-color: white;">
        <thead class="table_header_fix">
            <tr>
                <th>#</th>
                <th>Test Name</th>
                <th>Total Samples</th>
                <th>Total Samples Tested</th>
                <!-- <th>Sample Collection Time</th> -->
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
                $name = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid` = '$main_data[testid]'"));

                $total_tests = $main_data['total_tests'];
                $within_tat = $main_data['within_tat'];
                $exceed_tat = $main_data['exceed_tat'];
                $total_tat_minutes = $main_data['total_tat_minutes'];
                $percentage_within_tat = ($total_tests > 0) ? ($within_tat / $total_tests) * 100 : 0;
                $average_tat = $total_tat_minutes / $total_tests;
                $average_tat = floor($average_tat / 60) . " Hours " . ($average_tat % 60) . " Minutes";

                $total_count = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT('opd_id') as total_tests FROM `phlebo_sample` WHERE `testid` = '$main_data[testid]' AND `date` BETWEEN '$date1' AND '$date2'"));

                ?>
                <tr>
                    <td><?= $n++; ?></td>
                    <td><?php echo $name['testname']; ?></td>
                    <td><?php echo $total_count['total_tests'] ?></td>
                    <td><?php echo $total_tests; ?></td>
                    <!-- <td><?php echo date("d-m-Y / h:i a", strtotime($main_data['reg_date'] . "  " . $main_data['reg_time'])); ?> -->
                    </td>
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
