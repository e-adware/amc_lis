<?php
include'../../includes/connection.php';
$type=$_POST['type'];
$date=date("Y-m-d");
$time=date("H:i:s");
$c_yr=date("Y");
$c_mn=date("m");


// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

if($type=="exp_items")
{
	$usr=$_POST['usr'];
	$u=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$usr'"));
	if($u['levelid']==1 || $u['levelid']==16)
	{
		
		$q="SELECT * FROM `ph_stock_master` WHERE `quantity`>0 and substore_id=1 AND `exp_date` like '$c_yr-$c_mn-%'";
		//$q1="SELECT DISTINCT `item_id` FROM `stock_master` WHERE `quantity`>0 AND `quantity`<10";
		//$q="SELECT * FROM `ph_stock_master` WHERE `quantity`>0 AND `exp_date` like '$c_yr-$c_mn-%' AND `exp_date`>='$date'";
         
		$qry=mysqli_query($link,$q);
		//$qry1=mysqli_query($link,$q1);
		$num=mysqli_num_rows($qry);
		//$num1=mysqli_num_rows($qry1);
		$num1=0;
		echo $num."@@".$num1;
	}
}

if($type=="notify_alert")
{
	?>
	<table class="table table-condensed table-bordered">
		<tr style="background:#ddd;">
			<th>#</th>
			<th>Item Name </th>
			<th>Batch No.</th>
			<th>Expiry</th>
			<th>Stock Quantity</th>
		</tr>
	<?php
	
	$qstore=mysqli_query($link,"SELECT DISTINCT a.`substore_id`,b.substore_name FROM `ph_stock_master` a,ph_sub_store b WHERE a.`substore_id`=b.`substore_id` and b.`substore_id`<3 order by b.substore_name");
	while($qstore1=mysqli_fetch_array($qstore))
	{
		?>
		<tr>
			<th colspan="5">Name : <?php echo $qstore1['substore_name'];?> </th>
		</tr>
		<?php
	$i=1;
	$q="SELECT * FROM `ph_stock_master` WHERE `quantity`>0 and substore_id='$qstore1[substore_id]' AND `exp_date` like '$c_yr-$c_mn-%'";
	//$q="SELECT * FROM `ph_stock_master` WHERE `quantity`>0 AND `exp_date` like '$c_yr-$c_mn-%' AND `exp_date`>='$date'";
	$qry=mysqli_query($link,$q);
	$num=mysqli_num_rows($qry);
	while($r=mysqli_fetch_array($qry))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td style="background:#F5D6D2;"><?php echo substr(convert_date($r['exp_date']),3,8);?></td>
			<td style="text-align:left;"><?php echo $r['quantity'];?></td>
		</tr>
		<?php
		$i++;
	}
	?>
	<?php
   }?>
	</table>
	<?php
}

if($type=="notify_low_alr")
{
	?>
	<table class="table table-condensed table-bordered">
		<tr style="background:#ddd;">
			<th>#</th>
			<th>Item Name</th>
			<th>Batch No.</th>
			<th>Stock Quantity</th>
		</tr>
	<?php
	$i=1;
	$q="SELECT DISTINCT `item_code` FROM `ph_stock_master` WHERE `quantity`>0 AND `quantity`<10";
	//$q="SELECT * FROM `ph_stock_master` WHERE `quantity`>0 AND `exp_date` like '$c_yr-$c_mn-%' AND `exp_date`>='$date'";
	$qry=mysqli_query($link,$q);
	$num=mysqli_num_rows($qry);
	while($r=mysqli_fetch_array($qry))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
		$qq=mysqli_query($link,"SELECT * FROM `ph_stock_master` WHERE `quantity`>0 AND `quantity`<10 AND `item_code`='$r[item_code]'");
		$nn=mysqli_num_rows($qq);
		while($rr=mysqli_fetch_array($qq))
		{
		?>
		<tr>
			<?php
			if($nn>0)
			{
			?>
			<td rowspan="<?php echo $nn;?>"><?php echo $i;?></td>
			<td rowspan="<?php echo $nn;?>"><?php echo $itm['item_name'];?></td>
			<?php
			$nn=0;
			}
			?>
			<td><?php echo $rr['batch_no'];?></td>
			<td style="text-align:left;"><?php echo $rr['quantity'];?></td>
		</tr>
		<?php
		}
		$i++;
	}
	?>
	</table>
	<style>
		tr:hover
		{
			background:none;
		}
	</style>
	<?php
}

?>
