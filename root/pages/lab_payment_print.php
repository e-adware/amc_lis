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
		
	$fdate=$_GET["fdate"];
	$tdate=$_GET["tdate"];
	$pat_name=$_GET["pat_name"];
	$pat_uhid=$_GET["pat_uhid"];
	$pin=$_GET["pin"];
	$hos_reg_no=$_GET["hos_reg_no"];
	
	if($fdate=="" && $tdate=="" && $pat_name=="" && $pat_uhid=="" && $pin=="")
	{
		$q=" SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `opd_id` in (SELECT `opd_id` FROM `invest_payment_detail` WHERE `typeofpayment`='A' AND `amount`=0) ";
		//$q=" SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `opd_id` in (SELECT a.`opd_id`,b.dis_amt FROM `invest_payment_detail` a,invest_patient_payment_details b WHERE a.`typeofpayment`='A' AND a.`amount`=0 and a.patient_id=b.patient_id and aa.opd_id=b.opd_id and b.dis_amt=0) ";
	}else
	{
		$q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id`>0 ";
	}
	if($fdate && $tdate)
	{
		$q.=" AND `date` between '$fdate' and '$tdate' ";
	}
	if($pat_name)
	{
		$q.=" AND `patient_id` IN ( SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$pat_name%' ) ";
	}
	
	if($pat_uhid)
	{
		$q.=" AND `patient_id` like '$pat_uhid%' ";
	}
	if($pin)
	{
		$q.=" AND `opd_id` like '$pin%' ";
	}
	if($hos_reg_no)
	{
		$q.=" AND `opd_id` IN ( SELECT `opd_id` FROM `patient_cabin` WHERE `hos_reg_no` like '$hos_reg_no%' ) ";
	}
	$q.=" AND `type`='2' ";
	$q.=" order by `slno` DESC";
	//echo $q;
	$qq_qry=mysqli_query($link, $q );
	$qq_num=mysqli_num_rows($qq_qry);
	
?>
<html>
<head>
	<title>Balance Report</title>
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
			<h4>Balance Report</h4>
			<?php if($fdate && $tdate){?><b>From <?php echo convert_date($fdate); ?> to <?php echo convert_date($tdate); ?></b><?php }?>
		</center>
		<br>
		<table class="table table-bordered text-center">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Age/Sex</th>
			<th>Bill Amount</th>
			<th>Date Time</th>
			<th>Center</th>
		</tr>
	<?php
		$n=1;
		while($qq=mysqli_fetch_array($qq_qry))
		{
			$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$qq[patient_id]' AND `opd_id`='$qq[opd_id]' "));
			if($pat_pay_detail['tot_amount']>0)
			{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$qq[patient_id]' "));
			if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			
			$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT `tot_amount` FROM `invest_patient_payment_details` WHERE `patient_id`='$qq[patient_id]' AND `opd_id`='$qq[opd_id]' "));
			
			$cashier_access_num=0;
			$cashier_access=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cashier_access` WHERE `emp_id`='$c_user' "));
			if($cashier_access["lab_cashier"]>0)
			{
				$cashier_access_num=1;
			}
			$cname=mysqli_fetch_array(mysqli_query($link,"select centrename from centremaster where centreno='$qq[center_no]'"));
			
			$cab=mysqli_fetch_array(mysqli_query($link,"select * from patient_cabin where patient_id='$qq[patient_id]' and opd_id='$qq[opd_id]'"));
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $pat_info["patient_id"]; ?></td>
				<td><?php echo $qq["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $age."/".$pat_info["sex"]; ?></td>
				<td><?php echo $rupees_symbol.$pat_pay_detail["tot_amount"]; ?></td>
				<td><?php echo convert_date($qq["date"]); ?> <?php echo convert_time($qq["time"]); ?></td>
				<td><?php echo $cname['centrename'];?> <?php if($cab['cabin_no']){ echo "- ".$cab['cabin_no']; } if($cab['hos_reg_no']){ echo " (".$cab['hos_reg_no'].")"; }?></td>
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
<script>//window.print();</script>
