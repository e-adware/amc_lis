<?php
include("../../includes/connection.php");

$fDt	=base64_decode($_GET['fDt']);
$tDt	=base64_decode($_GET['tDt']);
$dEp	=base64_decode($_GET['dEp']);
$tSt	=base64_decode($_GET['tSt']);
$wRd	=base64_decode($_GET['wRd']);
$tYp	=base64_decode($_GET['tYp']);

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Department Test Count Report</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/loader.css" />
	<script src="../../js/jquery.min.js"></script>
	<script>
		$(document).ready(function()
		{
			load_items();
		});
		function load_items()
		{
			$("#loader").show();
			$.post("department_wise_test_count_ajax.php",
			{
				fdate	:$("#fDt").val().trim(),
				tdate	:$("#tDt").val().trim(),
				dept	:$("#dEp").val().trim(),
				test	:$("#tSt").val().trim(),
				ward	:$("#wRd").val().trim(),
				type	:$("#tYp").val().trim()
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
				$(".btn_act").hide();
			});
		}
	</script>
</head>
<body>
	<input type="hidden" id="fDt" value="<?php echo $fDt;?>" />
	<input type="hidden" id="tDt" value="<?php echo $tDt;?>" />
	<input type="hidden" id="dEp" value="<?php echo $dEp;?>" />
	<input type="hidden" id="tSt" value="<?php echo $tSt;?>" />
	<input type="hidden" id="wRd" value="<?php echo $wRd;?>" />
	<input type="hidden" id="tYp" value="<?php echo $tYp;?>" />
	<div class="container-fluid">
		<?php
		include("page_header.php");
		?>
		<center><h4>Department Test Count Report</h4></center>
		<div ID="res"></div>
	</div>
	<div id="loader"></div>
	<style>
		#res
		{
			border-bottom: 1px solid;
		}
		.table
		{
			margin-bottom: 0px;
		}
		.table-condensed tr th, .table-condensed tr td
		{
			padding: 2px;
			font-size: 12px;
		}
	</style>
</body>
</html>