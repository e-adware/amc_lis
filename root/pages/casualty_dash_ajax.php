<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$date=date("Y-m-d");

$type=$_POST[type];

if($type==1)
{
	$ser_typ=$_POST[ser_typ];
	$usr=$_POST[usr];
	
	if($ser_typ==0)
	{
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='4' AND `date`='$date' ORDER BY `slno` DESC";
	}
	else
	{
		
		$uhid=mysqli_real_escape_string($link,$_POST[uhid]);
		$ipd=mysqli_real_escape_string($link,$_POST[ipd]);
		$name=mysqli_real_escape_string($link,$_POST[name]);
		$fdate=mysqli_real_escape_string($link,$_POST[fdate]);
		$tdate=mysqli_real_escape_string($link,$_POST[tdate]);
		//$date=date("d-m-Y");
		
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='4' AND `date`='$date' ORDER BY `slno` DESC";
		
		if($uhid)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='4'";
			$qry.=" and patient_id='$uhid'";	
		}
		if($ipd)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='4'";
			$qry.=" and opd_id='$ipd'";
		}
		if($name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='4'";
			$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		
		if(!$uhid && !$ipd && !$name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='4' AND `date`='$date' ORDER BY `slno` DESC";
		}
		
		if($fdate && $tdate)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='4' AND `date` between '$fdate' and '$tdate'";
		}
	}
	//echo $qry;
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='4' "));
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
		
		$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' AND `type`='2' "));
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
			$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
			$style="style='cursor:pointer;'";
			
			$tr_back_color="";
			$tr_title="";
		}
		
		
		$date_time=convert_date($q['date']).", Time: ".convert_time($q['time']);
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		echo "<tr $click $style $tr_back_color $tr_title><td>$i</td><td>$info[patient_id]</td><td>$q[opd_id]</td><td>$info[name]</td><td>$info[age] $info[age_type]</td><td>$info[sex]</td><td>$date_time</td></tr>";
		
		$i++;
	}
}

if($type==11)
{
	$ser_typ=$_POST[ser_typ];
	$usr=$_POST[usr];
	
	if($ser_typ==0)
	{
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6' AND `date`='$date' ORDER BY `slno` DESC";
	}
	else
	{
		
		$uhid=mysqli_real_escape_string($link,$_POST[uhid]);
		$ipd=mysqli_real_escape_string($link,$_POST[ipd]);
		$name=mysqli_real_escape_string($link,$_POST[name]);
		$fdate=mysqli_real_escape_string($link,$_POST[fdate]);
		$tdate=mysqli_real_escape_string($link,$_POST[tdate]);
		//$date=date("d-m-Y");
		
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6' AND `date`='$date' ORDER BY `slno` DESC";
		
		if($uhid)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6'";
			$qry.=" and patient_id='$uhid'";	
		}
		if($ipd)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6'";
			$qry.=" and opd_id='$ipd'";
		}
		if($name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6'";
			$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		
		if(!$uhid && !$ipd && !$name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6' AND `date`='$date' ORDER BY `slno` DESC";
		}
		
		if($fdate && $tdate)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6' AND `date` between '$fdate' and '$tdate'";
		}
	}
	//echo $qry;
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='6' "));
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
if($type==6)
{
	$ser_typ=$_POST[ser_typ];
	$usr=$_POST[usr];
	
	if($ser_typ==0)
	{
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6' AND `date`='$date' ORDER BY `slno` DESC";
	}
	else
	{
		
		$uhid=mysqli_real_escape_string($link,$_POST[uhid]);
		$ipd=mysqli_real_escape_string($link,$_POST[ipd]);
		$name=mysqli_real_escape_string($link,$_POST[name]);
		$fdate=mysqli_real_escape_string($link,$_POST[fdate]);
		$tdate=mysqli_real_escape_string($link,$_POST[tdate]);
		//$date=date("d-m-Y");
		
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6' AND `date`='$date' ORDER BY `slno` DESC";
		
		if($uhid)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6'";
			$qry.=" and patient_id='$uhid'";	
		}
		if($ipd)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6'";
			$qry.=" and opd_id='$ipd'";
		}
		if($name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6'";
			$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		
		if(!$uhid && !$ipd && !$name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6' AND `date`='$date' ORDER BY `slno` DESC";
		}
		
		if($fdate && $tdate)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='6' AND `date` between '$fdate' and '$tdate'";
		}
	}
	//echo $qry;
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='6' "));
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
		
		$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' AND `type`='2' "));
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
			$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
			$style="style='cursor:pointer;'";
			
			$tr_back_color="";
			$tr_title="";
		}
		
		$date_time=convert_date($q['date']).", Time: ".convert_time($q['time']);
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		echo "<tr $click $style $tr_back_color $tr_title><td>$i</td><td>$info[patient_id]</td><td>$q[opd_id]</td><td>$info[name]</td><td>$info[age] $info[age_type]</td><td>$info[sex]</td><td>$date_time</td></tr>";
		
		$i++;
	}
}
if($type==7)
{
	$ser_typ=$_POST[ser_typ];
	$usr=$_POST[usr];
	
	if($ser_typ==0)
	{
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='7' AND `date`='$date' ORDER BY `slno` DESC";
	}
	else
	{
		
		$uhid=mysqli_real_escape_string($link,$_POST[uhid]);
		$ipd=mysqli_real_escape_string($link,$_POST[ipd]);
		$name=mysqli_real_escape_string($link,$_POST[name]);
		$fdate=mysqli_real_escape_string($link,$_POST[fdate]);
		$tdate=mysqli_real_escape_string($link,$_POST[tdate]);
		//$date=date("d-m-Y");
		
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='7' AND `date`='$date' ORDER BY `slno` DESC";
		
		if($uhid)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='7'";
			$qry.=" and patient_id='$uhid'";	
		}
		if($ipd)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='7'";
			$qry.=" and opd_id='$ipd'";
		}
		if($name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='7'";
			$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		
		if(!$uhid && !$ipd && !$name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='7' AND `date`='$date' ORDER BY `slno` DESC";
		}
		
		if($fdate && $tdate)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='7' AND `date` between '$fdate' and '$tdate'";
		}
	}
	//echo $qry;
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='7' "));
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
		
		$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' AND `type`='2' "));
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
			$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
			$style="style='cursor:pointer;'";
			
			$tr_back_color="";
			$tr_title="";
		}
		
		$date_time=convert_date($q['date']).", Time: ".convert_time($q['time']);
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		echo "<tr $click $style $tr_back_color $tr_title><td>$i</td><td>$info[patient_id]</td><td>$q[opd_id]</td><td>$info[name]</td><td>$info[age] $info[age_type]</td><td>$info[sex]</td><td>$date_time</td></tr>";
		
		$i++;
	}
}

