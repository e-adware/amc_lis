<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$date=date("Y-m-d");
$time=date('H:i:s');

$type=$_POST['type'];

if($type==1)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$pin=$_POST['pin'];
	$stat=$_POST['stat'];
	$ward=$_POST['ward'];
	
	if($pin)
	{
		$qry="SELECT DISTINCT `pin` FROM `patient_medicine_detail` WHERE `pin` like '%$pin%' ORDER BY `slno` LIMIT 0,30";
	}
	else
	{
		
		$qry="SELECT DISTINCT `pin` FROM `patient_medicine_detail` WHERE `status`='$stat' AND `date` BETWEEN '$fdate' AND '$tdate' ORDER BY `slno` LIMIT 0,30";
	}
	
	if($ward>0)
	{
		$qry="SELECT DISTINCT a.`pin` FROM `patient_medicine_detail` a,ipd_pat_bed_details b WHERE a.`status`='$stat' AND a.`date` 
		BETWEEN '$fdate' AND '$tdate' and a.`patient_id`=b.`patient_id` and a.`pin`=b.`ipd_id` and b.`ward_id`='$ward' order by a.`slno` LIMIT 0,30 ";
	}
	//$qry.=" ORDER BY `slno` LIMIT 0,30";
	//echo $qry;
	$q=mysqli_query($link,$qry);
	?>
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>UNIT NO.</th>
			<th>Bill No</th>
			<th>Name</th>
			<th>Ward</th>
			<th>Age / Sex</th>
			<th>Indent No</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			
			
			$pid=mysqli_fetch_assoc(mysqli_query($link,"SELECT `patient_id` FROM `uhid_and_opdid` WHERE `opd_id`='$r[pin]'"));
			$pat_info=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name`,`dob`,`age`,`age_type`,`sex` FROM `patient_info` WHERE `patient_id`='$pid[patient_id]'"));
			
			$ward_info=mysqli_fetch_array(mysqli_query($link, " SELECT b.`name` FROM `ipd_pat_bed_details` a,ward_master b WHERE a.`patient_id`='$pid[patient_id]' and a.ipd_id='$r[pin]' and a.ward_id =b.ward_id  "));
			
			if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
		?>
		<tr class="nm">
			<td><?php echo $i;?></td>
			<td><?php echo $pid['patient_id'];?></td>
			<td><?php echo $r['pin'];?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $ward_info['name'];?></td>
			<td><?php echo $age." / ".$pat_info['sex'];?></td>
			<td>
			<div class="btn-group">
			<?php 
			$qq=mysqli_query($link,"SELECT DISTINCT `indent_num` FROM `patient_medicine_detail` WHERE `pin`='$r[pin]' AND `status`='$stat' AND `date` BETWEEN '$fdate' AND '$tdate'");
			while($rr=mysqli_fetch_assoc($qq))
			{
				?>
				<button type="button" class="btn btn-info btn-mini" onclick="load_med_det('<?php echo $pid['patient_id'];?>','<?php echo $r['pin'];?>','<?php echo $rr['indent_num'];?>')">Indent <?php echo $rr['indent_num'];?></button>
				<?php
			}
			?>
			</div>
			</td>
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}

if($type==2)
{
	$dname=$_POST['val'];
	$ph=$_POST['ph'];
	$pid=base64_decode($_POST['pid']);
	$opd=base64_decode($_POST['opd']);
	$ino=base64_decode($_POST['ino']);
?>
	<table class="table table-condensed table-report table-bordered">
		<tr>
			<th width="5%">#</th><th width="20%">Item Code</th><th>Item Name</th><th width="10%">Stock</th><th width="10%">Indent</th><th width="10%">Rack</th>
		</tr>
<?php
	if($dname)
	{
		$q="select distinct b.`item_code` from ph_item_master a, ph_stock_master b, `patient_medicine_detail` c where";
		$q.=" a.item_id=b.item_code and b.`item_code`=c.`item_code` and b.quantity>0 and b.substore_id='$ph' and c.`patient_id`='$pid' and c.`pin`='$opd' and c.`indent_num`='$ino' and c.`status`='0' and a.item_name!='' and (a.item_id like '$dname%' OR a.item_name like '$dname%')";
		$q.=" order by a.item_name limit 0,30";
	}
	else
	{
		$q="select distinct b.`item_code` from ph_item_master a, ph_stock_master b, `patient_medicine_detail` c where a.item_id=b.item_code and b.`item_code`=c.`item_code`  and b.substore_id='$ph' and c.`patient_id`='$pid' and c.`pin`='$opd' and c.`indent_num`='$ino' and c.`status`='0' and a.item_name!=''";
		$q.=" order by a.item_name limit 0,30";
	}
	//echo $q;
	$d=mysqli_query($link, $q);
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_id`,`item_name`,`rack_no` FROM `ph_item_master` WHERE `item_id`='$d1[item_code]'"));
		$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`quantity`),0) AS stock FROM `ph_stock_master` WHERE `item_code`='$d1[item_code]' AND `substore_id`='$ph'"));
		$ind=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity` FROM `patient_medicine_detail` WHERE `patient_id`='$pid' AND `pin`='$opd' AND `indent_num`='$ino' AND `item_code`='$d1[item_code]' AND `status`='0'"));
		?>
		<tr onclick="doc_load('<?php echo $itm['item_id'];?>','<?php echo $itm['item_name'];?>','<?php echo $ind['quantity'];?>')" style="cursor:pointer" <?php echo "id=itemm".$itm['item_id'];?> class="boldred">
			<td><?php echo $i;?></td>
			<td><?php echo $itm['item_id'];?></td>
			<td><?php echo $itm['item_name'];?>
				<div <?php echo "id=dvdoc".$i;?> style="display:none;">
				<?php echo "#".$itm['item_id']."#".$itm['item_name']."#".$ind['quantity'];?>
				</div>
			</td>
			<td><?php echo $stk['stock'];?></td>
			<td><?php echo $ind['quantity'];?></td>
			<td><?php echo $itm['rack_no'];?></td>
		</tr>
	<?php
		$i++;
	}
	?>
	</table>
<?php
}

if($type==3)
{
	$item_id=$_POST['item_id'];
	$ph=$_POST['ph'];
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Batch No</th><th>Stock</th><th>MRP</th><th>GST</th><th>Expiry</th>
		</tr>
<?php
	if($dname)
	{
		$d=mysqli_query($link, "SELECT * FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$item_id' AND `batch_no` like '$dname%' AND `quantity`>0 ORDER BY `exp_date`");
	}
	else
	{
		$d=mysqli_query($link, "SELECT * FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$item_id' AND `quantity`>0 ORDER BY `exp_date`");
	}
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		$mrp=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,`gst_per` FROM `ph_purchase_receipt_details` WHERE `item_code`='$item_id' AND `batch_no`='$d1[batch_no]'"));
		?>
		<tr onClick="hguide_load('<?php echo $d1['batch_no'];?>','<?php echo $d1['quantity'];?>','<?php echo $mrp['recpt_mrp'];?>','<?php echo $mrp['gst_per'];?>','<?php echo $d1['exp_date'];?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
			<td><?php echo $d1['batch_no'];?></td>
			<td>
				<?php echo $d1['quantity'];?>
				<div <?php echo "id=dvhguide".$i;?> style="display:none;">
					<?php echo "#".$d1['batch_no']."#".$d1['quantity']."#".$mrp['recpt_mrp']."#".$mrp['gst_per']."#".$d1['exp_date'];?>
				</div>
			</td>
			<td><?php echo number_format($mrp['recpt_mrp'],2);?></td>
			<td><?php echo $mrp['gst_per'];?></td>
			<td><?php echo convert_date($d1['exp_date']);?></td>
		</tr>
	<?php
		$i++;
	}
	?>
	</table>
<?php
}

if($type==4)
{
	$branch_id=1;
	$ph=$_POST['ph'];
	$pid=base64_decode($_POST['pid']);
	$opd=base64_decode($_POST['opd']);
	$ino=base64_decode($_POST['ino']);
	$total=$_POST['total'];
	$discount=$_POST['discount'];
	$disc_amt=$_POST['disc_amt'];
	$paid=$_POST['paid'];
	$balance=$_POST['balance'];
	$payMode=$_POST['payMode'];
	$all=$_POST['all'];
	$user=$_POST['user'];
	//$entry_no=mktime().$user;
	$entry_no="";
	$bill_id=date("YmdHis").$user;
	
	$checkQnt=0;
	$al=explode("#@#",$all);
	foreach($al as $a)
	{
		$v		=explode("@@",$a);
		$itm	=$v[0];
		$bch	=$v[1];
		$qnt	=$v[2];
		$mrp	=$v[3];
		$amt	=$v[4];
		$gst_per=$v[5];
		$gst_amt=$v[6];
		$exp_dt	=$v[7];
		
		if($itm)
		{
			$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `quantity` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch'"));
			if($stk['quantity']<$qnt)
			{
				$checkQnt++;
			}
		}
	}
	
	$arr=array();
	if($checkQnt)
	{
		$arr['response']=0;
		$arr['msg']="Some items have low stock";
	}
	else
	{
		$txt		="";
		$p_info		=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name`,`phone`,`address` FROM `patient_info` WHERE `patient_id`='$pid'"));
		$p_addr		=mysqli_fetch_assoc(mysqli_query($link,"SELECT `city` FROM `patient_info_rel` WHERE `patient_id`='$pid'"));
		$cname		=$p_info['name'];
		$phone		=$p_info['phone'];
		$address	=$p_info['address'];
		if($address)
		{
			$address.=", ".$p_addr['city'];
		}
		else
		{
			$address=$p_addr['city'];
		}
		//$txt.="\n";
		if(mysqli_query($link,"INSERT INTO `ph_sell_master`(`branch_id`, `bill_id`, `bill_no`, `substore_id`, `entry_date`, `customer_name`, `customer_phone`, `address`, `co`, `total_amt`, `discount_perchant`, `discount_amt`, `adjust_amt`, `paid_amt`, `balance`, `bill_type_id`, `patient_id`, `opd_id`, `ipd_id`, `patient_type`, `refbydoctorid`, `pat_type`, `user`, `time`, `round_type`, `round`, `gst_amount`, `return_amt`) VALUES ('$branch_id','$bill_id','$bill_id','$ph','$date','$cname','$phone','$address','','$total','$discount','$disc_amt','0','$paid','$balance','1','$pid','$opd','$opd','1','0','0','$user','$time','0','0','0','0')"))
		{
			mysqli_query($link,"INSERT INTO `ph_bill_generation`(`bill_id`, `date`, `time`, `user`) VALUES ('$bill_id','$date','$time','$user')");
			$txt.="\nINSERT INTO `ph_bill_generation`(`bill_id`, `date`, `time`, `user`) VALUES ('$bill_id','$date','$time','$user')";
			
			$last_bill_no=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `ph_bill_generation` WHERE `bill_id`='$bill_id' AND `date`='$date' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1"));
			
			$bill_no="SNM/".str_pad($last_bill_no['slno'], 4, 0, STR_PAD_LEFT);
			$bill_no=trim($bill_no); // display bill no
			$last_slno=$last_bill_no['slno'];
			
			$all_amount	=0;
			$amount		=0;
			$gst_amount	=0;
			$al=explode("#@#",$all);
			foreach($al as $a)
			{
				$v		=explode("@@",$a);
				$itm	=$v[0];
				$bch	=$v[1];
				$qnt	=$v[2];
				$mrp	=$v[3];
				$amt	=$v[4];
				$gst_per=$v[5];
				$gst_amt=$v[6];
				$exp_dt	=$v[7];
				
				if($itm)
				{
					$rate=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recept_cost_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$itm' AND `batch_no`='$bch' ORDER BY `slno` DESC LIMIT 0,1"));
					$cost=$rate['recept_cost_price'];
					if(!$cost){$cost=0;}
					$amt	=($cost*$qnt);
					$amount+=$amt;
					$gst_amt=(($amt*$gst_per)/100);
					$gst_amount+=$gst_amt;
					
					//mysqli_query($link,"INSERT INTO `ph_ipd_sell_details`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_id`, `batch_no`, `expiry_date`, `qnt`, `mrp`, `gst_per`, `gst_amt`, `item_cost_price`, `sale_price`, `time`, `user`) VALUES ('$entry_no','$opd','$ph','$date','$itm','$bch','$exp_dt','$qnt','$mrp','$gst_per','$gst_amt','$cost','$mrp','$time','$user')");
					
					mysqli_query($link,"UPDATE `patient_medicine_detail` SET `status`='1',`bill_no`='$bill_no' WHERE `patient_id`='$pid' AND `pin`='$opd' AND `indent_num`='$ino' AND `item_code`='$itm'");
					
					$iname=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_id`='$itm'"));
					$item_name=$iname['item_name'];
					$itm_amt=($mrp*$qnt);
					$all_amount+=$itm_amt;
					//mysqli_query($link,"INSERT INTO `ipd_pat_service_details_sub`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`) VALUES ('$pid','$opd','$group_id','$itm','$item_name','$qnt','$mrp','$itm_amt','$ino','$user','$time','$date','0','0','0',NULL)");
					
					mysqli_query($link,"INSERT INTO `ph_sell_details`(`bill_id`, `bill_no`, `substore_id`, `indent_num`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`) VALUES ('$bill_id','$bill_no','$ph','$ino','$date','$itm','$bch','$exp_dt','$qnt','0','$mrp','$itm_amt','$itm_amt','$gst_per','$gst_amt','$cost','$mrp')");
					
					$substore_id=$ph;
					$process_no=$opd;
					$process_type=14;
					include("pharmacy_stock_deduct.php");
				}
			}
			//$net_amount		=($amount+$gst_amount);
			
			mysqli_query($link,"UPDATE `ph_sell_master` SET `bill_no`='$bill_no', `gst_amount`='$gst_amount' WHERE `bill_id`='$bill_id' AND `patient_id`='$pid' AND `ipd_id`='$opd'");
			
			if($paid)
			{
				mysqli_query($link,"INSERT INTO `ph_payment_details`(`branch_id`, `bill_no`, `substore_id`, `entry_date`, `amount`, `payment_mode`, `check_no`, `type_of_payment`, `user`, `time`) VALUES ('$branch_id','$bill_no','$ph','$date','$paid','$payMode','','A','$user','$time')");
			}
			
			$arr['response']=1;
			$arr['msg']="Done";
			$arr['bill_no']=$bill_no;
		}
		else
		{
			$arr['response']=0;
			$arr['msg']="Error";
		}
		$arr['text']=$txt;
	}
	echo json_encode($arr);
}

