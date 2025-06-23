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
	$con_cod_id=$_GET['con_cod_id'];
	$dept_id=$_GET['dept_id'];
	
?>
<html>
<head>
	<title>Doctor Account Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Doctor Account Report</h4>
			<b style="float: left;">From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
			<div class="noprint ">
				<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
	<input type="hidden" id="con_cod_id" value="<?php echo $con_cod_id; ?>">
	<input type="hidden" id="dept_id" value="<?php echo $dept_id; ?>">
</body>
</html>
<script>
	$(document).ready(function(){
		view();
		//$(".noprint").hide();
	});
	function view()
	{
		$("#loader").show();
		$.post("doctor_account_data.php",
		{
			type:"doctor_account",
			date1:$("#from").val(),
			date2:$("#to").val(),
			con_cod_id:$("#con_cod_id").val(),
			dept_id:$("#dept_id").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			$("#print_div").hide();
			$("#loader").hide();
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
	
	function close_window_child()
	{
		window.close();
	}
	function refreshParent()
	{
		//window.opener.location.reload(true);
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
.table
{
	margin-bottom: 0px;
}
.table-condensed th, .table-condensed td
{
	padding: 0;
}
</style>
