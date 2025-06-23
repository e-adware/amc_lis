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
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$user=$_POST['user'];
		
	$q="SELECT * FROM `ot_book` WHERE `date` BETWEEN '$date1' AND '$date2'";
	//echo $q;
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>#</th>
				<!--<th>UHID</th>-->
				<th>PIN</th>
				<th>Name</th>
				<th>Sex</th>
				<th>Age (DOB)</th>
				<th>OT Type</th>
			</tr>
		<?php
		$j=1;
			while($r=mysqli_fetch_array($qq))
			{
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
				$rnum=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_pac_status` WHERE `patient_id`='$r[patient_id]' AND `ipd_id`='$r[ipd_id]'"));
				if($p["dob"]!=""){ $age=age_calculator($p["dob"])." (".$p["dob"].")"; }else{ $age=$p["age"]." ".$p["age_type"]; }
				if($r['scheduled']==0)
				{
					$stat="Not Sheduled";
				}
				if($r['scheduled']==1)
				{
					$stat="Sheduled";
				}
				$ot=mysqli_fetch_array(mysqli_query($link, " SELECT `ot_no`, `ot_type`, `ot_dept_id` FROM `ot_schedule` WHERE `schedule_id`='$r[schedule_id]'"));
				$ot_name=mysqli_fetch_array(mysqli_query($link, " SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$ot[ot_no]'"));
				if($ot['ot_type']==1)
				{
					$ot_type="Minor";
				}
				if($ot['ot_type']==2)
				{
					$ot_type="Major";
				}
				//$u=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
			?>
				<!--<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['ipd_id'];?>','<?php echo $st;?>','<?php echo $rnum;?>')" style="cursor:pointer;">-->
				<tr>
					<td><?php echo $j;?></td>
					<!--<td><?php echo $r['patient_id'];?></td>-->
					<td><?php echo $r['ipd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $age;?></td>
					<td><?php echo $ot_type;?></td>
				</tr>
			<?php
			$j++;
			}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="doc_wise_pat")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$user=$_POST['user'];
	
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Ref Doctor Name</th>
			<!--<th>UHID</th>-->
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Age / Sex</th>
			<th>Time</th>
			<th></th>
		</tr>
	<?php
	$j=1;
	$q=mysqli_query($link,"SELECT DISTINCT `requesting_doc` FROM `ot_schedule` WHERE `date` BETWEEN '$date1' AND '$date2'");
	while($r=mysqli_fetch_array($q))
	{
		$doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$r[requesting_doc]'"));
		$qq=mysqli_query($link,"SELECT * FROM `ot_schedule` WHERE `requesting_doc`='$r[requesting_doc]' AND `date` BETWEEN '$date1' AND '$date2'");
		$num=mysqli_num_rows($qq);
		while($rr=mysqli_fetch_array($qq))
		{
			if($rr['leaved']==0)
			{
				$styl="";
				$clas="btn-success";
				$titl="Schedule";
			}
			if($rr['leaved']==1)
			{
				$styl="";
				$clas="btn-danger";
				$titl="Leaved";
			}
			$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$rr[patient_id]'"));
		?>
		<tr>
			<?php
			if($num>0)
			{
			?>
			<td rowspan="<?php echo $num;?>"><?php echo $j;?></td>
			<td rowspan="<?php echo $num;?>"><?php echo $doc['Name'];?></td>
			<?php
			}
			?>
			<!--<td><?php echo $rr['patient_id'];?></td>-->
			<td><?php echo $rr['ipd_id'];?></td>
			<td><?php echo $p['name'];?></td>
			<td><?php echo $p['age']." / ".$p['sex'];?></td>
			<td><?php echo convert_time($rr['start_time'])." - ".convert_time($rr['end_time']);?></td>
			<td><button type="button" class="btn btn-mini <?php echo $clas;?> tip-top" title="<?php echo $titl;?>" disabled style="border-radius:50%;">O</button></td>
		</tr>
		<?php
		$num=0;
		}
		$j++;
	}
	?>
	</table>
	<style>
		.table tr:hover
		{
			background:none;
		}
	</style>
	<?php
}

if($_POST["type"]=="ot_type_rep")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<!--<th>UHID</th>-->
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Age / Sex</th>
		</tr>
	<?php
	$q=mysqli_query($link,"SELECT DISTINCT `ot_type` FROM `ot_schedule` WHERE `date` BETWEEN '$date1' AND '$date2' ORDER BY `ot_type`");
	while($r=mysqli_fetch_array($q))
	{
		if($r['ot_type']==1)
		{
			$ot_type="Minor";
		}
		if($r['ot_type']==2)
		{
			$ot_type="Major";
		}
		?>
		<tr>
			<th colspan="5"><?php echo $ot_type;?></th>
		</tr>
		<?php
		$qq=mysqli_query($link,"SELECT * FROM `ot_schedule` WHERE `ot_type`='$r[ot_type]' AND `date` BETWEEN '$date1' AND '$date2' ORDER BY `ot_type`");
		$num=mysqli_num_rows($qq);
		$j=1;
		while($rr=mysqli_fetch_array($qq))
		{
			$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$rr[patient_id]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<!--<td><?php echo $rr['patient_id'];?></td>-->
			<td><?php echo $rr['ipd_id'];?></td>
			<td><?php echo $p['name'];?></td>
			<td><?php echo $p['age']." / ".$p['sex'];?></td>
		</tr>
		<?php
		$j++;
		}
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="delivery_rep")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	?>
	<span class="text-right" id="hide_print">
		<button class="btn btn-info" onclick="delivery_rep('<?php echo $date1;?>','<?php echo $date2;?>','delivery_rep')"><i class="icon-print icon-large"></i> Print</button></span>
	<table class="table table-condensed table-bordered">
	<?php
	$date_qry=mysqli_query($link,"SELECT DISTINCT `delivery_mode` FROM `ipd_pat_delivery_det` WHERE `dob` BETWEEN '$date1' AND '$date2'");
	while($r_date=mysqli_fetch_assoc($date_qry))
	{
		?>
		<tr>
			<th colspan="8">Delivery Type : <?php echo $r_date['delivery_mode'];?></th>
		</tr>
		<tr>
			<th>#</th>
			<th>Baby Name</th>
			<th>Sex</th>
			<th>Baby PIN</th>
			<th>Born Date</th>
			<th>Born Time</th>
			<th>Weight</th>
			<th>Blood Group</th>
			<th>Delivery Type</th>
		</tr>
		<?php
		$i=1;
		$qry=mysqli_query($link,"SELECT * FROM `ipd_pat_delivery_det` WHERE `delivery_mode`='$r_date[delivery_mode]' AND `dob` BETWEEN '$date1' AND '$date2' ORDER BY `dob`");
		while($r=mysqli_fetch_assoc($qry))
		{
			$baby=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[baby_uhid]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $baby['name'];?></td>
			<td><?php echo $r['sex'];?></td>
			<td><?php echo $r['ipd_id'];?></td>
			<td><?php echo convert_date($r['dob']);?></td>
			<td><?php echo convert_time($r['born_time']);?></td>
			<td><?php echo $r['weight'];?></td>
			<td><?php echo $r['blood_group'];?></td>
			<td><?php echo $r['delivery_mode'];?></td>
		</tr>
		<?php
		$i++;
		}
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="ot_schedule_reason")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
}

?>
