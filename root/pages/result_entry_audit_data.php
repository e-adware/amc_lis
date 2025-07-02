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
                        <th>Slno.</th>
                        <th>Hospital No.</th>
                        <th>Test Name</th>
                        <th>Parameter Name</th>
                        <th>Result Value</th>
                        <th>Entry Type</th>
                        <th>Entered/Changed By</th>
                        <th>Date & Time</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $n = 1;

                    // $audit_qry = mysqli_query($link, "SELECT `patient_id`, `opd_id`, `testid`, `paramid`, `result`, `status`, `instrument_id`, `time`, `date`, `doc`, `tech`, `main_tech` FROM `testresults` WHERE `date` BETWEEN '$f_date' AND '$t_date' ORDER BY `time` DESC");
                

                    $audit_qry = mysqli_query($link, "
                        SELECT tr.*
                        FROM testresults tr
                        INNER JOIN (
                            SELECT patient_id, opd_id, testid, paramid, MAX(CONCAT(date, ' ', time)) AS latest_datetime
                            FROM testresults
                            WHERE date BETWEEN '$f_date' AND '$t_date'
                            GROUP BY patient_id, opd_id, testid, paramid
                        ) latest ON tr.patient_id = latest.patient_id 
                                 AND tr.opd_id = latest.opd_id 
                                 AND tr.testid = latest.testid 
                                 AND tr.paramid = latest.paramid 
                                 AND CONCAT(tr.date, ' ', tr.time) = latest.latest_datetime
                        ORDER BY tr.time DESC
                    ");

                    while ($audit = mysqli_fetch_array($audit_qry)) {
                        $test_name = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid` = '$audit[testid]'"));
                        $param_name = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `Parameter_old` WHERE `ID` = '$audit[paramid]'"));
                        $hosp_no = mysqli_fetch_array(mysqli_query($link, "SELECT `hosp_no` FROM `patient_info` WHERE `patient_id`='$audit[patient_id]'"));

                        $formula_check = mysqli_fetch_array(mysqli_query($link, "SELECT `ParameterID` FROM `parameter_formula` WHERE `ParameterID` = '{$audit['paramid']}'"));
                        $is_formula_based = !empty($formula_check);

                        $entry_type = "Unknown";
                        $entry_type1 = "by Unknown";

                        if ($audit['instrument_id'] == 0) {
                            $entry_type = "Manual";
                            if (!empty($audit['doc'])) {
                                $entered_by = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` = '{$audit['doc']}'"));
                                $entry_type1 = "by " . $entered_by['name'];
                            } elseif (!empty($audit['main_tech'])) {
                                $entered_by = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` = '{$audit['main_tech']}'"));
                                $entry_type1 = "by Tech. " . $entered_by['name'];
                            } elseif (!empty($audit['tech'])) {
                                $entered_by = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` = '{$audit['tech']}'"));
                                $entry_type1 = "by Tech. " . $entered_by['name'];
                            }
                        } elseif ($audit['instrument_id'] == 1 && $audit['status'] == 3) {
                            $entry_type = "Machine";
                            $instrument = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_instrument_master` WHERE `id` = '1'"));
                            $entry_type1 = "by Machine: " . $instrument['name'];
                        } elseif ($audit['instrument_id'] == 1 && $audit['status'] == 4) {
                            $entry_type = "Edited";
                            if (!empty($audit['doc'])) {
                                $entered_by = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` = '{$audit['doc']}'"));
                                $entry_type1 = "Edited by " . $entered_by['name'];
                            } elseif (!empty($audit['main_tech'])) {
                                $entered_by = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` = '{$audit['main_tech']}'"));
                                $entry_type1 = "Edited by Tech. " . $entered_by['name'];
                            } elseif (!empty($audit['tech'])) {
                                $entered_by = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` = '{$audit['tech']}'"));
                                $entry_type1 = "Edited by Tech. " . $entered_by['name'];
                            }
                        }

                        if ($is_formula_based) {
                            $entry_type1 .= " <span style='color: orange;'>(Calculated Value)</span>";
                            $remark = "<span style='color: purple; font-weight: bold;'>System Generated</span>";
                        } else {
                            if ($entry_type == "Manual") {
                                $remark = "<span style='color: red; font-weight: bold;'>Manually Entered</span>";
                            } elseif ($entry_type == "Machine") {
                                $remark = "<span style='color: green; font-weight: bold;'>Auto-Imported</span>";
                            } elseif ($entry_type == "Edited") {
                                $remark = "<span style='color: blue; font-weight: bold;'>Value Updated Manually</span>";
                            } else {
                                $remark = "<span style='color: gray;'>Unknown Entry Type</span>";
                            }
                        }

                        if (strpos($entry_type1, 'by Tech.') !== false || strpos($entry_type1, 'Edited by Tech.') !== false) {
                            $entry_type1 = "<span style='color: red; font-weight: bold;'>$entry_type1</span>";
                        } elseif (strpos($entry_type1, 'by Machine') !== false) {
                            $entry_type1 = "<span style='color: green; font-weight: bold;'>$entry_type1</span>";
                        } elseif (strpos($entry_type1, 'Edited by ') !== false || strpos($entry_type1, 'by ') !== false) {
                            $entry_type1 = "<span style='color: blue; font-weight: bold;'>$entry_type1</span>";
                        } else {
                            $entry_type1 = "<span style='color: gray;'>$entry_type1</span>";
                        }
                        ?>
                        <tr data-entry-type="<?php echo $entry_type; ?>">
                            <td><?php echo $n++; ?></td>
                            <td><?php echo $audit['patient_id']; ?></td>
                            <td><?php echo $hosp_no['hosp_no']; ?></td>
                            <td><?php echo $test_name['testname']; ?></td>
                            <td><?php echo $param_name['Name']; ?></td>
                            <td><?php echo $audit['result']; ?></td>
                            <td><?php echo $entry_type; ?></td>
                            <td><?php echo $entry_type1; ?></td>
                            <td><?php echo date("d-M-Y / h:i A", strtotime($audit['date'] . ' ' . $audit['time'])); ?></td>
                            <td><?php echo $remark; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>


            </table>
        </div>



        <?php
}