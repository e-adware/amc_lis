<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");
$time=date("H:i:s");

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


if($_POST["type"]==1)
{
	$id=$_POST['id'];
	$grade=$_POST['grade'];
	$cab=$_POST['cab'];
	$typ=$_POST['typ'];
	$dept=$_POST['dept'];
	$emp=$_POST['emp'];
	$fee=$_POST['fee'];
	$user=$_POST['user'];
	/*
	foreach($emp as $em)
	{
		if($em)
		{
			$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_resource_master` WHERE `emp_id`='$em'"));
			if($n==0)
			{
				mysqli_query($link,"INSERT INTO `ot_resource_master`(`grade_id`, `type_id`, `ot_dept_id`, `emp_id`, `charge_id`) VALUES ('$grade','$typ','$dept','$em','$fee')");
			}
		}
	}
	echo "Saved";
	*/
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `ot_resource_master` SET `grade_id`='$grade',`ot_cabin_id`='$cab',`type_id`='$typ',`ot_dept_id`='$dept',`emp_id`='$emp',`charge_id`='$fee' WHERE `id`='$id'"))
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
		$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_resource_master` WHERE `grade_id`='$grade' AND `ot_cabin_id`='$cab' AND `type_id`='$typ'"));
		if($n>0)
		{
			echo "Already exist";
		}
		else
		{
			if(mysqli_query($link,"INSERT INTO `ot_resource_master`(`grade_id`, `ot_cabin_id`, `type_id`, `ot_dept_id`, `emp_id`, `charge_id`) VALUES ('$grade','$cab','$typ','$dept','$emp','$fee')"))
			{
				echo "Saved";
			}
			else
			{
				echo "Error";
			}
		}
	}
}

if($_POST["type"]==2)
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `grade_id`,`ot_cabin_id`,`type_id`,`ot_dept_id`,`emp_id`,`charge_id` FROM `ot_resource_master` WHERE `id`='$id'"));
	//echo $id."#@#".$d['grade_id']."#@#".$d['type_id']."#@#".$d['ot_dept_id']."#@#".$d['emp_id']."#@#".$d['charge_id']."#@#";
	echo $id."#@#".$d['grade_id']."#@#".$d['ot_cabin_id']."#@#".$d['type_id']."#@#".$d['charge_id']."#@#";
}

if($_POST["type"]==3)
{
	$grade=$_POST['grade'];
	$user=$_POST['user'];
	$srch=$_POST['srch'];
	
	if($user==101 || $user==102)
	{
		$btn_style="";
	}else
	{
		$btn_style="style='display:none;'";
	}
	
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_resource_master` WHERE `emp_id` IN (SELECT `emp_id` FROM `employee` WHERE `name` like '%$srch%') ORDER BY `grade_id`,`ot_cabin_id`,`type_id`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_resource_master` ORDER BY `grade_id`,`ot_cabin_id`,`type_id`");
	}
	?>
	<table class="table table-condensed table-bordered" id="tblData">
		<tr>
			<th>#</th>
			<th>Grade</th>
			<th>Cabin</th>
			<th>Type</th>
			<th>Amount</th>
			<th <?php echo $btn_style; ?>><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			$gr=mysqli_fetch_array(mysqli_query($link,"SELECT `grade_name` FROM `ot_grade_master` WHERE `grade_id`='$r[grade_id]'"));
			$t=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$r[type_id]'"));
			$cab=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_cabin_name` FROM `ot_cabin_master` WHERE `ot_cabin_id`='$r[ot_cabin_id]'"));
			//$nm=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[emp_id]'"));
		?>
		<tr class="nm">
			<!--<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $n;?></td>-->
			<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $n;?></td>
			<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $gr['grade_name'];?></td>
			<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $cab['ot_cabin_name'];?></td>
			<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $t['type'];?></td>
			<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo number_format($r['charge_id'],2);?></td>
			<td <?php echo $btn_style; ?> ><i class="icon-remove icon-large" onclick="confirmm('<?php echo $r['id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
<?php
}

if($_POST["type"]==4)
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `ot_resource_master` WHERE `id`='$id'");
	echo "Deleted";
}

if($_POST["type"]==5)
{
	//$q=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`!='1' AND `emp_id` NOT IN (SELECT DISTINCT `emp_id` FROM `ot_resource_link`)");
	$q=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`!='1' ");
	?>
	<select id="emp" multiple="true">
		<?php
		while($r=mysqli_fetch_array($q))
		{
		?>
		<option value="<?php echo $r['emp_id'];?>"><?php echo $r['name'];?></option>
		<?php
		}
		?>
	</select>
	<?php
}

if($_POST["type"]==6)
{
	$id=$_POST['id'];
	$typ=$_POST['typ'];
	$emp=$_POST['emp'];
	$user=$_POST['user'];
	
	foreach($emp as $em)
	{
		if($em)
		{
			$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_resource_link` WHERE `emp_id`='$em' and `type_id`='$typ'"));
			if($n==0)
			{
				//mysqli_query($link,"INSERT INTO `ot_resource_master`(`grade_id`, `type_id`, `ot_dept_id`, `emp_id`, `charge_id`) VALUES ('$grade','$typ','$dept','$em','$fee')");
				mysqli_query($link,"INSERT INTO `ot_resource_link`(`type_id`, `emp_id`) VALUES ('$typ','$em')");
			}
		}
	}
	echo "Saved";
	
}

if($_POST["type"]==7)
{
	$srch=$_POST['srch'];
	if($srch)
	{
		//$q=mysqli_query($link,"SELECT * FROM `ot_resource_master` WHERE `emp_id` IN (SELECT `emp_id` FROM `employee` WHERE `name` like '%$srch%') ORDER BY `type_id`");
		$q=mysqli_query($link,"SELECT a.* FROM `ot_resource_link` a, `ot_type_master` b WHERE a.`type_id`=b.`type_id` AND b.`type` like '$srch%' ORDER BY a.`type_id`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_resource_link` ORDER BY `type_id`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
			<tr>
				<th>#</th><th>Resourse Type</th><th>Employee</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
			</tr>
			<?php
			$n=1;
			while($r=mysqli_fetch_array($q))
			{
				$gr=mysqli_fetch_array(mysqli_query($link,"SELECT `grade_name` FROM `ot_grade_master` WHERE `grade_id`='$r[grade_id]'"));
				$t=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$r[type_id]'"));
				$em=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[emp_id]'"));
			?>
			<tr class="nm">
				<!--<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $n;?></td>-->
				<td><?php echo $n;?></td>
				<td><?php echo $t['type'];?></td>
				<td><?php echo $em['name'];?></td>
				<td><i class="icon-remove icon-large" onclick="confirmm('<?php echo $r['link_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
	<?php
}

if($_POST["type"]==8)
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `ot_resource_link` WHERE `link_id`='$id'");
	echo "Deleted";
}

if($_POST["type"]==999)
{
	$ward=$_POST['ward'];
	$uhid=$_POST['uhid'];
}
?>
