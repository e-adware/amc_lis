<?php
include("../../includes/connection.php");
$dname=$_POST['val'];
?>
<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
<th>Drug Name</th>
<?php


if($dname)
{

	$d=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `item_master` where `item_name` like '$dname%' order by `item_name`");
}
else
{
	$d=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `item_master` order by `item_name`");
}

$i=1;
while($d1=mysqli_fetch_array($d))
{
	?>
	<tr onclick="select_med_post('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['item_type_id'];?>','<?php echo $d1['generic_name'];?>')" style="cursor:pointer" <?php echo "id=medp".$i;?>>
		<td><?php echo $d1['item_name'];?>
			<div <?php echo "id=mdname".$i;?> style="display:none;">
			<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$d1['item_type_id']."#".$d1['generic_name'];?>
			</div>
		</td>
	</tr>
    <?php
    $i++;
}
?>
</table>
