<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$user=base64_decode($_GET['user']);
$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));

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
?>
<html lang="en">
	<head>
		<title>Bill Summary</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<style>
			.table td, .table th{border-top:none;padding: 0px;}
		</style>
	</head>
	<body>
		<div class="container-fluid">
			<div style="height:100px;background:snow;">
				
			</div>
			<div id="final_b" style="font-size:25px;" align="center">
				<?php
				if($_GET[val]>0)
				{
					echo "<u><b>FINAL BILL</b></u><br/><br/>";
				}
				?>
			</div>
			<div>
				<table class="table table-condensed" style="font-size:12px;">
					<tr>
						<th>UHID</th><td><?php echo $uhid;?></td>
						<th>IPD</th><td><?php echo $ipd;?></td>
						<th>Admission Date</th><td><?php echo convert_date_g($dt['date'])." ".convert_time($dt['time']);?></td>
					</tr>
					<tr>
						<th>Name</th><td><?php echo $pat['name'];?></td>
						<th>Age/Sex</th><td><?php echo $pat['age']." ".$pat['age_type']."/".$pat['sex'];?></td>
						<th>Discharge Date</th><td></td>
					</tr>
				</table>
				<table class="table table-condensed" style="font-size:12px;">
					<tr style="border-bottom:1px solid;">
						<th>Description</th><th width="20%">Date</th><th width="15%">Unit Rate</th><th width="5%">Qty</th><th width="20%">Amount(Rs)</th>
					</tr>
					<!------------------------------------------>
					<?php
					$qry=mysqli_query($link,"SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` ASC");
					$num4=mysqli_num_rows($qry);
					if($num4>0)
					{
						?>
						<tr>
							<th colspan="5"><u>Bed Charges</u></th>
						</tr>
						<?php
						$totl=0;
						while($res=mysqli_fetch_array($qry))
						{
							$ldt=mysqli_fetch_array(mysqli_query($link,"SELECT `time`,`date` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `ward_id`='$res[ward_id]' AND `bed_id`='$res[bed_id]' AND `alloc_type`='0' AND `slno`>'$res[slno]' ORDER BY `slno` ASC LIMIT 0,1"));
							$wd=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$res[ward_id]'"));
							$bd=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no`,`charges` FROM `bed_master` WHERE `bed_id`='$res[bed_id]'"));
							if($ldt['date']=="")
							$last=date("d-M-y");
							else
							$last=convert_date($ldt['date']);
							$diff=abs(strtotime($res['date'])-strtotime($last));
							$diff=$diff/60/60/24;
							$chrg=$diff*$bd['charges'];
							$totl+=$chrg;
						?>
						<tr>
							<td><?php echo $wd['name']." ".$bd['bed_no'];?></td><td><?php echo convert_date($res['date'])." <b>to</b> ".$last;?></td><td><?php echo $bd['charges'];?></td><td><?php echo $diff;?></td><td><?php echo val_con($chrg);?></td>
						</tr>
						<?php
						$row++;
						}
						$grand_tot+=$totl;
						?>
					<tr style="border-top:1px solid;">
						<td></td><td></td><th><u>Bed Charge Total</u></th><td></td><th><?php echo currency($totl);?></th>
					</tr>
					<!------------------------------------------>
					<?php
					}
					$con_tot=0;
					$qry=mysqli_query($link,"SELECT DISTINCT `date` FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
					$num1=mysqli_num_rows($qry);
					if($num1>0)
					{
						?>
						<tr>
							<th colspan="5"><u>Consultation Fees</u></th>
						</tr>
						<?php
					while($res=mysqli_fetch_array($qry))
					{
						$q=mysqli_query($link,"SELECT DISTINCT `consultantdoctorid` FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$res[date]'");
						while($r=mysqli_fetch_array($q))
						{
							$count=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) AS total FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `consultantdoctorid`='$r[consultantdoctorid]' AND `date`='$res[date]'"));
							$d=mysqli_fetch_array(mysqli_query($link,"SELECT `Name`,`ipd_visit_fee` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$r[consultantdoctorid]'"));
							$chg=$d['ipd_visit_fee']*$count['total'];
							$con_tot+=$chg;
						?>
						<tr>
							<td><?php echo $d['Name'];?></td><td><?php echo convert_date_g($res['date']);?></td><td><?php echo val_con($d['ipd_visit_fee']);?></td><td><?php echo $count['total'];?></td><td><?php echo val_con($chg);?></td>
						</tr>
						<?php
						$row++;
						}
					}
					$grand_tot+=$con_tot;
					?>
					<tr style="border-top:1px solid;">
						<td></td><td></td><th colspan="2"><u>Consulation Total</u></th><th><?php echo currency($con_tot);?></th>
					</tr>
					<!------------------------------------------>
					<?php
					}
					/*
					$amt=0;
					$qry=mysqli_query($link,"SELECT DISTINCT `date` FROM `ipd_pat_medicine_given` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `status`='1'");
					$num2=mysqli_num_rows($qry);
					if($num2>0)
					{
						?>
						<tr>
							<th colspan="5"><u>Drugs</u></th>
						</tr>
						<?php
						while($res=mysqli_fetch_array($qry))
						{
						$q=mysqli_query($link,"SELECT DISTINCT `item_code` FROM `ipd_pat_medicine_given` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `status`='1' AND `date`='$res[date]'");
						while($r=mysqli_fetch_array($q))
						{
							$drug=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) AS total FROM `ipd_pat_medicine_given` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `item_code`='$r[item_code]' AND `status`='1' AND `date`='$res[date]'"));
							$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name`,`item_mrp` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
							$amt+=$itm['item_mrp']*$drug['total'];
						?>
						<tr>
							<td><?php echo $itm['item_name'];?></td><td><?php echo convert_date_g($res['date']);?></td><td><?php echo $itm['item_mrp'];?></td><td><?php echo $drug['total'];?></td><td><?php echo val_con($itm['item_mrp']*$drug['total']);?></td>
						</tr>
						<?php
						$row++;
						}
						$grand_tot+=$amt;
					}
					?>
					<tr style="border-top:1px solid;">
						<td></td><td></td><th colspan="2"><u>Drugs Total</u></th><th><?php echo currency($amt);?></th>
					</tr>
					<!------------------------------------------>
					<?php
					}
					*/
					
					$qry=mysqli_query($link,"SELECT DISTINCT `date` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
					$num3=mysqli_num_rows($qry);
					if($num3>0)
					{
						?>
						<tr>
							<th colspan="5"><u>Investigation</u></th>
						</tr>
						<?php
						$amt=0;
						$tst_count=0;
						while($res=mysqli_fetch_array($qry))
						{
							$q=mysqli_query($link,"SELECT DISTINCT `testid` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$res[date]'");
							while($r=mysqli_fetch_array($q))
							{
								$test=mysqli_fetch_array(mysqli_query($link,"SELECT `testname`,`rate` FROM `testmaster` WHERE `testid`='$r[testid]'"));
								$tst_count=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) AS total FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `testid`='$r[testid]' AND `date`='$res[date]'"));
								$amt+=$test['rate']*$tst_count['total'];
								$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `testid`='$r[testid]'"));
							?>
							<tr>
								<td><?php echo $test['testname'];?></td><td><?php echo convert_date_g($res['date']);?></td><td><?php echo $test['rate'];?></td><td><?php echo $tst_count['total'];?></td><td><?php echo val_con($test['rate']*$tst_count['total']);?></td>
							</tr>
							<?php
							$row++;
							}
						}
						$grand_tot+=$amt;
					?>
					<tr style="border-top:1px solid;">
						<td></td><td></td><th colspan="2"><u>Investigation Total</u></th><th><?php echo currency($amt);?></th>
					</tr>
					<!------------------------------------------>
					<?php
					$cons_tot=0;
					$cons_gst=0;
					$ipd_cons=mysqli_query($link,"select distinct consumable_id from ipd_pat_consumable where `patient_id`='$uhid' AND `ipd_id`='$ipd'");
					$chk_cons=mysqli_num_rows($ipd_cons);
					if($chk_cons>0)
					{
						?>
						<tr>
							<th colspan="5"><u>Consumables</u></th>
						</tr>
						<?php
						while($con_item=mysqli_fetch_array($ipd_cons))
						{
							$item_tot=mysqli_fetch_array(mysqli_query($link,"select sum(quantity) as tot from ipd_pat_consumable where `patient_id`='$uhid' AND `ipd_id`='$ipd' and consumable_id='$con_item[consumable_id]'"));							
							$item_det=mysqli_fetch_array(mysqli_query($link,"select * from inv_indent_master where slno='$con_item[consumable_id]'"));
							
							$tot_amount=number_format($item_tot[tot]*$item_det[price],2);
							
							if($item_det[gst_id]>0)
							{
								$gst_p=mysqli_fetch_array(mysqli_query($link,"select gst_percent from inv_gst where gst_id='$item_det[gst_id]'"));
								
								$gst_amount=$tot_amount*$gst_p[gst_percent]/100;	
								
								$cons_gst=$cons_gst+$gst_amount;
							}
							
							?>
							<tr>
								<td><?php echo $item_det[name];?></td>
								<td><?php echo convert_date_g(date("Y-m-d"));?></td>
								<td><?php echo $item_det[price];?></td>
								<td><?php echo $item_tot[tot];?></td>
								<td><?php echo $tot_amount;?></td>
							</tr>
							<?php
							$cons_tot=$cons_tot+$tot_amount;
						}
						?>
						
						<tr style="border-top:1px solid;">
							<td></td><td></td><th colspan="2"><u>Consumable Total</u></th><th><?php echo currency($cons_tot);?></th>
						</tr>
						<tr>
							<td></td><td></td><th colspan="2"><u>GST%</u></th><th><?php echo currency($cons_gst);?></th>
						</tr>
						<?php
						
						$grand_tot+=$tot_amount;
						$gst=$gst+$cons_gst;
					}
					
					
					
					
					}
					$adv=mysqli_query($link,"SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
					$num5=mysqli_num_rows($adv);
					if($num5>0)
					{
						$tot=0;
						?>
						<tr>
							<th colspan="5"><u>Advance Payment</u></th>
						</tr>
						<?php
						$n=1;
						while($res=mysqli_fetch_array($adv))
						{
						?>
						<tr>
							<td>Bill No: <?php echo $res['bill_no'];?></td><td><?php echo convert_date_g($res['date']);?></td><td></td><td></td><td><?php echo $res['amount'];?></td>
						</tr>
						<?php
						$tot+=$res['amount'];
						$n++;
						}
					?>
					<tr style="border-top:1px solid;">
						<td></td><td></td><th colspan="2"><u>Advance Total</u></th><th><?php echo currency($tot);?></th>
					</tr>
					<!------------------------------------------>
					<?php
					
					
					}
					if($num1>0 || $num2>0 || $num3>0)
					{
						$u=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$user'"));
					?>
					<tr style="border-top:1px solid;">
						<td></td><td></td><th colspan="2"><u>Total Hospital Charges</u></th><th><?php echo currency($grand_tot);?></th>
					</tr>
					<tr>
						<td></td><td></td><th colspan="2"><u>GST Charges</u></th><th><?php echo currency($gst);?></th>
					</tr>
					<tr>
						<td></td><td></td><th colspan="2"><u>Total</u></th><th><?php echo currency($gst+$grand_tot);?></th>
					</tr>
					<?php
					$ceiled = ceil($grand_tot);
					$floored = floor($grand_tot);
					$round=round($grand_tot-$floored,2);
					?>
					<tr>
						<th colspan="5"><u>Amount in words</u>:-&nbsp;&nbsp;&nbsp;&nbsp; <?php echo convert_number($gst+$grand_tot);?> only</th>
					</tr>
					<tr>
						<th>Remarks:</th><th colspan="4"></th>
					</tr>
					<tr>
						<th colspan="5">Prepared By: <?php echo $u['name'];?></th>
					</tr>
					<?php
					}
					?>
				</table>
			</div>
		</div>
	</body>
</html>
