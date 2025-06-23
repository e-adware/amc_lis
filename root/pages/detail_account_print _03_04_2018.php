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
	
	// important
	$date11=$_GET['date1'];
	$date22=$_GET['date2'];
	
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	
	// Close account
		$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `date`='$date2' "));	
		if($check_close_account_today)
		{
			$con_max_slno_less=$check_close_account_today['con_slno'];
			$con_max_slno_str_less=" AND `slno`<=$con_max_slno_less ";
			
			$inv_max_slno_less=$check_close_account_today['inv_slno'];
			$inv_max_slno_str_less=" AND `slno`<=$inv_max_slno_less ";
			
			$ipd_max_slno_less=$check_close_account_today['ipd_slno'];
			$ipd_max_slno_str_less=" AND `slno`<=$ipd_max_slno_less ";
		}
		else
		{
			$con_max_slno_str_less="";
			$inv_max_slno_str_less="";
			$ipd_max_slno_str_less="";
		}
		
		$last_date=date('Y-m-d', strtotime($date1. ' - 1 days'));
		$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `date`='$last_date' "));	
		if($check_close_account_today)
		{
			$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));
			
			$con_max_slno_grtr=$check_close_account_today['con_slno'];
			$con_max_slno_str_grtr=" AND `slno`>$con_max_slno_grtr ";
			
			$inv_max_slno_grtr=$check_close_account_today['inv_slno'];
			$inv_max_slno_str_grtr=" AND `slno`>$inv_max_slno_grtr ";
			
			$ipd_max_slno_grtr=$check_close_account_today['ipd_slno'];
			$ipd_max_slno_str_grtr=" AND `slno`>$ipd_max_slno_grtr ";
		}
		else
		{
			$con_max_slno_str_grtr="";
			$inv_max_slno_str_grtr="";
			$ipd_max_slno_str_grtr="";
		}
	
	$encounter=$_GET['encounter'];
	$user_entry=$_GET['user_entry'];
	$pay_mode=$_GET['pay_mode'];
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	
	$all_pin=array();
	$card_pin="";
	$cheque_pin="";
	$i=1;
	if($encounter==0 || $encounter==1)
	{
		$con_pay_qry=mysqli_query($link, " SELECT distinct `opd_id` FROM `consult_payment_detail` WHERE `date` between '$date1' and '$date2' $con_max_slno_str_less $con_max_slno_str_grtr $user ORDER BY `slno`");
		while($con_pay=mysqli_fetch_array($con_pay_qry))
		{
			$all_pin[$i]=$con_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter==2 || $encounter==5)
	{
		$inv_pay_qry=mysqli_query($link, " SELECT distinct `opd_id` FROM `invest_payment_detail` WHERE `date` between '$date1' and '$date2' $inv_max_slno_str_less $inv_max_slno_str_grtr $user ORDER BY `slno`");
		while($inv_pay=mysqli_fetch_array($inv_pay_qry))
		{
			$all_pin[$i]=$inv_pay["opd_id"];
			$i++;
		}
	}
	if($encounter==0 || $encounter==3 || $encounter==4 || $encounter==8)
	{
		$ipd_casual_pay_qry=mysqli_query($link, " SELECT `ipd_id` FROM `ipd_advance_payment_details` WHERE `date` between '$date1' and '$date2' $ipd_max_slno_str_less $ipd_max_slno_str_grtr $user ORDER BY `slno`");
		while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
		{
			$all_pin[$i]=$ipd_casual_pay["ipd_id"];
			$i++;
		}
	}
	
	sort($all_pin);
?>
<html>
<head>
	<title>Detail Account Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<style>
		*{font-size:12px;}
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Detail Account Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-hover table-bordered">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>UHID</th>
				<th>PIN</th>
				<th>Patient Name</th>
				<th>Bill No</th>
				<th>Amount</th>
				<th>User</th>
				<th>Encounter</th>
			</tr>
		<?php
			$n=1;
			$zz=$yy=$ww=1;
			$tot_amt=$tot_amt_cash=$tot_amt_card="";
			$tot_amt_ipd="";
			$pin=0;
			foreach($all_pin as $all_pin)
			{
				if($all_pin)
				{
					$ipd_casual=0;
					$show_info=0;
					if($encounter==0)
					{
						$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pin' "));
					}else if($encounter>0)
					{
						$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pin' AND `type`='$encounter' "));
					}
					
					if($encounter==$all_pat["type"] && $encounter>0 || $encounter==0)
					{
						if($pay_mode=="Cash")
						{
							if($zz==1)
							{
								echo "<tr><th colspan='9'>Cash</th></tr>";
								$zz++;
							}
							if($pin==$all_pat['opd_id'])
							{
								$all_pat["type"]=2;
							}else
							{
								$pin=$all_pat['opd_id'];
							}
							$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
							
							if($all_pat["type"]==1)
							{
								$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Cash' AND `date` between '$date1' and '$date2' $user ";
								$pat_pay_detail_qry=mysqli_query($link, $con_pay_det);
							}
							if($all_pat["type"]==2 || $all_pat["type"]==5)
							{
								$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Cash' AND `date` between '$date1' and '$date2' $user ";
								$inv_pat_pay_detail_qry=mysqli_query($link, $inv_pay_det);
							}
							if($all_pat["type"]==3 || $all_pat["type"]==4 || $all_pat["type"]==8)
							{
								$show_info=0;
								$ipd_casual=1;
								$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='Cash' AND `date` between '$date1' and '$date2' $user ";
								$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
								
							}
							$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
							$Encounter=$pat_typ_text['p_type'];
								
							while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
							{
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
								$pay_date=$ipd_cas_pat_pay_detail["date"];
								$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
								$amount=$ipd_cas_pat_pay_detail["amount"];
								$tot_amt+=$amount;
								$tot_amt_ipd+=$amount;
							?>
								<tr>
									<td><?php echo $n; ?></td>
									<td><?php echo convert_date($pay_date); ?></td>
									<td><?php echo $pat_info["patient_id"]; ?></td>
									<td><?php echo $all_pat["opd_id"]; ?></td>
									<td><?php echo $pat_info["name"]; ?></td>
									<td><?php echo $bill_no; ?></td>
									<td><?php echo number_format($amount,2); ?></td>
									<td><?php echo $user_name["name"]; ?></td>
									<td><?php echo $Encounter; ?></td>
								</tr>
							<?php
								$n++;
								$zz++;
							}
							if($ipd_casual==0)
							{
								while($pat_pay_detail=mysqli_fetch_array($pat_pay_detail_qry))
								{
									$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
									$pay_date=$pat_pay_detail["date"];
									$bill_no=$pat_pay_detail["bill_no"];
									$amount=$pat_pay_detail["amount"];
									$tot_amt+=$amount;
									
									$all_pat_master=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `uhid_and_opdid` WHERE `opd_id`='$all_pat[opd_id]' "));
									$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat_master[type]' "));
									$Encounter=$pat_typ_text['p_type'];
									
									//$Encounter="OPD";
									if($pat_pay_detail["payment_mode"]=="Cash")
									{
										$show_info=1;
									}
									if($show_info==1)
									{
							?>
									<tr>
										<td><?php echo $n; ?></td>
										<td><?php echo convert_date($pay_date); ?></td>
										<td><?php echo $pat_info["patient_id"]; ?></td>
										<td><?php echo $all_pat["opd_id"]; ?></td>
										<td><?php echo $pat_info["name"]; ?></td>
										<td><?php echo $bill_no; ?></td>
										<td><?php echo number_format($amount,2); ?></td>
										<td><?php echo $user_name["name"]; ?></td>
										<td><?php echo $Encounter; ?></td>
									</tr>
									<?php
										$n++;
										$zz++;
									}
								}
								while($inv_pat_pay_detail=mysqli_fetch_array($inv_pat_pay_detail_qry))
								{
									$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
									$pay_date=$inv_pat_pay_detail["date"];
									$bill_no=$inv_pat_pay_detail["bill_no"];
									$amount=$inv_pat_pay_detail["amount"];
									$tot_amt+=$amount;
									
									$all_pat_master=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `uhid_and_opdid` WHERE `opd_id`='$all_pat[opd_id]' "));
									$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat_master[type]' "));
									$Encounter=$pat_typ_text['p_type'];
									
									//$Encounter="Lab";
									if($inv_pat_pay_detail["payment_mode"]=="Cash")
									{
										$show_info=1;
									}
									if($show_info==1)
									{
							?>
									<tr>
										<td><?php echo $n; ?></td>
										<td><?php echo convert_date($pay_date); ?></td>
										<td><?php echo $pat_info["patient_id"]; ?></td>
										<td><?php echo $all_pat["opd_id"]; ?></td>
										<td><?php echo $pat_info["name"]; ?></td>
										<td><?php echo $bill_no; ?></td>
										<td><?php echo number_format($amount,2); ?></td>
										<td><?php echo $user_name["name"]; ?></td>
										<td><?php echo $Encounter; ?></td>
									</tr>
									<?php
										$n++;
										$zz++;
									}
								}
							}
						}
						if($pay_mode=="Card")
						{
							if($yy==1)
							{
								echo "<tr><th colspan='9'>Card</th></tr>";
								$yy++;
							}
							if($pin==$all_pat['opd_id'])
							{
								$all_pat["type"]=2;
							}else
							{
								$pin=$all_pat['opd_id'];
							}
							$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
							
							if($all_pat["type"]==1)
							{
								$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Card' AND `date` between '$date1' and '$date2' $user ";
								$pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $con_pay_det));
								$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
								$pay_date=$pat_pay_detail["date"];
								$bill_no=$pat_pay_detail["bill_no"];
								$amount=$pat_pay_detail["amount"];
								$tot_amt+=$amount;
								//$Encounter="OPD";
								if($pat_pay_detail_num>0)
								{
									$show_info=1;
								}
							}
							if($all_pat["type"]==2 || $all_pat["type"]==5)
							{
								$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Card' AND `date` between '$date1' and '$date2' $user ";
								$inv_pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, $inv_pay_det));
								$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
								$pay_date=$inv_pat_pay_detail["date"];
								$bill_no=$inv_pat_pay_detail["bill_no"];
								$amount=$inv_pat_pay_detail["amount"];
								$tot_amt+=$amount;
								//$Encounter="Lab";
								if($inv_pat_pay_detail_num)
								{
									$show_info=1;
								}
							}
							if($all_pat["type"]==3 || $all_pat["type"]==4 || $all_pat["type"]==8)
							{
								$show_info=0;
								$ipd_casual=1;
								$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='Card' AND `date` between '$date1' and '$date2' $user ";
								$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
								
							}
							$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
							$Encounter=$pat_typ_text['p_type'];
							
							while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
							{
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
								$pay_date=$ipd_cas_pat_pay_detail["date"];
								$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
								$amount=$ipd_cas_pat_pay_detail["amount"];
								$tot_amt+=$amount;
								$tot_amt_ipd+=$amount;
							?>
								<tr>
									<td><?php echo $n; ?></td>
									<td><?php echo convert_date($pay_date); ?></td>
									<td><?php echo $pat_info["patient_id"]; ?></td>
									<td><?php echo $all_pat["opd_id"]; ?></td>
									<td><?php echo $pat_info["name"]; ?></td>
									<td><?php echo $bill_no; ?></td>
									<td><?php echo number_format($amount,2); ?></td>
									<td><?php echo $user_name["name"]; ?></td>
									<td><?php echo $Encounter; ?></td>
								</tr>
							<?php
								$n++;
								$yy++;
							}
							if($ipd_casual==0)
							{
								if($show_info==1)
								{
						?>
								<tr>
									<td><?php echo $n; ?></td>
									<td><?php echo convert_date($pay_date); ?></td>
									<td><?php echo $pat_info["patient_id"]; ?></td>
									<td><?php echo $all_pat["opd_id"]; ?></td>
									<td><?php echo $pat_info["name"]; ?></td>
									<td><?php echo $bill_no; ?></td>
									<td><?php echo number_format($amount,2); ?></td>
									<td><?php echo $user_name["name"]; ?></td>
									<td><?php echo $Encounter; ?></td>
								</tr>
								<?php
									$n++;
									$yy++;
								}
							}
						}
						if($pay_mode=="0") // All payment mode
						{
							if($ww==1)
							{
								echo "<tr><th colspan='9'>Cash</th></tr>";
								$ww++;
							}
							if($pin==$all_pat['opd_id'])
							{
								$all_pat["type"]=2;
							}else
							{
								$pin=$all_pat['opd_id'];
							}
							$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
							
							if($all_pat["type"]==1)
							{
								$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `date` between '$date1' and '$date2' $user ";
								$pat_pay_detail_qry=mysqli_query($link, $con_pay_det);
							}
							if($all_pat["type"]==2 || $all_pat["type"]==5)
							{
								$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `date` between '$date1' and '$date2' $user ";
								$inv_pat_pay_detail_qry=mysqli_query($link, $inv_pay_det);
							}
							if($all_pat["type"]==3 || $all_pat["type"]==4 || $all_pat["type"]==8)
							{
								$show_info=0;
								$ipd_casual=1;
								$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `date` between '$date1' and '$date2' $user ";
								$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
								
							}
							$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
							$Encounter=$pat_typ_text['p_type'];
							
							$show_info_ipd=0;
							while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
							{
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
								$pay_date=$ipd_cas_pat_pay_detail["date"];
								$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
								$amount=$ipd_cas_pat_pay_detail["amount"];
								
								if($ipd_cas_pat_pay_detail["pay_mode"]=="Cash")
								{
									$show_info_ipd=1;
									$tot_amt+=$amount;
									$tot_amt_cash+=$amount;
									$tot_amt_ipd+=$amount;
								}else if($ipd_cas_pat_pay_detail["pay_mode"]=="Card")
								{
									$card_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##34@@";
									$show_info_ipd=0;
								}else if($ipd_cas_pat_pay_detail["pay_mode"]=="Cheque")
								{
									$cheque_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##34@@";
									$show_info_ipd=0;
								}
								if($show_info_ipd==1)
								{
									$all_test="";
									if($all_pat["type"]==4 || $all_pat["type"]==8)
									{
										$test_qry=mysqli_query($link, " SELECT `service_text` FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_info[patient_id]' AND `ipd_id`='$all_pat[opd_id]' ");
										while($test=mysqli_fetch_array($test_qry))
										{
											$all_test.=$test["service_text"].",<br>";
										}
										$all_test="<br>(".$all_test."";
									}
							?>
								<tr>
									<td><?php echo $n; ?></td>
									<td><?php echo convert_date($pay_date); ?></td>
									<td><?php echo $pat_info["patient_id"]; ?></td>
									<td><?php echo $all_pat["opd_id"]; ?></td>
									<td><?php echo $pat_info["name"]; ?></td>
									<td><?php echo $bill_no; ?></td>
									<td><?php echo number_format($amount,2); ?></td>
									<td><?php echo $user_name["name"]; ?></td>
									<td><?php echo $Encounter.$all_test; ?></td>
								</tr>
							<?php
								$n++;
								$ww++;
								}
							}
							if($ipd_casual==0)
							{
								while($inv_pat_pay_detail=mysqli_fetch_array($inv_pat_pay_detail_qry))
								{
									$show_info=0;
									$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
									$pay_date=$inv_pat_pay_detail["date"];
									$bill_no=$inv_pat_pay_detail["bill_no"];
									$amount=$inv_pat_pay_detail["amount"];
									//$tot_amt+=$amount;
									//$Encounter="Lab";
									
									$all_pat_master=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `uhid_and_opdid` WHERE `opd_id`='$all_pat[opd_id]' "));
									$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat_master[type]' "));
									$Encounter=$pat_typ_text['p_type'];
									
									if($inv_pat_pay_detail["payment_mode"]=="Cash")
									{
										$show_info=1;
										$tot_amt+=$amount;
										$tot_amt_cash+=$amount;
									}else if($inv_pat_pay_detail["payment_mode"]=="Card")
									{
										$card_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
									}else if($inv_pat_pay_detail["payment_mode"]=="Cheque")
									{
										$cheque_pin.=$all_pat['opd_id']."##".$inv_pat_pay_detail["bill_no"]."##2@@";
									}
									if($show_info==1)
									{
							?>
									<tr>
										<td><?php echo $n; ?></td>
										<td><?php echo convert_date($pay_date); ?></td>
										<td><?php echo $pat_info["patient_id"]; ?></td>
										<td><?php echo $all_pat["opd_id"]; ?></td>
										<td><?php echo $pat_info["name"]; ?></td>
										<td><?php echo $bill_no; ?></td>
										<td><?php echo number_format($amount,2); ?></td>
										<td><?php echo $user_name["name"]; ?></td>
										<td><?php echo $Encounter; ?></td>
									</tr>
									<?php
										$n++;
										$ww++;
									}
								}
								while($pat_pay_detail=mysqli_fetch_array($pat_pay_detail_qry))
								{
									$show_info=0;
									$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
									$pay_date=$pat_pay_detail["date"];
									$bill_no=$pat_pay_detail["bill_no"];
									$amount=$pat_pay_detail["amount"];
									//$tot_amt+=$amount;
									//$Encounter="OPD";
									
									$all_pat_master=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `uhid_and_opdid` WHERE `opd_id`='$all_pat[opd_id]' "));
									$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat_master[type]' "));
									$Encounter=$pat_typ_text['p_type'];
									
									if($pat_pay_detail["payment_mode"]=="Cash")
									{
										$show_info=1;
										$tot_amt+=$amount;
										$tot_amt_cash+=$amount;
									}else if($pat_pay_detail["payment_mode"]=="Card")
									{
										$card_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
									}else if($pat_pay_detail["payment_mode"]=="Cheque")
									{
										$cheque_pin.=$all_pat['opd_id']."##".$pat_pay_detail["bill_no"]."##1@@";
									}
									if($show_info==1)
									{
							?>
									<tr>
										<td><?php echo $n; ?></td>
										<td><?php echo convert_date($pay_date); ?></td>
										<td><?php echo $pat_info["patient_id"]; ?></td>
										<td><?php echo $all_pat["opd_id"]; ?></td>
										<td><?php echo $pat_info["name"]; ?></td>
										<td><?php echo $bill_no; ?></td>
										<td><?php echo number_format($amount,2); ?></td>
										<td><?php echo $user_name["name"]; ?></td>
										<td><?php echo $Encounter; ?></td>
									</tr>
									<?php
										$n++;
										$ww++;
									}
								}
							}
						}
					}
				}
			}
			if($pay_mode=="0")
			{
			?>
				<tr>
					<th colspan="6"><span class="text-right">Total Cash Amount</span></th>
					<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_cash,2); ?></td>
				</tr>
			<?php
			}
			if($card_pin)
			{
				//echo $card_pin."<br>";
				echo "<tr><th colspan='9'>Card</th></tr>";
				$same_card_pin="";
				$card_pins=explode("@@",$card_pin);
				foreach($card_pins as $card_pinn)
				{
					if($card_pinn)
					{
						$ipd_casual_card==0;
						
						$card_pinn=explode("##",$card_pinn);
						$card_pin=$card_pinn[0];
						$card_bill=$card_pinn[1];
						$card_type=$card_pinn[2];
						//echo $card_pin."<br>";
						$all_pat=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$card_pin' "));
						$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
						
						if($card_type==1)
						{
							$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$card_bill' ";
							$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $con_pay_det));
							$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
							$pay_date=$pat_pay_detail["date"];
							$bill_no=$pat_pay_detail["bill_no"];
							$amount=$pat_pay_detail["amount"];
							$tot_amt+=$amount;
							$tot_amt_card+=$amount;
							//$Encounter="OPD";
						}
						if($all_pat["type"]==2 || $all_pat["type"]==5)
						{
							$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' AND `opd_id`='$all_pat[opd_id]' AND `bill_no`='$card_bill' ";
							$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, $inv_pay_det));
							$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
							$pay_date=$inv_pat_pay_detail["date"];
							$bill_no=$inv_pat_pay_detail["bill_no"];
							$amount=$inv_pat_pay_detail["amount"];
							$tot_amt+=$amount;
							$tot_amt_card+=$amount;
							//$Encounter="Lab";
						}
						if($card_type==34)
						{
							$ipd_casual_card=1;
							$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$card_pin' AND `bill_no`='$card_bill' ";
							$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
							
						}
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
						$Encounter=$pat_typ_text['p_type'];
						
						while($ipd_cas_pat_pay_detail=mysqli_fetch_array($ipd_cas_pat_pay_detail_qry))
						{
							$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_cas_pat_pay_detail[user]' "));
							$pay_date=$ipd_cas_pat_pay_detail["date"];
							$bill_no=$ipd_cas_pat_pay_detail["bill_no"];
							$amount=$ipd_cas_pat_pay_detail["amount"];
							$tot_amt+=$amount;
							$tot_amt_card+=$amount;
							$tot_amt_ipd+=$amount;
							
						?>
							<tr>
								<td><?php echo $n; ?></td>
								<td><?php echo convert_date($pay_date); ?></td>
								<td><?php echo $pat_info["patient_id"]; ?></td>
								<td><?php echo $all_pat["opd_id"]; ?></td>
								<td><?php echo $pat_info["name"]; ?></td>
								<td><?php echo $bill_no; ?></td>
								<td><?php echo number_format($amount,2); ?></td>
								<td><?php echo $user_name["name"]; ?></td>
								<td><?php echo $Encounter; ?></td>
							</tr>
						<?php
							$n++;
						}
						if($ipd_casual_card==0)
						{
					?>
						<tr>
							<td><?php echo $n; ?></td>
							<td><?php echo convert_date($pay_date); ?></td>
							<td><?php echo $pat_info["patient_id"]; ?></td>
							<td><?php echo $all_pat["opd_id"]; ?></td>
							<td><?php echo $pat_info["name"]; ?></td>
							<td><?php echo $bill_no; ?></td>
							<td><?php echo number_format($amount,2); ?></td>
							<td><?php echo $user_name["name"]; ?></td>
							<td><?php echo $Encounter; ?></td>
						</tr>
						<?php
							$n++;
						}
					}
				}
			}
			/*if($u_level=='1')
			{
				$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date1' and '$date2'"));	
			}else
			{
				$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date1' and '$date2' and `user`='$c_user' "));	
			}*/
			
			// Discount
		$opd_discount_amt=0;
		if($encounter==0 || $encounter==1)
		{
			$opd_discount=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(dis_amt),0) as opd_dis from consult_patient_payment_details where `date` between '$date11' and '$date22' "));
			$opd_discount_amt=$opd_discount["opd_dis"];
		}
		
		$lab_discount_amt=0;
		if($encounter==0 || $encounter==2 || $encounter==5)
		{
			$lab_discount=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(dis_amt),0) as lab_dis from invest_patient_payment_details where `date` between '$date11' and '$date22' "));
			$lab_discount_amt=$lab_discount["lab_dis"];
		}
		
		$other_discount_amt=0;
		if($encounter==0)
		{
			$other_discount=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(discount),0) as other_dis from ipd_advance_payment_details where `date` between '$date11' and '$date22' "));
			$other_discount_amt=$other_discount["other_dis"];
		}
		
		$ipd_discount_amt=0;
		if($encounter==0 || $encounter==3)
		{
			$ipd_discount=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(discount),0) as ipd_dis from ipd_advance_payment_details where `date` between '$date11' and '$date22' and `ipd_id` in ( SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='3' ) "));
			$ipd_discount_amt=$ipd_discount["ipd_dis"];
		}
		
		$casu_discount_amt=0;
		if($encounter==0 || $encounter==4)
		{
			$casu_discount=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(discount),0) as casu_dis from ipd_advance_payment_details where `date` between '$date11' and '$date22' and `ipd_id` in ( SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='4' ) "));
			$casu_discount_amt=$casu_discount["casu_dis"];
		}
		
		$rad_discount_amt=0;
		if($encounter==0 || $encounter==8)
		{
			$rad_discount=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(discount),0) as rad_dis from ipd_advance_payment_details where `date` between '$date11' and '$date22' and `ipd_id` in ( SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='8' ) "));
			$rad_discount_amt=$rad_discount["rad_dis"];
		}
		
		$tot_discount=$opd_discount_amt+$lab_discount_amt+$other_discount_amt+$ipd_discount_amt+$casu_discount_amt+$rad_discount_amt;
			
			if($user_entry==0)
			{
				if($encounter==0 || $encounter==3)
				{
					$qrefund=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund),0) as maxref from ipd_advance_payment_details where `date` between '$date11' and '$date22' "));
				}
				$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date11' and '$date22'"));	
			}
			else
			{
				if($encounter==0 || $encounter==3)
				{
					$qrefund=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund),0) as maxref from ipd_advance_payment_details where `date` between '$date11' and '$date22' and user='$user_entry' "));
				}
				$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date11' and '$date22' and `user`='$user_entry' "));	
			}
			$tot_expense=$tot_expense_qry["tot_exp"];
			if($pay_mode=="0")
			{
				if($card_pin)
				{
			?>
			<tr>
				<th colspan="6"><span class="text-right">Total Card Amount</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_card,2); ?></td>
			</tr>
			<?php } } ?>
			<tr>
				<th colspan="6"><span class="text-right">Total <?php if($pay_mode!='0'){ echo $pay_mode; } ?> Amount</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt,2); ?></td>
			</tr>
			<tr>
				<th colspan="6"><span class="text-right">Total Discount</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($tot_discount,2); ?></td>
			</tr>
			<?php //if($encounter==0 && $pay_mode=="0"){ ?>
			<?php if($pay_mode=="0"){ ?>
			<tr>
				<th colspan="6"><span class="text-right">Total Expense</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($tot_expense,2); ?></td>
			</tr>
			<tr>
			   <th colspan="6"><span class="text-right">Total Refund</span></th>
			  <td colspan="3"><?php echo $rupees_symbol.number_format($qrefund['maxref'],2); ?></td>
			</tr>
			<tr>
				<th colspan="6"><span class="text-right">Net Amount</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format(($tot_amt-$tot_expense-$qrefund['maxref']),2); ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
</body>
</html>
<script>//window.print();</script>
