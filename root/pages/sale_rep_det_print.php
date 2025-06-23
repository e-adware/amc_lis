<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<?php
include('../../includes/connection.php');
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
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));
?>

<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Cash Memo</u></h5></center>
				
			</div>
	

<table>

<tr><td colspan="5" style="font-weight:bold;font-size:13px">From : <?php echo convert_date($fdate);?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">To &nbsp;&nbsp;&nbsp;&nbsp; : <?php echo convert_date($tdate);?></td></tr>
</table>

<table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" class="btn btn-default" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" class="btn btn-success" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
<table class="table table-condensed table-bordered">
	
	 <tr bgcolor="#EAEAEA" class="bline" >
		<td style="font-weight:bold;font-size:12px">#</td>
		<td style="font-weight:bold;font-size:12px">Bill No</td>
		<td style="font-weight:bold;font-size:12px">Customer Name</td>
		<td style="font-weight:bold;font-size:12px">Item Details</td>
		<td style="font-weight:bold;font-size:12px">Rate</td>
		<td style="font-weight:bold;font-size:12px">Quantity</td>
		<td style="font-weight:bold;font-size:12px">Amount</td>
		<td style="font-weight:bold;font-size:12px">Cost Price</td>
		<td style="font-weight:bold;font-size:12px">GST (Rs)</td>
		<td style="font-weight:bold;font-size:12px">Net Amount(Round)</td>
		<td style="font-weight:bold;font-size:12px">Date</td>
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
				<td colspan="11" style="font-weight:bold;font-size:12px">Bill Type : <?php echo $vbltype;?></td>
			</tr>
			<?php
				
	$qry=mysqli_query($link,"SELECT DISTINCT `bill_no` FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' and bill_type_id='$qbilltype1[bill_type_id]'");
	while($res=mysqli_fetch_array($qry))
	{
		
		$q=mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_no`='$res[bill_no]'");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$vcsrprice=$r['sale_qnt']*$r['item_cost_price'];
			$cus=mysqli_fetch_array(mysqli_query($link,"SELECT `customer_name`,`total_amt` FROM `ph_sell_master` WHERE `bill_no`='$r[bill_no]'"));
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
	?>
	<tr>
		<?php if($num>0){echo "<td style='font-size:11px' rowspan='".$num."'>".$n."</td><td style='font-size:11px' rowspan='".$num."'>".$r['bill_no']."</td><td style='font-size:11px' rowspan='".$num."'>".$cus['customer_name']."</td>";}?>
		<td style="font-size:11px"><?php echo $itm['item_name'];?></td>
		<td style="font-size:11px"><?php echo $r['mrp'];?></td>
		<td style="font-size:11px"><?php echo $r['sale_qnt'];?></td>
		<td style="font-size:11px"><?php echo $r['total_amount'];?></td>
		<td style="font-size:11px"><?php echo $vcsrprice;?></td>
		<td style="font-size:11px"><?php echo val_con($r['gst_amount']);?></td>
		<td style="font-size:11px"><?php echo val_con(round($r['net_amount']));?></td>
		<?php if($num>0){echo "<td  style='font-size:11px' rowspan='".$num."'>".convert_date($r['entry_date'])."</td>";}?>
	</tr>
	<?php
		$num=0;
		}
		$n++;
	?>
	<tr>
		<td colspan="7"></td>
		<td colspan="2" style="text-align:right;font-size:12px">Total</td>
		<td style="font-size:12px"><?php echo $cus['total_amt'];?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="11"></td>
	</tr>
	<?php
	}}
	?>
</table>
</div>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<script>
<!--window.print();-->
</script>
