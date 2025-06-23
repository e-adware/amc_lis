<?php
include("../../includes/connection.php");

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));

$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));

$pat_rel=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid'"));

$admit=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` ASC LIMIT 0,1"));

$death_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_death_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
$diagnosed=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$death_det[diagnosed_by]'"));


$pat_other_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));

$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));

$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$add_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$add_info[district]' "));

if($add_info["city"])
{
	$city=" Town/Vill: <span>".$add_info["city"]."</span>";
}

if($add_info["post_office"])
{
	$post_office="Post Office: <span>".$add_info["post_office"]."</span>";
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
	$state="State: <span>".$st_info["name"]."</span>";
}
if($add_info["pin"])
{
	$pin="PIN: <span>".$add_info["pin"]."</span>";
}

$addr="resident of ".$city." ".$post_office." ".$police." ".$pin." ".$dist." ".$state;

?>
<html>
	<head>
		<title>Death Certificate</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../css/Cherry_Swash.css" />
		<style>
			.cont
			{
				margin:auto;
				//font-family: 'Cherry Swash';
				font-size:18px;
				padding:20px;
				word-spacing: 8px;
				line-height: 30px;
			}
			[class*="span"]
			{
				float: none;
				margin-right:0px;
				margin-left:0px;
				text-decoration:none;
			}
			.u_span
			{
				//font-weight:bold;
				font-family:'Arial';
				//font-style:italic;
				text-decoration:underline;
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
				<?php //include('page_header.php'); ?>
				<?php include('page_header_ongc_bill.php'); ?>
			</div>
			
			<div style="border:1px solid #999999;">
				<div class=""><center><h3>DEATH CERTIFICATE</h3></center></div>
				<div class="cont">
					Name of the patient <span class="u_span"><?php echo trim($pat['name']);?></span>, 
					Age <span class="u_span"><?php echo $pat['age']." ".$pat['age_type'];?></span>, 
					Sex <span class="u_span"><?php echo $pat['sex'];?></span>, 
					C/O <span class="u_span"><?php echo trim($pat['gd_name']);?></span>, 
					
					<?php echo $addr; ?>
					
					<!--Address <span class="u_span"><?php echo trim($pat['address']);?></span>
					PO <span class="u_span"><?php echo trim($pat_rel['pin']);?></span>
					City <span class="u_span"><?php echo trim($pat_rel['city']);?></span>
					PS <span class="u_span"><?php echo trim($pat_rel['police']);?></span>-->
					<br/>
					Name of Doctor In-charge : <span class="u_span"><?php echo trim($diagnosed['Name']);?></span><br/>
					Date of Admission <span class="u_span"><?php echo convert_date($admit['date']);?></span><br/>
					Hospital regn No. <span class="u_span"><?php echo $ipd;?></span>
					<br/><br/>
					<table class="table">
						<tr>
							<td>Date &amp; Time of Death : <?php echo convert_date($death_det['death_date'])." ".convert_time($death_det['death_time']);?></td>
						</tr>
						<tr>
							<td>Cause of Death : <u> <?php echo $death_det['death_cause'];?> </u></td>
						</tr>
					</table>
					<table class="table">
						<tr>
							<td><font style="float:right;margin-right:5%;">Medical officer</font></td>
						</tr>
						<tr>
							<td><font style="float:right;"><?php echo $company_info['name'];?></font></td>
						</tr>
					</table>
				</div>
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
