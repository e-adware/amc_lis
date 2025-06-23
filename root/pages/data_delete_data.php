<?php
session_start();
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

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

if($_POST["type"]=="show_pat")
{
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	
?>
	<table class="table table-bordered table-condensed table-hover">
		<tr>
			<th>#</th>
			<!--<th>Serial No</th>-->
			<th>PIN</th>
			<th>Name-Phone</th>
			<th>Age-Sex</th>
			<th>No. of Test</th>
			<th>Bill Amount</th>
			<th>Date Time</th>
		</tr>
<?php
	$qry=mysqli_query($link, " SELECT a.`name`, a.`sex`, a.`age`, a.`age_type`, a.`phone`,b.*, d.`tot_amount` FROM `patient_info` a, `uhid_and_opdid` b, `centremaster` c, `invest_patient_payment_details` d WHERE a.`patient_id`=b.`patient_id` AND b.`center_no`=c.`centreno` AND c.`backup`=1 AND b.`opd_id`=d.`opd_id` AND b.`date` BETWEEN '$date1' AND '$date2' AND b.`type`='2' ORDER BY d.`tot_amount` DESC ");
	
	$n=1;
	while($pat_val=mysqli_fetch_array($qry))
	{
		$test_num=mysqli_num_rows(mysqli_query($link,"select * from patient_test_details where patient_id='$pat_val[patient_id]' and opd_id='$pat_val[opd_id]' "));
		
		$test_num_del=mysqli_num_rows(mysqli_query($link,"select * from patient_test_details_delete where patient_id='$pat_val[patient_id]' and opd_id='$pat_val[opd_id]' "));
		if($test_num_del>0)
		{
			$tr_style="cursor:pointer;background-color: #efcbcb;";
		}else
		{
			$tr_style="cursor:pointer;";
		}
?>
	<tr onClick="show_test_details('<?php echo $pat_val["patient_id"]; ?>','<?php echo $pat_val["opd_id"]; ?>')" style="<?php echo $tr_style; ?>" >
		<td><?php echo $n; ?></td>
		<!--<td><?php echo $pat_val["ipd_serial"]; ?></td>-->
		<td><?php echo $pat_val["opd_id"]; ?></td>
		<td><?php echo $pat_val["name"]; ?> - <?php echo $pat_val["phone"]; ?></td>
		<td><?php echo $pat_val["age"]; ?> <?php echo $pat_val["age_type"]; ?><?php echo $pat_val["sex"]; ?></td>
		<td><?php echo $test_num; ?></td>
		<td><?php echo $pat_val["tot_amount"]; ?></td>
		<td><?php echo convert_date($pat_val["date"]); ?> <?php echo convert_time($pat_val["time"]); ?></td>
	</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}

if($_POST["type"]=="load_pat_details")
{
	$pid=$_POST["pid"];
	$opd_id=$_POST["opd"];
	
	$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pid'"));
	
	$reg=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where patient_id='$pid' and opd_id='$opd_id' "));
	
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"select * from invest_patient_payment_details where patient_id='$pid' and opd_id='$opd_id' "));
	
	$ref_doc=mysqli_fetch_array(mysqli_query($link," SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$reg[refbydoctorid]' "));
	
	$center=mysqli_fetch_array(mysqli_query($link," SELECT `centrename` FROM `centremaster` WHERE `centreno`='$reg[center_no]' "));
	
	$del_btn_dis="";
	if($pat_pay_det["balance"]>0)
	{
		$del_btn_dis="disabled";
	}
	if($pat_pay_det["dis_amt"]>0)
	{
		$del_btn_dis="disabled";
	}
