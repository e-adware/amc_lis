<?php
include'../../includes/connection.php';
$date=date("Y-m-d");
$time=date('h:i:s A');

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

function text_query($txt)
{
	if($txt)
	{
		$myfile = file_put_contents('log/item_issue.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
}

$type=$_POST['type'];

if($type==1)
{
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th>
			<th>Issue No</th>
			<th>Issue To</th>
			<th>Date Time</th>
			<th>Item</th>
			<th>Batch No</th>
			<th>Quantity</th>
			<th>Receive</th>
		</tr>
		<?php
		$i=1;
		$qry=mysqli_query($link,"SELECT DISTINCT `order_no` FROM `inv_mainstore_issue_details_cnf` WHERE `status`='0'");
		while($row=mysqli_fetch_assoc($qry))
		{
			$order_qry=mysqli_query($link,"SELECT * FROM `inv_mainstore_issue_details_cnf` WHERE `order_no`='$row[order_no]' AND `status`='0'");
			$num=mysqli_num_rows($order_qry);
			$j=0;
			while($r=mysqli_fetch_assoc($order_qry))
			{
				$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
			?>
			<tr class="cnf tr<?php echo $r['order_no'];?>">
				<?php
				if($num>0)
				{
				?>
				<td rowspan="<?php echo $num;?>"><?php echo $i;?></td>
				<td rowspan="<?php echo $num;?>"><?php echo $row['order_no'];?></td>
				<td rowspan="<?php echo $num;?>"><?php echo $r['issue_to'];?></td>
				<td rowspan="<?php echo $num;?>"><?php echo convert_date($r['issue_date'])." ".convert_time($r['time']);?></td>
				<?php
				}
				?>
				<!--<td>
					<label><input type="checkbox" class="chk chk<?php echo $r['order_no'];?>" value="<?php echo $r['item_id'].'@@'.$r['batch_no'];?>" onclick="chk_btnn('<?php echo $r['order_no'];?>')" /></label>
				</td>-->
				<td>
					<input type="hidden" class="itm<?php echo $r['order_no'];?>" value="<?php echo $r['item_id'];?>" />
					<?php echo $itm['item_name'];?>
				</td>
				<td>
					<input type="hidden" class="bch<?php echo $r['order_no'];?>" value="<?php echo $r['batch_no'];?>" />
					<?php echo $r['batch_no'];?>
				</td>
				<td>
					<?php echo $r['issue_qnt'];?>
				</td>
				<?php
				if($num>0)
				{
				?>
				<td rowspan="<?php echo $num;?>">
					<button type="button" class="btn btn-mini btn-success" id="btn<?php echo $r['order_no'];?>" onclick="rcv_confirm('<?php echo $r['order_no'];?>')">Receive</button>
				</td>
				<?php
				}
				?>
			</tr>
			<?php
			$num=0;
			$j++;
			}
		$i++;
		}
		?>
	</table>
	<style>
	.table
	{
		background:#FFFFFF;
	}
	.table tr:hover
	{
		background:none;
	}
	tr.cnf td
	{
		display:none;
	}
	</style>
	<?php
}

if($type==2)
{
	$ord=$_POST['ord'];
	$all=$_POST['all'];
	$user=$_POST['user'];
	$al=explode("#@#",$all);
	$result="2";
	foreach($al as $a)
	{
		$vl=explode("@@",$a);
		$itm=$vl[0];
		$bch=$vl[1];
		if($itm && $bch)
		{
			$qrstk1=mysqli_fetch_assoc(mysqli_query($link,"select * from inv_mainstore_issue_details_cnf where order_no='$ord' and substore_id='1' and item_id='$itm' and batch_no='$bch' and status='0'"));
			$txt="\nselect * from inv_mainstore_issue_details_cnf where order_no='$ord' and substore_id='1' and item_id='$itm' and batch_no='$bch' and status='0'";
			if($qrstk1)
			{
				$qdetails=mysqli_fetch_array(mysqli_query($link,"select * from inv_main_stock_received_detail where item_id='$qrstk1[item_id]' and recept_batch='$qrstk1[batch_no]' order by slno desc"));
				$txt.="\nselect * from inv_main_stock_received_detail where item_id='$qrstk1[item_id]' and recept_batch='$qrstk1[batch_no]' order by slno desc";
				$vitmamt=$qrstk1['issue_qnt']*$qdetails['recept_cost_price'];
	 	
				mysqli_query($link,"insert into ph_purchase_receipt_details(`order_no`,`bill_no`,`item_code`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`fid`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$ord','$ord','$qrstk1[item_id]','0000-00-00','$qdetails[expiry_date]','$date','$qrstk1[issue_qnt]',0,'$qrstk1[batch_no]','','$qdetails[recpt_mrp]','$qdetails[recept_cost_price]','$qdetails[sale_price]',0,'$vitmamt',0,0,'$qdetails[gst_per]','$gstamt')");
				$txt.="\ninsert into ph_purchase_receipt_details(`order_no`,`bill_no`,`item_code`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`fid`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$ord','$ord','$qrstk1[item_id]','0000-00-00','$qdetails[expiry_date]','$date','$qrstk1[issue_qnt]',0,'$qrstk1[batch_no]','','$qdetails[recpt_mrp]','$qdetails[recept_cost_price]','$qdetails[sale_price]',0,'$vitmamt',0,0,'$qdetails[gst_per]','$gstamt')";
				
				$qsubstr1=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='1' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and date='$date' "));
				$txt.="\nselect * from ph_stock_process where substore_id='1' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and date='$date' ";
					if($qsubstr1['item_code']!='')
					{
						$vsubsrstkqnt=$qsubstr1['s_remain']+$qrstk1['issue_qnt'];
						$vsubsrrcvqnt=$qsubstr1['added']+$qrstk1['issue_qnt'];
						mysqli_query($link,"update ph_stock_process set s_remain='$vsubsrstkqnt',added='$vsubsrrcvqnt' where date='$date' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='1' ");
						$txt.="\nupdate ph_stock_process set s_remain='$vsubsrstkqnt',added='$vsubsrrcvqnt' where date='$date' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='1' ";
						
						mysqli_query($link,"delete from ph_stock_master where item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='1'");
						$txt.="\ndelete from ph_stock_master where item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='1'";
						
						mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('1','$qrstk1[item_id]','$qrstk1[batch_no]','$vsubsrstkqnt','0000-00-00','$qdetails[expiry_date]') ");
						$txt.="\ninsert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('1','$qrstk1[item_id]','$qrstk1[batch_no]','$vsubsrstkqnt','0000-00-00','$qdetails[expiry_date]') ";
					}
					else
					{
						$vclqnt=0;
						$vopqnt=0;
						$qchk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='1' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' order by slno desc "));
						$txt.="\nselect * from ph_stock_process where substore_id='1' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' order by slno desc ";
						if($qchk)
						{
							$vopqnt=$qchk['s_remain'];
							$vclsnqnt=$qchk['s_remain']+$qrstk1['issue_qnt'];
						}
						else
						{
							$vclsnqnt=$qrstk1['issue_qnt'];
							
						}
						
						mysqli_query($link,"insert into ph_stock_process(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('1','Direct','$qrstk1[item_id]','$qrstk1[batch_no]','$qchk[s_remain]','$qrstk1[issue_qnt]',0,0,0,'$vclsnqnt','$date')");
						$txt.="\ninsert into ph_stock_process(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('1','Direct','$qrstk1[item_id]','$qrstk1[batch_no]','$qchk[s_remain]','$qrstk1[issue_qnt]',0,0,0,'$vclsnqnt','$date')";
						
						mysqli_query($link,"delete from ph_stock_master where item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='1'");
						$txt.="\ndelete from ph_stock_master where item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='1'";
						
						mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('1','$qrstk1[item_id]','$qrstk1[batch_no]','$vclsnqnt','0000-00-00','$qdetails[expiry_date]') ");
						$txt.="\ninsert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('1','$qrstk1[item_id]','$qrstk1[batch_no]','$vclsnqnt','0000-00-00','$qdetails[expiry_date]') ";
					}
				/////end////////////////
				
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'  order by  date desc "));
				$txt.="\nselect * from inv_mainstock_details where date='$date' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'  order by  date desc ";
				if($qrstkmaster['item_id']!='')
				{
					$vstkqnt=$qrstkmaster['closing_qnty']-$qrstk1['issue_qnt'];
					$isuqnt=$qrstkmaster['issu_qnty']+$qrstk1['issue_qnt'];
					
					mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',issu_qnty='$isuqnt' where  date='$date' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
					$txt.="\nupdate inv_mainstock_details set closing_qnty='$vstkqnt',issu_qnty='$isuqnt' where  date='$date' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ";
					
									
					mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'  ");
					$txt.="\nupdate inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'  ";
				}
				else///for if data not found
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' order by slno desc"));
					$txt.="\nselect * from inv_mainstock_details where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' order by slno desc";
					$vstkqnt=$qrstkmaster['closing_qnty']-$qrstk1['issue_qnt'];
					
					 mysqli_query($link,"insert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) value('$qrstk1[item_id]','$qrstk1[batch_no]','$date','$qrstkmaster[closing_qnty]',0,'$qrstk1[issue_qnt]','$vstkqnt') ");
					 $txt.="\ninsert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) value('$qrstk1[item_id]','$qrstk1[batch_no]','$date','$qrstkmaster[closing_qnty]',0,'$vqnt','$vstkqnt') ";
					
					
					
					mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'  ");
					$txt.="\nupdate inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'  ";
				}
				mysqli_query($link,"UPDATE `inv_mainstore_issue_details_cnf` SET `status`='1', `rcv_by`='$user',`rcv_date`='$date', `rcv_time`='$time' WHERE order_no='$ord' and substore_id='1' and item_id='$itm' and batch_no='$bch' and status='0'");
				$txt.="\nUPDATE `inv_mainstore_issue_details_cnf` SET `status`='1', `rcv_by`='$user',`rcv_date`='$date', `rcv_time`='$time' WHERE order_no='$ord' and substore_id='1' and item_id='$itm' and batch_no='$bch' and status='0'\n";
				$result="1";
			}
		}
	}
	text_query($txt);
	echo $result;
}

if($type==999)
{
	
}
?>
