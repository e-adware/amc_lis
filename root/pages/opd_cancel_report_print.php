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
	$rupees_symbol="&#x20b9; ";
	
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$con_cod_id=$_GET['con_cod_id'];
	$dept_id=$_GET['dept_id'];
	if($con_cod_id==0)
	{
		$qry=" SELECT * FROM `appointment_book` WHERE `date` between '$date1' and '$date2' ";
	}else
	{
		$qry=" SELECT * FROM `appointment_book` WHERE `date` between '$date1' and '$date2' and `consultantdoctorid`='$con_cod_id' ";
	}
	$dept_str="";
	if($dept_id>0)
	{
		$dept_str=" AND `consultantdoctorid` in (SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `dept_id`='$dept_id')";
	}
	
	$qry.=$dept_str;
	//echo $qry;
	$pat_reg_qry=mysqli_query($link, $qry );
	
	//$pat_reg_num=mysqli_num_rows($pat_reg_qry);
	
?>
<html>
<head>
	<title>OPD Cancel Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
</head>
<body style="width:100%" onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>OPD Cancel Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-bordered table-condensed table-report" style="font-size: 14px;">
			<tr>
				<th>#</th>
				<th>UHID / Bill No</th>
				<th> Name</th>
				<th>Cancel date</th>
				<th><span class="text-right">Bill Amount</span></th>
				<th><span class="text-right">Reason</span></th>
				<th><span class="text-right">User</span></th>
			</tr>
			<?php
					$i=1;
					$cashamt=0;
					$patientdel=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `patient_cancel_reason` WHERE `type`='1' and `date` between '$date1' and'$date2' order by `date`  ");
					
					while($d=mysqli_fetch_array($patientdel))
					{
						$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$d[patient_id]'"));
						
						$pay=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from consult_patient_payment_details_cancel where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
						$cashamt=$cashamt+$pay['tot_amount'];
						$quser=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
			?>
					<tr>
						<td><?php echo $i;?></td>
						<td><?php echo $pat_info['uhid']." / ".$d['opd_id'];?></td>
						<td><?php echo $pat_info['name'];?></td>
						<td><?php echo convert_date($d['date']);?></td>
						<td><span class="text-right"><?php echo "&#x20b9; ".number_format($pay['tot_amount'],2);?></span></td>
						<td><span class="text-right"><?php echo $d['reason'];?></span></td>
						<td><span class="text-right"><?php echo $quser['name'];?></span></td>
					</tr>
			<?php
						$i++;
					}
				?>
			<tr>
			  <td colspan="4"><span class="text-right"><strong>Total Cancel Amount :</strong></span></td>
			  <td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($cashamt,2);?> </strong></span></td>
			  <td colspan="2">&nbsp;</td>
			</tr>
		</table>
	</div>
</body>
</html>
<script>
	window.print();
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
				window.close();
		}
	}
</script>
