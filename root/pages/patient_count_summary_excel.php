<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
$c_user = $_SESSION["emp_id"];
$f_date = base64_decode($_GET['f_date']);
$t_date = base64_decode($_GET['t_date']);


$filename = "summary_report_from_" . $f_date . "_to_" . $t_date . ".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename=' . $filename);

$count_pat_qry = mysqli_query($link, "SELECT CONCAT(type_prefix, pat_type) AS reg_type, COUNT(opd_id) AS opd_id_count
FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' GROUP BY reg_type; ");

?>

<div>
    <p class="head-text">Report Generated On: <?php echo date('d-M-Y / h:i A'); ?></p>
</div>
<table class="table ">
    <thead>
        <th>Registration Type</th>
        <th>Total Patients</th>
    </thead>
    <tbody>
        <?php
        $total = 0;
        while ($count_pat = mysqli_fetch_array($count_pat_qry)) {
            $total += $count_pat['opd_id_count'];
            ?>
            <tr>
                <td><?= $count_pat['reg_type'] ?></td>
                <td><?= $count_pat['opd_id_count'] ?></td>
            </tr>
        <?php }

        ?>
    <tfoot>
        <th style="text-align: right;">Total: </th>
        <th style="text-align: left;"><?= $total; ?></th>
    </tfoot>
    </tbody>
</table>