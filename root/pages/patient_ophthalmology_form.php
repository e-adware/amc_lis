<?php
	$patient_case_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_case_history` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_eye_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_history` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_eye_visual=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_visual` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_eye_fractometer=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_fractometer` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_eye_present_power=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_present_power` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_eye_prescribe_power=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_prescribe_power` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_eye_external_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_external_exam` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_eye_lamp_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_lamp_exam` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	
	if(!$patient_eye_history)
	{
		//~ $patient_eye_history["present_history"]="Nil";
		//~ $patient_eye_history["past_history"]="Nil";
		//~ $patient_eye_history["family_history"]="Nil";
		//~ $patient_eye_history["birth_history"]="Nil";
		//~ $patient_eye_history["nutrition_history"]="Normal";
		//~ $patient_eye_history["pedigree_chart"]="Nil";
	}
?>
<br>
<div style="max-height: 520px;overflow-y: scroll;">
	<form role="form" id="patient_ophthalmology_form" method="post" enctype="multipart/form-data">
		<center>
			<h4>Ophthalmology</h4>
		</center>
		<table class="table table-condensed table-bordered">
			<!--<tr>
				<th colspan="8" style="text-align:center;background-color: #ddd;">Case History</th>
			</tr>-->
			<tr>
				<th style="width: 140px;">Case History</th>
				<td colspan="7">
					<!--<input type="text" class="span8" name="case_history_eye" id="case_history_eye" onKeyUp="case_history_eye_up(this.value,event)" style="width:98%;" placeholder="Patient Case History" list="case_history_eye_list" value="<?php echo $patient_case_history["case_history"]; ?>" autocomplete="on" />-->
					
					<textarea rows="5" name="case_history_eye" id="case_history_eye" placeholder="Patient Case History" style="width: 98%;resize: none;"><?php echo $patient_case_history["case_history"]; ?></textarea>
					
					<datalist id="case_history_eye_list"></datalist>
				</td>
			</tr>
			<tr>
				<th style="width: 140px;">History of Present Illness</th>
				<td colspan="7">
					<textarea rows="4" name="present_history" id="present_history" placeholder="Patient Present History" style="width: 98%;resize: none;"><?php echo $patient_eye_history["present_history"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th style="width: 140px;">History of Past Illness</th>
				<td colspan="7">
					<textarea rows="4" name="past_history" id="past_history" placeholder="Patient Past History" style="width: 98%;resize: none;"><?php echo $patient_eye_history["past_history"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th style="width: 140px;">Family History</th>
				<td colspan="7">
					<textarea rows="4" name="family_history" id="family_history" placeholder="Patient Family History" style="width: 98%;resize: none;"><?php echo $patient_eye_history["family_history"]; ?></textarea>
				</td>
			</tr>
			<tr style="display:none;">
				<th style="width: 140px;">Personal History</th>
				<td colspan="7">
					<textarea rows="4" name="personal_history" id="personal_history" placeholder="Patient Personal History" style="width: 98%;resize: none;"><?php echo $patient_eye_history["personal_history"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th style="width: 140px;">Birth History</th>
				<td colspan="7">
					<textarea rows="4" name="birth_history" id="birth_history" placeholder="Patient Birth History" style="width: 98%;resize: none;"><?php echo $patient_eye_history["birth_history"]; ?></textarea>
				</td>
			</tr>
			<tr style="display:none;">
				<th>Nutrition History</th>
				<td colspan="7">
					<textarea rows="4" name="nutrition_history" id="nutrition_history" placeholder="Patient Nutrition History" style="width: 98%;resize: none;"><?php echo $patient_eye_history["nutrition_history"]; ?></textarea>
				</td>
			</tr>
			<tr style="display:none;">
				<th>Pedigree Chart</th>
				<td colspan="7">
					<textarea rows="4" name="pedigree_chart" id="pedigree_chart" placeholder="Pedigree Chart" style="width: 98%;resize: none;"><?php echo $patient_eye_history["pedigree_chart"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th style="width: 140px;">Surgeries/Lasers</th>
				<td colspan="7">
					<textarea rows="4" name="surgeries_lasers" id="surgeries_lasers" placeholder="Patient Surgeries/Lasers History" style="width: 98%;resize: none;"><?php echo $patient_eye_history["surgeries_lasers"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th style="width: 140px;">Allergies</th>
				<td colspan="7">
					<textarea rows="4" name="allergies" id="allergies" placeholder="Patient Allergies" style="width: 98%;resize: none;"><?php echo $patient_eye_history["allergies"]; ?></textarea>
				</td>
			</tr>
			<tr style="display:none;">
				<th style="width: 140px;">Nutrition Status</th>
				<td colspan="7">
					<textarea rows="4" name="nutrition_status" id="nutrition_status" placeholder="Nutrition Status" style="width: 98%;resize: none;"><?php echo $patient_eye_history["nutrition_status"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th style="width: 140px;">Differently Able</th>
				<td colspan="7">
					<textarea rows="4" name="differently_able" id="differently_able" placeholder="Differently Able" style="width: 98%;resize: none;"><?php echo $patient_eye_history["differently_able"]; ?></textarea>
				</td>
			</tr>
			<tr style="display:none;">
				<th style="width: 140px;">General Examination</th>
				<td colspan="7">
					<textarea rows="4" name="general_examination" id="general_examination" placeholder="General Examination" style="width: 98%;resize: none;"><?php echo $patient_eye_history["general_examination"]; ?></textarea>
				</td>
			</tr>
			<tr style="display:none;">
				<th style="width: 140px;">Systemic Examination</th>
				<td colspan="7">
					<textarea rows="4" name="systemic_examination" id="systemic_examination" placeholder="Systemic Examination" style="width: 98%;resize: none;"><?php echo $patient_eye_history["systemic_examination"]; ?></textarea>
				</td>
			</tr>
			<tr style="display:none;">
				<th style="width: 140px;">Psychosocial Assessment</th>
				<td colspan="7">
					<textarea rows="4" name="psychosocial_assessment" id="psychosocial_assessment" placeholder="Psychosocial Assessment" style="width: 98%;resize: none;"><?php echo $patient_eye_history["psychosocial_assessment"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th colspan="8" style="text-align:center;background-color: #ddd;">Visual Acuity</th>
			</tr>
			<tr>
				<td colspan="8">
					<table class="table table-condensed table-bordered">
						<tr>
							<th></th>
							<th colspan="4" style="text-align:center;">Right Eye</th>
							<th colspan="4" style="text-align:center;">Left Eye</th>
						</tr>
						<tr>
							<th>Unaided</th>
							<th>Distance</th>
							<th>
								<input type="text" class="span2" name="unaided_right_distance" id="unaided_right_distance" value="<?php echo $patient_eye_visual["unaided_right_distance"]; ?>">
							</th>
							<th>Near</th>
							<th>
								<input type="text" class="span2" name="unaided_right_near" id="unaided_right_near" value="<?php echo $patient_eye_visual["unaided_right_near"]; ?>">
							</th>
							<th>Distance</th>
							<th>
								<input type="text" class="span2" name="unaided_left_distance" id="unaided_left_distance" value="<?php echo $patient_eye_visual["unaided_left_distance"]; ?>">
							</th>
							<th>Near</th>
							<th>
								<input type="text" class="span2" name="unaided_left_near" id="unaided_left_near" value="<?php echo $patient_eye_visual["unaided_left_near"]; ?>">
							</th>
						</tr>
						<tr>
							<th>Aided</th>
							<th>Distance</th>
							<th>
								<input type="text" class="span2" name="aided_right_distance" id="aided_right_distance" value="<?php echo $patient_eye_visual["aided_right_distance"]; ?>">
							</th>
							<th>Near</th>
							<th>
								<input type="text" class="span2" name="aided_right_near" id="aided_right_near" value="<?php echo $patient_eye_visual["aided_right_near"]; ?>">
							</th>
							<th>Distance</th>
							<th>
								<input type="text" class="span2" name="aided_left_distance" id="aided_left_distance" value="<?php echo $patient_eye_visual["aided_left_distance"]; ?>">
							</th>
							<th>Near</th>
							<th>
								<input type="text" class="span2" name="aided_left_near" id="aided_left_near" value="<?php echo $patient_eye_visual["aided_left_near"]; ?>">
							</th>
						</tr>
						<tr style="display:none;">
							<th>Miscellaneous</th>
							<th colspan="4">
								<input type="text" class="span4" name="miscellaneous_right" id="miscellaneous_right" value="<?php echo $patient_eye_visual["miscellaneous_right"]; ?>" style="width: 98%;" list="miscellaneous_list">
							</th>
							<th colspan="4">
								<input type="text" class="span4" name="miscellaneous_left" id="miscellaneous_left" value="<?php echo $patient_eye_visual["miscellaneous_left"]; ?>" style="width: 98%;" list="miscellaneous_list">
								<datalist id="miscellaneous_list">
									<option>DVA assessed with SLOAN optotype</option>
								</datalist>
							</th>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th colspan="8" style="text-align:center;background-color: #ddd;">Auto Refractometer</th>
			</tr>
			<tr>
				<td colspan="8">
					<table class="table table-condensed table-bordered">
						<tr>
							<th colspan="3" style="text-align:center;">Right Eye</th>
							<th colspan="3" style="text-align:center;">Left Eye</th>
						</tr>
						<tr>
							<th style="text-align:center;">Sph</th>
							<th style="text-align:center;">Cyl</th>
							<th style="text-align:center;">Axis</th>
							<th style="text-align:center;">Sph</th>
							<th style="text-align:center;">Cyl</th>
							<th style="text-align:center;">Axis</th>
						</tr>
						<tr>
							<td><input type="text" class="span2" name="fractometer_right_sph" id="fractometer_right_sph" value="<?php echo $patient_eye_fractometer["fractometer_right_sph"]; ?>"></td>
							<td><input type="text" class="span2" name="fractometer_right_cyl" id="fractometer_right_cyl" value="<?php echo $patient_eye_fractometer["fractometer_right_cyl"]; ?>"></td>
							<td><input type="text" class="span2" name="fractometer_right_axis" id="fractometer_right_axis" value="<?php echo $patient_eye_fractometer["fractometer_right_axis"]; ?>"></td>
							<td><input type="text" class="span2" name="fractometer_left_sph" id="fractometer_left_sph" value="<?php echo $patient_eye_fractometer["fractometer_left_sph"]; ?>"></td>
							<td><input type="text" class="span2" name="fractometer_left_cyl" id="fractometer_left_cyl" value="<?php echo $patient_eye_fractometer["fractometer_left_cyl"]; ?>"></td>
							<td><input type="text" class="span2" name="fractometer_left_axis" id="fractometer_left_axis" value="<?php echo $patient_eye_fractometer["fractometer_left_axis"]; ?>"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th colspan="8" style="text-align:center;background-color: #ddd;">Present Power</th>
			</tr>
			<tr>
				<td colspan="8">
					<table class="table table-condensed table-bordered">
						<tr>
							<th style="text-align:left;">Right Eye</th>
							<th style="text-align:left;">Left Eye</th>
						</tr>
						<tr>
							<td><input type="text" class="span6" name="present_power_right" id="present_power_right" value="<?php echo $patient_eye_present_power["present_power_right"]; ?>"></td>
							<td><input type="text" class="span6" name="present_power_left" id="present_power_left" value="<?php echo $patient_eye_present_power["present_power_left"]; ?>"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th colspan="8" style="text-align:center;background-color: #ddd;">Prescription For Glasses</th>
			</tr>
			<tr>
				<td colspan="8">
					<table class="table table-condensed table-bordered">
						<tr>
							<th></th>
							<th colspan="4" style="text-align:center;">Right Eye</th>
							<th colspan="4" style="text-align:center;">Left Eye</th>
						</tr>
						<tr>
							<th></th>
							<th style="text-align:center;">Sph</th>
							<th style="text-align:center;">Cyl</th>
							<th style="text-align:center;">Axis</th>
							<th style="text-align:center;">Vision</th>
							<th style="text-align:center;">Sph</th>
							<th style="text-align:center;">Cyl</th>
							<th style="text-align:center;">Axis</th>
							<th style="text-align:center;">Vision</th>
						</tr>
						<tr>
							<th>Distance</th>
							<td><input type="text" class="span_1half" name="distance_right_sph" id="distance_right_sph" value="<?php echo $patient_eye_prescribe_power["distance_right_sph"]; ?>"></td>
							<td><input type="text" class="span_1half" name="distance_right_cyl" id="distance_right_cyl" value="<?php echo $patient_eye_prescribe_power["distance_right_cyl"]; ?>"></td>
							<td><input type="text" class="span_1half" name="distance_right_axis" id="distance_right_axis" value="<?php echo $patient_eye_prescribe_power["distance_right_axis"]; ?>"></td>
							<td><input type="text" class="span_1half" name="distance_right_vision" id="distance_right_vision" value="<?php echo $patient_eye_prescribe_power["distance_right_vision"]; ?>"></td>
							<td><input type="text" class="span_1half" name="distance_left_sph" id="distance_left_sph" value="<?php echo $patient_eye_prescribe_power["distance_left_sph"]; ?>"></td>
							<td><input type="text" class="span_1half" name="distance_left_cyl" id="distance_left_cyl" value="<?php echo $patient_eye_prescribe_power["distance_left_cyl"]; ?>"></td>
							<td><input type="text" class="span_1half" name="distance_left_axis" id="distance_left_axis" value="<?php echo $patient_eye_prescribe_power["distance_left_axis"]; ?>"></td>
							<td><input type="text" class="span_1half" name="distance_left_vision" id="distance_left_vision" value="<?php echo $patient_eye_prescribe_power["distance_left_vision"]; ?>"></td>
						</tr>
						<tr>
							<th>Near</th>
							<td><input type="text" class="span_1half" name="near_right_sph" id="near_right_sph" value="<?php echo $patient_eye_prescribe_power["near_right_sph"]; ?>"></td>
							<td><input type="text" class="span_1half" name="near_right_cyl" id="near_right_cyl" value="<?php echo $patient_eye_prescribe_power["near_right_cyl"]; ?>"></td>
							<td><input type="text" class="span_1half" name="near_right_axis" id="near_right_axis" value="<?php echo $patient_eye_prescribe_power["near_right_axis"]; ?>"></td>
							<td><input type="text" class="span_1half" name="near_right_vision" id="near_right_vision" value="<?php echo $patient_eye_prescribe_power["near_right_vision"]; ?>"></td>
							<td><input type="text" class="span_1half" name="near_left_sph" id="near_left_sph" value="<?php echo $patient_eye_prescribe_power["near_left_sph"]; ?>"></td>
							<td><input type="text" class="span_1half" name="near_left_cyl" id="near_left_cyl" value="<?php echo $patient_eye_prescribe_power["near_left_cyl"]; ?>"></td>
							<td><input type="text" class="span_1half" name="near_left_axis" id="near_left_axis" value="<?php echo $patient_eye_prescribe_power["near_left_axis"]; ?>"></td>
							<td><input type="text" class="span_1half" name="near_left_vision" id="near_left_vision" value="<?php echo $patient_eye_prescribe_power["near_left_vision"]; ?>"></td>
						</tr>
						<tr>
							<th>Pupillary Distance</th>
							<td colspan="8"><input type="text" class="" name="pupillary_distance" id="pupillary_distance" value="<?php echo $patient_eye_prescribe_power["pupillary_distance"]; ?>" style="width:98%;"></td>
						</tr>
						<tr>
							<th>Remarks</th>
							<td colspan="8"><input type="text" class="" name="power_remarks" id="power_remarks" value="<?php echo $patient_eye_prescribe_power["power_remarks"]; ?>" style="width:98%;"></td>
						</tr>
						<tr>
							<th>Acceptance/Refraction</th>
							<td colspan="8">
								<textarea rows="4" name="power_refraction" id="power_refraction" placeholder="Acceptance/Refraction" style="width: 98%;resize: none;"><?php echo $patient_eye_prescribe_power["power_refraction"]; ?></textarea>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th colspan="8" style="text-align:center;background-color: #ddd;">External Examination</th>
			</tr>
			<tr>
				<td colspan="8">
					<table class="table table-condensed table-bordered">
						<tr>
							<th style="width: 135px;">Facial Symmetry</th>
							<td>
								<input type="text" name="facial_symmetry" id="facial_symmetry" placeholder="Facial Symmetry" value="<?php echo $patient_eye_external_exam["facial_symmetry"]; ?>" style="width: 95%;" list="external_exam_list">
							</td>
							<th style="width: 100px;">External Face</th>
							<td>
								<input type="text" name="external_face" id="external_face" placeholder="External Face" value="<?php echo $patient_eye_external_exam["external_face"]; ?>" style="width: 95%;" list="external_exam_list">
							</td>
						</tr>
						<tr>
							<th style="width: 100px;">Head Posture</th>
							<td>
								<input type="text" name="head_posture" id="head_posture" placeholder="Head Posture" value="<?php echo $patient_eye_external_exam["head_posture"]; ?>" style="width: 95%;" list="external_exam_list">
							</td>
							<th style="width: 135px;">Ocular Position</th>
							<td>
								<input type="text" name="ocular_position" id="ocular_position" placeholder="Ocular Position" value="<?php echo $patient_eye_external_exam["ocular_position"]; ?>" style="width: 95%;" list="external_exam_list">
							</td>
						</tr>
						<tr>
							<th style="width: 135px;">Ocular Alignment</th>
							<td>
								<input type="text" name="ocular_alignment" id="ocular_alignment" placeholder="Ocular Alignment" value="<?php echo $patient_eye_external_exam["ocular_alignment"]; ?>" style="width: 95%;" list="external_exam_list">
								<datalist id="external_exam_list">
									<option>Symmetrical on both sides</option>
									<option>Normal</option>
									<option>Epicanthal fold with telecanthus and broad nasal bridge</option>
									<option>HBT - Central</option>
								</datalist>
							</td>
							<td></td>
							<td></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th colspan="8" style="text-align:center;background-color: #ddd;">Ocular Motility</th>
			</tr>
			<tr>
				<td colspan="8">
					<table class="table table-condensed table-bordered">
						<tr>
							<th style="text-align:center;">Right Eye</th>
							<th style="text-align:center;">Left Eye</th>
						</tr>
						<tr>
							<th>
								<input type="text" name="ocular_motility_right" id="ocular_motility_right" value="<?php echo $patient_eye_external_exam["ocular_motility_right"]; ?>" style="width: 95%;" list="ocular_motility_list">
							</th>
							<th>
								<input type="text" name="ocular_motility_left" id="ocular_motility_left" value="<?php echo $patient_eye_external_exam["ocular_motility_left"]; ?>" style="width: 95%;" list="ocular_motility_list">
								<datalist id="ocular_motility_list">
									<option>Full and free in all gazes</option>
								</datalist>
							</th>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th colspan="8" style="text-align:center;background-color: #ddd;">Intraocular Pressure</th>
			</tr>
			<tr>
				<td colspan="8">
					<table class="table table-condensed table-bordered">
						<tr>
							<th style="text-align:center;">Right Eye</th>
							<th style="text-align:center;">Left Eye</th>
						</tr>
						<tr>
							<th>
								<input type="text" name="intraocular_pressure_right" id="intraocular_pressure_right" placeholder="mm of HG" value="<?php echo $patient_eye_external_exam["intraocular_pressure_right"]; ?>" style="width: 95%;" list="intraocular_pressure_list">
							</th>
							<th>
								<input type="text" name="intraocular_pressure_left" id="intraocular_pressure_left" placeholder="mm of HG" value="<?php echo $patient_eye_external_exam["intraocular_pressure_left"]; ?>" style="width: 95%;" list="intraocular_pressure_list">
								<datalist id="intraocular_pressure_list">
									<option>mm of HG</option>
								</datalist>
							</th>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th colspan="8" style="text-align:center;background-color: #ddd;">Slit Lamp Examination</th>
			</tr>
			<tr>
				<td colspan="8">
					<table class="table table-condensed table-bordered">
						<tr>
							<th style="width: 150px;"></th>
							<th style="text-align:center;">Right Eye</th>
							<th style="text-align:center;">Left Eye</th>
						</tr>
						<tr>
							<th>Eyelids</th>
							<th>
								<input type="text" name="eyelids_right" id="eyelids_right" value="<?php echo $patient_eye_lamp_exam["eyelids_right"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
							<th>
								<input type="text" name="eyelids_left" id="eyelids_left" value="<?php echo $patient_eye_lamp_exam["eyelids_left"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
						</tr>
						<tr>
							<th>Conjunctiva</th>
							<th>
								<input type="text" name="conjunctiva_right" id="conjunctiva_right" value="<?php echo $patient_eye_lamp_exam["conjunctiva_right"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
							<th>
								<input type="text" name="conjunctiva_left" id="conjunctiva_left" value="<?php echo $patient_eye_lamp_exam["conjunctiva_left"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
						</tr>
						<tr>
							<th>Sclera</th>
							<th>
								<input type="text" name="sclera_right" id="sclera_right" value="<?php echo $patient_eye_lamp_exam["sclera_right"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
							<th>
								<input type="text" name="sclera_left" id="sclera_left" value="<?php echo $patient_eye_lamp_exam["sclera_left"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
						</tr>
						<tr>
							<th>Cornea</th>
							<th>
								<input type="text" name="cornea_right" id="cornea_right" value="<?php echo $patient_eye_lamp_exam["cornea_right"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
							<th>
								<input type="text" name="cornea_left" id="cornea_left" value="<?php echo $patient_eye_lamp_exam["cornea"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
						</tr>
						<tr>
							<th>Interior Chamber</th>
							<th>
								<input type="text" name="interior_chamber_right" id="interior_chamber_right" value="<?php echo $patient_eye_lamp_exam["interior_chamber_right"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
							<th>
								<input type="text" name="interior_chamber_left" id="interior_chamber_left" value="<?php echo $patient_eye_lamp_exam["interior_chamber_left"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
						</tr>
						<tr>
							<th>Iris</th>
							<th>
								<input type="text" name="iris_right" id="iris_right" value="<?php echo $patient_eye_lamp_exam["iris_right"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
							<th>
								<input type="text" name="iris_left" id="iris_left" value="<?php echo $patient_eye_lamp_exam["iris_left"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
						</tr>
						<tr>
							<th>Pupil</th>
							<th>
								<input type="text" name="pupil_right" id="pupil_right" value="<?php echo $patient_eye_lamp_exam["pupil_right"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
							<th>
								<input type="text" name="pupil_left" id="pupil_left" value="<?php echo $patient_eye_lamp_exam["pupil_left"]; ?>" style="width: 95%;" list="lamp_exam_list">
							</th>
							<datalist id="lamp_exam_list">
								<option>Flat</option>
								<option>Normal</option>
								<option>Clear</option>
								<option>Normal color and pattern</option>
								<option>Round, Regular, Reacting</option>
								<option>Round, Regular, Reacting No RAPD</option>
								<option>Normal in contents and depth PACD</option>
							</datalist>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th colspan="8" style="text-align:center;background-color: #ddd;">Diagnosis</th>
			</tr>
			<tr>
				<td colspan="8">
					<table class="table table-condensed table-bordered">
						<tr>
							<th style="text-align:center;">Right Eye</th>
							<th style="text-align:center;">Left Eye</th>
						</tr>
						<tr>
							<th>
								<textarea rows="4" name="diagnosis_right" id="diagnosis_right" placeholder="Patient Right Eye Diagnosis" style="width: 98%;resize: none;"><?php echo $patient_eye_lamp_exam["diagnosis_right"]; ?></textarea>
							</th>
							<th>
								<textarea rows="4" name="diagnosis_left" id="diagnosis_left" placeholder="Patient Left Eye Diagnosis" style="width: 98%;resize: none;"><?php echo $patient_eye_lamp_exam["diagnosis_left"]; ?></textarea>
							</th>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th style="width: 140px;">Plan of Management</th>
				<td colspan="7">
					<textarea rows="6" name="management_plan" id="management_plan" placeholder="Plan of Management" style="width: 98%;resize: none;"><?php echo $patient_eye_history["management_plan"]; ?></textarea>
				</td>
			</tr>
		</table>
		<input type="hidden" name="eye_patient_id" id="eye_patient_id" value="<?php echo $uhid; ?>">
		<input type="hidden" name="eye_opd_id" id="eye_opd_id" value="<?php echo $opd_id; ?>">
		<input type="hidden" name="eye_consultantdoctorid" id="eye_consultantdoctorid" value="<?php echo $consultantdoctorid; ?>">
	</form>
	<br>
	<center>
		<button class="btn btn-save" id="save_ophthalmology_btn" onclick="save_ophthalmology()"><i class="icon-save"></i> Save</button>
		
		<button class="btn btn-print" onclick="eye_prescription()"><i class="icon-print"></i> Prescription For Glasses</button>
	</center>
	<br>
	<br>
	<br>
</div>

<script>
	var _changeInterval = null;
	function case_history_eye_up(val,e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_case_history_eye(val,e);
			}, 300);
		}
	}
	function load_case_history_eye(val,e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_case_history",
			val:val,
		},
		function(data,status)
		{
			$("#case_history_eye_list").html(data);
		})
	}
	function save_ophthalmology()
	{
		$("#loader").show();
		var input = document.getElementById("patient_ophthalmology_form");
		formData= new FormData(input);
		$.ajax({
			url: "pages/patient_ophthalmology_save.php",    // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: formData,       // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{
				$("#loader").hide();
				//alert(data);
				bootbox.dialog({ message: "<h4>"+data+"</h4>"});
				setTimeout(function()
				{
					bootbox.hideAll();
				}, 2000);
			},
			error: function (err) {
				alert(err);
			}
		});
	}
	
	function eye_prescription()
	{
		var url="pages/eye_prescription.php";
		
		var uhid=$("#sel_uhid").val();
		url=url+"?v="+btoa(1234567890)+"&uhid="+btoa(uhid);
		
		var opd_id=$("#sel_opd_id").val();
		url=url+"&opdid="+btoa(opd_id);
		
		var user=$("#user").text().trim();
		url=url+"&user="+btoa(user);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
.span_1half
{
	width: 100px;
}
</style>
