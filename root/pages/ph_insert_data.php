<?php
session_start();

include'../../includes/connection.php';

$date=date('Y-m-d'); // impotant
$time=date('H:i:s');


function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

///////Date difference in php
	 /*$vendat=strtotime($mnufctr);
	 $vpaydat=strtotime($expiry);	
	 $vda=$vpaydat-$vendat;
	 echo  floor($vda/3600/24); */
	//////end///////////////
	

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
	
$fid=mysqli_fetch_array(mysqli_query($link,"select max(FID) as maxfid from financialyear_master "));
$fd=$fid['maxfid'];
$fd=0;

$type=$_POST['type'];


 

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

if($type=="itemtype") ///For  Item Type Master
{
	$csid=$_POST['csid'];
	$csname=$_POST['csname'];
		
}

////////////////////////////////
elseif($type=="phcategory") //for  pharmacy Category
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	
	$qrch=mysqli_fetch_array(mysqli_query($link,"select ph_cate_id from ph_category_master where ph_cate_id='$id'"));
	if($qrch)
	{
		mysqli_query($link,"update ph_category_master set ph_cate_name='$name' where ph_cate_id='$id'");
	
	}
	else
	{
	  mysqli_query($link,"insert into ph_category_master values('$id','$name')");
  
	}
}
///////////////////////////////

if($_POST["type"]=="insert_item_master")  //////////for Pharmacy Item
{
	$csid=$_POST['csid'];
	$csname=$_POST['csname'];
	$catid=$_POST['catid'];
	$csname= str_replace("'", "''", "$csname");
	$vtpeye=$_POST['vtpeye'];
	$gen_name=$_POST['gen_name'];
	$vmrp=$_POST['vmrp'];
	$vmrp= str_replace("'", "''", "$vmrp");
	$vstrngth=$_POST['vstrngth'];
	$vstrngth= str_replace("'", "''", "$vstrngth");
	$vvat=0;
	$costprice=$_POST['costprice'];
	$gst=$_POST['gst'];
	$strpqnty=$_POST['strpqnty'];
	$hsncode=$_POST['hsncode'];
	
	$qrch=mysqli_num_rows(mysqli_query($link,"select * from ph_item_master where item_code='$csid'"));
	
	if($qrch>0)
	{
		if(mysqli_query($link,"update ph_item_master set item_name='$csname',generic='$gen_name',item_strength='$vstrngth',item_mrp='$vmrp',cost_price='$costprice',vat='$vvat',item_type_id='$vtpeye',ph_cate_id='$catid',strip_qnty='$strpqnty',gst_percent='$gst',hsn_code='$hsncode' where item_code='$csid'"))
		{
			echo "Updated";
		}
	}
	else
	{
	  if(mysqli_query($link,"insert into  ph_item_master values('$csid','$csname',$gen_name,'$vstrngth','$vmrp','$costprice','$vvat','$vtpeye','$catid','$strpqnty','$gst','$hsncode')"))
	  {
		  echo "Saved";
	  }
	}
}

//////////////////////////////////

