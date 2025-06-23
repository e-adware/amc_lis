<?php
session_start();
include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$date1=$_POST['date1'];
$date2=$_POST['date2'];

// important
$date11=$_POST['date1'];
$date22=$_POST['date2'];

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d M Y', $timestamp);
	return $new_date;
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

$rupees_symbol="&#x20b9; ";

if($_POST["type"]=="all_account")
{
	// Close account
	$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$date2' "));	
	if($check_close_account_today)
	{
		$con_max_slno_less=$check_close_account_today['con_slno'];
		$con_max_slno_str_less=" AND `slno`<=$con_max_slno_less ";
		
		$inv_max_slno_less=$check_close_account_today['inv_slno'];
		$inv_max_slno_str_less=" AND `slno`<=$inv_max_slno_less ";
		
		$ipd_max_slno_less=$check_close_account_today['ipd_slno'];
		$ipd_max_slno_str_less=" AND `slno`<=$ipd_max_slno_less ";
	}
	else
	{
		$con_max_slno_str_less="";
		$inv_max_slno_str_less="";
		$ipd_max_slno_str_less="";
	}
	
	$last_date=date('Y-m-d', strtotime($date1. ' - 1 days'));
	$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$last_date' "));	
	if($check_close_account_today)
	{
		$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));
		
		$con_max_slno_grtr=$check_close_account_today['con_slno'];
		$con_max_slno_str_grtr=" AND `slno`>$con_max_slno_grtr ";
		
		$inv_max_slno_grtr=$check_close_account_today['inv_slno'];
		$inv_max_slno_str_grtr=" AND `slno`>$inv_max_slno_grtr ";
		
		$ipd_max_slno_grtr=$check_close_account_today['ipd_slno'];
		$ipd_max_slno_str_grtr=" AND `slno`>$ipd_max_slno_grtr ";
	}
	else
	{
		$con_max_slno_str_grtr="";
		$inv_max_slno_str_grtr="";
		$ipd_max_slno_str_grtr="";
	}
	
	$encounter=$_POST['encounter'];
	$user_entry=$_POST['user_entry'];
	$pay_mode=$_POST['pay_mode'];
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	
	$all_pin=array();
	$card_pin="";
	$cheque_pin="";
	$i=1;
	if($encounter==0 || $encounter==1)
	{
		$con_pay_qry=mysqli_query($link, " SELECT distinct `opd_id` FROM `consult_payment_detail` WHERE `date` between '$date1' and '$date2' $con_max_slno_str_less $con_max_slno_str_grtr $user ORDER BY `slno`");
		while($con_pay=mysqli_fetch_array($con_pay_qry))
		{
			$all_pin[$i]=$con_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter==2 || $encounter==5)
	{
		$inv_pay_qry=mysqli_query($link, " SELECT distinct `opd_id` FROM `invest_payment_detail` WHERE `date` between '$date1' and '$date2' $inv_max_slno_str_less $inv_max_slno_str_grtr $user ORDER BY `slno`");
		while($inv_pay=mysqli_fetch_array($inv_pay_qry))
		{
			$all_pin[$i]=$inv_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter==3 || $encounter==4 || $encounter==8)
	{
		$ipd_casual_pay_qry=mysqli_query($link, " SELECT `ipd_id` FROM `ipd_advance_payment_details` WHERE `date` between '$date1' and '$date2' $ipd_max_slno_str_less $ipd_max_slno_str_grtr $user ORDER BY `slno`");
		while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
		{
			$all_pin[$i]=$ipd_casual_pay["ipd_id"];
			$i++;
		}
	}
	sort($all_pin);
	//print_r($all_pin);
	
?>	<p style="margin-top: 2%;">
		<b>Detail Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/detail_account_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>&pay_mode=<?php echo $pay_mode;?>"><i class="icon-file icon-large"></i> Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','<?php echo $pay_mode;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>Date</th>
			<th class="ipd_serial">Sl No.</th>
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Bill No</th>
			<th>Amount</th>
			<th>User</th>
			<th>Encounter</th>
		</tr>
	<?php
		$n=1;
		$zz=$yy=$ww=1;
		$tot_amt=$tot_amt_cash=$tot_amt_card="";
		$tot_amt_ipd="";
		$pin=0;
		foreach($all_pin as $all_pin)
		{
			if($all_pin)
			{
				$ipd_casual=0;
				$show_info=0;
				if($encounter==0)
				{
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pin' "));
				}else if($encounter>0)
				{
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pin' AND `type`='$encounter' "));
				}
				
				if($encounter==$all_pat["type"] && $encounter>0 || $encounter==0)
				{
					if($pay_mode=="Cash")
					{
						if($zz==1)
						{
							echo "<tr><th colspan='9'>Cash</th></tr>";
							$zz++;
						}
						if($pin==$all_pat['opd_id'])
						{
							$all_pat["type"]=2;
						}else
						{
							$pin=$all_pat['opd_id'];
						}
						$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
						
						$uhid_id=$pat_info["patient_id"];
						
						if($all_pat["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Cash' AND `date` between '$date1' and '$date2' $user ";
							$pat_pay_detail_qry=mysqli_query($link, $con_pay_det);
						}
						if($all_pat["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Cash' AND `date` between '$date1' and '$date2' $user ";
							$inv_pat_pay_detail_qry=mysqli_query($link, $inv_pay_det);
						}
						if($all_pat["type"]==3 || $all_pat["type"]>3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='Cash' AND `date` between '$date1' and '$date2' $user ";
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
							
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
							$pay_date=$ipd_cas_pat_pay_detail["date"];
							$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
							$amount=$ipd_cas_pat_pay_detail["amount"];
							$tot_amt+=$amount;
							$tot_amt_ipd+=$amount;
						?>
							<tr>
								<td><?php echo $n; ?></td>
								<td><?php echo convert_date($pay_date); ?></td>
								<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
								<td><?php echo $all_pat["opd_id"]; ?></td>
								<td><?php echo $pat_info["name"]; ?></td>
								<td><?php echo $bill_no; ?></td>
								<td><?php echo number_format($amount,2); ?></td>
								<td><?php echo $user_name["name"]; ?></td>
								<td><?php echo $Encounter; ?></td>
							</tr>
						<?php
							$n++;
							$zz++;
						}
						if($ipd_casual==0)
						{
							while($pat_pay_detail=mysqli_fetch_array($pat_pay_detail_qry))
							{
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
								$pay_date=$pat_pay_detail["date"];
								$bill_no=$pat_pay_detail["bill_no"];
								$amount=$pat_pay_detail["amount"];
								$tot_amt+=$amount;
								
								$all_pat_master=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `uhid_and_opdid` WHERE `opd_id`='$all_pat[opd_id]' "));
								$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat_master[type]' "));
								$Encounter=$pat_typ_text['p_type'];
								
								//$Encounter="OPD";
								if($pat_pay_detail["payment_mode"]=="Cash")
								{
									$show_info=1;
								}
								if($show_info==1)
								{
						?>
								<tr>
									<td><?php echo $n; ?></td>
									<td><?php echo convert_date($pay_date); ?></td>
									<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
									<td><?php echo $all_pat["opd_id"]; ?></td>
									<td><?php echo $pat_info["name"]; ?></td>
									<td><?php echo $bill_no; ?></td>
									<td><?php echo number_format($amount,2); ?></td>
									<td><?php echo $user_name["name"]; ?></td>
									<td><?php echo $Encounter; ?></td>
								</tr>
								<?php
									$n++;
									$zz++;
								}
							}
							while($inv_pat_pay_detail=mysqli_fetch_array($inv_pat_pay_detail_qry))
							{
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
								$pay_date=$inv_pat_pay_detail["date"];
								$bill_no=$inv_pat_pay_detail["bill_no"];
								$amount=$inv_pat_pay_detail["amount"];
								$tot_amt+=$amount;
								
								$all_pat_master=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `uhid_and_opdid` WHERE `opd_id`='$all_pat[opd_id]' "));
								$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat_master[type]' "));
								$Encounter=$pat_typ_text['p_type'];
								
								//$Encounter="Lab";
								if($inv_pat_pay_detail["payment_mode"]=="Cash")
								{
									$show_info=1;
								}
								if($show_info==1)
								{
						?>
								<tr>
									<td><?php echo $n; ?></td>
									<td><?php echo convert_date($pay_date); ?></td>
									<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
									<td><?php echo $all_pat["opd_id"]; ?></td>
									<td><?php echo $pat_info["name"]; ?></td>
									<td><?php echo $bill_no; ?></td>
									<td><?php echo number_format($amount,2); ?></td>
									<td><?php echo $user_name["name"]; ?></td>
									<td><?php echo $Encounter; ?></td>
								</tr>
								<?php
									$n++;
									$zz++;
								}
							}
						}
					}
					if($pay_mode=="Card")
					{
						if($yy==1)
						{
							echo "<tr><th colspan='9'>Card</th></tr>";
							$yy++;
						}
						if($pin==$all_pat['opd_id'])
						{
							$all_pat["type"]=2;
						}else
						{
							$pin=$all_pat['opd_id'];
						}
						$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
						$uhid_id=$pat_info["patient_id"];
						
						if($all_pat["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Card' AND `date` between '$date1' and '$date2' $user ";
							$pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $con_pay_det));
							$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
							$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
							$pay_date=$pat_pay_detail["date"];
							$bill_no=$pat_pay_detail["bill_no"];
							$amount=$pat_pay_detail["amount"];
							$tot_amt+=$amount;
							//$Encounter="OPD";
							if($pat_pay_detail_num>0)
							{
								$show_info=1;
							}
						}
						if($all_pat["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Card' AND `date` between '$date1' and '$date2' $user ";
							$inv_pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $inv_pay_det));
							$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
							$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
							$pay_date=$inv_pat_pay_detail["date"];
							$bill_no=$inv_pat_pay_detail["bill_no"];
							$amount=$inv_pat_pay_detail["amount"];
							$tot_amt+=$amount;
							//$Encounter="Lab";
							if($inv_pat_pay_detail_num)
							{
								$show_info=1;
							}
						}
						if($all_pat["type"]==3 || $all_pat["type"]>3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='Card' AND `date` between '$date1' and '$date2' $user ";
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
							$pay_date=$ipd_cas_pat_pay_detail["date"];
							$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
							$amount=$ipd_cas_pat_pay_detail["amount"];
							$tot_amt+=$amount;
							$tot_amt_ipd+=$amount;
						?>
							<tr>
								<td><?php echo $n; ?></td>
								<td><?php echo convert_date($pay_date); ?></td>
								<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
								<td><?php echo $all_pat["opd_id"]; ?></td>
								<td><?php echo $pat_info["name"]; ?></td>
								<td><?php echo $bill_no; ?></td>
								<td><?php echo number_format($amount,2); ?></td>
								<td><?php echo $user_name["name"]; ?></td>
								<td><?php echo $Encounter; ?></td>
							</tr>
						<?php
							$n++;
							$yy++;
						}
						if($ipd_casual==0)
						{
							if($show_info==1)
							{
					?>
							<tr>
								<td><?php echo $n; ?></td>
								<td><?php echo convert_date($pay_date); ?></td>
								<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
								<td><?php echo $all_pat["opd_id"]; ?></td>
								<td><?php echo $pat_info["name"]; ?></td>
								<td><?php echo $bill_no; ?></td>
								<td><?php echo number_format($amount,2); ?></td>
								<td><?php echo $user_name["name"]; ?></td>
								<td><?php echo $Encounter; ?></td>
							</tr>
							<?php
								$n++;
								$yy++;
							}
						}
					}
					if($pay_mode=="0") // All payment mode
					{
						if($ww==1)
						{
							echo "<tr><th colspan='9'>Cash</th></tr>";
							$ww++;
						}
						if($pin==$all_pat['opd_id'])
						{
							$all_pat["type"]=2;
						}else
						{
							$pin=$all_pat['opd_id'];
						}
						$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
						$uhid_id=$pat_info["patient_id"];
						
						if($all_pat["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `date` between '$date1' and '$date2' $user ";
							$pat_pay_detail_qry=mysqli_query($link, $con_pay_det);
						}
						if($all_pat["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `date` between '$date1' and '$date2' $user ";
							$inv_pat_pay_detail_qry=mysqli_query($link, $inv_pay_det);
						}
						if($all_pat["type"]==3 || $all_pat["type"]>3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `date` between '$date1' and '$date2' $user ";
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						$show_info_ipd=0;
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
							$pay_date=$ipd_cas_pat_pay_detail["date"];
							$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
							$amount=$ipd_cas_pat_pay_detail["amount"];
							
							if($ipd_cas_pat_pay_detail["pay_mode"]=="Cash")
							{
								$show_info_ipd=1;
								$tot_amt+=$amount;
								$tot_amt_cash+=$amount;
								$tot_amt_ipd+=$amount;
							}else if($ipd_cas_pat_pay_detail["pay_mode"]=="Card")
							{
								$card_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##34@@";
								$show_info_ipd=0;
							}else if($ipd_cas_pat_pay_detail["pay_mode"]=="Cheque")
							{
								$cheque_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##34@@";
								$show_info_ipd=0;
							}
							if($show_info_ipd==1)
							{
						?>
							<tr>
								<td><?php echo $n; ?></td>
								<td><?php echo convert_date($pay_date); ?></td>
								<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
								<td><?php echo $all_pat["opd_id"]; ?></td>
								<td><?php echo $pat_info["name"]; ?></td>
								<td><?php echo $bill_no; ?></td>
								<td><?php echo number_format($amount,2); ?></td>
								<td><?php echo $user_name["name"]; ?></td>
								<td><?php echo $Encounter; ?></td>
							</tr>
						<?php
							$n++;
							$ww++;
							}
						}
						if($ipd_casual==0)
						{
							while($inv_pat_pay_detail=mysqli_fetch_array($inv_pat_pay_detail_qry))
							{
								$show_info=0;
								
								//~ $qfree=mysqli_fetch_array(mysqli_query($link, "SELECT free_amount FROM `invest_payment_free` where `date` between '$date1' and '$date2' and `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]'   "));
								
								$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date1' and '$date2' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='2' "));
								
								$lab_refund_amount=$lab_refund_val["maxref"];
								
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
								
								$pay_date=$inv_pat_pay_detail["date"];
								$bill_no=$inv_pat_pay_detail["bill_no"];
								//$amount=$inv_pat_pay_detail["amount"]+$qrefund['maxref']+$qfree['free_amount'];
								$amount=$inv_pat_pay_detail["amount"]+$lab_refund_amount;
								//$tot_amt+=$amount;
								//$Encounter="Lab";
								
								$all_pat_master=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `uhid_and_opdid` WHERE `opd_id`='$all_pat[opd_id]' "));
								$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat_master[type]' "));
								$Encounter=$pat_typ_text['p_type'];
								
								if($inv_pat_pay_detail["payment_mode"]=="Cash")
								{
									$show_info=1;
									$tot_amt+=$amount;
									$tot_amt_cash+=$amount;
								}else if($inv_pat_pay_detail["payment_mode"]=="Card")
								{
									$card_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
								}else if($inv_pat_pay_detail["payment_mode"]=="Cheque")
								{
									$cheque_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
								}
								if($show_info==1)
								{
						?>
								<tr>
									<td><?php echo $n; ?></td>
									<td><?php echo convert_date($pay_date); ?></td>
									<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
									<td><?php echo $all_pat["opd_id"]; ?></td>
									<td><?php echo $pat_info["name"]; ?></td>
									<td><?php echo $bill_no; ?></td>
									<td><?php echo number_format($amount,2); ?></td>
									<td><?php echo $user_name["name"]; ?></td>
									<td><?php echo $Encounter; ?></td>
								</tr>
								<?php
									$n++;
									$ww++;
								}
							}
							while($pat_pay_detail=mysqli_fetch_array($pat_pay_detail_qry))
							{
								$show_info=0;
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
								$pay_date=$pat_pay_detail["date"];
								$bill_no=$pat_pay_detail["bill_no"];
								$amount=$pat_pay_detail["amount"];
								//$tot_amt+=$amount;
								//$Encounter="OPD";
								
								$all_pat_master=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `uhid_and_opdid` WHERE `opd_id`='$all_pat[opd_id]' "));
								$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat_master[type]' "));
								$Encounter=$pat_typ_text['p_type'];
								
								if($pat_pay_detail["payment_mode"]=="Cash")
								{
									$show_info=1;
									$tot_amt+=$amount;
									$tot_amt_cash+=$amount;
								}else if($pat_pay_detail["payment_mode"]=="Card")
								{
									$card_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
								}else if($pat_pay_detail["payment_mode"]=="Cheque")
								{
									$cheque_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
								}
								if($show_info==1)
								{
						?>
								<tr>
									<td><?php echo $n; ?></td>
									<td><?php echo convert_date($pay_date); ?></td>
									<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
									<td><?php echo $all_pat["opd_id"]; ?></td>
									<td><?php echo $pat_info["name"]; ?></td>
									<td><?php echo $bill_no; ?></td>
									<td><?php echo number_format($amount,2); ?></td>
									<td><?php echo $user_name["name"]; ?></td>
									<td><?php echo $Encounter; ?></td>
								</tr>
								<?php
									$n++;
									$ww++;
								}
							}
						}
					}
				}
			}
		}
		
		if($card_pin)
		{
			//echo $card_pin."<br>";
			echo "<tr><th colspan='9'>Card</th></tr>";
			$same_card_pin="";
			$card_pins=explode("@@",$card_pin);
			foreach($card_pins as $card_pinn)
			{
				if($card_pinn)
				{
					$ipd_casual_card==0;
					
					$card_pinn=explode("##",$card_pinn);
					$card_pin=$card_pinn[0];
					$card_bill=$card_pinn[1];
					$card_type=$card_pinn[2];
					//echo $card_pin."<br>";
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$card_pin' "));
					$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
					$uhid_id=$pat_info["patient_id"];
					
					if($card_type==1)
					{
						$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$card_bill' ";
						$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
						$pay_date=$pat_pay_detail["date"];
						$bill_no=$pat_pay_detail["bill_no"];
						$amount=$pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_card+=$amount;
						//$Encounter="OPD";
					}
					if($card_type==2)
					{
						$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$card_bill' ";
						$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
						$pay_date=$inv_pat_pay_detail["date"];
						$bill_no=$inv_pat_pay_detail["bill_no"];
						$amount=$inv_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_card+=$amount;
						//$Encounter="Lab";
					}
					if($card_type==34)
					{
						$ipd_casual_card=1;
						$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$card_pin' AND `bill_no`='$card_bill' ";
						$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
						
					}
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
					$Encounter=$pat_typ_text['p_type'];
					
					while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
					{
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
						$pay_date=$ipd_cas_pat_pay_detail["date"];
						$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
						$amount=$ipd_cas_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_card+=$amount;
						$tot_amt_ipd+=$amount;
						
					?>
						<tr>
							<td><?php echo $n; ?></td>
							<td><?php echo convert_date($pay_date); ?></td>
							<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
							<td><?php echo $all_pat["opd_id"]; ?></td>
							<td><?php echo $pat_info["name"]; ?></td>
							<td><?php echo $bill_no; ?></td>
							<td><?php echo number_format($amount,2); ?></td>
							<td><?php echo $user_name["name"]; ?></td>
							<td><?php echo $Encounter; ?></td>
						</tr>
					<?php
						$n++;
					}
					if($ipd_casual_card==0)
					{
				?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo convert_date($pay_date); ?></td>
						<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
						<td><?php echo $all_pat["opd_id"]; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $bill_no; ?></td>
						<td><?php echo number_format($amount,2); ?></td>
						<td><?php echo $user_name["name"]; ?></td>
						<td><?php echo $Encounter; ?></td>
					</tr>
					<?php
						$n++;
					}
				}
			}
		}
		if($user_entry==0)
		{
			if($encounter==0 || $encounter==3)
			{
				$ipd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund),0) as maxref from ipd_advance_payment_details where `date` between '$date11' and '$date22' "));
			}
			$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date11' and '$date22'"));	
			
			$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='2' "));
			
		}
		else
		{
			if($encounter==0 || $encounter==3)
			{
				$ipd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund),0) as maxref from ipd_advance_payment_details where `date` between '$date11' and '$date22' and user='$user_entry' "));
			}
			$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date11' and '$date22' and `user`='$user_entry' "));
			
			$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='2' and a.`user`='$user_entry' "));
			
		}
		$tot_expense=$tot_expense_qry["tot_exp"];
		
		$lab_refund=$lab_refund_val["maxref"];
		
		$ipd_refund=$ipd_refund_val['maxref'];
		
		if($pay_mode=="0")
		{
			if($card_pin)
			{
		?>
		<tr>
			<th colspan="6"><span class="text-right">Total Card Amount</span></th>
			<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_card,2); ?></td>
		</tr>
		<?php } } ?>
		<?php
		if($pay_mode=="0")
		{
		?>
			<tr>
				<th colspan="5"><span class="text-right">Total Cash Amount</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_cash,2); ?></td>
			</tr>
		<?php
		}
		?>
			<tr>
				<th colspan="5"><span class="text-right">Total <?php if($pay_mode!='0'){ echo $pay_mode; } ?> Amount</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt,2); ?></td>
			</tr>
		<?php //if($encounter==0 && $pay_mode=="0"){ ?>
		<?php if($pay_mode=="0"){ ?>
			<tr>
				<th colspan="5"><span class="text-right">Total Expense</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($tot_expense,2); ?></td>
			</tr>
			<tr>
				<th colspan="5"><span class="text-right">Total Refund</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($ipd_refund+$lab_refund,2); ?></td>
			</tr>
			<!--<tr>
				<th colspan="6"><span class="text-right">Total Free Amount</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($qfree['maxfree'],2); ?></td>
			</tr>-->
			<tr>
				<th colspan="5"><span class="text-right">Net Amount</span></th>
				
				<td colspan="3"><?php echo $rupees_symbol.number_format(($tot_amt-$tot_expense-$ipd_refund-$lab_refund),2); ?></td>
			</tr>
<?php 	
		}
		$user_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
		if($user_level["levelid"]==1)
		{
		
			$date=date("Y-m-d");
			$check_close_account=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$date22' "));
			if($check_close_account)
			{
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$check_close_account[user]' "));
				
				echo "<tr><td colspan='9'><button class='btn btn-danger'>Account Closed By $user_name[name] </button></td></tr>";
			}else
			{
				if($date==$date22)
				{
					$close_btn="Close Today's Account";
				}else
				{
					$close_btn="Close Account of ".convert_date($date22);
				}
			?>
			<tr style="display:none;">
				<td colspan="9">
					<input type="button" value="<?php echo $close_btn; ?>" class="btn btn-danger" onClick="close_account('<?php echo $date22; ?>')" >
				</td>
			</tr>
<?php 		}
		}
