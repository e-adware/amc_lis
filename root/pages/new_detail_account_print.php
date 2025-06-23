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
	
	$encounter=$_GET['encounter'];
	$user_entry=$_GET['user_entry'];
	$pay_mode=$_GET['pay_mode'];
	$user_val=$_GET['EpMl'];
	$account_break=$_GET['account_break'];
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	
	// Close account
	$check_close_account_today="";
		if($account_break>0)
		{
			$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$date2' AND `slno`='$account_break' $user "));
		}
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
		//$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `user`='$user_val' AND `date`='$last_date' "));
		if($account_break>0)
		{
			$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `slno` IN(SELECT MAX(`slno`) FROM `daily_account_close` WHERE `slno`<$account_break $user ) "));
		}else
		{
			//$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `slno` IN(SELECT MAX(`slno`) FROM `daily_account_close`) $user "));
			$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `slno` IN(SELECT MAX(`slno`) FROM `daily_account_close` WHERE `user`='$user_entry') "));
		}
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
	if($encounter==0 || $encounter==2)
	{
		//echo " SELECT distinct `opd_id` FROM `invest_payment_detail` WHERE `date` between '$date1' and '$date2' $inv_max_slno_str_less $inv_max_slno_str_grtr $user ORDER BY `slno`";
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
	<script src="../../js/jquery.min.js"></script>
	<style>
		*{font-size:11px;}
	</style>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Detail Account Report</h4>
			<b>From <?php echo convert_date($date11); ?> to <?php echo convert_date($date22); ?></b>
			
			<div class="account_close_div">
				<?php
					$date=date("Y-m-d");
					$check_close_account=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `user`='$user_val' AND `close_date`='$date22' "));
					if($check_close_account)
					{
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$check_close_account[user]' "));
						
					?>
						<div class="noprint "><input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="close_window_child()"></div>
					<?php
					}else
					{
						if($date==$date22)
						{
							$close_btn="Close Today's Account";
						}else
						{
							$close_btn="Close Account of ".convert_date($date22);
						}
				?>
						<input type="button" id="btn_close" value="<?php echo $close_btn; ?>" class="btn btn-danger" onClick="close_account('<?php echo $user_val; ?>','<?php echo $date22; ?>')" >
						<div class="noprint "><input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()"></div>
				<?php
					}
				?>
			</div>
			
		</center>
		<br>
		<table class="table table-hover table-condensed">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th class="ipd_serial">Sl No.</th>
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
							$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
							
							$uhid_id=$pat_info["patient_id"];
							
							$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
							$Encounter=$pat_typ_text['p_type'];
							
							if($pat_typ_text["type"]==1)
							{
								$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Cash' AND `date` between '$date1' and '$date2' $user ";
								$pat_pay_detail_qry=mysqli_query($link, $con_pay_det);
							}
							if($pat_typ_text["type"]==2)
							{
								$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `payment_mode`='Cash' AND `date` between '$date1' and '$date2' $user ";
								$inv_pat_pay_detail_qry=mysqli_query($link, $inv_pay_det);
							}
							if($pat_typ_text["type"]==3)
							{
								$show_info=0;
								$ipd_casual=1;
								$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='Cash' AND `date` between '$date1' and '$date2' $user ";
								$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
								
							}
								
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
									<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
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
										<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
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
										<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
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
							$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
							$uhid_id=$pat_info["patient_id"];
							
							$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
							$Encounter=$pat_typ_text['p_type'];
							
							if($pat_typ_text["type"]==1)
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
							if($pat_typ_text["type"]==2)
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
							if($pat_typ_text["type"]==3)
							{
								$show_info=0;
								$ipd_casual=1;
								$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `pay_mode`='Card' AND `date` between '$date1' and '$date2' $user ";
								$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
								
							}
							
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
									<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
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
									<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
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
							$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
							$uhid_id=$pat_info["patient_id"];
							
							$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$all_pat[type]' "));
							$Encounter=$pat_typ_text['p_type'];
							
							if($pat_typ_text["type"]==1)
							{
								$con_pay_det=" SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `date` between '$date1' and '$date2' $user ";
								$pat_pay_detail_qry=mysqli_query($link, $con_pay_det);
							}
							if($pat_typ_text["type"]==2)
							{
								$inv_pay_det=" SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$all_pat[patient_id]' and `opd_id`='$all_pat[opd_id]' AND `date` between '$date1' and '$date2' $user ";
								$inv_pat_pay_detail_qry=mysqli_query($link, $inv_pay_det);
							}
							if($pat_typ_text["type"]==3)
							{
								$show_info=0;
								$ipd_casual=1;
								$ipd_adv_pay_det=" SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$all_pat[patient_id]' and `ipd_id`='$all_pat[opd_id]' AND `date` between '$date1' and '$date2' $user ";
								$ipd_cas_pat_pay_detail_qry=mysqli_query($link, $ipd_adv_pay_det);
								
							}
							
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
									$card_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
									$show_info_ipd=0;
								}else if($ipd_cas_pat_pay_detail["pay_mode"]=="Cheque")
								{
									$cheque_pin.=$all_pat['opd_id']."##".$ipd_cas_pat_pay_detail["bill_no"]."##3@@";
									$show_info_ipd=0;
								}
								if($show_info_ipd==1)
								{
							?>
								<tr>
									<td><?php echo $n; ?></td>
									<td><?php echo convert_date($pay_date); ?></td>
									<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
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
							if($ipd_casual==0)
							{
								while($inv_pat_pay_detail=mysqli_fetch_array($inv_pat_pay_detail_qry))
								{
									$show_info=0;
									
									// Refund Amount
									$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date1' and '$date2' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='2' "));
									
									$lab_refund_amount=$lab_refund_val["maxref"];
									
									// Free Amount
									$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_lab from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date1' and '$date2' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='2' "));
									
									$lab_free_amount=$lab_free_val["free_lab"];
									
									$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_pat_pay_detail[user]' "));
									
									$pay_date=$inv_pat_pay_detail["date"];
									$bill_no=$inv_pat_pay_detail["bill_no"];
									
									$amount=$inv_pat_pay_detail["amount"]+$lab_refund_amount+$lab_free_amount;
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
										<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
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
									
									// Refund Amount
									$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date1' and '$date2' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='1' "));
									
									$opd_refund_amount=$opd_refund_val["maxref_opd"];
									
									// Free Amount
									$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as free_opd from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date1' and '$date2' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`patient_id`='$all_pat[patient_id]' and a.`opd_id`='$all_pat[opd_id]' and b.type='1' "));
									
									$opd_free_amount=$opd_free_val["free_opd"];
									
									$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
									
									$pay_date=$pat_pay_detail["date"];
									$bill_no=$pat_pay_detail["bill_no"];
									
									$amount=$pat_pay_detail["amount"]+$opd_refund_amount+$opd_free_amount;
									//$tot_amt+=$amount;
									//$Encounter="Lab";
									
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
										<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
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
						$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$all_pat[patient_id]' "));
						$uhid_id=$pat_info["patient_id"];
						
						if($card_type==1)
						{
							$ipd_casual_card=0;
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
						if($card_type==2)
						{
							$ipd_casual_card=0;
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
						if($card_type==3)
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
								<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
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
							<td class="ipd_serial"><?php echo $all_pat["ipd_serial"]; ?></td>
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
			if($user_entry==0)
			{
				if($encounter==0 || $encounter==3)
				{
					$ipd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund),0) as maxref from ipd_advance_payment_details where `date` between '$date11' and '$date22' "));
				}
				$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date11' and '$date22'"));
				// Lab Refund
				$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='2' "));
				// Lab Free
				$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as lab_free from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='2' "));
				// OPD Refund
				$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='1' "));
				// OPD Free
				$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as opd_free from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='1' "));
				
			}
			else
			{
				if($encounter==0 || $encounter==3)
				{
					$ipd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund),0) as maxref from ipd_advance_payment_details where `date` between '$date11' and '$date22' and user='$user_entry' "));
				}
				$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date11' and '$date22' and `user`='$user_entry' "));
				
				// Lab Refund
				$lab_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='2' and a.`user`='$user_entry' "));
				// Lab Free
				$lab_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as lab_free from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='2' and a.`user`='$user_entry' "));
				// OPD Refund
				$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='1' and a.`user`='$user_entry' "));
				// OPD Free
				$opd_free_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.free_amount),0) as opd_free from invest_payment_free a , uhid_and_opdid b where a.`date` between '$date11' and '$date22' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='1' and a.`user`='$user_entry' "));
				
			}
			$tot_expense=$tot_expense_qry["tot_exp"];
			
			$opd_refund=$opd_refund_val["maxref_opd"];
			$lab_refund=$lab_refund_val["maxref"];
			$total_refund_amount=$opd_refund+$lab_refund;
			
			// Free
			$opd_free_amount=$opd_free_val["opd_free"];
			$lab_free_amount=$lab_free_val["lab_free"];
			$total_free_amount=$opd_free_amount+$lab_free_amount;
			
			$ipd_refund=$ipd_refund_val["maxref"];
			
			if($pay_mode=="0")
			{
				if($card_pin)
				{
			?>
			<tr>
				<th colspan="5"><span class="text-right">Total Card Amount</span></th>
				<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_card,2); ?></td>
			</tr>
			<?php } } ?>
			<?php
			if($pay_mode=="0")
			{
			?>
				<tr>
					<th colspan="5"><span class="text-right">Total Cash Amount</span></th>
					<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt_cash,2); ?></td>
				</tr>
			<?php
			}
			?>
				<tr>
					<th colspan="5"><span class="text-right">Total <?php if($pay_mode!='0'){ echo $pay_mode; } ?> Amount</span></th>
					<td colspan="3"><?php echo $rupees_symbol.number_format($tot_amt,2); ?></td>
				</tr>
			<?php //if($encounter==0 && $pay_mode=="0"){ ?>
			<?php if($pay_mode=="0"){ ?>
				<tr>
					<th colspan="5"><span class="text-right">Total Expense</span></th>
					<td colspan="3"><?php echo $rupees_symbol.number_format($tot_expense,2); ?></td>
				</tr>
				<tr>
					<th colspan="5"><span class="text-right">Total Refund</span></th>
					<td colspan="3"><?php echo $rupees_symbol.number_format($ipd_refund+$total_refund_amount,2); ?></td>
				</tr>
				<tr>
					<th colspan="5"><span class="text-right">Total Free</span></th>
					<td colspan="3"><?php echo $rupees_symbol.number_format($total_free_amount,2); ?></td>
				</tr>
				<tr>
					<th colspan="5"><span class="text-right">Net Amount</span></th>
					
					<td colspan="3"><?php echo $rupees_symbol.number_format(($tot_amt-$tot_expense-$ipd_refund-$total_refund_amount-$total_free_amount),2); ?></td>
				</tr>
	<?php 	
			}
	?>
		</table>
	</div>
	<span id="user" style="display:none;"><?php echo $user_val; ?></span>
</body>
</html>
<script>
	$(document).keydown(function (event) {
		if (event.keyCode == 123 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent F12
			return false;
		} else if (event.ctrlKey && event.shiftKey && event.keyCode == 73 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent Ctrl+Shift+I        
			return false;
		}
	});
	$(document).on("contextmenu",function(e){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			e.preventDefault();
		}
	});
	$(document).ready(function(){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			//window.print();
		}
		$(".noprint").hide();
	});
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
	function close_account(user,c_date)
	{
		//alert(user+' '+cdate);
		if(confirm("Are you sure want to close account ?"))
		{
			$.post("../pages/close_account_data.php",
			{
				type:"close_account_single",
				c_date:c_date,
				user:user,
			},
			function(data,status)
			{
				alert(data+"'s account is closed");
				//window.location.reload(true);
				$(".noprint").show();
				$("#btn_close").hide();
			})
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
<style>
.ipd_serial
{
	display:none;
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
</style>
