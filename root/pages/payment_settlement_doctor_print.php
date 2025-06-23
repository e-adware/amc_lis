<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$consultantdoctorid=$_GET['con_cod_id'];
$dept_id=$_GET['dept_id'];
$branch_id=$_GET['branch_id'];

$str="SELECT a.* FROM `payment_settlement_doc` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id`";

if($date1 && $date2)
{
	$str.=" AND a.`date` BETWEEN '$date1' AND '$date2'";
}

if($consultantdoctorid)
{
	$str.=" AND a.`consultantdoctorid`='$consultantdoctorid'";
	
	$doc_info=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$consultantdoctorid' "));
}

if($dept_id)
{
	$str.=" AND b.`type`='$dept_id'";
}

if($dept_id)
{
	$str.=" AND a.`branch_id`='$branch_id'";
}
$str.=" ORDER BY a.`slno` ASC";

$qry=mysqli_query($link, $str);

?>
<html>
<head>
	<title>Doctor Payment Report</title>
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
			<h4>
				Doctor Payment Report
			<?php if($doc_info){ echo "<br>".$doc_info["Name"]; }else{ echo "<br>All Doctors"; } ?>
			</h4>
			<div class="noprint ">
				<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data">
			<table class="table table-condensed table-hover" style="background-color:white;">
				<thead class="table_header_fix">
					<tr>
						<th>#</th>
						<th>UHID</th>
						<th>Bill No</th>
						<th>Patient Name</th>
						<th>Fees Name</th>
						<th>Amount</th>
						<th>Date</th>
						<!--<th>User</th>-->
					</tr>
				</thead>
<?php
			$n=1;
			$total_amount=0;
			while($pat_reg=mysqli_fetch_array($qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $pat_reg["patient_id"]; ?></td>
					<td><?php echo $pat_reg["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $pat_reg["charge_name"]; ?></td>
					<td><?php echo number_format($pat_reg["amount"],2); ?></td>
					<td><?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?></td>
				</tr>
<?php
				$n++;
				$total_amount+=$pat_reg["amount"];
			}
?>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th style="text-align:right;">Total &nbsp;</th>
					<th><?php echo number_format($total_amount,2); ?></th>
					<th></th>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		//view();
		//$(".noprint").hide();
	});
	function view()
	{
		$("#loader").show();
		$.post("payment_settlement_doctor_data.php",
		{
			type:"doctor_account",
			date1:$("#from").val(),
			date2:$("#to").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			dept_id:$("#dept_id").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$("#print_div").hide();
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
	
	function close_window_child()
	{
		window.close();
	}
	function refreshParent()
	{
		window.opener.location.reload(true);
	}
</script>
<style type="text/css" media="print">
  @page { size: landscape; }
  
</style>
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
	.noprint1
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
.table
{
	margin-bottom: 0px;
}
.table-condensed th, .table-condensed td
{
	padding: 0px;
}
</style>
