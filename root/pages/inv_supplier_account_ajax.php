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

function convert_date_only_sm_year($date)
{
	if($date)
	{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
	}
}

// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

function text_query($txt,$file)
{
	if($txt)
	{
		$myfile = file_put_contents('../../log/'.$file, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
}

//$user_access=array(101,102,103,154);

$type=$_POST['type'];

if($type==1)
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$billno=$_POST['billno'];
	$user=$_POST['user'];
	$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$user'"));
	 
	$qry_type=2;
	if($qry_type==1)
	{
		if($billno!="")
		{
			$q=mysqli_query($link,"SELECT * FROM ph_purchase_receipt_master WHERE bill_no='$billno' order by bill_date desc");
		}
		else if($splrid==0)
		{
			$q=mysqli_query($link,"SELECT * FROM ph_purchase_receipt_master WHERE bill_date between '$fdate' and '$tdate' order by bill_date desc");
		}
		else
		{
			$q=mysqli_query($link,"SELECT * FROM ph_purchase_receipt_master WHERE bill_date between '$fdate' and '$tdate' and supp_code='$splrid' order by bill_date desc");
		}
		?>
		<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Received No</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Supplier</th>
			<th style="text-align:right">Bill Amount</th>
			<th>User</th>
			<th>View / Excel</th>
			
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supp_code]'"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			$vttl=$vttl+$r['net_amt'];
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td><?php echo $qsupplier['name'];?></td>
			<td style="text-align:right"><?php echo $r['net_amt'];?></td>
			<td ><?php echo $quser['name'];?></td>
			<td>
				<button type="button" class="btn btn-info" onclick="ph_rcv_print('<?php echo base64_encode($r['order_no']);?>')">View</button>
				<!--<button type="button" class="btn btn-success" onclick="bill_report_xls('<?php echo $r[bill_no];?>','<?php echo $r[supp_code];?>','<?php echo $r[recpt_date];?>')">Export to excel</button>-->
			</td>
				
		</tr>
		
		
		<?php
		$i++;
		}
		?>
		<tr>
			<td colspan="5" style="text-align:right;font-weight:bold;">Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttl,2);?></td>
			<td></td>
			<td></td>
		</tr>
	</table>
		<?php
	}
	if($qry_type==2)
	{
		if($billno !="")
		{
			$q=mysqli_query($link,"SELECT * FROM inv_main_stock_received_master  WHERE bill_no='$billno' and `supp_code`!='0' order by slno desc");
		}
		elseif($splrid==0)
		{
          $q=mysqli_query($link,"SELECT * FROM inv_main_stock_received_master  WHERE bill_date between '$fdate' and '$tdate' and `supp_code`!='0' order by slno desc");
		}
		else
		{
			$q=mysqli_query($link,"SELECT * FROM inv_main_stock_received_master  WHERE bill_date between '$fdate' and '$tdate' and supp_code='$splrid' order by slno desc");
		}

	
	?>
	<button type="button" class="btn btn-success" onclick="supplier_summery_print_excel('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>')">Excel</button>
	<button type="button" class="btn btn-info" onclick="supplier_summery_print('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<!--<th>Order No</th>-->
			<th>Received No</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Supplier</th>
			<th style="text-align:right">Bill Amount</th>
			<th>User</th>
			<th>View / Excel</th>
			
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supp_code]'"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			$vttl=$vttl+$r['net_amt'];
			$next_bill=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `inv_main_stock_received_master` WHERE `slno`>'$r[slno]' AND `supp_code`='$r[supp_code]' ORDER BY `slno` LIMIT 0,1"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<!--<td><?php echo $r['order_no'];?></td>-->
			<td><?php echo $r['receipt_no'];?></td>
			<td>
				<?php echo $r['bill_no'];
				//if($u['levelid']=="1" && !$next_bill)
				if($u['levelid']=="1")
				{
				?>
				<span style="float:right;" class="btn_edit" onclick="view_items('<?php echo base64_encode($r['order_no']);?>','<?php echo base64_encode($r['receipt_no']);?>')"><i class="icon-list"></i></span>
				<?php
				}
				?>
			</td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td><?php echo $qsupplier['name'];?><span style="float:right;" class="btn_edit" onclick="bill_det_edit('<?php echo base64_encode($r['receipt_no']);?>')"><i class="icon-edit icon-large text-success"></i></span></td>
			<td style="text-align:right"><?php echo number_format(round($r['net_amt']),2);?></td>
			<td ><?php echo $quser['name'];?></td>
			<td>
				<button type="button" class="btn btn-primary btn-mini" onclick="report_print_billwise('<?php echo $r['order_no'];?>','<?php echo $r['receipt_no'];?>')">View</button>
				<button type="button" class="btn btn-success btn-mini" onclick="bill_report_xls('<?php echo $r['order_no'];?>','<?php echo $r['receipt_no'];?>')">Export</button>
			</td>	
		</tr>
		<?php
		$i++;
		}
		if(in_array($user,$user_access))
		{
		?>
		<tr>
			<td colspan="5" style="text-align:right;font-weight:bold">Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format(round($vttl),2);?></td>
			<td></td>
			<td></td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
	}
}

