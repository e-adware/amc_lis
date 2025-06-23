<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');

$c_user=trim($_SESSION['emp_id']);

$date=date("Y-m-d");

$str="SELECT * FROM `ipd_pat_delivery_det` WHERE `slno`>0";

if($_POST["type"]=="load_all_baby")
{
	$mother_uhid=$_POST["mother_uhid"];
	$baby_uhid=$_POST["baby_uhid"];
	
	if(strlen($mother_uhid)>2)
	{
		$str.=" AND `patient_id` LIKE '$mother_uhid%'";
	}
	
	if(strlen($baby_uhid)>2)
	{
		$str.=" AND `baby_uhid` LIKE '$baby_uhid%'";
	}
	
	$str.=" ORDER BY `slno` ASC LIMIT 0,100";
	
	//echo $str;
?>
	<table class="table table-bordered text-center">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Mother UHID</th>
				<th>IPD ID</th>
				<th>Name</th>
				<th>Baby UHID</th>
				<th><i class="icon icon-print"></i></th>
			</tr>
		</thead>
<?php
	$n=1;
	$qry=mysqli_query($link, $str);
	while($data=mysqli_fetch_array($qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$data[patient_id]' "));
		
		$en_mother_uhid=base64_encode($data["patient_id"]);
		$en_baby_uhid=base64_encode($data["baby_uhid"]);
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $data["patient_id"]; ?></td>
			<td><?php echo $data["ipd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $data["baby_uhid"]; ?></td>
			<td>
				<button class="btn btn-info btn-mini" onclick="print_birth_certificate('<?php echo $en_mother_uhid; ?>','<?php echo $en_baby_uhid; ?>')">Birth Certificate</button>
			</td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}

?>
