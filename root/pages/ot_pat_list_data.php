<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user=$_SESSION["emp_id"];

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="ot_pat_list")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$user=$_POST['usr'];
	$list_start=$_POST["list_start"];
	
	$zz=0;
	
	$str=" SELECT * FROM `patient_ot_schedule` WHERE `schedule_id`>0";
	
	if($uhid)
	{
		if(strlen($uhid)>2)
		{
			$str.=" AND `patient_id`='$uhid' ";
			$zz++;
		}
	}
	if($ipd)
	{
		if(strlen($ipd)>2)
		{
			$str.=" AND `ipd_id`='$ipd' ";
			$zz++;
		}
	}
	if($name)
	{
		if(strlen($name)>3)
		{
			$str.=" AND `patient_id` IN(SELECT `patient_id` FROM `patient_info` WHERE `name` LIKE '%$name%') ";
			$zz++;
		}
	}
	if($dat)
	{
		$str.=" AND `date`='$dat' ";
		$zz++;
	}
	
	if($zz==0)
	{
		//$str.=" AND `ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_bed_details`) ";
	}
	$str.=" ORDER BY `schedule_id` DESC limit ".$list_start;
	
	//echo $str;
	
	$num=mysqli_num_rows(mysqli_query($link,$str));
	if($num>0)
	{
		$qry=mysqli_query($link,$str);
?>
		<table class="table table-condensed table-bordered" style="background-color:white;">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Unit No.</th>
					<th>Bill No.</th>
					<th>Patient Name</th>
					<th>Sex</th>
					<th>Age</th>
					<th>OT Room</th>
					<th>OT Date</th>
					<th>OT Department</th>
					<th>Entry Date</th>
				</tr>
			</thead>
<?php
			$n=1;
			while($data=mysqli_fetch_array($qry))
			{
				$display=1;
				if($display==1)
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$data[patient_id]'"));
					
					$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$data[patient_id]' and `opd_id`='$data[ipd_id]' "));
					
					$reg_date=$pat_reg["date"];
					
					if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
					
					$ot_area=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$data[ot_area_id]'"));
					$ot_dept=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$data[ot_dept_id]'"));
?>
				<tr onclick="redirect_page('<?php echo $data['patient_id'];?>','<?php echo $data['ipd_id'];?>','<?php echo $data['schedule_id'];?>')" style="cursor:pointer;">
					<td><?php echo $n;?></td>
					<td><?php echo $data['patient_id'];?></td>
					<td><?php echo $data['ipd_id'];?></td>
					<td><?php echo $pat_info['name'];?></td>
					<td><?php echo $pat_info['sex'];?></td>
					<td><?php echo $age;?></td>
					<td><?php echo $ot_area['ot_area_name'];?></td>
					<td><?php echo date("d-m-Y",strtotime($data["ot_date"]));?></td>
					<td><?php echo $ot_dept['ot_dept_name'];?></td>
					<td><?php echo date("d-m-Y",strtotime($data["date"]));?></td>
				</tr>
<?php
					$n++;
				}
			}
?>
		</table>
<?php
    }
}

?>
