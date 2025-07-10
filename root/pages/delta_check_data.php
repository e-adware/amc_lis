<?php
session_start();

include ("../../includes/connection.php");

$date = date("Y-m-d");
$time = date("H:i:s");

if($_POST["type"] == "load_delta_check")
{
	$uhid = $_POST["uhid"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$param_id = $_POST["paramid"];
	
	$pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT `hosp_no` FROM `patient_info` WHERE `patient_id`='$uhid'"));
	
	// DLC
	$dlc_patient_ids=[];
	$dlc_patient_ids_qry=mysqli_query($link, "SELECT `patient_id` FROM `patient_info` WHERE `hosp_no`='$pat_info[hosp_no]'");
	while($dlc_patient_ids_val=mysqli_fetch_assoc($dlc_patient_ids_qry))
	{
		$dlc_patient_ids[]=$dlc_patient_ids_val["patient_id"];
	}
	$dlc_patient_ids=array_unique($dlc_patient_ids);
	$dlc_patient_ids=implode(",",$dlc_patient_ids);
	
	$param_info=mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `Parameter_old` WHERE `ID`='$param_id'"));
	
	$str="SELECT * FROM `testresults` WHERE `patient_id` IN($dlc_patient_ids) AND `paramid`='$param_id' ORDER BY `date`,`time` ASC";
	
	$qry=mysqli_query($link, $str);
?>
	<div style="font-size: 14px;font-weight: bold;">
		Test Name : <?php echo $param_info["Name"]; ?>
	</div>
	<table class="table table-condensed table-bordered table-hover">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Hospital No</th>
				<th>Sample No.</th>
			<?php
				if($client_type==1)
				{
					echo "<th>Batch No.</th>";
				}
			?>
				<th>Date Time</th>
				<th>Result</th>
			</tr>
		</thead>
<?php
	$n=1;
	while($test_result=mysqli_fetch_array($qry))
	{
		$pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$test_result[patient_id]' AND (`opd_id`='$test_result[opd_id]' OR `opd_id`='$test_result[ipd_id]') "));
		
		$tr_color="color:black;";
		if($opd_id==$test_result["opd_id"] && $ipd_id==$test_result["ipd_id"] && $batch_no==$test_result["batch_no"])
		{
			$tr_color="color:black;font-weight:bold;";
		}
?>
		<tr style="<?php echo $tr_color; ?>">
			<td><?php echo $n; ?></td>
			<td><?php echo $pat_info["hosp_no"]; ?></td>
			<td><?php echo $pat_reg["type_prefix"].$pat_reg["sample_serial"]; ?></td>
	<?php
		if($client_type==1)
		{
			echo "<td>".$test_result["batch_no"]."</td>";
		}
	?>
			<td><?php echo date("d-m-Y",strtotime($test_result["date"])) ?> <?php echo date("h:i A",strtotime($test_result["time"])) ?></td>
			<td><?php echo nl2br($test_result["result"]); ?></td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
	<center>
		<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
	</center>
<?php
}

mysqli_close($link);
?>
