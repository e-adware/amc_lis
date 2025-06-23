<?php
include('../../includes/connection.php');

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

$filename ="service_report.xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$service_id=$_GET['service_id'];
$not_accountant = array();
array_push($not_accountant, 104, 148, 150, 151);
$not_accountant = join(',',$not_accountant);

$service=" AND `service_id`='$service_id'";
if($service_id=='0')
{
	$service="";
}

$qq=" SELECT * FROM `ipd_pat_service_details` WHERE `date` between '$date1' AND '$date2' $service AND `group_id` NOT IN ($not_accountant) AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='9') ORDER BY `slno` DESC ";

$counter_qry=mysqli_query($link, $qq);

$counter_num=mysqli_num_rows($counter_qry);

if($counter_num>0)
{
?>
<p style="margin-top: 2%;" id="hide_p"><b>Service Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
	<button class="btn btn-info text-right" id="det_excel" onclick="export_excel()"><i class="icon-file"></i> Excel</button>
	<button type="button" class="btn btn-info text-right" onclick="print_page('doctor_service','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $service_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
</p>
<table class="table table-hover">
	<tr>
		<th>#</th>
		<th>UHID</th>
		<th>PIN</th>
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
		<th colspan="4"></th>
		<th><span class="text-right">Total:</span></th>
		<td><?php echo $rupees_symbol.number_format($tot_bill_amout,2); ?></td>
		<td colspan="3"></td>
	</tr>
</table>
<?php
}