if($_POST["type"]=="purchse_ord_temp")
{
	$ordrdate=$_POST['ordrdate'];
	$supplr=$_POST['supplr'];
	$orderno=$_POST['orderno'];
	$itmcode=$_POST['itmcode'];
	$orqnt=$_POST['orqnt'];
	$fid=$_POST['fid'];
	$itmcode1=explode("-",$itmcode);
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ph_purchase_order_details_temp` WHERE `order_no`='$orderno' AND `item_code`='$itmcode1[1]'"));
	if($num>0)
	{
		if(mysqli_query($link,"UPDATE `ph_purchase_order_details_temp` SET `order_qnt`='$orqnt',`bl_qnt`='$orqnt' WHERE `order_no`='$orderno' AND `item_code`='$itmcode1[1]' AND `SuppCode`='$supplr'"))
		{
			echo "1";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `ph_purchase_order_details_temp`(`order_no`, `item_code`, `SuppCode`, `order_qnt`, `bl_qnt`, `order_date`, `stat`,fid) VALUES ('$orderno','$itmcode1[1]','$supplr','$orqnt','$orqnt','$ordrdate','0','$fid')"))
		{
			echo "1";
		}
	}
}
///////////////////////////////////////

if($_POST["type"]=="purchse_ord_final")
{
	$supplr=$_POST['supplr'];
	$orderno=$_POST['orderno'];
	$vdate=$_POST['ordrdate'];
	$fid=$_POST['fid'];
		
	mysqli_query($link,"delete from  ph_purchase_order_details where order_no ='$orderno' and SuppCode='$supplr' ");
	if(mysqli_query($link,"insert into ph_purchase_order_details select * from ph_purchase_order_details_temp where order_no='$orderno' and SuppCode='$supplr'"))
	{
		echo "Saved";
	}
	$qchk=mysqli_num_rows(mysqli_query($link,"select * from ph_purchase_order_details_temp where order_no ='$orderno' and SuppCode='$supplr' "));
	if($qchk>0)
	{
		mysqli_query($link,"delete from ph_purchase_order_master where order_no ='$orderno' and SuppCode='$supplr'  ");
		
		mysqli_query($link,"insert into ph_purchase_order_master values('$orderno','$supplr','$vdate','$fid' )");
		
		mysqli_query($link,"delete from ph_purchase_order_details_temp where order_no ='$orderno' and SuppCode='$supplr'  ");
	}
}
////////////////////////////////////

if($_POST["type"]=="itemreturn")  //for item Return from customer
{
	$billno=$_POST['billno'];
	$all=$_POST['all'];
	$all_alt=$_POST['all_alt'];
	$rtndate=$_POST['rtndate'];
	$reason=$_POST['reason'];
	$disc=$_POST['disc'];
	$ref_amt=$_POST['ref_amt'];
	$user=$_POST['user'];
	$prss="RETURN";
	
	$ph=mysqli_fetch_array(mysqli_query($link,"select substore_id from ph_sell_details where bill_no='$billno' limit 0,1"));
	$ph=$ph['substore_id'];
	$less="";
	$al_alt=explode("#%#",$all_alt);
	foreach($al_alt as $a_alt)
	{
		$v_alt=explode("@@",$a_alt);
		$itm=$v_alt[0];
		$bch=$v_alt[1];
		$qnt=$v_alt[2];
		$mrp=$v_alt[3];
		$amt=$v_alt[4];
		$gst_per=$v_alt[5];
		$gst_amt=$v_alt[6];
		$expdt=$v_alt[7];
		
		if($itm && $bch)
		{
			$stk=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch'"));
			if($stk['quantity']<$qnt)
			{
				$less.=$itm."@@".$bch."@@#%#";
			}
		}
	}
	
	if($less=="")
	{
		$add_alt_amt=0;
		$al_alt=explode("#%#",$all_alt);
		foreach($al_alt as $a_alt)
		{
			$v_alt=explode("@@",$a_alt);
			$itm=$v_alt[0];
			$bch=$v_alt[1];
			$qnt=$v_alt[2];
			$mrp=$v_alt[3];
			$amt=$v_alt[4];
			$gst_per=$v_alt[5];
			$gst_amt=$v_alt[6];
			$expdt=$v_alt[7];
			$net_amt=$amt+$gst_amt;
			$add_alt_amt=$add_alt_amt+$amt;
			if($itm && $bch && $qnt)
			{
				$sell=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_no`='$billno' AND `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch'"));
				
				$price=mysqli_fetch_array(mysqli_query($link,"SELECT `recept_cost_price`,`sale_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$itm' AND `expiry_date`='$expdt' AND `recept_batch`='$bch'"));
				$txt.="\nSELECT `recept_cost_price`,`sale_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$itm' AND `expiry_date`='$expdt' AND `recept_batch`='$bch';";
				
				if($sell)
				{
					$txt.="\nSELECT * FROM `ph_sell_details` WHERE `bill_no`='$billno' AND `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch';";
					$qnt=$qnt+$sell['sale_qnt'];
					$amt=$amt+$sell['total_amount'];
					$gst_amt=$gst_amt+$sell['gst_amount'];
					$net_amt=$net_amt+$sell['net_amount'];
					mysqli_query($link,"UPDATE `ph_sell_details` SET `sale_qnt`='$qnt',`total_amount`='$amt',`net_amount`='$net_amt',`gst_amount`='$gst_amt' WHERE `bill_no`='$billno' AND `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch'");
					$txt.="\nUPDATE `ph_sell_details` SET `sale_qnt`='$qnt',`total_amount`='$amt',`net_amount`='$net_amt',`gst_amount`='$gst_amt' WHERE `bill_no`='$billno' AND `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch';";
				}
				else
				{
					$vttlamt=($qnt*$mrp);
					//$vttlcstprc=val_con($qnt*$recpt);
					$vslprice1=$mrp-($mrp*(100/(100+$gst_per)));
					$vslprice=round($mrp-$vslprice1,2);
					$gst_amt=$vttlamt-($vttlamt*(100/(100+$gst_per)));
					$net_amt=$vttlamt-$gst_amt;
					$sell=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_id` FROM `ph_sell_details` WHERE `bill_no`='$billno' AND `substore_id`='$ph'"));
					$txt.="\nSELECT `bill_id` FROM `ph_sell_details` WHERE `bill_no`='$billno' AND `substore_id`='$ph';";
					
					mysqli_query($link,"INSERT INTO `ph_sell_details`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`) VALUES ('$sell[bill_id]','$billno','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$vslprice')");
					$txt.="\nINSERT INTO `ph_sell_details`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`) VALUES ('$sell[bill_id]','$billno','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$vslprice');";
				}
				$cnt=mysqli_fetch_array(mysqli_query($link,"select max(counter) as max from ph_item_alter_details where bill_no='$billno'"));
				$txt.="\nselect max(counter) as max from ph_item_alter_details where bill_no='$billno';";
				$count=$cnt['max']+1;
				
				mysqli_query($link,"INSERT INTO `ph_item_alter_details`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`, `counter`) VALUES ('$sell[bill_id]','$billno','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$vslprice','$count')");
				$txt.="\nINSERT INTO `ph_item_alter_details`(`bill_id`, `bill_no`, `substore_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`, `gst_amount`, `item_cost_price`, `sale_price`, `counter`) VALUES ('$sell[bill_id]','$billno','$ph','$date','$itm','$bch','$expdt','$qnt','0','$mrp','$amt','$net_amt','$gst_per','$gst_amt','$price[recept_cost_price]','$vslprice','$count');";
				
				//-------------stock----------------
				$slqnt=$qnt;
				$freqnt=0;
				$vqnt=$slqnt+$freqnt;
				$vbtch=$bch;
				
				$vstkqnt=0;
				$num=mysqli_num_rows(mysqli_query($link,"select * from ph_stock_process where date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph'"));
				$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
				if($num>0)
				{
					$txt.="\nselect * from ph_stock_process where date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph';";
					$txt.="\nselect * from ph_stock_process where date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1;";
					$vstkqnt=$stk['s_remain']-$vqnt;
					$slqnt=$stk['sell']+$vqnt;
					
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$billno','$itm','$bch','$stk[s_remain]','$qnt','$vstkqnt','11','$date','$time','$user')");
					$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$billno','$itm','$bch','$stk[s_remain]','$qnt','$vstkqnt','11','$date','$time','$user')";
					
					mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',sell='$slqnt' where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
					$txt.="\nupdate ph_stock_process set s_remain='$vstkqnt',sell='$slqnt' where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]';";
					
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
					$txt.="\nupdate ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph';";
				}
				else // for if data not found
				{
					$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
					$txt.="\nselect * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1;";
					
					$vstkqnt=$stk['s_remain']-$vqnt;
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$billno','$itm','$bch','$stk[s_remain]','$qnt','$vstkqnt','11','$date','$time','$user')");
					$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$billno','$itm','$bch','$stk[s_remain]','$qnt','$vstkqnt','11','$date','$time','$user')";
					
					mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$bill_id','$itm','$bch','$stk[s_remain]','0','$vqnt','$vstkqnt','$date')");
					$txt.="\ninsert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$bill_id','$itm','$bch','$stk[s_remain]','0','$vqnt','$vstkqnt','$date');";
					
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
					$txt.="\nupdate ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph';";
				}
			}
		}
		
		// =========== return ==============
		
		$all=explode("#%#",$all);
		$tot_amt=0;
		$cnt=mysqli_fetch_array(mysqli_query($link,"select max(counter) as max from ph_item_return_master where bill_no='$billno'"));
		$count=$cnt['max']+1;
		$p_amt=mysqli_fetch_array(mysqli_query($link,"select total_amt from ph_sell_master where bill_no='$billno'"));
		foreach($all as $al)
		{
			$al=explode("@@",$al);
			$itmid=$al[0];
			$batch=$al[1];
			$qnt=$al[2];
			if($itmid && $batch && $qnt)
			{
				
				////////////////for bill//////////////////
				$qmrp=mysqli_fetch_array(mysqli_query($link,"select substore_id,mrp,sale_qnt,gst_percent from ph_sell_details where bill_no='$billno' and item_code='$itmid' and batch_no='$batch'"));
				
				$vrtnamt=0;
				$vslqnt=$qmrp['sale_qnt']-$qnt;
				$vmrp=$qmrp['mrp']*$vslqnt;
				
				$vrtnamt=$qmrp['mrp']*$qnt;
				$vgstamt=$vmrp-($vmrp*(100/(100+$qmrp['gst_percent'])));
				//$tot_amt+=$vmrp;
				
				mysqli_query($link,"insert into ph_item_return_master(`substore_id`, `bill_no`, `item_code`, `batch_no`, `return_date`, `return_qnt`, `mrp`, `amount`, `return_reason`, `user`, `time`, `counter`) values('$qmrp[substore_id]','$billno','$itmid','$batch','$rtndate','$qnt','$qmrp[mrp]','$vrtnamt','$reason','$userid','$time','$count')");
				
				//mysqli_query($link,"update ph_sell_details set sale_qnt='$vslqnt',total_amount='$vmrp',net_amount='$vmrp',gst_amount='$vgstamt' where bill_no='$billno' and item_code='$itmid' and batch_no='$batch' ");
				
				//mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$qmrp[substore_id]','$billno','$itmid','$batch','$qnt','3','$date','$time','$user')");
				
				//------------------For stock--------------------------//
				 $vstkqnt=0;
				 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$rtndate' and item_code='$itmid' and  batch_no='$batch' and substore_id='1' order by slno desc limit 0,1"));
				 if($qrstkmaster['item_code']!='')
				 {
					$vstkqnt=$qrstkmaster['s_remain']+$qnt;
					$slqnt=$qrstkmaster['return_cstmr']+$qnt;
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$qmrp[substore_id]','$billno','$itmid','$batch','$qrstkmaster[s_remain]','$qnt','$vstkqnt','3','$date','$time','$user')");
					mysqli_query($link,"update ph_stock_process set process_no='$prss',s_remain='$vstkqnt',return_cstmr='$slqnt' where  date='$rtndate' and item_code='$itmid' and  batch_no='$batch' and substore_id='1'");
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itmid' and batch_no='$batch' and substore_id='1' ");
				 }
				 else///for if data not found
				 {
					 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$itmid' and  batch_no='$batch' and substore_id='1' order by slno desc limit 0,1"));
					 $vstkqnt=$qrstkmaster['s_remain']+$qnt;
					 mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$qmrp[substore_id]','$billno','$itmid','$batch','$qrstkmaster[s_remain]','$qnt','$vstkqnt','3','$date','$time','$user')");
					 mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,return_cstmr,return_supplier,s_remain,date) values('1','$prss','$itmid','$batch','$qrstkmaster[s_remain]',0,0,'$qnt',0,'$vstkqnt','$rtndate')");
					 mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$itmid' and batch_no='$batch' and substore_id='1'");
				 }
			}
		}
		
		
			$q1=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$billno'  "));
			$q2=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0)as maxreturn from ph_item_return_master where bill_no='$billno'  "));
			$vamt=$q2['maxreturn'];
			$tot_amt=$vamt;
			$vttlamt=round($q1['total_amt']-$vamt);
			$vpaid=round($q1['paid_amt']-$vamt);
			if($vpaid<0)
			{
			$vpaid=0;
			}

			$vbal=round($vttlamt-$vpaid-$q1['discount_amt']-$q1['adjust_amt']);
			if($vbal<0)
			{
			$vbal=0;
			}
			mysqli_query($link,"update ph_sell_master set balance='$vbal' where bill_no='$billno'  ");
			//mysqli_query($link,"update ph_sell_master set total_amt='$vttlamt',paid_amt='$vpaid',balance='$vbal' where bill_no='$billno'  ");

			////////////////////////
				
		$dis_amt=0;
		if($disc>0)
		{
			$dis_amt=($tot_amt*$disc)/100;
		}
		$tot_amt=$tot_amt-$dis_amt;
		$tot_amt=floor($tot_amt);
		if($add_alt_amt>0)
		{
			$old_sell=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$billno' and substore_id='$ph'"));
			$txt.="\nselect * from ph_sell_master where bill_no='$billno' and substore_id='$ph';";
			$new_tot=$old_sell['total_amt']+$add_alt_amt;
			$new_paid=$old_sell['paid_amt']+$add_alt_amt;
			$vbal=round($new_tot-$new_paid-$old_sell['discount_amt']-$old_sell['adjust_amt']);
			mysqli_query($link,"update ph_sell_master set total_amt='$new_tot',paid_amt='$new_paid', `balance`='0' where bill_no='$billno'");
			$txt.="\nupdate ph_sell_master set total_amt='$new_tot',paid_amt='$new_paid', `balance`='0' where bill_no='$billno';";
		}
		
		mysqli_query($link,"INSERT INTO `ph_item_return`(`bill_no`, `prev_amt`, `amount`, `date`, `time`, `user`) VALUES ('$billno','$p_amt[total_amt]','$ref_amt','$rtndate','$time','$user')");
		$txt.="INSERT INTO `ph_item_return`(`bill_no`, `prev_amt`, `amount`, `date`, `time`, `user`) VALUES ('$billno','$p_amt[total_amt]','$ref_amt','$rtndate','$time','$user')";
		
		if($p_amt['entry_date']==$date)
		{
			mysqli_query($link,"DELETE FROM `ph_payment_details` WHERE `bill_no`='$billno' AND `substore_id`='$p_amt[substore_id]' AND `type_of_payment`='B'");
			$txt.="\nDELETE FROM `ph_payment_details` WHERE `bill_no`='$billno' AND `substore_id`='$p_amt[substore_id]' AND `type_of_payment`='B';";
			
			mysqli_query($link,"UPDATE `ph_payment_details` SET `amount`='$vpaid' WHERE `bill_no`='$billno' AND `substore_id`='$p_amt[substore_id]' AND `type_of_payment`='A'");
			$txt.="\nUPDATE `ph_payment_details` SET `amount`='$vpaid' WHERE `bill_no`='$billno' AND `substore_id`='$p_amt[substore_id]' AND `type_of_payment`='A';";
		}
		
		echo $count."@penguin@Saved";
	}
	else
	{
		echo "0@penguin@".$less;
	}
}

