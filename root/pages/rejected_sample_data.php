<?php
include("../../includes/connection.php");
require("../../includes/global.function.php");


$date = date("Y-m-d");
$time = date("H:i:s");

$type = $_POST['type'];

if ($type == 1)
{
	//print_r($_POST);
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	
	//$qry="SELECT a.*,b.`date`,b.`time`,b.`testid`,b.`user` FROM `phlebo_sample_status` a, `phlebo_sample` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`=b.`ipd_id` AND a.`batch_no`=b.`batch_no` AND a.`vaccu`=b.`vaccu` AND a.`status_id`>'1' AND b.`date` BETWEEN '$date1' AND '$date2'";
	$qry="SELECT a.*,b.`date`,b.`time`,b.`testid`,b.`user` FROM `phlebo_sample_status` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`=b.`ipd_id` AND a.`batch_no`=b.`batch_no` AND a.`status_id`>'1' AND b.`date` BETWEEN '$date1' AND '$date2'";
	//$qry="SELECT * FROM `phlebo_sample_status` WHERE `status_id`>'1' AND `date` BETWEEN '$date1' AND '$date2'";
	//echo $qry;
	?>
	<div class="print_div">
	<button type="button" class="btn btn-primary btn-mini" onclick="report_print('<?php echo base64_encode($date1);?>','<?php echo base64_encode($date2);?>')"><i class="icon-print icon-large"></i> Print</button>
	<button type="button" class="btn btn-success btn-mini" onclick="report_export('<?php echo base64_encode($date1);?>','<?php echo base64_encode($date2);?>')"><i class="icon-file icon-large"></i> Export</button>
	</div>
	<table class="table table-condensed">
            <thead class="table_header_fix">
                <tr>
                    <th>#</th>
                    <th>Hospital No</th>
                    <th>Patient Name</th>
                    <th>Test Name</th>
                    <th>Sample Status</th>
                    <th>Date Time</th>
                    <th>User</th>
                </tr>
            </thead>
            <?php
            $j=1;
            $q=mysqli_query($link, $qry);
            while($r=mysqli_fetch_array($q))
            {
				$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name`,`hosp_no` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
				$test=mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$r[testid]'"));
				$stat=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `phlebo_sample_status_master` WHERE `status_id`='$r[status_id]'"));
				$emp=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
			?>
			<tr>
				<td><?php echo $j;?></td>
				<td><?php echo $pat_info['hosp_no'];?></td>
				<td><?php echo $pat_info['name'];?></td>
				<td><?php echo $test['testname'];?></td>
				<td><?php echo $stat['name'];?></td>
				<td><?php echo date("d-m-y", strtotime($r['date']))." ".date("h:i A", strtotime($r['time']));?></td>
				<td><?php echo $emp['name'];?></td>
			</tr>
			<?php
			$j++;
			}
            ?>
          </table>
	<?php
}
