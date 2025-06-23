<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date('H:i:s');

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-Y', $timestamp);
		return $new_date;
	}
}

// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}
//------------------------------------------------------------------------------------------------//

function getDatesFromRange($start, $end, $format = 'Y-m-d')
{
	$array = array();
	$interval = new DateInterval('P1D');

	$realEnd = new DateTime($end);
	$realEnd->add($interval);

	$period = new DatePeriod(new DateTime($start), $interval, $realEnd);

	foreach($period as $date) { 
		$array[] = $date->format($format); 
	}

	return $array;
}


$type=$_POST['type'];

if($_POST["type"]==1)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	$vfetal1=$vfetalrate=$vneonatalrate=$vmaternalrate=$vcaesareanrate=$vlenghtstay1=$vlenghtstay=$vaveragelengthstayrate=$vhospitaldeathrate=$vaveragedailycensus=$bedavailble=$vpercentoccupancy=0;
	
	$qcensus=mysqli_fetch_array(mysqli_query($link,"SELECT count(`opd_id`) as maxcensus FROM `uhid_and_opdid` WHERE `date` between '$fdate' and '$tdate' "));
	$qipdcensus=mysqli_fetch_array(mysqli_query($link,"SELECT count(`opd_id`) as maxipdcensus FROM `uhid_and_opdid` WHERE `date` between '$fdate' and '$tdate' and type='3'  "));
	///////Date difference in php
	 $vendat=strtotime($fdate);
	 $vpaydat=strtotime($tdate);	
	 $vda=$vpaydat-$vendat;
	 $vttldays=floor($vda/3600/24); 
	 $vttldays=$vttldays+1;
	//////end///////////////
	
	$vaveragedailycensus=$qcensus['maxcensus']/$vttldays;
	
	
	
	$qlengthstay=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.hour),0) as maxhour,ifnull(sum(a.minute),0) as maxminutes FROM `ipd_pat_staying_time` a,ipd_pat_discharge_details b WHERE b.`date` between '$fdate' and '$tdate' and a.patient_id=b.patient_id and a.ipd_id=b.ipd_id "));
	
	$vmnt_to_hour=$qlengthstay['maxminutes']/60; // 60 minutes for an hour
	$vlenghtstay1=$qlengthstay['maxhour']+$vmnt_to_hour;
	$vlenghtstay=round($vlenghtstay1/24); ///24 hours a day
	$qttldischarge=mysqli_fetch_array(mysqli_query($link,"SELECT count(`ipd_id`) as maxttldischarge FROM `ipd_pat_discharge_details`  WHERE `date` BETWEEN '$fdate' and '$tdate' "));
	$vaveragelengthstayrate=$vlenghtstay/$qttldischarge['maxttldischarge'];
	
	
	$qfetal=mysqli_fetch_array(mysqli_query($link,"SELECT count(`ipd_id`) as maxfetal FROM `ipd_pat_death_details` WHERE `death_date` between '$fdate' and '$tdate' and `death_cause`='1'"));
	$qlivebirth=mysqli_fetch_array(mysqli_query($link,"SELECT count(`ipd_id`) as maxlivebirth FROM `ipd_pat_delivery_det` WHERE `date` between '$fdate' and '$tdate'"));
		
	$vfetal1=$qlivebirth['maxlivebirth']+$qfetal['maxfetal'];
	$vfetalrate=($qfetal['maxfetal']/$vfetal1)*100;
	
	
	$qneonatal=mysqli_fetch_array(mysqli_query($link,"SELECT count(`ipd_id`) as maxneonatal FROM `ipd_pat_death_details` WHERE `death_date` between '$fdate' and '$tdate' and `death_cause`='2'"));
	$qdischarged=mysqli_fetch_array(mysqli_query($link,"SELECT count(a.`ipd_id`) as maxdischarge FROM `ipd_pat_discharge_details` a,ipd_pat_delivery_det b WHERE a.`date` between '$fdate' and '$tdate' and a.patient_id=b.patient_id and a.ipd_id=b.ipd_id "));
	$vneonatalrate=($qneonatal['maxneonatal']/$qdischarged['maxdischarge'])*100;
	
		
	$qmaternal=mysqli_fetch_array(mysqli_query($link,"SELECT count(`ipd_id`) as maxmaternal FROM `ipd_pat_death_details` WHERE `death_date` between '$fdate' and '$tdate' and `death_cause`='3'"));
	$qobsdischarge=mysqli_fetch_array(mysqli_query($link,"SELECT count(a.`ipd_id`) as maxobsdischarge FROM `ipd_pat_discharge_details` a,ipd_bed_alloc_details b WHERE a.`date` BETWEEN '$fdate' and '$tdate' and a.`patient_id`=b.`patient_id` and a.`ipd_id`=b.`ipd_id` and b.ward_id='2' and b.alloc_type='0'"));
	
	$vmaternalrate=($qmaternal['maxmaternal']/$qobsdischarge['maxobsdischarge'])*100;
	
	
	
	$qcaesarean=mysqli_fetch_array(mysqli_query($link,"SELECT count(`ipd_id`) as maxcaesarean FROM `ipd_pat_delivery_det` WHERE `date` between '$fdate' and '$tdate' and `delivery_mode`='LSCS'"));
	$vcaesareanrate=($qcaesarean['maxcaesarean']/$qlivebirth['maxlivebirth'])*100;
		
		
	$qttldeath=mysqli_fetch_array(mysqli_query($link,"SELECT count(`ipd_id`) as maxdeath FROM `ipd_pat_death_details` WHERE `death_date` between '$fdate' and '$tdate' "));
	
	$vhospitaldeathrate=($qttldeath['maxdeath']/$qttldischarge['maxttldischarge'])*100;
	
	
		
	$inpatient_number=1;
	$dates = getDatesFromRange($fdate,$tdate);
	foreach($dates as $date)
	{
		if($date)
		{
			$ipd_admit_pat_qry=mysqli_query($link, " SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date') ");
			while($ipd_admit_pat=mysqli_fetch_array($ipd_admit_pat_qry))
			{
				   $inpatient_str.=$ipd_admit_pat["ipd_id"]."@";
				   $inpatient_number++."</br>";
				
			}
		}
	}
	
	 $qno_of_bed=mysqli_fetch_array(mysqli_query($link,"select count(bed_id) as maxbed from bed_master"));
	//$vpercentoccupancy=($qipdcensus['maxipdcensus']/$inpatient_number)*100;
	$bedavailble=$qno_of_bed['maxbed']*$vttldays;
	$vpercentoccupancy=($inpatient_number/$bedavailble)*100;
	?>
	<div style="text-align:right;">
		<button type="button" class="btn btn-primary" id="act_btn" onclick="print_report('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	</div>
	<table class="table table-condensed table-report">
		<tr>
			<td>#</td>
			<td>Description</td>
			<td>Status</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>1.</td>
			<td>Average Daily Census</td>
			<td><?php echo number_format($vaveragedailycensus,2);?> </td>
			<td>(<?php echo $qcensus['maxcensus'].' / '.$vttldays;?>)</td>
		</tr>
		<tr>
			<td>2.</td>
			<td>Average Length of Stay</td>
			<td><?php echo number_format($vaveragelengthstayrate,2);?> Days</td>
			<td>(<?php echo $vlenghtstay.' / '.$qttldischarge['maxttldischarge'];?>)</td>
		</tr>
		
		<tr>
			<td>3.</td>
			<td>Percentage of Occupancy</td>
			<td><?php echo number_format($vpercentoccupancy,2);?> %</td>
			<td>(<?php echo $inpatient_number.' / '.$bedavailble.' X 100 ';?>)</td>
		</tr>
		
		<tr>
			<td>4.</td>
			<td>Hospital Death Rate (Gross)</td>
			<td><?php echo number_format($vhospitaldeathrate,2);?> %</td>
			<td>(<?php echo $qttldeath['maxdeath'].' / '.$qttldischarge['maxttldischarge'].' X 100 ';?>)</td>
		</tr>
		
		<tr>
			<td>5.</td>
			<td>Fetal Death Rate</td>
			<td><?php echo number_format($vfetalrate,2);?> %</td>
			<td>(<?php echo $qfetal['maxfetal'].' / '.$vfetal1.' X 100 ';?>)</td>
		</tr>
		
		<tr>
			<td>6.</td>
			<td>Neonatal Mortality Rate(Death Rate)</td>
			<td><?php echo number_format($vneonatalrate,2);?> %</td>
			<td>(<?php echo $qneonatal['maxneonatal'].' / '.$qdischarged['maxdischarge'].' X 100 ';?>)</td>
		</tr>
		
		<tr>
			<td>7.</td>
			<td>Maternal Mortality Rate(Death Rate)</td>
			<td><?php echo number_format($vmaternalrate,2);?> %</td>
			<td>(<?php echo $qmaternal['maxmaternal'].' / '.$qobsdischarge['maxobsdischarge'].' X 100 ';?>)</td>
		</tr>
		
		<tr>
			<td>8.</td>
			<td>Caesarean-Section Rate</td>
			<td><?php echo number_format($vcaesareanrate,2);?> %</td>
			<td>(<?php echo $qcaesarean['maxcaesarean'].' / '.$qlivebirth['maxlivebirth'].' X 100 ';?>)</td>
		</tr>
		
	</table>
	<?php
}

