<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

$rupees_symbol="&#x20b9; ";

function convert_date($date)
{
	if($date)
	{
	$timestamp = strtotime($date); 
	$new_date = date('d-M-y', $timestamp);
	return $new_date;
	}
}

function convert_number($number) 
{ 
    if (($number < 0) || ($number > 999999999)) 
    { 
    throw new Exception("Number is out of range");
    } 

    $Gn = floor($number / 100000);  /* Lakh (100 kilo) */ 
    $number -= $Gn * 100000; 
    $kn = floor($number / 1000);     /* Thousands (kilo) */ 
    $number -= $kn * 1000; 
    $Hn = floor($number / 100);      /* Hundreds (hecto) */ 
    $number -= $Hn * 100; 
    $Dn = floor($number / 10);       /* Tens (deca) */ 
    $n = $number % 10;               /* Ones */ 

    $res = ""; 

    if ($Gn) 
    { 
        $res .= convert_number($Gn) . " Lakh"; 
    } 

    if ($kn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($kn) . " Thousand"; 
    } 

    if ($Hn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($Hn) . " Hundred"; 
    } 

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
        "Nineteen"); 
    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
        "Seventy", "Eigthy", "Ninety"); 

    if ($Dn || $n) 
    { 
        if (!empty($res)) 
        { 
            $res .= " and "; 
        } 

        if ($Dn < 2) 
        { 
            $res .= $ones[$Dn * 10 + $n]; 
        } 
        else 
        { 
            $res .= $tens[$Dn]; 

            if ($n) 
            { 
                $res .= "-" . $ones[$n]; 
            } 
        } 
    } 

    if (empty($res)) 
    { 
        $res = "zero"; 
    } 

    return $res; 
}

// Indian Money format
setlocale(LC_MONETARY, 'en_IN');


if($_POST["type"]=="usr_list")
{
	$fdate=$_POST["fdate"];
	$tdate=$_POST["tdate"];
	$user=$_POST["user"];
	$l=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$user'"));
	$q=mysqli_query($link,"SELECT DISTINCT `user` FROM `ph_payment_details` ");
	?>
	<b>Select User</b> &nbsp;&nbsp;&nbsp;
	<select id="all_usr" class="span4" >
		<?php
		while($u=mysqli_fetch_array($q))
		{
			$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$u[user]'"));
		?>
		<option value="<?php echo $u['user'];?>" <?php if($u['user']==$user){echo "selected='selected'";}?>><?php echo $e['name'];?></option>
		<?php
		}
		?>
	</select>
	<button type="button" class="btn btn-info" onclick="view_user_det()">View</button>
	<div id="all_det"></div>
	<?php
}

