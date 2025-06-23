<?php
session_start();
$c_user=trim($_SESSION['emp_id']);

include'../../includes/connection.php';
require('../../includes/global.function.php');

if(!$test_dept_exclude){ $test_dept_exclude=0; }

$type=mysqli_real_escape_string($link, base64_decode($_GET['typ']));
$date1=mysqli_real_escape_string($link, base64_decode($_GET['dt1']));
$date2=mysqli_real_escape_string($link, base64_decode($_GET['dt2']));
$refbydoctorid=mysqli_real_escape_string($link, base64_decode($_GET['rdoc']));
$encounter=mysqli_real_escape_string($link, base64_decode($_GET['tp']));

$filename ="comm_report_from_".$date1."_to_".$date2.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$encounter_type=0;
if($encounter>0)
{
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
	$encounter_type=$pat_typ_text["type"];
}


?>
<html>
<head>
<title>Reports</title>

<style>
input[type="text"]
{
	border:none;
}
body {
	padding: 10px;
	font-size:13px; font-family:Arial, Helvetica, sans-serif; line-height: 18px;
}
.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}
.bline td{border-bottom:1px solid;}
*{font-size:13px;}
</style>
</head>
<body>
	<div class="container">
<?php
	if($encounter==0 || $encounter_type==2)
	{
		$doc_str="SELECT b.`refbydoctorid`, b.`ref_name` FROM `uhid_and_opdid` a, `refbydoctor_master` b, `dal_com_setup` c WHERE a.`refbydoctorid`=b.`refbydoctorid` AND a.`refbydoctorid`=c.`refbydoctorid`";
		
		if($encounter==0)
		{
			$doc_str.=" AND a.`type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`=2)";
		}
		else
		{
			$doc_str.=" AND a.`type`='$encounter'";
		}
		
		if($date1 && $date2)
		{
			$doc_str.=" AND a.`date` BETWEEN '$date1' AND '$date2'";
		}
		
		if($refbydoctorid)
		{
			//~ $doc_str.=" AND a.`refbydoctorid`='$refbydoctorid'";
			$doc_str.=" AND a.`refbydoctorid` IN($refbydoctorid)";
		}
		
		$doc_str.=" GROUP BY b.`refbydoctorid` ORDER BY b.`ref_name` ASC";
?>
		<table class="table table-condensed table-bordered table-hover">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Date</th>
					<th>UHID</th>
					<th>Bill No</th>
					<th>Name</th>
					<th style="text-align:right;">Bill Amount</th>
					<th style="text-align:right;">Discount Amount</th>
					<!--<th style="text-align:right;">After Discount</th>
					<th style="text-align:right;">Comm Amount</th>-->
					<th>Test Name</th>
					<th style="text-align:right;">Test Rate</th>
					<th style="text-align:right;">Comm Amount</th>
					<th style="text-align:right;">Total Comm</th>
				</tr>
			</thead>
<?php
		$n=1;
		$doc_qry=mysqli_query($link, $doc_str);
		while($doc_data=mysqli_fetch_array($doc_qry))
		{
			$refbydoctorid=$doc_data["refbydoctorid"];
			echo "<tr><th colspan='11' style='display:none;' id='dr$refbydoctorid'>$doc_data[ref_name]</th></tr>";
			
			
			$bill_amount_each=0;
			$discount_amount_each=0;
			$bill_amount_after_discount_each=0;
			$comm_amount_each=0;
			
			$pat_str="SELECT * FROM `uhid_and_opdid` WHERE `refbydoctorid`='$refbydoctorid'";
			
			if($encounter==0)
			{
				$pat_str.=" AND `type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`=2)";
			}
			else
			{
				$pat_str.=" AND `type`='$encounter'";
			}
			
			if($date1 && $date2)
			{
				$pat_str.=" AND `date` BETWEEN '$date1' AND '$date2'";
			}
			$pat_str.=" ORDER BY `slno` ASC";
			
			$pat_qry=mysqli_query($link, $pat_str);
			while($pat_reg=mysqli_fetch_array($pat_qry))
			{
				$patient_id=$pat_reg["patient_id"];
				$opd_id=$pat_reg["opd_id"];
				$pat_encounter=$pat_reg["type"];
				$centreno=$pat_reg["center_no"];
				$doc_discount=$pat_reg["doc_discount"];
				
				$comm_amount_bill=0;
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `patient_info` WHERE `patient_id`='$patient_id'"));
				
				$pat_pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
				
				$bill_amount=$pat_pay_det["tot_amount"];
				$discount_amount=$pat_pay_det["dis_amt"];
				
				if($doc_discount==0)
				{
					$discount_amount=0;
				}
				
				$bill_amount_after_discount=$bill_amount-$discount_amount;
				
				$batch_no=1;
				$comm_amount_bill=calculate_commission_bill_wise("$refbydoctorid","$patient_id","$opd_id","$batch_no","$pat_encounter","$centreno");
				
				$discount_per=round(($discount_amount/$bill_amount)*100,2);
				
				$pat_test_qry=mysqli_query($link, "SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");
				$pat_test_num=mysqli_num_rows($pat_test_qry);
				
				$i=1;
				while($pat_test=mysqli_fetch_array($pat_test_qry))
				{
					$comm_amount_test=0;
					
					$test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `testname`,`category_id`,`type_id` FROM `testmaster` WHERE `testid`='$pat_test[testid]'"));
					
					$testid   =$pat_test["testid"];
					$test_amount=$pat_test["test_rate"];
					
					$test_disacount=round(($test_amount*$discount_per)/100);
					//$test_disacount=0;
					$test_amount_after_discount=$test_amount-$test_disacount;
					
					$comm_amount_test=calculate_commission_test_wise("$refbydoctorid","$testid","$test_amount_after_discount","$pat_encounter","$centreno");
					$tr_display="";
					//if($pat_test_num==1 && $comm_amount_test==0 || $comm_amount_bill==0)
					if($pat_test_num==1 && $comm_amount_test==0 || $comm_amount_bill==0)
					{
						$tr_display="display:none;";
					}
					
					$td_cls="";
					if($comm_amount_test==0)
					{
						$td_cls="remove_td";
					}
					
					if($comm_amount_bill>0)
					{
?>
					<tr style="<?php echo $tr_display;?>">
					<?php if($i==1){ ?>
						<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $n; ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>"><?php echo date("d-M-Y",strtotime($pat_reg["date"])); ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $pat_reg["patient_id"]; ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $pat_reg["opd_id"]; ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $pat_info["name"]; ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>" style="text-align:right;"><?php echo $bill_amount; ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>" style="text-align:right;"><?php echo number_format($discount_amount,2); ?></td>
						<!--<td rowspan="<?php echo $pat_test_num; ?>" style="text-align:right;"><?php echo number_format($bill_amount_after_discount,2); ?></td>-->
					<?php } ?>
						<td class="<?php echo $td_cls; ?>"><span style="<?php if($comm_amount_test==0){echo "display:none;";}?>"><?php echo $test_info["testname"]; ?></span></td>
						<td class="<?php echo $td_cls; ?>" style="text-align:right;"><span style="<?php if($comm_amount_test==0){echo "display:none;";}?>"><?php echo $test_amount; ?></span></td>
						<td class="<?php echo $td_cls; ?>" style="text-align:right;"><span style="<?php if($comm_amount_test==0){echo "display:none;";}?>"><?php echo number_format($comm_amount_test,2); ?></span></td>
					<?php if($i==1){ ?>
						<td rowspan="<?php echo $pat_test_num; ?>" style="text-align:right;"><?php echo number_format($comm_amount_bill,2); ?></td>
					<?php } ?>
					</tr>
<?php
						$i++;
					}
				}
				
				$bill_amount_each+=$bill_amount;
				$discount_amount_each+=$discount_amount;
				$bill_amount_after_discount_each+=$bill_amount_after_discount;
				$comm_amount_each+=$comm_amount_bill;
				if($tr_display=="")
				{
					$n++;
				}
			}
?>
			<tr style="<?php echo $tr_display;?>">
				<th colspan="4"></th>
				<th style="text-align:right;">Total</th>
				<th style="text-align:right;"><?php echo number_format($bill_amount_each,2); ?></th>
				<th style="text-align:right;"><?php echo number_format($discount_amount_each,2); ?></th>
				<!--<th style="text-align:right;"><?php echo number_format($bill_amount_after_discount_each,2); ?></th>-->
				<td></td>
				<td></td>
				<td></td>
				<th style="text-align:right;"><?php echo number_format($comm_amount_each,2); ?></th>
			</tr>
<?php
			if($comm_amount_each>0)
			{
				echo "<script>$('#dr$refbydoctorid').show();</script>";
			}
		}
?>
		</table>
<?php
	}
?>
	</div>
</body>
</html>
