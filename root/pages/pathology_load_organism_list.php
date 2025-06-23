<?php

include("../../includes/connection.php");

$org=$_POST['org'];
$growth_val=$_POST['growth_val'];
$val=$_POST['val'];

echo "<datalist id='list$val'>";

//~ if($growth_val==1){ $sel=mysqli_query($link, "select * from ResultOptions where id='89' and optionid='1186'");}
//~ else { $sel=mysqli_query($link, "select * from ResultOptions where id='89' and optionid='1187'"); }

$sel=mysqli_query($link, "select * from ResultOptions where id='89'");

while($s=mysqli_fetch_array($sel))
{
	$op=mysqli_fetch_array(mysqli_query($link, "select name from Options where id='$s[optionid]'"));
	echo "<option value='".str_replace("*val*",$org,$op[name])."'>".str_replace("*val*",$org,$op[name])."</option>";
}
echo "</datalist>";
?>