/////////////////////////////////////
elseif($type=="ph_ipd_item_return") ///for ph ipd item return
{

	$cid=$_POST['cid'];
	$reason=$_POST['reason'];
	$date1=$_POST['date1'];
	$sbstrid=$_POST['sbstrid'];
	$billno=$_POST['blno'];
	$billno=explode("@#",$billno);
	$prss="Return";
	
	mysqli_query($link,"delete ph_item_return_master where return_qnt=0 ");
	foreach($billno as $blno)
	{		
		if($blno)
		{
		    $blnonw=explode("%%",$blno);
		    		    
		    $billno=$blnonw[0];
		    
			$cnt=mysqli_fetch_array(mysqli_query($link,"select max(counter) as max from ph_item_return_master where bill_no='$blnonw[0]'"));
			$count=$cnt['max']+1;

			$qph_ipdpay_chk=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$blnonw[0]' and ipd_id='$cid' and balance>0"));

			////////////////for bill//////////////////
			$qmrp=mysqli_fetch_array(mysqli_query($link,"select substore_id,mrp,sale_qnt,gst_percent from ph_sell_details where bill_no='$blnonw[0]' and item_code='$blnonw[3]' and batch_no='$blnonw[2]' "));
			
			$vrtnamt=0;
			$qnt=$blnonw[1];
			
			$vrtnamt=$qmrp['mrp']*$qnt;
			$vgstamt=$vmrp-($vmrp*(100/(100+$qmrp['gst_percent'])));
			//$tot_amt+=$vmrp;
			
			
			
			mysqli_query($link,"insert into ph_item_return_master(`substore_id`,`bill_no`, `item_code`, `batch_no`, `return_date`, `return_qnt`, `mrp`, `amount`, `return_reason`, `user`, `time`, `counter`) values('$qmrp[substore_id]','$blnonw[0]','$blnonw[3]','$blnonw[2]','$date1','$qnt','$qmrp[mrp]','$vrtnamt','$reason','$userid','$time','$count')");
			
			////////////////////////
			
					
			
			//------------------For stock--------------------------//
			 $vstkqnt=0;
			 $slqnt=0;
			 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$date' and item_code='$blnonw[3]' and  batch_no='$blnonw[2]' and substore_id='$qmrp[substore_id]' order by slno desc limit 0,1"));
			 if($qrstkmaster['item_code']!='')
			 {
				$vstkqnt=$qrstkmaster['s_remain']+$qnt;
				$slqnt=$qrstkmaster['return_cstmr']+$qnt;
				
				mysqli_query($link,"update ph_stock_process set process_no='$prss',s_remain='$vstkqnt',return_cstmr='$slqnt' where  date='$date1' and item_code='$blnonw[3]' and  batch_no='$blnonw[2]' and substore_id='$qmrp[substore_id]'");
				
				mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$blnonw[3]' and batch_no='$blnonw[2]' and substore_id='$qmrp[substore_id]' ");
			 }
			 else///for if data not found
			 {
				 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$blnonw[3]' and  batch_no='$blnonw[2]' and substore_id='$qmrp[substore_id]' order by slno desc limit 0,1"));
				 
				 $vstkqnt=$qrstkmaster['s_remain']+$qnt;
				 mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,return_cstmr,return_supplier,s_remain,date) values('$qmrp[substore_id]','$prss','$blnonw[3]','$blnonw[2]','$qrstkmaster[s_remain]',0,0,'$qnt',0,'$vstkqnt','$date1')");
				 
				 mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$blnonw[3]' and batch_no='$blnonw[2]' and substore_id='$qmrp[substore_id]'");
			 }
			 /////////////////
			 
			 
			 //////////////////////
		
			$q1=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$billno' and substore_id='$qmrp[substore_id]'"));
			
			$qchkrtn=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxrtnamt from ph_item_return_master where bill_no='$billno' and substore_id='$qmrp[substore_id]'"));
			
			//$vamt=$qchkrtn['maxrtnamt'];
			//$tot_amt+=$vamt;
			
			$vrefamt=round($qchkrtn['maxrtnamt']);			
			$vpaid=round($q1['paid_amt']);
			
			if($vpaid<0)
			{
				$vpaid=0;
			}
			//$vbal=round($vttlamt-$vpaid-$q1['discount_amt']-$q1['adjust_amt']);
			$vbal=round($q1['total_amt']-$vpaid-$q1['discount_amt']-$q1['adjust_amt']-$vrefamt);
			if($vbal<0)
			{
			 $vbal=0;
			}
			mysqli_query($link,"update ph_sell_master set balance='$vbal',return_amt='$vrefamt' where bill_no='$billno' and substore_id='$qmrp[substore_id]' ");
			$cash_return=0;
			$cash_return=$vrefamt+$vbal+$q1['paid_amt']+$q1['discount_amt']+$q1['adjust_amt']-$q1['total_amt'];
			if($cash_return<0)
			{
			$cash_return=0;
			}					
			mysqli_query($link,"INSERT INTO `ph_item_return`(`substore_id`, `bill_no`, `prev_amt`, `amount`, `cash_return`, `date`, `time`, `user`, `status`, `accp_user`,`counter`) VALUES ('$qmrp[substore_id]','$billno','$q1[total_amt]','$vrtnamt','$cash_return','$date1','$time','$userid',0,0,0)");
			

		   ///////////////////////////
		
			   
		}
		
		
			   
	}
}

///////////////////////////////////////////
if($_POST["type"]=="purchase_rcv_tmp")
{
	$splrcode=$_POST['spplrid'];
	$orderno=$_POST['orderno'];
	$billno=$_POST['billno'];
	$itmid=$_POST['itmid'];
	$mnufctr=$_POST['mnufctr'];
	$expiry=$_POST['vexpiry'];
	$batch=$_POST['batch'];
	$qnt=$_POST['qnt'];
	$bal=$_POST['bal'];
	$freeqnt=$_POST['freeqnt'];
	$Vmrp=$_POST['mrp'];
	$pktqnt=$_POST['pktqnt'];
	$slprice=$_POST['slprice'];
	$vcstprice=$_POST['vcstprice'];
	$vscstamt=$_POST['vscstamt'];
	$gst=$_POST['gst'];
	$disprchnt=$_POST['disprchnt'];
	
	if($disprchnt=="")
	{
		$disprchnt=0;
	}
	
	//$itmid1=explode("-#",$itmid);
	//$vitmid=$itmid1['1'];
	$vitmid=$itmid;
	
	$mrp=$Vmrp/$pktqnt;
	
	$vdate=date('Y-m-d');
	$fid=0;
	
    
    $frstdy='01';
	$vdt=$expiry.'-'.'01';
	$d = new DateTime( $vdt ); 
    $vexpiry=$d->format( 'Y-m-t' );
    
    $gstamt=0;
    
    $vdisamt=0;
    $vdisamt=($vscstamt*$disprchnt/100);
    $aftrdis=$vscstamt-$vdisamt;
    
    $gstamt=$aftrdis*$gst/100;
    
    $qmax=mysqli_fetch_array(mysqli_query($link,"select max(slno) as maxsl from inv_main_stock_received_detail_temp"));
    $vsl=$qmax['maxsl']+1;
    
	mysqli_query($link,"delete from inv_main_stock_received_detail_temp where order_no='$orderno' and item_id='$vitmid' and recept_batch='$batch' ");
	mysqli_query($link,"insert into inv_main_stock_received_detail_temp values('$vsl','$orderno','$billno','$vitmid','$mnufctr','$vexpiry','$vdate','$qnt','$freeqnt','$batch','$splrcode','$mrp','$vcstprice','$slprice','$vscstamt','$disprchnt','$vdisamt','$gst','$gstamt','$userid')");
	//mysqli_query($link,"update ph_item_master set gst_percent='$gst',strip_qnty='$pktqnt' where item_code='$vitmid'");
}

