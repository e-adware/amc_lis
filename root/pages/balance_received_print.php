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
	$encounter=$_GET['encounter'];
	$encounter_val=$_GET['encounter'];
	$pay_mode=$_GET['pay_mode'];
	$user_entry=$_GET['user_entry'];
	$user_val=$_GET['EpMl'];
	$account_break=$_GET['account_break'];
	
	// important
	$date11=$_GET['date1'];
	$date22=$_GET['date2'];
	
?>
<html>
<head>
	<title>Balance Received Report</title>
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
		<center>
			<h4>Balance Received Report</h4>
			<div class="noprint ">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
			</div>
		</center>
		<b style="float: left;">From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date11; ?>">
	<input type="hidden" id="to" value="<?php echo $date22; ?>">
	<input type="hidden" id="encounter" value="<?php echo $encounter; ?>">
	<input type="hidden" id="user_entry" value="<?php echo $user_entry; ?>">
	<input type="hidden" id="pay_mode" value="<?php echo $pay_mode; ?>">
	<input type="hidden" id="account_break" value="<?php echo $account_break; ?>">
</body>
</html>
<script>
	$(document).ready(function(){
		view();
	});
	function view()
	{
		$.post("daily_account_details_all_user_break.php",
		{
			type:"balance_received",
			date1:$("#from").val(),
			date2:$("#to").val(),
			encounter:$("#encounter").val(),
			user_entry:$("#user_entry").val(),
			pay_mode:$("#pay_mode").val(),
			account_break:$("#account_break").val(),
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
