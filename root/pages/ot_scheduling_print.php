<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid = mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$ipd_id = mysqli_real_escape_string($link, base64_decode($_GET["ipd"]));
$schedule_id = mysqli_real_escape_string($link, base64_decode($_GET["sched"]));
$user = mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v = mysqli_real_escape_string($link, base64_decode($_GET["v"]));

$pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$ipd_id' "));

$company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));

$branch_id = $pat_reg["branch_id"];

$prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$ipd_id[type]' "));

$emp = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if ($pat_info["dob"] != "") {
	$age = age_calculator_date_only($pat_info["dob"], $pat_reg["date"]);
} else {
	$age = $pat_info["age"] . " " . $pat_info["age_type"];
}

$patient_ot_schedule = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `schedule_id`='$schedule_id'"));
if (!$patient_ot_schedule) {
	echo "<center><h3>Error !</h3></center>";
	exit;
}

$st_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `state` WHERE `state_id`='$pat_info[state]' "));

$dist_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `district` WHERE `district_id`='$pat_info[district]' "));

$address = "";
if ($pat_info["city"]) {
	//$address.="Town/Vill- ".$pat_info["city"]."<br>";
	$address .= "" . $pat_info["city"] . "<br>";
}
if ($pat_info["address"]) {
	$address .= $pat_info["address"] . ", ";
}
if ($pat_info["police"]) {
	$address .= "P.S- " . $pat_info["police"] . "<br>";
}
if ($dist_info["name"]) {
	//$address.=" &nbsp; District- ".$dist_info["name"]."<br>";
}
if ($st_info["name"]) {
	//$address.=" &nbsp; State- ".$st_info["name"]."<br>";
}
if ($pat_info["pin"]) {
	$address .= "PIN-" . $pat_info["pin"];
}

$request_doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$patient_ot_schedule[request_doc_id]' "));
$ot_area = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$patient_ot_schedule[ot_area_id]'"));
$ot_dept = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$patient_ot_schedule[ot_dept_id]'"));

$slno = 1;
?>
<html>

