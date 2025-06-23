<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<?php
include('../../includes/connection.php');
$ipdno=$_GET['ipdno'];
$panme=$_GET['panme'];
$opdno=$_GET['opdno'];

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-Y', $timestamp);
		return $new_date;
	}
}


$vtype="";
if($ipdno!="")
{
	$vpno=$ipdno;
	$vtype="IPD NO";
}
elseif($opdno!="")
{
	$vpno=$opdno;
	$vtype="OPD NO";
	
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
$qname=mysqli_fetch_array(mysqli_query($link,"select a.name,b.patient_id from patient_info a,uhid_and_opdid b where a.patient_id=b.patient_id and b.opd_id='$vpno'  "));

if($panme!="")
{
	$vpname=$panme;
}
else
{
	$vpname=$qname['name'];
}
?>

<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Detail Bill</u></h5></center>
				
			</div>
	

<table>

<tr><td colspan="5" style="font-weight:bold;font-size:13px"><?php echo $vtype;?> : <?php echo $vpno;?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Name &nbsp;&nbsp; :  <?php echo $vpname;?></td></tr>
</table>

<table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" class="btn btn-default" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" class="btn btn-success" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
<table class="table table-condensed table-bordered">
	
	 <tr bgcolor="#EAEAEA" class="bline" >
		<td style="font-weight:bold;font-size:13px">Sl No</td>
		<td style="font-weight:bold;font-size:13px">Bill No</td>
		<td style="font-weight:bold;font-size:13px">Customer Name</td>
		<td style="font-weight:bold;font-size:13px">Item Details</td>
		<td style="font-weight:bold;font-size:13px">MRP</td>
		<td style="font-weight:bold;font-size:13px">Quantity</td>
		<td style="font-weight:bold;font-size:13px">Return Qnty</td>
		<td style="font-weight:bold;font-size:13px"> Amount(Round)</td>
		<td style="font-weight:bold;font-size:13px">Paid</td>
		<td style="font-weight:bold;font-size:13px">Balance</td>
		<td style="font-weight:bold;font-size:13px">Date</td>
	</tr>
	
			<?php
				$n=1;
				$vttlbal=0;
				
	//$qry=mysqli_query($link,"SELECT distinct bill_no FROM `ph_sell_master` WHERE `ipd_id`='$ipdno'  ");
	if($ipdno!="")	
		{
			$qry=mysqli_query($link,"SELECT distinct bill_no FROM `ph_sell_master` WHERE `ipd_id`='$ipdno'  ");
		}
		elseif($opdno!="")
		{
			$qry=mysqli_query($link,"SELECT distinct bill_no FROM `ph_sell_master` WHERE `opd_id`='$opdno'  ");
		}
		else
		{
			$qry=mysqli_query($link,"SELECT distinct bill_no FROM `ph_sell_master` WHERE `customer_name`='$panme'  ");
		}
		
		
		
	while($res=mysqli_fetch_array($qry))
	{
		$vbal=0;
		$qreturnamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxrtrn from `ph_item_return_master` where `bill_no`='$res[bill_no]'  "));
		$vrtrnamt1=0;
		$vrtrnamt1+=round($qreturnamt['maxrtrn']);
		$vrtrnamt+=$vrtrnamt1;
		
		$cus=mysqli_fetch_array(mysqli_query($link,"SELECT `customer_name`,`total_amt`,paid_amt,balance FROM `ph_sell_master` WHERE `bill_no`='$res[bill_no]'"));
		if($cus['balance']>0)
			{
				$vbal=$cus['balance'];
				$vttlbal=$vttlbal+$cus['balance'];
			}
		$q=mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_no`='$res[bill_no]'");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$vcsrprice=$r['sale_qnt']*$r['item_cost_price'];
			$qreturn=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(return_qnt),0) as maxrtrnqnt from `ph_item_return_master` where `bill_no`='$res[bill_no]' and `item_code`='$r[item_code]' and `batch_no`='$r[batch_no]' "));
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
	?>
	<tr>
		<?php if($num>0){echo "<td style='font-size:12px' rowspan='".$num."'>".$n."</td><td style='font-size:13px' rowspan='".$num."'>".$r['bill_no']."</td><td style='font-size:13px' rowspan='".$num."'>".$cus['customer_name']."</td>";}?>
		<td style="font-size:12px"><?php echo $itm['item_name'];?></td>
		<td style="font-size:12px"><?php echo $r['mrp'];?></td>
		<td style="font-size:12px"><?php echo $r['sale_qnt'];?></td>
		<td style="font-size:12px"><?php echo $qreturn['maxrtrnqnt'];?></td>
		
		
		<td style="font-size:12px"><?php echo val_con(round($r['total_amount']));?></td>
		<td style="font-size:12px">&nbsp;</td>
		<td style="font-size:12px">&nbsp;</td>
		<?php if($num>0){echo "<td  style='font-size:13px' rowspan='".$num."'>".convert_date($r['entry_date'])."</td>";}?>
	</tr>
	<?php
		$num=0;
		}
		$n++;
	?>
	<tr>
		<?php
		 if($cus['total_amt']==$cus['paid_amt'])
		 {
			 $vbillpaid=$cus['paid_amt']-$vrtrnamt1;
		 }
		 else
		 {
			 $vbillpaid=$cus['paid_amt'];
		 }
		 //$vbillpaid=$cus['total_amt']-$vrtrnamt1-$vbal;
		if($qreturnamt['maxrtrn']>0)
		{
			echo "<td colspan='5' style='text-align:right;font-weight:bold;font-size:12px'>Return Amount :$vrtrnamt1</td>";
		}
		else
		{
		?>
		<td colspan="5"></td>
		<?php
	     }?>
	     
		
		<td colspan="2" style="text-align:right;font-weight:bold;font-size:12px">Total</td>
		<td style="font-weight:bold;font-size:12px"><?php echo $cus['total_amt'];?></td>
		<td style="font-weight:bold;font-size:12px"><?php echo number_format($vbillpaid,2);?></td>
		<td style="font-weight:bold;font-size:12px"><?php echo number_format($vbal,2);?></td>
		<td style="font-weight:bold"></td>
	</tr>
	<tr>
		<td colspan="11"></td>
	</tr>
	<?php
	 $grndttl=$grndttl+$cus['total_amt'];
	  $paidttl=$paidttl+$vbillpaid;
	}
	?>
	
	<tr>
		    <td colspan="5"></td>
			<td colspan="2" style="font-weight:bold;font-size:12px">Total Return</td>
			<td style="font-weight:bold">&nbsp;</td>
			<td style="font-weight:bold">&nbsp;</td>
			<td colspan="2" style="font-weight:bold;font-size:12px"><?php echo number_format($vrtrnamt,2);?></td>
			
			
	</tr>	
	
	<tr>
		    <td colspan="5"></td>
			<td colspan="2" style="font-weight:bold;font-size:13px">Grand Total</td>
			<td style="font-weight:bold;font-size:13px"><?php echo number_format($grndttl,2);?></td>
			<td style="font-weight:bold;font-size:13px"><?php echo number_format($paidttl,2);?></td>
			<td style="font-weight:bold;font-size:13px"><?php echo number_format($vttlbal,2);?></td>
			<td style="font-weight:bold">&nbsp;</td>
	</tr>
	
	
</table>
</div>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<script>
<!--window.print();-->
</script>
