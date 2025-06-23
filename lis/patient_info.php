<?php
session_start();
include('../includes/connection.php');
$date	=date("Y-m-d");
$time	=date("H:i:s");
$user	=101;


function text_query_json($txt,$file)
{
	$txt_file="../barcodes/".$file;
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

$aurl	=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `amtron_base_url`"));
$url	=$aurl['base_url'];

//*
$last	=mysqli_fetch_array(mysqli_query($link,"SELECT `slno` FROM `patient_info_last_fetch` ORDER BY `sn` DESC LIMIT 1"));
$slno	=$last['slno'];
if(!$slno)
{
	$slno=1;
}
$lastSl=0;


$counts=0;
echo "SELECT DISTINCT `slno`,`PatientNo` FROM `a_patient_test_details` WHERE `slno`>='$slno' ORDER BY `slno` LIMIT 100";
$q		=mysqli_query($link,"SELECT DISTINCT `slno`,`PatientNo` FROM `a_patient_test_details` WHERE `slno`>='$slno' ORDER BY `slno` LIMIT 100");
while($r=mysqli_fetch_array($q))
{
	//echo "<br/>SELECT * FROM `a_patient_test_details` WHERE `slno`='$r[slno]' AND `PatientNo`='$r[PatientNo]'";
	$det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `a_patient_test_details` WHERE `slno`='$r[slno]' AND `PatientNo`='$r[PatientNo]' ORDER BY `slno` DESC"));
	$lastSl=$det['slno'];
	//echo "<br/>";
	$dob		=strToDOB($det['age']);
	$age		=($date-$dob);
	$age_type	="Years";
	
	//echo "<br/>---- SELECT `slno` FROM `patient_info` WHERE `patient_id`='$r[PatientNo]'";
	$patChk=mysqli_fetch_array(mysqli_query($link,"SELECT `slno` FROM `patient_info` WHERE `patient_id`='$r[PatientNo]'"));
	if(!$patChk)
	{
		$dt		=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `test_sample_result_data` WHERE `patient_id`='$r[PatientNo]' AND `barcode_id`='$det[barcode_id]' ORDER BY `slno` LIMIT 1"));
		//echo "<br/>SELECT `date`,`time` FROM `test_sample_result_data` WHERE `patient_id`='$r[PatientNo]' AND `barcode_id`='$det[barcode_id]' ORDER BY `slno` LIMIT 1";
		mysqli_query($link,"INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$r[PatientNo]','$r[PatientNo]','$det[patient_name]','$det[gender]','$dob','$age','$age_type','','','$user','$dt[date]','$dt[time]')");
		//echo "<br/>INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$r[PatientNo]','$r[PatientNo]','$det[patient_name]','$det[gender]','$dob','$age','$age_type','','','$user','$dt[date]','$dt[time]')";
		//echo "<br/><br/>";
	}
	//print_r($det);
	//echo "<br/>".$r['PatientNo'];
	$counts++;
}
echo "<br/><br/>";

if($counts)
{
	mysqli_query($link,"INSERT INTO `patient_info_last_fetch`(`slno`, `date`, `time`) VALUES ('$lastSl','$date','$time')");
}

//*/


//~ $curl = curl_init();

//~ curl_setopt_array($curl, array(
  //~ CURLOPT_URL => $url,
  //~ CURLOPT_RETURNTRANSFER => true,
  //~ CURLOPT_ENCODING => '',
  //~ CURLOPT_MAXREDIRS => 10,
  //~ CURLOPT_TIMEOUT => 0,
  //~ CURLOPT_FOLLOWLOCATION => true,
  //~ CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  //~ CURLOPT_CUSTOMREQUEST => 'POST',
  //~ //CURLOPT_POSTFIELDS => array('empId' => 'APP100101','secretKey' => '123456'),
//~ ));

//~ $response = curl_exec($curl);
//~ echo $response;
//~ curl_close($curl);

/*
$lastDate		=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `test_sample_result_last_entry` ORDER BY `slno` DESC LIMIT 0,1"));
$lastDateTime	=$lastDate['last_time'];
$lastQueryTime	=$lastDate['last_query_time'];
$currDateTime	=date("YmdHis");
$fetchTimeDiff	=($currDateTime-$lastQueryTime);
$_SESSION['fetchPatient']=$fetchTimeDiff;
$FromDate="16/11/2024 09:00:01";
$ToDate="16/11/2024 09:05:00";

$lastFetch=strtotime($lastDateTime);
$currTime=mktime();

//~ $FromDate	=date("d/m/Y H:i:s", strtotime($lastDateTime."+1 second"));
//~ $ToDate		=date("d/m/Y H:i:s", strtotime($lastDateTime."+ $nextMinute minute"));
$request_headers = ["client_id:LIS", "client_secret:Lis@123", "grant_type:client_credentials", "FromDate:$FromDate","ToDate:$ToDate"];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url."GetPatientDataByDate");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
$server_output = curl_exec($ch);
curl_close($ch);
//echo $server_output;

//$r=json_decode($server_output, true);
$json = json_decode($server_output,true);
//print_r($json);
//print_r($result);
$resArray = $json['result'];
$BarcodeDetails	=$resArray['BarcodeDetails'];
//print_r($BarcodeDetails);
//$json_file=json_encode($BarcodeDetails,true);
//$file=mktime().".json";
//text_query_json($json_file,$file);
//$json = json_decode($server_output,true);
foreach($BarcodeDetails as $key=>$val)
{
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
	$jsonFile	=array();
	array_push($jsonFile,$key);
	//$jsonFile	=json_encode($val);
	$file		=$val['PatientNo']."111.json";
	text_query_json($jsonFile,$file);
	//mysqli_query($link,"INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$pid','$pid','$name','$sex','$dob','$age','$age_type','$phone','$rDate','$user','$regDate','$regTime')");
	echo "<br/>INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$pid','$pid','$name','$sex','$dob','$age','$age_type','$phone','$rDate','$user','$regDate','$regTime')";
}
//*/

//echo $response;
$file=mktime().".json";
// Check for cURL errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Save response to a file
    $file = 'api_response.json';
    file_put_contents("../barcodes/".$file, $response);
    echo "Response saved to $file";
}

// Close cURL session
curl_close($ch);
?>
