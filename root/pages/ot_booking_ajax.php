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


if($_POST["type"]=="search_patient_list_ipd")
{
	$ward=$_POST['ward'];
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	$ward=0;
	
	$q=" SELECT * FROM `uhid_and_opdid` WHERE `type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='3') ";
	$input=0;
	
	if($uhid)
	{
		$q.=" AND `patient_id` like '$uhid%' ";
		$input=1;
		
		//$q="SELECT DISTINCT a.* FROM `ipd_pat_bed_details` a, `ot_book` b WHERE a.`patient_id`!=b.`patient_id` AND a.`ipd_id`!=b.`ipd_id` AND a.`patient_id`='$uhid' ORDER BY a.`date` DESC LIMIT 0,20";
	}
	if($ipd)
	{
		$q.=" AND `opd_id` like '$ipd%' ";
		$input=1;
		
		//$q="SELECT DISTINCT a.* FROM `ipd_pat_bed_details` a, `ot_book` b WHERE a.`patient_id`!=b.`patient_id` AND a.`ipd_id`!=b.`ipd_id` AND a.`ipd_id`='$ipd' ORDER BY a.`date` DESC LIMIT 0,20";
	}
	if($name)
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$name%' ) ";
		$input=1;
		
		//$q="SELECT DISTINCT a.* FROM `ipd_pat_bed_details` a, `ot_book` b, `patient_info` c WHERE a.`patient_id`!=b.`patient_id` AND a.`ipd_id`!=b.`ipd_id` AND a.`patient_id`=c.`patient_id` AND c.`name` like '%$name%' ORDER BY a.`date` DESC LIMIT 0,20";
	}
	if($dat)
	{
		$q=" SELECT * FROM `uhid_and_opdid` WHERE `date`='$dat' ";
		$input=1;
		
		//$q="SELECT DISTINCT a.* FROM `ipd_pat_bed_details` a, `ot_book` b WHERE a.`patient_id`!=b.`patient_id` AND a.`ipd_id`!=b.`ipd_id` AND a.`date`='$dat' ORDER BY a.`date` DESC LIMIT 0,20";
	}
	
	if($input==0)
	{
		$q=" SELECT * FROM `uhid_and_opdid` WHERE `opd_id` IN(SELECT `ipd_id` FROM `ot_book`) ";
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
				<th>Age (DOB)</th>
				<th>Ward / Bed No.</th>
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
				
				$rnum=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_pac_status` WHERE `patient_id`='$r[patient_id]' AND `ipd_id`='$r[opd_id]'"));
				
				if($p["dob"]!=""){ $age=age_calculator($p["dob"])." (".convert_date_g($p["dob"]).")"; }else{ $age=$p["age"]." ".$p["age_type"]; }
				
				$pat_bed=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$r[patient_id]' AND `ipd_id`='$r[opd_id]' "));
				
				$w=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_bed[ward_id]'"));
				
				$b=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no` FROM `bed_master` WHERE `bed_id`='$pat_bed[bed_id]'"));
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['opd_id'];?>','<?php echo $st;?>','<?php echo $rnum;?>')" style="cursor:pointer;">
					<td><?php echo $r['patient_id'];?></td>
					<td><?php echo $r['opd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $age;?></td>
					<td><?php echo $w['name']." / ".$b['bed_no'];?></td>
				</tr>
			<?php
			}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="ot_schedule_reason")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$reason=$_POST['reason'];
	$reason= str_replace("'", "''", "$reason");
	$usr=$_POST['usr'];
	mysqli_query($link,"INSERT INTO `ot_pac_status`(`patient_id`, `ipd_id`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$reason','$date','$time','$usr')");
}

