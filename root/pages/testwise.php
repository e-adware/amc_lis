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
			
			$cmpny=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select centrename from centremaster where centreno='$centr'"));
			//$date2 = $_GET['date2'];
			?>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="text-center">
						<h3>Testwise</h3>
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
								<td>Name</td>
								<td>Doctor Name</td>
								<td class="text-right">Rate</td>
								<td class="text-right">Discount Rate</td>
							</tr>
							<?php
								$qrslct=mysqli_query($GLOBALS["___mysqli_ston"], "select a.*,b.reg_no,c.name from patient_details a,patient_reg_details b,patient_info c where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.date between'$fdate' and'$tdate' and a.centreno='$centr' and a.patient_id=c.patient_id order by b.date,b.reg_no ");
								
								while($qrslct1=mysqli_fetch_array($qrslct)){
							     $vcrate=0;	
							     $prate=0;	
								$dn=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ref_name from refbydoctor_master where refbydoctorid='$qrslct1[refbydoctorid]'"));
								$qpaymnt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_payment_details where patient_id='$qrslct1[patient_id]' and visit_no='$qrslct1[visit_no]'"));
								$qvaccu=mysqli_fetch_array(mysqli_query($link,"select * from patient_vaccu_details  where patient_id='$qrslct1[patient_id]' and visit_no='$qrslct1[visit_no]' "));
								
								?>
							<tr>
								<td style="font-weight:bold;font-size:12px"><?php echo $qrslct1['date'];?></td>
								<td style="font-weight:bold;font-size:12px"><?php echo $qrslct1['reg_no'];?></td>
								<td style="font-weight:bold;font-size:12px"><?php echo $qrslct1['name'];?></td>
								<td colspan="3" style="font-weight:bold;font-size:12px"><?php echo substr($dn['ref_name'],0,30);?></td>
							</tr>
							<?php
								$qrtest=mysqli_query($GLOBALS["___mysqli_ston"], "select a.testid,a.test_rate,b.testname,b.rate from patient_test_details a, testmaster b where a.testid=b.testid and a.patient_id='$qrslct1[patient_id]' and a.visit_no='$qrslct1[visit_no]' order by b.testname");
								$rs=mysqli_num_rows($qrtest);
								while($qrtest1=mysqli_fetch_array($qrtest)) {
								$vnrmlrt=$qrtest1['rate'];	
								if($qrtest1['test_rate']==0)
								{
									$vnrmlrt=0;
								}
								
								$vcrate=$vcrate+$vnrmlrt;	
								$prate=$prate+$qrtest1['test_rate'];
									
								$vrate=$vrate+$vnrmlrt;
								$vdsrate=$vdsrate+$qrtest1['test_rate'];
								?> 
							<tr>
								<td></td>
								<td></td>
								<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;<i><?php echo substr($qrtest1['testname'],0,40);?></i></td>
								<td class="text-right" ><?php echo number_format($vnrmlrt,2);?></td>
								<td class="text-right"><?php echo $qrtest1['test_rate'];?></td>
							</tr>
							<?php
								; }
								?>
								<?php
							   if($qvaccu)
							    {
									$vcrate=$vcrate+20;
									$prate=$prate+20;
									
									$vrate=$vrate+20;
									$vdsrate=$vdsrate+20;
									
									?>
									<tr>
										<td colspan="2"></td>
										<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;<i>Vaccu</i></td>
										<td align="right"><i><?php echo '20.00';?></i></td>
										<td align="right"><i><?php echo '20.00';?></i></td>
								    </tr>
									
								<?php
							   }	
							   ?>
								<tr>
									<td colspan="3"><strong>Total Amount :<?php echo $qpaymnt['tot_amount'];?>&nbsp;Discount :<?php echo $qpaymnt['discount'];?>&nbsp;Paid :<?php echo $qpaymnt['advance'];?>&nbsp;Balance :<?php echo $qpaymnt['balance'];?></strong></td>
									
									<td><strong>Total</strong></td>
									<td class="text-right" ><strong><?php echo number_format($vcrate,2);?></strong></td>
									<td class="text-right" ><strong><?php echo number_format($prate,2);?></strong></td>
								</tr>
							<?php
								;}
								?>
							<tr>
								<td colspan="4" class="text-right"><strong>Total</strong></td>
								<td class="text-right"><strong><?php echo $vrate.'.00';?></strong></td>
								<td class="text-right"><strong><?php echo $vdsrate.'.00';?></strong></td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
