<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date('H:i:s');

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-Y', $timestamp);
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
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$ph=1;
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-primary act_btn" onclick="sale_rep_prr('<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $ph;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Type</th>
			<th>Customer Name</th>
			<th style="text-align:right;">Amount</th>
			<th style="text-align:right;">Discount</th>
			<th style="text-align:right;">Adjust. </th>
			<th style="text-align:right;">Paid</th>
			<th style="text-align:right;">Balance</th>
			<th>Date</th>
			<th>User</th>
		</tr>
	<?php
	$qry=mysqli_query($link,"SELECT DISTINCT `patient_type` FROM `ph_sell_master` WHERE `patient_type`>0 AND `entry_date` BETWEEN '$fdate' AND '$tdate' ORDER BY `patient_type`");
	while($res=mysqli_fetch_assoc($qry))
	{
		if($res['patient_type']=="1")
		{
			$patient_type="General";
		}
		if($res['patient_type']=="2")
		{
			$patient_type="ESI";
		}
		if($res['patient_type']=="3")
		{
			$patient_type="In House";
		}
		if($res['patient_type']=="4")
		{
			$patient_type="Ayushman";
		}
		if($res['patient_type']=="5")
		{
			$patient_type="Staff";
		}
		if($res['patient_type']=="6")
		{
			$patient_type="Donor";
		}
		?>
		<tr>
			<th colspan="11"><?php echo $patient_type;?></th>
		</tr>
		<?php
		$n=1;
		$p_tot=0;
		$vttl=0;
		$vpaid=0;
		$vdis=0;
		$vadjsut=0;
		$vbal=0;
		$q=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' AND `patient_type`='$res[patient_type]'");
		while($r=mysqli_fetch_array($q))
		{
			$qemp=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			//$qtype=mysqli_fetch_array(mysqli_query($link,"select sell_name from ph_sell_type where sell_id='$r[pat_type]'"));
			$rrr=mysqli_fetch_array(mysqli_query($link,"SELECT sum(`total_amount`) as ttt FROM `ph_sell_details` WHERE `bill_no`='$r[bill_no]' "));
			$vttl=$vttl+$r['total_amt'];
			$vpaid=$vpaid+$r['paid_amt'];
			$vdis=$vdis+$r['discount_amt'];
			$vadjsut=$vadjsut+$r['adjust_amt'];
			$vbal=$vbal+$r['balance'];
			$vipd="";
			if($r['ipd_id']!=="")
			{
				$vipd=" (".$r['ipd_id'].")";
			}
			
			if($r['bill_type_id']=="1")
			{
				$bill_typ="Cash";
			}
			if($r['bill_type_id']=="2")
			{
				$bill_typ="Credit";
			}
			if($r['bill_type_id']=="3")
			{
				$bill_typ="";
			}
			if($r['bill_type_id']=="4")
			{
				$bill_typ="Card";
			}
			
			
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo $bill_typ;?></td>
			<td>
				<?php echo $r['customer_name'].$vipd;?>
				<span style="float:right;"><i class="icon-edit icon-large text-success" onclick="pat_bill_edit('<?php echo $r['bill_no']?>')"></i></span>
			</td>
			<td style="text-align:right;"><?php echo $r['total_amt'];?></td>
			<td style="text-align:right;"><?php echo ($r['discount_amt']);?></td>
			<td style="text-align:right;"><?php echo ($r['adjust_amt']);?></td>
			<td style="text-align:right;"><?php echo ($r['paid_amt']);?></td>
			<td style="text-align:right;"><?php echo $r['balance'];?></td>
			<td><?php echo convert_date($r['entry_date']);?></td>
			<td><?php echo $qemp['name'];?></td>
		</tr>
		<?php
		$p_tot+=$rrr['ttt'];
		$n++;
		}
		?>
		<tr>
			<th colspan="4" style="text-align:right">Total <?php //echo $p_tot;?></th>
			<th style="text-align:right;"><?php echo number_format($vttl,2);?></th>
			<th style="text-align:right;"><?php echo number_format($vdis,2);?></th>
			<th style="text-align:right;"><?php echo number_format($vadjsut,2);?></th>
			<th style="text-align:right;"><?php echo number_format($vpaid,2);?></th>
			<th style="text-align:right;"><?php echo number_format($vbal,2);?></th>
			<th></th>
			<th></th>
		</tr>
		<?php
	}
	?>
	</table>
	<style>
		.icon-edit
		{
			cursor:pointer;
		}
		.icon-edit:hover
		{
			box-shadow: 0px 0px 10px 0px #999999;
		}
	</style>
	<?php
} // 1

