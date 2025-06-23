<?php
include("../../includes/connection.php");
require("../../includes/global.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");


if($_POST["type"]=="new_close_account_single")
{
	$user=$_POST["user"];
	
	$c_date=$date;
	
	$counter_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `user`='$user' "));
	$counter=$counter_num+1;
	
	$pay_max=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(MAX(`pay_id`),0) as pay_id FROM `payment_detail_all` "));
	$pay_max_slno=$pay_max['pay_id'];
	
	$refund_max=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(MAX(`slno`),0) as refund_slno FROM `invest_payment_refund` "));
	$refund_max_slno=$refund_max['refund_slno'];
	
	$free_max=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(MAX(`slno`),0) as refund_slno FROM `invest_payment_free` "));
	$free_max_slno=$free_max['refund_slno'];
	
	$ph_max=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(MAX(`sl_no`),0) as ph_slno FROM `ph_payment_details` "));
	$ph_max_slno=$ph_max['ph_slno'];
	
	$ret_max=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(MAX(`slno`),0) as ret_slno FROM `ph_item_return` "));
	$ret_max_slno=$ret_max['ret_slno'];
	
	$exps_max=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(MAX(`slno`),0) as expns_slno FROM `expensedetail` "));
	$exps_max_slno=$exps_max['expns_slno'];
	
	if(!$pay_max_slno){ $pay_max_slno=0; }
	if(!$ph_max_slno){ $ph_max_slno=0; }
	if(!$ret_max_slno){ $ret_max_slno=0; }
	if(!$exps_max_slno){ $exps_max_slno=0; }
	if(!$refund_max_slno){ $refund_max_slno=0; }
	if(!$free_max_slno){ $free_max_slno=0; }
	
	mysqli_query($link, " INSERT INTO `daily_account_close_new`(`pay_id`, `ph_slno`, `ret_slno`, `exp_slno`, `refund_slno`, `free_slno`, `counter`, `close_date`, `user`, `date`, `time`) VALUES ('$pay_max_slno','$ph_max_slno','$ret_max_slno','$exps_max_slno','$refund_max_slno','$free_max_slno','$counter','$c_date','$user','$date','$time') ");
	
	$last_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `user`='$user' AND `counter`='$counter' ORDER BY `slno` DESC "));
	
	if($c_date==$date)
	{
		$msg="Today";
	}else
	{
		$msg=convert_date($c_date);
	}
	
	echo $msg."@#$@".$last_entry["slno"];
}
if($_POST["type"]=="close_account_single")
{
	$user=$_POST["user"];
	
	$c_date=$date;
	
	$counter_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `user`='$user' "));
	$counter=$counter_num+1;
	
	$con_max=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`slno`) as con_slno FROM `consult_payment_detail` "));
	$con_max_slno=$con_max['con_slno'];
	
	$inv_max=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`slno`) as inv_slno FROM `invest_payment_detail` "));
	$inv_max_slno=$inv_max['inv_slno'];
	
	$refund_max=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`slno`) as refund_slno FROM `invest_payment_refund` "));
	$refund_max_slno=$refund_max['refund_slno'];
	
	$free_max=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`slno`) as refund_slno FROM `invest_payment_free` "));
	$free_max_slno=$free_max['refund_slno'];
	
	$ipd_max=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`slno`) as ipd_slno FROM `ipd_advance_payment_details` "));
	$ipd_max_slno=$ipd_max['ipd_slno'];
	
	$ph_max=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`sl_no`) as ph_slno FROM `ph_payment_details` "));
	$ph_max_slno=$ph_max['ph_slno'];
	
	$ret_max=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`slno`) as ret_slno FROM `ph_item_return` "));
	$ret_max_slno=$ret_max['ret_slno'];
	
	$exps_max=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`slno`) as expns_slno FROM `expensedetail` "));
	$exps_max_slno=$exps_max['expns_slno'];
	
	mysqli_query($link, " INSERT INTO `daily_account_close`(`con_slno`, `inv_slno`, `ipd_slno`, `ph_slno`, `ret_slno`, `exp_slno`, `user`, `close_date`, `date`, `time`, `refund_slno`, `free_slno`, `counter`) VALUES ('$con_max_slno','$inv_max_slno','$ipd_max_slno','$ph_max_slno','$ret_max_slno','$exps_max_slno','$user','$c_date','$date','$time','$refund_max_slno','$free_max_slno','$counter') ");
	
	$last_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `user`='$user' AND `counter`='$counter' ORDER BY `slno` DESC "));
	
	if($c_date==$date)
	{
		$msg="Today";
	}else
	{
		$msg=convert_date($c_date);
	}
	
	echo $msg."@#$@".$last_entry["slno"];
}

