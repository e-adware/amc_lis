<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");

if($_SESSION["levelid"]!=13) // 13 = Pathology Doctor
{
	exit();
}

$c_user=$_SESSION["emp_id"];

$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST['type'];


if($type=="load_dept_tests")
{
	$dept_id=$_POST['dept_id'];
	
	$test_str="SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='1'";
	
	echo '<option value="0">--All(Test)--</option>';
	
	if($dept_id>0)
	{
		$test_str.=" AND `type_id`='$dept_id'";
	}
	
	$test_str.=" ORDER BY `testname` ASC";
	
	$test_qry=mysqli_query($link, $test_str);
	
	while($test_info=mysqli_fetch_array($test_qry))
	{
		echo "<option value='$test_info[testid]'>$test_info[testname]</option>";
	}
}

if($type=="load_pat_list")
{
	$pat_type=$_POST['pat_type'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$dept_id=$_POST['dept_id'];
	$testid=$_POST['testid'];
	$approve_status=$_POST['approve_status'];
	
	$bill_no=$_POST['bill_no'];
	$name=$_POST['name'];
	$uhid=$_POST['uhid'];
	$barcode_id=$_POST['barcode_id'];
	
	$list_start=$_POST["list_start"];
	
	$dept_serial=$_POST["dept_serial"];
	$dept_serial_no=$_POST["dept_serial_no"];
	$patType=$_POST["patType"];
	
	if($approve_status>0)
	{
		$list_start=1000;
	}
	
	$zz=0;
	
	$test_str="SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no` FROM `phlebo_sample` a , `uhid_and_opdid` b, `testmaster` c, `patient_info` d WHERE a.`patient_id`=b.`patient_id` AND a.`patient_id`=d.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`testid`=c.`testid`";
	
	$barcode_str="SELECT DISTINCT `barcode_id` FROM `test_sample_result` WHERE `slno`>0";
	
	if($dept_id>0)
	{
		$test_str.=" AND c.`type_id`='$dept_id'";
	}
	
	if($testid>0)
	{
		$test_str.=" AND c.`testid`='$testid'";
	}
	
	if($pat_type=="opd_id")
	{
		$test_str.=" AND a.`ipd_id`=''";
	}
	if($pat_type=="ipd_id")
	{
		$test_str.=" AND a.`opd_id`=''";
	}
	
	if($bill_no!="")
	{
		$test_str.=" AND b.`cashMemoNo`='$bill_no'";
		
		$barcode_str.=" AND (`cashMemoNo`='$bill_no' OR `cashMemoNo`='$bill_no')";
		
		$zz++;
	}
	
	if($uhid!="")
	{
		$test_str.=" AND d.`uhid`='$uhid'";
		
		$barcode_str.=" AND `uhid`='$uhid'";
		
		$zz++;
	}
	
	if(strlen($name)>2)
	{
		$test_str.=" AND d.`name` LIKE '%$name%'";
		
		$zz++;
	}
	
	if($dept_serial_no)
	{
		$dept_serial_val=$dept_serial."/".$dept_serial_no;
		
		$test_str.=" AND a.`dept_serial`='$dept_serial_val'";
		
		$zz++;
	}
	
	if($patType)
	{
		$test_str.=" AND b.`receipt_no`='$patType'";
		
		$zz++;
	}
	
	if($barcode_id!="")
	{
		$test_str="SELECT DISTINCT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no` FROM `phlebo_sample` a , `test_sample_result` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`=b.`ipd_id` AND a.`testid`=b.`testid` AND b.`barcode_id`='$barcode_id'";
		
		$barcode_str="SELECT DISTINCT `barcode_id` FROM `test_sample_result` WHERE `slno`>0";
		$barcode_str.=" AND `barcode_id`='$barcode_id'";
		
		$zz++;
	}
	
	if($zz==0)
	{
		$test_str.=" AND a.`date` BETWEEN '$fdate' AND '$tdate'";
		
		$barcode_str.=" AND `date` BETWEEN '$fdate' AND '$tdate'";
	}
	
	$test_str.=" AND c.`category_id`=1";
	
	$test_str.=" GROUP BY a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`";
	
	$test_str.=" ORDER BY a.`slno` DESC LIMIT ".$list_start;
	
	$barcode_qry=mysqli_query($link, $barcode_str);
	$barcode_num=mysqli_num_rows($barcode_qry);
	
	//echo $test_str;
	//echo $barcode_str;
	
	$test_qry=mysqli_query($link, $test_str );
?>
	<table class="table table-bordered table-condensed" style="background-color:white;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Date/Time</th>
				<th>Hospital No.</th>
				<!--<th>Barcode Id</th>
				<th>Cash Memo No.</th>
				<th>Sl No.</th>-->
		<?php
			if($glob_patient_type==1)
			{
				echo "<th>Batch No</th>";
			}
		?>
		<?php
			if($barcode_num>0)
			{
				echo "<th>Barcode</th>";
			}
		?>
				<th>Name</th>
				<th>Age-Sex</th>
				<th style="width: 15%;">
					Status
					
					<div style="float:right;" id="auto_refresh_div"></div>
				</th>
			</tr>
		</thead>
<?php
	$i=1;
	while($test_det=mysqli_fetch_array($test_qry))
	{
		$patient_id=$test_det["patient_id"];
		$opd_id=$test_det["opd_id"];
		$ipd_id=$test_det["ipd_id"];
		$batch_no=$test_det["batch_no"];
		
		$pat_display=0;
		
		if($approve_status>=0)
		{
			$pat_approve_status_array=check_test_approve($patient_id,$opd_id,$ipd_id,$batch_no,0,0,$barcode_id);
			//print_r($pat_approve_status_array);
			if($pat_approve_status_array["main_tech_approve"]==0) // Validated, 0> Not Approved
			{
				$pat_display=1; // No Display
			}
			if($approve_status==2 && $pat_approve_status_array["not_approve"]==0) // Not Validated, 0==Approved
			{
				$pat_display=1; // No Display
			}
			if($approve_status==1 && $pat_approve_status_array["approve"]==0) // Validated, 0> Not Approved
			{
				$pat_display=1; // No Display
			}
		}
		
		if($pat_display==0)
		{
			$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND (`opd_id`='$opd_id' OR `opd_id`='$ipd_id') "));
			
			$urgent_patient=0;
			
			if($opd_id!="" && $ipd_id=="")
			{
				$test_date=$pat_reg["date"];
				$test_time=$pat_reg["time"];
				
				$urgent_patient=$pat_reg["urgent"];
			}else
			{
				$test_ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_test_ref_doc` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' "));
				
				$test_date=$test_ref_doc["date"];
				$test_time=$test_ref_doc["time"];
				
				$urgent_patient=$test_ref_doc["urgent"];
			}
			
			$urgent_patient_img="";
			if($urgent_patient)
			{
				$urgent_patient_img="<img src='../images/blinking_dot.gif' style='width:10px;' />";
			}
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `hosp_no`,`name`,`sex`,`dob`,`age`,`age_type`,`phone` FROM `patient_info` WHERE `patient_id`='$patient_id' "));
			
			//$reg_date=$pat_reg["date"];
			$reg_date=$test_date;
			
			if($pat_info["dob"]!="" && $pat_info["dob"]!="0000-00-00"){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			
			$chk_flag=0;
			if($dept_id>0)
			{
				$chk_flag=mysqli_num_rows(mysqli_query($link,"select * from patient_flagged_details where patient_id='$patient_id' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and dept_id='$dept_id'"));
			}
			$sl=mysqli_fetch_array(mysqli_query($link,"SELECT `dept_serial` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_serial`!='' LIMIT 0,1"));
			
			$flag_class="";
			if($chk_flag>0)
			{
				$flag_class="flagged";
			}
			
			$tr_id=str_replace("/","",$pat_reg["opd_id"]).$batch_no;
			
			$printClass="";
			if($dept_id)
			{
				$checkPrint=mysqli_fetch_array(mysqli_query($link,"SELECT `slno` FROM `testreport_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_id`='$dept_id'"));
				if($checkPrint)
				{
					$printClass="printed";
				}
			}
			
			$repClass=0;
			$repCount=mysqli_query($link,"SELECT DISTINCT a.`paramid` FROM `pathology_repeat_param_details` a, `test_sample_result` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`=b.`ipd_id` AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND a.`paramid`=b.`paramid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND a.`date` BETWEEN '$days_ago' AND '$date' AND b.`result`=''");
			while($repPar=mysqli_fetch_array($repCount))
			{
				$repCountRs=mysqli_fetch_array(mysqli_query($link,"SELECT `slno` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `paramid`='$repPar[paramid]'"));
				if(!$repCountRs || ($repCountRs && $repCountRs['result']!=''))
				{
					$repClass++;
				}
			}
			$repRow="";
			if($repClass>0)
			{
				$repRow="<span class='text-right repeaters'><img src='../images/rep.png' title='repeat' alt='' style='width:25px;' /></i></span>";
			}
?>
		<tr class="<?php echo $flag_class;?> <?php echo $printClass;?>" id="<?php echo $tr_id;?>">
			<td><?php echo $i;?></td>
			<td>
				<?php echo date("d-m-Y",strtotime($test_date))."/".date("h:i:s A",strtotime($test_time));?>
				<span style="float:right;"><?php echo $urgent_patient_img;?></span>
			</td>
			<td><?php echo $pat_info['hosp_no'];?></td>
			<td style="display:none;">
				<?php echo $opd_id;?>
				<input type="hidden" id="pid_<?php echo $i;?>" value="<?php echo $patient_id;?>"/>
				<input type="hidden" id="opd_<?php echo $i;?>" value="<?php echo $opd_id;?>"/>
				<input type="hidden" id="ipd_<?php echo $i;?>" value="<?php echo $ipd_id;?>"/>
				<input type="hidden" id="batch_<?php echo $i;?>" value="<?php echo $batch_no;?>"/>
				
			</td>
			<!--<td><?php echo $pat_reg['cashMemoNo'];?></td>
			<td><?php echo $sl['dept_serial'];?></td>-->
		<?php
			if($glob_patient_type==1)
			{
				echo "<td>$batch_no</td>";
			}
			
		$repeatCounts=mysqli_fetch_array(mysqli_query($link,"SELECT DISTINCT COUNT(`repeat_id`) AS `total` FROM `pathology_repeat_param_details` a, `test_sample_result` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`=b.`ipd_id` AND a.`batch_no`=b.`batch_no` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND `paramid` NOT IN (639,640,641) AND b.`result`=''"));
		$noResultCount=mysqli_fetch_array(mysqli_query($link,"SELECT DISTINCT COUNT(`paramid`) AS `total` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `paramid` NOT IN (639,640,641) AND (`result`='No Result' OR `result`='NO RESULT')"));
			if($barcode_num>0)
			{
				echo "<td>";
				$each_barcode_str="SELECT DISTINCT a.`barcode_id` FROM `test_sample_result` a, `testmaster` c WHERE a.`testid`=c.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no'";
				if($dept_id>0)
				{
					$each_barcode_str.=" AND c.`type_id`='$dept_id'";
				}
				if($testid>0)
				{
					$each_barcode_str.=" AND c.`testid`='$testid'";
				}
				if($barcode_id!="")
				{
					$each_barcode_str.=" AND a.`barcode_id`='$barcode_id'";
				}
				$each_barcode_qry=mysqli_query($link, $each_barcode_str);
				while($barcode_data=mysqli_fetch_array($each_barcode_qry))
				{
					echo "<span style='background-color:#ddd;'>".$barcode_data["barcode_id"]."</span> ";
				}
				if($repeatCounts['total']>0)
				{
					echo "<span class='label label-warning' style='float:right;border-radius:5px;' title='Repeat'>".$repeatCounts['total']."</span>";
				}
				if($noResultCount['total']>0)
				{
					echo "<span class='label label-inverse' style='float:right;border-radius:5px;' title='No Result'>".$noResultCount['total']."</span>";
				}
				echo $repRow;
				echo "</td>";
			}
		?>
			<td><?php echo $pat_info['name'].$repRow;?></td>
			<td><?php echo $age." , ".$pat_info['sex'];?></td>
			<td>
				<div class="">
				<?php
					$j=1;
					$cls="";
					
					$dept_str="SELECT DISTINCT b.`type_id` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`category_id`=1 AND b.`type_id` NOT IN($non_reporting_test_dept_id)";
					
					if($dept_id>0)
					{
						$dept_str.=" AND b.`type_id`='$dept_id'";
					}
					
					if($testid>0)
					{
						$dept_str.=" AND b.`testid`='$testid'";
					}
					
					if($barcode_id!="")
					{
						$dept_str.=" AND b.`testid` IN(SELECT DISTINCT `testid` FROM `test_sample_result` WHERE `barcode_id`='$barcode_id')";
					}
					
					$dept_qry=mysqli_query($link,$dept_str);
					
					while($dept=mysqli_fetch_array($dept_qry))
					{
						$type_id=$dept["type_id"];
						
						$pat_dept_display=0;
						
						if($approve_status>=0)
						{
							$pat_approve_status_array=check_test_approve($patient_id,$opd_id,$ipd_id,$batch_no,$type_id,$testid,$barcode_id);
							//print_r($pat_approve_status_array);
							if($approve_status==2 && $pat_approve_status_array["not_approve"]==0) // Not Validated, 0==Approved
							{
								$pat_dept_display=1; // No Display
							}
							if($approve_status==1 && $pat_approve_status_array["approve"]==0) // Validated, 0> Not Approved
							{
								$pat_dept_display=1; // No Display
							}
							if($pat_approve_status_array["main_tech_approve"]==0) // Validated, 0> Not Approved
							{
								$pat_dept_display=1; // No Display
							}
						}
						
						if($pat_dept_display==0)
						{
							$cls="";
							$dept_info=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$type_id'"));
							
							// No Main Tech Approve
							$non_approve_testresults=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(a.slno) AS `tot` FROM `testresults` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND a.`main_tech`=0 AND a.`doc`=0"));
							
							$non_approve_summary=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(a.slno) AS `tot` FROM `patient_test_summary` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND a.`main_tech`=0 AND a.`doc`=0"));
							
							$non_approve_widal["tot"]=0;
							if($type_id==32) // Serology
							{
								$non_approve_widal=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(DISTINCT a.`testid`) AS `tot` FROM `widalresult` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND a.`main_tech`=0 AND a.`doc`=0 LIMIT 1"));
							}
							
							//$tot_lis_n=mysqli_fetch_array(mysqli_query($link,"SELECT count(*) as tot FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id'  AND `batch_no`='$batch_no'  AND `result`!='' AND `testid` IN (SELECT `testid` FROM `testmaster` WHERE `type_id`='$type_id') AND `paramid` NOT IN (SELECT `paramid` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id'  AND `batch_no`='$batch_no' )"));
							
							$lis_result_not_in_testresults=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) as tot FROM `test_sample_result` tsr JOIN `testmaster` tm ON tsr.testid = tm.testid LEFT JOIN `testresults` tr ON tsr.patient_id = tr.patient_id AND tsr.opd_id = tr.opd_id AND tsr.ipd_id = tr.ipd_id AND tsr.batch_no = tr.batch_no AND tsr.paramid = tr.paramid WHERE tsr.patient_id = '$patient_id' AND tsr.opd_id = '$opd_id' AND tsr.ipd_id = '$ipd_id' AND tsr.batch_no = '$batch_no' AND tsr.result != '' AND tm.type_id = '$type_id' AND tr.paramid IS NULL AND tsr.paramid NOT IN(639,640,641)"));
							
							// Main Tech Approve
							$approved_testresults=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(a.slno) AS `tot` FROM `testresults` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND (a.`main_tech`>0 OR a.`doc`>0)"));
							
							$approved_summary=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(a.slno) AS `tot` FROM `patient_test_summary` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND (a.`main_tech`>0 OR a.`doc`>0)"));
							
							$approved_widal["tot"]=0;
							if($type_id==32) // Serology
							{
								$approved_widal=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(DISTINCT a.`testid`) AS `tot` FROM `widalresult` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND (a.`main_tech`>0 OR a.`doc`>0) LIMIT 1"));
							}
							
							$tot_non_approved_chk=$non_approve_testresults["tot"]+$non_approve_summary["tot"]+$non_approve_widal["tot"];
							$tot_approved_chk=$approved_testresults["tot"]+$approved_summary["tot"]+$approved_widal["tot"];
							
							$cls="btn btn-success btn-mini";
							
							if($tot_approved_chk==0)
							{
								$cls="btn btn-danger btn-mini";
							}
							if($tot_non_approved_chk>0 || $lis_result_not_in_testresults["tot"]>0)
							{
								$cls="btn btn-warning btn-mini";
							}
							
							//~ $chk_canc=mysqli_num_rows(mysqli_query($link,"SELECT ptd.* FROM `patient_test_details` ptd JOIN `testmaster` tm ON ptd.testid = tm.testid WHERE ptd.patient_id = '$patient_id' AND ptd.opd_id = '$opd_id' AND ptd.ipd_id = '$ipd_id' AND ptd.batch_no = '$batch_no' AND tm.type_id = '$type_id'"));
							//~ if($chk_canc==0)
							//~ {
								//~ $cls="btn btn-default btn-mini";
							//~ }
							
							$dept_param_str="SELECT COUNT(DISTINCT a.`ParamaterId`) AS `tot_param` FROM `Testparameter` a, `testmaster` b , `patient_test_details` c , `Parameter_old` d WHERE a.`TestId`=b.`testid` AND c.`TestId`=b.`testid` AND  a.`status`=0 AND a.`ParamaterId`=d.`ID` AND d.`ResultType` NOT IN(0,5) AND c.patient_id = '$patient_id' AND c.opd_id = '$opd_id' AND c.ipd_id = '$ipd_id' AND c.batch_no = '$batch_no' AND b.type_id = '$type_id' AND a.`ParamaterId` NOT IN(639,640,641)";
							
							$dept_param_count=mysqli_fetch_array(mysqli_query($link, $dept_param_str));
							$dept_tot_param=$dept_param_count["tot_param"];
							
							if($tot_approved_chk>0 && $dept_tot_param>$tot_approved_chk)
							{
								$cls="btn btn-primary btn-mini";
							}
							if($dept_tot_param==$tot_approved_chk)
							{
								$cls="btn btn-success btn-mini";
							}
							
							$btnDisabled="";
							$flagCheck=mysqli_fetch_array(mysqli_query($link,"SELECT `slno` FROM `patient_flagged_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_id`='$type_id'"));
							if($flagCheck)
							{
								$btnDisabled="disabled";
								$dept_info["name"]="<i class='icon-flag icon-large'></i> &nbsp; Flagged";
								$cls="btn btn-inverse btn-mini";
							}
						?>
							<button id="dep_<?php echo $type_id;?>" class="<?php echo $cls;?>" onclick="load_pat_dept_tests('<?php echo $i;?>','<?php echo $type_id;?>')" <?php echo $btnDisabled;?>><?php echo $dept_info["name"];?></button>
						<?php
						}
					}
				?>
				</div>
			</td>
			
		</tr>
<?php
			$i++;
		}
	}
?>
	</table>
<?php
}

if($type=="load_pat_dept_tests")
{
	$patient_id=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$ipd_id=$_POST["ipd_id"];
	$batch_no=$_POST["batch_no"];
	$sel_dept_id=$_POST["dept_id"];
	$fdoc=$_POST["fdoc"];
	$search_dept_id=$_POST["search_dept_id"];
	$search_testid=$_POST["search_testid"];
	$barcode_id=$_POST["barcode_id"];
	
	include("pathology_normal_range_new.php");
	
	$dept_str="SELECT `id`,`name` FROM `test_department` WHERE `id` IN(SELECT DISTINCT a.`type_id` FROM `testmaster` a, `patient_test_details` b WHERE a.testid=b.testid AND a.category_id=1 AND b.patient_id='$patient_id' AND b.opd_id='$opd_id' AND b.ipd_id='$ipd_id' AND b.batch_no='$batch_no')";
	
	if($search_testid>0)
	{
		$dept_str="SELECT `id`,`name` FROM `test_department` WHERE `id` IN(SELECT DISTINCT a.`type_id` FROM `testmaster` a, `patient_test_details` b WHERE a.testid=b.testid AND a.category_id=1 AND b.patient_id='$patient_id' AND b.opd_id='$opd_id' AND b.ipd_id='$ipd_id' AND b.batch_no='$batch_no' AND b.testid='$search_testid')";
	}
	
	if($search_dept_id>0)
	{
		$dept_str.=" AND `id`='$search_dept_id'";
	}
	$dept_str.=" AND `id` NOT IN($non_reporting_test_dept_id)";
	//$dept_str="SELECT `id`,`name` FROM `test_department` WHERE `id`='$sel_dept_id'";
	//echo $dept_str;
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND (`opd_id`='$opd_id' or `opd_id`='$ipd_id') "));
	$bill_no=$pat_reg["opd_id"];
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `hosp_no`,`name`,`sex`,`dob`,`age`,`age_type`,`phone` FROM `patient_info` WHERE `patient_id`='$patient_id' "));
	
	if($opd_id!="" && $ipd_id=="")
	{
		$test_date=$pat_reg["date"];
		$test_time=$pat_reg["time"];
		
		$urgent_patient=$pat_reg["urgent"];
		$refbydoctorid=$pat_reg["refbydoctorid"];
	}else
	{
		$test_ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_test_ref_doc` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' "));
		
		$test_date=$test_ref_doc["date"];
		$test_time=$test_ref_doc["time"];
		
		$urgent_patient=$test_ref_doc["urgent"];
		$refbydoctorid=$test_ref_doc["refbydoctorid"];
	}
	
	$reg_date=$test_date;
	
	if($pat_info["dob"]!="" && $pat_info["dob"]!="0000-00-00"){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
	
	$urgent_patient_img="";
	if($urgent_patient)
	{
		$urgent_patient_img="<img src='../images/blinking_dot.gif' style='width:10px;' />";
	}
	
	$ref_doc=mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$refbydoctorid'"));
	
	$sample_receive_users=array();
	
	$sample_user_qry=mysqli_query($link, "SELECT DISTINCT b.`user` FROM `testmaster` a, `phlebo_sample` b WHERE a.testid=b.testid AND a.category_id=1 AND b.patient_id='$patient_id' AND b.opd_id='$opd_id' AND b.ipd_id='$ipd_id' AND b.batch_no='$batch_no'");
	while($sample_user=mysqli_fetch_array($sample_user_qry))
	{
		$user_name=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$sample_user[user]'"));
		
		$sample_receive_users[]=$user_name["name"];
	}
	$sample_receive_user=implode(",",$sample_receive_users);
	$sl=mysqli_fetch_array(mysqli_query($link,"SELECT `dept_serial` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_serial`!='' LIMIT 0,1"));
	
	$barcodes="";
	$each_barcode_qry=mysqli_query($link,"SELECT DISTINCT a.`barcode_id` FROM `test_sample_result` a, `testmaster` c WHERE a.`testid`=c.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no'");
	while($bCode=mysqli_fetch_array($each_barcode_qry))
	{
		if($barcodes)
		{
			$barcodes.=",".$bCode['barcode_id'];
		}
		else
		{
			$barcodes=$bCode['barcode_id'];
		}
	}
?>
<div id="pat_test_data">
	<div class="pat_info">
		<table class="table table-condensed table-bordered" style="background-color: white;">
			<tr>
				<th>Hospital No.</th>
				<th>Barcode No</th>
				<!--<th>Cash Memo No</th>-->
				<!--<th>Sl No</th>-->
		<?php
			if($glob_patient_type==1)
			{
				echo "<th>Batch No.</th>";
			}
		?>
				<th>Patient Name</th>
				<th>Age-Sex</th>
				<th>Test Time</th>
				<th>Sample Received By</th>
				<th>
					<!--Ref. Doctor-->
					
					<button class="btn btn-back btn-mini" onclick="back_to_list()" style="float:right;"><i class="icon-backward"></i> Back To List (ESC)</button>
				</th>
			</tr>
			<tr>
				<td><?php echo $pat_info['hosp_no']; ?></td>
				<td>
					<?php echo $barcodes; ?>
					<span style="float:right;"><?php echo $urgent_patient_img;?></span>
				</td>
				<!--<td><?php echo $pat_reg['cashMemoNo'];?></td>-->
				<!--<td><?php echo $sl['dept_serial'];?></td>-->
			<?php
				if($glob_patient_type==1)
				{
					echo "<td>$batch_no</td>";
				}
			?>
				<td>
					<?php echo $pat_info["name"]; ?>
					<?php
						if($pat_info["phone"])
						{
							echo "<div><button class='btn btn-link btn-mini' onclick='pat_phone_display()'><i class='icon-phone'></i></button><span id='pat_phone_display' style='display:none;color:black;'> &nbsp;&nbsp;".$pat_info["phone"]."</span></div>";
						}
					?>
				</td>
				<td><?php echo $age."-".$pat_info["sex"]; ?></td>
				<td><?php echo date("d-m-Y",strtotime($test_date))."/".date("h:i:s A",strtotime($test_time));?></td>
				<td><?php echo $sample_receive_user; ?></td>
				<td>
					<?php //echo $ref_doc["ref_name"]; ?>
				</td>
			</tr>
		</table>
	</div>
	<div>
		<b>Hot Key :</b> 
		<span style="background-color: white;font-weight: bold;color: red;border: 1px solid white;border-radius: 10px;padding: 3px;">CTRL+ENTER</span> To <b>Validate</b> 
		<span style="background-color: white;font-weight: bold;color: red;border: 1px solid white;border-radius: 10px;padding: 3px;">ESC</span> To <b>Back</b>
	</div>
	<div class="widget-box">
		<div class="widget-title">
			<ul class="nav nav-tabs">
		<?php
			$tab=1;
			$dept_qry=mysqli_query($link,$dept_str);
			while($dept_info=mysqli_fetch_array($dept_qry))
			{
				$active_cls="";
				$dept_id=$dept_info["id"];
				if($sel_dept_id>0)
				{
					if($sel_dept_id==$dept_id)
					{
						$active_cls="active";
					}
				}
				else
				{
					if($tab==1)
					{
						$active_cls="active";
					}
				}
		?>
				<li class="li_cls <?php echo $active_cls; ?>" id="li_dept_<?php echo $dept_id; ?>" onclick="load_pat_dept_tests_refresh('<?php echo $dept_id; ?>')" title="Click to Re-Fresh"><a data-toggle="tab" href="#tab<?php echo $tab; ?>"><?php echo $dept_info["name"]; ?></a></li>
		<?php
				$tab++;
			}
		?>
			</ul>
		</div>
		<div class="widget-content tab-content" style="background-color: white;">
	<?php
		$tab=1;
		$dept_qry=mysqli_query($link,$dept_str);
		while($dept_info=mysqli_fetch_array($dept_qry))
		{
			$active_cls="";
			$dept_id=$dept_info["id"];
			if($sel_dept_id>0)
			{
				if($sel_dept_id==$dept_id)
				{
					$active_cls="active";
				}
			}
			else
			{
				if($tab==1)
				{
					$active_cls="active";
				}
			}
	?>
			<div id="tab<?php echo $tab; ?>" class="tab-pane <?php echo $active_cls; ?>">
			<?php
				if($sel_dept_id==$dept_id)
				{
					include("pathology_result_approve_doc_load_param.php");
				}
			?>
			</div>
	<?php
			$tab++;
		}
	?>
		</div>
	</div>
	<input type="hidden" id="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" id="opd_id" value="<?php echo $opd_id; ?>">
	<input type="hidden" id="ipd_id" value="<?php echo $ipd_id; ?>">
	<input type="hidden" id="batch_no" value="<?php echo $batch_no; ?>">
	<input type="hidden" id="approve_val" value="0">
	<script>
		function check_form(testid,paramid, form, dec) {
			var sqr_chk = 0;
			var form = form.split("@");
			var fr = "";
			var non_zero=0;
			for (var i = 0; i < form.length; i++) {
				var chk = form[i].split("p");
				if (chk[1] > 0) {
					if ($("#"+testid+"PRM"+chk[1]).length > 0) {
						//fr += $("#"+testid+"PRM"+chk[1]).val();
						
						var val = parseFloat($("#"+testid+"PRM"+chk[1]).val());
						if(!val){ val=0; }
						
						fr += val;
						
						if(val>0)
						{
							non_zero++;
						}
					} else {
						break;
					}
				} else {
					if (form[i] == "sqr_root") {
						fr += "Math.sqrt(";
						var sqr_chk = 1;
					} else {
						fr += form[i];
						if (sqr_chk == 1) {
							fr += ")";
							sqr_chk = 0;
						}
					}
				}
			}
			
			var res = eval(fr).toFixed(dec);
			
			if(!res || res=="NaN" || non_zero==0)
			{
				res="";
			}
			
			$("#"+testid+"PRM"+ paramid+"").val(res);
			
			setTimeout(function(){
				check_normal_range(testid,paramid,res);
			},100);
		}
		function check_normal_range(testid,paramid,res)
		{
			var instrument_id	=$("#"+testid+"PRM"+paramid).attr("instrument_id");
			
			$("#loader").show();
			$.post("pages/pathology_result_approve_doc_ajax.php",
			{
				type:"check_normal_range",
				patient_id:$("#patient_id").val(),
				testid:testid,
				paramid:paramid,
				result:res,
				instrument_id:instrument_id,
			},
			function(data,status)
			{
				$("#loader").hide();
				if(data==1)
				{
					$("#"+testid+"PRM"+paramid).css({"color":"red"});
				}else
				{
					$("#"+testid+"PRM"+paramid).css({"color":"black"});
				}
			})
		}
		// Pad
		function load_editor(slno) {
			$("#padd_display_div"+slno).slideUp(400);
			$("#padd_edit_div"+slno).slideDown(600);
			
			add_pad_param(slno);
		}
		function back_editor(slno) {
			$("#padd_edit_div"+slno).slideUp(400);
			$("#padd_display_div"+slno).slideDown(600);
		}
		function add_pad_param(slno) {
			//alert(slno);
			if (CKEDITOR.instances['t_par'+slno]) {
				CKEDITOR.instances['t_par'+slno].destroy(true);
			}
			CKEDITOR.replace('t_par'+slno);
			CKEDITOR.config.height = 300;
			
			// Remove text formatting while pasting
			CKEDITOR.config.extraPlugins = (CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins + ',pastefromword' : 'pastefromword');
			CKEDITOR.config.pasteFromWordRemoveFontStyles = true;
			CKEDITOR.config.pasteFromWordRemoveStyles = true;

			// Optionally remove inline styles and classes
			CKEDITOR.config.forcePasteAsPlainText = true;

			CKEDITOR.config.removeFormatTags = 'b,strong,em,i,font,u,strike,sub,sup';
			CKEDITOR.config.removeFormatAttributes = 'style,span,class,width,height,align,hspace,valign';

			CKEDITOR.on('instanceReady', function (evt) {
				evt.editor.on('paste', function (event) {
					var data = event.data.dataValue;
					// Remove all HTML tags except <br>
					data = data.replace(/<(?!br\s*\/?)[^>]+>/gi, '');
					event.data.dataValue = data;
				});
			});
			
			$(".modal-dialog").animate({
				width: '100%'
			}, "slow", function() {
				$("#rad_res").contents().find('body').focus()
			});
		}
		// Summary
		function load_summary_editor(testid) {
			$("#summary_display_div"+testid).slideUp(400);
			$("#summary_edit_div"+testid).slideDown(600);
			
			add_summary_param(testid);
		}
		function back_summary_editor(testid) {
			$("#summary_edit_div"+testid).slideUp(400);
			$("#summary_display_div"+testid).slideDown(600);
		}
		function add_summary_param(testid) {
			//alert(testid);
			if (CKEDITOR.instances['test_summ'+testid]) {
				CKEDITOR.instances['test_summ'+testid].destroy(true);
			}
			CKEDITOR.replace('test_summ'+testid);
			CKEDITOR.config.height = 300;
			
			// Remove text formatting while pasting
			CKEDITOR.config.extraPlugins = (CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins + ',pastefromword' : 'pastefromword');
			CKEDITOR.config.pasteFromWordRemoveFontStyles = true;
			CKEDITOR.config.pasteFromWordRemoveStyles = true;

			// Optionally remove inline styles and classes
			CKEDITOR.config.forcePasteAsPlainText = true;

			CKEDITOR.config.removeFormatTags = 'b,strong,em,i,font,u,strike,sub,sup';
			CKEDITOR.config.removeFormatAttributes = 'style,span,class,width,height,align,hspace,valign';

			CKEDITOR.on('instanceReady', function (evt) {
				evt.editor.on('paste', function (event) {
					var data = event.data.dataValue;
					// Remove all HTML tags except <br>
					data = data.replace(/<(?!br\s*\/?)[^>]+>/gi, '');
					event.data.dataValue = data;
				});
			});
			
			$(".modal-dialog").animate({
				width: '100%'
			}, "slow", function() {
				$("#rad_res").contents().find('body').focus()
			});
		}
		
		// Repeat
		function repeat_param_save(uhid,opd_id,ipd_id,batch_no,testid,paramid,iso_no,param_name,dept_id)
		{
			bootbox.dialog({
				message: "<h5>Are you sure want to repeat "+param_name+" parameter ?</h5>",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> No',
						className: "btn btn-inverse",
						callback: function() {
							$("#repeat_param"+testid+"tst"+paramid).prop("checked", false);
							bootbox.hideAll();
						}
					},
					confirm: {
						label: '<i class="icon-ok"></i> Yes',
						className: "btn btn-danger",
						callback: function() {
							repeat_param_save_reason(uhid,opd_id,ipd_id,batch_no,testid,paramid,iso_no,param_name);
						}
					}
				}
			});
		}
		function repeat_param_save_reason(uhid,opd_id,ipd_id,batch_no,testid,paramid,iso_no,param_name)
		{
			bootbox.dialog({
				message: "Reason:<input type='text' id='repeat_reason' autofocus />",
				title: "Parameter Repeat Reason",
				buttons: {
					main: {
						label: "Save",
						className: "btn-primary",
						callback: function() {
						if($("#repeat_reason").val()!="")
						{
							$.post("pages/pathology_reporting_repeat_param_tech.php",
							{
								type:"repeat_parameter_save",
								uhid:uhid,
								opd_id:opd_id,
								ipd_id:ipd_id,
								batch_no:batch_no,
								testid:testid,
								paramid:paramid,
								iso_no:iso_no,
								repeat_reason:$("#repeat_reason").val(),
							},
							function(data,status)
							{
								//alert(data);
								var res=data.split("@$@");
								if(res[0]=="404")
								{
									alertmsg(res[1], 0);
									
									$("#repeat_param"+testid+"tst"+paramid).prop("checked", false);
								}else
								{
									alertmsg(res[1], 1);
									$("#repeat_param_label"+testid+"tst"+paramid).hide();
									$("#repeat_param_view_btn"+testid+"tst"+paramid).show();
								}
								
								load_pat_dept_tests_refresh($("#sel_dept_id").val());
							})
						}else
						{
							$("#repeat_param"+testid+"tst"+paramid).prop("checked", false);
							bootbox.alert("Reason cannot blank");
						}
						
					  }
					}
				}
			});
		}
		// Repeat Test
		function repeat_test_save(uhid,opd_id,ipd_id,batch_no,testid,iso_no,test_name,dept_id)
		{
			bootbox.dialog({
				message: "<h5>Are you sure want to repeat "+test_name+" ?</h5>",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> No',
						className: "btn btn-inverse",
						callback: function() {
							$("#repeat_test"+testid).prop("checked", false);
							bootbox.hideAll();
						}
					},
					confirm: {
						label: '<i class="icon-ok"></i> Yes',
						className: "btn btn-danger",
						callback: function() {
							repeat_test_save_reason(uhid,opd_id,ipd_id,batch_no,testid,iso_no,test_name,dept_id);
						}
					}
				}
			});
		}
		function repeat_test_save_reason(uhid,opd_id,ipd_id,batch_no,testid,iso_no,test_name,dept_id)
		{
			bootbox.dialog({
				message: "Reason:<input type='text' id='test_repeat_reason' autofocus />",
				title: "Test Repeat Reason",
				buttons: {
					main: {
						label: "Save",
						className: "btn-primary",
						callback: function() {
						if($("#test_repeat_reason").val()!="")
						{
							$.post("pages/pathology_reporting_repeat_param_tech.php",
							{
								type:"repeat_test_save",
								uhid:uhid,
								opd_id:opd_id,
								ipd_id:ipd_id,
								batch_no:batch_no,
								testid:testid,
								iso_no:iso_no,
								repeat_reason:$("#test_repeat_reason").val(),
							},
							function(data,status)
							{
								//alert(data);
								var res=data.split("@$@");
								if(res[0]=="404")
								{
									alertmsg(res[1], 0);
									
									$("#repeat_test"+testid).prop("checked", false);
								}else
								{
									alertmsg(res[1], 1);
									$("#repeat_test_label"+testid).hide();
								}
								
								load_pat_dept_tests_refresh($("#sel_dept_id").val());
							})
						}else
						{
							$("#repeat_test"+testid).prop("checked", false);
							bootbox.alert("Reason cannot blank");
						}
						
					  }
					}
				}
			});
		}
		function repeat_param_view(uhid,opd_id,ipd_id,batch_no,testid,paramid,iso_no)
		{
			$.post("pages/pathology_reporting_repeat_param_tech.php",
			{
				type:"repeat_param_view",
				uhid:uhid,
				opd_id:opd_id,
				ipd_id:ipd_id,
				batch_no:batch_no,
				testid:testid,
				paramid:paramid,
				iso_no:iso_no,
			},
			function(data,status)
			{
				$("#result_repeat").html(data);
				$("#modal_btn_repeat").click();
				
			})
		}
		
		// Approve
		function approve_data()
		{
			$("#myModal_repeat").modal("hide");
			
			var Widaldata = 0;
			if($(".widal_result :visible").length>0)
			{
				Widaldata = 1;
			}
			
			var test_summary = 0;
			if($(".approve_test_summary:visible").length>0)
			{
				test_summary = 1;
			}
			
			//var param=$(".approve_param:not(:checked)");
			var param=$(".approve_param:visible");
			if(param.length==0 && Widaldata==0 && test_summary==0)
			{
				alertmsg("Nothing to approve", 0);
				return false;
			}
			
			// Culture ISO No. Focus
			var iso_no_cls=$(".active.iso_no_cls:visible");
			if(iso_no_cls.length)
			{
				var sel_iso=iso_no_cls.attr("id").split("iso_li");
				var testid=sel_iso[0];
				var iso_no=sel_iso[1];
				sessionStorage.setItem('testid', testid);
				sessionStorage.setItem('iso_no', iso_no);
			}
			
			var dataArray = [];
			var paramid_1_non_result="";
			for(var i=0;i<param.length;i++)
			{
				var testparam_ids	=param[i].value;
				var testparam		=testparam_ids.split("TP");
				var testid			=testparam[0];
				var paramid			=testparam[1];
				var iso_no			=$("#"+testid+"PRM"+paramid).attr("iso_no");
				var instrument_id	=$("#"+testid+"PRM"+paramid).attr("instrument_id");
				var result			=$("#"+testid+"PRM"+paramid).val().trim();
				var padd			=$("#"+testid+"PRM"+paramid).attr("padd");
				if(!padd){ padd=0; }
				
				var iso_no_total=0;
				
				//if(result)
				if(1==1)
				{
					if($("#iso_no_total"+testid+":visible").length>0)
					{
						iso_no_total=$("#iso_no_total"+testid).val();
					}
					if(!iso_no_total){ iso_no_total=0; }
					
					if($("#"+testid+"PRM"+paramid+"_mic:visible").length>0)
					{
						var result_mic=$("#"+testid+"PRM"+paramid+"_mic").val().trim();
						result=result+" ("+result_mic+")";
					}
					
					var dontPrint=$("#dontPrint_param"+testid+"tst"+paramid+":checked").length;
					
					dataArray.push({testid: testid, paramid: paramid, iso_no: iso_no, instrument_id: instrument_id, result: result, dontPrint: dontPrint, padd: padd, iso_no_total: iso_no_total});
				}else if(paramid_1_non_result=="")
				{
					paramid_1_non_result=testid+"PRM"+paramid;
				}
			}
			
			if(dataArray.length==0 && Widaldata==0 && test_summary==0)
			{
				alertmsg("Enter result", 0);
				$("#"+paramid_1_non_result).focus();
				return false;
			}
			
			var approve_data = JSON.stringify(dataArray);
			
			if($("#approve_val").val()==1) // Approve data sent to server
			{
				load_pat_dept_tests_refresh($("#sel_dept_id").val());
				return false;
			}
			approve_param_save(approve_data,Widaldata,test_summary,1);
			/*bootbox.hideAll();
			bootbox.dialog({
				message: "<h5>Are you sure approve all result(s) ?</h5>",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> No (ESC)',
						className: "btn btn-inverse",
						callback: function() {
							bootbox.hideAll();
						}
					},
					confirm: {
						label: '<i class="icon-ok"></i> Yes (Enter)',
						className: "btn btn-primary",
						callback: function() {
							approve_param_save(approve_data,Widaldata,test_summary,1);
						}
					}
				}
			});*/
		}
		function approve_param_each(testid,paramid,iso_no,param_name,dept_id)
		{
			var test_summary = 0;
			var Widaldata = 0;
			var val=0;
			if($("#approve_val").val()==1) // Approve data sent to server
			{
				load_pat_dept_tests_refresh($("#sel_dept_id").val());
				return false;
			}
			if($("#"+testid+"T"+paramid+":checked").length==1)
			{
				var result=$("#"+testid+"PRM"+paramid).val().trim();
				if(result=="")
				{
					alertmsg("Enter result", 0);
					$("#"+testid+"PRM"+paramid).focus();
					$("#"+testid+"T"+paramid).prop("checked",false);
					return false;
				}
				var msg="Are you sure want to approve "+param_name+" ?";
				
				val=1;
			}else
			{
				var msg="Are you sure want to unapprove "+param_name+" ?";
				
				val=0;
			}
			
			var dataArray = [];
			
			var instrument_id=$("#"+testid+"PRM"+paramid).attr("instrument_id");
			var result=$("#"+testid+"PRM"+paramid).val().trim();
			var padd=$("#"+testid+"PRM"+paramid).attr("padd");
			if(!padd){ padd=0; }
			
			var iso_no_total=0;
			
			if($("#iso_no_total"+testid+":visible").length>0)
			{
				iso_no_total=$("#iso_no_total"+testid).val();
			}
			if(!iso_no_total){ iso_no_total=0; }
			
			if($("#"+testid+"PRM"+paramid+"_mic:visible").length>0)
			{
				var result_mic=$("#"+testid+"PRM"+paramid+"_mic").val().trim();
				result=result+" ("+result_mic+")";
			}
			
			var dontPrint=$("#dontPrint_param"+testid+"tst"+paramid+":checked").length;
			
			dataArray.push({testid: testid, paramid: paramid, iso_no: iso_no, instrument_id: instrument_id, result: result, dontPrint: dontPrint, padd: padd, iso_no_total: iso_no_total});
			
			var approve_data = JSON.stringify(dataArray);
			approve_param_save(approve_data,Widaldata,test_summary,val);
			
			sessionStorage.setItem('testid', testid);
			sessionStorage.setItem('iso_no', iso_no);
			
			/*bootbox.dialog({
				message: "<h5>"+msg+"</h5>",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> No (ESC)',
						className: "btn btn-inverse",
						callback: function() {
							bootbox.hideAll();
							
							if(val==1)
							{
								$("#"+testid+"T"+paramid).prop("checked",false);
							}
							else
							{
								$("#"+testid+"T"+paramid).prop("checked",true);
							}
						}
					},
					confirm: {
						label: '<i class="icon-ok"></i> Yes (Enter)',
						className: "btn btn-primary",
						callback: function() {
							var dataArray = [];
							
							var instrument_id=$("#"+testid+"PRM"+paramid).attr("instrument_id");
							var result=$("#"+testid+"PRM"+paramid).val().trim();
							var padd=$("#"+testid+"PRM"+paramid).attr("padd");
							if(!padd){ padd=0; }
							
							var iso_no_total=0;
							
							if($("#iso_no_total"+testid+":visible").length>0)
							{
								iso_no_total=$("#iso_no_total"+testid).val();
							}
							if(!iso_no_total){ iso_no_total=0; }
							
							if($("#"+testid+"PRM"+paramid+"_mic:visible").length>0)
							{
								var result_mic=$("#"+testid+"PRM"+paramid+"_mic").val().trim();
								result=result+" ("+result_mic+")";
							}
							
							var dontPrint=$("#dontPrint_param"+testid+"tst"+paramid+":checked").length;
							
							dataArray.push({testid: testid, paramid: paramid, iso_no: iso_no, instrument_id: instrument_id, result: result, dontPrint: dontPrint, padd: padd, iso_no_total: iso_no_total});
							
							var approve_data = JSON.stringify(dataArray);
							approve_param_save(approve_data,Widaldata,test_summary,val);
							
							sessionStorage.setItem('testid', testid);
							sessionStorage.setItem('iso_no', iso_no);
						}
					}
				}
			});*/
		}
		function approve_test_summ_each(testid,iso_no,testname,dept_id)
		{
			var test_summary = 0;
			var Widaldata = 0;
			var val=0;
			if($("#approve_val").val()==1) // Approve data sent to server
			{
				load_pat_dept_tests_refresh($("#sel_dept_id").val());
				return false;
			}
			if($("#approve_test_summary"+testid+":checked").length==1)
			{
				var msg="Are you sure want to approve summary of "+testname+" ?";
				
				val=1;
			}else
			{
				var msg="Are you sure want to unapprove summary of "+testname+" ?";
				
				val=0;
			}
			
			test_summary=1;
			approve_param_save(approve_data,Widaldata,test_summary,val);
			
			sessionStorage.setItem('testid', testid);
			sessionStorage.setItem('iso_no', iso_no);
			
			/*bootbox.dialog({
				message: "<h5>"+msg+"</h5>",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> No (ESC)',
						className: "btn btn-inverse",
						callback: function() {
							bootbox.hideAll();
							
							if(val==1)
							{
								$("#approve_test_summary"+testid).prop("checked",false);
							}
							else
							{
								$("#approve_test_summary"+testid).prop("checked",true);
							}
						}
					},
					confirm: {
						label: '<i class="icon-ok"></i> Yes (Enter)',
						className: "btn btn-primary",
						callback: function() {
							test_summary=1;
							approve_param_save(approve_data,Widaldata,test_summary,val);
							
							sessionStorage.setItem('testid', testid);
							sessionStorage.setItem('iso_no', iso_no);
						}
					}
				}
			});*/
		}
		function approve_param_save(approve_data,Widaldata,test_summary,val)
		{
			if($("#approve_val").val()==1) // Approve data sent to server
			{
				load_pat_dept_tests_refresh($("#sel_dept_id").val());
				return false;
			}
			if($("#approve_val").val()==0)
			{
				if(Widaldata==1)
				{
					var WidaldataArray = [];
					
					var oval = "1";
					var hval = "2";
					var ahval = "3";
					var bhval = "4";
					for (var iw = 0; iw < 6; iw++) {
						var aow = document.getElementById("o" + iw).value;
						oval = oval + "@@" + aow;
					}
					for (var iw1 = 0; iw1 < 6; iw1++) {
						var hw = document.getElementById("h" + iw1).value;
						hval = hval + "@@" + hw;
					}
					for (var iw2 = 0; iw2 < 6; iw2++) {
						var ahw = document.getElementById("ah" + iw2).value;
						ahval = ahval + "@@" + ahw;
					}
					for (var iw3 = 0; iw3 < 6; iw3++) {
						var bhw = document.getElementById("bh" + iw3).value;
						bhval = bhval + "@@" + bhw;
					}
					
					WidaldataArray.push({testid: $("#widal_chk").val(), oval: oval, hval: hval, ahval: ahval, bhval: bhval, impression: $("#impression").val(), specimen: $("#specimen").val(), incubation_temp: $("#incubation_temp").val(), method: $("#method").val()});
					var widal_data = JSON.stringify(WidaldataArray);
				}
				
				if(test_summary==1)
				{
					var SummaryDataArray = [];
					
					var test_summary=$(".approve_test_summary");
					
					for(var i=0;i<test_summary.length;i++)
					{
						var testid=test_summary[i].value;
						
						if(testid>0)
						{
							SummaryDataArray.push({testid: testid});
						}
					}
					var test_summary_data = JSON.stringify(SummaryDataArray);
				}
				
				$("#approve_val").val(1);
				$("#loader").show();
				$.post("pages/pathology_result_approve_doc_ajax.php",
				{
					type:"approve_param_save",
					patient_id:$("#patient_id").val(),
					opd_id:$("#opd_id").val(),
					ipd_id:$("#ipd_id").val(),
					batch_no:$("#batch_no").val(),
					dept_id:$("#sel_dept_id").val(),
					for_doc:$("#for_doc").val(),
					approve_data:approve_data,
					widal_data:widal_data,
					test_summary_data:test_summary_data,
					val:val,
				},
				function(data,status)
				{
					//alert(data);
					
					var res=JSON.parse(data);
					if(res["error"]==0)
					{
						alertmsg(res["message"], 1);
					}else
					{
						alertmsg(res["message"], 0);
					}
					
					//sendUpdates();
					load_pat_dept_tests_refresh($("#sel_dept_id").val());
					
					setTimeout(function(){
						$("#approve_val").val(0);
						$("#loader").hide();
						bootbox.hideAll();
						var iso_no_cls=$(".active.iso_no_cls:visible");
						if(iso_no_cls.length)
						{
							var testid=sessionStorage.getItem('testid');
							var iso_no=sessionStorage.getItem('iso_no');
							cult_tab_click(testid,iso_no);
							
							$(".iso_no_cls").removeClass("active");
							$("#"+testid+"iso_li"+iso_no).addClass("active");
						}
					},2000);
					
				})
			}
		}
		
		function save_test_summary(testid)
		{
			$("#approve_val").val(1);
			$("#loader").show();
			$.post("pages/pathology_result_approve_doc_ajax.php",
			{
				type:"save_test_summary",
				patient_id:$("#patient_id").val(),
				opd_id:$("#opd_id").val(),
				ipd_id:$("#ipd_id").val(),
				batch_no:$("#batch_no").val(),
				dept_id:$("#sel_dept_id").val(),
				testid:testid,
				for_doc:$("#for_doc").val(),
				summary:$("#tpadd"+testid+" #rad_res").contents().find('body').html(),
			},
			function(data,status)
			{
				var res=JSON.parse(data);
				if(res["error"]==0)
				{
					alertmsg(res["message"], 1);
				}else
				{
					alertmsg(res["message"], 0);
				}
				
				setTimeout(function(){
					$("#approve_val").val(0);
					$("#loader").hide();
					bootbox.hideAll();
					load_pat_dept_tests_refresh($("#sel_dept_id").val());
				},1000);
			})
			/*bootbox.dialog({
				message: "<h5>Are you sure want to save ?</h5>",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> No (ESC)',
						className: "btn btn-inverse",
						callback: function() {
							bootbox.hideAll();
						}
					},
					confirm: {
						label: '<i class="icon-ok"></i> Yes (Enter)',
						className: "btn btn-primary",
						callback: function() {
							$("#approve_val").val(1);
							$("#loader").show();
							$.post("pages/pathology_result_approve_doc_ajax.php",
							{
								type:"save_test_summary",
								patient_id:$("#patient_id").val(),
								opd_id:$("#opd_id").val(),
								ipd_id:$("#ipd_id").val(),
								batch_no:$("#batch_no").val(),
								dept_id:$("#sel_dept_id").val(),
								testid:testid,
								for_doc:$("#for_doc").val(),
								summary:$("#tpadd"+testid+" #rad_res").contents().find('body').html(),
							},
							function(data,status)
							{
								var res=JSON.parse(data);
								if(res["error"]==0)
								{
									alertmsg(res["message"], 1);
								}else
								{
									alertmsg(res["message"], 0);
								}
								
								setTimeout(function(){
									$("#approve_val").val(0);
									$("#loader").hide();
									bootbox.hideAll();
									load_pat_dept_tests_refresh($("#sel_dept_id").val());
								},1000);
							})
						}
					}
				}
			});*/
		}
		
		function save_param_summary(testid,paramid,iso_no,instrument_id)
		{
			$("#approve_val").val(1);
			$("#loader").show();
			$.post("pages/pathology_result_approve_doc_ajax.php",
			{
				type:"save_param_summary",
				patient_id:$("#patient_id").val(),
				opd_id:$("#opd_id").val(),
				ipd_id:$("#ipd_id").val(),
				batch_no:$("#batch_no").val(),
				dept_id:$("#sel_dept_id").val(),
				testid:testid,
				paramid:paramid,
				iso_no:iso_no,
				instrument_id:instrument_id,
				for_doc:$("#for_doc").val(),
				summary:$("#padd"+testid+"PRM"+paramid+" #rad_res").contents().find('body').html(),
			},
			function(data,status)
			{
				//alert(data);
				var res=JSON.parse(data);
				if(res["error"]==0)
				{
					alertmsg(res["message"], 1);
				}else
				{
					alertmsg(res["message"], 0);
				}
				
				setTimeout(function(){
					$("#approve_val").val(0);
					$("#loader").hide();
					bootbox.hideAll();
					load_pat_dept_tests_refresh($("#sel_dept_id").val());
				},1000);
			})
			/*bootbox.dialog({
				message: "<h5>Are you sure want to save ?</h5>",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> No (ESC)',
						className: "btn btn-inverse",
						callback: function() {
							bootbox.hideAll();
						}
					},
					confirm: {
						label: '<i class="icon-ok"></i> Yes (Enter)',
						className: "btn btn-primary",
						callback: function() {
							$("#approve_val").val(1);
							$("#loader").show();
							$.post("pages/pathology_result_approve_doc_ajax.php",
							{
								type:"save_param_summary",
								patient_id:$("#patient_id").val(),
								opd_id:$("#opd_id").val(),
								ipd_id:$("#ipd_id").val(),
								batch_no:$("#batch_no").val(),
								dept_id:$("#sel_dept_id").val(),
								testid:testid,
								paramid:paramid,
								iso_no:iso_no,
								instrument_id:instrument_id,
								for_doc:$("#for_doc").val(),
								summary:$("#padd"+testid+"PRM"+paramid+" #rad_res").contents().find('body').html(),
							},
							function(data,status)
							{
								//alert(data);
								var res=JSON.parse(data);
								if(res["error"]==0)
								{
									alertmsg(res["message"], 1);
								}else
								{
									alertmsg(res["message"], 0);
								}
								
								setTimeout(function(){
									$("#approve_val").val(0);
									$("#loader").hide();
									bootbox.hideAll();
									load_pat_dept_tests_refresh($("#sel_dept_id").val());
								},1000);
							})
						}
					}
				}
			});*/
		}
		
		function test_note(testid)
		{
			$("#loader").show();
			$.post("pages/pathology_result_approve_doc_ajax.php",
			{
				type:"load_test_note",
				patient_id:$("#patient_id").val(),
				opd_id:$("#opd_id").val(),
				ipd_id:$("#ipd_id").val(),
				batch_no:$("#batch_no").val(),
				dept_id:$("#sel_dept_id").val(),
				testid:testid,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#load_data_note").html(data);
				$("#btn_modal_note").click();
				
				setTimeout(function(){
					$("#test_note_val").focus();
				},1000);
			})
		}
		function save_test_note(testid)
		{
			$("#loader").show();
			$.post("pages/pathology_result_approve_doc_ajax.php",
			{
				type:"save_test_note",
				patient_id:$("#patient_id").val(),
				opd_id:$("#opd_id").val(),
				ipd_id:$("#ipd_id").val(),
				batch_no:$("#batch_no").val(),
				dept_id:$("#sel_dept_id").val(),
				testid:testid,
				test_note:$("#test_note_val").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				var res=JSON.parse(data);
				
				if(res["error"]==2)
				{
					$("#test_note_val").focus();
				}else
				{
					$("#btn_modal_note").click();
					if(res["error"]==0)
					{
						alertmsg(res["message"], 1);
					}else
					{
						alertmsg(res["message"], 0);
					}
				}
				setTimeout(function(){
					$("#loader").hide();
					load_pat_dept_tests_refresh($("#sel_dept_id").val());
				},1000);
			})
		}
		
		// Widal Approve
		function approve_widal_click(testid,dept_id)
		{
			var val=0;
			if($("#approve_val").val()==1) // Approve data sent to server
			{
				load_pat_dept_tests_refresh($("#sel_dept_id").val());
				return false;
			}
			if($("#widal_chk:checked").length==1)
			{
				var msg="Are you sure want to approve ?";
				
				val=1;
			}else
			{
				var msg="Are you sure want to unapprove ?";
				
				val=0;
			}
			
			approve_param_save("","1",val);
			
			/*bootbox.dialog({
				message: "<h5>"+msg+"</h5>",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> No (ESC)',
						className: "btn btn-inverse",
						callback: function() {
							bootbox.hideAll();
							$("#widal_chk").prop("checked",false);
						}
					},
					confirm: {
						label: '<i class="icon-ok"></i> Yes (Enter)',
						className: "btn btn-primary",
						callback: function() {
							approve_param_save("","1",val);
						}
					}
				}
			});*/
		}
		
		
		// Culture
		function load_culture_data(testid)
		{
			if($("#growth_val"+testid).val()==2) // Growth
			{
				$("#iso_field"+testid).show();
			}
			else
			{
				$("#iso_field"+testid).hide();
				$("#growth_val"+testid).val("1");
				$("#iso_no_total"+testid).val("0");
			}
			
			$.post("pages/pathology_result_approve_doc_culture_data.php", {
				type:"load_culture_data",
				growth_val:$("#growth_val"+testid).val(),
				iso_no_total:$("#iso_no_total"+testid).val(),
				
				patient_id:$("#patient_id").val(),
				opd_id:$("#opd_id").val(),
				ipd_id:$("#ipd_id").val(),
				batch_no:$("#batch_no").val(),
				dept_id:$("#sel_dept_id").val(),
				testid: testid,
			},
			function(data, status) {
				$("#load_culture_data_div"+testid).html(data);
				
				setTimeout(function(){
					if($("#iso_no_total"+testid).val()>0)
					{
						$("#iso_no_selected"+testid).val("1");
					}
					else
					{
						$("#iso_no_selected"+testid).val(0);
					}
					
					cult_tab_click(testid,1);
				},100);
			})
		}
		function cult_tab_click(testid,iso_no)
		{
			$.post("pages/pathology_result_approve_doc_culture_data.php",
			{
				type:"load_culture_iso_data",
				iso_no:iso_no,
				
				patient_id:$("#patient_id").val(),
				opd_id:$("#opd_id").val(),
				ipd_id:$("#ipd_id").val(),
				batch_no:$("#batch_no").val(),
				dept_id:$("#sel_dept_id").val(),
				testid: testid,
			},
			function(data, status) {
				$(".tab_iso_cls"+testid).html("");
				$("#tab_iso"+testid+iso_no).show().html(data);
			})
		}
		function search(inputVal)
		{
			var table = $('#tblData');
			table.find('tr').each(function(index, row)
			{
				var allCells = $(row).find('td');
				if(allCells.length > 0)
				{
					var found = false;
					allCells.each(function(index, td)
					{
						var regExp = new RegExp(inputVal, 'i');
						if(regExp.test($(td).text()))
						{
							found = true;
							return false;
						}
					});
					if(found == true)
					{
						$("#no_record").text("");
						$(row).show();
					}else{
						$(row).hide();
						var n = $('tr:visible').length;
						if(n==1)
						{
							$("#no_record").text("No matching records found");
						}else
						{
							$("#no_record").text("");
						}
					}
					//if(found == true)$(row).show();else $(row).hide();
				}
			});
		}
		
		function load_delta_check(uhid,opd_id,ipd_id,batch_no,testid,paramid)
		{
			$("#loader").show();
			$.post("pages/delta_check_data.php",
			{
				type:"load_delta_check",
				uhid:uhid,
				opd_id:opd_id,
				ipd_id:ipd_id,
				batch_no:batch_no,
				testid: testid,
				paramid: paramid,
			},
			function(data, status)
			{
				$("#loader").hide();
				$("#load_data_note").html(data);
				$("#btn_modal_note").click();
			})
		}
		
		function flag_patient()
		{
			$("#loader").show();
			$.post("pages/pathology_result_approve_tech_ajax.php",
			{
				type:"flag_patient",
				patient_id:$("#patient_id").val(),
				opd_id:$("#opd_id").val(),
				ipd_id:$("#ipd_id").val(),
				batch_no:$("#batch_no").val(),
				dept_id:$("#sel_dept_id").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#load_data_note").html(data);
				$("#btn_modal_note").click();
				
				setTimeout(function(){
					$("#flag_cause").focus();
				},1000);
			})
		}
		function save_flag_patient(flag)
		{
			if(flag=="0" && $("#flag_cause").val().trim()=="") // Save
			{
				alertmsg("Enter Cause", 0);
				$("#flag_cause").focus();
				return false;
			}
			$("#loader").show();
			$.post("pages/pathology_result_approve_tech_ajax.php",
			{
				type:"save_flag_patient",
				patient_id:$("#patient_id").val(),
				opd_id:$("#opd_id").val(),
				ipd_id:$("#ipd_id").val(),
				batch_no:$("#batch_no").val(),
				dept_id:$("#sel_dept_id").val(),
				flag_cause:$("#flag_cause").val().trim(),
				flag_remarks:$("#flag_remarks").val().trim(),
			},
			function(data,status)
			{
				$("#loader").hide();
				var res=JSON.parse(data);
				
				if(res["error"]==2)
				{
					$("#flag_cause").focus();
				}else
				{
					$("#btn_modal_note").click();
					if(res["error"]==0)
					{
						alertmsg(res["message"], 1);
					}else
					{
						alertmsg(res["message"], 0);
					}
				}
				setTimeout(function(){
					$("#loader").hide();
					//load_pat_dept_tests_refresh($("#sel_dept_id").val());
					load_pat_ser(0);
				},1000);
			})
		}
		
		function print_preview(view,dept_id,barcode_id)
		{
			var uhid=$("#patient_id").val();
			var opd_id=$("#opd_id").val();
			var ipd_id=$("#ipd_id").val();
			var batch_no=$("#batch_no").val();
			
			var tst="";
			var user = $("#user").text().trim();
			
			var url = "pages/pathology_report_print.php?uhid=" + btoa(uhid) + "&opd_id=" + btoa(opd_id) + "&ipd_id=" + btoa(ipd_id) + "&batch_no=" + btoa(batch_no) + "&tests=" + btoa(tst) + "&hlt=" + btoa(tst) + "&user=" + btoa(user) +"&sel_doc=" + btoa(0) + "&view=" + btoa(1) + "&iso_no=" + btoa(0) + "&doc_view=" + btoa(view) + "&dept_id=" + btoa(dept_id);
			var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
		}
	</script>
