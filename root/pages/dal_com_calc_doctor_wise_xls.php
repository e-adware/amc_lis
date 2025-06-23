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
					<th rowspan="2">#</th>
					<th rowspan="2">Doctor Name</th>
					<th colspan="3" style="text-align:center;">Pathology</th>
<?php
			$d=0;
			$dept_qry=mysqli_query($link, "SELECT a.`id`,a.`category_id`,a.`name` FROM `test_department` a, `testmaster` b, `patient_test_details` c WHERE a.`category_id`=b.`category_id` AND a.`id`=b.`type_id` AND b.`testid`=c.`testid` AND b.`category_id`!='1' GROUP BY a.`id`"); // AND c.`date` BETWEEN '$date1' AND '$date2'
			while($dept_info=mysqli_fetch_array($dept_qry))
			{
			?>
					<th colspan="3" style="text-align:center;"><?php echo $dept_info["name"]; ?></th>
			<?php
				$d++;
			}
?>
					<th rowspan="2" style="text-align:center;">Total Comm Amount</th>
				</tr>
				<tr>
			<?php
				for($i=0;$i<=$d;$i++)
				{
			?>
					<th style="text-align:center;">Test No.</th>
					<th style="text-align:right;">Test Amount</th>
					<th style="text-align:right;">Comm Amount</th>
			<?php
				}
			?>
				</tr>
			</thead>
<?php
		$all_doc_comm_amount=0;
		$n=1;
		$doc_qry=mysqli_query($link, $doc_str);
		while($doc_data=mysqli_fetch_array($doc_qry))
		{
			$refbydoctorid=$doc_data["refbydoctorid"];
			$each_doc_comm_amount=0;
			
			$pat_test_qry=mysqli_query($link, "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`,a.`testid`,a.`test_rate`,a.`test_discount`,b.`type_id`,b.`category_id`,c.`type`,c.`center_no`,c.`doc_discount` FROM `patient_test_details` a, `testmaster` b, `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND b.`category_id`='1' AND b.`type_id` NOT IN($test_dept_exclude) AND c.`refbydoctorid`='$refbydoctorid' AND c.`date` BETWEEN '$date1' AND '$date2' ");
			$pat_test_num=mysqli_num_rows($pat_test_qry);
			
			$each_test_num=$each_test_amount=$each_test_comm_amount=0;
			while($pat_test=mysqli_fetch_array($pat_test_qry))
			{
				$comm_amount_test=0;
				
				$patient_id =$pat_test["patient_id"];
				$opd_id     =$pat_test["opd_id"];
				$testid     =$pat_test["testid"];
				$test_amount=$pat_test["test_rate"];
				$doc_discount=$pat_test["doc_discount"];
				
				$pat_pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT `tot_amount`,`dis_amt` FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
				
				$bill_amount=$pat_pay_det["tot_amount"];
				$discount_amount=$pat_pay_det["dis_amt"];
				
				if($doc_discount==0)
				{
					$discount_amount=0;
				}
				
				$discount_per=round(($discount_amount/$bill_amount)*100,2);
				
				$test_disacount=round(($test_amount*$discount_per)/100);
				//$test_disacount=0;
				$test_amount_after_discount=$test_amount-$test_disacount;
				
				$pat_encounter=$pat_test["type"];
				$centreno=$pat_test["center_no"];
				
				$comm_amount_test=calculate_commission_test_wise("$refbydoctorid","$testid","$test_amount_after_discount","$pat_encounter","$centreno");
				
				if($comm_amount_test>0)
				{
					$each_test_num++;
					$each_test_amount+=$test_amount_after_discount;
					$each_test_comm_amount+=$comm_amount_test;
					$each_doc_comm_amount+=$comm_amount_test;
					$all_doc_comm_amount+=$comm_amount_test;
				}
				
			}
?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $doc_data["ref_name"]; ?></td>
				<td style="text-align:right;"><?php echo $each_test_num; ?></td>
				<td style="text-align:right;"><?php echo number_format($each_test_amount,2); ?></td>
				<td style="text-align:right;"><?php echo number_format($each_test_comm_amount,2); ?></td>
<?php
		$dept_qry=mysqli_query($link, "SELECT a.`id`,a.`category_id`,a.`name` FROM `test_department` a, `testmaster` b, `patient_test_details` c WHERE a.`category_id`=b.`category_id` AND a.`id`=b.`type_id` AND b.`testid`=c.`testid` AND b.`category_id`!='1' GROUP BY a.`id`"); // AND c.`date` BETWEEN '$date1' AND '$date2'
		while($dept_info=mysqli_fetch_array($dept_qry))
		{
			$pat_test_qry=mysqli_query($link, "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`,a.`testid`,a.`test_rate`,a.`test_discount`,b.`type_id`,b.`category_id`,c.`type`,c.`center_no`,c.`doc_discount` FROM `patient_test_details` a, `testmaster` b, `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND b.`category_id`!='1' AND b.`type_id`='$dept_info[id]' AND c.`refbydoctorid`='$refbydoctorid' AND c.`date` BETWEEN '$date1' AND '$date2' ");
			$pat_test_num=mysqli_num_rows($pat_test_qry);
			
			$each_test_num=$each_test_amount=$each_test_comm_amount=0;
			while($pat_test=mysqli_fetch_array($pat_test_qry))
			{
				$comm_amount_test=0;
				
				$patient_id =$pat_test["patient_id"];
				$opd_id     =$pat_test["opd_id"];
				$testid     =$pat_test["testid"];
				$test_amount=$pat_test["test_rate"];
				$doc_discount=$pat_test["doc_discount"];
				
				$pat_pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT `tot_amount`,`dis_amt` FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
				
				$bill_amount=$pat_pay_det["tot_amount"];
				$discount_amount=$pat_pay_det["dis_amt"];
				
				if($doc_discount==0)
				{
					$discount_amount=0;
				}
				
				$discount_per=round(($discount_amount/$bill_amount)*100,2);
				
				$test_disacount=round(($test_amount*$discount_per)/100);
				//$test_disacount=0;
				$test_amount_after_discount=$test_amount-$test_disacount;
				
				$pat_encounter=$pat_test["type"];
				$centreno=$pat_test["center_no"];
				
				$comm_amount_test=calculate_commission_test_wise("$refbydoctorid","$testid","$test_amount_after_discount","$pat_encounter","$centreno");
				
				if($comm_amount_test>0)
				{
					$each_test_num++;
					$each_test_amount+=$test_amount_after_discount;
					$each_test_comm_amount+=$comm_amount_test;
					$each_doc_comm_amount+=$comm_amount_test;
					$all_doc_comm_amount+=$comm_amount_test;
				}
			}
?>
				<td style="text-align:right;"><?php echo $each_test_num; ?></td>
				<td style="text-align:right;"><?php echo number_format($each_test_amount,2); ?></td>
				<td style="text-align:right;"><?php echo number_format($each_test_comm_amount,2); ?></td>
<?php
		}
?>
				<th style="text-align:right;"><?php echo number_format($each_doc_comm_amount,2); ?></th>
			</tr>
<?php
			$n++;
		}
?>
		</table>
<?php
	}
?>
	</div>
</body>
</html>
