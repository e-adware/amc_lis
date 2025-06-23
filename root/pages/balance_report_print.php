<?php
session_start();

include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);

//print_r($_GET);

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$encounter=$_GET['encounter'];
$branch_id=$_GET['branch_id'];
$uhid=$_GET['uhid'];
$bill_no=$_GET['bill_no'];
$pat_name=$_GET['pat_name'];
$address=$_GET['address'];
$val=$_GET['val'];

?>
<html>
<head>
	<title>Credit Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<!--<h4>Credit Report</h4>-->
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
			<!--<div class="noprint ">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>-->
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="date1" value="<?php echo $date1; ?>">
	<input type="hidden" id="date2" value="<?php echo $date2; ?>">
	<input type="hidden" id="encounter" value="<?php echo $encounter; ?>">
	<input type="hidden" id="branch_id" value="<?php echo $branch_id; ?>">
	<input type="hidden" id="uhid" value="<?php echo $uhid; ?>">
	<input type="hidden" id="bill_no" value="<?php echo $bill_no; ?>">
	<input type="hidden" id="pat_name" value="<?php echo $pat_name; ?>">
	<input type="hidden" id="address" value="<?php echo $address; ?>">
	<input type="hidden" id="val" value="<?php echo $val; ?>">
	
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
		$.post("balance_reports_data.php",
		{
			type:$("#val").val(),
			date1:$("#date1").val(),
			date2:$("#date2").val(),
			encounter:$("#encounter").val(),
			branch_id:$("#branch_id").val(),
			uhid:$("#uhid").val(),
			bill_no:$("#bill_no").val(),
			pat_name:$("#pat_name").val(),
			address:$("#address").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$(".print_btn").hide();
			
			setTimeout(function(){
				window.print();
				window.close();
			},100);
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
@page{
	margin: 0.2cm;
}
</style>
