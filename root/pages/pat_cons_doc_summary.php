<?php
include("../../includes/connection.php");
		
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
$user=$_GET['user'];
$level=$_GET['level'];
$rupees_symbol="&#x20b9; ";
// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}
?>
<html>
	<head>
		<link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
		<style>
			.table th, .table td {
				padding: 2px;
			}
			@page
			{
				margin-right:2px;
				margin-left:2px;
			}
		</style>
	</head>
	<body onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center><b><u>Doctor Payment Summary</u></b></center>
		<?php
		if($fdate==$tdate)
		{
			$dt="Date: ".convert_date($fdate)."<br/>";
		}
		else
		{
			$dt="From <i>".convert_date($fdate)."</i> to <i>".convert_date($tdate)."</i><br/>";
		}
		echo "<center><b>".$dt."</b></center>";
		?>
		<table class="table table-condensed table-bordered" style="font-size:13px;">
		<?php
		$qry=mysqli_query($link,"SELECT DISTINCT a.`consultantdoctorid` FROM `doctor_payment` a, `appointment_book` b WHERE b.`appointment_date` BETWEEN '$fdate' AND '$tdate'");
		while($dc=mysqli_fetch_array($qry))
		{
			$dname=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$dc[consultantdoctorid]'"));
			$qq=mysqli_query($link,"select a.consultantdoctorid,b.`patient_id`,b.`opd_id`,b.`visit_fee`,b.`regd_fee`,c.`amount`,c.`date`,c.`time`,c.`user` from appointment_book a,consult_patient_payment_details b, doctor_payment c where a.consultantdoctorid='$dc[consultantdoctorid]' and a.patient_id=b.patient_id and a.patient_id=c.patient_id and a.opd_id=b.opd_id and a.opd_id=c.opd_id and b.date between '$fdate' and '$tdate'");
			$d_num=mysqli_num_rows($qq);
			if($d_num>0)
			{
			?>
			<tr>
				<th colspan="8" style="text-shadow: 2px 2px #aaaaaa;"><?php echo $dname['Name'];?></th>
			</tr>
			<tr>
				<th>#</th>
				<th>Patient Name</th>
				<th>UHID</th>
				<th>PIN</th>
				<th style="text-align:right">Amount</th>
				<th style="text-align:right">Paid</th>
				<th>Date/Time</th>
				<th>User</th>
			</tr>
			<?php
			$j=1;
			$doc_tot_amt=0;
			$doc_tot_paid=0;
			while($rr=mysqli_fetch_array($qq))
			{
				$pinfo=mysqli_fetch_array(mysqli_query($link,"select `name` from patient_info where patient_id='$rr[patient_id]'"));
				$v_fee=$rr['visit_fee']-$rr['ref_amt'];
				$usr=mysqli_fetch_array(mysqli_query($link,"select `name` from employee where emp_id='$rr[user]'"));
			?>
			<tr>
				<td><?php echo $j;?></td>
				<td><?php echo $pinfo['name'];?></td>
				<td><?php echo $rr['patient_id'];?></td>
				<td><?php echo $rr['opd_id'];?></td>
				<td style="text-align:right"><?php echo number_format($v_fee,2);?></td>
				<td style="text-align:right"><?php echo number_format($rr['amount'],2);?></td>
				<td><?php echo convert_date($rr['date'])." ".convert_time($rr['time']);?></td>
				<td><?php echo $usr['name'];?></td>
			</tr>
			<?php
			$doc_tot_amt+=$v_fee;
			$doc_tot_paid+=$rr['amount'];
			$j++;
			}
			?>
			<tr>
				<th colspan="4" style="text-align:right">Total :</th>
				<th style="text-align:right"><?php echo number_format($doc_tot_amt,2);?></th>
				<th style="text-align:right"><?php echo number_format($doc_tot_paid,2);?></th>
				<th colspan="2"></th>
			</tr>
			<?php
		}
		}
		?>
		</table>
		</div>
	</body>
<script>
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
</html>
