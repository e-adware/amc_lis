<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$ipd_id=mysqli_real_escape_string($link, base64_decode($_GET["ipd"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v=mysqli_real_escape_string($link, base64_decode($_GET["v"]));

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$ipd_id' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));

$branch_id=$pat_reg["branch_id"];

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$ipd_id[type]' "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$pat_reg["date"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$patient_discharge_summary=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' "));

$doc_info=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$patient_discharge_summary[consultantdoctorid]' "));

$dept_info=mysqli_fetch_array(mysqli_query($link," SELECT a.`name` FROM `doctor_specialist_list` a, `ipd_pat_doc_details` b WHERE a.`speciality_id`=b.`dept_id` AND b.`patient_id`='$uhid' AND b.`ipd_id`='$ipd_id' "));


$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$pat_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$pat_info[district]' "));

$address="";
if($pat_info["city"])
{
	//$address.="Town/Vill- ".$pat_info["city"]."<br>";
	$address.="".$pat_info["city"]."<br>";
}
if($pat_info["address"])
{
	$address.=$pat_info["address"].", ";
}
if($pat_info["police"])
{
	$address.="P.S- ".$pat_info["police"]."<br>";
}
if($dist_info["name"])
{
	//$address.=" &nbsp; District- ".$dist_info["name"]."<br>";
}
if($st_info["name"])
{
	//$address.=" &nbsp; State- ".$st_info["name"]."<br>";
}
if($pat_info["pin"])
{
	$address.="PIN-".$pat_info["pin"];
}

$discharge_date="";
$patient_discharge_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' "));
if($patient_discharge_det)
{
	$discharge_date=date("d-m-Y",strtotime($patient_discharge_det["date"]));
}

if($patient_discharge_summary["discharge_id"]>0)
{
	$discharge_type_info=mysqli_fetch_array(mysqli_query($link, "SELECT `discharge_name` FROM `discharge_master` WHERE `discharge_id`='$patient_discharge_summary[discharge_id]'"));
}

$slno=1;
?>
<html>
<head>
	<title>IPD Patient Medicine Indent Details</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
	<div class="container-fluid">
		<div class="">
			<div class="">
				<?php //include('page_header.php');?>
			</div>
		</div>
		<div class="row" >
			<div class="span2" >
				<img src="../../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png" style="width:80px;margin-top:0px;margin-bottom:-70px;" />
			</div>
			<div class="span10 text-center" style="margin-left:0px;">
				<span style="font-size:12px;"><?php echo $page_head_line;; ?></span>
				<h4>
					<?php echo $company_info["name"]; ?><br>
					<small>
						(REGISTERED UNDER REGISTRATION OF SOCIETIES ACT, XXI OF 1860)
						<br>
						DURTLANG, AIZAWL - 796015, MIZORAM
					</small>
				</h4>
			</div>
		</div>
		<div style="text-align:center;">
			<!--<b><?php echo $dept_info["name"]; ?> DEPARTMENT</b><br/>-->
			<b>IPD Patient Medicine Indent Details</b>
		</div>
		<div class="">
			<div class="">
				<table class="table table-condensed">
					<tr>
						<th>Name</th>
						<td><b>: </b><?php echo $pat_info["name"]; ?></td>
						
						<th>Age/Sex</th>
						<td><b>: </b><?php echo $age."/".$pat_info["sex"]; ?></td>
						
						<th>Admission Date</th>
						<td><b>: </b><?php echo date("d-m-Y",strtotime($pat_reg["date"])); ?></td>
					</tr>
					<tr>
						<th>Address</th>
						<td colspan="5"><b>: </b><?php echo $address; ?></td>
					</tr>
					<tr>
						<th>Unit No.</th>
						<td><b>: </b><?php echo $pat_reg["patient_id"]; ?></td>
						
						<th>Bill No.</th>
						<td><b>: </b><?php echo $pat_reg["opd_id"]; ?></td>
						
						<th>Discharge Date</th>
						<td><b>: </b><?php echo $discharge_date; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<hr style="margin: 0;border: 1px solid #000;">
		<center>
			<div class="noprint">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div>
			<table class="table table-condensed">
				<tr>
					<th>#</th>
					<th>Description</th>
					<th>Batch No</th>
					<th>Expiry</th>
					<th>Quantity</th>
					<th>MRP</th>
					<th>Amount</th>
				</tr>
				<?php
				$tot=0;
				$q=mysqli_query($link,"SELECT DISTINCT `bill_no`,`indent_num` FROM `ph_sell_master` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id'");
				while($r=mysqli_fetch_assoc($q))
				{
					
				?>
				<tr>
					<td></td>
					<th colspan="6"><?php echo "Indent No : ".$r['indent_num'];?></th>
				</tr>
				<?php
				$j=1;
				$qq=mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_no`='$r[bill_no]' AND `indent_num`='$r[indent_num]'");
				while($rr=mysqli_fetch_assoc($qq))
				{
					$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$rr[item_code]'"));
				?>
				<tr>
					<td><?php echo $j;?></td>
					<td><?php echo $itm['item_name'];?></td>
					<td><?php echo $rr['batch_no'];?></td>
					<td><?php echo date("Y-m", strtotime($rr['expiry_date']));?></td>
					<td><?php echo $rr['sale_qnt'];?></td>
					<td><?php echo $rr['mrp'];?></td>
					<td><?php echo $rr['total_amount'];?></td>
				</tr>
				<?php
				$tot+=$rr['total_amount'];
				$j++;
				}
				?>
				<tr>
					<td colspan="7" style="padding:0.5px;background:#000000;"></td>
				</tr>
				<?php
				}
				?>
				<tr>
					<th colspan="5"></th>
					<th>Total (round)</th>
					<th><?php echo number_format(round($tot),2);?></th>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../../css/loader.css" />
<style>
.txt_small{
	font-size:10px;
}
.table
{
	font-size: 13px;
}
@media print
{
	.noprint
	{
		display:none;
	}
}
.table
{
	margin-bottom: 0px;
}
.table-condensed th, .table-condensed td
{
	//padding: 0;
	padding: 0 10px 0 0;
}
@page{
	margin: 0.2cm;
}
</style>
<script>
	$(document).ready(function(){
		$("#loader").hide();
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