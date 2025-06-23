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
	
	$encounter_str="";
	if($encounter!='0')
	{
		$encounter_str=" AND b.`type`='$encounter'";
	}
	$user_str="";
	if($user_entry!='0')
	{
		$user_str=" AND a.`user`='$user_entry'";
	}
	
	$encounter_pay_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type=$pat_typ_text["type"];
	}
	
	if($encounter==0 || $encounter_pay_type==1)
	{
		$con_pay_qry=mysqli_query($link, " SELECT a.`opd_id` FROM `consult_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`date` between '$date1' and '$date2' and a.`dis_amt`>0 $encounter_str $user_str ORDER BY a.`slno` ");
		while($con_pay=mysqli_fetch_array($con_pay_qry))
		{
			$all_pin[$i]=$con_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter_pay_type==2)
	{
		$inv_pay_qry=mysqli_query($link, " SELECT a.`opd_id` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`date` between '$date1' and '$date2' and a.`dis_amt`>0 $encounter_str $user_str ORDER BY a.`slno` ");
		while($inv_pay=mysqli_fetch_array($inv_pay_qry))
		{
			$all_pin[$i]=$inv_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter_pay_type==3)
	{
		$ipd_casual_pay_qry=mysqli_query($link, " SELECT `ipd_id` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`ipd_id`=b.`opd_id` and a.`date` between '$date1' and '$date2' and a.`discount`>0 $encounter_str $user_str ORDER BY a.`slno`");
		while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
		{
			$all_pin[$i]=$ipd_casual_pay["ipd_id"];
			$i++;
		}
	}
	sort($all_pin);
	//print_r($all_pin);
	
?>
<html>
<head>
	<title>Discount Account Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
</head>
<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Discount Account Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-bordered table-condensed table-report" style="font-size:13px;">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>PIN</th>
				<th>Patient Name</th>
				<th>Bill Amout</th>
				<th>Discount</th>
				<th>Reason</th>
				<th>User</th>
				<th>Encounter</th>
				<th>Date</th>
			</tr>
	<?php
		$n=1;
		$tot_bill=$tot_dis=$tot_paid=$tot_bal=0;
		foreach($all_pin as $all_pin)
		{
			if($all_pin)
			{
				if($encounter==0)
				{
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pin' "));
				}else if($encounter>0)
				{
					$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pin' AND `type`='$encounter' "));
				}
				
				if($encounter==$all_pat["type"] && $encounter>0 || $encounter==0)
				{
					$show_pat=0;
					$reason="";
					$bill_amt=$discount=$paid=$balance=0;
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$all_pat[patient_id]'"));
					
					$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
					$encounter=$pat_typ_text['p_type'];
					$encounter_str=$pat_typ_text['type'];
					
					if($encounter_str==1) // OPD
					{
						$pay_qry=mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$all_pat[patient_id]' and opd_id='$all_pat[opd_id]' and `dis_amt`>0");
						$pay_num=mysqli_num_rows($pay_qry);
						if($pay_num>0)
						{
							$show_pat=1;
							$pay=mysqli_fetch_array($pay_qry);
							$bill_amt=$pay['tot_amount'];
							$discount=$pay['dis_amt'];
							$paid=$pay['advance'];
							$balance=$pay['balance'];
							$ddate=$pay['date'];
							//$encounter="OPD";
							$reason=$pay['dis_reason'];
							$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay[user]' "));
						}
					}
					if($encounter_str==2) // LAB
					{
						$pay_qry=mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$all_pat[patient_id]' and opd_id='$all_pat[opd_id]' and `dis_amt`>0");
						$pay_num=mysqli_num_rows($pay_qry);
						if($pay_num>0)
						{
							$show_pat=1;
							$pay=mysqli_fetch_array($pay_qry);
							$bill_amt=$pay['tot_amount'];
							$discount=$pay['dis_amt'];
							$paid=$pay['advance'];
							$balance=$pay['balance'];
							$ddate=$pay['date'];
							//$encounter="Lab";
							$reason=$pay['dis_reason'];
							$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay[user]' "));
						}
					}
					
					if($encounter_str==3) // Casualty
					{
						$ipd_adv_dis_qry=mysqli_query($link, " SELECT `date`,`user` FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' AND `discount`>0 ");
						$ipd_adv_dis_num=mysqli_num_rows($ipd_adv_dis_qry);
						if($ipd_adv_dis_num>0)
						{
							$show_pat=1;
							$ipd_adv_dis=mysqli_fetch_array($ipd_adv_dis_qry);
							
							$pat_ser=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(sum(`amount`),0) as sum_tot_amt FROM `ipd_pat_service_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' "));
							$bill_amt=$pat_ser['sum_tot_amt'];
							
							$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' "));
							//$bill_amt=$tot_amountt['sum_tot_amt'];
							$discount=$tot_amountt['sum_dis'];
							$paid=$tot_amountt['sum_paid'];
							$balance=($bill_amt-$discount-$paid);
							
							$dis_reason=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discount_reason` WHERE `patient_id`='$all_pat[patient_id]' AND `ipd_id`='$all_pat[opd_id]' "));
							$reason=$dis_reason['reason'];
							
							$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_adv_dis[user]' "));
							$ddate=$ipd_adv_dis['date'];
						}
					}
					$tot_bill=$tot_bill+$bill_amt;
					$tot_dis=$tot_dis+$discount;
					$tot_paid=$tot_paid+$paid;
					$tot_bal=$tot_bal+$balance;
					if($show_pat==1)
					{
				?>
						<tr>
							<td><?php echo $n; ?></td>
							<td><?php echo $pat_info["patient_id"]; ?></td>
							<td><?php echo $all_pat["opd_id"]; ?></td>
							<td><?php echo $pat_info["name"]; ?></td>
							<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
							<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
							<td><?php echo $reason; ?></td>
							<td><?php echo $quser["name"]; ?></td>
							<td><?php echo $encounter; ?></td>
							<td><?php echo convert_date($ddate); ?></td>
						</tr>
					<?php
						$n++;
					}
				}
			}
		}
	?>
			<tr>
			  <td colspan="4"><span class="text-right"><strong>Total:</strong></span></td>
			  <td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bill),2);?> </strong></span></td>
			  <td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_dis),2);?> </strong></span></td>
			  <td colspan="4">&nbsp;</td>
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