if($type==2)
{
	$bill=$_POST['bill'];
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `bill_no`='$bill'"));
	$dis_per=explode(".",$det['discount_perchant']);
	$dis_per=$dis_per[0];
	?>
	<input type="hidden" id="upd_bl_no" value="<?php echo $bill;?>" />
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Bill No</th>
			<th>Name</th>
			<th>Bill Type</th>
			<th>Bill Amount</th>
			<th>Discount %</th>
			<th>Adjust</th>
			<th>Paid</th>
			<th>Balance</th>
			<th>Patient Type</th>
			<th>Bill Date</th>
		</tr>
		<tr>
			<th><?php echo $bill;?></th>
			<td>
				<input type="text" class="span3" id="pat_name" value="<?php echo $det['customer_name'];?>" />
			</td>
			<td>
				<select style="width:100px;" id="bl_type">
					<option value="1" <?php if($det['bill_type_id']=="1"){echo "selected='selected'";}?>>Cash</option>
					<option value="2" <?php if($det['bill_type_id']=="2"){echo "selected='selected'";}?>>Credit</option>
					<!--<option value="3">ESI</option>-->
					<option value="4" <?php if($det['bill_type_id']=="4"){echo "selected='selected'";}?>>Card</option>
				</select>
			</td>
			<td>
				<input type="text" class="span1" id="bill_amt" value="<?php echo $det['total_amt'];?>" readonly />
			</td>
			<td>
				<input type="text" class="span1" id="dis_per" maxlength="3" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');calc_disc(this.value)" value="<?php echo $dis_per;?>" />
				<input type="hidden" class="span1" id="dis_amt" value="<?php echo $det['discount_amt'];?>" readonly />
			</td>
			<td>
				<input type="text" class="span1" id="adj_amt" onkeyup="chk_dec(this,event);calc_adj(this.value)" value="<?php echo $det['adjust_amt'];?>" />
			</td>
			<td>
				<input type="text" class="span1" id="pay_amt" onkeyup="chk_dec(this,event);calc_paid(this.value)" value="<?php echo $det['paid_amt'];?>" />
			</td>
			<td>
				<input type="text" class="span1" id="bal_amt" onkeyup="" value="<?php echo $det['balance'];?>" readonly />
			</td>
			<td>
				<select style="width:100px;" id="pat_type">
					<option value="1" <?php if($det['patient_type']=="1"){echo "selected='selected'";}?>>General</option>
					<option value="2" <?php if($det['patient_type']=="2"){echo "selected='selected'";}?>>ESIC</option>
					<option value="3" <?php if($det['patient_type']=="3"){echo "selected='selected'";}?>>In House</option>
					<option value="4" <?php if($det['patient_type']=="4"){echo "selected='selected'";}?>>Ayushman</option>
					<option value="5" <?php if($det['patient_type']=="5"){echo "selected='selected'";}?>>Staff</option>
					<option value="6" <?php if($det['patient_type']=="6"){echo "selected='selected'";}?>>Donor</option>
				</select>
			</td>
			<td>
				<input type="text" class="span2" id="bill_date" value="<?php echo $det['entry_date'];?>" readonly />
			</td>
		</tr>
		<tr>
			<td colspan="10" style="text-align:center;">
				<button type="button" id="sav" class="btn btn-primary" onclick="pat_bill_update()">Save</button>
				<button type="button" id="can" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Cancel</button>
			</td>
		</tr>
	</table>
	<script>
		$("#bill_date").datepicker({dateFormat: 'yy-mm-dd',maxDate:'0'});
		$("#pat_name").keyup(function()
		{
			$("#pat_name").val($("#pat_name").val().toUpperCase());
		});
		setTimeout(function()
		{
			$("#pat_name").focus();
		},500);
	</script>
	<?php
} // 2