if($type==2)
{
	$supp=$_POST['supp'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	if($supp)
	{
		$qry="SELECT DISTINCT a.`supp_code`, b.`name` FROM `inv_main_stock_received_master` a, `inv_supplier_master` b WHERE a.`supp_code`=b.`id` AND a.`bill_date` BETWEEN '$fdate' AND '$tdate' AND a.`supp_code`='$supp' ORDER BY b.`name`";
	}
	else
	{
		$qry="SELECT DISTINCT a.`supp_code`, b.`name` FROM `inv_main_stock_received_master` a, `inv_supplier_master` b WHERE a.`supp_code`=b.`id` AND a.`bill_date` BETWEEN '$fdate' AND '$tdate' ORDER BY b.`name`";
	}
	//echo $qry;
	$text="Supplier wise details from ".convert_date($fdate)." to ".convert_date($tdate);
	$val=array();
	$q=mysqli_query($link,$qry);
	while($r=mysqli_fetch_assoc($q))
	{
		$amt=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifnull(SUM(`net_amt`),0) AS `net_amt` FROM `inv_main_stock_received_master` WHERE `supp_code`='$r[supp_code]' AND `bill_date` BETWEEN '$fdate' AND '$tdate'"));
		$temp=array();
		array_push($temp, $r['name']);
		array_push($temp, (float)$amt['net_amt']);
		array_push($val, $temp);
	}
	echo $text."@govinda@".json_encode($val);
}

if($type==3)
{
	$supp=$_POST['supp'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	$chkDate = date('Y-04-01');
	$chkDate = date('Y-m-d', strtotime($chkDate));
	$startDate = date('Y-m-d', strtotime($fdate));
	$endDate = date('Y-m-d', strtotime($tdate));
	$fdate1="";
	$tdate1="";
	$open=1;
	if(($chkDate >= $startDate) && ($chkDate <= $endDate))
	{
		//~ $fdate=$startDate;
		//~ $tdate=date('Y-m-d',strtotime($chkDate.' -1 day'));
		//~ $fdate1=$chkDate;
		//~ $tdate1=$endDate;
		$open=0;
	}
	//~ else
	//~ {
		//~ if(($chkDate >= $startDate))
		//~ {
			//~ $fdate=$startDate;
		//~ }
		//~ if(($chkDate <= $endDate))
		//~ {
			//~ $tdate=$endDate;
		//~ }
	//~ }
	$qry="SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$supp'";
	if($fdate && $tdate)
	{
		$qry.=" AND `date` BETWEEN '$fdate' AND '$tdate' ORDER BY `date`,`time`";
	}
	//echo $qry;
	$j=1;
	$q=mysqli_query($link,$qry);
	$s_name=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$supp'"));
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th colspan="5">Supplier : <?php echo $s_name['name'];?></th>
			<th colspan="4">
				Received From : <?php echo convert_date($fdate);?> To : <?php echo convert_date($tdate);?>
				<span style="float:right;">
					<button type="button" class="btn btn-primary btn-mini" onclick="print_supp_bal('<?php echo $supp;?>','<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
				</span>
			</th>
		</tr>
		<tr>
			<th>#</th>
			<th>Bill Date</th>
			<th>Bill No</th>
			<th>Trans Type</th>
			<th>Pay Bill No</th>
			<th style="text-align:right;">Dr Amount</th>
			<th style="text-align:right;">Cr Amount</th>
			<th style="text-align:right;">Adjust</th>
			<th style="text-align:right;">Balance</th>
		</tr>
		<?php
		$net_balance=0;
		$dr_amt=0;
		$cr_amt=0;
		$adjust=0;
		
		$open_bal=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$supp' AND `type`!='9' AND `date`<'$fdate' ORDER BY `slno` DESC LIMIT 0,1"));
		$opening_bal=$open_bal['balance_amt'];
		$net_balance=$opening_bal;
		$dr_amt=$opening_bal;
		if($open)
		{
		?>
		<tr>
			<td><?php echo $j;?></td>
			<th>Opening Balance</th>
			<td><?php //echo number_format($open_bal['bal_amount'],2);?></td>
			<td></td>
			<td></td>
			<td style="text-align:right;"><?php //echo number_format($opening_bal,2);?></td>
			<td style="text-align:right;"><?php //echo number_format($amt,2);?></td>
			<td style="text-align:right;"><?php //echo number_format($amt,2);?></td>
			<td style="text-align:right;"><?php echo number_format($opening_bal,2);?></td>
		</tr>
	<?php
		}
	$j++;
	while($r=mysqli_fetch_assoc($q))
	{
		$process_no="";
		$payment_mode="";
		$cheque_no="";
		$all_bills="";
		if($r['type']=="1")
		{
			$process_no=$r['process_no'];
			$payment_mode="Purchase";
		}
		if($r['type']=="2")
		{
			$n=1;
			$process_no="";
			$br="";
			$pay=mysqli_fetch_assoc(mysqli_query($link,"SELECT `payment_mode`,`cheque_no` FROM `inv_supplier_payment_master` WHERE `payment_no`='$r[process_no]'"));
			$payment_mode=$pay['payment_mode'];
			$cheque_no=$pay['cheque_no'];
			$bil=mysqli_query($link,"SELECT `bill_no` FROM `inv_supplier_payment_details` WHERE `payment_no`='$r[process_no]'");
			while($bl=mysqli_fetch_assoc($bil))
			{
				if($all_bills)
				{
					$all_bills.=", ".$br.$bl['bill_no'];
				}
				else
				{
					$all_bills=$bl['bill_no'];
				}
				if($n==3)
				{
					$br="<br/>";
					$n=1;
				}
				else
				{
					$br="";
					$n++;
				}
			}
		}
		if($r['type']=="3")
		{
			$process_no=$r['process_no'];
			$payment_mode="Credit";
		}
		if($r['type']=="4")
		{
			$process_no=$r['process_no'];
			$payment_mode="Add Balance";
		}
		if($r['type']=="5")
		{
			$process_no=$r['process_no'];
			$payment_mode="Bill Less";
		}
		if($r['type']=="6")
		{
			$process_no=$r['process_no'];
			$payment_mode="Add Bill";
		}
		if($r['type']=="9")
		{
			$process_no="-";
			$payment_mode="FY ".date("Y")." opening entry";
			$r['debit_amt']=$r['balance'];
		}
		if($r['type']=="10")
		{
			$process_no="-";
			$payment_mode="Balance Settlement";
		}
		if($r['type']=="9")
		{
		?>
		<tr>
			<td colspan="9" style="background:#E3E3E3;"></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $process_no;?></td>
			<td><?php echo "<small>".$payment_mode."</small>"; if($cheque_no){echo " <small>(".$cheque_no.")</small>";}?></td>
			<td><?php echo $all_bills;?></td>
			<td style="text-align:right;"><?php echo number_format($r['debit_amt'],2);?></td>
			<td style="text-align:right;"><?php echo number_format($r['credit_amt'],2);?></td>
			<td style="text-align:right;"><?php echo number_format($r['adjust_amt'],2);?></td>
			<td style="text-align:right;"><?php echo number_format($r['balance_amt'],2);?></td>
		</tr>
		<?php
		$j++;
		//~ $dr_amt+=$r['debit_amt'];
		//~ $cr_amt+=$r['credit_amt'];
		//~ $adjust+=$r['adjust_amt'];
		//~ $net_balance=$r['balance_amt'];
	}
	$final_bal=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$supp' AND `type`!='9' ORDER BY `slno` DESC LIMIT 0,1"));
	?>
		<tr style="display:none;">
			<th></th>
			<th><?php echo date("d-M-Y");?></th>
			<th></th>
			<th colspan="2">Final Balance till date</th>
			<th></th>
			<th></th>
			<th></th>
			<th style="text-align:right;"><?php echo number_format($final_bal['balance_amt'],2);?></th>
		</tr>
	</table>
	<?php
}

if($type==4)
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	 
	
	if($splrid==0)
		{
          $q=mysqli_query($link,"SELECT * FROM inv_item_return_supplier_master  WHERE date between '$fdate' and '$tdate'  order by slno");
		}
		else
		{
			$q=mysqli_query($link,"SELECT * FROM inv_item_return_supplier_master  WHERE  date between '$fdate' and '$tdate' and  supplier_id='$splrid' order by slno");
		}
	
	
	
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ord_rep_prr('<?php echo $ord;?>')">Print</button>-->
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>Return No</th>
			<th> Date</th>
			<th>Supplier</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">GST</th>
			<th>View</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supplier_id]'"));
			
			$qamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(item_amount),0) as maxamt,ifnull(sum(gst_amount),0) as maxgstamt from inv_item_return_supplier_detail where returnr_no='$r[returnr_no]' and supplier_id='$r[supplier_id]'"));
			$vreturnamt=0;
			$vreturnamt=$qamt['maxamt']+$qamt['maxgstamt'];
			$vgstamtttl1+=$qamt['maxgstamt'];
			$vttl1=$vttl1+$vreturnamt;
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['returnr_no'];?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td>
				<?php echo $qsupplier['name'];?>
				<span style="float:right;">
					<button type="button" class="btn btn-default btn-mini" onclick="ret_bill_edit('<?php echo base64_encode($r['returnr_no']);?>')"><i class="icon-edit icon-large"></i></button>
				</span>
			</td>
			<td style="text-align:right"><?php echo number_format($vreturnamt,2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt['maxgstamt'],2);?></td>
			<!--<td><button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $r[order_no];?>')">Export to excel</button></td>-->
			<td><button type="button" class="btn btn-primary btn-mini" onclick="report_print_return('<?php echo $r['returnr_no'];?>','<?php echo $r['supplier_id'];?>')">View</button></td>
		</tr>
		
		
		<?php
		$i++;
		}
		?>
		<?php
		$vttl=round($vttl1);
		$vgstamtttl=round($vgstamtttl1);
		?>
		<tr>
			<td colspan="4" style="text-align:right;font-weight:bold">Total(Rounded) :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttl,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vgstamtttl,2);?></td>
			<td></td>
			
		</tr>
	</table>
	<?php
}

