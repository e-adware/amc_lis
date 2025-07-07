<?php

include("../../includes/connection.php");
include("../../includes/global.function.php");

$date1 = base64_decode($_GET['fDt']);
$date2 = base64_decode($_GET['tDt']);
$dept = base64_decode($_GET['dEp']);
$type = base64_decode($_GET['tYp']);

if($type==1)
{
	$pageHeader="Department Wise Report";
}
if($type==2)
{
	$pageHeader="Department Wise Report";
}
if($type==3)
{
	$pageHeader="Patient Report Records";
}
?>
<html>

<head>
    <title><?php echo $pageHeader;?></title>
    <link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../../css/custom.css" type="text/css" rel="stylesheet" />
    <script src="../../js/jquery.min.js"></script>
	<link rel="stylesheet" href="../../css/loader.css" />
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.css" />
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)">
    <div class="container-fluid">
        <div class="row">
            <div class="">
                <?php include('page_header.php'); ?>
            </div>
        </div>
        <div class="noprint" style="text-align:center;">
			<button type="button" class="btn btn-success" onclick="window.print();">Print</button>
			<button type="button" class="btn btn-danger" onclick="window.close();">Exit</button>
        </div>
        <div style="text-align:center;font-size:18px;"><?php echo $pageHeader;?></div>
        <div style="text-align:right;font-size:10px;">Print Time : <?php echo date("d M Y h:i A"); ?></div>
        <div id="result"></div>
    </div>
    <input type="hidden" id="fdt" value="<?php echo $date1; ?>" />
    <input type="hidden" id="tdt" value="<?php echo $date2; ?>" />
    <input type="hidden" id="dep" value="<?php echo $dept; ?>" />
    <input type="hidden" id="typ" value="<?php echo $type; ?>" />

<!-- Loader -->
<div id="loader" style="position:fixed;margin-top:1%;left:50%;"></div>
<style>
	@media print
	{
		.noprint {
			display: none !important;
		}
    }
    .table-condensed tr th, .table-condensed tr td
    {
		font-size: 13px;
		padding: 2px;
	}
	#test_data
	{
		margin:0px;
		padding:0px;
	}
	#test_data tr td
	{
		border: 0px;
		border-bottom: 1px solid #666 !important;
		padding: 1px;
	}
</style>
<script>
    $(document).ready(function () {
        view();
    });

	function close_window(e)
	{
		if(e.keyCode==27)
		{
			window.close();
		}
	}
    function view() {
		$("#loader").show();
        $.post("report_dep_wise_ajax.php",
		{
			fdate: $("#fdt").val().trim(),
			tdate: $("#tdt").val().trim(),
			dep: $("#dep").val().trim(),
			type: $("#typ").val().trim(),
		},
		function (data, status) {
			$("#loader").hide();
			$("#result").html(data);
			$(".btn_act").hide();
		});
    }
</script>
</body>
</html>