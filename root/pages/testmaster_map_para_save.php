<?php
include("../../includes/connection.php");

$testid=$_POST["id"];
$pids=$_POST["pids"];
$sq=$_POST["sq"];

mysqli_query($link, "delete from  Testparameter where TestId='$testid'");

$p=explode("#",$pids);
$s=explode("#",$sq);

$tot=sizeof($p);

for($i=0;$i<$tot;$i++)
{
	if($p[$i])
	{
		$det=explode("%",$p[$i]);
		$param_id = $det[0];
		$samp = $det[1];
		$vaccu = $det[2];
		$dont_print = $det[3];
		
		$sequnce=$s[$i];
		
		if(!$samp || $samp==0)
		{
			$test_sample=mysqli_fetch_array(mysqli_query($link, "SELECT `SampleId` FROM `TestSample` WHERE `TestId`='$testid';"));
			if($test_sample)
			{
				$samp=$test_sample["SampleId"];
			}
		}
		
		if(!$vaccu || $vaccu==0)
		{
			$test_vaccu=mysqli_fetch_array(mysqli_query($link, "SELECT `vac_id` FROM `test_vaccu` WHERE `testid`='$testid';"));
			if($test_vaccu)
			{
				$vaccu=$test_vaccu["vac_id"];
			}
		}
		
		if(!$sequnce){ $sequnce=0; }
		if(!$samp){ $samp=0; }
		if(!$vaccu){ $vaccu=0; }
		
		mysqli_query($link, "INSERT INTO `Testparameter`(`TestId`, `ParamaterId`, `sequence`, `sample`, `vaccu`, `status`) VALUES ('$testid','$param_id','$sequnce','$samp','$vaccu','$dont_print')");
	}
}

//----------Mand------------//
mysqli_query($link,"delete from test_param_mandatory where testid='$testid' and paramid not in(select ParamaterId from Testparameter where TestId='$testid')");
//--------------------------//

?>
