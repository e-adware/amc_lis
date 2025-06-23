<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_info["branch_id"];

function convert_date_ch($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-Y', $timestamp);
		return $new_date;
	}
}

$date=date("Y-m-d");





//---------------------------------------------------------------------------------------------------//
function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
//-------------------------------------------------------------------------------------------------//
if($_POST["type"]=="edit_consult_doc") // gov
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$id'"));
	echo $id."@govin@".$d['Name']."@govin@".$d['Designation']."@govin@".$d['pass']."@govin@".$d['Address']."@govin@".$d['Phone_number']."@govin@".$d['email']."@govin@".$d['doc_type']."@govin@".$d['opd_visit_fee']."@govin@".$d['ipd_visit_fee']."@govin@".$d['dept_id']."@govin@".$d['validity']."@govin@";
}

if($_POST["type"]=="load_consult_doc") // gov
{
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th><th>Name</th><th></th>
		</tr>
		<?php
		$n=1;
		$q=mysqli_query($link,"SELECT * FROM `consultant_doctor_master` ORDER BY `Name`");
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td width="70%"><?php echo $r['Name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[consultantdoctorid];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[consultantdoctorid];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="search_consult_doc") // gov
{
	$srch=$_POST['srch'];
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th><th>Name</th><th></th>
		</tr>
		<?php
		$n=1;
		$q=mysqli_query($link,"SELECT * FROM `consultant_doctor_master` WHERE `Name` like '$srch%' ORDER BY `Name`");
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td width="70%"><?php echo $r['Name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[consultantdoctorid];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[consultantdoctorid];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_dept_id") // gov
{
	echo $vid=nextId("","doctor_specialist_list","speciality_id","1");
}

if($_POST["type"]=="load_ward_id") // gov
{
	$i=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(ward_id) as max FROM `ward_master`"));
	echo $i['max']+1;
}

if($_POST["type"]=="load_dept") // gov
{
	$user=$_POST["user"];
	
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>ID</th>
			<th>Department</th>
		<?php if($user==101 || $user==102){ ?>
			<th>Action</th>
		<?php } ?>
		</tr>
		<?php
		$q=mysqli_query($link,"SELECT * FROM `doctor_specialist_list` order by `name`");
		while($r=mysqli_fetch_array($q))
		{
			if($r['speciality_id']==1 || $r['speciality_id']==2)
			{
				$dis="disabled='disabled'";
			}
			else
			{
				$dis="";
			}
		?>
		<tr>
			<td><?php echo $r['speciality_id'];?></td>
			<td><?php echo $r['name'];?></td>
		<?php if($user==101 || $user==102){ ?>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r['speciality_id'];?>')" value="Edit" <?php echo $dis;?> />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r['speciality_id'];?>')" value="Delete" <?php echo $dis;?> />
			</td>
		<?php } ?>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_ward") // gov
{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Id</th><th>Ward</th><th>Floor</th><th>Action</th>
		</tr>
		<?php
		$q=mysqli_query($link,"SELECT * FROM `ward_master` ORDER BY `name`");
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $r['ward_id'];?></td>
			<td><?php echo $r['name'];?></td>
			<td><?php echo $r['floor_name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[sl_no];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[sl_no];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="edit_dept") // gov
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `doctor_specialist_list` WHERE `speciality_id`='$id'"));
	echo $id."@govin@".$d['name']."@govin@";
}

if($_POST["type"]=="search_dept") // gov
{
	$srch=$_POST['srch'];
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Id</th><th>Department</th><th>Action</th>
		</tr>
		<?php
		$q=mysqli_query($link,"SELECT * FROM `doctor_specialist_list` WHERE `name` like '$srch%' ORDER BY `name`");
		while($r=mysqli_fetch_array($q))
		{
			if($r['speciality_id']==1 || $r['speciality_id']==2)
			{
				$dis="disabled='disabled'";
			}
			else
			{
				$dis="";
			}
		?>
		<tr>
			<td><?php echo $r['speciality_id'];?></td>
			<td><?php echo $r['name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r['speciality_id'];?>')" value="Edit" <?php echo $dis;?> />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r['speciality_id'];?>')" value="Delete" <?php echo $dis;?> />
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="search_ward") // gov
{
	$srch=$_POST['srch'];
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Id</th><th>Ward</th><th>Action</th>
		</tr>
		<?php
		$q=mysqli_query($link,"SELECT * FROM `ward_master` WHERE `name` like '$srch%' ORDER BY `name`");
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $r['ward_id'];?></td>
			<td><?php echo $r['name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[sl_no];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[sl_no];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_level_id") // gov
{
	$i=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(levelid) as max FROM `level_master`"));
	echo $i['max']+1;
}

if($_POST["type"]=="load_level") // gov
{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Id</th><th>Level Name</th><th>Action</th>
		</tr>
		<?php
		$q=mysqli_query($link,"SELECT * FROM `level_master` ORDER BY `name`");
		while($r=mysqli_fetch_array($q))
		{
			if($r['levelid']==5)
			{
				$dis="disabled='disabled'";
			}
			else
			{
				$dis="";
			}
		?>
		<tr>
			<td><?php echo $r['levelid'];?></td>
			<td><?php echo $r['name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[slno];?>')" value="Edit" <?php echo $dis;?> />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[slno];?>')" value="Delete" <?php echo $dis;?> />
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="edit_level") // gov
{
	$sl=$_POST['sl'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `level_master` WHERE `slno`='$sl'"));
	echo $sl."@govin@".$d['levelid']."@govin@".$d['name']."@govin@".$d['snippets']."@govin@";
}

if($_POST["type"]=="edit_ward") // gov
{
	$sl=$_POST['sl'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ward_master` WHERE `sl_no`='$sl'"));
	echo $sl."@govin@".$d['ward_id']."@govin@".$d['name']."@govin@".$d['floor_name']."@govin@";
}

if($_POST["type"]=="search_level") // gov
{
	$srch=$_POST['srch'];
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Id</th><th>Level Name</th><th>Action</th>
		</tr>
		<?php
		$q=mysqli_query($link,"SELECT * FROM `level_master` WHERE `name` like '$srch%' ORDER BY `name`");
		while($r=mysqli_fetch_array($q))
		{
			if($r['levelid']==5)
			{
				$dis="disabled='disabled'";
			}
			else
			{
				$dis="";
			}
		?>
		<tr>
			<td><?php echo $r['levelid'];?></td>
			<td><?php echo $r['name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[slno];?>')" value="Edit" <?php echo $dis;?> />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[slno];?>')" value="Delete" <?php echo $dis;?> />
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_menu") // gov
{
	$head=$_POST['head'];
?>
<table class="table table-condensed" style="">
	<tr>
		<th>Parameter Id</th><th>Header</th><th>Name</th><th>Sequence</th><th>Action</th><?php if($emp_id==101 || $emp_id==102){ ?><th>Hide</th><?php } ?>
	</tr>
	<?php
	$q=mysqli_query($link,"SELECT * FROM `menu_master` WHERE `header`='$head' ORDER BY `sequence`,`par_name`");
	while($r=mysqli_fetch_array($q))
	{
		$h=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `menu_header_master` WHERE `id`='$r[header]'"));
	?>
	<tr>
		<td><?php echo $r['par_id'];?></td>
		<td><?php echo $h['name'];?></td>
		<td><?php echo $r['par_name'];?></td>
		<td><?php echo $r['sequence'];?></td>
		<td><input type="button" id="upd" class="btn btn-info btn-mini" onclick="edit('<?php echo $r[par_id];?>')" value="Update" /> <input type="button" id="upd" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[par_id];?>')" value="Delete" /></td>
		<?php if($emp_id==101 || $emp_id==102){ ?>
		<td>
			<label><input type="radio" name="vaccu<?php echo $r['par_id']; ?>" <?php if($r['hidden']==1){echo "checked='checked'";}?> value="1" onClick="menu_hidden(this.value,'<?php echo $r['par_id']; ?>')" /> Yes</label>
			<label><input type="radio" name="vaccu<?php echo $r['par_id']; ?>" <?php if($r['hidden']==0){echo "checked='checked'";}?> value="0" onClick="menu_hidden(this.value,'<?php echo $r['par_id']; ?>')" /> no</label>
		</td>
		<?php } ?>
	</tr>
	<?php
	}
	?>
</table>
<style>
	label
	{
		display:inline-block;
	}
</style>
<?php
}

if($_POST["type"]=="edit_menu") // gov
{
	$id=$_POST['id'];
	$m=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `menu_master` WHERE `par_id`='$id'"));
	echo $id."@gov@".$m['par_name']."@gov@".$m['header']."@gov@".$m['sequence']."@gov@";
}

if($_POST["type"]=="load_header") // gov
{
	$hid=$_POST['hid'];
	$h=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `menu_header_master` WHERE `id`='$hid'"));
	echo $hid."@gov@".$h['name']."@gov@".$h['sequence']."@gov@";
}

if($_POST["type"]=="edit_user") // gov
{
	$id=$_POST['id'];
	$u=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `employee` WHERE `emp_id`='$id'"));
	echo $id."@govin@".$u['name']."@govin@".$u['phone']."@govin@".$u['address']."@govin@".$u['Qualification']."@govin@".$u['staff_type']."@govin@".$u['password']."@govin@".$u['edit_opd']."@govin@".$u['edit_lab']."@govin@".$u['cancel_pat']."@govin@";
}

if($_POST["type"]=="load_user") // gov
{
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th><th>Name</th><th></th>
		</tr>
		<?php
		$n=1;
		$q=mysqli_query($link,"SELECT * FROM `employee` ORDER BY `name`");
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td width="70%"><?php echo $r['name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[emp_id];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[emp_id];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="search_user") // gov
{
	$srch=$_POST['srch'];
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th><th>Name</th><th></th>
		</tr>
		<?php
		$n=1;
		$q=mysqli_query($link,"SELECT * FROM `employee` WHERE `name` like '$srch%' ORDER BY `name`");
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td width="70%"><?php echo $r['name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[emp_id];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[emp_id];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="edit_refer_doc") // gov
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refbydoctor_master` WHERE `refbydoctorid`='$id'"));
	echo $id."@govin@".$d['ref_name']."@govin@".$d['qualification']."@govin@".$d['address']."@govin@".$d['phone']."@govin@";
}

if($_POST["type"]=="load_refer_doc") // gov
{
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th><th>Name</th><th></th>
		</tr>
		<?php
		$n=1;
		$q=mysqli_query($link,"SELECT * FROM `refbydoctor_master` ORDER BY `ref_name`");
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td width="70%"><?php echo $r['ref_name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[refbydoctorid];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[refbydoctorid];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="search_refer_doc") // gov
{
	$srch=$_POST['srch'];
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th><th>Name</th><th></th>
		</tr>
		<?php
		$n=1;
		$q=mysqli_query($link,"SELECT * FROM `refbydoctor_master` WHERE `ref_name` like '$srch%' ORDER BY `ref_name`");
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td width="70%"><?php echo $r['ref_name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[refbydoctorid];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[refbydoctorid];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_room") // gov
{
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th><th>Ward</th><th>Room No</th><th></th>
		</tr>
		<?php
		$n=1;
		$q=mysqli_query($link,"SELECT * FROM `room_master` ORDER BY `ward_id`");
		while($r=mysqli_fetch_array($q))
		{
			$w=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$r[ward_id]'"));
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $w['name'];?></td>
			<td><?php echo $r['room_no'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[room_id];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[room_id];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="edit_room") // gov
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `room_master` WHERE `room_id`='$id'"));
	echo $id."@govin@".$d['ward_id']."@govin@".$d['room_no']."@govin@";
}

if($_POST["type"]=="load_room_list")
{
	$ward=$_POST['ward'];
	if($ward==0)
	{
		?>
		<select id="room">
			<option value="0">--Select--</option>
		</select>
		<?php
	}
	else
	{
		?>
		<select id="room">
			<option value="0">--Select--</option>
			<?php
			$q=mysqli_query($link,"SELECT * FROM `room_master` WHERE `ward_id`='$ward'");
			while($r=mysqli_fetch_array($q))
			{
			?>
			<option value="<?php echo $r['room_id'];?>"><?php echo $r['room_no'];?></option>
			<?php
			}
			?>
		</select>
		<?php
	}
	?>
	<script>
		$("#room").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				{
					$(this).css("border","1px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#bed").focus();
				}
			}
		});
	</script>
	<?php
}

if($_POST["type"]=="load_bed") // gov
{
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th><th>Ward</th><th>Room No</th><th>Bed No</th><th></th>
		</tr>
		<?php
		$n=1;
		$q=mysqli_query($link,"SELECT * FROM `bed_master` ORDER BY `ward_id`");
		while($r=mysqli_fetch_array($q))
		{
			$w=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$r[ward_id]'"));
			$rm=mysqli_fetch_array(mysqli_query($link,"SELECT `room_no` FROM `room_master` WHERE `room_id`='$r[room_id]'"));
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $w['name'];?></td>
			<td><?php echo $rm['room_no'];?></td>
			<td><?php echo $r['bed_no'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[bed_id];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[bed_id];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="edit_bed") // gov
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `bed_master` WHERE `bed_id`='$id'"));
	echo $id."@govin@".$d['ward_id']."@govin@".$d['room_id']."@govin@".$d['bed_no']."@govin@".$d['charges']."@govin@".$d['other_charges']."@govin@";
}
if($_POST["type"]=="bed_add_other_charge")
{
	$sval=$_POST['sval'];
?>
	<table class="table">
<?php
	$sval=explode("@@",$sval);
	foreach($sval as $chrg)
	{
		if($chrg)
		{
			$d=mysqli_fetch_array(mysqli_query($link," SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$chrg' "));
		?>
			<tr>
				<td>
					<?php echo $d["charge_name"]; ?>
					<span onClick="delete_data('<?php echo $chrg;?>')" class="text-right" style="cursor:pointer;"><img height="15" width="15" src="../images/delete.ico"/></span>
				</td>
			</tr>
		<?php
		}
	}
	?>
	</table>
	<?php
}
if($_POST["type"]=="area_add_other_charge")
{
	$sval=$_POST['sval'];
	if($sval)
	{
?>
	<table class="table">
		<tr>
			<th>Item Name</th>
			<th>Material Name</th>
			<th>Frequency</th>
		</tr>
<?php
	$sval=explode("###",$sval);
	foreach($sval as $chrg)
	{
		if($chrg)
		{
			$chrg=explode("@@",$chrg);
			$item=$chrg[0];
			$item_id= str_replace('i', '', $item);
			$item_mat=$chrg[1];
			$item_mat_id= str_replace('m', '', $item_mat);
			$freq=$chrg[2];
			$freq_id= str_replace('f', '', $freq);
			if($freq_id==1)
			{
				$sel_freq="Once a day";
			}
			if($freq_id==2)
			{
				$sel_freq="Twice a day";
			}
			if($freq_id==3)
			{
				$sel_freq="Thice a day";
			}
			if($freq_id==7)
			{
				$sel_freq="Weekly";
			}
			if($freq_id==1)
			{
				$sel_freq="Monthly";
			}
			
			$itm=mysqli_fetch_array(mysqli_query($link," SELECT `item_name` FROM `cleaning_item_master` WHERE `item_id`='$item_id' "));
			$itm_mat=mysqli_fetch_array(mysqli_query($link," SELECT `item_mat_name` FROM `cleaning_material_master` WHERE `item_mat_id`='$item_mat_id' "));
		?>
			<tr>
				<td><?php echo $itm["item_name"]; ?></td>
				<td><?php echo $itm_mat["item_mat_name"]; ?></td>
				<td>
					<?php echo $sel_freq; ?>
					<!--<span onClick="delete_data('<?php echo $chrg;?>')" class="text-right" style="cursor:pointer;"><img height="15" width="15" src="../images/delete.ico"/></span>-->
				</td>
			</tr>
		<?php
		}
	}
	?>
	</table>
	<?php
	}
}

if($_POST["type"]=="search_patient_list") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$usr'"));
	$docid=$doc['consultantdoctorid'];
	$q="SELECT * FROM `appointment_book` WHERE `appointment_date`='$date' AND `consultantdoctorid`='$docid'";
	if($uhid)
	{
		$q="SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `consultantdoctorid`='$docid'";
	}
	if($opd)
	{
		$q="SELECT * FROM `appointment_book` WHERE `opd_id`='$opd' AND `consultantdoctorid`='$docid'";
	}
	if($name)
	{
		$q="SELECT * FROM `appointment_book` WHERE `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '$name%') AND `consultantdoctorid`='$docid'";
	}
	if($dat)
	{
		$q="SELECT * FROM `appointment_book` WHERE `appointment_date`='$dat' AND `consultantdoctorid`='$docid'";
	}
	//echo $q;
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>UHID</th>
				<th>PIN</th>
				<th>Name</th>
				<th>Sex</th>
				<th>Age (DOB)</th>
				<th>Contact</th>
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
				if($p["dob"]!=""){ $age=age_calculator($p["dob"])." (".$p["dob"].")"; }else{ $age=$p["age"]." ".$p["age_type"]; }
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['opd_id'];?>')" style="cursor:pointer;">
					<td><?php echo $r['patient_id'];?></td>
					<td><?php echo $r['opd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $age;?></td>
					<td><?php echo $p['phone'];?></td>
				</tr>
			<?php
			}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="doc_queue") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$prev_rec=$_POST['prev_rec'];
	$val=$_POST['val'];
	$usr=$_POST['usr'];
	$app=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid'"));
	if($app>1)
	{
		if($val!=6)
		{
			$prv="<a onclick='load_res(6)'>Previous Record</a>";
		}
		else
		{
			$prv="<a>Previous Record</a>";
		}
	}
	else
	{
		$prv="";
	}
	$pcr="";
	if($val==1)
	{
		$hd="Observations";
		$brd="<a class='current'>Observation</a><a onclick='load_res(2)'>Diagnosis</a><a onclick='load_res(3)'>Investigations</a><a onclick='load_res(4)'>Medications</a><a onclick='load_res(5)'>Disposition/Consultation</a>$prv";
	}
	if($val==2)
	{
		$hd="Diagnosis";
		$brd="<a onclick='load_res(1)'>Observation</a><a class='current'>Diagnosis</a><a onclick='load_res(3)'>Investigations</a><a onclick='load_res(4)'>Medications</a><a onclick='load_res(5)'>Disposition/Consultation</a>$prv";
	}
	if($val==3)
	{
		$hd="Investigations";
		$brd="<a onclick='load_res(1)'>Observation</a><a onclick='load_res(2)'>Diagnosis</a><a class='current'>Investigations</a><a onclick='load_res(4)'>Medications</a><a onclick='load_res(5)'>Disposition/Consultation</a>$prv";
	}
	if($val==4)
	{
		$hd="Medications";
		$brd="<a onclick='load_res(1)'>Observation</a><a onclick='load_res(2)'>Diagnosis</a><a onclick='load_res(3)'>Investigations</a><a class='current'>Medications</a><a onclick='load_res(5)'>Disposition/Consultation</a>$prv";
	}
	if($val==5)
	{
		$hd="Disposition / Consultation";
		$brd="<a onclick='load_res(1)'>Observation</a><a onclick='load_res(2)'>Diagnosis</a><a onclick='load_res(3)'>Investigations</a><a onclick='load_res(4)'>Medications</a><a class='current'>Disposition/Consultation</a>$prv";
	}
	if($val==6)
	{
		$hd="Previous Record";
		$prv="<a class='current'>Previous Record</a>";
		$brd="<a onclick='load_res(1)'>Observation</a><a onclick='load_res(2)'>Diagnosis</a><a onclick='load_res(3)'>Investigations</a><a onclick='load_res(4)'>Medications</a><a onclick='load_res(5)'>Disposition/Consultation</a>$prv";
	}
	if($prev_rec==0){ $dis_all="disabled"; }else{ $dis_all=""; }
	?>
	<div id="breadcrumb" style="background:transparent;"><?php echo $brd;?></div>
	<center>
		<b style="font-size:14pt;"><?php echo $hd;?>
			<input type="text" id="val" style="display:none;" value="<?php echo $val;?>" />
			<input type="text" id="prev_rec" style="display:none;" value="<?php echo $prev_rec;?>" />
		</b>
	</center>
		<?php
		if($val==1)
		{
			?>
			<table class="table table-condensed" id="hist_table">
				<tr>
					<th colspan="10" style="text-align:center;background:#666666;color:#ffffff;">History &amp; Examination</th>
				</tr>
			<?php
				$q=mysqli_query($link,"SELECT * FROM `pat_complaints` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
				$num=mysqli_num_rows($q);
				$nm=1;
				if($num>0)
				{
				while($r=mysqli_fetch_array($q))
				{
			?>
				<tr id="tr<?php echo $nm;?>">
					<th>Chief Complaints</th>
					<td colspan="4">
						<input list="browsrs" type="text" id="chief<?php echo $nm;?>" value="<?php echo $r['comp_one']; ?>" onkeyup="sel_chief(<?php echo $nm;?>,event)" />
						<datalist id="browsrs">
						<?php
							$qq = mysqli_query($link," SELECT * FROM `complain_master`");
							while($cc=mysqli_fetch_array($qq))
							{
								echo "<option value='$cc[complain]'>";
							}
						?>
						</datalist>
					</td>
					<td colspan="4">
						<b>For</b> 
						<select id="cc<?php echo $nm;?>" class="span2" onkeyup="sel_chief(<?php echo $nm;?>,event)">
							<option value="0">Select</option>
							<?php
							for($n=1;$n<=30;$n++)
							{
							?>
							<option value="<?php echo $n;?>" <?php if($n==$r['comp_two']){echo "selected='selected'";}?>><?php echo $n;?></option>
							<?php
							}
							?>
						</select>
						<select id="tim<?php echo $nm;?>" class="span2" onkeyup="sel_chief(<?php echo $nm;?>,event)">
							<option value="0">--Select--</option>
							<option value="Minutes" <?php if($r['comp_three']=="Minutes"){echo "selected='selected'";}?>>Minutes</option>
							<option value="Hours" <?php if($r['comp_three']=="Hours"){echo "selected='selected'";}?>>Hours</option>
							<option value="Days" <?php if($r['comp_three']=="Days"){echo "selected='selected'";}?>>Days</option>
							<option value="Week" <?php if($r['comp_three']=="Week"){echo "selected='selected'";}?>>Week</option>
							<option value="Month" <?php if($r['comp_three']=="Month"){echo "selected='selected'";}?>>Month</option>
							<option value="Year" <?php if($r['comp_three']=="Year"){echo "selected='selected'";}?>>Year</option>
						</select>
					</td>
					<td>
					<?php if($nm==1){ ?>
						<span style="float:right"><input type="button" id="addmore" class="btn btn-mini btn-info" onclick="add_row(1)" value="Add More" <?php echo $dis_all; ?> ></span>
					<?php }else{ if($prev_rec==1){ ?>
						<span style="float:right"><button type="button" class="btn btn-mini btn-danger" onclick="del_comp('<?php echo $r['slno']; ?>')" ><span class="icon-remove"></span></button></span>
						<?php } } ?>
					</td>
				</tr>
			<?php
			$nm++;
				}
				}
				else
				{
				?>
				<tr id="tr1">
					<th>Chief Complaints</th>
					<td colspan="4">
						<input list="browsrs" type="text" id="chief1" value="" onkeyup="sel_chief(1,event)" />
						<datalist id="browsrs">
						<?php
							$qq = mysqli_query($link," SELECT * FROM `complain_master`");
							while($cc=mysqli_fetch_array($qq))
							{
								echo "<option value='$cc[complain]'>";
							}
						?>
						</datalist>
					</td>
					<td colspan="4">
						<b>For</b> 
						<select id="cc1" class="span2" onkeyup="sel_chief(1,event)">
							<option value="0">Select</option>
							<?php
							for($n=1;$n<=30;$n++)
							{
							?>
							<option value="<?php echo $n;?>"><?php echo $n;?></option>
							<?php
							}
							?>
						</select>
						<select id="tim1" class="span2" onkeyup="sel_chief(1,event)">
							<option value="0">--Select--</option>
							<option value="Minutes">Minutes</option>
							<option value="Hours">Hours</option>
							<option value="Days">Days</option>
							<option value="Week">Week</option>
							<option value="Month">Month</option>
							<option value="Year">Year</option>
						</select>
					</td>
					<td>
						<span style="float:right"><input type="button" id="addmore" class="btn btn-mini btn-info" onclick="add_row(1)" value="Add More" <?php echo $dis_all; ?> ></span>
					</td>
				</tr>
				<?php
				}
				$h=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' ORDER BY `slno` DESC"));
			?>
				<tr id="hh">
					<th>History</th>
					<td colspan="9"><textarea style="resize:none;width:96%" name="history" onkeyup="tab(this.id,event)" id="history"><?php echo $h['history'];?></textarea></td>
				</tr>
				<tr>
					<th>Examination</th>
					<td colspan="9"><textarea style="resize:none;width:96%" name="exam" onkeyup="tab(this.id,event)" id="exam"><?php echo $h['examination'];?></textarea></td>
				</tr>
				<!--<tr>
					<td colspan="10"><span style="float:right;"><input type="button" id="sav" class="btn btn-info" value="Save" onclick="save_exam()" <?php echo $dis_all; ?> ></span></td>
				</tr>-->
					<tr>
						<th colspan="10" style="text-align:center;background:#666666;color:#ffffff;">Vitals</th>
					</tr>
					<?php
					$vitl=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
					?>
					<tr>
						<td><b>Weight</b> <input id="val" style="display:none;" value="3" type="text"></td>
						<td><input id="weight" class="span1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');physical(this.value,event)" placeholder="KG" type="text" value="<?php echo $vitl['weight'];?>" /></td>
						<td><b>Height</b></td>
						<td><input id="height" class="span1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');physical1(this.value,event)" placeholder="CM" type="text" value="<?php echo $vitl['height'];?>" /></td>
						<td><b>BMI</b></td>
						<td><input id="bmi1" readonly="readonly" style="width:30px;" value="<?php echo $vitl['BMI_1'];?>" type="text" /> <input id="bmi2" readonly="readonly" style="width:30px;" value="<?php echo $vitl['BMI_2'];?>" type="text" /></td>
						<td><b>SPO<sub>2</sub>(%)</b></td>
						<td><input id="spo" type="text" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" value="<?php echo $vitl['spo2'];?>" /></td>
						<td><b>Pulse</b></td>
						<td><input id="pulse" type="text" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" value="<?php echo $vitl['pulse'];?>" /></td>
					</tr>
					<tr>
						<td><b>Temperature (<sup>o</sup>C)</b></td>
						<td><input id="temp" onkeyup="tab(this.id,event)" class="span1" type="text" value="<?php echo $vitl['temp'];?>" /></td>
						<td><b>PR</b></td>
						<td><input id="pr" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" value="<?php echo $vitl['PR'];?>" /></td>
						<td><b>RR(/min)</b></td>
						<td><input id="rr" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" value="<?php echo $vitl['RR'];?>" /></td>
						<td><b>BP:-</b> <b style="float:right;margin-right:10%;">Systolic:</b></td>
						<td><input id="systolic" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" value="<?php echo $vitl['systolic'];?>" /></td>
						<td><b>Diastolic:</b></td>
						<td><input id="diastolic" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" value="<?php echo $vitl['diastolic'];?>" /></td>
					</tr>
					<tr>
						<td><b>Note</b></td>
						<td colspan="10"><input type="text" id="vit_note" onkeyup="tab(this.id,event)" style="width:80%;" value="<?php echo $vitl['note'];?>" /></td>
					</tr>
					<!--<tr>
						<td colspan="10"><span style="float:right;"><input type="button" id="sav_vit" class="btn btn-info" value="Save" onclick="save_vital()" <?php echo $dis_all; ?> ></span></td>
					</tr>-->
			</table>			
			<style>
				#hist_table tr th, #hist_table tr td
				{
					padding-top:3px;
					padding-bottom:3px;
				}
				#hist_table tr:hover
				{background:none !important;}
			</style>
			<?php
		}
		if($val==10)
		{
		?>
		<div class="accordion" id="collapse-group">
			<div class="accordion-group widget-box">
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse1" data-toggle="collapse" onclick="show_icon(1)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">History &amp; Examination</b><i class="icon-arrow-down" id="ard1"></i><i class="icon-arrow-up" id="aru1" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign1" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign1" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse1" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl1" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box">
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse2" data-toggle="collapse" onclick="show_icon(2)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Vitals</b><i class="icon-arrow-down" id="ard2"></i><i class="icon-arrow-up" id="aru2" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign2" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign2" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="collapse accordion-body" id="collapse2" style="overflow:unset;">
					<div class="widget-content hidden_div" id="cl2" style="display:none;">
						<table class="table table-condensed">
							<tbody>
							<tr>
								<td><b>Weight</b> <input id="val" style="display:none;" value="3" type="text"></td>
								<td><input id="weight" class="span1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');physical(this.value,event)" placeholder="KG" type="text"></td>
								<td><b>Height</b></td>
								<td><input id="height" class="span1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');physical1(this.value,event)" placeholder="CM" type="text"></td>
								<td><b>BMI</b></td>
								<td><input id="bmi1" readonly="readonly" style="width:30px;" type="text"> <input id="bmi2" readonly="readonly" style="width:30px;" type="text"></td>
								<td><b>SPO<sub>2</sub>(%)</b></td>
								<td><input id="spo" type="text" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" /></td>
								<td><b>Pulse</b></td>
								<td><input id="pulse" type="text" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" /></td>
							</tr>
<!--
							<tr>
								<td></td>
								<td></td>
								<td><b>SPO<sub>2</sub>(%)</b></td>
								<td><input id="spo" type="text" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" /></td>
								<td><b>Pulse</b></td>
								<td><input id="pulse" type="text" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" /></td>
								<td><b>Temperature (<sup>o</sup>C)</b></td>
								<td><input id="temp" onkeyup="tab(this.id,event)" class="span1" type="text" /></td>
							</tr>
-->
							<tr>
								<td><b>Temperature (<sup>o</sup>C)</b></td>
								<td><input id="temp" onkeyup="tab(this.id,event)" class="span1" type="text" /></td>
								<td><b>PR</b></td>
								<td><input id="pr" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
								<td><b>RR(/min)</b></td>
								<td><input id="rr" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
								<td><b>BP:-</b> <b style="float:right;margin-right:10%;">Systolic:</b></td>
								<td><input id="systolic" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
								<td><b>Diastolic:</b></td>
								<td><input id="diastolic" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
							</tr>
							<tr>
								<td><b>Note</b></td>
								<td colspan="10"><input type="text" id="vit_note" onkeyup="tab(this.id,event)" style="width:80%;" /></td>
							</tr>
							<tr>
								<td colspan="10"><span style="float:right;"><input type="button" id="sav_vit" class="btn btn-info" value="Save" onclick="save_vital()" <?php echo $dis_all; ?> ></span></td>
							</tr>
						</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		if($val==2)
		{
		?>
		<div id="diag_res_all">
		
		</div>
		<?php
		}
		if($val==3)
		{
			$new_opd=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `opdid_link_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' "));
			if($new_opd["new_opd_id"])
			{
				$dis_new_test_entry="disabled";
			}else
			{
				$dis_new_test_entry="";
			}
		?>
		<table class="table table-condensed">
			<tr>
				<th>Select Test(s)</th>
				<td colspan="5">
					<input type="text" name="test" id="test" class="span9" onFocus="load_test_list()" onKeyUp="load_test_list1(this.value,event)" onBlur="javascript:$('#ref_doc').fadeOut(500)" <?php echo $dis_all.$dis_new_test_entry; ?> >
					<input type="hidden" id="testid" />
					<input type="hidden" id="rate" />
					<div id="doc_info"></div>
					<div id="ref_doc" align="center">
						<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
							<th>Test Name</th>
							<?php
								$d=mysqli_query($link, "SELECT * FROM `testmaster` order by `testname`");
								$i=1;
								while($d1=mysqli_fetch_array($d))
								{
							?>
								<tr onclick="doc_load('<?php echo $d1['testid'];?>','<?php echo $d1['testname'];?>','<?php echo $d1['rate'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
									<td><?php echo $d1['testname'];?>
										<div <?php echo "id=dvdoc".$i;?> style="display:none;">
										<?php echo "#".$d1['testid']."#".$d1['testname']."#".$d1['rate'];?>
										</div>
									</td>
								</tr>
							<?php
								$i++;
								}
							?>
						</table>
					</div>
				</td>
			</tr>
			<tr id="test_list" style="display:none;">
				<th>Selected Test(s)</th>
				<td colspan="5" id="test_list_data">
					
				</td>
			</tr>
		</table>
		<style>
			.table tr:hover{background:none;}
		</style>
		<?php
		}
		if($val==4)
		{
			if($dis_all)
			{
				$foc_fun="";
			}
			else
			{
				$foc_fun="load_medi_list()";
			}
			$del_btn=0;
		?>
		<table class="table table-condensed">
			<tr>
				<th width="15%">Drug Name</th>
				<td colspan="5">
					<input type="text" name="medi" id="medi" class="span8" onFocus="<?php echo $foc_fun;?>" onKeyUp="load_medi_list1(this.value,event)" onBlur="javascript:$('#med_div').fadeOut(500)" <?php echo $dis_all; ?> >
					<span class="text-right">
						<button type="button" class="btn btn-default" style="display:;" onclick="new_medi()" <?php echo $dis_all; ?>>New</button>
					</span>
					<input type="text" name="new_medi" id="new_medi" style="display:none;" onkeyup="meditab(this.id,event)" class="span8" <?php echo $dis_all; ?> >
					<input type="hidden" id="medid" />
					<div id="med_info"></div>
					<div id="med_div" align="center">
						<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
							<th>Drug Name</th>
							<?php
								//$d=mysqli_query($link, "SELECT * FROM `ph_stock_master` WHERE `quantity`>0");
								$d=mysqli_query($link, "SELECT * FROM `item_master` ORDER BY `item_name`");
								$i=1;
								while($d1=mysqli_fetch_array($d))
								{
									//$m=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `ph_item_master` where `item_code`='$d1[item_code]'"));
							?>
								<tr onclick="select_med('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['item_type_id'];?>','<?php echo $d1['generic_name'];?>')" style="cursor:pointer" <?php echo "id=med".$i;?>>
									<td><?php echo $d1['item_name'];?>
										<div <?php echo "id=mdname".$i;?> style="display:none;">
										<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$d1['item_type_id']."#".$d1['generic_name'];?>
										</div>
									</td>
								</tr>
							<?php
								$i++;
								}
							?>
						</table>
					</div>
					<span id="med_dos" style="display:none;">
						<table class="table table-condensed">
							<tr>
								<th>Dosage</th>
								<td>
									<input type="text" list="brow" id="dos" class="span6" onkeyup="meditab(this.id,event)" style="" <?php echo $dis_all; ?> /><br/>
									<span id="dos_list"></span>
								</td>
								<td>
									<input type="text" class="span1" id="ph_quantity" placeholder="Quantity" onkeyup="meditab(this.id,event)" >
								</td>
								<th>Instruction</th>
								<td>
									<select id="inst" style="width:120px;" onkeyup="meditab(this.id,event)" <?php echo $dis_all; ?>>
										<option value="1">As Directed</option>
										<option value="2">Before Meal</option>
										<option value="3">Empty Stomach</option>
										<option value="4">After Meal</option>
										<option value="5">In the Morning</option>
										<option value="6">In the Evening</option>
										<option value="7">At Bedtime</option>
										<option value="8">Immediately</option>
									</select>
								</td>
							</tr>
						</table>
						<center>
							<input type="button" id="add_medi" class="btn btn-info" value="Add" onclick="insert_medi()" <?php echo $dis_all; ?> />
							<button type="button" id="add_new_medi" style="display:none;" class="btn btn-info" onclick="save_new_medi()" <?php echo $dis_all; ?>>Add</button>
						</center>
					</span>
				</td>
			</tr>
			<tr id="medi_list" style="display:none;">
				<th>Selected Medicine(s)</th>
				<td id="medi_list_data">
				
				</td>
			</tr>
		</table>
		<style>
			.table tr:hover{background:none;}
		</style>
		<script>
			$("#st_date").datepicker(
			{
				dateFormat: 'yy-mm-dd',
				minDate: '0',
			});
		</script>
		<?php
		}
		if($val==5)
		{
		?>
		<div id="disp_res">
			
		</div>
		<?php
		}
		if($val==6)
		{
		?>
		<div id="disp_res">
			
		</div>
		<?php
		}
		if($val==7)
		{
		?>
		<div id="disp_res">
			
		</div>
		<?php
		}
}

if($_POST["type"]=="view_chief") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$prev_rec=$_POST['prev_rec'];
	$usr=$_POST['usr'];
	if($prev_rec==0){ $dis_all="disabled"; }else{ $dis_all=""; }
?>
	<table class="table table-condensed" id="hist_table">
<?php
	$q=mysqli_query($link,"SELECT * FROM `pat_complaints` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$num=mysqli_num_rows($q);
	$nm=1;
	if($num>0)
	{
	while($r=mysqli_fetch_array($q))
	{
?>
	<tr id="tr<?php echo $nm;?>">
		<th>Chief Complaints</th>
		<td>
			<input list="browsrs" type="text" id="chief<?php echo $nm;?>" value="<?php echo $r['comp_one']; ?>" onkeyup="sel_chief(<?php echo $nm;?>,event)" />
			<datalist id="browsrs">
			<?php
				$qq = mysqli_query($link," SELECT * FROM `complain_master`");
				while($cc=mysqli_fetch_array($qq))
				{
					echo "<option value='$cc[complain]'>";
				}
			?>
			</datalist>
		</td>
		<td>
			<b>For</b> 
			<select id="cc<?php echo $nm;?>" class="span2" onkeyup="sel_chief(<?php echo $nm;?>,event)">
				<option value="0">Select</option>
				<?php
				for($n=1;$n<=30;$n++)
				{
				?>
				<option value="<?php echo $n;?>" <?php if($n==$r['comp_two']){echo "selected='selected'";}?>><?php echo $n;?></option>
				<?php
				}
				?>
			</select>
			<select id="tim<?php echo $nm;?>" class="span2" onkeyup="sel_chief(<?php echo $nm;?>,event)">
				<option value="0">--Select--</option>
				<option value="Minutes" <?php if($r['comp_three']=="Minutes"){echo "selected='selected'";}?>>Minutes</option>
				<option value="Hours" <?php if($r['comp_three']=="Hours"){echo "selected='selected'";}?>>Hours</option>
				<option value="Days" <?php if($r['comp_three']=="Days"){echo "selected='selected'";}?>>Days</option>
				<option value="Week" <?php if($r['comp_three']=="Week"){echo "selected='selected'";}?>>Week</option>
				<option value="Month" <?php if($r['comp_three']=="Month"){echo "selected='selected'";}?>>Month</option>
				<option value="Year" <?php if($r['comp_three']=="Year"){echo "selected='selected'";}?>>Year</option>
			</select>
		</td>
		<td>
		<?php if($nm==1){ ?>
			<span style="float:right"><input type="button" id="addmore" class="btn btn-info" onclick="add_row(1)" value="Add More" <?php echo $dis_all; ?> ></span>
		<?php }else{ if($prev_rec==1){ ?>
			<span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="del_comp('<?php echo $r['slno']; ?>')" ><span class="icon-remove"></span></button></span>
			<?php } } ?>
		</td>
	</tr>
<?php
$nm++;
	}
	}
	else
	{
	?>
	<tr id="tr1">
		<th>Chief Complaints</th>
		<td>
			<input list="browsrs" type="text" id="chief1" value="" onkeyup="sel_chief(1,event)" />
			<datalist id="browsrs">
			<?php
				$qq = mysqli_query($link," SELECT * FROM `complain_master`");
				while($cc=mysqli_fetch_array($qq))
				{
					echo "<option value='$cc[complain]'>";
				}
			?>
			</datalist>
		</td>
		<td>
			<b>For</b> 
			<select id="cc1" class="span2" onkeyup="sel_chief(1,event)">
				<option value="0">Select</option>
				<?php
				for($n=1;$n<=30;$n++)
				{
				?>
				<option value="<?php echo $n;?>"><?php echo $n;?></option>
				<?php
				}
				?>
			</select>
			<select id="tim1" class="span2" onkeyup="sel_chief(1,event)">
				<option value="0">--Select--</option>
				<option value="Minutes">Minutes</option>
				<option value="Hours">Hours</option>
				<option value="Days">Days</option>
				<option value="Week">Week</option>
				<option value="Month">Month</option>
				<option value="Year">Year</option>
			</select>
		</td>
		<td>
			<span style="float:right"><input type="button" id="addmore" class="btn btn-info" onclick="add_row(1)" value="Add More" <?php echo $dis_all; ?> ></span>
		</td>
	</tr>
	<?php
	}
?>
	<tr id="hh">
		<th>History</th>
		<td colspan="3"><textarea style="resize:none;width:96%" name="history" onkeyup="tab(this.id,event)" id="history"></textarea></td>
	</tr>
	<tr>
		<th>Examination</th>
		<td colspan="3"><textarea style="resize:none;width:96%" name="exam" onkeyup="tab(this.id,event)" id="exam"></textarea></td>
	</tr>
	<tr>
		<td colspan="4"><span style="float:right;"><input type="button" id="sav" class="btn btn-info" value="Save" onclick="save_exam()" <?php echo $dis_all; ?> ></span></td>
	</tr>
</table>
<?php
}

if($_POST["type"]=="view_his") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$usr=$_POST['usr'];
	$h=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' ORDER BY `slno` DESC"));
	echo $h['history']."#govinda#".$h['examination']."#govinda#";
}

if($_POST["type"]=="load_diag") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$q=mysqli_query($link,"SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>#</th>
				<th>Diagnosis</th>
				<th>Order</th>
				<th>Certainity</th>
			</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			?>
			<tr>
				<td><?php echo $n;?></td>
				<td><?php echo $r['diagnosis'];?></td>
				<td><?php echo $r['order'];?></td>
				<td><?php echo $r['certainity'];?></td>
			</tr>
			<?php
			$n++;
		}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="load_vital") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$q=mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$r=mysqli_fetch_array($q);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Weight</th><td><?php echo $r['weight'];?> KG</td>
				<th>Height</th><td><?php echo $r['height'];?> CM</td>
<!--
				<th>Mid-arm Circumference</th><td><?php echo $r['medium_circumference'];?></td>
				<th>Head Circumference</th><td><?php echo $r['head_circumference'];?></td>
-->
				<th>BMI</th><td><?php echo $r['BMI_1'].".".$r['BMI_2'];?></td>
				<th>SPO<sub>2</sub></th><td><?php echo $r['spo2'];?></td>
			</tr>
			<tr>
				<th>Pulse</th><td><?php echo $r['pulse'];?></td>
				<th>Temperature</th><td><?php echo $r['temp'];?> <sup>0</sup>C</td>
				<th>RR</th><td><?php echo $r['RR'];?></td>
				<th>PR</th><td><?php echo $r['PR'];?></td>
			</tr>
			<tr>
				<th>BP</th>
				<th>Systolic</th>
				<td><?php echo $r['systolic'];?></td>
				<th>Diastolic</th>
				<td><?php echo $r['diastolic'];?></td>
				<td colspan="3"></td>
			</tr>
		</table>
		<style>
			.table tr:hover{background:none;}
		</style>
		<?php
	}
}

if($_POST["type"]=="load_comp") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$q=mysqli_query($link,"SELECT * FROM `pat_complaints` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>#</th>
				<th>Complaints</th>
				<th>For</th>
			</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			?>
			<tr>
				<td><?php echo $n;?></td>
				<td><?php echo $r['comp_one'];?></td>
				<td><?php echo $r['comp_two']." ".$r['comp_three'];?></td>
			</tr>
			<?php
			$n++;
		}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="load_vital_data") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$usr=$_POST['usr'];
	$h=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	
	echo $h['weight']."#govinda#".$h['height']."#govinda#".$h['medium_circumference']."#govinda#".$h['BMI_1']."#govinda#".$h['BMI_2']."#govinda#".$h['spo2']."#govinda#".$h['pulse']."#govinda#".$h['head_circumference']."#govinda#".$h['PR']."#govinda#".$h['RR']."#govinda#".$h['BP']."#govinda#".$h['temp']."#govinda#".$h['systolic']."#govinda#".$h['diastolic']."#govinda#".$h['note']."#govinda#";
}

if($_POST["type"]=="load_diag_data") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$usr=$_POST['usr'];
	$prev_rec=$_POST['prev_rec'];
	if($prev_rec==0){ $dis_all="disabled"; }else{ $dis_all=""; }
	$q=mysqli_query($link,"SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$num=mysqli_num_rows($q);
	$bt=1;
	if($num>0)
	{
	?>
	<table id="diag_table" class="table table-condensed">
		<?php
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr id="tr<?php echo $bt;?>">
			<th>Diagnosis</th>
			<td>
				<input list="browsrs" type="text" name="diagnosis<?php echo $bt;?>" class="span4" value="<?php echo $r['diagnosis'];?>" onkeyup="diagtab(<?php echo $bt;?>,event)" id="diagnosis1" />
				<datalist id="browsrs">
				<?php
					$qq = mysqli_query($link," SELECT * FROM `diagnosis_master`");
					while($cc=mysqli_fetch_array($qq))
					{
						echo "<option value='$cc[diagnosis]'>";
					}
				?>
				</datalist>
			</td>
			<th>Order</th>
			<td>
				<select id="order<?php echo $bt;?>" onkeyup="diagtab(<?php echo $bt;?>,event)" class="span2">
					<option value="0">--Select--</option>
					<option value="Primary" <?php if($r['order']=="Primary"){echo "selected='selected'";}?>>Primary</option>
					<option value="Secondary" <?php if($r['order']=="Secondary"){echo "selected='selected'";}?>>Secondary</option>
				</select>
			</td>
			<th>Certainity</th>
			<td>
				<select id="cert<?php echo $bt;?>" onkeyup="diagtab(<?php echo $bt;?>,event)" class="span2">
					<option value="0">--Select--</option>
					<option value="Confirmed" <?php if($r['certainity']=="Confirmed"){echo "selected='selected'";}?>>Confirmed</option>
					<option value="Presumed" <?php if($r['certainity']=="Presumed"){echo "selected='selected'";}?>>Presumed</option>
				</select>
			</td>
			<td>
			<?php if($bt==1){ ?>
				<input type="button" id="addmore" class="btn btn-info" onclick="add_row(2)" value="Add More" <?php echo $dis_all; ?> >
			<?php }else{ if($prev_rec==1){ ?>
				<span style="float:right"><button type="button" class="btn btn-mini btn-danger" onclick="del_diag('<?php echo $r['slno'];?>')"><span class="icon-remove"></span></button></span>
			<?php } } ?>
			</td>
		</tr>
		<?php
		$bt++;
		}
		?>
	</table>
	<!--<table class="table table-condensed">
		<tr>
			<td><span style="float:right;"><input type="button" id="sav" class="btn btn-info" value="Save" onclick="save_diag()" <?php echo $dis_all; ?> ></span></td>
		</tr>
	</table>-->
	<?php
	}
	else
	{
	?>
	<table id="diag_table" class="table table-condensed">
		<tr id="tr1">
			<th>Diagnosis</th>
			<td>
				<input list="browsrs" type="text" name="diagnosis1" class="span4" onkeyup="diagtab('1',event)" id="diagnosis1" <?php echo $dis_all?> />
				<datalist id="browsrs">
				<?php
					$qq = mysqli_query($link," SELECT * FROM `diagnosis_master`");
					while($cc=mysqli_fetch_array($qq))
					{
						echo "<option value='$cc[diagnosis]'>";
					}
				?>
				</datalist>
			</td>
			<th>Order</th>
			<td>
				<select id="order1" onkeyup="diagtab('1',event)" class="span2" <?php echo $dis_all?>>
					<option value="0">--Select--</option>
					<option value="Primary">Primary</option>
					<option value="Secondary">Secondary</option>
				</select>
			</td>
			<th>Certainity</th>
			<td>
				<select id="cert1" onkeyup="diagtab('1',event)" class="span2" <?php echo $dis_all?>>
					<option value="0">--Select--</option>
					<option value="Confirmed">Confirmed</option>
					<option value="Presumed">Presumed</option>
				</select>
			</td>
			<td><input type="button" id="addmore" class="btn btn-mini btn-info" onclick="add_row(2)" value="Add More" <?php echo $dis_all?> /></td>
		</tr>
	</table>
	<!--<table class="table table-condensed">
		<tr>
			<td><span style="float:right;"><input type="button" id="sav" class="btn btn-info" value="Save" onclick="save_diag()" /></span></td>
		</tr>
	</table>-->
	<?php
	}
}

if($_POST["type"]=="load_test") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	
	$new_opd=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `opdid_link_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' "));
	if($new_opd["new_opd_id"])
	{
		$new_opd_id=$new_opd["new_opd_id"];
		
		$cl="";
		$dis="";
		
		$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `opd_id`='$new_opd_id'");
	}else
	{
		$new_opd_id=$opd;
		
		$dis='onclick=del_test("'.$r['testid'].'")';
		$cl="icon-remove icon-large";
		
		$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	}
	
	$num=mysqli_num_rows($q);
	if($num>0)
	{
	?>
	<table class="table table-condensed table-bordered table-hover">
		<tr>
			<th>#</th><th colspan="2">Test Name</th>
		</tr>
	<?php
	$n=1;
	while($r=mysqli_fetch_array($q))
	{
		$t=mysqli_fetch_array(mysqli_query($link,"SELECT `testname`,`category_id` FROM `testmaster` WHERE `testid`='$r[testid]'"));
		if($t['category_id']==1)
		{
			if($r["testid"]=="1227") // widal
			{
				$result_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$new_opd_id' AND `batch_no`='$r[batch_no]' "));
			}else
			{
				$result_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$new_opd_id' AND `batch_no`='$r[batch_no]' AND `testid`='$r[testid]'"));
				if($result_num==0)
				{
					$result_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$new_opd_id' AND `batch_no`='$r[batch_no]' AND `testid`='$r[testid]'"));
				}
			}
		}
		if($t['category_id']==2)
		{
			$result_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `opd_id`='$new_opd_id' AND `batch_no`='$r[batch_no]' AND `testid`='$r[testid]'"));
		}
		if($t['category_id']==3)
		{
			$result_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults_card` WHERE `patient_id`='$uhid' AND `opd_id`='$new_opd_id' AND `batch_no`='$r[batch_no]' AND `testid`='$r[testid]'"));
		}
		//~ $dis="";
		//~ if($result_num==0)
		//~ {
			//~ $dis='onclick=del_test("'.$r['testid'].'")';
			//~ $cl="icon-remove icon-large";
		//~ }
		//~ else
		//~ {
			//~ $cl="icon-ban-circle icon-large";
		//~ }
	?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $t['testname'];?></td>
			<td width="10%"><?php if($result_num>0){ ?><button class="btn btn-mini btn-success" onclick="rep_pop('<?php echo $uhid;?>','<?php echo $new_opd_id;?>','','<?php echo $r['batch_no'];?>','<?php echo $r['testid'];?>','<?php echo $t['category_id'];?>')">Result</button><?php }else{ ?><span class="<?php echo $cl;?>" style="color:#a00;cursor:pointer;"  <?php echo $dis; ?>></span><?php }?></td>
		</tr>
	<?php
	$n++;
	}
	?>
	</table>
	<?php
	}
}

if($_POST["type"]=="load_medi") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$prev_rec=$_POST['prev_rec'];
	$q=mysqli_query($link,"SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$opd' AND `type`='1'");
	$num=mysqli_num_rows($q);
	if($num!=0)
	{
	?>
	<table class="table table-condensed table-bordered table-hover">
		<tr>
			<th>#</th><th width="60%">Medicine Name</th><th>Dosage</th><th>Quantity</th><th>Instruction</th><?php if($prev_rec==1){ ?><th>Delete</th><?php } ?>
		</tr>
	<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			$m=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			
			if($r["instruction"]==1)
			$inst="As Directed";
			if($r["instruction"]==2)
			$inst="Before Meal";
			if($r["instruction"]==3)
			$inst="Empty Stomach";
			if($r["instruction"]==4)
			$inst="After Meal";
			if($r["instruction"]==5)
			$inst="In the Morning";
			if($r["instruction"]==6)
			$inst="In the Evening";
			if($r["instruction"]==7)
			$inst="At Bedtime";
			if($r["instruction"]==8)
			$inst="Immediately";
		?>
			<tr>
				<td><?php echo $n;?></td>
				<td><?php echo $m['item_name'];?></td>
				<td><?php echo $r['dosage'];?></td>
				<td><?php echo $r['quantity'];?></td>
				<td><?php echo $inst;?></td>
			<?php if($prev_rec==1){ ?>
				<td><span class="icon-remove icon-large" style="color:#a00;cursor:pointer;" onclick="del_medicine('<?php echo $r['item_code'];?>')"></span></td>
			<?php } ?>
			</tr>
		<?php
		$n++;
		}
	?>
	</table>
	<?php
	}
}

if($_POST["type"]=="load_note") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$usr=$_POST['usr'];
	$nt=mysqli_fetch_array(mysqli_query($link,"SELECT `con_note` FROM `pat_consultation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	echo $nt['con_note'];
}

if($_POST["type"]=="load_print") // gov
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$num1=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	$num2=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_consultation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	$num3=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	if($num1>0 || $num2>0 || $num3>0)
	echo "1";
	else
	echo "0";
}

if($_POST["type"]=="load_test_master_id") // gov
{
	$i=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(testid) as max FROM `testmaster`"));
	echo $i['max']+1;
}

if($_POST["type"]=="load_test_master") // gov
{
	$srch=$_POST['srch'];
	$q=mysqli_query($link,"SELECT * FROM `testmaster` ORDER BY `testid`");
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `testmaster` WHERE `testname` like '$srch%' ORDER BY `testid`");
	}
	
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Test ID</th><th>Name</th><th>Rate</th><th></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $r['testid'];?></td>
			<td width="70%" class="edt" style="cursor:pointer;" onclick="edt(<?php echo $r['testid'];?>)"><?php echo $r['testname'];?></td>
			<td><?php echo $r['rate'];?></td>
			<td>
				<button type="button" class="btn btn-danger btn-mini" onclick="del(<?php echo $r['testid'];?>)" ><i class="icon-remove"></i></button>
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="edit_test_master") // gov
{
	$id=$_POST['id'];
	$t=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `testmaster` WHERE `testid`='$id'"));
	echo $id."@govin@".$t['testname']."@govin@".$t['rate']."@govin@";
}

if($_POST["type"]=="load_medicine_master_id") // gov
{
	$i=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(medicine_id) as max FROM `medicine_master`"));
	echo $i['max']+1;
}

if($_POST["type"]=="load_medicine_master") // gov
{
	$srch=$_POST['srch'];
	$q=mysqli_query($link,"SELECT * FROM `medicine_master` ORDER BY `medicine_id`");
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `medicine_master` WHERE `medicine_name` like '$srch%' ORDER BY `medicine_id`");
	}
	
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Medicine Id</th><th>Name</th><th></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $r['medicine_id'];?></td>
			<td width="70%" class="edt" style="cursor:pointer;" onclick="edt(<?php echo $r['medicine_id'];?>)"><?php echo $r['medicine_name'];?></td>
			<td>
				<button type="button" class="btn btn-danger btn-mini" onclick="del(<?php echo $r['medicine_id'];?>)" ><i class="icon-remove"></i></button>
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="edit_medicine_master") // gov
{
	$id=$_POST['id'];
	$t=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `medicine_master` WHERE `medicine_id`='$id'"));
	echo $id."@govin@".$t['medicine_name']."@govin@";
}

if($_POST["type"]=="stock") // gov
{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from item_master where item_code in(select item_code from ph_stock_master where quantity>0 )  and item_name like '$val%' order by item_name";
	 }
	 else
	 {
	   	 $q="select * from  item_master where item_code in(select item_code from ph_stock_master where quantity>0 ) order by item_name";
	 }
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	?>
	<table class="table table-condensed table-bordered">
	<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
		?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['item_code'];?>')" id="rad_test<?php echo $i;?>">

		<td id="itcd<?php echo $i;?>"><?php echo $qrpdct1['item_id'];?></td>
		<td><?php echo $qrpdct1['item_name'];?></td>
		<td><?php echo $qrpdct1['item_mrp'];?></td>
		</tr>	
		<?php	
		$i++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="item_name") // gov
{
	$pid=$_POST['pid'];
	$qruser=mysqli_fetch_array(mysqli_query($link,"select * from item_master  where item_id='$pid'"));
	$val=$pid.'@'.$qruser['item_name'].'@'.$qruser['item_mrp'];
	echo $val;
}

if($_POST["type"]=="batchload") // gov
{
	$prdctid=$_POST['prdctid'];
	$qpdct=mysqli_query($link,"select * from ph_stock_master where item_code='$prdctid' and quantity>0");
	$qpdct1=mysqli_fetch_array($qpdct);
	$val=$qpdct1['batch_no'].'@'.$qpdct1['batch_no'].'#';
	echo  $val;
}

if($_POST["type"]=="load_sale_id") // gov
{
	$vid=nextId("","ph_sell_master","bill_no","101");
	echo $vid;
}

if($_POST["type"]=="manufactre") // gov
{
	$itmcode=$_POST['itmcode'];
	$btchno=$_POST['btchno'];
	$itmrate=0;
	$qmrp=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from ph_purchase_receipt_details where item_code='$itmcode' and recept_batch='$btchno' order by recpt_date desc"));
	if($qmrp['recpt_mrp']>0)
	{
		$itmrate=$qmrp['recpt_mrp'];
	}
	else
	{
		$mrp1=mysqli_fetch_array(mysqli_query($link,"select item_mrp from ph_item_master where item_code='$itmcode'"));
		$itmrate=$mrp1['item_mrp'];
	} 
	 
	$qpdct1=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_master  where item_code='$itmcode' and batch_no='$btchno' "));
    $val=$qpdct1['mfc_date'].'@'.$qpdct1['exp_date'].'@'.$qpdct1['quantity'].'@'.$itmrate;
	echo  $val;
}

if($_POST["type"]=="loadstockproduct") // gov
{
	$val=$_POST['val'];
	$patient_id=$_POST['patient_id'];
	$pin=$_POST['pin'];
	$indno=$_POST['indno'];
	$typ=$_POST['typ'];
	
	if($val)
	{
		if($patient_id!='0')
		{
			$q="select a.*,b.quantity from item_master a,patient_medicine_detail b where a.item_id=b.item_code and b.`patient_id`='$patient_id' AND b.`pin`='$pin' and b.type='$typ' and b.status=0 and indent_num='$indno'  and a.item_name like '$val%' order by a.item_name";
			
		}else
		{
			$q="select * from item_master where item_id in(select item_code from ph_stock_master where quantity>0 )  and item_name like '$val%' order by item_name";
		}
	}
	else
	{
		if($patient_id!='0')
		{
			
			$q="select a.*,b.quantity from item_master a,patient_medicine_detail b where a.item_id=b.item_code and b.`patient_id`='$patient_id' AND b.`pin`='$pin' and b.type='$typ' and b.status=0 and indent_num='$indno' order by a.item_name";
		}else
		{
			$q="select * from item_master where item_id in(select item_code from ph_stock_master where quantity>0 ) order by item_name";
		}
	}
	
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	?>
	<table class="table table-condensed table-bordered">
	<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
	?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['item_id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_id'];?></td>
		<td><?php echo $qrpdct1['item_name'];?></td>
		<td><?php echo $qrpdct1['quantity'];?></td>
	</tr>	
	<?php	
	$i++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="loaditemtype") // gov
{
	$val=$_POST['val'];
	 if($val)
	 {
		 $q="SELECT * FROM `ph_item_type_master` WHERE `item_type` like '$val%' order by `item_type`";
	 }
	 else
	 {
	   	 $q="SELECT * FROM `ph_item_type_master` order by `item_type`";
	 }
	 ?>
	 <table class="table table-condensed table-bordered">
	 <?php
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct))
		{
	     ?>
		<tr style="cursor:pointer" onclick="javascript:val_load_new('<?php echo $qrpdct1['item_type_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_type_id'];?></td>
			<td class="itmtype"><?php echo $qrpdct1['item_type'];?></td>
			<td>
				<button type="button" class="btn btn-danger btn-mini" onclick="delete_data('<?php echo $qrpdct1[item_type_id];?>')" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-remove"></i></button>
			</td>
		</tr>
         <?php	
		   $i++;
		}
		?>
         </table>
		<?php
}

if($_POST["type"]=="edititemtype") // gov
{
	$rid=$_POST['rid'];
	$qrm=mysqli_query($link,"select * from ph_item_type_master where item_type_id='$rid' ");
	$qrm1=mysqli_fetch_array($qrm);
	$val=$rid.'@'.$qrm1['item_type'];
	echo $val;
}

if($_POST["type"]=="itemtypeid") // gov
{
	$vid=nextId("TYPE","ph_item_type_master","item_type_id","101");
	echo $vid;
}

if($_POST["type"]=="load_item_master") // gov
{
	$val=$_POST['val'];
	 if($val)
	 {
		 $q="SELECT * FROM `item_master` WHERE `item_name` like '$val%' order by `item_name`";
	 }
	 else
	 {
	   	 $q="SELECT * FROM `item_master` order by `item_name`";
	 }
	 ?>
	 <table class="table table-condensed table-bordered">
	 <?php
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct))
		{
	     ?>
		<tr style="cursor:pointer" onclick="val_load_new('<?php echo $qrpdct1['item_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_id'];?></td>
			<td class="itname"><?php echo $qrpdct1['item_name'];?></td>
			<td>
				<button type="button" class="btn btn-danger btn-mini" onclick="delete_data('<?php echo $qrpdct1['item_id'];?>')" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-remove"></i></button>
			</td>
		</tr>
         <?php	
		   $i++;
		}
		?>
         </table>
		<?php
}

if($_POST["type"]=="item_master_id") // gov
{
	$vid=nextId("ITM","item_master","item_id","101");
	echo $vid;
}

if($_POST["type"]=="edit_item_master") // gov
{
	$rid=$_POST['rid'];
	$qrm=mysqli_query($link,"select * from item_master where item_id='$rid' ");
	$qrm1=mysqli_fetch_array($qrm);
	$val=$rid.'@'.$qrm1['item_name'].'@'.$qrm1['generic'].'@'.$qrm1['item_strength'].'@'.$qrm1['item_mrp'].'@'.$qrm1['cost_price'].'@'.$qrm1['vat'].'@'.$qrm1['item_type_id'];
	echo $val;
}

if($_POST["type"]=="purchase_item_list") // load_item()
{
	$val=$_POST['val'];
	if($val)
	{
		$q="SELECT * FROM `item_master` WHERE `item_name` like '$val%' order by `item_name`";
	}
	else
	{
		$q="SELECT * FROM `item_master` order by `item_name`";
	}
	?>
	<table class="table table-condensed table-bordered">
	<?php
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
	?>
		<tr style="cursor:pointer" onclick="val_load_new('<?php echo $qrpdct1['item_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_code'];?></td>
			<td class="itname"><?php echo $qrpdct1['item_name'];?></td>
		</tr>
	<?php	
	$i++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="purchase_load_item")
{
	$id=$_POST['id'];
	$qry=mysqli_query($link,"select * from item_master where item_id='$id' ");
	$res=mysqli_fetch_array($qry);
	$val=$id.'@'.$res['item_name'].'@';
	echo $val;
}

if($_POST["type"]=="purchase_order_id")
{
	$vid=nextId("ORD","ph_purchase_order_master","order_no","101");
	echo $vid;
}

if($_POST["type"]=="load_order_item")
{
	$ord=$_POST['orderno'];
	$qry=mysqli_query($link,"SELECT * FROM `ph_purchase_order_details` WHERE `order_no`='$ord' AND `stat`='0'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
	?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Item Code</th><th>Item Name</th><th>Order Qnt</th><th>Balance Qnt</th>
			</tr>
			<?php
			while($r=mysqli_fetch_array($qry))
			{
				$i=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			?>
			<tr style="cursor:pointer"  onclick="val_load_new('<?php echo $r['item_code'];?>')">
				<td><?php echo $r['item_code'];?></td>
				<td><?php echo $i['item_name'];?></td>
				<td><?php echo $r['order_qnt'];?></td>
				<td><?php echo $r['bl_qnt'];?></td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php
	}
}

if($_POST["type"]=="load_purchase_det")
{
	$id=$_POST['id'];
	$ord=$_POST['ord'];
	$itm=mysqli_fetch_array(mysqli_query($link,"select * from item_master  where item_id='$id' "));
	$or=mysqli_fetch_array(mysqli_query($link,"select * from `ph_purchase_order_details` where `order_no`='$ord' and `item_code`='$id'"));
	$sp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id`='$or[SuppCode]'"));
	$val=$id.'@'.$itm['item_name'].'@'.$itm['item_mrp'].'@'.$or['bl_qnt'].'@'.$sp['name'];
	echo $val;
}

if($_POST["type"]=="purchse_ord_tmp")
{
	$ord=$_POST['orderno'];
	$qry=mysqli_query($link,"SELECT * FROM `ph_purchase_order_details_temp` WHERE `order_no`='$ord'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
	?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Item Code</th><th>Item Name</th><th>Order Qnt</th><th>Remove</th>
			</tr>
			<?php
			while($r=mysqli_fetch_array($qry))
			{
				$i=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			?>
			<tr>
				<td><?php echo $r['item_code'];?></td>
				<td><?php echo $i['item_name'];?></td>
				<td><?php echo $r['order_qnt'];?></td>
				<td><button type="button" class="btn btn-mini btn-danger" onclick="delete_data('<?php echo $r[item_code];?>','<?php echo $r[order_no];?>')"><span class="icon icon-remove"></span></button></td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php
	}
}

if($_POST["type"]=="load_purchase_rcpt_tmp")
{
	$orderno=$_POST['orderno'];
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Order No</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><th>Quantity</th><th>Free</th><th></th>
			</tr>
		<?php
		$qrpdct=mysqli_query($link,"select a.*,b.item_name from ph_purchase_receipt_temp a,item_master b  where a.item_id=b.item_code and a.order_no='$orderno' order by b.item_name ");
		while($qrpdct1=mysqli_fetch_array($qrpdct))
		{
	     ?>
         <tr>
             <td><?php echo $qrpdct1['order_no'];?> </td>
             <td><?php echo $qrpdct1['item_code'];?></td>
             <td><?php echo $qrpdct1['item_name'];?></td>
             <td><?php echo $qrpdct1['recept_batch'];?></td>
             <td><?php echo $qrpdct1['recpt_quantity'];?></td>
             <td><?php echo $qrpdct1['free_qnt'];?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_code'];?>','<?php echo $qrpdct1['recept_batch'];?>','<?php echo $qrpdct1['order_no'];?>','<?php echo $qrpdct1['recpt_quantity'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"> <i class="fa fa-times fa-red" data-toggle="tooltip" data-placement="top" title="Delete"></i></a></td>
         </tr>	
         <?php	
		  }
		 ?>
		 </table>
		 <?php
}

if($_POST["type"]=="balance_receipt")
{
	$blno=$_POST['blno'];
	
	$qpdct=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master  where bill_no='$blno' ")); 
	$aftrdisamt=$qpdct['total_amt']-$qpdct['discount_amt'];
	
	$val=$blno.'@'.$qpdct['customer_name'].'@'.$qpdct['discount_amt'].'@'.$qpdct['total_amt'].'@'.$qpdct['paid_amt'].'@'.$qpdct['balance'].'@'.$qpdct['entry_date'];
     echo  $val;
}

if($_POST["type"]=="load_return_item")
{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from item_master where item_name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from item_master order by item_name";
	 }
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Id</th>
			<th>Name</th>
			<th>MRP</th>
		</tr>
	<?php
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
	?>
	<tr style="cursor:pointer"  onclick="val_load_new('<?php echo $qrpdct1['item_id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_id'];?></td>
		<td><?php echo $qrpdct1['item_name'];?></td>
		<td><?php echo $qrpdct1['item_mrp'];?></td>
	</tr>
	<?php	
	$i++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="load_quant_return_item")
{
	$itm=$_POST['itm'];
	$qry=mysqli_query($link,"select * from item_master where item_id='$itm' ");
	$q=mysqli_fetch_array($qry);
	$val=$itm.'@'.$q['item_name'];
	echo $val;
}

if($_POST["type"]=="lod_batchno")
{
	$itm=$_POST['itm'];
	$qry=mysqli_query($link,"select * from ph_stock_master where item_code='$itm' and quantity>0");
	while($q=mysqli_fetch_array($qry))
	{
	  $val=$q['batch_no'].'@'.$q['batch_no'].'#';
	  echo  $val;
	}
}

if($_POST["type"]=="load_patient_sale")
{
	$uhid=$_POST['uhid'];
	$pat_type=$_POST['pat_type'];
	if($pat_type==2) // opd_id
	{
		$opd=mysqli_fetch_array(mysqli_query($link,"SELECT `opd_id` FROM `uhid_and_opdid` where `patient_id`='$uhid' order by `opd_id` desc limit 0,1"));
		$opd_id=$opd['opd_id'];
		$ipd_id=0;
	}
	if($pat_type==3) // ipd_id
	{
		$ipd=mysqli_fetch_array(mysqli_query($link,"SELECT `ipd_id` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' order by `ipd_id` desc limit 0,1"));
		$ipd_id=$ipd['ipd_id'];
		$opd_id=0;
	}
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name`,`phone` FROM `patient_info` WHERE `patient_id`='$uhid'"));
	$vl=$pat['name']."@".$pat['phone']."@".$opd_id."@".$ipd_id."@";
	echo $vl;
}

if($_POST["type"]=="showselectedsale_product")
{
	$bill=$_POST['billno'];
	$q="select *from ph_sell_details_temp where bill_no='$bill'";
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Id</th>
			<th>Name</th>
			<th>Quantity</th>
			<th>MRP</th>
			<th></th>
		</tr>
	<?php
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name`,`item_mrp` FROM `item_master` WHERE `item_id`='$qrpdct1[item_code]'"));
		$mrp=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp` FROM `ph_purchase_receipt_details` WHERE `item_code`='$qrpdct1[item_code]'"));
	?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['item_code'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_code'];?></td>
		<td><?php echo $itm['item_name'];?></td>
		<td><?php echo $qrpdct1['sale_qnt'];?></td>
		<td><?php echo $mrp['recpt_mrp'];?></td>
		<td><button type="button" class="btn btn-mini btn-danger" onclick="delete_data('<?php echo $qrpdct1[item_code];?>','<?php echo $qrpdct1[batch_no];?>')"><span class="icon icon-remove"></span></button></td>
	</tr>
	<?php	
	$i++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="calculateamt")
{
	$pat_typ=$_POST['pat_typ'];
	$bill=$_POST['billno'];
	
	$disc=mysqli_fetch_array(mysqli_query($link,"SELECT `discount` FROM `ph_sell_type` WHERE `sell_id`='$pat_typ' "));
	
	$qpdct=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amount),0)as maxamt from ph_sell_details_temp  where bill_no='$bill' and user='$emp_id' "));
	$tot=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(net_amount),0)as tot from ph_sell_details_temp  where bill_no='$bill' and user='$emp_id'"));
    $vamt=round($qpdct['maxamt']);
    $vtot=round($tot['tot']);
	$val1=$vamt;
	$val2=$vtot;
	echo  $val1."@@".$val2."@@".$disc['discount'];
}

if($_POST["type"]=="calc_vat")
{
	$bill=$_POST['bill'];
	$vt=0;
	$nt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(net_amount),0)as net from ph_sell_details_temp  where bill_no='$bill'"));
	$t=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amount),0)as tot from ph_sell_details_temp  where bill_no='$bill'"));
	
	$vt=($nt['net']-$t['tot']);
	echo round($vt);
}

if($_POST["type"]=="dtaserach")
{
	$blno=$_POST['blno'];
	
	mysqli_query($link,"delete from ph_sell_details_temp");
	
	mysqli_query($link,"insert into ph_sell_details_temp select * from ph_sell_details where bill_no='$blno'");
	$qpdct=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$blno'"));
	$aftrdisamt=$qpdct['total_amt']-$qpdct['discount_amt'];
	$amtt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amount),0)as tot from ph_sell_details_temp  where bill_no='$blno'"));
	$net=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(net_amount),0)as net from ph_sell_details_temp  where bill_no='$blno'"));
	$vat=round($net['net']-$amtt['tot']);
	
	$val=$blno.'@'.$qpdct['entry_date'].'@'.$qpdct['customer_name'].'@'.$qpdct['customer_phone'].'@'.$amtt['tot'].'@'.$qpdct['discount_perchant'].'@'.val_con(round($aftrdisamt)).'@'.val_con(round($qpdct['paid_amt'])).'@'.$qpdct['balance'].'@'.$qpdct['total_amt'].'@'.$vat;
     echo  $val;
}

if($_POST["type"]=="load_supp_id")
{
	$vid=nextId("SUP","ph_supplier_master","id","101");
	echo $vid;
}

if($_POST["type"]=="load_supp_list")
{
	$val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from ph_supplier_master where name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from ph_supplier_master order by name";
	 }
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Supplier Id</th>
			<th>Name</th>
			<th></th>
		</tr>
	<?php
	$qry=mysqli_query($link,$q);
	$i=1;
	while($r=mysqli_fetch_array($qry))
	{
	?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $r['id'];?>')">
		<td><?php echo $i;?></td>
		<td><?php echo $r['id'];?></td>
		<td><?php echo $r['name'];?></td>
		<td></td>
	</tr>
	<?php	
	$i++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="load_supp_det")
{
	$id=$_POST['id'];
	$sup=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_supplier_master` WHERE `id`='$id'"));
	$vl=$id."#gov#".$sup['name']."#gov#".$sup['address']."#gov#".$sup['phone_no']."#gov#".$sup['email_add']."#gov#".$sup['gst_no']."#gov#".$sup['dl_no'];
	echo $vl;
}

if($_POST["type"]=="load_order_report")
{
	$ord=$_POST['ord'];
	$s=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id` IN (SELECT `SuppCode` FROM `ph_purchase_order_master` WHERE `order_no`='$ord')"));
	$q=mysqli_query($link,"SELECT * FROM `ph_purchase_order_details` WHERE `order_no`='$ord'");
	$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `order_date` FROM `ph_purchase_order_master` WHERE `order_no`='$ord'"));
	?>
	<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ord_rep_prr('<?php echo $ord;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th colspan="3">Supplier : <?php echo $s['name'];?></th>
			<th>Date : <?php echo $dt['order_date'];?></th>
		</tr>
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th width="60%">Item Name</th>
			<th>Quantity</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['order_qnt'];?></td>
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_receive_report")
{
	$ord=$_POST['ord'];
	$s=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id` IN (SELECT `SuppCode` FROM `ph_purchase_order_master` WHERE `order_no`='$ord')"));
	$q=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_details` WHERE `order_no`='$ord'");
	$n=mysqli_num_rows($q);
	?>
	<button type="button" class="btn btn-default" onclick="rcv_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="rcv_rep_prr('<?php echo $ord;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th colspan="7">Supplier : <?php echo $s['name'];?></th>
		</tr>
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th width="40%">Item Name</th>
			<th>Order</th>
			<th>Receive</th>
			<th>Date</th>
			<th>Bill No</th>
		</tr>
		<?php
		$i=1;
		$bill='';
		$dt='';
		while($r=mysqli_fetch_array($q))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			$o=mysqli_fetch_array(mysqli_query($link,"SELECT `order_qnt` FROM `ph_purchase_order_details` WHERE `item_code`='$r[item_code]' AND `order_no`='$ord'"));
			$bl=mysqli_query($link,"SELECT `recpt_date`,`bill_no` FROM `ph_purchase_receipt_master` WHERE `order_no`='$r[order_no]' AND `supp_code`='$r[SuppCode]'");
			while($b=mysqli_fetch_array($bl))
			{
				$bill.=$b['bill_no']."<br/>";
				$dt.=$b['recpt_date']."<br/>";
			}
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $o['order_qnt'];?></td>
			<td><?php echo $r['recpt_quantity'];?></td>
			<?php if($n!="0"){echo "<td rowspan='".$n."'>".$dt."</td>";}?>
			<?php if($n!="0"){echo "<td rowspan='".$n."'>".$bill."</td>";}?>
		</tr>
		<?php
		$n=0;
		$i++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_sale_report")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="sale_rep_prr('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Type</th>
			<th>Customer Name</th>
			<th>Amount</th>
			<th>Discount</th>
			<th>Adjust. </th>
			<th>Paid</th>
			<th>Balance</th>
			<th>Date</th>
			<th>User</th>
		</tr>
		<?php
		$n=1;
		$p_tot=0;
		$q=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate'");
		while($r=mysqli_fetch_array($q))
		{
			$qemp=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			$qtype=mysqli_fetch_array(mysqli_query($link,"select sell_name from ph_sell_type where sell_id='$r[pat_type]'"));
			$rrr=mysqli_fetch_array(mysqli_query($link,"SELECT sum(`total_amount`) as ttt FROM `ph_sell_details` WHERE `bill_no`='$r[bill_no]' "));
			$vttl=$vttl+$r['total_amt'];
			$vpaid=$vpaid+$r['paid_amt'];
			$vdis=$vdis+$r['discount_amt'];
			$vadjsut=$vadjsut+$r['adjust_amt'];
			$vbal=$vbal+$r['balance'];
			$vipd="";
			if($r['ipd_id']!=="")
			{
				$vipd=" / ".$r['ipd_id'];
			}
			
			
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo $qtype['sell_name'];?><?php //echo $rrr['ttt'];?></td>
			<td><?php echo $r['customer_name'].$vipd;?></td>
			<td><?php echo $r['total_amt'];?></td>
			<td><?php echo val_con(round($r['discount_amt']));?></td>
			<td><?php echo val_con(round($r['adjust_amt']));?></td>
			<td><?php echo val_con(round($r['paid_amt']));?></td>
			<td><?php echo $r['balance'];?></td>
			<td><?php echo convert_date_ch($r['entry_date']);?></td>
			<td><?php echo $qemp['name'];?></td>
		</tr>
		<?php
		$p_tot+=$rrr['ttt'];
		$n++;
		}
		?>
		<tr>
			<th colspan="4" style="text-align:right">Total <?php //echo $p_tot;?></th>
			<th><?php echo number_format($vttl,2);?></th>
			<th><?php echo number_format($vdis,2);?></th>
			<th><?php echo number_format($vadjsut,2);?></th>
			<th><?php echo number_format($vpaid,2);?></th>
			<th><?php echo number_format($vbal,2);?></th>
			<th></th>
			<th></th>
		</tr>
	</table>
	<?php
}



if($_POST["type"]=="load_sale_credit")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="sale_rep_credit('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Customer Name</th>
			<th>Amount</th>
			<th>Discount (Rs)</th>
			<th>Paid</th>
			<th>Balance</th>
			<th>Date</th>
		</tr>
		<?php
		
		$q1=mysqli_query($link,"select distinct a.pat_type,b.sell_name from ph_sell_master a, ph_sell_type b where a.pat_type=b.sell_id and a.`entry_date` BETWEEN '$fdate' AND '$tdate' and a.balance>0 ");
		while($q2=mysqli_fetch_array($q1))
		{
			$n=1;
		 ?>
		 <tr>
			<td>&nbsp;</td>
		 </tr>
		 <?php	
		$q=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' and balance>0 ");
		while($r=mysqli_fetch_array($q))
		{
			$vttl=$vttl+$r['total_amt'];
			  $vttldis=$vttldis+$r['discount_amt'];
			  $vttlpaid=$vttlpaid+$r['paid_amt'];
			  $vttlbl=$vttlbl+$r['balance'];
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo $r['customer_name'];?></td>
			<td><?php echo $r['total_amt'];?></td>
			<td><?php echo val_con(round($r['discount_amt']));?></td>
			<td><?php echo val_con(round($r['paid_amt']));?></td>
			<td><?php echo $r['balance'];?></td>
			<td><?php echo convert_date_ch($r['entry_date']);?></td>
		</tr>
		<?php
		$n++;
		}
		?>
		 <tr >
					<td align="right" colspan="3" style="font-weight:bold;font-size:13px">Total</td>
					<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttl,2);?></td>
					<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttldis,2);?></td>
					<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttlpaid,2);?></td>
					<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttlbl,2);?></td>
					<td></td>
             </tr>
		<?php
	  }?>
	</table>
	<?php
}


if($_POST["type"]=="load_sale_costprice")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$fid=0;
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="sale_rep_costprice('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th>Qnty.</th>
			<th>MRP</th>
			<th>Cost Price</th>
			<th>MRP Amount</th>
			<th>Cost Amount</th>
			<th>Profit</th>
		</tr>
		<?php
		$n=1;
		
		$q=mysqli_query($link,"select distinct a.item_code,b.item_name from ph_sell_details a,item_master b where a.item_code=b.item_id and a.entry_date between'$fdate' and '$tdate'   order by  b.item_name");
		
		while($r=mysqli_fetch_array($q))
		{
			$itmqnt=mysqli_fetch_array(mysqli_query($link,"select sum(sale_qnt)as maxqnt,sum(free_qnt)as maxfree from ph_sell_details where entry_date between'$fdate' and '$tdate' and  FID='$fid' and item_code='$r[item_code]' "));

			$qrate=mysqli_fetch_array(mysqli_query($link,"select mrp from ph_sell_details where item_code='$r[item_code]' "));
			$qcstprice=mysqli_fetch_array(mysqli_query($link,"select recept_cost_price from ph_purchase_receipt_details where item_code='$r[item_code]' order by slno desc limit 0,1 "));

			$vitmamt=$itmqnt['maxqnt']*$qrate['mrp'];
			$vitcostmamt=$itmqnt['maxqnt']*$qcstprice['recept_cost_price'];
			$vprofitamt=$vitmamt-$vitcostmamt;
			$vamount=$vamount+$vitmamt;


			$rsnttl=$rsnttl+$vitmamt;
			$vttlcstamt=$vttlcstamt+$vitcostmamt;
			$vttlprofit=$vttlprofit+$vprofitamt;
					
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $itmqnt['maxqnt'];?></td>
			<td><?php echo $qrate['mrp'];?></td>
			<td><?php echo $qcstprice['recept_cost_price'];?></td>
			<td><?php echo round($vitmamt,0);?></td>
			<td><?php echo round($vitcostmamt,0);?></td>
			<td><?php echo round($vprofitamt,0);?></td>
		</tr>
		<?php
		$n++;
		}
			
		?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>Total Amount</td>
			<td><?php echo round($rsnttl,0);?></td>
			<td><?php echo round($vttlcstamt,0);?></td>
			<td><?php echo round($vttlprofit,0);?></td>
		</tr>
	</table>
	<?php
}



if($_POST["type"]=="load_item_expiry")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	if($fdate==$tdate)
	{
	  $fdate=date('Y-m-d');
      $tdate=date('Y-m-d', strtotime($fdate. ' + 90 days'));
	}

	
	$fid=0;
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="item_expiry_rep('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>MRP</th>
			<th>Batch No</th>
			<th>Cl. Stk</th>
			<th>Expiry Date</th>
			<th>Reciept No.</th>
			<!--<th>Bill No.</th>
			<th>Supplier</th>-->
		</tr>
		<?php
		$n=1;
		
		$q=mysqli_query($link,"SELECT distinct(a.item_code),b.item_name FROM ph_stock_master a,item_master  b WHERE a.item_code=b.item_id and  a.quantity>0 and a.substore_id='1' and a.exp_date between '$fdate' and '$tdate' ORDER BY b.item_name");
		while($r=mysqli_fetch_array($q))
		{
			$vstk=0;
			$itmttlamt=0;
			$qmrp=mysqli_fetch_array(mysqli_query($link,"select mrp,cost_price from item_master where item_id ='$r[item_code]'"));	
					
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $qmrp['mrp'];?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			
			
		</tr>
		
               <?php
			    $vrcprt=0;
			    $qbatch=mysqli_query($link,"select * from ph_stock_master  where item_code='$r[item_code]' and  quantity>0 and substore_id='1' and exp_date between '$fdate' and '$tdate'");
				while($qbatch1=mysqli_fetch_array($qbatch)){
				
				$rec=mysqli_fetch_array(mysqli_query($link,"SELECT `order_no`,`bill_no`,SuppCode FROM `ph_purchase_receipt_details` WHERE `item_code`='$qbatch1[item_code]' AND `expiry_date`='$qbatch1[exp_date]' AND `recept_batch`='$qbatch1[batch_no]'"));
				
				$qmrp=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,SuppCode FROM `inv_main_stock_received_detail` WHERE `item_id`='$qbatch1[item_code]'  AND `recept_batch`='$qbatch1[batch_no]'"));
				
								
				$qsupplier=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$qmrp[SuppCode]' "));
				
				
				$vrcptamt=$qmrp['cost_price']*$qbatch1['quantity'];
				$itmttlamt=$itmttlamt+$vrcptamt;
				$vttlamt=$vttlamt+$vrcptamt;
				$vstk=$vstk+$qbatch1['quantity'];
				$tr_style='';
				if($qbatch1['exp_date']<=$date)
				{
					
					$tr_style='style="cursor:pointer;background-color: red;"';
				}
				?>	
				<tr <?php echo $tr_style;?> >  
               
                    <td></td> 
                    <td></td> 
                    <td align="right"></td>
                     <td align="right" style="font-size:12px"><?php echo $qmrp['recpt_mrp'];?></td>
                    <td align="right" style="font-size:12px"><?php echo $qbatch1['batch_no'];?></td>
                    <td align="right" style="font-size:12px"><?php echo $qbatch1['quantity'];?></td>
                    <td align="right" style="font-size:12px"><?php echo convert_date($qbatch1['exp_date']);?></td>
					<td><?php echo $rec['order_no'];?></td>
					<!--<td><?php echo $rec['bill_no'];?></td>
                    <td><?php echo $qsupplier['name'];?></td>-->
                  </tr> 
                  <?php
					 ;}?>
                  
		<?php
		$n++;
		}
			
		?>
		
	</table>
	<?php
}



if($_POST["type"]=="load_sale_det")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<button type="button" class="btn btn-default" onclick="sale_rep_det_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="sale_rep_det_prr('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Customer Name</th>
			<th>Item Details</th>
			<th>Rate</th>
			<th>Quantity</th>
			<th>MRP</th>
			<th>Cost Price</th>
			<th>GST (Rs)</th>
			<th>Net Amount(Round)</th>
			<th>Date</th>
		</tr>
		<?php
		$n=1;
		$qbilltype=mysqli_query($link,"SELECT DISTINCT `bill_type_id` FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' order by bill_type_id");
		while($qbilltype1=mysqli_fetch_array($qbilltype))
		{
			if($qbilltype1['bill_type_id']==1)
			{
				$vbltype="Cash";
			}
			else
			{
				$vbltype="Credit";
			}
			?>
			<tr>
				<td colspan="11" style="font-weight:bold">Bill Type : <?php echo $vbltype;?></td>
			</tr>
			<?php
		$qry=mysqli_query($link,"SELECT DISTINCT `bill_no` FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' and bill_type_id='$qbilltype1[bill_type_id]'");
		while($res=mysqli_fetch_array($qry))
		{
			$q=mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_no`='$res[bill_no]'");
			$num=mysqli_num_rows($q);
			while($r=mysqli_fetch_array($q))
			{
				$vcsrprice=$r['sale_qnt']*$r['item_cost_price'];
				$cus=mysqli_fetch_array(mysqli_query($link,"SELECT `customer_name`,`total_amt` FROM `ph_sell_master` WHERE `bill_no`='$r[bill_no]'"));
				//$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
				$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
		?>
		<tr>
			<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$r['bill_no']."</td><td rowspan='".$num."'>".$cus['customer_name']."</td>";}?>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['mrp'];?></td>
			<td><?php echo $r['sale_qnt'];?></td>
			<td><?php echo $r['total_amount'];?></td>
			<td><?php echo $vcsrprice;?></td>
			<td><?php echo val_con($r['gst_amount']);?></td>
			<td><?php echo val_con(round($r['net_amount']));?></td>
			<?php if($num>0){echo "<td rowspan='".$num."'>".convert_date_ch($r['entry_date'])."</td>";}?>
		</tr>
		<?php
			$num=0;
			}
			$n++;
		?>
		<tr>
			<td colspan="7"></td>
			<td colspan="2">Total</td>
			<td colspan="2"><?php echo $cus['total_amt'];?></td>
		</tr>
		<tr>
			<td colspan="11" style="background:#ccc;"></td>
		</tr>
		<?php
		}
		?>
		
		<?php
	}?>
	</table>
	<?php
}

if($_POST["type"]=="load_sale_item_rep")
{
$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<button type="button" class="btn btn-default" onclick="sale_item_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="sale_item_rep_prr('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Sale</th>
			<th>Pharmacy Stock</th>
			<th>Central Stock</th>
			<th>Current Stock</th>
		</tr>
		<?php
		$n=1;
		$q=mysqli_query($link,"SELECT DISTINCT a.`item_code`,b.item_name  FROM `ph_sell_details` a,item_master b WHERE a.entry_date  BETWEEN '$fdate' AND '$tdate' and a.item_code=b.item_id order by b.item_name");
		while($r=mysqli_fetch_array($q))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			$bch=mysqli_fetch_array(mysqli_query($link,"SELECT `batch_no` FROM `ph_stock_process` WHERE `item_code`='$r[item_code]'"));
			$add=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(added),0)as adds FROM `ph_stock_process` WHERE `item_code`='$r[item_code]'"));
			
			$sell=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(sale_qnt),0)as sells,ifnull(sum(free_qnt),0)as maxfree FROM `ph_sell_details` WHERE `item_code`='$r[item_code]' and entry_date between '$fdate' AND '$tdate'"));
			
			$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(`quantity`),0) as maxph FROM `ph_stock_master` WHERE `item_code`='$r[item_code]' and substore_id='1' and quantity>0 "));
			
			$mainstk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(`closing_stock`),0) as maxcntrl FROM `inv_maincurrent_stock` WHERE `item_id`='$r[item_code]' and  closing_stock>0 "));
			
			$vsalqnt=$sell['sells']+$sell['maxfree'];
			$vttlstk=$stk['maxph']+$mainstk['maxcntrl'];
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $vsalqnt;?></td>
			<td><?php echo $stk['maxph'];?></td>
			<td><?php echo $mainstk['maxcntrl'];?></td>
			<td><?php echo $vttlstk;?></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_sale_item_det")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<button type="button" class="btn btn-default" onclick="sale_item_det_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="sale_item_det_prr('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Date</th>
			<th>Open</th>
			<th>Added</th>
			<th>Sale</th>
			<th>Close</th>
		</tr>
	<?php
	$n=1;
	$qry=mysqli_query($link,"SELECT DISTINCT `item_code` FROM `ph_stock_process` WHERE `sell`!='0' AND `date` BETWEEN '$fdate' AND '$tdate'");
	while($res=mysqli_fetch_array($qry))
	{
		$q=mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `item_code`='$res[item_code]' AND `date` BETWEEN '$fdate' AND '$tdate'");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
		?>
		<tr>
			<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$r['item_code']."</td><td rowspan='".$num."'>".$itm['item_name']."</td><td rowspan='".$num."'>".$r['batch_no']."</td>";}?>
			<td><?php echo $r['date'];?></td>
			<td><?php echo $r['s_available'];?></td>
			<td><?php echo $r['added'];?></td>
			<td><?php echo $r['sell'];?></td>
			<td><?php echo $r['s_remain'];?></td>
		</tr>
		<?php
		$num=0;
		}
		?>
		<tr>
			<td colspan="9" style="background:#ccc;"></td>
		</tr>
		<?php
		$n++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="load_return_item_report")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<button type="button" class="btn btn-default" onclick="ret_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ret_rep_prr('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Date</th>
			<th>Quantity</th>
		</tr>
	<?php
	$n=1;
	$qry=mysqli_query($link,"SELECT DISTINCT `item_code` FROM `ph_item_return_master` WHERE `return_date` BETWEEN '$fdate' AND '$tdate'");
	while($res=mysqli_fetch_array($qry))
	{
		$q=mysqli_query($link,"SELECT * FROM `ph_item_return_master` WHERE `item_code`='$res[item_code]' AND `return_date` BETWEEN '$fdate' AND '$tdate'");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
		?>
		<tr>
			<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$r['item_code']."</td><td rowspan='".$num."'>".$itm['item_name']."</td><td rowspan='".$num."'>".$r['batch_no']."</td>";}?>
			<td><?php echo $r['return_date'];?></td>
			<td><?php echo $r['return_qnt'];?></td>
		</tr>
		<?php
		$num=0;
		}
	$n++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="item_wise_aval_report")
{
	?>
	<button type="button" class="btn btn-default" onclick="stk_exp()">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="stk_prr()">Print</button>
	<table class="table table-condensed table-bordered" id="stk_tbl">
		<tr>
			<th>#</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><th>MFG Date</th><th>Exp Date</th><th>Available Stock</th>
		</tr>
	<?php
	$i=1;
	$qry=mysqli_query($link,"SELECT DISTINCT `item_code` FROM `ph_stock_master` WHERE `quantity`>0");
	while($res=mysqli_fetch_array($qry))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$res[item_code]'"));
		$q=mysqli_query($link,"SELECT * FROM `ph_stock_master` WHERE `item_code`='$res[item_code]' AND `quantity`>0");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
	?>
		<tr>
			<?php
			if($num>0)
			{
				echo "<td rowspan='".$num."'>$i</td>";
				echo "<td rowspan='".$num."'>".$res['item_code']."</td>";
				echo "<td rowspan='".$num."'>".$itm['item_name']."</td>";
			}
			?>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo ($r['mfc_date']);?></td>
			<td><?php echo ($r['exp_date']);?></td>
			<td><?php echo $r['quantity'];?></td>
		</tr>
	<?php
		$num=0;
		}
		$i++;
	}
	?>
	</table>
	<style>
		.table tr:hover
		{
			background:none;
		}
	</style>
	<?php
}

if($_POST["type"]=="item_short_report")
{
	?>
	<button type="button" class="btn btn-default" onclick="stk_exp()">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="stk_prr()">Print</button>
	<table class="table table-condensed table-bordered" id="stk_tbl">
		<tr>
			<th>#</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><th>MFG Date</th><th>Exp Date</th><th>Available Stock</th>
		</tr>
	<?php
	$i=1;
	$qry=mysqli_query($link,"SELECT DISTINCT `item_code` FROM `ph_stock_master` WHERE `quantity`<10");
	while($res=mysqli_fetch_array($qry))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$res[item_code]'"));
		$q=mysqli_query($link,"SELECT * FROM `ph_stock_master` WHERE `item_code`='$res[item_code]' AND `quantity`<10");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
	?>
		<tr>
			<?php
			if($num>0)
			{
				echo "<td rowspan='".$num."'>$i</td>";
				echo "<td rowspan='".$num."'>".$res['item_code']."</td>";
				echo "<td rowspan='".$num."'>".$itm['item_name']."</td>";
			}
			?>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo ($r['mfc_date']);?></td>
			<td><?php echo ($r['exp_date']);?></td>
			<td><?php echo $r['quantity'];?></td>
		</tr>
	<?php
		$num=0;
		}
		$i++;
	}
	?>
	</table>
	<style>
		.table tr:hover
		{
			background:none;
		}
	</style>
	<?php
}

if($_POST["type"]=="stock_entry")
{
	$val=$_POST['val'];
	if($val)
	{
		$q="select * from ph_item_master where item_name like '$val%' order by item_name";
	}
	else
	{
		$q="select * from ph_item_master order by item_name";
	}
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	?>
	<table class="table table-condensed table-bordered">
		<?php
		while($qrpdct1=mysqli_fetch_array($qrpdct))
		{
		?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['item_code'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_code'];?></td>
			<td width="70%"><?php echo $qrpdct1['item_name'];?></td>
			<td><?php echo $qrpdct1['item_mrp'];?></td>
		</tr>	
		<?php	
		$i++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="stock_item_load")
{
	$id=$_POST['id'];
	$vl=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name`,gst_percent FROM `ph_item_master` WHERE `item_code`='$id'"));
	echo $id."#g#".$vl['item_name']."#g#".$vl['gst_percent'];
}

if($_POST["type"]=="view_disp")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$prev_rec=$_POST['prev_rec'];
	if($prev_rec==0){ $dis_all="disabled"; }else{ $dis_all=""; }
	$app=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid'"));
	if($app>1)
	{
		$prev_his=1;
	}
	else
	{
		$prev_his=0;
	}
	$usr=$_POST['usr'];
	$sel="";
	$sell="";
	$q=mysqli_query($link,"SELECT * FROM `pat_disposition` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$qq=mysqli_query($link,"SELECT * FROM `pat_confidential` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$num=mysqli_num_rows($q);
	$nn=mysqli_num_rows($qq);
	if($num>0)
	{
		$r=mysqli_fetch_array($q);
	}
	if($nn>0)
	{
		$rr=mysqli_fetch_array($qq);
	}
	?>
	<table id="disp_table" class="table table-condensed">
		<?php
		if($r['ref_opd']=="")
		{
			$sel_dis="";
		}
		else
		{
			$sel_dis="disabled='disabled'";
		}
		
		if($r['disposition']==2)
		{
			$shw="";
		}
		else
		{
			$shw="style='display:none;'";
		}
		?>
		<tr>
			<th width="20%">Disposition</th>
			<td>
				<select id="discom" onkeyup="tab(this.id,event)" onchange="load_refer()" <?php echo $sel_dis;?> <?php echo $dis_all;?>>
					<option value="0">Select</option>
					<option value="1" <?php if($r['disposition']==1){echo "selected=selected";}?>>Admit Patient</option>
					<option value="2" <?php if($r['disposition']==2){echo "selected=selected";}?>>Refer</option>
				</select>
			</td>
			<!--<td><span style="float:right;"><input type="button" id="sav_dsp" class="btn btn-info" value="Save" onclick="save_disp()" <?php echo $dis_all; ?> ></span></td>-->
		</tr>
		<tr id="ref_tr" <?php echo $shw;?>>
			<th>Refer to</th>
			<td>
				<input type="text" class="span5" id="ref_to" value="<?php echo $r['ref_doctor_to'];?>" placeholder="Refer Doctor / Refer Hospital" />
			</td>
		</tr>
		<tr>
			<th>Confidential</th>
			<td>
				<select id="confident" onkeyup="tab(this.id,event)" <?php echo $dis_all?>>
					<option value="0">No</option>
					<option value="1" <?php if($rr['confident']==1){echo "selected='selected'";}?>>Yes</option>
				</select>
			</td>
			<!--<td>
				<span style="float:right;"><input type="button" id="sav_conf" class="btn btn-info" value="Save" onclick="save_conf()"></span>
			</td>-->
		</tr>
	</table>
	<input type="hidden" id="prev_his" value="<?php echo $prev_his;?>" />
	<script>if($("#prev_his").val().trim()==0){$('#next').hide();$('#save').show();}else{$('#next').show();$('#save').hide();}</script>
	<?php
}

if($_POST["type"]=="load_ind_type_id")
{
	$vid=nextId("TYPE","inv_indent_type","type_id","1");
	echo $vid;
}

if($_POST["type"]=="load_ind_type")
{
	$srch=$_POST['srch'];
	if($srch)
	$qry=mysqli_query($link,"SELECT * FROM `inv_indent_type` WHERE `name` like '$srch%' ORDER BY `name`");
	else
	$qry=mysqli_query($link,"SELECT * FROM `inv_indent_type` ORDER BY `name`");
	?>
	<table id="" class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Type Id</th>
			<th width="60%">Name</th>
			<th><span class="icon-trash icon-large" style="color:#c00;"></span></th>
		</tr>
	<?php
		$n=1;
		while($r=mysqli_fetch_array($qry))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['type_id'];?></td>
			<td class="nm" onclick="edit('<?php echo $r['sl_no'];?>')"><?php echo $r['name'];?></td>
			<td><span class="icon-remove icon-large" style="color:#c00;cursor:pointer;" onclick="del('<?php echo $r['sl_no'];?>')"></span></td>
		</tr>
		<?php
		$n++;
		}
	?>
	</table>
	<?php
}

if($_POST["type"]=="load_ind_type_details")
{
	$sl=$_POST['sl'];
	$val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `inv_indent_type` WHERE `sl_no`='$sl'"));
	echo $sl."#gov#".$val['type_id']."#gov#".$val['name']."#gov#";
}

if($_POST["type"]=="load_ind")
{
	$srch=$_POST['srch'];
	if($srch)
	$qry=mysqli_query($link,"SELECT * FROM `inv_indent_master` WHERE `name` like '$srch%' ORDER BY `name`");
	else
	$qry=mysqli_query($link,"SELECT * FROM `inv_indent_master` ORDER BY `name`");
	?>
	<table id="" class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th width="80%">Name</th>
			<th><span class="icon-trash icon-large" style="color:#c00;"></span></th>
		</tr>
	<?php
		$n=1;
		while($r=mysqli_fetch_array($qry))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td class="nm" onclick="edit('<?php echo $r['id'];?>')"><?php echo $r['name'];?></td>
			<td><span class="icon-remove icon-large" style="color:#c00;cursor:pointer;" onclick="del('<?php echo $r['id'];?>')"></span></td>
		</tr>
		<?php
		$n++;
		}
	?>
	</table>
	<?php
}

if($_POST["type"]=="load_ind_details")
{
	$id=$_POST['id'];
	$val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `inv_indent_master` WHERE `id`='$id'"));
	echo $id."#gov#".$val['indent_type']."#gov#".$val['name']."#gov#".$val['vat']."#gov#".$val['specific_type']."#gov#";
}

if($_POST["type"]=="load_ind_supplier")
{
	$srch=$_POST['srch'];
	$ph=$_POST['ph'];
	if($srch)
	$qry=mysqli_query($link,"SELECT * FROM `inv_supplier_master` WHERE Status='$ph' and `name` like '$srch%' ORDER BY `name`");
	else
	$qry=mysqli_query($link,"SELECT * FROM `inv_supplier_master` where Status='$ph' ORDER BY `name`");
	?>
	<table id="" class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th width="80%">Name</th>
			<th><span class="icon-trash icon-large" style="color:#c00;"></span></th>
		</tr>
	<?php
		$n=1;
		while($r=mysqli_fetch_array($qry))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td class="nm" onclick="edit('<?php echo $r['id'];?>')"><?php echo $r['name'];?></td>
			<td><span class="icon-remove icon-large" style="color:#c00;cursor:pointer;" onclick="del('<?php echo $r['id'];?>')"></span></td>
		</tr>
		<?php
		$n++;
		}
	?>
	</table>
	<?php
}

