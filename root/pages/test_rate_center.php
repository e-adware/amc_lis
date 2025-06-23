<?php
include("../../includes/connection.php");
$test=$_POST['test'];
//$coll=$_POST['coll'];
//$coll=explode("-",$coll);
$rate=$_POST['prc'];

$q=mysqli_query($GLOBALS["___mysqli_ston"], "select rate from testmaster_rate where centreno='$coll[0]' and testid='$test'");
$num=mysqli_num_rows($q);
if($num>0)
{
        $tr=mysqli_fetch_array($q);
        echo $tr[rate];
}
else
{
        echo $rate;
}

?>
