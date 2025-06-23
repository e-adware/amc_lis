<?php
include("../../includes/connection.php");

$date1=$_POST['date1'];
$date2=$_POST['date2'];

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d M Y', $timestamp);
	return $new_date;
}

if($_POST["type"]=="summary_reports")
{
	$regd=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`regd_fee`) as regd_tot FROM `consult_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' "));
	
	//$free_regd=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(a.`regd_fee`) AS `free_regd_tot` FROM `consult_patient_payment_details` a, `invest_payment_free` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` BETWEEN '$date1' AND '$date2' "));
	
	$free_regd=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`regd_fee`) AS `free_regd_tot` FROM `consult_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `opd_id` IN(SELECT `opd_id` FROM `invest_payment_free`)"));
	
	$ongc_regd=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`regd_fee`) AS `ongc_regd_tot` FROM `consult_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `center_no`='C102')"));
	$ongc_regd_amount=$ongc_regd["ongc_regd_tot"];
	
	$regd_total=$regd["regd_tot"]-$free_regd["free_regd_tot"]-$ongc_regd_amount;
	
	$consult=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`visit_fee`) as consult_tot FROM `consult_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' "));
	
	//$free_consult=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(a.`visit_fee`) AS `free_consult_tot` FROM `consult_patient_payment_details` a, `invest_payment_free` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` BETWEEN '$date1' AND '$date2' "));
	
	$free_consult=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`visit_fee`) AS `free_consult_tot` FROM `consult_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `opd_id` IN(SELECT `opd_id` FROM `invest_payment_free`)"));
	
	$ongc_consult=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`visit_fee`) AS `ongc_consult_tot` FROM `consult_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `center_no`='C102')"));
	$ongc_consult_amount=$ongc_consult["ongc_consult_tot"];
	
	
	$consult_total=$consult["consult_tot"]-$free_consult["free_consult_tot"]-$ongc_consult_amount;
	
