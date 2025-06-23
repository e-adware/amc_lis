<?php
include('../../includes/connection.php');
require('../../includes/global.function.php');

$rupees_symbol="&#x20b9; ";

$date1=$_GET['fdate'];
$date2=$_GET['tdate'];
$refbydoctorid=$_GET['doc'];
$head_id=$_GET['dept'];
$encounter=$_GET['encounter'];
$branch_id=$_GET['branch_id'];
$type=$_GET['type'];
	
$encounter_str="";
if($encounter>0)
{
	$encounter_str=" AND a.`type`='$encounter'";
}

$qry=mysqli_query($link,$q);
?>
<html>
<head>
	<title>Ref Doctor Tests List</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/loader.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body>
	<div class="container-fluid">
		<div class="">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center><h4>Referral Doctor Tests List</h4></center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
	<input type="hidden" id="refbydoctorid" value="<?php echo $refbydoctorid; ?>">
	<input type="hidden" id="head_id" value="<?php echo $head_id; ?>">
	<input type="hidden" id="encounter" value="<?php echo $encounter; ?>">
	<input type="hidden" id="branch_id" value="<?php echo $branch_id; ?>">
	<input type="hidden" id="type" value="<?php echo $type; ?>">
	
	<div id="loader"></div>
</body>
</html>
<script>
	$(document).ready(function(){
		view();
		$(".noprint").hide();
	});
	function view()
	{
		$.post("ref_doc_test_reports_data.php",
		{
			type:$("#type").val(),
			date1:$("#from").val(),
			date2:$("#to").val(),
			refbydoctorid:$("#refbydoctorid").val(),
			head_id:$("#head_id").val(),
			encounter:$("#encounter").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			$(".print_div").hide();
			$("#loader").hide();
		})
	}
</script>
<style>
@page
{
	margin: 0.2cm;
}
*
{
	font-size:11px;
}
</style>
