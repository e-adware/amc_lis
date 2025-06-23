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


$supplr=$_GET['supplr'];
$orderno=$_GET['orderno'];

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
$splr=mysqli_fetch_array(mysqli_query($link,"select * from ph_supplier_master where id='$supplr'  "));

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>




<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Available Stock Report </u></h5></center>
				
			</div>
</div>			

<table>
<tr><td colspan="5">Print Date:<?php echo date('d/m/Y');?></td></tr>
</table>				
				
<div class="container">
<div align="center">
  <form name="form1" id="form1" method="post" action="">
   <div class="row">
      <div class="span12">
      <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" class="btn btn-default" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" class="btn btn-default" name="button" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
         <table width="100%">
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
               <td style="font-weight:bold;font-size:13px">Item Code</td>
               <td style="font-weight:bold;font-size:13px">Item Name</td>
               <td style="font-weight:bold;font-size:13px">Batch No</td>
               <td align="right" style="font-weight:bold;font-size:13px">Stock</td>
              
            </tr>
             <?php 
              $i=1;
			  $qrslctitm=mysqli_query($link,"SELECT * FROM `ph_stock_master` WHERE `quantity`<10");  
			while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$qrslctitm1[item_code]'"));
			
			 ?>
             <tr class="line">
				<td style="font-size:13px"><?php echo $i;?></td>
               <td style="font-size:13px"><?php echo $qrslctitm1['item_code'];?></td>
               <td style="font-size:13px"><?php echo $itm['item_name'];?></td>
               <td style="font-size:13px"><?php echo $qrslctitm1['batch_no'];?></td>
               <td align="right" style="font-size:13px"><?php echo $qrslctitm1['quantity'];?></td>
              
                  
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			  ?>
             
             
               
              
<tr class="bline">         
<td colspan="8">&nbsp;</td>
</tr>
</table>  
       
      </div>
   </div>
  
   </form>
  </div>
 </div>  
</body>
</html>

