<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="load_all_item")
{
	$category_id=$_POST['category_id'];
	$sub_category_id=$_POST['sub_category_id'];
	$item_type_id=$_POST['item_type_id'];
	$manufacturer_id=$_POST['manufacturer_id'];
	$user=$_POST['user'];
	$level=$_POST['level'];
	$val=$_POST['val'];
	
	$btn_hide="style='display:none;'";
	$btn_dis="disabled";
	if($level==1 || $user==143 || $user==136)
	{
		$btn_dis="";
	}
	
	if($user==101 || $user==102 || $level==1)
	{
		$btn_hide="";
	}
	
	if(strlen($val)>0)
	{
		$qry=" SELECT * FROM `item_master` WHERE `item_id`>0 AND `item_name`!='' AND `item_name` LIKE '%$val%'";
	}else
	{
		$qry=" SELECT * FROM `item_master` WHERE `item_id`>0 AND `item_name`!=''";
	}

	if($category_id>0)
	{
		$qry.=" AND `category_id`='$category_id' ";
	}
	if($sub_category_id>0)
	{
		$qry.=" AND `sub_category_id`='$sub_category_id' ";
	}
	if($item_type_id>0)
	{
		$qry.=" AND `item_type_id`='$item_type_id' ";
	}
	if($manufacturer_id>0)
	{
		$qry.=" AND `manufacturer_id`='$manufacturer_id' ";
	}

	//$qry.=" ORDER BY `item_name` limit 0,50 ";
	$qry.=" ORDER BY `item_name` ";

	//echo $qry;

?>
	<table class="table table-bordered data-table">
		<thead style="background: #ddd;">
			<tr>
				<th>#</th>
				<th>Item Name</th>
				<th>Short Name</th>
				<th>Category</th>
				<!--<th>Sub Category</th>-->
				<th>Item Type</th>
				<th>HSN Code</th>
				<th><i class="icon-cogs"></i></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$i=1;
			$item_qry=mysqli_query($link, $qry);
			while($item=mysqli_fetch_array($item_qry))
			{
				$category=mysqli_fetch_array(mysqli_query($link, " SELECT `category_name` FROM `stock_category_master` WHERE `category_id`='$item[category_id]' "));
				
				$sub_category=mysqli_fetch_array(mysqli_query($link, " SELECT `sub_category_name` FROM `stock_sub_category_master` WHERE `sub_category_id`='$item[sub_category_id]' "));
				
				$item_type=mysqli_fetch_array(mysqli_query($link, " SELECT `item_type_name` FROM `item_type_master` WHERE `item_type_id`='$item[item_type_id]' "));
				
				//$manufacturer=mysqli_fetch_array(mysqli_query($link, " SELECT `manufacturer_name` FROM `manufacturer_company` WHERE `manufacturer_id`='$item[manufacturer_id]' "));
				
		?>
			<tr class="gradeX" id="test<?php echo $i ?>">
				<td id="item_id<?php echo $i ?>">
					<?php echo $i; ?>
				</td>
				<td>
					<?php echo $item["item_name"]; ?>
				</td>
				<td>
					<?php echo $item["short_name"]; ?>
				</td>
				<td><?php echo $category['category_name']; ?></td>
				<!--<td>
					<span><?php echo $sub_category['sub_category_name']; ?></span>
				</td>-->
				<td>
					<span><?php echo $item_type['item_type_name']; ?></span>
				</td>
				<td>
					<span><?php echo $item['hsn_code']; ?></span>
				</td>
				<td>
					<button class="btn btn-mini btn-success" onClick="load_item_info('<?php echo $item["item_id"]; ?>')" <?php echo $btn_dis; ?>><i class="icon-edit"></i></button>
					<!--<button class="btn btn-mini btn-danger" onClick="delete_item('<?php echo $item["item_id"]; ?>')" <?php echo $btn_hide; ?>><i class="icon-remove"></i></button>-->
				</td>
			</tr>
		<?php
				$i++;
			}
		?>
		</tbody>
	</table>
<?php
}

