<?php
include("../../includes/connection.php");
$id=$_POST[id];
$range=$_POST[range];



mysqli_query($GLOBALS["___mysqli_ston"], "delete from  ParaDetailNew where ParameterID='$id'");
$range=explode("#",$range);
foreach($range as $ran)
{
	if($ran)
	{
		$r=explode("@",$ran);
		mysqli_query($GLOBALS["___mysqli_ston"], "insert into ParaDetailNew values('$id','$r[1]','$r[2]','$r[3]','$r[4]','$r[5]','$r[6]','','','','')");
	}
}
?>
