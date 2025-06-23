<?php
include('../../includes/connection.php');

$filename ="shortage_stock.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
?>
<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="5" style="text-align:center;"><h4>Available Stock</h4></th>
	</tr>
	<tr>
		<th>Sl No</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><th>Stock</th>
	</tr>
<?php
$i=1;
$q=mysqli_query($link,"SELECT * FROM `ph_stock_master` WHERE `quantity`<10");
while($r=mysqli_fetch_array($q))
{
	$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
?>
	<tr>
		<td><?php echo $i;?></td>
		<td><?php echo $r['item_code'];?></td>
		<td><?php echo $itm['item_name'];?></td>
		<td><?php echo $r['batch_no'];?></td>
		<td><?php echo $r['quantity'];?></td>
	</tr>
<?php
$i++;
}
?>
</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
