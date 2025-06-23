<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
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

<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Available Stock Report (Sub Store) </u></h5>
				
			</div>
</div>	


<table>
<tr><td colspan="5" style="font-size:13px">Print Date:<?php echo date('d/m/Y');?></td></tr>
</table>
<?php

?>

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
            <tr  class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
               <td style="font-weight:bold;font-size:13px">Item Code</td>
               <td style="font-weight:bold;font-size:13px">Item Name</td>
               <td align="right" style="font-weight:bold;font-size:13px">Stock</td>
              
            </tr>
			<?php
			$qsubstrnm=mysqli_query($link,"SELECT distinct a.substore_id,b.substore_name FROM inv_substorecurrent_stock a,inv_sub_store b WHERE a.substore_id=b.substore_id and a.closing_stock>0 order by b.substore_name");
			while($qsubstrnm1=mysqli_fetch_array($qsubstrnm))
			{
			?>
			<tr>
			    <td colspan="4" style="font-size:13px"><b><i>Substore Name : <?php echo $qsubstrnm1['substore_name'];?></i></b></td>
			</tr>
            
             <?php 
              $i=1;
              
			  $qrslctitm=mysqli_query($link,"SELECT * FROM `inv_substorecurrent_stock` WHERE   substore_id='$qsubstrnm1[substore_id]' and  closing_stock>0");  
			while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_indent_master` WHERE `id`='$qrslctitm1[item_code]'"));
			
			 ?>
             <tr class="line">
				<td style="font-size:13px"><?php echo $i;?></td>
				<td style="font-size:13px"><?php echo $qrslctitm1['item_code'];?></td>
				<td style="font-size:13px"><?php echo $itm['name'];?></td>
				<td align="right" style="font-size:13px"><?php echo $qrslctitm1['closing_stock'];?></td>
              
                  
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			  ?>
             
             <?php
		   }?>
             
               
              
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

