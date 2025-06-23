<?php
include('../../includes/connection.php');
$gst=$_GET['gst'];
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];

//~ $qw=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `total_amt`=0");
//~ while($rt=mysqli_fetch_array($qw))
//~ {
	//~ mysqli_query($link,"UPDATE `ph_sell_details` SET `mrp`='0', `total_amount`='0', `net_amount`='0', `gst_percent`='0', `gst_amount`='0', `sale_price`='0' WHERE `bill_no`='$rt[bill_no]'");
//~ }



function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-y', $timestamp);
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
				<center><h5><u>GST Return Report (Datewise) </u></h5></center>
				
			</div>
<table style="font-size:13px;">
<tr><td colspan="5">From : <?php echo convert_date($fdate);?></td></tr>
<tr><td colspan="5">To &nbsp; &nbsp;  : <?php echo convert_date($tdate);?></td></tr>

</table>	
	
<table class="table table-condensed table-bordered" style="font-size:13px;">
	<?php
	
		  $qdsinctgst=mysqli_query($link,"select distinct a.gst from item_master a,ph_item_return_master b where b.`return_date` BETWEEN '$fdate' AND '$tdate' and a.item_id=b.item_code  order by a.gst");
		  while($qdsinctgst1=mysqli_fetch_array($qdsinctgst))
		  {
			
			$vcgst=$qdsinctgst1['gst']/2;
		?>
		<tr>
			<td colspan="6" style="font-weight:bold;font-size:13px">GST <?php echo $qdsinctgst1['gst'];?> %</td>
			</tr>
		<tr>
			<th>Sl No</th>
			<th>Date</th>
			
			<th>Amount</th>
			<th>CGST <?php echo $vcgst;?> % </th>
			<th>SGST <?php echo $vcgst;?> % </th>
		</tr>
		
		
		
	<?php
	$i=1;
	$sale_amt=0;
	$gst_amt=0;
	 $qry=mysqli_query($link,"SELECT  distinct return_date  FROM `ph_item_return_master` WHERE  `return_date` BETWEEN '$fdate' AND '$tdate'  order by return_date");
	while($r=mysqli_fetch_array($qry))
	{
		
		
		$qfirst=mysqli_fetch_array(mysqli_query($link,"select slno,bill_no from ph_sell_master where entry_date ='$r[entry_date]' order by slno limit 0,1"));
		$qlast=mysqli_fetch_array(mysqli_query($link,"select slno,bill_no from ph_sell_master where entry_date ='$r[entry_date]' order by slno desc limit 0,1"));
		
		
		
		//$qsum=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amount),0) as maxmrpsale,ifnull(sum(gst_amount),0) as maxgstamt from ph_sell_details where `entry_date` = '$r[entry_date]'  and gst_percent='$qdsinctgst1[gst_percent]' "));
		
		$vmrpsale=0;  
		$q1=mysqli_query($link,"select a.item_code,a.batch_no,a.return_qnt from ph_item_return_master a,item_master b where a.`return_date` = '$r[return_date]'  and a.item_code=b.item_id and b.gst='$qdsinctgst1[gst]'");
		while($q2=mysqli_fetch_array($q1))
		{
			$vmrpsale1=0;
			$q3=mysqli_fetch_array(mysqli_query($link,"select mrp from ph_sell_details where item_code='$q2[item_code]' and batch_no='$q2[batch_no]'"));
			$vmrpsale1=$q2['return_qnt']*$q3['mrp'];
			$vmrpsale=$vmrpsale+$vmrpsale1;
		}
		
		$q_ret=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.total_amount),0) as rsale,ifnull(sum(a.gst_amount),0) as rgst from ph_sell_details a, ph_item_return_master b where a.`entry_date` BETWEEN '$fdate' AND '$tdate' and a.gst_percent='$qdsinctgst1[gst_percent]' and a.bill_no=b.bill_no and a.item_code=b.item_code"));
		
		//$vmrpsale=round($qsum[maxmrpsale]);
		$vgstamt=round($qsum[maxgstamt]);
		
		$gstamt=$vmrpsale-($vmrpsale*(100/(100+$qdsinctgst1['gst'])));
		//$gstamt=$vmrpsale*$gst/100;
		
		$vcgstamt=$gstamt/2;
		$vrang=$qfirst['bill_no'].'-'.$qlast['bill_no'];
		
		$vtt
		
		
		
	 ?>
	 <tr>
		<td><?php echo $i; ?></td>
		<td><?php echo convert_date($r['return_date']); ?></td>
		
		
		<td><?php echo number_format($vmrpsale,2); ?></td>
		<td><?php echo number_format($vcgstamt,2); ?></td>
		
		<td><?php echo number_format($vcgstamt,2); ?></td>
		
	 </tr>
	 <?php
		$sale_amt+=$vmrpsale;
		$gst_amt+=$vcgstamt;
	   $i++;
	}
	?>
		<tr>
			<th colspan="2">Total</th>
			<th><?php echo number_format($sale_amt,2);?></th>
			<th><?php echo number_format($gst_amt,2);?></th>
			
			<th><?php echo number_format($gst_amt,2);?></th>
		</tr>
		
		<?php
	  }?>
	 </table>
	</div>
<script>
 //window.print();
</script>
</body>
</html>
