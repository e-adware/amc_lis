<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

$srch=mysqli_real_escape_string($link, base64_decode($_GET["srch"]));

?>
<html>
<head>
	<title>Refer Doctor List</title>
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
	<link rel="stylesheet" href="../../css/matrix-style.css" />
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<link href="../../font-awesome/css/font-awesome.css" rel="stylesheet" />
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<br>
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Refer Doctor List</h4>
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
			
			<div class="noprint ">
				<button class="btn btn-print" onclick="javascript:window.print()"><i class="icon-print"></i> Print</button>
				<button class="btn btn-close" onclick="javascript:window.close()"><i class="icon-off"></i> Exit</button>
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="branch_id" value="<?php echo $branch_id; ?>">
	<input type="hidden" id="srch" value="<?php echo $srch; ?>">
</body>
</html>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		view();
		//$(".noprint").hide();
	});
	function view()
	{
		$("#loader").show();
		$.post("refer_doc_data.php",
		{
			type:"load_refer_doc",
			srch:$("#srch").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$("#print_div").hide();
			$(".edit_td").hide();
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
.txt_small{
	font-size:10px;
}
.table
{
	font-size: 11px;
}
@media print
{
	.noprint1
	{
		display:none;
	}
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