?>
	</table>
<?php
	
}

if($_POST["type"]=="all_patient")
{
	$user_entry=$_POST['user_entry'];
	$encounter=$_POST['encounter'];
	$user="";
	
?>
	<p style="margin-top: 2%;"><b>Patient Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('all_patient','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		
		<input type="text" placeholder="To" id="sl_to" style="width:50px" class="btn btn-info text-right"/>
		<input type="text" placeholder="From" id="sl_frm" style="width:50px" class="btn btn-info text-right"/> 
	</p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th width="5%">#</th>
			<th class="ipd_serial" width="5%">Sl No.</th>
			<th width="10%">PIN</th>
			<th width="10%">Name</th>
			<th width="10%">Doctor</th>
			<th width="10%">date</th>
			<th width="50%">Test Details</th>
		</tr>
		<?php
				$i=1;
				$tot_cash=$cashamt=0;
				$disamt=$discount=0;
				$patientdel=mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `date` between '$date1' and '$date2'  order by `date` ");
				
				while($d=mysqli_fetch_array($patientdel))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
					
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$d[type]' "));
					$encounter=$pat_typ_text['p_type'];
					
					$bill_amt_each=0;
					$vtst="";
					$qtestnum=mysqli_num_rows(mysqli_query($link,"select a.testid,b.testname from patient_test_details a,testmaster b where a.patient_id='$d[patient_id]' and a.opd_id='$d[opd_id]' and a.testid=b.testid"));
						
						if($qtestnum>0)
						{
					$qtest=mysqli_query($link,"select a.testid,b.testname from patient_test_details a,testmaster b where a.patient_id='$d[patient_id]' and a.opd_id='$d[opd_id]' and a.testid=b.testid");
					while($qtest1=mysqli_fetch_array($qtest))
					{
						$vtst=$vtst.' ,'.$qtest1['testname'];
					}
					$qdoc=mysqli_fetch_array(mysqli_query($link,"select a.refbydoctorid,a.ipd_serial,b.ref_name from uhid_and_opdid a,refbydoctor_master b where a.patient_id='$d[patient_id]' and a.opd_id='$d[opd_id]' and a.refbydoctorid=b.refbydoctorid"));
					
					
					$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
					$cab=mysqli_fetch_array(mysqli_query($link,"select cabin_no from patient_cabin where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
		?>
				<tr>
					<td><?php echo $i;?></td>
					<td class="ipd_serial"><?php echo $qdoc['ipd_serial'];?></td>
					<td><?php echo $d['opd_id'];?></td>
					<td><?php echo $pat_info['name'];?><?php if($cab[cabin_no]){ echo "- ".$cab[cabin_no]; }?></td>
					<td><?php echo $qdoc['ref_name'];?></td>
					<td><?php echo convert_date($d['date']);?></td>
					<td><?php echo $vtst;?></td>
				</tr>
		<?php
					
					$i++;
				}
			}
			?>
		
	</table>
<?php
}


if($_POST["type"]=="view_summry")
{
	$encounter="";
	
?>
	<p style="margin-top: 2%;"><b>Account Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('view_summry','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th >#</th>
			<th >User</th>
			<th >Amount</th>
		</tr>
		<?php
				$i=1;
				
				$qexpense=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxexp from expense_detail where `date` between '$date1' and '$date2' "));
				$qrefund=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund_amount),0) as maxref from invest_payment_refund where `date` between '$date1' and '$date2' "));
				$qfree=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(free_amount),0) as maxfree from invest_payment_free where `date` between '$date1' and '$date2' "));
				$patientdel=mysqli_query($link, "SELECT distinct a.user,b.name FROM `invest_payment_detail` a,employee b WHERE a.`date` between '$date1' and '$date2' and a.amount>0 and a.user=b.emp_id  order by b.`name` ");
				
				while($d=mysqli_fetch_array($patientdel))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
					$quserrefund=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxuserref from invest_payment_refund a,uhid_and_opdid b where a.`date` between '$date1' and '$date2' and a.user='$d[user]' and a.patient_id=b.patient_id and a.opd_id=b.opd_id and a.date=b.date "));
					$quserrfree=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as maxuserfree from invest_payment_free a,uhid_and_opdid b where a.`date` between '$date1' and '$date2' and a.user='$d[user]' and a.patient_id=b.patient_id and a.opd_id=b.opd_id and a.date=b.date "));
					
					$qpay=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxpay from invest_payment_detail where `date` between '$date1' and '$date2' and user='$d[user]'"));
					$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
					$vttlclction=$vttlclction+$qpay['maxpay']+$quserrefund['maxuserref']+$quserrfree['maxuserfree'];
		?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $d['name'];?></td>
					<td><?php echo $qpay['maxpay']+$quserrefund['maxuserref']+$quserrfree['maxuserfree'];?></td>
				</tr>
		<?php
					
					$i++;
				}
				$vnetamt=$vttlclction-$qexpense['maxexp']-$qrefund['maxref']-$qfree['maxfree'];
				//$vnetamt=$vttlclction-$qexpense['maxexp'];
			?>
			<tr>
				<td>&nbsp;</td>
				<td style="font-weight:bold;text-align:right" >Total Collection :</td>
				<td style="font-weight:bold"><?php echo number_format($vttlclction,2);?></td>
			</tr>
		    <tr>
				<td>&nbsp;</td>
				<td style="font-weight:bold;text-align:right" >Total Expense :</td>
				<td style="font-weight:bold"><?php echo number_format($qexpense['maxexp'],2);?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="font-weight:bold;text-align:right" >Total Refund :</td>
				<td style="font-weight:bold"><?php echo number_format($qrefund['maxref'],2);?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="font-weight:bold;text-align:right" >Total Free Amount :</td>
				<td style="font-weight:bold"><?php echo number_format($qfree['maxfree'],2);?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="font-weight:bold;text-align:right" >Net Amount :</td>
				<td style="font-weight:bold"><?php echo number_format($vnetamt,2);?></td>
			</tr>
	</table>
