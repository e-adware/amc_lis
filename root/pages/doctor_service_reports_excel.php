<?php
session_start();
$c_user=trim($_SESSION['emp_id']);

include'../../includes/connection.php';

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d M Y', $timestamp);
	return $new_date;
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}


$date1=$_GET['date1'];
$date2=$_GET['date2'];

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];


$service_id=trim($_GET['service_id']);
$consultantdoctorid=trim($_GET['consultantdoctorid']);
$service=" AND `service_id`='$service_id'";
if($service_id=='0')
{
	$service="";
}
$consult=" AND `consultantdoctorid`='$consultantdoctorid'";
$consult_emp=" AND `emp_id` IN(SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$consultantdoctorid') ";
if($consultantdoctorid=='0')
{
	$consult="";
	$consult_emp="";
}

$qq=" SELECT * FROM `doctor_service_done` WHERE `date` between '$date1' AND '$date2' $service $consult ORDER BY `slno` DESC ";

$qq_part=" SELECT * FROM `ipd_pat_minor_service_details` WHERE `date` between '$date1' AND '$date2' $service $consult_emp ORDER BY `slno` DESC ";

$counter_qry=mysqli_query($link, $qq);

$counter_num=mysqli_num_rows($counter_qry);

$filename ="doctor_service_reports_".$date1."_to_".$date2.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
<html>
<head>
<title>Service Summary Reports</title>

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
		<table class="table table-hover">
			<tr>
				<th>#</th>
				<th>IPD ID</th>
				<th>Patient Name</th>
				<th>Doctor Name</th>
				<th>Service</th>
				<th>Amount</th>
				<th>Encounter</th>
				<th>Date Time</th>
				<th>User</th>
			</tr>
			<?php
			$i=1;
			$tot_bill_amout=0;
			$ot_ipd_ids="";
			while($all_pat=mysqli_fetch_array($counter_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$all_pat[patient_id]'"));
				
				$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$all_pat[consultantdoctorid]' "));
				
				$service=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$all_pat[service_id]' "));
				
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$all_pat[user]' "));
				
				$bill_amount=0;
				$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `ipd_pat_service_details` WHERE `slno`='$all_pat[rel_slno]' "));
				$bill_amount=$ipd_service_bill["amount"];
				
				$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[ipd_id]' "));
				if($pat_type["type"]==3)
				{
					$Encounter="IPD";
				}
				if($pat_type["type"]==4)
				{
					$Encounter="Casualty";
				}
				if($pat_type["type"]==5)
				{
					$Encounter="Day Care";
				}
				$style="";
				$show_service="Yes";
				if($all_pat["schedule_id"]>0)
				{
					$ot_ipd_ids.=$all_pat["ipd_id"]."@@";
					
					$cons=mysqli_fetch_array(mysqli_query($link, "SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$all_pat[consultantdoctorid]'"));
					if($cons)
					{
						$empp=$cons['emp_id'];
						
						$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[ipd_id]' AND `schedule_id`='$all_pat[schedule_id]' AND `emp_id`='$empp' AND `ot_group_id`='155' AND `ot_service_id`='$all_pat[service_id]'"));
						$bill_amount=$ipd_service_bill["amount"];
						$service["charge_name"]=$service_name=$ipd_service_bill["service_text"];
						$style="";
					}
					else
					{
						$empp=$all_pat['consultantdoctorid'];
						$style="display:none;";
						$bill_amount=0;
						
						$ipd_service_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[ipd_id]' AND `service_id`='$all_pat[service_id]' "));
						$service["charge_name"]=$service_name=$ipd_service_det["service_text"];
					}
				}else
				{
					if (strpos($ot_ipd_ids, $all_pat["ipd_id"]) !== false) {
						$show_service="No";
					}else
					{
						$ot_entry_check=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[ipd_id]' "));
						if($ot_entry_check)
						{
							$show_service="No";
						}else
						{
							$show_service="Yes";
						}
					}
					if($pat_type["type"]==3)
					{
						$show_service="Yes";
					}
				}
				if($show_service=="Yes")
				{
					$tot_bill_amout+=$bill_amount;
					
					$check_entry_qry=mysqli_query($link, "SELECT * FROM `ipd_pat_minor_service_details` WHERE `serv_slno`='$all_pat[slno]'");
					$check_entry_num=mysqli_num_rows($check_entry_qry);
					if($check_entry_num>0)
					{
						while($check_entry=mysqli_fetch_array($check_entry_qry))
						{
							$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `emp_id`='$check_entry[emp_id]' "));
				?>
					<!--<tr style="<?php echo $style;?>">
						<td><?php echo $i; ?></td>
						<td><?php echo $all_pat["ipd_id"]; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $con_doc["Name"]; ?></td>
						<td><?php echo $check_entry["service_text"]; ?></td>
						<td><?php echo $rupees_symbol.number_format($check_entry["amount"],2); ?></td>
						<td><?php echo $Encounter; ?></td>
						<td><?php echo convert_date($all_pat["date"]); ?> <?php echo convert_time($all_pat["time"]); ?></td>
						<td><?php echo $user_name["name"]; ?></td>
					</tr>-->
				<?php
							//$i++;
						}
					}else
					{
						if($ipd_service_bill["amount"]>0)
						{
					
	?>
					<tr style="<?php echo $style;?>">
						<td><?php echo $i; ?></td>
						<td><?php echo $all_pat["ipd_id"]; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $con_doc["Name"]; ?></td>
						<td><?php echo $service["charge_name"]; ?></td>
						<td><?php echo $rupees_symbol.number_format($ipd_service_bill["amount"],2); ?></td>
						<td><?php echo $Encounter; ?></td>
						<td><?php echo convert_date($all_pat["date"]); ?> <?php echo convert_time($all_pat["time"]); ?></td>
						<td><?php echo $user_name["name"]; ?></td>
					</tr>
				<?php
							if($all_pat["schedule_id"]>0)
							{
								if($cons)
								{
									$i++;
								}
							}
							else
							{
								$i++;
							}
						}
					}
				}
			}
			$counter_emp_qry=mysqli_query($link, $qq_part);
			//echo $qq_part;
			$counter_emp_num=mysqli_num_rows($counter_emp_qry);
			
			if($counter_emp_num>0)
			{
				while($all_pat=mysqli_fetch_array($counter_emp_qry))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$all_pat[patient_id]'"));
					
					$doc_info=mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$all_pat[emp_id]' "));
					
					$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doc_info[consultantdoctorid]' "));
					
					$service=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$all_pat[service_id]' "));
					
					$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$all_pat[user]' "));
					
					$bill_amount=0;
					$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `ipd_pat_service_details` WHERE `slno`='$all_pat[rel_slno]' "));
					$bill_amount=$ipd_service_bill["amount"];
					
					$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[ipd_id]' "));
					if($pat_type["type"]==3)
					{
						$Encounter="IPD";
					}
					if($pat_type["type"]==4)
					{
						$Encounter="Casualty";
					}
					$style="";
					if($all_pat["schedule_id"]>0)
					{
						$cons=mysqli_fetch_array(mysqli_query($link, "SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doc_info[consultantdoctorid]'"));
						if($cons)
						{
							$empp=$cons['emp_id'];
							
							$ipd_service_bill=mysqli_fetch_array(mysqli_query($link, "SELECT `amount` FROM `ot_pat_service_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[ipd_id]' AND `schedule_id`='$all_pat[schedule_id]' AND `emp_id`='$empp' AND `ot_group_id`='155' AND `ot_service_id`='$all_pat[service_id]'"));
							$bill_amount=$ipd_service_bill["amount"];
							$style="";
						}
						else
						{
							$empp=$all_pat['consultantdoctorid'];
							$style="display:none;";
							$bill_amount=0;
						}
					}
					
					$tot_bill_amout+=$bill_amount;
					
					$check_entry_qry=mysqli_query($link, "SELECT * FROM `ipd_pat_minor_service_details` WHERE `serv_slno`='$all_pat[slno]'");
					$check_entry_num=mysqli_num_rows($check_entry_qry);
					if($check_entry_num>0)
					{
						while($check_entry=mysqli_fetch_array($check_entry_qry))
						{
							$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `emp_id`='$check_entry[emp_id]' "));
				?>
					<!--<tr style="<?php echo $style;?>">
						<td><?php echo $i; ?></td>
						<td><?php echo $all_pat["ipd_id"]; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $con_doc["Name"]; ?></td>
						<td><?php echo $check_entry["service_text"]; ?></td>
						<td><?php echo $rupees_symbol.number_format($check_entry["amount"],2); ?></td>
						<td><?php echo $Encounter; ?></td>
						<td><?php echo convert_date($all_pat["date"]); ?> <?php echo convert_time($all_pat["time"]); ?></td>
						<td><?php echo $user_name["name"]; ?></td>
					</tr>-->
				<?php
							//$i++;
						}
					}else
					{
					
		?>
					<tr style="<?php echo $style;?>">
						<td><?php echo $i; ?></td>
						<td><?php echo $all_pat["ipd_id"]; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $con_doc["Name"]; ?></td>
						<td><?php echo $service["charge_name"]; ?></td>
						<td><?php echo $rupees_symbol.number_format($all_pat["amount"],2); ?></td>
						<td><?php echo $Encounter; ?></td>
						<td><?php echo convert_date($all_pat["date"]); ?> <?php echo convert_time($all_pat["time"]); ?></td>
						<td><?php echo $user_name["name"]; ?></td>
					</tr>
				<?php
						if($all_pat["schedule_id"]>0)
						{
							if($cons)
							{
								$i++;
							}
						}
						else
						{
							$i++;
						}
					}
				}
			}
		?>
			<tr>
				<th colspan="5"><span class="text-right">Total:</span></th>
				<td colspan=""><?php echo $rupees_symbol.number_format($tot_bill_amout,2); ?></td>
				<td colspan="3"></td>
			</tr>
		</table>
	</div>
</body>
</html>
