<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");
$dateTime=mktime();

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

function nextId($prefix,$table,$idno,$start="100") 
{
	$idRes=mysqli_query($GLOBALS["___mysqli_ston"], "Select max( cast( right(".$idno.",length(".$idno.")-".strlen($prefix).") AS signed ) ) from ".$table);
				//SELECT max( cast( right( r_id, length( r_id ) -2 ) AS signed ) ) 

//echo "Select max( cast( right(".$idno.",length(".$idno.")-".strlen($prefix).") AS signed ) ) from ".$table;
	$idArr=mysqli_fetch_array($idRes);
		if ($idArr[0]==null) {
			$NewId=$start;
		}
		else {
			$NewId=$idArr[0]+1;
		}
	$NewId=$prefix.$NewId;
	return $NewId;
	//return "Select max( cast( right(".$idno.",length(".$idno.")-".strlen($prefix).") AS signed ) ) from ".$table."<br>SELECT max( cast( right( r_id, length( r_id ) -2 ) AS signed ) )";
}

$type=$_POST['type'];

if($type==1) // for load sale price gst calculation
{
	$rate=$_POST['rate'];
	$pktqnt=$_POST['pktqnt'];
	$gst=$_POST['gst'];
	$vunitprice=$rate/$pktqnt;
	$vslprice1=$vunitprice-($vunitprice*(100/(100+$gst)));///Remove gst Calculattion
	$vslprice2=round($vslprice1,2);
	$vslprice3=$vunitprice-$vslprice2;
	$vslprice=$vslprice3;
	
	$val=$vslprice.'@'.$vslprice;
	echo $val;
} // 1

if($type==2)
{
	$spplrid=$_POST['supp'];
	$splrblno=$_POST['bill_no'];
	$bildate=$_POST['billdate'];
	$billamt=$_POST['bill_amt'];
	$total=$_POST['total'];
	$disamt=$_POST['discount'];
	$gstamt=$_POST['all_gst'];
	$ttlamt=$_POST['net_amt'];
	$user=$userid=$_POST['user'];
	$all=$_POST['all'];
	$btn_val=$_POST['btn_val'];
	$entrydate=$date;
	$rad=0;
	$adj=0;
	$branch_id=1;
	$substore_id=0;
	
	$arr=array();
	
	if($btn_val=="Done")
	{
		$count_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`receipt_no`) as tot FROM `inv_main_stock_received_master`"));
		$bill_tot_num=$count_qry["tot"];
		$bill_no=$bill_tot_num+1;
		
		$orderno=$rcv_no="RCV".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
		
		if(mysqli_query($link,"INSERT INTO `inv_main_stock_received_master`(`branch_id`, `order_no`, `receipt_no`, `bill_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `date`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$branch_id','$orderno','$orderno','$bildate','$billamt','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','$userid','$entrydate','$time','$rad','$adj')"))
		{
			$txt.="INSERT INTO `inv_main_stock_received_master`(`branch_id`, `order_no`, `receipt_no`, `bill_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `date`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$branch_id','$orderno','$orderno','$bildate','$billamt','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','$userid','$entrydate','$time','$rad','$adj')";
			
			$balance_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `branch_id`='$branch_id' AND `supp_code`='$spplrid' ORDER BY `slno` DESC LIMIT 0,1"));
			$balance=$balance_det['balance'];
			if(!$balance)
			{
				$balance=0;
			}
			$final_balance=($balance_det['balance_amt']+$ttlamt);
			
			mysqli_query($link,"INSERT INTO `inv_supplier_transaction`(`branch_id`, `process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `adjust_type`, `balance`, `balance_amt`, `type`, `date`, `time`, `datetime`, `user`) VALUES ('$branch_id','$orderno','$spplrid','$ttlamt','0','0','$adj','$balance','$final_balance','1','$date','$time','$dateTime','$user')");
			$txt.="\nINSERT INTO `inv_supplier_transaction`(`branch_id`, `process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `adjust_type`, `balance`, `balance_amt`, `type`, `date`, `time`, `datetime`, `user`) VALUES ('$branch_id','$orderno','$spplrid','$ttlamt','0','0','$adj','$balance','$final_balance','1','$date','$time','$dateTime','$user')";
			
			$al=explode("#%#",$all);
			foreach($al as $a)
			{
				$v=explode("@@",$a);
				$itm=$v[0];
				$bch=mysqli_real_escape_string($link,$v[1]);
				$exp_dt=$v[2];
				$exp_dt.="-01";
				$exp_dt=date('Y-m-t', strtotime($exp_dt));
				$qnt=$v[3];
				$free=$v[4];
				$gst=$v[5];
				$gst_amt=$v[6];
				$pkd_qnt=$v[7];
				$strip_mrp=$v[8];
				$mrp=$strip_mrp/$pkd_qnt;
				$unit_sale=$v[9];
				$strip_cost=$v[10];
				$unit_cost=$v[11];
				$dis_per=$v[12];
				$dis_amt=$v[13];
				$itm_amt=$v[14];
				$hsn=mysqli_real_escape_string($link,$v[15]);
				$rack_no=mysqli_real_escape_string($link,$v[16]);
				$qnt=$qnt*$pkd_qnt;
				$free=$free*$pkd_qnt;
				if($itm && $bch)
				{
					mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`order_no`, `receipt_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `batch_no`, `strip_quantity`, `supp_code`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$orderno','$splrblno','$itm','0000-00-00','$exp_dt','$date','$qnt','$free','$bch','$pkd_qnt','$spplrid','$mrp','$unit_cost','$strip_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
					$txt.="\nINSERT INTO `inv_main_stock_received_detail`(`order_no`, `receipt_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `batch_no`, `strip_quantity`, `supp_code`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$orderno','$splrblno','$itm','0000-00-00','$exp_dt','$date','$qnt','$free','$bch','$pkd_qnt','$spplrid','$mrp','$unit_cost','$strip_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')";
					
					$vqnt=$qnt+$free;
					$vst=0;
					mysqli_query($link,"UPDATE `item_master` SET `hsn_code`='$hsn',`rack_no`='$rack_no',`gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
					$txt.="\nUPDATE `item_master` SET `hsn_code`='$hsn',`rack_no`='$rack_no',`gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'";
					
					$process_no=$orderno;
					$process_type=1;
					include("inv_stock_add.php");
				}
			}
			$arr['response']=1;
			$arr['msg']="Saved";
		}
		else
		{
			$arr['response']=0;
			$arr['msg']="Error, please try again";
		}
	}
	else
	{
		$arr['response']=0;
		$arr['msg']="Error";
	}
	echo json_encode($arr);
} // 2

