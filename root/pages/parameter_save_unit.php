<?php
include("../../includes/connection.php");
$unit=$_POST[unit];

$id=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select max(ID) as m from Units"));

if($id[m])
{
	$nid=$id[m]+1;
}
else
{
	$nid=1;
}

mysqli_query($GLOBALS["___mysqli_ston"], "insert into Units values('$nid','$unit')");

echo "insert into Units values('$nid','$unit')";
?>
