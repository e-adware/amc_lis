<?php
include("../../includes/connection.php");
require("../../includes/global.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");

$rupees_symbol="&#x20b9; ";
$type=$_POST['type'];

if($type==1)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$supp=$_POST['supp'];
	$challan_no=$_POST['challan_no'];
	$user=$_POST['user'];
	$lv=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$user'"));
	$chalan="";
	$qry="SELECT * FROM `ph_challan_receipt_master` WHERE `supp_code`!='0' AND `stat`='0'";
	if($challan_no)
	{
		$qry.=" AND `challan_no`='$challan_no'";
	}
	if($supp)
	{
		$qry.=" AND `supp_code`='$supp'";
		$chalan="chalan";
	}
	$qry.=" AND `date` BETWEEN '$fdate' AND '$tdate'";
	
	$q=mysqli_query($link,$qry);
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Supplier</th>
			<th>Bill No.</th>
			<th>Challan No.</th>
			<th>Receipt Date</th>
			<th>Amount</th>
			<th>Receive</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_array($q))
		{
			$sp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[supp_code]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $sp["name"];?></td>
			<td><?php echo $r["bill_no"];?></td>
			<td>
				<?php
				if($supp)
				{
					echo "<label class='$chalan' style='$check_show'><span><input type='checkbox' id='".$r['order_no']."' class='form-control checker' /> ".$r["challan_no"]."</span><span></span></label>";
				}
				else
				{
					echo $r['challan_no'];
				}
				if($lv['levelid']==1)
				{
				?>
				<span style="float:right;" class="btn_edit" onclick="edit_challan('<?php echo base64_encode($r["order_no"]);?>')"><i class="icon-edit icon-large"></i></span>
				<?php
				}
				?>
			</td>
			<td><?php echo convert_date($r["date"]);?></td>
			<td><?php echo $rupees_symbol.$r["net_amt"];?></td>
			<td>
				<button type="button" class="btn btn-primary btn-mini" onclick="challan_print_billwise('<?php echo $r['order_no'];?>')">View</button>
				<button type="button" class="btn btn-success btn-mini" id="btn<?php echo $r['order_no'];?>" onclick="challan_received('<?php echo $r['order_no'];?>')">Receive</button>
				<?php
				if($lv['levelid']==1)
				{
				?>
				<button type="button" class="btn btn-info btn-mini" onclick="add_challan_items('<?php echo base64_encode($r['order_no']);?>')">Add Items</button>
				<?php
				}
				?>
			</td>
		</tr>
		<?php
		$j++;
		}
		if($supp)
		{
		?>
		<tr>
			<th colspan="3"></th>
			<th>
				<button type="button" class="btn btn-primary btn-block" onclick="generate_bill()">Generate Bill</button>
			</th>
			<th colspan="3"></th>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}

if($type==2)
{
	$ord=$_POST['ord'];
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `challan_no`,`bill_date`,`bill_no`,`stat` FROM `ph_challan_receipt_master` WHERE `order_no`='$ord'"));
	$disb="";
	if($det['stat']>0)
	{
		$disb="disabled";
	}
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th colspan="2" style="text-align:center;">Bill Generation Process</th>
		</tr>
		<tr>
			<th>Challan No</th>
			<td><?php echo $det['challan_no'];?></td>
		</tr>
		<tr>
			<th>Bill No</th>
			<td>
				<input type="text" class="span2" id="bill_no" value="<?php echo $det['bill_no'];?>" placeholder="Bill No" />
			</td>
		</tr>
		<tr>
			<th>Bill Date</th>
			<td>
				<input type="text" class="span2" id="bill_dt" value="<?php echo $det['bill_date'];?>" placeholder="Bill Date" />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button type="button" class="btn btn-primary" id="sav" onclick="save_bill('<?php echo base64_encode($ord);?>')" <?php echo $disb;?>>Done</button>
				<button type="button" class="btn btn-danger" id="canc" data-dismiss="modal">Cancel</button>
			</td>
		</tr>
	</table>
	<script>
		$("#bill_dt").datepicker({dateFormat: 'yy-mm-dd', maxDate: '0'});
	</script>
	<?php
}

