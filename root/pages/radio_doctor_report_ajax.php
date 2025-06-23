<?php
include("../../includes/connection.php");
$type=$_POST['type'];

function convert_date($date)
{
	 if($date)
	 {
		 $timestamp = strtotime($date); 
		 $new_date = date('d-M-Y', $timestamp);
		 return $new_date;
	 }
}

if($type=="report")
{
	$date=$_POST[date];
	$doc=$_POST[doc];
	
	
	?>
	<hr/>
	<div>
		<?php echo "<h4>".convert_date($date)."</h4>";?>
		<div class="row">
		
		<?php
		$next_div="span10";
		$x_num=mysqli_num_rows(mysqli_query($link,"select * from testresults_rad where date='$date' and doc='$doc' and testid in(select testid from testmaster where type_id='40') order by slno desc"));
		if($x_num>0)
		{
		?>
		<div class="span4">
		<h4>X-Ray (<?php echo $x_num;?>)</h4>
			<table class="table table-bordered table-condensed">
				<tr><th>Patient Name</th><th>Test Name</th></tr>
				<?php
				$x_ray=mysqli_query($link,"select * from testresults_rad where date='$date' and doc='$doc' and testid in(select testid from testmaster where type_id='40') order by slno desc");
				while($x_det=mysqli_fetch_array($x_ray))
				{
					$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$x_det[patient_id]'"));
					echo "<tr><td class='name'>$info[name]</td><td>$x_det[testname]</td></tr>";
				}
				?>
			</table>
		</div>
		<?php
		$next_div="span4";
		}
		?>
	
		<div class="<?php echo $next_div;?>">
		<?php
		$usg_num=mysqli_num_rows(mysqli_query($link,"select * from testresults_rad where date='$date' and doc='$doc' and testid in(select testid from testmaster where type_id='128') order by slno desc"));
		?>
		<h4>USG (<?php echo $usg_num;?>)</h4>
		<table class="table table-bordered table-condensed table-report">
				<tr><th>ID</th><th>Entry Date/Time</th><th>Patient Name</th><th>Age/Sex</th><th>Test Name</th><th>Rate</th></tr>
				<?php
				$usg=mysqli_query($link,"select * from testresults_rad where date='$date' and doc='$doc' and testid in(select testid from testmaster where type_id='128') order by slno desc");
				while($usg_det=mysqli_fetch_array($usg))
				{
					if($usg_det[opd_id]!="")
					{
						$id="OPD ID: ".$usg_det[opd_id];
					}
					else
					{
						$id="IPD ID: ".$usg_det[ipd_id];
					}
					
					$reg_date=mysqli_fetch_array(mysqli_query($link,"select date,time from uhid_and_opdid where patient_id='$usg_det[patient_id]' and (opd_id='$usg_det[ipd_id]' or opd_id='$usg_det[ipd_id]')"));
					
					$test_rate=mysqli_fetch_array(mysqli_query($link,"select test_rate from patient_test_details where patient_id='$usg_det[patient_id]' and ipd_id='$usg_det[ipd_id]' and opd_id='$usg_det[opd_id]' and batch_no='$usg_det[batch_no]' and testid='$usg_det[testid]'"));
					$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$usg_det[patient_id]'"));
					echo "<tr><td>$id</td><td>".convert_date($reg_date)."</td><td class='name'>$info[name]</td><td>$info[age] $info[age_type] / $info[sex]</td><td>$usg_det[testname]</td><td>$test_rate[test_rate]</td></tr>";
				}
				?>
			</table>
		</div>
		
<!--
		<div class="span4">
		<?php
		$ct_num=mysqli_num_rows(mysqli_query($link,"select * from testresults_rad where date='$date' and doc='$doc' and testid in(select testid from testmaster where type_id='139') order by slno desc"));
		?>
		<h4>CT (<?php echo $ct_num;?>)</h4>
		<table class="table table-bordered table-condensed">
				<tr><th>Patient Name</th><th>Test Name</th></tr>
				<?php
				$ct=mysqli_query($link,"select * from testresults_rad where date='$date' and doc='$doc' and testid in(select testid from testmaster where type_id='139') order by slno desc");
				while($ct_det=mysqli_fetch_array($ct))
				{
					$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$ct_det[patient_id]'"));
					echo "<tr><td class='name'>$info[name]</td><td>$ct_det[testname]</td></tr>";
				}
				?>
			</table>
		
		</div>
-->
	
	</div>
	</div>
	<?php
	
}


?>