if($_POST["type"]=="usr_summary")
{
	$fdate=$_POST["fdate"];
	$ffdate=$_POST["fdate"];
	$tdate=$_POST["tdate"];
	
	?>
	<span style="float:left;"><button type="button" class="btn btn-info" onclick="print_user_summary('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button></span>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>User</th>
			<th>Received(Total)</th>
			<th>OPD(RCV)</th>
			<th>IPD(RCV)</th>
			<th>Return</th>
			<th>Cr. Due</th>
			<th>Cr. Receive</th>
			<th>Balance</th>
			<th>Discount</th>
			<th>Net Amount</th>
		</tr>
	<?php
	$i=1;
	$total=0;
	$all_net=0;
	$all_ret=0;
	$crd_tot=0;
	$crr_tot=0;
	$bal_tot=0;
	//-- credit amount
	
	
	$cr_qry=mysqli_query($link,"SELECT DISTINCT `user` FROM `ph_payment_details` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate'");
	while($cr_usr=mysqli_fetch_array($cr_qry))
	{
		$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM employee WHERE `emp_id`='$cr_usr[user]'"));
		$crd_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(balance),0) as tot from ph_sell_master  where  bill_type_id='2' and entry_date between '$fdate' and '$tdate'"));
		$crd_tot+=$crd_amt["tot"];
		//$crr_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(paid_amt),0) as tot from ph_sell_master  where user='$cr_usr[user]' and bill_type_id='2' and entry_date between '$ffdate' and '$tdate'"));
		
		$crr_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.amount),0) as tot from ph_payment_details a,ph_sell_master b  where a.user='$cr_usr[user]' and b.bill_type_id='2' and a.entry_date between '$ffdate' and '$tdate' and a.bill_no=b.bill_no and a.entry_date !=b.entry_date"));
		$crr_tot+=$crr_amt["tot"];
		$bal_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(balance),0) as tot from ph_sell_master  where user='$cr_usr[user]' and entry_date between '$fdate' and '$tdate'"));
		$bal_tot+=$bal_amt["tot"];
		$dis_amt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(discount_amt),0) as tot from ph_sell_master  where user='$cr_usr[user]' and entry_date between '$fdate' and '$tdate'"));
		$dis_tot+=$dis_amt["tot"];
	?>

	<?php
	}
	
	// Close account
	$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$tdate' "));	
	if($check_close_account_today)
	{
		$ph_max_slno_less=$check_close_account_today['ph_slno'];
		$ph_max_slno_str_less=" AND `sl_no`<=$ph_max_slno_less ";
		
		
	}
	else
	{
		$ph_max_slno_str_less="";
		
	}
	
	$last_date=date('Y-m-d', strtotime($fdate. ' - 1 days'));
	$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$last_date' "));	
	if($check_close_account_today)
	{
		$fdate=date('Y-m-d', strtotime($fdate. ' - 1 days'));
		
		$ph_max_slno_grtr=$check_close_account_today['ph_slno'];
		$ph_max_slno_str_grtr=" AND `sl_no`>$ph_max_slno_grtr ";
		
		
	}
	else
	{
		$ph_max_slno_str_grtr="";
		
	}
	//////////end//////////////////////////////
	
	$qry=mysqli_query($link,"SELECT DISTINCT `user` FROM `ph_payment_details` ");
	while($r=mysqli_fetch_array($qry))
	{
		$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM employee WHERE `emp_id`='$r[user]'"));
		$amt=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(sum(amount),0) as amount FROM `ph_payment_details` WHERE `user`='$r[user]' AND `entry_date` BETWEEN '$fdate' AND '$tdate'"));
		//$total+=$amt["amount"];
		$u_tot=0;
		$u_net=0;
		$u_ret=0;
		$opdrcv=0;
		
		
	
		//$qq=mysqli_query($link,"SELECT * FROM `ph_payment_details` WHERE `user`='$r[user]' AND `entry_date` BETWEEN '$fdate' AND '$tdate'");
		$qq=mysqli_query($link,"SELECT * FROM `ph_payment_details` WHERE `user`='$r[user]' AND `entry_date` BETWEEN '$fdate' AND '$tdate' $ph_max_slno_str_less $ph_max_slno_str_grtr  ORDER BY `sl_no`");
		while($rr=mysqli_fetch_array($qq))
		{
			$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `total_amt`,`discount_amt`,`adjust_amt`,`paid_amt`,`balance` FROM `ph_sell_master` WHERE `bill_no`='$rr[bill_no]'"));
			$sum=($pat['total_amt']-$pat['discount_amt']-$pat['adjust_amt']);
			$ret=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(`amount`),0) as amount  FROM `ph_item_return` WHERE `bill_no`='$rr[bill_no]' AND `accp_user`='$rr[user]' and date BETWEEN '$fdate' AND '$tdate'"));
			$u_tot+=$sum;
			$u_ret+=$ret['amount'];
		}
		
		$qopd=mysqli_query($link,"SELECT a.*,b.bill_no FROM `ph_payment_details` a,ph_sell_master b WHERE a.`user`='$r[user]' and a.bill_no=b.bill_no and b.pat_type!='6' AND a.`entry_date` BETWEEN '$fdate' AND '$tdate' $ph_max_slno_str_less $ph_max_slno_str_grtr  ORDER BY `sl_no`");
		while($qopd1=mysqli_fetch_array($qopd))
		{
			
			$opdsum=$qopd1['amount'];
			$opd_tot+=$opdsum;
			
		}
		
		$qipd=mysqli_query($link,"SELECT a.*,b.bill_no FROM `ph_payment_details` a,ph_sell_master b WHERE a.`user`='$r[user]' and a.bill_no=b.bill_no and b.pat_type='6' AND a.`entry_date` BETWEEN '$fdate' AND '$tdate' $ph_max_slno_str_less $ph_max_slno_str_grtr  ORDER BY `sl_no`");
		while($qipd1=mysqli_fetch_array($qipd))
		{
			
			$ipdsum=$qipd1['amount'];
			$ipd_tot+=$ipdsum;
			
		}
		
		$total+=$u_tot;
		$opdtotal+=$opd_tot;
		$ipdtotal+=$ipd_tot;
		
		$all_ret+=$u_ret;
		$p_ret=0;
		$qq=mysqli_query($link,"SELECT * FROM `ph_item_return` WHERE `accp_user`='$r[user]' AND `bill_no` NOT IN (SELECT bill_no FROM `ph_payment_details` WHERE `user`='$r[user]' AND `entry_date` BETWEEN '$ffdate' AND '$tdate') AND `date` BETWEEN '$ffdate' AND '$tdate'");
		$p_num=mysqli_num_rows($qq);
		if($p_num>0)
		{
		$i=1;
		while($rr=mysqli_fetch_array($qq))
		{
			$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `entry_date`,`customer_name`,`total_amt`,`discount_amt`,`adjust_amt`,`paid_amt`,`balance` FROM `ph_sell_master` WHERE `bill_no`='$rr[bill_no]'"));
		
		$p_ret+=$rr["amount"];
		$i++;
		}
		}
		$all_ret+=$p_ret;
		$u_net=$u_tot-$p_ret-$u_ret;
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $e["name"];?></td>
			<td style="text-align:right;"><?php echo number_format($u_tot,2);?></td>
			<td style="text-align:right;"><?php echo number_format($opd_tot,2);?></td>
			<td style="text-align:right;"><?php echo number_format($ipd_tot,2);?></td>
			<td style="text-align:right;"><?php echo number_format(($p_ret+$u_ret),2);?></td>
			
			<td style="text-align:right;"><?php echo number_format(0,2);?></td>
			<td style="text-align:right;"><?php echo number_format(0,2);?></td>
			<td style="text-align:right;"><?php echo number_format(0,2);?></td>
			<td style="text-align:right;"><?php echo number_format(0,2);?></td>
			<td style="text-align:right;"><?php echo number_format($u_net,2);?></td>
		</tr>
		<?php
		$i++;
		$all_net+=$u_net;
		}
		$total=round($total);
		$opdtotal=round($opdtotal);
		$ipdtotal=round($ipdtotal);
		$all_ret=round($all_ret);
		$all_net=round($all_net);
		
		?>
		<tr>
			<th colspan="2" style="text-align:right;margin-right:2%;">Total Amount : </th>
			<th style="text-align:right;"><?php echo number_format($total,2);?></th>
			<th style="text-align:right;"><?php echo number_format($opdtotal,2);?></th>
			<th style="text-align:right;"><?php echo number_format($ipdtotal,2);?></th>
			<th style="text-align:right;"><?php echo number_format(($all_ret),2);?></th>
			<th style="text-align:right;"><?php echo number_format($crd_tot,2);?></th>
			<th style="text-align:right;"><?php echo number_format($crr_tot,2);?></th>
			<th style="text-align:right;"><?php echo number_format($bal_tot,2);?></th>
			<th style="text-align:right;"><?php echo number_format($dis_tot,2);?></th>
			<th style="text-align:right;"><?php echo number_format($all_net,2);?></th>
		</tr>
	</table>
	<span style="float:left;"><b>Amount in words : <?php echo convert_number($all_net,2);?> only.</b></span>
	<?php
}

