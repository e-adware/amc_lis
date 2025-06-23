<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$uhid=$_GET["uhid"];
$opd_id=$_GET["opd"];
$ipd_id=$_GET["ipd"];
$batch_no=$_GET["batch"];

$fdate=$_GET["fdate"];
$tdate=$_GET["tdate"];
$ftime=$_GET["ftime"];
$ttime=$_GET["ttime"];
$dept=$_GET["dept"];
$pat_type=$_GET["pat_type"];

if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

?>
<html>
<head>
	<title>Worksheet</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php //include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Worksheet</h4>
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
			<div class="noprint ">
				<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="fdate" value="<?php echo $fdate; ?>">
	<input type="hidden" id="tdate" value="<?php echo $tdate; ?>">
	<input type="hidden" id="ftime" value="<?php echo $ftime; ?>">
	<input type="hidden" id="ttime" value="<?php echo $ttime; ?>">
	<input type="hidden" id="dept" value="<?php echo $dept; ?>">
	<input type="hidden" id="pat_type" value="<?php echo $pat_type; ?>">
	<input type="hidden" id="uhid" value="<?php echo $uhid; ?>">
	<input type="hidden" id="opd_id" value="<?php echo $opd_id; ?>">
	<input type="hidden" id="ipd_id" value="<?php echo $ipd_id; ?>">
	<input type="hidden" id="batch_no" value="<?php echo $batch_no; ?>">
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
		$("#loader").show();
		$.post("worksheet_new_ajax.php",
		{
			type:1,
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			ftime:$("#ftime").val(),
			ttime:$("#ttime").val(),
			dept:$("#dept").val(),
			pat_type:$("#pat_type").val(),
			uhid:$("#uhid").val(),
			opd_id:$("#opd_id").val(),
			ipd_id:$("#ipd_id").val(),
			batch_no:$("#batch_no").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$(".print_btn").hide();
			
			window.print();
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
	function close_account(user)
	{
		//alert(user);
		if(confirm("Are you sure want to close account ?"))
		{
			$.post("../pages/close_account_data.php",
			{
				type:"new_close_account_single",
				user:user,
			},
			function(data,status)
			{
				var res=data.split("@#$@");
				
				alert(res[0]+"'s account is closed");
				//window.location.reload(true);
				//~ $(".noprint").show();
				//~ $("#btn_close").hide();
				
				var user=$("#user").text().trim();
				
				url="new_account_report_receipt_detail_print.php?date1="+$("#from").val()+"&date2="+$("#to").val()+"&encounter="+$("#encounter").val()+"&user_entry="+$("#user_entry").val()+"&pay_mode="+$("#pay_mode").val()+"&EpMl="+user+"&account_break="+res[1];
				
				window.location.href=url;
			})
		}
	}
	function close_window()
	{
		window.close();
	}
</script>
<style>
@page { size: landscape; }
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
	 @page{
		 margin: 0.2cm;
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
	padding: 0;
	//padding: 0 10px 0 0;
}
.table th, .table td
{
	border: 1px solid #000;
}
.table caption + thead tr:first-child th, .table caption + thead tr:first-child td, .table colgroup + thead tr:first-child th, .table colgroup + thead tr:first-child td, .table thead:first-child tr:first-child th, .table thead:first-child tr:first-child td
{
	border-top: 1px solid #000;
}
hr
{
	margin: 2px 0;
}
</style>
