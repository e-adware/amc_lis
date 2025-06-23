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
	
?>
<html>
<head>
	<title>Laboratory Record Report</title>
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
			<h4>Laboratory Record Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
			<!--<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>-->
			<div class="noprint ">
				<input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()"> 
				<input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
</body>
</html>
<script>
	$(document).ready(function(){
		view();
		//$(".noprint").hide();
	});
	function view()
	{
		$.post("lab_records_data.php",
		{
			type:"lab_records_test_pat",
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			$("#print_div").hide();
		})
	}
	function export_excel()
	{
		var date1=$("#from").val();
		var date2=$("#to").val();
		
		var url="detail_account_print_user_break_xls.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&pay_mode="+pay_mode+"&account_break="+account_break;
		document.location=url;
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
	function mouse_over(val)
	{
		$("."+val+"_test").css({'color': '#F00'});
	}
	function mouse_out(val)
	{
		$("."+val+"_test").css({'color': '#666666'});
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
	text-align: center;
}
</style>
