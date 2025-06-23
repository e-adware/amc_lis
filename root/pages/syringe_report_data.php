<?php
session_start();
include("../../includes/connection.php");

$date = date("Y-m-d");
$time = date("H:i:s");
$type = $_POST['type'];

$fromDate = $_POST['dateF'];
$toDate = $_POST['dateT'];

$user = trim($_SESSION['emp_id']);

if ($type == 'load_report') {
    ?>
    <h5>Syringe Usage Report From:
        <?php echo date('d-m-Y', strtotime($fromDate)) . " to: " . date('d-m-Y', strtotime($toDate)); ?>
    </h5>
    <table class="table table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th>Patient ID</th>
                <th>OPD/IPD ID</th>
                <th>Patient Name</th>
                <th>Syringes Used (<span id="total_usage"></span>)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $n = 1;
            $total = 0;
            $qry = mysqli_query($link, "SELECT * FROM `syringe_usage` WHERE `date` BETWEEN '$fromDate' AND '$toDate' GROUP BY `patient_id`");
            while ($row = mysqli_fetch_array($qry)) {
                $pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `patient_info` WHERE `patient_id` = '$row[patient_id]'"));
                $usage = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `syringe_usage` WHERE `patient_id` = '$row[patient_id]'"));
                ?>
                <tr>
                    <td><?php echo $n++; ?></td>
                    <td><?php echo $row['patient_id']; ?></td>
                    <td><?php echo $row['opd_id']; ?></td>
                    <td><?php echo $pat_info['name']; ?></td>
                    <td><?php echo $usage; ?></td>
                </tr>
                <?php
                $total += $usage;
            } ?>
            <tr>
                <th colspan="4" style="text-align:right;">Total Syringes Used:</th>
                <th><?php echo $total; ?></th>
            </tr>
        </tbody>
    </table>
    <script>
        $("#total_usage").html('<?php echo $total; ?>')
    </script>
    <?php
}