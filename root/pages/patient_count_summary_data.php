<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
$c_user = $_SESSION["emp_id"];
$f_date = $_POST['from_date'];
$t_date = $_POST['to_date'];

$type = $_POST['type'];

if ($type == 'load_data') {


    $ipd_paid = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `type`='2' AND `free`='0'"));

    $opd_paid = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `type`='1' AND `free`='0'"));

    $nr_opd = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `type` = '3' AND `pat_type` = 'OPD' AND `free`='0'"));

    $nr_ipd = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `type` = '3' AND `pat_type` = 'IPD' AND `free`='0'"));

    $free_opd = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `pat_type` = 'OPD' AND `free` NOT IN (0, 16,17,18)"));
    $free_ipd = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `pat_type` = 'IPD' AND `free` NOT IN (0, 16,17,18)"));

    $night_ipd = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `type_prefix` = 'NRM_EMRG/' AND `pat_type` = 'IPD' AND `free` = '0'"));
    $night_opd = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `type_prefix` = 'NRM_EMRG/' AND `pat_type` = 'OPD' AND `free` = '0'"));

    $emer = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND (`type_prefix` = 'EC/' OR `pat_type` = 'EMER') AND `free` = '0'"));


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
        <p class="head-text">Report Generated On: <?php echo date('d-M-Y / h:i A'); ?> <br>From Date:
            <?= date('d-M-Y', strtotime($f_date)) . " to " . date('d-M-Y', strtotime($t_date)); ?>
        </p>
    </div>
    <div class="row" style="display: flex; justify-content: center;">


        <!-- Paid Cases -->
        <div class="span4">
            <table class="table table-condensed">
                <tbody>
                    <tr class="theader">
                        <th colspan="2">Total Paid Cases: <?= $ipd_paid['tot'] + $opd_paid['tot']; ?></th>
                    </tr>
                    <tr>
                        <th>IPD: </th>
                        <td><?= $ipd_paid['tot']; ?></td>
                    </tr>
                    <tr>
                        <th>OPD: </th>
                        <td><?= $opd_paid['tot']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Paid Cases End-->

        <!-- FREE Cases -->
        <div class="span4">
            <table class="table table-condensed">
                <tbody>
                    <tr class="theader">
                        <th colspan="2">Total Free Cases: <?= $free_opd['tot'] + $free_ipd['tot']; ?></th>
                    </tr>
                    <tr>
                        <th>IPD: </th>
                        <td><?= $free_ipd['tot']; ?></td>
                    </tr>
                    <tr>
                        <th>OPD: </th>
                        <td><?= $free_opd['tot']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- FREE Cases END-->

        <!-- NRHM Cases -->
        <div class="span4">
            <table class="table table-condensed">
                <tbody>
                    <tr class="theader">
                        <th colspan="2">Total NRHM Cases: <?= $nr_opd['tot'] + $nr_ipd['tot']; ?></th>
                    </tr>
                    <tr>
                        <th>IPD: </th>
                        <td><?= $nr_ipd['tot']; ?></td>
                    </tr>
                    <tr>
                        <th>OPD: </th>
                        <td><?= $nr_opd['tot']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- NRHM Cases END -->
    </div>
    <div class="row" style="display: flex; justify-content: center;">

        <!-- EMERGENCY Cases -->
        <div class="span4">
            <table class="table table-condensed">
                <tbody>
                    <tr class="theader">
                        <th colspan="2">Total Emergency/Night Cases:
                            <?= $night_opd['tot'] + $night_ipd['tot'] + $emer['tot']; ?>
                        </th>
                    </tr>
                    <tr>
                        <th>NRHM EMERGENCY IPD: </th>
                        <td><?= $night_ipd['tot']; ?></td>
                    </tr>
                    <tr>
                        <th>NRHM EMERGENCY OPD: </th>
                        <td><?= $night_opd['tot']; ?></td>
                    </tr>
                    <tr>
                        <th>EMERGENCY: </th>
                        <td><?= $emer['tot']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- NRHM Cases END -->

    </div>
    <?php
}