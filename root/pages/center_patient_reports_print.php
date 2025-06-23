<?php
session_start();
include('../../includes/connection.php');

	$c_user=trim($_SESSION['emp_id']);

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
	$val=$_GET['val'];
	$center_no=$_GET['center_no'];
	
	if($val==1)
	{
		$title="Center Patient Report";
	}
?>
<html>
<head>
	<title><?php echo $title; ?></title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
	<div class="container-fluid">
		<div class="">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<div class="noprint ">
			<center>
				<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()"> 
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</center>
		</div>
		<center>
			<h4><?php echo $title; ?></h4>
			<b style="float: left;">From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
		</center>
		<br>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
	<input type="hidden" id="center_no" value="<?php echo $center_no; ?>">
	<input type="hidden" id="val" value="<?php echo $val; ?>">
<div id="loader" style="margin-top:-10%;"></div>
<!-- Loader -->
<link rel="stylesheet" href="../../css/loader.css" />
</body>
</html>
<script>
	$(document).ready(function(){
		allreport();
		//$(".noprint").hide();
	});
	function allreport()
	{
		$("#loader").show();
		$.post("center_patient_reports_data.php",
		{
			type:$("#val").val(),
			cid:$("#center_no").val(),
			fdate:$("#from").val(),
			tdate:$("#to").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$("#print_div").hide();
		})
	}
</script>
<style type="text/css" media="print">
  @page { size: landscape; }
  
</style>
<style>
	.txt_small{
	font-size:10px;
}
.table
{
	font-size: 11px;
}
@media print
{
	.account_close_div
	{
		display:none;
	}
	.noprint{
		display:none;
	 }
	<?php if($account_break==0){ ?>
	 *{ //display:none; }
	<?php } ?>
}
.ipd_serial
{
	display:none;
}
.table
{
	margin-bottom: 0px;
}
.table-condensed th, .table-condensed td
{
	padding: 0;
}
</style>
