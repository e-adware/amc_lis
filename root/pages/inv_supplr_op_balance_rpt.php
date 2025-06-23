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
$splr=mysqli_fetch_array(mysqli_query($link,"SELECT a.*,b.supplier_id,b.user FROM inv_supplier_master a,inv_purchase_order_master b WHERE  a.id=b.supplier_id and b.order_no='$ord' "));


$qemname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$splr[user]'"));
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Supplier Opening Balance Report</u></h5></center>
				
			</div>


<table width="100%">

<tr><td colspan="5" style="font-weight:bold;font-size:13px"></td><td style="text-align:right;font-weight:bold;font-size:13px" >Date : <?php echo date('d-m-Y');?> </td></tr>


</table>
<?php

?>


 
      <table width="100%">
     <!-- <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" class="btn btn-success" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" class="btn btn-success" name="button" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>-->
      <tr>
		  <td  colspan="6">&nbsp;</td>
      </tr>
      
      
      <tr>
		  <td  colspan="6">&nbsp;</td>
      </tr>
      
      </table>
         <table width="100%">
			<tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
				<td style="font-weight:bold;font-size:13px">Supplier Name</td>
				<td align="right" style="font-weight:bold;font-size:13px">Opennig Balance</td>
				<td align="right" style="font-weight:bold;font-size:13px">Date</td>
				
			</tr>
             <?php 
               $i=1;
              
             
              $q=mysqli_query($link,"select a.*,b.name from inv_supplier_opening_balance a,inv_supplier_master b where a.supp_code=b.id  order by b.name");
              while($q1=mysqli_fetch_array($q))
              {
				  
				$vitemamt=0; 
				
				$vttl+=$q1['op_balance'];  
               
			 ?>
             <tr class="line">
				<td style="font-size:13px">&nbsp;&nbsp;<?php echo $i;?></td>
				<td style="font-size:13px"><?php echo $q1['name'];?></td>
				<td align="right" style="font-size:13px"><?php echo $q1['op_balance'];?></td>
				<td align="right" style="font-size:13px"><?php echo $q1['date'];?></td>
				
             </tr>  
                         
             <?php
			$i++ ;}?>
			
		              
<tr class="bline">         
 <td colspan="5">&nbsp;</td>
</tr>


<tr>
  <td  colspan="5">&nbsp;</td>
</tr>




</table>  
       

  </div>
 
</body>
</html>

