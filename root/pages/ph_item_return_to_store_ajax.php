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
		$new_date = date('M Y', $timestamp);
		return $new_date;
	}
}

// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}


$dname=mysqli_real_escape_string($link,$_POST['val']);
$ph=$_POST['substore_id'];
//$ph=2;
if(trim($_POST['type'])=="item")
{
if($dname)
{
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Code</th><th>Item Name</th><th>Stock</th>
		</tr>
<?php
	
	if($dname)
	{
		//$q="select a.* from item_master a, ph_stock_master b where a.item_name!='' and a.item_name like '$dname%' or a.item_name!='' and a.item_id like '$dname%' or a.item_name!='' and a.short_name like '$dname%' order by a.item_name limit 0,30";
		
		$q="select distinct b.`item_code` from item_master a, ph_stock_master b where";
		$q.=" a.item_id=b.item_code and b.quantity>0  and b.substore_id='$ph' and a.item_name!='' and a.item_name like '$dname%'";
		$q.=" or a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph' and a.item_name!='' and a.item_id like '$dname%'";
		$q.=" or a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph' and a.item_name!='' and a.short_name like '$dname%'";
		$q.=" order by a.item_name limit 0,30";
	}
	else
	{
		$q="select distinct b.`item_code` from item_master a, ph_stock_master b where a.item_name!='' and a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph'";
		$q.=" order by a.item_name limit 0,30";
	}
	//echo $q;
	$d=mysqli_query($link, $q);
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `item_master` WHERE `item_id`='$d1[item_code]'"));
		$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`quantity`),0) AS stock FROM `ph_stock_master` WHERE `item_code`='$d1[item_code]' AND `substore_id`='$ph'"));
		?>
		<tr onclick="doc_load('<?php echo $itm['item_id'];?>','<?php echo $itm['item_name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
			<td><?php echo $itm['item_id'];?></td>
			<td><?php echo $itm['item_name'];?>
				<div <?php echo "id=dvdoc".$i;?> style="display:none;">
				<?php echo "#".$itm['item_id']."#".$itm['item_name'];?>
				</div>
			</td>
			<td><?php echo $stk['stock'];?></td>
		</tr>
	<?php
		$i++;
	}
	?>
	</table>
<?php
}
}