if($type==3)
{
	$ord=base64_decode($_POST['ord']);
	$bill_no=mysqli_real_escape_string($link,$_POST['bill_no']);
	$bill_dt=$_POST['bill_dt'];
	$user=$_POST['user'];
	/*
	$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`slno`) as tot FROM `ph_purchase_receipt_master`"));
	$bill_tot_num=$bill_no_qry["tot"];
	$bill_num=$bill_tot_num+1;
	$orderno="RCV".str_pad($bill_num, 6, 0, STR_PAD_LEFT);
	*/
	$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`slno`) as tot FROM `inv_main_stock_received_master`"));
	$bill_tot_num=$bill_no_qry["tot"];
	$bill_num=$bill_tot_num+1;
	$orderno="RCV".str_pad($bill_num, 6, 0, STR_PAD_LEFT);
	
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_challan_receipt_master` WHERE `order_no`='$ord'"));
	
	//if(mysqli_query($link,"INSERT INTO `ph_purchase_receipt_master`(`order_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$orderno','$bill_dt','$date','$det[bill_amount]','$det[gst_amt]','$det[dis_amt]','$det[net_amt]','$det[supp_code]','$bill_no','$det[user]','$time','$det[adjust_type]','$det[adjust_amt]')"))
	if(mysqli_query($link,"INSERT INTO `inv_main_stock_received_master`(`order_no`, `receipt_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`, `adjust_amt`, `goods_rcv_no`, `del`) VALUES ('$det[order_no]','$orderno','$bill_dt','$date','$det[bill_amount]','$det[gst_amt]','$det[dis_amt]','$det[net_amt]','$det[supp_code]','$bill_no','$det[user]','$time','$det[adjust_type]','$det[adjust_amt]','','0')"))
	{
		//$txt="\nINSERT INTO `ph_purchase_receipt_master`(`order_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$orderno','$bill_dt','$date','$det[bill_amount]','$det[gst_amt]','$det[dis_amt]','$det[net_amt]','$det[supp_code]','$bill_no','$det[user]','$time','$det[adjust_type]','$det[adjust_amt]')";
		$txt="\nINSERT INTO `inv_main_stock_received_master`(`order_no`, `receipt_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`, `adjust_amt`, `goods_rcv_no`, `del`) VALUES ('$det[order_no]','$orderno','$bill_dt','$date','$det[bill_amount]','$det[gst_amt]','$det[dis_amt]','$det[net_amt]','$det[supp_code]','$bill_no','$det[user]','$time','$det[adjust_type]','$det[adjust_amt]','','0')";
		$balance_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$det[supp_code]' ORDER BY `date`,`slno` DESC LIMIT 0,1"));
		$txt.="\nSELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$det[supp_code]' ORDER BY `date`,`slno` DESC LIMIT 0,1";
		$final_balance=$balance_det['balance_amt']+$ttlamt;
		mysqli_query($link,"INSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$bill_no','$det[supp_code]','$det[net_amt]','0','0','$final_balance','1','$date','$time','$user')");
		$txt.="\nINSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$bill_no','$det[supp_code]','$det[net_amt]','0','0','$final_balance','1','$date','$time','$user')";
		
		$q=mysqli_query($link,"SELECT * FROM `ph_challan_receipt_details` WHERE `order_no`='$ord'");
		while($r=mysqli_fetch_assoc($q))
		{
			//mysqli_query($link,"INSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$bill_no','$r[item_id]','','$r[expiry_date]','$date','$r[recpt_quantity]','$r[free_qnt]','$r[recept_batch]','$r[supp_code]','$r[recpt_mrp]','$r[recept_cost_price]','$r[sale_price]','0','$r[item_amount]','$r[dis_per]','$r[dis_amt]','$r[gst_per]','$r[gst_amount]')");
			mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`order_no`, `rcv_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$det[order_no]','$orderno','$bill_no','$r[item_id]','','$r[expiry_date]','$date','$r[strip_quantity]','$r[recpt_quantity]','$r[free_qnt]','$r[recept_batch]','$r[supp_code]','$r[recpt_mrp]','$r[recept_cost_price]','$r[cost_price]','$r[sale_price]','$r[item_amount]','$r[dis_per]','$r[dis_amt]','$r[gst_per]','$r[gst_amount]')");
			//$txt.="\nINSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$bill_no','$r[item_id]','','$r[expiry_date]','$date','$r[recpt_quantity]','$r[free_qnt]','$r[recept_batch]','$r[supp_code]','$r[recpt_mrp]','$r[recept_cost_price]','$r[sale_price]','0','$r[item_amount]','$r[dis_per]','$r[dis_amt]','$r[gst_per]','$r[gst_amount]')";
			$txt.="\nINSERT INTO `inv_main_stock_received_detail`(`order_no`, `rcv_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$det[order_no]','$orderno','$bill_no','$r[item_id]','','$r[expiry_date]','$date','$r[strip_quantity]','$r[recpt_quantity]','$r[free_qnt]','$r[recept_batch]','$r[supp_code]','$r[recpt_mrp]','$r[recept_cost_price]','$r[cost_price]','$r[sale_price]','$r[item_amount]','$r[dis_per]','$r[dis_amt]','$r[gst_per]','$r[gst_amount]')";
		}
		
		mysqli_query($link,"UPDATE `ph_challan_receipt_master` SET `bill_no`='$bill_no', `stat`='1' WHERE `order_no`='$ord'");
		mysqli_query($link,"UPDATE `ph_challan_receipt_details` SET `bill_no`='$bill_no' WHERE `order_no`='$ord'");
		
		echo "1@_@".base64_encode($orderno);
	}
	else
	{
		echo "2@_@0";
	}
}