if($type==3)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_det_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="sale_rep_det_prr('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Customer Name</th>
			<th>Item Details</th>
			<th>Rate</th>
			<th>Quantity</th>
			<th>MRP</th>
			<th>Cost Price</th>
			<th>GST (Rs)</th>
			<th>Net Amount(Round)</th>
			<th>Date</th>
		</tr>
		<?php
		$n=1;
		$qbilltype=mysqli_query($link,"SELECT DISTINCT `bill_type_id` FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' order by bill_type_id");
		while($qbilltype1=mysqli_fetch_array($qbilltype))
		{
			$vbltype="";
			if($qbilltype1['bill_type_id']=="1")
			{
				$vbltype="Cash";
			}
			if($qbilltype1['bill_type_id']=="2")
			{
				$vbltype="Credit";
			}
			if($qbilltype1['bill_type_id']=="3")
			{
				$vbltype="";
			}
			if($qbilltype1['bill_type_id']=="4")
			{
				$vbltype="Card";
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
				$vcsrprice=$r['sale_qnt']*$r['item_cost_price'];
				$cus=mysqli_fetch_array(mysqli_query($link,"SELECT `customer_name`,`total_amt` FROM `ph_sell_master` WHERE `bill_no`='$r[bill_no]'"));
				//$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
				$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
		?>
		<tr>
			<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$r['bill_no']."</td><td rowspan='".$num."'>".$cus['customer_name']."</td>";}?>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['mrp'];?></td>
			<td><?php echo $r['sale_qnt'];?></td>
			<td><?php echo $r['total_amount'];?></td>
			<td><?php echo $vcsrprice;?></td>
			<td><?php echo number_format($r['gst_amount'],2);?></td>
			<td><?php echo number_format($r['net_amount'],2);?></td>
			<?php if($num>0){echo "<td rowspan='".$num."'>".convert_date($r['entry_date'])."</td>";}?>
		</tr>
		<?php
			$num=0;
			}
			$n++;
		?>
		<tr>
			<td colspan="7"></td>
			<td colspan="2">Total</td>
			<td colspan="2"><?php echo $cus['total_amt'];?></td>
		</tr>
		<tr>
			<td colspan="11" style="background:#ccc;"></td>
		</tr>
		<?php
		}
		?>
		
		<?php
	}?>
	</table>
	<?php
} // 3

if($type==4)
{
$fdate=$_POST['fdate'];
$tdate=$_POST['tdate'];
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_item_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" id="print_btn" class="btn btn-default" onclick="sale_item_rep_prr('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Sale</th>
			<!--<th>Pharmacy Stock</th>-->
			<!--<th>Central Stock</th>-->
			<th>Current Stock</th>
		</tr>
		<?php
		$n=1;
		$q=mysqli_query($link,"SELECT DISTINCT a.`item_code`,b.item_name  FROM `ph_sell_details` a,item_master b WHERE a.entry_date  BETWEEN '$fdate' AND '$tdate' and a.item_code=b.item_id and a.`sale_qnt` > '0' order by b.item_name");
		while($r=mysqli_fetch_array($q))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			$bch=mysqli_fetch_array(mysqli_query($link,"SELECT `batch_no` FROM `ph_stock_process` WHERE `item_code`='$r[item_code]'"));
			$add=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(added),0)as adds FROM `ph_stock_process` WHERE `item_code`='$r[item_code]'"));
			
			$sell=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(sale_qnt),0)as sells,ifnull(sum(free_qnt),0)as maxfree FROM `ph_sell_details` WHERE `item_code`='$r[item_code]' and entry_date between '$fdate' AND '$tdate'"));
			
			$ret=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`return_qnt`),0) AS `qnt` FROM `ph_item_return_master` WHERE `item_code`='$r[item_code]' AND `return_date` BETWEEN '$fdate' AND '$tdate'"));
			
			$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(`quantity`),0) as maxph FROM `ph_stock_master` WHERE `item_code`='$r[item_code]' and substore_id='1' and quantity>0 "));
			
			//$mainstk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(`closing_stock`),0) as maxcntrl FROM `inv_maincurrent_stock` WHERE `item_id`='$r[item_code]' and  closing_stock>0 "));
			
			$vsalqnt=$sell['sells']+$sell['maxfree']-$ret['qnt'];
			$vttlstk=$stk['maxph']+$mainstk['maxcntrl'];
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $vsalqnt;?></td>
			<!--<td><?php echo $stk['maxph'];?></td>-->
			<!--<td><?php echo $mainstk['maxcntrl'];?></td>-->
			<td><?php echo $vttlstk;?></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
} // 4

if($type==5)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$ph=1;
	?>
	<!--<button type="button" class="btn btn-default" onclick="ret_dis_exl_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default btn_act" onclick="ret_dis_prr('<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $ph;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Customer Name</th>
			<th style="text-align:right;">Amount</th>
			<th style="text-align:right;">Discount</th>
			<th style="text-align:right;">Adjust. </th>
			<th style="text-align:right;">Paid</th>
			<th style="text-align:right;">Balance</th>
			<th>Date</th>
	   </tr>		
	<?php
	$i=1;
	$amount=0;
	$discount=0;
	$adjust=0;
	$paid=0;
	$balance=0;
	$qbilno=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' and discount_amt>0 ");
	while($qbilno1=mysqli_fetch_array($qbilno))
	{
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $qbilno1['bill_no'];?></td>
			<td><?php echo $qbilno1['customer_name'];?></td>
			<td style="text-align:right;"><?php echo $qbilno1['total_amt'];?></td>
			<td style="text-align:right;"><?php echo $qbilno1['discount_amt'];?></td>
			<td style="text-align:right;"><?php echo $qbilno1['adjust_amt'];?></td>
			<td style="text-align:right;"><?php echo $qbilno1['paid_amt'];?></td>
			<td style="text-align:right;"><?php echo $qbilno1['balance'];?></td>
			<td><?php echo convert_date($qbilno1['entry_date']);?></td>
		</tr>
		<?php
		$i++;
		$amount+=$qbilno1['total_amt'];
		$discount+=$qbilno1['discount_amt'];
		$adjust+=$qbilno1['adjust_amt'];
		$paid+=$qbilno1['paid_amt'];
		$balance=$qbilno1['balance'];
	}
	?>
    <tr>
	  <td colspan="3" style="font-weight:bold;font-size:13px;text-align:right">Total</td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($amount,2);?></td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($discount,2);?></td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($adjust,2);?></td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($paid,2);?></td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($balance,2);?></td>
	  <td>&nbsp;</td>
	  
  </tr> 
	</table>
	<?php
} // 5

