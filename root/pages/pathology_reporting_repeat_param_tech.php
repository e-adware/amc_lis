<?php
session_start();

include("../../includes/connection.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

if(!$c_user)
{
	echo "Error";
}

$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST["type"];

if($type=="repeat_test_save")
{
	$patient_id=trim(mysqli_real_escape_string($link, $_POST["uhid"]));
	$opd_id=trim(mysqli_real_escape_string($link, $_POST["opd_id"]));
	$ipd_id=trim(mysqli_real_escape_string($link, $_POST["ipd_id"]));
	$batch_no=trim(mysqli_real_escape_string($link, $_POST["batch_no"]));
	$testid=trim(mysqli_real_escape_string($link, $_POST["testid"]));
	$iso_no=trim(mysqli_real_escape_string($link, $_POST["iso_no"]));
	$repeat_reason=trim(mysqli_real_escape_string($link, $_POST["repeat_reason"]));
	
	if(!$repeat_reason)
	{
		echo "303@$@Reason cannot blank !";
		exit();
	}
	
	$repeat_num=0;
	
	$test_param_qry=mysqli_query($link, "SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' AND `ParamaterId` NOT IN(639,640,641) ORDER BY `sequence` ASC");
	while($test_param=mysqli_fetch_array($test_param_qry))
	{
		$paramid=$test_param["ParamaterId"];
		
		$test_results=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));
		
		$test_sample=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`=''"));
		
		if($test_results || $test_sample["result"]!="")
		{
			$max_repeat=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`repeat_no`) AS `repeat_no` FROM `pathology_repeat_param_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no' "));
			
			$repeat_no=$max_repeat["repeat_no"]+1;
			
			if(mysqli_query($link, "INSERT INTO `pathology_repeat_param_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `reason`, `user`, `date`, `time`, `repeat_no`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$repeat_reason','$c_user','$date','$time','$repeat_no')"))
			{
				$last_row=mysqli_fetch_array(mysqli_query($link, "SELECT `repeat_id` FROM `pathology_repeat_param_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no' AND `repeat_no`='$repeat_no' ORDER BY `repeat_id` DESC LIMIT 1"));
				$repeat_id=$last_row["repeat_id"];
				
				if($test_results)
				{
					mysqli_query($link, "INSERT INTO `testresults_repeat`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `repeat_id`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$test_results[sequence]','$test_results[result]','$test_results[range_status]','$test_results[range_id]','$test_results[status]','$test_results[tech_note]','$test_results[doc_note]','$test_results[instrument_id]','$test_results[time]','$test_results[date]','$test_results[doc]','$test_results[tech]','$test_results[main_tech]','$test_results[for_doc]','$repeat_id')");
					
					mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'");
				}
				
				if($test_sample["result"]!="")
				{
					if($iso_no==0){ $iso_no_val=""; }
					
					mysqli_query($link, "INSERT INTO `test_sample_result_repeat`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `iso_no`, `result`, `time`, `date`, `user`, `repeat_id`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$test_sample[barcode_id]','$test_sample[vaccus]','$test_sample[sample_id]','$test_sample[equip_id]','$testid','$paramid','$iso_no_val','$test_sample[result]','$test_sample[time]','$test_sample[date]','$test_sample[user]','$repeat_id')");
					
					mysqli_query($link, "UPDATE `test_sample_result` SET `result`='' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no_val'");
				}
				
				$repeat_num++;
			}
		}
	}
	
	if($repeat_num>0)
	{
		echo "101@$@Saved@$@0";
	}else
	{
		echo "404@$@Faile, try again later.";
	}
}

