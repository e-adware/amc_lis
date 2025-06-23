<!DOCTYPE html>
<html>
	<head>
		<title>Cheque Payment Report</title>
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
			$new_date = date('d-M-Y', $timestamp);
			return $new_date;
			}
			
			?>
		<div class="container-fluid">
			<div class="text-center">
				<h3>Userwise Summary Report</h3>
				<p>From: <?php echo $date1;?> To: <?php echo $date2;?></p>
				<div class="no_print bottom-margin"><input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" /> <input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div>
				</div>
				<table class="table table-bordered table-condensed">						
					<tr>
						<th>#</th>
						<th>User Name</th>
						<th><span class="text-right">Amount</span></th>
					</tr>
					<?php
						$vttl=0;
						$i=1;	
						$qrtest=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT 
						  distinct(a.user),b.name  FROM invest_patient_payment_details a,employee b WHERE a.user=b.emp_id and a.date between'$date1' and '$date2'  order by b.name ");
						while($qrtest1=mysqli_fetch_array($qrtest))
						{
							$qamt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ifnull(sum(amount),0) as maxamt from invest_payment_detail where date between'$date1' and '$date2' and user='$qrtest1[user]' "));	
							$vttl=$vttl+$qamt['maxamt'];
					?>
							<tr>
								<td><?php echo $i;?></td>
								<td><?php echo $qrtest1['name'];?></td>
								<td><span class="text-right"><?php echo $qamt['maxamt'];?>.00</span></td>
							</tr>
						<?php
							$i++;
						}
						?>
							<tr>
								<td colspan="2" class="">Total Cash Collection</td>							
								<td><span class="text-right"><?php echo $vttl.'.00';?></span></td>
							</tr>
							<tr>
								<td colspan="3">Extra Receipt</td>
							</tr>
					<?php
						$vttlextra=0;
						$i=1;
						$qrexp=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select sum(Amount) as maxexp from expensedetail where Date1 between'$date1' and'$date2'"));	
						  $qrtest=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT distinct userid FROM center_extra_receipt WHERE date between'$date1' and '$date2'  order by userid ");
							
						while($qrtest1=mysqli_fetch_array($qrtest)){
						//$qamt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ifnull(sum(amount),0) as maxextraamt from extra_receipt where date1 between'$date1' and '$date2' and user='$qrtest1[user]' "));	
						$qamt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ifnull(sum(amount),0) as maxextraamt from center_extra_receipt where date between'$date1' and '$date2' and userid='$qrtest1[userid]' "));	
						$vttlextra=$vttlextra+$qamt['maxextraamt'];
						?>
					<tr>
						<td><?php echo $i;?></td>
						<td>
							<?php 
								
								$uname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select Name from Employee where ID='$qrtest1[userid]'"));
								echo $uname[Name];
							?>
						</td>
						<td><span class="text-right"><?php echo $qamt['maxextraamt'];?></span></td>
					</tr>
					<?php
						$i++;}
						?>
					<tr>
						<td colspan="2" class="" >Total Extra Receipt Collection</td>
						<td><span class="text-right"><?php echo $vttlextra.'.00';?></span></td>
					</tr>
					<?php
						$vtlcash=$vttlextra+$vttl-$qrexp['maxexp'];
						?>
					<tr>
						<td colspan="2" class="">Expense</td>
						<td><span class="text-right"><?php echo number_format($qrexp['maxexp'],2);?></span></td>
					</tr>
					<tr class="">
						<td colspan="2"><strong>Net Collection</strong></td>
						<td><span class="text-right"><strong><?php echo number_format($vtlcash,2);?></strong></span></td>
					</tr>
				</table>
		</div>
	</body>
</html>
