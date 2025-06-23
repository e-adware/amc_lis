<?php
include("../../includes/connection.php");

//$file_name=$_FILES['file']['name'];
$emp=$_POST['emp'];
$u=explode("@",$emp);
$pan=$_FILES['pan_doc']['name'];
$vot=$_FILES['voter_doc']['name'];
$resi=$_FILES['resi_doc']['name'];
$file_type=$_FILES['file']['type'];
$file_size=(($_FILES['file']['size'])/1000)." KB";
//$target = "../../emp_documents/".$file_name;
if($pan)
{
	$file_ext=strtolower(end(explode('.',$_FILES['pan_doc']['name'])));
	$doc="EMP".$u[0]."_pan".".".$file_ext;
	if(move_uploaded_file($_FILES["pan_doc"]["tmp_name"],"../../emp_documents/".$doc))
	{
		$n=101;
		$q=mysqli_query($link,"SELECT `file_name` FROM `employee_doc` WHERE `id`=(SELECT MAX(`id`) FROM `employee_doc` WHERE `emp_id`='$u[0]')");
		$num=mysqli_num_rows($q);
		if($num>0)
		{
			$f=mysqli_fetch_array($q);
			$fe=explode("-",$f['file_name']);
			$fi=(int)$fe[2]+1;
			$fl="EMP-".$u[0]."-".$fi;
		}
		else
		{
			$fl="EMP-".$u[0]."-".$n;
		}
		$linko->query("DELETE FROM `employee_doc` WHERE `emp_id`='$u[0]' AND `type`='pan'");
		mysqli_query($link,"INSERT INTO `employee_doc`(`emp_id`, `file_name`, `file_name_emp`, `type`, `user`) VALUES ('$u[0]','$fl','$doc','pan','$u[1]')");
		echo "1";
	}
}
if($vot)
{
	$file_ext=strtolower(end(explode('.',$_FILES['voter_doc']['name'])));
	$doc="EMP".$u[0]."_voter".".".$file_ext;
	if(move_uploaded_file($_FILES["voter_doc"]["tmp_name"],"../../emp_documents/".$doc))
	{
		$n=101;
		$q=mysqli_query($link,"SELECT `file_name` FROM `employee_doc` WHERE `id`=(SELECT MAX(`id`) FROM `employee_doc` WHERE `emp_id`='$u[0]')");
		$num=mysqli_num_rows($q);
		if($num>0)
		{
			$f=mysqli_fetch_array($q);
			$fe=explode("-",$f['file_name']);
			$fi=(int)$fe[2]+1;
			$fl="EMP-".$u[0]."-".$fi;
		}
		else
		{
			$fl="EMP-".$u[0]."-".$n;
		}
		$linko->query("DELETE FROM `employee_doc` WHERE `emp_id`='$u[0]' AND `type`='voter'");
		mysqli_query($link,"INSERT INTO `employee_doc`(`emp_id`, `file_name`, `file_name_emp`, `type`, `user`) VALUES ('$u[0]','$fl','$doc','voter','$u[1]')");
		echo "1";
	}
}
if($resi)
{
	$file_ext=strtolower(end(explode('.',$_FILES['resi_doc']['name'])));
	$doc="EMP".$u[0]."_residense".".".$file_ext;
	if(move_uploaded_file($_FILES["resi_doc"]["tmp_name"],"../../emp_documents/".$doc))
	{
		$n=101;
		$q=mysqli_query($link,"SELECT `file_name` FROM `employee_doc` WHERE `id`=(SELECT MAX(`id`) FROM `employee_doc` WHERE `emp_id`='$u[0]')");
		$num=mysqli_num_rows($q);
		if($num>0)
		{
			$f=mysqli_fetch_array($q);
			$fe=explode("-",$f['file_name']);
			$fi=(int)$fe[2]+1;
			$fl="EMP-".$u[0]."-".$fi;
		}
		else
		{
			$fl="EMP-".$u[0]."-".$n;
		}
		$linko->query("DELETE FROM `employee_doc` WHERE `emp_id`='$u[0]' AND `type`='resi'");
		mysqli_query($link,"INSERT INTO `employee_doc`(`emp_id`, `file_name`, `file_name_emp`, `type`, `user`) VALUES ('$u[0]','$fl','$doc','resi','$u[1]')");
		echo "1";
	}
}
//if(move_uploaded_file($_FILES["file"]["tmp_name"],"../../emp_documents/".$file_name))
//echo "Saved";
?>
