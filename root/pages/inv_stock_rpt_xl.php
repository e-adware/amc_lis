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

function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}
	
	
	
?>
<table>
<tr><td colspan="5" style="font-size:13px">Print Date : <?php echo date('d/m/Y');?></td></tr>
<tr><td colspan="5" style="font-size:13px">Category : <?php echo $vcatname;?></td></tr>
</table>

<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="5" style="text-align:center;"><h4>Available Stock</h4></th>
	</tr>
	<tr>
		<th>#</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><th>Expiry Date</th><th>Available Stock</th>
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
	<tr>
		<td><?php echo $i;?></td>
		<td><?php echo $vitmid;?></td>
		<td><?php echo $r['item_name'];?></td>
		<td><?php echo $r['batch_no'];?></td>
		<td><?php echo convert_date($vexpiry);?></td>
		<td><?php echo $vclsngqnt;?></td>
		
	</tr>
<?php
$i++;
}
?>

	
</table>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