/////////////////////////////////////
elseif($type=="ph_ipd_credi_receipt") ///for ph ipd credit received
{

	$cid=$_POST['cid'];
	$date1=$_POST['date1'];
	$sbstrid=$_POST['sbstrid'];
	$discount=$_POST['discount'];
	$billno=$_POST['blno'];
	$billno=explode("@#",$billno);
	foreach($billno as $blno)
	{		
		if($blno)
		{
		    $blnonw=explode("%%",$blno);
		  		    
			   $qph_ipdpay_chk=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$blnonw[0]' and ipd_id='$cid' and balance>0"));
			   
			   //$qdetail=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,recept_cost_price,sale_price,gst_per from inv_main_stock_received_detail where item_id='$blnonw[0]' and recept_batch='$blnonw[2]' order by slno desc limit 1"));
			  // $vitmamt=$blnonw[1]*$qdetail['recept_cost_price'];
			   
			   if($qph_ipdpay_chk)
			   {
				   $vdiscount=0;
				   $vnwpaid=0;
				   if($discount>0)
				   {
					   if($qph_ipdpay_chk['balance']>$discount)
					   {
						   $vnwpaid=$blnonw[1]-$discount;
						   $vdiscount=$discount;
						   $discount=0;
					   }
					   else
					   {
						   $vnwpaid=$blnonw[1];
					   }
			       }
			       else
			       {
					   $vnwpaid=$blnonw[1];
				   }
			       
				   //$vpaidamt=$qph_ipdpay_chk['paid_amt']+$blnonw[1];
				   $vpaidamt=$qph_ipdpay_chk['paid_amt']+$vnwpaid;
				   //$vbal=$qph_ipdpay_chk['balance']-$blnonw[1];
				   $vbal=0;
			       mysqli_query($link,"update ph_sell_master set paid_amt='$vpaidamt',discount_amt='$vdiscount',balance='$vbal' where bill_no='$blnonw[0]' and ipd_id='$cid'  ");
			       
			       mysqli_query($link,"delete from ph_payment_details where bill_no='$blnonw[0]' and entry_date='$date1' and type_of_payment='B' and substore_id='$sbstrid'");
			       
			       mysqli_query($link,"insert into ph_payment_details (`bill_no`, `substore_id`, `entry_date`, `amount`, `payment_mode`, `check_no`, `type_of_payment`, `user`, `time`) values('$blnonw[0]','$qph_ipdpay_chk[substore_id]','$date1','$vnwpaid','Cash','','B','$userid','$time')");
			       
			   
			   }
			   
		}
	}
	
}