?>
	<p style="margin-top: 2%;" id="print_div"><b>Summary Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		<button type="button" class="btn btn-info text-right " onclick="print_page('summary_reports','<?php echo $date1;?>','<?php echo $date2;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-condensed">
		<tr>
			<th colspan="2">OPD</th>
		</tr>
		<tr>
			<td rowspan="2" style="width: 30%;">Registration</td>
			<td>Rs. <?php echo number_format($regd_total,2); ?> (GENERAL)</td>
		</tr>
		<tr>
			<td>Rs. <?php echo number_format($ongc_regd_amount,2); ?> (ONGC)</td>
		</tr>
	<?php
		$tot_serv_amt_casualty_doctor=0;
		$tot_serv_amt_casualty_hospital=0;
		$distnct_service_qry=mysqli_query($link, " SELECT DISTINCT(a.`service_id`) FROM `ipd_pat_service_details` a, `uhid_and_opdid` b, `ipd_advance_payment_details` c WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=4 AND a.`patient_id`=c.`patient_id` AND a.`ipd_id`=c.`ipd_id` AND c.`date` BETWEEN '$date1' AND '$date2' ");
		
		$distnct_service_num=mysqli_num_rows($distnct_service_qry);
		if($distnct_service_num>0)
		{
			echo "<tr><th colspan='2'>CASUALTY / DAYCARE</th></tr>";
		}
		
		while($distnct_service=mysqli_fetch_array($distnct_service_qry))
		{
			$service_amount=0;
			
			$service_name=mysqli_fetch_array(mysqli_query($link, " SELECT `service_text` FROM `ipd_pat_service_details` WHERE `service_id`='$distnct_service[service_id]' "));
			
			$doc_service=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `doctor_service_done` WHERE `service_id`='$distnct_service[service_id]' "));
			if($doc_service)
			{
				$service_amount_val=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(a.`amount`) as service_tot FROM `ipd_pat_service_details` a, `uhid_and_opdid` b, `ipd_advance_payment_details` c, `doctor_service_done` d WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=4 AND a.`patient_id`=c.`patient_id` AND a.`ipd_id`=c.`ipd_id` AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`service_id`='$distnct_service[service_id]' AND a.`patient_id`=d.`patient_id` AND a.`ipd_id`=d.`ipd_id` AND a.`service_id`=d.`service_id` AND d.`consultantdoctorid`!='72' "));
				$tot_serv_amt_casualty_doctor+=$service_amount=$service_amount_val["service_tot"];
				
				$service_amount_val=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(a.`amount`) as service_tot FROM `ipd_pat_service_details` a, `uhid_and_opdid` b, `ipd_advance_payment_details` c, `doctor_service_done` d WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=4 AND a.`patient_id`=c.`patient_id` AND a.`ipd_id`=c.`ipd_id` AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`service_id`='$distnct_service[service_id]' AND a.`patient_id`=d.`patient_id` AND a.`ipd_id`=d.`ipd_id` AND a.`service_id`=d.`service_id` AND d.`consultantdoctorid`='72' "));
				$service_amount_hospital+=$service_amount=$service_amount_val["service_tot"];
			}else
			{
				$service_amount_val=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(a.`amount`) as service_tot FROM `ipd_pat_service_details` a, `uhid_and_opdid` b, `ipd_advance_payment_details` c WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=4 AND a.`patient_id`=c.`patient_id` AND a.`ipd_id`=c.`ipd_id` AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`service_id`='$distnct_service[service_id]' "));
				$tot_serv_amt_casualty_hospital+=$service_amount=$service_amount_val["service_tot"];
			}
		?>
		<tr>
			<td style=""><?php echo $service_name["service_text"]; ?></td>
			<td>Rs. <?php echo number_format($service_amount,2); ?></td>
		</tr>
<?php
			$tot_serv_amt_casualty+=$service_amount;
		}

		$final_pat_qry=mysqli_query($link, " SELECT a.* FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=3 AND a.`date` BETWEEN '$date1' AND '$date2' group by a.`patient_id`,a.`ipd_id` ");
		
		$final_pat_num=mysqli_num_rows($final_pat_qry);
		if($final_pat_num>0)
		{
			echo "<tr><th colspan='2'>IPD</th></tr>";
		}
		
		while($final_pat=mysqli_fetch_array($final_pat_qry))
		{
			$service_name=mysqli_fetch_array(mysqli_query($link, " SELECT a.`charge_name` FROM `charge_master` a, `bed_master` b WHERE a.`charge_id`=b.`charge_id` AND b.`bed_id` IN(SELECT `bed_id` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$final_pat[patient_id]' AND `ipd_id`='$final_pat[ipd_id]' AND `alloc_type`=0 ORDER BY `slno` DESC) "));
			
			$service_amount="0";
			$service_amount_val=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) as service_tot FROM `ipd_advance_payment_details` WHERE `patient_id`='$final_pat[patient_id]' AND `ipd_id`='$final_pat[ipd_id]' AND `date` BETWEEN '$date1' AND '$date2' "));
			$service_amount=$service_amount_val["service_tot"];
			
		?>
		<tr>
			<td style=""><?php echo $service_name["charge_name"]; ?></td>
			<td>Rs. <?php echo number_format($service_amount,2); ?></td>
		</tr>
<?php
			$tot_serv_amt_ipd+=$service_amount;
		}
		
		// Expense
		$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select ifnull(sum(amount),0) as tot_exp from expensedetail where entry_date between '$date1' and '$date2'"));
		$tot_expense=$tot_expense_qry["tot_exp"];
		
		// Discount
		$tot_dis_opd=mysqli_fetch_array(mysqli_query($link, "select ifnull(sum(dis_amt),0) as tot_dis from consult_patient_payment_details where `date` between '$date1' and '$date2' AND `opd_id` NOT IN(SELECT `opd_id` FROM `invest_payment_free`)"));
		
		$tot_dis_opd_amount=$tot_dis_opd["tot_dis"];
		
		$tot_dis_ipd_casualty=mysqli_fetch_array(mysqli_query($link, "select ifnull(sum(discount),0) as tot_ipd from ipd_advance_payment_details where `date` between '$date1' and '$date2'"));
		$tot_dis_ipd_casualty_amount=$tot_dis_ipd_casualty["tot_ipd"];
		
		$total_discount=$tot_dis_opd_amount+$tot_dis_ipd_casualty_amount;