if($type==5)
{
	$ord=base64_decode($_POST['ord']);
	$user=$_POST['user'];
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_damage_entry_master` WHERE `entry_no`='$ord'"));
	$emp=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$det[user]'"));
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th colspan="4">Date</th>
			<th colspan="4">User</th>
		</tr>
		<tr>
			<td colspan="4"><?php echo convert_date($det['date'])." ".convert_time($det['time']);?></td>
			<td colspan="4"><?php echo $emp['name'];?></td>
		</tr>
		<tr>
			<th>#</th>
			<th>Description</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th>Quantity</th>
			<th>MRP</th>
			<th>Rate</th>
			<th>Amount</th>
		</tr>
		<?php
		$j=1;
		$q=mysqli_query($link,"SELECT * FROM `ph_damage_entry_detail` WHERE `entry_no`='$ord'");
		while($r=mysqli_fetch_assoc($q))
		{
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_id`='$r[item_id]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo $r['expiry_date'];?></td>
			<td><?php echo $r['quantity'];?></td>
			<td><?php echo $r['recpt_mrp'];?></td>
			<td><?php echo $r['recept_cost_price'];?></td>
			<td><?php echo $r['item_amount'];?></td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<input type="hidden" id="order" value="<?php echo base64_encode($ord);?>" />
	<?php
}

