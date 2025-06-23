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
		var url="ph_gst_hsnwise_rpt_excel.php?gst="+gst+"&fdate="+f+"&tdate="+t+"&ph="+ph;
		document.location=url;
	}
</script>

<?php
include('../../includes/connection.php');
$gst=$_GET['gst'];
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
$ph=$_GET['ph'];

$splr=mysqli_fetch_array(mysqli_query($link,"select substore_name from inv_sub_store  where substore_id='$ph'  "));
if($splr)
{
	$vsbstrnm=$splr['substore_name'];
}
else
{
	$vsbstrnm="All";
}


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
				<center><h5><u>HSN Wise Tax Deatils(Sales) </u></h5></center>
				
			</div>
<table style="font-weight:bold;font-size:13px">
 <tr ><td >From </td><td > : <?php echo convert_date($fdate);?></td></tr>
 <tr><td >To </td> <td > : <?php echo convert_date($tdate);?></td></tr>
 <tr><td >Pharmacy </td><td > : <?php echo $vsbstrnm;?></td></tr>
</table>	
		
  <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" id="button" class="btn btn-success" value="Excel" onClick="sale_rep_det_hsn_exp('<?php echo $gst;?>','<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $ph;?>')" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>
        
      </tr>
      </table>	      
      	
<table class="table table-condensed table-bordered" style="font-size:13px;">
	
		
		<tr>
			<th>Sl No</th>
			<th>HSN</th>
			<th>UQC</th>
			<th style="text-align:right">Total Qnty</th>
			<th style="text-align:right">Total Value</th>
			<th style="text-align:right">Taxable Value</th>
			<th style="text-align:right">CGST Amount</th>
			<th style="text-align:right">SGST Amount</th>
			
		</tr>
		
		
		
	<?php
	$i=1;
	$vunits="UNT-UNITS";
	$sale_amt=0;
	$gst_amt=0;
	 if($ph>0) 
	{
	 	 
	   $qry=mysqli_query($link,"SELECT DISTINCT b.hsn_code FROM `ph_sell_details` a,item_master b WHERE a.`entry_date` between '$fdate' and '$tdate' and a.substore_id='$ph' AND a.`item_code`=b.item_id and b.hsn_code!='' and b.category_id='1' and b.sub_category_id='1' order by b.hsn_code");
    }
    else
    {
		//$qry=mysqli_query($link,"SELECT DISTINCT a.`item_code`,b.item_name,b.hsn_code,b.item_type_id,b.gst FROM `ph_sell_details` a,item_master b WHERE a.`entry_date` between '$fdate' and '$tdate' AND a.`item_code`=b.item_id and b.hsn_code!='' order by b.hsn_code");
		
		$qry=mysqli_query($link,"SELECT DISTINCT b.hsn_code FROM `ph_sell_details` a,item_master b WHERE a.`entry_date` between '$fdate' and '$tdate' AND a.`item_code`=b.item_id and b.hsn_code!='' and b.category_id='1' and b.sub_category_id='1' order by b.hsn_code");
	}
	
	while($r=mysqli_fetch_array($qry))
	{
			
		if($ph>0)
		{			 
		    
		     $qquanty=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.sale_qnt),0) as maxsalqnty,ifnull(sum(a.total_amount),0) as maxsaleamt,ifnull(sum(a.net_amount),0) as maxnetamt,ifnull(sum(a.gst_amount),0) as maxgstamt from ph_sell_details a,item_master b where a.entry_date between '$fdate' and '$tdate' and a.substore_id='$ph' and a.item_code=b.item_id and b.hsn_code='$r[hsn_code]' and b.category_id='1' and b.sub_category_id='1'"));
		  
		     //$qreturnquanty=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.return_qnt),0) as maxrtrnqnty,ifnull(sum(a.amount),0) as maxrtrnamt from ph_item_return_master a,item_master b where  a.return_date between '$fdate' and '$tdate' and a.substore_id='$ph' and a.item_code=b.item_id and b.hsn_code='$r[hsn_code]' and b.category_id='1' and b.sub_category_id='1'"));
		    		   		 		
		}
		else
		{
		
		 
		  $qquanty=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.sale_qnt),0) as maxsalqnty,ifnull(sum(a.total_amount),0) as maxsaleamt,ifnull(sum(a.net_amount),0) as maxnetamt,ifnull(sum(a.gst_amount),0) as maxgstamt from ph_sell_details a,item_master b where a.entry_date between '$fdate' and '$tdate' and a.item_code=b.item_id and b.hsn_code='$r[hsn_code]' and b.category_id='1' and b.sub_category_id='1'"));
		  
		  //$qreturnquanty=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.return_qnt),0) as maxrtrnqnty,ifnull(sum(a.amount),0) as maxrtrnamt from ph_item_return_master a,item_master b where  a.return_date between '$fdate' and '$tdate' and a.item_code=b.item_id and b.hsn_code='$r[hsn_code]' and b.category_id='1' and b.sub_category_id='1' "));
	    }
	    
	    $qitemid=mysqli_fetch_array(mysqli_query($link,"select gst,item_type_id from item_master where hsn_code='$r[hsn_code]' and category_id='1' and gst>0 limit 0,1"));	
	    
	    $qtypename=mysqli_fetch_array(mysqli_query($link,"select item_type_id,item_type_name from item_type_master where item_type_id='$qitemid[item_type_id]'"));
	    $vnetsaleamt=0;	
	    if($fdate>'2021-01-31')
	    {
			$vnetsaleamt=$qquanty['maxnetamt']+$qquanty['maxgstamt'];
			
		}
		else
		{
			$vnetsaleamt=$qquanty['maxsaleamt'];
		}
		$vitmttlsaleqnty=$qquanty['maxsalqnty'];
		$vitmttlsaleamt=$vnetsaleamt;
		//$vitmttlsaleamt=$vnetsaleamt-$qreturnquanty['maxrtrnamt'];
		
		$vtaxableamt=0;
		$vtaxableamt1=$vitmttlsaleamt-($vitmttlsaleamt*(100/(100+$qitemid['gst'])));
		$vtaxableamt=round($vitmttlsaleamt-$vtaxableamt1,2);
			
		$vitmttlsaleamt=$vitmttlsaleamt;
		
		
		$vcgstamt=$vtaxableamt1/2;
		$vttlsalqnty+=$vitmttlsaleqnty;
		$vttlvalue1+=$vitmttlsaleamt;
		$vttltaxableamt1+=$vtaxableamt;		
		$vttlcgst1+=$vcgstamt;
				
		$vttigst=0;
		$vcesamt=0;
		
		
		
	 ?>
	 <tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $r['hsn_code']; ?></td>
		<!--<td><?php echo $qtypename['item_type_name']; ?></td>-->
		<td><?php echo $vunits; ?></td>
		<td style="text-align:right"><?php echo number_format($vitmttlsaleqnty,0); ?></td>
		<td style="text-align:right"><?php echo number_format($vitmttlsaleamt,2); ?></td>
		<td style="text-align:right"><?php echo number_format($vtaxableamt,2); ?></td>
		<td style="text-align:right"><?php echo number_format($vcgstamt,2); ?></td>
		<td style="text-align:right"><?php echo number_format($vcgstamt,2); ?></td>
		
	 </tr>
	 <?php
		
	   $i++;
	   
			$vttlvalue=round($vttlvalue1);
			$vttltaxableamt=round($vttltaxableamt1);
			$vttlcgst=round($vttlcgst1);
	}
	?>
		<tr>
			<td colspan="3" style="text-align:right;font-weight:bold;font-size:12px">Total (Rounded) </td>
			<td style="text-align:right;font-weight:bold;font-size:12px"><?php echo number_format($vttlsalqnty,0);?></td>
			<td style="text-align:right;font-weight:bold;font-size:12px"><?php echo number_format($vttlvalue,2);?></td>
			<td style="text-align:right;font-weight:bold;font-size:12px"><?php echo number_format($vttltaxableamt,2);?></td>
			<td style="text-align:right;font-weight:bold;font-size:12px"><?php echo number_format($vttlcgst,2);?></td>
			<td style="text-align:right;font-weight:bold;font-size:12px"><?php echo number_format($vttlcgst,2);?></td>
			
		</tr>
		
		<tr>
			<th colspan="8">Item Return</th>
		</tr>	
		
		
		<tr>
			<th>Sl No</th>
			<th>HSN</th>
			<th>UQC</th>
			<th style="text-align:right">Total Qnty</th>
			<th style="text-align:right">Total Value</th>
			<th style="text-align:right">Taxable Value</th>
			<th style="text-align:right">CGST Amount</th>
			<th style="text-align:right">SGST Amount</th>
			
		</tr>
		
		
		
	<?php
	// return
	$i=1;
	$vunits="UNT-UNITS";
	$sale_amt=0;
	$gst_amt=0;
	if($ph>0)
	{
	 $rtnqry=mysqli_query($link,"SELECT DISTINCT b.hsn_code FROM `ph_item_return_master` a,item_master b WHERE a.`return_date` between '$fdate' and '$tdate' and a.substore_id='$ph' AND a.`item_code`=b.item_id order by b.hsn_code");
    }
    else
    {
		$rtnqry=mysqli_query($link,"SELECT DISTINCT b.hsn_code FROM `ph_item_return_master` a,item_master b WHERE a.`return_date` between '$fdate' and '$tdate' AND a.`item_code`=b.item_id order by b.hsn_code");
		
		
	}
	while($r=mysqli_fetch_array($rtnqry)) // distinct hsn_code
	{
		if($ph>0)
		{
			
			 $qreturnquanty=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.return_qnt),0) as maxrtrnqnty,ifnull(sum(a.amount),0) as maxrtrnamt from ph_item_return_master a,item_master b where  a.return_date between '$fdate' and '$tdate' and a.substore_id='$ph' and a.item_code=b.item_id and b.hsn_code='$r[hsn_code]' and b.category_id='1' and b.sub_category_id='1'"));
		}
		else
		{
			 $qreturnquanty=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.return_qnt),0) as maxrtrnqnty,ifnull(sum(a.amount),0) as maxrtrnamt from ph_item_return_master a,item_master b where  a.return_date between '$fdate' and '$tdate'  and a.item_code=b.item_id and b.hsn_code='$r[hsn_code]' and b.category_id='1' and b.sub_category_id='1'"));
			 
			
			
		}
		
			
		 $qitemid=mysqli_fetch_array(mysqli_query($link,"select gst,item_type_id from item_master where hsn_code='$r[hsn_code]' and category_id='1' and gst>0 limit 0,1"));	
			    
		$vitmttlretrnqnty=$qreturnquanty['maxrtrnqnty'];
		$vitmttlretrnamt=$qreturnquanty['maxrtrnamt'];
		
		$vtaxableamt=0;
		$vrtrngstamt1=$vitmttlretrnamt-($vitmttlretrnamt*(100/(100+$qitemid['gst'])));
		//$vtaxableamt1=$qquanty['maxsaleamt'];
		
		$vrtrntaxableamt=($vitmttlretrnamt-$vrtrngstamt1);
		//$vtaxableamt=round($vtaxableamt1);
		
		$vrtrncgstamt=$vrtrngstamt1/2;
		$vttlrtrnqnty+=$vitmttlretrnqnty;
		$vttl_return_value1+=$vitmttlretrnamt;
		$vttltaxablertrnamt1+=$vrtrntaxableamt;		
		$vttlrtrncgst1+=$vrtrncgstamt;
			
		$vttigst=0;
		$vcesamt=0;
		
		
		
	 ?>
	 <tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $r['hsn_code']; ?></td>
		<td><?php echo $vunits; ?></td>
		<td style="text-align:right"><?php echo $vitmttlretrnqnty; ?></td>
		<td style="text-align:right"><?php echo number_format($vitmttlretrnamt,2); ?></td>
		<td style="text-align:right"><?php echo number_format($vrtrntaxableamt,2); ?></td>
		<td style="text-align:right"> <?php echo number_format($vrtrncgstamt,2); ?></td>
		<td style="text-align:right"><?php echo number_format($vrtrncgstamt,2); ?></td>
		
		
	 </tr>
	 <?php
		
	   $i++;
	   
			$vttl_return_value=($vttl_return_value1);
			$vttltaxablertrnamt=($vttltaxablertrnamt1);
			$vttlrtrncgst=($vttlrtrncgst1);
	}
	
	?>
		<tr>
			<td colspan="3" style="text-align:right;font-weight:bold;font-size:13px">Total Return</td>
			<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo $vttlrtrnqnty;?></td>
			<td style="text-align:right;font-weight:bold;font-size:12px"><?php echo number_format($vttl_return_value,2);?></td>
			<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($vttltaxablertrnamt,2);?></td>
			<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($vttlrtrncgst,2);?></td>
			<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($vttlrtrncgst,2);?></td>
			
		</tr>
		
		<tr>
			<th colspan="8">&nbsp;</th>
		</tr>
		
		<?php
		$vnettaxable=$vttltaxableamt-$vttltaxablertrnamt;
		$vnettcgst=$vttlcgst-$vttlrtrncgst;
		?>

<!--
		<tr>
			<td colspan="3" style="text-align:right;font-weight:bold;font-size:13px">Net</td>
			<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo ($vttlsalqnty-$vttlrtrnqnty)?></td>
			<td style="text-align:right;font-weight:bold;font-size:12px"><?php echo number_format($vttlvalue,2);?></td>
			<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($vnettaxable,2);?></td>
			<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($vnettcgst,2);?></td>
			<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($vnettcgst,2);?></td>
			
		</tr>
-->

		
	 </table>
	</div>
<script>
 //window.print();
</script>
</body>
</html>
