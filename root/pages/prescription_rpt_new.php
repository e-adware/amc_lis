<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$opd_id=mysqli_real_escape_string($link, base64_decode($_GET["opdid"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v=mysqli_real_escape_string($link, base64_decode($_GET["v"]));

$pat_reg=$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));
$cer=mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));

$branch_id=$pat_reg["branch_id"];

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$dt_tm[type]' "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

//$pat_other_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));
$pat_other_info=$pat_info;

//~ if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
//~ if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$pat_reg["date"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$con_id=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$con=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_id[consultantdoctorid]' "));  

$con_doc_room=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `opd_doctor_room` WHERE `room_id`='$con[room_id]' "));  

$adv_paid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' "));

$dpt_name=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `department` WHERE `dept_id`='$con[dept_id]' "));

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}
$regd_no=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_id[consultantdoctorid]' "));

//$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
$add_info=$pat_info;

$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$add_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$add_info[district]' "));

$address="";
//~ if($pat_info["gd_name"])
//~ {
	//~ $address="C/O: ".$pat_info["gd_name"].", ";
//~ }
if($add_info["city"])
{
	$address.="Town/Vill- ".$add_info["city"]."<br>";
}
if($pat_info["address"])
{
	$address.=$pat_info["address"].", ";
}
if($add_info["police"])
{
	$address.="P.S- ".$add_info["police"]."<br>";
}
if($dist_info["name"])
{
	//$address.=" &nbsp; District- ".$dist_info["name"]."<br>";
}
if($st_info["name"])
{
	//$address.=" &nbsp; State- ".$st_info["name"]."<br>";
}
if($add_info["pin"])
{
	$address.="PIN-".$add_info["pin"];
}

$visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `visit_fee` FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($visit_fee["visit_fee"]==0)
{
	$visit_fee=0;
	$visit_type="Free";
}else
{
	$visit_fee=$visit_fee["visit_fee"];
	$visit_fee_num_qry=mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `visit_fee`>0 order by `slno` ASC ");
	$n=1;
	while($visit_fee=mysqli_fetch_array($visit_fee_num_qry))
	{
		if($visit_fee["opd_id"]==$opd_id)
		{
			$visit_fee_num=$n;
			break;
		}else
		{
			$n++;
		}
	}
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
$dept_name=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$con[dept_id]'"));
$vit=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
$nurse=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$vit[user]' "));
$page_name="<center style='margin-right:40px;'><b>OP CASE SHEET</b></center>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Prescription</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>

</head>
<body style="width:100%" onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
<div class="container-fluid">
	<div class="text-center">
		<?php include('page_header.php'); ?>
	</div>
	<div>
		<table class="table table-no-top-border">
			<tr>
				<th>Patient Name</th>
				<th>: <?php echo $pat_info["name"]; ?></th>
				<th>UHID</th>
				<!--<td>: <?php echo $pat_info["uhid"]; ?></td>-->
				<td>: <?php echo $uhid; ?></td>
				<!--<th>Facility</th>
				<td>: <?php echo $company_info["short_name"]; ?></td>-->
			</tr>
			<tr>
				<th>Gender/Age</th>
				<td>: <?php echo $pat_info["sex"]; ?>/<?php echo $age; ?></td>
				<th><?php echo $prefix_det["prefix"]; ?></th>
				<td>: <?php echo $opd_id; ?></td>
			</tr>
			<tr>
				<th>Contact No</th>
				<td>: <?php echo $pat_info["phone"]; ?></td>
				<th>Reg. Date</th>
				<td>: <?php echo date("M j Y", strtotime($dt_tm["date"])); ?> <?php echo convert_time($dt_tm["time"]); ?></td>
			</tr>
			<tr>
				<th>Address</th>
				<td colspan="3">: <?php echo $address; ?></td>
				<!--<th>Guardian Name</th>
				<td>: <?php echo $pat_info["gd_name"]; ?></td>
				<th>Relationship</th>
				<td>: <?php echo $pat_other_info["relation"]; ?></td>-->
			</tr>
			<tr>
				<th>District</th>
				<td>: <?php echo $dist_info["name"]; ?></td>
				<th>State</th>
				<td>: <?php echo $st_info["name"]; ?></td>
			</tr>
			<tr>
				<th>Doctor Name</th>
				<th colspan="3">: <?php echo $con["Name"].",&nbsp;&nbsp;".$con["qualification"]; ?></th>
			</tr>
			<tr>
				<th>Specialisation</th>
				<td>: <?php echo $dept_name['name']; ?></td>
				<th>Doc. Room No.</th>
				<td>: <?php echo $con_doc_room["room_name"]; ?></td>
			</tr>
			<tr>
				<th>Ref By</th>
				<td>: <?php echo $ref_doc['ref_name']; ?>
				<!--<th>License No</th>
				<td>: <?php echo $regd_no['regd_no']; ?>-->
				
			</tr>
		</table>
		<hr/>
		<br/>
	</div>
	<table class="table table-no-top-border">
		<!--<tr>
			style="border-bottom:2px dashed;"
			<th colspan="5"><u>Nursing Assessment</u> <i style="font-size:10px;">(Done by <?php echo $nurse['name'];?>)</i> :</th>
		</tr>-->
		<tr>
			<th colspan="4">
				Diagnosis & Investigations:
			</th>
		</tr>
		<!--<tr>
			<th colspan="4">
				Clinical details:
				<br/>
				<br/>
				<br/>
				<br/>
			</th>
		</tr>
		<tr>
			<th colspan="4">
				<br>
				Allergic to:
				<br/>
				<br/>
				<br/>
				<br/>
			</th>
		</tr>
		<tr>
			<th colspan="4">
				<br>
				Investigation:
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
			</th>
		</tr>
		<tr>
			<th colspan="4">Vital signs</th>
		</tr>
		<tr>
			<th style="width: 15%;">BP : </th>
			<th style="width: 15%;">Pulse : <?php echo $vit['pulse'];?></th>
			<th style="width: 30%;">Height : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vit['height'];?> CM</th>
			<th style="width: 40%;">Weight : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vit['weight'];?> KG</th>
			<!--<th>BMI : <?php echo $vit['BMI_1'].".".$vit['BMI_2'];?></th>-->
		<!--</tr>-->
	</table>
	<!--<br/><b><u>Treatment / Plan of care</u> :</b>-->
	<b style="clear:both;position:fixed;width:90%;bottom:5%;left:30px;">Next follow up : <span style="float:right;">Emergency Contact: 91278-18895</span></b>
</div>
<span id="user" style="display:none;"><?php echo $user; ?></span>
<span id="uhid" style="display:none;"><?php echo $uhid; ?></span>
<span id="opd_id" style="display:none;"><?php echo $opd_id; ?></span>
<span id="url_redirect" style="display:none;"><?php echo $url_redirect; ?></span>
<script>
	$(document).keydown(function (event) {
		if (event.keyCode == 123 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent F12
			return false;
		} else if (event.ctrlKey && event.shiftKey && event.keyCode == 73 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent Ctrl+Shift+I        
			return false;
		}
	});
	$(document).on("contextmenu",function(e){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			e.preventDefault();
		}
	});
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
	function refreshParent()
	{
		var uhid=$("#uhid").text().trim();
		var opd_id=$("#opd_id").text().trim();
		var url_redirect=$("#url_redirect").text().trim();
		if(url_redirect==1)
		{
			//window.opener.location.reload(true);
			window.opener.location.href="../processing.php?param=3&uhid="+uhid+"&consult=1&opd="+opd_id;
		}
	}
</script>
</body>
</html>
<style>

.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
}
.table
{
	margin-bottom:5px;
}
hr
{
	margin:0;
	border-bottom:0;
	border-top: 1px solid #000;
}
*
{
	font-size:13px;
}
</style>
