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

if($_POST["type"]=="load_ot_grade")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_grade_master` WHERE `grade_name` like '$srch%' ORDER BY `grade_name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_grade_master` ORDER BY `grade_name`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
			<tr>
				<th width="10%">#</th><th>Department</th><th>Grade</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
			</tr>
			<?php
			$n=1;
			while($r=mysqli_fetch_array($q))
			{
				$d=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$r[ot_dept_id]'"));
			?>
			<tr>
				<td><?php echo $n;?></td>
				<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['grade_id'];?>')"><?php echo $d['ot_dept_name'];?></td>
				<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['grade_id'];?>')"><?php echo $r['grade_name'];?></td>
				<td><i class="icon-remove icon-large" onclick="confirmm('<?php echo $r['grade_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
	<?php
}

if($_POST["type"]=="save_ot_grade")
{
	$id=$_POST['id'];
	$dept=$_POST['dept'];
	$grade=$_POST['grade'];
	
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `ot_grade_master` SET `ot_dept_id`='$dept',`grade_name`='$grade' WHERE `grade_id`='$id'"))
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
		if(mysqli_query($link,"INSERT INTO `ot_grade_master`(`grade_name`,`ot_dept_id`) VALUES ('$grade','$dept')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
	
}

if($_POST["type"]=="load_ot_grade_details")
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_id`,`grade_name` FROM `ot_grade_master` WHERE `grade_id`='$id'"));
	echo $id."#@#".$d['ot_dept_id']."#@#".$d['grade_name']."#@#";
}

if($_POST["type"]=="delete_ot_grade")
{
	echo $id=$_POST['id'];
	
}

if($_POST["type"]=="oooo")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
}
?>
