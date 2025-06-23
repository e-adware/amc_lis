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
	// Time format convert
	function convert_time($time)
	{
		$time = date("g:i A", strtotime($time));
		return $time;
	}
	$rupees_symbol="&#x20b9; ";
	
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$user_entry=$_GET['user_entry'];
	
	$user=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
	
	$distnct_date_qry=mysqli_query($link, " SELECT distinct(`date`) FROM `cash_deposit` WHERE `emp_id`='$user_entry' AND `date` BETWEEN '$date1' AND '$date2' ");
	
?>
<html>
<head>
	<title>Cash Deposit Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
</head>
<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center><h4>Cash Deposit Report</h4></center>
		<b>User: <?php echo $user["name"]; ?></b>
		<table class="table table-hover table-condensed" style="font-size: 14px;">
			<tr>
				<th>#</th>
				<th>Amount</th>
				<!--<th>Date</th>-->
				<th>Time</th>
				<th>Received By</th>
			</tr>
			<?php
				while($distnct_date=mysqli_fetch_array($distnct_date_qry))
				{
					echo "<tr><td colspan='5'>Date: ".convert_date($distnct_date['date'])."</td></tr>";
					
					$n=1;
					$qry=mysqli_query($link, " SELECT * FROM `cash_deposit` WHERE `emp_id`='$user_entry' AND `date`='$distnct_date[date]' ");
					while($val=mysqli_fetch_array($qry))
					{
						$receiver=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$val[user]' "));
					?>
						<tr>
							<td><?php echo $n; ?></td>
							<td><?php echo $rupees_symbol.number_format($val["amount"],2); ?></td>
							<!--<td><?php echo convert_date($val["date"]); ?></td>-->
							<td><?php echo convert_time($val["time"]); ?></td>
							<td><?php echo $receiver["name"]; ?></td>
						</tr>
					<?php
						$n++;
					}
				}
			?>
		</table>
	</div>
</body>
</html>
<script>
	window.print();
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
				window.close();
		}
	}
</script>
