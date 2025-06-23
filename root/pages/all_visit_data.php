<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="load_uhid")
{
	$uhid=$_POST["uhid"];
	
	if(strlen($uhid)>2)
	{
		$pid = mysqli_query($link," SELECT `patient_id` FROM `patient_info` WHERE `patient_id` LIKE '$uhid%'");
		while($pat_uid=mysqli_fetch_array($pid))
		{
			echo "<option value='$pat_uid[patient_id]'>";
		}
	}
	
}
if($_POST["type"]=="load_all_pat_date")
{
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	
	$qry="";
	if($date1 && $date2)
	{
		//$qry=" SELECT DISTINCT `patient_id` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2' ";
		$qry=" SELECT * FROM `patient_info` WHERE `date` BETWEEN '$date1' AND '$date2' OR `patient_id` IN(SELECT `patient_id` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2') GROUP BY `patient_id` ORDER BY `slno` ASC ";
	}
	
	//echo $qry;
	
	$qq_qry=mysqli_query($link, $qry);
	
?>
	<center><button type="button" id="print_div" class="btn btn-info btn-mini" onClick="print_div()" >Print</button></center>
	<p style="margin-top: 1%;">
		<b>Patient List from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
	</p>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Patient Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Phone</th>
		</tr>
<?php
	$n=1;
	while($all_pat=mysqli_fetch_array($qq_qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
		
		if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".convert_date_g($pat_info["dob"]).")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
?>
		<tr onClick="view_all(<?php echo $pat_info["patient_id"]; ?>)" style="cursor:pointer;">
			<td><?php echo $n; ?></td>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $age; ?></td>
			<td><?php echo $pat_info["sex"]; ?></td>
			<td><?php echo $pat_info["phone"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}
if($_POST["type"]=="load_all_pat")
{
	$patient_id=$_POST["pat_uhid"];
	$pat_name=$_POST["pat_name"];
	
	if($patient_id!="" && $pat_name=="")
	{
		$qry=" SELECT a.* FROM `uhid_and_opdid` a, `patient_info` b WHERE a.`patient_id`=b.`patient_id` AND a.`patient_id`='$patient_id' ";
	}
	
	if($patient_id=="" && $pat_name!="")
	{
		$qry=" SELECT a.* FROM `uhid_and_opdid` a, `patient_info` b WHERE a.`patient_id`=b.`patient_id` AND b.`name` LIKE '%$pat_name%' ";
	}
	
	if($patient_id!="" && $pat_name!="")
	{
		$qry=" SELECT a.* FROM `uhid_and_opdid` a, `patient_info` b WHERE a.`patient_id`=b.`patient_id` AND a.`patient_id`='$patient_id' AND b.`name` LIKE '%$pat_name%' ";
	}
	
	$qry.=" ORDER BY a.`slno` ASC";
	
	//echo $qry;
?>
	<p style="margin-top: 1%;">
		<b>All Visit Details</b>
	</p>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Name</th>
			<th>Encounter</th>
			<th>Registration Date Time</th>
		</tr>
<?php
	$n=1;
	$each_visit_qry=mysqli_query($link, $qry);
	while($each_visit=mysqli_fetch_array($each_visit_qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$each_visit[patient_id]' "));
		
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$each_visit[type]' "));
		$Encounter=$pat_typ_text['p_type'];
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $each_visit["patient_id"]; ?></td>
			<td><?php echo $each_visit["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $Encounter; ?></td>
			<td>
				<?php echo convert_date_g($each_visit["date"]); ?> <?php echo convert_time($each_visit["time"]); ?>
				<button id="more_btn" class="btn btn-mini btn-info text-right" onClick="load_detail('<?php echo $each_visit["patient_id"] ?>','<?php echo $each_visit["opd_id"] ?>')">More..</button>
			</td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}

if($_POST["type"]=="load_detail")
{
	$patient_id=$_POST["uhid"];
	$pin=$_POST["pin"];
	
	$visit_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' "));
	
	$account_type=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`, `type` FROM `patient_type_master` WHERE `p_type_id`='$visit_info[type]' "));
	
	if($account_type["type"]==1) // OPD
	{
		$appointment_info=mysqli_fetch_array(mysqli_query($link, " SELECT a.*, b.`Name` FROM `appointment_book` a, `consultant_doctor_master` b WHERE a.`patient_id`='$patient_id' AND a.`opd_id`='$pin' AND a.`consultantdoctorid`=b.`consultantdoctorid` "));
		
		$week = array("","Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"); 
		$this_day=$week[$appointment_info["appointment_day"]];
		
		$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' "));
		
		if($pat_pay_detail["visit_fee"]>0)
		{
			$visit_fee_str=$pat_pay_detail["visit_fee"];
		}else
		{
			$visit_fee_str="Free";
		}
		if($pat_pay_detail["regd_fee"]>0)
		{
			$regd_fee_str=$pat_pay_detail["regd_fee"];
		}else
		{
			$regd_fee_str="Free";
		}
		if($pat_pay_detail["emergency_fee"]>0)
		{
			$emergency_fee_str=$pat_pay_detail["emergency_fee"];
		}else
		{
			$emergency_fee_str="No";
		}
?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Consultant Doctor</th>
			<td><?php echo $appointment_info["Name"]; ?></td>
			<th>Appointment Day</th>
			<td><?php echo convert_date_g($appointment_info["appointment_date"]); ?> <?php echo $this_day; ?></td>
			<th>Appointment No</th>
			<td><?php echo $appointment_info["appointment_no"]; ?></td>
		</tr>
		<tr>
			<th>Consultation Fee</th>
			<td><?php echo $visit_fee_str; ?></td>
			<th>Registration Fee</th>
			<td><?php echo $regd_fee_str; ?></td>
			<!--<th>Emergency Fee</th>
			<td><?php echo $emergency_fee_str; ?></td>-->
			<th>Total Amount</th>
			<td><?php echo $pat_pay_detail["tot_amount"]; ?></td>
		</tr>
		<tr>
			<th>Paid Amount</th>
			<td><?php echo $pat_pay_detail["advance"]; ?></td>
			<th>Discount</th>
			<td><?php echo $pat_pay_detail["dis_amt"]; ?></td>
			<th>Balance</th>
			<td><?php echo $pat_pay_detail["balance"]; ?></td>
		</tr>
	</table>
	<table class="table table-bordered table-condensed">
		<tr>
			<th colspan="6">Payment Details</th>
		</tr>
		<tr>
			<th>#</th>
			<th>Amount</th>
			<th>Payment Mode</th>
			<th>Payment Type</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
<?php
	$n=1;
	$pay_deail_qry=mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' ");
	while($pay_deail=mysqli_fetch_array($pay_deail_qry))
	{
		if($pay_deail["typeofpayment"]=="A"){ $pay_type="Advance Received"; }else{ $pay_type="Balance Received"; }
		
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_deail[user]' "));
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $pay_deail["amount"]; ?></td>
			<td><?php echo $pay_deail["payment_mode"]; ?></td>
			<td><?php echo $pay_type; ?></td>
			<td>
				<?php echo date("d-F-Y", strtotime($pay_deail["date"])); ?> 
				<?php echo date("h:i A", strtotime($pay_deail["time"])); ?> 
			</td>
			<td><?php echo $user_info["name"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
	}
	if($account_type["type"]==2)
	{
		$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' "));
		
		$pat_test_deail_qry=mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' ");
		
?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th style="width: 10%;">#</th>
			<th colspan="7">Test Name</th>
		</tr>
<?php
	$n=1;
	while($pat_test_deail=mysqli_fetch_array($pat_test_deail_qry))
	{
		$test_name=mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$pat_test_deail[testid]' "));
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td colspan="7"><?php echo $test_name["testname"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>
		<tr>
			<th>Total Amount</th>
			<td><?php echo $pat_pay_detail["tot_amount"]; ?></td>
			<th>Paid Amount</th>
			<td><?php echo $pat_pay_detail["advance"]; ?></td>
			<th>Discount</th>
			<td><?php echo $pat_pay_detail["dis_amt"]; ?></td>
			<th>Balance</th>
			<td><?php echo $pat_pay_detail["balance"]; ?></td>
		</tr>
	</table>
	<table class="table table-bordered table-condensed">
		<tr>
			<th colspan="6">Payment Details</th>
		</tr>
		<tr>
			<th>#</th>
			<th>Amount</th>
			<th>Payment Mode</th>
			<th>Payment Type</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
<?php
	$n=1;
	$pay_deail_qry=mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' ");
	while($pay_deail=mysqli_fetch_array($pay_deail_qry))
	{
		if($pay_deail["typeofpayment"]=="A"){ $pay_type="Advance Received"; }else{ $pay_type="Balance Received"; }
		
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_deail[user]' "));
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $pay_deail["amount"]; ?></td>
			<td><?php echo $pay_deail["payment_mode"]; ?></td>
			<td><?php echo $pay_type; ?></td>
			<td>
				<?php echo date("d-F-Y", strtotime($pay_deail["date"])); ?> 
				<?php echo date("h:i A", strtotime($pay_deail["time"])); ?> 
			</td>
			<td><?php echo $user_info["name"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
	}
	if($account_type["type"]==3)
	{
		$pat_discharge_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$pin' "));
		
		$timestamp = strtotime($visit_info["date"]); 
		$visit_day = date('l', $timestamp);
		
		if($pat_discharge_detail)
		{
			$dis_date=convert_date_g($pat_discharge_detail["date"]);
			$dis_time=convert_time($pat_discharge_detail["time"]);
			
			$timestamp = strtotime($pat_discharge_detail["date"]); 
			$discharged_day = date('l', $timestamp);
			
			$discharge_str=$discharged_day." ".$dis_date." ".$dis_time;
		}else
		{
			$discharge_str="Not discharged yet";
		}
		
		$pat_admit_reason=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_admit_reason` WHERE `patient_id`='$patient_id' AND `ipd_id`='$pin' "));
		$pat_admit_reason_str="N/A";
		if($pat_admit_reason["admit_reason"])
		{
			$pat_admit_reason_str=$pat_admit_reason["admit_reason"];
		}else
		{
			$pat_admit_reason_str="";
			$pat_complain_qry=mysqli_query($link, " SELECT * FROM `ipd_pat_complaints` WHERE `patient_id`='$patient_id' AND `ipd_id`='$pin' ");
			while($pat_complain=mysqli_fetch_array($pat_complain_qry))
			{
				$pat_admit_reason_str.=$pat_complain["comp_one"]." For ".$pat_complain["comp_two"]." ".$pat_complain["comp_three"]."<br>";
			}
			if(!$pat_admit_reason_str)
			{
				$pat_admit_reason_str="N/A";
			}
		}
		
		// Bed Details
		$bed_num=1;
		$pat_bed_detail_qry=mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$pin' AND `alloc_type`='1' ORDER BY `slno`");
		while($pat_bed_detail=mysqli_fetch_array($pat_bed_detail_qry))
		{
			$ward=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ward_master` WHERE `ward_id`='$pat_bed_detail[ward_id]' "));
			
			$bed=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `bed_master` WHERE `bed_id`='$pat_bed_detail[bed_id]' "));
			
			$room=mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `ward_id`='$pat_bed_detail[ward_id]' AND `room_id`='$bed[room_id]' "));
			
			if($ward["floor_name"]){ $floor_name_str="(".$ward["floor_name"].")"; }else{ $floor_name_str=""; }
			
			if($bed_num>1)
			{
				$trans_date=convert_date_g($pat_bed_detail["date"]);
				$trans_time=convert_time($pat_bed_detail["time"]);
				
				$timestamp = strtotime($pat_discharge_detail["date"]); 
				$trans_day = date('l', $timestamp);
				
				$trans_str=$trans_day." ".$trans_date." ".$trans_time;
				
				$pat_bed_detail_str.="<br><b>Transferred To</b> ".$ward["name"].$floor_name_str." Room No: ".$room["room_no"]." Bed No: ".$bed["bed_no"]." <b>On</b> ".$trans_str;
			}else
			{
				$pat_bed_detail_str=$ward["name"].$floor_name_str." Room No: ".$room["room_no"]." Bed No: ".$bed["bed_no"];
			}
			
			$bed_num++;
		}
?>
	<table class="table table-bordered table-condensed">
<?php
		if($visit_info["type"]==3) // IPD
		{
?>
		<tr>
			<th>Adminisnion Date Time</th>
			<td><?php echo $visit_day; ?> <?php echo convert_date_g($visit_info["date"]); ?> <?php echo convert_time($visit_info["time"]); ?> </td>
			<th>Discharged Date Time</th>
			<td><?php echo $discharge_str;?></td>
		</tr>
		<tr>
			<th>Reason For Admission</th>
			<td colspan="3"><?php echo $pat_admit_reason_str;?></td>
		</tr>
		<tr>
			<th>Admitted In</th>
			<td colspan="3"><?php echo $pat_bed_detail_str;?></td>
		</tr>
<?php
		}	
		$pat_delivery_qry=mysqli_query($link, " SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$patient_id' AND `ipd_id`='$pin' ");
		$pat_delivery_num=mysqli_num_rows($pat_delivery_qry);
		if($pat_delivery_num>0)
		{
			while($pat_delivery=mysqli_fetch_array($pat_delivery_qry))
			{
				$timestamp = strtotime($pat_delivery["dob"]); 
				$born_day = date('l', $timestamp);
				
				if($pat_delivery["sex"]=="Male"){ $sex_str="boy"; }
				if($pat_delivery["sex"]=="Female"){ $sex_str="girl"; }
				$pat_delivery_str.="A baby ".$sex_str." was born on ".convert_date_g($pat_delivery["dob"])." ".convert_time($pat_delivery["born_time"])." (".$born_day.").<br>";
			}
?>
			<tr>
				<th>Delivery Details</th>
				<td colspan="3">
					<?php echo $pat_delivery_str;?>
				</td>
			</tr>
	<?php
		}
		echo "<tr>";
		echo "<th>Services</th>";
		echo "<td colspan='3'>";
		echo "<div class='ScrollStyle'>";
		echo "<table class='table table-condensed table-bordered'>";
		$i=1;
		$serv_qry=mysqli_query($link,"select distinct group_id from ipd_pat_service_details where patient_id='$patient_id' and ipd_id='$pin' and group_id!='141' ");
		while($pat_serv=mysqli_fetch_array($serv_qry))
		{
			$group=mysqli_fetch_array(mysqli_query($link,"select * from charge_group_master where group_id='$pat_serv[group_id]'"));
			echo "<tr><th>$i. $group[group_name]</th></tr>";
			$serv_detail_qry=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$patient_id' and ipd_id='$pin' and group_id='$pat_serv[group_id]'");
			$j=1;
			while($serv_detail=mysqli_fetch_array($serv_detail_qry))
			{
				echo "<tr><td> &nbsp;&nbsp; $j. $serv_detail[service_text]</td></tr>";
				$j++;
			}
			
			$i++;
		}
		
		// OT Charge
		$to_serv_detail_qry=mysqli_query($link,"select * from ot_pat_service_details where patient_id='$patient_id' and ipd_id='$pin'");
		$to_serv_detail_num=mysqli_fetch_array($to_serv_detail_qry);
		if($to_serv_detail_num)
		{
			echo "<tr><th>$i. OT Charge</th></tr>";
			$j=1;
			while($to_serv_detail=mysqli_fetch_array($to_serv_detail_qry))
			{
				echo "<tr><td> &nbsp;&nbsp; $j. $to_serv_detail[service_text]</td></tr>";
				$j++;
			}		
			$i++;
		}
		
		// Baby Charges
		
		$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$patient_id' and ipd_id='$pin' "));
		if($delivery_check)
		{
			$i=1;
			$baby_serv_qry=mysqli_query($link,"select distinct group_id from ipd_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' and group_id!='141' ");
			$baby_serv_num=mysqli_num_rows($baby_serv_qry);
			if($baby_serv_num>0)
			{
				echo "<tr><th>Baby's Services</th></tr>";
				while($pat_serv=mysqli_fetch_array($baby_serv_qry))
				{
					$group=mysqli_fetch_array(mysqli_query($link,"select * from charge_group_master where group_id='$pat_serv[group_id]'"));
					echo "<tr><th>$i. $group[group_name]</th></tr>";
					$serv_detail_qry=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' and group_id='$pat_serv[group_id]'");
					$j=1;
					while($serv_detail=mysqli_fetch_array($serv_detail_qry))
					{
						echo "<tr><td> &nbsp;&nbsp; $j. $serv_detail[service_text]</td></tr>";
						$j++;
					}
					
					$i++;
				}
			}
			
			// OT Charge
			$to_serv_detail_qry=mysqli_query($link,"select * from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' ");
			$to_serv_detail_num=mysqli_fetch_array($to_serv_detail_qry);
			if($to_serv_detail_num)
			{
				echo "<tr><th>$i. OT Charge</th></tr>";
				$j=1;
				while($to_serv_detail=mysqli_fetch_array($to_serv_detail_qry))
				{
					echo "<tr><td> &nbsp;&nbsp; $j. $to_serv_detail[service_text]</td></tr>";
					$j++;
				}		
				$i++;
			}
		}
		
		echo "</table>";
		echo "</div>";
		echo "</td>";
		echo "</tr>";
		
		// Bill Details
		$uhid=$patient_id;
		$ipd=$pin;
		$baby_serv_tot=0;
		$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
		if($delivery_check)
		{
			$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
			$baby_serv_tot=$baby_tot_serv["tots"];
			
			// OT Charge Baby
			$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
			$baby_ot_total=$baby_ot_tot_val["g_tot"];
		}
		
		$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
		$no_of_days=$no_of_days_val["ser_quantity"];
		
		$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
		$tot_serv_amt1=$tot_serv1["tots"];
		
		$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
		$tot_serv_amt2=$tot_serv2["tots"];
		
		// OT Charge
		$grp_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
		$grp_tot=$grp_tot_val["g_tot"];
		
		$tot_serv_amt=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$grp_tot+$baby_ot_total;
		
		$adv_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as advs FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Advance' "));
		$adv_serv_amt=$adv_serv["advs"];
		
		$bal_serv_amt=$bal_serv["advs"];
		
		$pat_refund=mysqli_fetch_array(mysqli_query($link," SELECT sum(`refund`) as rfnd FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
		$pat_refund_amt=$pat_refund["rfnd"];
		
		$final_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as final,sum(`discount`) as discnt FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
		$final_serv_amt=$final_serv["final"];
		$adv_serv_dis=$final_serv["discnt"];
		
		//echo number_format(($tot_serv_amt-$adv_serv_amt-$bal_serv_amt-$final_serv_amt-$adv_serv_dis+$pat_refund_amt),2);
		
		$delivery_check_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE baby_uhid='$uhid' and baby_ipd_id='$ipd' "));
		if($delivery_check_val)
		{
			$final_serv_rel=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE patient_id='$delivery_check_val[patient_id]' and ipd_id='$delivery_check_val[ipd_id]' and pay_type='Final' "));
			
			if($final_serv_rel)
			{
				$pending_amount=0;
			}else
			{
				$pending_amount=($tot_serv_amt-$adv_serv_amt-$final_serv_amt-$adv_serv_dis+$pat_refund_amt);
			}

		}else
		{
			$pending_amount=($tot_serv_amt-$adv_serv_amt-$final_serv_amt-$adv_serv_dis+$pat_refund_amt);
		}
	?>
		<tr>
			<th>Total Amount</th>
			<td><?php echo number_format($tot_serv_amt,2); ?></td>
			<th>Paid Amount</th>
			<td><?php echo number_format($adv_serv_amt+$final_serv_amt,2); ?></td>
		</tr>
		<tr>
			<th>Discount</th>
			<td><?php echo number_format($adv_serv_dis,2); ?></td>
			<th>Balance</th>
			<td><?php echo number_format($pending_amount,2); ?></td>
		</tr>
	<?php
		if($pat_refund_amt>0)
		{
			echo "<tr><th>Refunded</th><td colspan='3'>$pat_refund_amt</td></tr>";
		}
?>
	</table>
	<table class="table table-bordered table-condensed">
		<tr>
			<th colspan="6">Payment Details</th>
		</tr>
		<tr>
			<th>#</th>
			<th>Amount</th>
			<th>Payment Mode</th>
			<th>Payment Type</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
<?php
	$n=1;
	$pay_deail_qry=mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$pin' ");
	while($pay_deail=mysqli_fetch_array($pay_deail_qry))
	{
		if($pay_deail["typeofpayment"]=="A"){ $pay_type="Advance Received"; }else{ $pay_type="Balance Received"; }
		
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_deail[user]' "));
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $pay_deail["amount"]; ?></td>
			<td><?php echo $pay_deail["pay_mode"]; ?></td>
			<td><?php echo $pay_deail["pay_type"]; ?></td>
			<td>
				<?php echo date("d-F-Y", strtotime($pay_deail["date"])); ?> 
				<?php echo date("h:i A", strtotime($pay_deail["time"])); ?> 
			</td>
			<td><?php echo $user_info["name"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
	}
?>
	</table>
<?php
}

?>
