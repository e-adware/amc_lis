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
	$visit_type_id=trim($_GET['visit_type_id']);
	$val=trim($_GET['val']);
	
	if($val=="patient_wise")
	{
		$title="Patient Visit Report";
	}
?>
<html>
<head>
	<title><?php echo $title; ?></title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4><?php echo $title; ?></h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
			<div class="noprint ">
				<input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
	<input type="hidden" id="visit_type_id" value="<?php echo $visit_type_id; ?>">
	<input type="hidden" id="val" value="<?php echo $val; ?>">
	</div>
</body>
</html>
<script>
	$(document).ready(function(){
		view();
	});
	function view()
	{
		$.post("patient_visit_type_report_data.php",
		{
			type:$("#val").val(),
			date1:$("#from").val(),
			date2:$("#to").val(),
			visit_type_id:$("#visit_type_id").val(),
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
*
{
	font-size:11px;
}
@page
{
	margin:0.1cm;
}
@media print
{
	.noprint{
		display:none;
	 }
}
.table th, .table td
{
	padding: 1px;
}
</style>
