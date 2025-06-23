<?php
session_start();
include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$type=$_POST['type'];

if($type=="load_emp")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$emp=mysqli_query($link,"SELECT `emp_id`,`name`,`password`,`levelid` FROM `employee` WHERE `name` like '$srch%' ORDER BY `name`");
	}
	else
	{
		$emp=mysqli_query($link,"SELECT `emp_id`,`name`,`password`,`levelid` FROM `employee` ORDER BY `name`");
	}
	?>
	<span style="float:right;">
		<button type="button" class="btn btn-mini btn-primary" onclick="print_page()"><b class="icon-print icon-large"></b> Print</button>
	</span>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th>
			<th>Name</th>
		<?php if($emp_info["levelid"]==1){ ?>
			<th>Employee Type</th>
			<th>Application Access</th>
		<?php } ?>
		</tr>
		<?php
		$i=1;
		while($e=mysqli_fetch_array($emp))
		{
			$l=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `level_master` WHERE `levelid`='$e[levelid]' "));
			if($e['password'])
			$accs="Yes";
			else
			$accs="No";
			
			if($e['emp_id']=="101" || $e['emp_id']=="102")
			{
			}else
			{
		?>
		<tr onclick="goedit('<?php echo $e['emp_id'];?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $e['name'];?></td>
		<?php if($emp_info["levelid"]==1){ ?>
			<td><?php echo $l["name"]; ?></td>
			<td><?php echo $accs; ?></td>
		<?php } ?>
		</tr>
		<?php
				$i++;
			}
		}
		?>
	</table>
	<?php
}

if($type=="load_doctor")
{
	$srch=$_POST['srch'];
	$doc_speclty=$_POST['doc_speclty'];
	$user_str="";
	if($doc_speclty>0)
	{
		$user_str=" AND `dept_id`='$doc_speclty'";
	}
	if($srch)
	{
		$emp=mysqli_query($link,"SELECT * FROM `consultant_doctor_master` WHERE `Name` like '%$srch%' $user_str ORDER BY `Name`");
	}
	else
	{
		$emp=mysqli_query($link,"SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`>0 $user_str ORDER BY `Name`");
	}
	?>
	<span style="float:right;">
		<button type="button" class="btn btn-mini btn-primary" onclick="print_page()"><b class="icon-print icon-large"></b> Print</button>
	</span>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>Qualification</th>
			<th>Designation</th>
			<th>Department</th>
			<th>OPD Visit Fee</th>
			<th>OPD Validity</th>
			<th>OPD Reg Fee</th>
			<th>OPD Reg Validity</th>
			<!--<th>IPD Visit Fee</th>-->
			<!--<th>Application Access</th>-->
		</tr>
		<?php
		$i=1;
		while($e=mysqli_fetch_array($emp))
		{
			$acc=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$e[emp_id]' "));
			$sp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id` IN (SELECT `speciality_id` FROM `employee` WHERE `emp_id`='$e[emp_id]') "));
			if($acc['password'])
			$accs="Yes";
			else
			$accs="No";
			//~ if($e['opd_visit_fee']>0)
			$opd_fee="Rs ".number_format($e['opd_visit_fee'],2);
			$opd_reg_fee="Rs ".number_format($e['opd_reg_fee'],2);
			//~ else
			//~ $opd_fee="0";
			//~ if($e['ipd_visit_fee']>0)
			$ipd_fee="Rs ".number_format($e['ipd_visit_fee'],2);
			//~ else
			//~ $ipd_fee="0";
		?>
		<tr onclick="goedit('<?php echo $e['consultantdoctorid'];?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $e['Name'];?></td>
			<td><?php echo $acc["qualification"]; ?></td>
			<td><?php echo $e["Designation"]; ?></td>
			<td><?php echo $sp["name"]; ?></td>
			<td><?php echo $opd_fee; ?></td>
			<td><?php echo $e["validity"]; ?></td>
			<td><?php echo $opd_reg_fee; ?></td>
			<td><?php echo $e["opd_reg_validity"]; ?></td>
			<!--<td><?php echo $ipd_fee; ?></td>-->
			<!--<td><?php echo $accs; ?></td>-->
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}

if($type=="oo")
{
	
}
?>
