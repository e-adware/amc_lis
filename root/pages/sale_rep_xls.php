<?php
include('../../includes/connection.php');

$filename ="sale_report.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];

function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}

function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
?>
<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="8" style="text-align:center;"><h4>Sale Report from <?php echo convert_date($fdate);?> to <?php echo convert_date($tdate);?></h4></th>
	</tr>
	<tr>
		<th>Sl No</th>
		<th>Bill No</th>
		<th>Date</th>
		<th>Customer Name</th>
		<th>Amount</th>
		<th>Discount (Rs)</th>
		<th>Adjust (Rs)</th>
		<th>Paid</th>
		<th>Balance</th>
		
	</tr>
	<?php
	$n=1;
	$q=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate'");
	while($r=mysqli_fetch_array($q))
	{
		
	$vttl=$vttl+$r['total_amt'];
	$vttldis=$vttldis+$r['discount_amt'];
	$vttladjst=$vttladjst+$r['adjust_amt'];
	$vttlpaid=$vttlpaid+$r['paid_amt'];
	$vttlbl=$vttlbl+$r['balance'];
	?>
	<tr>
		<td><?php echo $n;?></td>
		<td><?php echo $r['bill_no'];?></td>
		<td><?php echo convert_date($r['entry_date']);?></td>
		<td><?php echo $r['customer_name'];?></td>
		<td><?php echo $r['total_amt'];?></td>
		<td><?php echo val_con(round($r['discount_amt']));?></td>
		<td><?php echo val_con(round($r['adjust_amt']));?></td>
		<td><?php echo val_con(round($r['paid_amt']));?></td>
		<td><?php echo $r['balance'];?></td>
		
	</tr>
	<?php
	$n++;
	}
	?>
		<tr class="line">
			<td align="right" colspan="4" style="font-weight:bold;font-size:13px">Total</td>
			<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttl,2);?></td>
			<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttldis,2);?></td>
			<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttladjst,2);?></td>
			<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttlpaid,2);?></td>
			<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttlbl,2);?></td>
			<td></td>
		</tr>
</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
