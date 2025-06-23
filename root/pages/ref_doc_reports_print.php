<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");

$c_user=trim($_SESSION['emp_id']);

$rupees_symbol="&#x20b9; ";

$date1=$_GET['fdate'];
$date2=$_GET['tdate'];
$refbydoctorid=$_GET['doc'];
$encounter=$_GET['encounter'];
$branch_id=$_GET['brid'];

?>
<html>
<head>
	<title>Ref Doctor Patient Report</title>
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
			<h4>Ref Doctor Patient Report</h4>
			
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
	<input type="hidden" id="refbydoctorid" value="<?php echo $refbydoctorid; ?>">
	<input type="hidden" id="encounter" value="<?php echo $encounter; ?>">
	<input type="hidden" id="branch_id" value="<?php echo $branch_id; ?>">
</body>
</html>
<script>
	$(document).ready(function(){
		view();
		//$(".noprint").hide();
	});
	function view()
	{
		$.post("ref_doc_reports_data.php",
		{
			type:"view",
			date1:$("#from").val(),
			date2:$("#to").val(),
			refbydoctorid:$("#refbydoctorid").val(),
			encounter:$("#encounter").val(),
			branch_id:$("#branch_id").val(),
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
