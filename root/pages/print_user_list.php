<?php
session_start();
include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
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
			<h3>Employee List</h3>
		</center>
		<table class="table table-bordered text-center">
			<tr>
				<th>#</th>
				<th>Employee Name</th>
			<?php if($emp_info["levelid"]==1){ ?>
				<th>Employee Type</th>
				<th>Application Access</th>
			<?php } ?>
			</tr>
		<?php
			$n=1;
			$q=mysqli_query($link, " SELECT `emp_id`,`name`,`password`,`levelid` FROM `employee` ORDER BY `name` ");
			while($r=mysqli_fetch_array($q))
			{
				$l=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `level_master` WHERE `levelid`='$r[levelid]' "));
				if($r['password'])
				$accs="Yes";
				else
				$accs="No";
				
				if($r['emp_id']=="101" || $r['emp_id']=="102")
				{
				}else
				{
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $r["name"]; ?></td>
				<?php if($emp_info["levelid"]==1){ ?>
					<td><?php echo $l["name"]; ?></td>
					<td><?php echo $accs; ?></td>
				<?php } ?>
				</tr>
			<?php
				$n++;
				}
			}
		?>
		</table>
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