if($_POST["type"]=="load_ind_supp_details")
{
	$id=$_POST['id'];
	$val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `inv_supplier_master` WHERE `id`='$id'"));
	echo $id."#gov#".$val['name']."#gov#".$val['contact']."#gov#".$val['contact_person']."#gov#".$val['email']."#gov#".$val['fax']."#gov#".$val['address']."#gov#".$val['gst_no']."#gov#".$val['bank_id']."#gov#".$val['bank_ac_no']."#gov#".$val['branch']."#gov#".$val['ifsc_code']."#gov#".$val['sup_condition']."#gov#".$val['sup_condition_2']."#gov#".$val['igst'];
}

if($_POST["type"]=="load_inv_items")
{
	$srch=$_POST['srch'];
	if($srch)
	$qry=mysqli_query($link,"SELECT * FROM `inv_indent_master` WHERE `name` like '$srch%' ORDER BY `name`");
	else
	$qry=mysqli_query($link,"SELECT * FROM `inv_indent_master` ORDER BY `name`");
	?>
	<table id="" class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th width="80%">Name</th>
		</tr>
	<?php
		$n=1;
		while($r=mysqli_fetch_array($qry))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td class="nm" onclick="sel('<?php echo $r['id'];?>','<?php echo $r['name'];?>')"><?php echo $r['name'];?></td>
		</tr>
		<?php
		$n++;
		}
	?>
	</table>
	<?php
}

