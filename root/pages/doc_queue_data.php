<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");

$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$_SESSION["levelid"]=$emp_info["levelid"];
$branch_id=$emp_info["branch_id"];

$date=date("Y-m-d");

if($_POST["type"]=="opd_patient_list")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$pat_name=mysqli_real_escape_string($link, $_POST["pat_name"]);
	$appointment_date=mysqli_real_escape_string($link, $_POST["appointment_date"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$dept_id=mysqli_real_escape_string($link, $_POST["dept_id"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$str="SELECT a.* FROM `appointment_book` a JOIN `uhid_and_opdid` b ON a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` WHERE b.`branch_id`='$branch_id'";
	
	if($consultantdoctorid)
	{
		$main_doc_id=$consultantdoctorid;
		
		$doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `main_doc_id`='$consultantdoctorid' ");
		while($doc_info=mysqli_fetch_array($doc_qry))
		{
			$main_doc_id.=",".$doc_info["consultantdoctorid"];
		}
		
		$str.=" AND a.`consultantdoctorid` IN($main_doc_id)";
	}
	else
	{
		if($_SESSION["levelid"]==5)
		{
			$str.=" AND a.`dept_id` IN($dept_id)";
		}
	}
	
	$zz=0;
	if($uhid)
	{
		$str.=" AND b.`patient_id`='$uhid'";
		$zz++;
	}
	
	if($opd_id)
	{
		$str.=" AND b.`opd_id`='$opd_id'";
		$zz++;
	}
	
	if(strlen($pat_name)>2)
	{
		$str.=" AND b.`patient_id` IN(SELECT `patient_id` FROM `patient_info` WHERE `name` LIKE '%$pat_name%')";
		$zz++;
	}
	
	if($appointment_date && $zz==0)
	{
		$str.=" AND a.`appointment_date`='$appointment_date'";
		
		$str.=" ORDER BY a.`appointment_no` ASC";
	}
	else
	{
		$str.=" ORDER BY a.`date` DESC";
	}
	
	//echo $str;
	
	$qry=mysqli_query($link, $str);
	
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='1' "));
?>
	<table class="table table-condensed table-bordered" style="background-color: white;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Unit No.</th>
				<th><?php echo $prefix_det["prefix"]; ?></th>
				<th>Patient Name</th>
				<th>Age</th>
				<th>Sex</th>
				<th>Phone</th>
				<th>Appoint Date</th>
		<?php
			if($_SESSION["levelid"]==40)
			{
		?>
				<th>Doctor</th>
		<?php
			}
		?>
				<th>
			<?php
				if($_SESSION["levelid"]==40)
				{
			?>
					<span style="width:20px;border: 1px solid green;padding: 2px 2px;background-color: green;color: white;">Vital</span>
			<?php
				}
			?>
					<span style="width:20px;border: 1px solid green;padding: 2px 8px;background-color: green;color: white;">OB</span>
					<span style="width:20px;border: 1px solid darkorchid;padding: 2px 8px;background-color: darkorchid;color: white;">Dx</span>
					<span style="width:20px;border: 1px solid indigo;padding: 2px 4px;background-color: indigo;color: white;">EYE</span>
					<span style="width:20px;border: 1px solid deeppink;padding: 2px 2px;background-color: deeppink;color: white;">ANC</span>
					<span style="width:20px;border: 1px solid blue;padding: 2px 14px;background-color: blue;color: white;">Ix</span>
					<span style="width:20px;border: 1px solid red;padding: 2px 8px;background-color: red;color: white;">Rx</span>
					<span style="width:20px;border: 1px solid darkcyan;padding: 2px 2px;background-color: darkcyan;color: white;">ADV</span>
					<span style="width:20px;border: 1px solid brown;padding: 2px 8px;background-color: brown;color: white;">RA</span>
					<span style="width:20px;border: 1px solid slategray;padding: 2px 4px;background-color: slategray;color: white;">PAC</span>
				</th>
			</tr>
		</thead>
		<tbody>
<?php
		$n=1;
		while($data=mysqli_fetch_array($qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$data[patient_id]' "));
			
			$reg_date=$pat_reg["date"];
			
			if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			
			$patient_vitals_str='<img src="../images/Delete.png" style="width: 20px;">';
			$patient_vitals=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$data[opd_id]' LIMIT 1 "));
			if($patient_vitals)
			{
				$patient_vitals_str='<img src="../images/right.png" style="width: 20px;">';
			}
			$patient_case_history_str='<img src="../images/Delete.png" style="width: 20px;">';
			$patient_case_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_case_history` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$data[opd_id]' "));
			if($patient_case_history)
			{
				$patient_case_history_str='<img src="../images/right.png" style="width: 20px;">';
			}
			
			$pat_diagnosis_str='<img src="../images/Delete.png" style="width: 20px;">';
			$pat_diagnosis=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$data[opd_id]' "));
			if($pat_diagnosis)
			{
				$pat_diagnosis_str='<img src="../images/right.png" style="width: 20px;">';
			}
			
			$pat_eye_str='<img src="../images/Delete.png" style="width: 20px;">';
			$patient_eye_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_history` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$data[opd_id]' "));
			if($patient_eye_history)
			{
				$pat_eye_str='<img src="../images/right.png" style="width: 20px;">';
			}
			
			$patient_antenatal_detail_str='<img src="../images/Delete.png" style="width: 20px;">';
			$patient_antenatal_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_antenatal_detail` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$data[opd_id]' "));
			if($patient_antenatal_detail)
			{
				$patient_antenatal_detail_str='<img src="../images/right.png" style="width: 20px;">';
			}
			
			$opd_clinic_investigation_str='<img src="../images/Delete.png" style="width: 20px;">';
			$opd_clinic_investigation=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_investigation` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$data[opd_id]' LIMIT 1"));
			if($opd_clinic_investigation)
			{
				$opd_clinic_investigation_str='<img src="../images/red_right.png" style="width: 25px;">';
				
				$test_num=mysqli_num_rows(mysqli_query($link, "SELECT a.`testid` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`category_id`='1' AND `patient_id`='$data[patient_id]' AND `opd_id`='$opd_clinic_investigation[lab_id]'"));
				
				$testresult_num=mysqli_num_rows(mysqli_query($link, "SELECT DISTINCT `testid` FROM `testresults` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$opd_clinic_investigation[lab_id]'"));
				$test_summary_num=mysqli_num_rows(mysqli_query($link, "SELECT DISTINCT `testid` FROM `patient_test_summary` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$opd_clinic_investigation[lab_id]'"));
				$widalresult_num=mysqli_num_rows(mysqli_query($link, "SELECT DISTINCT `testid` FROM `widalresult` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$opd_clinic_investigation[lab_id]'"));
				
				$total_result_num=$testresult_num+$test_summary_num+$widalresult_num;
				
				if($total_result_num>=$test_num)
				{
					$opd_clinic_investigation_str='<img src="../images/right.png" style="width: 20px;">';
				}
				else
				{
					$opd_clinic_investigation_str='<img src="../images/yellow.png" style="width: 20px;">';
				}
			}
			
			$opd_clinic_medication_str='<img src="../images/Delete.png" style="width: 20px;">';
			$opd_clinic_medication=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_medication` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$data[opd_id]' LIMIT 1"));
			if($opd_clinic_medication)
			{
				$opd_clinic_medication_str='<img src="../images/right.png" style="width: 20px;">';
			}
			
			$opd_clinic_advice_str='<img src="../images/Delete.png" style="width: 20px;">';
			
			$opd_clinic_revisit_advice_str='<img src="../images/Delete.png" style="width: 20px;">';
			$opd_clinic_revisit_advice=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_revisit_advice` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$data[opd_id]' LIMIT 1"));
			if($opd_clinic_revisit_advice)
			{
				$opd_clinic_revisit_advice_str='<img src="../images/right.png" style="width: 20px;">';
			}
			
			$opd_clinic_pac_str='<img src="../images/Delete.png" style="width: 20px;">';
			$patient_pac_details=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details` WHERE `patient_id`='$data[patient_id]' AND `opd_id`='$data[opd_id]' LIMIT 1"));
			if($patient_pac_details)
			{
				$opd_clinic_pac_str='<img src="../images/right.png" style="width: 20px;">';
			}
?>
			<tr onclick="load_clinical_data('<?php echo $data["patient_id"]; ?>','<?php echo $data["opd_id"]; ?>')" style="cursor:pointer;">
				<td><?php echo $n; ?></td>
				<td><?php echo $data["patient_id"]; ?></td>
				<td><?php echo $data["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $age; ?></td>
				<td><?php echo $pat_info["sex"]; ?></td>
				<td><?php echo $pat_info["phone"]; ?></td>
				<td><?php echo date("d-M-Y",strtotime($data["appointment_date"])); ?></td>
		<?php
			if($_SESSION["levelid"]==40)
			{
				$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$data[consultantdoctorid]'"));
		?>
				<td><?php echo $doc_info["Name"]; ?></td>
		<?php
			}
		?>
				<td>
			<?php
				if($_SESSION["levelid"]==40)
				{
			?>
					<span style="width:30px;border: 1px solid white;padding: 2px 8px;">
						<?php echo $patient_vitals_str; ?>
					</span>
			<?php
				}
			?>
					<span style="width:20px;border: 1px solid white;padding: 2px 8px;">
						<?php echo $patient_case_history_str; ?>
					</span>
					<span style="width:20px;border: 1px solid white;padding: 2px 8px;">
						<?php echo $pat_diagnosis_str; ?>
					</span>
					<span style="width:20px;border: 1px solid white;padding: 2px 8px;">
						<?php echo $pat_eye_str; ?>
					</span>
					<span style="width:20px;border: 1px solid white;padding: 2px 8px;">
						<?php echo $patient_antenatal_detail_str; ?>
					</span>
					<span style="width:20px;border: 1px solid white;padding: 2px 8px;">
						<?php echo $opd_clinic_investigation_str; ?>
					</span>
					<span style="width:20px;border: 1px solid white;padding: 2px 8px;">
						<?php echo $opd_clinic_medication_str; ?>
					</span>
					<span style="width:20px;border: 1px solid white;padding: 2px 8px;">
						<?php echo $opd_clinic_advice_str; ?>
					</span>
					<span style="width:20px;border: 1px solid white;padding: 2px 8px;">
						<?php echo $opd_clinic_revisit_advice_str; ?>
					</span>
					<span style="width:20px;border: 1px solid white;padding: 2px 8px;">
						<?php echo $opd_clinic_pac_str; ?>
					</span>
				</td>
			</tr>
<?php
			$n++;
		}
?>
		</tbody>
	</table>
<?php
}

if($_POST["type"]=="load_clinical_data")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name`,`sex`,`dob`,`phone` FROM `patient_info` WHERE `patient_id`='$uhid'"));
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$reg_date=$pat_reg["date"];
	
	if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
	
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
	
?>
	<div class="">
		<table class="table table-condensed table-report">
			<tr>
				<th>Unit No.</th>
				<th><?php echo $prefix_det["prefix"]; ?></th>
				<th>Patient Name</th>
				<th>Age</th>
				<th>Sex</th>
				<th>Phone</th>
			</tr>
			<tr>
				<td><?php echo $uhid; ?></td>
				<td><?php echo $opd_id; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $age; ?></td>
				<td><?php echo $pat_info["sex"]; ?></td>
				<td><?php echo $pat_info["phone"]; ?></td>
			</tr>
		</table>
		<div class="widget-box">
			<div class="widget-title">
				<ul class="nav nav-tabs">
			<?php
				if($_SESSION["levelid"]==5)
				{
			?>
					<li class="active"><a data-toggle="tab" href="#tab1" id="opd_clinical_btn1" onclick="opd_clinical_form(1)">Observation</a></li>
					<!--<li class=""><a data-toggle="tab" href="#tab2" id="opd_clinical_btn2" onclick="opd_clinical_form(2)">Diagnosis</a></li>-->
					<li class=""><a data-toggle="tab" href="#tab2" id="opd_clinical_btn2" onclick="opd_clinical_form(2)">Ophthalmology</a></li>
					<li class=""><a data-toggle="tab" href="#tab3" id="opd_clinical_btn3" onclick="opd_clinical_form(3)">Antenatal</a></li>
					<li class=""><a data-toggle="tab" href="#tab4" id="opd_clinical_btn4" onclick="opd_clinical_form(4)">Investigation</a></li>
					<li class=""><a data-toggle="tab" href="#tab5" id="opd_clinical_btn5" onclick="opd_clinical_form(5)">Medication</a></li>
					<li class=""><a data-toggle="tab" href="#tab6" id="opd_clinical_btn6" onclick="opd_clinical_form(6)">Advice</a></li>
					<li class=""><a data-toggle="tab" href="#tab7" id="opd_clinical_btn7" onclick="opd_clinical_form(7)">Re-visit Advice</a></li>
					<li class=""><a data-toggle="tab" href="#tab8" id="opd_clinical_btn8" onclick="opd_clinical_form(8)">Previous Record</a></li>
					<li class=""><a data-toggle="tab" href="#tab9" id="opd_clinical_btn9" onclick="opd_clinical_form(9)">PAC</a></li>
			<?php
				}
				else
				{
			?>
					<li class="active"><a data-toggle="tab" href="#tab20" id="opd_clinical_btn20" onclick="opd_clinical_form(20)">Vitals</a></li>
			<?php
				}
			?>
				</ul>
			</div>
			<div class="" style="height: 530px;">
				<div id="linear_loading_div">
					<br>
					<br>
					<?php include("../../linear_loading_text.php"); ?>
				</div>
				<div id="tab1" class="tab-pane active">
					
				</div>
				<div id="tab2" class="tab-pane">
					
				</div>
				<div id="tab3" class="tab-pane">
					
				</div>
				<div id="tab4" class="tab-pane">
					
				</div>
				<div id="tab5" class="tab-pane">
					
				</div>
				<div id="tab6" class="tab-pane">
					
				</div>
				<div id="tab7" class="tab-pane">
					
				</div>
				<div id="tab8" class="tab-pane">
					
				</div>
				<div id="tab9" class="tab-pane">
					
				</div>
				<div id="tab10" class="tab-pane">
					
				</div>
				
				<div id="tab20" class="tab-pane">
					
				</div>
			</div>
		</div>
	</div>
	<center>
<?php
	if($_SESSION["levelid"]==5)
	{
?>
		<button class="btn btn-new" id="previous_btn" onclick="next_tab(1)"><i class="icon-backward"></i> Previous</button>
		<button class="btn btn-delete" onclick="next_tab(0)"><i class="icon-refresh"></i> Refresh</button>
		<button class="btn btn-excel" id="next_btn"  onclick="next_tab(2)">Next <i class="icon-forward"></i></button>
		<br>
		<br>
<?php
	}
?>
		<button class="btn btn-print" onclick="print_prescription()"><i class="icon-print"></i> Prescription</button>
		<button class="btn btn-back"  onclick="opd_patient_list()"><i class="icon-circle-arrow-up"></i> Back To List</button>
		
		<input type="hidden" id="tab_id">
	</center>
	<input type="hidden" name="sel_uhid" id="sel_uhid" value="<?php echo $uhid; ?>" readonly>
	<input type="hidden" name="sel_opd_id" id="sel_opd_id" value="<?php echo $opd_id; ?>" readonly>
<?php
}


// Vitals Start
if($_POST["type"]=="opd_clinical_form20")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$patient_vitals=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	
?>
<br>
<div style="text-align:center;">
	<h4>Vitals</h4>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Weight</th>
			<td>
				<input type="text" class="span2" name="weight" id="weight" onKeyUp="weight_up(this.value,event)" placeholder="KG" value="<?php echo $patient_vitals["weight"]; ?>" />
			</td>
			<th>Height</th>
			<td>
				<input type="text" class="span2" name="height" id="height" onKeyUp="height_up(this.value,event)" placeholder="CM" value="<?php echo $patient_vitals["height"]; ?>" />
			</td>
			<th>BMI</th>
			<td>
				<input type="text" class="span1" name="BMI_1" id="BMI_1" value="<?php echo $patient_vitals["BMI_1"]; ?>" disabled>
				<input type="text" class="span1" name="BMI_2" id="BMI_2" value="<?php echo $patient_vitals["BMI_2"]; ?>" disabled>
			</td>
			<th>Temperature (<sup>o</sup>C)</th>
			<td>
				<input type="text" class="span2" name="temp" id="temp" onKeyUp="temp_up(this.value,event)" placeholder="°C" value="<?php echo $patient_vitals["temp"]; ?>" />
			</td>
		</tr>
		<tr>
			<th>Pulse</th>
			<td>
				<input type="text" class="span2" name="pulse" id="pulse" onKeyUp="pulse_up(this.value,event)" placeholder="BPM" value="<?php echo $patient_vitals["pulse"]; ?>" />
			</td>
			<th>SPO<sub>2</sub></th>
			<td>
				<input type="text" class="span2" name="spo2" id="spo2" onKeyUp="spo2_up(this.value,event)" placeholder="%" value="<?php echo $patient_vitals["spo2"]; ?>" />
			</td>
			<th>BP(Systolic)</th>
			<td>
				<input type="text" class="span2" name="systolic" id="systolic" onKeyUp="systolic_up(this.value,event)" placeholder="Systolic" value="<?php echo $patient_vitals["systolic"]; ?>" />
			</td>
			<th>BP(Diastolic)</th>
			<td>
				<input type="text" class="span2" name="diastolic" id="diastolic" onKeyUp="diastolic_up(this.value,event)" placeholder="Diastolic" value="<?php echo $patient_vitals["diastolic"]; ?>" />
			</td>
		</tr>
		<tr>
			<th>Respiration Rate(RR)</th>
			<td>
				<input type="text" class="span2" name="RR" id="RR" onKeyUp="RR_up(this.value,event)" placeholder="BPM" value="<?php echo $patient_vitals["RR"]; ?>" />
			</td>
			<th>FBS</th>
			<td>
				<input type="text" class="span2" name="fbs" id="fbs" onKeyUp="fbs_up(this.value,event)" placeholder="FBS" value="<?php echo $patient_vitals["fbs"]; ?>" />
			</td>
			<th>RBS</th>
			<td>
				<input type="text" class="span2" name="rbs" id="rbs" onKeyUp="rbs_up(this.value,event)" placeholder="RBS" value="<?php echo $patient_vitals["rbs"]; ?>" />
			</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th style="width: 140px;">Note</th>
			<td colspan="7">
				<input type="text" class="span8" name="note" id="note" onKeyUp="note_up(this.value,event)" style="width:98%;" placeholder="Note" value="<?php echo $patient_vitals["note"]; ?>" />
			</td>
		</tr>
		<tr>
			<td colspan="8" style="text-align:center;">
				<button class="btn btn-save" id="sav_observation" onclick="sav_vitals()"><i class="icon-save"></i> Save</button>
			</td>
		</tr>
	</table>
</div>
<?php
}
if($_POST["type"]=="sav_vitals")
{
	//print_r($_POST);
	
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	//Vitals
	$weight=mysqli_real_escape_string($link, $_POST["weight"]);
	$height=mysqli_real_escape_string($link, $_POST["height"]);
	$BMI_1=mysqli_real_escape_string($link, $_POST["BMI_1"]);
	$BMI_2=mysqli_real_escape_string($link, $_POST["BMI_2"]);
	$temp=mysqli_real_escape_string($link, $_POST["temp"]);
	$pulse=mysqli_real_escape_string($link, $_POST["pulse"]);
	$spo2=mysqli_real_escape_string($link, $_POST["spo2"]);
	$systolic=mysqli_real_escape_string($link, $_POST["systolic"]);
	$diastolic=mysqli_real_escape_string($link, $_POST["diastolic"]);
	$RR=mysqli_real_escape_string($link, $_POST["RR"]);
	$fbs=mysqli_real_escape_string($link, $_POST["fbs"]);
	$rbs=mysqli_real_escape_string($link, $_POST["rbs"]);
	$note=mysqli_real_escape_string($link, $_POST["note"]);
	
	$PR="";
	$medium_circumference="";
	$head_circumference="";
	
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	else
	{
		$appoint_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
		$consultantdoctorid=$appoint_info["consultantdoctorid"];
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$branch_id=$pat_reg["branch_id"];
	
	// Vitals
	$patient_vitals=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_vitals)
	{
		if(!$weight && !$height && !$spo2 && !$pulse && !$RR && !$temp && !$systolic && !$diastolic && !$fbs && !$rbs && !$note)
		{
			mysqli_query($link, "DELETE FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
			
			echo "Updated";
		}
		else
		{
			if(mysqli_query($link, "UPDATE `pat_vital` SET `weight`='$weight',`height`='$height',`medium_circumference`='$medium_circumference',`BMI_1`='$BMI_1',`BMI_2`='$BMI_2',`spo2`='$spo2',`pulse`='$pulse',`head_circumference`='$head_circumference',`PR`='$PR',`RR`='$RR',`temp`='$temp',`systolic`='$systolic',`diastolic`='$diastolic',`rbs`='$rbs',`note`='$note' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"))
			{
				echo "Updated";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
	else
	{
		if($weight || $height || $spo2 || $pulse || $RR || $temp || $systolic || $diastolic || $fbs || $rbs || $note)
		{
			if(mysqli_query($link, "INSERT INTO `pat_vital`(`patient_id`, `opd_id`, `consultantdoctorid`, `weight`, `height`, `medium_circumference`, `BMI_1`, `BMI_2`, `spo2`, `pulse`, `head_circumference`, `PR`, `RR`, `temp`, `systolic`, `diastolic`, `fbs`, `rbs`, `note`, `date`, `time`, `user`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$weight','$height','$medium_circumference','$BMI_1','$BMI_2','$spo2','$pulse','$head_circumference','$PR','$RR','$temp','$systolic','$diastolic','$fbs','$rbs','$note','$date','$time','$c_user')"))
			{
			echo "Saved";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
}
// Vitals End

// Observation Start
if($_POST["type"]=="opd_clinical_form1")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$patient_case_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_case_history` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_vitals=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	
	$patient_general_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_general_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if(!$patient_general_exam)
	{
		$patient_general_exam["pallor"]="Nil";
		$patient_general_exam["edema"]="Nil";
		$patient_general_exam["icterus"]="Nil";
		$patient_general_exam["dehydration"]="Nil";
		$patient_general_exam["cyanosis"]="Nil";
		$patient_general_exam["lymph_node"]="Not enlarged";
		$patient_general_exam["gcs"]="15/15";
	}
	
	$patient_systemic_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_systemic_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if(!$patient_systemic_exam)
	{
		$patient_systemic_exam["chest"]="Clear/Bilateral";
		$patient_systemic_exam["cvs"]="S1 S2 Normal";
		$patient_systemic_exam["cns"]="Conscious/Oriented";
		$patient_systemic_exam["pa"]="Soft/Non tender";
	}
	
	$patient_diagnosis=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
?>
<br>
<div style="text-align:center;max-height: 520px;overflow-y: scroll;">
	<h4>Observation</h4>
	<table class="table table-condensed table-bordered">
		<tr>
			<th style="width: 140px;">Case History <b style="color:#ff0000;">*</b></th>
			<td colspan="7">
				<!--<input type="text" class="span8" name="case_history" id="case_history" onKeyUp="case_history_up(this.value,event)" style="width:98%;" placeholder="Patient Case History" list="case_history_list" value="<?php echo $patient_case_history["case_history"]; ?>" />-->
				
				<textarea rows="10" id="case_history" placeholder="Patient Case History" style="width: 97%;resize: none;"><?php echo $patient_case_history["case_history"]; ?></textarea>
				<datalist id="case_history_list"></datalist>
			</td>
		</tr>
		<tr>
			<th colspan="8" style="text-align:center;background-color: #ddd;">General Examination</th>
		</tr>
		<tr>
			<th>Pallor</th>
			<td>
				<input type="text" class="span2" name="pallor" id="pallor" onKeyUp="pallor_up(this.value,event)" placeholder="Pallor" value="<?php echo $patient_general_exam["pallor"]; ?>" />
			</td>
			<th>Edema</th>
			<td>
				<input type="text" class="span2" name="edema" id="edema" onKeyUp="edema_up(this.value,event)" placeholder="Edema" value="<?php echo $patient_general_exam["edema"]; ?>" />
			</td>
			<th>Icterus</th>
			<td>
				<input type="text" class="span2" name="icterus" id="icterus" onKeyUp="icterus_up(this.value,event)" placeholder="Icterus" value="<?php echo $patient_general_exam["icterus"]; ?>" />
			</td>
			<th>Dehydration</th>
			<td>
				<input type="text" class="span2" name="dehydration" id="dehydration" onKeyUp="dehydration_up(this.value,event)" placeholder="Dehydration" value="<?php echo $patient_general_exam["dehydration"]; ?>" />
			</td>
		</tr>
		<tr>
			<th>Cyanosis</th>
			<td>
				<input type="text" class="span2" name="cyanosis" id="cyanosis" onKeyUp="cyanosis_up(this.value,event)" placeholder="Cyanosis" value="<?php echo $patient_general_exam["cyanosis"]; ?>" />
			</td>
			<th>Lymph Node</th>
			<td>
				<input type="text" class="span2" name="lymph_node" id="lymph_node" onKeyUp="lymph_node_up(this.value,event)" placeholder="Lymph Node" value="<?php echo $patient_general_exam["lymph_node"]; ?>" />
			</td>
			<th>GCS</th>
			<td>
				<input type="text" class="span2" name="gcs" id="gcs" onKeyUp="gcs_up(this.value,event)" placeholder="GCS" value="<?php echo $patient_general_exam["gcs"]; ?>" />
			</td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th colspan="8" style="text-align:center;background-color: #ddd;">Systemic Examination</th>
		</tr>
		<tr>
			<th>Chest</th>
			<td>
				<input type="text" class="span2" name="chest" id="chest" onKeyUp="chest_up(this.value,event)" placeholder="Chest" value="<?php echo $patient_systemic_exam["chest"]; ?>" list="chest_list" autocomplete="on">
				<datalist id="chest_list">
					<option>Clear</option>
					<option>Bilateral</option>
				</datalist>
			</td>
			<th>CVS</th>
			<td>
				<input type="text" class="span2" name="cvs" id="cvs" onKeyUp="cvs_up(this.value,event)" placeholder="CVS" value="<?php echo $patient_systemic_exam["cvs"]; ?>" list="cvs_list" autocomplete="on">
				<datalist id="cvs_list">
					<option>Normal</option>
					<option>S1</option>
					<option>S2</option>
				</datalist>
			</td>
			<th>CNS</th>
			<td>
				<input type="text" class="span2" name="cns" id="cns" onKeyUp="cns_up(this.value,event)" placeholder="CNS" value="<?php echo $patient_systemic_exam["cns"]; ?>" list="cns_list" autocomplete="on">
				<datalist id="cns_list">
					<option>Conscious</option>
					<option>Oriented</option>
				</datalist>
			</td>
			<th>P/A</th>
			<td>
				<input type="text" class="span2" name="pa" id="pa" onKeyUp="pa_up(this.value,event)" placeholder="P/A" value="<?php echo $patient_systemic_exam["pa"]; ?>" list="pa_list" autocomplete="on">
				<datalist id="pa_list">
					<option>Soft</option>
					<option>Non tender</option>
				</datalist>
			</td>
		</tr>
		<tr>
			<th colspan="8" style="text-align:center;background-color: #ddd;">Diagnosis</th>
		</tr>
		<tr>
			<th style="width: 140px;">Diagnosis</th>
			<td colspan="8">
				<input type="text" class="span8" name="diagnosis_observ" id="diagnosis_observ" onKeyUp="diagnosis_up(this.value,event)" style="width:98%;" placeholder="Diagnosis" list="diagnosis_list" value="<?php echo $patient_diagnosis["diagnosis"]; ?>" />
				<datalist id="diagnosis_list"></datalist>
			</td>
		</tr>
		<tr>
			<th colspan="8" style="text-align:center;background-color: #ddd;">Vitals</th>
		</tr>
		<tr>
			<th>Weight</th>
			<td>
				<input type="text" class="span2" name="weight" id="weight" onKeyUp="weight_up(this.value,event)" placeholder="KG" value="<?php echo $patient_vitals["weight"]; ?>" />
			</td>
			<th>Height</th>
			<td>
				<input type="text" class="span2" name="height" id="height" onKeyUp="height_up(this.value,event)" placeholder="CM" value="<?php echo $patient_vitals["height"]; ?>" />
			</td>
			<th>BMI</th>
			<td>
				<input type="text" class="span1" name="BMI_1" id="BMI_1" value="<?php echo $patient_vitals["BMI_1"]; ?>" disabled>
				<input type="text" class="span1" name="BMI_2" id="BMI_2" value="<?php echo $patient_vitals["BMI_2"]; ?>" disabled>
			</td>
			<th>Temperature (<sup>o</sup>C)</th>
			<td>
				<input type="text" class="span2" name="temp" id="temp" onKeyUp="temp_up(this.value,event)" placeholder="°C" value="<?php echo $patient_vitals["temp"]; ?>" />
			</td>
		</tr>
		<tr>
			<th>Pulse</th>
			<td>
				<input type="text" class="span2" name="pulse" id="pulse" onKeyUp="pulse_up(this.value,event)" placeholder="BPM" value="<?php echo $patient_vitals["pulse"]; ?>" />
			</td>
			<th>SPO<sub>2</sub></th>
			<td>
				<input type="text" class="span2" name="spo2" id="spo2" onKeyUp="spo2_up(this.value,event)" placeholder="%" value="<?php echo $patient_vitals["spo2"]; ?>" />
			</td>
			<th>BP(Systolic)</th>
			<td>
				<input type="text" class="span2" name="systolic" id="systolic" onKeyUp="systolic_up(this.value,event)" placeholder="Systolic" value="<?php echo $patient_vitals["systolic"]; ?>" />
			</td>
			<th>BP(Diastolic)</th>
			<td>
				<input type="text" class="span2" name="diastolic" id="diastolic" onKeyUp="diastolic_up(this.value,event)" placeholder="Diastolic" value="<?php echo $patient_vitals["diastolic"]; ?>" />
			</td>
		</tr>
		<tr>
			<th>Respiration Rate(RR)</th>
			<td>
				<input type="text" class="span2" name="RR" id="RR" onKeyUp="RR_up(this.value,event)" placeholder="BPM" value="<?php echo $patient_vitals["RR"]; ?>" />
			</td>
			<th>FBS</th>
			<td>
				<input type="text" class="span2" name="fbs" id="fbs" onKeyUp="fbs_up(this.value,event)" placeholder="FBS" value="<?php echo $patient_vitals["fbs"]; ?>" />
			</td>
			<th>RBS</th>
			<td>
				<input type="text" class="span2" name="rbs" id="rbs" onKeyUp="rbs_up(this.value,event)" placeholder="RBS" value="<?php echo $patient_vitals["rbs"]; ?>" />
			</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th style="width: 140px;">Note</th>
			<td colspan="7">
				<input type="text" class="span8" name="note" id="note" onKeyUp="note_up(this.value,event)" style="width:98%;" placeholder="Note" value="<?php echo $patient_vitals["note"]; ?>" />
			</td>
		</tr>
		<tr>
			<td colspan="8" style="text-align:center;">
				<button class="btn btn-save" id="sav_observation" onclick="sav_observation()"><i class="icon-save"></i> Save</button>
			</td>
		</tr>
	</table>
</div>
<?php
}
if($_POST["type"]=="sav_observation")
{
	//print_r($_POST);
	
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$case_history=mysqli_real_escape_string($link, $_POST["case_history"]);
	
	//General Examination
	$pallor=mysqli_real_escape_string($link, $_POST["pallor"]);
	$edema=mysqli_real_escape_string($link, $_POST["edema"]);
	$icterus=mysqli_real_escape_string($link, $_POST["icterus"]);
	$dehydration=mysqli_real_escape_string($link, $_POST["dehydration"]);
	$cyanosis=mysqli_real_escape_string($link, $_POST["cyanosis"]);
	$lymph_node=mysqli_real_escape_string($link, $_POST["lymph_node"]);
	$gcs=mysqli_real_escape_string($link, $_POST["gcs"]);
	
	//Systemic Examination
	$chest=mysqli_real_escape_string($link, $_POST["chest"]);
	$cvs=mysqli_real_escape_string($link, $_POST["cvs"]);
	$cns=mysqli_real_escape_string($link, $_POST["cns"]);
	$pa=mysqli_real_escape_string($link, $_POST["pa"]);
	
	$diagnosis=mysqli_real_escape_string($link, $_POST["diagnosis"]);
	
	//Vitals
	$weight=mysqli_real_escape_string($link, $_POST["weight"]);
	$height=mysqli_real_escape_string($link, $_POST["height"]);
	$BMI_1=mysqli_real_escape_string($link, $_POST["BMI_1"]);
	$BMI_2=mysqli_real_escape_string($link, $_POST["BMI_2"]);
	$temp=mysqli_real_escape_string($link, $_POST["temp"]);
	$pulse=mysqli_real_escape_string($link, $_POST["pulse"]);
	$spo2=mysqli_real_escape_string($link, $_POST["spo2"]);
	$systolic=mysqli_real_escape_string($link, $_POST["systolic"]);
	$diastolic=mysqli_real_escape_string($link, $_POST["diastolic"]);
	$RR=mysqli_real_escape_string($link, $_POST["RR"]);
	$fbs=mysqli_real_escape_string($link, $_POST["fbs"]);
	$rbs=mysqli_real_escape_string($link, $_POST["rbs"]);
	$note=mysqli_real_escape_string($link, $_POST["note"]);
	
	$PR="";
	$medium_circumference="";
	$head_circumference="";
	
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$branch_id=$pat_reg["branch_id"];
	
	//Case History
	$patient_case_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_case_history` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_case_history)
	{
		if(mysqli_query($link, " UPDATE `patient_case_history` SET `case_history`='$case_history' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "))
		{
			echo "Updated";
		}
		else
		{
			echo "Failed, try again later";
		}
	}
	else
	{
		if($case_history)
		{
			if(mysqli_query($link, " INSERT INTO `patient_case_history`(`patient_id`, `opd_id`, `consultantdoctorid`, `case_history`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$case_history','$c_user','$date','$time') "))
			{
				echo "Saved";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
	
	// General Examination
	$patient_general_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_general_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_general_exam)
	{
		mysqli_query($link, "UPDATE `patient_general_examination` SET `pallor`='$pallor',`edema`='$edema',`icterus`='$icterus',`dehydration`='$dehydration',`cyanosis`='$cyanosis',`lymph_node`='$lymph_node',`gcs`='$gcs' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		
		if(!$pallor && !$edema && !$icterus && !$dehydration && !$cyanosis && !$lymph_node && !$gcs)
		{
			mysqli_query($link, "DELETE FROM `patient_general_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		}
	}
	else
	{
		if($pallor || $edema || $icterus || $dehydration || $cyanosis || $lymph_node || $gcs)
		{
			mysqli_query($link, "INSERT INTO `patient_general_examination`(`patient_id`, `opd_id`, `consultantdoctorid`, `pallor`, `edema`, `icterus`, `dehydration`, `cyanosis`, `lymph_node`, `gcs`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$pallor','$edema','$icterus','$dehydration','$cyanosis','$lymph_node','$gcs','$c_user','$date','$time')");
		}
	}
	
	// Systemic Examination
	$patient_systemic_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_systemic_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_systemic_exam)
	{
		if(!$chest && !$cvs && !$cns && !$pa)
		{
			mysqli_query($link, "DELETE FROM `patient_systemic_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		}
		else
		{
			mysqli_query($link, "UPDATE `patient_systemic_examination` SET `chest`='$chest',`cvs`='$cvs',`cns`='$cns',`pa`='$pa' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		}
	}
	else
	{
		if($chest || $cvs || $cns || $pa)
		{
			mysqli_query($link, "INSERT INTO `patient_systemic_examination`(`patient_id`, `opd_id`, `consultantdoctorid`, `chest`, `cvs`, `cns`, `pa`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$chest','$cvs','$cns','$pa','$c_user','$date','$time')");
		}
	}
	
	// Vitals
	$patient_vitals=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_vitals)
	{
		if(!$weight && !$height && !$spo2 && !$pulse && !$RR && !$temp && !$systolic && !$diastolic && !$fbs && !$rbs && !$note)
		{
			mysqli_query($link, "DELETE FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		}
		else
		{
			mysqli_query($link, "UPDATE `pat_vital` SET `weight`='$weight',`height`='$height',`medium_circumference`='$medium_circumference',`BMI_1`='$BMI_1',`BMI_2`='$BMI_2',`spo2`='$spo2',`pulse`='$pulse',`head_circumference`='$head_circumference',`PR`='$PR',`RR`='$RR',`temp`='$temp',`systolic`='$systolic',`diastolic`='$diastolic',`rbs`='$rbs',`note`='$note' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		}
	}
	else
	{
		if($weight || $height || $spo2 || $pulse || $RR || $temp || $systolic || $diastolic || $fbs || $rbs || $note)
		{
			mysqli_query($link, "INSERT INTO `pat_vital`(`patient_id`, `opd_id`, `consultantdoctorid`, `weight`, `height`, `medium_circumference`, `BMI_1`, `BMI_2`, `spo2`, `pulse`, `head_circumference`, `PR`, `RR`, `temp`, `systolic`, `diastolic`, `fbs`, `rbs`, `note`, `date`, `time`, `user`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$weight','$height','$medium_circumference','$BMI_1','$BMI_2','$spo2','$pulse','$head_circumference','$PR','$RR','$temp','$systolic','$diastolic','$fbs','$rbs','$note','$date','$time','$c_user')");
		}
	}
	
	// Diagnosis
	$order="";
	$certainity="";
	
	$patient_diagnosis=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	
	if($patient_diagnosis)
	{
		if(!$diagnosis)
		{
			mysqli_query($link, "DELETE FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		}
		else
		{
			if(mysqli_query($link, "UPDATE `pat_diagnosis` SET `diagnosis`='$diagnosis' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"))
			{
				//echo "Updated";
			}
			else
			{
				//echo "Failed, try again later.";
			}
		}
	}
	else
	{
		if($diagnosis)
		{
			if(mysqli_query($link, "INSERT INTO `pat_diagnosis`(`patient_id`, `opd_id`, `consultantdoctorid`, `diagnosis`, `order`, `certainity`, `date`, `time`, `user`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$diagnosis','$order','$certainity','$date','$time','$c_user')"))
			{
				//echo "Saved";
			}
			else
			{
				//echo "Failed, try again later.";
			}
		}
	}
	
	$diagnosis_chk=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `diagnosis_master` WHERE `diagnosis`='$diagnosis' "));
	if(!$diagnosis_chk)
	{
		$doc_dept=mysqli_fetch_array(mysqli_query($link, " SELECT `dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$consultantdoctorid' "));
		
		mysqli_query($link, "INSERT INTO `diagnosis_master`(`speciality_id`, `diagnosis`) VALUES ('$doc_dept[dept_id]','$diagnosis')");
	}
}
// Observation End


// Diagnosis Start
if($_POST["type"]=="opd_clinical_form2_old")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$patient_diagnosis=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
?>
<br>
<div style="text-align:center;">
	<h4>Diagnosis</h4>
	<table class="table table-condensed table-bordered table-hover">
		<tr>
			<th style="width: 140px;">Diagnosis</th>
			<td>
				<input type="text" class="span8" name="diagnosis" id="diagnosis" onKeyUp="diagnosis_up(this.value,event)" style="width:98%;" placeholder="Diagnosis" list="diagnosis_list" value="<?php echo $patient_diagnosis["diagnosis"]; ?>" />
				<datalist id="diagnosis_list"></datalist>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button class="btn btn-save" id="diagnosis_btn" onclick="save_diagnosis()"><i class="icon-save"></i> Save</button>
			</td>
		</tr>
	</table>
</div>
<?php
}
if($_POST["type"]=="save_diagnosis")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$diagnosis=mysqli_real_escape_string($link, $_POST["diagnosis"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	$order="";
	$certainity="";
	
	$patient_diagnosis=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	
	if($patient_diagnosis)
	{
		if(!$diagnosis)
		{
			mysqli_query($link, "DELETE FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		}
		else
		{
			if(mysqli_query($link, "UPDATE `pat_diagnosis` SET `diagnosis`='$diagnosis' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"))
			{
				echo "Updated";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
	else
	{
		if($diagnosis)
		{
			if(mysqli_query($link, "INSERT INTO `pat_diagnosis`(`patient_id`, `opd_id`, `consultantdoctorid`, `diagnosis`, `order`, `certainity`, `date`, `time`, `user`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$diagnosis','$order','$certainity','$date','$time','$c_user')"))
			{
				echo "Saved";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
	
	$diagnosis_chk=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `diagnosis_master` WHERE `diagnosis`='$diagnosis' "));
	if(!$diagnosis_chk)
	{
		$doc_dept=mysqli_fetch_array(mysqli_query($link, " SELECT `dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$consultantdoctorid' "));
		
		mysqli_query($link, "INSERT INTO `diagnosis_master`(`speciality_id`, `diagnosis`) VALUES ('$doc_dept[dept_id]','$diagnosis')");
	}
}
// Diagnosis Start


// Antenatal Start
if($_POST["type"]=="opd_clinical_form3")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$patient_antenatal_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_antenatal_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if(!$patient_antenatal_detail)
	{
		
	}
?>
<br>
<div style="text-align:center;">
	<h4>Antenatal</h4>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>LMP</th>
			<td>
				<input type="text" class="span2 datepicker_max" name="last_menstrual_period" id="last_menstrual_period" onKeyUp="last_menstrual_period_up(this.value,event)" placeholder="LMP" value="<?php echo $patient_antenatal_detail["last_menstrual_period"]; ?>" onChange="last_menstrual_period_change()">
			</td>
			<th>EDD</th>
			<td>
				<input type="text" class="span2 datepicker_min" name="est_delivery_date" id="est_delivery_date" placeholder="EDD" value="<?php echo $patient_antenatal_detail["est_delivery_date"]; ?>" onChange="est_delivery_date_change()">
			</td>
			<th>GA</th>
			<td>
				<input type="text" class="span2" name="gestational_age" id="gestational_age" placeholder="GA" value="<?php echo $patient_antenatal_detail["gestational_age"]; ?>" readonly>
			</td>
			<th>GA BY USG</th>
			<td>
				<input type="text" class="span2" name="gestational_age_usg" id="gestational_age_usg" placeholder="GA" value="<?php echo $patient_antenatal_detail["gestational_age_usg"]; ?>">
			</td>
		</tr>
		<tr>
			<th>Fundal Height</th>
			<td>
				<input type="text" class="span2" name="fundal_height" id="fundal_height" onKeyUp="fundal_height_up(this.value,event)" placeholder="Fundal Height" value="<?php echo $patient_antenatal_detail["fundal_height"]; ?>" />
			</td>
			<th>Presentation</th>
			<td>
				<input type="text" class="span2" name="presentation" id="presentation" onKeyUp="presentation_up(this.value,event)" placeholder="Presentation" value="<?php echo $patient_antenatal_detail["presentation"]; ?>" />
			</td>
			<th>FHR</th>
			<td>
				<input type="text" class="span2" name="fetal_heart_rate" id="fetal_heart_rate" onKeyUp="fetal_heart_rate_up(this.value,event)" placeholder="FHR" value="<?php echo $patient_antenatal_detail["fetal_heart_rate"]; ?>" />
			</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="8" style="text-align:center;">
				<button class="btn btn-save" id="sav_observation" onclick="sav_antenatal()"><i class="icon-save"></i> Save</button>
				
				<!--<button class="btn btn-process" onclick="$('#opd_clinical_btn3').click();"><i class="icon-forward"></i> Next</button>-->
			</td>
		</tr>
	</table>
</div>
<?php
}
if($_POST["type"]=="last_menstrual_period_change")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$last_menstrual_period=mysqli_real_escape_string($link, $_POST["last_menstrual_period"]);
	if($last_menstrual_period)
	{
		//$est_delivery_date=date(date("Y-m-d",strtotime($last_menstrual_period)), strtotime('+9 months'));
		$est_delivery_date=date("Y-m-d", strtotime("+9 months +7 days", strtotime($last_menstrual_period)));
		
		$earlier = new DateTime($last_menstrual_period);
		$today   = new DateTime(date("Y-m-d"));
		
		$abs_diff = $today->diff($earlier)->format("%a");
		
		$days = $abs_diff % 7;
		$weeks = ($abs_diff - $days) / 7;
		
		$gestational_age=$weeks." weeks ".$days." days";
		
		echo $est_delivery_date."@$@".$gestational_age;
	}
	else
	{
		echo "@$@";
	}
}
if($_POST["type"]=="est_delivery_date_change")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$est_delivery_date=mysqli_real_escape_string($link, $_POST["est_delivery_date"]);
	if($est_delivery_date)
	{
		$last_menstrual_period=date("Y-m-d", strtotime("-9 months -7 days", strtotime($est_delivery_date)));
		
		$earlier = new DateTime($last_menstrual_period);
		$today   = new DateTime(date("Y-m-d"));
		
		$abs_diff = $today->diff($earlier)->format("%a");
		
		$days = $abs_diff % 7;
		$weeks = ($abs_diff - $days) / 7;
		
		$gestational_age=$weeks." weeks ".$days." days";
		
		echo $last_menstrual_period."@$@".$gestational_age;
	}
	else
	{
		echo "@$@";
	}
}
if($_POST["type"]=="sav_antenatal")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$last_menstrual_period=mysqli_real_escape_string($link, $_POST["last_menstrual_period"]);
	$est_delivery_date=mysqli_real_escape_string($link, $_POST["est_delivery_date"]);
	$gestational_age=mysqli_real_escape_string($link, $_POST["gestational_age"]);
	$gestational_age_usg=mysqli_real_escape_string($link, $_POST["gestational_age_usg"]);
	$fundal_height=mysqli_real_escape_string($link, $_POST["fundal_height"]);
	$presentation=mysqli_real_escape_string($link, $_POST["presentation"]);
	$fetal_heart_rate=mysqli_real_escape_string($link, $_POST["fetal_heart_rate"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$branch_id=$pat_reg["branch_id"];
	
	$patient_antenatal_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_antenatal_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	
	if($patient_antenatal_detail)
	{
		if(!$last_menstrual_period && !$gestational_age_usg && !$fundal_height && !$presentation && !$fetal_heart_rate)
		{
			mysqli_query($link, "DELETE FROM `patient_antenatal_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		}
		else
		{
			if(mysqli_query($link, "UPDATE `patient_antenatal_detail` SET `last_menstrual_period`='$last_menstrual_period',`est_delivery_date`='$est_delivery_date',`gestational_age`='$gestational_age',`gestational_age_usg`='$gestational_age_usg',`fundal_height`='$fundal_height',`presentation`='$presentation',`fetal_heart_rate`='$fetal_heart_rate' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"))
			{
				echo "Updated";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
	else
	{
		if($last_menstrual_period || $gestational_age_usg || $fundal_height || $presentation || $fetal_heart_rate)
		{
			if(mysqli_query($link, "INSERT INTO `patient_antenatal_detail`(`patient_id`, `opd_id`, `consultantdoctorid`, `last_menstrual_period`, `est_delivery_date`, `gestational_age`, `gestational_age_usg`, `fundal_height`, `presentation`, `fetal_heart_rate`, `date`, `time`, `user`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$last_menstrual_period','$est_delivery_date','$gestational_age','$gestational_age_usg','$fundal_height','$presentation','$fetal_heart_rate','$date','$time','$c_user')"))
			{
				echo "Saved";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
	
	$diagnosis_chk=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `diagnosis_master` WHERE `diagnosis`='$diagnosis' "));
	if(!$diagnosis_chk)
	{
		$doc_dept=mysqli_fetch_array(mysqli_query($link, " SELECT `dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$consultantdoctorid' "));
		
		mysqli_query($link, "INSERT INTO `diagnosis_master`(`speciality_id`, `diagnosis`) VALUES ('$doc_dept[dept_id]','$diagnosis')");
	}
}
// Antenatal End

// Investigation Start
if($_POST["type"]=="opd_clinical_form4")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
?>
<br>
<div class="row"style="text-align:center;">
	<h4>Investigation</h4>
	<div class="span9">
	<table class="table table-condensed table-bordered">
		<tr>
			<th style="width: 100px;">Select Test <b style="color:#ff0000;">*</b></th>
			<td>
				<input type="text" class="span6" name="testname" id="testname" onfocus="ref_load_focus()" onKeyUp="ref_load_refdoc(this.value,event)" onblur="javascript:$('#ref_doc').fadeOut(500)" placeholder="Search Test Name Here">
				<input type="hidden" id="testid">
				<button class="btn btn-save" id="save_test_btn" onclick="save_test()" style="margin-bottom:10px;"><i class="icon-save"></i> Save</button>
				
				<input type="text" id="new_test" class="span6" onkeyup="new_test_up(event);" style="display:none;" placeholder="New Test Name" list="new_test_list">
				<datalist id="new_test_list" style="height: 0;"></datalist>
				
				<button class="btn btn-new" id="new_test_btn" onclick="new_test()" style="margin-bottom: 10px;"><i class="icon-edit"></i> New Test</button>
				
				<button class="btn btn-save" id="save_new_test_btn" onclick="save_new_test()"style="margin-bottom:10px;display:none;"><i class="icon-save"></i> Save New Test</button>
				
				<button type="button" class="btn btn-danger" id="new_test_can_btn" style="margin-bottom:10px;display:none;" onclick="can_test()"><i class="icon-remove"></i> Cancel</button>
				
				<div id="doc_info"></div>
				<div id="ref_doc" align="center">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="test_dd" style="max-height: 400px;overflow-y: scroll;">
				</div>
			</td>
		</tr>
	</table>
	</div>
	<div class="span3" style="max-height: 460px;overflow-y: scroll;">
		<table class="table table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>Frequently Used Investigation</th>
				</tr>
			</thead>
<?php
		$qry=mysqli_query($link, "SELECT `testid`,COUNT(`testid`) FROM `opd_clinic_investigation` WHERE `consultantdoctorid`='$consultantdoctorid' AND `testid`>0 GROUP BY `testid` ORDER BY COUNT(`testid`) DESC");
		while($data=mysqli_fetch_array($qry))
		{
			$test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$data[testid]'"));
?>
			<tr>
				<td>
					<a class="btn btn-link" style="padding: 0;" onclick="add_test('<?php echo $data["testid"]; ?>','<?php echo $test_info["testname"]; ?>')"><?php echo $test_info["testname"]; ?></a>
				</td>
			</tr>
<?php
		}
?>
		</table>
	</div>
</div>
<?php
}

if($_POST["type"]=="load_selected_tests")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$test_qry=mysqli_query($link, "SELECT * FROM `opd_clinic_investigation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	$test_num=mysqli_num_rows($test_qry);
	if($test_num>0)
	{
?>
		<table class="table table-condensed table-bordered">
			<thead class="table_header_fix">
				<tr>
					<th style="width: 30px;">#</th>
					<th>Test Name</th>
					<th width="5%">Remove</th>
				</tr>
			</thead>
<?php
		$m=1;
		$test_print="";
		while($test_data=mysqli_fetch_array($test_qry))
		{
			$test_print.="@".$test_data["testid"];
			$test_info=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$test_data[testid]'"));
			if($test_data["testid"]==0)
			{
				$test_info["testname"]=$test_data["testname"];
			}
?>
			<tr>
				<td><?php echo $m; ?></td>
				<td><?php echo $test_info["testname"];?></td>
				<td>
			<?php
				if($test_data['lab_id']=="")
				{
			?>
					<i class="icon-remove icon-large" style="color:#980000;cursor:pointer;" onclick="del_test('<?php echo $test_data['slno'];?>')"></i>
			<?php
				}
			?>
				</td>
			</tr>
<?php
			$m++;
		}
?>
			<tr>
				<td colspan="3">
					<input type="hidden" id="test_print" value="<?php echo $test_print; ?>">
				<?php
					$test_lab_qry=mysqli_query($link, "SELECT DISTINCT `lab_id` FROM `opd_clinic_investigation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `lab_id`!=''");
					while($test_lab=mysqli_fetch_array($test_lab_qry))
					{
						$testresult_num=mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$test_lab[lab_id]'"));
						$test_summary_num=mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$test_lab[lab_id]'"));
						$widalresult_num=mysqli_num_rows(mysqli_query($link, "SELECT `sl_no` FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$test_lab[lab_id]'"));
						
						if($testresult_num>0 || $test_summary_num>0 || $widalresult_num>0)
						{
				?>
						<button class="btn btn-process" onclick="view_test_result('<?php echo $uhid; ?>','<?php echo $test_lab["lab_id"]; ?>')" id="view_test_result_btn"><i class="icon-eye-open"></i> View Result(<?php echo $test_lab["lab_id"]; ?>)</button>
				<?php
						}
					}
				?>
				</td>
			</tr>
		</table>
<?php
	}
}

if($_POST["type"]=="search_test")
{
	//print_r($_POST);
	
	$test=mysqli_real_escape_string($link, $_POST["test"]);
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `sex` FROM `patient_info` WHERE `patient_id`='$uhid' "));

	$pat_sex=$pat_info["sex"];

	$sex_str="";
	if($pat_sex=="Male")
	{
		$sex_str=" AND (sex='M' OR sex='all')";
	}
	if($pat_sex=="Female")
	{
		$sex_str=" AND (sex='F' OR sex='all')";
	}
	
	if($test=="")
	{
		//$q="select * from testmaster where testid>0 $reg_category_str $reg_dept_str $sex_str order by testname";
	}
	else
	{
		$q="select * from testmaster where testname like '%$test%' $sex_str order by testname";
	}

	//echo $q;

	$data=mysqli_query($link, $q);
	
	$data_num=mysqli_num_rows($data);
	if($data_num>0)
	{
?>
		<table style="background-color:#FFF;" class="table table-bordered table-condensed" id="center_table">
			<th>Test Name</th>
			<?php
				$i=1;
				while($d=mysqli_fetch_array($data))
				{
					$rate=mysqli_fetch_array(mysqli_query($link, "select rate from testmaster_rate where testid='$d[testid]' and centreno='$center_no'"));	
					if($rate['rate'])
					{
						$drate=$rate['rate'];
					}
					else
					{
						$drate=$d['rate'];
					}
					//$drate=$d['rate'];
			?>
				<tr onClick="test_load('<?php echo $d['testid'];?>','<?php echo $d['testname'];?>','<?php echo $drate;?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
					<td>
						<?php echo $d['testname'];?>
						<div <?php echo "id=dvdoc".$i;?> style="display:none;">
							<?php echo "#".$d['testid']."#".$d['testname']."#".$drate;?>
						</div>
					</td>
				</tr>
			<?php
				$i++;
				}
			?>
		</table>
<?php
	}
}

if($_POST["type"]=="save_new_test")
{
	//print_r($_POST);
	
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$testname=mysqli_real_escape_string($link, $_POST["testname"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	$testid=0;
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$branch_id=$pat_reg["branch_id"];
	
	if($testname)
	{
		if(mysqli_query($link," INSERT INTO `opd_clinic_investigation`(`patient_id`, `opd_id`, `consultantdoctorid`, `branch_id`, `testid`, `testname`, `rate`, `lab_id`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$branch_id','$testid','$testname','0','','$user','$date','$time') "))
		{
			echo "Saved";
		}
		else
		{
			echo "Failed, try again later.";
		}
	}
	else
	{
		echo "Select Test";
	}
}

if($_POST["type"]=="save_test")
{
	//print_r($_POST);
	
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$testid=mysqli_real_escape_string($link, $_POST["testid"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$branch_id=$pat_reg["branch_id"];
	
	if($testid)
	{
		$entry_check_num=mysqli_num_rows(mysqli_query($link,"SELECT `slno` FROM `opd_clinic_investigation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `testid`='$testid'"));
		if($entry_check_num>0)
		{
			echo "Already exist !";
			//~ if(mysqli_query($link," UPDATE `opd_clinic_investigation` SET `consultantdoctorid`='$consultantdoctorid',`branch_id`='$branch_id' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `testid`='$testid' "))
			//~ {
				//~ echo "Updated";
			//~ }
			//~ else
			//~ {
				//~ echo "Failed, try again later";
			//~ }
		}
		else
		{
			if(mysqli_query($link," INSERT INTO `opd_clinic_investigation`(`patient_id`, `opd_id`, `consultantdoctorid`, `branch_id`, `testid`, `testname`, `rate`, `lab_id`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$branch_id','$testid',NULL,'0','','$user','$date','$time') "))
			{
				echo "Saved";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
	else
	{
		echo "Select Test";
	}
}
if($_POST["type"]=="del_test")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$slno=mysqli_real_escape_string($link, $_POST["slno"]);
	
	if(mysqli_query($link,"DELETE FROM `opd_clinic_investigation` WHERE `slno`='$slno' AND `patient_id`='$uhid' AND `opd_id`='$opd_id'"))
	{
		echo "Removed";
	}
	else
	{
		echo "Failed, try again later";
	}
}

// Investigation End


// Medication Start
if($_POST["type"]=="opd_clinical_form5")
{
	//print_r($_POST);
	
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$branch_id=$pat_reg["branch_id"];
?>
<br>
<div class="row"style="text-align:center;">
	<h4>Medication</h4>
	<div class="span9">
	<table class="table table-condensed table-bordered">
		<tr>
			<th style="width: 140px;">Select Drug <b style="color:#ff0000;">*</b></th>
			<td>
				<input type="text" name="medi" id="medi" class="span6" onFocus="focus_medi_list()" onKeyUp="load_medi_list(this.value,event)" onBlur="javascript:$('#med_div').fadeOut(500)" placeholder="Drug Name">
				<input type="text" id="new_medi" class="span6" onkeyup="new_medi_up(event);" style="display:none;" placeholder="New Drug Name" list="new_medi_list">
				<datalist id="new_medi_list" style="height: 0;"></datalist>
				
				<button class="btn btn-new" id="new_btn" onclick="new_medi()" style="margin-bottom: 10px;"><i class="icon-edit"></i> New Drug</button>
				<button type="button" class="btn btn-danger" id="can_btn" style="display:none;" onclick="can_medi()"><i class="icon-remove"></i> Cancel</button>
				
				<input type="hidden" id="medid" value="0">
				<input type="hidden" id="mediname">
				<div id="med_info"></div>
				<div id="med_div" align="center" style=""></div>
			</td>
		</tr>
		<tr>
			<th>Instruction</th>
			<td>
				<!--<input type="text" id="dosage" list="dosage_list" class="span8" placeholder="Dosage / Instruction" onkeyup="dosage_up(event)">-->
				<input type="text" class="span2" id="dosage" onkeyup="dosage_up(event)" placeholder="Dosage" list="dosage_list">
				<datalist id="dosage_list" style="height: 0;"></datalist>
				
				<input type="text" class="span2" id="frequency" onkeyup="frequency_up(event)" placeholder="Frequency" list="frequency_list">
				<datalist id="frequency_list" style="height: 0;"></datalist>
				
				<input type="text" class="span2" id="duration" onkeyup="duration_up(event)" placeholder="Duration" list="duration_list">
				<datalist id="duration_list" style="height: 0;"></datalist>
				
				<input type="hidden" class="span1" id="ph_quantity" placeholder="Quantity" onkeyup="ph_quantity(event)">
				
				<button class="btn btn-save" id="sav_medi" onclick="sav_medi()"><i class="icon-save"></i> Save</button>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="load_selected_medicines" style="max-height: 380px;overflow-y: scroll;">
					
				</div>
			</td>
		</tr>
	</table>
	</div>
	<div class="span3" style="max-height: 460px;overflow-y: scroll;">
		<table class="table table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>Frequently Used Medicines</th>
				</tr>
			</thead>
<?php
		$qry=mysqli_query($link, "SELECT `item_id`,COUNT(`item_id`),`dosage`,`frequency`,`duration`,`quantity` FROM `opd_clinic_medication` WHERE `consultantdoctorid`='$consultantdoctorid' AND `item_id`>0 GROUP BY `item_id` ORDER BY COUNT(`item_id`) DESC");
		while($data=mysqli_fetch_array($qry))
		{
			$item_info=mysqli_fetch_array(mysqli_query($link, "SELECT `item_name` FROM `item_master` WHERE `item_id`='$data[item_id]'"));
?>
			<tr>
				<td>
					<a class="btn btn-link" style="padding: 0;" onclick="add_medicine('<?php echo $data["item_id"]; ?>','<?php echo $item_info["item_name"]; ?>','<?php echo $data["dosage"]; ?>','<?php echo $data["frequency"]; ?>','<?php echo $data["duration"]; ?>','<?php echo $data["quantity"]; ?>')"><?php echo $item_info["item_name"]; ?></a>
				</td>
			</tr>
<?php
		}
?>
		</table>
	</div>
</div>
<?php
}
if($_POST["type"]=="load_selected_medicines")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$medi_qry=mysqli_query($link, "SELECT * FROM `opd_clinic_medication` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	$medi_num=mysqli_num_rows($medi_qry);
	if($medi_num>0)
	{
?>
		<table class="table table-condensed table-bordered">
			<thead class="table_header_fix">
				<tr>
					<th style="width: 30px;">#</th>
					<th>Drug Name</th>
					<th>Dosage</th>
					<th>Frequency</th>
					<th>Duration</th>
					<!--<th width="5%">Quantity</th>-->
					<th width="5%">Remove</th>
				</tr>
			</thead>
<?php
		$m=1;
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
?>
			<tr>
				<td><?php echo $m; ?></td>
				<td><?php echo $item_info["item_name"];?></td>
				<td><?php echo $medi_data["dosage"];?></td>
				<td><?php echo $medi_data["frequency"];?></td>
				<td><?php echo $medi_data["duration"];?></td>
				<!--<td><?php echo $medi_data["quantity"];?></td>-->
				<td><i class="icon-remove icon-large" style="color:#980000;cursor:pointer;" onclick="del_medicine('<?php echo $medi_data['slno'];?>')"></i></td>
			</tr>
<?php
			$m++;
		}
?>
		</table>
<?php
	}
}
if($_POST["type"]=="load_medicine")
{
	$dname=$_POST['val'];
	
	if(strlen($dname)>1)
	{
		$d=mysqli_query($link, "SELECT * FROM `item_master` where category_id='1' and `item_name` like '%$dname%' and `item_name`!='' order by `item_name` LIMIT 50");
	}
	else
	{
		//$d=mysqli_query($link, "SELECT * FROM `item_master` where category_id='1' order by `item_name` and `item_name`!='' LIMIT 50");
	}
	
	$num=mysqli_num_rows($d);
	
	if($num>0)
	{
?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table">
		<th>Drug Name</th>
<?php
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
?>
		<tr onclick="select_med('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['item_type_id'];?>','<?php echo $d1['generic_name'];?>')" style="cursor:pointer" <?php echo "id=med".$i;?>>
			<td><?php echo $d1['item_name'];?>
				<div <?php echo "id=mdname".$i;?> style="display:none;">
				<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$d1['item_type_id']."#".$d1['generic_name'];?>
				</div>
			</td>
		</tr>
		<?php
		$i++;
	}
?>
	</table>
<?php
	}
}

if($_POST["type"]=="save_medicine")
{
	//print_r($_POST);
	
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	//$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$item_id=mysqli_real_escape_string($link, $_POST["item_id"]);
	$new_medi=mysqli_real_escape_string($link, $_POST["new_medi"]);
	$dosage=mysqli_real_escape_string($link, $_POST["dosage"]);
	$frequency=mysqli_real_escape_string($link, $_POST["frequency"]);
	$duration=mysqli_real_escape_string($link, $_POST["duration"]);
	$quantity=mysqli_real_escape_string($link, $_POST["ph_quantity"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	if(!$quantity){ $quantity=0; }
	if(!$consultantdoctorid){ $consultantdoctorid=0; }
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$branch_id=$pat_reg["branch_id"];
	
	$doc_dept=mysqli_fetch_array(mysqli_query($link, "SELECT `dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$consultantdoctorid'"));
	$dept_id=$doc_dept["dept_id"];
	
	if(!$dept_id){ $dept_id=0; }
	
	if($item_id)
	{
		$entry_check_num=mysqli_num_rows(mysqli_query($link,"SELECT `slno` FROM `opd_clinic_medication` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `item_id`='$item_id'"));
		if($entry_check_num>0)
		{
			if(mysqli_query($link," UPDATE `opd_clinic_medication` SET `consultantdoctorid`='$consultantdoctorid',`dept_id`='$dept_id',`branch_id`='$branch_id',`dosage`='$dosage',`frequency`='$frequency',`duration`='$duration',`quantity`='$quantity' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `item_id`='$item_id' "))
			{
				echo "Updated";
			}
			else
			{
				echo "Failed, try again later";
			}
		}
		else
		{
			if(mysqli_query($link," INSERT INTO `opd_clinic_medication`(`patient_id`, `opd_id`, `consultantdoctorid`, `dept_id`, `branch_id`, `item_id`, `item_name`, `dosage`, `frequency`, `duration`, `quantity`, `bill_no`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$dept_id','$branch_id','$item_id',NULL,'$dosage','$frequency','$duration','$quantity','','$user','$date','$time') "))
			{
				echo "Saved";
			}
			else
			{
				echo "Failed, try again later";
			}
		}
	}
	else
	{
		//$new_id=nextID("","item_master","item_id","");
		
		//mysqli_query($link," INSERT INTO `item_master`(`item_id`, `short_name`, `item_name`, `hsn_code`, `category_id`, `sub_category_id`, `item_type_id`, `re_order`, `no_of_test`, `critical_stock`, `generic_name`, `rack_no`, `manufacturer_id`, `mrp`, `gst`, `strength`, `strip_quantity`, `unit`, `specific_type`, `class`, `need`) VALUES ('$new_id','','$new_medi','','1','1','0','0','0','0','','','0','0','0','0','0','','0','','0') ");
		
		$new_id=0;
		
		if(mysqli_query($link," INSERT INTO `opd_clinic_medication`(`patient_id`, `opd_id`, `consultantdoctorid`, `dept_id`, `branch_id`, `item_id`, `item_name`, `dosage`, `frequency`, `duration`, `quantity`, `bill_no`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$dept_id','$branch_id','$new_id','$new_medi','$dosage','$frequency','$duration','$quantity','','$user','$date','$time') "))
		{
			echo "Saved";
		}
		else
		{
			echo "Failed, try again later";
		}
		
		mysqli_query($link,"INSERT INTO `item_master_changes`(`item_id`, `old_name`, `new_name`, `process`, `date`, `time`, `user`) VALUES ('$new_id','','$new_medi','NEW ENTRY OPD CLINIC','$date','$time','$user')");
	}
	
	$dosage_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `dosage_master` WHERE `dosage`='$dosage'"));
	if($dosage_num==0 && $dosage)
	{
		mysqli_query($link,"INSERT INTO `dosage_master`(`dosage`) VALUES ('$dosage')");
	}
	
	$frequency_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `frequency_master` WHERE `frequency`='$frequency'"));
	if($frequency_num==0 && $frequency)
	{
		mysqli_query($link,"INSERT INTO `frequency_master`(`frequency`) VALUES ('$frequency')");
	}
	
	$duration_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `duration_master` WHERE `duration`='$duration'"));
	if($duration_num==0 && $duration)
	{
		mysqli_query($link,"INSERT INTO `duration_master`(`duration`) VALUES ('$duration')");
	}
}

if($_POST["type"]=="del_medicine")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$slno=mysqli_real_escape_string($link, $_POST["slno"]);
	
	if(mysqli_query($link,"DELETE FROM `opd_clinic_medication` WHERE `slno`='$slno' AND `patient_id`='$uhid' AND `opd_id`='$opd_id'"))
	{
		echo "Removed";
	}
	else
	{
		echo "Failed, try again later";
	}
}
// Medication End

// Advice Start
if($_POST["type"]=="opd_clinical_form6")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$patient_advice=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_advice_note` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
?>
<br>
<div style="text-align:center;">
	<h4>Advice Note</h4>
	<table class="table table-condensed table-bordered table-hover">
		<tr>
			<th style="width: 140px;">Advice Note</th>
			<td>
				<textarea rows="10" id="advice_note" placeholder="Advice Note" style="width: 90%;resize: none;"><?php echo $patient_advice["advice_note"]; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button class="btn btn-save" id="revisit_btn" onclick="save_advice_note()"><i class="icon-save"></i> Save</button>
			</td>
		</tr>
	</table>
</div>
<?php
}
if($_POST["type"]=="save_advice_note")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$advice_note=mysqli_real_escape_string($link, $_POST["advice_note"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$branch_id=$pat_reg["branch_id"];
	
	$patient_advice_note=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_advice_note` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_advice_note)
	{
		if($advice_note=="")
		{
			mysqli_query($link, "DELETE FROM `opd_clinic_advice_note` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		}
		else
		{
			if(mysqli_query($link, "UPDATE `opd_clinic_advice_note` SET `advice_note`='$advice_note' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"))
			{
					echo "Updated";
			}
			else
			{
				echo "Failed, try again later";
			}
		}
	}
	else
	{
		if($advice_note!="")
		{
			if(mysqli_query($link, "INSERT INTO `opd_clinic_advice_note`(`patient_id`, `opd_id`, `consultantdoctorid`, `advice_note`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$advice_note','$c_user','$date','$time')"))
			{
				echo "Saved";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
}
// Advice Start

// Re-visit Advice Start
if($_POST["type"]=="opd_clinical_form7")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$patient_revisit_advice=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_revisit_advice` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
?>
<br>
<div style="text-align:center;">
	<h4>Re-visit Advice</h4>
	<table class="table table-condensed table-bordered table-hover">
		<tr>
			<th style="width: 140px;">Re-visit after</th>
			<td>
				<select id="revisit_id">
			<?php
				$qry=mysqli_query($link, "SELECT `revisit_id`, `revisit_val`, `revisit_name` FROM `revisit_master` WHERE `status`=0 ORDER BY `revisit_val` ASC");
				while($data=mysqli_fetch_array($qry))
				{
					if($patient_revisit_advice["revisit_id"]==$data["revisit_id"]){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$data[revisit_id]' $sel>$data[revisit_name]</option>";
				}
			?>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width: 140px;">Advice Note</th>
			<td>
				<textarea rows="10" id="revisit_advice_note" placeholder="Advice Note" style="width: 90%;resize: none;"><?php echo $patient_revisit_advice["advice_note"]; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button class="btn btn-save" id="revisit_btn" onclick="save_revisit()"><i class="icon-save"></i> Save</button>
			</td>
		</tr>
	</table>
</div>
<?php
}
if($_POST["type"]=="save_revisit")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$revisit_id=mysqli_real_escape_string($link, $_POST["revisit_id"]);
	$advice_note=mysqli_real_escape_string($link, $_POST["revisit_advice_note"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$branch_id=$pat_reg["branch_id"];
	
	$patient_revisit_advice=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_revisit_advice` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_revisit_advice)
	{
		if($revisit_id==0 || $advice_note=="")
		{
			mysqli_query($link, "DELETE FROM `opd_clinic_revisit_advice` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
		}
		else
		{
			if(mysqli_query($link, "UPDATE `opd_clinic_revisit_advice` SET `revisit_id`='$revisit_id',`advice_note`='$advice_note' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"))
			{
				echo "Updated";
			}
			else
			{
				echo "Failed, try again later";
			}
		}
	}
	else
	{
		if($revisit_id>0 || $advice_note!="")
		{
			if(mysqli_query($link, "INSERT INTO `opd_clinic_revisit_advice`(`patient_id`, `opd_id`, `consultantdoctorid`, `revisit_id`, `advice_note`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$revisit_id','$advice_note','$c_user','$date','$time')"))
			{
				echo "Saved";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
}
// Re-visit Advice End

// Previous Record Start
if($_POST["type"]=="opd_clinical_form8")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$appoint_qry=mysqli_query($link, "SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `consultantdoctorid`>0 ORDER BY `slno` DESC");
	$appoint_num=mysqli_num_rows($appoint_qry);
	
	if($appoint_num==0)
	{
		echo "<center><h4>No Previous Record</h4></center>";
		exit;
	}
?>
<br>
<div style="text-align:center;">
	<h4>Previous Record</h4>
	<table class="table table-condensed table-bordered table-hover">
		<thead class="table_header_fix">
			<th style="width: 50px;">#</th>
			<th>Bill No</th>
			<th>Date</th>
			<th>Consultant Doctor</th>
			<th>Case History</th>
			<th></th>
		</thead>
<?php
	$n=1;
	while($appoint_info=mysqli_fetch_array($appoint_qry))
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appoint_info[consultantdoctorid]'"));
		
		$patient_case_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_case_history` WHERE `patient_id`='$appoint_info[patient_id]' AND `opd_id`='$appoint_info[opd_id]' "));
		
		$doc_td_str="";
		if($consultantdoctorid==$appoint_info["consultantdoctorid"])
		{
			$doc_td_str="color: blue;font-weight:bold;";
		}
?>
		<tr>
			<td><?php echo $appoint_num; ?></td>
			<td><?php echo $appoint_info["opd_id"]; ?></td>
			<td><?php echo date("d-m-Y",strtotime($appoint_info["date"])); ?></td>
			<td style="<?php echo $doc_td_str; ?>"><?php echo $doc_info["Name"]; ?></td>
			<td><?php echo $patient_case_history["case_history"]; ?></td>
			<td>
<?php
			if($opd_id==$appoint_info["opd_id"])
			{
?>
				<a class="btn btn-link" style="color:green;font-weight:bold;" disabled>Current Visit</a>
<?php
			}
			else
			{
?>
				<a class="btn btn-link" onclick="load_each_previous_record('<?php echo $appoint_info["patient_id"]; ?>','<?php echo $appoint_info["opd_id"]; ?>')">View Details</a>
<?php
			}
?>
			</td>
		</tr>
<?php
		$n++;
		$appoint_num--;
	}
?>
	</table>
</div>
<?php
}

if($_POST["type"]=="load_each_previous_record")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name`,`sex`,`dob`,`phone` FROM `patient_info` WHERE `patient_id`='$uhid'"));
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$reg_date=$pat_reg["date"];
	
	if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
	
	$appoint_info=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
	$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appoint_info[consultantdoctorid]'"));
	
	$patient_case_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_case_history` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
?>
	<table class="table table-condensed">
		<tr>
			<th>Unit No.</th>
			<th>Bill No.</th>
			<th>Patient Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Visit Time</th>
		</tr>
		<tr>
			<td><?php echo $uhid; ?></td>
			<td><?php echo $opd_id; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $age; ?><?php if($pat_info["dob"]){ echo "<br><small>(DOB: ".$pat_info["dob"].")</small>"; } ?></td>
			<td><?php echo $pat_info["sex"]; ?></td>
			<td><?php echo date("d-m-Y",strtotime($pat_reg["date"])); ?> <?php echo date("h:i A",strtotime($pat_reg["time"])); ?></td>
		</tr>
	</table>
	<table class="table table-condensed">
		<tr>
			<th>Consultant Doctor <span style="float:right;">:</span></th>
			<td><?php echo $doc_info["Name"]; ?></td>
		</tr>
		<tr>
			<th style="width: 150px;">Case History <span style="float:right;">:</span></th>
			<td><?php echo $patient_case_history["case_history"]; ?></td>
		</tr>
<?php
	$patient_general_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_general_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_general_exam)
	{
?>
		<tr>
			<th style="width: 150px;">General Examination <span style="float:right;">:</span></th>
			<td>
				<table class="table table-condensed">
					<tr>
						<th>Pallor</th>
						<th>Edema</th>
						<th>Icterus</th>
						<th>Dehydration</th>
						<th>Cyanosis</th>
						<th>Lymph Node</th>
						<th>GCS</th>
					</tr>
					<tr>
						<td><?php echo $patient_general_exam["pallor"]; ?></td>
						<td><?php echo $patient_general_exam["edema"]; ?></td>
						<td><?php echo $patient_general_exam["icterus"]; ?></td>
						<td><?php echo $patient_general_exam["dehydration"]; ?></td>
						<td><?php echo $patient_general_exam["cyanosis"]; ?></td>
						<td><?php echo $patient_general_exam["lymph_node"]; ?></td>
						<td><?php echo $patient_general_exam["gcs"]; ?></td>
					</tr>
				</table>
			</td>
		</tr>
<?php
	}
?>
<?php
	$patient_systemic_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_systemic_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_systemic_exam)
	{
?>
		<tr>
			<th style="width: 150px;">Systemic Examination <span style="float:right;">:</span></th>
			<td>
				<table class="table table-condensed">
					<tr>
						<th>Chest</th>
						<th>CVS</th>
						<th>CNS</th>
						<th>P/A</th>
					</tr>
					<tr>
						<td><?php echo $patient_systemic_exam["chest"]; ?></td>
						<td><?php echo $patient_systemic_exam["cvs"]; ?></td>
						<td><?php echo $patient_systemic_exam["cns"]; ?></td>
						<td><?php echo $patient_systemic_exam["pa"]; ?></td>
					</tr>
				</table>
			</td>
		</tr>
<?php
	}
?>
<?php
	$patient_vitals=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_vitals)
	{
		$bmi_color="";
		if($patient_vitals["BMI_1"]<18 || $patient_vitals["BMI_1"]>=25)
		{
			$bmi_color="color:red;font-weight:bold;";
		}
?>
		<tr>
			<th style="width: 150px;">Vitals <span style="float:right;">:</span></th>
			<td>
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
						<td><?php echo $patient_vitals["temp"]; ?><sup>0</sup>C</td>
						<td><?php echo $patient_vitals["pulse"]; ?></td>
						<td><?php echo $patient_vitals["spo2"]; ?>%</td>
						<td><?php echo $patient_vitals["systolic"]."/".$patient_vitals["diastolic"]; ?></td>
						<td><?php echo $patient_vitals["RR"]; ?></td>
						<td><?php echo $patient_vitals["fbs"]; ?></td>
						<td><?php echo $patient_vitals["rbs"]; ?></td>
					</tr>
			<?php
				if($patient_vitals["note"])
				{
					echo "<tr><th>Note</th><td colspan='9'>$patient_vitals[note]</td></tr>";
				}
			?>
				</table>
			</td>
		</tr>
<?php
	}
?>
<?php
	$patient_diagnosis=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_diagnosis)
	{
?>
		<tr>
			<th style="width: 150px;">Diagnosis <span style="float:right;">:</span></th>
			<td><?php echo $patient_diagnosis["diagnosis"]; ?></td>
		</tr>
<?php
	}
?>
<?php
	$patient_antenatal_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_antenatal_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_antenatal_detail)
	{
?>
		<tr>
			<th style="width: 150px;">Antenatal <span style="float:right;">:</span></th>
			<td>
				<table class="table table-condensed">
					<tr>
						<th>LMP</th>
						<th>EDD</th>
						<th>GA</th>
						<th>Fundal Height</th>
						<th>Presentation</th>
						<th>FHR</th>
					</tr>
					<tr>
						<td><?php echo date("d-m-Y",strtotime($patient_antenatal_detail["last_menstrual_period"])); ?></td>
						<td><?php echo date("d-m-Y",strtotime($patient_antenatal_detail["est_delivery_date"])); ?></td>
						<td><?php echo $patient_antenatal_detail["gestational_age"]; ?></td>
						<td><?php echo $patient_antenatal_detail["fundal_height"]; ?></td>
						<td><?php echo $patient_antenatal_detail["presentation"]; ?></td>
						<td><?php echo $patient_antenatal_detail["fetal_heart_rate"]; ?></td>
					</tr>
				</table>
			</td>
		</tr>
<?php
	}
?>
<?php
	$test_qry=mysqli_query($link, "SELECT a.`lab_id`,a.`testid`,b.`testname` FROM `opd_clinic_investigation` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$uhid' AND a.`opd_id`='$opd_id'");
	$test_num=mysqli_num_rows($test_qry);
	if($test_num>0)
	{
?>
		<tr>
			<th>Investigation <span style="float:right;">:</span></th>
			<td>
				<table class="table table-condensed">
			<?php
				$test_print="";
				while($test_info=mysqli_fetch_array($test_qry))
				{
					$testdone_color="";
					if($test_info["lab_id"]!="")
					{
						$testdone_color="color:green;";
						
						$test_print.="@".$test_info["testid"];
					}
			?>		<tr>
						<td style="<?php echo $testdone_color; ?>">
							<?php echo $test_info["testname"]; ?>
						</td>
					</tr>
			
			<?php
				}
			?>
					<tr>
						<td>
							<input type="hidden" id="test_print" value="<?php echo $test_print; ?>">
					<?php
						$test_lab_qry=mysqli_query($link, "SELECT DISTINCT `lab_id` FROM `opd_clinic_investigation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `lab_id`!=''");
						while($test_lab=mysqli_fetch_array($test_lab_qry))
						{
							$testresult_num=mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$test_lab[lab_id]'"));
							$test_summary_num=mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$test_lab[lab_id]'"));
							$widalresult_num=mysqli_num_rows(mysqli_query($link, "SELECT `sl_no` FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$test_lab[lab_id]'"));
							
							if($testresult_num>0 || $test_summary_num>0 || $widalresult_num>0)
							{
					?>
							<button class="btn btn-process" onclick="view_test_result('<?php echo $uhid; ?>','<?php echo $test_lab["lab_id"]; ?>')" id="view_test_result_btn"><i class="icon-eye-open"></i> View Result(<?php echo $test_lab["lab_id"]; ?>)</button>
					<?php
							}
						}
					?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
<?php
	}
?>
<?php
// Eye
	if($appoint_info["dept_id"]==12)
	{
?>
		<tr>
			<td colspan="2">
			<?php
				$right_click_image='<img src="../images/right.png" class="right_click">';
				
				include("eye_prescription_part.php");
				include("pac_prescription_part.php");
			?>
			</td>
		</tr>
<?php
	}
?>
<?php
	$medi_qry=mysqli_query($link, "SELECT * FROM `opd_clinic_medication` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	$medi_num=mysqli_num_rows($medi_qry);
	if($medi_num>0)
	{
?>
		<tr>
			<th>Medication <span style="float:right;">:</span></th>
			<td>
				<table class="table table-condensed">
			<?php
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
						<td><?php echo $item_info["item_name"]; ?> <?php if($medi_data["dosage"]){ echo "<br><small>".$medi_data["dosage"]." ".$medi_data["frequency"]." x ".$medi_data["duration"]."</small>"; } ?></td>
					</tr>
			
			<?php
				}
			?>
				</table>
			</td>
		</tr>
<?php
	}
?>
<?php
	$patient_advice=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_advice_note` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_advice)
	{
?>
		<tr>
			<th style="width: 150px;">Advice Note <span style="float:right;">:</span></th>
			<td><?php echo nl2br($patient_advice["advice_note"]); ?></td>
		</tr>
<?php
	}
?>
<?php
	$patient_revisit_advice=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_clinic_revisit_advice` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($patient_revisit_advice)
	{
		if($patient_revisit_advice["revisit_id"]>0)
		{
			$revisit_info=mysqli_fetch_array(mysqli_query($link, "SELECT `revisit_val`,`revisit_name` FROM `revisit_master` WHERE `revisit_id`='$patient_revisit_advice[revisit_id]'"));
			if($revisit_info)
			{
				echo "<p><b>Next follow up :</b>".$revisit_info["revisit_name"]."</p>";
			}
		}
?>
		<tr>
			<th style="width: 150px;">Re-visit Advice <span style="float:right;">:</span></th>
			<td><?php echo nl2br($patient_revisit_advice["advice_note"])."<br><b>Next follow up :</b>".$revisit_info["revisit_name"]; ?></td>
		</tr>
<?php
	}
?>
	</table>
<?php
}
// Previous Record End

// PAC Start
if($_POST["type"]=="opd_clinical_form9")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	include("patient_pac_form.php");
}
// PAC End

// Ophthalmology Start
if($_POST["type"]=="opd_clinical_form2")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	if($emp_info["levelid"]==5) // Doctor Login
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
		$consultantdoctorid=$doc_info["consultantdoctorid"];
	}
	
	include("patient_ophthalmology_form.php");
}
// Ophthalmology End

if($_POST["type"]=="load_case_history")
{
	$val=mysqli_real_escape_string($link, $_POST["val"]);
	
	if(strlen($val)>2)
	{
		$qry=mysqli_query($link, "SELECT DISTINCT `case_history` FROM `patient_case_history` WHERE `case_history` LIKE '%$val%' ORDER BY `case_history` ASC LIMIT 20");
		while($data=mysqli_fetch_array($qry))
		{
			echo "<option>$data[case_history]</option>";
		}
	}
}

if($_POST["type"]=="load_diagnosis")
{
	$val=mysqli_real_escape_string($link, $_POST["val"]);
	
	if(strlen($val)>2)
	{
		$qry=mysqli_query($link, "SELECT `diagnosis` FROM `diagnosis_master` WHERE `diagnosis` LIKE '%$val%' ORDER BY `diagnosis` ASC LIMIT 20");
		while($data=mysqli_fetch_array($qry))
		{
			echo "<option>$data[diagnosis]</option>";
		}
	}
}

if($_POST["type"]=="load_new_medi")
{
	$val=mysqli_real_escape_string($link, $_POST["val"]);
	
	if(strlen($val)>2)
	{
		$qry=mysqli_query($link, "SELECT DISTINCT `item_name` FROM `opd_clinic_medication` WHERE `item_name` LIKE '%$val%' LIMIT 20");
		while($data=mysqli_fetch_array($qry))
		{
			echo "<option>$data[item_name]</option>";
		}
	}
}

if($_POST["type"]=="load_dose")
{
	$val=mysqli_real_escape_string($link, $_POST["val"]);
	
	if(strlen($val)>=0)
	{
		$qry=mysqli_query($link, "SELECT DISTINCT `dosage` FROM `dosage_master` WHERE `dosage` LIKE '%$val%' LIMIT 20");
		while($data=mysqli_fetch_array($qry))
		{
			echo "<option>$data[dosage]</option>";
		}
	}
}

if($_POST["type"]=="load_frequency")
{
	$val=mysqli_real_escape_string($link, $_POST["val"]);
	
	if(strlen($val)>=0)
	{
		$qry=mysqli_query($link, "SELECT DISTINCT `frequency` FROM `frequency_master` WHERE `frequency` LIKE '%$val%' LIMIT 20");
		while($data=mysqli_fetch_array($qry))
		{
			echo "<option>$data[frequency]</option>";
		}
	}
}

if($_POST["type"]=="load_duration")
{
	$val=mysqli_real_escape_string($link, $_POST["val"]);
	
	if(strlen($val)>=0)
	{
		$qry=mysqli_query($link, "SELECT DISTINCT `duration` FROM `duration_master` WHERE `duration` LIKE '%$val%' LIMIT 20");
		while($data=mysqli_fetch_array($qry))
		{
			echo "<option>$data[duration]</option>";
		}
	}
}
?>
