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


$rcv=base64_decode($_GET['rCv']);
$ord=base64_decode($_GET['oRd']);

//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}

$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_main_stock_received_master WHERE order_no='$ord' and receipt_no='$rcv'")); 
$splr=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_master where id='$qrcv[supp_code]'"));
//$qrcvcharge=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_main_stock_received_charge_master WHERE supp_code='$qrcv[supp_code]' and bill_no='$qrcv[bill_no]' and date='$qrcv[recpt_date]'"));   
$qemname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$qrcv[user]'")); 

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name "));

if($splr['igst']==1)
{
	$gsttxt="IGST";
}
else
{
	$gsttxt="GST";
}

?>
<div class="container">
	<div class="" style="">
		<?php include('page_header_ph.php'); ?>
		<center><b><u>Goods Received Report</u></b></center>
	</div>
<table style="font-size:13px;font-weight:bold;">
	<tr><td>Supplier</td><td style="width:78%;">: <?php echo $splr['name'];?></td><td>Bill No</td><td>: <?php echo $qrcv['bill_no'];?></td></tr>
	<tr><td>Bill Date</td><td>: <?php echo convert_date($qrcv['bill_date']);?></td><td>Print Date</td><td>: <?php echo date('d-m-Y');?></td></tr>
</table>
<div class="noprint" style="text-align:center;">
	<input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="window.print()" />
	<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="window.close()" />
</div>
         <table width="100%">
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
				<td style="font-weight:bold;font-size:13px">Item Code</td>
				<td style="font-weight:bold;font-size:13px">Item Name</td>
				<td style="font-weight:bold;font-size:13px">Batch No</td>
				<td style="font-weight:bold;font-size:13px">MRP</td>
				<td style="font-weight:bold;font-size:13px">Rate</td>
				<td style="font-weight:bold;font-size:13px"><?php echo $gsttxt;?> %</td>
				<td style="font-weight:bold;font-size:13px">Dis %</td>
				<td align="right" style="font-weight:bold;font-size:13px">Qnty.</td>
				<td align="right" style="font-weight:bold;font-size:13px">Free.</td>
				<td align="right" style="font-weight:bold;font-size:13px">Amount.</td>
           </tr>
             <?php 
              $i=1;
              $tot=0;
              
			  $qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name from inv_main_stock_received_detail a,item_master  b,inv_main_stock_received_master c WHERE a.order_no='$ord' and a.item_id=b.item_id and a.order_no=c.order_no and a.bill_no=c.bill_no and c.supp_code='$qrcv[supp_code]' and c.date='$qrcv[date]'  ORDER BY b.item_name");  
			  
			  
			  while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
				$vitemamount=0;  
				$vitemamount=$qrslctitm1['recpt_quantity']*$qrslctitm1['cost_price'];  
				$vitmttl=$vitmttl+$vitemamount;
				$vgstamt=$vgstamt+$qrslctitm1['gst_amount'];
			
			 ?>
             <tr class="line">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_id'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_name'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['batch_no'];?></td>
					<td style="font-size:13px"><?php echo number_format($qrslctitm1['recpt_mrp']*$qrslctitm1['strip_quantity'],2);?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['cost_price'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['gst_per'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['dis_per'];?></td>
					<td align="right" style="font-size:13px"><?php echo ($qrslctitm1['recpt_quantity']/$qrslctitm1['strip_quantity']);?></td>
					<td align="right" style="font-size:13px"><?php echo ($qrslctitm1['free_qnt']/$qrslctitm1['strip_quantity']);?></td>
					<td align="right" style="font-size:13px"><?php echo number_format($vitemamount,2);?></td>
                
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			
			  /*$cgst=$vgstamt/2;
			  $tot1=$vitmttl+$cgst+$cgst;
			  $tot=round($tot1);*/
			  
			  $cgst=$vgstamt;
			  $tot1=$vitmttl+$cgst+$qrcvcharge['delivery_charge']-$qrcv['dis_amt'];
			  $tot=round($tot1);
			  ?>
             
             
               
              
<tr class="line">
	<td colspan="10" align="right" style="font-size:13px;font-weight:bold">Total :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($vitmttl,2);?></td>
</tr>
<?php
if($qrcv['dis_amt']>0)
{
?>
<tr class="line">   
	<td colspan="10" align="right" style="font-size:13px;font-weight:bold">Discount:</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcv['dis_amt'],2);?></td>

</tr>
<?php
}
?>
<tr class="line">   
	<td colspan="10" align="right" style="font-size:13px;font-weight:bold"><?php echo $gsttxt;?> :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($cgst,2);?></td>

</tr>
<!--<tr class="line">   
	<td colspan="8" align="right" style="font-size:13px;font-weight:bold">CGST :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($cgst,2);?></td>

</tr>

<tr class="line">   
	<td colspan="8" align="right" style="font-size:13px;font-weight:bold">SGST :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($cgst,2);?></td>

</tr>
<tr class="line">   
	<td colspan="10" align="right" style="font-size:13px;font-weight:bold">Transport Charge :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcvcharge['delivery_charge'],2);?></td>

</tr>-->

<tr class="line">   
	<td colspan="10" align="right" style="font-size:13px;font-weight:bold">Net Amount(Rounded) :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($tot,2);?></td>
</tr>
<tr class="no_line">
	<td style="font-size:13px;font-weight:bold;">GST %</td>
	<td style="font-size:13px;font-weight:bold;">Amount</td>
	<td style="font-size:13px;font-weight:bold;">Gst Amt</td>
	<td colspan="8" style="font-size:13px"></td>
</tr>
<?php
$all_gst=array(0,5,12,18,28);
foreach($all_gst as $gst)
{
$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail WHERE `order_no`='$qrcv[order_no]' and `gst_per`='$gst'"));
?>
<tr class="no_line">
	<td style="font-size:13px"><?php echo $gst." %";?></td>
	<td style="font-size:13px"><?php echo number_format($qamt['maxamt'],2);?></td>
	<td style="font-size:13px"><?php echo number_format($qamt['maxgst'],2);?></td>
	<td colspan="8" style="font-size:13px"></td>
</tr>
<?php
}
?>
<tr>
	<td colspan="11">&nbsp;</td>
</tr>
<tr class="no_line">
	<td colspan="5" style="font-size:13px">Received By : <?php echo $qemname['name'];?></td>
	<td></td>
	<td></td>
	<td colspan="4" style="font-size:13px;text-align:right;">Authourised Signatory </td>
</tr>
</table>
</div>
<style>
	.table-condensed tr th, .table-condensed tr td
	{
		padding: 1px;
	}
</style>
<script src="../../js/jquery.min.js"></script>
<script>
	$(document).ready(function()
	{
		$(document).keyup(function(e)
		{
			if(e.keyCode==27)
			window.close();
		});
	})
</script>
</body>
</html>