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
		$q="SELECT * FROM `test_department` WHERE `name` like '%$val%'";
	}
	else
	{
		$q="SELECT * FROM `test_department` order by `name`";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
	<thead class="table_header_fix">
		<tr>
			<th>ID</th>
			<th>Name</th>
			<!--<th>Delete</th>-->
		</tr>
	</thead>
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $i;?></td>
			<td><?php echo $qrpdct1['name'];?></td>
			<!-- <td><?php echo $qrpdct1['prefix'];?></td> -->
			<!--<td><span onclick="delete_data('<?php echo $qrpdct1['id'];?>')"> <img height="15" width="15" src="../images/delete.ico"/></a></td>-->
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
	$tid=$_POST['deptid'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select * from test_department where id='$tid' "));
	$val=$tid.'#'.$qrm['name'] . '#' . $qrm['category_id'];
	echo $val;
}

if($_POST["type"]=="save_type")
{
	$dept_id = $_POST['dept_id'];
	$cat_id=$_POST['cat_id'];
	$name=mysqli_real_escape_string($link, $_POST['dept_name']);
	
	if($dept_id==0)
	{
		mysqli_query($link, " INSERT INTO `test_department`(`category_id`, `name`) VALUES ('$cat_id', '$name') ");
	}else
	{
		mysqli_query($link, " UPDATE `test_department` SET `name`='$name', `category_id` = '$cat_id' WHERE `id`='$dept_id' ");
	}
	echo "Saved"; 	
}

?>
