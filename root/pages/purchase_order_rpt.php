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
$splr=mysqli_fetch_array(mysqli_query($link,"select * from ph_supplier_master where id='$supplr'  "));

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>

<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Purchase Order Report</u></h5></center>
				
			</div>
	

<table>

<tr><td colspan="5" style="font-weight:bold;font-size:13px" >Supplier :<?php echo $splr['name'];?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Order No:<?php echo $orderno;?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Print Date:<?php echo date('d/m/Y');?></td></tr>
</table>
<?php

?>


      <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" class="btn btn-default" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" class="btn btn-success" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
         <table width="100%">
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
               <td  style="font-weight:bold;font-size:13px">Item Code</td>
               <td  style="font-weight:bold;font-size:13px">Item Name</td>
               <td  align="right" style="font-weight:bold;font-size:13px">Order Qnty.</td>
               
              
            </tr>
             <?php 
              $i=1;
			  $qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name from ph_purchase_order_details a,ph_item_master  b WHERE a.order_no='$orderno' and a.SuppCode='$supplr' and a.item_code=b.item_code  ORDER BY b.item_name");  
			while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
				$vstk=0;
				$itmttlamt=0;
			
			 ?>
             <tr class="line">
				<td style="font-size:13px"><?php echo $i;?></td>
               <td style="font-size:13px"><?php echo $qrslctitm1['item_code'];?></td>
               <td style="font-size:13px"><?php echo $qrslctitm1['item_name'];?></td>
               <td align="right" style="font-size:13px"><?php echo $qrslctitm1['order_qnt'];?></td>
              
                  
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			  ?>
             
             
               
              
<tr class="bline">         
<td colspan="8">&nbsp;</td>
</tr>
</table>  
       
      
  
   </form>
  </div>
 
</body>
</html>

