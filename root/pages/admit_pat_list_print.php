<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$typ=$_GET['typ'];
$date1=$_GET['fdate'];
$date2=$_GET['tdate'];
$doc=$_GET['doc'];

if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

?>
<html>
<head>
	<title>Admit Patient List Report</title>
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
			<h4>Admit Patient List Report</h4>
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
			<!--<div class="noprint ">
				<input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>-->
			<div class="account_close_div">
			<?php
				if($account_break==0)
				{
					if($c_user==$user_entry)
					{
						$date=date("Y-m-d");
					
						if($date==$date22)
						{
							$close_btn="Close Today's Account";
						}else
						{
							$close_btn="Close Account of ".convert_date($date22);
						}
						$close_btn="Close Account";
			?>
				<?php
					if($pay_mode=="0" && $encounter==0) // All payment mode
					{
				?>
					<input type="button" id="btn_close" value="<?php echo $close_btn; ?>" class="btn btn-danger" onClick="close_account('<?php echo $user_val; ?>')" >
				<?php } ?>
					<div class="noprint ">
						<!--<input type="button" class="btn btn-info" id="det_excel" value="Excel" onclick="export_excel()">-->
						<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()"> 
						<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="close_window_child()">
					</div>
			<?php
					}	
				}else
				{
			?>
					<div class="noprint1 ">
						<!--<input type="button" class="btn btn-info" id="det_excel" value="Excel" onclick="export_excel()">-->
						<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()"> 
						<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="close_window_child()">
					</div>
			<?php
				}
			?>
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
	<input type="hidden" id="typ" value="<?php echo $typ; ?>">
	<input type="hidden" id="doc" value="<?php echo $doc; ?>">
</body>
</html>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		view();
		$(".noprint").hide();
	});
	function view()
	{
		$("#loader").show();
		$.post("admit_pat_list_qry.php",
		{
			type:"view_admit_pat_list",
			typ:$("#typ").val(),
			date1:$("#from").val(),
			date2:$("#to").val(),
			consultantdoctorid:$("#doc").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$(".btn-hide-print").hide();
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
	font-size: 12px;
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
