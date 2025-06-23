<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<style>
body {
	padding: 10px;
	/*font-size:15px; font-family: "Courier New", Courier, monospace; line-height: 18px;*/
}
.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
 .chk
 {
	background:#FFF;
	color:#000;
 }
}
.bline td{border-bottom:1px solid;}
.line td{border-top:1px dotted;}
</style>

</head>
<body>
<?php
include'../../includes/connection.php';


$rcv=base64_decode($_GET['rCv']);
$ord=base64_decode($_GET['oRd']);

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

$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from ph_challan_receipt_master WHERE order_no='$ord'")); 
$splr=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_master where id='$qrcv[supp_code]'"));
$qemname=mysqli_fetch_array(mysqli_query($link,"select a.name from employee a,ph_challan_receipt_master b where a.emp_id=b.user and b.order_no='$ord'")); 

$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name"));
$sub_id=1;
if($splr['igst']==1)
{
	$gsttxt="IGST";
}
else
{
	$gsttxt="GST";
}

?>
<div class="container">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Challan Received Report</u></h5></center>
			</div>

<table style="font-weight:bold;font-size:13px">

<tr><td>Challan No </td><td> : <?php echo $qrcv['challan_no'];?></td></tr>
<tr><td>Supplier </td><td> : <?php echo $splr['name'];?></td></tr>
<tr><td>Received Date </td><td> : <?php echo convert_date($qrcv['date'],2);?></td></tr>
<tr><td>Print Date </td><td> : <?php echo date('d-m-Y');?></td></tr>
</table>
<input type="hidden" id="ord" value="<?php echo $ord;?>" />
<input type="hidden" id="rcv" value="<?php echo $rcv;?>" />
<div class="noprint" style="text-align:center;">
	<input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="window.print()" />
	<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="window.close()" />
</div>
<center><input type="text" id="val" class="span5 noprint form-control" onkeyup="load_items()" placeholder="Search" /></center>
<div id="res"></div>
</div>
<style>
	.table-condensed tr th, .table-condensed tr td
	{
		padding: 1px;
	}
	.line:hover
	{
		cursor:pointer;
	}
	.chk, .chk:hover
	{
		background:#1A1313;
		color:#FFF;
	}
</style>
<script src="../../js/jquery.min.js"></script>
<script>
	$(document).ready(function()
	{
		$(document).keyup(function(e)
		{
			if(e.keyCode==27)
			window.close();
		});
		load_items();
	});
	function chk(ths)
	{
		if($(ths).hasClass("chk")==true)
		{
			$(ths).removeClass("chk");
		}
		else
		{
			$(ths).addClass("chk");
		}
	}
	function load_items()
	{
		$.post("ph_challan_receive_ajax.php",
		{
			ord:$("#ord").val().trim(),
			rcv:$("#rcv").val().trim(),
			val:$("#val").val().trim(),
			type:19,
		},
		function(data,status)
		{
			$("#res").html(data);
		});
	}
</script>
</body>
</html>

