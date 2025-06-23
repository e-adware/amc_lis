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
	$id=$_POST['id'];
	$supp=$_POST['supp'];
	$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`hsn_code`,`mrp`,`gst` FROM `item_master` WHERE `item_id`='$id'"));
	$sup_price=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_items` WHERE `supp_id`='$supp' AND `item_id`='$id'"));
	$gst=explode(".",$itm['gst']);
	$cost=$sup_price['cost_price'];
	//$gst_amt=$cost-($cost*(100/(100+$itm['gst'])));
	$gst_amt=(($cost*$itm['gst'])/100);
	$gst_amt=$gst_amt;
	echo $itm['item_name']."@@".$itm['hsn_code']."@@".$cost."@@".$gst[0]."@@".$gst_amt;
}

if($type==2)
{
	$supp=$_POST['supp'];
	$all=$_POST['all'];
	$final_gst=$_POST['final_gst'];
	$final_rate=$_POST['final_rate'];
	$user=$_POST['user'];
	
	$billnos=100;
		
	$date_str=explode("-", $date);
	$dis_year=date("Y");
	$dis_month=date("m");
	$dis_year_sm=date("y");

	//$c_m_y=$dis_year."-".$dis_month;
	$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`order_no`) as tot FROM `inv_purchase_order_master`"));
	$bill_num=$bill_no_qry["tot"];

	$bill_tot_num=$bill_num;

	if($bill_tot_num==0)
	{
		//$bill_no=$billnos+1;
		$bill_no=1;
	}else
	{
		//$bill_no=$billnos+$bill_tot_num+1;
		$bill_no=$bill_tot_num+1;
	}
	
	//$orderno="ORD".$bill_no.$dis_month.$dis_year_sm;
	$orderno="ORD".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
	if(mysqli_query($link,"INSERT INTO `inv_purchase_order_master`(`order_no`, `supplier_id`, `order_date`, `gst_amount`, `net_amount`, `stat`, `del`, `user`, `time`) VALUES ('$orderno','$supp','$date','$final_gst','$final_rate','0','0','$user','$time')"))
	{
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			if($a)
			{
				$v=explode("@@",$a);
				$itm=$v[0];
				$qnt=$v[1];
				$mrp=$v[2];
				$gst_per=$v[3];
				$gst_amt=$v[4];
				$net_amt=$v[5];
				if($itm && $qnt)
				{
					mysqli_query($link,"INSERT INTO `inv_purchase_order_details`(`order_no`, `item_id`, `mrp`, `gst`, `gst_amt`, `amount`, `supplier_id`, `order_qnt`, `order_date`, `stat`, `user`) VALUES ('$orderno','$itm','$mrp','$gst_per','$gst_amt','$net_amt','$supp','$qnt','$date','0','$user')");
				}
			}
		}
		echo "1";
	}
	else
	{
		echo "2";
	}
}

if($type==3)
{
	//$id=$_POST['id'];
	$qry=mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `stat`='0' ORDER BY `order_date` DESC");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Order No</th>
			<th>Supplier</th>
			<th>Order Date</th>
			<th>Amount</th>
			<th>User</th>
			<th>Action</th>
		</tr>
		<?php
		$j=1;
		while($res=mysqli_fetch_assoc($qry))
		{
			$supp=mysqli_fetch_assoc(mysqli_query($link,"select name from inv_supplier_master where id='$res[supplier_id]'"));
			$user=mysqli_fetch_assoc(mysqli_query($link,"select name from employee where emp_id='$res[user]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $res['order_no'];?></td>
			<td><?php echo $supp['name'];?></td>
			<td><?php echo convert_date($res['order_date']);?></td>
			<td><?php echo number_format($res['net_amount'],2);?></td>
			<td><?php echo $user['name'];?></td>
			<td>
				<?php
				if($res['approve']==0)
				{
				?>
				<button type="button" class="btn btn-primary btn-mini" onclick="view_ord('<?php echo $res['order_no'];?>')"><i class="icon-eye-open"></i> <b>View</b></button>
				<button type="button" class="btn btn-info btn-mini" onclick="edit_ord('<?php echo $res['order_no'];?>')"><i class="icon-edit"></i> <b>Edit</b></button>
				<?php
				}
				if($res['approve']==1)
				{
				?>
				<button type="button" class="btn btn-primary btn-mini" onclick="view_ord('<?php echo $res['order_no'];?>')"><i class="icon-eye-open"></i> <b>View</b></button>
				<button type="button" class="btn btn-success btn-mini" onclick="print_ord('<?php echo $res['order_no'];?>')"><i class="icon-print"></i> <b>Print</b></button>
				<?php
				}
				if($res['approve']==2)
				{
				?>
				<button type="button" class="btn btn-danger btn-block btn-mini" disabled="disabled"><i class="icon-remove"></i> <b>Cancelled</b></button>
				<?php
				}
				?>
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
	}
}

if($type==4)
{
	$ord=$_POST['ord'];
	$sid=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$ord'"));
	?>
	<input type="hidden" id="ord_id" value="<?php echo $ord;?>" />
	<input type="hidden" id="supp_id" value="<?php echo $sid['supplier_id'];?>" />
	<table class="table table-condensed table-bordered table-report" id="mytable">
		<tr>
			<th>#</th>
			<th>Description</th>
			<th>Quantity</th>
			<th>Rate</th>
			<th>GST %</th>
			<th>GST Amount</th>
			<th>Amount</th>
			<th width="5%">Remove</th>
		</tr>
	<?php
	$i=1;
	$gst_tot=0;
	$net_tot=0;
	$qry=mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord'");
	while($r=mysqli_fetch_assoc($qry))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`hsn_code`,`mrp`,`gst` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		$id=$r['item_id'];
		$bch="";
		$qnt=$r['order_qnt'];
		$mrp=$r['mrp'];
		$gst_per=explode(".",$r['gst']);
		$gst_per=$gst_per[0];
		$gst_amt=$r['gst_amt'];
		$amount=$r['amount'];
		$gst_tot+=$gst_amt;
		$net_tot+=$amount;
	?>
		<tr class="all_tr">
			<td><?php echo $i;?></td>
			<td><?php echo $itm['item_name'];?><input type='hidden' value="<?php echo $id;?>" class=''/><input type='hidden' value="<?php echo $id.$bch;?>" class='test_id'/></td>
			<td><?php echo $r['order_qnt'];?><input type='hidden' value="<?php echo $qnt;?>" /></td>
			<td><?php echo $r['mrp'];?><input type='hidden' value="<?php echo $mrp;?>" /></td>
			<td><?php echo $gst_per;?> %<input type='hidden' value="<?php echo $gst_per;?>" class='gst'/></td>
			<td><?php echo $r['gst_amt'];?><input type='hidden' value="<?php echo $gst_amt;?>" class='gst_amt'/></td>
			<td><?php echo $r['amount'];?><input type='hidden' value="<?php echo $amount;?>" class='all_rate' /></td>
			<td style='text-align:center;'><span onclick='$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span></td>
		</tr>
	<?php
	$i++;
	}
	?>
	<tr id='new_tr'>
		<td colspan='5' style='text-align:right;'><b>Total</b></td>
		<td id='final_gst'><?php echo $gst_tot;?></td><td colspan='3' id='final_rate'><?php echo number_format($net_tot,2);?></td>
	</tr>
	</table>
	<?php
}

if($type==5)
{
	$orderno=$_POST['ord_id'];
	$supp=$_POST['supp_id'];
	$all=$_POST['all'];
	$final_gst=$_POST['final_gst'];
	$final_rate=$_POST['final_rate'];
	$user=$_POST['user'];
	
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `approve` FROM `inv_purchase_order_master` WHERE `order_no`='$orderno' AND `supplier_id`='$supp'"));
	if($det['approve']=="0")
	{
		if(mysqli_query($link,"UPDATE `inv_purchase_order_master` SET `gst_amount`='$final_gst',`net_amount`='$final_rate' WHERE `order_no`='$orderno' AND `supplier_id`='$supp'"))
		{
			mysqli_query($link,"DELETE FROM `inv_purchase_order_details` WHERE `order_no`='$orderno' AND `supplier_id`='$supp'");
			$al=explode("#%#",$all);
			foreach($al as $a)
			{
				if($a)
				{
					$v=explode("@@",$a);
					$itm=$v[0];
					$qnt=$v[1];
					$mrp=$v[2];
					$gst_per=$v[3];
					$gst_amt=$v[4];
					$net_amt=$v[5];
					if($itm && $qnt)
					{
						mysqli_query($link,"INSERT INTO `inv_purchase_order_details`(`order_no`, `item_id`, `mrp`, `gst`, `gst_amt`, `amount`, `supplier_id`, `order_qnt`, `order_date`, `stat`, `user`) VALUES ('$orderno','$itm','$mrp','$gst_per','$gst_amt','$net_amt','$supp','$qnt','$date','0','$user')");
					}
				}
			}
			echo "1";
		}
		else
		{
			echo "2";
		}
	}
	else
	{
		echo "2";
	}
}

if($type==6)
{
	$ord=$_POST['ord'];
	$sid=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$ord'"));
	$supp_name=mysqli_fetch_assoc(mysqli_query($link,"select name from inv_supplier_master where id='$sid[supplier_id]'"));
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th colspan="2">Order No. : <?php echo $ord;?></th>
			<th colspan="3">Supplier : <?php echo $supp_name['name'];?></th>
			<th colspan="2">Order Date : <?php echo convert_date($sid['order_date']);?></th>
		</tr>
		<tr>
			<th>#</th>
			<th>Description</th>
			<th style="text-align:right;">Quantity</th>
			<th style="text-align:right;">Rate</th>
			<th style="text-align:right;">GST %</th>
			<th style="text-align:right;">GST Amount</th>
			<th style="text-align:right;">Amount</th>
		</tr>
	<?php
	$i=1;
	$gst_tot=0;
	$net_tot=0;
	$qry=mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord'");
	while($r=mysqli_fetch_assoc($qry))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`hsn_code`,`mrp`,`gst` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		$id=$r['item_id'];
		$bch="";
		$qnt=$r['order_qnt'];
		$mrp=$r['mrp'];
		$gst_per=explode(".",$r['gst']);
		$gst_per=$gst_per[0];
		$gst_amt=$r['gst_amt'];
		$amount=$r['amount'];
		$gst_tot+=$gst_amt;
		$net_tot+=$amount;
	?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td style="text-align:right;"><?php echo $r['order_qnt'];?></td>
			<td style="text-align:right;"><?php echo $r['mrp'];?></td>
			<td style="text-align:right;"><?php echo $gst_per;?> %</td>
			<td style="text-align:right;"><?php echo $r['gst_amt'];?></td>
			<td style="text-align:right;"><?php echo $r['amount'];?></td>
		</tr>
	<?php
	$i++;
	}
	?>
	<tr>
		<td colspan='5' style='text-align:right;'><b>Total</b></td>
		<td style="text-align:right;"><?php echo $gst_tot;?></td><td colspan='3' style="text-align:right;"><?php echo number_format($net_tot,2);?></td>
	</tr>
	</table>
	<?php
}

if($type==7)
{
	//$id=$_POST['id'];
	$qry=mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `stat`='0' ORDER BY `slno` DESC");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Order No</th>
			<th>Supplier</th>
			<th>Order Date</th>
			<th>User</th>
			<th>Action</th>
		</tr>
		<?php
		$j=1;
		while($res=mysqli_fetch_assoc($qry))
		{
			$supp=mysqli_fetch_assoc(mysqli_query($link,"select name from inv_supplier_master where id='$res[supplier_id]'"));
			$user=mysqli_fetch_assoc(mysqli_query($link,"select name from employee where emp_id='$res[user]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $res['order_no'];?></td>
			<td><?php echo $supp['name'];?></td>
			<td><?php echo convert_date($res['date']);?></td>
			<td><?php echo $user['name'];?></td>
			<td>
				<button type="button" class="btn btn-info" onclick="inv_order_print('<?php echo $res['order_no'];?>')"><i class="icon-print icon-large"></i> <b>Print</b></button>
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
	}
}

if($type==8)
{
	$ord=$_POST['ord'];
	$user=$_POST['user'];
	if(mysqli_query($link,"UPDATE `inv_purchase_order_master` SET `approve`='1' WHERE `order_no`='$ord'"))
	{
		mysqli_query($link,"INSERT INTO `inv_purchase_ord_approve`(`order_no`, `approve`, `date`, `time`, `user`) VALUES ('$ord','1','$date','$time','$user')");
		echo "Approved";
	}
	else
	{
		echo "Error";
	}
}

if($type==9)
{
	$ord=$_POST['ord'];
	$user=$_POST['user'];
	if(mysqli_query($link,"UPDATE `inv_purchase_order_master` SET `approve`='2' WHERE `order_no`='$ord'"))
	{
		mysqli_query($link,"INSERT INTO `inv_purchase_ord_approve`(`order_no`, `approve`, `date`, `time`, `user`) VALUES ('$ord','2','$date','$time','$user')");
		echo "Cancelled";
	}
	else
	{
		echo "Error";
	}
}

if($type==10)
{
	$orderno=$_POST['orderno'];
	
	$q=mysqli_fetch_assoc(mysqli_query($link,"select a.order_date,a.net_amount,a.supplier_id,b.name from inv_purchase_order_master a,inv_supplier_master b where a.order_no='$orderno' and a.supplier_id=b.id "));
	$amt=explode(".",$q['net_amount']);
	$val=$orderno.'@'.$q['name'].'@'.$q['supplier_id'].'@'.$q['order_date'].'@'.$amt[0];
	
	echo $val;
}

if($type==11-11111) // old
{
	$orderno=$_POST['orderno'];
	
	$apprv = mysqli_fetch_assoc(mysqli_query($link," SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$orderno'"));
	if($apprv['approve']==0)
	{
		$sid=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$orderno'"));
		$supp_name=mysqli_fetch_assoc(mysqli_query($link,"select name from inv_supplier_master where id='$sid[supplier_id]'"));
		?>
		<table class="table table-condensed table-bordered table-report">
			<tr>
				<th>Order No.</th>
				<th>Supplier</th>
				<th>Order Date</th>
				<th>Approve Status</th>
			</tr>
			<tr style="background:#FDF3AE;color:#222222;">
				<td><?php echo $orderno;?></td>
				<td><?php echo $supp_name['name'];?></td>
				<td><?php echo convert_date($sid['order_date']);?></td>
				<td>Pending</td>
			</tr>
		</table>
		<?php
	}
	if($apprv['stat']==0 && $apprv['approve']==1)
	{
		$qpdct = mysqli_query($link," SELECT a.*,b.`sub_category_id` FROM `inv_purchase_order_details` a, `item_master` b where order_no='$orderno' and a.`item_id`=b.`item_id` order by b.`sub_category_id`,a.`stat`");
		if(mysqli_num_rows($qpdct)>0)
		{
			?>
			<table class="table table-condensed table-bordered table-report" id="tbl_rcv">
			<tr>
				<th>#</th>
				<th>Description</th>
				<th style="text-align:right;">Order Qnt</th>
				<th style="text-align:right;">Rate</th>
				<th style="text-align:right;">GST %</th>
				<th style="text-align:right;">GST Amount</th>
				<th style="text-align:right;">Amount</th>
				<th style="text-align:right;">Batch No</th>
				<th style="text-align:right;">Received</th>
				<th style="text-align:right;">Expiry</th>
				<th style="text-align:right;">Add Batch</th>
			</tr>
			<?php
			$j=1;
			while($res=mysqli_fetch_assoc($qpdct))
			{
				$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`hsn_code`,`mrp`,`gst`,`strip_quantity` FROM `item_master` WHERE `item_id`='$res[item_id]'"));
				$supp=mysqli_fetch_assoc(mysqli_query($link,"select name from inv_supplier_master where id='$res[supplier_id]'"));
				$user=mysqli_fetch_assoc(mysqli_query($link,"select name from employee where emp_id='$res[user]'"));
				$gst=explode(".",$res['gst']);
				$gst_per=$gst[0];
				
				if($res['stat']==0)
				{
					$rcv_qnt="";
					$rcv_bch="";
					$rcv_exp_dt="";
					$rcv_loop=0;
					$prev_rcv_qry=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `order_no`='$orderno' AND `item_id`='$res[item_id]'");
					$prev_rcv_num=mysqli_num_rows($prev_rcv_qry);
					if($prev_rcv_num>0)
					{
					while($prev_rcv_det=mysqli_fetch_assoc($prev_rcv_qry))
					{
						$prev_rcv_num=$prev_rcv_num+1;
					if($rcv_loop==0)
					{
					$tr_id="tr".$res['item_id'];
					$tr_class="itm".$res['item_id']." vals";
					?>
					<tr id="<?php echo $tr_id;?>" class="<?php echo $tr_class;?>">
						<?php
						if($prev_rcv_num>0)
						{
							$prev_rcv_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifnull(SUM(`recpt_quantity`),0) AS prev_rcv FROM `inv_main_stock_received_detail` WHERE `order_no`='$orderno' AND `item_id`='$res[item_id]'"));
						?>
						<td rowspan="<?php echo $prev_rcv_num;?>"><?php echo $j;?></td>
						<td rowspan="<?php echo $prev_rcv_num;?>"><?php echo $itm['item_name'];?><input type="hidden" value="<?php echo $res['item_id'];?>" /></td>
						<td rowspan="<?php echo $prev_rcv_num;?>" style="text-align:right;"><?php echo $res['order_qnt'];?><input type="hidden" value="<?php echo $res['order_qnt']-($prev_rcv_qnt['prev_rcv']/$itm['strip_quantity']);?>" /></td>
						<td rowspan="<?php echo $prev_rcv_num;?>" style="text-align:right;"><?php echo $res['mrp'];?></td>
						<td rowspan="<?php echo $prev_rcv_num;?>" style="text-align:right;"><?php echo $gst_per;?> %</td>
						<td rowspan="<?php echo $prev_rcv_num;?>" style="text-align:right;"><?php echo $res['gst_amt'];?></td>
						<td rowspan="<?php echo $prev_rcv_num;?>" style="text-align:right;"><?php echo $res['amount'];?></td>
						<?php
						}
						?>
						<td style="text-align:right;">
							<input type="text" class="inpt" onkeyup="tab_next(this,event)" style="width:100px;" placeholder="Batch No" />
						</td>
						<td style="text-align:right;">
							<input type="text" class="span1 inpt qnt<?php echo $res['item_id'];?>" onkeyup="chk_num(this,event)" value="<?php echo $res['order_qnt']-($prev_rcv_qnt['prev_rcv']/$itm['strip_quantity']);?>" />
						</td>
						<td style="text-align:right;">
							<input type="text" class="inpt" style="width:100px;" onkeyup="exp_dt(this,this.value,event)" maxlength="7" placeholder="YYYY-MM" />
						</td>
						<td style="text-align:right;">
							<button type="button" class="btn btn-info" onclick="add_batch('<?php echo $res['item_id'];?>')"><i class="icon-plus icon-large"></i></button>
						</td>
					</tr>
					<?php
					$prev_rcv_num=0;
					}
					if($rcv_loop>0)
					{
					$tr_id="";
					$tr_class="itm".$res['item_id'];
					}
					else
					{
					$tr_id="tr".$res['item_id'];
					$tr_class="itm".$res['item_id'];
					}
					?>
					<tr id="<?php echo $tr_id;?>" class="<?php echo $tr_class;?>">
						<td style="text-align:right;"><?php echo $prev_rcv_det['recept_batch'];?></td>
						<td style="text-align:right;"><?php echo ($prev_rcv_det['recpt_quantity']/$itm['strip_quantity'])." x ".$itm['strip_quantity'];?></td>
						<td style="text-align:right;"><?php echo $prev_rcv_det['expiry_date'];?></td>
						<td></td>
					</tr>
					<?php
					$rcv_loop++;
					}
				}
				else
				{
					?>
					<tr id="<?php echo $tr_id;?>" class="<?php echo $tr_class;?>">
						<td><?php echo $j;?></td>
						<td><?php echo $itm['item_name'];?><input type="hidden" value="<?php echo $res['item_id'];?>" /></td>
						<td style="text-align:right;"><?php echo $res['order_qnt'];?><input type="hidden" value="<?php echo $res['order_qnt'];?>" /></td>
						<td style="text-align:right;"><?php echo $res['mrp'];?></td>
						<td style="text-align:right;"><?php echo $gst_per;?> %</td>
						<td style="text-align:right;"><?php echo $res['gst_amt'];?></td>
						<td style="text-align:right;"><?php echo $res['amount'];?></td>
						<td style="text-align:right;">
							<input type="text" class="inpt" onkeyup="tab_next(this,event)" style="width:100px;" placeholder="Batch No" />
						</td>
						<td style="text-align:right;">
							<input type="text" class="span1 inpt qnt<?php echo $res['item_id'];?>" onkeyup="chk_num(this,event)" value="<?php echo $res['order_qnt'];?>" />
						</td>
						<td style="text-align:right;">
							<input type="text" class="inpt" style="width:100px;" onkeyup="exp_dt(this,this.value,event)" maxlength="7" placeholder="YYYY-MM" />
						</td>
						<td style="text-align:right;">
							<button type="button" class="btn btn-info" onclick="add_batch('<?php echo $res['item_id'];?>')"><i class="icon-plus icon-large"></i></button>
						</td>
					</tr>
					<?php
				}
			}
			if($res['stat']==1)
			{
				//$ord_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `order_no`='$orderno' AND `item_id`='$res[item_id]' AND `SuppCode`='$res[supplier_id]'"));
				$ord_qry=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `order_no`='$orderno' AND `item_id`='$res[item_id]' AND `SuppCode`='$res[supplier_id]'");
				$ord_rcv_num=mysqli_num_rows($ord_qry);
				while($ord_det=mysqli_fetch_assoc($ord_qry))
				{
				$rcv_qnt=$ord_det['recpt_quantity'];
				$rcv_bch=$ord_det['recept_batch'];
				$rcv_exp_dt=$ord_det['expiry_date'];
				$tr_id="";
				$tr_class="";
				?>
				<tr id="<?php echo $tr_id;?>" class="<?php echo $tr_class;?>">
				<?php
				if($ord_rcv_num>0)
				{
					?>
					<td rowspan="<?php echo $ord_rcv_num;?>"><?php echo $j;?></td>
					<td rowspan="<?php echo $ord_rcv_num;?>"><?php echo $itm['item_name'];?><input type="hidden" value="<?php echo $res['item_id'];?>" /></td>
					<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $res['order_qnt'];?><input type="hidden" value="<?php echo $res['order_qnt'];?>" /></td>
					<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $res['mrp'];?></td>
					<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $gst_per;?> %</td>
					<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $res['gst_amt'];?></td>
					<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $res['amount'];?></td>
					<?php
					}
					$ord_rcv_num=0;
					?>
					<td style="text-align:right;">
						<?php
							echo $rcv_bch;
						?>
					</td>
					<td style="text-align:right;">
						<?php
							echo $rcv_qnt/$itm['strip_quantity']." x ".$itm['strip_quantity'];
						?>
					</td>
					<td style="text-align:right;">
						<?php
						
							$rcv_exp_dt = date('Y-m', strtotime($rcv_exp_dt));
							echo $rcv_exp_dt;
						?>
					</td>
					<td style="text-align:right;">
						
					</td>
				</tr>
					<?php
					}
				}
			$j++;
			}
			?>
			<tr>
				<td colspan="11" style="text-align:center;">
					<button type="button" id="rcv_btn" class="btn btn-success" onclick="receive_all()"><i class="icon-save icon-large"></i> Save</button>
				</td>
			</tr>
		</table>
		<style>
			#tbl_rcv tr:hover
			{
				background:none;
			}
		</style>
			<?php
		}
	}
	if($apprv['approve']==2)
	{
		$sid=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$orderno'"));
		$supp_name=mysqli_fetch_assoc(mysqli_query($link,"select name from inv_supplier_master where id='$sid[supplier_id]'"));
		?>
		<table class="table table-condensed table-bordered table-report">
			<tr>
				<th>Order No.</th>
				<th>Supplier</th>
				<th>Order Date</th>
				<th>Approve Status</th>
			</tr>
			<tr style="background:#FDB7AE;color:#222222;">
				<td><?php echo $orderno;?></td>
				<td><?php echo $supp_name['name'];?></td>
				<td><?php echo convert_date($sid['order_date']);?></td>
				<td>Cancelled</td>
			</tr>
		</table>
		<?php
	}
}

if($type==11)
{
	$orderno=mysqli_real_escape_string($link,$_POST['orderno']);
	
	$apprv = mysqli_fetch_assoc(mysqli_query($link," SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$orderno'"));
	if($apprv['approve'])
	{
	if($apprv['approve']==0)
	{
		$sid=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$orderno'"));
		$supp_name=mysqli_fetch_assoc(mysqli_query($link,"select name from inv_supplier_master where id='$sid[supplier_id]'"));
		?>
		<table class="table table-condensed table-bordered table-report">
			<tr>
				<th>Order No.</th>
				<th>Supplier</th>
				<th>Order Date</th>
				<th>Approve Status</th>
			</tr>
			<tr style="background:#FDF3AE;color:#222222;">
				<td><?php echo $orderno;?></td>
				<td><?php echo $supp_name['name'];?></td>
				<td><?php echo convert_date($sid['order_date']);?></td>
				<td>Pending</td>
			</tr>
		</table>
		<?php
	}
	if($apprv['stat']==0 && $apprv['approve']==1)
	{
		//$qpdct = mysqli_query($link," SELECT * FROM `inv_purchase_order_details` where order_no='$orderno' order by `stat`");
		$qpdct = mysqli_query($link," SELECT a.*,b.`sub_category_id` FROM `inv_purchase_order_details` a, `item_master` b where order_no='$orderno' and a.`item_id`=b.`item_id` order by b.`sub_category_id`,a.`stat`");
		if(mysqli_num_rows($qpdct)>0)
		{
			?>
			<table class="table table-condensed table-bordered table-report" id="tbl_rcv">
			<tr style="background:#666666;">
				<th>#</th>
				<th>Description</th>
				<th style="text-align:right;">Order Qty</th>
				<th style="text-align:right;">Cost</th>
				<th style="text-align:right;">MRP</th>
				<th style="text-align:right;">GST %</th>
				<th style="text-align:right;">Batch No</th>
				<th style="text-align:right;">Received Qty</th>
				<th style="text-align:right;">Free Qty</th>
				<th style="text-align:right;">Expiry</th>
				<th style="text-align:right;">Add Batch</th>
			</tr>
			<?php
			$tr=0;
			$j=1;
			while($res=mysqli_fetch_assoc($qpdct))
			{
				$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`hsn_code`,`mrp`,`gst`,`strip_quantity` FROM `item_master` WHERE `item_id`='$res[item_id]'"));
				$supp=mysqli_fetch_assoc(mysqli_query($link,"select name from inv_supplier_master where id='$res[supplier_id]'"));
				$user=mysqli_fetch_assoc(mysqli_query($link,"select name from employee where emp_id='$res[user]'"));
				$gst=explode(".",$res['gst']);
				$gst_per=$gst[0];
				
				if($res['stat']==0) // not all received
				{
					$rcv_qnt="";
					$rcv_bch="";
					$rcv_exp_dt="";
					$rcv_loop=0;
					$prev_rcv_qry=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `order_no`='$orderno' AND `item_id`='$res[item_id]'");
					$prev_rcv_num=mysqli_num_rows($prev_rcv_qry);
					if($prev_rcv_num>0)
					{
					while($prev_rcv_det=mysqli_fetch_assoc($prev_rcv_qry))
					{
						$prev_rcv_num=$prev_rcv_num+1;
					if($rcv_loop==0)
					{
					$tr_id="tr".$res['item_id'];
					//$tr_class="itm".$res['item_id']." vals";
					
					//~ if($res['sub_category_id']>2)
					//~ {
						//~ $itm_class="oth";
					//~ }
					//~ else
					{
						$itm_class="vals";
					}
					$tr_class="itm".$res['item_id']." $itm_class";
					?>
					<tr id="<?php echo $tr_id;?>" class="<?php echo $tr_class;?>">
						<?php
						if($prev_rcv_num>0) // prev part received
						{
							$prev_rcv_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifnull(SUM(`recpt_quantity`),0) AS prev_rcv FROM `inv_main_stock_received_detail` WHERE `order_no`='$orderno' AND `item_id`='$res[item_id]'"));
						?>
						<td rowspan="<?php echo $prev_rcv_num;?>"><?php echo $j;?></td>
						<?php
						}
						$rem_qnt=$res['order_qnt']-($prev_rcv_qnt['prev_rcv']/$itm['strip_quantity']);
						?>
						<td><?php echo $itm['item_name'];?><input type="hidden" value="<?php echo $res['item_id'];?>" /></td>
						<td style="text-align:right;">
							<?php echo $res['order_qnt'];?><input type="hidden" value="<?php echo $rem_qnt;?>" />
						</td>
						<td style="text-align:right;">
							<input type="text" class="span1 cost" onkeyup="chk_dec(this,event,'cost')" value="<?php echo $res['mrp'];?>" />
						</td>
						<td style="text-align:right;"><input type="text" class="span1 mrp" onkeyup="chk_dec(this,event,'mrp')" value="" /></td>
						<td style="text-align:right;">
							<?php
							if($res['sub_category_id']<=2)
							{
							?>
							<input type="text" class="span1 inpt gst" onkeyup="chk_dec(this,event,'gst')" value="<?php echo $gst_per;?>" />
							<?php
							}
							?>
						</td>
						<td style="text-align:right;">
							<?php
							if($res['sub_category_id']<=2)
							{
							?>
							<input type="text" class="inpt bch" onkeyup="tab_next(this,event)" style="width:100px;" placeholder="Batch No" />
							<?php
							}
							?>
						</td>
						<td style="text-align:right;">
							<input type="text" class="span1 rcv_qnt rcv<?php echo $res['item_id'];?>" onkeyup="chk_rcv(this,'<?php echo $res['item_id'];?>');chk_num(this,event,'rcv_qnt')" value="<?php echo $rem_qnt;?>" />
						</td>
						<td style="text-align:right;">
							<?php
							if($res['sub_category_id']<=2)
							{
							?>
							<input type="text" class="span1 fre_qnt" onkeyup="chk_num(this,event,'fre_qnt')" value="<?php //echo $res['order_qnt'];?>" />
							<?php
							}
							?>
						</td>
						<td style="text-align:right;">
							<!--<input type="text" class="inpt" style="width:100px;" onkeyup="exp_dt(this,this.value,event)" maxlength="7" placeholder="YYYY-MM" />-->
							<?php
							if($res['sub_category_id']<=2)
							{
							?>
							<input type="text" class="inpt exp_dt" style="width:80px;" onkeyup="exp_dt(this,this.value,event,'exp_dt','<?php echo $tr;?>')" maxlength="7" placeholder="YYYY-MM" />
							<?php
							}
							?>
						</td>
						<td style="text-align:right;">
							<?php
							if($res['sub_category_id']<=2)
							{
							?>
							<button type="button" class="btn btn-info" onclick="add_batch('<?php echo $res['item_id'];?>')"><i class="icon-plus icon-large"></i></button>
							<?php
							}
							?>
						</td>
					</tr>
					<?php
					$prev_rcv_num=0;
					}
					if($rcv_loop>0)
					{
					$tr_id="";
					$tr_class="itm".$res['item_id'];
					}
					else
					{
					$tr_id="tr".$res['item_id'];
					$tr_class="itm".$res['item_id'];
					}
					$rcv_exp_dt = date('Y-m', strtotime($prev_rcv_det['expiry_date']));
					?>
					<tr id="<?php echo $tr_id;?>" class="<?php echo $tr_class;?>">
						<td style="text-align:right;">
							<?php //echo $res['order_qnt'];?><input type="hidden" class="span1 cost" value="<?php echo $res['order_qnt'];?>" />
						</td>
						<td style="text-align:right;">
							<?php //echo $res['order_qnt']-($prev_rcv_qnt['prev_rcv']/$itm['strip_quantity']);?><input type="hidden" value="<?php echo $res['order_qnt']-($prev_rcv_qnt['prev_rcv']/$itm['strip_quantity']);?>" />
						</td>
						<td style="text-align:right;">
							<?php echo $prev_rcv_det['recept_cost_price'];?>
						</td>
						<td style="text-align:right;">
							<?php echo $prev_rcv_det['recpt_mrp'];?>
						</td>
						<td style="text-align:right;">
							<?php
							if($res['sub_category_id']<=2)
							{
								echo $gst_per." %";
							}
							?>
						</td>
						<td style="text-align:right;">
							<?php
							if($res['sub_category_id']<=2)
							{
							echo $prev_rcv_det['recept_batch'];
							}
							?>
						</td>
						<td style="text-align:right;">
							<?php
							if($res['sub_category_id']<=2)
							{
								echo ($prev_rcv_det['recpt_quantity']/$itm['strip_quantity'])." x ".$itm['strip_quantity'];
							}
							else
							{
								echo $prev_rcv_det['recpt_quantity'];
							}
							?>
						</td>
						<td style="text-align:right;">
							<?php
							if($res['sub_category_id']<=2)
							{
								if($prev_rcv_det['free_qnt']>0)
								{
									echo ($prev_rcv_det['free_qnt']/$itm['strip_quantity'])." x ".$itm['strip_quantity'];
								}
								else
								{
									echo $prev_rcv_det['free_qnt'];
								}
							}
							else
							{
								echo $prev_rcv_det['free_qnt'];
							}
							?>
						</td>
						<td style="text-align:right;">
							<?php
							if($res['sub_category_id']<=2)
							{
							echo $rcv_exp_dt;
							}
							?>
						</td>
						<td></td>
					</tr>
					<?php
					$rcv_loop++;
					}
				}
				else // not received
				{
					$tr_id="tr".$res['item_id'];
					//$tr_class="itm".$res['item_id']." vals";
					//~ if($res['sub_category_id']>2)
					//~ {
						//~ $itm_class="oth";
					//~ }
					//~ else
					{
						$itm_class="vals";
					}
					$tr_class="itm".$res['item_id']." $itm_class";
					?>
					<tr id="<?php echo $tr_id;?>" class="<?php echo $tr_class;?>">
						<td><?php echo $j;?></td>
						<td><?php echo $itm['item_name'];?><input type="hidden" value="<?php echo $res['item_id'];?>" /></td>
						<td style="text-align:right;"><?php echo $res['order_qnt'];?><input type="hidden" value="<?php echo $res['order_qnt'];?>" /></td>
						<td style="text-align:right;"><input type="text" class="span1 cost" onkeyup="chk_dec(this,event,'cost')" value="<?php echo $res['mrp'];?>" /></td>
						<td style="text-align:right;"><input type="text" class="span1 mrp" onkeyup="chk_dec(this,event,'mrp')" value="" /></td>
						<td style="text-align:right;">
							<?php
							if($itm_class=="vals")
							{
							?>
							<input type="text" class="span1 inpt gst" onkeyup="chk_dec(this,event,'gst')" value="<?php echo $gst_per;?>" />
							<?php
							}
							?>
						</td>
						<td style="text-align:right;">
							<?php
							if($itm_class=="vals")
							{
							?>
							<input type="text" class="inpt bch" onkeyup="tab_next(this,event,'bch')" style="width:80px;" placeholder="Batch No" />
							<?php
							}
							?>
						</td>
						<td style="text-align:right;">
							<input type="text" class="span1 rcv_qnt rcv<?php echo $res['item_id'];?>" onkeyup="chk_rcv(this,'<?php echo $res['item_id'];?>');chk_num(this,event,'rcv_qnt')" value="<?php echo $res['order_qnt'];?>" />
						</td>
						<td style="text-align:right;">
							<?php
							if($itm_class=="vals")
							{
							?>
							<input type="text" class="span1 fre_qnt" onkeyup="chk_num(this,event,'fre_qnt')" value="<?php //echo $res['order_qnt'];?>" />
							<?php
							}
							?>
						</td>
						<td style="text-align:right;">
							<?php
							if($itm_class=="vals")
							{
							?>
							<input type="text" class="inpt exp_dt" style="width:80px;" onkeyup="exp_dt(this,this.value,event,'exp_dt','<?php echo $tr;?>')" maxlength="7" placeholder="YYYY-MM" />
							<?php
							}
							?>
						</td>
						<td style="text-align:right;">
							<?php
							if($itm_class=="vals")
							{
							?>
							<button type="button" class="btn btn-info" onclick="add_batch('<?php echo $res['item_id'];?>')"><i class="icon-plus icon-large"></i></button>
							<?php
							}
							?>
						</td>
					</tr>
					<?php
				}
			}
			if($res['stat']==1) // fully received
			{
				//$ord_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `order_no`='$orderno' AND `item_id`='$res[item_id]' AND `SuppCode`='$res[supplier_id]'"));
				$ord_qry=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `order_no`='$orderno' AND `item_id`='$res[item_id]' AND `SuppCode`='$res[supplier_id]'");
				$ord_rcv_num=mysqli_num_rows($ord_qry);
				while($ord_det=mysqli_fetch_assoc($ord_qry))
				{
				$rcv_qnt=$ord_det['recpt_quantity'];
				$rcv_bch=$ord_det['recept_batch'];
				$rcv_exp_dt=$ord_det['expiry_date'];
				$tr_id="";
				$tr_class="";
				?>
				<tr id="<?php echo $tr_id;?>" class="<?php echo $tr_class;?>">
				<?php
				if($ord_rcv_num>0)
				{
					?>
					<td rowspan="<?php echo $ord_rcv_num;?>"><?php echo $j;?></td>
					<td rowspan="<?php echo $ord_rcv_num;?>"><?php echo $itm['item_name'];?><input type="hidden" value="<?php echo $res['item_id'];?>" /></td>
					<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $res['order_qnt'];?><input type="hidden" value="<?php echo $res['order_qnt'];?>" /></td>
					<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $ord_det['recept_cost_price'];?></td>
					<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $ord_det['recpt_mrp'];?></td>
					<?php
				}
					$ord_rcv_num=0;
					?>
					<td style="text-align:right;">
						<?php
						//if($res['sub_category_id']<=2)
						{
							echo $gst_per." %";
						}
						?>
					</td>
					<td style="text-align:right;">
						<?php
						//if($res['sub_category_id']<=2)
						{
							echo $rcv_bch;
						}
						?>
					</td>
					<td style="text-align:right;">
						<?php
						if($res['sub_category_id']<=2)
						{
							echo $ord_det['recpt_quantity']/$itm['strip_quantity']." x ".$itm['strip_quantity'];
						}
						else
						{
							echo $ord_det['recpt_quantity'];
						}
						?>
					</td>
					<td style="text-align:right;">
						<?php
						if($res['sub_category_id']<=2)
						{
							if($ord_det['free_qnt']>0){echo $ord_det['free_qnt']/$itm['strip_quantity']." x ".$itm['strip_quantity'];}
							else {echo $ord_det['free_qnt'];}
						}
						else {echo $ord_det['free_qnt'];}
						?>
					</td>
					<td style="text-align:right;">
						<?php
						//if($res['sub_category_id']<=2)
						{
							if($rcv_exp_dt!="0000-00-00")
							{
								$rcv_exp_dt = date('Y-m', strtotime($rcv_exp_dt));
							}
							else
							{
								$rcv_exp_dt="";
							}
							echo $rcv_exp_dt;
						}
						?>
					</td>
					<td style="text-align:right;">
						
					</td>
				</tr>
					<?php
					}
				}
			$tr++;
			$j++;
			}
			?>
			<tr>
				<td colspan="11" style="text-align:center;">
					<button type="button" id="rcv_btn" class="btn btn-success" onclick="receive_all()"><i class="icon-save icon-large"></i> Save</button>
				</td>
			</tr>
		</table>
		<style>
			#tbl_rcv tr:hover
			{
				background:none;
			}
		</style>
			<?php
		}
	}
	if($apprv['stat']==1 && $apprv['approve']==1)
	{
		?>
		<table class="table table-condensed table-bordered table-report" id="tbl_rcv">
			<tr style="background:#666666;">
				<th>Sl</th>
				<th>Description</th>
				<th style="text-align:right;">Order Qnt</th>
				<th style="text-align:right;">Rate</th>
				<th style="text-align:right;">GST %</th>
				<th style="text-align:right;">GST Amount</th>
				<th style="text-align:right;">Amount</th>
				<th style="text-align:right;">Batch No</th>
				<th style="text-align:right;">Received</th>
				<th style="text-align:right;">Expiry</th>
			</tr>
		<?php
		$j=1;
		//$qpdct = mysqli_query($link," SELECT * FROM `inv_purchase_order_details` where order_no='$orderno' order by `stat`");
		$qpdct = mysqli_query($link," SELECT a.*,b.`sub_category_id` FROM `inv_purchase_order_details` a, `item_master` b where order_no='$orderno' and a.`item_id`=b.`item_id` order by b.`sub_category_id`,a.`stat`");
		while($res=mysqli_fetch_assoc($qpdct))
		{
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`hsn_code`,`mrp`,`gst`,`strip_quantity` FROM `item_master` WHERE `item_id`='$res[item_id]'"));
		$ord_qry=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `order_no`='$orderno' AND `item_id`='$res[item_id]' AND `SuppCode`='$res[supplier_id]'");
		$ord_rcv_num=mysqli_num_rows($ord_qry);
		while($ord_det=mysqli_fetch_assoc($ord_qry))
		{
		$rcv_qnt=$ord_det['recpt_quantity'];
		$rcv_bch=$ord_det['recept_batch'];
		$rcv_exp_dt=$ord_det['expiry_date'];
		$tr_id="";
		$tr_class="";
		?>
		<tr id="<?php echo $tr_id;?>" class="<?php echo $tr_class;?>">
		<?php
		if($ord_rcv_num>0)
		{
			$gst_per=explode(".",$ord_det['gst_per']);
			$gst_per=$gst_per[0];
			?>
			<td rowspan="<?php echo $ord_rcv_num;?>"><?php echo $j;?></td>
			<td rowspan="<?php echo $ord_rcv_num;?>"><?php echo $itm['item_name'];?><input type="hidden" value="<?php echo $res['item_id'];?>" /></td>
			<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $res['order_qnt'];?><input type="hidden" value="<?php echo $res['order_qnt'];?>" /></td>
			<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $res['mrp'];?></td>
			<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php if($res['sub_category_id']<=2){ echo $gst_per." %";}?></td>
			<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php if($res['sub_category_id']<=2){ echo $res['gst_amt'];}?></td>
			<td rowspan="<?php echo $ord_rcv_num;?>" style="text-align:right;"><?php echo $res['amount'];?></td>
			<?php
			}
			$ord_rcv_num=0;
			?>
			<td style="text-align:right;">
				<?php
				if($res['sub_category_id']<=2)
				{
					echo $rcv_bch;
				}
				?>
			</td>
			<td style="text-align:right;">
				<?php
				if($res['sub_category_id']>2)
				{
					echo $rcv_qnt;
				}
				else
				{
					echo $rcv_qnt/$itm['strip_quantity']." x ".$itm['strip_quantity'];
				}
				?>
			</td>
			<td style="text-align:right;">
				<?php
				if($res['sub_category_id']<=2)
				{
					if($rcv_exp_dt!="0000-00-00")
					{
						$rcv_exp_dt = date('Y-m', strtotime($rcv_exp_dt));
					}
					else
					{
						$rcv_exp_dt="";
					}
					echo $rcv_exp_dt;
				}
				?>
			</td>
		</tr>
		<?php
		}
		$j++;
		}
		?>
		</table>
		<style>
			#tbl_rcv tr:hover
			{
				background:none;
			}
		</style>
		<?php
	}
	if($apprv['approve']==2)
	{
		$sid=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$orderno'"));
		$supp_name=mysqli_fetch_assoc(mysqli_query($link,"select name from inv_supplier_master where id='$sid[supplier_id]'"));
		?>
		<table class="table table-condensed table-bordered table-report">
			<tr>
				<th>Order No.</th>
				<th>Supplier</th>
				<th>Order Date</th>
				<th>Approve Status</th>
			</tr>
			<tr style="background:#FDB7AE;color:#222222;">
				<td><?php echo $orderno;?></td>
				<td><?php echo $supp_name['name'];?></td>
				<td><?php echo convert_date($sid['order_date']);?></td>
				<td>Cancelled</td>
			</tr>
		</table>
		<?php
	}
	}
	else
	{
		?>
		<table class="table table-condensed table-bordered table-report">
			<tr>
				<th>Order No.</th>
				<th>Supplier</th>
				<th>Order Date</th>
				<th>Approve Status</th>
			</tr>
			<tr style="background:#FFD685;color:#222222;">
				<td><?php echo $orderno;?></td>
				<td>Not Found</td>
				<td>Not Found</td>
				<td>Not Found</td>
			</tr>
		</table>
		<?php
	}
}

if($type==12)
{
	function text_query($txt)
	{
		if($txt)
		{
			$myfile = file_put_contents('log/order_received.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
		}
	}
	
	$ord=$_POST['ord'];
	$supp=$_POST['supp'];
	$sup_bill_no=$_POST['bill_no'];
	$bill_dt=$_POST['bill_dt'];
	$user=$_POST['user'];
	$all=$_POST['all'];
	$others=$_POST['others'];
	
	$billnos=100;
	$dis_year=date("Y");
	$dis_month=date("m");
	$dis_year_sm=date("y");
	
	$c_m_y=$dis_year."-".$dis_month;
	//~ $bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`order_no`) as tot FROM `inv_main_stock_received_master` WHERE `date` like '$c_m_y%' "));
	//~ $bill_num=$bill_no_qry["tot"];

	//~ $bill_tot_num=$bill_num;

	//~ if($bill_tot_num==0)
	//~ {
		//~ $billnos=$billnos+1;
	//~ }else
	//~ {
		//~ $billnos=$billnos+$bill_tot_num+1;
	//~ }
	
	//~ $rcv_no="RCV".$billnos.$dis_month.$dis_year_sm;
	
	$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`receipt_no`) as tot FROM `inv_main_stock_received_master`"));
	$bill_num=$bill_no_qry["tot"];

	$bill_tot_num=$bill_num;

	if($bill_tot_num==0)
	{
		//$bill_no=$billnos+1;
		$bill_no=1;
	}
	else
	{
		//$bill_no=$billnos+$bill_tot_num+1;
		$bill_no=$bill_tot_num+1;
	}
	
	$rcv_no="PRN".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
	
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$ord'"));
	
	if(mysqli_query($link,"INSERT INTO `inv_main_stock_received_master`(`receipt_no`, `order_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$rcv_no','$ord','$bill_dt','$date','$det[bill_amount]','$det[gst_amount]','$det[dis_amt]','$det[net_amount]','$supp','$sup_bill_no','$user','$time','0','0')"))
	{
		$txt.="\nINSERT INTO `inv_main_stock_received_master`(`receipt_no`, `order_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$rcv_no','$ord','$bill_dt','$date','$det[net_amount]','$det[gst_amount]','$det[dis_amt]','$det[net_amount]','$supp','$sup_bill_no','$user','$time','0','0')";
		if($all)
		{
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@@",$a);
			$itm=$v[0];
			$cost=$v[1];
			$mrp=$v[2];
			$gst=$v[3];
			$bch=$v[4];
			if($bch=="")
			{
				$bch="batch001";
			}
			$qnt=$v[5];
			$free=$v[6];
			if($v[7])
			{
				$exp_dt=$v[7]."-01";
				$exp_dt=date("Y-m-t", strtotime($exp_dt));
			}
			else
			{
				$exp_dt="";
			}
			if($itm && $cost && $qnt)
			{
				$itm_strp=mysqli_fetch_assoc(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
				$qnt=$qnt*$itm_strp['strip_quantity'];
				
				$i_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'"));
				$txt.="\nSELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'";
				
				mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`receive_no`, `order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`, `strip_quantity`) VALUES ('$rcv_no','$ord','$sup_bill_no','$itm','0000-00-00','$exp_dt','$date','$qnt','$free','$bch','$supp','$mrp','$cost','0','$i_det[amount]','0','0','$gst','$i_det[gst_amt]','$itm_strp[strip_quantity]')");
				
				$txt.="\nINSERT INTO `inv_main_stock_received_detail`(`receive_no`, `order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`, `strip_quantity`) VALUES ('$rcv_no','$ord','$sup_bill_no','$itm','0000-00-00','$exp_dt','$date','$qnt','$free','$bch','$supp','$mrp','$cost','0','$i_det[amount]','0','0','$gst','$i_det[gst_amt]','$itm_strp[strip_quantity]')";
				
				//----------stock entry------------------
				
				$stk_qnt=0;
				$qnt=$qnt+$free; // add free qty
				$stk_master=mysqli_fetch_assoc(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and batch_no='$bch' order by  date desc"));
				$txt.="\nselect * from inv_mainstock_details where date='$date' and item_id='$itm' and batch_no='$bch' order by  date desc";
				if($stk_master['item_id']!='')
				{
					$stk_qnt=$stk_master['closing_qnty']+$qnt;
					$rcv_qnt=$stk_master['recv_qnty']+$qnt;
					mysqli_query($link,"update inv_mainstock_details set closing_qnty='$stk_qnt',recv_qnty='$rcv_qnt' where date='$date' and item_id='$itm' and batch_no='$bch'");
					$txt.="\nupdate inv_mainstock_details set closing_qnty='$stk_qnt',recv_qnty='$rcv_qnt' where date='$date' and item_id='$itm' and batch_no='$bch'";
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
					mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$stk_qnt','$exp_dt')");
					$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$stk_qnt','$exp_dt')";
				}
				else
				{
					$stk_master=mysqli_fetch_assoc(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc"));
					$txt.="\nselect * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc";
					$stk_qnt=$stk_master['closing_qnty']+$qnt;
					mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','$bch','$date','$stk_master[closing_qnty]','$qnt',0,'$stk_qnt')");
					$txt.="\ninsert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','$bch','$date','$stk_master[closing_qnty]','$qnt',0,'$stk_qnt')";
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
					mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$stk_qnt','$exp_dt')");
					$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$stk_qnt','$exp_dt')";
				}
			}
		}
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@@",$a);
			$itm=$v[0];
			$cost=$v[1];
			$mrp=$v[2];
			$gst=$v[3];
			$bch=$v[4];
			if($bch=="")
			{
				$bch="batch001";
			}
			$qnt=$v[5];
			$free=$v[6];
			if($itm)
			{
				$ord_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'"));
				$itm_strp_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
				$rcv_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifNull(SUM(`recpt_quantity`),0) as rcv_qnt FROM `inv_main_stock_received_detail` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `SuppCode`='$supp'"));
				if($ord_det['order_qnt']==($rcv_det['rcv_qnt']/$itm_strp_qnt['strip_quantity']))
				{
					mysqli_query($link,"UPDATE `inv_purchase_order_details` SET `stat`='1' WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'");
				}
			}
		}
		$ord_r=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) as all_stat FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `supplier_id`='$supp' AND `stat`='0'"));
		if($ord_r['all_stat']==0)
		{
			mysqli_query($link,"UPDATE `inv_purchase_order_master` SET `stat`='1' WHERE `order_no`='$ord' AND `supplier_id`='$supp' AND `stat`='0'");
		}
		}
		// stationary items
		if($others)
		{
			$al=explode("#%#",$others);
			foreach($al as $a)
			{
				$v=explode("@@",$a);
				$itm=$v[0];
				$cost=$v[1];
				$mrp=$v[2];
				$qnt=$v[3];
				if($itm && $cost && $qnt)
				{
					$i_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'"));
					$txt.="\nSELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'";
					
					mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`receive_no`, `order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$rcv_no','$ord','$sup_bill_no','$itm','0000-00-00','0000-00-00','$date','$qnt','0','','$supp','$mrp','$cost','0','$i_det[amount]','0','0','0','$i_det[gst_amt]')");
					
					$txt.="\nINSERT INTO `inv_main_stock_received_detail`(`receive_no`, `order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$rcv_no','$ord','$sup_bill_no','$itm','0000-00-00','0000-00-00','$date','$qnt','0','','$supp','$mrp','$cost','0','$i_det[amount]','0','0','0','$i_det[gst_amt]')";
					
					//----------stock entry------------------
					
					$stk_qnt=0;
					//$qnt=$qnt+$free; // add free qty
					$stk_master=mysqli_fetch_assoc(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' order by  date desc"));
					$txt.="\nselect * from inv_mainstock_details where date='$date' and item_id='$itm' order by  date desc";
					if($stk_master['item_id']!='')
					{
						$stk_qnt=$stk_master['closing_qnty']+$qnt;
						$rcv_qnt=$stk_master['recv_qnty']+$qnt;
						mysqli_query($link,"update inv_mainstock_details set closing_qnty='$stk_qnt',recv_qnty='$rcv_qnt' where date='$date' and item_id='$itm'");
						$txt.="\nupdate inv_mainstock_details set closing_qnty='$stk_qnt',recv_qnty='$rcv_qnt' where date='$date' and item_id='$itm'";
						mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm'");
						$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm'";
						mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','','$stk_qnt','0000-00-00')");
						$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','','$stk_qnt','0000-00-00')";
					}
					else
					{
						$stk_master=mysqli_fetch_assoc(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' order by slno desc"));
						$txt.="\nselect * from inv_mainstock_details where item_id='$itm' order by slno desc";
						$stk_qnt=$stk_master['closing_qnty']+$qnt;
						mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','','$date','$stk_master[closing_qnty]','$qnt',0,'$stk_qnt')");
						$txt.="\ninsert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','','$date','$stk_master[closing_qnty]','$qnt',0,'$stk_qnt')";
						mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm'");
						$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm'";
						mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','','$stk_qnt','$exp_dt')");
						$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','','$stk_qnt','$exp_dt')";
					}
				}
			}
			
			$al=explode("#%#",$others);
			foreach($al as $a)
			{
				$v=explode("@@",$a);
				$itm=$v[0];
				$cost=$v[1];
				$mrp=$v[2];
				$qnt=$v[3];
				if($itm && $cost && $qnt)
				{
					$ord_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'"));
					$itm_strp_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
					$rcv_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifNull(SUM(`recpt_quantity`),0) as rcv_qnt FROM `inv_main_stock_received_detail` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `SuppCode`='$supp'"));
					if($ord_det['order_qnt']==($rcv_det['rcv_qnt']))
					{
						mysqli_query($link,"UPDATE `inv_purchase_order_details` SET `stat`='1' WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'");
					}
				}
			}
			$ord_r=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) as all_stat FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `supplier_id`='$supp' AND `stat`='0'"));
			if($ord_r['all_stat']==0)
			{
				mysqli_query($link,"UPDATE `inv_purchase_order_master` SET `stat`='1' WHERE `order_no`='$ord' AND `supplier_id`='$supp' AND `stat`='0'");
			}
		}
		$txt.="\n---------------------------------------------------------------------------------------------------";
		text_query($txt);
		$sl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$rcv_no'"));
		echo "Done@@".$sl['slno'];
	}
	else
	{
		echo "Error@@0";
	}
}

