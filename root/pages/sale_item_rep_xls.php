<?php
include('../../includes/connection.php');

$filename ="sale_item_report.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
?>
<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="7" style="text-align:center;"><h4>Sale Item(s) Report from <?php echo $fdate;?> to <?php echo $tdate;?></h4></th>
	</tr>
	<tr>
		    <th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Sale</th>
			<th>Pharmacy Stock</th>
			<th>Central Stock</th>
			<th>Current Stock</th>
	</tr>
	<?php
	$n=1;
	$q=mysqli_query($link,"SELECT DISTINCT a.`item_code`,b.item_name  FROM `ph_sell_details` a,item_master b WHERE a.entry_date  BETWEEN '$fdate' AND '$tdate' and a.item_code=b.item_id order by b.item_name");
	while($r=mysqli_fetch_array($q))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
		$bch=mysqli_fetch_array(mysqli_query($link,"SELECT `batch_no` FROM `ph_stock_process` WHERE `item_code`='$r[item_code]'"));
		$add=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(added),0)as adds FROM `ph_stock_process` WHERE `item_code`='$r[item_code]'"));
		$sell=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(	sale_qnt),0)as sells,ifnull(sum(free_qnt),0)as maxfree FROM `ph_sell_details` WHERE `item_code`='$r[item_code]' and entry_date between '$fdate' AND '$tdate'"));
		
		$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(`quantity`),0) as maxph FROM `ph_stock_master` WHERE `item_code`='$r[item_code]' and substore_id='1' and quantity>0 "));

		$mainstk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(`closing_stock`),0) as maxcntrl FROM `inv_maincurrent_stock` WHERE `item_id`='$r[item_code]' and  closing_stock>0 "));

		$vttlstk=0;
		$vsalqnt=$sell['sells']+$sell['maxfree'];
		$vttlstk=$stk['maxph']+$mainstk['maxcntrl'];
	?>
	<tr>
		<td><?php echo $n;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $vsalqnt;?></td>
			<td><?php echo $stk['maxph'];?></td>
			<td><?php echo $mainstk['maxcntrl'];?></td>
			<td><?php echo $vttlstk;?></td>
	</tr>
	<?php
	$n++;
	}
	?>
</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
