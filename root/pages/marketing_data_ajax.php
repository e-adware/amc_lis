<?php
include("../../includes/connection.php");

if($_POST[typ]=="save")
{
	
	
}

else if($_POST[typ]=="load_doctor")
{
	$mrkid=$_POST['mrkid'];
	$val="";
	$q=mysqli_query($link,"SELECT a.`refbydoctorid`,b.`ref_name` FROM `marketing_master` a,refbydoctor_master b WHERE a.`emp_id`='$mrkid' and a.`refbydoctorid`=b.`refbydoctorid` order by b.`ref_name`");
	while($r=mysqli_fetch_assoc($q))
	{
		if($val)
		{
			$val.="#%#".$r['refbydoctorid']."@@".$r['ref_name'];
		}
		else
		{
			$val=$r['refbydoctorid']."@@".$r['ref_name'];
		}
	}
	echo $val;
}
?>