if($_POST["type"]=="load_generic")
{
	$id=$_POST['id'];
	$gen=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `generic` WHERE `id`='$id'"));
	echo $gen['name'];
}

if($_POST["type"]=="pat_ipd_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$dt=mysqli_query($link,"SELECT DISTINCT `date` FROM `ipd_pat_diagnosis` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `date` DESC");
	$num=mysqli_num_rows($dt);
	if($num>0)
	{
		while($res=mysqli_fetch_array($dt))
		{
			$q=mysqli_query($link,"SELECT * FROM `ipd_pat_diagnosis` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$res[date]'");
			?>
			<span><b>Date: <?php echo $res['date'];?></b></span>
			<table id="" class="table table-condensed table-bordered">
				<tr>
					<th width="50%">Diagnosis</th><th>Order</th><th>Certainity</th><th>Diagnosed By</th>
				</tr>
			<?php
			while($r=mysqli_fetch_array($q))
			{
				$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$r[consultantdoctorid]'"));
			?>
				<tr>
					<td><?php echo $r['diagnosis'];?></td><td><?php echo $r['order'];?></td><td><?php echo $r['certainity'];?></td><td><?php echo $doc['Name'];?></td>
				</tr>
			<?php
			}
			?>
			</table>
			<?php
		}
		?>
		<button type="button" class="btn btn-info" id="add" onclick="addd()" style=""><i class="icon-plus"></i> Add More</button>
		<?php
	}
	else
	{
		?>
		<button type="button" class="btn btn-info" id="add" onclick="addd()" style=""><i class="icon-plus"></i> Add</button>
		<?php
	}
}