if($type=="repeat_parameter_save")
{
	$patient_id=trim(mysqli_real_escape_string($link, $_POST["uhid"]));
	$opd_id=trim(mysqli_real_escape_string($link, $_POST["opd_id"]));
	$ipd_id=trim(mysqli_real_escape_string($link, $_POST["ipd_id"]));
	$batch_no=trim(mysqli_real_escape_string($link, $_POST["batch_no"]));
	$testid=trim(mysqli_real_escape_string($link, $_POST["testid"]));
	$paramid=trim(mysqli_real_escape_string($link, $_POST["paramid"]));
	$iso_no=trim(mysqli_real_escape_string($link, $_POST["iso_no"]));
	$repeat_reason=trim(mysqli_real_escape_string($link, $_POST["repeat_reason"]));
	
	if(!$repeat_reason)
	{
		echo "303@$@Reason cannot blank !";
		exit();
	}
	
	$max_repeat=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`repeat_no`) AS `repeat_no` FROM `pathology_repeat_param_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no' "));
	
	$repeat_no=$max_repeat["repeat_no"]+1;
	
	if(mysqli_query($link, "INSERT INTO `pathology_repeat_param_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `reason`, `user`, `date`, `time`, `repeat_no`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$repeat_reason','$c_user','$date','$time','$repeat_no')"))
	{
		$last_row=mysqli_fetch_array(mysqli_query($link, "SELECT `repeat_id` FROM `pathology_repeat_param_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no' AND `repeat_no`='$repeat_no' ORDER BY `repeat_id` DESC LIMIT 1"));
		$repeat_id=$last_row["repeat_id"];
		
		$test_results=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));
		if($test_results)
		{
			mysqli_query($link, "INSERT INTO `testresults_repeat`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `repeat_id`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$test_results[sequence]','$test_results[result]','$test_results[range_status]','$test_results[range_id]','$test_results[status]','$test_results[tech_note]','$test_results[doc_note]','$test_results[instrument_id]','$test_results[time]','$test_results[date]','$test_results[doc]','$test_results[tech]','$test_results[main_tech]','$test_results[for_doc]','$repeat_id')");
			
			mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'");
		}
		
		if($iso_no==0){ $iso_no=""; }
		
		$test_sample=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));
		if($test_sample["result"]!="")
		{
			if($iso_no==0){ $iso_no_val=""; }
			
			mysqli_query($link, "INSERT INTO `test_sample_result_repeat`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `iso_no`, `result`, `time`, `date`, `user`, `repeat_id`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$test_sample[barcode_id]','$test_sample[vaccus]','$test_sample[sample_id]','$test_sample[equip_id]','$testid','$paramid','$iso_no_val','$test_sample[result]','$test_sample[time]','$test_sample[date]','$test_sample[user]','$repeat_id')");
			
			mysqli_query($link, "UPDATE `test_sample_result` SET `result`='' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no_val'");
		}
		
		echo "101@$@Saved@$@".$repeat_no;
	}
	else
	{
		echo "404@$@Faile, try again later.";
	}
}

if($type=="repeat_param_view")
{
	$patient_id=trim(mysqli_real_escape_string($link, $_POST["uhid"]));
	$opd_id=trim(mysqli_real_escape_string($link, $_POST["opd_id"]));
	$ipd_id=trim(mysqli_real_escape_string($link, $_POST["ipd_id"]));
	$batch_no=trim(mysqli_real_escape_string($link, $_POST["batch_no"]));
	$testid=trim(mysqli_real_escape_string($link, $_POST["testid"]));
	$paramid=trim(mysqli_real_escape_string($link, $_POST["paramid"]));
	$iso_no=trim(mysqli_real_escape_string($link, $_POST["iso_no"]));
	
	$repeat_qry=mysqli_query($link, "SELECT * FROM `pathology_repeat_param_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$paramid' and iso_no='$iso_no' ORDER BY `repeat_id` ASC");
?>
	<table class="table table-condensed table-bordered table-hover">
		<tr>
			<th>Repeat No.</th>
			<th>Repeat Reason</th>
			<th>Previous Result</th>
			<th>User</th>
			<th>Date</th>
			<th>Time</th>
		</tr>
<?php
	while($repeat_data=mysqli_fetch_array($repeat_qry))
	{
		$result=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `testresults_repeat` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$paramid' and iso_no='$iso_no' and repeat_id='$repeat_data[repeat_id]'"));
		
		if(!$result)
		{
			if($iso_no==0){ $iso_no=""; }
			
			$result=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `test_sample_result_repeat` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$paramid' and iso_no='$iso_no' and repeat_id='$repeat_data[repeat_id]'"));
		}
		
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$repeat_data[user]'"));
?>
		<tr>
			<td><?php echo $repeat_data["repeat_no"]; ?></td>
			<td><?php echo $repeat_data["reason"]; ?></td>
			<td><?php echo nl2br($result["result"]); ?></td>
			<td><?php echo $user_info["name"]; ?></td>
			<td><?php echo date("d-M-Y",strtotime($repeat_data["date"])); ?></td>
			<td><?php echo date("h:i:s A",strtotime($repeat_data["time"])); ?></td>
		</tr>
<?php
	}
?>
	</table>
<?php
}
?>
