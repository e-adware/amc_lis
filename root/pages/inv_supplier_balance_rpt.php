<html>
<head>
<title>Supplier Balance Report</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<script src="../../js/jquery.min.js"></script>
<style>
@media print{
 .noprint{
	 display:none;
 }
}
@page
{
	margin-left:5px;
	margin-right:5px;
}
.table tr th, .table tr td
{
	padding:0px 3px 0px 3px;
	font-size:13px;
}
</style>

</head>
<body onkeyup="close_window(event)">
<?php
include'../../includes/connection.php';

$supp=base64_decode($_GET['supp']);
$fdate=base64_decode($_GET['fdate']);
$tdate=base64_decode($_GET['tdate']);

//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}


function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
$splr=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_master where id='$splrid'  "));

$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_item_return_supplier_master WHERE supplier_id='$splrid' and returnr_no='$billno' "));  
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container">
	<div class="" style="">
		<?php include('page_header_ph.php'); ?>
		<center><h5><u>Supplier Balance Report</u></h5></center>
	</div>
	<input type="hidden" id="supp" value="<?php echo $supp;?>" />
	<input type="hidden" id="fdate" value="<?php echo $fdate;?>" />
	<input type="hidden" id="tdate" value="<?php echo $tdate;?>" />
	<span style="font-size:13px;"><b>Print Date : <?php echo date('d-m-Y');?></b></span>
	<span style="float:right">
		<div class="noprint"><input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div>
	</span>

	<div id="res"></div>
</div>
<script>
	$(document).ready(function()
	{
		load_data();
	});
	function load_data()
	{
		$.post("inv_supplier_account_ajax.php",
		{
			supp:$("#supp").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:3,
		},
		function(data,status)
		{
			$("#res").html(data);
			$(".btn-primary").hide();
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
</body>
</html>

