<?php
session_start();
$emp_id=$_SESSION["emp_id"];
$date=date("Y-m-d");

include('../../includes/connection.php');
include('../../includes/global.function.php');

$user=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$emp_id' "));

$uhid=$_GET['uhid'];
$pin=$opd_id=$_GET['visitid'];
$user_val=$_GET["user"];

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$cmpny=mysqli_fetch_array(mysqli_query($link, "select centrename from centremaster where centreno='$centr'"));
$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
$pat_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `bill_no`,`date`,`time` FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' AND `typeofpayment`='A' "));

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$appointment_qry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' "));
$appointment_no=$appointment_qry["appointment_no"];
$appointment_no = str_pad($appointment_no,2,"0",STR_PAD_LEFT);

$app_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id`,`room_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appointment_qry[consultantdoctorid]' "));
$doc_dept=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$app_doc[dept_id]' "));
$opd_room=mysqli_fetch_array(mysqli_query($link, " SELECT `room_name` FROM `opd_doctor_room` WHERE `room_id`='$app_doc[room_id]' "));

$visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `visit_fee` FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' "));
if($visit_fee["visit_fee"]==0)
{
	$visit_fee=0;
	$visit_type="Free";
}else
{
	$visit_fee=$visit_fee["visit_fee"];
	$visit_fee_num=mysqli_num_rows(mysqli_query($link, " SELECT `visit_fee` FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `visit_fee`>0 "));

	function numToOrdinalWord($num)
	{
		$first_word = array('eth','First','Second','Third','Fouth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth','Elevents','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth','Eighteenth','Nineteenth','Twentieth');
		$second_word =array('','','Twenty','Thirthy','Forty','Fifty');

		if($num <= 20)
			return $first_word[$num];

		$first_num = substr($num,-1,1);
		$second_num = substr($num,-2,1);

		return $string = str_replace('y-eth','ieth',$second_word[$second_num].'-'.$first_word[$first_num]);
	}
	//echo $visit_fee_num;
	$visit_type=numToOrdinalWord($visit_fee_num)." visit";
}

$check_last_regd_fee_qry=mysqli_query($link, " SELECT `date` FROM `appointment_book` WHERE `patient_id`='$uhid' order by `slno` DESC limit 0,1 ");
$check_last_regd_fee_num=mysqli_num_rows($check_last_regd_fee_qry);

$dates_array = getDatesFromRange("2017-09-01", $date);
$day_diff=sizeof($dates_array);

?>
<html>
	<head>
		<title>Doctor Requisition Report</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../css/custom.css" />
		<script src="../../js/jquery.min.js"></script>
		<script src="../../js/bootstrap.min.js"></script>
	
	</head>
	<body onafterprint="window.close();" onkeypress="close_window(event)">
		<div class="container-fluid">
			<?php include('page_header.php'); ?>
			<center><b>Doctor Requisition Report</b><br/>
			This slip is for internal use only</center>
			<b class="text-left">Room: <?php echo $opd_room["room_name"]; ?></b>
			<i class="text-right">User: <?php echo $user["name"]; ?></i>
			<table class="table table-condensed">
				<tr>
					<th>UHID</th>
					<!--<td>: <?php echo $pat_info["uhid"]; ?></td>-->
					<td>: <?php echo $uhid; ?></td>
					<th>PIN</th>
					<td>: <?php echo $pin; ?></td>
					<th>Bill No</th>
					<td>: <?php echo $pat_bill["bill_no"]; ?></td>
					<th>Date Time</th>
					<td>: <?php echo convert_date($pat_bill["date"]); ?> <?php echo convert_time($pat_bill["time"]); ?></td>
				</tr>
				<tr>
					<th>Name</th>
					<td>: <?php echo $pat_info["name"]; ?></td>
					<th>Age</th>
					<td>: <?php echo $pat_info["age"]." ".$pat_info["age_type"]; ?></td>
					<th>Sex</th>
					<td>: <?php echo $pat_info["sex"]; ?></td>
					<th>Ref By</th>
					<td>: <?php echo $ref_doc["ref_name"]; ?></td>
				</tr>
			</table>
			<table class="table table-condensed">
				<tr>
					<th>Department</th><th>Consultant</th><th>Serial No</th>
				</tr>
				<tr>
					<td><?php echo $doc_dept["name"]; ?></td><td><?php echo $app_doc["Name"]; ?></td><td><?php echo $appointment_no; ?></td>
				</tr>
				<tr>
					<td>Visit Type : <?php echo $visit_type; ?></td><td>Visit Fee : <?php echo "&#x20b9; ".number_format($visit_fee,2); ?></td><td></td>
				</tr>
			</table>
		</div>
	</body>
	<span id="user" style="display:none;"><?php echo $user_val; ?></span>
</html>
<script>
	$(document).ready(function(){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			window.print();
		}
	});
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
				window.close();
		}
	}
</script>
<style>
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
}
.table
{
	margin-bottom:5px;
	font-size:12px;
}
hr
{
	margin:0;
	border-bottom:0;
	border-top: 1px solid #000;
}
</style>
