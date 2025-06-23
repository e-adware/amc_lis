<?php
ob_start();	


include("../../includes/connection.php");
require("../../includes/global.function.php");
include("pathology_normal_range_new.php");

$nb_text_patho=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='1' "));

$uhid=$_GET['uhid'];
$opd_id=$_GET['opd_id'];
$ipd_id=$_GET['ipd_id'];
$batch_no=$_GET['batch_no'];
$tests=$_GET['tests'];
$lab_doc_id=$_GET['doc'];

$doc_sign=$_GET[sel_doc];
$docc=explode(",",$doc_sign);
/*-----------Result Check for Printing--------*/
$print_tst="";
$ntst=explode("@",$tests);
foreach($ntst as $nt)
{
	$chk_res=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$nt'"));
	$chk_sum=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$nt'"));
	$chk_wid[tot]=0;
	if($nt==1227)
	{
		$chk_wid=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no'"));
	}
	if($chk_res[tot]>0 || $chk_sum[tot]>0 || $chk_wid[tot]>0)
	{
		$print_tst.="@".$nt;	
	}
}
/*---------------------------------------------*/
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
		<link href="../../css/report.css" rel="stylesheet" type="text/css">
		<link href="../../css/loader.css" rel="stylesheet" type="text/css">
		<script src="../../js/jquery.min.js"></script> 
		<script>
			
		</script>
		<style>
		*{ font-size:14px;}
		.page_no{ position:absolute;right:10%;bottom:30px;font-style:italic;}
		#t_bold td{ font-size:13px}
		.re_table td{ line-height:20px;}
		.re_par { padding-left:20px;}
		.re_sub { padding-left:60px;}
		#testname{ font-size:15px; font-weight:bold;}
		.meth_name{ font-style:italic;font-size:12px;}
		</style>
	</head>
	<body>

<?php

$user=$_GET['user'];
$uname=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$user'"));



$id_pref=mysqli_fetch_array(mysqli_query($link,"select * from id_setup limit 1"));





$test1=explode("@",$tests);

foreach($test1 as $tst)
{
	if($tst)
	{
		$doc_n=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));	
		$doc_pd=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));	
		$doc_wd=mysqli_fetch_array(mysqli_query($link,"select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' limit 1"));	
		
		$dc[]=$doc_n[doc];
		//if($doc_n[doc]>0){ $dc[]=$doc_n[doc]; }
		
		$dc[]=$doc_pd[doc];
		//if($doc_pd[doc]>0)	{$dc[]=$doc_pd[doc];}
		
		$dc[]=$doc_wd[doc];
		//if($doc_wd[doc]>0){	$dc[]=$doc_wd[doc];	}
	}
}

$dct=array_unique($dc);

$all_d=0;