if($type==6)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<!--<button type="button" class="btn btn-default" onclick="ret_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="ret_rep_prr('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Date</th>
			<th>Return.Quantity</th>
			<th>MRP</th>
			<th style='text-align:right'>Amount</th>
			<th>User</th>
		</tr>
	<?php
	$qbilno=mysqli_query($link,"select distinct bill_no  FROM ph_item_return_master WHERE `return_date` BETWEEN '$fdate' AND '$tdate'");
	while($qbilno1=mysqli_fetch_array($qbilno))
	{
	?>
	  <tr>
			<td colspan="9" style="font-weight:bold">Bill No : <?php echo $qbilno1['bill_no'];?></td>
			
		</tr>
	<?php	
	$n=1;
	$qry=mysqli_query($link,"SELECT  distinct `item_code` FROM `ph_item_return_master` WHERE  bill_no='$qbilno1[bill_no]' and return_date BETWEEN '$fdate' AND '$tdate'");
	$sum=0;
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `discount_perchant`,`customer_name` FROM `ph_sell_master` WHERE `bill_no`='$qbilno1[bill_no]'"));
	while($res=mysqli_fetch_array($qry))
	{
		$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res[user]'"));
		$q=mysqli_query($link,"SELECT * FROM `ph_item_return_master` WHERE bill_no='$qbilno1[bill_no]' and `item_code`='$res[item_code]' AND `return_date` BETWEEN '$fdate' AND '$tdate'");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			
			$qmrm=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from ph_purchase_receipt_details where item_code='$r[item_code]' and recept_batch='$r[batch_no]'"));
			if($qmrm)
			{
				//$qmrm['recpt_mrp'];
			}
			else
			{
				$qmrm=mysqli_fetch_array(mysqli_query($link,"select mrp from ph_sell_details where bill_no='$qbilno1[bill_no]' and item_code='$r[item_code]' and batch_no='$r[batch_no]'"));
				$qmrm['recpt_mrp']=$qmrm['mrp'];
			}
			$vmrp1=0;
			$vmrp1=$r['return_qnt']*$qmrm['recpt_mrp'];
			//$vttlcstmrrtrn=$vttlcstmrrtrn+$vmrp1;
			$sum+=$vmrp1;
		?>
		<tr>
			<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$r['item_code']."</td><td rowspan='".$num."'>".$itm['item_name']."</td><td rowspan='".$num."'>".$r['batch_no']."</td>";}?>
			<td><?php echo convert_date($r['return_date']);?></td>
			<td><?php echo $r['return_qnt'];?></td>
			<td><?php echo $qmrm['recpt_mrp'];?></td>
			<td style='text-align:right'><?php echo number_format($vmrp1,2);?></td>
			<td><?php echo $quser['name'];?></td>
		</tr>
		<?php
		$num=0;
		}
	$n++;
	}
	$disc=0;
	if($pat['discount_perchant']>0)
	{
		$disc=($sum*$pat['discount_perchant'])/100;
		$sum=$sum-$disc;
	}
	$sum=floor($sum);
	$vttlcstmrrtrn+=$sum;
	?>
	<tr>
		<td colspan="3" style="font-weight:bold;font-size:13px;text-align:right">Discount %</td>
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo $pat['discount_perchant'];?></td>
		<td colspan="3" style="font-weight:bold;font-size:13px;text-align:right">Return Amount</td>
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($sum,2);?></td>
		<td></td>
	</tr>
	<?php
  }?>
  
    <tr>
	  <td colspan="7" style="font-weight:bold;font-size:13px;text-align:right">Total Return</td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttlcstmrrtrn,2);?></td>
	  <td>&nbsp;</td>
	  
  </tr> 
	</table>
	<?php
} // 6

