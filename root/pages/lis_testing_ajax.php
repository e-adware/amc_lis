<?php
include("../../includes/connection.php");


$type=$_POST[type];

if($type==1)
{
	$pin=$_POST[pin];
	$test=$_POST[test];


	$lis=mysqli_query($link,"select * from lis_testing where testid='$test'");
	while($ls=mysqli_fetch_array($lis))
	{
		$chk_pin=mysqli_fetch_array(mysqli_query($link,"select type from patient_type_master where p_type_id in(select type from uhid_and_opdid where opd_id='$pin')"));
		if($chk_pin[type]==2)
		{
			mysqli_query($link,"update test_sample_result set result='$ls[default_value]' where opd_id='$pin' and testid='$test' and paramid='$ls[paramid]'");		
		}
		else if($chk_pin[type]==3)
		{
			mysqli_query($link,"update test_sample_result set result='$ls[default_value]' where ipd_id='$pin' and testid='$test' and paramid='$ls[paramid]'");		
		}
	}
	
}
?>
