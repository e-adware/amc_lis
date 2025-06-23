<?php
include('../../includes/connection.php');

$filename ="return_item.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];

function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}

?>
<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="6" style="text-align:center;"><h4>Return Item(s) from <?php echo $fdate;?> to <?php echo $tdate;?></h4></th>
	</tr>
	<tr>
		<th>Sl No</th>
		<th>Item Code</th>
		<th>Item Name</th>
		<th>Batch No</th>
		<th>Date</th>
		<td >Return Qnty</td>
		<td style="text-align:right">MRP</td>
		<td style="text-align:right">Amount</td>
		<td >User</td>
	</tr>
<?php
	$qbilno=mysqli_query($link,"select distinct bill_no  FROM ph_item_return_master WHERE `return_date` BETWEEN '$fdate' AND '$tdate'");
	while($qbilno1=mysqli_fetch_array($qbilno))
	{
	?>
	  <tr>
			<td colspan="7" style="font-weight:bold;font-size:13px">Bill No : <?php echo $qbilno1['bill_no'];?></td>
			
		</tr>
		
<?php
$n=1;
$qry=mysqli_query($link,"SELECT  distinct `item_code` FROM `ph_item_return_master` WHERE  bill_no='$qbilno1[bill_no]' and return_date BETWEEN '$fdate' AND '$tdate'");
while($res=mysqli_fetch_array($qry))
{
	$q=mysqli_query($link,"SELECT * FROM `ph_item_return_master` WHERE bill_no='$qbilno1[bill_no]' and `item_code`='$res[item_code]' AND `return_date` BETWEEN '$fdate' AND '$tdate'");
	$num=mysqli_num_rows($q);
	while($r=mysqli_fetch_array($q))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
		$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
		
		$qmrm=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from ph_purchase_receipt_details where item_code='$r[item_code]' and recept_batch='$r[batch_no]'"));
		$vmrp1=0;
		$vmrp1=$r['return_qnt']*$qmrm['recpt_mrp'];
		$vttlcstmrrtrn=$vttlcstmrrtrn+$vmrp1;
	?>
	<tr>
		<?php if($num>0){echo "<td  style='font-size:13px' rowspan='".$num."'>".$n."</td><td style='font-size:13px' rowspan='".$num."'>".$r['item_code']."</td><td style='font-size:13px' rowspan='".$num."'>".$itm['item_name']."</td><td style='font-size:13px' rowspan='".$num."'>".$r['batch_no']."</td>";}?>
		<td style="font-size:13px"><?php echo convert_date($r['return_date']);?></td>
		<td style="font-size:13px"><?php echo $r['return_qnt'];?></td>
		<td align="right" style='font-size:13px;text-align:right'><?php echo $qmrm['recpt_mrp'];?></td>
		<td align="right" style='font-size:13px;text-align:right'><?php echo number_format($vmrp1,2);?></td>
			
		<td style="font-size:13px"><?php echo $quser['name'];?></td>
	</tr>
	<?php
	$num=0;
	}
$n++;
}

?>

<?php
  }?>
  
  <tr>
	  <td colspan="7" style="font-weight:bold;font-size:13px;text-align:right">Total</td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttlcstmrrtrn,2);?></td>
	  <td>&nbsp;</td>
 </tr> 
 
</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