if($_POST["type"]=="ipd_pat_add_diag")
{
	?>
	<table id="diag_table" class="table table-condensed table-bordered">
		<tr>
			<th>Diagnosis</th>
			<th>Order</th>
			<th>Certainity</th>
			<th>Diagnosed By</th>
			<th></th>
		</tr>
		<tr id="tr1">
			<td>
				<input list="browsr0" type="text" id="diag" class="span4" placeholder="Diagnosis" />
				<datalist id='browsr0'>
				<?php
				$qq = mysqli_query($link,"SELECT `diagnosis` FROM `diagnosis_master`");
				while($cc=mysqli_fetch_array($qq))
				{
				?>
				<option value="<?php echo $cc['diagnosis'];?>">
				<?php
				}
				?>
				</datalist>
			</td>
			<td><select id="ord" class="span2"><option value="0">Select</option><option value="Primary">Primary</option><option value="Secondary">Secondary</option></select></td>
			<td><select id="cert" class="span2"><option value="0">Select</option><option value="Confirmed">Confirmed</option><option value="Presumed">Presumed</option></select></td>
			<td>
				<select id="doc">
					<option value="0">Select</option>
					<?php
					$qr=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($r=mysqli_fetch_array($qr))
					{
					?>
					<option value="<?php echo $r['consultantdoctorid'];?>"><?php echo $r['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<th><input type="button" class="btn btn-mini btn-info" id="ad" value="Add" onclick="ad()" style="" /></th>
		</tr>
	</table>
	<?php
}

if($_POST["type"]=="ipd_pat_doc_list")
{
	$no=$_POST['no'];
	$val="<datalist id='browsr".$no."'>";
	$qq = mysqli_query($link,"SELECT `diagnosis` FROM `diagnosis_master`");
	while($cc=mysqli_fetch_array($qq))
	{
	$val.="<option value='$cc[diagnosis]'>";
	}
	$val.="</datalist>";
	//echo $val;
	
	$d="";
	$q=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
	while($r=mysqli_fetch_array($q))
	{
		$d.="<option value='".$r['consultantdoctorid']."'>".$r['Name']."</option>";
	}
	echo $val."@".$d;
}

if($_POST["type"]=="search_patient_list_ipd")
{
	$ward=$_POST['ward'];
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	
	$zz=0;
	
	$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date') ";
	
	if($dat)
	{
		$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$dat' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$dat') ";
	}
	//$q=" SELECT a.*, c.`bed_no` FROM `uhid_and_opdid` a, `ipd_bed_alloc_details` b, `bed_master` c WHERE a.`opd_id`=b.`ipd_id` AND b.`bed_id`=c.`bed_id` AND a.`type`='3' AND b.`alloc_type`='1' ";
	
	if($ward>0)
	{
		$q.=" AND `ward_id`='$ward'";
		$zz=0;
	}
	if($uhid)
	{
		if(strlen($uhid)>2)
		{
			$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `patient_id` like '$uhid%'";
			
			$zz=1;
		}
	}
	if($ipd)
	{
		if(strlen($ipd)>2)
		{
			$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `ipd_id` like '$ipd%'";
			
			$zz=1;
		}
	}
	if($name)
	{
		if(strlen($name)>2)
		{
			$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$name%')";
			
			$zz=1;
		}
	}
	
	//~ if($zz==0)
	//~ {
		//~ $q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date') ";
		
		//~ if($ward>0)
		//~ {
			//~ $q.=" AND `ward_id`='$ward'";
		//~ }
	//~ }
	
	$q.=" and ipd_id IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
	
	$q.=" ORDER BY `slno` ASC";
	
	//echo $q;
	
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		
?>
		<p style="margin-top: 2%;" id="print_div">
			<span class="text-right" id="excel_btn_hide"><a class="btn btn-info btn-mini" href="pages/nursing_pat_list_xls.php?ward=<?php echo $ward ?>&uhid=<?php echo $uhid ?>&ipd=<?php echo $ipd ?>&name=<?php echo $name ?>&dat=<?php echo $dat ?>"><i class="icon-file icon-large" style="line-height: 24px;"></i> Excel</a></span>
			
		</p>
		<table class="table table-condensed table-bordered">
			<tr>
				<!--<th>UHID</th>-->
				<th>#</th>
				<th>IPD ID</th>
				<th>Name</th>
				<th>Sex</th>
				<th>Age (DOB)</th>
				<!--<th>Ward</th>
				<th>Bed No</th>-->
				<th>Doctor</th>
				<th>User</th>
			</tr>
		<?php
			$n=1;
			while($data=mysqli_fetch_array($qq))
			{
				$r=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$data[ipd_id]'"));
				
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
				if($p["dob"]!=""){ $age=age_calculator($p["dob"])." (".convert_date_g($p["dob"]).")"; }else{ $age=$p["age"]." ".$p["age_type"]; }
				
				$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$r[patient_id]' and `opd_id`='$r[opd_id]' "));
		
				$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$dt_tm[user]' "));
				
				$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$r[patient_id]' and ipd_id='$r[opd_id]'"));
				if($bed_det['bed_id'])
				{
					$ward=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$bed_det[ward_id]'"));
					$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$bed_det[bed_id]'"));
					
					$ward=$ward["name"];
					$bed=$bed_det["bed_no"];
				}else
				{
					$ward="";
					$bed="";
					$bed_alloc_qry=mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$r[patient_id]' and ipd_id='$r[opd_id]' and alloc_type=1 order by slno asc");
					while($bed_alloc=mysqli_fetch_array($bed_alloc_qry))
					{
						$ward_val=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$bed_alloc[ward_id]'"));
						$bed_det_val=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$bed_alloc[bed_id]'"));
						
						$ward.=$ward_val["name"]."<br>";
						$bed.=$bed_det_val["bed_no"]."<br>";
					}
					//$ward="Discharged";
					//$bed="Discharged";
				}
				// Consultant Doctor
				$at_doc=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$r[patient_id]' and `ipd_id`='$r[opd_id]' ) "));
				
				$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `type`='2' "));
				if($cancel_request)
				{
					$td_function="";
					
					$td_style="style='background-color: #ff000021'";
					
					$emp_info_del=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$cancel_request[user]' "));
					
					$tr_title="title='Cancel request by $emp_info_del[name]' id='cancel_request_tr'";
				}
				else
				{
					$td_function="onclick=\"redirect_page('$r[patient_id]','$r[opd_id]')\"";
					$td_style="style='cursor:pointer;'";
					$tr_title="";
				}
				
		?>
				<!--<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['opd_id'];?>')" style="cursor:pointer;">-->
				<tr <?php echo $td_style." ".$tr_title." ".$td_function; ?> >
					<!--<td><?php echo $p['patient_id'];?></td>-->
					<td><?php echo $n;?></td>
					<td><?php echo $r['opd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $age;?></td>
					<!--<td><?php echo $ward;?></td>
					<td><?php echo $bed;?></td>-->
					<td><?php echo $at_doc['Name'];?></td>
					<td><?php echo $emp_info['name'];?></td>
				</tr>
			<?php
				$n++;
			}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="pat_ipd_med_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$bch=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `batch_no` DESC");
	while($res=mysqli_fetch_array($bch))
	{
		$qq=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'");
		$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'"));
		?>
		<span><b>Plan: <?php echo $res['batch_no'];?> Date: <?php echo $dt['date']." ".$dt['time'];?></b></span>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>#</th><th width="40%">Drug Name</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Total</th><th>Instructon</th><th>Start Date</th>
			</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($qq))
		{
			$m=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			if($r['frequency']==1)
			$fq="Immediately";
			if($r['frequency']==2)
			$fq="Once a day";
			if($r['frequency']==3)
			$fq="Twice a day";
			if($r['frequency']==4)
			$fq="Thrice a day";
			if($r['frequency']==5)
			$fq="Four times a day";
			if($r['frequency']==6)
			$fq="Five times a day";
			if($r['frequency']==7)
			$fq="Every Hour";
			if($r['frequency']==8)
			$fq="Every 2 Hours";
			if($r['frequency']==9)
			$fq="Every 3 Hours";
			if($r['frequency']==10)
			$fq="Every 4 Hours";
			if($r['frequency']==11)
			$fq="Every 5 Hours";
			if($r['frequency']==12)
			$fq="Every 6 Hours";
			if($r['frequency']==13)
			$fq="Every 7 Hours";
			if($r['frequency']==14)
			$fq="Every 8 Hours";
			if($r['frequency']==15)
			$fq="Every 10 Hours";
			if($r['frequency']==16)
			$fq="Every 12 Hours";
			
			if($r['instruction']==1)
			$ins="As Directed";
			if($r['instruction']==2)
			$ins="Before Meal";
			if($r['instruction']==3)
			$ins="Empty Stomach";
			if($r['instruction']==4)
			$ins="After Meal";
			if($r['instruction']==5)
			$ins="In the Morning";
			if($r['instruction']==6)
			$ins="In the Evening";
			if($r['instruction']==7)
			$ins="At Bedtime";
			if($r['instruction']==8)
			$ins="Immediately";
			$sn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_given` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' AND `item_code`='$r[item_code]' and `status`='3'"));
			if($sn>0)
			{
				$ds="disabled='disabled'";
				$val="Stopped";
				$cl="btn-danger";
			}else
			{
				$ds="";
				$val="Update";
				$cl="btn-primary";
			}
			?>
			<tr>
				<td><?php echo $n;?></td>
				<td><?php echo $m['item_name'];?><span class="text-right"><input type="button" class="btn btn-mini <?php echo $cl;?>" onclick="change_med('<?php echo $r['id'];?>')" value="<?php echo $val;?>" style="border-radius:10%;font-weight:bold;" <?php echo $ds;?> /></span></td>
				<td><?php echo $r['dosage'];?></td>
				<td><?php echo $fq;?></td>
				<td><?php echo $r['duration']." ".$r['unit_days'];?></td>
				<td><?php echo $r['total_drugs'];?></td>
				<td><?php echo $ins;?></td>
				<td><?php echo $r['start_date'];?></td>
			</tr>
			<?php
			$n++;
		}
		?>
		</table>
		<?php
	}
	$num=mysqli_num_rows($qq);
	if($num>0)
	{
		?>
		<button type="button" class="btn btn-info" id="ad" onclick="ad_med()"><i class="icon-plus"></i> Add More Plan</button>
		<?php
	}
	else
	{
		?>
		<button type="button" class="btn btn-info" id="ad" onclick="ad_med()"><i class="icon-plus"></i> Add Medication</button>
		<?php
	}
	?>
	<style>
		.widget-content{border-bottom:none;}
	</style>
	<script>
		$("#st_date").datepicker(
		{
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
	</script>
		<?php
}

if($_POST["type"]=="pat_ipd_inv_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	
	$pat_discharge_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($pat_discharge_num>0)
	{
		$btndis="disabled='disabled'";
	}
	else
	{
		$btndis="";
	}
	
	//$ds=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `type`='4' ORDER BY `batch_no` DESC");
	$ds=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `batch_no` DESC");
	//$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `type`='4'");
	$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
	$num=mysqli_num_rows($q);
	$no=mysqli_num_rows($ds);
?>
	<div class="span5" style="margin-left:0px;">
	<?php
	if($no>0)
	{
		while($res=mysqli_fetch_array($ds))
		{
			//$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' AND `type`='4'"));
			$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' "));
			$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'"));
			echo "<button type='button' class='btn btn-default bt' id='ad".$res['batch_no']."' onclick='view_batch(".$res['batch_no'].")'><span class='sp'>Batch No=".$res['batch_no']."</span><span class='sp'>Tests=".$nn."</span><span class='sp'>Date: ".convert_date_g($dt['date'])."</span><span class='sp'>Time: ".convert_time($dt['time'])."</span></button><br/>";
		}
	}
	if($num>0 && $pat_discharge_num==0)
	{
	?>
		<button type="button" class="btn btn-info" id="adm" onclick="ad_tests()" style="" <?php echo $btndis; ?>><i class="icon-plus"></i> Add New Batch</button>
	<?php
	}
	else if($pat_discharge_num==0)
	{
	?>
	<button type="button" class="btn btn-info" id="ad" onclick="ad_tests()" style="" <?php echo $btndis; ?>><i class="icon-plus"></i> Add</button>
	<?php
	}
	
	if($num>1)
	{
?>
		<button class="btn btn-print" onclick="print_batch_bill('<?php echo $uhid; ?>','<?php echo $ipd; ?>','0')"><i class="icon-print"></i> All Batch</button>
<?php
	}
	?>
	</div>
	<div id="batch_details" class="span5" style="margin-left:-40px;max-width:550px;min-width:540px;"></div>
	<style>
		.widget-content{border-bottom:none;}
		.sp{margin-left:10px;}
		.bt{margin-bottom:5px;}
	</style>
	<?php
}

if($_POST["type"]=="pat_day_inv_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	
	$ds=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `batch_no` DESC");
	
	$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
	$num=mysqli_num_rows($q);
	$no=mysqli_num_rows($ds);
	?>
	<div class="span5" style="margin-left:0px;">
	<?php
	if($no>0)
	{
		while($res=mysqli_fetch_array($ds))
		{
			$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' "));
			$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'"));
			echo "<button type='button' class='btn btn-default bt' id='ad".$res['batch_no']."' onclick='view_batch(".$res['batch_no'].")'><span class='sp'>Batch No=".$res['batch_no']."</span><span class='sp'>Tests=".$nn."</span><span class='sp'>Date: ".convert_date_g($dt['date'])."</span><span class='sp'>Time: ".convert_time($dt['time'])."</span></button><br/>";
		}
	}
	if($num>0)
	{
	?>
	<button type="button" class="btn btn-info" id="adm" onclick="ad_tests()" style=""><i class="icon-plus"></i> Add New Batch</button>
	<?php
	}
	else
	{
	?>
	<button type="button" class="btn btn-info" id="ad" onclick="ad_tests()" style=""><i class="icon-plus"></i> Add</button>
	<?php
	}
	?>
	</div>
	<div id="batch_details" class="span5" style="margin-left:-40px;max-width:550px;min-width:540px;"></div>
	<style>
		.widget-content{border-bottom:none;}
		.sp{margin-left:10px;}
		.bt{margin-bottom:5px;}
	</style>
	<?php
}

if($_POST["type"]=="show_sel_tests_day")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	?>
	<div id="test_sel">
		<div id="list_all_test" style="" class="up_div"></div>
		<!--<h5 class="text-left" onClick="load_tab(2,'a')">Test Details For</h5>-->
		<table class="table">
			<tr>
				<th><label for="test">Select Test</label></th>
				<td><input type="text" name="test" id="test" class="span6" onFocus="test_enable()" onKeyUp="select_test_new(this.value,event)" /><input type="text" name="batch" id="batch" style="display:none;" value="<?php echo $batch;?>" /></td>
				<th><label for="test">Doctor</label></th>
				<td>
					<select id="ipd_test_ref_doc">
						<option value="0">Select</option>
				<?php
					$ref_doc_val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' "));
					$ipd_test_ref_qry=mysqli_query($link," SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name` ");
					while($ipd_test_ref=mysqli_fetch_array($ipd_test_ref_qry))
					{
						if($ipd_test_ref['consultantdoctorid']==$ref_doc_val['consultantdoctorid']){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$ipd_test_ref[consultantdoctorid]' $sel >$ipd_test_ref[Name]</option>";
					}
				?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<div id="test_d">
						
					</div>
				</td>
			</tr>
		</table>
		</div>
		<div id="ss_tests" style="height: 150px;">
			<?php
			//$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `type`='4'");
			$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' ");
			$num=mysqli_num_rows($q);
			if($num>0)
			{
			?>
			<table class='table table-condensed table-bordered' style='style:none' id='test_list'>
				<tr>
					<th style='background-color:#cccccc'>#</th><th style='background-color:#cccccc'>Tests</th><th style='background-color:#cccccc'>Remove</th>
				</tr>
				<?php
				$i=1;
				while($r=mysqli_fetch_array($q))
				{
					$t=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$r[testid]'"));
					$t_res1=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
					$t_res2=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults_card` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
					$t_res3=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
					if($r['testid']==1227)
					{
						$t_res4=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch'"));
					}
					$t_res5=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
				?>
				<tr><td><?php echo $i;?></td><td width='80%'><?php echo $t['testname'];?><input type='hidden' value='<?php echo $r['testid'];?>' class='test_id'/></td>
				<?php
				if($t_res1>0 || $t_res2>0 || $t_res3>0 || $t_res4>0 || $t_res5>0)
				{
				?><td></td>
				<?php
				}else
				{
				?><td onclick='delete_rows(this,2)'><span class='text-danger' style='cursor:pointer'><i class='icon-remove'></i></span></td>
				<?php
				}?>
				</tr>
				<?php
				$i++;
				}
				?>
			</table>
			<?php
			}
			?>
		</div>
	</div>
	<script>
		$("#ipd_test_ref_doc").select2({ theme: "classic" });
	</script>
	<style>
		.select2-dropdown
		{
			z-index:99999 !important;
		}
	</style>
	<?php
}

if($_POST["type"]=="ipd_batch_details")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd_id'];
	$batch=$_POST['batch_no'];
	$usr=$_POST['user'];
	
	$pat_discharge_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($pat_discharge_num>0)
	{
		$btndis="disabled='disabled'";
	}
	else
	{
		$btndis="";
	}
	
	//$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `type`='4'");
	$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch'");
	
	// Ref doc
	//$ref_doc_val=mysqli_fetch_array(mysqli_query($link," SELECT a.`Name` FROM `consultant_doctor_master` a, `ipd_test_ref_doc` b WHERE a.`consultantdoctorid`=b.`consultantdoctorid` AND b.`patient_id`='$uhid' AND b.`ipd_id`='$ipd' AND b.`batch_no`='$batch' "));
	
	$ref_doc_val=mysqli_fetch_array(mysqli_query($link," SELECT a.`ref_name` FROM `refbydoctor_master` a, `ipd_test_ref_doc` b WHERE a.`refbydoctorid`=b.`refbydoctorid` AND b.`patient_id`='$uhid' AND b.`ipd_id`='$ipd' AND b.`batch_no`='$batch' "));
	
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		//$d=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' "));
	?>
	<table class="table table-condensed table-bordered" style="margin-bottom: 2px;">
		<tr>
			<th>#</th>
			<th>Test Name</th>
			<th>Doctor: <?php echo $ref_doc_val["ref_name"]; ?></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			$tst_cat=mysqli_fetch_array(mysqli_query($link," SELECT `category_id` FROM `testmaster` WHERE `testid`='$r[testid]' "));
			$tst=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$r[testid]'"));
			if($tst_cat['category_id']==1)
			{
				if($r["testid"]!="1227")
				{
					$bt1=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
					$bt2=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
					$bt=$bt1+$bt2;
				}else
				{
					$bt=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' "));
				}
			}
			if($tst_cat['category_id']==2)
			{
				$bt=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
			}
			if($tst_cat['category_id']==3)
			{
				$bt=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults_card` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
			}
			if($bt>0)
			{
				$rep_btn="<button class='btn btn-mini btn-success' onclick=rep_pop('$uhid','$ipd','$batch','$r[testid]','$tst_cat[category_id]')>Report</button>";
			}
			else
			{
				$rep_btn="";
			}
		?>
		<tr>
			<td><?php echo $n;?></td><td colspan="2"><?php echo $tst['testname'];?><span class="text-right"><?php echo $rep_btn;?></span></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
<?php
	if($pat_discharge_num==0)
	{
?>
	<input type="button" class="btn btn-info" id="adb" value="Add More Test" onclick="ad_tests('<?php echo $batch;?>')" style="" <?php echo $btndis ?> />
<?php
	}
?>
	<!--<input type="button" class="btn btn-info" id="rcv" value="Receive Sample" onclick="rcv_sample('<?php echo $uhid;?>','<?php echo $ipd;?>','<?php echo $batch;?>')" style="" />-->
	<!--<input type="button" class="btn btn-info" id="trf" value="Requisation Form" onclick="lab_copy('<?php echo $uhid;?>','<?php echo $ipd;?>','<?php echo $batch;?>')" style="" />-->
	
	<div class="btn-group">
		<button data-toggle="dropdown" class="btn btn-info dropdown-toggle">Requisition <span class="caret"></span></button>
		<ul class="dropdown-menu">
		<?php
			$deps=mysqli_query($link,"select distinct a.id,a.name from test_department a,testmaster b,patient_test_details c where a.id=b.type_id and b.testid=c.testid and c.patient_id='$uhid' and c.batch_no='$batch' and c.ipd_id='$ipd' order by a.id");
			if(mysqli_num_rows($deps)>1)
			{
		?>
				<li onclick="print_req('<?php echo $uhid;?>','<?php echo $ipd;?>','<?php echo $batch;?>','')"><a>All</a></li>
				<li class="divider"></li>
		<?php 
			}
			while($dp=mysqli_fetch_array($deps))
			{
				echo "<li onclick=\"print_req('$uhid','$ipd','$batch','$dp[id]')\"><a>$dp[name]</a></li>";
			}
		?>
		</ul>
	</div>
	
	<button class="btn btn-print" id="nurse_test_bill_print_btn" onclick="print_batch_bill('<?php echo $uhid;?>','<?php echo $ipd;?>','<?php echo $batch;?>')"><i class="icon-print"></i> Print</button>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<?php
	}
}

if($_POST["type"]=="update_note_ipd")
{
	$test=$_POST['test_id'];
	$patient_id=$_POST['uhid'];
	$ipd_id=$_POST['ipd'];
	$batch=$_POST['batch'];
	$usr=$_POST['usr'];

	$qry=mysqli_num_rows(mysqli_query($link, " select * from sample_note where patient_id='$patient_id' and ipd_id='$ipd_id' and `batch_no`='$batch' and test_id='$test' and user='$usr' "));
	if($qry>0)
	{
		$val=mysqli_fetch_array(mysqli_query($link, " select * from sample_note where patient_id='$patient_id' and ipd_id='$ipd_id' and `batch_no`='$batch' and test_id='$test' and user='$usr' "));
		echo $val['note'];
	}
	else
	{
		echo "";
	}
}

if($_POST["type"]=="pat_ipd_vital_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$view=$_POST['view'];
	?>
	<select id="view" style="margin-bottom:0px;" onchange="view_vital(this.value)">
		<option value="1" <?php if($view=="1"){echo "selected='selected'";}?>>Current</option>
		<option value="2" <?php if($view=="2"){echo "selected='selected'";}?>>All</option>
	</select>
	<?php
	if($view==1)
	{
		$qry=mysqli_query($link,"SELECT * FROM `ipd_pat_vital` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$date'");
		$num=mysqli_num_rows($qry);
		if($num>0)
		{
			$det=mysqli_fetch_array($qry);
			$weight=$det['weight'];
			$height=$det['height'];
			$mid_cum=$det['medium_circumference'];
			$hd_cum=$det['head_circumference'];
			$bmi1=$det['BMI_1'];
			$bmi2=$det['BMI_2'];
			$spo=$det['spo2'];
			$pulse=$det['pulse'];
			$temp=$det['temp'];
			$pr=$det['PR'];
			$rr=$det['RR'];
			$systolic=$det['systolic'];
			$diastolic=$det['diastolic'];
			$note=$det['note'];
			$val="Update";
		}
		else
		{
			$weight="";
			$height="";
			$mid_cum="";
			$hd_cum="";
			$bmi1="";
			$bmi2="";
			$spo="";
			$pulse="";
			$temp="";
			$pr="";
			$rr="";
			$systolic="";
			$diastolic="";
			$note="";
			$val="Save";
		}
	?>
	<table class="table table-condensed">
		<tbody>
			<tr>
				<td><b>Weight</b></td>
				<td><input id="weight" class="span1" value="<?php echo $weight;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');physical(this.value,event)" placeholder="KG" type="text"></td>
				<td><b>Height</b></td>
				<td><input id="height" class="span1" value="<?php echo $height;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');physical1(this.value,event)" placeholder="CM" type="text"></td>
				<td><b>Mid-arm Circumference</b></td>
				<td><input id="mid_cum" value="<?php echo $mid_cum;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text"></td><td class="span3"><b>Head Circumference</b></td>
				<td><input id="hd_cum" value="<?php echo $hd_cum;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text"></td>
			</tr>
			<tr>
				<td><b>BMI</b></td>
				<td><input id="bmi1" readonly="readonly" value="<?php echo $bmi1;?>" style="width:30px;" type="text"> <input id="bmi2" readonly="readonly" value="<?php echo $bmi2;?>" style="width:30px;" type="text"></td>
				<td><b>SPO<sub>2</sub>(%)</b></td>
				<td><input id="spo" type="text" value="<?php echo $spo;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" /></td>
				<td><b>Pulse</b></td>
				<td><input id="pulse" type="text" value="<?php echo $pulse;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" /></td>
				<td><b>Temperature (<sup>o</sup>C)</b></td>
				<td><input id="temp" value="<?php echo $temp;?>" onkeyup="tab(this.id,event)" class="span1" type="text" /></td>
			</tr>
			<tr>
				<td><b>PR</b></td>
				<td><input id="pr" value="<?php echo $pr;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
				<td><b>RR(/min)</b></td>
				<td><input id="rr" value="<?php echo $rr;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
				<td><b>BP:-</b> <b style="float:right;margin-right:10%;">Systolic:</b></td>
				<td><input id="systolic" value="<?php echo $systolic;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
				<td><b>Diastolic:</b></td>
				<td><input id="diastolic" value="<?php echo $diastolic;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" /></td>
			</tr>
			<tr>
				<td><b>Note</b></td>
				<td colspan="7"><input type="text" id="vit_note" value="<?php echo $note;?>" onkeyup="tab(this.id,event)" style="width:50%;" /></td>
			</tr>
			<tr>
				<td colspan="8"><span style="float:right;"><button type="button" id="sav_vit" class="btn btn-info" onclick="save_vital()" ><i class="icon-save"></i> <?php echo $val;?></button></span></td>
			</tr>
		</tbody>
	</table>
	<?php
	}
	else
	{
		$qry=mysqli_query($link,"SELECT * FROM `ipd_pat_vital` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `date` DESC");
		$num=mysqli_num_rows($qry);
		if($num>0)
		{
			$n=1;
			while($r=mysqli_fetch_array($qry))
			{
			?>
			<div><b>Date: <?php echo convert_date_g($r['date'])." ".convert_time($r['time']);?></b></div>
			<table class="table table-condensed table-bordered">
				<tr>
					<td colspan="6" style="background:#dddddd;"></td>
				</tr>
				<tr>
					<th>Weight</th>
					<th>Height</th>
					<th>BMI</th>
					<th>SPO<sub>2</sub>(%)</th>
					<th>Head Circumference</th>
					<th>Mid-arm Circumference</th>
				</tr>
				<tr>
					<td><?php echo $r['weight'];?> KG</td>
					<td><?php echo $r['height'];?> CM</td>
					<td><?php echo $r['BMI_1'].".".$r['BMI_2'];?></td>
					<td><?php echo $r['spo2'];?></td>
					<td><?php echo $r['head_circumference'];?></td>
					<td><?php echo $r['medium_circumference'];?></td>
				</tr>
				<tr>
					<td colspan="6" style="background:#dddddd;"></td>
				</tr>
				<tr>
					<th>PR</th>
					<th>RR(/min)</th>
					<th>BP</th>
					<th>Pulse</th>
					<th>Temperature (<sup>o</sup>C)</th>
					<th width="25%">Note</th>
				</tr>
				<tr>
					<td><?php echo $r['PR'];?></td>
					<td><?php echo $r['RR'];?></td>
					<td><?php echo $r['systolic']."/".$r['diastolic'];?></td>
					<td><?php echo $r['pulse'];?></td>
					<td><?php echo $r['temp'];?></td>
					<td><div style="max-height:100px;overflow-Y:scroll;"><?php echo $r['note'];?></div></td>
				</tr>
				<tr>
					<td colspan="6" style="background:#dddddd;"></td>
				</tr>
			</table>
			<?php
			$n++;
			}
		}
	}
}

if($_POST["type"]=="pat_ipd_med_admin")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$view=$_POST['view'];
	if($view==1)
	{
		$q=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `dosage_date`='$date' order by `id`");
		$dis="";
	}
	if($view==2)
	{
		$q=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' order by `dosage_date`, `id`");
		$dis="disabled='disabled'";
	}
	$num=mysqli_num_rows($q);
	if($num>0)
	{
	?>
	<style>
		#samp{width:450px;margin-bottom:auto;}
		#samp tr td{border-top:none;}
	</style>
	<span>
		<!--<b>Date: <?php echo $date;?></b>-->
		<select id="view" style="margin-bottom:0px;" onchange="view_medi(this.value)">
			<option value="1" <?php if($view=="1"){echo "selected='selected'";}?>>Current</option>
			<option value="2" <?php if($view=="2"){echo "selected='selected'";}?>>All</option>
		</select>
	</span>
	<span class="text-right">
		<table class="table table-condensed" id="samp">
			<tr>
				<td><input type="button" class="btn btn-mini btn-info" value="O" style="border-radius:50%;font-weight:bold;" disabled="disabled" /> Remaining</td>
				<td><input type="button" class="btn btn-mini btn-success" value="O" style="border-radius:50%;font-weight:bold;" disabled="disabled" /> Administered</td>
				<td><input type="button" class="btn btn-mini btn-warning" value="O" style="border-radius:50%;font-weight:bold;" disabled="disabled" /> Skipped</td>
				<td><input type="button" class="btn btn-mini btn-danger" value="O" style="border-radius:50%;font-weight:bold;" disabled="disabled" /> Stopped</td>
			</tr>
		</table>
	</span>
	<table id="med_adm" class="table table-condensed table-bordered" style="margin-bottom: 2px;">
		<tr>
			<th>#</th><th width="40%">Drug</th><th>Instruction</th><th>Consultant By</th><th>Dosage</th>
		</tr>
		<?php
		$itm="";
		$btch="";
		$n=$nn=1;
		$stop_itm="";
		while($r=mysqli_fetch_array($q))
		{
			$bch=$r['batch_no'];
			$tst=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			$f=mysqli_fetch_array(mysqli_query($link,"SELECT `instruction`,`consultantdoctorid` FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$r[batch_no]' AND `item_code`='$r[item_code]'"));
			$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$f[consultantdoctorid]'"));
			
			if($f['instruction']==1)
			$ins="As Directed";
			if($f['instruction']==2)
			$ins="Before Meal";
			if($f['instruction']==3)
			$ins="Empty Stomach";
			if($f['instruction']==4)
			$ins="After Meal";
			if($f['instruction']==5)
			$ins="In the Morning";
			if($f['instruction']==6)
			$ins="In the Evening";
			if($f['instruction']==7)
			$ins="At Bedtime";
			if($f['instruction']==8)
			$ins="Immediately";
			$fol='';
						
			for($jj=1;$jj<=$r['drugs'];$jj++)
			{
				if($view==1)
				{
					$giv_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_given` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$r[batch_no]' AND `item_code`='$r[item_code]' and `status`='3'"));
					if($giv_num>0)
					{
						$stop="Yes";
					}else
					{
						$giv_f=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_given` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$r[batch_no]' AND `item_code`='$r[item_code]' AND `serial_num`='$jj' AND `date`='$r[dosage_date]'"));
						if($giv_f['status']==1)
						{
							$dss="disabled='disabled'";
							$cs="btn-success";
							$stop="No";
						}else if($giv_f['status']==2)
						{
							$dss="disabled='disabled'";
							$cs="btn-warning";
							$stop="No";
						}else
						{
							$stop="No";
							$cs="btn-info";
							$dss="";
						}
					}
				}
				if($view==2)
				{
					$giv=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_given` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$r[batch_no]' AND `item_code`='$r[item_code]' AND `serial_num`='$jj' AND `date`='$r[dosage_date]'");
					$g=mysqli_num_rows($giv);
					if($itm=="" || $btch=="")
					{
						$itm=$r['item_code'];
						$btch=$r['batch_no'];
						$stop="No";
					}else
					{
						if($stop=="Yes" && $itm==$r['item_code'] && $btch==$r['batch_no'])
						{
							$stop="Yes";
							$itm=$r['item_code'];
							$btch=$r['batch_no'];
						}else
						{
							$stop="No";
							$itm=$r['item_code'];
							$btch=$r['batch_no'];
						}
					}
					if($g==0)
					{
						if($stop=="No")
						{
							$dss="";
							$cs="btn-info";
						}else
						{
							$dss="disabled='disabled'";
							$cs="btn-danger";
						}
					}
					else
					{
						$f=mysqli_fetch_array($giv);
						if($f['status']==1)
						{
							$dss="disabled='disabled'";
							$cs="btn-success";
							$stop="No";
						}
						if($f['status']==2)
						{
							$dss="disabled='disabled'";
							$cs="btn-warning";
							$stop="No";
						}
						if($f['status']==3)
						{
							$dss="disabled='disabled'";
							$cs="btn-danger";
							$stop="Yes";
							$stop_itm.=$r['item_code']."@".$r['batch_no']."@";
						}
					}
					if(strpos($stop_itm, $r['item_code'])!==false)
					{
						$btc="@".$r['batch_no']."@";
						if(strpos($stop_itm, $btc)!==false)
						{
							$stop="Yes";
						}
					}
				}
				if($stop=="Yes")
				{
					$dss="disabled='disabled'";
					$cs="btn-danger";
				}
				else
				{							
					$stop="No";
				}
				$fol.='<input type="button" class="a btn btn-mini '.$cs.'" id="a'.$r[id].$jj.'" value="O" onclick="folow($(this).offset(),'.$r[id].','.$jj.')" style="border-radius:50%;font-weight:bold;" '.$dss.' '.$dis.' /> &nbsp;&nbsp;&nbsp; ';
			}
			if($n==1)
			{
				$dos_dt=$r['dosage_date'];
				$brk_tr="Yes";
			}else
			{
				if($dos_dt!=$r['dosage_date'])
				{
					$brk_tr="Yes";
					$dos_dt=$r['dosage_date'];
					$nn=1;
				}else
				{
					$brk_tr="No";
				}
			}
			if($brk_tr=="Yes")
			{
				echo "<tr><th colspan='5'>Date: ".$dos_dt."</th></tr>";
			}
		?>
		<tr class="<?php if($r['plan']=="1"){echo "emer";}?>">
			<td><?php echo $nn;?></td><td><?php echo $tst['item_name'];?></td><td><?php echo $ins;?></td><td><?php echo $doc['Name'];?></td><td><?php echo $fol;?></td>
		</tr>
		<?php
		$n++;
		$nn++;
		}
		?>
	</table>
	<button type="button" class="btn btn-info" id="adb" onclick="ad_med_emer()" style=""><i class="icon-plus"></i> Add Medicine</button>
	<?php
	}
}

if($_POST["type"]=="ipd_add_medicine")
{
	$batch=$_POST['batch'];
	?>
	<table class="table table-condensed">
		<tr>
			<th width="15%">Drug Name</th>
			<td colspan="5">
				<input type="text" name="medi" id="medi" class="span5" onFocus="load_medi_list()" onKeyUp="load_medi_list1(this.value,event)" onBlur="javascript:$('#med_div').fadeOut(500)" /> <span id="g_name" style="display:none;"><b>Generic Name</b> <input type="text" id="generic" class="span3" /></span>
				<input type="hidden" id="medid" />
				<div id="med_info"></div>
				<div id="med_div" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
						<th>Drug Name</th>
						<?php
							$d=mysqli_query($link, "SELECT * FROM `item_master` order by `item_name`");
							$i=1;
							while($d1=mysqli_fetch_array($d))
							{
							?>
								<tr onclick="select_med('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['item_type_id'];?>','<?php echo $d1['generic_name'];?>')" style="cursor:pointer" <?php echo "id=med".$i;?>>
									<td><?php echo $d1['item_name'];?>
										<div <?php echo "id=mdname".$i;?> style="display:none;">
										<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$d1['item_type_id']."#".$d1['generic_name'];?>
										</div>
									</td>
								</tr>
							<?php
								$i++;
								}
							?>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<span id="med_dos" style="display:none;">
						<table class="table table-condensed">
							<tr>
								<th>Dosage</th>
								<td>
									<select id="dos" onkeyup="meditab(this.id,event)" onchange="calc_totday()" style="width:80px;">
										<option value="0">select</option>
										<?php
										for($j=1;$j<=5;$j++)
										{
										?>
										<option value="<?php echo $j;?>"><?php echo $j;?></option>
										<?php
										}
										?>
									</select>
								</td>
								<th>Unit</th>
								<td><input type="text" id="unit" readonly="readonly" class="span2" placeholder="Units" /></td>
								<th>Frequency</th>
								<td>
									<select id="freq" onkeyup="meditab(this.id,event)" onchange="calc_totday()" class="span2">
										<option value="0">Select</option>
										<option value="1">Immediately</option>
										<option value="2">Once a day</option>
										<option value="3">Twice a day</option>
										<option value="4">Thrice a day</option>
										<option value="5">Four times a day</option>
										<option value="6">Five times a day</option>
										<option value="7">Every hour</option>
										<option value="8">Every 2 hours</option>
										<option value="9">Every 3 hours</option>
										<option value="10">Every 4 hours</option>
										<option value="11">Every 5 hours</option>
										<option value="12">Every 6 hours</option>
										<option value="13">Every 7 hours</option>
										<option value="14">Every 8 hours</option>
										<option value="15">Every 10 hours</option>
										<option value="16">Every 12 hours</option>
										<!--<option value="17">On alternate days</option>
										<option value="18">Once a week</option>
										<option value="19">Twice a week</option>
										<option value="20">Thrice a week</option>
										<option value="21">Every 2 weeks</option>
										<option value="22">Every 3 weeks</option>
										<option value="23">Once a month</option>-->
									</select>
								</td>
								<th>Start Date</th>
								<td><input type="text" id="st_date" style="width:100px;" onkeyup="meditab(this.id,event)" /></td>
							</tr>
							<tr>
								<th>Duration</th>
								<td>
									<select id="dur" onkeyup="meditab(this.id,event)" onchange="calc_totday()" style="width:80px;">
										<option value="0">select</option>
										<?php
										for($j=1;$j<=10;$j++)
										{
										?>
										<option value="<?php echo $j;?>"><?php echo $j;?></option>
										<?php
										}
										?>
									</select>
								</td>
								<th>Unit Days</th>
								<td>
									<select id="unit_day" style="width:80px;" onchange="calc_totday()" onkeyup="meditab(this.id,event)">
										<option value="0">select</option>
										<option value="Days">Days</option>
										<option value="Weeks">Weeks</option>
										<option value="Months">Months</option>
									</select>
								</td>
								<th>Total</th>
								<td><input type="text" id="totl" class="span2" readonly="readonly" /></td>
								<th>Instruction</th>
								<td>
									<select id="inst" style="width:120px;" onkeyup="meditab(this.id,event)">
										<option value="1">As Directed</option>
										<option value="2">Before Meal</option>
										<option value="3">Empty Stomach</option>
										<option value="4">After Meal</option>
										<option value="5">In the Morning</option>
										<option value="6">In the Evening</option>
										<option value="7">At Bedtime</option>
										<option value="8">Immediately</option>
									</select>
								</td>
							</tr>
							<tr id="p_ls">
								<th>SOS</th>
								<td>
									<input type="checkbox" id="sos" class="checkbox" value="sos" />
								</td>
								<th>Consultant Doctor</th>
								<td>
									<select id="con_doc" onkeyup="meditab(this.id,event)">
										<option value="0">Select</option>
										<?php
										$q=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master`");
										while($r=mysqli_fetch_array($q))
										{
										?>
										<option value="<?php echo $r['consultantdoctorid'];?>"><?php echo $r['Name'];?></option>
										<?php
										}
										?>
									</select>
								</td>
								<td colspan="4">
									
								</td>
							</tr>
						</table>
						<center><input type="button" id="add_medi" class="btn btn-info" value="Add" onclick="set_medi()" /></center>
						<!--<table class="table table-condensed">
							<tr>
								<th>Dosage</th>
								<td>
									<select id="dos" onkeyup="meditab(this.id,event)" style="width:80px;">
										<option value="0">select</option>
										<?php
										for($j=1;$j<=10;$j++)
										{
										?>
										<option value="<?php echo $j;?>"><?php echo $j;?></option>
										<?php
										}
										?>
									</select>
								</td>
								<th>Unit</th>
								<td><input type="text" id="unit" readonly="readonly" class="span2" placeholder="Units" /></td>
								<th>Instruction</th>
								<td><input type="text" id="inst" onkeyup="meditab(this.id,event)" class="span5" placeholder="Instruction" /></td>
							</tr>
						</table>
						<center>
							<input type="button" id="add_medi" class="btn btn-info" value="Add" onclick="set_medi('<?php echo $batch;?>')" /> <!--insert_medi-->
							<!--<input type="button" id="" class="btn btn-danger" value="Close" data-dismiss="modal" onclick="$('#med_list').css('height','100px')" />
						</center>-->
					</span>
				</td>
			</tr>
			<tr id="medi_list" style="display:none;">
				<td id="medi_list_data" colspan="2">
				
				</td>
			</tr>
		</table>
		<style>
			.table tr:hover{background:none;}
		</style>
		<script>
			$("#st_date").datepicker(
			{
				dateFormat: 'yy-mm-dd',
				minDate: '0',
			});
		</script>
	<?php
}

if($_POST["type"]=="ipd_add_medicine_post")
{
	$batch=$_POST['batch'];
	?>
	<table class="table table-condensed">
		<tr>
			<th width="15%">Drug Name</th>
			<td colspan="5">
				<input type="text" name="medip" id="medip" class="span5" onFocus="load_medi_list_post()" onKeyUp="load_medi_list_post1(this.value,event)" onBlur="javascript:$('#med_div_post').fadeOut(500)" /> <span id="g_name" style="display:none;"><b>Generic Name</b> <input type="text" id="generic1" class="span3" /></span>
				<input type="hidden" id="medidp" />
				<div id="med_info_post"></div>
				<div id="med_div_post" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
						<th>Drug Name</th>
						<?php
							$d=mysqli_query($link, "SELECT * FROM `item_master` order by `item_name`");
							$i=1;
							while($d1=mysqli_fetch_array($d))
							{
							?>
								<tr onclick="select_med_post('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['item_type_id'];?>','<?php echo $d1['generic_name'];?>')" style="cursor:pointer" <?php echo "id=medp".$i;?>>
									<td><?php echo $d1['item_name'];?>
										<div <?php echo "id=mdname".$i;?> style="display:none;">
										<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$d1['item_type_id']."#".$d1['generic_name'];?>
										</div>
									</td>
								</tr>
							<?php
								$i++;
								}
							?>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<span id="med_dos_post" style="display:none;">
						<table class="table table-condensed">
							<tr>
								<th>Dosage</th>
								<td>
									<select id="dos" onkeyup="meditab(this.id,event)" onchange="calc_totday()" style="width:80px;">
										<option value="0">select</option>
										<?php
										for($j=1;$j<=5;$j++)
										{
										?>
										<option value="<?php echo $j;?>"><?php echo $j;?></option>
										<?php
										}
										?>
									</select>
								</td>
								<th>Unit</th>
								<td><input type="text" id="unitp" readonly="readonly" class="span2" placeholder="Units" /></td>
								<th>Frequency</th>
								<td>
									<select id="freq" onkeyup="meditab(this.id,event)" onchange="calc_totday()" class="span2">
										<option value="0">Select</option>
										<option value="1">Immediately</option>
										<option value="2">Once a day</option>
										<option value="3">Twice a day</option>
										<option value="4">Thrice a day</option>
										<option value="5">Four times a day</option>
										<option value="6">Five times a day</option>
										<option value="7">Every hour</option>
										<option value="8">Every 2 hours</option>
										<option value="9">Every 3 hours</option>
										<option value="10">Every 4 hours</option>
										<option value="11">Every 5 hours</option>
										<option value="12">Every 6 hours</option>
										<option value="13">Every 7 hours</option>
										<option value="14">Every 8 hours</option>
										<option value="15">Every 10 hours</option>
										<option value="16">Every 12 hours</option>
										<!--<option value="17">On alternate days</option>
										<option value="18">Once a week</option>
										<option value="19">Twice a week</option>
										<option value="20">Thrice a week</option>
										<option value="21">Every 2 weeks</option>
										<option value="22">Every 3 weeks</option>
										<option value="23">Once a month</option>-->
									</select>
								</td>
								<th>Start Date</th>
								<td><input type="text" id="st_date" style="width:100px;" onkeyup="meditab(this.id,event)" /></td>
							</tr>
							<tr>
								<th>Duration</th>
								<td>
									<select id="dur" onkeyup="meditab(this.id,event)" onchange="calc_totday()" style="width:80px;">
										<option value="0">select</option>
										<?php
										for($j=1;$j<=10;$j++)
										{
										?>
										<option value="<?php echo $j;?>"><?php echo $j;?></option>
										<?php
										}
										?>
									</select>
								</td>
								<th>Unit Days</th>
								<td>
									<select id="unit_day" style="width:80px;" onchange="calc_totday()" onkeyup="meditab(this.id,event)">
										<option value="0">select</option>
										<option value="Days">Days</option>
										<option value="Weeks">Weeks</option>
										<option value="Months">Months</option>
									</select>
								</td>
								<th>Total</th>
								<td><input type="text" id="totl" class="span2" readonly="readonly" /></td>
								<th>Instruction</th>
								<td>
									<select id="inst" style="width:120px;" onkeyup="meditab(this.id,event)">
										<option value="1">As Directed</option>
										<option value="2">Before Meal</option>
										<option value="3">Empty Stomach</option>
										<option value="4">After Meal</option>
										<option value="5">In the Morning</option>
										<option value="6">In the Evening</option>
										<option value="7">At Bedtime</option>
										<option value="8">Immediately</option>
									</select>
								</td>
							</tr>
							<tr id="p_ls">
								<th>SOS</th>
								<td>
									<input type="checkbox" id="sos" class="checkbox" value="sos" />
								</td>
								<th>Consultant Doctor</th>
								<td>
									<select id="con_doc" onkeyup="meditab(this.id,event)">
										<option value="0">Select</option>
										<?php
										$q=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master`");
										while($r=mysqli_fetch_array($q))
										{
										?>
										<option value="<?php echo $r['consultantdoctorid'];?>"><?php echo $r['Name'];?></option>
										<?php
										}
										?>
									</select>
								</td>
								<td colspan="4">
									
								</td>
							</tr>
						</table>
						<center><input type="button" id="add_medi" class="btn btn-info" value="Add" onclick="set_medi_post()" /></center>
						<!--<table class="table table-condensed">
							<tr>
								<th>Dosage</th>
								<td>
									<select id="dos" onkeyup="meditab(this.id,event)" style="width:80px;">
										<option value="0">select</option>
										<?php
										for($j=1;$j<=10;$j++)
										{
										?>
										<option value="<?php echo $j;?>"><?php echo $j;?></option>
										<?php
										}
										?>
									</select>
								</td>
								<th>Unit</th>
								<td><input type="text" id="unit" readonly="readonly" class="span2" placeholder="Units" /></td>
								<th>Instruction</th>
								<td><input type="text" id="inst" onkeyup="meditab(this.id,event)" class="span5" placeholder="Instruction" /></td>
							</tr>
						</table>
						<center>
							<input type="button" id="add_medi" class="btn btn-info" value="Add" onclick="set_medi('<?php echo $batch;?>')" /> <!--insert_medi-->
							<!--<input type="button" id="" class="btn btn-danger" value="Close" data-dismiss="modal" onclick="$('#med_list').css('height','100px')" />
						</center>-->
					</span>
				</td>
			</tr>
			<tr id="medi_list_post" style="display:none;">
				<td id="medi_list_data" colspan="2">
				
				</td>
			</tr>
		</table>
		<style>
			.table tr:hover{background:none;}
		</style>
		<script>
			$("#st_date").datepicker(
			{
				dateFormat: 'yy-mm-dd',
				minDate: '0',
			});
		</script>
	<?php
}

if($_POST["type"]=="ipd_pat_med_folow")
{
	$id=$_POST['id'];
	$sl=$_POST['sl'];
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	//$m=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code` IN (SELECT `item_code` FROM `ipd_pat_medicine_details` WHERE `id`='$id')"));
	?>
	<table class='table table-condensed table-bordered' id=''>
		<!--<tr>
			<th><?php echo $m['item_name'];?></th>
		</tr>-->
		<tr>
			<td>
				<input type="button" id="" class="btn btn-success btn-block" value="Administer" onclick="medi_given('1','<?php echo $id;?>','<?php echo $sl;?>')" />
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="" class="btn btn-warning btn-block" value="Skipped" onclick="medi_given('2','<?php echo $id;?>','<?php echo $sl;?>')" />
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="" class="btn btn-danger btn-block" value="Stopped" onclick="medi_given('3','<?php echo $id;?>','<?php echo $sl;?>')" />
			</td>
		</tr>
	</table>
	<span class="text-right"><input type="button" id="" class="btn btn-mini btn-danger" value="Close" onclick="$('.a').removeClass('clk');$('#gter').fadeOut(500);" /></span>
	<?php
}

if($_POST["type"]=="pat_ipd_med_plan_upd")
{
	$id=$_POST['id'];
	$f=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine` WHERE `id`='$id'"));
	?>
	<table class="table table-condensed">
		<tr>
			<th>Drug Name</th>
			<td>
				<select id="drug">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `item_id`,`item_name` FROM `item_master` ORDER BY `item_name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['item_id'];?>" <?php if($r['item_id']==$f['item_code']){echo "selected='selected'";}?>><?php echo $r['item_name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<th>Dosage</th>
			<td>
				<select id="dose">
					<option value="0">Select</option>
					<?php
					for($j=1;$j<=5;$j++)
					{
					?>
					<option value="<?php echo $j;?>" <?php if($f['dosage']==$j){echo "selected='selected'";}?>><?php echo $j;?></option>
					<?php
					}
					?>
				</select>
			</td>
			<th>Frequency</th>
			<td>
				<select id="freq">
					<option value="0">Select</option>
					<option value="1" <?php if($f['frequency']=="1"){echo "selected='selected'";}?>>Immediately</option>
					<option value="2" <?php if($f['frequency']=="2"){echo "selected='selected'";}?>>Once a day</option>
					<option value="3" <?php if($f['frequency']=="3"){echo "selected='selected'";}?>>Twice a day</option>
					<option value="4" <?php if($f['frequency']=="4"){echo "selected='selected'";}?>>Thrice a day</option>
					<option value="5" <?php if($f['frequency']=="5"){echo "selected='selected'";}?>>Four times a day</option>
					<option value="6" <?php if($f['frequency']=="6"){echo "selected='selected'";}?>>Five times a day</option>
					<option value="7" <?php if($f['frequency']=="7"){echo "selected='selected'";}?>>Every hour</option>
					<option value="8" <?php if($f['frequency']=="8"){echo "selected='selected'";}?>>Every 2 hours</option>
					<option value="9" <?php if($f['frequency']=="9"){echo "selected='selected'";}?>>Every 3 hours</option>
					<option value="10" <?php if($f['frequency']=="10"){echo "selected='selected'";}?>>Every 4 hours</option>
					<option value="11" <?php if($f['frequency']=="11"){echo "selected='selected'";}?>>Every 5 hours</option>
					<option value="12" <?php if($f['frequency']=="12"){echo "selected='selected'";}?>>Every 6 hours</option>
					<option value="13" <?php if($f['frequency']=="13"){echo "selected='selected'";}?>>Every 7 hours</option>
					<option value="14" <?php if($f['frequency']=="14"){echo "selected='selected'";}?>>Every 8 hours</option>
					<option value="15" <?php if($f['frequency']=="15"){echo "selected='selected'";}?>>Every 10 hours</option>
					<option value="16" <?php if($f['frequency']=="16"){echo "selected='selected'";}?>>Every 12 hours</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Duration</th>
			<td>
				<select id="dur">
					<option value="0">Select</option>
					<?php
					for($j=1;$j<=10;$j++)
					{
					?>
					<option value="<?php echo $j;?>" <?php if($f['duration']==$j){echo "selected='selected'";}?>><?php echo $j;?></option>
					<?php
					}
					?>
				</select>
			</td>
			<th>Unit Days</th>
			<td>
				<select id="unit_day">
					<option value="0">Select</option>
					<option value="Days" <?php if($f['unit_days']=="Days"){echo "selected='selected'";}?>>Days</option>
					<option value="Weeks" <?php if($f['unit_days']=="Weeks"){echo "selected='selected'";}?>>Weeks</option>
					<option value="Months" <?php if($f['unit_days']=="Months"){echo "selected='selected'";}?>>Months</option>
				</select>
			</td>
			<th>Instruction</th>
			<td>
				<select id="inst">
					<option value="1" <?php if($f['instruction']=="1"){echo "selected='selected'";}?>>As Directed</option>
					<option value="2" <?php if($f['instruction']=="2"){echo "selected='selected'";}?>>Before Meal</option>
					<option value="3" <?php if($f['instruction']=="3"){echo "selected='selected'";}?>>Empty Stomach</option>
					<option value="4" <?php if($f['instruction']=="4"){echo "selected='selected'";}?>>After Meal</option>
					<option value="5" <?php if($f['instruction']=="5"){echo "selected='selected'";}?>>In the Morning</option>
					<option value="6" <?php if($f['instruction']=="6"){echo "selected='selected'";}?>>In the Evening</option>
					<option value="7" <?php if($f['instruction']=="7"){echo "selected='selected'";}?>>At Bedtime</option>
					<option value="8" <?php if($f['instruction']=="8"){echo "selected='selected'";}?>>Immediately</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Start Date</th>
			<td><input type="text" id="st_date_upd" value="<?php echo $f['start_date'];?>" /></td>
			<th>Consultant Doctor</th>
			<td>
				<select class="form-control" id="con_doc" disabled="disabled">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['consultantdoctorid'];?>" <?php if($f['consultantdoctorid']==$r['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $r['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td colspan="2"></td>
		</tr>
	</table>
	<span class="text-right">
		<a data-dismiss="modal" onclick="update_plan('<?php echo $id;?>');$('#upd_med_plan_det').css('height','100px');" class="btn btn-primary" href="#">Save</a>
		<a data-dismiss="modal" onclick="$('#upd_med_plan_det').css('height','100px');" class="btn btn-info" href="#">Cancel</a>
	</span>
	<script>
		$("#st_date_upd").datepicker(
		{
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
		$("#upd_med_plan_det").css('height','200px');
		//$("#con_doc").select2({ theme: "classic" });
	</script>
	<style>
		.table tr:hover{background:none;}
	</style>
	<?php
}

if($_POST["type"]=="pat_ipd_ip_consult")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	?>
	<!--<select id="ip_view" onchange="">
		<option value="1">Current</option>
		<option value="2">All</option>
	</select>-->
	<?php
	$qry=mysqli_query($link,"SELECT DISTINCT `date` FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `date` DESC");
	while($res=mysqli_fetch_array($qry))
	{
		$q=mysqli_query($link,"SELECT * FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$res[date]'");
		$num=mysqli_num_rows($q);
		if($num>0)
		{
		?>
		<div><b>Date: <?php echo convert_date_g($res['date']);?></b></div>
		<table class="table table-condensed table-bordered">
			<tr>
				<th width="5%">#</th><th width="40%">Note</th><th>Doctor</th><th width="15%">Date</th><th width="20%"></th>
			</tr>
			<?php
			$n=1;
			if($res['date']==$date)
			$dis="";
			else
			$dis="disabled='disabled'";
			while($r=mysqli_fetch_array($q))
			{
				$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$r[consultantdoctorid]'"));
				if($r['note']=="")
				$btn1="Add Note";
				else
				$btn1="Edit Note";
			?>
			<tr>
				<td><?php echo $n;?></td><td><?php echo $r['note'];?></td><td><?php echo $doc['Name'];?></td><td><?php echo convert_date_g($r['date'])." ".convert_time($r['time']);?></td><td><button type="button" class="btn btn-mini btn-info" onclick="ip_note('<?php echo $r['id'];?>')" <?php echo $dis;?>><?php echo $btn1;?></button></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
		<?php
		}
		if($res['date']==$date)
		{
			?>
			<button type="button" class="btn btn-primary" onclick="ipd_save_note()"><i class="icon-plus"></i> Add Doctor</button>
			<?php
		}
	}
}

if($_POST["type"]=="ipd_ip_note_edit")
{
	$id=$_POST['id'];
	$n=mysqli_fetch_array(mysqli_query($link,"SELECT `note`,`consultantdoctorid` FROM `ipd_ip_consultation` WHERE `id`='$id'"));
	if($n['note'])
	$note=$n['note'];
	else
	$note="";
	?>
	<table class="table table-condensed" id="note_tbl">
		<tr>
			<td>
				<b>Note</b><br/>
				<textarea id="ip_note" style="width:500px;resize:none;"><?php echo $note;?></textarea><br/>
				<button type="button" class="btn btn-success" data-dismiss="modal" onclick="save_ip_note('<?php echo $id;?>')">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</td>
		</tr>
	</table>
	<style>
		#note_tbl tr:hover{background:none;}
	</style>
	<?php
}

if($_POST["type"]=="ipd_ip_note_doc_edit")
{
	$id=$_POST['id'];
	$n=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `ipd_ip_consultation` WHERE `id`='$id'"));
	?>
	<table class="table table-condensed" id="note_tbl">
		<tr>
			<td>
				<select id="ip_note_doc">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `doc_type`!='1' ORDER BY `Name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['consultantdoctorid'];?>" <?php if($n['consultantdoctorid']==$r['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $r['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				<button type="button" class="btn btn-success" data-dismiss="modal" onclick="save_ip_note_doc('<?php echo $id;?>')">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</td>
		</tr>
	</table>
	<style>
		#note_tbl tr:hover{background:none;}
	</style>
	<?php
}

if($_POST["type"]=="opd_prev_record")
{
	/*$uhid=$_POST['uhid'];
	$usr=$_POST['usr'];
	$n=1;
	$q=mysqli_query($link,"SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `consultantdoctorid` IN (SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$usr')");
	$q_num=mysqli_num_rows($q);
	while($r=mysqli_fetch_array($q))
	{
		if($n==$q_num){ $pr="1"; }else{ $pr="0"; }
		?>
		<div class="text-center">
			<button type="button" class="btn btn-default" style="width:200px;" onclick="prev_view('<?php echo $r['opd_id'];?>','<?php echo $pr;?>')"><?php echo "<span class='text-left'>".$n.".</span> ".convert_date_g($r['appointment_date']);?></button>
		</div>
		<?php
		$n++;
	}
	*/
	//-------------------------------------------------------//
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$opd_crr=$_POST['opd_crr'];
	$usr=$_POST['usr'];
	$n=1;
	$qry=mysqli_query($link,"SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' ORDER BY `appointment_date` DESC");
	?>
	<center><table class="table table-condensed rec_tbl" style="width:50%;">
	<?php
	while($r=mysqli_fetch_array($qry))
	{
		//$q_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `consultantdoctorid`='$usr'"));
		if($opd_crr==$r['opd_id'])
		{ $pr="1"; }
		else
		{ $pr="0"; }
		if($r['appointment_date']>$date)
			{$tomoro="disabled='disabled'";}
		else
			{$tomoro="";}
		$c_val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_confidential` WHERE `patient_id`='$uhid' AND `opd_id`='$r[opd_id]'"));
		$u=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$r[opd_id]'"));
		$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `Name`,`emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$r[consultantdoctorid]'"));
		$hidden=0;
		if($c_val['confident']==1)
		{
			if($usr==$emp['emp_id'])
			{
				$class="btn btn-default btn-block btn-info";
				$btn_dsb="";
				$doctor="Your Visit";
				$hidden=0;
				$view=0;
				$bg="#B5E9B8";
				$text="";
			}
			else
			{
				$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$u[consultantdoctorid]'"));
				$class="btn btn-danger btn-block";
				$btn_dsb="disabled='disabled'";
				$doctor=$doc['Name'];
				$hidden=1;
				$view=1;
				$bg="";
				$text="Confidential";
			}
		}
		else
		{
			if($usr==$emp['emp_id'])
			{
				$class="btn btn-default btn-block btn-info";
				$btn_dsb="";
				$doctor="Your Visit";
				$hidden=0;
				$view=0;
				$bg="#B5E9B8";
				$text="";
			}
			else
			{
				$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$u[consultantdoctorid]'"));
				$class="btn btn-default btn-block btn-primary";
				$btn_dsb="";
				$doctor=$doc['Name'];
				$hidden=0;
				$view=1;
				$bg="";
				$text="View";
			}
		}
		if($view==0)
		{
			if($u['appointment_date']==$date)
			{
				$class="btn btn-block btn-success";
				$doctor="Your Current Visit";
				$bg="#FFFFBB";
			}
			else
			{
				$class="btn btn-block btn-info";
				$doctor="Your Visit";
				$bg="#B5E9B8";
			}
		}
		//echo $pr;
		//if($hidden==0)
		{
			if($view==1)
			{
				if($hidden==1)
				{
					$clk="";
				}
				else if($hidden==0)
				{
					$clk="view_presc('$uhid','$r[opd_id]')";
				}
			?>
			<tr onclick="<?php echo $clk;?>" style="cursor:pointer;background:<?php echo $bg;?>;">
				<td><?php echo $n;?>.</td>
				<td><?php if($doctor){echo convert_date_g($r['appointment_date']);}?></td>
				<td><?php if($doctor){echo $doctor;}?></td>
				<td><?php if($hidden==1){echo "<span class='text-danger'>$text</span>";}if($hidden==0){echo "<span class='text-info'>$text</span>";}?></td>
				<!--<td>
					<span class="text-right text-info">
						<button type="button" class="btn btn-default" onclick="view_presc('<?php echo $uhid;?>','<?php echo $r['opd_id'];?>')">View</button>
					</span>	
				</td>-->
			</tr>
			<?php
			}
			else if($view==0)
			{
			?>
			<tr onclick="prev_view('<?php echo $r['opd_id'];?>','<?php echo $pr;?>')" style="cursor:pointer;background:<?php echo $bg;?>;">
				<td><?php echo $n;?>.</td>
				<td><?php if($doctor){echo convert_date_g($r['appointment_date']);}?></td>
				<td><?php if($doctor){echo $doctor;}?></td>
				<td></td>
				<!--<td>
					<span class="text-right text-info">
						<button type="button" <?php echo $class;?> onclick="prev_view('<?php echo $r['opd_id'];?>','<?php echo $pr;?>')" <?php echo $btn_dsb;?> <?php echo $tomoro;?>></button>
					</span>	
				</td>-->
			</tr>
			<?php
			}
		$n++;
		}
	}
	?>
	</table></center>
	<style>
	.rec_tbl tr{border-top: 2px solid;border-bottom: 2px solid;}
	.text-danger{color:#E31818;}
	.text-info{color:#1A3EB1;}
	</style>
	<?php
}

if($_POST["type"]=="pat_ipd_room_status")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	$qq1=mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$qq2=mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `bed_id` NOT IN (SELECT `bed_id` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')");
	$n1=mysqli_num_rows($qq1);
	$n2=mysqli_num_rows($qq2);
	
	$dis_det=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($dis_det>0)
	{
		$btndis="disabled='disabled'";
		$btnname="Discharged";
	}
	else
	{
		$btndis="";
		$btnname="Bed Transfer";
	}
	
	if($n1==0 && $n2>0)
	{
		echo '<button type="button" class="btn btn-info" onclick="ipd_pat_bed_alloc()"><i class="icon icon-check"></i> Accept</button>';
	}
	else if($n1>0 && $n2>0)
	{
		$qry=mysqli_query($link,"SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` DESC");
		$num=mysqli_num_rows($qry);
		if($num>0)
		{
			?>
			<table class="table table-condensed table-bordered">
				<tr>
					<th>Ward</th><th>Bed No</th><th>Occupied On</th><th>Released On</th><th>User</th>
				</tr>
			<?php
			$zz=1;
			while($res=mysqli_fetch_array($qry))
			{
				$ldt=mysqli_fetch_array(mysqli_query($link,"SELECT `time`,`date`,`user` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `ward_id`='$res[ward_id]' AND `bed_id`='$res[bed_id]' AND `alloc_type`='0' AND `slno`>'$res[slno]' ORDER BY `slno` ASC LIMIT 0,1"));
				$wd=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$res[ward_id]'"));
				$bd=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no`,`charges` FROM `bed_master` WHERE `bed_id`='$res[bed_id]'"));
				$emp_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$ldt[user]'"));
				//~ if($ldt['date']=="")
				if($num==$zz)
				{
					//$dt=date("d-M-Y");
					$dt="Still Occupied";
				}
				else
				{
					$dt=convert_date_g($ldt['date'])." / ".convert_time($ldt['time']);
				}
				?>
				<tr>
					<td><?php echo $wd['name'];?></td><td><?php echo $bd['bed_no'];?></td><td><?php echo convert_date_g($res['date'])." / ".convert_time($res['time']);?></td><td><?php echo $dt;?></td><td><?php echo $emp_info["name"];?></td>
				</tr>
		<?php
				$zz++;
			}
			?>
			</table>
			<button type="type" class="btn btn-info" onclick="nursing_bed_transfer()" <?php echo $btndis; ?>><i class="icon icon-forward"></i> <?php echo $btnname; ?></button>
			<?php
			if($n2>0)
			{
				$f=mysqli_fetch_array($qq2);
				$w=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$f[ward_id]'"));
				$b=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no` FROM `bed_master` WHERE `bed_id`='$f[bed_id]'"));
				$ward=$w['name'];
				$bed=$b['bed_no'];
				$style="";
			}
			else
			{
				$ward="";
				$bed="";
				$style="display:none;";
			}
			?>
			<div id="bed_info" <?php echo $style; ?>>
				<b>Selected</b><br/>Ward: <?php echo $ward; ?><br/>Bed No: <?php echo $bed; ?>
				<input type='hidden' id='ward_id' value="<?php echo $f['ward_id']; ?>" />
				<input type='hidden' id='bed_id' value="<?php echo $f['bed_id']; ?>" />
			</div>
			<div id="bed_btn_info" <?php echo $style; ?>>
				<input type="text" class="datepicker" id="bed_transfer_date" value="<?php echo date("Y-m-d"); ?>"><br>
				<button type='button' class='btn btn-primary' onclick='bed_assign_ok()'>Update</button>
				<button type='button' class='btn btn-danger' onclick='clr_bed_assign()'>Cancel</button>
			</div>
			<?php
		}
	}
	else
	{
		$qry=mysqli_query($link,"SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` ASC");
		$num=mysqli_num_rows($qry);
		if($num>0)
		{
			?>
			<table class="table table-condensed table-bordered">
				<tr>
					<th>Ward</th><th>Bed No</th><th>Occupied On</th><th>Released On</th><th>User</th>
				</tr>
			<?php
			$zz=1;
			while($res=mysqli_fetch_array($qry))
			{
				$ldt=mysqli_fetch_array(mysqli_query($link,"SELECT `time`,`date`,`user` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `ward_id`='$res[ward_id]' AND `bed_id`='$res[bed_id]' AND `alloc_type`='1' ORDER BY `slno` ASC LIMIT 0,1"));
				
				$wd=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$res[ward_id]'"));
				$bd=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no`,`charges` FROM `bed_master` WHERE `bed_id`='$res[bed_id]'"));
				$emp_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$ldt[user]'"));
				
				//~ if($ldt['date']=="")
				if($num==$zz)
				{
					//$dt=date("d-M-Y");
					$dt="Still Occupied";
					$edit_btn="<button class='btn btn-default  text-right' onclick=\"nursing_bed_transfer('1')\" $btndis><i class='icon-edit'></i> Bed Edit</button>";
				}
				else
				{
					$dt=convert_date_g($ldt['date'])." / ".convert_time($ldt['time']);
					$edit_btn="";
				}
				?>
				<tr>
					<td><?php echo $wd['name'];?></td><td><?php echo $bd['bed_no'];?></td><td><?php echo convert_date_g($res['date'])." / ".convert_time($res['time']);?></td><td><?php echo $dt.$edit_btn;?></td><td><?php echo $emp_info["name"];?></td>
				</tr>
		<?php
				$zz++;
			}
			?>
			</table>
			<button type="type" class="btn btn-info" onclick="nursing_bed_transfer('0')" <?php echo $btndis; ?>><i class="icon icon-forward"></i> <?php echo $btnname; ?></button>
			<div id="bed_info"></div>
			<div id="bed_btn_info" style="display:none;">
				<input type="text" class="datepicker" id="bed_transfer_date" value="<?php echo date("Y-m-d"); ?>"><br>
				<button type='button' class='btn btn-primary' onclick='bed_assign_ok()'>Update</button>
				<button type='button' class='btn btn-danger' onclick='clr_bed_assign()'>Cancel</button>
			</div>
			<?php
		}
	}
}

if($_POST["type"]=="pat_ipd_equipment")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$qry=mysqli_query($link,"SELECT * FROM `ipd_pat_equipment` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `date`");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Equipment</th><th>No of Hour(s)</th><th>Date</th>
		</tr>
		<?php
		$n=1;
		while($res=mysqli_fetch_array($qry))
		{
			$eq=mysqli_fetch_array(mysqli_query($link,"SELECT `equipment_name` FROM `ipd_equipment` WHERE `equipment_id`='$res[equipment_id]'"));
		?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $eq['equipment_name'];?></td><td><?php echo $res['hours'];?></td><td><?php echo convert_date_g($res['date'])." ".convert_time($res['time']);?></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<button type="button" id="add_eq" class="btn btn-info" onclick="add_equip()"><i class="icon-plus"></i> Add More Equipment</button>
	<?php
	}
	else
	{
	?>
		<button type="button" id="add_eq" class="btn btn-info" onclick="add_equip()"><i class="icon-plus"></i> Add Equipment</button>
	<?php
	}
	?>
	<div id="add_equip" style="display:none;">
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Select</th>
				<td>
					<select id="equip">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT `equipment_id`,`equipment_name` FROM `ipd_equipment` ORDER BY `equipment_name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['equipment_id'];?>"><?php echo $r['equipment_name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
				<th>No of Hours</th>
				<td>
					<select id="hour">
						<option value="0">Select</option>
						<?php
						for($i=1;$i<=24;$i++)
						{
						?>
						<option value="<?php echo $i;?>"><?php echo $i;?> Hour(s)</option>
						<?php
						}
						?>
					</select>
				</td>
				<td>
					<button type="button" class="btn btn-info" onclick="save_equip()"><i class="icon-file"></i> Save</button>
					<button type="button" class="btn btn-danger" onclick="close_equip()"><i class="icon-ban-circle"></i> Cancel</button>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

if($_POST["type"]=="pat_ipd_consumable")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$qry=mysqli_query($link,"SELECT * FROM `ipd_pat_consumable` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `date`");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Equipment</th><th>Quantity</th><th>Date</th>
		</tr>
		<?php
		$n=1;
		while($res=mysqli_fetch_array($qry))
		{
			$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_indent_master` WHERE `slno`='$res[consumable_id]'"));
		?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $c['name'];?></td><td><?php echo $res['quantity'];?></td><td><?php echo convert_date_g($res['date'])." ".convert_time($res['time']);?></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<button type="button" id="add_con" class="btn btn-info" onclick="add_consume()"><i class="icon-plus"></i> Add More Consumable</button>
	<?php
	}
	else
	{
	?>
		<button type="button" id="add_con" class="btn btn-info" onclick="add_consume()"><i class="icon-plus"></i> Add Consumable</button>
	<?php
	}
	?>
	<div id="add_consume" style="display:none;">
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Select</th>
				<td>
					<select id="consume">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT `slno`,`name` FROM `inv_indent_master` WHERE `type_id`='TYPE2' ORDER BY `name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['slno'];?>"><?php echo $r['name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
				<th>Quantity</th>
				<td>
					<input type="text" id="consume_qnt" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');physical(this.value,event)" placeholder="Quantity" />
				</td>
				<td>
					<button type="button" class="btn btn-info" onclick="save_consumable()"><i class="icon-file"></i> Save</button>
					<button type="button" class="btn btn-danger" onclick="close_consumable()"><i class="icon-ban-circle"></i> Close</button>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

if($_POST["type"]=="pat_ipd_sur_consumable")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$qry=mysqli_query($link,"SELECT * FROM `ipd_pat_sur_consumable` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `date`");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Equipment</th><th>Quantity</th><th>Date</th>
		</tr>
		<?php
		$n=1;
		while($res=mysqli_fetch_array($qry))
		{
			$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_indent_master` WHERE `slno`='$res[consumable_id]'"));
		?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $c['name'];?></td><td><?php echo $res['quantity'];?></td><td><?php echo convert_date_g($res['date'])." ".convert_time($res['time']);?></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<button type="button" id="add_con" class="btn btn-info" onclick="add_sur_consume()"><i class="icon-plus"></i> Add More Consumable</button>
	<?php
	}
	else
	{
	?>
		<button type="button" id="add_con" class="btn btn-info" onclick="add_sur_consume()"><i class="icon-plus"></i> Add Consumable</button>
	<?php
	}
	?>
	<div id="add_sur_consume" style="display:none;">
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Select</th>
				<td>
					<select id="consume1">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT `slno`,`name` FROM `inv_indent_master` WHERE `type_id`='TYPE1' ORDER BY `name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['slno'];?>"><?php echo $r['name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
				<th>Quantity</th>
				<td>
					<input type="text" id="consume_qnt1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');physical(this.value,event)" placeholder="Quantity" />
				</td>
				<td>
					<button type="button" class="btn btn-info" onclick="save_sur_consumable()"><i class="icon-file"></i> Save</button>
					<button type="button" class="btn btn-danger" onclick="close_sur_consumable()"><i class="icon-ban-circle"></i> Close</button>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

if($_POST["type"]=="pat_ipd_ad_med_emer")
{
	?>
	<table class="table table-condensed">
		<tr>
			<th>Drug Name</th>
			<td>
				<select id="drug">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `item_id`,`item_name` FROM `item_master` ORDER BY `item_name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['item_id'];?>"><?php echo $r['item_name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<th>Dosage</th>
			<td>
				<select id="dose">
					<option value="0">Select</option>
					<?php
					for($j=1;$j<=5;$j++)
					{
					?>
					<option value="<?php echo $j;?>"><?php echo $j;?></option>
					<?php
					}
					?>
				</select>
			</td>
			<th>Frequency</th>
			<td>
				<select id="freq">
					<option value="0">Select</option>
					<option value="1">Immediately</option>
					<option value="2">Once a day</option>
					<option value="3">Twice a day</option>
					<option value="4">Thrice a day</option>
					<option value="5">Four times a day</option>
					<option value="6">Five times a day</option>
					<option value="7">Every hour</option>
					<option value="8">Every 2 hours</option>
					<option value="9">Every 3 hours</option>
					<option value="10">Every 4 hours</option>
					<option value="11">Every 5 hours</option>
					<option value="12">Every 6 hours</option>
					<option value="13">Every 7 hours</option>
					<option value="14">Every 8 hours</option>
					<option value="15">Every 10 hours</option>
					<option value="16">Every 12 hours</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Duration</th>
			<td>
				<select id="dur">
					<option value="0">Select</option>
					<?php
					for($j=1;$j<=10;$j++)
					{
					?>
					<option value="<?php echo $j;?>"><?php echo $j;?></option>
					<?php
					}
					?>
				</select>
			</td>
			<th>Unit Days</th>
			<td>
				<select id="unit_day">
					<option value="0">Select</option>
					<option value="Days">Days</option>
					<option value="Weeks">Weeks</option>
					<option value="Months">Months</option>
				</select>
			</td>
			<th>Instruction</th>
			<td>
				<select id="inst">
					<option value="1">As Directed</option>
					<option value="2">Before Meal</option>
					<option value="3">Empty Stomach</option>
					<option value="4">After Meal</option>
					<option value="5">In the Morning</option>
					<option value="6">In the Evening</option>
					<option value="7">At Bedtime</option>
					<option value="8">Immediately</option>
				</select>
			</td>
		</tr>
	</table>
	<span class="text-right">
		<a data-dismiss="modal" onclick="ad_med_emer_set();$('#upd_med_plan_det').css('height','100px');" class="btn btn-primary" href="#">Save</a>
		<a data-dismiss="modal" onclick="$('#upd_med_plan_det').css('height','100px');" class="btn btn-info" href="#">Cancel</a>
	</span>
	<script>
		$("#st_date_upd").datepicker(
		{
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
		$("#upd_med_plan_det").css('height','200px');
	</script>
	<?php
}

if($_POST["type"]=="ipd_ip_add_doc")
{
	?>
	<table class="table table-condensed" id="note_tbl">
		<tr>
			<td>
				<b>Note</b><br/>
				<textarea id="ip_note" style="width:500px;resize:none;"><?php echo $note;?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<b>Doctor</b><br/>
				<select id="con_doc">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['consultantdoctorid'];?>"><?php echo $r['Name'];?></option>
					<?php
					}
					?>
				</select>
				<br/>
				<button type="button" class="btn btn-success" data-dismiss="modal" onclick="ipd_save_new_note()">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</td>
		</tr>
	</table>
	<style>
		#note_tbl tr:hover{background:none;}
	</style>
	<?php
}

if($_POST["type"]=="ipd_pat_chief_complain")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	?>
	<table class="table table-condensed" id="hist_table">
<?php
	$opdq=mysqli_query($link,"SELECT * FROM `pat_complaints` WHERE `patient_id`='$uhid'");
	$ipdq=mysqli_query($link,"SELECT * FROM `ipd_pat_complaints` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num1=mysqli_num_rows($opdq);
	$num2=mysqli_num_rows($ipdq);
	$nm=1;
	$nn=1;
	if($num1>0)
	{
	?>
	<tr>
		<th colspan="4">OPD Complains</th>
	</tr>
	<?php
	while($rr=mysqli_fetch_array($opdq))
	{
?>
	<tr>
		<th><?php echo $nn; ?></th>
		<td><?php echo $rr['comp_one']; ?></td>
		<td><b>For</b> <?php echo $rr['comp_two']." ".$rr['comp_three']; ?></td>
		<td>Date: <?php echo convert_date_g($rr['date'])." ".convert_time($rr['time']); ?></td>
	</tr>
<?php
$nn++;
	}
	}
	if($num2>0)
	{
	?>
	<tr>
		<th colspan="4">IPD Complains</th>
	</tr>
	<?php
	while($r=mysqli_fetch_array($ipdq))
	{
?>
	<tr>
		<th><?php echo $nm; ?></th>
		<td><?php echo $r['comp_one']; ?></td>
		<td><b>For</b> <?php echo $r['comp_two']." ".$r['comp_three']; ?></td>
		<td>Date: <?php echo convert_date_g($r['date'])." ".convert_time($r['time']); ?></td>
	</tr>
<?php
$nm++;
	}
	}
	else
	{
	?>
	<tr class="cc" id="tr0">
		<th>Chief Complaints</th>
		<td>
			<input list="browsrs0" type="text" id="chief0" value="" onkeyup="sel_chief(this.id,0,event)" />
			<datalist id="browsrs0">
			<?php
				$qq = mysqli_query($link," SELECT * FROM `complain_master`");
				while($cc=mysqli_fetch_array($qq))
				{
					echo "<option value='$cc[complain]'>";
				}
			?>
			</datalist>
		</td>
		<td>
			<b>For</b> 
			<select id="cc0" class="span2" onkeyup="sel_chief(this.id,0,event)">
				<option value="0">Select</option>
				<?php
				for($n=1;$n<=30;$n++)
				{
				?>
				<option value="<?php echo $n;?>"><?php echo $n;?></option>
				<?php
				}
				?>
			</select>
			<select id="tim0" class="span2" onkeyup="sel_chief(this.id,0,event)">
				<option value="0">--Select--</option>
				<option value="Minutes">Minutes</option>
				<option value="Hours">Hours</option>
				<option value="Days">Days</option>
				<option value="Week">Week</option>
				<option value="Month">Month</option>
				<option value="Year">Year</option>
			</select>
		</td>
		<td>
			<span style="float:right"><button type="button" id="addmore" class="btn btn-info" onclick="add_row(1)"><i class="icon-plus"></i> Add More</button></span>
		</td>
	</tr>
	<?php
	}
	if($num2>0)
	{
	?>
	<tr>
		<td colspan="4">
			<span style="float:right"><button type="button" id="addmore" class="btn btn-info" onclick="add_row(1)"><i class="icon-plus"></i> Add More</button></span>
		</td>
	</tr>
	<?php
	}
?>
	<tr id="hh">
		<td colspan="4">
		<span style="float:right"><button type="button" id="addmore" class="btn btn-info" onclick="insert_complain()"><i class="icon-save"></i> Save</button></span>
		</td>
	</tr>
</table>
<?php
}

if($_POST["type"]=="ipd_pat_past_history")
{
	$uhid=$_POST['uhid'];
	$q=mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$d=mysqli_fetch_array($q);
		?>
		<table class="table table-condensed" id="">
			<tr>
				<th width="10%">History</th>
				<td width="80%"><textarea id="p_hist" placeholder="History" style="width:98%;height:100px;resize:none;"><?php echo $d['history'];?></textarea></td>
				<td><input type="button" class="btn btn-info" onclick="update_hist()" value="Update" /></td>
			</tr>
		</table>
		<?php
	}
	else
	{
		?>
		<table class="table table-condensed" id="">
			<tr>
				<th width="10%">History</th>
				<td width="80%"><textarea id="p_hist" placeholder="History" style="width:98%;height:100px;resize:none;"></textarea></td>
				<td><input type="button" class="btn btn-info" onclick="update_hist()" value="Save" /></td>
			</tr>
		</table>
		<?php
	}
}

if($_POST["type"]=="ipd_pat_examination")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$q=mysqli_query($link,"SELECT * FROM `ipd_pat_examination` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		?>
		<table class="table table-condensed" id="">
			<tr>
				<th>#</th><th width="70%">Examination</th><th>Date</th>
			</tr>
			<?php
			$n=1;
			while($r=mysqli_fetch_array($q))
			{
			?>
			<tr>
				<td><?php echo $n;?></td><td><?php echo $r['examination'];?></td><td><?php echo convert_date_g($r['date'])." ".convert_time($r['time']);?></td>
			</tr>
			<?php
			$n++;
			}
			?>
			<tr>
				<td colspan="3">
					<textarea id="exam" placeholder="Examination" style="width:96%;height:100px;resize:none;"></textarea><br/>
					<button type="button" class="btn btn-info" onclick="save_exam()"><i class="icon-save"></i> Save</button>
				</td>
			</tr>
		</table>
		<?php
	}
	else
	{
		?>
		<table class="table table-condensed" id="">
			<tr>
				<td>Examination<br/>
					<textarea id="exam" placeholder="Examination" style="width:96%;height:100px;resize:none;"></textarea><br/>
					<input type="button" class="btn btn-info" onclick="save_exam()" value="Save" />
				</td>
			</tr>
		</table>
		<?php
	}
}

if($_POST["type"]=="ipd_pat_discharge_summ")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$q=mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$det=mysqli_fetch_array($q);
		$course=$det['course'];
		$fd=$det['final_diagnosis'];
		$foll=$det['follow_up'];
		$dis="";
		$value="Update";
	}
	else
	{
		$course="";
		$fd="";
		$foll="";
		$dis="disabled='disabled'";
		$value="Save";
	}
	?>
	<table class="table table-condensed" id="">
			<tr>
				<td><b>Course in hospital</b><br/>
					<textarea id="course" placeholder="Course in hospital" onkeyup="tab(this.id,event)" style="width:96%;height:100px;resize:none;"><?php echo $course; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><b>Final diagnosis</b><br/>
					<textarea id="final_diag" placeholder="Final diagnosis" onkeyup="tab(this.id,event)" style="width:96%;height:100px;resize:none;"><?php echo $fd; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><b>Follow up</b><br/>
					<textarea id="foll" placeholder="Follow up" onkeyup="tab(this.id,event)" style="width:96%;height:100px;resize:none;"><?php echo $foll; ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<button type="button" id="summ_btn" class="btn btn-info" onclick="insert_disc_summ()">
						<i class="icon-file"></i> <?php echo $value; ?>
					</button>
				</td>
			</tr>
			<tr>
				<td><b>Post Discharge Medication Plan</b><br/>
					<table class="table table-condensed" id="">
						<tr>
							<th>#</th>
							<th>Drugs</th>
							<th>Dosage</th>
							<th>Frequency</th>
							<th>Duration</th>
							<th>Instruction</th>
						</tr>
						<?php
						$i=1;
						$qr=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_post_discharge` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
						while($rr=mysqli_fetch_array($qr))
						{
							$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$rr[item_code]'"));
							if($rr['frequency']==1)
							$fq="Immediately";
							if($rr['frequency']==2)
							$fq="Once a day";
							if($rr['frequency']==3)
							$fq="Twice a day";
							if($rr['frequency']==4)
							$fq="Thrice a day";
							if($rr['frequency']==5)
							$fq="Four times a day";
							if($rr['frequency']==6)
							$fq="Five times a day";
							if($rr['frequency']==7)
							$fq="Every Hour";
							if($rr['frequency']==8)
							$fq="Every 2 Hours";
							if($rr['frequency']==9)
							$fq="Every 3 Hours";
							if($rr['frequency']==10)
							$fq="Every 4 Hours";
							if($rr['frequency']==11)
							$fq="Every 5 Hours";
							if($rr['frequency']==12)
							$fq="Every 6 Hours";
							if($rr['frequency']==13)
							$fq="Every 7 Hours";
							if($rr['frequency']==14)
							$fq="Every 8 Hours";
							if($rr['frequency']==15)
							$fq="Every 10 Hours";
							if($rr['frequency']==16)
							$fq="Every 12 Hours";
							
							if($rr['instruction']==1)
							$ins="As Directed";
							if($rr['instruction']==2)
							$ins="Before Meal";
							if($rr['instruction']==3)
							$ins="Empty Stomach";
							if($rr['instruction']==4)
							$ins="After Meal";
							if($rr['instruction']==5)
							$ins="In the Morning";
							if($rr['instruction']==6)
							$ins="In the Evening";
							if($rr['instruction']==7)
							$ins="At Bedtime";
							if($rr['instruction']==8)
							$ins="Immediately";
						?>
						<tr>
							<td><?php echo $i; ?></td>
							<td><?php echo $itm['item_name']; ?></td>
							<td><?php echo $rr['dosage']; ?></td>
							<td><?php echo $fq; ?></td>
							<td><?php echo $rr['duration']." ".$rr['unit_days']; ?></td>
							<td><?php echo $ins; ?></td>
						</tr>
						<?php
						$i++;
						}
						?>
					</table>
					<button type="button" class="btn btn-info" onclick="post_drugs()">
						<i class="icon-plus"></i> Add Medicine
					</button>
				</td>
			</tr>
			<tr>
				<td>
					<span class="text-right"><button type="button" class="btn btn-primary" onclick="print_disc_summary()" <?php echo $dis; ?>>
						<i class="icon-print"></i> Print
					</button></span>
				</td>
			</tr>
		</table>
	<?php
}

if($_POST["type"]=="load_donor_type_id")
{
	$i=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(type_id) as max FROM `blood_donor_type`"));
	echo $i['max']+1;
}

if($_POST["type"]=="display_donor_type")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_type` WHERE `name` like '$srch%' ORDER BY `name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_type` ORDER BY `name`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
			<tr>
				<th>SN</th><th>Type</th><th></th>
			</tr>
			<?php
			$n=1;
			while($r=mysqli_fetch_array($q))
			{
			?>
			<tr>
				<td><?php echo $n;?></td><td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['type_id'];?>')"><?php echo $r['name'];?></td><td><i class="icon-remove icon-large text-danger" onclick="confirmm('<?php echo $r['type_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
	<?php
}

if($_POST["type"]=="load_details_donor_type")
{
	$id=$_POST['id'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `blood_donor_type` WHERE `type_id`='$id'"));
	echo $id."#ea#".$v['name']."#ea#";
}

if($_POST["type"]=="load_pack_id")
{
	$i=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(pack_id) as max FROM `blood_pack_master`"));
	echo $i['max']+1;
}

if($_POST["type"]=="load_pack_list")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_pack_master` WHERE `name` like '$srch%' ORDER BY `name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_pack_master` ORDER BY `name`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
			<tr>
				<th>SN</th><th>Pack Name</th><th></th>
			</tr>
			<?php
			$n=1;
			while($r=mysqli_fetch_array($q))
			{
			?>
			<tr>
				<td><?php echo $n;?></td><td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['pack_id'];?>')"><?php echo $r['name'];?></td><td><i class="icon-remove icon-large text-danger" onclick="confirmm('<?php echo $r['pack_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
	<?php
}

if($_POST["type"]=="load_details_pack_master")
{
	$id=$_POST['id'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `blood_pack_master` WHERE `pack_id`='$id'"));
	echo $id."#ea#".$v['name']."#ea#";
}

if($_POST["type"]=="load_donor_list")
{
	$name=$_POST['name'];
	$id=$_POST['id'];
	$contact=$_POST['contact'];
	$q=mysqli_query($link,"SELECT * FROM `blood_donor_reg` WHERE `donor_id` NOT IN (SELECT `donor_id` FROM `blood_donor_rejected`) ORDER BY `date` DESC LIMIT 0,20");
	if($name)
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_reg` WHERE `name` like '$name%' AND `donor_id` NOT IN (SELECT `donor_id` FROM `blood_donor_rejected`) ORDER BY `name`");
	}
	if($id)
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_reg` WHERE `donor_id` like '$id%' AND `donor_id` NOT IN (SELECT `donor_id` FROM `blood_donor_rejected`) ORDER BY `name`");
	}
	if($contact)
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_reg` WHERE `contact` like '$contact%' AND `donor_id` NOT IN (SELECT `donor_id` FROM `blood_donor_rejected`) ORDER BY `name`");
	}
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>ID</th><th>Name</th><th>Age/Sex/Weight</th><th>Blood Group</th><th>Contact</th><th>Last Donation</th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			if($r['abo']=="0")
			$abo="";
			else
			$abo=$r['abo'];
			if($r['rh']=="0")
			$rh="";
			else
			$rh=$r['rh'];
		?>
		<tr style="cursor:pointer;" onclick="gopage('<?php echo $r['donor_id'];?>')">
			<td><?php echo $n;?></td><td><?php echo $r['donor_id'];?></td><td><?php echo $r['name'];?></td><td><?php echo $r['age']." / ".$r['sex']." / ".$r['weight']." KG";?></td><td><?php echo $abo." ".$rh;?></td><td><?php echo $r['contact'];?></td><td><?php echo convert_date_g($r['last_donate']);?></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="show_donor_screw")
{
	$bar=$_POST['bar'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `donor_id`,`name` FROM `blood_donor_reg` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_receipt` WHERE `bar_code`='$bar')"));
	$qr=mysqli_query($link,"SELECT * FROM `blood_screwing_details` WHERE `donor_id`='$d[donor_id]' AND`bar_code`='$bar'");
	$n=mysqli_num_rows($qr);
	if($n>0)
	{
		$r=mysqli_fetch_array($qr);
		$abo=$r['abo'];
		$rh=$r['rh'];
		$hiv=$r['hiv'];
		$hepb=$r['hep_b'];
		$hepc=$r['hep_c'];
		$mp=$r['mp'];
		$vdrl=$r['vdrl'];
	}
	else
	{
		$abo=0;
		$rh=0;
		$hiv=0;
		$hepb=0;
		$hepc=0;
		$mp=0;
		$vdrl=0;
	}
	echo $d['donor_id']."#@#".$d['name']."#@#".$abo."#@#".$rh."#@#".$hiv."#@#".$hepb."#@#".$hepc."#@#".$mp."#@#".$vdrl."#@#";
}

if($_POST["type"]=="blood_donor_components")
{
	$bar=$_POST['bar'];
	$q=mysqli_query($link,"SELECT * FROM `blood_receipt` WHERE `bar_code`='$bar'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$st=mysqli_fetch_array(mysqli_query($link,"SELECT `status` FROM `blood_receipt` WHERE `bar_code`='$bar'"));
		if($st['status']==0)
		{
			$d=mysqli_fetch_array(mysqli_query($link,"SELECT `donor_id`,`name` FROM `blood_donor_reg` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_receipt` WHERE `bar_code`='$bar')"));
			$b=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `blood_pack_master` WHERE `pack_id` IN (SELECT `pack_id` FROM `blood_receipt` WHERE `donor_id`='$d[donor_id]' AND `bar_code`='$bar')"));
			echo $d['donor_id']."#@#".$d['name']."#@#".$b['pack_id']."#@#".$b['name']."#@#";
		}
		else if($st['status']==1)
		echo "1";
		else if($st['status']==2)
		echo "2";
	}
	else
	echo "";
}

if($_POST["type"]=="display_component_stock")
{
	$abo=$_POST['abo'];
	$rh=$_POST['rh'];
	$n=1;
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Component</th><th>Stock</th>
		</tr>
		<?php
		$q=mysqli_query($link,"SELECT DISTINCT `component_id` FROM `blood_component_stock` WHERE `bar_code` IN (SELECT `bar_code` FROM `blood_receipt` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_donor_inventory` WHERE `abo`='$abo' AND `rh`='$rh'))");
		while($r=mysqli_fetch_array($q))
		{
			$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_component_master` WHERE `component_id`='$r[component_id]'"));
			$count=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) AS tot FROM `blood_component_stock` WHERE `component_id`='$r[component_id]' AND `bar_code` IN (SELECT `bar_code` FROM `blood_receipt` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_donor_inventory` WHERE `abo`='$abo' AND `rh`='$rh'))"));
		?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $c['name'];?></td><td><?php echo $count['tot'];?></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="display_patient_details")
{
	$uhid=$_POST['uhid'];
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id`,`name`,`blood_group` FROM `patient_info` WHERE `uhid`='$uhid'"));
	$w=mysqli_fetch_array(mysqli_query($link,"SELECT `ward_id`,`bed_id` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$p[patient_id]' AND `alloc_type`='1' ORDER BY `slno` DESC LIMIT 0,1"));
	$ward=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$w[ward_id]'"));
	$bed=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no`,`room_id` FROM `bed_master` WHERE `ward_id`='$w[ward_id]' AND `bed_id`='$w[bed_id]'"));
	$room=mysqli_fetch_array(mysqli_query($link,"SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' AND `ward_id`='$w[ward_id]'"));
	//$ward=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id` IN (SELECT `ward_id` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid')"));
	if($p['blood_group'])
	{
		$bl=explode(" ",$p['blood_group']);
		$abo=$bl[0];
		$rh=$bl[1];
		$sel="selected='selected'";
		$dis="disabled='disabled'";
	}
	else
	{
		$abo="";
		$rh="";
		$sel="";
		$dis="";
	}
	if($uhid)
	{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Patient Name</th><th><?php echo $p['name'];?></th>
		</tr>
		<tr>
			<th>Ward</th><th><?php echo $ward['name'];?></th>
		</tr>
		<tr>
			<th>Room No</th><th><?php echo $room['room_no'];?></th>
		</tr>
		<tr>
			<th>Bed No</th><th><?php echo $bed['bed_no'];?></th>
		</tr>
		<tr>
			<th>Blood Group</th>
			<th>
				<select id="abo" class="span2" <?php echo $dis;?>>
					<option value="0">Select</option>
					<option value="A" <?php if($abo=="A"){echo $sel;}?>>A</option>
					<option value="B" <?php if($abo=="B"){echo $sel;}?>>B</option>
					<option value="AB" <?php if($abo=="AB"){echo $sel;}?>>AB</option>
					<option value="O" <?php if($abo=="O"){echo $sel;}?>>O</option>
				</select>
				<select id="rh" class="span2" <?php echo $dis;?>>
					<option value="0">Select</option>
					<option value="positive" <?php if($rh=="Positive"){echo $sel;}?>>Positive</option>
					<option value="negative" <?php if($rh=="Negative"){echo $sel;}?>>Negative</option>
				</select>
			</th>
		</tr>
		<tr>
			<th>Components Required</th>
			<th>
				<span id="chkk" style="padding:3px;padding-right:20px;">
					<label><input type="checkbox" id="rbc" value="rbc" /> RBC</label>
					<label><input type="checkbox" id="plat" value="plat" /> Platelets</label>
					<label><input type="checkbox" id="ffp" value="ffp" /> FFP</label>
					<label><input type="checkbox" id="cpp" value="cpp" /> CPP</label>
					<label><input type="checkbox" id="cryo" value="cryo" /> Cryo</label>
				</span>
			</th>
		</tr>
		<tr>
			<th>Number of units</th>
			<td>
				<input type="text" id="unit" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="Number of units" />
			</td>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;"><input type="button" class="btn btn-info" onclick="save()" value="Save" /></th>
		</tr>
	</table>
	<style>
		label{display:inline-block;margin-left:20px;}
		input[type="checkbox"]{margin:0;}
		.err{border:1px solid #ee0000;}
	</style>
	<?php
	}
}

if($_POST["type"]=="blood_pat_details")
{
	$uhid=$_POST['uhid'];
	if($uhid)
	{
		$p=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id`,`name`,`sex`,`dob`,`age`,`age_type`,`blood_group` FROM `patient_info` WHERE `uhid`='$uhid'"));
		$b=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_request` WHERE `patient_id`='$p[patient_id]'"));
		if($pat_info["dob"]!="")
		{
			$age=age_calculator($p["dob"])." (".$p["dob"].")";
		}
		else
		{
			$age=$p["age"]." ".$p["age_type"];
		}
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Patient Name</th><th><?php echo $p['name'];?></th>
			</tr>
			<tr>
				<th>Blood Group</th>
				<th><?php echo $b['abo']." ".$b['rh'];?></th>
			</tr>
			<tr>
				<th>Age / Sex</th>
				<th><?php echo $age." / ".$p['sex'];?></th>
			</tr>
			<tr>
				<td colspan="2" style="background:#dddddd;"></td>
			</tr>
			<tr>
				<th>Components Required</th><th>Units</th>
			</tr>
			<?php
			$q=mysqli_query($link,"SELECT `component_id`,`units` FROM `blood_request` WHERE `patient_id`='$p[patient_id]'");
			while($r=mysqli_fetch_array($q))
			{
				$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_component_master` WHERE `component_id`='$r[component_id]'"));
			?>
			<tr>
				<td><?php echo $c['name'];?></td><td><?php echo $r['units'];?></td>
			</tr>
			<?php
			}
			?>
		</table>
	<?php
	}
}

if($_POST["type"]=="blood_donor_details")
{
	$bar=$_POST['bar'];
	if($bar)
	{
		$d=mysqli_fetch_array(mysqli_query($link,"SELECT `type_id`,`name`,`weight`,`age`,`sex`,`last_donate` FROM `blood_donor_reg` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_donor_inventory` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_receipt` WHERE `bar_code`='$bar'))"));
		$t=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_donor_type` WHERE `type_id`='$d[type_id]'"));
		$bl=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_screwing_details` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_donor_inventory` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_receipt` WHERE `bar_code`='$bar'))"));
		if($d['name'])
		$yr=$d['weight']." / ".$d['age']." years / ".$d['sex'];
		else
		$yr="";
		if($d['name'])
		{
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Donor Name</th><th><?php echo $d['name'];?></th>
			</tr>
			<tr>
				<th>Blood Group</th><th><?php echo $bl['abo']." ".$bl['rh'];?></th>
			</tr>
			<tr>
				<th>Weight/Age/Sex</th><th><?php echo $yr;?></th>
			</tr>
			<tr>
				<th>Last Donation</th><th><?php echo convert_date_g($d['last_donate']);?></th>
			</tr>
			<tr>
				<td colspan="2" style="background:#dddddd;"></td>
			</tr>
			<tr>
				<th>Available Components</th><th>Expire Date</th>
			</tr>
			<?php
			$q=mysqli_query($link,"SELECT `component_id`,`expiry_date` FROM `blood_component_stock` WHERE `bar_code`='$bar'");
			while($r=mysqli_fetch_array($q))
			{
				$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_component_master` WHERE `component_id`='$r[component_id]'"));
			?>
			<tr>
				<td><?php echo $c['name'];?></td><td><?php echo convert_date_g($r['expiry_date']);?></td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php
		}
	}
}

if($_POST["type"]=="blood_compare_details")
{
	$uhid=$_POST['uhid'];
	$bar=$_POST['bar'];
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id` FROM `patient_info` WHERE `uhid`='$uhid'"));
	$p_bl=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_request` WHERE `patient_id`='$p[patient_id]'"));
	$d_bl=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_donor_inventory` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_receipt` WHERE `bar_code`='$bar')"));
	$rbc=mysqli_fetch_array(mysqli_query($link,"SELECT `status` FROM `blood_rbc_compatibility` WHERE `donor_abo`='$d_bl[abo]' AND `donor_rh`='$d_bl[rh]' AND `receiver_abo`='$p_bl[abo]' AND `receiver_rh`='$p_bl[rh]'"));
	$plas=mysqli_fetch_array(mysqli_query($link,"SELECT `status` FROM `blood_plasma_compatibility` WHERE `donor_abo`='$d_bl[abo]' AND `receiver_abo`='$p_bl[abo]'"));
	$plate=mysqli_fetch_array(mysqli_query($link,"SELECT `status` FROM `blood_platelet_compatibility` WHERE `donor_abo`='$d_bl[abo]' AND `donor_rh`='$d_bl[rh]' AND `receiver_abo`='$p_bl[abo]' AND `receiver_rh`='$p_bl[rh]'"));
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>RBC Compatibility</th>
			<th>Plasma Compatibility</th>
			<th>Platelet Compatibility</th>
		</tr>
		<tr>
			<td><?php if($rbc['status']=="1"){echo "Compatable";}else if($rbc['status']=="0"){echo "Not Compatable";}?></td>
			<td><?php if($plas['status']=="1"){echo "Compatable";}else if($plas['status']=="0"){echo "Not Compatable";}?></td>
			<td><?php if($plate['status']=="1"){echo "Compatable";}else if($plate['status']=="0"){echo "Not Compatable";}?></td>
		</tr>
	</table>
	<b>Crossmatch Type</b>
	<select id="cross">
		<option value="0">Select</option>
		<option value="1">Major Crossmatch</option>
		<option value="2">Minor Crossmatch</option>
	</select>
	<b>Agglutination</b>
	<select id="agg">
		<option value="0">Select</option>
		<option value="1">Yes</option>
		<option value="2">No</option>
	</select>
	<input type="button" class="btn btn-info" onclick="submitt()" value="Submit" />
	<?php
}

if($_POST["type"]=="blood_pat_issue")
{
	$uhid=$_POST['uhid'];
	if($uhid)
	{
		$p=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id`,`name`,`blood_group` FROM `patient_info` WHERE `uhid`='$uhid'"));
		$b=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_request` WHERE `patient_id`='$p[patient_id]'"));
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th width="20%">Patient Name</th><th colspan="2"><?php echo $p['name'];?></th>
			</tr>
			<tr>
				<th>Blood Group</th>
				<th width="20%"><?php echo $b['abo']." ".$b['rh'];?></th><th></th>
			</tr>
			<tr>
				<td colspan="3" style="background:#dddddd;"></td>
			</tr>
			<tr>
				<th>Components Required</th><th>Units Requested</th><th>Units Issued</th>
			</tr>
			<?php
			$jj=1;
			$q=mysqli_query($link,"SELECT `request_id`,`component_id`,`units`,`issued` FROM `blood_request` WHERE `patient_id`='$p[patient_id]' AND `units`!=`issued`");
			while($r=mysqli_fetch_array($q))
			{
				$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_component_master` WHERE `component_id`='$r[component_id]'"));
			?>
			<tr>
				<td>
					<?php echo $c['name'];?>
					<input type="text" id="tr<?php echo $jj;?>" value="<?php echo $r['component_id'];?>" style="display:none;" />
				</td>
				<td>
					<?php echo $r['units'];?>
					<input type="text" id="req<?php echo $jj;?>" value="<?php echo $r['request_id'];?>" style="display:none;" />
				</td>
				<td>
					<select class="span1 sel" id="sel<?php echo $jj;?>" onchange="bar_field(this.value,'bar<?php echo $jj;?>','<?php echo $r['component_id'];?>',event)">
						<option value="0">Select</option>
						<?php
						$loop=$r['units']-$r['issued'];
						for($i=1;$i<=$loop;$i++)
						{
						?>
						<option value="<?php echo $i;?>"><?php echo $i;?></option>
						<?php
						}
						?>
					</select>
					<span id="bar<?php echo $jj;?>"></span>
				</td>
			</tr>
			<?php
			$jj++;
			}
			?>
			<tr>
				<td colspan="3" style="text-align:center;"><input type="button" class="btn btn-info" onclick="blood_issue()" value="Issue" /></td>
			</tr>
		</table>
	<?php
	}
}

if($_POST["type"]=="blood_pat_issue_check")
{
	$uhid=$_POST['uhid'];
	$bar=$_POST['bar'];
	$comp=$_POST['comp'];
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id` FROM `patient_info` WHERE `uhid`='$uhid'"));
	$q=mysqli_query($link,"SELECT * FROM `blood_component_stock` WHERE `component_id`='$comp' AND `bar_code`='$bar'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		if($comp==1)
		{
			$p_bl=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_request` WHERE `patient_id`='$p[patient_id]'"));
			$d_bl=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_donor_inventory` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_receipt` WHERE `bar_code`='$bar')"));
			$rbc=mysqli_fetch_array(mysqli_query($link,"SELECT `status` FROM `blood_rbc_compatibility` WHERE `donor_abo`='$d_bl[abo]' AND `donor_rh`='$d_bl[rh]' AND `receiver_abo`='$p_bl[abo]' AND `receiver_rh`='$p_bl[rh]'"));
			if($rbc['status']==1)
			{
				echo "1";
			}
			if($rbc['status']==0)
			{
				echo "2";
			}
		}
		if($comp==2)
		{
			$pl=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `blood_component_stock` WHERE `component_id`='$comp' AND `bar_code`='$bar'"));
			if($pl>0)
			{
				echo "1";
			}
			else
			{
				echo "2";
			}
		}
		if($comp==3)
		{
			$p_bl=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_request` WHERE `patient_id`='$p[patient_id]'"));
			$d_bl=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_donor_inventory` WHERE `donor_id` IN (SELECT `donor_id` FROM `blood_receipt` WHERE `bar_code`='$bar')"));
			$plas=mysqli_fetch_array(mysqli_query($link,"SELECT `status` FROM `blood_platelet_compatibility` WHERE `donor_abo`='$d_bl[abo]' AND `donor_rh`='$d_bl[rh]' AND `receiver_abo`='$p_bl[abo]' AND `receiver_rh`='$p_bl[rh]'"));
			if($plas['status']==1)
			{
				echo "1";
			}
			if($plas['status']==0)
			{
				echo "2";
			}
		}
		if($comp==4)
		{
			$cp=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `blood_component_stock` WHERE `component_id`='$comp' AND `bar_code`='$bar'"));
			if($cp>0)
			{
				echo "1";
			}
			else
			{
				echo "2";
			}
		}
		if($comp==5)
		{
			$cr=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `blood_component_stock` WHERE `component_id`='$comp' AND `bar_code`='$bar'"));
			if($cr>0)
			{
				echo "1";
			}
			else
			{
				echo "2";
			}
		}
	}
	else
	{
		echo "3";
	}
}

if($_POST["type"]=="check_exp_date")
{
	$usr=$_POST['usr'];
	$time=date('H:i:s');
	$dt=date('Y-m-d', strtotime($date . ' -1 days'));
	$q=mysqli_query($link,"SELECT * FROM `blood_component_stock` WHERE `expiry_date`='$dt'");
	while($r=mysqli_fetch_array($q))
	{
		mysqli_query($link,"INSERT INTO `blood_component_expired`(`component_id`, `bar_code`, `expiry_date`, `date`, `time`, `user`) VALUES ('$r[component_id]','$r[bar_code]','$r[expiry_date]','$date','$time','$usr')");
		mysqli_query($link,"DELETE FROM `blood_component_stock` WHERE `component_id`='$r[component_id]' AND `bar_code`='$r[bar_code]'");
	}
}

if($_POST["type"]=="load_clinical_procedures")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `clinical_procedure` WHERE `name` like '$srch%' ORDER BY `name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `clinical_procedure` ORDER BY `name`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
		<tr>
			<th>SN</th><th>Procedure Name</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $n;?></td><td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $r['name'];?></td><td><i class="icon-remove icon-large text-danger" onclick="confirmm('<?php echo $r['id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="pat_ipd_delivery_num") // delevery details
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$discharge_request=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	$discharge=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$u=mysqli_fetch_array(mysqli_query($link,"SELECT `edit_ipd` FROM `employee` WHERE `emp_id`='$usr'"));
	$q=mysqli_query($link,"SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$f=mysqli_fetch_array(mysqli_query($link,"SELECT `father_name` FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Father&apos;s Name</th>
			<th colspan="2"><?php echo $f['father_name'];?></th>
			<td colspan="3"><input type="text" id="edit_fat" onkeyup="fat_upper()" style="display:none;" value="<?php echo $f['father_name'];?>" /></td>
			<td>
				<?php
				//if($discharge==0)
				//{
					//if(!$discharge_request)
					//{
						//if($u['edit_ipd']==1)
						//{
			?>
						<button type="button" class="btn btn-mini btn-info" id="btn_edt_fat" onclick="edt_fat_nam()">Edit Father Name</button>
						<button type="button" class="btn btn-mini btn-success" id="btn_sav_fat" style="display:none" onclick="sav_fat_nam()">Save</button>
						<button type="button" class="btn btn-mini btn-danger" id="btn_can_fat" style="display:none" onclick="canc_fat_nam()">Cancel</button>
				<?php
						//}
					//}
				//}
				?>
			</td>
		</tr>
		<tr>
			<th>Baby Name</th>
			<th>D.O.B</th>
			<th>Sex</th>
			<th>Time of Birth</th>
			<th>Weight</th>
			<th>Blood Group</th>
			<th></th>
		</tr>
		<?php
		while($r=mysqli_fetch_array($q))
		{
			$name=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[baby_uhid]'"));
		?>
		<tr id="clk<?php echo $r['slno'];?>" class="clk_tr">
			<td><?php echo $name['name'];?></td>
			<td><?php echo convert_date_g($r['dob']);?></td>
			<td><?php echo $r['sex'];?></td>
			<td><?php echo convert_time($r['born_time']);?></td>
			<td><?php echo $r['weight'];?></td>
			<td><?php echo $r['blood_group'];?></td>
			<td>
				<button type="button" class="btn btn-mini btn-info btn_edt_cert" id="btn_cert<?php echo $r['slno'];?>" onclick="pr_certficate('<?php echo $uhid;?>','<?php echo $r['baby_uhid'];?>')">Print Certificate</button>	
				<button type="button" class="btn btn-mini btn-info btn_edt_cert" id="btn_cert<?php echo $r['slno'];?>" onclick="pr_vaccine('<?php echo $uhid;?>','<?php echo $r['baby_uhid'];?>')">Vaccine Schedule </button>			
				<button type="button" class="btn btn-mini btn-primary btn_edt_cert" id="btn_edt_cert<?php echo $r['slno'];?>" onclick="edit_certficate('<?php echo $r['slno'];?>')">Edit</button>
			<?php
			if($discharge==0)
			{
				if(!$discharge_request)
				{
					if($u['edit_ipd']==1)
					{
			?>
						<button type="button" class="btn btn-mini btn-danger btn_edt_cert" id="btn_del_cert<?php echo $r['slno'];?>" onclick="del_certficate('<?php echo $r['slno'];?>')">Delete</button>
					<?php
					}
				}
			}
			?>
			</td>
		</tr>
		<tr id="tr<?php echo $r['slno'];?>" class="baby_tr" style="display:none;">
			<td colspan="7">
				<div id="sl<?php echo $r['slno'];?>" class="baby_div" style="display:none;"></div>
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
	}
	else
	{
	?>
		<button type="button" class="btn btn-info" id="deli_add_btn" onclick="show_deli_num()"><i class="icon icon-plus"></i> Add</button>
		<div id="show_deli_num" style="display:none;">
			<b>No of children:</b>
			<select id="no">
				<option value="0">Select</option>
				<?php
				for($i=1;$i<=6;$i++)
				{
				?>
				<option value="<?php echo $i;?>"><?php echo $i;?></option>
				<?php
				}
				?>
			</select>
			<input type="text" id="fat_name" onkeyup="fat_value()" placeholder="Father's Name" />
			<button type="button" class="btn btn-primary" onclick="add_deli_det()"><i class="icon icon-ok"></i> Ok</button>
			<button type="button" class="btn btn-danger" id="deli_rem_btn" onclick="rem_deli_det()"><i class="icon icon-ban-circle"></i> Cancel</button>
		</div>
		<div id="add_deli_det" style="display:none;">
		
		</div>
	<?php
	}
}

if($_POST["type"]=="pat_ipd_delivery_det")
{
	$uhid=$_POST['uhid'];
	$no=$_POST['no'];
	$fat_name=$_POST['fat_name'];
	mysqli_query($link,"DELETE FROM `ipd_baby_bed_temp` WHERE `patient_id`='$uhid'");
	?>
	<input type="text" id="val" style="display:none;" value="<?php echo $no;?>" />
	<input type="text" id="fat_name" style="display:none;" value="<?php echo $fat_name;?>" />
	<input type="text" id="bedd" style="display:none;" value="" />
	<table class="table table-condensed table-bordered" id="deli_tbl">
		<?php
		for($i=1;$i<=$no;$i++)
		{
			if($no>1)
			{
			?>
			<tr>
				<th colspan="8" style="background:#cccccc;">Baby <?php echo $i;?></th>
			</tr>
			<?php
			}
		?>
		<tr id="tr<?php echo $i;?>" class="tr">
			<th>D.O.B<br/><input type="text" class="txt" readonly="readonly" style="cursor:unset;" id="dob<?php echo $i;?>" placeholder="Date Of Birth" /> <i class="icon-calendar icon-large" style="color:#F0A11F;"></i></th>
			<th>
				Time Of Birth<br/>
				<!--<input type="text" class="txt" readonly="readonly" style="cursor:unset;" id="time<?php echo $i;?>" placeholder="HH:MM" /> <i class="icon-time icon-large" style="color:#F0A11F;"></i>-->
				<select id="hrs<?php echo $i;?>" class="span1">
					<option value="0">Hours</option>
					<?php
					for($h=1; $h<=12; $h++)
					{
					?>
					<option value="<?php echo $h;?>"><?php echo $h;?></option>
					<?php
					}
					?>
				</select>
				<select id="mins<?php echo $i;?>" class="span1">
					<option value="0">Minutes</option>
					<?php
					for($m=0; $m<60; $m++)
					{
						$m = str_pad($m,2,"0",STR_PAD_LEFT);
					?>
					<option value="<?php echo $m;?>"><?php echo $m;?></option>
					<?php
					}
					?>
				</select>
				<select id="ampm<?php echo $i;?>" class="span1">
					<option value="0">Select</option>
					<option value="am">AM</option>
					<option value="pm">PM</option>
				</select>
			</th>
			<th>Sex<br/>
				<select id="sex<?php echo $i;?>" class="span2">
					<option value="0">Select</option>
					<option value="Male">Male</option>
					<option value="Female">Female</option>
				</select>
			</th>
			<!--<th>Weight<br/><input type="text" id="wt<?php echo $i;?>" onfocus="weight_ui_div()" readonly="readonly" style="cursor:unset;" class="txt" placeholder="#.## K.G" /></th>-->
			<th>Weight<br/><input type="text" id="wt<?php echo $i;?>" class="txt numericfloat" placeholder="In KG" /></th>
		</tr>
		<tr>
			<th>Blood Group<br/>
				<select id="blood<?php echo $i;?>" class="span2">
					<option value="">Select</option>
					<option value="O Positive">O Positive</option>
					<option value="O Negative">O Negative</option>
					<option value="A Positive">A Positive</option>
					<option value="A Negative">A Negative</option>
					<option value="B Positive">B Positive</option>
					<option value="B Negative">B Negative</option>
					<option value="AB Positive">AB Positive</option>
					<option value="AB Negative">AB Negative</option>
				</select>
			</th>
			<th>
				Delivery Mode<br/>
				<select id="dmode<?php echo $i;?>" class="span2">
					<option value="0">Select</option>
					<option value="LSCS">LSCS</option>
					<option value="Normal">Normal</option>
					<option value="Forceps">Forceps</option>
					<option value="Vacuum">Vacuum</option>
					<option value="Ventose">Ventose</option>
				</select>
			</th>
			<th>
				Conducted By<br/>
				<select id="conducted<?php echo $i;?>" class="span3">
					<option value="0">Select</option>
					<?php
					$cc=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($c=mysqli_fetch_array($cc))
					{
					?>
					<option value="<?php echo $c['consultantdoctorid'];?>"><?php echo $c['Name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th>
				Dead Tag<br/>
				<select id="tag<?php echo $i;?>" class="span2">
					<option value="No">No</option>
					<option value="Yes">Yes</option>
				</select>
			</th>
			<!--<th>Bed No<br/>
				<input type="text" id="ward<?php echo $i;?>" style="display:none;" value="" />
				<input type="text" id="bed<?php echo $i;?>" style="display:none;" value="" />
				<button type="button" id="b<?php echo $i;?>" style="width:90px;" class="btn btn-primary" onclick="view_baby_bed('<?php echo $uhid;?>',<?php echo $i;?>)"><i class="icon icon-eye-open"></i> View</button>
				<!--<select id="bed<?php echo $i;?>" class="span2">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `bed_id`,`room_id`,`bed_no` FROM `bed_master` WHERE `ward_id`='6'");
					while($r=mysqli_fetch_array($q))
					{
						$room=mysqli_fetch_array(mysqli_query($link,"SELECT `room_no` FROM `room_master` WHERE `room_id`='$r[room_id]'"));
					?>
					<option value="<?php echo $r['bed_id'];?>"><?php echo "Room: ".$room['room_no']." Bed: ".$r['bed_no'];?></option>
					<?php
					}
					?>
				</select>-->
			<!--</th>-->
		</tr>
		<script>
			$("#dob"+<?php echo $i;?>).datepicker({dateFormat: 'yy-mm-dd',maxDate:'0'});
			//$("#time"+<?php echo $i;?>).timepicker({minutes: {starts: 0,interval: 01,}});
			//$('#wt'+<?php echo $i;?>).timepicker({showPeriodLabels:false,showLeadingZero: false,defaultTime:'',timeSeparator:'.',hourText: 'KG',minuteText: 'Gram',hours:{starts: 1,ends: 4},minutes: {starts: 0,ends:99,interval: 01,}});
		</script>
		<?php
		}
		?>
	</table>
	<button type="button" class="btn btn-primary" id="deli_sav" onclick="pat_ipd_delivery_save()"><i class="icon icon-file"></i> Save</button>
	<button type="button" class="btn btn-danger" onclick="clear_all_deli()"><i class="icon icon-ban-circle"></i> Cancel</button>
	<style>
		#deli_tbl tr:hover
		{
			background:none;
		}
		#deli_tbl tr th, #deli_tbl tr td
		{
			padding-top: 0px;
			padding-bottom: 0px;
		}
	</style>
	<?php
}

if($_POST["type"]=="view_baby_bed")
{
	$uhid=$_POST['uhid'];
	$sn=$_POST['sn'];
	?>
	<b style="font-size:16px;">Baby Ward: Bed Details</b><hr/>
	<input type="text" style="display:none;" id="hid" value="<?php echo $sn;?>" />
	<?php
	$ward=mysqli_query($link,"SELECT DISTINCT `room_id` FROM `bed_master` WHERE `ward_id`='6'");
	while($w=mysqli_fetch_array($ward))
	{
		$i=0;
		$r=mysqli_fetch_array(mysqli_query($link,"SELECT `room_no` FROM `room_master` WHERE `room_id`='$w[room_id]'"));
		?>
		<b>Room No: <?php echo $r['room_no'];?></b><br/>
		<?php
		$q=mysqli_query($link,"SELECT `bed_id`,`bed_no`,`status` FROM `bed_master` WHERE `ward_id`='6' AND `room_id`='$w[room_id]'");
		while($r=mysqli_fetch_array($q))
		{
			$style="width:70px;font-weight:bold;margin-bottom:6px;color:#000000;";
			$dis="";
			$chk_bd=mysqli_query($link,"select * from ipd_baby_bed_temp where ward_id='6' and bed_id='$r[bed_id]'");
			if(mysqli_num_rows($chk_bd)>0)
			{
				if(mysqli_num_rows(mysqli_query($link,"select * from ipd_baby_bed_temp where ward_id='6' and bed_id='$r[bed_id]' and patient_id='$uhid' and `baby_no`='$sn'"))>0)
				{
					$style.="background-color:#5bc0de;";
					$dis="";
				}
				else
				{
					$style.="background-color:#FF7A6F;";
					$dis="disabled='disabled'";
				}
			}
			else if($r['status']==1)
			{
				$style.="background-color:#ffbb33;";
				$dis="disabled='disabled'";
			}
			else
			{
				//---------------------------------
				$chk_bd_main=mysqli_query($link,"select * from ipd_pat_bed_details where ward_id='6' and bed_id='$r[bed_id]'");
				if(mysqli_num_rows($chk_bd_main)>0)
				{
					$chk_bd_ipd=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$uhid' and ipd_id='$ipd' and ward_id='6' and bed_id='$r[bed_id]'"));
					if($chk_bd_ipd>0)
					{
						$style.="background-color:#5cb85c;font-weight:bold;text-decoration:underline;";
						$dis="";
					}
					else
					{
						$style.="background-color:#5cb85c;";
						$dis="disabled='disabled'";
					}
				}
				//----------------------------------
			}
			?>
			<button type="button" id="btn<?php echo $r['bed_id'];?>" class="btn baby_btn <?php echo $bg;?>" style="<?php echo $style;?>" onclick="baby_bed_assign(this.id,'6','<?php echo $w['room_id'];?>','<?php echo $r['bed_id'];?>','<?php echo $r['bed_no'];?>','<?php echo $sn;?>')" <?php echo $dis;?>><?php echo $r['bed_no'];?></button>
			<?php
			if($i==6)
			{
				$i=0;
				echo "<br/>";
			}
			else
			{
				$i++;
			}
		}
		echo "<hr/>";
	}
}

if($_POST["type"]=="load_ot_type_id")
{
	$i=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(type_id) as max FROM `ot_type_master`"));
	echo $i['max']+1;
}

if($_POST["type"]=="load_ot_type")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_type_master` WHERE `type` like '$srch%' ORDER BY `type`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_type_master` ORDER BY `type`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
			<tr>
				<th>SN</th><th>Type</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
			</tr>
			<?php
			$n=1;
			while($r=mysqli_fetch_array($q))
			{
			?>
			<tr>
				<td><?php echo $n;?></td><td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['type_id'];?>')"><?php echo $r['type'];?></td><td><i class="icon-remove icon-large" onclick="confirmm('<?php echo $r['type_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
	<?php
}

if($_POST["type"]=="load_ot_res_details")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_resource_master` WHERE `emp_id` IN (SELECT `emp_id` FROM `employee` WHERE `name` like '$srch%') ORDER BY `type_id`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_resource_master` ORDER BY `type_id`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
			<tr>
				<th>SN</th><th>Name</th><th>Type</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
			</tr>
			<?php
			$n=1;
			while($r=mysqli_fetch_array($q))
			{
				$t=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$r[type_id]'"));
				$nm=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[emp_id]'"));
			?>
			<tr class="nm">
				<td><?php echo $n;?></td><td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $nm['name'];?></td><td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $t['type'];?></td><td><i class="icon-remove icon-large" onclick="confirmm('<?php echo $r['id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
	<?php
}

if($_POST["type"]=="load_ot_res_edit")
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `type_id`,`emp_id`,`charge_id` FROM `ot_resource_master` WHERE `id`='$id'"));
	echo $id."#@#".$d['type_id']."#@#".$d['emp_id']."#@#".$d['charge_id']."#@#";
}
if($_POST["type"]=="load_ot_type_details")
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$id'"));
	echo $id."#@#".$d['type']."#@#";
}

if($_POST["type"]=="opd_patient_list")
{
	$uhid=$_POST['uhid'];
	$pin=$_POST['pin'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$usr'"));
	$docid=$doc['consultantdoctorid'];
	$q="SELECT * FROM `appointment_book` WHERE `appointment_date`='$date' AND `consultantdoctorid`='$docid'"; // AND `consultantdoctorid`='$docid'
	if($uhid)
	{
		//$q="SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `consultantdoctorid`='$docid'";
		$q="SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `consultantdoctorid`='$docid'";
	}
	if($pin)
	{
		//$q="SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `consultantdoctorid`='$docid'";
		$q="SELECT * FROM `appointment_book` WHERE `opd_id`='$pin' AND `consultantdoctorid`='$docid'";
	}
	if($name)
	{
		$q="SELECT * FROM `appointment_book` WHERE `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '$name%' AND `consultantdoctorid`='$docid')"; // 
	}
	if($dat)
	{
		$q="SELECT * FROM `appointment_book` WHERE `appointment_date`='$dat' AND `consultantdoctorid`='$docid'";
	}
	//echo $q;
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>UHID</th>
				<th>PIN</th>
				<th>Name</th>
				<th>Sex</th>
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
				$ck1=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]'"));
				$ck2=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_complaints` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]'"));
				$ck3=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]'"));
				if($ck1>0 || $ck2>0 || $ck3>0)
				{
					$bg="background:#BFEDD0;"; // B0F9CA
				}
				else
				{
					$bg="";
				}
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['opd_id'];?>')" style="cursor:pointer;<?php echo $bg;?>">
					<td><?php echo $p['patient_id'];?></td>
					<td><?php echo $r['opd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
				</tr>
			<?php
			}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="ipd_patient_list")
{
	$uhid=$_POST['uhid'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$usr'"));
	$docid=$doc['consultantdoctorid'];
	$q="SELECT * FROM `ipd_pat_doc_details` WHERE `attend_doc`='$docid' LIMIT 0,20"; // AND `consultantdoctorid`='$docid'
	if($uhid)
	{
		//$q="SELECT * FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `attend_doc`='$docid'";
		$q="SELECT * FROM `ipd_pat_doc_details` WHERE `patient_id` IN(SELECT `patient_id` FROM `patient_info` WHERE `uhid`='$uhid') AND `attend_doc`='$docid'";
	}
	if($name)
	{
		$q="SELECT * FROM `ipd_pat_doc_details` WHERE `patient_id` IN (SELECT `patient_id` FROM `patient_info` WHERE `name` like '$name%') AND `attend_doc`='$docid' LIMIT 0,20";
	}
	if($dat)
	{
		$q="SELECT * FROM `ipd_pat_doc_details` WHERE `patient_id` IN (SELECT `patient_id` FROM `ipd_pat_details` WHERE `date`='$dat') AND `attend_doc`='$docid' LIMIT 0,20";
	}
	//echo $q;
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		?>
		<table class="table table-condensed table-bordered table-hover">
			<tr>
				<th>UHID</th>
				<th>PIN</th>
				<th>Name</th>
				<th>Sex</th>
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			?>
				<tr onclick="ipd_redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['ipd_id'];?>')" style="cursor:pointer;">
					<td><?php echo $p['patient_id'];?></td>
					<td><?php echo $r['ipd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
				</tr>
			<?php
			}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="pat_ipd_ot_booking")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$q=mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$d=mysqli_fetch_array($q);
		$ot=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$d[ot_area_id]'"));
		//$pr=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `clinical_procedure` WHERE `id`='$d[procedure_id]'"));
		$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$d[consultantdoctorid]'"));
		if($d['scheduled']==0)
		{
			$sh="Not Sheduled";
			$disb="";
			$edt_func="edit_ot_book()";
			$del_func="remove_ot_book()";
		}
		if($d['scheduled']==1)
		{
			$sh="Sheduled";
			$disb="disabled='disabled'";
			$edt_func="";
			$del_func="";
		}
		?>
		<table class="table table-condensed table-bordered" id="">
			<tr class="sh_data">
				<!--<th>OT</th>-->
				<th>Procedure</th>
				<th>Date</th>
				<th>Requesting Doctor</th>
				<th>Status</th>
				<th width="8%" style="text-align:center;"><i class="icon-cogs icon-large"></i></th>
			</tr>
			<tr class="sh_data">
				<!--<th><?php echo $ot['ot_area_name'];?></th>-->
				<th><?php echo $d['procedure_id'];?></th>
				<td><?php echo $d['ot_date'];?></td>
				<td><?php echo $doc['Name'];?></td>
				<th><?php echo $sh;?></th>
				<td>
					<button type="button" class="btn btn-mini btn-primary" <?php echo $disb;?> onclick="<?php echo $edt_func;?>"><i class="icon-edit icon-large"></i></button>
					<button type="button" class="btn btn-mini btn-danger" <?php echo $disb;?> onclick="<?php echo $del_func;?>"><i class="icon-ban-circle icon-large"></i></button>
				</td>
			</tr>
			<tr class="ed_data" style="display:none;">
				<th colspan="2">Procedure</th>
				<th>Date</th>
				<th colspan="2">Requesting Doctor</th>
			</tr>
			<tr class="ed_data" style="display:none;">
				<td colspan="2">
					<input type="text" list="browsrs" id="pr" class="span5" placeholder="Procedure" value="<?php echo $d['procedure_id'];?>"/>
					<datalist id="browsrs">
					<?php
						$qq = mysqli_query($link,"SELECT `name` FROM `clinical_procedure` ORDER BY `name`");
						while($cc=mysqli_fetch_array($qq))
						{
							echo "<option value='$cc[name]'>";
						}
					?>
					</datalist>
				</td>
				<td><input type="text" id="ot_date" class="span2" readonly="readonly" style="cursor:text;" placeholder="Date" value="<?php echo $d['ot_date'];?>"/></td>
				<td colspan="2">
					<select id="doc">
						<option value="0">Select</option>
						<?php
						//$qry=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='5'");
						$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
						while($rr=mysqli_fetch_array($qry))
						{
						?>
						<option value="<?php echo $rr['consultantdoctorid'];?>" <?php if($d['consultantdoctorid']==$rr['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $rr['Name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr class="ed_data" style="display:none;">
				<td colspan="5" style="text-align:center;">
					<button type="button" class="btn btn-primary" id="ot_upd_btn" onclick="upd_ot_book()"><i class="icon-save icon-large"></i> Update</button>
					<button type="button" class="btn btn-danger" onclick="canc_ot_book()"><i class="icon-ban-circle icon-large"></i> Cancel</button>
				</td>
			</tr>
		</table>
		<?php
	}
	else
	{
		?>
		<button type="button" id="otad" class="btn btn-info" onclick="ot_det_show()"><i class="icon icon-plus"></i> ADD</button>
		<div id="ot_det" style="display:none;">
			<table class="table table-condensed table-bordered" id="">
				<tr>
					<th>Date</th>
					<th><input type="text" id="ot_date" readonly="readonly" style="cursor:text;" placeholder="Date" /></th>
					<th>Requesting Doctor</th>
					<th>
						<select id="doc">
							<option value="0">Select</option>
							<?php
							//$qry=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='5'");
							$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
							while($rr=mysqli_fetch_array($qry))
							{
							?>
							<option value="<?php echo $rr['consultantdoctorid'];?>"><?php echo $rr['Name'];?></option>
							<?php
							}
							?>
						</select>
					</th>
				</tr>
				<tr>
					<th style="display:none">Select OT</th>
					<th style="display:none">
						<select id="ot">
							<option value="0">Select</option>
							<?php
							$q=mysqli_query($link,"SELECT `ot_area_id`,`ot_area_name` FROM `ot_area_master` ORDER BY `ot_area_name`");
							while($r=mysqli_fetch_array($q))
							{
							?>
							<option value="<?php echo $r['ot_area_id'];?>"><?php echo $r['ot_area_name'];?></option>
							<?php
							}
							?>
						</select>
					</th>
					<th>Procedure</th>
					<th colspan="3">
						<input type="text" list="browsrs" class="span8" id="pr" placeholder="Procedure" />
						<datalist id="browsrs">
						<?php
							$qq = mysqli_query($link,"SELECT `name` FROM `clinical_procedure` ORDER BY `name`");
							while($cc=mysqli_fetch_array($qq))
							{
								echo "<option value='$cc[name]'>";
							}
						?>
						</datalist>
						<!--<select id="pr">
							<option value="0">Select</option>
							<?php
							//$qr=mysqli_query($link,"SELECT `id`,`name` FROM `clinical_procedure` ORDER BY `name`");
							//while($rr=mysqli_fetch_array($qr))
							{
							?>
							<option value="<?php echo $rr['id'];?>"><?php echo $rr['name'];?></option>
							<?php
							}
							?>
						</select>-->
					</th>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center;"><button type="button" class="btn btn-primary" onclick="save_ot_book()"><i class="icon icon-save"></i> Save</button></td>
				</tr>
			</table>
			<button type="button" id="" class="btn btn-danger" onclick="ot_det_hide()"><i class="icon icon-ban-circle"></i> Cancel</button>
		</div>
	<?php
	}
	?>
	<script>
		$("#ot_date").datepicker({dateFormat: 'yy-mm-dd',minDate:0,});
	</script>
	<?php
}

if($_POST["type"]=="ot_pat_list")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	$q="SELECT DISTINCT a.`patient_id`,a.`ipd_id`,a.`scheduled`,a.`schedule_id` FROM `ot_book` a, `ot_schedule` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.ipd_id AND b.`leaved`='0' ORDER BY a.`ot_date` DESC";
	if($uhid)
	{
		$q="SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' ORDER BY `ot_date` DESC";
	}
	if($ipd)
	{
		$q="SELECT * FROM `ot_book` WHERE `ipd_id`='$ipd' ORDER BY `ot_date` DESC LIMIT 0,20";
	}
	if($name)
	{
		$q="SELECT * FROM `ot_book` WHERE `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$name%') ORDER BY `ot_date` DESC LIMIT 0,20";
	}
	if($dat)
	{
		$q="SELECT * FROM `ot_book` WHERE `ot_date`='$dat' ORDER BY `ot_date` DESC LIMIT 0,20";
	}
	//echo $q;
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>UHID</th>
				<th>PIN</th>
				<th>Name</th>
				<th>Sex</th>
				<th>Status</th>
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				$lv=mysqli_fetch_array(mysqli_query($link,"SELECT `leaved` FROM `ot_schedule` WHERE `schedule_id`='$r[schedule_id]' AND `patient_id`='$r[patient_id]' AND `ipd_id`='$r[ipd_id]'"));
				if($lv['leaved']==1)
				{
					$sh="Leaved";
				}
				else if($lv['leaved']==0)
				{
					if($r['scheduled']=="0")
					{
						$sh="Not scheduled";
					}
					if($r['scheduled']=="1")
					{
						$sh="Scheduled";
					}
				}
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['ipd_id'];?>','<?php echo $r['schedule_id'];?>')" style="cursor:pointer;">
					<td><?php echo $r['patient_id'];?></td>
					<td><?php echo $r['ipd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $sh;?></td>
				</tr>
			<?php
			}
		?>
		</table>
		<?php
	}
}

//---------------------------------------------------------------------------------------
if($_POST["type"]=="ot_scheduling")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$r=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid`,`name`,`sex`,`dob`,`age`,`age_type` FROM `patient_info` WHERE `patient_id`='$uhid'"));
	if($r['scheduled']=="0")
	$sh="Not scheduled";
	if($r['scheduled']=="1")
	$sh="Scheduled";
	$j=1;
	$qry=mysqli_query($link,"SELECT * FROM `ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		while($r=mysqli_fetch_array($qry))
		{
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th colspan="5" style="background:#cccccc;"><?php if($num>1){echo "Schedule ".$j;}?></th>
			</tr>
			<tr>
				<th>Schedule No</th>
				<th>OT Date</th>
				<th>Start Time</th>
				<th>End Time</th>
				<th>OT No</th>
			</tr>
			<tr>
				<td><?php echo $r['schedule_id'];?></td>
				<td><?php echo $r['ot_date'];?></td>
				<td><?php echo $r['start_time'];?></td>
				<td><?php echo $r['end_time'];?></td>
				<td><?php echo $r['ot_no'];?></td>
			</tr>
			<?php
			if($r['remarks']!="")
			{
			?>
			<tr>
				<th>Remarks</th>
				<td colspan="4"><?php echo $r['remarks'];?></td>
			</tr>
			<?php
			}
			$qq=mysqli_query($link,"SELECT * FROM `ot_resource` WHERE `schedule_id`='$r[schedule_id]'");
			$nn=mysqli_num_rows($qq);
			if($nn>0)
			{
			?>
			<tr>
				<td colspan="5" style="background:#dddddd;"></td>
			</tr>
			<tr>
				<th colspan="5" style="text-align:center;">OT Resources</th>
			</tr>
			<tr>
				<th>SN</th>
				<th>Resource</th>
				<th>Employee</th>
				<th></th>
				<th></th>
			</tr>
			<?php
			$i=1;
			while($rr=mysqli_fetch_array($qq))
			{
				$res=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$rr[resourse_id]'"));
				$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$rr[emp_id]'"));
			?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $res['type'];?></td>
				<td><?php echo $emp['name'];?></td>
				<td></td>
				<td></td>
			</tr>
			<?php
			$i++;
			}
			?>
			<tr>
				<th colspan="5" style="background:#dddddd;"></th>
			</tr>
			<?php
			}
			?>
			
		</table>
		<?php
		$j++;
		}
	}
	else
	{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>UHID</th>
			<th>PIN</th>
			<th>Name</th>
			<th>Sex</th>
			<th>Age (DOB)</th>
			<th>Schedule</th>
		</tr>
		<tr>
			<td><?php echo $p['uhid'];?></td>
			<td><?php echo $ipd;?></td>
			<td><?php echo $p['name'];?></td>
			<td><?php echo $p['sex'];?></td>
			<td><?php echo $p['age']." ".$p['age_type'];?></td>
			<td>
				<?php echo $sh;?>
				<span class="text-right"><button class="btn btn-primary" onclick="ot_schedule()">Schedule</button></span>
			</td>
		</tr>
	</table>
	<?php
	}
}

if($_POST["type"]=="ot_schedule")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$o=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	?>
	<table class="table table-condensed table-bordered" id="ot_tbl">
		<tr>
			<th>Select OT</th>
			<th>
				<select id="ot">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `ot_area_id`,`ot_area_name` FROM `ot_area_master` ORDER BY `ot_area_name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['ot_area_id'];?>" <?php if($r['ot_area_id']==$o['ot_area_id']){echo "selected='selected'";}?>><?php echo $r['ot_area_name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th>Select Procedure</th>
			<th>
				<input type="text" list="browsrs" class="span4" id="pr" value="<?php echo $o['procedure_id'];?>" placeholder="Procedure" />
				<datalist id="browsrs">
				<?php
					$qq = mysqli_query($link,"SELECT `name` FROM `clinical_procedure` ORDER BY `name`");
					while($cc=mysqli_fetch_array($qq))
					{
						echo "<option value='$cc[name]'>";
					}
				?>
				</datalist>
				<!--<select id="pr">
					<option value="0">Select</option>
					<?php
					//$qr=mysqli_query($link,"SELECT `id`,`name` FROM `clinical_procedure` ORDER BY `name`");
					//while($rr=mysqli_fetch_array($qr))
					{
					?>
					<option value="<?php echo $rr['id'];?>" <?php if($rr['id']==$o['procedure_id']){echo "selected='selected'";}?>><?php echo $rr['name'];?></option>
					<?php
					}
					?>
				</select>-->
			</th>
		</tr>
		<tr>
			<th>Date</th>
			<th><input type="text" id="ot_date" value="<?php echo $o['ot_date'];?>" placeholder="Date" /></th>
			<th>Requesting Doctor</th>
			<th>
				<select id="doc">
					<option value="0">Select</option>
					<?php
					$qry=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='5'");
					while($rrr=mysqli_fetch_array($qry))
					{
					?>
					<option value="<?php echo $rrr['emp_id'];?>" <?php if($rrr['emp_id']==$o['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $rrr['name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
		</tr>
		<tr>
			<th>Start Time</th>
			<th><input type="text" id="st_time" placeholder="Start Time" /></th>
			<th>End Time</th>
			<th><input type="text" id="en_time" placeholder="End Time" /></th>
		</tr>
		<tr>
			<th>Remarks</th>
			<th colspan="3"><textarea id="rem" placeholder="Remarks" style="width:80%;resize:none;"></textarea></th>
		</tr>
		<tr>
			<th>OT Resources</th>
			<th>
				<select id="ot_type" onchange="ot_resource_list()">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `ot_type_master` ORDER BY `type`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['type_id'];?>"><?php echo $r['type'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th id="resource_list">
				<select id="rs">
					<option value="0">Select</option>
				</select>
			</th>
			<th>
				<input type="text" id="sel_val" style="display:none;" />
				<span class="text-right"><button type="button" class="btn btn-primary" onclick="add_res()"><i class="icon icon-plus"></i> Add</button></span>
			</th>
		</tr>
		<tr id="end_tr">
			<td colspan="4" style="text-align:center;">
				<button type="button" class="btn btn-primary" onclick="save_shed()"><i class="icon icon-save"></i> Save</button>
				<button type="button" class="btn btn-danger" onclick="scheduling()"><i class="icon icon-remove"></i> Cancel</button>
			</td>
		</tr>
	</table>
	<script>
		$("#ot_date").datepicker({dateFormat: 'yy-mm-dd',minDate:0,});
		$("#st_time").timepicker({minutes: {starts: 0,interval: 05,}});
		$("#en_time").timepicker({minutes: {starts: 0,interval: 05,}});
	</script>
	<?php
}

if($_POST["type"]=="ot_resource_list")
{
	$ot_type=$_POST['ot_type'];
	?>
	<select id="rs">
		<option value="0">Select</option>
		<?php
		$q=mysqli_query($link,"SELECT `emp_id` FROM `ot_resource_master` WHERE `type_id`='$ot_type'");
		while($rr=mysqli_fetch_array($q))
		{
			$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$rr[emp_id]'"));
		?>
		<option value="<?php echo $rr['emp_id'];?>"><?php echo $e['name'];?></option>
		<?php
		}
		?>
	</select>
	<?php
}

if($_POST["type"]=="res_type")
{
	$typ=$_POST['typ'];
	$emp=$_POST['emp'];
	$o=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$typ'"));
	$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$emp'"));
	echo $o['type']."@".$e['name']."@";
}
if($_POST["type"]=="hidden_menu")
{
	$pid=$_POST['pid'];
	$val=$_POST['val'];
	
	mysqli_query($link," UPDATE `menu_master` SET `hidden`='$val' WHERE `par_id`='$pid' ");
}

if($_POST["type"]=="complain_templates_list")
{
	$no=$_POST['no'];
	$val="<datalist id='browsrs".$no."' style='height:0;'>";
	$qq = mysqli_query($link,"SELECT `complain` FROM `complain_master`");
	while($cc=mysqli_fetch_array($qq))
	{
	$val.="<option value='$cc[complain]'>";
	}
	$val.="</datalist>";
	echo $val;
}

if($_POST["type"]=="diagnosis_templates_list")
{
	$no=$_POST['no'];
	$val="<datalist id='brows".$no."' style='height:0;'>";
	$qq = mysqli_query($link,"SELECT `diagnosis` FROM `diagnosis_master`");
	while($cc=mysqli_fetch_array($qq))
	{
	$val.="<option value='$cc[diagnosis]'>";
	}
	$val.="</datalist>";
	echo $val;
}

if($_POST["type"]=="ipd_pat_post_medi_upd")
{
	$id=$_POST['id'];
	$f=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_final_discharge` WHERE `id`='$id'"));
	?>
	<table class="table table-condensed">
		<tr>
			<th width="20%">Drug Name</th>
			<td colspan="5">
				<input type="text" name="mediname" id="mediname" value="<?php echo $f['medicine'];?>" class="span5" />
			</td>
		</tr>
		<tr>
			<td colspan="6">
					<table class="table table-condensed">
						<tr>
							<th>Dosage</th>
							<td>
								<input type="text" id="dos" value="<?php echo $f['dosage'];?>" />
							</td>
							<th></th>
							<td></td>
							<th>Frequency</th>
							<td>
								<select id="freq" onkeyup="meditab(this.id,event)" onchange="calc_totday()" class="span2">
									<option value="0">Select</option>
									<option value="1" <?php if($f['frequency']==1){echo "selected='selected'";}?>>Immediately</option>
									<option value="2" <?php if($f['frequency']==2){echo "selected='selected'";}?>>Once a day</option>
									<option value="3" <?php if($f['frequency']==3){echo "selected='selected'";}?>>Twice a day</option>
									<option value="4" <?php if($f['frequency']==4){echo "selected='selected'";}?>>Thrice a day</option>
									<option value="5" <?php if($f['frequency']==5){echo "selected='selected'";}?>>Four times a day</option>
									<option value="6" <?php if($f['frequency']==6){echo "selected='selected'";}?>>Five times a day</option>
									<option value="7" <?php if($f['frequency']==7){echo "selected='selected'";}?>>Every hour</option>
									<option value="8" <?php if($f['frequency']==8){echo "selected='selected'";}?>>Every 2 hours</option>
									<option value="9" <?php if($f['frequency']==9){echo "selected='selected'";}?>>Every 3 hours</option>
									<option value="10" <?php if($f['frequency']==10){echo "selected='selected'";}?>>Every 4 hours</option>
									<option value="11" <?php if($f['frequency']==11){echo "selected='selected'";}?>>Every 5 hours</option>
									<option value="12" <?php if($f['frequency']==12){echo "selected='selected'";}?>>Every 6 hours</option>
									<option value="13" <?php if($f['frequency']==13){echo "selected='selected'";}?>>Every 7 hours</option>
									<option value="14" <?php if($f['frequency']==14){echo "selected='selected'";}?>>Every 8 hours</option>
									<option value="15" <?php if($f['frequency']==15){echo "selected='selected'";}?>>Every 10 hours</option>
									<option value="16" <?php if($f['frequency']==16){echo "selected='selected'";}?>>Every 12 hours</option>
									<!--<option value="17">On alternate days</option>
									<option value="18">Once a week</option>
									<option value="19">Twice a week</option>
									<option value="20">Thrice a week</option>
									<option value="21">Every 2 weeks</option>
									<option value="22">Every 3 weeks</option>
									<option value="23">Once a month</option>-->
								</select>
							</td>
							<th>Start Date</th>
							<td><input type="text" id="st_date" value="<?php echo $f['start_date'];?>" style="width:100px;" onkeyup="meditab(this.id,event)" /></td>
						</tr>
						<tr>
							<th>Duration</th>
							<td>
								<select id="dur" onkeyup="meditab(this.id,event)" onchange="calc_totday()" style="width:80px;">
									<option value="0">select</option>
									<?php
									for($j=1;$j<=100;$j++)
									{
									?>
									<option value="<?php echo $j;?>" <?php if($f['duration']==$j){echo "selected='selected'";}?>><?php echo $j;?></option>
									<?php
									}
									?>
								</select>
							</td>
							<th>Unit Days</th>
							<td>
								<select id="unit_day" style="width:80px;" onchange="calc_totday()" onkeyup="meditab(this.id,event)">
									<option value="0">select</option>
									<option value="Days" <?php if($f['unit_days']=="Days"){echo "selected='selected'";}?>>Days</option>
									<option value="Weeks" <?php if($f['unit_days']=="Weeks"){echo "selected='selected'";}?>>Weeks</option>
									<option value="Months" <?php if($f['unit_days']=="Months"){echo "selected='selected'";}?>>Months</option>
								</select>
							</td>
							<th>Total</th>
							<td><input type="text" id="totl" class="span2" value="<?php echo $f['total_drugs'];?>" readonly="readonly" /></td>
							<th>Instruction</th>
							<td>
								<select id="inst" style="width:120px;" onkeyup="meditab(this.id,event)">
									<option value="1" <?php if($f['instruction']==1){echo "selected='selected'";}?>>As Directed</option>
									<option value="2" <?php if($f['instruction']==2){echo "selected='selected'";}?>>Before Meal</option>
									<option value="3" <?php if($f['instruction']==3){echo "selected='selected'";}?>>Empty Stomach</option>
									<option value="4" <?php if($f['instruction']==4){echo "selected='selected'";}?>>After Meal</option>
									<option value="5" <?php if($f['instruction']==5){echo "selected='selected'";}?>>In the Morning</option>
									<option value="6" <?php if($f['instruction']==6){echo "selected='selected'";}?>>In the Evening</option>
									<option value="7" <?php if($f['instruction']==7){echo "selected='selected'";}?>>At Bedtime</option>
									<option value="8" <?php if($f['instruction']==8){echo "selected='selected'";}?>>Immediately</option>
								</select>
							</td>
						</tr>
					</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<span class="text-right">
					<a data-dismiss="modal" onclick="update_plan('<?php echo $id;?>')" class="btn btn-primary" href="#">Save</a>
					<a data-dismiss="modal" onclick="" class="btn btn-info" href="#">Cancel</a>
				</span>
			</td>
		</tr>
	</table>
	<style>
		.table tr:hover{background:none;}
	</style>
	<script>
		$("#st_date").datepicker(
		{
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
	</script>
	<?php
}

if($_POST["type"]=="pat_ipd_discharge_request_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$m=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	$tot=$n+$m;
	
	echo $tot;
}

if($_POST["type"]=="load_dos_list")
{
	$val="<datalist id='brow'>";
	$qq = mysqli_query($link,"SELECT `dosage` FROM `dosage_master`");
	while($cc=mysqli_fetch_array($qq))
	{
	$val.="<option value='$cc[dosage]'>";
	}
	$val.="</datalist>";
	echo $val;
}

if($_POST["type"]=="view_presc_doc")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	
	$fetch = mysqli_fetch_array(mysqli_query($link,"select * from consultant_doctor_master_emr where user_id='$_SESSION[id]'"));

	$pat=mysqli_fetch_array(mysqli_query($link," select * from patient_info where patient_id='$uhid' "));

	$ref_doc=mysqli_fetch_array(mysqli_query($link," SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid` in ( SELECT `refbydoctorid` FROM `patient_info` WHERE `patient_id`='$uhid' ) "));

	$complaints=mysqli_query($link," Select * from pat_complaints where patient_id='$uhid' and opd_id='$opd' ");
	$comp_num=mysqli_num_rows($complaints);
	$vital=mysqli_query($link," Select * from pat_vital where patient_id='$uhid' and opd_id='$opd' ");
	$vit_num=mysqli_num_rows($vital);
	$consultation=mysqli_query($link," Select * from pat_consultation where patient_id='$uhid' and opd_id='$opd' ");
	$conslt_num=mysqli_num_rows($consultation);
	$disp_note=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_disposition` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	if($disp_note['disposition']==0)
	{
		$note="";
	}
	if($disp_note['disposition']==2 && $disp_note['ref_doctor_to']!="other")
	{
		$em=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$disp_note[ref_doctor_to]'"));
		$note="Refer to ".$em['name'];
	}
	if($disp_note['disposition']==2 && $disp_note['ref_doctor_to']=="other")
	{
		$note="Refer to ".$disp_note['doctor_name'];
	}
	if($disp_note['disposition']==1)
	{
		//$em=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$disp_note[user]'"));
		$note="Admit Patient";
	}
	$conslt_doc_id=mysqli_fetch_array(mysqli_query($link," SELECT `consultantdoctorid`,`appointment_date` FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd' "));
	$conslt_doc_name=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$conslt_doc_id[consultantdoctorid]' "));
	$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
	$phone="";
	$addr="";
	if($company_info["phone1"])
	$phone.=$company_info["phone1"];
	if($company_info["phone2"])
	{
		if($phone)
		$phone.=", ".$company_info["phone2"];
		else
		$phone.=$company_info["phone2"];
	}
	if($company_info["phone3"])
	{
		if($phone)
		$phone.=", ".$company_info["phone3"];
		else
		$phone.=$company_info["phone3"];
	}
	if($company_info["address"])
	{
		if($addr)
		$addr.=", ".$company_info["address"];
		else
		$addr.=$company_info["address"];
	}
	if($company_info["city"])
	{
		if($addr)
		$addr.=", ".$company_info["city"];
		else
		$addr.=$company_info["city"];
	}
	if($company_info["pincode"])
	{
		if($addr)
		$addr.="-".$company_info["pincode"];
		else
		$addr.=$company_info["pincode"];
	}
	if($company_info["state"])
	{
		if($addr)
		$addr.=", ".$company_info["state"];
		else
		$addr.=$company_info["state"];
	}
	?>
		<table style="margin:0 auto;font-size:13px;" class="table tab">
			<tr>
				<td width="60%"><b>PATIENT NAME :</b> <?php echo $pat['name']; ?></td>
				<td style="text-align:;"><b>Age / Gender :</b> <?php echo $pat['age']." ".$pat['age_type']." / ".$pat['sex']; ?></td>
			</tr>
			<tr>
				<!--<td>Regd No : <?php echo $pat["reg_no"]; ?></td>-->
				<td>
					<b>UHID :</b> <?php echo $pat["patient_id"]; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span><b>OPD :</b> <?php echo $opd; ?></span>
				</td>
				<td style="text-align:;"><b>Admit Date :</b> <?php echo convert_date_g($pat['date']); ?></td>
			</tr>
			<tr>
				<td><b>Referred By :</b> <?php echo $ref_doc["ref_name"]; ?></td>
				<td style="text-align:;"><b>Visit Date :</b> <?php echo convert_date_g($conslt_doc_id['appointment_date']); ?></td>
			</tr>
		</table>
		<hr/>
		<div class="" style="padding:0;font-size:13px;">
			<?php
			if($comp_num>0)
			{
				?><br/>
			<p><b>Chief Complaints:</b></p>
				<?php
				while($comp=mysqli_fetch_array($complaints))
				{ ?>
			<span style="text-indent:0px;text-align:justify;font-size:13px;"><?php echo $comp['comp_one'].' for '.$comp['comp_two'].' '.$comp['comp_three'].'.'; ?></span><br/>
			<?php
				}
			}
			if($impression['clinical_impression']!='')
			{?>
			<p><b>Clinical Impression:</b></p>
			<p style="text-indent:0px;text-align:justify;"><?php echo $impression['clinical_impression']; ?></p>
			<?php
			}
			if($vit_num>0)
			{
				?>
			<p><b>Vitals:</b></p>
				<?php
				while($vit=mysqli_fetch_array($vital))
				{
				?>
			<table class="table table-condensed tab" style="font-size:13px;">
				<tr>
					<?php if($vit["height"]){echo "<td>Height: ".$vit["height"]." Cm</td>";$td++;}
					if($vit["weight"]){echo "<td>Weight: ".$vit["weight"]." Kg</td>";$td++;}
					if($vit["systolic"] && $vit["diastolic"]){echo "<td>BP: ".$vit["systolic"].'/'.$vit["diastolic"]."</td>";$td++;}
					if($td==4)
					{
						echo "</tr><tr>";
						$td=0;
					}
					if($vit["BMI_1"]){echo "<td>BMI: ".$vit["BMI_1"].".".$vit["BMI_2"]."</td>";$td++;}
					if($td==4)
					{
						echo "</tr><tr>";
						$td=0;
					}
					if($vit["spo2"]){echo "<td>SPO<sub>2</sub>: ".$vit["spo2"]."</td>";$td++;}
					if($td==4)
					{
						echo "</tr><tr>";
						$td=0;
					}
					if($vit["temp"]){echo "<td>Temperature: ".$vit["temp"]."</td>";$td++;}
					if($td==4)
					{
						echo "</tr><tr>";
						$td=0;
					}
					if($vit["pulse"]){echo "<td>Pulse: ".$vit["pulse"]."</td>";$td++;}
					if($td==4)
					{
						echo "</tr><tr>";
						$td=0;
					}
					if($vit["PR"]){echo "<td>PR: ".$vit["PR"]."</td>";$td++;}
					if($td==4)
					{
						echo "</tr><tr>";
						$td=0;
					}
					if($vit["RR"]){echo "<td>RR: ".$vit["RR"]."</td>";$td++;}
					if($td==4)
					{
						echo "</tr><tr>";
						$td=0;
					}
					?>
				</tr>
			</table>
			<?php
				}
			}
			$his=mysqli_query($link,"Select * from pat_examination where patient_id='$uhid' and opd_id='$opd'");
			if(mysqli_num_rows($his)>0)
			{
				$hs=mysqli_fetch_array($his);
				if($hs['history'])
				{
					$hist=str_replace("\n","<br/>",$hs['history']);
				?><br/>
			<p><b>History:</b></p>
			<p style="text-indent:0px;text-align:justify;font-size:13px;"><?php echo $hist; ?></p>
				<?php
				}
				if($hs['examination'])
				{
					$exm=str_replace("\n","<br/>",$hs['examination']);
				?>
			<p><b>Examination:</b></p>
			<p style="text-indent:0px;text-align:justify;font-size:14px;"><?php echo $exm; ?></p>
				<?php
				}
			}
			
			$diag=mysqli_query($link," Select * from pat_diagnosis where patient_id='$uhid' and opd_id='$opd' ");
			$dg_num=mysqli_num_rows($diag);
			if($dg_num>0)
			{
			?>
			<p><b>Diagnosis:</b></p>
			<p>
				<table class="table table-condensed" style="font-size:13px;">
				<tr>
					<th width="10%">Sl No.</th>
					<th>Diagnosis</th>
				</tr>
				<?php
				$mm=1;
				while($dg=mysqli_fetch_array($diag))
				{
				?>
				<tr>
					<td><?php echo $mm;?></td>
					<td><?php echo $dg['diagnosis'];?></td>
				</tr>
				<?php
				$mm++;
				}
				?>
				</table>
			</p>
			<?php
			}
			
				$medi=mysqli_query($link," Select * from medicine_check where patient_id='$uhid' and opd_id='$opd' ");
				$medi_num=mysqli_num_rows($medi);
				if($medi_num>0)
				{
				?>
				<p><b>Rx</b></p>
				<table class="table table-condensed" style="font-size:13px;">
					<tr>
						<th>Sl No.</th>
						<th>Medication</th>
						<th>Dosage</th>
						<th>Instruction</th>
					</tr>
				<?php
				$i=1;
				while($medi_detail=mysqli_fetch_array($medi))
				{
					$medi_name=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `item_master` WHERE `item_code`='$medi_detail[item_code]' "));
					if($medi_detail["frequency"]==1)
					$freq="Immediately";
					if($medi_detail["frequency"]==2)
					$freq="Once a day";
					if($medi_detail["frequency"]==3)
					$freq="Twice a day";
					if($medi_detail["frequency"]==4)
					$freq="Thrice a day";
					if($medi_detail["frequency"]==5)
					$freq="Four times a day";
					if($medi_detail["frequency"]==6)
					$freq="Five times a day";
					if($medi_detail["frequency"]==7)
					$freq="Every hour";
					if($medi_detail["frequency"]==8)
					$freq="Every 2 hours";
					if($medi_detail["frequency"]==9)
					$freq="Every 3 hours";
					if($medi_detail["frequency"]==10)
					$freq="Every 4 hours";
					if($medi_detail["frequency"]==11)
					$freq="Every 5 hours";
					if($medi_detail["frequency"]==12)
					$freq="Every 6 hours";
					if($medi_detail["frequency"]==13)
					$freq="Every 7 hours";
					if($medi_detail["frequency"]==14)
					$freq="Every 8 hours";
					if($medi_detail["frequency"]==15)
					$freq="Every 10 hours";
					if($medi_detail["frequency"]==16)
					$freq="Every 12 hours";
					if($medi_detail["frequency"]==17)
					$freq="On alternate days";
					if($medi_detail["frequency"]==18)
					$freq="Once a week";
					if($medi_detail["frequency"]==19)
					$freq="Twice a week";
					if($medi_detail["frequency"]==20)
					$freq="Thrice a week";
					if($medi_detail["frequency"]==21)
					$freq="Every 2 weeks";
					if($medi_detail["frequency"]==22)
					$freq="Every 3 weeks";
					if($medi_detail["frequency"]==23)
					$freq="Once a month";
					
					if($medi_detail["instruction"]==1)
					$instr="As Directed";
					if($medi_detail["instruction"]==2)
					$instr="Before Meal";
					if($medi_detail["instruction"]==3)
					$instr="Empty Stomach";
					if($medi_detail["instruction"]==4)
					$instr="After Meal";
					if($medi_detail["instruction"]==5)
					$instr="In the Morning";
					if($medi_detail["instruction"]==6)
					$instr="In the Evening";
					if($medi_detail["instruction"]==7)
					$instr="At Bedtime";
					if($medi_detail["instruction"]==8)
					$instr="Immediately";
				?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $medi_name["item_name"]; ?></td>
						<td><?php echo $medi_detail["dosage"]; ?></td>
						<td><?php echo $instr; ?></td>
					</tr>
				<?php
					$i++;
					}
				?>
			</table>
			<?php
			}
			$cons=mysqli_fetch_array($consultation);
			if($cons['con_note']!='')
			{
			?>
			<p><b>Consultation Advised:</b></p>
			<?php
				$array = preg_split('/$\R?^/m', $cons['con_note']);
				foreach($array as $treatment)
				{
					if($treatment)
					{
			?>
					<p style="text-indent:0px;text-align:justify;"><?php echo $treatment; ?></p>
			<?php
					}
				}
			}
			
			$test=mysqli_query($link,"Select * from patient_test_details where patient_id='$uhid' and opd_id='$opd' ");
			$test_num=mysqli_num_rows($test);
			if($test_num!=0)
			{
		?>
				<p><b>Investigation:</b></p>
				<p style="text-indent:0px;font-size:13px;">
		<?php
				$tests="";
				$i=1;
				while($test_detail=mysqli_fetch_array($test))
				{
					$test_name=mysqli_fetch_array(mysqli_query($link,"Select distinct testname from testmaster where testid='$test_detail[testid]'"));
					if($tests)
					$tests.=", ".$test_name['testname'];
					else
					$tests.=$test_name['testname'];
					//echo $test_name['testname']." , ";
				}
				echo $tests;
				?>
				</p>
				<?php
			}
			?>
		<?php
	if($disp_note['disp_note'])
	{
		$nots=str_replace(",", "<br/>", "$disp_note[disp_note]");
	?>
	<b>Advice : </b><br/><?php echo $nots;
	
	}
	if($note)
	{
	?>
	<br/>
	<b>Disposition : </b><?php echo $note;
	}
	?>
	</div>
			<span style="float:right;"><b><?php echo $conslt_doc_name['Name']; ?></b></span>
			<br/>
			<br/>
<style>

.tab > thead > tr > th, .tab > tbody > tr > th, .tab > tfoot > tr > th, .tab > thead > tr > td, .tab > tbody > tr > td, .tab > tfoot > tr > td
{
	border-top: 1px solid #FFF;
	padding: 0px;
	line-height: 20px;
}
hr
{
	border-top: 1px solid #737373;
	margin-top:3px;
	margin-bottom:3px;
}
	</style>
	<?php
}

if($_POST["type"]=="edit_certficate")
{
	$slno=$_POST['slno'];
	$usr=$_POST['usr'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_delivery_det` WHERE `slno`='$slno'"));
	?>
	<table class="table table-condensed" id="edt_deli_tbl">
		<tr id="tr<?php echo $i;?>" class="tr">
			<th>D.O.B <i class="icon-calendar icon-large" style="color:#F0A11F;"></i><br/><input type="text" class="txt" readonly="readonly" style="cursor:unset;" id="edit_dob" value="<?php echo $v['dob'];?>" placeholder="Date Of Birth" /></th>
			<th>
				Time Of Birth <i class="icon-time icon-large" style="color:#F0A11F;"></i><br/>
				<!--<input type="text" class="txt" readonly="readonly" style="cursor:unset;" id="edit_time" value="<?php echo $v['born_time'];?>" placeholder="HH:MM" /> <i class="icon-time icon-large" style="color:#F0A11F;"></i>-->
				<?php
				$b_tim=explode(":",date("h:i:s", strtotime($v['born_time'])));
				$b_hours=$b_tim[0];
				$b_mins=$b_tim[1];
				$b_secs=$b_tim[3];
				$am_pm=date("a", strtotime($v['born_time']));
				//echo date("h:i:s", strtotime($v['born_time']));
				?>
				<select id="edit_hrs" class="span1"> <!---select hours--->
					<option value="0">Hours</option>
					<?php
					for($h=1; $h<=12; $h++)
					{
					?>
					<option value="<?php echo $h;?>" <?php if($h==$b_hours){echo "selected='selected'";}?>><?php echo $h;?></option>
					<?php
					}
					?>
				</select>
				<select id="edit_mins" class="span1"> <!---select minutes--->
					<option value="0">Minutes</option>
					<?php
					for($m=0; $m<60; $m++)
					{
						$m = str_pad($m,2,"0",STR_PAD_LEFT);
					?>
					<option value="<?php echo $m;?>" <?php if($m==$b_mins){echo "selected='selected'";}?>><?php echo $m;?></option>
					<?php
					}
					?>
				</select>
				<select id="edit_ampm" class="span1"> <!---select am/pm--->
					<option value="0">Select</option>
					<option value="am" <?php if($am_pm=="am"){echo "selected='selected'";}?>>AM</option>
					<option value="pm" <?php if($am_pm=="pm"){echo "selected='selected'";}?>>PM</option>
				</select>
			</th>
			<th>Sex<br/>
				<select id="edit_sex" class="span2">
					<option value="0">Select</option>
					<option value="Male" <?php if($v['sex']=="Male"){echo "selected='selected'";}?>>Male</option>
					<option value="Female" <?php if($v['sex']=="Female"){echo "selected='selected'";}?>>Female</option>
				</select>
			</th>
			<!--<th>Weight<br/><input type="text" id="edit_wt" onfocus="weight_ui_div()" readonly="readonly" style="cursor:unset;" class="txt" value="<?php echo $v['weight'];?>" placeholder="#.## K.G" /></th>-->
			<th>Weight<br/><input type="text" id="edit_wt" class="txt" value="<?php echo $v['weight'];?>" placeholder="#.## K.G" /></th>
		</tr>
		<tr>
			<th>Blood Group<br/>
				<select id="edit_blood" class="span2">
					<option value="">Select</option>
					<option value="O Positive" <?php if($v['blood_group']=="O Positive"){echo "selected='selected'";}?>>O Positive</option>
					<option value="O Negative" <?php if($v['blood_group']=="O Negative"){echo "selected='selected'";}?>>O Negative</option>
					<option value="A Positive" <?php if($v['blood_group']=="A Positive"){echo "selected='selected'";}?>>A Positive</option>
					<option value="A Negative" <?php if($v['blood_group']=="A Negative"){echo "selected='selected'";}?>>A Negative</option>
					<option value="B Positive" <?php if($v['blood_group']=="B Positive"){echo "selected='selected'";}?>>B Positive</option>
					<option value="B Negative" <?php if($v['blood_group']=="B Negative"){echo "selected='selected'";}?>>B Negative</option>
					<option value="AB Positive" <?php if($v['blood_group']=="AB Positive"){echo "selected='selected'";}?>>AB Positive</option>
					<option value="AB Negative" <?php if($v['blood_group']=="AB Negative"){echo "selected='selected'";}?>>AB Negative</option>
				</select>
			</th>
			<th>
				Delivery Mode<br/>
				<select id="edit_dmode" class="span2">
					<option value="0">Select</option>
					<option value="LSCS" <?php if($v['delivery_mode']=="LSCS"){echo "selected='selected'";}?>>LSCS</option>
					<option value="Normal" <?php if($v['delivery_mode']=="Normal"){echo "selected='selected'";}?>>Normal</option>
					<option value="Forceps" <?php if($v['delivery_mode']=="Forceps"){echo "selected='selected'";}?>>Forceps</option>
					<option value="Vacuum" <?php if($v['delivery_mode']=="Vacuum"){echo "selected='selected'";}?>>Vacuum</option>
					<option value="Ventose" <?php if($v['delivery_mode']=="Ventose"){echo "selected='selected'";}?>>Ventose</option>
				</select>
			</th>
			<th>
				Conducted By<br/>
				<select id="edit_conducted" class="span3">
					<option value="0">Select</option>
					<?php
					$cc=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($c=mysqli_fetch_array($cc))
					{
					?>
					<option value="<?php echo $c['consultantdoctorid'];?>" <?php if($v['conducted_by']==$c['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $c['Name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th>
				Dead Tag<br/>
				<select id="edit_tag" class="span2">
					<option value="No" <?php if($v['dead_tag']=="No"){echo "selected='selected'";}?>>No</option>
					<option value="Yes" <?php if($v['dead_tag']=="Yes"){echo "selected='selected'";}?>>Yes</option>
				</select>
			</th>
		</tr>
	</table>
	<button type="button" class="btn btn-primary" id="save_cert_edit" onclick="save_cert_edit('<?php echo $slno;?>')"><i class="icon icon-ok"></i> Ok</button>
	<button type="button" class="btn btn-danger" id="close_cert_edit" onclick="close_cert_edit('<?php echo $slno;?>')"><i class="icon icon-ban-circle"></i> Cancel</button>
	<style>
		#edt_deli_tbl tr:hover
		{
			background:none;
		}
		#edt_deli_tbl tr th, #edt_deli_tbl tr td
		{
			padding-top: 0px;
			padding-bottom: 0px;
		}
	</style>
	<script>
		$("#edit_dob").datepicker({dateFormat: 'yy-mm-dd',maxDate:'0'});
		$("#edit_time").timepicker({minutes: {starts: 0,interval: 01,}});
		//$("#edit_wt").timepicker({showPeriodLabels:false,showLeadingZero: false,defaultTime:'',timeSeparator:'.',hourText: 'KG',minuteText: 'Gram',hours:{starts: 1,ends: 4},minutes: {starts: 0,ends:99,interval: 01,}});
	</script>
	<?php
}

if($_POST["type"]=="save_cert_edit")
{
	$sl=$_POST['sl'];
	$edit_dob=$_POST['edit_dob'];
	//$edit_time=$_POST['edit_time'];
	$edit_time=date("H:i:s", strtotime($_POST['edit_time']));
	$edit_sex=$_POST['edit_sex'];
	$edit_wt=$_POST['edit_wt'];
	$edit_blood=$_POST['edit_blood'];
	$edit_dmode=$_POST['edit_dmode'];
	$edit_conducted=$_POST['edit_conducted'];
	$edit_tag=$_POST['edit_tag'];
	//echo $sl;
	
	mysqli_query($link,"UPDATE `ipd_pat_delivery_det` SET `sex`='$edit_sex',`dob`='$edit_dob',`born_time`='$edit_time',`weight`='$edit_wt',`blood_group`='$edit_blood',`delivery_mode`='$edit_dmode',`conducted_by`='$edit_conducted',`dead_tag`='$edit_tag' WHERE `slno`='$sl'");
	
	$pat_delvry_det_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `slno`='$sl' ");
	
	while($pat_delvry_det=mysqli_fetch_array($pat_delvry_det_qry))
	{
		mysqli_query($link,"UPDATE `patient_info` SET `sex`='$edit_sex',`dob`='$edit_dob',`blood_group`='$edit_blood',`father_name`='$edit_fat' WHERE `patient_id`='$pat_delvry_det[baby_uhid]'");
		
		//mysqli_query($link,"UPDATE `patient_info_rel` SET `father_name`='$edit_fat' WHERE `patient_id`='$pat_delvry_det[baby_uhid]'");
	}
	
	echo "Updated";
}

if($_POST["type"]=="sav_fat_nam")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$edit_fat=mysqli_real_escape_string($link, $_POST['edit_fat']);
	
	mysqli_query($link,"UPDATE `ipd_pat_delivery_det` SET `father_name`='$edit_fat' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	
	$pat_delvry_det_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
	
	while($pat_delvry_det=mysqli_fetch_array($pat_delvry_det_qry))
	{
		mysqli_query($link,"UPDATE `patient_info_rel` SET `father_name`='$edit_fat' WHERE `patient_id`='$pat_delvry_det[baby_uhid]'");
	}
	
	echo "Updated";
}

if($_POST["type"]=="del_certficate")
{
	$sl=$_POST['sl'];
	mysqli_query($link,"DELETE FROM `ipd_pat_delivery_det` WHERE `slno`='$sl'");
	echo "Deleted";
}

if($_POST["type"]=="pat_last_bed_date")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$pat_bed_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` DESC "));
	echo $pat_bed_det["date"];
	
}

if($_POST["type"]=="oo")
{
	$slno=$_POST['slno'];
}
?>
