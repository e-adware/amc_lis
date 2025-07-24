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

<div>
    <p class="head-text">Report Generated On: <?php echo date('d-M-Y / h:i A'); ?> <br>From Date:
        <?= date('d-M-Y', strtotime($f_date)) . " to " . date('d-M-Y', strtotime($t_date)); ?>
    </p>
</div>
<div class="row">


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
<div class="row">

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