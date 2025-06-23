<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<script src="../../js/jquery.min.js"></script>
<script src="../../js/bootstrap.min.js"></script>
</head>
<body>
<?php
include'../../includes/connection.php';


$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
$ph=$_GET['ph'];

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
$splr=mysqli_fetch_array(mysqli_query($link,"select substore_name from inv_sub_store  where substore_id='$ph'  "));
if($splr)
{
	$vsbstrnm=$splr['substore_name'];
}
else
{
	$vsbstrnm="All";
}

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>

<div class="container-fluid">
	<input type="hidden" id="fdate" value="<?php echo $fdate;?>" />
	<input type="hidden" id="tdate" value="<?php echo $tdate;?>" />
	<input type="hidden" id="ph" value="<?php echo $ph;?>" />
	<div>
		<?php include('page_header_ph.php'); ?>
	</div>
	<center><h5><u>Store Receive Report</u></h5></center>
      <div class="noprint" style="text-align:center;">
		  <input type="button" name="button" class="btn btn-default" id="button" value="Print" onClick="window.print()" />
		  <input type="button" name="button" class="btn btn-success" id="button" value="Exit" onClick="window.close()" />
      </div> 
      <div id="res"></div>
 </div>
 <script src="../../js/jquery.min.js"></script>
 <script>
	$(document).ready(function()
	{
		load_data();
	})
	
	function load_data()
	{
		$.post("stock_report_ajax.php",
		{
			type:3,
		},
		function(data,status)
		{
			$("#res").html(data);
			$(".act_btn").hide();
		})
	}
 </script>
 <style>
	.table-condensed tr th, .table-condensed tr td
	{
		padding: 1px 3px 1px 3px;
		font-size: 12px;
	}
	.no_print
	{
		display:none;
	}
	@media print
	{
		.noprint
		{
			display:none;
		}
	}
	@page
	{
		margin-left:1px;
		margin-right:1px;
	}
 </style>
</body>
</html>

