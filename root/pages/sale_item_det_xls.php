<?php
include('../../includes/connection.php');

$filename ="sale_item_details.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
?>
<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="9" style="text-align:center;"><h4>Sale Item(s) details from <?php echo $fdate;?> to <?php echo $tdate;?></h4></th>
	</tr>
	<tr>
		<th>Sl No</th>
		<th>Item Code</th>
		<th>Item Name</th>
		<th>Batch No</th>
		<th>Date</th>
		<th>Open</th>
		<th>Added</th>
		<th>Sale</th>
		<th>Close</th>
	</tr>
<?php
$n=1;
$qry=mysqli_query($link,"SELECT DISTINCT `item_code` FROM `ph_stock_process` WHERE `sell`!='0' AND `date` BETWEEN '$fdate' AND '$tdate'");
while($res=mysqli_fetch_array($qry))
{
	$q=mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `item_code`='$res[item_code]' AND `date` BETWEEN '$fdate' AND '$tdate'");
	$num=mysqli_num_rows($q);
	while($r=mysqli_fetch_array($q))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
	?>
	<tr>
		<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$r['item_code']."</td><td rowspan='".$num."'>".$itm['item_name']."</td><td rowspan='".$num."'>".$r['batch_no']."</td>";}?>
		<td><?php echo $r['date'];?></td>
		<td><?php echo $r['s_available'];?></td>
		<td><?php echo $r['added'];?></td>
		<td><?php echo $r['sell'];?></td>
		<td><?php echo $r['s_remain'];?></td>
	</tr>
	<?php
	$num=0;
	}
	$n++;
}
?>
</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