/////////////////////////////////////
//////////////////////////////////////////////
if($_POST["type"]=="purchase_rcv_final")
{
	
	$orderno=$_POST['orderno'];
	$splrblno=$_POST['splrblno'];
	$spplrid=$_POST['spplrid'];
	$gstamt=$_POST['gstamt'];
	$billamt=$_POST['billamt'];
	$disamt=$_POST['disamt'];
	$ttlamt=$_POST['ttlamt'];
	$bildate=$_POST['bildate'];
	$rad=$_POST['rad'];
	$adj=$_POST['adj'];
	$btn_val=$_POST['btn_val'];
	
	$itm=$_POST['itm'];
	
	$entrydate=date('Y-m-d');
	
	$fid=0;
	$ttlamt=round($ttlamt);
	if($btn_val=="Done")
	{
		mysqli_query($link,"delete from inv_main_stock_received_detail where order_no ='$orderno' and bill_no='$splrblno' ");
		mysqli_query($link,"insert into inv_main_stock_received_detail(`order_no`,`bill_no`,`item_id`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) select `order_no`,`bill_no`,`item_id`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount` from inv_main_stock_received_detail_temp where order_no ='$orderno' and bill_no='$splrblno' and user='$userid' ");
		
		
		
		
		
		mysqli_query($link,"delete from inv_main_stock_received_master where  receipt_no ='$orderno' and supplier_code='$qsuplrcode[SuppCode]' ");
		mysqli_query($link,"insert into inv_main_stock_received_master(`receipt_no`,`bill_date`,`recpt_date`,`bill_amount`,`gst_amt`,`dis_amt`,`net_amt`,`supp_code`,`bill_no`,`user`,`time`,`adjust_type`,`adjust_amt`) values('$orderno','$bildate','$entrydate','$billamt','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','$userid','$time','$rad','$adj')");
		
		//////////end mrp///////////////////////////
		mysqli_query($link,"delete from inv_main_stock_received_detail_temp where order_no ='$orderno' and bill_no='$splrblno' and user='$userid' ");
		////////////For stock''''''''''''''''''''
			
		$qrstk=mysqli_query($link,"select * from inv_main_stock_received_detail  where order_no ='$orderno'  and bill_no='$splrblno' and SuppCode='$spplrid' order by item_id");
		
		while($qrstk1=mysqli_fetch_array($qrstk))
		{
		  $vqnt=$qrstk1['recpt_quantity']+$qrstk1['free_qnt'];
				 
		   $vst=0;
		   //mysqli_query($link,"UPDATE `ph_purchase_order_details` SET `bl_qnt`='$vbalqnt', `stat`='1' WHERE order_no='$orderno' and item_code='$qrstk1[item_code]'");
			$vstkqnt=0;
			$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$entrydate' and  item_id='$qrstk1[item_id]' and  batch_no='$qrstk1[recept_batch]' order by  date desc "));
			if($qrstkmaster['item_id']!='')
			{
				$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
				$rcvqnt=$qrstkmaster['recv_qnty']+$vqnt;
				mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$rcvqnt' where  date='$entrydate' and item_id='$qrstk1[item_id]' and  batch_no='$qrstk1[recept_batch]'");
				mysqli_query($link,"delete from inv_maincurrent_stock where  item_id='$qrstk1[item_id]' and batch_no='$qrstk1[recept_batch]'");
				mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$qrstk1[item_id]','$qrstk1[recept_batch]','$vstkqnt','$qrstk1[expiry_date]')");
			}
			else///for if data not found
			{
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  item_id='$qrstk1[item_id]' and  batch_no='$qrstk1[recept_batch]' order by slno desc"));
				$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
				mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$qrstk1[item_id]','$qrstk1[recept_batch]','$entrydate','$qrstkmaster[closing_qnty]','$vqnt',0,'$vstkqnt')");
				mysqli_query($link,"delete from inv_maincurrent_stock where  item_id='$qrstk1[item_id]' and batch_no='$qrstk1[recept_batch]'");
				mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$qrstk1[item_id]','$qrstk1[recept_batch]','$vstkqnt','$qrstk1[expiry_date]')");
			}
		}
		
		mysqli_query($link,"update inv_purchase_order_master set stat=1 where order_no='$orderno' and supplier_id='$spplrid'");
	}
	else if($btn_val=="Update")
	{
	  /*
		//echo "poppipoipoi";
		//mysqli_query($link,"delete from purchase_receipt_details where order_no ='$orderno' and bill_no='$splrblno' ");
		//mysqli_query($link,"insert into ph_purchase_receipt_details select * from ph_purchase_receipt_temp where order_no ='$orderno' and bill_no='$splrblno' ");
		
		$q1=mysqli_query($link,"select * from ph_purchase_receipt_temp where bill_no='$splrblno' and order_no ='$orderno' and SuppCode='$spplrid'");
		while($q2=mysqli_fetch_array($q1))
		{
			$rcp_qnt_t=$q2['recpt_quantity'];	// ph_purchase_receipt_temp
			$fre_qnt_t=$q2['free_qnt'];
			$cr_qnt=$rcp_qnt_t+$fre_qnt_t;

			$q3=mysqli_fetch_array(mysqli_query($link,"select * from ph_purchase_receipt_details where bill_no='$splrblno' and order_no ='$orderno' and SuppCode='$spplrid' and item_code='$q2[item_code]' and recept_batch='$q2[recept_batch]'"));
			
			if($q3)
			{
				$rcp_qnt=$q3['recpt_quantity'];		// ph_purchase_receipt_details
				$fre_qnt=$q3['free_qnt'];
				$old_qnt=$rcp_qnt+$fre_qnt;
				
				
				if($cr_qnt>$old_qnt)
				{
					$f_qnt=$rcp_qnt_t-$rcp_qnt;
					$f_free=$fre_qnt_t-$fre_qnt;
					
					$final_qnt=$f_qnt+$rcp_qnt;
					$final_free=$f_free+$fre_qnt;
				}
				else if($cr_qnt<$old_qnt)
				{
					$f_qnt=$rcp_qnt_t;
					$f_free=$fre_qnt_t;
					
					$final_qnt=$f_qnt;
					$final_free=$f_free;
				}
				if($rcp_qnt!=$rcp_qnt_t)
				{
					mysqli_query($link,"update ph_purchase_receipt_details set recpt_quantity='$final_qnt', free_qnt='$final_free', recpt_mrp='$q2[recpt_mrp]', recept_cost_price='$q2[recept_cost_price]', sale_price='$q2[sale_price]', item_amount='$q2[item_amount]', dis_per='$q2[dis_per]', dis_amt='$q2[dis_amt]', gst_per='$q2[gst_per]', gst_amount='$q2[gst_amount]' where bill_no='$splrblno' and order_no ='$orderno' and SuppCode='$spplrid' and item_code='$q2[item_code]' and recept_batch='$q2[recept_batch]'");
					
					$stk_mas=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_master where item_code='$q2[item_code]' and batch_no='$q2[recept_batch]'"));
					$stk_prs=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where process_no='$orderno' and item_code='$q2[item_code]' and batch_no='$q2[recept_batch]' and substore_id='1' "));
					
					$stk_added=$stk_prs['added'];
					$stk_qnt=$stk_mas['quantity'];
					$vdifqnt=$cr_qnt-$old_qnt;
					if($vdifqnt>0)
					{
						$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$entrydate' and item_code='$q2[item_code]' and  batch_no='$q2[recept_batch]' and substore_id='1' order by  date desc "));
						if($qrstkmaster['item_code']!='')
						{
							$vstkqnt=$qrstkmaster['s_remain']+$vdifqnt;
							$rcvqnt=$qrstkmaster['added']+$vdifqnt;
							mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',added='$rcvqnt' where  date='$entrydate' and item_code='$q2[item_code]' and  batch_no='$q2[recept_batch]' and substore_id='1'");
							mysqli_query($link,"delete from ph_stock_master where  item_code='$q2[item_code]' and batch_no='$q2[recept_batch]'");
							mysqli_query($link,"insert into ph_stock_master(item_code,batch_no,quantity,mfc_date,exp_date) values('$q2[item_code]','$q2[recept_batch]','$vstkqnt','$q2[manufactre_date]','$q2[expiry_date]')");
						}
						else///for if data not found
						{
							$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$q2[item_code]' and  batch_no='$q2[recept_batch]' order by slno desc"));
							$vstkqnt=$qrstkmaster['s_remain']+$vdifqnt;
							mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('UPDATE','$q2[item_code]','$q2[recept_batch]','$qrstkmaster[s_remain]','$vdifqnt',0,'$vstkqnt','$entrydate')");
							mysqli_query($link,"delete from ph_stock_master where item_code='$q2[item_code]' and batch_no='$q2[recept_batch]'");
							mysqli_query($link,"insert into ph_stock_master(item_code,batch_no,quantity,mfc_date,exp_date) values('$q2[item_code]','$q2[recept_batch]','$vstkqnt','$q2[manufactre_date]','$q2[expiry_date]')");
						}
					}
					else
					{
						$vdifqnt=$old_qnt-$cr_qnt;
						$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$entrydate' and item_code='$q2[item_code]' and  batch_no='$q2[recept_batch]' order by  date desc "));
						if($qrstkmaster['item_code']!='')
						{
							$vstkqnt=$qrstkmaster['s_remain']-$vdifqnt;
							$rcvqnt=$qrstkmaster['added']-$vdifqnt;
							mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',added='$rcvqnt' where  date='$entrydate' and item_code='$q2[item_code]' and  batch_no='$q2[recept_batch]'");
							mysqli_query($link,"delete from ph_stock_master where  item_code='$q2[item_code]' and batch_no='$q2[recept_batch]'");
							mysqli_query($link,"insert into ph_stock_master(item_code,batch_no,quantity,mfc_date,exp_date) values('$q2[item_code]','$q2[recept_batch]','$vstkqnt','$q2[manufactre_date]','$q2[expiry_date]')");
						}
						else///for if data not found
						{
							$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$q2[item_code]' and  batch_no='$q2[recept_batch]' order by slno desc"));
							$vstkqnt=$qrstkmaster['s_remain']-$vdifqnt;
							mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('UPDATE','$q2[item_code]','$q2[recept_batch]','$qrstkmaster[s_remain]','$vdifqnt',0,'$vstkqnt','$entrydate')");
							mysqli_query($link,"delete from ph_stock_master where item_code='$q2[item_code]' and batch_no='$q2[recept_batch]'");
							mysqli_query($link,"insert into ph_stock_master(item_code,batch_no,quantity,mfc_date,exp_date) values('$q2[item_code]','$q2[recept_batch]','$vstkqnt','$q2[manufactre_date]','$q2[expiry_date]')");
						}
					}
				}
				
			}
			else
			{
				mysqli_query($link,"INSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$q2[item_code]','$q2[manufactre_date]','$q2[expiry_date]','$q2[recpt_date]','$q2[recpt_quantity]','$q2[free_qnt]','$q2[recept_batch]','$q2[SuppCode]','$q2[recpt_mrp]','$q2[recept_cost_price]','$q2[sale_price]','0','$q2[item_amount]','$q2[dis_per]','$q2[dis_amt]','$q2[gst_per]','$q2[gst_amount]')");
				
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$entrydate' and item_code='$q2[item_code]' and  batch_no='$q2[recept_batch]' order by  date desc "));
				if($qrstkmaster['item_code']!='')
				{
					$vstkqnt=$qrstkmaster['s_remain']+$cr_qnt;
					$rcvqnt=$qrstkmaster['added']+$cr_qnt;
					mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',added='$rcvqnt' where  date='$entrydate' and item_code='$q2[item_code]' and  batch_no='$q2[recept_batch]'");
					mysqli_query($link,"delete from ph_stock_master where  item_code='$q2[item_code]' and batch_no='$q2[recept_batch]'");
					mysqli_query($link,"insert into ph_stock_master(item_code,batch_no,quantity,mfc_date,exp_date) values('$q2[item_code]','$q2[recept_batch]','$vstkqnt','$q2[manufactre_date]','$q2[expiry_date]')");
				}
				else///for if data not found
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$q2[item_code]' and  batch_no='$q2[recept_batch]' order by slno desc"));
					$vstkqnt=$qrstkmaster['s_remain']+$cr_qnt;
					
					mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('UPDATE','$q2[item_code]','$q2[recept_batch]','$qrstkmaster[s_remain]','$cr_qnt',0,'$vstkqnt','$entrydate')");
					mysqli_query($link,"delete from ph_stock_master where item_code='$q2[item_code]' and batch_no='$q2[recept_batch]'");
					mysqli_query($link,"insert into ph_stock_master(item_code,batch_no,quantity,mfc_date,exp_date) values('$q2[item_code]','$q2[recept_batch]','$vstkqnt','$q2[manufactre_date]','$q2[expiry_date]')");
				}
			}
		}
		//echo "update ph_purchase_receipt_master set bill_amount='$billamt', gst_amt='$gstamt', dis_amt='$disamt', net_amt='$ttlamt'  where bill_no='$splrblno' and order_no ='$orderno' and supp_code='$spplrid'";
		mysqli_query($link,"update ph_purchase_receipt_master set bill_amount='$billamt', gst_amt='$gstamt', dis_amt='$disamt', net_amt='$ttlamt', `adjust_type`='$rad', `adjust_amt`='$adj' where bill_no='$splrblno' and order_no ='$orderno' and supp_code='$spplrid'");
		mysqli_query($link,"delete from ph_purchase_receipt_details where order_no ='$orderno' and bill_no='$splrblno' and SuppCode='$spplrid'");
		mysqli_query($link,"insert into ph_purchase_receipt_details select * from ph_purchase_receipt_temp where order_no ='$orderno' and bill_no='$splrblno' and SuppCode='$spplrid'");
		mysqli_query($link,"delete from ph_purchase_receipt_temp where order_no ='$orderno' and bill_no='$splrblno' ");
		*/
	}
}
////////////////////////////

