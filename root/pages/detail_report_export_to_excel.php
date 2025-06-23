
<html>
<head>
<title>Detail Report</title>

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
	include'../../includes/connection.php';

	$date1=$_GET['date1'];
	$date2=$_GET['date2'];

	function convert_date($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d M Y', $timestamp);
		return $new_date;
	}	
	$filename ="detail_report".$date1."_to_".$date2.".xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);

	?>
	<p style="margin-top: 2%;"><b>Lab Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?></p>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Date </th>
			<th>UHID / PIN</th>
			<th>Patient Name</th>
			<th>Ref Doctor</th>
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
				<th><?php echo $i;?></th>
				<th><?php echo $pat_info["uhid"]."/".$qrtest1["opd_id"];?></th>
				<th><?php echo $pat_info["name"]."/".$pat_info["age"]." ".$pat_info["age_type"]."/".$pat_info["sex"];?></th>
				<th><?php echo $ref_doc["ref_name"];?></th>
				<th><?php echo $center_name["centrename"];?></th>
				<th><?php echo "";?></th>
				<th><?php echo number_format($pat_pay_detail['tot_amount'],2);?></th>
				<th><?php echo number_format($pat_pay_detail['dis_amt'],2);?></th>
				<th><?php echo number_format($pat_pay_detail['advance'],2);?></th>
				<th><?php echo number_format($pat_pay_detail['balance'],2);?></th>
				<th><?php echo $quser["name"];?></th>
			</tr>
		<?php
			$qtest=mysqli_query($link,"select a.testid,a.test_rate,b.testname from patient_test_details a,testmaster b where a.patient_id='$qrtest1[patient_id]' and a.opd_id='$qrtest1[opd_id]' and a.testid=b.testid  order by b.testname");
			while($qtest1=mysqli_fetch_array($qtest))
			{
			?>
				<tr>
					<td colspan="2"></td>
					<td colspan="3"><i><?php echo $qtest1['testname'];?></i></td>
					<td align="right"><i><?php echo number_format($qtest1['test_rate'],2);?></i></td>
					<td colspan="6">&nbsp;</td>
				</tr>
			<?php
			}
			$vaccu_charge_qry=mysqli_query($link, " SELECT `rate` FROM `patient_vaccu_details` WHERE `patient_id`='$qrtest1[patient_id]' and `opd_id`='$qrtest1[opd_id]' ");
			$vaccu_charge_num=mysqli_num_rows($vaccu_charge_qry);
			if($vaccu_charge_num>0)
			{
				$tot_vaccu="";
				while($vaccu_charge=mysqli_fetch_array($vaccu_charge_qry))
				{
					$tot_vaccu=$tot_vaccu+$vaccu_charge["rate"];
				}
			}
			?>
				<tr>
					<td colspan="2"></td>
					<td colspan="3"><i>Vaccu Charge</i></td>
					<td align="right"><i><?php echo number_format($tot_vaccu,2);?></i></td>
					<td colspan="6">&nbsp;</td>
				</tr>
			<?php
			$i++;
		}
	?>
	</table>
</div>
</body>
</html>
