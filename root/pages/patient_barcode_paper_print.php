<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
$top_line_break=2;


$uhid=mysqli_real_escape_string($link, base64_decode($_GET['uhid']));
$opd_id=mysqli_real_escape_string($link, base64_decode($_GET['ipd']));
$user=mysqli_real_escape_string($link, base64_decode($_GET['user']));

$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
$paddrss=substr($pat_info['city'],0,35);
if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Paper Barcode</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<script src="../../js/jquery.min.js"></script>
</head>

<body onafterprint="window.close()" onkeyup="close_window(event)">
	<div class="container-fluid" style="padding: 1;">
		<div>
			<br><br>
			<table class="table table-condensed"  >
				
			<?php
			    
				for($n=1;$n<=8;$n++)
				{
					
			?>
			     <tr style="height:127px;">
					<td >
						<span>Name :<?php echo $pat_info["name"]; ?></span><br>
						<span>Unit No: <?php echo $pat_info["patient_id"]; ?></span><br>
						<span>IPd No: <?php echo $pat_reg["opd_id"]; ?> &nbsp;Reg Date:<?php echo convert_date($pat_reg['date']);?></span><br>
						<span> <?php echo $paddrss; ?></span><br>
						<span>
							<img src="../../barcode-master/barcode.php?f=jpg&s=code-128&d=<?php echo $pat_reg["opd_id"];?>&h=45ms=r&tc=white" style="margin-left: -15px;margin-top: -3px;">
						</span>
					</td>
					

					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</td>

					
                 <td >
						<span>Name :<?php echo $pat_info["name"]; ?></span><br>
						<span>Unit No: <?php echo $pat_info["patient_id"]; ?></span><br>
						<span>IPd No: <?php echo $pat_reg["opd_id"]; ?> &nbsp;Reg Date:<?php echo convert_date($pat_reg['date']);?></span><br>
						<span> <?php echo $paddrss; ?></span><br>
						<span>
							<img src="../../barcode-master/barcode.php?f=jpg&s=code-128&d=<?php echo $pat_reg["opd_id"];?>&h=45ms=r&tc=white" style="margin-left: -15px;margin-top: -3px;">
						</span>
					</td>
					
					</tr>
			<?php
				}
			?>
				

<!--
			 <tr>	
			<?php
				for($n=1;$n<=2;$n++)
				{
			?>
			    
					<td>
						<span><?php echo $pat_info["name"]; ?></span><br>
						<span><?php echo $pat_info["patient_id"]; ?></span><br>
						<span><?php echo $pat_reg["opd_id"]; ?></span><br>
						<span>
							<img src="../../barcode-master/barcode.php?f=jpg&s=code-128&d=<?php echo $pat_reg["opd_id"];?>&h=45ms=r&tc=white" style="margin-left: -15px;margin-top: -4px;">
						</span>
					</td>
					
			<?php
				}
			?>
				</tr>
-->

			
			
			</table>
		</div>
	</div>
</body>
<span id="user" style="display:none;"><?php echo $user; ?></span>
</html>
<script>
	$(document).keydown(function (event) {
		if (event.keyCode == 123 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent F12
			return false;
		} else if (event.ctrlKey && event.shiftKey && event.keyCode == 73 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent Ctrl+Shift+I        
			return false;
		}
	});
	$(document).on("contextmenu",function(e){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			e.preventDefault();
		}
	});
	
	//window.print();
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
<style>
.table th, .table td
{
	line-height: 15px;
	font-weight: bold;
}
.table th, .table td
{
	border-top: none !important;
}
@media print{
	.noprint{
		display:none;
	}
}
@page{
	margin: 0.2cm;
}
</style>