if($type==12-121212)
{
	function text_query($txt)
	{
		if($txt)
		{
			$myfile = file_put_contents('log/order_received.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
		}
	}
	
	$ord=$_POST['ord'];
	$supp=$_POST['supp'];
	$bill_no=$_POST['bill_no'];
	$bill_dt=$_POST['bill_dt'];
	$user=$_POST['user'];
	$all=$_POST['all'];
	
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$ord'"));
	if(mysqli_query($link,"INSERT INTO `inv_main_stock_received_master`(`receipt_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$ord','$bill_dt','$date','$det[net_amount]','$det[gst_amount]','0','$det[net_amount]','$supp','$bill_no','$user','$time','0','0')"))
	{
		$txt.="\nINSERT INTO `inv_main_stock_received_master`(`receipt_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$ord','$bill_dt','$date','$det[net_amount]','$det[gst_amount]','0','$det[net_amount]','$supp','$bill_no','$user','$time','0','0')";
		
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@",$a);
			$itm=$v[0];
			$bch=$v[1];
			$qnt=$v[2];
			$exp_dt=$v[3]."-01";
			$exp_dt=date("Y-m-t", strtotime($exp_dt));
			if($itm && $bch)
			{
				$itm_strp=mysqli_fetch_assoc(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
				$qnt=$qnt*$itm_strp['strip_quantity'];
				
				$i_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'"));
				$txt.="\nSELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'";
				
				mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$ord','$bill_no','$itm','0000-00-00','$exp_dt','$date','$qnt','0','$bch','$supp','$i_det[mrp]','$i_det[mrp]','0','$i_det[amount]','0','0','$i_det[gst]','$i_det[gst_amt]')");
				
				$txt.="\nINSERT INTO `inv_main_stock_received_detail`(`order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$ord','$bill_no','$itm','0000-00-00','$exp_dt','$date','$qnt','0','$bch','$supp','$i_det[mrp]','$i_det[mrp]','0','$i_det[amount]','0','0','$i_det[gst]','$i_det[gst_amt]')";
				
				//----------stock entry------------------
				
				$stk_qnt=0;
				$stk_master=mysqli_fetch_assoc(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and batch_no='$bch' order by  date desc"));
				$txt.="\nselect * from inv_mainstock_details where date='$date' and item_id='$itm' and batch_no='$bch' order by  date desc";
				if($stk_master['item_id']!='')
				{
					$stk_qnt=$stk_master['closing_qnty']+$qnt;
					$rcv_qnt=$stk_master['recv_qnty']+$qnt;
					mysqli_query($link,"update inv_mainstock_details set closing_qnty='$stk_qnt',recv_qnty='$rcv_qnt' where date='$date' and item_id='$itm' and batch_no='$bch'");
					$txt.="\nupdate inv_mainstock_details set closing_qnty='$stk_qnt',recv_qnty='$rcv_qnt' where date='$date' and item_id='$itm' and batch_no='$bch'";
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
					mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$stk_qnt','$exp_dt')");
					$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$stk_qnt','$exp_dt')";
				}
				else
				{
					$stk_master=mysqli_fetch_assoc(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc"));
					$txt.="\nselect * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc";
					$stk_qnt=$stk_master['closing_qnty']+$qnt;
					mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','$bch','$date','$stk_master[closing_qnty]','$qnt',0,'$stk_qnt')");
					$txt.="\ninsert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','$bch','$date','$stk_master[closing_qnty]','$qnt',0,'$stk_qnt')";
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
					mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$stk_qnt','$exp_dt')");
					$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$stk_qnt','$exp_dt')";
				}
			}
		}
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@",$a);
			$itm=$v[0];
			$bch=$v[1];
			$qnt=$v[2];
			if($itm && $bch)
			{
				$ord_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'"));
				$itm_strp_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
				$rcv_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifNull(SUM(`recpt_quantity`),0) as rcv_qnt FROM `inv_main_stock_received_detail` WHERE `order_no`='$ord' AND `item_id`='$itm' AND `SuppCode`='$supp'"));
				if($ord_det['order_qnt']==($rcv_det['rcv_qnt']/$itm_strp_qnt['strip_quantity']))
				{
					mysqli_query($link,"UPDATE `inv_purchase_order_details` SET `stat`='1' WHERE `order_no`='$ord' AND `item_id`='$itm' AND `supplier_id`='$supp'");
				}
			}
		}
		$ord_r=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) as all_stat FROM `inv_purchase_order_details` WHERE `order_no`='$ord' AND `supplier_id`='$supp' AND `stat`='0'"));
		if($ord_r['all_stat']==0)
		{
			mysqli_query($link,"UPDATE `inv_purchase_order_master` SET `stat`='1' WHERE `order_no`='$ord' AND `supplier_id`='$supp' AND `stat`='0'");
		}
		$txt.="\n---------------------------------------------------------------------------------------------------";
		text_query($txt);
	}
}

if($type==13)
{
	$sub_store=$_POST['sub_store'];
	$all=$_POST['all'];
	$user=$_POST['user'];
	
	$billnos=100;
	$dis_year=date("Y");
	$dis_month=date("m");
	$dis_year_sm=date("y");
	$c_m_y=$dis_year."-".$dis_month."-";
	//$c_m_y=$dis_year;
	//~ $bill_no_qry=mysqli_fetch_array(mysqli_query($link,"SELECT count(`order_no`) as tot FROM `inv_substore_indent_order_master` WHERE `order_date` like '$c_m_y%' "));
	//~ $bill_num=$bill_no_qry["tot"];
	//~ $bill_tot_num=$bill_num;
	//~ if($bill_tot_num==0)
	//~ {
		//~ $bill_no=$billnos+1;
	//~ }else
	//~ {
		//~ $bill_no=$billnos+$bill_tot_num+1;
	//~ }
	//~ //$vid=$bill_no."/".$dis_year_sm;
	//~ $vid="REQ".$bill_no.$dis_month.$dis_year_sm;
	
	$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`order_no`) as tot FROM `inv_substore_indent_order_master`"));
	$bill_num=$bill_no_qry["tot"];

	$bill_tot_num=$bill_num;

	if($bill_tot_num==0)
	{
		//$bill_no=$billnos+1;
		$bill_no=1;
	}
	else
	{
		//$bill_no=$billnos+$bill_tot_num+1;
		$bill_no=$bill_tot_num+1;
	}
	
	$vid="REQ".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
	//echo $vid;
	$approval=0; // required approval
	//$approval=1; // bydefault approved
	if(mysqli_query($link,"INSERT INTO `inv_substore_indent_order_master`(`order_no`, `substore_id`, `order_date`, `stat`, `approve`, `user`, `time`) VALUES ('$vid','$sub_store','$date','0','$approval','$user','$time')"))
	{
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@",$a);
			$itm=$v[0];
			$qnt=$v[1];
			if($itm && $qnt)
			{
				mysqli_query($link,"INSERT INTO `inv_substore_order_details`(`order_no`, `item_id`, `substore_id`, `order_qnt`, `bl_qnt`, `order_date`, `stat`) VALUES ('$vid','$itm','$sub_store','$qnt','$qnt','$date','0')");
			}
		}
		echo "1";
	}
	else
	{
		echo "2";
	}
} // 13

