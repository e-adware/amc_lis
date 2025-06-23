
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
	// Time format convert
	function convert_time($time)
	{
		$time = date("g:i A", strtotime($time));
		return $time;
	}
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$service_id=$_GET['service_id'];
	$consultantdoctorid=$_GET['consultantdoctorid'];
	
	$filename ="doctor_service_reports_".$date1."_to_".$date2.".xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);
	
	$service=" AND `service_id`='$service_id'";
	if($service_id=='0')
	{
		$service="";
	}
	$consult=" AND `consultantdoctorid`='$consultantdoctorid'";
	if($consultantdoctorid=='0')
	{
		$consult="";
	}
	
	$qq=" SELECT * FROM `doctor_service_done` WHERE `date` between '$date1' AND '$date2' $service $consult ORDER BY `slno` DESC ";
	
	$counter_qry=mysqli_query($link, $qq);
	
	$counter_num=mysqli_num_rows($counter_qry);
	
	if($counter_num>0)
	{
	?>
	<p style="margin-top: 2%;"><b>Doctor Service Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>UHID / PIN</th>
			<th>Patient Name</th>
			<th>Doctor Name</th>
			<th>Service</th>
			<th>Amount</th>
			<th>Encounter</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
		<?php
		$i=1;
		$tot_bill_amout=0;
		while($all_pat=mysqli_fetch_array($counter_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$all_pat[patient_id]'"));
			
			$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$all_pat[consultantdoctorid]' "));
			
			$service=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$all_pat[service_id]' "));
			
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$all_pat[user]' "));
			
			$bill_amount=0;
			$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `ipd_pat_service_details` WHERE `slno`='$all_pat[rel_slno]' "));
			$bill_amount=$ipd_service_bill["amount"];
			
			$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[ipd_id]' "));
			if($pat_type["type"]==3)
			{
				$Encounter="IPD";
			}
			if($pat_type["type"]==4)
			{
				$Encounter="Casualty";
			}
			$tot_bill_amout+=$bill_amount;
		?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $pat_info["uhid"]; ?> / <?php echo $all_pat["ipd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $con_doc["Name"]; ?></td>
				<td><?php echo $service["charge_name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($ipd_service_bill["amount"],2); ?></td>
				<td><?php echo $Encounter; ?></td>
				<td><?php echo convert_date($all_pat["date"]); ?> <?php echo convert_time($all_pat["time"]); ?></td>
				<td><?php echo $user_name["name"]; ?></td>
			</tr>
		<?php
			$i++;
		}
	?>
		<tr>
			<th colspan="4"></th>
			<th colspan=""><span class="text-right">Total:</span></th>
			<td colspan=""><?php echo $rupees_symbol.number_format($tot_bill_amout,2); ?></td>
			<th colspan="3"></th>
		</tr>
	</table>
<?php } ?>
</div>
</body>
</html>
