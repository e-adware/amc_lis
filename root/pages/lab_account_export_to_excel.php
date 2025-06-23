
<html>
<head>
<title>Lab Acount</title>

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

	$date1=$_GET['date1'];
	$date2=$_GET['date2'];

	function convert_date($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d M Y', $timestamp);
		return $new_date;
	}

	$filename ="bal_account_".$date1."_to_".$date2.".xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);

	$pat_reg_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `invest_patient_payment_details` WHERE `date` between '$date1' and '$date2' order by `slno` DESC ");

	?>

	<p style="margin-top: 2%;"><b>All Departments</b><br/><b>Lab Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?></p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Total Amount</th>
			<th>Discount</th>
			<th>Paid Amount</th>
			<th>Balance</th>
			<th>Date</th>
			<th>User</th>
		</tr>
	<?php
		$n=1;
		$tot="";
		$dis="";
		$paid="";
		$bal="";
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
			
			$user_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
	?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $pat_info["uhid"]; ?></td>
			<td><?php echo $pat_reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo "&#x20b9; ".number_format($pat_reg["tot_amount"],2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($pat_reg["dis_amt"],2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($pat_reg["advance"],2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($pat_reg["balance"],2); ?></td>
			<td><?php echo convert_date($pat_reg["date"]); ?></td>
			<td><?php echo $user_name["name"]; ?></td>
		</tr>
		<?php
				$tot=$tot+$pat_reg["tot_amount"];
				$dis=$dis+$pat_reg["dis_amt"];
				$paid=$paid+$pat_reg["advance"];
				$bal=$bal+$pat_reg["balance"];
				$n++;
			}
		?>
		<tr>
			<td></td><td></td><td></td>
			<th colspan=""><span class="text-right">Total</span></th>
			<td><?php echo "&#x20b9; ".number_format($tot,2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($dis,2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($paid,2); ?></td>
			<td colspan="3"><?php echo "&#x20b9; ".number_format($bal,2); ?></td>
		</tr>
	</table>
</div>
</body>
</html>
