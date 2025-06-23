<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

$date1=$_GET['date1'];
$date2=$_GET['date2'];

// important
$date11=$_GET['date1'];
$date22=$_GET['date2'];

$encounter=$_GET['encounter'];
$encounter_val=$_GET['encounter'];
$pay_mode=$_GET['pay_mode'];
$user_entry=$_GET['user_entry'];
$user_val=$_GET['EpMl'];
$account_break=$_GET['account_break'];
$branch_id=$_GET['branch_id'];

if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

//~ $branch_str=" AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
//~ $branch_str_a=" AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
//~ $branch_str_b=" AND b.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";

$filename ="account_discount_report_".$date1."_to_".$date2."_".$account_break.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$close_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`='$account_break' "));
if($close_info)
{
	$break_number=$close_info["counter"]. " on ".date("d-M-Y", strtotime($close_info["date"]))." ".date("h:i A", strtotime($close_info["time"]));
}
else
{
	$break_number=0;
}

$pay_max_slno_str_less="";
$exp_max_slno_str_less="";
$ref_max_slno_str_less="";
$fre_max_slno_str_less="";

$pay_max_slno_str_grtr="";
$exp_max_slno_str_grtr="";
$ref_max_slno_str_grtr="";
$fre_max_slno_str_grtr="";

$date_str="";
$date_str_exp="";
$date_str_a="";
$date_str_b="";
$user="";
$cashier_str="All user";
if($user_entry>0)
{
	$user=" AND `user`='$user_entry'";
	$user_a=" AND a.`user`='$user_entry'";
	
	$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
	$cashier_str=$user_name["name"];
}

// Less
if($user_entry!=$c_user)
{
	// admin
	$date_str=" AND `date` BETWEEN '$date1' AND '$date2'";
	$date_str_exp=" AND `expense_date` BETWEEN '$date1' AND '$date2'";
	$date_str_a=" AND a.`date` BETWEEN '$date1' AND '$date2'";
	$date_str_b=" AND b.`date` BETWEEN '$date1' AND '$date2'";
}else
{
	if($account_break==0)
	{
		// Greater
		$check_close_account_last=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `user`='$user_entry' ORDER BY `slno` DESC limit 1 "));
		
		if($check_close_account_last)
		{
			//$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));
			
			$first_date=$check_close_account_last['date'];
			
			$date_str=" AND date>='$first_date'";
			$date_str_a=" AND a.date>='$first_date'";
			
			$pay_max_slno_grtr=$check_close_account_last['pay_id'];
			$pay_max_slno_str_grtr=" AND `pay_id`>$pay_max_slno_grtr ";
			
			$exp_max_slno_grtr=$check_close_account_last['exp_slno'];
			$exp_max_slno_str_grtr=" AND `slno`>$exp_max_slno_grtr ";
			
			// Object a
			$ref_max_slno_grtr=$check_close_account_last['refund_slno'];
			$ref_max_slno_str_grtr=" AND a.`slno`>$ref_max_slno_grtr ";
			
			$fre_max_slno_grtr=$check_close_account_last['free_slno'];
			$fre_max_slno_str_grtr=" AND a.`slno`>$fre_max_slno_grtr ";
			
		}
		else
		{
			$first_date="";
			$pay_max_slno_str_grtr=" AND `pay_id`>0 ";
			$exp_max_slno_str_grtr=" AND `slno`>0 ";
			$ref_max_slno_str_grtr=" AND a.`slno`>0 ";
			$fre_max_slno_str_grtr=" AND a.`slno`>0 ";
		}
		
	}else
	{
		$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`='$account_break' "));
		
		$last_date=$check_close_account_today["close_date"];
		
		$date_str=" AND date<='$last_date'";
		$date_str_a=" AND a.date<='$last_date'";
		
		$pay_max_slno_less=$check_close_account_today['pay_id'];
		$pay_max_slno_str_less=" AND `pay_id`<=$pay_max_slno_less ";
		
		$exp_max_slno_less=$check_close_account_today['exp_slno'];
		$exp_max_slno_str_less=" AND `slno`<=$exp_max_slno_less ";
		
		// Object a
		$ref_max_slno_less=$check_close_account_today['refund_slno'];
		$ref_max_slno_str_less=" AND a.`slno`<=$ref_max_slno_less ";
		
		$fre_max_slno_less=$check_close_account_today['free_slno'];
		$fre_max_slno_str_less=" AND a.`slno`<=$fre_max_slno_less ";
		
		// Greater
		$check_close_account_last=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`<'$account_break' AND `user`='$c_user' ORDER BY `slno` DESC limit 1 "));
		
		if($check_close_account_last)
		{
			$first_date=$check_close_account_last["close_date"];
			
			$date_str.=" AND date>='$first_date'";
			$date_str_a.=" AND a.date>='$first_date'";
			
			$pay_max_slno_grtr=$check_close_account_last['pay_id'];
			$pay_max_slno_str_grtr=" AND `pay_id`>$pay_max_slno_grtr ";
			
			$exp_max_slno_grtr=$check_close_account_last['exp_slno'];
			$exp_max_slno_str_grtr=" AND `slno`>$exp_max_slno_grtr ";
			
			// Object a
			$ref_max_slno_grtr=$check_close_account_last['refund_slno'];
			$ref_max_slno_str_grtr=" AND a.`slno`>$ref_max_slno_grtr ";
			
			$fre_max_slno_grtr=$check_close_account_last['free_slno'];
			$fre_max_slno_str_grtr=" AND a.`slno`>$fre_max_slno_grtr ";
			
		}
		else
		{
			$first_date="";
		}
	}
}

// No Break Wise Start
$date_str=" AND `date` BETWEEN '$date1' AND '$date2'";
$date_str_exp=" AND `expense_date` BETWEEN '$date1' AND '$date2'";
$date_str_a=" AND a.`date` BETWEEN '$date1' AND '$date2'";
$date_str_b=" AND b.`date` BETWEEN '$date1' AND '$date2'";

$pay_max_slno_str_less="";
$exp_max_slno_str_less="";
$ref_max_slno_str_less="";
$fre_max_slno_str_less="";

$pay_max_slno_str_grtr="";
$exp_max_slno_str_grtr="";
$ref_max_slno_str_grtr="";
$fre_max_slno_str_grtr="";
// No Break Wise End


$encounter_str="";
$encounter_pay_type=0;
if($encounter>0)
{
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
	$encounter_pay_type=$pat_typ_text["type"];
	
	$encounter_str=" AND `encounter`='$encounter'";
}

$report_name="Receipt Detail ";
//~ if($user_entry!=$c_user)
//~ {
	//~ $report_name.=" from ".convert_date($date1)." to ".convert_date($date2);
//~ }
//~ else
//~ {
	//~ $report_name.=" of Break no. ".$break_number;
//~ }
$report_name.=" from ".convert_date($date1)." to ".convert_date($date2);
?>
	<table class="table table-hover table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Bill No</th>
				<th>Transaction No</th>
				<th>Patient Name</th>
				<th>Discount Amount</th>
				<th>Discount Reason</th>
				<th>User</th>
				<th>Encounter</th>
			</tr>
		</thead>
<?php
		$total_amount=0;
		$pmode=1;
		$trans=1;
		
		$payment_mode_qry=mysqli_query($link, "SELECT `p_mode_name`,`operation` FROM `payment_mode_master` WHERE `p_mode_name` IN(SELECT DISTINCT `payment_mode` FROM `payment_detail_all` WHERE `pay_id`>0 AND (`discount_amount`>0 OR `discount_amount`<0) $encounter_str $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user $branch_str) ORDER BY `sequence` ASC");
		while($payment_mode=mysqli_fetch_array($payment_mode_qry))
		{
			$pay_str=" SELECT * FROM `payment_detail_all` WHERE `pay_id`>0 AND (`discount_amount`>0 OR `discount_amount`<0) AND `payment_mode`='$payment_mode[p_mode_name]' $encounter_str $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user $branch_str ORDER BY `pay_id` ASC ";
			
			//echo $pay_str."<br>";
			
			$pay_mode_amount=0;
			$pay=1;
			$pay_det_qry=mysqli_query($link, $pay_str);
			while($pay_det=mysqli_fetch_array($pay_det_qry))
			{
				$patient_id =$pay_det["patient_id"];
				$opd_id     =$pay_det["opd_id"];
				
				$amount=$pay_det["discount_amount"];
				
				$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
				
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$patient_id' "));
				
				$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_det[user]' "));
				
				$cheque_ref_no="";
?>
			<tr>
				<td><?php echo $trans; ?></td>
				<td><?php echo $pay_det["date"]; ?></td>
				<td><?php echo $pay_det["opd_id"]; ?></td>
				<td><?php echo $pay_det["transaction_no"].$cheque_ref_no; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo indian_currency_format($amount); ?></td>
				<td><?php echo $pay_det["discount_reason"]; ?></td>
				<td><?php echo $user_info["name"]; ?></td>
				<td><?php echo $encounter_name; ?></td>
			</tr>
<?php
				$total_amount+=$amount;
				$trans++;
				$pay++;
			}
			$pmode++;
		}
?>
		<tr>
			<th colspan="4"></th>
			<th style="text-align:right">Total Discount Amount</th>
			<th><?php echo indian_currency_format($total_amount); ?></th>
			<th></th>
			<th></th>
		</tr>
	</table>
