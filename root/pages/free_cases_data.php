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
    $sel_test = $_POST['sel_test'];
    $time_per = $_POST['time_period'];
    $free_type = $_POST['free_type'];

    list($start, $end) = explode('@@', $time_per);
    $startTime = $start . ":00";
    $endTime = $end . ":59";
    $type_string = "";

    $ftest_qry = "SELECT DISTINCT(b.`free`), a.`free_name` FROM `pat_free_master` a, `uhid_and_opdid` b WHERE b.`date` BETWEEN '$f_date' AND '$t_date' AND a.id = b.`free` AND b.`free` != 0";
    if ($free_type) {
        $ftest_qry .= " AND a.`id` = '$free_type'";

    }
    if ($time_per) {
        $ftest_qry .= " AND b.`time` BETWEEN '$startTime' AND '$endTime'";
    }
    $ftest_qry .= " ORDER BY a.`free_name`";

    $ward_names = [];
    $ward_sql = mysqli_query($link, $ftest_qry);
    while ($row = mysqli_fetch_array($ward_sql)) {
        $ward_names[] = $row['free_name'];
        if ($free_type) {
            $type_string = $row['free_name'];
        } else {
            $type_string = "All Tests";
        }
    }
    $test_rates = [];


    $test_qry = "SELECT DISTINCT a.`testid`, b.`testname`, a.test_rate FROM `patient_test_details` a, `testmaster` b WHERE a.`testid` = b.`testid` AND a.`date` BETWEEN '$f_date' AND '$t_date'";
    if ($time_per) {
        $test_qry .= " AND a.`time` BETWEEN '$startTime' AND '$endTime'";
    }
    if ($sel_test) {
        $test_qry .= " AND a.`testid` = '$sel_test'";
    }
    $test_qry .= " GROUP BY a.`testid` ORDER BY b.`testname` ";

    $test_names = [];
    $test_ids = [];

    $test_sql = mysqli_query($link, $test_qry);
    while ($row = mysqli_fetch_array($test_sql)) {
        $test_ids[] = $row['testid'];
        $test_names[] = $row['testname'];
        $test_rates[$row['testname']] = $row['test_rate'];  // Store rate by test name
    }



    $count_query = "SELECT 
        c.free_name AS free_type,
        d.testname, b.test_rate,
        COUNT(*) AS total
    FROM uhid_and_opdid a
    JOIN pat_free_master c ON a.free = c.id
    JOIN patient_test_details b ON a.opd_id = b.opd_id
    JOIN testmaster d ON b.testid = d.testid
    WHERE a.date BETWEEN '$f_date' AND '$t_date'";

    if ($free_type) {
        $count_query .= " AND c.id = '$free_type'";
    }
    if ($time_per) {
        $count_query .= " AND a.time BETWEEN '$startTime' AND '$endTime'";
    }
    if ($sel_test) {
        $count_query .= " AND b.testid = '$sel_test'";
    }

    $count_query .= " GROUP BY c.free_name, d.testname ORDER BY c.free_name, d.testname";


    // Store counts in associative array
    $count_sql = mysqli_query($link, $count_query);
    while ($row = mysqli_fetch_array($count_sql)) {
        $ftype = $row['free_type'];
        $tname = $row['testname'];
        $counts[$ftype][$tname] = $row['total'];
    }


    $colspan = 1;

    $column_totals = array_fill_keys($test_names, 0);
    $grand_total = 0;
    $mode = isset($_POST['mode']) ? $_POST['mode'] : 'normal';

    if ($mode == 'print') {


        if ($pat_reg["branch_id"]) {
            $company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));
            $cer = mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));

            $branch_id = $pat_reg["branch_id"];
        } else {
            if ($branch_id) {
                $company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$branch_id' limit 0,1 "));
                $cer = mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$branch_id' limit 0,1 "));
            } else {
                $company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
                $cer = mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` limit 0,1 "));
            }
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
        <?php
    }

    ?>

    <div id="no_print" style="margin-left: auto; padding: 4px 10px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div><strong>Report Generated On:</strong> <?= date('d-M-y') . " / " . date('h:i A'); ?></div>
            <div style="display: flex; gap: 10px;">

                <button
                    onclick="printTable('<?= $f_date; ?>','<?= $t_date; ?>','<?= $sel_test; ?>','<?= $time_per ?>', '<?= $free_type; ?>')"
                    class="btn btn-mini btn-print">
                    <i class="icon-print icon-large"></i> Print
                </button>
                <button
                    onclick="exportTableToExcel('<?= $f_date; ?>','<?= $t_date; ?>','<?= $sel_test; ?>','<?= $time_per ?>', '<?= $free_type; ?>')"
                    class="btn btn-mini btn-warning">
                    <i class="icon-file icon-large"></i> Excel
                </button>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-responsive table-bordered align-middle text-nowrap">
            <tr>
                <th colspan="<?= count($ward_names) + 4 ?>">
                    Free Test Details For <?= $type_string; ?> From: <?= date('d-M-Y', strtotime($f_date)); ?> To:
                    <?= date('d-M-Y', strtotime($t_date)); ?>
                </th>
            </tr>

            <tr style="background-color: #f2f2f2;">
                <th>Test Name↓ / Free Type →</th>
                <?php foreach ($ward_names as $ward): ?>
                    <th><?= htmlspecialchars($ward) ?></th>
                <?php endforeach; ?>
                <th>Total</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
            <tbody>
                <?php foreach ($test_names as $test): ?>
                    <?php
                    $row_total = 0;
                    foreach ($ward_names as $ward) {
                        $val = isset($counts[$ward][$test]) ? $counts[$ward][$test] : 0;
                        $row_total += $val;
                    }

                    if ($row_total == 0)
                        continue;

                    $rate = isset($test_rates[$test]) ? $test_rates[$test] : 0;
                    $amount = $rate * $row_total;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($test) ?></td>

                        <?php
                        foreach ($ward_names as $ward):
                            $val = isset($counts[$ward][$test]) ? $counts[$ward][$test] : 0;
                            $column_totals[$ward] += $val;
                            $cell_style = $val > 0 ? 'background-color: #dff0d8;' : '';
                            ?>
                            <td align="center" style="<?= $cell_style ?>"><?= $val ?></td>
                        <?php endforeach; ?>
                        <td align="center"><strong><?= $row_total ?></strong></td>
                        <td align="center"><?= number_format($rate, 2) ?></td>
                        <td align="center"><strong><?= number_format($amount, 2) ?></strong></td>
                        <?php
                        $grand_total += $row_total;
                        $grand_amount += $amount;
                        ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tr style="background-color: #e0e0e0; font-weight: bold;">
                <td>Total</td>
                <?php foreach ($ward_names as $ward): ?>
                    <td align="center"><?= $column_totals[$ward] ?? 0 ?></td>
                <?php endforeach; ?>
                <td align="center"><?= $grand_total ?? 0 ?></td>
                <td align="center">--</td> <!-- No total rate -->
                <td align="center"><?= number_format($grand_amount ?? 0, 2) ?></td>
            </tr>
        </table>




    </div>
    <?php
}