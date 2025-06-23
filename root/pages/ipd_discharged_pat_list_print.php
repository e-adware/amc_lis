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
	
	$from=$_GET['from'];
	$to=$_GET['to'];
	$balance_discharge=$_GET['balance_discharge'];
	$uhid=$_GET['uhid'];
	$ipd=$_GET['ipd'];
	$ipd_serial=$_GET['ipd_serial'];
	$list_start=$_GET['list_start'];
	$usr=$_GET['usr'];
	
?>
<html>
<head>
	<title>Discharged Patient List</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Discharged Patient List</h4>
			<div class="noprint ">
				<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()"> 
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $from; ?>">
	<input type="hidden" id="to" value="<?php echo $to; ?>">
	<input type="hidden" id="balance_discharge" value="<?php echo $balance_discharge; ?>">
	<input type="hidden" id="uhid" value="<?php echo $uhid; ?>">
	<input type="hidden" id="ipd" value="<?php echo $ipd; ?>">
	<input type="hidden" id="ipd_serial" value="<?php echo $ipd_serial; ?>">
	<input type="hidden" id="list_start" value="<?php echo $list_start; ?>">
	<input type="hidden" id="usr" value="<?php echo $usr; ?>">
</body>
</html>
<script>
	$(document).ready(function(){
		view();
	});
	function view()
	{
		$.post("ipd_discharged_pat_list_data.php",
		{
			type:"search_patient_list_ipd_dis",
			from:$("#from").val(),
			to:$("#to").val(),
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			ipd_serial:$("#ipd_serial").val(),
			balance_discharge:$("#balance_discharge").val(),
			list_start:$("#list_start").val(),
			usr:$("#usr").val(),
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
}
.ipd_serial
{
	display:none;
}
@page{
	margin-bottom: 0.5cm;
}
</style>
