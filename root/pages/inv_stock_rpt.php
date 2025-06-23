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


$catid=$_GET['catid'];


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


	if($catid==1)
	{
		$vcatname="Main Store";
	}
	else
	{
		$vcatname="Pharmacy";
	}
$splr=mysqli_fetch_array(mysqli_query($link,"select * from ph_supplier_master where id='$supplr'  "));

$category=mysqli_fetch_array(mysqli_query($link,"select * from inv_indent_type where inv_cate_id='$catid'  "));

?>

<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Available Stock Report (Main Store) </u></h5>
				
			</div>
</div>	


<table>
<tr><td colspan="5" style="font-size:13px">Print Date : <?php echo date('d-m-y');?></td></tr>
<tr><td colspan="5" style="font-size:13px">Category : <?php echo $vcatname;?></td></tr>
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
               <td style="font-weight:bold;font-size:13px">Batch Name</td>
               <td style="font-weight:bold;font-size:13px">Expiry Date</td>
               <td align="right" style="font-weight:bold;font-size:13px">Stock</td>
              
              
            </tr>
             <?php 
              $i=1;
				if($catid==1)
				{
				$q=mysqli_query($link,"SELECT a.*,b.item_name FROM `inv_maincurrent_stock` a,item_master b WHERE a.`closing_stock`>0 and a.item_id=b.item_id  order by b.item_name");
				}
				else
				{

				$q=mysqli_query($link,"SELECT a.*,b.item_name FROM `ph_stock_master` a,item_master b WHERE a.`quantity`>0 and a.substore_id='1' and a.item_code=b.item_id  order by b.item_name");
				}


				while($r=mysqli_fetch_array($q))
				{

				$vclsngqnt=0;
				$vitmid="";
				if($catid==1)
				{
					$vclsngqnt=$r['closing_stock'];
					$vitmid=$r['item_id'];
					$vexpiry=$r['exp_date'];
				}
				else
				{
					$vclsngqnt=$r['quantity'];
					$vitmid=$r['item_code'];
					$vexpiry=$r['exp_date'];
				}
			
			 ?>
             <tr class="line">
				<td style="font-size:13px"><?php echo $i;?></td>
				<td style="font-size:13px"><?php echo $vitmid;?></td>
				<td style="font-size:13px"><?php echo $r['item_name'];?></td>
				<td style="font-size:13px"><?php echo $r['batch_no'];?></td>
				<td style="font-size:13px"><?php echo convert_date($vexpiry);?></td>
				<td align="right" style="font-size:13px"><?php echo $vclsngqnt;?></td>
				
                  
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

