<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<script src="../../js/jquery.min.js"></script>
<style>
.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}
.bline td{border-bottom:1px solid;}
.line td{border-top:1px dotted;}
.table
	{
		font-size:13px;
	}
</style>

</head>
<body>
<?php
include'../../includes/connection.php';


$fdate=$_GET['date1'];
$tdate=$_GET['date2'];
$ph=1;

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
$splr=mysqli_fetch_array(mysqli_query($link,"select a.*,b.name from ph_purchase_order_master a,ph_supplier_master b where order_no='$ord'  "));

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Credit Report </u></h5></center>
				
			</div>


	<input type="hidden" id="fdate" value="<?php echo $fdate;?>" />
	<input type="hidden" id="tdate" value="<?php echo $tdate;?>" />
	<input type="hidden" id="ph" value="<?php echo $ph;?>" />
<table>
	<tr>
		<td style="font-weight:bold;font-size:13px">From</td>
		<td style="font-weight:bold;font-size:13px">: <?php echo convert_date($fdate);?></td>
	</tr>
	<tr>
		<td style="font-weight:bold;font-size:13px">To</td>
		<td style="font-weight:bold;font-size:13px">: <?php echo convert_date($tdate);?></td>
	</tr>
</table>
<div class="noprint" style="text-align:right;">
	<input type="button" class="btn btn-default" name="button" id="button" value="Print" onClick="window.print()" />
	<input type="button" class="btn btn-success" name="button" id="button" value="Exit" onClick="window.close()" />
</div>
<div id="res"></div>
</div>
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
			type:7,
		},
		function(data,status)
		{
			$("#res").html(data);
			$(".act_btn").hide();
		})
	}
</script>
</body>
</html>