</div>
<?php
}

if($type=="refresh_pat_dept_color")
{
	$patient_id=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$ipd_id=$_POST["ipd_id"];
	$batch_no=$_POST["batch_no"];
	
	$bill_no="";
	if($opd_id)
	{
		$bill_no=$opd_id;
	}
	if($ipd_id)
	{
		$bill_no=$ipd_id;
	}
	
	$data = [];
	
	$dept_str="SELECT DISTINCT b.`type_id` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`category_id`=1 AND b.`type_id` NOT IN($non_reporting_test_dept_id)";
	
	$dept_qry=mysqli_query($link,$dept_str);
	
	while($dept=mysqli_fetch_array($dept_qry))
	{
		$type_id=$dept["type_id"];
		
		$cls="";
		$dept_info=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$type_id'"));
		
		// No Doc Approve
		$non_approve_testresults=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(a.slno) AS `tot` FROM `testresults` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND a.`main_tech`=0 AND a.`doc`=0"));
		
		$non_approve_summary=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(a.slno) AS `tot` FROM `patient_test_summary` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND a.`main_tech`=0 AND a.`doc`=0"));
		
		$non_approve_widal["tot"]=0;
		if($type_id==32) // Serology
		{
			$non_approve_widal=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(DISTINCT a.`testid`) AS `tot` FROM `widalresult` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND a.`main_tech`=0 AND a.`doc`=0 LIMIT 1"));
		}
		
		//$tot_lis_n=mysqli_fetch_array(mysqli_query($link,"SELECT count(*) as tot FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id'  AND `batch_no`='$batch_no'  AND `result`!='' AND `testid` IN (SELECT `testid` FROM `testmaster` WHERE `type_id`='$type_id') AND `paramid` NOT IN (SELECT `paramid` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id'  AND `batch_no`='$batch_no' )"));
		
		$lis_result_not_in_testresults=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) as tot FROM `test_sample_result` tsr JOIN `testmaster` tm ON tsr.testid = tm.testid LEFT JOIN `testresults` tr ON tsr.patient_id = tr.patient_id AND tsr.opd_id = tr.opd_id AND tsr.ipd_id = tr.ipd_id AND tsr.batch_no = tr.batch_no AND tsr.paramid = tr.paramid WHERE tsr.patient_id = '$patient_id' AND tsr.opd_id = '$opd_id' AND tsr.ipd_id = '$ipd_id' AND tsr.batch_no = '$batch_no' AND tsr.result != '' AND tm.type_id = '$type_id' AND tr.paramid IS NULL"));
		
		// Main Tech Approve
		$approved_testresults=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(a.slno) AS `tot` FROM `testresults` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND (a.`doc`>0 OR a.`main_tech`>0)"));
		
		$approved_summary=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(a.slno) AS `tot` FROM `patient_test_summary` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND (a.`doc`>0 OR a.`main_tech`>0)"));
		
		$approved_widal["tot"]=0;
		if($type_id==32) // Serology
		{
			$approved_widal=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(DISTINCT a.`testid`) AS `tot` FROM `widalresult` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND (a.`doc`>0 OR a.`main_tech`>0) LIMIT 1"));
		}
		
		$tot_non_approved_chk=$non_approve_testresults["tot"]+$non_approve_summary["tot"]+$non_approve_widal["tot"];
		$tot_approved_chk=$approved_testresults["tot"]+$approved_summary["tot"]+$approved_widal["tot"];
		
		$cls="btn btn-success btn-mini";
		
		if($tot_approved_chk==0)
		{
			$cls="btn btn-danger btn-mini";
			
			// IN test_sample_result but not in testresults
			$lis_result_not_in_testresults=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) as tot FROM `test_sample_result` tsr JOIN `testmaster` tm ON tsr.testid = tm.testid LEFT JOIN `testresults` tr ON tsr.patient_id = tr.patient_id AND tsr.opd_id = tr.opd_id AND tsr.ipd_id = tr.ipd_id AND tsr.batch_no = tr.batch_no AND tsr.paramid = tr.paramid WHERE tsr.patient_id = '$patient_id' AND tsr.opd_id = '$opd_id' AND tsr.ipd_id = '$ipd_id' AND tsr.batch_no = '$batch_no' AND tsr.result != '' AND tm.type_id = '$type_id' AND tr.paramid IS NULL"));
			
			if($lis_result_not_in_testresults["tot"]>0)
			{
				$cls="btn btn-warning btn-mini";
			}
		}
		//if($tot_non_approved_chk>0 || $lis_result_not_in_testresults["tot"]>0)
		if($tot_non_approved_chk>0)
		{
			$cls="btn btn-warning btn-mini";
			
			if($tot_approved_chk>0)
			{
				$cls="btn btn-primary btn-mini";
			}
		}
		
		$data[] = [
			"dept_id" => $type_id,
			"btn_cls" => $cls,
			"bill_id" => str_replace("/","",$bill_no).$batch_no,
		];
	}
	
	echo json_encode($data);
}

