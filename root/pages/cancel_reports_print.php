<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

$branch_id=$_GET['branch_id'];
if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

$type=$_GET["type"];

$date1=$_GET["date1"];
$date2=$_GET["date2"];

$search_data=$_GET["search_data"];

if($type=="patient_cancel")
{
	$title="Patient Cancel Report";
}
if($type=="payment_cancel")
{
	$title="Payment Cancel Report";
}

?>
<html>
<head>
	<title><?php echo $title; ?></title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
	<div class="container-fluid">
		<div class="">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<div class="noprint ">
			<center>
				<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()"> 
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</center>
		</div>
		<center>
			<h4><?php echo $title; ?></h4>
			<b style="float: left;">From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
		</center>
		<br>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
	<input type="hidden" id="search_data" value="<?php echo $search_data; ?>">
	<input type="hidden" id="branch_id" value="<?php echo $branch_id; ?>">
	<input type="hidden" id="type" value="<?php echo $type; ?>">
<div id="loader" style="margin-top:-10%;"></div>
<!-- Loader -->
<link rel="stylesheet" href="../../css/loader.css" />
</body>
</html>
<script>
	$(document).ready(function(){
		view();
		//$(".noprint").hide();
	});
	function view()
	{
		$("#loader").show();
		$.post("cancel_reports_data.php",
		{
			type:$("#type").val(),
			search_data:$("#search_data").val(),
			branch_id:$("#branch_id").val(),
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$(".print_div").hide();
		})
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
	.account_close_div
	{
		display:none;
	}
	.noprint{
		display:none;
	 }
	<?php if($account_break==0){ ?>
	 *{ //display:none; }
	<?php } ?>
}
.ipd_serial
{
	display:none;
}
.table
{
	margin-bottom: 0px;
}
.table-condensed th, .table-condensed td
{
	padding: 0;
}
</style>
