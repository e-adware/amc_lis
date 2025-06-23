<table class="table table-bordered table-condensed" id="tblData">
	<th>ID</th><th>Parameter Name</th><th>User Interface</th>
<?php
include("../../includes/connection.php");
$id=$_POST['id'];

if($id==0)
{
	$par=mysqli_query($GLOBALS["___mysqli_ston"], "select * from Parameter_old order by Name");
	while($p=mysqli_fetch_array($par))
	{
		$r_nm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ResultType_name from ResultType where ResultTypeId='$p[ResultType]'"));
		?>
		
		<tr onclick="add_para(<?php echo $p['ID'];?>,'<?php echo ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $p['Name']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));?>','<?php echo $r_nm['ResultType_name'];?>')">
		
		<?php
		echo "<td>$p[ID]</td><td width='60%'>$p[Name]</td><td>$r_nm[ResultType_name]</td></tr>";
	}
}else
{
	$xx=1;
	$param_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$id' order by `sequence` ");
	while($param=mysqli_fetch_array($param_qry))
	{
		$p=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from Parameter_old where ID='$param[ParamaterId]' order by Name"));
		$r_nm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ResultType_name from ResultType where ResultTypeId='$p[ResultType]'"));
		//$all_para.=$p['ID']."@@".$p['Name']."@@".$r_nm['ResultType_name']."###";
	?>
		<tr onclick="add_para(<?php echo $p['ID'];?>,'<?php echo ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $p['Name']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));?>','<?php echo $r_nm['ResultType_name'];?>')" class="sel_param">
	<?php
		echo "<td id='pid$xx'>$p[ID]</td><td id='pname$xx' width='60%'>$p[Name]</td><td id='prtname$xx'>$r_nm[ResultType_name]</td></tr>";
		$xx++;
	}
	//echo "<tr><td colspan='3'><button class='btn btn-success' onCLick='add_all_param()'>Add all</button></td></tr>";
}
?>
	<tr><td colspan="3"><button class="btn btn-success" onCLick="add_all_param()">Add all</button></td></tr>
</table>
