<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$time=date('H:i:s');
$date=date("Y-m-d");

$type=$_POST['type'];

if($type=="view_admit_pat_list")
{
	$typ=$_POST['typ'];
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$doc=$_POST['consultantdoctorid'];
	if($typ=="admit")
	{
		$doc_type="Admited Doctor";
		
		$q="SELECT a.*,b.`attend_doc`,b.`admit_doc`,b.`dept_id` FROM `uhid_and_opdid` a, `ipd_pat_doc_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`ipd_id` AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`type`='3'";
		
		if($doc>0)
		{
			$q.=" AND b.`admit_doc`='$doc'";
		}
	}
	if($typ=="attend")
	{
		$doc_type="Consultant Doctor";
		
		$q="SELECT a.*,b.`attend_doc`,b.`admit_doc`,b.`dept_id` FROM `uhid_and_opdid` a, `ipd_pat_doc_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`ipd_id` AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`type`='3'";
		
		if($doc>0)
		{
			$q.=" AND b.`attend_doc`='$doc'";
		}
	}
	//echo $q;
	$qry=mysqli_query($link,$q);
	
?>
	<div style="text-align:right;">
		<button type="button" class="btn btn-print btn-hide-print" onclick="print_page('<?php echo $typ;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $doc;?>')"><i class="icon-print icon-large"></i> Print</button>
		<a class="btn btn-excel btn-hide-print" href="pages/admit_pat_reports_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&doc=<?php echo $doc;?>&typ=<?php echo $typ;?>" ><i class="icon-file icon-large"></i>Excel</a>
	</div>
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
	<?php
}

if($type=="")
{
	
}
?>
