<?php
include("../../includes/connection.php");
include('../../includes/global.function.php');

$type=$_POST["type"];

if($type=="load_item_type_list")
{
	$user=$_POST["user"];
	$lavel_id=$_POST["lavel_id"];
	
	$dis_del_btn="disabled";
	//if($user==101 || $user==102)
	if($lavel_id==1)
	{
		//$dis_del_btn="";
	}
?>
	<table class="table table-condensed table-bordered" id="tblData">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th><i class="icon-trash"></i></th>
		</tr>
<?php
	$n=1;
	$category_qry=mysqli_query($link, " SELECT * FROM `item_type_master` ORDER BY `item_type_name` ");
	while($category=mysqli_fetch_array($category_qry))
	{
?>
		<tr>
			<td onClick="load_category_detail('<?php echo $category["item_type_id"]; ?>')" style="cursor:pointer;"><?php echo $n; ?></td>
			<td onClick="load_category_detail('<?php echo $category["item_type_id"]; ?>')" style="cursor:pointer;"><?php echo $category["item_type_name"]; ?></td>
			<td>
				<button class="btn btn-mini btn-danger" onClick="delete_category('<?php echo $category["item_type_id"]; ?>')" <?php echo $dis_del_btn; ?> ><i class="icon-remove"></i></button>
			</td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}

if($type=="load_item_type_detail")
{
	$item_type_id=$_POST["item_type_id"];
	
	$category=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `item_type_master` WHERE `item_type_id`='$item_type_id' "));
	
	echo $category["item_type_id"]."@#@".$category["item_type_name"];
}

if($type=="save_item_type")
{
	$item_type_id=$_POST["item_type_id"];
	$item_type_name=mysqli_real_escape_string($link, $_POST["item_type_name"]);
	
	if($item_type_id>0)
	{
		mysqli_query($link, " UPDATE `item_type_master` SET `item_type_name`='$item_type_name' WHERE `item_type_id`='$item_type_id' ");
		
		echo "<h5>Updated</h5>";
	}else
	{
		mysqli_query($link, " INSERT INTO `item_type_master`(`item_type_name`) VALUES ('$item_type_name') ");
		
		echo "<h5>Saved</h5>";
	}
}

if($type=="delete_item_type")
{
	$item_type_id=$_POST["item_type_id"];
	
	mysqli_query($link, " DELETE FROM `item_type_master` WHERE `item_type_id`='$item_type_id' ");
	
	echo "<h5>Deleted</h5>";
		
}
?>
