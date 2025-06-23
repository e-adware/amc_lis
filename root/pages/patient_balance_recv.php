<?php
include("../../includes/connection.php");
include("../../includes/idgeneration.function.php");

$date=date("Y-m-d");
$time=date('H:i:s');

$tax_deduct=mysqli_real_escape_string($link, $_POST['tax_deduct']);
$discount=mysqli_real_escape_string($link, $_POST['discount']);
$discount_reason=mysqli_real_escape_string($link, $_POST['discount_reason']);
$now_pay=mysqli_real_escape_string($link, $_POST['now_pay']);
$uhid=mysqli_real_escape_string($link, $_POST['uhid']);
$opd_id=mysqli_real_escape_string($link, $_POST['opd']);
$pay_mode=mysqli_real_escape_string($link, $_POST['pay_mode']);
$cheque_ref_no=mysqli_real_escape_string($link, $_POST['cheque_ref_no']);
$user=mysqli_real_escape_string($link, $_POST['user']);

$tax_deduct=0;
$refund_amount=0;

$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($pat_pay_det)
{
	$bill_amount=$pat_pay_det["tot_amount"];
	$past_discount=$pat_pay_det["dis_amt"];
	$past_advance=$pat_pay_det["advance"];
	$past_balance=$pat_pay_det["balance"];
	
	$balance=$past_balance-$tax_deduct-$discount-$now_pay;
	
	$total_advance=$past_advance+$now_pay;
	
	$dis_amt=$past_discount+$discount;
	
	if($balance>=0)
	{
		$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
		$bill_name=$pat_typ_text["bill_name"];
		
		$bill_no=generate_bill_no($bill_name,2);
		
		if(mysqli_query($link, " INSERT INTO `invest_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','B','$now_pay','$balance','$discount','$discount_reason','$refund_amount','','$cheque_ref_no','$user','$time','$date') "))
		{
			mysqli_query($link, "update invest_patient_payment_details set dis_amt='$dis_amt',advance='$total_advance',balance='$balance' where patient_id='$uhid' and opd_id='$opd_id'");
			
			echo "1";
		}
		else
		{
			echo "202";
		}
	}
	else
	{
		echo "203";
	}
}
else
{
	echo "204";
}

?>
