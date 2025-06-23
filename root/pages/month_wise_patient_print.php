<?php
include("../../includes/connection.php");

$fDt	=base64_decode($_GET['fDt']);
$tDt	=base64_decode($_GET['tDt']);

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Month Wise Patient Count Report</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/loader.css" />
	<script src="../../js/jquery.min.js"></script>
	<script>
		$(document).ready(function()
		{
			load_data();
		});
		function load_data()
		{
			$("#loader").show();
			$.post("month_wise_patient_ajax.php",
			{
				fdate	:$("#fDt").val().trim(),
				tdate	:$("#tDt").val().trim(),
				type	:1
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
	<div class="container-fluid">
		<?php
		include("page_header.php");
		?>
		<center><h4>Month Wise Patient Count Report</h4></center>
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