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
	
	$regd=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`regd_fee`) as regd_tot FROM `consult_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' "));
	$regd_total=$regd["regd_tot"];
	
	$opd_lab=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`tot_amount`) as opd_lab_tot FROM `invest_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' "));
	$opd_lab_total=$opd_lab["opd_lab_tot"];
	
	$ipd_lab=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) as ipd_lab_tot FROM `ipd_pat_service_details` WHERE `group_id`='104' AND `date` BETWEEN '$date1' AND '$date2' "));
	$ipd_lab_total=$ipd_lab["ipd_lab_tot"];
	
	
?>
<html>
<head>
	<title>Service Report</title>
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
			<h4>Service Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-condensed table-bordered">
			<tr>
				<td style="">Registration</td>
				<td>Rs. <?php echo number_format($regd_total,2); ?></td>
			</tr>
			<tr>
				<td style="">OPD Laboratory</td>
				<td>Rs. <?php echo number_format($opd_lab_total,2); ?></td>
			</tr>
			<tr>
				<td style="">IPD Laboratory</td>
				<td>Rs. <?php echo number_format($ipd_lab_total,2); ?></td>
			</tr>
		<?php
			$tot_serv_amt=0;
			$distnct_service_qry=mysqli_query($link, " SELECT DISTINCT(`service_id`) FROM `ipd_pat_service_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `group_id`!='104' ");
			while($distnct_service=mysqli_fetch_array($distnct_service_qry))
			{
				$service_name=mysqli_fetch_array(mysqli_query($link, " SELECT `service_text` FROM `ipd_pat_service_details` WHERE `service_id`='$distnct_service[service_id]' "));
				$service_amount="0";
				$service_amount_val=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) as service_tot FROM `ipd_pat_service_details` WHERE `service_id`='$distnct_service[service_id]' AND `date` BETWEEN '$date1' AND '$date2' "));
				$service_amount=$service_amount_val["service_tot"];
				
			?>
			<tr>
				<td style=""><?php echo $service_name["service_text"]; ?></td>
				<td>Rs. <?php echo number_format($service_amount,2); ?></td>
			</tr>
			<?php
				$tot_serv_amt+=$service_amount;
			}
		?>
			<tr>
				<th>Total</th>
				<td>Rs. <?php echo number_format($tot_serv_amt+$ipd_lab_total+$opd_lab_total+$regd_total,2); ?></td>
			</tr>
		</table>
	</div>
</body>
</html>
<script>//window.print();</script>
