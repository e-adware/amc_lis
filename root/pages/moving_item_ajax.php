<?php
include("../../includes/connection.php");
$date=date('Y-m-d');
$time=date('H:i:s');
$type=$_POST['type'];

if($_POST["type"]=="moving_item_list")
{
	$item=$_POST['item'];
	if($item)
	{
	$qq=mysqli_query($link,"SELECT `item_code`,`item_name` FROM `ph_item_master` WHERE `item_name` like '$item%' AND `item_code` NOT IN (SELECT `item_code` FROM `ph_moving_item_master_list`) ORDER BY `item_name`");
	$i=1;
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>Add</th>
		</tr>
	<?php
	while($r=mysqli_fetch_array($qq))
	{
	?>
	<tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $r['item_name']; ?></td>
		<td style="text-align:center;"><span style="cursor:pointer;display:block;" onclick="add_item('<?php echo $r['item_code'];?>')"><b class="icon-plus icon-large text-success"></b></span></td>
	</tr>
	<?php
	$i++;
	}
	?>
	</table>
	<?php
	}
}

if($type=="load_qnt")
{
	$move_id=$_POST['move_id'];
	$q=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity` FROM `ph_moving_item_master` WHERE `move_id`='$move_id'"));
	echo $q['quantity'];
}

if($type=="add_moving_item")
{
	$move_id=$_POST['move_id'];
	$item=$_POST['item'];
	mysqli_query($link,"INSERT INTO `ph_moving_item_master_list`(`move_id`, `item_code`) VALUES ('$move_id','$item')");
}

if($type=="load_moving_item")
{
	$move_id=$_POST['move_id'];
	$typ=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ph_moving_item_master` WHERE `move_id`='$move_id' "));
	$q=mysqli_query($link,"SELECT * FROM `ph_moving_item_master_list` WHERE `move_id`='$move_id'");
	?>
	<b>Type : <?php echo $typ['name'];?></b>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Item Code</th><th>Item Name</th><th>Remove</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_array($q))
		{
			$itm=mysqli_fetch_array(mysqli_query($link, " SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]' "));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td style="text-align:center;"><span style="cursor:pointer;display:block;color:#cc0000;" onclick="remove_move_item('<?php echo $r['slno'];?>')"><b class="icon-remove icon-large"></b></span></td>
		</tr>
		<?php
		$j++;
			//echo "<br/>".$r['item_code'];
		}
		?>
	</table>
	<?php
}

if($type=="remove_move_item")
{
	$sl=$_POST['sl'];
	mysqli_query($link,"DELETE FROM `ph_moving_item_master_list` WHERE `slno`='$sl'");
}

if($type=="oo")
{

}
?>
