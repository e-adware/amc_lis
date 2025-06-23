<?php
include('../../includes/connection.php');

$filename ="purchase_receive_report.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
$ord=$_GET['orderno'];

function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-y', $timestamp);
return $new_date;
}

$s=mysqli_fetch_array(mysqli_query($link,"select a.*,b.name from ph_purchase_receipt_master a,ph_supplier_master b where a.supp_code=b.id and a.order_no='$ord'  "));
$q=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_details` WHERE `order_no`='$ord'");
$n=mysqli_num_rows($q);
?>
<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="7">Supplier : <?php echo $s['name'];?></th>
	</tr>
	
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
				<td align="right" style="font-weight:bold;font-size:13px"> Dis %.</td>
				<td align="right" style="font-weight:bold;font-size:13px"> Amount</td>
           </tr>
	
	 <?php 
              $i=1;
              $itmttlamt=0;
              $qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from ph_purchase_receipt_master WHERE order_no='$ord' "));  
			  $qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name,b.gst_percent from ph_purchase_receipt_details a,ph_item_master  b WHERE a.order_no='$ord'  and a.item_code=b.item_code  ORDER BY b.item_name");  
			while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
				$vstk=0;
				
				$vqnty=0;
				$vqnty=$qrslctitm1['recpt_quantity']+$qrslctitm1['free_qnt'];
			    $itmttlamt+=$qrslctitm1['item_amount'];
			 ?>
             <tr class="line">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_code'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_name'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['recept_batch'];?></td>
					<td style="font-size:13px"><?php echo convert_date($qrslctitm1['expiry_date']);?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['recpt_mrp'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['recept_cost_price'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['gst_percent'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['free_qnt'];?></td>
					<td align="right" style="font-size:13px"><?php echo $vqnty;?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['dis_per'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['item_amount'];?></td>
                
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			  ?>
             
             
               
              
<tr class="bline">         
<td colspan="10">&nbsp;</td>
</tr>

<tr class="line">
  <td colspan="11" style="font-size:13px;font-weight:bold" align="right">Sub Total :</td>
  <td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($itmttlamt,2);?></td>
</tr>
<tr class="line">
  <td colspan="11" style="font-size:13px;font-weight:bold" align="right">Less Discount :</td>
  <td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['dis_amt'];?></td>
</tr>
<tr class="line">
  <td colspan="11" style="font-size:13px;font-weight:bold" align="right">Add GST  :</td>
  <td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['gst_amt'];?></td>
</tr>
<tr class="line">
  <td colspan="11" style="font-size:13px;font-weight:bold" align="right">Adjust  :</td>
  <td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['adjust_amt'];?></td>
</tr>

<tr class="line">
  <td colspan="11" style="font-size:13px;font-weight:bold" align="right">Net Amount :</td>
  <td align="right" style="font-size:13px;font-weight:bold"><?php echo $qrcv['net_amt'];?></td>
</tr>


</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
