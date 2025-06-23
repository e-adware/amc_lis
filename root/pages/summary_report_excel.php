<?php
session_start();
$c_user=trim($_SESSION['emp_id']);

include'../../includes/connection.php';

$date1=$_GET['date1'];
$date2=$_GET['date2'];

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$filename ="summary_reports_".$date1."_to_".$date2.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
<html>
<head>
<title>Service Summary Reports</title>

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
<?php
		
	// Patients
	$patients_array = array();
	$p=1;
	//$pat_ipd_qry=mysqli_query($link, " SELECT `patient_id`, `ipd_id` FROM `ipd_pat_service_details` WHERE `date` BETWEEN '$date1' AND '$date2' GROUP BY `patient_id`,`ipd_id`");
	$pat_ipd_qry=mysqli_query($link, " SELECT `patient_id`, `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date` BETWEEN '$date1' AND '$date2' GROUP BY `patient_id`,`ipd_id`");
	while($pat_ipd=mysqli_fetch_array($pat_ipd_qry))
	{
		$patients_array[$p]=$pat_ipd["patient_id"]."@#@".$pat_ipd["ipd_id"];
		$p++;
	}
	//print_r($patients_array);
	// Services
	$service_ids_array = array();
	$j=1;
	$service_ipd_qry=mysqli_query($link, " SELECT DISTINCT a.`service_id` FROM `ipd_pat_service_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=3 AND a.`group_id`!=141 AND a.`service_id`>0 AND a.`date` BETWEEN '$date1' AND '$date2' ");
	while($service_ipd=mysqli_fetch_array($service_ipd_qry))
	{
		$service_ids_array[$j]=$service_ipd["service_id"]."@1";
		$j++;
	}
	$service_ot_qry=mysqli_query($link, " SELECT DISTINCT `ot_service_id` FROM `ot_pat_service_details` WHERE `ot_service_id`>0 AND `date` BETWEEN '$date1' AND '$date2' ");
	while($service_ot=mysqli_fetch_array($service_ot_qry))
	{
		$service_ids_array[$j]=$service_ot["ot_service_id"]."@2";
		$j++;
	}
	//print_r($service_ids_array);
?>
	<table class="table table-hover table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>IPD ID</th>
				<th>Name</th>
				<th>Room Rent</th>
<?php
				foreach($service_ids_array AS $service_ids)
				{
					if($service_ids)
					{
						$service_ids=explode("@", $service_ids);
						$service_id=$service_ids[0];
						$val=$service_ids[1];
						if($val==1)
						{
							// IPD
							$service_det=mysqli_fetch_array(mysqli_query($link, "SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$service_id'"));
							$service_name=$service_det["charge_name"];
							if(!$service_name)
							{
								$service_det=mysqli_fetch_array(mysqli_query($link, "SELECT `service_text` FROM `ipd_pat_service_details` WHERE `service_id`='$service_id'"));
								$service_name=$service_det["service_text"];
							}
						}
						if($val==2)
						{
							// OT
							$service_det=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `ot_type_master` WHERE `type_id`='$service_id'"));
							$service_name=$service_det["type"];
						}
						
						echo "<th>$service_name</th>";
					}
				}
?>
			</tr>
		</thead>
<?php
	
	$each_serv_tot=array();
	$tot_bed_amount=0;
	$pat=1;
	foreach($patients_array AS $patients)
	{
		if($patients)
		{
			$patients=explode("@#@", $patients);
			$patient_id=$patients[0];
			$ipd_id=$patients[1];
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `patient_info` WHERE `patient_id`='$patient_id'"));
		
			$service_amount_ipd=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `ipd_pat_service_details` WHERE `group_id`='141' AND `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
			$tot_serv_amount=$service_amount_ipd["tot_amount"];
			$tot_bed_amount+=$tot_serv_amount;
			$tot_serv_amount=number_format($tot_serv_amount,2);
	?>
			<tr>
				<td><?php echo $pat; ?></td>
				<td><?php echo $ipd_id; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $tot_serv_amount; ?></td>
	<?php
				$i=1;
				foreach($service_ids_array AS $service_ids)
				{
					if($service_ids)
					{
						$service_ids=explode("@", $service_ids);
						$service_id=$service_ids[0];
						$val=$service_ids[1];
						if($val==1)
						{
							// IPD
							$service_amount_ipd=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `ipd_pat_service_details` WHERE `service_id`='$service_id' AND `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
							
							$tot_serv_amount=$service_amount_ipd["tot_amount"];
						}
						if($val==2)
						{
							// OT
							$service_amount_ot=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `ot_pat_service_details` WHERE `ot_service_id`='$service_id' AND `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
							
							$tot_serv_amount=$service_amount_ot["tot_amount"];
						}
						
						$each_serv_tot[$i]+=$tot_serv_amount;
						
						$tot_serv_amount=number_format($tot_serv_amount,2);
						
						echo "<td>$tot_serv_amount</td>";
						
						$i++;
					}
				}
	?>
			</tr>
	<?php
			$pat++;
		}
	}
	
	//~ $dis_date_qry=mysqli_query($link, " SELECT DISTINCT `date` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2' ");
	//~ while($dis_date=mysqli_fetch_array($dis_date_qry))
	//~ {
		
	//~ }
?>
		<tr style="display:;">
			<th colspan="3"><span class="text-right">Total : </span></th>
			<th><?php echo number_format($tot_bed_amount,2); ?></th>
<?php
		for($m=1;$m<$i;$m++)
		{
?>
			<th><?php echo number_format($each_serv_tot[$m],2); ?></th>
<?php
		}
?>
		</tr>
	</table>
	</div>
</body>
</html>
