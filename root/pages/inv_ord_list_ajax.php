<?php
include'../../includes/connection.php';

//$day=7;
$type=$_POST['type'];

if($type==1)
{
	$day=$_POST['days'];
	$name=mysqli_real_escape_string($link,$_POST['name']);
	$date2=date("Y-m-d");
	$date1=date("Y-m-d", strtotime("-$day days"));

	$ids="";
	//~ if($name)
	//~ {
		//~ $q=mysqli_query($link,"SELECT DISTINCT a.`item_id` FROM `inv_maincurrent_stock` a, `item_master` b WHERE a.`item_id`=b.`item_id` AND b.`item_name` like '$name%' AND a.`closing_stock`='0' ORDER BY b.`item_name`");
	//~ }
	//~ else
	//~ {
		//~ $q=mysqli_query($link,"SELECT DISTINCT a.`item_id` FROM `inv_maincurrent_stock` a, `item_master` b WHERE a.`item_id`=b.`item_id` AND a.`closing_stock`='0' ORDER BY b.`item_name`");
	//~ }
	//~ while($r=mysqli_fetch_assoc($q))
	//~ {
		//~ $v=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_id` FROM `inv_maincurrent_stock` WHERE `item_id`='$r[item_id]' AND `closing_stock`>'0'"));
		//~ if(!$v)
		//~ {
			//~ if($ids)
			//~ {
				//~ $ids.=",".$r['item_id'];
			//~ }
			//~ else
			//~ {
				//~ $ids=$r['item_id'];
			//~ }
		//~ }
	//~ }

	?>
	<table class="table table-condensed" style="font-size:13px !important;">
		<thead class="table_header_fix">
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th><?php echo $day;?> days sale qnt</th>
			<th>per day sale qnt</th>
			<th>14 days expected stock</th>
			<th>Current Stock</th>
			<th class="noprint"></th>
		</tr>
		</thead>
	<?php
	$j=1;
	$idd=explode(",",$ids);
	foreach($idd as $id)
	{
		if($id)
		{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`strip_quantity` FROM `item_master` WHERE `item_id`='$id'"));
		$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT SUM(`closing_stock`) AS `quantity` FROM `inv_maincurrent_stock` WHERE `item_id`='$id'"));
		$avg_sale=round($r['avgs']*14);
		?>
		<tr class="tr">
			<td><?php echo $j;?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo round($r['sale_qnt']/$itm['strip_quantity']);?></td>
			<td><?php echo number_format(($r['avgs']/$itm['strip_quantity']),2);?></td>
			<td><?php echo round($avg_sale/$itm['strip_quantity']);?></td>
			<td><?php echo round($stk['quantity']/$itm['strip_quantity']);?></td>
			<td style="text-align:center;" class="noprint"><b class="icon-remove icon-large" style="cursor:pointer;color:#F11212;" onclick="rem_list(this)"><img src="../../images/Delete.png" style="width:20px;" /></b></td>
		</tr>
		<?php
		$j++;
		}
	}
	?>
		<tr>
			<td colspan="7"></td>
		</tr>
	<?php
	if($name)
	{
		if($ids)
		{
			$qry="SELECT DISTINCT a.`item_id`, SUM(a.`issue_qnt`) AS `sale_qnt`, (SUM(a.`issue_qnt`)/$day) AS `avgs` FROM `ph_stock_transfer_details` a, `item_master` b WHERE a.`item_id`=b.`item_id` AND b.`re_order`='0' AND a.`item_id` NOT IN ($ids) AND b.`item_name` like '$name%' AND a.`date` BETWEEN '$date1' AND '$date2' GROUP BY a.`item_id` HAVING SUM(a.`issue_qnt`)>0 ORDER BY b.`item_name`";
		}
		else
		{
			$qry="SELECT DISTINCT a.`item_id`, SUM(a.`issue_qnt`) AS `sale_qnt`, (SUM(a.`issue_qnt`)/$day) AS `avgs` FROM `ph_stock_transfer_details` a, `item_master` b WHERE a.`item_id`=b.`item_id` AND b.`re_order`='0' AND b.`item_name` like '$name%' AND a.`date` BETWEEN '$date1' AND '$date2' GROUP BY a.`item_id` HAVING SUM(a.`issue_qnt`)>0 ORDER BY b.`item_name`";
		}
	}
	else
	{
		if($ids)
		{
			$qry="SELECT DISTINCT a.`item_id`, SUM(a.`issue_qnt`) AS `sale_qnt`, (SUM(a.`issue_qnt`)/$day) AS `avgs` FROM `ph_stock_transfer_details` a, `item_master` b WHERE a.`item_id`=b.`item_id` AND b.`re_order`='0' AND a.`item_id` NOT IN ($ids) AND a.`date` BETWEEN '$date1' AND '$date2' GROUP BY a.`item_id` HAVING SUM(a.`issue_qnt`)>0 ORDER BY b.`item_name`";
		}
		else
		{
			$qry="SELECT DISTINCT a.`item_id`, SUM(a.`issue_qnt`) AS `sale_qnt`, (SUM(a.`issue_qnt`)/$day) AS `avgs` FROM `ph_stock_transfer_details` a, `item_master` b WHERE a.`item_id`=b.`item_id` AND b.`re_order`='0' AND a.`date` BETWEEN '$date1' AND '$date2' GROUP BY a.`item_id` HAVING SUM(a.`issue_qnt`)>0 ORDER BY b.`item_name`";
		}
	}
	//echo $qry;
	$q=mysqli_query($link,$qry);
	//$j=1;
	while($r=mysqli_fetch_assoc($q))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`strip_quantity` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT SUM(`closing_stock`) AS `quantity` FROM `inv_maincurrent_stock` WHERE `item_id`='$r[item_id]'"));
		$avg_sale=round($r['avgs']*14);
		if($stk['quantity']<=$avg_sale && $stk['quantity']>=0)
		{
			//echo "<br/>".$j.". ".$r['item_code'];
			?>
			<tr class="tr">
				<td><?php echo $j;?></td>
				<td><?php echo $itm['item_name'];?></td>
				<td><?php echo round($r['sale_qnt']/$itm['strip_quantity']);?></td>
				<td><?php echo number_format(($r['avgs']/$itm['strip_quantity']),2);?></td>
				<td><?php echo round($avg_sale/$itm['strip_quantity']);?></td>
				<td><?php echo round($stk['quantity']/$itm['strip_quantity']);?></td>
				<td style="text-align:center;" class="noprint"><b class="icon-remove icon-large" style="cursor:pointer;color:#F11212;" onclick="rem_list(this,'<?php echo $r['item_id'];?>')"><img src="../../images/Delete.png" style="width:20px;" /></b></td>
			</tr>
			<?php
			$j++;
		}
	}
	?>
	</table>
	<?php
}

if($type==2)
{
	$itm=$_POST['itm'];
	mysqli_query($link,"UPDATE `item_master` SET `re_order`='1' WHERE `item_id`='$itm'");
}
?>
