<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

$branch_id=$_POST['branch_id'];
if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

$type=$_POST["type"];

$date1=$_POST["date1"];
$date2=$_POST["date2"];

$search_data=$_POST["search_data"];

if($type=="patient_cancel")
{
	$str="SELECT a.*,b.`type`,b.`date` AS `reg_date` FROM `patient_cancel_reason` a, `uhid_and_opdid_cancel` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND b.`branch_id`='$branch_id'";
	
	if($search_data)
	{
		$str.=" AND (b.`patient_id` LIKE '$search_data' OR b.`opd_id` LIKE '$search_data')";
	}
	else
	{
		$str.=" AND a.`date` BETWEEN '$date1' AND '$date2'";
	}
	
	$str.=" ORDER BY a.`slno` ASC";
	
	$qry=mysqli_query($link, $str);
	
?>
	<span class="print_div">
		<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('<?php echo $type;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $search_data;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</span>
	
	<table class="table table-bordered text-center">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No.</th>
				<th>Patient Name</th>
				<th>Bill Amount</th>
				<th>Reg Date</th>
				<th>Request By</th>
				<th>Cancel Date</th>
				<th>Cancel Time</th>
				<th>Cancel Reason</th>
				<th>Cancel User</th>
				<!--<th>Encounter</th>-->
			</tr>
		</thead>
<?php
	$n=1;
	while($data=mysqli_fetch_array($qry))
	{
		$uhid=$data["patient_id"];
		$opd_id=$data["opd_id"];
		
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$data[user]' "));
		
		$request_user_info=mysqli_fetch_array(mysqli_query($link, " SELECT a.`name` FROM `employee` a, `cancel_request` b WHERE a.`emp_id`=b.`user` AND b.`patient_id`='$uhid' AND b.`opd_id`='$opd_id' "));
		
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$data[patient_id]' "));
		
		$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$data[type]' "));
		
		if($prefix_det["type"]==1)
		{
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_patient_payment_details_cancel` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
			
			$bill_amount=$pat_pay_det["tot_amount"];
		}
		if($prefix_det["type"]==2)
		{
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_patient_payment_details_cancel` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
			
			$bill_amount=$pat_pay_det["tot_amount"];
		}
		if($prefix_det["type"]==3)  // Other
		{
			$baby_serv_tot=0;
			$baby_ot_total=0;
			$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det_cancel` WHERE patient_id='$uhid' and ipd_id='$opd_id' ");
			while($delivery_check=mysqli_fetch_array($delivery_qry))
			{
				$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details_cancel` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_serv_tot+=$baby_tot_serv["tots"];
				
				// OT Charge Baby
				$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details_cancel where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_ot_total+=$baby_ot_tot_val["g_tot"];
				
			}
			
			$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details_cancel where patient_id='$uhid' and ipd_id='$opd_id' and `group_id`='141' "));
			$no_of_days=$no_of_days_val["ser_quantity"];
			
			$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details_cancel` WHERE patient_id='$uhid' and ipd_id='$opd_id' and group_id='141' "));
			$tot_serv_amt1=$tot_serv1["tots"];
			//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
			
			$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details_cancel` WHERE patient_id='$uhid' and ipd_id='$opd_id' and group_id!='141' "));
			$tot_serv_amt2=$tot_serv2["tots"];
			
			// OT Charge
			$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details_cancel where patient_id='$uhid' and ipd_id='$opd_id' "));
			$ot_total=$ot_tot_val["g_tot"];
			
			// Total
			$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
		}
		if(!$bill_amount){ $bill_amount="0.00"; }
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $data["patient_id"]; ?></td>
			<td><?php echo $data["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $bill_amount; ?></td>
			<td><?php echo date("d-M-Y", strtotime($data["reg_date"])); ?></td>
			<td><?php echo $request_user_info["name"]; ?></td>
			<td><?php echo date("d-M-Y", strtotime($data["date"])); ?></td>
			<td><?php echo date("h:i:s A", strtotime($data["time"])); ?></td>
			<td><?php echo $data["reason"]; ?></td>
			<td><?php echo $user_info["name"]; ?></td>
			<!--<td><?php echo $prefix_det["p_type"]; ?></td>-->
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}
if($type=="payment_cancel")
{
	$str="SELECT a.*,b.`type`,b.`date` AS `reg_date` FROM `payment_detail_all_delete` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`branch_id`='$branch_id'";
	
	if($search_data)
	{
		$str.=" AND (b.`patient_id` LIKE '%$search_data%' OR b.`opd_id` LIKE '%$search_data%')";
	}
	else
	{
		$str.=" AND a.`del_date` BETWEEN '$date1' AND '$date2'";
	}
	
	$str.=" ORDER BY a.`slno` ASC";
	
	$qry=mysqli_query($link, $str);
	
?>
	<span class="print_div">
		<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('<?php echo $type;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $search_data;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</span>
	
	<table class="table table-bordered text-center">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No.</th>
				<th>Patient Name</th>
				<th>Cancel Amount</th>
				<th>Cancel Date</th>
				<th>Cancel Time</th>
				<th>Cancel Reason</th>
				<th>Cancel User</th>
				<th>Encounter</th>
			</tr>
		</thead>
<?php
	$n=1;
	while($data=mysqli_fetch_array($qry))
	{
		$uhid=$data["patient_id"];
		$opd_id=$data["opd_id"];
		
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$data[del_user]' "));
		
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$data[patient_id]' "));
		
		$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$data[type]' "));
		
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $data["patient_id"]; ?></td>
			<td><?php echo $data["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $data["amount"]; ?></td>
			<td><?php echo date("d-M-Y", strtotime($data["date"])); ?></td>
			<td><?php echo date("h:i:s A", strtotime($data["time"])); ?></td>
			<td><?php echo $data["del_reason"]; ?></td>
			<td><?php echo $user_info["name"]; ?></td>
			<td><?php echo $prefix_det["p_type"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}
?>
