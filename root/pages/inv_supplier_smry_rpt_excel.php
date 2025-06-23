<?php

$filename ="Supplier_summary_report.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
?>
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

$splrid=$_GET['splirid'];
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
$bltype=$_GET['bltype'];

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
if($splrid==0)
{
	$vsplrname="All";
}
else
{
	$vsplrname=$splr['name'];
}
$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_item_return_supplier_master WHERE supplier_id='$splrid' and returnr_no='$billno' "));  
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Summary Report</u></h5></center>
			</div>
<table>
 <tr ><td style="font-weight:bold;font-size:13px">From </td><td style="font-weight:bold;font-size:13px"> : <?php echo convert_date($fdate);?></td></tr>
 <tr><td style="font-weight:bold;font-size:13px">To  <td style="font-weight:bold;font-size:13px"> : <?php echo convert_date($tdate);?></td></tr>
 <tr><td style="font-weight:bold;font-size:13px">Print Date  <td style="font-weight:bold;font-size:13px"> : <?php echo date('d-m-Y');?></td></tr>
 <tr><td style="font-weight:bold;font-size:13px">Supplier </td><td style="font-weight:bold;font-size:13px"> : <?php echo $vsplrname;?></td></tr>
</table>
<?php

?>
      <table width="100%">
      <tr>
        <td colspan="8" style="text-align:right"><div class="noprint"><input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
         <table width="100%" >
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
				<td style="font-weight:bold;font-size:13px">Order No</td>
				<td style="font-weight:bold;font-size:13px">Receipt No</td>
				<td style="font-weight:bold;font-size:13px">Bill No</td>
				<td style="font-weight:bold;font-size:13px">Bill Date</td>
				<td style="font-weight:bold;font-size:13px">Received Date</td>
				<td style="font-weight:bold;font-size:13px">Supplier</td>
				
				<td align="right" style="font-weight:bold;font-size:13px">Bill Amount</td>
				<td align="right" style="font-weight:bold;font-size:13px">User</td>
           </tr>
             <?php 
              $i=1;
              $tot=0;
                            				
				if($splrid==0)
				{
					if($bltype==0)	
					{
					  $q=mysqli_query($link,"SELECT * FROM inv_main_stock_received_master  WHERE bill_date between '$fdate' and '$tdate' and del =0 order by bill_date,slno desc");
					}
					else
					{
					  $q=mysqli_query($link,"SELECT * FROM inv_main_stock_received_master  WHERE bill_date between '$fdate' and '$tdate' and del =0 and bill_type_id='$bltype'  order by bill_date,slno desc");
					}
				     
				}
				else
				{
					if($bltype==0)	
					{
					  $q=mysqli_query($link,"SELECT * FROM inv_main_stock_received_master  WHERE bill_date between '$fdate' and '$tdate' and del =0 and supp_code='$splrid' order by bill_date,slno desc");
					}
					else
					{
					  $q=mysqli_query($link,"SELECT * FROM inv_main_stock_received_master  WHERE bill_date between '$fdate' and '$tdate' and del =0 and supp_code='$splrid' and bill_type_id='$bltype' order by bill_date,slno desc");
					}
			
				   
				}

				while($r=mysqli_fetch_array($q))
				{
					$qbill=mysqli_fetch_array(mysqli_query($link,"select * from inv_main_stock_received_master where bill_no='$r[bill_no]' and supp_code='$r[supp_code]' and del =0"));
					$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supp_code]'"));
					$qbilltype=mysqli_fetch_array(mysqli_query($link,"select bill_type_name from inv_bill_type_master where bill_type_id='$r[bill_type_id]'"));
					$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$qbill[user]'"));
					
					$vttl=$vttl+$qbill['net_amt'];
					$vttlpaid=$vttlpaid+$r['paid'];
					$vttlbal=$vttlbal+$r['balance'];
				
			 ?>
             <tr class="line">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo $r['order_no'];?></td>
					<td style="font-size:13px"><?php echo $r['receipt_no'];?></td>
					<td style="font-size:13px"><?php echo $r['bill_no'];?></td>
					<td style="font-size:13px"><?php echo convert_date($qbill['bill_date']);?></td>
					<td style="font-size:13px"><?php echo convert_date($qbill['recpt_date']);?></td>
					<td style="font-size:13px"><?php echo $qsupplier['name'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qbill['net_amt'];?></td>
					<td align="right" style="font-size:13px"><?php echo $quser['name'];?></td>
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
	<td colspan="7" style="text-align:right;font-weight:bold;font-size:13px">Total :</td>
	<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($vttl,2);?></td>
	<td>&nbsp;</td>
	
</tr>



</table>  
       
    
  
   </form>
 
 </div>  
</body>
</html>