if($type==7)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default act_btn" onclick="sale_rep_credit('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Customer Name</th>
			<th>Amount</th>
			<th>Discount (Rs)</th>
			<th>Paid</th>
			<th>Balance</th>
			<th>Date</th>
		</tr>
		<?php
		
		//$q1=mysqli_query($link,"select distinct a.pat_type,b.sell_name from ph_sell_master a, ph_sell_type b where a.pat_type=b.sell_id and a.`entry_date` BETWEEN '$fdate' AND '$tdate' and a.balance>0 ");
		//~ $q1=mysqli_query($link,"select * from ph_sell_master where `entry_date` BETWEEN '$fdate' AND '$tdate' and balance>0 ");
		//~ while($q2=mysqli_fetch_array($q1))
		{
			$n=1;
		 ?>
		 <!--<tr>
			<td colspan="8"></td>
		 </tr>-->
		 <?php	
		$q=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' and balance>0 ");
		while($r=mysqli_fetch_array($q))
		{
			$vttl=$vttl+$r['total_amt'];
			  $vttldis=$vttldis+$r['discount_amt'];
			  $vttlpaid=$vttlpaid+$r['paid_amt'];
			  $vttlbl=$vttlbl+$r['balance'];
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo $r['customer_name'];?></td>
			<td><?php echo $r['total_amt'];?></td>
			<td><?php echo number_format($r['discount_amt'],2);?></td>
			<td><?php echo number_format($r['paid_amt'],2);?></td>
			<td><?php echo $r['balance'];?></td>
			<td><?php echo convert_date($r['entry_date']);?></td>
		</tr>
		<?php
		$n++;
		}
		?>
		 <tr >
					<td align="right" colspan="3" style="font-weight:bold;font-size:13px">Total</td>
					<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttl,2);?></td>
					<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttldis,2);?></td>
					<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttlpaid,2);?></td>
					<td align="right" style="font-weight:bold;font-size:13px"><?php echo number_format($vttlbl,2);?></td>
					<td></td>
             </tr>
		<?php
	  }?>
	</table>
	<?php
} // 7

if($type==8)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$fid=0;
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default act_btn" onclick="sale_rep_costprice('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th style="text-align:right;">Qnty.</th>
			<th style="text-align:right;">MRP</th>
			<th style="text-align:right;">Cost Price</th>
			<th style="text-align:right;">MRP Amount</th>
			<th style="text-align:right;">Cost Amount</th>
			<th style="text-align:right;">Profit</th>
		</tr>
		<?php
		$n=1;
		
		$q=mysqli_query($link,"select distinct a.item_code,b.item_name from ph_sell_details a,item_master b where a.item_code=b.item_id and a.entry_date between'$fdate' and '$tdate'   order by  b.item_name");
		
		while($r=mysqli_fetch_array($q))
		{
			$itmqnt=mysqli_fetch_array(mysqli_query($link,"select sum(sale_qnt)as maxqnt,sum(free_qnt)as maxfree from ph_sell_details where entry_date between'$fdate' and '$tdate' and item_code='$r[item_code]' "));
			
			$ret=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`return_qnt`),0) AS `qnt` FROM `ph_item_return_master` WHERE `item_code`='$r[item_code]' AND `return_date` BETWEEN '$fdate' AND '$tdate'"));

			$qrate=mysqli_fetch_array(mysqli_query($link,"select mrp from ph_sell_details where item_code='$r[item_code]' order by slno desc limit 0,1 "));
			$qcstprice=mysqli_fetch_array(mysqli_query($link,"select recept_cost_price from ph_purchase_receipt_details where item_code='$r[item_code]' order by slno desc limit 0,1 "));
			
			$item_sell=$itmqnt['maxqnt']-$ret['qnt'];

			//$vitmamt=$itmqnt['maxqnt']*$qrate['mrp'];
			//$vitcostmamt=$itmqnt['maxqnt']*$qcstprice['recept_cost_price'];
			$vitmamt=$item_sell*$qrate['mrp'];
			$vitcostmamt=$item_sell*$qcstprice['recept_cost_price'];
			$vprofitamt=$vitmamt-$vitcostmamt;
			$vamount=$vamount+$vitmamt;


			$rsnttl=$rsnttl+$vitmamt;
			$vttlcstamt=$vttlcstamt+$vitcostmamt;
			$vttlprofit=$vttlprofit+$vprofitamt;
					
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['item_name'];?></td>
			<td style="text-align:right;"><?php echo $item_sell;?></td>
			<td style="text-align:right;"><?php echo $qrate['mrp'];?></td>
			<td style="text-align:right;"><?php echo $qcstprice['recept_cost_price'];?></td>
			<td style="text-align:right;"><?php echo number_format($vitmamt,2);?></td>
			<td style="text-align:right;"><?php echo number_format($vitcostmamt,2);?></td>
			<td style="text-align:right;"><?php echo number_format($vprofitamt,2);?></td>
		</tr>
		<?php
		$n++;
		}
			
		?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<th style="text-align:right;">Total Amount</th>
			<th style="text-align:right;"><?php echo number_format($rsnttl,2);?></th>
			<th style="text-align:right;"><?php echo number_format($vttlcstamt,2);?></th>
			<th style="text-align:right;"><?php echo number_format($vttlprofit,2);?></th>
		</tr>
	</table>
	<?php
} // 8

