<?php
session_start();
$c_user=trim($_SESSION['emp_id']);

include'../../includes/connection.php';
require('../../includes/global.function.php');


$date1=$_GET['date1'];
$date2=$_GET['date2'];
$refbydoctorid=$_GET['refbydoctorid'];
$head_id=$_GET['head_id'];
$encounter=$_GET['encounter'];
$branch_id=$_GET['branch_id'];

$filename ="refer_doctor_test_report_from_".$date1."_to_".$date2.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

$branch_id=$_GET['branch_id'];
if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

$encounter_pay_type="";
$encounter_str_b="";
$encounter_str_d="";
if($encounter>0)
{
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
	$encounter_pay_type=$pat_typ_text["type"];
	
	$encounter_str_b=" AND b.`type`='$encounter'";
	$encounter_str_d=" AND d.`type`='$encounter'";
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
		<table class="table table-condensed table-bordered">
			<tr>
				<th colspan="11">
					Refer Doctor Test Report from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
				</th>
			</tr>
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No</th>
				<th>Batch No</th>
				<th>Name</th>
				<th>Ref Doctor</th>
				<th>Tests</th>
				<th>Test Amount</th>
				<th>Discount %</th>
				<th>After Discount</th>
				<!--<th>User</th>-->
				<th>Date Time</th>
			</tr>
	<?php
			$n=1;
			$tot_amount=$tot_amount_after_dis=$tot_discount_amount=0;
			if($encounter==0 || $encounter_pay_type==2)
			{
				//echo "<tr><th colspan='11'>OPD INVESTIGATION</th></tr>";
				
				$test_dept_str="";
				if($head_id>0)
				{
					if($head_id==1)
					{
						$test_dept_str="AND c.`category_id`='$head_id'";
					}else
					{
						$test_dept_str="AND c.`type_id`='$head_id'";
					}
				}
				
				$qry="SELECT a.`patient_id`, a.`opd_id`, a.`ipd_id`, a.`batch_no`, a.`date`, a.`time`, b.`refbydoctorid`, b.`user` FROM `patient_test_details` a, `uhid_and_opdid` b, `testmaster` c WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`='' AND a.`testid`=c.`testid` AND a.`date` BETWEEN '$date1' AND '$date2' $test_dept_str $encounter_str_b AND b.`branch_id`='$branch_id' ";
				
				if($refbydoctorid>0)
				{
					$qry.=" AND b.`refbydoctorid`='$refbydoctorid'";
				}
				
				$qry.=" GROUP BY a.`patient_id`, a.`opd_id`, a.`ipd_id`, a.`batch_no` ORDER BY a.`slno`";
			
				$reg_qry=mysqli_query($link,$qry);
				
				while($uhidopd=mysqli_fetch_array($reg_qry))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$uhidopd[patient_id]' "));
					if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
					
					$ref_doc=mysqli_fetch_array(mysqli_query($link," SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$uhidopd[refbydoctorid]' "));
					
					$test_dept_str="";
					if($head_id>0)
					{
						if($head_id==1)
						{
							$test_dept_str="AND b.`category_id`='$head_id'";
						}else
						{
							$test_dept_str="AND b.`type_id`='$head_id'";
						}
					}
					
					if($uhidopd["opd_id"]!="")
					{
						$q=" SELECT a.`test_rate`,b.`testname` FROM `patient_test_details` a,`testmaster` b WHERE a.`patient_id`='$uhidopd[patient_id]' AND a.`opd_id`='$uhidopd[opd_id]' AND a.`ipd_id`='$uhidopd[ipd_id]' AND a.`batch_no`='$uhidopd[batch_no]' AND a.`testid`=b.`testid` $test_dept_str ";
						
						// Discount Calculation
						$discount_cal=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhidopd[patient_id]' AND `opd_id`='$uhidopd[opd_id]' "));
						$discount_per=round(($discount_cal["dis_amt"]/$discount_cal["tot_amount"])*100);
					}else
					{
						$q=" SELECT a.`test_rate`,b.`testname` FROM `patient_test_details` a,`testmaster` b WHERE a.`patient_id`='$uhidopd[patient_id]' AND a.`opd_id`='$uhidopd[opd_id]' AND a.`ipd_id`='$uhidopd[ipd_id]' AND a.`batch_no`='$uhidopd[batch_no]' AND a.`testid`=b.`testid` $test_dept_str ";
						
						// Discount Calculation
						$total_service_cal=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot_serv` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhidopd[patient_id]' AND `ipd_id`='$uhidopd[ipd_id]' "));
						
						$discount_cal=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`discount`),0) AS `dis` FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhidopd[patient_id]' AND `ipd_id`='$uhidopd[ipd_id]' "));
						$discount_per=round(($discount_cal["dis"]/$total_service_cal["tot_serv"])*100);
					}
					
					$all_test="";
					$amount=0;
					$z=1;
					$pat_test_qry=mysqli_query($link, $q);
					while($pat_test=mysqli_fetch_array($pat_test_qry))
					{
						$all_test.=$z.". ".$pat_test["testname"]."<br>";
						$amount+=$pat_test["test_rate"];
						
						$z++;
					}
					// Discount Calculation
					$discount_amount=round(($discount_per/100)*$amount);
					$amount_after_discount=($amount-$discount_amount);
					
					if($all_test)
					{
						$u=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$uhidopd[user]'"));
						
							
						?>
						<tr>
							<td><?php echo $n;?></td>
							<td><?php echo $pat_info['patient_id'];?></td>
							<td><?php echo $uhidopd['opd_id'];?></td>
							<td><?php echo $uhidopd['batch_no'];?></td>
							<td><?php echo $pat_info['name'];?></td>
							<td><?php echo $ref_doc["ref_name"];?></td>
							<td><?php echo $all_test;?></td>
							<td><?php echo $amount;?></td>
							<td><?php echo $discount_per;?>%</td>
							<td><?php echo $amount_after_discount;?></td>
							<!--<td><?php echo $u['name'];?></td>-->
							<td><?php echo convert_date($uhidopd['date'])." ".convert_time($uhidopd['time']);?></td>
						</tr>
						<?php
							$tot_amount+=$amount;
							$tot_amount_after_dis+=$amount_after_discount;
							$tot_discount_amount+=$discount_amount;
						
						$n++;
					}
				}
			}
			if($encounter==0 || $encounter_pay_type==3)
			{
				//echo "<tr><th colspan='11'>IPD INVESTIGATION</th></tr>";
				
				$test_dept_str="";
				if($head_id>0)
				{
					if($head_id==1)
					{
						$test_dept_str="AND c.`category_id`='$head_id'";
					}else
					{
						$test_dept_str="AND c.`type_id`='$head_id'";
					}
				}
				
				$qry="SELECT a.`patient_id`, a.`opd_id`, a.`ipd_id`, a.`batch_no`, a.`date`, a.`time`, b.`refbydoctorid` FROM `patient_test_details` a, `ipd_test_ref_doc` b, `testmaster` c, `uhid_and_opdid` d WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`ipd_id` AND a.`batch_no`=b.`batch_no` AND a.`patient_id`=d.`patient_id` AND a.`ipd_id`=d.`opd_id` AND a.`opd_id`='' AND a.`testid`=c.`testid` AND a.`date` BETWEEN '$date1' AND '$date2' $test_dept_str $encounter_str_d AND d.`branch_id`='$branch_id' ";
				
				if($refbydoctorid>0)
				{
					$qry.=" AND b.`refbydoctorid`='$refbydoctorid'";
				}
				
				$qry.=" GROUP BY a.`patient_id`, a.`opd_id`, a.`ipd_id`, a.`batch_no` ORDER BY a.`slno`";
			
				$reg_qry=mysqli_query($link,$qry);
				
				while($uhidopd=mysqli_fetch_array($reg_qry))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$uhidopd[patient_id]' "));
					if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
					
					$ref_doc=mysqli_fetch_array(mysqli_query($link," SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$uhidopd[refbydoctorid]' "));
					
					$test_dept_str="";
					if($head_id>0)
					{
						if($head_id==1)
						{
							$test_dept_str="AND b.`category_id`='$head_id'";
						}else
						{
							$test_dept_str="AND b.`type_id`='$head_id'";
						}
					}
					
					if($uhidopd["opd_id"]!="")
					{
						$q=" SELECT a.`test_rate`,b.`testname` FROM `patient_test_details` a,`testmaster` b WHERE a.`patient_id`='$uhidopd[patient_id]' AND a.`opd_id`='$uhidopd[opd_id]' AND a.`ipd_id`='$uhidopd[ipd_id]' AND a.`batch_no`='$uhidopd[batch_no]' AND a.`testid`=b.`testid` $test_dept_str ";
						
						// Discount Calculation
						$discount_cal=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhidopd[patient_id]' AND `opd_id`='$uhidopd[opd_id]' "));
						$discount_per=round(($discount_cal["dis_amt"]/$discount_cal["tot_amount"])*100);
					}else
					{
						$q=" SELECT a.`test_rate`,b.`testname` FROM `patient_test_details` a,`testmaster` b WHERE a.`patient_id`='$uhidopd[patient_id]' AND a.`opd_id`='$uhidopd[opd_id]' AND a.`ipd_id`='$uhidopd[ipd_id]' AND a.`batch_no`='$uhidopd[batch_no]' AND a.`testid`=b.`testid` $test_dept_str ";
						
						// Discount Calculation
						$total_service_cal=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot_serv` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhidopd[patient_id]' AND `ipd_id`='$uhidopd[ipd_id]' "));
						
						$discount_cal=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`discount`),0) AS `dis` FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhidopd[patient_id]' AND `ipd_id`='$uhidopd[ipd_id]' "));
						$discount_per=round(($discount_cal["dis"]/$total_service_cal["tot_serv"])*100);
					}
					
					$all_test="";
					$amount=0;
					$z=1;
					$pat_test_qry=mysqli_query($link, $q);
					while($pat_test=mysqli_fetch_array($pat_test_qry))
					{
						$all_test.=$z.". ".$pat_test["testname"]."<br>";
						$amount+=$pat_test["test_rate"];
						
						$z++;
					}
					// Discount Calculation
					$discount_amount=round(($discount_per/100)*$amount);
					$amount_after_discount=($amount-$discount_amount);
					
					if($all_test)
					{
						$u=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$uhidopd[user]'"));
						
							
						?>
						<tr>
							<td><?php echo $n;?></td>
							<td><?php echo $pat_info['patient_id'];?></td>
							<td><?php echo $uhidopd['ipd_id'];?></td>
							<td><?php echo $uhidopd['batch_no'];?></td>
							<td><?php echo $pat_info['name'];?></td>
							<td><?php echo $ref_doc["ref_name"];?></td>
							<td><?php echo $all_test;?></td>
							<td><?php echo $amount;?></td>
							<td><?php echo $discount_per;?>%</td>
							<td><?php echo $amount_after_discount;?></td>
							<!--<td><?php echo $u['name'];?></td>-->
							<td><?php echo convert_date($uhidopd['date'])." ".convert_time($uhidopd['time']);?></td>
						</tr>
						<?php
							$tot_amount+=$amount;
							$tot_amount_after_dis+=$amount_after_discount;
							$tot_discount_amount+=$discount_amount;
						
						$n++;
					}
				}
			}
			
			$tot_amount = money_format('%!i', $tot_amount);
			$tot_amount_after_dis = money_format('%!i', $tot_amount_after_dis);
			$tot_discount_amount = money_format('%!i', $tot_discount_amount);
		?>
			<tr>
				<th colspan="7"><span class="text-right">Total</span></th>
				<td colspan=""><?php echo $rupees_symbol.$tot_amount;?></td>
				<td colspan=""><?php echo $rupees_symbol.$tot_discount_amount;?></td>
				<td><?php echo $rupees_symbol.$tot_amount_after_dis;?></td>
				<td colspan="2"></td>
			</tr>
		</table>
	</div>
</body>
</html>