if($type==6)
{
	$ord=base64_decode($_POST['ord']);
	$user=$_POST['user'];
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_damage_entry_master` WHERE `entry_no`='$ord'"));
	$substore_id=2;
	$process_type=13;
	$process_no=$ord;
	
	$arr=array();
	if($det['stat']==0)
	{
		mysqli_query($link,"UPDATE `ph_damage_entry_master` SET `stat`='1' WHERE `entry_no`='$ord'");
		
		$qq=mysqli_query($link,"SELECT * FROM `ph_damage_entry_detail` WHERE `entry_no`='$ord'");
		while($rr=mysqli_fetch_assoc($qq))
		{
			$itm		=$rr['item_id'];
			$bch		=$rr['batch_no'];
			$qnt		=$rr['quantity'];
			$exp_dt		=$rr['expiry_date'];
			/*-----------------daily entry process-----------------*/
			$stockQry=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `substore_id`='$substore_id' AND `item_code`='$itm' AND `batch_no`='$bch' AND `date`='$date'"));
			if($stockQry)
			{
				$opening	=$stockQry['s_remain'];
				$closing	=($stockQry['s_remain']-$qnt);
				$deduct		=($stockQry['return_supplier']+$qnt);
				
				if(!$opening){$opening=0;}
				if(!$closing){$closing=0;}
				
				mysqli_query($link,"UPDATE `ph_stock_process` SET `return_supplier`='$deduct', `s_remain`='$closing' WHERE `slno`='$stockQry[slno]'");
			}
			else
			{
				$stockQry=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `substore_id`='$substore_id' AND `item_code`='$itm' AND `batch_no`='$bch' ORDER BY `slno` DESC LIMIT 0,1"));
				
				$opening	=$stockQry['s_remain'];
				$closing	=($stockQry['s_remain']-$qnt);
				
				if(!$opening){$opening=0;}
				if(!$closing){$closing=0;}
				
				mysqli_query($link,"INSERT INTO `ph_stock_process`(`substore_id`, `process_no`, `item_code`, `batch_no`, `s_available`, `added`, `sell`, `return_cstmr`, `return_supplier`, `s_remain`, `date`) VALUES ('$substore_id','$process_no','$itm','$bch','$opening','0','0','0','$qnt','$closing','$date')");
			}

			/*-----------------per entry process-----------------*/
			mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$process_no','$itm','$bch','$opening','$qnt','$closing','$process_type','$date','$time','$user')");

			/*-----------------stock master-----------------*/
			$stockCheck=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_master` WHERE `substore_id`='$substore_id' AND `item_code`='$itm' AND `batch_no`='$bch'"));
			if($stockCheck)
			{
				mysqli_query($link,"UPDATE `ph_stock_master` SET `quantity`='$closing' WHERE `slno`='$stockCheck[slno]'");
			}
			else
			{
				mysqli_query($link,"INSERT INTO `ph_stock_master`(`substore_id`, `item_code`, `batch_no`, `quantity`, `mfc_date`, `exp_date`) VALUES ('$substore_id','$itm','$bch','$closing','','$exp_dt')");
			}
		}
		
		
		$arr['response']=1;
		$arr['msg']="Approved";
	}
	if($det['stat']==1)
	{
		$arr['response']=0;
		$arr['msg']="Already Approved";
	}
	if($det['stat']==2)
	{
		$arr['response']=0;
		$arr['msg']="Already Approved";
	}
	if($det['stat']==3)
	{
		$arr['response']=0;
		$arr['msg']="Order Cancelled";
	}
	if($det['stat']==4)
	{
		$arr['response']=0;
		$arr['msg']="Order Deleted";
	}
	echo json_encode($arr);
}

if($_POST['type']=="pat_det")
{
	$pin=mysqli_real_escape_string($link,$_POST['pin']);
	$val=array();
	$val['name']			="";
    $val['refbydoctorid']	="";
    $val['phone']			="";
    $val['address']			="";
    $val['balance']			=0;

    if($pin)
    {
		//$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$val'")); // search patient_id
		$pat=mysqli_fetch_array(mysqli_query($link,"SELECT a.`name`,a.`phone`,a.`address`,b.`refbydoctorid`,b.`center_no` FROM `patient_info` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND b.`opd_id`='$pin'")); // search patient_id
	   
	    //$qbalchk=mysqli_fetch_array(mysqli_query($link,"SELECT pharmacy_bal  FROM `patient_source_master` WHERE `centreno`='$pat[center_no]'")); 
	    if($pat)
	    {
		    $val['name']			=	$pat['name'];
		    $val['refbydoctorid']	=	$pat['refbydoctorid'];
		    $val['phone']			=	$pat['phone'];
		    $val['address']			=	$pat['address'];
		    $val['balance']			=	0;
		}
	}
	echo json_encode($val);
}