if($type==14)
{
	$supp=$_POST['supp'];
	$billno=$_POST['billno'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$bltype=$_POST['bltype'];
	$q="SELECT * FROM inv_purchase_receipt_master  WHERE bill_date between '$fdate' and '$tdate'";
	if($billno)
	{
		$q.=" AND bill_no='$billno'";
	}
	if($supp)
	{
		$q.=" AND supp_code='$supp'";
	}
	$q.=" order by bill_date desc";
	//echo $q;
	$qry=mysqli_query($link,$q);
	?>
	<table class="table table-condensed table-report table-bordered">
		<tr>
			<th width="5%">SL</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Supplier</th>
			<th style="text-align:right">Bill Amount</th>
			<th>User</th>
			<th>Print</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_assoc($qry))
		{
			$supp_name=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[supp_code]'"));
			$amt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `gst_amount`,`net_amount` FROM `inv_purchase_order_master` WHERE `order_no`='$r[order_no]'"));
			$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
			if($amt['net_amount'])
			{
				$amount=$amt['net_amount'];
			}
			else
			{
				$amount=$r['bill_amount'];
			}
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo $r['bill_date'];?></td>
			<td><?php echo $supp_name['name'];?></td>
			<td style="text-align:right"><?php echo number_format($amount,2);?></td>
			<td><?php echo $usr['name'];?></td>
			<td>
				<button type="button" class="btn btn-mini btn-primary" onclick="report_print_billwise('<?php echo $r['bill_no'];?>','<?php echo $r['supp_code'];?>')"><i class="icon-print icon-large"></i> Print</button>
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
} // 14

if($type==15)
{
	$supp=$_POST['supp'];
	if($supp>0)
	{
	//$pid = mysqli_query($link," SELECT 	item_id,item_name FROM `item_master` order by `item_name` ");
		$pid=mysqli_query($link,"SELECT b.`item_id`,b.`item_name` FROM `inv_supplier_items` a,`item_master` b WHERE a.`item_id`=b.`item_id` AND a.`supp_id`='$supp' ORDER BY b.`item_name`");
		while($pat1=mysqli_fetch_assoc($pid))
		{
		?>
			<option value="<?php echo $pat1['item_id'];?>"><?php echo $pat1['item_name'];?></option>
		<?php
		}
	}
	else
	{
		
	}
}

if($type==16)
{
	$id=$_POST['id'];
	$qry=mysqli_query($link,"SELECT * FROM `inv_substore_indent_order_master` WHERE `stat`='0' ORDER BY `slno` DESC");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		?>
		<table class="table table-condensed table-bordered table-report">
			<tr>
				<th width="5%">Sl</th>
				<th>Request No</th>
				<th>Department</th>
				<th>Order Date</th>
				<th>User</th>
				<th>Action</th>
			</tr>
			<?php
			$j=1;
			while($r=mysqli_fetch_assoc($qry))
			{
				$dep=mysqli_fetch_assoc(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$r[substore_id]'"));
				$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
			?>
			<tr>
				<td><?php echo $j;?></td>
				<td><?php echo $r['order_no'];?></td>
				<td><?php echo $dep['substore_name'];?></td>
				<td><?php echo convert_date($r['order_date']);?></td>
				<td><?php echo $usr['name'];?></td>
				<td>
					<?php
					if($r['approve']==1)
					{
					?>
					<button type="button" class="btn btn-primary btn-mini" onclick="view_dept_req('<?php echo $r['order_no'];?>')"><i class="icon-eye-open"></i> <b>View</b></button>
					<button type="button" class="btn btn-success btn-mini" disabled="disabled"><i class="icon-ok"></i> <b>Approved</b></button>
					<?php
					}
					if($r['approve']==0)
					{
					?>
					<button type="button" class="btn btn-primary btn-mini" onclick="view_dept_req('<?php echo $r['order_no'];?>')"><i class="icon-eye-open"></i> <b>View</b></button>
					<button type="button" class="btn btn-success btn-mini" onclick="approv_dept_req('<?php echo $r['order_no'];?>')"><i class="icon-ok"></i> <b>Approve</b></button>
					<button type="button" class="btn btn-danger btn-mini" onclick="cancel_dept_req('<?php echo $r['order_no'];?>')"><i class="icon-remove"></i> <b>Cancel</b></button>
					<?php
					}
					if($r['approve']==2)
					{
					?>
					<button type="button" class="btn btn-primary btn-mini" onclick="view_dept_req('<?php echo $r['order_no'];?>')"><i class="icon-eye-open"></i> <b>View</b></button>
					<button type="button" class="btn btn-danger btn-mini" disabled="disabled"><i class="icon-remove"></i> <b>Cancelled</b></button>
					<?php
					}
					?>
				</td>
			</tr>
			<?php
			$j++;
			}
			?>
		</table>
		<?php
	}
} // 16

if($type==17)
{
	$ord=$_POST['ord'];
	$sid=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_substore_indent_order_master` WHERE `order_no`='$ord'"));
	$dep=mysqli_fetch_assoc(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$sid[substore_id]'"));
	$usr=mysqli_fetch_assoc(mysqli_query($link,"select name from employee where emp_id='$sid[user]'"));
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th></th>
			<th>Request No. : <?php echo $ord;?></th>
			<th>Department : <?php echo $dep['substore_name'];?></th>
			<th>Order Date : <?php echo convert_date($sid['order_date']);?></th>
			<th>User : <?php echo $usr['name'];?></th>
			<th></th>
		</tr>
		<tr>
			<th>#</th>
			<th colspan="4">Description</th>
			<th style="text-align:right;">Quantity</th>
		</tr>
	<?php
	$i=1;
	$gst_tot=0;
	$net_tot=0;
	$qry=mysqli_query($link,"SELECT * FROM `inv_substore_order_details` WHERE `order_no`='$ord'");
	while($r=mysqli_fetch_assoc($qry))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`hsn_code`,`mrp`,`gst` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		$id=$r['item_id'];
		$bch="";
		$qnt=$r['order_qnt'];
		$mrp=$r['mrp'];
		$gst_per=explode(".",$r['gst']);
		$gst_per=$gst_per[0];
		$gst_amt=$r['gst_amt'];
		$amount=$r['amount'];
		$gst_tot+=$gst_amt;
		$net_tot+=$amount;
	?>
		<tr>
			<td><?php echo $i;?></td>
			<td colspan="4"><?php echo $itm['item_name'];?></td>
			<td style="text-align:right;"><?php echo $r['order_qnt'];?></td>
		</tr>
	<?php
	$i++;
	}
	?>
	</table>
	<?php
} // 17

if($type==18)
{
	$ord=$_POST['ord'];
	$user=$_POST['user'];
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `approve` FROM `inv_substore_indent_order_master` WHERE `order_no`='$ord'"));
	if($det['approve']=="0")
	{
		mysqli_query($link,"UPDATE `inv_substore_indent_order_master` SET `approve`='1' WHERE `order_no`='$ord'");
		mysqli_query($link,"INSERT INTO `inv_sub_dept_ord_approve`(`order_no`, `approve`, `date`, `time`, `user`) VALUES ('$ord','1','$date','$time','$user')");
		echo "Approved";
	}
	else if($det['approve']=="1")
	{
		echo "Already Approved";
	}
	else if($det['approve']=="2")
	{
		echo "Already Cancelled";
	}
} // 18

if($type==19)
{
	$ord=$_POST['ord'];
	$user=$_POST['user'];
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `approve` FROM `inv_substore_indent_order_master` WHERE `order_no`='$ord'"));
	if($det['approve']=="0")
	{
		mysqli_query($link,"UPDATE `inv_substore_indent_order_master` SET `approve`='2' WHERE `order_no`='$ord'");
		mysqli_query($link,"INSERT INTO `inv_sub_dept_ord_approve`(`order_no`, `approve`, `date`, `time`, `user`) VALUES ('$ord','2','$date','$time','$user')");
		echo "Cancelled";
	}
	else if($det['approve']=="1")
	{
		echo "Already Approved";
	}
	else if($det['approve']=="2")
	{
		echo "Already Cancelled";
	}
} // 19

if($type==20)
{
	$emps=$_POST['emps'];
	$j=1;
	$q=mysqli_query($link,"SELECT * FROM `inv_sub_store` ORDER BY `substore_name`");
	if(mysqli_num_rows($q)>0)
	{
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th width="5%"></th>
				<th>Sub Department</th>
			</tr>
			<?php
			while($r=mysqli_fetch_assoc($q))
			{
				$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `inv_sub_dept_access` WHERE `emp_id`='$emps' AND `substore_id`='$r[substore_id]'"));
				if($chk)
				{
					$checked="checked";
				}
				else
				{
					$checked="";
				}
			?>
			<tr>
				<td><?php echo $j;?></td>
				<td>
					<label>
						<div class="checker" id="uniform-undefined"><span class="<?php echo $checked;?>"><input id="r<?php echo $r['substore_id'];?>" class="chk" value="<?php echo $r['substore_id'];?>" onchange="chk_change(this.id)" <?php echo $checked;?> type="checkbox"></span></div>
						<?php echo $r['substore_name'];?>
					</label>
				</td>
			</tr>
			<?php
			$j++;
			}
			?>
			<tr>
				<td colspan="2" style="text-align:center;">
					<button type="button" class="btn btn-success" id="btn_asg" onclick="dept_assign()">Assign</button>
				</td>
			</tr>
		</table>
		<?php
	}
} // 20

if($type==21)
{
	$emps=$_POST['emps'];
	$all=$_POST['all'];
	$al=explode("@",$all);
	mysqli_query($link,"DELETE FROM `inv_sub_dept_access` WHERE `emp_id`='$emps'");
	foreach($al as $a)
	{
		if($a)
		{
			mysqli_query($link,"INSERT INTO `inv_sub_dept_access`(`emp_id`, `substore_id`) VALUES ('$emps','$a')");
		}
	}
	echo "Assigned";
} // 21

if($type==22)
{
	$sub_dept=$_POST['sub_dept'];
	$q=mysqli_query($link,"SELECT * FROM `stock_sub_category_master` ORDER BY `sub_category_name`");
	if(mysqli_num_rows($q)>0)
	{
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th width="5%"></th>
				<th>Item Sub Category</th>
			</tr>
			<?php
			$j=1;
			while($r=mysqli_fetch_assoc($q))
			{
				$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_sub_dept_category` WHERE `substore_id`='$sub_dept' AND `sub_category_id`='$r[sub_category_id]'"));
				if($chk)
				{
					$checked="checked";
				}
				else
				{
					$checked="";
				}
			?>
			<tr>
				<td><?php echo $j;?></td>
				<td>
					<label>
						<div class="checker" id="uniform-undefined"><span class="<?php echo $checked;?>"><input id="sc<?php echo $r['sub_category_id'];?>" class="dept_items" value="<?php echo $r['sub_category_id'];?>" onchange="chk_change(this.id)" <?php echo $checked;?> type="checkbox"></span></div>
						<?php echo $r['sub_category_name'];?>
					</label>
				</td>
			</tr>
			<?php
			$j++;
			}
			?>
			<tr>
				<td colspan="2" style="text-align:center;">
					<button type="button" class="btn btn-success" id="btn_d_item" onclick="save_dept_items()">Assign</button>
				</td>
			</tr>
		</table>
		<?php
	}
} // 22

