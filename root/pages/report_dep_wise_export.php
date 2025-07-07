<?php
include'../../includes/connection.php';
include"../../includes/global.function.php";

$fdate = base64_decode($_GET['fDt']);
$tdate = base64_decode($_GET['tDt']);
$dep = base64_decode($_GET['dEp']);
$type = base64_decode($_GET['tYp']);

if($type==1)
{
	$pageHeader="Department Wise Report";
}
if($type==2)
{
	$pageHeader="Department Wise Report";
}
if($type==3)
{
	$pageHeader="Patient Report Records";
}

$filename ="department_wise_report_from_".$fdate."_to_".$tdate.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
<html>
<head>
<title>Department Wise Reports</title>
</head>
<body>
<?php
if($type==1)
{
	$qry="select distinct a.patient_id,a.opd_id from uhid_and_opdid a,patient_test_details b,testmaster c where a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.testid=c.testid and c.type_id='$dep' and b.date=a.date and a.date between '$fdate' and '$tdate' order by a.slno";
	
	//echo $qry;
	$dname=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$dep'"));
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>Sl No</th>
			<th>Date Time</th>
			<th>P No</th>
			<th>Cash Memo No</th>
			<th>Hosp No</th>
			<th>Patient Name</th>
			<th>Age Sex</th>
			<th style="text-align:right;">Amount</th>
			<th>Particulars of test</th>
			<th>Remarks</th>
		</tr>
		<?php
		$qry1=mysqli_query($link,$qry);
		$i=1;
		
		$tot=0;
		while($q=mysqli_fetch_array($qry1))
		{
			$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
			
			$det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where patient_id='$q[patient_id]' and opd_id='$q[opd_id]'"));
			$amt=mysqli_fetch_array(mysqli_query($link,"SELECT IFnull(SUM(a.`test_rate`),0) AS `rate` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`type_id`='$dep' AND a.`patient_id`='$q[patient_id]' AND a.`opd_id`='$q[opd_id]'"));
			
			$tNames="";
			$tstDet=mysqli_query($link,"SELECT b.`testname` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`type_id`='$dep' AND a.`patient_id`='$q[patient_id]' AND a.`opd_id`='$q[opd_id]'");
			while($tt=mysqli_fetch_array($tstDet))
			{
				if($tNames)
				{
					$tNames.=", ".$tt['testname'];
				}
				else
				{
					$tNames=$tt['testname'];
				}
			}
			?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $det['date']." ".$det['time'];?></td>
			<td><?php echo $det['opd_id'];?></td>
			<td><?php echo $det['bill_no'];?></td>
			<td><?php echo $info['hosp_no'];?></td>
			<td><?php echo $info['name'];?></td>
			<td><?php echo $info['age']." ".$info['age_type']." / ".$info['sex'];?></td>
			<td style="text-align:right;"><?php echo number_format($amt['rate'],2);?></td>
			<td><?php echo $tNames;?></td>
			<td><?php echo $dname['name'];?></td>
		</tr>
			<?php
			$tot+=$amt['rate'];
			$i++;
		}
		?>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th colspan="2" style="text-align:right;"><?php echo number_format($tot,2);?></th>
			<th></th>
			<th></th>
		</tr>
	</table>
	<?php
}

