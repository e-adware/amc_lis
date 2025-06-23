<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
$date=date("Y-m-d");
$time=date('H:i:s');


if($_POST["type"]=="nicu_disc_summary")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$btn_name="Save";
	$mother_blood_group="";
	$baby_blood_group="";
	$data_baby=mysqli_fetch_array(mysqli_query($link," SELECT `patient_id`,`blood_group` FROM `ipd_pat_delivery_det` WHERE `baby_uhid`='$uhid' "));
	if($data_baby)
	{
		$baby_blood_group=$data_baby["blood_group"];
		
		$data_mother=mysqli_fetch_array(mysqli_query($link," SELECT `blood_group` FROM `patient_info` WHERE `patient_id`='$data_baby[patient_id]' "));
		if($data_mother)
		{
			$mother_blood_group=$data_mother["blood_group"];
		}
	}
	
	$data=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_discharge_summary_nicu` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
	if($data)
	{
		$btn_name="Update";
		
		$baby_blood_group=$data["abo_baby"];
		$mother_blood_group=$data["abo_mother"];
	}
	
	?>
	<table class="table table-condensed" id="nicu_summary_table">
		<tr>
			<td colspan="2">
				<b>Gestations : </b>
				<input type="text" id="gestations" value="<?php echo $data["gestations"]; ?>"> Weeks
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>Apgar Score</b>
				<br/>
				<table class="table table-condensed table-bordered" style="margin-bottom:0;">
					<tr>
						<th>1 Minute</th>
						<th>5 Minutes</th>
						<th>10 Minutes</th>
					</tr>
					<tr>
						<td><input type="text" id="apgar_score_1m" class="span3" value="<?php echo $data["apgar_score_1m"]; ?>" /></td>
						<td><input type="text" id="apgar_score_5m" class="span3" value="<?php echo $data["apgar_score_5m"]; ?>" /></td>
						<td><input type="text" id="apgar_score_10m" class="span3" value="<?php echo $data["apgar_score_10m"]; ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>ABO RH GROUPING</b>
				<br/>
				<table class="table table-condensed table-bordered" style="margin-bottom:0;">
					<tr>
						<th>Mother</th>
						<th>Baby</th>
					</tr>
					<tr>
						<td>
							<input type="text" id="abo_mother" class="span3" value="<?php echo $mother_blood_group; ?>" list="abo_mother_list" />
							<datalist id="abo_mother_list">
						<?php
							$abo_qry=mysqli_query($link," SELECT DISTINCT `blood_group` FROM `ipd_pat_delivery_det` WHERE `blood_group`!='' ORDER BY `blood_group` ");
							while($abo_val=mysqli_fetch_array($abo_qry))
							{
								echo "<option>$abo_val[blood_group]</option>";
							}
						?>
							</datalist>
						</td>
						<td>
							<input type="text" id="abo_baby" class="span3" value="<?php echo $baby_blood_group; ?>" list="abo_baby_list" />
							<datalist id="abo_baby_list">
						<?php
							$abo_qry=mysqli_query($link," SELECT DISTINCT `blood_group` FROM `ipd_pat_delivery_det` WHERE `blood_group`!='' ORDER BY `blood_group` ");
							while($abo_val=mysqli_fetch_array($abo_qry))
							{
								echo "<option>$abo_val[blood_group]</option>";
							}
						?>
							</datalist>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="table table-condensed table-bordered" style="margin-bottom:0;">
					<tr>
						<th>Immunization Date BCG</th>
						<th>OPV (Birth Dose)</th>
						<th>Hepatitis B</th>
					</tr>
					<tr>
						<td><input type="text" id="immunization_date_bcg" class="span3" value="<?php echo $data["immunization_date_bcg"]; ?>" /></td>
						<td><input type="text" id="opv_birth_dose" class="span3" value="<?php echo $data["opv_birth_dose"]; ?>" /></td>
						<td><input type="text" id="hepatitis_b" class="span3" value="<?php echo $data["hepatitis_b"]; ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>Discharge Weight : </b>
				<input type="text" id="dicharge_weight" value="<?php echo $data["dicharge_weight"]; ?>"> KG
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>Treatment in hospital</b>
				<br>
				<textarea id="treatment_in_hospital" placeholder="Treatment in hospital" style="width:96%;height:100px;resize:none;"><?php echo $data["treatment_in_hospital"]; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>Case Summary</b>
				<br>
				<textarea style="width:96%;height:100px;resize:none;" id="case_summary"><?php echo $data["case_summary"]; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>Course in hospital</b>
				<br>
				<textarea id="course_in_hospital" placeholder="Course in hospital" style="width:96%;height:100px;resize:none;"><?php echo $data["course_in_hospital"]; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<center>
					<button type="button" id="nicu_summ_btn" class="btn btn-info" onclick="save_nicu_summary()">
						<i class="icon-file"></i> <?php echo $btn_name; ?>
					</button>
				</center>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background:#dddddd;"></td>
		</tr>
			<tr>
				<td><b>Medication at Discharge</b><br/>
				</td>
			</tr>
		<?php
			$old_medi_check=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_final` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
			$mdc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_final` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
			$medicine=$mdc['medicine'];
		?>
			<tr>
				<td>
					<?php
					if($old_medi_check>0)
					{
					?>
					<textarea id="post_med_det" style="width:95%;height:100px;resize:none;" placeholder="Drug details"><?php echo $medicine;?></textarea>
					<?php
					}
					else
					{
					?>
					<b>Drug Name</b> : <input type="text" name="medi" id="medi" class="span8" onFocus="load_medi_list()" onKeyUp="load_medi_list1(this.value,event)" onBlur="javascript:$('#med_div').fadeOut(500)" placeholder="Drug Name" <?php echo $dis_all; ?> >
					<input type="text" id="new_medi" class="span8" onkeyup="tab(this.id,event);$(this).val($(this).val().toUpperCase())" style="display:none;" placeholder="New Drug Name" />
					<button type="button" class="btn" id="new_btn" onclick="new_medi()">New</button>
					<button type="button" class="btn btn-danger" id="can_btn" style="display:none;" onclick="can_medi()">Cancel</button>
					<input type="hidden" id="medid" />
					<input type="hidden" id="mediname" />
					<div id="med_info"></div>
					<div id="med_div" align="center" style="margin-left: 93px;">
						<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
							<th>Drug Name</th>
							<?php
							$d=mysqli_query($link, "SELECT * FROM `item_master` where category_id='1' and `item_name`!='' order by `item_name`");
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
					</div>
					<b>Instruction</b> :&nbsp; <input type="text" id="dos" list="doses" class="span8" placeholder="Dosage / Instruction" onkeyup="add_dose(event)" >
					<input type="text" class="span1" id="ph_quantity" placeholder="Quantity" onkeyup="ph_quantity(event)">
					<datalist id="doses" style="height: 0;">
					<?php
						$doss=mysqli_query($link,"SELECT * FROM `dosage_master`");
						while($d=mysqli_fetch_array($doss))
						{
							echo "<option value='$d[dosage]'>";
						}
					?>
					</datalist>
					<?php
					}
					?>
					
				</td>
			</tr>
			<tr id="item_tr" style="display:none;">
				<td>
					<div id="temp_item">
					
					</div>
				</td>
			</tr>
			<tr>
				<td style="text-align:center;">
					<?php
					if($old_medi_check>0)
					{
					?>
						<button type="button" id="sav_medi" class="btn btn-primary" onclick="save_final_medi()"><i class="icon-save"></i> Save</button>
					<?php
					}
					else
					{
					?>
						<button type="button" id="sav_medi" class="btn btn-primary" onclick="save_all_medi()"><i class="icon-save"></i> Save</button>
					<?php
					}
					?>
				</td>
			</tr>			
		<?php
			if($old_medi_check==0)
			{
				$drug_qry=mysqli_query($link,"SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd' AND `type`='3'");
				if(mysqli_num_rows($drug_qry)>0)
				{
		?>
				<tr>
					<td>
						<table class="table table-condensed">
							<tr style="background:#bbbbbb;color:#444444;">
								<th>#</th><th>Drug Name</th><th>Dosage / Instruction</th><th width="5%">Quantity</th><th width="5%">Remove</th>
							</tr>
							<?php
							$p=1;
							while($drg=mysqli_fetch_array($drug_qry))
							{
								$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$drg[item_code]'"));
							?>
							<tr>
								<td><?php echo $p;?></td>
								<td><?php echo $itm['item_name'];?></td>
								<td><?php echo $drg['dosage'];?></td>
								<td><?php echo $drg['quantity'];?></td>
								<td><i class="icon-remove icon-large" style="color:#980000;cursor:pointer;" onclick="del_med('<?php echo $drg['slno'];?>')"></i></td>
							</tr>
							<?php
							$p++;
							}
							?>
						</table>
					</td>
				</tr>
			<?php
				}
			}
			$disqry=mysqli_query($link,"SELECT * FROM `ipd_dischage_type` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
			$ndm=mysqli_num_rows($disqry);
			if($ndm>0)
			{
				$dtyp=mysqli_fetch_array($disqry);
				$tp=$dtyp['type'];
				$d_by=$dtyp['diagnosed_by'];
			}
			else
			{
				$tp=0;
				$d_by=0;
			}
			?>
			<tr>
				<td>
					<b>Discharge Type</b>
					<select id="dtype" onchange="death_date()">
						<option value="0" <?php if($tp==0){echo "selected='selected'";} ?>>Select</option>
					<?php
						$dis_typ_qry=mysqli_query($link," SELECT * FROM `discharge_master` ORDER BY `discharge_name` ");
						while($dis_typ=mysqli_fetch_array($dis_typ_qry))
						{
							if($tp==$dis_typ['discharge_id']){ $sel_typ="selected"; }else{ $sel_typ=""; }
							echo "<option value='$dis_typ[discharge_id]' $sel_typ >$dis_typ[discharge_name]</option>";
						}
					?>
					</select>
					<b>Diagnosed By</b>
					<select id="diagnosed">
						<option value="0">Select</option>
						<?php
						$doc=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
						while($dr=mysqli_fetch_array($doc))
						{
						?>
						<option value="<?php echo $dr['consultantdoctorid']?>" <?php if($d_by==$dr['consultantdoctorid']){echo "selected='selected'";} ?>><?php echo $dr['Name'];?></option>
						<?php
						}
						?>
					</select>
					<?php
					if($tp=="105")
					{
						$deth_btn="";
						$death_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ipd_pat_death_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
						$death_date=$death_det['death_date'];
						$death_time=$death_det['death_time'];
						$death_cause=$death_det['death_cause'];
					}
					else
					{
						$deth_btn="display:none;";
						$death_date="";
						$death_time="";
						$death_cause="";
					}
					?>
					<div id="death_det" style="<?php echo $deth_btn;?> padding-left:10px; box-shadow: 0px 0px 6px 1px #ECBBB9;">
						<br/>
						<b>Death Date</b> : <input type="text" id="death_date" class="span2 datepicker" value="<?php echo $death_date;?>" placeholder="YY-MM-DD" /> &nbsp;&nbsp;
						<b>Death Time</b> : <input type="text" id="death_time" class="span2 timepicker" value="<?php echo $death_time;?>" placeholder="HH:MM" /><br/>
						<b>Cause</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <input type="text" id="death_cause" class="span6" value="<?php echo $death_cause;?>" placeholder="Cause of death" list="death_cause_list" />
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
					<center>
						<button type="button" class="btn btn-primary" onclick="save_dis_type()">
							<i class="icon-save"></i> Save
						</button>
						<button type="button" class="btn btn-info" style="<?php echo $deth_btn;?>" onclick="print_death_certificate()"><i class="icon-print"></i> Print Death Certificate</button>
					</center>
					<?php
						$pdis=" disabled";
						$final_bill=mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final'");
						$chk_final=mysqli_num_rows($final_bill);
						if($chk_final>0)
						{
							$pdis="";
						}
						$pdis="";
					?>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;">
				<button type="button" class="btn btn-primary" onclick="print_nicu_disc_summary()" <?php echo $dis." ".$pdis; ?>>
					<i class="icon-print"></i> Print
				</button>
				</td>
			</tr>
		</table>
	<?php
}

if($_POST["type"]=="save_nicu_disc_summary")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$user=$_POST['user'];
	$gestations=mysqli_real_escape_string($link, $_POST['gestations']);
	$apgar_score_1m=mysqli_real_escape_string($link, $_POST['apgar_score_1m']);
	$apgar_score_5m=mysqli_real_escape_string($link, $_POST['apgar_score_5m']);
	$apgar_score_10m=mysqli_real_escape_string($link, $_POST['apgar_score_10m']);
	$abo_mother=mysqli_real_escape_string($link, $_POST['abo_mother']);
	$abo_baby=mysqli_real_escape_string($link, $_POST['abo_baby']);
	$immunization_date_bcg=mysqli_real_escape_string($link, $_POST['immunization_date_bcg']);
	$opv_birth_dose=mysqli_real_escape_string($link, $_POST['opv_birth_dose']);
	$hepatitis_b=mysqli_real_escape_string($link, $_POST['hepatitis_b']);
	$dicharge_weight=mysqli_real_escape_string($link, $_POST['dicharge_weight']);
	$treatment_in_hospital=mysqli_real_escape_string($link, $_POST['treatment_in_hospital']);
	$case_summary=mysqli_real_escape_string($link, $_POST['case_summary']);
	$course_in_hospital=mysqli_real_escape_string($link, $_POST['course_in_hospital']);
	
	$entry_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_discharge_summary_nicu` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
	if($entry_check)
	{
		mysqli_query($link," UPDATE `ipd_pat_discharge_summary_nicu` SET `gestations`='$gestations',`apgar_score_1m`='$apgar_score_1m',`apgar_score_5m`='$apgar_score_5m',`apgar_score_10m`='$apgar_score_10m',`abo_mother`='$abo_mother',`abo_baby`='$abo_baby',`immunization_date_bcg`='$immunization_date_bcg',`opv_birth_dose`='$opv_birth_dose',`hepatitis_b`='$hepatitis_b',`dicharge_weight`='$dicharge_weight',`treatment_in_hospital`='$treatment_in_hospital',`case_summary`='$case_summary',`course_in_hospital`='$course_in_hospital',`user`='$user',`date`='$date',`time`='$time' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
	}
	else
	{
		mysqli_query($link," INSERT INTO `ipd_pat_discharge_summary_nicu`(`patient_id`, `ipd_id`, `gestations`, `apgar_score_1m`, `apgar_score_5m`, `apgar_score_10m`, `abo_mother`, `abo_baby`, `immunization_date_bcg`, `opv_birth_dose`, `hepatitis_b`, `dicharge_weight`, `treatment_in_hospital`, `case_summary`, `course_in_hospital`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$gestations','$apgar_score_1m','$apgar_score_5m','$apgar_score_10m','$abo_mother','$abo_baby','$immunization_date_bcg','$opv_birth_dose','$hepatitis_b','$dicharge_weight','$treatment_in_hospital','$case_summary','$course_in_hospital','$user','$date','$time') ");
	}
	
}

if($_POST["type"]=="oo")
{
	
}
?>
