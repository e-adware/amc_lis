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

// Time format convert
function convert_time($time)
{
	$time = date("h:i A", strtotime($time));
	return $time;
}

if($_POST["type"]=="item_query_old")
{
	$itm=$_POST["itmid"];
	$itmid=explode("-#",$itm);
	
	$fdate=$_POST["fdate"];
	$tdate=$_POST["tdate"];
	
	
	
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Date</th>
			<th>Name</th>
			<th>Batch No</th>
			<th>Sale</th>
			<th>MRP</th>
			<th>Amount</th>
		</tr>
		<?php
		$i=1;
		
		$qry=mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `item_code`='$itmid[1]' and entry_date between '$fdate' and '$tdate' order by entry_date ");
		while($r=mysqli_fetch_array($qry))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$itmid[1]'"));
			$vttlsale+=$r["sale_qnt"];
			$vttalamt+=$r["total_amount"];
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r["bill_no"];?></td>
			<td><?php echo convert_date($r["entry_date"]);?></td>
			<td><?php echo $itm["item_name"];?></td>
			<td><?php echo $r["batch_no"];?></td>
			<td><?php echo $r["sale_qnt"];?></td>
			<td><?php echo $r["mrp"];?></td>
			<td><?php echo $r["total_amount"];?></td>
		</tr>
		<?php
		$i++;
		}
		?>
		<tr>
			<td colspan="5" style="font-weight:bold"> Total </td>
			<td style="font-weight:bold"><?php echo $vttlsale;?> </td>
			<td>&nbsp;</td>
			<td style="font-weight:bold"><?php echo number_format($vttalamt,2);?> </td>
			
		</tr>
	</table>
	<?php
}

if($_POST["type"]=="item_query")
{
	$fdate=$_POST["fdate"];
	$tdate=$_POST["tdate"];
	$itm=$_POST["itm"];
	?>
	<table class="table table-condensed" style="background:#FFF;">
		<thead class="table_header_fix">
		<tr>
			<th>#</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th>Opening</th>
			<th>Add</th>
			<th>Deduct</th>
			<th>Closing</th>
			<th>Process</th>
			<th>Date/Time</th>
			<th>User</th>
		</tr>
		</thead>
		<tbody>
	<?php
	$j=1;
	$qry=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `ph_item_process` WHERE `substore_id`='1' AND `item_id`='$itm' AND `date` BETWEEN '$fdate' AND '$tdate'");
	while($rr=mysqli_fetch_assoc($qry))
	{
	$exp_date=mysqli_fetch_assoc(mysqli_query($link,"SELECT `exp_date` FROM `ph_stock_master` WHERE `substore_id`='1' AND `item_code`='$itm' AND `batch_no`='$rr[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
	$q=mysqli_query($link,"SELECT * FROM `ph_item_process` WHERE `substore_id`='1' AND `item_id`='$itm' AND `batch_no`='$rr[batch_no]' AND `date` BETWEEN '$fdate' AND '$tdate'");
	while($r=mysqli_fetch_assoc($q))
	{
		$process="";
		$added=0;
		$deduct=0;
		if($r['process_type']=="1")
		{
			$process="Purchase <small>(".$r['process_no'].")</small>";
			$added=$r['qnt'];
			$deduct=0;
		}
		else if($r['process_type']=="2")
		{
			$process="Sale <small>(".$r['process_no'].")</small>";
			$added=0;
			$deduct=$r['qnt'];
		}
		else if($r['process_type']=="3")
		{
			$process="Sale Return <small>(".$r['process_no'].")</small>";
			$added=$r['qnt'];
			$deduct=0;
		}
		else if($r['process_type']=="4")
		{
			$process="Supplier Return <small>(".$r['process_no'].")</small>";
			$added=0;
			$deduct=$r['qnt'];
		}
		else if($r['process_type']=="5")
		{
			$process="Stock Add";
			$added=$r['qnt'];
			$deduct=0;
		}
		else if($r['process_type']=="6")
		{
			$process="Stock Deduct";
			$added=0;
			$deduct=$r['qnt'];
		}
		else if($r['process_type']=="7")
		{
			$process="Store Issue <small>(".$r['process_no'].")</small>";
			$added=$r['qnt'];
			$deduct=0;
		}
		else if($r['process_type']=="8")
		{
			$process="Store Return <small>(".$r['process_no'].")</small>";
			$added=0;
			$deduct=$r['qnt'];
		}
		else if($r['process_type']=="9")
		{
			$process="Bill Edit <small>(".$r['process_no'].")</small>";
			$added=$r['qnt'];
			$deduct=0;
		}
		else if($r['process_type']=="10")
		{
			$process="Bill Edit <small>(".$r['process_no'].")</small>";
			$added=0;
			$deduct=$r['qnt'];
		}
		else if($r['process_type']=="11")
		{
			$process="Bill Edit <small>(".$r['process_no'].")</small>";
			$added=$r['qnt'];
			$deduct=0;
		}
		else if($r['process_type']=="12")
		{
			$process="Bill Edit <small>(".$r['process_no'].")</small>";
			$added=0;
			$deduct=$r['qnt'];
		}
		if($added>0)
		{
			$color="#073D00";
		}
		else
		{
			$color="#AA1500";
		}
		$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		?>
		<tr style="color:<?php echo $color;?>;">
			<td><?php echo $j;?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo date("Y-m", strtotime($exp_date['exp_date']));?></td>
			<td><?php echo $r['opening'];?></td>
			<td><?php echo $added;?></td>
			<td><?php echo $deduct;?></td>
			<td><?php echo $r['closing'];?></td>
			<td><?php echo $process;?></td>
			<td><?php echo convert_date($r['date'])." / ".convert_time($r['time']);?></td>
			<td><?php echo $usr['name'];?></td>
		</tr>
		<?php
		$j++;
	}
	?>
		<tr>
			<td colspan="10" style="background:#888;"></td>
		</tr>
	<?php
	}
	?>
		</tbody>
	</table>
	<?php
}
?>
