<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$date=date("Y-m-d");
$time=date("H:i:s");


if($_POST["type"]=="load_all_types")
{
	$val=$_POST['val'];
	if($val)
	{
		$q="SELECT * FROM `doctor_specialist_list` WHERE `name` like '%$val%'";
	}
	else
	{
		$q="SELECT * FROM `doctor_specialist_list` order by `name`";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
	<thead class="table_header_fix">
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Prefix</th>
			<!--<th>Delete</th>-->
		</tr>
	</thead>
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['speciality_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['speciality_id'];?></td>
			<td><?php echo $qrpdct1['name'];?></td>
			<td><?php echo $qrpdct1['prefix'];?></td>
			<!--<td><span onclick="delete_data('<?php echo $qrpdct1['speciality_id'];?>')"> <img height="15" width="15" src="../images/delete.ico"/></a></td>-->
		</tr>
<?php	
		$i++;
	}
?>
</table>
<?php
}

if($_POST["type"]=="load_single_type")
{
	$tid=$_POST['doid1'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select * from doctor_specialist_list where speciality_id='$tid' "));
	$val=$tid.'#'.$qrm['name'].'#'.$qrm['prefix'];
	echo $val;
}

if($_POST["type"]=="save_type")
{
	$speciality_id=$_POST['speciality_id'];
	$name=mysqli_real_escape_string($link, $_POST['dept_name']);
	$prefix=mysqli_real_escape_string($link, $_POST['prefix']);
	
	if($speciality_id==0)
	{
		mysqli_query($link, " INSERT INTO `doctor_specialist_list`(`name`, `prefix`, `status`) VALUES ('$name','$prefix','0') ");
	}else
	{
		mysqli_query($link, " UPDATE `doctor_specialist_list` SET `name`='$name',`prefix`='$prefix' WHERE `speciality_id`='$speciality_id' ");
	}
	echo "Saved";
}

?>
