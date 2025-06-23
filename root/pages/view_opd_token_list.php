<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$branch_id=mysqli_real_escape_string($link, $_GET["bid"]);

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
</head>

<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div>
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<center><h5>Token List</h5></center>
		<table class="table">
			<tr>
	<?php
		$distinct_doc_qry=mysqli_query($link, " SELECT DISTINCT a.`consultantdoctorid`, b.`Name` FROM `appointment_book` a, `consultant_doctor_master` b WHERE a.`consultantdoctorid`=b.`consultantdoctorid` AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `date`='$date' AND `branch_id`='$branch_id') ORDER BY b.`Name` ");
		while($distinct_doc=mysqli_fetch_array($distinct_doc_qry))
		{
			$tot_pat_num=0;
			$tot_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `consultantdoctorid`='$distinct_doc[consultantdoctorid]' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `date`='$date' AND `branch_id`='$branch_id') "));
	?>
		<th>
			<?php echo $distinct_doc["Name"]." <span style='margin-left: 10%;'>Total:</span> ".$tot_pat_num; ?>
			<table class="table">
		<?php
			$opd_pat_qry=mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `consultantdoctorid`='$distinct_doc[consultantdoctorid]' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `date`='$date' AND `branch_id`='$branch_id') ");
			while($opd_pat=mysqli_fetch_array($opd_pat_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$opd_pat[patient_id]' "));
			?>
				<tr>
					<td style="width: 60%;"><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $opd_pat["appointment_no"]; ?></td>
				</tr>
			<?php
			}
		?>	
			</table>
		</th>
	<?php
		}
	?>
			</tr>
		</table>
	</div>
</body>
</html>
<script>
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
