<?php
include("../../includes/connection.php");

$type=$_POST["type"];
$date=date("Y-m-d");
$time=date("H:i:s");

$rupees_symbol="&#x20b9; ";

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d F Y', $timestamp);
		return $new_date;
	}
}
// Time format convert
function convert_time($time)
{
	if($time)
	{
		$time = date("g:i A", strtotime($time));
		return $time;
	}
}

if($type==1)
{
	$val=$_POST['val'];
	$typ=$_POST['typ'];
	
	if(strlen($val)>2)
	{
		$qry=" SELECT a.*, b.`name`,b.`phone` FROM `uhid_and_opdid` a, `patient_info` b WHERE a.`patient_id`=b.`patient_id` and a.`type` IN(4,5,6,7,9) ";
		
		if($typ=="name")
		{
			$qry.=" AND b.`name` like '%$val%' ";
		}
		if($typ=="pin")
		{
			$qry.=" AND a.`opd_id` like '$val%' ";
		}
		if($typ=="uhid")
		{
			$qry.=" AND b.`patient_id` like '$val%' ";
		}
		if($typ=="phone")
		{
			$qry.=" AND b.`phone` like '$val%' ";
		}
	}
	
	$qry.=" ORDER BY a.`slno` DESC ";
	
	//echo $qry;
	
	$qry=mysqli_query($link, $qry);
	?>
	<table class="table table-condensed table-bordered">
		<th>#</th><th>UHID</th><th>OPD ID</th><th>Name</th><th>Phone</th>
	<?php
	$i=1;
	while($pat_info=mysqli_fetch_array($qry))
	{
		//$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
		
		//$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
		
	?>
		<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $pat_info['patient_id'];?>','<?php echo $pat_info['opd_id'];?>','<?php echo $typ;?>','<?php echo $pat_info["type"];?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $pat_info['patient_id']."@@".$pat_info['opd_id']."@@".$typ."@@".$pat_info["type"];?>"/></td>
			<td><?php echo $pat_info['opd_id'];?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $pat_info['phone'];?></td>
		</tr>
		<?php	
		$i++;
	}
	?>
	</table>
<?php
}

if($type==2)
{
	$charge_id=$_POST['charge_id'];
	
	$charge_det=mysqli_fetch_array(mysqli_query($link," SELECT `amount` FROM `charge_master` WHERE `charge_id`='$charge_id' "));	
	
	echo $charge_det["amount"];
	
}

if($type==3)
{
	$uhid=$_POST['uhid'];
	$opd_id=$_POST['opd_id'];
	$dis_amt=$_POST['dis_amt'];
	$charge_id=$_POST['charge_id'];
	$service_amount=$_POST['service_amount'];
	$reason=mysqli_real_escape_string($link, $_POST['reason']);
	$user=$_POST['user'];
	
	$charge=mysqli_fetch_array(mysqli_query($link," SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$charge_id' "));	
	$service_text=$charge["charge_name"];
	
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS `tot_day_amount` FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id'"));	
	$pat_tot_paid=$pat_pay_det["tot_day_amount"];
	
	$service_amount_final=$service_amount-$dis_amt;
	
	$refund_amount=$pat_tot_paid-$service_amount_final;
	
	// Edit Counter
	$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `type`='5' "));
	$edit_counter_num=$edit_counter["cntr"];
	$counter_num=$edit_counter_num+1;
	
	$pat_serv_det_qry=mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id'");
	
	while($pat_serv_det=mysqli_fetch_array($pat_serv_det_qry))
	{
		mysqli_query($link," INSERT INTO `ipd_pat_service_details_edit`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `counter`, `bed_id`) VALUES ('$pat_serv_det[patient_id]','$pat_serv_det[ipd_id]','$pat_serv_det[group_id]','$pat_serv_det[service_id]','$pat_serv_det[service_text]','$pat_serv_det[ser_quantity]','$pat_serv_det[rate]','$pat_serv_det[amount]','$pat_serv_det[days]','$pat_serv_det[user]','$pat_serv_det[time]','$pat_serv_det[date]','$counter_num','$pat_serv_det[bed_id]') ");
	}
	
	$pat_pay_det_qry=mysqli_query($link,"SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id'");
	
	while($pat_pay_det=mysqli_fetch_array($pat_pay_det_qry))
	{
		mysqli_query($link," INSERT INTO `ipd_advance_payment_details_edit`(`patient_id`, `ipd_id`, `bill_no`, `tot_amount`, `discount`, `amount`, `balance`, `refund`, `pay_type`, `pay_mode`, `time`, `date`, `user`, `counter`) VALUES ('$pat_pay_det[patient_id]','$pat_pay_det[ipd_id]','$pat_pay_det[bill_no]','$pat_pay_det[tot_amount]','$pat_pay_det[discount]','$pat_pay_det[amount]','$pat_pay_det[balance]','$pat_pay_det[refund]','$pat_pay_det[pay_type]','$pat_pay_det[pay_mode]','$pat_pay_det[time]','$pat_pay_det[date]','$pat_pay_det[user]','$counter_num') ");
		
	}
	
	// edit counter record
	mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$uhid','$opd_id','$date','$time','$user','5','$counter_num') ");
	
	// Refund record
	mysqli_query($link, " INSERT INTO `invest_payment_refund`(`patient_id`, `opd_id`, `ipd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd_id','','$pat_tot_paid','$refund_amount','$reason','$date','$time','$user') ");
	
	// Delete old service
	mysqli_query($link, " DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' ");
	
	// Insert new servce
	mysqli_query($link, " INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$opd_id','166','$charge_id','$service_text','1','$service_amount','$service_amount','0','$user','$time','$date','0') ");
	
	// Update Paymemt
	mysqli_query($link, " UPDATE `ipd_advance_payment_details` SET `tot_amount`='$service_amount',`amount`='$service_amount_final' WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' ");
	
	$last_slno=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' ORDER BY `slno` DESC "));
	
	// Updte Doctor Service
	mysqli_query($link, " UPDATE `doctor_service_done` SET `service_id`='$charge_id',`user`='$user',`rel_slno`='$last_slno[slno]' WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' ");
	
}

if($type==99)
{
	$uhid=$_POST['uhid'];
}
?>