<?php
}


if($_POST["type"]=="cancel_pat")
{
	$user_entry=$_POST['user_entry'];
	$encounter=$_POST['encounter'];
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	$type="";
	if($encounter>0)
	{
		$type=" and `type`='$encounter'";
	}
?>
	<p style="margin-top: 2%;"><b>Patient Cancel Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/pat_cancel_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>"><i class="icon-file icon-large"></i> Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('cancel_pat','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>PIN</th>
			<!--<th>Bill No</th>-->
			<th>Name</th>
			<th>Cancel date</th>
			<th><span class="text-right">Bill Amount</span></th>
			<th><span class="text-right">Reason</span></th>
			<th><span class="text-right">User</span></th>
			<th><span class="text-right">Encounter</span></th>
		</tr>
		<?php
				$i=1;
				$tot_cash=$cashamt=0;
				$disamt=$discount=0;
				$patientdel=mysqli_query($link, "SELECT * FROM `patient_cancel_reason` WHERE `date` between '$date1' and '$date2' $user $type order by `date` ");
				
				while($d=mysqli_fetch_array($patientdel))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
					
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$d[type]' "));
					$encounter=$pat_typ_text['p_type'];
					
					$bill_amt_each=0;
					
					if($d["type"]==1) // OPD
					{
						$pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details_cancel where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
						$bill_amt_each=$pay['tot_amount'];
						$cashamt=$cashamt+$pay['tot_amount'];
						$disamt=$disamt+$pay['dis_amt'];
						$discount=$pay['dis_amt'];
						//$encounter="OPD";
					}
					if($d["type"]==2) // LAB
					{
						$pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details_cancel where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
						$bill_amt_each=$pay['tot_amount'];
						$cashamt=$cashamt+$pay['tot_amount'];
						$disamt=$disamt+$pay['dis_amt'];
						$discount=$pay['dis_amt'];
						//$encounter="Lab";
					}
					if($d["type"]==3) // IPD
					{
						$pay=mysqli_fetch_array(mysqli_query($link, "select * from ipd_advance_payment_details_cancel where patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
						$bill_amt_each=$pay['tot_amount'];
						$amt=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`amount`) as sum FROM `ipd_pat_service_details_cancel` WHERE patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
						//$cashamt=$cashamt+$pay['tot_amount'];
						$bill_amt_each=$amt['sum'];
						$cashamt=$cashamt+$amt['sum'];
						$disamt=$disamt+$pay['discount'];
						$discount=$pay['discount'];
						//$encounter="IPD";
						//$bill_no=$pay["bill_no"];
					}
					if($d["type"]>3) // Casualty
					{
						$pay=mysqli_fetch_array(mysqli_query($link, "select * from ipd_advance_payment_details_cancel where patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
						$amt=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`amount`) as sum FROM `ipd_pat_service_details_cancel` WHERE patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
						//$cashamt=$cashamt+$pay['tot_amount'];
						$bill_amt_each=$amt['sum'];
						$cashamt=$cashamt+$amt['sum'];
						$disamt=$disamt+$pay['discount'];
						$discount=$pay['discount'];
						//$encounter="Casualty";
						//$bill_no=$pay["bill_no"];
					}
					
					
					$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
					$sl=mysqli_fetch_array(mysqli_query($link, " SELECT `ipd_serial` FROM `uhid_and_opdid` WHERE `opd_id`='$d[opd_id]' "));
		?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $d['opd_id'];?></td>
					<!--<td><?php echo $bill_no;?></td>-->
					<td><?php echo $pat_info['name'];?></td>
					<td><?php echo convert_date($d['date']);?></td>
					<!--<td><span class="text-right"><?php echo $rupees_symbol.number_format($pay['tot_amount']-$discount,2);?></span></td>-->
					<td><span class="text-right"><?php echo $rupees_symbol.number_format($bill_amt_each,2);?></span></td>
					<td><span class="text-right"><?php echo $d['reason'];?></span></td>
					<td><span class="text-right"><?php echo $quser['name'];?></span></td>
					<td><span class="text-right"><?php echo $encounter;?></span></td>
				</tr>
		<?php
					$tot_cash=$tot_cash+$bill_amt_each;
					$i++;
				}
			?>
		<tr>
		  <td colspan="4"><span class="text-right"><strong>Total Cancel Amount :</strong></span></td>
		  <td><span class="text-right"><strong><?php echo $rupees_symbol.number_format($tot_cash,2);?> </strong></span></td>
		  <td colspan="4">&nbsp;</td>
		</tr>
	</table>
<?php
}

//////////////////////////////

if($_POST["type"]=="freepatient")  /////////// for free patient
{
	$user_entry=$_POST['user_entry'];
	$encounter=$_POST['encounter'];
	$user="";
	if($user_entry>0)
	{
		$user=" and a.`user`='$user_entry'";
	}
	$type="";
	if($encounter>0)
	{
		$type=" and `type`='$encounter'";
	}
?>
	<p style="margin-top: 2%;"><b>Patient Free Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/lab_pat_free_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>"><i class="icon-file icon-large"></i> Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('free_pat','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th class="ipd_serial">Sl No.</th>
			<th>PIN</th>
			<!--<th>Bill No</th>-->
			<th>Name</th>
			<th>date</th>
			<th><span class="text-right">Free Amount</span></th>
			<th><span class="text-right">Reason</span></th>
			<th><span class="text-right">User</span></th>
			<th><span class="text-right">Encounter</span></th>
		</tr>
		<?php
				$i=1;
				$tot_cash=$cashamt=0;
				$disamt=$discount=0;
				
				$patientdel=mysqli_query($link, "SELECT a.*,b.ipd_serial,b.type FROM `invest_payment_free` a,uhid_and_opdid b WHERE a.`date` between '$date1' and '$date2' and a.patient_id=b.patient_id and a.opd_id=b.opd_id $user $type order by a.`date` ");
				
				while($d=mysqli_fetch_array($patientdel))
				{
					//$ptype=mysqli_fetch_array(mysqli_query($link,"select type from uhid_and_opdid where patient_id='$d[patient_id]'' and opd_id='$d[opd_id]'"));
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
					
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$d[type]' "));
					$encounter=$pat_typ_text['p_type'];
					
					$bill_amt_each=0;
					
					if($d["type"]==1) // OPD
					{
						$pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_free where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
						$bill_amt_each=$pay['tot_amount'];
						$cashamt=$cashamt+$pay['tot_amount'];
						$disamt=$disamt+$pay['dis_amt'];
						$discount=$pay['dis_amt'];
						//$encounter="OPD";
					}
					if($d["type"]==2) // LAB
					{
						
						
						$pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_free where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
						$bill_amt_each=$pay['tot_amount'];
						$cashamt=$cashamt+$pay['tot_amount'];
						$disamt=$disamt+$pay['dis_amt'];
						$discount=$pay['dis_amt'];
						//$encounter="Lab";
					}
					
					if($d["type"]==3) // IPD
					{
						$pay=mysqli_fetch_array(mysqli_query($link, "select * from ipd_advance_payment_details_cancel where patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
						$bill_amt_each=$pay['tot_amount'];
						$amt=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`amount`) as sum FROM `ipd_pat_service_details_cancel` WHERE patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
						//$cashamt=$cashamt+$pay['tot_amount'];
						$bill_amt_each=$amt['sum'];
						$cashamt=$cashamt+$amt['sum'];
						$disamt=$disamt+$pay['discount'];
						$discount=$pay['discount'];
						//$encounter="IPD";
						//$bill_no=$pay["bill_no"];
					}
					if($d["type"]>3) // Casualty
					{
						$pay=mysqli_fetch_array(mysqli_query($link, "select * from ipd_advance_payment_details_cancel where patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
						$amt=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`amount`) as sum FROM `ipd_pat_service_details_cancel` WHERE patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
						//$cashamt=$cashamt+$pay['tot_amount'];
						$bill_amt_each=$amt['sum'];
						$cashamt=$cashamt+$amt['sum'];
						$disamt=$disamt+$pay['discount'];
						$discount=$pay['discount'];
						//$encounter="Casualty";
						//$bill_no=$pay["bill_no"];
					}
					
					
					$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
		?>
				<tr>
					<td><?php echo $i;?></td>
					<td class="ipd_serial"><?php echo $d['ipd_serial'];?></td>
					<td><?php echo $d['opd_id'];?></td>
					<!--<td><?php echo $bill_no;?></td>-->
					<td><?php echo $pat_info['name'];?></td>
					<td><?php echo convert_date($d['date']);?></td>
					<!--<td><span class="text-right"><?php echo $rupees_symbol.number_format($pay['tot_amount']-$discount,2);?></span></td>-->
					<td><span class="text-right"><?php echo $rupees_symbol.number_format($bill_amt_each,2);?></span></td>
					<td><span class="text-right"><?php echo $d['reason'];?></span></td>
					<td><span class="text-right"><?php echo $quser['name'];?></span></td>
					<td><span class="text-right"><?php echo $encounter;?></span></td>
				</tr>
		<?php
					$tot_cash=$tot_cash+$bill_amt_each;
					$i++;
				}
			?>
		<tr>
		  <td colspan="5"><span class="text-right"><strong>Total Free Amount :</strong></span></td>
		  <td><span class="text-right"><strong><?php echo $rupees_symbol.number_format($tot_cash,2);?> </strong></span></td>
		  <td colspan="4">&nbsp;</td>
		</tr>
	</table>
<?php
}

//////////////////////////////////////////////////////

if($_POST["type"]=="payment_cancel")
{
	$user_entry=$_POST['user_entry'];
	$encounter=$_POST['encounter'];
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	$type="";
	if($encounter>0)
	{
		$type=" and `type`='$encounter'";
	}
?>
	<p style="margin-top: 2%;"><b>Payment Cancel Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/pay_cancel_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>"><i class="icon-file icon-large"></i> Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('payment_cancel','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>PIN</th>
			<th>Bill No</th>
			<th>Name</th>
			<th>Cancel date</th>
			<th><span class="text-right">Bill Amount</span></th>
			<th><span class="text-right">Discount</span></th>
			<th><span class="text-right">Reason</span></th>
			<th><span class="text-right">User</span></th>
			<th><span class="text-right">Encounter</span></th>
		</tr>
		<?php
				$i=1;
				$cashamt=0;
				$disamt=0;
				$patientdel=mysqli_query($link, "SELECT * FROM `cancel_payment` WHERE `date` between '$date1' and '$date2' $user $type order by `date`  ");
				
				while($d=mysqli_fetch_array($patientdel))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
					
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$d[type]' "));
					$encounter=$pat_typ_text['p_type'];
					
					if($d["type"]==1) // OPD
					{
						$cashamt=$cashamt+$d['amount'];
						$disamt=$disamt+$d['discount'];
						//$encounter="OPD";
					}
					if($d["type"]==2) // LAB
					{
						$cashamt=$cashamt+$d['amount'];
						$disamt=$disamt+$d['discount'];
						//$encounter="Lab";
					}
					if($d["type"]==3) // IPD
					{
						$cashamt=$cashamt+$d['amount'];
						$disamt=$disamt+$d['discount'];
						//$encounter="IPD";
					}
					if($d["type"]>3) // Casualty
					{
						$cashamt=$cashamt+$d['amount'];
						$disamt=$disamt+$d['discount'];
						//$encounter="Casualty";
					}
					
					
					$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
					$sl=mysqli_fetch_array(mysqli_query($link, " SELECT `ipd_serial` FROM `uhid_and_opdid` WHERE `opd_id`='$d[opd_id]' "));
		?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $d['ipd_id'];?></td>
					<td><?php echo $d['bill_no'];?></td>
					<td><?php echo $pat_info['name'];?></td>
					<td><?php echo convert_date($d['date']);?></td>
					<td><span class="text-right"><?php echo $rupees_symbol.number_format($d['amount'],2);?></span></td>
					<td><span class="text-right"><?php echo $rupees_symbol.number_format($d['discount'],2);?></span></td>
					<td><span class="text-right"><?php echo $d['reason'];?></span></td>
					<td><span class="text-right"><?php echo $quser['name'];?></span></td>
					<td><span class="text-right"><?php echo $encounter;?></span></td>
				</tr>
		<?php
					$i++;
				}
			?>
		<tr>
		  <td colspan="5"><span class="text-right"><strong>Total :</strong></span></td>
		  <td><span class="text-right"><strong><?php echo $rupees_symbol.number_format($cashamt,2);?> </strong></span></td>
		  <td><span class="text-right"><strong><?php echo $rupees_symbol.number_format($disamt,2);?> </strong></span></td>
		  <td colspan="3">&nbsp;</td>
		</tr>
		<tr>
		  <td colspan="5"><span class="text-right"><strong>Total Cancel Amount :</strong></span></td>
		  <td><span class="text-right"><strong><?php echo $rupees_symbol.number_format(($cashamt+$disamt),2);?> </strong></span></td>
		  <td colspan="4">&nbsp;</td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="expense")
{
	$user_entry=$_POST['user_entry'];
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	if($date1=="" && $date2=="")
	{
		$q=mysqli_query($link,"SELECT * FROM `expense_detail`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `expense_detail` WHERE `date` BETWEEN '$date1' AND '$date2' $user");
	}
	
?>
	<p style="margin-top: 2%;"><b>Daily Expense Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/daily_expense_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&user_entry=<?php echo $user_entry;?>">Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('expense','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<!--<th>Details</th>-->
			<th>Description</th>
			<th>Amount</th>
			<th>Date</th>
			<th>User</th>
		</tr>
		<?php
		$i=1;
		$tot=0;
		while($r=mysqli_fetch_array($q))
		{
			$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
			$c=mysqli_fetch_array(mysqli_query($link, " SELECT `cat_name` FROM `category_master` WHERE `cat_id`='$r[details]' "));
			$tot=$tot+$r["amount"];
		?>
		<tr>
			<td><?php echo $i;?></td>
			<!--<td><?php echo $c["cat_name"]; ?></td>-->
			<td><?php echo $r["description"]; ?></td>
			<td><?php echo $rupees_symbol.number_format($r["amount"],2); ?></td>
			<td><?php echo convert_date($r["date"]); ?></td>
			<td><?php echo $emp["name"]; ?></td>
		</tr>
		<?php
		$i++;
		}
		?>
		<tr>
			<td colspan="2"><span class="text-right"><b>Total</b></span></td>
			<td colspan="3"><span class="text-left"><b><?php echo $rupees_symbol.number_format($tot,2); ?></b></span></td>
		</tr>
	</table>
<?php	
}

if($_POST["type"]=="account_summary")
{
	$encounter=$_POST['encounter'];
	$user_entry=$_POST['user_entry'];
	
	$q="SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' ";
	if($encounter!='0')
	{
		$q.=" AND `type`='$encounter'";
	}
	if($user_entry!='0')
	{
		$q.=" AND `user`='$user_entry'";
	}
	$q.=" ORDER BY `slno`";
	
	//echo $q;
	
	$uhid_opdid_qry=mysqli_query($link, $q);
?>
	<p style="margin-top: 2%;"><b>Account Summary Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/account_summary_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>"><i class="icon-file icon-large"></i> Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('summary','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Bill Amout</th>
			<th>Discount</th>
			<th>Paid</th>
			<th>Balance</th>
			<th>User</th>
			<th>Encounter</th>
			<th>Date</th>
		</tr>
<?php
	$n=1;
	$tot_bill=$tot_dis=$tot_paid=$tot_bal=0;
	while($d=mysqli_fetch_array($uhid_opdid_qry))
	{
		$pat_show=0;
		$bill_amt=$discount=$paid=$balance=0;
		
		$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
		
		$uhid_id=$pat_info["patient_id"];
		
		$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
		
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$d[type]' "));
		$encounter=$pat_typ_text['p_type'];
		
		if($d["type"]==1) // OPD
		{
			$pay_qry=mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'");
			$pay_num=mysqli_num_rows($pay_qry);
			if($pay_num>0)
			{
				$pay=mysqli_fetch_array($pay_qry);
				$bill_amt=$pay['tot_amount'];
				$discount=$pay['dis_amt'];
				$paid=$pay['advance'];
				$balance=$pay['balance'];
				//$encounter="OPD";
			}else
			{
				$pat_show=1;
			}
		}
		if($d["type"]==2) // LAB
		{
			$pay_qry=mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'");
			$pay_num=mysqli_num_rows($pay_qry);
			if($pay_num>0)
			{
				$pay=mysqli_fetch_array($pay_qry);
				
				$bill_amt=$pay['tot_amount'];
				$discount=$pay['dis_amt'];
				$paid=$pay['advance'];
				$balance=$pay['balance'];
				//$encounter="Lab";
			}else
			{
				$pat_show=1;
			}
		}
		if($d["type"]==3) // IPD
		{
			$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$d[patient_id]' AND `ipd_id`='$d[opd_id]'"));
			$bill_amt=$tot_serv['sum_tot_amt'];
			
			$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$d[patient_id]' AND `ipd_id`='$d[opd_id]'"));
			
			$discount=$tot_amountt['sum_dis'];
			$paid=$tot_amountt['sum_paid'];
			$balance=($bill_amt-$discount-$paid);
			//$balance=$tot_amountt['sum_bal'];
			//$encounter="IPD";
			if($bill_amt==0)
			{
				$pat_show=0;
				$balance=0;
			}
		}
		if($d["type"]>3) // Casualty
		{
			$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$d[patient_id]' AND `ipd_id`='$d[opd_id]'"));
			$bill_amt=$tot_serv['sum_tot_amt'];
			
			$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$d[patient_id]' AND `ipd_id`='$d[opd_id]'"));
			
			$discount=$tot_amountt['sum_dis'];
			$paid=$tot_amountt['sum_paid'];
			$balance=($bill_amt-$discount-$paid);
			//$balance=$tot_amountt['sum_bal'];
			//$encounter="Casualty";
			if($bill_amt==0)
			{
				$pat_show=0;
				$balance=0;
			}
		}
		
		if($pat_show=='0')
		{
			$tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
	?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $d["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($d["date"]); ?></td>
			</tr>
		<?php
			$n++;
		}
	}
?>
		<tr>
		  <td colspan="3"><span class="text-right"><strong>Total:</strong></span></td>
		  <td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bill),2);?> </strong></span></td>
		  <td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_dis),2);?> </strong></span></td>
		  <td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid),2);?> </strong></span></td>
		  <td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bal),2);?> </strong></span></td>
		  <td colspan="4">&nbsp;</td>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="discount_report")
{
	$encounter=$_POST['encounter'];
	$user_entry=$_POST['user_entry'];
	
	$encounter_str="";
	if($encounter!='0')
	{
		$encounter_str=" AND b.`type`='$encounter'";
	}
	$user_str="";
	if($user_entry!='0')
	{
		$user_str=" AND a.`user`='$user_entry'";
	}
	
	if($encounter==0 || $encounter==1)
	{
		$con_pay_qry=mysqli_query($link, " SELECT a.`opd_id` FROM `consult_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`date` between '$date1' and '$date2' and a.`dis_amt`>0 $encounter_str $user_str ORDER BY a.`slno` ");
		while($con_pay=mysqli_fetch_array($con_pay_qry))
		{
			$all_pin[$i]=$con_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter==2)
	{
		$inv_pay_qry=mysqli_query($link, " SELECT a.`opd_id` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`date` between '$date1' and '$date2' and a.`dis_amt`>0 $encounter_str $user_str ORDER BY a.`slno` ");
		while($inv_pay=mysqli_fetch_array($inv_pay_qry))
		{
			$all_pin[$i]=$inv_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter>3)
	{
		$ipd_casual_pay_qry=mysqli_query($link, " SELECT `ipd_id` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`ipd_id`=b.`opd_id` and a.`date` between '$date1' and '$date2' and a.`discount`>0 $encounter_str $user_str ORDER BY a.`slno`");
		while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
		{
			$all_pin[$i]=$ipd_casual_pay["ipd_id"];
			$i++;
		}
	}
	sort($all_pin);
	//print_r($all_pin);
	
?>
	<p style="margin-top: 2%;"><b>Discount Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/discount_report_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>">Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('discount','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Bill Amout</th>
			<th>Discount</th>
			<th>Reason</th>
			<th>User</th>
			<th>Encounter</th>
			<th>Date</th>
		</tr>
<?php
	$n=1;
	$tot_bill=$tot_dis=$tot_paid=$tot_bal=0;
	foreach($all_pin as $all_pin)
	{
		if($all_pin)
		{
			if($encounter==0)
			{
				$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pin' "));
			}else if($encounter>0)
			{
				$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pin' AND `type`='$encounter' "));
			}
			
			if($encounter==$all_pat["type"] && $encounter>0 || $encounter==0)
			{
				$show_pat=0;
				$reason="";
				$bill_amt=$discount=$paid=$balance=0;
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$all_pat[patient_id]'"));
				
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
				$encounter=$pat_typ_text['p_type'];
				
				if($all_pat["type"]==1) // OPD
				{
					$pay_qry=mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$all_pat[patient_id]' and opd_id='$all_pat[opd_id]' and `dis_amt`>0");
					$pay_num=mysqli_num_rows($pay_qry);
					if($pay_num>0)
					{
						$show_pat=1;
						$pay=mysqli_fetch_array($pay_qry);
						$bill_amt=$pay['tot_amount'];
						$discount=$pay['dis_amt'];
						$paid=$pay['advance'];
						$balance=$pay['balance'];
						$ddate=$pay['date'];
						//$encounter="OPD";
						$reason=$pay['dis_reason'];
						$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay[user]' "));
					}
				}
				if($all_pat["type"]==2) // LAB
				{
					$pay_qry=mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$all_pat[patient_id]' and opd_id='$all_pat[opd_id]' and `dis_amt`>0");
					$pay_num=mysqli_num_rows($pay_qry);
					if($pay_num>0)
					{
						$show_pat=1;
						$pay=mysqli_fetch_array($pay_qry);
						$bill_amt=$pay['tot_amount'];
						$discount=$pay['dis_amt'];
						$paid=$pay['advance'];
						$balance=$pay['balance'];
						$ddate=$pay['date'];
						//$encounter="Lab";
						$reason=$pay['dis_reason'];
						$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay[user]' "));
					}
				}
				if($all_pat["type"]==3) // IPD
				{
					$ipd_adv_dis_qry=mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' AND `discount`>0 ");
					$ipd_adv_dis_num=mysqli_num_rows($ipd_adv_dis_qry);
					if($ipd_adv_dis_num>0)
					{
						$show_pat=1;
						$ipd_adv_dis=mysqli_fetch_array($ipd_adv_dis_qry);
						$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`amount`) sum_paid FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' "));
						$bill_amt=$ipd_adv_dis['tot_amount'];
						$discount=$ipd_adv_dis['discount'];
						$paid=$tot_amountt['sum_paid'];
						$balance=($bill_amt-$discount-$paid);
						
						$dis_reason=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discount_reason` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' "));
						$reason=$dis_reason['reason'];
						
						$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_adv_dis[user]' "));
						$ddate=$ipd_adv_dis['date'];
					}
				}
				if($all_pat["type"]>3) // Casualty
				{
					$ipd_adv_dis_qry=mysqli_query($link, " SELECT `date`,`user` FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' AND `discount`>0 ");
					$ipd_adv_dis_num=mysqli_num_rows($ipd_adv_dis_qry);
					if($ipd_adv_dis_num>0)
					{
						$show_pat=1;
						$ipd_adv_dis=mysqli_fetch_array($ipd_adv_dis_qry);
						$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' "));
						$bill_amt=$tot_amountt['sum_tot_amt'];
						$discount=$tot_amountt['sum_dis'];
						$paid=$tot_amountt['sum_paid'];
						$balance=($bill_amt-$discount-$paid);
						
						$dis_reason=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discount_reason` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' "));
						$reason=$dis_reason['reason'];
						
						$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_adv_dis[user]' "));
						$ddate=$ipd_adv_dis['date'];
					}
				}
				$tot_bill=$tot_bill+$bill_amt;
				$tot_dis=$tot_dis+$discount;
				$tot_paid=$tot_paid+$paid;
				$tot_bal=$tot_bal+$balance;
				if($show_pat==1)
				{
			?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo $pat_info["patient_id"]; ?></td>
						<td><?php echo $all_pat["opd_id"]; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
						<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
						<td><?php echo $reason; ?></td>
						<td><?php echo $quser["name"]; ?></td>
						<td><?php echo $encounter; ?></td>
						<td><?php echo convert_date($ddate); ?></td>
					</tr>
				<?php
					$n++;
				}
			}
		}
	}
?>
		<tr>
		  <td colspan="4"><span class="text-right"><strong>Total:</strong></span></td>
		  <td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bill),2);?> </strong></span></td>
		  <td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_dis),2);?> </strong></span></td>
		  <td colspan="4">&nbsp;</td>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="cash_deposit")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$user=$_POST['user_entry'];
	
	if($user>0)
	{
		// OPD
		$consult_payment=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `consult_tot` FROM `consult_payment_detail` WHERE `payment_mode`='Cash' AND `user`='$user' AND `date` BETWEEN '$date1' AND '$date2' "));
		$consult_payment_amt=$consult_payment["consult_tot"];
		
		// LAB
		$invest_payment=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `invest_tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `user`='$user' AND `date` BETWEEN '$date1' AND '$date2' "));
		$invest_payment_amt=$invest_payment["invest_tot"];
		
		// Casualty
		$casual_payment=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `casual_tot` FROM `ipd_advance_payment_details` WHERE `bill_no` like '%CA' AND `pay_mode`='Cash' AND `user`='$user' AND `date` BETWEEN '$date1' AND '$date2' "));
		$casual_payment_amt=$casual_payment["casual_tot"];
		
		// IPD
		$ipd_payment=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `ipd_tot` FROM `ipd_advance_payment_details` WHERE `bill_no` like '%IP' AND `pay_mode`='Cash' AND `user`='$user' AND `date` BETWEEN '$date1' AND '$date2' "));
		$ipd_payment_amt=$ipd_payment["ipd_tot"];
		
		// Expense
		$exp_payment=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `exp_tot` FROM `expense_detail` WHERE `user`='$user' AND `date` BETWEEN '$date1' AND '$date2' "));
		$exp_payment_amt=$exp_payment["exp_tot"];
		
		// Expense
		$already=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `paid_tot` FROM `cash_deposit` WHERE `emp_id`='$user' AND `date` BETWEEN '$date1' AND '$date2' "));
		$already_paid=$already["paid_tot"];
		
		$tot_amt=$consult_payment_amt+$invest_payment_amt+$casual_payment_amt+$ipd_payment_amt;
		$net_amt=$consult_payment_amt+$invest_payment_amt+$casual_payment_amt+$ipd_payment_amt-$exp_payment_amt-$already_paid;
		
		// User
		$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));
	?>
		<p><b>Account of <i><?php echo $user_name["name"]; ?></i> from <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b> </p>
		<table class="table table-hover table-condensed" style="font-size: 14px;">
			<tr>
				<th style="width: 30%;">OPD Acount</th><td><b>: </b><?php echo $rupees_symbol.number_format($consult_payment_amt,2); ?></td>
			</tr>
			<tr>
				<th style="width: 30%;">LAB Acount</th><td><b>: </b><?php echo $rupees_symbol.number_format($invest_payment_amt,2); ?></td>
			</tr>
			<tr>
				<th style="width: 30%;">Casualty Acount</th><td><b>: </b><?php echo $rupees_symbol.number_format($casual_payment_amt,2); ?></td>
			</tr>
			<tr>
				<th style="width: 30%;">IPD Acount</th><td><b>: </b><?php echo $rupees_symbol.number_format($ipd_payment_amt,2); ?></td>
			</tr>
			<tr>
				<th style="width: 30%;">Total Amount</th><td><b>: </b><?php echo $rupees_symbol.number_format($tot_amt,2); ?></td>
			</tr>
			<tr>
				<th style="width: 30%;">Expense Amount</th><td><b>: </b><?php echo $rupees_symbol.number_format($exp_payment_amt,2); ?></td>
			</tr>
			<tr>
				<th style="width: 30%;">Already Paid</th><td><b>: </b><?php echo $rupees_symbol.number_format($already_paid,2); ?></td>
			</tr>
			<tr>
				<th style="width: 30%;">Net Amount</th><td><b>: </b><?php echo $rupees_symbol.number_format($net_amt,2); ?></td>
			</tr>
			<tr>
				<th style="width: 30%;">Now Pay</th>
				<td>
					<b>: </b>
					<input type="text" value="<?php echo $net_amt; ?>" id="now_pay">
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="">
					<button class="btn btn-info" onClick="save_cash_deposit()">Save</button>
					<button class="btn btn-success" onClick="detail_cash_deposit()">Print Detail</button>
				</td>
			</tr>
		</table>
	<?php
	}	
}

if($_POST["type"]=="save_cash_deposit")
{
	$date=date("Y-m-d");
	$time=date("H:i:s");
	
	$now_pay=$_POST['now_pay'];
	$user_entry=$_POST['user_entry'];
	$user=$_POST['user'];
	
	if($now_pay>0)
	{
		if(mysqli_query($link, " INSERT INTO `cash_deposit`(`emp_id`, `amount`, `user`, `date`, `time`) VALUES ('$user_entry','$now_pay','$user','$date','$time') "))
		{
			echo "Saved";
		}else
		{
			echo "Error !";
		}
	}else
	{
		echo "Not saved";
	}
	
}


if($_POST["type"]=="centre_summary")
{
	?>
	<p style="margin-top: 2%;"><b>Centre Summary Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/centre_summary_xls.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>"><i class="icon-file icon-large"></i> Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('centre_summary','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<?php
	$q="SELECT DISTINCT `center_no` FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' ORDER BY `slno`";
	$qry=mysqli_query($link,$q);
	$cen_num=mysqli_num_rows($qry);
	$cent="";
	while($rr=mysqli_fetch_array($qry))
	{
		$cent.=$rr['center_no']."@@";
	}
	
	$q2="SELECT DISTINCT `date` FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' ORDER BY `slno`";
	$qry2=mysqli_query($link,$q2);
	$dt_num=mysqli_num_rows($qry2);
	$dt="";
	while($rr2=mysqli_fetch_array($qry2))
	{
		$dt.=$rr2['date']."@@";
	}
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th width="10%">Date</th><th colspan="<?php echo $cen_num;?>">Center</th><th>Net Total</th>
		</tr>
		<tr>
			<td></td>
			<?php
			$cen=explode("@@",$cent);
			foreach($cen as $cn)
			{
				if($cn)
				{
					$cn_name=mysqli_fetch_array(mysqli_query($link, "SELECT `centrename` FROM `centremaster` WHERE `centreno`='$cn'"));
				?>
				<td><?php echo $cn_name['centrename'];?></td>
				<?php
				}
			}
			?>
			<td></td>
		</tr>
		<?php
		$dtt=explode("@@",$dt);
		foreach($dtt as $d)
		{
		if($d)
		{
		?>
		<tr>
			<th><?php echo convert_date($d);?></th>
			<?php
			$net=0;
			$c_amt="";
			$cen=explode("@@",$cent);
			foreach($cen as $cn)
			{
				if($cn)
				{
					$amt=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(sum(advance),0) as tot FROM `invest_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` AND a.`date`='$d' AND b.`center_no`='$cn'"));
					
				?>
				<td style="text-align:right;"><?php echo $amt['tot'];?></td>
				<?php
				$net+=$amt['tot'];
				$c_amt.=$cn."@@";
				}
			}
			?>
			<td style="text-align:right;"><?php echo number_format($net,2);?></td>
		</tr>
		<?php
		}
		}
		?>
		<tr>
			<th>Total</th>
			<?php
			$all_net=0;
			$ca=explode("@@",$c_amt);
			foreach($ca as $c)
			{
				if($c)
				{
					$n_amt=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(sum(advance),0) as tot FROM `invest_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`center_no`='$c'"));
				?>
				<td style="text-align:right;"><?php echo number_format($n_amt['tot'],2);?></td>
				<?php
				$all_net+=$n_amt['tot'];
				}
			}
			?>
			<td style="text-align:right;"><?php echo number_format($all_net,2);?></td>
		</tr>
	</table>
	<?php
}

