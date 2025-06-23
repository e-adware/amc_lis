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
	
	$date1=base64_decode($_GET['date1']);
	$date2=base64_decode($_GET['date2']);
?>
<html>
<head>
	<title>TAT Non conformity Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/loader.css" type="text/css" rel="stylesheet"/>
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
            <h4>TAT Non conformity Report</h4>
        </center>
		<center>
			<div class="noprint">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()"> 
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
	
	<div id="loader"></div>
</body>
</html>
<script>
	$(document).ready(function(){
		view();
	});
	function view()
	{
		$.post("headwise_test_data.php",
		{
			date1:$("#from").val(),
			date2:$("#to").val(),
			type:"non_conformity_report"
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$(".print_div").hide();
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
.table-condensed tr th, .table-condensed tr td
{
	padding: 2px;
	font-size: 12px;
}
.text-justified {
  text-align: justify;
}
@media print
{
	body {
		padding: 0px;
		margin: 0px;
	}
	.noprint{
		display:none;
	}
}
</style>