if($type==5)
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<span class="noprint" style="float:right;">
		<button type="button" class="btn btn-primary act_btn" onclick="supplier_rcvd_gst_print('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
		<button type="button" class="btn btn-success act_btn" onclick="supplier_rcvd_gst_export('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>')">Export</button>
	</span>
	<table class="table table-condensed table-bordered table-report">
		<tr style="background:#DFE6E4;">
			<th>#</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Party Name</th>
			<th>GSTIN</th>
			<th style="text-align:right">Bill Value</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">Discount</th>
			<th style="text-align:right">Credit Note</th>
			<th style="text-align:right">Adjustment</th>
			<th style="text-align:right">Round Off</th>
			<th style="text-align:right">GST Amount</th>
			<th colspan="2">Amount <span style="float:right">0 %</span></th>
			<th colspan="2">Amount <span style="float:right">5 %</span></th>
			<th colspan="2">Amount <span style="float:right">12 %</span></th>
			<th colspan="2">Amount <span style="float:right">18 %</span></th>
			<th style="text-align:right">Tax Amount</th>
		</tr>
		<?php
		if($splrid)
		{
			$bil=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_master` WHERE `supp_code`='$splrid' AND `bill_date` BETWEEN '$fdate' and '$tdate' ORDER BY `bill_date`");
		}
		else
		{
			$bil=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_master` WHERE `bill_date` BETWEEN '$fdate' and '$tdate' ORDER BY `bill_date`");
		}
		$j=1;
		$tot_disc=0;
		$tot_gst=0;
		$bill_amount=0;
		$tot_amt=0;
		$cred_amt=0;
		$adj_amt=0;
		$tot_amt_0=0;
		$tot_gst_0=0;
		$tot_amt_5=0;
		$tot_gst_5=0;
		$tot_amt_12=0;
		$tot_gst_12=0;
		$tot_amt_18=0;
		$tot_gst_18=0;
		while($bl=mysqli_fetch_assoc($bil))
		{
			$s_name=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name`,`address`,`gst_no` FROM `inv_supplier_master` WHERE `id`='$bl[supp_code]'"));
			$qamt_0=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail  WHERE `rcv_no`='$bl[receipt_no]' and gst_per='0.00'")); // 0%
			$qamt_5=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail  WHERE `rcv_no`='$bl[receipt_no]' and gst_per='5.00'")); // 5%
			$qamt_12=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail  WHERE `rcv_no`='$bl[receipt_no]' and gst_per='12.00'")); // 12%
			$qamt_18=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail  WHERE `rcv_no`='$bl[receipt_no]' and gst_per='18.00'")); // 18%
			
			$tot_amt_0+=$qamt_0['maxamt'];
			$tot_gst_0+=$qamt_0['maxgst'];
			
			$tot_amt_5+=$qamt_5['maxamt'];
			$tot_gst_5+=$qamt_5['maxgst'];
			
			$tot_amt_12+=$qamt_12['maxamt'];
			$tot_gst_12+=$qamt_12['maxgst'];
			
			$tot_amt_18+=$qamt_18['maxamt'];
			$tot_gst_18+=$qamt_18['maxgst'];
			?>
			<tr>
				<td><?php echo $j;?></td>
				<td><?php echo $bl['bill_no'];?></td>
				<td><?php echo convert_date($bl['bill_date']);?></td>
				<td><?php echo $s_name['name']; /*if($s_name['address']){echo ", ".$s_name['address'];}*/?></td>
				<td><?php echo $s_name['gst_no'];?></td>
				<td style="text-align:right"><?php echo round($bl['net_amt']).".00";?></td>
				<td style="text-align:right"><?php echo round($bl['bill_amount']).".00";?></td>
				<td style="text-align:right"><?php echo $bl['dis_amt'];?></td>
				<td style="text-align:right"><?php echo $bl['credit_amt'];?></td>
				<td style="text-align:right"><?php echo $bl['adjust_amt'];?></td>
				<td style="text-align:right">0.00</td>
				<td style="text-align:right"><?php echo $bl['gst_amt'];?></td>
				<td style="text-align:right"><?php echo number_format($qamt_0['maxamt'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_0['maxgst'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_5['maxamt'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_5['maxgst'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_12['maxamt'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_12['maxgst'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_18['maxamt'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_18['maxgst'],2);?></td>
				<td style="text-align:right"><?php echo number_format($bl['gst_amt'],2);?></td>
			</tr>
			<?php
			$j++;
			
			$tot_disc+=$bl['dis_amt'];
			$tot_amt+=round($bl['net_amt']);
			$bill_amount+=round($bl['bill_amount']);
			$cred_amt+=$bl['credit_amt'];
			$adj_amt+=$bl['adjust_amt'];
			$tot_gst+=$bl['gst_amt'];
			
			//~ $net_disc+=$tot_disc;
			//~ $net_amount+=$tot_amt;
			//~ $net_amt_0+=$tot_amt_0;
			//~ $net_gst_0+=$tot_gst_0;
			//~ $net_amt_5+=$tot_amt_5;
			//~ $net_gst_5+=$tot_gst_5;
			//~ $net_amt_12+=$tot_amt_12;
			//~ $net_gst_12+=$tot_gst_12;
			//~ $net_amt_18+=$tot_amt_18;
			//~ $net_gst_18+=$tot_gst_18;
		}
		?>
		<tr>
			<th colspan="5" style="text-align:right">Grand Total :</th>
			<th style="text-align:right"><?php echo number_format($tot_amt,2);?></th>
			<th style="text-align:right"><?php echo number_format($bill_amount,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_disc,2);?></th>
			<th style="text-align:right"><?php echo number_format($cred_amt,2);?></th>
			<th style="text-align:right"><?php echo number_format($adj_amt,2);?></th>
			<th style="text-align:right"><?php echo number_format(0,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_0,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_0,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_5,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_5,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_12,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_12,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_18,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_18,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst,2);?></th>
		</tr>
	</table>
	<?php
}

if($type==555)
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<span class="noprint" style="float:right;">
		<button type="button" class="btn btn-default" id="act_btn" onclick="supplier_rcvd_gst_print('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	</span>
	<table class="table table-condensed table-bordered table-report">
		<tr style="background:#DFE6E4;">
			<th>#</th>
			<th>Bill Date</th>
			<th>Received Date</th>
			<th>Bill No</th>
			<th style="text-align:right">Discount</th>
			<th style="text-align:right">Bill Amount</th>
			<th colspan="2">Amount <span style="float:right">0 %</span></th>
			<th colspan="2">Amount <span style="float:right">5 %</span></th>
			<th colspan="2">Amount <span style="float:right">12 %</span></th>
			<th colspan="2">Amount <span style="float:right">18 %</span></th>
			<!--<th style="text-align:right">Amount @ 5 %</th>
			<th style="text-align:right">Amount @ 12 %</th>
			<th style="text-align:right">Amount @ 18 %</th>-->
		</tr>
	<?php
	$net_disc=0;
	$net_amount=0;
	$net_amt_0=0;
	$net_gst_0=0;
	$net_amt_5=0;
	$net_gst_5=0;
	$net_amt_12=0;
	$net_gst_12=0;
	$net_amt_18=0;
	$net_gst_18=0;
	if($splrid)
	{
		$sid=mysqli_query($link,"SELECT DISTINCT `supp_code` FROM `inv_main_stock_received_master` WHERE `supp_code`='$splrid' and `bill_date` BETWEEN '$fdate' and '$tdate' ORDER BY `bill_date`");
	}
	else
	{
		$sid=mysqli_query($link,"SELECT DISTINCT `supp_code` FROM `inv_main_stock_received_master` WHERE `bill_date` BETWEEN '$fdate' and '$tdate' ORDER BY `bill_date`");
	}
	while($ss=mysqli_fetch_assoc($sid))
	{
		$s_name=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$ss[supp_code]'"));
		$bil=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_master` WHERE `supp_code`='$ss[supp_code]' and `bill_date` BETWEEN '$fdate' and '$tdate' ORDER BY `bill_date`");
		?>
		<tr>
			<th colspan="2">Supplier :</th>
			<th colspan="12"><?php echo $s_name['name'];?></th>
		</tr>
		<?php
		$j=1;
		$tot_disc=0;
		$tot_amt=0;
		$tot_amt_0=0;
		$tot_gst_0=0;
		$tot_amt_5=0;
		$tot_gst_5=0;
		$tot_amt_12=0;
		$tot_gst_12=0;
		$tot_amt_18=0;
		$tot_gst_18=0;
		while($bl=mysqli_fetch_assoc($bil))
		{
			//~ $qq_gst=mysqli_query($link,"SELECT DISTINCT `gst_per` FROM `inv_main_stock_received_detail` WHERE `order_no`='$bl[receipt_no]' ORDER BY `gst_per`");
			//~ $gsts="";
			//~ while($gg=mysqli_fetch_assoc($qq_gst))
			//~ {
				//~ $gsts.=$gg['gst_per'].",";
			//~ }
			
			//$qamt_0=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.item_amount),0) as maxamt, ifnull(sum(a.`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail a,inv_main_stock_received_master b,item_master c  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and a.`bill_no`='$bl[bill_no]' and a.SuppCode='$ss[supp_code]' and b.bill_date='$bl[bill_date]' and a.gst_per='0.00' and a.item_id=c.item_id and c.category_id='1' ")); // 0%
			$qamt_0=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail  WHERE `order_no`='$bl[receipt_no]' and gst_per='0.00'")); // 0%
			
			//$qamt_5=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.item_amount),0) as maxamt, ifnull(sum(a.`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail a,inv_main_stock_received_master b,item_master c  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and a.`bill_no`='$bl[bill_no]' and a.SuppCode='$ss[supp_code]' and b.bill_date='$bl[bill_date]' and a.gst_per='5.00' and a.item_id=c.item_id and c.category_id='1' ")); // 5%
			$qamt_5=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail  WHERE `order_no`='$bl[receipt_no]' and gst_per='5.00'")); // 5%
			
			//$qamt_12=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.item_amount),0) as maxamt, ifnull(sum(a.`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail a,inv_main_stock_received_master b,item_master c  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and a.`bill_no`='$bl[bill_no]' and a.SuppCode='$ss[supp_code]' and b.bill_date='$bl[bill_date]' and a.gst_per='12.00' and a.item_id=c.item_id and c.category_id='1' ")); // 12%
			$qamt_12=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail  WHERE `order_no`='$bl[receipt_no]' and gst_per='12.00'")); // 12%
			
			//$qamt_18=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.item_amount),0) as maxamt, ifnull(sum(a.`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail a,inv_main_stock_received_master b,item_master c  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and a.`bill_no`='$bl[bill_no]' and a.SuppCode='$ss[supp_code]' and b.bill_date='$bl[bill_date]' and a.gst_per='18.00' and a.item_id=c.item_id and c.category_id='1' ")); // 18%
			$qamt_18=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail  WHERE `order_no`='$bl[receipt_no]' and gst_per='18.00'")); // 18%
			
			//$gst_amt_0=$qamt_0['maxamt']*0/100;
			//$tot_gst_0+=$gst_amt_0;
			$tot_amt_0+=$qamt_0['maxamt'];
			$tot_gst_0+=$qamt_0['maxgst'];
			
			//$gst_amt_5=$qamt_5['maxamt']*5/100;
			//$tot_gst_5+=$gst_amt_5;
			$tot_amt_5+=$qamt_5['maxamt'];
			$tot_gst_5+=$qamt_5['maxgst'];
			
			//$gst_amt_12=$qamt_12['maxamt']*12/100;
			//$tot_gst_12+=$gst_amt_12;
			$tot_amt_12+=$qamt_12['maxamt'];
			$tot_gst_12+=$qamt_12['maxgst'];
			
			//$gst_amt_18=$qamt_18['maxamt']*18/100;
			//$tot_gst_18+=$gst_amt_18;
			$tot_amt_18+=$qamt_18['maxamt'];
			$tot_gst_18+=$qamt_18['maxgst'];
			
			$bill_discount=$bl['dis_amt'];
			//$bill_discount=($bl['bill_amount']-$qamt_0['maxamt']-$qamt_5['maxamt']-$qamt_12['maxamt']-$qamt_18['maxamt']);
			$tot_disc+=$bill_discount;
			//$tot_amt+=$bl['bill_amount'];
			$tot_amt+=$bl['net_amt'];
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo convert_date($bl['bill_date']);?></td>
			<td><?php echo convert_date($bl['recpt_date']);?></td>
			<td><?php echo $bl['bill_no'];?></td>
			<td style="text-align:right"><?php echo number_format($bill_discount,2);?></td>
			<td style="text-align:right"><?php echo number_format($bl['net_amt'],2);?></td>
			<!--<td style="text-align:right"><?php echo number_format($gst_amt_0,2);?></td>
			<td style="text-align:right"><?php echo number_format($gst_amt_5,2);?></td>
			<td style="text-align:right"><?php echo number_format($gst_amt_12,2);?></td>
			<td style="text-align:right"><?php echo number_format($gst_amt_18,2);?></td>-->
			
			<td style="text-align:right"><?php echo number_format($qamt_0['maxamt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt_0['maxgst'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt_5['maxamt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt_5['maxgst'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt_12['maxamt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt_12['maxgst'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt_18['maxamt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt_18['maxgst'],2);?></td>
		</tr>
		<?php
		$j++;
		}
		?>
		<tr>
			<th colspan="4" style="text-align:right">Total <i>(round)</i> :</th>
			<th style="text-align:right"><?php echo number_format($tot_disc,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_0,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_0,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_5,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_5,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_12,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_12,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_18,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_18,2);?></th>
		</tr>
		<?php
		$net_disc+=$tot_disc;
		$net_amount+=$tot_amt;
		$net_amt_0+=$tot_amt_0;
		$net_gst_0+=$tot_gst_0;
		$net_amt_5+=$tot_amt_5;
		$net_gst_5+=$tot_gst_5;
		$net_amt_12+=$tot_amt_12;
		$net_gst_12+=$tot_gst_12;
		$net_amt_18+=$tot_amt_18;
		$net_gst_18+=$tot_gst_18;
	}
	?>
		<tr>
			<th colspan="4" style="text-align:right">Grand Total :</th>
			<th style="text-align:right"><?php echo number_format($net_disc,2);?></th>
			<th style="text-align:right"><?php echo number_format($net_amount,2);?></th>
			<th style="text-align:right"><?php echo number_format($net_amt_0,2);?></th>
			<th style="text-align:right"><?php echo number_format($net_gst_0,2);?></th>
			<th style="text-align:right"><?php echo number_format($net_amt_5,2);?></th>
			<th style="text-align:right"><?php echo number_format($net_gst_5,2);?></th>
			<th style="text-align:right"><?php echo number_format($net_amt_12,2);?></th>
			<th style="text-align:right"><?php echo number_format($net_gst_12,2);?></th>
			<th style="text-align:right"><?php echo number_format($net_amt_18,2);?></th>
			<th style="text-align:right"><?php echo number_format($net_gst_18,2);?></th>
		</tr>
	</table>
	<?php
}

if($type==555)
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="supplier_rcvd_gst_print('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>Bill Date</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">CGST</th>
			<th style="text-align:right">SGST</th>
		
		</tr>
		
		
		<?php
		$qgst=mysqli_query($link,"SELECT distinct a.gst_per FROM inv_main_stock_received_detail a,inv_main_stock_received_master b  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date between '$fdate' and '$tdate' order by gst_per");
		 //$qgst=mysqli_query($link,"SELECT distinct a.gst_per FROM inv_main_stock_received_detail a,inv_main_stock_received_master b,item_master c  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date between '$fdate' and '$tdate' and a.item_id=c.item_id and c.category_id='1' order by a.gst_per");
		while($qgst1=mysqli_fetch_array($qgst))
		{
			$i=1;
			$subttlamt=0;
			$subttlcgst=0;
		
		?>
		  <tr>
			  <td colspan="5" style="font-weight:bold">Gst <?php echo number_format($qgst1['gst_per'],0);?> %</td>
		  </tr>
		<?php
        
               
        $q=mysqli_query($link,"SELECT distinct b.bill_date FROM inv_main_stock_received_detail a,inv_main_stock_received_master b  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date between '$fdate' and '$tdate' and a.gst_per='$qgst1[gst_per]' order by b.bill_date");
		while($r=mysqli_fetch_array($q))
		{
			
			$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.item_amount),0) as maxamt FROM inv_main_stock_received_detail a,inv_main_stock_received_master b  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date='$r[bill_date]'  and a.gst_per='$qgst1[gst_per]' "));
			$qbilldetail=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_payment_details where bill_no='$r[bill_no]' and supp_code='$r[supp_code]'"));
			
			//$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.item_amount),0) as maxamt FROM inv_main_stock_received_detail a,inv_main_stock_received_master b,item_master c  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date='$r[bill_date]'  and a.gst_per='$qgst1[gst_per]' and a.item_id=c.item_id and c.category_id='1' "));
			$vgstamt1=$qamt['maxamt']*$qgst1['gst_per']/100;
			$vgstamt=($vgstamt1);
			$cgst=$vgstamt/2;
			
			$subttlamt+=$qamt['maxamt'];
			$subttlcgst+=$cgst;
			
			?>
			 <tr>
			  <td ><?php echo $i;?> </td>
			  <td ><?php echo convert_date($r['bill_date']);?> </td>
			  <td style="text-align:right"><?php echo $qamt['maxamt'];?> </td>
			  <td style="text-align:right"><?php echo number_format($cgst,2);?> </td>
			  <td style="text-align:right"><?php echo number_format($cgst,2);?> </td>
		  </tr>
			
		<?php
	  $i++; }	
		?>
		
		  <tr>
			<td colspan="2" style="text-align:right;font-weight:bold">Sub Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlamt,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlcgst,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlcgst,2);?></td>
			
		</tr>
		
		<?php
	 }?>
		
		</tr>
	</table>
	<?php
}

