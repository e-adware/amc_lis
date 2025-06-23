
<table class='table table-bordered' id="extra_list">

<tr><th colspan='3' style='background-color:#cccccc'>Extra</th><th style='text-align:right;background-color:#cccccc'>Total:<span id='extra_total'></span></th></tr>

<?php
include("../../includes/connection.php");
$test_all=$_POST[test_all];

$ts=explode("@",$test_all);

foreach($ts as $t)
{
	if($t)
	{
		$vc_n=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_vaccu where testid='$t'");
		while($vcn=mysqli_fetch_array($vc_n))
		{
			$vc_id.="@".$vcn[vac_id];	
		}	
	}	
}

$vc=explode("@",$vc_id);
$vc1=array_unique($vc);
$i=1;
foreach($vc1 as $v)
{
	if($v)
	{
		$vn=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from vaccu_master where id='$v'"));
		
		echo "<tr><td>$i</td><td>$vn[type] <input type='hidden' value='$vn[id]' class='extra_id'/></td><td class='extra_price'>$vn[rate]</td><td onclick='delete_rows(this,1)'><span class='text-danger'><i class='fa fa-times-circle fa-lg'></i></span></td></tr>";
		$i++;
	}
}

?>

</table>
