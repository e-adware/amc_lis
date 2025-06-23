
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
<script>
	function sale_rep_det_hsn_exp(gst,f,t,ph)
	{
		//var gst="<?php echo $gst;?> ";
		var url="ph_gst_summry_rpt_excel.php?gst="+gst+"&fdate="+f+"&tdate="+t+"&ph="+ph;
		document.location=url;
	}
</script>

<?php
include('../../includes/connection.php');
$gst=$_GET['gst'];
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
$ph=$_GET['ph'];


//~ $qw=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `total_amt`=0");
//~ while($rt=mysqli_fetch_array($qw))
//~ {
	//~ mysqli_query($link,"UPDATE `ph_sell_details` SET `mrp`='0', `total_amount`='0', `net_amount`='0', `gst_percent`='0', `gst_amount`='0', `sale_price`='0' WHERE `bill_no`='$rt[bill_no]'");
//~ }



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
				<center><h5><u>GST Sales Report (Summary) </u></h5></center>
				
			</div>
			


<table>
 <tr ><td style="font-weight:bold;font-size:13px">From </td><td style="font-weight:bold;font-size:13px"> : <?php echo convert_date($fdate);?></td></tr>
 <tr><td style="font-weight:bold;font-size:13px">To  <td style="font-weight:bold;font-size:13px"> : <?php echo convert_date($tdate);?></td></tr>
 <tr><td style="font-weight:bold;font-size:13px">Pharmacy </td><td style="font-weight:bold;font-size:13px"> : <?php echo $vsbstrnm;?></td></tr>
</table>

<table width="100%">
<tr>
<td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" id="button" class="btn btn-success" value="Excel" onClick="sale_rep_det_hsn_exp('<?php echo $gst;?>','<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $ph;?>')" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>

</tr>
</table>	  
      	