//$cls="position:fixed";
$ath_dt="display:none";
if(sizeof($dct)>1)
{
	//$cls="position:absolute;bottom:-200px";
	$ath_dt="display:block;";
}
$cls="position:absolute;bottom:-125px";
$ath_dt="display:none";
$j=1;
foreach($dct as $d)
{
	if($d>-1)
	{
		
		if($all_d>0)
		{
			?>
				<div class="page_break"></div>
			<?php
		}
		
		$all_profile="";
		$all_test="";
		$all_cult="";
		$all_pad="";
		$all_ptst="";
		$all_re=0;
		$pos=0;
		$wid=0;
		$nbl_note=0;
		$gtt=0;
		
		
			foreach($test1 as $tst)
			{
				$tsts=mysqli_query($link,"select distinct testid from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and doc='$d' and testid in(select testid from testmaster where testid='$tst') order by testid");
				while($tt=mysqli_fetch_array($tsts))
				{
					
					if(in_array($tt[testid],$test1))
					{
						$tnm=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$tt[testid]'"));
						
						if (strpos($tnm['testname'],'culture') !== false) 
						{
							$pos=2;
						}
						elseif (strpos($tnm['testname'],'CULTURE') != false) 
						{
							$pos=2;
						}
						elseif (strpos($tnm['testname'],'Culture') != false) 
						{
							$pos=2;
						}
						
						elseif($tt[testid]==806 || $tt[testid]==94 || $tt[testid]==1064) //----Semen annnnnalysis same as urine------//
						//elseif($tt[testid]==806 || $tt[testid]==94)
						{
							$pos=5;
						}
						
						else
						{
							$pos=0;
							
							$t_sum=mysqli_num_rows(mysqli_query($link,"select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tt[testid]' and doc='$d' order by testid"));
							if($t_sum>0)
							{
								$chk_sm=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tt[testid]' and doc='0' order by testid"));
								if($chk_sm['summary']!="<p><br></p>")
								{
									$pos=3;								
								}
							}
							else
							{ 
								 $if_sum=mysqli_num_rows(mysqli_query($link, "select * from summary_check where testid='$tt[testid]'"));
								 if($if_sum==0)
								 {
									$ts_sum=mysqli_fetch_array(mysqli_query($link,"select * from test_summary where testid='$tt[testid]'"));
									if(strlen($ts_sum['summary'])>15)
									{
										$pos=3;
									}
								 }
							}
							 if($tt['testid']==515)
							 {
								 $pos=4;
							 }
						}
						
						if($pos==0)
						{
							$test_param_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `Testparameter` WHERE `TestId`='$tt[testid]' "));
							if($test_param_num>4)
							{
								$all_profile.="@".$tt['testid'];
							}else
							{
								$all_test.="@".$tt['testid'];
							}
							//$all_test.="@".$tt['testid'];
						}else if($pos==2)
						{
							$all_cult=$tt['testid'];	
						}
						else if($pos==3)
						{
							$all_ptst.="@".$tt['testid'];
						}
						else if($pos==4)
						{
							$gtt=1;
						}
						else if($pos==5)
						{
							$all_re.="@".$tt['testid'];
						}
						else
						{	
							$all_test.="@".$tt['testid'];
						}
						
					}
				}
			}
		
				
		
		$sum_test=mysqli_query($link,"select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and doc='$d' and testid not in(select testid from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no')");
		while($st=mysqli_fetch_array($sum_test))
		{
			if($st['testid'])
			{
				if(in_array($st['testid'],$test1))
				{
					$all_pad.="@".$st['testid'];			
				}
			}
		}
		
		$wd_chk=mysqli_num_rows(mysqli_query($link,"select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and doc='$d' limit 1"));
		if($wd_chk>0)
		{
			if(in_array(1227,$test1))
			{
				$wid=1;
			}
		}	
		
		
		
		//$reg=mysqli_fetch_array(mysqli_query($link, "select * from patient_reg_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' "));
		if($opd_id!="")
		{
			$v_text="OPD ID";
			$v_id=$opd_id;
			$reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
		}else if($ipd_id!="")
		{
			$v_text="IPD ID";
			$v_id=$ipd_id;
			$reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd_id' "));
		}
		$v_text="PIN";
		$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));
		
		//$uhid_id=$pinfo['uhid'];
		$uhid_id=$uhid;
		
		$doc=mysqli_fetch_array(mysqli_query($link, "select refbydoctorid,ref_name,qualification from refbydoctor_master where refbydoctorid in( SELECT `refbydoctorid` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$v_id' )"));
		
		 if($doc['refbydoctorid']!="101")
		 {
			$dname=$doc['ref_name']." , ".$doc['qualification'];
		 }
		 else
		 {
			$dname="Self";
		 }
		 
		
		
		
		if($all_test)
		{
			$tech_n="";
			$test=explode("@",$all_test);
			
			$sam="";
			$sam1=" ";
			$micr=0;
			$instr_str="";
			$ins_chk=0;
			foreach($test as $ttst)
			{
				if($ttst)
				{
					$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='$ttst' and SampleId!='1')"));
					$sam.=",".$samp['Name'];
				}
			}
			
			
			$sam=explode(",",$sam);
			$samp=array_unique($sam);
			
			foreach($samp as $samp1)
			{
			if($samp1)
			{
			$sam1.=$samp1.",";
			}
			}
			
			$rp_page=0;
			if($_GET[rp_page]>0)
			{
				$rp_page=$_GET[rp_page];
			}
			$t_page=0;
			
			unset($l_user);
			
			?>
			<br/><br/><br/><br/><br/><br/><br/>
			<!--<img type="jpg/png/jpeg" src="../../images/pad_head.png" style="width:100%;height:80px;top:0px;" />-->
		    <input type="hidden" id="chk_page" value="<?php echo $rp_page;?>"/>
			<div class="container-fluid">
				<div class="row">
					<?php
						//include("page_header.php");
					?>
					<div class="span10">
						
						<h3>Department of Pathology</h3>
						 <div style="text-align:right;width:100%" class="dupl_1"></div>
						<?php
							$phl=mysqli_fetch_array(mysqli_query($link, "select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' limit 0,1"));
							if($phl)
							{
								$collection_date=$phl["date"];
								$collection_time=$phl["time"];
							}else
							{
								$collection_date=$reg["date"];
								$collection_time=$reg["time"];
							}
							 //$lab=mysqli_fetch_array(mysqli_query($link, "select time,date from lab_sample where patient_id='$uhid' and opd_id`='$opd_id' and `ipd_id`='$ipd_id' limit 0,1"));
							 $res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$test[1]' limit 0,1"));
							
												
					 /*-------Report Header-------*/
							
							$sample_name=rtrim($sam1, ",");
							include("report_patient_header_pdf.php");
												
					/*-------Report Header-------*/
					
					
						 ?>
									
					
								
					<div style="height:610px;" id="test_param">
						<table class="table-condensed" width="100%">
							<tr id='t_bold'>
								<td width="30%">TEST</td>
								<td width="15%">RESULTS </td>
								<td width="15%">UNIT </td>
								<td>METHOD</td>
								<td><?php if(!$lab_no[result]){ echo "NORMAL RANGE";}?></td>
							</tr>
							<tr><td colspan="3" style="height:10px"></td></tr>
							<?php
							/*$instr=mysqli_fetch_array(mysqli_query($link,"select * from test_instr where depid='$type_id[type_id]'"));
							
							if($instr[instr_text])
							{
								echo "<tr><td colspan='6' style='border-bottom:1px solid #CCC !important;'>$instr[instr_text]</td></tr>";
								echo "<tr><td colspan='6' style='height:10px'></td></tr>";
							}*/
							?>
							
							<?php
								$equip="";
								$dup1=0;
								$type="";
								$tech_n="";
								if($tests)
								{
									
								
								$l=1;
								$t_p=0;
								$nbl_note==1;
								$nbl_note_test==1;
								$dep=0;
								foreach($test as $tst)
								{
								
								/*
								$chk_equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$tst'"));
								if($chk_equip[equipment]>0)
								{
									$equip.="@@".$chk_equip[equipment];
								}
								
								
								$test_dept=mysqli_fetch_array(mysqli_query($link,"select type_id from testmaster where testid='$tst'"));
								if($test_dept[type_id]!=$dep)
								{
									$instr=mysqli_fetch_array(mysqli_query($link,"select * from test_instr where depid='$test_dept[type_id]'"));
							
									if($instr[instr_text])
									{
										if($t_p>0)
										{
											echo "<tr><td colspan='6' style='border-bottom:1px solid #CCC !important;border-top:1px solid #CCC !important;' contenteditable='true'>$instr[instr_text]</td></tr>";
										}
										else
										{
											echo "<tr><td colspan='6' style='border-bottom:1px solid #CCC !important;' contenteditable='true'>$instr[instr_text]</td></tr>";
										}
										echo "<tr><td colspan='6' style='height:12px'></td></tr>";
										$l++;
									}
									$dep=$test_dept[type_id];
								}
								*/
								
								$chk_dup1=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
								if($chk_dup1>0)
								{
									$dup1=1;
								}
								
								
								$num_tst=mysqli_num_rows(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));									
								if($tst)
								{
								$usr="";
									
								$auth=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
								
								
								$lab_doc=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
								$l_doc[]=$lab_doc[doc];
								
								//////// ***** //////////
								//$lis=mysqli_num_rows(mysqli_query($link,"select * from test_sample_result where reg_no='$reg[reg_no]' and testid='$tst' and result>0"));
								$lis=0;
								if($lis>0)
								{
									$l_user[]="LIS";
								}
								else
								{
									$l_user[]=$lab_doc[tech];
								}
								
								$tech=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$lab_doc[main_tech]'"));
								$tech_n.=$tech[Name].",";
								
								$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$tst'"));
								$type="@".$tname[type_id];
								
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$tst'"));
								if($tot_par>1)
								{
								$t_p=0;
								?>
								<tr>
									<td colspan='6' style="padding-bottom:5px">
										<?php
								$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$tst' and paramid='0'"));
								 if($nbl_test>0)
								 {
									 $nbl_star="*";
									 $nbl_note_test=1;
								 }
										
												
										?>
									
										<b><?php echo $nbl_star.$tname[testname];?></b>
									</td>
								</tr>
							<?php
								}
								else
								{
									//echo "select * from nabl_logo where paramid in(select ParamaterId from Testparameter where TestId='$tst')";
									$t_p=1;
									$nbl_star_par="";
									$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid in(select ParamaterId from Testparameter where TestId='$tst')"));
									if($nbl_tst==0)
									{
										$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
										if($nabl[nabl]==1)
										{
											$nbl_star_par="*";
											$nbl_note=1;
										}
									}
								
								?>
							<tr>
								
								<?php
									}
									
									?>
								<?php
									$i=1;
									$param=mysqli_query($link, "select * from Testparameter where TestId='$tst' order by sequence");
									while($p=mysqli_fetch_array($param))
									{
									$pn=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$p[ParamaterId]'"));
									if($pn[ResultType]!=0)
									{
									$res=mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and paramid='$p[ParamaterId]'");
									$num=mysqli_num_rows($res);
									if($num>0)
									{
										
										$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
										if($pat_note[note])
										{
											$note=$pat_note[note];
										}
										
										
										if($tname[type_id]==33)
										{
											$micr=1;
										}
										
										$all_d++;
									/*-------------------------Check Double Page-----------------------*/ 
									$div_ht="111";
									?>
									
									<?php
									if($l>15)
									{
										$chk_dept=mysqli_fetch_array(mysqli_query($link,"select type_id from testmaster where testid='$tst'"));
										$max_tst=mysqli_fetch_array(mysqli_query($link,"select max(testid) as m_tst from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid in(select testid from testmaster where type_id='$chk_dept[type_id]')"));
										if($tst==$max_tst[m_tst])
										{
											//echo $max_tst[m_tst];$chk_equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$tst'"));
											//$l=24;
										}
								
									}
									
									$chk_page=30;
									if($t_page==0)
									{
										$chk_page=30;
									}
									
									if($l>$chk_page)
									 { $l=1;
									$t_page++;
									
										 ?>
						</table>
						<?php
							if($note)
							{
								echo "<b><i><u>*Note : $note</u></i></b>";
								echo "<p class='text-center' style='margin-top: 5px;position:absolute;right:40%' ><b>----Continued on to next page----</b></p>";
								$note="";
								
							}
							else
							{
								echo "<p class='text-center' style='margin-top: 5px;position:absolute;right:40%' ><b>----Continued on to next page----</b></p>";
								
							}
							
							
							
						?>
						
						
						
					</div>
					
				</div>
			</div>
			
			<!-----Doctors Part----->
			
			<div class="doctors-container">		
			<div class="doctors" style="<?php echo $cls;?>">
				<?php
					$aprv_by=$d;
					$entry_by=$lab_doc[tech];
					$analysis_by=$lab_doc[main_tech];
					include('report_doctor_footer_pdf.php'); 
				?>
			</div>
			</div>
			<!-----Doctors Part----->
			
			
		</div>
		
		</div>
		
		<div class="page_break"></div>
		<div class="container-fluid">
			
			<div class="row">
				<?php
					//include("page_header.php");
				?>
				<div class="span10">
					<br/><br/><br/><br/><br/><br/><br/>
					<h3>Department of Pathology</h3>
					 <div style="text-align:right;width:100%" id="dupl_1"></div>
					<?php
					
						 /*-------Report Header-------*/
							
							$sample_name=rtrim($sam1, ",");
							include("report_patient_header_pdf.php");
												
						/*-------Report Header-------*/
					?>
					<div class="test_gap"></div>
					<div style="height:610px;">
						<table class="table-condensed" width="100%">
							<?php
								
								//$tech_n="";
								$test_rs=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
								if($test_rs>0)
								{
								$all_d++;
														
								
								?>
							<tr id='t_bold'>
								<td width="25%">TEST</td>
								<td width="15%">RESULTS </td>
								<td width="15%">UNIT </td>
								<td>METHOD</td>
								<td><?php if(!$lab_no[result]){ echo "NORMAL RANGE";}?></td>
							</tr>
							<?php
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$tst'"));
								if($tot_par>1)
								{
								?>
							<tr>
								<th colspan='6'>
									<?php
								//$nbl_note=0;
								$nbl_star="";
								$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where testid='$tst'"));
								if($nbl_tst==0)
								{
									$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
									if($nabl[nabl]==1)
									{
										$nbl_star="*";
										$nbl_note=1;
									}
								}
								?>
								
								
								
								
									<?php echo $tname[testname];?>
								</th>
							</tr>
							<?php
								}
								else
								{
									//$nbl_note=0;
									$nbl_star_par="";
									$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where testid='$tst'"));
									if($nbl_tst==0)
									{
										$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
										if($nabl[nabl]==1)
										{
											$nbl_star_par="*";
											$nbl_note=1;
										}
									}
								}
								
								}
								else
								{
								echo "<tr><th colspan='6'>$tname[testname]</th></tr>";
								//$tname=mysql_fetch_array(mysql_query("select testname"));
								}
								}
								
								
								
								
								$p_unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID='$pn[UnitsID]'"));
								$meth=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id in(select method_id from parameter_method where param_id='$p[ParamaterId]')"));
								
								$meth_name=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id='$pn[method]'"));
								
								$t_res=mysqli_fetch_array($res);
								
								
								if($t_p>0)
								{
									$p_name=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID in(select ParamaterId from Testparameter where TestId='$tst')"));
									if($t_p==1)
									{
										?><td class="test_height"><b><?php echo $nbl_star_par.$tname[testname];?></b></td><?php
									}
									else
									{
									?>
									<td class="test_height"><b><?php echo $nbl_star_par.$p_name[Name];?></b></td>
							
									<?php
									}
								}
								else
								{
								echo "<tr class='tr_test'>";
								}
								
								?>
							<?php
								$nres=$t_res[result];
								if($pn[ResultType]!=27)
								{
								
								if($t_p>0)
								{
								
								}
								else
								{
									
								$par_class="";
								if($pn[ResultType]==8)
								{
								$par_class="tname";
								}
								else
								{
								$par_class="";
								}	
									if($nbl_note_test!=1)
									{
										$nbl_star_par="";
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star_par="*";
												$nbl_note=1;
											}
										}
									}
								
								
																						
								
								
								?>
							
							<td class="<?php echo $par_class;?>" valign="top" contenteditable="true"><?php echo $nbl_star_par.$pn[Name];?></td>
							
							<?php
								}
								
								
								
								/*
								$nr=load_normal($uhid,$p[ParamaterId],$t_res['result']);
								$nr1=explode("#",$nr);
								$norm_range=$nr1[0];
								*/
								
								
								?>
							
							<!--<td style="font-size:12px;font-style:italic"><?php echo $meth_name[name];?></td>-->
							
							
							
							<td valign="top" id="result<?php echo $i;?>">
									<?php
									if($t_res[range_status]==1)
									{
										?> <span class="res_size" style='font-weight:bold'> <?php
									}
									else
									{
										?> <span class="res_size"> <?php
									}
									?>
									
										<?php echo nl2br($t_res[result]);?>
									</span>
							</td>
							
							<td class="unit">
									<?php
										echo $p_unit[unit_name];
									?>
							</td>
							
							<td class="meth_name"><?php echo $meth_name[name];?></td>
							
							<td id="norm_r<?php echo $i;?>" style='text-align:left;'>
								<div style="display:inline-block;margin-left:0px;">
								<?php
									$range=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$t_res[range_id]'"));
									$norm_range=str_replace("\n","<br/>",$range[normal_range]);
									echo nl2br($norm_range);
									$lines_arr = preg_split('/\n|\r/',$norm_range);
									if(!$norm_range)
									{
										$num_newlines =1;
									}
									else
									{
										$num_newlines = count($lines_arr); 
									}
									 $l=$l+$num_newlines;
								?>
								</div>
							</td>
							
							<?php
								$j++;
								
								
								}
								else
								{
								?>
							
							
							<?php
								if($meth_name[name])
								{
								?>
							<td width="12%" colspan="2" valign="top">
								<b><?php echo nl2br($t_res[result]);?></b>
							</td>
							<td>
								<?php echo $meth_name[name];?>
							</td>
							<?php
								}
								else
								{
								?>
							<td><?php echo $pn[Name];?></td>
							<td colspan="6" valign="top">
								<?php echo nl2br($t_res[result]);?>
							</td>
							<?php
								}
								$l++;
								}
								
								?>
							</tr>
							<?php
								}
								}
								else
								{
									/*
									if($nbl_note_test!=1)
									{
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star="*";
												$nbl_note=1;
											}
										}
									}
									
									echo "<tr class='tr_test'><td colspan='3'><b>$nbl_star$pn[Name]</b></td></tr>";
									*/
									echo "<tr class='tr_test'><td colspan='3'><b>$pn[Name]</b></td></tr>";
									$l++;
								}
								
								}
								
								}
								$j++;
								}
								}
								
								?>
						</table>
						<?php
											
							if($note)
							{
								echo "<b><i><u>*Note : $note</u></i></b>";
								echo "<p class='text-center' style='margin-top: 5px;' ><b>----End of report----</b></p>";
								
							}
							else
							{
								echo "<p class='text-center' style='margin-top: 10px;' ><b>----End of report----</b></p>";
								
							}
							
							if($nbl_note_test==1){ 
							
							$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
							 $nbl_note=1;}
							 else { }
							
							
							
						?>
					</div>
					<?php
						$equip=explode("@@",$equip);
						$eqp=array_unique($equip);
						
						if(sizeof($eqp)<3)
						{
							foreach($eqp as $eq)
							{
								if($eq)
								{
									$eq_det=mysqli_fetch_array(mysqli_query($link,"select * from lab_instrument_master where id='$eq'"));
									echo "<p contenteditable='true' style='font-weight:bold;font-style:italic;text-align:center'>$eq_det[report_text]</p>";
								}
							}
						}
						
						?>
					<script>load_page_no()</script>
					
				</div>
			</div>
		</div>
				
		<?php
			
	
	
	
	?>
	<!-----Doctors Part----->
		
		<div class="doctors-container">		
		<div class="doctors" style="<?php echo $cls;?>">
			<?php
				$aprv_by=$d;
				$entry_by=$lab_doc[tech];
				$analysis_by=$lab_doc[main_tech];
				include('report_doctor_footer_pdf.php'); 
			?>
		</div>
	    </div>
	<!-----Doctors Part----->
	
	
	<?php
	
	}
	
	if($all_profile)
	{
		$tech_n="";
		$test=explode("@",$all_profile);
		foreach($test as $apt)
		{
			if($apt)
			{
				if($all_d>0)
				{
					$j++;
				?>
					<div class="page_break"></div>
				<?php
				}
					$l=1;
					$samp=mysqli_fetch_array(mysqli_query($link,"select Name from Sample where ID in(select SampleId from TestSample where TestId='$apt')"));
					$auth=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$apt'"));
									
					$phl=mysqli_fetch_array(mysqli_query($link,"select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$apt'"));
					if($phl)
					{
						$collection_date=$phl["date"];
						$collection_time=$phl["time"];
					}else
					{
						$collection_date=$reg["date"];
						$collection_time=$reg["time"];
					}
					////////// ***** /////////
					//$lab=mysqli_fetch_array(mysqli_query($link,"select time,date from lab_sample where patient_id='$uhid' and visit_no='$visit' and testid='$apt'"));
					
					$res_time=mysqli_fetch_array(mysqli_query($link,"select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$apt' limit 0,1"));
					//$cname=mysqli_fetch_array(mysqli_query($link,"select centreno,centrename,onLine from centremaster where centreno in(select centreno from patient_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and centreno!='C100')"));
					//$cname=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));
					if(!$res_time[date])
					{
						$res_time=mysqli_fetch_array(mysqli_query($link,"select time,date from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap'"));	
					}
					
									
					$lab_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysql_ston"],"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$apt' limit 1"));
					$l_doc[]=$lab_doc[doc];
					
					////////// ***** /////////
					//$lis=mysqli_num_rows(mysqli_query($GLOBALS["___mysql_ston"],"select * from test_sample_result where reg_no='$reg[reg_no]' and testid='$apt' and result>0"));
					$lis=0;
					if($lis>0)
					{
						$l_user="LIS";
					}
					else
					{
						$l_us_name=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID='$lab_doc[tech]'"));
						$l_user=$l_us_name[Name];
					}
					
					$tech=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID='$lab_doc[main_tech]'"));
					$tech_n.=$tech[Name].",";
					
					$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$apt'"));
					
					$nbl_star="";
					$nbl_note_test=0;
					$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$apt' and paramid='0'"));
					 if($nbl_test>0)
					 {
						 $nbl_star="*";
						 $nbl_note_test=1;
					 }
					
					$equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$apt'"));
					$dp_name=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$tname[type_id]'"));
					
										
					?>
						<div class="container-fluid">
								<div class="row">
									<?php
										//include("page_header.php");
									?>
									<div class="span10">
										<br/><br/><br/><br/><br/><br/><br/>
										<h3>Department of <?php echo $dp_name[name];?></h3>
										<div style="text-align:right;width:100%" class="dupl_2"></div>
										<?php
										/*-------Report Header-------*/
											$uhid_id=$uhid;
											$sample_name=$samp['Name'];
											include("report_patient_header_pdf.php");
																
										/*-------Report Header-------*/ 
										?>
										
										<div class="test_gap"></div>
										
										<div style="height:610px">
											<table class="table-condensed" width="100%">
											<?php
												$dup2=0;
												$test_rs=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$apt'"));
												if($test_rs>0)
												{
													
													$chk_dup2=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$apt'"));
													if($chk_dup2>0)
													{
														$dup2=1;
													}
													
													$note="";
													$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$apt'"));
													if($pat_note[note])
													{
														$note=$pat_note[note];
													}
													
													
												?>
											<tr id='t_bold'>
												<td width="25%">TEST</td>
												<td width="15%">RESULTS </td>
												<td width="15%">UNIT </td>
												<td>METHOD</td>
												<td><?php if(!$lab_no[result]){ echo "NORMAL RANGE";}?></td>
											</tr>
											<?php
											$usr="";
									
																
								
								
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$apt'"));
								if($tot_par>1)
								{
								$t_p=0;
								?>
								<tr>
									<td colspan='6' style="padding-bottom:5px">
										<?php
								$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$apt' and paramid='0'"));
								 if($nbl_test>0)
								 {
									 $nbl_star="*";
									 $nbl_note_test=1;
								 }
										
												
										?>
									
										<b><?php echo $nbl_star.$tname[testname];?></b>
									</td>
								</tr>
							<?php
								}
								else
								{
									//echo "select * from nabl_logo where paramid in(select ParamaterId from Testparameter where TestId='$apt')";
									$t_p=1;
									$nbl_star_par="";
									$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid in(select ParamaterId from Testparameter where TestId='$apt')"));
									if($nbl_tst==0)
									{
										$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
										if($nabl[nabl]==1)
										{
											$nbl_star_par="*";
											$nbl_note=1;
										}
									}
								
								?>
							<tr>
								
								<?php
									}
									
									?>
								<?php
									$i=1;
									$param=mysqli_query($link, "select * from Testparameter where TestId='$apt' order by sequence");
									while($p=mysqli_fetch_array($param))
									{
									$pn=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$p[ParamaterId]'"));
									if($pn[ResultType]!=0)
									{
									$res=mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$apt' and paramid='$p[ParamaterId]'");
									$num=mysqli_num_rows($res);
									if($num>0)
									{
										
										$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$apt'"));
										if($pat_note[note])
										{
											$note=$pat_note[note];
										}
										
										
										if($tname[type_id]==33)
										{
											$micr=1;
										}
										
										$all_d++;
									/*-------------------------Check Double Page-----------------------*/ 
									$div_ht="111";
									?>
									
									<?php
									if($l>15)
									{
										$chk_dept=mysqli_fetch_array(mysqli_query($link,"select type_id from testmaster where testid='$apt'"));
										$max_tst=mysqli_fetch_array(mysqli_query($link,"select max(testid) as m_tst from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid in(select testid from testmaster where type_id='$chk_dept[type_id]')"));
										if($apt==$max_tst[m_tst])
										{
											//echo $max_tst[m_tst];$chk_equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$apt'"));
											//$l=24;
										}
								
									}
									
									$chk_page=22;
									if($t_page==0)
									{
										$chk_page=22;
									}
									
									if($l>$chk_page)
									 { $l=1;
									$t_page++;
									
										 ?>
						</table>
						<?php
							if($note)
							{
								echo "<b><i><u>*Note : $note</u></i></b>";
								echo "<p class='text-center' style='margin-top: 5px;position:absolute;right:40%' ><b>----Continued on to next page----</b></p>";
								$note="";
								
							}
							else
							{
								echo "<p class='text-center' style='margin-top: 5px;position:absolute;right:40%' ><b>----Continued on to next page----</b></p>";
								
							}
							
							
							
						?>
						
						
						
					</div>
					
				</div>
			</div>
			
			<!-----Doctors Part----->
			
			<div class="doctors-container">		
			<div class="doctors" style="<?php echo $cls;?>">
				<?php
					$aprv_by=$d;
					$entry_by=$lab_doc[tech];
					$analysis_by=$lab_doc[main_tech];
					include('report_doctor_footer_pdf.php'); 
				?>
			</div>
			</div>
			<!-----Doctors Part----->
			
			
		</div>
		
		</div>
		
		<div class="page_break"></div>
		<div class="container-fluid">
			
			<div class="row">
				<?php
					//include("page_header.php");
				?>
				<div class="span10">
					<br/><br/><br/><br/><br/><br/><br/>
					<!--<img type="jpg/png/jpeg" src="../../images/pad_head.png" style="width:100%;height:80px;top:0px;" />-->
					<h3>Department of Pathology</h3>
					 <div style="text-align:right;width:100%" id="dupl_1"></div>
					<?php
					
						 /*-------Report Header-------*/
							
							$sample_name=rtrim($sam1, ",");
							include("report_patient_header_pdf.php");
												
						/*-------Report Header-------*/
					?>
					<div class="test_gap"></div>
					<div style="height:610px;">
						<table class="table-condensed" width="100%">
							<?php
								
								//$tech_n="";
								$test_rs=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$apt'"));
								if($test_rs>0)
								{
								$all_d++;
														
								
								?>
							<tr id='t_bold'>
								<td width="25%">TEST</td>
								<td width="15%">RESULTS </td>
								<td width="15%">UNIT </td>
								<td>METHOD</td>
								<td><?php if(!$lab_no[result]){ echo "NORMAL RANGE";}?></td>
							</tr>
							<?php
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$apt'"));
								if($tot_par>1)
								{
								?>
							<tr>
								<td colspan='6'>
								<b>
									<?php
								//$nbl_note=0;
								$nbl_star="";
								$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where testid='$apt'"));
								if($nbl_tst==0)
								{
									$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
									if($nabl[nabl]==1)
									{
										$nbl_star="*";
										$nbl_note=1;
									}
								}
								?>
								
								
								
								
									<?php echo $tname[testname];?>
									</b>	
								</td>
							</tr>
							<?php
								}
								else
								{
									//$nbl_note=0;
									$nbl_star_par="";
									$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where testid='$apt'"));
									if($nbl_tst==0)
									{
										$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
										if($nabl[nabl]==1)
										{
											$nbl_star_par="*";
											$nbl_note=1;
										}
									}
								}
								
								}
								else
								{
								echo "<tr><th colspan='6'>$tname[testname]</th></tr>";
								
								}
								}
								
								
								
								
								$p_unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID='$pn[UnitsID]'"));
								$meth=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id in(select method_id from parameter_method where param_id='$p[ParamaterId]')"));
								
								$meth_name=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id='$pn[method]'"));
								
								$t_res=mysqli_fetch_array($res);
								
								
								if($t_p>0)
								{
									$p_name=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID in(select ParamaterId from Testparameter where TestId='$apt')"));
									if($t_p==1)
									{
										?><td class="test_height"><b><?php echo $nbl_star_par.$tname[testname];?></b></td><?php
									}
									else
									{
									?>
									<td class="test_height"><b><?php echo $nbl_star_par.$p_name[Name];?></b></td>
							
									<?php
									}
								}
								else
								{
								echo "<tr class='tr_test'>";
								}
								
								?>
							<?php
								$nres=$t_res[result];
								if($pn[ResultType]!=27)
								{
								
								if($t_p>0)
								{
								
								}
								else
								{
									
								$par_class="";
								if($pn[ResultType]==8)
								{
								$par_class="tname";
								}
								else
								{
								$par_class="";
								}	
									if($nbl_note_test!=1)
									{
										$nbl_star_par="";
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star_par="*";
												$nbl_note=1;
											}
										}
									}
								
								
																						
								
								
								?>
							
							<td class="<?php echo $par_class;?>" valign="top" contenteditable="true"><?php echo $nbl_star_par.$pn[Name];?></td>
							
							<?php
								}
								
								
								
							$res_cols=1;	
							$unit_cols=1;
							if(trim($p_unit[unit_name])=="") 
							{
								$res_cols++;
								if(trim($meth_name[name])=="") 
								{
									$res_cols++;	
									$unit_cols++;	
								}
							}
								
							?>
							
									
							
							<td valign="top" id="result<?php echo $i;?>" colspan="<?php echo $res_cols;?>">
									<?php
									if($t_res[range_status]==1)
									{
										?> <span class="res_size" style='font-weight:bold'> <?php
									}
									else
									{
										?> <span class="res_size"> <?php
									}
									?>
									
										<?php echo nl2br($t_res[result]);?>
									</span>
							</td>
							<?php
							if(trim($p_unit[unit_name])!="") 
							{ ?><td class="unit" colspan="<?php echo $unit_cols;?>"><?php echo $p_unit[unit_name];?></td><?php	}
							
							if(trim($p_unit[unit_name])!="") 
							{ ?> <td class="meth_name"><?php echo $meth_name[name];?></td> <?php } ?>
							
							<td id="norm_r<?php echo $i;?>" style='text-align:left;'>
								<div style="display:inline-block;margin-left:0px;">
								<?php
									$range=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$t_res[range_id]'"));
									$norm_range=str_replace("\n","<br/>",$range[normal_range]);
									echo nl2br($norm_range);
									$lines_arr = preg_split('/\n|\r/',$norm_range);
									if(!$norm_range)
									{
										$num_newlines =1;
									}
									else
									{
										$num_newlines = count($lines_arr); 
									}
									 $l=$l+$num_newlines;
								?>
								</div>
							</td>
							
							<?php
								$j++;
								
								
								}
								else
								{
								?>
							
							
							<?php
								if($meth_name[name])
								{
								?>
							<td width="12%" colspan="2" valign="top">
								<b><?php echo nl2br($t_res[result]);?></b>
							</td>
							<td>
								<?php echo $meth_name[name];?>
							</td>
							<?php
								}
								else
								{
								?>
							<td><?php echo $pn[Name];?></td>
							<td colspan="6" valign="top">
								<?php echo nl2br($t_res[result]);?>
							</td>
							<?php
								}
								$l++;
								}
								
								?>
							</tr>
							<?php
								}
								}
								else
								{
									/*
									if($nbl_note_test!=1)
									{
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star="*";
												$nbl_note=1;
											}
										}
									}
									
									echo "<tr class='tr_test'><td colspan='3'><b>$nbl_star$pn[Name]</b></td></tr>";
									*/
									echo "<tr class='tr_test'><td colspan='3'><b>$pn[Name]</b></td></tr>";
									$l++;
								}
								
								}
								
											
								}
								?>
								</table>
								<?php
									
								if($note)
								{
									echo "<b><i><u>*Note : $note</u></i></b>";
									echo "<p class='text-center' style='margin-top: 5px;'><b>----End of report----</b></p>";
									
								}
								else
								{
									echo "<p class='text-center' style='margin-top: 10px;'><b>----End of report----</b></p>";
									
								}
								?>
								</div>
								<script>load_page_no()</script>
							</div>
						</div>
					</div>
					
	
		<!-----Doctors Part----->
		
		<div class="doctors-container">		
		<div class="doctors" style="<?php echo $cls;?>">
			<?php
				$aprv_by=$d;
				$entry_by=$lab_doc[tech];
				$analysis_by=$lab_doc[main_tech];
				include('report_doctor_footer_pdf.php'); 
			?>
		</div>
	    </div>
				<?php
				
				$all_d++;
			}
			$j++;
		}
	}
	
	if($all_re)
	{
		$tech_n="";
		$test=explode("@",$all_re);
		foreach($test as $re)
		{
			if($re)
			{
				if($all_d>0)
				{
					$j++;
				?>
					<div class="page_break"></div>
				<?php
				}
					$l=1;
					$samp=mysqli_fetch_array(mysqli_query($link,"select Name from Sample where ID in(select SampleId from TestSample where TestId='$apt')"));
					$auth=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$re'"));
									
					$phl=mysqli_fetch_array(mysqli_query($link,"select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$re'"));
					if($phl)
					{
						$collection_date=$phl["date"];
						$collection_time=$phl["time"];
					}else
					{
						$collection_date=$reg["date"];
						$collection_time=$reg["time"];
					}
					////////// ***** /////////
					//$lab=mysqli_fetch_array(mysqli_query($link,"select time,date from lab_sample where patient_id='$uhid' and visit_no='$visit' and testid='$re'"));
					
					$res_time=mysqli_fetch_array(mysqli_query($link,"select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$re' limit 0,1"));
					//$cname=mysqli_fetch_array(mysqli_query($link,"select centreno,centrename,onLine from centremaster where centreno in(select centreno from patient_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and centreno!='C100')"));
					//$cname=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));
					if(!$res_time[date])
					{
						$res_time=mysqli_fetch_array(mysqli_query($link,"select time,date from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap'"));	
					}
					
									
					$lab_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysql_ston"],"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$re' limit 1"));
					$l_doc[]=$lab_doc[doc];
					
					////////// ***** /////////
					//$lis=mysqli_num_rows(mysqli_query($GLOBALS["___mysql_ston"],"select * from test_sample_result where reg_no='$reg[reg_no]' and testid='$re' and result>0"));
					$lis=0;
					if($lis>0)
					{
						$l_user="LIS";
					}
					else
					{
						$l_us_name=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID='$lab_doc[tech]'"));
						$l_user=$l_us_name[Name];
					}
					
					$tech=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID='$lab_doc[main_tech]'"));
					$tech_n.=$tech[Name].",";
					
					$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$re'"));
					
					$nbl_star="";
					$nbl_note_test=0;
					$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$re' and paramid='0'"));
					 if($nbl_test>0)
					 {
						 $nbl_star="*";
						 $nbl_note_test=1;
					 }
					
					$equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$re'"));
					$dp_name=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$tname[type_id]'"));
					
					
					?>
						<div class="container-fluid">
								<div class="row">
									<?php
										//include("page_header.php");
									?>
									<div class="span10">
										<br/><br/><br/><br/><br/><br/><br/>
										<h3>Department of <?php echo $dp_name[name];?></h3>
										<div style="text-align:right;width:100%" class="dupl_2"></div>
										<?php
										/*-------Report Header-------*/
											$uhid_id=$uhid;
											$sample_name=$samp['Name'];
											include("report_patient_header_pdf.php");
																
										/*-------Report Header-------*/ 
										?>
										
										<div class="test_gap"></div>
										
										<div style="height:610px">
											<table class="table-condensed" width="100%">
											<?php
												$dup2=0;
												$test_rs=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$re'"));
												if($test_rs>0)
												{
													
													$chk_dup2=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$re'"));
													if($chk_dup2>0)
													{
														$dup2=1;
													}
													
													$note="";
													$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$re'"));
													if($pat_note[note])
													{
														$note=$pat_note[note];
													}
													
													
												?>
											<tr id='t_bold'>
												<td width="50%">TEST</td>
												<td width="50%">RESULTS </td>
											</tr>
											<?php
											$usr="";
									
																
								
								
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$re'"));
								if($tot_par>1)
								{
								$t_p=0;
								?>
								<tr>
									<td colspan='6' style="padding-bottom:5px">
										<?php
								$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$re' and paramid='0'"));
								 if($nbl_test>0)
								 {
									 $nbl_star="*";
									 $nbl_note_test=1;
								 }
										
												
										?>
									
										<b><?php echo $nbl_star.$tname[testname];?></b>
									</td>
								</tr>
							<?php
								}
								else
								{
									//echo "select * from nabl_logo where paramid in(select ParamaterId from Testparameter where TestId='$re')";
									$t_p=1;
									$nbl_star_par="";
									$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid in(select ParamaterId from Testparameter where TestId='$re')"));
									if($nbl_tst==0)
									{
										$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
										if($nabl[nabl]==1)
										{
											$nbl_star_par="*";
											$nbl_note=1;
										}
									}
								
								?>
							<tr>
								
								<?php
									}
									
									?>
								<?php
									$i=1;
									$param=mysqli_query($link, "select * from Testparameter where TestId='$re' order by sequence");
									while($p=mysqli_fetch_array($param))
									{
									$pn=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$p[ParamaterId]'"));
									if($pn[ResultType]!=0)
									{
									$res=mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$re' and paramid='$p[ParamaterId]'");
									$num=mysqli_num_rows($res);
									if($num>0)
									{
										
										$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$re'"));
										if($pat_note[note])
										{
											$note=$pat_note[note];
										}
										
										
										if($tname[type_id]==33)
										{
											$micr=1;
										}
										
										$all_d++;
									/*-------------------------Check Double Page-----------------------*/ 
									$div_ht="111";
									?>
									
									<?php
									if($l>15)
									{
										$chk_dept=mysqli_fetch_array(mysqli_query($link,"select type_id from testmaster where testid='$re'"));
										$max_tst=mysqli_fetch_array(mysqli_query($link,"select max(testid) as m_tst from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid in(select testid from testmaster where type_id='$chk_dept[type_id]')"));
										if($re==$max_tst[m_tst])
										{
											//echo $max_tst[m_tst];$chk_equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$re'"));
											//$l=24;
										}
								
									}
									
									$chk_page=22;
									if($t_page==0)
									{
										$chk_page=22;
									}
									
									if($l>$chk_page)
									 { $l=1;
									$t_page++;
									
										 ?>
						</table>
						<?php
							if($note)
							{
								echo "<b><i><u>*Note : $note</u></i></b>";
								echo "<p class='text-center' style='margin-top: 5px;position:absolute;right:40%' ><b>----Continued on to next page----</b></p>";
								$note="";
								
							}
							else
							{
								echo "<p class='text-center' style='margin-top: 5px;position:absolute;right:40%' ><b>----Continued on to next page----</b></p>";
								
							}
							
							
							
						?>
						
						
						
					</div>
					
				</div>
			</div>
			
			<!-----Doctors Part----->
			
			<div class="doctors-container">		
			<div class="doctors" style="<?php echo $cls;?>">
				<?php
					$aprv_by=$d;
					$entry_by=$lab_doc[tech];
					$analysis_by=$lab_doc[main_tech];
					include('report_doctor_footer_pdf.php'); 
				?>
			</div>
			</div>
			<!-----Doctors Part----->
			
			
		</div>
		
		</div>
		
		<div class="page_break"></div>
		<div class="container-fluid">
			
			<div class="row">
				<?php
					//include("page_header.php");
				?>
				<div class="span10">
					<br/><br/><br/><br/><br/><br/><br/>
					<!--<img type="jpg/png/jpeg" src="../../images/pad_head.png" style="width:100%;height:80px;top:0px;" />-->
					<h3>Department of Pathology</h3>
					 <div style="text-align:right;width:100%" id="dupl_1"></div>
					<?php
					
						 /*-------Report Header-------*/
							
							$sample_name=rtrim($sam1, ",");
							include("report_patient_header_pdf.php");
												
						/*-------Report Header-------*/
					?>
					<div class="test_gap"></div>
					<div style="height:610px;">
						<table class="table-condensed" width="100%">
							<?php
								
								//$tech_n="";
								$test_rs=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$re'"));
								if($test_rs>0)
								{
								$all_d++;
														
								
								?>
							<tr id='t_bold'>
								<td width="50%">TEST</td>
								<td width="50%">RESULTS </td>
							</tr>
							<?php
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$re'"));
								if($tot_par>1)
								{
								?>
							<tr>
								<td colspan='6'>
								<b>
									<?php
								//$nbl_note=0;
								$nbl_star="";
								$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where testid='$re'"));
								if($nbl_tst==0)
								{
									$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
									if($nabl[nabl]==1)
									{
										$nbl_star="*";
										$nbl_note=1;
									}
								}
								?>
								
								
								
								
									<?php echo $tname[testname];?>
									</b>	
								</td>
							</tr>
							<?php
								}
								else
								{
									//$nbl_note=0;
									$nbl_star_par="";
									$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where testid='$re'"));
									if($nbl_tst==0)
									{
										$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
										if($nabl[nabl]==1)
										{
											$nbl_star_par="*";
											$nbl_note=1;
										}
									}
								}
								
								}
								else
								{
								echo "<tr><th colspan='6'>$tname[testname]</th></tr>";
								
								}
								}
								
								
								
								
								$p_unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID='$pn[UnitsID]'"));
								$meth=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id in(select method_id from parameter_method where param_id='$p[ParamaterId]')"));
								
								$meth_name=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id='$pn[method]'"));
								
								$t_res=mysqli_fetch_array($res);
								
								
								if($t_p>0)
								{
									$p_name=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID in(select ParamaterId from Testparameter where TestId='$re')"));
									if($t_p==1)
									{
										?><td class="test_height"><b><?php echo $nbl_star_par.$tname[testname];?></b></td><?php
									}
									else
									{
									?>
									<td class="test_height"><b><?php echo $nbl_star_par.$p_name[Name];?></b></td>
							
									<?php
									}
								}
								else
								{
								echo "<tr class='tr_test'>";
								}
								
								?>
							<?php
								$nres=$t_res[result];
								if($pn[ResultType]!=27)
								{
								
								if($t_p>0)
								{
								
								}
								else
								{
									
								$par_class="";
								if($pn[ResultType]==8)
								{
								$par_class="tname";
								}
								else
								{
								$par_class="";
								}	
									if($nbl_note_test!=1)
									{
										$nbl_star_par="";
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star_par="*";
												$nbl_note=1;
											}
										}
									}
								
								
																						
								
								
								?>
							
							<td class="<?php echo $par_class;?>" valign="top" contenteditable="true"><?php echo $nbl_star_par.$pn[Name];?></td>
							
							<?php
								}
								
								
								
							$res_cols=1;	
							$unit_cols=1;
							if(trim($p_unit[unit_name])=="") 
							{
								$res_cols++;
								if(trim($meth_name[name])=="") 
								{
									$res_cols++;	
									$unit_cols++;	
								}
							}
								
							?>
							
									
							
							<td valign="top" id="result<?php echo $i;?>" colspan="<?php echo $res_cols;?>">
									<?php
									if($t_res[range_status]==1)
									{
										?> <span class="res_size" style='font-weight:bold'> <?php
									}
									else
									{
										?> <span class="res_size"> <?php
									}
									?>
									
										<?php echo nl2br($t_res[result]);?>
									</span>
							</td>
							<!--
							<?php
							if(trim($p_unit[unit_name])!="") 
							{ ?><td class="unit" colspan="<?php echo $unit_cols;?>"><?php echo $p_unit[unit_name];?></td><?php	}
							
							if(trim($p_unit[unit_name])!="") 
							{ ?> <td class="meth_name"><?php echo $meth_name[name];?></td> <?php } ?>
							
							<td id="norm_r<?php echo $i;?>" style='text-align:left;'>
								<div style="display:inline-block;margin-left:0px;">
								<?php
									$range=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$t_res[range_id]'"));
									$norm_range=str_replace("\n","<br/>",$range[normal_range]);
									echo nl2br($norm_range);
									$lines_arr = preg_split('/\n|\r/',$norm_range);
									if(!$norm_range)
									{
										$num_newlines =1;
									}
									else
									{
										$num_newlines = count($lines_arr); 
									}
									 $l=$l+$num_newlines;
								?>
								</div>
							</td>
							-->
							<?php
								$j++;
								
								
								}
								else
								{
								?>
							
							
							<?php
								if($meth_name[name])
								{
								?>
							<td width="12%" colspan="2" valign="top">
								<b><?php echo nl2br($t_res[result]);?></b>
							</td>
							<td>
								<?php echo $meth_name[name];?>
							</td>
							<?php
								}
								else
								{
								?>
							<td><?php echo $pn[Name];?></td>
							<td colspan="6" valign="top">
								<?php echo nl2br($t_res[result]);?>
							</td>
							<?php
								}
								$l++;
								}
								
								?>
							</tr>
							<?php
								}
								}
								else
								{
									/*
									if($nbl_note_test!=1)
									{
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star="*";
												$nbl_note=1;
											}
										}
									}
									
									echo "<tr class='tr_test'><td colspan='3'><b>$nbl_star$pn[Name]</b></td></tr>";
									*/
									echo "<tr class='tr_test'><td colspan='3'><b>$pn[Name]</b></td></tr>";
									$l++;
								}
								
								}
								
											
								}
								?>
								</table>
								<?php
								if($note)
								{
									echo "<b><i><u>*Note : $note</u></i></b>";
									echo "<p class='text-center' style='margin-top: 5px;'><b>----End of report----</b></p>";
									
								}
								else
								{
									echo "<p class='text-center' style='margin-top: 10px;'><b>----End of report----</b></p>";
									
								}
								?>
								</div>
								<script>load_page_no()</script>
							</div>
						</div>
					</div>
					
	
		<!-----Doctors Part----->
		
		<div class="doctors-container">		
		<div class="doctors" style="<?php echo $cls;?>">
			<?php
				$aprv_by=$d;
				$entry_by=$lab_doc[tech];
				$analysis_by=$lab_doc[main_tech];
				include('report_doctor_footer_pdf.php'); 
			?>
		</div>
	    </div>
				<?php
				
				$all_d++;	
			}
		}	
	}
	
	if($gtt>0)
	{
		$j++;
		?>
			<div class="container-fluid">
				
				<div class="row">
					<?php
						//include("page_header.php");
						
					?>
					<div class="span10">
						<h3>Department of Pathology</h3>
						 <div style="text-align:right;width:100%" class="dupl_1"></div>
						<?php
							 $phl=mysqli_fetch_array(mysqli_query($link, "select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='515' limit 0,1"));
							 //$lab=mysqli_fetch_array(mysqli_query($link, "select time,date from lab_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' limit 0,1"));
							 $res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='515' limit 0,1"));
							 $samp=mysqli_fetch_array(mysqli_query($link,"select Name from Sample where ID in(select SampleId from TestSample where TestId='515')"));	
								if($phl)
								{
									$collection_date=$phl["date"];
									$collection_time=$phl["time"];
								}else
								{
									$collection_date=$reg["date"];
									$collection_time=$reg["time"];
								}	 
						 ?>
						
								<?php
								/*-------Report Header-------*/
									$uhid_id=$uhid;
									$sample_name=$samp['Name'];
									include("report_patient_header_pdf.php");
														
								/*-------Report Header-------*/ 
								?>
						
					<div class="test_gap"></div>				
					<div style="height:610px;" id="test_param">
						<table class="table-condensed" width="100%">
							<tr id='t_bold'>
								<td>TEST</td>
								<!--<td>METHOD</td>-->
								<td colspan="1">RESULTS </td>
								<td>UNIT</td>
								<td ><?php if(!$lab_no[result]){ echo "NORMAL RANGE";}?></td>
								
							</tr>
							<?php
								$dup1=0;
								$type="";
								$tech_n="";
								if($gtt)
								{
									
								
								$l=1;
								$t_p=0;
								$tst=515;
								
								
								
								$chk_dup1=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
								if($chk_dup1>0)
								{
									$dup1=1;
								}
								
								
								$num_tst=mysqli_num_rows(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));									
								if($tst)
								{
								$usr="";
									
								$auth=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
								
								
								$lab_doc=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
								$l_doc[]=$lab_doc[doc];
								
								//////////// ***** /////////////
								//$lis=mysqli_num_rows(mysqli_query($link,"select * from test_sample_result where reg_no='$reg[reg_no]' and testid='$tst' and result>0"));
								$lis=0;
								if($lis>0)
								{
									$l_user[]="LIS";
								}
								else
								{
									$l_user[]=$lab_doc[tech];
								}
								
								$tech=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$lab_doc[main_tech]'"));
								$tech_n.=$tech[Name].",";
								
								$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$tst'"));
								$type="@".$tname[type_id];
								
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$tst'"));
								if($tot_par>1)
								{
								$t_p=0;
								?>
								<tr>
									<td colspan='6' style="padding-bottom:5px">
										<?php
								$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$tst' and paramid='0'"));
								 if($nbl_test>0)
								 {
									 $nbl_star="*";
									 $nbl_note_test=1;
								 }
										
												
										?>
									
										<b><?php echo $nbl_star.$tname[testname];?></b>
									</td>
								</tr>
							<?php
								}
								else
								{
									//echo "select * from nabl_logo where paramid in(select ParamaterId from Testparameter where TestId='$tst')";
									$t_p=1;
									$nbl_star_par="";
									$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid in(select ParamaterId from Testparameter where TestId='$tst')"));
									if($nbl_tst==0)
									{
										$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
										if($nabl[nabl]==1)
										{
											$nbl_star_par="*";
											$nbl_note=1;
										}
									}
								
								?>
							<tr>
								
								<?php
									}
									
									?>
								<?php
									$i=1;
									$param=mysqli_query($link, "select * from Testparameter where TestId='$tst' order by sequence");
									while($p=mysqli_fetch_array($param))
									{
									$pn=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$p[ParamaterId]'"));
									if($pn[ResultType]!=0)
									{
									$res=mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and paramid='$p[ParamaterId]'");
									$num=mysqli_num_rows($res);
									if($num>0)
									{
										
										$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
										if($pat_note[note])
										{
											$note=$pat_note[note];
										}
										
										
										if($tname[type_id]==33)
										{
											$micr=1;
										}
										
										$all_d++;
									
									$div_ht="111";
									?>
									
									<?php
									$chk_page=15;
									if($t_page==0)
									{
										$chk_page=12;
									}
									
								
								
								
								
								
								$p_unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID='$pn[UnitsID]'"));
								$meth=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id in(select method_id from parameter_method where param_id='$p[ParamaterId]')"));
								
								$meth_name=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id='$pn[method]'"));
								
								$t_res=mysqli_fetch_array($res);
								
								if($t_p>0)
								{
									$p_name=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID in(select ParamaterId from Testparameter where TestId='$tst')"));
									?>
									<td class="test_height"><b><?php echo $nbl_star_par.$p_name[Name];?></b></td>
							
									<?php
								}
								else
								{
								echo "<tr class='tr_test'>";
								}
								
								?>
							<?php
								$nres=$t_res[result];
								if($pn[ResultType]!=27)
								{
								
								if($t_p>0)
								{
								
								}
								else
								{
									
								$par_class="";
								if($pn[ResultType]==8)
								{
								$par_class="tname";
								}
								else
								{
								$par_class="";
								}	
									if($nbl_note_test!=1)
									{
										$nbl_star_par="";
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star_par="*";
												$nbl_note=1;
											}
										}
									}
									
								?>
							<td class="<?php echo $par_class;?>" valign="top" contenteditable="true"><?php echo $nbl_star_par.$pn[Name];?></td>
							<?php
								}
								?>
							<td style="font-size:13px;font-style:italic"><?php echo $meth_name[name];?></td>
							<td width="1%" ><b><span id="chk_res<?php echo $j;?>"></span></b></td>
							<td valign="top" id="result<?php echo $i;?>" ><span class="res_size"><b><?php echo nl2br($t_res[result]);?></b></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $p_unit[unit_name];?></td>
							<td id="norm_r<?php echo $j;?>"  class="normal_range">
								<?php
								/*							
								$nr=load_normal($uhid,$p[ParamaterId],$t_res[result]);
								$nr1=explode("#",$nr);
									
								if($nr1[1]=="Error")
								{
									//echo "<script>$('#chk_res'+".$j."+'').text('*')</script>";
								}
								
								$norm_range=$nr1[0];
								*/
								$range=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$t_res[range_id]'"));
								echo $norm_range=str_replace("\n","<br/>",$range[normal_range]);
								?>
							</td>
							
							<?php
								$j++;
								$l++;
								
								}
								else
								{
								?>
							
							
							<?php
								if($meth_name[name])
								{
								?>
							<td width="12%" colspan="3" valign="top">
								<b><?php echo nl2br($t_res[result]);?></b>
							</td>
							<td>
								<?php echo $meth_name[name];?>
							</td>
							<?php
								}
								else
								{
								?>
							<td><?php echo $pn[Name];?></td>
							<td colspan="6" valign="top">
								<?php echo nl2br($t_res[result]);?>
							</td>
							<?php
								}
								$l++;
								}
								
								?>
							</tr>
							<?php
								}
								}
								else
								{
									/*
									if($nbl_note_test!=1)
									{
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star="*";
												$nbl_note=1;
											}
										}
									}
									
									echo "<tr class='tr_test'><td colspan='3'><b>$nbl_star$pn[Name]</b></td></tr>";
									*/
									echo "<tr class='tr_test'><td colspan='3'><b>$pn[Name]</b></td></tr>";
								}
								
								}
								
								}
								$j++;
								
								}
								
								?>
						</table>
						<br/>
						<div align="center">
							<img src="image_graph.php?uhid=<?php echo $uhid;?>&opd_id=<?php echo $opd_id;?>&ipd_id=<?php echo $ipd_id;?>&batch_no=<?php echo $batch_no;?>&type=1"/>
							<img src="image_graph.php?uhid=<?php echo $uhid;?>&opd_id=<?php echo $opd_id;?>&ipd_id=<?php echo $ipd_id;?>&batch_no=<?php echo $batch_no;?>&type=2"/>
						</div>
						<?php
							if($note)
							{
								echo "<b><i><u>*Note : $note</u></i></b>";
								echo "<p class='text-center' style='margin-top: 5px;' ><b>----End of report----</b></p>";
							}
							else
							{
								echo "<p class='text-center' style='margin-top: 12px;' ><b>----End of report----</b></p>";
							}
							
							if($nbl_note_test==1){ 
							
							$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
							 $nbl_note=1;}
							 else { }
							
							
							
						?>
					</div>
					
					<script>load_page_no()</script>
					<script>load_duplicate(1,<?php echo $dup1;?>)</script>
				</div>
			</div>
		</div>
		
				
		<?php
			
	
	
	
	?>
	<!-----Doctors Part----->
		
		<div class="doctors-container">		
		<div class="doctors" style="<?php echo $cls;?>">
			<?php
				$aprv_by=$d;
				$entry_by=$lab_doc[tech];
				$analysis_by=$lab_doc[main_tech];
				include('report_doctor_footer_pdf.php'); 
			?>
		</div>
	    </div>
		<!-----Doctors Part----->
	<?php
	
	
	
	
	
	}
	
	
	
	if($all_ptst)
	{
		$al_pt=explode("@",$all_ptst);
		foreach($al_pt as $pts)
		{
			if($pts)
			{
				if($all_d>0)
				{
					$j++;
					?>
						<div class="page_break"></div>
					<?php
				}
				$l=1;
					$samp=mysqli_fetch_array(mysqli_query($link,"select Name from Sample where ID in(select SampleId from TestSample where TestId='$pts')"));
					$auth=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts'"));
									
					$phl=mysqli_fetch_array(mysqli_query($link,"select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts'"));
					if($phl)
					{
						$collection_date=$phl["date"];
						$collection_time=$phl["time"];
					}else
					{
						$collection_date=$reg["date"];
						$collection_time=$reg["time"];
					}
					////////// ***** /////////
					//$lab=mysqli_fetch_array(mysqli_query($link,"select time,date from lab_sample where patient_id='$uhid' and visit_no='$visit' and testid='$pts'"));
					
					$res_time=mysqli_fetch_array(mysqli_query($link,"select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts' limit 0,1"));
					//$cname=mysqli_fetch_array(mysqli_query($link,"select centreno,centrename,onLine from centremaster where centreno in(select centreno from patient_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and centreno!='C100')"));
					//$cname=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));
					if(!$res_time[date])
					{
						$res_time=mysqli_fetch_array(mysqli_query($link,"select time,date from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap'"));	
					}
					
									
					$lab_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysql_ston"],"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts' limit 1"));
					$l_doc[]=$lab_doc[doc];
					
					////////// ***** /////////
					//$lis=mysqli_num_rows(mysqli_query($GLOBALS["___mysql_ston"],"select * from test_sample_result where reg_no='$reg[reg_no]' and testid='$pts' and result>0"));
					$lis=0;
					if($lis>0)
					{
						$l_user="LIS";
					}
					else
					{
						$l_us_name=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID='$lab_doc[tech]'"));
						$l_user=$l_us_name[Name];
					}
					
					$tech=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID='$lab_doc[main_tech]'"));
					$tech_n.=$tech[Name].",";
					
					$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$pts'"));
					
					$nbl_star="";
					$nbl_note_test=0;
					$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$pts' and paramid='0'"));
					 if($nbl_test>0)
					 {
						 $nbl_star="*";
						 $nbl_note_test=1;
					 }
					
					$equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$pts'"));
					$dp_name=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$tname[type_id]'"));
										
					?>
						<div class="container-fluid">
								<div class="row">
									<?php
										//include("page_header.php");
									?>
									<div class="span10">
										<br/><br/><br/><br/><br/><br/><br/>
										<h3>Department of <?php echo $dp_name[name];?></h3>
										<div style="text-align:right;width:100%" class="dupl_2"></div>
										<?php
										/*-------Report Header-------*/
											$uhid_id=$uhid;
											$sample_name=$samp['Name'];
											include("report_patient_header_pdf.php");
																
										/*-------Report Header-------*/ 
										?>
										
										<div class="test_gap"></div>
										
										<div style="height:610px">
											<table class="table-condensed" width="100%">
											<?php
												$dup2=0;
												$test_rs=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts'"));
												if($test_rs>0)
												{
													
													$chk_dup2=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts'"));
													if($chk_dup2>0)
													{
														$dup2=1;
													}
													
													$note="";
													$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts'"));
													if($pat_note[note])
													{
														$note=$pat_note[note];
													}
													
													
												?>
											<tr id='t_bold'>
												<td width="25%">TEST</td>
												<td width="15%">RESULTS </td>
												<td width="15%">UNIT </td>
												<td>METHOD</td>
												<td><?php if(!$lab_no[result]){ echo "NORMAL RANGE";}?></td>
											</tr>
											<?php
											$usr="";
									
																
								
								
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$pts'"));
								if($tot_par>1)
								{
								$t_p=0;
								?>
								<tr>
									<td colspan='6' style="padding-bottom:5px">
										<?php
								$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$pts' and paramid='0'"));
								 if($nbl_test>0)
								 {
									 $nbl_star="*";
									 $nbl_note_test=1;
								 }
										
												
										?>
									
										<b><?php echo $nbl_star.$tname[testname];?></b>
									</td>
								</tr>
							<?php
								}
								else
								{
									//echo "select * from nabl_logo where paramid in(select ParamaterId from Testparameter where TestId='$pts')";
									$t_p=1;
									$nbl_star_par="";
									$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid in(select ParamaterId from Testparameter where TestId='$pts')"));
									if($nbl_tst==0)
									{
										$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
										if($nabl[nabl]==1)
										{
											$nbl_star_par="*";
											$nbl_note=1;
										}
									}
								
								?>
							<tr>
								
								<?php
									}
									
									?>
								<?php
									$i=1;
									$param=mysqli_query($link, "select * from Testparameter where TestId='$pts' order by sequence");
									while($p=mysqli_fetch_array($param))
									{
									$pn=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$p[ParamaterId]'"));
									if($pn[ResultType]!=0)
									{
									$res=mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts' and paramid='$p[ParamaterId]'");
									$num=mysqli_num_rows($res);
									if($num>0)
									{
										
										$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts'"));
										if($pat_note[note])
										{
											$note=$pat_note[note];
										}
										
										
										if($tname[type_id]==33)
										{
											$micr=1;
										}
										
										$all_d++;
									/*-------------------------Check Double Page-----------------------*/ 
									$div_ht="111";
									?>
									
									<?php
									if($l>15)
									{
										$chk_dept=mysqli_fetch_array(mysqli_query($link,"select type_id from testmaster where testid='$pts'"));
										$max_tst=mysqli_fetch_array(mysqli_query($link,"select max(testid) as m_tst from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid in(select testid from testmaster where type_id='$chk_dept[type_id]')"));
										if($pts==$max_tst[m_tst])
										{
											//echo $max_tst[m_tst];$chk_equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$pts'"));
											//$l=24;
										}
								
									}
									
									$chk_page=22;
									if($t_page==0)
									{
										$chk_page=22;
									}
									
									if($l>$chk_page)
									 { $l=1;
									$t_page++;
									
										 ?>
						</table>
						<?php
							if($note)
							{
								echo "<b><i><u>*Note : $note</u></i></b>";
								echo "<p class='text-center' style='margin-top: 5px;position:absolute;right:40%' ><b>----Continued on to next page----</b></p>";
								$note="";
								
							}
							else
							{
								echo "<p class='text-center' style='margin-top: 5px;position:absolute;right:40%' ><b>----Continued on to next page----</b></p>";
								
							}
							
							
							
						?>
						
						
						
					</div>
					
				</div>
			</div>
			
			<!-----Doctors Part----->
			
			<div class="doctors-container">		
			<div class="doctors" style="<?php echo $cls;?>">
				<?php
					$aprv_by=$d;
					$entry_by=$lab_doc[tech];
					$analysis_by=$lab_doc[main_tech];
					include('report_doctor_footer_pdf.php'); 
				?>
			</div>
			</div>
			<!-----Doctors Part----->
			
			
		</div>
		
		</div>
		
		<div class="page_break"></div>
		<div class="container-fluid">
			
			<div class="row">
				<?php
					//include("page_header.php");
				?>
				<div class="span10">
					<br/><br/><br/><br/><br/><br/><br/>
					<!--<img type="jpg/png/jpeg" src="../../images/pad_head.png" style="width:100%;height:80px;top:0px;" />-->
					<h3>Department of Pathology</h3>
					 <div style="text-align:right;width:100%" id="dupl_1"></div>
					<?php
						 /*-------Report Header-------*/
							
							$sample_name=rtrim($sam1, ",");
							include("report_patient_header_pdf.php");
												
						/*-------Report Header-------*/
					?>
					<div class="test_gap"></div>
					<div style="height:610px;">
						<table class="table-condensed" width="100%">
							<?php
								
								//$tech_n="";
								$test_rs=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts'"));
								if($test_rs>0)
								{
								$all_d++;
														
								
								?>
							<tr id='t_bold'>
								<td width="25%">TEST</td>
								<td width="15%">RESULTS </td>
								<td width="15%">UNIT </td>
								<td>METHOD</td>
								<td><?php if(!$lab_no[result]){ echo "NORMAL RANGE";}?></td>
							</tr>
							<?php
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$pts'"));
								if($tot_par>1)
								{
								?>
							<tr>
								<td colspan='6'>
								<b>
									<?php
								//$nbl_note=0;
								$nbl_star="";
								$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where testid='$pts'"));
								if($nbl_tst==0)
								{
									$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
									if($nabl[nabl]==1)
									{
										$nbl_star="*";
										$nbl_note=1;
									}
								}
								?>
								
								
								
								
									<?php echo $tname[testname];?>
									</b>	
								</td>
							</tr>
							<?php
								}
								else
								{
									//$nbl_note=0;
									$nbl_star_par="";
									$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where testid='$pts'"));
									if($nbl_tst==0)
									{
										$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
										if($nabl[nabl]==1)
										{
											$nbl_star_par="*";
											$nbl_note=1;
										}
									}
								}
								
								}
								else
								{
								echo "<tr><th colspan='6'>$tname[testname]</th></tr>";
								
								}
								}
								
								
								
								
								$p_unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID='$pn[UnitsID]'"));
								$meth=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id in(select method_id from parameter_method where param_id='$p[ParamaterId]')"));
								
								$meth_name=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id='$pn[method]'"));
								
								$t_res=mysqli_fetch_array($res);
								
								
								if($t_p>0)
								{
									$p_name=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID in(select ParamaterId from Testparameter where TestId='$pts')"));
									if($t_p==1)
									{
										?><td class="test_height"><b><?php echo $nbl_star_par.$tname[testname];?></b></td><?php
									}
									else
									{
									?>
									<td class="test_height"><b><?php echo $nbl_star_par.$p_name[Name];?></b></td>
							
									<?php
									}
								}
								else
								{
								echo "<tr class='tr_test'>";
								}
								
								?>
							<?php
								$nres=$t_res[result];
								if($pn[ResultType]!=27)
								{
								
								if($t_p>0)
								{
								
								}
								else
								{
									
								$par_class="";
								if($pn[ResultType]==8)
								{
								$par_class="tname";
								}
								else
								{
								$par_class="";
								}	
									if($nbl_note_test!=1)
									{
										$nbl_star_par="";
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star_par="*";
												$nbl_note=1;
											}
										}
									}
								
								
																						
								
								
								?>
							
							<td class="<?php echo $par_class;?>" valign="top" contenteditable="true"><?php echo $nbl_star_par.$pn[Name];?></td>
							
							<?php
								}
								
								
								
							$res_cols=1;	
							$unit_cols=1;
							if(trim($p_unit[unit_name])=="") 
							{
								$res_cols++;
								if(trim($meth_name[name])=="") 
								{
									$res_cols++;	
									$unit_cols++;	
								}
							}
								
							?>
							
									
							
							<td valign="top" id="result<?php echo $i;?>" colspan="<?php echo $res_cols;?>">
									<?php
									if($t_res[range_status]==1)
									{
										?> <span class="res_size" style='font-weight:bold'> <?php
									}
									else
									{
										?> <span class="res_size"> <?php
									}
									?>
									
										<?php echo nl2br($t_res[result]);?>
									</span>
							</td>
							<?php
							if(trim($p_unit[unit_name])!="") 
							{ ?><td class="unit" colspan="<?php echo $unit_cols;?>"><?php echo $p_unit[unit_name];?></td><?php	}
							
							if(trim($p_unit[unit_name])!="") 
							{ ?> <td class="meth_name"><?php echo $meth_name[name];?></td> <?php } ?>
							
							<td id="norm_r<?php echo $i;?>" style='text-align:left;'>
								<div style="display:inline-block;margin-left:0px;">
								<?php
									$range=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$t_res[range_id]'"));
									$norm_range=str_replace("\n","<br/>",$range[normal_range]);
									echo nl2br($norm_range);
									$lines_arr = preg_split('/\n|\r/',$norm_range);
									if(!$norm_range)
									{
										$num_newlines =1;
									}
									else
									{
										$num_newlines = count($lines_arr); 
									}
									 $l=$l+$num_newlines;
								?>
								</div>
							</td>
							
							<?php
								$j++;
								
								
								}
								else
								{
								?>
							
							
							<?php
								if($meth_name[name])
								{
								?>
							<td width="12%" colspan="2" valign="top">
								<b><?php echo nl2br($t_res[result]);?></b>
							</td>
							<td>
								<?php echo $meth_name[name];?>
							</td>
							<?php
								}
								else
								{
								?>
							<td><?php echo $pn[Name];?></td>
							<td colspan="6" valign="top">
								<?php echo nl2br($t_res[result]);?>
							</td>
							<?php
								}
								$l++;
								}
								
								?>
							</tr>
							<?php
								}
								}
								else
								{
									/*
									if($nbl_note_test!=1)
									{
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star="*";
												$nbl_note=1;
											}
										}
									}
									
									echo "<tr class='tr_test'><td colspan='3'><b>$nbl_star$pn[Name]</b></td></tr>";
									*/
									echo "<tr class='tr_test'><td colspan='3'><b>$pn[Name]</b></td></tr>";
									$l++;
								}
								
								}
								
											
								}
								?>
								</table>
								<div class="table-modifier">
													<?php
														$pat_sum=mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$pts'");
														$num_pat=mysqli_num_rows($pat_sum);
														$pat_sum_dis="";
														if($num_pat>0)
														{
														 $pat_s=mysqli_fetch_array($pat_sum);
														 $pat_sum_dis=$pat_s[summary];
														}
														else
														{
														 $chk_sum=mysqli_query($link, "select * from test_summary where testid='$pts'");
														 $num_sum=mysqli_num_rows($chk_sum);
														 if($num_sum>0)
														 {
														 $summ_all=mysqli_fetch_array($chk_sum);
														 $pat_sum_dis=$summ_all[summary];
														 }
														 
														}
														
														if($pat_sum_dis!='')
														{
															$pss=explode("@@nextpage@@",$pat_sum_dis);
															
															if(sizeof($pss)>1)
															{
																$ps_i=1;
																foreach($pss as $ps)
																{
																	if($ps!='')
																	{
																	
																	echo $ps;
																	if(sizeof($pss)!=$ps_i)
																	{
																	?>
																	</div>
																	<?php
																	if($note)
																	{
																		echo "<b><i><u>*Note : $note</u></i></b>";
																		//echo "<p class='text-center' style='margin-top: 5px;'><b>----End of report----</b></p>";
																		
																	}
																	else
																	{
																		echo "<p class='text-center' style='margin-top: 10px;'><b>----Continued on to next page----</b></p>";
																		
																	}
																	
																	if($nbl_note_test==1)
																	{ 
																			$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
																			$nbl_note=1;
																	}
																	else { }
																	?>
																	<script>load_page_no()</script>
																	</div>
																	</div>
																	</div>	
																	<!--
																	<div class="doctors-container">		
																		
																		
																	</div>
																	-->
																	<?php
																	$all_d++;
																	?>
																	<div class="page_break"></div>
																	<br/><br/><br/><br/><br/><br/><br/>
																	<!--<img type="jpg/png/jpeg" src="../../images/pad_head.png" style="width:100%;height:80px;top:0px;" />-->
																	<div class="container-fluid">
																			<div class="row">
																				<div class="span10">
																				<h3>Department of <?php echo $dp_name[name];?></h3>
																				<div style="text-align:right;width:100%" class="dupl_2"></div>
																				<?php
																				/*-------Report Header-------*/
													
																					include("report_patient_header_pdf.php");
																										
																				/*-------Report Header-------*/ 
																				?>
																				<div class="test_gap"></div>
																				
																				<div style="height:610px">
																	<?php
																	$ps_i++;
																}
																}
																}
															}
															else
															{
																echo $pat_sum_dis;
															}
														}
														
														?>
												</div>
								<?php
								
								if($note)
								{
									echo "<b><i><u>*Note : $note</u></i></b>";
									echo "<p class='text-center' style='margin-top: 5px;'><b>----End of report----</b></p>";
									
								}
								else
								{
									echo "<p class='text-center' style='margin-top: 10px;'><b>----End of report----</b></p>";
									
								}
								?>
								</div>
								<script>load_page_no()</script>
							</div>
						</div>
					</div>
					
	
		<!-----Doctors Part----->
		
		<div class="doctors-container">		
		<div class="doctors" style="<?php echo $cls;?>">
			<?php
				$aprv_by=$d;
				$entry_by=$lab_doc[tech];
				$analysis_by=$lab_doc[main_tech];
				include('report_doctor_footer_pdf.php'); 
			?>
		</div>
	    </div>
				<?php
				
				$all_d++;
					
			}
		}
	}
	
	
	
	if($all_cult)
	{
		if($all_d>0)
		{
		?>
			<div class="page_break"></div>
		<?php
		}
		?>
				<br/><br/><br/><br/><br/><br/><br/>
				<!--<img type="jpg/png/jpeg" src="../../images/pad_head.png" style="width:100%;height:80px;top:0px;" />-->
				<input type="hidden" id="chk_page" value="<?php echo $rp_page;?>"/>
				<div class="container-fluid">
					<div class="row">
					<?php
						//include("page_header.php");
					?>
					<div class="span10">
						
						<h5 align="center">Department of Microbiology</h5>
					
					<?php
						$phl=mysqli_fetch_array(mysqli_query($link, "select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and  testid='$all_cult'"));
						if($phl)
						{
							$collection_date=$phl["date"];
							$collection_time=$phl["time"];
						}else
						{
							$collection_date=$reg["date"];
							$collection_time=$reg["time"];
						}
						$res_time=mysqli_fetch_array(mysqli_query($link, "select time,date,tech,main_tech from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and  testid='$all_cult' limit 1"));
						if(!$res_time[date])
						{
							$res_time=mysqli_fetch_array(mysqli_query($link, "select time,date,user as tech,main_tech from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and  testid='$all_cult' limit 1"));
						}
						
						$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='$all_cult')"));
						$sam1=$samp["Name"];
						
						$auth_c=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and  testid='$all_cult'"));
						
						$usr_c=mysqli_fetch_array(mysqli_query($link,"Select name from employee where emp_id='$res_time[tech]'"));
						$usr_t=mysqli_fetch_array(mysqli_query($link,"Select name from employee where emp_id='$res_time[main_tech]'"));
						
											
						 /*-------Report Header-------*/
							
							$sample_name=$samp["Name"];
							include("report_patient_header_pdf.php");
												
						/*-------Report Header-------*/
						
				?>
					
				<div>
					<?php
						$tname=mysqli_fetch_array(mysqli_query($link, "select testname from testmaster where testid='$all_cult'"));
						$spec1=explode(" ",$tname[testname]);
						$spec_s=sizeof($spec1);
						$spec=array_pop($spec1);
						
						$col=mysqli_fetch_array(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and  testid='$all_cult' and paramid='311'"));
						//$num=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and  testid='$all_cult' and paramid='630'"));
						$num=mysqli_num_rows(mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and  testid='$all_cult'"));
						
						$dup3=0; 
						$chk_dup3=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and  testid='$all_cult'"));
						if($chk_dup3>0)
						{
							$dup3=1;
						}
						
						?>
					<div align="center">
						<b><u>MICROBIOLOGY REPORT</u></b>
					</div>
					
					<?php
					$cult_table="";
					$cs_res_ant=mysqli_query($link,"select a.*,b.* from testresults a,Parameter_old b where a.patient_id='$uhid' and a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch_no' and a.testid='$all_cult' and a.paramid=b.ID and b.ResultOptionID='68' order by a.sequence");
					if(mysqli_num_rows($cs_res_ant)==0)
					{
						$cult_table="cult_table";
					}
					?>
					
					<div style="height:610px;">
						<table class="<?php echo $cult_table;?>" width="100%">
						<tr>
							<th style="text-align:left;width:200px;" valign="top">TEST</th>
							<td style="width:5px;" valign="top">:</td>
							<td valign="top"><?php echo $tname[testname];?></td>
						</tr>	
						<?php
							
							$fung=mysqli_fetch_array(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no'  and testid='$all_cult' and paramid='310'"));
							
							$cs_res=mysqli_query($link,"select a.*,b.* from testresults a,Parameter_old b where a.patient_id='$uhid' and a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch_no' and a.testid='$all_cult' and a.paramid=b.ID and b.ID!='311' and b.ID!='312' and b.ResultOptionID!='68' order by a.sequence");	
							while($cs_r=mysqli_fetch_array($cs_res))
							{
								
								if($cs_r[ID]==311 || $cs_r[ID]==312)
								{
											
								}
								else
								{
								?>
									<tr>
										<th style="text-align:left" valign="top"><?php echo $cs_r[Name];?></th>
										<td style="width:5px;" valign="top">:</td>
										<td valign="top"><?php echo $cs_r[result];?></td>
									</tr>
								<?php								
								}
							}
							
							$col=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$all_cult' and paramid='311'"));	
							$pow=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$all_cult' and paramid='312'"));	
							if($col[result])
							{
								?>
									<tr>
										<th style="text-align:left" valign="top">COLONY COUNT</th>
										<td style="width:5px;" valign="top">:</td>
										<td valign="top"><?php echo $col[result]."<sup>".$pow[result]."</sup> CFU/ml of ".$sample_name;?></td>
									</tr>
								<?php
							}
							?>
							
						</table>
						<br/>
						<?php
						
						if(mysqli_num_rows($cs_res_ant)>0)
						{
						?>
							<table class="table-condensed" width="100%">
								<tr>
									<th contentEditable="true" width="33%">SENSITIVE</th>
									<th contentEditable="true" width="33%">INTERMEDIATE</th>
									<th contentEditable="true" width="33%">RESISTANT</th>
								</tr>
								<tr>
									<td valign="top" style="border-right:1px solid #CCC">
										<?php
											$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where a.patient_id='$uhid' AND a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch_no' and a.testid='$all_cult' and (a.result like 'S' or a.result like 's%') and a.paramid=b.ID order by b.Name");
											while($s=mysqli_fetch_array($sen))
											{
												$mic_s=explode("#MICValue#",$s[result]);
												$pn=mysqli_fetch_array(mysqli_query($link, "select Name from Parameter_old where ID='$s[paramid]'"));
												echo "<div style='display:inline-block;min-width:160px'>".$pn[Name]."</div> <br/>";
											}
											?>
									</td>
									<td valign="top" style="border-right:1px solid #CCC">
										<?php
											$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where a.patient_id='$uhid' AND a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch_no' and a.testid='$all_cult' and (a.result like 'I%' or a.result like 'i%') and a.paramid=b.ID order by b.Name");
											while($s=mysqli_fetch_array($sen))
											{
												$mic_i=explode("#MICValue#",$s[result]);
												$pn=mysqli_fetch_array(mysqli_query($link, "select Name from Parameter_old where ID='$s[paramid]'"));
												echo "<div style='display:inline-block;width:160px'>".$pn[Name]."</div> <br/>";
											}
											?>
									</td>
									<td valign="top" style="border-right:1px solid #CCC">
										<?php
											$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where a.patient_id='$uhid' AND a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch_no'  and a.testid='$all_cult' and ( a.result like 'R%' or a.result like 'r%') and a.paramid=b.ID order by b.Name");
											while($s=mysqli_fetch_array($sen))
											{
												$mic_r=explode("#MICValue#",$s[result]);
												$pn=mysqli_fetch_array(mysqli_query($link, "select Name from Parameter_old where ID='$s[paramid]'"));
												echo "<div style='display:inline-block;width:160px'>".$pn[Name]."</div><br/>";											}
											?>
									</td>
								</tr>
							</table>
						<?php
							}
							
							
							?>
						
						<div style='text-align:left;margin-left:20px'>
							<br/>
							<?php
								$pat_sum=mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and  testid='$all_cult' and main_tech>0");
								$num_pat=mysqli_num_rows($pat_sum);
								
								if($num_pat>0)
								{
									$pat_s=mysqli_fetch_array($pat_sum);
									echo $pat_s[summary];
								}
								else
								{
									$chk_sum=mysqli_query($link, "select * from test_summary where testid='$all_cult'");
									$num_sum=mysqli_num_rows($chk_sum);
									if($num_sum>0)
									{
										$summ_all=mysqli_fetch_array($chk_sum);
										//echo $summ_all[summary];
									}
								}
								?>
						</div>
					</div>
					
				</div>
			</div>
			</div>
		</div>
		<script>load_duplicate(3,<?php echo $dup3;?>)</script>
		
		<!-----Doctors Part----->
		
		<div class="doctors-container">		
			<div class="doctors" style="<?php echo $cls;?>">
				<?php
					$aprv_by=$d;
					$entry_by=$lab_doc[tech];
					$analysis_by=$res_time[main_tech];
					include('report_doctor_footer_pdf.php'); 
				?>
			</div>
		</div>
	</div>
		<!-----Doctors Part----->
	<?php
	$all_d++;
	}
	
	if($all_pad)
	{
		$al_p=explode("@",$all_pad);
		foreach($al_p as $ap)
		{
			if($ap)
			{
				$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$ap'"));
				$dep=mysqli_fetch_array(mysqli_query($link, "select name from test_department where id='$tname[type_id]'"));
				
				$auth_p=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap'"));

			if($all_d>0)
			{
			?>
				<div class="page_break"></div>
			<?php
			}
			?>
				<br/><br/><br/><br/><br/><br/><br/>
				<!--<img type="jpg/png/jpeg" src="../../images/pad_head.png" style="width:100%;height:80px;top:0px;" />-->
			<input type="hidden" id="chk_page" value="<?php echo $rp_page;?>"/>
		<div class="container-fluid">
			
			<div class="row">
				<?php
					//include("page_header.php");
				?>
				<div class="span10">
					<h3>Department of <?php echo $dep[name];?></h3>
					<div style="text-align:right;width:100%" class="dupl_4"></div>
					<?php
						
						$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='$ap')"));
						$phl=mysqli_fetch_array(mysqli_query($link, "select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap'"));
						if($phl)
						{
							$collection_date=$phl["date"];
							$collection_time=$phl["time"];
						}else
						{
							$collection_date=$reg["date"];
							$collection_time=$reg["time"];
						}
						//$lab=mysqli_fetch_array(mysqli_query($link, "select time,date from lab_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and testid='$ap'"));
						
						$res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap' limit 0,1"));
						//$cname=mysqli_fetch_array(mysqli_query($link, "select centreno,centrename,onLine from centremaster where centreno in(select centreno from patient_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and centreno!='C100')"));
						//$cname=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));
						if(!$res_time[date])
						{
							$res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap'"));	
						}
						
						$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$ap'"));
						 //$vcntr1=substr($cname[centrename],0,15);
						  $vcntr=$cname[name];
						  
						  $auth_p=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap'"));
						  
						  $user_pad=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID in(select user from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap')"));
						  $user_tech=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID in(select main_tech from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap')"));
						  
						  $equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$ap'"));
						  
						 						  
						   /*-------Report Header-------*/
							$sam1=$samp["Name"];
							$sample_name=rtrim($sam1, ",");
							include("report_patient_header_pdf.php");
												
						/*-------Report Header-------*/
						  
					?>
					<br/>
					<div style="height:610px;">
						<table class="table-condensed" width="100%">
							<tr>
								<td>
									<?php
										//$nbl_note=0;
										$nbl_star="";
										$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where testid='$tst'"));
										if($nbl_tst==0)
										{
											$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
											if($nabl[nabl]==1)
											{
												$nbl_star="*";
												$nbl_note=1;
											}
										}
										
										$chk_dup4=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
										if($chk_dup4>0)
										{
											$dup4=1;
										}
									?>
									<b>TEST NAME : <?php echo $nbl_star.$tname[testname];?></b>
								</td>
								
								
							</tr>
							<tr>
								<td>
								<div class="table-modifier">
									<?php
										$pat_sum=mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$ap'");
										$num_pat=mysqli_num_rows($pat_sum);
										
										if($num_pat>0)
										{
											$pat_s=mysqli_fetch_array($pat_sum);
											echo $pat_s[summary];
											$docid=$pat_s[doc];	
											$tech=mysqli_fetch_array(mysqli_query($link, "Select Name from Employee where ID='$pat_s[main_tech]'"));
										}
										else
										{
											$chk_sum=mysqli_query($link, "select * from test_summary where testid='$ap'");
											$num_sum=mysqli_num_rows($chk_sum);
											if($num_sum>0)
											{
												$summ_all=mysqli_fetch_array($chk_sum);
												echo $summ_all[summary];
											}
											
										}
										?>
									</div>	
								</td>
							</tr>
						</table>
						
					</div>
					
					<?php
						if($equip[equipment]>0)
						{
							
							$eq_det=mysqli_fetch_array(mysqli_query($link,"select * from lab_instrument_master where id='$equip[equipment]'"));
							echo "<p contenteditable='true' style='font-weight:bold;font-style:italic;text-align:center'>$eq_det[report_text]</p>";
							
						}
					?>
                  
				</div>
			</div>
		</div>
		
		<!-----Doctors Part----->
		
		<div class="doctors-container">		
			<div class="doctors" style="<?php echo $cls;?>">
				<?php
					$aprv_by=$d;
					$entry_by=$lab_doc[tech];
					$analysis_by=$lab_doc[main_tech];
					include('report_doctor_footer_pdf.php'); 
				?>
			</div>
	    </div>
	</div>
		<!-----Doctors Part----->
		
		
		<?php
				
		}
		}
		$all_d++;
	}
	echo "</div></div></div>";
	if($wid>0)
	{
		if($all_d>0)
		{
		?>
			<div class="page_break"></div>
		<?php
		}
		?>	
			
			<br/><br/><br/><br/><br/><br/><br/>
			<!--<img type="jpg/png/jpeg" src="../../images/pad_head.png" style="width:100%;height:80px;top:0px;" />-->
			<div class="container-fluid">
				
			<div class="row">
				<?php
					//include("page_header.php");
				?>
				<div class="span10">
					<h3>Department of Pathology</h3>
					<div style="text-align:right;width:100%" class="dupl_5"></div>
					<?php
						
						$auth_w=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='1227'"));
											
						$phl=mysqli_fetch_array(mysqli_query($link, "select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='1227'"));
						
						if($phl)
						{
							$collection_date=$phl["date"];
							$collection_time=$phl["time"];
						}else
						{
							$collection_date=$reg["date"];
							$collection_time=$reg["time"];
						}
						
						$res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' limit 1"));
						
						$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='1227')"));
						
						$user_wid=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID in(select v_User from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' )"));
						$user_wid_t=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID in(select main_tech from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' )"));
						
											
					 /*-------Report Header-------*/
							$sam1=$samp["Name"];
							$sample_name=rtrim($sam1, ",");
							include("report_patient_header_pdf.php");
												
					/*-------Report Header-------*/
					
					
						$tname=mysqli_fetch_array(mysqli_query($link, "select testname from testmaster where testid='$tst'"));
						
						$chk_dup5=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
						if($chk_dup5>0)
						{
							$dup5=1;
						}
						?>
					<p class="text-center"><b><u>Widal Test</u></b></p>
					<p>The antibody titre shows positivity to Salmonella antigen in the following dilutions :</p>
					<div style="height:550px;">
						<?php
							$w1=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=1"));
							$w2=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=2"));
							$w3=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=3"));
							$w4=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=4"));
							?>
						<table class="table-condensed" width="100%">
							<tr class="tr_border">
								<td><strong>Dilution:</strong></td>
								<td><strong>1:20</strong></td>
								<td><strong>1:40</strong></td>
								<td><strong>1:80</strong></td>
								<td><strong>1:160</strong></td>
								<td><strong>1:320</strong></td>
								<td><strong>1:640</strong></td>
							</tr>
							<tr>
								<td><strong>Antigen 'O'</strong></td>
								<td><?php echo $w1[F1]?></td>
								<td><?php echo $w1[F2]?></td>
								<td><?php echo $w1[F3]?></td>
								<td><?php echo $w1[F4]?></td>
								<td><?php echo $w1[F5]?></td>
								<td><?php echo $w1[F6]?></td>
							</tr>
							<tr>
								<td><strong>Antigen 'H'</strong></td>
								<td><?php echo $w2[F1]?></td>
								<td><?php echo $w2[F2]?></td>
								<td><?php echo $w2[F3]?></td>
								<td><?php echo $w2[F4]?></td>
								<td><?php echo $w2[F5]?></td>
								<td><?php echo $w2[F6]?></td>
							</tr>
							<tr>
								<td><strong>Antigen 'A(H)'</strong></td>
								<td><?php echo $w3[F1]?></td>
								<td><?php echo $w3[F2]?></td>
								<td><?php echo $w3[F3]?></td>
								<td><?php echo $w3[F4]?></td>
								<td><?php echo $w3[F5]?></td>
								<td><?php echo $w3[F6]?></td>
							</tr>
							<tr>
								<td><strong>Antigen 'B(H)'</strong></td>
								<td><?php echo $w4[F1]?></td>
								<td><?php echo $w4[F2]?></td>
								<td><?php echo $w4[F3]?></td>
								<td><?php echo $w4[F4]?></td>
								<td><?php echo $w4[F5]?></td>
								<td><?php echo $w4[F6]?></td>
							</tr>
							<tr>
								<td><strong>IMPRESSION</strong></td>
								<td colspan="6"><?php echo nl2br($w4[DETAILS]);?></td>
							</tr>
						</table>
						<br/>
						<?php
							$pat_sum=mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='1227'");
							$num_pat=mysqli_num_rows($pat_sum);
							
							if($num_pat>0)
							{
								$pat_s=mysqli_fetch_array($pat_sum);
								echo $pat_s[summary];	
							}
							else
							{
								$chk_sum=mysqli_query($link, "select * from test_summary where testid='1227'");
								$num_sum=mysqli_num_rows($chk_sum);
								if($num_sum>0)
								{
									$summ_all=mysqli_fetch_array($chk_sum);
									//echo $summ_all[summary];
								}
								
							}
							?>
					</div>
					
					<p class="text-center" style="margin-top: 10px;"><b>----End of report----</b></p>
				</div>
			</div>
		</div>
		<script>load_duplicate(5,<?php echo $dup5;?>)</script>
		
		<!-----Doctors Part----->
		
		<div class="doctors-container">		
		<div class="doctors" style="<?php echo $cls;?>">
			<?php
				$aprv_by=$d;
				$entry_by=$lab_doc[tech];
				$analysis_by=$lab_doc[main_tech];
				include('report_doctor_footer_pdf.php'); 
			?>
		</div>
	    </div>
		<!-----Doctors Part----->
		<?php
		
		
	}
	?>
			
	<?php
	$all_d++;
}
}
?>
<span id="user" style="display:none;"><?php echo $user; ?></span>
<script>load_page_no()</script>		
</body>
</html>
<style>
.container-fluid
{
	padding-left:5px;
}
h3 {
	margin: 0;
}
h4 {
    margin: 0;
}
.span10
{
	margin-left:50px;
}
@page
{
	margin-left:0cm;
	margin-right:0.7cm;
}
.cult_table
{
	margin-top:50px;
	margin-left:0px;
}

.cult_table td,.cult_table th
{
	padding:10px;
	font-size:15px;
	
}
</style>

<?php
	    
	$rg=str_replace("/","_",$opd_id);
	$gen_time=date('H_i_s');
	
	$file_name=$rg."_".$gen_time.".pdf";
	$m_chk="I"; //---ONly Generates--//
	$output = ob_get_contents();
	include("../../MPDF/mpdf60/mpdf.php");
	
	$payStub=new mPDF('c','A4','','',2,0,0,0,2,5); 
	
	
	
	
	$payStub->WriteHTML($output);
	ob_clean(); 
	//$payStub->Output($file_name, 'I');
	$payStub->Output($file_name, $m_chk);

	exit;
?>