if($type==4)
{
	$ord=base64_decode($_POST['ord']);
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `challan_no`,`bill_date`,`supp_code` FROM `ph_challan_receipt_master` WHERE `order_no`='$ord'"));
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th colspan="2" style="text-align:center;">Challan Edit</th>
		</tr>
		<tr>
			<th>Challan No</th>
			<td>
				<input type="text" class="span2" id="challan_no" value="<?php echo $det['challan_no'];?>" placeholder="Challan No" />
			</td>
		</tr>
		<tr>
			<th>Supplier</th>
			<td>
				<select id="supp" class="span4">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `id`,`name` FROM `inv_supplier_master` ORDER BY `name`");
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
			<th>Challan Date</th>
			<td>
				<input type="text" class="span2" id="challan_dt" value="<?php echo $det['bill_date'];?>" placeholder="Challan Date" />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button type="button" class="btn btn-primary" id="sav" onclick="save_challan_edit('<?php echo base64_encode($ord);?>')" <?php echo $disb;?>>Done</button>
				<button type="button" class="btn btn-danger" id="canc" data-dismiss="modal">Cancel</button>
			</td>
		</tr>
	</table>
	<script>
		$("#challan_dt").datepicker({dateFormat: 'yy-mm-dd', maxDate: '0'});
	</script>
	<?php
}

if($type==5)
{
	$ord=base64_decode($_POST['ord']);
	$challan_no=mysqli_real_escape_string($link,$_POST['challan_no']);
	$supp=$_POST['supp'];
	$challan_dt=$_POST['challan_dt'];
	if(mysqli_query($link,"UPDATE `ph_challan_receipt_master` SET `challan_no`='$challan_no', `bill_date`='$challan_dt', `supp_code`='$supp' WHERE `order_no`='$ord'"))
	{
		mysqli_query($link,"UPDATE `ph_challan_receipt_details` SET `bill_no`='$challan_no', `supp_code`='$supp' WHERE `order_no`='$ord'");
		echo "Done";
	}
	else
	{
		echo "Error";
	}
}