if($_POST["type"]=="pending_bill")
{
	?>
	<p style="margin-top: 2%;"><b>Pending Bill Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/pending_bill_xls.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>"><i class="icon-file icon-large"></i> Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('pending_bill','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Date</th><th>Sl No.</th><th>PIN</th><th>Patient Name</th><th>Amount</th><th>C/O</th><th style="width: 25%;">Remarks</th>
		</tr>
	<?php
	$n=1;
	$tot=0;
	$q="SELECT DISTINCT `opd_id` FROM `invest_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `balance`>0 ORDER BY `slno`";
	$qry=mysqli_query($link,$q);
	while($res=mysqli_fetch_array($qry))
	{
		$qq=mysqli_query($link,"SELECT * FROM `invest_patient_payment_details` WHERE `opd_id`='$res[opd_id]'");
		while($r=mysqli_fetch_array($qq))
		{
			$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			$cn=mysqli_fetch_array(mysqli_query($link,"SELECT `ipd_serial`,`center_no` FROM `uhid_and_opdid` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]'"));
			$cab=mysqli_fetch_array(mysqli_query($link,"SELECT `centrename` FROM `centremaster` WHERE `centreno`='$cn[center_no]'"));
			
			$remark_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `remark` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' "));
			
			$patient_cabin=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_cabin` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' "));
			if($patient_cabin)
			{
				$center_info=$cab['centrename']."(".$patient_cabin["cabin_no"].")";
			}else
			{
				$center_info=$cab['centrename'];
			}
		?>
		<tr>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $cn['ipd_serial'];?></td>
			<td><?php echo $r['opd_id'];?></td>
			<td><?php echo $pat['name'];?></td>
			<td style="text-align:right;"><?php echo $r['balance'];?></td>
			<td><?php echo $center_info;?></td>
			<td ondblclick="pending_remark('<?php echo $r['patient_id'];?>','<?php echo $r['opd_id'];?>','<?php echo $n;?>')" style="cursor:pointer;" >
				<span id="remark_val<?php echo $n;?>">
					<?php echo $remark_val["remarks"];?>
				</span>
				<input type="text" class="pending_remark" id="pending_remark<?php echo $n;?>" style="display:none;" onKeyup="pending_remark_up(event,'<?php echo $r['patient_id'];?>','<?php echo $r['opd_id'];?>','<?php echo $n;?>')" onblur="pending_remark_blur('<?php echo $r['patient_id'];?>','<?php echo $r['opd_id'];?>','<?php echo $n;?>')">
			</td>
		</tr>
		<?php
			$tot+=$r['balance'];
			$n++;
		}
	}
	?>
		<tr>
			<th colspan="4" style="text-align:right;">Total</th><th style="text-align:right;"><?php echo number_format($tot,2);?></th><th colspan="3"></th>
		</tr>
	</table>
	<?php
}

if($_POST["type"]=="insurance")
{
	?>
	<p>
		<b>Insurance Bill Pending Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<button type="button" class="btn btn-info text-right" onclick="export_page('insurance','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-file icon-large"></i> Export to Excel</button>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('insurance','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Date</th><th>Sl No.</th><th>PIN</th><th>Patient Name</th><th>Amount</th><th>C/O</th>
		</tr>
	<?php
	$tot=0;
	$q="SELECT a.*,b.center_no,b.`ipd_serial` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b, `centremaster` c WHERE b.`date` BETWEEN '$date1' AND '$date2' AND a.`balance`>0 AND b.`center_no`=c.`centreno` AND c.`insurance`>0 AND a.`opd_id`=b.`opd_id`";
	$qry=mysqli_query($link,$q);
	while($r=mysqli_fetch_array($qry))
	{
		$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
		$cab=mysqli_fetch_array(mysqli_query($link,"SELECT `centrename` FROM `centremaster` WHERE `centreno`='$r[center_no]'"));
		?>
		<tr>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $r['ipd_serial'];?></td>
			<td><?php echo $r['opd_id'];?></td>
			<td><?php echo $pat['name'];?></td>
			<td style="text-align:right;"><?php echo $r['balance'];?></td>
			<td><?php echo $cab['centrename'];?></td>
		</tr>
		<?php
		$tot+=$r['balance'];
	}
	?>
		<tr>
			<th colspan="4" style="text-align:right;">Total</th><th style="text-align:right;"><?php echo number_format($tot,2);?></th><th></th>
		</tr>
	</table>
	<?php
}

if($_POST["type"]=="esi")
{
	?>
	<p>
		<b>ESIC Bill Pending Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span style="float:right;">
			<button type="button" class="btn btn-info" onclick="print_page('esi','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')"><i class="icon-print icon-large"></i> Print</button>
			<button type="button" class="btn btn-info" onclick="export_page('esi','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')"><i class="icon-file icon-large"></i> Export to Excel</button>
		</span>
	</p>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Date</th><th>Sl No.</th><th>PIN</th><th>Cabin</th><th>Patient Name</th><th>Amount</th><th>Center</th>
		</tr>
	<?php
	$tot=0;
	$q="SELECT a.*,b.center_no,b.`ipd_serial` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b WHERE b.`date` BETWEEN '$date1' AND '$date2' AND a.`balance`>0 AND (b.`center_no`='C101' OR b.`center_no`='C124') AND a.`opd_id`=b.`opd_id`";
	$qry=mysqli_query($link,$q);
	while($r=mysqli_fetch_array($qry))
	{
		$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
		$cab=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_cabin` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]'"));
		$cen=mysqli_fetch_array(mysqli_query($link,"SELECT `centrename` FROM `centremaster` WHERE `centreno`='$r[center_no]'"));
		?>
		<tr>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $r['ipd_serial'];?></td>
			<td><?php echo $r['opd_id'];?></td>
			<td><?php echo $cab['cabin_no'];?></td>
			<td><?php echo $pat['name'];?></td>
			<td style="text-align:right;"><?php echo $r['balance'];?></td>
			<td><?php echo $cen['centrename'];?></td>
		</tr>
		<?php
		$tot+=$r['balance'];
	}
	?>
		<tr>
			<th colspan="5" style="text-align:right;">Total</th><th style="text-align:right;"><?php echo number_format($tot,2);?></th><th></th>
		</tr>
	</table>
	<?php
}