if($_POST["type"]=="ot_scheduling")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$r=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid`,`name`,`sex`,`dob`,`age`,`age_type` FROM `patient_info` WHERE `patient_id`='$uhid'"));
	if($r['scheduled']=="0")
	$sh="Not scheduled";
	if($r['scheduled']=="1")
	$sh="Scheduled";
	$j=1;
	$qry=mysqli_query($link,"SELECT * FROM `ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		while($r=mysqli_fetch_array($qry))
		{
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th colspan="5" style="background:#cccccc;"><?php if($num>1){echo "Schedule ".$j;}?></th>
			</tr>
			<tr>
				<th>Schedule No</th>
				<th>OT Date</th>
				<th>Start Time</th>
				<th>End Time</th>
				<th>OT No</th>
			</tr>
			<tr>
				<td><?php echo $r['schedule_id'];?></td>
				<td><?php echo $r['ot_date'];?></td>
				<td><?php echo $r['start_time'];?></td>
				<td><?php echo $r['end_time'];?></td>
				<td><?php echo $r['ot_no'];?></td>
			</tr>
			<?php
			if($r['remarks']!="")
			{
			?>
			<tr>
				<th>Remarks</th>
				<td colspan="4"><?php echo $r['remarks'];?></td>
			</tr>
			<?php
			}
			$qq=mysqli_query($link,"SELECT * FROM `ot_resource` WHERE `schedule_id`='$r[schedule_id]'");
			$nn=mysqli_num_rows($qq);
			if($nn>0)
			{
			?>
			<tr>
				<td colspan="5" style="background:#dddddd;"></td>
			</tr>
			<tr>
				<th colspan="5" style="text-align:center;">OT Resources</th>
			</tr>
			<tr>
				<th>SN</th>
				<th>Resource</th>
				<th>Employee</th>
				<th></th>
				<th></th>
			</tr>
			<?php
			$i=1;
			while($rr=mysqli_fetch_array($qq))
			{
				$res=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$rr[resourse_id]'"));
				$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$rr[emp_id]'"));
			?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $res['type'];?></td>
				<td><?php echo $emp['name'];?></td>
				<td></td>
				<td></td>
			</tr>
			<?php
			$i++;
			}
			?>
			<tr>
				<th colspan="5" style="background:#dddddd;"></th>
			</tr>
			<?php
			}
			?>
			
		</table>
		<?php
		$j++;
		}
	}
	else
	{
		$o=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	?>
	<table class="table table-condensed table-bordered" id="ot_tbl">
		<tr>
			<th>Select OT</th>
			<th>
				<select id="ot">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `ot_area_id`,`ot_area_name` FROM `ot_area_master` ORDER BY `ot_area_name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['ot_area_id'];?>" <?php if($r['ot_area_id']==$o['ot_area_id']){echo "selected='selected'";}?>><?php echo $r['ot_area_name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th>Select Procedure</th>
			<th>
				<select id="pr">
					<option value="0">Select</option>
					<?php
					$qr=mysqli_query($link,"SELECT `id`,`name` FROM `clinical_procedure` ORDER BY `name`");
					while($rr=mysqli_fetch_array($qr))
					{
					?>
					<option value="<?php echo $rr['id'];?>" <?php if($rr['id']==$o['procedure_id']){echo "selected='selected'";}?>><?php echo $rr['name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
		</tr>
		<tr>
			<th>Date</th>
			<th><input type="text" id="ot_date" value="<?php echo $o['ot_date'];?>" placeholder="Date" /></th>
			<th>Requesting Doctor</th>
			<th>
				<select id="doc">
					<option value="0">Select</option>
					<?php
					$qry=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='5'");
					while($rrr=mysqli_fetch_array($qry))
					{
					?>
					<option value="<?php echo $rrr['emp_id'];?>" <?php if($rrr['emp_id']==$o['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $rrr['name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
		</tr>
		<tr>
			<th>Start Time</th>
			<th><input type="text" id="st_time" placeholder="Start Time" /></th>
			<th>End Time</th>
			<th><input type="text" id="en_time" placeholder="End Time" /></th>
		</tr>
		<tr>
			<th>Remarks</th>
			<th colspan="3"><textarea id="rem" placeholder="Remarks" style="width:80%;resize:none;"></textarea></th>
		</tr>
		<tr>
			<th>OT Resources</th>
			<th>
				<select id="ot_type" onchange="ot_resource_list()">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `ot_type_master` ORDER BY `type`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['type_id'];?>"><?php echo $r['type'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th id="resource_list">
				<select id="rs">
					<option value="0">Select</option>
				</select>
			</th>
			<th>
				<input type="text" id="sel_val" style="display:none;" />
				<span class="text-right"><button type="button" class="btn btn-primary" onclick="add_res()"><i class="icon icon-plus"></i> Add</button></span>
			</th>
		</tr>
		<tr id="end_tr">
			<td colspan="4" style="text-align:center;"><button type="button" class="btn btn-primary" onclick="save_shed()"><i class="icon icon-save"></i> Save</button></td>
		</tr>
	</table>
	<script>
		$("#ot_date").datepicker({dateFormat: 'yy-mm-dd'});
		$("#st_time").timepicker({minutes: {starts: 0,interval: 05,}});
		$("#en_time").timepicker({minutes: {starts: 0,interval: 05,}});
	</script>
	<?php
	}
}
if($_POST["type"]=="cancel_ot_book")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	mysqli_query($link,"DELETE FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	echo "Booking cancelled";
}

if($_POST["type"]=="oooo")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
}
?>
