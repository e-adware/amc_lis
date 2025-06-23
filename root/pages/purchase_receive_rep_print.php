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

$ord=base64_decode($_GET['rCv']);

//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}

$splr=mysqli_fetch_array(mysqli_query($link,"select a.*,b.name from ph_purchase_receipt_master a,inv_supplier_master b where a.supp_code=b.id and a.order_no='$ord'"));
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));
?>
<div class="container">
	<div class="" style="">
		<?php include('page_header_ph.php'); ?>
		<center><h5><u>Purchase Received Report</u></h5></center>
	</div>
<table style="font-size:13px;font-weight:bold;">
	<tr><td>Supplier</td><td style="width:78%;">: <?php echo $splr['name'];?></td><td>Bill No</td><td>: <?php echo $splr['bill_no'];?></td></tr>
	<tr><td>Bill Date</td><td>: <?php echo convert_date($splr['bill_date']);?></td><td>Print Date</td><td>: <?php echo date('d-m-Y');?></td></tr>
</table>
<div class="noprint" style="text-align:center;">
	<input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="window.print()" />
	<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="window.close()" />
</div>
<table width="100%">
<tr class="bline">
<td colspan="12"></td>
</tr>
<tr bgcolor="#EAEAEA" class="bline">
<td style="font-weight:bold;font-size:13px">#</td>
<td style="font-weight:bold;font-size:13px">Item Code</td>
<td style="font-weight:bold;font-size:13px">Item Name</td>
<td style="font-weight:bold;font-size:13px">Batch No</td>
<td style="font-weight:bold;font-size:13px">Expiry</td>
<td align="right" style="font-weight:bold;font-size:13px">MRP</td>
<td align="right" style="font-weight:bold;font-size:13px">Rate</td>
<td align="right" style="font-weight:bold;font-size:13px">GST %</td>
<td align="right" style="font-weight:bold;font-size:13px">Qnt</td>
<td align="right" style="font-weight:bold;font-size:13px">Free</td>
<td align="right" style="font-weight:bold;font-size:13px">Dis %</td>
<td align="right" style="font-weight:bold;font-size:13px">Amount</td>
</tr>
<?php 
$i=1;
$itmttlamt=0;
$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from ph_purchase_receipt_master WHERE order_no='$ord' "));
$qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name,b.strip_quantity from ph_purchase_receipt_details a,item_master  b WHERE a.order_no='$ord'  and a.item_code=b.item_id and b.`item_name` like '$val%'");
while($qrslctitm1=mysqli_fetch_array($qrslctitm))
{
$vstk=0;

$vqnty=0;
$free_qnt=$qrslctitm1['free_qnt']/$qrslctitm1['strip_quantity'];
$vqnty=$qrslctitm1['recpt_quantity']/$qrslctitm1['strip_quantity'];
$itmttlamt+=$qrslctitm1['item_amount'];
?>
<tr class="line" onclick="chk(this)">
<td style="font-size:13px"><?php echo $i;?></td>
<td style="font-size:13px"><?php echo $qrslctitm1['item_code'];?></td>
<td style="font-size:13px"><?php echo $qrslctitm1['item_name'];?></td>
<td style="font-size:13px"><?php echo $qrslctitm1['recept_batch'];?></td>
<td style="font-size:13px"><?php echo date("Y-m", strtotime($qrslctitm1['expiry_date']));?></td>
<td align="right" style="font-size:13px"><?php echo number_format($qrslctitm1['recpt_mrp']*$qrslctitm1['strip_quantity'],2);?></td>
<td align="right" style="font-size:13px"><?php echo number_format($qrslctitm1['cost_price'],2);?></td>
<td align="right" style="font-size:13px"><?php echo $qrslctitm1['gst_per'];?></td>
<td align="right" style="font-size:13px"><?php echo $vqnty;?></td>
<td align="right" style="font-size:13px"><?php echo $free_qnt;?></td>
<td align="right" style="font-size:13px"><?php echo $qrslctitm1['dis_per'];?></td>
<td align="right" style="font-size:13px"><?php echo number_format($qrslctitm1['item_amount'],2);?></td>
</tr> 
<?php
$i++;
}
$cr_gst=0;
$cr_qry=mysqli_query($link,"SELECT `gst` FROM `ph_purchase_receipt_credits` WHERE `order_no`='$ord' AND `amount`>'0'");
while($cr_rs=mysqli_fetch_assoc($cr_qry))
{
	//$cr_gst+=(($cr_rs['amount']*$cr_rs['gst_per'])/100);
	$cr_gst+=$cr_rs['gst'];
}
?>
<tr class="bline">         
<td colspan="12"></td>
</tr>

<tr class="line">
<td colspan="11" style="font-size:13px;font-weight:bold" align="right">Sub Total :</td>
<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($itmttlamt,2);?></td>
</tr>
<tr class="line">
<td colspan="11" style="font-size:13px;font-weight:bold" align="right">Add <?php if($splr['igst']>0){echo "IGST";}else{echo "GST";}?>  :</td>
<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcv['gst_amt']-$cr_gst,2);?></td>
</tr>
<?php
if($qrcv['dis_amt']>0)
{
?>
<tr class="line">
<td colspan="11" style="font-size:13px;font-weight:bold" align="right">Less Discount :</td>
<td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['dis_amt'];?></td>
</tr>
<?php
}

if($qrcv['credit_amt']>0)
{
?>
<tr class="line">
<td colspan="11" style="font-size:13px;font-weight:bold" align="right">Credit Note :</td>
<td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['credit_amt'];?></td>
</tr>
<?php
}

if($qrcv['adjust_amt']>0)
{
?>
<tr class="line">
<td colspan="11" style="font-size:13px;font-weight:bold" align="right">Adjust  :</td>
<td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['adjust_amt'];?></td>
</tr>
<?php
}

if($cr_gst>0)
{
?>
<tr class="line">
<td colspan="11" style="font-size:13px;font-weight:bold" align="right">Adjust GST :</td>
<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($cr_gst,2);?></td>
</tr>
<?php
}

if($qrcv['tcs_amt']>0)
{
?>
<tr class="line">
<td colspan="11" style="font-size:13px;font-weight:bold" align="right">TCS :</td>
<td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['tcs_amt'];?></td>
</tr>
<?php
}
?>
<tr class="line">
<td colspan="11" style="font-size:13px;font-weight:bold" align="right">Net Amount (round off) :</td>
<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format(round($qrcv['net_amt']-$cr_gst),2);?></td>
</tr>

</table>

<?php
$all_gst=array(0,5,12,18,28);
?>
<table class="" style="width:40%;font-size:13px;">
<tr class="bline">
<td colspan="3"></td>
</tr>
<tr class="bline">
<td><b>GST %</b></td>
<td><b>Amount</b></td>
<td><b>Gst Amount</b></td>
</tr>
<?php
foreach($all_gst as $gst)
{
$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM ph_purchase_receipt_details WHERE `order_no`='$ord' and `gst_per`='$gst'"));
$cred_amt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `amount` FROM `ph_purchase_receipt_credits` WHERE `order_no`='$ord' AND `gst_per`='$gst'"));
$gst_amt=(($cred_amt['amount']*$gst)/100);
?>
<tr class="line">
<td><?php echo $gst." %";?></td>
<td><?php echo number_format($qamt['maxamt']-$cred_amt['amount'],2);?></td>
<td><?php echo number_format($qamt['maxgst']-$gst_amt,2);?></td>
</tr>
<?php
}
?>
<tr class="bline">
<td colspan="3"></td>
</tr>
</table>
</div>
</body>
</html>