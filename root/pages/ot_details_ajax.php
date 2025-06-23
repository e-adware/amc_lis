<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");
$time=date("H:i:s");

//---------------------------------------------------------------------------------------------------//
function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
//-------------------------------------------------------------------------------------------------//


if($_POST["type"]=="ot_pat_list")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	
	$q="SELECT DISTINCT a.`patient_id`,a.`ipd_id`,a.`scheduled`,a.`schedule_id` FROM `ot_book` a, `ot_schedule` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.ipd_id AND b.`leaved`='0' ORDER BY a.`ot_date` DESC LIMIT 0,50";
	if($uhid)
	{
		if(strlen($uhid)>2)
		{
			$q="SELECT * FROM `ot_book` WHERE `patient_id` like '$uhid%' ORDER BY `ot_date` DESC";
		}
	}
	if($ipd)
	{
		if(strlen($ipd)>2)
		{
			$q="SELECT * FROM `ot_book` WHERE `ipd_id` like '$ipd%' ORDER BY `ot_date` DESC LIMIT 0,20";
		}
	}
	if($name)
	{
		if(strlen($name)>2)
		{
			$q="SELECT * FROM `ot_book` WHERE `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$name%') ORDER BY `ot_date` DESC LIMIT 0,20";
		}
	}
	if($dat)
	{
		$q="SELECT * FROM `ot_book` WHERE `ot_date`='$dat' ORDER BY `ot_date` DESC LIMIT 0,20";
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
				<th>IPD ID</th>
				<th>Name</th>
				<th>Age</th>
				<th>Sex</th>
				<th>Encounter</th>
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				$p_typ=mysqli_fetch_array(mysqli_query($link,"SELECT a.`type`,b.`p_type` FROM `uhid_and_opdid` a, `patient_type_master` b WHERE a.`patient_id`='$r[patient_id]' AND a.`opd_id`='$r[ipd_id]' AND a.`type`=b.`p_type_id`"));
				
				$ot_entry=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$r[patient_id]' AND `ipd_id`='$r[ipd_id]'"));
				if($ot_entry)
				{
					$btn_name="Added to Bill";
				}else
				{
					$btn_name="Add to Bill";
				}
				
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['ipd_id'];?>','<?php echo $r['schedule_id'];?>')" style="cursor:pointer;">
					<td><?php echo $r['patient_id'];?></td>
					<td><?php echo $r['ipd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['age']." ".$p['age_type'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $p_typ['p_type'];?></td>
				</tr>
			<?php
			}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="ot_pat_details")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>OT Type</th>
			<td>
				<select id="ot_typ">
					<option value="0">Select</option>
					<option value="1" <?php if($det['ot_type']=="1"){echo "selected='selected'";}?>>Minor</option>
					<option value="2" <?php if($det['ot_type']=="2"){echo "selected='selected'";}?>>Major</option>
				</select>
			</td>
			<th>Department</th>
			<td>
				<select class="span3" disabled>
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `ot_dept_master` ORDER BY `ot_dept_name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['ot_dept_id'];?>" <?php if($r['ot_dept_id']==$det['ot_dept_id']){echo "selected='selected'";}?>><?php echo $r['ot_dept_name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Procedure</th>
			<td>
				<select id="pr" class="span4">
					<option value="0">Select</option>
					<?php
					$dept=$det['ot_dept_id'];
					$qry="SELECT `procedure_id`,`name` FROM `ot_clinical_procedure` WHERE `ot_dept_id`='$dept' ";
					$qry.="ORDER BY `name`";
					$qr=mysqli_query($link,$qry);
					while($rr=mysqli_fetch_array($qr))
					{
					?>
					<option value="<?php echo $rr['procedure_id'];?>" <?php if($rr['procedure_id']==$det['procedure_id']){echo "selected='selected'"; }?>><?php echo $rr['name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<th>Refer Doctor</th>
			<td>
				<select id="rf_doc">
					<option value="0">Select</option>
					<?php
					$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($rrr=mysqli_fetch_array($qry))
					{
					?>
					<option value="<?php echo $rrr['consultantdoctorid'];?>" <?php if($rrr['consultantdoctorid']==$det['requesting_doc']){echo "selected='selected'";}?>><?php echo $rrr['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>OT Date</th>
			<td>
				<input type="text" id="ot_date" value="<?php echo $det['ot_date'];?>" placeholder="OT Date" />
			</td>
			<th>Anesthesia type</th>
			<td>
				<select id="anas" class="span3">
					<option value="0">Select</option>
					<?php
					$qq = mysqli_query($link,"SELECT * FROM `ot_anesthesia_types` ORDER BY `name`");
					while($cc=mysqli_fetch_array($qq))
					{
						?>
						<option value="<?php echo $cc['anesthesia_id'];?>" <?php if($cc['anesthesia_id']==$det['anesthesia_id']){echo "selected='selected'";}?>><?php echo $cc['name'];?></option>
						<?php
					}
					?>
				</select>
			</td>
		</tr>
		<?php
		if($det['start_time']!="00:00:00")
		{
			$s_tim=explode(":",$det['start_time']);
			$st_tim=$s_tim[0].":".$s_tim[1];
		}
		else
		{
			$st_tim="";
		}
		if($det['end_time']!="00:00:00")
		{
			$e_tim=explode(":",$det['end_time']);
			$en_tim=$e_tim[0].":".$e_tim[1];
		}
		else
		{
			$en_tim="";
		}
		?>
		<tr>
			<th>Start Time</th>
			<td><input type="text" id="st_time" value="<?php echo $st_tim;?>" placeholder="Start Time" /></td>
			<th>End Time</th>
			<td><input type="text" id="en_time" value="<?php echo $en_tim;?>" placeholder="End Time" /></td>
		</tr>
		<?php
		$j=1;
		$resourse_qry=mysqli_query($link,"SELECT * FROM `ot_resource` WHERE `schedule_id`='$shed' AND `emp_id`!='0'");
		while($res=mysqli_fetch_assoc($resourse_qry))
		{
			$ot_type=mysqli_fetch_assoc(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$res[resourse_id]'"));
		?>
		<tr class="sel_type">
			<th>
				<?php echo $ot_type['type'];?>
			</th>
			<td colspan="2">
				<select class="span3" id="sel<?php echo $j;?>" onchange="change_ot_res('<?php echo $j;?>')">
					<?php
					$inp_val="";
					$typ_qry=mysqli_query($link,"SELECT * FROM `ot_resource_link` WHERE `type_id`='$res[resourse_id]'");
					while($typs=mysqli_fetch_assoc($typ_qry))
					{
						$emp=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$typs[emp_id]'"));
						$ot_emp=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ot_resource` WHERE `schedule_id`='$shed' AND `resourse_id`='$res[resourse_id]' AND `emp_id`='$typs[emp_id]'"));
						if($ot_emp)
						{
							$sel="selected='selected'";
							$inp_val=$typs['emp_id'];
						}
						else
						{
							$sel="";
						}
					?>
					<option value="<?php echo $typs['emp_id'];?>" <?php echo $sel;?>><?php echo $emp['name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				<input type="hidden" id="rs<?php echo $j;?>" disabled value="<?php echo $res['resourse_id'];?>" />
				<input type="hidden" id="inp<?php echo $j;?>" disabled value="<?php echo $inp_val;?>" />
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
		<tr>
			<th>Diagnosis</th>
			<th colspan="3"><input type="text" id="diag" placeholder="Diagnosis" value="<?php echo $det['diagnosis'];?>" style="width:80%;"></th>
		</tr>
		<tr>
			<th>Remarks</th>
			<th colspan="3"><textarea id="rem" placeholder="Remarks" style="width:80%;resize:none;"><?php echo $det['remarks'];?></textarea></th>
		</tr>
		<tr>
			<td colspan="4" style="text-align:center;">
				<button type="button" id="btn_upd" class="btn btn-primary" onclick="ot_details_update()">Update</button>
			</td>
		</tr>
	</table>
	<?php
}