if($type==6)
{
	$challans="";
	$stat=0;
	$all=$_POST['all'];
	$al=explode("@_@",$_POST['all']);
	foreach($al as $al)
	{
		$ord=$al;
		$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `challan_no`,`bill_date`,`bill_no`,`stat` FROM `ph_challan_receipt_master` WHERE `order_no`='$al'"));
		if($challans)
		{
			$challans.=", ".$det['challan_no'];
		}
		else
		{
			$challans=$det['challan_no'];
		}
		if($det['stat']>0)
		{
			$stat++;
		}
	}
	$sp=mysqli_fetch_assoc(mysqli_query($link,"SELECT `supp_code` FROM `ph_challan_receipt_master` WHERE `order_no`='$ord'"));
	$disb="";
	if($stat>0)
	{
		$disb="disabled";
	}
	?>
	<input type="hidden" id="chk_supp" value="<?php echo $sp['supp_code'];?>" />
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th colspan="2" style="text-align:center;">Bill Generation Process</th>
		</tr>
		<tr>
			<th>Challan No</th>
			<td><?php echo $challans;?></td>
		</tr>
		<tr>
			<th>Bill No</th>
			<td>
				<input type="text" class="span2" id="bill_no" value="<?php //echo $det['bill_no'];?>" placeholder="Bill No" />
			</td>
		</tr>
		<tr>
			<th>Bill Date</th>
			<td>
				<input type="text" class="span2" id="bill_dt" value="<?php //echo $det['bill_date'];?>" placeholder="Bill Date" />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button type="button" class="btn btn-primary" id="sav" onclick="generate_bill_confirm('<?php echo base64_encode($all);?>')" <?php echo $disb;?>>Done</button>
				<button type="button" class="btn btn-danger" id="canc" data-dismiss="modal">Cancel</button>
			</td>
		</tr>
	</table>
	<script>
		$("#bill_dt").datepicker({dateFormat: 'yy-mm-dd', maxDate: '0'});
	</script>
	<?php
}

