<?php
include("../../includes/connection.php");
include('../../includes/global.function.php');

$type=$_POST["type"];

if($type=="load_generic_list")
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
	$category_qry=mysqli_query($link, " SELECT * FROM `generic_master` ORDER BY `generic_name` ");
	while($category=mysqli_fetch_array($category_qry))
	{
?>
		<tr>
			<td onClick="load_category_detail('<?php echo $category["generic_id"]; ?>')" style="cursor:pointer;"><?php echo $n; ?></td>
			<td onClick="load_category_detail('<?php echo $category["generic_id"]; ?>')" style="cursor:pointer;"><?php echo $category["generic_name"]; ?></td>
			<td>
				<button class="btn btn-mini btn-danger" onClick="delete_category('<?php echo $category["generic_id"]; ?>')" <?php echo $dis_del_btn; ?> ><i class="icon-remove"></i></button>
			</td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}

if($type=="load_item_generic_detail")
{
	$generic_id=$_POST["generic_id"];
	
	$category=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `generic_master` WHERE `generic_id`='$generic_id' "));
	
	echo $category["generic_id"]."@#@".$category["generic_name"];
}

if($type=="save_generic_name")
{
	$generic_id=$_POST["generic_id"];
	$generic_name=mysqli_real_escape_string($link, $_POST["generic_name"]);
	
	if($generic_id>0)
	{
		mysqli_query($link, " UPDATE `generic_master` SET `generic_name`='$generic_name' WHERE `generic_id`='$generic_id' ");
		
		echo "<h5>Updated</h5>";
	}else
	{
		mysqli_query($link, " INSERT INTO `generic_master`(`generic_name`) VALUES ('$generic_name') ");
		
		echo "<h5>Saved</h5>";
	}
}

if($type=="delete_generic_name")
{
	$generic_id=$_POST["generic_id"];
	
	mysqli_query($link, " DELETE FROM `generic_master` WHERE `generic_id`='$generic_id' ");
	
	echo "<h5>Deleted</h5>";
		
}
?>
