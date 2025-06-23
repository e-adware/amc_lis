<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$type=$_POST["type"];

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="pat_ipd_vital_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$view=$_POST['view'];
?>
	<button type="button" class="btn btn-primary" onclick="add_vitals(0)"><i class="icon-plus"></i> Add Vitals</button>
	<div id="vital_data">
<?php
	$qry=mysqli_query($link,"SELECT * FROM `ipd_pat_vital` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `record_date` DESC, `record_time` DESC");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		$n=1;
		while($r=mysqli_fetch_array($qry))
		{
			$user_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$r[record_by]'"));
?>
		<div><b>Date: <?php echo convert_date_g($r['record_date'])." ".convert_time($r['record_time']);?></b></div>
		<table class="table table-condensed table-bordered">
			<tr>
				<td colspan="6" style="background:#dddddd;"></td>
			</tr>
			<tr>
				<th>Weight</th>
				<th>Height</th>
				<th>BMI</th>
				<th>SPO<sub>2</sub>(%)</th>
				<th>Head Circumference</th>
				<th>Mid-arm Circumference</th>
			</tr>
			<tr>
				<td><?php echo $r['weight'];?> KG</td>
				<td><?php echo $r['height'];?> CM</td>
				<td><?php echo $r['BMI_1'].".".$r['BMI_2'];?></td>
				<td><?php echo $r['spo2'];?></td>
				<td><?php echo $r['head_circumference'];?></td>
				<td><?php echo $r['medium_circumference'];?></td>
			</tr>
			<tr>
				<th>PR</th>
				<th>RR(/min)</th>
				<th>BP</th>
				<th>Pulse</th>
				<th>Temperature (<sup>o</sup>C)</th>
				<th width="25%">Note</th>
			</tr>
			<tr>
				<td><?php echo $r['PR'];?></td>
				<td><?php echo $r['RR'];?></td>
				<td><?php echo $r['systolic']."/".$r['diastolic'];?></td>
				<td><?php echo $r['pulse'];?></td>
				<td><?php echo $r['temp'];?></td>
				<td><div style="max-height:100px;overflow-Y:scroll;"><?php echo $r['note'];?></div></td>
			</tr>
			<tr>
				<th colspan="3">Intake Output</th>
				<th colspan="1">Recorded Time</th>
				<th colspan="1">Entry Time</th>
				<th colspan="1">Recorded By</th>
			</tr>
			<tr>
				<td colspan="3"><?php echo $r['intake_output_record'];?></td>
				<td colspan="1">
					<?php echo date("d-M-Y", strtotime($r['record_date']));?>
					<?php echo date("h:i A", strtotime($r['record_time']));?>
				</td>
				<td colspan="1">
					<?php echo date("d-M-Y", strtotime($r['date']));?>
					<?php echo date("h:i A", strtotime($r['time']));?>
				</td>
				<td colspan="1">
					<?php echo $user_info['name'];?>
					<button class="btn btn-info btn-mini" style="float:right;" onclick="add_vitals('<?php echo $r["id"]; ?>')"><i class="icon-edit"></i> Edit</button>
				</td>
			</tr>
			<tr>
				<td colspan="6" style="background:#dddddd;"></td>
			</tr>
		</table>
		<?php
		$n++;
		}
	}
?>
	</div>
<?php
}

