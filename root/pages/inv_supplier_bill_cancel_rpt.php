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
				<center><h5><u>Supplier Bill Cancelled Report</u></h5></center>
			</div>

<table>

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
				<td style="font-weight:bold;font-size:13px">Receipt No</td>
				<td style="font-weight:bold;font-size:13px">Bill No</td>
				<td style="font-weight:bold;font-size:13px">Bill Date</td>
				<td style="font-weight:bold;font-size:13px">Cancelled Date</td>
				<td style="font-weight:bold;font-size:13px">Cancelled Reason</td>
				<td style="font-weight:bold;font-size:13px">Supplier</td>
				<td align="right" style="font-weight:bold;font-size:13px">Bill Amount</td>
				<td align="right" style="font-weight:bold;font-size:13px">Cancelled by</td>
				<td align="right" style="font-weight:bold;font-size:13px">Time</td>
				
           </tr>
             <?php 
              $i=1;
              $tot=0;
              
              
					if($splrid==0)
					{
					$q=mysqli_query($link,"SELECT * FROM inv_main_bill_cancel_master  WHERE cancel_date between '$fdate' and '$tdate'  order by cancel_date desc");
					}
					else
					{
					$q=mysqli_query($link,"SELECT * FROM inv_main_bill_cancel_master  WHERE cancel_date between '$fdate' and '$tdate' and supp_code='$splrid' order by slno");

					}

				while($r=mysqli_fetch_array($q))
				{
					

					$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supp_code]'"));
					$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[cancel_by_user]'"));
					$vttl=$vttl+$r['net_amt'];
					
				
			 ?>
             <tr class="line">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo $r['receipt_no'];?></td>
					<td style="font-size:13px"><?php echo $r['bill_no'];?></td>
					<td style="font-size:13px"><?php echo convert_date($r['bill_date']);?></td>
					<td style="font-size:13px"><?php echo convert_date($r['cancel_date']);?></td>
					<td style="font-size:13px"><?php echo $r['cancel_reason'];?></td>
					<td style="font-size:13px"><?php echo $qsupplier['name'];?></td>
					<td align="right" style="font-size:13px"><?php echo $r['net_amt'];?></td>
					 <td align="right" style="font-size:13px"><?php echo $quser['name'];?></td>
					<td align="right" style="font-size:13px"><?php echo $r['cancel_time'];?></td>
					
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
	<td>&nbsp;</td>
</tr>



</table>  
       
    
  
   </form>
 
 </div>  
</body>
</html>

