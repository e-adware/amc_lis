<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$uhid=mysqli_real_escape_string($link, base64_decode($_GET['uhid']));
$ipd=mysqli_real_escape_string($link, base64_decode($_GET['ipd']));
$user=mysqli_real_escape_string($link, base64_decode($_GET['user']));
$pro=mysqli_real_escape_string($link, $_GET['pro']);

$dbl=0;
if($_GET["dbl"])
{
	$dbl=$_GET["dbl"];
}

$doc_amount_all=0;
if($dbl==1)
{
	$doc_pay=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `payment_settlement_doc` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' "));
	$doc_amount_all=$doc_pay["tot_amount"];
}

$pat_info=$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));

$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));

$reg_date=$pat_reg["date"];

if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$centre_info=mysqli_fetch_array(mysqli_query($link,"SELECT `centrename` FROM `centremaster` WHERE `centreno`='$pat_reg[center_no]'"));

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

function currency($rs)
{
	setlocale(LC_MONETARY, 'en_IN');
	
	$amount = money_format('%!i', $rs);
	return $amount;
}

$row=0;
$grand_tot=0;
$gst=0;

$at_doc=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE patient_id='$uhid' and ipd_id='$ipd' ) "));

$bed_alloc=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` DESC "));

$room=mysqli_fetch_array(mysqli_query($link," SELECT `room_no` FROM `room_master` WHERE `room_id` in ( SELECT `room_id` FROM `bed_master` WHERE `bed_id`='$bed_alloc[bed_id]' ) "));

$ward=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `ward_master` WHERE `ward_id`='$bed_alloc[ward_id]' "));

$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select SUM(`ser_quantity`) as tot_day from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
$no_of_days=$no_of_days_val["tot_day"];

//$pat_other_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));
$pat_other_info=$pat_info;

//$source_name=mysqli_fetch_array(mysqli_query($link, " SELECT `source_type` FROM `patient_source_master` WHERE `source_id`='$pat_other_info[source_id]' "));

//$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
$add_info=$pat_info;

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$add_info[district]' "));
$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$add_info[state]' "));
//$address=$add_info["city"].", ".$dist_info["name"].", ".$st_info["name"];
$address="";
//~ if($pat_info["gd_name"])
//~ {
	//~ $address="C/O: ".$pat_info["gd_name"]."<br>";
//~ }
if($add_info["city"])
{
	$address.=" &nbsp;Town/Vill- ".$add_info["city"].", ";
}
if($pat_info["address"])
{
	//$address.=$pat_info["address"].", ";
}
if($add_info["police"])
{
	$address.=" &nbsp; P.S- ".$add_info["police"].", ";
}
//~ if($dist_info["name"])
//~ {
	//~ $address.=" &nbsp; District- ".$dist_info["name"].", ";
//~ }
//~ if($st_info["name"])
//~ {
	//~ $address.=" &nbsp; State- ".$st_info["name"].", ";
//~ }
if($add_info["pin"])
{
	$address.=" &nbsp; PIN-".$add_info["pin"].", ";
}
//~ if($pat_info["phone"])
//~ {
	//~ $address.=" &nbsp; Phone-".$pat_info["phone"];
//~ }

$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE baby_uhid='$uhid' and baby_ipd_id='$ipd' "));
if($delivery_check)
{
	$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `uhid_and_opdid` WHERE `patient_id`='$delivery_check[patient_id]' AND `opd_id`='$delivery_check[ipd_id]'"));
	
	$date_final=mysqli_fetch_array(mysqli_query($link,"select `date` from payment_detail_all where patient_id='$delivery_check[patient_id]' and opd_id='$delivery_check[ipd_id]' and `payment_type`='Final' "));
	
	$date_final=mysqli_fetch_array(mysqli_query($link,"select `date` from ipd_pat_discharge_details where patient_id='$delivery_check[patient_id]' and ipd_id='$delivery_check[ipd_id]' "));
}else
{
	$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$date_final=mysqli_fetch_array(mysqli_query($link,"select `date` from payment_detail_all where patient_id='$uhid' and opd_id='$ipd' and `payment_type`='Final' "));
	
	$date_final=mysqli_fetch_array(mysqli_query($link,"select `date` from ipd_pat_discharge_details where patient_id='$uhid' and ipd_id='$ipd' "));
	
	if($date_final)
	{
		$pro=0;
	}else
	{
		$pro=1;
	}
}
?>
<html lang="en">
	<head>
		<title>Detail Bill</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../css/custom.css" />
		<script src="../../js/jquery.min.js"></script>
		<style>
			.table td, .table th{border-top:none;padding: 0px;}
			.td_head td, .table th{border-top:none;padding:3px;}
			#serv_det th{ border-bottom:1px solid;font-size:13px;}
			#serv_det td{ font-size:13px;}
		</style>
	</head>
	<body onafterprint="window.close();" onkeyup="close_window(event)" ondblclick="reload_page()">
		<div class="container-fluid">
			<div class="">
				<?php include('page_header.php'); ?>
			</div>
			<hr>
			<div id="final_b" style="font-size:16px;" align="center">
				<?php
				if($_GET[val]>0)
				{
					if($pro==0)
					{
						echo "<u><b>DETAIL BILL</b></u><br/><br/>";
					}
					if($pro==1)
					{
						echo "<u><b>PROVISIONAL BILL</b></u><br/><br/>";
					}
				}
				?>
			</div>
			<center>
				<div class="noprint "><input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()"></div>
			</center>
			<div>
				<table class="table table-condensed td_head" style="font-size:12px;border: 1px solid #000;">
					<tr>
						<th>UHID</th><th style="font-size: 15px;">: <?php echo $pat["patient_id"];?></th>
						<th><?php echo $prefix_det["prefix"]; ?></th><th style="font-size: 15px;">: <?php echo $ipd;?></th>
						<th>Admission Date</th><td>: <?php echo convert_date_g($dt['date'])." ".convert_time($dt['time']);?></td>
					</tr>
					<tr>
						<th>Name</th><td>: <?php echo $pat['name'];?></td>
						<th>Age/Sex</th><td>: <?php echo $age."/".$pat['sex'];?></td>
						<!--<th>Discharge Date</th><td><?php echo convert_date_g($date_final["date"]); ?></td>-->
						<th>Discharge Date</th><td>: <?php if($date_final["date"]){ echo convert_date_g($date_final["date"]); }else{ echo "N/A"; }; ?></td>
					</tr>
					<tr>
						<th>Phone</th>
						<td>: <?php echo $pat["phone"]; ?></td>
						<th>Ref By</th>
						<td colspan="1">: <?php echo $ref_doc["ref_name"]; ?></td>
						<th>Organization</th>
						<td>: <?php echo $centre_info["centrename"]; ?></td>
					</tr>
					<tr style="display:none;">
						<th>IPD Serial No</th><td><?php echo $pat_reg["ipd_serial"]; ?></td>
					</tr>
					<tr>
						<th>Room Category</th><td>: <?php echo $ward['name']; ?></td>
						<th>Room No</th><td>: <?php echo $room['room_no']; ?></td>
						<th>Consultant Doctor</th><td>: <?php echo $at_doc["Name"]; ?></td>
					</tr>
					<tr>
						<th>No. of Days</th><td>: <?php echo $no_of_days; ?></td>
						<th>District</th><td>: <?php echo $dist_info["name"]; ?></td>
						<th>State</th><td>: <?php echo $st_info["name"]; ?></td>
					</tr>
					<tr>
						<th>Address</th><td colspan="5">: <?php echo $address; ?></td>
					</tr>
				</table>
				
				<table class="table table-bordered" id="serv_det" style="font-size:13px;">
				<tr>
					<th>#</th>
					<th>Service Description</th>
					<th style="border-left: 1px solid #000;"><center>Entry Date</center></th>
					<th style="border-left: 1px solid #000;"><center>Quantity</center></th>
					<th style="border-left: 1px solid #000;">Rate</th>
					<th style="border-left: 1px solid #000;">Amount</th>
				</tr>
				<?php
				$i=1;
				$tot=0;
				$tot_val=0;
				$qry=mysqli_query($link,"select distinct group_id from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' order by slno");
				while($q=mysqli_fetch_array($qry))
				{
					$group=mysqli_fetch_array(mysqli_query($link,"select * from charge_group_master where group_id='$q[group_id]' "));
					
					if($q["group_id"]==141) // Bed Charge
					{
						echo "<tr><td colspan='2'><b><i> &nbsp; $group[group_name]</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
						$tot_grp=0;
						
						$service_qry=mysqli_query($link,"select distinct service_id from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' ");
						while($dist_service=mysqli_fetch_array($service_qry))
						{
							$s_g=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and `service_id`='$dist_service[service_id]' "));
							
							$bed_ser_num=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and `service_id`='$dist_service[service_id]' "));
							
							$tot_bed_amt=0;
							$tot_bed_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and `service_id`='$dist_service[service_id]' "));
							$tot_bed_amt=$tot_bed_serv["tots"];
							
							$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$dist_service[service_id]'"));
							
							$days=$q[days];
							if($q[days]==0)
							{
								$days="";
							}
							$entry_date=convert_date_g($s_g['date']);
							echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$bed_ser_num</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $tot_bed_amt</td></tr>";	
							
							$tot=$tot+$tot_bed_amt;
							$i++;
							$tot_grp+=$tot_bed_amt;
							
						}
					}
					else if($q["group_id"]==148)  // Bed Charge Plus
					{
						echo "<tr><td colspan='2'><b><i> &nbsp; $group[group_name]</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
						$tot_grp=0;
						
						$service_qry=mysqli_query($link,"select distinct service_id from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' ");
						while($dist_service=mysqli_fetch_array($service_qry))
						{
							$s_g=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and `service_id`='$dist_service[service_id]' "));
							
							$bed_ser_num=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and `service_id`='$dist_service[service_id]' "));
							
							$tot_bed_amt=0;
							$tot_bed_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and `service_id`='$dist_service[service_id]' "));
							$tot_bed_amt=$tot_bed_serv["tots"];
							
							$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$dist_service[service_id]'"));
							
							$days=$q[days];
							if($q[days]==0)
							{
								$days="";
							}
							$entry_date=convert_date_g($s_g['date']);
							echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$bed_ser_num</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $tot_bed_amt</td></tr>";	
							
							$tot=$tot+$tot_bed_amt;
							$i++;
							$tot_grp+=$tot_bed_amt;
							
						}
					}
					else if($q["group_id"]==155) // OT
					{
						$tot_val=1;
						echo "<tr><td colspan='2'><b><i> &nbsp; $group[group_name]</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
						$tot_grp=0;
						
						// From OT Dashboard
						$sub_grp=mysqli_query($link,"select * from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and ot_group_id='155' order by slno asc");
						$sub_grp_num=mysqli_num_rows($sub_grp);
						if($sub_grp_num>0)
						{
							
							//echo "<tr><td colspan='2'><b><i> &nbsp; OT Charge</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
							
							$tot_grp=0;
							
							while($s_g=mysqli_fetch_array($sub_grp))
							{
							
								//$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$s_g[service_id]'"));
								
								$days=$q[days];
								if($q[days]==0)
								{
									$days="";
								}
								$entry_date=convert_date_g($s_g['date']);
								if($s_g["amount"]>0)
								{
									echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$s_g[ser_quantity]</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[amount]</td></tr>";
								}
								
								$tot=$tot+$s_g[amount];		
								$i++;
								$tot_grp+=$s_g['amount'];
							}
							
							$tot_grp_val=number_format($tot_grp,2);
							echo "<tr style='display:none;'><td colspan='5'><b><i class='text-right'>Total $group[group_name] : &nbsp;</i></b></td><td> $tot_grp_val</td></tr>";
						}
						////////
						
						$service_qry=mysqli_query($link,"select distinct service_id from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' ");
						while($dist_service=mysqli_fetch_array($service_qry))
						{
							$s_g=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and `service_id`='$dist_service[service_id]' "));
							
							$bed_ser_num=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and `service_id`='$dist_service[service_id]' "));
							
							$tot_bed_amt=0;
							$tot_bed_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and `service_id`='$dist_service[service_id]' "));
							$tot_bed_amt=$tot_bed_serv["tots"];
							
							$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$dist_service[service_id]'"));
							
							$days=$q[days];
							if($q[days]==0)
							{
								$days="";
							}
							$entry_date=convert_date_g($s_g['date']);
							echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$bed_ser_num</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $tot_bed_amt</td></tr>";	
							
							$tot=$tot+$tot_bed_amt;
							$i++;
							$tot_grp+=$tot_bed_amt;	
						}
					}
					else if($q["group_id"]==104) // Laboratory (Pathology, Radiology, Cardilogy)
					{
						
						$lab_category_qry=mysqli_query($link,"SELECT DISTINCT `category_id` FROM `testmaster` WHERE `testid` IN(select service_id from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]')");
						
						while($lab_category=mysqli_fetch_array($lab_category_qry))
						{
							if($lab_category["category_id"]==1)
							{
								$group_name="LABORATORY CHARGES";
							}
							if($lab_category["category_id"]==2)
							{
								$group_name="RADIOLOGY CHARGES";
							}
							if($lab_category["category_id"]==3)
							{
								$group_name="CARDIOLOGY CHARGES";
							}
							echo "<tr><td colspan='2'><b><i> &nbsp; $group_name</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
							$tot_grp=0;
							
							$sub_grp=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and service_id IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='$lab_category[category_id]')");
							while($s_g=mysqli_fetch_array($sub_grp))
							{
							
								$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$s_g[service_id]'"));
								
								$days=$q[days];
								if($q[days]==0)
								{
									$days="";
								}
								$entry_date=convert_date_g($s_g['date']);
								echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$s_g[ser_quantity]</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[amount]</td></tr>";	
								
								$tot=$tot+$s_g['amount'];		
								$i++;
								$tot_grp+=$s_g['amount'];
							}
						}
					}
					else
					{
						$sub_grp_sum=mysqli_fetch_array(mysqli_query($link,"select ifnull(SUM(`amount`),0) AS `tot` from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]'"));
						
						$doc_amount_group=0;
						if($dbl==1)
						{
							$doc_pay=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `payment_settlement_doc` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' AND `group_id`='$q[group_id]' "));
							$doc_amount_group=$doc_pay["tot_amount"];
						}
						
						if(($sub_grp_sum["tot"]+$doc_amount_group)>0)
						{
							echo "<tr><td colspan='2'><b><i> &nbsp; $group[group_name]</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
							$tot_grp=0;
							
							$sub_grp=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]'");
							while($s_g=mysqli_fetch_array($sub_grp))
							{
								$doc_amount_service=0;
								if($dbl==1)
								{
									$doc_pay=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `payment_settlement_doc` WHERE `rel_slno`='$s_g[slno]' AND `patient_id`='$uhid' AND `opd_id`='$ipd' AND `group_id`='$q[group_id]' AND `charge_id`='$s_g[service_id]' "));
									$doc_amount_service=$doc_pay["tot_amount"];
								}
								
								if(($s_g['amount']+$doc_amount_service)>0)
								{
									$serv_amount=$s_g['amount']+$doc_amount_service;
									$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$s_g[service_id]'"));
									
									$days=$q[days];
									if($q[days]==0)
									{
										$days="";
									}
									$entry_date=convert_date_g($s_g['date']);
									echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$s_g[ser_quantity]</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $serv_amount</td></tr>";
									
									$tot=$tot+$serv_amount;
									$i++;
									$tot_grp+=$serv_amount;
								}
							}
						}
					}
					$tot_grp_val=number_format($tot_grp,2);
					echo "<tr style='display:none;'><td colspan='5'><b><i class='text-right'>Total $group[group_name] : &nbsp;</i></b></td><td> $tot_grp_val</td></tr>";
				}
				if($tot_val==0)
				{
					// OT Charge
					$sub_grp=mysqli_query($link,"select * from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' order by slno asc");
					$sub_grp_num=mysqli_num_rows($sub_grp);
					if($sub_grp_num>0)
					{
						
						echo "<tr><td colspan='2'><b><i> &nbsp; OT Charge</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
						
						$tot_grp=0;
						
						while($s_g=mysqli_fetch_array($sub_grp))
						{
						
							//$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$s_g[service_id]'"));
							
							$days=$q[days];
							if($q[days]==0)
							{
								$days="";
							}
							$entry_date=convert_date_g($s_g['date']);
							if($s_g["amount"]>0)
							{
								echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$s_g[ser_quantity]</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[amount]</td></tr>";
							}
							
							$tot=$tot+$s_g[amount];		
							$i++;
							$tot_grp+=$s_g['amount'];
						}
						
						$tot_grp_val=number_format($tot_grp,2);
						echo "<tr style='display:none;'><td colspan='5'><b><i class='text-right'>Total $group[group_name] : &nbsp;</i></b></td><td> $tot_grp_val</td></tr>";
					}
				}
				$delivery_check_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
				$delivery_check_num=mysqli_num_rows($delivery_check_qry);
				$n=1;
				while($delivery_check_val=mysqli_fetch_array($delivery_check_qry))
				{
					if($delivery_check_num==1)
					{
						$n="";
					}
					$qry=mysqli_query($link,"select distinct group_id from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]'");
					$baby_ser_num=mysqli_num_rows($qry);
					if($baby_ser_num>0)
					{
						echo "<tr><td colspan='6' style='border-top: 1px solid #000;'></td></tr>";
						echo "<tr><td colspan='6'><b>Baby $n's Charges</i></b></td></tr>";
						echo "<tr><td colspan='6' style='border-top: 1px solid #000;'></td></tr>";
					
						while($q=mysqli_fetch_array($qry))
						{
							$group=mysqli_fetch_array(mysqli_query($link,"select * from charge_group_master where group_id='$q[group_id]'"));
							
							if($q["group_id"]==104) // Laboratory (Pathology, Radiology, Cardilogy)
							{
								$lab_category_qry=mysqli_query($link,"SELECT DISTINCT `category_id` FROM `testmaster` WHERE `testid` IN(select service_id from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' and group_id='$q[group_id]')");
								
								while($lab_category=mysqli_fetch_array($lab_category_qry))
								{
									if($lab_category["category_id"]==1)
									{
										$group_name="LABORATORY CHARGES";
									}
									if($lab_category["category_id"]==2)
									{
										$group_name="RADIOLOGY CHARGES";
									}
									if($lab_category["category_id"]==3)
									{
										$group_name="CARDIOLOGY CHARGES";
									}
									echo "<tr><td colspan='2'><b><i> &nbsp; $group_name</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
									$tot_grp=0;
									
									$sub_grp=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' and group_id='$q[group_id]' and service_id IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='$lab_category[category_id]')");
									while($s_g=mysqli_fetch_array($sub_grp))
									{
									
										$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$s_g[service_id]'"));
										
										$days=$q[days];
										if($q[days]==0)
										{
											$days="";
										}
										$entry_date=convert_date_g($s_g['date']);
										echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$s_g[ser_quantity]</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[amount]</td></tr>";	
										
										$tot=$tot+$s_g['amount'];		
										$i++;
										$tot_grp+=$s_g['amount'];
									}
								}
							}
							else
							{
								echo "<tr><td colspan='2'><b><i> &nbsp; $group[group_name]</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
							
								$sub_grp=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' and group_id='$q[group_id]'");
								$tot_grp=0;
								while($s_g=mysqli_fetch_array($sub_grp))
								{
								
									$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$s_g[service_id]'"));
									
									$days=$q[days];
									if($q[days]==0)
									{
										$days="";
									}
									$entry_date=convert_date_g($s_g['date']);
									echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$s_g[ser_quantity]</center></td><td style='border-left: 1px solid #000;'> &nbsp;&nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp;&nbsp; $s_g[amount]</td></tr>";	
									
									$tot=$tot+$s_g[amount];		
									$i++;
									$tot_grp+=$s_g['amount'];
								}
							}
							$tot_grp_val=number_format($tot_grp,2);
							//echo "<tr><td colspan='5'><b><i class='text-right'>Total $group[group_name] : &nbsp;</i></b></td><td> $tot_grp_val</td></tr>";
						}
					}
					
					// OT Charge
					$sub_grp=mysqli_query($link,"select * from ot_pat_service_details where  patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' order by slno asc");
					$sub_grp_num=mysqli_num_rows($sub_grp);
					if($sub_grp_num>0)
					{
						echo "<tr><td colspan='2'><b><i> &nbsp; OT Charge</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
						$tot_grp=0;
						
						while($s_g=mysqli_fetch_array($sub_grp))
						{
						
							//$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$s_g[service_id]'"));
							
							$days=$q[days];
							if($q[days]==0)
							{
								$days="";
							}
							$entry_date=convert_date_g($s_g['date']);
							echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$s_g[ser_quantity]</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[amount]</td></tr>";	
							
							$tot=$tot+$s_g[amount];		
							$i++;
							$tot_grp+=$s_g['amount'];
						}
						
						$tot_grp_val=number_format($tot_grp,2);
						echo "<tr style='display:none;'><td colspan='5'><b><i class='text-right'>Total $group[group_name] : &nbsp;</i></b></td><td> $tot_grp_val</td></tr>";
					}
					$n++;
				}
				
				$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' "));

				$already_paid      =$check_paid["paid"]+$doc_amount_all;
				$already_discount  =$check_paid["discount"];
				$already_refund    =$check_paid["refund"];
				$already_tax       =$check_paid["tax"];

				$settle_amount=$already_paid+$already_discount+$already_tax-$already_refund;

				$balance_amount=$tot-$settle_amount;
				
				$td_col_span="5";
				
			?>
				<tr style="border-top:1px solid #000;border-bottom:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>" style="border-right:1px solid #000;">
						<b class="text-right">Total Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($tot,2);?>
					</td>
				</tr>
		<?php
			$mode_pay_total=0;
			$pay_mode_master_qry=mysqli_query($link," SELECT `p_mode_name` FROM `payment_mode_master` WHERE `operation`=1 AND `status`=0 ORDER BY `sequence` ");
			while($pay_mode_master=mysqli_fetch_array($pay_mode_master_qry))
			{
				$mode_pay=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as tot from payment_detail_all where patient_id='$uhid' and opd_id='$ipd' and `payment_mode`='$pay_mode_master[p_mode_name]' "));
				$mode_amount=$mode_pay["tot"];
				if($mode_amount>0)
				{
					$mode_pay_total+=$mode_amount;
		?>
				<tr>
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;"><?php echo $pay_mode_master["p_mode_name"]; ?> Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($mode_amount+$doc_amount_all,2);?>
					</td>
				</tr>
		<?php
				}
			}
		?>
				<?php if($already_discount>0){ ?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;">Discount &nbsp; </b></td><td> &nbsp; <?php echo number_format($already_discount,2);?>
					</td>
				</tr>
				<?php } ?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;">Total Paid Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($already_paid,2);?>
					</td>
				</tr>
				<?php if($already_refund>0){ ?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;">Refunded Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format(($already_refund),2);?>
					</td>
				</tr>
				<?php } ?>
				<?php if($already_tax>0){ ?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;">Tax Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format(($already_tax),2);?>
					</td>
				</tr>
				<?php } 
					
					// Credit
					$tot=round($tot);
					if($balance_amount>0)
					{
			?>
					<tr style="border-top:1px solid #000;">
						<td colspan="<?php echo $td_col_span; ?>">
							<b class="text-right" style="border-right:1px solid #000;">Credit Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($balance_amount,2);?>
						</td>
					</tr>
			<?php
					}
					
				?>
				
				</table>
				
			</div>
			
			<div class="span7"></div>
			<div class="span5 text-right" style="margin-top: 6%;line-height: 12px;">
				<b>FOR <?php echo $company_info["name"]; ?></b>
				<br>
				<br>
				(<?php echo $emp["name"]; ?>)
			</div>
		</div>
		<span id="user" style="display:none;"><?php echo $user; ?></span>
		<span id="dbl" style="display:none;"><?php echo $dbl; ?></span>
	</body>
</html>
<script>
	$(document).keydown(function (event) {
		if (event.keyCode == 123 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent F12
			//return false;
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
	
	//window.print();
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
				window.close();
		}
	}
	
	function reload_page()
	{
		var dbl=$("#dbl").text().trim();
		if(dbl==0)
		{
			dbl=1;
			
			var url = window.location.href;
			
			window.location.href=url+"&dbl="+dbl;
		}
	}
</script>
<style>
.text-right
{
	float:right;
}
.table-bordered th, .table-bordered td
{
	border-left: 0;
}
.table-bordered
{
	border-collapse: collapse;
	border: 1px solid #000;
}
hr
{
	margin: 10px 0;
}
@page
{
	margin-top:0.5cm;
}
@media print{
	.noprint{
		display:none;
	}
}
</style>
