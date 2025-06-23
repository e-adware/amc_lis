<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=base64_decode($_GET['uhid']);
$baby=base64_decode($_GET['baby_id']);

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
$b=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' AND `baby_uhid`='$baby'"));
$buhid=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$baby'"));
$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid`,`name`,`address` FROM `patient_info` WHERE `patient_id`='$uhid'"));
$cond=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$b[conducted_by]'"));

if($b['sex']=="Male")
$gen="BOY";
if($b['sex']=="Female")
$gen="GIRL";

$pat_other_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));

$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));

$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$add_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$add_info[district]' "));


if($add_info["city"])
{
	$city=" Town/Vill: <span>".$add_info["city"]."</span>";
}

if($add_info["police"])
{
	$police="P.S: <span>".$add_info["police"]."</span>";
}
if($dist_info["name"])
{
	$dist="District: <span>".$dist_info["name"]."</span>";
}
if($st_info["name"])
{
	$state="<span>".$st_info["name"]."</span>";
}
if($add_info["pin"])
{
	$pin="PIN: <span>".$add_info["pin"]."</span>";
}

$addr="resident of ".$city." ".$police." ".$pin." ".$dist." ".$state;

?>
<html>
	<head>
		<title>Baby Birth Certificate</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../css/Cherry_Swash.css" />
		<style>
			.cont
			{
				margin:auto;
				font-family: 'Cherry Swash';
				font-size:18px;
				padding:20px;
				word-spacing: 8px;
				line-height: 30px;
			}
			span
			{
				//font-weight:bold;
				font-family:'Arial';
				//font-style:italic;
				 border-bottom: 1px dotted #000;
				text-decoration: none;
			}
			.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
			{
				//border:1px solid;
				border-top:none;
			}
		</style>
	</head>
	<body onafterprint="window.close();" onkeypress="close_window(event)">
		<div class="container-fluid">
			<div class="">
				<?php include("page_header.php"); ?>
			</div>
			<br>
			<div style="border:1px solid #999999;height: 980px;">
				<table class="table">
					<tr>
						<td>UHID : <?php echo $buhid['patient_id'];?></td>
					</tr>
				</table>
				<br>
				<br>
				<br>
				<div class=""><center><h3>BIRTH CERTIFICATE</h3></center></div>
				<div class="cont">
					A baby <span id="gen"><?php echo $gen;?></span> is born to <span id="mother"><?php echo $pat['name'];?></span> wife of Mr. <span id="father"><?php echo $b['father_name'];?></span> <?php echo $addr; ?> at <span id="time"><?php echo convert_time($b['born_time']);?></span> on <span id="date"><?php echo convert_date_f($b['dob']);?></span> weighing <span id="weight"><?php echo $b['weight'];?></span> K.G.<br/>
					Attending Obstetrician <span id="conduct"><?php echo $cond['Name'];?></span>.
					
				</div>
				<table class="table" style="position: absolute;top: 87%;width: 95%;">
					<tr>
						<td>Date : <?php echo date('d-M-Y');?></td>
						<td>
							<font style="float:right;">
								
								Authorised Signatory
								<br><br>
								<?php echo $company_info['name'];?>
							</font>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
<script>
	//window.print();
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
