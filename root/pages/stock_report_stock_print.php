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

#dly_rcp tr td{ font-size:12px;}
</style>

</head>
<body>
<?php
include'../../includes/connection.php';


$supplr=$_GET['supplr'];
$orderno=$_GET['orderno'];

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
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>

<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Available Stock Report </u></h5>
				
			</div>
</div>	



<tr><td colspan="5">Print Date:<?php echo date('d/m/Y');?></td></tr>
</table>
<?php

?>

<div class="container">
<div align="center">
   <div class="row">
      <div class="span12">
		  <div class="noprint"><input type="button" class="btn btn-default" name="button" id="button" value="Print" onClick="window.print()" />&nbsp;<input type="button" class="btn btn-default" name="button" id="button" value="Exit" onClick="window.close()" /></div>
         <table width="100%" id="dly_rcp">
            <tr bgcolor="#EAEAEA" class="bline">
			   <td style="font-weight:bold">#</td>
               <td style="font-weight:bold">Item Name</td>
               <td style="font-weight:bold">Batch No</td>
               <td style="font-weight:bold">HSN Code</td>
               <td align="right" style="font-weight:bold">CGST</td>
               <td align="right" style="font-weight:bold">SGST</td>
               <td align="right" style="font-weight:bold">Stock</td>
               <td align="right" style="font-weight:bold">Cost</td>
               <td align="right" style="font-weight:bold">Cost Value</td>
               <td align="right" style="font-weight:bold">MRP</td>
               <td align="right" style="font-weight:bold">MRP Value</td>
            </tr>
             <?php 
              $i=1;
			//$qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name FROM `ph_stock_master` a,ph_item_master b WHERE a.`quantity`>0 and a.item_code=b.item_code order by b.item_name");  
			$qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name,b.hsn_code,b.gst FROM `ph_stock_master` a,item_master b WHERE a.`quantity`>0 and a.item_code=b.item_id order by b.item_name");
			
			while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
			//$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$qrslctitm1[item_code]'"));
			$qrate=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,recept_cost_price FROM `ph_purchase_receipt_details` WHERE `item_code`='$qrslctitm1[item_code]' and  recept_batch='$qrslctitm1[batch_no]'"));
		
			$vmrpvlue=0;
			$vcostvalue=0;
			$vmrpvlue=$qrate['recpt_mrp']*$qrslctitm1['quantity'];
			$vcostvalue=$qrate['recept_cost_price']*$qrslctitm1['quantity'];
			$vttlmrp=$vttlmrp+$vmrpvlue;
			$vttlcstprice=$vttlcstprice+$vcostvalue
			
			 ?>
             <tr class="line">
				<td><?php echo $i;?></td>
				<td><?php echo $qrslctitm1['item_name'];?></td>
				<td><?php echo $qrslctitm1['batch_no'];?></td>
				<td><?php echo $qrslctitm1['hsn_code'];?></td>
				<td align="right"><?php echo ($qrslctitm1['gst']/2);?></td>
				<td align="right"><?php echo ($qrslctitm1['gst']/2);?></td>
				<td align="right" style="font-size:13px"><?php echo $qrslctitm1['quantity'];?></td>
				<td align="right"><?php echo $qrate['recept_cost_price'];?></td>
				<td align="right"><?php echo number_format($vcostvalue,2);?></td>
				<td align="right"><?php echo $qrate['recpt_mrp'];?></td>
				<td align="right"><?php echo number_format($vmrpvlue,2);?></td>
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			  ?>
			<tr class="bline">         
			<td colspan="11"></td>
			</tr>
			
			<tr>
				<td colspan="8" style="text-align:right;font-weight:bold">Total</td>
				<td style="text-align:right;font-weight:bold"><?php echo number_format($vttlcstprice,2);?></td>
				<td></td>
				<td style="text-align:right;font-weight:bold"><?php echo number_format($vttlmrp,2);?></td>
			</tr> 
               
              

</table>  
       
      </div>
   </div>
  </div>
 </div>  
</body>
</html>

