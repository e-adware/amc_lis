<?php

include("../../includes/connection.php");
include("../../includes/global.function.php");

$date1 = base64_decode($_GET['date1']);
$date2 = base64_decode($_GET['date2']);
?>
<html>

<head>
    <title>Rejected Sample Report</title>
    <link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../../css/custom.css" type="text/css" rel="stylesheet" />
    <script src="../../js/jquery.min.js"></script>
</head>

<body onafterprint="window.close();" onkeypress="close_window(event)">
    <div class="container-fluid">
        <div class="row">
            <div class="">
                <?php include('page_header.php'); ?>
            </div>
        </div>
        <hr>
        <center>
            <h4>Rejected Sample Report</h4>
        </center>
        <center>
			<div class="noprint">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()"> 
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
        <div id="result"></div>
    </div>
    <input type="hidden" id="from" value="<?php echo $date1; ?>">
    <input type="hidden" id="to" value="<?php echo $date2; ?>">
</body>

</html>
<style>
.table-condensed tr th, .table-condensed tr td
{
	padding: 2px;
	font-size: 12px;
}
.text-justified {
  text-align: justify;
}
@media print
{
	body {
		padding: 0px;
		margin: 0px;
	}
	.noprint{
		display:none;
	}
}
</style>

<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../../css/loader.css" />
<script>
    $(document).ready(function () {
        $("#loader").hide();
        view();

    });

    function view() {
        $.post("rejected_sample_data.php",
            {
                date1: $("#from").val(),
                date2: $("#to").val(),
                type: 1
            },
            function (data, status) {
                $("#result").html(data);
                $(".print_div").hide();
            });
    }
    function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>