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
		$qry=" SELECT a.*, b.`name`,b.`phone` FROM `uhid_and_opdid` a, `patient_info` b WHERE a.`patient_id`=b.`patient_id` and a.`type` IN(2,10) ";
		
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
		<th>#</th><th>UHID</th><th>PIN</th><th>Name</th><th>Phone</th>
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
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$tot_amt=$_POST['tot_amt'];
	$ref=$_POST['ref'];
	$res=mysqli_real_escape_string($link, $_POST['res']);
	$user=$_POST['user'];
	if(mysqli_query($link,"INSERT INTO `invest_payment_refund`(`patient_id`, `opd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$tot_amt','$ref','$res','$date','$time','$user')"))
	{
		echo "Saved";
	}
	else
	{
		echo "Error";
	}
}

if($type==3)
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$q=mysqli_query($link,"SELECT * FROM `invest_payment_refund` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$n=mysqli_num_rows($q);
	$y=$n;
	$search=0;
	if($n>0)
	{
	?>
	<input type="hidden" id="rad_value" value="1" />
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Total Amount</th><th>Refund</th><th>Date-Time</th><th>User</th>
		</tr>
	<?php
		$j=1;
		while($r=mysqli_fetch_array($q))
		{
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
	?>
		<tr>
			<td><?php echo $j;?></td>
			<?php if($y>0){ echo "<td rowspan='".$y."'>&#8377; ".$r['tot_amount']."</td>";}?>
			<td><?php echo "&#8377; ".$r['refund_amount'];?></td>
			<td><?php echo convert_date($r['date'])." - ".convert_time($r['time']);?></td>
			<td><?php echo $user_name["name"]; ?></td>
		</tr>
		<?php
		$y=0;
		$j++;
		}
		?>
	</table>
	<?php
	$search=1;
	}
	$qq=mysqli_query($link,"SELECT * FROM `invest_payment_free` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$nn=mysqli_num_rows($qq);
	if($nn>0)
	{
		$rr=mysqli_fetch_array($qq);
	?>
	<input type="hidden" id="rad_value" value="0" />
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Total Amount</th><th>Total Refund</th><th>Reason</th><th>date-Time</th>
		</tr>
		<tr>
			<td><?php echo "&#8377; ".$rr['tot_amount'];?></td>
			<td><?php echo "&#8377; ".$rr['tot_amount'];?></td>
			<td><?php echo $rr['reason'];?></td>
			<td><?php echo convert_date($rr['date'])." - ".convert_time($rr['time']);?></td>
		</tr>
	</table>
	<?php
	$search=1;
	}
	if($search==0)
	{
	?>
	<input type="hidden" id="rad_value" value="" />
	<?php
	}
}

if($type==4)
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$encounter=$_POST['encounter'];
	$user_entry=$_POST['user_entry'];
	$pay_mode=$_POST['pay_mode'];
	
	$dis_date_qry=mysqli_query($link,"SELECT DISTINCT `date` FROM `invest_payment_refund` WHERE `date` BETWEEN '$date1' AND '$date2' ORDER BY `date`");
?>
	<b>Payment refund from <?php echo convert_date($date1)." to ".convert_date($date2);?></b>
	
	<button type="button" class="btn btn-info text-right" onclick="print_page('pay_refund','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	
	<table class="table table-condensed table-bordered" id="ref_tbl">
<?php
		$tot_recv=0;
		$tot_refund=0;
		while($dis_date=mysqli_fetch_array($dis_date_qry))
		{
			?>
			<tr>
				<th colspan="7">Date : <?php echo convert_date($dis_date['date']);?></th>
			</tr>
			<tr>
				<th>#</th>
				<th>PIN</th>
				<th>Name</th>
				<th>Bill Amount</th>
				<th>Refund</th>
				<th>Reason</th>
				<th>Encounter</th>
				<th>User</th>
				<th>Time</th>
			</tr>
<?php
			
			$qry=" SELECT a.*, b.`type` FROM `invest_payment_refund` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`='$dis_date[date]'";
			
			if($encounter>0)
			{
				$qry.=" AND b.`type`='$encounter'";
			}
			
			if($user_entry>0)
			{
				$qry.=" AND a.`user`='$user_entry'";
			}
			
			//echo "<br/>".$qry."<br/>";
			
			$pay_refund_qry=mysqli_query($link,$qry);
			$j=1;
			while($pay_refund=mysqli_fetch_array($pay_refund_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$pay_refund[patient_id]'"));
				
				$pay=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_payment_refund` WHERE `patient_id`='$pay_refund[patient_id]' AND `opd_id`='$pay_refund[opd_id]' AND `date`='$dis_date[date]' AND `user`='$pay_refund[user]' "));
				
				$usr=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$pay_refund[user]'"));
				
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pay_refund[type]' "));
				$Encounter=$pat_typ_text['p_type'];
		?>
				<tr>
					<td><?php echo $j;?></td>
					<td><?php echo $pay_refund["opd_id"];?></td>
					<td><?php echo $pat_info["name"];?></td>
					<td><?php echo $rupees_symbol.$pay['tot_amount'];?></td>
					<td><?php echo $rupees_symbol.$pay['refund_amount'];?></td>
					<td><?php echo $pay['reason'];?></td>
					<td><?php echo $Encounter;?></td>
					<td><?php echo $usr['name'];?></td>
					<td><?php echo convert_time($pay_refund['time']);?></td>
				</tr>
<?php
				$tot_refund+=$pay['refund_amount'];
				$j++;
				
			}
		}
?>
		<tr>
			<th colspan="4" style="text-align:right;">Total : </th>
			<th colspan="4"><?php echo $rupees_symbol.number_format($tot_refund,2);?></th>
		</tr>
	</table>
<?php
}

if($type==5)
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$user=$_POST['user'];
	
	?>
	<table class="table table-condensed table-bordered" id="tst_tbl">
		<tr style="background:#dddddd;">
			<th>#</th><th><label class="tst_lbl" style="display:inline-block;"><input type="checkbox" id="chk_all" onchange="test_all()" /> All</label> Test Name</th><th>Rate</th><th>Status</th>
		</tr>
	<?php
	$j=1;
	$q=mysqli_query($link,"SELECT `testid`,`test_rate` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND testid NOT IN (SELECT testid FROM `invest_payment_refund_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd')");
	while($r=mysqli_fetch_array($q))
	{
		$tst=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$r[testid]'"));
		?>
		<tr>
			<td><?php echo $j;?><span class="strs" style="float:right;display:none;font-size:16px;color:#ED0A0A;">*</span></td>
			<td><label class="tst_lbl"><input type="checkbox" name="" class="tst_id" onchange="chk_if_all()" value="<?php echo $r['testid'];?>" /> <?php echo $tst['testname'];?></label></td>
			<td>
				<?php echo $r['test_rate'];?>
				<input type="hidden" class="tst_rate" value="<?php echo $r['test_rate'];?>" />
			</td>
			<td>
			<?php
				$testresult_path_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd' AND `testid`='$r[testid]' "));
				$testresult_card_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_card` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd' AND `testid`='$r[testid]' "));
				$testresult_radi_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_rad` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd' AND `testid`='$r[testid]' "));
				$testresult_wild_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `widalresult` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd' "));
				$testresult_summ_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `patient_test_summary` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd' AND `testid`='$r[testid]' "));
				if($testresult_path_num>0 || $testresult_card_num>0 || $testresult_radi_num>0 || $testresult_wild_num>0 || $testresult_summ_num>0)
				{
					echo "Reported";
				}
				else
				{
					echo "Not Reported";
				}
			?>
			</td>
		</tr>
		<?php
	$j++;
	}
	?>
	</table>
	<style>
	.table .table
	{
		background:none;
	}
	</style>
	<?php
}

if($type==6)
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$rad=$_POST['rad'];
	$all=$_POST['all'];
	$tot_amt=$_POST['tot_amt'];
	$dis_amt=$_POST['dis_amt'];
	$res=mysqli_real_escape_string($link,$_POST['res']);
	$user=$_POST['user'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	
	if($rad==1)
	{
		$amt=0;
		$al=explode("##",$all);
		foreach($al as $a)
		{
			$v=explode("@",$a);
			$tstid=$v[0];
			$rate=$v[1];
			if($tstid && $rate)
			{
				$amt+=$rate;
				mysqli_query($link,"INSERT INTO `invest_payment_refund_details`(`patient_id`, `opd_id`, `testid`, `rate`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$tstid','$rate','$date','$time','$user')");
				mysqli_query($link,"DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
				mysqli_query($link,"DELETE FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
				mysqli_query($link,"DELETE FROM `phlebo_sample` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
				mysqli_query($link,"DELETE FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
				mysqli_query($link,"DELETE FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
				mysqli_query($link,"DELETE FROM `testresults_card` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
				mysqli_query($link,"DELETE FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
			}
		}
		
		$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
		$upd_amt=$tot_amt-$amt;
		
		if($v["balance"]>0)
		{
			$credit_amount=0;
			
			if($v["balance"]>=$amt)
			{
				$refund_amount=0;
				
				$credit_amount=$v["balance"]-$amt;
			}
			else
			{
				$refund_amount=$amt-$v["balance"];
				
				//$credit_amount=$amt-$v["balance"];
			}
			
			if($credit_amount<0)
			{
				$credit_amount=0;
			}
			
			if($refund_amount<0)
			{
				$refund_amount=0;
			}
			
			if($pat_reg["date"]==$date)
			{
				mysqli_query($link,"UPDATE `invest_payment_detail` SET `balance`='$credit_amount' WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `payment_mode`='Credit'");
			}
		}
		
		$qq=mysqli_query($link,"SELECT * FROM `invest_payment_refund` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `date`='$date' AND `user`='$user'");
		$num=mysqli_num_rows($qq);
		if($num>0)
		{
			$p_amt=mysqli_fetch_array($qq);
			$n_amt=$p_amt['refund_amount']+$refund_amount;
			mysqli_query($link,"UPDATE `invest_payment_refund` SET `refund_amount`='$n_amt' WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `date`='$date' AND `user`='$user'");
		}
		else
		{
			mysqli_query($link,"INSERT INTO `invest_payment_refund`(`patient_id`, `opd_id`, `ipd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd','','$tot_amt','$refund_amount','$res','$date','$time','$user')");
		}
		
		if($upd_amt==0)
		{
			$paid_amt=0;
			$bal=0;
			$dis_amount=0;
		}else
		{
			if($v["balance"]<$amt)
			{
				$upd_amt=$tot_amt-$amt-$v["dis_amt"]; //bill-refund-discount
				$bal=0;
				$paid_amt=$v["advance"]-($amt-$v["balance"]);
				$dis_amount=$v["dis_amt"];
				if($paid_amt<=0)
				{
					$dis_amount=round(($upd_amt*$v["dis_per"])/100);
					$paid_amt=$upd_amt-$dis_amount;
				}
			}
			if($v["balance"]>$amt)
			{
				$upd_amt=$tot_amt-$amt-$v["dis_amt"];
				$bal=$v["balance"]-$amt;
				$paid_amt=$v["advance"];
				$dis_amount=$v["dis_amt"];
			}
			if($v["balance"]==$amt)
			{
				$bal=0;
				$paid_amt=$v["advance"];
				$dis_amount=$v["dis_amt"];
				$upd_amt=$v["tot_amount"]-$amt-$v["dis_amt"];
			}
		}
		
		//~ $paid_amt=$tot_amt-$dis_amt-$v['balance'];
		//~ $bal=$upd_amt-$dis_amt-$paid_amt;
		
		mysqli_query($link,"DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `typeofpayment`='B'");
		mysqli_query($link,"UPDATE `invest_payment_detail` SET `amount`='$paid_amt' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
		mysqli_query($link,"UPDATE `invest_patient_payment_details` SET `tot_amount`='$upd_amt',`dis_amt`='$dis_amount', `advance`='$paid_amt', `balance`='$bal' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
		echo "Done";
	}
	if($rad==0)
	{
		$check=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `invest_payment_free` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
		if($check>0)
		{
			echo "Already Done Free";
		}
		else
		{
			mysqli_query($link,"INSERT INTO `invest_payment_free`(`patient_id`, `opd_id`, `tot_amount`, `free_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$tot_amt','$tot_amt','$res','$date','$time','$user')");
			mysqli_query($link,"DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `typeofpayment`='B'");
			mysqli_query($link,"UPDATE `invest_payment_detail` SET `amount`='0' WHERE `patient_id`='$uhid' AND `opd_id`='$opd' ");
			mysqli_query($link,"UPDATE `invest_patient_payment_details` SET `dis_per`='100', `dis_amt`='$tot_amt', `dis_reason`='$res', `advance`='0', `balance`='0' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
			echo "Done";
		}
	}
}

if($type==7)
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$user=$_POST['user'];
	
?>
	<table class="table table-condensed table-bordered" id="tst_tbl">
		<tr style="background:#dddddd;">
			<th>#</th><th><label class="tst_lbl" style="display:inline-block;"><input type="checkbox" id="chk_all" onchange="test_all()" /> All</label> Service Name</th><th>Amount</th>
		</tr>
<?php
	$j=1;
	$q=mysqli_query($link,"SELECT `service_text`, `service_id`, `amount` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd' AND service_id NOT IN (SELECT service_id FROM `patient_refund_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd')");
	while($r=mysqli_fetch_array($q))
	{
?>
		<tr>
			<td><?php echo $j;?><span class="strs" style="float:right;display:none;font-size:16px;color:#ED0A0A;">*</span></td>
			<td><label class="tst_lbl"><input type="checkbox" name="" class="tst_id" onchange="chk_if_all()" value="<?php echo $r['service_id'];?>" /> <?php echo $r['service_text'];?></label></td>
			<td>
				<?php echo $r['amount'];?>
				<input type="hidden" class="tst_rate" value="<?php echo $r['amount'];?>" />
			</td>
		</tr>
<?php
	$j++;
	}
?>
	</table>
	<style>
	.table .table
	{
		background:none;
	}
	</style>
<?php
}


if($type==8)
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$rad=$_POST['rad'];
	$all=$_POST['all'];
	$refund_amount=$_POST['refund_amount'];
	$res=mysqli_real_escape_string($link,$_POST['res']);
	$user=$_POST['user'];
	
	$pat_bill=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd'"));
	$bill_amount=$pat_bill["tot_amount"];
	
	if($rad==2)
	{
		$all="";
		if($all)
		{
			$amt=0;
			$al=explode("##",$all);
			foreach($al as $a)
			{
				$v=explode("@",$a);
				$tstid=$v[0];
				$rate=$v[1];
				if($tstid && $rate)
				{
					$amt+=$rate;
					mysqli_query($link,"INSERT INTO `invest_payment_refund_details`(`patient_id`, `opd_id`, `testid`, `rate`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$tstid','$rate','$date','$time','$user')");
					mysqli_query($link,"DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
					mysqli_query($link,"DELETE FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
					mysqli_query($link,"DELETE FROM `phlebo_sample` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
					mysqli_query($link,"DELETE FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
					mysqli_query($link,"DELETE FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
					mysqli_query($link,"DELETE FROM `testresults_card` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
					mysqli_query($link,"DELETE FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$tstid'");
				}
			}
			$qq=mysqli_query($link,"SELECT * FROM `invest_payment_refund` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `date`='$date' AND `user`='$user'");
			$num=mysqli_num_rows($qq);
			if($num>0)
			{
				$p_amt=mysqli_fetch_array($qq);
				$n_amt=$p_amt['refund_amount']+$amt;
				mysqli_query($link,"UPDATE `invest_payment_refund` SET `refund_amount`='$n_amt' WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `date`='$date' AND `user`='$user'");
			}
			else
			{
				mysqli_query($link,"INSERT INTO `invest_payment_refund`(`patient_id`, `opd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$tot_amt','$amt','$res','$date','$time','$user')");
			}
			$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
			$upd_amt=$tot_amt-$amt;
			
			if($upd_amt==0)
			{
				$paid_amt=0;
				$bal=0;
				$dis_amount=0;
			}else
			{
				if($v["balance"]<$amt)
				{
					$upd_amt=$tot_amt-$amt-$v["dis_amt"]; //bill-refund-discount
					$bal=0;
					$paid_amt=$v["advance"]-($amt-$v["balance"]);
					$dis_amount=$v["dis_amt"];
					if($paid_amt<=0)
					{
						$dis_amount=round(($upd_amt*$v["dis_per"])/100);
						$paid_amt=$upd_amt-$dis_amount;
					}
				}
				if($v["balance"]>$amt)
				{
					$upd_amt=$tot_amt-$amt-$v["dis_amt"];
					$bal=$v["balance"]-$amt;
					$paid_amt=$v["advance"];
					$dis_amount=$v["dis_amt"];
				}
				if($v["balance"]==$amt)
				{
					$bal=0;
					$paid_amt=$v["advance"];
					$dis_amount=$v["dis_amt"];
					$upd_amt=$v["tot_amount"]-$amt-$v["dis_amt"];
				}
			}
			
			//~ $paid_amt=$tot_amt-$dis_amt-$v['balance'];
			//~ $bal=$upd_amt-$dis_amt-$paid_amt;
			
			mysqli_query($link,"DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `typeofpayment`='B'");
			mysqli_query($link,"UPDATE `invest_payment_detail` SET `amount`='$paid_amt' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
			mysqli_query($link,"UPDATE `invest_patient_payment_details` SET `tot_amount`='$upd_amt',`dis_amt`='$dis_amount', `advance`='$paid_amt', `balance`='$bal' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
			echo "Done";
		}
		else
		{
			$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
			$bill_name=$pat_typ_text["bill_name"];
			
			$bill_no=101;
			$date2=date("Y-m-d");
			$date1=explode("-",$date2);	
			$c_var=$date1[0]."-".$date1[1];
			$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
			
			if($chk['tot_bill']>0)
			{
				$bill_no=$bill_no+$chk['tot_bill'];
			}
			$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
			$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
			
			$date4=date("y-m-d");
			$date3=explode("-",$date4);
			
			$random_no=rand(1,9);
			
			$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
			
			if(mysqli_query($link,"INSERT INTO `ipd_advance_payment_details`(patient_id, ipd_id, bill_no, tot_amount, discount, amount, balance, refund, pay_type, pay_mode, time, date, user) VALUES ('$uhid','$opd','$bill_id','$bill_amount','0','0','0','$refund_amount','Refund','Cash','$time','$date','$user')"))
			{
				mysqli_query($link," INSERT INTO `invest_payment_refund`(`patient_id`, `opd_id`, `ipd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$opd','$bill_amount','$refund_amount','$res','$date','$time','$user') ");
			}
		}
	}
}

if($type==9)
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$q=mysqli_query($link,"SELECT * FROM `invest_payment_refund` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$n=mysqli_num_rows($q);
	$y=$n;
	$search=0;
	if($n>0)
	{
		$ref_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_refund_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' "));
		if($ref_det)
		{
			$ref_val=2;
		}
		else
		{
			$ref_val=1;
		}
?>
	<input type="hidden" id="rad_value" value="<?php echo $ref_val; ?>" />
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Total Amount</th><th>Refund</th><th>Date-Time</th><th>User</th>
		</tr>
	<?php
		$j=1;
		while($r=mysqli_fetch_array($q))
		{
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
	?>
		<tr>
			<td><?php echo $j;?></td>
			<?php if($y>0){ echo "<td rowspan='".$y."'>&#8377; ".$r['tot_amount']."</td>";}?>
			<td><?php echo "&#8377; ".$r['refund_amount'];?></td>
			<td><?php echo convert_date($r['date'])." - ".convert_time($r['time']);?></td>
			<td><?php echo $user_name["name"]; ?></td>
		</tr>
		<?php
		$y=0;
		$j++;
		}
		?>
	</table>
<?php
	$search=1;
	}
}

?>
