<?php
session_start();
include('../../includes/connection.php');
require('../../includes/global.function.php');

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$date=date("Y-m-d");


$uhid=$_GET['uhid'];
$ipd=$_GET['ipd'];
$ipd_serial=$_GET['ipd_serial'];
$from=$_GET['from'];
$to=$_GET['to'];
$name=$_GET['name'];

$f_date=strtotime(date("Y-m-d"));
$f_date1=date("Y-m-d");
$t_date=date("Y-m-d",strtotime('-10 day',$f_date));


$filename ="ipd_discharged_pat_list.xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$q="SELECT * FROM `ipd_pat_discharge_details` ORDER BY `slno` DESC limit 0, 100";

if($uhid)
{
	$q="SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' )";
}
if($ipd_serial)
{
	$q="SELECT * FROM `ipd_pat_discharge_details` WHERE `ipd_id` in ( SELECT `opd_id` FROM `uhid_and_opdid` WHERE `ipd_serial`='$ipd_serial' )";
}
if($ipd)
{
	$q="SELECT * FROM `ipd_pat_discharge_details` WHERE `ipd_id`='$ipd'";
}
if($name)
{
	$q="SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '$name%')";
}

if($from && $to)
{
	$q="SELECT * FROM `ipd_pat_discharge_details` WHERE `date` between '$from' AND '$to' ORDER BY `date` DESC";
}

//echo $q;

$num=mysqli_num_rows(mysqli_query($link,$q));
if($num>0)
{
	$qq=mysqli_query($link,$q);

?>
<p style="margin-top: 2%;" id="print_div">
	<b>Discharged patient list </b>
</p>
<table class="table table-condensed table-bordered">
	<tr>
		<th>#</th>
		<th>UHID</th>
		<th>IPD ID</th>
		<th>Name</th>
		<th>Sex</th>
		<th>Age (DOB)</th>
		<th>Bill Amount</th>
		<th>Consultant</th>
		<!--<th>Contact</th>-->
		<th>Admission Date</th>
		<th>Admission Time</th>
		<th>Discharge Date</th>
		<th>Discharge Time</th>
		<th>User</th>
	</tr>
<?php
	$i=1;
	while($r=mysqli_fetch_array($qq))
	{
		$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
		if($p["dob"]!=""){ $age=age_calculator($p["dob"])." (".$p["dob"].")"; }else{ $age=$p["age"]." ".$p["age_type"]; }
		
		$tr_class="discharged";
			
		$admit_det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where `patient_id`='$r[patient_id]' AND `opd_id`='$r[ipd_id]' "));
		$dis_date=convert_date($r['date']);
		$dis_time=convert_time($r['time']);
		
		$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$r[user]' "));
		
		$doc_id=mysqli_fetch_array(mysqli_query($link, " SELECT `attend_doc`, `admit_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$r[patient_id]' AND `ipd_id`='$r[ipd_id]' "));

		$attend_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doc_id[attend_doc]' "));
		
		// Bill Details
		$uhid=$r["patient_id"];
		$ipd=$r["ipd_id"];
		
		$baby_serv_tot=0;
		$baby_ot_total=0;
		$delivery_check_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
		while($delivery_check=mysqli_fetch_array($delivery_check_qry))
		{
			//$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
			if($delivery_check)
			{
				$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_serv_tot+=$baby_tot_serv["tots"];
				
				// OT Charge Baby
				$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(`amount`),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_ot_total+=$baby_ot_tot_val["g_tot"];
			}
		}
		
		$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
		$no_of_days=$no_of_days_val["ser_quantity"];
		
		$tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
		$tot_serv_amt=$tot_serv["tots"];
		
		// OT Charge
		$grp_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(`amount`),0) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
		$grp_tot=$grp_tot_val["g_tot"];
		
		$tot_bill_amount=$tot_serv_amt+$baby_serv_tot+$baby_ot_total+$grp_tot;
		
	?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $p['patient_id'];?></td>
			<td><?php echo $r['ipd_id'];?></td>
			<td><?php echo $p['name'];?></td>
			<td><?php echo $p['sex'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo number_format($tot_serv_amt,2);?></td>
			<td><?php echo $attend_doc["Name"];?></td>
			<!--<td><?php echo $p['phone'];?></td>-->
			<td><?php echo convert_date($admit_det['date']);?></td>
			<td><?php echo convert_time($admit_det['time']);?></td>
			<td><?php echo $dis_date;?></td>
			<td><?php echo $dis_time;?></td>
			<td><?php echo $emp_info["name"]; ?></td>
		</tr>
	<?php
		$i++;
	}
?>
</table>
<?php
}
?>
