<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
<style>
.table-condensed tr th, .table-condensed tr td
{
	padding: 1px 1px 1px 3px;
}
@media print{
 .noprint{
	 display:none;
 }
}
@page
{
	margin-left: 1px;
	margin-right: 1px;
}
</style>

</head>
<body onkeydown="close_window(event)">
<?php
include'../../includes/connection.php';


$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
$ph=$_GET['ph'];
$billtype=$_GET['billtype'];

//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}


function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}


$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>

<div class="container-fluid">
	<input type="hidden" id="fdate" value="<?php echo $fdate;?>" />
	<input type="hidden" id="tdate" value="<?php echo $tdate;?>" />
	
	<div>
		<?php include('page_header.php'); ?>
	</div>
	<center><h5><u>Performance index Report</u></h5></center>
	
<table>
 <tr ><td style="font-weight:bold;font-size:13px">From </td><td style="font-weight:bold;font-size:13px"> : <?php echo $fdate;?></td></tr>
 <tr><td style="font-weight:bold;font-size:13px">To  <td style="font-weight:bold;font-size:13px"> : <?php echo $tdate;?></td></tr>

</table>
<table width="100%">
<tr>
<td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" class="btn btn-default" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" class="btn btn-default" id="button" value="Exit" onClick="javascript:window.close()" /></div></td>
</tr>
</table> 
      <div id="res"></div>
 </div>
 <script src="../../js/jquery.min.js"></script>
 <script>
	$(document).ready(function()
	{
		load_data();
	})
	
	function load_data()
	{
		$.post("performance_index_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:1,
		},
		function(data,status)
		{
			$("#res").html(data);
			$(".btn-primary").hide();
			$(".btn-success").hide();
		})
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
 <style>
	.table
	{
		font-size:13px;
	}
 </style>
</body>
</html>