if($type==23)
{
	$sub_dept=$_POST['sub_dept'];
	$all=$_POST['all'];
	$al=explode("@",$all);
	mysqli_query($link,"DELETE FROM `inv_sub_dept_category` WHERE `substore_id`='$sub_dept'");
	foreach($al as $a)
	{
		if($a)
		{
			mysqli_query($link,"INSERT INTO `inv_sub_dept_category`(`substore_id`, `sub_category_id`) VALUES ('$sub_dept','$a')");
		}
	}
	echo "Saved";
} // 23

if($type==24)
{
	$sub_store=$_POST['sub_store'];
	
	$val="";
	$q=mysqli_query($link,"SELECT * FROM `inv_sub_dept_category` WHERE `substore_id`='$sub_store' ORDER BY `sub_category_id`");
	if(mysqli_num_rows($q)>0)
	{
		$i=0;
		$qry="SELECT `item_id`,`item_name` FROM `item_master` WHERE ";
		while($r=mysqli_fetch_assoc($q))
		{
			if($i==0)
			{
				$qry.=" `sub_category_id`='$r[sub_category_id]'";
			}
			else
			{
				$qry.=" OR `sub_category_id`='$r[sub_category_id]'";
			}
			$i++;
		}
		//echo $qry;
		$qq=mysqli_query($link,$qry);
		while($itm=mysqli_fetch_assoc($qq))
		{
			$val.="<option value='$itm[item_id]' mytag='$itm[item_name]' >$itm[item_name]</option>";
		}
	}
	echo $val;
} // 24

