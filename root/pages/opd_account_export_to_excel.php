<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

?>
<html>
<head>
<title>OPD Acount</title>

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
	include'../../includes/connection.php';

	$date1=$_GET['date1'];
	$date2=$_GET['date2'];

	$filename ="opd_account_".$date1."_to_".$date2.".xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);

	$con_cod_id=$_GET['con_cod_id'];
	$payment_mode=$_GET['payment_mode'];
	$dept_id=$_GET['dept_id'];
	$visit_type=$_GET['visit_type'];
	$patient_type=$_GET['patient_type'];
	$user_entry=$_GET['user_entry'];
	
	$branch_id=$_GET['branch_id'];
	if(!$branch_id)
	{
		$branch_id=$emp_info["branch_id"];
	}

	$branch_str=" AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
	$branch_str_a=" AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
	$branch_str_b=" AND b.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
	
	$qry="SELECT a.* FROM `appointment_book` a, `uhid_and_opdid` b, `payment_detail_all` c WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND c.`patient_id`=b.`patient_id` AND c.`opd_id`=b.`opd_id` AND b.`type`=1 AND b.`branch_id`='$branch_id' AND c.`payment_type`='Advance' AND a.`date` between '$date1' and '$date2'";
	
	if($con_cod_id>0)
	{
		$qry.=" AND a.`consultantdoctorid`='$con_cod_id'";
	}
	
	$user_str="";
	if($user_entry>0)
	{
		$qry.=" AND b.`user`='$user_entry'";
		
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
		$user_name=$user_info["name"];
	}
	else
	{
		$user_name="All";
	}
	
	$payment_mode_str_a="";
	if($payment_mode!="")
	{
		$payment_mode_str_a=" AND a.`payment_mode`='$payment_mode'";
		
		$qry.=" AND c.`payment_mode`='$payment_mode'";
	}
	
	$qry.=" GROUP BY a.`opd_id`";
	
	//echo $qry;
	
	//~ $all_pay_mode=array();
	//~ $p_mode_qry=mysqli_query($link, "SELECT DISTINCT a.`payment_mode` FROM `payment_detail_all` a, `payment_mode_master` b WHERE a.`payment_mode`=b.`p_mode_name` AND b.`operation`=1 AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='1' AND `branch_id`='$branch_id') $payment_mode_str_a ORDER BY b.`sequence` ASC");
	//~ $p_mode_num=mysqli_num_rows($p_mode_qry);
	//~ while($p_mode=mysqli_fetch_array($p_mode_qry))
	//~ {
		//~ $all_pay_mode[]=$p_mode["payment_mode"];
	//~ }
	
	//~ $all_pay_modes=implode(",",$all_pay_mode);
	
	$pat_reg_qry=mysqli_query($link, $qry );
?>
	<p style="margin-top: 2%;"><b>OPD Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?></p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<!--<th>UHID</th>-->
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Phone No</th>
			<th>Consultant Doctor</th>
			<!--<th>Department</th>-->
			<th>Consultant Fee</th>
			<th>Registration Fee</th>
			<!--<th>Emergency Fee</th>-->
			<!--<th>Cross Consultation Fee</th>-->
			<th>Discount</th>
			<th>Date</th>
			<th>User</th>
		</tr>
	<?php
		$n=1;
		$tot_con="";
		$tot_reg="";
		$tot_emr="";
		$tot_dis="";
		$tot_cross="";
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name`,`phone` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
			
			$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]' "));
			
			$dept_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$con_doc[dept_id]' "));
			
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
			
			$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
			if($pat_reg["emergency"]>0)
			{
				$emrgncy_fee=$pat_pay_detail["emergency_fee"];
			}else
			{
				$emrgncy_fee=0;
			}
			$cross_consult=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cross_consultation` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
			if($cross_consult["amount"]>0)
			{
				$cross_consult_fee=$cross_consult["amount"];
			}else
			{
				$cross_consult_fee=0;
			}
	?>
		<tr>
			<td><?php echo $n; ?></td>
			<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
			<td><?php echo $pat_reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $pat_info["phone"]; ?></td>
			<td><?php echo $con_doc["Name"]; ?></td>
			<!--<td><?php echo $dept_name["name"]; ?></td>-->
			<td><?php echo number_format($pat_pay_detail["visit_fee"],2); ?></td>
			<td><?php echo number_format($pat_pay_detail["regd_fee"],2); ?></td>
			<!--<td><?php echo number_format($emrgncy_fee,2); ?></td>-->
			<!--<td><?php echo number_format($cross_consult_fee,2); ?></td>-->
			<td><?php echo number_format($pat_pay_detail["dis_amt"],2); ?></td>
			<td><?php echo convert_date($pat_reg["date"]); ?></td>
			<td><?php echo $user_name["name"]; ?></td>
		</tr>
		<?php
				$tot_con=$tot_con+$pat_pay_detail["visit_fee"];
				$tot_reg=$tot_reg+$pat_pay_detail["regd_fee"];
				$tot_dis=$tot_dis+$pat_pay_detail["dis_amt"];
				$tot_emr=$tot_emr+$emrgncy_fee;
				//$tot_cross=$tot_cross+$cross_consult_fee;
				$n++;
			}
		?>
		<tr>
			<th colspan="4"></th>
			<th colspan=""><span class="text-right">Total</span></th>
			<td><?php echo number_format($tot_con,2); ?></td>
			<td colspan=""><?php echo number_format($tot_reg,2); ?></td>
			<!--<td colspan=""><?php echo number_format($tot_emr,2); ?></td>-->
			<!--<td colspan=""><?php echo number_format($tot_emr,2); ?></td>-->
			<td colspan=""><?php echo number_format($tot_dis,2); ?></td>
			<th colspan="2"></th>
		</tr>
		<tr>
			<th colspan="4"></th>
			<th colspan=""><span class="text-right">Grand total</span></th>
			<td colspan=""><?php echo number_format(($tot_con+$tot_reg+$tot_emr+$tot_cross-$tot_dis),2); ?></td>
			<th colspan="6"></th>
		</tr>
	</table>
</div>
</body>
</html>
