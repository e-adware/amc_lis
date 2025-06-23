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
	$encounter=$_GET['encounter'];
	$user_entry=$_GET['user_entry'];
	
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	$type="";
	if($encounter>0)
	{
		$type=" and `type`='$encounter'";
	}
	
?>
<html>
<head>
	<title>Patient Cancel Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Patient Cancel Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-bordered table-condensed table-report">
			<tr>
				<th>#</th>
				<th>PIN</th>
				<!--<th>Bill No</th>-->
				<th>Name</th>
				<th>Cancel date</th>
				<th><span class="text-right">Bill Amount</span></th>
				<th><span class="text-right">Reason</span></th>
				<th><span class="text-right">User</span></th>
				<th><span class="text-right">Encounter</span></th>
			</tr>
			<?php
					$i=1;
					$tot_cash=$cashamt=0;
					$disamt=$discount=0;
					$patientdel=mysqli_query($link, "SELECT * FROM `patient_cancel_reason` WHERE `date` between '$date1' and '$date2' $user $type order by `date` ");
					
					while($d=mysqli_fetch_array($patientdel))
					{
						$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$d[type]' "));
						$encounter=$pat_typ_text['p_type'];
						
						$bill_amt_each=0;
						
						if($d["type"]==1) // OPD
						{
							$pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details_cancel where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
							$bill_amt_each=$pay['tot_amount'];
							$cashamt=$cashamt+$pay['tot_amount'];
							$disamt=$disamt+$pay['dis_amt'];
							$discount=$pay['dis_amt'];
							//$encounter="OPD";
						}
						if($d["type"]==2) // LAB
						{
							$pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details_cancel where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
							$bill_amt_each=$pay['tot_amount'];
							$cashamt=$cashamt+$pay['tot_amount'];
							$disamt=$disamt+$pay['dis_amt'];
							$discount=$pay['dis_amt'];
							//$encounter="Lab";
						}
						if($d["type"]==3) // IPD
						{
							$pay=mysqli_fetch_array(mysqli_query($link, "select * from ipd_advance_payment_details_cancel where patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
							$bill_amt_each=$pay['tot_amount'];
							$amt=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`amount`) as sum FROM `ipd_pat_service_details_cancel` WHERE patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
							//$cashamt=$cashamt+$pay['tot_amount'];
							$bill_amt_each=$amt['sum'];
							$cashamt=$cashamt+$amt['sum'];
							$disamt=$disamt+$pay['discount'];
							$discount=$pay['discount'];
							//$encounter="IPD";
							//$bill_no=$pay["bill_no"];
						}
						if($d["type"]>3) // Casualty
						{
							$pay=mysqli_fetch_array(mysqli_query($link, "select * from ipd_advance_payment_details_cancel where patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
							$amt=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`amount`) as sum FROM `ipd_pat_service_details_cancel` WHERE patient_id='$d[patient_id]' and ipd_id='$d[opd_id]'"));
							//$cashamt=$cashamt+$pay['tot_amount'];
							$bill_amt_each=$amt['sum'];
							$cashamt=$cashamt+$amt['sum'];
							$disamt=$disamt+$pay['discount'];
							$discount=$pay['discount'];
							//$encounter="Casualty";
							//$bill_no=$pay["bill_no"];
						}
						
						
						$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
			?>
					<tr>
						<td><?php echo $i;?></td>
						<td><?php echo $d['opd_id'];?></td>
						<!--<td><?php echo $bill_no;?></td>-->
						<td><?php echo $pat_info['name'];?></td>
						<td><?php echo convert_date($d['date']);?></td>
						<!--<td><span class="text-right"><?php echo $rupees_symbol.number_format($pay['tot_amount']-$discount,2);?></span></td>-->
						<td><span class="text-right"><?php echo $rupees_symbol.number_format($bill_amt_each,2);?></span></td>
						<td><span class="text-right"><?php echo $d['reason'];?></span></td>
						<td><span class="text-right"><?php echo $quser['name'];?></span></td>
						<td><span class="text-right"><?php echo $encounter;?></span></td>
					</tr>
			<?php
						$tot_cash=$tot_cash+$bill_amt_each;
						$i++;
					}
				?>
			<tr>
			  <td colspan="4"><span class="text-right"><strong>Total Cancel Amount :</strong></span></td>
			  <td><span class="text-right"><strong><?php echo $rupees_symbol.number_format($tot_cash,2);?> </strong></span></td>
			  <td colspan="4">&nbsp;</td>
			</tr>
		</table>
	</div>
</body>
</html>
<script>window.print();</script>
