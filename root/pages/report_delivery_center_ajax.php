<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$type=$_POST['type'];

if($type==1)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$name=mysqli_real_escape_string($link,$_POST['name']);
	$catg=$_POST['catg'];
	$pid=$_POST['pat_id'];
	
		
	if($name!='')
	{
		$qry="select a.patient_id,a.opd_id as bill_id,b.opd_id,b.ipd_id,b.batch_no,b.date,b.time from uhid_and_opdid a,patient_test_details b,patient_info c where a.patient_id=b.patient_id and b.patient_id=c.patient_id and c.name like '%$name%'";
	}
	else if($pid!='')
	{
		if($catg=="pin")
		{
			$qry="select distinct a.patient_id,a.opd_id as bill_id,b.opd_id,b.ipd_id,b.batch_no,b.date,b.time from uhid_and_opdid a,patient_test_details b where a.patient_id=b.patient_id and (a.opd_id=b.opd_id or a.opd_id=b.ipd_id) and b.opd_id like '$pid%'";	
		}
		else
		{
			$qry="select distinct a.patient_id,a.opd_id as bill_id,b.opd_id,b.ipd_id,b.batch_no,b.date,b.time from uhid_and_opdid a,patient_test_details b where a.patient_id=b.patient_id and (a.opd_id=b.opd_id or a.opd_id=b.ipd_id) and b.patient_id like '$pid%'";	
		}
	}
	else
	{
		$qry="select distinct a.patient_id,a.opd_id as bill_id,b.opd_id,b.ipd_id,b.batch_no,b.date,b.time from uhid_and_opdid a,patient_test_details b where a.patient_id=b.patient_id and (a.opd_id=b.opd_id or a.opd_id=b.ipd_id) and a.date between '$fdate' and '$tdate'";
	}
?>
	<table class="table table-bordered table-condensed table-report table-white" id="pat">
		<tr>
			<td>#</td>
			<td>Date/Time</td>
			<td>Bill No.</td>
			<td>UHID</td>
			<td>Name</td>
			<td>Phone</td>
			<td>Age</td>
			<td>Sex</td>
			<td>Test</td>
			<td></td>
		</tr>
<?php
		$i=1;
		$qrtest=mysqli_query($GLOBALS["___mysqli_ston"], $qry);
		while($q=mysqli_fetch_array($qrtest))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
			
			$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[bill_id]'"));
			
			$reg_date=$pat_reg["date"];
			
			if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			
			?>
			 <tr>
				<td><?php echo $i;?></td>
				<td><?php echo date("d-M-y",strtotime($q["date"]))." / ".date("h:i A",strtotime($q["time"]));?></td>
				<td>
					<?php echo $q["bill_id"];?>
					<input type="hidden" value="<?php echo $q["patient_id"];?>" id="pid<?php echo $i;?>"/>
					<input type="hidden" value="<?php echo $q["opd_id"];?>" id="opd_id<?php echo $i;?>"/>
					<input type="hidden" value="<?php echo $q["ipd_id"];?>" id="ipd_id<?php echo $i;?>"/>
					<input type="hidden" value="<?php echo $q["batch_no"];?>" id="batch<?php echo $i;?>"/>
					<input type="hidden" value="<?php echo $q["bill_id"];?>" id="bill_id<?php echo $i;?>"/>
				</td>
				<td><?php echo $q["patient_id"];?></td>
				<td><?php echo $pat_info["name"];?></td>
				<td><?php echo $pat_info["phone"];?></td>
				<td><?php echo $age;?></td>
				<td><?php echo $pat_info["sex"];?></td>
				<td>
					<?php
					$path=mysqli_num_rows(mysqli_query($link,"select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where category_id='1')"));
					if($path>0)
					{
						echo "Pathology : ". $path."<br/>";
					} 
					
					$rad=mysqli_num_rows(mysqli_query($link,"select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where category_id='2')"));
					if($rad>0)
					{
						echo "Radiology : ". $rad."<br/>";
					}
					
					$card=mysqli_num_rows(mysqli_query($link,"select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where category_id='3')"));
					if($card>0)
					{
						echo "Cardiology : ". $card;
					}
					?>
				</td>
				<td>
					<button class="btn btn-info" onclick="load_pat_data(<?php echo $i;?>)"><i class="icon-list"></i> View</button>
				</td>
			</tr>
		<?php
			$i++;
		}
?>
	</table>
<?php
}
?>