if($type==3)
{
	
} // 3

if($type==4)
{
	$val=$_POST['val'];
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th width="5%">#</th><th>Item Name</th>
		</tr>
	<?php
		
		if(strlen($val)>0)
		{
			$q="SELECT `item_id`,`item_name`,`hsn_code`,`rack_no`,`gst`,`strip_quantity` FROM `item_master` WHERE `item_name` like '$val%' ORDER BY `item_name` LIMIT 0,20";
		}
		else
		{
			$q="SELECT `item_id`,`item_name`,`hsn_code`,`rack_no`,`gst`,`strip_quantity` FROM `item_master` ORDER BY `item_name` LIMIT 0,20";
		}
		//echo $q;
		$d=mysqli_query($link, $q);
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			$gst=explode(".",$d1['gst']);
			$gst=$gst[0];
			?>
			<tr onclick="doc_load('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['gst'];?>','<?php echo $d1['strip_quantity'];?>','<?php echo $d1['hsn_code'];?>','<?php echo $d1['rack_no'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
				<td><?php echo $d1['item_id'];?></td>
				<td><?php echo $d1['item_name'];?>
					<div <?php echo "id=dvdoc".$i;?> style="display:none;">
					<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$gst."#".$d1['strip_quantity']."#".$d1['hsn_code']."#".$d1['rack_no']."#";?>
					</div>
				</td>
			</tr>
		<?php
			$i++;
		}
		?>
		</table>
	<?php
} // 4

if($type==5)
{
	$supp=$_POST['supp'];
	$bill_no=$_POST['bill_no'];
	$branch_id=1;
	
	$v=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `inv_main_stock_received_master` WHERE `branch_id`='$branch_id' AND `supp_code`='$supp' AND `bill_no`='$bill_no'"));
	if($v)
	{
		echo 1;
	}
	else
	{
		echo 0;
	}
} // 5

