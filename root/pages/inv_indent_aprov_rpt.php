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



$ord=$_GET['orderno'];

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
$splr=mysqli_fetch_array(mysqli_query($link,"SELECT a.*,b.substore_name FROM inv_substore_indent_order_master a,inv_sub_store b WHERE  a.substore_id=b.substore_id and a.order_no='$ord' order by order_no"));

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Item Approved Report</u></h5></center>
				
			</div>


<table>

<tr><td colspan="5" style="font-weight:bold;font-size:13px">Order No : <?php echo $ord;?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Order From  : <?php echo $splr[substore_name];?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Print Date : <?php echo date('d/m/Y');?></td></tr>
</table>
<?php

?>


 
      <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" class="btn btn-success" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" class="btn btn-success" name="button" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
         <table width="100%">
			<tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
				<td  style="font-weight:bold;font-size:13px">Item Name</td>
				<td  align="right" style="font-weight:bold;font-size:13px">Order Qnty</td>
				<td align="right" style="font-weight:bold;font-size:13px">Approved Qnty</td>
			</tr>
             <?php 
               $i=1; 
              $user=mysqli_fetch_array(mysqli_query($link,"select a.user,b.name from  inv_substorestock_aproved_details a,employee b where a.user=b.emp_id and a.order_no='$ord' and substore_id='$splr[substore_id]'  "));
              $q=mysqli_query($link,"select a.*,b.item_code,b.order_qnt from inv_indent_master a,inv_substore_order_details b where a.id=b.item_code and b.order_no='$ord' order by a.name");
              while($q1=mysqli_fetch_array($q))
              {
               $qrcv=mysqli_fetch_array(mysqli_query($link,"select * from inv_substorestock_rcv_details where order_no='$ord' and substore_id='$splr[substore_id]' and 	item_code='$q1[item_code]'"));
			 ?>
             <tr class="line">
				<td style="font-size:13px">&nbsp;&nbsp;<?php echo $i;?></td>
				<td style="font-size:13px"><?php echo $q1['name'];?></td>
				<td align="right" style="font-size:13px"><?php echo $q1['order_qnt'];?></td>
                <td align="right" style="font-size:13px"><?php echo $qrcv['rcv_qnt'];?></td>          
             </tr>  
                         
             <?php
			$i++ ;}?>
			
		              
<tr class="">         
<td colspan="4">&nbsp;</td>
</tr>

<tr >         
    <td align="right"  colspan="4" style="font-size:13px">Approved By</td>
    
</tr>
<tr >         
    <td align="right"  colspan="4" style="font-size:13px"><?php echo $user['name'];?></td>
    
</tr>

</table>  
       

  </div>
 
</body>
</html>

