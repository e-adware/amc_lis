<?php
include("../includes/connection.php");
$date=date("Y-m-d");
$time=date("H:i:s");



header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 0);
ini_set('error_log', 'error.log'); // Change to your desired log file



// Set the maximum execution time of the script to 30 seconds
set_time_limit(30);

$FromDate	=$_GET['FromDate'];
$ToDate		=$_GET['ToDate'];

//$FromDate	="28/04/2025 09:00:01";
//$ToDate		="28/04/2025 09:05:00";

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


$aurl=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `amtron_base_url`"));
$url=$aurl['base_url'];

$request_headers = ["client_id:LIS", "client_secret:Lis@123", "grant_type:client_credentials", "FromDate:$FromDate","ToDate:$ToDate"];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url."GetPatientDataByDate");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
$server_output = curl_exec($ch);
curl_close($ch);

echo $server_output;
//echo $json = json_decode($server_output,true);
//print_r($json);
?>
