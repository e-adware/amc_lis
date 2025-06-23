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

if($_POST["type"]=="search_patient_list_ipd")
{
	$ward=$_POST['ward'];
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	$list_start=$_POST["list_start"];
	
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
			$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid'";
			
			$zz=1;
		}
	}
	if($ipd)
	{
		if(strlen($ipd)>2)
		{
			$q=" SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `ipd_id`='$ipd'";
			
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
	
	$q.=" and ipd_id IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
	
	$q.=" ORDER BY `slno` ASC limit ".$list_start;
	
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
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Unit No.</th>
					<th>Bill No.</th>
					<th>Name</th>
					<th>Sex</th>
					<!--<th>Age (DOB)</th>-->
					<th>Ward</th>
					<th>Bed No</th>
					<th>Doctor</th>
					<th>Admission Date</th>
					<!--<th>User</th>-->
				</tr>
			</thead>
		<?php
			$n=1;
			while($data=mysqli_fetch_array($qq))
			{
				$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$data[ipd_id]'"));
				
				$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]'"));
				if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".convert_date_g($pat_info["dob"]).")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
				
				$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
		
				$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$dt_tm[user]' "));
				
				$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$pat_reg[patient_id]' and ipd_id='$pat_reg[opd_id]'"));
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
					$bed_alloc_qry=mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$pat_reg[patient_id]' and ipd_id='$pat_reg[opd_id]' and alloc_type=1 order by slno asc");
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
				$at_doc=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$pat_reg[patient_id]' and `ipd_id`='$pat_reg[opd_id]' ) "));
				
				$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' AND `type`='2' "));
				if($cancel_request)
				{
					$td_function="";
					
					$td_style="style='background-color: #ff000021'";
					
					$emp_info_del=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$cancel_request[user]' "));
					
					$tr_title="title='Cancel request by $emp_info_del[name]' id='cancel_request_tr'";
				}
				else
				{
					$td_function="onclick=\"redirect_page('$pat_reg[patient_id]','$pat_reg[opd_id]')\"";
					$td_style="style='cursor:pointer;'";
					$tr_title="";
				}
				
		?>
				<!--<tr onclick="redirect_page('<?php echo $pat_reg['patient_id'];?>','<?php echo $pat_reg['opd_id'];?>')" style="cursor:pointer;">-->
				<tr <?php echo $td_style." ".$tr_title." ".$td_function; ?> >
					<td><?php echo $n;?></td>
					<td><?php echo $pat_info['patient_id'];?></td>
					<td><?php echo $pat_reg['opd_id'];?></td>
					<td><?php echo $pat_info['name'];?></td>
					<td><?php echo $pat_info['sex'];?></td>
					<!--<td><?php echo $age;?></td>-->
					<td><?php echo $ward;?></td>
					<td><?php echo $bed;?></td>
					<td><?php echo $at_doc['Name'];?></td>
					<td><?php echo date("d-m-Y",strtotime($pat_reg["date"]));?> <?php echo date("h:i A",strtotime($pat_reg["time"]));?></td>
					<!--<td><?php echo $emp_info['name'];?></td>-->
				</tr>
			<?php
				$n++;
			}
		?>
		</table>
		<?php
	}
}

?>
