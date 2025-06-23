<?php
session_start();

include("../../includes/connection.php");
$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$tinfo=$_POST['tinfo'];
$user=$_POST['user'];
$level=$_POST['level'];
$instrument_id=$_POST['instrument_id'];

if($opd_id)
{
	$pin_str="OPD ID";
	$pin=$opd_id;
}
if($ipd_id)
{
	$pin_str="OPD ID";
	$pin=$opd_id;
}
$pin_str="Bill No.";

?>
<div class="param_list" style="padding:10px;width: 98%;">
	<table class="table table-bordered table-condensed" style="width: 98%;">
<?php
// Cancel Request Check
$cancel_request_check=mysqli_fetch_array(mysqli_query($link, "select * from cancel_request where patient_id='$uhid' AND `opd_id`='$pin' AND `type`='2' "));
if(!$cancel_request_check)
{
	$tinfo=explode("@",$tinfo);
	$testid=$tinfo[2];
	
	$test_info=mysqli_fetch_assoc(mysqli_query($link,"SELECT `testname`,`type_id` FROM `testmaster` WHERE `testid`='$testid'"));
	
	$testname=$test_info['testname'];
	$type_id=$test_info['type_id'];
	
	echo "<input type='hidden' id='sel_testid' value='$testid'>";

	$disabled_doc_approved="";
	//if (strpos(strtolower($testname),'culture') !== false || strpos(strtolower($testname),'Culture') !== false || strpos(strtolower($testname),'CULTURE') !== false)  //------------------Culture--------------//
	if($type_id==150)
	{
		$result=mysqli_fetch_array(mysqli_query($link, "select MAX(`iso_no`) AS `iso_no` from testresults where `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' limit 1"));
		
		$iso_no_display="display:none;";
		if($result["iso_no"]>0)
		{
			$iso_no_display="";
		}
?>
		<div>
			<span class="side_name">Select Growth</span>
			<select id="growth_val" onkeyup="growth_val_up(event)" onchange="load_culture_data()" style="margin-left: 112px;">
				<option value="0" <?php if(!$result){ echo "selected"; } ?>>---Select---</option>
				<option value="1" <?php if($result["iso_no"]==0){ echo "selected"; } ?>>No Growth</option>
				<option value="2" <?php if($result["iso_no"]>0){ echo "selected"; } ?>>Growth</option>
			</select>
			<span id="iso_field" style="<?php echo $iso_no_display; ?>">
				<span class="side_name">No. of ISO</span>
				<select class="span2" id="iso_no_total" onkeyup="iso_no_total_up(event)" onchange="load_culture_data()" style="margin-left: 84px;">
				<?php
					for($z=0;$z<=10;$z++)
					{
						if($result["iso_no"]==$z){ $sel_iso="selected"; }else{ $sel_iso=""; }
						echo "<option value='$z' $sel_iso>$z</option>";
					}
				?>
				</select>
			</span>
		</div>
		<div id="load_culture_data_div"></div>
<?php
	}
	else
	{
		$iso_no=0;
		
		//DLC CHECK
		$dlc_chk="";
		$dlc_c=mysqli_query($link,"select * from testmaster_dlc_check where testid='$testid'");
		while($dl=mysqli_fetch_array($dlc_c))
		{
			$dlc_chk=$dlc_chk.",".$dl["paramid"];
		}
?>
		<input type="hidden" value="<?php echo $dlc_chk;?>" id="dlc_check" />
<?php
		$tch=0;
		$dch=1000;
		$tc=mysqli_fetch_array(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' ORDER BY `doc` DESC limit 1"));
		if($tc['doc']>0)
		{
			$tch=$tc['main_tech'];
			$dch=$tc['for_doc'];
			
			$disabled_doc_approved="disabled";
			if($level=="1" || $level=="13") // Admin, Pathology Doctor
			{
				$disabled_doc_approved="";
			}
		}
		else
		{
			$disabled_doc_approved="";
			$sum_qry=mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' ORDER BY `doc` DESC limit 1");
			if(mysqli_num_rows($sum_qry)>0)
			{
				$tc_s=mysqli_fetch_array(mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' limit 1"));	
			
				if($tc_s['doc']>0)
				{
					$tch=$tc_s['main_tech'];
					$dch=$tc['for_doc'];
					
					$disabled_doc_approved="disabled";
					if($level=="1" || $level=="13")
					{
						$disabled_doc_approved="";
					}
				}
			}
			else
			{
				if($testid==1227)
				{
					$tc_w=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' limit 1"));
					if($tc_w['doc']>0)
					{
						$tch=$tc_w['main_tech'];
						$dch=$tc['for_doc'];
						
						$disabled_doc_approved="disabled";
						if($level=="1" || $level=="13")
						{
							$disabled_doc_approved="";
						}
					}
				}
			}
		}
		
		$summary_btn_name="Add";
		$test_summary_sum=mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND testid='$testid'"));
		if($test_summary_sum>0)
		{
			$summary_btn_name="Update";
		}
		
		if($testid!=1227) // Not Widal
		{
			echo "<tr><th colspan='3'>$testname</th></tr>";
			$i=1;
			$pad=0;
			
			$param=mysqli_query($link, "select * from Testparameter where TestId='$testid' order by sequence");
			
			while($test_param=mysqli_fetch_array($param))
			{
				$result_param=$val=mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_id` FROM `testresults` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$test_param[ParamaterId]'"));
				
				$repeat_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `pathology_repeat_param_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$test_param[ParamaterId]' ORDER BY `repeat_id` DESC LIMIT 1"));
				
				$repeat_btn_show="display:none;";
				if($repeat_check)
				{
					$repeat_btn_show="";
				}
				
				$param_info=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$test_param[ParamaterId]'"));
				//echo $param_info["ResultType"]."-";
				echo "<tr>";
				
				if($param_info["ResultType"]==0) // Sub-Heading
				{
					echo "<td colspan='3' style='font-weight:bold;'>$param_info[Name]";
				}
				else if($param_info["ResultType"]==7) // Pad
				{
					echo "<tr><td colspan='3' valign='top'>";
				}
				else
				{
					echo "<tr><td style='width:300px'>$param_info[Name]</td><td valign='top' colspan='2'>";
				}
				
				if($param_info["ResultType"]==1) // Numeric only
				{
					$form_qry=mysqli_query($link, "select * from parameter_formula where ParameterID='$test_param[ParamaterId]'");
					$num_form=mysqli_num_rows($form_qry);
					if($num_form>0)
					{
						$form=mysqli_fetch_array($form_qry);
						$form_onclk="onfocus=check_form(this.id,'$form[formula]',$form[res_dec])"; // Formula function
					}
					else
					{
						$form_onclk='';
					}
					
					$chk_rng=mysqli_query($link,"select * from parameter_range where paramid='$test_param[ParamaterId]'");
					$num_chk=mysqli_num_rows($chk_rng);
					if($num_chk>0)
					{
						$e_rng=mysqli_fetch_array($chk_rng);
						$form_onclk.=" onkeyup=check_entry_range($test_param[ParamaterId],'$e_rng[e_range]',$i)";
					}
					
					$param_range="";
					
					if(!$val['result'])
					{
						$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and paramid='$test_param[ParamaterId]'"));
						
						$par_ran=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where parameter_id='$test_param[ParamaterId]' and status=0 and instrument_id='$instrument_id'"));
						$param_range=nl2br($par_ran["normal_range"]);
					}
					if($val["range_id"] && $val["range_id"]>0)
					{
						$par_ran=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$val[range_id]'"));
						$param_range=nl2br($par_ran[normal_range]);
					}
					echo "<div class='row'>";
						echo "<div class='span3'>";	
							echo "<input type='text' name='t_par$i' id='$test_param[ParamaterId]' class='t_par' value='$val[result]' onblur='check_range($test_param[ParamaterId],this.value,$i);dlc_total($test_param[ParamaterId],$testid)' $form_onclk />";
						echo "</div>";
						echo "<div class='span4'>";
							echo " <div id='norm_$test_param[ParamaterId]' style='display:inline-block;font-weight:bold;border-left:1px solid;padding-left:5px'>$param_range</div>";
						echo "</div>";
					echo "</div>";
					echo "</td>";
				?>
					<td>
				<?php
					if($result_param["result"] && $repeat_parameter==1)
					{
				?>
						<label id="repeat_param_label<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>">
							<input type="checkbox" id="repeat_param<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_save('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0','<?php echo $param_info["Name"]; ?>')">
							Repeat
						</label>
				<?php
					}
				?>
						<button class="btn btn-excel btn-mini" id="repeat_param_view_btn<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_view('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0')" style="<?php echo $repeat_btn_show; ?>"><i class="icon-eye-open"></i> View Repeat(s)</button>
					</td>
				<?php
					echo "</tr>";
					$i++;
				}
				else if($param_info["ResultType"]==2) // List of Choices
				{
					$val=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$test_param[ParamaterId]'"));
					if(!$val['result'])
					{
						$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and paramid='$test_param[ParamaterId]'"));
					}
					
					//$val["result"]=mysqli_real_escape_string($link, $val["result"]);
					
					//echo "<select name='t_par$i' id='$test_param[ParamaterId]' class='t_par'>";
					echo "<input type='text' name='t_par$i' id='$test_param[ParamaterId]' list='list$i' class='t_par' value='$val[result]' />";
					echo "<datalist id='list$i'>";
					$sel=mysqli_query($link, "select * from ResultOptions where id='$param_info[ResultOptionID]'");
					while($s=mysqli_fetch_array($sel))
					{
						$op=mysqli_fetch_array(mysqli_query($link, "select name from Options where id='$s[optionid]'"));
						echo "<option value='$op[name]'>$op[name]</option>";
					}
					echo "</datalist>";
					echo " <span style='color:green'><i>(Double click for options)</i></span>";
					echo "<span id='norm$i' class='normal_range animated infinite pulse'></span></td>";
				?>
					<td>
				<?php
					if($result_param["result"] && $repeat_parameter==1)
					{
				?>
						<label id="repeat_param_label<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>">
							<input type="checkbox" id="repeat_param<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_save('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0','<?php echo $param_info["Name"]; ?>')">
							Repeat
						</label>
				<?php
					}
				?>
						<button class="btn btn-excel btn-mini" id="repeat_param_view_btn<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_view('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0')" style="<?php echo $repeat_btn_show; ?>"><i class="icon-eye-open"></i> View Repeat(s)</button>
					</td>
				<?php
					echo "</tr>";
					$i++;
					
				}
				else if($param_info["ResultType"]==3) // One Line Text
				{
					$$num_form=mysqli_num_rows(mysqli_query($link, "select * from parameter_formula where ParameterID='$test_param[ParamaterId]'"));
					$form=mysqli_fetch_array(mysqli_query($link, "select * from parameter_formula where ParameterID='$test_param[ParamaterId]'"));
					if($num_form>0)
					{
						$form_onclk="onfocus=check_form(this.id,'$form[formula]',$form[res_dec])";
					}
					else
					{
						$form_onclk='';
					}
					
					$chk_rng=mysqli_query($link,"select * from parameter_range where paramid='$test_param[ParamaterId]'");
					$num_chk=mysqli_num_rows($chk_rng);
					if($num_chk>0)
					{
						$e_rng=mysqli_fetch_array($chk_rng);
						$form_onclk.=" onkeyup=check_entry_range($test_param[ParamaterId],'$e_rng[e_range]',$i)";
					}
					
					$val=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$test_param[ParamaterId]'"));
					if(!$val[result])
					{
						$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and paramid='$test_param[ParamaterId]'"));
					}
					echo "<input type='text' name='t_par$i' id='$test_param[ParamaterId]' class='t_par' value='$val[result]' onblur='check_range($test_param[ParamaterId],this.value)' style='auto;' $form_onclk />";
					echo "<div id='norm$i' class='normal_range animated infinite pulse'></div></td>";
				?>
					<td>
				<?php
					if($result_param["result"] && $repeat_parameter==1)
					{
				?>
						<label id="repeat_param_label<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>">
							<input type="checkbox" id="repeat_param<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_save('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0','<?php echo $param_info["Name"]; ?>')">
							Repeat
						</label>
				<?php
					}
				?>
						<button class="btn btn-excel btn-mini" id="repeat_param_view_btn<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_view('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0')" style="<?php echo $repeat_btn_show; ?>"><i class="icon-eye-open"></i> View Repeat(s)</button>
					</td>
				<?php
					echo "</tr>";
					$i++;
				}
				else if($param_info["ResultType"]==4) // Missing
				{
					$val=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$test_param[ParamaterId]'"));
					
					if(!$val['result'])
					{
						$summary=mysqli_fetch_array(mysqli_query($link, "select summary from test_summary where testid='$testid' and paramid='$test_param[ParamaterId]'"));
						$res=$summary['summary'];
					}
					else
					{
						$res=$val['result'];
					}
					
					echo "<textarea rows='2' cols='60' name='t_par$i' id='$test_param[ParamaterId]' class='t_par' onblur='check_range($test_param[ParamaterId],this.value)'>$res</textarea>";
					echo "<div id='norm$i' class='normal_range animated infinite pulse'></div></td><td></td></tr>";
					$i++;
				}
				else if($param_info["ResultType"]==5) // Sub-Heading End
				{
					
				}
				else if($param_info["ResultType"]==6) // Formula
				{
					$num_form=mysqli_num_rows(mysqli_query($link, "select * from parameter_formula where ParameterID='$test_param[ParamaterId]'"));
					$form=mysqli_fetch_array(mysqli_query($link, "select * from parameter_formula where ParameterID='$test_param[ParamaterId]'"));
					if($num_form>0)
					{
						$form_onclk="onfocus=check_form(this.id,'$form[formula]',$form[res_dec])";
					}
					else
					{
						$form_onclk='';
					}
					
					$chk_rng=mysqli_query($link,"select * from parameter_range where paramid='$test_param[ParamaterId]'");
					$num_chk=mysqli_num_rows($chk_rng);
					if($num_chk>0)
					{
						$e_rng=mysqli_fetch_array($chk_rng);
						$form_onclk.=" onkeyup=check_entry_range($test_param[ParamaterId],'$e_rng[e_range]',$i)";
					}
					
					$param_range="";
					$val=mysqli_fetch_array(mysqli_query($link, "select result,range_id from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$test_param[ParamaterId]'"));
					if(!$val[result])
					{
						$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and paramid='$test_param[ParamaterId]'"));
					}
					if($val[range_id]>0)
					{
						$par_ran=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$val[range_id]'"));
						$param_range=nl2br($par_ran[normal_range]);
					}
					
					echo "<div class='row'>";
					echo "<div class='span3'>";	
					echo "<input type='text' name='t_par$i' id='$test_param[ParamaterId]' class='t_par' value='$val[result]' onblur='check_range($test_param[ParamaterId],this.value)' $form_onclk />";
					echo "</div>";
					echo "<div class='span4'>";	
					echo "<div id='norm_$test_param[ParamaterId]' style='display:inline-block;font-weight:bold;border-left:1px solid;padding-left:5px'>$param_range</div></div></div></td><td></td></tr>";
					$i++;
				}
				else if($param_info["ResultType"]==7) // Pad
				{
					//$val=mysqli_fetch_array(mysqli_query($link, "select summary from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid'"));
					
					if(!$val["result"])
					{
						$summary=mysqli_fetch_array(mysqli_query($link, "select summary from test_summary where paramid='$test_param[ParamaterId]'"));
						$res=$summary["summary"];
					}
					else
					{
						//$res=$val["summary"];
						$res=$val["result"];
					}
					
					echo "<div id='padd$test_param[ParamaterId]'><textarea rows='3' cols='60' name='t_par$i' id='$test_param[ParamaterId]' class='t_par' padd='1' onblur='check_range($test_param[ParamaterId],this.value)'>$res</textarea></div>";
					
					echo "<script>add_pad_param($i,$test_param[ParamaterId])</script>";
					
					$pad=0;
				?>
					<td>
				<?php
					if($result_param["result"] && $repeat_parameter==1)
					{
				?>
						<label id="repeat_param_label<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>">
							<input type="checkbox" id="repeat_param<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_save('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0','<?php echo $param_info["Name"]; ?>')">
							Repeat
						</label>
				<?php
					}
				?>
						<button class="btn btn-excel btn-mini" id="repeat_param_view_btn<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_view('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0')" style="<?php echo $repeat_btn_show; ?>"><i class="icon-eye-open"></i> View Repeat(s)</button>
					</td>
				<?php
					echo "</tr>";
					$i++;
					$pad++;
				}
				else if($param_info["ResultType"]==8) // Sub_param
				{
					$num_form=mysqli_num_rows(mysqli_query($link, "select * from parameter_formula where ParameterID='$test_param[ParamaterId]'"));
					$form=mysqli_fetch_array(mysqli_query($link, "select * from parameter_formula where ParameterID='$test_param[ParamaterId]'"));
					if($num_form>0)
					{
						$form_onclk="onfocus=check_form(this.id,'$form[formula]',$form[res_dec])";
					}
					else
					{
						$form_onclk='';
					}
					
					$chk_rng=mysqli_query($link,"select * from parameter_range where paramid='$test_param[ParamaterId]'");
					$num_chk=mysqli_num_rows($chk_rng);
					if($num_chk>0)
					{
						$e_rng=mysqli_fetch_array($chk_rng);
						$form_onclk.=" onkeyup=check_entry_range($test_param[ParamaterId],'$e_rng[e_range]',$i)";
					}
					
					$param_range="";
					$val=mysqli_fetch_array(mysqli_query($link, "select result,range_id from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$test_param[ParamaterId]'"));
					if(!$val["result"])
					{
						$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and paramid='$test_param[ParamaterId]'"));
						
						$par_ran=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where parameter_id='$test_param[ParamaterId]' and status=0 and instrument_id='$instrument_id'"));
						$param_range=nl2br($par_ran[normal_range]);
					}
					if($val["range_id"] && $val["range_id"]>0)
					{
						$par_ran=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$val[range_id]'"));
						$param_range=nl2br($par_ran["normal_range"]);
					}
					
					echo "<div class='row'>";
					echo "<div class='span3'>";
					echo "<input type='text' name='t_par$i' id='$test_param[ParamaterId]' class='t_par' value='$val[result]' onblur='check_range($test_param[ParamaterId],this.value,$i);dlc_total($test_param[ParamaterId],$testid)' $form_onclk />";
					echo "</div>";
					echo "<div class='span4'>";
					echo " <div id='norm_$test_param[ParamaterId]' style='display:inline-block;font-weight:bold;border-left:1px solid;padding-left:5px'>$param_range</div></div></div></td>";
				?>
					<td>
				<?php
					if($result_param["result"] && $repeat_parameter==1)
					{
				?>
						<label id="repeat_param_label<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>">
							<input type="checkbox" id="repeat_param<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_save('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0','<?php echo $param_info["Name"]; ?>')">
							Repeat
						</label>
				<?php
					}
				?>
						<button class="btn btn-excel btn-mini" id="repeat_param_view_btn<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_view('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0')" style="<?php echo $repeat_btn_show; ?>"><i class="icon-eye-open"></i> View Repeat(s)</button>
					</td>
				<?php
					echo "</tr>";
					$i++;
				}
				else if($param_info["ResultType"]==27) // Multiline Text
				{
					$val=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$test_param[ParamaterId]'"));
					
					if(!$val["result"])
					{
						//$summary=mysqli_fetch_array(mysqli_query($link, "select summary from test_summary where testid='$testid' and paramid='$test_param[ParamaterId]'"));
						//$res=$summary[summary];
						
						$val=mysqli_fetch_array(mysqli_query($link, "select result from param_fix_result where testid='$testid' and paramid='$test_param[ParamaterId]'"));
						$res=$val["result"];
					}
					else
					{
						$res=$val["result"];
					}
					
					echo "<textarea rows='5' name='t_par$i' id='$test_param[ParamaterId]' class='t_par' style='resize:none;width: 98%;'>$res</textarea>";
					echo "<div id='norm$i' class='normal_range animated infinite pulse'></div></td>";
				?>
					<td>
				<?php
					if($result_param["result"] && $repeat_parameter==1)
					{
				?>
						<label id="repeat_param_label<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>">
							<input type="checkbox" id="repeat_param<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_save('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0','<?php echo $param_info["Name"]; ?>')">
							Repeat
						</label>
				<?php
					}
				?>
						<button class="btn btn-excel btn-mini" id="repeat_param_view_btn<?php echo $testid; ?>tst<?php echo $test_param["ParamaterId"]; ?>" onclick="repeat_param_view('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $test_param["ParamaterId"]; ?>','0')" style="<?php echo $repeat_btn_show; ?>"><i class="icon-eye-open"></i> View Repeat(s)</button>
					</td>
				<?php
					echo "</tr>";
					$i++;
				}
				else if($param_info["ResultType"]==32 || $param_info["ResultType"]==36)
				{
					echo "Echo Type";
					echo "<div id='norm$i' class='normal_range'></div></td></tr>";
					$i++;
				}
				else if($param_info["ResultType"]==60)
				{
					echo "Grid";
					echo "<div id='norm$i' class='normal_range'></div></td></tr>";
					$i++;
				}
			}
		?>
			<tr>
				<td colspan="2" style="text-align:center">
				
					<div id="param_error"></div>
					
					<button class="btn btn-save" id="save" name="t_par<?php echo $i;?>" onclick="save_test_param('<?php echo $testid;?>','1')" <?php echo $disabled_doc_approved;?>><i class="icon-save"></i> Save &amp; Validate</button>
				<?php
					if($pad>0)
					{
						if($test_info["type_id"]==30) // Histopathology
						{
				?>
							<button class="btn btn-process" id="image" onclick="add_image(<?php echo $testid;?>)"><i class="icon-camera"></i> Add Image</button>
				<?php
						}
					}
					else
					{
				?>
						<button class="btn btn-new" id="summary" name="summary" onclick="add_summary('<?php echo $testid;?>')"><i class="icon-edit"></i> <?php echo $summary_btn_name;?> Summary</button>
				<?php
					}
				?>
					<button class="btn btn-back" id="cls" onclick="$('#btn_<?php echo $testid ?>').click();$('#test_id').focus()"><i class="icon-backward"></i> Back</button>
					
					<!--
					<button class="btn btn-save" id="save" name="t_par<?php echo $i;?>" onclick="save_summary('<?php echo $testid;?>','1')" <?php echo $disabled_doc_approved;?>><i class="icon-save"></i> Save &amp; Validate</button>
					
					<button class="btn btn-process" id="image" onclick="add_image(<?php echo $testid;?>)"><i class="icon-camera"></i> Add Image</button>
					
					<button class="btn btn-back" id="cls" onclick="$('#btn_<?php echo $testid ?>').click();$('#test_id').focus()"><i class="icon-backward"></i> Back</button>
					-->
			  </td>
			</tr>
		  </table>
		</div>
		<?php
		}
		else
		{
			$w1=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno=1"));
			$w2=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno=2"));
			$w3=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno=3"));
			$w4=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno=4"));
			
			echo "<th colspan='2'style='text-align:center'>$testname</th>";
			
		?>
		<tr>
			<td colspan="2">
				<table style="width: 100% !important;">
					<tr>
						<th>Specimen</th>
						<td>
							<input type="text" id="specimen" value="<?php echo $w1["specimen"]?>">
						</td>
						<th>Incubation Temperature(°C)</th>
						<td>
							<input type="text" id="incubation_temp" value="<?php echo $w1["incubation_temp"]?>">
						</td>
						<th>Method</th>
						<td>
							<input type="text" id="method" value="<?php echo $w1["method"]?>" list="method_list">
							<datalist id="method_list">
								<option>Slide Agglutination</option>
								<option>Tube Agglutination</option>
							</datalist>
						</td>
					</tr>
					<tr>
						<td colspan="6"></td>
					</tr>
				</table>
				<table style="width: 100% !important;">
					<tr>
						<th colspan="2">Dilution</th>
						<th>1:20</th>
						<th>1:40</th>
						<th>1:80</th>
						<th>1:160</th>
						<th>1:320</th>
						<th>1:640</th>
					</tr>
					<tr>
						<th rowspan="4">Anitgens</th>
						<th>"O"</th>
						<td><input type="text" name="t_par1" id="o0" size="2" class="td_widal" value="<?php echo $w1['F1']?>" maxlength='3' onkeyup="chk_widal1(1,this.id,event)" /></td>
						<td><input type="text" name="t_par2" id="o1" size="2" class="td_widal" value="<?php echo $w1['F2']?>" maxlength='3' onkeyup="chk_widal1(2,this.id,event)" /></td>
						<td><input type="text" name="t_par3" id="o2" size="2" class="td_widal" value="<?php echo $w1['F3']?>" maxlength='3' onkeyup="chk_widal1(3,this.id,event)" /></td>
						<td><input type="text" name="t_par4" id="o3" size="2" class="td_widal" value="<?php echo $w1['F4']?>" maxlength='3' onkeyup="chk_widal1(4,this.id,event)" /></td>
						<td><input type="text" name="t_par5" id="o4" size="2" class="td_widal" value="<?php echo $w1['F5']?>" maxlength='3' onkeyup="chk_widal1(5,this.id,event)" /></td>
						<td><input type="text" name="t_par6" id="o5" size="2" class="td_widal" value="<?php echo $w1['F6']?>" maxlength='3' onkeyup="chk_widal1(6,this.id,event)" /></td>
					</tr>
					<tr>
						<th style="text-align:left">"H"</th>
						<td><input type="text" name="t_par7" id="h0" size="2" class="td_widal" value="<?php echo $w2['F1']?>" maxlength='3' onkeyup="chk_widal1(7,this.id,event)" /></td>
						<td><input type="text" name="t_par8" id="h1" size="2" class="td_widal" value="<?php echo $w2['F2']?>" maxlength='3' onkeyup="chk_widal1(8,this.id,event)" /></td>
						<td><input type="text" name="t_par9" id="h2" size="2" class="td_widal" value="<?php echo $w2['F3']?>" maxlength='3' onkeyup="chk_widal1(9,this.id,event)" /></td>
						<td><input type="text" name="t_par10" id="h3" size="2" class="td_widal" value="<?php echo $w2['F4']?>" maxlength='3' onkeyup="chk_widal1(10,this.id,event)" /></td>
						<td><input type="text" name="t_par11" id="h4" size="2" class="td_widal" value="<?php echo $w2['F5']?>" maxlength='3' onkeyup="chk_widal1(11,this.id,event)" /></td>
						<td><input type="text" name="t_par12" id="h5" size="2" class="td_widal" value="<?php echo $w2['F6']?>" maxlength='3' onkeyup="chk_widal1(12,this.id,event)" /></td>
					</tr>
					<tr>
						<th style="text-align:left">"A(H)"</th>
						<td><input type="text" name="t_par13" id="ah0" size="2" class="td_widal" value="<?php echo $w3['F1']?>" maxlength='3' onkeyup="chk_widal1(13,this.id,event)" /></td>
						<td><input type="text" name="t_par14" id="ah1" size="2" class="td_widal" value="<?php echo $w3['F2']?>" maxlength='3' onkeyup="chk_widal1(14,this.id,event)" /></td>
						<td><input type="text" name="t_par15" id="ah2" size="2" class="td_widal" value="<?php echo $w3['F3']?>" maxlength='3' onkeyup="chk_widal1(15,this.id,event)" /></td>
						<td><input type="text" name="t_par16" id="ah3" size="2" class="td_widal" value="<?php echo $w3['F4']?>" maxlength='3' onkeyup="chk_widal1(16,this.id,event)" /></td>
						<td><input type="text" name="t_par17" id="ah4" size="2" class="td_widal" value="<?php echo $w3['F5']?>" maxlength='3' onkeyup="chk_widal1(17,this.id,event)" /></td>
						<td><input type="text" name="t_par18" id="ah5" size="2" class="td_widal" value="<?php echo $w3['F6']?>" maxlength='3' onkeyup="chk_widal1(18,this.id,event)" /></td>
					</tr>
					<tr>
						<th style="text-align:left">"B(H)"</th>
						<td><input type="text" name="t_par19" id="bh0" size="2" class="td_widal" value="<?php echo $w4['F1']?>" maxlength='3' onkeyup="chk_widal1(19,this.id,event)" /></td>
						<td><input type="text" name="t_par20" id="bh1" size="2" class="td_widal" value="<?php echo $w4['F2']?>" maxlength='3' onkeyup="chk_widal1(20,this.id,event)" /></td>
						<td><input type="text" name="t_par21" id="bh2" size="2" class="td_widal" value="<?php echo $w4['F3']?>" maxlength='3' onkeyup="chk_widal1(21,this.id,event)" /></td>
						<td><input type="text" name="t_par22" id="bh3" size="2" class="td_widal" value="<?php echo $w4['F4']?>" maxlength='3' onkeyup="chk_widal1(22,this.id,event)" /></td>
						<td><input type="text" name="t_par23" id="bh4" size="2" class="td_widal" value="<?php echo $w4['F5']?>" maxlength='3' onkeyup="chk_widal1(23,this.id,event)" /></td>
						<td><input type="text" name="t_par24" id="bh5" size="2" class="td_widal" value="<?php echo $w4['F6']?>" maxlength='3' onkeyup="chk_widal1(24,this.id,event)" /></td>
					</tr>
					<tr>
						<th colspan="2">IMPRESSION</th>
						<td colspan="7">
							<textarea id="imp" name="t_par25" style="width: 94%;resize: none;"><?php if($w4['DETAILS']){ echo $w4['DETAILS'];}else{ echo "Titre Insignificant";}?></textarea>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				
				<button class="btn btn-save" id="save" name="t_par<?php echo $i;?>" onclick="save_widal('<?php echo $testid;?>','1')" <?php echo $disabled_doc_approved;?>><i class="icon-save"></i> Save &amp; Validate</button>
				
				<button class="btn btn-new" id="summary" name="summary" onclick="add_summary('<?php echo $testid;?>')"><i class="icon-edit"></i> <?php echo $summary_btn_name;?> Summary</button>
				
				<button class="btn btn-back" id="cls" onclick="$('#btn_<?php echo $testid ?>').click();$('#test_id').focus()"><i class="icon-backward"></i> Back</button>
				<!--
				<input type="button" id="save" value="Save" name="t_par<?php echo $i;?>" class="btn btn-custom" onclick="save_widal(<?php echo $testid;?>)" <?php echo $disabled_doc_approved;?> />
				<input type="button" id="summary" name="summary" value="<?php echo $sum;?> Summary" class="btn btn-custom" onclick="add_summary(<?php echo $testid;?>)" />
				<input type="button" id="cls" value="Close" class="btn btn-custom" onclick="$('#btn_<?php echo $testid ?>').click(); $('#test_id').focus()" />
				-->
			</td>
		</tr>
	<?php
			$i=26;

		}
	}
}
else
{
	$val=2;
	include("cancel_request_msg.php");
}

mysqli_close($link);
?>
