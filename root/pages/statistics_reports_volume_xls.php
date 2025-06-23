<?php
include('../../includes/connection.php');

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$date1=$_GET['date1'];
$date2=$_GET['date2'];

$filename ="statistics_reports_volume_".$date1."_to_".$date2.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$today=date("Y-m-d");

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('M Y', $timestamp);
	return $new_date;
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

$start    = new DateTime($date1);
$start->modify('first day of this month');
$end      = new DateTime($date2);
$end->modify('first day of next month');
$interval = DateInterval::createFromDateString('1 day');
$period   = new DatePeriod($start, $interval, $end);

?>
<table class="table table-condensed table-hover table-boredered">
	<thead class="table_header_fix">
		<tr>
			<th>DATE</th>
			<th>DAY</th>
			<th colspan="5">OPD PATIENTS</th>
			<th colspan="3">IPD PATIENTS</th>
			<th colspan="3">SURGERY</th>
			<th colspan="1">ADMISSION</th>
			<th colspan="1">DISCHARGE</th>
			<th colspan="1">PHARMACY</th>
			<th colspan="2">LAB TEST</th>
			<th colspan="1">USG</th>
			<th colspan="1">X-RAY</th>
			<th colspan="1">ECG</th>
			<th colspan="1">ENDOSCOPY</th>
			<th colspan="1">DIALYSIS</th>
			<th colspan="1">PROCEDURES</th>
			<th colspan="1">DENTAL</th>
			<th colspan="1">REFERRALS</th>
			<th colspan="1">BIRTHS</th>
			<th colspan="1">DEATHS</th>
			<th colspan="1">MLC</th>
			<th colspan="1">DAYCARE</th>
		</tr>
		<tr>
			<th>Date</th>
			<th>day</th>
			<th colspan="5">OPD Patients</th>
			<th>ICU</th>
			<th>Ward & Room</th>
			<th>Total Ocuupancy</th>
			<th colspan="3">Surgery</th>
			<th colspan="1">Admission</th>
			<th colspan="1">Discharge</th>
			<th colspan="1">Pharmacy</th>
			<th colspan="2">Lab Test</th>
			<th colspan="1">USG</th>
			<th colspan="1">X-Ray</th>
			<th colspan="1">ECG</th>
			<th colspan="1">Endoscopy</th>
			<th colspan="1">Dialysis</th>
			<th colspan="1">Gastro/FNAC+Others</th>
			<th colspan="1">Dental Procedure</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th>NP</th>
			<th>PFU</th>
			<th>NPC/ER</th>
			<!--<th>NDE</th>
			<th>PFDE</th>-->
			<th>FFU</th>
			<th>Total</th>
			<th colspan="3">12 MN</th>
			<th>Major</th>
			<th>Minor</th>
			<th>Total</th>
			<th></th>
			<th></th>
			<th></th>
			<th>IPD</th>
			<th>OPD</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
<?php
$i=1;
$np_week=$pfu_week=$ncp_week=$nde_week=$pfde_week=$ffu_week=$opd_total_week=0;
$icu_ipd_admit_week=$non_icu_ipd_admit_week=$total_ipd_admit_week=$ipd_new_pat_week=$ipd_discharge_pat_week=0;
$majot_ot_num_week=$minor_ot_num_week=$total_ot_num_week=0;
$opd_test_num_week=$ipd_test_num_week=$total_usg_week=$total_xray_week=$total_ecg_week=$total_endoscopy_week=0;
$dialysis_pat_all_week=0;
$dental_procedure_num_week=$other_procedure_num_week=0;
$referral_num_week=0;
$birth_num_week=$death_num_week=$mlc_num_week=0;
$daycare_pat_week=0;

