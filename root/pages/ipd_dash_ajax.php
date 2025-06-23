<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_info["branch_id"];

$type=$_POST[type];

if($type==1)
{
	$branch_id=$_POST["branch_id"];
	$ser_typ=$_POST["ser_typ"];
	$usr=$_POST["usr"];
	$list_start=$_POST["list_start"];
	
	if($ser_typ==0)
	{	
		//$qry="select * from ipd_pat_bed_details order by slno asc";
		$qry=" SELECT a.*, b.`bed_no` FROM `ipd_pat_bed_details` a, `bed_master` b WHERE a.`bed_id`=b.`bed_id` ";
	}
	else
	{
		
		$uhid=mysqli_real_escape_string($link,$_POST["uhid"]);
		$ipd=mysqli_real_escape_string($link,$_POST["ipd"]);
		$ipd_serial=mysqli_real_escape_string($link,$_POST["ipd_serial"]);
		$name=mysqli_real_escape_string($link,$_POST["name"]);
		$phone=mysqli_real_escape_string($link,$_POST["phone"]);
		$fdate=mysqli_real_escape_string($link,$_POST["fdate"]);
		$tdate=mysqli_real_escape_string($link,$_POST["tdate"]);
		$date=date("d-m-Y");
		
		//$qry="select * from ipd_pat_bed_details where slno>0";
		$qry=" SELECT a.*, b.`bed_no` FROM `ipd_pat_bed_details` a, `bed_master` b WHERE a.`bed_id`=b.`bed_id` ";
		
		if($uhid)
		{
			$qry.=" and a.patient_id='$uhid'";
		}
		if($ipd)
		{
			$qry.=" and a.ipd_id='$ipd'";
		}
		if($ipd_serial)
		{
			$p=mysqli_fetch_array(mysqli_query($link," SELECT `opd_id` FROM `uhid_and_opdid` WHERE `ipd_serial`='$ipd_serial' "));
			$qry.=" and a.ipd_id='$p[opd_id]'";
		}
		if($name)
		{
			$qry.=" and a.patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		if($phone)
		{
			$qry.=" and a.patient_id in(select patient_id from patient_info where phone='$phone')";
		}

		if($fdate && $tdate)
		{
			$qry=" SELECT a.*, b.`bed_no` FROM `ipd_pat_bed_details` a, `bed_master` b WHERE a.`bed_id`=b.`bed_id` AND a.`date` between '$fdate' and '$tdate' ";
			//$qry="select * from ipd_pat_bed_details where date between '$fdate' and '$tdate'";
		}
	}
	
	$qry.=" and a.ipd_id IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
	
	$qry.=" ORDER BY a.`slno` ASC limit ".$list_start;
	
	//echo $qry;
	
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='3' "));
?>
		<table class="table table-condensed table-bordered">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>UNIT NO.</th>
					<th><?php echo $prefix_det["prefix"]; ?></th>
					<th>Name</th>
					<th>Age</th>
					<th>Sex</th>
					<!--<th>Floor</th>-->
					<!--<th>Ward</th>
					<th>Bed No</th>-->
					<th style="text-align:right;">Outstanding Amount</th>
					<th>Admission Time</th>
					<th>User</th>
				</tr>
			</thead>
	<?php
	$i=1;
	$qr=mysqli_query($link,$qry);
	while($q=mysqli_fetch_array($qr))
	{
		$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' and `opd_id`='$q[ipd_id]' "));
		$entry_date_time=convert_date_g($dt_tm['date'])." ".convert_time($dt_tm['time']);
		
		$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$dt_tm[user]' "));
		
		$lv=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$usr'"));
		
		$click="onclick=\"redirect_page('$q[patient_id]','$q[ipd_id]')\"";
		$style="style='cursor:pointer;'";
		
		$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]'"));
		if($bed_det['bed_id'])
		{
			$ward=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$bed_det[ward_id]'"));
			$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$bed_det[bed_id]'"));
		}
		else
		{
			
			$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from ipd_bed_details_temp where patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]'"));
			$ward=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$bed_det[ward_id]'"));
			$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$bed_det[bed_id]'"));
		}
		
		// Outstanding Amount
		//$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select SUM(`ser_quantity`) as tot_day from ipd_pat_service_details where patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]' and `group_id`='141' "));
		//~ //$no_of_days=$no_of_days_val["tot_day"];
		
		//~ $tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]' and group_id='141' "));
		//~ $tot_serv_amt1=$tot_serv1["tots"];
		
		//~ $tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]' and group_id!='141' "));
		//~ $tot_serv_amt2=$tot_serv2["tots"];
		
		//~ // OT Charge
		//~ $grp_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]' "));
		//~ $grp_tot=$grp_tot_val["g_tot"];
		
		//~ $delivery_check_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]' "));
		
		//~ if($delivery_check_val)
		//~ {
			//~ $baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' "));
			//~ $baby_serv_tot=$baby_tot_serv["tots"];
			
			//~ // OT Charge Baby
			//~ $baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' "));
			//~ $baby_ot_total=$baby_ot_tot_val["g_tot"];
		//~ }
		
		//~ $tot_serv_amt=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$grp_tot+$baby_ot_total;
		
		$tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`service_amount`),0) as tots FROM `ipd_pat_daily_service_details` WHERE patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]'"));
		$tot_serv_amt=$tot_serv["tots"];
		
		//echo $tot_serv_amt."<br>";
		
		$adv_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as advs FROM `ipd_advance_payment_details` WHERE patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]' and pay_type='Advance' "));
		$adv_serv_amt=$adv_serv["advs"];
		$final_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as final,sum(`discount`) as discnt FROM `ipd_advance_payment_details` WHERE patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]' and pay_type='Final' "));
		$final_serv_amt=$final_serv["final"];
		$adv_serv_dis=$final_serv["discnt"];
		
		$tot_outstanding=number_format(($tot_serv_amt-$adv_serv_amt-$final_serv_amt-$adv_serv_dis),2);
		
		$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[ipd_id]' AND `type`='2' "));
		if($cancel_request)
		{
			$click="";
			$style="";
			$tr_back_color="style='background-color: #ff000021'";
			
			$emp_info_del=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$cancel_request[user]' "));
			
			$tr_title="title='Cancel request by $emp_info_del[name]'";
		}
		else
		{
			$click="onclick=\"redirect_page('$q[patient_id]','$q[ipd_id]')\"";
			$style="style='cursor:pointer;'";
			
			$tr_back_color="";
			$tr_title="";
		}
		
		if($ser_typ==0)
		{
			$chk_dis=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_discharge_details where patient_id='$q[patient_id]' and ipd_id='$q[ipd_id]'"));
			if($chk_dis==0)
			{
				$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
				
				if($info["dob"]!=""){ $age=age_calculator($info["dob"])." (".$info["dob"].")"; }else{ $age=$info["age"]." ".$info["age_type"]; }
				
				//echo "<tr $click $style $tr_back_color $tr_title><td>$i</td><td>$q[ipd_id]</td><td>$info[name]</td><td>$age</td><td>$info[sex]</td><td>$ward[name]</td><td>$bed_det[bed_no]</td><td>$tot_outstanding</td><td>$entry_date_time</td><td>$emp_info[name]</td></tr>";
				
				echo "<tr $click $style $tr_back_color $tr_title><td>$i</td><td>$info[patient_id]</td><td>$q[ipd_id]</td><td>$info[name]</td><td>$age</td><td>$info[sex]</td><td style='text-align:right;'>$tot_outstanding</td><td>$entry_date_time</td><td>$emp_info[name]</td></tr>";
				
			}else
			{
				
			}
		}
		else
		{
			$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
			
			if($info["dob"]!=""){ $age=age_calculator($info["dob"])." (".$info["dob"].")"; }else{ $age=$info["age"]." ".$info["age_type"]; }
			
			//echo "<tr $click $style $tr_back_color $tr_title><td>$i</td><td>$q[ipd_id]</td><td>$info[name]</td><td>$age</td><td>$info[sex]</td><td>$ward[name]</td><td>$bed_det[bed_no]</td><td>$tot_outstanding</td><td>$entry_date_time</td><td>$emp_info[name]</td></tr>";
			
			echo "<tr $click $style $tr_back_color $tr_title><td>$i</td><td>$info[patient_id]</td><td>$q[ipd_id]</td><td>$info[name]</td><td>$age</td><td>$info[sex]</td><td style='text-align:right;'>$tot_outstanding</td><td>$entry_date_time</td><td>$emp_info[name]</td></tr>";
		}
		$i++;
	}
}
?>
