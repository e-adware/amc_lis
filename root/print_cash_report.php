<?php
include("../includes/connection.php");
require("../includes/global.function.php");

$date=date('Y-m-d');
$user=base64_decode($_GET["user"]);
$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$user'"));
$snp=mysqli_fetch_array(mysqli_query($link,"SELECT `snippets` FROM `level_master` WHERE `levelid`='$emp[levelid]'"));
?>
<html>
	<head>
		<title>Cashier Reports</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../css/bootstrap.min.css" />
		<style>
			.table
			{
				font-size:12px;
			}
			.hed
			{
				background: linear-gradient(-90deg, #FDFDFD, #969899);
			}
			.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
			{
				padding:2px;
				//border:1px solid;
				//border-top:none;
			}
		</style>
	</head>
	<body>
		<div class="container-fluid cont">
			<?php
			$line=10;
			include('page_header.php');
			if($emp['levelid']==1)
			{
				$all_total=0;
				$empl=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='3' OR `levelid`='14' OR `levelid`='15' OR `levelid`='16' OR `levelid`='18' OR `levelid`='19' OR `levelid`='1'");
				while($e=mysqli_fetch_array($empl))
				{
					$o_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`tot_amount`) AS total FROM `consult_patient_payment_details` WHERE `date`='$date' AND `user`='$e[emp_id]'"));
					$i_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `user`='$e[emp_id]'  AND `bill_no` like '%/IP'"));
					$casualty=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='4') AND `user`='$e[emp_id]'"));
					$dental=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='7') AND `user`='$e[emp_id]'"));
					$emergncy=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='6') AND `user`='$e[emp_id]'"));
					$physio=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='5') AND `user`='$e[emp_id]'"));
					$l_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `invest_payment_detail` WHERE `date`='$date' AND `user`='$e[emp_id]'"));
					$p_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ph_payment_details` WHERE `entry_date`='$date' AND `user`='$e[emp_id]' "));
					$all_total=0;	//$all_total=$o_c['total']+$i_c['total']+$casualty['total']+$dental['total']+$emergncy['total']+$physio['total']+$l_c['total']+$p_c['total'];
				?>
				<table class="table table-condensed table-bordered">
					<tr>
						<th colspan="2" style="text-align:center;background:linear-gradient(-90deg, #eeeeee, #aaaaaa);"><?php echo $e['name'];?></th>
					</tr>
				<?php
					if (strpos($snp['snippets'], '1@') !== false)
					{
						$all_total+=$o_c['total'];
				?>
					<tr>
						<th style="width: 70%;">OPD</th>
						<td><?php if($o_c['total']){echo "&#8377 ".number_format($o_c['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '3@') !== false)
					{
						$all_total+=$l_c['total'];
				?>
					<tr>
						<th style="width: 70%;">LAB</th>
						<td><?php if($l_c['total']){echo "&#8377 ".number_format($l_c['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '2@') !== false)
					{
						$all_total+=$i_c['total'];
				?>
					<tr>
						<th style="width: 70%;">IPD</th>
						<td><?php if($i_c['total']){echo "&#8377 ".number_format($i_c['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '7@') !== false)
					{
						$all_total+=$casualty['total'];
				?>
					<tr>
						<th style="width: 70%;">Casualty</th>
						<td><?php if($casualty['total']){echo "&#8377 ".number_format($casualty['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '8@') !== false)
					{
						$all_total+=$dental['total'];
				?>
					<tr>
						<th style="width: 70%;">Dental</th>
						<td><?php if($dental['total']){echo "&#8377 ".number_format($dental['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '9@') !== false)
					{
						$all_total+=$emergncy['total'];
				?>
					<tr>
						<th style="width: 70%;">Emergency</th>
						<td><?php if($emergncy['total']){echo "&#8377 ".number_format($emergncy['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '10@') !== false)
					{
						$all_total+=$physio['total'];
				?>
					<tr>
						<th style="width: 70%;">Physiotherapy</th>
						<td><?php if($physio['total']){echo "&#8377 ".number_format($physio['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '4@') !== false)
					{
						$all_total+=$p_c['total'];
				?>
					<tr>
						<th style="width: 70%;">Pharmacy</th>
						<td><?php if($p_c['total']){echo "&#8377 ".number_format($p_c['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if ($snp['snippets'])
					{
				?>
					<tr>
						<th style="width: 70%;">Total</th>
						<td><?php echo "&#8377 ".number_format($all_total,2);?></td>
					</tr>
				<?php } ?>
				</table>
				<?php
				}
			}
			else
			{
			?>
			<center><h4><u>Cashier Reports</u></h4></center>
			<table class="table table-condensed table-bordered">
			<?php
				$ename=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$user'"));
			
				$o_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`tot_amount`) AS total FROM `consult_patient_payment_details` WHERE `date`='$date' AND `user`='$user'"));
				$i_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `user`='$user'  AND `bill_no` like '%/IP'"));
				$casualty=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='4') AND `user`='$user'"));
				$dental=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='7') AND `user`='$user'"));
				$emergncy=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='6') AND `user`='$user'"));
				$physio=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='5') AND `user`='$user'"));
				$l_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `invest_payment_detail` WHERE `date`='$date' AND `user`='$user'"));
				$p_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ph_payment_details` WHERE `entry_date`='$date' AND `user`='$user'"));
				//$all_total=$o_c['total']+$i_c['total']+$casualty['total']+$dental['total']+$emergncy['total']+$physio['total']+$l_c['total']+$p_c['total'];
			?>
				<?php
					if (strpos($snp['snippets'], '1@') !== false)
					{
						$all_total+=$o_c['total'];
				?>
					<tr>
						<th style="width: 70%;">OPD</th>
						<td><?php if($o_c['total']){echo "&#8377 ".number_format($o_c['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '3@') !== false)
					{
						$all_total+=$l_c['total'];
				?>
					<tr>
						<th style="width: 70%;">LAB</th>
						<td><?php if($l_c['total']){echo "&#8377 ".number_format($l_c['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '2@') !== false)
					{
						$all_total+=$i_c['total'];
				?>
					<tr>
						<th style="width: 70%;">IPD</th>
						<td><?php if($i_c['total']){echo "&#8377 ".number_format($i_c['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '7@') !== false)
					{
						$all_total+=$casualty['total'];
				?>
					<tr>
						<th style="width: 70%;">Casualty</th>
						<td><?php if($casualty['total']){echo "&#8377 ".number_format($casualty['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '8@') !== false)
					{
						$all_total+=$dental['total'];
				?>
					<tr>
						<th style="width: 70%;">Dental</th>
						<td><?php if($dental['total']){echo "&#8377 ".number_format($dental['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '9@') !== false)
					{
						$all_total+=$emergncy['total'];
				?>
					<tr>
						<th style="width: 70%;">Emergency</th>
						<td><?php if($emergncy['total']){echo "&#8377 ".number_format($emergncy['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '10@') !== false)
					{
						$all_total+=$physio['total'];
				?>
					<tr>
						<th style="width: 70%;">Physiotherapy</th>
						<td><?php if($physio['total']){echo "&#8377 ".number_format($physio['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if (strpos($snp['snippets'], '4@') !== false)
					{
						$all_total+=$p_c['total'];
				?>
					<tr>
						<th style="width: 70%;">Pharmacy</th>
						<td><?php if($p_c['total']){echo "&#8377 ".number_format($p_c['total'],2);}else{echo "&#8377 0.00";}?></td>
					</tr>
				<?php } ?>
				<?php
					if ($snp['snippets'])
					{
				?>
					<tr>
						<th style="width: 70%;">Total</th>
						<td><?php echo "&#8377 ".number_format($all_total,2);?></td>
					</tr>
				<?php } ?>
			</table>
			<?php
			}
			?>
		</div>
	</body>
</html>