if($type==7)
{
	$all=base64_decode($_POST['all']);
	$bill_no=mysqli_real_escape_string($link,$_POST['bill_no']);
	$bill_dt=mysqli_real_escape_string($link,$_POST['bill_dt']);
	$supp=$_POST['supp'];
	$user=$_POST['user'];
	
	$stat=0;
	$r_challans="";
	$al=explode("@_@",$all);
	foreach($al as $a)
	{
		$ord=$a;
		$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `challan_no`,`stat` FROM `ph_challan_receipt_master` WHERE `order_no`='$ord'"));
		if($det['stat']>0)
		{
			$stat++;
			if($r_challans)
			{
				$r_challans.=", ".$det['challan_no'];
			}
			else
			{
				$r_challans=$det['challan_no'];
			}
		}
	}
	
	if($stat>0)
	{
		echo "0@_@".$r_challans;
	}
	else
	{
		$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`slno`) as tot FROM `inv_main_stock_received_master`"));
		$bill_tot_num=$bill_no_qry["tot"];
		$bill_num=$bill_tot_num+1;
		$orderno="RCV".str_pad($bill_num, 6, 0, STR_PAD_LEFT);
		
		$txt="";
		if(mysqli_query($link,"INSERT INTO `inv_main_stock_received_master`(`order_no`, `receipt_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`, `adjust_amt`, `goods_rcv_no`, `del`) VALUES ('$orderno','$orderno','$bill_dt','$date','0','0','0','0','$supp','$bill_no','$user','$time','0','0','','0')"))
		{
			$txt="\nINSERT INTO `inv_main_stock_received_master`(`order_no`, `receipt_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`, `adjust_amt`, `goods_rcv_no`, `del`) VALUES ('$orderno','$orderno','$bill_dt','$date','0','0','0','0','$supp','$bill_no','$user','$time','0','0','','0')";
			$al=explode("@_@",$all);
			foreach($al as $a)
			{
				$ord=$a;
				$d_qry=mysqli_query($link,"SELECT * FROM `ph_challan_receipt_details` WHERE `order_no`='$ord'");
				while($det=mysqli_fetch_assoc($d_qry))
				{
					mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`order_no`, `rcv_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$ord','$orderno','$bill_no','$det[item_id]','','$det[expiry_date]','$date','$det[strip_quantity]','$det[recpt_quantity]','$det[free_qnt]','$det[recept_batch]','$supp','$det[recpt_mrp]','$det[recept_cost_price]','$det[cost_price]','$det[sale_price]','$det[item_amount]','$det[dis_per]','$det[dis_amt]','$det[gst_per]','$det[gst_amount]')");
					
					$txt.="\nINSERT INTO `inv_main_stock_received_detail`(`order_no`, `rcv_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$ord','$orderno','$bill_no','$det[item_id]','','$det[expiry_date]','$date','$det[strip_quantity]','$det[recpt_quantity]','$det[free_qnt]','$det[recept_batch]','$supp','$det[recpt_mrp]','$det[recept_cost_price]','$det[cost_price]','$det[sale_price]','$det[item_amount]','$det[dis_per]','$det[dis_amt]','$det[gst_per]','$det[gst_amount]')";
				}
				mysqli_query($link,"UPDATE `ph_challan_receipt_master` SET `bill_no`='$bill_no', `stat`='1' WHERE `order_no`='$ord'");
				mysqli_query($link,"UPDATE `ph_challan_receipt_details` SET `bill_no`='$bill_no' WHERE `order_no`='$ord'");
			}
			
			$amount=mysqli_fetch_assoc(mysqli_query($link,"SELECT IFNULL(SUM(`item_amount`),0) AS `item_amount`, IFNULL(SUM(`dis_amt`),0) AS `dis_amt`, IFNULL(SUM(`gst_amount`),0) AS `gst_amount` FROM `inv_main_stock_received_detail` WHERE `rcv_no`='$orderno'"));
			$net_amt=($amount['item_amount']+$amount['gst_amount']);
			
			mysqli_query($link,"UPDATE `inv_main_stock_received_master` SET `bill_amount`='$amount[item_amount]',`gst_amt`='$amount[gst_amount]',`dis_amt`='$amount[dis_amt]',`net_amt`='$net_amt' WHERE `receipt_no`='$orderno' AND `supp_code`='$supp' AND `bill_no`='$bill_no' AND `user`='$user'");
			
			$balance_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$supp' ORDER BY `date`,`slno` DESC LIMIT 0,1"));
			$txt.="\nSELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$supp' ORDER BY `date`,`slno` DESC LIMIT 0,1";
			$final_balance=$balance_det['balance_amt']+$net_amt;
			
			mysqli_query($link,"INSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$bill_no','$supp','$net_amt','0','0','$final_balance','1','$date','$time','$user')");
			$txt.="\nINSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$bill_no','$supp','$net_amt','0','0','$final_balance','1','$date','$time','$user')";
			
			echo "1@_@Done@_@".$orderno."@_@".$txt;
		}
		else
		{
			echo "2@_@Error@_@0";
		}
	}
}

