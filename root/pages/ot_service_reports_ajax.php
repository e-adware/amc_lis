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

if($_POST["type"]==1)
{
	$res_id=$_POST['res_id'];
	$user=$_POST['user'];
	
	if($res_id>0)
	{
	$lk=mysqli_fetch_array(mysqli_query($link,"SELECT `link` FROM `ot_type_master` WHERE `type_id`='$res_id'"));
	$disb="disabled='disabled'";
	if($lk['link']>0)
	{
		$disb="";
	}
	?>
	<select id="emp_id" <?php echo $disb;?>>
		<option value="0">Select all</option>
	<?php
	if($lk['link']>0)
	{
		$q=mysqli_query($link,"SELECT DISTINCT `emp_id` FROM `ot_resource_master` WHERE `type_id`='$res_id'");
		while($r=mysqli_fetch_array($q))
		{
			$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[emp_id]'"));
			?>
			<option value="<?php echo $r['emp_id'];?>"><?php echo $e['name'];?></option>
			<?php
		}
	}
	?>
	</select>
	<?php
	}
	else
	{
		?>
		<select id="emp_id" onchange="search_rep()">
			<option value="0">Select</option>
			<?php
			$q=mysqli_query($link,"SELECT DISTINCT `emp_id` FROM `ot_resource_master`");
			while($r=mysqli_fetch_array($q))
			{
				$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[emp_id]'"));
			?>
			<option value="<?php echo $r['emp_id'];?>"><?php echo $e['name'];?></option>
			<?php
			}
			?>
		</select>
		<?php
	}
}

if($_POST["type"]==2)
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$res_id=$_POST['res_id'];
	$emp_id=$_POST['emp_id'];
	
	$qry="SELECT a.`patient_id`, a.`ipd_id`, a.`schedule_id`, a.`resourse_id`, a.`emp_id`, a.`ot_service_id`, a.`amount`, a.`bed_id`, b.`procedure_id`, b.`consultantdoctorid`, b.`ot_date` FROM `ot_pat_service_details` a, `ot_book` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`ipd_id` AND a.`schedule_id`=b.`schedule_id` AND b.`ot_date` BETWEEN '$date1' AND '$date2'";
	if($res_id)
	{
		$qry.=" AND a.`resourse_id`='$res_id'";
	}
	if($emp_id)
	{
		$qry.=" AND a.`emp_id`='$emp_id'";
	}
	
	//echo $qry;
	$q=mysqli_query($link,$qry);
	?>
	<table class="table table-condensed">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>PIN</th>
			<th>Name</th>
			<th>Procedure</th>
			<th>Grade</th>
			<th>Resourse</th>
			<th>Employee</th>
			<th style="text-align:right;">Amount</th>
			<th>OT Date</th>
		</tr>
		<?php
		$tot=0;
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			$proced=mysqli_fetch_array(mysqli_query($link,"SELECT `name`,`grade_id` FROM `clinical_procedure` WHERE `procedure_id`='$r[procedure_id]'"));
			$grade=mysqli_fetch_array(mysqli_query($link,"SELECT `grade_name` FROM `ot_grade_master` WHERE `grade_id`='$proced[grade_id]'"));
			$res=mysqli_fetch_array(mysqli_query($link,"SELECT `type`,`link` FROM `ot_type_master` WHERE `type_id`='$r[resourse_id]'"));
			$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[emp_id]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['patient_id'];?></td>
			<td><?php echo $r['ipd_id'];?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $proced['name'];?></td>
			<td><?php echo $grade['grade_name'];?></td>
			<td><?php echo $res['type'];?></td>
			<td><?php echo $emp['name'];?></td>
			<td style="text-align:right;"><?php echo number_format($r['amount'],2);?></td>
			<td><?php echo convert_date_g($r['ot_date']);?></td>
		</tr>
		<?php
		$tot+=$r['amount'];
		$i++;
		}
		?>
		<tr>
			<th colspan="8" style="text-align:right;">Total :</th>
			<th style="text-align:right;"><?php echo number_format($tot,2);?></th>
			<th></th>
		</tr>
	</table>
	<?php
}

if($_POST["type"]==999)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
}

?>
