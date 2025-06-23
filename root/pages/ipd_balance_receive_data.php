<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

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
$rupees_symbol="&#x20b9; ";

if($_POST["type"]=="load_all_pat")
{
	$search_data=$_POST["search_data"];
	
	$$search_data_str="";
	if(strlen($search_data)>2)
	{
		$search_data_str=" AND `ipd_id` LIKE '$search_data%'";
	}
?>
	<table class="table table-bordered table-condensed" id="tblData">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Age / Sex</th>
			<th>Balance Amount</th>
			<!--<th>Discharge Date</th>-->
		</tr>
<?php
	$bal_pat_qry=mysqli_query($link," SELECT a.* FROM `ipd_discharge_balance_pat` a, `uhid_and_opdid` b WHERE a.`slno`>0 AND a.`ipd_id`=b.`opd_id` $search_data_str ORDER BY a.`slno` DESC LIMIT 0,100"); // 
	$n=1;
	while($bal_pat=mysqli_fetch_array($bal_pat_qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$bal_pat[patient_id]' "));
		
		$discharge_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$bal_pat[patient_id]' AND `ipd_id`='$bal_pat[ipd_id]' "));
	?>
		<tr onClick="redirect_ipd_balance('<?php echo $bal_pat['patient_id']; ?>','<?php echo $bal_pat['ipd_id']; ?>')" style="cursor:pointer;">
			<td><?php echo $n; ?></td>
			<td><?php echo $bal_pat["patient_id"]; ?></td>
			<td><?php echo $bal_pat["ipd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $pat_info["age"]; ?> <?php echo $pat_info["age_type"]; ?> / <?php echo $pat_info["sex"]; ?></td>
			<td><?php echo $bal_pat["bal_amount"]; ?></td>
			<!--<td><?php if($discharge_info["date"]){ echo convert_date($discharge_info["date"]);} ?></td>-->
		</tr>
	<?php
		$n++;
	}
?>
	</table>
<?php
}

if($_POST["type"]=="save_ipd_bal")
{
	$uhid=$_POST["uhid"];
	$ipd=$_POST["ipd"];
	$discount=$_POST["discount"];
	$advance=$_POST["advance"];
	$pay_mode=$_POST["pay_mode"];
	$reference_no=$_POST["reference_no"];
	$tot_bill_amt=$_POST["tot_bill_amt"];
	$user=$_POST["user"];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
	$bill_name=$pat_typ_text["bill_name"];
	
	$bal_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
	
	$balance=$bal_info["bal_amount"];
	
	$remain_balance=$balance-$discount-$advance;
	
	
	$bill_no=101;
	$date2=date("Y-m-d");
	$date1=explode("-",$date2);	
	$c_var=$date1[0]."-".$date1[1];
	$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
	
	if($chk['tot_bill']>0)
	{
		$bill_no=$bill_no+$chk['tot_bill'];
	}
	$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
	$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
	
	$date4=date("y-m-d");
	$date3=explode("-",$date4);
	
	$random_no=rand(1,9);
	
	$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
	
	if(mysqli_query($link, " INSERT INTO `ipd_advance_payment_details`(`patient_id`, `ipd_id`, `bill_no`, `tot_amount`, `discount`, `amount`, `balance`, `refund`, `pay_type`, `pay_mode`, `reference_no`, `time`, `date`, `user`) VALUES ('$uhid','$ipd','$bill_id','$tot_bill_amt','$discount','$advance','$remain_balance','0','Balance','$pay_mode','$reference_no','$time','$date','$user') "))
	{
		mysqli_query($link, " UPDATE `ipd_discharge_balance_pat` SET `bal_amount`='$remain_balance' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
		
		echo "Saved";
	}
	else
	{
		echo "Failed. Try again later.";
	}
	
}
