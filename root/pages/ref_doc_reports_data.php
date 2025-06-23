<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");

$time=date("H:i:s");
$date=date("Y-m-d");

$type=$_POST['type'];

$rupees_symbol="&#x20b9; ";

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$_POST['branch_id'];
if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

if($type=="view")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$refbydoctorid=$_POST['refbydoctorid'];
	$encounter=$_POST['encounter'];
	
	$encounter_str="";
	if($encounter>0)
	{
		$encounter_str=" AND `type`='$encounter'";
	}
	
	if($refbydoctorid==0)
	{
		$q="SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`!='8' AND `branch_id`='$branch_id' $encounter_str ORDER BY `slno`";
	}
	else
	{
		$q="SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`!='8' AND `branch_id`='$branch_id' AND refbydoctorid='$refbydoctorid' $encounter_str ORDER BY `slno`";
	}
	
	//echo $q;
	$qry=mysqli_query($link,$q);
?>
	
	<span class="text-right" id="print_div">
		<a class="btn btn-excel btn-mini" href="pages/ref_doc_reports_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&refbydoctorid=<?php echo $refbydoctorid;?>&encounter=<?php echo $encounter;?>&brid=<?php echo $branch_id;?>"><i class="icon-file icon-large"></i> Export</a>
		<button type="button" class="btn btn-print btn-mini" onclick="print_page('<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $refbydoctorid;?>','<?php echo $encounter;?>','<?php echo $branch_id;?>')"><i class="icon-print icon-large"></i> Print</button>
	</span>
	<p style="margin-top: 2%;"><b>Referral Doctor Patient Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?></p>
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No</th>
				<th>Name</th>
				<th>Bill Amount</th>
				<th>Encounter</th>
				<th>Ref Doctor</th>
				<th>User</th>
				<th>Registration Time</th>
			</tr>
		</thead>
	<?php
		$n=1;
		while($uhidopd=mysqli_fetch_array($qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$uhidopd[patient_id]' "));
			
			if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			
			$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$uhidopd[refbydoctorid]' "));
			if(!$ref_doc)
			{
				$ref_doc=mysqli_fetch_array(mysqli_query($link," SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid` in ( SELECT `refbydoctorid` FROM `patient_info` WHERE `patient_id`='$uhidopd[patient_id]' ) "));
			}
			
			$bill_amount=0;
			
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$uhidopd[type]' "));
			$Encounter=$pat_typ_text['type'];
			
			if($Encounter==1)
			{
				$opd_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `tot_amount` FROM `consult_patient_payment_details` WHERE `patient_id`='$uhidopd[patient_id]' AND `opd_id`='$uhidopd[opd_id]' "));
				$bill_amount=$opd_bill["tot_amount"];
				
			}
			if($Encounter==2)
			{
				$lab_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `tot_amount` FROM `invest_patient_payment_details` WHERE `patient_id`='$uhidopd[patient_id]' AND `opd_id`='$uhidopd[opd_id]' "));
				$bill_amount=$lab_bill["tot_amount"];
				
			}
			if($Encounter==3)
			{
				//~ $ipd_bill=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `tot_bill` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhidopd[patient_id]' AND `ipd_id`='$uhidopd[opd_id]' "));
				//~ $bill_amount=$ipd_bill["tot_bill"];
				
				$uhid=$uhidopd["patient_id"];
				$ipd =$uhidopd["opd_id"];
				
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
				
				$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
				$no_of_days=$no_of_days_val["ser_quantity"];
				
				$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
				$tot_serv_amt1=$tot_serv1["tots"];
				//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
				
				$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
				$tot_serv_amt2=$tot_serv2["tots"];
				
				$ot_total=0;
				if($pat_reg["type"]==3) // If Caualty or day care and has entry ot, skip ot
				{
					// OT Charge
					$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
					$ot_total=$ot_tot_val["g_tot"];
				}
				// Total Amount
				$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
				
			}
			
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$uhidopd[type]' "));
			$Encounter=$pat_typ_text['p_type'];
			
			$u=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$uhidopd[user]'"));
			
			$tot_bill_amout+=$bill_amount;
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $pat_info['patient_id'];?></td>
			<td><?php echo $uhidopd['opd_id'];?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $rupees_symbol.number_format($bill_amount,2);?></td>
			<td><?php echo $Encounter;?></td>
			<td><?php echo $ref_doc["ref_name"];?></td>
			<td><?php echo $u['name'];?></td>
			<td><?php echo convert_date($uhidopd['date'])." ".convert_time($uhidopd['time']);?></td>
		</tr>
		<?php
			$n++;
		}
		$tot_bill_amout = money_format('%!i', $tot_bill_amout);
	?>
		<tr>
			<th colspan="3"></th>
			<th><span class="text-right">Total</span></th>
			<td><?php echo $rupees_symbol.$tot_bill_amout;?></td>
			<td colspan="4"></td>
		</tr>
	</table>
	<?php
}

if($type=="")
{
	
}
?>