if($_POST["type"]=="view_user_det")
{
	$fdate=$_POST["fdate"];
	$ffdate=$_POST["fdate"];
	$tdate=$_POST["tdate"];
	$all_usr=$_POST["all_usr"];
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Bill</th>
			<th>Type</th>
			<th>Entry Date</th>
			<th>Patient</th>
			<th>Amount</th>
			<th>Paid Amount</th>
			<th>Discount</th>
			<th>Balance</th>
			<th>Adjust</th>
			<th>Return</th>
		</tr>
		<?php
		
		// Close account
	$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$tdate' "));	
	if($check_close_account_today)
	{
		$ph_max_slno_less=$check_close_account_today['ph_slno'];
		$ph_max_slno_str_less=" AND `sl_no`<=$ph_max_slno_less ";
		
		
	}
	else
	{
		$ph_max_slno_str_less="";
		
	}
	
	$last_date=date('Y-m-d', strtotime($fdate. ' - 1 days'));
	$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$last_date' "));	
	if($check_close_account_today)
	{
		$fdate=date('Y-m-d', strtotime($fdate. ' - 1 days'));
		
		$ph_max_slno_grtr=$check_close_account_today['ph_slno'];
		$ph_max_slno_str_grtr=" AND `sl_no`>$ph_max_slno_grtr ";
		
		
	}
	else
	{
		$ph_max_slno_str_grtr="";
		
	}
		
		
		
		
		$j=1;
		$tot_amt=0;
		$tot_paid=0;
		$tot_dis=0;
		$tot_bal=0;
		$tot_adj=0;
		$tot_ret=0;
		
		$q=mysqli_query($link,"SELECT * FROM `ph_payment_details` WHERE `user`='$all_usr' AND `entry_date` BETWEEN '$fdate' AND '$tdate' $ph_max_slno_str_less $ph_max_slno_str_grtr  ORDER BY `bill_no`");
		
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `entry_date`,`customer_name`,`total_amt`,`discount_amt`,`adjust_amt`,`paid_amt`,`balance`,pat_type FROM `ph_sell_master` WHERE `bill_no`='$r[bill_no]'"));
			$qtype=mysqli_fetch_array(mysqli_query($link,"select sell_name from ph_sell_type where sell_id='$pat[pat_type]'"));
			$total=($pat['total_amt']-$pat['discount_amt']-$pat['adjust_amt']);
			$p_amt=mysqli_fetch_array(mysqli_query($link,"SELECT `total_amt` FROM `ph_sell_master_edit` WHERE `bill_no`='$r[bill_no]'"));
			$ret=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(`amount`),0) as amount FROM `ph_item_return` WHERE `bill_no`='$r[bill_no]' AND `accp_user`='$all_usr'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo $qtype['sell_name'];?></td>
			<td><?php echo convert_date($pat['entry_date']);?></td>
			<td><?php echo $pat['customer_name'];?></td>
			<td><?php echo number_format($total,2);?></td>
			<td><?php echo number_format($r['amount'],2);?></td>
			<td><?php echo number_format($pat['discount_amt'],2);?></td>
			<td><?php echo number_format($pat['balance'],2);?></td>
			<td><?php echo number_format($pat['adjust_amt'],2);?></td>
			<td><?php echo number_format($ret["amount"],2);?></td>
		</tr>
		<?php
		$tot_amt+=$total;
		$tot_paid+=$r["amount"];
		$tot_dis+=$pat["discount_amt"];
		$tot_bal+=$pat["balance"];
		$tot_adj+=$pat["adjust_amt"];
		$tot_ret+=$ret["amount"];
		$j++;
		}
		$tot_ret+=$rr["amount"];
		$tot_paid-=$rr["amount"];
		$tot_paid=round($tot_paid);
		$tot_ret=round($tot_ret);
		?>
		<tr>
			<th colspan="5" style="text-align:right;">Total Amount</th>
			<th><?php echo number_format($tot_amt,2);?></th>
			<th><?php echo number_format($tot_paid,2);?></th>
			<th><?php echo number_format($tot_dis,2);?></th>
			<th><?php echo number_format($tot_bal,2);?></th>
			<th><?php echo number_format($tot_adj,2);?></th>
			<th><?php echo number_format($tot_ret,2);?></th>
		</tr>
		<?php
		$qq=mysqli_query($link,"SELECT * FROM `ph_item_return` WHERE `accp_user`='$all_usr' AND `bill_no` NOT IN (SELECT bill_no FROM `ph_payment_details` WHERE `user`='$all_usr' AND `entry_date` BETWEEN '$ffdate' AND '$tdate') AND `date` BETWEEN '$ffdate' AND '$tdate'");
		$p_num=mysqli_num_rows($qq);
		if($p_num>0)
		{
		?>
		<tr>
			<th colspan="11" style="">Previous Bill Return Details</th>
		</tr>
		<?php
		$p_ret=0;
		$i=1;
		while($rr=mysqli_fetch_array($qq))
		{
			$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `entry_date`,`customer_name`,`total_amt`,`discount_amt`,`adjust_amt`,`paid_amt`,`balance`,pat_type FROM `ph_sell_master` WHERE `bill_no`='$rr[bill_no]'"));
			$qtype=mysqli_fetch_array(mysqli_query($link,"select sell_name from ph_sell_type where sell_id='$pat[pat_type]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $rr['bill_no'];?></td>
			<td><?php echo $qtype['sell_name'];?></td>
			<td><?php echo convert_date($pat['entry_date']);?></td>
			<td><?php echo $pat['customer_name'];?></td>
			<td><?php //echo number_format($total,2);?>--</td>
			<td><?php //echo number_format($r['amount'],2);?>--</td>
			<td><?php //echo number_format($pat['discount_amt'],2);?>--</td>
			<td><?php //echo number_format($pat['balance'],2);?>--</td>
			<td><?php //echo number_format($pat['adjust_amt'],2);?>--</td>
			<td><?php echo number_format($rr["amount"],2);?></td>
		</tr>
		<?php
		$p_ret+=$rr["amount"];
		$i++;
		}
		$p_ret=$tot_ret+$p_ret;
		$tot_paid=$tot_paid-$p_ret;
		?>
		<tr>
			<th colspan="4" style="text-align:right;">Total</th>
			<td>--</td>
			<th><?php echo number_format($tot_paid,2);?></th>
			<td>--</td>
			<td>--</td>
			<td>--</td>
			<th><?php echo number_format($p_ret,2);?></th>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}
?>
