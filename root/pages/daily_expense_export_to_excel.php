
<html>
<head>
<title>Detail Acount</title>

<style>
input[type="text"]
{
	border:none;
}
body {
	padding: 10px;
	font-size:13px; font-family:Arial, Helvetica, sans-serif; line-height: 18px;
}
.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}
.bline td{border-bottom:1px solid;}
*{font-size:13px;}
</style>
</head>
<body>
<div class="container">
	<?php
	include'../../includes/connection.php';

	function convert_date($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d M Y', $timestamp);
		return $new_date;
	}

	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	//~ $encounter=$_GET['encounter'];
	$user_entry=$_GET['user_entry'];
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	
	$filename ="daily_expense_".$date1."_to_".$date2.".xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);
	
	$q=mysqli_query($link,"SELECT * FROM `expense_detail` WHERE `date` BETWEEN '$date1' AND '$date2' $user");
	
	?>
	<p style="margin-top: 2%;"><b>Daily Expense Report from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?></p>
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
			<td colspan=""></td>
			<td colspan=""><span class="text-right"><b>Total</b></span></td>
			<td colspan=""><span class="text-left"><b><?php echo $rupees_symbol.number_format($tot,2); ?></b></span></td>
			<td colspan="2"></td>
		</tr>
	</table>
</div>
</body>
</html>
