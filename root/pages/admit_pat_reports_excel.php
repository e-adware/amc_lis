<?php
session_start();
$c_user=trim($_SESSION['emp_id']);

include'../../includes/connection.php';
include("../../includes/global.function.php");

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$doc=$_GET['doc'];
$typ=$_GET['typ'];

if($typ=="admit")
{
	$doc_type="Admited Doctor";
	$filename ="admit_patient_reports_".$date1."_to_".$date2.".xls";
	
	$q="SELECT a.*,b.`attend_doc`,b.`admit_doc`,b.`dept_id` FROM `uhid_and_opdid` a, `ipd_pat_doc_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`ipd_id` AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`type`='3'";
	
	if($doc>0)
	{
		$q.=" AND b.`admit_doc`='$doc'";
	}
}
if($typ=="attend")
{
	$doc_type="Consultant Doctor";
	$filename ="consult_patient_reports_".$date1."_to_".$date2.".xls";
	
	$q="SELECT a.*,b.`attend_doc`,b.`admit_doc`,b.`dept_id` FROM `uhid_and_opdid` a, `ipd_pat_doc_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`ipd_id` AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`type`='3'";
	
	if($doc>0)
	{
		$q.=" AND b.`attend_doc`='$doc'";
	}
}
//echo $q;
$qry=mysqli_query($link,$q);

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
<html>
<head>
<title>Reports</title>

<style>
input[type="text"]
{
	border:none;
}
body {
	padding: 10px;
	font-size:13px; font-family:Arial, Helvetica, sans-serif; line-height: 18px;
}
.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}
.bline td{border-bottom:1px solid;}
*{font-size:13px;}
</style>
</head>
<body>
	<div class="container">
		<table class="table table-condensed table-bordered">
			<tr>
				<th>#</th><th>UHID</th><th>Bill No.</th><th>Name</th><th>Sex</th><th>Age (Dob)</th><th><?php echo $doc_type;?></th><th>User</th><th>Reg. Time</th>
			</tr>
<?php
		$i=1;
		while($pat_reg=mysqli_fetch_array($qry))
		{
			$doc_name="";
			$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid`,`name`,`sex`,`dob`,`age`,`age_type`,`phone` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]'"));
			
			$reg_date=$pat_reg["date"];
			
			if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			
			if($doc_type=="Admited Doctor")
			{
				$d=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `admit_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[opd_id]')"));
				$doc_name=$d['Name'];
			}
			if($doc_type=="Consultant Doctor")
			{
				$d=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[opd_id]')"));
				$doc_name=$d['Name'];
			}
			$u=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $pat_reg['patient_id'];?></td>
			<td><?php echo $pat_reg['opd_id'];?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $pat_info['sex'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $doc_name;?></td>
			<td><?php echo $u['name'];?></td>
			<td><?php echo convert_date($pat_reg['date'])." ".$pat_reg['time'];?></td>
		</tr>
<?php
			$i++;
		}
?>
		</table>
	</div>
</body>
</html>
