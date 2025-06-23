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
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$date11=$_GET['date1'];
	$date22=$_GET['date2'];
	$con_cod_id=$_GET['con_cod_id'];
	
	$opd_first=$opd_revisit=0;
	$lab_first=$lab_revisit=0;
	$ipd_first=$ipd_revisit=0;
	$casualty_first=$casualty_revisit=0;
	
	$qry="SELECT * FROM `uhid_and_opdid` WHERE `patient_id`>0 and `date` between '$date1' and '$date2' ";
	
	if($con_cod_id>0)
	{
		$qry.=" AND `opd_id` in ( SELECT `opd_id` FROM `appointment_book` WHERE `consultantdoctorid`='$con_cod_id') ";
		$doc_qry=" AND b.`consultantdoctorid`='$con_cod_id'";
	}
	
	$qry.=" ORDER BY `slno` ASC";
	
	$pat_reg_qry=mysqli_query($link, $qry );
	while($pat_reg=mysqli_fetch_array($pat_reg_qry))
	{
		$visit_type_check="";
		$visit_type_check=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$pat_reg[patient_id]' AND `slno`<$pat_reg[slno] "));
		//~ if($pat_reg["type"]=='1')
		//~ {
			//~ if($visit_type_check)
			//~ {
				//~ $opd_revisit=$opd_revisit+1;
			//~ }else
			//~ {
				//~ $opd_first=$opd_first+1;
			//~ }
		//~ }
		if($pat_reg["type"]=='2')
		{
			if($visit_type_check)
			{
				$lab_revisit=$lab_revisit+1;
			}else
			{
				$lab_first=$lab_first+1;
			}
		}
		if($pat_reg["type"]=='3')
		{
			if($visit_type_check)
			{
				$ipd_revisit=$ipd_revisit+1;
			}else
			{
				$ipd_first=$ipd_first+1;
			}
		}
		if($pat_reg["type"]=='4')
		{
			if($visit_type_check)
			{
				$casualty_revisit=$casualty_revisit+1;
			}else
			{
				$casualty_first=$casualty_first+1;
			}
		}
	}

