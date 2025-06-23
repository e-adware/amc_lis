<?php
include'../includes/connection.php';
$date=date("Y-m-d");
$time=date("H:i:s");
$mkTime=mktime();

$aurl=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `amtron_base_url`"));
$url=$aurl['base_url'];
//$url="http://192.168.1.3/GMCHAPI/API/";

function text_query($txt)
{
	if($txt)
	{
		$myfile = file_put_contents('barcodes.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
}

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

//SELECT * FROM `patient_info` WHERE `patient_id` NOT IN (SELECT DISTINCT `patient_id` FROM `test_sample_result_data`) 
$entryDate=$date;
//$entryDate="2023-05-31";
$time=date('H:i:s', strtotime('-10 minutes'));
$j=1;
$qry="SELECT DISTINCT `barcode_id` FROM `a_patient_test_details` WHERE `date`='$entryDate' AND `barcode_id` NOT IN (SELECT DISTINCT `barcode_id` FROM `test_sample_result_data` WHERE `date`='$entryDate')";
//echo $qry;
//$q=mysqli_query($link,"SELECT DISTINCT `barcode_id` FROM `test_sample_result_data` WHERE `date`='$entryDate'");
//echo "SELECT DISTINCT `barcode_id` FROM `a_patient_test_details` WHERE `date`='$entryDate' AND `time`<'$time'";
$barcodes=array();
$q=mysqli_query($link,$qry);
while($r=mysqli_fetch_assoc($q))
{
	array_push($barcodes,$r['barcode_id']);
}

//print_r($barcodes);

foreach($barcodes as $BarcodeId)
{
	text_query($BarcodeId."-".$mkTime);
	//echo $BarcodeId;
	
	$request_headers = ["client_id:LIS", "client_secret:Lis@123", "grant_type:client_credentials", "BarcodeId:$BarcodeId"];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url."GetPatientData");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);


	$server_output = curl_exec($ch);

	curl_close ($ch);
	
	
	$json = json_decode($server_output,true);
	$resArray	=$json['result'];
	include("opd_id_generator.php");
	$name		=$resArray['Name'];
	$Age		=$resArray['Age'];
	$age_str	=$resArray['Age'];
	$ward		=$resArray['Ward'];
	$sex		=$resArray['Gender'];
	$pid		=$resArray['PatientNo'];
	if(!$opd_id)
	{
	$opd_id		=$val['PatientNo'];
	}
	$TestDetails=$resArray['TestDetails'];
	$dob		=strToDOB($Age);
	$age		=($date-$dob);
	$age_type	="Years";
	$batch_no	=1;
	$sample_id	=0;
	$dept		=0;
	$user		=102;
	$bcd		=substr($BarcodeId, 0, -2);
	$newCode	=preg_replace('/\d/', '', $bcd);
	
	$metatdata	=$json['metatdata'];
	$code		=$metatdata['code'];
	//echo $code;
	if($code==200)
	{
		mysqli_query($link,"INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$pid','$pid','$name','$sex','$dob','$age','$age_type','$phone','$address','$user','$date','$time')");
		mysqli_query($link,"INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `ward`, `dept`, `hosp_no`, `bill_no`, `date`, `time`, `user`, `type`, `pat_type`, `date_serial`) VALUES ('$pid','$opd_id','$ward','$dept','$pid','','$date','$time','$user','2','0','0')");
		foreach($TestDetails as $ky=>$vl)
		{
			$ParamId	=$vl['ParamId'];
			$TestId		=$vl['TestId'];
			$tst		=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `testmaster_link` WHERE `aTestId`='$TestId'"));
			$TestId		=$tst['pTestId'];
			
			$testSample	=mysqli_fetch_assoc(mysqli_query($link,"SELECT `SampleId` FROM `TestSample` WHERE `TestId`='$TestId'"));
			$sample_id	=$testSample['SampleId'];
			$testParamQry=mysqli_query($link,"SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$TestId' ORDER BY `sequence`");
			while($testParam=mysqli_fetch_assoc($testParamQry))
			{
				$ParamId=$testParam['ParamaterId'];
				$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `test_sample_result_data` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `barcode_id`='$BarcodeId' AND `sample_id`='$sample_id' AND `testid`='$TestId' AND `paramid`='$ParamId'"));
				$qry="INSERT INTO `test_sample_result_data`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `result`, `time`, `date`, `user`, `machine_name`) VALUES ('$pid','$opd_id','$ipd_id','$batch_no','$BarcodeId','$newCode','$sample_id','0','$TestId','$ParamId','','$time','$date','$user','')";
				if(!$chk)
				{
					//echo $qry."<br/>";
					mysqli_query($link,$qry);
				}
				//echo $qry."<br/>";
			}
		}
	}
}
?>
