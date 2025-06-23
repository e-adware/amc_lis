<!DOCTYPE html>
<html>
	<head>
		<title>Monthly Receipts</title>
		<link href="../../css/bootstrap.css" type="text/css" rel="stylesheet"/>
		<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	</head>
	<body>
		<?php
			include'../../includes/connection.php';
			//$date1 = $_GET['date1'];
			
			$fdate=$_GET['fdate'];
			$tdate=$_GET['tdate'];
			$centr=$_GET['centr'];
			
			//$date2 = $_GET['date2'];
			$cmpny=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select centrename from centremaster where centreno='$centr'"));
			?>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="text-center">
						<h3>Centre Bill</h3>
						<p><strong>Centre: <?php echo $cmpny['centrename'];?></strong></p>
						<p>From: <?php echo $fdate;?> To: <?php echo $tdate;?> </p>
						<div class="no_print bottom-margin"><a class="btn btn-success" href="centerbill_excel_rpt.php?fdate=<?php echo $fdate;?>&tdate=<?php echo $tdate;?>&centr=<?php echo $centr;?>">Export to Excel</a> <input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" /> <input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div>
					</div>
					<?php
						?>
					<form name="form1" id="form1" method="post" action="">
						<table class="table table-bordered table-condensed table-report">
							<tr>
								<td width="80">Date</td>
								<td>Patient ID</td>
								<td>Name</td>
								<td class="text-right">Total Amount</td>
								<td class="text-right">Advance</td>
								<td class="text-right">Discount</td>
								<td class="text-right">Balance</td>
							</tr>
							<?php
								$tot_am=0;
								$tot_adv=0;
								$tot_disc=0;
								$tot_bal=0;
								
								$qextrapaymnt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ifnull(sum(amount),0) as maxextrpaid from center_extra_receipt where date between'$fdate' and'$tdate' and centreno='$centr' "));
								$qprvosbalance=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select balance_amt  from center_extra_receipt where  centreno='$centr' order by slno desc limit 1 "));
								
								 $qrslct=mysqli_query($GLOBALS["___mysqli_ston"], "select a.*,b.reg_no,c.name from patient_details a,patient_reg_details b,patient_info c where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.date between'$fdate' and'$tdate' and a.centreno='$centr' and a.patient_id=c.patient_id order by a.date,b.reg_no ");
								 
								 while($qrslct1=mysqli_fetch_array($qrslct))
								 {
								 
								 $qrch=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_payment_details where patient_id='$qrslct1[patient_id]' and visit_no='$qrslct1[visit_no]'"));
								 
								 $qrvacu=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select sum(rate)as vaccu from patient_vaccu_details where  patient_id='$qrslct1[patient_id]' and visit_no='$qrslct1[visit_no]'"));
								 
								 $qrdoc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ref_name from refbydoctor_master where refbydoctorid='$qrslct1[refbydoctorid]'"));
								 
								 $tot_am=$tot_am+$qrch['tot_amount'];
								$tot_adv=$tot_adv+$qrch['advance'];
								$tot_disc=$tot_disc+$qrch['discount'];
								$tot_bal=$tot_bal+ $qrch['balance']; 
								$vacu=$vacu+$qrvacu['vaccu']; 
								 
								 ?>
							<tr>
								<td ><?php echo $qrslct1['date'];?></td>
								<td ><?php echo $qrslct1['reg_no'];?></td>
								<td ><?php echo substr($qrslct1['name'],0,15);?></td>
								<td class="text-right"><?php echo $qrch['tot_amount'];?></td>
								<td class="text-right"><?php echo $qrch['advance'];?></td>
								<td class="text-right"><?php echo $qrch['discount'];?></td>
								<td class="text-right"><?php echo $qrch['balance'];?></td>
							</tr>
							<?php
								;}
								?>
						</table>
						<table class="table table-condensed">
							<tr>
								<td>Bill Amount:</td>
								<td class="text-right"><?php echo $tot_am.'.00';?></td>
								<td class="text-right"><?php echo $tot_adv;?></td>
								<td class="text-right"><?php echo $tot_disc;?></td>
								<td class="text-right"><?php echo $tot_bal;?></td>
							</tr>
							<tr>
								<td>Pt. Discount (-):</td>
								<td class="text-right"><?php echo $tot_disc.'.00';?></td>
								<td class="text-right"></td>
								<td class="text-right"></td>
								<td class="text-right"></td>
							</tr>
							<tr>
								<?php
									$vnetamt=0;
									
									$vnetamt=$tot_am-($tot_disc+$vacu);
									$vbalance=$vnetamt+$qprvosbalance['balance_amt']-$qextrapaymnt['maxextrpaid'];
									?>
								<td>Extra/Vaccu (-) :</td>
								<td class="text-right"><?php echo $vacu.'.00';?></td>
								<td class="text-right"></td>
								<td class="text-right"></td>
								<td class="text-right"></td>
							</tr>
							<tr>
								<td><strong>Net Amount:</strong></td>
								<td class="text-right"><strong><?php echo $vnetamt.'.00';?></strong></td>
								<td class="text-right"></td>
								<td class="text-right"></td>
								<td class="text-right"></td>
							</tr>
							
							
							
						</table>
						<p class="text-right"><strong>Manager</strong></p>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
