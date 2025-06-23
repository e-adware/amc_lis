<?php
include('../../includes/connection.php');

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d M Y', $timestamp);
	return $new_date;
}

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$con_cod_id=$_GET['con_cod_id'];
$dept_id=$_GET['dept_id'];

$filename ="doctor_account_report.xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
<p style="margin-top: 2%;" id="print_div">
	<b>Doctor Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
</p>
<table class="table table-hover">
	<tr>
		<th>#</th>
		<!--<th>UHID</th>-->
		<th>PIN</th>
		<th>Patient Name</th>
		<th>Doctor</th>
		<th>Fees Type</th>
		<th>Amount</th>
		<th>Date</th>
		<th>User</th>
	</tr>
<?php
	$tot_con=0;
	$casu_tot_bill_amout=0;
	$ipd_tot_bill_amout=0;
	$daycare_tot_bill_amout=0;
	$dental_tot_bill_amout=0;
	$dialysis_tot_bill_amout=0;
	$baby_tot_bill_amout=0;
	$grand_total=0;
	
	if($dept_id==0 || $dept_id==1)
	{
		if($con_cod_id==0)
		{
			$opd_qry=" SELECT * FROM `appointment_book` WHERE `date` between '$date1' and '$date2' ";
		}else
		{
			$opd_qry=" SELECT * FROM `appointment_book` WHERE `date` between '$date1' and '$date2' and `consultantdoctorid`='$con_cod_id' ";
		}
		$opd_qry.=" ORDER BY `slno` ASC";
	
		//echo $opd_qry;
		$pat_reg_qry=mysqli_query($link, $opd_qry );
		
		$pat_reg_num=mysqli_num_rows($pat_reg_qry);
		if($pat_reg_num!=0)
		{
			echo "<tr><th>OPD Account</th><th colspan='8'></th></tr>";
	?>	
		<?php
			$n=1;
			
			while($pat_reg=mysqli_fetch_array($pat_reg_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
				
				$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]' "));
				
				$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
				
				$opd_pat_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_payment_free` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
				if($opd_pat_fee)
				{
					$visitt_fee_opd=0;
				}else
				{
					$visitt_fee_opd=$pat_pay_detail["visit_fee"];
				}
				
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
				<td><?php echo $pat_reg["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $con_doc["Name"]; ?></td>
				<td>Consultation Fees</td>
				<td><?php echo "&#x20b9; ".number_format($visitt_fee_opd,2); ?></td>
				<td><?php echo convert_date($pat_reg["date"]); ?></td>
				<td><?php echo $user_name["name"]; ?></td>
			</tr>
			<?php
					$tot_con=$tot_con+$visitt_fee_opd;
					$n++;
				}
			?>
			<tr>
				<th colspan="5"><span class="text-right">OPD Total</span></th>
				<td colspan="3"><?php echo "&#x20b9; ".number_format($tot_con,2); ?></td>
			</tr>
<?php
			$grand_total+=$tot_con;
		}
	}
	if($dept_id==0 || $dept_id==3)
	{
		if($con_cod_id==0)
		{
			$ipd_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=3 AND a.`date` between '$date1' AND '$date2' ";
		}else
		{
			$ipd_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=3 AND a.`date` between '$date1' AND '$date2' AND a.`consultantdoctorid`='$con_cod_id' ";
		}
		$opd_qry.=" ORDER BY b.`slno` ASC";
	
		//echo $ipd_qry;
		$pat_reg_qry=mysqli_query($link, $ipd_qry );
		
		$pat_reg_num=mysqli_num_rows($pat_reg_qry);
		if($pat_reg_num!=0)
		{
			echo "<tr><th>IPD Account</th><th colspan='8'></th></tr>";
	?>	
		<?php
			$n=1;
			$ipd_tot_bill_amout=0;
			$ot_ipd_ids="";
			while($pat_reg=mysqli_fetch_array($pat_reg_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
				
				$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]' "));
				
				$service=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$pat_reg[service_id]' "));
				
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
				
				$style="";
				$show_service="Yes";
				if($pat_reg["schedule_id"]>0)
				{
					$ot_ipd_ids.=$pat_reg["ipd_id"]."@@";
					
					$cons=mysqli_fetch_array(mysqli_query($link, "SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]'"));
					if($cons)
					{
						$empp=$cons['emp_id'];
						
						$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `schedule_id`='$pat_reg[schedule_id]' AND `emp_id`='$empp' AND `ot_group_id`='155' AND `ot_service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_bill["service_text"];
						$bill_amount=$ipd_service_bill["amount"];
						$style="";
					}
					else
					{
						$empp=$pat_reg['consultantdoctorid'];
						$style="display:none;";
						$bill_amount=0;
						
						$ipd_service_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_det["service_text"];
					}
				}
				else
				{
					if (strpos($ot_ipd_ids, $pat_reg["ipd_id"]) !== false) {
						$show_service="No";
					}else
					{
						$ot_entry_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' "));
						if($ot_entry_check)
						{
							$show_service="No";
						}else
						{
							$show_service="Yes";
							
							$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT `amount` FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
							$bill_amount=$ipd_service_bill["amount"];
						}
					}
				}
				if($show_service=="Yes")
				{
					if($ipd_service_bill["amount"]>0)
					{
		?>
				<tr>
					<td><?php echo $n; ?></td>
					<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
					<td><?php echo $pat_reg["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $con_doc["Name"]; ?></td>
					<td><?php echo $service["charge_name"]; ?></td>
					<td><?php echo "&#x20b9; ".number_format($bill_amount,2); ?></td>
					<td><?php echo convert_date($pat_reg["date"]); ?></td>
					<td><?php echo $user_name["name"]; ?></td>
				</tr>
		<?php
						$ipd_tot_bill_amout+=$bill_amount;
						$n++;
					}
				}
			}
		?>
			<tr>
				<th colspan="5"><span class="text-right">IPD Total</span></th>
				<td colspan="3"><?php echo "&#x20b9; ".number_format($ipd_tot_bill_amout,2); ?></td>
			</tr>
<?php
			$grand_total+=$ipd_tot_bill_amout;
		}
	}
	if($dept_id==0 || $dept_id==4)
	{
		if($con_cod_id==0)
		{
			$casu_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=4 AND a.`date` between '$date1' AND '$date2' ";
		}else
		{
			$casu_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=4 AND a.`date` between '$date1' AND '$date2' AND a.`consultantdoctorid`='$con_cod_id' ";
		}
		$casu_qry.=" ORDER BY b.`slno` ASC";
	
		//echo $casu_qry;
		$pat_reg_qry=mysqli_query($link, $casu_qry );
		
		$pat_reg_num=mysqli_num_rows($pat_reg_qry);
		if($pat_reg_num!=0)
		{
			echo "<tr><th>Casualty Account</th><th colspan='8'></th></tr>";
	?>	
		<?php
			$i=$n=1;
			$casu_tot_bill_amout=0;
			$ot_ipd_ids="";
			while($pat_reg=mysqli_fetch_array($pat_reg_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
				
				$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]' "));
				
				$service=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$pat_reg[service_id]' "));
				
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
				
				$style="";
				$show_service="Yes";
				if($pat_reg["schedule_id"]>0)
				{
					$ot_ipd_ids.=$pat_reg["ipd_id"]."@@";
					
					$cons=mysqli_fetch_array(mysqli_query($link, "SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]'"));
					if($cons)
					{
						$empp=$cons['emp_id'];
						
						$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `schedule_id`='$pat_reg[schedule_id]' AND `emp_id`='$empp' AND `ot_group_id`='155' AND `ot_service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_bill["service_text"];
						$bill_amount=$ipd_service_bill["amount"];
						$style="";
					}
					else
					{
						$empp=$pat_reg['consultantdoctorid'];
						$style="display:none;";
						$bill_amount=0;
						
						$ipd_service_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_det["service_text"];
					}
				}
				else
				{
					if (strpos($ot_ipd_ids, $pat_reg["ipd_id"]) !== false) {
						$show_service="No";
					}else
					{
						$ot_entry_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' "));
						if($ot_entry_check)
						{
							$show_service="No";
						}else
						{
							$show_service="Yes";
							
							$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT `amount` FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
							$bill_amount=$ipd_service_bill["amount"];
						}
					}
				}
				if($show_service=="Yes")
				{
					if($ipd_service_bill["amount"]>0)
					{
		?>
				<tr>
					<td><?php echo $n; ?></td>
					<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
					<td><?php echo $pat_reg["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $con_doc["Name"]; ?></td>
					<td><?php echo $service["charge_name"]; ?></td>
					<td><?php echo "&#x20b9; ".number_format($bill_amount,2); ?></td>
					<td><?php echo convert_date($pat_reg["date"]); ?></td>
					<td><?php echo $user_name["name"]; ?></td>
				</tr>
		<?php
						$casu_tot_bill_amout+=$bill_amount;
						$n++;
					}
				}
			}
		?>
			<tr>
				<th colspan="5"><span class="text-right">Casualty Total</span></th>
				<td colspan="3"><?php echo "&#x20b9; ".number_format($casu_tot_bill_amout,2); ?></td>
			</tr>
<?php
			$grand_total+=$casu_tot_bill_amout;
		}
	}
	if($dept_id==0 || $dept_id==5)
	{
		if($con_cod_id==0)
		{
			$daycare_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=5 AND a.`date` between '$date1' AND '$date2' ";
		}else
		{
			$daycare_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=5 AND a.`date` between '$date1' AND '$date2' AND a.`consultantdoctorid`='$con_cod_id' ";
		}
		$daycare_qry.=" ORDER BY b.`slno` ASC";
	
		//echo $daycare_qry;
		$pat_reg_qry=mysqli_query($link, $daycare_qry );
		
		$pat_reg_num=mysqli_num_rows($pat_reg_qry);
		if($pat_reg_num!=0)
		{
			echo "<tr><th>Day Care Account</th><th colspan='8'></th></tr>";
	?>	
		<?php
			$i=1;
			$n=1;
			$ot_ipd_ids="";
			while($pat_reg=mysqli_fetch_array($pat_reg_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
				
				$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]' "));
				
				$service=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$pat_reg[service_id]' "));
				
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
				
				$style="";
				$show_service="Yes";
				if($pat_reg["schedule_id"]>0)
				{
					$ot_ipd_ids.=$pat_reg["ipd_id"]."@@";
					
					$cons=mysqli_fetch_array(mysqli_query($link, "SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]'"));
					if($cons)
					{
						$empp=$cons['emp_id'];
						
						$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `schedule_id`='$pat_reg[schedule_id]' AND `emp_id`='$empp' AND `ot_group_id`='155' AND `ot_service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_bill["service_text"];
						$bill_amount=$ipd_service_bill["amount"];
						$style="";
					}
					else
					{
						$empp=$pat_reg['consultantdoctorid'];
						$style="display:none;";
						$bill_amount=0;
						
						$ipd_service_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_det["service_text"];
					}
				}
				else
				{
					if (strpos($ot_ipd_ids, $pat_reg["ipd_id"]) !== false) {
						$show_service="No";
					}else
					{
						$ot_entry_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' "));
						if($ot_entry_check)
						{
							$show_service="No";
						}else
						{
							$show_service="Yes";
							
							$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT `amount` FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
							$bill_amount=$ipd_service_bill["amount"];
						}
					}
				}
				if($show_service=="Yes")
				{
					if($ipd_service_bill["amount"]>0)
					{
		?>
				<tr>
					<td><?php echo $n; ?></td>
					<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
					<td><?php echo $pat_reg["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $con_doc["Name"]; ?></td>
					<td><?php echo $service["charge_name"]; ?></td>
					<td><?php echo "&#x20b9; ".number_format($bill_amount,2); ?></td>
					<td><?php echo convert_date($pat_reg["date"]); ?></td>
					<td><?php echo $user_name["name"]; ?></td>
				</tr>
		<?php
						$daycare_tot_bill_amout+=$bill_amount;
						$n++;
					}
				}
			}
		?>
			<tr>
				<th colspan="5"><span class="text-right">Day Care Total</span></th>
				<td colspan="3"><?php echo "&#x20b9; ".number_format($daycare_tot_bill_amout,2); ?></td>
			</tr>
<?php
			$grand_total+=$daycare_tot_bill_amout;
		}
	}
	if($dept_id==0 || $dept_id==6)
	{
		if($con_cod_id==0)
		{
			$daycare_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=6 AND a.`date` between '$date1' AND '$date2' ";
		}else
		{
			$daycare_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=6 AND a.`date` between '$date1' AND '$date2' AND a.`consultantdoctorid`='$con_cod_id' ";
		}
		$daycare_qry.=" ORDER BY b.`slno` ASC";
	
		//echo $daycare_qry;
		$pat_reg_qry=mysqli_query($link, $daycare_qry );
		
		$pat_reg_num=mysqli_num_rows($pat_reg_qry);
		if($pat_reg_num!=0)
		{
			echo "<tr><th>DENTAL Account</th><th colspan='8'></th></tr>";
	?>	
		<?php
			$i=1;
			$n=1;
			$ot_ipd_ids="";
			while($pat_reg=mysqli_fetch_array($pat_reg_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
				
				$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]' "));
				
				$service=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$pat_reg[service_id]' "));
				
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
				
				$style="";
				$show_service="Yes";
				if($pat_reg["schedule_id"]>0)
				{
					$ot_ipd_ids.=$pat_reg["ipd_id"]."@@";
					
					$cons=mysqli_fetch_array(mysqli_query($link, "SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]'"));
					if($cons)
					{
						$empp=$cons['emp_id'];
						
						$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `schedule_id`='$pat_reg[schedule_id]' AND `emp_id`='$empp' AND `ot_group_id`='155' AND `ot_service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_bill["service_text"];
						$bill_amount=$ipd_service_bill["amount"];
						$style="";
					}
					else
					{
						$empp=$pat_reg['consultantdoctorid'];
						$style="display:none;";
						$bill_amount=0;
						
						$ipd_service_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_det["service_text"];
					}
				}
				else
				{
					if (strpos($ot_ipd_ids, $pat_reg["ipd_id"]) !== false) {
						$show_service="No";
					}else
					{
						$ot_entry_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' "));
						if($ot_entry_check)
						{
							$show_service="No";
						}else
						{
							$show_service="Yes";
							
							$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT `amount` FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
							$bill_amount=$ipd_service_bill["amount"];
						}
					}
				}
				if($show_service=="Yes")
				{
					if($ipd_service_bill["amount"]>0)
					{
		?>
				<tr>
					<td><?php echo $n; ?></td>
					<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
					<td><?php echo $pat_reg["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $con_doc["Name"]; ?></td>
					<td><?php echo $service["charge_name"]; ?></td>
					<td><?php echo "&#x20b9; ".number_format($bill_amount,2); ?></td>
					<td><?php echo convert_date($pat_reg["date"]); ?></td>
					<td><?php echo $user_name["name"]; ?></td>
				</tr>
		<?php
						$dental_tot_bill_amout+=$bill_amount;
						$n++;
					}
				}
			}
		?>
			<tr>
				<th colspan="5"><span class="text-right">Dental Total</span></th>
				<td colspan="3"><?php echo "&#x20b9; ".number_format($dental_tot_bill_amout,2); ?></td>
			</tr>
<?php
			$grand_total+=$dental_tot_bill_amout;
		}
	}
	if($dept_id==0 || $dept_id==7)
	{
		if($con_cod_id==0)
		{
			$daycare_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=7 AND a.`date` between '$date1' AND '$date2' ";
		}else
		{
			$daycare_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=7 AND a.`date` between '$date1' AND '$date2' AND a.`consultantdoctorid`='$con_cod_id' ";
		}
		$daycare_qry.=" ORDER BY b.`slno` ASC";
	
		//echo $daycare_qry;
		$pat_reg_qry=mysqli_query($link, $daycare_qry );
		
		$pat_reg_num=mysqli_num_rows($pat_reg_qry);
		if($pat_reg_num!=0)
		{
			echo "<tr><th>DIALYSIS Account</th><th colspan='8'></th></tr>";
	?>	
		<?php
			$i=1;
			$n=1;
			$ot_ipd_ids="";
			while($pat_reg=mysqli_fetch_array($pat_reg_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
				
				$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]' "));
				
				$service=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$pat_reg[service_id]' "));
				
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
				
				$style="";
				$show_service="Yes";
				if($pat_reg["schedule_id"]>0)
				{
					$ot_ipd_ids.=$pat_reg["ipd_id"]."@@";
					
					$cons=mysqli_fetch_array(mysqli_query($link, "SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]'"));
					if($cons)
					{
						$empp=$cons['emp_id'];
						
						$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `schedule_id`='$pat_reg[schedule_id]' AND `emp_id`='$empp' AND `ot_group_id`='155' AND `ot_service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_bill["service_text"];
						$bill_amount=$ipd_service_bill["amount"];
						$style="";
					}
					else
					{
						$empp=$pat_reg['consultantdoctorid'];
						$style="display:none;";
						$bill_amount=0;
						
						$ipd_service_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_det["service_text"];
					}
				}
				else
				{
					if (strpos($ot_ipd_ids, $pat_reg["ipd_id"]) !== false) {
						$show_service="No";
					}else
					{
						$ot_entry_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' "));
						if($ot_entry_check)
						{
							$show_service="No";
						}else
						{
							$show_service="Yes";
							
							$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT `amount` FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
							$bill_amount=$ipd_service_bill["amount"];
						}
					}
				}
				if($show_service=="Yes")
				{
					if($ipd_service_bill["amount"]>0)
					{
		?>
				<tr>
					<td><?php echo $n; ?></td>
					<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
					<td><?php echo $pat_reg["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $con_doc["Name"]; ?></td>
					<td><?php echo $service["charge_name"]; ?></td>
					<td><?php echo "&#x20b9; ".number_format($bill_amount,2); ?></td>
					<td><?php echo convert_date($pat_reg["date"]); ?></td>
					<td><?php echo $user_name["name"]; ?></td>
				</tr>
		<?php
						$dialysis_tot_bill_amout+=$bill_amount;
						$n++;
					}
				}
			}
		?>
			<tr>
				<th colspan="5"><span class="text-right">DIALYSIS Total</span></th>
				<td colspan="3"><?php echo "&#x20b9; ".number_format($dialysis_tot_bill_amout,2); ?></td>
			</tr>
<?php
			$grand_total+=$dialysis_tot_bill_amout;
		}
	}
	if($dept_id==0 || $dept_id==8)
	{
		if($con_cod_id==0)
		{
			$daycare_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=8 AND a.`date` between '$date1' AND '$date2' ";
		}else
		{
			$daycare_qry=" SELECT a.* FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=8 AND a.`date` between '$date1' AND '$date2' AND a.`consultantdoctorid`='$con_cod_id' ";
		}
		$daycare_qry.=" ORDER BY b.`slno` ASC";
	
		//echo $daycare_qry;
		$pat_reg_qry=mysqli_query($link, $daycare_qry );
		
		$pat_reg_num=mysqli_num_rows($pat_reg_qry);
		if($pat_reg_num!=0)
		{
			echo "<tr><th>Baby Account</th><th colspan='8'></th></tr>";
	?>	
		<?php
			$i=1;
			$n=1;
			$ot_ipd_ids="";
			while($pat_reg=mysqli_fetch_array($pat_reg_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
				
				$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]' "));
				
				$service=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$pat_reg[service_id]' "));
				
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
				
				$style="";
				$show_service="Yes";
				if($pat_reg["schedule_id"]>0)
				{
					$ot_ipd_ids.=$pat_reg["ipd_id"]."@@";
					
					$cons=mysqli_fetch_array(mysqli_query($link, "SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]'"));
					if($cons)
					{
						$empp=$cons['emp_id'];
						
						$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `schedule_id`='$pat_reg[schedule_id]' AND `emp_id`='$empp' AND `ot_group_id`='155' AND `ot_service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_bill["service_text"];
						$bill_amount=$ipd_service_bill["amount"];
						$style="";
					}
					else
					{
						$empp=$pat_reg['consultantdoctorid'];
						$style="display:none;";
						$bill_amount=0;
						
						$ipd_service_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
						$service["charge_name"]=$service_name=$ipd_service_det["service_text"];
					}
				}
				else
				{
					if (strpos($ot_ipd_ids, $pat_reg["ipd_id"]) !== false) {
						$show_service="No";
					}else
					{
						$ot_entry_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' "));
						if($ot_entry_check)
						{
							$show_service="No";
						}else
						{
							$show_service="Yes";
							
							$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT `amount` FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' AND `service_id`='$pat_reg[service_id]'"));
							$bill_amount=$ipd_service_bill["amount"];
						}
					}
				}
				if($show_service=="Yes")
				{
					if($ipd_service_bill["amount"]>0)
					{
		?>
				<tr>
					<td><?php echo $n; ?></td>
					<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
					<td><?php echo $pat_reg["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $con_doc["Name"]; ?></td>
					<td><?php echo $service["charge_name"]; ?></td>
					<td><?php echo "&#x20b9; ".number_format($bill_amount,2); ?></td>
					<td><?php echo convert_date($pat_reg["date"]); ?></td>
					<td><?php echo $user_name["name"]; ?></td>
				</tr>
		<?php
						$baby_tot_bill_amout+=$bill_amount;
						$n++;
					}
				}
			}
		?>
			<tr>
				<th colspan="5"><span class="text-right">Baby Total</span></th>
				<td colspan="3"><?php echo "&#x20b9; ".number_format($baby_tot_bill_amout,2); ?></td>
			</tr>
<?php
			$grand_total+=$baby_tot_bill_amout;
		}
	}
?>
	<tr>
		<th colspan="5"><span class="text-right">Grand total</span></th>
		<td colspan="3"><?php echo "&#x20b9; ".number_format(($grand_total),2); ?></td>
	</tr>
</table>
