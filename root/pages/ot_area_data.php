<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="ot_area_master")
{
?>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th class="span2">OT Room Name</th>
			<td>
				<input type="text" id="ot_area_name" class="span4" onKeyup="ot_area_name_up(this.value,event)">
			</td>
			<th>OT Type</th>
			<td>
				<select id="ot_type" onKeyup="ot_type_up(this.value,event)">
					<!--<option value="0">Select</option>-->
					<option value="Primary">Primary</option>
					<option value="Major">Major</option>
				</select>
			</td>
			<th style="display:none;">Rate</th>
			<td style="display:none;">
				<input type="text" id="ot_rate" class="span2" value="0" onKeyup="ot_rate_up(this.value,event)">
			</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="5">
				<button class="btn btn-info" id="ot_area_save" onClick="ot_save_area()">Save</button>
				<button class="btn btn-danger" onClick="cancel_area()">Cancel</button>
			</td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="save_ot_area_master")
{
	$ot_area_name=mysqli_real_escape_string($link, $_POST["ot_area_name"]);
	$ot_type=mysqli_real_escape_string($link, $_POST["ot_type"]);
	$ot_rate=mysqli_real_escape_string($link, $_POST["ot_rate"]);
	
	mysqli_query($link, " INSERT INTO `ot_area_master`(`ot_area_name`, `ot_area_type`, `ot_area_rate`) VALUES ('$ot_area_name','$ot_type','$ot_rate') ");
	
}
if($_POST["type"]=="ot_area_load")
{
	$ot_area_id=$_POST['ot_area_id'];
	if($ot_area_id>0)
	{
	
	$area_name=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ot_area_master` WHERE `ot_area_id`='$ot_area_id' "));
?>
	<table class="table table-striped table-bordered table-condensed" id="cleaning_area_details">
		<tr>
			<th colspan="6">Selected Area: </th>
		</tr>
		<tr>
			<th class="span2">OT Room Name</th>
			<td>
				<input type="hidden" id="sel_ot_area_id" value="<?php echo $ot_area_id; ?>">
				<input type="text" id="sel_ot_area_name" class="" onKeyup="ot_area_name_up(this.value,event)" value="<?php echo $area_name["ot_area_name"]; ?>">
			</td>
			<th>OT Type</th>
			<td>
				<select id="sel_ot_type" onKeyup="ot_type_up(this.value,event)">
					<option value="0">Select</option>
					<option value="Primary" <?php if($area_name["ot_area_type"]=="Primary"){ echo "selected"; } ?> >Primary</option>
					<option value="Major" <?php if($area_name["ot_area_type"]=="Major"){ echo "selected"; } ?> >Major</option>
				</select>
				
				<button class="btn btn-info" onClick="update_area()">Update</button>
			</td>
			<th style="display:none;">Rate</th>
			<td style="display:none;">
				<input type="text" id="sel_ot_rate" class="span2" onKeyup="ot_rate_up(this.value,event)" value="<?php echo $area_name["ot_area_rate"]; ?>">
			</td>
		</tr>
		<tr id="row" class="area_item_mat_row" style="display:none;">
			<th class="span2">Item</th>
			<td>
				<select class="" id="item_id" name="item_id">
					<option value="0">Select</option>
					<?php
						$item_qry=mysqli_query($link," SELECT * FROM `cleaning_item_master` order by `item_id` ");
						while($item=mysqli_fetch_array($item_qry))
						{		
							echo "<option value='$item[item_id]'>$item[item_name]</option>";
						}
						?>
				</select>
			</td>
			<th>Material</th>
			<td>
				<select class="" id="item_mat_id" name="item_mat_id">
					<option value="0">Select</option>
					<?php
						$item_mat_qry=mysqli_query($link," SELECT * FROM `cleaning_material_master` order by `item_mat_id` ");
						while($item_mat=mysqli_fetch_array($item_mat_qry))
						{		
							echo "<option value='$item_mat[item_mat_id]'>$item_mat[item_mat_name]</option>";
						}
						?>
				</select>
			</td>
			<th>Frequency</th>
			<td>
				<select class="" id="frequency" name="frequency">
					<option value="0">Select</option>
					<option value="1">Once a day</option>
					<option value="2">Twice a day</option>
					<option value="3">Thice a day</option>
					<option value="7">Once a week</option>
					<option value="30">Once a month</option>
				</select>
				<button class="btn btn-info" onClick="save_area_info()">Save</button>
			</td>
		</tr>
	</table>
<?php
	}
}
if($_POST["type"]=="ot_area_master_update")
{
	$sel_ot_area_id=$_POST['sel_ot_area_id'];
	$sel_ot_area_name=$_POST['sel_ot_area_name'];
	$sel_ot_area_name= str_replace("'", "''", "$sel_ot_area_name");
	$sel_ot_type=$_POST['sel_ot_type'];
	$sel_ot_rate=$_POST['sel_ot_rate'];
	
	mysqli_query($link, " UPDATE `ot_area_master` SET `ot_area_name`='$sel_ot_area_name',`ot_area_type`='$sel_ot_type',`ot_area_rate`='$sel_ot_rate' WHERE `ot_area_id`='$sel_ot_area_id' ");
}
if($_POST["type"]=="ot_area_data_save")
{
	$sel_ot_area_id=$_POST['sel_ot_area_id'];
	$item_id=$_POST['item_id'];
	$item_mat_id=$_POST['item_mat_id'];
	$frequency=$_POST['frequency'];
	
	$q_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ot_area` WHERE `ot_area_id`='$sel_ot_area_id' and `item_id`='$item_id' and `item_mat_id`='$item_mat_id' "));
	if($q_num==0)
	{
		mysqli_query($link, " INSERT INTO `ot_area`(`ot_area_id`, `item_id`, `item_mat_id`, `frequency`) VALUES ('$sel_ot_area_id','$item_id','$item_mat_id','$frequency') ");
		echo "11";
	}else
	{
		echo "22";
	}
}
if($_POST["type"]=="ot_area_added_data_load")
{
	$area_id=$_POST["ot_area_id"];
	if($area_id==0)
	{
		$q_qry=mysqli_query($link, " SELECT * FROM `ot_area` order by `slno` DESC ");
	}else
	{
		$q_qry=mysqli_query($link, " SELECT * FROM `ot_area` WHERE `ot_area_id`='$area_id' order by `slno` DESC ");
	}
	$q_num=mysqli_num_rows($q_qry);
	if($q_num>0)
	{
?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th>
			<th>OT Room Name</th>
			<th>Item Name</th>
			<th>Material Name</th>
			<th>Frequency</th>
		</tr>
<?php
	$i=1;
	while($q=mysqli_fetch_array($q_qry))
	{
		$area_name=mysqli_fetch_array(mysqli_query($link, " SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$q[ot_area_id]' "));
		$item_name=mysqli_fetch_array(mysqli_query($link, " SELECT `item_name` FROM `cleaning_item_master` WHERE `item_id`='$q[item_id]' "));
		$item_mat_name=mysqli_fetch_array(mysqli_query($link, " SELECT `item_mat_name` FROM `cleaning_material_master` WHERE `item_mat_id`='$q[item_mat_id]' "));
		$freq="";
		if($q["frequency"]==1)
		{
			$freq="Once A day";
		}
		if($q["frequency"]==2)
		{
			$freq="Twice A day";
		}
		if($q["frequency"]==3)
		{
			$freq="Thice A day";
		}
		if($q["frequency"]==7)
		{
			$freq="Once A week";
		}
		if($q["frequency"]==30)
		{
			$freq="Once A month";
		}
?>
	<tr onClick="load_selected_area_data('<?php echo $q['item_id']; ?>','<?php echo $q['item_mat_id']; ?>','<?php echo $q['frequency']; ?>')" style="cursor:pointer;">
		<td><?php echo $i; ?></td>
		<td><?php echo $area_name["ot_area_name"]; ?></td>
		<td><?php echo $item_name["item_name"]; ?></td>
		<td><?php echo $item_mat_name["item_mat_name"]; ?></td>
		<td>
			<?php echo $freq; ?>
			<button class="btn btn-mini btn-danger text-right" onClick="remove_selected_area(<?php echo $q["slno"]; ?>)"><i class="icon-remove"></i></button>
		</td>
	</tr>
<?php
	$i++;
	}
?>
	</table>
<?php
	}
}
if($_POST["type"]=="delete_selected_area_data")
{
	$slno=$_POST['slno'];
	mysqli_query($link, " DELETE FROM `ot_area` WHERE `slno`='$slno' ");
}
?>
