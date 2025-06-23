<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<style>
	


@font-face
	{

	font-family:'dotmatrix';
	src:url('../../fonts/dotmat.ttf')

	}

	body {
	font-family: Arial, Helvetica, sans-serif;	
  }
			
				
input[type="text"]
{
	border:none;
}
body {
	padding: 0px;
	/*font-size:15px; font-family: "Courier New", Courier, monospace; line-height: 18px;*/
}
.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}

@media print
{
html,body
{
   letter-spacing:0px;
   font-size:11px !important;
   /*font-family:'dotmatrix';*/
   font-weight:bold;
   //text-transform: uppercase;
}


}

			
			
.bline td{border-bottom:1px solid;}
.line td{border-top:1px dotted;}
#item_list tr td, #tbl_return tr td
{
	padding : 0px 0px 0px 4px;
	border-top: 1px solid #525252;
	border-left: 1px solid #525252;
}
.amttr
{
    border-right: 1px solid #525252;
}
</style>

</head>
<body onkeydown="close_window(event)" onafterprint="window.close()">
<?php
include'../../includes/connection.php';

$bill=$_GET['billno'];
$sub_id=$_GET['sub_id'];
$chk=$_GET['chk'];
$bill=base64_decode($bill);
$sub_id=base64_decode($sub_id);

$cus=mysqli_fetch_array(mysqli_query($link,"select * from `ph_sell_master` where `bill_no`='$bill' AND `substore_id`='$sub_id'"));
$qipd=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$cus[ipd_id]'"));
$quser=mysqli_fetch_array(mysqli_query($link,"select name from `employee` where `emp_id`='$cus[user]'"));

$qreturn=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxrtrn from `ph_item_return_master` where `bill_no`='$bill' and substore_id='$sub_id' "));