if($_POST["type"]=="load_item")
{
	$item_id=$_POST["item_id"];
	if($item_id>0)
	{
		$btn_name="Update";
	}else
	{
		$btn_name="Save";
	}
	
	$item_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `item_master` WHERE `item_id`='$item_id' "));
	
?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Item Name</th>
			<td>
				<input type="text" id="item_name" onkeyup="caps_it(this.id,this.value)" value="<?php echo $item_info["item_name"]; ?>">
			</td>
			<th>Short Name</th>
			<td>
				<input type="text" id="short_name" value="<?php echo $item_info["short_name"]; ?>">
			</td>
			<th>Generic Name</th>
<!--
			<td>
				<input type="text" id="generic_name" value="<?php echo $item_info["generic_name"]; ?>">
			</td>
-->
           <td>
				<select id="generic_name" >
					<option value="0">Select Generic</option>
					<?php
						$stock_generic_qry=mysqli_query($link, " SELECT * FROM `generic_master` ORDER BY `generic_name` ");
						while($stock_generic=mysqli_fetch_array($stock_generic_qry))
						{
							if($item_info["generic_name"]==$stock_generic["generic_name"]){ $stock_generic_sel="selected"; }else{ $stock_generic_sel=""; }
							echo "<option value='$stock_generic[generic_name]' $stock_generic_sel>$stock_generic[generic_name]</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Category</th>
			<td>
				<select id="category_id_modal" onchange="category_change(this.value)">
					<option value="0">Select Category</option>
					<?php
						$stock_category_qry=mysqli_query($link, " SELECT * FROM `stock_category_master` ORDER BY `category_name` ");
						while($stock_category=mysqli_fetch_array($stock_category_qry))
						{
							if($item_info["category_id"]==$stock_category["category_id"]){ $stock_category_sel="selected"; }else{ $stock_category_sel=""; }
							echo "<option value='$stock_category[category_id]' $stock_category_sel>$stock_category[category_name]</option>";
						}
					?>
				</select>
			</td>
			<th>Sub Category</th>
			<td>
				<select id="sub_category_id_modal">
					<option value="0">Select Sub Category</option>
					<?php
						$stock_sub_category_qry=mysqli_query($link, " SELECT * FROM `stock_sub_category_master` WHERE `category_id`='$item_info[category_id]' ORDER BY `sub_category_name` ");
						while($stock_sub_category=mysqli_fetch_array($stock_sub_category_qry))
						{
							if($item_info["sub_category_id"]==$stock_sub_category["sub_category_id"]){ $stock_sub_category_sel="selected"; }else{ $stock_sub_category_sel=""; }
							echo "<option value='$stock_sub_category[sub_category_id]' $stock_sub_category_sel>$stock_sub_category[sub_category_name]</option>";
						}
					?>
				</select>
			</td>
			<th>Product Type</th>
			<td>
				<select id="item_type_id_modal">
					<option value="0">Select Product Type</option>
					<?php
						$item_type_qry=mysqli_query($link, " SELECT * FROM `item_type_master` ORDER BY `item_type_name` ");
						while($item_type=mysqli_fetch_array($item_type_qry))
						{
							if($item_info["item_type_id"]==$item_type["item_type_id"]){ $item_type_sel="selected"; }else{ $item_type_sel=""; }
							echo "<option value='$item_type[item_type_id]' $item_type_sel>$item_type[item_type_name]</option>";								
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Manufacturer</th>
			<td>
				<select id="manufacturer_id_modal">
					<option value="0">Select Manufacturer</option>
					<?php
						$manufacturer_qry=mysqli_query($link, " SELECT * FROM `manufacturer_company` ORDER BY `manufacturer_name` ");
						while($manufacturer=mysqli_fetch_array($manufacturer_qry))
						{
							if($item_info["manufacturer_id"]==$manufacturer["manufacturer_id"]){ $manufacturer_sel="selected"; }else{ $manufacturer_sel=""; }
							echo "<option value='$manufacturer[manufacturer_id]' $manufacturer_sel >$manufacturer[manufacturer_name]</option>";								
						}
					?>
				</select>
			</td>
			<th>Pack Quantity</th>
			<td>
				<input type="text" id="strip_quantity" onkeyup="chk_num(this.id,this.value)" value="<?php echo $item_info["strip_quantity"]; ?>">
			</td>
			<th>GST %</th>
			<td>
				<!--<input type="text" id="gst" value="<?php echo $item_info["gst"]; ?>">-->
				<select id="gst" class="">
					<option value="">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `gst_percent_master`");
					while($r=mysqli_fetch_assoc($q))
					{
					?>
					<option value="<?php echo $r['gst_per'];?>" <?php if($item_info["gst"]==$r['gst_per']){echo "selected='selected'";} ?>><?php echo $r['gst_per'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr style="display:none;">
			<th>Item Strength</th>
			<td>
				<input type="text" id="strength" value="<?php echo $item_info["strength"]; ?>">
			</td>
			<th>Item MRP</th>
			<td>
				<input type="text" id="mrp" value="<?php echo $item_info["mrp"]; ?>">
			</td>
			<th>Unit</th>
			<td>
				<input type="text" id="unit" value="<?php echo $item_info["unit"]; ?>">
			</td>
		</tr>
		<tr style="display:none;">
			<th>Re-order Quantity</th>
			<td>
				<input type="text" id="re_order" onkeyup="chk_num(this.id,this.value)" value="<?php echo $item_info["re_order"]; ?>">
			</td>
			<th>Critical Stock</th>
			<td>
				<input type="text" id="critical_stock" onkeyup="chk_num(this.id,this.value)" value="<?php echo $item_info["critical_stock"]; ?>">
			</td>
			<th>Specific Type</th>
			<td>
				<select id="specific_type">
					<option value="0" <?php if($item_info["specific_type"]==0){ echo "selected"; } ?> >No</option>
					<option value="1" <?php if($item_info["specific_type"]==1){ echo "selected"; } ?> >Yes</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>HSN Code</th>
			<td colspan="">
				<input type="text" id="hsn_code" onkeyup="chk_num(this.id,this.value)" value="<?php echo $item_info["hsn_code"]; ?>">
			</td>
			<th>Rack No</th>
			<td>
				<input type="text" id="rack_no" value="<?php echo $item_info["rack_no"]; ?>">
			</td>
			<th>Not Require</th>
			<td colspan="">
				<input type="checkbox" id="item_require" <?php if($item_info["need"]==1){ echo "checked"; } ?> value="1">
			</td>
		</tr>
		<tr>
			<th>No of Test</th>
			<td>
				<input type="text" id="no_of_test" onkeyup="chk_num(this.id,this.value)" value="<?php echo $item_info["no_of_test"]; ?>" />
			</td>
			<td colspan="4"></td>
		</tr>
		<tr>
			<td colspan="6">
				<center>
					<input type="button" class="btn btn-info" id="sav" value="<?php echo $btn_name; ?>" onClick="save_item('<?php echo $item_id; ?>')">
					<button type="button" class="btn btn-warning" id="modal_btn_close" data-dismiss="modal">Close</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="load_sub_category")
{
	$category_id=$_POST["category_id"];
	
	if($category_id>0)
	{
		$stock_sub_category_qry=mysqli_query($link, " SELECT * FROM `stock_sub_category_master` WHERE `category_id`='$category_id' ORDER BY `sub_category_name` ");
	}else
	{
		$stock_sub_category_qry=mysqli_query($link, " SELECT * FROM `stock_sub_category_master` ORDER BY `sub_category_name` ");
	}
	
	echo "<option value='0'>Select Sub Category</option>";
	while($stock_sub_category=mysqli_fetch_array($stock_sub_category_qry))
	{
		echo "<option value='$stock_sub_category[sub_category_id]'>$stock_sub_category[sub_category_name]</option>";
	}
}

if($_POST["type"]=="save_item")
{
	$item_id=$_POST["item_id"];
	$item_name=mysqli_real_escape_string($link, $_POST["item_name"]);
	$short_name=mysqli_real_escape_string($link, $_POST["short_name"]);
	$generic_name=mysqli_real_escape_string($link, $_POST["generic_name"]);
	$category_id=$_POST["category_id"];
	$sub_category_id=$_POST["sub_category_id"];
	$item_type_id=$_POST["item_type_id"];
	if(!$item_type_id){$item_type_id=0;}
	$manufacturer_id=$_POST["manufacturer_id"];
	$mrp=mysqli_real_escape_string($link, $_POST["mrp"]);
	if(!$mrp){$mrp=0;}
	$gst=mysqli_real_escape_string($link, $_POST["gst"]);
	if(!$gst){$gst=0;}
	$strength=mysqli_real_escape_string($link, $_POST["strength"]);
	$strip_quantity=mysqli_real_escape_string($link, $_POST["strip_quantity"]);
	if(!$strip_quantity){$strip_quantity=1;}
	$unit=mysqli_real_escape_string($link, $_POST["unit"]);
	$re_order=mysqli_real_escape_string($link, $_POST["re_order"]);
	if(!$re_order){$re_order=0;}
	$no_of_test=mysqli_real_escape_string($link, $_POST["no_of_test"]);
	if(!$no_of_test){$no_of_test=0;}
	$critical_stock=mysqli_real_escape_string($link, $_POST["critical_stock"]);
	if(!$critical_stock){$critical_stock=0;}
	$rack_no=mysqli_real_escape_string($link, $_POST["rack_no"]);
	$specific_type=$_POST["specific_type"];
	if(!$specific_type){$specific_type=0;}
	$hsn_code=mysqli_real_escape_string($link, $_POST["hsn_code"]);
	$item_require=$_POST["item_require"];
	$user=$_POST["user"];
	
	$class="";
	
	if($item_id==0)
	{
		$item_no=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`item_id`) AS `max_id` FROM `item_master` "));
		$item_id=$item_no["max_id"]+1;
		
		mysqli_query($link, " INSERT INTO `item_master`(`item_id`, `short_name`, `item_name`, `hsn_code`, `category_id`, `sub_category_id`, `item_type_id`, `re_order`, `no_of_test`, `critical_stock`, `generic_name`, `rack_no`, `manufacturer_id`, `mrp`, `gst`, `strength`, `strip_quantity`, `unit`, `specific_type`, `class`, `need`) VALUES ('$item_id','$short_name','$item_name','$hsn_code','$category_id','$sub_category_id','$item_type_id','$re_order','$no_of_test','$critical_stock','$generic_name','$rack_no','$manufacturer_id','$mrp','$gst','$strength','$strip_quantity','$unit','$specific_type','$class','$item_require') ");
		
		mysqli_query($link,"INSERT INTO `item_master_changes`(`item_id`, `old_name`, `new_name`, `process`, `date`, `time`, `user`) VALUES ('$item_id','','$item_name','NEW ENTRY','$date','$time','$user')");
		
		echo "<h5>Updated</h5>";
		
	}else
	{
		$old=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$item_id'"));
		
		mysqli_query($link, " UPDATE `item_master` SET `short_name`='$short_name',`item_name`='$item_name',`hsn_code`='$hsn_code',`category_id`='$category_id',`sub_category_id`='$sub_category_id',`item_type_id`='$item_type_id',`re_order`='$re_order',`no_of_test`='$no_of_test',`critical_stock`='$critical_stock',`generic_name`='$generic_name',`rack_no`='$rack_no',`manufacturer_id`='$manufacturer_id',`mrp`='$mrp',`gst`='$gst',`strength`='$strength',`strip_quantity`='$strip_quantity',`unit`='$unit',`specific_type`='$specific_type',`class`='$class',`need`='$item_require' WHERE `item_id`='$item_id' ");
		
		mysqli_query($link,"INSERT INTO `item_master_changes`(`item_id`, `old_name`, `new_name`, `process`, `date`, `time`, `user`) VALUES ('$item_id','$old[item_name]','$item_name','UPDATE','$date','$time','$user')");
		
		echo "<h5>Saved</h5>";
	}
}

if($_POST["type"]=="delete_item")
{
	$item_id=$_POST["item_id"];
	
	mysqli_query($link, " DELETE FROM `item_master` WHERE `item_id`='$item_id' ");
}
?>
