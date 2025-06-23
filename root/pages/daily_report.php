<!DOCTYPE html>
<html>
	<head>
		
		<title>Details Report</title>
		<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
		<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
		<script>
			function goTo(param) {
				document.srs.action=param.href;
				document.srs.submit();
			}
		</script>
	</head>
	<body>
		<?php
			include'../../includes/connection.php';
			include'../../includes/function.inc.php';
			$date1 = $_GET['date1'];
			$date2 = $_GET['date2'];
			
			function convert_date($date)
			{
			$timestamp = strtotime($date); 
			$new_date = date('d M Y', $timestamp);
			return $new_date;
			}
			
			?>
		<div class="container-fluid">
			<div class="text-center">
				<h3>Details Report</h3>
				<p>From: <?php echo convert_date($date1);?> To: <?php echo convert_date($date2);?></p>
				<div class="no_print bottom-margin"><input class="btn btn-success" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" /> <input class="btn btn-warning" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div>
			</div>
			<table class="table table-bordered table-condensed">
				<tr>
					<th>Date </th>
					<th>UHID / Lab ID</th>
					<th>Patient Name</th>
					<th>Doctor Name</th>
					<th>Center Name</th>
					<th>Test Rate</th>
					<th>Amount</th>
					<th>Discount</th>
					<th>Paid</th>
					<th>Balance</th>
					<th>User</th>
				</tr>
			<?php
				$tamt=0;
				$dscnt=0;
				$advnc=0;
				$blnc=0;
				$i=1;
				$qrtest=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' and'$date2' and `type`=2 order by `date` ");
				while($qrtest1=mysqli_fetch_array($qrtest))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$qrtest1[patient_id]'"));
					$ref_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
					$center_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$pat_info[center_no]' "));
					$pat_pay_detail=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$qrtest1[patient_id]' and `opd_id`='$qrtest1[opd_id]'  "));
					
					if ($pat_pay_detail['dis_amt']>0)
					{
						$vdscnt=$pat_pay_detail['dis_amt'];
					}
					else
					{
						$vdscnt='';
					}
					
					$tamt=$tamt+$pat_pay_detail['tot_amount'];
					$dscnt=$dscnt+$vdscnt;
					$advnc=$advnc+$pat_pay_detail['advance'];
					$blnc=$blnc+$pat_pay_detail['balance'];
					
					$quser=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `name` FROM `employee` WHERE `emp_id`='$qrtest1[user]' "));
					
					if($dummyDate!=$qrtest1['date'])
					{
						echo '<tr><td align="left" colspan="11"><b>'.convert_date($qrtest1['date']).'</b></td></tr>';
						$dummyDate=$qrtest1['date'];
						
					}
				?>
					<tr>
						<td><?php echo $i;?></td>
						<td><?php echo $qrtest1["patient_id"]."/".$qrtest1["opd_id"];?></td>
						<td><?php echo $pat_info["name"]."/".$pat_info["age"]." ".$pat_info["age_type"]."/".$pat_info["sex"];?></td>
						<td><?php echo $ref_doc["ref_name"]." ( ".$ref_doc["qualification"]." )";?></td>
						<td><?php echo $center_name["centrename"];?></td>
						<td><?php echo "";?></td>
						<td><?php echo $pat_pay_detail['tot_amount'];?></td>
						<td><?php echo $pat_pay_detail['dis_amt'];?></td>
						<td><?php echo $pat_pay_detail['advance'];?></td>
						<td><?php echo $pat_pay_detail['balance'];?></td>
						<td><?php echo $quser["name"];?></td>
					</tr>
				<?php
					$qtest=mysqli_query($link,"select a.testid,a.test_rate,b.testname from patient_test_details a,testmaster b where a.patient_id='$qrtest1[patient_id]' and a.opd_id='$qrtest1[opd_id]' and a.testid=b.testid  order by b.testname");
					while($qtest1=mysqli_fetch_array($qtest))
					{
					?>
						<tr>
							<td colspan="2"></td>
							<td colspan="3"><i><?php echo $qtest1['testname'];?></i></td>
							<td align="right"><i><?php echo $qtest1['test_rate'];?></i></td>
							<td colspan="6">&nbsp;</td>
						</tr>
					<?php
					}
					$i++;
				}
			?>
				
				
			</table>
		</div>
	</body>
</html>
