<html>
<head>
<title>Dump Stock</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../font-awesome/css/font-awesome.css" />
<script src="../../js/jquery.min.js"></script>
<style>
.t_rows
{
	cursor:pointer;
}
.t_rows_chk
{
	background:#80D690;
}
.div_icon
{
	display:inline-block;
	width:20px;
	float:right;
}
@media print{
 .noprint{
	 display:none;
 }
}
@page
{
	margin-left:20px;
	margin-right:20px;
}
.table tr th, .table tr td
{
	padding:0px 1px 0px 1px;
	font-size:12px;
}
</style>

</head>
<body onkeyup="close_window(event)" oncopy="return false;" oncut="return false;" onpaste="return false;" oncontextmenu="return false;">
<?php
include'../../includes/connection.php';

$sub_id=1;
$days=base64_decode($_GET['dY']);

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
//$dept=mysqli_fetch_array(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$ph'"));
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));
?>
<div class="container">
	<div class="" style="">
		<?php include('page_header_ph.php'); ?>
		<center><h5><u>Dump Stock Report</u></h5></center>
	</div>
	<input type="hidden" id="days" value="<?php echo $days;?>" />
	<b>Central Store</b><br/>
	<b><?php echo $days." Days";?></b>
	<span style="float:right">
		<div class="noprint">
			<input type="button" name="button" id="button" class="btn btn-info" value="Print" onClick="window.print()" />
			<input type="button" name="button" id="button" class="btn btn-danger" value="Exit" onClick="window.close()" />
		</div>
	</span>

	<div id="res"></div>
	<div id="loader" style="display:none;margin-top:-10%;"></div>
</div>
<link rel="stylesheet" href="../../css/loader.css" />
<script>
	$(document).ready(function()
	{
		load_data();
	});
	function load_data()
	{
		$("#loader").show();
		$.post("inv_stock_report_ajax.php",
		{
			days:$("#days").val().trim(),
			type:2,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
			$("select").hide();
			$(".btn-primary").hide();
		})
	}
	function click_chk(ths,i)
	{
		//alert(ths);
		if($(ths).hasClass("t_rows_chk")==true)
		{
			$(ths).removeClass("t_rows_chk");
			$("#icon"+i).hide();
		}
		else
		{
			$(ths).addClass("t_rows_chk");
			$("#icon"+i).show();
		}
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