if($_POST["type"]=="cabin")
{
?>
	<p>
		<b>Cabin Bill Pending Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span style="float:right;">
			<button type="button" class="btn btn-info" onclick="print_page('cabin','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')"><i class="icon-print icon-large"></i> Print</button>
			<button type="button" class="btn btn-info" onclick="export_page('cabin','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')"><i class="icon-file icon-large"></i> Export to Excel</button>
		</span>
	</p>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Date</th><th>Sl No.</th><th>PIN</th><th>Cabin</th><th>Patient Name</th><th>Amount</th><th>Center</th>
		</tr>
	<?php
	$tot=0;
	$q="SELECT a.*,b.center_no,b.`ipd_serial` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b, `patient_cabin` c WHERE b.`date` BETWEEN '$date1' AND '$date2' AND a.`balance`>0 AND a.`opd_id`=b.`opd_id` AND b.`opd_id`=c.`opd_id` ";
	$qry=mysqli_query($link,$q);
	while($r=mysqli_fetch_array($qry))
	{
		$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
		$cab=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_cabin` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]'"));
		$cen=mysqli_fetch_array(mysqli_query($link,"SELECT `centrename` FROM `centremaster` WHERE `centreno`='$r[center_no]'"));
		?>
		<tr>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $r['ipd_serial'];?></td>
			<td><?php echo $r['opd_id'];?></td>
			<td><?php echo $cab['cabin_no'];?></td>
			<td><?php echo $pat['name'];?></td>
			<td style="text-align:right;"><?php echo $r['balance'];?></td>
			<td><?php echo $cen['centrename'];?></td>
		</tr>
		<?php
		$tot+=$r['balance'];
	}
	?>
		<tr>
			<th colspan="5" style="text-align:right;">Total</th><th style="text-align:right;"><?php echo number_format($tot,2);?></th><th></th>
		</tr>
	</table>
	<b>Cabin Advance/Bill Pending Receive Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Date</th><th>Sl No.</th><th>PIN</th><th>Cabin</th><th>Patient Name</th><th>Amount</th><th>Center</th>
		</tr>
	<?php
	$tot=0;
	$q="SELECT a.*,b.center_no,b.`ipd_serial` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b, `patient_cabin` c WHERE b.`date` BETWEEN '$date1' AND '$date2' AND a.`balance`=0 AND a.`tot_amount`>0 AND a.`opd_id`=b.`opd_id` AND b.`opd_id`=c.`opd_id` ";
	$qry=mysqli_query($link,$q);
	while($r=mysqli_fetch_array($qry))
	{
		$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
		$cab=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_cabin` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]'"));
		$cen=mysqli_fetch_array(mysqli_query($link,"SELECT `centrename` FROM `centremaster` WHERE `centreno`='$r[center_no]'"));
		?>
		<tr>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $r['ipd_serial'];?></td>
			<td><?php echo $r['opd_id'];?></td>
			<td><?php echo $cab['cabin_no'];?></td>
			<td><?php echo $pat['name'];?></td>
			<td style="text-align:right;"><?php echo $r['tot_amount'];?></td>
			<td><?php echo $cen['centrename'];?></td>
		</tr>
		<?php
		$tot+=$r['tot_amount'];
	}
	?>
		<tr>
			<th colspan="5" style="text-align:right;">Total</th><th style="text-align:right;"><?php echo number_format($tot,2);?></th><th></th>
		</tr>
	</table>
	<?php
}