if($chk)
{
	$check_bill_payment=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_payment_details` WHERE `bill_no`='$bill' AND `substore_id`='$sub_id'"));
	if(!$check_bill_payment)
	{
		if($cus['bill_type_id']==1)
		{
			$type_of_payment="A";
		}
		else
		{
			$type_of_payment="B";
		}
		mysqli_query($link,"INSERT INTO `ph_payment_details`(`bill_no`, `substore_id`, `entry_date`, `amount`, `payment_mode`, `check_no`, `type_of_payment`, `user`, `time`) VALUES ('$bill','$sub_id','$cus[entry_date]','$cus[paid_amt]','Cash','','$type_of_payment','$cus[user]','$cus[time]')");
	}
}

if($cus['balance']>0)
{
	$vmnyrcpt="Credit Memo";
}
else
{
	$vmnyrcpt="Cash Memo";
}
$vmnyrcpt="GST INVOICE";
if($cus['ipd_id']!="")
{
	$opdid=$cus['ipd_id'];
}
else
{
	$opdid=$cus['opd_id'];
}


function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-Y', $timestamp);
		return $new_date;
	}
}

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
    $tens = array("", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", 
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
//------------check bill payment------------//
$check_payment=mysqli_fetch_assoc(mysqli_query($link, " SELECT * FROM `ph_payment_details` WHERE `bill_no`='$bill'"));
if(!$check_payment)
{
	$chk_pay_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `entry_date`,`paid_amt`,`bill_type_id`,`user`,`time` FROM `ph_sell_master` WHERE `bill_no`='$bill'"));
	if($chk_pay_det['bill_type_id']=="1")
	{
		$type_of_payment="A";
	}
	if($chk_pay_det['bill_type_id']=="2")
	{
		$type_of_payment="B";
	}
	mysqli_query($link,"INSERT INTO `ph_payment_details`(`bill_no`, `substore_id`, `entry_date`, `amount`, `payment_mode`, `check_no`, `type_of_payment`, `user`, `time`) VALUES ('$bill','$sub_id','$chk_pay_det[entry_date]','$chk_pay_det[paid_amt]','Cash','','$type_of_payment','$chk_pay_det[user]','$chk_pay_det[time]')");
}
//------------exists patient's details------------//
$pat_qry=mysqli_query($link,"SELECT * FROM `patient_bill_record` WHERE `bill_no`='$bill'");
$pat_num=mysqli_num_rows($pat_qry);
//------------bill details------------//

$ds=$cus['total_amt']-$cus['discount_amt'];
$vittmttl=0;
//------------------------------------//
$qgst=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(gst_amount),0) as maxgst,ifnull(sum(total_amount),0) as maxttl from `ph_sell_details` where `bill_no`='$bill'"));
$vgstamt=$qgst['maxgst']/2;
$vittmttl=round($qgst['maxttl']);

$ref_name="";
if($cus['ref_by_name'])
{
	$ref_name=$cus['ref_by_name'];
}
else
{
	$ref=mysqli_fetch_assoc(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$cus[refbydoctorid]'"));
	$ref_name=$ref['Name'];
}
$qry=mysqli_query($link,"select * from `ph_sell_details` where `bill_no`='$bill' and sale_qnt>0");
$num=mysqli_num_rows($qry);
if($num>0)
{
?>
<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><b><u><?php echo $vmnyrcpt;?></u></b></center>
			</div>


<table width="100%">
	<tr>
		<td style="font-size:14px;font-weight:bold;"> Bill No </td><td style="font-size:14px">: <?php echo $bill;?></td>
		<td style="font-size:14px;font-weight:bold;"> Date </td><td style="font-size:14px"> : <?php echo convert_date($cus['entry_date']).' '.$cus['time'];?></td>
	</tr>
	
	<tr>
		<td style="font-size:14px;font-weight:bold;"> Name</td><td style="font-size:14px">: <?php echo $cus['customer_name'];?></td>
		<td style="font-size:14px;font-weight:bold;"> Phone</td><td style="font-size:14px">: <?php echo $cus['customer_phone'];?></td>
	</tr>
	<?php
	if($opdid)
	{
	?>
	<tr>
		<td style="font-size:14px;font-weight:bold;"> Pin No </td><td style="font-size:14px">: <?php echo $opdid;?></td>
		<td style="font-size:14px;font-weight:bold;"> UHID </td><td style="font-size:14px">: <?php echo $cus['patient_id'];?></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td style="font-size:14px;font-weight:bold;">Prescribed By</td><td style="font-size:14px">: <?php echo $ref_name;?></td>
		<td style="font-size:14px;font-weight:bold;">Address</td><td style="font-size:14px">: <?php echo $cus['address'];?></td>
	</tr>
	
</table>


<?php
if($pat_num>0)
{
	$p_rec=mysqli_fetch_array($pat_qry);
	{
		echo "Patient ID: ".$p_rec['patient_id'];
	}
}
?>
<table class="table table-condensed table-bordered" id="item_list">
	<tr bgcolor="" class="bline" >
		<td style="font-size:14px;font-weight:bold;">#</td>
		<td style="font-size:14px;font-weight:bold;">Description</td>
		<td style="font-size:14px;font-weight:bold;">HSN</td>
		<td style="font-size:14px;font-weight:bold;">MFG</td>
		<td style="font-size:14px;font-weight:bold;">Batch No</td>
		<td style="font-size:14px;font-weight:bold;">Expiry</td>
		<td style="font-size:14px;font-weight:bold;">Quantity</td>
		<td style="font-size:14px;font-weight:bold;">MRP</td>
		<!--<td style="font-size:14px;font-weight:bold;">Rate</td>-->
		<td style="font-size:14px;font-weight:bold;">CGST %</td>
		<td style="font-size:14px;font-weight:bold;">SGST %</td>
		<td style="font-size:14px;font-weight:bold;" class="amttr">Net Amount</td>
	</tr>
	<?php
	$n=1;
	$tot="";
	while($r=mysqli_fetch_array($qry))
	{
		$it=mysqli_fetch_array(mysqli_query($link,"select `item_name`,`hsn_code`,`manufacturer_id` from `item_master` where `item_id`='$r[item_code]'"));
		$qexpiry=mysqli_fetch_array(mysqli_query($link,"select expiry_date from `ph_purchase_receipt_details` where `item_code`='$r[item_code]' and recept_batch='$r[batch_no]'"));
		$manuf=mysqli_fetch_array(mysqli_query($link,"SELECT `manufacturer_name` FROM `manufacturer_company` where `manufacturer_id`='$it[manufacturer_id]'"));
		$rate=$r['sale_qnt']*$r['mrp'];
		//$vitmttl=$r['sale_price']*$r['sale_qnt'];
		$vitmttl=$r['mrp']*$r['sale_qnt'];
		$vsbttl=$vsbttl+$vitmttl;
		$vcgst=$r['gst_percent']/2;
		$tot+=$rate;
		$vexpiry=substr($qexpiry['expiry_date'],2,5);
	?>
	<tr>
		<td style="font-size:14px"><?php echo $n++;?></td>
		<td style="font-size:14px"><?php echo $it['item_name'];?></td>
		<td style="font-size:14px"><?php echo $it['hsn_code'];?></td>
		<td style="font-size:10px"><?php echo "<i>".$manuf['manufacturer_name']."</i>";?></td>
		<td style="font-size:14px"><?php echo $r['batch_no'];?></td>
		<td style="font-size:14px"><?php echo $vexpiry;?></td>
		<td style="font-size:14px"><?php echo $r['sale_qnt'];?></td>
		<td style="font-size:14px"><?php echo number_format($r['mrp']);?></td>
		<!--<td style="font-size:14px"><?php echo $r['sale_price'];?></td>-->
		<td style="font-size:14px"><?php echo $vcgst;?></td>
		<td style="font-size:14px"><?php echo $vcgst;?></td>
		<td style="font-size:14px" class="amttr"><?php echo number_format($vitmttl,2);?></td>
		
	</tr>
	<?php
	}
	?>
	<tr style="display:none;">
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:14px">Sub Total</td>
		<td style="font-size:14px" class="amttr"><?php echo number_format($vsbttl,2);?></td>
	</tr>
	<tr style="display:none;">
		<td></td>
		<td colspan="2" style="font-size:14px">Taxable CGST : <?php echo number_format($vsbttl,2);?> Amt : <?php echo number_format($vgstamt,2);?></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:14px">Total (CGST+SGST)</td>
		<td style="font-size:14px" class="amttr"><?php echo number_format($qgst[maxgst],2);?></td>
	</tr>
	<tr style="display:none;">
		<td></td>
		<td colspan="2" style="font-size:14px">Taxable SGST : <?php echo number_format($vsbttl,2);?> Amt : <?php echo number_format($vgstamt,2);?></td>		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:14px">Total Amount</td>
		<td style="font-size:14px" class="amttr"><?php echo number_format($vittmttl,2);?></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:14px">Total Amount</td>
		<td style="font-size:14px" class="amttr"><?php echo number_format($vittmttl,2);?></td>
	</tr>
	<?php
	$vbal=0;
	if($vittmttl!=$cus['total_amt'])
	{
		$vbal=$vittmttl-$cus['discount_amt']-$cus['paid_amt']-$cus['adjust_amt']-$qreturn['maxrtrn'];
		//mysqli_query($link,"UPDATE `ph_sell_master` SET `total_amt`='$vittmttl',`balance`='$vbal' WHERE `bill_no`='$bill'");
		
		$vbal=0;
	}
	else
	{
		$vbal=round($cus['balance']);
	}
	
	if($cus['discount_amt']>0)
	{
	?>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:14px">Discount</td>
		<td style="font-size:14px" class="amttr"><?php echo number_format($cus['discount_amt'],2);?></td>
	</tr>
	
	<?php
	}
	?>
	<?php
	if($cus['adjust_amt']>0)
	{
	?>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:14px">Adjust Amount</td>
		<td style="font-size:14px" class="amttr"><?php echo number_format($cus['adjust_amt'],2);?></td>
	</tr>
	
	<?php
	}
	?>
	
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:14px">Paid</td>
		<td style="font-size:14px" class="amttr"><?php echo val_con(round($cus['paid_amt']));?></td>
	</tr>
	
	<?php
	  if($qreturn['maxrtrn']>0)
	  {
		  $discount_perchant=explode(".",$cus['discount_perchant']);
		  $discount_perchant=$discount_perchant[0];
		  $ret_amount=$qreturn['maxrtrn']-(($qreturn['maxrtrn']*$cus['discount_perchant'])/100);
	?>
	
	 <tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:14px">Return Amount</td>
		<td style="font-size:14px" class="amttr"><?php echo val_con(round($ret_amount));?></td>
	</tr>
	
	<?php
  }?>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:14px">Balance</td>
		<td style="font-size:14px" class="amttr"><?php echo number_format($vbal,2);?></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
	</tr>
</table>
<b><?php echo "Rupees in words : ".convert_number(round($cus['paid_amt']))." only.";?></b><br/>

<div style="display:inline-block;">
	Taxable CGST : <?php echo number_format($vsbttl,2);?> Amt : <?php echo number_format($vgstamt,2);?><br/>
	Taxable SGST : <?php echo number_format($vsbttl,2);?> Amt : <?php echo number_format($vgstamt,2);?>
	<?php
	$br="";
	if($cus['discount_amt']>0)
	{
		echo "<br/><b>You have saved : Rs ".$cus['discount_amt']."</b>";
		$br="<br/>";
	}
	?>
</div>
<div style="float:right;margin-top:0px;margin-right:30px;">
	<?php echo $br;?>
Authorised Signatory<br/>
<?php //echo $quser['name'];?>
</div>

<?php
	  if($qreturn['maxrtrn']>0)
	  {
	?>

</table>

<table class="table table-condensed table-bordered" id="tbl_return">
   <tr>
	  <th colspan="5" style="font-size:13px">Item Return</th>
   </tr>
   <tr>
	   <td style="font-size:13px">Bill No</td>
	   <td style="font-size:13px">Return Date</td>
	   <td style="font-size:13px">Item Name</td>
	   <td style="font-size:13px">Qnty.</td>
	   <td style="font-size:13px">Amount Refunded</td>
   </tr>
   <?php
	  $tot_ref=0;
	  $ref=mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_item_return_master where  bill_no='$bill' ");
	 
	  while($ref_m=mysqli_fetch_array($ref))
	  {
		$bill=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from payment_detail where patient_id='$ref_m[patient_id]' and visit_no='$ref_m[visit_no]' limit 0,1"));
		$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_sell_master where bill_no='$ref_m[bill_no]' "));
		
		//$qrate=mysqli_fetch_array(mysqli_query($link,"select a.recpt_mrp,b.item_name from ph_item_return_master a,item_master b where a.item_code='$ref_m[item_code]' and a.recept_batch='$ref_m[batch_no]' and a.item_code=b.item_id"));
		$qrate=mysqli_fetch_array(mysqli_query($link,"select item_name from item_master where item_id='$ref_m[item_code]' "));
		$vrtrnamt1=$ref_m['return_qnt']*$ref_m['mrp'];
		
		$vrtrnamt=$vrtrnamt+$vrtrnamt1;
		
		echo "<tr style='font-size:13px'><td>$ref_m[bill_no]</td><td>$ref_m[return_date]</td><td>$qrate[item_name]</td><td>$ref_m[return_qnt]</td><td>$vrtrnamt1</td></tr>";
		
	  }
	  
	  $vttcashinhand=$r_tot-$vrtrnamt;
	  ?>
	<tr>
		<td style="font-weight:bold;font-size:13px;text-align:right"></td>
		<td style="font-weight:bold;font-size:13px;text-align:right">Discount : <?php echo $discount_perchant." %";?></td>
		<td colspan="2" style="font-weight:bold;font-size:13px;text-align:right">Total Refund :</td>
		<td style="font-weight:bold;font-size:13px;text-align:left"> <?php echo number_format($ret_amount,2);?></td>
   </tr>
   </table>
<?php
}?>
<script>
	window.print();
</script>
<?php
}
else
{
?>
<h4 align="center"><u>MONEY RECIEPT</u></h4>
<h5 align="center"><b>Bill No: <?php echo $bill;?></b></h5>
<h4 align="center">Not Found</h4>
<?php
}
?>
<script>
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		
		if(unicode==27)
		{
			window.close();
		}
	}
</script>
</body>
</html>
