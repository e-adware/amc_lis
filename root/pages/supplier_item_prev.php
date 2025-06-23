<?php
include('../../includes/connection.php');
$supp=base64_decode($_GET['supplier']);
$s=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$supp'"));
?>
<link rel="stylesheet" href="../../css/bootstrap.min.css" type="text/css" />
<table class="table table-condensed table-bordered">
	<tr>
		<td colspan="3" style="text-align:center;font-weight:bold;">Supplier Name: <?php echo $s['name']; ?></td>
	</tr>
	<tr>
		<th>#</th><th width="80%">Item Name</th><th>Rate</th>
	</tr>
	<?php
	$n=1;
	$qr=mysqli_query($link,"SELECT * FROM `inv_supplier_rate` WHERE `supplier_id`='$supp'");
	while($r=mysqli_fetch_array($qr))
	{
		$it=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_indent_master` WHERE `id`='$r[indent_id]'"));
	?>
	<tr>
		<td><?php echo $n;?></td>
		<td><?php echo $it['name'];?></td>
		<td><?php echo $r['rate'];?></td>
	</tr>
	<?php
	$n++;
	}
	?>
</table>
