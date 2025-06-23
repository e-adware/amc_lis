<html>
<head>
<title>Sale Report</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<script src="../../js/jquery.min.js"></script>
</head>
<body>
<?php
include'../../includes/connection.php';


$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];

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

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>

<div class="container-fluid">
	<div class="" style="">
		<?php include('page_header_ph.php'); ?>
		<center><h5><u>Sale Report (Item wise) </u></h5></center>
	</div>
<table>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">From : <?php echo $fdate;?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">To &nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $tdate;?></td></tr>
</table>

<input type="hidden" id="fdate" value="<?php echo $fdate; ?>">
<input type="hidden" id="tdate" value="<?php echo $tdate; ?>">
	
<div class="noprint" style="text-align:center;"><input type="button" class="btn btn-default" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" class="btn btn-success" name="button" id="button" value="Exit" onClick="javascript:window.close()" /></div>
      
	<div id="res">
	
	</div>
 </div>  
</body>
<script>
	$(document).ready(function()
	{
		view();
	});
	function view()
	{
		$.post("sale_report_ajax.php",
		{
			type:4,
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
		},
		function(data,status)
		{
			$("#res").html(data);
			$("#print_btn").hide();
		})
	}
</script>
<style>
@page
{
	margin: 0.2cm;
}
@media print
{
	.noprint
	{
		display:none;
	}
}
.table-condensed
{
	font-size:12px;
}
</style>
</html>