if($_POST["type"]=="saletemp")
{
	$bill=$_POST['billno'];
	$entrydate=$_POST['entrydate'];
	$itmcode=$_POST['itmcode'];
	$batchno=$_POST['batchno'];
	$expiry=$_POST['expiry'];
	$quantity=$_POST['quantity'];
	$mrp=$_POST['rate'];
	$fid=0;
	
	$vt=mysqli_fetch_array(mysqli_query($link,"select gst,mrp from `item_master` where `item_id`='$itmcode'"));
	$vcstprice=mysqli_fetch_array(mysqli_query($link,"select recept_cost_price,sale_price from `ph_purchase_receipt_details` where `item_code`='$itmcode' and recept_batch='$batchno' order by recpt_date desc limit 0,1"));
	
	$vslprice=0;
	
	$pp=0;
	if($vcstprice['sale_price']=="")
	{
		
			$vslprice1=$vt['mrp']-($vt['mrp']*(100/(100+$vt['gst'])));
			$vslprice=round($vt['item_mrp']-$vslprice1,2);
			$pp=1;
	
	}
	else
	{
		$vslprice=$vcstprice['sale_price'];
		$recpt=$vcstprice['recept_cost_price'];
	}
	
	
	   $slprice1=0;	
	   $slprice=0;
	   $slprice1=$mrp-($mrp*(100/(100+$vt['gst'])));
	   $slprice=$mrp-$slprice1;
	   $vslprice=$slprice;
    
    
	$vttlamt=val_con($quantity*$mrp);
	$vttlcstprc=val_con($quantity*$recpt);
	$gstamt=$vttlamt-($vttlamt*(100/(100+$vt['gst'])));
	
	mysqli_query($link,"delete from ph_sell_details_temp where bill_no='$bill' and item_code='$itmcode' and batch_no='$batchno' and user='$userid'");
	
	$qchqnty=mysqli_fetch_array(mysqli_query($link,"select quantity  from ph_stock_master  where  item_code ='$itmcode' and batch_no  ='$batchno' and substore_id ='1'"));
	if($qchqnty['quantity']>=$quantity)
	{
		echo "0";
	
	 mysqli_query($link,"insert into ph_sell_details_temp values('$bill','$entrydate','$itmcode','$batchno','$expiry','$quantity','$free','$mrp','$vttlamt','$vttlamt','$vt[gst]','$fid','$gstamt','$recpt','$vslprice','$userid')");
    }
    else
    {
		echo $qchqnty['quantity'];
	}
	
	//mysqli_query($link,"insert into ph_sell_details_temp values('$bill','$entrydate','$itmcode','$batchno','$expiry','$quantity','$free','$mrp','$vttlamt','$vttlamt','$vt[gst]','$fid','$gstamt','$recpt','$vslprice','$userid')");
 
   $dt=mysqli_fetch_array(mysqli_query($link,"SELECT distinct entry_date FROM ph_sell_details_temp where  bill_no='$bill'  order by entry_date asc "));
   mysqli_query($link,"update ph_sell_details_temp set entry_date='$dt[entry_date]' where  bill_no='$bill' ");
   //echo $vslprice."-".$pp;
}
/////////////////////////////////////
elseif($type=="phitmrtrn1111") ///for item return from Customer
{

	$pbillno=$_POST['pbillno'];
	$date1=$_POST['date1'];
	$billno=$_POST['bildtl'];
	$reason=$_POST['reason'];
	$crdtno=$_POST['crdtno'];
	
	$billno=explode("@#",$billno);
	foreach($billno as $blno)
	{		
		if($blno)
		{
		   $blnonw=explode("%%",$blno);
		   mysqli_query($link,"insert into ph_item_return_master values('$pbillno','$blnonw[0]','$blnonw[2]','$date1','$blnonw[1]','$reason','$_SESSION[emp_id]')");
		   
		   $qstk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_master where item_code='$blnonw[0]' and batch_no ='$blnonw[2]'"));
		   $vstkqnty=$qstk['quantity']+$blnonw[1];
		   mysqli_query($link,"update ph_stock_master set quantity='$vstkqnty' where item_code='$blnonw[0]' and batch_no ='$blnonw[2]'");
		   $q1=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$blnonw[0]' and batch_no='$blnonw[2]' and date='$date1'"));
		   if($q1)
		    {
				 $vadded=$q1['added']+$blnonw[1];
				 mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnty',added='$vadded' where item_code='$blnonw[0]' and batch_no ='$blnonw[2]' and date='$date1'");
			}
			else
			{
			}
		  
		}
	}
	mysqli_query($link,"delete from ph_item_return_creditnote where bill_no='$pbillno'");
	mysqli_query($link,"insert into ph_item_return_creditnote value('$pbillno','$crdtno','$date1')");
	
}


////////////////////////////////
elseif($type=="ph_itemretrun_to_store_tmp") //for  Item Return To Supplier temp
{
	$date=$_POST['date'];
	$returnno=$_POST['returnno'];
	$reason=$_POST['reason'];
	$itmcode=$_POST['itmcode'];
	$btchno=$_POST['btchno'];
	$rtrnqnt=$_POST['rtrnqnt'];
	$expiry=$_POST['expiry'];
	
	$itmcode1=explode("-#",$itmcode);
	
	$qchk=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,recept_cost_price,gst_per from ph_purchase_receipt_details where  item_code='$itmcode1[1]' and recept_batch='$btchno' order by slno desc "));
	$i=1;	
	$vitmamt=$qchk['recept_cost_price']*$rtrnqnt;
	$gstamt=$vitmamt*$qchk['gst_per']/100;
	
	$qmax=mysqli_fetch_array(mysqli_query($link,"select max(slno) as maxsl from ph_item_return_store_detail_temp where 	returnr_no='$returnno'"));
	$i=$qmax['maxsl']+1;
	
	mysqli_query($link,"delete from ph_item_return_store_detail_temp where returnr_no ='$returnno' and  item_id='$itmcode1[1]' and  batch_no='$btchno' and  user='$userid'");
	mysqli_query($link,"insert into ph_item_return_store_detail_temp values('$i','$returnno','$reason','$itmcode1[1]','$expiry','$date','$rtrnqnt',0,'$btchno','$qchk[recpt_mrp]','$qchk[recept_cost_price]','$vitmamt',0,0,'$qchk[gst_per]','$gstamt','$userid')");
	
	
}

//////////////////////////////////////////////
if($type=="ph_item_retrunto_store_final")
{
	
	$date=$_POST['date'];
	$returnno=$_POST['returnno'];
	$reason=$_POST['reason'];
	
	
	mysqli_query($link,"delete from ph_item_return_store_detail where returnr_no ='$issueno'  ");
	mysqli_query($link,"insert into ph_item_return_store_detail( `returnr_no`, `reason`, `item_id`, `expiry_date`, `date`, `quantity`, `free_qnt`, `batch_no`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`,`user`) select `returnr_no`, `reason`, `item_id`, `expiry_date`, `date`, `quantity`, `free_qnt`, `batch_no`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`,`user` from ph_item_return_store_detail_temp where returnr_no ='$returnno'  and user='$userid' ");
	
		
	mysqli_query($link,"delete from ph_item_return_store_detail_temp where returnr_no ='$returnno'  ");
	mysqli_query($link,"delete from ph_item_return_store_master where returnr_no ='$returnno'  ");
	mysqli_query($link,"insert into ph_item_return_store_master(`returnr_no`,  `date`, `stat`, `del`, `user`, `time`) values('$returnno','$date',0,0,'$userid','$time')");
	
	//////////end mrp///////////////////////////
	
	////////////For stock''''''''''''''''''''
		
	$qrstk=mysqli_query($link,"select * from ph_item_return_store_detail  where returnr_no ='$returnno'  order by item_id");
		
	while($qrstk1=mysqli_fetch_array($qrstk))
	{
		$vqnt=$qrstk1['quantity'];
		$vitmamt=0;
		$vst=0;
		$vstkqnt=0;
		$gstamt=0;
	 	
	 	
		$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$date' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='1'  order by  date desc "));
		if($qrstkmaster['item_code']!='')
		{
			$vstkqnt=$qrstkmaster['s_remain']-$vqnt;
			$rtrnqnt=$qrstkmaster['return_supplier']+$vqnt;
			
			mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',return_supplier='$rtrnqnt' where  date='$date' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='1' ");
						
			mysqli_query($link,"update ph_stock_master set  quantity='$vstkqnt' where item_code='$qrstk1[item_id]' and  batch_no='$qrstk1[batch_no]' and substore_id='1'");
			
			
		}
		else///for if data not found
		{
			$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$qrstk1[item_id]' and  batch_no='$qrstk1[batch_no]' and substore_id='1'  order by slno desc limit 0,1"));
			$vstkqnt=$qrstkmaster['s_remain']-$vqnt;
		  
			mysqli_query($link,"insert into ph_stock_process(`substore_id`, `process_no`, `item_code`, `batch_no`, `s_available`, `added`, `sell`, `return_cstmr`, `return_supplier`, `s_remain`, `date`) values('1','Return','$qrstk1[item_id]','$qrstk1[batch_no]','$qrstkmaster[s_remain]','0','0','0','$vqnt','$vstkqnt','$date')");
			mysqli_query($link,"update ph_stock_master set  quantity='$vstkqnt' where item_code='$qrstk1[item_id]' and  batch_no='$qrstk1[batch_no]' and substore_id='1'");
		}
		//////////store/////////////////////
		
		$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'  order by  date desc "));
		if($qrstkmaster['item_id']!='')
		{
			$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
			$rcvqnt=$qrstkmaster['recv_qnty']+$vqnt;
			
			mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$rcvqnt' where  date='$date' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
				
			
			mysqli_query($link,"delete from inv_maincurrent_stock where  item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'");
			mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$qrstk1[item_id]','$qrstk1[batch_no]','$vstkqnt','$qrstk1[expiry_date]')");
			
			//mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
			
		}
		else///for if data not found
		{
			$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  item_id='$qrstk1[item_id]' and  batch_no='$qrstk1[batch_no]'   order by slno desc"));
			$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
			
			mysqli_query($link,"insert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) value('$qrstk1[item_id]','$qrstk1[batch_no]','$date','$qrstkmaster[closing_qnty]','$vqnt',0,'$vstkqnt') ");
			
			mysqli_query($link,"delete from inv_maincurrent_stock where  item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'");
			mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$qrstk1[item_id]','$qrstk1[batch_no]','$vstkqnt','$qrstk1[expiry_date]')");
			
			//mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
		}
		
		//////////////////
		
	}
}
////////////////////////////////////////////////////////////////////