if($_POST['type']=="check_all_items")
{
	$all=$_POST['all'];
	$btnvalue=$_POST['btnvalue'];
	$ph=$_POST['ph'];
	$less="";
	$val=explode("#@#",$all);
	if($btnvalue=="Done")
	{
		foreach($val as $vl)
		{
			$v=explode("@@",$vl);
			$itm=$v[0];
			$bch=$v[1];
			$qnt=$v[2];
			if($itm && $bch)
			{
				$stk=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch'"));
				if($stk['quantity']<$qnt)
				{
					$less.=$itm."@@".$bch."@@#@#";
				}
			}
		}
	}
	echo $less;
}

if($_POST['type']=="save_items")
{
	function text_query($txt)
	{
		if($txt)
		{
			$myfile = file_put_contents('../../log/sales.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
		}
	}
	
	$all=$_POST['all'];
	$pin=$_POST['pin'];
	$cust_name=mysqli_real_escape_string($link,$_POST['cust_name']);
	$ref_by=$_POST['ref_by'];
	$contact=$_POST['contact'];
	$addr=mysqli_real_escape_string($link,$_POST['addr']);
	$co=mysqli_real_escape_string($link,$_POST['co']);
	$final_rate=$_POST['final_rate'];
	$total=$_POST['total'];
	$gst=$_POST['gst'];
	$discount=$_POST['discount'];
	$dis_amt=$_POST['dis_amt'];
	$adjust=$_POST['adjust'];
	$paid=$_POST['paid'];
	$balance=$_POST['balance'];
	$updt_bill=$_POST['updt_bill'];
	$bill_id=$_POST['bill_id'];
	$btn_val=$_POST['btn_val'];
	$bill_typ=$_POST['bill_typ'];
	$pat_type=$_POST['pat_type'];
	$ph=$_POST['ph'];
	$patient_id=$_POST['patient_id'];
	$ind_no=$_POST['ind_no']; // basket patient
	$user=$_POST['user'];
	$uhid="";
	if($pin)
	{
		$u_id=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id` FROM `uhid_and_opdid` WHERE `opd_id`='$pin'"));
		$uhid=$u_id['patient_id'];
	}
	//------------------------------------------------------------------
	$round=number_format(($total-$final_rate),2);
	if($round==0)
	{
		$round_type=0;
	}
	else if($round>0)
	{
		$round_type=1;
	}
	else if($round<0)
	{
		$round_type=2;
	}
	//------------------------------------------------------------------
	if($pat_type=="2") // esic
	{
		$discount=0;
		$dis_amt=0;
		$paid=0;
		$adjust=0;
		$balance=$total;
	}
	else if($pat_type=="6") // donor
	{
		$discount=100;
		$dis_amt=$total;
		$paid=0;
		$adjust=0;
		$balance=0;
	}
	//------------------------------------------------------------------
	if($bill_typ=="1")
	{
		$payment_mode="Cash";
	}
	if($bill_typ=="2")
	{
		$payment_mode="Credit";
	}
	if($bill_typ=="3")
	{
		$payment_mode="";
	}
	if($bill_typ=="4")
	{
		$payment_mode="Card";
	}
	if($btn_val=="Done")
	{
		$crr_yrs=date('Y');
		$crr_yr=date('y');
		$crr_mn=date('m');
		$crr_dy=date('d');
		$srch=$crr_yrs."-".$crr_mn;
		$start_bill=100;
		//$count_bill="SELECT COUNT(`slno`) AS cnt FROM `ph_sell_master` WHERE `entry_date` like '$srch-%'";
		//$bill=mysqli_fetch_array(mysqli_query($link,$count_bill));
		//echo $bill['cnt'];
		
		//$bill_id=($bill['cnt']+1);
		$bill_id=date("YmdHis").$user;
		$bill_id=trim($bill_id); // query bill no
		
		$bill_no=($start_bill+$bill['cnt']+1);
		$bill_no.="/".$crr_mn.$crr_yr;
		$bill_no=trim($bill_no); // display bill no
		
		//----------------------------------------
		$less="";
		$val=explode("#@#",$all);
		foreach($val as $vl)
		{
			$v=explode("@@",$vl);
			$itm=$v[0];
			$bch=$v[1];
			$qnt=$v[2];
			if($itm && $bch)
			{
				$stk=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch'"));
				if($stk['quantity']<$qnt)
				{
					$less.=$itm."@@".$bch."@@#@#";
				}
			}
		}
		if($less=="")
		{
			//----------------------------------------
			//~ $change_year=date("Y-04-01");
			//~ if($date>=$change_year)
			//~ {
				//~ $crr_month=date("Y-04-");
				//~ $bill_month=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) AS cnt FROM `ph_bill_generation` WHERE `date` like '$crr_month%'"));
				//~ if($bill_month['cnt']==0)
				//~ {
					//~ mysqli_query($link,"TRUNCATE TABLE `ph_bill_generation`");
				//~ }
			//~ }
			
			//~ $current_month=date("m");
			//~ if($current_month<4)
			//~ {
				//~ mysqli_query($link,"INSERT INTO `ph_bill_generation`(`bill_id`, `date`, `time`, `user`) VALUES ('$bill_id','$date','$time','$user')");
			//~ }
			//~ else
			//~ {
				//~ $current_year=date("Y-04-01");
				//~ $c=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_bill_generation` WHERE `date`>='$current_year'"));
				//~ if($c)
				//~ {
					//~ mysqli_query($link,"INSERT INTO `ph_bill_generation`(`bill_id`, `date`, `time`, `user`) VALUES ('$bill_id','$date','$time','$user')");
				//~ }
				//~ else
				//~ {
					//~ mysqli_query($link,"TRUNCATE TABLE `ph_bill_generation`");
					//~ mysqli_query($link,"INSERT INTO `ph_bill_generation`(`bill_id`, `date`, `time`, `user`) VALUES ('$bill_id','$date','$time','$user')");
				//~ }
			//~ }
			
			//~ //mysqli_query($link,"INSERT INTO `ph_bill_generation`(`bill_id`, `date`, `time`, `user`) VALUES ('$bill_id','$date','$time','$user')");
			//~ $last_bill_no=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `ph_bill_generation` WHERE `bill_id`='$bill_id' AND `date`='$date' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1"));
			//----------------------------------------
			$crr_yr=date('y');
			$crr_mn=date('m');
			$bill_id=date("YmdHis").$user;
			$bill_id=trim($bill_id); // query bill_id
			//----------------------------------------
			if(mysqli_query($link,"INSERT INTO `ph_sell_master`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `customer_name`, `customer_phone`, `address`, `co`, `total_amt`, `discount_perchant`, `discount_amt`, `adjust_amt`, `paid_amt`, `balance`, `bill_type_id`, `patient_id`, `opd_id`, `ipd_id`, `patient_type`, `refbydoctorid`, `pat_type`, `user`, `time`, `round_type`, `round`, `gst_amount`, `return_amt`) VALUES ('$bill_id','$bill_id','$ph','$date','$cust_name','$contact','$addr','$co','$total','$discount','$dis_amt','$adjust','$paid','$balance','$bill_typ','$uhid','$pin','$pin','$pat_type','$ref_by','0','$user','$time','$round_type','$round','$gst','0.00')"))
			{
				//----------------------------------------
				$crr_year=date("Y-");
				$bill_month=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) AS cnt FROM `ph_bill_generation` WHERE `date` like '$crr_year%'"));
				if($bill_month['cnt']==0)
				{
					mysqli_query($link,"TRUNCATE TABLE `ph_bill_generation`");
				}
				mysqli_query($link,"INSERT INTO `ph_bill_generation`(`bill_id`, `date`, `time`, `user`) VALUES ('$bill_id','$date','$time','$user')");
				
				$last_bill_no=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `ph_bill_generation` WHERE `bill_id`='$bill_id' AND `date`='$date' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1"));
				
				$start_bill=100;
				$bill_no=($start_bill+$last_bill_no['slno']);
				$bill_no.="/".$crr_mn.$crr_yr;
				$bill_no=trim($bill_no); // display bill no
				$last_slno=$last_bill_no['slno'];
				//mysqli_query($link,"DELETE FROM `ph_bill_generation` WHERE `slno`='$last_slno'"); // delete last slno
				mysqli_query($link,"UPDATE `ph_sell_master` SET `bill_no`='$bill_no' WHERE `bill_id`='$bill_id' AND `bill_no`='$bill_id' AND `user`='$user'");
				//----------------------------------------
				// text file
				$txt = "INSERT INTO `ph_sell_master`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `customer_name`, `customer_phone`, `address`, `co`, `total_amt`, `discount_perchant`, `discount_amt`, `adjust_amt`, `paid_amt`, `balance`, `bill_type_id`, `patient_id`, `opd_id`, `ipd_id`, `patient_type`, `refbydoctorid`, `pat_type`, `user`, `time`, `round_type`, `round`, `gst_amount`, `return_amt`) VALUES ('$bill_id','$bill_no','$ph','$date','$cust_name','$contact','$addr','$co','$total','$discount','$dis_amt','$adjust','$paid','$balance','$bill_typ','$uhid','$pin','$pin','$pat_type','$ref_by','0','$user','$time','$round_type','$round','$gst','0.00')";
				
				mysqli_query($link,"INSERT INTO `ph_sell_master_edit`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `customer_name`, `customer_phone`, `address`, `co`, `total_amt`, `discount_perchant`, `discount_amt`, `adjust_amt`, `paid_amt`, `balance`, `bill_type_id`, `patient_id`, `opd_id`, `ipd_id`, `patient_type`, `refbydoctorid`, `pat_type`, `user`, `time`, `round_type`, `round`, `gst_amount`, `return_amt`, `entry_no`) VALUES ('$bill_id','$bill_no','$ph','$date','$cust_name','$contact','$addr','$co','$total','$discount','$dis_amt','$adjust','$paid','$balance','$bill_typ','$uhid','$pin','$pin','$pat_type','$ref_by','0','$user','$time','$round_type','$round','$gst','0.00','0')");
				
				$txt.="\nINSERT INTO `ph_sell_master_edit`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `customer_name`, `customer_phone`, `total_amt`, `discount_perchant`, `discount_amt`, `adjust_amt`, `paid_amt`, `balance`, `bill_type_id`, `patient_id`, `opd_id`, `ipd_id`, `patient_type`, `refbydoctorid`, `pat_type`, `user`, `time`, `round_type`, `round`, `gst_amount`, `return_amt`, `entry_no`) VALUES ('$bill_id','$bill_no','$ph','$date','$cust_name','$contact','$total','$discount','$dis_amt','$adjust','$paid','$balance','$bill_typ','$uhid','$pin','$pin','$pat_type','$ref_by','0','$user','$time','$round_type','$round','$gst','0.00','0')";
				
				mysqli_query($link,"INSERT INTO `ph_payment_details`( `bill_no`, `substore_id`, `entry_date`, `amount`, `payment_mode`, `check_no`, `type_of_payment`, `user`, `time`) VALUES ('$bill_no','$ph','$date','$paid','$payment_mode','','A','$user','$time')");
				
				$txt.="\nINSERT INTO `ph_payment_details`( `bill_no`, `substore_id`, `entry_date`, `amount`, `payment_mode`, `check_no`, `type_of_payment`, `user`, `time`) VALUES ('$bill_no','$ph','$date','$paid','$payment_mode','','A','$user','$time')";
				
				$al=explode("#@#",$all);
				foreach($al as $a)
				{
					$v=explode("@@",$a); // all+=itm+"@@"+bch+"@@"+qnt+"@@"+mrp+"@@"+amt+"@@"+gst_per+"@@"+gst_amt+"@@"+expdt+"@@#@#";
					$itm=$v[0];
					$bch=$v[1];
					$qnt=$v[2];
					$mrp=$v[3];
					$amt=$v[4];
					$gst_per=$v[5];
					$gst_amt=$v[6];
					$expdt=$v[7];
					$net_amt=$amt+$gst_amt;
					if($itm && $qnt)
					{
						//$price=mysqli_fetch_array(mysqli_query($link,"SELECT `recept_cost_price`,`sale_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$itm' AND `expiry_date`='$expdt' AND `recept_batch`='$bch'"));
						$price=mysqli_fetch_array(mysqli_query($link,"SELECT `recept_cost_price`,`sale_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$itm' AND `recept_batch`='$bch'"));
						
						$vttlamt=($qnt*$mrp);
						//$vttlcstprc=val_con($qnt*$recpt);
						$vslprice1=$mrp-($mrp*(100/(100+$gst_per)));
						$vslprice=($mrp-$vslprice1);
						$gst_amt=$vttlamt-($vttlamt*(100/(100+$gst_per)));
						$net_amt=$vttlamt-$gst_amt;
						
						mysqli_query($link,"INSERT INTO `ph_sell_details`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`) VALUES ('$bill_id','$bill_no','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$vslprice')");
						
						$txt.="\nINSERT INTO `ph_sell_details`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`) VALUES ('$bill_id','$bill_no','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$vslprice')";
						
						mysqli_query($link,"INSERT INTO `ph_sell_details_edit`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`, `entry_no`) VALUES ('$bill_id','$bill_no','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$price[sale_price]','0')");
						
						$txt.="\nINSERT INTO `ph_sell_details_edit`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`, `entry_no`) VALUES ('$bill_id','$bill_no','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$vslprice','0')";
						
						//-------------stock----------------
						$slqnt=$qnt;
						$freqnt=0;
						$vqnt=$slqnt+$freqnt;
						$vbtch=$bch;
						
						$vstkqnt=0;
						$num=mysqli_num_rows(mysqli_query($link,"select * from ph_stock_process where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' "));
						$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
						if($stk)
						{
							$vstkqnt=$stk['s_remain']-$vqnt;
							$slqnt=$stk['sell']+$vqnt;
							
							//mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$qnt','2','$date','$time','$user')");
							mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$qnt','$vstkqnt','2','$date','$time','$user')");
							$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$qnt','2','$date','$time','$user')";
							
							mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',sell='$slqnt' where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
							
							$txt.="\nupdate ph_stock_process set s_remain='$vstkqnt',sell='$slqnt' where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'";
							
							mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
							
							$txt.="\nupdate ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
						}
						else // for if data not found
						{
							$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
							
							$vstkqnt=$stk['s_remain']-$vqnt;
							
							mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$qnt','$vstkqnt','2','$date','$time','$user')");
							
							mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$bill_no','$itm','$bch','$stk[s_remain]','0','$vqnt','$vstkqnt','$date')");
							
							$txt.="\ninsert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$bill_no','$itm','$bch','$stk[s_remain]','0','$vqnt','$vstkqnt','$date')";
							
							mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
							
							$txt.="\nupdate ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
						}
						if($ind_no && $patient_id)
						{
							$indend_chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `ipd_pat_medicine_indent` WHERE `patient_id`='$patient_id' AND `ipd_id`='$pin' AND `indent_num`='$ind_no' AND `item_code`='$itm'"));
							if($indend_chk)
							{
								mysqli_query($link,"UPDATE `ipd_pat_medicine_indent` SET `status`='1' WHERE `patient_id`='$patient_id' AND `ipd_id`='$pin' AND `indent_num`='$ind_no' AND `item_code`='$itm'");
								mysqli_query($link,"UPDATE `patient_medicine_detail` SET `status`='$qnt', `bill_no`='$bill_no' WHERE `patient_id`='$patient_id' AND `pin`='$pin' AND `indent_num`='$ind_no' AND `item_code`='$itm'");
							}
						}
					}
				}
				
				$txt.="\n-----------------------------------------------------------------------------------";
				text_query($txt);
				echo "Saved@penguin@".$bill_id."@@".$bill_no;
			}
			else
			{
				echo "0@penguin@0@@0";
			}
		}
		else
		{
			//echo "0@@0@@0";
			echo "Less@penguin@".$less;
		}
	}
	if($btn_val=="Update")
	{
		$bill_no=$updt_bill;
		
		//------------------For stock insert--------------------------//
		$sell_qry=mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_id`='$bill_id'");
		$txt="\nSELECT * FROM `ph_sell_details` WHERE `bill_id`='$bill_id'\n";
		$sell_num=mysqli_num_rows($sell_qry);
		if($sell_num>0)
		{
			while($ins=mysqli_fetch_assoc($sell_qry))
			{
				$vstkqnt=0;
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where `substore_id`='$ph' and date='$date' and item_code='$ins[item_code]' and  batch_no='$ins[batch_no]' order by slno desc limit 0,1"));
				if($qrstkmaster['item_code']!='') // if data found in current date
				{
					$vstkqnt=$qrstkmaster['s_remain']+$ins['sale_qnt'];
					$add=$qrstkmaster['added']+$ins['sale_qnt'];
					
					mysqli_query($link,"update ph_stock_process set process_no='$bill_id',s_remain='$vstkqnt' where `substore_id`='$ph' and date='$date' and item_code='$ins[item_code]' and batch_no='$ins[batch_no]'");
					
					$txt="update ph_stock_process set process_no='$bill_id',added='$add',s_remain='$vstkqnt' where `substore_id`='$ph' and date='$date' and item_code='$ins[item_code]' and batch_no='$ins[batch_no]'";
					
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where `substore_id`='$ph' and item_code='$ins[item_code]' and batch_no='$ins[batch_no]'");
					$txt.="\nupdate ph_stock_master set quantity='$vstkqnt' where `substore_id`='$ph' and item_code='$ins[item_code]' and batch_no='$ins[batch_no]'";
				}
				else///for if data not found
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where `substore_id`='$ph' and item_code='$ins[item_code]' and batch_no='$ins[batch_no]' order by slno desc limit 0,1"));
					$vstkqnt=$qrstkmaster['s_remain']+$ins['sale_qnt'];
					
					mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,return_cstmr,return_supplier,s_remain,date) values('$ph','$bill_id','$ins[item_code]','$ins[batch_no]','$qrstkmaster[s_remain]','$ins[sale_qnt]',0,0,0,'$vstkqnt','$date')");
					
					$txt.="\ninsert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,return_cstmr,return_supplier,s_remain,date) values('$ph','$bill_id','$ins[item_code]','$ins[batch_no]','$qrstkmaster[s_remain]','$ins[sale_qnt]',0,0,0,'$vstkqnt','$date')";
					
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where substore_id='$ph' and item_code='$ins[item_code]' and batch_no='$ins[batch_no]'");
					$txt.="\nupdate ph_stock_master set quantity='$vstkqnt' where substore_id='$ph' and item_code='$ins[item_code]' and batch_no='$ins[batch_no]'";
				}
			}
			mysqli_query($link,"DELETE FROM `ph_sell_details` WHERE `bill_id`='$bill_id' AND `substore_id`='$ph'");
			$txt.="\nDELETE FROM `ph_sell_details` WHERE `bill_id`='$bill_id' AND `substore_id`='$ph'";
		}
		//--------------new insert----------------------------//
		
		if(mysqli_query($link,"UPDATE `ph_sell_master` SET `customer_name`='$cust_name', `customer_phone`='$contact', `address`='$addr', `co`='$co', `total_amt`='$total',`discount_perchant`='$discount',`discount_amt`='$dis_amt',`adjust_amt`='$adjust',`paid_amt`='$paid',`balance`='$balance',`bill_type_id`='$bill_typ',`refbydoctorid`='$ref_by',`round_type`='$round_type',`round`='$round',`gst_amount`='$gst' WHERE `bill_no`='$bill_no' AND `substore_id`='$ph'"))
		{
			// text file
			$txt.="\nUPDATE `ph_sell_master` SET `customer_name`='$cust_name', `customer_phone`='$contact', `address`='$addr', `co`='$co', `total_amt`='$total',`discount_perchant`='$discount',`discount_amt`='$dis_amt',`adjust_amt`='$adjust',`paid_amt`='$paid',`balance`='$balance',`bill_type_id`='$bill_typ',`refbydoctorid`='$ref_by',`round_type`='$round_type',`round`='$round',`gst_amount`='$gst' WHERE `bill_no`='$bill_no' AND `substore_id`='$ph'";
			
			mysqli_query($link,"delete from  `ph_payment_details`  WHERE `bill_no`='$bill_no' AND `substore_id`='$ph' and type_of_payment='A' ");
			
			mysqli_query($link,"UPDATE `ph_payment_details` SET `amount`='$paid' WHERE `bill_no`='$bill_no' AND `substore_id`='$ph'");
			
			$txt.="\nUPDATE `ph_payment_details` SET `amount`='$paid' WHERE `bill_no`='$bill_no' AND `substore_id`='$ph'";
						
			$cntr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `entry_no` FROM `ph_sell_master_edit` WHERE `bill_no`='$bill_no' AND `substore_id`='$ph' ORDER BY `slno` DESC"));
			$entry_no=$cntr['entry_no']+1;
			
			mysqli_query($link,"INSERT INTO `ph_sell_master_edit`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `customer_name`, `customer_phone`, `total_amt`, `discount_perchant`, `discount_amt`, `adjust_amt`, `paid_amt`, `balance`, `bill_type_id`, `patient_id`, `opd_id`, `ipd_id`, `patient_type`, `refbydoctorid`, `pat_type`, `user`, `time`, `round_type`, `round`, `gst_amount`, `entry_no`) VALUES ('$bill_id','$bill_no','$ph','$date','$cust_name','$contact','$total','$discount','$dis_amt','$adjust','$paid','$balance','0','$uhid','$pin','$pin','0','$ref_by','0','$user','$time','$round_type','$round','$gst','$entry_no')");
			
			$txt.="\nINSERT INTO `ph_sell_master_edit`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `customer_name`, `customer_phone`, `total_amt`, `discount_perchant`, `discount_amt`, `adjust_amt`, `paid_amt`, `balance`, `bill_type_id`, `patient_id`, `opd_id`, `ipd_id`, `patient_type`, `refbydoctorid`, `pat_type`, `user`, `time`, `round_type`, `round`, `gst_amount`, `entry_no`) VALUES ('$bill_id','$bill_no','$ph','$date','$cust_name','$contact','$total','$discount','$dis_amt','$adjust','$paid','$balance','0','$uhid','$pin','$pin','0','$ref_by','0','$user','$time','$round_type','$round','$gst','$entry_no')";
			
			$al=explode("#@#",$all);
			foreach($al as $a)
			{
				$v=explode("@@",$a); // all+=itm+"@@"+bch+"@@"+qnt+"@@"+mrp+"@@"+amt+"@@"+gst_per+"@@"+gst_amt+"@@"+expdt+"@@#@#";
				$itm=$v[0];
				$bch=$v[1];
				$qnt=$v[2];
				$mrp=$v[3];
				$amt=$v[4];
				$gst_per=$v[5];
				$gst_amt=$v[6];
				$expdt=$v[7];
				$net_amt=$amt-$gst_amt;
				if($itm && $qnt)
				{
					$price=mysqli_fetch_array(mysqli_query($link,"SELECT `recept_cost_price`,`sale_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$itm' AND `expiry_date`='$expdt' AND `recept_batch`='$bch'"));
					
					$vttlamt=($qnt*$mrp);
					//$vttlcstprc=val_con($qnt*$recpt);
					$vslprice1=$mrp-($mrp*(100/(100+$gst_per)));
					$vslprice=round($mrp-$vslprice1,2);
					
					$gst_amt=$vttlamt-($vttlamt*(100/(100+$gst_per)));
					$net_amt=$vttlamt-$gst_amt;
					
					mysqli_query($link,"INSERT INTO `ph_sell_details`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`) VALUES ('$bill_id','$bill_no','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$vslprice')");
					
					$txt.="\nINSERT INTO `ph_sell_details`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`) VALUES ('$bill_id','$bill_no','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$price[sale_price]')";
					
					mysqli_query($link,"INSERT INTO `ph_sell_details_edit`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`, `entry_no`) VALUES ('$bill_id','$bill_no','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$price[sale_price]','$entry_no')");
					
					$txt.="\nINSERT INTO `ph_sell_details_edit`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`, `entry_no`) VALUES ('$bill_id','$bill_no','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$price[sale_price]','$entry_no')";
					
					//-------------stock----------------
					$slqnt=$qnt;
					$freqnt=0;
					$vqnt=$slqnt+$freqnt;
					$vbtch=$bch;
					
					$vstkqnt=0;
					$num=mysqli_num_rows(mysqli_query($link,"select * from ph_stock_process where date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' "));
					$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
					if($num>0)
					{
						$vstkqnt=$stk['s_remain']-$vqnt;
						$slqnt=$stk['sell']+$vqnt;
						mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',sell='$slqnt' where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
						
						$txt.="\nupdate ph_stock_process set s_remain='$vstkqnt',sell='$slqnt' where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'";
						
						mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
						
						$txt.="\nupdate ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
					}
					else // for if data not found
					{
						$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
						$vstkqnt=$stk['s_remain']-$vqnt;
						mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$bill_id','$itm','$bch','$stk[s_remain]','0','$vqnt','$vstkqnt','$date')");
						
						$txt.="\ninsert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$bill_id','$itm','$bch','$stk[s_remain]','0','$vqnt','$vstkqnt','$date')";
						
						mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
						
						$txt.="\nupdate ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
					}
				}
			}
			text_query($txt);
			echo "Updated@penguin@".$bill_id."@@".$bill_no;
		}
		else
		{
			echo "0@penguin@0@@0";
		}
	}
}


