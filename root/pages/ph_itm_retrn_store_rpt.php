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
<?php
include('../../includes/connection.php');
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];

$date=date('Y-m-d');
$time=date('h:i:s A');

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-Y', $timestamp);
		return $new_date;
	}
}






?>

<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u> Return to Store Report</u></h5></center>
				
			</div>


<table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
<table>
<table>

<tr><td colspan="5" style="font-weight:bold;font-size:13px">From : <?php echo convert_date($fdate);?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">To &nbsp;&nbsp;&nbsp;&nbsp; : <?php echo convert_date($tdate);?></td></tr>
</table>	
	
<table class="table table-condensed table-bordered">
	
	<tr bgcolor="#EAEAEA" class="bline" >
		<td style="font-weight:bold;font-size:13px">Sl No</td>
		<td style="font-weight:bold;font-size:13px">Item Code</td>
		<td style="font-weight:bold;font-size:13px">Item Name</td>
		<td style="font-weight:bold;font-size:13px">Batch No</td>
		<td style="font-weight:bold;font-size:13px">Date</td>
		<td style="font-weight:bold;font-size:13px">Return Qnty</td>
		<td style="font-weight:bold;font-size:13px;text-align:right">MRP</td>
		<td style="font-weight:bold;font-size:13px;text-align:right">Amount</td>
		<td style="font-weight:bold;font-size:13px">Reason</td>
		<td style="font-weight:bold;font-size:13px">User</td>
	</tr>
	<?php
	$qbilno=mysqli_query($link,"select distinct returnr_no  FROM ph_item_return_store_detail WHERE `date` BETWEEN '$fdate' AND '$tdate'");
	while($qbilno1=mysqli_fetch_array($qbilno))
	{
	?>
	  <tr>
			<td colspan="9" style="font-weight:bold;font-size:11px">Return No : <?php echo $qbilno1['returnr_no'];?></td>
			
		</tr>
		
<?php
$n=1;
$qry=mysqli_query($link,"SELECT  * FROM `ph_item_return_store_detail` WHERE  returnr_no='$qbilno1[returnr_no]' and date BETWEEN '$fdate' AND '$tdate'");
$sum=0;
while($res=mysqli_fetch_array($qry))
{
	
		   $quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res[user]'"));
			$q=mysqli_query($link,"SELECT * FROM `ph_item_return_store_detail` WHERE returnr_no='$qbilno1[returnr_no]' and `item_id`='$res[item_id]' AND `date` BETWEEN '$fdate' AND '$tdate'");
			$num=mysqli_num_rows($q);
			
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$res[item_id]'"));
			
			
			$vmrp1=0;
			$vmrp1=$res['quantity']*$res['recpt_mrp'];
			
			$sum+=$vmrp1;
	?>
	<tr>
		<?php if($num>0){echo "<td  style='font-size:11px' rowspan='".$num."'>".$n."</td><td style='font-size:11px' rowspan='".$num."'>".$res['item_id']."</td><td style='font-size:11px' rowspan='".$num."'>".$itm['item_name']."</td><td style='font-size:11px' rowspan='".$num."'>".$res['batch_no']."</td>";}?>
			<td style="font-size:11px"><?php echo convert_date($res['date']);?></td>
			<td style="font-size:11px"><?php echo $res['quantity'];?></td>
			<td style="font-size:11px"><?php echo $res['recpt_mrp'];?></td>
			<td style='text-align:right;font-size:11px'><?php echo number_format($vmrp1,2);?></td>
			<td style="font-size:11px"><?php echo $res['reason'];?></td>
			<td style="font-size:11px"><?php echo $quser['name'];?></td>
	</tr>
	<?php
	$num=0;
	}
$n++;

$vttlcstmrrtrn+=$sum;
?>
<tr>
		<td colspan="3" style="font-weight:bold;font-size:13px;text-align:right"></td>
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo $pat['discount_perchant'];?></td>
		<td colspan="3" style="font-weight:bold;font-size:11px;text-align:right">Return Amount</td>
		<td style="font-weight:bold;font-size:11px;text-align:right"><?php echo number_format($sum,2);?></td>
		<td></td>
		<td></td>
	</tr>
<?php
  }?>
  
  <tr>
	  <td colspan="7" style="font-weight:bold;font-size:13px;text-align:right">Total</td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttlcstmrrtrn,2);?></td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
 </tr> 
 
</table>
</div>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<script>
//window.print();
</script>