?>
		<tr>
			<th>CASUALTY / DAYCARE TOTAL</th>
			<th>Rs. <?php echo number_format($tot_serv_amt_casualty,2); ?></th>
		</tr>
		<tr>
			<th>TOTAL</th>
			<th>Rs. <?php echo number_format($tot_serv_amt_casualty+$tot_serv_amt_ipd+$regd_total,2); ?></th>
		</tr>
		<tr>
			<td colspan="2"> &nbsp; </td>
		</tr>
		<!--<tr>
			<th>Hospital Copy</th>
			<td>Rs. <?php echo number_format($tot_serv_amt_casualty+$tot_serv_amt_ipd+$regd_total,2); ?></td>
		</tr>-->
		<tr>
			<th rowspan="2">Doctors Pay</th>
			<td>Rs. <?php echo number_format($consult_total+$tot_serv_amt_casualty_doctor,2); ?> (GENERAL)</td>
		</tr>
		<tr>
			<td>Rs. <?php echo number_format($ongc_consult_amount,2); ?> (ONGC)</td>
		</tr>
		<tr>
			<th>Total Amount</th>
			<td>Rs. <?php echo number_format($tot_serv_amt_casualty+$tot_serv_amt_ipd+$regd_total+$consult_total,2); ?></td>
		</tr>
		<tr>
			<th>Discount</th>
			<td>Rs. <?php echo number_format($total_discount,2); ?></td>
		</tr>
		<tr>
			<th>Expense</th>
			<td>Rs. <?php echo number_format($tot_expense,2); ?></td>
		</tr>
		<tr>
			<th>Total ONGC</th>
			<td>Rs. <?php echo number_format($ongc_consult_amount+$ongc_regd_amount,2); ?></td>
		</tr>
		<tr>
			<th>Net Amount</th>
			<th>Rs. <?php echo number_format($tot_serv_amt_casualty+$tot_serv_amt_ipd+$regd_total+$consult_total-$tot_expense-$total_discount,2); ?></th>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="doctor_wise_summary")
{
?>
	<p style="margin-top: 2%;" id="print_div"><b>Doctor Wise Summary Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		<button type="button" class="btn btn-info text-right " onclick="print_page('doctor_wise_summary','<?php echo $date1;?>','<?php echo $date2;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
<?php
	$all_doc=array();
	$i=1;
	$opd_qry=mysqli_query($link, " SELECT distinct `consultantdoctorid` FROM `appointment_book` WHERE `date` between '$date1' and '$date2' ");
	while($opd_doc=mysqli_fetch_array($opd_qry))
	{
		$all_doc[$i]=$opd_doc["consultantdoctorid"];
		$i++;
	}
	
	$doc_qry=mysqli_query($link, " SELECT distinct `consultantdoctorid` FROM `doctor_service_done` WHERE `date` BETWEEN '$date1' AND '$date2' ");
	while($doc_val=mysqli_fetch_array($doc_qry))
	{
		$all_doc[$i]=$doc_val["consultantdoctorid"];
		$i++;
	}
	$all_doc=array_unique($all_doc);
	//$all_doc=array_values($all_doc);
	//print_r($all_doc);
?>
	<table class="table table-hover table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>Doctor Name</th>
				<th>Consult Fee</th>
				<th>Regd Fee</th>
<?php
			$service_qry=mysqli_query($link, " SELECT distinct `service_id` FROM `doctor_service_done` WHERE `date` BETWEEN '$date1' AND '$date2' ");
			while($service=mysqli_fetch_array($service_qry))
			{
				$charge_info=mysqli_fetch_array(mysqli_query($link, "SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$service[service_id]'"));
				
				echo "<th>$charge_info[charge_name]</th>";
			}
?>
			</tr>
		</thead>
<?php
	
	$tot_visit_fee=$tot_regd_fee=0;
	$each_serv_tot=array();
	foreach($all_doc as $consultantdoctorid)
	{
		if($consultantdoctorid)
		{
			$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$consultantdoctorid'"));
			echo "<tr><td>$doc_info[Name]</td>";
			
			// Regd Free
			$regd=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`regd_fee`),0) as regd_tot FROM `consult_patient_payment_details` a, `appointment_book` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`consultantdoctorid`='$consultantdoctorid' AND a.`date` BETWEEN '$date1' AND '$date2' "));
			
			$free_regd=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`regd_fee`),0) as free_regd_tot FROM `consult_patient_payment_details` a, `appointment_book` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`consultantdoctorid`='$consultantdoctorid' AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`opd_id` IN(SELECT `opd_id` FROM `invest_payment_free`)"));
			
			//~ $ongc_regd=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`regd_fee`),0) as ongc_regd_tot FROM `consult_patient_payment_details` a, `appointment_book` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`consultantdoctorid`='$consultantdoctorid' AND a.`date` BETWEEN '$date1' AND '$date2' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `center_no`='C102')"));
			//~ $ongc_regd_amount=$ongc_regd["ongc_regd_tot"];
			
			$regd_total=$regd["regd_tot"]-$free_regd["free_regd_tot"];
			
			$tot_regd_fee+=$regd_total;
			
			$regd_total=number_format($regd_total,2);
			
			// Consultation Fee
			$consult=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`visit_fee`),0) as consult_tot FROM `consult_patient_payment_details` a, `appointment_book` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`consultantdoctorid`='$consultantdoctorid' AND a.`date` BETWEEN '$date1' AND '$date2' "));
			
			$free_consult=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`visit_fee`),0) as free_consult_tot FROM `consult_patient_payment_details` a, `appointment_book` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`consultantdoctorid`='$consultantdoctorid' AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`opd_id` IN(SELECT `opd_id` FROM `invest_payment_free`)"));
			
			//~ $ongc_consult=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`visit_fee`),0) as ongc_consult_tot FROM `consult_patient_payment_details` a, `appointment_book` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`consultantdoctorid`='$consultantdoctorid' AND a.`date` BETWEEN '$date1' AND '$date2' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `center_no`='C102')"));
			//~ $ongc_regd_amount=$ongc_regd["ongc_regd_tot"];
			
			$consult_total=$consult["consult_tot"]-$free_consult["free_consult_tot"];
			
			$tot_visit_fee+=$consult_total;
			
			$consult_total=number_format($consult_total,2);
			
			echo "<td>$consult_total</td>";
			echo "<td>$regd_total</td>";
			
			$service_qry=mysqli_query($link, " SELECT distinct `service_id` FROM `doctor_service_done` WHERE `date` BETWEEN '$date1' AND '$date2' ");
			$i=1;
			while($service=mysqli_fetch_array($service_qry))
			{
				$service_amount=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(a.`amount`),0) AS `tot_amount` FROM `ipd_pat_service_details` a, `doctor_service_done` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`ipd_id` AND a.`service_id`=b.`service_id` AND b.`service_id`='$service[service_id]' AND b.`consultantdoctorid`='$consultantdoctorid' AND b.`date` BETWEEN '$date1' AND '$date2'"));
				
				$each_serv_tot[$i]+=$service_amount["tot_amount"];
				
				echo "<td>$service_amount[tot_amount]</td>";
				
				$i++;
			}