if($type==111) // Day Care
{
	$ser_typ=$_POST[ser_typ];
	$usr=$_POST[usr];
	
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
		//$date=date("d-m-Y");
		
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='5' AND `date`='$date' ORDER BY `slno` DESC";
		
		if($uhid)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='5'";
			$qry.=" and patient_id='$uhid'";	
		}
		if($ipd)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='5'";
			$qry.=" and opd_id='$ipd'";
		}
		if($name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='5'";
			$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		
		if(!$uhid && !$ipd && !$name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='5' AND `date`='$date' ORDER BY `slno` DESC";
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
			<th>Service</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
	<?php
	$i=1;
	$qr=mysqli_query($link,$qry);
	while($q=mysqli_fetch_array($qr))
	{
		$serv_str="";
		$pat_serv_qry=mysqli_query($link,"select * from ipd_pat_service_details where `patient_id`='$q[patient_id]' AND `ipd_id`='$q[opd_id]' ");
		while($pat_serv=mysqli_fetch_array($pat_serv_qry))
		{
			$serv_str.=$pat_serv["service_text"]."<br>";
		}
		
		$ac=mysqli_fetch_array(mysqli_query($link,"SELECT `ipd_cashier` FROM `cashier_access` WHERE `emp_id`='$usr'"));
		$lv=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$usr'"));
		if($ac['casuality_cashier']==0)
		{
			$click="";
			$style="";
		}
		
		$tr_style='style="cursor:pointer;"';
		$qotchk=mysqli_fetch_array(mysqli_query($link,"select * from ot_pat_service_details where `patient_id`='$q[patient_id]' AND `ipd_id`='$q[opd_id]' "));
		if($qotchk)
		{
			
			
			$tr_style='style="cursor:pointer;background-color: aquamarine;"';
		}
		
		if($ac['casuality_cashier']==1 || $lv['levelid']==1)
		{
			$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
			//$style="style='cursor:pointer;'";
		}
		$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
		//$style="style='cursor:pointer;'";
		
		$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' AND `type`='2' "));
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
			$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
			$style="style='cursor:pointer;'";
			
			$tr_back_color="";
			$tr_title="";
		}
		
		$date_time=convert_date($q['date']).", Time: ".convert_time($q['time']);
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		$usr=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$q[user]'"));
		echo "<tr $click $style $tr_back_color $tr_title ><td>$i</td><td>$info[patient_id]</td><td>$q[opd_id]</td><td>$info[name]</td><td>$info[age] $info[age_type]</td><td>$info[sex]</td><td>$serv_str</td><td>$date_time</td><td>$usr[name]</td></tr>";
		
		$i++;
	}
}

