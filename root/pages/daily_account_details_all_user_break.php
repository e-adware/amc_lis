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

// Function that gives days between including two dates
function getDatesFromRange($start, $end, $format = 'Y-m-d')
{
	$array = array();
	$interval = new DateInterval('P1D');

	$realEnd = new DateTime($end);
	$realEnd->add($interval);

	$period = new DatePeriod(new DateTime($start), $interval, $realEnd);

	foreach($period as $date) { 
		$array[] = $date->format($format); 
	}

	return $array;
}

function indian_money_format($amount)
{
	setlocale(LC_MONETARY, 'en_IN');
	$amount = money_format('%!i', $amount);
	return $amount;
}
//~ $rupees_symbol="&#x20b9; ";
$rupees_symbol="";

if($_POST["type"]=="all_account")
{
	$encounter=trim($_POST['encounter']);
	$user_entry=trim($_POST['user_entry']);
	$pay_mode=trim($_POST['pay_mode']);
	$account_break=trim($_POST['account_break']);
	
	$date_str="";
	$date_str_exp="";
	$date_str_a="";
	$date_str_b="";
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	
	// Less
	if($user_entry!=$c_user)
	{
		// admin
		$date_str=" AND `date` between '$date1' and '$date2'";
		$date_str_exp=" AND `expense_date` between '$date1' and '$date2'";
		$date_str_a=" AND a.`date` between '$date1' and '$date2'";
		$date_str_b=" AND b.`date` between '$date1' and '$date2'";
	}else
	{
		if($account_break==0)
		{
			$con_max_slno_str_less="";
			$inv_max_slno_str_less="";
			$ipd_max_slno_str_less="";
			$exp_max_slno_str_less="";
			$ref_max_slno_str_less="";
			$fre_max_slno_str_less="";
		}else
		{
			$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `slno`='$account_break' "));
			
			$con_max_slno_less=$check_close_account_today['con_slno'];
			$con_max_slno_str_less=" AND `slno`<=$con_max_slno_less ";
			
			$inv_max_slno_less=$check_close_account_today['inv_slno'];
			$inv_max_slno_str_less=" AND `slno`<=$inv_max_slno_less ";
			
			$ipd_max_slno_less=$check_close_account_today['ipd_slno'];
			$ipd_max_slno_str_less=" AND `slno`<=$ipd_max_slno_less ";
			
			$exp_max_slno_less=$check_close_account_today['exp_slno'];
			$exp_max_slno_str_less=" AND `slno`<=$exp_max_slno_less ";
			
			// Object a
			$ref_max_slno_less=$check_close_account_today['refund_slno'];
			$ref_max_slno_str_less=" AND a.`slno`<=$ref_max_slno_less ";
			
			$fre_max_slno_less=$check_close_account_today['free_slno'];
			$fre_max_slno_str_less=" AND a.`slno`<=$fre_max_slno_less ";
		}
	}
	
	// Greater
	if($user_entry!=$c_user)
	{
		// Admin
		$date_str=" AND `date` between '$date1' and '$date2'";
		$date_str_exp=" AND `expense_date` between '$date1' and '$date2'";
		$date_str_a=" AND a.`date` between '$date1' and '$date2'";
		$date_str_b=" AND b.`date` between '$date1' and '$date2'";
	}else
	{
		if($account_break==0)
		{
			//$last_date=date('Y-m-d', strtotime($date1. ' - 1 days'));
			
			$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `user`='$user_entry' ORDER BY `slno` DESC"));	
			if(!$check_close_account_today)
			{
				$con_max_slno_str_grtr=" AND `date` between '$date1' and '$date2'";
				$inv_max_slno_str_grtr=" AND `date` between '$date1' and '$date2'";
				$ipd_max_slno_str_grtr=" AND `date` between '$date1' and '$date2'";
				$exp_max_slno_str_grtr=" AND `expense_date` between '$date1' and '$date2'";
				//~ $ref_max_slno_str_grtr=" AND `date` between '$date1' and '$date2'";
				$ref_max_slno_str_grtr="";
				$fre_max_slno_str_grtr=" AND `date` between '$date1' and '$date2'";
			}else
			{
				$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));
			
				$con_max_slno_grtr=$check_close_account_today['con_slno'];
				$con_max_slno_str_grtr=" AND `slno`>$con_max_slno_grtr ";
				
				$inv_max_slno_grtr=$check_close_account_today['inv_slno'];
				$inv_max_slno_str_grtr=" AND `slno`>$inv_max_slno_grtr ";
				
				$ipd_max_slno_grtr=$check_close_account_today['ipd_slno'];
				$ipd_max_slno_str_grtr=" AND `slno`>$ipd_max_slno_grtr ";
				
				$exp_max_slno_grtr=$check_close_account_today['exp_slno'];
				$exp_max_slno_str_grtr=" AND `slno`>$exp_max_slno_grtr ";
				
				// Object a
				$ref_max_slno_grtr=$check_close_account_today['refund_slno'];
				$ref_max_slno_str_grtr=" AND a.`slno`>$ref_max_slno_grtr ";
				
				$fre_max_slno_grtr=$check_close_account_today['free_slno'];
				$fre_max_slno_str_grtr=" AND a.`slno`>$fre_max_slno_grtr ";
			}
		}else
		{
			$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `slno`<'$account_break' AND `user`='$user_entry' ORDER BY `slno` DESC "));
			
			if($check_close_account_today)
			{
				$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));
				
				$con_max_slno_grtr=$check_close_account_today['con_slno'];
				$con_max_slno_str_grtr=" AND `slno`>$con_max_slno_grtr ";
				
				$inv_max_slno_grtr=$check_close_account_today['inv_slno'];
				$inv_max_slno_str_grtr=" AND `slno`>$inv_max_slno_grtr ";
				
				$ipd_max_slno_grtr=$check_close_account_today['ipd_slno'];
				$ipd_max_slno_str_grtr=" AND `slno`>$ipd_max_slno_grtr ";
				
				$exp_max_slno_grtr=$check_close_account_today['exp_slno'];
				$exp_max_slno_str_grtr=" AND `slno`>$exp_max_slno_grtr ";
				
				// Object a
				$ref_max_slno_grtr=$check_close_account_today['refund_slno'];
				$ref_max_slno_str_grtr=" AND a.`slno`>$ref_max_slno_grtr ";
				
				$fre_max_slno_grtr=$check_close_account_today['free_slno'];
				$fre_max_slno_str_grtr=" AND a.`slno`>$fre_max_slno_grtr ";
			}else
			{
				$con_max_slno_str_grtr="";
				$inv_max_slno_str_grtr="";
				$ipd_max_slno_str_grtr="";
				$exp_max_slno_str_grtr="";
				$ref_max_slno_str_grtr="";
				$fre_max_slno_str_grtr="";
			}
		}
	}
	
	$encounter_pay_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type=$pat_typ_text["type"];
	}
	
	$all_pin=array();
	$card_pin="";
	$cheque_pin="";
	$neft_pin="";
	$rtgs_pin="";
	$imps_pin="";
	$upi_pin="";
	$credit_pin="";
	$i=1;
	if($encounter==0 || $encounter_pay_type==1)
	{
		//echo " SELECT distinct `opd_id` FROM `consult_payment_detail` WHERE `slno`>0 $date_str $con_max_slno_str_less $con_max_slno_str_grtr $user ORDER BY `slno`<br>";
		$con_pay_qry=mysqli_query($link, " SELECT distinct `opd_id` FROM `consult_payment_detail` WHERE `slno`>0 $date_str $con_max_slno_str_less $con_max_slno_str_grtr $user ORDER BY `slno`");
		while($con_pay=mysqli_fetch_array($con_pay_qry))
		{
			$all_pin[$i]=$con_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter_pay_type==2)
	{
		$inv_pay_qry=mysqli_query($link, " SELECT distinct `opd_id` FROM `invest_payment_detail` WHERE `slno`>0 $date_str $inv_max_slno_str_less $inv_max_slno_str_grtr $user ORDER BY `slno`");
		while($inv_pay=mysqli_fetch_array($inv_pay_qry))
		{
			$all_pin[$i]=$inv_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter_pay_type==3)
	{
		//echo " SELECT `ipd_id` FROM `ipd_advance_payment_details` WHERE `slno`>0 $date_str $ipd_max_slno_str_less $ipd_max_slno_str_grtr $user ORDER BY `slno`";
		$ipd_casual_pay_qry=mysqli_query($link, " SELECT `ipd_id` FROM `ipd_advance_payment_details` WHERE `slno`>0 $date_str $ipd_max_slno_str_less $ipd_max_slno_str_grtr $user ORDER BY `slno`");
		while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
		{
			$all_pin[$i]=$ipd_casual_pay["ipd_id"];
			$i++;
		}
	}
	sort($all_pin);
	//print_r($all_pin);
	
?>
	<p style="margin-top: 2%;" id="print_div">
		<b>Detail Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info btn-mini" href="pages/detail_account_print_user_break_xls.php?date1=<?php echo $date11;?>&date2=<?php echo $date22;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>&pay_mode=<?php echo $pay_mode;?>&account_break=<?php echo $account_break;?>"><i class="icon-file icon-large"></i> Excel</a></span>
		
		<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('detail','<?php echo $date11;?>','<?php echo $date22;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','<?php echo $pay_mode;?>','<?php echo $account_break;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		
	</p>
	<table class="table table-hover table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th class="ipd_serial">Sl No.</th>
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Bill No</th>
				<th>Amount</th>
				<th>User</th>
				<th>Encounter</th>
			</tr>
		</thead>
	<?php
		$n=1;
		$zz=$yy=$ww=1;
		$tot_amt=$tot_amt_cash=$tot_amt_card=$tot_amt_cheque=$tot_amt_neft=$tot_amt_rtgs=$tot_amt_imps=$tot_amt_upi=$tot_amt_credit=0;
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
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						if($pat_typ_text["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Cash' $date_str $user  $con_max_slno_str_less $con_max_slno_str_grtr"; //AND `date` between '$date1' and '$date2'
							$pat_pay_detail_qry=mysqli_query($link, $con_pay_det);
						}
						if($pat_typ_text["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Cash' $date_str $user $inv_max_slno_str_less $inv_max_slno_str_grtr "; // AND `date` between '$date1' and '$date2'
							$inv_pat_pay_detail_qry=mysqli_query($link, $inv_pay_det);
						}
						if($pat_typ_text["type"]==3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='Cash' $date_str $user $ipd_max_slno_str_less $ipd_max_slno_str_grtr "; // AND `date` between '$date1' and '$date2'
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
							
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
							//echo "<tr><th colspan='9'>Card</th></tr>";
							$yy++;
						}
						if($pin==$all_pat['opd_id'])
						{
							$all_pat["type"]=2;
						}else
						{
							$pin=$all_pat['opd_id'];
						}
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						if($pat_typ_text["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str $user  $con_max_slno_str_less $con_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $con_pay_det));
							$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
							
							if($pat_pay_detail_num>0)
							{
								$card_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
							}
						}
						if($pat_typ_text["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str  $user  $inv_max_slno_str_less $inv_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$inv_pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $inv_pay_det));
							$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
							
							if($inv_pat_pay_detail_num>0)
							{
								$card_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
							}
						}
						if($pat_typ_text["type"]==3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='$pay_mode' $date_str $user $ipd_max_slno_str_less $ipd_max_slno_str_grtr "; // AND `date` between '$date1' and '$date2'
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							if($ipd_cas_pat_pay_detail)
							{
								$card_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
							}
						}
						
					}
					if($pay_mode=="Cheque")
					{
						if($yy==1)
						{
							//echo "<tr><th colspan='9'>Cheque</th></tr>";
							$yy++;
						}
						if($pin==$all_pat['opd_id'])
						{
							$all_pat["type"]=2;
						}else
						{
							$pin=$all_pat['opd_id'];
						}
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						if($pat_typ_text["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str $user  $con_max_slno_str_less $con_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $con_pay_det));
							$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
							
							if($pat_pay_detail_num>0)
							{
								$cheque_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
							}
						}
						if($pat_typ_text["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str  $user  $inv_max_slno_str_less $inv_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$inv_pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $inv_pay_det));
							$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
							
							if($inv_pat_pay_detail_num>0)
							{
								$cheque_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
							}
						}
						if($pat_typ_text["type"]==3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='$pay_mode' $date_str $user $ipd_max_slno_str_less $ipd_max_slno_str_grtr "; // AND `date` between '$date1' and '$date2'
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							if($ipd_cas_pat_pay_detail)
							{
								$cheque_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
							}
						}	
					}
					if($pay_mode=="NEFT")
					{
						if($yy==1)
						{
							//echo "<tr><th colspan='9'>Cheque</th></tr>";
							$yy++;
						}
						if($pin==$all_pat['opd_id'])
						{
							$all_pat["type"]=2;
						}else
						{
							$pin=$all_pat['opd_id'];
						}
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						if($pat_typ_text["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str $user  $con_max_slno_str_less $con_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $con_pay_det));
							$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
							
							if($pat_pay_detail_num>0)
							{
								$neft_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
							}
						}
						if($pat_typ_text["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str  $user  $inv_max_slno_str_less $inv_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$inv_pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $inv_pay_det));
							$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
							
							if($inv_pat_pay_detail_num>0)
							{
								$neft_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
							}
						}
						if($pat_typ_text["type"]==3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='$pay_mode' $date_str $user $ipd_max_slno_str_less $ipd_max_slno_str_grtr "; // AND `date` between '$date1' and '$date2'
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							if($ipd_cas_pat_pay_detail)
							{
								$neft_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
							}
						}	
					}
					if($pay_mode=="RTGS")
					{
						if($yy==1)
						{
							//echo "<tr><th colspan='9'>Cheque</th></tr>";
							$yy++;
						}
						if($pin==$all_pat['opd_id'])
						{
							$all_pat["type"]=2;
						}else
						{
							$pin=$all_pat['opd_id'];
						}
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						if($pat_typ_text["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str $user  $con_max_slno_str_less $con_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $con_pay_det));
							$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
							
							if($pat_pay_detail_num>0)
							{
								$rtgs_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
							}
						}
						if($pat_typ_text["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str  $user  $inv_max_slno_str_less $inv_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$inv_pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $inv_pay_det));
							$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
							
							if($inv_pat_pay_detail_num>0)
							{
								$rtgs_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
							}
						}
						if($pat_typ_text["type"]==3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='$pay_mode' $date_str $user $ipd_max_slno_str_less $ipd_max_slno_str_grtr "; // AND `date` between '$date1' and '$date2'
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							if($ipd_cas_pat_pay_detail)
							{
								$rtgs_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
							}
						}	
					}
					if($pay_mode=="IMPS")
					{
						if($yy==1)
						{
							//echo "<tr><th colspan='9'>Cheque</th></tr>";
							$yy++;
						}
						if($pin==$all_pat['opd_id'])
						{
							$all_pat["type"]=2;
						}else
						{
							$pin=$all_pat['opd_id'];
						}
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						if($pat_typ_text["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str $user  $con_max_slno_str_less $con_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $con_pay_det));
							$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
							
							if($pat_pay_detail_num>0)
							{
								$imps_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
							}
						}
						if($pat_typ_text["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str  $user  $inv_max_slno_str_less $inv_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$inv_pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $inv_pay_det));
							$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
							
							if($inv_pat_pay_detail_num>0)
							{
								$imps_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
							}
						}
						if($pat_typ_text["type"]==3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='$pay_mode' $date_str $user $ipd_max_slno_str_less $ipd_max_slno_str_grtr "; // AND `date` between '$date1' and '$date2'
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							if($ipd_cas_pat_pay_detail)
							{
								$imps_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
							}
						}	
					}
					if($pay_mode=="UPI")
					{
						if($yy==1)
						{
							//echo "<tr><th colspan='9'>Cheque</th></tr>";
							$yy++;
						}
						if($pin==$all_pat['opd_id'])
						{
							$all_pat["type"]=2;
						}else
						{
							$pin=$all_pat['opd_id'];
						}
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						if($pat_typ_text["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str $user  $con_max_slno_str_less $con_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $con_pay_det));
							$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
							
							if($pat_pay_detail_num>0)
							{
								$upi_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
							}
						}
						if($pat_typ_text["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str  $user  $inv_max_slno_str_less $inv_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$inv_pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $inv_pay_det));
							$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
							
							if($inv_pat_pay_detail_num>0)
							{
								$upi_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
							}
						}
						if($pat_typ_text["type"]==3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='$pay_mode' $date_str $user $ipd_max_slno_str_less $ipd_max_slno_str_grtr "; // AND `date` between '$date1' and '$date2'
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							if($ipd_cas_pat_pay_detail)
							{
								$upi_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
							}
						}	
					}
					if($pay_mode=="Credit")
					{
						if($yy==1)
						{
							//echo "<tr><th colspan='9'>$pay_mode</th></tr>";
							$yy++;
						}
						if($pin==$all_pat['opd_id'])
						{
							$all_pat["type"]=2;
						}else
						{
							$pin=$all_pat['opd_id'];
						}
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						if($pat_typ_text["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str $user  $con_max_slno_str_less $con_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $con_pay_det));
							$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
							
							if($pat_pay_detail_num>0)
							{
								$credit_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
							}
						}
						if($pat_typ_text["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='$pay_mode' $date_str  $user  $inv_max_slno_str_less $inv_max_slno_str_grtr"; // AND `date` between '$date1' and '$date2'
							$inv_pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $inv_pay_det));
							$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
							
							if($inv_pat_pay_detail_num>0)
							{
								$credit_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
							}
						}
						if($pat_typ_text["type"]==3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='$pay_mode' $date_str $user $ipd_max_slno_str_less $ipd_max_slno_str_grtr "; // AND `date` between '$date1' and '$date2'
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							if($ipd_cas_pat_pay_detail)
							{
								$credit_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
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
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						if($pat_typ_text["type"]==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' $date_str $user $con_max_slno_str_less $con_max_slno_str_grtr "; //  AND `date` between '$date1' and '$date2'
							$pat_pay_detail_qry=mysqli_query($link, $con_pay_det);
						}
						if($pat_typ_text["type"]==2)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' $date_str $user $inv_max_slno_str_less $inv_max_slno_str_grtr "; //  AND `date` between '$date1' and '$date2'
							$inv_pat_pay_detail_qry=mysqli_query($link, $inv_pay_det);
						}
						if($pat_typ_text["type"]==3)
						{
							$show_info=0;
							$ipd_casual=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' $date_str $user $ipd_max_slno_str_less $ipd_max_slno_str_grtr "; // AND `date` between '$date1' and '$date2'
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						
						$show_info_ipd=0;
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							// Refund Amount
							//~ $daycare_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='3') $date_str_a ")); // a.`date` between '$date1' and '$date2' and
							
							//~ $daycare_refund=$daycare_refund_val["maxref"];
							$daycare_refund=0;
							
							//$daycare_refund=$ipd_cas_pat_pay_detail["refund"];
							
							$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
							$pay_date=$ipd_cas_pat_pay_detail["date"];
							$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
							//$amount=$ipd_cas_pat_pay_detail["amount"];
							$amount=$ipd_cas_pat_pay_detail["amount"]+$daycare_refund;
							
							if($ipd_cas_pat_pay_detail["pay_mode"]=="Cash")
							{
								$show_info_ipd=1;
								$tot_amt+=$amount;
								$tot_amt_cash+=$amount;
								$tot_amt_ipd+=$amount;
							}else if($ipd_cas_pat_pay_detail["pay_mode"]=="Card")
							{
								$card_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
								$show_info_ipd=0;
							}
							else if($ipd_cas_pat_pay_detail["pay_mode"]=="Cheque")
							{
								$cheque_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
								$show_info_ipd=0;
							}
							else if($ipd_cas_pat_pay_detail["pay_mode"]=="NEFT")
							{
								$neft_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
								$show_info_ipd=0;
							}
							else if($ipd_cas_pat_pay_detail["pay_mode"]=="RTGS")
							{
								$rtgs_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
								$show_info_ipd=0;
							}
							else if($ipd_cas_pat_pay_detail["pay_mode"]=="IMPS")
							{
								$imps_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
								$show_info_ipd=0;
							}
							else if($ipd_cas_pat_pay_detail["pay_mode"]=="UPI")
							{
								$upi_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
								$show_info_ipd=0;
							}
							else if($ipd_cas_pat_pay_detail["pay_mode"]=="Credit")
							{
								$credit_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
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
								
								// Refund Amount
								$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
								
								$lab_refund_amount=$lab_refund_val["maxref"];
								
								// Free Amount
								$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_lab from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
								
								$lab_free_amount=$lab_free_val["free_lab"];
								
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
								
								$pay_date=$inv_pat_pay_detail["date"];
								$bill_no=$inv_pat_pay_detail["bill_no"];
								
								$amount=$inv_pat_pay_detail["amount"]+$lab_refund_amount+$lab_free_amount;
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
								}
								else if($inv_pat_pay_detail["payment_mode"]=="Cheque")
								{
									$cheque_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
								}
								else if($inv_pat_pay_detail["payment_mode"]=="NEFT")
								{
									$neft_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
								}
								else if($inv_pat_pay_detail["payment_mode"]=="RTGS")
								{
									$rtgs_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
								}
								else if($inv_pat_pay_detail["payment_mode"]=="IMPS")
								{
									$imps_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
								}
								else if($inv_pat_pay_detail["payment_mode"]=="UPI")
								{
									$upi_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
								}
								else if($inv_pat_pay_detail["payment_mode"]=="Credit")
								{
									$credit_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
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
								
								// Refund Amount
								$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
								
								$opd_refund_amount=$opd_refund_val["maxref_opd"];
								
								// Free Amount
								$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_opd from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
								
								$opd_free_amount=$opd_free_val["free_opd"];
								
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
								
								$pay_date=$pat_pay_detail["date"];
								$bill_no=$pat_pay_detail["bill_no"];
								
								$amount=$pat_pay_detail["amount"]+$opd_refund_amount+$opd_free_amount;
								//$tot_amt+=$amount;
								//$Encounter="Lab";
								
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
								}
								else if($pat_pay_detail["payment_mode"]=="Cheque")
								{
									$cheque_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
								}
								else if($pat_pay_detail["payment_mode"]=="NEFT")
								{
									$neft_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
								}
								else if($pat_pay_detail["payment_mode"]=="RTGS")
								{
									$rtgs_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
								}
								else if($pat_pay_detail["payment_mode"]=="IMPS")
								{
									$imps_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
								}
								else if($pat_pay_detail["payment_mode"]=="UPI")
								{
									$upi_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
								}
								else if($pat_pay_detail["payment_mode"]=="Credit")
								{
									$credit_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
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
					$ipd_casual_card=0;
					
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
						
						// Refund Amount
						$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_refund_amount=$opd_refund_val["maxref_opd"];
						
						// Free Amount
						$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_opd from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_free_amount=$opd_free_val["free_opd"];
						
						$amount=$pat_pay_detail["amount"]+$opd_refund_amount+$opd_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
						$pay_date=$pat_pay_detail["date"];
						$bill_no=$pat_pay_detail["bill_no"];
						//$amount=$pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_card+=$amount;
						//$Encounter="OPD";
					}
					if($card_type==2)
					{
						$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$card_bill' ";
						$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
						
						// Refund Amount
						$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_refund_amount=$lab_refund_val["maxref"];
						
						// Free Amount
						$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_lab from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_free_amount=$lab_free_val["free_lab"];
						
						$amount=$inv_pat_pay_detail["amount"]+$lab_refund_amount+$lab_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
						$pay_date=$inv_pat_pay_detail["date"];
						$bill_no=$inv_pat_pay_detail["bill_no"];
						//$amount=$inv_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_card+=$amount;
						//$Encounter="Lab";
					}
					if($card_type==3)
					{
						$ipd_casual_card=1;
						$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$card_pin' AND `bill_no`='$card_bill' ";
						$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
						
					}
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
					$Encounter=$pat_typ_text['p_type'];
					
					while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
					{
						// Refund Amount
						//~ $daycare_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='5' "));
						//~ $daycare_refund=$daycare_refund_val["maxref"];
						$daycare_refund=0;
						
						//$daycare_refund=$ipd_cas_pat_pay_detail["refund"];
						
						$amount=$ipd_cas_pat_pay_detail["amount"]+$daycare_refund;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
						$pay_date=$ipd_cas_pat_pay_detail["date"];
						$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
						//$amount=$ipd_cas_pat_pay_detail["amount"];
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
		if($cheque_pin)
		{
			//echo $cheque_pin."<br>";
			echo "<tr><th colspan='9'>Cheque</th></tr>";
			$same_card_pin="";
			$cheque_pins=explode("@@",$cheque_pin);
			foreach($cheque_pins as $cheque_pinn)
			{
				if($cheque_pinn)
				{
					$ipd_casual_card=0;
					
					$cheque_pinn=explode("##",$cheque_pinn);
					$cheque_pin=$cheque_pinn[0];
					$cheque_bill=$cheque_pinn[1];
					$cheque_type=$cheque_pinn[2];
					//echo $cheque_pin."<br>";
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$cheque_pin' "));
					$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
					$uhid_id=$pat_info["patient_id"];
					
					if($cheque_type==1)
					{
						$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$cheque_bill' ";
						$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
						
						// Refund Amount
						$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_refund_amount=$opd_refund_val["maxref_opd"];
						
						// Free Amount
						$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_opd from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_free_amount=$opd_free_val["free_opd"];
						
						$amount=$pat_pay_detail["amount"]+$opd_refund_amount+$opd_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
						$pay_date=$pat_pay_detail["date"];
						$bill_no=$pat_pay_detail["bill_no"];
						//$amount=$pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_cheque+=$amount;
						//$Encounter="OPD";
					}
					if($cheque_type==2)
					{
						$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$cheque_bill' ";
						$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
						
						// Refund Amount
						$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_refund_amount=$lab_refund_val["maxref"];
						
						// Free Amount
						$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_lab from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_free_amount=$lab_free_val["free_lab"];
						
						$amount=$inv_pat_pay_detail["amount"]+$lab_refund_amount+$lab_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
						$pay_date=$inv_pat_pay_detail["date"];
						$bill_no=$inv_pat_pay_detail["bill_no"];
						//$amount=$inv_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_cheque+=$amount;
						//$Encounter="Lab";
					}
					if($cheque_type==3)
					{
						$ipd_casual_card=1;
						$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$cheque_pin' AND `bill_no`='$cheque_bill' ";
						$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
						
					}
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
					$Encounter=$pat_typ_text['p_type'];
					
					while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
					{
						// Refund Amount
						//~ $daycare_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='5' "));
						//~ $daycare_refund=$daycare_refund_val["maxref"];
						$daycare_refund=0;
						
						//$daycare_refund=$ipd_cas_pat_pay_detail["refund"];
						
						$amount=$ipd_cas_pat_pay_detail["amount"]+$daycare_refund;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
						$pay_date=$ipd_cas_pat_pay_detail["date"];
						$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
						//$amount=$ipd_cas_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_cheque+=$amount;
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
		if($neft_pin)
		{
			//echo $neft_pin."<br>";
			echo "<tr><th colspan='9'>NEFT</th></tr>";
			$same_card_pin="";
			$neft_pins=explode("@@",$neft_pin);
			foreach($neft_pins as $neft_pinn)
			{
				if($neft_pinn)
				{
					$ipd_casual_card=0;
					
					$neft_pinn=explode("##",$neft_pinn);
					$neft_pin=$neft_pinn[0];
					$neft_bill=$neft_pinn[1];
					$neft_type=$neft_pinn[2];
					//echo $neft_pin."<br>";
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$neft_pin' "));
					$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
					$uhid_id=$pat_info["patient_id"];
					
					if($neft_type==1)
					{
						$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$neft_bill' ";
						$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
						
						// Refund Amount
						$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_refund_amount=$opd_refund_val["maxref_opd"];
						
						// Free Amount
						$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_opd from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_free_amount=$opd_free_val["free_opd"];
						
						$amount=$pat_pay_detail["amount"]+$opd_refund_amount+$opd_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
						$pay_date=$pat_pay_detail["date"];
						$bill_no=$pat_pay_detail["bill_no"];
						//$amount=$pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_neft+=$amount;
						//$Encounter="OPD";
					}
					if($neft_type==2)
					{
						$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$neft_bill' ";
						$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
						
						// Refund Amount
						$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_refund_amount=$lab_refund_val["maxref"];
						
						// Free Amount
						$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_lab from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_free_amount=$lab_free_val["free_lab"];
						
						$amount=$inv_pat_pay_detail["amount"]+$lab_refund_amount+$lab_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
						$pay_date=$inv_pat_pay_detail["date"];
						$bill_no=$inv_pat_pay_detail["bill_no"];
						//$amount=$inv_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_neft+=$amount;
						//$Encounter="Lab";
					}
					if($neft_type==3)
					{
						$ipd_casual_card=1;
						$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$neft_pin' AND `bill_no`='$neft_bill' ";
						$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
						
					}
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
					$Encounter=$pat_typ_text['p_type'];
					
					while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
					{
						// Refund Amount
						//~ $daycare_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='5' "));
						//~ $daycare_refund=$daycare_refund_val["maxref"];
						$daycare_refund=0;
						
						//$daycare_refund=$ipd_cas_pat_pay_detail["refund"];
						
						$amount=$ipd_cas_pat_pay_detail["amount"]+$daycare_refund;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
						$pay_date=$ipd_cas_pat_pay_detail["date"];
						$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
						//$amount=$ipd_cas_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_neft+=$amount;
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
		if($rtgs_pin)
		{
			//echo $rtgs_pin."<br>";
			echo "<tr><th colspan='9'>RTGS</th></tr>";
			$same_card_pin="";
			$rtgs_pins=explode("@@",$rtgs_pin);
			foreach($rtgs_pins as $rtgs_pinn)
			{
				if($rtgs_pinn)
				{
					$ipd_casual_card=0;
					
					$rtgs_pinn=explode("##",$rtgs_pinn);
					$rtgs_pin=$rtgs_pinn[0];
					$rtgs_bill=$rtgs_pinn[1];
					$rtgs_type=$rtgs_pinn[2];
					//echo $rtgs_pin."<br>";
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$rtgs_pin' "));
					$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
					$uhid_id=$pat_info["patient_id"];
					
					if($rtgs_type==1)
					{
						$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$rtgs_bill' ";
						$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
						
						// Refund Amount
						$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_refund_amount=$opd_refund_val["maxref_opd"];
						
						// Free Amount
						$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_opd from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_free_amount=$opd_free_val["free_opd"];
						
						$amount=$pat_pay_detail["amount"]+$opd_refund_amount+$opd_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
						$pay_date=$pat_pay_detail["date"];
						$bill_no=$pat_pay_detail["bill_no"];
						//$amount=$pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_rtgs+=$amount;
						//$Encounter="OPD";
					}
					if($rtgs_type==2)
					{
						$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$rtgs_bill' ";
						$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
						
						// Refund Amount
						$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_refund_amount=$lab_refund_val["maxref"];
						
						// Free Amount
						$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_lab from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_free_amount=$lab_free_val["free_lab"];
						
						$amount=$inv_pat_pay_detail["amount"]+$lab_refund_amount+$lab_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
						$pay_date=$inv_pat_pay_detail["date"];
						$bill_no=$inv_pat_pay_detail["bill_no"];
						//$amount=$inv_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_rtgs+=$amount;
						//$Encounter="Lab";
					}
					if($rtgs_type==3)
					{
						$ipd_casual_card=1;
						$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$rtgs_pin' AND `bill_no`='$rtgs_bill' ";
						$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
						
					}
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
					$Encounter=$pat_typ_text['p_type'];
					
					while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
					{
						// Refund Amount
						//~ $daycare_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='5' "));
						//~ $daycare_refund=$daycare_refund_val["maxref"];
						$daycare_refund=0;
						
						//$daycare_refund=$ipd_cas_pat_pay_detail["refund"];
						
						$amount=$ipd_cas_pat_pay_detail["amount"]+$daycare_refund;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
						$pay_date=$ipd_cas_pat_pay_detail["date"];
						$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
						//$amount=$ipd_cas_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_rtgs+=$amount;
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
		if($imps_pin)
		{
			//echo $imps_pin."<br>";
			echo "<tr><th colspan='9'>IMPS</th></tr>";
			$same_card_pin="";
			$imps_pins=explode("@@",$imps_pin);
			foreach($imps_pins as $imps_pinn)
			{
				if($imps_pinn)
				{
					$ipd_casual_card=0;
					
					$imps_pinn=explode("##",$imps_pinn);
					$imps_pin=$imps_pinn[0];
					$imps_bill=$imps_pinn[1];
					$imps_type=$imps_pinn[2];
					//echo $imps_pin."<br>";
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$imps_pin' "));
					$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
					$uhid_id=$pat_info["patient_id"];
					
					if($imps_type==1)
					{
						$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$imps_bill' ";
						$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
						
						// Refund Amount
						$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_refund_amount=$opd_refund_val["maxref_opd"];
						
						// Free Amount
						$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_opd from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_free_amount=$opd_free_val["free_opd"];
						
						$amount=$pat_pay_detail["amount"]+$opd_refund_amount+$opd_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
						$pay_date=$pat_pay_detail["date"];
						$bill_no=$pat_pay_detail["bill_no"];
						//$amount=$pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_imps+=$amount;
						//$Encounter="OPD";
					}
					if($imps_type==2)
					{
						$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$imps_bill' ";
						$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
						
						// Refund Amount
						$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_refund_amount=$lab_refund_val["maxref"];
						
						// Free Amount
						$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_lab from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_free_amount=$lab_free_val["free_lab"];
						
						$amount=$inv_pat_pay_detail["amount"]+$lab_refund_amount+$lab_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
						$pay_date=$inv_pat_pay_detail["date"];
						$bill_no=$inv_pat_pay_detail["bill_no"];
						//$amount=$inv_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_imps+=$amount;
						//$Encounter="Lab";
					}
					if($imps_type==3)
					{
						$ipd_casual_card=1;
						$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$imps_pin' AND `bill_no`='$imps_bill' ";
						$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
						
					}
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
					$Encounter=$pat_typ_text['p_type'];
					
					while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
					{
						// Refund Amount
						//~ $daycare_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='5' "));
						//~ $daycare_refund=$daycare_refund_val["maxref"];
						$daycare_refund=0;
						
						//$daycare_refund=$ipd_cas_pat_pay_detail["refund"];
						
						$amount=$ipd_cas_pat_pay_detail["amount"]+$daycare_refund;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
						$pay_date=$ipd_cas_pat_pay_detail["date"];
						$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
						//$amount=$ipd_cas_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_imps+=$amount;
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
		if($upi_pin)
		{
			//echo $upi_pin."<br>";
			echo "<tr><th colspan='9'>UPI</th></tr>";
			$same_card_pin="";
			$upi_pins=explode("@@",$upi_pin);
			foreach($upi_pins as $upi_pinn)
			{
				if($upi_pinn)
				{
					$ipd_casual_card=0;
					
					$upi_pinn=explode("##",$upi_pinn);
					$upi_pin=$upi_pinn[0];
					$upi_bill=$upi_pinn[1];
					$upi_type=$upi_pinn[2];
					//echo $upi_pin."<br>";
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$upi_pin' "));
					$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
					$uhid_id=$pat_info["patient_id"];
					
					if($upi_type==1)
					{
						$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$upi_bill' ";
						$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
						
						// Refund Amount
						$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_refund_amount=$opd_refund_val["maxref_opd"];
						
						// Free Amount
						$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_opd from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_free_amount=$opd_free_val["free_opd"];
						
						$amount=$pat_pay_detail["amount"]+$opd_refund_amount+$opd_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
						$pay_date=$pat_pay_detail["date"];
						$bill_no=$pat_pay_detail["bill_no"];
						//$amount=$pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_upi+=$amount;
						//$Encounter="OPD";
					}
					if($upi_type==2)
					{
						$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$upi_bill' ";
						$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
						
						// Refund Amount
						$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_refund_amount=$lab_refund_val["maxref"];
						
						// Free Amount
						$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_lab from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_free_amount=$lab_free_val["free_lab"];
						
						$amount=$inv_pat_pay_detail["amount"]+$lab_refund_amount+$lab_free_amount;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
						$pay_date=$inv_pat_pay_detail["date"];
						$bill_no=$inv_pat_pay_detail["bill_no"];
						//$amount=$inv_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_upi+=$amount;
						//$Encounter="Lab";
					}
					if($upi_type==3)
					{
						$ipd_casual_card=1;
						$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$upi_pin' AND `bill_no`='$upi_bill' ";
						$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
						
					}
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
					$Encounter=$pat_typ_text['p_type'];
					
					while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
					{
						// Refund Amount
						//~ $daycare_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='5' "));
						//~ $daycare_refund=$daycare_refund_val["maxref"];
						$daycare_refund=0;
						
						//$daycare_refund=$ipd_cas_pat_pay_detail["refund"];
						
						$amount=$ipd_cas_pat_pay_detail["amount"]+$daycare_refund;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
						$pay_date=$ipd_cas_pat_pay_detail["date"];
						$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
						//$amount=$ipd_cas_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_upi+=$amount;
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
		if($credit_pin)
		{
			//echo $card_pin."<br>";
			echo "<tr><th colspan='9'>Credit</th></tr>";
			$same_card_pin="";
			$credit_pins=explode("@@",$credit_pin);
			foreach($credit_pins as $credit_pinn)
			{
				if($credit_pinn)
				{
					$ipd_casual_credit=0;
					
					$credit_pinn=explode("##",$credit_pinn);
					$credit_pin=$credit_pinn[0];
					$credit_bill=$credit_pinn[1];
					$credit_type=$credit_pinn[2];
					//echo $credit_pin."<br>";
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$credit_pin' "));
					$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
					$uhid_id=$pat_info["patient_id"];
					
					if($credit_type==1)
					{
						$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$credit_bill' ";
						$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
						
						$pat_balance=$pat_pay_detail["balance"];
						
						$patient_payment_detail=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]'"));
						
						//~ $pat_balance=$patient_payment_detail["balance"];
						
						// Refund Amount
						$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_refund_amount=$opd_refund_val["maxref_opd"];
						
						// Free Amount
						$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_opd from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1') "));
						
						$opd_free_amount=$opd_free_val["free_opd"];
						
						$balance_recv_check=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(amount),0) as bal_recvd FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$credit_pin' AND `typeofpayment`='B' $date_str ")); // $con_max_slno_str_less $con_max_slno_str_grtr $user
						$tot_balance_recvd=$balance_recv_check["bal_recvd"];
						
						//~ $amount=$pat_pay_detail["amount"]+$opd_refund_amount+$opd_free_amount+$pat_balance;
						$amount=$pat_balance-$tot_balance_recvd;
						
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
						$pay_date=$pat_pay_detail["date"];
						$bill_no=$pat_pay_detail["bill_no"];
						//$amount=$pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_credit+=$amount;
						//$Encounter="OPD";
					}
					if($credit_type==2)
					{
						$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$credit_bill' ";
						$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
						
						$patient_payment_detail=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]'"));
						//~ $pat_balance=$patient_payment_detail["balance"];
						
						$pat_balance=$inv_pat_pay_detail["balance"];
						
						// Refund Amount
						$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_refund_amount=$lab_refund_val["maxref"];
						
						// Free Amount
						$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_lab from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2') "));
						
						$lab_free_amount=$lab_free_val["free_lab"];
						
						$balance_recv_check=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(amount),0) as bal_recvd FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$credit_pin' AND `typeofpayment`='B' $date_str ")); // $inv_max_slno_str_less $inv_max_slno_str_grtr $user
						$tot_balance_recvd=$balance_recv_check["bal_recvd"];
						
						//~ $amount=$inv_pat_pay_detail["amount"]+$lab_refund_amount+$lab_free_amount+$pat_balance-$tot_balance_recvd;
						$amount=$pat_balance-$tot_balance_recvd;
						
						if($amount<0)
						{
							$amount=0;
						}
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
						$pay_date=$inv_pat_pay_detail["date"];
						$bill_no=$inv_pat_pay_detail["bill_no"];
						//$amount=$inv_pat_pay_detail["amount"];
						$tot_amt+=$amount;
						$tot_amt_credit+=$amount;
						//$Encounter="Lab";
					}
					if($credit_type==3)
					{
						$ipd_casual_credit=1;
						$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$credit_pin' AND `bill_no`='$credit_bill' ";
						$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
						
					}
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
					$Encounter=$pat_typ_text['p_type'];
					
					while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
					{
						$balance_recv_check=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(amount),0) as bal_recvd, ifnull(sum(discount),0) as bal_discount FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$credit_pin' AND `pay_type`='Balance' $date_str  ")); // $ipd_max_slno_str_less $ipd_max_slno_str_grtr $user
						
						$tot_balance_recvd=$balance_recv_check["bal_recvd"];
						$tot_balance_discount=$balance_recv_check["bal_discount"];
						
						// Refund Amount
						//~ $daycare_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='5' "));
						//~ $daycare_refund=$daycare_refund_val["maxref"];
						$daycare_refund=0;
						
						//~ $daycare_refund=$ipd_cas_pat_pay_detail["refund"];
						
						$pat_balance=$ipd_cas_pat_pay_detail["balance"];
						
						//~ $amount=$ipd_cas_pat_pay_detail["amount"]+$daycare_refund+$pat_balance-$tot_balance_recvd;
						$amount=$pat_balance-$tot_balance_recvd-$tot_balance_discount;
						
						if($amount<0)
						{
							$amount=0;
						}
						if($amount>0)
						{
							$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
							$pay_date=$ipd_cas_pat_pay_detail["date"];
							$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
							//$amount=$ipd_cas_pat_pay_detail["amount"];
							$tot_amt+=$amount;
							$tot_amt_credit+=$amount;
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
					}
					if($ipd_casual_credit==0)
					{
						if($amount>0)
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
		}
		$encounter_strs_b="";
		
		$encounter_strs_b_1="  and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='1')";
		$encounter_strs_b_2="  and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='2')";
		$encounter_strs_b_3="  and b.type IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='3')";
		
		if($encounter>0)
		{
			$encounter_strs_b_1.=" and b.type='$encounter' ";
			$encounter_strs_b_2.=" and b.type='$encounter' ";
			$encounter_strs_b_3.=" and b.type='$encounter' ";
		}
		
		if($user_entry==0)
		{
			if($encounter==0 || $encounter_pay_type==3)
			{
				$ipd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund),0) as maxref from ipd_advance_payment_details a, uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`ipd_id`=b.`opd_id` and a.`date` between '$date11' and '$date22' $encounter_strs_b_3 "));
			}
			
			if($encounter==0 || $encounter_pay_type==2)
			{
				$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expensedetail where entry_date between '$date11' and '$date22'"));
				// Lab Refund
				$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` $encounter_strs_b_2 "));
				// Lab Free
				$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as lab_free from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` $encounter_strs_b_2 "));
			}
			
			if($encounter==0 || $encounter_pay_type==1)
			{
				// OPD Refund
				$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` $encounter_strs_b_1 "));
				// OPD Free
				$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as opd_free from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` $encounter_strs_b_1 "));
			}
		}
		else
		{
			if($encounter==0 || $encounter_pay_type==3)
			{
				$encounter_strs="";
				if($encounter>0)
				{
					$encounter_strs=" and ipd_id IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$encounter')";
				}
				
				$ipd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund),0) as maxref from ipd_advance_payment_details where user='$user_entry' $date_str $ipd_max_slno_str_less $ipd_max_slno_str_grtr $encounter_strs")); // `date` between '$date11' and '$date22'
				
			}
			$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expensedetail where slno>0 $exp_max_slno_str_less $exp_max_slno_str_grtr $date_str_exp and `user`='$user_entry' ")); // entry_date between '$date11' and '$date22'
			
			if($encounter==0 || $encounter_pay_type==2)
			{
				// Lab Refund
				$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`user`='$user_entry' $ref_max_slno_str_less $ref_max_slno_str_grtr $date_str_a $encounter_strs_b_2 ")); // a.`date` between '$date11' and '$date22' and
				
				// Lab Free
				$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as lab_free from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.`user`='$user_entry' $fre_max_slno_str_less $fre_max_slno_str_grtr $date_str_a $encounter_strs_b_2 ")); // a.`date` between '$date11' and '$date22' and
			}
			
			if($encounter==0 || $encounter_pay_type==1)
			{
				// OPD Refund
				$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.slno>0 and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`user`='$user_entry' $ref_max_slno_str_less $ref_max_slno_str_grtr $date_str_a $encounter_strs_b_1 "));
				// `date` between '$date11' and '$date22'
				
				// OPD Free
				$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as opd_free from invest_payment_free a , uhid_and_opdid b where a.slno>0 and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.`user`='$user_entry' $fre_max_slno_str_less $fre_max_slno_str_grtr $date_str_a $encounter_strs_b_1"));
			}
		}
		$tot_expense=$tot_expense_qry["tot_exp"];
		
		$opd_refund=$opd_refund_val["maxref_opd"];
		$lab_refund=$lab_refund_val["maxref"];
		$total_refund_amount=$opd_refund+$lab_refund;
		
		// Free
		$opd_free_amount=$opd_free_val["opd_free"];
		$lab_free_amount=$lab_free_val["lab_free"];
		$total_free_amount=$opd_free_amount+$lab_free_amount;
		
		$ipd_refund=$ipd_refund_val["maxref"];
		
		if($pay_mode=="0")
		{
		?>
			<tr>
				<th colspan="5"><span class="text-right">Total Cash Amount</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_cash,2); ?></td>
			</tr>
		<?php
			if($card_pin)
			{
		?>
		<tr>
			<th colspan="5"><span class="text-right">Total Card Amount</span></th>
			<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_card,2); ?></td>
		</tr>
		<?php }
			if($cheque_pin)
			{
		?>
		<tr>
			<th colspan="5"><span class="text-right">Total Cheque Amount</span></th>
			<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_cheque,2); ?></td>
		</tr>
		<?php }
			if($neft_pin)
			{
		?>
		<tr>
			<th colspan="5"><span class="text-right">Total NEFT Amount</span></th>
			<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_neft,2); ?></td>
		</tr>
		<?php }
			if($rtgs_pin)
			{
		?>
		<tr>
			<th colspan="5"><span class="text-right">Total RTGS Amount</span></th>
			<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_rtgs,2); ?></td>
		</tr>
		<?php }
			if($imps_pin)
			{
		?>
		<tr>
			<th colspan="5"><span class="text-right">Total IMPS Amount</span></th>
			<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_imps,2); ?></td>
		</tr>
		<?php }
			if($upi_pin)
			{
		?>
		<tr>
			<th colspan="5"><span class="text-right">Total UPI Amount</span></th>
			<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_upi,2); ?></td>
		</tr>
		<?php }
			if($credit_pin)
			{
		?>
		<tr>
			<th colspan="5"><span class="text-right">Total Credit Amount</span></th>
			<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_credit,2); ?></td>
		</tr>
		<?php } } ?>
		<?php
		if($pay_mode=="0")
		{
		?>
			<!--<tr>
				<th colspan="5"><span class="text-right">Total Cash Amount</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_cash,2); ?></td>
			</tr>-->
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
				<td colspan="3"><?php echo $rupees_symbol.number_format($ipd_refund+$total_refund_amount,2); ?></td>
			</tr>
			<tr>
				<th colspan="5"><span class="text-right">Total Free</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($total_free_amount,2); ?></td>
			</tr>
			<tr>
				<th colspan="5"><span class="text-right">Net Amount</span></th>
				
				<td colspan="3"><?php echo $rupees_symbol.number_format(($tot_amt-$tot_expense-$ipd_refund-$total_refund_amount-$total_free_amount-$tot_amt_credit),2); ?></td>
			</tr>
			<tr>
				<th colspan="5"><span class="text-right">Net Cash</span></th>
				
				<td colspan="3"><?php echo $rupees_symbol.number_format(($tot_amt-$tot_expense-$ipd_refund-$total_refund_amount-$total_free_amount-$tot_amt_card-$tot_amt_cheque-$tot_amt_neft-$tot_amt_rtgs-$tot_amt_imps-$tot_amt_upi-$tot_amt_credit),2); ?></td>
			</tr>
<?php
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
		<thead class="table_header_fix">
			<tr>
				<th width="5%">#</th>
				<th class="ipd_serial" width="5%">Sl No.</th>
				<th width="10%">Bill No</th>
				<th width="10%">Name</th>
				<th width="10%">Doctor</th>
				<th width="10%">date</th>
				<th width="50%">Test Details</th>
			</tr>
		</thead>
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
		<thead class="table_header_fix">
			<tr>
				<th >#</th>
				<th >User</th>
				<th >Amount</th>
			</tr>
		</thead>
		<?php
				$i=1;
				
				$qexpense=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxexp from expensedetail where `entry_date` between '$date1' and '$date2' "));
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
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Bill No</th>
				<!--<th>Bill No</th>-->
				<th>Name</th>
				<th>Cancel date</th>
				<th><span class="text-right">Bill Amount</span></th>
				<th><span class="text-right">Reason</span></th>
				<th><span class="text-right">User</span></th>
				<th><span class="text-right">Encounter</span></th>
			</tr>
		</thead>
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
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th class="ipd_serial">Sl No.</th>
				<th>Bill No</th>
				<!--<th>Bill No</th>-->
				<th>Name</th>
				<th>date</th>
				<th><span class="text-right">Free Amount</span></th>
				<th><span class="text-right">Reason</span></th>
				<th><span class="text-right">User</span></th>
				<th><span class="text-right">Encounter</span></th>
			</tr>
		</thead>
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
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Bill No</th>
				<th>Bill No</th>
				<th>Name</th>
				<th>Cancel date</th>
				<th><span class="text-right">Bill Amount</span></th>
				<th><span class="text-right">Discount</span></th>
				<th><span class="text-right">Reason</span></th>
				<th><span class="text-right">User</span></th>
				<th><span class="text-right">Encounter</span></th>
			</tr>
		</thead>
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
		$q=mysqli_query($link,"SELECT * FROM `expensedetail`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `expensedetail` WHERE `entry_date` BETWEEN '$date1' AND '$date2' $user");
	}
	
?>
	<p style="margin-top: 2%;"><b>Daily Expense Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/daily_expense_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&user_entry=<?php echo $user_entry;?>">Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('expense','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<!--<th>Details</th>-->
				<th>Description</th>
				<th>Amount</th>
				<th>Date</th>
				<th>User</th>
			</tr>
		</thead>
		<?php
		$i=1;
		$tot=0;
		while($r=mysqli_fetch_array($q))
		{
			$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
			$c=mysqli_fetch_array(mysqli_query($link, " SELECT `cat_name` FROM `category_master` WHERE `cat_id`='$r[details]' "));
			$tot=$tot+$r["Amount"];
		?>
		<tr>
			<td><?php echo $i;?></td>
			<!--<td><?php echo $c["cat_name"]; ?></td>-->
			<td><?php echo $r["description"]; ?></td>
			<td><?php echo $rupees_symbol.number_format($r["Amount"],2); ?></td>
			<td><?php echo convert_date($r["entry_date"]); ?></td>
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
	$encounter_val=$_POST['encounter'];
	$user_entry=$_POST['user_entry'];
	$pay_mode=$_POST['pay_mode'];
	
?>
	<p id="print_div" style="margin-top: 2%;"><b>Account Summary Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<!--<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/account_summary_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>&pay_mode=<?php echo $pay_mode;?>"><i class="icon-file icon-large"></i> Export to Excel</a></span>-->
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('summary','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','<?php echo $pay_mode;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Bill Amout</th>
				<th>Discount</th>
				<th>Paid</th>
				<th>Balance</th>
				<th>User</th>
				<th>Encounter</th>
				<th>Date</th>
			</tr>
		</thead>
<?php
	$n=1;
	$tot_bill=$tot_dis=$tot_paid=$tot_bal=0;
	
	$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter_val' "));
	$pat_typ_encounter=$pat_typ["type"];
	
	$encounter_val_str_ipd=" AND `type`='3' ";
	if($encounter_val>0)
	{
		$encounter_val_str=" AND b.`type`='$encounter_val' ";
	}
	$user_str="";
	if($user_entry>0)
	{
		$user_str=" AND a.`user`='$user_entry' ";
		$user_str_ipd=" AND `user`='$user_entry' ";
	}
	
	if($encounter_val=='0' || $pat_typ_encounter=='1')
	{
		$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `consult_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
		while($con_bal=mysqli_fetch_array($con_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$paid=$con_bal['amount'];
			
			$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
			
			$bill_amt=$con_pat_pay['tot_amount'];
			$discount=$con_pat_pay['dis_amt'];
			$balance=$con_pat_pay['balance'];
			
			// Check Refund
			$con_pat_refund=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_refund where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]' "));
			
			if($con_pat_refund)
			{
				$paid+=$con_pat_refund["refund_amount"];
			}
			// Check Free
			$con_pat_free=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_free where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]' "));
			
			if($con_pat_free)
			{
				$paid+=$con_pat_free["free_amount"];
			}
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $con_bal["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($con_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
		}
	}
	if($encounter_val=='0' || $pat_typ_encounter=='2')
	{
		$inv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str");
		while($inv_bal=mysqli_fetch_array($inv_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$paid=$inv_bal['amount'];
			
			$inv_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$inv_bal[patient_id]' and opd_id='$inv_bal[opd_id]'"));
			
			$bill_amt=$inv_pat_pay['tot_amount'];
			$discount=$inv_pat_pay['dis_amt'];
			$balance=$inv_pat_pay['balance'];
			
			// Check Refund
			$con_pat_refund=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_refund where patient_id='$inv_bal[patient_id]' and opd_id='$inv_bal[opd_id]' "));
			
			if($con_pat_refund)
			{
				$paid+=$con_pat_refund["refund_amount"];
				$bill_amt+=$con_pat_refund["refund_amount"];
			}
			// Check Free
			$con_pat_free=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_free where patient_id='$inv_bal[patient_id]' and opd_id='$inv_bal[opd_id]' "));
			
			if($con_pat_free)
			{
				$paid+=$con_pat_free["free_amount"];
			}
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$inv_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$inv_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $inv_bal["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($inv_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
		}
	}
	if($encounter_val=='0' || $encounter_val=='4' || $encounter_val=='5' || $encounter_val=='8')
	{
		if($encounter_val=='0')
		{
			$encounter_val_str_other_ipd=" AND (b.`type`='4' OR b.`type`='5' OR b.`type`='8') "; // Casulty or Daycare or Baby
		}
		if($encounter_val==4)
		{
			$encounter_val_str_other_ipd=" AND b.`type`='4' "; // Casulty
		}
		if($encounter_val==5)
		{
			$encounter_val_str_other_ipd=" AND b.`type`='5' "; // Daycare
		}
		if($encounter_val==8)
		{
			$encounter_val_str_other_ipd=" AND b.`type`='8' "; // Baby
		}
		
		$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str_other_ipd $user_str ");
		while($adv_bal=mysqli_fetch_array($adv_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			if($adv_bal["type"]==3)
			{
				$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			}
			
			// Baby's Charge
			$baby_serv_tot=0;
			$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]' "));
			if($delivery_check)
			{
				$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_serv_tot=$baby_tot_serv["tots"];
				
				// OT Charge Baby
				$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_ot_total=$baby_ot_tot_val["g_tot"];
				
				$tot_baby_bill=$baby_serv_tot+$baby_ot_total;
				
			}
			
			$bill_amt=$tot_serv['sum_tot_amt']+$tot_serv_ot['sum_tot_amt']+$tot_baby_bill;
			
			$tot_amountt_pay=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS sum_paid FROM `ipd_advance_payment_details` WHERE `slno`='$adv_bal[slno]'"));
			$paid=$tot_amountt_pay['sum_paid'];
			
			$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			$discount=$tot_amountt['sum_dis'];
			$paidd=$tot_amountt['sum_paid'];
			$balance=($bill_amt-$discount-$paidd);
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $adv_bal["ipd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($adv_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
		}
	}
	
	// Balance Received
	
	//echo "<tr><th colspan='10'>Balance Received</th></tr>";
	
	if($encounter_val=='0' || $pat_typ_encounter=='1')
	{
		$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `consult_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
		$con_bal_num=mysqli_num_rows($con_bal_qry);
		$zz=0;
		if($con_bal_num>0)
		{
			echo "<tr><th colspan='10'>Balance Received</th></tr>";
			$zz=1;
		}
		while($con_bal=mysqli_fetch_array($con_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$paid=$con_bal['amount'];
			
			$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
			
			$bill_amt=$con_pat_pay['tot_amount'];
			$discount=$con_pat_pay['dis_amt'];
			$balance=$con_pat_pay['balance'];
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $con_bal["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($con_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			//~ $tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			//~ $tot_bal=$tot_bal+$balance;
		}
	}
	if($encounter_val=='0' || $pat_typ_encounter=='2')
	{
		$inv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
		
		if($zz==0)
		{
			$inv_bal_num=mysqli_num_rows($inv_bal_qry);
			if($inv_bal_num>0)
			{
				$zz=1;
				echo "<tr><th colspan='10'>Balance Received</th></tr>";
			}
		}
		
		while($inv_bal=mysqli_fetch_array($inv_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$paid=$inv_bal['amount'];
			
			$inv_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$inv_bal[patient_id]' and opd_id='$inv_bal[opd_id]'"));
			
			$bill_amt=$inv_pat_pay['tot_amount'];
			$discount=$inv_pat_pay['dis_amt'];
			$balance=$inv_pat_pay['balance'];
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$inv_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$inv_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $inv_bal["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($inv_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			//~ $tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			//~ $tot_bal=$tot_bal+$balance;
		}
	}
	if($encounter_val=='0' || $encounter_val=='4' || $encounter_val=='5' || $encounter_val=='8')
	{
		if($encounter_val=='0')
		{
			$encounter_val_str_other_ipd=" AND (b.`type`='4' OR b.`type`='5') "; // Casulty or Daycare or Baby
		}
		if($encounter_val==4)
		{
			$encounter_val_str_other_ipd=" AND b.`type`='4' "; // Casulty
		}
		if($encounter_val==5)
		{
			$encounter_val_str_other_ipd=" AND b.`type`='5' "; // Daycare
		}
		if($encounter_val==8)
		{
			$encounter_val_str_other_ipd=" AND b.`type`='8' "; // Baby
		}
		
		$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str_other_ipd $user_str");
		
		if($zz==0)
		{
			$adv_bal_num=mysqli_num_rows($adv_bal_qry);
			if($adv_bal_num>0)
			{
				$zz=1;
				echo "<tr><th colspan='10'>Balance Received</th></tr>";
			}
		}
		
		while($adv_bal=mysqli_fetch_array($adv_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			if($adv_bal["type"]==3)
			{
				$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
				
			}
			
			// Baby's Charge
			$baby_serv_tot=0;
			$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]' "));
			if($delivery_check)
			{
				$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_serv_tot=$baby_tot_serv["tots"];
				
				// OT Charge Baby
				$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_ot_total=$baby_ot_tot_val["g_tot"];
				
				$tot_baby_bill=$baby_serv_tot+$baby_ot_total;
				
			}
			
			$bill_amt=$tot_serv['sum_tot_amt']+$tot_serv_ot['sum_tot_amt']+$tot_baby_bill;
			
			$tot_amountt_pay=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS sum_paid FROM `ipd_advance_payment_details` WHERE `slno`='$adv_bal[slno]'"));
			$paid=$tot_amountt_pay['sum_paid'];
			
			$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			$discount=$tot_amountt['sum_dis'];
			$paidd=$tot_amountt['sum_paid'];
			$balance=($bill_amt-$discount-$paidd);
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $adv_bal["ipd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($adv_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			//~ $tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			//~ $tot_bal=$tot_bal+$balance;
		}
	}
	
?>
		<tr>
			<td colspan="2"></td>
			<td colspan=""><span class="text-right"><strong>Total:</strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bill),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_dis),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bal),2);?> </strong></span></td>
			<td colspan="4">&nbsp;</td>
		</tr>
	</table>
<?php
	if($encounter_val=='0' || $encounter_val=='3')
	{
?>
	<table class="table">
		<tr>
			<th colspan="10">IPD Accounts</th>
		</tr>
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Bill Amount</th>
			<th>Discount</th>
			<th>Paid Amount</th>
			<th>Payment Type</th>
			<th>Balance</th>
			<th>Payment Time</th>
			<th>User</th>
		</tr>
<?php
		
		$ipd_pat_reg_qry=mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' $encounter_val_str_ipd ");
		while($ipd_pat_reg=mysqli_fetch_array($ipd_pat_reg_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$ipd_pat_reg[patient_id]' AND `ipd_id`='$ipd_pat_reg[opd_id]' "));
			
			$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$ipd_pat_reg[patient_id]' AND `ipd_id`='$ipd_pat_reg[opd_id]' "));
			
			// Baby's Charge
			$baby_serv_tot=0;
			$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$ipd_pat_reg[patient_id]' AND `ipd_id`='$ipd_pat_reg[opd_id]' "));
			if($delivery_check)
			{
				$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_serv_tot=$baby_tot_serv["tots"];
				
				// OT Charge Baby
				$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_ot_total=$baby_ot_tot_val["g_tot"];
				
				$tot_baby_bill=$baby_serv_tot+$baby_ot_total;
				
			}
			
			$bill_amt=$tot_serv['sum_tot_amt']+$tot_serv_ot['sum_tot_amt']+$tot_baby_bill;
			
			$tot_amountt_pay=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS sum_paid, SUM(`discount`) AS sum_dis FROM `ipd_advance_payment_details` WHERE `patient_id`='$ipd_pat_reg[patient_id]' AND `ipd_id`='$ipd_pat_reg[opd_id]' AND `date` between '$date1' AND '$date2' $user_str_ipd "));
			$paid=$tot_amountt_pay['sum_paid'];
			$discount=$tot_amountt_pay['sum_dis'];
			$balance=$bill_amt-$paid-$discount;
			if($balance<0)
			{
				//$balance=0;
			}
			
			$ipd_payment_qry=mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$ipd_pat_reg[patient_id]' AND `ipd_id`='$ipd_pat_reg[opd_id]' AND `date` between '$date1' AND '$date2' $user_str_ipd ORDER BY `slno` ");
			$ipd_payment_num=mysqli_num_rows($ipd_payment_qry);
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$ipd_pat_reg[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $ipd_pat_reg["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo number_format($bill_amt,2); ?></td>
				<td><?php echo number_format($discount,2); ?></td>
				<td><?php echo number_format($paid,2); ?></td>
				<td><?php echo ""; ?></td>
				<td><?php echo number_format($balance,2); ?></td>
				<td><?php echo convert_date($ipd_pat_reg["date"]); ?></td>
				<td></td>
			</tr>
		<?php
			if($ipd_payment_num>0)
			{
				while($ipd_payment=mysqli_fetch_array($ipd_payment_qry))
				{
					$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_payment[user]' "));
			?>
			<tr>
				<td colspan="4"></td>
				<td><?php echo number_format($ipd_payment["discount"],2); ?></td>
				<td><?php echo number_format($ipd_payment["amount"],2); ?></td>
				<td><?php echo $ipd_payment["pay_type"]; ?></td>
				<td></td>
				<td><?php echo convert_date($ipd_payment["date"]); ?> <?php echo convert_time($ipd_payment["time"]); ?></td>
				<td><?php echo $quser["name"]; ?></td>
			</tr>
			<?php
				}
			}
			$n++;
			
			$tot_bill=$tot_bill+$bill_amt;
			$tot_bill_ipd=$tot_bill_ipd+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_dis_ipd=$tot_dis_ipd+$discount;
			$tot_paid_ipd=$tot_paid_ipd+$paid;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
			$tot_bal_ipd=$tot_bal_ipd+$balance;
		}
?>
		<tr>
			<td colspan="2"></td>
			<td colspan=""><span class="text-right"><strong>Total:</strong></span></td>
			<td><span class=""><strong><?php echo number_format(($tot_bill_ipd),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo number_format(($tot_dis_ipd),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo number_format(($tot_paid_ipd),2);?> </strong></span></td>
			<td></td>
			<td><span class=""><strong><?php echo number_format(($tot_bal_ipd),2);?> </strong></span></td>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="10">Balance Received</th>
		</tr>
<?php
	
	$adv_bal_qry=mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND b.`date`<'$date1' $encounter_val_str_ipd $user_str ");
	
	while($adv_bal=mysqli_fetch_array($adv_bal_qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
		
		$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $adv_bal["ipd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td></td>
			<td><?php echo number_format($adv_bal["discount"],2); ?></td>
			<td><?php echo number_format($adv_bal["amount"],2); ?></td>
			<td><?php echo $adv_bal["pay_type"]; ?></td>
			<td></td>
			<td><?php echo convert_date($adv_bal["date"]); ?> <?php echo convert_time($adv_bal["time"]); ?></td>
			<td><?php echo $quser["name"]; ?></td>
		</tr>
<?php
		$adv_bal_amount+=$adv_bal["amount"];
		$n++;
	}
	if($user_entry>0)
	{
		$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expensedetail where expense_date between '$date1' and '$date2' $user_str_ipd "));
	}
	else
	{
		$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expensedetail where expense_date between '$date1' and '$date2'"));
	}
	$tot_expense=$tot_expense_qry["tot_exp"];
?>
		<tr>
			<td colspan="3"></td>
			<td colspan="2"><span class="text-right"><strong>Balance Received:</strong></span></td>
			<td colspan="5"><span class=""><strong><?php echo $rupees_symbol.number_format(($adv_bal_amount),2);?> </strong></span></td>
		</tr>
		<!--<tr>
			<td colspan="3"></td>
			<td colspan="2"><span class="text-right"><strong>Total Amount:</strong></span></td>
			<td colspan="5"><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid+$adv_bal_amount),2);?> </strong></span></td>
		</tr>
		<tr>
			<td colspan="4"></td>
			<td colspan=""><span class="text-right"><strong>Expense:</strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_expense),2);?> </strong></span></td>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3"></td>
			<td colspan="2"><span class="text-right"><strong>Net Amount:</strong></span></td>
			<td colspan="2"><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid+$adv_bal_amount-$tot_expense),2);?> </strong></span></td>
			<td colspan="5">&nbsp;</td>
		</tr>-->
	</table>
	<table class="table">
		<tr>
			<th colspan="5">Account Summary</th>
		</tr>
		<tr>
			<th>Bill Amount</th>
			<th>Discount Amount</th>
			<th>Paid Amount</th>
			<!--<th>Balance Receive</th>-->
			<th>Balance Amount</th>
		</tr>
		<tr>
			<th><?php echo number_format(($tot_bill),2);?></th>
			<th><?php echo number_format(($tot_dis),2);?></th>
			<th><?php echo number_format(($tot_paid),2);?></th>
			<!--<th><?php echo number_format(($adv_bal_amount),2);?></th>-->
			<th><?php echo number_format(($tot_bal),2);?></th>
		</tr>
		<tr>
			<td></td>
			<th><span class="text-right">Balance Received :</span></th>
			<th><?php echo number_format(($adv_bal_amount),2);?></th>
			<th colspan="2"></th>
		</tr>
<?php
	
	$date11=$date1;
	$date22=$date2;
	if($user_entry==0)
	{
		if($encounter==0 || $encounter==3)
		{
			$ipd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund),0) as maxref from ipd_advance_payment_details where `date` between '$date11' and '$date22' "));
		}
		$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expensedetail where expense_date between '$date11' and '$date22'"));
		
		if($encounter==0 || $encounter==2)
		{
			// Lab Refund
			$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='2' "));
			// Lab Free
			$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as lab_free from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='2' "));
		}
		
		if($encounter==0 || $encounter==1)
		{
			// OPD Refund
			$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='1' "));
			// OPD Free
			$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as opd_free from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='1' "));
		}
		if($encounter==0 || $encounter==5)
		{
			// Day Care Refund
			$daycare_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_day from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='5' "));
		}
	}
	else
	{
		if($encounter==0 || $encounter==3)
		{
			$ipd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund),0) as maxref from ipd_advance_payment_details where `date` between '$date11' and '$date22' and user='$user_entry' "));
			
		}
		$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expensedetail where expense_date between '$date11' and '$date22' and `user`='$user_entry' "));
		
		if($encounter==0 || $encounter==5)
		{
			// Day Care Refund
			$daycare_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_day from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='5' and a.`user`='$user_entry' "));
		}
		if($encounter==0 || $encounter==2)
		{
			// Lab Refund
			$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='2' and a.`user`='$user_entry' "));
			// Lab Free
			$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as lab_free from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='2' and a.`user`='$user_entry' "));
		}
		if($encounter==0 || $encounter==1)
		{
			// OPD Refund
			$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='1' and a.`user`='$user_entry' "));
			// OPD Free
			$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as opd_free from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='1' and a.`user`='$user_entry' "));
		}
		
	}
	$tot_expense=$tot_expense_qry["tot_exp"];
	
	$day_refund=$daycare_refund_val["maxref_day"];
	$opd_refund=$opd_refund_val["maxref_opd"];
	$lab_refund=$lab_refund_val["maxref"];
	$total_refund_amount=$opd_refund+$lab_refund;
	
	// Free
	$opd_free_amount=$opd_free_val["opd_free"];
	$lab_free_amount=$lab_free_val["lab_free"];
	$total_free_amount=$opd_free_amount+$lab_free_amount;
	
	$ipd_refund=$ipd_refund_val["maxref"];
?>
		<tr>
			<td></td>
			<th><span class="text-right">Total Amount :</span></th>
			<th><?php echo number_format(($tot_paid+$adv_bal_amount),2);?></th>
			<th colspan="2"></th>
		</tr>
		<tr>
			<td></td>
			<th><span class="text-right"> Total Expense :</span></th>
			<th><?php echo number_format(($tot_expense),2);?></th>
			<th colspan="2"></th>
		</tr>
		<tr>
			<td></td>
			<th><span class="text-right">Total Refund :</span></th>
			<th><?php echo number_format($total_refund_amount,2); ?></th>
			<th colspan="2"></th>
		</tr>
		<tr>
			<td></td>
			<th><span class="text-right">Total Free :</span></th>
			<th><?php echo number_format($total_free_amount,2); ?></th>
			<th colspan="2"></th>
		</tr>
		<tr>
			<td></td>
			<th><span class="text-right">Net Amount :</span></th>
			<th><?php echo number_format(($tot_paid+$adv_bal_amount-$tot_expense-$total_refund_amount-$total_free_amount),2);?></th>
			<th colspan="2"></th>
		</tr>
	</table>
<?php
}
?>
<?php
}
if($_POST["type"]=="account_summary_old")
{
	$encounter=$_POST['encounter'];
	$encounter_val=$_POST['encounter'];
	$user_entry=$_POST['user_entry'];
	$pay_mode=$_POST['pay_mode'];
	
?>
	<p style="margin-top: 2%;"><b>Account Summary Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/account_summary_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>&pay_mode=<?php echo $pay_mode;?>"><i class="icon-file icon-large"></i> Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('summary','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','<?php echo $pay_mode;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Bill Amout</th>
				<th>Discount</th>
				<th>Paid</th>
				<th>Balance</th>
				<th>User</th>
				<th>Encounter</th>
				<th>Date</th>
			</tr>
		</thead>
<?php
	$n=1;
	$tot_bill=$tot_dis=$tot_paid=$tot_bal=0;
	
	$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter_val' "));
	$pat_typ_encounter=$pat_typ["type"];
	
	$encounter_val_str="";
	if($encounter_val>0)
	{
		$encounter_val_str=" AND b.`type`='$encounter_val' ";
	}
	$user_str="";
	if($user_entry>0)
	{
		$user_str=" AND a.`user`='$user_entry' ";
	}
	
	if($encounter_val=='0' || $pat_typ_encounter=='1')
	{
		$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `consult_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
		while($con_bal=mysqli_fetch_array($con_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$paid=$con_bal['amount'];
			
			$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
			
			$bill_amt=$con_pat_pay['tot_amount'];
			$discount=$con_pat_pay['dis_amt'];
			$balance=$con_pat_pay['balance'];
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $con_bal["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($con_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
		}
	}
	if($encounter_val=='0' || $pat_typ_encounter=='2')
	{
		$inv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str");
		while($inv_bal=mysqli_fetch_array($inv_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$paid=$inv_bal['amount'];
			
			$inv_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$inv_bal[patient_id]' and opd_id='$inv_bal[opd_id]'"));
			
			$bill_amt=$inv_pat_pay['tot_amount'];
			$discount=$inv_pat_pay['dis_amt'];
			$balance=$inv_pat_pay['balance'];
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$inv_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$inv_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $inv_bal["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($inv_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
		}
	}
	
	if($encounter_val=='0' || $pat_typ_encounter=='3')
	{
		$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
		while($adv_bal=mysqli_fetch_array($adv_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			// Baby's Charge
			$baby_serv_tot=0;
			$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]' "));
			if($delivery_check)
			{
				$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_serv_tot=$baby_tot_serv["tots"];
				
				// OT Charge Baby
				$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_ot_total=$baby_ot_tot_val["g_tot"];
				
				$tot_baby_bill=$baby_serv_tot+$baby_ot_total;
				
			}
			
			$bill_amt=$tot_serv['sum_tot_amt']+$tot_serv_ot['sum_tot_amt']+$tot_baby_bill;
			
			$tot_amountt_pay=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS sum_paid FROM `ipd_advance_payment_details` WHERE `slno`='$adv_bal[slno]'"));
			$paid=$tot_amountt_pay['sum_paid'];
			
			$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			$discount=$tot_amountt['sum_dis'];
			$paidd=$tot_amountt['sum_paid'];
			$balance=($bill_amt-$discount-$paidd);
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $adv_bal["ipd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($adv_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
		}
	}
	
	// Balance Received
	
	echo "<tr><th colspan='10'>Balance Received</th></tr>";
	
	if($encounter_val=='0' || $pat_typ_encounter=='1')
	{
		$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `consult_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
		while($con_bal=mysqli_fetch_array($con_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$paid=$con_bal['amount'];
			
			$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
			
			$bill_amt=$con_pat_pay['tot_amount'];
			$discount=$con_pat_pay['dis_amt'];
			$balance=$con_pat_pay['balance'];
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $con_bal["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($con_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
		}
	}
	if($encounter_val=='0' || $pat_typ_encounter=='2')
	{
		$inv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
		while($inv_bal=mysqli_fetch_array($inv_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$paid=$inv_bal['amount'];
			
			$inv_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$inv_bal[patient_id]' and opd_id='$inv_bal[opd_id]'"));
			
			$bill_amt=$inv_pat_pay['tot_amount'];
			$discount=$inv_pat_pay['dis_amt'];
			$balance=$inv_pat_pay['balance'];
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$inv_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$inv_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $inv_bal["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($inv_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
		}
	}
	if($encounter_val=='0' || $pat_typ_encounter=='3')
	{
		$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str");
		while($adv_bal=mysqli_fetch_array($adv_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			// Baby's Charge
			$baby_serv_tot=0;
			$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]' "));
			if($delivery_check)
			{
				$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_serv_tot=$baby_tot_serv["tots"];
				
				// OT Charge Baby
				$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_ot_total=$baby_ot_tot_val["g_tot"];
				
				$tot_baby_bill=$baby_serv_tot+$baby_ot_total;
				
			}
			
			$bill_amt=$tot_serv['sum_tot_amt']+$tot_serv_ot['sum_tot_amt']+$tot_baby_bill;
			
			$tot_amountt_pay=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS sum_paid FROM `ipd_advance_payment_details` WHERE `slno`='$adv_bal[slno]'"));
			$paid=$tot_amountt_pay['sum_paid'];
			
			$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			$discount=$tot_amountt['sum_dis'];
			$paidd=$tot_amountt['sum_paid'];
			$balance=($bill_amt-$discount-$paidd);
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
			$encounter=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $adv_bal["ipd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<td><?php echo $quser["name"]; ?></td>
				<td><?php echo $encounter; ?></td>
				<td><?php echo convert_date($adv_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill=$tot_bill+$bill_amt;
			$tot_dis=$tot_dis+$discount;
			$tot_paid=$tot_paid+$paid;
			$tot_bal=$tot_bal+$balance;
		}
	}
	if($encounter_val=='0')
	{
		$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date1' and '$date2'"));
	}else
	{
		$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date1' and '$date2' AND `user`='$encounter_val' "));
	}
	$tot_expense=$tot_expense_qry["tot_exp"];
?>
		<tr>
			<td colspan="2"></td>
			<td colspan=""><span class="text-right"><strong>Total:</strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bill),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_dis),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bal),2);?> </strong></span></td>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"></td>
			<td colspan=""><span class="text-right"><strong>Expense:</strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_expense),2);?> </strong></span></td>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"></td>
			<td><span class="text-right"><strong>Net Amount:</strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid-$tot_expense),2);?> </strong></span></td>
			<td colspan="5">&nbsp;</td>
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
	
	$encounter_pay_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type=$pat_typ_text["type"];
	}
	
	if($encounter==0 || $encounter_pay_type==1)
	{
		$con_pay_qry=mysqli_query($link, " SELECT a.`opd_id` FROM `consult_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`date` between '$date1' and '$date2' and a.`dis_amt`>0 $encounter_str $user_str ORDER BY a.`slno` ");
		while($con_pay=mysqli_fetch_array($con_pay_qry))
		{
			$all_pin[$i]=$con_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter_pay_type==2)
	{
		$inv_pay_qry=mysqli_query($link, " SELECT a.`opd_id` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`date` between '$date1' and '$date2' and a.`dis_amt`>0 $encounter_str $user_str ORDER BY a.`slno` ");
		while($inv_pay=mysqli_fetch_array($inv_pay_qry))
		{
			$all_pin[$i]=$inv_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter_pay_type==3)
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
		<span class="text-right" id="excel_btn_hide">
			<a class="btn btn-info btn-mini" href="pages/discount_report_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&encounter=<?php echo $encounter;?>&user_entry=<?php echo $user_entry;?>">Export to Excel</a>
		</span>
		
		<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('discount','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed ">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Bill Amout</th>
				<th>Discount</th>
				<th>Reason</th>
				<th>User</th>
				<th>Encounter</th>
				<th>Date</th>
			</tr>
		</thead>
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
				$encounter_str=$pat_typ_text['type'];
				
				if($encounter_str==1) // OPD
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
				if($encounter_str==2) // LAB
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
				if($encounter_str==3) // Casualty
				{
					$ipd_adv_dis_qry=mysqli_query($link, " SELECT `date`,`user` FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' AND `discount`>0 ");
					$ipd_adv_dis_num=mysqli_num_rows($ipd_adv_dis_qry);
					if($ipd_adv_dis_num>0)
					{
						$show_pat=1;
						$ipd_adv_dis=mysqli_fetch_array($ipd_adv_dis_qry);
						
						$pat_ser=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(sum(`amount`),0) as sum_tot_amt FROM `ipd_pat_service_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' "));
						$bill_amt=$pat_ser['sum_tot_amt'];
						
						$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' "));
						
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
	?>
		<p><b>Account of <i><?php echo $user_name["name"]; ?></i> from <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b> </p>
		<table class="table table-hover table-condensed" style="font-size: 14px;">
	<?php
		$tot_amt=0;
		$pat_typ_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` ORDER BY `p_type_id` ");
		while($pat_typ=mysqli_fetch_array($pat_typ_qry))
		{
			if($pat_typ["type"]==1)
			{
				// OPD
				$consult_payment_amt=0;
				$consult_payment=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `consult_tot` FROM `consult_payment_detail` WHERE `payment_mode`='Cash' AND `user`='$user' AND `date` BETWEEN '$date1' AND '$date2' "));
				$consult_payment_amt=$consult_payment["consult_tot"];
				$tot_amt+=$consult_payment_amt;
	?>
			<tr>
				<th style="width: 30%;"><?php echo $pat_typ["p_type"]; ?> Acount</th><td><b>: </b><?php echo $rupees_symbol.number_format($consult_payment_amt,2); ?></td>
			</tr>
	<?php
			}
			if($pat_typ["type"]==2)
			{
				// LAB
				$invest_payment_amt=0;
				$invest_payment=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `invest_tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `user`='$user' AND `date` BETWEEN '$date1' AND '$date2' "));
				$invest_payment_amt=$invest_payment["invest_tot"];
				$tot_amt+=$invest_payment_amt;
	?>
			<tr>
				<th style="width: 30%;"><?php echo $pat_typ["p_type"]; ?> Acount</th><td><b>: </b><?php echo $rupees_symbol.number_format($invest_payment_amt,2); ?></td>
			</tr>
	<?php
			}
			if($pat_typ["type"]==3)
			{
				// Other
				$ipd_payment_amt=0;
				$ipd_payment=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(a.`amount`) AS `ipd_tot` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`pay_mode`='Cash' AND a.`user`='$user' AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`type`='$pat_typ[p_type_id]' "));
				$ipd_payment_amt=$ipd_payment["ipd_tot"];
				$tot_amt+=$ipd_payment_amt;
	?>
			<tr>
				<th style="width: 30%;"><?php echo $pat_typ["p_type"]; ?> Acount</th><td><b>: </b><?php echo $rupees_symbol.number_format($ipd_payment_amt,2); ?></td>
			</tr>
	<?php
			}
		}
		// Expense
		$exp_payment=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `exp_tot` FROM `expense_detail` WHERE `user`='$user' AND `date` BETWEEN '$date1' AND '$date2' "));
		$exp_payment_amt=$exp_payment["exp_tot"];
		
		// Already Paid
		$already=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `paid_tot` FROM `cash_deposit` WHERE `emp_id`='$user' AND `date` BETWEEN '$date1' AND '$date2' "));
		$already_paid=$already["paid_tot"];
		
		$net_amt=$tot_amt-$exp_payment_amt-$already_paid;
		
		// User
		$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));
	?>
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
			<th>Date</th><th>Sl No.</th><th>Bill No</th><th>Patient Name</th><th>Amount</th><th>C/O</th><th style="width: 25%;">Remarks</th>
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
			<th>Date</th><th>Sl No.</th><th>Bill No</th><th>Patient Name</th><th>Amount</th><th>C/O</th>
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
			<th>Date</th><th>Sl No.</th><th>Bill No</th><th>Cabin</th><th>Patient Name</th><th>Amount</th><th>Center</th>
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
			<th>Date</th><th>Sl No.</th><th>Bill No</th><th>Cabin</th><th>Patient Name</th><th>Amount</th><th>Center</th>
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
			<th>Date</th><th>Sl No.</th><th>Bill No</th><th>Cabin</th><th>Patient Name</th><th>Amount</th><th>Center</th>
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
			<th>#</th><th>Bill No</th><th>Patient Name</th><th>Amount</th><th>Reg Date Time</th><th>User</th>
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

if($_POST["type"]=="daily_reports")
{
	$user_entry=trim($_POST['user_entry']);
	
	$user_str="";
	$user_str_a="";
	$user_str_b="";
	$emp_name="All User";
	if($user_entry>0)
	{
		$user_str=" AND `user`='$user_entry'";
		$user_str_a=" AND a.`user`='$user_entry'";
		$user_str_b=" AND b.`user`='$user_entry'";
		
		$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
		$emp_name=$user_name["name"];
	}
?>
	<div id="print_div">
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info btn-mini" href="pages/daily_reports_xls.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&user_entry=<?php echo $user_entry;?>"><i class="icon-file icon-large"></i> Excel</a></span>

		<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('daily_reports','<?php echo $date1;?>','<?php echo $date2;?>','','<?php echo $user_entry;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</div>
	<table class="table table-hover table-condensed">
		<tr>
			<td colspan="3">
				<b>CASHIER ON DUTY : </b> <?php echo $emp_name; ?>
			</td>
		</tr>
<?php
		// OPD
		$opd_pat_new=$opd_pat_pfu=$opd_pat_ffu=0;
		$opd_visit_amount=$opd_reg_amount=$opd_disount_amount=$opd_free_amount_det=0;
		
		$pat_pay_det_opd_qry=mysqli_query($link," SELECT * FROM `consult_patient_payment_details` WHERE `date` between '$date1' AND '$date2' $user_str");
		while($pat_pay_det_opd=mysqli_fetch_array($pat_pay_det_opd_qry))
		{
			// Patient count
			if($pat_pay_det_opd["visit_fee"]>0)
			{
				$pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='6' "));
				if($pat_paid_follow)
				{
					$opd_pat_pfu++;
				}else
				{
					$opd_pat_new++;
				}
			}
			else
			{
				$opd_pat_ffu++;
			}
			// Payment
			$opd_visit_amount+=$pat_pay_det_opd["visit_fee"];
			$opd_reg_amount+=$pat_pay_det_opd["regd_fee"]+$pat_pay_det_opd["emergency_fee"];
			
			$opd_pat_free=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `invest_payment_free` WHERE `opd_id`='$con_pay[opd_id]' "));
			if(!$opd_pat_free)
			{
				$opd_disount_amount+=$pat_pay_det_opd["dis_amt"];
				
				$all_total_disount_amount+=$pat_pay_det_opd["dis_amt"];
			}else
			{
				$opd_free_amount_det+=$opd_pat_free["free_amount"];
			}
		}
		
?>
		<tr>
			<th colspan="3">OPD Registration</th>
		</tr>
		<tr>
			<td></td>
			<td>No. of Patient</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td></td>
			<td><?php echo ($opd_pat_new+$opd_pat_pfu+$opd_pat_ffu); ?> (NP : <?php echo $opd_pat_new; ?>, PFU : <?php echo $opd_pat_pfu; ?>, FFU : <?php echo $opd_pat_ffu; ?>)</td>
			<td><?php echo number_format($opd_visit_amount,2); ?></td>
		</tr>
<?php
		// Dental
		//~ $dental_pat_num=$dental_pat_new=$dental_pat_pfu=$dental_pat_ffu=0;
		//~ $dental_amount=0;
		
		//~ $dental_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='6' $user_str");
		//~ while($dental_pat=mysqli_fetch_array($dental_pat_qry))
		//~ {
			//~ $dental_pat_ser=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_dental FROM `ipd_pat_service_details` WHERE `patient_id`='$dental_pat[patient_id]' AND `ipd_id`='$dental_pat[opd_id]' AND `group_id`='188' AND `service_id`='253' "));
			//~ if($dental_pat_ser["tot_dental"]>0)
			//~ {
				//~ $pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='11' "));
				//~ if($pat_paid_follow)
				//~ {
					//~ $dental_pat_pfu++;
				//~ }else
				//~ {
					//~ $dental_pat_new++;
				//~ }
				
				//~ $dental_pat_num++;
				
				//~ $dental_amount+=$dental_pat_ser["tot_dental"];	
			//~ }
			//~ else
			//~ {
				//~ $dental_pat_ffu++;
				//~ $dental_pat_num++;
			//~ }
		//~ }
?>
		<!--<tr>
			<th colspan="3">Dental Registration</th>
		</tr>
		<tr>
			<td></td>
			<td>No. of Patient</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td></td>
			<td><?php echo ($dental_pat_new+$dental_pat_pfu+$dental_pat_ffu); ?> (NPDE : <?php echo $dental_pat_new; ?>, PFDE : <?php echo $dental_pat_pfu; ?>, FFDE : <?php echo $dental_pat_ffu; ?>)</td>
			<td><?php echo number_format($dental_amount,2); ?></td>
		</tr>-->
<?php
		// CASUALTY
		$casu_pat_new=0;
		$casu_pat_pfu=0;
		$casu_pat_ffu=0;
		$casu_amount=0;
		
		$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='4' $user_str");
		while($casu_pat=mysqli_fetch_array($casu_pat_qry))
		{
			//~ $only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='186' AND `service_id`='251' "));
			
			$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' "));
			
			if($only_casu_qry["tot_casu"]>0)
			{
				$pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='8' "));
				if($pat_paid_follow)
				{
					$casu_pat_pfu++;
				}else
				{
					$casu_pat_new++;
				}
				
				$only_casu_amount+=$only_casu_qry["tot_casu"];
				
			}
			else
			{
				$casu_pat_ffu++;
			}
		}
?>
		<tr>
			<th colspan="3">EMERGENCY ROOM Registration</th>
		</tr>
		<tr>
			<td></td>
			<td>No. of Patient</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<?php echo ($casu_pat_new+$casu_pat_pfu+$casu_pat_ffu); ?>
				(NPER : <?php echo $casu_pat_new; ?>, PFER : <?php echo $casu_pat_pfu; ?>, FFER : <?php echo $casu_pat_ffu; ?>)
			</td>
			<td><?php echo number_format($only_casu_amount,2); ?></td>
		</tr>
<?php
		// Dialysis
		$dialysis_pat_new=0;
		$dialysis_pat_pfu=0;
		$dialysis_pat_ffu=0;
		$dialysis_amount=0;
		
		$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='7' $user_str");
		while($casu_pat=mysqli_fetch_array($casu_pat_qry))
		{
			//~ $only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='187' AND `service_id`='252' "));
			
			$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' "));
			
			if($only_casu_qry["tot_casu"]>0)
			{
				$pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='16' "));
				if($pat_paid_follow)
				{
					$dialysis_pat_pfu++;
				}else
				{
					$dialysis_pat_new++;
				}
				
				$dialysis_amount+=$only_casu_qry["tot_casu"];
				
			}
			else
			{
				$dialysis_pat_ffu++;
			}
		}
		// IPD Dailysis
		$ipd_dialysis_pat=mysqli_num_rows(mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_pat_service_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' AND a.`service_id`='252' $user_str_a ORDER BY a.`slno`"));
?>
		<tr>
			<th colspan="3">Dialysis Registration</th>
		</tr>
		<tr>
			<td></td>
			<td>No. of Patient</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<?php echo ($dialysis_pat_new+$dialysis_pat_pfu+$dialysis_pat_ffu+$ipd_dialysis_pat); ?>
				(NPDI : <?php echo $dialysis_pat_new; ?>, PFDI : <?php echo $dialysis_pat_pfu; ?>, FFDI : <?php echo $dialysis_pat_ffu; ?>, IPD : <?php echo $ipd_dialysis_pat; ?>)
			</td>
			<td><?php echo number_format($dialysis_amount,2); ?></td>
		</tr>
<?php
		// Daycare
		$daycare_pat_new=0;
		$daycare_amount=0;
		
		$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='5' $user_str");
		while($casu_pat=mysqli_fetch_array($casu_pat_qry))
		{
			//~ $only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='167' AND `service_id`='150' "));
			
			$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' "));
			
			if($only_casu_qry["tot_casu"]>0)
			{
				$daycare_pat_new++;
				
				$daycare_amount+=$only_casu_qry["tot_casu"];
			}
		}
?>
		<tr>
			<th colspan="3">Daycare Registration</th>
		</tr>
		<tr>
			<td></td>
			<td>No. of Patient</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<?php echo $daycare_pat_new; ?>
			</td>
			<td><?php echo number_format($daycare_amount,2); ?></td>
		</tr>
<?php
		// Ambulance
		$ambulance_pat_new=0;
		$ambulance_amount=0;
		
		$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='14' $user_str");
		while($casu_pat=mysqli_fetch_array($casu_pat_qry))
		{
			//~ $only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='167' AND `service_id`='150' "));
			
			$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' "));
			
			if($only_casu_qry["tot_casu"]>0)
			{
				$ambulance_pat_new++;
				
				$ambulance_amount+=$only_casu_qry["tot_casu"];
			}
		}
?>
		<tr>
			<th colspan="3">Ambulance</th>
		</tr>
		<tr>
			<td></td>
			<td>No. of Patient</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<?php echo $ambulance_pat_new; ?>
			</td>
			<td><?php echo number_format($ambulance_amount,2); ?></td>
		</tr>
<?php
		// Dental Procedure `group_id`='192'
		$dental_procedure_pat=$dental_procedure_num=$dental_procedure_amount=0;
		
		$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='6' $user_str");
		while($casu_pat=mysqli_fetch_array($casu_pat_qry))
		{
			//~ $only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='192' "));
			
			$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' "));
			
			if($only_casu_qry["tot_casu"]>0)
			{
				$dental_procedure_pat++;
				
				$dental_procedure_amount+=$only_casu_qry["tot_casu"];
				
				$dental_serv_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' ")); // AND `group_id`='192'
				$dental_procedure_num+=$dental_serv_num;
			}
		}
		
?>
		<tr>
			<th colspan="3">Dental Procedure</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No of Procedure</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $dental_procedure_pat; ?></td>
			<td><?php echo $dental_procedure_num; ?></td>
			<td><?php echo number_format($dental_procedure_amount,2); ?></td>
		</tr>
<?php
		// Other Procedure
		$other_procedure_pat=$other_procedure_num=$other_procedure_amount=0;
		
		$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='15' $user_str");
		while($casu_pat=mysqli_fetch_array($casu_pat_qry))
		{
			//~ $only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='192' "));
			
			$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' "));
			
			if($only_casu_qry["tot_casu"]>0)
			{
				$other_procedure_pat++;
				
				$other_procedure_amount+=$only_casu_qry["tot_casu"];
				
				$other_serv_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' ")); // AND `group_id`='192'
				$other_procedure_num+=$other_serv_num;
			}
		}
		
?>
		<tr>
			<th colspan="3">Other Procedure</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No of Procedure</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $other_procedure_pat; ?></td>
			<td><?php echo $other_procedure_num; ?></td>
			<td><?php echo number_format($other_procedure_amount,2); ?></td>
		</tr>
<?php
		// Daycare Services
		//~ $day_group_id = array();
		//~ array_push($day_group_id, 191, 192); // MISCELLANEOUS , DENTAL PROCEDURE
		//~ $day_group_id = join(',',$day_group_id);
		
		//~ $ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date` between '$date1' and '$date2' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id` NOT IN($day_group_id)) $user_str_b ORDER BY a.`slno`");
		//~ $ipd_casual_pay_num=mysqli_num_rows($ipd_casual_pay_qry);
		//~ if($ipd_casual_pay_num>0)
		//~ {
			//~ echo "<tr><th colspan='3'>DAYCARE SERVICES</th></tr>";
			//~ echo "<tr><td>Service Name</td><td>No</td><td>Amount</td></tr>";
			
			//~ $charge_qry=mysqli_query($link, "SELECT `charge_id`, `charge_name` FROM `charge_master` WHERE `group_id` NOT IN($day_group_id) ORDER BY `charge_name`");
			//~ while($charge_val=mysqli_fetch_array($charge_qry))
			//~ {
				//~ $each_service_num=0;
				//~ $each_service_amount=0;
				//~ $ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date` between '$date1' and '$date2' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id` NOT IN($day_group_id)) ORDER BY a.`slno`");
				//~ while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
				//~ {
					//~ $service_qry=mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `ipd_id`='$ipd_casual_pay[ipd_id]' AND `service_id`='$charge_val[charge_id]' AND `rate`>0 ");
					//~ //$service_num=mysqli_num_rows($service_qry);
					//~ $service_num=$service_amount=0;
					//~ while($service_val=mysqli_fetch_array($service_qry))
					//~ {
						//~ $service_num+=$service_val["ser_quantity"];
						//~ $service_amount=$service_val["rate"]*$service_num;
					//~ }
					
					//~ $each_service_num+=$service_num;
					//~ $each_service_amount+=$service_amount;
				//~ }
				//~ if($each_service_num>0)
				//~ {
					//~ echo "<tr><td>$charge_val[charge_name]</td><td>$each_service_num</td><td>$each_service_amount</td></tr>";
					//~ $all_total_amount+=$each_service_amount;
				//~ }
			//~ }
		//~ }
		// MISCELLANEOUS Services
		
		//~ $ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date` between '$date1' and '$date2' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id`='191') $user_str_b ORDER BY a.`slno`");
		
		$ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date` between '$date1' and '$date2' $user_str_b ORDER BY a.`slno`");
		$ipd_casual_pay_num=mysqli_num_rows($ipd_casual_pay_qry);
		if($ipd_casual_pay_num>0)
		{
			echo "<tr><th colspan='3'>MISCELLANEOUS SERVICES</th></tr>";
			echo "<tr><td>Service Name</td><td>No</td><td>Amount</td></tr>";
			
			$charge_qry=mysqli_query($link, "SELECT `charge_id`, `charge_name` FROM `charge_master` WHERE `group_id`='191' ORDER BY `charge_name`");
			while($charge_val=mysqli_fetch_array($charge_qry))
			{
				$each_service_num=0;
				$each_service_amount=0;
				$ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date` between '$date1' and '$date2' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id`='191') ORDER BY a.`slno`");
				while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
				{
					$mis_service_qry=mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `ipd_id`='$ipd_casual_pay[ipd_id]' AND `service_id`='$charge_val[charge_id]' AND `rate`>0 ");
					//$service_num=mysqli_num_rows($mis_service_qry);
					$service_num=$service_amount=0;
					while($service_val=mysqli_fetch_array($mis_service_qry))
					{
						$service_num+=$service_val["ser_quantity"];
						$service_amount=$service_val["rate"]*$service_num;
					}
					
					$each_service_num+=$service_num;
					$each_service_amount+=$service_amount;
				}
				if($each_service_num>0)
				{
					echo "<tr><td>$charge_val[charge_name]</td><td>$each_service_num</td><td>$each_service_amount</td></tr>";
					$all_total_amount+=$each_service_amount;
				}
			}
		}
		
?>
<?php
		// Laboratory OPD
		$opd_lab_pat=$opd_lab_path_amount=$opd_test_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='2' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='1') AND `date` BETWEEN '$date1' AND '$date2' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$opd_lab_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$opd_test_num++;
					$opd_lab_path_amount+=$opd_test["test_rate"];
				}
			}
		}
?>
		<tr>
			<th colspan="3">LABORATORY</th>
		</tr>
		<tr>
			<th colspan="3">OPD</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No. of Test</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $opd_lab_pat; ?></td>
			<td><?php echo $opd_test_num; ?></td>
			<td><?php echo number_format($opd_lab_path_amount,2); ?></td>
		</tr>
<?php
		// Laboratory IPD
		$ipd_lab_pat=$ipd_lab_path_amount=$ipd_test_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='1') AND `date` BETWEEN '$date1' AND '$date2' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$ipd_lab_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$ipd_test_num++;
					$ipd_lab_path_amount+=$opd_test["test_rate"];
				}
			}
		}
?>
		<tr>
			<th colspan="3">IPD</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No. of Test</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $ipd_lab_pat; ?></td>
			<td><?php echo $ipd_test_num; ?></td>
			<td><?php echo number_format($ipd_lab_path_amount,2); ?></td>
		</tr>
<?php
		// ECG OPD
		$opd_ecg_pat=$opd_ecg_path_amount=$opd_ecg_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='12' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='3') AND `date` BETWEEN '$date1' AND '$date2' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$opd_ecg_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$opd_ecg_num++;
					$opd_ecg_path_amount+=$opd_test["test_rate"];
				}
			}
		}
?>
		<tr>
			<th colspan="3">ECG</th>
		</tr>
		<tr>
			<th colspan="3">OPD</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No. of Test</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $opd_ecg_pat; ?></td>
			<td><?php echo $opd_ecg_num; ?></td>
			<td><?php echo number_format($opd_ecg_path_amount,2); ?></td>
		</tr>
<?php
		// ECG IPD
		$ipd_ecg_pat=$ipd_ecg_path_amount=$ipd_ecg_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='3') AND `date` BETWEEN '$date1' AND '$date2' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$ipd_ecg_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$ipd_ecg_num++;
					$ipd_ecg_path_amount+=$opd_test["test_rate"];
				}
			}
		}
?>
		<tr>
			<th colspan="3">IPD</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No. of Test</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $ipd_ecg_pat; ?></td>
			<td><?php echo $ipd_ecg_num; ?></td>
			<td><?php echo number_format($ipd_ecg_path_amount,2); ?></td>
		</tr>
<?php
		// SONOGRAPHY OPD
		$opd_usg_pat=$opd_usg_path_amount=$opd_usg_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='10' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='128') AND `date` BETWEEN '$date1' AND '$date2' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$opd_usg_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$opd_usg_num++;
					$opd_usg_path_amount+=$opd_test["test_rate"];
				}
			}
		}
?>
		<tr>
			<th colspan="3">SONOGRAPHY</th>
		</tr>
		<tr>
			<th colspan="3">OPD</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No. of Test</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $opd_usg_pat; ?></td>
			<td><?php echo $opd_usg_num; ?></td>
			<td><?php echo number_format($opd_usg_path_amount,2); ?></td>
		</tr>
<?php
		// SONOGRAPHY IPD
		$ipd_usg_pat=$ipd_usg_path_amount=$ipd_usg_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='128') AND `date` BETWEEN '$date1' AND '$date2' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$ipd_usg_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$ipd_usg_num++;
					$ipd_usg_path_amount+=$opd_test["test_rate"];
				}
			}
		}
?>
		<tr>
			<th colspan="3">IPD</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No. of Test</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $ipd_usg_pat; ?></td>
			<td><?php echo $ipd_usg_num; ?></td>
			<td><?php echo number_format($ipd_usg_path_amount,2); ?></td>
		</tr>
<?php
		// X-RAY OPD
		$opd_xray_pat=$opd_xray_path_amount=$opd_xray_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='11' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='40') AND `date` BETWEEN '$date1' AND '$date2' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$opd_xray_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$opd_xray_num++;
					$opd_xray_path_amount+=$opd_test["test_rate"];
				}
			}
		}
?>
		<tr>
			<th colspan="3">X-RAY</th>
		</tr>
		<tr>
			<th colspan="3">OPD</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No. of Test</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $opd_xray_pat; ?></td>
			<td><?php echo $opd_xray_num; ?></td>
			<td><?php echo number_format($opd_xray_path_amount,2); ?></td>
		</tr>
<?php
		// X-RAY IPD
		$ipd_xray_pat=$ipd_xray_path_amount=$ipd_xray_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='40') AND `date` BETWEEN '$date1' AND '$date2' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$ipd_xray_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$ipd_xray_num++;
					$ipd_xray_path_amount+=$opd_test["test_rate"];
				}
			}
		}
