<!DOCTYPE html>
<html>
	<head>
		<title>Monthly Receipts</title>
		<link href="../../css/bootstrap.css" type="text/css" rel="stylesheet"/>
		<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	</head>
	<body>
		<?php
			function chnage_d($dt)
			{
			 $d=explode("/",$dt);
			$ndate=$d[2]."-".$d[1]."-".$d[0];
			return $ndate;
			}
			
			include'../../includes/connection.php';
			//$date1 = $_GET['date1'];
			$fdate=$_GET['fdate'];
			$tdate=$_GET['tdate'];
			$centr=$_GET['centr'];
			
			$cmpny=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select centrename from centremaster where centreno='$centr'"));
			
			//$date2 = $_GET['date2'];
			
			?>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="text-center">
						<h3>List Preview</h3>
						<p><strong>Centre: <?php echo $cmpny['centrename'];?></strong></p>
						<p>From: <?php echo $fdate;?> To: <?php echo $tdate;?> </p>
						<div class="no_print bottom-margin"><input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" /> <input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div>
					</div>
					<?php
						?>
					<form name="form1" id="form1" method="post" action="">
						<table class="table table-bordered table-condensed table-report">
							<tr>
								<td>Date</td>
								<td>Patient ID</td>
								<td >Name</td>
								<td class="text-right">Total Amount</td>
								<td class="text-right">Advance</td>
								<td class="text-right" >Discount</td>
								<td class="text-right">Balance</td>
							</tr>
							<?php
								$tot_am=0;
								$tot_adv=0;
								$tot_disc=0;
								$tot_bal=0;
								 $qrslct=mysqli_query($GLOBALS["___mysqli_ston"], "select a.*,b.reg_no,c.name from patient_details a,patient_reg_details b,patient_info c where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.date between'$fdate' and'$tdate' and a.centreno='$centr' and a.patient_id=c.patient_id order by b.date, b.reg_no ");
								 
								 while($qrslct1=mysqli_fetch_array($qrslct)){
								 
								 $qrch=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_payment_details where patient_id='$qrslct1[patient_id]' and visit_no='$qrslct1[visit_no]'"));
								 
								 $qrdoc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ref_name from refbydoctor_master where refbydoctorid='$qrslct1[refbydoctorid]'"));
								 
								 $tot_am=$tot_am+$qrch['tot_amount'];
								$tot_adv=$tot_adv+$qrch['advance'];
								$tot_disc=$tot_disc+$qrch['discount'];
								$tot_bal=$tot_bal+ $qrch['balance']; 
								 
								 ?>
							<tr>
								<td><?php echo $qrslct1['date'];?></td>
								<td><?php echo $qrslct1['reg_no'];?></td>
								<td><?php echo $qrslct1['name'];?></td>
								<td class="text-right"><?php echo $qrch['tot_amount'];?></td>
								<td class="text-right"><?php echo $qrch['advance'];?></td>
								<td class="text-right"><?php echo $qrch['discount'];?></td>
								<td class="text-right"><?php echo $qrch['balance'];?></td>
							</tr>
							<?php
								;}
								?>
							<tr>
								<td colspan="3" class="text-right"><strong>Total</strong></td>
								<td class="text-right"><strong><?php echo number_format($tot_am,2);?></strong></td>
								<td class="text-right"><strong><?php echo number_format($tot_adv,2);?></strong></td>
								<td class="text-right"><strong><?php echo number_format($tot_disc,2);?></strong></td>
								<td class="text-right"><strong><?php echo number_format($tot_bal,2);?></strong></td>
							</tr>
						</table>
						<br>
						<p class="text-right"><strong>Manager</strong></p>
						<?php
							$qrcnt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select count(distinct date) as countdate from patient_details where centreno='$centr' and date between '$fdate' and '$tdate'"));
							$vcamt=$qrcnt['countdate']*20;
							?>
						
						</p>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
