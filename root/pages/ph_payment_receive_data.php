<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");

$c_user=trim($_SESSION['emp_id']);

$date=date("Y-m-d");
$time=date("H:i:s");

$rupees_symbol="&#x20b9; ";

if($_POST["type"]=="load_all_pat")
{
	$fdate=$_POST["from"];
	$tdate=$_POST["to"];
	$bill=$_POST["bill"];
	
	$q="SELECT * FROM `ph_sell_master` WHERE `entry_date`='$date' AND `balance`>0 AND `bill_no` not in (SELECT `bill_no` FROM `ph_payment_details` WHERE `entry_date`='$date')";
	
	if($fdate && $tdate)
	{
		$q="SELECT * FROM `ph_sell_master` WHERE `entry_date` between '$fdate' and '$tdate' ";
	}
	
	if($bill)
	{
		$q="SELECT * FROM `ph_sell_master` WHERE `bill_no` like '$bill%' ";
	}
	//$q.=" AND `type`='2' ";
	//$q.=" order by `sl_no` DESC";
	//echo $q;
	$qq_qry=mysqli_query($link, $q );
	$qq_num=mysqli_num_rows($qq_qry);
	
	if($qq_num>0)
	{
	?>
	<table class="table table-bordered text-center">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Patient Type</th>
			<th>Bill Amount</th>
			<th>Amount Paid</th>
			<th>Date Time</th>
		</tr>
	<?php
		$n=1;
		while($qq=mysqli_fetch_array($qq_qry))
		{
			$amt=$qq["total_amt"]-$qq["discount_amt"]-$qq["adjust_amt"];
			$p_typ=mysqli_fetch_array(mysqli_query($link, " SELECT `sell_name` FROM `ph_sell_type` WHERE `sell_id`='$qq[pat_type]'"));
			$typ=$p_typ["sell_name"];
			$cashier_access_num=0;
			$cashier_access=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cashier_access` WHERE `emp_id`='$c_user' "));
			if($cashier_access["lab_cashier"]>0)
			{
				$cashier_access_num=1;
			}
		?>
			<tr onClick="redirect_page('<?php echo $qq["bill_no"]; ?>','<?php echo $cashier_access_num; ?>')" style="cursor:pointer;">
				<td><?php echo $n; ?></td>
				<td><?php echo $qq["bill_no"]; ?></td>
				<td><?php echo $qq["customer_name"]; ?></td>
				<td><?php echo $typ; ?></td>
				<td><?php echo $rupees_symbol.$amt; ?></td>
				<td><?php echo $rupees_symbol.$qq["paid_amt"]; ?></td>
				<td><?php echo convert_date_g($qq["date"]); ?> <?php echo convert_time($qq["time"]); ?></td>
			</tr>
		<?php
			$n++;
			
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
	
	$bill=$_POST["bill_no"];
	$user=$_POST["user"];
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	
	if($mode=="Save")
	{
		//mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `tot_amount`='$total',`dis_per`='$dis_per',`dis_amt`='$dis_amnt',`dis_reason`='$dis_reason',`advance`='$advance',`balance`='$balance',`bal_reason`='$bal_reason',`user`='$user' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		
		//mysqli_query($link, " UPDATE `invest_payment_detail` SET `payment_mode`='$pay_mode', `amount`='$advance', `user`='$user', `time`='$time', `date`='$date' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' ");
		
		//mysqli_query($link,"delete from ph_payment_details where  bill_no='$blno'");
		mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode,check_no,type_of_payment,user,time,dis_reason,bal_reason) values('$bill','$date','$advance','Cash','','A','$user','$time','$dis_reason','$bal_reason')");
		
		$f=mysqli_fetch_array(mysqli_query($link,"select total_amt,discount_amt,adjust_amt,paid_amt,balance from ph_sell_master where bill_no='$bill'"));
		$paid=$f['paid_amt']+$advance;
		$bal=($f['total_amt']-$f['discount_amt']-$f['adjust_amt']-$advance);
		mysqli_query($link,"update ph_sell_master set paid_amt='$paid',balance='$bal' where bill_no='$bill'");
		//echo "update ph_sell_master set paid_amt='$paid',balance='$bal' where bill_no='$bill'";
		
	}
	
	if($mode=="Update")
	{
		// Edit Counter
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `type`='2' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
			
			// edit counter record
			//mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$uhid','$opd_id','$date','$time','$user','2','$counter_num') ");
		$f=mysqli_fetch_array(mysqli_query($link,"select total_amt,discount_amt,adjust_amt,paid_amt,balance from ph_sell_master where bill_no='$bill'"));
		$paid=$f['paid_amt']+$advance;
		$bal=$total-$paid-$f['adjust_amt'];
		if($f['balance']>0)
		{
			mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode,check_no,type_of_payment,user,time,dis_reason,bal_reason) values('$bill','$date','$advance','Cash','','B','$user','$time','$dis_reason','$bal_reason')");
			mysqli_query($link,"update ph_sell_master set `discount_perchant`='$dis_per', `discount_amt`='$dis_amnt', paid_amt='$paid',balance='$bal' where bill_no='$bill'");
		}
		
		//mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `tot_amount`='$total',`dis_per`='$dis_per',`dis_amt`='$dis_amnt',`dis_reason`='$dis_reason',`advance`='$advance',`balance`='$balance',`bal_reason`='$bal_reason',`user`='$user' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		
		//mysqli_query($link, " DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='B' ");
		
		//mysqli_query($link, " UPDATE `invest_payment_detail` SET `payment_mode`='$pay_mode', `amount`='$advance', `user`='$user', `time`='$time', `date`='$date' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' ");
	}
	
}
