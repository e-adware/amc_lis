<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}


$type=$_POST['type'];

if($type==1)
{
	?>
	<!--<button type="button" class="btn btn-default" onclick="stk_exp()">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="stk_prr()">Print</button>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><th>Available Stock</th><th>MRP</th><th>MRP Value</th><th>Cost Value</th>
		</tr>
	<?php
	$i=1;
	$q=mysqli_query($link,"SELECT a.*,b.item_name FROM `ph_stock_master` a,item_master b WHERE a.`quantity`>0 and a.item_code=b.item_id order by b.item_name");
	while($r=mysqli_fetch_array($q))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
		$qrate=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,recept_cost_price FROM `ph_purchase_receipt_details` WHERE `item_code`='$r[item_code]' and  recept_batch='$r[batch_no]'"));
		
		$vmrpvlue=0;
		$vcostvalue=0;
		$vmrpvlue=$qrate['recpt_mrp']*$r['quantity'];
		$vcostvalue=$qrate['recept_cost_price']*$r['quantity'];
		$vttlmrp=$vttlmrp+$vmrpvlue;
		$vttlcstprice=$vttlcstprice+$vcostvalue
	?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo $r['quantity'];?></td>
			<td><?php echo $qrate['recpt_mrp'];?></td>
			<td><?php echo number_format($vmrpvlue,2);?></td>
			<td><?php echo number_format($vcostvalue,2);?></td>
		</tr>
	<?php
	$i++;
	}
	?>
	<tr>
		<th colspan="6" style="text-align:right">Total</th>
		<th><?php echo number_format($vttlmrp,2);?></th>
		<th><?php echo number_format($vttlcstprice,2);?></th>
	</tr>
	</table>
	<?php
}

if($_POST["type"]==2)
{
	?>
	<!--<button type="button" class="btn btn-default" onclick="stk_exp()">Export to excel</button>-->
	<!--<button type="button" class="btn btn-default" onclick="stk_prr()">Print</button>-->
	<table class="table table-condensed table-bordered table-report" id="stk_tbl">
		<tr>
			<th>#</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><!--<th>MFG Date</th>--><th>Exp Date</th><th>Available Stock</th>
		</tr>
	<?php
	$i=1;
	$qry=mysqli_query($link,"SELECT DISTINCT `item_code` FROM `ph_stock_master` WHERE `quantity`<10");
	while($res=mysqli_fetch_array($qry))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$res[item_code]'"));
		$q=mysqli_query($link,"SELECT * FROM `ph_stock_master` WHERE `item_code`='$res[item_code]' AND `quantity`<10");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
	?>
		<tr>
			<?php
			if($num>0)
			{
				echo "<td rowspan='".$num."'>$i</td>";
				echo "<td rowspan='".$num."'>".$res['item_code']."</td>";
				echo "<td rowspan='".$num."'>".$itm['item_name']."</td>";
			}
			?>
			<td><?php echo $r['batch_no'];?></td>
			<!--<td><?php echo ($r['mfc_date']);?></td>-->
			<td><?php echo date("Y-m", strtotime($r['exp_date']));?></td>
			<td><?php echo $r['quantity'];?></td>
		</tr>
	<?php
		$num=0;
		}
		$i++;
	}
	?>
	</table>
	<style>
		.table tr:hover
		{
			background:none;
		}
	</style>
	<?php
}

if($_POST["type"]==3)
{
	$id=$_POST['id'];
	?>
	<button type="button" class="btn act_btn" onclick="print_ph_rcv_report()">Print</button>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th width="5%">#</th>
			<th>Issue Id</th>
			<th>Issue To</th>
			<th>Issue Date</th>
			<th style="text-align:right;">Amount</th>
			<th style="text-align:right;">GST Amount</th>
			<th style="text-align:right;">Net Amount</th>
			<th>User</th>
			<th class="no_print">View</th>
		</tr>
		<?php
		$j=1;
		$amt=0;
		$gst=0;
		$net=0;
		$q=mysqli_query($link,"SELECT * FROM `inv_substore_issue_master` WHERE `substore_id`='1' ORDER BY `slno` DESC LIMIT 0,10");
		while($r=mysqli_fetch_assoc($q))
		{
			$emp=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['issue_no'];?></td>
			<td><?php echo mysqli_real_escape_string($link,$r['issue_to']);?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td style="text-align:right;"><?php echo number_format($r['amount'],2);?></td>
			<td style="text-align:right;"><?php echo number_format($r['gst_amount'],2);?></td>
			<td style="text-align:right;"><?php echo number_format($r['amount']+$r['gst_amount'],2);?></td>
			<td><?php echo $emp['name'];?></td>
			<td class="no_print">
				<button type="button" class="btn btn-primary btn-mini" onclick="issue_report('<?php echo base64_encode($r['issue_no']);?>')">View</button>
			</td>
		</tr>
		<?php
		$amt+=$r['amount'];
		$gst+=$r['gst_amount'];
		$j++;
		}
		?>
		<tr>
			<td colspan="3"></td>
			<th>Total :</th>
			<th style="text-align:right;"><?php echo number_format($amt,2);?></th>
			<th style="text-align:right;"><?php echo number_format($gst,2);?></th>
			<th style="text-align:right;"><?php echo number_format($amt+$gst,2);?></th>
			<th></th>
			<th class="no_print"></th>
		</tr>
	</table>
	<?php
}

if($_POST["type"]==999)
{
	$id=$_POST['id'];
}
?>
