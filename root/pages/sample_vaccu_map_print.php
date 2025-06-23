<?php
include("../../includes/connection.php");
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Sample Vaccu Mapped Details</title>
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
			$.post("sample_vaccu_map_ajax.php",
			{
				type	:3
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			});
		}
	</script>
</head>
<body>
	<div class="container-fluid">
		<?php
		//include("page_header.php");
		?>
		<center><h4>Sample Vaccu Mapped Details</h4></center>
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
		.table-condensed tr th, .table-condensed tr td {
			border-top: 0px !important;
		}
		.table-condensed tr td.first-cell {
			border-top: 1px solid #444 !important;
		}
	</style>
</body>
</html>