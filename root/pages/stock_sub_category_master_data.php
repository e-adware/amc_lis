<?php
include("../../includes/connection.php");
include('../../includes/global.function.php');

$type=$_POST["type"];

if($type=="load_sub_category_list")
{
	$user=$_POST["user"];
	$lavel_id=$_POST["lavel_id"];
	$category_id=$_POST["category_id"];
	
	if($category_id>0)
	{
		$qry="SELECT * FROM `stock_sub_category_master` WHERE `category_id`='$category_id' ORDER BY `sub_category_name` ";
	}else
	{
		$qry="SELECT * FROM `stock_sub_category_master` ORDER BY `sub_category_name` ";
	}
	
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
	$sub_category_qry=mysqli_query($link, $qry);
	while($sub_category=mysqli_fetch_array($sub_category_qry))
	{
?>
		<tr>
			<td onClick="load_sub_category_detail('<?php echo $sub_category["sub_category_id"]; ?>')" style="cursor:pointer;"><?php echo $n; ?></td>
			<td onClick="load_sub_category_detail('<?php echo $sub_category["sub_category_id"]; ?>')" style="cursor:pointer;"><?php echo $sub_category["sub_category_name"]; ?></td>
			<td>
				<button class="btn btn-mini btn-danger" onClick="delete_sub_category('<?php echo $sub_category["sub_category_id"]; ?>')" <?php echo $dis_del_btn; ?> ><i class="icon-remove"></i></button>
			</td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}

if($type=="load_sub_category_detail")
{
	$sub_category_id=$_POST["sub_category_id"];
	
	$sub_category=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `stock_sub_category_master` WHERE `sub_category_id`='$sub_category_id' "));
	
	echo $sub_category["category_id"]."@#@".$sub_category_id."@#@".$sub_category["sub_category_name"];
}

if($type=="save_sub_category")
{
	$category_id=$_POST["category_id"];
	$sub_category_id=$_POST["sub_category_id"];
	$sub_category_name=mysqli_real_escape_string($link, $_POST["sub_category_name"]);
	
	if($sub_category_id>0)
	{
		mysqli_query($link, " UPDATE `stock_sub_category_master` SET `sub_category_name`='$sub_category_name' WHERE `category_id`='$category_id' AND `sub_category_id`='$sub_category_id' ");
		
		echo "<h5>Updated</h5>";
	}else
	{
		mysqli_query($link, " INSERT INTO `stock_sub_category_master`(`category_id`, `sub_category_name`) VALUES ('$category_id','$sub_category_name') ");
		
		echo "<h5>Saved</h5>";
	}
}

if($type=="delete_sub_category")
{
	$sub_category_id=$_POST["sub_category_id"];
	
	mysqli_query($link, " DELETE FROM `stock_sub_category_master` WHERE `sub_category_id`='$sub_category_id' ");
	
	echo "<h5>Deleted</h5>";
		
}
?>
