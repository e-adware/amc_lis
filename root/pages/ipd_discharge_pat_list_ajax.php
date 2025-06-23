<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$time=date('H:i:s');
$date=date("Y-m-d");

$type=$_POST['type'];


if($type==1)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	$q="SELECT * FROM `ipd_pat_details` WHERE `patient_id` NOT IN (SELECT `patient_id` FROM `ipd_pat_discharge_details`) ORDER BY `date`,`time` DESC";
	if($uhid)
	{
		$q="SELECT * FROM `ipd_pat_details` WHERE `patient_id` NOT IN (SELECT `patient_id` FROM `ipd_pat_discharge_details`) AND `patient_id` IN (SELECT `patient_id` FROM `patient_info` WHERE `uhid`='$uhid') ORDER BY `date`,`time` DESC LIMIT 0,20";
	}
	if($ipd)
	{
		$q="SELECT * FROM `ipd_pat_details` WHERE `patient_id` NOT IN (SELECT `patient_id` FROM `ipd_pat_discharge_details`) AND `ipd_id`='$ipd' ORDER BY `date`,`time` DESC LIMIT 0,20";
	}
	if($name)
	{
		$q="SELECT * FROM `ipd_pat_details` WHERE `patient_id` NOT IN (SELECT `patient_id` FROM `ipd_pat_discharge_details`) AND `patient_id` IN (SELECT `patient_id` FROM `patient_info` WHERE `name` like '$name%') ORDER BY `date`,`time` DESC LIMIT 0,20";
	}
	if($dat)
	{
		$q="SELECT * FROM `ipd_pat_details` WHERE `patient_id` NOT IN (SELECT `patient_id` FROM `ipd_pat_discharge_details`) AND `date`='$dat' ORDER BY `date`,`time` DESC LIMIT 0,20";
	}
	//echo $q;
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>UHID</th>
				<th>PIN</th>
				<th>Name</th>
				<th>Sex</th>
				<!--<th>Status</th>-->
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				if($r['scheduled']=="0")
				$sh="Not scheduled";
				if($r['scheduled']=="1")
				$sh="Scheduled";
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['ipd_id'];?>')" style="cursor:pointer;">
					<td><?php echo $p['uhid'];?></td>
					<td><?php echo $r['ipd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<!--<td><?php echo $sh;?></td>-->
				</tr>
			<?php
			}
		?>
		</table>
		<?php
	}
}

