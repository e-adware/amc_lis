<table class="table table-bordered table-condensed">
<th>#</th><th>Name</th>
<?php
	include("../../includes/connection.php");
	$val=$_POST[val];
	
	if($val)
	{
		$qry=mysqli_query($GLOBALS["___mysqli_ston"], "select * from testmaster where category_id!='2' and testname like '$val%' order by testname");	
	}
	else
	{
		$qry=mysqli_query($GLOBALS["___mysqli_ston"], "select * from testmaster where category_id!='2' order by testname");	
	}
	
$i=1;
while($q=mysqli_fetch_array($qry))
{
?>
<tr onclick="load_test_info(<?php echo $q[testid];?>)">
	<td><?php echo $i;?></td>
	<td><?php echo $q[testname];?></td>
</tr>
<?php
$i++;	
}
?>
</table>

