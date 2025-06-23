<?php
session_start();
$emp_id=trim($_SESSION['emp_id']);

include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$branch_id=$emp_info["branch_id"];

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="load_refer_doc") // gov
{
	$srch=$_POST["srch"];
	
	$q=mysqli_query($link,"SELECT * FROM `refbydoctor_master` ORDER BY `ref_name`");
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `refbydoctor_master` WHERE `ref_name` LIKE '%$srch%' ORDER BY `ref_name`");
	}
?>
	<table class="table table-bordered table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th><th>Name</th><th>Qualification</th><th>Phone</th><th>Address</th><th class="edit_td"></th>
			</tr>
		</thead>
		<?php
		$n=1;
		
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['ref_name'];?></td>
			<td><?php echo $r['qualification'];?></td>
			<td><?php echo $r['phone'];?></td>
			<td><?php echo $r['address'];?></td>
			<td class="edit_td">
			<?php if($r["refbydoctorid"]!="101"){ ?>
				<button class="btn btn-edit btn-mini" onclick="edt('<?php echo $r["refbydoctorid"];?>')"><i class="icon-edit"></i> Edit</button>
				<!--<button class="btn btn-delete btn-mini" onclick="del('<?php echo $r["refbydoctorid"];?>')"><i class="icon-remove"></i> Delete</button>-->
			<?php } ?>
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="save_refer_doc")
{
	$refbydoctorid=$_POST['id'];
	$name=mysqli_real_escape_string($link, $_POST['name']);
	$quali=mysqli_real_escape_string($link, $_POST['quali']);
	$add=mysqli_real_escape_string($link, $_POST['add']);
	$contact=mysqli_real_escape_string($link, $_POST['contact']);
	$discount_prchnt=mysqli_real_escape_string($link, $_POST['discount_prchnt']);
	$executive_id=mysqli_real_escape_string($link, $_POST['executive_id']);
	$lcd_rate=mysqli_real_escape_string($link, $_POST['lcd_rate']);
	$full_rate=mysqli_real_escape_string($link, $_POST['full_rate']);
	$not_required=mysqli_real_escape_string($link, $_POST['not_required']);
	$user=$_POST['usr'];
	
	if(!$discount_prchnt){ $discount_prchnt=0; }
	$en_type="";
	
	if($refbydoctorid>0)
	{
		mysqli_query($link, "INSERT INTO `refbydoctor_master_edit`(`refbydoctorid`, `ref_name`, `qualification`, `address`, `phone`, `lcd_rate`, `full_rate`, `en_type`, `discount_prchnt`, `branch_id`, `not_required`, `edit_user`, `edit_date`, `edit_time`) SELECT `refbydoctorid`, `ref_name`, `qualification`, `address`, `phone`, `lcd_rate`, `full_rate`, `en_type`, `discount_prchnt`, `branch_id`, `not_required`,'$c_user','$date','$time' FROM `refbydoctor_master` WHERE `refbydoctorid`='$id'");
		
		if(mysqli_query($link,"UPDATE `refbydoctor_master` SET `ref_name`='$name',`qualification`='$quali',`address`='$add',`phone`='$contact',`discount_prchnt`='$discount_prchnt',`lcd_rate`='$lcd_rate',`full_rate`='$full_rate',`not_required`='$not_required' WHERE `refbydoctorid`='$refbydoctorid'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `refbydoctor_master`(`ref_name`, `qualification`, `address`, `phone`, `lcd_rate`, `full_rate`, `en_type`, `discount_prchnt`, `branch_id`, `not_required`) VALUES ('$name','$quali','$add','$contact','$lcd_rate','$full_rate','$en_type','$discount_prchnt','$branch_id','$not_required')"))
		{
			$last_row=mysqli_fetch_array(mysqli_query($link, "SELECT `refbydoctorid` FROM `refbydoctor_master` WHERE `ref_name`='$name' AND `phone`='$contact'"));
			$refbydoctorid=$last_row["refbydoctorid"];
			
			if($executive_id>0 && $refbydoctorid>0)
			{
				mysqli_query($link, "DELETE FROM `marketing_master` WHERE `emp_id`='$executive_id' AND `refbydoctorid`='$refbydoctorid'");
				
				mysqli_query($link, "INSERT INTO `marketing_master`(`emp_id`, `refbydoctorid`, `user`, `date`, `time`) VALUES ('$executive_id','$refbydoctorid','$c_user','$date','$time')");
			}
			
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}
if($_POST["type"]=="edit_refer_doc") // gov
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refbydoctor_master` WHERE `refbydoctorid`='$id'"));
	echo $id."@govin@".$d['ref_name']."@govin@".$d['qualification']."@govin@".$d['address']."@govin@".$d['phone']."@govin@".$d['lcd_rate']."@govin@".$d['full_rate']."@govin@".$d['en_type']."@govin@".$d['discount_prchnt']."@govin@".$d['not_required'];
}

if($_POST["type"]=="delete_refer_doc")
{
	$id=$_POST['id'];
	
	$check=mysqli_fetch_array(mysqli_query($link,"SELECT `slno` FROM `uhid_and_opdid` WHERE `refbydoctorid`='$id'"));
	
	if(!$check)
	{
		if(mysqli_query($link,"DELETE FROM `refbydoctor_master` WHERE `refbydoctorid`='$id'"))
		{
			echo "Deleted";
		}
		else
		{
			echo "Failed, try again later.";
		}
	}
	else
	{
		echo "Doctor has been used.";
	}
}

if($_POST["type"]=="merge_doctor_div")
{
?>
	<table class="table table-condensed">
		<tr>
			<th>Main Doctor</th>
			<td>
				<select id="main_doc" onchange="main_doc_change()">
					<option value="0">Select</option>
			<?php
				$qry=mysqli_query($link,"SELECT * FROM `refbydoctor_master` ORDER BY `ref_name`");
				while($doc_info=mysqli_fetch_array($qry))
				{
					echo "<option value='$doc_info[refbydoctorid]'>$doc_info[ref_name]</option>";
				}
			?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Duplicate Doctor(s)</th>
			<td>
				<select multiple id="duplicate_doc">
					<!--<option value="0">Select</option>
			<?php
				$qry=mysqli_query($link,"SELECT * FROM `refbydoctor_master` ORDER BY `ref_name`");
				while($doc_info=mysqli_fetch_array($qry))
				{
					echo "<option value='$doc_info[refbydoctorid]'>$doc_info[ref_name]</option>";
				}
			?>-->
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<center>
					<button class="btn btn-save" onclick="save_merge()"><i class="icon-save"></i> Merge</button>
					<button class="btn btn-close" id="modal_close_btn" data-dismiss="modal"><i class="icon-off"></i> Close</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="load_duplicate_doc")
{
	$main_doc=$_POST["main_doc"];
	
	$qry=mysqli_query($link,"SELECT * FROM `refbydoctor_master` WHERE `refbydoctorid` NOT IN('$main_doc','101') ORDER BY `ref_name`");
	while($doc_info=mysqli_fetch_array($qry))
	{
		echo "<option value='$doc_info[refbydoctorid]'>$doc_info[ref_name]</option>";
	}
}

if($_POST["type"]=="save_merge")
{
	//print_r($_POST);
	
	$main_doc=$_POST["main_doc"];
	$duplicate_doc=$_POST["duplicate_doc"];
	
	$val=0;
	
	foreach($duplicate_doc AS $dupli_doc)
	{
		if($dupli_doc)
		{
			$dupli_doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name`, `qualification`, `address`, `phone` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dupli_doc'"));
			
			$ref_name=mysqli_real_escape_string($link, $dupli_doc_info["ref_name"]);
			$qualification=mysqli_real_escape_string($link, $dupli_doc_info["qualification"]);
			$address=mysqli_real_escape_string($link, $dupli_doc_info["address"]);
			$phone=mysqli_real_escape_string($link, $dupli_doc_info["phone"]);
			
			if(mysqli_query($link, "INSERT INTO `refer_doctor_merge_record`(`refbydoctorid`, `ref_name`, `qualification`, `address`, `phone`, `main_refbydoctorid`, `user`, `date`, `time`) VALUES ('$dupli_doc','$ref_name','$qualification','$address','$phone','$main_doc','$c_user','$date','$time')"))
			{
				mysqli_query($link," UPDATE `uhid_and_opdid` SET `refbydoctorid`='$main_doc' WHERE `refbydoctorid`='$dupli_doc' ");
				
				mysqli_query($link," UPDATE `ipd_test_ref_doc` SET `refbydoctorid`='$main_doc' WHERE `refbydoctorid`='$dupli_doc' ");
				
				mysqli_query($link," UPDATE `ipd_test_ref_doc` SET `ref_id`='$main_doc' WHERE `refbydoctorid`='$dupli_doc' ");
				
				mysqli_query($link," UPDATE `pat_ref_doc` SET `refbydoctorid`='$main_doc' WHERE `refbydoctorid`='$dupli_doc' ");
				
				mysqli_query($link," UPDATE `patient_refer_details` SET `refbydoctorid`='$main_doc' WHERE `refbydoctorid`='$dupli_doc' ");
				
				
				mysqli_query($link," DELETE FROM `dal_com_setup` WHERE `refbydoctorid`='$dupli_doc' ");
				
				mysqli_query($link," DELETE FROM `refbydoctor_master` WHERE `refbydoctorid`='$dupli_doc' ");
				
				$val++;
			}
		}
	}
	
	if($val>0)
	{
		echo "Successfully Merged";
	}
	else
	{
		echo "Failed, try again later.";
	}
}
?>