?>
		<tr>
			<th colspan="3">IPD</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No. of Test</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $ipd_xray_pat; ?></td>
			<td><?php echo $ipd_xray_num; ?></td>
			<td><?php echo number_format($ipd_xray_path_amount,2); ?></td>
		</tr>
<?php
		// ENDOSCOPY OPD
		$opd_endos_pat=$opd_endos_path_amount=$opd_endos_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='13' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='121') AND `date` BETWEEN '$date1' AND '$date2' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$opd_endos_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$opd_endos_num++;
					$opd_endos_path_amount+=$opd_test["test_rate"];
				}
			}
		}
?>
		<tr>
			<th colspan="3">ENDOSCOPY</th>
		</tr>
		<tr>
			<th colspan="3">OPD</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No. of Test</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $opd_endos_pat; ?></td>
			<td><?php echo $opd_endos_num; ?></td>
			<td><?php echo number_format($opd_endos_path_amount,2); ?></td>
		</tr>
<?php
		// ENDOSCOPY IPD
		$ipd_endos_pat=$ipd_endos_path_amount=$ipd_endos_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='121') AND `date` BETWEEN '$date1' AND '$date2' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$ipd_endos_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$ipd_endos_num++;
					$ipd_endos_path_amount+=$opd_test["test_rate"];
				}
			}
		}