?>
			
<?php
			echo "</tr>";
		}
	}
?>
		<tr style="display:;">
			<th><span class="text-right">Total : </span></th>
			<th><?php echo number_format($tot_visit_fee,2); ?></th>
			<th><?php echo number_format($tot_regd_fee,2); ?></th>
<?php
		for($m=1;$m<$i;$m++)
		{
?>
			<th><?php echo number_format($each_serv_tot[$m],2); ?></th>
<?php
		}
?>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="service_wise_summary")
{
?>
	<p style="margin-top: 2%;" id="print_div"><b>Service Wise Summary Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		<button type="button" class="btn btn-info text-right " onclick="print_page('service_wise_summary','<?php echo $date1;?>','<?php echo $date2;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	
	<table class="table table-hover table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>Date</th>
<?php
			$service_qry=mysqli_query($link, " SELECT distinct `service_id` FROM `ipd_pat_service_details` WHERE `date` BETWEEN '$date1' AND '$date2' ORDER BY `service_id` ASC ");
			while($service=mysqli_fetch_array($service_qry))
			{
				$charge_info=mysqli_fetch_array(mysqli_query($link, "SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$service[service_id]'"));
				
				echo "<th>$charge_info[charge_name]</th>";
			}
?>
			</tr>
		</thead>
<?php
	
	$each_serv_tot=array();
	
	$dis_date_qry=mysqli_query($link, " SELECT DISTINCT `date` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2' ");
	while($dis_date=mysqli_fetch_array($dis_date_qry))
	{
		//echo $dis_date[date]."<br>";
?>
		<tr>
			<td><?php echo convert_date($dis_date["date"]); ?></td>
<?php
		$service_qry=mysqli_query($link, " SELECT distinct `service_id` FROM `ipd_pat_service_details` WHERE `date` BETWEEN '$date1' AND '$date2' ORDER BY `service_id` ASC ");
		$i=1;
		while($service=mysqli_fetch_array($service_qry))
		{
			$service_amount=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `ipd_pat_service_details` WHERE `service_id`='$service[service_id]' AND `date`='$dis_date[date]'"));
			
			$each_serv_tot[$i]+=$service_amount["tot_amount"];
			
			echo "<td>$service_amount[tot_amount]</td>";
			
			$i++;
		}
?>
		</tr>
<?php
	}
?>
		<tr style="display:;">
			<th><span class="text-right">Total : </span></th>
<?php
		for($m=1;$m<$i;$m++)
		{
?>
			<th><?php echo number_format($each_serv_tot[$m],2); ?></th>
<?php
		}
?>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="service_summary")
{
	// Patients
	$patients_array = array();
	$p=1;
	//$pat_ipd_qry=mysqli_query($link, " SELECT `patient_id`, `ipd_id` FROM `ipd_pat_service_details` WHERE `date` BETWEEN '$date1' AND '$date2' GROUP BY `patient_id`,`ipd_id`");
	$pat_ipd_qry=mysqli_query($link, " SELECT `patient_id`, `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date` BETWEEN '$date1' AND '$date2' GROUP BY `patient_id`,`ipd_id`");
	while($pat_ipd=mysqli_fetch_array($pat_ipd_qry))
	{
		$patients_array[$p]=$pat_ipd["patient_id"]."@#@".$pat_ipd["ipd_id"];
		$p++;
	}
	//print_r($patients_array);
	// Services
	$service_ids_array = array();
	$j=1;
	$service_ipd_qry=mysqli_query($link, " SELECT DISTINCT a.`service_id` FROM `ipd_pat_service_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`=3 AND a.`group_id`!=141 AND a.`service_id`>0 AND a.`date` BETWEEN '$date1' AND '$date2' ");
	while($service_ipd=mysqli_fetch_array($service_ipd_qry))
	{
		$service_ids_array[$j]=$service_ipd["service_id"]."@1";
		$j++;
	}
	$service_ot_qry=mysqli_query($link, " SELECT DISTINCT `ot_service_id` FROM `ot_pat_service_details` WHERE `ot_service_id`>0 AND `date` BETWEEN '$date1' AND '$date2' ");
	while($service_ot=mysqli_fetch_array($service_ot_qry))
	{
		$service_ids_array[$j]=$service_ot["ot_service_id"]."@2";
		$j++;
	}
	//print_r($service_ids_array);
?>
	<p style="margin-top: 2%;" id="print_div"><b>Service Summary Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		<button type="button" class="btn btn-info text-right " onclick="print_page('service_summary','<?php echo $date1;?>','<?php echo $date2;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		
		<a class="btn btn-info text-right" href="pages/summary_report_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>" style="margin-right: 5px;"><i class="icon-file icon-large"></i>Excel</a>
	</p>
	
	<table class="table table-hover table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>IPD ID</th>
				<th>Name</th>
				<th>Room Rent</th>
<?php
				foreach($service_ids_array AS $service_ids)
				{
					if($service_ids)
					{
						$service_ids=explode("@", $service_ids);
						$service_id=$service_ids[0];
						$val=$service_ids[1];
						if($val==1)
						{
							// IPD
							$service_det=mysqli_fetch_array(mysqli_query($link, "SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$service_id'"));
							$service_name=$service_det["charge_name"];
							if(!$service_name)
							{
								$service_det=mysqli_fetch_array(mysqli_query($link, "SELECT `service_text` FROM `ipd_pat_service_details` WHERE `service_id`='$service_id'"));
								$service_name=$service_det["service_text"];
							}
						}
						if($val==2)
						{
							// OT
							$service_det=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `ot_type_master` WHERE `type_id`='$service_id'"));
							$service_name=$service_det["type"];
						}
						
						echo "<th>$service_name</th>";
					}
				}
?>
			</tr>
		</thead>
<?php
	
	$each_serv_tot=array();
	$tot_bed_amount=0;
	$pat=1;
	foreach($patients_array AS $patients)
	{
		if($patients)
		{
			$patients=explode("@#@", $patients);
			$patient_id=$patients[0];
			$ipd_id=$patients[1];
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `patient_info` WHERE `patient_id`='$patient_id'"));
		
			$service_amount_ipd=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `ipd_pat_service_details` WHERE `group_id`='141' AND `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
			$tot_serv_amount=$service_amount_ipd["tot_amount"];
			$tot_bed_amount+=$tot_serv_amount;
			$tot_serv_amount=number_format($tot_serv_amount,2);
	?>
			<tr>
				<td><?php echo $pat; ?></td>
				<td><?php echo $ipd_id; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $tot_serv_amount; ?></td>
	<?php
				$i=1;
				foreach($service_ids_array AS $service_ids)
				{
					if($service_ids)
					{
						$service_ids=explode("@", $service_ids);
						$service_id=$service_ids[0];
						$val=$service_ids[1];
						if($val==1)
						{
							// IPD
							$service_amount_ipd=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `ipd_pat_service_details` WHERE `service_id`='$service_id' AND `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
							
							$tot_serv_amount=$service_amount_ipd["tot_amount"];
						}
						if($val==2)
						{
							// OT
							$service_amount_ot=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `ot_pat_service_details` WHERE `ot_service_id`='$service_id' AND `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
							
							$tot_serv_amount=$service_amount_ot["tot_amount"];
						}
						
						$each_serv_tot[$i]+=$tot_serv_amount;
						
						$tot_serv_amount=number_format($tot_serv_amount,2);
						
						echo "<td>$tot_serv_amount</td>";
						
						$i++;
					}
				}
	?>
			</tr>
	<?php
			$pat++;
		}
	}
	
	//~ $dis_date_qry=mysqli_query($link, " SELECT DISTINCT `date` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2' ");
	//~ while($dis_date=mysqli_fetch_array($dis_date_qry))
	//~ {
		
	//~ }
?>
		<tr style="display:;">
			<th colspan="3"><span class="text-right">Total : </span></th>
			<th><?php echo number_format($tot_bed_amount,2); ?></th>
<?php
		for($m=1;$m<$i;$m++)
		{
?>
			<th><?php echo number_format($each_serv_tot[$m],2); ?></th>
<?php
		}
?>
		</tr>
	</table>
<?php
}
?>