<head>
    <title>Patient OT Summary</title>
    <link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../../css/custom.css" type="text/css" rel="stylesheet" />
    <script src="../../js/jquery.min.js"></script>
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
    <div class="container-fluid">
        <div class="">
            <div class="">
                <?php //include('page_header.php');
				?>
            </div>
        </div>
        <div class="row">
            <div class="span2">
                <img src="../../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png"
                    style="width:80px;margin-top:0px;margin-bottom:-70px;" />
            </div>
            <div class="span10 text-center" style="margin-left:0px;">
                <span style="font-size:12px;"><?php echo $page_head_line;; ?></span>
                <h4>
                    <?php echo $company_info["name"]; ?><br>
                    <small>
                        <?php echo $company_info["address"]; ?>
                    </small>
                </h4>
            </div>
        </div>
        <div style="text-align:center;">
            <h4 style="margin: 0;">PATIENT OT SUMMARY</h4>
            <b><?php echo strtoupper($ot_dept["ot_dept_name"]); ?> DEPARTMENT</b>
        </div>
        <div class="">
            <div class="">
                <table class="table table-condensed">
                    <tr>
                        <th>Name</th>
                        <td><b>: </b><?php echo $pat_info["name"]; ?></td>

                        <th>Age/Sex</th>
                        <td><b>: </b><?php echo $age . "/" . $pat_info["sex"]; ?></td>

                        <th>Admission Date</th>
                        <td><b>: </b><?php echo date("d-m-Y", strtotime($pat_reg["date"])); ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td colspan="5"><b>: </b><?php echo $address; ?></td>
                    </tr>
                    <tr>
                        <th>Unit No.</th>
                        <td><b>: </b><?php echo $pat_reg["patient_id"]; ?></td>

                        <th>Bill No.</th>
                        <td><b>: </b><?php echo $pat_reg["opd_id"]; ?></td>

                        <!--<th>OT Date</th>
						<td><b>: </b><?php echo date("d-m-Y", strtotime($patient_ot_schedule["ot_date"])); ?></td>-->
                    </tr>
                </table>
            </div>
        </div>
        <hr style="margin: 0;border: 1px solid #000;">
        <center>
            <div class="noprint ">
                <input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
                <input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
            </div>
        </center>
        <div>
            <table class="table table-condensed table-no-top-border">
                <?php
				if ($patient_ot_schedule["ot_date"]) {
				?>
                <tr>
                    <td colspan="2">
                        <table class="table table-condensed">
                            <tr>
                                <th>OT Date</th>
                                <td><b>: </b><?php echo date("d-m-Y", strtotime($patient_ot_schedule["ot_date"])); ?>
                                </td>
                                <th>OT Start Time</th>
                                <td><b>: </b><?php echo date("h:i A", strtotime($patient_ot_schedule["start_time"])); ?>
                                </td>
                                <th>OT End Time</th>
                                <td><b>: </b><?php echo date("h:i A", strtotime($patient_ot_schedule["end_time"])); ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
					$slno++;
				}
				?>
                <?php
				if ($patient_ot_schedule["procedure_id"]) {
					$procedure_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `ot_clinical_procedure` WHERE `procedure_id`='$patient_ot_schedule[procedure_id]'"));
				?>
                <tr>
                    <td colspan="2">
                        <b>Procedure :</b><br>

                        <div class="results">
                            <?php echo nl2br($procedure_info["name"]); ?>
                        </div>
                    </td>
                </tr>
                <?php
					$slno++;
				}
				?>
                <?php
				if ($patient_ot_schedule["anesthesia_id"]) {
					$anesthesia_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `ot_anesthesia_types` WHERE `anesthesia_id`='$patient_ot_schedule[anesthesia_id]'"));
				?>
                <tr>
                    <td colspan="2">
                        <b>Anesthesia :</b><br>

                        <div class="results">
                            <?php echo nl2br($anesthesia_info["name"]); ?>
                        </div>
                    </td>
                </tr>
                <?php
					$slno++;
				}
				?>
                <?php
				if ($patient_ot_schedule["diagnosis"]) {
				?>
                <tr>
                    <td colspan="2">
                        <b>Diagnosis :</b><br>
                        <div class="results">
                            <?php echo nl2br($patient_ot_schedule["diagnosis"]); ?>
                        </div>
                    </td>
                </tr>
                <?php
					$slno++;
				}
				?>
                <?php
				if ($patient_ot_schedule["ot_note"]) {
				?>
                <tr>
                    <td colspan="2">
                        <b>OT Note :</b><br>
                        <div class="results">
                            <?php echo nl2br($patient_ot_schedule["ot_note"]); ?>
                        </div>
                    </td>
                </tr>
                <?php
					$slno++;
				}
				?>
                <?php
				$patient_ot_resources_qry = mysqli_query($link, "SELECT a.*,b.`resource_name` FROM `patient_ot_resources` a, `ot_resource_master` b WHERE a.`resource_id`=b.`resource_id` AND a.`patient_id`='$uhid' AND a.`ipd_id`='$ipd_id' AND a.`schedule_id`='$schedule_id' ORDER BY b.`sequence` ASC");

				$patient_ot_resources_num = mysqli_num_rows($patient_ot_resources_qry);
				if ($patient_ot_resources_num > 0) {
				?>
                <tr>
                    <td colspan="2">
                        <b>OT Resources :</b><br>
                        <div class="results">
                            <table class="table table-condensed">
                                <?php
									while ($patient_ot_resources = mysqli_fetch_array($patient_ot_resources_qry)) {
										$resource_id = $patient_ot_resources["resource_id"];
										$ot_staff_id = $patient_ot_resources["emp_id"];

										$staff_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ot_staff_id' "));
									?>
                                <tr>
                                    <th style="width: 200px;"><?php echo $patient_ot_resources["resource_name"]; ?>
                                        <span style="float:right;">:</span>
                                    </th>
                                    <td><?php echo $staff_info["name"]; ?></td>
                                </tr>
                                <?php
									}
								}
								?>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../../css/loader.css" />
<script>
$(document).ready(function() {
    $("#loader").hide();
});

function close_window(e) {
    var unicode = e.keyCode ? e.keyCode : e.charCode;

    if (unicode == 27) {
        window.close();
    }
}
</script>
<style>
.txt_small {
    font-size: 10px;
}

.table {
    font-size: 13px;
}

@media print {
    .noprint {
        display: none;
    }
}

.results {
    margin-left: 30px;
}

.table {
    margin-bottom: 0px;
}

.table-condensed th,
.table-condensed td {
    //padding: 0;
    padding: 0 10px 0 0;
}

@page {
    margin: 0.2cm;
}
</style>