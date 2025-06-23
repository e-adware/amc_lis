<?php
include("../../includes/connection.php");
require("../../includes/global.function.php");

$date1=mysqli_real_escape_string($link, $_GET["date1"]);
$date2=mysqli_real_escape_string($link, $_GET["date2"]);
$sguide_id=mysqli_real_escape_string($link, $_GET["sguide_id"]);
$hguide_id=mysqli_real_escape_string($link, $_GET["hguide_id"]);

$filename ="health_guide_reports_".$date1."_to_".$date2.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$report_name="Health Guide Reports from ".convert_date($date1)." to ".convert_date($date2);

$hguide_id_str="";
if($hguide_id>0)
{
	$hguide_id_str=" AND b.`hguide_id`='$hguide_id'";
	
	$hsguide_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `health_guide` WHERE `hguide_id`='$hguide_id' "));
	
	$report_name.=" of ".$hsguide_info["name"];
}

$qq=" SELECT a.*,b.`hguide_id` FROM `uhid_and_opdid` a, `pat_health_guide` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' $hguide_id_str ORDER BY a.`slno` ASC ";

$counter_qry=mysqli_query($link, $qq);

?>

<table class="table table-hover">
	<tr>
		<td colspan="9">
			<b>Health Guide Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		</td>
	</tr>
	<tr>
		<th>#</th>
		<th>UHID</th>
		<th>Bill No</th>
		<th>Name</th>
		<th>Bill Amout</th>
		<th>Encounter</th>
		<th>Health Agents</th>
		<th>Date Time</th>
		<th>User</th>
	</tr>
	<?php
	$i=1;
	$tot_bill_amout=0;
	while($all_pat=mysqli_fetch_array($counter_qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$all_pat[patient_id]'"));
		
		$guide_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `health_guide` WHERE `hguide_id`='$all_pat[hguide_id]' "));
		
		$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$all_pat[user]' "));
		
		$bill_amount=0;
		
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
		$Encounter=$pat_typ_text['type'];
		
		if($Encounter==1)
		{
			$opd_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `tot_amount` FROM `consult_patient_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' "));
			$bill_amount=$opd_bill["tot_amount"];
			
		}
		if($Encounter==2)
		{
			$lab_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `tot_amount` FROM `invest_patient_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' "));
			$bill_amount=$lab_bill["tot_amount"];
			
		}
		if($Encounter==3)
		{
			$ipd_bill=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `tot_bill` FROM `ipd_pat_service_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' "));
			$bill_amount=$ipd_bill["tot_bill"];
			
		}
		
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
		$Encounter=$pat_typ_text['p_type'];
		
		$tot_bill_amout+=$bill_amount;
	?>
		<tr>
			<td><?php echo $i; ?></td>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $all_pat["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $rupees_symbol.number_format($bill_amount,2); ?></td>
			<td><?php echo $Encounter; ?></td>
			<td><?php echo $guide_info["name"]; ?></td>
			<td><?php echo convert_date($all_pat["date"]); ?> <?php echo convert_time($all_pat["time"]); ?></td>
			<td><?php echo $user_name["name"]; ?></td>
		</tr>
	<?php
		$i++;
	}
?>
	<tr>
		<th colspan="4"><span class="text-right">Total:</span></th>
		<td><?php echo $rupees_symbol.number_format($tot_bill_amout,2); ?></td>
		<th colspan="4"></th>
	</tr>
</table>
