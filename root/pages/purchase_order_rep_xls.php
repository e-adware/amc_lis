<?php
include('../../includes/connection.php');

$filename ="purchase_order_report.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
$ord=$_GET['orderno'];
$s=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id` IN (SELECT `SuppCode` FROM `ph_purchase_order_master` WHERE `order_no`='$ord')"));
$q=mysqli_query($link,"SELECT * FROM `ph_purchase_order_details` WHERE `order_no`='$ord'");
$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `order_date` FROM `ph_purchase_order_master` WHERE `order_no`='$ord'"));
?>
<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="3">Supplier : <?php echo $s['name'];?></th>
		<th>Date : <?php echo $dt['order_date'];?></th>
	</tr>
	<tr>
		<th>Sl No</th>
		<th>Item Code</th>
		<th width="60%">Item Name</th>
		<th>Quantity</th>
	</tr>
	<?php
	$i=1;
	while($r=mysqli_fetch_array($q))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
	?>
	<tr>
		<td><?php echo $i;?></td>
		<td><?php echo $r['item_code'];?></td>
		<td><?php echo $itm['item_name'];?></td>
		<td><?php echo $r['order_qnt'];?></td>
	</tr>
	<?php
	$i++;
	}
	?>
</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
