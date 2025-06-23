<?php
include("../../includes/connection.php");

$tst=$_POST['tst'];

$test=explode("@",$tst);

foreach($test as $t)
{
	if($t)
	{
		$vc_n=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_vaccu where testid='$t'");
		while($vcn=mysqli_fetch_array($vc_n))
		{
			$vc_id.="@".$vcn['vac_id'];	
		}
	}
}


$vc=explode("@",$vc_id);
$vc1=array_unique($vc);

$vacc="";
foreach($vc1 as $v)
{
	if($v)
	{
		$vacc.="@".$v;
	}
}

echo $vacc;

?>
