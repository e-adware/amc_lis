<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
$c_user = $_SESSION["emp_id"];
$f_date = $_POST['from_date'];
$t_date = $_POST['to_date'];

$type = $_POST['type'];

if ($type == 'load_data') {
    $sel_test = $_POST['sel_test'];
    $priority = $_POST['priority'];
    $qry = "SELECT DISTINCT (a.`testid`) FROM `phlebo_sample` a, `testmaster` b, uhid_and_opdid c WHERE a.opd_id = c.opd_id AND a.testid = b.testid  AND b.category_id='1' AND a.`date` BETWEEN '$f_date' AND '$t_date'";
    if ($sel_test) {
        $qry .= " AND a.testid = '$sel_test'";
    }
    $p_qry = "";
    if ($priority) {
        if ($priority == '1') {
            $p_qry .= " AND c.`urgent` = '0'";
        } else {
            $p_qry .= " AND c.`urgent` = '1'";
        }
    }
    $qry .= "$p_qry ORDER BY b.`testname`";

    $tests_head_qry = mysqli_query($link, $qry);
    ?>
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div><b>Segment Definitions: </b>
            <ul>
                <li><strong>Seg 1 (Reg):</strong> Patient Registration -> Barcode Generation</li>
                <li><strong>Seg 2 (Analytical):</strong> Barcode Generation -> Result Entry from Instrument/data entry
                    by
                    operator
                </li>
                <li><strong>Seg 3 (Review):</strong> Result Available -> Technician/Doctor Approval</li>
            </ul>
        </div>
        <div id="no_print" style="margin-left: auto; padding: 4px 10px;">
            <button
                onclick="print_table('<?php echo $f_date; ?>','<?php echo $t_date; ?>','<?php echo $sel_test; ?>','<?php echo $priority; ?>')"
                class="btn btn-mini btn-primary"><i class="icon-print icon-large"></i> Print</button>
            <button
                onclick="exportTableToExcel('<?php echo $f_date; ?>','<?php echo $t_date; ?>','<?php echo $sel_test; ?>','<?php echo $priority; ?>')"
                class="btn btn-mini btn-warning"><i class="icon-file icon-large"></i> Excel</button>
        </div>
    </div>
    <div>
        <p class="head-text">Report Generated On: <?php echo date('d-M-Y / h:i A'); ?></p>
    </div>
    <table class="table table-condensed">

        <thead>
            <tr>
                <th>Test Name</th>
                <th>No. Of Tests</th>
                <th>Seg 1 (Avg / Med)</th>
                <th>Seg 2 (Avg / Med)</th>
                <th>Seg 3 (Avg / Med)</th>
                <th>Overall TAT (Avg / Med)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($tests_head = mysqli_fetch_array($tests_head_qry)) {
                $testname = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid` = '$tests_head[testid];'"));
                $test_count_qry = "SELECT COUNT(a.testid) AS `ctr` FROM `phlebo_sample` a, `uhid_and_opdid` c WHERE a.testid = '$tests_head[testid]' AND a.`date` BETWEEN '$f_date' AND '$t_date' AND a.opd_id = c.opd_id $p_qry ";

                $test_count = mysqli_fetch_array(mysqli_query($link, $test_count_qry));

                $seg1 = mysqli_query($link, "SELECT a.`time` AS `api_time`, b.`time` AS `bar_time`, a.`date` AS `api_date`, b.`date` AS `bar_date` FROM `patient_test_details` a, `phlebo_sample` b, `uhid_and_opdid` c WHERE a.`opd_id` = b.`opd_id` AND a.opd_id = c.opd_id AND a.`testid` = b.`testid` AND b.testid = '$tests_head[testid]' AND a.date BETWEEN '$f_date' AND '$t_date' $p_qry");

                $seg1_api_times = [];
                $seg1_api_dates = [];
                $seg1_bar_times = [];
                $seg1_bar_dates = [];
                while ($row = mysqli_fetch_assoc($seg1)) {
                    $seg1_api_times[] = $row['api_time'];
                    $seg1_api_dates[] = $row['api_date'];
                    $seg1_bar_times[] = $row['bar_time'];
                    $seg1_bar_dates[] = $row['bar_date'];
                }

                $seg2 = mysqli_query($link, "SELECT a.`time` AS `res_time`, b.`time` AS `bar_time`, a.`date` AS `res_date`, b.`date` AS `bar_date` FROM `testresults` a, `phlebo_sample` b, `uhid_and_opdid` c WHERE a.`opd_id` = b.`opd_id` AND a.`opd_id` = c.`opd_id` AND a.`testid` = b.`testid` AND b.testid = '$tests_head[testid]' AND b.date BETWEEN '$f_date' AND '$t_date' $p_qry");
                $seg2_res_times = [];
                $seg2_bar_times = [];
                $seg2_res_dates = [];
                $seg2_bar_dates = [];
                while ($row = mysqli_fetch_assoc($seg2)) {
                    $seg2_res_times[] = $row['res_time'];
                    $seg2_bar_times[] = $row['bar_time'];
                    $seg2_res_dates[] = $row['res_date'];
                    $seg2_bar_dates[] = $row['bar_date'];
                }

                $seg3 = mysqli_query($link, "SELECT  a.`time` AS `res_time`, b.`t_time` AS `t_time`, b.`d_time` AS `d_time`, a.`date` AS `res_date`, b.`t_date` AS `t_date`, b.`d_date` AS `d_date` FROM `testresults` a, `approve_details` b, `uhid_and_opdid` c WHERE a.`opd_id` = b.`opd_id` AND a.`opd_id` = c.`opd_id` AND a.`testid` = b.`testid` AND b.testid = '$tests_head[testid]' AND b.`t_date` BETWEEN '$f_date' AND '$t_date' $p_qry GROUP BY a.`opd_id`");

                $seg3_res_times = [];
                $seg3_t_times = [];
                $seg3_res_dates = [];
                $seg3_t_dates = [];
                while ($row = mysqli_fetch_assoc($seg3)) {
                    $seg3_res_times[] = $row['res_time'];

                    if ($row['t_time'] == '00:00:00') {
                        $seg3_t_times[] = $row['d_time'];
                    } else {
                        $seg3_t_times[] = $row['t_time'];
                    }

                    if ($row['t_date'] == "0000-00-00") {
                        $seg3_t_dates[] = $row['d_date'];
                    } else {
                        $seg3_t_dates[] = $row['t_date'];

                    }

                    $seg3_res_dates[] = $row['res_date'];
                }
                $seg1_avg = calculate_average_tat($seg1_api_dates, $seg1_bar_dates, $seg1_api_times, $seg1_bar_times);
                $seg1_med = calculate_median_tat($seg1_api_dates, $seg1_bar_dates, $seg1_api_times, $seg1_bar_times);

                $seg2_avg = calculate_average_tat($seg2_bar_dates, $seg2_res_dates, $seg2_bar_times, $seg2_res_times);
                $seg2_med = calculate_median_tat($seg2_bar_dates, $seg2_res_dates, $seg2_bar_times, $seg2_res_times);

                $seg3_avg = calculate_average_tat($seg3_res_dates, $seg3_t_dates, $seg3_res_times, $seg3_t_times);
                $seg3_med = calculate_median_tat($seg3_res_dates, $seg3_t_dates, $seg3_res_times, $seg3_t_times);

                $seg1_avg_time = parseTimeString($seg1_avg);
                $seg2_avg_time = parseTimeString($seg2_avg);
                $seg3_avg_time = parseTimeString($seg3_avg);
                $seg1_med_time = parseTimeString($seg1_med);
                $seg2_med_time = parseTimeString($seg2_med);
                $seg3_med_time = parseTimeString($seg3_med);

                // Store as numeric arrays for total calc
                $avg_times = [$seg1_avg_time, $seg2_avg_time, $seg3_avg_time];
                $med_times = [$seg1_med_time, $seg2_med_time, $seg3_med_time];

                // Save individual segment arrays if needed
                $seg_1_avg_arr[] = $avg_times;
                $seg_1_med_arr[] = $med_times;

                // Compute total average and median
                $total_avg = compute_average_from_segments($avg_times);
                $total_median = compute_median_from_segments($med_times);

                // Output (optional)
        

                ?>

                <tr>
                    <th><?php echo $testname['testname']; ?>
                    <td><?php echo $test_count['ctr']; ?></td>
                    <td><?php echo $seg1_avg . " / " . $seg1_med; ?>
                    </td>
                    <td><?php echo $seg2_avg . " / " . $seg2_med; ?>
                    </td>
                    <td><?php echo $seg3_avg . " / " . $seg3_med; ?>
                    <td><?php
                    echo $total_avg . " / " . $total_median;
                    ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php
}


function calculate_average_tat($in_dates, $out_dates, $in_times, $out_times)
{
    if (count($in_dates) !== count($out_dates) || count($in_dates) !== count($in_times) || count($out_dates) !== count($out_times)) {
        return "Invalid input: Mismatched time or date arrays";
    }

    $total_diff = 0;
    $count = 0;

    foreach ($in_dates as $index => $in_date_str) {
        $in_date = $in_date_str . " " . $in_times[$index];
        $out_date = $out_dates[$index] . " " . $out_times[$index];

        $in_time = strtotime($in_date);
        $out_time = strtotime($out_date);

        if ($in_time === false || $out_time === false) {
            continue;
        }

        // Adjust for times that cross over midnight
        if ($out_time < $in_time) {
            $out_time = strtotime("+1 day", $out_time);
        }

        // Calculate the difference in seconds
        $diff = abs($out_time - $in_time);
        $total_diff += $diff;
        $count++;
    }

    if ($count === 0) {
        return "N/A"; // No valid TATs
    }

    $avg_diff_sec = $total_diff / $count;

    // If the average is less than 60 seconds, return N/A
    if ($avg_diff_sec < 60) {
        return "N/A";
    }

    // Convert seconds into hours and minutes
    $hours = floor($avg_diff_sec / 3600);
    $minutes = floor(($avg_diff_sec % 3600) / 60);

    // Build the output string
    $output = '';
    if ($hours > 0) {
        $output .= $hours . ' hour' . ($hours > 1 ? 's' : '');
    }
    if ($minutes > 0 || $hours === 0) {
        if ($output !== '') {
            $output .= ' ';
        }
        $output .= $minutes . ' min' . ($minutes !== 1 ? 's' : '');
    }

    return $output;
}



function calculate_median_tat($in_dates, $out_dates, $in_times, $out_times)
{
    if (
        count($in_dates) !== count($out_dates) ||
        count($in_dates) !== count($in_times) ||
        count($out_dates) !== count($out_times)
    ) {
        return "Invalid input: Mismatched arrays";
    }

    $time_differences = [];

    for ($i = 0; $i < count($in_dates); $i++) {
        $in_datetime_str = $in_dates[$i] . ' ' . $in_times[$i];
        $out_datetime_str = $out_dates[$i] . ' ' . $out_times[$i];

        $in_timestamp = strtotime($in_datetime_str);
        $out_timestamp = strtotime($out_datetime_str);

        if ($in_timestamp !== false && $out_timestamp !== false) {
            // Handle overnight shift
            if ($out_timestamp < $in_timestamp) {
                $out_timestamp += 86400; // add 1 day in seconds
            }

            $diff = $out_timestamp - $in_timestamp;
            $time_differences[] = $diff;
        }
    }

    if (count($time_differences) === 0) {
        return "N/A";
    }

    sort($time_differences);
    $count = count($time_differences);
    $middle = floor($count / 2);

    $median_diff_sec = ($count % 2 === 0)
        ? ($time_differences[$middle - 1] + $time_differences[$middle]) / 2
        : $time_differences[$middle];

    $hours = floor($median_diff_sec / 3600);
    $minutes = floor(($median_diff_sec % 3600) / 60);

    if ($hours === 0 && $minutes === 0) {
        return "Less than a minute";
    }

    $output = '';
    if ($hours > 0) {
        $output .= $hours . ' hour' . ($hours > 1 ? 's' : '');
    }
    if ($minutes > 0 || $hours === 0) {
        if ($output !== '') {
            $output .= ' ';
        }
        $output .= $minutes . ' min' . ($minutes !== 1 ? 's' : '');
    }

    return $output !== '' ? $output : "N/A";
}

function parseTimeString($timeStr)
{
    preg_match('/(?:(\d+)\s*hours?)?\s*(?:(\d+)\s*mins?)?/', strtolower($timeStr), $matches);
    $hours = isset($matches[1]) ? (int) $matches[1] : 0;
    $minutes = isset($matches[2]) ? (int) $matches[2] : 0;
    return $hours * 60 + $minutes;
}

function calculateTotalAvgAndMedian($averages, $medians)
{
    // Convert all time strings to minutes
    $avg_minutes = array_map('parseTimeString', $averages);
    $med_minutes = array_map('parseTimeString', $medians);

    // Average
    $total_avg = array_sum($avg_minutes) / count($avg_minutes);
    $avg_hours = floor($total_avg / 60);
    $avg_mins = round($total_avg % 60);

    // Median
    sort($med_minutes);
    $count = count($med_minutes);
    if ($count % 2 === 1) {
        $median = $med_minutes[floor($count / 2)];
    } else {
        $mid1 = $med_minutes[$count / 2 - 1];
        $mid2 = $med_minutes[$count / 2];
        $median = ($mid1 + $mid2) / 2;
    }
    $median_hours = floor($median / 60);
    $median_mins = round($median % 60);

    return [
        'average' => "{$avg_hours} hours {$avg_mins} mins",
        'median' => "{$median_hours} hours {$median_mins} mins"
    ];
}

function compute_average_from_segments2($timeArray) //With 0
{
    $total = array_sum($timeArray);
    $count = count($timeArray);
    $avg = $count > 0 ? $total / $count : 0;
    return format_minutes($avg);
}

function compute_average_from_segments(array $timeArray): string
{
    // Remove zero values
    $filtered = array_filter($timeArray, function ($value) {
        return $value != 0;
    });

    // Re-index array
    $filtered = array_values($filtered);

    $total = array_sum($filtered);
    $count = count($filtered);

    $avg = $count > 0 ? $total / $count : 0;

    return format_minutes($avg);
}


function compute_median_from_segments(array $timeArray): string
{
    // Filter out 0 values
    $timeArray = array_filter($timeArray, function ($value) {
        return $value !== 0;
    });

    // Re-index array after filtering
    $timeArray = array_values($timeArray);

    // Sort the array
    sort($timeArray);

    $count = count($timeArray);

    // Handle case where array is empty after filtering
    if ($count === 0) {
        return format_minutes(0); // Or handle as needed
    }

    if ($count % 2 === 1) {
        // Odd number of elements, pick the middle one
        $median = $timeArray[floor($count / 2)];
    } else {
        // Even number of elements, average the two middle values
        $mid = (int) ($count / 2);
        $median = ($timeArray[$mid - 1] + $timeArray[$mid]) / 2;
    }

    return format_minutes($median);
}



function compute_median_from_segments2(array $timeArray): string//With 0
{
    // Remove all zero values
    $filtered = array_filter($timeArray, function ($value) {
        return $value != 0;
    });

    // Re-index array
    $filtered = array_values($filtered);
    $count = count($filtered);

    if ($count === 0) {
        return format_minutes(0);
    }

    sort($filtered);

    if ($count % 2 === 1) {
        $median = $filtered[floor($count / 2)];
    } else {
        $mid = $count / 2;
        $median = ($filtered[$mid - 1] + $filtered[$mid]) / 2;
    }

    return format_minutes($median);
}


function format_minutes($minutes)
{
    $hours = floor($minutes / 60);
    $mins = round($minutes % 60);

    if ($hours > 0 && $mins > 0) {
        return "{$hours} hours {$mins} minutes";
    } elseif ($hours > 0) {
        return "{$hours} hours";
    } else {
        return "{$mins} minutes";
    }
}

