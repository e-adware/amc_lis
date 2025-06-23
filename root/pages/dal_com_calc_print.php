<?php
include('../../includes/connection.php');

$type=mysqli_real_escape_string($link, base64_decode($_GET['typ']));
$date1=mysqli_real_escape_string($link, base64_decode($_GET['dt1']));
$date2=mysqli_real_escape_string($link, base64_decode($_GET['dt2']));
$refbydoctorid=mysqli_real_escape_string($link, base64_decode($_GET['rdoc']));
$encounter=mysqli_real_escape_string($link, base64_decode($_GET['tp']));

?>
<html>
<head>
	<title>Referral Doctor Case</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="">
			<div class="">
				<?php //include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Refer Case</h4>
			<!--<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>-->
			<div class="noprint ">
				<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="date1" value="<?php echo $date1; ?>">
	<input type="hidden" id="date2" value="<?php echo $date2; ?>">
	<input type="hidden" id="refbydoctorid" value="<?php echo $refbydoctorid; ?>">
	<input type="hidden" id="encounter" value="<?php echo $encounter; ?>">
	<input type="hidden" id="type" value="<?php echo $type; ?>">
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
		$.post("dal_com_calc_data.php",
		{
			type:$("#type").val(),
			date1:$("#date1").val(),
			date2:$("#date2").val(),
			refbydoctorid:$("#refbydoctorid").val(),
			encounter:$("#encounter").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$("#print_btn").hide();
			$(".print_div").hide();
			
			window.print();
			window.close();
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
@page
{
	margin: 0.2cm;
}
*
{
	font-size:11px;
}
@media print
{
	.noprint{
		display:none;
	 }
}
</style>
