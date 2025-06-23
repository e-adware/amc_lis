<?php
include("../../includes/connection.php");
$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST["type"];

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

if($type=="load_supp_bill")
{
	$supp=$_POST["supp"];
	?>
	<datalist id="browsrs">
		<?php
			$qq = mysqli_query($link," SELECT DISTINCT bill_no FROM `ph_purchase_receipt_master` WHERE `supp_code`='$supp'");
			while($cc=mysqli_fetch_array($qq))
			{
				echo "<option value='$cc[bill_no]'>";
			}
		?>
		</datalist>
	<?php
}

if($type=="srch_bill")
{
	$supp=$_POST["supp"];
	$bill=$_POST["bill"];
	//echo "SELECT * FROM `ph_purchase_receipt_details` WHERE `bill_no`='$bill' AND `SuppCode`='$supp'";
	?>
	<input type="hidden" id="suppid" value="<?php echo $supp;?>" />
	<input type="hidden" id="billno" value="<?php echo $bill;?>" />
	<table class="table table-condensed table-bordered">
		<tr class="thh">
			<th>#</th><th>Item</th><th>Batch</th><th>Expiry</th><th>Cost Price</th><th>MRP</th><th>Sale Price</th><th>Discount %</th><th>GST(%)</th><th>Quantity</th><th>Free</th><th>Total</th><th>Total Amount</th><th><b class="icon-upload-alt icon-large"></b></th>
		</tr>
	<?php
	$j=1;
	$qry=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_details` WHERE `bill_no`='$bill' AND `SuppCode`='$supp'");
	while($r=mysqli_fetch_array($qry))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
		$tot=$r['recpt_quantity']+$r['free_qnt'];
		$discount_per=explode(".",$r['dis_per']);
		$discount_per=$discount_per[0];
		$stk_qry=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `item_code`='$r[item_code]' AND `batch_no`='$r[recept_batch]' AND `date`='$r[recpt_date]'"));
		$slno=$stk_qry['slno'];
		$stk_next=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `item_code`='$r[item_code]' AND `batch_no`='$r[recept_batch]' AND `slno`>'$slno'"));
		//echo "SELECT * FROM `ph_stock_process` WHERE `item_code`='$r[item_code]' AND `batch_no`='$r[recept_batch]' AND `slno`>'$slno'";
		if($stk_next['slno'])
		{
			$dis="disabled='disabled'";
			$stock=0;
		}
		else
		{
			$dis="";
			$stock=1;
		}
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><input type="hidden" id="itm<?php echo $j;?>" value="<?php echo $r['item_code'];?>" /><?php echo $itm['item_name'];?></td>
			<td><input type="hidden" id="bch<?php echo $j;?>" value="<?php echo $r['recept_batch'];?>" /><?php echo $r['recept_batch'];?></td>
			<td><input type="text" id="exp<?php echo $j;?>" maxlength="10" style="width:80px;" value="<?php echo $r['expiry_date'];?>" title="YYYY-MM-DD" placeholder="yyyy-mm-dd" /></td>
			<td><input type="text" class="span1" id="cost<?php echo $j;?>" value="<?php echo $r['recept_cost_price'];?>" /></td>
			<td><input type="text" class="span1" id="mrp<?php echo $j;?>" onkeyup="calc_gst(this.value,<?php echo $j;?>,event)" value="<?php echo $r['recpt_mrp'];?>" /></td>
			<td><input type="text" class="span1" id="sale<?php echo $j;?>" value="<?php echo $r['sale_price'];?>" readonly="readonly" /></td>
			<td><input type="text" class="span1" id="disc<?php echo $j;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');" maxlength="2" value="<?php echo $discount_per;?>" /></td>
			<td><input type="text" class="span1" id="gst<?php echo $j;?>" value="<?php echo $r['gst_per'];?>" readonly="readonly" /></td>
			<td><input type="text" class="span1" id="qnt<?php echo $j;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');calc_tot_qnt(this.value,'qnt',<?php echo $j;?>,event)" value="<?php echo $r['recpt_quantity'];?>" <?php echo $dis;?> /></td>
			<td><input type="text" class="span1" id="free<?php echo $j;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');calc_tot_qnt(this.value,'free',<?php echo $j;?>,event)" value="<?php echo $r['free_qnt'];?>" <?php echo $dis;?> /></td>
			<td><input type="text" class="span1" id="tot<?php echo $j;?>" value="<?php echo $tot;?>" readonly="readonly" /></td>
			<td><?php echo $r['item_amount'];?></td>
			<td><button type="button" class="btn btn-success btn-mini" id="u_btn<?php echo $j;?>" disabled onclick="upd_purchase_all('<?php echo $j;?>','<?php echo $stock;?>')">Update</button></td>
		</tr>
		<?php
		$j++;
	}
	?>
	</table>
	<style>
	.thh th
	{
		text-align:center;
	}
	</style>
	<?php
}

if($type=="upd_purchase_all")
{
	$supp=$_POST["supp"];
	$bill=$_POST["bill"];
	$itm=$_POST["itm"];
	$bch=$_POST["bch"];
	$expiry=$_POST["expiry"];
	$dt=explode("-",$expiry);
	$yr=$dt[0];
	$mn=$dt[1];
	$dat=$yr."-".$mn."-01";
	$expiry = new DateTime( $dat );
	$expiry=$expiry->format( 'Y-m-t' );
	$cost=$_POST["cost"];
	$mrp=$_POST["mrp"];
	$sale=$_POST["sale"];
	$disc=$_POST["disc"];
	$gst=$_POST["gst"];
	$qnt=$_POST["qnt"];
	$free=$_POST["free"];
	$stk=$_POST["stk"];
	
	$tot_qnt=$qnt+$free;
		
	$tot_amt=val_con($qnt*$mrp);
	$gstamt=$tot_amt-($tot_amt*(100/(100+$gst)));
	$gstamt=number_format($gstamt,2);
	$itm_dis=round($tot_amt*$disc/100);
	
	if($stk>0)
	{
		$prs=mysqli_fetch_array(mysqli_query($link,"SELECT `order_no` FROM `ph_purchase_receipt_master` WHERE `bill_no`='$bill' AND `supp_code`='$supp'"));	
		mysqli_query($link,"UPDATE `ph_purchase_receipt_details` SET `expiry_date`='$expiry', `recpt_quantity`='$qnt', `free_qnt`='$free', `recpt_mrp`='$mrp', `recept_cost_price`='$cost', `sale_price`='$sale', `item_amount`='$tot_amt', `dis_per`='$disc', `dis_amt`='$itm_dis', `gst_amount`='$gstamt' WHERE `bill_no`='$bill' AND `SuppCode`='$supp' AND `item_code`='$itm' AND `recept_batch`='$bch'");
		$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `process_no`='$prs[order_no]' AND `item_code`='$itm' AND `batch_no`='$bch'"));
		$avabl=$v['s_available'];
		$added=$v['added'];
		$sell=$v['sell'];
		$cus_ret=$v['return_cstmr'];
		$sup_ret=$v['return_supplier'];
		$rem=($avabl+$tot_qnt-$sell+$cus_ret-$sup_ret);
		
		mysqli_query($link,"UPDATE `ph_stock_process` SET `added`='$tot_qnt', `s_remain`='$rem' WHERE `process_no`='$prs[order_no]' AND `item_code`='$itm' AND `batch_no`='$bch'");
		mysqli_query($link,"UPDATE `ph_stock_master` SET `quantity`='$tot_qnt',`exp_date`='$expiry' WHERE `item_code`='$itm' AND `batch_no`='$bch'");
		$n_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(item_amount),0) as net_amt from ph_purchase_receipt_details where bill_no='$bill' AND `SuppCode`='$supp'"));
		$g_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(gst_amount),0) as gst_amt from ph_purchase_receipt_details where bill_no='$bill' AND `SuppCode`='$supp'"));
		$d_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(dis_amt),0) as dis_amt from ph_purchase_receipt_details where bill_no='$bill' AND `SuppCode`='$supp'"));
		$net_amt=$n_amt['net_amt'];
		$gst_amt=$g_amt['gst_amt'];
		$dis_amt=$d_amt['dis_amt'];
		mysqli_query($link,"UPDATE `ph_purchase_receipt_master` SET `gst_amt`='$gst_amt', `dis_amt`='$dis_amt', `net_amt`='$net_amt' WHERE `bill_no`='$bill' AND `supp_code`='$supp'");
	}
	else
	{
		mysqli_query($link,"UPDATE `ph_purchase_receipt_details` SET `expiry_date`='$expiry', `recpt_mrp`='$mrp', `recept_cost_price`='$cost', `sale_price`='$sale', `item_amount`='$tot_amt', `dis_per`='$disc', `dis_amt`='$itm_dis', `gst_amount`='$gstamt' WHERE `bill_no`='$bill' AND `SuppCode`='$supp' AND `item_code`='$itm' AND `recept_batch`='$bch'");
		$n_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(item_amount),0) as net_amt from ph_purchase_receipt_details where bill_no='$bill' AND `SuppCode`='$supp'"));
		$g_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(gst_amount),0) as gst_amt from ph_purchase_receipt_details where bill_no='$bill' AND `SuppCode`='$supp'"));
		$d_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(dis_amt),0) as dis_amt from ph_purchase_receipt_details where bill_no='$bill' AND `SuppCode`='$supp'"));
		$net_amt=$n_amt['net_amt'];
		$gst_amt=$g_amt['gst_amt'];
		$dis_amt=$d_amt['dis_amt'];
		mysqli_query($link,"UPDATE `ph_purchase_receipt_master` SET `gst_amt`='$gst_amt', `dis_amt`='$dis_amt', `net_amt`='$net_amt' WHERE `bill_no`='$bill' AND `supp_code`='$supp'");
	}
	echo "Updated";
	
	// 230 	Pharmacy 	Purchase Received Bill Update 	18
}

if($type=="oo")
{
	
}
?>
