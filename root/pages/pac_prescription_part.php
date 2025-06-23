<style>
	.right_click
	{
		width: 14px;
	}
	.results2
	{
		margin-left: 0px;
	}
	.click_head
	{
		display:none;
	}
</style>
<?php
$patient_pac_details=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details)
{
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
}
?>

<?php
	if($patient_pac_details)
	{
?>
	<div>
		<b>PAC: </b><br>
		<div class="results">
		<?php if($patient_pac_details["propose_procedure"]){ ?>
			<div>
				<b>Procedure Proposed: </b>
				<?php echo nl2br($patient_pac_details["propose_procedure"]); ?>
			</div>
		<?php } ?>
		<?php if($patient_pac_details["previous_surgeries"]){ ?>
			<div>
				<b>Previous Surgeries &amp; Anesthesia: </b><br>
				<?php echo nl2br($patient_pac_details["previous_surgeries"]); ?>
			</div>
		<?php } ?>
		<?php if($patient_pac_details["prescribed_medication"]){ ?>
			<div>
				<b>Prescribed Medications: </b><br>
				<?php echo nl2br($patient_pac_details["prescribed_medication"]); ?>
			</div>
		<?php } ?>
		<?php if($patient_pac_details["blood_thinner"]){ ?>
			<div>
				<b>Blood thinners last taken: </b>
				<?php echo nl2br($patient_pac_details["blood_thinner"]); ?>
			</div>
		<?php } ?>
		<?php if($patient_pac_details["food_allergies"]){ ?>
			<div>
				<b>Food &amp; Drugs allergies / Reactions: </b>
				<?php echo nl2br($patient_pac_details["food_allergies"]); ?>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_cd){ ?>
			<div>
				<div class="click_head"><b>Cardiovascular Disease: </b></div>
				<div class="results2">
				<?php if($patient_pac_details_cd["chest_pain"]==1){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Food &amp; Drugs allergies / Reactions
					</div>
				<?php } ?>
				<?php if($patient_pac_details_cd["irregu_hear_beat"]==1){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Irregular Heart Beat
					</div>
				<?php } ?>
				<?php if($patient_pac_details_cd["pacemeaker"]==1){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Pacemaker/Defibrillator <?php if($patient_pac_details_cd["pacemeaker_brand"]){ echo ", Brand : ".$patient_pac_details_cd["pacemeaker_brand"]; } ?>
					</div>
				<?php } ?>
				<?php if($patient_pac_details_cd["problem_circulation"]==1){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Problem with Circulation
					</div>
				<?php } ?>
				<?php if($patient_pac_details_cd["blood_clot"]==1){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Blood Clot in legs or lungs
					</div>
				<?php } ?>
				<?php if($patient_pac_details_cd["high_bp"]==1){ ?>
					<div>
						<?php echo $right_click_image; ?>
						High Blood Pressure
					</div>
				<?php } ?>
				<?php if($patient_pac_details_cd["cd_other_details"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Other : <?php echo $patient_pac_details_cd["cd_other_details"]; ?>
					</div>
				<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_rd){ ?>
			<div>
				<div class="click_head"><b>Respiratory Disease: </b></div>
				<div class="results2">
				<?php if($patient_pac_details_rd["smoking"]==1){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Smoking &nbsp; <?php if($patient_pac_details_rd["smoking_pack"]){ echo $patient_pac_details_rd["smoking_pack"]." packs per day."; } ?> &nbsp; <?php if($patient_pac_details_rd["smoking_quit"]){ echo "Quit : ".$patient_pac_details_rd["smoking_quit"].""; } ?>
					</div>
				<?php } ?>
				<?php if($patient_pac_details_rd["asthma"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Asthma
					</div>
				<?php } ?>
				<?php if($patient_pac_details_rd["emphysema"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Emphysema/Brochitis
					</div>
				<?php } ?>
				<?php if($patient_pac_details_rd["breath_shortness"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Shortness of breath at rest
					</div>
				<?php } ?>
				<?php if($patient_pac_details_rd["respiratory_infection"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Upper respiratory infection(cold) within 2 weeks
					</div>
				<?php } ?>
					<div>
					<?php if($patient_pac_details_rd["sleep_apnea"]){ ?>
						<?php echo $right_click_image; ?>
						Sleep apnea
						 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php } ?>
					<?php if($patient_pac_details_rd["cpap"]){ ?>
						<?php echo $right_click_image; ?>
						Use CPAP
						 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php } ?>
					<?php if($patient_pac_details_rd["tb"]){ ?>
						<?php echo $right_click_image; ?>
						TB
					<?php } ?>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_nd){ ?>
			<div>
				<div class="click_head"><b>Neurological Disease: </b></div>
				<div class="results2">
				<?php if($patient_pac_details_nd["stroke"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Stroke or mini-stroke (T.I.A.)
					</div>
				<?php } ?>
				<?php if($patient_pac_details_nd["seizures"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Seizures
					</div>
				<?php } ?>
				<?php if($patient_pac_details_nd["back_neck"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Back or neck problems
					</div>
				<?php } ?>
				<?php if($patient_pac_details_nd["phys_resctriction"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Physical restrictions/limitations
					</div>
				<?php } ?>
				<?php if($patient_pac_details_nd["memory_loss"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Forgetfullness / Memory loss / Confusion
					</div>
				<?php } ?>
				<?php if($patient_pac_details_nd["sclerosis"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Multiple sclerosis / muscular dystrophy
					</div>
				<?php } ?>
				<?php if($patient_pac_details_nd["nerve_injury"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Nerve / Spinal cord injury
					</div>
				<?php } ?>
				<?php if($patient_pac_details_nd["neuropathy"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Neuropathy
					</div>
				<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_dtkg["diabetes"]==1 || $patient_pac_details_dtkg["insulin"]==1 || $patient_pac_details_dtkg["oha"]==1){ ?>
			<div>
				<div class="click_head"><b>Diabetes Disease: </b></div>
				<div class="results2">
				<?php if($patient_pac_details_dtkg["diabetes"]){ ?>
					<?php echo $right_click_image; ?>
					Diabetes
					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_details_dtkg["insulin"]){ ?>
					<?php echo $right_click_image; ?>
					Taking insulin
					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_details_dtkg["oha"]){ ?>
					<?php echo $right_click_image; ?>
					OHA
				<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_dtkg["thyroid"]==1){ ?>
			<div>
				<div class="">
					<?php echo $right_click_image; ?>
					Thyroid disorder 
					 &nbsp;
					 <?php if($patient_pac_details_dtkg["thyroid_disorder_details"]){ echo " : &nbsp;".$patient_pac_details_dtkg["thyroid_disorder_details"].""; } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_dtkg["kidney_disorder"]==1){ ?>
			<div>
				<div class="">
					<?php echo $right_click_image; ?>
					Kidney/Bladder/Prostrate Disorder
					 &nbsp;
					 <?php if($patient_pac_details_dtkg["kidney_disorder_details"]){ echo " : &nbsp;".$patient_pac_details_dtkg["kidney_disorder_details"].""; } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_dtkg["urinate"]==1){ ?>
			<div>
				<div class="">
					<?php echo $right_click_image; ?>
					Inability to urinate after anesthesia
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_dtkg["dialysis_schedule"]==1){ ?>
			<div>
				<div class="">
					<?php echo $right_click_image; ?>
					Dialysis
					 &nbsp;
					 <?php if($patient_pac_details_dtkg["dialysis_schedule_details"]){ echo ", Schedule : &nbsp;".$patient_pac_details_dtkg["dialysis_schedule_details"].""; } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_dtkg["jaundice"]==1 || $patient_pac_details_dtkg["hernia"]==1 || $patient_pac_details_dtkg["gastro_other_details"]){ ?>
			<div>
				<div class="click_head"><b>Gastro-Intestinal Disease: </b></div>
				<div class="results2">
				<?php if($patient_pac_details_dtkg["jaundice"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Liver Disease (Jaundice/Hepatitis)
					</div>
				<?php } ?>
				<?php if($patient_pac_details_dtkg["hernia"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Hiatal hernia / reflux / heartburn
					</div>
				<?php } ?>
				<?php if($patient_pac_details_dtkg["gastro_other_details"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Other : <?php echo $patient_pac_details_dtkg["gastro_other_details"]?>
					</div>
				<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_bd){ ?>
			<div>
				<div class="click_head"><b>Blood Disorder: </b></div>
				<div class="results2">
				<?php if($patient_pac_details_bd["bleeding_tendency"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Abnormal bleeding tendency or taking blood tinners
					</div>
				<?php } ?>
				<?php if($patient_pac_details_bd["cell_disease"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Sickle cell disease or trait
					</div>
				<?php } ?>
				<?php if($patient_pac_details_bd["blood_transfusion"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						History of blood transfusions
					</div>
				<?php } ?>
				<?php if($patient_pac_details_bd["blood_transfusion_objection"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Religious or other objections to blood transfusion
					</div>
				<?php } ?>
					<div>
					<?php if($patient_pac_details_bd["hcv"]){ ?>
						<?php echo $right_click_image; ?>
						HCV
						 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php } ?>
					<?php if($patient_pac_details_bd["hbsag"]){ ?>
						<?php echo $right_click_image; ?>
						HBsAg
						 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php } ?>
					<?php if($patient_pac_details_bd["hiv"]){ ?>
						<?php echo $right_click_image; ?>
						HIV
					<?php } ?>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_ed["eye_disorder"]==1){ ?>
			<div>
				<div class="">
					<?php echo $right_click_image; ?>
					Eye Disorder/Glaucoma/Retinal detachment
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_ed["ear_disorder"]==1){ ?>
			<div>
				<div class="">
					<?php echo $right_click_image; ?>
					Ear Disorder/Ringing in ear/Hearing loss
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_ed["cancer_therapy"]==1){ ?>
			<div>
				<div class="">
					<?php echo $right_click_image; ?>
					Cancer/Chemotherapy/Radioation Therapy
					 &nbsp;
					 <?php if($patient_pac_details_ed["cancer_therapy_reason"]){ echo " : &nbsp;".$patient_pac_details_ed["cancer_therapy_reason"].""; } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_ed["psychiatric_disorder"]==1){ ?>
			<div>
				<div class="">
					<?php echo $right_click_image; ?>
					Psychiatric disorder
					 &nbsp;
					 <?php if($patient_pac_details_ed["psychiatric_disorder_reason"]){ echo " : &nbsp;".$patient_pac_details_ed["psychiatric_disorder_reason"].""; } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_ed["other_disease"]==1){ ?>
			<div>
				<div class="">
					<?php echo $right_click_image; ?>
					Other illness or disease
					 &nbsp;
					 <?php if($patient_pac_details_ed["other_disease_name"]){ echo " : &nbsp;".$patient_pac_details_ed["other_disease_name"].""; } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_women){ ?>
			<div>
				<div class="click_head"><b>For Women: </b></div>
				<div class="results2">
				<?php if($patient_pac_details_women["pregnant"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Could you be pregnant
					</div>
				<?php } ?>
				<?php if($patient_pac_details_women["last_menses"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						First day of lasts menses
						 &nbsp;
					 <?php if($patient_pac_details_women["last_menses_details"]){ echo " : &nbsp;".$patient_pac_details_women["last_menses_details"].""; } ?>
					</div>
				<?php } ?>
				<?php if($patient_pac_details_women["post_menopause"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Post menopause / hysterectomy
					</div>
				<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_ari){ ?>
			<div>
				<div class="click_head"><b>Anesthesia Related Information: </b></div>
				<div class="results2">
				<?php if($patient_pac_details_ari["anesthesia_year"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Anesthesia with one year
					</div>
				<?php } ?>
				<?php if($patient_pac_details_ari["intubation"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						History of difficult intubation
					</div>
				<?php } ?>
				<?php if($patient_pac_details_ari["epidural_anesthesia"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Any object to spinal / epidural anesthesia
					</div>
				<?php } ?>
				<?php if($patient_pac_details_ari["advesre_reaction"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Advesre reaction to anesthesia
					</div>
				<?php } ?>
				<?php if($patient_pac_details_ari["hyperthermia"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Relative with malignant Hyperthermia
					</div>
				<?php } ?>
				<?php if($patient_pac_details_ari["nausea"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Nausea or Vomiting after Anesthesia
					</div>
				<?php } ?>
				<?php if($patient_pac_details_ari["anesthesia_eat"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Are you aware of the risk of eating or drinking the day of your anesthesia
					</div>
				<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_drug){ ?>
			<div>
				<div class="click_head"><b>Beacause drugs may interact adversely with anesthesia, please indicate the following: </b></div>
				<div class="results2">
				<?php if($patient_pac_details_drug["alcohol"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						History of regular alcohol use or within 24 hours
					</div>
				<?php } ?>
				<?php if($patient_pac_details_drug["steroids"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Use of steroids / cortisone in the past year
					</div>
				<?php } ?>
				<?php if($patient_pac_details_drug["street_drug"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						History of "Street Drugs" use or within 30 days
					</div>
				<?php } ?>
				<?php if($patient_pac_details_drug["crapped_teeth"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Loose or crapped teeth or dentures in place
					</div>
				<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_details_intubation){ ?>
			<div>
				<div class="click_head"><b>Intubation Assessment: </b></div>
				<div class="results2">
				<?php if($patient_pac_details_intubation["dentures"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Dentures
					</div>
				<?php } ?>
				<?php if($patient_pac_details_intubation["crowns"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Cap/Crowns
					</div>
				<?php } ?>
				<?php if($patient_pac_details_intubation["overbites"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Overbites
					</div>
				<?php } ?>
				<?php if($patient_pac_details_intubation["loose_teeth"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Loose teeth
					</div>
				<?php } ?>
				<?php if($patient_pac_details_intubation["oedentululous"]){ ?>
					<div>
						<?php echo $right_click_image; ?>
						Oedentululous
					</div>
				<?php } ?>
				<?php if($patient_pac_details_intubation["tm_joint_rom"]){ ?>
					<div>
						TM JOINT ROM : <?php echo $patient_pac_details_intubation["tm_joint_rom"]?>
					</div>
				<?php } ?>
				<?php if($patient_pac_details_intubation["neck_rom"]){ ?>
					<div>
						NECK ROM : <?php echo $patient_pac_details_intubation["neck_rom"]?>
					</div>
				<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if($patient_pac_asa_grade){ ?>
			<div>
				<b>ASA GRADE: &nbsp;&nbsp;&nbsp;</b>
				<?php if($patient_pac_asa_grade["asa_grade_1"]){ ?>
					1
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_asa_grade["asa_grade_2"]){ ?>
					2
					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_asa_grade["asa_grade_3"]){ ?>
					3
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_asa_grade["asa_grade_4"]){ ?>
					4
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_asa_grade["asa_grade_5"]){ ?>
					5
					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_asa_grade["asa_grade_6"]){ ?>
					6
				<?php } ?>
			</div>
		<?php } ?>
		<?php if($patient_pac_plan){ ?>
			<div>
				<b>PLAN: &nbsp;&nbsp;&nbsp;</b>
				<?php if($patient_pac_plan["plan_ga"]){ ?>
					GA
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_plan["plan_sa"]){ ?>
					SA
					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_plan["plan_epidural"]){ ?>
					Epidural
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_plan["plan_tiva"]){ ?>
					TIVA
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_plan["plan_bb"]){ ?>
					BB
					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if($patient_pac_plan["plan_other"]){ ?>
					<?php echo $patient_pac_plan["plan_other"]?>
				<?php } ?>
			</div>
		<?php } ?>
		<?php if($patient_pac_details["advice"]){ ?>
			<div>
				<b>Advice: </b>
				<?php echo nl2br($patient_pac_details["advice"]); ?>
			</div>
		<?php } ?>
		<?php if($patient_pac_details["final_plan"]){ ?>
			<div>
				<b>Final Plan: </b><br>
				<?php echo nl2br($patient_pac_details["final_plan"]); ?>
			</div>
		<?php } ?>
		</div>
	</div>
<?php
	}
?>