if($type==6)
{
	$ord=base64_decode($_POST['ord']);
	?>
	<input type="hidden" id="edit_ord" value="<?php echo base64_encode($ord);?>" />
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th style="text-align:right">MRP</th>
			<th style="text-align:right">Rate</th>
			<th style="text-align:right">GST %</th>
			<th style="text-align:right">Qnty</th>
			<th style="text-align:right">Free Qnt</th>
			<th style="text-align:right">Dis %.</th>
			<th>Amount</th>
		</tr>
		<?php
		$i=0;
		$j=1;
		$q=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `order_no`='$ord'");
		while($r=mysqli_fetch_assoc($q))
		{
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
			$gst_per=explode(".",$r['gst_per']);
			$gst_per=$gst_per[0];
			//~ $dis_per=explode(".",$r['dis_per']);
			//~ $dis_per=$dis_per[0];
			$dis_per=$r['dis_per'];
		?>
		<tr class="all_tr" id="tr<?php echo $j;?>">
			<td><?php echo $j;?></td>
			<td>
				<?php echo $itm['item_name'];?>
				<input type="hidden" class="input" id="itm" value="<?php echo $r['item_id'];?>" readonly />
			</td>
			<td>
				<span><?php echo $r['batch_no'];?></span>
			</td>
			<td>
				<?php echo date("Y-m", strtotime($r['expiry_date']));?>
				<input type="hidden" class="input" id="exp_date" value="<?php echo date("Y-m", strtotime($r['expiry_date']));?>" maxlength="7" />
			</td>
			<td style="text-align:right;"><input type="text" class="input" id="mrp" onkeyup="chk_dec(this,event)" value="<?php echo $r['recpt_mrp'];?>" /></td>
			<td style="text-align:right;"><input type="text" class="input" id="cost" onkeyup="chk_dec(this,event);change_val('<?php echo $j;?>')" value="<?php echo $r['recept_cost_price'];?>" /></td>
			<td style="text-align:right;">
				<input type="text" class="input" id="gst" onkeyup="chk_num(this,event);change_val('<?php echo $j;?>')" value="<?php echo $gst_per;?>" maxlength="2" />
				<input type="hidden" class="input" id="gst_amt" value="<?php echo $r['gst_amount'];?>" readonly />
			</td>
			<td style="text-align:right;">
				<?php echo $r['recpt_quantity'];?>
				<input type="hidden" class="input" id="qnt" onkeyup="chk_num(this,event);change_val('<?php echo $j;?>')" value="<?php echo $r['recpt_quantity'];?>" readonly />
			</td>
			<td style="text-align:right;">
				<?php echo $r['free_qnt'];?>
				<input type="hidden" class="input" id="free" onkeyup="chk_num(this,event)" value="<?php echo $r['free_qnt'];?>" readonly />
			</td>
			<td style="text-align:right;">
				<input type="text" class="input" id="disc" onkeyup="chk_dec(this,event);change_val('<?php echo $j;?>')" value="<?php echo $dis_per;?>" />
				<input type="hidden" class="input" id="dis_amt" value="<?php echo $r['dis_amt'];?>" />
			</td>
			<?php
			$i_amt=($r['item_amount']+$r['gst_amount'])-$r['dis_amt'];
			//$i_amt=($r['item_amount']+$r['gst_amount']);
			$i_amt_expl=explode(".",$i_amt);
			if($i_amt_expl[1]>0)
			{
				if(strlen($i_amt_expl[1])>1)
				{
					$item_amount=$i_amt_expl[0].".".$i_amt_expl[1];
				}
				else
				{
					$item_amount=$i_amt_expl[0].".".$i_amt_expl[1]."0";
				}
			}
			else
			{
				$item_amount=$i_amt_expl[0].".00";
			}
			?>
			<td style="text-align:right;">
				<span><?php echo $item_amount;?></span>
				<input type="hidden" class="input" id="item_amount" value="<?php echo ($r['item_amount']+$r['gst_amount']);?>" readonly />
			</td>
		</tr>
		<?php
		$i++;
		$j++;
		}
		?>
		<tr>
			<td colspan="11" style="text-align:center;">
				<button type="button" class="btn btn-primary" id="sav_btn" onclick="update_purchase('<?php echo $i;?>')">Save</button>
				<button type="button" class="btn btn-danger" id="clos_btn" data-dismiss="modal">Cancel</button>
			</td>
		</tr>
	</table>
	<style>
		.input
		{
			width:60px;
		}
	</style>
	<?php
} // 6

