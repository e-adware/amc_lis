<?php
include("../../includes/connection.php");

$type=$_POST['type'];


if($type=="load_cat")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q="SELECT * FROM `category_master` WHERE `cat_name` like '$srch%' ORDER BY `cat_name`";
	}
	else
	{
		$q="SELECT * FROM `category_master` ORDER BY `cat_name`";
	}
	$data=mysqli_query($link, $q);
	?>

	<table class="table table-bordered table-condensed">
		<tr>
			<th width="10%">#</th>
			<th>Category Name</th>
			<th style="text-align:center;color:#ff0000;"><b class="icon-trash icon-large"></b></th>
		</tr>
	<?php
	$i=1;
	while($d=mysqli_fetch_array($data))
	{
		//$rate=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select rate from testmaster_rate where testid='$d[testid]' and centreno='$pat_center'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td class="nm" onclick="cat_det('<?php echo $d['cat_id'];?>')" style="cursor:pointer"><?php echo $d['cat_name'];?></td>
			<td style="cursor:pointer;color:#ff0000;text-align:center;" onclick="del('<?php echo $d['cat_id'];?>')"><b class="icon-remove icon-large"></b></td>
		<?php
		$i++;
	}
	?>
	</table>
	<?php
}

if($type=="save_cat")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	if($id>0)
	{
		mysqli_query($link,"UPDATE `category_master` SET `cat_name`='$name' WHERE `cat_id`='$id'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `category_master`(`cat_name`) VALUES ('$name')");
		echo "Saved";
	}
}

if($type=="edit_cat")
{
	$id=$_POST['id'];
	$val=mysqli_fetch_array(mysqli_query($link,"SELECT `cat_name` FROM `category_master` WHERE `cat_id`='$id'"));
	echo $id."@govin@".$val['cat_name'];
}

if($type=="delete_cat")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `category_master` WHERE `cat_id`='$id'"))
	echo "Deleted";
	else
	echo "Error";
}

if($type=="oo")
{
	
}
?>
