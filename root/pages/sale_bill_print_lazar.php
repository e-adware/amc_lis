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
	padding: 10px;
	/*font-size:15px; font-family: "Courier New", Courier, monospace; line-height: 18px;*/
}
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

$bill=$_GET['billno'];
$cus=mysqli_fetch_array(mysqli_query($link,"select * from `ph_sell_master` where `bill_no`='$bill'"));
$qipd=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$cus[ipd_id]'"));
if($cus['bill_type_id']==2)
{
	$vmnyrcpt="Credit Memo";
}
else
{
	$vmnyrcpt="Cash Memo";
}

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-y', $timestamp);
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

//------------exists patient's details------------//
$pat_qry=mysqli_query($link,"SELECT * FROM `patient_bill_record` WHERE `bill_no`='$bill'");
$pat_num=mysqli_num_rows($pat_qry);
//------------bill details------------//

$ds=$cus['total_amt']-$cus['discount_amt'];
//------------------------------------//
$qgst=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(gst_amount),0) as maxgst from `ph_sell_details` where `bill_no`='$bill'"));
$vgstamt=$qgst['maxgst']/2;

$qry=mysqli_query($link,"select * from `ph_sell_details` where `bill_no`='$bill'");
$num=mysqli_num_rows($qry);
if($num>0)
{
?>
<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u><?php echo $vmnyrcpt;?></u></h5></center>
				
			</div>
</div>	

<h5 align='center'><b>&nbsp;</b></h5><span style="float:right;">Date: <?php echo $cus['entry_date'];?></span>
<table width="100%">
	<tr>
		<td colspan="2" style="font-size:13px"> Bill No &nbsp;: <?php echo $bill;?></td>
	</tr>
	
	<tr>
		<td style="font-size:13px"> Name &nbsp;&nbsp;: <?php echo $cus['customer_name'];?></td>
		<td style="font-size:13px">Phone : <?php echo $cus['customer_phone'];?></td>
	</tr>
	
	<tr>
		<td style="font-size:13px">Pin No : <?php echo $cus['ipd_id'];?></td>
		<td style="font-size:13px">UH ID &nbsp;: <?php echo $qipd['patient_id'];?></td>
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
<table class="table table-condensed table-bordered">
	<tr bgcolor="#EAEAEA" class="bline" >
		<td style="font-size:13px">#</td>
		<td style="font-size:13px">Description</td>
		<td style="font-size:13px">HSN Code</td>
		<td style="font-size:13px">Batch No</td>
		<td style="font-size:13px">Expiry</td>
		<td style="font-size:13px">Quantity</td>
		<td style="font-size:13px">MRP</td>
		<td style="font-size:13px">Rate</td>
		<td style="font-size:13px">CGST %</td>
		<td style="font-size:13px">SGST %</td>
		<td style="font-size:13px">Net Amount</td>
	</tr>
	<?php
	$n=1;
	$tot="";
	while($r=mysqli_fetch_array($qry))
	{
		
		$it=mysqli_fetch_array(mysqli_query($link,"select `item_name`,hsn_code from `ph_item_master` where `item_code`='$r[item_code]'"));
		$qexpiry=mysqli_fetch_array(mysqli_query($link,"select expiry_date from `ph_purchase_receipt_details` where `item_code`='$r[item_code]' and recept_batch='$r[batch_no]'"));
		$rate=$r['sale_qnt']*$r['mrp'];
		$vitmttl=$r['sale_price']*$r['sale_qnt'];
		$vsbttl=$vsbttl+$vitmttl;
		$vcgst=$r['gst_percent']/2;
		$tot+=$rate;
	?>
	<tr>
		<td style="font-size:13px"><?php echo $n++;?></td>
		<td style="font-size:13px"><?php echo $it['item_name'];?></td>
		<td style="font-size:13px"><?php echo $it['hsn_code'];?></td>
		<td style="font-size:13px"><?php echo $r['batch_no'];?></td>
		<td style="font-size:13px"><?php echo $qexpiry['expiry_date'];?></td>
		<td style="font-size:13px"><?php echo $r['sale_qnt'];?></td>
		<td style="font-size:13px"><?php echo $r['mrp'];?></td>
		<td style="font-size:13px"><?php echo $r['sale_price'];?></td>
		<td style="font-size:13px"><?php echo $vcgst;?></td>
		<td style="font-size:13px"><?php echo $vcgst;?></td>
		<td style="font-size:13px"><?php echo round($vitmttl,2);?></td>
		
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
		
		<td colspan="4" style="font-size:13px">Sub Total</td>
		<td style="font-size:13px"><?php echo number_format($vsbttl,2);?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan="2" style="font-size:13px">Taxable CGST : <?php echo number_format($vsbttl,2);?> Amt : <?php echo number_format($vgstamt,2);?></td>
		
		<td></td>
		<td></td>
		<td></td>
		
		<td colspan="4" style="font-size:13px">Total (CGST+SGST)</td>
		<td style="font-size:13px"><?php echo number_format($qgst[maxgst],2);?></td>
	</tr>
	<tr>
		<td></td>
       <td colspan="2" style="font-size:13px">Taxable SGST : <?php echo number_format($vsbttl,2);?> Amt : <?php echo number_format($vgstamt,2);?></td>		<td></td>
		<td></td>
		<td></td>
		
		<td colspan="4" style="font-size:13px">Total Amount</td>
		<td style="font-size:13px"><?php echo val_con(round($cus['total_amt']));?></td>
	</tr>
	<?php
	if($cus['discount_perchant']>0)
	{
	?>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:13px">Discount</td>
		<td style="font-size:13px"><?php echo round($cus['discount_perchant'])." %";?></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:13px">After Discount</td>
		<td style="font-size:13px"><?php echo val_con(round($ds));?></td>
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
		<td colspan="4" style="font-size:13px">Paid</td>
		<td style="font-size:13px"><?php echo val_con(round($cus['paid_amt']));?></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" style="font-size:13px">Balance</td>
		<td style="font-size:13px"><?php echo val_con(round($cus['balance']));?></td>
	</tr>
	<!--<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td colspan="2"></td>
	</tr>-->
</table>
<b><?php echo "Rupees: ".convert_number(round($cus['paid_amt']))." only";?></b>
<div style="float:right;margin-top:100px;margin-right:30px;">
For<br/>
------------------
<b><?php //echo $pharmacyName;?></b>
</div>
<script>//window.print();</script>
<?php
}
else
{
?>
<h4 align="center"><u>MONEY RECIEPT</u></h4>
<h5 align='center'><b>Bill No: <?php echo $bill;?></b></h5>
<h4 align="center">Not Found</h4>
<?php
}
?>
</body>
</html>
