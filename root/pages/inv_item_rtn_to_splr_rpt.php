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


$fdate=$_GET['fdate'];
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

$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_item_return_supplier_master WHERE supplier_id='$splrid' and returnr_no='$billno' "));  
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Item Return Report</u></h5></center>
			</div>

<table>

<tr><td colspan="4" style="font-weight:bold;font-size:13px">Return No: <?php echo $billno;?></td></tr>
<tr><td colspan="4" style="font-weight:bold;font-size:13px">Supplier  : <?php echo $splr['name'];?></td></tr>
<tr><td colspan="4" style="font-weight:bold;font-size:13px">Return Date  : <?php echo convert_date($qrcv['date'],2);?></td></tr>
<tr><td colspan="4" style="font-weight:bold;font-size:13px">Print Date : <?php echo date('d-m-Y');?></td></tr>
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
				<td style="font-weight:bold;font-size:13px">GST %</td>
				<td align="right" style="font-weight:bold;font-size:13px">Qnty.</td>
				<td align="right" style="font-weight:bold;font-size:13px">Amount.</td>
           </tr>
             <?php 
              $i=1;
              $tot=0;
              
              
			  $qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name from inv_item_return_supplier_detail a,item_master  b,inv_item_return_supplier_master c WHERE a.returnr_no='$billno' and a.item_id=b.item_id and a.returnr_no=c.returnr_no and a.supplier_id=c.supplier_id and c.supplier_id='$splrid'  ORDER BY b.item_name");  
			 
			  while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
				$vitmttl=$vitmttl+$qrslctitm1['item_amount'];
				$vgstamt=$vgstamt+$qrslctitm1['gst_amount'];
			
			 ?>
             <tr class="line">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_id'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_name'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['batch_no'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['recpt_mrp'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['recept_cost_price'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['gst_per'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['quantity'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['item_amount'];?></td>
                
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			
			  /*$cgst=$vgstamt/2;
			  $tot1=$vitmttl+$cgst+$cgst;
			  $tot=round($tot1);*/
			  
			  $cgst=$vgstamt;
			  $tot1=$vitmttl+$cgst;
			  $tot=round($tot1);
			  ?>
             
             
               
              
<tr class="line">   
	      
	<td colspan="8" align="right" style="font-size:13px;font-weight:bold">Total :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($vitmttl,2);?></td>
</tr>

<tr class="line">   
	<td colspan="8" align="right" style="font-size:13px;font-weight:bold">GST :</td>
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
	<td colspan="8" align="right" style="font-size:13px;font-weight:bold">Net Amount(Rounded) :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($tot,2);?></td>

</tr>
</table>  
       
    
  
   </form>
 
 </div>  
</body>
</html>

