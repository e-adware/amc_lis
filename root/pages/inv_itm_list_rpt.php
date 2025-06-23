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
$splr=mysqli_fetch_array(mysqli_query($link,"select a.*,b.name from ph_purchase_order_master a,ph_supplier_master b where a.SuppCode=b.id and a.order_no='$ord'  "));

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container-fluid">
			<div class="" style="">
				<?php //include('page_header_ph.php'); ?>
				<center><h5><u>Item List</u></h5></center>
				
			</div>


<table>


<tr><td colspan="5" style="font-weight:bold;font-size:13px">Print Date:<?php echo date('d/m/Y');?></td></tr>
</table>
<?php

?>


 
      <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" class="btn btn-default" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" class="btn btn-success" name="button" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
         <table width="100%">
			<tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
				<td  style="font-weight:bold;font-size:13px">Item Name</td>
			</tr>
             <?php 
              
              $q=mysqli_query($link,"select distinct a.inv_cate_id,b.name  from inv_indent_master a,inv_indent_type b where a.inv_cate_id=b.inv_cate_id order by a.inv_cate_id");
              while($q1=mysqli_fetch_array($q))
              {
               ?>
				<tr class="line">
				   <td style="font-weight:bold;font-size:13px" colspan="3">Category Name : <?php echo $q1['name'];?></td>
			    </tr>  
               <?php
				$qsub=mysqli_query($link,"select distinct a.sub_cat_id,b.sub_cat_name  from inv_indent_master a,inv_subcategory b where a.sub_cat_id=b.sub_cat_id and a.inv_cate_id='$q1[inv_cate_id]' order by b.sub_cat_name");
				while($qsub1=mysqli_fetch_array($qsub))
				{
				  ?>
					<tr class="line">
					<td style="font-size:13px" colspan="3"><i>Sub Category Name : <?php echo $qsub1['sub_cat_name'];?></i></td>
					</tr>  
				  <?php
              $i=1;
			$qrslctitm=mysqli_query($link,"SELECT * from inv_indent_master  WHERE inv_cate_id='$q1[inv_cate_id]' and sub_cat_id='$qsub1[sub_cat_id]'  ORDER BY name");  
			while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
				
			
			 ?>
             <tr class="line">
				<td style="font-size:13px">&nbsp;&nbsp;<?php echo $i;?></td>
               
               <td style="font-size:13px"><?php echo $qrslctitm1['name'];?></td>
              
              
                  
             </tr>  
                         
             <?php
			$i++ ;}?>
			
				<?php
				}?>
                    
				<?php
				}
				?>
             
             
               
              
<tr class="bline">         
<td colspan="8">&nbsp;</td>
</tr>
</table>  
       

  </div>
 
</body>
</html>

