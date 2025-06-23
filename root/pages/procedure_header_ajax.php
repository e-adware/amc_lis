<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");

if($_POST["type"]=="load_header")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `clinical_procedure_header` WHERE `name` like '%$srch%' ORDER BY `name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `clinical_procedure_header` ORDER BY `name`");
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
			<td><?php echo $n;?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['head_id'];?>')"><?php echo $r['name'];?></td>
			<td><i class="icon-remove icon-large text-danger" onclick="confirmm('<?php echo $r['head_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_header_det")
{
	$id=$_POST['id'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `clinical_procedure_header` WHERE `head_id`='$id'"));
	echo $id."#ea#".$v['name']."#ea#";
}

if($_POST["type"]=="save_clinical_procedures")
{
	$id=$_POST['id'];
	$name=mysqli_real_escape_string($link,$_POST['name']);
	$usr=$_POST['usr'];
	if($id>0)
	{
		mysqli_query($link,"UPDATE `clinical_procedure_header` SET `name`='$name' WHERE `head_id`='$id'");
		echo "Updated";
	}
	else
	{
		if($name)
		{
			mysqli_query($link,"INSERT INTO `clinical_procedure_header`(`name`) VALUES ('$name')");
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