if($type==9) // Service
{
	$ser_typ=$_POST[ser_typ];
	$usr=$_POST[usr];
	
	if($ser_typ==0)
	{
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='9' AND `date`='$date' ORDER BY `slno` DESC";		
	}
	else
	{
		
		$uhid=mysqli_real_escape_string($link,$_POST[uhid]);
		$ipd=mysqli_real_escape_string($link,$_POST[ipd]);
		$name=mysqli_real_escape_string($link,$_POST[name]);
		$fdate=mysqli_real_escape_string($link,$_POST[fdate]);
		$tdate=mysqli_real_escape_string($link,$_POST[tdate]);
		//$date=date("d-m-Y");
		
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='9' AND `date`='$date' ORDER BY `slno` DESC";
		
		if($uhid)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='9'";
			$qry.=" and patient_id='$uhid'";	
		}
		if($ipd)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='9'";
			$qry.=" and opd_id='$ipd'";
		}
		if($name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='9'";
			$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		
		if(!$uhid && !$ipd && !$name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='9' AND `date`='$date' ORDER BY `slno` DESC";
		}
		
		if($fdate && $tdate)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='9' AND `date` between '$fdate' and '$tdate'";
		}
	}
	
	//echo $qry;
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='9' "));
?>
		<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th><?php echo $prefix_det["prefix"]; ?></th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Service</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
	<?php
	$i=1;
	$qr=mysqli_query($link,$qry);
	while($q=mysqli_fetch_array($qr))
	{
		$serv_str="";
		$pat_serv_qry=mysqli_query($link,"select * from ipd_pat_service_details where `patient_id`='$q[patient_id]' AND `ipd_id`='$q[opd_id]' ");
		while($pat_serv=mysqli_fetch_array($pat_serv_qry))
		{
			$serv_str.=$pat_serv["service_text"]."<br>";
		}
		
		$ac=mysqli_fetch_array(mysqli_query($link,"SELECT `ipd_cashier` FROM `cashier_access` WHERE `emp_id`='$usr'"));
		$lv=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$usr'"));
		if($ac['casuality_cashier']==0)
		{
			$click="";
			$style="";
		}
		
		$tr_style='style="cursor:pointer;"';
		$qotchk=mysqli_fetch_array(mysqli_query($link,"select * from ot_pat_service_details where `patient_id`='$q[patient_id]' AND `ipd_id`='$q[opd_id]' "));
		if($qotchk)
		{
			
			
			$tr_style='style="cursor:pointer;background-color: aquamarine;"';
		}
		
		if($ac['casuality_cashier']==1 || $lv['levelid']==1)
		{
			$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
			//$style="style='cursor:pointer;'";
		}
		$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
		//$style="style='cursor:pointer;'";
		
		$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' AND `type`='2' "));
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
			$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
			$style="style='cursor:pointer;'";
			
			$tr_back_color="";
			$tr_title="";
		}
		
		$date_time=convert_date($q['date']).", Time: ".convert_time($q['time']);
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		$usr=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$q[user]'"));
		echo "<tr $click $style $tr_back_color $tr_title ><td>$i</td><td>$info[patient_id]</td><td>$q[opd_id]</td><td>$info[name]</td><td>$info[age] $info[age_type]</td><td>$info[sex]</td><td>$serv_str</td><td>$date_time</td><td>$usr[name]</td></tr>";
		
		$i++;
	}
}