if($type=="cust_credit") ////Balance Received Pharmacy
{
	$blno=$_POST['blno'];
	$ptype=$_POST['ptype'];
	$amtpaid=val_con($_POST['amtpaid']);
	$amtblnce=val_con($_POST['amtblnce']);
	$txtcrdtamt=val_con($_POST['txtcrdtamt']);
	$pymtdate=$_POST['pymtdate'];
	$chk_no=$_POST['chk_no'];
	$vpaid=0;
	
	mysqli_query($link,"DELETE FROM `ph_payment_details` WHERE `bill_no`='$blno' and `amount`='$txtcrdtamt' and `type_of_payment`='B' and `entry_date`='$pymtdate' and  user='$_SESSION[emp_id]'");
	mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode,check_no,type_of_payment,user,time) values('$blno','$pymtdate','$txtcrdtamt','$ptype','$chk_no','B','$_SESSION[emp_id]','$time')");
	$qpaid=mysqli_fetch_array(mysqli_query($link,"select total_amt,discount_amt,adjust_amt,paid_amt from ph_sell_master where bill_no='$blno'"));
	$vpaid=$qpaid['paid_amt']+$txtcrdtamt;
	$bal=$amtblnce-$txtcrdtamt;
	mysqli_query($link,"update ph_sell_master set paid_amt='$vpaid',balance='$bal' where bill_no='$blno'");
	
	if($qpaid['total_amt']<$vpaid)
	{
		$vttlpaid=$qpaid['total_amt']-$qpaid['discount_amt']-$qpaid['adjust_amt'];
		mysqli_query($link,"update ph_sell_master set paid_amt='$vttlpaid',balance=0 where bill_no='$blno'");
	}
	
}