foreach ($period as $dt)
{
	if($dt)
	{
		$date=$dt->format("Y-m-d");
		
		$day=date("D", strtotime($date));
		
		$last_day_month=date("Y-m-t", strtotime($date));
		
		$referral_num=0;
		
		// OPD
		$all_ffu=$all_opd_pat=0;
		$opd_pat_new=$opd_pat_pfu=$opd_pat_ffu=0;
		$pat_pay_det_opd_qry=mysqli_query($link," SELECT * FROM `consult_patient_payment_details` WHERE `date`='$date'");
		while($pat_pay_det_opd=mysqli_fetch_array($pat_pay_det_opd_qry))
		{
			// Patient count
			if($pat_pay_det_opd["visit_fee"]>0)
			{
				$pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='6' "));
				if($pat_paid_follow)
				{
					$opd_pat_pfu++;
					$all_opd_pat++;
					$pfu_week++;
				}else
				{
					$pat_referral=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='10' "));
					if($pat_referral)
					{
						$referral_num++;
						$all_opd_pat++;
						$referral_num_week++;
					}
					else
					{
						$opd_pat_new++;
						$all_opd_pat++;
						$np_week++;
					}
				}
			}
			else
			{
				$opd_pat_ffu++;
				$all_opd_pat++;
				$all_ffu++;
				$ffu_week++;
			}
		}
		// CASUALTY / Emergency
		$casu_pat_new=0;
		$casu_pat_pfu=0;
		$casu_pat_ffu=0;
		
		$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`='4'");
		while($casu_pat=mysqli_fetch_array($casu_pat_qry))
		{
			$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='186' AND `service_id`='251' "));
			
			if($only_casu_qry["tot_casu"]>0)
			{
				$pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='8' "));
				if($pat_paid_follow)
				{
					//~ $casu_pat_pfu++;
					$casu_pat_new++;
					$all_opd_pat++;
					$ncp_week++;
				}else
				{
					$casu_pat_new++;
					$all_opd_pat++;
					$ncp_week++;
				}
			}
			else
			{
				$casu_pat_ffu++;
				$all_ffu++;
				$all_opd_pat++;
				$ffu_week++;
			}
		}
		// Dental
		//~ $dental_pat_num=$dental_pat_new=$dental_pat_pfu=$dental_pat_ffu=0;
		
		//~ $dental_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`='6'");
		//~ while($dental_pat=mysqli_fetch_array($dental_pat_qry))
		//~ {
			//~ $dental_pat_ser=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_dental FROM `ipd_pat_service_details` WHERE `patient_id`='$dental_pat[patient_id]' AND `ipd_id`='$dental_pat[opd_id]' AND `group_id`='188' AND `service_id`='253' "));
			//~ if($dental_pat_ser["tot_dental"]>0)
			//~ {
				//~ $pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='11' "));
				//~ if($pat_paid_follow)
				//~ {
					//~ $dental_pat_pfu++;
					//~ $all_opd_pat++;
					//~ $pfde_week++;
				//~ }else
				//~ {
					//~ $dental_pat_new++;
					//~ $all_opd_pat++;
					//~ $nde_week++;
				//~ }
			//~ }
			//~ else
			//~ {
				//~ $dental_pat_ffu++;
				//~ $all_opd_pat++;
				//~ $all_ffu++;
				//~ $ffu_week++;
			//~ }
		//~ }
		// IPD
		$ipd_admit_pat_num_icu=mysqli_num_rows(mysqli_query($link, " SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date') AND `ward_id`=4 "));
		
		$ipd_admit_pat_num_non_icu=mysqli_num_rows(mysqli_query($link, " SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date') AND `ward_id`!=4 "));
		
		$total_ipd_admit_pat_num=$ipd_admit_pat_num_icu+$ipd_admit_pat_num_non_icu;
		
		$icu_ipd_admit_week+=$ipd_admit_pat_num_icu;
		$non_icu_ipd_admit_week+=$ipd_admit_pat_num_non_icu;
		$total_ipd_admit_week+=$ipd_admit_pat_num_icu+$ipd_admit_pat_num_non_icu;
		
		// Surgery
		$majot_ot_num=$minor_ot_num=$total_ot_num=0;
		
		$major_ot = array();
		array_push($major_ot, 189, 194);
		$major_ot = join(',',$major_ot);
		
		$majot_ot_num=mysqli_num_rows(mysqli_query($link," SELECT DISTINCT `ipd_id` FROM `ipd_pat_service_details` WHERE `date`='$date' AND `group_id` IN($major_ot) "));
		$majot_ot_num_week+=$majot_ot_num;
		
		$minor_ot = array();
		array_push($minor_ot, 195);
		$minor_ot = join(',',$minor_ot);
		
		$minor_ot_num=mysqli_num_rows(mysqli_query($link," SELECT DISTINCT `ipd_id` FROM `ipd_pat_service_details` WHERE `date`='$date' AND `group_id` IN($minor_ot) "));
		$minor_ot_num_week+=$minor_ot_num;
		
		$total_ot_num+=$majot_ot_num+$minor_ot_num;
		$total_ot_num_week+=$total_ot_num;
		
		// IPD Admission
		$ipd_new_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`='3' "));
		$ipd_new_pat_week+=$ipd_new_pat_num;
		
		// IPD Discharge
		$ipd_discharge_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ipd_pat_discharge_details` WHERE `date`='$date' "));
		$ipd_discharge_pat_week+=$ipd_discharge_pat_num;
		
		// Laboratory OPD
		$opd_lab_pat=$opd_lab_path_amount=$opd_test_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='2' AND b.`date`='$date' ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='1') AND `date`='$date' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$opd_lab_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$opd_test_num++;
					//~ $opd_lab_path_amount+=$opd_test["test_rate"];
				}
			}
		}
		$opd_test_num_week+=$opd_test_num;
		
		// Laboratory IPD
		$ipd_lab_pat=$ipd_lab_path_amount=$ipd_test_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date`='$date' ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='1') AND `date`='$date' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$ipd_lab_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$ipd_test_num++;
					//~ $ipd_lab_path_amount+=$opd_test["test_rate"];
				}
			}
		}
		$ipd_test_num_week+=$ipd_test_num;
		
		// SONOGRAPHY OPD
		$total_usg=0;
		$opd_usg_pat=$opd_usg_path_amount=$opd_usg_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='10' AND b.`date`='$date' ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='128') AND `date`='$date' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$opd_usg_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$opd_usg_num++;
					$total_usg++;
					//~ $opd_usg_path_amount+=$opd_test["test_rate"];
				}
			}
		}
		// SONOGRAPHY IPD
		$ipd_usg_pat=$ipd_usg_path_amount=$ipd_usg_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date`='$date' ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='128') AND `date`='$date' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$ipd_usg_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$ipd_usg_num++;
					$total_usg++;
					$ipd_usg_path_amount+=$opd_test["test_rate"];
				}
			}
		}
		
		$total_usg_week+=$total_usg;
		
		// X-RAY OPD
		$total_xray=0;
		$opd_xray_pat=$opd_xray_path_amount=$opd_xray_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='11' AND b.`date`='$date' ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='40') AND `date`='$date' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$opd_xray_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$opd_xray_num++;
					$total_xray++;
					//~ $opd_xray_path_amount+=$opd_test["test_rate"];
				}
			}
		}
		// X-RAY IPD
		$ipd_xray_pat=$ipd_xray_path_amount=$ipd_xray_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date`='$date' ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='40') AND `date`='$date' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$ipd_xray_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$ipd_xray_num++;
					$total_xray++;
					//~ $ipd_xray_path_amount+=$opd_test["test_rate"];
				}
			}
		}
		$total_xray_week+=$total_xray;
		
		// ECG OPD
		$total_ecg=0;
		$opd_ecg_pat=$opd_ecg_path_amount=$opd_ecg_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='12' AND b.`date`='$date' ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='3') AND `date`='$date' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$opd_ecg_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$opd_ecg_num++;
					$total_ecg++;
					//~ $opd_ecg_path_amount+=$opd_test["test_rate"];
				}
			}
		}
		// ECG IPD
		$ipd_ecg_pat=$ipd_ecg_path_amount=$ipd_ecg_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date`='$date' ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='3') AND `date`='$date' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$ipd_ecg_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$ipd_ecg_num++;
					$total_ecg++;
					//~ $ipd_ecg_path_amount+=$opd_test["test_rate"];
				}
			}
		}
		$total_ecg_week+=$total_ecg;
		
		// ENDOSCOPY OPD
		$total_endoscopy=0;
		$opd_endos_pat=$opd_endos_path_amount=$opd_endos_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='13' AND b.`date`='$date' ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='121') AND `date`='$date' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$opd_endos_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$opd_endos_num++;
					$total_endoscopy++;
					//~ $opd_endos_path_amount+=$opd_test["test_rate"];
				}
			}
		}
		// ENDOSCOPY IPD
		$ipd_endos_pat=$ipd_endos_path_amount=$ipd_endos_num=0;
		$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date`='$date' ");
		while($lab_pat=mysqli_fetch_array($lab_pat_qry))
		{
			$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='121') AND `date`='$date' ");
			$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
			if($opd_test_num_rows>0)
			{
				$ipd_endos_pat++;
				
				while($opd_test=mysqli_fetch_array($opd_test_qry))
				{
					$total_endoscopy++;
					//~ $ipd_endos_path_amount+=$opd_test["test_rate"];
				}
			}
		}
		$total_endoscopy_week+=$total_endoscopy;
		
		// Dialysis
		$dialysis_pat_all=0;
		$dialysis_pat_new=0;
		$dialysis_pat_pfu=0;
		$dialysis_pat_ffu=0;
		$dialysis_amount=0;
		
		$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`='7' ");
		while($casu_pat=mysqli_fetch_array($casu_pat_qry))
		{
			$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='187' AND `service_id`='252' "));
			
			if($only_casu_qry["tot_casu"]>0)
			{
				$pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='16' "));
				if($pat_paid_follow)
				{
					$dialysis_pat_pfu++;
					$dialysis_pat_all++;
				}else
				{
					$dialysis_pat_new++;
					$dialysis_pat_all++;
				}
				
				//~ $dialysis_amount+=$only_casu_qry["tot_casu"];
				
			}
			else
			{
				$dialysis_pat_ffu++;
				$dialysis_pat_all++;
			}
		}
		
		// IPD Dailysis
		$ipd_dialysis_pat=mysqli_num_rows(mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_pat_service_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date`='$date' AND a.`service_id`='252' $user_str_a ORDER BY a.`slno`"));
		
		$dialysis_pat_all+=$ipd_dialysis_pat;

		$dialysis_pat_all_week+=$dialysis_pat_all;
		
		// Other Procedure
		$other_procedure = array();
		array_push($other_procedure, 176, 177, 185, 179, 182, 184, 181, 178);
		$other_procedure = join(',',$other_procedure);
		
		$other_procedure_pat=$other_procedure_num=0;
		
		//~ $ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date`='$date' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id` IN($other_procedure)) ORDER BY a.`slno`");
		
		//$ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='15' AND b.`date`='$date' ORDER BY a.`slno`");
		
		$ipd_casual_pay_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`='15'");
		while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
		{
			$other_procedure_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS tot_casu FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `patient_id`='$ipd_casual_pay[patient_id]' AND `ipd_id`='$ipd_casual_pay[opd_id]' ")); // AND `ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id` IN($other_procedure))
			
			if($other_procedure_val["tot_casu"]>0)
			{
				$other_procedure_pat++;
				
				//~ $dental_procedure_amount+=$other_procedure_val["tot_casu"];
				
				$other_serv_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$ipd_casual_pay[patient_id]' AND `ipd_id`='$ipd_casual_pay[opd_id]' ")); // AND `group_id` IN($other_procedure)
				$other_procedure_num+=$other_serv_num;
			}
		}
		$other_procedure_num_week+=$other_procedure_num;
		
		// Dental Procedure `group_id`='192'
		$dental_procedure_pat=$dental_procedure_num=$dental_procedure_amount=0;
		
		//~ $ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date`='$date' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id`='192') ORDER BY a.`slno`");
		
		$ipd_casual_pay_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`='6'");
		
		while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
		{
			$dental_procedure=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS tot_casu FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `patient_id`='$ipd_casual_pay[patient_id]' AND `ipd_id`='$ipd_casual_pay[opd_id]' ")); //  AND `ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id`='192')
			
			if($dental_procedure["tot_casu"]>0)
			{
				$dental_procedure_pat++;
				
				//~ $dental_procedure_amount+=$dental_procedure["tot_casu"];
				
				$dental_serv_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$ipd_casual_pay[patient_id]' AND `ipd_id`='$ipd_casual_pay[opd_id]' ")); // AND `group_id`='192'
				$dental_procedure_num+=$dental_serv_num;
			}
		}
		$dental_procedure_num_week+=$dental_procedure_num;
		
		// Birth
		$birth_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `dob`='$date' "));
		$birth_num_week+=$birth_num;
		
		// Death
		$death_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_pat_death_details` WHERE `death_date`='$date' AND `type`='105' "));
		$death_num_week+=$death_num;
		
		// MLC
		$mlc_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `emergency_patient_details` WHERE `date`='$date' AND `type_id`='9' "));
		$mlc_num_week+=$mlc_num;
		
		// Daycare
		$daycare_pat=0;
		$daycare_amount=0;
		
		$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`='5' $user_str");
		while($casu_pat=mysqli_fetch_array($casu_pat_qry))
		{
			$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='167' AND `service_id`='150' "));
			
			if($only_casu_qry["tot_casu"]>0)
			{
				$daycare_pat++;
				$daycare_pat_week++;
				
				$daycare_amount+=$only_casu_qry["tot_casu"];
			}
		}
?>
		<tr>
			<td><?php echo date("d/M", strtotime($date)); ?></td>
			<td><?php echo $day; ?></td>
			<td><?php echo $opd_pat_new; ?></td>
			<td><?php echo $opd_pat_pfu; ?></td>
			<td><?php echo $casu_pat_new; ?></td>
			<!--<td><?php echo $dental_pat_new; ?></td>
			<td><?php echo $dental_pat_pfu; ?></td>-->
			<td><?php echo $all_ffu; ?></td>
			<td><?php echo $all_opd_pat; ?></td>
			<td><?php echo $ipd_admit_pat_num_icu; ?></td>
			<td><?php echo $ipd_admit_pat_num_non_icu; ?></td>
			<td><?php echo $total_ipd_admit_pat_num; ?></td>
			<td><?php echo $majot_ot_num; ?></td>
			<td><?php echo $minor_ot_num; ?></td>
			<td><?php echo $total_ot_num; ?></td>
			<td><?php echo $ipd_new_pat_num; ?></td>
			<td><?php echo $ipd_discharge_pat_num; ?></td>
			<td>0</td>
			<td><?php echo $ipd_test_num; ?></td>
			<td><?php echo $opd_test_num; ?></td>
			<td><?php echo $total_usg; ?></td>
			<td><?php echo $total_xray; ?></td>
			<td><?php echo $total_ecg; ?></td>
			<td><?php echo $total_endoscopy; ?></td>
			<td><?php echo $dialysis_pat_all; ?></td>
			<td><?php echo $other_procedure_num; ?></td>
			<td><?php echo $dental_procedure_num; ?></td>
			<td><?php echo $referral_num; ?></td>
			<td><?php echo $birth_num; ?></td>
			<td><?php echo $death_num; ?></td>
			<td><?php echo $mlc_num; ?></td>
			<td><?php echo $daycare_pat; ?></td>
		</tr>
<?php
		$opd_total_week+=$all_opd_pat;
		if($day=="Sun" || $last_day_month==$date)
		{
?>
		<tr>
			<td>Total</td>
			<td></td>
			<td><?php echo $np_week; ?></td>
			<td><?php echo $pfu_week; ?></td>
			<td><?php echo $ncp_week; ?></td>
			<!--<td><?php echo $nde_week; ?></td>
			<td><?php echo $pfde_week; ?></td>-->
			<td><?php echo $ffu_week; ?></td>
			<td><?php echo $opd_total_week; ?></td>
			<td><?php echo $icu_ipd_admit_week; ?></td>
			<td><?php echo $non_icu_ipd_admit_week; ?></td>
			<td><?php echo $total_ipd_admit_week; ?></td>
			<td><?php echo $majot_ot_num_week; ?></td>
			<td><?php echo $minor_ot_num_week; ?></td>
			<td><?php echo $total_ot_num_week; ?></td>
			<td><?php echo $ipd_new_pat_week; ?></td>
			<td><?php echo $ipd_discharge_pat_week; ?></td>
			<td>0</td>
			<td><?php echo $ipd_test_num_week; ?></td>
			<td><?php echo $opd_test_num_week; ?></td>
			<td><?php echo $total_usg_week; ?></td>
			<td><?php echo $total_xray_week; ?></td>
			<td><?php echo $total_ecg_week; ?></td>
			<td><?php echo $total_endoscopy_week; ?></td>
			<td><?php echo $dialysis_pat_all_week; ?></td>
			<td><?php echo $other_procedure_num_week; ?></td>
			<td><?php echo $dental_procedure_num_week; ?></td>
			<td><?php echo $referral_num_week; ?></td>
			<td><?php echo $birth_num_week; ?></td>
			<td><?php echo $death_num_week; ?></td>
			<td><?php echo $mlc_num_week; ?></td>
			<td><?php echo $daycare_pat_week; ?></td>
		</tr>
<?php
			$np_week=$pfu_week=$ncp_week=$nde_week=$pfde_week=$ffu_week=$opd_total_week=0;
			$icu_ipd_admit_week=$non_icu_ipd_admit_week=$total_ipd_admit_week=$ipd_new_pat_week=$ipd_discharge_pat_week=0;
			$majot_ot_num_week=$minor_ot_num_week=$total_ot_num_week=0;
			$opd_test_num_week=$ipd_test_num_week=$total_usg_week=$total_xray_week=$total_ecg_week=$total_endoscopy_week=0;
			$dialysis_pat_all_week=0;
			$dental_procedure_num_week=$other_procedure_num_week=0;
			$referral_num_week=0;
			$birth_num_week=$death_num_week=$mlc_num_week=0;
			$daycare_pat_week=0;
		}
		$i++;
	}
}
?>
</table>
