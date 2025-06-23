<?php
$filename ="GST_datewise.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
?>
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
</style>


<?php
include('../../includes/connection.php');
$gst=$_GET['gst'];
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
$ph=$_GET['ph'];


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

$splr=mysqli_fetch_array(mysqli_query($link,"select substore_name from inv_sub_store  where substore_id='$ph'  "));
if($splr)
{
	$vsbstrnm=$splr['substore_name'];
}
else
{
	$vsbstrnm="All";
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
				<center><h5><u>GST Sales Report (Datewise) </u></h5></center>
				
			</div>
<table style="font-weight:bold;font-size:13px">
 <tr ><td >From </td><td > : <?php echo convert_date($fdate);?></td></tr>
 <tr><td >To </td> <td > : <?php echo convert_date($tdate);?></td></tr>
 <tr><td >Pharmacy </td><td > : <?php echo $vsbstrnm;?></td></tr>
</table>	
</table>
	
  <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" id="button" class="btn btn-success" value="Excel" onClick="sale_rep_det_exp('<?php echo $gst;?>','<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $ph;?>')" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>
        
      </tr>
      </table>	      
      		
<table class="table table-condensed table-bordered" style="font-size:13px;">
	<?php
		  $qdsinctgst=mysqli_query($link,"select distinct gst_percent from ph_sell_details where `entry_date` BETWEEN '$fdate' AND '$tdate'  order by gst_percent");
		  while($qdsinctgst1=mysqli_fetch_array($qdsinctgst))
		  {
			//$vcgst=$qdsinctgst1['gst_percent']/2;
			$vcgst=$qdsinctgst1['gst_percent'];
		?>
		<tr>
			<td colspan="6" style="font-weight:bold;font-size:13px">GST <?php echo $qdsinctgst1['gst_percent'];?> %</td>
			</tr>
		<tr>
			<th>Sl No</th>
			<th>Date</th>
			<th>Amount</th>
			<th>GST <?php echo $vcgst;?> % </th>
			
		</tr>
		
		
		
	<?php
	$i=1;
	$sale_amt=0;
	$gst_amt=0;
	$qry=mysqli_query($link,"SELECT  distinct entry_date  FROM `ph_sell_master` WHERE  `entry_date` BETWEEN '$fdate' AND '$tdate'  order by entry_date");
	while($r=mysqli_fetch_array($qry))
	{
		
		
		$qfirst=mysqli_fetch_array(mysqli_query($link,"select slno,bill_no from ph_sell_master where entry_date ='$r[entry_date]' order by slno limit 0,1"));
		$qlast=mysqli_fetch_array(mysqli_query($link,"select slno,bill_no from ph_sell_master where entry_date ='$r[entry_date]' order by slno desc limit 0,1"));
		
		
		if($ph>0)
		{
			$qsum=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.total_amount),0) as maxmrpsale,ifnull(sum(a.net_amount),0) as maxnetamt,ifnull(sum(a.gst_amount),0) as maxgstamt from ph_sell_details a,item_master b where a.`entry_date` = '$r[entry_date]' and a.substore_id='$ph'  and a.gst_percent='$qdsinctgst1[gst_percent]' and a.item_code=b.item_id and b.category_id='1' and b.sub_category_id='1' "));
			
			$q_ret=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.amount),0) as rsale from ph_item_return_master a,ph_sell_details b where a.`return_date`='$r[entry_date]' and b.gst_percent='$qdsinctgst1[gst_percent]' and a.substore_id='$ph' and a.bill_no=b.bill_no and a.item_code=b.item_code"));
			
			
		}
		else
		{
		  $qsum=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.total_amount),0) as maxmrpsale,ifnull(sum(a.net_amount),0) as maxnetamt,ifnull(sum(a.gst_amount),0) as maxgstamt from ph_sell_details a,item_master b where a.`entry_date` = '$r[entry_date]'   and a.gst_percent='$qdsinctgst1[gst_percent]' and a.item_code=b.item_id and b.category_id='1' and b.sub_category_id='1' "));
		  
		  $q_ret=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.amount),0) as rsale from ph_item_return_master a,ph_sell_details b where a.`return_date`='$r[entry_date]' and b.gst_percent='$qdsinctgst1[gst_percent]'  and a.bill_no=b.bill_no and a.item_code=b.item_code"));
		  
		  
		}
		
		$vmrpsale=0;	
	    if($fdate>'2021-01-31')
	    {
			$vmrpsale=round($qsum['maxnetamt']+$qsum['maxgstamt']);
			$gstamt=$vmrpsale-($vmrpsale*(100/(100+$qdsinctgst1['gst_percent'])));
		    $vmrpsale=round($vmrpsale);
			
		}
		else
		{
			$vmrpsale=round($qsum['maxmrpsale']);
			$gstamt=$vmrpsale-($vmrpsale*(100/(100+$qdsinctgst1['gst_percent'])));
		    //$vmrpsale=round($vmrpsale+$gstamt);
		}
		
		
		
		
		
		//$vcgstamt=$gstamt/2;
		$vcgstamt=round($gstamt);
		$vrang=$qfirst['bill_no'].'-'.$qlast['bill_no'];
		
				
		
	 ?>
	 <tr>
		<td><?php echo $i; ?></td>
		<td><?php echo convert_date($r['entry_date']); ?></td>
		<!--<td><?php echo $vrang; ?></td>-->
		
		<td><?php echo number_format($vmrpsale,2); ?></td>
		<td><?php echo number_format($vcgstamt,2); ?></td>
		
		
		
	 </tr>
	 <?php
		$sale_amt+=$vmrpsale;
		$gst_amt+=round($vcgstamt);
		
		$vttlsale+=$vmrpsale;
		$vttlgst+=round($vcgstamt);
	   $i++;
	}
	?>
		<tr>
			<th colspan="2">Total</th>
			<th><?php echo number_format($sale_amt,2);?></th>
			<th><?php echo number_format($gst_amt,2);?></th>
			
			
		</tr>
		
		<?php
	  }?>
	  
	  <tr>
			<th colspan="2">Grand Total</th>
			<th><?php echo number_format($vttlsale,2);?></th>
			<th><?php echo number_format($vttlgst,2);?></th>
			
			
		</tr>
		
	 </table>
	</div>
<script>
 //window.print();
</script>
</body>
</html>
