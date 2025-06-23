<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");

$c_user=trim($_SESSION['emp_id']);

$rupees_symbol="&#x20b9; ";

$date1=$_GET['fdate'];
$date2=$_GET['tdate'];
$sguide_id=$_GET['sguide'];
$hguide_id=$_GET['hguide'];
?>
<html>
<head>
	<title>Health Guide Patient Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Health Guide Report</h4>
			
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
			<div class="noprint ">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
	<input type="hidden" id="sguide_id" value="<?php echo $sguide_id; ?>">
	<input type="hidden" id="hguide_id" value="<?php echo $hguide_id; ?>">
</body>
</html>
<script>
	$(document).ready(function(){
		view();
		//$(".noprint").hide();
	});
	function view()
	{
		$.post("health_guide_reports_load.php",
		{
			type:"health_guide_reports",
			date1:$("#from").val(),
			date2:$("#to").val(),
			sguide_id:$("#sguide_id").val(),
			hguide_id:$("#hguide_id").val(),
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
	font-size: 12px;
}
@media print
{
	.noprint{
		display:none;
	 }
}

.table
{
	margin-bottom: 0px;
}
.table-condensed th, .table-condensed td
{
	//padding: 0;
	padding: 0 10px 0 0;
}
</style>