if($_POST["type"]=="daily_details")
{
	$qry=mysqli_query($link, " SELECT DISTINCT `date` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2' ");
?>
	<p>
		<b>Daily Summary Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span style="float:right;">
			<button type="button" class="btn btn-info" onclick="print_page('daily_details','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')"><i class="icon-print icon-large"></i> Print</button>
			<button type="button" class="btn btn-info" onclick="export_page('daily_details','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')"><i class="icon-print icon-large"></i> Export to Excel</button>
		</span>
	</p>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Date</th><th>Bill Amount</th><th>Money Received</th><th>Balance Received</th><th>Free</th><th>Refund</th><th>Net Amount</th><th>Discount</th><th>Difference</th><!--<th>Insurance</th><th>ESI</th><th>Cabin</th>-->
		</tr>
<?php
	$net_tot_bill=$net_adv_recv=$net_bal_recv=$net_recv=$net_free=$net_refund=$net_dis=$net_diff=0;
	while($dis_date=mysqli_fetch_array($qry))
	{
		$tot_bill_date=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`tot_amount`) AS `tot_bill` FROM `invest_patient_payment_details` WHERE `date`='$dis_date[date]' "));
		$tot_bill_date_amount=$tot_bill_date["tot_bill"];
		$net_tot_bill+=$tot_bill_date_amount;
		
		$tot_adv_date=mysqli_fetch_array(mysqli_query($link," SELECT SUM(a.`amount`) AS `tot_adv` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` and a.`date`=b.`date` AND a.`date`='$dis_date[date]' "));
		$tot_adv_date_amount=$tot_adv_date["tot_adv"];
		$net_adv_recv+=$tot_adv_date_amount;
		
		$tot_bal_date=mysqli_fetch_array(mysqli_query($link," SELECT SUM(a.`amount`) AS `tot_bal` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` AND b.`date`!=a.`date` AND a.`date`='$dis_date[date]'  ")); //AND a.`typeofpayment`='B'
		$tot_bal_date_amount=$tot_bal_date["tot_bal"];
		$net_bal_recv+=$tot_bal_date_amount;
		
		$tot_free_date_same=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`free_amount`) AS `tot_free` FROM `invest_payment_free` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` and a.`date`=b.`date` AND a.`date`='$dis_date[date]' "));
		$tot_free_date_amount_same=$tot_free_date_same["tot_free"];
		
		$tot_free_date_other=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`free_amount`) AS `tot_free` FROM `invest_payment_free` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` and a.`date`!=b.`date` AND a.`date`='$dis_date[date]' "));
		$tot_free_date_amount_other=$tot_free_date_other["tot_free"];
		
		$tot_free_date_amount=$tot_free_date_amount_same+$tot_free_date_amount_other;
		
		$net_free+=$tot_free_date_amount;
		
		
		$tot_refund_date_same=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`refund_amount`) AS `tot_refund` FROM `invest_payment_refund` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` AND b.`date`=a.`date` AND a.`date`='$dis_date[date]' "));
		$tot_refund_date_amount_same=$tot_refund_date_same["tot_refund"];
		
		$tot_refund_date_other=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`refund_amount`) AS `tot_refund` FROM `invest_payment_refund` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` AND b.`date`!=a.`date` AND a.`date`='$dis_date[date]' "));
		$tot_refund_date_amount_other=$tot_refund_date_other["tot_refund"];
		
		$tot_refund_date_amount=$tot_refund_date_amount_same+$tot_refund_date_amount_other;
		
		$net_refund+=$tot_refund_date_amount;
		
		$tot_dis_date=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`dis_amt`) AS `tot_dis` FROM `invest_patient_payment_details` WHERE `date`='$dis_date[date]' "));
		$tot_dis_date_amount=$tot_dis_date["tot_dis"];
		$net_dis+=$tot_dis_date_amount;
		
		$tot_recv=$tot_adv_date_amount+$tot_bal_date_amount-$tot_free_date_amount_other-$tot_refund_date_amount_other;
		//$tot_recv=$tot_adv_date_amount+$tot_bal_date_amount;
		$net_recv+=$tot_recv;
		
		$tot_diff=$tot_bill_date_amount-$tot_adv_date_amount-$tot_dis_date_amount;
		$net_diff+=$tot_diff;
