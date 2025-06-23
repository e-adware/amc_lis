<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$cid=base64_decode($_GET['cid']);
$rep=base64_decode($_GET['type']);
$fdate=base64_decode($_GET['fdate']);
$tdate=base64_decode($_GET['tdate']);

if($rep==1)
$title="Patientwise";
if($rep==2)
$title="Testwise";
$cen=mysqli_fetch_array(mysqli_query($link,"SELECT `centrename` FROM `centremaster` WHERE `centreno`='$cid'"));
?>
<html>
	<head>
		<title>Center <?php echo $title;?> Report</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
		<style>
		.table
		{
			font-size:12px;
		}
		</style>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row">
				<?php
				include("page_header.php");
				?>
			</div>
			<?php
			echo "Center : <b>".$cen['centrename']."</b>";
			if($rep==1)
			{
			$qry=mysqli_query($link,"SELECT `patient_id`,`uhid`,`name`,`refbydoctorid` FROM `patient_info` WHERE `center_no`='$cid'");
			?>
			<table class="table table-condensed table-bordered">
				<tr>
					<th>#</th><th>Date</th><th>UHID</th><th>PIN</th><th>Name</th><th>Refer Doctor</th><th>Paid</th><th>Balance</th><th>Total Amount</th>
				</tr>
				<?php
				$i=1;
				$tot=$bal=$paid=0;
				while($rr=mysqli_fetch_array($qry))
				{
					$rdoc=mysqli_fetch_array(mysqli_query($link,"SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$rr[refbydoctorid]'"));
					$qq=mysqli_query($link,"SELECT DISTINCT `opd_id`,`ipd_id` FROM `patient_test_details` WHERE `patient_id`='$rr[patient_id]' AND `date` BETWEEN '$fdate' AND '$tdate'");
					$num=mysqli_num_rows($q);
					$dt="";
					while($r=mysqli_fetch_array($qq))
					{
						$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$rr[patient_id]' AND `opd_id`='$r[opd_id]' AND `ipd_id`='$r[ipd_id]' AND `date` BETWEEN '$fdate' AND '$tdate'");
						while($re=mysqli_fetch_array($q))
						{
						$dt=$re['date'];
						if($r['opd_id'])
						$vid=$r['opd_id'];
						if($r['ipd_id'])
						$vid=$r['ipd_id'];
						}
						$bl=mysqli_fetch_array(mysqli_query($link,"SELECT `tot_amount`,`advance`,`balance` FROM `invest_patient_payment_details` WHERE `patient_id`='$rr[patient_id]' AND `opd_id`='$r[opd_id]'"));
						?>				
						<tr>
							<td><?php echo $i;?></td><td><?php echo convert_date_g($dt);?></td><td><?php echo $rr['uhid'];?></td><td><?php echo $vid;?></td><td><?php echo $rr['name'];?></td><td><?php echo $rdoc['ref_name'];?></td><td><?php echo number_format($bl['tot_amount'],2);?></td><td><?php echo number_format($bl['advance'],2);?></td><td><?php echo number_format($bl['balance'],2);?></td>
						</tr>
						<?php
					$tot+=$bl['tot_amount'];
					$bal+=$bl['balance'];
					$paid+=$bl['advance'];
					$i++;
					}
				}
				?>
				<tr style="background:#cccccc;">
					<td colspan="9"></td>
				</tr>
				<tr>
					<td></td><td></td><td></td><td></td><td></td><td>Total Amount</td><th><?php echo "&#8377; ".number_format($paid,2);?></th><th><?php echo "&#8377; ".number_format($bal,2);?></th><th><?php echo "&#8377; ".number_format($tot,2);?></th>
				</tr>
			</table>
			<?php
			}
			if($rep==2)
			{
				?>
				<table class="table table-condensed table-bordered table-report">
					<tr>
						<th>#</th><th>Date</th><th>UHID</th><th>PIN</th><th>Name</th><th>Test Name</th><th>Rate</th><th>Vaccu Charge</th><th>Total</th>
					</tr>
					<?php
					$j=1;
					$tot=0;
					$v_rate=0;
					$qq=mysqli_query($link,"SELECT DISTINCT `patient_id` FROM `patient_test_details` WHERE `patient_id` IN (SELECT `patient_id` FROM `patient_info` WHERE `center_no`='$cid') AND `date` BETWEEN '$fdate' AND '$tdate' ORDER BY `date`");
					while($rr=mysqli_fetch_array($qq))
					{
						$qr=mysqli_query($link,"SELECT DISTINCT `opd_id`,`ipd_id` FROM `patient_test_details` WHERE `patient_id`='$rr[patient_id]' AND `date` BETWEEN '$fdate' AND '$tdate'");
						while($r=mysqli_fetch_array($qr))
						{
							$a=0;
							$qw=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$rr[patient_id]' AND `opd_id`='$r[opd_id]' AND `ipd_id`='$r[ipd_id]'");
							$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$rr[patient_id]' AND `opd_id`='$r[opd_id]' AND `ipd_id`='$r[ipd_id]'");
							while($m=mysqli_fetch_array($qw))
							{
								$a+=$m['test_rate'];
							}
							
							$vaccu=mysqli_query($link,"SELECT `rate` FROM `patient_vaccu_details` WHERE `patient_id`='$rr[patient_id]' AND `opd_id`='$r[opd_id]'");
							$vac=0;
							
							while($vcu=mysqli_fetch_array($vaccu))
							{
								$vac+=$vcu['rate'];
								//$v_rate+=$vac;
							}
							$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name`,`uhid` FROM `patient_info` WHERE `patient_id`='$rr[patient_id]'"));
							$num=mysqli_num_rows($q);
							while($re=mysqli_fetch_array($q))
							{
							$dt=$re['date'];
							if($re['opd_id'])
							$pin=$re['opd_id'];
							if($r['ipd_id'])
							$pin=$r['ipd_id'];
							$tst=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$re[testid]'"));
							
							$tot+=$re['test_rate'];
							$v+=$re['test_rate'];
							
							$net=$re['test_rate']+$vac;
							?>
					<tr>
						<?php if($num>0){echo "<td rowspan='".$num."'>".$j."</td><td rowspan='".$num."'>".convert_date_g($dt)."</td><td rowspan='".$num."'>".$pat['uhid']."</td><td rowspan='".$num."'>".$pin."</td><td rowspan='".$num."'>".$pat['name']."</td>";}?>
						<td><?php echo $tst['testname'];?></td>
						<td><?php echo number_format($re['test_rate'],2);?></td>
						<!--<td><?php echo number_format($vcc,2);?></td>-->
						<?php if($num>0){echo "<td rowspan='".$num."'>".number_format($vac,2)."</td>";}?>
						<?php if($num>0){echo "<td rowspan='".$num."'>".number_format(($a+$vac),2)."</td>";}?>
						<!--<td><?php echo number_format($net,2);?></td>-->
					</tr>
							<?php
							$num=0;
							}
							$v_rate+=$vac;
							$v+=$vac;
							$j++;
						}
					}
					?>
					<tr style="background:#cccccc;">
						<td colspan="9"></td>
					</tr>
					<tr>
						<td></td><td></td><td></td><td></td><td></td><td>Total Amount</td><th><?php echo "&#8377; ".number_format($tot,2);?></th><th><?php echo "&#8377; ".number_format($v_rate,2);?></th><th><?php echo "&#8377; ".number_format(($v),2);?></th>
					</tr>
				</table>
				<?php
			}
			?>
		</div>
	</body>
</html>