if($type==6)
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="supplier_return_gst_print('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $billno;?>')">Print</button>
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>Return Date</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">CGST</th>
			<th style="text-align:right">SGST</th>
		
		</tr>
		
		
		<?php
		
		
		
		$qgst=mysqli_query($link,"SELECT distinct gst_per FROM inv_item_return_supplier_detail  WHERE date between '$fdate' and '$tdate' order by gst_per");
		while($qgst1=mysqli_fetch_array($qgst))
		{
			$i=1;
			$subttlamt=0;
			$subttlcgst=0;
		
		?>
		  <tr>
			  <td colspan="5" style="font-weight:bold">Gst <?php echo number_format($qgst1['gst_per'],0);?> %</td>
		  </tr>
		<?php
        
               
        $q=mysqli_query($link,"SELECT distinct date FROM inv_item_return_supplier_detail   WHERE date between '$fdate' and '$tdate' and gst_per='$qgst1[gst_per]' order by date");
		while($r=mysqli_fetch_array($q))
		{
			
			$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt FROM inv_item_return_supplier_detail   WHERE date='$r[date]'  and gst_per='$qgst1[gst_per]' "));
			$qbilldetail=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_payment_details where bill_no='$r[bill_no]' and supp_code='$r[supp_code]'"));
			$vgstamt1=$qamt['maxamt']*$qgst1['gst_per']/100;
			$vgstamt=($vgstamt1);
			$cgst=$vgstamt/2;
			
			$subttlamt+=$qamt['maxamt'];
			$subttlcgst+=$cgst;
			
			?>
			 <tr>
			  <td ><?php echo $i;?> </td>
			  <td ><?php echo convert_date($r['date']);?> </td>
			  <td style="text-align:right"><?php echo $qamt['maxamt'];?> </td>
			  <td style="text-align:right"><?php echo number_format($cgst,2);?> </td>
			  <td style="text-align:right"><?php echo number_format($cgst,2);?> </td>
		  </tr>
			
		<?php
	  $i++; }	
		?>
		
		  <tr>
			<td colspan="2" style="text-align:right;font-weight:bold">Sub Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlamt,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlcgst,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlcgst,2);?></td>
			
		</tr>
		
		<?php
	 }?>
		
		</tr>
	</table>
	<?php
} // 6

