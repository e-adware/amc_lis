<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<script src="../../js/jquery.min.js"></script>
<style>
	
</style>

</head>
<body>
<?php
include'../../includes/connection.php';

function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-M-Y', $timestamp);
return $new_date;
}

$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
$ph=$_GET['ph'];

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header.php'); ?>
				<center><h5><u>Stock Expiry Report </u></h5></center>
			</div>
<table style="font-weight:bold;font-size:13px">
<tr><td>From</td><td>: <?php echo convert_date($fdate);?></td></tr>
<tr><td>To</td><td>: <?php echo convert_date($tdate);?></td></tr>
<tr><td>Print Date</td><td>: <?php echo date('d-M-Y');?></td></tr>
</table>
<input type='hidden' id="fdate" value='<?php echo $fdate;?>' />
<input type='hidden' id="tdate" value='<?php echo $tdate;?>' />
<input type='hidden' id="ph" value='<?php echo $ph;?>' />
<div class="noprint" style="text-align:center;">
	<input type="button" class="btn btn-success" name="button" id="button" value="Print" onClick="window.print()" />
	<input type="button" class="btn btn-danger" name="button" id="button" value="Exit" onClick="window.close()" />
</div>
<div id="res"></div>
</div>
<script>
	$(document).ready(function()
	{
		view_report();
	});
	function view_report()
	{
		$.post("sale_report_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			ph:$("#ph").val(),
			type:10,
		},
		function(data,status)
		{
			$("#res").html(data);
			$(".btn_act").hide();
		})
	}
</script>
<style>
	.table tr th, .table tr td
	{
		font-size:13px;
		padding:0px 5px;
	}
	@media print
	{
		.noprint
		{
			display:none;
		}
	}
</style>
</body>
</html>

