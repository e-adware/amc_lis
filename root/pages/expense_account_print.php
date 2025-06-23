<?php
include('../../includes/connection.php');

	// Date format convert
	function convert_date($date)
	{
		if($date)
		{
			$timestamp = strtotime($date); 
			$new_date = date('d M Y', $timestamp);
			return $new_date;
		}
	}
	$rupees_symbol="&#x20b9; ";
	
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$encounter=$_GET['encounter'];
	$user_entry=$_GET['user_entry'];
	
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	if($date1=="" && $date2=="")
	{
		$q=mysqli_query($link,"SELECT * FROM `expense_detail`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `expense_detail` WHERE `date` BETWEEN '$date1' AND '$date2' $user");
	}
	
?>
<html>
<head>
	<title>Expense Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Expense Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-bordered table-condensed table-report">
			<tr>
				<th>#</th>
				<!--<th>Details</th>-->
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
				<!--<td><?php echo $c["cat_name"]; ?></td>-->
				<td><?php echo $r["description"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($r["amount"],2); ?></td>
				<td><?php echo convert_date($r["date"]); ?></td>
				<td><?php echo $emp["name"]; ?></td>
			</tr>
			<?php
			$i++;
			}
			?>
			<tr>
				<td colspan="2"><span class="text-right"><b>Total</b></span></td>
				<td colspan="3"><span class="text-left"><b><?php echo $rupees_symbol.number_format($tot,2); ?></b></span></td>
			</tr>
		</table>
	</div>
</body>
</html>
<script>window.print();</script>
