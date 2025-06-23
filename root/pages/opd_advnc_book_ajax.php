<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
$date=date("Y-m-d"); // important
$time=date("H:i:s");

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

if($_POST["type"]=="load_appointed_patients")
{
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	$pat_name=$_POST["pat_name"];
	$pat_uhid=$_POST["pat_uhid"];
	$monitor_id=$_POST["monitor_id"];
	
	if((!$monitor_id && $monitor_id!=0) || $monitor_id==""){ $monitor_id=9; }
	
	$str=" SELECT * FROM `advance_booking` WHERE `appointment_date` BETWEEN '$date1' AND '$date2' ";
	$monitor_str=" SELECT * FROM `advance_booking` WHERE `appointment_date` BETWEEN '$date1' AND '$date2' ";
	
	if(strlen($pat_name)>2)
	{
		$str.=" AND `patient_id` IN(SELECT `patient_id` FROM `patient_info` WHERE `name` LIKE '%$pat_name%') ";
		$monitor_str.=" AND `patient_id` IN(SELECT `patient_id` FROM `patient_info` WHERE `name` LIKE '%$pat_name%') ";
	}
	if(strlen($pat_uhid)>2)
	{
		$str.=" AND `patient_id` LIKE '$pat_uhid%' ";
		$monitor_str.=" AND `patient_id` LIKE '$pat_uhid%' ";
	}
	if($monitor_id!=9)
	{
		$str.=" AND `status`='$monitor_id'";
	}
	$str.=" ORDER BY `booking_id`";
	
	$schedule_num=mysqli_num_rows(mysqli_query($link, $monitor_str));
	$pending_num=mysqli_num_rows(mysqli_query($link, $monitor_str." AND `status`=0"));
	$processed_num=mysqli_num_rows(mysqli_query($link, $monitor_str." AND `status`=1"));
	$calceled_num=mysqli_num_rows(mysqli_query($link, $monitor_str." AND `status`=2"));
?>
	<button class="btn btn-search btn-monitor" onclick="load_monitor(9)"><?php echo $schedule_num; ?> Schedule</button>
	<button class="btn btn-reset btn-monitor" onclick="load_monitor(0)"><?php echo $pending_num; ?> Pending</button>
	<button class="btn btn-new btn-monitor" onclick="load_monitor(1)"><?php echo $processed_num; ?> Processed</button>
	<button class="btn btn-delete btn-monitor" onclick="load_monitor(2)"><?php echo $calceled_num; ?> Canceled</button>
	<input type="hidden" id="monitor_id" value="9">
	<table class="table table-bordered text-center table-condensed">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Patient Name</th>
			<th>Phone</th>
			<th>Doctor Name</th>
			<th>Appointment Date</th>
			<th>Action</th>
			<th>User</th>
			<th>Book Time</th>
		</tr>
	<?php
		$n=1;
		$data_qry=mysqli_query($link, $str);
		while($data=mysqli_fetch_array($data_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$data[patient_id]' "));
			
			$doc_info=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$data[consultantdoctorid]' "));
			
			$user=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$data[user]' "));
			
			$status_str="";
			if($data["status"]==1 || $data["status"]==2)
			{
				$status_str="disabled";
			}
			
			$processed_str="disabled";
			if($data["appointment_date"]==$date)
			{
				$processed_str="";
			}
		?>
			<tr <?php echo $onclick.$pointer; ?>>
				<td><?php echo $n; ?></td>
				<td><?php echo $pat_info["patient_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $pat_info["phone"]; ?></td>
				<td><?php echo $doc_info["Name"]; ?></td>
				<td><?php echo date("d-M-Y",strtotime($data["appointment_date"])); ?></td>
				<td style="text-align: right;">
					<select class="span2" id="status<?php echo $data["booking_id"]; ?>" onchange="status_change('<?php echo $data["patient_id"]; ?>','<?php echo $data["booking_id"]; ?>')" <?php echo $status_str; ?>>
						<option value="0" <?php if($data["status"]==0){ echo "selected"; } ?>>Pending</option>
						<option value="1" <?php if($data["status"]==1){ echo "selected"; } ?> <?php echo $processed_str; ?>>Process</option>
						<option value="2" <?php if($data["status"]==2){ echo "selected"; } ?>>Cancel</option>
						<option value="3" <?php if($data["status"]==3){ echo "selected"; } ?>>Reschedule</option>
					</select>
				<td><?php echo $user["name"]; ?></td>
				<td><?php echo date("d-M-Y",strtotime($data["date"])); ?> <?php echo date("h:i A",strtotime($data["time"])); ?></td>
			</tr>
		<?php
				$n++;
			}
	?>
	</table>
<?php
}
if($_POST["type"]=="status_change")
{
	$patient_id=$_POST["uhid"];
	$booking_id=$_POST["bid"];
	$status=$_POST["status"];
	$user=$_POST["user"];
	
	if($status==0 || $status) // 0 = Pending, 2 = Cancel
	{
		mysqli_query($link, " UPDATE `advance_booking` SET `status`='$status' WHERE `booking_id`='$booking_id' AND `patient_id`='$patient_id' ");
		
		mysqli_query($link, " INSERT INTO `advance_booking_activity`(`booking_id`, `opd_id`, `status`, `user`, `date`, `time`) VALUES ('$booking_id','','$status','$user','$date','$time') ");
		
		echo "101@Saved@".$booking_id;
	}
}

?>
