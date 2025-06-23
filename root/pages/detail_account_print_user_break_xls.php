<?php
session_start();
include('../../includes/connection.php');

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$date1=$_GET['date1'];
$date2=$_GET['date2'];

// important
$date11=$_GET['date1'];
$date22=$_GET['date2'];

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

//~ $rupees_symbol="&#x20b9; ";
$rupees_symbol="";

$encounter=$_GET['encounter'];
$encounter_val=$_GET['encounter'];
$pay_mode=$_GET['pay_mode'];
$user_entry=$_GET['user_entry'];
$user_val=$_GET['EpMl'];
$account_break=$_GET['account_break'];

$encounter_pay_type=0;
if($encounter>0)
{
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
	$encounter_pay_type=$pat_typ_text["type"];
}

$filename ="detail_account_report_of_break_".$date1."_to_".$date2."_".$account_break.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);


$date_str="";
$date_str_exp="";
$date_str_a="";
$date_str_b="";
$user="";
if($user_entry>0)
{
	$user=" and `user`='$user_entry'";
	
	$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
	$emp_name=$user_name["name"];
}else
{
	$emp_name="All User";
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

?>	<p style="margin-top: 2%;" id="print_div">
	
	<b>Detail Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
	
</p>
<table class="table table-hover table-condensed">
	<thead class="table_header_fix">
		<tr>
			<th>#</th>
			<th>Date</th>
			
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
			$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.`user`='$user_entry' $ref_max_slno_str_less $ref_max_slno_str_grtr $date_str_a $encounter_strs_b_2 ")); // a.`date` between '$date11' and '$date22' and
			
			// Lab Free
			$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as lab_free from invest_payment_free a , uhid_and_opdid b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.`user`='$user_entry' $fre_max_slno_str_less $fre_max_slno_str_grtr $date_str_a $encounter_strs_b_2 ")); // a.`date` between '$date11' and '$date22' and
		}
		
		if($encounter==0 || $encounter_pay_type==1)
		{
			// OPD Refund
			$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.slno>0 and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.`user`='$user_entry' $ref_max_slno_str_less $ref_max_slno_str_grtr $date_str_a $encounter_strs_b_1 "));
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
			<td colspan="1"><?php echo $rupees_symbol.number_format($tot_amt_cash,2); ?></td>
			<td colspan="2"></td>
		</tr>
	<?php
		if($card_pin)
		{
	?>
	<tr>
		<th colspan="5"><span class="text-right">Total Card Amount</span></th>
		<td colspan="1"><?php echo $rupees_symbol.number_format($tot_amt_card,2); ?></td>
		<td colspan="2"></td>
	</tr>
	<?php }
		if($cheque_pin)
		{
	?>
	<tr>
		<th colspan="5"><span class="text-right">Total Cheque Amount</span></th>
		<td colspan="1"><?php echo $rupees_symbol.number_format($tot_amt_cheque,2); ?></td>
		<td colspan="2"></td>
	</tr>
	<?php }
		if($neft_pin)
		{
	?>
	<tr>
		<th colspan="5"><span class="text-right">Total NEFT Amount</span></th>
		<td colspan="1"><?php echo $rupees_symbol.number_format($tot_amt_neft,2); ?></td>
		<td colspan="2"></td>
	</tr>
	<?php }
		if($rtgs_pin)
		{
	?>
	<tr>
		<th colspan="5"><span class="text-right">Total RTGS Amount</span></th>
		<td colspan="1"><?php echo $rupees_symbol.number_format($tot_amt_rtgs,2); ?></td>
		<td colspan="2"></td>
	</tr>
	<?php }
		if($imps_pin)
		{
	?>
	<tr>
		<th colspan="5"><span class="text-right">Total IMPS Amount</span></th>
		<td colspan="1"><?php echo $rupees_symbol.number_format($tot_amt_imps,2); ?></td>
		<td colspan="2"></td>
	</tr>
	<?php }
		if($upi_pin)
		{
	?>
	<tr>
		<th colspan="5"><span class="text-right">Total UPI Amount</span></th>
		<td colspan="1"><?php echo $rupees_symbol.number_format($tot_amt_upi,2); ?></td>
		<td colspan="2"></td>
	</tr>
	<?php }
		if($credit_pin)
		{
	?>
	<tr>
		<th colspan="5"><span class="text-right">Total Credit Amount</span></th>
		<td colspan="1"><?php echo $rupees_symbol.number_format($tot_amt_credit,2); ?></td>
		<td colspan="2"></td>
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
			<td colspan="1"><?php echo $rupees_symbol.number_format($tot_amt,2); ?></td>
			<td colspan="2"></td>
		</tr>
	<?php //if($encounter==0 && $pay_mode=="0"){ ?>
	<?php if($pay_mode=="0"){ ?>
		<tr>
			<th colspan="5"><span class="text-right">Total Expense</span></th>
			<td colspan="1"><?php echo $rupees_symbol.number_format($tot_expense,2); ?></td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th colspan="5"><span class="text-right">Total Refund</span></th>
			<td colspan="1"><?php echo $rupees_symbol.number_format($ipd_refund+$total_refund_amount,2); ?></td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th colspan="5"><span class="text-right">Total Free</span></th>
			<td colspan="1"><?php echo $rupees_symbol.number_format($total_free_amount,2); ?></td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th colspan="5"><span class="text-right">Net Amount</span></th>
			
			<td colspan="1"><?php echo $rupees_symbol.number_format(($tot_amt-$tot_expense-$ipd_refund-$total_refund_amount-$total_free_amount-$tot_amt_credit),2); ?></td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th colspan="5"><span class="text-right">Net Cash</span></th>
			
			<td colspan="1"><?php echo $rupees_symbol.number_format(($tot_amt-$tot_expense-$ipd_refund-$total_refund_amount-$total_free_amount-$tot_amt_card-$tot_amt_cheque-$tot_amt_neft-$tot_amt_rtgs-$tot_amt_imps-$tot_amt_upi-$tot_amt_credit),2); ?></td>
			<td colspan="2"></td>
		</tr>
<?php
	}
?>
</table>
