<?php
session_start();
include('../../includes/connection.php');

$c_user=trim($_SESSION['emp_id']);

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d M Y', $timestamp);
		return $new_date;
	}
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

$v=mysqli_real_escape_string($link, base64_decode($_GET["v"]));
$typ=mysqli_real_escape_string($link, base64_decode($_GET["typ"]));
$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uid"]));
$ipd_id=mysqli_real_escape_string($link, base64_decode($_GET["bid"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["usr"]));

?>
<html>

<head>
    <title><?php echo $typ." ".str_replace("/","-",$ipd_id); ?></title>
    <link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="../../font-awesome/css/font-awesome.css" />
    <link href="../../css/custom.css" type="text/css" rel="stylesheet" />
    <script src="../../js/jquery.min.js"></script>
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
    <div class="container-fluid">
        <div class="">
            <div class="">
                <?php include('page_header.php');?>
            </div>
        </div>
        <hr>
        <center>
            <h4><?php echo $typ; ?></h4>
            <!--<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>-->
            <div class="noprint ">
                <input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
                <input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
            </div>
        </center>
        <div id="load_data"></div>
        <p class="watermark1"><?php $watermark_text=$company_info["name"]; echo $watermark_text; ?></p>
    </div>
    <input type="hidden" id="typ" value="<?php echo $typ; ?>">
    <input type="hidden" id="uhid" value="<?php echo $uhid; ?>">
    <input type="hidden" id="ipd_id" value="<?php echo $ipd_id; ?>">
    <input type="hidden" id="user" value="<?php echo $user; ?>">
</body>

</html>
<script>
$(document).ready(function() {
    view();
    $(".noprint").hide();
});

function view() {
    $.post("mrd_reports_data.php", {
            type: $("#typ").val(),
            uhid: $("#uhid").val(),
            ipd_id: $("#ipd_id").val(),
            user: $("#user").val(),
        },
        function(data, status) {
            $("#load_data").html(data);
            $("#print_div").hide();
        })
}

function close_window(e) {
    var unicode = e.keyCode ? e.keyCode : e.charCode;

    if (unicode == 27) {
        window.close();
    }
}
</script>
<style type="text/css" media="print">
@page {
    size: landscape;
}
</style>
<style>
.txt_small {
    font-size: 10px;
}

.table {
    font-size: 11px;
}

@media print {
    .noprint1 {
        display: none;
    }

    .noprint {
        display: none;
    }

    .watermark1,
        {
        display: block;
    }
}

.table {
    margin-bottom: 0px;
}

.table-condensed th,
.table-condensed td {
    //padding: 0;
    padding: 0 10px 0 0;
}

hr {
    margin: 0;
    border: 1px solid #ddd;
}

.watermark1 {
    position: fixed;
    bottom: 60%;
    left: 10%;

    z-index: 0;
    display: block;

    opacity: 0.1;
    font-size: 80px;
    font-weight: bold;
    transform: rotate(315deg);
    -webkit-transform: rotate(315deg);

    display: none;
}

.page-break {
    page-break-after: always;
}
</style>