if($type==7)
{
	$ord=base64_decode($_POST['ord']);
	$all=$_POST['all'];
	$val=0;
	
	$all_amt=0;
	$all_gst=0;
	$all_dis=0;
	$al=explode("#penguin#",$all);
	foreach($al as $a)
	{
		$v=explode("@@@",$a);
		$itm=$v[0];
		$bch=$v[1];
		$qnt=$v[2];
		$mrp=$v[3];
		$rate=$v[4];
		$gst_per=$v[5];
		$gst_amt=$v[6];
		$dis_per=$v[7];
		$dis_amt=$v[8];
		$itm_amt=$v[9];
		
		$amt=$rate*$qnt;
		$sale_price=($mrp*(100/(100+$gst_per)));///Remove gst Calculattion
		if($itm)
		{
			mysqli_query($link,"UPDATE `inv_main_stock_received_detail` SET `recpt_mrp`='$mrp',`recept_cost_price`='$rate',`sale_price`='$sale_price',`item_amount`='$amt',`dis_per`='$dis_per',`dis_amt`='$dis_amt',`gst_per`='$gst_per',`gst_amount`='$gst_amt' WHERE `order_no`='$ord' AND `item_id`='$itm' AND `batch_no`='$bch'");
			$all_amt+=$amt;
			$all_gst+=$gst_amt;
			$all_dis+=$dis_amt;
			$val++;
		}
	}
	if($val)
	{
		$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_main_stock_received_master` WHERE `receipt_no`='$ord'"));
		$net_amt=($all_gst+$all_amt);
		mysqli_query($link,"UPDATE `inv_main_stock_received_master` SET `gst_amt`='$all_gst', `dis_amt`='$all_dis', `net_amt`='$net_amt' WHERE `receipt_no`='$ord'");
		echo "Done";
	}
	else
	{
		echo "DONE";
	}
} // 7

if($type==8)
{
	$id=$_POST['id'];
	$options="";
	//$q=mysqli_query($link, "SELECT DISTINCT `batch_no` FROM `ph_stock_master` WHERE `item_code`='$id' AND `exp_date`>='$date' ORDER BY `exp_date` LIMIT 0,10");
	$q=mysqli_query($link, "SELECT DISTINCT `batch_no` FROM `inv_main_stock_received_detail` WHERE `item_id`='$id' AND `supp_code`!='0' AND `expiry_date`>='$date' ORDER BY `slno` DESC LIMIT 0,10");
	while($r=mysqli_fetch_assoc($q))
	{
		$options.="<option value='".$r['batch_no']."' />";
	}
	echo $options;
} // 8

if($type==9)
{
	$itm=$_POST['itm'];
	$options="";
	$q=mysqli_query($link, "SELECT `bill_no`, `expiry_date`, `recpt_quantity`, `free_qnt`, `batch_no`, `strip_quantity`, `supp_code`, `recpt_mrp`, `cost_price`, `dis_per` FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `supp_code`!='0' ORDER BY `slno` DESC LIMIT 0,10");
	if(mysqli_num_rows($q)>0)
	{
	?>
	<table class="table table-condensed table-hover" style="margin:0;color:black;">
	<tr>
		<th>#</th>
		<th>Supplier</th>
		<th>Bill No</th>
		<th>Batch No</th>
		<th>Expiry Date</th>
		<th>Qnt</th>
		<th>Free</th>
		<th>MRP</th>
		<th>Cost</th>
		<th>Discount %
		<span style="float:right;"><button type="button" class="btn btn-mini btn-danger" onclick="rem_old_list(1)"><i class="icon-remove"></i></button></span>
		</th>
	</tr>
	<?php
	$j=1;
	while($r=mysqli_fetch_assoc($q))
	{
	//$pkd=mysqli_fetch_assoc(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
	$sup=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[supp_code]'"));
	?>
	<tr id="bchtr<?php echo $r['batch_no'];?>">
		<td><?php echo $j;?></td>
		<td><?php echo $sup['name'];?></td>
		<td><?php echo $r['bill_no'];?></td>
		<td><?php echo $r['batch_no'];?><input type="hidden" value="<?php echo $r['batch_no'];?>" /></td>
		<td><?php echo date("Y-m", strtotime($r['expiry_date']));?><input type="hidden" value="<?php echo date("Y-m", strtotime($r['expiry_date']));?>" /></td>
		<td><?php echo number_format($r['recpt_quantity']/$r['strip_quantity'],0);?></td>
		<td><?php echo number_format($r['free_qnt']/$r['strip_quantity'],0);?></td>
		<td><?php echo number_format($r['recpt_mrp']*$r['strip_quantity'],2);?><input type="hidden" value="<?php echo $r['recpt_mrp']*$r['strip_quantity'];?>" /></td>
		<td><?php echo $r['cost_price'];?><input type="hidden" value="<?php echo $r['cost_price'];?>" /></td>
		<td><?php echo number_format($r['dis_per'],0)." %";?></td>
	</tr>
	<?php
	$j++;
	}
	?>
	</table>
	<?php
	}
} // 9

if($type==999)
{
	$id=$_POST['id'];
} // 999
?>
