<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$ipd_id=mysqli_real_escape_string($link, base64_decode($_GET["ipd"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v=mysqli_real_escape_string($link, base64_decode($_GET["v"]));

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$ipd_id' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));

$branch_id=$pat_reg["branch_id"];

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$ipd_id[type]' "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$pat_reg["date"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$patient_discharge_summary=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' "));
if(!$patient_discharge_summary)
{
	echo "<center><h3>Error !</h3></center>";
	exit;
}

$patient_discharge_summary_obs=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discharge_summary_obs` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' "));
$patient_discharge_summary_baby_qry=mysqli_query($link, " SELECT * FROM `patient_discharge_summary_baby` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' ");
$patient_discharge_summary_baby_num=mysqli_num_rows($patient_discharge_summary_baby_qry);
$patient_antenatal_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_antenatal_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd_id' "));

$doc_info=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$patient_discharge_summary[consultantdoctorid]' "));

$dept_info=mysqli_fetch_array(mysqli_query($link," SELECT a.`name` FROM `doctor_specialist_list` a, `ipd_pat_doc_details` b WHERE a.`speciality_id`=b.`dept_id` AND b.`patient_id`='$uhid' AND b.`ipd_id`='$ipd_id' "));


$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$pat_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$pat_info[district]' "));

$address="";
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

$discharge_date="";
$patient_discharge_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' "));
if($patient_discharge_det)
{
	$discharge_date=date("d-m-Y",strtotime($patient_discharge_det["date"]));
}
else
{
	if($patient_discharge_summary["discharge_date"])
	{
		$discharge_date=date("d-m-Y",strtotime($patient_discharge_summary["discharge_date"]));
	}
}

if($patient_discharge_summary["discharge_id"]>0)
{
	$discharge_type_info=mysqli_fetch_array(mysqli_query($link, "SELECT `discharge_name` FROM `discharge_master` WHERE `discharge_id`='$patient_discharge_summary[discharge_id]'"));
}

// Get Ward or Bed
$ward_name="";
$pat_bed_alloc=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `alloc_type`=1 ORDER BY `slno` DESC"));
if($pat_bed_alloc)
{
	$ward_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_bed_alloc[ward_id]'"));
	if($ward_info)
	{
		$ward_name=$ward_info["name"];
	}
}

$slno=1;
?>
<html>
<head>
	<title>Patient Discharge Summary</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
	<div class="container-fluid">
		<div class="">
			<div class="">
				<?php //include('page_header.php');?>
			</div>
		</div>
		<div class="row" >
			<div class="span2" >
				<img src="../../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png" style="width:80px;margin-top:0px;margin-bottom:-70px;" />
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
			<h4 style="margin: 0;">DISCHARGE SUMMARY</h4>
			<b><?php echo $dept_info["name"]; ?> DEPARTMENT</b>
			<?php
				if($ward_name)
				{
					echo "<br><small><b>Ward: $ward_name</b></small>";
				}
			?>
		</div>
		<div class="">
			<div class="">
				<table class="table table-condensed">
					<tr>
						<th>Name</th>
						<td><b>: </b><?php echo $pat_info["name"]; ?></td>
						
						<th>Age/Sex</th>
						<td><b>: </b><?php echo $age."/".$pat_info["sex"]; ?></td>
						
						<th>Admission Date</th>
						<td><b>: </b><?php echo date("d-m-Y",strtotime($pat_reg["date"])); ?></td>
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
						
						<th>Discharge Date</th>
						<td><b>: </b><?php echo $discharge_date; ?></td>
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
				<tr>
					<td colspan="2">
						<table class="table table-condensed">
							<tr>
								<th style="width: 120px;">Discharge Type <b style="float:right;">:</b></th>
								<td style="width: 250px;"><?php echo $discharge_type_info["discharge_name"]; ?></td>
								<th style="width: 90px;">Doctor Name <b style="float:right;">:</b></th>
								<td><?php echo $doc_info["Name"]; ?></td>
							</tr>
						</table>
					</td>
				</tr>
		<?php
			if($patient_discharge_summary["admission_reason"])
			{
		?>
				<tr>
					<td colspan="2">
						<b>Reason for admission :</b><br>
						
						<div class="results">
							<?php echo nl2br($patient_discharge_summary["admission_reason"]); ?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
		<?php
			if($patient_discharge_summary["final_diagnosis"])
			{
		?>
				<tr>
					<td colspan="2">
						<b>Final Diagnosis :</b><br>
						
						<div class="results">
							<?php echo nl2br($patient_discharge_summary["final_diagnosis"]); ?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
		<?php
			if($patient_discharge_summary["case_history"])
			{
		?>
				<tr>
					<td colspan="2">
						<b>Case Summary :</b><br>
						<div class="results">
							<?php echo nl2br($patient_discharge_summary["case_history"]); ?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
			if($patient_discharge_summary_obs)
			{
		?>
				<tr>
					<td colspan="2">
						<b>Obstetric History :</b><br>
						<div class="results">
						<?php
							$booked=0;
							if($patient_discharge_summary_obs["booked"]==1)
							{
						?>
							<label for="booked_obs" style="display: inline;">
								Booked
							</label>
						<?php
								$booked++;
							}
						?>
						<?php
							if($patient_discharge_summary_obs["unbooked"]==1)
							{
								if($booked>0){ echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; }
						?>
							<label for="unbooked_obs" style="display: inline;">
								Unbooked
							</label>
						<?php
								$booked++;
							}
						?>
						<?php
							if($patient_discharge_summary_obs["booked_elsewhere"]==1)
							{
								if($booked>0){ echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; }
						?>
							<label for="booked_elsewhere_obs" style="display: inline;">
								Booked Elsewhere
							</label>
						<?php
							}
						?>
							 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php
							if($patient_discharge_summary_obs["gravida"])
							{
						?>
							<label for="gravida_obs" style="display: inline;">
								<b>G</b> <input type="text" name="gravida_obs" id="gravida_obs" value="<?php echo $patient_discharge_summary_obs["gravida"]?>" style="width:30px;border: 1px solid #000;border-radius: 0;" disabled>
							</label>
						<?php
							}
						?>
						<?php
							if($patient_discharge_summary_obs["para"])
							{
						?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="para_obs" style="display: inline;">
								<b>P</b> <input type="text" name="para_obs" id="para_obs" value="<?php echo $patient_discharge_summary_obs["para"]?>" style="width:30px;border: 1px solid #000;border-radius: 0;" disabled>
							</label>
						<?php
							}
						?>
						<?php
							if($patient_discharge_summary_obs["live"])
							{
						?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="live_obs" style="display: inline;">
								<b>L</b> <input type="text" name="live_obs" id="live_obs" value="<?php echo $patient_discharge_summary_obs["live"]?>" style="width:30px;border: 1px solid #000;border-radius: 0;" disabled>
							</label>
						<?php
							}
						?>
						<?php
							if($patient_discharge_summary_obs["abortion"])
							{
						?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="abortion_obs" style="display: inline;">
								<b>A</b> <input type="text" name="abortion_obs" id="abortion_obs" value="<?php echo $patient_discharge_summary_obs["abortion"]?>" style="width:30px;border: 1px solid #000;border-radius: 0;" disabled>
							</label>
						<?php
							}
						?>
						
						<?php
							if($patient_antenatal_detail)
							{
						?>
							<div>
								<div class="">
									<table class="table table-condensed table-no-top-border">
										<tr>
										<?php if($patient_antenatal_detail["last_menstrual_period"]){ ?>
											<th style="width: 38px;">LMP :</th>
											<td style="text-align:left;"><?php echo date("d/m/Y",strtotime($patient_antenatal_detail["last_menstrual_period"])); ?></td>
										<?php } ?>
										<?php if($patient_antenatal_detail["est_delivery_date"]){ ?>
											<th style="width: 38px;">EDD :</th>
											<td style="text-align:left;"><?php echo date("d/m/Y",strtotime($patient_antenatal_detail["est_delivery_date"])); ?></td>
										<?php } ?>
										<?php if($patient_antenatal_detail["gestational_age"]){ ?>
											<th style="width: 30px;">GA :</th>
											<td style="text-align:left;"><?php echo $patient_antenatal_detail["gestational_age"]; ?></td>
										<?php } ?>
										<?php if($patient_antenatal_detail["gestational_age_usg"]){ ?>
											<th style="width: 60px;">BY USG :</th>
											<td style="text-align:left;"><?php echo $patient_antenatal_detail["gestational_age_usg"]; ?></td>
										<?php } ?>
										<?php if($patient_antenatal_detail["fundal_height"]){ ?>
											<th>Fundal Height</th>
											<td style="text-align:left;"><?php echo $patient_antenatal_detail["fundal_height"]; ?></td>
										<?php } ?>
										<?php if($patient_antenatal_detail["presentation"]){ ?>
											<th>Presentation</th>
											<td style="text-align:left;"><?php echo $patient_antenatal_detail["presentation"]; ?></td>
										<?php } ?>
										<?php if($patient_antenatal_detail["fetal_heart_rate"]){ ?>
											<th>FHR</th>
											<td style="text-align:left;"><?php echo $patient_antenatal_detail["fetal_heart_rate"]; ?></td>
										<?php } ?>
										</tr>
									</table>
								</div>
							</div>
						<?php
							}
						?>
						
						<?php
							if($patient_discharge_summary_obs["risk_factor"])
							{
						?>
							<label for="risk_factor">
								Risk Factor : <?php echo $patient_discharge_summary_obs["risk_factor"]?>
							</label>
						<?php
							}
						?>
						<?php
							if($patient_discharge_summary_obs["antenatal_complications"])
							{
						?>
							<label for="risk_factor">
								Antenatal Complications : <?php echo $patient_discharge_summary_obs["antenatal_complications"]?>
							</label>
						<?php
							}
						?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
		<?php
			if($patient_discharge_summary["examination"])
			{
		?>
				<tr>
					<td colspan="2">
						<b>Examination :</b><br>
						<div class="results">
							<?php echo nl2br($patient_discharge_summary["examination"]); ?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
		<?php
			if($patient_discharge_summary["procedure_performed"])
			{
		?>
				<tr>
					<td colspan="2">
						<b>Procedure Performed :</b><br>
						<div class="results">
							<?php echo nl2br($patient_discharge_summary["procedure_performed"]); ?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
			if($patient_discharge_summary_baby_num>0)
			{
		?>
				<tr>
					<td colspan="2">
					<?php if($patient_discharge_summary["procedure_performed"]==""){ ?>
						<b>Procedure Performed :</b><br>
					<?php } ?>
						<div class="results">
						<?php
							$baby=1;
							while($patient_discharge_summary_baby=mysqli_fetch_array($patient_discharge_summary_baby_qry))
							{
								$patient_delivery=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd_id'  AND `baby_uhid`='$patient_discharge_summary_baby[baby_uhid]' AND `baby_ipd_id`='$patient_discharge_summary_baby[baby_ipd_id]'"));
						?>
								<u>Baby Details <?php if($patient_discharge_summary_baby_num>1){ echo " of ".$baby; } ?> :</u><br>
							
							<?php
								$birth=0;
								if($patient_discharge_summary_baby["live_birth"]==1)
								{
							?>
								<label for="gravida_obs" style="display: inline;">
									Live Birth
								</label>
							<?php
									$birth++;
								}
							?>
							<?php
								if($patient_discharge_summary_baby["fresh_birth"]==1)
								{
									if($birth>0){ echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; }
							?>
								<label for="fresh_birth<?php echo $baby; ?>" style="display: inline;">
									Fresh Still Birth
								</label>
							<?php
									$birth++;
								}
							?>
							<?php
								if($patient_discharge_summary_baby["macerated_birth"]==1)
								{
									if($birth>0){ echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; }
							?>
								<label for="macerated_birth<?php echo $baby; ?>" style="display: inline;">
									Macerated Still Birth
								</label>
							<?php
								}
							?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<?php
								$term=0;
								if($patient_discharge_summary_baby["term"]==1)
								{
							?>
								<label for="term<?php echo $baby; ?>" style="display: inline;">
									Term
								</label>
							<?php
									$term++;
								}
							?>
							<?php
								if($patient_discharge_summary_baby["preterm"]==1)
								{
									if($term>0){ echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; }
							?>
								<label for="preterm<?php echo $baby; ?>" style="display: inline;">
									Preterm
								</label>
							<?php
									$term++;
								}
							?>
							<?php
								if($patient_discharge_summary_baby["iugr"]==1)
								{
									if($term>0){ echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; }
							?>
								<label for="iugr<?php echo $baby; ?>" style="display: inline;">
									IUGR
								</label>
							<?php
								}
							?>
								<br>
								<label for="tob<?php echo $baby; ?>" style="display: inline;">
									Time of Birth : <?php echo date("d-m-Y",strtotime($patient_delivery["dob"])); ?> <?php echo date("h:i A",strtotime($patient_delivery["born_time"])); ?>
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="sex<?php echo $baby; ?>" style="display: inline;">
									Sex : <?php echo $patient_delivery["sex"]; ?>
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="weight<?php echo $baby; ?>" style="display: inline;">
									Weight : <?php echo $patient_delivery["weight"]; ?> KG
								</label>
								
								<br>
						<?php
								$baby++;
							}
						?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
		<?php
			if($patient_discharge_summary["course_hospital"])
			{
		?>
				<tr>
					<td colspan="2">
						<b>Course in Hospital :</b><br>
						<div class="results">
							<?php echo nl2br($patient_discharge_summary["course_hospital"]); ?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
		<?php
			$patient_vitalsXXX=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd_id' "));
			if($patient_vitals)
			{
		?>
				<tr>
					<th colspan="2">Vitals signs at the time of discharge</th>
				</tr>
				<tr>
					<td colspan="2">
						<table class="table table-condensed">
							<tr>
								<th>Weight</th>
								<th>Height</th>
								<th>BMI</th>
								<th>Temperature</th>
								<th>Pulse</th>
								<th>SPO<sub>2</sub></th>
								<th>Blood Pressure</th>
								<th>Respiration Rate</th>
								<th>FBS</th>
								<th>RBS</th>
							</tr>
							<tr>
								<td><?php echo $patient_vitals["weight"]; ?>KG</td>
								<td><?php echo $patient_vitals["height"]; ?>CM</td>
								<td style="<?php echo $bmi_color; ?>"><?php echo $patient_vitals["BMI_1"].".".$patient_vitals["BMI_2"]; ?></td>
								<td><?php if($patient_vitals["temp"]){ echo $patient_vitals["temp"]; ?><sup>0</sup>C<?php } ?></td>
								<td><?php echo $patient_vitals["pulse"]; ?></td>
								<td><?php if($patient_vitals["spo2"]){ echo $patient_vitals["spo2"]; ?>%<?php } ?></td>
								<td><?php echo $patient_vitals["systolic"]."/".$patient_vitals["diastolic"]; ?></td>
								<td><?php echo $patient_vitals["RR"]; ?></td>
								<td><?php echo $patient_vitals["fbs"]; ?></td>
								<td><?php echo $patient_vitals["rbs"]; ?></td>
							</tr>
						</table>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
		<?php
			$medi_qry=mysqli_query($link, "SELECT * FROM `opd_clinic_medication` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd_id'");
			$medi_num=mysqli_num_rows($medi_qry);
			if($medi_num>0)
			{
		?>
				<tr>
					<th colspan="2">Discharge Medications</th>
				</tr>
				<tr>
					<td colspan="2">
						<table class="table table-condensed medicine results" style="width: 100%;">
					<?php
						$n=1;
						while($medi_data=mysqli_fetch_array($medi_qry))
						{
							if($medi_data["item_id"]==0)
							{
								$item_info["item_name"]=$medi_data["item_name"];
							}
							else
							{
								$item_info=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$medi_data[item_id]'"));
							}
					?>		<tr>
								<td style="width: 30%;"><?php echo $n; ?>. <?php echo $item_info["item_name"]; ?></td>
								<td><?php if($medi_data["dosage"]){ echo $medi_data["dosage"]." ".$medi_data["frequency"]." x ".$medi_data["duration"]; } ?></td>
							</tr>
					
					<?php
							$n++;
						}
					?>
						</table>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
		<?php
			if($patient_discharge_summary["discharge_instruction"])
			{
		?>
				<tr>
					<td colspan="2">
						<b>Discharge Instructions :</b><br>
						<div class="results">
							<?php echo nl2br($patient_discharge_summary["discharge_instruction"]); ?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
		<?php
			if($patient_discharge_summary["revisit_id"])
			{
				//$revisit_info=mysqli_fetch_array(mysqli_query($link, "SELECT `revisit_val`,`revisit_name` FROM `revisit_master` WHERE `revisit_id`='$patient_discharge_summary[revisit_id]'"));
		?>
				<tr>
					<td colspan="2">
						<b>Review :</b><br>
						<div class="results">
							<?php //echo $revisit_info["revisit_name"]; ?>
							<?php echo $patient_discharge_summary["revisit_id"]; ?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
			</table>
		</div>
	</div>
	<!--<div class="page_break"></div>-->
	<div class="container-fluid">
		<!--<div class="">
			<div class="">
				<?php //include('page_header.php');?>
			</div>
		</div>
		<div class="row" >
			<div class="span2" >
				<img src="../../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png" style="width:80px;margin-top:0px;margin-bottom:-70px;" />
			</div>
			<div class="span10 text-center" style="margin-left:0px;">
				<span style="font-size:12px;"><?php echo $page_head_line;; ?></span>
				<h4>
					<?php echo $company_info["name"]; ?><br>
					<small>
						(REGISTERED UNDER REGISTRATION OF SOCIETIES ACT, XXI OF 1860)
						<br>
						DURTLANG, AIZAWL - 796015, MIZORAM
					</small>
				</h4>
			</div>
		</div>
		<div style="text-align:center;">
			<h4 style="margin: 0;">CASE SUMMARY</h4>
			<b><?php echo $dept_info["name"]; ?> DEPARTMENT</b>
		</div>
		<div class="">
			<div class="">
				<table class="table table-condensed">
					<tr>
						<th>Name</th>
						<td><b>: </b><?php echo $pat_info["name"]; ?></td>
						
						<th>Age/Sex</th>
						<td><b>: </b><?php echo $age."/".$pat_info["sex"]; ?></td>
						
						<th>Admission Date</th>
						<td><b>: </b><?php echo date("d-m-Y",strtotime($pat_reg["date"])); ?></td>
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
						
						<th>Discharge Date</th>
						<td><b>: </b><?php echo $discharge_date; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<hr style="margin: 0;border: 1px solid #000;">-->
		<br>
		<br>
		<div>
			<table class="table table-condensed table-no-top-border">
		<?php
			if($patient_discharge_summary["hospital_report"])
			{
		?>
				<tr>
					<th colspan="2">Report to Hospital in Case of:</th>
				</tr>
				<tr>
					<td colspan="2">
						<div class="results">
							<?php echo nl2br($patient_discharge_summary["hospital_report"]); ?>
						</div>
					</td>
				</tr>
		<?php
				$slno++;
			}
		?>
		<?php
			if($patient_discharge_summary["emergency_contact"])
			{
		?>
				<tr>
					<th style="width: 160px;">Emergency Contact No. <b style="float:right;">:</b></th>
					<td><?php echo $patient_discharge_summary["emergency_contact"]; ?></td>
				</tr>
		<?php
				$slno++;
			}
		?>
			</table>
			<br>
			<br>
			<p>I have received the above instruction &amp; was given the opportunity to ask questions.</p>
			<br>
			<br>
			<br>
			<table class="table table-condensed table-no-top-border">
				<tr>
					<td>Treating Doctor Name &amp; Sign</td>
					<td style="text-align:center;">Discharge handed over by</td>
					<td style="text-align:right;">Name & Sign of patient relative</td>
				</tr>
			</table>
		</div>
	</div>
	
<?php
	$max_page_date_num=12;
	
	$date_num=0;
	$date_str="";
	
	$n=0;
	$dist_date_qry=mysqli_query($link, "SELECT DISTINCT `date` FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `testid` NOT IN(SELECT `testid` FROM `testmaster` WHERE `testname` LIKE '%culture%') ORDER BY `date` ASC");
	$dist_date_num=mysqli_num_rows($dist_date_qry);
	
	if($dist_date_num>0)
	{
		while($dist_date=mysqli_fetch_array($dist_date_qry))
		{
			$date=$dist_date["date"];
			
			if($date_num==0)
			{
				$date_str.="@$@".$date;
			}
			
			$n++;
			$date_num++;
			if($date_num==$max_page_date_num)
			{
				$date_str.="#$#".$date;
				
				$date_num=0;
			}
			
			if($n==$dist_date_num && $date_num>0)
			{
				$date_str.="#$#".$date;
			}
		}
		//echo $date_str;
		
		$page=1;
		$date_strs=explode("@$@", $date_str);
		foreach($date_strs AS $date_strz)
		{
			if($date_strz)
			{
				$dates=explode("#$#", $date_strz);
				$date1=$dates[0];
				$date2=$dates[1];
				
				if($date1 && $date2)
				{
					if($page>=1)
					{
						echo '<div class="page_break"></div>';
					}
		?>
					<div class="container-fluid">
					<?php
						if($page>=1)
						{
					?>
						<div class="">
							<div class="">
								<?php //include('page_header.php');?>
							</div>
						</div>
						<div class="row" >
							<div class="span2" >
								<img src="../../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png" style="width:80px;margin-top:0px;margin-bottom:-70px;" />
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
							<h4 style="margin: 0;">DISCHARGE SUMMARY</h4>
							<b><?php echo $dept_info["name"]; ?> DEPARTMENT</b>
							<?php
								if($ward_name)
								{
									echo "<br><small><b>Ward: $ward_name</b></small>";
								}
							?>
						</div>
						<div class="">
							<div class="">
								<table class="table table-condensed">
									<tr>
										<th>Name</th>
										<td><b>: </b><?php echo $pat_info["name"]; ?></td>
										
										<th>Age/Sex</th>
										<td><b>: </b><?php echo $age."/".$pat_info["sex"]; ?></td>
										
										<th>Admission Date</th>
										<td><b>: </b><?php echo date("d-m-Y",strtotime($pat_reg["date"])); ?></td>
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
										
										<th>Discharge Date</th>
										<td><b>: </b><?php echo $discharge_date; ?></td>
									</tr>
								</table>
							</div>
						</div>
						<hr style="margin: 0;border: 1px solid #000;">
					<?php
						}
						else
						{
					?>
							<br>
							<br>
							<br>
					<?php
						}
					?>
						<table class="table table-condensed table-result">
							<thead class="table_header_fix">
								<tr>
									<th> &nbsp; Test</th>
							<?php
								$dates_qry=mysqli_query($link, "SELECT DISTINCT `date` FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `date` BETWEEN '$date1' AND '$date2' ORDER BY `date`,`slno` ASC");
								while($dates=mysqli_fetch_array($dates_qry))
								{
									$date=$dates["date"];
							?>
									<th style="text-align:center;"><?php echo date("d-m-y",strtotime($date)); ?></th>
							<?php
								}
							?>
								</tr>
							</thead>
							<tbody>
						<?php
							$dist_test_str="SELECT DISTINCT `paramid` FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `date` BETWEEN '$date1' AND '$date2' AND `testid` NOT IN(SELECT `testid` FROM `testmaster` WHERE `testname` LIKE '%culture%') ORDER BY `date`,`slno` ASC";
							$dist_test_qry=mysqli_query($link, $dist_test_str);
							while($dist_test=mysqli_fetch_array($dist_test_qry))
							{
								//$testid=$dist_test["testid"];
								$paramid=$dist_test["paramid"];
								
								$param_info=mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `Parameter_old` WHERE `ID`='$dist_test[paramid]'"));
						?>
								<tr>
									<td> &nbsp; <?php echo $param_info["Name"]; ?></td>
							<?php
								$dates_qry=mysqli_query($link, "SELECT DISTINCT `date` FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `date` BETWEEN '$date1' AND '$date2' ORDER BY `date`,`slno` ASC");
								while($dates=mysqli_fetch_array($dates_qry))
								{
									$date=$dates["date"];
									
									$i=0;
									$test_result_val="";
									$test_result_str="SELECT `result` FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `paramid`='$paramid' AND `date`='$date' ORDER BY `slno` ASC";
									$test_result_qry=mysqli_query($link, $test_result_str);
									while($test_result=mysqli_fetch_array($test_result_qry))
									{
										if($i>0)
										{
											$test_result_val.="<br>";
										}
										$test_result_val.=$test_result["result"];
										$i++;
									}
							?>
									<td style="text-align:center;"><?php echo $test_result_val; ?></td>
							<?php
								}
							?>
								</tr>
						<?php
							}
						?>
							</tbody>
						</table>
					</div>
		<?php
					$page++;
				}
			}
		}
?>
		
<?php
	}
?>
</body>
</html>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
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
<style>
.txt_small{
	font-size:10px;
}
.table
{
	font-size: 13px;
}
@media print
{
	.noprint{
		display:none;
	 }
}
.results
{
	margin-left: 30px;
}
.table
{
	margin-bottom: 0px;
}
.table-condensed th, .table-condensed td
{
	//padding: 0;
	padding: 0 10px 0 0;
}
.medicine th, .medicine td
{
	padding: 0;
}
.table-result th, .table-result td
{
	padding: 0px !important;
	font-size: 10px !important;
	border-left: 1px solid #000;
	border-right: 1px solid #000;
	border-top: 1px solid #000;
	border-bottom: 1px solid #000;
	
	line-height: 10px !important;
}
.table-result caption + thead tr:first-child th, .table-result caption + thead tr:first-child td, .table-result colgroup + thead tr:first-child th, .table-result colgroup + thead tr:first-child td, .table-result thead:first-child tr:first-child th, .table-result thead:first-child tr:first-child td
{
	border-top: 1px solid #000;
}

@page{
	margin: 0.2cm;
	//margin-left: 2cm;
}
</style>
