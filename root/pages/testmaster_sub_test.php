<?php
include("../../includes/connection.php");

$type=$_POST[type];

if($type=="load")
{
	$tid=$_POST[tid];

	$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$tid'"));

	?>
	<div class="row">
		<div class="span10 text-center">
			<table class="table table-bordered table-condensed">
				<tr>
					<th colspan="2"><?php echo $tname[testname];?></th>
				</tr>
				<tr>
					<th>Search Test</th>
					<th>
						<select id="testadd" style="">
							<option value="0">     --Select--   </option>
							<?php
							$test=mysqli_query($link,"select * from testmaster order by testname");
							while($tst=mysqli_fetch_array($test))
							{
								echo "<option value='$tst[testid]@#$tst[testname]'>$tst[testname]</option>";
							}
							?>		
						</select>
						<input type="button" class="btn btn-default" value="Add" onclick="add_sub_test()"/>
					</th>
				</tr>
				<tr>
					<table class="table table-bordered" id="s_list">
					<?php
					$i=0;
					$chk_bt="Save";
					$chk_sty="display:none;text-align:center";
					
					$sub_ls=mysqli_query($link,"select * from testmaster_sub where testid='$tid'");
					while($s_l=mysqli_fetch_array($sub_ls))
					{
						$test_nm=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$s_l[sub_testid]'"));
						echo "<tr><th>$test_nm[testname] <input type='hidden' class='tst_list' value='$s_l[sub_testid]'/></th><th onclick='$(this).parent().remove()'>Remove</th></tr>";
						$i++;
					}
					
					if($i>0)
					{
						$chk_bt="Update";
						$chk_sty="display:block;text-align:center";
					}
					
					?>	
					</table>
				</tr>
				
			</table>
			<div style="<?php echo $chk_sty;?>" id="sv_bt">
				<input type="button" class="btn btn-info" value="<?php echo $chk_bt;?>" onclick="save_sub_test(<?php echo $tid;?>)"/>
			</div>
		</div>
	</div>
<?php
}
else if($type=="save")
{
	$tid=$_POST[tid];
	$t_list=$_POST[t_list];
	
	mysqli_query($link,"delete from testmaster_sub where testid='$tid'");
	$t_list=explode("@#",$t_list);
	foreach($t_list as $ts)
	{
		if($ts)
		{
			mysqli_query($link,"insert into testmaster_sub(testid,sub_testid) values('$tid','$ts')");
		}
	}
	
}
else if($type=="sample_par")
{
	$test=$_POST['tid'];
	$parm=$_POST['pid'];
	$sample=$_POST['samp'];
	
	$chk_par=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from Testparameter where TestId='$test' and ParamaterId='$parm'"));
	if($chk_par[tot]>0)
	{
		mysqli_query($link,"update Testparameter set sample='$sample' where TestId='$test' and ParamaterId='$parm'");
		mysqli_query($link,"UPDATE `Parameter_old` SET `sample`='$sample' WHERE `ID`='$parm'");
	}
}
else if($type=="vaccu_par")
{
	$test=$_POST['tid'];
	$parm=$_POST['pid'];
	$vaccu=$_POST['vaccu'];
	
	$chk_par=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from Testparameter where TestId='$test' and ParamaterId='$parm'"));
	if($chk_par[tot]>0)
	{
		mysqli_query($link,"update Testparameter set vaccu='$vaccu' where TestId='$test' and ParamaterId='$parm'");
		mysqli_query($link,"UPDATE `Parameter_old` SET `vaccu`='$vaccu' WHERE `ID`='$parm'");
	}
}
else if($type=="sample_vaccu")
{
	$pid=$_POST['id'];
	$sam_vac=mysqli_fetch_array(mysqli_query($link,"select sample,vaccu from Parameter_old where ID='$pid'"));
	?>
	<select id="samp_<?php echo$pid;?>" class="samp">
		<option value="0">--Select Sample--</option>
	<?php
		$sam=mysqli_query($GLOBALS["___mysqli_ston"], "select * from  Sample order by Name");
		while($s=mysqli_fetch_array($sam))
		{
			if($sam_vac[sample]==$s[ID]){ echo $sel="Selected='selected'";} else{ $sel="";}
			echo "<option value='$s[ID]' $sel>$s[Name]</option>";							
		}
	?>
	</select>
	<br/>
	<select id="vac_<?php echo$pid;?>" class="vacc">
		<option value="0">--Select Vaccu--</option>
		<?php
		$vac=mysqli_query($GLOBALS["___mysqli_ston"], "select * from vaccu_master order by type");
		while($v=mysqli_fetch_array($vac))
		{
			if($sam_vac[vaccu]==$v[id]){ echo $sel2="Selected='selected'";} else{ $sel2="";}
			echo "<option value='$v[id]' $sel2>$v[type]</option>";
		}
		?>
	</select>
	<?php
}
else if($type=="dlc_check")
{
	$tst=$_POST['tst'];
	$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$tst'"));
	?>
	<table class="table table-condensed">
	<th colspan="2"><?php echo $tname[testname];?></th>
	<?php
	$testp=mysqli_query($link,"select * from Testparameter where TestId='$tst' order by sequence");
	while($test=mysqli_fetch_array($testp))
	{
		$name=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID='$test[ParamaterId]'"));
		$chk_par=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testmaster_dlc_check where testid='$tst' and paramid='$test[ParamaterId]'"));
		$cls="icon-check-empty";
		if($chk_par[tot]>0)
		{
			$cls="icon-check";
		}
		?>
		<tr style="cursor:pointer" onclick="add_dlc(<?php echo $tst;?>,<?php echo $test[ParamaterId];?>,'<?php echo $cls;?>')">
			<td>
				<i name="" class="<?php echo $cls;?>" id="dlc_<?php echo $test[ParamaterId];?>" value="<?php echo $chk_par[tot];?>"></i>
			</td>
			<td>
				<?php echo $name[Name];?>
			</td>
		</tr>
		<?php
	}
	?>
	<tr><td colspan='2' style="text-align:center" onclick="$('#mod2').click()" id="dlc_close"><button class="btn btn-danger btn-sm">Close</button></td></tr>
	</table>
	<?php
}
else if($type=="dlc_save")
{
	$tst=$_POST['tst'];
	$param=$_POST['param'];
	$typ=$_POST['typ'];
	
	if($typ==0) //----Save----//
	{
		mysqli_query($link,"insert into testmaster_dlc_check(testid,paramid) values('$tst','$param')");
		echo "1";
	}
	else
	{
		mysqli_query($link,"delete from testmaster_dlc_check where testid='$tst' and paramid='$param'");
		echo "0";
	}
}
else if($type=="param_mand")
{
	$tst=$_POST['tst'];
	$param=$_POST['param'];
	$chk=$_POST['chk'];
	
	if($chk==1)
	{
		mysqli_query($link,"insert into test_param_mandatory(testid,paramid) values('$tst','$param')");
	}
	else
	{
		mysqli_query($link,"delete from test_param_mandatory where testid='$tst' and paramid='$param'");
	}
}
?>
