<?php
include("../../includes/connection.php");

$date1=$_POST['date1'];
$date2=$_POST['date2'];

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

$rupees_symbol="&#x20b9; ";
setlocale(LC_MONETARY, 'en_IN');
function money_view($val)
{
	//$val=number_format($val,2);
	$val = money_format('%!i', $val);
	return $val;
}

$start    = new DateTime($date1);
$start->modify('first day of this month');
$end      = new DateTime($date2);
$end->modify('first day of next month');
$interval = DateInterval::createFromDateString('1 day');
$period   = new DatePeriod($start, $interval, $end);

if($_POST["type"]=="daily_volume")
{
?>
	<p style="margin-top: 2%;"><b>Daily Volume Statistics from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		<!--<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/doctor_service_reports_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&service_id=<?php echo $service_id;?>&consultantdoctorid=<?php echo $consultantdoctorid;?>">Export to Excel</a></span>-->
		
	</p>
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
<?php
}

if($_POST["type"]=="daily_revenue")
{
?>
	<table class="table table-condensed table-hover table-boredered">
		<thead class="table_header_fix">
			<tr>
				<th>DATE</th>
				<th>DAY</th>
				<th colspan="4">OPD Revenue</th>
				<th colspan="4">Emergency Room Revenue</th>
				<th colspan="4">Daycare Revenue</th>
				<th colspan="4">IPD Revenue</th>
				<th colspan="4">Pharmacy Revenue</th>
				<th colspan="4">LAB Revenue</th>
				<th colspan="4">USG Revenue</th>
				<th colspan="4">X-Ray Revenue</th>
				<th colspan="4">Endoscopy Revenue</th>
				<th colspan="4">ECG Revenue</th>
				<th colspan="4">Dialysis Revenue</th>
				<th colspan="4">Ambulance Revenue</th>
				<th colspan="4">Dental Procedure Revenue</th>
				<th colspan="4">Other Procedure Revenue</th>
				<th colspan="4">Miscelleneous Revenue</th>
				<th colspan="4">Total Revenue</th>
				<th>Grand Total</th>
			</tr>
			<tr>
				<td></td>
				<td></td>
			<?php
				$td=0;
				while($td<16)
				{
					echo "<td>Credit</td>";
					echo "<td>Cash</td>";
					echo "<td>Card</td>";
					echo "<th>Total</th>";
					$td++;
				}
			?>
				<td></td>
			</tr>
		</thead>
<?php
		$opd_cash_amount_week=$opd_card_amount_week=$opd_credit_amount_week=$opd_total_amount_week=0;
		$daycare_cash_amount_week=$daycare_card_amount_week=$daycare_credit_amount_week=$daycare_total_amount_week=0;
		$ipd_cash_amount_week=$ipd_card_amount_week=$ipd_credit_amount_week=$ipd_total_amount_week=0;
		$lab_cash_amount_week=$lab_card_amount_week=$lab_credit_amount_week=$lab_total_amount_week=0;
		$usg_cash_amount_week=$usg_card_amount_week=$usg_credit_amount_week=$usg_total_amount_week=0;
		$xray_cash_amount_week=$xray_card_amount_week=$xray_credit_amount_week=$xray_total_amount_week=0;
		$ecg_cash_amount_week=$ecg_card_amount_week=$ecg_credit_amount_week=$ecg_total_amount_week=0;
		$endo_cash_amount_week=$endo_card_amount_week=$endo_credit_amount_week=$endo_total_amount_week=0;
		$dialysis_cash_amount_week=$dialysis_card_amount_week=$dialysis_credit_amount_week=$dialysis_total_amount_week=0;
		$emergency__cash_amount_week=$emergency__card_amount_week=$emergency__credit_amount_week=$emergency__total_amount_week=0;
		$denpro_cash_amount_week=$denpro_card_amount_week=$denpro_credit_amount_week=$denpro_total_amount_week=0;
		$misc_cash_amount_week=$misc_card_amount_week=$misc_credit_amount_week=$misc_total_amount_week=0;
		$ambulance_cash_amount_week=$ambulance_card_amount_week=$ambulance_credit_amount_week=$ambulance_total_amount_week=0;
		$otherpro_cash_amount_week=$otherpro_card_amount_week=$otherpro_credit_amount_week=$otherpro_total_amount_week=0;
		$each_day_credit_week=$each_day_cash_week=$each_day_card_week=$each_day_total_week=0;
		
		foreach ($period as $dt)
		{
			if($dt)
			{
				$date=$dt->format("Y-m-d");
				$day=date("D", strtotime($date));
				
				$last_day_month=date("Y-m-t", strtotime($date));
				
				$each_day_credit=0;
				$each_day_cash=0;
				$each_day_card=0;
				$each_day_total=0;
				
				// OPD
				$opd_total_amount=0;
					
					// Cash
					$opd_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `consult_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' "));
					$opd_cash_amount=$opd_cash["tot"];
					
					$opd_total_amount+=$opd_cash_amount;
					$opd_cash_amount_week+=$opd_cash_amount;
					$each_day_cash+=$opd_cash_amount;
					$each_day_cash_week+=$opd_cash_amount;
				
					// Card
					$opd_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `consult_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' "));
					$opd_card_amount=$opd_card["tot"];
					
					$opd_total_amount+=$opd_card_amount;
					$opd_card_amount_week+=$opd_card_amount;
					$each_day_card+=$opd_card_amount;
					$each_day_card_week+=$opd_card_amount;
					
					// Credit
					$opd_credit_amount=0;
					$opd_credit_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' ");
					while($opd_credit=mysqli_fetch_array($opd_credit_qry))
					{
						$opd_credit_amount+=$opd_credit["balance"];
						
						// Check Same Day Balance Receive
						$opd_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `consult_payment_detail` WHERE `patient_id`='$opd_credit[patient_id]' AND `opd_id`='$opd_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
						$opd_sameday_balance_amount=$opd_sameday_balance["tot"];
						
						$opd_credit_amount-=$opd_sameday_balance_amount;
					}
					
					$opd_total_amount+=$opd_credit_amount;
					$opd_credit_amount_week+=$opd_credit_amount;
					$each_day_credit+=$opd_credit_amount;
					$each_day_credit_week+=$opd_credit_amount;
				
				$each_day_total+=$opd_total_amount;
				$each_day_total_week+=$opd_total_amount;
				$opd_total_amount_week+=$opd_total_amount;
				
				// Daycare
				$pat_visit_type=5;
				$daycare_total_amount=0;
					// Cash
					$daycare_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$daycare_cash_amount=$daycare_cash["tot"];
					
					$daycare_total_amount+=$daycare_cash_amount;
					$daycare_cash_amount_week+=$daycare_cash_amount;
					$each_day_cash+=$daycare_cash_amount;
					$each_day_cash_week+=$daycare_cash_amount;
					
					// Card
					$daycare_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$daycare_card_amount=$daycare_card["tot"];
					
					$daycare_total_amount+=$daycare_card_amount;
					$daycare_card_amount_week+=$daycare_card_amount;
					$each_day_card+=$daycare_card_amount;
					$each_day_card_week+=$daycare_card_amount;
					
					// Credit
					$daycare_credit_amount=0;
					$daycare_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($daycare_credit=mysqli_fetch_array($daycare_credit_qry))
					{
						$daycare_credit_amount+=$daycare_credit["balance"];
						
						// Check Same Day Balance Receive
						$daycare_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$daycare_credit[patient_id]' AND `ipd_id`='$daycare_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
						$daycare_sameday_balance_amount=$daycare_sameday_balance["tot"];
						
						$daycare_credit_amount-=$daycare_sameday_balance_amount;
					}
					$daycare_total_amount+=$daycare_credit_amount;
					$daycare_credit_amount_week+=$daycare_credit_amount;
					$each_day_credit+=$daycare_credit_amount;
					$each_day_credit_week+=$daycare_credit_amount;
					
				$each_day_total+=$daycare_total_amount;
				$each_day_total_week+=$daycare_total_amount;
				$daycare_total_amount_week+=$daycare_total_amount;
				
				// IPD
				$pat_visit_type=3;
				$ipd_total_amount=0;
					// Cash
					$ipd_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$ipd_cash_amount=$ipd_cash["tot"];
					
					$ipd_total_amount+=$ipd_cash_amount;
					$ipd_cash_amount_week+=$ipd_cash_amount;
					$each_day_cash+=$ipd_cash_amount;
					$each_day_cash_week+=$ipd_cash_amount;
					
					// Card
					$ipd_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$ipd_card_amount=$ipd_card["tot"];
					
					$ipd_total_amount+=$ipd_card_amount;
					$ipd_card_amount_week+=$ipd_card_amount;
					$each_day_card+=$ipd_card_amount;
					$each_day_card_week+=$ipd_card_amount;
					
					// Credit
					$ipd_credit_amount=0;
					$ipd_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($ipd_credit=mysqli_fetch_array($ipd_credit_qry))
					{
						$ipd_credit_amount+=$ipd_credit["balance"];
						
						// Check Same Day Balance Receive
						$ipd_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$ipd_credit[patient_id]' AND `ipd_id`='$ipd_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
						$ipd_sameday_balance_amount=$ipd_sameday_balance["tot"];
						
						$ipd_credit_amount-=$ipd_sameday_balance_amount;
					}
					$ipd_total_amount+=$ipd_credit_amount;
					$ipd_credit_amount_week+=$ipd_credit_amount;
					$each_day_credit+=$ipd_credit_amount;
					$each_day_credit_week+=$ipd_credit_amount;
					
				$each_day_total+=$ipd_total_amount;
				$each_day_total_week+=$ipd_total_amount;
				$ipd_total_amount_week+=$ipd_total_amount;
				
				// Lab
				$pat_visit_type=2;
				$lab_total_amount=0;
					
					// Cash
					$lab_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
					$lab_cash_amount=$lab_cash["tot"];
					
					$lab_total_amount+=$lab_cash_amount;
					$lab_cash_amount_week+=$lab_cash_amount;
					$each_day_cash+=$lab_cash_amount;
					$each_day_cash_week+=$lab_cash_amount;
				
					// Card
					$lab_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
					$lab_card_amount=$lab_card["tot"];
					
					$lab_total_amount+=$lab_card_amount;
					$lab_card_amount_week+=$lab_card_amount;
					$each_day_card+=$lab_card_amount;
					$each_day_card_week+=$lab_card_amount;
					
					// Credit
					$lab_credit_amount=0;
					$lab_credit_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($lab_credit=mysqli_fetch_array($lab_credit_qry))
					{
						$lab_credit_amount+=$lab_credit["balance"];
						
						// Check Same Day Balance Receive
						$lab_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$lab_credit[patient_id]' AND `opd_id`='$lab_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
						$lab_sameday_balance_amount=$lab_sameday_balance["tot"];
						
						$lab_credit_amount-=$lab_sameday_balance_amount;
					}
					
					$lab_total_amount+=$lab_credit_amount;
					$lab_credit_amount_week+=$lab_credit_amount;
					$each_day_credit+=$lab_credit_amount;
					$each_day_credit_week+=$lab_credit_amount;
				
				$each_day_total+=$lab_total_amount;
				$each_day_total_week+=$lab_total_amount;
				$lab_total_amount_week+=$lab_total_amount;
				
				// USG
				$pat_visit_type=10;
				$usg_total_amount=0;
					
					// Cash
					$usg_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
					$usg_cash_amount=$usg_cash["tot"];
					
					$usg_total_amount+=$usg_cash_amount;
					$usg_cash_amount_week+=$usg_cash_amount;
					$each_day_cash+=$usg_cash_amount;
					$each_day_cash_week+=$usg_cash_amount;
				
					// Card
					$usg_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
					$usg_card_amount=$usg_card["tot"];
					
					$usg_total_amount+=$usg_card_amount;
					$usg_card_amount_week+=$usg_card_amount;
					$each_day_card+=$usg_card_amount;
					$each_day_card_week+=$usg_card_amount;
					
					// Credit
					$usg_credit_amount=0;
					$usg_credit_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($usg_credit=mysqli_fetch_array($usg_credit_qry))
					{
						$usg_credit_amount+=$usg_credit["balance"];
						
						// Check Same Day Balance Receive
						$usg_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$usg_credit[patient_id]' AND `opd_id`='$usg_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
						$usg_sameday_balance_amount=$usg_sameday_balance["tot"];
						
						$usg_credit_amount-=$usg_sameday_balance_amount;
					}
					
					$usg_total_amount+=$usg_credit_amount;
					$usg_credit_amount_week+=$usg_credit_amount;
					$each_day_credit+=$usg_credit_amount;
					$each_day_credit_week+=$usg_credit_amount;
				
				$each_day_total+=$usg_total_amount;
				$each_day_total_week+=$usg_total_amount;
				$usg_total_amount_week+=$usg_total_amount;
				
				// XRAY
				$pat_visit_type=11;
				$xray_total_amount=0;
					
					// Cash
					$xray_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
					$xray_cash_amount=$xray_cash["tot"];
					
					$xray_total_amount+=$xray_cash_amount;
					$xray_cash_amount_week+=$xray_cash_amount;
					$each_day_cash+=$xray_cash_amount;
					$each_day_cash_week+=$xray_cash_amount;
				
					// Card
					$xray_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
					$xray_card_amount=$xray_card["tot"];
					
					$xray_total_amount+=$xray_card_amount;
					$xray_card_amount_week+=$xray_card_amount;
					$each_day_card+=$xray_card_amount;
					$each_day_card_week+=$xray_card_amount;
					
					// Credit
					$xray_credit_amount=0;
					$xray_credit_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($xray_credit=mysqli_fetch_array($xray_credit_qry))
					{
						$xray_credit_amount+=$xray_credit["balance"];
						
						// Check Same Day Balance Receive
						$xray_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$xray_credit[patient_id]' AND `opd_id`='$xray_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
						$xray_sameday_balance_amount=$xray_sameday_balance["tot"];
						
						$xray_credit_amount-=$xray_sameday_balance_amount;
					}
					
					$xray_total_amount+=$xray_credit_amount;
					$xray_credit_amount_week+=$xray_credit_amount;
					$each_day_credit+=$xray_credit_amount;
					$each_day_credit_week+=$xray_credit_amount;
				
				$each_day_total+=$xray_total_amount;
				$each_day_total_week+=$xray_total_amount;
				$xray_total_amount_week+=$xray_total_amount;
				
				// ECG
				$pat_visit_type=12;
				$ecg_total_amount=0;
					
					// Cash
					$ecg_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
					$ecg_cash_amount=$ecg_cash["tot"];
					
					$ecg_total_amount+=$ecg_cash_amount;
					$ecg_cash_amount_week+=$ecg_cash_amount;
					$each_day_cash+=$ecg_cash_amount;
					$each_day_cash_week+=$ecg_cash_amount;
				
					// Card
					$ecg_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
					$ecg_card_amount=$ecg_card["tot"];
					
					$ecg_total_amount+=$ecg_card_amount;
					$ecg_card_amount_week+=$ecg_card_amount;
					$each_day_card+=$ecg_card_amount;
					$each_day_card_week+=$ecg_card_amount;
					
					// Credit
					$ecg_credit_amount=0;
					$ecg_credit_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($ecg_credit=mysqli_fetch_array($ecg_credit_qry))
					{
						$ecg_credit_amount+=$ecg_credit["balance"];
						
						// Check Same Day Balance Receive
						$ecg_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$ecg_credit[patient_id]' AND `opd_id`='$ecg_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
						$ecg_sameday_balance_amount=$ecg_sameday_balance["tot"];
						
						$ecg_credit_amount-=$ecg_sameday_balance_amount;
					}
					
					$ecg_total_amount+=$ecg_credit_amount;
					$ecg_credit_amount_week+=$ecg_credit_amount;
					$each_day_credit+=$ecg_credit_amount;
					$each_day_credit_week+=$ecg_credit_amount;
				
				$each_day_total+=$ecg_total_amount;
				$each_day_total_week+=$ecg_total_amount;
				$ecg_total_amount_week+=$ecg_total_amount;
				
				// Endoscopy
				$pat_visit_type=13;
				$endo_total_amount=0;
					
					// Cash
					$endo_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
					$endo_cash_amount=$endo_cash["tot"];
					
					$endo_total_amount+=$endo_cash_amount;
					$endo_cash_amount_week+=$endo_cash_amount;
					$each_day_cash+=$endo_cash_amount;
					$each_day_cash_week+=$endo_cash_amount;
				
					// Card
					$endo_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
					$endo_card_amount=$endo_card["tot"];
					
					$endo_total_amount+=$endo_card_amount;
					$endo_card_amount_week+=$endo_card_amount;
					$each_day_card+=$endo_card_amount;
					$each_day_card_week+=$endo_card_amount;
					
					// Credit
					$endo_credit_amount=0;
					$endo_credit_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($endo_credit=mysqli_fetch_array($endo_credit_qry))
					{
						$endo_credit_amount+=$endo_credit["balance"];
						
						// Check Same Day Balance Receive
						$endo_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$endo_credit[patient_id]' AND `opd_id`='$endo_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
						$endo_sameday_balance_amount=$endo_sameday_balance["tot"];
						
						$endo_credit_amount-=$endo_sameday_balance_amount;
					}
					
					$endo_total_amount+=$endo_credit_amount;
					$endo_credit_amount_week+=$endo_credit_amount;
					$each_day_credit+=$endo_credit_amount;
					$each_day_credit_week+=$endo_credit_amount;
				
				$each_day_total+=$endo_total_amount;
				$each_day_total_week+=$endo_total_amount;
				$endo_total_amount_week+=$endo_total_amount;
				
				// Dialysis
				$pat_visit_type=7;
				$dialysis_total_amount=0;
					// Cash
					$dialysis_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$dialysis_cash_amount=$dialysis_cash["tot"];
					
					$dialysis_total_amount+=$dialysis_cash_amount;
					$dialysis_cash_amount_week+=$dialysis_cash_amount;
					$each_day_cash+=$dialysis_cash_amount;
					$each_day_cash_week+=$dialysis_cash_amount;
					
					// Card
					$dialysis_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$dialysis_card_amount=$dialysis_card["tot"];
					
					$dialysis_total_amount+=$dialysis_card_amount;
					$dialysis_card_amount_week+=$dialysis_card_amount;
					$each_day_card+=$dialysis_card_amount;
					$each_day_card_week+=$dialysis_card_amount;
					
					// Credit
					$dialysis_credit_amount=0;
					$dialysis_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($dialysis_credit=mysqli_fetch_array($dialysis_credit_qry))
					{
						$dialysis_credit_amount+=$dialysis_credit["balance"];
						
						// Check Same Day Balance Receive
						$dialysis_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$dialysis_credit[patient_id]' AND `ipd_id`='$dialysis_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
						$dialysis_sameday_balance_amount=$dialysis_sameday_balance["tot"];
						
						$dialysis_credit_amount-=$dialysis_sameday_balance_amount;
					}
					$dialysis_total_amount+=$dialysis_credit_amount;
					$dialysis_credit_amount_week+=$dialysis_credit_amount;
					$each_day_credit+=$dialysis_credit_amount;
					$each_day_credit_week+=$dialysis_credit_amount;
					
				$each_day_total+=$dialysis_total_amount;
				$each_day_total_week+=$dialysis_total_amount;
				$dialysis_total_amount_week+=$dialysis_total_amount;
				
				// Emergency Room
				$pat_visit_type=4;
				$emergency_total_amount=0;
					// Cash
					$emergency_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$emergency_cash_amount=$emergency_cash["tot"];
					
					$emergency_total_amount+=$emergency_cash_amount;
					$emergency_cash_amount_week+=$emergency_cash_amount;
					$each_day_cash+=$emergency_cash_amount;
					$each_day_cash_week+=$emergency_cash_amount;
					
					// Card
					$emergency_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$emergency_card_amount=$emergency_card["tot"];
					
					$emergency_total_amount+=$emergency_card_amount;
					$emergency_card_amount_week+=$emergency_card_amount;
					$each_day_card+=$emergency_card_amount;
					$each_day_card_week+=$emergency_card_amount;
					
					// Credit
					$emergency_credit_amount=0;
					$emergency_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($emergency_credit=mysqli_fetch_array($emergency_credit_qry))
					{
						$emergency_credit_amount+=$emergency_credit["balance"];
						
						// Check Same Day Balance Receive
						$emergency_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$emergency_credit[patient_id]' AND `ipd_id`='$emergency_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
						$emergency_sameday_balance_amount=$emergency_sameday_balance["tot"];
						
						$emergency_credit_amount-=$emergency_sameday_balance_amount;
					}
					$emergency_total_amount+=$emergency_credit_amount;
					$emergency_credit_amount_week+=$emergency_credit_amount;
					$each_day_credit+=$emergency_credit_amount;
					$each_day_credit_week+=$emergency_credit_amount;
					
				$each_day_total+=$emergency_total_amount;
				$each_day_total_week+=$emergency_total_amount;
				$emergency_total_amount_week+=$emergency_total_amount;
				
				// Dental Procedure
				$pat_visit_type=6;
				$denpro_total_amount=0;
					// Cash
					$denpro_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$denpro_cash_amount=$denpro_cash["tot"];
					
					$denpro_total_amount+=$denpro_cash_amount;
					$denpro_cash_amount_week+=$denpro_cash_amount;
					$each_day_cash+=$denpro_cash_amount;
					$each_day_cash_week+=$denpro_cash_amount;
					
					// Card
					$denpro_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$denpro_card_amount=$denpro_card["tot"];
					
					$denpro_total_amount+=$denpro_card_amount;
					$denpro_card_amount_week+=$denpro_card_amount;
					$each_day_card+=$denpro_card_amount;
					$each_day_card_week+=$denpro_card_amount;
					
					// Credit
					$denpro_credit_amount=0;
					$denpro_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($denpro_credit=mysqli_fetch_array($denpro_credit_qry))
					{
						$denpro_credit_amount+=$denpro_credit["balance"];
						
						// Check Same Day Balance Receive
						$denpro_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$denpro_credit[patient_id]' AND `ipd_id`='$denpro_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
						$denpro_sameday_balance_amount=$denpro_sameday_balance["tot"];
						
						$denpro_credit_amount-=$denpro_sameday_balance_amount;
					}
					$denpro_total_amount+=$denpro_credit_amount;
					$denpro_credit_amount_week+=$denpro_credit_amount;
					$each_day_credit+=$denpro_credit_amount;
					$each_day_credit_week+=$denpro_credit_amount;
					
				$each_day_total+=$denpro_total_amount;
				$each_day_total_week+=$denpro_total_amount;
				$denpro_total_amount_week+=$denpro_total_amount;
				
				// MISCELLANEOUS
				$pat_visit_type=9;
				$misc_total_amount=0;
					// Cash
					$misc_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$misc_cash_amount=$misc_cash["tot"];
					
					$misc_total_amount+=$misc_cash_amount;
					$misc_cash_amount_week+=$misc_cash_amount;
					$each_day_cash+=$misc_cash_amount;
					$each_day_cash_week+=$misc_cash_amount;
					
					// Card
					$misc_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$misc_card_amount=$misc_card["tot"];
					
					$misc_total_amount+=$misc_card_amount;
					$misc_card_amount_week+=$misc_card_amount;
					$each_day_card+=$misc_card_amount;
					$each_day_card_week+=$misc_card_amount;
					
					// Credit
					$misc_credit_amount=0;
					$misc_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($misc_credit=mysqli_fetch_array($misc_credit_qry))
					{
						$misc_credit_amount+=$misc_credit["balance"];
						
						// Check Same Day Balance Receive
						$misc_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$misc_credit[patient_id]' AND `ipd_id`='$misc_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
						$misc_sameday_balance_amount=$misc_sameday_balance["tot"];
						
						$misc_credit_amount-=$misc_sameday_balance_amount;
					}
					$misc_total_amount+=$misc_credit_amount;
					$misc_credit_amount_week+=$misc_credit_amount;
					$each_day_credit+=$misc_credit_amount;
					$each_day_credit_week+=$misc_credit_amount;
					
				$each_day_total+=$misc_total_amount;
				$each_day_total_week+=$misc_total_amount;
				$misc_total_amount_week+=$misc_total_amount;
				
				// AMBULANCE
				$pat_visit_type=14;
				$ambulance_total_amount=0;
					// Cash
					$ambulance_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$ambulance_cash_amount=$ambulance_cash["tot"];
					
					$ambulance_total_amount+=$ambulance_cash_amount;
					$ambulance_cash_amount_week+=$ambulance_cash_amount;
					$each_day_cash+=$ambulance_cash_amount;
					$each_day_cash_week+=$ambulance_cash_amount;
					
					// Card
					$ambulance_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$ambulance_card_amount=$ambulance_card["tot"];
					
					$ambulance_total_amount+=$ambulance_card_amount;
					$ambulance_card_amount_week+=$ambulance_card_amount;
					$each_day_card+=$ambulance_card_amount;
					$each_day_card_week+=$ambulance_card_amount;
					
					// Credit
					$ambulance_credit_amount=0;
					$ambulance_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($ambulance_credit=mysqli_fetch_array($ambulance_credit_qry))
					{
						$ambulance_credit_amount+=$ambulance_credit["balance"];
						
						// Check Same Day Balance Receive
						$ambulance_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$ambulance_credit[patient_id]' AND `ipd_id`='$ambulance_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
						$ambulance_sameday_balance_amount=$ambulance_sameday_balance["tot"];
						
						$ambulance_credit_amount-=$ambulance_sameday_balance_amount;
					}
					$ambulance_total_amount+=$ambulance_credit_amount;
					$ambulance_credit_amount_week+=$ambulance_credit_amount;
					$each_day_credit+=$ambulance_credit_amount;
					$each_day_credit_week+=$ambulance_credit_amount;
					
				$each_day_total+=$ambulance_total_amount;
				$each_day_total_week+=$ambulance_total_amount;
				$ambulance_total_amount_week+=$ambulance_total_amount;
				
				// OTHER PROCEDURE
				$pat_visit_type=15;
				$otherpro_total_amount=0;
					// Cash
					$otherpro_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$otherpro_cash_amount=$otherpro_cash["tot"];
					
					$otherpro_total_amount+=$otherpro_cash_amount;
					$otherpro_cash_amount_week+=$otherpro_cash_amount;
					$each_day_cash+=$otherpro_cash_amount;
					$each_day_cash_week+=$otherpro_cash_amount;
					
					// Card
					$otherpro_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
					$otherpro_card_amount=$otherpro_card["tot"];
					
					$otherpro_total_amount+=$otherpro_card_amount;
					$otherpro_card_amount_week+=$otherpro_card_amount;
					$each_day_card+=$otherpro_card_amount;
					$each_day_card_week+=$otherpro_card_amount;
					
					// Credit
					$otherpro_credit_amount=0;
					$otherpro_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
					while($otherpro_credit=mysqli_fetch_array($otherpro_credit_qry))
					{
						$otherpro_credit_amount+=$otherpro_credit["balance"];
						
						// Check Same Day Balance Receive
						$otherpro_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$otherpro_credit[patient_id]' AND `ipd_id`='$otherpro_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
						$otherpro_sameday_balance_amount=$otherpro_sameday_balance["tot"];
						
						$otherpro_credit_amount-=$otherpro_sameday_balance_amount;
					}
					$otherpro_total_amount+=$otherpro_credit_amount;
					$otherpro_credit_amount_week+=$otherpro_credit_amount;
					$each_day_credit+=$otherpro_credit_amount;
					$each_day_credit_week+=$otherpro_credit_amount;
					
				$each_day_total+=$otherpro_total_amount;
				$each_day_total_week+=$otherpro_total_amount;
				$otherpro_total_amount_week+=$otherpro_total_amount;
				
	?>
				<tr>
					<td><?php echo date("d/M", strtotime($date)); ?></td>
					<td><?php echo $day; ?></td>
					<td><?php echo money_view($opd_credit_amount); ?></td>
					<td><?php echo money_view($opd_cash_amount); ?></td>
					<td><?php echo money_view($opd_card_amount); ?></td>
					<th><?php echo money_view($opd_total_amount); ?></th>
					<td><?php echo money_view($emergency_credit_amount); ?></td>
					<td><?php echo money_view($emergency_cash_amount); ?></td>
					<td><?php echo money_view($emergency_card_amount); ?></td>
					<th><?php echo money_view($emergency_total_amount); ?></th>
					<td><?php echo money_view($daycare_credit_amount); ?></td>
					<td><?php echo money_view($daycare_cash_amount); ?></td>
					<td><?php echo money_view($daycare_card_amount); ?></td>
					<th><?php echo money_view($daycare_total_amount); ?></th>
					<td><?php echo money_view($ipd_credit_amount); ?></td>
					<td><?php echo money_view($ipd_cash_amount); ?></td>
					<td><?php echo money_view($ipd_card_amount); ?></td>
					<th><?php echo money_view($ipd_total_amount); ?></th>
					<td><?php echo money_view(0); ?></td>
					<td><?php echo money_view(0); ?></td>
					<td><?php echo money_view(0); ?></td>
					<th><?php echo money_view(0); ?></th>
					<td><?php echo money_view($lab_credit_amount); ?></td>
					<td><?php echo money_view($lab_cash_amount); ?></td>
					<td><?php echo money_view($lab_card_amount); ?></td>
					<th><?php echo money_view($lab_total_amount); ?></th>
					<td><?php echo money_view($usg_credit_amount); ?></td>
					<td><?php echo money_view($usg_cash_amount); ?></td>
					<td><?php echo money_view($usg_card_amount); ?></td>
					<th><?php echo money_view($usg_total_amount); ?></th>
					<td><?php echo money_view($xray_credit_amount); ?></td>
					<td><?php echo money_view($xray_cash_amount); ?></td>
					<td><?php echo money_view($xray_card_amount); ?></td>
					<th><?php echo money_view($xray_total_amount); ?></th>
					<td><?php echo money_view($endo_credit_amount); ?></td>
					<td><?php echo money_view($endo_cash_amount); ?></td>
					<td><?php echo money_view($endo_card_amount); ?></td>
					<th><?php echo money_view($endo_total_amount); ?></th>
					<td><?php echo money_view($ecg_credit_amount); ?></td>
					<td><?php echo money_view($ecg_cash_amount); ?></td>
					<td><?php echo money_view($ecg_card_amount); ?></td>
					<th><?php echo money_view($ecg_total_amount); ?></th>
					<td><?php echo money_view($dialysis_credit_amount); ?></td>
					<td><?php echo money_view($dialysis_cash_amount); ?></td>
					<td><?php echo money_view($dialysis_card_amount); ?></td>
					<th><?php echo money_view($dialysis_total_amount); ?></th>
					<td><?php echo money_view($ambulance_credit_amount); ?></td>
					<td><?php echo money_view($ambulance_cash_amount); ?></td>
					<td><?php echo money_view($ambulance_card_amount); ?></td>
					<th><?php echo money_view($ambulance_total_amount); ?></th>
					<td><?php echo money_view($denpro_credit_amount); ?></td>
					<td><?php echo money_view($denpro_cash_amount); ?></td>
					<td><?php echo money_view($denpro_card_amount); ?></td>
					<th><?php echo money_view($denpro_total_amount); ?></th>
					<td><?php echo money_view($otherpro_credit_amount); ?></td>
					<td><?php echo money_view($otherpro_cash_amount); ?></td>
					<td><?php echo money_view($otherpro_card_amount); ?></td>
					<th><?php echo money_view($otherpro_total_amount); ?></th>
					<td><?php echo money_view($misc_credit_amount); ?></td>
					<td><?php echo money_view($misc_cash_amount); ?></td>
					<td><?php echo money_view($misc_card_amount); ?></td>
					<th><?php echo money_view($misc_total_amount); ?></th>
					<td><?php echo money_view($each_day_credit); ?></td>
					<td><?php echo money_view($each_day_cash); ?></td>
					<td><?php echo money_view($each_day_card); ?></td>
					<th><?php echo money_view($each_day_total); ?></th>
				</tr>
	<?php
				if($day=="Sun" || $last_day_month==$date)
				{
	?>
				<tr>
					<th>Total</th>
					<th></th>
					<td><?php echo money_view($opd_credit_amount_week); ?></td>
					<td><?php echo money_view($opd_cash_amount_week); ?></td>
					<td><?php echo money_view($opd_card_amount_week); ?></td>
					<th><?php echo money_view($opd_total_amount_week); ?></th>
					<td><?php echo money_view($emergency_credit_amount_week); ?></td>
					<td><?php echo money_view($emergency_cash_amount_week); ?></td>
					<td><?php echo money_view($emergency_card_amount_week); ?></td>
					<th><?php echo money_view($emergency_total_amount_week); ?></th>
					<td><?php echo money_view($daycare_credit_amount_week); ?></td>
					<td><?php echo money_view($daycare_cash_amount_week); ?></td>
					<td><?php echo money_view($daycare_card_amount_week); ?></td>
					<th><?php echo money_view($daycare_total_amount_week); ?></th>
					<td><?php echo money_view($ipd_credit_amount_week); ?></td>
					<td><?php echo money_view($ipd_cash_amount_week); ?></td>
					<td><?php echo money_view($ipd_card_amount_week); ?></td>
					<th><?php echo money_view($ipd_total_amount_week); ?></th>
					<th><?php echo money_view(0); ?></th>
					<th><?php echo money_view(0); ?></th>
					<th><?php echo money_view(0); ?></th>
					<th><?php echo money_view(0); ?></th>
					<td><?php echo money_view($lab_credit_amount_week); ?></td>
					<td><?php echo money_view($lab_cash_amount_week); ?></td>
					<td><?php echo money_view($lab_card_amount_week); ?></td>
					<th><?php echo money_view($lab_total_amount_week); ?></th>
					<td><?php echo money_view($usg_credit_amount_week); ?></td>
					<td><?php echo money_view($usg_cash_amount_week); ?></td>
					<td><?php echo money_view($usg_card_amount_week); ?></td>
					<th><?php echo money_view($usg_total_amount_week); ?></th>
					<td><?php echo money_view($xray_credit_amount_week); ?></td>
					<td><?php echo money_view($xray_cash_amount_week); ?></td>
					<td><?php echo money_view($xray_card_amount_week); ?></td>
					<th><?php echo money_view($xray_total_amount_week); ?></th>
					<td><?php echo money_view($endo_credit_amount_week); ?></td>
					<td><?php echo money_view($endo_cash_amount_week); ?></td>
					<td><?php echo money_view($endo_card_amount_week); ?></td>
					<th><?php echo money_view($endo_total_amount_week); ?></th>
					<td><?php echo money_view($ecg_credit_amount_week); ?></td>
					<td><?php echo money_view($ecg_cash_amount_week); ?></td>
					<td><?php echo money_view($ecg_card_amount_week); ?></td>
					<th><?php echo money_view($ecg_total_amount_week); ?></th>
					<td><?php echo money_view($dialysis_credit_amount_week); ?></td>
					<td><?php echo money_view($dialysis_cash_amount_week); ?></td>
					<td><?php echo money_view($dialysis_card_amount_week); ?></td>
					<th><?php echo money_view($dialysis_total_amount_week); ?></th>
					<td><?php echo money_view($ambulance_credit_amount_week); ?></td>
					<td><?php echo money_view($ambulance_cash_amount_week); ?></td>
					<td><?php echo money_view($ambulance_card_amount_week); ?></td>
					<th><?php echo money_view($ambulance_total_amount_week); ?></th>
					<td><?php echo money_view($denpro_credit_amount_week); ?></td>
					<td><?php echo money_view($denpro_cash_amount_week); ?></td>
					<td><?php echo money_view($denpro_card_amount_week); ?></td>
					<th><?php echo money_view($denpro_total_amount_week); ?></th>
					<td><?php echo money_view($otherpro_credit_amount_week); ?></td>
					<td><?php echo money_view($otherpro_cash_amount_week); ?></td>
					<td><?php echo money_view($otherpro_card_amount_week); ?></td>
					<th><?php echo money_view($otherpro_total_amount_week); ?></th>
					<td><?php echo money_view($misc_credit_amount_week); ?></td>
					<td><?php echo money_view($misc_cash_amount_week); ?></td>
					<td><?php echo money_view($misc_card_amount_week); ?></td>
					<th><?php echo money_view($misc_total_amount_week); ?></th>
					<td><?php echo money_view($each_day_credit_week); ?></td>
					<td><?php echo money_view($each_day_cash_week); ?></td>
					<td><?php echo money_view($each_day_card_week); ?></td>
					<th><?php echo money_view($each_day_total_week); ?></th>
				</tr>
	<?php
					$opd_cash_amount_week=$opd_card_amount_week=$opd_credit_amount_week=$opd_total_amount_week=0;
					$daycare_cash_amount_week=$daycare_card_amount_week=$daycare_credit_amount_week=$daycare_total_amount_week=0;
					$ipd_cash_amount_week=$ipd_card_amount_week=$ipd_credit_amount_week=$ipd_total_amount_week=0;
					$lab_cash_amount_week=$lab_card_amount_week=$lab_credit_amount_week=$lab_total_amount_week=0;
					$usg_cash_amount_week=$usg_card_amount_week=$usg_credit_amount_week=$usg_total_amount_week=0;
					$xray_cash_amount_week=$xray_card_amount_week=$xray_credit_amount_week=$xray_total_amount_week=0;
					$ecg_cash_amount_week=$ecg_card_amount_week=$ecg_credit_amount_week=$ecg_total_amount_week=0;
					$endo_cash_amount_week=$endo_card_amount_week=$endo_credit_amount_week=$endo_total_amount_week=0;
					$dialysis_cash_amount_week=$dialysis_card_amount_week=$dialysis_credit_amount_week=$dialysis_total_amount_week=0;
					$emergency__cash_amount_week=$emergency__card_amount_week=$emergency__credit_amount_week=$emergency__total_amount_week=0;
					$denpro_cash_amount_week=$denpro_card_amount_week=$denpro_credit_amount_week=$denpro_total_amount_week=0;
					$misc_cash_amount_week=$misc_card_amount_week=$misc_credit_amount_week=$misc_total_amount_week=0;
					$ambulance_cash_amount_week=$ambulance_card_amount_week=$ambulance_credit_amount_week=$ambulance_total_amount_week=0;
					$otherpro_cash_amount_week=$otherpro_card_amount_week=$otherpro_credit_amount_week=$otherpro_total_amount_week=0;
					$each_day_credit_week=$each_day_cash_week=$each_day_card_week=$each_day_total_week=0;
				}
				$i++;
			}
		}
?>
	</table>
<?php
}
?>
