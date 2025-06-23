<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
<style>

.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}
.bline td{border-bottom:1px solid;}
.line td{border-top:1px dotted;}
</style>

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
	<center><h5><u>Sale Report</u></h5></center>
	
<table>
 <tr><td style="font-weight:bold;font-size:13px">From </td><td style="font-weight:bold;font-size:13px"> : <?php echo $fdate;?></td></tr>
 <tr><td style="font-weight:bold;font-size:13px">To  <td style="font-weight:bold;font-size:13px"> : <?php echo $tdate;?></td></tr>
 <!--<tr><td style="font-weight:bold;font-size:13px">Pharmacy </td><td style="font-weight:bold;font-size:13px"> : <?php echo $vsbstrnm;?></td></tr>-->
</table>

      <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" class="btn btn-default" id="button" value="Print" onClick="window.print()" />&nbsp;<input type="button" name="button" class="btn btn-success" id="button" value="Exit" onClick="window.close()" /></div></td>
      </tr>
      </table> 
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
		$.post("sale_report_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			ph:$("#ph").val(),
			type:1,
		},
		function(data,status)
		{
			$("#res").html(data);
			$(".act_btn").hide();
		})
	}
 </script>
 <style>
	.table
	{
		font-size:13px;
	}
 </style>
</body>
</html>

