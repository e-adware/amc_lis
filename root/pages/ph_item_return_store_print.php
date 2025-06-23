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



$ord=base64_decode($_GET['oRdr']);

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
$det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM ph_item_return_store_master WHERE `returnr_no`='$ord'"));
$dept=mysqli_fetch_assoc(mysqli_query($link,"select substore_name FROM `inv_sub_store` where substore_id='$det[substore_id]'"));

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Item Return Details</u></h5></center>
				
			</div>


<table>

<tr><td colspan="5" style="font-weight:bold;font-size:13px">Order No : <?php echo $ord;?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Order From  : <?php echo $dept['substore_name'];?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Order Date  : <?php echo convert_date($det['date']);?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">Print Date : <?php echo date('d-m-Y');?></td></tr>
</table>
<?php

?>


 
      <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" class="btn btn-success" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" class="btn btn-success" name="button" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
         <table width="100%">
			 <tr class="bline">         
			<td colspan="8"></td>
			</tr>
			<tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">#</td>
				<td  style="font-weight:bold;font-size:13px">Item Name</td>
				<td  style="font-weight:bold;font-size:13px">Batch No</td>
				<td  style="font-weight:bold;font-size:13px">Expiry</td>
				<td  style="font-weight:bold;font-size:13px;text-align:right">Return Qnty</td>
			</tr>
             <?php 
               $i=1;
              
              //$q=mysqli_query($link,"select a.*,b.item_id,b.order_qnt from item_master a,inv_substore_order_details b where a.item_id=b.item_id and b.order_no='$ord' order by a.item_name");
              $q=mysqli_query($link,"SELECT a.*,b.`item_name` FROM `ph_item_return_store_detail` a, `item_master` b WHERE a.`item_id`=b.`item_id` AND `returnr_no`='$ord'");
              
              while($q1=mysqli_fetch_array($q))
              {
			 ?>
             <tr class="line">
				<td style="font-size:13px">&nbsp;&nbsp;<?php echo $i;?></td>
				<td style="font-size:13px"><?php echo $q1['item_name'];?></td>
				<td style="font-size:13px;"><?php echo $q1['batch_no'];?></td>
				<td style="font-size:13px;"><?php echo $q1['expiry_date'];?></td>
				<td style="font-size:13px;text-align:right;"><?php echo $q1['quantity'];?></td>
             </tr>
             <?php
			$i++;
			}
			?>
<tr class="bline">         
<td colspan="8"></td>
</tr>
</table>  
       

  </div>
 
</body>
</html>

