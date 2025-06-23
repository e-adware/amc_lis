<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

if($_POST["type"]=="gender_record")
{
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	
	// OPD
	$qry=" SELECT `patient_id` FROM `consult_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' ";
	
	$opd_male_pat=0;
	$opd_female_pat=0;
	
	$dis_pat_qry=mysqli_query($link, $qry);
	while($dis_pat=mysqli_fetch_array($dis_pat_qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `sex` FROM `patient_info` WHERE `patient_id`='$dis_pat[patient_id]'"));
		if($pat_info["sex"]=="Male")
		{
			$opd_male_pat++;
		}
		if($pat_info["sex"]=="Female")
		{
			$opd_female_pat++;
		}
	}
	// IPD
	$qry=" SELECT `patient_id` FROM `uhid_and_opdid` WHERE `date` between '$date1' and '$date2' AND `type`='3' ";
	
	$ipd_male_pat=0;
	$ipd_female_pat=0;
	
	$dis_pat_qry=mysqli_query($link, $qry);
	while($dis_pat=mysqli_fetch_array($dis_pat_qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `sex` FROM `patient_info` WHERE `patient_id`='$dis_pat[patient_id]'"));
		if($pat_info["sex"]=="Male")
		{
			$ipd_male_pat++;
		}
		if($pat_info["sex"]=="Female")
		{
			$ipd_female_pat++;
		}
	}
	// Laboratory
	$qry="SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='2' AND b.`date` BETWEEN '$date1' AND '$date2' ";
	
	$lab_male_pat=0;
	$lab_female_pat=0;
	
	$dis_pat_qry=mysqli_query($link, $qry);
	while($dis_pat=mysqli_fetch_array($dis_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$dis_pat[patient_id]' AND `opd_id`='$dis_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='1') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `sex` FROM `patient_info` WHERE `patient_id`='$dis_pat[patient_id]'"));
			if($pat_info["sex"]=="Male")
			{
				$lab_male_pat++;
			}
			if($pat_info["sex"]=="Female")
			{
				$lab_female_pat++;
			}
		}
	}
	// Radiology
	$qry="SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='10' AND b.`date` BETWEEN '$date1' AND '$date2' ";
	
	$radio_male_pat=0;
	$radio_female_pat=0;
	
	$dis_pat_qry=mysqli_query($link, $qry);
	while($dis_pat=mysqli_fetch_array($dis_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$dis_pat[patient_id]' AND `opd_id`='$dis_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`!='1') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `sex` FROM `patient_info` WHERE `patient_id`='$dis_pat[patient_id]'"));
			if($pat_info["sex"]=="Male")
			{
				$radio_male_pat++;
			}
			if($pat_info["sex"]=="Female")
			{
				$radio_female_pat++;
			}
		}
	}
	
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th colspan="2">OPD Patient</th>
			<th colspan="2">IPD Patient</th>
			<th colspan="2">Laboratory Patient</th>
			<th colspan="2">Radiology Patient</th>
		</tr>
		<tr>
			<th>Male</th>
			<th>Female</th>
			<th>Male</th>
			<th>Female</th>
			<th>Male</th>
			<th>Female</th>
			<th>Male</th>
			<th>Female</th>
		</tr>
		<tr>
			<td><?php echo $opd_male_pat; ?></td>
			<td><?php echo $opd_female_pat; ?></td>
			<td><?php echo $ipd_male_pat; ?></td>
			<td><?php echo $ipd_female_pat; ?></td>
			<td><?php echo $lab_male_pat; ?></td>
			<td><?php echo $lab_female_pat; ?></td>
			<td><?php echo $radio_male_pat; ?></td>
			<td><?php echo $radio_female_pat; ?></td>
		</tr>
	</table>
<?php
}
?>