if($type==9)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$ph=1;
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $ph;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default act_btn" onclick="show_user_smry('<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $ph;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report" style="margin-bottom:0px;">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th style="text-align:right">Bill Amount</th>
			<th style="text-align:right">Received Amount</th>
			<th style="text-align:right">Discount Amount</th>
			<th style="text-align:right">Adjust Amount</th>
			<th style="text-align:right">Balance Amount</th>
			<th style="text-align:right">Current Return</th>
			<th style="text-align:right">Previous Return</th>
			<th style="text-align:right">Net Amount</th>
		</tr>
		
		<?php
		$bar_class=array("","success","info","warning","success","info","warning","success","info","warning");
		$all_user=array();
		$n=1;
		if($ph)
		{
			$qry="select distinct a.user,b.name from ph_payment_details a,employee b where a.`substore_id`='$ph' and a.entry_date between '$fdate' and '$tdate' and a.user=b.emp_id order by b.name ";
		}
		else
		{
			$qry="select distinct a.user,b.name from ph_payment_details a,employee b where entry_date between '$fdate' and '$tdate' and a.user=b.emp_id order by b.name ";
		}
		//echo $qry;
		$all_bal=0;
		$all_adj=0;
		$all_dis=0;
		$all_prev_ret_amt=0;
		$qbill=mysqli_query($link,$qry);
		while($qbill1=mysqli_fetch_array($qbill))
		{
			$vrtrnamt=0;
			if($ph)
			{
				//$qsaleamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amt),0) as maxsaleamt,ifnull(sum(paid_amt),0) as maxadvamt, ifnull(sum(discount_amt),0) as dis_amt, ifnull(sum(adjust_amt),0) as adj_amt, ifnull(sum(balance),0) as bal_amt from ph_sell_master where `substore_id`='$ph' and user='$qbill1[user]' and entry_date between '$fdate' and '$tdate' AND `bill_no` NOT IN (SELECT `bill_no` FROM `ph_esi_sell` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate')"));
				//$qamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxamt from ph_payment_details where `substore_id`='$ph' and user='$qbill1[user]' and entry_date between '$fdate' and '$tdate' AND `bill_no` NOT IN (SELECT `bill_no` FROM `ph_esi_sell` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate')"));
				$qsaleamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amt),0) as maxsaleamt,ifnull(sum(paid_amt),0) as maxadvamt, ifnull(sum(discount_amt),0) as dis_amt, ifnull(sum(adjust_amt),0) as adj_amt, ifnull(sum(balance),0) as bal_amt from ph_sell_master where `substore_id`='$ph' and user='$qbill1[user]' and entry_date between '$fdate' and '$tdate'"));
				$qamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxamt from ph_payment_details where `substore_id`='$ph' and user='$qbill1[user]' and entry_date between '$fdate' and '$tdate'"));
				
				//$qreturn=mysqli_query($link,"select * from ph_item_return_master  where `substore_id`='$ph' and user='$qbill1[user]' and return_date between '$fdate' and '$tdate'");
				$qreturn=mysqli_query($link,"SELECT a.* FROM `ph_item_return_master` a, `ph_sell_master` b WHERE a.`bill_no`=b.`bill_no` AND b.`entry_date` BETWEEN '$fdate' AND '$tdate' AND a.`user`='$qbill1[user]' AND a.`return_date` BETWEEN '$fdate' AND '$tdate'");
				while($qreturn1=mysqli_fetch_array($qreturn))
				{
					$qchkpay=mysqli_fetch_array(mysqli_query($link,"select paid_amt,balance from ph_sell_master where bill_no='$qreturn1[bill_no]' and substore_id='$ph' "));
					
					//if($qchkpay['paid_amt']>0 && $qchkpay['balance']==0)
					{
						$vrtrnamt1=$qreturn1['amount'];
						$vrtrnamt=$vrtrnamt+$vrtrnamt1;
					}
				}
		    }
		    else
			{
				//~ $qsaleamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amt),0) as maxsaleamt,ifnull(sum(paid_amt),0) as maxadvamt, ifnull(sum(discount_amt),0) as dis_amt, ifnull(sum(adjust_amt),0) as adj_amt, ifnull(sum(balance),0) as bal_amt from ph_sell_master  where user='$qbill1[user]' and entry_date between '$fdate' and '$tdate'"));
				//~ $qamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxamt from ph_payment_details  where user='$qbill1[user]' and entry_date between '$fdate' and '$tdate'"));
				//~ $qreturn=mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_item_return_master  where  user='$qbill1[user]' and return_date between '$fdate' and '$tdate'");
				//~ while($qreturn1=mysqli_fetch_array($qreturn))
				//~ {
					
					//~ $qchkpay=mysqli_fetch_array(mysqli_query($link,"select paid_amt,balance from ph_sell_master where bill_no='$qreturn1[bill_no]' and substore_id='$ph' "));
					
					//~ if($qchkpay['paid_amt']>0 && $qchkpay['balance']==0)
					//~ {
						//~ $vrtrnamt1=$qreturn1['amount'];
						//~ $vrtrnamt=$vrtrnamt+$vrtrnamt1;
					//~ }
				//~ }
		    }
		    $vttlsaleamt+=$qsaleamt['maxsaleamt'];
		    //$vnetamt=$qamt['maxamt']-$vrtrnamt;
		    
		    $prev_ret_amt=0;
		    $prev_ret_qry=mysqli_query($link,"SELECT a.* FROM `ph_item_return_master` a, `ph_sell_master` b WHERE a.`bill_no`=b.`bill_no` AND b.`entry_date` NOT BETWEEN '$fdate' AND '$tdate' AND a.`user`='$qbill1[user]' AND `return_date` BETWEEN '$fdate' AND '$tdate'");
		    while($prev_ret=mysqli_fetch_assoc($prev_ret_qry))
		    {
				$prev_ret_amt+=$prev_ret['amount'];
			}
		    
		    $all_adj+=$qsaleamt['adj_amt'];
		    $all_dis+=$qsaleamt['dis_amt'];
		    $all_bal+=$qsaleamt['bal_amt'];
		    $all_prev_ret_amt+=$prev_ret_amt;
		    
		    $vnetamt=$qamt['maxamt']-$prev_ret_amt;
		    $vttlrcpt=$vttlrcpt+$qamt['maxamt'];
		    $vttlrtrn=$vttlrtrn+$vrtrnamt;
		    $vttlnet=$vttlnet+$vnetamt;
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $qbill1['name'];?><span style="float:right;"><i class="icon-circle icon-large text-<?php echo $bar_class[$n];?> noprint"></i></span></td>
			<td style="text-align:right"><?php echo number_format($qsaleamt['maxsaleamt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt['maxamt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qsaleamt['dis_amt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qsaleamt['adj_amt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qsaleamt['bal_amt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($vrtrnamt,2);?></td>
			<td style="text-align:right"><?php echo number_format($prev_ret_amt,2);?></td>
			<td style="text-align:right"><?php echo number_format($vnetamt,2);?></td>
		</tr>
		<?php
		$n++;
		$u_val=$qbill1['user']."@".$qsaleamt['maxsaleamt'];
		array_push($all_user,$u_val);
		}
		$sum_net=$vttlsaleamt;
		?>
		<tr>
			<th colspan="2" style="text-align:right">Total :</th>
			<th style="text-align:right"><?php echo number_format($vttlsaleamt,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlrcpt,2)?> </th>
			<th style="text-align:right"><?php echo number_format($all_dis,2)?> </th>
			<th style="text-align:right"><?php echo number_format($all_adj,2)?> </th>
			<th style="text-align:right"><?php echo number_format($all_bal,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlrtrn,2)?> </th>
			<th style="text-align:right"><?php echo number_format($all_prev_ret_amt,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlnet,2)?> </th>
		</tr>
		<?php
		$all_expense=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) as tot FROM `ph_expense_detail` WHERE `date` BETWEEN '$fdate' AND '$tdate' AND `del`='0'"));
		if($all_expense['tot']>0)
		{
		?>
		<tr>
			<th colspan="2" style="text-align:right">Expense :</th>
			<th colspan="8" style="text-align:right"><?php echo number_format($all_expense['tot'],2)?></th>
		</tr>
		<?php
		$vttlnet=$vttlnet-$all_expense['tot'];
		?>
		<tr>
			<th colspan="2" style="text-align:right">Net Total :</th>
			<th style="text-align:right"><?php echo number_format($vttlsaleamt,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlrcpt,2)?> </th>
			<th style="text-align:right"><?php echo number_format($all_dis,2)?> </th>
			<th style="text-align:right"><?php echo number_format($all_adj,2)?> </th>
			<th style="text-align:right"><?php echo number_format($all_bal,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlrtrn,2)?> </th>
			<th style="text-align:right"><?php echo number_format($all_prev_ret_amt,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlnet,2)?> </th>
		</tr>
		<?php
		}
		?>
	</table>
	<div class="progress noprint">
		<?php
		$bar=1;
		foreach($all_user as $al_usr)
		{
			$vl=explode("@",$al_usr);
			$sale_amt=floor($vl[1]);
			$per=(($sale_amt/$sum_net)*100);
			//$per=number_format($per,2);
			?>
			<div class="bar bar-<?php echo $bar_class[$bar];?>" style="width: <?php echo $per;?>%;"><?php echo number_format($per,2)."%";?></div>
			<?php
			$bar++;
		}
		?>
	</div>
	<?php
} // 9

if($type==10)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	if($fdate==$tdate)
	{
	  $fdate=date('Y-m-d');
      $tdate=date('Y-m-d', strtotime($fdate. ' + 90 days'));
	}

	
	$fid=0;
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default btn_act" onclick="item_expiry_rep('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>MRP</th>
			<th>Batch No</th>
			<th>Cl. Stk</th>
			<th>Expiry Date</th>
			<th>Reciept No.</th>
			<!--<th>Bill No.</th>
			<th>Supplier</th>-->
		</tr>
		<?php
		$n=1;
		
		$q=mysqli_query($link,"SELECT distinct(a.item_code),b.item_name FROM ph_stock_master a,item_master  b WHERE a.item_code=b.item_id and  a.quantity>0 and a.substore_id='1' and a.exp_date between '$fdate' and '$tdate' ORDER BY b.item_name");
		while($r=mysqli_fetch_array($q))
		{
			$vstk=0;
			$itmttlamt=0;
			$qmrp=mysqli_fetch_array(mysqli_query($link,"select mrp,cost_price from item_master where item_id ='$r[item_code]'"));	
			$qbatch=mysqli_query($link,"select * from ph_stock_master  where item_code='$r[item_code]' and  quantity>0 and substore_id='1' and exp_date between '$fdate' and '$tdate'");
			$num_r=mysqli_num_rows($qbatch);
		?>
		<!--<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $qmrp['mrp'];?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>-->
		
               <?php
			    $vrcprt=0;
				while($qbatch1=mysqli_fetch_array($qbatch))
				{
				
				$rec=mysqli_fetch_array(mysqli_query($link,"SELECT `order_no`,`bill_no`,`SuppCode`,`recpt_mrp` FROM `ph_purchase_receipt_details` WHERE `item_code`='$qbatch1[item_code]' AND `expiry_date`='$qbatch1[exp_date]' AND `recept_batch`='$qbatch1[batch_no]'"));
				
				//$qmrp=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,SuppCode FROM `inv_main_stock_received_detail` WHERE `item_id`='$qbatch1[item_code]'  AND `recept_batch`='$qbatch1[batch_no]'"));
				
								
				$qsupplier=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$qmrp[SuppCode]' "));
				
				
				$vrcptamt=$qmrp['cost_price']*$qbatch1['quantity'];
				$itmttlamt=$itmttlamt+$vrcptamt;
				$vttlamt=$vttlamt+$vrcptamt;
				$vstk=$vstk+$qbatch1['quantity'];
				$tr_style='';
				if($qbatch1['exp_date']<=$date)
				{
					
					$tr_style='style="cursor:pointer;background-color: red;"';
				}
				?>	
				<tr>
					<?php
					if($num_r>0)
					{
					?>
					<td rowspan="<?php echo $num_r;?>"><?php echo $n;?></td>
					<td rowspan="<?php echo $num_r;?>"><?php echo $r['item_code'];?></td>
					<td rowspan="<?php echo $num_r;?>"><?php echo $r['item_name'];?></td>
					<?php
					$num_r=0;
					}
					?>
                    <td align="right" style="font-size:12px"><?php echo $rec['recpt_mrp'];?></td>
                    <td align="right" style="font-size:12px"><?php echo $qbatch1['batch_no'];?></td>
                    <td align="right" style="font-size:12px"><?php echo $qbatch1['quantity'];?></td>
                    <td align="right" style="font-size:12px"><?php echo date("Y-m", strtotime($qbatch1['exp_date']));?></td>
					<td><?php echo $rec['order_no'];?></td>
					<!--<td><?php echo $rec['bill_no'];?></td>
                    <td><?php echo $qsupplier['name'];?></td>-->
                  </tr> 
                  <?php
					 ;}?>
                  
		<?php
		$n++;
		}
			
		?>
		
	</table>
	<?php
} // 10

if($type==999)
{
	
}
?>
