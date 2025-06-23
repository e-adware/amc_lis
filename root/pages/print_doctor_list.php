<?php
include("../../includes/connection.php");

$n=1;
$q=mysqli_query($link,"SELECT * FROM `consultant_doctor_master` ORDER BY `Name`");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>Employee List</title>
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
</head>

<body>
	<div class="container-fluid">
		<center>
			<h3>Consultant Doctor List</h3>
		</center>
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
			while($e=mysqli_fetch_array($q))
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
	</div>
</body>
</html>
<script>window.print()</script>
<style>
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
}
.table
{
	margin-top:20px;
	margin-bottom:10px;
}
}
</style>

