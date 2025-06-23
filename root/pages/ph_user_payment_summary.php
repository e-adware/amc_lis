<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
<style>

.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}
.bline td{border-bottom:1px solid;}
.line td{border-top:1px dotted;}
</style>

</head>
<body>
<?php
include'../../includes/connection.php';


$fdate=$_GET['fdate'];
$ffdate=$_GET['fdate'];
$tdate=$_GET['tdate'];

//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-M-y', $timestamp);
return $new_date;
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


//$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>

<div class="container-fluid">
	<div>
		<?php include('page_header_ph.php'); ?>
	</div>
	<center><b style="font-size:16px;"><u>Pharmacy Summary Report</u></b></center>
	From <?php echo convert_date($fdate);?> to  <?php echo convert_date($tdate);?>
	<div class="noprint" style="text-align:right;"><input type="button" name="button" class="btn btn-default" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" class="btn btn-success" id="button" value="Exit" onClick="javascript:window.close()" /></div>
      <table class="table table-condensed table-bordered" style="font-size:13px;">
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
	///////////end///////////////////////
	
	$qry=mysqli_query($link,"SELECT DISTINCT `user` FROM `ph_payment_details` ");
	while($r=mysqli_fetch_array($qry))
	{
		$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM employee WHERE `emp_id`='$r[user]'"));
		$amt=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(sum(amount),0) as amount FROM `ph_payment_details` WHERE `user`='$r[user]' AND `entry_date` BETWEEN '$fdate' AND '$tdate'"));
		//$total+=$amt["amount"];
		$u_tot=0;
		$u_net=0;
		$u_ret=0;
		
		 $i=1;
	
	  
		//$qq=mysqli_query($link,"SELECT * FROM `ph_payment_details` WHERE `user`='$r[user]' AND `entry_date` BETWEEN '$fdate' AND '$tdate'");
		$qq=mysqli_query($link,"SELECT * FROM `ph_payment_details` WHERE `user`='$r[user]' AND `entry_date` BETWEEN '$fdate' AND '$tdate' $ph_max_slno_str_less $ph_max_slno_str_grtr  ORDER BY `sl_no`");
		
		while($rr=mysqli_fetch_array($qq))
		{
			
			
			if($i==1)
			{
				$vbllfrom=$rr['bill_no'];
			}
			$vbllto=$rr['bill_no'];
			if($vbllfrom!=$vbllto)
			{
			  $vbllto=$rr['bill_no'];
		    }
		    
			$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `total_amt`,`discount_amt`,`adjust_amt`,`paid_amt`,`balance` FROM `ph_sell_master` WHERE `bill_no`='$rr[bill_no]'"));
			$sum=($pat['total_amt']-$pat['discount_amt']-$pat['adjust_amt']);
			//$ret=mysqli_fetch_array(mysqli_query($link,"SELECT `amount` FROM `ph_item_return` WHERE `bill_no`='$rr[bill_no]' AND `accp_user`='$rr[user]'"));
			$ret=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(`amount`),0) as amount  FROM `ph_item_return` WHERE `bill_no`='$rr[bill_no]' AND `accp_user`='$rr[user]' and date BETWEEN '$fdate' AND '$tdate'"));
			$u_tot+=$sum;
			$u_ret+=$ret['amount'];
		$i++;}
		
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
	<p><span ><b>Bill From : <?php echo $vbllfrom;?> To : <?php echo $vbllto;?></b></span></p>
	<span style="float:left;"><b>Amount in words : <?php echo convert_number($all_net,2);?> only.</b></span>
 </div>  
</body>
</html>