if($_POST['type']=="load_sale_bill")
{
	$bill_id=$_POST['bill_id'];
	$bill=$_POST['bill'];
	
	$qry=mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_no`='$bill'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		?>
		<table class='table table-condensed table-bordered' id='mytable'>
			<tr style='background-color:#cccccc'>
				<th>Sl No</th><th>Medicine</th><th>Batch No</th><th>Quantity</th><th>MRP</th><th>Amount</th><th style='width:5%;'>Remove</th>
			</tr>
			<?php
			$j=1;
			while($r=mysqli_fetch_assoc($qry))
			{
				$itm=$r['item_code'];
				$bch=$r['batch_no'];
				$rt=($r['sale_qnt']*$r['mrp']);
				$it=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_id`='$itm'"));
				$gst=$rt-($rt*(100/(100+$r['gst_percent'])));
				$gst=number_format($gst,2);
			?>
			<tr class='all_tr <?php echo $itm.$bch;?>'>
				<td><?php echo $j;?></td>
				<td>
					<?php echo $it['item_name'];?>
					<input type='hidden' value='<?php echo $itm;?>' class='itm' />
					<input type='hidden' value='<?php echo $itm.$bch;?>' class='test_id' />
				</td>
				<td>
					<?php echo $bch;?><input type='hidden' value='<?php echo $bch;?>' class='batch' />
				</td>
				<td>
					<input type='text' value='<?php echo $r['sale_qnt'];?>' class='qnt span1' style='padding:0px 3px;' onkeyup='manage_qnt(this,event)' />
				</td>
				<td>
					<?php echo $r['mrp'];?><input type='hidden' value='<?php echo $r['mrp'];?>' class='mrp' />
				</td>
				<td>
					<span class='rate_str'><?php echo $rt;?></span><input type='hidden' value='<?php echo $rt;?>' class='all_rate' />
					<input type='hidden' value='<?php echo $r['gst_percent'];?>' class='gst_per' />
				</td>
				<td style='text-align:center;'>
					<input type='hidden' value='<?php echo $gst;?>' class='all_gst' />
					<input type='hidden' value='<?php echo $r['expiry_date'];?>' class='expdt' />
					<span onclick='$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'>
						<i class='icon-remove icon-large text-danger'></i>
					</span>
					<span></span>
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

if($_POST['type']=="load_bill_det")
{
	$bill=$_POST['bill'];
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `bill_no`='$bill'"));
	$adj=explode(".",$det['adjust_amt']);
	$paid=explode(".",$det['paid_amt']);
	$dis_p=explode(".",$det['discount_perchant']);
	$dis_amt=explode(".",$det['discount_amt']);
	$bal=explode(".",$det['balance']);
	echo $det['opd_id']."@@".$det['customer_name']."@@".$det['refbydoctorid']."@@".$det['customer_phone']."@@".$dis_p[0]."@@".$dis_amt[0]."@@".$adj[0]."@@".$paid[0]."@@".$bal[0]."@@".$det['address']."@@".$det['co']."@@".$det['bill_type_id']."@@".$det['substore_id'];
}

if($_POST['type']=="edit_cus_name")
{
	$bill=$_POST['bill'];
	$d=mysqli_fetch_assoc(mysqli_query($link,"SELECT `customer_name`,`customer_phone`,`address`,`co`,`patient_type` FROM `ph_sell_master` WHERE `bill_no`='$bill'"));
	?>
	<input type="hidden" id="upd_bill" value="<?php echo $bill;?>" />
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Customer Name</th><th>Contact No</th><th>Address</th><th>Care of</th><th>Patient Type</th>
		</tr>
		<tr>
			<td>
				<input type="text" id="cname" value="<?php echo $d['customer_name'];?>" placeholder="Customer Name" />
			</td>
			<td>
				<input type="text" id="ccontact" maxlength="10" onkeyup="chk_num(this,event)" value="<?php echo $d['customer_phone'];?>" placeholder="Contact" />
			</td>
			<td>
				<input type="text" id="caddress" value="<?php echo $d['address'];?>" placeholder="Address" />
			</td>
			<td>
				<input type="text" id="ccare" value="<?php echo $d['co'];?>" placeholder="Care of" />
			</td>
			<td>
				<select id="cp_type" class="span2">
					<option value="1" <?php if($d['patient_type']=="1"){echo "selected='selected'";}?>>General</option>
					<option value="2" <?php if($d['patient_type']=="2"){echo "selected='selected'";}?>>ESI</option>
					<option value="3" <?php if($d['patient_type']=="3"){echo "selected='selected'";}?>>In House</option>
					<option value="4" <?php if($d['patient_type']=="4"){echo "selected='selected'";}?>>Ayushman</option>
					<option value="5" <?php if($d['patient_type']=="5"){echo "selected='selected'";}?>>Staff</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="5" style="text-align:center;">
				<button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
				<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="update_cus_name()" aria-hidden="true">Update</button>
			</td>
		</tr>
	</table>
	<?php
}

if($_POST['type']=="update_cus_name")
{
	$bill=$_POST['bill'];
	$cname=$_POST['cname'];
	$ccontact=$_POST['ccontact'];
	$caddress=$_POST['caddress'];
	$ccare=$_POST['ccare'];
	$cp_type=$_POST['cp_type'];
	if(mysqli_query($link,"UPDATE `ph_sell_master` SET `customer_name`='$cname',`customer_phone`='$ccontact',`address`='$caddress',`co`='$ccare',`patient_type`='$cp_type' WHERE `bill_no`='$bill'"))
	{
		mysqli_query($link,"UPDATE `ph_sell_master_edit` SET `customer_name`='$cname',`customer_phone`='$ccontact',`address`='$caddress',`co`='$ccare',`patient_type`='$cp_type' WHERE `bill_no`='$bill'");
		echo "Updated";
	}
	else
	{
		echo "Error";
	}
}

if($_POST['type']=="check_bill_id")
{
	$current_month=date("m");
	if($current_month<4)
	{
		echo "1";
	}
	else
	{
		$current_year=date("Y-04-01");
		$c=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`>='$current_year'"));
		if($c)
		{
			echo "2";
		}
		else
		{
			echo "3";
		}
	}
}

if($_POST['type']=="pat_bill_update")
{
	$bill=$_POST['bill'];
	$pat_name=mysqli_real_escape_string($link,$_POST['pat_name']);
	$bl_type=$_POST['bl_type'];
	$dis_per=$_POST['dis_per'];
	$dis_amt=$_POST['dis_amt'];
	$adj_amt=$_POST['adj_amt'];
	$pay_amt=$_POST['pay_amt'];
	$bal_amt=$_POST['bal_amt'];
	$pat_type=$_POST['pat_type'];
	$bill_date=$_POST['bill_date'];
	
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `bill_no`='$bill'"));
	if($dis_per=="")
	{
		$dis_per=0;
	}
	if($bl_type=="4")
	{
		$pay_type="Card";
	}
	else
	{
		$pay_type="Cash";
	}
	
	//~ $dis_amt=(($det['total_amt']*$dis_per)/100);
	//~ $net_pay=$det['total_amt']-$dis_amt;
	//~ $adj_amt=0;
	//~ $balance=$net_pay-$det['paid_amt'];
	
	//~ $adj_amt=$det['paid_amt']-$net_pay;
	//~ $balance=0;
	//-------------------------------------------------
	//~ $dis_per=$det['discount_perchant'];
	//~ $dis_amt=$det['discount_amt'];
	//~ $adj_amt=$det['adjust_amt'];
	//~ $net_pay=$det['paid_amt'];
	//~ $balance=$det['balance'];
	
	
	if(mysqli_query($link,"UPDATE `ph_sell_master` SET `entry_date`='$bill_date', `customer_name`='$pat_name', `discount_perchant`='$dis_per', `discount_amt`='$dis_amt', `adjust_amt`='$adj_amt', `paid_amt`='$pay_amt', `balance`='$bal_amt', `bill_type_id`='$bl_type', `patient_type`='$pat_type' WHERE `bill_no`='$bill'"))
	{
		$pay_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_payment_details` WHERE `bill_no`='$bill'"));
		if($pay_det)
		{
			mysqli_query($link,"DELETE FROM `ph_payment_details` WHERE `bill_no`='$bill' AND `type_of_payment`='B'");
			mysqli_query($link,"UPDATE `ph_payment_details` SET `entry_date`='$bill_date', `amount`='$pay_amt', `payment_mode`='$pay_type' WHERE `bill_no`='$bill'");
		}
		else
		{
			mysqli_query($link,"INSERT INTO `ph_payment_details`(`bill_no`, `substore_id`, `entry_date`, `amount`, `payment_mode`, `check_no`, `type_of_payment`, `user`, `time`) VALUES ('$bill','$det[substore_id]','$bill_date','$pay_amt','$pay_type','0','A','$user','$det[time]')");
		}
		echo "Updated";
	}
	else
	{
		echo "Error";
	}
}

if($_POST['type']=="oo")
{
	$bill=$_POST['bill'];
}
?>
