<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$date=date("Y-m-d");

$type=$_POST["type"];


if($type==1)
{
	$ser_typ=$_POST["ser_typ"];
	$usr=$_POST["usr"];
	
	if($ser_typ==0)
	{
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='5' AND `date`='$date' ORDER BY `slno` DESC";		
	}
	else
	{
		
		$uhid=mysqli_real_escape_string($link,$_POST[uhid]);
		$ipd=mysqli_real_escape_string($link,$_POST[ipd]);
		$name=mysqli_real_escape_string($link,$_POST[name]);
		$fdate=mysqli_real_escape_string($link,$_POST[fdate]);
		$tdate=mysqli_real_escape_string($link,$_POST[tdate]);
		$date=date("d-m-Y");
		
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='5'";
		if($uhid)
		{
			//$p=mysqli_fetch_array(mysqli_query($link,"select patient_id from patient_info where uhid='$uhid'"));
			$qry.=" and patient_id='$uhid'";	
		}
		if($ipd)
		{
			$qry.=" and opd_id='$ipd'";
		}
		if($name)
		{
			$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		
		if(!$uhid && !$ipd && !$name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='5' ORDER BY `slno` DESC";
		}
		
		if($fdate && $tdate)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='5' AND `date` between '$fdate' and '$tdate'";
		}
	}
	//echo $qry;
	
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='5' "));
	
?>
		<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th><?php echo $prefix_det["prefix"]; ?></th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Date Time</th>
		</tr>
	<?php
	$i=1;
	$qr=mysqli_query($link,$qry);
	while($q=mysqli_fetch_array($qr))
	{		
		$ac=mysqli_fetch_array(mysqli_query($link,"SELECT `ipd_cashier` FROM `cashier_access` WHERE `emp_id`='$usr'"));
		$lv=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$usr'"));
		if($ac['casuality_cashier']==0)
		{
			$click="";
			$style="";
		}
		if($ac['casuality_cashier']==1 || $lv['levelid']==1)
		{
			$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
			$style="style='cursor:pointer;'";
		}
		$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
		$style="style='cursor:pointer;'";
		
		$date_time=convert_date($q['date']).", Time: ".convert_time($q['time']);
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		echo "<tr $click $style><td>$i</td><td>$info[patient_id]</td><td>$q[opd_id]</td><td>$info[name]</td><td>$info[age] $info[age_type]</td><td>$info[sex]</td><td>$date_time</td></tr>";
		
		$i++;
	}
}

?>
