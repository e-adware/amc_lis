<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$date=date("Y-m-d");
$time=date("H:i:s");

$rupees_symbol="&#x20b9; ";

if($_POST["type"]=="load_all_lab_pat")
{
	$fdate=$_POST["from"];
	$tdate=$_POST["to"];
	$pat_name=$_POST["pat_name"];
	$pat_uhid=$_POST["pat_uhid"];
	$pin=$_POST["pin"];
	$pat_type=$_POST["pat_type"];
	
	if($pat_type=="1")
	{
		$q=" SELECT a.* FROM `uhid_and_opdid` a, invest_patient_payment_details b WHERE a.`date` BETWEEN '$fdate' AND '$tdate' AND a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`dis_amt`=0 AND b.`advance`=0 ";
	}
	if($pat_type=="2")
	{
		$q=" SELECT a.* FROM `uhid_and_opdid` a, invest_patient_payment_details b WHERE a.`date` BETWEEN '$fdate' AND '$tdate' AND a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND (b.`dis_amt`>0 OR b.`advance`>0) ";
	}
	if($pat_type=="3")
	{
		$q=" SELECT a.* FROM `uhid_and_opdid` a, invest_patient_payment_details b WHERE a.`date` BETWEEN '$fdate' AND '$tdate' AND a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` ";
	}
	
	if($pat_name)
	{
		$q.=" AND a.`patient_id` IN ( SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$pat_name%' ) ";
	}
	
	if($pat_uhid)
	{
		$q.=" AND a.`patient_id` like '$pat_uhid%' ";
	}
	if($pin)
	{
		$q.=" AND a.`opd_id` like '$pin%' ";
	}
	
	$q.=" AND a.`type`='2' ";
	$q.=" order by a.`slno` DESC";
	
	//echo $q;
	
	$qq_qry=mysqli_query($link, $q );
	$qq_num=mysqli_num_rows($qq_qry);
	
	if($qq_num>0)
	{
		
	?>
	<span style="float:right;">
		<!--<button type="button" class="btn btn-info" onclick="print_rep()"><b class="icon-print icon-large"></b> Print</button>-->
	</span>
	<table class="table table-bordered text-center">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Age/Sex</th>
			<th>Bill Amount</th>
			<th>Date Time</th>
			<th>Center</th>
		</tr>
	<?php
		$n=1;
		while($qq=mysqli_fetch_array($qq_qry))
		{
			$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$qq[patient_id]' AND `opd_id`='$qq[opd_id]' "));
			if($pat_pay_detail['tot_amount']>0)
			{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$qq[patient_id]' "));
			if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			
			$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT `tot_amount` FROM `invest_patient_payment_details` WHERE `patient_id`='$qq[patient_id]' AND `opd_id`='$qq[opd_id]' "));
			
			$cashier_access_num=0;
			$cashier_access=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cashier_access` WHERE `emp_id`='$c_user' "));
			if($cashier_access["lab_cashier"]>0)
			{
				$cashier_access_num=1;
			}
			$cname=mysqli_fetch_array(mysqli_query($link,"select centrename from centremaster where centreno='$qq[center_no]'"));
			
			$cab=mysqli_fetch_array(mysqli_query($link,"select * from patient_cabin where patient_id='$qq[patient_id]' and opd_id='$qq[opd_id]'"));
		?>
			<tr onClick="redirect_page('<?php echo $qq["patient_id"]; ?>','<?php echo $qq["opd_id"]; ?>','<?php echo $cashier_access_num; ?>')" style="cursor:pointer;">
				<td><?php echo $n; ?></td>
				<td><?php echo $qq["patient_id"]; ?></td>
				<td><?php echo $qq["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $age."/".$pat_info["sex"]; ?></td>
				<td><?php echo $rupees_symbol.$pat_pay_detail["tot_amount"]; ?></td>
				<td><?php echo convert_date_g($qq["date"]); ?> <?php echo convert_time($qq["time"]); ?></td>
				<td><?php echo $cname['centrename'];?></td>
			</tr>
		<?php
			$n++;
			
		}
	}
	?>
	</table>
	<?php
	}
}

if($_POST["type"]=="save_pat_payment")
{
	$mode=$_POST["mode"];
	$regd_fee=$_POST["regd_fee"];
	$total=$_POST["total"];
	$dis_per=$_POST["dis_per"];
	$dis_amnt=$_POST["dis_amnt"];
	$dis_reason=$_POST["dis_reason"];
	$advance=$_POST["advance"];
	$bal_reason=$_POST["bal_reason"];
	$balance=$_POST["balance"];
	$pay_mode=$_POST["pay_mode"];
	
	$user=$_POST["user"];
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	
	if($mode=="Save")
	{
		
		mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `tot_amount`='$total',`dis_per`='$dis_per',`dis_amt`='$dis_amnt',`dis_reason`='$dis_reason',`advance`='$advance',`balance`='$balance',`bal_reason`='$bal_reason',`user`='$user' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		
		$reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
		if($reg["date"]==$date)
		{
			mysqli_query($link, " UPDATE `invest_payment_detail` SET `payment_mode`='$pay_mode', `amount`='$advance', `user`='$user', `time`='$time', `date`='$date' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' ");
		}else
		{	
			$bill_no=generate_bill_no('LB');
					
			$check_bill_no=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_detail where bill_no='$bill_no' "));
			if($check_bill_no)
			{
				$bill_no=generate_bill_no('LB');
			}

			mysqli_query($link, " INSERT INTO `invest_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','B','$advance','$user','$time','$date') ");
		}
		
	}
	if($mode=="Update")
	{
		// Edit Counter
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `type`='2' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
			
			// edit counter record
			mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$uhid','$opd_id','$date','$time','$user','2','$counter_num') ");
			
			// Payment
				// invest_patient_payment_details
				$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				mysqli_query($link, "  INSERT INTO `invest_patient_payment_details_edit`(`patient_id`, `opd_id`, `regd_fee`, `tot_amount`, `dis_per`, `dis_amt`, `dis_reason`, `advance`, `balance`, `bal_reason`, `date`, `time`, `user` ,`counter`) VALUES ('$inv_pat_pay_detail[patient_id]','$inv_pat_pay_detail[opd_id]','$inv_pat_pay_detail[regd_fee]','$inv_pat_pay_detail[tot_amount]','$inv_pat_pay_detail[dis_per]','$inv_pat_pay_detail[dis_amt]','$inv_pat_pay_detail[dis_reason]','$inv_pat_pay_detail[advance]','$inv_pat_pay_detail[balance]','$inv_pat_pay_detail[bal_reason]','$inv_pat_pay_detail[date]','$inv_pat_pay_detail[time]','$inv_pat_pay_detail[user]','$counter_num') ");
				// invest_payment_detail
				$inv_pay_detail_qry=mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
				while($inv_pay_detail=mysqli_fetch_array($inv_pay_detail_qry))
				{
					mysqli_query($link, " INSERT INTO `invest_payment_detail_edit`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `user`, `time`, `date` ,`counter`) VALUES ('$inv_pay_detail[patient_id]','$inv_pay_detail[opd_id]','$inv_pay_detail[bill_no]','$inv_pay_detail[payment_mode]','$inv_pay_detail[typeofpayment]','$inv_pay_detail[amount]','$inv_pay_detail[user]','$inv_pay_detail[time]','$inv_pay_detail[date]','$counter_num') ");
				}
				// Test Entry
				$test_qry=mysqli_query($link, "  SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
				//mysqli_query($link, " DELETE FROM `patient_test_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' AND `counter`='$counter_num' ");
				while($test_val=mysqli_fetch_array($test_qry))
				{
					mysqli_query($link, "  INSERT INTO `patient_test_details_edit`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `date`, `time`, `user`, `type` ,`counter`) VALUES ('$test_val[patient_id]','$test_val[opd_id]','$test_val[ipd_id]','$test_val[batch_no]','$test_val[testid]','$test_val[sample_id]','$test_val[test_rate]','$test_val[date]','$test_val[time]','$test_val[user]','$test_val[type]','$counter_num') ");
				}
				// Vaccu Entry
				$vaccu_qry=mysqli_query($link, "  SELECT * FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
				//mysqli_query($link, " DELETE FROM `patient_vaccu_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' AND `counter`='$counter_num' ");
				while($vaccu=mysqli_fetch_array($vaccu_qry))
				{
					mysqli_query($link, " INSERT INTO `patient_vaccu_details_edit`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vaccu_id`, `rate`, `time`, `date` ,`counter`) VALUES ('$vaccu[patient_id]','$vaccu[opd_id]','$vaccu[ipd_id]','$vaccu[batch_no]','$vaccu[vaccu_id]','$vaccu[rate]','$vaccu[time]','$vaccu[date]','$counter_num') ");
				}
		
		mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `tot_amount`='$total',`dis_per`='$dis_per',`dis_amt`='$dis_amnt',`dis_reason`='$dis_reason',`advance`='$advance',`balance`='$balance',`bal_reason`='$bal_reason',`user`='$user' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		
		mysqli_query($link, " DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='B' ");
		
		mysqli_query($link, " UPDATE `invest_payment_detail` SET `payment_mode`='$pay_mode', `amount`='$advance', `user`='$user'  WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' ");
	}
	
}
