<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$con_cod_id=$_GET['con_cod_id'];
$payment_mode=$_GET['payment_mode'];
$dept_id=$_GET['dept_id'];
$visit_type=$_GET['visit_type'];
$patient_type=$_GET['patient_type'];
$branch_id=$_GET['branch_id'];
$user_entry=$_GET['user_entry'];

// important
$date11=$_GET['date1'];
$date22=$_GET['date2'];

?>
<html>
<head>
	<title>OPD Account Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<!--<h4>OPD Account Report</h4>-->
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
			<div class="noprint ">
				<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date11; ?>">
	<input type="hidden" id="to" value="<?php echo $date22; ?>">
	<input type="hidden" id="con_cod_id" value="<?php echo $con_cod_id; ?>">
	<input type="hidden" id="payment_mode" value="<?php echo $payment_mode; ?>">
	<input type="hidden" id="dept_id" value="<?php echo $dept_id; ?>">
	<input type="hidden" id="visit_type" value="<?php echo $visit_type; ?>">
	<input type="hidden" id="patient_type" value="<?php echo $patient_type; ?>">
	<input type="hidden" id="branch_id" value="<?php echo $branch_id; ?>">
	<input type="hidden" id="user_entry" value="<?php echo $user_entry; ?>">
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
		$.post("opd_account_data.php",
		{
			type:"opd_account",
			date1:$("#from").val(),
			date2:$("#to").val(),
			con_cod_id:$("#con_cod_id").val(),
			payment_mode:$("#payment_mode").val(),
			dept_id:$("#dept_id").val(),
			visit_type:$("#visit_type").val(),
			patient_type:$("#patient_type").val(),
			branch_id:$("#branch_id").val(),
			user_entry:$("#user_entry").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$("#excel_btn_hide").hide();
			$("#print_btn").hide();
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
	
	function close_window_child()
	{
		window.close();
	}
	function refreshParent()
	{
		window.opener.location.reload(true);
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
	.noprint1
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
	//padding: 0;
	padding: 0 10px 0 0;
}
</style>