?>
		<tr>
			<th colspan="3">IPD</th>
		</tr>
		<tr>
			<td>No. of Patient</td>
			<td>No. of Test</td>
			<td>Amount</td>
		</tr>
		<tr>
			<td><?php echo $ipd_endos_pat; ?></td>
			<td><?php echo $ipd_endos_num; ?></td>
			<td><?php echo number_format($ipd_endos_path_amount,2); ?></td>
		</tr>
<?php
	$ipd_new_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' and '$date2' AND `type`='3' $user_str "));
	
	$ipd_discharge_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ipd_pat_discharge_details` WHERE `date` between '$date1' and '$date2'  $user_str "));
	
	$ipd_admit_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date1' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date1') "));
	
	// IPD Payment
	$ipd_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `tot_adv` FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' AND a.`pay_type`='Advance' $user_str_a"));
	$ipd_advance_recv=$ipd_pay_det["tot_adv"];
	
	$ipd_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `tot_final` FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' AND a.`pay_type`='Final' $user_str_a"));
	$ipd_final_recv=$ipd_pay_det["tot_final"];
	
	$ipd_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `tot_bal` FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' AND a.`pay_type`='Balance' $user_str_a"));
	$ipd_balance_recv=$ipd_pay_det["tot_bal"];
	
	$ipd_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`refund`),0) AS `tot_refund` FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' "));
	$ipd_refunded=$ipd_pay_det["tot_refund"];
	
	$ipd_total_amount_recv=$ipd_advance_recv+$ipd_final_recv+$ipd_balance_recv-$ipd_refunded;
?>
		<tr>
			<th colspan="3">IPD</th>
		</tr>
		<tr>
			<td></td>
			<td>No. of Admission</td>
			<td><?php echo $ipd_new_pat_num; ?></td>
		</tr>
		<tr>
			<td></td>
			<td>No. of Discharge</td>
			<td><?php echo $ipd_discharge_pat_num; ?></td>
		</tr>
		<tr>
			<td></td>
			<td>No. of Admitted Patient</td>
			<td><?php echo $ipd_admit_pat_num; ?></td>
		</tr>
		<tr>
			<td></td>
			<td>Amount received</td>
			<td><?php echo number_format($ipd_total_amount_recv,2); ?></td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="balance_report")
{
	$str="SELECT * FROM `invest_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `balance`>0";
	
	$qry=mysqli_query($link, $str);
