<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$user=base64_decode($_GET['user']);
$pat_info=$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));

if($pat["dob"]!=""){ $age=age_calculator($pat["dob"])." (".convert_date_g($pat["dob"]).")"; }else{ $age=$pat["age"]." ".$pat["age_type"]; }

$dt=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$dt[type]' "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$at_doc=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE patient_id='$uhid' and ipd_id='$ipd' ) "));

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
$date_final=mysqli_fetch_array(mysqli_query($link,"select `date` from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and `pay_type`='Final' "));
?>
<html lang="en">
	<head>
		<title>Bill Summary</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../css/custom.css" />
		<style>
			.table td, .table th{border-top:none;padding: 0px;}
			.td_head td, .table th{border-top:none;padding:3px;}
			#serv_det th{ border-bottom:1px solid;font-size:13px;}
			#serv_det td{ font-size:13px;}
		</style>
	</head>
	<body onafterprint="window.close();" onkeypress="close_window(event)">
		<div class="container-fluid">
			<div class="">
				<?php include('page_header.php'); ?>
			</div>
			<hr>
			<div id="final_b" style="font-size:16px;" align="center">
				<?php
				
					echo "<u><b>Day Care Bill</b></u><br/><br/>";
				
				?>
			</div>
			<div>
				<table class="table table-condensed td_head" style="font-size:12px;border: 1px solid #ccc;">
					<tr>
						<th>UHID</th><td><?php echo $pat['patient_id'];?></td>
						<th><?php echo $prefix_det["prefix"]; ?></th><td><?php echo $ipd;?></td>
						<th>Registration Date</th><td><?php echo convert_date_g($dt['date'])." ".convert_time($dt['time']);?></td>
					</tr>
					<tr>
						<th>Name</th><td><?php echo $pat['name'];?></td>
						<th>Age/Sex</th><td><?php echo $age."/".$pat['sex'];?></td>
						<th>Consultant Doctor</th><td> <?php echo $at_doc["Name"]; ?></td>
						<!--<th>Discharge Date</th><td><?php echo convert_date_g($date_final["date"]); ?></td>-->
					</tr>
				</table>
				
				<table class="table table-condensed" id="serv_det">
				<tr>
					<th>#</th>
					<th>Service Description</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
				<?php
				$i=1;
				$tot=0;
				$qry=mysqli_query($link,"select distinct group_id from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd'");
				while($q=mysqli_fetch_array($qry))
				{
					$group=mysqli_fetch_array(mysqli_query($link,"select * from charge_group_master where group_id='$q[group_id]'"));
					
					echo "<tr><td colspan='4'><b><i>$group[group_name]</i></b></td></tr>";
					$sub_grp=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and group_id='$q[group_id]'");
					while($s_g=mysqli_fetch_array($sub_grp))
					{
					
						$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$s_g[service_id]'"));
						
						$days=$q[days];
						if($q[days]==0)
						{
							$days="";
						}
						
						echo "<tr><td>$i</td><td><span style='margin-left:10px'>$s_g[service_text]</span></td><td>$s_g[amount]</td><td>$s_g[amount]</td></tr>";	
						
						$tot=$tot+$s_g[amount];		
						$i++;
					}
				}
				
				$invest=mysqli_query($link,"select * from bill_patient_test_details where patient_id='$uhid' and ipd_id='$ipd'");
				if(mysqli_num_rows($invest)>0)
				{
					echo "<tr><td colspan='4'><b><i>Investigation</i></b></td></tr>";
					while($inv=mysqli_fetch_array($invest))
					{
						$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$inv[testid]'"));
						echo "<tr><td>$i</td><td><span style='margin-left:10px'>$tname[testname]</span></td><td></td><td>$inv[test_rate]</td></tr>";	
						$tot=$tot+round($inv[test_rate]);
						$i++;
					}
				}
				
				$pharm=mysqli_query($link,"select * from bill_ph_sell_details where patient_id='$uhid' and ipd_id='$ipd'");
				if(mysqli_num_rows($pharm)>0)
				{
					
					echo "<tr><td colspan='4'><b><i>Pharmacy</i></b></td></tr>";
					while($prm=mysqli_fetch_array($pharm))
					{
						$pname=mysqli_fetch_array(mysqli_query($link,"select item_name from ph_item_master where item_code='$prm[item_code]'"));
						echo "<tr><td>$i</td><td><span style='margin-left:10px'>$pname[item_name] (Unit - $prm[sale_qnt])</span></td><td></td><td>$prm[net_amount]</td></tr>";	
						$tot=$tot+$prm[net_amount];
						$i++;
					}
					
				}
				
				$paym=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd'"));
				$tot_phar=0;
				$pharm_pay=mysqli_query($link,"select distinct(bill_no) from bill_ph_sell_details where patient_id='$uhid' and ipd_id='$ipd' and deleted='0' and manip='0'");
				while($phr=mysqli_fetch_array($pharm_pay))
				{
					$paid=mysqli_fetch_array(mysqli_query($link,"select paid from bill_ph_sell_details where bill_no='$phr[bill_no]'"));
					$tot_phar=$tot_phar+$paid[paid];
				}
				$discountt=mysqli_fetch_array(mysqli_query($link,"select sum(discount) as dis from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
				$discountt=$discountt["dis"];
				
				$summ=$paym[tot]+$tot_phar;
				if($summ=='')
				{
					$summ=0;
				}
				
				$tot=round($tot);
				?>
				<tr><th colspan="4"></th></tr>
				<tr><td colspan="2"></td><td>Total Amount</td><td>: <?php echo number_format($tot,2);?></td></tr>
				<tr><td colspan="2"></td><td>Advance</td><td>: <?php echo number_format($summ,2);?></td></tr>
				<?php if($discountt>0){ ?>
				<tr><td colspan="2"></td><td>Discount</td><td>: <?php echo number_format($discountt,2);?></td></tr>
				<?php } ?>
				
				<?php
				if($tot>$summ)
				{
					$res=$tot-$summ-$discountt;
					?> <tr><td colspan="2"></td><td>Balance</td><td>: <?php echo number_format($res,2);?></td></tr> <?php					
				}
				else if($summ>$tot)
				{
					$res=$summ-$tot;
					?> <tr><td colspan="2"></td><td>Refund</td><td>: <?php echo number_format($res,2);?></td></tr> <?php					
				}
				?>
				<tr><td colspan="4"><b><i>Amount in words: <?php echo convert_number($summ);?></i></b></td></tr>
				</table>
				
			</div>
			<div class="span8"></div>
			<div class="span4 text-right" style="margin-top: 6%;line-height: 12px;">
				<b>FOR <?php echo $company_info["name"]; ?></b>
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
hr
{
	margin: 10px 0;
}

</style>
