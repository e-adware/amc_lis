<?php
session_start();
include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$val=mysqli_real_escape_string($link, base64_decode($_GET["val"]));
$centreno=mysqli_real_escape_string($link, base64_decode($_GET["cno"]));
$refbydoctorid=mysqli_real_escape_string($link, base64_decode($_GET["rid"]));
$category_id=mysqli_real_escape_string($link, base64_decode($_GET["cid"]));
$type_id=mysqli_real_escape_string($link, base64_decode($_GET["did"]));
$testid=mysqli_real_escape_string($link, base64_decode($_GET["tid"]));
$branch_id=mysqli_real_escape_string($link, base64_decode($_GET["bid"]));

if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

if(!$refbydoctorid){ $refbydoctorid=0; }
if(!$testid){ $testid=0; }
?>
<html>
<head>
	<title>Centre Test Discount</title>
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
			<h4>Ref Doctor Contribution Setup</h4>
			<!--<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>-->
			<div class="noprint ">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="val" value="<?php echo $val; ?>">
	<input type="hidden" id="centreno" value="<?php echo $centreno; ?>">
	<input type="hidden" id="refbydoctorid" value="<?php echo $refbydoctorid; ?>">
	<input type="hidden" id="category_id" value="<?php echo $category_id; ?>">
	<input type="hidden" id="type_id" value="<?php echo $type_id; ?>">
	<input type="hidden" id="testid" value="<?php echo $testid; ?>">
	<input type="hidden" id="branch_id" value="<?php echo $branch_id; ?>">
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
		if(!$("#refbydoctorid").val())
		{
			var ref_docs="0";
		}
		else
		{
			var ref_docs=$("#refbydoctorid").val().toString();
		}
		if(!$("#testid").val())
		{
			var testids="0";
		}
		else
		{
			var testids=$("#testid").val().toString();
		}
		
		$("#loader").show();
		$.post("centre_test_discount_setup_data.php",
		{
			type:$("#val").val(),
			centreno:$("#centreno").val(),
			ref_docs:ref_docs,
			category_id:$("#category_id").val(),
			type_id:$("#type_id").val(),
			testids:testids,
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$("#print_div").hide();
			$(".del_btn").hide();
		})
	}
	function tr_focus(own_cls,cls)
	{
		$("."+cls).css({"color":"#000"});
		$("."+own_cls).css({"color":"#f00"});
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
	//@page{ size: landscape; }  
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
}
@page{
	margin:0.2cm;
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
hr {
    margin: 0;
}
</style>
