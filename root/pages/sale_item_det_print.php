<?php
include('../../includes/connection.php');
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));
?>
<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
</head>
<body>

<div class="container-fluid">
	<div>
		<?php include('page_header_ph.php'); ?>
	</div>
	<center><h5><u>Sale Item(s) details</u></h5></center>
	<table>

	<tr><td colspan="5">From :<?php echo $fdate;?></td></tr>
	<tr><td colspan="5">To :<?php echo $tdate;?></td></tr>

	</table>	
	
<table class="table table-condensed table-bordered">
	
	<tr>
		<th>Sl No</th>
		<th>Item Code</th>
		<th>Item Name</th>
		<th>Batch No</th>
		<th>Date</th>
		<th>Open</th>
		<th>Added</th>
		<th>Sale</th>
		<th>Close</th>
	</tr>
<?php
$n=1;
$qry=mysqli_query($link,"SELECT DISTINCT `item_code` FROM `ph_stock_process` WHERE `sell`!='0' AND `date` BETWEEN '$fdate' AND '$tdate'");
while($res=mysqli_fetch_array($qry))
{
	$q=mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `item_code`='$res[item_code]' AND `date` BETWEEN '$fdate' AND '$tdate'");
	$num=mysqli_num_rows($q);
	while($r=mysqli_fetch_array($q))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
	?>
	<tr>
		<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$r['item_code']."</td><td rowspan='".$num."'>".$itm['item_name']."</td><td rowspan='".$num."'>".$r['batch_no']."</td>";}?>
		<td><?php echo $r['date'];?></td>
		<td><?php echo $r['s_available'];?></td>
		<td><?php echo $r['added'];?></td>
		<td><?php echo $r['sell'];?></td>
		<td><?php echo $r['s_remain'];?></td>
	</tr>
	<?php
	$num=0;
	}
	$n++;
}
?>
</table>
<script>
window.print();
</script>
 </div>  
</body>
</html>
