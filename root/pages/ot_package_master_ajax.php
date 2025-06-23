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
		$q=mysqli_query($link,"SELECT * FROM `ot_package_master` WHERE `name` like '%$srch%' ORDER BY `name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_package_master` ORDER BY `name`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
		<tr>
			<th>#</th><th>Package Name</th><th>Amount</th><!--<th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>-->
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['pack_id'];?>')"><?php echo $r['name'];?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['pack_id'];?>')"><?php echo $r['amount'];?></td>
			<!--<td><i class="icon-remove icon-large text-danger" onclick="confirmm('<?php echo $r['pack_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>-->
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
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_package_master` WHERE `pack_id`='$id'"));
	$amt=explode(".",$v['amount']);
	$amt=$amt[0];
	echo $id."#ea#".$v['ot_dept_id']."#ea#".$v['name']."#ea#".$amt."#ea#";
}

if($_POST["type"]==3)
{
	$id=$_POST['id'];
	$dept_id=$_POST['dept_id'];
	$name=mysqli_real_escape_string($link,$_POST['name']);
	$amount=$_POST['amount'];
	$usr=$_POST['usr'];
	if($id>0)
	{
		mysqli_query($link,"UPDATE `ot_package_master` SET `ot_dept_id`='$dept_id',`name`='$name',`amount`='$amount' WHERE `pack_id`='$id'");
		echo "Updated";
	}
	else
	{
		if($name)
		{
			mysqli_query($link,"INSERT INTO `ot_package_master`(`ot_dept_id`, `name`, `amount`) VALUES ('$dept_id','$name','$amount')");
			echo "Saved";
		}
		else
		{
			echo "Name Cannot Empty";
		}
	}
}

if($_POST["type"]=="delete_header_procedure")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `clinical_procedure_header` WHERE `head_id`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="oo")
{
	
}
?>
