<?php
include("../../includes/connection.php");


if($_POST["type"]=="lab_by_phone")
{
	$phone=$_POST["val"];
	if($phone)
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `phone`='$phone' "));
		$ref_doc_name=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
		
		if($pat_info)
		{
			echo $pat_info["name"]."@#@".$pat_info["dob"]."@#@".$pat_info["age"]."@#@".$pat_info["age_type"]."@#@".$pat_info["gd_name"]."@#@".$pat_info["sex"]."@#@".$pat_info["refbydoctorid"]."@#@".$ref_doc_name["ref_name"]."@#@".$pat_info["center_no"]."@#@".$pat_info["patient_id"];
		}
	}
}


?>
