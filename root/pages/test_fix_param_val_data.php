<?php
include("../../includes/connection.php");

if($_POST["type"]=="load_all_param")
{
	$testid=$_POST['testid'];
	
	echo "<option value='0'>Select Parameter</option>";
	
	$tst_qry=mysqli_query($link, " SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' ORDER BY `sequence` ");
	while($tst=mysqli_fetch_array($tst_qry))
	{
		$param_name=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `Parameter_old` WHERE `ID`='$tst[ParamaterId]' "));
		echo "<option value='$tst[ParamaterId]'>$param_name[Name]</option>";
	}
}			


if($_POST["type"]=="load_fix_param_val")
{
	$testid=$_POST['testid'];
	$param=$_POST['param'];
	
	if($param)
	{
	
		$fix_param=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `param_fix_result` WHERE `testid`='$testid' AND `paramid`='$param' "));
		
		if($fix_param["range_check"]==1)
		{
			$range_check_ch="checked";
		}else
		{
			$range_check_ch="";
		}
		
		if($fix_param["must_save"]==1)
		{
			$must_save_ch="checked";
		}else
		{
			$must_save_ch="";
		}
	
?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Default value</th>
		</tr>
		<tr>
			<td>
				<!--<input type="text" id="fix_param_val" value="<?php echo $fix_param['result']; ?>" onkeyup="save_param_fix_val('<?php echo $testid; ?>','<?php echo $param; ?>',event,this.value)">-->
				<textarea id="fix_param_val" class="span5"><?php echo trim($fix_param['result']); ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" id="range_check" <?php echo $range_check_ch; ?> onClick="fix_para_check('<?php echo $testid; ?>','<?php echo $param; ?>')" > Check result
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" id="must_save" <?php echo $must_save_ch; ?> onClick="fix_para_must_save('<?php echo $testid; ?>','<?php echo $param; ?>')" > Must Save (Before Approval)
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="save" value="Save" class="btn btn-success" onClick="save_param_fix_val('<?php echo $testid; ?>','<?php echo $param; ?>')" >
			</td>
		</tr>
	</table>
<?php
	}
}


if($_POST["type"]=="save_param_fix_val")
{
	$testid=$_POST['testid'];
	$param=$_POST['param'];
	$val=$_POST['fix_param_val'];
	$val=str_replace("'","''", $val);
	$range_check=$_POST['range_check'];
	$must_save=$_POST['must_save'];
	
	mysqli_query($link, " DELETE FROM `param_fix_result` WHERE `testid`='$testid' AND `paramid`='$param' ");
	echo " INSERT INTO `param_fix_result`(`testid`, `paramid`, `result`, `range_check`, `must_save`) VALUES ('$testid','$param','$val','$range_check','$must_save') ";
	if($val)
	{
		mysqli_query($link, " INSERT INTO `param_fix_result`(`testid`, `paramid`, `result`, `range_check`, `must_save`) VALUES ('$testid','$param','$val','$range_check','$must_save') ");
	}
	
}

?>
