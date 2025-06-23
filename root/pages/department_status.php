<?php
include("../../includes/connection.php");

$date1=base64_decode($_GET['dt1']);
$date2=base64_decode($_GET['dt2']);
$typ=base64_decode($_GET['tYp']);
if($typ==1)
{
	$page_header="Department Wise Patient Summary List";
}
if($typ==2)
{
	$page_header="Date Wise Patient Details List";
}
if($date1=="")
{
	$date1=date("Y-m-d");
}
if($date2=="")
{
	$date2=date("Y-m-d");
}
// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d-M-Y', $timestamp);
	return $new_date;
}
function age_calculator($dob)
{
	//~ $from = new DateTime($dob);
	//~ $to   = new DateTime('today');
	//~ $year=$from->diff($to)->y;
	//~ $month=$from->diff($to)->m;
	//~ if($year==0)
	//~ {
		//~ //$month=$from->diff($to)->m;
		//~ if($month==0)
		//~ {
			//~ $day=$from->diff($to)->d;
			//~ return $day." Days";
		//~ }else
		//~ {
			//~ return $month." Months";
		//~ }
	//~ }else
	//~ {
		//~ return $year.".".$month." Years";
	//~ }
	
	$bday = new DateTime($dob);
	$today = new DateTime('today');
	$diff = $today->diff($bday);
    
    $age_str="";
	if($diff->y>0)
	{
		$age_str=$diff->y." Years";
	}
	if($diff->m>0)
	{
		$age_str.=" ".$diff->m." Months";
	}
	if($diff->d>0)
	{
		$age_str.=" ".$diff->d." Days";
	}
	
	return $age_str;
	//return $diff->y."Y ".$diff->m."M ".$diff->d."D";
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>Department Summary</title>
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
</head>

<body onkeyup="close_window(event)">
	<div class="container-fluid">
		<center>
			<h4><?php echo $page_header;?></h4>
		</center>
		<?php
		if($typ==1)
		{
		$all_tot=0;
		$qq=mysqli_query($link,"SELECT DISTINCT `date` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2'");
		$n=mysqli_num_rows($qq);
		if($n>0)
		{
		?>
		<table class="table table-condensed">
		<thead class="table_header_fix">
		<tr>
		<th>#</th>
		<th>Department</th>
		<th style="text-align:right;">No of patients</th>
		</tr>
		</thead>
		<tr>
		<th colspan="3" style="background:#AAAAAA;"></th>
		</tr>
		<?php
		while($rr=mysqli_fetch_assoc($qq))
		{
		?>
		<tr>
		<th colspan="3">Date : <?php echo convert_date($rr['date']);?></th>
		</tr>
		<?php
		$j=1;
		$tot=0;
		$q=mysqli_query($link,"SELECT DISTINCT u.`type`, p.`p_type` FROM `uhid_and_opdid` u, `patient_type_master` p WHERE u.`type`=p.`p_type_id` AND u.`date`='$rr[date]' ORDER BY p.`p_type_id`");
		while($r=mysqli_fetch_assoc($q))
		{
		$count=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`opd_id`) AS cnt FROM `uhid_and_opdid` WHERE `type`='$r[type]' AND `date`='$rr[date]'"));
		?>
		<tr>
		<td><?php echo $j;?></td>
		<td><?php echo $r['p_type'];?></td>
		<td style="text-align:right;"><?php echo $count['cnt'];?></td>
		</tr>
		<?php
		$j++;
		$tot+=$count['cnt'];
		}
		?>
		<tr>
		<td></td>
		<th>Total</th>
		<th style="text-align:right;"><?php echo $tot;?></th>
		</tr>
		<tr>
		<th colspan="3" style="background:#AAAAAA;"></th>
		</tr>
		<?php
		$all_tot+=$tot;
		}
		?>
		<tr>
		<td></td>
		<th>All Total</th>
		<th style="text-align:right;"><?php echo $all_tot;?></th>
		</tr>
		</table>
		<?php
		}
		}
		/*-------------------------------------------------------------------------------*/
		if($typ==2)
		{
		$qry=mysqli_query($link,"SELECT DISTINCT `date` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2'");
		$n=mysqli_num_rows($qry);
		if($n>0)
		{
		?>
		<table class="table table-condensed">
		<thead class="table_header_fix">
		<tr>
		<th>#</th>
		<th>UHID</th>
		<th>PIN</th>
		<th>Patient Name</th>
		<th>Age/Sex</th>
		<th>Phone</th>
		<th>Type</th>
		<th>User</th>
		</tr>
		</thead>
		<?php
		while($res=mysqli_fetch_assoc($qry))
		{
		?>
		<tr>
		<th colspan="8">Date : <?php echo convert_date($res['date']);?></th>
		</tr>
		<?php
		$j=1;
		$qq=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$res[date]' order by `slno`");
		while($rr=mysqli_fetch_assoc($qq))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$rr[patient_id]' "));
			if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$rr[type]' "));
			$pat_typ=$pat_typ_text['p_type'];
			$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$rr[user]' "));
		?>
		<tr>
		<td><?php echo $j;?></td>
		<td><?php echo $rr['patient_id'];?></td>
		<td><?php echo $rr['opd_id'];?></td>
		<td><?php echo $pat_info['name'];?></td>
		<td><?php echo $age;?></td>
		<td><?php echo $pat_info['phone'];?></td>
		<td><?php echo $pat_typ;?></td>
		<td><?php echo $user_info["name"];?></td>
		</tr>
		<?php
		$j++;
		}
		}
		?>
		</table>
		<?php
		}
		}
		?>
	</div>
</body>
<script>
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
<style>
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 1px 1px 1px 5px;
	font-size: 13px;
}
.table-bordered
{
	border-radius: 0;
}
</style>
</html>