//////////////////////////////////////
if($_POST["type"]=="salefinal")
{
	$billtype=$_POST['billtype'];
	$ref_by=$_POST['ref_by'];
	$pat_typ=$_POST['pat_typ'];
	$ptype=$_POST['ptype'];
	$blno=$_POST['blno'];
	$entrydate=$_POST['entrydate'];
	$ttlamt=val_con($_POST['net']);
	$discountprcnt=val_con($_POST['discountprcnt']);
	$aftrdscnt=val_con($_POST['aftrdscnt']);
	$paidamont=val_con($_POST['paidamont']);
	$customername=$_POST['customername'];
	$customername= str_replace("'", "''", "$customername");
	//$customername=$_POST['customername'];
	$custphone=$_POST['custphone'];
	$disamt=$ttlamt-$aftrdscnt;	
	$balance=round($_POST['balance']);
	$uhid=$_POST['uhid'];
	$cabinno=$_POST['cabinno'];
	$adjustmt=$_POST['adjustmt'];
	$btnvalue=$_POST['btnvalue'];
	$ind_num=$_POST['ind_num'];
	$fid=0;
	
	$pat_type=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$uhid' "));
	if($pat_type["type"]==3)
	{
		$opd_id="";
		$ipd_id=$uhid;
		$pin=$uhid;
	}else
	{
		$pin=$uhid;
		$opd_id=$uhid;
		$ipd_id="";
	}
	
	$patient_id=$pat_type["patient_id"];
	
	if($billtype==2)	
	{
		$ptype=2;
	}
	else
	{
		$ptype=1;
	}
	
	$usr=$_POST['usr'];
	
	if($btnvalue=="Done")
	{
		
	    $billnos=100;
	    $date_str=explode("-", $date);
		$dis_year=$date_str[0];
		$dis_month=$date_str[1];
		$dis_year_sm=convert_date_only_sm_year($date);

		//$c_m_y=$dis_year."-".$dis_month;
		$c_m_y=$dis_year;
		$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`bill_no`) as tot FROM `ph_sell_master` WHERE `entry_date` like '$c_m_y%' "));
		$bill_num=$bill_no_qry["tot"];
	
		$bill_tot_num=$bill_num;

		if($bill_tot_num==0)
		{
			$bill_no=$billnos+1;
		}else
		{
			$bill_no=$billnos+$bill_tot_num+1;
		}
		
		$blno=$bill_no."/".$dis_year_sm;
	  
	  
	  
	  mysqli_query($link,"update ph_sell_details_temp set bill_no='$blno' where entry_date='$entrydate' and user='$userid'");
     }
     
	$num_r=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_no`='$blno'"));
	if($num_r>0)
	{
		$qchkbl=mysqli_fetch_array(mysqli_query($link,"select bill_no,entry_date from ph_sell_master where bill_no='$blno'"));
		$entrydate1=$qchkbl['entry_date'];
		if($qchkbl)
		{
		//---------------------------for existing bill stock update------------------------------//
			$qry=mysqli_query($link,"select * from ph_sell_details where bill_no='$blno' order by item_code,batch_no");
			while($qrupdt1=mysqli_fetch_array($qry))
			{
				$slqnt=0;
				$slqnt=$qrupdt1['sale_qnt']+$qrupdt1['free_qnt'];					
				$vstkqnt=0;
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$entrydate' and item_code='$qrupdt1[item_code]' and  batch_no='$qrupdt1[batch_no]' and  substore_id='1' order by slno desc limit 0,1"));
				if($qrstkmaster['item_code']!='')
				{
					$vchkqnt=0; 
					$vdif=0;
					$vrcvqnt=0;
					$qntchk=mysqli_fetch_array(mysqli_query($link,"select sale_qnt from ph_sell_details_temp where item_code='$qrupdt1[item_code]' and  batch_no='$qrupdt1[batch_no]' and bill_no='$blno'")); 
					$vchkqnt=$qntchk['sale_qnt'];
					
					$vsel=$qrstkmaster['sell']-$slqnt;
					$vstkqnt=$qrstkmaster['s_remain']+$slqnt;
					mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',sell='$vsel' where date='$entrydate' and item_code='$qrupdt1[item_code]' and  batch_no='$qrupdt1[batch_no]' and substore_id='1' and  slno='$qrstkmaster[slno]'");
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$qrupdt1[item_code]' and batch_no='$qrupdt1[batch_no]' and substore_id='1'");
				}
				else
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$qrupdt1[item_code]' and  batch_no='$qrupdt1[batch_no]' and substore_id='1' order by slno desc limit 0,1"));
					$vstkqnt=$qrstkmaster['s_remain']+$slqnt;

					mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('1','$blno','$qrupdt1[item_code]','$qrupdt1[batch_no]','$qrstkmaster[s_remain]','$slqnt',0,'$vstkqnt','$entrydate')");
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$qrupdt1[item_code]' and batch_no='$qrupdt1[batch_no]' and substore_id='1'");
				}
			}

			//----------------------end---------------------------------------//
			mysqli_query($link,"delete from ph_sell_details where  bill_no='$blno'");
			mysqli_query($link,"insert into ph_sell_details select bill_no,entry_date,item_code, batch_no,expiry_date,sale_qnt,free_qnt,mrp,total_amount,net_amount,gst_percent,FID,gst_amount,item_cost_price,sale_price from  ph_sell_details_temp where  user='$userid' and bill_no='$blno'");
			
			$qitmchk=mysqli_fetch_array(mysqli_query($link,"select item_code from ph_sell_details where bill_no='$blno'"));
			if($qitmchk['item_code'] !='')
			{
				$qchkno=mysqli_fetch_array(mysqli_query($link,"select ifnull(count(bill_no),0) as maxentry from ph_sell_master_edit where bill_no='$blno' "));
				$vno=$qchkno[maxentry]+1;
				mysqli_query($link,"insert into ph_sell_details_edit select a.*,'$vno' from  ph_sell_details a where a.bill_no='$blno'");
				
				mysqli_query($link,"UPDATE `ph_sell_master` SET `customer_name`='$customername',`customer_phone`='$custphone',`total_amt`='$ttlamt',`discount_perchant`='$discountprcnt',`discount_amt`='$disamt',`adjust_amt`='$adjustmt',`paid_amt`='$paidamont',`balance`='$balance',`bill_type_id`='$billtype',`patient_id`='$patient_id',`opd_id`='$opd_id',`ipd_id`='$ipd_id',`patient_type`='$ptype',`refbydoctorid`='$ref_by',`pat_type`='$pat_typ' WHERE `bill_no`='$blno'");
								
				
				mysqli_query($link,"INSERT INTO `ph_sell_master_edit`(bill_no,entry_date,customer_name,customer_phone,total_amt,discount_perchant,discount_amt,adjust_amt,paid_amt,balance,bill_type_id,patient_id,opd_id,ipd_id,patient_type,fid,refbydoctorid,pat_type,user,time,entry_no) VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$adjustmt','$paidamont','$balance','$billtype','$patient_id','$opd_id','$ipd_id','$ptype','$fid','$ref_by','$pat_typ','$userid','$time','$vno')");
				mysqli_query($link,"UPDATE `ph_payment_details` SET `amount`='$paidamont' WHERE `bill_no`='$blno'");
				mysqli_query($link,"delete from ph_payment_details where bill_no='$blno' and type_of_payment='B'");
				
				
				//mysqli_query($link,"delete from ph_payment_details where bill_no='$blno' and entry_date='$entrydate'");
				//mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode,check_no,type_of_payment,user,time) values('$blno','$entrydate','$paidamont','Cash','','A','$_SESSION[emp_id]','$time')");
			}
			//--------------------------For stock---------------------------//
			$qrstk=mysqli_query($link,"select * from ph_sell_details where bill_no='$blno' order by item_code");
			while($qrstk1=mysqli_fetch_array($qrstk))
			{
				$slqnt=$qrstk1['sale_qnt'];
				$freqnt=$qrstk1['free_qnt'];
				$vqnt=$slqnt+$freqnt;
				$vbtch=$qrstk1['batch_no'];
				$vstkqnt=0;
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$entrydate' and item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[batch_no]' and substore_id='1' order by slno desc limit 0,1"));
				if($qrstkmaster['item_code']!='')
				{
					$vsel=0;
					$vsel=$qrstkmaster['sell']+$slqnt;
					$vstkqnt=$qrstkmaster['s_remain']-$vqnt;				 
					mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',sell='$vsel' where date='$entrydate' and item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[batch_no]' and substore_id='1' and slno='$qrstkmaster[slno]'");
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[batch_no]' and substore_id='1'");
				}
				else///for if data not found
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[batch_no]' and substore_id='1' order by slno desc limit 0,1"));
					$vstkqnt=$qrstkmaster['s_remain']-$vqnt;
					mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('1','$blno','$qrstk1[item_code]','$qrstk1[batch_no]','$qrstkmaster[s_remain]','0','$vqnt','$vstkqnt','$entrydate')");
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[batch_no]' and substore_id='1' ");
				}
			}
		}
	}
//-------------------------------new entry----------------------------------------------//
	else
	{
		
		
		mysqli_query($link,"delete from ph_sell_master where bill_no='$blno'"); 
		mysqli_query($link,"INSERT INTO `ph_sell_master`(`bill_no`,`entry_date`,`customer_name`,`customer_phone`,`total_amt`,`discount_perchant`,`discount_amt`,`adjust_amt`,`paid_amt`,`balance`,`bill_type_id`,`patient_id`,`opd_id`,`ipd_id`,`patient_type`,`fid`,`refbydoctorid`,`pat_type`,`user`,`time` ) VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$adjustmt','$paidamont','$balance','$billtype','$patient_id','$opd_id','$ipd_id','$ptype','$fid','$ref_by','$pat_typ','$userid','$time')");
		
		mysqli_query($link,"delete from ph_sell_details where  bill_no='$blno'");
		//mysqli_query($link,"insert into ph_sell_details select bill_no,entry_date,item_code, batch_no,expiry_date,sale_qnt,free_qnt,mrp,total_amount,net_amount,gst_percent,FID,gst_amount,item_cost_price,sale_price from  ph_sell_details_temp where  user='$userid' and entry_date='$entrydate' and bill_no='$blno'");
		
		$qntry=mysqli_query($link,"select * from ph_sell_details_temp where  user='$userid' and entry_date='$entrydate' and bill_no='$blno' ");
		while($qntry1=mysqli_fetch_array($qntry))
		{
			mysqli_query($link,"delete from ph_sell_details where bill_no='$blno' and item_code='$qntry1[item_code]' and batch_no='$qntry1[batch_no]' ");
			mysqli_query($link,"insert into ph_sell_details values('$blno','$qntry1[entry_date]','$qntry1[item_code]','$qntry1[batch_no]','$qntry1[expiry_date]','$qntry1[sale_qnt]','$qntry1[free_qnt]','$qntry1[mrp]','$qntry1[total_amount]','$qntry1[net_amount]','$qntry1[gst_percent]','$qntry1[FID]','$qntry1[gst_amount]','$qntry1[item_cost_price]','$qntry1[sale_price]')");
		}
		
		
		mysqli_query($link,"insert into ph_sell_details_edit select bill_no,entry_date,item_code, batch_no,expiry_date,sale_qnt,free_qnt,mrp,total_amount,net_amount,gst_percent,FID,gst_amount,item_cost_price,sale_price,'1' from  ph_sell_details_temp where  user='$userid' and entry_date='$entrydate' and bill_no='$blno'");
		
			
		mysqli_query($link,"INSERT INTO `ph_sell_master_edit`(bill_no,entry_date,customer_name,customer_phone,total_amt,discount_perchant,discount_amt,adjust_amt,paid_amt,balance,bill_type_id,patient_id,opd_id,ipd_id,patient_type,fid,refbydoctorid,pat_type,user,time,entry_no) VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$adjustmt','$paidamont','$balance','$billtype','$patient_id','$opd_id','$ipd_id','$ptype','$fid','$ref_by','$pat_typ','$userid','$time','1')");
		
		mysqli_query($link,"delete from ph_payment_details where  bill_no='$blno'");
		mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode,check_no,type_of_payment,user,time) values('$blno','$entrydate','$paidamont','Cash','','A','$userid','$time')");
		//---------------------------------For stock-------------------------------------//
		$qry=mysqli_query($link,"select * from ph_sell_details where bill_no='$blno' order by item_code");
		while($run=mysqli_fetch_array($qry))
		{
			$slqnt=$run['sale_qnt'];
			$freqnt=$run['free_qnt'];
			$vqnt=$slqnt+$freqnt;
			$vbtch=$run['batch_no'];
			
			mysqli_query($link,"update patient_medicine_detail set status='$slqnt', bill_no='$blno' where  item_code='$run[item_code]' and patient_id='$patient_id' and pin='$pin' and indent_num='$ind_num'");	
			
			$vstkqnt=0;
			
			$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$entrydate' and item_code='$run[item_code]' and  batch_no='$run[batch_no]' and substore_id='1' order by slno desc limit 0,1"));
			if($stk['item_code']!="")
			{
				$vstkqnt=$stk['s_remain']-$vqnt;
				$slqnt=$stk['sell']+$vqnt;
				mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',sell='$slqnt' where  date='$entrydate' and item_code='$run[item_code]' and  batch_no='$run[batch_no]' and substore_id='1' and slno='$stk[slno]'");
				mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$run[item_code]' and  batch_no='$run[batch_no]' and substore_id='1' ");
			}
			else // for if data not found
			{
				$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$run[item_code]' and  batch_no='$run[batch_no]' and substore_id='1' order by slno desc limit 0,1"));
				$vstkqnt=$stk['s_remain']-$vqnt;
				mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('1','$blno','$run[item_code]','$run[batch_no]','$stk[s_remain]','0','$vqnt','$vstkqnt','$entrydate')");
				mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$run[item_code]' and batch_no='$run[batch_no]' and substore_id='1'");
			}
		}
		mysqli_query($link,"delete from ph_sell_details_temp where  user='$userid'");
	
		
	}
//-----------------if patient's uhid exists---------------------------//
	
//--------------------------------------------------------------------//
  $vitmttl=0;
  $q1=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amount),0) as maxamt from ph_sell_details where bill_no='$blno' "));
  $q2=mysqli_fetch_array(mysqli_query($link,"select total_amt,discount_amt,adjust_amt,paid_amt,balance from ph_sell_master where bill_no='$blno' "));
  $vitmttl=round($q1['maxamt']);
  if($q2['total_amt']!=$vitmttl)
  {
	  $vbal=$vitmttl-$q2['discount_amt']-$q2['paid_amt']-$q2['adjust_amt'];
	  mysqli_query($link,"UPDATE `ph_sell_master` SET `total_amt`='$vitmttl',`balance`='$vbal' WHERE `bill_no`='$blno'");
	  mysqli_query($link,"UPDATE `ph_payment_details` SET `amount`='$q2[paid_amt]' WHERE `bill_no`='$blno' and type_of_payment='A'");
	  mysqli_query($link,"UPDATE `ph_payment_details` SET `amount`='0' WHERE `bill_no`='$blno' and type_of_payment='B'");
  }
  
	if($billtype==1)
	{
		
	  $vbal=0;
	  $vpaid=$vitmttl-$q2['discount_amt']-$q2['adjust_amt'];
	  mysqli_query($link,"UPDATE `ph_sell_master` SET total_amt='$vitmttl',`paid_amt`='$vpaid',`balance`='$vbal' WHERE `bill_no`='$blno'");
	  mysqli_query($link,"UPDATE `ph_payment_details` SET `amount`='$vpaid' WHERE `bill_no`='$blno' and type_of_payment='A'");
	  mysqli_query($link,"delete from `ph_payment_details`  WHERE `bill_no`='$blno' and type_of_payment='B'");
		
	}
	
   if($q2['balance']<0)
  {
	  mysqli_query($link,"UPDATE `ph_sell_master` SET `balance`=0 WHERE `bill_no`='$blno'"); 
  }
  

	echo $blno;
	
}

/////////////////////////////////////
elseif($type=="phbalancercv") ///for indent substore rcv
{

	$cid=$_POST['cid'];
	$date1=$_POST['date1'];
	$sbstrid=$_POST['sbstrid'];
	$billno=$_POST['blno'];
	$billno=explode("@#",$billno);
	foreach($billno as $blno)
	{		
		if($blno)
		{
		   $blnonw=explode("%%",$blno);
		   
		   mysqli_query($link,"insert into ph_payment_details(bill_no,entry_date,amount,payment_mode,check_no,payment_type) values('$cid','$blnonw[0]','$date1','$sbstrid','$blnonw[1]',0)");
		   mysqli_query($link,"update inv_substore_order_details set stat='1' where order_no='$cid' and item_code='$blnonw[0]' and substore_id='$sbstrid'");
		}
	}
	
	$qchk=mysqli_fetch_array(mysqli_query($link,"select item_code from inv_substore_order_details where order_no='$cid' and substore_id='$sbstrid' and stat='0' "));
	if(!$qchk)
	{
		mysqli_query($link,"update inv_substore_indent_order_master set status='1' where order_no='$cid' and substore_id='$sbstrid' ");
	}
}


?>

