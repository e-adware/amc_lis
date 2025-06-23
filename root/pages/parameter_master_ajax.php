<?php
include("../../includes/connection.php");
$val=$_POST[val];
$test=$_POST['test'];
if($val)
{
	if($test)
	{
		$q="select a.* from  Parameter_old a,Testparameter b where b.TestId='$test' and a.ID=b.ParamaterId and a.Name like '%$val%' order by a.Name";
	}
	else
	{
		$q="select * from  Parameter_old where Name like '%$val%' order by Name";
	}
}
else
{
	if($test)
	{
		$q="select a.* from  Parameter_old a,Testparameter b where b.TestId='$test' and a.ID=b.ParamaterId order by a.Name";
	}
	else
	{
		$q="select * from  Parameter_old order by Name";
	}
}

?>

<table class="table table-bordered table-condensed table-report">

  <th>ID</th>
  <th>Name</th>
  <th>Sample</th>
  <th>Vaccu</th>
  <th>Tests</th>
  <th></th>
  <?php
$i=1;
$par=mysqli_query($GLOBALS["___mysqli_ston"], $q);
while($p=mysqli_fetch_array($par))
{
	
	$test_l="";
	$p_tst=mysqli_query($link,"select distinct(TestId) from Testparameter where ParamaterId='$p[ID]'");
	while($p_t=mysqli_fetch_array($p_tst))
	{
		$name=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$p_t[TestId]'"));
		$test_l=$test_l." , ".$name[testname];
	}
	
	echo "<tr id='param$i'><td id='par_id$i'>$p[ID]</td><td>$p[Name]</td>";
	
	?>
  <td id="samp_tr<?php echo $i;?>">
    <?php
			$sam=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from  Sample where ID='$p[sample]'"));
			echo $sam[Name];
		?>
  </td>
  <td id="vac_tr<?php echo $i;?>">
    <?php
			$vac=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from vaccu_master where id='$p[vaccu]'"));
			echo $vac[type];
		?>

  </td>
  <?php
	echo "<td width='500px'><i><b>$test_l</b></i></td><td><div class='btn-group'><input type='button' class='btn btn-info btn-mini' value='Update' onclick='load_param_info($p[ID])' /><input type='button' class='btn btn-danger btn-mini' value='Delete' onclick='delete_para($p[ID])' /></td></tr>";
	$i++;
}
?>
</table>