if($_POST["type"]=="add_vitals")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$id=$_POST['id'];
	
	$qry=mysqli_query($link,"SELECT * FROM `ipd_pat_vital` WHERE `id`='$id' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		$det=mysqli_fetch_array($qry);
		$weight=$det['weight'];
		$height=$det['height'];
		$mid_cum=$det['medium_circumference'];
		$hd_cum=$det['head_circumference'];
		$bmi1=$det['BMI_1'];
		$bmi2=$det['BMI_2'];
		$spo=$det['spo2'];
		$pulse=$det['pulse'];
		$temp=$det['temp'];
		$pr=$det['PR'];
		$rr=$det['RR'];
		$systolic=$det['systolic'];
		$diastolic=$det['diastolic'];
		$note=$det['note'];
		$intake_output_record=$det['intake_output_record'];
		$record_by=$det['record_by'];
		$record_date=$det['record_date'];
		$record_time=$det['record_time'];
		$val="Update";
	}
	else
	{
		$weight="";
		$height="";
		$mid_cum="";
		$hd_cum="";
		$bmi1="";
		$bmi2="";
		$spo="";
		$pulse="";
		$temp="";
		$pr="";
		$rr="";
		$systolic="";
		$diastolic="";
		$note="";
		$intake_output_record="";
		$record_by=0;
		$record_date="";
		$record_time="";
		$val="Save";
	}
