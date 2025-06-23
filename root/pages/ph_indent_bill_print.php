<html>
<head>
<title>IPD Medicine Indent Details</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
</head>
<body onkeydown="close_window(event)" onafterprint="window.close()">
<?php
include'../../includes/connection.php';
include'../../includes/global.function.php';

$pid=(base64_decode($_GET['pId']));
$opd=(base64_decode($_GET['iPd']));
$ino=(base64_decode($_GET['iNo']));

$vmnyrcpt="IPD INDENT DETAILS";
$pat=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `patient_info` WHERE `patient_id`='$pid'"));
$ward_info=mysqli_fetch_array(mysqli_query($link, " SELECT a.`bed_id`,b.`name` FROM `ipd_pat_bed_details` a,ward_master b WHERE a.`patient_id`='$pid' and a.ipd_id='$opd' and a.ward_id =b.ward_id  "));
$bed=mysqli_fetch_array(mysqli_query($link, "SELECT `bed_no` FROM `bed_master` WHERE `bed_id`='$ward_info[bed_id]'"));
$emp=mysqli_fetch_array(mysqli_query($link, "SELECT a.`name`, b.`date`, b.`time` FROM `employee` a, `ipd_pat_medicine_indent` b WHERE a.`emp_id`=b.`user` AND b.`patient_id`='$pid' AND b.`ipd_id`='$opd' AND b.`indent_num`='$ino' LIMIT 0,1"));
?>
<div class="container-fluid">
	<div class="" style="">
		<?php include('page_header_ph.php'); ?>
		<br>
		<div style="text-align: center;font-weight: bold;font-size: 13px;"><u><?php echo $vmnyrcpt; ?></u></div>
		<br/>
	</div>
<table class="table table-condensed table-no-top-border" width="100%">
	<tr>
		<th> Name</th><td>: <?php echo $pat['name'];?></td>
	</tr>
	<tr>
		<th>IPD ID</th><td>: <?php echo $opd;?></td>
	</tr>
	<tr>
		<th>Indent No.</th><td>: <?php echo $ino;?></td>
	</tr>
	<tr>
		<th> Ward (Bed)</th><td>: <?php echo $ward_info['name']." (".$bed['bed_no'].")";?></td>
	</tr>
</table>

<hr style="margin: 0;border-top: 1px solid #000;">
<table class="table table-condensed table-no-top-border" id="item_list">
	<tr>
		<th style="width: 30px;">#</th>
		<th>Medicine Name</th>
		<th>Request</th>
	</tr>
	<?php
	$n=1;
	$tot="";
	$qry=mysqli_query($link,"SELECT `item_code`,`quantity` FROM `ipd_pat_medicine_indent` WHERE `patient_id`='$pid' AND `ipd_id`='$opd' AND `indent_num`='$ino'");
	while($r=mysqli_fetch_array($qry))
	{
		$it=mysqli_fetch_array(mysqli_query($link,"select `item_name`,`hsn_code`,`manufacturer_id` from `item_master` where `item_id`='$r[item_code]'"));
	?>
	<tr>
		<td><?php echo $n++;?></td>
		<td><?php echo $it['item_name'];?></td>
		<td><?php echo $r['quantity'];?></td>
	</tr>
	<?php
	}
	?>
	<tr class="bline">
		<td colspan="3"></td>
	</tr>
</table>
<hr style="margin: 0px;margin-bottom:5px;border-top: 1px solid #000;">
<i style="font-size:12px;">Requested by : <?php echo $emp['name']." &nbsp;&nbsp;&nbsp; Date : ".convert_date($emp['date']).", Time : ".convert_time($emp['time']);?></i>
</div>
<script>
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		
		if(unicode==27)
		{
			window.close();
		}
	}
	//window.print();
</script>
</body>
</html>
<style>
	*{
		line-height:10px;
	}
	.table
	{
		margin-bottom: 0px;
	}
	@page
	{
		//margin: 0.0cm;
		//margin-left: 0.2cm;
	}
	.bline
	{
		//border-top:1px solid #000 !important;
	}
	.table-condensed th, .table-condensed td {
		padding: 0px !important;
	}
	.table th, .table td
	{
		//line-height: 18px;
	}
</style>
