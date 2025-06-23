<?php
include("../../includes/connection.php");
$id=$_POST[id];
$opts=mysqli_query($GLOBALS["___mysqli_ston"], "select * from ResultOptions where id='$id'");
while($opt=mysqli_fetch_array($opts))
{
	$nm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select name from Options where id='$opt[optionid]'"));
	//echo "<div id='$opt[optionid]' class='options' onclick='$(this).remove()'>$nm[name]</div>";
	echo "<div id='$opt[optionid]' class='options'>$nm[name]</div>";
}
?>
