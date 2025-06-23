<?php
include("../../includes/connection.php");

$user=$_GET["user"];
$category_id=$_GET["category_id"];

if($category_id>0)
{
	$qry="SELECT * FROM `stock_sub_category_master` WHERE `category_id`='$category_id' ORDER BY `sub_category_name` ";
}else
{
	$qry="SELECT * FROM `stock_sub_category_master` ORDER BY `sub_category_name` ";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Stock Sub Category Print</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
</head>

<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div>
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<center><b>Stock Sub Category Print</b></center>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>#</th>
				<th>Category</th>
				<th>Sub Category</th>
			</tr>
	<?php
		$n=1;
		$sub_category_qry=mysqli_query($link, $qry);
		while($sub_category=mysqli_fetch_array($sub_category_qry))
		{
			$stock_category=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `stock_category_master` WHERE `category_id`='$sub_category[category_id]' "));
			
			echo "<tr><td>$n</td><td>$stock_category[category_name]</td><td>$sub_category[sub_category_name]</td></tr>";
			
			$n++;
		}
	?>
		</table>
	</div>
	<span id="user" style="display:none;"><?php echo $user; ?></span>
</body>
</html>
<script>
	$(document).ready(function(){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			//window.print();
		}
	});
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
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
}
.table
{
	margin-bottom:5px;
}
hr
{
	margin:0;
	border-bottom:0;
	border-top: 1px solid #000;
}

.page_break {page-break-before: always; padding-top: 5px;}
.req_slip{ min-height:520px;}
.f_req_slip{ min-height:500px;}
.rad_req_slip{ min-height:300px;}
.large_text tbody > tr > td
{
	padding:5px;
}
*
{
	font-size:13px;
}
</style>
