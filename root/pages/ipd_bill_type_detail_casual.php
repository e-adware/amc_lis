<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$user=base64_decode($_GET['user']);
$pro=$_GET['pro'];

$cash_final_pay_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='Cash' ");
$cash_final_pay_num=mysqli_num_rows($cash_final_pay_qry);

if($cash_final_pay_num>1)
{
	$h=1;
	while($cash_final_pay_val=mysqli_fetch_array($cash_final_pay_qry))
	{
		if($h>1)
		{
			$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' "));
			if(!$check_pay_mode_change)
			{
				mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `slno`='$cash_final_pay_val[slno]' ");
			}
		}
		$h++;
	}
}

$card_final_pay_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='Card' ");
$card_final_pay_num=mysqli_num_rows($card_final_pay_qry);

if($card_final_pay_num>1)
{
	$h=1;
	while($card_final_pay_val=mysqli_fetch_array($card_final_pay_qry))
	{
		if($h>1)
		{
			$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' "));
			if(!$check_pay_mode_change)
			{
				mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `slno`='$card_final_pay_val[slno]' ");
			}
		}
		$h++;
	}
}

$card_final_pay_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='Credit' ");
$card_final_pay_num=mysqli_num_rows($card_final_pay_qry);

if($card_final_pay_num>1)
{
	$h=1;
	while($card_final_pay_val=mysqli_fetch_array($card_final_pay_qry))
	{
		if($h>1)
		{
			$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' "));
			if(!$check_pay_mode_change)
			{
				mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `slno`='$card_final_pay_val[slno]' ");
			}
		}
		$h++;
	}
}

$pat_info=$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

