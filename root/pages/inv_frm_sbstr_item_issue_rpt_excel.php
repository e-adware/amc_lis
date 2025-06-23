<?php

$filename ="item_issue.xls";
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

$ord=base64_decode($_GET['orderno']);

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

$qitmissueto=mysqli_fetch_array(mysqli_query($link,"select * from inv_substore_issue_master where issue_no='$ord'"));
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));
$splr=mysqli_fetch_array(mysqli_query($link,"select * from inv_sub_store where  substore_id='$qitmissueto[substore_id]'  "));

?>
<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Item Issue Report</u></h5></center>
			</div>

<table>

<tr><td colspan="4" style="font-weight:bold;font-size:13px">Issue Id: <?php echo $ord;?></td></tr>
<tr><td colspan="4" style="font-weight:bold;font-size:13px">Department  : <?php echo $splr['substore_name'];?></td></tr>
<tr><td colspan="4" style="font-weight:bold;font-size:13px">Issue To  : <?php echo $qitmissueto['issue_to'];?></td></tr>
<tr><td colspan="4" style="font-weight:bold;font-size:13px">Issue No  : <?php echo $qitmissueto['issue_num'];?></td></tr>
<tr><td colspan="4" style="font-weight:bold;font-size:13px">Issue Date: <?php echo convert_date($qitmissueto['date']);?></td></tr>
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
				<!--<td style="font-weight:bold;font-size:13px">Item Code</td>-->
				<td style="font-weight:bold;font-size:13px">Item Name</td>
				<td style="font-weight:bold;font-size:13px">Batch No</td>
				<td align="right" style="font-weight:bold;font-size:13px">Issued Qnty.</td>
				
				<!--<td align="right" style="font-weight:bold;font-size:13px">Rate</td>
				<td align="right" style="font-weight:bold;font-size:13px">Amount</td>
				<td align="right" style="font-weight:bold;font-size:13px">GST Amount</td>
				<td align="right" style="font-weight:bold;font-size:13px">Total</td>-->
				
           </tr>
             <?php 
              $i=1;
              $total_amount=0;
			  $qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name,b.gst from inv_substore_issue_details a,item_master b WHERE a.issue_no='$ord' and a.item_id=b.item_id  ORDER BY b.item_name");  
			 while($qrslctitm1=mysqli_fetch_array($qrslctitm))
			 {
			 ?>
             <tr class="line">
					<td style="font-size:13px"><?php echo $i;?></td>
					<!--<td style="font-size:13px"><?php echo $qrslctitm1['item_id'];?></td>-->
					<td style="font-size:13px"><?php echo $qrslctitm1['item_name'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['batch_no'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['issue_qnt'];?></td>
					<!--<td align="right" style="font-size:13px"><?php echo $qrslctitm1['rate'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['amount'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['gst_amount'];?></td>
					<td align="right" style="font-size:13px"><?php echo number_format($qrslctitm1['amount']+$qrslctitm1['gst_amount'],2);?></td>-->
             </tr>  
               
                  <?php
                  $total_amount+=$qrslctitm1['amount']+$qrslctitm1['gst_amount'];
			$i++ ;}
			  ?>
             
          
          
               
              
	<tr class="bline">         
	    <td colspan="4">&nbsp;</td>
	</tr>

<!--
    <tr class="bline">         
	    <td colspan="7" style="text-align:right;font-size:12px;font-weight:bold">Total</td>
	    <td style="text-align:right;font-size:12px;font-weight:bold"><?php echo number_format($total_amount,2);?></td>
	</tr>
-->

</table>  
       
    
  
   </form>
 
 </div>  
</body>
</html>

