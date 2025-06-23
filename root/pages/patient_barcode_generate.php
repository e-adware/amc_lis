<html>
<head>

</head>
<body>

<h2>Generating Barcode</h2>

<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$pid	=base64_decode($_GET['pId']);
$opd	=base64_decode($_GET['oPd']);
$ipd	=base64_decode($_GET['iPd']);
$user	=$_GET['uSr'];

$date=date('Y-m-d');
$time=date("H:i:s");

if($opd)
{
	$pin=$opd;
}
if($ipd)
{
	$pin=$ipd;
}

$pat_info	=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$pid'"));
$hosp_no	=$pat_info['patient_id'];
$sex		=$pat_info['sex'][0];

$pat_reg	=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where `patient_id`='$pid' AND `opd_id`='$opd'"));
$reg_dt		=mysqli_fetch_array(mysqli_query($link,"SELECT `CashMemoDate` FROM `aPatientList` WHERE `PatientNo`='$pat_reg[hospital_no]' AND `CashMemoNo`='$pat_reg[cashMemoNo]'"));
if($reg_dt)
{
$reg_date	=$reg_dt["CashMemoDate"];
}
else
{
$reg_date	=date("d/m/y", strtotime($pat_reg['date']));
}
$slNo		="";
$slnoQry	=mysqli_query($link,"SELECT DISTINCT `dept_serial` FROM `patient_test_details` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `dept_serial`!='' ORDER BY `slno` DESC LIMIt 0,1");
while($sl=mysqli_fetch_array($slnoQry))
{
	if($slNo)
	{
		$slNo.=", ".$sl['dept_serial'];
	}
	else
	{
		$slNo=$sl['dept_serial'];
	}
}

$bCodeNums="";
$bCodeQry=mysqli_query($link,"SELECT DISTINCT `barcode_id` FROM `test_sample_result` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `barcode_id`!=''");
while($bCodes=mysqli_fetch_array($bCodeQry))
{
	if($bCodeNums)
	{
		$bCodeNums.=",".$bCodes['barcode_id'];
	}
	else
	{
		$bCodeNums=$bCodes['barcode_id'];
	}
}

$dailySl=mysqli_fetch_array(mysqli_query($link,"SELECT `daily_slno` FROM `phlebo_sample_status` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `daily_slno`!=''"));
$dailySlno=$dailySl['daily_slno'];

//if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
$age=$pat_info["age"]." ".$pat_info["age_type"];

$barcode_str="name=".$pat_info['name']."&age=".$age."&sex=".$sex."&pin=".$pin."&patTyp=".$pat_reg['receipt_no']."&slNo=".$slNo."&hosp=".$hosp_no."&regDt=".$reg_date."&cashMemoNo=".$opd."&bCodeNums=".$bCodeNums."&dailySlno=".$dailySlno;
//echo $barcode_str;

$target_file="../../js_print/patDetPrint.php?".$barcode_str;
	
?>
	<script>
		window.location="<?php echo $target_file;?>";
	</script>
<script>//window.close();</script>
</body>
</html>
