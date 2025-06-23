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
		$q=mysqli_query($link,"SELECT * FROM `clinical_procedure` WHERE `name` like '$srch%' ORDER BY `name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `clinical_procedure` ORDER BY `name`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
		<tr>
			<th>#</th><th>Grade</th><th>Procedure Name</th><th>Rate</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			$gr=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_grade_master` WHERE `grade_id`='$r[grade_id]'"));
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['procedure_id'];?>')"><?php echo $gr['grade_name'];?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['procedure_id'];?>')"><?php echo $r['name'];?></td>
			<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['procedure_id'];?>')"><?php echo $r['rate'];?></td>
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
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `clinical_procedure` WHERE `procedure_id`='$id'"));
	echo $id."#ea#".$v['name']."#ea#".$v['header_id']."#ea#".$v['grade_id']."#ea#".$v['rate']."#ea#";
}

if($_POST["type"]=="save_clinical_procedures")
{
	$id=$_POST['id'];
	$head=$_POST['head'];
	$name=$_POST['name'];
	$name=mysqli_real_escape_string($link,$name);
	$grade=$_POST['grade'];
	$rate=$_POST['rate'];
	$all_res=$_POST['all_res'];
	$usr=$_POST['usr'];
	if($id>0)
	{
		mysqli_query($link,"UPDATE `clinical_procedure` SET `name`='$name',`header_id`='$head',`grade_id`='$grade',`rate`='$rate' WHERE `procedure_id`='$id'");
		mysqli_query($link,"DELETE FROM `ot_clinical_resourse` WHERE `procedure_id`='$id'");
		$all=explode("#@#",$all_res);
		foreach($all as $al)
		{
			$a=explode("@@",$al);
			$res_id=$a[0];
			$res_amt=$a[1];
			if($res_id && $res_amt)
			{
				mysqli_query($link,"INSERT INTO `ot_clinical_resourse`(`procedure_id`, `grade_id`, `resourse_id`, `amount`) VALUES ('$id','$grade','$res_id','$res_amt')");
			}
		}
		echo "Updated";
	}
	else
	{
		if($name)
		{
			if(mysqli_query($link,"INSERT INTO `clinical_procedure`(`name`, `header_id`, `grade_id`, `rate`, `user`) VALUES ('$name','$head','$grade','$rate','$usr')"))
			{
				$c_id=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(`procedure_id`) AS mx FROM `clinical_procedure` WHERE `user`='$usr'"));
				$p_id=$c_id['mx'];
				
				$all=explode("#@#",$all_res);
				foreach($all as $al)
				{
					$a=explode("@@",$al);
					$res_id=$a[0];
					$res_amt=$a[1];
					if($res_id && $res_amt)
					{
						mysqli_query($link,"INSERT INTO `ot_clinical_resourse`(`procedure_id`, `grade_id`, `resourse_id`, `amount`) VALUES ('$p_id','$grade','$res_id','$res_amt')");
					}
				}
			}
			echo "Save";
		}
		else
		{
			echo "Name Cannot Empty";
		}
	}
}

if($_POST["type"]=="delete_clinical_procedures")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `clinical_procedure` WHERE `procedure_id`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="load_res_list")
{
	$tt="";
	$all=$_POST['all'];
	$al=explode("@@",$all);
	foreach($al as $a)
	{
		if($a)
		{
			if($tt)
			{
				$tt.=",".$a;
			}
			else
			{
				$tt=$a;
			}
		}
	}
	if($all)
	{
		$qry="SELECT * FROM `ot_type_master` WHERE `type_id` NOT IN ($tt) ORDER BY `type`";
	}
	else
	{
		$qry="SELECT * FROM `ot_type_master` ORDER BY `type`";
	}
	//$qry="SELECT * FROM `ot_type_master` ORDER BY `type`";
	$q=mysqli_query($link,$qry);
	$num=mysqli_num_rows($q);
	if($num>0)
	{
	?>
	<select>
		<option value="0">Select</option>
		<?php
		while($r=mysqli_fetch_array($q))
		{
		?>
		<option value="<?php echo $r['type_id'];?>"><?php echo $r['type'];?></option>
		<?php
		}
		?>
	</select>
	<?php
	}
}

if($_POST["type"]=="load_resources")
{
	$id=$_POST['id'];
	$gr=mysqli_fetch_array(mysqli_query($link,"SELECT `grade_id` FROM `clinical_procedure` WHERE `procedure_id`='$id'"));
	$grade=$gr['grade_id'];
	$q=mysqli_query($link,"SELECT * FROM `ot_clinical_resourse` WHERE `procedure_id`='$id' AND `grade_id`='$grade'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		while($r=mysqli_fetch_array($q))
		{
			$rs=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$r[resourse_id]'"));
			$sel="<select><option value='".$r['resourse_id']."'>".$rs['type']."</option></select>";
			$a=explode(".",$r['amount']);
			$amt=$a[0];
			echo '<tr class="res_row"><td>'.$sel.'</td><td><input type="text" onkeyup="set_amt(this)" value="'.$amt.'" placeholder="Amount" /><span style="float:right;"><i class="icon-remove icon-large i_rem" style="color:#aa0000;cursor:pointer;" onclick="$(this).parent().parent().parent().remove()"></i></span></td></tr>#@#';
		}
	}
}

if($_POST["type"]=="oo")
{
	$id=$_POST['id'];
}
?>
