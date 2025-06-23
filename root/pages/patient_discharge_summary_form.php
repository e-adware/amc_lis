<?php
$patient_discharge_summary=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
$patient_vitals=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' "));

$patient_doc_det=mysqli_fetch_array(mysqli_query($link, " SELECT `attend_doc`,`admit_doc`,`dept_id` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));

$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid`,`Name`,`dept_id` FROM `consultant_doctor_master` WHERE `emp_id`='$_SESSION[emp_id]' "));
$consultantdoctorid=$con_doc["consultantdoctorid"];

$summary_print_btn_show="";
$btn_name="Update Summary";
if(!$patient_discharge_summary)
{
	$summary_print_btn_show="display:none;";
	$btn_name="Save Summary";
	
	$patient_discharge_summary["consultantdoctorid"]=$consultantdoctorid;
}
if(!$patient_discharge_summary["emergency_contact"] || $patient_discharge_summary["emergency_contact"]=="")
{
	$patient_discharge_summary["emergency_contact"]="";
}

$patient_antenatal_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_antenatal_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' "));

$death_tr="display:none;";
$ipd_pat_death_details=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_death_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
if($ipd_pat_death_details && $patient_discharge_summary["discharge_id"]==105)
{
	$death_tr="";
}

if($template_id>0)
{
	$patient_antenatal_detail=$patient_discharge_summary=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discharge_summary_template` WHERE `template_id`='$template_id' "));
}
?>
<div style="max-height: 540px;overflow-y: scroll;">
	<!--<form role="form" id="patient_discharge_summary_form" method="post" enctype="multipart/form-data">-->
		<table class="table table-condensed">
			<tr>
				<td colspan="2">
					<select id="template_id" onchange="template_change(this.value)">
						<option value="0">Select Summary Template</option>
				<?php
					$qry=mysqli_query($link, "SELECT `template_id`,`template_name` FROM `patient_discharge_summary_template`"); //WHERE `user`='$_SESSION[emp_id]'
					while($data=mysqli_fetch_array($qry))
					{
						if($template_id==$data["template_id"]){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$data[template_id]' $sel>$data[template_name]</option>";
					}
				?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2"><b>Reason for admission</b><br/>
					<textarea name="admission_reason" id="admission_reason" placeholder="Reason for admission" style="width:96%;height:100px;resize:none;"><?php echo $patient_discharge_summary["admission_reason"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"><b>Case History</b><br/>
					<textarea name="case_history" id="case_history" placeholder="Case History" style="width:96%;height:100px;resize:none;"><?php echo $patient_discharge_summary["case_history"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"><b>Examination</b><br/>
					<textarea name="examination" id="examination" placeholder="Examination" style="width:96%;height:100px;resize:none;"><?php echo $patient_discharge_summary["examination"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"><b>Procedure Performed</b><br/>
					<textarea name="procedure_performed" id="procedure_performed" placeholder="Procedure Performed" style="width:96%;height:100px;resize:none;"><?php echo $patient_discharge_summary["procedure_performed"]; ?></textarea>
				</td>
			</tr>
		<?php
			$bbstetrics_tr="display:none;";
			$patient_delivery_qry=mysqli_query($link, " SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `slno` ASC");
			$patient_delivery_num=mysqli_num_rows($patient_delivery_qry);
			if($patient_delivery_num>0)
			{
				$bbstetrics_tr="";
			}
			$patient_discharge_summary_obs=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discharge_summary_obs` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
		?>
			<tr style="border: 2px solid #000;<?php echo $bbstetrics_tr; ?>">
				<td colspan="2">
					<table class="table table-condensed">
						<tr>
							<th>Obstetrics</th>
						</tr>
						<tr>
							<td>
								<label for="booked_obs" style="display: inline;">
									<input type="checkbox" name="booked_obs" id="booked_obs" value="1" <?php if($patient_discharge_summary_obs["booked"]==1){ echo "checked"; } ?>>
									Booked
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="unbooked_obs" style="display: inline;">
									<input type="checkbox" name="unbooked_obs" id="unbooked_obs" value="1" <?php if($patient_discharge_summary_obs["unbooked"]==1){ echo "checked"; } ?>>
									Unbooked
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="booked_elsewhere_obs" style="display: inline;">
									<input type="checkbox" name="booked_elsewhere_obs" id="booked_elsewhere_obs" value="1" <?php if($patient_discharge_summary_obs["booked_elsewhere"]==1){ echo "checked"; } ?>>
									Booked Elsewhere
								</label>
							</td>
						</tr>
						<tr>
							<td>
								<label for="gravida_obs" style="display: inline;">
									<b>G</b> <input type="text" name="gravida_obs" id="gravida_obs" value="<?php echo $patient_discharge_summary_obs["gravida"]?>" style="width:60px;">
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="para_obs" style="display: inline;">
									<b>P</b> <input type="text" name="para_obs" id="para_obs" value="<?php echo $patient_discharge_summary_obs["para"]?>" style="width:60px;">
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="live_obs" style="display: inline;">
									<b>L</b> <input type="text" name="live_obs" id="live_obs" value="<?php echo $patient_discharge_summary_obs["live"]?>" style="width:60px;">
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="abortion_obs" style="display: inline;">
									<b>A</b> <input type="text" name="abortion_obs" id="abortion_obs" value="<?php echo $patient_discharge_summary_obs["abortion"]?>" style="width:60px;">
								</label>
							</td>
						</tr>
						<tr>
							<td>
								<label for="last_menstrual_period" style="display: inline;">
									<b>LMP</b> <input type="text" class="span2 datepicker_max" name="last_menstrual_period" id="last_menstrual_period" onKeyUp="last_menstrual_period_up(this.value,event)" placeholder="LMP" value="<?php echo $patient_antenatal_detail["last_menstrual_period"]; ?>" onChange="last_menstrual_period_change()" readonly>
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="est_delivery_date" style="display: inline;">
									<b>EDD</b> <input type="text" class="span2 datepicker_min" name="est_delivery_date" id="est_delivery_date" placeholder="EDD" value="<?php echo $patient_antenatal_detail["est_delivery_date"]; ?>" onChange="est_delivery_date_change()" readonly>
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="gestational_age" style="display: inline;">
									<b>GA</b> <input type="text" class="span3" name="gestational_age" id="gestational_age" placeholder="GA" value="<?php echo $patient_antenatal_detail["gestational_age"]; ?>" readonly>
									
									<input type="text" class="span3" name="gestational_age_usg" id="gestational_age_usg" placeholder="BY USG" value="<?php echo $patient_antenatal_detail["gestational_age_usg"]; ?>" >
									
									<input type="hidden" class="span2" name="fundal_height" id="fundal_height" placeholder="Fundal Height" value="<?php echo $patient_antenatal_detail["fundal_height"]; ?>">
									<input type="hidden" class="span2" name="presentation" id="presentation" placeholder="Presentation" value="<?php echo $patient_antenatal_detail["presentation"]; ?>">
									<input type="hidden" class="span2" name="fetal_heart_rate" id="fetal_heart_rate" placeholder="FHR" value="<?php echo $patient_antenatal_detail["fetal_heart_rate"]; ?>">
								</label>
							</td>
						</tr>
						<tr>
							<td colspan="2"><b>Risk Factors</b><br/>
								<textarea name="risk_factor" id="risk_factor" placeholder="Risk Factors" style="width:96%;height:60px;resize:none;"><?php echo $patient_discharge_summary_obs["risk_factor"]; ?></textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2"><b>Antenatal Complications</b><br/>
								<textarea name="antenatal_complications" id="antenatal_complications" placeholder="Antenatal Complications" style="width:96%;height:60px;resize:none;"><?php echo $patient_discharge_summary_obs["antenatal_complications"]; ?></textarea>
							</td>
						</tr>
				<?php
					$baby=1;
					while($patient_delivery=mysqli_fetch_array($patient_delivery_qry))
					{
						$patient_discharge_summary_baby=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discharge_summary_baby` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `baby_uhid`='$patient_delivery[baby_uhid]' AND `baby_ipd_id`='$patient_delivery[baby_ipd_id]' "));
				?>
						<tr>
							<th>Baby Details <?php if($patient_delivery_num>1){ echo $baby; } ?></th>
						</tr>
						<tr>
							<td>
								<input type="hidden" class="baby" name="baby<?php echo $baby; ?>" id="baby<?php echo $baby; ?>" value="<?php echo $baby; ?>">
								<input type="hidden" name="baby_uhid<?php echo $baby; ?>" id="baby_uhid<?php echo $baby; ?>" value="<?php echo $patient_delivery["baby_uhid"]; ?>">
								<input type="hidden" name="baby_ipd_id<?php echo $baby; ?>" id="baby_ipd_id<?php echo $baby; ?>" value="<?php echo $patient_delivery["baby_ipd_id"]; ?>">
								<label for="live_birth<?php echo $baby; ?>" style="display: inline;">
									<input type="checkbox" name="live_birth<?php echo $baby; ?>" id="live_birth<?php echo $baby; ?>" value="1" <?php if($patient_discharge_summary_baby["live_birth"]==1){ echo "checked"; } ?>>
									Live Birth
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="fresh_birth<?php echo $baby; ?>" style="display: inline;">
									<input type="checkbox" name="fresh_birth<?php echo $baby; ?>" id="fresh_birth<?php echo $baby; ?>" value="1" <?php if($patient_discharge_summary_baby["fresh_birth"]==1){ echo "checked"; } ?>>
									Fresh Still Birth
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="macerated_birth<?php echo $baby; ?>" style="display: inline;">
									<input type="checkbox" name="macerated_birth<?php echo $baby; ?>" id="macerated_birth<?php echo $baby; ?>" value="1" <?php if($patient_discharge_summary_baby["macerated_birth"]==1){ echo "checked"; } ?>>
									Macerated Still Birth
								</label>
							</td>
						</tr>
						<tr>
							<td>
								<label for="term<?php echo $baby; ?>" style="display: inline;">
									<input type="checkbox" name="term<?php echo $baby; ?>" id="term<?php echo $baby; ?>" value="1" <?php if($patient_discharge_summary_baby["term"]==1){ echo "checked"; } ?>>
									Term
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="preterm<?php echo $baby; ?>" style="display: inline;">
									<input type="checkbox" name="preterm<?php echo $baby; ?>" id="preterm<?php echo $baby; ?>" value="1" <?php if($patient_discharge_summary_baby["preterm"]==1){ echo "checked"; } ?>>
									Preterm
								</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label for="iugr<?php echo $baby; ?>" style="display: inline;">
									<input type="checkbox" name="iugr<?php echo $baby; ?>" id="iugr<?php echo $baby; ?>" value="1" <?php if($patient_discharge_summary_baby["iugr"]==1){ echo "checked"; } ?>>
									IUGR
								</label>
							</td>
						</tr>
						<tr>
							<td>
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
							</td>
						</tr>
				<?php
						$baby++;
					}
				?>
					</table>
				</td>
			</tr>
			
			<tr>
				<td colspan="2"><b>Course in Hospital</b><br/>
					<textarea name="course_hospital" id="course_hospital" placeholder="Course in Hospital" style="width:96%;height:100px;resize:none;"><?php echo $patient_discharge_summary["course_hospital"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"><b>Final diagnosis</b><br/>
					<textarea name="final_diagnosis" id="final_diagnosis" placeholder="Final diagnosis" style="width:96%;height:100px;resize:none;"><?php echo $patient_discharge_summary["final_diagnosis"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"><b>Discharge Instructions</b><br/>
					<textarea name="discharge_instruction" id="discharge_instruction" placeholder="Discharge Instructions" style="width:96%;height:100px;resize:none;"><?php echo $patient_discharge_summary["discharge_instruction"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"><b>Report to Hospital in Case of</b><br/>
					<textarea name="hospital_report" id="hospital_report" placeholder="Report to Hospital in Case of" style="width:96%;height:100px;resize:none;"><?php echo $patient_discharge_summary["hospital_report"]; ?></textarea>
				</td>
			</tr>
			<tr style="display:none;">
				<td colspan="2"><b>Vital signs at the time of discharge</b><br/>
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
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2"><b>Medication at discharge</b><br/>
					<div class="span9" style="width:75%;margin-left:0;">
						<table class="table table-condensed table-bordered">
							<tr>
								<th style="width: 140px;">Select Drug <b style="color:#ff0000;">*</b></th>
								<td>
									<input type="text" name="medi" id="medi" class="span8" onFocus="focus_medi_list()" onKeyUp="load_medi_list(this.value,event)" placeholder="Drug Name">
									<input type="text" id="new_medi" class="span8" onkeyup="new_medi_up(event);" style="display:none;" placeholder="New Drug Name" list="new_medi_list">
									<datalist id="new_medi_list" style="height: 0;"></datalist>
									
									<button type="button" class="btn btn-new" id="new_btn" onclick="new_medi()" style="margin-bottom: 10px;"><i class="icon-edit"></i> New Drug</button>
									<button type="button" class="btn btn-danger" id="can_btn" style="display:none;" onclick="can_medi()"><i class="icon-remove"></i> Cancel</button>
									
									<input type="hidden" id="medid" value="0">
									<input type="hidden" id="mediname">
									<div id="med_info"></div>
									<div id="med_div_list" align="center" style=""></div>
								</td>
							</tr>
							<tr>
								<th>Instruction</th>
								<td>
									<!--<input type="text" id="dosage" list="dosage_list" class="span8" placeholder="Dosage / Instruction" onkeyup="dosage_up(event)">-->
									<input type="text" class="span3" id="dosage" onkeyup="dosage_up(event)" ondblclick="load_dose(event)" placeholder="Dosage" list="dosage_list">
									<datalist id="dosage_list" style="height: 0;"></datalist>
									
									<input type="text" class="span3" id="frequency" onkeyup="frequency_up(event)" ondblclick="load_frequency(event)" placeholder="Frequency" list="frequency_list">
									<datalist id="frequency_list" style="height: 0;"></datalist>
									
									<input type="text" class="span3" id="duration" onkeyup="duration_up(event)" ondblclick="load_duration(event)" placeholder="Duration" list="duration_list">
									<datalist id="duration_list" style="height: 0;"></datalist>
									
									<input type="hidden" class="span1" id="ph_quantity" placeholder="Quantity" onkeyup="ph_quantity(event)">
									
									<button type="button" class="btn btn-save" id="sav_medi" onclick="sav_medi()"><i class="icon-save"></i> Add</button>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="load_selected_medicines" style="max-height: 300px;overflow-y: scroll;">
										
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div class="span3" style="width:20%;margin-left:0;max-height: 400px;overflow-y: scroll;">
						<table class="table table-condensed">
							<thead class="table_header_fix">
								<tr>
									<th>Frequently Used Medicines</th>
								</tr>
							</thead>
				<?php
						$qry=mysqli_query($link, "SELECT `item_id`,COUNT(`item_id`),`dosage`,`frequency`,`duration`,`quantity` FROM `opd_clinic_medication` WHERE `item_id`>0 GROUP BY `item_id` ORDER BY COUNT(`item_id`) DESC");
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
				</td>
			</tr>
			<tr>
				<th style="width: 220px;">Re-visit after <b style="float:right;">:</b></th>
				<td>
					<input type="text" name="revisit_id" id="revisit_id" value="<?php echo $patient_discharge_summary["revisit_id"]; ?>" placeholder="1 month" list="revisit_id_list">
					<datalist id="revisit_id_list">
					<?php
						$qry=mysqli_query($link, "SELECT `revisit_id`, `revisit_val`, `revisit_name` FROM `revisit_master` WHERE `status`=0 ORDER BY `revisit_val` ASC");
						while($data=mysqli_fetch_array($qry))
						{
							echo "<option>$data[revisit_name]</option>";
						}
					?>
					</datalist>
					<!--<select name="revisit_id" id="revisit_id">
				<?php
					$qry=mysqli_query($link, "SELECT `revisit_id`, `revisit_val`, `revisit_name` FROM `revisit_master` WHERE `status`=0 ORDER BY `revisit_val` ASC");
					while($data=mysqli_fetch_array($qry))
					{
						if($patient_discharge_summary["revisit_id"]==$data["revisit_id"]){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$data[revisit_id]' $sel>$data[revisit_name]</option>";
					}
				?>
					</select>-->
				</td>
			</tr>
			<tr>
				<th>Discharge Type <b style="color:#ff0000;">*</b> <b style="float:right;">:</b></th>
				<td>
					<select name="discharge_id" id="discharge_id" onchange="discharge_change()">
						<option value="0">Select</option>
				<?php
					$qry=mysqli_query($link, "SELECT `discharge_id`, `discharge_name` FROM `discharge_master` WHERE `discharge_name`!='' ORDER BY `discharge_name` ASC");
					while($data=mysqli_fetch_array($qry))
					{
						if($patient_discharge_summary["discharge_id"]==$data["discharge_id"]){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$data[discharge_id]' $sel>$data[discharge_name]</option>";
					}
				?>
					</select>
					
					<b> Discharge By <span style="color:#ff0000;">*</span></b>
					<select name="discharge_doc_id" id="discharge_doc_id">
						<option value="0">Select</option>
				<?php
					$qry=mysqli_query($link, "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `status`=0 ORDER BY `Name` ASC");
					while($data=mysqli_fetch_array($qry))
					{
						if($patient_discharge_summary["consultantdoctorid"]==$data["consultantdoctorid"]){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$data[consultantdoctorid]' $sel>$data[Name]</option>";
					}
				?>
					</select>
				</td>
			</tr>
			<tr class="death_tr" style="<?php echo $death_tr;?>">
				<td colspan="2">
					<div id="death_det" style="padding-left:10px; box-shadow: 0px 0px 6px 1px #ECBBB9;">
						<br/>
						<b>Death Date</b> : <input type="text" id="death_date" class="span2 datepicker" value="<?php echo $ipd_pat_death_details["death_date"];?>" placeholder="YY-MM-DD" /> &nbsp;&nbsp;
						<b>Death Time</b> : <input type="text" id="death_time" class="span2 timepicker" value="<?php echo $ipd_pat_death_details["death_time"];?>" placeholder="HH:MM" /> &nbsp;&nbsp;
						<b>Cause</b> : <input type="text" id="death_cause" class="span6" value="<?php echo $ipd_pat_death_details["death_cause"];?>" placeholder="Cause of death" list="death_cause_list" />
						<datalist id="death_cause_list">
					<?php
						$death_qry=mysqli_query($link,"SELECT DISTINCT `death_cause` FROM `ipd_pat_death_details` ORDER BY `death_cause`");
						while($death_val=mysqli_fetch_array($death_qry))
						{
							echo "<option>$death_val[death_cause]</option>";
						}
					?>
						</datalist>
					</div>
				</td>
			</tr>
			<tr>
				<th>Emergency Contact No. <b style="float:right;">:</b></th>
				<td>
					<input type="text" name="emergency_contact" id="emergency_contact" value="<?php echo $patient_discharge_summary["emergency_contact"]; ?>">
				</td>
			</tr>
		</table>
		<input type="hidden" name="consultantdoctorid_summary" id="consultantdoctorid_summary" value="<?php echo $patient_doc_det["attend_doc"]; ?>">
	<!--</form>-->
	<br>
	<center>
		<button type="button" class="btn btn-lg btn-info" id="save_pac_btn" onclick="save_discharge_summary()"><i class="icon-save"></i> <?php echo $btn_name; ?></button>
		
		<button class="btn btn-excel" onclick="save_discharge_summary_template()"><i class="icon-file"></i> Save As Template</button>
		
		<button class="btn btn-print" id="summary_print_btn" onclick="print_discharge_summary()" style="<?php echo $summary_print_btn_show; ?>"><i class="icon-print"></i> Summary</button>
		<button class="btn btn-close" id="death_certificate_print_btn" onclick="print_death_certificate()" style="<?php echo $death_tr; ?>"><i class="icon-print"></i> Death Certificate</button>
	</center>
	<br>
	<br>
	<br>
</div>
<script>
	function last_menstrual_period_change()
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"last_menstrual_period_change",
			last_menstrual_period:$("#last_menstrual_period").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			
			var res=data.split("@$@");
			$("#est_delivery_date").val(res[0]);
			$("#gestational_age").val(res[1]);
		})
	}
	function est_delivery_date_change()
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"est_delivery_date_change",
			est_delivery_date:$("#est_delivery_date").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			
			if(data)
			{
				var res=data.split("@$@");
				$("#last_menstrual_period").val(res[0]);
				$("#gestational_age").val(res[1]);
			}
		})
	}
	
	load_selected_medicines();
	function weight_up()
	{
		calculate_bmi();
	}
	function height_up()
	{
		calculate_bmi();
	}
	function calculate_bmi()
	{
		var weight=parseInt($("#weight").val());
		if(!weight){ weight=0; }
		
		var height=parseInt($("#height").val());
		if(!height){ height=0; }
		
		height=height/100;
		
		if(weight>0 && height>0)
		{
			var bmi=(weight/(height*height));
			var bmi = bmi.toFixed(2);
			var bmi = bmi.split(".");
			$("#BMI_1").val(bmi[0]);
			$("#BMI_2").val(bmi[1]);
		}
		else
		{
			$("#BMI_1").val("");
			$("#BMI_2").val("");
		}
	}
	
	// Medication Start
	function focus_medi_list()
	{
		//$("#med_div_list").fadeIn(500);
		$("#medi").select();
	}
	var med_tr=1;
	var med_sc=0;
	function load_medi_list(val,e)
	{
		$("#med_dos").hide();
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#med_div_list").html("<img src='../images/ajax-loader.gif' />");
				$("#med_div_list").fadeIn(500);
				$.post("pages/doc_queue_data.php",
				{
					val:val,
					type:"load_medicine"
				},
				function(data,status)
				{
					$("#med_div_list").html(data);
					med_tr=1;
					med_sc=0;
				})	
			}
			else if(unicode==40)
			{
				var chk=med_tr+1;
				var cc=document.getElementById("med"+chk).innerHTML;
				if(cc)
				{
					med_tr=med_tr+1;
					$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var med_tr1=med_tr-1;
					$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=med_tr%1;
					if(z3==0)
					{
						$("#med_div_list").scrollTop(med_sc)
						med_sc=med_sc+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=med_tr-1;
				var cc=document.getElementById("med"+chk).innerHTML;
				if(cc)
				{
					med_tr=med_tr-1;
					$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var med_tr1=med_tr+1;
					$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=med_tr%1;
					if(z3==0)
					{
						med_sc=med_sc-30;
						$("#med_div_list").scrollTop(med_sc)
					}
				}
			}
		}
		else
		{
			var docs=document.getElementById("mdname"+med_tr).innerHTML.split("#");
			var doc_naam=docs[2].trim()
			$("#medi").val(doc_naam);
			$("#medid").val(docs[1]);
			$("#unit").val(docs[3]);
			var d_in=docs[5];
			//$("#doc_mark").val(docs[5]);
			$("#med_info").html(d_in);
			$("#med_info").fadeIn(500);
			$("#g_name").show();
			select_med(docs[1],docs[2],docs[3],docs[4]);
		}
	}
	function select_med(id,name,typ,gen)
	{
		//alert(id+' '+name+' '+typ+' '+gen);
		$("#medi").val(name);
		$("#medid").val(id);
		$("#med_info").html("");
		$("#med_div_list").fadeOut(500);
		$("#unit").val(typ);
		
		$("#dosage").focus();
	}
	
	var _changeInterval = null;
	function new_medi_up(e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else if(e.which==13 && $("#new_medi").val().trim()!="")
		{
			$("#dosage").focus();
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_new_medi(e);
			}, 300);
		}
	}
	function load_new_medi(e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_new_medi",
			val:$("#new_medi").val(),
		},
		function(data,status)
		{
			$("#new_medi_list").html(data);
		})
	}
	
	var _changeInterval = null;
	function dosage_up(e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else if(e.which==13 && $("#dosage").val().trim()!="")
		{
			$("#frequency").focus();
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_dose(e);
			}, 300);
		}
	}
	function load_dose(e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_dose",
			val:$("#dosage").val(),
		},
		function(data,status)
		{
			$("#dosage_list").html(data);
		})
	}
	
	var _changeInterval = null;
	function frequency_up(e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else if(e.which==13 && $("#frequency").val().trim()!="")
		{
			$("#duration").focus();
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_frequency(e);
			}, 300);
		}
	}
	function load_frequency(e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_frequency",
			val:$("#frequency").val(),
		},
		function(data,status)
		{
			$("#frequency_list").html(data);
		})
	}
	
	var _changeInterval = null;
	function duration_up(e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else if(e.which==13 && $("#duration").val().trim()!="")
		{
			$("#sav_medi").focus();
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_duration(e);
			}, 300);
		}
	}
	function load_duration(e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_duration",
			val:$("#duration").val(),
		},
		function(data,status)
		{
			$("#duration_list").html(data);
		})
	}
	
	function ph_quantity(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$("#sav_medi").focus();
		}
	}
	function new_medi()
	{
		$("#medi").hide();
		$("#new_btn").hide();
		$("#can_btn").show();
		$("#new_medi").show().val('').focus();
		$("#medid").val('');
	}
	function can_medi()
	{
		$("#new_medi").hide();
		$("#can_btn").hide();
		$("#new_btn").show();
		$("#medi").show().val('').focus();
		$("#medid").val('');
	}
	function tab(id,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13 && $("#new_medi").val().trim()!="")
		{
			$("#dosage").focus();
		}
	}
	function sav_medi()
	{
		if($("#new_medi").val()=="" && ($("#medi").val().trim()=="" || $("#medid").val().trim()=="" || $("#medid").val().trim()=="0"))
		{
			can_medi();
			$("#medi").focus();
			return false;
		}
		if($("#dosage").val().trim()=="")
		{
			$("#dosage").focus();
			//return false;
		}
		
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"save_medicine",
			uhid:$("#uhid").val(),
			opd_id:$("#ipd").val(),
			consultantdoctorid:$("#consultantdoctorid_summary").val(),
			item_id:$("#medid").val(),
			new_medi:$("#new_medi").val(),
			dosage:$("#dosage").val(),
			frequency:$("#frequency").val(),
			duration:$("#duration").val(),
			ph_quantity:$("#ph_quantity").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				load_selected_medicines();
				$("#medi").focus();
			}, 1000);
		})
	}
	function load_selected_medicines()
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"load_selected_medicines",
			uhid:$("#uhid").val(),
			opd_id:$("#ipd").val(),
			consultantdoctorid:$("#consultantdoctorid_summary").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_selected_medicines").html(data);
			
			$("#medi").val("");
			$("#new_medi").val("");
			$("#medid").val("0");
			$("#mediname").val("");
			$("#dosage").val("");
			$("#frequency").val("");
			$("#duration").val("");
			$("#ph_quantity").val("");
		})
	}
	function add_medicine(item_id,item_name,dosage,frequency,duration,quantity)
	{
		$("#medi").val(item_name);
		$("#new_medi").val("");
		$("#medid").val(item_id);
		$("#mediname").val(item_name);
		$("#dosage").val(dosage);
		$("#frequency").val(frequency);
		$("#duration").val(duration);
		$("#ph_quantity").val(quantity);
	}
	function del_medicine(slno)
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to remove this medicine?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Ok',
					className: "btn btn-danger",
					callback: function()
					{
						$("#loader").show();
						$.post("pages/doc_queue_data.php",
						{
							type:"del_medicine",
							slno:slno,
							uhid:$("#uhid").val(),
							opd_id:$("#ipd").val(),
						},
						function(data,status)
						{
							$("#loader").hide();
							bootbox.dialog({ message: "<h4>"+data+"</h4>"});
							setTimeout(function()
							{
								bootbox.hideAll();
								load_selected_medicines();
							}, 1000);
						})
					}
				}
			}
		});
	}
	// Medication End
	
	function template_change(template_id)
	{
		discharge_summ(template_id);
	}
	
	function save_discharge_summary_template()
	{
		if($("#admission_reason").val()=="" && $("#case_history").val()=="" && $("#examination").val()=="" && $("#procedure_performed").val()=="" && $("#course_hospital").val()=="" && $("#final_diagnosis").val()=="" && $("#discharge_instruction").val()=="" && $("#hospital_report").val()=="")
		{
			bootbox.dialog({ message: "<h4>Empty Template</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 2000);
			return false;
		}
		
		bootbox.dialog({
			message: "Template Name:<input type='text' id='template_name' autofocus />",
			title: "Case Summary Template",
			buttons: {
				main: {
					label: "<i class='icon-ok'></i>Save",
					className: "btn-primary",
					callback: function() {
						if($("#template_name").val()!="")
						{
							save_discharge_summary_template_ok();
						}else
						{
							bootbox.alert("Template name cannot blank");
						}
					}
				}
			}
		});
		
		if($("#template_id").val()>0)
		{
			$("#template_name").val($("#template_id option:selected").text());
		}
	}
	
	function save_discharge_summary_template_ok()
	{
		$("#loader").show();
		$.post("pages/patient_discharge_summary_template_save.php",
		{
			type:"discharge_summary_template",
			uhid:$("#uhid").val(),
			ipd_id:$("#ipd").val(),
			
			template_id:$("#template_id").val(),
			template_name:$("#template_name").val(),
			
			admission_reason:$("#admission_reason").val(),
			case_history:$("#case_history").val(),
			examination:$("#examination").val(),
			procedure_performed:$("#procedure_performed").val(),
			course_hospital:$("#course_hospital").val(),
			final_diagnosis:$("#final_diagnosis").val(),
			discharge_instruction:$("#discharge_instruction").val(),
			hospital_report:$("#hospital_report").val(),
			
			risk_factor:$("#risk_factor").val(),
			antenatal_complications:$("#antenatal_complications").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			var res=data.split("@");
			bootbox.dialog({ message: "<h4>"+res[1]+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				if(res[0]==101)
				{
					window.location.reload(true);
				}
			}, 2000);
		})
	}
	
	function save_discharge_summary()
	{
		var baby=$(".baby");
		var baby_all="";
		for(var i=0;i<baby.length;i++)
		{
			var tr_counter=baby[i].value;
			
			baby_all=baby_all+"@#@"+$("#live_birth"+tr_counter+":checked").length+"@@"+$("#fresh_birth"+tr_counter+":checked").length+"@@"+$("#macerated_birth"+tr_counter+":checked").length+"@@"+$("#term"+tr_counter+":checked").length+"@@"+$("#preterm"+tr_counter+":checked").length+"@@"+$("#iugr"+tr_counter+":checked").length+"@@"+$("#baby_uhid"+tr_counter+"").val()+"@@"+$("#baby_ipd_id"+tr_counter+"").val();
		}
		
		$("#loader").show();
		$.post("pages/patient_discharge_summary_save.php",
		{
			type:"opd_patient_list",
			uhid:$("#uhid").val(),
			ipd_id:$("#ipd").val(),
			
			admission_reason:$("#admission_reason").val(),
			case_history:$("#case_history").val(),
			examination:$("#examination").val(),
			procedure_performed:$("#procedure_performed").val(),
			course_hospital:$("#course_hospital").val(),
			final_diagnosis:$("#final_diagnosis").val(),
			discharge_instruction:$("#discharge_instruction").val(),
			hospital_report:$("#hospital_report").val(),
			
			// Vital
			weight:$("#weight").val(),
			height:$("#height").val(),
			BMI_1:$("#BMI_1").val(),
			BMI_2:$("#BMI_2").val(),
			temp:$("#temp").val(),
			pulse:$("#pulse").val(),
			spo2:$("#spo2").val(),
			systolic:$("#systolic").val(),
			diastolic:$("#diastolic").val(),
			RR:$("#RR").val(),
			fbs:$("#fbs").val(),
			rbs:$("#rbs").val(),
			
			revisit_id:$("#revisit_id").val(),
			discharge_id:$("#discharge_id").val(),
			discharge_doc_id:$("#discharge_doc_id").val(),
			emergency_contact:$("#emergency_contact").val(),
			
			booked:$("#booked_obs:checked").length,
			unbooked:$("#unbooked_obs:checked").length,
			booked_elsewhere:$("#booked_elsewhere_obs:checked").length,
			gravida:$("#gravida_obs").val(),
			para:$("#para_obs").val(),
			live:$("#live_obs").val(),
			abortion:$("#abortion_obs").val(),
			risk_factor:$("#risk_factor").val(),
			antenatal_complications:$("#antenatal_complications").val(),
			baby_all:baby_all,
			
			last_menstrual_period:$("#last_menstrual_period").val(),
			est_delivery_date:$("#est_delivery_date").val(),
			gestational_age:$("#gestational_age").val(),
			gestational_age_usg:$("#gestational_age_usg").val(),
			fundal_height:$("#fundal_height").val(),
			presentation:$("#presentation").val(),
			fetal_heart_rate:$("#fetal_heart_rate").val(),
			
			death_date:$("#death_date").val(),
			death_time:$("#death_time").val(),
			death_cause:$("#death_cause").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				$("#summary_print_btn").show();
				
				if($("#discharge_id").val()==105)
				{
					$("#death_certificate_print_btn").show();
				}
				else
				{
					$("#death_certificate_print_btn").hide();
				}
			}, 2000);
		})
	}
	function discharge_change()
	{
		if($("#discharge_id").val()==105)
		{
			$(".death_tr").show();
		}
		else
		{
			$(".death_tr").hide();
		}
	}
	function print_death_certificate()
	{
		var uhid=btoa($("#uhid").val());
		var ipd=btoa($("#ipd").val());
		var usr=btoa($("#user").text().trim());
		url="pages/death_certificate.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function print_discharge_summary()
	{
		var url="pages/patient_discharge_summary_print.php";
		
		var uhid=$("#uhid").val();
		url=url+"?v="+btoa(1234567890)+"&uhid="+btoa(uhid);
		
		var ipd=$("#ipd").val();
		url=url+"&ipd="+btoa(ipd);
		
		var user=$("#user").text().trim();
		url=url+"&user="+btoa(user);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
#med_div_listxx
{
	background-color:#FFF;
	border:1px solid;
	box-shadow:2px 2px 15px #000;
	display:none;
	max-height:300px;
	overflow:scroll;
	overflow-x:hidden;
	position:relative;
	width:770px;
	z-index:9999;
}
</style>
