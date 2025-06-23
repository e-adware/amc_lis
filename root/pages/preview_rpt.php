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
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="text-center">
						<h3>Preview Report</h3>
						<p><strong>Centre: <?php echo $cmpny['centrename'];?></strong></p>
						<p>From: <?php echo $fdate;?> To: <?php echo $tdate;?></p>
						<div class="no_print bottom-margin"><input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" /> <input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div>
					</div>
					<?php
						?>
					<form name="form1" id="form1" method="post" action="">
						<table class="table table-bordered table-condensed table-report">
							<tr>
								<td>Date</td>
								<td>Patient ID</td>
								<td>Name</td>
								<td>Ref. Doc</td>
								<td class="text-right">Total Amount</td>
							</tr>
							<?php
								$tot_am=0;
								$tot_adv=0;
								$tot_disc=0;
								$tot_bal=0;
								$vamt=0;
								
								$vacuchk=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select vacu_charge from centremaster where centreno='$centr'"));
								
								//$q="SELECT a.*,b.vaccu_id,b.rate,d.reg_no FROM `patient_payment_details` a,patient_vaccu_details b,patient_details c,patient_reg_details d WHERE a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.patient_id=c.patient_id and a.visit_no=c.visit_no  and a.patient_id=d.patient_id and a.visit_no=d.visit_no and a.date between'2015-12-01' and '2015-12-31' and c.centreno='C184'";
								 $qrslct=mysqli_query($GLOBALS["___mysqli_ston"], "select a.*,b.reg_no,c.name from patient_details a,patient_reg_details b,patient_info c where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.date between'$fdate' and'$tdate' and a.centreno='$centr' and a.patient_id=c.patient_id order by a.date,b.slno ");
								 
								 while($qrslct1=mysqli_fetch_array($qrslct)){
								 
								 $qrch=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select tot_amount from patient_payment_details where patient_id='$qrslct1[patient_id]' and visit_no='$qrslct1[visit_no]'"));
								 
								 $qrdoc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ref_name from refbydoctor_master where refbydoctorid='$qrslct1[refbydoctorid]'"));
								 
								 
								 if($vacuchk['vacu_charge']==0)
								 {
									
									 $vvcuchrg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ifnull(sum(rate),0) as mxvcu from patient_vaccu_details where patient_id='$qrslct1[patient_id]' and visit_no='$qrslct1[visit_no]'"));
									 $vvcuamt=$vvcuchrg['mxvcu'];
								 }
								 else
								 {
									  $vvcuamt=0;
								 }
								 
								 
								 
								
								$vamt=0;
								
								$vamt=$qrch['tot_amount'];
								
								$tot_am=$tot_am+$vamt;
								
								 ?>
							<tr>
								<td><?php echo $qrslct1['date'];?></td>
								<td><?php echo $qrslct1['reg_no'];?></td>
								<td><?php echo $qrslct1['name'];?></td>
								<td><?php echo substr($qrdoc['ref_name'],0,15);?></td>
								<td class="text-right"><?php echo $vamt;?></td>
							</tr>
							<?php
								;}
								?>
							<tr>
								<td colspan="4" class="text-right"><strong>Total</strong></td>
								<td class="text-right"><strong><?php echo number_format($tot_am,2);?></strong></td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