?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>PIN</th><td><?php echo $opd_id; ?></td>
			<th>Name</th><td><?php echo $info["name"]; ?></td>
			<th>Age-Sex</th><td><?php echo $info["age"]; ?> <?php echo $info["age_type"]; ?> - <?php echo $info["sex"]; ?></td>
		</tr>
		<tr>
			<th>Referred By</th><td colspan="2"><?php echo $ref_doc["ref_name"]; ?></td>
			<th>Center</th><td colspan="2"><?php echo $center["centrename"]; ?></td>
		</tr>
		<tr>
			<th colspan="5">Tests</th>
			<th>Rate</th>
		</tr>
	<?php
		$i=1;
		$test_qry=mysqli_query($link,"select * from patient_test_details where patient_id='$pid' and opd_id='$opd_id' ");
		while($test=mysqli_fetch_array($test_qry))
		{
			$test_val=mysqli_fetch_array(mysqli_query($link," SELECT `testname` FROM `testmaster` WHERE `testid`='$test[testid]' "));
			
			// Check if printed for pathology
			if(!$del_btn_dis && $test["sample_id"]>0)
			{
				$test_print_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `testreport_print` WHERE `patient_id`='$test[patient_id]' AND `opd_id`='$test[opd_id]' AND `ipd_id`='$test[ipd_id]' AND `batch_no`='$test[batch_no]' AND `testid`='$test[testid]' "));
				if($test_print_check)
				{
					$del_btn_dis_print="";
				}else
				{
					$del_btn_dis_print="disabled";
				}
			}
	?>
		<tr>
			<td colspan="5">
				<label><input type="checkbox" class="test" id="test<?php echo $test["testid"]; ?>" value="<?php echo $test["testid"]; ?> " <?php echo $del_btn_dis.$del_btn_dis_print; ?> >
				<?php echo $test_val["testname"]; ?></label> 
			</td>
			<td><?php echo $test["test_rate"]; ?></td>
		</tr>
	<?php
			$i++;
		}
	?>
		<tr>
			<th>Total amount</th><td colspan="2"><?php echo $pat_pay_det["tot_amount"]; ?></td>
			<th>Paid amount</th><td colspan="2"><?php echo $pat_pay_det["advance"]; ?></td>
		</tr>
	<?php
		$del_btn_dis="";
		if($pat_pay_det["balance"]>0)
		{
			$del_btn_dis="disabled";
	?>
		<tr>
			<th>Balance</th>
			<td colspan="5"><?php echo $pat_pay_det["balance"]; ?></td>
		</tr>
	<?php
		}
		if($pat_pay_det["dis_amt"]>0)
		{
			$del_btn_dis="disabled";
	?>
		<tr>
			<th>Discount</th>
			<td colspan="5"><?php echo $pat_pay_det["dis_amt"]; ?></td>
		</tr>
	<?php
		}
	?>
		<tr>
			<td colspan="6">
				<center>
					<button class="btn btn-danger" onClick="delete_sel_test('<?php echo $pid; ?>','<?php echo $opd_id; ?>')" <?php echo $del_btn_dis; ?> >Delete</button>
				</center>
			</td>
		</tr>
	<?php
		$test_del_qry=mysqli_query($link,"select * from patient_test_details_delete where patient_id='$pid' and opd_id='$opd_id' ");
		$test_del_num=mysqli_num_rows($test_del_qry);
		if($test_del_num>0)
		{
			echo "<tr><th colspan='6'><center>Deleted Tests</center></th></tr>";
	?>
		<tr>
			<th colspan="5">Tests</th>
			<th>Rate</th>
		</tr>
	<?php
			while($test_del=mysqli_fetch_array($test_del_qry))
			{
				$test_del_val=mysqli_fetch_array(mysqli_query($link," SELECT `testname` FROM `testmaster` WHERE `testid`='$test_del[testid]' "));
		?>
		<tr>
			<td colspan="5">
				<?php echo $test_del_val["testname"]; ?>
			</td>
			<td><?php echo $test_del["test_rate"]; ?></td>
		</tr>
		<?php
			}
		}
	?>
	</table>
<?php
}

