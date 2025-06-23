<?php
include("../../includes/connection.php");

$type=$_POST['type'];

if($type=="save_diagnosis")
{
	$id=$_POST['id'];
	$dept=$_POST['dept'];
	$name=$_POST['name'];
	$name= str_replace("'", "''", "$name");
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `diagnosis_master` SET `speciality_id`='$dept', `diagnosis`='$name' WHERE `diagnosis_id`='$id'"))
		echo "Updated";
		else
		echo "Error";
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `diagnosis_master`(`speciality_id`, `diagnosis`) VALUES ('$dept','$name')"))
		echo "Saved";
		else
		echo "Error";
	}
}

if($type=="load_diagnosis")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$qry=mysqli_query($link,"SELECT * FROM `diagnosis_master` WHERE `diagnosis` like '$srch%'");
	}
	else
	{
		$qry=mysqli_query($link,"SELECT * FROM `diagnosis_master`");
	}
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th width="70%">Diagnosis</th><th>Action</th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($qry))
		{
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['diagnosis'];?></td>
			<td>
				<input type="button" class="btn btn-info btn-mini" onclick="edt('<?php echo $r['diagnosis_id'];?>')" value="Edit" />
				<input type="button" class="btn btn-danger btn-mini" onclick="del('<?php echo $r['diagnosis_id'];?>')" value="Delete" />
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($type=="edit_diagnosis")
{
	$id=$_POST['id'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `diagnosis_master` WHERE `diagnosis_id`='$id'"));
	echo $id."@govin@".$v['speciality_id']."@govin@".$v['diagnosis']."@govin@";
}

if($type=="delete_diagnosis")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `diagnosis_master` WHERE `diagnosis_id`='$id'"))
	echo "Deleted";
	else
	echo "Error";
}

if($type=="oo")
{
	
}
?>
