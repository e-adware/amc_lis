<?php
session_start();
include'../../includes/connection.php';
include'../../includes/global.function.php';
$date=date("Y-m-d");
$time=date("H:i:s");
$sunrise = "08:00:00";
$sunset = "14:00:00";
$ip_addr=$_SERVER["REMOTE_ADDR"];

function text_query_json($txt,$file)
{
	$txt_file="../../log/".$file;
	$fp = fopen($txt_file, 'w');
	if(file_exists($txt_file))
	{
		file_put_contents($txt_file, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
	else
	{
		$fp = fopen($txt_file, 'w');
		file_put_contents($txt_file, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
}

function strToDOB($age)
{
	$age=strtoupper($age);
	$a=explode("Y",$age);
	$yr=$a[0];
	$rm=$a[1];
	$m=explode("M",$rm);
	$mn=$m[0];
	$rd=$m[1];
	$d=explode("D",$rd);
	$dy=$d[0];
	$dob=date("Y-m-d", strtotime("-".$dy." day"));
	$dob=date("Y-m-d", strtotime($date." -".$mn." month"));
	$dob=date("Y-m-d", strtotime($date." -".$yr." year"));
	return $dob;
}

function strToAge($age)
{
	$age=strtoupper($age);
	$Age=array();
	$a=explode("Y",$age);
	$yr=$a[0];
	if($yr>0)
	{
		$Age['year']=$yr;
	}
	$rm=$a[1];
	$m=explode("M",$rm);
	$mn=$m[0];
	if($mn>0)
	{
		$Age['month']=$mn;
	}
	$rd=$m[1];
	$d=explode("D",$rd);
	$dy=$d[0];
	if($dy>0)
	{
		$Age['day']=$dy;
	}
	return $Age;
}

function strToYears($str)
{
	preg_match('/(?P<years>\d+)Y/', $str, $years);
	preg_match('/(?P<months>\d+)M/', $str, $months);
	preg_match('/(?P<days>\d+)D/', $str, $days);
	
	if($years['years'])
	{
		$ageStr=$years['years'];
		$ageType="Years";
	}
	else if($months['months'])
	{
		$ageStr=$months['months'];
		$ageType="Months";
	}
	else if($days['days'])
	{
		$ageStr=$days['days'];
		$ageType="Days";
	}
	
	//~ $ageStr="";
	//~ $ageType="";
	
	//~ $a=explode("Y",$age);
	//~ $yr=$a[0];
	//~ if($yr>0)
	//~ {
		//~ $ageStr=$yr;
		//~ $ageType="Years";
	//~ }
	//~ $rm=$a[1];
	//~ $m=explode("M",$rm);
	//~ $mn=$m[0];
	//~ if($mn>0)
	//~ {
		//~ if($ageStr)
		//~ {
			//~ $ageStr.=".".$mn;
		//~ }
		//~ else
		//~ {
			//~ $ageStr=$mn;
		//~ }
		//~ if($ageType=="")
		//~ {
			//~ $ageType="Months";
		//~ }
	//~ }
	//~ $rd=$m[1];
	//~ $d=explode("D",$rd);
	//~ $dy=$d[0];
	//~ if($dy>0)
	//~ {
		//~ if($ageStr)
		//~ {
			//~ $ageStr.=".".$dy;
		//~ }
		//~ else
		//~ {
			//~ $ageStr=$dy;
		//~ }
		//~ if($ageType=="")
		//~ {
			//~ $ageType="Days";
		//~ }
	//~ }
	return $ageStr."@".$ageType;
}

$url="http://192.168.1.1:8081/tmch-api/api/public/lis/";
//$token=$_POST['token'];
//echo strToAge("0Y6M0D")."<br/>";

$type = $_POST['type'];

if($type==1)
{
	$CashMemoNo = $_POST['cashMemo'];
	//*
	$request_headers = ["client_id:amtron", "client_secret:password", "grant_type:client_credentials", "cash-memo-no:$CashMemoNo"];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url."getPatientDataByCashMemoNo");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds


	$server_output = curl_exec($ch);
	
	if(curl_errno($ch))
	{
		$error_msg = curl_error($ch);
		echo "Error ".$error_msg;
		exit;
    }

	curl_close ($ch);
	//*/
	//$server_output=file_get_contents('../../api/'.$CashMemoNo.'.json');
	//echo $server_output;
	
	$json = json_decode($server_output,true);
	$resArray	=$json['result'];
	$CashMemoNo	=$CashMemoNo;
	$name		=$resArray['Name'];
	$Age		=$resArray['Age'];
	$age_str	=$resArray['Age'];
	$ward		=$resArray['Ward'];
	$sex		=$resArray['Gender'];
	$pid		=$resArray['PatientNo'];
	$opd_id		=$resArray['PatientNo'];
	$patType	=$resArray['patientType'];
	$regDate	=$resArray['RegistrationDate'];
	$CashMemoDate=$resArray['CashMemoDate'];
	$rDt		=explode(" ", $regDate);
	$dt			=explode("/", $rDt[0]);
	$rDate		=date("Y-m-d", strtotime($dt[2]."-".$dt[1]."-".$dt[0]));
	$rTime		=date("H:i:s", strtotime($rDt[1]));
	$TestDetails=$resArray['TestDetails'];
	$dob		=strToDOB($Age);
	$ageCheck	=strToAge($Age);
	$age="";
	if($ageCheck['year'])
	{
		$age=$ageCheck['year']." Years";
	}
	if($ageCheck['month'])
	{
		if($age)
		{
			$age.=" ".$ageCheck['month']." Months";
		}
		else
		{
			$age=$ageCheck['month']." Months";
		}
	}
	if($ageCheck['day'])
	{
		if($age)
		{
			$age.=" ".$ageCheck['day']." Days";
		}
		else
		{
			$age=$ageCheck['day']." Days";
		}
	}
	$ag			=explode("@",strToYears($Age));
	$ageStr		=$ag[0];
	$ageType	=$ag[1];
	$batch_no	=1;
	$sample_id	=0;
	$dept		=0;
	$user		=101;
	
	$metadata	=$json['metadata'];
	$code		=$metadata['code'];
	//$regDate	=$metadata['timestatmp'];
	$wardClass="";
	if($ward=="NICU")
	{
		$wardClass="#FFEAA1";
	}
	?>
	<div style="display:none;"><?php echo $server_output;?></div>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<td colspan="6" style="padding:1px;"></td>
		</tr>
		<tr>
			<th>Patient No</th>
			<td><?php echo $pid;?></td>
			<th>Cash Memo No</th>
			<td><?php echo $CashMemoNo;?></td>
			<th>Reg Date Time</th>
			<td><?php echo $regDate;?></td>
		</tr>
		<tr>
			<th>Name</th><td><?php echo $name;?></td>
			<th>Age Sex</th><td><?php echo $age." ".$sex;?></td>
			<th>Ward</th><td style="background:<?php echo $wardClass;?>;"><?php echo $ward;?></td>
		</tr>
		<tr>
			<th colspan="6" style="text-align:center;border-top:1px solid;border-bottom:1px solid;">Tests</th>
		</tr>
		<?php
		$labTest=0;
		$fluidTest=0;
		$allTests="";
		$aTests="";
		$i=1;
		$testDepts=array();
		foreach($TestDetails as $ky=>$vl)
		{
			$TestId		=$vl['TestId'];
			if($aTests)
			{
				$aTests.=",".$TestId;
			}
			else
			{
				$aTests=$TestId;
			}
			$tst		=mysqli_fetch_assoc(mysqli_query($link,"SELECT `testname`,`pTestId`,`labTest`,`fluidTest` FROM `amtronTestList` WHERE `aTestId`='$TestId'"));
			
			/*------------- for NICU Dept ------------*/
			if($ward=="NICU" && ($TestId=="1146" || $TestId=="1193"))
			{
				$tst['pTestId']="2811";
			}
			
			/*------------- for fluid test -----------*/
			$tdClass="";
			if($tst['fluidTest']>0)
			{
				$fluidTest++;
				$tdClass="tdFluidTest";
				$tst['pTestId']="0";
			}
			
			/*------------- for lab test -------------*/
			if($tst['labTest']>0)
			{
				if($tst['pTestId'])
				{
					if($allTests!="")
					{
						$allTests.=",".$tst['pTestId'];
					}
					else
					{
						$allTests=$tst['pTestId'];
					}
					
					if($TestId=="1458")
					{
						$allTests.=",83";
					}
				}
				$labTest++;
				$tdClass="tdLabTest";
			}
			else
			{
				$tdClass="tdRadTest";
			}
			
			
			$dept=mysqli_fetch_assoc(mysqli_query($link,"SELECT a.`type_id`, b.`prefix` FROM `testmaster` a, `test_department` b WHERE a.`type_id`=b.`id` AND a.`testid`='$tst[pTestId]'"));
		?>
		<tr>
			<td class="<?php echo $tdClass;?>" colspan="6"><?php echo $i.". ".$tst['testname']." (".$TestId.")";?></td>
		</tr>
		<?php
		array_push($testDepts,$dept['type_id']);
		$i++;
		}
		$testDepts=array_unique($testDepts);
		$testDepts=array_filter($testDepts, function($value) { return !is_null($value) && $value !== ''; });
		//print_r($testDepts);
		$allDepts="";
		foreach($testDepts as $tDep)
		{
			if($allDepts)
			{
				$allDepts.=",".$tDep;
			}
			else
			{
				$allDepts=$tDep;
			}
		}
		if($fluidTest>0)
		{
		?>
		<tr>
			<th colspan="6" style="text-align:center;">Select Fluid Test</th>
		</tr>
		<tr>
			<td colspan="6">
				<?php
				$ll=1;
				$fluidQry=mysqli_query($link,"SELECT `testid`,`testname` FROM `testmaster` WHERE `testname` LIKE '%fluid%' ORDER BY `testname`");
				while($r=mysqli_fetch_assoc($fluidQry))
				{
					$onclick="onclick='fluidTestChk($ll)'";
					echo "<div class='fluidTest' id='fluidTest$ll'><label><input type='checkbox' class='chkBox' id='chk$ll' $onclick value='$r[testid]' /> $r[testname]</label></div>";
					$ll++;
				}
				?>
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<input type="hidden" id="PatientNo" value="<?php echo $pid;?>" />
	<input type="hidden" id="CashMemoNo" value="<?php echo $CashMemoNo;?>" />
	<input type="hidden" id="regDate" value="<?php echo $regDate;?>" />
	<input type="hidden" id="CashMemoDate" value="<?php echo $CashMemoDate;?>" />
	<input type="hidden" id="name" value="<?php echo $name;?>" />
	<input type="hidden" id="aAge" value="<?php echo $Age;?>" />
	<input type="hidden" id="age" value="<?php echo $ageStr;?>" />
	<input type="hidden" id="ageType" value="<?php echo $ageType;?>" />
	<input type="hidden" id="dob" value="<?php echo $dob;?>" />
	<input type="hidden" id="sex" value="<?php echo $sex;?>" />
	<input type="hidden" id="ward" value="<?php echo $ward;?>" />
	<input type="hidden" id="labTest" value="<?php echo $labTest;?>" />
	<input type="hidden" id="allTests" value="<?php echo $allTests;?>" />
	<input type="hidden" id="aTests" value="<?php echo $aTests;?>" />
	<input type="hidden" id="testDepts" value="<?php echo $allDepts;?>" />
	<input type="hidden" id="patType" value="<?php echo $patType;?>" />
	<?php
}

if($type==2)
{
	$PatientNo	= $_POST['PatientNo'];
	$CashMemoNo = $_POST['CashMemoNo'];
	$name		= $_POST['name'];
	$aAge		= $_POST['aAge'];
	$age		= $_POST['age'];
	$ageType	= $_POST['ageType'];
	$dob		= $_POST['dob'];
	$sex		= $_POST['sex'];
	if($sex=="M")
	{
		$sex="Male";
	}
	if($sex=="F")
	{
		$sex="Female";
	}
	if($sex=="O")
	{
		$sex="Other";
	}
	$ward		= $_POST['ward'];
	$allTests	= $_POST['allTests'];
	$aTests		= $_POST['aTests'];
	$regDate	= $_POST['regDate'];
	$CashMemoDate= $_POST['CashMemoDate'];
	$rDt		= explode(" ", $CashMemoDate);
	$dt			= explode("/", $rDt[0]);
	$rDate		= date("Y-m-d", strtotime($dt[2]."-".$dt[1]."-".$dt[0]));
	$rTime		= date("H:i:s", strtotime($rDt[1]));
	$testDepts	= $_POST['testDepts'];
	$patType	= $_POST['patType'];
	$user		= $_POST['user'];
	
	$branch_id	=1;
	$pid		=date("His").$user.rand(11,99);
	$dis_month	=date("m");
	$dis_year_sm=date("y");
	
	if($patType=="OPD")
	{
		$reg_type=1;
	}
	if($patType=="IPD")
	{
		$reg_type=2;
	}
	if(!$reg_type)
	{
		$reg_type=2;
	}
	
	$val		=array();
	
	
	$checkPat	=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `aPatientList` WHERE `PatientNo`='$PatientNo' AND `CashMemoNo`='$CashMemoNo'"));
	
	$txt="";
	if($checkPat)
	{
		$patIdArr=array();
		$opdIdArr=array();
		$tstDeptArr=array();
		
		$aPatListCheck=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `aPatientList` WHERE `CashMemoNo`='$CashMemoNo'"));
		if(!$aPatListCheck)
		{
			mysqli_query($link,"INSERT INTO `aPatientList`(`name`, `age`, `sex`, `PatientNo`, `CashMemoNo`, `Ward`, `TestDetails`, `regDate`, `CashMemoDate`) VALUES ('$name','$aAge','$sex','$PatientNo','$CashMemoNo','$ward','$aTests','$regDate','$CashMemoDate')");
			$txt.="\nINSERT INTO `aPatientList`(`name`, `age`, `sex`, `PatientNo`, `CashMemoNo`, `Ward`, `TestDetails`, `regDate`, `CashMemoDate`) VALUES ('$name','$aAge','$sex','$PatientNo','$CashMemoNo','$ward','$aTests','$regDate','$CashMemoDate')";
			$aPatientList="INSERT INTO `aPatientList`(`name`, `age`, `sex`, `PatientNo`, `CashMemoNo`, `Ward`, `TestDetails`, `regDate`, `CashMemoDate`) VALUES ('$name','$aAge','$sex','$PatientNo','$CashMemoNo','$ward','$aTests','$regDate','$CashMemoDate')";
		}
		mysqli_query($link,"UPDATE `patient_info` SET `name`='$name', `sex`='$sex', `dob`='$dob', `age`='$age', `age_type`='$ageType' WHERE `uhid`='$PatientNo'");
		$txt.="\nUPDATE `patient_info` SET `name`='$name', `sex`='$sex', `dob`='$dob', `age`='$age', `age_type`='$ageType' WHERE `uhid`='$PatientNo'";
		
		$idsQry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `hospital_no`='$PatientNo' AND `cashMemoNo`='$CashMemoNo'");
		while($ids=mysqli_fetch_assoc($idsQry))
		{
			array_push($patIdArr,$ids['patient_id']);
			array_push($opdIdArr,$ids['opd_id']);
			
			$typ_Id=mysqli_fetch_array(mysqli_query($link,"SELECT DISTINCT a.`type_id` FROM `testmaster` a, `patient_test_details` b WHERE a.`testid`=b.`testid` AND b.`patient_id`='$ids[patient_id]' AND b.`opd_id`='$ids[opd_id]'"));
			$depName=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `test_department` WHERE `id`='$typ_Id[type_id]'"));
			array_push($tstDeptArr, $depName['name']);
		}
		
		
		text_query_json($txt,$CashMemoNo.".txt");
		text_query_json($aPatientList,$CashMemoNo."_patDet.txt");
		
		$val['status']=2;
		$val['pid']=$patIdArr;
		$val['opd']=$opdIdArr;
		$val['dept']=$tstDeptArr;
		$val['batch_no']="1";
		$val['msg']="Already Exists";
		
		/*
		$patient_id=$ids['patient_id'];
		$opd_id=$ids['opd_id'];
		
		$patCheck=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `patient_info` WHERE `patient_id`='$patient_id'"));
		if(!$patCheck)
		{
			mysqli_query($link,"INSERT INTO `patient_info`(`patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `occupation`, `gurdian_Occupation`, `income_id`, `education`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `file_create`, `user`, `date`, `time`) VALUES ('$patient_id','$PatientNo','$name','','','$sex','$dob','$age','$ageType','$phone','$address','','0','','0','','','0','','','','','4','65','','','','','0','$user','$rDate','$rTime')");
			$txt.="\nINSERT INTO `patient_info`(`patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `occupation`, `gurdian_Occupation`, `income_id`, `education`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `file_create`, `user`, `date`, `time`) VALUES ('$patient_id','$PatientNo','$name','','','$sex','$dob','$age','$ageType','$phone','$address','','0','','0','','','0','','','','','4','65','','','','','0','$user','$rDate','$rTime');";
		}
		
		$allTst=explode(",", $allTests);
		foreach($allTst as $test)
		{
			//$txt.="\n allTestsIds:".$testId;
			if($test)
			{
				$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test' "));
				$SampleId=$smpl['SampleId'];
				if(!$SampleId){$SampleId=0;}
				
				//------------------dept check--------------//
				$tstPre=mysqli_fetch_assoc(mysqli_query($link,"SELECT a.`type_id`, b.`prefix` FROM `testmaster` a, `test_department` b WHERE a.`type_id`=b.`id` AND a.`testid`='$test'"));
				$dept=$tstPre['type_id'];
				
				//$lastSl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `testDepartment$dept` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' ORDER BY `slno` DESC LIMIT 0,1"));
				
				//$prefNo="E".substr($tstPre['prefix'],0,1).$lastSl['slno'];
				$prefNo="";
				//-------------------------------//
				
				$testCheck=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$test'"));
				if(!$testCheck)
				{
					mysqli_query($link,"INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$test','$SampleId','0','0','','0','$date','$time','$user','2')");
					$txt.="\nINSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$test','$SampleId','0','0','','0','$date','$time','$user','2');";
				}
			}
		}
		//*/
	}
	else
	{
		$checkPatNo	=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `uhid`='$PatientNo' ORDER BY `slno` DESC LIMIT 0,1"));
		if($checkPatNo)
		{
			$patient_id=$checkPatNo['patient_id'];
		}
		else
		{
			//--------pid generator--------------//
			mysqli_query($link,"INSERT INTO `patient_id_generator`(`user`, `date`, `time`, `ip_addr`) VALUES ('$user','$date','$time','$ip_addr')");
			$txt.="\nINSERT INTO `patient_id_generator`(`user`, `date`, `time`, `ip_addr`) VALUES ('$user','$date','$time','$ip_addr');";
			
			$lastPid=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `patient_id_generator` WHERE `user`='$user' AND `date`='$date' AND `time`='$time' AND `ip_addr`='$ip_addr' ORDER BY `slno` DESC LIMIT 0,1"));
			$txt.="\nSELECT `slno` FROM `patient_id_generator` WHERE `user`='$user' AND `date`='$date' AND `time`='$time' AND `ip_addr`='$ip_addr' ORDER BY `slno` DESC LIMIT 0,1";
			$patient_id=(100+$lastPid['slno']);
			
			mysqli_query($link,"INSERT INTO `patient_info`(`patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `occupation`, `gurdian_Occupation`, `income_id`, `education`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `file_create`, `user`, `date`, `time`) VALUES ('$patient_id','$PatientNo','$name','','','$sex','$dob','$age','$ageType','$phone','$address','','0','','0','','','0','','','','','4','65','','','','','0','$user','$rDate','$rTime')");
			$txt.="\nINSERT INTO `patient_info`(`patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `occupation`, `gurdian_Occupation`, `income_id`, `education`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `file_create`, `user`, `date`, `time`) VALUES ('$patient_id','$PatientNo','$name','','','$sex','$dob','$age','$ageType','$phone','$address','','0','','0','','','0','','','','','4','65','','','','','0','$user','$rDate','$rTime');";
			
			mysqli_query($link,"INSERT INTO `aPatientList`(`name`, `age`, `sex`, `PatientNo`, `CashMemoNo`, `Ward`, `TestDetails`, `regDate`, `CashMemoDate`) VALUES ('$name','$aAge','$sex','$PatientNo','$CashMemoNo','$ward','$aTests','$regDate','$CashMemoDate')");
			$txt.="\nINSERT INTO `aPatientList`(`name`, `age`, `sex`, `PatientNo`, `CashMemoNo`, `Ward`, `TestDetails`, `regDate`, `CashMemoDate`) VALUES ('$name','$aAge','$sex','$PatientNo','$CashMemoNo','$ward','$aTests','$regDate','$CashMemoDate')";
			$aPatientList="INSERT INTO `aPatientList`(`name`, `age`, `sex`, `PatientNo`, `CashMemoNo`, `Ward`, `TestDetails`, `regDate`, `CashMemoDate`) VALUES ('$name','$aAge','$sex','$PatientNo','$CashMemoNo','$ward','$aTests','$regDate','$CashMemoDate')";
		}
		$aPatListCheck=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `aPatientList` WHERE `CashMemoNo`='$CashMemoNo'"));
		if(!$aPatListCheck)
		{
			mysqli_query($link,"INSERT INTO `aPatientList`(`name`, `age`, `sex`, `PatientNo`, `CashMemoNo`, `Ward`, `TestDetails`, `regDate`, `CashMemoDate`) VALUES ('$name','$aAge','$sex','$PatientNo','$CashMemoNo','$ward','$aTests','$regDate','$CashMemoDate')");
			$txt.="\nINSERT INTO `aPatientList`(`name`, `age`, `sex`, `PatientNo`, `CashMemoNo`, `Ward`, `TestDetails`, `regDate`, `CashMemoDate`) VALUES ('$name','$aAge','$sex','$PatientNo','$CashMemoNo','$ward','$aTests','$regDate','$CashMemoDate')";
			$aPatientList="INSERT INTO `aPatientList`(`name`, `age`, `sex`, `PatientNo`, `CashMemoNo`, `Ward`, `TestDetails`, `regDate`, `CashMemoDate`) VALUES ('$name','$aAge','$sex','$PatientNo','$CashMemoNo','$ward','$aTests','$regDate','$CashMemoDate')";
		}
		
		$patIdArr=array();
		$opdIdArr=array();
		$tstDeptArr=array();
		
		$allTst=explode(",", $allTests);
		$patAllTests=implode(",",$allTst);
		
		$disDeptQry=mysqli_query($link,"SELECT DISTINCT `type_id` FROM `testmaster` WHERE `testid` IN ($patAllTests)");
		while($disDept=mysqli_fetch_array($disDeptQry))
		{
			$depName=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `test_department` WHERE `id`='$disDept[type_id]'"));
			array_push($tstDeptArr, $depName['name']);
			
			$all_dept_Tests=array();
			$disDeptTests=mysqli_query($link,"SELECT `testid` FROM `testmaster` WHERE `testid` IN ($patAllTests) AND `type_id`='$disDept[type_id]'");
			while($disTests=mysqli_fetch_array($disDeptTests))
			{
				array_push($all_dept_Tests,$disTests['testid']);
			}
			
			if(mysqli_query($link,"INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `hospital_no`, `refbydoctorid`, `center_no`, `ward_id`, `wardName`, `cashMemoNo`, `branch_id`, `urgent`, `receipt_no`, `serial_no`, `reg_type`, `type`, `user`, `date`, `time`) VALUES ('$pid','$pid','$PatientNo','0','','0','$ward','$CashMemoNo','$branch_id','0','$patType','','$reg_type','2','$user','$date','$time')"))
			{
				$txt.="\nINSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `hospital_no`, `refbydoctorid`, `center_no`, `ward_id`, `wardName`, `cashMemoNo`, `branch_id`, `urgent`, `receipt_no`, `serial_no`, `reg_type`, `type`, `user`, `date`, `time`) VALUES ('$pid','$pid','$PatientNo','0','','0','$ward','$CashMemoNo','$branch_id','0','$patType','','$reg_type','2','$user','$date','$time');";
				
				
				//--------pin generator--------------//
				$dis_year=date("Y");
				$dis_month=date("m");
				$dis_year_sm=date("y");
				
				$c_y_m=$dis_year."-".$dis_month;
				
				$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_y_m%' "));
				if(!$c_data)
				{
					mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
					
					mysqli_query($link, " INSERT INTO `pin_generator`(`slno`, `patient_id`, `type`, `user`, `date`, `time`) VALUES ('100','0','$patient_reg_type','0','$date','$time') ");
				}

				mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$patient_id','$patient_reg_type','$user','$date','$time') ");
				//----------------------------------//
				mysqli_query($link,"INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$patient_id','2','$user','$date','$time')");
				$txt.="\nINSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$patient_id','2','$user','$date','$time')";
				$lastPin=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `pin_generator` WHERE `patient_id`='$patient_id' AND `user`='$user' AND `date`='$date' AND `time`='$time' ORDER BY `slno` DESC LIMIT 0,1"));
				$txt.="\nSELECT * FROM `pin_generator` WHERE `patient_id`='$patient_id' AND `user`='$user' AND `date`='$date' AND `time`='$time' ORDER BY `slno` DESC LIMIT 0,1";
				$opd_idd=(100+$lastPin['slno']);
				$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
				
				//------------date wise sl no---------//
				$dateSl=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) AS totl FROM `uhid_and_opdid` WHERE `wardName`='$ward' AND `date`='$date'"));
				$date_serial=($dateSl['totl']+1);
				
				mysqli_query($link,"UPDATE `uhid_and_opdid` SET `patient_id`='$patient_id', `opd_id`='$opd_id', `serial_no`='$date_serial' WHERE `patient_id`='$pid' AND `opd_id`='$pid' AND `wardName`='$ward' AND `hospital_no`='$PatientNo' AND `cashMemoNo`='$CashMemoNo' AND `date`='$date' AND `time`='$time' AND `user`='$user'");
				$txt.="\nUPDATE `uhid_and_opdid` SET `patient_id`='$patient_id', `opd_id`='$opd_id', `serial_no`='$date_serial' WHERE `patient_id`='$pid' AND `opd_id`='$pid' AND `wardName`='$ward' AND `hospital_no`='$PatientNo' AND `cashMemoNo`='$CashMemoNo' AND `date`='$date' AND `time`='$time' AND `user`='$user';";
				
				//$allTst=explode(",", $allTests);
				foreach($all_dept_Tests as $test)
				{
					//$txt.="\n allTestsIds:".$testId;
					if($test)
					{
						$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test' "));
						$SampleId=$smpl['SampleId'];
						if(!$SampleId){$SampleId=0;}
						
						//------------------dept check--------------//
						$tstPre=mysqli_fetch_assoc(mysqli_query($link,"SELECT a.`type_id`, b.`prefix` FROM `testmaster` a, `test_department` b WHERE a.`type_id`=b.`id` AND a.`testid`='$test'"));
						$dept=$tstPre['type_id'];
						
						//$lastSl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `testDepartment$dept` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' ORDER BY `slno` DESC LIMIT 0,1"));
						
						//$prefNo="E".substr($tstPre['prefix'],0,1).$lastSl['slno'];
						$prefNo="";
						//-------------------------------//
						
						mysqli_query($link,"INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$test','$SampleId','0','0','','0','$date','$time','$user','2')");
						$txt.="\nINSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$test','$SampleId','0','0','','0','$date','$time','$user','2');";
						
						//mysqli_query($link,"INSERT INTO `phlebo_sample`(`patient_id`, `opd_id`, `hosp_no`, `testid`, `sampleid`, `vaccu`, `user`, `time`, `date`) VALUES ('$patient_id','$opd_id','$PatientNo','$test','$SampleId','0','$user','$time','$date')");
						//$txt.="\nINSERT INTO `phlebo_sample`(`patient_id`, `opd_id`, `hosp_no`, `testid`, `sampleid`, `user`, `time`, `date`) VALUES ('$patient_id','$opd_id','$PatientNo','$test','$SampleId','$user','$time','$date')";
						/*
						
						$vaacQry=mysqli_query($link, " SELECT distinct `vac_id` FROM `test_vaccu` WHERE `testid`='$test' ");
						while($vcu=mysqli_fetch_assoc($vaacQry))
						{
							$vaccuId=$vcu['vac_id'];
							if(!$vaccuId){$vaccuId=0;}
							mysqli_query($link,"INSERT INTO `patient_sample_details`(`slno`, `patient_id`, `opd_id`, `sample_id`, `time`, `date`, `user`) values('0','$patient_id','$opd_id','$SampleId', '$time','$date', '$user')");
							$txt.="\nINSERT INTO `patient_sample_details`(`slno`, `patient_id`, `opd_id`, `sample_id`, `time`, `date`, `user`) values('0','$patient_id','$opd_id','$SampleId', '$time','$date', '$user')";
							
							mysqli_query($link, "insert into phlebo_sample(`patient_id`, `opd_id`, `hosp_no`, `testid`, `sampleid`, `vaccu`, `user`, `time`, `date`) values('$patient_id','$opd_id','$PatientNo','$test','$SampleId','$vaccuId','$user','$time','$date')");
							$txt.="\ninsert into phlebo_sample(`patient_id`, `opd_id`, `hosp_no`, `testid`, `sampleid`, `vaccu`, `user`, `time`, `date`) values('$patient_id','$opd_id','$PatientNo','$test','$SampleId','$vaccuId','$user','$time','$date')";
						}
						//*/
					}
				}
				array_push($patIdArr,$patient_id);
				array_push($opdIdArr,$opd_id);
			}
			else
			{
				$val['status']=0;
				$val['msg']="Error";
			}
		}
		
		text_query_json($txt,$CashMemoNo.".txt");
		text_query_json($aPatientList,$CashMemoNo."_patDet.txt");
		
		$val['status']=1;
		$val['msg']="Saved";
		$val['pid']=$patIdArr;
		$val['opd']=$opdIdArr;
		$val['dept']=$tstDeptArr;
		$val['batch_no']="1";
	}
	
	//$val['txt']=$txt;
	echo json_encode($val);
}

if($type==3)
{
	$pid	= $_POST['pid'];
	$opd	= $_POST['opd'];
	//print_r($_POST);
	
	$txt="";
	$vcc="";
	$vaccuIds=array();
	$txt="SELECT * FROM `patient_test_details` WHERE `patient_id`='$pid' AND`opd_id`='$opd'";
	$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$pid' AND`opd_id`='$opd'");
	while($r=mysqli_fetch_assoc($q))
	{
	$txt.="\nselect distinct vac_id from test_vaccu where testid='$r[testid]' and vac_id>0";
	$vaccu=mysqli_query($link,"select distinct vac_id from test_vaccu where testid='$r[testid]' and vac_id>0");
	while($vac=mysqli_fetch_array($vaccu))
	{
		$vcc.=",".$vac['vac_id'];
		array_push($vaccuIds,$vac['vac_id']); 
	}
	}
	//echo $vcc;
	$vaccuIds=array_unique($vaccuIds);
	//print_r($vaccuIds);
	foreach($vaccuIds as $vacId)
	{
		$vc=mysqli_fetch_assoc(mysqli_query($link,"SELECT `type` FROM `vaccu_master` WHERE `id`='$vacId'"));
		echo "<button type='button' class='btn'>$vc[type]</button>";
	}
}

if($type==4)
{
	//print_r($_POST);
	
	$pid	=$_POST['pid'];
	$opd	=$_POST['opd_id'];
	$ipd	=$_POST['ipd_id'];
	$vacc	=$_POST['vac'];
	$vacc_n	=$_POST['vac_n'];
	$hosp_no=$_POST['hosp_id'];
	$user	=$_POST['user'];
	$txt	="";
	$vac=explode("@@",$vacc_n);
	foreach($vac as $vc)
	{
		$chk_tst=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from phlebo_sample where patient_id='$pid' and opd_id='$opd' and testid='$t[testid]' and `vaccu`='$vc'"));
		$txt.="\nselect count(*) as tot from phlebo_sample where patient_id='$pid' and opd_id='$opd' and testid='$t[testid]' and `vaccu`='$vc'";
		if($chk_tst[tot]==0)
		{
			mysqli_query($link,"INSERT INTO `patient_sample_details`(`slno`, `patient_id`, `opd_id`, `sample_id`, `time`, `date`, `user`) values('0','$pid','$opd','$smp', '$time','$date', '$user')");
			$txt.="\nINSERT INTO `patient_sample_details`(`slno`, `patient_id`, `opd_id`, `sample_id`, `time`, `date`, `user`) values('0','$pid','$opd','$smp', '$time','$date', '$user')";
			mysqli_query($link,"INSERT INTO `phlebo_sample`(`patient_id`, `opd_id`, `hosp_no`, `testid`, `sampleid`, `user`, `time`, `date`) values('$pid','$opd','$hosp_no','$tst','$smp','$user','$time', '$date')");
			$txt.="\nINSERT INTO `phlebo_sample`(`patient_id`, `opd_id`, `hosp_no`, `testid`, `sampleid`, `user`, `time`, `date`) values('$pid','$opd','$hosp_no','$tst','$smp','$user','$time', '$date')";
		}
	}
	echo $txt;
}

if($type==5)
{
	//print_r($_POST);
	$oldMemoNo	=$_POST['cashMemo1'];
	$r			=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `aPatientList` WHERE `CashMemoNo`='$oldMemoNo'"));
	//print_r($r);
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<td colspan="2" style="padding:1px;"></td>
		</tr>
		<tr>
			<th>Patient No</th>
			<td>
				<?php echo $r['PatientNo'];?>
				<input type="hidden" id="oldHospNo" value="<?php echo $r['PatientNo'];?>" />
			</td>
		</tr>
		<tr>
			<th>Cash Memo No</th>
			<td>
				<?php echo $oldMemoNo;?>
				<input type="hidden" id="oldMemoDet" value="<?php echo $r['CashMemoNo'];?>" />
			</td>
		</tr>
		<tr>
			<th>Reg Date Time</th>
			<td><?php echo $r['CashMemoDate'];?></td>
		</tr>
		<tr>
			<th>Name</th>
			<td><?php echo $r['name'];?></td>
		</tr>
		<tr>
			<th>Age Sex</th>
			<td><?php echo $r['age']." ".$r['sex'];?></td>
		</tr>
		<tr>
			<th>Ward</th>
			<td><?php echo $r['Ward'];?></td>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;border-top:1px solid;border-bottom:1px solid;">Tests</th>
		</tr>
		<?php
		if(strpos($r['TestDetails'], ",")!==false)
		{
		$j=1;
		$allTests=explode(",", $r['TestDetails']);
		foreach($allTests as $alTest)
		{
			$tst=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `amtronTestList` WHERE `aTestId`='$alTest'"));
		?>
		<tr>
			<td colspan="2"><?php echo $j.". ".$tst['testname'];?></td>
		</tr>
		<?php
		$j++;
		}
		}
		else
		{
		if($r['TestDetail'])
		{
		$tst=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `amtronTestList` WHERE `aTestId`='$r[TestDetails]'"));
		?>
		<tr>
			<td colspan="2">1. <?php echo $tst['testname'];?></td>
		</tr>
		<?php
		}
		}
		?>
	</table>
	<?php
}

if($type==6)
{
	//print_r($_POST);
	$oldMemoNo	=$_POST['oldMemoNo'];
	$newMemoNo	=$_POST['newMemoNo'];
	$allTests	=$_POST['allTests'];
	$user		=$_POST['user'];
	
	$det		=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `cashMemoNo`='$oldMemoNo'"));
	$pid		=$det['patient_id'];
	$opd		=$det['opd_id'];
	$hosp_no	=$det['hospital_no'];
	
	$val=array();
	if($det)
	{
		$allTst=explode(",", $allTests);
		foreach($allTst as $tst)
		{
			if($tst)
			{
				$checkTest=mysqli_fetch_array(mysqli_query($link,"SELECT `slno` FROM `patient_test_details` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `testid`='$tst'"));
				if(!$checkTest)
				{
					$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$tst' "));
					$SampleId=$smpl['SampleId'];
					if(!$SampleId){$SampleId=0;}
					
					mysqli_query($link,"INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$pid','$opd','','1','$tst','$SampleId','0','0','','0','$date','$time','$user','2')");
				}
			}
		}
		$val['response']=1;
		$val['msg']="Merged";
		$val['pid']=$pid;
		$val['opd']=$opd;
		$val['batch_no']="1";
		$val['dept']="Biochemistry";
	}
	else
	{
		$val['response']=0;
		$val['msg']="Details Not Found";
	}
	echo json_encode($val);
}

if($type==22)
{
	$user=101;
	$lastDate		=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `test_sample_result_last_entry` ORDER BY `slno` DESC LIMIT 0,1"));
	$lastDateTime	=$lastDate['last_time'];
	$lastQueryTime	=$lastDate['last_query_time'];
	$currDateTime	=date("YmdHis");
	$fetchTimeDiff	=($currDateTime-$lastQueryTime);
	$tot=0;
	//echo $currDateTime." - ".$lastQueryTime." = ".$fetchTimeDiff;
	if($_SESSION['fetchPatient'])
	{
		$respMsg	="Message : Fetch under process";
	}
	else if($fetchTimeDiff<3000)
	{
		$respMsg	="Message : Short time gapping ".$fetchTimeDiff;
	}
	else
	{
	//$FromDate		="20/11/2022 13:00:01";
	//$ToDate		="20/11/2022 14:30:00";
	$_SESSION['fetchPatient']=$currDateTime;
	$FromDate		=date("d/m/Y H:i:s", strtotime($lastDateTime."+1 second"));
	$ToDate			=date("d/m/Y H:i:s", strtotime($lastDateTime."+15 minute"));
	
	$request_headers = ["client_id:LIS", "client_secret:Lis@123", "grant_type:client_credentials", "FromDate:$FromDate","ToDate:$ToDate"];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url."GetPatientDataByDate");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
	$server_output = curl_exec($ch);
	curl_close($ch);
	$server_output;

	//$r=json_decode($server_output, true);
	
	//$result=$r['result'];
	//print_r($result);
	
	$json 			=json_decode($server_output,true);
	//print_r($json);
	$resArray 		=$json['result'];
	$BarcodeCount	=$resArray['BarcodeCount'];
	$BarcodeDetails	=$resArray['BarcodeDetails'];
	$metatdata		=$json['metatdata'];
	$msg			=$metatdata['message'];
	$code			=$metatdata['code'];
	$respMsg		="Message : Fetched";
	//print_r($BarcodeDetails);
	//echo sizeof($BarcodeDetails)."<br/>";
	//*
	//if(sizeof($BarcodeDetails)>0)
	
	if($code==200)
	{
		foreach($BarcodeDetails as $key=>$val)
		{
			//echo $val['Barcode']." -- ".$key." ".$val."<br/>";
			$barcode	=$val['Barcode'];
			$pid		=$val['PatientNo'];
			$opd_id		=$val['PatientNo'];
			$name		=$val['Name'];
			$age		=$val['Age'];
			$age_str	=$val['Age'];
			$ward		=$val['Ward'];
			$sex		=$val['Gender'];
			$TestDetails=$val['TestDetails'];
			$testid		=$val['testId'];
			$paramid	=$val['paramId'];
			$rDate		=$val['RegistrationDate'];
			$regDate	=date("Y-m-d", strtotime($rDate));
			$regTime	=date("H:i:s", strtotime($rDate));
			$dob		=strToDOB($age);
			$age		=($date-$dob);
			$age_type	="Years";
			$batch_no	=1;
			$sample_id	=0;
			$dept		=0;
			
			mysqli_query($link,"INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$pid','$pid','$name','$sex','$dob','$age','$age_type','$phone','$rDate','$user','$regDate','$regTime')");
			//echo "INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$pid','$pid','$name','$sex','$dob','$age','$age_type','$phone','$address','$user','$date','$time')<br/>";
			mysqli_query($link,"INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `ward`, `dept`, `hosp_no`, `bill_no`, `date`, `time`, `user`, `type`, `pat_type`, `date_serial`) VALUES ('$pid','$opd_id','$ward','$dept','$pid','','$regDate','$regTime','$user','2','0','0')");
			//echo "INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `ward`, `dept`, `hosp_no`, `bill_no`, `date`, `time`, `user`, `type`, `pat_type`, `date_serial`) VALUES ('$pid','$opd_id','$ward','$dept','$pid','','$date','$time','$user','2','0','0')<br/>";
			foreach($TestDetails as $ky=>$vl)
			{
				//echo $ky." : ".$vl."<br/>";
				$aParamId	=$vl['ParamId'];
				$aTestId	=$vl['TestId'];
				$TestId		=$vl['TestId'];
				//$par		=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `paramid_link` WHERE `aParId`='$ParamId'"));
				//$ParamId	=$par['pParId'];
				
				mysqli_query($link,"INSERT INTO `a_patient_test_details`(`PatientNo`, `patient_name`, `barcode_id`, `age`, `gender`, `ward`, `testId`, `paramId`) VALUES ('$pid','$name','$barcode','$age_str','$sex','$ward','$aTestId','$aParamId')");
				
				$tst		=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `testmaster_link` WHERE `aTestId`='$TestId'"));
				$TestId		=$tst['pTestId'];
				
				$testSample	=mysqli_fetch_assoc(mysqli_query($link,"SELECT `SampleId` FROM `TestSample` WHERE `TestId`='$TestId'"));
				$sample_id	=$testSample['SampleId'];
				$testParamQry=mysqli_query($link,"SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$TestId' ORDER BY `sequence`");
				while($testParam=mysqli_fetch_assoc($testParamQry))
				{
					$ParamId=$testParam['ParamaterId'];
					//$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `test_sample_result_data` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `barcode_id`='$barcode' AND `sample_id`='$sample_id' AND `testid`='$TestId' AND `paramid`='$ParamId'"));
					$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `test_sample_result_data` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `barcode_id`='$barcode' AND `paramid`='$ParamId'"));
					
					$qry="INSERT INTO `test_sample_result_data`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `result`, `time`, `date`, `user`, `machine_name`) VALUES ('$pid','$opd_id','$ipd_id','$batch_no','$barcode','','$sample_id','0','$TestId','$ParamId','','$time','$date','$user','')";
					if(!$chk)
					{
						//echo $qry."<br/>";
						mysqli_query($link,$qry);
					}
					//echo $qry."<br/>";
				}
			}
			$tot++;
			//echo "<br/>";
			//print_r($TestDetails);
		}
	}
	
	if($code==200 || ($tot==$BarcodeCount) || ($code==204 && $msg=="no data available"))
	{
		$lastDateTimeNew=date("Y-m-d H:i:s", strtotime($lastDateTime."+15 minute"));
		$last_query_time=date("YmdHis", strtotime($lastDateTimeNew));
		mysqli_query($link,"INSERT INTO `test_sample_result_last_entry`(`last_time`, `last_query_time`) VALUES ('$lastDateTimeNew','$last_query_time')");
		unset($_SESSION['fetchPatient']);
	}
	}
	//*/
	echo "Total : ".$tot." ".$respMsg;
}

if($type==33)
{
	$BarcodeId	= $_POST['BarcodeId'];
	$Testid		= $_POST['Testid'];
	$ParamId	= $_POST['ParamId'];
	$TestResult	= $_POST['TestResult'];
	
	$BarcodeId="C12308693SE";
	$Testid="1143";
	$ParamId="6";
	$TestResult="9876";

	$request_headers = ["client_id:LIS", "client_secret:Lis@123", "grant_type:client_credentials", "BarcodeId:$BarcodeId", "Testid:$Testid", "ParamId:$ParamId", "TestResult:$TestResult"];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url."SubmitTestReport");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);


	$server_output = curl_exec($ch);

	curl_close ($ch);

	echo $server_output;
}

