<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");

if($_POST["type"]=="load_clinical_procedures")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_clinical_procedure` WHERE `name` like '$srch%' ORDER BY `ot_dept_id`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_clinical_procedure` ORDER BY `ot_dept_id`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
		<tr>
			<th>#</th><th>Department</th><th>Procedure Name</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			$dep=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$r[ot_dept_id]'"));
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['procedure_id'];?>')"><?php echo $dep['ot_dept_name'];?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['procedure_id'];?>')"><?php echo $r['name'];?></td>
			<td><i class="icon-remove icon-large text-danger" onclick="confirmm('<?php echo $r['procedure_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_clinical_procedures_det")
{
	$id=$_POST['id'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_clinical_procedure` WHERE `procedure_id`='$id'"));
	echo $id."#ea#".$v['ot_dept_id']."#ea#".$v['name']."#ea#";
}

if($_POST["type"]=="save_clinical_procedures")
{
	$id=$_POST['id'];
	$dept=$_POST['dept'];
	$name=$_POST['name'];
	$name=mysqli_real_escape_string($link,$name);
	$usr=$_POST['usr'];
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `ot_clinical_procedure` SET `ot_dept_id`='$dept',`name`='$name',`user`='$usr' WHERE `procedure_id`='$id'"))
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
		if(mysqli_query($link,"INSERT INTO `ot_clinical_procedure`(`ot_dept_id`, `name`, `user`) VALUES ('$dept','$name','$usr')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="delete_clinical_procedures")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `ot_clinical_procedure` WHERE `procedure_id`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="oo")
{
	$id=$_POST['id'];
}
?>
