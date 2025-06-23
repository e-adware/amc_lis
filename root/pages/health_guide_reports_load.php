<?php
include("../../includes/connection.php");
require("../../includes/global.function.php");

$date1=$_POST['date1'];
$date2=$_POST['date2'];

$rupees_symbol="&#x20b9; ";

if($_POST["type"]=="load_hguide")
{
	$sguide_id=mysqli_real_escape_string($link, $_POST["sguide_id"]);
	
	echo "<option value='0'>Select Agent</option>";
	
	$sguide_str="";
	if($sguide_id)
	{
		$sguide_str=" AND `sguide_id`='$sguide_id'";
	}
	
	$hsguide_qry=mysqli_query($link, " SELECT `hguide_id`, `name` FROM `health_guide` WHERE `hguide_id`>0 $sguide_str ORDER BY `name` ");
	while($hsguide=mysqli_fetch_array($hsguide_qry))
	{
		echo "<option value='$hsguide[hguide_id]'>$hsguide[name]</option>";
	}
	
}

if($_POST["type"]=="health_guide_reports")
{
	$sguide_id=mysqli_real_escape_string($link, $_POST["sguide_id"]);
	$hguide_id=mysqli_real_escape_string($link, $_POST["hguide_id"]);
	
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
	
	$counter_num=mysqli_num_rows($counter_qry);
	
	if($counter_num>0)
	{
?>
	<p style="margin-top: 2%;">
		<?php echo $report_name; ?>
		<span class="text-right" id="print_div">
			<a class="btn btn-info btn-mini" href="pages/health_guide_reports_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&sguide_id=<?php echo $sguide_id;?>&hguide_id=<?php echo $hguide_id;?>"><i class="icon-file icon-large"></i> Export</a>
			
			<button type="button" class="btn btn-info btn-mini" onclick="print_page('<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $sguide_id;?>','<?php echo $hguide_id;?>')"><i class="icon-print icon-large"></i> Print</button>
		</span>
	</p>
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix">
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
		</thead>
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
<?php
	}
}

?>
