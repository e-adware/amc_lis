<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user=$_SESSION["emp_id"];

$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST['type'];

//~ print_r($_POST);
//~ exit();

if($_POST["type"]=="load_culture_data")
{
	$growth_val=mysqli_real_escape_string($link, $_POST["growth_val"]);
	$iso_no_total=mysqli_real_escape_string($link, $_POST["iso_no_total"]);
	
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$ipd_id=mysqli_real_escape_string($link, $_POST["ipd_id"]);
	$batch_no=mysqli_real_escape_string($link, $_POST["batch_no"]);
	$testid=mysqli_real_escape_string($link, $_POST["testid"]);
	
	$cult_testid=$culture_setup_testid; // From connection file
?>
	<table class="table table-condensed">
<?php
	
	if($growth_val==1) // No Growth
	{
?>
		<thead class="table_header_fix" id="pat_dept_test_cult_params_header">
			<tr>
				<th style="width: 300px;">Parameter</th>
				<th>RESULT</th>
				<th style="width: 110px;">Validate</th>
				<th style="width: 100px;">Result Status</th>
			</tr>
		</thead>
<?php
		$i=1;
		$iso_no=0;
		$cult_param_qry=mysqli_query($link,"SELECT a.*,b.Name,b.ID,b.ResultType,b.ResultOptionID FROM Testparameter a, Parameter_old b where a.TestId='$cult_testid' and b.ID=a.ParamaterId and b.ResultOptionID!='68' and b.ID NOT IN(311,312) order by a.sequence");
		
		while($param_info=mysqli_fetch_array($cult_param_qry))
		{
			$paramid=$param_info["ParamaterId"];
			
			$unit_info=mysqli_fetch_array(mysqli_query($link, "SELECT `unit_name` FROM `Units` WHERE `ID`='$param_info[UnitsID]'"));
			
			$result_text_style="";
			
			$repeat_param_btn_display=0;
			
			$instrument_name="";
			$data_entry_name="";
			$main_tech_name="";
			$result="";
			$approve_result="";
			$main_tech=0;
			$instrument_id=0;
			$test_result=mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_status`,`range_id`,`tech_note`,`doc_note`,`instrument_id`,`result_hide`,`doc`,`tech`,`main_tech`,`for_doc` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));
			if($test_result)
			{
				$result=$test_result["result"];
				$instrument_id=$test_result["instrument_id"];
				
				$main_tech=$test_result["main_tech"];
				$approve_result=$result;
				
				if($instrument_id>0)
				{
					$instrument_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_instrument_master` WHERE `id`='$instrument_id'"));
					$instrument_name=$instrument_info["name"];
				}else
				{
					$data_entry_user=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[tech]'"));
					$data_entry_name=$data_entry_user["name"];
				}
				
				if($test_result["main_tech"]>0)
				{
					$main_tech_user=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[main_tech]'"));
					$main_tech_name=$main_tech_user["name"];
				}
				if($test_result["range_status"]>0)
				{
					$result_text_style="color:red;";
				}
				
				$dlc_check_str="SELECT `slno`,`opd_id`,`ipd_id`,`batch_no`,`result`,`range_status`,`range_id` FROM `testresults` WHERE `patient_id`='$patient_id' AND `paramid`='$paramid' AND `slno`<'$test_result[slno]' ORDER BY `slno` DESC";
				
				$repeat_param_btn_display++;
			}else
			{
				$test_sample_result=mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`equip_name` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`=''")); // iso_blank
				if($test_sample_result["result"])
				{
					$result=$test_sample_result["result"];
					$approve_result=$result;
					
					$lab_instrument=mysqli_fetch_array(mysqli_query($link,"SELECT `id` AS `instrument_id` FROM `lab_instrument_master` WHERE `name`='$test_sample_result[equip_name]'"));
					if($lab_instrument)
					{
						$instrument_id=$lab_instrument["instrument_id"];
					}
					
					$instrument_name=$test_sample_result["equip_name"];
					
					$repeat_param_btn_display++;
				}
				else
				{
					$param_fix_result=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `param_fix_result` WHERE `testid`='$testid' AND `paramid`='$paramid'"));
					
					$result=$param_fix_result["result"];
				}
				$dlc_check_str="SELECT `slno`,`opd_id`,`ipd_id`,`batch_no`,`result`,`range_status`,`range_id` FROM `testresults` WHERE `patient_id`='$patient_id' AND `paramid`='$paramid' ORDER BY `slno` DESC";
			}
			$dlc_check_qry=mysqli_query($link, $dlc_check_str);
			$dlc_check_num=mysqli_num_rows($dlc_check_qry);
			
			if($result=="" && $paramid==309) // Specimen
			{
				//~ $test_sample_name=mysqli_fetch_array(mysqli_query($link, "SELECT a.`Name` FROM `Sample` a, `TestSample` b WHERE a.`ID`=b.`SampleId` AND b.`TestId`='$testid'"));
				//~ if($test_sample_name)
				//~ {
					//~ $result=$test_sample_name["Name"];
				//~ }
			}
			
			$approve_cls="approve_param";
			$doc_approve_disabled="";
			$approve_function="onclick=\"approve_param_each('$testid','$paramid','0','$param_info[Name]','$dept_id')\"";
			
			if($test_result["doc"]>0)
			{
				$doc_approve_disabled="disabled='disabled'";
				$approve_function="";
				$approve_cls="";
			}
			
			$td_str="td";
			if($param_info["ResultType"]==0)
			{
				$td_str="th";
			}
			$left_space_str=" &nbsp;&nbsp;&nbsp;&nbsp;";
			
			// Repeat Start
			$repeat_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `pathology_repeat_param_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$paramid' ORDER BY `repeat_id` DESC LIMIT 1"));
			
			$repeat_param_btn_data="<div style='display: inline;'>";
			if($repeat_param_btn_display>0)
			{
				$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <label id='repeat_param_label".$testid."tst".$paramid."' style='display: inline;'> <input type='checkbox' id='repeat_param".$testid."tst".$paramid."' onclick=\"repeat_param_save('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','0','$param_info[Name]','$dept_id')\" $doc_approve_disabled> Repeat </label>";
			}else
			{
				$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			
			if($repeat_check)
			{
				$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp; <a class='btn btn-link btn-mini' id='repeat_param_view_btn".$testid."tst".$paramid."' onclick=\"repeat_param_view('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','0')\" style='$repeat_btn_show'>View Repeat(s)</a>";
			}else
			{
				$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
			}
			$repeat_param_btn_data.="</div>";
			// Repeat End
			
			// Check result update
			$testresults_update_str="";
			$testresults_update=mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`edit_user`,`edit_date`,`edit_time` FROM `testresults_update` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no' ORDER BY `slno` DESC"));
			if($testresults_update)
			{
				$ch_user_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$testresults_update[edit_user]'"));
				$testresults_update_str="Last result: ".$testresults_update["result"].", changed by ".$ch_user_info["name"]." on ".date("d-M-Y",strtotime($testresults_update["edit_date"]))." ".date("h:i A",strtotime($testresults_update["edit_time"]));
			}
			
			$chk_box="";
			if($test_result["main_tech"]>0)
			{
				$chk_box="checked='checked'";
			}
?>
		<tr>
			<td><?php echo $param_info["Name"]; ?></td>
			<td>
				<input type="text" class="t_par" name="t_par<?php echo $i; ?>" id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>" param_testid="<?php echo $testid; ?>" iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>" slno="<?php echo $i; ?>" value="<?php echo $result; ?>" list="list<?php echo $i; ?>" <?php echo $doc_approve_disabled; ?>>
				<datalist id="list<?php echo $i; ?>">
			<?php
				if($param_info["ResultOptionID"]>0)
				{
					$ResultOptions_qry=mysqli_query($link, "SELECT a.`name` FROM `Options` a, `ResultOptions` b WHERE a.`id`=b.`optionid` AND b.`id`='$param_info[ResultOptionID]'");
					while($ResultOptions=mysqli_fetch_array($ResultOptions_qry))
					{
						echo "<option>$ResultOptions[name]</option>";
					}
				}else
				{
					if($paramid==948) // Organism Quantity
					{
						$organism_qry=mysqli_query($link, "SELECT `organism` FROM `organism_quantity` WHERE `slno`>0 ORDER BY `organism`");
						while($organism=mysqli_fetch_array($organism_qry))
						{
							echo "<option>$organism[organism]</option>";
						}
					}
				}
			?>
				</datalist>
			<?php
				if($repeat_param_btn_data)
				{
					echo $repeat_param_btn_data;
				}
				
				if($dlc_check_num>0)
				{
			?>
					<a class="btn btn-link btn-mini" style="font-size:10px;" onclick="load_delta_check('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $paramid; ?>')">Delta Check</a>
			<?php
				}
			?>
				<div style="color:red;display:;font-size: 10px;" id="last_result<?php echo $i; ?>">
					<?php
						echo $testresults_update_str;
					?>
				</div>
			</td>
			<td>
				<label name="approve_param<?php echo $testid; ?>tst<?php echo $paramid; ?>">
					<input type="checkbox" class="<?php echo $approve_cls; ?> approve<?php echo $i; ?>" id="<?php echo $testid; ?>T<?php echo $paramid; ?>" value="<?php echo $testid; ?>TP<?php echo $paramid; ?>" <?php echo $approve_function; ?> <?php echo $doc_approve_disabled; ?> <?php echo $chk_box; ?> >
			<?php
				if($test_result["main_tech"]>0)
				{
					$tech_name=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[main_tech]'"));
					echo "Validated<br><small>(".$tech_name["name"].")</small>";
				}else
				{
					echo "Validate";
				}
			?>
				</label>
			</td>
			<td>
			<?php
				if($instrument_name)
				{
					echo "<div> Interfaced from <br><b>".$instrument_name."</b></div>";
				}
				if($data_entry_name)
				{
					echo "<div>Data entry by <br><b>".$data_entry_name."</b></div>";
				}
				if($main_tech_name)
				{
					echo "<div>Validated by <br><b>".$main_tech_name."</b></div>";
				}
			?>
			</td>
		</tr>
<?php
			$i++;
		}
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
							<li class="<?php echo $active_cls; ?> iso_no_cls" value="<?php echo $iso_no; ?>" id="<?php echo $testid; ?>iso_li<?php echo $iso_no; ?>"><a data-toggle="tab" href="#tab_iso<?php echo $iso_no; ?>" id="cult_tab<?php echo $iso_no; ?>" onclick="cult_tab_click('<?php echo $testid; ?>','<?php echo $iso_no; ?>')">ISO <?php echo $iso_no; ?></a></li>
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
						<div id="tab_iso<?php echo $testid; ?><?php echo $iso_no; ?>" class="tab_iso_cls<?php echo $testid; ?> tab-pane <?php echo $active_cls; ?>">
							
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
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$ipd_id=mysqli_real_escape_string($link, $_POST["ipd_id"]);
	$batch_no=mysqli_real_escape_string($link, $_POST["batch_no"]);
	$testid=mysqli_real_escape_string($link, $_POST["testid"]);
	
	$iso_no=mysqli_real_escape_string($link, $_POST["iso_no"]);
	
	$main_tech=0;
	$for_doc=0;
	$tc=mysqli_fetch_array(mysqli_query($link, "select * from testresults where `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and `doc`>0 limit 1"));
	if($tc['doc']>0)
	{
		$main_tech=$tc['main_tech'];
		$for_doc=$tc['for_doc'];
		
		$disabled_doc_approved="disabled";
		if($level=="1" || $level=="13") // Admin or Pathology Doctor
		{
			$disabled_doc_approved="";
		}
	}
	else
	{
		$disabled_doc_approved="";
		$tc_s=mysqli_fetch_array(mysqli_query($link, "select * from patient_test_summary where `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and `doc`>0 limit 1"));
		
		if($tc_s['doc']>0)
		{
			$main_tech=$tc_s['main_tech'];
			$for_doc=$tc['for_doc'];
			
			$disabled_doc_approved="disabled";
			if($level=="1" || $level=="13") // Admin or Pathology Doctor
			{
				$disabled_doc_approved="";
			}
		}
	}
	$cult_testid=$culture_setup_testid; // From connection file
?>
	<div>
		<table class="table table-condensed">
			<thead class="table_header_fix" id="pat_dept_test_cult_params_header">
				<tr>
					<th style="width: 20%;">Parameter</th>
					<th style="width: 50%;">RESULT</th>
					<th style="width: 15%;">Validate</th>
					<th style="width: 15%;">Result Status</th>
				</tr>
			</thead>
	<?php
		$i=1;
		$cult_param_qry=mysqli_query($link,"SELECT a.*,b.Name,b.ID,b.ResultType,b.ResultOptionID FROM Testparameter a, Parameter_old b where a.TestId='$cult_testid' and b.ID=a.ParamaterId and b.ResultOptionID!='68' and b.ID NOT IN(311,312) ORDER BY a.`sequence`");
		
		while($param_info=mysqli_fetch_array($cult_param_qry))
		{
			$paramid=$param_info["ParamaterId"];
			
			$unit_info=mysqli_fetch_array(mysqli_query($link, "SELECT `unit_name` FROM `Units` WHERE `ID`='$param_info[UnitsID]'"));
			
			$result_text_style="";
			
			$repeat_param_btn_display=0;
			
			$instrument_name="";
			$data_entry_name="";
			$main_tech_name="";
			$result="";
			$approve_result="";
			$main_tech=0;
			$instrument_id=0;
			$test_result=mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_status`,`range_id`,`tech_note`,`doc_note`,`instrument_id`,`result_hide`,`doc`,`tech`,`main_tech`,`for_doc` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));
			if($test_result)
			{
				$result=$test_result["result"];
				$instrument_id=$test_result["instrument_id"];
				
				$main_tech=$test_result["main_tech"];
				$approve_result=$result;
				
				if($instrument_id>0)
				{
					$instrument_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_instrument_master` WHERE `id`='$instrument_id'"));
					$instrument_name=$instrument_info["name"];
				}else
				{
					$data_entry_user=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[tech]'"));
					$data_entry_name=$data_entry_user["name"];
				}
				
				if($test_result["main_tech"]>0)
				{
					$main_tech_user=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[main_tech]'"));
					$main_tech_name=$main_tech_user["name"];
				}
				
				if($test_result["range_status"]>0)
				{
					$result_text_style="color:red;";
				}
				
				$dlc_check_str="SELECT `slno`,`opd_id`,`ipd_id`,`batch_no`,`result`,`range_status`,`range_id` FROM `testresults` WHERE `patient_id`='$patient_id' AND `paramid`='$paramid' AND `slno`<'$test_result[slno]' ORDER BY `slno` DESC";
				
				$repeat_param_btn_display++;
			}else
			{
				$test_sample_result=mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`equip_name` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));
				if($test_sample_result["result"])
				{
					$result=$test_sample_result["result"];
					$approve_result=$result;
					
					$lab_instrument=mysqli_fetch_array(mysqli_query($link,"SELECT `id` AS `instrument_id` FROM `lab_instrument_master` WHERE `name`='$test_sample_result[equip_name]'"));
					if($lab_instrument)
					{
						$instrument_id=$lab_instrument["instrument_id"];
					}
					$instrument_name=$test_sample_result["equip_name"];
					
					$repeat_param_btn_display++;
				}
				else
				{
					$param_fix_result=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `param_fix_result` WHERE `testid`='$testid' AND `paramid`='$paramid'"));
					
					$result=$param_fix_result["result"];
					
					if($paramid==535) // Comments
					{
						$result="To be co related clinically";
					}
				}
				$dlc_check_str="SELECT `slno`,`opd_id`,`ipd_id`,`batch_no`,`result`,`range_status`,`range_id` FROM `testresults` WHERE `patient_id`='$patient_id' AND `paramid`='$paramid' ORDER BY `slno` DESC";
			}
			$dlc_check_qry=mysqli_query($link, $dlc_check_str);
			$dlc_check_num=mysqli_num_rows($dlc_check_qry);
			
			if($result=="" && $paramid==309) // Specimen
			{
				//~ $test_sample_name=mysqli_fetch_array(mysqli_query($link, "SELECT a.`Name` FROM `Sample` a, `TestSample` b WHERE a.`ID`=b.`SampleId` AND b.`TestId`='$testid'"));
				//~ if($test_sample_name)
				//~ {
					//~ $result=$test_sample_name["Name"];
				//~ }
			}
			
			$approve_cls="approve_param";
			$doc_approve_disabled="";
			$approve_function="onclick=\"approve_param_each('$testid','$paramid','$iso_no','$param_info[Name]','$dept_id')\"";
			
			if($test_result["doc"]>0)
			{
				$doc_approve_disabled="disabled='disabled'";
				$approve_function="";
				$approve_cls="";
			}
			
			$td_str="td";
			if($param_info["ResultType"]==0)
			{
				$td_str="th";
			}
			$left_space_str=" &nbsp;&nbsp;&nbsp;&nbsp;";
			
			// Repeat Start
			$repeat_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `pathology_repeat_param_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$paramid' ORDER BY `repeat_id` DESC LIMIT 1"));
			
			$repeat_param_btn_data="<div style='display: inline;'>";
			if($repeat_param_btn_display>0)
			{
				$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <label id='repeat_param_label".$testid."tst".$paramid."' style='display: inline;'> <input type='checkbox' id='repeat_param".$testid."tst".$paramid."' onclick=\"repeat_param_save('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','0','$param_info[Name]','$dept_id')\" $doc_approve_disabled> Repeat </label>";
			}else
			{
				$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			if($repeat_check)
			{
				$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp; <a class='btn btn-link btn-mini' id='repeat_param_view_btn".$testid."tst".$paramid."' onclick=\"repeat_param_view('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','0')\" style='$repeat_btn_show'>View Repeat(s)</a>";
			}else
			{
				$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
			}
			$repeat_param_btn_data.="</div>";
			// Repeat End
			
			// Check result update
			$testresults_update_str="";
			$testresults_update=mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`edit_user`,`edit_date`,`edit_time` FROM `testresults_update` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no' ORDER BY `slno` DESC"));
			if($testresults_update)
			{
				$ch_user_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$testresults_update[edit_user]'"));
				$testresults_update_str="Last result: ".$testresults_update["result"].", changed by ".$ch_user_info["name"]." on ".date("d-M-Y",strtotime($testresults_update["edit_date"]))." ".date("h:i A",strtotime($testresults_update["edit_time"]));
			}
			
			$chk_box="";
			if($test_result["main_tech"]>0)
			{
				$chk_box="checked='checked'";
			}
?>
		<tr>
			<td><?php echo $param_info["Name"]; ?></td>
			<td>
				<input type="text" class="t_par" name="t_par<?php echo $i; ?>" id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>" param_testid="<?php echo $testid; ?>" iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>" slno="<?php echo $i; ?>" value="<?php echo $result; ?>" list="list<?php echo $i; ?>" <?php echo $doc_approve_disabled; ?>>
				<datalist id="list<?php echo $i; ?>">
			<?php
				if($param_info["ResultOptionID"]>0)
				{
					$ResultOptions_qry=mysqli_query($link, "SELECT a.`name` FROM `Options` a, `ResultOptions` b WHERE a.`id`=b.`optionid` AND b.`id`='$param_info[ResultOptionID]'");
					while($ResultOptions=mysqli_fetch_array($ResultOptions_qry))
					{
						echo "<option>$ResultOptions[name]</option>";
					}
				}else
				{
					if($paramid==948) // Organism Quantity
					{
						$organism_qry=mysqli_query($link, "SELECT `organism` FROM `organism_quantity` WHERE `slno`>0 ORDER BY `organism`");
						while($organism=mysqli_fetch_array($organism_qry))
						{
							echo "<option>$organism[organism]</option>";
						}
					}
				}
			?>
				</datalist>
			<?php
				if($repeat_param_btn_data)
				{
					echo $repeat_param_btn_data;
				}
				
				if($dlc_check_num>0)
				{
			?>
					<a class="btn btn-link btn-mini" style="font-size:10px;" onclick="load_delta_check('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $paramid; ?>')">Delta Check</a>
			<?php
				}
			?>
				<div style="color:red;display:;font-size: 10px;" id="last_result<?php echo $i; ?>">
					<?php
						echo $testresults_update_str;
					?>
				</div>
			</td>
			<td>
				<label name="approve_param<?php echo $testid; ?>tst<?php echo $paramid; ?>">
					<input type="checkbox" class="<?php echo $approve_cls; ?> approve<?php echo $i; ?>" id="<?php echo $testid; ?>T<?php echo $paramid; ?>" value="<?php echo $testid; ?>TP<?php echo $paramid; ?>" <?php echo $approve_function; ?> <?php echo $doc_approve_disabled; ?> <?php echo $chk_box; ?> >
			<?php
				if($test_result["main_tech"]>0)
				{
					$tech_name=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[main_tech]'"));
					echo "Validated<br><small>(".$tech_name["name"].")</small>";
				}else
				{
					echo "Validate";
				}
			?>
				</label>
			</td>
			<td>
			<?php
				if($instrument_name)
				{
					echo "<div> Interfaced from : <b>".$instrument_name."</b></div>";
				}
				if($data_entry_name)
				{
					echo "<div>Data entry by : <b>".$data_entry_name."</b></div>";
				}
				if($main_tech_name)
				{
					echo "<div>Validated by : <b>".$main_tech_name."</b></div>";
				}
			?>
			</td>
		</tr>
<?php
			$i++;
		}
?>
		</table>
		<style>
			.widget-content {
			  width: 98%;
			}
		</style>
	</div>
	<div class="iso_antibiotic_data">
		<div style="text-align:center;font-weight:bold;background-color:#CCC;">
			ANTIBIOTICS OF ISO <?php echo $iso_no; ?>
			<br>
			
			<input type="text" id="searchh" onkeyup="search(this.value)" placeholder="Type to search ANTIBIOTICS">
		</div>
		<div style="max-height:600px;overflow:scroll;overflow-x:hidden">
			<table class="table table-bordered table-condensed" id="tblData">
				<thead class="table_header_fix pat_dept_test_cult_antimicrobial_header">
					<tr>
						<th style="width: 20%;">Antimicrobial</th>
						<th style="width: 40%;">Interpretation</th>
						<th style="width: 10%;">MIC</th>
						<th style="width: 15%;">Validate</th>
						<th style="width: 15%;">Result Status</th>
					</tr>
				</thead>
			<?php
				$j=1;
				
				$cult_iso_result=0;
				$test_sample_result_count=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`result`) AS `tot` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='$iso_no' AND `result`!=''"));
				
				if($test_sample_result_count["tot"]>0)
				{
					$cult_iso_result++;
				}else{
					$testresults_count=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`result`) AS `tot` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='$iso_no'"));
					
					if($testresults_count["tot"]>0)
					{
						$cult_iso_result++;
					}
				}
				
				$cult_param_qry=mysqli_query($link,"SELECT a.*,b.Name,b.ID,b.ResultType,b.ResultOptionID FROM Testparameter a, Parameter_old b where a.TestId='$cult_testid' and b.ID=a.ParamaterId and b.ResultOptionID='68' ORDER BY b.Name"); //a.`sequence`
				while($param_info=mysqli_fetch_array($cult_param_qry))
				{
					$param_tr_show=0;
					
					$paramid=$param_info["ParamaterId"];
					
					$unit_info=mysqli_fetch_array(mysqli_query($link, "SELECT `unit_name` FROM `Units` WHERE `ID`='$param_info[UnitsID]'"));
					
					$result_text_style="";
					
					$tr_background_color="";
					
					$repeat_param_btn_display=0;
					
					$tr_background_color="";
					$instrument_name="";
					$data_entry_name="";
					$main_tech_name="";
					$approve_result="";
					$main_tech=0;
					$instrument_id=0;
					$test_result=mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_status`,`range_id`,`tech_note`,`doc_note`,`instrument_id`,`result_hide`,`doc`,`tech`,`main_tech`,`for_doc` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));
					if($test_result)
					{
						$result=$test_result["result"];
						$instrument_id=$test_result["instrument_id"];
						
						$main_tech=$test_result["main_tech"];
						$approve_result=$result;
						
						if($instrument_id>0)
						{
							$instrument_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_instrument_master` WHERE `id`='$instrument_id'"));
							$instrument_name=$instrument_info["name"];
						}else
						{
							$data_entry_user=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[tech]'"));
							$data_entry_name=$data_entry_user["name"];
						}
						
						if($test_result["main_tech"]>0)
						{
							$main_tech_user=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[main_tech]'"));
							$main_tech_name=$main_tech_user["name"];
						}
						
						if($test_result["range_status"]>0)
						{
							$result_text_style="color:red;";
						}
						
						$dlc_check_str="SELECT `slno`,`opd_id`,`ipd_id`,`batch_no`,`result`,`range_status`,`range_id` FROM `testresults` WHERE `patient_id`='$patient_id' AND `paramid`='$paramid' AND `slno`<'$test_result[slno]' ORDER BY `slno` DESC";
						
						$repeat_param_btn_display++;
					}else
					{
						$test_sample_result=mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`equip_name` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));
						if($test_sample_result["result"])
						{
							$result=$test_sample_result["result"];
							$approve_result=$result;
							
							$lab_instrument=mysqli_fetch_array(mysqli_query($link,"SELECT `id` AS `instrument_id` FROM `lab_instrument_master` WHERE `name`='$test_sample_result[equip_name]'"));
							if($lab_instrument)
							{
								$instrument_id=$lab_instrument["instrument_id"];
							}
							
							$instrument_name=$test_sample_result["equip_name"];
							
							$repeat_param_btn_display++;
						}
						else
						{
							$param_fix_result=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `param_fix_result` WHERE `testid`='$testid' AND `paramid`='$paramid'"));
							
							$result=$param_fix_result["result"];
						}
						$dlc_check_str="SELECT `slno`,`opd_id`,`ipd_id`,`batch_no`,`result`,`range_status`,`range_id` FROM `testresults` WHERE `patient_id`='$patient_id' AND `paramid`='$paramid' ORDER BY `slno` DESC";
					}
					$dlc_check_qry=mysqli_query($link, $dlc_check_str);
					$dlc_check_num=mysqli_num_rows($dlc_check_qry);
					
					if($cult_iso_result>0)
					{
						if($test_sample_result["result"])
						{
							$param_tr_show++;
						}else if($test_result["result"])
						{
							$param_tr_show++;
						}
					}else
					{
						$param_tr_show++;
					}
					
					$res=explode(" (",$result);
					$resultz=explode(")",$res[1]);
					$res[1]=$resultz[0];
					
					$approve_cls="approve_param";
					$doc_approve_disabled="";
					$approve_function="onclick=\"approve_param_each('$testid','$paramid','$iso_no','$param_info[Name]','$dept_id')\"";
					
					if($test_result["doc"]>0)
					{
						$doc_approve_disabled="disabled='disabled'";
						$approve_function="";
						$approve_cls="";
					}
					
					$td_str="td";
					if($param_info["ResultType"]==0)
					{
						$td_str="th";
					}
					$left_space_str=" &nbsp;&nbsp;&nbsp;&nbsp;";
					
					// Repeat Start
					$repeat_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `pathology_repeat_param_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$paramid' ORDER BY `repeat_id` DESC LIMIT 1"));
					
					$repeat_param_btn_data="<div style='display: inline;'>";
					if($repeat_param_btn_display>0)
					{
						$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <label id='repeat_param_label".$testid."tst".$paramid."' style='display: inline;'> <input type='checkbox' id='repeat_param".$testid."tst".$paramid."' onclick=\"repeat_param_save('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','0','$param_info[Name]','$dept_id')\" $doc_approve_disabled> Repeat </label>";
					}else
					{
						$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					if($repeat_check)
					{
						$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp; <a class='btn btn-link btn-mini' id='repeat_param_view_btn".$testid."tst".$paramid."' onclick=\"repeat_param_view('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','0')\" style='$repeat_btn_show'>View Repeat(s)</a>";
					}else
					{
						$repeat_param_btn_data.=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
					}
					$repeat_param_btn_data.="</div>";
					// Repeat End
					
					// Check result update
					$testresults_update_str="";
					$testresults_update=mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`edit_user`,`edit_date`,`edit_time` FROM `testresults_update` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no' ORDER BY `slno` DESC"));
					if($testresults_update)
					{
						$ch_user_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$testresults_update[edit_user]'"));
						$testresults_update_str="Last result: ".$testresults_update["result"].", changed by ".$ch_user_info["name"]." on ".date("d-M-Y",strtotime($testresults_update["edit_date"]))." ".date("h:i A",strtotime($testresults_update["edit_time"]));
					}
					
					$chk_box="";
					if($test_result["main_tech"]>0)
					{
						$chk_box="checked='checked'";
					}
					
					if($param_tr_show>0)
					{
		?>
				<tr style="<?php echo $tr_background_color; ?>">
					<td><?php echo $param_info["Name"]; ?></td>
					<td>
						<input type="text" class="t_par" name="t_par<?php echo $i; ?>" id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>" param_testid="<?php echo $testid; ?>" iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>" slno="<?php echo $i; ?>" value="<?php echo $res[0]; ?>" list="list<?php echo $i; ?>" <?php echo $doc_approve_disabled; ?>  style="width: 205px !important;">
						<datalist id="list<?php echo $i; ?>">
					<?php
						if($param_info["ResultOptionID"]>0)
						{
							$ResultOptions_qry=mysqli_query($link, "SELECT a.`name` FROM `Options` a, `ResultOptions` b WHERE a.`id`=b.`optionid` AND b.`id`='$param_info[ResultOptionID]'");
							while($ResultOptions=mysqli_fetch_array($ResultOptions_qry))
							{
								echo "<option>$ResultOptions[name]</option>";
							}
						}
					?>
						</datalist>
					<?php
						if($repeat_param_btn_data)
						{
							echo $repeat_param_btn_data;
						}
						
						if($dlc_check_num>0)
						{
					?>
							<a class="btn btn-link btn-mini" style="font-size:10px;" onclick="load_delta_check('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $paramid; ?>')">Delta Check</a>
					<?php
						}
					?>
						<div style="color:red;display:;font-size: 10px;" id="last_result<?php echo $i; ?>">
							<?php
								echo $testresults_update_str;
							?>
						</div>
					</td>
					<td>
					<?php
						$i++;
					?>
						<input type="text" class="t_par" name="t_par<?php echo $i; ?>" id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>_mic" param_testid="<?php echo $testid; ?>" iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>" slno="<?php echo $i; ?>" value="<?php echo $res[1]; ?>" placeholder="MIC Value" <?php echo $doc_approve_disabled; ?> style="width: 80px !important;">
					</td>
					<td>
						<label name="approve_param<?php echo $testid; ?>tst<?php echo $paramid; ?>">
							<input type="checkbox" class="<?php echo $approve_cls; ?> approve<?php echo $i; ?>" id="<?php echo $testid; ?>T<?php echo $paramid; ?>" value="<?php echo $testid; ?>TP<?php echo $paramid; ?>" <?php echo $approve_function; ?> <?php echo $doc_approve_disabled; ?> <?php echo $chk_box; ?> >
					<?php
						if($test_result["main_tech"]>0)
						{
							$tech_name=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[main_tech]'"));
							echo "Validated<br><small>(".$tech_name["name"].")</small>";
						}else
						{
							echo "Validate";
						}
					?>
						</label>
					</td>
					<td>
					<?php
						if($instrument_name)
						{
							echo "<div> Interfaced from : <b>".$instrument_name."</b></div>";
						}
						if($data_entry_name)
						{
							echo "<div>Data entry by : <b>".$data_entry_name."</b></div>";
						}
						if($main_tech_name)
						{
							echo "<div>Validated by : <b>".$main_tech_name."</b></div>";
						}
					?>
					</td>
				</tr>
		<?php
						$i++;
					}
				}
			?>
			</table>
		</div>
	</div>
<?php
}

?>