if($_POST["type"]=="delete_selected_tests")
{
	$pid=$_POST["pid"];
	$opd_id=$_POST["opd"];
	$test_str=$_POST["test_str"];
	$user=$_POST["user"];
	
	$del_test_amount=0;
	
	$test_str=explode("@#", $test_str);
	foreach($test_str as $testid)
	{
		if($testid)
		{
			$test_det=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_details where patient_id='$pid' and opd_id='$opd_id' and testid='$testid' "));
			
			$del_test_amount+=$test_det["test_rate"];
			
			mysqli_query($link," INSERT INTO `patient_test_details_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `date`, `time`, `user`, `type`) VALUES ('$test_det[patient_id]','$test_det[opd_id]','$test_det[ipd_id]','$test_det[batch_no]','$test_det[testid]','$test_det[sample_id]','$test_det[test_rate]','$test_det[date]','$test_det[time]','$test_det[user]','$test_det[type]') ");
			
			$test_result_qry=mysqli_query($link,"select * from testresults where patient_id='$pid' and opd_id='$opd_id' and testid='$testid' ");
			$test_result_num=mysqli_num_rows($test_result_qry);
			if($test_result_num>0)
			{
				while($test_result=mysqli_fetch_array($test_result_qry))
				{
					mysqli_query($link," INSERT INTO `testresults_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `sequence`, `result`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) VALUES ('$test_result[patient_id]','$test_result[opd_id]','$test_result[ipd_id]','$test_result[batch_no]','$test_result[testid]','$test_result[paramid]','$test_result[sequence]','$test_result[result]','$test_result[time]','$test_result[date]','$test_result[doc]','$test_result[tech]','$test_result[main_tech]','$test_result[for_doc]') ");
				}
			}
			
			$test_summ_qry=mysqli_query($link,"select * from patient_test_summary where patient_id='$pid' and opd_id='$opd_id' and testid='$testid' ");
			$test_summ_num=mysqli_num_rows($test_summ_qry);
			if($test_summ_num>0)
			{
				while($test_summ=mysqli_fetch_array($test_summ_qry))
				{
					mysqli_query($link," INSERT INTO `patient_test_summary_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `summary`, `time`, `date`, `user`, `doc`, `main_tech`, `for_doc`) VALUES ('$test_summ[patient_id]','$test_summ[opd_id]','$test_summ[ipd_id]','$test_summ[batch_no]','$test_summ[testid]','$test_summ[summary]','$test_summ[time]','$test_summ[date]','$test_summ[user]','$test_summ[doc]','$test_summ[main_tech]','$test_summ[for_doc]') ");
				}
			}
			
			$test_rad_qry=mysqli_query($link,"select * from testresults_rad where patient_id='$pid' and opd_id='$opd_id' and testid='$testid' ");
			$test_rad_num=mysqli_num_rows($test_rad_qry);
			if($test_rad_num>0)
			{
				while($test_rad=mysqli_fetch_array($test_rad_qry))
				{
					mysqli_query($link," INSERT INTO `testresults_rad_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `observ`, `doc`, `time`, `date`) VALUES ('$test_rad[patient_id]','$test_rad[opd_id]','$test_rad[ipd_id]','$test_rad[batch_no]','$test_rad[testid]','$test_rad[observ]','$test_rad[doc]','$test_rad[time]','$test_rad[date]') ");
				}
			}
			
			mysqli_query($link," DELETE FROM `patient_test_details` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `testid`='$testid' ");
			mysqli_query($link," DELETE FROM `phlebo_sample` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `testid`='$testid' ");
			mysqli_query($link," DELETE FROM `testresults` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `testid`='$testid' ");
			mysqli_query($link," DELETE FROM `testresults_rad` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `testid`='$testid' ");
			mysqli_query($link," DELETE FROM `testresults_card` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `testid`='$testid' ");
			mysqli_query($link," DELETE FROM `patient_test_summary` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `testid`='$testid' ");
			mysqli_query($link," DELETE FROM `testreport_print` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `testid`='$testid' ");
			
			mysqli_query($link," INSERT INTO `data_user_record`(`patient_id`, `pin`, `testid`, `user`, `date`, `time`) VALUES ('$pid','$opd_id','$testid','$user','$date','$time') ");
			
		}
	}
	
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"select * from invest_patient_payment_details where patient_id='$pid' and opd_id='$opd_id' "));
	
	$tot_amount=$pat_pay_det["tot_amount"]-$del_test_amount;
	$rest_amount=$pat_pay_det["advance"]-$del_test_amount;
	if($rest_amount>=0)
	{
		mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `typeofpayment`='B' ");
		
		mysqli_query($link," UPDATE `invest_payment_detail` SET `amount`='$rest_amount' WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' ");
		
		mysqli_query($link," UPDATE `invest_patient_payment_details` SET `tot_amount`='$tot_amount',`advance`='$rest_amount' WHERE `patient_id`='$pid' AND `opd_id`='$opd_id' ");
		
		echo "1";
		
	}else
	{
		echo "Error !";
	}
}

if($_POST["type"]=="load_tot_paid_amount")
{
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	
	$pat_pay=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(d.`advance`) AS `tot` FROM `uhid_and_opdid` b, `centremaster` c, `invest_patient_payment_details` d WHERE b.`center_no`=c.`centreno` AND c.`backup`=1 AND b.`opd_id`=d.`opd_id` AND b.`date` BETWEEN '$date1' AND '$date2' AND b.`type`='2' "));
	
	$pat_pay_amt=$pat_pay["tot"];
	
	echo $pat_pay_amt;
	
}

?>
