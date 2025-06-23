<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");

if($_POST["type"]==1)
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT a.* FROM `ot_cabin_rate` a, `ot_cabin_master` b WHERE a.`ot_cabin_id`=b.`ot_cabin_id` AND b.`ot_cabin_name` like '$srch%' ORDER BY b.`ot_cabin_name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_cabin_rate` ORDER BY `grade_id`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
		<tr>
			<th>#</th><th>Grade</th><th>Cabin Name</th><th>Rate</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			$gr=mysqli_fetch_array(mysqli_query($link,"SELECT `grade_name` FROM `ot_grade_master` WHERE `grade_id`='$r[grade_id]'"));
			$cab=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_cabin_name` FROM `ot_cabin_master` WHERE `ot_cabin_id`='$r[ot_cabin_id]'"));
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['slno'];?>')"><?php echo $gr['grade_name'];?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['slno'];?>')"><?php echo $cab['ot_cabin_name'];?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['slno'];?>')"><?php echo $r['amount'];?></td>
			<td><i class="icon-remove icon-large text-danger" onclick="confirmm('<?php echo $r['slno'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]==2)
{
	$id=$_POST['id'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT `grade_id`, `ot_cabin_id`, `amount` FROM `ot_cabin_rate` WHERE `slno`='$id'"));
	echo $id."#ea#".$v['grade_id']."#ea#".$v['ot_cabin_id']."#ea#".$v['amount']."#ea#";
}

if($_POST["type"]==3)
{
	$id=$_POST['id'];
	$grade=$_POST['grade'];
	$cabin=$_POST['cabin'];
	$amount=$_POST['amount'];
	$usr=$_POST['usr'];
	if($id>0)
	{
		$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_cabin_rate` WHERE `grade_id`='$grade' AND `ot_cabin_id`='$cabin' AND `slno`!='$id'"));
		if($n>0)
		{
			echo "Already Exists";
		}
		else
		{
			if(mysqli_query($link,"UPDATE `ot_cabin_rate` SET `grade_id`='$grade',`ot_cabin_id`='$cabin',`amount`='$amount' WHERE `slno`='$id'"))
			{
				echo "Updated";
			}
			else
			{
				echo "Error";
			}
		}
	}
	else
	{
		$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_cabin_rate` WHERE `grade_id`='$grade' AND `ot_cabin_id`='$cabin'"));
		if($n>0)
		{
			echo "Already Exists";
		}
		else
		{
			if(mysqli_query($link,"INSERT INTO `ot_cabin_rate`(`grade_id`, `ot_cabin_id`, `amount`) VALUES ('$grade','$cabin','$amount')"))
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

if($_POST["type"]==4)
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `ot_cabin_rate` WHERE `slno`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="oo")
{
	$id=$_POST['id'];
}
?>
