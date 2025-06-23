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

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$head_id=$_GET['head_id'];
	
?>
<html>
<head>
	<title>Test Out Sample Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Test Out Sample Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
			<div class="noprint ">
				<!--<input type="button" class="btn btn-info" id="det_excel" value="Excel" onclick="export_excel()">-->
				<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()"> 
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
	<input type="hidden" id="head_id" value="<?php echo $head_id; ?>">
</body>
</html>
<script>
	$(document).ready(function(){
		view();
	});
	function view()
	{
		$.post("pat_test_out_data.php",
		{
			type:"report",
			date1:$("#from").val(),
			date2:$("#to").val(),
			head_id:$("#head_id").val(),
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
		var head_id=$("#head_id").val();
		
		var url="detail_account_print_xls.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&pay_mode="+pay_mode+"&account_break="+account_break;
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
</style>