<table class="table table-condensed table-bordered" style="font-size:13px;">
		<tr>
			<th>Sl No</th>
			<th>Description</th>
			<th>Sale Amount</th>
			<th>GST Amount</th>
		</tr>
	<?php
	$i=1;
	$sale_amt=0;
	$gst_amt=0;
	 $qry=mysqli_query($link,"SELECT distinct gst_percent FROM `ph_sell_details` WHERE  `entry_date` BETWEEN '$fdate' AND '$tdate' order by gst_percent");
	while($r=mysqli_fetch_array($qry))
	{
		
		$vdes=$r['gst_percent'].' % Sale';
		if($ph>0)
		{
			$qsum=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.total_amount),0) as maxmrpsale,ifnull(sum(a.net_amount),0) as maxnetsale,ifnull(sum(a.gst_amount),0) as maxgstamt from ph_sell_details a,item_master b where a.`entry_date` BETWEEN '$fdate' AND '$tdate' and a.substore_id='$ph' and a.gst_percent='$r[gst_percent]' and a.item_code=b.item_id and b.category_id='1' and b.sub_category_id='1' "));
		    
		    $q_ret=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.total_amount),0) as rsale,ifnull(sum(a.gst_amount),0) as rgst from ph_sell_details a, ph_item_return_master b where a.`entry_date` BETWEEN '$fdate' AND '$tdate' and a.gst_percent='$r[gst_percent]' and a.and substore_id='$ph' and a.bill_no=b.bill_no and a.item_code=b.item_code"));
		}
		else
		{
		$qsum=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.total_amount),0) as maxmrpsale,ifnull(sum(a.net_amount),0) as maxnetsale,ifnull(sum(a.gst_amount),0) as maxgstamt from ph_sell_details a,item_master b where a.`entry_date` BETWEEN '$fdate' AND '$tdate'  and a.gst_percent='$r[gst_percent]' and a.item_code=b.item_id and b.category_id='1' and b.sub_category_id='1' "));
		
		$q_ret=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.total_amount),0) as rsale,ifnull(sum(a.gst_amount),0) as rgst from ph_sell_details a, ph_item_return_master b where a.`entry_date` BETWEEN '$fdate' AND '$tdate' and a.gst_percent='$r[gst_percent]' and a.bill_no=b.bill_no and a.item_code=b.item_code"));
	    }
	    
	    $vmrpsale=0;
	    if($fdate>'2020-01-31')
	    {
			$vmrpsale=round($qsum['maxnetsale']+$qsum['maxgstamt']);
			//$vmrpsale=round($qsum['maxmrpsale']);
			
		}
		else
		{
			$vmrpsale=round($qsum['maxmrpsale']);
		}
		
		$gstamt=$vmrpsale-($vmrpsale*(100/(100+$r['gst_percent'])));
		//$vgstamt=round($qsum[maxgstamt]);
		$vgstamt=round($gstamt);
		
		
	 ?>
	 <tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $vdes; ?></td>
		<td><?php echo number_format($vmrpsale,2); ?></td>
		<td><?php echo number_format($vgstamt,2); ?></td>
	 </tr>
	 <?php
		$sale_amt+=$vmrpsale;
		$gst_amt+=$vgstamt;
	   $i++;
	}
	?>
		<tr>
			<th colspan="2">Total(Sale)</th>
			<th><?php echo number_format($sale_amt,2);?></th>
			<th><?php echo number_format($gst_amt,2);?></th>
		</tr>
		
		
		<tr>
			<td  colspan="4" >&nbsp;</td>
		</tr>
		<tr>
			<td  colspan="4" style="font-weight:bold">Retrun Details</td>
		</tr>
		<tr>
			<th>Sl No</th>
			<th>Description</th>
			<th>Return Amount</th>
			<th>GST Amount</th>
		</tr>
		
		<?php
	$i=1;
	$sale_amt=0;
	$gst_amt=0;
	 $qry=mysqli_query($link,"SELECT distinct a.gst_percent FROM `ph_sell_details` a,ph_item_return_master b WHERE  b.`return_date` BETWEEN '$fdate' AND '$tdate' and a.bill_no=b.bill_no and a.item_code=b.item_code and a.batch_no=b.batch_no order by a.gst_percent");
	
	while($r=mysqli_fetch_array($qry))
	{
		
		$vdes=$r['gst_percent'].' % Return';
		if($ph>0)
		{
			
		    
		    $q_ret=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.amount),0) as maxreturn from  ph_item_return_master a,ph_sell_details b where a.`return_date` BETWEEN '$fdate' AND '$tdate' and a.bill_no=b.bill_no and a.item_code=b.item_code and a.batch_no=b.batch_no and b.gst_percent='$r[gst_percent]' and a.substore_id='$ph' "));
		}
		else
		{
				
		$q_ret=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.amount),0) as maxreturn from  ph_item_return_master a,ph_sell_details b where a.`return_date` BETWEEN '$fdate' AND '$tdate' and a.bill_no=b.bill_no and a.item_code=b.item_code and a.batch_no=b.batch_no and b.gst_percent='$r[gst_percent]'  "));
	    }
	    
	    
		$vmrpsale=round($q_ret['maxreturn']);
			
		$gstamt=$vmrpsale-($vmrpsale*(100/(100+$r['gst_percent'])));
		//$vgstamt=round($qsum[maxgstamt]);
		$vgstamt=round($gstamt);
		
		
	 ?>
	 <tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $vdes; ?></td>
		<td><?php echo number_format($vmrpsale,2); ?></td>
		<td><?php echo number_format($vgstamt,2); ?></td>
	 </tr>
	 <?php
		$sale_amt+=$vmrpsale;
		$gst_amt+=$vgstamt;
	   $i++;
	}
	?>
		<tr>
			<th colspan="2">Total(Return)</th>
			<th><?php echo number_format($sale_amt,2);?></th>
			<th><?php echo number_format($gst_amt,2);?></th>
		</tr>
	 </table>
	 
	 
	</div>
<script>
 //window.print();
</script>
</body>
</html>
