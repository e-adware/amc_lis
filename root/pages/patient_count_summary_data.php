<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
$c_user = $_SESSION["emp_id"];
$f_date = $_POST['from_date'];
$t_date = $_POST['to_date'];

$type = $_POST['type'];

if ($type == 'load_data') {
    $count_pat_qry = mysqli_query($link, "SELECT CONCAT(type_prefix, pat_type) AS reg_type, COUNT(opd_id) AS opd_id_count FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' GROUP BY reg_type; ");

    ?>
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
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

    <?php
}