// Close account
$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$date2' "));	
if($check_close_account_today)
{
	$con_max_slno_less=$check_close_account_today['con_slno'];
	$con_max_slno_str_less=" AND `slno`<=$con_max_slno_less ";
	
	$con_max_slno_str_less_doc=" AND a.`slno`<=$con_max_slno_less ";
	
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
$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$last_date' "));	
if($check_close_account_today)
{
	$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));
	
	$con_max_slno_grtr=$check_close_account_today['con_slno'];
	$con_max_slno_str_grtr=" AND `slno`>$con_max_slno_grtr ";
	
	$con_max_slno_str_grtr_doc=" AND a.`slno`>$con_max_slno_grtr ";
	
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
?>
<html>
<head>
	<title>Patient Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Patient Report</h4>
			<b>From <?php echo convert_date($date11); ?> to <?php echo convert_date($date22); ?></b>
		</center>
		<br>
		<table class="table table-condensed" style="font-size:12px;">
			<tr>
				<th colspan="5">OPD Patient</th>
			</tr>
			<!--<tr>
				<td style="width:25%;"></td>
				<th style="width:10%;">First Visit</th>
				<td style="width:25%;"><?php echo $opd_first; ?></td>
				<th rowspan="2" style="width:25%;">Total</th>
				<td rowspan="2"><?php echo ($opd_first+$opd_revisit); ?></td>
			</tr>
			<tr>
				<td style="width:25%;"></td>
				<th>Re-Visit</th>
				<td><?php echo $opd_revisit; ?></td>
			</tr>-->
	<?php
		$tot_nv=$tot_rv=0;
		
		$appoint_book_qry=mysqli_query($link, " SELECT DISTINCT(b.`consultantdoctorid`) FROM `consult_payment_detail` a, `appointment_book` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` BETWEEN '$date1' AND '$date2' $con_max_slno_str_less_doc $con_max_slno_str_grtr_doc $doc_qry ORDER BY a.`slno` ");
		while($appoint_book=mysqli_fetch_array($appoint_book_qry))
		{
			$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appoint_book[consultantdoctorid]' "));
			
			$i=1;
				
			unset($opd_pin_doc); // $opd_pin_doc is gone
			$opd_pin_doc = array(); // $opd_pin_doc is here again
			
			$con_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`opd_id`) FROM `consult_payment_detail` a, `appointment_book` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`consultantdoctorid`='$appoint_book[consultantdoctorid]' AND a.`date` BETWEEN '$date1' AND '$date2' $con_max_slno_str_less_doc $con_max_slno_str_grtr_doc ORDER BY a.`slno` ");
			while($con_pay=mysqli_fetch_array($con_pay_qry))
			{
				$opd_pin_doc[$i]=$con_pay["opd_id"];
				
				$i++;
			}
			//print_r($opd_pin_doc);
			$nv=$rv=$tv=0;
			foreach($opd_pin_doc as $opd_pin)
			{
				if($opd_pin)
				{
					$opd_uhid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$opd_pin' "));
					
					$opd_visit_type_check=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$opd_uhid[patient_id]' AND `slno`<$opd_uhid[slno] "));
					if($opd_visit_type_check)
					{
						// Re-visit
						$rv++;
						
					}else
					{
						// First Visit
						$nv++;
					}
					$tv=$nv+$rv;
				}
			}
			$tot_rv+=$rv;
			$tot_nv+=$nv;
			if($tv>0)
			{
			
	?>
			<tr>
				<th rowspan="2"><?php echo $con_doc["Name"]; ?></th>
				<td>New Visit</td>
				<td> <span><?php echo $nv; ?></span></td>
				<th rowspan="2" style="width:25%;">Total</th>
				<td rowspan="2"><?php echo $tv; ?></td>
			</tr>
			<tr>
				<td>Re-Visit</td>
				<td> <span><?php echo $rv; ?></span></td>
			</tr>
	<?php
			}
		}
	?>
			<tr>
				<th rowspan="2">Total</th>
				<td>New Visit</td>
				<td> <span><?php echo $tot_nv; ?></span></td>
				<th rowspan="2" style="width:25%;">Total</th>
				<td rowspan="2"><?php echo ($tot_nv+$tot_rv); ?></td>
			</tr>
			<tr>
				<td>Re-Visit</td>
				<td> <span><?php echo $tot_rv; ?></span></td>
			</tr>
			
			<tr>
				<th colspan="5">LAB Patient</th>
			</tr>
			<tr>
				<td style="width:25%;"></td>
				<td style="width:10%;">First Visit</td>
				<td style="width:25%;"><?php echo $lab_first; ?></td>
				<th rowspan="2" style="width:25%;">Total</th>
				<td rowspan="2"><?php echo ($lab_first+$lab_revisit); ?></td>
			</tr>
			<tr>
				<td style="width:25%;"></td>
				<td>Re-Visit</td>
				<td><?php echo $lab_revisit; ?></td>
			</tr>
			<tr>
				<th colspan="5">IPD Patient</th>
			</tr>
			<tr>
				<td style="width:25%;"></td>
				<td style="width:10%;">First Visit</td>
				<td style="width:25%;"><?php echo $ipd_first; ?></td>
				<th rowspan="2" style="width:25%;">Total</th>
				<td rowspan="2"><?php echo ($ipd_first+$ipd_revisit); ?></td>
			</tr>
			<tr>
				<td style="width:25%;"></td>
				<td>Re-Visit</td>
				<td><?php echo $ipd_revisit; ?></td>
			</tr>
			<tr>
				<th colspan="5">Casualty Patient</th>
			</tr>
			<tr>
				<td style="width:25%;"></td>
				<td style="width:10%;">First Visit</td>
				<td style="width:25%;"><?php echo $casualty_first; ?></td>
				<th rowspan="2" style="width:25%;">Total</th>
				<td rowspan="2"><?php echo ($casualty_first+$casualty_revisit); ?></td>
			</tr>
			<tr>
				<td style="width:25%;"></td>
				<td>Re-Visit</td>
				<td><?php echo $casualty_revisit; ?></td>
			</tr>
		</table>
	</div>
</body>
</html>
<script>//window.print();</script>