if($type==2)
{
	$qry="select distinct a.patient_id,a.opd_id from uhid_and_opdid a,patient_test_details b,testmaster c where a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.testid=c.testid and c.type_id='$dep' and b.date=a.date and a.date between '$fdate' and '$tdate' order by a.slno";
	
	//echo $qry;
	$dname=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$dep'"));
	?>
	<table class="table table-condensed table-bordered table-report">
	<tr>
		<th>#</th>
		<th>Hosp No</th>
		<th>Pat No</th>
		<th>Name</th>
		<th>Age/Sex</th>
		<th><?php echo $dname['name'];?> Tests <span style="float:right;">Rate</span></th>
		<th>Date / Time</th>
		<th>User</th>
	</tr>
	<?php
	$qry1=mysqli_query($link,$qry);
	$i=1;
	
	$tot=0;
	while($q=mysqli_fetch_array($qry1))
	{
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		
		$det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where patient_id='$q[patient_id]' and opd_id='$q[opd_id]'"));
		$emp=mysqli_fetch_array(mysqli_query($link,"select `name` from `employee` where `emp_id`='$det[user]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $det[hosp_no];?></td>
			<td><?php echo $det[opd_id];?></td>
			<td><?php echo $info[name];?></td>
			<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td>
			<td>
				<table class="table table-condensed" id="test_data" style="margin:0px;padding:0px;font-size:12px;margin-bottom:0px;">
				<?php
				$j=1;
				$tests=mysqli_query($link,"select a.testname,b.`test_rate` from testmaster a,patient_test_details b where b.patient_id='$q[patient_id]' and b.opd_id='$q[opd_id]' and a.testid=b.testid and a.type_id='$dep'");
				
				while($tst=mysqli_fetch_array($tests))
				{
					//echo $tst[testname]." ".$tst['test_rate']."<br/>";
					?>
					<tr>
						<td><?php echo $tst['testname'];?></td>
						<td style="text-align:right;"><?php echo $tst['test_rate'];?></td>
					</tr>
					<?php
					$tot+=$tst['test_rate'];
					$j++;
				}
				?>
				</table>
			</td>
			<td><?php echo convert_date($det['date'])." / ".convert_time($det['time']);?></td>
			<td><?php echo $emp['name'];?></td>
		</tr>
		<?php
		$i++;
		
	}
	?>
	<tr>
		<th colspan="4"></th>
		<th colspan="2" style="text-align:right;"><?php echo number_format($tot,2);?></th>
		<th></th>
		<th></th>
	</tr>
	</table> <?php
}


if($type==3)
{
	$qry="select distinct a.patient_id,a.opd_id from uhid_and_opdid a,patient_test_details b,testmaster c where a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.testid=c.testid and c.type_id='$dep' and b.date=a.date and a.date between '$fdate' and '$tdate' order by a.slno";
	
	//echo $qry;
	$dname=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$dep'"));
	?>
	<table class="table table-condensed table-bordered table-report">
	<tr>
		<th>#</th>
		<th>Hosp No</th>
		<th>Pat No</th>
		<th>Name</th>
		<th>Age/Sex</th>
		<th><?php echo $dname['name'];?> Tests</th>
		<th>Date / Time</th>
	</tr>
	<?php
	$qry1=mysqli_query($link,$qry);
	$i=1;
	
	while($q=mysqli_fetch_array($qry1))
	{
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		
		$det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where patient_id='$q[patient_id]' and opd_id='$q[opd_id]'"));
		$emp=mysqli_fetch_array(mysqli_query($link,"select `name` from `employee` where `emp_id`='$det[user]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $det[hosp_no];?></td>
			<td><?php echo $det[opd_id];?></td>
			<td><?php echo $info[name];?></td>
			<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td>
			<td>
				<table class="table table-condensed" id="test_data" style="margin:0px;padding:0px;font-size:12px;margin-bottom:0px;">
				<?php
				$j=1;
				$tests=mysqli_query($link,"select a.`testid`,a.`testname`,b.`test_rate` from testmaster a,patient_test_details b where b.patient_id='$q[patient_id]' and b.opd_id='$q[opd_id]' and a.testid=b.testid and a.`type_id`='$dep'");
				
				while($tst=mysqli_fetch_array($tests))
				{
					$tstPar=mysqli_query($link,"SELECT `paramid`,`result` FROM `testresults` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' AND `testid`='$tst[testid]' AND `paramid` NOT IN(639,640,641) AND `result`!='' ORDER BY `sequence`");
					?>
						<?php
						if(mysqli_num_rows($tstPar)>1)
						{
						?>
						<tr>
							<td><i class="icon-circle"></i> <?php echo $tst['testname'];?></td>
							<td></td>
							<td></td>
						</tr>
						<?php
						while($pp=mysqli_fetch_array($tstPar))
						{
							$pDet=mysqli_fetch_array(mysqli_query($link,"SELECT `Name`, `UnitsID` FROM `Parameter_old` WHERE `ID`='$pp[paramid]'"));
							$unit=mysqli_fetch_array(mysqli_query($link,"SELECT `unit_name` FROM `Units` WHERE `ID`='$pDet[UnitsID]'"));
						?>
						<tr>
							<td> &nbsp;&nbsp;<i class="icon-caret-right"></i> <?php echo $pDet['Name'];?></td>
							<td><?php echo $pp['result'];?></td>
							<td><?php echo $unit['unit_name'];?></td>
						</tr>
						<?php
						}
						}
						else
						{
						$tstParRes=mysqli_fetch_assoc(mysqli_query($link,"SELECT `paramid`,`result` FROM `testresults` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' AND `testid`='$tst[testid]' AND `paramid` NOT IN(639,640,641) AND `result`!='' ORDER BY `sequence`"));
						$pDet=mysqli_fetch_array(mysqli_query($link,"SELECT `UnitsID` FROM `Parameter_old` WHERE `ID`='$tstParRes[paramid]'"));
						$unit=mysqli_fetch_array(mysqli_query($link,"SELECT `unit_name` FROM `Units` WHERE `ID`='$pDet[UnitsID]'"));
						?>
						<tr>
							<td><i class="icon-circle"></i> <?php echo $tst['testname'];?></td>
							<td><?php echo $tstParRes['result'];?></td>
							<td><?php echo $unit['unit_name'];?></td>
						</tr>
						<?php
						}
						?>
					</tr>
					<?php
					$j++;
				}
				?>
				</table>
			</td>
			<td><?php echo convert_date($det['date'])." / ".convert_time($det['time']);?></td>
		</tr>
		<?php
		$i++;
		
	}
	?>
	</table> <?php
}



mysqli_close($link);
	?>
</body>
</html>