if($type==7)
{
	$rcv=base64_decode($_POST['rcv']);
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$rcv'"));
	?>
	<input type="hidden" id="e_rcv" value="<?php echo $rcv;?>" />
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th colspan="2" style="text-align:center;">Bill Generation Process</th>
		</tr>
		<tr>
			<th>Suppier</th>
			<td>
				<select id="e_supp" class="span4">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `id`, `name` FROM `inv_supplier_master` ORDER BY `name`");
					while($r=mysqli_fetch_assoc($q))
					{
					?>
					<option value="<?php echo $r['id'];?>" <?php if($r['id']==$det['supp_code']){echo "selected='selected'";}?>><?php echo $r['name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Bill No</th>
			<td>
				<input type="text" class="span2" id="e_bill_no" value="<?php echo $det['bill_no'];?>" placeholder="Bill No" />
			</td>
		</tr>
		<tr>
			<th>Bill Date</th>
			<td>
				<input type="text" class="span2" id="e_bill_dt" value="<?php echo $det['bill_date'];?>" placeholder="Bill Date" />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button type="button" class="btn btn-primary" id="sav" onclick="save_bill('<?php echo base64_encode($rcv);?>')" <?php echo $disb;?>>Done</button>
				<button type="button" class="btn btn-danger" id="canc" data-dismiss="modal">Cancel</button>
			</td>
		</tr>
	</table>
	<script>
		$("#bill_dt").datepicker({dateFormat: 'yy-mm-dd', maxDate: '0'});
		setTimeout(function(){$("#e_bill_no").focus();},500);
	</script>
	<?php
} // 7

if($type==8)
{
	$supp=$_POST['supp'];
	$bill_no=mysqli_real_escape_string($link,$_POST['bill_no']);
	$bill_dt=$_POST['bill_dt'];
	$rcv=base64_decode($_POST['rcv']);
	
	if(mysqli_query($link,"UPDATE `inv_main_stock_received_master` SET `bill_date`='$bill_dt', `supp_code`='$supp', `bill_no`='$bill_no' WHERE `receipt_no`='$rcv'"))
	{
		mysqli_query($link,"UPDATE `inv_main_stock_received_detail` SET `bill_no`='$bill_no', `SuppCode`='$supp' WHERE `rcv_no`='$rcv'");
		//if() supplier transaction check supplier
		echo "Done";
	}
	else
	{
		echo "Error";
	}
}

if($type==9)
{
	$dt=date("Y-m-d");
	$ord=base64_decode($_POST['ord']);
	$rcv=base64_decode($_POST['rcv']);
	$user=$_POST['user'];
	$u=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` from employee WHERE emp_id='$user'"));
	$det=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_main_stock_received_master WHERE receipt_no='$rcv'"));
	?>
	<input type="hidden" id="ord" value="<?php echo $ord;?>" />
	<input type="hidden" id="rcv" value="<?php echo $rcv;?>" />
	<input type="hidden" id="years" value="<?php echo date("Y");?>" />
	<table class="table table-condensed table-bordered">
		<tr>
			<th colspan="10" style="text-align:center;">Bill Details</th>
		</tr>
		<tr>
			<th>#</th>
			<th>Description</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th>Qnt + Free</th>
			<th>Mrp</th>
			<th>GST %</th>
			<th>Cost</th>
			<th>Discount %</th>
			<th>Amount</th>
		</tr>
	<?php
	if($u['levelid']=="1")
	{
	$j=1;
	//$q=mysqli_query($link,"SELECT `item_id`, `expiry_date`, `recpt_quantity`, `free_qnt`, `batch_no`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount` FROM `inv_main_stock_received_detail` WHERE `order_no`='$rcv' AND `rcv_no`='$rcv'");
	$q=mysqli_query($link,"SELECT a.`slno`, a.`item_id`, a.`expiry_date`, a.`recpt_quantity`, a.`free_qnt`, a.`batch_no`, a.`recpt_mrp`, a.`recept_cost_price`,a.`cost_price`, a.`item_amount`, a.`dis_per`, a.`dis_amt`, a.`gst_per`, a.`gst_amount` ,b.item_name from inv_main_stock_received_detail a,item_master  b,inv_main_stock_received_master c WHERE a.rcv_no='$rcv' and a.item_id=b.item_id and a.rcv_no=c.receipt_no and a.bill_no=c.bill_no and c.supp_code='$det[supp_code]' and c.recpt_date='$det[recpt_date]'");
	//echo "SELECT `item_id`, `expiry_date`, `recpt_quantity`, `free_qnt`, `batch_no`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount` FROM `inv_main_stock_received_detail` WHERE `order_no`='$rcv' AND `rcv_no`='$rcv'";
	$tot=0;
	$dis=0;
	$gst=0;
	while($r=mysqli_fetch_assoc($q))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`strip_quantity` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		$dis_per=explode(".",$r['dis_per']);
		$dis_per=$dis_per[0];
		$gst_per=explode(".",$r['gst_per']);
		$gst_per=$gst_per[0];
		?>
		<tr class="all_tr">
			<td>
				<?php echo $j;?>
				<input type="hidden" class="sl" value="<?php echo $r['slno'];?>" />
			</td>
			<td>
				<?php echo $itm['item_name'];?>
				<input type="hidden" class="span1 pack" id="pack<?php echo $j;?>" value="<?php echo $itm['strip_quantity'];?>" />
			</td>
			<td><?php echo $r['batch_no'];?></td>
			<td>
				<input type="text" class="span1 exp_dt" id="dt<?php echo $j;?>" onkeyup="exp_dt(this.id,this.value)" maxlength="7" value="<?php echo date("Y-m",strtotime($r['expiry_date']));?>" placeholder="YYYY-MM" />
			</td>
			<td>
				<?php echo ($r['recpt_quantity']/$itm['strip_quantity'])." + ".($r['free_qnt']/$itm['strip_quantity']);?>
				<input type="hidden" class="span1 qnt" id="qnt<?php echo $j;?>" value="<?php echo $r['recpt_quantity'];?>" />
			</td>
			<td>
				<input type="text" class="span1 mrp" id="mrp<?php echo $j;?>" onkeyup="chk_dec(this.value,this.id)" value="<?php echo number_format($r['recpt_mrp']*$itm['strip_quantity'], 2, '.', '');?>" placeholder="MRP" />
			</td>
			<td>
				<select class="span1" id="gst<?php echo $j;?>" onchange="calc_amount(this,'<?php echo $j;?>')">
					<?php
					$gq=mysqli_query($link,"SELECT * FROM `gst_percent_master`");
					while($gr=mysqli_fetch_assoc($gq))
					{
					?>
					<option value="<?php echo $gr['gst_per'];?>" <?php if($gr['gst_per']==$gst_per){echo "selected='selected'";}?>><?php echo $gr['gst_per'];?></option>
					<?php
					}
					?>
				</select>
				<input type="hidden" class="span1 gst_amt" id="gst_amt<?php echo $j;?>" value="<?php echo $r['gst_amount'];?>" />
			</td>
			<td>
				<input type="text" class="span1 cost" id="cost<?php echo $j;?>" onkeyup="chk_dec(this.value,this.id);calc_amount(this,'<?php echo $j;?>')" value="<?php echo number_format($r['cost_price'],2,'.','');?>" placeholder="cost Price" />
				<input type="hidden" class="span1 cost_amt" id="cost_amt<?php echo $j;?>" value="<?php echo $r['item_amount'];?>" />
			</td>
			<td>
				<input type="text" class="span1 dis_per" id="dis_per<?php echo $j;?>" onkeyup="chk_dec(this.value,this.id);calc_amount(this,'<?php echo $j;?>')" value="<?php echo $dis_per;?>" placeholder="Discount %" />
				<input type="hidden" class="span1 dis_amt" id="dis_amt<?php echo $j;?>" value="<?php echo $r['dis_amt'];?>" />
			</td>
			<td id="amt<?php echo $j;?>">
				<?php echo number_format($r['item_amount'],2,'.','');?>
			</td>
		</tr>
		<?php
		$tot+=$r['item_amount'];
		$dis+=$r['dis_amt'];
		$gst+=$r['gst_amount'];
		$j++;
	}
	$v=mysqli_fetch_assoc(mysqli_query($link,"SELECT `tcs_amt`,`adjust_amt`,`credit_amt` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$rcv'"));
	?>
		<tr>
			<td colspan="8"></td>
			<th>Sub Total</th>
			<td id="all_tot"><?php echo number_format($tot,2);?></td>
		</tr>
		<tr>
			<td colspan="8"></td>
			<th>Discount</th>
			<td id="all_dis"><?php echo number_format($dis,2);?></td>
		</tr>
		<tr>
			<td colspan="8"></td>
			<th>GST</th>
			<td id="all_gst"><?php echo number_format($gst,2);?></td>
		</tr>
		<tr>
			<td colspan="8"></td>
			<th>Credit Note</th>
			<td id=""><input type="text" class="span2" id="credit" value="<?php echo $v['credit_amt'];?>" onkeyup="chk_dec(this.value,this.id);calc_net()" placeholder="Credit Note" /></td>
		</tr>
		<tr>
			<td colspan="8"></td>
			<th>TCS</th>
			<td id=""><input type="text" class="span2" id="tcs" value="<?php echo $v['tcs_amt'];?>" onkeyup="chk_dec(this.value,this.id);calc_net()" placeholder="TCS" /></td>
		</tr>
		<tr>
			<td colspan="8"></td>
			<th>Adjust</th>
			<td id=""><input type="text" class="span2" id="adjust" value="<?php echo $v['adjust_amt'];?>" onkeyup="chk_dec(this.value,this.id);calc_net()" placeholder="Adjust" /></td>
		</tr>
		<tr>
			<td colspan="8"></td>
			<th>Net Amount</th>
			<td id="all_net"><?php echo number_format($tot+$gst-$v['credit_amt']+$v['tcs_amt']-$v['adjust_amt'],2);?></td>
		</tr>
		<tr>
			<td colspan="10" style="text-align:center;">
				<button type="button" class="btn btn-danger" id="canc" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="sav" onclick="item_update()">Done</button>
			</td>
		</tr>
	<?php
	}
	else
	{
	?>
		<tr>
			<th colspan="9" style="text-align:center;">You are not an admin</th>
		</tr>
	<?php
	}
	?>
	</table>
	<style>
	#myModal
	{
		width: 90%;
		left: 25%;
	}
	</style>
	<?php
}

