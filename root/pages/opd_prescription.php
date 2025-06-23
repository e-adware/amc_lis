<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$opd_id=mysqli_real_escape_string($link, base64_decode($_GET["opdid"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v=mysqli_real_escape_string($link, base64_decode($_GET["v"]));

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));

$branch_id=$pat_reg["branch_id"];

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$pat_reg["date"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$appoint_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$doctor_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appoint_info[consultantdoctorid]' "));  
if($doctor_info["main_doc_id"]>0)
{
	$doctor_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doctor_info[main_doc_id]' "));  
}

$doctor_room=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `opd_doctor_room` WHERE `room_id`='$doctor_info[room_id]' "));  

$dpt_name=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `department` WHERE `dept_id`='$doctor_info[dept_id]' "));

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));

$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$pat_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$pat_info[district]' "));

$address="";
//~ if($pat_info["gd_name"])
//~ {
	//~ $address="C/O: ".$pat_info["gd_name"].", ";
//~ }
if($pat_info["city"])
{
	//$address.="Town/Vill- ".$pat_info["city"]."<br>";
	$address.="".$pat_info["city"]."<br>";
}
if($pat_info["address"])
{
	$address.=$pat_info["address"].", ";
}
if($pat_info["police"])
{
	$address.="P.S- ".$pat_info["police"]."<br>";
}
if($dist_info["name"])
{
	//$address.=" &nbsp; District- ".$dist_info["name"]."<br>";
}
if($st_info["name"])
{
	//$address.=" &nbsp; State- ".$st_info["name"]."<br>";
}
if($pat_info["pin"])
{
	$address.="PIN-".$pat_info["pin"];
}


