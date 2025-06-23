<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$uhid=mysqli_real_escape_string($link, base64_decode($_GET['uhid']));
$ipd=$opd_id=mysqli_real_escape_string($link, base64_decode($_GET['ipd']));
$user=mysqli_real_escape_string($link, base64_decode($_GET['user']));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$pat_info=$pat=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

//if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$u=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$user'"));

$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));

$reg_date=$pat_reg["date"];

if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

$dis_dt=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));

$adv_paid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' and `pay_type`='Final' "));

$con_doc=mysqli_query($link,"SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `consultantdoctorid` FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `consultantdoctorid` NOT IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'))");

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));

$pat_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_info` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));

$delivery_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_delivery_det` WHERE `baby_uhid`='$uhid' "));

$mother_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$delivery_det[patient_id]' "));

$discharge_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_summary_nicu` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));

$consultants="";
$addr="";
if($addr)
{
	$addr.=", ".$pat_det['add_1'];
}
else
{
	$addr=$pat_det['add_1'];
}
if($addr)
{
	$addr.=", ".$pat_det['add_2'];
}
else
{
	$addr=$pat_det['add_2'];
}
if($addr)
{
	$addr=", ".$pat_det['city'];
}
else
{
	$addr=$pat_det['city'];
}
if($pat_det['state'])
{
	$st=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `state` WHERE `state_id`='$pat_det[state]'"));
	$addr.=", ".$st['name'];
}
if($pat_det['country'])
$addr.=", ".$pat_det['country'];
while($c=mysqli_fetch_array($con_doc))
{
	$consultants.=$c['Name'].", ";
}
function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
function currency($rs)
{
	setlocale(LC_MONETARY, 'en_IN');
				
	$amount = money_format('%!i', $rs);
	return $amount;
}
$row=0;
$grand_tot=0;
ob_start();
include "page_header.php";
$my_head=ob_get_clean();

$self_file = basename($_SERVER['PHP_SELF']); 
$no_of_lines = count(file($self_file));
//echo $no_of_lines;
//echo $lines = count(file($self_file)) - 1;
$lines=15;
?>
<html lang="en">
	<head>
		<title>Discharge Certificate</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	
	</head>
	<body  onafterprint="window.close();" onkeypress="close_window(event)">
		<div class="container-fluid">
			<div class="" style="border-bottom:1px solid">
				<?php
				include('page_header.php');
				?>
				
			</div>
			<div>
				<center>
					<span class="" style="font-size: 16px;font-weight: bold;">DISCHARGE SUMMARY</span>
					<div class="noprint "><input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()"></div>
				</center>
			</div>
			
			<div>
				<table class="table table-condensed table-bordered" style="font-size:12px;border-bottom: 1px solid #000;"id="d">
					<tr>
						<th>UHID</th><td><?php echo $pat['patient_id'];?></td>
						<th><?php echo $prefix_det["prefix"]; ?></th><td><?php echo $ipd;?></td>
						<th>Admission Date</th><td><?php echo convert_date_g($pat_reg['date'])." ".convert_time($pat_reg['time']);?></td>
					</tr>
					<tr>
						<th>Name</th><td><?php echo "BABY OF ".$mother_info['name'];?></td>
						<th>Age</th><td><?php echo $pat['age']." ".$pat['age_type'];?></td>
						<th>Discharge Date</th><td><?php if($dis_dt){ echo convert_date_g($dis_dt['date'])." ".convert_time($dis_dt['time']);}?></td>
					</tr>
					<tr>
						<th>Mother’s Age</th><td><?php echo $mother_info['name'];?></td>
						<th>Father’s Name</th><td><?php echo $delivery_det["father_name"];?></td>
						<th>Contact No.</th><td><?php echo $pat['phone']; ?></td>
					</tr>
					<tr>
						<th>Address</th><td colspan="3"><?php echo $addr;?></td>
						<th>Date of Birth</th><td><?php echo date("d-M-Y", strtotime($delivery_det['dob'])); ?></td>
					</tr>
					<tr>
						<th>Birth Time</th><td><?php echo date("H:i A", strtotime($delivery_det['born_time']));?></td>
						<th>Weight</th><td><?php echo $delivery_det["weight"];?> KG</td>
						<th>Sex</th><td><?php echo $pat['sex'];?></td>
					</tr>
				</table>
				<br>
				<table class="table table-condensed table-bordered" style="font-size:12px;">
					<tr>
						<th>Mode of delivery</th><td><?php echo $delivery_det['delivery_mode'];?></td>
						<th>Gestations</th><td colspan="3"><?php echo $discharge_det["gestations"];?> Weeks &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (AGA/SGA/CGA)</td>
					</tr>
					<tr>
						<th>Apgar Score</th>
						<td>
							<b style="border-right: 1px solid #ccc;padding-right: 30px;">1 Minute</b>
							<span><?php echo $discharge_det['apgar_score_1m'];?></span>
						</td>
						<th>5 Minutes</th>
						<td><?php echo $discharge_det['apgar_score_5m'];?></td>
						<th>10 Minutes</th>
						<td><?php echo $discharge_det['apgar_score_10m'];?></td>
					</tr>
					<tr>
						<th>ABO RH GROUPING</th>
						<td colspan="3">
							<b style="border-right: 1px solid #ccc;padding-right: 40px;">Mother</b>
							<span><?php echo $discharge_det['abo_mother'];?></span>
						</td>
						<th>Baby</th>
						<td><?php echo $discharge_det['abo_baby'];?></td>
					</tr>
					<tr>
						<th>Immunization Date BCG</th><td><?php echo $discharge_det['immunization_date_bcg'];?></td>
						<th>OPV (Birth Dose)</th><td><?php echo $discharge_det['opv_birth_dose'];?></td>
						<th>Hepatitis B</th><td><?php echo $discharge_det['hepatitis_b'];?></td>
					</tr>
					</tr>
					<tr>
						<th>Discharge Weight</th><td colspan="5"><?php echo $discharge_det["dicharge_weight"];?> KG</td>
					</tr>
				</table>
				
				<table class="table table-no-top-border">
				<?php
					$discharge_by=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN(SELECT `diagnosed_by` FROM `ipd_dischage_type` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd') "));
					
					if($discharge_by["consultantdoctorid"]==3)
					{
						$emp_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$discharge_by[emp_id]' "));
						if($emp_info["qualification"]!="")
						{
							$qualification="(".$emp_info["qualification"].")";
						}
				?>
					<tr class="consultant_tr">
						<th style="width: 15%;">Consultant Doctor</th>
						<th>: <?php echo $discharge_by['Name']." ".$qualification;?></th>
					</tr>
				<?php	
					}else
					{
						$con_doc_qry=mysqli_query($link," SELECT * FROM `ipd_pat_doc_transfer` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `status`=1 ORDER BY `slno` ASC ");
						$con_doc_num=mysqli_num_rows($con_doc_qry);
						if($con_doc_num==1)
						{
							$con_doc=mysqli_fetch_array($con_doc_qry);
							$doc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_doc[attend_doc]' "));
							
							$doc_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$doc[emp_id]' "));
					?>
						<tr class="consultant_tr">
							<th style="width: 15%;">Consultant Doctor</th>
							<th>: <?php echo $doc["Name"]." (".$doc_info["qualification"].")";?></th>
						</tr>
					<?php
						}else
						{
							//~ $xex=1;
							//~ while($con_doc=mysqli_fetch_array($con_doc_qry))
							//~ {
								//~ $doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_doc[attend_doc]' "));
								
								//~ $from_date_to_str="From ".convert_date_g($con_doc["date"])." ".convert_time($con_doc["time"]);
								
								//~ if($xex==1)
								//~ {
									//~ echo "<tr><th rowspan='$con_doc_num' style='width: 20%;'>Consultant Doctor</th><td>$doc[Name]</td><td>$from_date_to_str</td></tr>";
								//~ }else
								//~ {
									//~ echo "<tr><td>$doc[Name]</td><td>$from_date_to_str</td></tr>";
								//~ }
								
								//~ $xex++;
							//~ }
						}
						//~ $ot_doc_qry=mysqli_query($link," SELECT a.* FROM `ot_pat_service_details` a, `ot_type_master` b WHERE a.`resourse_id`=b.`type_id` AND  a.`patient_id`='$uhid' AND a.`ipd_id`='$ipd' AND b.`link`=1 ORDER BY b.`seq` ");
						//~ while($ot_doc=mysqli_fetch_array($ot_doc_qry))
						//~ {
							//~ if($ot_doc["resourse_id"]=="1357" && $ot_doc["emp_id"]!=0) // surgeon
							//~ {
								//~ $doc_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$ot_doc[emp_id]' "));
								//~ echo "<tr><th>Surgeon</th><th>: $doc_info[name] ($doc_info[qualification])</th></tr>";
							//~ }
							//~ if($ot_doc["resourse_id"]=="1358" && $ot_doc["emp_id"]!=0) // Asst surgeon
							//~ {
								//~ $doc_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$ot_doc[emp_id]' "));
								//~ echo "<tr><th>Asst. Surgeon</th><th>: $doc_info[name] ($doc_info[qualification])</th></tr>";
							//~ }
							//~ if($ot_doc["resourse_id"]=="1359" && $ot_doc["emp_id"]!=0) // Anaesthesist
							//~ {
								//~ $doc_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$ot_doc[emp_id]' "));
								//~ echo "<tr><th>Anaesthesist</th><th>: $doc_info[name] ($doc_info[qualification])</th></tr>";
							//~ }
							//~ if($ot_doc["resourse_id"]=="1374" && $ot_doc["emp_id"]!=0) // Paediatrician
							//~ {
								//~ $doc_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$ot_doc[emp_id]' "));
								//~ echo "<tr><th>Paediatrician</th><th>: $doc_info[name] ($doc_info[qualification])</th></tr>";
							//~ }
						//~ }
					}
				?>
				
				</table>
			
			<?php
				
				$z=1;
				
				$arr=explode(" ",$discharge_det['treatment_in_hospital']);
				$hhhh="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$hhhh.=$ss." ";
					//if($ll==500)
					if($lines==30)
					{
						$hhhh.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				$brk=$ll;
				if($discharge_det['treatment_in_hospital'])
				{
				?>
				<!--<b>Case Summary</b>-->
				<?php
				//echo $ll;
					echo "<p align='justify'><b>Treatment in hospital :</b> <br>".$hhhh."</p>";
				}
				
				$arr=explode(" ",$discharge_det['case_summary']);
				$hhhh="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$hhhh.=$ss." ";
					//if($ll==500)
					if($lines==30)
					{
						$hhhh.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				$brk=$ll;
				if($discharge_det['case_summary'])
				{
				?>
				<?php
					echo "<p align='justify'><b>Case Summary :</b> <br>".$hhhh."</p>";
				}
				
				$arr=explode(" ",$discharge_det['course_in_hospital']);
				$hhhh="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$hhhh.=$ss." ";
					//if($ll==500)
					if($lines==30)
					{
						$hhhh.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				$brk=$ll;
				if($discharge_det['course_in_hospital'])
				{
				?>
				<?php
					echo "<p align='justify'><b>Course in hospital :</b> <br>".$hhhh."</p>";
				}
			
				
				$q=mysqli_query($link,"SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd' AND `type`='3'");
				$rows=mysqli_num_rows($q);
				if($rows>0)
				{
				?>
				<b>Medicine Prescribed</b>
				<table class="table table-condensed" style="font-size:12px;">
					<tr>
						<th>#</th>
						<th>Medicine Name</th>
						<th>Dosage / Instruction</th>
						<th>Quantity</th>
					</tr>
				<?php
				$j=1;
				$tdata="";
				//echo $ll;
				while($rr=mysqli_fetch_array($q))
				{
					$md=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$rr[item_code]'"));
					
					$tdata.="<tr><td>".$j."</td><td>".$md['item_name']."</td><td>".$rr['dosage']."</td><td>".$rr['quantity']."</td><td>".$ins."</td></tr>";
					
					if($lines==30)
					{
						$tdata.='</table><p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				$j++;
				}
				echo $tdata;
				//echo $ll;
				?>
				</table>
				<?php
				}
				$m_qry=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_final` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
				if(mysqli_num_rows($m_qry)>0)
				{
					$medi="";
					$mdc=mysqli_fetch_array($m_qry);
					$medicine=explode("\n",$mdc['medicine']);
					?>
					<table class="table table-condensed" style="font-size:12px;">
						<tr>
							<th>Medicine Prescribed / Description / Instruction</th>
						</tr>
						<?php
						foreach($medicine as $mm)
						{
							if($mm)
							{
								echo "<tr><td>".$mm."</td></tr>";
								$line++;
							}
						}
						?>
					</table>
					<?php
				}
				
				$drug_qry=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_post_discharge` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
				if(mysqli_num_rows($drug_qry)>0)
				{
				?>
				<table class="table table-condensed">
					<tr>
						<th>#</th><th>Medicine Prescribed</th><th>Instruction</th>
					</tr>
					<?php
					$pl=1;
					while($drg=mysqli_fetch_array($drug_qry))
					{
						$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$drg[item_code]'"));
					?>
					<tr>
						<td><?php echo $pl;?></td>
						<td><?php echo $itm['item_name'];?></td>
						<td><?php echo $drg['dosage'];?></td>
					</tr>
					<?php
					$line++;
					$pl++;
					}
					?>
				</table>
				<?php
				}
				
				$typ=mysqli_fetch_array(mysqli_query($link,"SELECT `type`,`diagnosed_by` FROM `ipd_dischage_type` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
				$dis_typ=mysqli_fetch_array(mysqli_query($link," SELECT `discharge_name` FROM `discharge_master` WHERE `discharge_id`='$typ[type]' "));
				if($dis_typ)
				{
					$tp="<b>Discharge Type</b> ".$dis_typ["discharge_name"]."<br/>";
				}else
				{
					$tp="";
				}
				
				$_SESSION['page_no']=$z;
				?>
				<?php echo $tp; ?>
				<?php
				
					$dr=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$typ[diagnosed_by]'"));
					$prepared=$dr['Name'];
				?>
				
				<hr/>
				<p>I have received the above instruction &amp; was given the opportunity to ask questions.</p>
				<br>
				<br>
				
				<!--<span class="text-left">Name &amp; Sign of Discharging Nurse:</span>-->
				<span class="text-right">Name &amp; Sign of patient relative</span>
				<span class="text-left">Treating Doctor Name &amp; Sign:</span>
				<br>
				<br>
			</div>
			
			<div class="row">
				<div class="span5" id="Frame4" dir="ltr" style="float: left;height: 2.01cm; border: none; padding: 0cm; background: #ffffff">
					<table dir="ltr" width="402" cellspacing="0" cellpadding="7">
						<colgroup><col width="71">
						<col width="301">
						</colgroup><tbody><tr>
							<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0cm; padding-bottom: 0cm; padding-left: 0.19cm; padding-right: 0cm" width="71">
								<p class="western" lang="en-US" align="center"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3"><b>Advice
								at discharge:</b></font></font></p>
							</td>
							<td style="border: 1px solid #000000; padding: 0cm 0.19cm" width="301" valign="top">
								<ul>
									<li>
										<p style="margin-bottom: 0cm" lang="en-US"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3"><i>Exclusive Breast feeding for 6 months</i></font></font></p>
									</li>
									<li>
										<p style="margin-bottom: 0cm" lang="en-US"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3"><i>Vaccination as per schedule</i></font></font></p>
									</li>
									<li>
										<p lang="en-US"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3"><i>Follow
									with a paediatrician</i></font></font></p>
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>		
				</div>
				<div class="span4" id="Frame3" dir="ltr" style="float: left; height: 3.5cm; border: none; padding: 0cm; background: #ffffff">
					<table dir="ltr" width="268" cellspacing="0" cellpadding="7">
						<colgroup><col width="148">
						<col width="89">
						</colgroup><tbody><tr>
							<td colspan="2" style="border: 1px solid #000000; padding: 0cm 0.19cm" width="252" valign="top">
								<p class="western" lang="en-US" align="center"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3"><u><b>Major
								Development Milestones</b></u></font></font></p>
							</td>
						</tr>
						<tr valign="top">
							<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0cm; padding-bottom: 0cm; padding-left: 0.19cm; padding-right: 0cm" width="148">
								<p class="western" lang="en-US" align="center"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3">Social
								Smile </font></font>
								</p>
							</td>
							<td style="border: 1px solid #000000; padding: 0cm 0.19cm" width="89">
								<p class="western" lang="en-US" align="center"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3"><b>2
								months</b></font></font></p>
							</td>
						</tr>
						<tr valign="top">
							<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0cm; padding-bottom: 0cm; padding-left: 0.19cm; padding-right: 0cm" width="148">
								<p class="western" lang="en-US" align="center"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3">Head
								Holding </font></font>
								</p>
							</td>
							<td style="border: 1px solid #000000; padding: 0cm 0.19cm" width="89">
								<p class="western" lang="en-US" align="center"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3"><b>4
								months</b></font></font></p>
							</td>
						</tr>
						<tr valign="top">
							<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0cm; padding-bottom: 0cm; padding-left: 0.19cm; padding-right: 0cm" width="148">
								<p class="western" lang="en-US" align="center"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3">Sitting
								with support</font></font></p>
							</td>
							<td style="border: 1px solid #000000; padding: 0cm 0.19cm" width="89">
								<p class="western" lang="en-US" align="center"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3"><b>8
								months</b></font></font></p>
							</td>
						</tr>
						<tr valign="top">
							<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0cm; padding-bottom: 0cm; padding-left: 0.19cm; padding-right: 0cm" width="148">
								<p class="western" lang="en-US" align="center"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3">Standing
								with Support</font></font></p>
							</td>
							<td style="border: 1px solid #000000; padding: 0cm 0.19cm" width="89">
								<p class="western" lang="en-US" align="center"><font face="Times New Roman, serif"><font style="font-size: 12pt" size="3"><b>12
								months</b></font></font></p>
							</td>
						</tr>
					</tbody>
					</table>
				</div>
			</div>
			
		</div>
		<footer class="text-center" style="font-size:12px;"><?php echo 'Page '.$z.' of '.$_SESSION["page_no"]; ?></footer>
	</body>
</html>
<script>
	//window.print();
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
				window.close();
		}
	}
</script>
<style>
.consultant_tr
{
	display:none;
}
p {
    margin: 0 0 3px;
}
.table {
    margin-bottom: 0px;
}
*
{
	font-size:12px;
}
@page{
	margin-left:0.5cm;
	margin-right:0.5cm;
}
  @media print{
 .noprint{
 display:none;
 }
 }
 .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
}
.table
{
	margin-bottom:5px;
	font-size:12px;
}
hr
{
	margin:10;
	border-bottom:0;
	border-top: 1px solid #000;
}

.page-break {page-break-after: always; padding-top: 5px;}
.req_slip{ min-height:520px;}
.f_req_slip{ min-height:670px;}
.rad_req_slip{ min-height:300px;}

*
{
	font-size:13px;
}

</style>