if($type==10)
{
	$i=0;
	$ord=$_POST['ord'];
	$rcv=$_POST['rcv'];
	$all=$_POST['all'];
	$credit=$_POST['credit'];
	$tcs=$_POST['tcs'];
	$adjust=$_POST['adjust'];
	$txt="";
	$bill=mysqli_fetch_assoc(mysqli_query($link,"SELECT `supp_code`, `bill_no`, `adjust_amt` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$rcv'"));
	$txt="\nSELECT `supp_code`, `bill_no` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$rcv'";
	$al=explode("#_#",$all);
	foreach($al as $a)
	{
		$v=explode("@@",$a);
		$sl=$v[0];
		$exp_dt=$v[1]."-30";
		$exp_dt=date("Y-m-t", strtotime($exp_dt));
		$mrp=$v[2];
		$gst_per=$v[3];
		$gst_amt=$v[4];
		$cost=$v[5];
		$cost_price=$cost;
		$itm_amt=$v[6];
		$dis_per=$v[7];
		$dis_amt=$v[8];
		
		if($sl)
		{
			//echo $sl."-";
			$d=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_id`, `recpt_quantity`, `batch_no` FROM `inv_main_stock_received_detail` WHERE `slno`='$sl'"));
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`strip_quantity` FROM `item_master` WHERE `item_id`='$d[item_id]'"));
			$txt.="\nSELECT `item_id`, `recpt_quantity`, `batch_no` FROM `inv_main_stock_received_detail` WHERE `slno`='$sl'";
			$mrp=($mrp/$itm['strip_quantity']);
			$cost=($cost/$itm['strip_quantity']);
			$itm_amt=($cost*$d['recpt_quantity']);
			$dis_amt=(($itm_amt*$dis_per)/100);
			$itm_amt=($itm_amt-$dis_amt);
			mysqli_query($link,"UPDATE `inv_main_stock_received_detail` SET `expiry_date`='$exp_dt', `recpt_mrp`='$mrp', `recept_cost_price`='$cost', `cost_price`='$cost_price', `sale_price`='$sale_price', `item_amount`='$itm_amt', `dis_per`='$dis_per', `dis_amt`='$dis_amt', `gst_per`='$gst_per', `gst_amount`='$gst_amt' WHERE `slno`='$sl'");
			$txt.="\nUPDATE `inv_main_stock_received_detail` SET `expiry_date`='$exp_dt', `recpt_mrp`='$mrp', `recept_cost_price`='$cost', `cost_price`='$cost_price', `sale_price`='$sale_price', `item_amount`='$itm_amt', `dis_per`='$dis_per', `dis_amt`='$dis_amt', `gst_per`='$gst_per', `gst_amount`='$gst_amt' WHERE `slno`='$sl'";
			mysqli_query($link,"UPDATE `inv_maincurrent_stock` SET `exp_date`='$exp_dt' WHERE `item_id`='$d[item_id]' AND `batch_no`='$d[batch_no]'");
			$txt.="\nUPDATE `inv_maincurrent_stock` SET `exp_date`='$exp_dt' WHERE `item_id`='$d[item_id]' AND `batch_no`='$d[batch_no]'";
			$i++;
		}
	}
	if($i)
	{
		$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifnull(SUM(`item_amount`),0) AS `item_amount`, ifnull(SUM(`gst_amount`),0) AS `gst_amount`, ifnull(SUM(`dis_amt`),0) AS `dis_amt` FROM `inv_main_stock_received_detail` WHERE `rcv_no`='$rcv'"));
		$txt.="\nSELECT ifnull(SUM(`item_amount`),0) AS `item_amount`, ifnull(SUM(`gst_amount`),0) AS `gst_amount`, ifnull(SUM(`dis_amt`),0) AS `dis_amt` FROM `inv_main_stock_received_detail` WHERE `rcv_no`='$rcv'";
		if($det)
		{
			//$net_amt=$det['item_amount']+$det['gst_amount']-$bill['adjust_amt'];
			$net_amt=($det['item_amount']+$det['gst_amount']-$credit+$tcs-$adjust);
			//mysqli_query($link,"UPDATE `inv_main_stock_received_master` SET `bill_amount`='$det[item_amount]', `gst_amt`='$det[gst_amount]', `dis_amt`='$det[dis_amt]', `net_amt`='$net_amt' WHERE `order_no`='$ord' AND `receipt_no`='$rcv'");
			mysqli_query($link,"UPDATE `inv_main_stock_received_master` SET `bill_amount`='$det[item_amount]', `gst_amt`='$det[gst_amount]', `dis_amt`='$det[dis_amt]', `net_amt`='$net_amt', `tcs_amt`='$tcs', `adjust_amt`='$adjust', `credit_amt`='$credit' WHERE `receipt_no`='$rcv'");
			$txt.="\nUPDATE `inv_main_stock_received_master` SET `bill_amount`='$det[item_amount]', `gst_amt`='$det[gst_amount]', `dis_amt`='$det[dis_amt]', `net_amt`='$net_amt', `tcs_amt`='$tcs', `adjust_amt`='$adjust', `credit_amt`='$credit' WHERE `receipt_no`='$rcv'";
			
			$pay=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `inv_supplier_transaction` WHERE `process_no`='$bill[bill_no]' AND `supp_code`='$bill[supp_code]' AND `type`='1'"));
			$txt.="\nSELECT `slno` FROM `inv_supplier_transaction` WHERE `process_no`='$bill[bill_no]' AND `supp_code`='$bill[supp_code]' AND `type`='1'";
			$last_pay=mysqli_fetch_assoc(mysqli_query($link,"SELECT `balance`, `balance_amt` FROM `inv_supplier_transaction` WHERE `slno`<'$pay[slno]' AND `supp_code`='$bill[supp_code]' ORDER BY `slno` DESC LIMIT 0,1"));
			$txt.="\nSELECT `balance`, `balance_amt` FROM `inv_supplier_transaction` WHERE `slno`<'$pay[slno]' AND `supp_code`='$bill[supp_code]' ORDER BY `slno` DESC LIMIT 0,1";
			$balance=$last_pay['balance_amt'];
			$final_balance=$balance+$net_amt;
			mysqli_query($link,"UPDATE `inv_supplier_transaction` SET `debit_amt`='$net_amt', `credit_amt`='$credit', `tcs_amt`='$tcs', `balance`='$last_pay[balance]', `balance_amt`='$final_balance' WHERE `slno`='$pay[slno]'");
			$txt.="\nUPDATE `inv_supplier_transaction` SET `debit_amt`='$net_amt', `credit_amt`='$credit', `tcs_amt`='$tcs', `balance`='$last_pay[balance]', `balance_amt`='$final_balance' WHERE `slno`='$pay[slno]'";
			
			$qq=mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `slno`>'$pay[slno]' AND `supp_code`='$bill[supp_code]'");
			$txt.="\nSELECT * FROM `inv_supplier_transaction` WHERE `slno`>'$pay[slno]' AND `supp_code`='$bill[supp_code]'";
			while($rr=mysqli_fetch_assoc($qq))
			{
				mysqli_query($link,"UPDATE `inv_supplier_transaction` SET `balance`='$balance', `balance_amt`='$final_balance' WHERE `slno`='$rr[slno]'");
				$txt.="\nUPDATE `inv_supplier_transaction` SET `balance`='$balance', `balance_amt`='$final_balance' WHERE `slno`='$rr[slno]'";
				$balance=$rr['balance'];
				$final_balance+=$rr['debit_amt']-$rr['credit_amt']-$rr['adjust_amt']+$rr['balance'];
			}
		}
		$txt.="\n---------------------------------------------------------------------------------------------------";
		text_query($txt,"receive_adjust.txt");
		echo "Done";
	}
	else
	{
		echo "Error";
	}
}

if($type==11)
{
	$val=mysqli_real_escape_string($link,$_POST['val']);
	$ph=1;
	if($val)
	{
		$q="select distinct b.`item_id` AS `item_code` from `item_master` a, `inv_maincurrent_stock` b where";
		$q.=" a.item_id=b.item_id and a.item_name!='' and a.item_name like '$val%'";
		$q.=" or a.item_id=b.item_id AND a.item_name!='' and a.item_id like '$val%'";
		$q.=" or a.item_id=b.item_id AND a.item_name!='' and a.short_name like '$val%'";
		$q.=" order by a.item_name limit 0,30";
	}
	else
	{
		$q="select distinct b.`item_id` AS `item_code` from `item_master` a, `inv_maincurrent_stock` b where";
		$q.=" a.item_id=b.item_id and a.item_name!=''";
		$q.=" order by a.item_name limit 0,30";
	}
	//echo $q;
	$d=mysqli_query($link, $q);
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Code</th><th>Item Name</th><th>Stock</th><th>Rack No</th>
		</tr>
	<?php
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_id`,`item_name`,`item_type_id`,`gst`,`rack_no`,`strip_quantity` FROM `item_master` WHERE `item_id`='$d1[item_code]'"));
		$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`closing_stock`),0) AS stock FROM `inv_maincurrent_stock` WHERE `item_id`='$d1[item_code]'"));
		//$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`quantity`),0) AS stock FROM `ph_stock_master` WHERE `item_code`='$d1[item_code]' AND `substore_id`='$ph'"));
		$gst=explode(".",$itm['gst']);
		$gst=$gst[0];
		$i_type="";
		if($itm['item_type_id'])
		{
			$i_typ=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_type_name` FROM `item_type_master` WHERE `item_type_id`='$itm[item_type_id]'"));
			$i_type=" <small style='float:right;'><b><i>(".$i_typ['item_type_name'].")</i></b></small>";
		}
		?>
		<tr onclick="doc_load('<?php echo $itm['item_id'];?>','<?php echo mysqli_real_escape_string($link,$itm['item_name']);?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
			<td><?php echo $itm['item_id'];?></td>
			<td><?php echo $itm['item_name'].$i_type;?>
				<div <?php echo "id=dvdoc".$i;?> style="display:none;">
				<?php echo "#".$itm['item_id']."#".$itm['item_name']."#".$gst;?>
				</div>
			</td>
			<td><?php echo $stk['stock']/$itm['strip_quantity'];?></td>
			<td><?php echo $itm['rack_no'];?></td>
		</tr>
	<?php
		$i++;
	}
	?>
	</table>
	<?php
}

if($type==12)
{
	$val=$_POST['val'];
	$rcv=$_POST['rcv'];
	$ord=$_POST['ord'];
	$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_main_stock_received_master WHERE receipt_no='$rcv'"));
	$splr=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_master where id='$qrcv[supp_code]'"));
	$qemname=mysqli_fetch_array(mysqli_query($link,"select a.name from employee a,inv_main_stock_received_master b where a.emp_id=b.user and b.receipt_no='$rcv'"));
	if($splr['igst']==1)
	{
		$gsttxt="IGST";
		$gsttxt1="IGST";
	}
	else
	{
		$gsttxt="GST";
		$gsttxt1="CGST";
		$gsttxt2="SGST";
	}
	?>
	<table width="100%">
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
				<td style="font-weight:bold;font-size:13px">Item Code</td>
				<td style="font-weight:bold;font-size:13px">Item Name</td>
				<td style="font-weight:bold;font-size:13px">Batch No</td>
				<td style="font-weight:bold;font-size:13px">Exp Date</td>
				<td align="right" style="font-weight:bold;font-size:13px">Pkd</td>
				<td align="right" style="font-weight:bold;font-size:13px">MRP</td>
				<td align="right" style="font-weight:bold;font-size:13px">Rate</td>
				<td align="right" style="font-weight:bold;font-size:13px"><?php echo $gsttxt;?> %</td>
				<td align="right" style="font-weight:bold;font-size:13px">Dis %</td>
				<td align="right" style="font-weight:bold;font-size:13px">Qnty.</td>
				<td align="right" style="font-weight:bold;font-size:13px">Free.</td>
				<td align="right" style="font-weight:bold;font-size:13px">Amount.</td>
           </tr>
             <?php 
              $i=1;
              $tot=0;
              
              
			  //$qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name from inv_main_stock_received_detail a,item_master b,inv_main_stock_received_master c WHERE c.receipt_no='$rcv' and a.item_id=b.item_id and a.rcv_no=c.receipt_no and a.bill_no=c.bill_no and c.supp_code='$qrcv[supp_code]' and c.recpt_date='$qrcv[recpt_date]' and b.`item_name` like '$val%'");  //  ORDER BY b.item_name
			  $qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name from inv_main_stock_received_detail a,item_master b WHERE a.item_id=b.item_id and a.rcv_no='$rcv' and b.`item_name` like '$val%'");  //  ORDER BY b.item_name
			  
			  while($qrslctitm1=mysqli_fetch_array($qrslctitm))
			  {
				$vitemamount=0;  
				//$vitemamount=$qrslctitm1['recpt_quantity']*$qrslctitm1['recept_cost_price'];  
				$vitemamount=$qrslctitm1['item_amount'];
				$vitmttl=$vitmttl+$vitemamount;
				$vgstamt=$vgstamt+$qrslctitm1['gst_amount'];
			
			 ?>
             <tr class="line" onclick="chk(this)">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_id'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_name'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['batch_no'];?></td>
					<td style="font-size:13px"><?php echo date("Y-m", strtotime($qrslctitm1['expiry_date']));?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['strip_quantity'];?></td>
					<td align="right" style="font-size:13px"><?php echo number_format($qrslctitm1['recpt_mrp']*$qrslctitm1['strip_quantity'],2);?></td>
					<!--<td align="right" style="font-size:13px"><?php echo number_format(($qrslctitm1['item_amount']/$qrslctitm1['recpt_quantity'])*$qrslctitm1['strip_quantity'],2);?></td>-->
					<td align="right" style="font-size:13px"><?php echo number_format(($qrslctitm1['cost_price']),2);?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['gst_per'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['dis_per'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['recpt_quantity']/$qrslctitm1['strip_quantity'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['free_qnt'];?></td>
					<td align="right" style="font-size:13px"><?php echo number_format($vitemamount,2);?></td>
                
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			
			  /*$cgst=$vgstamt/2;
			  $tot1=$vitmttl+$cgst+$cgst;
			  $tot=round($tot1);*/
			  
			  $cgst=$vgstamt;
			  //$tot1=$vitmttl+$cgst+$qrcvcharge['delivery_charge']-$qrcv['dis_amt']-$qrcv['adjust_amt'];
			  $tot1=$vitmttl+$cgst+$qrcvcharge['delivery_charge']-$qrcv['adjust_amt']+$qrcv['tcs_amt']-$qrcv['credit_amt'];
			  $tot=round($tot1);
			  ?>
	<tr class="line">   
			  
		<td colspan="12" align="right" style="font-size:13px;font-weight:bold"><?php if($qrcv['dis_amt']>0){echo "( After Discount : ".$qrcv['dis_amt']." ) &nbsp;";}?> Total :</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($vitmttl,2);?></td>
	</tr>

	<?php
	if($qrcv['dis_amt']>0)
	{
	?>
	<!--<tr class="line">   
		<td colspan="11" align="right" style="font-size:13px;font-weight:bold">Discount:</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcv['dis_amt'],2);?></td>

	</tr>-->
	<?php
	}
	if($splr['igst']==1)
	{
	?>
	<tr class="line">   
		<td colspan="12" align="right" style="font-size:13px;font-weight:bold"><?php echo $gsttxt1;?> :</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($cgst,2);?></td>

	</tr>
	<?php
	}
	else
	{
	?>
	<tr class="line">   
		<td colspan="12" align="right" style="font-size:13px;font-weight:bold"><?php echo $gsttxt1;?> :</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format(($cgst/2),2);?></td>

	</tr>
	<tr class="line">   
		<td colspan="12" align="right" style="font-size:13px;font-weight:bold"><?php echo $gsttxt2;?> :</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format(($cgst/2),2);?></td>

	</tr>
	<?php
	}
	?>
	<!--<tr class="line">   
		<td colspan="8" align="right" style="font-size:13px;font-weight:bold">CGST :</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($cgst,2);?></td>

	</tr>

	<tr class="line">   
		<td colspan="8" align="right" style="font-size:13px;font-weight:bold">SGST :</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($cgst,2);?></td>

	</tr>-->
	<!--<tr class="line">   
		<td colspan="10" align="right" style="font-size:13px;font-weight:bold">Transport Charge :</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcvcharge['delivery_charge'],2);?></td>

	</tr>
	-->
	<?php
	if($qrcv['credit_amt']>0)
	{
	?>
	<tr class="line">   
		<td colspan="12" align="right" style="font-size:13px;font-weight:bold">Credit Note:</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcv['credit_amt'],2);?></td>

	</tr>
	<?php
	}
	if($qrcv['tcs_amt']>0)
	{
	?>
	<tr class="line">   
		<td colspan="12" align="right" style="font-size:13px;font-weight:bold">TCS:</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcv['tcs_amt'],2);?></td>

	</tr>
	<?php
	}
	if($qrcv['adjust_amt']>0)
	{
	?>
	<tr class="line">   
		<td colspan="12" align="right" style="font-size:13px;font-weight:bold">Adjust:</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcv['adjust_amt'],2);?></td>

	</tr>
	<?php
	}
	?>
	<tr class="line">   
		<td colspan="12" align="right" style="font-size:13px;font-weight:bold">Net Amount(Rounded) :</td>
		<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($tot,2);?></td>

	</tr>
	<tr class="no_line">
		<td style="font-size:13px;font-weight:bold;">GST %</td>
		<td style="font-size:13px;font-weight:bold;text-align:right;">Taxable Amount</td>
		<td style="font-size:13px;font-weight:bold;text-align:right;">Gst Amount</td>
		<td colspan="9" style="font-size:13px"></td>
	</tr>
	<?php
	$all_gst=array(0,5,12,18,28);
	foreach($all_gst as $gst)
	{
	$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM inv_main_stock_received_detail WHERE `rcv_no`='$rcv' and `gst_per`='$gst'"));
	?>
	<tr class="no_line">
		<td style="font-size:13px;"><?php echo $gst." %";?></td>
		<td style="font-size:13px;text-align:right;"><?php echo number_format($qamt['maxamt'],2);?></td>
		<td style="font-size:13px;text-align:right;"><?php echo number_format($qamt['maxgst'],2);?></td>
		<td colspan="9" style="font-size:13px"></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td colspan="12">&nbsp;</td>
	</tr>
	<tr class="no_line">
		<td colspan="5" style="font-size:13px">Received By : <?php echo $qemname['name'];?></td>
		
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:13px;text-align:right;">Authourised Signatory </td>
		
	</tr>

	</table>
	<?php
}

if($type==13)
{
	$r_no=base64_decode($_POST['r_no']);
	$user=$_POST['user'];
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Item Name</th><th>Batch No</th><th>Qnt</th><th>Free</th><th>Mrp</th><th>Rate</th><th>Amount</th><th>Bill No</th>
		</tr>
		<?php
		$j=1;
		$q=mysqli_query($link,"SELECT * FROM `inv_item_return_supplier_detail` WHERE `returnr_no`='$r_no'");
		while($r=mysqli_fetch_assoc($q))
		{
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		?>
		<tr class="ret_tr">
			<td><?php echo $j;?><input type="hidden" value="<?php echo $r['slno'];?>" /></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo $r['quantity'];?><input type="hidden" id="qnt<?php echo $r['slno'];?>" value="<?php echo $r['quantity'];?>" /></td>
			<td><?php echo $r['free_qnt'];?></td>
			<td>
				<input type="text" class="span2 mrp" id="mrp<?php echo $r['slno'];?>" onkeyup="chk_dec(this.value,this.id)" value="<?php echo $r['recpt_mrp'];?>" placeholder="MRP" />
			</td>
			<td>
				<input type="text" class="span2 cost" id="cost<?php echo $r['slno'];?>" onkeyup="chk_dec(this.value,this.id);calc_ret_amt(this.value,'<?php echo $r['slno'];?>')" value="<?php echo $r['recept_cost_price'];?>" placeholder="Rate" />
			</td>
			<td>
				<input type="hidden" class="span2 amt" id="amt<?php echo $r['slno'];?>" onkeyup="chk_dec(this.value,this.id)" value="<?php echo $r['item_amount'];?>" /><span id="amt_txt<?php echo $r['slno'];?>"><?php echo $r['item_amount'];?></span>
			</td>
			<td>
				<input type="text" class="span2 bill" id="bill<?php echo $r['slno'];?>" value="<?php echo $r['bill_no'];?>" placeholder="Bill No" />
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
		<tr>
			<td colspan="9" style="text-align:center;">
				<button type="button" class="btn btn-info" id="sav" onclick="ret_bill_save('<?php echo base64_encode($r_no);?>')">Done</button>
				<button type="button" class="btn btn-danger" id="canc" data-dismiss="modal">Cancel</button>
			</td>
		</tr>
	</table>
	<style>
		#myModal
		{
			width: 90%;
			left: 25%;
		}
	</style>
	<?php
}

if($type==14)
{
	$r_no=base64_decode($_POST['r_no']);
	$all=$_POST['all'];
	
	$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_item_return_supplier_master` WHERE `returnr_no`='$r_no'"));
	if($chk['del']==0)
	{
		$tot=0;
		$g_amt=0;
		$al=explode("#_#",$all);
		foreach($al as $a)
		{
			$v=explode("@@",$a);
			$sl=$v[0];
			$mrp=$v[1];
			$cost=$v[2];
			$bill=$v[3];
			if($sl)
			{
				$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_item_return_supplier_detail` WHERE `slno`='$sl'"));
				//~ $itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$det[item_id]'"));
				//~ $mrp=($mrp/$itm['strip_quantity']);
				//~ $cost=($cost/$itm['strip_quantity']);
				$amt=($cost*$det['quantity']);
				$tot+=$amt;
				$gst_amt=(($amt*$det['gst_per'])/100);
				$g_amt+=$gst_amt;
				mysqli_query($link,"UPDATE `inv_item_return_supplier_detail` SET `recpt_mrp`='$mrp',`recept_cost_price`='$cost',`item_amount`='$amt',`gst_amount`='$gst_amt',`bill_no`='$bill' WHERE `slno`='$sl'");
			}
		}
		$net_amt=($tot+$g_amt);
		mysqli_query($link,"UPDATE `inv_item_return_supplier_master` SET `amount`='$tot',`gst_amount`='$g_amt',`net_amount`='$net_amt' WHERE `returnr_no`='$r_no'");
		echo "Done";
	}
	else
	{
		echo "Cannot updated";
	}
}

if($type==999)
{
	$supp=$_POST['supp'];
}
?>
