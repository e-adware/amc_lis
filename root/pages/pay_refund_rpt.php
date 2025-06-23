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
	
	function convert_date1($date)
	{
		if($date)
		{
			$timestamp = strtotime($date); 
			$new_date = date('d-m-y', $timestamp);
			return $new_date;
		}
	}
	// Time format convert
	function convert_time($time)
	{
		if($time)
		{
			$time = date("g:i A", strtotime($time));
			return $time;
		}
	}
	$rupees_symbol="&#x20b9; ";
	
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$encounter=$_GET['encounter'];
	$user_entry=$_GET['user_entry'];
	
	$dis_date_qry=mysqli_query($link,"SELECT DISTINCT `date` FROM `invest_payment_refund` WHERE `date` BETWEEN '$date1' AND '$date2' ORDER BY `date`");
	
?>
<html>
<head>
	<title>Refund  Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<style>
		*{font-size:12px}
	</style>
</head>

<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Payment Refund Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-condensed table-bordered" id="ref_tbl">
<?php
			$tot_recv=0;
			$tot_refund=0;
			while($dis_date=mysqli_fetch_array($dis_date_qry))
			{
				?>
				<tr>
					<th colspan="7">Date : <?php echo convert_date($dis_date['date']);?></th>
				</tr>
				<tr>
					<th>#</th>
					<th>PIN</th>
					<th>Name</th>
					<th>Bill Amount</th>
					<th>Refund</th>
					<th>Reason</th>
					<th>Encounter</th>
					<th>User</th>
					<th>Time</th>
				</tr>
	<?php
				
				$qry=" SELECT a.*, b.`type` FROM `invest_payment_refund` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`='$dis_date[date]'";
				
				if($encounter>0)
				{
					$qry.=" AND b.`type`='$encounter'";
				}
				
				if($user_entry>0)
				{
					$qry.=" AND a.`user`='$user_entry'";
				}
				
				//echo "<br/>".$qry."<br/>";
				
				$pay_refund_qry=mysqli_query($link,$qry);
				$j=1;
				while($pay_refund=mysqli_fetch_array($pay_refund_qry))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$pay_refund[patient_id]'"));
					
					$pay=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_payment_refund` WHERE `patient_id`='$pay_refund[patient_id]' AND `opd_id`='$pay_refund[opd_id]' AND `date`='$dis_date[date]' AND `user`='$pay_refund[user]' "));
					
					$usr=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$pay_refund[user]'"));
					
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pay_refund[type]' "));
					$Encounter=$pat_typ_text['p_type'];
			?>
					<tr>
						<td><?php echo $j;?></td>
						<td><?php echo $pay_refund["opd_id"];?></td>
						<td><?php echo $pat_info["name"];?></td>
						<td><?php echo $rupees_symbol.$pay['tot_amount'];?></td>
						<td><?php echo $rupees_symbol.$pay['refund_amount'];?></td>
						<td><?php echo $pay['reason'];?></td>
						<td><?php echo $Encounter;?></td>
						<td><?php echo $usr['name'];?></td>
						<td><?php echo convert_time($pay_refund['time']);?></td>
					</tr>
	<?php
					$tot_refund+=$pay['refund_amount'];
					$j++;
					
				}
			}
	?>
			<tr>
				<th colspan="4" style="text-align:right;">Total : </th>
				<th colspan="4"><?php echo $rupees_symbol.number_format($tot_refund,2);?></th>
			</tr>
		</table>
	</div>
</body>
</html>
<script>
	//window.print();
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
