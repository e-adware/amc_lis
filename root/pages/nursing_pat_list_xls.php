<?php
session_start();
include('../../includes/connection.php');
require('../../includes/global.function.php');

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$ward=$_GET['ward'];
$uhid=$_GET['uhid'];
$ipd=$_GET['ipd'];
$name=$_GET['name'];
$date=$_GET['dat'];

if(!$date)
{
	$date=date("Y-m-d");
}

$filename ="admitted_patient_list_on_".$date.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
<p style="margin-top: 2%;" id="print_div">
	<b>Admitted patient list on:</b> <?php echo convert_date($date); ?>
</p>
<?php

	$zz=0;
	
	$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date') ";
	
	if($dat)
	{
		$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$dat' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$dat') ";
	}
	//$q=" SELECT a.*, c.`bed_no` FROM `uhid_and_opdid` a, `ipd_bed_alloc_details` b, `bed_master` c WHERE a.`opd_id`=b.`ipd_id` AND b.`bed_id`=c.`bed_id` AND a.`type`='3' AND b.`alloc_type`='1' ";
	
	if($ward>0)
	{
		$q.=" AND `ward_id`='$ward'";
		$zz=0;
	}
	if($uhid)
	{
		if(strlen($uhid)>2)
		{
			$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `patient_id` like '$uhid%'";
			
			$zz=1;
		}
	}
	if($ipd)
	{
		if(strlen($ipd)>2)
		{
			$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `ipd_id` like '$ipd%'";
			
			$zz=1;
		}
	}
	if($name)
	{
		if(strlen($name)>2)
		{
			$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$name%')";
			
			$zz=1;
		}
	}
	
	//~ if($zz==0)
	//~ {
		//~ $q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date') ";
		
		//~ if($ward>0)
		//~ {
			//~ $q.=" AND `ward_id`='$ward'";
		//~ }
	//~ }
	
	$q.=" ORDER BY `slno` ASC";
	
	//echo $q;
	
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		
?>
		<p style="margin-top: 2%;" id="print_div">
			<span class="text-right" id="excel_btn_hide"><a class="btn btn-info btn-mini" href="pages/nursing_pat_list_xls.php?ward=<?php echo $ward ?>&uhid=<?php echo $uhid ?>&ipd=<?php echo $ipd ?>&name=<?php echo $name ?>&dat=<?php echo $dat ?>"><i class="icon-file icon-large" style="line-height: 24px;"></i> Excel</a></span>
			
		</p>
		<table class="table table-condensed table-bordered">
			<tr>
				<!--<th>UHID</th>-->
				<th>#</th>
				<th>IPD ID</th>
				<th>Name</th>
				<th>Sex</th>
				<th>Age (DOB)</th>
				<th>Ward</th>
				<th>Bed No</th>
				<th>Doctor</th>
				<th>User</th>
			</tr>
		<?php
			$n=1;
			while($data=mysqli_fetch_array($qq))
			{
				$r=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$data[ipd_id]'"));
				
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
				if($p["dob"]!=""){ $age=age_calculator($p["dob"])." (".convert_date_g($p["dob"]).")"; }else{ $age=$p["age"]." ".$p["age_type"]; }
				
				$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$r[patient_id]' and `opd_id`='$r[opd_id]' "));
		
				$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$dt_tm[user]' "));
				
				$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$r[patient_id]' and ipd_id='$r[opd_id]'"));
				if($bed_det['bed_id'])
				{
					$ward=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$bed_det[ward_id]'"));
					$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$bed_det[bed_id]'"));
					
					$ward=$ward["name"];
					$bed=$bed_det["bed_no"];
				}else
				{
					$ward="";
					$bed="";
					$bed_alloc_qry=mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$r[patient_id]' and ipd_id='$r[opd_id]' and alloc_type=1 order by slno asc");
					while($bed_alloc=mysqli_fetch_array($bed_alloc_qry))
					{
						$ward_val=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$bed_alloc[ward_id]'"));
						$bed_det_val=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$bed_alloc[bed_id]'"));
						
						$ward.=$ward_val["name"]."<br>";
						$bed.=$bed_det_val["bed_no"]."<br>";
					}
					//$ward="Discharged";
					//$bed="Discharged";
				}
				// Consultant Doctor
				$at_doc=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$r[patient_id]' and `ipd_id`='$r[opd_id]' ) "));
				
				
		?>
				<tr>
					<!--<td><?php echo $p['patient_id'];?></td>-->
					<td><?php echo $n;?></td>
					<td><?php echo $r['opd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $age;?></td>
					<td><?php echo $ward;?></td>
					<td><?php echo $bed;?></td>
					<td><?php echo $at_doc['Name'];?></td>
					<td><?php echo $emp_info['name'];?></td>
				</tr>
			<?php
				$n++;
			}
		?>
		</table>
		<?php
	}
?>
