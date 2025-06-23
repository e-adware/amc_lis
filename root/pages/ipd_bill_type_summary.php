<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$user=base64_decode($_GET['user']);

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

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$uhid_and_opdid[type]' "));

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

$pat_refund=mysqli_fetch_array(mysqli_query($link," SELECT sum(`refund`) as rfnd FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
$pat_refund_amt=$pat_refund["rfnd"];

//$date_final=mysqli_fetch_array(mysqli_query($link,"select `date` from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and `pay_type`='Final' "));

$at_doc=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE patient_id='$uhid' and ipd_id='$ipd' ) "));

$bed_alloc=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` DESC "));

$room=mysqli_fetch_array(mysqli_query($link," SELECT `room_no` FROM `room_master` WHERE `room_id` in ( SELECT `room_id` FROM `bed_master` WHERE `bed_id`='$bed_alloc[bed_id]' ) "));

$ward=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `ward_master` WHERE `ward_id`='$bed_alloc[ward_id]' "));

$no_of_days=mysqli_num_rows(mysqli_query($link,"select `date` from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));

$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select SUM(`ser_quantity`) as tot_day from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
$no_of_days=$no_of_days_val["tot_day"];

$pat_other_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));

$source_name=mysqli_fetch_array(mysqli_query($link, " SELECT `source_type` FROM `patient_source_master` WHERE `source_id`='$pat_other_info[source_id]' "));

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
	$address.=$pat_info["address"].", ";
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
		<title>Bill Summary</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<script src="../../js/jquery.min.js"></script>
		<style>
			.table td, .table th{border-top:none;padding: 0px;}
			.td_head td, .table th{border-top:none;padding:3px;}
			//#serv_det th{ border-bottom:1px solid;font-size:13px;}
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
				if($_GET[val]>0)
				{
					echo "<b>SUMMARY BILL</b><br/>";
				}
				?>
			</div>
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
					<tr style="display:none;">
						<th>IPD Serial No</th><td><?php echo $uhid_and_opdid["ipd_serial"]; ?></td>
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
						<th>Address</th><td colspan="3">: <?php echo $address; ?></td>
						<th>Ref By</th>
						<td>: <?php echo $ref_doc["ref_name"]; ?></td>
					</tr>
					<tr>
						<th>Organization</th>
						<td colspan="5">: <?php echo $source_name["source_type"]; ?></td>
					</tr>
				</table>
				
				<table class="table table-bordered" id="serv_det" style="font-size:13px;">
				<tr style="border-bottom: 1px solid #000;">
					<th>#</th>
					<th>Description</th>
					<!--<th>Quantity</th>-->
					<th colspan="2"> Amount</th>
				</tr>
				<?php
				$i=1;
				$tot=0;
				$qry=mysqli_query($link,"select distinct group_id from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd'");
				while($q=mysqli_fetch_array($qry))
				{
					$group=mysqli_fetch_array(mysqli_query($link,"select * from charge_group_master where group_id='$q[group_id]'"));
					
					$days=$q[days];
					if($q[days]==0)
					{
						$days="";
					}
					
					if($q["group_id"]==104) // Laboratory (Pathology, Radiology, Cardilogy)
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
							
							$tot_group_amount=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and service_id IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='$lab_category[category_id]')"));
							$tot_serv=$tot_group_amount[tot];
							
							$quantity=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]' and service_id IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='$lab_category[category_id]')"));
						
							$tot_serv_str=number_format($tot_serv,2);
							$tot=$tot+$tot_serv;
							//echo "<tr><td>$i</td><td>$group[group_name]</td><td>$quantity</td><td>$tot_group_amount[tot]</td></tr>";	
							echo "<tr><td> &nbsp; $i</td><td>$group_name</td><td colspan='2'> &nbsp; $tot_serv_str</td></tr>";
						}
					}
					else
					{
						if($q[group_id]=='141')
						{
							$tot_group_amount=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]'"));
							$tot_serv=$tot_group_amount[tot];
							//$tot_serv=$tot_group_amount[tot]*$no_of_days;
						}else
						{
							$tot_group_amount=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]'"));
							$tot_serv=$tot_group_amount[tot];
						}
						
						$quantity=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]'"));
						$tot=$tot+$tot_serv;
						$tot_serv_str=number_format($tot_serv,2);
						
						//echo "<tr><td>$i</td><td>$group[group_name]</td><td>$quantity</td><td>$tot_group_amount[tot]</td></tr>";	
						echo "<tr><td> &nbsp; $i</td><td>$group[group_name]</td><td colspan='2'> &nbsp; $tot_serv_str</td></tr>";	
					}
					//$tot=$tot+$tot_serv;			
					$i++;
				}
				
				// OT Charge
				$grp_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
				$grp_tot=$grp_tot_val["g_tot"];
				if($grp_tot>0)
				{
					$tot=$tot+$grp_tot;	
				
					echo "<tr><td> &nbsp; $i</td><td>OT Charge</td><td colspan='2'> &nbsp; $grp_tot</td></tr>";	
				}
				
				$baby_num=1;
				$delivery_check_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
				$delivery_check_num=mysqli_num_rows($delivery_check_qry);
				while($delivery_check_val=mysqli_fetch_array($delivery_check_qry))
				{
					if($delivery_check_num==1)
					{
						$baby_num="";
					}
					
					echo "<tr><td colspan='3' style='border-top: 1px solid #000;'></td></tr>";
					echo "<tr><td colspan='3'><b><i>Baby $baby_num's Charges</i></b></td></tr>";
					echo "<tr><td colspan='3' style='border-top: 1px solid #000;'></td></tr>";
					$qry=mysqli_query($link,"select distinct group_id from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]'");
					while($q=mysqli_fetch_array($qry))
					{
						$group=mysqli_fetch_array(mysqli_query($link,"select * from charge_group_master where group_id='$q[group_id]'"));
					
						$days=$q[days];
						if($q[days]==0)
						{
							$days="";
						}
						
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
								
								$tot_group_amount=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' and group_id='$q[group_id]' and service_id IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='$lab_category[category_id]')"));
								$tot_serv=$tot_group_amount[tot];
								
								$quantity=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' and group_id='$q[group_id]' and service_id IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='$lab_category[category_id]')"));
								$tot=$tot+$tot_serv;
								$tot_serv_str=number_format($tot_serv,2);
								
								//echo "<tr><td>$i</td><td>$group[group_name]</td><td>$quantity</td><td>$tot_group_amount[tot]</td></tr>";	
								echo "<tr><td> &nbsp; $i</td><td>$group_name</td><td colspan='2'> &nbsp; $tot_serv_str</td></tr>";
							}
						}
						else
						{
							if($q[group_id]=='141')
							{
								$tot_group_amount=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' and group_id='$q[group_id]'"));
								$tot_serv=$tot_group_amount[tot];
								//$tot_serv=$tot_group_amount[tot]*$no_of_days;
							}else
							{
								$tot_group_amount=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' and group_id='$q[group_id]'"));
								$tot_serv=$tot_group_amount[tot];
							}
							
							$quantity=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' and group_id='$q[group_id]'"));
							$tot=$tot+$tot_serv;
							$tot_serv_str=number_format($tot_serv,2);
							
							//echo "<tr><td>$i</td><td>$group[group_name]</td><td>$quantity</td><td>$tot_group_amount[tot]</td></tr>";	
							echo "<tr><td> &nbsp; $i</td><td>$group[group_name]</td><td colspan='2'> &nbsp; $tot_serv_str</td></tr>";	
						}
						//$tot=$tot+$tot_serv;			
						$i++;
						
					}
					
					// OT Charge Baby
					$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' "));
					$baby_ot_total=$baby_ot_tot_val["g_tot"];
					
					if($baby_ot_total>0)
					{
						$tot=$tot+$baby_ot_total;	
				
						echo "<tr><td> &nbsp; $i</td><td>OT Charge</td><td colspan='2'> &nbsp; $baby_ot_total</td></tr>";	
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
				
				$td_col_span=2;
			?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right">Total Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($tot,2);?>
					</td>
				</tr>
		<?php
			$mode_pay_total=0;
			$pay_mode_master_qry=mysqli_query($link," SELECT `p_mode_name` FROM `payment_mode_master` WHERE `operation`=1 AND `status`=0 ORDER BY `sequence` ");
			while($pay_mode_master=mysqli_fetch_array($pay_mode_master_qry))
			{
				$mode_pay=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as tot from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and `pay_mode`='$pay_mode_master[p_mode_name]' "));
				$mode_amount=$mode_pay["tot"];
				if($mode_amount>0)
				{
					$mode_pay_total+=$mode_amount;
		?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;"><?php echo $pay_mode_master["p_mode_name"]; ?> Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($mode_amount,2);?>
					</td>
				</tr>
		<?php
				}
			}
				// Credit
				$credit_amount=$tot-$mode_pay_total-$total_discount+$total_refund;
				$tot=round($tot);
				if($credit_amount>0)
				{
		?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right" style="border-right:1px solid #000;">Credit Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format($credit_amount,2);?>
					</td>
				</tr>
		<?php
				}
		?>
				<tr style="border-top:1px solid #000;">
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right">Advance Paid &nbsp; </b></td><td> &nbsp; <?php echo number_format($advance_amount,2);?>
					</td>
				</tr>
				<?php if($total_discount>0){ ?>
				<tr>
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right">Discount &nbsp; </b></td><td> &nbsp; <?php echo number_format($total_discount,2);?>
					</td>
				</tr>
				<?php } ?>
				<?php if($total_refund>0){ ?>
				<tr>
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right">Refunded Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format(($total_refund),2);?>
					</td>
				</tr>
				<?php } ?>
				<?php if($final_amount>0){ ?>
				<tr>
					<td colspan="<?php echo $td_col_span; ?>">
						<b class="text-right">Paid Amount &nbsp; </b></td><td> &nbsp; <?php echo number_format(($final_amount),2);?>
					</td>
				</tr>
				<?php } ?>
				</table>
				
			</div>
			<br>
			<br>
			<br>
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
<style>
.text-right
{
	float:right;
}
.table-bordered th, .table-bordered td
{
	border-left: 1px solid #000;
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
.table
{
	margin-bottom: 5px;
}
@page
{
	margin-top:0cm;
}
</style>