if(trim($_POST['type'])=="batch")
{
	$item_id=$_POST['item_id'];
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
		$mrp=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,`gst_per` FROM `ph_purchase_receipt_details` WHERE `item_code`='$item_id' AND `recept_batch`='$d1[batch_no]'"));
		?>
		<tr onClick="hguide_load('<?php echo $d1['batch_no'];?>','<?php echo $d1['quantity'];?>','<?php echo $mrp['recpt_mrp'];?>','<?php echo $mrp['gst_per'];?>','<?php echo $d1['exp_date'];?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
			<td><?php echo $d1['batch_no'];?></td>
			<td>
				<?php echo $d1['quantity'];?>
				<div <?php echo "id=dvhguide".$i;?> style="display:none;">
					<?php echo "#".$d1['batch_no']."#".$d1['quantity']."#".$mrp['recpt_mrp']."#".$mrp['gst_per']."#".$d1['exp_date'];?>
				</div>
			</td>
			<td><?php echo $mrp['recpt_mrp'];?></td>
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

if($_POST['type']=="pat_det")
{
	$val=mysqli_real_escape_string($link,$_POST['val']);
	$ph=$_POST['ph'];
	//$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$val'")); // search patient_id
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT a.`name`,a.phone,a.address,b.refbydoctorid,b.center_no,b.type FROM `patient_info` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND b.`ipd_serial`='$val'")); // search patient_id
   
    $qbalchk=mysqli_fetch_array(mysqli_query($link,"SELECT pharmacy_bal  FROM `patient_source_master` WHERE `centreno`='$pat[center_no]'")); 
     
     $refdoc=$pat['refbydoctorid'];
    if($pat['type']==1)
    {
		$qdoc=mysqli_fetch_array(mysqli_query($link," select consultantdoctorid from appointment_book where  opd_id='$val'"));
		$refdoc=$qdoc['consultantdoctorid'];
	}     

    $val=$pat['name'].'@#'.$refdoc.'@#'.$pat['phone'].'@#'.$pat['address'].'@#'.$qbalchk['pharmacy_bal'].'@#'.$ph;
	echo $val;
}

if($_POST['type']=="check_all_items")
{
	$all=$_POST['all'];
	$btnvalue=$_POST['btnvalue'];
	$ph=$_POST['substore_id'];
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
	$issue_to=mysqli_real_escape_string($link,$_POST['issue_to']);
	$ph=$_POST['substore_id'];
	$user=$_POST['user'];
	$all=$_POST['all'];
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
		$bill_tot=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) AS cnt FROM `ph_item_return_store_master`"));
		$bill_no=$bill_tot['cnt']+1;
		$issueno="RET".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
		//$issueno=date("YmdHis").$user;
		mysqli_query($link,"INSERT INTO `ph_item_return_store_master`(`returnr_no`, `substore_id`, `date`, `stat`, `del`, `user`, `time`) VALUES ('$issueno','$ph','$date','0','0','$user','$time')");
		$val=explode("#@#",$all);
		foreach($val as $vl)
		{
			$v=explode("@@",$vl);
			$itm=$v[0];
			$bch=$v[1];
			$qnt=$v[2];
			$mrp=$v[3];
			$amt=$v[4];
			$gst_per=$v[5];
			$gst_amt=$v[6];
			$expdt=$v[7];
			$vqnt=$qnt;
			if($itm && $bch)
			{
				$price=mysqli_fetch_array(mysqli_query($link,"SELECT `recept_cost_price`,`sale_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$itm' AND `recept_batch`='$bch' ORDER BY `slno` DESC LIMIT 0,1"));
				
				mysqli_query($link,"INSERT INTO `ph_item_return_store_detail`(`returnr_no`, `reason`, `item_id`, `expiry_date`, `date`, `quantity`, `free_qnt`, `batch_no`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`, `user`) VALUES ('$issueno','','$itm','$expdt','$date','$qnt','0','$bch','$mrp','$price[recept_cost_price]','$amt','0','0','$gst_per','$gst_amt','$user')");
				
				$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='$ph' and item_code='$itm' and batch_no='$bch' and date='$date' order by slno desc limit 0,1"));
				if($stk)
				{
					$remain=$stk['s_remain']-$vqnt;
					$return=$stk['return_supplier']+$vqnt;
					mysqli_query($link,"update ph_stock_process set s_remain='$remain',return_supplier='$return' where date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$issueno','$itm','$bch','$stk[s_remain]','$qnt','$remain','8','$date','$time','$user')");
					mysqli_query($link,"update ph_stock_master set quantity='$remain' where substore_id='$ph' and item_code='$itm' and batch_no='$bch'");
				}
				else
				{
					$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='$ph' and item_code='$itm' and batch_no='$bch' order by slno desc limit 0,1"));
					$remain=$stk['s_remain']-$vqnt;
					mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,return_supplier,s_remain,date) values('$ph','RETURN','$itm','$bch','$stk[s_remain]','0','$vqnt','$remain','$date')");
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$issueno','$itm','$bch','$stk[s_remain]','$qnt','$remain','8','$date','$time','$user')");
					mysqli_query($link,"update ph_stock_master set quantity='$remain' where substore_id='$ph' and item_code='$itm' and batch_no='$bch'");
				}
				
				/*---------------------------------central store stock------------------------------*/
				
				$stk_master=mysqli_fetch_assoc(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and batch_no='$bch' order by  date desc"));
				if($stk_master)
				{
					$stk_qnt=$stk_master['closing_qnty']+$qnt;
					$rcv_qnt=$stk_master['recv_qnty']+$qnt;
					mysqli_query($link,"update inv_mainstock_details set closing_qnty='$stk_qnt',recv_qnty='$rcv_qnt' where date='$date' and item_id='$itm' and batch_no='$bch'");
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$stk_qnt','$exp_dt')");
				}
				else
				{
					$stk_master=mysqli_fetch_assoc(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc"));
					$stk_qnt=$stk_master['closing_qnty']+$qnt;
					mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','$bch','$date','$stk_master[closing_qnty]','$qnt',0,'$stk_qnt')");
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$stk_qnt','$exp_dt')");
				}
			}
		}
		echo "1@govin@Done";
	}
	else
	{
		echo "0@govin@".$less;
	}
}

if($_POST['type']=="oo")
{
	
}
?>
