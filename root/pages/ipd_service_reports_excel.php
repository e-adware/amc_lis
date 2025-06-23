<?php
session_start();
$c_user=trim($_SESSION['emp_id']);

include'../../includes/connection.php';
require('../../includes/global.function.php');


$date1=$_GET['date1'];
$date2=$_GET['date2'];
$service_id=trim($_GET['service_id']);

$filename ="service_report_from_".$date1."_to_".$date2.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$not_accountant = array();
array_push($not_accountant, 104, 148, 150, 151); // Bed, Patho , Radio, Cardio
$not_accountant = join(',',$not_accountant);

$qq=" SELECT * FROM `ipd_pat_service_details` WHERE `date` between '$date1' AND '$date2' AND `group_id` NOT IN ($not_accountant) ORDER BY `slno` DESC ";
if($service_id>0)
{
	$qq=" SELECT * FROM `ipd_pat_service_details` WHERE `date` between '$date1' AND '$date2' AND `service_id`='$service_id' ORDER BY `slno` DESC ";
}

$counter_qry=mysqli_query($link, $qq);

$counter_num=mysqli_num_rows($counter_qry);

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
		<table class="table table-hover">
			<tr>
				<th colspan="9">
					Service Report from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
				</th>
			</tr>
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Service</th>
				<th>Amount</th>
				<th>Encounter</th>
				<th>Date Time</th>
				<th>User</th>
			</tr>
			<?php
			$i=1;
			$tot_bill_amout=0;
			while($all_pat=mysqli_fetch_array($counter_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$all_pat[patient_id]'"));
				
				$service=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$all_pat[service_id]' "));
				
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$all_pat[user]' "));
				
				$bill_amount=0;
				
				$bill_amount=$all_pat["amount"];
				
				$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[ipd_id]' "));
				
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_type[type]' "));
				$Encounter=$pat_typ_text['p_type'];
				
				$tot_bill_amout+=$bill_amount;
			?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $pat_info["patient_id"]; ?></td>
					<td><?php echo $all_pat["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $service["charge_name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($all_pat["amount"],2); ?></td>
					<td><?php echo $Encounter; ?></td>
					<td><?php echo convert_date($all_pat["date"]); ?> <?php echo convert_time($all_pat["time"]); ?></td>
					<td><?php echo $user_name["name"]; ?></td>
				</tr>
			<?php
				$i++;
			}
		?>
			<tr>
				<td colspan="4"></td>
				<th colspan="1"><span class="text-right">Total:</span></th>
				<td colspan="1"><?php echo $rupees_symbol.number_format($tot_bill_amout,2); ?></td>
				<td colspan="3"></td>
			</tr>
		</table>
	</div>
</body>
</html>