if($type=="load_pat_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	?>
	<table class="table table-condensed" id="hist_table">
	<?php
		$q=mysqli_query($link,"SELECT * FROM `ipd_pat_complaints` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		$num=mysqli_num_rows($q);
		$nm=1;
		if($num>0)
		{
		while($r=mysqli_fetch_array($q))
		{
	?>
		<tr class="cc">
			<th>Chief Complaints</th>
			<td>
				<input type="text" id="chief<?php echo $nm;?>" value="<?php echo $r['comp_one']; ?>" onkeyup="sel_chief(<?php echo $nm;?>,event)" />
			</td>
			<td>
				<b>For</b> 
				<select id="cc<?php echo $nm;?>" class="span2" onkeyup="sel_chief(<?php echo $nm;?>,event)">
					<option value="0">Select</option>
					<?php
					for($n=1;$n<=30;$n++)
					{
					?>
					<option value="<?php echo $n;?>" <?php if($n==$r['comp_two']){echo "selected='selected'";}?>><?php echo $n;?></option>
					<?php
					}
					?>
				</select>
				<select id="tim<?php echo $nm;?>" class="span2" onkeyup="sel_chief(<?php echo $nm;?>,event)">
					<option value="0">--Select--</option>
					<option value="Minutes" <?php if($r['comp_three']=="Minutes"){echo "selected='selected'";}?>>Minutes</option>
					<option value="Hours" <?php if($r['comp_three']=="Hours"){echo "selected='selected'";}?>>Hours</option>
					<option value="Days" <?php if($r['comp_three']=="Days"){echo "selected='selected'";}?>>Days</option>
					<option value="Week" <?php if($r['comp_three']=="Week"){echo "selected='selected'";}?>>Week</option>
					<option value="Month" <?php if($r['comp_three']=="Month"){echo "selected='selected'";}?>>Month</option>
					<option value="Year" <?php if($r['comp_three']=="Year"){echo "selected='selected'";}?>>Year</option>
				</select>
			</td>
			<td>
			<?php if($nm==1){ ?>
				<span style="float:right"><input type="button" id="addmore" class="btn btn-info" onclick="add_row(1)" value="Add More" <?php echo $dis_all; ?> ></span>
			<?php }else{?>
				<span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span>
				<?php } ?>
			</td>
		</tr>
	<?php
	$nm++;
		}
		}
		else
		{
		?>
		<tr class="cc">
			<th>Chief Complaints</th>
			<td>
				<input type="text" id="chief1" value="" onkeyup="sel_chief(1,event)" />
			</td>
			<td>
				<b>For</b> 
				<select id="cc1" class="span2" onkeyup="sel_chief(1,event)">
					<option value="0">Select</option>
					<?php
					for($n=1;$n<=30;$n++)
					{
					?>
					<option value="<?php echo $n;?>"><?php echo $n;?></option>
					<?php
					}
					?>
				</select>
				<select id="tim1" class="span2" onkeyup="sel_chief(1,event)">
					<option value="0">--Select--</option>
					<option value="Minutes">Minutes</option>
					<option value="Hours">Hours</option>
					<option value="Days">Days</option>
					<option value="Week">Week</option>
					<option value="Month">Month</option>
					<option value="Year">Year</option>
				</select>
			</td>
			<td>
				<span style="float:right"><input type="button" id="addmore" class="btn btn-info" onclick="add_row(1)" value="Add More" <?php echo $dis_all; ?> ></span>
			</td>
		</tr>
		<?php
		}
		$h_q=mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		$e=mysqli_num_rows($h_q);
		if($e>0)
		{
			$h_e=mysqli_fetch_array($h_q);
			$hist=$h_e['history'];
			$exm=$h_e['examination'];
		}
		else
		{
			$hist="";
			$exm="";
		}
	?>
		<tr id="hh">
			<th colspan="4"><span style="float:right"><input type="button" id="p" class="btn btn-info" onclick="save_comp()" value="Save" /></span></th>
		</tr>
		<tr>
			<th colspan="4" style="background:#dddddd;"></th>
		</tr>
		<tr>
			<th>Past History</th>
			<td colspan="3"><textarea style="resize:none;width:96%" name="history" onkeyup="tab(this.id,event)" id="history"><?php echo $hist; ?></textarea></td>
		</tr>
		<tr>
			<th>Examination</th>
			<td colspan="3"><textarea style="resize:none;width:96%" name="exam" onkeyup="tab(this.id,event)" id="exam"><?php echo $exm; ?></textarea></td>
		</tr>
		<tr>
			<td colspan="4"><span style="float:right;"><input type="button" id="sav" class="btn btn-info" value="Save" onclick="save_exam()" ></span></td>
		</tr>
		<tr>
			<th colspan="4" style="background:#dddddd;"></th>
		</tr>
	</table>
	<table id="diag_table" class="table table-condensed table-bordered">
		<tr>
			<th>Diagnosis</th>
			<th>Order</th>
			<th>Certainity</th>
			<th></th>
		</tr>
		<?php
		$d_q=mysqli_query($link,"SELECT * FROM `ipd_pat_diagnosis` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		$n_d=mysqli_num_rows($d_q);
		if($n_d>0)
		{
			while($det_q=mysqli_fetch_array($d_q))
			{
		?>
		<tr>
			<td><?php echo $det_q['diagnosis']; ?></td>
			<td><?php echo $det_q['order']; ?></td>
			<td><?php echo $det_q['certainity']; ?></td>
			<td></td>
		</tr>
		<?php
			}
		}
		?>
		<tr class="diag">
			<td><input type="text" id="diag" class="span3" placeholder="Diagnosis" /></td>
			<td><select id="ord" class="span2"><option value="0">Select</option><option value="Primary">Primary</option><option value="Secondary">Secondary</option></select></td>
			<td><select id="cert" class="span2"><option value="0">Select</option><option value="Confirmed">Confirmed</option><option value="Presumed">Presumed</option></select></td>
			<th><input type="button" class="btn btn-mini btn-info" id="ad" value="Add" onclick="add_row(2)" style="" /></th>
		</tr>
		<tr id="addiagnosis">
			<td colspan="5"><span style="float:right;"><input type="button" id="dasav" class="btn btn-info" value="Save" onclick="save_diagno()" /></span></td>
		</tr>
		<tr>
			<td colspan="5" style="background:#dddddd;"></td>
		</tr>
	</table>
	<?php
	$qryy=mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$nds=mysqli_num_rows($qryy);
	if($nds>0)
	{
		$det=mysqli_fetch_array($qryy);
		$course=$det['course'];
		$fd=$det['final_diagnosis'];
		$v_bp=$det['final_bp'];
		$v_pulse=$det['final_pulse'];
		$v_temp=$det['final_temp'];
		$v_weight=$det['final_weight'];
		$foll=$det['follow_up'];
		$dis="";
		$value="Update";
	}
	else
	{
		$course="";
		$fd="";
		$v_bp="";
		$v_pulse="";
		$v_temp="";
		$v_weight="";
		$foll="";
		$dis="disabled='disabled'";
		$value="Save";
	}
	?>
	<table class="table table-condensed" id="">
			<tr>
				<td><b>Course in hospital</b><br/>
					<textarea id="course" placeholder="Course in hospital" onkeyup="tab(this.id,event)" style="width:98%;height:100px;resize:none;"><?php echo $course; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><b>Lists of procedures performed with date</b><br/>
					<textarea id="final_diag" placeholder="Lists of procedures" onkeyup="tab(this.id,event)" style="width:98%;height:100px;resize:none;"><?php echo $fd; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><b>Vital before discharge</b><br/>
					<table class="table table-condensed table-bordered" style="margin-bottom:0;">
						<tr>
							<th>BP</th>
							<th>Pulse</th>
							<th>Temp.</th>
							<th>Weight</th>
						</tr>
						<tr>
							<td><input type="text" id="v_bp" class="span1" value="<?php echo $v_bp; ?>" /></td>
							<td><input type="text" id="v_pulse" class="span1" value="<?php echo $v_pulse; ?>" /></td>
							<td><input type="text" id="v_temp" class="span1" value="<?php echo $v_temp; ?>" /></td>
							<td><input type="text" id="v_weight" class="span1" value="<?php echo $v_weight; ?>" /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td><b>Discharge Instruction</b><br/>
					<textarea id="foll" placeholder="Discharge Instruction" onkeyup="tab(this.id,event)" style="width:98%;height:100px;resize:none;"><?php echo $foll; ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<button type="button" id="summ_btn" class="btn btn-info" onclick="insert_disc_summ()">
						<i class="icon-file"></i> <?php echo $value; ?>
					</button>
				</td>
			</tr>
		<tr>
			<td colspan="2" style="background:#dddddd;"></td>
		</tr>
			<tr>
				<td><b>Medicine Prescribed</b><br/>
					<table class="table table-condensed" id="">
						<tr>
							<th>#</th>
							<th>Drugs</th>
							<th>Dosage</th>
							<th>Frequency</th>
							<th>Duration</th>
							<th>Instruction</th>
						</tr>
						<?php
						$i=1;
						$qr=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_final_discharge` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
						while($rr=mysqli_fetch_array($qr))
						{
							$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$rr[item_code]'"));
							if($rr['frequency']==1)
							$fq="Immediately";
							if($rr['frequency']==2)
							$fq="Once a day";
							if($rr['frequency']==3)
							$fq="Twice a day";
							if($rr['frequency']==4)
							$fq="Thrice a day";
							if($rr['frequency']==5)
							$fq="Four times a day";
							if($rr['frequency']==6)
							$fq="Five times a day";
							if($rr['frequency']==7)
							$fq="Every Hour";
							if($rr['frequency']==8)
							$fq="Every 2 Hours";
							if($rr['frequency']==9)
							$fq="Every 3 Hours";
							if($rr['frequency']==10)
							$fq="Every 4 Hours";
							if($rr['frequency']==11)
							$fq="Every 5 Hours";
							if($rr['frequency']==12)
							$fq="Every 6 Hours";
							if($rr['frequency']==13)
							$fq="Every 7 Hours";
							if($rr['frequency']==14)
							$fq="Every 8 Hours";
							if($rr['frequency']==15)
							$fq="Every 10 Hours";
							if($rr['frequency']==16)
							$fq="Every 12 Hours";
							
							if($rr['instruction']==1)
							$ins="As Directed";
							if($rr['instruction']==2)
							$ins="Before Meal";
							if($rr['instruction']==3)
							$ins="Empty Stomach";
							if($rr['instruction']==4)
							$ins="After Meal";
							if($rr['instruction']==5)
							$ins="In the Morning";
							if($rr['instruction']==6)
							$ins="In the Evening";
							if($rr['instruction']==7)
							$ins="At Bedtime";
							if($rr['instruction']==8)
							$ins="Immediately";
						?>
						<tr>
							<td><?php echo $i; ?></td>
							<td><?php echo $rr['medicine']; ?></td>
							<td><?php echo $rr['dosage']; ?></td>
							<td><?php echo $fq; ?></td>
							<td><?php echo $rr['duration']." ".$rr['unit_days']; ?></td>
							<td><?php echo $ins; ?></td>
						</tr>
						<?php
						$i++;
						}
						?>
					</table>
					<button type="button" class="btn btn-info" onclick="post_drugs()">
						<i class="icon-plus"></i> Add Medicine
					</button>
				</td>
			</tr>
			<?php
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
					<select id="dtype">
						<option value="0" <?php if($tp==0){echo "selected='selected'";} ?>>Select</option>
						<option value="1" <?php if($tp==1){echo "selected='selected'";} ?>>Routine</option>
						<option value="2" <?php if($tp==2){echo "selected='selected'";} ?>>Transfer to other type of healthcare facility</option>
						<option value="3" <?php if($tp==3){echo "selected='selected'";} ?>>Home Health Care</option>
						<option value="4" <?php if($tp==4){echo "selected='selected'";} ?>>Transfer to short term hospital</option>
						<option value="5" <?php if($tp==5){echo "selected='selected'";} ?>>In hospital death</option>
						<option value="6" <?php if($tp==6){echo "selected='selected'";} ?>>Left against medical advice</option>
						<option value="7" <?php if($tp==7){echo "selected='selected'";} ?>>Discharged alive, destination unknown</option>
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
					<button type="button" class="btn btn-primary" onclick="save_dis_type()">
						<i class="icon-save"></i> Save
					</button>
					<?php
						$pdis=" disabled";
						$final_bill=mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final'");
						$chk_final=mysqli_num_rows($final_bill);
						if($chk_final>0)
						{
							$pdis="";
						}
					?>
					<span class="text-right"><button type="button" class="btn btn-primary" onclick="print_disc_summary()" <?php echo $dis." ".$pdis; ?>>
						<i class="icon-print"></i> Print
					</button></span>
					
				</td>
			</tr>
		</table>
		<?php
}

if($type=="ipd_pat_examination")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$hist=$_POST['hist'];
	$hist=str_replace("'", "''", "$hist");
	$exam=$_POST['exam'];
	$exam=str_replace("'", "''", "$exam");
	$usr=$_POST['usr'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `pat_examination` SET `history`='$hist',`examination`='$exam' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `pat_examination`(`patient_id`, `opd_id`, `ipd_id`, `history`, `examination`, `date`, `time`, `user`) VALUES ('$uhid','','$ipd','$hist','$exam','$date','$time','$usr')");
		//echo "Saved";
		echo "INSERT INTO `pat_examination`(`patient_id`, `opd_id`, `ipd_id`, `history`, `examination`, `date`, `time`, `user`) VALUES ('$uhid','','$ipd','$hist','$exam','$date','$time','$usr')";
	}
}
?>
