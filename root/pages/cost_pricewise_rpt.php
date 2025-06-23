<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<style>
input[type="text"]
{
	border:none;
}
body {
	padding: 10px;
	/*font-size:15px; font-family: "Courier New", Courier, monospace; line-height: 18px;*/
}
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


$fdate=$_GET['date1'];
$tdate=$_GET['date2'];
$ph=$_GET['ph'];
$fid=0;
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
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Cost Price(s) </u></h5></center>
				
			</div>



<table>
<tr ><td style="font-weight:bold;font-size:13px">From </td><td style="font-weight:bold;font-size:13px"> : <?php echo $fdate;?></td></tr>
<tr><td style="font-weight:bold;font-size:13px">To  <td style="font-weight:bold;font-size:13px"> : <?php echo $tdate;?></td></tr>
<!--<tr><td style="font-weight:bold;font-size:13px">Pharmacy </td><td style="font-weight:bold;font-size:13px"> : <?php echo $vsbstrnm;?></td></tr>-->
</table>
<div class="noprint" style="text-align:right;">
	<input type="button" class="btn btn-default" name="button" id="button" value="Print" onClick="window.print()" />
	<input type="button" class="btn btn-success" name="button" id="button" value="Exit" onClick="window.close()" />
</div>
 <div id="res"></div>    
 </div>  
</body>
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
			type:8,
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
</html>

