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
#mytable_txt tr td{ font-size:13px}
</style>

</head>
<body>
<?php
include'../../includes/connection.php';


$fdate=date('Y-m-d');
$tdate=date('Y-m-d', strtotime($fdate. ' + 90 days'));



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
				<?php include('page_header.php'); ?>
				<center><h5><u>Stock Expiry Report(Central Store) </u></h5></center>
				
			</div>


<table>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Print Date: <?php echo date('d-m-Y');?></td></tr>
</table>
<?php

?>


      <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input  type="button" class="btn btn-default" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" class="btn btn-success" name="button" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
         <table width="100%" id="mytable_txt">
            <tr bgcolor="#EAEAEA" class="bline" >
			   <td style="font-weight:bold;font-size:11px">#</td>
               <td style="font-weight:bold;font-size:11px">Item Code</td>
               <td style="font-weight:bold;font-size:11px">Item Name</td>
               <td align="right" style="font-weight:bold;font-size:11px">MRP</td>
               <td align="right" style="font-weight:bold;font-size:11px">Batch No</td>
               <td align="right" style="font-weight:bold;font-size:11px">Stock</td>
               <td align="right" style="font-weight:bold;font-size:11px">Expiry Date</td>
               <td align="right" style="font-weight:bold;font-size:11px">Bill No.</td>
               <td align="right" style="font-weight:bold;font-size:11px">Supplier</td>
              
            </tr>
             <?php 
             $i=1;
			  $qrslctitm=mysqli_query($link,"SELECT distinct(a.item_id),b.item_name FROM inv_maincurrent_stock a,item_master  b WHERE a.item_id=b.item_id and  a.closing_stock>0 and a.exp_date between '$fdate' and '$tdate' ORDER BY a.exp_date");  
			while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
				$vstk=0;
				$itmttlamt=0;
			    $qmrp=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,recept_cost_price from inv_main_stock_received_detail where item_id ='$qrslctitm1[item_id]' order by slno desc"));	
			 ?>
             <tr class="line">
			   <td style="font-size:11px"><?php echo $i;?></td>	 
               <td style="font-size:11px"><?php echo $qrslctitm1['item_id'];?></td>
               <td style="font-size:11px"><?php echo $qrslctitm1['item_name'];?></td>
               <td align="right" style="font-size:11px"><?php echo $qmrp['recpt_mrp'];?></td>
               <td></td> 
               <td></td> 
               <td></td>
               <td></td>
               <td></td>
                <td></td>   
             </tr>  
             
              
               <?php
			    $vrcprt=0;
			    $qbatch=mysqli_query($link,"select * from inv_maincurrent_stock  where item_id='$qrslctitm1[item_id]' and  exp_date between '$fdate' and '$tdate' and  closing_stock>0");
				while($qbatch1=mysqli_fetch_array($qbatch)){
				$rec=mysqli_fetch_array(mysqli_query($link,"SELECT `order_no`,`bill_no`,SuppCode FROM `inv_main_stock_received_detail` WHERE `item_id`='$qbatch1[item_id]' AND `expiry_date`='$qbatch1[exp_date]' AND `recept_batch`='$qbatch1[batch_no]'"));
				$qsupplier=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$rec[SuppCode]' "));
				
				$vrcptamt=$qmrp['recept_cost_price']*$qbatch1['closing_stock'];
				$itmttlamt=$itmttlamt+$vrcptamt;
				$vttlamt=$vttlamt+$vrcptamt;
				$vstk=$vstk+$qbatch1['quantity'];	
				
			   ?>
               <tr >  
               
                    <td></td> 
                    <td></td> 
                    <td></td> 
                    <td align="right"></td>
                    <td align="right" style="font-size:11px" ><?php echo $qbatch1['batch_no'];?></td>
                    <td align="right" style="font-size:11px"><?php echo $qbatch1['closing_stock'];?></td>
                    
                    <td align="right" style="font-size:11px" ><?php echo convert_date($qbatch1['exp_date']);?></td>
                    
                    <td align="right" style="font-size:11px"><?php echo $rec['bill_no'];?></td>
                    <td align="right" style="font-size:11px" ><?php echo substr($qsupplier['name'],0,15);?></td>
                  </tr> 
                     <?php
					 ;}?>
                    
                  <?php
			 $i++;}
			  ?>
             
             
               
              
<tr class="bline">         
<td colspan="9">&nbsp;</td>
</tr>
</table>  
    
 </div>  
</body>
</html>