if($type==8)
{
	$val=mysqli_real_escape_string($link,$_POST['val']);
	if($val)
	{
		$q="select distinct b.`item_id` AS `item_code` from `item_master` a, `inv_maincurrent_stock` b where";
		//$q.=" a.item_id=b.item_id and b.closing_stock>0 and a.item_name!='' and a.item_name like '$val%'";
		$q.=" a.item_id=b.item_id and a.item_name!='' and a.item_id like '$val%'";
		$q.=" or a.item_id=b.item_id and a.item_name!='' and a.item_name like '$val%'";
		$q.=" or a.item_id=b.item_id and a.item_name!='' and a.short_name like '$val%'";
		$q.=" order by a.item_name limit 0,30";
	}
	else
	{
		$q="select distinct b.`item_id` AS `item_code` from `item_master` a, `inv_maincurrent_stock` b where";
		//$q.=" a.item_id=b.item_id and b.closing_stock>0 and a.item_name!=''";
		$q.=" a.item_id=b.item_id and a.item_name!=''";
		$q.=" order by a.item_name limit 0,30";
	}
	//echo $q;
	$d=mysqli_query($link, $q);
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Code</th><th>Item Name</th><th>Rack No</th>
		</tr>
	<?php
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_id`,`item_name`,`item_type_id`,`gst`,`rack_no`,`strip_quantity` FROM `item_master` WHERE `item_id`='$d1[item_code]'"));
		//$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`closing_stock`),0) AS stock FROM `inv_maincurrent_stock` WHERE `item_id`='$d1[item_code]'"));
		//$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`quantity`),0) AS stock FROM `ph_stock_master` WHERE `item_code`='$d1[item_code]' AND `substore_id`='$ph'"));
		//~ $gst=explode(".",$itm['gst']);
		//~ $gst=$gst[0];
		$i_type="";
		if($itm['item_type_id'])
		{
			$i_typ=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_type_name` FROM `item_type_master` WHERE `item_type_id`='$itm[item_type_id]'"));
			$i_type=" <small style='float:right;'><b><i>(".$i_typ['item_type_name'].")</i></b></small>";
		}
		?>
		<tr onclick="item_load('<?php echo $itm['item_id'];?>','<?php echo $itm['item_name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
			<td><?php echo $itm['item_id'];?></td>
			<td><?php echo $itm['item_name'].$i_type;?>
				<div <?php echo "id=dvdoc".$i;?> style="display:none;">
				<?php echo "#".$itm['item_id']."#".$itm['item_name']."#";?>
				</div>
			</td>
			<td><?php echo $itm['rack_no'];?></td>
		</tr>
	<?php
		$i++;
	}
	?>
	</table>
	<?php
}

if($type==9)
{
	$itm=$_POST['itm'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$supp=$_POST['supp'];
	$challan_no=$_POST['challan_no'];
	$user=$_POST['user'];
	$lv=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$user'"));
	$chalan="";
	$qry="SELECT b.`challan_no`,a.`bill_no`,a.`item_id`,a.`expiry_date`,a.`strip_quantity`,a.`recpt_quantity`,a.`free_qnt`,a.`recept_batch`,a.`supp_code`,a.`recpt_mrp`,a.`recept_cost_price`,a.`cost_price`,a.`dis_per`,a.`gst_per`,b.`date` FROM `ph_challan_receipt_details` a, `ph_challan_receipt_master` b WHERE a.`order_no`=b.`order_no` AND b.`stat`='0' AND b.`bill_date` BETWEEN '$fdate' AND '$tdate' AND a.`item_id`='$itm'";
	if($challan_no)
	{
		//$qry.=" AND `challan_no`='$challan_no'";
	}
	if($supp)
	{
		//$qry.=" AND `supp_code`='$supp'";
		$chalan="chalan";
	}
	//$qry.=" AND `date` BETWEEN '$fdate' AND '$tdate'";
	//echo $qry;
	$q=mysqli_query($link,$qry);
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Supplier</th>
			<th>Challan No.</th>
			<th>Qnt</th>
			<th>Free</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th>MRP</th>
			<th>Cost</th>
			<th>Discount</th>
			<th>GST %</th>
			<th>Receipt Date</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_array($q))
		{
			$sp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[supp_code]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $sp["name"];?></td>
			<td><?php echo $r["challan_no"];?></td>
			<td><?php echo $r["recpt_quantity"]/$r["strip_quantity"];?></td>
			<td><?php echo $r["free_qnt"];?></td>
			<td><?php echo $r["recept_batch"];?></td>
			<td><?php echo $r["expiry_date"];?></td>
			<td><?php echo number_format($r["strip_quantity"]*$r["recpt_mrp"],2);?></td>
			<td><?php echo $r["cost_price"];?></td>
			<td><?php echo $r["dis_per"];?></td>
			<td><?php echo $r["gst_per"];?></td>
			<td><?php echo convert_date($r["date"]);?></td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
}

if($type==999)
{
	
}
?>
