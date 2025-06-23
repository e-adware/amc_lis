<?php
session_start();

include("../../includes/connection.php");
require('../../includes/global.function.php');

$centreno=mysqli_real_escape_string($link, base64_decode($_GET["centreno"]));
$slno=mysqli_real_escape_string($link, base64_decode($_GET["slno"]));

$c_user=$_SESSION["emp_id"];
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$c_user' "));

$title="Centre Balance Received Receipt";

$pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `centre_balance_receive` WHERE `slno`='$slno' AND `centreno`='$centreno'"));

$centre_info=mysqli_fetch_array(mysqli_query($link, "SELECT `centrename` FROM `centremaster` WHERE `centreno`='$centreno'"));
?>
<html>
	<head>
		<title><?php echo $title;?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
		<script src="../../js/jquery.min.js"></script>
	</head>
	<body onkeypress="close_window(event)">
		<div class="container-fluid">
			<div class="">
				<div class="">
					<?php include('page_header.php');?>
				</div>
			</div>
			<hr>
			<br>
			<center>
				<h4><?php echo $title;?></h4>
			</center>
			<div id="load_data">
				<br>
				<br>
				<p>
					Received with thanks from <?php echo $centre_info["centrename"]; ?> of amount <?php echo $pay_det["receive_amount"]; ?> by <?php echo $pay_det["payment_mode"]; ?> on payment date <?php echo date("d-m-Y",strtotime($pay_det["payment_date"])); ?>
				</p>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				<p style="text-align:right;">
					<b>For <?php echo $company_info["name"]; ?></b>
				</p>
				<hr>
				<p>
					<p>Indian Rupees <?php echo convert_number($pay_det["receive_amount"]); ?> Only</p>
				</p>
			</div>
		</div>
	</body>
</html>
<div id="loader" style="margin-top:-10%;"></div>
<!-- Loader -->
<link rel="stylesheet" href="../../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
	});
	
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
<style type="text/css" media="print">
  //@page { size: landscape; }
</style>
<style>

.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
	border-top: 1px solid #fff;
}
.table
{
	margin-bottom:1px;
}
hr
{
	margin:0;
	border-bottom:0;
	border-top: 1px solid #000;
}

@media print
{
	.noprint{
		display:none;
	 }
}
@page
{
	margin:0.2cm;
}
</style>
