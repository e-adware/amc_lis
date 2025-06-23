<?php
include('../../includes/connection.php');
require('../../includes/global.function.php');

$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
//$date=date("Y-m-d");

?>
<html>
	<head>
		<title>Daily Expense Print</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../css/custom.css" />
		<style>
			.table{font-size:12px;}
		</style>
	</head>
	<body>
		<div class="container-fluid">
			<?php
			include('page_header.php');
			?>
			<hr/>
		<?php
			if($fdate=="" && $tdate=="")
			{
				$q=mysqli_query($link,"SELECT * FROM `expense_detail`");
			}
			else
			{
				$q=mysqli_query($link,"SELECT * FROM `expense_detail` WHERE `date` BETWEEN '$fdate' AND '$tdate'");
			}
		?>
			<center><h5><u>Daily Expense</u></h5></center>
			<?php
			if($fdate && $tdate)
			{
			?>
			From : <b><?php echo convert_date($fdate);?></b> to <b><?php echo convert_date($tdate);?></b>
			<?php
			}
			?>
			<table class="table table-condensed table-bordered">
				<tr>
					<th>#</th>
					<th>Details</th>
					<th>Description</th>
					<th>Amount</th>
					<th>Date</th>
					<th>User</th>
				</tr>
				<?php
				$i=1;
				$tot=0;
				while($r=mysqli_fetch_array($q))
				{
					$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
					$c=mysqli_fetch_array(mysqli_query($link, " SELECT `cat_name` FROM `category_master` WHERE `cat_id`='$r[details]' "));
					$tot=$tot+$r["amount"];
				?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $c["cat_name"]; ?></td>
					<td><?php echo $r["description"]; ?></td>
					<td>&#x20b9; <?php echo number_format($r["amount"],2); ?></td>
					<td><?php echo convert_date($r["date"]); ?></td>
					<td><?php echo $emp["name"]; ?></td>
				</tr>
				<?php
				$i++;
				}
				?>
				<tr>
					<td colspan="3"><span class="text-right"><b>Total</b></span></td>
					<td colspan="3"><span class="text-left"><b>&#x20b9; <?php echo number_format($tot,2); ?></b></span></td>
				</tr>
			</table>
		</div>
	</body>
</html>
<script>
//window.print();
</script>