if($_POST["type"]=="ot_details_update")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	$ot_typ=$_POST['ot_typ'];
	$pr=$_POST['pr'];
	$rf_doc=$_POST['rf_doc'];
	$ot_date=$_POST['ot_date'];
	$anas=$_POST['anas'];
	$st_time=$_POST['st_time'];
	$en_time=$_POST['en_time'];
	$diag=mysqli_real_escape_string($link,$_POST['diag']);
	$rem=mysqli_real_escape_string($link,$_POST['rem']);
	$user=$_POST['user'];
	$all=$_POST['all'];
	
	
	$v=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	$cnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) AS cnt FROM `ot_schedule_update` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$count=$cnt['cnt']+1;
	
	if(mysqli_query($link,"UPDATE `ot_schedule` SET `ot_date`='$ot_date',`start_time`='$st_time',`end_time`='$en_time',`ot_type`='$ot_typ',`anesthesia_id`='$anas',`diagnosis`='$diag',`remarks`='$rem',`requesting_doc`='$rf_doc',`procedure_id`='$pr' WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"))
	{
		mysqli_query($link,"INSERT INTO `ot_schedule_update`(`schedule_id`, `patient_id`, `ipd_id`, `ot_date`, `start_time`, `end_time`, `ot_type`, `anesthesia_id`, `diagnosis`, `remarks`, `requesting_doc`, `procedure_id`, `date`, `time`, `user`, `counter`) VALUES ('$shed','$uhid','$ipd','$v[ot_date]','$v[start_time]','$v[end_time]','$v[ot_type]','$v[anesthesia_id]','$v[diagnosis]','$v[remarks]','$v[requesting_doc]','$v[procedure_id]','$date','$time','$user','$count')");
		
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@",$a);
			$res_id=$v[0];
			$emp_id=$v[1];
			if($res_id && $emp_id)
			{
				$old=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ot_resource` WHERE `schedule_id`='$shed' AND `resourse_id`='$res_id'"));
				mysqli_query($link,"INSERT INTO `ot_resource_update`(`schedule_id`, `resourse_id`, `emp_id`, `counter`) VALUES ('$shed','$old[resourse_id]','$old[emp_id]','$count')");
				mysqli_query($link,"UPDATE `ot_resource` SET `emp_id`='$emp_id' WHERE `schedule_id`='$shed' AND `resourse_id`='$res_id'");
			}
		}
		echo "Updated";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="ot_schedule_reason")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
}

?>
