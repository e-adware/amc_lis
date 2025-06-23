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
$encounter=$_GET['encounter'];
$encounter_val=$_GET['encounter'];
$pay_mode=$_GET['pay_mode'];
$user_entry=$_GET['user_entry'];
$user_val=$_GET['EpMl'];
$account_break=$_GET['account_break'];
$branch_id=$_GET['branch_id'];

if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}


// important
$date11=$_GET['date1'];
$date22=$_GET['date2'];

?>
<html>
<head>
	<title>Refund Report</title>
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
			<h4>Refund Report</h4>
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
	<input type="hidden" id="encounter" value="<?php echo $encounter; ?>">
	<input type="hidden" id="user_entry" value="<?php echo $user_entry; ?>">
	<input type="hidden" id="pay_mode" value="<?php echo $pay_mode; ?>">
	<input type="hidden" id="account_break" value="<?php echo $account_break; ?>">
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
		$.post("new_account_report_data.php",
		{
			type:"refund_report",
			date1:$("#from").val(),
			date2:$("#to").val(),
			encounter:$("#encounter").val(),
			user_entry:$("#user_entry").val(),
			pay_mode:$("#pay_mode").val(),
			account_break:$("#account_break").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$("#print_div").hide();
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
	function close_window_child()
	{
		window.close();
	}
	function refreshParent()
	{
		window.opener.location.reload(true);
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
