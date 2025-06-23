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


if($_POST["type"]=="pat_ipd_insulin_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$view=$_POST['view'];
?>
	<button type="button" class="btn btn-primary" onclick="add_insulin(0)"><i class="icon-plus"></i> Add Insuline</button>
	<div id="insulin_data">
<?php
	$qry=mysqli_query($link,"SELECT * FROM `ipd_pat_insulin_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `given_date` DESC, `given_time` DESC");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		$n=1;
		while($data=mysqli_fetch_array($qry))
		{
			$insulin_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `insulin_type_master` WHERE `insulin_id`='$data[insulin_id]'"));
			
			$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$data[consultantdoctorid]'"));
			
			$user_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$data[given_by]'"));
?>
		<div><b>Date: <?php echo date("d-M-Y", strtotime($data['given_date']))." ".date("h:i A", strtotime($data['given_time']));?></b></div>
		<table class="table table-condensed table-bordered">
			<tr>
				<td colspan="6" style="background:#dddddd;"></td>
			</tr>
			<tr>
				<th>Insuline</th>
				<th>Dosage</th>
				<th>Note</th>
				<th>Doctor</th>
				<th>Given By</th>
				<th>Given Time</th>
			</tr>
			<tr>
				<td><?php echo $insulin_info["name"];?></td>
				<td><?php echo $data["dosage"];?></td>
				<td><?php echo $data["insulin_note"];?></td>
				<td><?php echo $doc_info["Name"];?></td>
				<td><?php echo $user_info['name'];?></td>
				<td>
					<?php echo date("d-M-Y", strtotime($data['given_date']));?>
					<?php echo date("h:i A", strtotime($data['given_time']));?>
					<button class="btn btn-info btn-mini" style="float:right;" onclick="add_insulin('<?php echo $data["slno"]; ?>')"><i class="icon-edit"></i> Edit</button>
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

if($_POST["type"]=="add_insulin")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$slno=$_POST['slno'];
	
	$qry=mysqli_query($link,"SELECT * FROM `ipd_pat_insulin_details` WHERE `slno`='$slno' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		$det=mysqli_fetch_array($qry);
		$insulin_id=$det['insulin_id'];
		$insulin_dosage=$det['dosage'];
		$insulin_note=$det['insulin_note'];
		$consultantdoctorid=$det['consultantdoctorid'];
		$given_by=$det['given_by'];
		$given_date=$det['given_date'];
		$given_time=$det['given_time'];
		$val="Update";
	}
	else
	{
		$insulin_id=0;
		$insulin_dosage="";
		$insulin_note="";
		$consultantdoctorid=0;
		$given_by=0;
		$given_date="";
		$given_time="";
		$val="Save";
	}
?>
<table class="table table-condensed">
	<tbody>
		<tr>
			<th>Insuline <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="insulin_id">
					<option value="0">Select</option>
			<?php
				$qry=mysqli_query($link, "SELECT `insulin_id`, `name` FROM `insulin_type_master` WHERE `status`=0 ORDER BY `name` ASC");
				while($data=mysqli_fetch_array($qry))
				{
					if($insulin_id==$data["insulin_id"]){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$data[insulin_id]' $sel>$data[name]</option>";
				}
			?>
				</select>
			</td>
			<th>Dosage <b style="color:#ff0000;">*</b></th>
			<td>
				<input type="text" id="insulin_dosage" value="<?php echo $insulin_dosage;?>" />
			</td>
			<th>Note</th>
			<td>
				<input type="text" id="insulin_note" value="<?php echo $insulin_note;?>">
			</td>
		</tr>
		<tr>
			<th>Doctor <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="insulin_consultantdoctorid">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT a.* FROM `consultant_doctor_master` a, `employee` b WHERE a.`emp_id`=b.`emp_id` AND b.`status`='0' ORDER BY `Name`");
					while($r=mysqli_fetch_array($q))
					{
						if($consultantdoctorid==$r["consultantdoctorid"]){ $sel="selected"; }else{ $sel=""; }
					?>
					<option value="<?php echo $r['consultantdoctorid'];?>" <?php echo $sel; ?>><?php echo $r['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<th>Given By <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="insulin_given_by">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `employee` WHERE `status`='0' AND `levelid` IN(5,11) ORDER BY `name`");
					while($r=mysqli_fetch_array($q))
					{
						if($given_by==$r["emp_id"]){ $sel="selected"; }else{ $sel=""; }
					?>
					<option value="<?php echo $r['emp_id'];?>" <?php echo $sel; ?>><?php echo $r['name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<th>Given Date <b style="color:#ff0000;">*</b></th>
			<td>
				<input type="text" class="datepicker" id="insulin_given_date" value="<?php echo $given_date;?>" style="width: 72px;" />
				<input type="text" class="timepicker span1" id="insulin_given_time" value="<?php echo $given_time;?>" />
			</td>
		</tr>
		<tr>
			<td colspan="6">
				<center>
					<input type="hidden" id="insulin_slno" value="<?php echo $slno; ?>">
					<button type="button" id="sav_insulin" class="btn btn-info" onclick="save_insulin()" ><i class="icon-save"></i> <?php echo $val;?></button>
					<button class="btn btn-inverse" onclick="insulin()"><i class="icon-backward"></i> Back</button>
				</center>
			</td>
		</tr>
	</tbody>
</table>
<?php
}


if($_POST["type"]=="pat_ipd_insulin_save")
{
	//print_r($_POST);
	
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$ipd=mysqli_real_escape_string($link, $_POST["ipd"]);
	$slno=mysqli_real_escape_string($link, $_POST["insulin_slno"]);
	$insulin_id=mysqli_real_escape_string($link, $_POST["insulin_id"]);
	$dosage=mysqli_real_escape_string($link, $_POST["insulin_dosage"]);
	$insulin_note=mysqli_real_escape_string($link, $_POST["insulin_note"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$given_by=mysqli_real_escape_string($link, $_POST["insulin_given_by"]);
	$given_date=mysqli_real_escape_string($link, $_POST["insulin_given_date"]);
	$given_time=mysqli_real_escape_string($link, $_POST["insulin_given_time"]);
	$user=mysqli_real_escape_string($link, $_POST["usr"]);
	
	if($slno==0)
	{
		if(mysqli_query($link," INSERT INTO `ipd_pat_insulin_details`(`patient_id`, `ipd_id`, `insulin_id`, `dosage`, `insulin_note`, `consultantdoctorid`, `given_by`, `given_date`, `given_time`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$insulin_id','$dosage','$insulin_note','$consultantdoctorid','$given_by','$given_date','$given_time','$user','$date','$time') "))
		{
			echo "Saved";
		}
		else
		{
			echo "Failed, try gain later.";
		}
	}
	else
	{
		if(mysqli_query($link," UPDATE `ipd_pat_insulin_details` SET `insulin_id`='$insulin_id',`dosage`='$dosage',`insulin_note`='$insulin_note',`consultantdoctorid`='$consultantdoctorid',`given_by`='$given_by',`given_date`='$given_date',`given_time`='$given_time',`user`='$user',`date`='$date',`time`='$time' WHERE `slno`='$slno' "))
		{
			echo "Updated";
		}
		else
		{
			echo "Failed, try gain later.";
		}
	}
}
?>
