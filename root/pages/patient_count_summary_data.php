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

    $emer1free = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `time` BETWEEN '17:00:01' AND '22:00:00' AND `free` NOT IN (0, 16,17,18) AND `pat_type` = 'EMER'"));

    $emer1paid = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `time` BETWEEN '17:00:01' AND '22:00:00' AND `free` = '0' AND `pat_type` = 'EMER'"));

    $emer1nhm = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `time` BETWEEN '17:00:01' AND '22:00:00' AND `type_prefix` = 'NRM_EMRG/'"));

    $emer2free = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `time` BETWEEN '22:00:01' AND '23:59:59' AND `free` NOT IN (0, 16,17,18) AND `pat_type` = 'EMER'"));

    $emer2paid = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `time` BETWEEN '22:00:01' AND '23:59:59' AND `free` = '0' AND `pat_type` = 'EMER'"));

    $emer2nhm = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `time` BETWEEN '22:00:01' AND '23:59:59' AND `type_prefix` = 'NRM_EMRG/'"));

    $emer3free = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `time` BETWEEN '00:00:00' AND '08:00:00' AND `free` NOT IN (0, 16,17,18) AND `pat_type` = 'EMER'"));

    $emer3paid = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `time` BETWEEN '00:00:00' AND '08:00:00' AND `free` = '0' AND `pat_type` = 'EMER'"));

    $emer3nhm = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`opd_id`) AS `tot` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$f_date' AND '$t_date' AND `time` BETWEEN '00:00:00' AND '08:00:00' AND `type_prefix` = 'NRM_EMRG/'"));
    if ($_POST['mode'] == 'print') {

        if ($branch_id) {
            $company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$branch_id' limit 0,1 "));
            $cer = mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$branch_id' limit 0,1 "));
        } else {
            $company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
            $cer = mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` limit 0,1 "));
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
    <?php }
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
                        <th colspan="2">Total Emergency Cases:
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

    </div>
    <!-- NRHM Cases END -->
    <!-- EMERGENCY 1 CASES -->
    <div class="row" style="display: flex; justify-content: center;">

        <div class="span4">
            <table class="table table-condensed">
                <tbody>
                    <tr class="theader">
                        <th colspan="2">Emergency(5PM to 10PM):
                            <?= $emer1free['tot'] + $emer1paid['tot'] + $emer1nhm['tot'] ?>
                        </th>
                    </tr>
                    <tr>
                        <th>Free: </th>
                        <td><?= $emer1free['tot']; ?></td>
                    </tr>
                    <tr>
                        <th>Paid: </th>
                        <td><?= $emer1paid['tot'] ?></td>
                    </tr>
                    <tr>
                        <th>NHM: </th>
                        <td><?= $emer1nhm['tot'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- EMERGENCY 1 CASES END-->
        <!-- EMERGENCY 2 CASES-->

        <div class="span4">
            <table class="table table-condensed">
                <tbody>
                    <tr class="theader">
                        <th colspan="2">Emergency(10PM to 12AM):
                            <?= $emer2free['tot'] + $emer2paid['tot'] + $emer2nhm['tot'] ?>
                        </th>
                    </tr>
                    <tr>
                        <th>Free: </th>
                        <td><?= $emer2free['tot']; ?></td>
                    </tr>
                    <tr>
                        <th>Paid: </th>
                        <td><?= $emer2paid['tot'] ?></td>
                    </tr>
                    <tr>
                        <th>NHM: </th>
                        <td><?= $emer2nhm['tot'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- EMERGENCY 2 CASES END-->

        <!-- EMERGENCY 3 CASES-->

        <div class="span4">
            <table class="table table-condensed">
                <tbody>
                    <tr class="theader">
                        <th colspan="2">Emergency(12AM to 8AM):
                            <?= $emer3free['tot'] + $emer3paid['tot'] + $emer3nhm['tot'] ?>
                        </th>
                    </tr>
                    <tr>
                        <th>Free: </th>
                        <td><?= $emer3free['tot']; ?></td>
                    </tr>
                    <tr>
                        <th>Paid: </th>
                        <td><?= $emer3paid['tot'] ?></td>
                    </tr>
                    <tr>
                        <th>NHM: </th>
                        <td><?= $emer3nhm['tot'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- EMERGENCY 3 CASES END-->

    </div>
    <?php
}