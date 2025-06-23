<?php
session_start();

include("../../includes/connection.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_info["branch_id"];

if($_POST["type"]=="load_culture_data")
{
	$growth_val=mysqli_real_escape_string($link, $_POST["growth_val"]);
	$iso_no_total=mysqli_real_escape_string($link, $_POST["iso_no_total"]);
	
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$ipd_id=mysqli_real_escape_string($link, $_POST["ipd_id"]);
	$batch_no=mysqli_real_escape_string($link, $_POST["batch_no"]);
	$testid=mysqli_real_escape_string($link, $_POST["testid"]);
	
	$tch=0;
	$dch=1000;
	$tc=mysqli_fetch_array(mysqli_query($link, "select * from testresults where `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and `doc`>0 limit 1"));
	if($tc['doc']>0)
	{
		$tch=$tc['main_tech'];
		$dch=$tc['for_doc'];
		
		$dis="disabled";
		if($level=="1" || $level=="13") // Admin or Pathology Doctor
		{
			$dis="";
		}
	}
	else
	{
		if($tch==0)
		{
			$dis="";
			$tc_s=mysqli_fetch_array(mysqli_query($link, "select * from patient_test_summary where `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and `doc`>0 limit 1"));
			
			if($tc_s['doc']>0)
			{
				$tch=$tc_s['main_tech'];
				$dch=$tc['for_doc'];
				
				$dis="disabled";
				if($level=="1" || $level=="13") // Admin or Pathology Doctor
				{
					$dis="";
				}
			}
		}
	}
	$cult_test=3374; //------------Loading parameter of Urine culture & Sensitivity ----------//
	
?>
	<table class="table table-condensed">
<?php
	
	if($growth_val==1) // No Growth
	{
		$i=1;
		$iso_no=0;
		$cult=mysqli_query($link,"SELECT a.*,b.Name,b.ID,b.ResultType,b.ResultOptionID FROM Testparameter a, Parameter_old b where a.TestId='$cult_test' and b.ID=a.ParamaterId and b.ResultOptionID!='68' and b.ID NOT IN(311,312) order by a.sequence");
		while($p=mysqli_fetch_array($cult))
		{
			if($i==1 || $i%2!=0)
			{
				echo "<tr>";
			}
			echo "<th>$p[Name]</th>";
			
			$cols=0;
			$wdt="90%";
			if($p["ID"]==649)
			{
				$cols=3;
				$wdt="96%;";
			}
			
			echo "<td colspan='$cols'>";
			
			if($p["ResultType"]==2)
			{
				$val=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and paramid='$p[ParamaterId]'"));
				if(!$val['result'])
				{
					$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and paramid='$p[ParamaterId]'"));
					if(!$val[result])
					{
						if($p[ParamaterId]==309)
						{
							$samp_name=mysqli_fetch_array(mysqli_query($link,"select Name from Sample where ID in(select SampleId from TestSample where TestId='$testid')"));
							$val[result]=$samp_name[Name];
						}
					}
				}
				
				$org_rem="";
				if($p["ParamaterId"]==649)
				{
					$org_rem="onfocus='load_org_list($i)'";
				}
				
				echo "<input type='text' name='t_par$i' id='$p[ParamaterId]' list='list$i' class='t_par' value='$val[result]' style='width:$wdt !important' $org_rem/>";
				echo "<div id='data_list_cult_$i'>";
				if($org_rem=="")
				{
					echo "<datalist id='list$i'>";
					$sel=mysqli_query($link, "select * from ResultOptions where id='$p[ResultOptionID]'");
					while($s=mysqli_fetch_array($sel))
					{
						$op=mysqli_fetch_array(mysqli_query($link, "select name from Options where id='$s[optionid]'"));
						echo "<option value='$op[name]'>$op[name]</option>";
					}
					echo "</datalist>";
				}
				echo "</div>";
			}
			else
			{
				$val=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and paramid='$p[ParamaterId]'"));
				if(!$val['result'])
				{
					$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and paramid='$p[ParamaterId]'"));
				}
				echo "<input type='text' name='t_par$i' id='$p[ParamaterId]' class='t_par' value='$val[result]' style='width:$wdt !important'/>";
			}
			
			echo "</td>";
			
			if($i%2==0)
			{
				echo "</tr>";
			}
			
			$i++;
		}
?>
		<input type="hidden" id="iso_no" value="<?php echo $iso_no; ?>">
		<tr>
			<td colspan="4" style="text-align:center;">
				<button class="btn btn-save" id="save" name="t_par<?php echo $i;?>" onclick="save_culture('<?php echo $testid;?>','<?php echo $iso_no;?>','1')" <?php echo $dis;?>><i class="icon-save"></i> Save &amp; Validate</button>
				
				<button class="btn btn-new" id="summary" name="summary" onclick="add_summary('<?php echo $testid;?>','<?php echo $iso_no;?>')"><i class="icon-edit"></i> <?php echo $sum;?> Summary</button>
				
				<button class="btn btn-back" id="cls" onclick="$('#btn_<?php echo $testid ?>').click();$('#test_id').focus()"><i class="icon-backward"></i> Back</button>
			</td>
		</tr>
<?php
	}
	else
	{
?>
		<tr>
			<td colspan="4">
				<div class="widget-box">
					<div class="widget-title">
						<ul class="nav nav-tabs">
					<?php
						for($iso_no=1;$iso_no<=$iso_no_total;$iso_no++)
						{
							$active_cls="";
							if($iso_no==1)
							{
								$active_cls="active";
							}
					?>
							<li class="<?php echo $active_cls; ?>"><a data-toggle="tab" href="#tab_iso<?php echo $iso_no; ?>" id="cult_tab<?php echo $iso_no; ?>" onclick="cult_tab_click('<?php echo $iso_no; ?>')">ISO <?php echo $iso_no; ?></a></li>
					<?php
						}
					?>
						</ul>
					</div>
					<div class="widget-content tab-content" style="background-color: white;">
				<?php
					for($iso_no=1;$iso_no<=$iso_no_total;$iso_no++)
					{
						$i=1;
						$active_cls="";
						if($iso_no==1)
						{
							$active_cls="active";
						}
				?>
						<div id="tab_iso<?php echo $iso_no; ?>" class="tab_iso_cls tab-pane <?php echo $active_cls; ?>">
							
						</div>
				<?php
					}
				?>
					</div>
				</div>
			</td>
		</tr>
<?php
	}
?>
	</table>
	<style>
		.widget-content {
		  width: 98%;
		}
	</style>
<?php
}
if($_POST["type"]=="load_culture_iso_data")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$ipd_id=mysqli_real_escape_string($link, $_POST["ipd_id"]);
	$batch_no=mysqli_real_escape_string($link, $_POST["batch_no"]);
	$testid=mysqli_real_escape_string($link, $_POST["testid"]);
	
	$iso_no=mysqli_real_escape_string($link, $_POST["iso_no"]);
	
	$tch=0;
	$dch=1000;
	$tc=mysqli_fetch_array(mysqli_query($link, "select * from testresults where `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and `doc`>0 limit 1"));
	if($tc['doc']>0)
	{
		$tch=$tc['main_tech'];
		$dch=$tc['for_doc'];
		
		$dis="disabled";
		if($level=="1" || $level=="13") // Admin or Pathology Doctor
		{
			$dis="";
		}
	}
	else
	{
		if($tch==0)
		{
			$dis="";
			$tc_s=mysqli_fetch_array(mysqli_query($link, "select * from patient_test_summary where `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and `doc`>0 limit 1"));
			
			if($tc_s['doc']>0)
			{
				$tch=$tc_s['main_tech'];
				$dch=$tc['for_doc'];
				
				$dis="disabled";
				if($level=="1" || $level=="13") // Admin or Pathology Doctor
				{
					$dis="";
				}
			}
		}
	}
	$i=1;
	$cult_test=3374; //------------Loading parameter of Urine culture & Sensitivity ----------//
?>
	<div>
		<input type="hidden" id="iso_no_selected" value="<?php echo $iso_no; ?>">
	<?php
		echo "<table class='table table-condensed'>";
		$cult=mysqli_query($link,"SELECT a.*,b.Name,b.ID,b.ResultType,b.ResultOptionID FROM Testparameter a, Parameter_old b where a.TestId='$cult_test' and b.ID=a.ParamaterId and b.ResultOptionID!='68' order by a.sequence");
		while($p=mysqli_fetch_array($cult))
		{
			if($i==1 || $i%2!=0)
			{
				echo "<tr>";
			}
			echo "<th>$p[Name]</th>";
			
			$cols=0;
			$wdt="90%";
			if($p["ID"]==649)
			{
				$cols=3;
				$wdt="96%;";
			}
			
			echo "<td colspan='$cols'>";
			
			if($p["ResultType"]==2)
			{
				$val=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and paramid='$p[ParamaterId]'"));
				if(!$val['result'])
				{
					$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and iso_no='$iso_no' and paramid='$p[ParamaterId]'"));
					if(!$val["result"])
					{
						if($p["ParamaterId"]==309)
						{
							$samp_name=mysqli_fetch_array(mysqli_query($link,"select Name from Sample where ID in(select SampleId from TestSample where TestId='$testid')"));
							$val["result"]=$samp_name["Name"];
						}
					}
				}
				
				$org_rem="";
				if($p["ParamaterId"]==649)
				{
					$org_rem="onfocus='load_org_list($i)'";
				}
				
				echo "<input type='text' name='t_par$i' class='t_par' id='$p[ParamaterId]' list='list$i' value='$val[result]' style='width:$wdt !important' $org_rem/>";
				echo "<div id='data_list_cult_$i'>";
				if($org_rem=="")
				{
					echo "<datalist id='list$i'>";
					$sel=mysqli_query($link, "select * from ResultOptions where id='$p[ResultOptionID]'");
					while($s=mysqli_fetch_array($sel))
					{
						$op=mysqli_fetch_array(mysqli_query($link, "select name from Options where id='$s[optionid]'"));
						echo "<option value='$op[name]'>$op[name]</option>";
					}
					echo "</datalist>";
				}
				echo "</div>";
			}
			else
			{
				$val=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and paramid='$p[ParamaterId]'"));
				if(!$val['result'])
				{
					$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and paramid='$p[ParamaterId]'"));
				}
				echo "<input type='text' name='t_par$i' class='t_par' id='$p[ParamaterId]' value='$val[result]' style='width:$wdt !important'/>";
			}
			
			echo "</td>";
			
			if($i%2==0)
			{
				echo "</tr>";
			}
			
			$i++;
		}
		echo "</table>";
	?>
		<div style="text-align:center;font-weight:bold;background-color:#CCC;">
			ANTIBIOTICS OF ISO <?php echo $iso_no; ?>
			<br>
			
			<input type="text" id="searchh" onkeyup="search(this.value)" placeholder="Type to search ANTIBIOTICS">
		</div>
		<div style="max-height:300px;overflow:scroll;overflow-x:hidden">
			<table class="table table-bordered table-condensed" id="tblData">
			<?php
					$j=1;
					//-----------Loading Anti Biotics from Urine Culture & Sensitivity ----//
					$cult_ant=mysqli_query($link,"SELECT a.*,b.Name,b.ID,b.ResultType,b.ResultOptionID FROM Testparameter a, Parameter_old b where a.TestId='$cult_test' and b.ID=a.ParamaterId and b.ResultOptionID='68' order by b.Name");
					while($ant=mysqli_fetch_array($cult_ant))
					{
						if($j==1 || $j%2!=0)
						{
							echo "<tr>";
						}
						echo "<td>$ant[Name]</td>";
						
						echo "<td>";
						
						if($ant["ResultType"]==2)
						{
							$val=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and paramid='$ant[ParamaterId]'"));
							if(!$val['result'])
							{
								$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and paramid='$ant[ParamaterId]'"));
							}
							
							//$res=explode("#MIC#",$val['result']);
							
							$res=explode(" (",$val['result']);
							$resultz=explode(")",$res[1]);
							$res[1]=$resultz[0];
							
							echo "<input type='text' name='t_par$i' class='t_par' id='$ant[ParamaterId]' list='a_list$i' value='$res[0]' style='width:80px !important' placeholder='Result' />";
							$i++;
							echo "<input type='text' name='t_par$i' class='t_par' id='$ant[ParamaterId]_mic' value='$res[1]' style='width:80px !important;margin-left: 5px;' placeholder='MIC Value' />";
							echo "<datalist id='a_list$i'>";
							$sel=mysqli_query($link, "select * from ResultOptions where id='$ant[ResultOptionID]'");
							while($s=mysqli_fetch_array($sel))
							{
								$op=mysqli_fetch_array(mysqli_query($link, "select name from Options where id='$s[optionid]'"));
								echo "<option value='$op[name]'>$op[name]</option>";
							}
							echo "</datalist>";
							//$i++;
							//echo "<input type='text' name='t_par$i' id='$ant[ParamaterId]_mic' class='t_par$iso_no' value='$res[1]' placeholder='Add MIC' style='width:100px !important'/>";
						}
						
						echo "</td>";
						if($j%2==0)
						{
							echo "</tr>";
						}
						
						$i++;
						$j++;
						
					}
				?>
				<tr>
					<td colspan="4" style="text-align:center;">
						
					</td>
				</tr>
			</table>
		</div>
		<div style="text-align:center;background-color:#CCC;">
			<button class="btn btn-save" id="save" name="t_par<?php echo $i;?>" onclick="save_culture('<?php echo $testid;?>','<?php echo $iso_no;?>','1')" <?php echo $dis;?>><i class="icon-save"></i> Save &amp; Validate</button>
			
			<!--<button class="btn btn-new" id="summary" name="summary" onclick="add_summary('<?php echo $testid;?>','<?php echo $iso_no;?>')"><i class="icon-edit"></i> <?php echo $sum;?> Summary</button>-->
			
			<button class="btn btn-back" id="cls" onclick="$('#btn_<?php echo $testid ?>').click();$('#test_id').focus()"><i class="icon-backward"></i> Back</button>
			
			<button class="btn btn-print" onclick="single_print_test('<?php echo $testid;?>','<?php echo $iso_no;?>')"><i class="icon-print"></i> Print</button>
			
		</div>
	</div>
<?php
}

?>