?>
	<p>
		<b>Balance Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span style="float:right;">
			<button type="button" class="btn btn-info btn-mini" onclick="print_page('daily_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>')"><i class="icon-print icon-large"></i> Print</button>
		</span>
	</p>
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>OPD ID</th>
				<th>Patient Name</th>
				<th>Bill Amout</th>
				<th>Discount</th>
				<th>Paid</th>
				<th>Balance</th>
				<th>Reg Date</th>
			</tr>
		</thead>
<?php
		$n=1;
		$total_bill=$total_discount=$total_paid=$total_balance=0;
		while($bal_pat=mysqli_fetch_array($qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$bal_pat[patient_id]'"));
			
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$bal_pat[patient_id]' and opd_id='$bal_pat[opd_id]'"));
?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $bal_pat["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $pat_pay_det["tot_amount"]; ?></td>
				<td><?php echo $pat_pay_det["dis_amt"]; ?></td>
				<td><?php echo $pat_pay_det["advance"]; ?></td>
				<td><?php echo $pat_pay_det["balance"]; ?></td>
				<td><?php echo date("d-M-Y", strtotime($pat_pay_det["date"])); ?></td>
			</tr>
<?php
			$n++;
			
			$total_bill+=$pat_pay_det["tot_amount"];
			$total_discount+=$pat_pay_det["dis_amt"];
			$total_paid+=$pat_pay_det["advance"];
			$total_balance+=$pat_pay_det["balance"];
		}
?>
		<tr>
			<th></th>
			<th></th>
			<th><span class="text-right">Total</span></th>
			<th><?php echo number_format($total_bill,2); ?></th>
			<th><?php echo number_format($total_discount,2); ?></th>
			<th><?php echo number_format($total_paid,2); ?></th>
			<th><?php echo number_format($total_balance,2); ?></th>
			<th></th>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="balance_received")
{
	$encounter=trim($_POST['encounter']);
	$user_entry=trim($_POST['user_entry']);
	$pay_mode=trim($_POST['pay_mode']);
	$account_break=trim($_POST['account_break']);
	
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	
	$pay_mode_str="";
	$payment_mode_str="";
	if($pay_mode!="0")
	{
		$payment_mode_str=" AND `payment_mode`='$pay_mode' ";
		$pay_mode_str=" AND `pay_mode`='$pay_mode' ";
	}
	
	$encounter_pay_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type=$pat_typ_text["type"];
	}
	
?>
	<p style="margin-top: 2%;" id="print_div">
		<b>Balance Received Report from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
		<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('<?php echo $_POST["type"] ?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $user_entry;?>','<?php echo $pay_mode;?>','<?php echo $account_break;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		
	</p>
	<table class="table table-hover table-condensed table-bordered">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Reg Date</th>
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Bill Amount</th>
				<th>Discount Amount</th>
				<th>Previous Paid Amount</th>
				<th>Paid Amount</th>
				<th>Balance Amount</th>
				<th>Encounter</th>
				<th>User</th>
			</tr>
		</thead>
<?php
	
	//$date_str=" AND `date` between '$date1' and '$date2'";
	$same_date="";
	$dates=getDatesFromRange($date1,$date2);
	$n=1;
	foreach($dates as $date)
	{
		if($date)
		{
			if($same_date!=$date)
			{
				$date_str=" AND `date`='$date'";
				$same_date=$date;
				$same_date_str=date("d-M-Y", strtotime($same_date));
				
				$all_pin=array();
				$i=1;
				if($encounter==0 || $encounter_pay_type==1)
				{
					$con_pay_qry=mysqli_query($link, " SELECT distinct `opd_id` FROM `consult_payment_detail` WHERE `slno`>0 AND `typeofpayment`='B' $date_str $user ORDER BY `slno`");
					while($con_pay=mysqli_fetch_array($con_pay_qry))
					{
						$all_pin[$i]=$con_pay["opd_id"];
						$i++;
					}
				}
				if($encounter==0 || $encounter_pay_type==2)
				{
					$inv_pay_qry=mysqli_query($link, " SELECT distinct `opd_id` FROM `invest_payment_detail` WHERE `slno`>0 AND `typeofpayment`='B' $date_str $user ORDER BY `slno`");
					while($inv_pay=mysqli_fetch_array($inv_pay_qry))
					{
						$all_pin[$i]=$inv_pay["opd_id"];
						$i++;
					}
				}
				if($encounter==0 || $encounter_pay_type==3)
				{
					$ipd_casual_pay_qry=mysqli_query($link, " SELECT `ipd_id` FROM `ipd_advance_payment_details` WHERE `slno`>0 AND `pay_type`='Balance' $date_str $user ORDER BY `slno`");
					while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
					{
						$all_pin[$i]=$ipd_casual_pay["ipd_id"];
						$i++;
					}
				}
				sort($all_pin);
				$all_pin=array_unique($all_pin);
				//print_r($all_pin);
				$val_length=sizeof($all_pin);
				if($val_length>0)
				{
					echo "<tr><th colspan='3'>Date: $same_date_str</th><th colspan='8'></th></tr>";
					
					foreach($all_pin as $all_pin)
					{
						if($all_pin)
						{
							if($encounter==0)
							{
								$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pin' "));
							}
							else if($encounter>0)
							{
								$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pin' AND `type`='$encounter' "));
							}
							
							$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
							
							$uhid_id=$pat_info["patient_id"];
							$pin=$pat_reg["opd_id"];
							
							$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
							$encounter_str=$pat_typ_text['p_type'];
							
							if($pat_typ_text["type"]==1)
							{
								$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' AND `typeofpayment`='B' $payment_mode_str $date_str $user ORDER BY `slno` ASC";
								$pat_pay_detail_qry=mysqli_query($link, $con_pay_det);
							}
							if($pat_typ_text["type"]==2)
							{
								$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' AND `typeofpayment`='B' $payment_mode_str $date_str $user ORDER BY `slno` ASC";
								$inv_pat_pay_detail_qry=mysqli_query($link, $inv_pay_det);
							}
							if($pat_typ_text["type"]==3)
							{
								$show_info=0;
								$ipd_casual=1;
								$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' and `ipd_id`='$pat_reg[opd_id]' AND `pay_type`='Balance' $pay_mode_str $date_str $user ORDER BY `slno` ASC";
								$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
								
							}
							
							// OPD
							while($pay_detail_opd=mysqli_fetch_array($pat_pay_detail_qry))
							{
								$pay=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]'"));
								
								//$futured_pay=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount`),0) AS `discount`, ifnull(SUM(`refund`),0) AS `refund` FROM `consult_payment_detail` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' AND `slno`> $pay_detail_opd[slno]"));
								
								$past_pay=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as final, ifnull(sum(`discount`),0) as discnt FROM `consult_payment_detail` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' AND `slno`<'$pay_detail_opd[slno]' "));
								$past_pay_amount=$past_pay["final"];
								$past_pay_discount=$past_pay["discnt"];
								
								
								$now_pay_amount=$pay_detail_opd["amount"];
								$now_pay_discount=$pay_detail_opd["discount"];
								
								$discount=$past_pay_discount+$now_pay_discount;
								if($discount==0)
								{
									$discount=$pay["dis_amt"];
								}
								
								$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_detail_opd[user]' "));
					?>
							<tr>
								<td><?php echo $n; ?></td>
								<td><?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?></td>
								<td><?php echo $pin; ?></td>
								<td><?php echo $pat_info["name"]; ?></td>
								<td><?php echo indian_money_format($pay["tot_amount"]); ?></td>
								<td><?php echo indian_money_format($discount); ?></td>
								<td><?php echo indian_money_format($past_pay_amount); ?></td>
								<td><?php echo indian_money_format($now_pay_amount); ?></td>
								<td><?php echo indian_money_format($pay_detail_opd["balance"]); ?></td>
								<td><?php echo $encounter_str; ?></td>
								<td><?php echo $user_info["name"]; ?></td>
							</tr>
					<?php
								$n++;
							}
							
							// INVESTIGATION
							while($pay_detail_inv=mysqli_fetch_array($inv_pat_pay_detail_qry))
							{
								$pay=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]'"));
								
								$past_pay=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as final, ifnull(sum(`discount`),0) as discnt FROM `invest_payment_detail` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' AND `slno`<'$pay_detail_inv[slno]' "));
								$past_pay_amount=$past_pay["final"];
								$past_pay_discount=$past_pay["discnt"];
								
								
								$now_pay_amount=$pay_detail_inv["amount"];
								$now_pay_discount=$pay_detail_inv["discount"];
								
								$discount=$past_pay_discount+$now_pay_discount;
								if($discount==0)
								{
									$discount=$pay["dis_amt"];
								}
								
								$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_detail_inv[user]' "));
					?>
							<tr>
								<td><?php echo $n; ?></td>
								<td><?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?></td>
								<td><?php echo $pin; ?></td>
								<td><?php echo $pat_info["name"]; ?></td>
								<td><?php echo indian_money_format($pay["tot_amount"]); ?></td>
								<td><?php echo indian_money_format($discount); ?></td>
								<td><?php echo indian_money_format($past_pay_amount); ?></td>
								<td><?php echo indian_money_format($now_pay_amount); ?></td>
								<td><?php echo indian_money_format($pay_detail_inv["balance"]); ?></td>
								<td><?php echo $encounter_str; ?></td>
								<td><?php echo $user_info["name"]; ?></td>
							</tr>
					<?php
								$n++;
							}
							
							// IPD & Casualty & Daycare
							while($ipd_cas_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
							{
								$uhid=$ipd_cas_pay_detail["patient_id"];
								$ipd=$ipd_cas_pay_detail["ipd_id"];
								
								$baby_serv_tot=0;
								$baby_ot_total=0;
								$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
								while($delivery_check=mysqli_fetch_array($delivery_qry))
								{
									$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
									$baby_serv_tot+=$baby_tot_serv["tots"];
									
									// OT Charge Baby
									$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
									$baby_ot_total+=$baby_ot_tot_val["g_tot"];
									
								}
								
								$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
								$tot_serv_amt1=$tot_serv1["tots"];
								
								$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
								$tot_serv_amt2=$tot_serv2["tots"];
								
								// OT Charge
								$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull((sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
								$ot_total=$ot_tot_val["g_tot"];
								
								// Total Amount
								$tot_serv_amt=$tot_serv_amt1+$tot_serv_amt2+$ot_total+$baby_serv_tot+$baby_ot_total;
								
								$past_pay=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as final, ifnull(sum(`discount`),0) as discnt FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' AND `slno`<'$ipd_cas_pay_detail[slno]' "));
								$past_pay_amount=$past_pay["final"];
								$past_pay_discount=$past_pay["discnt"];
								
								
								$now_pay_amount=$ipd_cas_pay_detail["amount"];
								$now_pay_discount=$ipd_cas_pay_detail["discount"];
								
								$discount=$past_pay_discount+$now_pay_discount;
								
								$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pay_detail[user]' "));
					?>
							<tr>
								<td><?php echo $n; ?></td>
								<td><?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?></td>
								<td><?php echo $pin; ?></td>
								<td><?php echo $pat_info["name"]; ?></td>
								<td><?php echo indian_money_format($tot_serv_amt); ?></td>
								<td><?php echo indian_money_format($discount); ?></td>
								<td><?php echo indian_money_format($past_pay_amount); ?></td>
								<td><?php echo indian_money_format($now_pay_amount); ?></td>
								<td><?php echo indian_money_format($ipd_cas_pay_detail["balance"]); ?></td>
								<td><?php echo $encounter_str; ?></td>
								<td><?php echo $user_info["name"]; ?></td>
							</tr>
					<?php
								$n++;
							}
						}
					}
				}
			}
		}
	}
	
?>
	</table>
<?php
}
?>