if($type==3)
{
	$ser_typ=$_POST[ser_typ];
	$usr=$_POST[usr];
	
	if($ser_typ==0)
	{
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='8' ORDER BY `slno` DESC";
	}
	else
	{
		$uhid=mysqli_real_escape_string($link,$_POST[uhid]);
		$ipd=mysqli_real_escape_string($link,$_POST[ipd]);
		$name=mysqli_real_escape_string($link,$_POST[name]);
		$fdate=mysqli_real_escape_string($link,$_POST[fdate]);
		$tdate=mysqli_real_escape_string($link,$_POST[tdate]);
		//$date=date("d-m-Y");
		
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='8' ORDER BY `slno` DESC";
		
		if($uhid)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='8'";
			$qry.=" and patient_id='$uhid'";	
		}
		if($ipd)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='8'";
			$qry.=" and opd_id='$ipd'";
		}
		if($name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='8'";
			$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		
		if($fdate && $tdate)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='8' AND `date` between '$fdate' and '$tdate'";
		}
	}
	//echo $qry;
	
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='8' "));
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
		if($ac['ipd_cashier']==0)
		{
			$click="";
			$style="";
		}
		if($ac['ipd_cashier']==1 || $lv['levelid']==1 || $lv['levelid']==7)
		{
			$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
			$style="style='cursor:pointer;'";
		}
		$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
		$style="style='cursor:pointer;'";
		
		
		$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `opd_id` IN(SELECT `ipd_id` FROM `ipd_pat_delivery_det` WHERE `patient_id`='$q[patient_id]' AND `ipd_id`='$q[opd_id]') AND `type`='2' "));
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
			$click="onclick=\"redirect_page('$q[patient_id]','$q[opd_id]')\"";
			$style="style='cursor:pointer;'";
			
			$tr_back_color="";
			$tr_title="";
		}	
		
		$date_time=convert_date($q['date']).", Time: ".convert_time($q['time']);
		
		$pat_info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		
		if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".convert_date_g($pat_info["dob"]).")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
		
		echo "<tr $click $style $tr_back_color $tr_title><td>$i</td><td>$pat_info[patient_id]</td><td>$q[opd_id]</td><td>$pat_info[name]</td><td>$age</td><td>$pat_info[sex]</td><td>$date_time</td></tr>";
		
		$i++;
	}
}

if($type==15) // Procedure
{
	$ser_typ=$_POST[ser_typ];
	$usr=$_POST[usr];
	
	if($ser_typ==0)
	{
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='15' AND `date`='$date' ORDER BY `slno` DESC";
	}
	else
	{
		
		$uhid=mysqli_real_escape_string($link,$_POST[uhid]);
		$ipd=mysqli_real_escape_string($link,$_POST[ipd]);
		$name=mysqli_real_escape_string($link,$_POST[name]);
		$fdate=mysqli_real_escape_string($link,$_POST[fdate]);
		$tdate=mysqli_real_escape_string($link,$_POST[tdate]);
		//$date=date("d-m-Y");
		
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='15' AND `date`='$date' ORDER BY `slno` DESC";
		
		if($uhid)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='15'";
			$qry.=" and patient_id='$uhid'";	
		}
		if($ipd)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='15'";
			$qry.=" and opd_id='$ipd'";
		}
		if($name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='15'";
			$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		
		if(!$uhid && !$ipd && !$name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='15' AND `date`='$date' ORDER BY `slno` DESC";
		}
		
		if($fdate && $tdate)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='15' AND `date` between '$fdate' and '$tdate'";
		}
	}
	//echo $qry;
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='15' "));
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

if($type==14) // Ambulance
{
	$ser_typ=$_POST[ser_typ];
	$usr=$_POST[usr];
	
	if($ser_typ==0)
	{
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='14' AND `date`='$date' ORDER BY `slno` DESC";
	}
	else
	{
		
		$uhid=mysqli_real_escape_string($link,$_POST[uhid]);
		$ipd=mysqli_real_escape_string($link,$_POST[ipd]);
		$name=mysqli_real_escape_string($link,$_POST[name]);
		$fdate=mysqli_real_escape_string($link,$_POST[fdate]);
		$tdate=mysqli_real_escape_string($link,$_POST[tdate]);
		//$date=date("d-m-Y");
		
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='14' AND `date`='$date' ORDER BY `slno` DESC";
		
		if($uhid)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='14'";
			$qry.=" and patient_id='$uhid'";	
		}
		if($ipd)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='14'";
			$qry.=" and opd_id='$ipd'";
		}
		if($name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='14'";
			$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		}
		
		if(!$uhid && !$ipd && !$name)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='14' AND `date`='$date' ORDER BY `slno` DESC";
		}
		
		if($fdate && $tdate)
		{
			$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='14' AND `date` between '$fdate' and '$tdate'";
		}
	}
	//echo $qry;
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='14' "));
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
