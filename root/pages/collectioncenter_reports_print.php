<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$cid=base64_decode($_GET['cid']);
$bid=base64_decode($_GET['bid']);
$rep=base64_decode($_GET['type']);
$fdate=base64_decode($_GET['fdate']);
$tdate=base64_decode($_GET['tdate']);

if($rep==1)
$title="Patient Wise Centre Report";
if($rep==2)
$title="Test Wise Centre Report";
?>
<html>
	<head>
		<title><?php echo $title;?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
		<script src="../../js/jquery.min.js"></script>
	</head>
	<body onkeypress="close_window(event)">
		<div class="container-fluid">
			<div class="row">
				<div class="">
					<?php include('page_header.php');?>
				</div>
			</div>
			<hr>
			<center>
				<h4><?php echo $title;?></h4>
				<b>From <?php echo convert_date($fdate); ?> to <?php echo convert_date($tdate); ?></b>
				<div class="noprint ">
					<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
					<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
				</div>
			</center>
			<div id="load_data"></div>
		</div>
		<input type="hidden" id="txtfrom" value="<?php echo $fdate; ?>">
		<input type="hidden" id="txtto" value="<?php echo $tdate; ?>">
		<input type="hidden" id="report_type" value="<?php echo $rep; ?>">
		<input type="hidden" id="txtcntrid" value="<?php echo $cid; ?>">
		<input type="hidden" id="bid" value="<?php echo $bid; ?>">
		<input type="hidden" id="pay_mode" value="<?php echo $pay_mode; ?>">
	</body>
</html>
<div id="loader" style="margin-top:-10%;"></div>
<!-- Loader -->
<link rel="stylesheet" href="../../css/loader.css" />
<script>
	$(document).ready(function(){
		view();
	});
	function view()
	{
		$.post("collectioncenter_report.php",
		{
			rep:$("#report_type").val(),
			cid:$("#txtcntrid").val(),
			branch_id:$("#bid").val(),
			fdate:$("#txtfrom").val(),
			tdate:$("#txtto").val(),
			type:"collectionreport",
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
</style>
