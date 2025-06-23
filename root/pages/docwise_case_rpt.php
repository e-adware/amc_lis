<?php
include('../../includes/connection.php');

	// Date format convert
	function convert_date($date)
	{
		if($date)
		{
			$timestamp = strtotime($date); 
			$new_date = date('d M Y', $timestamp);
			return $new_date;
		}
	}
	$rupees_symbol="&#x20b9; ";
	
	$date1=$_GET['fdate'];
	$date2=$_GET['tdate'];
	$refbydoctorid=$_GET['doc'];
	
	
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	$type="";
	if($encounter>0)
	{
		$type=" and `type`='$encounter'";
	}
	
?>
<html>
<head>
	<title>Payment  Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<style>
		*{font-size:12px}
	</style>
</head>

<body>
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Details Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-bordered table-condensed table-report" id="all_pat">
			<tr>
				<th>#</th>
				<th>Details</th>
				<th>No.of Test</th>
				<th>Amount</th>

			</tr>
			<?php
					 $qlabamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.test_rate),0) as maxlab from patient_test_details a,uhid_and_opdid b,testmaster c where a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.`date` between '$date1' AND '$date2' and a.testid=c.testid  and c.category_id='1' and c.type_id !='132' and b.refbydoctorid='$refbydoctorid' "));
					 
			?>
					<tr>
			 <td>1</td>
			 <td>Lab</td>
			 <td>0</td>
			 <td><?php echo $qlabamt['maxlab'];?></td>
		</tr>
		<?php
		$qtest=mysqli_query($link,"select distinct(a.testid),c.testname from patient_test_details a,uhid_and_opdid b,testmaster c where a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.`date` between '$date1' AND '$date2' and a.testid=c.testid  and c.category_id>1 and c.type_id !='132' and b.refbydoctorid='$refbydoctorid' order by a.testid ");
		while($qtest1=mysqli_fetch_array($qtest))
		{
			$qnumtest=mysqli_num_rows(mysqli_query($link,"select a.testid from patient_test_details a,uhid_and_opdid b,testmaster c where a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.`date` between '$date1' AND '$date2' and a.testid=c.testid  and c.category_id>1 and c.type_id !='132' and b.refbydoctorid='$refbydoctorid' and a.testid='$qtest1[testid]' order by a.testid "));
			$qrate=mysqli_fetch_array(mysqli_query($link,"select a.test_rate from patient_test_details a,uhid_and_opdid b,testmaster c where a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.`date` between '$date1' AND '$date2' and a.testid=c.testid   and b.refbydoctorid='$refbydoctorid' and a.testid='$qtest1[testid]' "));
			
			$vamt=$qnumtest*$qrate['test_rate'];
			$vttl=$vttl+$vamt;
		?>
		<tr>
			 <td>1</td>
			 <td><?php echo $qtest1['testname'];?></td>
			 <td><?php echo $qnumtest;?></td>
			 <td><?php echo $vamt;?></td>
		</tr>
		<?php
	}?>
	
	  <tr>
			 <td>&nbsp;</td>
			 <td>&nbsp;</td>
			 <td style="font-weight:bold">Total</td>
			 <td style="font-weight:bold"><?php echo number_format($vttl+$qlabamt['maxlab'],2);?></td>
		</tr>
			
		</table>
	</div>
</body>
</html>
<script>window.print();</script>
