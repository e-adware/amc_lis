<?php
include('../../includes/connection.php');

	// Date format convert
	function convert_date($date)
	{
		if($date)
		{
			$timestamp = strtotime($date); 
			$new_date = date('d-m-y', $timestamp);
			return $new_date;
		}
	}
	$rupees_symbol="&#x20b9; ";
	
$uhid=base64_decode($_GET['uhid']);
$baby=base64_decode($_GET['baby_id']);
$b=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' AND `baby_uhid`='$baby'"));
$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid`,`name`,`address` FROM `patient_info` WHERE `patient_id`='$uhid'"));

$bDate =$b['dob'] ;
$week6=date('Y-m-d', strtotime($bDate. ' + 42 days'));
$week10=date('Y-m-d', strtotime($bDate. ' + 70 days'));
$week14=date('Y-m-d', strtotime($bDate. ' + 98 days'));
$mnth6=date('Y-m-d', strtotime($bDate. ' + 180 days'));
$mnth9=date('Y-m-d', strtotime($bDate. ' + 270 days'));
$mnth12=date('Y-m-d', strtotime($bDate. ' + 365 days'));
$mnth15=date('Y-m-d', strtotime($bDate. ' + 450 days'));
$mnth16=date('Y-m-d', strtotime($bDate. ' + 480 days'));
$mnth18=date('Y-m-d', strtotime($bDate. ' + 540 days'));
$year2=date('Y-m-d', strtotime($bDate. ' + 730 days'));
$year4=date('Y-m-d', strtotime($bDate. ' + 1460 days'));
$year10=date('Y-m-d', strtotime($bDate. ' + 3650 days'));

?>
<html>
<head>
	<title>vaccine Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
</head>
<body>
	<div class="container-fluid">
		<div class="">
			<?php include('page_header.php');?>
		</div>
		<hr>
		<center>
			<b style="font-size:20px;"><u>Vaccination Schedule</u></b><br/>
			
		</center>
	
	
	<table>
		<tr>
			<td style="font-weight:bold;font-size:12px">&nbsp;</td>
		</tr>
		<tr>
			<td style="font-weight:bold;font-size:12px">Name of Baby : Baby Of <?php echo $pat['name'];?> </td>
		</tr>
		<tr>
			<td style="font-weight:bold;font-size:12px">Date of Birth : <?php echo convert_date($b['dob']);?> </td>
		</tr>
		
	</table>
	<table class="table table-condensed table-bordered" style="font-size:13px;">
		<tr>
			<th>#</th>
			<th>Age(completed weeks/months/years)</th>
			<th >Vaccines</th>
			<th style="text-align:right;">Doses</th>
			<th>Content Tag</th>
			<th>Due Date</th>
			<th >Date Administered</th>
		</tr>
		<?php
		$j=1;
		$tot_amt=0;
		$tot_pat=0;
		
			?>
			<tr>
				<td>1</td>
				<td>Birth</td>
				<td >Bacillus Calmette–Guérin (BCG)</br> Oral polio vaccine (OPV 0)</br>Hepatitis B (Hep – B1)</td>
				<td style="text-align:right;">1</br>1</br>1</td>
				<td >BCG</br>OPV</br>Hep -B</td>
				<td><?php echo convert_date($b['dob']);?></td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td>2</td>
				<td>6 weeks</td>
				<td >Diptheria, Tetanus and Pertussis vaccine (DTwP 1)</br> Inactivated polio vaccine (IPV 1)</br> Hepatitis B  (Hep – B2)</br>Haemophilus influenzae type B (Hib 1)</br>Rotavirus 1</br>Pneumococcal conjugate vaccine (PCV 1)</td>
				<td style="text-align:right;">1</br>1</br>1</br>1</br>1</br>1</td>
				<td >DTP</br>IPV</br>Hep -B</br>Hib</br>Rotavirus</br>PCV</td>
				<td><?php echo convert_date($week6);?></td>
				<td>&nbsp;</td>
				
			</tr>
			
			<tr>
				<td>3</td>
				<td>10 weeks</td>
				<td >Diptheria, Tetanus and Pertussis vaccine (DTwP 2)</br>Inactivated polio vaccine (IPV 2)</br>Haemophilus influenzae type B (Hib 2)</br>Rotavirus 2</br>Pneumococcal conjugate vaccine (PCV 2)</td>
				<td style="text-align:right;">1</br>1</br>1</br>1</br>1</td>
				<td >DTP</br>IPV</br>Hib</br>Rotavirus</br>PCV</td>
				<td><?php echo convert_date($week10);?></td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td>4</td>
				<td>14 weeks</td>
				<td >Diptheria, Tetanus and Pertussis vaccine (DTwP 3)</br>Inactivated polio vaccine (IPV 3)</br>Haemophilus influenzae type B (Hib 3)</br>Rotavirus 3 2</br>Pneumococcal conjugate vaccine (PCV 3)</td>
				<td style="text-align:right;">1</br>1</br>1</br>1</br>1</td>
				<td >DTP</br>IPV</br>Hib</br>Rotavirus</br>PCV</td>
				<td><?php echo convert_date($week14);?></td>
				<td>&nbsp;</td>
			</tr>
		   <tr>
				<td>5</td>
				<td>6 months</td>
				<td >Oral polio vaccine (OPV 1)</br>Hepatitis B (Hep – B3)</td>
				<td style="text-align:right;">1</br>1</td>
				<td >OPV</br>Hep -B</td>
				<td><?php echo convert_date($mnth6);?></td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td>6</td>
				<td>9 months</td>
				<td >Oral polio vaccine (OPV 2)</br>Measles, Mumps, and Rubella (MMR – 1)</td>
				<td style="text-align:right;">1</br>1</td>
				<td >OPV</br>MMR</td>
				<td><?php echo convert_date($mnth9);?></td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td>7</td>
				<td>9–12 months</td>
				<td >Typhoid Conjugate Vaccine</td>
				<td style="text-align:right;">1</td>
				<td >Typhoid Conjugate Vaccine</td>
				<td><?php echo convert_date($mnth9);?></td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td>8</td>
				<td>12 months</td>
				<td >Hepatitis A (Hep – A1)</td>
				<td style="text-align:right;">1</td>
				<td >Hep -A</td>
				<td><?php echo convert_date($mnth12);?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>9</td>
				<td>15 months</td>
				<td >Measles, Mumps, and Rubella (MMR 2)</br>Varicella 1</br>PCV booster</td>
				<td style="text-align:right;">1</br>1</br>1</td>
				<td >MMR</br>Varicella</br>PCV</td>
				<td><?php echo convert_date($mnth15);?></td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td>10</td>
				<td>16-18 months</td>
				<td >Diphtheria,Perussis,and Tetanus(DTwP B1/DTaP B1)</br>Inactivated polio vaccine (IPV B1)</br>Haemophilus influenzae type B (Hib B1)</td>
				<td style="text-align:right;">1</br></br>1</br>1</td>
				<td >DTP</br></br>IPV</br>Hib</td>
				<td><?php echo convert_date($mnth16);?></td>
				<td>&nbsp;</td>
			</tr>
		   
		   <tr>
				<td>11</td>
				<td>18 months</td>
				<td >Hepatitis A (Hep – A2)</td>
				<td style="text-align:right;">1</td>
				<td >Hep -A</br></td>
				<td><?php echo convert_date($mnth18);?></td>
				<td>&nbsp;</td>
			</tr>
		   <tr>
				<td>12</td>
				<td>2 Years</td>
				<td >Booster of Typhoid Conjugate Vaccine</td>
				<td style="text-align:right;">1</td>
				<td >Typhoid Conjugate Vaccine</td>
				<td><?php echo convert_date($year2);?></td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td>13</td>
				<td>4-6 Years</td>
				<td >Diphtheria,Perussis,and Tetanus(DTwP B2/DTaP B2)</br>Oral polio vaccine (OPV 3)</br>Varicella 2</br>Measles,Mumps,and Rubella(MMR 3)</td>
				<td style="text-align:right;">1</br></br>1</br>1</br>1</td>
				<td >DTP</br></br>OPV</br>Varicella</br>MMR</td>
				<td><?php echo convert_date($year4);?></td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td>14</td>
				<td>10-12 Years</td>
				<td >Tdap/Td</br>Human Papilloma Virus (HPV)</td>
				<td style="text-align:right;">1</br>1</td>
				<td >Tdap</br>HPV</td>
				<td><?php echo convert_date($year10);?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="7"><i>Reference: IAP Immunization Timetable 2016</i></td>
				
			</tr>
	</table>
	
	</div>
</body>
</html>
<script>//window.print();</script>
<style>
.table-condensed th, .table-condensed td
{
    padding: 2px;
}
</style>
