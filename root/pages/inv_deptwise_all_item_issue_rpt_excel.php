<?php

$filename ="item_issue.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
?>
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

$sbstrid=$_GET['sbstrid'];
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];



//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}

function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
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
$splr=mysqli_fetch_array(mysqli_query($link,"select * from inv_sub_store where  substore_id='$sbstrid'  "));
if($sbstrid==0)
{
	$vsplrname="All";
}
else
{
	$vsplrname=$splr['substore_name'];
}
$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_item_return_supplier_master WHERE supplier_id='$splrid' and returnr_no='$billno' "));  
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Summary Report</u></h5></center>
			</div>
<table>
 <tr ><td style="font-weight:bold;font-size:13px">From </td><td style="font-weight:bold;font-size:13px"> : <?php echo convert_date($fdate);?></td></tr>
 <tr><td style="font-weight:bold;font-size:13px">To  <td style="font-weight:bold;font-size:13px"> : <?php echo convert_date($tdate);?></td></tr>
 <tr><td style="font-weight:bold;font-size:13px">Print Date  <td style="font-weight:bold;font-size:13px"> : <?php echo date('d-m-Y');?></td></tr>
 <tr><td style="font-weight:bold;font-size:13px">Substore </td><td style="font-weight:bold;font-size:13px"> : <?php echo $vsplrname;?></td></tr>
</table>
<?php

?>
      <table width="100%">
      <tr>
        <td colspan="8" style="text-align:right"><div class="noprint"><input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
         <table width="100%" >
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
				<td style="font-weight:bold;font-size:13px">Issue No</td>
				<td style="font-weight:bold;font-size:13px">Issue Date</td>
				<td style="font-weight:bold;font-size:13px">Time</td>
				<td style="font-weight:bold;font-size:13px">Issue To</td>
				<td style="font-weight:bold;font-size:13px">Department</td>
				<td style="font-weight:bold;font-size:13px">Item Name</td>
				<td style="font-weight:bold;font-size:13px">Batch</td>
			   <td align="right" style="font-weight:bold;font-size:13px">Quantity</td>
				<td align="right" style="font-weight:bold;font-size:13px">User</td>
           </tr>
           
		
             <?php 
              $i=1;
              $vttl=0;
			$i=1;
			if($sbstrid==0)
			{
				
				$qdate=mysqli_query($link,"select distinct date  from `inv_substore_issue_details`  where date between '$fdate' and '$tdate' order by slno   ");
				
				
			}
			else
			{
				$qdate=mysqli_query($link,"select distinct a.date from inv_substore_issue_details a,inv_substore_issue_master b where a.date between '$fdate' and '$tdate'  and a.issue_no=b.issue_no and  b.substore_id='$sbstrid' order by a.slno   ");
				
								
			}
			
				
			while($qdate1=mysqli_fetch_array($qdate))
			{
				
			 ?>
			 <tr>
				 <td colspan="9"><i>Issue Date : <?php echo  convert_date($qdate1['date']);?></i></td>
			 </tr>
				<?php	

				if($sbstrid==0)
				{
				 
				   $q=mysqli_query($link,"SELECT a.*,b.substore_id,b.issue_to,b.user  FROM inv_substore_issue_details a,inv_substore_issue_master b  WHERE a.date='$qdate1[date]'  and  a.issue_no=b.issue_no  order by a.date");
				
				}
				else
				{
				 
				 
				 $q=mysqli_query($link,"SELECT a.*,b.substore_id,b.issue_to,b.user  FROM inv_substore_issue_details a,inv_substore_issue_master b  WHERE a.date='$qdate1[date]' and  a.issue_no=b.issue_no and b.substore_id='$sbstrid'  order by a.date");
				}	
				while($r=mysqli_fetch_array($q))
				{
				$qitem=mysqli_fetch_array(mysqli_query($link,"SELECT item_name FROM item_master  WHERE item_id='$r[item_id]' "));
				$qsubstor=mysqli_fetch_array(mysqli_query($link,"SELECT substore_name FROM inv_sub_store  WHERE substore_id='$r[substore_id]' "));
				$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
				$vttl+=$r['issue_qnt'];

				?>
             <tr class="line">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo $r['issue_no'];?></td>
					<td style="font-size:13px"><?php echo convert_date($r['date']);?></td>
					<td style="font-size:13px"><?php echo convert_time($r['time']);?></td>
					
					<td style="font-size:13px"><?php echo substr($r['issue_to'],0,15);?></td>
					<td style="font-size:13px"><?php echo $qsubstor['substore_name'];?></td>
					<td style="font-size:13px"><?php echo $qitem['item_name'];?></td>
					<td style="font-size:13px"><?php echo $r['batch_no'];?></td>
					<td align="right" style="font-size:13px"><?php echo number_format($r['issue_qnt'],0);?></td>
					<td align="right" style="font-size:13px"><?php echo $quser['name'];?></td>
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			
			 
			  ?>
             
             <?php
		 }?>
               
              
<!--
<tr class="line">
	<td colspan="7" style="text-align:right;font-weight:bold;font-size:13px">Total :</td>
	<td style="text-align:right;font-weight:bold;font-size:13px"><?php echo number_format($vttl,0);?></td>
	<td>&nbsp;</td>
	
</tr>
-->



</table>  
       
    
  
   </form>
 
 </div>  
</body>
</html>

