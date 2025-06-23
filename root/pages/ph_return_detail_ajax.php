<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

$rupees_symbol="&#x20b9; ";


function convert_date($date)
{
	if($date)
	{
	$timestamp = strtotime($date); 
	$new_date = date('d-M-y', $timestamp);
	return $new_date;
	}
}

if($_POST["type"]=="loadreturnsbill")
{
	$fdate=$_POST["fdate"];
	$tdate=$_POST["tdate"];
	$q=mysqli_query($link,"SELECT DISTINCT `bill_no` FROM `ph_item_return_master` WHERE return_date between '$fdate' and '$tdate' order by `bill_no`");
	$i=1;
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Bill date</th>
			<th>Customer</th>
			<th>Return Amount</th>
			<th>Print</th>
		</tr>
		<?php
		$i=1;
		$bill='';
		$dt='';
		while($r=mysqli_fetch_array($q))
		{
			$qq=mysqli_query($link,"SELECT DISTINCT `counter` FROM `ph_item_return_master` WHERE `bill_no`='$r[bill_no]' AND return_date between '$fdate' and '$tdate'");
			while($rr=mysqli_fetch_array($qq))
			{
				$ret=0;
				$vrtrnamt=0;
				$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_item_return_master` WHERE `bill_no`='$r[bill_no]' AND `counter`='$rr[counter]'"));			
				$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `discount_perchant`,`customer_name` FROM `ph_sell_master` WHERE `bill_no`='$r[bill_no]'"));
				$amt=mysqli_fetch_array(mysqli_query($link,"SELECT `amount` FROM `ph_item_return` WHERE `bill_no`='$r[bill_no]' AND `counter`='$rr[counter]'"));
				
				$qreturn=mysqli_query($link,"select * from ph_item_return_master where  bill_no='$r[bill_no]' AND counter='$rr[counter]'");
				while($qreturn1=mysqli_fetch_array($qreturn))
				{
					$qrate=mysqli_fetch_array(mysqli_query($link,"select a.mrp,b.item_name from ph_sell_details a,item_master b where a.item_code='$qreturn1[item_code]' and a.batch_no='$qreturn1[batch_no]' and a.item_code=b.item_id"));
					$vrtrnamt1=$qreturn1['return_qnt']*$qrate['mrp'];
					$vrtrnamt=$vrtrnamt+$vrtrnamt1;
				}
				$ret=$vrtrnamt;
				if($pat['discount_perchant']>0)
				{
					$disc=($ret*$pat['discount_perchant'])/100;
					$ret=$ret-$disc;
				}
				//$ret=floor($ret);
				$ret=($ret);
			?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $r['bill_no'];?></td>
				<td><?php echo convert_date($v['return_date']);?></td>
				<td><?php echo $pat['customer_name'];?></td>
				<td><?php echo number_format($ret,2);?></td>
				<td><button type="button" class="btn btn-info" onclick="rcv_rep_prr('<?php echo $r['bill_no'];?>','<?php echo $rr['counter'];?>')">Print</button></td>
			</tr>
			<?php
			$n=0;
			$i++;
			}
		}
		?>
	</table>
	<?php
}
?>
