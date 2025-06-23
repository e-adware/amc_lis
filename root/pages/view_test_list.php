<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$date=date("Y-m-d");
$time=date("H:i:s");

//$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Token List</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	
	<script src="../../js/jquery.dataTables.min.js"></script>
</head>

<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div>
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<center>
			<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="window.close()">
		</center>
		<div class="">
			<div class="span7">
				<div class="widget-box">
					<div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
						<h5>Test Rate</h5>
					</div>
					<div class="widget-content nopadding">
						<table class="table table-bordered data-table">
							<thead style="background: #ddd;">
								<tr>
									<th>Test ID</th>
									<th>Test Name</th>
									<th>Test Rate</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$tst_qry=mysqli_query($link, " SELECT `testid`,`testname`,`rate` FROM `testmaster` order by `testid` ");
								while($tst=mysqli_fetch_array($tst_qry))
								{
							?>
								<tr class="gradeX" onClick="sel_test('<?php echo $tst['testid'] ?>','<?php echo $tst['testname'] ?>','<?php echo $tst['rate'] ?>')" style="cursor:pointer;">
									<td><?php echo $tst["testid"]; ?></td>
									<td><?php echo $tst["testname"]; ?></td>
									<td>&#x20b9; <?php echo $tst["rate"]; ?></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<input type="hidden" id="selected_test">
			<input type="hidden" id="selected_test_id">
			<div class="span4" id="out_pat_sel">
				
			</div>
		</div>
	</div>
</body>
</html>
<script>
	$(document).ready(function(){
		$('.data-table').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"sDom": '<""l>t<"F"fp>'
		});
	});
	function sel_test(id,name,rate)
	{
		var ww=$("#selected_test_id").val();
		
		if (ww.indexOf(id) > -1)
		{
			alert("Already added");
			return true;
		}
		
		var tid=ww+'@@'+id;
		$("#selected_test_id").val(tid);
		
		var qq=$("#selected_test").val();
		var tval=qq+'###'+name+'@@'+rate+'@@'+id;
		$("#selected_test").val(tval);
		load_sel_test();
	}
	function load_sel_test()
	{
		$.post("view_test_data.php",
		{
			type:"out_pat_test_rate",
			all_tst:$("#selected_test").val(),
		},
		function(data,status)
		{
			$("#out_pat_sel").html(data);
		})
	}
	function clear_cart()
	{
		$("#selected_test_id").val('');
		$("#selected_test").val('');
		load_sel_test();
	}
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
*{
	font-size:13px;
}
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
</style>
