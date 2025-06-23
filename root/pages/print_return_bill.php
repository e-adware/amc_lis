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
#item_list tr td, #item_list tr th
{
	padding : 0px 0px 0px 4px;
}
</style>

</head>
<body onkeyup="close_window(event)" onafterprint="window.close()">
<?php
include'../../includes/connection.php';

$bill=$_GET['billno'];
$cnt=$_GET['counter'];
$sub_id=1;
//$bill=base64_decode($bill);
//$sub_id=base64_decode($sub_id);

$cus=mysqli_fetch_array(mysqli_query($link,"select * from `ph_sell_master` where `bill_no`='$bill' AND `substore_id`='$sub_id'"));
$qipd=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$cus[ipd_id]'"));

$qreturn=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxrtrn from `ph_item_return_master` where `bill_no`='$bill' and substore_id='$sub_id' "));
$emp=mysqli_fetch_array(mysqli_query($link,"select user from `ph_item_return_master` where `bill_no`='$bill' and substore_id='$sub_id' AND `counter`='$cnt'"));
$quser=mysqli_fetch_array(mysqli_query($link,"select name from `employee` where `emp_id`='$emp[user]'"));
if($cus['balance']>0)
{
	$vmnyrcpt="Credit Memo";
}
else
{
	$vmnyrcpt="Cash Memo";
}
$vmnyrcpt="Return Memo";
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

?>
<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><b><u><?php echo $vmnyrcpt;?></u></b></center>
			</div>


<table width="100%">
	<tr>
		<td style="font-size:14px"> Bill No </td><td style="font-size:14px">: <?php echo $bill;?></td>
		<td style="font-size:14px"> Date </td><td style="font-size:13px"> : <?php echo convert_date($cus['entry_date']).' '.$cus['time'];?></td>
	</tr>
	
	<tr>
		<td style="font-size:14px"> Name</td><td style="font-size:14px">: <?php echo $cus['customer_name'];?></td>
		<td style="font-size:13px"> Phone</td><td style="font-size:13px">: <?php echo $cus['customer_phone'];?></td>
	</tr>
	<?php
	if($opdid)
	{
	?>
	<tr>
		<td style="font-size:14px"> Pin No </td><td style="font-size:14px">: <?php echo $opdid;?></td>
		<td style="font-size:14px"> UHID </td><td style="font-size:14px">: <?php echo $cus['patient_id'];?></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td style="font-size:14px">Prescribed By</td><td style="font-size:14px">: <?php echo $ref['Name'];?></td>
		<td style="font-size:14px">Care of</td><td style="font-size:14px">: <?php echo $cus['co'];?></td>
	</tr>
	
</table>


<?php
	  if($qreturn['maxrtrn']>0)
	  {
	?>


<table class="table table-condensed table-bordered" id="item_list">
   <tr>
	   <th style="font-size:13px">Bill No</th>
	   <th style="font-size:13px">Return Date</th>
	   <th style="font-size:13px">Item Name</th>
	   <th style="font-size:13px">Qnty.</th>
	   <th style="font-size:13px">Amount Refunded</th>
   </tr>
   <?php
	  $tot_ref=0;
	  $ref=mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_item_return_master where  bill_no='$bill' and `counter`='$cnt'");
	 $reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_sell_master where bill_no='$bill' "));
	  while($ref_m=mysqli_fetch_array($ref))
	  {
		$bill=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from payment_detail where patient_id='$ref_m[patient_id]' and visit_no='$ref_m[visit_no]' limit 0,1"));
		
		$qrate=mysqli_fetch_array(mysqli_query($link,"select a.recpt_mrp,b.item_name from ph_item_return_master a,item_master b where a.item_code='$ref_m[item_code]' and a.recept_batch='$ref_m[batch_no]' and a.item_code=b.item_id"));
		$qrate=mysqli_fetch_array(mysqli_query($link,"select item_name from item_master where item_id='$ref_m[item_code]' "));
		$vrtrnamt1=$ref_m['return_qnt']*$ref_m['mrp'];
		
		$vrtrnamt=$vrtrnamt+$vrtrnamt1;
		
		echo "<tr style='font-size:13px'><td>$ref_m[bill_no]</td><td>$ref_m[return_date]</td><td>$qrate[item_name]</td><td>$ref_m[return_qnt]</td><td>$vrtrnamt1</td></tr>";
		
	  }
		if($reg['discount_perchant']>0)
		{
		$disc=($vrtrnamt*$reg['discount_perchant'])/100;
		$vrtrnamt=$vrtrnamt-$disc;
		}
	  //$vttcashinhand=$r_tot-$vrtrnamt;
	  if($reg['discount_perchant']>0)
	  {
	  ?>
	<tr>
	  <td colspan="4" style="font-weight:bold;font-size:13px;text-align:right">Discount (<?php echo $reg['discount_perchant']." %";?>) :</td>
	  <td style="font-weight:bold;font-size:13px;text-align:left"> <?php echo number_format($disc,2);?></td>
   </tr>
   <?php
   }
   ?>
	<tr>
	  <td colspan="4" style="font-weight:bold;font-size:13px;text-align:right">Total Refund :</td>
	  <td style="font-weight:bold;font-size:13px;text-align:left"> <?php echo number_format($vrtrnamt,2);?></td>
   </tr>
   </table>
<?php
}?>

<b><?php echo "Rupees in words : ".convert_number(round($vrtrnamt))." only";?></b>
<div style="float:right;margin-top:0px;margin-right:30px;">
For Pharmacy Incharge<br/>
<?php echo $quser['name'];?>
</div>
<script>
	//window.print();
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
