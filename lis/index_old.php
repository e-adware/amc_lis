<?php
session_start();
include'../includes/connection.php';
$date=date("Y-m-d");
$time=date("H:i:s");

function strToDOB($age)
{
	$a=explode("Y",$age);
	$yr=$a[0];
	$rm=$a[1];
	$m=explode("M",$rm);
	$mn=$m[0];
	$rd=$m[1];
	$d=explode("D",$rd);
	$dy=$d[0];
	$dob=date("Y-m-d", strtotime("-".$dy." day"));
	$dob=date("Y-m-d", strtotime($dob." -".$mn." month"));
	$dob=date("Y-m-d", strtotime($dob." -".$yr." year"));
	return $dob;
}

$aurl=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `amtron_base_url`"));
$url=$aurl['base_url'];
//$url="http://192.168.1.3/GMCHAPI/API/";



$user=101;
$lastDate	=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `test_sample_result_last_entry` ORDER BY `slno` DESC LIMIT 0,1"));
$lastDateTime=$lastDate['last_time'];
$lastQueryTime	=$lastDate['last_query_time'];
$currDateTime	=date("YmdHis");
$fetchTimeDiff	=($currDateTime-$lastQueryTime);
$_SESSION['fetchPatient']=$fetchTimeDiff;
//$FromDate="16/11/2022 09:00:00";
//$ToDate="16/11/2022 15:30:00";
$FromDate	=date("d/m/Y H:i:s", strtotime($lastDateTime."+1 second"));
$ToDate		=date("d/m/Y H:i:s", strtotime($lastDateTime."+1 minute"));

$request_headers = ["client_id:LIS", "client_secret:Lis@123", "grant_type:client_credentials", "FromDate:$FromDate","ToDate:$ToDate"];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url."GetPatientDataByDate");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
$server_output = curl_exec($ch);
curl_close($ch);
//$server_output;

//$r=json_decode($server_output, true);

//$result=$r['result'];
//print_r($result);

$json = json_decode($server_output,true);
//print_r($json);
$resArray = $json['result'];
$BarcodeCount	=$resArray['BarcodeCount'];
$BarcodeDetails	=$resArray['BarcodeDetails'];
$metatdata		=$json['metatdata'];
$msg			=$metatdata['message'];
$code			=$metatdata['code'];
//print_r($BarcodeDetails);
//echo sizeof($BarcodeDetails)."<br/>";
//*
$tot=0;
//if(sizeof($BarcodeDetails)>0)

if($code==200)
{
foreach($BarcodeDetails as $key=>$val)
{
	//echo $val['Barcode']." -- ".$key." ".$val."<br/>";
	//include("opd_id_generator.php");
	$barcode	=$val['Barcode'];
	$pid		=$val['PatientNo'];
	$opd_id		=$val['PatientNo'];
	if(!$opd_id)
	{
	//$opd_id		=$val['PatientNo'];
	}
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
	$bcd		=substr($barcode, 0, -2);
	$newCode	=preg_replace('/\d/', '', $bcd);
	
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
		
		mysqli_query($link,"INSERT INTO `a_patient_test_details`(`PatientNo`, `patient_name`, `barcode_id`, `age`, `gender`, `ward`, `testId`, `paramId`, `date`, `time`) VALUES ('$pid','$name','$barcode','$age_str','$sex','$ward','$aTestId','$aParamId','$date','$time')");
		
		$tst		=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `testmaster_link` WHERE `aTestId`='$TestId'"));
		$TestId		=$tst['pTestId'];
		
		$testSample	=mysqli_fetch_assoc(mysqli_query($link,"SELECT `SampleId` FROM `TestSample` WHERE `TestId`='$TestId'"));
		$sample_id	=$testSample['SampleId'];
		$testParamQry=mysqli_query($link,"SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$TestId' ORDER BY `sequence`");
		while($testParam=mysqli_fetch_assoc($testParamQry))
		{
			$ParamId=$testParam['ParamaterId'];
			$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `test_sample_result_data` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `barcode_id`='$barcode' AND `sample_id`='$sample_id' AND `testid`='$TestId' AND `paramid`='$ParamId'"));
			$qry="INSERT INTO `test_sample_result_data`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `result`, `time`, `date`, `user`, `machine_name`) VALUES ('$pid','$opd_id','$ipd_id','$batch_no','$barcode','$newCode','$sample_id','0','$TestId','$ParamId','','$time','$date','$user','')";
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
	$lastDateTimeNew=date("Y-m-d H:i:s", strtotime($lastDateTime."+1 minute"));
	$last_query_time=date("YmdHis", strtotime($lastDateTimeNew));
	mysqli_query($link,"INSERT INTO `test_sample_result_last_entry`(`last_time`, `last_query_time`, `patCount`) VALUES ('$lastDateTimeNew','$last_query_time','$BarcodeCount')");
	unset($_SESSION['fetchPatient']);
}
?>
