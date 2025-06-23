<?php
session_start();
$user = $_SESSION["emp_id"];
$ip_addr = $_SESSION["ip_addr"];

include '../../includes/connection.php';


$date1 = base64_decode($_GET['date1']);
$date2 = base64_decode($_GET['date2']);
$urgent = base64_decode($_GET['urgent']);
$include_test = base64_decode($_GET['include_testid']);

$filename = "Overall_TAT_Report_" . $date1 . ".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename=' . $filename);


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
            $average_tat = floor($average_tat / 100) . " Hours " . ($average_tat % 60) . " Minutes";

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