?>
		<tr>
			<td><?php echo convert_date($dis_date["date"]); ?></td>
			<td><?php echo number_format($tot_bill_date_amount,2); ?></td>
			<td><?php echo number_format($tot_adv_date_amount,2); ?></td>
			<td><?php echo number_format($tot_bal_date_amount,2); ?></td>
			<td><?php echo number_format($tot_free_date_amount,2); ?></td>
			<td><?php echo number_format($tot_refund_date_amount,2); ?></td>
			<td><?php echo number_format($tot_recv,2); ?></td>
			<td><?php echo number_format($tot_dis_date_amount,2); ?></td>
			<td><?php echo number_format($tot_diff,2); ?></td>
		</tr>
<?php
	}
?>
		<tr>
			<th>Total</th>
			<th><?php echo number_format($net_tot_bill,2); ?></th>
			<th><?php echo number_format($net_adv_recv,2); ?></th>
			<th><?php echo number_format($net_bal_recv,2); ?></th>
			<th><?php echo number_format($net_free,2); ?></th>
			<th><?php echo number_format($net_refund,2); ?></th>
			<th><?php echo number_format($net_recv,2); ?></th>
			<th><?php echo number_format($net_dis,2); ?></th>
			<th><?php echo number_format($net_diff,2); ?></th>
		</tr>
	</table>