if($type==44)
{
	$last_fetch=mysqli_fetch_assoc(mysqli_query($link,"SELECT `last_time` FROM `test_sample_result_last_entry` ORDER BY `slno` DESC LIMIT 0,1"));
	echo "Last Fetch :".convert_date($last_fetch['last_time'])." ".convert_time($last_fetch['last_time']);
}

if($type==55)
{
	$startTime		=$_POST['startTime'];
	$endTime		=$_POST['endTime'];
	$FromDate		=date("Y-m-d")." ".$startTime.":00";
	$ToDate			=date("Y-m-d")." ".$endTime.":00";
	
	$FromDate		=date("d/m/Y H:i:s", strtotime($FromDate."+1 second"));
	$ToDate			=date("d/m/Y H:i:s", strtotime($ToDate));
	$user			="101";
	
	$request_headers = ["client_id:LIS", "client_secret:Lis@123", "grant_type:client_credentials", "FromDate:$FromDate","ToDate:$ToDate"];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url."GetPatientDataByDate");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
	$server_output = curl_exec($ch);
	curl_close($ch);
	$server_output;

	//$r=json_decode($server_output, true);
	
	//$result=$r['result'];
	//print_r($result);
	
	$json 			=json_decode($server_output,true);
	//print_r($json);
	$resArray 		=$json['result'];
	$BarcodeDetails	=$resArray['BarcodeDetails'];
	$metatdata		=$json['metatdata'];
	$msg			=$metatdata['message'];
	$code			=$metatdata['code'];
	$respMsg		="Message : Fetched";
	//print_r($BarcodeDetails);
	//echo sizeof($BarcodeDetails)."<br/>";
	//*
	//if(sizeof($BarcodeDetails)>0)
	
	if($code==200)
	{
		foreach($BarcodeDetails as $key=>$val)
		{
			//echo $val['Barcode']." -- ".$key." ".$val."<br/>";
			$barcode	=$val['Barcode'];
			$pid		=$val['PatientNo'];
			$opd_id		=$val['PatientNo'];
			$name		=$val['Name'];
			$age		=$val['Age'];
			$age_str	=$val['Age'];
			$ward		=$val['Ward'];
			$sex		=$val['Gender'];
			$TestDetails=$val['TestDetails'];
			$testid		=$val['testId'];
			$paramid	=$val['paramId'];
			$rDate		=$val['RegistrationDate'];
			$regDate	=date("Y-m-d", strtotime($rDate));
			$regTime	=date("H:i:s", strtotime($rDate));
			$dob		=strToDOB($age);
			$age		=($date-$dob);
			$age_type	="Years";
			$batch_no	=1;
			$sample_id	=0;
			$dept		=0;
			
			mysqli_query($link,"INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$pid','$pid','$name','$sex','$dob','$age','$age_type','$phone','$rDate','$user','$regDate','$regTime')");
			//echo "INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$pid','$pid','$name','$sex','$dob','$age','$age_type','$phone','$address','$user','$date','$time')<br/>";
			mysqli_query($link,"INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `ward`, `dept`, `hosp_no`, `bill_no`, `date`, `time`, `user`, `type`, `pat_type`, `date_serial`) VALUES ('$pid','$opd_id','$ward','$dept','$pid','','$regDate','$regTime','$user','2','0','0')");
			//echo "INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `ward`, `dept`, `hosp_no`, `bill_no`, `date`, `time`, `user`, `type`, `pat_type`, `date_serial`) VALUES ('$pid','$opd_id','$ward','$dept','$pid','','$date','$time','$user','2','0','0')<br/>";
			foreach($TestDetails as $ky=>$vl)
			{
				//echo $ky." : ".$vl."<br/>";
				$aParamId	=$vl['ParamId'];
				$aTestId	=$vl['TestId'];
				$TestId		=$vl['TestId'];
				//$par		=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `paramid_link` WHERE `aParId`='$ParamId'"));
				//$ParamId	=$par['pParId'];
				
				mysqli_query($link,"INSERT INTO `a_patient_test_details`(`PatientNo`, `patient_name`, `barcode_id`, `age`, `gender`, `ward`, `testId`, `paramId`) VALUES ('$pid','$name','$barcode','$age_str','$sex','$ward','$aTestId','$aParamId')");
				
				$tst		=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `testmaster_link` WHERE `aTestId`='$TestId'"));
				$TestId		=$tst['pTestId'];
				
				$testSample	=mysqli_fetch_assoc(mysqli_query($link,"SELECT `SampleId` FROM `TestSample` WHERE `TestId`='$TestId'"));
				$sample_id	=$testSample['SampleId'];
				$testParamQry=mysqli_query($link,"SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$TestId' ORDER BY `sequence`");
				while($testParam=mysqli_fetch_assoc($testParamQry))
				{
					$ParamId=$testParam['ParamaterId'];
					//$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `test_sample_result_data` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `barcode_id`='$barcode' AND `sample_id`='$sample_id' AND `testid`='$TestId' AND `paramid`='$ParamId'"));
					$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `test_sample_result_data` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `barcode_id`='$barcode' AND `paramid`='$ParamId'"));
					$qry="INSERT INTO `test_sample_result_data`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `result`, `time`, `date`, `user`, `machine_name`) VALUES ('$pid','$opd_id','$ipd_id','$batch_no','$barcode','','$sample_id','0','$TestId','$ParamId','','$time','$date','$user','')";
					if(!$chk)
					{
						//echo $qry."<br/>";
						mysqli_query($link,$qry);
					}
					//echo $qry."<br/>";
				}
			}
			$tot++;
			//echo "<br/>";
			//print_r($TestDetails);
		}
	}
	
	//*/
	echo "Total : ".$tot." ".$respMsg;
}
?>