function check_test_approve($patient_id,$opd_id,$ipd_id,$batch_no,$dept_id,$testid,$barcode_id)
{
	global $link;
	global $non_reporting_test_dept_id;
	
	$dept_str="SELECT DISTINCT b.`type_id` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`category_id`=1 AND b.`type_id` NOT IN($non_reporting_test_dept_id)";
	
	if($dept_id>0)
	{
		$dept_str.=" AND b.`type_id`='$dept_id'";
	}
	
	if($testid>0)
	{
		$dept_str.=" AND b.`testid`='$testid'";
	}
	
	if($barcode_id!="")
	{
		$dept_str.=" AND b.`testid` IN(SELECT DISTINCT `testid` FROM `test_sample_result` WHERE `barcode_id`='$barcode_id')";
	}
	//echo $dept_str."<br>";
	$dept_qry=mysqli_query($link,$dept_str);
	
	$status_array["dept_id"]=0;
	$status_array["approve"]=0;
	$status_array["not_approve"]=0;
	$status_array["main_tech_approve"]=0;
	
	while($dept=mysqli_fetch_array($dept_qry))
	{
		$type_id=$dept["type_id"];
		
		$status_array["dept_id"]=$type_id;
		
		// Main Tech Approve
		/*
		$approved_testresults_main_tech_str="SELECT COUNT(a.slno) AS `tot` FROM `testresults` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND a.`main_tech`>0";
		
		if($testid>0)
		{
			$approved_testresults_main_tech_str.=" AND b.`testid`='$testid'";
		}
		
		$approved_testresults_main_tech=mysqli_fetch_array(mysqli_query($link, $approved_testresults_main_tech_str));
		
		$approved_summary_main_tech_str="SELECT COUNT(a.slno) AS `tot` FROM `patient_test_summary` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND a.`main_tech`>0";
		
		if($testid>0)
		{
			$approved_summary_main_tech_str.=" AND b.`testid`='$testid'";
		}
		
		$approved_summary_main_tech=mysqli_fetch_array(mysqli_query($link, $approved_summary_main_tech_str));
		
		$approved_widal_main_tech["tot"]=0;
		if($type_id==32) // Serology
		{
			$approved_widal_main_tech_str="SELECT COUNT(DISTINCT a.`testid`) AS `tot` FROM `widalresult` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND a.`main_tech`>0";
			
			if($testid>0)
			{
				$approved_widal_main_tech_str.=" AND b.`testid`='$testid'";
			}
			$approved_widal_main_tech_str.=" LIMIT 1";
			
			$approved_widal_main_tech=mysqli_fetch_array(mysqli_query($link, $approved_widal_main_tech_str));
		}
		
		$tot_approved_main_tech_chk=$approved_testresults_main_tech["tot"]+$approved_summary_main_tech["tot"]+$approved_widal_main_tech["tot"]; // Total Main Tech Approve
		*/
		$tot_approved_main_tech_chk=1; // Total Main Tech Approve
		
		// Doctor Approve
		$approved_testresults_str="SELECT COUNT(a.slno) AS `tot` FROM `testresults` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND (a.`doc`>0 OR a.`main_tech`>0)";// AND a.`main_tech`>0
		
		if($testid>0)
		{
			$approved_testresults_str.=" AND b.`testid`='$testid'";
		}
		
		$approved_testresults=mysqli_fetch_array(mysqli_query($link, $approved_testresults_str));
		
		$approved_summary_str="SELECT COUNT(a.slno) AS `tot` FROM `patient_test_summary` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND (a.`doc`>0 OR a.`main_tech`>0)";// AND a.`main_tech`>0
		
		if($testid>0)
		{
			$approved_summary_str.=" AND b.`testid`='$testid'";
		}
		
		$approved_summary=mysqli_fetch_array(mysqli_query($link, $approved_summary_str));// AND a.`main_tech`>0
		
		$approved_widal["tot"]=0;
		if($type_id==32) // Serology
		{
			$approved_widal_str="SELECT COUNT(DISTINCT a.`testid`) AS `tot` FROM `widalresult` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$type_id' AND (a.`doc`>0 OR a.`main_tech`>0)";// AND a.`main_tech`>0
			
			if($testid>0)
			{
				$approved_widal_str.=" AND b.`testid`='$testid'";
			}
			$approved_widal_str.=" LIMIT 1";
			
			$approved_widal=mysqli_fetch_array(mysqli_query($link, $approved_widal_str));
		}
		
		$tot_approved_doc_chk=$approved_testresults["tot"]+$approved_summary["tot"]+$approved_widal["tot"]; // Total Approve
		
		if($tot_approved_doc_chk==0)
		{
			if($tot_approved_main_tech_chk>0)
			{
				$status_array["not_approve"]=1;  // Not Approve or No Data
			}
		}else
		{
			$status_array["approve"]=1;  // Full or Partial Approve
		}
		
		if($tot_approved_main_tech_chk>0)
		{
			$status_array["main_tech_approve"]=1;
		}
		else
		{
			if($type_id==150) // Culture
			{
				$test_sample_result_str="SELECT `result`,`equip_name` FROM `test_sample_result` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`type_id`='$type_id' AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no'";
				
				if($testid>0)
				{
					$test_sample_result_str.=" AND b.`testid`='$testid'";
				}
				$test_sample_result_str.=" AND a.`result`!='' limit 1";
				
				$test_sample_result=mysqli_fetch_array(mysqli_query($link, $test_sample_result_str));
				
				if($test_sample_result)
				{
					$status_array["main_tech_approve"]=1;
				}
			}
		}
	}
	
	return $status_array;
}

mysqli_close($link);
?>
