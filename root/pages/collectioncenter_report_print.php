<?php
include("../../includes/connection.php");

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
	
	$cid=base64_decode($_GET['cid']);
	$rep=base64_decode($_GET['type']);
	$fdate=base64_decode($_GET['fdate']);
	$tdate=base64_decode($_GET['tdate']);

	if($rep==1)
	$title="Patientwise";
	if($rep==2)
	$title="Testwise";
	$cen=mysqli_fetch_array(mysqli_query($link,"SELECT `centrename` FROM `centremaster` WHERE `centreno`='$cid'"));
	?>
<html>
<head>
	<title>Collection Center Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php //include('page_header.php');?>
				<?php include('page_header_ongc_bill.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Collection Center Report</h4>
			<b>From <?php echo convert_date($fdate); ?> to <?php echo convert_date($tdate); ?></b>
			<div class="noprint ">
				<input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="fdate" value="<?php echo $fdate; ?>">
	<input type="hidden" id="tdate" value="<?php echo $tdate; ?>">
	<input type="hidden" id="cid" value="<?php echo $cid; ?>">
	<input type="hidden" id="rep" value="<?php echo $rep; ?>">
</body>
</html>
<script>
	$(document).ready(function(){
		view();
	});
	function view()
	{
		$.post("collectioncenter_report.php",
		{
			type:"collectionreport",
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			cid:$("#cid").val(),
			rep:$("#rep").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			$("#print_div").hide();
		})
	}
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
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
}
</style>