if($_POST["type"]=="close_account_pharmacy")
{
	
	$c_date=$_POST["c_date"];
	$ph=$_POST["ph"];
	$user=$_POST["user"];
	
	$ph_max=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`sl_no`) as ph_slno FROM `ph_payment_details` "));
	$ph_max_slno=$ph_max['ph_slno'];
	
	$ret_max=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`slno`) as ret_slno FROM `ph_item_return` "));
	$ret_max_slno=$ret_max['ret_slno'];
	
	mysqli_query($link, " INSERT INTO `daily_account_close_pharmacy`(`substore_id`,`ph_slno`, `ret_slno`, `user`, `close_date`, `date`, `time`) VALUES ('$ph','$ph_max_slno','$ret_max_slno','$user','$c_date','$date','$time') ");
	
	//mysqli_query($link, " INSERT INTO `daily_account_close`(`con_slno`, `inv_slno`, `ipd_slno`, `user`, `close_date`, `date`, `time`) VALUES ('$con_max_slno','$inv_max_slno','$ipd_max_slno','$user','$c_date','$date','$time') ");
	
	if($c_date==$date)
	{
		$msg="Today";
	}else
	{
		$msg=convert_date($c_date);
	}
	
	echo $msg;
}

if($_POST["type"]=="view_account_single")
{
	$account_break=$_POST["account_break"];
	$user=$_POST["user"];
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	$ph=$_POST["ph"];
	
	if($user>0)
	{
		$user_str=" and `user`='$user' ";
	}
	
	$check_close_account_today="";
	if($account_break>0)
	{
		$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_pharmacy` WHERE `slno`='$account_break' "));
	}
	if($check_close_account_today)
	{
		$con_max_slno_less=$check_close_account_today['ph_slno'];
		$con_max_slno_less_return=$check_close_account_today['ret_slno'];
		$con_max_slno_str_less=" AND `sl_no`<=$con_max_slno_less ";
		$con_max_slno_str_less_retrun=" AND `slno`<=$con_max_slno_less_return ";
	}
	else
	{
		$con_max_slno_str_less_retrun="";
		$con_max_slno_str_less="";
		$inv_max_slno_str_less="";
		$ipd_max_slno_str_less="";
	}
	
	//$last_date=date('Y-m-d', strtotime($date1. ' - 1 days'));
	//$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `user`='$user_val' AND `date`='$last_date' "));
	
	if($account_break>0)
	{
		$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_pharmacy` WHERE substore_id='$ph' and `slno` IN(SELECT MAX(`slno`) FROM `daily_account_close_pharmacy` WHERE `slno`<$account_break) "));
		
	}else
	{
		$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_pharmacy` WHERE substore_id='$ph' and `slno` IN(SELECT MAX(`slno`) FROM `daily_account_close_pharmacy`) "));
		echo "2";
	}
	if($check_close_account_today)
	{
		$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));
		
		$con_max_slno_grtr=$check_close_account_today['ph_slno'];
		$con_max_slno_grtr_return=$check_close_account_today['ret_slno'];
		$con_max_slno_str_grtr=" AND `sl_no`>$con_max_slno_grtr ";
		$con_max_slno_str_grtr_return=" AND `slno`>$con_max_slno_grtr_return ";
	}
	else
	{
		$con_max_slno_str_grtr_return="";
		$con_max_slno_str_grtr="";
		$inv_max_slno_str_grtr="";
		$ipd_max_slno_str_grtr="";
	}
?>
	<table class="table table-hover table-condensed">
		<tr>
			<th>#</th>
			<th>Date Time</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Bill Amount</th>
			<th>Disc.</th>
			<th>Amount Received</th>
			<th>Balance</th>
			<th>User</th>
		</tr>
<?php
		$n=1;
		
		$q=" SELECT * FROM `ph_payment_details` WHERE `user`>0 $con_max_slno_str_less $con_max_slno_str_grtr ";
		
		$data=mysqli_query($link,$q);
		while($p=mysqli_fetch_array($data))
		{
			
			$reg=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$p[bill_no]' "));
			$vttl+=$reg["total_amt"];
			$vdis+=$reg["discount_amt"];
			$vpaid+=$p["amount"];
			//$vpaid+=$reg["paid_amt"];
			$vbal+=$reg["balance"];
			
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$p[user]' "));
			
	?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo convert_date_g($reg["entry_date"]); ?> <?php echo convert_time($reg["time"]); ?></td>
			<td><?php echo $reg["bill_no"]; ?></td>
			<td><?php echo $reg["customer_name"]; ?></td>
			<td><?php echo $reg["total_amt"]; ?></td>
			<td><?php echo $reg["discount_amt"]; ?></td>
			<td><?php echo $p["amount"]; ?></td>
			<td><?php echo $reg["balance"]; ?></td>
			<td><?php echo substr($user_name["name"],0,12); ?></td>
		</tr>
<?php
			$n++;
		}
		
		
		
?>
     <tr>
		 <td colspan="4" style="font-weight:bold;font-size:11px">Total (* Bill amount is after refund)</td>
		 <td style="font-weight:bold;font-size:11px"><?php echo number_format($vttl,2);?></td>
		 <td style="font-weight:bold;font-size:11px"><?php echo number_format($vdis,2);?></td>
		 <td style="font-weight:bold;font-size:11px"><?php echo number_format($vpaid,2);?></td>
		 <td style="font-weight:bold;font-size:11px"><?php echo number_format($vbal,2);?></td>
		 <td>&nbsp;</td>
     </tr>
     
     <tr>
		 <td colspan="9" style="font-weight:bold;font-size:11px">Payment Refund</td>
		 
     </tr>
     
     <?php
     $i=1;
     //$q=" SELECT * FROM `ph_item_return` WHERE `user`>0  and slno>109";
     $q=" SELECT * FROM `ph_item_return` WHERE `user`>0 $con_max_slno_str_less_retrun $con_max_slno_str_grtr_return ";
     echo $qrefund11;
     
    
     $qrefund=mysqli_query($link,$q);
     while($qrefund1=mysqli_fetch_array($qrefund))
     {
		 $reg=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$qrefund1[bill_no]' "));
		 if($reg['paid_amt']>0 && $reg['balance']==0)
		 {
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$qrefund1[user]' "));
				$vttrfnd+=$qrefund1['amount'];
				?>
				<tr>
				 <td><?php echo $i;?></td>
				 <td><?php echo convert_date_g($qrefund1["date"]); ?> <?php echo convert_time($qrefund1["time"]); ?></td>
				 <td><?php echo $qrefund1['bill_no'];?></td>
				 <td><?php echo $reg["customer_name"]; ?></td>
				 <td>&nbsp;</td>
				 <td>&nbsp;</td>
				 <td><?php echo $qrefund1['amount'];?></td>
				 <td>&nbsp;</td>
				 <td><?php echo substr($user_name["name"],0,12); ?></td>
				</tr>
				<?php
				$i++;}
				?>
			<?php
		}?>	
     
     <tr>
		 <td colspan="4" style="font-weight:bold;font-size:11px">Total Refund</td>
		 <td style="font-weight:bold;font-size:11px">&nbsp;</td>
		 <td style="font-weight:bold;font-size:11px">&nbsp;</td>
		 <td style="font-weight:bold;font-size:11px"><?php echo number_format($vttrfnd,2);?></td>
		 <td style="font-weight:bold;font-size:11px">&nbsp;</td>
		 <td style="font-weight:bold;font-size:11px">&nbsp;</td>
     </tr>
     
      <tr>
		 <td colspan="4" style="font-weight:bold;font-size:11px">Cash in Hand</td>
		 <td style="font-weight:bold;font-size:11px">&nbsp;</td>
		 <td style="font-weight:bold;font-size:11px">&nbsp;</td>
		 <td style="font-weight:bold;font-size:11px"><?php echo number_format($vpaid-$vttrfnd,2);?></td>
		 <td >&nbsp;</td>
		 <td >&nbsp;</td>
     </tr>
     
	</table>
<?php
}


?>
