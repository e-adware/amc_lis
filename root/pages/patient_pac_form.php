<style>
	input[type="radio"], input[type="checkbox"]
	{
		width: 17px;
		height: 17px;
	}
</style>
<?php
	$patient_pac_details=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_details_cd=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_cd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_details_rd=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_rd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_details_nd=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_nd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_details_dtkg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_dtkg` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_details_bd=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_bd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_details_ed=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_ed` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_details_women=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_women` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_details_ari=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_ari` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_details_drug=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_drug` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_details_intubation=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_intubation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_asa_grade=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_asa_grade` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$patient_pac_plan=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_plan` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	
	$print_btn_display="display:none;";
	if($patient_pac_details)
	{
		$print_btn_display="";
	}
?>
<div style="max-height: 540px;overflow-y: scroll;">
	<form role="form" id="patient_pac_form" method="post" enctype="multipart/form-data">
		<table class="table table-condensed">
			<tr>
				<th style="width: 280px;">Procedure Proposed <b style="color:#ff0000;">*</b> <span style="float:right;">:</span></th>
				<td>
					<input type="text" class="span8" name="propose_procedure" id="propose_procedure" onKeyUp="propose_procedure_up(this.value,event)" style="width:98%;" placeholder="Procedure Proposed" list="propose_procedure_list" value="<?php echo $patient_pac_details["propose_procedure"]; ?>" />
					<datalist id="propose_procedure_list"></datalist>
				</td>
			</tr>
			<tr>
				<th>Previous Surgeries &amp; Anesthesia <span style="float:right;">:</span></th>
				<td>
					<textarea rows="5" name="previous_surgeries" id="previous_surgeries" placeholder="Previous Surgeries &amp; Anesthesia" style="width:98%;resize: none;"><?php echo $patient_pac_details["previous_surgeries"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th>Prescribed Medications <span style="float:right;">:</span></th>
				<td>
					<textarea rows="5" name="prescribed_medication" id="prescribed_medication" placeholder="Prescribed Medications" style="width:98%;resize: none;"><?php echo $patient_pac_details["prescribed_medication"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th><label for="blood_thinner"><b>Blood thinners last taken <span style="float:right;">:</span></b></label></th>
				<td>
					<label for="blood_thinner">
						<!--<input type="checkbox" name="blood_thinner" id="blood_thinner" value="1" <?php if($patient_pac_details["blood_thinner"]==1){ echo "checked"; } ?>>-->
						<input type="text" name="blood_thinner" id="blood_thinner" value="<?php echo $patient_pac_details["blood_thinner"]?>" style="width: 185px;">
					</label>
				</td>
			</tr>
			<tr>
				<th>Food &amp; Drugs allergies / Reactions <span style="float:right;">:</span></th>
				<td>
					<input type="text" class="span8" name="food_allergies" id="food_allergies" onKeyUp="food_allergies_up(this.value,event)" style="width:98%;" placeholder="Food &amp; Drugs allergies / Reactions" list="food_allergies_list" value="<?php echo $patient_pac_details["food_allergies"]; ?>" />
					<datalist id="food_allergies_list"></datalist>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="span5" style="width:49%;margin-left:0;">
						<table class="table table-condensed">
							<tr>
								<th>Cardiovascular Disease</th>
							</tr>
							<tr>
								<td>
									<label for="chest_pain">
										<input type="checkbox" name="chest_pain" id="chest_pain" value="1" <?php if($patient_pac_details_cd["chest_pain"]==1){ echo "checked"; } ?>>
										Chest Pain/Tightness/Pressure/Heart Attack
									</label>
									<label for="irregu_hear_beat">
										<input type="checkbox" name="irregu_hear_beat" id="irregu_hear_beat" value="1" <?php if($patient_pac_details_cd["irregu_hear_beat"]==1){ echo "checked"; } ?>>
										Irregular Heart Beat
									</label>
									<label for="pacemeaker">
										<input type="checkbox" name="pacemeaker" id="pacemeaker" value="1" <?php if($patient_pac_details_cd["pacemeaker"]==1){ echo "checked"; } ?>>
										Pacemaker/Defibrillator Brand &nbsp; <input type="text" name="pacemeaker_brand" id="pacemeaker_brand" value="<?php echo $patient_pac_details_cd["pacemeaker_brand"]?>" style="width: 380px;">
									</label>
									<label for="problem_circulation">
										<input type="checkbox" name="problem_circulation" id="problem_circulation" value="1" <?php if($patient_pac_details_cd["problem_circulation"]==1){ echo "checked"; } ?>>
										Problem with Circulation
									</label>
									<label for="blood_clot">
										<input type="checkbox" name="blood_clot" id="blood_clot" value="1" <?php if($patient_pac_details_cd["blood_clot"]==1){ echo "checked"; } ?>>
										Blood Clot in legs or lungs
									</label>
									<label for="high_bp">
										<input type="checkbox" name="high_bp" id="high_bp" value="1" <?php if($patient_pac_details_cd["high_bp"]==1){ echo "checked"; } ?>>
										High Blood Pressure
									</label>
									<label for="cd_other">
										Other &nbsp; <input type="text" name="cd_other_details" id="cd_other_details" value="<?php echo $patient_pac_details_cd["cd_other_details"]?>" style="width: 570px;">
									</label>
								</td>
							</tr>
							<tr>
								<th>Respiratory Disease</th>
							</tr>
							<tr>
								<td>
									<label for="smoking">
										<input type="checkbox" name="smoking" id="smoking" value="1" <?php if($patient_pac_details_rd["smoking"]==1){ echo "checked"; } ?>>
										Smoking &nbsp; <input type="text" name="smoking_pack" id="smoking_pack" value="<?php echo $patient_pac_details_rd["smoking_pack"]?>" style="width: 200px;"> packs per day.
										 Quit &nbsp; <input type="text" name="smoking_quit" id="smoking_quit" value="<?php echo $patient_pac_details_rd["smoking_quit"]?>" style="width: 200px;" list="smoking_quit_list">
										 <datalist id="smoking_quit_list">
											<option>Yes</option>
											<option>No</option>
										 </datalist>
									</label>
									<label for="asthma">
										<input type="checkbox" name="asthma" id="asthma" value="1" <?php if($patient_pac_details_rd["asthma"]==1){ echo "checked"; } ?>>
										Asthma
									</label>
									<label for="emphysema">
										<input type="checkbox" name="emphysema" id="emphysema" value="1" <?php if($patient_pac_details_rd["emphysema"]==1){ echo "checked"; } ?>>
										Emphysema/Brochitis
									</label>
									<label for="breath_shortness">
										<input type="checkbox" name="breath_shortness" id="breath_shortness" value="1" <?php if($patient_pac_details_rd["breath_shortness"]==1){ echo "checked"; } ?>>
										Shortness of breath at rest
									</label>
									<label for="respiratory_infection">
										<input type="checkbox" name="respiratory_infection" id="respiratory_infection" value="1" <?php if($patient_pac_details_rd["respiratory_infection"]==1){ echo "checked"; } ?>>
										Upper respiratory infection(cold) within 2 weeks
									</label>
									<label for="sleep_apnea" style="display: inline;">
										<input type="checkbox" name="sleep_apnea" id="sleep_apnea" value="1" <?php if($patient_pac_details_rd["sleep_apnea"]==1){ echo "checked"; } ?>>
										Sleep apnea &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</label>
									<label for="cpap" style="display: inline;">
										<input type="checkbox" name="cpap" id="cpap" value="1" <?php if($patient_pac_details_rd["cpap"]==1){ echo "checked"; } ?>>
										Use CPAP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</label>
									<label for="tb" style="display: inline;">
										<input type="checkbox" name="tb" id="tb" value="1" <?php if($patient_pac_details_rd["tb"]==1){ echo "checked"; } ?>>
										TB
									</label>
								</td>
							</tr>
							<tr>
								<th>Neurological Disease</th>
							</tr>
							<tr>
								<td>
									<label for="stroke">
										<input type="checkbox" name="stroke" id="stroke" value="1" <?php if($patient_pac_details_nd["stroke"]==1){ echo "checked"; } ?>>
										Stroke or mini-stroke (T.I.A.)
									</label>
									<label for="seizures">
										<input type="checkbox" name="seizures" id="seizures" value="1" <?php if($patient_pac_details_nd["seizures"]==1){ echo "checked"; } ?>>
										Seizures
									</label>
									<label for="back_neck">
										<input type="checkbox" name="back_neck" id="back_neck" value="1" <?php if($patient_pac_details_nd["back_neck"]==1){ echo "checked"; } ?>>
										Back or neck problems
									</label>
									<label for="phys_resctriction">
										<input type="checkbox" name="phys_resctriction" id="phys_resctriction" value="1" <?php if($patient_pac_details_nd["phys_resctriction"]==1){ echo "checked"; } ?>>
										Physical restrictions/limitations
									</label>
									<label for="memory_loss">
										<input type="checkbox" name="memory_loss" id="memory_loss" value="1" <?php if($patient_pac_details_nd["memory_loss"]==1){ echo "checked"; } ?>>
										Forgetfullness / Memory loss / Confusion
									</label>
									<label for="sclerosis">
										<input type="checkbox" name="sclerosis" id="sclerosis" value="1" <?php if($patient_pac_details_nd["sclerosis"]==1){ echo "checked"; } ?>>
										Multiple sclerosis / muscular dystrophy
									</label>
									<label for="nerve_injury">
										<input type="checkbox" name="nerve_injury" id="nerve_injury" value="1" <?php if($patient_pac_details_nd["nerve_injury"]==1){ echo "checked"; } ?>>
										Nerve / Spinal cord injury
									</label>
									<label for="neuropathy">
										<input type="checkbox" name="neuropathy" id="neuropathy" value="1" <?php if($patient_pac_details_nd["neuropathy"]==1){ echo "checked"; } ?>>
										Neuropathy
									</label>
								</td>
							</tr>
							<tr>
								<th>Diabetes Disease</th>
							</tr>
							<tr>
								<td>
									<label for="diabetes" style="display: inline;">
										<input type="checkbox" name="diabetes" id="diabetes" value="1" <?php if($patient_pac_details_dtkg["diabetes"]==1){ echo "checked"; } ?>>
										Diabetes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</label>
									<label for="insulin" style="display: inline;">
										<input type="checkbox" name="insulin" id="insulin" value="1" <?php if($patient_pac_details_dtkg["insulin"]==1){ echo "checked"; } ?>>
										Taking insulin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</label>
									<label for="oha" style="display: inline;">
										<input type="checkbox" name="oha" id="oha" value="1" <?php if($patient_pac_details_dtkg["oha"]==1){ echo "checked"; } ?>>
										OHA
									</label>
									<label for="thyroid">
										<input type="checkbox" name="thyroid" id="thyroid" value="1" <?php if($patient_pac_details_dtkg["thyroid"]==1){ echo "checked"; } ?>>
										<b>Thyroid disorder</b>  &nbsp; If yes, Specify <input type="text" name="thyroid_disorder_details" id="thyroid_disorder_details" value="<?php echo $patient_pac_details_dtkg["thyroid_disorder_details"]?>" style="width:345px;">
									</label>
									<label for="kidney_disorder">
										<input type="checkbox" name="kidney_disorder" id="kidney_disorder" value="1" <?php if($patient_pac_details_dtkg["kidney_disorder"]==1){ echo "checked"; } ?>>
										<b>Kidney/Bladder/Prostrate Disorder</b>  &nbsp; If yes, Specify <input type="text" name="kidney_disorder_details" id="kidney_disorder_details" value="<?php echo $patient_pac_details_dtkg["kidney_disorder_details"]?>" style="width:345px;">
									</label>
									<label for="urinate">
										<input type="checkbox" name="urinate" id="urinate" value="1" <?php if($patient_pac_details_dtkg["urinate"]==1){ echo "checked"; } ?>>
										Inability to urinate after anesthesia
									</label>
									<label for="dialysis_schedule">
										<input type="checkbox" name="dialysis_schedule" id="dialysis_schedule" value="1" <?php if($patient_pac_details_dtkg["dialysis_schedule"]==1){ echo "checked"; } ?>>
										Dialysis : Schedule <input type="text" name="dialysis_schedule_details" id="dialysis_schedule_details" value="<?php echo $patient_pac_details_dtkg["dialysis_schedule_details"]?>" style="width:597px;">
									</label>
								</td>
							</tr>
							<tr>
								<th>Gastro-Intestinal Disease</th>
							</tr>
							<tr>
								<td>
									<label for="jaundice">
										<input type="checkbox" name="jaundice" id="jaundice" value="1" <?php if($patient_pac_details_dtkg["jaundice"]==1){ echo "checked"; } ?>>
										Liver Disease (Jaundice/Hepatitis)
									</label>
									<label for="hepatitis" style="display:none;">
										<input type="checkbox" name="hepatitis" id="hepatitis" value="1" <?php if($patient_pac_details_dtkg["hepatitis"]==1){ echo "checked"; } ?>>
										Liver Disease (Hepatitis)
									</label>
									<label for="hernia">
										<input type="checkbox" name="hernia" id="hernia" value="1" <?php if($patient_pac_details_dtkg["hernia"]==1){ echo "checked"; } ?>>
										Hiatal hernia / reflux / heartburn
									</label>
									<label for="gastro_other">
										Other <input type="text" name="gastro_other_details" id="gastro_other_details" value="<?php echo $patient_pac_details_dtkg["gastro_other_details"]?>" style="width:695px;">
									</label>
								</td>
							</tr>
						</table>
					</div>
					<div class="span5" style="width:49%;margin-left:0;">
						<table class="table table-condensed">
							<tr>
								<th>Blood Disorder</th>
							</tr>
							<tr>
								<td>
									<label for="bleeding_tendency">
										<input type="checkbox" name="bleeding_tendency" id="bleeding_tendency" value="1" <?php if($patient_pac_details_bd["bleeding_tendency"]==1){ echo "checked"; } ?>>
										Abnormal bleeding tendency or taking blood tinners
									</label>
									<label for="cell_disease">
										<input type="checkbox" name="cell_disease" id="cell_disease" value="1" <?php if($patient_pac_details_bd["cell_disease"]==1){ echo "checked"; } ?>>
										Sickle cell disease or trait
									</label>
									<label for="blood_transfusion">
										<input type="checkbox" name="blood_transfusion" id="blood_transfusion" value="1" <?php if($patient_pac_details_bd["blood_transfusion"]==1){ echo "checked"; } ?>>
										History of blood transfusions
									</label>
									<label for="blood_transfusion_objection">
										<input type="checkbox" name="blood_transfusion_objection" id="blood_transfusion_objection" value="1" <?php if($patient_pac_details_bd["blood_transfusion_objection"]==1){ echo "checked"; } ?>>
										Religious or other objections to blood transfusion
									</label>
									<label for="hcv" style="display: inline;">
										<input type="checkbox" name="hcv" id="hcv" value="1" <?php if($patient_pac_details_bd["hcv"]==1){ echo "checked"; } ?>>
										HCV
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<label for="hbsag" style="display: inline;">
										<input type="checkbox" name="hbsag" id="hbsag" value="1" <?php if($patient_pac_details_bd["hbsag"]==1){ echo "checked"; } ?>>
										HBsAg
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<label for="hiv" style="display: inline;">
										<input type="checkbox" name="hiv" id="hiv" value="1" <?php if($patient_pac_details_bd["hiv"]==1){ echo "checked"; } ?>>
										HIV
									</label>
								</td>
							</tr>
							<tr>
								<td>
									<label for="eye_disorder">
										<input type="checkbox" name="eye_disorder" id="eye_disorder" value="1" <?php if($patient_pac_details_ed["eye_disorder"]==1){ echo "checked"; } ?>>
										<b>Eye Disorder/Glaucoma/Retinal detachment</b>
									</label>
								</td>
							</tr>
							<tr>
								<td>
									<label for="ear_disorder">
										<input type="checkbox" name="ear_disorder" id="ear_disorder" value="1" <?php if($patient_pac_details_ed["ear_disorder"]==1){ echo "checked"; } ?>>
										<b>Ear Disorder/Ringing in ear/Hearing loss</b>
									</label>
								</td>
							</tr>
							<tr>
								<td>
									<label for="cancer_therapy">
										<input type="checkbox" name="cancer_therapy" id="cancer_therapy" value="1" <?php if($patient_pac_details_ed["cancer_therapy"]==1){ echo "checked"; } ?>>
										<b>Cancer/Chemotherapy/Radioation Therapy</b> &nbsp; If yes, Specify <input type="text" name="cancer_therapy_reason" id="cancer_therapy_reason" value="<?php echo $patient_pac_details_ed["cancer_therapy_reason"]?>" style="width:310px;">
									</label>
								</td>
							</tr>
							<tr>
								<td>
									<label for="psychiatric_disorder">
										<input type="checkbox" name="psychiatric_disorder" id="psychiatric_disorder" value="1" <?php if($patient_pac_details_ed["psychiatric_disorder"]==1){ echo "checked"; } ?>>
										<b>Psychiatric disorder</b> &nbsp; If yes, Specify <input type="text" name="psychiatric_disorder_reason" id="psychiatric_disorder_reason" value="<?php echo $patient_pac_details_ed["psychiatric_disorder_reason"]?>" style="width:488px;">
									</label>
								</td>
							</tr>
							<tr>
								<td>
									<label for="other_disease">
										<input type="checkbox" name="other_disease" id="other_disease" value="1" <?php if($patient_pac_details_ed["other_disease"]==1){ echo "checked"; } ?>>
										<b>Other illness or disease</b> &nbsp; If yes, Specify <input type="text" name="other_disease_name" id="other_disease_name" value="<?php echo $patient_pac_details_ed["other_disease_name"]?>" style="width:460px;">
									</label>
								</td>
							</tr>
							<tr>
								<th>For Women</th>
							</tr>
							<tr>
								<td>
									<label for="pregnant">
										<input type="checkbox" name="pregnant" id="pregnant" value="1" <?php if($patient_pac_details_women["pregnant"]==1){ echo "checked"; } ?>>
										Could you be pregnant
									</label>
									<label for="last_menses">
										<input type="checkbox" name="last_menses" id="last_menses" value="1" <?php if($patient_pac_details_women["last_menses"]==1){ echo "checked"; } ?>>
										First day of lasts menses &nbsp; <input type="text" name="last_menses_details" id="last_menses_details" value="<?php echo $patient_pac_details_women["last_menses_details"]?>" style="width:575px;">
									</label>
									<label for="post_menopause">
										<input type="checkbox" name="post_menopause" id="post_menopause" value="1" <?php if($patient_pac_details_women["post_menopause"]==1){ echo "checked"; } ?>>
										Post menopause / hysterectomy
									</label>
								</td>
							</tr>
							<tr>
								<th>Anesthesia Related Information</th>
							</tr>
							<tr>
								<td>
									<label for="anesthesia_year">
										<input type="checkbox" name="anesthesia_year" id="anesthesia_year" value="1" <?php if($patient_pac_details_ari["anesthesia_year"]==1){ echo "checked"; } ?>>
										Anesthesia with one year
									</label>
									<label for="intubation">
										<input type="checkbox" name="intubation" id="intubation" value="1" <?php if($patient_pac_details_ari["intubation"]==1){ echo "checked"; } ?>>
										History of difficult intubation
									</label>
									<label for="epidural_anesthesia">
										<input type="checkbox" name="epidural_anesthesia" id="epidural_anesthesia" value="1" <?php if($patient_pac_details_ari["epidural_anesthesia"]==1){ echo "checked"; } ?>>
										Any object to spinal / epidural anesthesia
									</label>
									<label for="advesre_reaction">
										<input type="checkbox" name="advesre_reaction" id="advesre_reaction" value="1" <?php if($patient_pac_details_ari["advesre_reaction"]==1){ echo "checked"; } ?>>
										Advesre reaction to anesthesia
									</label>
									<label for="hyperthermia">
										<input type="checkbox" name="hyperthermia" id="hyperthermia" value="1" <?php if($patient_pac_details_ari["hyperthermia"]==1){ echo "checked"; } ?>>
										Relative with malignant Hyperthermia
									</label>
									<label for="nausea">
										<input type="checkbox" name="nausea" id="nausea" value="1" <?php if($patient_pac_details_ari["nausea"]==1){ echo "checked"; } ?>>
										Nausea or Vomiting after Anesthesia
									</label>
									<label for="anesthesia_eat">
										<input type="checkbox" name="anesthesia_eat" id="anesthesia_eat" value="1" <?php if($patient_pac_details_ari["anesthesia_eat"]==1){ echo "checked"; } ?>>
										Are you aware of the risk of eating or drinking the day of your anesthesia
									</label>
								</td>
							</tr>
							<tr>
								<th>Beacause drugs may interact adversely with anesthesia, please indicate the following</th>
							</tr>
							<tr>
								<td>
									<label for="alcohol">
										<input type="checkbox" name="alcohol" id="alcohol" value="1" <?php if($patient_pac_details_drug["alcohol"]==1){ echo "checked"; } ?>>
										History of regular alcohol use or within 24 hours
									</label>
									<label for="steroids">
										<input type="checkbox" name="steroids" id="steroids" value="1" <?php if($patient_pac_details_drug["steroids"]==1){ echo "checked"; } ?>>
										Use of steroids / cortisone in the past year
									</label>
									<label for="street_drug">
										<input type="checkbox" name="street_drug" id="street_drug" value="1" <?php if($patient_pac_details_drug["street_drug"]==1){ echo "checked"; } ?>>
										History of "Street Drugs" use or within 30 days
									</label>
									<label for="crapped_teeth">
										<input type="checkbox" name="crapped_teeth" id="crapped_teeth" value="1" <?php if($patient_pac_details_drug["crapped_teeth"]==1){ echo "checked"; } ?>>
										<b>Loose or crapped teeth or dentures in place</b>
									</label>
								</td>
							</tr>
							<tr>
								<th>Intubation Assessment</th>
							</tr>
							<tr>
								<td>
									<label for="dentures">
										<input type="checkbox" name="dentures" id="dentures" value="1" <?php if($patient_pac_details_intubation["dentures"]==1){ echo "checked"; } ?>>
										Dentures
									</label>
									<label for="crowns">
										<input type="checkbox" name="crowns" id="crowns" value="1" <?php if($patient_pac_details_intubation["crowns"]==1){ echo "checked"; } ?>>
										Cap/Crowns
									</label>
									<label for="overbites">
										<input type="checkbox" name="overbites" id="overbites" value="1" <?php if($patient_pac_details_intubation["overbites"]==1){ echo "checked"; } ?>>
										Overbites
									</label>
									<label for="loose_teeth">
										<input type="checkbox" name="loose_teeth" id="loose_teeth" value="1" <?php if($patient_pac_details_intubation["loose_teeth"]==1){ echo "checked"; } ?>>
										Loose teeth
									</label>
									<label for="oedentululous">
										<input type="checkbox" name="oedentululous" id="oedentululous" value="1" <?php if($patient_pac_details_intubation["oedentululous"]==1){ echo "checked"; } ?>>
										Oedentululous
									</label>
									<label for="tm_joint_rom">
										<b>TM JOINT ROM :</b>
										<input type="text" name="tm_joint_rom" id="tm_joint_rom" value="<?php echo $patient_pac_details_intubation["tm_joint_rom"]?>" style="width:650px;">
									</label>
									<label for="neck_rom">
										<b>NECK ROM &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</b>
										<input type="text" name="neck_rom" id="neck_rom" value="<?php echo $patient_pac_details_intubation["neck_rom"]?>" style="width:650px;">
									</label>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<th>ASA GRADE <span style="float:right;">:</span></th>
				<td>
					<label for="asa_grade_1" style="display: inline;">
						<input type="checkbox" name="asa_grade_1" id="asa_grade_1" value="1" <?php if($patient_pac_asa_grade["asa_grade_1"]==1){ echo "checked"; } ?>>
						1
					</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="asa_grade_2" style="display: inline;">
						<input type="checkbox" name="asa_grade_2" id="asa_grade_2" value="1" <?php if($patient_pac_asa_grade["asa_grade_2"]==1){ echo "checked"; } ?>>
						2
					</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="asa_grade_3" style="display: inline;">
						<input type="checkbox" name="asa_grade_3" id="asa_grade_3" value="1" <?php if($patient_pac_asa_grade["asa_grade_3"]==1){ echo "checked"; } ?>>
						3
					</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="asa_grade_4" style="display: inline;">
						<input type="checkbox" name="asa_grade_4" id="asa_grade_4" value="1" <?php if($patient_pac_asa_grade["asa_grade_4"]==1){ echo "checked"; } ?>>
						4
					</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="asa_grade_5" style="display: inline;">
						<input type="checkbox" name="asa_grade_5" id="asa_grade_5" value="1" <?php if($patient_pac_asa_grade["asa_grade_5"]==1){ echo "checked"; } ?>>
						5
					</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="asa_grade_6" style="display: inline;">
						<input type="checkbox" name="asa_grade_6" id="asa_grade_6" value="1" <?php if($patient_pac_asa_grade["asa_grade_6"]==1){ echo "checked"; } ?>>
						6
					</label>
				</td>
			</tr>
			<tr>
				<th>PLAN <span style="float:right;">:</span></th>
				<td>
					<label for="plan_ga" style="display: inline;">
						<input type="checkbox" name="plan_ga" id="plan_ga" value="1" <?php if($patient_pac_plan["plan_ga"]==1){ echo "checked"; } ?>>
						GA
					</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="plan_sa" style="display: inline;">
						<input type="checkbox" name="plan_sa" id="plan_sa" value="1" <?php if($patient_pac_plan["plan_sa"]==1){ echo "checked"; } ?>>
						SA
					</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="plan_epidural" style="display: inline;">
						<input type="checkbox" name="plan_epidural" id="plan_epidural" value="1" <?php if($patient_pac_plan["plan_epidural"]==1){ echo "checked"; } ?>>
						Epidural
					</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="plan_tiva" style="display: inline;">
						<input type="checkbox" name="plan_tiva" id="plan_tiva" value="1" <?php if($patient_pac_plan["plan_tiva"]==1){ echo "checked"; } ?>>
						TIVA
					</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="plan_bb" style="display: inline;">
						<input type="checkbox" name="plan_bb" id="plan_bb" value="1" <?php if($patient_pac_plan["plan_bb"]==1){ echo "checked"; } ?>>
						BB
					</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label for="plan_other" style="display: inline;">
						Other
						<input type="text" name="plan_other" id="plan_other" value="<?php echo $patient_pac_plan["plan_other"]?>" style="width: 685px;">
					</label>
				</td>
			</tr>
			<tr>
				<th>Advice <span style="float:right;">:</span></th>
				<td>
					<textarea rows="5" name="advice" id="advice" placeholder="Advice" style="width:98%;resize: none;"><?php echo $patient_pac_details["advice"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th>Final Plan <span style="float:right;">:</span></th>
				<td>
					<textarea rows="5" name="final_plan" id="final_plan" placeholder="Final Plan" style="width:98%;resize: none;"><?php echo $patient_pac_details["final_plan"]; ?></textarea>
				</td>
			</tr>
		</table>
		<input type="hidden" name="pac_patient_id" id="pac_patient_id" value="<?php echo $uhid; ?>">
		<input type="hidden" name="pac_opd_id" id="pac_opd_id" value="<?php echo $opd_id; ?>">
		<input type="hidden" name="pac_consultantdoctorid" id="pac_consultantdoctorid" value="<?php echo $consultantdoctorid; ?>">
	</form>
	<br>
	<center>
		<button class="btn btn-save" id="save_pac_btn" onclick="save_pac()"><i class="icon-save"></i> Save</button>
<?php
	if($ipd_pat_doc)
	{
?>
		<button class="btn btn-print" id="pac_print_btn" onclick="pac_print()" style="<?php echo $print_btn_display; ?>;"><i class="icon-print"></i> Print</button>
<?php
	}
?>
	</center>
	<br>
	<br>
	<br>
</div>

<script>
	function save_pac()
	{
		if($("#propose_procedure").val()=="")
		{
			$("#propose_procedure").focus();
			return false
		}
		
		$("#loader").show();
		var input = document.getElementById("patient_pac_form");
		formData= new FormData(input);
		$.ajax({
			url: "pages/patient_pac_save.php",    // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: formData,       // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{
				$("#loader").hide();
				bootbox.dialog({ message: "<h4>"+data+"</h4>"});
				setTimeout(function()
				{
					bootbox.hideAll();
					$("#pac_print_btn").show();
				}, 2000);
			},
			error: function (err) {
				alert(err);
			}
		});
	}
	function pac_print()
	{
		var uhid=$("#pac_patient_id").val();
		var opd_id=$("#pac_opd_id").val();
		var user=$("#user").text().trim();
		
		url="pages/patient_pac_print.php?v="+btoa(1234567890)+"&uhid="+btoa(uhid)+"&opdid="+btoa(opd_id)+"&user="+btoa(user);
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
