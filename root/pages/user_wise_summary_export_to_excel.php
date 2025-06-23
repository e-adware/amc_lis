
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
	$filename ="user_wise_summary".$date1."_to_".$date2.".xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);

	?>
	<p style="margin-top: 2%;"><b>Userwise Summary Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
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