$patient_case_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_case_history` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_general_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_general_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_systemic_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_systemic_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$pat_diagnosis=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_vitals=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_antenatal_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_antenatal_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_advice_note=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_advice_note` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_revisit_advice=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_revisit_advice` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Prescription</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>

</head>
<body style="width:100%" onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
<div class="container-fluid">
	<div class="">
		<div class="">
			<?php include('page_header.php');?>
		</div>
	</div>
	<hr/>
	<div>
		<table class="table table-no-top-border">
			<tr>
				<th>Unit No.</th>
				<td>: <?php echo $uhid; ?></td>
				<th>Patient Name</th>
				<th>: <?php echo $pat_info["name"]; ?></th>
			</tr>
			<tr>
				<th><?php echo $prefix_det["prefix"]; ?></th>
				<td>: <?php echo $opd_id; ?></td>
				<th>Reg. Date</th>
				<td>: <?php echo date("M j Y", strtotime($pat_reg["date"])); ?> <?php echo convert_time($pat_reg["time"]); ?></td>
			</tr>
			<tr>
				<th>Phone No</th>
				<td>: <?php echo $pat_info["phone"]; ?></td>
				<th>Gender/Age</th>
				<th>: <?php echo $pat_info["sex"]; ?>/<?php echo $age; ?></th>
			</tr>
			<!--<tr>
				<th>Doctor Name</th>
				<td>: <?php echo $doctor_info["Name"].", ".$doctor_info["qualification"]; ?></td>
				<th>Doc. Room No.</th>
				<td>: <?php echo $doctor_room["room_name"]; ?></td>
			</tr>-->
			<tr>
				<th>Address</th>
				<td colspan="3">: <?php echo $address; ?></td>
				<!--<th>Guardian Name</th>
				<td>: <?php echo $pat_info["gd_name"]; ?></td>
				<th>Relationship</th>
				<td>: <?php echo $pat_other_info["relation"]; ?></td>-->
			</tr>
			
			<tr>
				<th>District</th>
				<td>: <?php echo $dist_info["name"]; ?></td>
				<th>State</th>
				<td>: <?php echo $st_info["name"]; ?></td>
			</tr>
			<tr>
				<th>Ref By</th>
				<td>: <?php echo $ref_doc['ref_name']; ?>
				<!--<th>License No</th>
				<td>: <?php echo $doc_info['regd_no']; ?>-->
			</tr>
		</table>
		<hr/>
	</div>
	<div class="" style="height:820px;line-height: 16px;">
		<div class="" style="margin-left: 0;">
		<?php
			if($patient_case_history["case_history"])
			{
		?>
			<div>
				<b>Case History: </b><br>
				<div class="results">
					<?php
						echo nl2br($patient_case_history["case_history"]);
					?>
				</div>
			</div>
		<?php
			}
		?>
		<?php
			if($patient_antenatal_detail)
			{
		?>
			<div>
				<div class="results">
					<table class="table table-no-top-border">
						<tr>
						<?php if($patient_antenatal_detail["last_menstrual_period"]){ ?>
							<th>LMP</th>
						<?php } ?>
						<?php if($patient_antenatal_detail["est_delivery_date"]){ ?>
							<th>EDD</th>
						<?php } ?>
						<?php if($patient_antenatal_detail["gestational_age"]){ ?>
							<th>GA</th>
						<?php } ?>
						<?php if($patient_antenatal_detail["gestational_age_usg"]){ ?>
							<th>BY USG</th>
						<?php } ?>
						<?php if($patient_antenatal_detail["fundal_height"]){ ?>
							<th>Fundal Height</th>
						<?php } ?>
						<?php if($patient_antenatal_detail["presentation"]){ ?>
							<th>Presentation</th>
						<?php } ?>
						<?php if($patient_antenatal_detail["fetal_heart_rate"]){ ?>
							<th>FHR</th>
						<?php } ?>
						</tr>
						<tr>
						<?php if($patient_antenatal_detail["last_menstrual_period"]){ ?>
							<td><?php echo date("d/m/Y",strtotime($patient_antenatal_detail["last_menstrual_period"])); ?></td>
						<?php } ?>
						<?php if($patient_antenatal_detail["est_delivery_date"]){ ?>
							<td><?php echo date("d/m/Y",strtotime($patient_antenatal_detail["est_delivery_date"])); ?></td>
						<?php } ?>
						<?php if($patient_antenatal_detail["gestational_age"]){ ?>
							<td><?php echo $patient_antenatal_detail["gestational_age"]; ?></td>
						<?php } ?>
						<?php if($patient_antenatal_detail["gestational_age_usg"]){ ?>
							<td><?php echo $patient_antenatal_detail["gestational_age_usg"]; ?></td>
						<?php } ?>
						<?php if($patient_antenatal_detail["fundal_height"]){ ?>
							<td><?php echo $patient_antenatal_detail["fundal_height"]; ?></td>
						<?php } ?>
						<?php if($patient_antenatal_detail["presentation"]){ ?>
							<td><?php echo $patient_antenatal_detail["presentation"]; ?></td>
						<?php } ?>
						<?php if($patient_antenatal_detail["fetal_heart_rate"]){ ?>
							<td><?php echo $patient_antenatal_detail["fetal_heart_rate"]; ?></td>
						<?php } ?>
						</tr>
					</table>
				</div>
			</div>
		<?php
			}
		?>
		<?php
			if($patient_vitals)
			{
		?>
			<div>
				<b>Vital Signs:</b><br>
				<div class="results">
					<table class="table table-no-top-border">
						<tr>
						<?php
							if($patient_vitals["weight"])
							{
						?>
							<th>Weight</th>
						<?php
							}
						?>
						<?php
							if($patient_vitals["height"])
							{
						?>
							<th>Height</th>
						<?php
							}
						?>
						<?php
							if($patient_vitals["BMI_1"])
							{
						?>
							<th>BMI</th>
						<?php
							}
						?>
						<?php
							if($patient_vitals["temp"])
							{
						?>
							<th>Temp</th>
						<?php
							}
						?>
						<?php
							if($patient_vitals["pulse"])
							{
						?>
							<th>Pulse</th>
						<?php
							}
						?>
						<?php
							if($patient_vitals["spo2"])
							{
						?>
							<th>SPO<sub>2</sub></th>
						<?php
							}
						?>
						<?php
							if($patient_vitals["systolic"])
							{
						?>
							<th>BP</th>
						<?php
							}
						?>
						<?php
							if($patient_vitals["RR"])
							{
						?>
							<th>RR</th>
						<?php
							}
						?>
						<?php
							if($patient_vitals["fbs"])
							{
						?>
							<th>FBS</th>
						<?php
							}
						?>
						<?php
							if($patient_vitals["rbs"])
							{
						?>
							<th>RBS</th>
						<?php
							}
						?>
						</tr>
						<tr>
						<?php
							if($patient_vitals["weight"])
							{
						?>
							<td><?php echo $patient_vitals["weight"]; ?>KG</td>
						<?php
							}
						?>
						<?php
							if($patient_vitals["height"])
							{
						?>
							<td><?php echo $patient_vitals["height"]; ?>CM</td>
						<?php
							}
						?>
						<?php
							if($patient_vitals["BMI_1"])
							{
						?>
							<td><?php echo $patient_vitals["BMI_1"]; ?>.<?php echo $patient_vitals["BMI_2"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_vitals["temp"])
							{
						?>
							<td><?php echo $patient_vitals["temp"]; ?><sup>0</sup>C</td>
						<?php
							}
						?>
						<?php
							if($patient_vitals["pulse"])
							{
						?>
							<td><?php echo $patient_vitals["pulse"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_vitals["spo2"])
							{
						?>
							<td><?php echo $patient_vitals["spo2"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_vitals["systolic"])
							{
						?>
							<td><?php echo $patient_vitals["systolic"]; ?>/<?php echo $patient_vitals["diastolic"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_vitals["RR"])
							{
						?>
							<td><?php echo $patient_vitals["RR"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_vitals["fbs"])
							{
						?>
							<td><?php echo $patient_vitals["fbs"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_vitals["rbs"])
							{
						?>
							<td><?php echo $patient_vitals["rbs"]; ?></td>
						<?php
							}
						?>
						</tr>
					</table>
				</div>
			</div>
		<?php
			}
		?>
		
		<?php
			if($patient_general_exam)
			{
		?>
			<div>
				<b>General Examination: </b><br>
				<div class="results">
					<table class="table table-no-top-border">
						<tr>
						<?php
							if($patient_general_exam["pallor"])
							{
						?>
							<th>Pallor</th>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["edema"])
							{
						?>
							<th>Edema</th>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["icterus"])
							{
						?>
							<th>Icterus</th>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["dehydration"])
							{
						?>
							<th>Dehydration</th>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["cyanosis"])
							{
						?>
							<th>Cyanosis</th>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["lymph_node"])
							{
						?>
							<th>Lymph Node</th>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["gcs"])
							{
						?>
							<th>GCS</th>
						<?php
							}
						?>
						</tr>
						<tr>
						<?php
							if($patient_general_exam["pallor"])
							{
						?>
							<td><?php echo $patient_general_exam["pallor"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["edema"])
							{
						?>
							<td><?php echo $patient_general_exam["edema"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["icterus"])
							{
						?>
							<td><?php echo $patient_general_exam["icterus"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["dehydration"])
							{
						?>
							<td><?php echo $patient_general_exam["dehydration"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["cyanosis"])
							{
						?>
							<td><?php echo $patient_general_exam["cyanosis"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["lymph_node"])
							{
						?>
							<td><?php echo $patient_general_exam["lymph_node"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_general_exam["gcs"])
							{
						?>
							<td><?php echo $patient_general_exam["gcs"]; ?></td>
						<?php
							}
						?>
						</tr>
					</table>
				</div>
			</div>
		<?php
			}
		?>
		<?php
			if($patient_systemic_exam)
			{
		?>
			<div>
				<b>Systemic Examination: </b><br>
				<div class="results">
					<table class="table table-no-top-border">
						<tr>
						<?php
							if($patient_systemic_exam["chest"])
							{
						?>
							<th>Chest</th>
						<?php
							}
						?>
						<?php
							if($patient_systemic_exam["cvs"])
							{
						?>
							<th>CVS</th>
						<?php
							}
						?>
						<?php
							if($patient_systemic_exam["cns"])
							{
						?>
							<th>CNS</th>
						<?php
							}
						?>
						<?php
							if($patient_systemic_exam["pa"])
							{
						?>
							<th>P/A</th>
						<?php
							}
						?>
						</tr>
						<tr>
						<?php
							if($patient_systemic_exam["chest"])
							{
						?>
							<td><?php echo $patient_systemic_exam["chest"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_systemic_exam["cvs"])
							{
						?>
							<td><?php echo $patient_systemic_exam["cvs"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_systemic_exam["cns"])
							{
						?>
							<td><?php echo $patient_systemic_exam["cns"]; ?></td>
						<?php
							}
						?>
						<?php
							if($patient_systemic_exam["pa"])
							{
						?>
							<td><?php echo $patient_systemic_exam["pa"]; ?></td>
						<?php
							}
						?>
						</tr>
					</table>
				</div>
			</div>
		<?php
			}
		?>
		
		<?php
			if($pat_diagnosis["diagnosis"])
			{
		?>
			<div>
				<b>Diagnosis: </b><br>
				<div class="results">
					<?php
						echo nl2br($pat_diagnosis["diagnosis"]);
					?>
				</div>
			</div>
		<?php
			}
		?>
		
		<?php
			
			$right_click_image='<img src="../../images/right.png" class="right_click">';
			
			include("eye_prescription_part.php");
			include("pac_prescription_part.php");
		?>
		
		
		<?php
			$test_qry=mysqli_query($link, "SELECT * FROM `opd_clinic_investigation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
			$test_num=mysqli_num_rows($test_qry);
			if($test_num>0)
			{
		?>
			<div>
				<b>Examination: </b><br>
				<div class="results">
					<table class="table table-no-top-border">
					<?php
						$test_slno=1;
						while($test_info=mysqli_fetch_array($test_qry))
						{
							if($test_info["testid"]==0)
							{
								$item_info["testname"]=$test_info["testname"];
							}
							else
							{
								$test_info=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$test_info[testid]'"));
							}
							
							echo "<tr><td colspan='3'>".$test_slno.". ".$test_info["testname"]."</td></tr>";
							
							$test_slno++;
						}
					?>
					</table>
				</div>
			</div>
		<?php
			}
		?>
		<?php
			$medicine_qry=mysqli_query($link, "SELECT * FROM `opd_clinic_medication` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
			$medicine_num=mysqli_num_rows($medicine_qry);
			if($medicine_num>0)
			{
		?>
			<div>
				<b>Medication: </b><br>
				<div class="results">
					<table class="table table-no-top-border" style="width: 90%;">
					<?php
						$medicine_slno=1;
						while($medicine_info=mysqli_fetch_array($medicine_qry))
						{
							if($medicine_info["item_id"]==0)
							{
								$item_info["item_name"]=$medicine_info["item_name"];
							}
							else
							{
								$item_info=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$medicine_info[item_id]'"));
							}
							
							echo "<tr><td>".$medicine_slno.". ".$item_info["item_name"]."</td><td>".$medicine_info["dosage"]." ".$medicine_info["frequency"]." x ".$medicine_info["duration"]."</td></tr>";
							
							$medicine_slno++;
							$tot_br--;
						}
					?>
					</table>
				</div>
			</div>
		<?php
			}
		?>
		<?php
			if($patient_advice_note["advice_note"])
			{
		?>
			<div>
				<b>Advice: </b><br>
				<div class="results">
					<?php
						echo nl2br($patient_advice_note["advice_note"]);
					?>
				</div>
			</div>
		<?php
			}
		?>
		<?php
			if($patient_revisit_advice["revisit_id"]>0)
			{
				$revisit_info=mysqli_fetch_array(mysqli_query($link, "SELECT `revisit_val`,`revisit_name` FROM `revisit_master` WHERE `revisit_id`='$patient_revisit_advice[revisit_id]'"));
		?>
			<div>
				<b>Next follow up: </b><br>
				<div class="results">
					<?php
						echo $revisit_info["revisit_name"];
					?>
				</div>
			</div>
		<?php
			}
		?>
		<?php
			if($patient_revisit_advice["advice_note"])
			{
		?>
			<div>
				<b>Follow up advice: </b><br>
				<div class="results">
					<?php
						echo nl2br($patient_revisit_advice["advice_note"]);
					?>
				</div>
			</div>
		<?php
			}
		?>
		</div>
		<div class="span1" style="margin-left: px;display:none;">
			<b><u>Vital Sign</u></b>
			<br>
			<br>
			<p><b>BP:</b> <?php if($patient_vitals["systolic"]){ echo $patient_vitals["systolic"]; ?>/<?php echo $patient_vitals["diastolic"];} ?></p>
			<br>
			<br>
			<p><b>Pulse:</b> <?php if($patient_vitals["pulse"]){ echo $patient_vitals["pulse"];} ?></p>
			<br>
			<br>
			<p><b>SPO<sub>2</sub>:</b> <?php if($patient_vitals["spo2"]){ echo $patient_vitals["spo2"]."%";} ?></p>
			<br>
			<br>
			<p><b>Temp:</b> <?php if($patient_vitals["temp"]){ echo $patient_vitals["temp"]; ?>(<sup>0</sup>C) <?php } ?></p>
			<br>
			<br>
			<p><b>RBS:</b> <?php if($patient_vitals["rbs"]){ echo $patient_vitals["rbs"]; } ?></p>
			<br>
			<br>
			<p><b>Weight:</b> <?php if($patient_vitals["weight"]){ echo $patient_vitals["weight"]." KG"; } ?></p>
			<br>
			<br>
			<p><b>Height:</b> <?php if($patient_vitals["height"]){ echo $patient_vitals["height"]." CM"; } ?></p>
		</div>
	</div>
	<div style="text-align:right;">
		<b style=""><?php echo $doctor_info["Name"]; ?> <?php if($doctor_info["designation"]){ echo "<br>(".$doctor_info["designation"].")"; } ?></b>
	</div>
</div>
<span id="user" style="display:none;"><?php echo $user; ?></span>
<span id="uhid" style="display:none;"><?php echo $uhid; ?></span>
<span id="opd_id" style="display:none;"><?php echo $opd_id; ?></span>
<script>
	$(document).keydown(function (event) {
		if (event.keyCode == 123 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent F12
			return false;
		} else if (event.ctrlKey && event.shiftKey && event.keyCode == 73 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent Ctrl+Shift+I        
			return false;
		}
	});
	$(document).on("contextmenu",function(e){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			e.preventDefault();
		}
	});
	$(document).ready(function(){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			//window.print();
		}
	});
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
	
</script>
</body>
</html>
<style>

.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
}
.table
{
	margin-bottom:5px;
}
hr
{
	margin:0;
	border-bottom:0;
	border-top: 1px solid #000;
}
*
{
	font-size:12px;
}
.results
{
	margin-left: 20px;
}
.table-bordered
{
	//border: 1px solid #000 !important;
}
.table-bordered th, .table-bordered td
{
	//border-left: 1px solid #000 !important;
	//border-top: 1px solid #000 !important;
}
.table th, .table td
{
	line-height: 15px;
}
@media print {
	@page {
		margin-top: 0;
		margin-bottom: 0;
	}
}
.span7 {
	width: 580px !important;
}
</style>
