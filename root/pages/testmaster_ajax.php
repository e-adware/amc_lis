<?php
include("../../includes/connection.php");
$val=$_POST[val];
$vac=$_POST[vac];
$dep=$_POST[dep];
$sam=$_POST[sam];



if($val)
{
	$q="select * from testmaster where testname like '%$val%'";
	if($vac)
	{
		$q.=" and testid in(select testid from test_vaccu where vac_id='$vac')";
	}	
	if($dep)
	{
		$q.=" and type_id='$dep'";	
	}
	if($sam)
	{
		$q.=" and testid in(select TestId from TestSample where SampleId='$sam')";
	}
	$q.=" order by testid";
}
else
{
	$q="select * from testmaster where testid>0";
	if($vac)
	{
		$q.=" and testid in(select testid from test_vaccu where vac_id='$vac')";
	}	
	if($dep)
	{
		$q.=" and type_id='$dep'";	
	}
	if($sam)
	{
		$q.=" and testid in(select TestId from TestSample where SampleId='$sam')";
	}
	$q.="  order by testid";
	
	
}



/*
if($val)
{
	$q="select * from testmaster where testname like '%$val%' order by type_id,testname";
}
else
{
	$q="select * from testmaster order by type_id,testname";
}
*/
?>

<table class="table table-bordered table-condensed">
<th>ID</th><th>Name</th><th>Department</th><th>Price</th><th></th><th></th><th></th>
<?php
$i=1;
$tst=mysqli_query($GLOBALS["___mysqli_ston"], $q);
while($t=mysqli_fetch_array($tst))
{
	//$p_name = str_replace( $val , "<b>".$val."</b>" , $p[Name] );
	$cls="";
	$txt_p="Map Parameter";
	$par=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from Testparameter where TestId='$t[testid]'"));
	if($par>0)
	{
		$txt_p="Edit Parameter";
		$cls="btn btn-info";
	}
	else
	{
		$cls="btn btn-default";
	}
	
	$price=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from Category_Test where cat_id='1' and id='$t[testid]'"));
	if($price[price])
	{
		$prc=$price[price];
	}
	else
	{
		$prc=$t[rate];	
	}
	
	echo "<tr id='test$i' class='$t[testname]'><td id='test_id$i'>$t[testid]</td><td id='$t[testid]_name'>$t[testname]</td><td>$t[type_name]</td><td id='$t[testid]_prc'>$t[rate]</td><td class='$cls'><span onclick='map_para($t[testid])' class='$cls'>$txt_p</span></td><td><input type='button' id='upd' class='btn btn-info' value='Update' onclick='load_test_info($t[testid])'/></td><td><input type='button' id='dlt' class='btn btn-info' value='Delete' onclick='delete_test($t[testid])'/></td></tr>";
	$i++;
}
?>
</table>

