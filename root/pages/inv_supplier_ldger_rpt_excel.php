<?php

$filename ="purchase_receive_report.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
?>

<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />

</head>
<body>
<?php
include'../../includes/connection.php';


$rcptdate=$_GET['rcptdate'];
$tdate=$_GET['tdate'];
$splrid=$_GET['splirid'];
$billno=$_GET['billno'];


//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
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

$splr=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_master where  id='$splrid'  "));
$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_main_stock_received_master WHERE supp_code='$splrid' and bill_no='$billno' and recpt_date='$rcptdate'  ")); 
$qrcvcharge=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_main_stock_received_charge_master WHERE supp_code='$splrid' and bill_no='$billno' and recpt_date='$rcptdate'  "));   
$qemname=mysqli_fetch_array(mysqli_query($link,"select a.name from employee a,inv_purchase_order_master b  where a.emp_id=b.user and b.order_no='$qrcv[order_no]'")); 
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

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
				<center><h5><u>Item Received Report</u></h5></center>
			</div>


<table style="font-weight:bold;font-size:13px">

<tr><td >Bill No </td><td> : <?php echo $billno;?></td></tr>
<tr><td >Order No </td><td> : <?php echo $qrcv['order_no'];?></td></tr>
<tr><td >Received No </td><td> : <?php echo $qrcv['goods_rcv_no'];?></td></tr>
<tr><td >Supplier </td><td> : <?php echo $splr['name'];?></td></tr>
<tr><td >Received Date </td><td> : <?php echo convert_date($qrcv['recpt_date'],2);?></td></tr>
<tr><td >Print Date </td><td> : <?php echo date('d-m-Y');?></td></tr>
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
              
              
			  $qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name from inv_main_stock_received_detail a,item_master  b,inv_main_stock_received_master c WHERE a.bill_no='$billno' and a.item_id=b.item_id and a.order_no=c.order_no and a.bill_no=c.bill_no and c.supp_code='$splrid' and c.recpt_date='$rcptdate'  ORDER BY b.item_name");  
			 
			  while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
				$vitemamount=0;  
				$vitemamount=$qrslctitm1['recpt_quantity']*$qrslctitm1['recept_cost_price'];  
				$vitmttl=$vitmttl+$vitemamount;
				$vgstamt=$vgstamt+$qrslctitm1['gst_amount'];
			
			 ?>
             <tr class="line">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_id'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_name'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['recept_batch'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['recpt_mrp'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['recept_cost_price'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['gst_per'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['dis_per'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['recpt_quantity'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['free_qnt'];?></td>
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

</tr>-->
<tr class="line">   
	<td colspan="10" align="right" style="font-size:13px;font-weight:bold">Transport Charge :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcvcharge['delivery_charge'],2);?></td>

</tr
<tr class="line">   
	<td colspan="10" align="right" style="font-size:13px;font-weight:bold">Net Amount(Rounded) :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($tot,2);?></td>

</tr>
<tr>
	<td colspan="11">&nbsp;</td>
</tr>
<tr class="no_line">
	<td colspan="5" style="font-size:13px">Indented By : <?php echo $qemname['name'];?></td>
	
	<td></td>
	<td></td>
	<td colspan="4" style="font-size:13px;text-align:right;">Authourised Signatory </td>
	
</tr>

</table>  
       
    
  
   </form>
 
 </div>  
</body>
</html>

