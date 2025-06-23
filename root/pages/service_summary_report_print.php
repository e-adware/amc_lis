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
	
	
?>
<html>
<head>
	<title>Service Summary Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header_ongc_bill.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Service Summary Report</h4>
			
			<br>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
			<div class="noprint ">
				<input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()">
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
	});
	function view()
	{
		$.post("summary_report_data.php",
		{
			type:"service_summary",
			date1:$("#from").val(),
			date2:$("#to").val(),
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
</style>
