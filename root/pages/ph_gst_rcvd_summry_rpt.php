<?php
include('../../includes/connection.php');
$gst=$_GET['gst'];
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];

function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-M-Y', $timestamp);
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
?>
<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
</head>
<body>
<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>GST Received Report (Summary) </u></h5></center>
				
			</div>
<table style="font-size:13px;">
<tr><td colspan="5">From : <?php echo convert_date($fdate);?></td></tr>
<tr><td colspan="5">To : <?php echo convert_date($tdate);?></td></tr>
</table>	
	
<table class="table table-condensed table-bordered" style="font-size:13px;">
		<tr>
			<th>Sl No</th>
			<th>Description</th>
			<th>Received Amount</th>
			<th>GST Amount</th>
		</tr>
	<?php
	$i=1;
	 //$qry=mysqli_query($link,"SELECT distinct gst_per FROM `ph_purchase_receipt_details` WHERE bill_no IN (select bill_no from ph_purchase_receipt_master where bill_date BETWEEN '$fdate' AND '$tdate') order by gst_per");
	 $qry=mysqli_query($link,"SELECT distinct gst_percent FROM `ph_item_master` order by gst_percent");
	while($r=mysqli_fetch_array($qry))
	{
		
		$vdes=$r['gst_percent'].' %';
		$qsum=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(item_amount),0) as maxmrpsale,ifnull(sum(gst_amount),0) as maxgstamt from ph_purchase_receipt_details where order_no in (select order_no from ph_purchase_receipt_master where bill_date BETWEEN '$fdate' AND '$tdate') and gst_per='$r[gst_percent]' "));
		$vmrpsale=round($qsum[maxmrpsale]);
		//$vgstamt=round($qsum[maxgstamt]);
		$vgstamt=round($vmrpsale*$r['gst_percent']/100);
		
		
	 ?>
	 <tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $vdes; ?></td>
		<td><?php echo number_format($vmrpsale,2); ?></td>
		<td><?php echo number_format($vgstamt,2); ?></td>
		
			
	 </tr>
	 <?php	
	   $i++;
	}
	?>
	 </table>
	</div>
<script>
 //window.print();
</script>
</body>
</html>
