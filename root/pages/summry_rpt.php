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
	$type="";
	if($encounter>0)
	{
		$type=" and `type`='$encounter'";
	}
	
?>
<html>
<head>
	<title>summary  Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<style>
		*{font-size:12px}
	</style>
</head>

<body>
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Summry Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-bordered table-condensed table-report" id="all_pat">
			<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $d['name'];?></td>
					<td><?php echo $qpay['maxpay'];?></td>
			</tr>
			<?php
			   $i=1;
				$qexpense=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxexp from expense_detail where `date` between '$date1' and '$date2' "));
				$qrefund=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund_amount),0) as maxref from invest_payment_refund where `date` between '$date1' and '$date2' "));
				$patientdel=mysqli_query($link, "SELECT distinct a.user,b.name FROM `invest_payment_detail` a,employee b WHERE a.`date` between '$date1' and '$date2' and a.amount>0 and a.user=b.emp_id  order by b.`name` ");
				
				while($d=mysqli_fetch_array($patientdel))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
					
					$qpay=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxpay from invest_payment_detail where `date` between '$date1' and '$date2' and user='$d[user]'"));
					$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
					$vttlclction=$vttlclction+$qpay['maxpay'];
			?>
					<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $d['name'];?></td>
					<td><?php echo $qpay['maxpay'];?></td>
				</tr>
			<?php
						$i++;
					}
					$vnetamt=$vttlclction-$qexpense['maxexp']-$qrefund['maxref'];
				?>
			<tr>
				<td>&nbsp;</td>
				<td style="font-weight:bold;text-align:right" >Total Collection :</td>
				<td style="font-weight:bold"><?php echo number_format($vttlclction,2);?></td>
			</tr>
		    <tr>
				<td>&nbsp;</td>
				<td style="font-weight:bold;text-align:right" >Total Expense :</td>
				<td style="font-weight:bold"><?php echo number_format($qexpense['maxexp'],2);?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="font-weight:bold;text-align:right" >Total Refund :</td>
				<td style="font-weight:bold"><?php echo number_format($qrefund['maxref'],2);?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="font-weight:bold;text-align:right" >Net Amount :</td>
				<td style="font-weight:bold"><?php echo number_format($vnetamt,2);?></td>
			</tr>
		</table>
	</div>
</body>
</html>
<script>window.print();</script>
