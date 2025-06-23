<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");

if($reporting_without_sample_receive==1)
{
	$table_name="phlebo_sample";
	//$table_name="lab_sample_receive";
	$sample_id="sampleid";
}
else
{
	$table_name="patient_test_details";
	$sample_id="sample_id";
}

$type=mysqli_real_escape_string($link, $_POST['type']);

$zz=0;

$str="SELECT DISTINCT a.`patient_id`,a.`opd_id`,a.`ipd_id` FROM `$table_name` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`category_id`=1";

if($type==1)
{
	$fdate=$_POST['fdate'];	
	$tdate=$_POST['tdate'];	
	$pat_type=$_POST['pat_type'];
	$dept_id=$_POST['dept_id'];
	
	$str.=" AND a.`date` BETWEEN '$fdate' AND '$tdate'";
	
	if($pat_type=="opd_id")
	{
		$str.=" AND a.`ipd_id`=''";
	}
	if($pat_type=="ipd_id")
	{
		$str.=" AND a.`opd_id`=''";
	}
	
	if($dept_id>0)
	{
		$str.=" AND b.`type_id`='$dept_id'";
	}
}
else
{
	$search_val=mysqli_real_escape_string($link, $_POST['search_val']);
	$search_type=$_POST['search_type'];
	$pat_type=$_POST['pat_type'];
	
	if($search_val)
	{
		if($search_type=="bill_no")
		{
			if($pat_type=="opd_id" && strlen($search_val)>2)
			{
				$str.=" AND a.`opd_id` LIKE '$search_val%' AND a.`ipd_id`=''";
				
				$zz++;
			}
			if($pat_type=="ipd_id" && strlen($search_val)>2)
			{
				$str.=" AND a.`ipd_id` LIKE '$search_val%' AND a.`opd_id`=''";
				
				$zz++;
			}
		}
		if($search_type=="uhid")
		{
			if($pat_type=="opd_id" && strlen($search_val)>2)
			{
				$str.=" AND a.`patient_id` LIKE '$search_val%' AND a.`ipd_id`=''";
				
				$zz++;
			}
			if($pat_type=="ipd_id" && strlen($search_val)>2)
			{
				$str.=" AND a.`patient_id` LIKE '$search_val%' AND a.`opd_id`=''";
				
				$zz++;
			}
		}
		if($search_type=="name")
		{
			if($pat_type=="opd_id" && strlen($search_val)>2)
			{
				$str.=" AND a.`ipd_id`=''";
				
				$zz++;
			}
			if($pat_type=="ipd_id" && strlen($search_val)>2)
			{
				$str.=" AND a.`opd_id`=''";
				
				$zz++;
			}
			$str.=" AND a.`patient_id` IN(SELECT `patient_id` FROM `patient_info` WHERE `name` LIKE '%$search_val%')";
		}
	}
}

//echo $str;

