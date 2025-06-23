<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<style>
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

$bill=$_GET['blno'];
$cus=mysqli_fetch_array(mysqli_query($link,"select * from `ph_sell_master` where `bill_no`='$bill'"));
$qipd=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$cus[ipd_id]'"));

$vmnyrcpt="Credit Note";

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

$qcrdtno=mysqli_fetch_array(mysqli_query($link,"select credit_no,date from ph_item_return_creditnote where bill_no='$bill'"));
$qry=mysqli_query($link,"select * from `ph_item_return_master` where `bill_no`='$bill'");
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

<h5 align='center'><b>&nbsp;</b></h5><span style="float:right;">Date: <?php echo $qcrdtno['date'];?></span>
<table width="100%">
	<tr>
		<td  style="font-weight:bold;font-size:13px"> Bill No : <?php echo $bill;?></td>
		<td  style="font-weight:bold;font-size:13px"> Credit No : <?php echo $qcrdtno['credit_no'];?></td>
	</tr>
	
	<tr>
		<td style="font-weight:bold;font-size:13px"> Name &nbsp;&nbsp;: <?php echo $cus['customer_name'];?></td>
		<td style="font-weight:bold;font-size:13px">Phone : <?php echo $cus['customer_phone'];?></td>
	</tr>
	
	<tr>
		<td style="font-weight:bold;font-size:13px">PIN NO : <?php echo $cus['ipd_id'];?></td>
		<td style="font-weight:bold;font-size:13px">UH ID &nbsp;: <?php echo $qipd['patient_id'];?></td>
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
		<td style="font-weight:bold;font-size:13px">#</td>
		<td style="font-weight:bold;font-size:13px">Description</td>
		<td style="font-weight:bold;font-size:13px">HSN Code</td>
		<td style="font-weight:bold;font-size:13px">Batch No</td>
		<td style="font-weight:bold;font-size:13px">Quantity</td>
		<td style="font-weight:bold;font-size:13px">Rate</td>
		<td style="font-weight:bold;font-size:13px">Amount</td>
		<!--<td style="font-weight:bold;font-size:13px">GST (%)</td>
		<td style="font-weight:bold;font-size:13px">GST </td>-->
		<td style="font-weight:bold;font-size:13px">Net Amount</td>
	</tr>
	<?php
	$n=1;
	$tot="";
	while($r=mysqli_fetch_array($qry))
	{
		$qmrp=mysqli_fetch_array(mysqli_query($link,"select mrp from ph_sell_details where bill_no='$bill' and item_code='$r[item_code]' and batch_no='$r[batch_no]'"));
		$vgstamt=$r['net_amount']-$r['total_amount'];
		$it=mysqli_fetch_array(mysqli_query($link,"select `item_name`,hsn_code from `ph_item_master` where `item_code`='$r[item_code]'"));
		$rate=$r['return_qnt']*$qmrp['mrp'];
		$vttlamt=$vttlamt+$rate;
		$tot+=$rate;
	?>
	<tr>
		<td style="font-size:13px"><?php echo $n++;?></td>
		<td style="font-size:13px"><?php echo $it['item_name'];?></td>
		<td style="font-size:13px"><?php echo $it['hsn_code'];?></td>
		<td style="font-size:13px"><?php echo $r['batch_no'];?></td>
		<td style="font-size:13px"><?php echo $r['return_qnt'];?></td>
		<td style="font-size:13px"><?php echo $qmrp['mrp'];?></td>
		<td style="font-size:13px"><?php echo val_con($rate);?></td>
		<!--<td style="font-size:13px"><?php echo $r['gst_percent'];?></td>
		<td style="font-size:13px"><?php echo number_format($vgstamt,2);?></td>-->
		<td style="font-size:13px"><?php echo val_con(round($rate));?></td>
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
		
		
		<td colspan="2" style="font-weight:bold;font-size:13px">Total Amount</td>
		<td style="font-weight:bold;font-size:13px"><?php echo val_con(round($vttlamt));?></td>
	</tr>
	
	
	
</table>
<b><?php echo "Rupees: ".convert_number(round($vttlamt))." only";?></b>
<div style="float:right;margin-top:100px;margin-right:30px;">
For<br/>
------------------
<b><?php //echo $pharmacyName;?></b>
</div>
<script>window.print();</script>
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
