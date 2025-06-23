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
	$category_qry=mysqli_query($link, " SELECT * FROM `manufacturer_company` ORDER BY `manufacturer_name` ");
	while($category=mysqli_fetch_array($category_qry))
	{
?>
		<tr>
			<td onClick="load_category_detail('<?php echo $category["manufacturer_id"]; ?>')" style="cursor:pointer;"><?php echo $n; ?></td>
			<td onClick="load_category_detail('<?php echo $category["manufacturer_id"]; ?>')" style="cursor:pointer;"><?php echo $category["manufacturer_name"]; ?></td>
			<td>
				<button class="btn btn-mini btn-danger" onClick="delete_category('<?php echo $category["manufacturer_id"]; ?>')" <?php echo $dis_del_btn; ?> ><i class="icon-remove"></i></button>
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
	$manufacturer_id=$_POST["manufacturer_id"];
	
	$category=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `manufacturer_company` WHERE `manufacturer_id`='$manufacturer_id' "));
	
	echo $category["manufacturer_id"]."@#@".$category["manufacturer_name"];
}

if($type=="save_manufacturer_name")
{
	$manufacturer_id=$_POST["manufacturer_id"];
	$manufacturer_name=mysqli_real_escape_string($link, $_POST["manufacturer_name"]);
	
	if($manufacturer_id>0)
	{
		mysqli_query($link, " UPDATE `manufacturer_company` SET `manufacturer_name`='$manufacturer_name' WHERE `manufacturer_id`='$manufacturer_id' ");
		
		echo "<h5>Updated</h5>";
	}else
	{
		mysqli_query($link, " INSERT INTO `manufacturer_company`(`manufacturer_name`) VALUES ('$manufacturer_name') ");
		
		echo "<h5>Saved</h5>";
	}
}

if($type=="delete_manufacturer_name")
{
	$manufacturer_id=$_POST["manufacturer_id"];
	
	mysqli_query($link, " DELETE FROM `manufacturer_company` WHERE `manufacturer_id`='$manufacturer_id' ");
	
	echo "<h5>Deleted</h5>";
		
}
?>
