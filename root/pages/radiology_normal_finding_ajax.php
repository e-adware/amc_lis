<?php
	include("../../includes/connection.php");
	$type=$_POST[type];
	
	
	if($type=="save")
	{
		$name=mysqli_real_escape_string($link,$_POST['find_name']);	
		$normal=mysqli_real_escape_string($link,$_POST['normal']);
		
		mysqli_query($link,"insert into radiology_normal_finding(name,format) values('$name','$normal')");	
	}
	
	elseif($type=="load")
	{
		$id=$_POST[id];
		
		$normal=mysqli_fetch_array(mysqli_query($link,"select * from  radiology_normal_finding where id='$id'"));
		
		echo $normal[format];
	}
	
	elseif($type=="update")
	{
		$id=$_POST[f_id];
		$normal=mysqli_real_escape_string($link,$_POST['normal']);
		
		echo "update radiology_normal_finding set format='$normal' where id='$id'";
		
		mysqli_query($link,"update radiology_normal_finding set format='$normal' where id='$id'");
	}
	
	elseif($type=="delete")
	{
		$id=$_POST[f_id];
		mysqli_query($link,"delete from radiology_normal_finding where id='$id'");
	}
	
?>
