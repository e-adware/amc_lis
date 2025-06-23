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
				<center><h5><u>Supplier Received GST Report</u></h5></center>
			</div>

<table>

<tr><td  style="font-weight:bold;font-size:13px">From : <?php echo convert_date($fdate);?></td></tr>
<tr><td  style="font-weight:bold;font-size:13px">To &nbsp; &nbsp; : <?php echo convert_date($tdate);?></td></tr>
</table>
<?php

?>
      <table width="100%">
      <tr>
        <td colspan="5" style="text-align:right"><div class="noprint"><input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
         <table width="100%">
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
				<td style="font-weight:bold;font-size:13px">Bill Date</td>
	     		<td align="right" style="font-weight:bold;font-size:13px"> Amount</td>
				<td align="right" style="font-weight:bold;font-size:13px">CGST</td>
				<td align="right" style="font-weight:bold;font-size:13px">SGST</td>
				
           </tr>
             <?php 
             //////////for Pharmacy Item Only
		
             
			//$qgst=mysqli_query($link,"SELECT distinct a.gst_per FROM inv_main_stock_received_detail a,inv_main_stock_received_master b  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date between '$fdate' and '$tdate' order by gst_per");
             $qgst=mysqli_query($link,"SELECT distinct a.gst_per FROM inv_main_stock_received_detail a,inv_main_stock_received_master b,item_master c  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date between '$fdate' and '$tdate' and a.item_id=c.item_id and c.category_id='1' order by a.gst_per");
			while($qgst1=mysqli_fetch_array($qgst))
			{
			$i=1;
			$subttlamt=0;
			$subttlcgst=0;
		
		?>
		  <tr>
			  <td colspan="5" style="font-weight:bold">GST <?php echo number_format($qgst1['gst_per'],0);?> %</td>
		  </tr>
		<?php
        
        $q=mysqli_query($link,"SELECT distinct b.bill_date FROM inv_main_stock_received_detail a,inv_main_stock_received_master b  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date between '$fdate' and '$tdate' and a.gst_per='$qgst1[gst_per]' order by b.bill_date");
		while($r=mysqli_fetch_array($q))
		{
			
			//$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.item_amount),0) as maxamt FROM inv_main_stock_received_detail a,inv_main_stock_received_master b  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date='$r[bill_date]'  and a.gst_per='$qgst1[gst_per]' "));
			//$qbilldetail=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_payment_details where bill_no='$r[bill_no]' and supp_code='$r[supp_code]'"));
			$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.item_amount),0) as maxamt FROM inv_main_stock_received_detail a,inv_main_stock_received_master b,item_master c  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date='$r[bill_date]'  and a.gst_per='$qgst1[gst_per]' and a.item_id=c.item_id and c.category_id='1' "));
			
			$vgstamt1=$qamt['maxamt']*$qgst1['gst_per']/100;
			$vgstamt=round($vgstamt1);
			$cgst=$vgstamt/2;
			
			$subttlamt+=$qamt['maxamt'];
			$subttlcgst+=$cgst;
				
			 ?>
             <tr class="line">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo convert_date($r['bill_date']);?> </td>
					<td align="right" style="font-size:13px"><?php echo $qamt['maxamt'];?> </td>
					<td align="right" style="font-size:13px"><?php echo number_format($cgst,2);?> </td>
					<td align="right" style="font-size:13px"><?php echo number_format($cgst,2);?> </td>
					
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			
			  
			  ?>
             
             
               
              
<tr class="line">
	<td colspan=2" style="text-align:right;font-weight:bold;font-size:13px">Total :</td>
	<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($subttlamt,2);?></td>
	<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($subttlcgst,2);?></td>
	<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($subttlcgst,2);?></td>
	
</tr>

<?php
}?>

</table>  
       
    
  
   </form>
 
 </div>  
</body>
</html>

