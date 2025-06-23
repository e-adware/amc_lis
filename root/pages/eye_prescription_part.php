<?php
$patient_eye_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_history` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_eye_visual=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_visual` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_eye_fractometer=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_fractometer` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_eye_present_power=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_present_power` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_eye_external_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_external_exam` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$patient_eye_lamp_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_lamp_exam` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
?>

<?php
	if($patient_eye_history["present_history"])
	{
?>
	<div>
		<b>History of Present Illness: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["present_history"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["past_history"])
	{
?>
	<div>
		<b>History of Past Illness: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["past_history"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["family_history"])
	{
?>
	<div>
		<b>Family History: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["family_history"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["personal_history"])
	{
?>
	<div>
		<b>Personal History: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["personal_history"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["birth_history"])
	{
?>
	<div>
		<b>Birth History: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["birth_history"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["nutrition_history"])
	{
?>
	<div>
		<b>Nutrition History: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["nutrition_history"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["pedigree_chart"])
	{
?>
	<div>
		<b>Pedigree Chart: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["pedigree_chart"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["surgeries_lasers"])
	{
?>
	<div>
		<b>Surgeries/Lasers: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["surgeries_lasers"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["allergies"])
	{
?>
	<div>
		<b>Allergies: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["allergies"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["nutrition_status"])
	{
?>
	<div>
		<b>Nutrition Status: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["nutrition_status"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["differently_able"])
	{
?>
	<div>
		<b>Differently Able: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["differently_able"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["general_examination"])
	{
?>
	<div>
		<b>General Examination: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["general_examination"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["systemic_examination"])
	{
?>
	<div>
		<b>Systemic Examination: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["systemic_examination"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["psychosocial_assessment"])
	{
?>
	<div>
		<b>Psychosocial Assessment: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["psychosocial_assessment"]);
			?>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_visual)
	{
?>
	<div>
		<b>Visual Acuity: </b><br>
		<div class="results">
			<table class="table table-condensed table-bordered">
				<tr>
					<th></th>
					<th colspan="4" style="text-align:center;">Right Eye</th>
					<th colspan="4" style="text-align:center;">Left Eye</th>
				</tr>
		<?php
			if($patient_eye_visual["unaided_right_distance"] || $patient_eye_visual["unaided_right_near"] || $patient_eye_visual["unaided_left_distance"] || $patient_eye_visual["unaided_left_near"])
			{
		?>
				<tr>
					<th>Unaided</th>
					<td>Distance</td>
					<td>
						<?php echo $patient_eye_visual["unaided_right_distance"]; ?>
					</td>
					<td>Near</td>
					<td>
						<?php echo $patient_eye_visual["unaided_right_near"]; ?>
					</td>
					<td>Distance</td>
					<td>
						<?php echo $patient_eye_visual["unaided_left_distance"]; ?>
					</td>
					<td>Near</td>
					<td>
						<?php echo $patient_eye_visual["unaided_left_near"]; ?>
					</td>
				</tr>
		<?php
			}
		?>
		<?php
			if($patient_eye_visual["aided_right_distance"] || $patient_eye_visual["aided_right_near"] || $patient_eye_visual["aided_left_distance"] || $patient_eye_visual["aided_left_near"])
			{
		?>
				<tr>
					<th>Aided</th>
					<td>Distance</td>
					<td>
						<?php echo $patient_eye_visual["aided_right_distance"]; ?>
					</td>
					<td>Near</td>
					<td>
						<?php echo $patient_eye_visual["aided_right_near"]; ?>
					</td>
					<td>Distance</td>
					<td>
						<?php echo $patient_eye_visual["aided_left_distance"]; ?>
					</td>
					<td>Near</td>
					<td>
						<?php echo $patient_eye_visual["aided_left_near"]; ?>
					</td>
				</tr>
		<?php
			}
		?>
		<?php
			if($patient_eye_visual["miscellaneous_right"] || $patient_eye_visual["miscellaneous_left"])
			{
		?>
				<tr>
					<th>Miscellaneous</th>
					<td colspan="4">
						<?php echo $patient_eye_visual["miscellaneous_right"]; ?>
					</td>
					<td colspan="4">
						<?php echo $patient_eye_visual["miscellaneous_left"]; ?>
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
?>
<?php
	if($patient_eye_fractometer)
	{
?>
	<div>
		<b>Auto Refractometer: </b><br>
		<div class="results">
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
					<td style="text-align:center;"><?php echo $patient_eye_fractometer["fractometer_right_sph"]; ?></td>
					<td style="text-align:center;"><?php echo $patient_eye_fractometer["fractometer_right_cyl"]; ?></td>
					<td style="text-align:center;"><?php echo $patient_eye_fractometer["fractometer_right_axis"]; ?></td>
					<td style="text-align:center;"><?php echo $patient_eye_fractometer["fractometer_left_sph"]; ?></td>
					<td style="text-align:center;"><?php echo $patient_eye_fractometer["fractometer_left_cyl"]; ?></td>
					<td style="text-align:center;"><?php echo $patient_eye_fractometer["fractometer_left_axis"]; ?></td>
				</tr>
			</table>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_present_power)
	{
?>
	<div>
		<b>Present Power: </b><br>
		<div class="results">
			<table class="table table-condensed table-bordered">
				<tr>
					<th style="text-align:center;">Right Eye</th>
					<th style="text-align:center;">Left Eye</th>
				</tr>
				<tr>
					<td style="text-align:center;"><?php echo $patient_eye_present_power["present_power_right"]; ?></td>
					<td style="text-align:center;"><?php echo $patient_eye_present_power["present_power_left"]; ?></td>
				</tr>
			</table>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_external_exam["facial_symmetry"] || $patient_eye_external_exam["external_face"] || $patient_eye_external_exam["head_posture"] || $patient_eye_external_exam["ocular_position"] || $patient_eye_external_exam["ocular_alignment"])
	{
?>
	<div>
		<b>External Examination: </b><br>
		<div class="results">
			<table class="table table-condensed">
			<?php if($patient_eye_external_exam["facial_symmetry"]){ ?>
				<tr>
					<th style="width: 110px;">Facial Symmetry <b style="float:right;">:</b></th>
					<td style="padding: 0 0 0 5px;">
						<?php echo $patient_eye_external_exam["facial_symmetry"]; ?>
					</td>
				</tr>
			<?php } ?>
			<?php if($patient_eye_external_exam["external_face"]){ ?>
				<tr>
					<th>External Face <b style="float:right;">:</b></th>
					<td style="padding: 0 0 0 5px;">
						<?php echo $patient_eye_external_exam["external_face"]; ?>
					</td>
				</tr>
			<?php } ?>
			<?php if($patient_eye_external_exam["head_posture"]){ ?>
				<tr>
					<th>Head Posture <b style="float:right;">:</b></th>
					<td style="padding: 0 0 0 5px;">
						<?php echo $patient_eye_external_exam["head_posture"]; ?>
					</td>
				</tr>
			<?php } ?>
			<?php if($patient_eye_external_exam["ocular_position"]){ ?>
				<tr>
					<th>Ocular Position <b style="float:right;">:</b></th>
					<td style="padding: 0 0 0 5px;">
						<?php echo $patient_eye_external_exam["ocular_position"]; ?>
					</td>
				</tr>
			<?php } ?>
			<?php if($patient_eye_external_exam["ocular_alignment"]){ ?>
				<tr>
					<th>Ocular Alignment <b style="float:right;">:</b></th>
					<td style="padding: 0 0 0 5px;">
						<?php echo $patient_eye_external_exam["ocular_alignment"]; ?>
					</td>
				</tr>
			<?php } ?>
			</table>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_external_exam["ocular_motility_right"] || $patient_eye_external_exam["ocular_motility_left"])
	{
?>
	<div>
		<b>Ocular Motility: </b><br>
		<div class="results">
			<table class="table table-condensed">
				<tr>
					<th style="text-align:left;">Right Eye</th>
					<th style="text-align:left;">Left Eye</th>
				</tr>
				<tr>
					<td>
						<?php echo $patient_eye_external_exam["ocular_motility_right"]; ?>
					</td>
					<td>
						<?php echo $patient_eye_external_exam["ocular_motility_left"]; ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
<?php
	}
	if($patient_eye_external_exam["intraocular_pressure_right"] || $patient_eye_external_exam["intraocular_pressure_left"])
	{
?>
	<div>
		<b>Intraocular Pressure: </b><br>
		<div class="results">
			<table class="table table-condensed">
				<tr>
					<th style="text-align:left;">Right Eye</th>
					<th style="text-align:left;">Left Eye</th>
				</tr>
				<tr>
					<td>
						<?php echo $patient_eye_external_exam["intraocular_pressure_right"]; ?>
					</td>
					<td>
						<?php echo $patient_eye_external_exam["intraocular_pressure_left"]; ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
<?php
	}
?>

<?php
	if($patient_eye_lamp_exam)
	{
?>
	<div>
		<b>Slit Lamp Examination: </b><br>
		<div class="results">
			<table class="table table-condensed table-bordered">
				<tr>
					<th></th>
					<th style="text-align:left;">Right Eye</th>
					<th style="text-align:left;">Left Eye</th>
				</tr>
		<?php
			if($patient_eye_lamp_exam["eyelids_right"] || $patient_eye_lamp_exam["eyelids_left"])
			{
		?>
				<tr>
					<th>Eyelids</th>
					<td>
						<?php echo $patient_eye_lamp_exam["eyelids_right"]; ?>
					</td>
					<td>
						<?php echo $patient_eye_lamp_exam["eyelids_left"]; ?>
					</td>
				</tr>
		<?php
			}
		?>
		<?php
			if($patient_eye_lamp_exam["conjunctiva_right"] || $patient_eye_lamp_exam["conjunctiva_left"])
			{
		?>
				<tr>
					<th>Conjunctiva</th>
					<td>
						<?php echo $patient_eye_lamp_exam["conjunctiva_right"]; ?>
					</td>
					<td>
						<?php echo $patient_eye_lamp_exam["conjunctiva_left"]; ?>
					</td>
				</tr>
		<?php
			}
		?>
		<?php
			if($patient_eye_lamp_exam["sclera_right"] || $patient_eye_lamp_exam["sclera_left"])
			{
		?>
				<tr>
					<th>Sclera</th>
					<td>
						<?php echo $patient_eye_lamp_exam["sclera_right"]; ?>
					</td>
					<td>
						<?php echo $patient_eye_lamp_exam["sclera_left"]; ?>
					</td>
				</tr>
		<?php
			}
		?>
		<?php
			if($patient_eye_lamp_exam["cornea_right"] || $patient_eye_lamp_exam["cornea_left"])
			{
		?>
				<tr>
					<th>Cornea</th>
					<td>
						<?php echo $patient_eye_lamp_exam["cornea_right"]; ?>
					</td>
					<td>
						<?php echo $patient_eye_lamp_exam["cornea_left"]; ?>
					</td>
				</tr>
		<?php
			}
		?>
		<?php
			if($patient_eye_lamp_exam["interior_chamber_right"] || $patient_eye_lamp_exam["interior_chamber_left"])
			{
		?>
				<tr>
					<th>Interior Chamber</th>
					<td>
						<?php echo $patient_eye_lamp_exam["interior_chamber_right"]; ?>
					</td>
					<td>
						<?php echo $patient_eye_lamp_exam["interior_chamber_left"]; ?>
					</td>
				</tr>
		<?php
			}
		?>
		<?php
			if($patient_eye_lamp_exam["iris_right"] || $patient_eye_lamp_exam["iris_left"])
			{
		?>
				<tr>
					<th>Iris</th>
					<td>
						<?php echo $patient_eye_lamp_exam["iris_right"]; ?>
					</td>
					<td>
						<?php echo $patient_eye_lamp_exam["iris_left"]; ?>
					</td>
				</tr>
		<?php
			}
		?>
		<?php
			if($patient_eye_lamp_exam["pupil_right"] || $patient_eye_lamp_exam["pupil_left"])
			{
		?>
				<tr>
					<th>Pupil</th>
					<td>
						<?php echo $patient_eye_lamp_exam["pupil_right"]; ?>
					</td>
					<td>
						<?php echo $patient_eye_lamp_exam["pupil_left"]; ?>
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
?>

<?php
	if($patient_eye_lamp_exam["diagnosis_right"] || $patient_eye_lamp_exam["diagnosis_left"])
	{
?>
	<div>
		<b>Diagnosis: </b><br>
		<div class="results">
			<table class="table table-condensed">
			<?php if($patient_eye_lamp_exam["diagnosis_right"]){ ?>
				<tr>
					<th style="width: 70px;">Right Eye<b style="float:right;">:</b></th>
					<td style="padding: 0 0 0 5px;">
						<?php echo nl2br($patient_eye_lamp_exam["diagnosis_right"]); ?>
					</td>
				</tr>
			<?php } ?>
			<?php if($patient_eye_lamp_exam["diagnosis_left"]){ ?>
				<tr>
					<th style="width: 70px;">Left Eye<b style="float:right;">:</b></th>
					<td style="padding: 0 0 0 5px;">
						<?php echo nl2br($patient_eye_lamp_exam["diagnosis_left"]); ?>
					</td>
				</tr>
			<?php } ?>
			</table>
		</div>
	</div>
<?php
	}
?>
<?php
	if($patient_eye_history["management_plan"])
	{
?>
	<div>
		<b>Plan of Management: </b><br>
		<div class="results">
			<?php
				echo nl2br($patient_eye_history["management_plan"]);
			?>
		</div>
	</div>
<?php
	}
?>
