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
			
			?>
            <div class="container">
			<div class="row">
			  <div class="col-md-12">
	  <div class="text-center">
						<h3>Center Balance Summary</h3>
						
						<p>From: <?php echo $fdate;?> To: <?php echo $tdate;?> </p>
						<div class="no_print bottom-margin"><input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" /> <input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div>
                </div>
            
		<?php
			?>
		<form name="form1" id="form1" method="post" action="">
			<table class="table table-bordered table-condensed table-report mytable">
				<tr>
					<td>#</td>
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
					 $i=1;
					
					
					 
					 $qrslct=mysqli_query($GLOBALS["___mysqli_ston"], "select distinct(a.centreno),b.centrename from patient_details a,centremaster b,patient_payment_details c where a.centreno=b.centreno and a.centreno !='C100' and a.patient_id=c.patient_id and a.visit_no=c.visit_no and a.date between '$fdate' and '$tdate' and c.balance>0 order by b.centrename ");
					 
					 while($qrslct1=mysqli_fetch_array($qrslct)){
					 
					 $qrch=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select sum(a.tot_amount) as maxtot from patient_payment_details a,patient_details b where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.date between '$fdate' and '$tdate' and b.centreno='$qrslct1[centreno]' ")); 
					
					 
					 
					 $qrbln=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select sum(a.balance) as maxbln from patient_payment_details a,patient_details b where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.date between '$fdate' and '$tdate' and b.centreno='$qrslct1[centreno]' ")); 
					 $qrad=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select sum(a.advance) as maxad from patient_payment_details a,patient_details b where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.date between '$fdate' and '$tdate' and b.centreno='$qrslct1[centreno]' ")); 
					 $qrds=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select sum(a.discount) as maxds from patient_payment_details a,patient_details b where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.date between '$fdate' and '$tdate' and b.centreno='$qrslct1[centreno]' ")); ;
					 
					 
					 /* if($qrbln['maxbln']>0)
					 { */
					 $tot_am=$tot_am+$qrch['maxtot'];
					$tot_adv=$tot_adv+$qrad['maxad'];
					$tot_disc=$tot_disc+$qrds['maxds'];
					$tot_bal=$tot_bal+ $qrbln['maxbln']; 
					?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo substr($qrslct1['centrename'],0,27);?></td>
					<td class="text-right"><?php echo $qrch['maxtot'];?></td>
				  <td class="text-right"><?php echo $qrad['maxad'];?></td>
				  <td class="text-right"><?php echo $qrds['maxds'];?></td>
					<td class="text-right"><?php echo $qrbln['maxbln'];?></td>
				</tr>
				<?php
					$i++;
					 }
					
					?>
				<tr >
					<td colspan="2" class="text-right" ><strong>Total</strong></td>
					<td class="text-right"><strong><?php echo $tot_am;?></strong></td>
				  <td class="text-right"><strong><?php echo $tot_adv;?></strong></td>
				  <td class="text-right"><strong><?php echo $tot_disc;?></strong></td>
					<td class="text-right"><strong><?php echo $tot_bal;?></strong></td>
				</tr>
			</table>
		</form>
		</div></div>
		</div> 
	</body>
</html>
