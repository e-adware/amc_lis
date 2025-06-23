<?php
include('../../includes/connection.php');

$filename ="available_stock.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$catid=$_GET['catid'];
$category=mysqli_fetch_array(mysqli_query($link,"select * from inv_indent_type where inv_cate_id='$catid'  "));

if($catid==1)
	{
		$vcatname="Main Store";
	}
	else
	{
		$vcatname="Pharmacy";
	}
	
?>
<table>
<tr><td colspan="5" style="font-size:13px">Print Date : <?php echo date('d/m/Y');?></td></tr>

</table>

<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="5" style="text-align:center;"><h4>Item Re-Order Report</h4></th>
	</tr>
	<tr>
		<th>#</th><th>Item Code</th><th>Item Name</th><th>Re-Order Quantity</th><th> Stock</th>
	</tr>
<?php
$i=1;
$q=mysqli_query($link,"SELECT item_id,item_name,re_order FROM `item_master`  WHERE re_order>0 and need=0   order by item_name");
				while($r=mysqli_fetch_array($q))
				{

				$qphstk=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(quantity),0) as mxphstk from ph_stock_master where item_code='$r[item_id]' "));
				$qcntlstk=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(closing_stock),0) as mxcntrlstk from inv_maincurrent_stock where item_id='$r[item_id]' "));				

				$vttlstk=$qphstk['mxphstk']+$qcntlstk['mxcntrlstk'];
				if($vttlstk<$r['re_order'])
				{
			
			 ?>
             <tr class="line">
				<td style="font-size:13px"><?php echo $i;?></td>
				<td style="font-size:13px"><?php echo $r['item_id'];?></td>
				<td style="font-size:13px"><?php echo $r['item_name'];?></td>
				<td style="font-size:13px"><?php echo $r['re_order'];?></td>
				<td align="right" style="font-size:13px"><?php echo $vttlstk;?></td>
				
                  
             </tr>  
             
             
             <?php
	$i++;
	}
	?>
	
		<?php
	}?>
	
</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