if($type==25)
{
	$supp=$_POST['supp'];
	$billno=$_POST['billno'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$q="SELECT * FROM `inv_supplier_payment_details`  WHERE `date` between '$fdate' and '$tdate'";
	if($billno)
	{
		$q.=" AND bill_no='$billno'";
	}
	if($supp)
	{
		$q.=" AND supp_code='$supp'";
	}
	$q.=" order by date desc";
	//echo $q;
	$qry=mysqli_query($link,$q);
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th width="5%">SL</th>
			<th>Bill No</th>
			<th>Supplier Name</th>
			<th>Date</th>
			<th>User</th>
			<th>Print</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_assoc($qry))
		{
			$sup=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[supp_code]'"));
			$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo $sup['name'];?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $usr['name'];?></td>
			<td>
				<button type="button" class="btn btn-primary btn-mini" onclick="print_pay('<?php echo $r['slno'];?>')">Print</button>
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
} // 25

if($type==26)
{
	$supp=$_POST['supp'];
	$val=$_POST['val'];
	if(trim($_POST['typ'])=="item")
	{
	?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Item Code</th><th>Item Name</th><th>GST %</th><th>Stock</th>
			</tr>
	<?php
		
		if($val)
		{
			//$q="select a.* from item_master a, ph_stock_master b where a.item_name!='' and a.item_name like '$dname%' or a.item_name!='' and a.item_id like '$dname%' or a.item_name!='' and a.short_name like '$dname%' order by a.item_name limit 0,30";
			
			$q="select distinct b.`item_id` from item_master a, inv_supplier_items b, inv_maincurrent_stock c where";
			$q.=" a.item_id=b.item_id and b.supp_id='$supp' and a.item_id=c.item_id and c.closing_stock>0 and a.item_name!='' and a.item_name like '$val%'";
			$q.=" or a.item_id=b.item_id and b.supp_id='$supp' and a.item_id=c.item_id and c.closing_stock>0 and a.item_name!='' and a.item_id like '$val%'";
			$q.=" or a.item_id=b.item_id and b.supp_id='$supp' and a.item_id=c.item_id and c.closing_stock>0 and a.item_name!='' and a.short_name like '$val%'";
			$q.=" order by a.item_name limit 0,30";
		}
		else
		{
			$q="select distinct b.`item_id` from item_master a, inv_supplier_items b, inv_maincurrent_stock c where a.item_name!='' and a.item_id=b.item_id and b.supp_id='$supp' and a.item_id=c.item_id and c.closing_stock>0 ";
			$q.=" order by a.item_name limit 0,30";
		}
		//echo $q;
		$d=mysqli_query($link, $q);
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `item_master` WHERE `item_id`='$d1[item_id]'"));
			$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`closing_stock`),0) AS stock FROM `inv_maincurrent_stock` WHERE `item_id`='$d1[item_id]'"));
			$gst=explode(".",$itm['gst']);
			$gst=$gst[0];
			?>
			<tr onclick="doc_load('<?php echo $itm['item_id'];?>','<?php echo $itm['item_name'];?>','<?php echo $itm['gst_percent'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
				<td><?php echo $itm['item_id'];?></td>
				<td><?php echo $itm['item_name'];?>
					<div <?php echo "id=dvdoc".$i;?> style="display:none;">
					<?php echo "#".$itm['item_id']."#".$itm['item_name']."#".$gst;?>
					</div>
				</td>
				<td><?php echo $itm['gst'];?></td>
				<td><?php echo $stk['stock'];?></td>
			</tr>
		<?php
			$i++;
		}
		?>
		</table>
	<?php
	}
} // 26

