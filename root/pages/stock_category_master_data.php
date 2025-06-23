<?php
include("../../includes/connection.php");
include('../../includes/global.function.php');

$type=$_POST["type"];

if($type=="load_category_list")
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
	$category_qry=mysqli_query($link, " SELECT * FROM `stock_category_master` ORDER BY `category_name` ");
	while($category=mysqli_fetch_array($category_qry))
	{
?>
		<tr>
			<td onClick="load_category_detail('<?php echo $category["category_id"]; ?>')" style="cursor:pointer;"><?php echo $n; ?></td>
			<td onClick="load_category_detail('<?php echo $category["category_id"]; ?>')" style="cursor:pointer;"><?php echo $category["category_name"]; ?></td>
			<td>
				<button class="btn btn-mini btn-danger" onClick="delete_category('<?php echo $category["category_id"]; ?>')" <?php echo $dis_del_btn; ?> ><i class="icon-remove"></i></button>
			</td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}

if($type=="load_category_detail")
{
	$category_id=$_POST["category_id"];
	
	$category=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `stock_category_master` WHERE `category_id`='$category_id' "));
	
	echo $category["category_id"]."@#@".$category["category_name"];
}

if($type=="save_category")
{
	$category_id=$_POST["category_id"];
	$category_name=mysqli_real_escape_string($link, $_POST["category_name"]);
	
	if($category_id>0)
	{
		mysqli_query($link, " UPDATE `stock_category_master` SET `category_name`='$category_name' WHERE `category_id`='$category_id' ");
		
		echo "<h5>Updated</h5>";
	}else
	{
		mysqli_query($link, " INSERT INTO `stock_category_master`(`category_name`) VALUES ('$category_name') ");
		
		echo "<h5>Saved</h5>";
	}
}

if($type=="delete_category")
{
	$category_id=$_POST["category_id"];
	
	mysqli_query($link, " DELETE FROM `stock_category_master` WHERE `category_id`='$category_id' ");
	
	echo "<h5>Deleted</h5>";
		
}
?>
