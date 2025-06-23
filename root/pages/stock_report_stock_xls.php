<?php
include('../../includes/connection.php');

$filename ="available_stock.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
?>
<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="5" style="text-align:center;"><h4>Available Stock</h4></th>
	</tr>
	<tr>
		<th>#</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><th>Available Stock</th><th>MRP</th><th>MRP Value</th><th>Cost Value</th>
	</tr>
<?php
$i=1;
$q=mysqli_query($link,"SELECT a.*,b.item_name FROM `ph_stock_master` a,ph_item_master b WHERE a.`quantity`>0 and a.item_code=b.item_code order by b.item_name");
while($r=mysqli_fetch_array($q))
{
	$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
	$qrate=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,recept_cost_price FROM `ph_purchase_receipt_details` WHERE `item_code`='$r[item_code]' and  recept_batch='$r[batch_no]'"));
		
		$vmrpvlue=0;
		$vcostvalue=0;
		$vmrpvlue=$qrate['recpt_mrp']*$r['quantity'];
		$vcostvalue=$qrate['recept_cost_price']*$r['quantity'];
		$vttlmrp=$vttlmrp+$vmrpvlue;
		$vttlcstprice=$vttlcstprice+$vcostvalue
?>
	<tr>
		<td><?php echo $i;?></td>
		<td><?php echo $r['item_code'];?></td>
		<td><?php echo $itm['item_name'];?></td>
		<td><?php echo $r['batch_no'];?></td>
		<td><?php echo $r['quantity'];?></td>
		<td><?php echo $qrate['recpt_mrp'];?></td>
		<td><?php echo number_format($vmrpvlue,2);?></td>
		<td><?php echo number_format($vcostvalue,2);?></td>
	</tr>
<?php
$i++;
}
?>
<tr>
		<th colspan="6" style="text-align:right">Total</th>
		<th><?php echo number_format($vttlmrp,2);?></th>
		<th><?php echo number_format($vttlcstprice,2);?></th>
	</tr>
</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
