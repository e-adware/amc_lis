<?php
include("../../includes/connection.php");

$type=$_POST["type"];
$date=date("Y-m-d");
$time=date("H:i:s");
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d M Y', $timestamp);
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
	if($typ=="name")
	{
		$q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$val%' ) AND `type`='1' order by `opd_id` DESC";
	}
	if($typ=="pin")
	{
		$q="SELECT * FROM `uhid_and_opdid` WHERE `opd_id` like '$val%' AND `type`='1' order by `opd_id` DESC";
	}
	if($typ=="uhid")
	{
		$q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id` like '$val%' AND `type`='1' order by `opd_id` DESC ";
	}
	if($typ=="phone")
	{
		$q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `phone` like '$val%' ) AND `type`='1' order by `opd_id` DESC ";
	}
	//echo $q;
	$qry=mysqli_query($link, $q);
	?>
	<table class="table table-condensed table-bordered">
	<th>#</th><th>UHID</th><th>PIN</th><th>Name</th><th>Phone</th>
	<?php
	$i=1;
	while($q=mysqli_fetch_array($qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
		
		$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
		
	?>
		<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $q['patient_id'];?>','<?php echo $q['opd_id'];?>','<?php echo $typ;?>','<?php echo $pat_type["type"];?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $q['patient_id']."@@".$q['opd_id']."@@".$typ."@@".$pat_type["type"];?>"/></td>
			<td><?php echo $q['opd_id'];?></td>
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
	$res=$_POST['res'];
	$res= str_replace("'", "''", "$res");
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
			<th>#</th><th>Total Amount</th><th>Refund</th><th>date-Time</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $j;?></td>
			<?php if($y>0){ echo "<td rowspan='".$y."'>&#8377; ".$r['tot_amount']."</td>";}?>
			<td><?php echo "&#8377; ".$r['refund_amount'];?></td>
			<td><?php echo convert_date($r['date'])." - ".convert_time($r['time']);?></td>
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
	$qq=mysqli_query($link,"SELECT DISTINCT `date` FROM `invest_payment_refund` WHERE `date` BETWEEN '$date1' AND '$date2' ORDER BY `date`");
	?>
	<b>Payment refund from <?php echo convert_date($date1)." to ".convert_date($date2);?></b>
	<button type="button" class="btn btn-info text-right" onclick="print_page('pay_refund','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	<table class="table table-condensed table-bordered" id="ref_tbl">
	<?php
	$tot_recv=0;
	$tot_refn=0;
	while($rr=mysqli_fetch_array($qq))
	{
		?>
		<tr>
			<th colspan="2">Date </th><th colspan="6"><?php echo convert_date($rr['date']);?></th>
		</tr>
		<tr>
			<th>#</th><th>Sl No.</th><th>PIN</th><th>Name</th><th>Paid Amount</th><th>Refund</th><th>Reason</th><th>User</th>
		</tr>
		<?php
		$q="SELECT DISTINCT `opd_id` FROM `invest_payment_refund` WHERE `date`='$rr[date]'";
		if($encounter>0)
		{
			$q.=" AND `opd_id` IN (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$encounter')";
		}
		if($user_entry>0)
		{
			$q.=" AND `user`='$user_entry'";
		}
		//echo $q."<br/>";
		$qry=mysqli_query($link,$q);
		$j=1;
		while($r=mysqli_fetch_array($qry))
		{
			$qy=mysqli_query($link,"SELECT DISTINCT `slno`,`opd_id` FROM `invest_payment_refund` WHERE `opd_id`='$r[opd_id]' AND `date`='$rr[date]'");
			$num=mysqli_num_rows($qy);
			while($r=mysqli_fetch_array($qy))
			{
				$pat_id=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id`,`ipd_serial` FROM `uhid_and_opdid` WHERE `opd_id`='$r[opd_id]'"));
				$pat_name=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$pat_id[patient_id]'"));
				$pay=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_payment_refund` WHERE `slno`='$r[slno]' AND `patient_id`='$pat_id[patient_id]' AND `opd_id`='$r[opd_id]'"));
				$usr=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$pay[user]'"));
			?>
			<tr>
				<td><?php echo $j;?></td>
				<?php
				if($num>0)
				{
					echo "<td rowspan='".$num."'>".$pat_id['ipd_serial']."</td><td rowspan='".$num."'>".$r['opd_id']."</td><td rowspan='".$num."'>".$pat_name['name']."</td><td rowspan='".$num."'>&#8377; ".$pay['tot_amount']."</td>";
					$tot_recv=$tot_recv+$pay['tot_amount'];
				}
				$tot_refn=$tot_refn+$pay['refund_amount'];
				?>
				<td><?php echo "&#8377; ".$pay['refund_amount'];?></td>
				<td><?php echo $pay['reason'];?></td>
				<td><?php echo $usr['name'];?></td>
			</tr>
			<?php
			$num=0;
			$j++;
			}
		}
	}
	?>
	<tr>
		<th colspan="4" style="text-align:right;">Total : </th><th><?php echo "&#8377; ".number_format($tot_recv,2);?></th><th colspan="3"><?php echo "&#8377; ".number_format($tot_refn,2);?></th>
	</tr>
	</table>
	<style>
	#ref_tbl tr:hover
	{
		background:none;
	}
	</style>
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
			<th>#</th><th><label class="tst_lbl" style="display:inline-block;"><input type="checkbox" id="chk_all" onchange="test_all()" /> All</label> Test Name</th><th>Rate</th>
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
	$tot_amt=$_POST['tot_amt'];
	$dis_amt=$_POST['dis_amt'];
	$res=mysqli_real_escape_string($link,$_POST['res']);
	$ref_regd_fee=$_POST['ref_regd_fee'];
	$ref_doc_fee=$_POST['ref_doc_fee'];
	$user=$_POST['user'];
	
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	
	$refund_amt=0;
	if($ref_regd_fee>0)
	{
		$refund_amt=$ref_regd_fee;
		
		$visit_fee=$v["visit_fee"];
		$regd_fee=$v["regd_fee"]-$ref_regd_fee;
	}
	if($ref_doc_fee>0)
	{
		$refund_amt=$ref_doc_fee;
		
		$visit_fee=$v["visit_fee"]-$ref_doc_fee;
		$regd_fee=$v["regd_fee"];
	}
	if($rad==1) // Free
	{
		mysqli_query($link,"INSERT INTO `invest_payment_free`(`patient_id`, `opd_id`, `ipd_id`, `tot_amount`, `free_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd','','$tot_amt','$tot_amt','$res','$date','$time','$user')");
		
		mysqli_query($link,"DELETE FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `typeofpayment`='B'");
		mysqli_query($link,"UPDATE `consult_payment_detail` SET `amount`='0' WHERE `patient_id`='$uhid' AND `opd_id`='$opd' ");
		mysqli_query($link,"UPDATE `consult_patient_payment_details` SET `dis_per`='100', `dis_amt`='$tot_amt', `dis_reason`='$res', `advance`='0', `balance`='0' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
		
	}
	if($rad==2) // Refund
	{
		mysqli_query($link,"INSERT INTO `invest_payment_refund`(`patient_id`, `opd_id`, `ipd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd','','$tot_amt','$refund_amt','$res','$date','$time','$user')");
		
		mysqli_query($link,"INSERT INTO `consult_payment_refund_details`(`patient_id`, `opd_id`, `visit_fee`, `regd_fee`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$ref_doc_fee','$ref_regd_fee','$date','$time','$user')");
		
		//$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
		$amt=$refund_amt;
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
		
		mysqli_query($link,"DELETE FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `typeofpayment`='B'");
		
		mysqli_query($link,"UPDATE `consult_payment_detail` SET `amount`='$paid_amt' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
		
		mysqli_query($link,"UPDATE `consult_patient_payment_details` SET `visit_fee`='$visit_fee', `regd_fee`='$regd_fee', `tot_amount`='$upd_amt',`dis_amt`='$dis_amount', `advance`='$paid_amt', `balance`='$bal' WHERE `patient_id`='$uhid' AND `opd_id`='$opd' ");
		
		mysqli_query($link," UPDATE `appointment_book` SET `visit_fee`='$visit_fee' WHERE `patient_id`='$uhid' AND `opd_id`='$opd' ");
		
		if($regd_fee==0)
		{
			mysqli_query($link," DELETE FROM `pat_regd_fee` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' ");
		}
		
	}
	echo "Saved";
}

if($type==66666)
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$rad=$_POST['rad'];
	$tot_amt=$_POST['tot_amt'];
	$dis_amt=$_POST['dis_amt'];
	$adv=$_POST['adv'];
	$doc_fee=$_POST['doc_fee'];
	$refund=$_POST['refund'];
	$res=$_POST['res'];
	$res=str_replace("'", "''", "$res");
	$res=mysqli_real_escape_string($link,$res);
	$user=$_POST['user'];
	if($rad==1)
	{
		if($adv>=$doc_fee)
		{
			$refund=$doc_fee;
		}
		else
		{
			$refund=$adv;
		}
	}
	
	if(mysqli_query($link,"UPDATE `consult_patient_payment_details` SET `ref_status`='$rad',`ref_amt`='$refund',`ref_reason`='$res' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"))
	{
		mysqli_query($link,"INSERT INTO `opd_payment_refund`(`patient_id`, `opd_id`, `ipd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd','','$tot_amt','$refund','$res','$date','$time','$user')");
		echo "Saved";
	}
	else
	{
		echo "Error";
	}
}

if($type==99)
{
	$uhid=$_POST['uhid'];
}
?>