if($type==27)
{
	$item_id=$_POST['item_id'];
	$supp=$_POST['supp'];
	$val=$_POST['val'];
	
	if(trim($_POST['typ'])=="batch")
	{
		$item_id=$_POST['item_id'];
	?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Batch No</th><th>Stock</th><th>Rate</th><th>Expiry</th>
			</tr>
	<?php
		
		if($val)
		{
			$d=mysqli_query($link, "SELECT * FROM `inv_maincurrent_stock` WHERE `item_id`='$item_id' AND `batch_no` like '$val%' AND `closing_stock`>0 ORDER BY `exp_date`");
		}
		else
		{
			$d=mysqli_query($link, "SELECT * FROM `inv_maincurrent_stock` WHERE `item_id`='$item_id' AND `closing_stock`>0 ORDER BY `exp_date`");
		}
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			$cost=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recept_cost_price` FROM `inv_main_stock_received_detail` WHERE `item_id`='$item_id' AND `recept_batch`='$d1[batch_no]' AND `SuppCode`='$supp'"));
			if($cost)
			{
				$cost_price=$cost['recept_cost_price'];
			}
			else
			{
				$cost=mysqli_fetch_assoc(mysqli_query($link,"SELECT `cost_price` FROM `inv_supplier_items` WHERE `item_id`='$item_id' AND `supp_id`='$supp'"));
				$cost_price=$cost['cost_price'];
			}
			
			if($d1['exp_date']!="0000-00-00")
			{
				$exp_dt=date('Y-m', strtotime($d1['exp_date']));
			}
			else
			{
				$exp_dt="";
			}
			if($d1['batch_no']=="")
			{
				$bch_val=0;
			}
			else
			{
				$bch_val=1;
			}
			?>
			<tr onClick="hguide_load('<?php echo $d1['batch_no'];?>','<?php echo $d1['closing_stock'];?>','<?php echo $exp_dt;?>','<?php echo $bch_val;?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
				<td><?php echo $d1['batch_no'];?></td>
				<td>
					<?php echo $d1['closing_stock'];?>
					<div <?php echo "id=dvhguide".$i;?> style="display:none;">
						<?php echo "#".$d1['batch_no']."#".$d1['closing_stock']."#".$cost_price."#".$exp_dt."#".$bch_val;?>
					</div>
				</td>
				<td><?php echo $cost_price;?></td>
				<td><?php echo $exp_dt;?></td>
			</tr>
		<?php
			$i++;
		}
		?>
		</table>
	<?php
	}
} // 27

if($type==28)
{
	$supp=$_POST['supp'];
	$reason=$_POST['reason'];
	$final_gst=$_POST['final_gst'];
	$final_rate=$_POST['final_rate'];
	$user=$_POST['user'];
	$all=$_POST['all'];
	$net_amount=$final_gst+$final_rate;
	
	$c_m_y=date("Y-m-");
	$ret_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`returnr_no`) as tot FROM `inv_item_return_supplier_master` WHERE `date` like '$c_m_y%' "));
	$bill_num=$ret_no_qry["tot"];
	
	$ret_no=$bill_num+1;
	$ret_no.="/".date("my");
	
	mysqli_query($link,"delete from inv_item_return_supplier_detail where returnr_no='$ret_no' and supplier_id='$supp'");
	mysqli_query($link,"delete from inv_item_return_supplier_master where returnr_no ='$ret_no' and supplier_id='$supp'");
	//if(mysqli_query($link,"insert into inv_item_return_supplier_master(`returnr_no`, `supplier_id`, `date`, `stat`, `del`, `user`, `time`) values('$ret_no','$supp','$date',0,0,'$user','$time')"))
	if(mysqli_query($link,"INSERT INTO `inv_item_return_supplier_master`(`returnr_no`, `supplier_id`, `amount`, `gst_amount`, `net_amount`, `date`, `stat`, `del`, `user`, `time`) values('$ret_no','$supp','$final_rate','$final_gst','$net_amount','$date',0,0,'$user','$time')"))
	{
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@@",$a);
			$itm=$v[0];
			$bch=$v[1];
			$qnt=$v[2];
			$free=$v[3];
			$cost=$v[4];
			$amt=$v[5];
			$gst_per=$v[6];
			$gst_amt=$v[7];
			if($itm && $qnt)
			{
				$vqnt=$qnt+$free;
				$itm_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recpt_mrp`,`recept_cost_price`,`expiry_date`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount` FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `recept_batch`='$bch' AND `SuppCode`='$supp'"));
				$vitmamt=$cost*$qnt;
				$vst=0;
				$vstkqnt=0;
				$gstamt=0;
				
				mysqli_query($link,"INSERT INTO `inv_item_return_supplier_detail`(`returnr_no`, `reason`, `item_id`, `expiry_date`, `date`, `quantity`, `free_qnt`, `batch_no`, `supplier_id`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$ret_no','$reason','$itm','$itm_det[expiry_date]','$date','$qnt','$free','$bch','$supp','$itm_det[recpt_mrp]','$cost','$vitmamt','$itm_det[dis_per]','$itm_det[dis_amt]','$gst_per','$gst_amt')");
				
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and batch_no='$bch' order by date desc "));
				if($qrstkmaster['item_id']!='')
				{
					$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
					$isuqnt=$qrstkmaster['issu_qnty']+$vqnt;
					mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',issu_qnty='$isuqnt' where  date='$date' and item_id='$itm' and batch_no='$bch' ");
					mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$itm' and batch_no='$bch'");
				}
				else///for if data not found
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc"));
					$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
					mysqli_query($link,"insert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) value('$itm','$bch','$date','$qrstkmaster[closing_qnty]',0,'$vqnt','$vstkqnt') ");
					mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$itm' and batch_no='$bch'");
				}
			}
		}
		echo "Done";
	}
	else
	{
		echo "Error";
	}
} // 28

if($type==29)
{
	$supp=$_POST['supp'];
	$billno=$_POST['billno'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	$q="SELECT * FROM `inv_item_return_supplier_master` ";
	if($fdate && $tdate)
	{
		$q="SELECT * FROM `inv_item_return_supplier_master` WHERE `date` BETWEEN '$fdate' AND '$tdate'";
	}
	if($supp)
	{
		$q.=" AND `supplier_id`='$supp'";
	}
	if($billno)
	{
		$q.=" AND `returnr_no`='$billno'";
	}
	//echo $q;
	$qry=mysqli_query($link,$q);
	if(mysqli_num_rows($qry)>0)
	{
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th width="5%">SL</th>
			<th>Return No</th>
			<th>Supplier</th>
			<th>Date</th>
			<th style="text-align:right;">Amount</th>
			<th style="text-align:right;">GST Amount</th>
			<th style="text-align:right;">Net Amount</th>
			<th>User</th>
			<th>Print</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_assoc($qry))
		{
			$sp=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[supplier_id]'"));
			$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['returnr_no'];?></td>
			<td><?php echo $sp['name'];?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td style="text-align:right;"><?php echo $r['amount'];?></td>
			<td style="text-align:right;"><?php echo $r['gst_amount'];?></td>
			<td style="text-align:right;"><?php echo $r['net_amount'];?></td>
			<td><?php echo $usr['name'];?></td>
			<td>
				<button type="button" class="btn btn-primary btn-mini" onclick="print_return_bill('<?php echo $r['returnr_no'];?>')"><i class="icon-print icon-large"></i> Print</button>
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
	}
} // 29

if($type==30)
{
	$itm=$_POST['itm'];
	$bch=$_POST['bch'];
	$supp=$_POST['supp'];
	$d=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `recept_batch`='$bch' AND `SuppCode`='$supp' ORDER BY `order_no` DESC "));
	$dt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_date` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$d[receive_no]'"));
	if($d)
	{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Invoice</th>
			<th>Invoice date</th>
			<th>Rcv Qty</th>
			<th>Free Qty</th>
			<th>Received</th>
		</tr>
		<tr>
			<td><?php echo $d['bill_no'];?></td>
			<td><?php echo convert_date($dt['bill_date']);?></td>
			<td><?php echo $d['recpt_quantity'];?></td>
			<td><?php echo $d['free_qnt'];?></td>
			<td><?php echo convert_date($d['recpt_date']);?></td>
		</tr>
	</table>
	<?php
	}
} // 30

if($type==31)
{
	function text_query($txt)
	{
		if($txt)
		{
			$myfile = file_put_contents('log/req_issued.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
		}
	}
	
	$billno=$_POST['all'];
	$cid=$_POST['cid'];
	$pamt=$_POST['pamt'];
	$date1=$_POST['date1'];
	$sbstrid=$_POST['sbstrid'];
	$user=$_POST['user'];
	$userid=$user;
	
	$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`issue_no`) as tot FROM `inv_mainstore_direct_issue_master`"));
	$bill_num=$bill_no_qry["tot"];

	$bill_tot_num=$bill_num;

	if($bill_tot_num==0)
	{
		//$bill_no=$billnos+1;
		$bill_no=1;
	}
	else
	{
		//$bill_no=$billnos+$bill_tot_num+1;
		$bill_no=$bill_tot_num+1;
	}
	
	$vid="ISU".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
	
	$txt="";
	$billno=explode("#%#",$billno);
	foreach($billno as $blno)
	{
		if($blno)
		{
			$vl=explode("@@",$blno);
			$itm=$vl[0];
			$qnt=$vl[1];
			$bch=$vl[2];
			if($bch=="")
			{
				$bch="batch001";
			}
			
			if($itm && $qnt)
			{
				$qexpiry=mysqli_fetch_array(mysqli_query($link,"select expiry_date from inv_main_stock_received_detail where item_id='$itm' and recept_batch='$bch' order by slno desc limit 0,1"));
				$txt.="\nselect expiry_date from inv_main_stock_received_detail where item_id='$itm' and recept_batch='$bch' order by slno desc limit 0,1";

				$qdetail=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,recept_cost_price,sale_price,gst_per from inv_main_stock_received_detail where item_id='$itm' and recept_batch='$bch' order by slno desc limit 0,1"));
				$txt.="\nselect recpt_mrp,recept_cost_price,sale_price,gst_per from inv_main_stock_received_detail where item_id='$itm' and recept_batch='$bch' order by slno desc limit 0,1";

				$vitmamt=$qnt*$qdetail['recept_cost_price'];
				
				$qstkrcv1=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$date1' and item_code='$itm' and batch_no='$bch' and substore_id='$sbstrid'"));
				if($qstkrcv1) // ph_stock_process same date
				{
					$txt.="\nselect * from ph_stock_process where date='$date1' and item_code='$itm' and batch_no='$bch' and substore_id='$sbstrid'";
					$vrcvqnt=$qstkrcv1['added']+$qnt;
					$vclsnqnt=$qstkrcv1['s_remain']+$qnt;
					
					mysqli_query($link,"update ph_stock_process set added='$vrcvqnt',s_remain='$vclsnqnt' where date='$date1' and item_code='$itm' and batch_no='$bch' and substore_id='$sbstrid'");
					$txt.="\nupdate ph_stock_process set added='$vrcvqnt',s_remain='$vclsnqnt' where date='$date1' and item_code='$itm' and batch_no='$bch' and substore_id='$sbstrid'";
					
					mysqli_query($link,"delete from ph_stock_master where item_code='$itm' and batch_no='$bch' and substore_id='$sbstrid'");
					$txt.="\ndelete from ph_stock_master where item_code='$itm' and batch_no='$bch' and substore_id='$sbstrid'";
					
					mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$sbstrid','$itm','$bch','$vclsnqnt','0000-00-00','$qexpiry[expiry_date]')");
					$txt.="\ninsert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$sbstrid','$itm','$bch','$vclsnqnt','0000-00-00','$qexpiry[expiry_date]')";
				}
				else // ph_stock_process not same date
				{
					$qstkrcv2=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$sbstrid' order by date DESC limit 0,1"));
					$txt.="\nselect * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$sbstrid' order by date DESC limit 0,1";
					
					$vclsnqnt=$qstkrcv2['s_remain']+$qnt;
					
					mysqli_query($link,"insert into ph_stock_process(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$sbstrid','$cid','$itm','$bch','$qstkrcv2[s_remain]','$qnt',0,0,0,'$vclsnqnt','$date1')");
					$txt.="\ninsert into ph_stock_process(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$sbstrid','$cid','$itm','$bch','$qstkrcv2[s_remain]','$qnt',0,0,0,'$vclsnqnt','$date1')";
					
					mysqli_query($link,"delete from ph_stock_master where item_code='$itm' and batch_no='$bch' and substore_id='$sbstrid'");
					$txt.="\ndelete from ph_stock_master where item_code='$itm' and batch_no='$bch' and substore_id='$sbstrid'";
					
					mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$sbstrid','$itm','$bch','$vclsnqnt','0000-00-00','$qexpiry[expiry_date]')");
					$txt.="\ninsert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$sbstrid','$itm','$bch','$vclsnqnt','0000-00-00','$qexpiry[expiry_date]')";
				}
				
				////--------------for main store stock------------------////
				$qmainstk=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' and date='$date1'"));
				if($qmainstk) // inv_mainstock_details same date
				{
					$txt.="\nselect * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' and date='$date1'";
					$vissuqnt=$qmainstk['issu_qnty']+$qnt;
					$Vminclsnqnt=$qmainstk['closing_qnty']-$qnt;
					mysqli_query($link,"update inv_mainstock_details set issu_qnty='$vissuqnt',closing_qnty='$Vminclsnqnt' where item_id='$itm' and batch_no='$bch' and date='$date1'");
					$txt.="\nupdate inv_mainstock_details set issu_qnty='$vissuqnt',closing_qnty='$Vminclsnqnt' where item_id='$itm' and batch_no='$bch' and date='$date1'";
					
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
					
					mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$Vminclsnqnt','$qexpiry[expiry_date]')");
					$txt.="\ninsert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$Vminclsnqnt','$qexpiry[expiry_date]')";
				}
				else // inv_mainstock_details not same date
				{
					$qmainstk1=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno DESC limit 0,1"));
					$txt.="\nselect * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno DESC limit 0,1";
					
					$Vminclsnqnt=$qmainstk1['closing_qnty']-$qnt;
					mysqli_query($link,"insert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values ('$itm','$bch','$date1','$qmainstk1[closing_qnty]',0,'$qnt','$Vminclsnqnt')");
					$txt.="\ninsert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values ('$itm','$bch','$date1','$qmainstk1[closing_qnty]',0,'$qnt','$Vminclsnqnt')";

					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
					
					mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$Vminclsnqnt','$qexpiry[expiry_date]')");
					$txt.="\ninsert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$Vminclsnqnt','$qexpiry[expiry_date]')";
				}
				
				mysqli_query($link,"insert into inv_mainstore_issue_details (`order_no`,`item_id`,`batch_no`,`substore_id`,`issue_to`,`issue_qnt`,`issue_date`,`user`,`time`) values('$vid','$itm','$bch','$sbstrid','','$qnt','$date1','$userid','$time')");
				$txt.="\ninsert into inv_mainstore_issue_details (`order_no`,`item_id`,`batch_no`,`substore_id`,`issue_to`,`issue_qnt`,`issue_date`,`user`,`time`) values('$vid','$itm','$bch','$sbstrid','','$qnt','$date1','$userid','$time')";

				mysqli_query($link,"insert into ph_purchase_receipt_details (`order_no`,`bill_no`,`item_code`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`fid`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$cid','$cid','$itm','','$qexpiry[expiry_date]','$date1','$qnt',0,'$bch','','$qdetail[recpt_mrp]','$qdetail[recept_cost_price]','$qdetail[sale_price]',0,'$vitmamt',0,0,'$qdetail[gst_per]',0)");
				$txt.="\ninsert into ph_purchase_receipt_details (`order_no`,`bill_no`,`item_code`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`fid`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$cid','$cid','$itm','','$qexpiry[expiry_date]','$date1','$qnt',0,'$bch','','$qdetail[recpt_mrp]','$qdetail[recept_cost_price]','$qdetail[sale_price]',0,'$vitmamt',0,0,'$qdetail[gst_per]',0)";
				
				$ord_bal=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bl_qnt` FROM `inv_substore_order_details` where order_no='$cid' and item_id='$itm' and substore_id='$sbstrid'"));
				$txt.="\nSELECT `bl_qnt` FROM `inv_substore_order_details` where order_no='$cid' and item_id='$itm' and substore_id='$sbstrid'";
				
				$itm_bal=$ord_bal['bl_qnt']-$qnt; // balance quantity
				mysqli_query($link,"update inv_substore_order_details set `bl_qnt`='$itm_bal',`stat`='1' where order_no='$cid' and item_id='$itm' and substore_id='$sbstrid'");
				$txt.="\nupdate inv_substore_order_details set `bl_qnt`='$itm_bal',`stat`='1' where order_no='$cid' and  item_id='$itm' and substore_id='$sbstrid'";
			}
		}
	}
	if($txt)
	{
		mysqli_query($link,"insert into inv_mainstore_direct_issue_master(issue_no,order_no,substore_id,user,date,time) values('$vid','$cid','$sbstrid','$userid','$date1','$time')");
		$txt.="\ninsert into inv_mainstore_direct_issue_master(issue_no,order_no,substore_id,user,date,time) values('$vid','$cid','$sbstrid','$userid','$date1','$time')";
		mysqli_query($link,"insert into inv_substorestock_aproved_details(order_no,substore_id,user,date,time) values('$cid','$sbstrid','$userid','$date1','$time')");
		$txt.="\ninsert into inv_substorestock_aproved_details(order_no,substore_id,user,date,time) values('$cid','$sbstrid','$userid','$date1','$time')";
		$qchk=mysqli_fetch_array(mysqli_query($link,"select item_id from inv_substore_order_details where order_no='$cid' and substore_id='$sbstrid' and stat='0'"));
		$txt.="\nselect item_id from inv_substore_order_details where order_no='$cid' and substore_id='$sbstrid' and stat='0'";
		if(!$qchk)
		{
			mysqli_query($link,"update inv_substore_indent_order_master set stat='1' where order_no='$cid' and substore_id='$sbstrid'");
			$txt.="\nupdate inv_substore_indent_order_master set stat='1' where order_no='$cid' and substore_id='$sbstrid'";
		}
		
		$txt.="\n-----======================================================================================-----";
		text_query($txt);
		echo "Done";
	}
	else
	{
		echo "Cannot Issue";
	}
	
} // 31

if($type==32)
{
	$supp=$_POST['supp'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	$bal=mysqli_fetch_assoc(mysqli_query($link,"SELECT `balance` FROM `inv_supplier_transaction` WHERE `supp_code`='$supp' ORDER BY `slno` DESC LIMIT 0,1"));
	$b=explode(".",$bal['balance']);
	echo $b[0];
} // 32

if($type==33)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$supp=$_POST['supp'];
	if($supp)
	{
		$vals="";
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>#</th>
				<th>Bill Date</th>
				<th>Bill No</th>
				<th>Amount</th>
				<th>Details</th>
			</tr>
		<?php
		$i=1;
		$tot=0;
		//$qpdct=mysqli_query($link,"select bill_no,bill_amount from inv_main_stock_received_master where supp_code='$supp' and bill_no not in(select bill_no from inv_supplier_payment_master where supp_code='$supp' )  order by slno");
		if($fdate && $tdate)
		{
			// central store
			//$qpdct=mysqli_query($link,"select receipt_no,bill_date,bill_no,bill_amount,net_amt from inv_main_stock_received_master where supp_code='$supp' and bill_no not in(select bill_no from inv_supplier_payment_details where supp_code='$supp' ) and bill_date BETWEEN '$fdate' AND '$tdate' order by bill_date");
			
			// pharmacy
			$qpdct=mysqli_query($link,"select order_no,bill_date,bill_no,bill_amount,net_amt from ph_purchase_receipt_master where supp_code='$supp' and bill_no not in(select bill_no from inv_supplier_payment_details where supp_code='$supp' ) and bill_date BETWEEN '$fdate' AND '$tdate' order by bill_date");
		}
		else
		{
			// central store
			//$qpdct=mysqli_query($link,"select receipt_no,bill_date,bill_no,bill_amount,net_amt from inv_main_stock_received_master where supp_code='$supp' and bill_no not in(select bill_no from inv_supplier_payment_details where supp_code='$supp' ) order by bill_date"); // central store
			
			// pharmacy
			$qpdct=mysqli_query($link,"select order_no,bill_date,bill_no,bill_amount,net_amt from ph_purchase_receipt_master where supp_code='$supp' and bill_no not in(select bill_no from inv_supplier_payment_details where supp_code='$supp' ) order by bill_date");
		}
		while($qpdct1=mysqli_fetch_array($qpdct))
		{
			//$process_no=$qpdct1['receipt_no'];
			$process_no=$qpdct1['order_no'];
			?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo convert_date($qpdct1['bill_date']);?></td>
				<td><label><input type="checkbox" class="bill_amt" name="<?php echo $qpdct1['bill_no'];?>" value="<?php echo $qpdct1['net_amt'];?>" onchange="load_bill_amt()" /> <?php echo $qpdct1['bill_no'];?></label></td>
				<td><?php echo $qpdct1['net_amt'];?></td>
				<td>
					<button type="button" class="btn btn-primary btn-mini" onclick="view_bill_detail('<?php echo $process_no;?>')">View</button>
				</td>
			</tr>
			<?php
			$tot+=$qpdct1['net_amt'];
			$i++;
		}
		?>
			<tr>
				<th colspan="3" style="text-align:right;">Total :</th>
				<th colspan="2"><?php echo number_format($tot,2);?></th>
			</tr>
		</table>
		<?php
	}
} // 33

if($type==34)
{
	$supplrid=$_POST['supplrid'];
	$billno=$_POST['billno'];
	$ttlamt=$_POST['ttlamt'];
	$alrdypaid=$_POST['alrdypaid'];
	$adjst=$_POST['adjst'];
	$nwpaid=$_POST['nwpaid'];
	$balance=$_POST['balance'];
	$ptype=$_POST['ptype'];
	$collect=$_POST['collect'];
	$chqno=$_POST['chqno'];
	$chk_bank=$_POST['chk_bank'];
	$chk_reason=mysqli_real_escape_string($link,$_POST['chk_reason']);
	$pay_date=$_POST['pay_date'];
	$cr_note=$_POST['cr_note']; // credit note
	$note_no=mysqli_real_escape_string($link,$_POST['note_no']); // credit note
	$note_dt=$_POST['note_dt']; // credit note
	$note_amt=$_POST['note_amt']; // credit note
	if($cr_note=="0")
	{
		$note_no="";
		$note_dt="";
		$note_amt=0;
	}
	if($pay_date=="")
	{
		$pay_date=$date;
	}
	$user=$_POST['user'];
	
	$pay_no="PAY".date("YmdHis").$user;
	
	if(mysqli_query($link,"INSERT INTO `inv_supplier_payment_master`(`payment_no`, `supp_code`, `bill_no`, `total_amount`, `paid`, `adjustment`, `balance`, `credit_note`, `note_no`, `note_date`, `note_amt`, `payment_mode`, `collected_by`, `cheque_no`, `bank_id`, `date`, `time`, `user`) VALUES ('$pay_no','$supplrid','','$ttlamt','$nwpaid','$adjst','0','$cr_note','$note_no','$note_dt','$note_amt','$ptype','$collect','$chqno','$chk_bank','$pay_date','$time','$user')"))
	{
		$txt="INSERT INTO `inv_supplier_payment_master`(`payment_no`, `supp_code`, `bill_no`, `total_amount`, `paid`, `adjustment`, `balance`, `credit_note`, `note_no`, `note_date`, `note_amt`, `payment_mode`, `collected_by`, `cheque_no`, `bank_id`, `date`, `time`, `user`) VALUES ('$pay_no','$supplrid','','$ttlamt','$nwpaid','$adjst','0','$cr_note','$note_no','$note_dt','$note_amt','$ptype','$collect','$chqno','$chk_bank','$pay_date','$time','$user')\n";
		
		$supp_bal=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$supplrid' ORDER BY `slno` DESC LIMIT 0,1"));
		$final_balance=$supp_bal['balance_amt']-$nwpaid+$balance;
		if($final_balance<0)
		{
			$final_balance=0;
		}
		mysqli_query($link,"INSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `adjust_type`, `balance`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$pay_no','$supplrid','0','$nwpaid','$adjst','$adjust_type','$balance','$final_balance','2','$date','$time','$user')");
		
		if($chqno)
		{
			mysqli_query($link,"INSERT INTO `inv_supplier_cheque_payment`(`payment_no`, `supp_code`, `cheque_no`, `bank_id`, `cheque_reason`, `date`, `time`, `user`) VALUES ('$pay_no','$supplrid','$chqno','$chk_bank','$chk_reason','$pay_date','$time','$user')");
			$txt.="INSERT INTO `inv_supplier_cheque_payment`(`payment_no`, `supp_code`, `cheque_no`, `bank_id`, `cheque_reason`, `date`, `time`, `user`) VALUES ('$pay_no','$supplrid','$chqno','$chk_bank','$chk_reason','$pay_date','$time','$user')\n";
		}
		$bill=explode("#%#",$billno);
		foreach($bill as $bil)
		{
			$v=explode("@@",$bil);
			$bill_no=$v[0];
			$bill_amt=$v[1];
			if($bill_no)
			{
				mysqli_query($link,"INSERT INTO `inv_supplier_payment_details`(`payment_no`, `supp_code`, `bill_no`, `amount`) VALUES ('$pay_no','$supplrid','$bill_no','$bill_amt')");
				$txt.="INSERT INTO `inv_supplier_payment_details`(`payment_no`, `supp_code`, `bill_no`, `amount`) VALUES ('$pay_no','$supplrid','$bill_no','$bill_amt')\n";
			}
		}
		$txt.="---------------------------------------------------------------------------\n";
		//text_query($txt);
		echo "Paid";
	}
	else
	{
		echo "Error";
	}
} // 34

if($type==45)
{
	$supp=$_POST['supp'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$qry="SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$supp'";
	if($fdate && $tdate)
	{
		$qry.=" AND `date` BETWEEN '$fdate' AND '$tdate'";
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
		
		$open_bal=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$supp' AND `date`<'$fdate' ORDER BY `slno` DESC LIMIT 0,1"));
		$opening_bal=$open_bal['balance_amt'];
		$net_balance=$opening_bal;
		$dr_amt=$opening_bal;
		?>
		<tr>
			<td><?php echo $j;?></td>
			<th>Opening Balance</th>
			<td><?php //echo number_format($open_bal['bal_amount'],2);?></td>
			<td></td>
			<td></td>
			<td style="text-align:right;"><?php echo number_format($opening_bal,2);?></td>
			<td style="text-align:right;"><?php //echo number_format($amt,2);?></td>
			<td style="text-align:right;"><?php //echo number_format($amt,2);?></td>
			<td style="text-align:right;"><?php echo number_format($opening_bal,2);?></td>
		</tr>
	<?php
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
			$process_no="Payment ".$r['slno'];
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
		if($r['type']=="7")
		{
			$process_no=$r['process_no'];
			$payment_mode="Bill Edit";
		}
		if($r['type']=="8")
		{
			$process_no=$r['process_no'];
			$payment_mode="Bill Edit";
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
	$final_bal=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$supp' ORDER BY `slno` DESC LIMIT 0,1"));
	?>
		<tr>
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
} // 45

if($type==999)
{
	$id=$_POST['id'];
} // 999
?>
