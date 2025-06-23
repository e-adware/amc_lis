<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<style>
.table-condensed tr th, .table-condensed tr td
{
	padding: 1px 1px 1px 3px;
}
.no_line td
{
	border-top:none;
}
@media print
{
	.noprint
	{
		display:none;
	}
}
</style>

</head>
<body>
<?php
include'../../includes/connection.php';



$ord=base64_decode($_GET['orderno']);

//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
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


$splr=mysqli_fetch_array(mysqli_query($link,"SELECT a.*,b.supplier_id,b.user FROM inv_supplier_master a,inv_purchase_order_master b WHERE  a.id=b.supplier_id and b.order_no='$ord' "));

$qbank=mysqli_fetch_array(mysqli_query($link,"select bank_name from banks where bank_id='$splr[bank_id]'"));
$qemname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$splr[user]'"));
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Purchase Order</u></h5></center>
				
			</div>


<table width="100%">
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Order No : <?php echo $ord;?></td><td style="text-align:right;font-weight:bold;font-size:13px" >Date : <?php echo date('d-m-Y');?> </td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">To ,</td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px"><?php echo $splr['name'];?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px"><?php echo $splr['address'];?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Contact No. : <?php echo $splr['contact'];?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">E-mail : <?php echo $splr['email'];?></td></tr>
</table>



<br/>

<table width="100%" style="display:none;">
<tr><td colspan="2" style="font-weight:bold;font-size:13px">Bank Details :</td><td>&nbsp;</td><td colspan="2" style="font-weight:bold;font-size:13px"> CEO </td> </tr>	
<tr><td colspan="2" style="font-size:13px">Bank Name : <?php echo $qbank['bank_name'];?></td><td>&nbsp;</td><td colspan="2" style="font-size:13px"> Swargdew Siu-Ka-Pha Multispecialty</td> </tr>	
<tr><td colspan="2" style="font-size:13px">Bank Ac No : <?php echo $splr['bank_ac_no'];?></td></td><td>&nbsp;</td><td colspan="2" style="font-size:13px"> Hospital, Rajabari, Sivasagar Assam</td> </tr>	
<tr><td colspan="2" style="font-size:13px">Branch  : <?php echo $splr['branch'];?></td></td><td>&nbsp;</td><td colspan="2" style="font-size:13px"> Mail  - ceo@medicitysibsagar.org ,</td> </tr>	
<tr><td colspan="2" style="font-size:13px">IFSC Code : <?php echo $splr['ifsc_code'];?></td></td><td>&nbsp;</td><td colspan="2" style="font-size:13px">manageroperations@medicitysibsagar.org</td> </tr>	
</table>
 <br/>     
      
   <table width="100%" style="font-size:13px;">
     <!-- <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" class="btn btn-success" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" class="btn btn-success" name="button" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>-->
      <tr>
		  <td colspan="6"><b>Dear Sir /Madam</b></br> &nbsp; &nbsp; We are pleased to place this order for supply of the following items as per specification rates and other terms &amp; conditions as
mentioned below. Please submit the bills in duplicate for payment by cheque/cash/account transfer.</td>
      </tr>
      </table>
      <br/>
         <table class="table table-condensed" style="font-size:13px;"  >
			<tr>
				<th>Sl No</th>
				<th>Item Name</th>
				<th style="text-align:right;">Order Quantity</th>
				<th style="text-align:right;">Rate</th>
				<th style="text-align:right;">Amount</th>
			</tr>
             <?php 
              $i=1;
              $q=mysqli_query($link,"select a.*,b.item_name,b.mrp from inv_purchase_order_details a,item_master b where a.item_id=b.item_id and a.order_no='$ord' order by b.item_name");
              while($q1=mysqli_fetch_array($q))
              {
							
				$vttl+=$q1['amount'];  
			 ?>
             <tr>
				<td><?php echo $i;?></td>
				<td><?php echo $q1['item_name'];?></td>
				<td style="text-align:right;"><?php echo $q1['qnt'];?></td>
				<td style="text-align:right;"><?php echo $q1['rate'];?></td>
				<td style="text-align:right;"><?php echo $q1['amount'];?></td>
             </tr>  
                         
             <?php
			$i++;
			}
			?>
		<tr>
			<td colspan="4" style="font-weight:bold;font-size:12px;text-align:right;">Total :</td>
			<td style="font-weight:bold;font-size:12px;text-align:right;"><?php echo number_format($vttl,2);?></td>
		</tr>
		
		<tr>
			<td colspan="5"><?php echo "Rupees: ".convert_number(round($vttl))." only";?></td>
		</tr>
        <tr>
			<td colspan="5">&nbsp;</td>
		</tr>
	
<tr class="no_line">
	<td colspan="2">Indented By : <?php echo $qemname['name'];?></td>
	
	<td></td>
	<td colspan="2" style="text-align:right;">Authourised Signatory </td>
	
</tr>
<tr class="no_line">
	<td colspan="5"> Please Note :</td>
</tr>

<tr class="no_line">
	<!--<td colspan="5">1. <?php echo $splr['sup_condition'];?> </td>-->
	<td colspan="5">1. Payment 30% advance, 50% after delivery of the 50% materials & 20% payment within a month after delivery.</td>
</tr>
<tr class="no_line">
	<td colspan="5">2. Freight charges will be free of cost.</td>
</tr>
<tr class="no_line">
	<td colspan="5">3. If found any damage during the time of delivery, item should be changed in that place.</td>
</tr>
<tr class="no_line">
	<td colspan="5">4. All rates are without tax.</td>
</tr>

</table>  
       

  </div>
 
</body>

<script src="../../js/jquery.min.js"></script>
<script>
	$(document).ready(function()
	{
		$(document).keyup(function(e)
		{
			if(e.keyCode==27)
			window.close();
		});
	})
</script>
</html>

