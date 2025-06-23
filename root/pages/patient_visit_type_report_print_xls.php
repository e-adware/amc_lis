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
$visit_type_id=$_GET['visit_type_id'];

$visit_type_str="";
if($visit_type_id>0)
{
	$visit_type_str=" AND `visit_type_id`='$visit_type_id'";
}

$qq=" SELECT * FROM `patient_visit_type_details` WHERE `date` between '$date1' AND '$date2' $visit_type_str ORDER BY `slno` ASC ";

$counter_qry=mysqli_query($link, $qq);

$counter_num=mysqli_num_rows($counter_qry);

if($counter_num>0)
{
?>
<p style="margin-top: 2%;" id="print_div"><b>Patient Visit Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
	<button class="btn btn-info text-right" id="det_excel" onclick="export_excel()"><i class="icon-file"></i> Excel</button>
	<button type="button" class="btn btn-info text-right" onclick="print_page('<?php echo $_POST["type"]; ?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $visit_type_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
</p>
<table class="table table-hover">
	<tr>
		<th>#</th>
		<th>UHID</th>
		<th>Bill No</th>
		<th>Patient Name</th>
		<th>Visit Type</th>
		<th>Encounter</th>
		<th>Date Time</th>
		<!--<th>User</th>-->
	</tr>
	<?php
	$i=1;
	$tot_bill_amout=0;
	while($all_pat=mysqli_fetch_array($counter_qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$all_pat[patient_id]'"));
		
		$pat_visit_type=mysqli_fetch_array(mysqli_query($link, "SELECT `visit_type_name`, `prefix`, `p_type_id` FROM `patient_visit_type_master` WHERE `visit_type_id`='$all_pat[visit_type_id]'"));
		
		//$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$all_pat[user]' "));
		
		//$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' "));
		
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_visit_type[p_type_id]' "));
		$Encounter=$pat_typ_text['p_type'];
		
	?>
		<tr>
			<td><?php echo $i; ?></td>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $all_pat["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $pat_visit_type["visit_type_name"]; ?></td>
			<td><?php echo $Encounter; ?></td>
			<td><?php echo convert_date($all_pat["date"]); ?> <?php echo convert_time($all_pat["time"]); ?></td>
			<!--<td><?php echo $user_name["name"]; ?></td>-->
		</tr>
	<?php
		$i++;
	}
?>
</table>
<?php
}
