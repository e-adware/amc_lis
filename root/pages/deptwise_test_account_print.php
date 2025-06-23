<?php
include('../../includes/connection.php');

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
	$rupees_symbol="&#x20b9; ";
	
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$encounter=$_GET['encounter'];
	$encounter_val=$_GET['encounter'];
	$branch_id=$_GET['branch_id'];
	$user_entry=$_GET['user_entry'];
	
?>
<html>
<head>
	<title>Summary Account Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<!--<h4>Detail Account Report</h4>-->
			<div class="noprint ">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<?php
			$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
			$pat_typ_encounter=$pat_typ["p_type"];
			
			//echo "<b>Department: $pat_typ_encounter</b>";
		?>
		<!--<br>
		<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		<span style="float:right;">Printing time : <?php echo date("d-M-Y h:i A"); ?></span>-->
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date1; ?>">
	<input type="hidden" id="to" value="<?php echo $date2; ?>">
	<input type="hidden" id="encounter" value="<?php echo $encounter; ?>">
	<input type="hidden" id="user_entry" value="<?php echo $user_entry; ?>">
	<input type="hidden" id="branch_id" value="<?php echo $branch_id; ?>">
</body>
</html>
<script>
	$(document).ready(function(){
		view();
	});
	function view()
	{
		$.post("summary_account_detail_data_new.php",
		{
			type:"deptwise_test",
			date1:$("#from").val(),
			date2:$("#to").val(),
			encounter:$("#encounter").val(),
			user_entry:$("#user_entry").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			//$("#print_div").hide();
			$(".print_btn").hide();
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
	.txt_small{
	font-size:10px;
}
.table
{
	font-size: 11px;
}
@media print
{
	.account_close_div
	{
		display:none;
	}
	.noprint{
		display:none;
	 }
}
.ipd_serial
{
	display:none;
}
hr{
	margin: 0;
}
</style>