<?php
}


if($_POST["type"]=="daily_detail")
{
?>
	<p>
		<b>Daily Details Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span style="float:right;">
			<button type="button" class="btn btn-info" onclick="print_page('daily_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')"><i class="icon-print icon-large"></i> Print</button>
			<button type="button" class="btn btn-info" onclick="export_page('daily_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')"><i class="icon-print icon-large"></i> Export to Excel</button>
		</span>
	</p>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>PIN</th><th>Patient Name</th><th>Amount</th><th>Reg Date Time</th><th>User</th>
		</tr>
		<tr>
			<th colspan="6">Advance Received</th>
		</tr>
<?php
	$n=1;
	$net_adv_recv=$net_bal_recv=$net_recv=$net_free=$net_refund=$net_dis=0;
	// Advance
	$adv_pat_qry=mysqli_query($link," SELECT DISTINCT(a.`opd_id`) FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` AND b.`date` BETWEEN '$date1' AND '$date2' AND a.`amount`>0 ORDER BY a.`slno` ");
	while($adv_pat=mysqli_fetch_array($adv_pat_qry))
	{
		$reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$adv_pat[opd_id]' "));
		
		$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$reg[patient_id]' "));
		
		$tot_adv_date=mysqli_fetch_array(mysqli_query($link," SELECT SUM(a.`amount`) AS `tot_adv` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` and a.`date`=b.`date` AND a.`opd_id`='$adv_pat[opd_id]' "));
		
		$tot_adv_date_amount=$tot_adv_date["tot_adv"];
		$net_adv_recv+=$tot_adv_date_amount;
		
		$user_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$reg[user]' "));
		
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $tot_adv_date_amount; ?></td>
			<td><?php echo convert_date($reg["date"]); ?> <?php echo convert_time($reg["time"]); ?></td>
			<td><?php echo $user_info["name"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>
		<tr>
			<th colspan="3"><span class="text-right">Total Advance Received</span> </th>
			<td colspan="3"><?php echo number_format($net_adv_recv,2); ?></td>
		</tr>
		
		<tr>
			<th colspan="6">Balance Received</th>
		</tr>
<?php
	// Balance
	$bal_pat_qry=mysqli_query($link," SELECT DISTINCT(a.`opd_id`) FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` AND b.`date`!=a.`date` AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`amount`>0 ORDER BY a.`slno` ");
	while($bal_pat=mysqli_fetch_array($bal_pat_qry))
	{
		$reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$bal_pat[opd_id]' "));
		
		$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$reg[patient_id]' "));
		
		$tot_bal_date=mysqli_fetch_array(mysqli_query($link," SELECT SUM(a.`amount`) AS `tot_bal` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`opd_id`=b.`opd_id` and a.`date`!=b.`date` AND a.`opd_id`='$bal_pat[opd_id]' "));
		
		$tot_bal_date_amount=$tot_bal_date["tot_bal"];
		$net_bal_recv+=$tot_bal_date_amount;
		
		$user_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$reg[user]' "));
		
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $tot_bal_date_amount; ?></td>
			<td><?php echo convert_date($reg["date"]); ?> <?php echo convert_time($reg["time"]); ?></td>
			<td><?php echo $user_info["name"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>
		<tr>
			<th colspan="3"><span class="text-right">Total Balance Received</span> </th>
			<td colspan="3"><?php echo number_format($net_bal_recv,2); ?></td>
		</tr>
		
		<tr>
			<th colspan="6">Free Patient</th>
		</tr>
<?php
	// Free
	$free_pat_qry=mysqli_query($link," SELECT * FROM `invest_payment_free` WHERE `date` BETWEEN '$date1' AND '$date2' ");
	while($free_pat=mysqli_fetch_array($free_pat_qry))
	{
		$reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$free_pat[opd_id]' "));
		
		$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$reg[patient_id]' "));
		
		$tot_free_date_amount=$free_pat["free_amount"];
		$net_free+=$tot_free_date_amount;
		
		$user_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$free_pat[user]' "));
		
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $tot_free_date_amount; ?></td>
			<td><?php echo convert_date($reg["date"]); ?> <?php echo convert_time($reg["time"]); ?></td>
			<td><?php echo $user_info["name"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>		<tr>
			<th colspan="3"><span class="text-right">Total Free Amount</span> </th>
			<td colspan="3"><?php echo number_format($net_free,2); ?></td>
		</tr>
		<tr>
			<th colspan="6">Refund Patient</th>
		</tr>
<?php
	// Free
	$refund_pat_qry=mysqli_query($link," SELECT * FROM `invest_payment_refund` WHERE `date` BETWEEN '$date1' AND '$date2' ");
	while($refund_pat=mysqli_fetch_array($refund_pat_qry))
	{
		$reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$refund_pat[opd_id]' "));
		
		$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$reg[patient_id]' "));
		
		$tot_refund_date_amount=$refund_pat["refund_amount"];
		$net_refund+=$tot_refund_date_amount;
		
		$user_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$refund_pat[user]' "));
		
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $tot_refund_date_amount; ?></td>
			<td><?php echo convert_date($reg["date"]); ?> <?php echo convert_time($reg["time"]); ?></td>
			<td><?php echo $user_info["name"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>		<tr>
			<th colspan="3"><span class="text-right">Total Free Amount</span> </th>
			<td colspan="3"><?php echo number_format($net_refund,2); ?></td>
		</tr>
		
		<tr>
			<th colspan="3"><span class="text-right">Net Amount</span> </th>
			<td colspan="3"><?php echo number_format(($net_adv_recv+$net_bal_recv-$net_free-$net_refund),2); ?></td>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="save_pending_remark")
{
	$date=date("Y-m-d");
	$time=date("H:i:s");
	
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$user=$_POST["user"];
	$remark=$_POST["remark"];
	$remark=str_replace("'","''", $remark);
	
	$check_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `remark` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	if($check_val)
	{
		mysqli_query($link," UPDATE `remark` SET `remarks`='$remark',`user`='$user',`date`='$date',`time`='$time' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
	}else
	{
		mysqli_query($link," INSERT INTO `remark`(`patient_id`, `opd_id`, `remarks`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$remark','$user','$date','$time') ");
	}
	
}
?>