$qry=mysqli_query($link, $str);
?>
	<table class="table table-bordered table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No.</th>
				<th>Name</th>
				<th>Batch No.</th>
				<th>Time</th>
				<th>Collection Center</th>
				<th>User</th>
			</tr>
		</thead>
	<?php
		$i=1;
		while($data=mysqli_fetch_array($qry))
		{
			$patient_id=$data["patient_id"];
			$opd_id=$data["opd_id"];
			$ipd_id=$data["ipd_id"];
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));
			
			$cls="";
			if($data["opd_id"]!=="")
			{
				$pin_str="OPD ID";
				$bill_no=$data["opd_id"];
			}
			if($data["ipd_id"])
			{
				$pin_str="IPD ID";
				$bill_no=$data["ipd_id"];
			}
			
			$pin_str="Bill No.";
			
			// For different batch No
			$batch_str="SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `sample_id`!=0 ORDER BY `slno` DESC";
			$batch_qry=mysqli_query($link, $batch_str);
			$batch_num=mysqli_num_rows($batch_qry);
			while($batch_val=mysqli_fetch_array($batch_qry))
			{
				$batch_no=$batch_val["batch_no"];
				$test_date=$batch_val["date"];
				
				$test_user_time=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `date`,`time`,`user` FROM `patient_test_details` WHERE `patient_id`='$pat_info[patient_id]' and `opd_id`='$data[opd_id]' and `ipd_id`='$data[ipd_id]' and `batch_no`='$batch_val[batch_no]' "));
				$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$test_user_time[user]' "));
				
				//------Status------//
				$tot_path=mysqli_fetch_array(mysqli_query($link,"select count(distinct a.testid) as tot from patient_test_details a,testmaster b where a.patient_id='$pat_info[patient_id]' and a.opd_id='$data[opd_id]' and a.ipd_id='$data[ipd_id]' and a.batch_no='$batch_val[batch_no]' and a.testid=b.testid and b.category_id='1' and b.`type_id` NOT IN($non_reporting_test_dept_id)"));
				
				$tot_path_res=mysqli_fetch_array(mysqli_query($link,"select count(distinct testid) as tot from testresults where patient_id='$pat_info[patient_id]' and opd_id='$data[opd_id]' and ipd_id='$data[ipd_id]' and batch_no='$batch_val[batch_no]'"));
				
				$tot_path_sum=mysqli_fetch_array(mysqli_query($link,"select count(distinct testid) as tot from patient_test_summary where patient_id='$pat_info[patient_id]' and opd_id='$data[opd_id]' and ipd_id='$data[ipd_id]' and batch_no='$batch_val[batch_no]' and testid not in(select testid from testresults where patient_id='$pat_info[patient_id]' and opd_id='$data[opd_id]' and ipd_id='$data[ipd_id]' and batch_no='$batch_val[batch_no]')"));
				
				$tot_path_wid=mysqli_fetch_array(mysqli_query($link,"select count(distinct testid) as tot from widalresult where patient_id='$pat_info[patient_id]' and opd_id='$data[opd_id]' and ipd_id='$data[ipd_id]' and batch_no='$batch_val[batch_no]'"));
				
				$tot_path_reported=$tot_path_res["tot"]+$tot_path_sum["tot"]+$tot_path_wid["tot"];
				
				$style_span="background-color: #d59a9a;"; //--RED--//
				if($tot_path_reported==$tot_path["tot"])
				{
					$style_span="background-color:#9dcf8a;"; //--Green--//
					
					$tot_path_print=mysqli_fetch_array(mysqli_query($link,"select count(distinct testid) as tot from testreport_print where patient_id='$pat_info[patient_id]' and opd_id='$data[opd_id]' and ipd_id='$data[ipd_id]' and batch_no='$batch_val[batch_no]'"));
					if($tot_path_print["tot"]>=$tot_path["tot"])
					{
						$style_span="background-color:#89898D;"; //--Grey--//
					}
				}
				if($tot_path["tot"]>$tot_path_reported && $tot_path_reported>0)
				{
					$style_span="background-color:yellow;"; //----Yellow--//
				}
				//----------------- //
				$img="";
				$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT a.`centrename`, b.`urgent` FROM `centremaster` a, `uhid_and_opdid` b WHERE a.`centreno`=b.`center_no` AND b.`patient_id`='$pat_info[patient_id]' AND b.`opd_id`='$data[opd_id]' "));
				if($pat_reg["urgent"]==1)
				{
					$cls=" urgent";
					$img="<img src='../images/blinking_dot.gif' style='width:10px;' />";
				}
		?>
			<tr class="<?php echo $cls; ?>" id="path_tr<?php echo $i;?>" onClick="load_test_detail('<?php echo $data['patient_id'];?>','<?php echo $data['opd_id'];?>','<?php echo $data['ipd_id'];?>','<?php echo $batch_val["batch_no"];?>')" style="cursor:pointer;">
				<td><span class="btn_round" style="<?php echo $style_span;?>"><?php echo $i;?></span></td>
				<td><?php echo $pat_info['patient_id'];?></td>
				<td><?php echo $bill_no;?></td>
				<td><?php echo $pat_info['name'];?><span style="float:right;"><?php echo $img;?></span></td>
				<td><?php echo $batch_val['batch_no'];?></td>
				<td><?php echo date("d-m-Y",strtotime($test_user_time['date']))." ".date("h:i A",strtotime($test_user_time['time']));?></td>
				<td><?php echo $pat_reg['centrename'];?></td>
				<td><?php echo $user_info["name"];?>
				<div id="path_pat<?php echo $i;?>" style="display:none">
					<?php echo "@".$data['patient_id']."@".$data['opd_id']."@".$data['ipd_id']."@".$batch_val['batch_no'];?>
				</div>
				</td>
			</tr>
		<?php	
				$i++;
			}
		}
	
	
	
	?>
	</table>
	
	



