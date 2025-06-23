<?php
include('../../includes/connection.php');

$filename ="sale_report_details.xls";
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
		<th colspan="10" style="text-align:center;"><h4>Sales Report details from <?php echo $fdate;?> to <?php echo $tdate;?></h4></th>
	</tr>
	<tr>
		<th>Sl No</th>
		<th>Bill No</th>
		<th>Customer Name</th>
		<th>Item Details</th>
		<th>Rate</th>
		<th>Quantity</th>
		<th>Amount</th>
		<th>Vat (Rs)</th>
		<th>Net Amount</th>
		<th>Date</th>
	</tr>
	<?php
		$n=1;
		$qbilltype=mysqli_query($link,"SELECT DISTINCT `bill_type_id` FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' order by bill_type_id");
		while($qbilltype1=mysqli_fetch_array($qbilltype))
		{
			if($qbilltype1['bill_type_id']==1)
			{
				$vbltype="Cash";
			}
			else
			{
				$vbltype="Credit";
			}
			?>
			<tr>
				<td colspan="11" style="font-weight:bold">Bill Type : <?php echo $vbltype;?></td>
			</tr>
			<?php
	$qry=mysqli_query($link,"SELECT DISTINCT `bill_no` FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' and bill_type_id='$qbilltype1[bill_type_id]'");
	while($res=mysqli_fetch_array($qry))
	{
		$q=mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_no`='$res[bill_no]'");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$cus=mysqli_fetch_array(mysqli_query($link,"SELECT `customer_name`,`total_amt` FROM `ph_sell_master` WHERE `bill_no`='$r[bill_no]'"));
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
	?>
	<tr>
		<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$r['bill_no']."</td><td rowspan='".$num."'>".$cus['customer_name']."</td>";}?>
		<td><?php echo $itm['item_name'];?></td>
		<td><?php echo $r['mrp'];?></td>
		<td><?php echo $r['sale_qnt'];?></td>
		<td><?php echo $r['total_amount'];?></td>
		<td><?php echo val_con(round($r['net_amount']-$r['total_amount']));?></td>
		<td><?php echo val_con(round($r['net_amount']));?></td>
		<?php if($num>0){echo "<td rowspan='".$num."'>".convert_date($r['entry_date'])."</td>";}?>
	</tr>
	<?php
		$num=0;
		}
		$n++;
	?>
	<tr>
		<td colspan="6"></td>
		<td colspan="2">Total</td>
		<td ><?php echo $cus['total_amt'];?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="11" style="background:#ccc;"></td>
	</tr>
	<?php
	}}
	?>
</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
