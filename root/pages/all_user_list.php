<?php
include("../../includes/connection.php");

$branch_id=base64_decode($_GET["bid"]);

$exclude=" AND `emp_id` NOT IN('101','102')";

$all_user_qry=mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`>0 $exclude AND `branch_id`='$branch_id' ORDER BY `name` ");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>User List</title>
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
</head>

<body>
	<div class="container-fluid">
		<center>
			<h3>User List</h3>
		</center>
	  <table width="100%">
		  <tr>
			<td colspan="6" style="text-align:right"><div class="noprint"><input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div></td>
		  </tr>
	  </table>
		<table class="table table-bordered text-center">
			<tr>
				<th>#</th>
				<th>Employee Name</th>
				<th>Employee Type</th>
			</tr>
		<?php
			$n=1;
			while($all_user=mysqli_fetch_array($all_user_qry))
			{
				$user_level=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `level_master` WHERE `levelid`='$all_user[levelid]' "));
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $all_user["name"]; ?></td>
					<td><?php echo $user_level["name"]; ?></td>
				</tr>
			<?php
				$n++;
			}
		?>
		</table>
</body>
</html>
<script>//window.print()</script>
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
@media print{
 input[type="button"]{
	 display:none;
 }
}
</style>