?>
<table class="table table-condensed">
	<tbody>
		<tr>
			<td><b>Weight</b></td>
			<td><input id="weight" class="span1" value="<?php echo $weight;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');physical(this.value,event)" placeholder="KG" type="text"></td>
			<td><b>Height</b></td>
			<td><input id="height" class="span1" value="<?php echo $height;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');physical1(this.value,event)" placeholder="CM" type="text"></td>
			<td><b>Mid-arm Circumference</b></td>
			<td><input id="mid_cum" value="<?php echo $mid_cum;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text"></td><td class="span3"><b>Head Circumference</b></td>
			<td><input id="hd_cum" value="<?php echo $hd_cum;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text"></td>
		</tr>
		<tr>
			<td><b>BMI</b></td>
			<td><input id="bmi1" readonly="readonly" value="<?php echo $bmi1;?>" style="width:30px;" type="text"> <input id="bmi2" readonly="readonly" value="<?php echo $bmi2;?>" style="width:30px;" type="text"></td>
			<td><b>SPO<sub>2</sub>(%)</b></td>
			<td><input id="spo" type="text" value="<?php echo $spo;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" /></td>
			<td><b>Pulse</b></td>
			<td><input id="pulse" type="text" value="<?php echo $pulse;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" /></td>
			<td><b>Temperature (<sup>o</sup>C)</b></td>
			<td><input id="temp" value="<?php echo $temp;?>" onkeyup="tab(this.id,event)" class="span1" type="text" /></td>
		</tr>
		<tr>
			<td><b>PR</b></td>
			<td><input id="pr" value="<?php echo $pr;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
			<td><b>RR(/min)</b></td>
			<td><input id="rr" value="<?php echo $rr;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
			<td><b>BP:-</b> <b style="float:right;margin-right:10%;">Systolic:</b></td>
			<td><input id="systolic" value="<?php echo $systolic;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
			<td><b>Diastolic:</b></td>
			<td><input id="diastolic" value="<?php echo $diastolic;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
		</tr>
		<tr>
			<td><b>Intake Output</b></td>
			<td colspan="8">
				<div class="widget-content_con_doc_note">
					<div class="control-group">
						<div class="controls">
							<textarea class="textarea_editor span9" id="intake_output_record" rows="1" placeholder="Intake Output ..." style="resize:none;"><?php echo $intake_output_record;?></textarea>
						</div>
					</div>
				</div>
			</td>
			<td>
		</tr>
		<tr>
			<td><b>Note</b></td>
			<td colspan="3"><input type="text" id="vit_note" value="<?php echo $note;?>" onkeyup="tab(this.id,event)" style="width:90%;" /></td>
			<td style="width: 5%;">
				<b>Record Date <b style="color:#ff0000;">*</b></b>
			</td>
			<td>
				<input type="text" class="datepicker" id="record_date" value="<?php echo $record_date;?>" style="width: 72px;" />
				<input type="text" class="timepicker span1" id="record_time" value="<?php echo $record_time;?>" />
			</td>
			<td style="width: 5%;">
				<b>Record By <b style="color:#ff0000;">*</b></b>
			</td>
			<td>
				<select id="record_by">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `employee` WHERE `status`='0' AND `levelid` IN(5,11) ORDER BY `name`");
					while($r=mysqli_fetch_array($q))
					{
						if($record_by==$r["emp_id"]){ $sel="selected"; }else{ $sel=""; }
					?>
					<option value="<?php echo $r['emp_id'];?>" <?php echo $sel; ?>><?php echo $r['name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="8">
				<center>
					<input type="hidden" id="vital_id" value="<?php echo $id; ?>">
					<button type="button" id="sav_vit" class="btn btn-info" onclick="save_vital()" ><i class="icon-save"></i> <?php echo $val;?></button>
					<!--<button type="button" class="btn btn-danger" id="close_btn_doc_note" data-dismiss="modal"><i class="icon-remove"></i> Close</button>-->
					<button class="btn btn-inverse" onclick="vital()"><i class="icon-backward"></i> Back</button>
				</center>
			</td>
		</tr>
	</tbody>
</table>
<?php
}

if($_POST["type"]=="pat_ipd_vital_save")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$id=$_POST['vital_id'];
	$weight=$_POST['weight'];
	$height=$_POST['height'];
	$mid_cum=$_POST['mid_cum'];
	$hd_cum=$_POST['hd_cum'];
	$bmi1=$_POST['bmi1'];
	$bmi2=$_POST['bmi2'];
	$spo=$_POST['spo'];
	$pulse=$_POST['pulse'];
	$temp=$_POST['temp'];
	$pr=$_POST['pr'];
	$rr=$_POST['rr'];
	$systolic=$_POST['systolic'];
	$diastolic=$_POST['diastolic'];
	$vit_note=mysqli_real_escape_string($link, $_POST['vit_note']);
	$intake_output_record=mysqli_real_escape_string($link, $_POST['intake_output_record']);
	$record_by=$_POST['record_by'];
	$record_date=$_POST['record_date'];
	$record_time=$_POST['record_time'];
	$usr=$_POST['usr'];
	
	if(!$weight){ $weight=0; }
	if(!$height){ $height=0; }
	if(!$mid_cum){ $mid_cum=0; }
	if(!$hd_cum){ $hd_cum=0; }
	if(!$bmi1){ $bmi1=0; }
	if(!$bmi2){ $bmi2=0; }
	if(!$spo){ $spo=0; }
	if(!$pulse){ $pulse=0; }
	if(!$temp){ $temp=0; }
	if(!$pr){ $pr=0; }
	if(!$rr){ $rr=0; }
	if(!$systolic){ $systolic=0; }
	if(!$diastolic){ $diastolic=0; }
	if(!$record_by){ $record_by=0; }
	if(!$usr){ $usr=0; }
	
	//$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_vital` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$date'"));
	if($id>0)
	{
		mysqli_query($link,"UPDATE `ipd_pat_vital` SET `weight`='$weight',`height`='$height',`medium_circumference`='$mid_cum',`BMI_1`='$bmi1',`BMI_2`='$bmi2',`spo2`='$spo',`pulse`='$pulse',`head_circumference`='$hd_cum',`PR`='$pr',`RR`='$rr',`temp`='$temp',`systolic`='$systolic',`diastolic`='$diastolic',`note`='$vit_note',`intake_output_record`='$intake_output_record',`record_by`='$record_by',`record_date`='$record_date',`record_time`='$record_time',`date`='$date',`time`='$time',`user`='$usr' WHERE `id`='$id' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_vital`(`patient_id`, `ipd_id`, `weight`, `height`, `medium_circumference`, `BMI_1`, `BMI_2`, `spo2`, `pulse`, `head_circumference`, `PR`, `RR`, `temp`, `systolic`, `diastolic`, `note`, `intake_output_record`, `record_by`, `record_date`, `record_time`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$weight','$height','$mid_cum','$bmi1','$bmi2','$spo','$pulse','$hd_cum','$pr','$rr','$temp','$systolic','$diastolic','$vit_note','$intake_output_record','$record_by','$record_date','$record_time','$date','$time','$usr')");
		
		echo "Saved";
	}
}

?>
