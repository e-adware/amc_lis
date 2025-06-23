<html>
<head>
<title>Item Return To Supplier</title>
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

.table-report tr:nth-last-child(1) td:nth-last-child(1)
{
	//background: #FFF;
}
</style>

</head>
<body>
<?php
include'../../includes/connection.php';


$supplr=$_GET['supplr'];
$ord=base64_decode($_GET['orderno']);

//$date2 = $_GET['date2'];
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
$splr=mysqli_fetch_array(mysqli_query($link,"select a.*,b.name,b.igst from ph_item_return_supplier_master a,inv_supplier_master b where a.supplier_id=b.id and a.returnr_no='$ord'"));

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container-fluid">
	<div class="" style="">
		<?php include('page_header_ph.php'); ?>
		<center><h5><u>Item Return To Supplier</u></h5></center>
	</div>
<table>

<tr><td colspan="4" style="font-weight:bold;font-size:13px">Supplier : <?php echo $splr['name'];?></td></tr>
<tr><td colspan="4" style="font-weight:bold;font-size:13px">Return No: <?php echo $ord;?></td></tr>
<tr><td colspan="4" style="font-weight:bold;font-size:13px">Print Date: <?php echo date('d/m/Y');?></td></tr>
</table>
<?php

?>
      <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
         <table width="100%">
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px"># &nbsp;</td>
				<td style="font-weight:bold;font-size:13px">Item Code</td>
				<td style="font-weight:bold;font-size:13px">Item Name</td>
				<td style="font-weight:bold;font-size:13px">Batch No</td>
				<td style="font-weight:bold;font-size:13px">Expiry</td>
				<td align="right" style="font-weight:bold;font-size:13px">MRP</td>
				<td align="right" style="font-weight:bold;font-size:13px">Rate</td>
				<td align="right" style="font-weight:bold;font-size:13px">GST %</td>
				<td align="right" style="font-weight:bold;font-size:13px"> Free Qnt.</td>
				<td align="right" style="font-weight:bold;font-size:13px"> Qnty.</td>
				<td align="right" style="font-weight:bold;font-size:13px"> Amount</td>
           </tr>
             <?php 
              $i=1;
              $itmttlamt=0;
              $qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from ph_item_return_supplier_master WHERE returnr_no='$ord' "));  
			  $qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name,b.gst from ph_item_return_supplier_details a,item_master  b WHERE a.returnr_no='$ord'  and a.item_id=b.item_id  ORDER BY b.item_name");  
			while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
				$vstk=0;
				
				$vqnty=0;
				$vqnty=$qrslctitm1['quantity'];
			    $itmttlamt+=$qrslctitm1['item_amount'];
			 ?>
             <tr class="line">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_id'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_name'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['batch_no'];?></td>
					<td style="font-size:13px"><?php echo convert_date($qrslctitm1['expiry_date']);?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['recpt_mrp'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['recept_cost_price'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['gst'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['free_qnt'];?></td>
					<td align="right" style="font-size:13px"><?php echo $vqnty;?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['item_amount'];?></td>
                
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			  ?>
             
             
               
              
<tr class="bline">         
<td colspan="11"></td>
</tr>

<tr class="line">
  <td colspan="10" style="font-size:13px;font-weight:bold" align="right">Sub Total :</td>
  <td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcv['amount'],2);?></td>
</tr>
<!--<tr class="line">
  <td colspan="10" style="font-size:13px;font-weight:bold" align="right">Less Discount :</td>
  <td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['dis_amt'];?></td>
</tr>-->
<tr class="line">
  <td colspan="10" style="font-size:13px;font-weight:bold" align="right">Add <?php if($splr['igst']>0){echo "IGST";}else{echo "GST";}?>  :</td>
  <td align="right" style="font-size:13px;font-weight:bold"><?php echo $splr['gst_amount'];?></td>
</tr>
<!--<tr class="line">
  <td colspan="10" style="font-size:13px;font-weight:bold" align="right">Adjust  :</td>
  <td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['adjust_amt'];?></td>
</tr>-->

<tr class="line">
  <td colspan="10" style="font-size:13px;font-weight:bold" align="right">Net Amount :</td>
  <td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['net_amount'];?></td>
</tr>



</table> 
 </div>  
</body>
</html>

