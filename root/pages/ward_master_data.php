<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="load_ward_id") // gov
{
	$i=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(ward_id) as max FROM `ward_master`"));
	echo $i['max']+1;
}


if($_POST["type"]=="load_ward") // gov
{
	$branch_id=$_POST['branch_id'];
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>ID</th><th>Ward</th><th>Floor</th><th>Action</th>
		</tr>
<?php
		$q=mysqli_query($link,"SELECT * FROM `ward_master` WHERE `branch_id`='$branch_id' ORDER BY `name`");
		while($r=mysqli_fetch_array($q))
		{
?>
		<tr>
			<td><?php echo $r['ward_id'];?></td>
			<td><?php echo $r['name'];?></td>
			<td><?php echo $r['floor_name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[sl_no];?>')" value="Edit" />
				<!--<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[sl_no];?>')" value="Delete" />-->
			</td>
		</tr>
<?php
		}
?>
	</table>
<?php
}

if($_POST["type"]=="edit_ward") // gov
{
	$sl=$_POST['sl'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ward_master` WHERE `sl_no`='$sl'"));
	echo $sl."@govin@".$d['ward_id']."@govin@".$d['name']."@govin@".$d['floor_name']."@govin@".$d['branch_id']."@govin@";
}

if($_POST["type"]=="save_ward")
{
	$branch_id=$_POST['branch_id'];
	$sl=$_POST['sl'];
	$id=$_POST['id'];
	$name=mysqli_real_escape_string($link, $_POST['name']);
	$floor_name=mysqli_real_escape_string($link, $_POST['floor_name']);
	if($sl>0)
	{
		if(mysqli_query($link,"UPDATE `ward_master` SET `ward_id`='$id',`name`='$name',`floor_name`='$floor_name',`branch_id`='$branch_id' WHERE `sl_no`='$sl'"))
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
		if(mysqli_query($link,"INSERT INTO `ward_master`(`ward_id`,`name`,`floor_name`,`branch_id`) VALUES ('$id','$name','$floor_name','$branch_id')"))
		{
			echo "Saved";
		}
		else
		{
			echo "INSERT INTO `ward_master`(`ward_id`,`name`,`floor_name`,`branch_id`) VALUES ('$id','$name','$floor_name','$branch_id')";
		}
	}
}

if($_POST["type"]=="search_ward") // gov
{
	$branch_id=$_POST['branch_id'];
	$srch=$_POST['srch'];
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>ID</th><th>Ward</th><th>Action</th>
		</tr>
<?php
		$q=mysqli_query($link,"SELECT * FROM `ward_master` WHERE `branch_id`='$branch_id' AND `name` like '%$srch%' ORDER BY `name`");
		while($r=mysqli_fetch_array($q))
		{
?>
		<tr>
			<td><?php echo $r['ward_id'];?></td>
			<td><?php echo $r['name'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r[sl_no];?>')" value="Edit" />
				<!--<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[sl_no];?>')" value="Delete" />-->
			</td>
		</tr>
<?php
		}
?>
	</table>
<?php
}

if($_POST["type"]=="delete_ward")
{
	$sl=$_POST['sl'];
	
	$ward=mysqli_fetch_array(mysqli_query($link, " SELECT `ward_id` FROM `ward_master` WHERE `sl_no`='$sl' "));
	
	if(mysqli_query($link,"DELETE FROM `ward_master` WHERE `sl_no`='$sl'"))
	{
		mysqli_query($link,"DELETE FROM `bed_other_charge` WHERE `bed_id` IN(SELECT `bed_id` FROM `bed_master` WHERE `room_id` IN (SELECT `room_id` FROM `room_master` WHERE `ward_id`='$ward[ward_id]'))");
		
		mysqli_query($link,"DELETE FROM `charge_master` WHERE `charge_id` IN(SELECT `charge_id` FROM `bed_master` WHERE `room_id` IN (SELECT `room_id` FROM `room_master` WHERE `ward_id`='$ward[ward_id]'))");
		
		mysqli_query($link,"DELETE FROM `bed_master` WHERE `room_id` IN (SELECT `room_id` FROM `room_master` WHERE `ward_id`='$ward[ward_id]')");
		
		mysqli_query($link,"DELETE FROM `room_master` WHERE `ward_id`='$ward[ward_id]'");
		
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="save_room")
{
	$branch_id=$_POST['branch_id'];
	
	if(!$branch_id)
	{
		$branch_id=$emp_info["branch_id"];
	}
	
	$id=$_POST['id'];
	$ward=$_POST['ward'];
	$room=$_POST['room'];
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `room_master` SET `ward_id`='$ward',`room_no`='$room' WHERE `room_id`='$id'"))
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
		if(mysqli_query($link,"INSERT INTO `room_master`(`ward_id`, `room_no`) VALUES ('$ward','$room')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="load_room") // gov
{
	$branch_id=$_POST['branch_id'];
?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th><th>Ward</th><th>Room No</th><th></th>
		</tr>
<?php
		$n=1;
		$q=mysqli_query($link,"SELECT * FROM `room_master` WHERE `ward_id` IN(SELECT `ward_id` FROM `ward_master` WHERE `branch_id`='$branch_id') ORDER BY `ward_id`");
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
				<!--<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[room_id];?>')" value="Delete" />-->
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
				<!--<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r[bed_id];?>')" value="Delete" />-->
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

if($_POST["type"]=="delete_room")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `room_master` WHERE `room_id`='$id'"))
	{
		mysqli_query($link,"DELETE FROM `bed_other_charge` WHERE `bed_id` IN(SELECT `bed_id` FROM `bed_master` WHERE `room_id`='$id')");
		
		mysqli_query($link,"DELETE FROM `charge_master` WHERE `charge_id` IN(SELECT `charge_id` FROM `bed_master` WHERE `room_id`='$id')");
		
		mysqli_query($link,"DELETE FROM `bed_master` WHERE `room_id`='$id'");
		
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_bed")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `bed_master` WHERE `bed_id`='$id'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

?>