//$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
$uhid_and_opdid=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$uhid_and_opdid[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$uhid_and_opdid[type]' "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
function currency($rs)
{
	setlocale(LC_MONETARY, 'en_IN');
				
	$amount = money_format('%!i', $rs);
	return $amount;
}

$row=0;
$grand_tot=0;
$gst=0;

//$date_final=mysqli_fetch_array(mysqli_query($link,"select `date` from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and `pay_type`='Final' "));

$pat_refund=mysqli_fetch_array(mysqli_query($link," SELECT sum(`refund`) as rfnd FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
$pat_refund_amt=$pat_refund["rfnd"];

$at_doc=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE patient_id='$uhid' and ipd_id='$ipd' ) "));

$bed_alloc=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` DESC "));

$room=mysqli_fetch_array(mysqli_query($link," SELECT `room_no` FROM `room_master` WHERE `room_id` in ( SELECT `room_id` FROM `bed_master` WHERE `bed_id`='$bed_alloc[bed_id]' ) "));

$ward=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `ward_master` WHERE `ward_id`='$bed_alloc[ward_id]' "));

//$no_of_days=mysqli_num_rows(mysqli_query($link,"select `date` from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select SUM(`ser_quantity`) as tot_day from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
$no_of_days=$no_of_days_val["tot_day"];

$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
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
	$address.=" &nbsp; PIN-".$add_info["pin"];
}

$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE baby_uhid='$uhid' and baby_ipd_id='$ipd' "));
if($delivery_check)
{
	$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `ipd_pat_details` WHERE `patient_id`='$delivery_check[patient_id]' AND `ipd_id`='$delivery_check[ipd_id]'"));
	$date_final=mysqli_fetch_array(mysqli_query($link,"select `date` from ipd_advance_payment_details where patient_id='$delivery_check[patient_id]' and ipd_id='$delivery_check[ipd_id]' and `pay_type`='Final' "));
}else
{
	$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$date_final=mysqli_fetch_array(mysqli_query($link,"select `date` from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and `pay_type`='Final' "));
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
	<body onafterprint="window.close();" onkeyup="close_window(event)">
		<div class="container-fluid">
			<div class="">
				<?php include('page_header.php'); ?>
			</div>
			<hr>
			<div id="final_b" style="font-size:16px;" align="center">
				<?php
					echo "<u><b>$prefix_det[p_type] BILL</b></u><br/><br/>";
					//echo "<u><b>Day Care Bill</b></u><br/><br/>";
				?>
			</div>
			<div>
				<table class="table table-condensed td_head" style="font-size:12px;border: 1px solid #000;">
					<tr>
						<th>UHID</th><th style="font-size: 15px;">: <?php echo $pat["patient_id"];?></th>
						<th><?php echo $prefix_det["prefix"]; ?></th><th style="font-size: 15px;">: <?php echo $ipd;?></th>
						<th>Registration Date</th><td>: <?php echo convert_date_g($uhid_and_opdid['date'])." ".convert_time($uhid_and_opdid['time']);?></td>
					</tr>
					<tr>
						<th>Name</th><td>: <?php echo $pat['name'];?></td>
						<th>Age-Sex</th><td>: <?php echo $age." - ".$pat['sex'];?></td>
						<th>District</th><td>: <?php echo $dist_info["name"]; ?></td>
					</tr>
					<tr style="display:none;">
						<th>IPD Serial No</th><td><?php echo $uhid_and_opdid["ipd_serial"]; ?></td>
					</tr>
					<tr>
						<th>State</th><td>: <?php echo $st_info["name"]; ?></td>
						<th>Address</th><td colspan="3">: <?php echo $address; ?></td>
					</tr>
					<tr>
						<th>Ref By</th>
						<td>: <?php echo $ref_doc["ref_name"]; ?></td>
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
				$qry=mysqli_query($link,"select distinct group_id from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd'");
				while($q=mysqli_fetch_array($qry))
				{
					$group=mysqli_fetch_array(mysqli_query($link,"select * from charge_group_master where group_id='$q[group_id]' "));
					if($q["group_id"]==141)
					{
						//echo "<tr><td colspan='2'><b><i> &nbsp; $group[group_name]</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
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
					}else if($q["group_id"]==148)
					{
						//echo "<tr><td colspan='2'><b><i> &nbsp; $group[group_name]</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
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
					else
					{
						//echo "<tr><td colspan='2'><b><i> &nbsp; $group[group_name]</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
						$tot_grp=0;
						
						$sub_grp=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]'");
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
							
							$tot=$tot+$s_g[amount];		
							$i++;
							$tot_grp+=$s_g['amount'];
						}
					}
					$tot_grp_val=number_format($tot_grp,2);
					echo "<tr style='display:none;'><td colspan='5'><b><i class='text-right'>Total $group[group_name] : &nbsp;</i></b></td><td> $tot_grp_val</td></tr>";
				}
				if($uhid_and_opdid["type"]==3)
				{
					// OT Charge
					$sub_grp=mysqli_query($link,"select * from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' order by slno asc");
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
							echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$s_g[ser_quantity]</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[amount]</td></tr>";	
							
							$tot=$tot+$s_g[amount];		
							$i++;
							$tot_grp+=$s_g['amount'];
						}
						
						$tot_grp_val=number_format($tot_grp,2);
						echo "<tr style='display:none;'><td colspan='5'><b><i class='text-right'>Total $group[group_name] : &nbsp;</i></b></td><td> $tot_grp_val</td></tr>";
					}
				}
				$delivery_check_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
				if($delivery_check_val)
				{
					echo "<tr><td colspan='6' style='border-top: 1px solid #000;'></td></tr>";
					echo "<tr><td colspan='6'><b>Baby's Charges</i></b></td></tr>";
					echo "<tr><td colspan='6' style='border-top: 1px solid #000;'></td></tr>";
					
					$qry=mysqli_query($link,"select distinct group_id from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]'");
					while($q=mysqli_fetch_array($qry))
					{
						$group=mysqli_fetch_array(mysqli_query($link,"select * from charge_group_master where group_id='$q[group_id]'"));
						
						//echo "<tr><td colspan='2'><b><i> &nbsp; $group[group_name]</i></b></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td><td style='border-left: 1px solid #000;'></td></tr>";
						
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
						$tot_grp_val=number_format($tot_grp,2);
						//echo "<tr><td colspan='5'><b><i class='text-right'>Total $group[group_name] : &nbsp;</i></b></td><td> $tot_grp_val</td></tr>";
					}
					
					// OT Charge
					$sub_grp=mysqli_query($link,"select * from ot_pat_service_details where  patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' order by slno asc");
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
							echo "<tr><td> &nbsp; $i</td><td><span>$s_g[service_text]</span></td><td style='border-left: 1px solid #000;'><center>$entry_date</center></td><td style='border-left: 1px solid #000;'><center>$s_g[ser_quantity]</center></td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[rate]</td><td style='border-left: 1px solid #000;'> &nbsp; $s_g[amount]</td></tr>";	
							
							$tot=$tot+$s_g[amount];		
							$i++;
							$tot_grp+=$s_g['amount'];
						}
						
						$tot_grp_val=number_format($tot_grp,2);
						echo "<tr style='display:none;'><td colspan='5'><b><i class='text-right'>Total $group[group_name] : &nbsp;</i></b></td><td> $tot_grp_val</td></tr>";
					}
				}
				
				// Advance Pay
				$paym_adv=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as tot, ifnull(sum(discount),0) as dis, ifnull(sum(refund),0) as refund from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and `pay_type`='Advance' "));
				
				$advance_amount=$paym_adv['tot'];
				$discount_amount=$paym_adv['dis'];
				$refund_amount=$paym_adv['refund'];
				
				// Final Pay
				$paym_final=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as tot, ifnull(sum(discount),0) as dis, ifnull(sum(refund),0) as refund from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and `pay_type`='Final' "));
				
				$final_amount=$paym_final['tot'];
				$discount_amount+=$paym_final['dis'];
				$refund_amount+=$paym_final['refund'];
				
				// Total Discount and Refund
				$dis_ref=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(discount),0) as dis, ifnull(sum(refund),0) as refund from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' "));
				
				$total_discount=$dis_ref['dis'];
				$total_refund=$dis_ref['refund'];
				
				$tot=round($tot);
				
				$td_col_span="5";
				
				?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>" style="border-right:1px solid #000;">
						<b class="text-right">Total Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($tot,2);?>
					</td>
				</tr>
			<?php
				$total_paid_amount=0;
				$pay_mode_qry=mysqli_query($link," SELECT DISTINCT `pay_mode` FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' AND `pay_mode`!='Credit' ");
				while($pay_mode=mysqli_fetch_array($pay_mode_qry))
				{
					$pay=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as tot from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and `pay_mode`='$pay_mode[pay_mode]' "));
					
					$pay_amount=$pay["tot"];
					$total_paid_amount+=$pay["tot"];
			?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;"><?php echo $pay_mode["pay_mode"]; ?> Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($pay_amount,2);?>
					</td>
				</tr>
			<?php
				}
				// Credit
				$credit_amount=$tot-$total_paid_amount-$total_discount+$total_refund;
				
				if($credit_amount>0)
				{
			?>
				<tr>
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;">Credit Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($credit_amount,2);?>
					</td>
				</tr>
			<?php
				}
			?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;">Paid Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($advance_amount+$final_amount,2);?>
					</td>
				</tr>
				<?php if($total_discount>0){ ?>
				<tr>
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;">Discount &nbsp; </b></td><td> &nbsp; <?php echo number_format($total_discount,2);?>
					</td>
				</tr>
				<?php } ?>
				<?php if($total_refund>0){ ?>
				<tr>
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;">Refunded Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format(($total_refund),2);?>
					</td>
				</tr>
				<?php } ?>
				<?php if($final_amount>0){ ?>
				<!--<tr>
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;">Paid Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format(($final_amount),2);?>
					</td>
				</tr>-->
				<?php } ?>
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
	</body>
</html>
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

</style>
