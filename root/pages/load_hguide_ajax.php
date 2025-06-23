<?php
include("../../includes/connection.php");
$dname=$_POST['val'];
?>
<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
<th>Health Guide ID</th><th>Health Guide Name</th>
<?php

if($dname)
{

	$d=mysqli_query($link, " SELECT * FROM `health_guide` WHERE `hguide_id` like '%$dname%' ORDER BY `name` ");
}
else
{
	$d=mysqli_query($link, " SELECT * FROM `health_guide` WHERE `hguide_id`='HG101' ");
}
$i=1;
while($val=mysqli_fetch_array($d))
{
	?>
	<tr onClick="hguide_load('<?php echo $val['hguide_id'];?>','<?php echo $val['name'];?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
		<td>
			<?php echo $val['hguide_id'];?>
		</td>
		<td>
			<?php echo $val['name'];?>
			<div <?php echo "id=dvhguide".$i;?> style="display:none;">
				<?php echo "#".$val['hguide_id']."#".$val['name'];?>
			</div>
		</td>
	</tr>
    <?php
    $i++;
}
?>
</table>
