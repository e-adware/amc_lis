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
				<center><h5><u>GST Sales Report on <?php echo $gst; ?> %</u></h5></center>
				
			</div>
<table>
<tr><td colspan="5">From : <?php echo convert_date($fdate);?></td></tr>
<tr><td colspan="5">To : <?php echo convert_date($tdate);?></td></tr>
</table>	
	
<table class="table table-condensed table-bordered" style="font-size:13px;">
		<tr>
			<th>Sl No</th>
			<th>Item Details</th>
			<th>MRP</th>
			<th>Quantity</th>
			<th style="text-align:right">Total Amount</th>
			<th style="text-align:right">GST Amount</th>
			
		</tr>
	<?php
	if($fdate=="" && $tdate=="")
	{
	 $q="SELECT * FROM `ph_sell_details` WHERE `gst_percent`='$gst'";
	}
	else
	{
	 $q="SELECT distinct item_code FROM `ph_sell_details` WHERE `gst_percent`='$gst' AND `entry_date` BETWEEN '$fdate' AND '$tdate'";
	}
	$qry=mysqli_query($link,$q);
	$i=1;
	while($r=mysqli_fetch_array($qry))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT a.mrp,a.item_cost_price,b.`item_name` FROM ph_sell_details a,`item_master` b WHERE a.item_code=b.item_id and a.`item_code`='$r[item_code]' and a.`gst_percent`='$gst' AND a.`entry_date` BETWEEN '$fdate' AND '$tdate'"));
		$qslqnt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amount),0) as maxsaleamt,ifnull(sum(item_cost_price),0) as maxcstprice,ifnull(sum(sale_qnt),0) as maxsale,ifnull(sum(gst_amount),0) as maxgstamt from ph_sell_details where `item_code`='$r[item_code]' and `gst_percent`='$gst' AND `entry_date` BETWEEN '$fdate' AND '$tdate'"));
		
		$vttl=$vttl+$qslqnt['maxsaleamt'];
		$vttlcstprice=$vttlcstprice+$qslqnt['maxcstprice'];
		$vttlgstamt=$vttlgstamt+$qslqnt['maxgstamt'];
	 ?>
	 <tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $itm['item_name']; ?></td>
		<td><?php echo $itm['mrp']; ?></td>
		<td><?php echo $qslqnt['maxsale']; ?></td>
		<td style="text-align:right"><?php echo number_format($qslqnt['maxsaleamt'],2); ?></td>
		<td style="text-align:right"><?php echo $qslqnt['maxgstamt']; ?></td>
		
	 </tr>
	 <?php	
	   $i++;
	}
	?>
	<tr>
		<td colspan="4" style="font-weight:bold;font-size:13px;text-align:right">Total</td>
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttl,2);?></td>
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttlgstamt,2);?></td>
		
	</tr>
	 </table>
	</div>
<script>
window.print();
</script>
</body>
</html>
