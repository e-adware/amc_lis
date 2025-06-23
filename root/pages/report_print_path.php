<?php error_reporting(0);?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="../../css/report.css" rel="stylesheet" type="text/css">
		<link href="../../css/loader.css" rel="stylesheet" type="text/css">
		<script src="../../js/jquery.js"></script> 
		<script>
			$(document).ajaxStop(function()
			{
				$("#loader").hide();
			});
			////////////block print////////////
			/*
			function check_online(e)
			{
			   var onl=document.getElementById("online_cen").value	;
			   var docaprvd=document.getElementById("docaprvd").value;
			    
			   if(docaprvd==0)
			   {
				   var unicode=e.keyCode? e.keyCode : e.charCode;
							if(e.ctrlKey==1)
							{
								if(unicode==112)
								{
									e.preventDefault();
									alert("Need Technician/Doctor Approval");	
									cls_hide();
								}
							}
			   }
					   
			      
				 else if(onl==1)
					 {
						var unicode=e.keyCode? e.keyCode : e.charCode;
							if(e.ctrlKey==1)
							{
								if(unicode==112)
								{
									e.preventDefault();
									alert("You can not print this page");	
									cls_hide();
								}
							}
					   }
				 
			}
	        */
			function cls_hide()
			{
			  window.opener.hide_div();
			  window.opener.document.getElementById("new").focus();
			  window.close();
			}
            //////////block end//////////////////
    
			function load_normal(uhid,param,val,no)
			{
				$("#loader").show();
				$.post("pathology_normal_range_new.php",
				{
					uhid:uhid,
					param:param,
					val:val
				},
				function(data,satus)
				{
				
					var data=data.split("#");
					
					$("#norm_r"+no).html("<div style='margin-left:5px;font-size:11px;'>"+data[0]+"</div>");
					
					
					if(data[1]=="Error")
					{
						//$("#chk_res"+no).text("*");
					}
				})
			}
			function save_print_test(tst,opd,ipd,uhid,batch_no)
			{
				window.close();
				$.post("report_print_path_save.php",
				{
					tst:tst,
					opd_id:opd,
					ipd_id:ipd,
					uhid:uhid,
					batch_no:batch_no,
					type:"sing"
				},
				function(data,status)
				{
					
					if($("#chk_page").val()>0)
					{
						//var reg=uhid+"@"+visit;
						//var reg=uhid;
						//window.opener.load_pat_info(reg,'update_p_sim');
					}
					else
					{
						//window.opener.load_test_detail(data);
					}
					
				})
			}
			function close_window(e)
			{
				var unicode=e.keyCode? e.keyCode : e.charCode;
				
				if(unicode==27)
				{
					window.close();
				}
			}
			function load_page_no()
			{
				var p=$(".page_no");
				for(var i=0;i<p.length;i++)
				{
					var n=i+1;
					$(p[i]).html("Page "+n+" of "+p.length+"");
				}
			}
		</script>
		<style>
		.page_no{ position:absolute;right:0px;font-weight:bold;font-style:italic;margin-top:20px}
		#t_bold td{ font-size:10px}
		*{ font-size:14px !important;}
		</style>
	</head>
	<body onkeypress="close_window(event)" onafterprint="save_print_test('<?php echo $_GET['tstid'];?>','<?php echo $_GET['opd_id'];?>','<?php echo $_GET['ipd_id'];?>','<?php echo $_GET['uhid'];?>','<?php echo $_GET['batch_no'];?>')">
	
	<div id="loader" style="display:none"></div>
	
		<?php
			include("../../includes/connection.php");
			include("pathology_normal_range_new.php");
			
			$uhid=$_GET['uhid'];
			$opd_id=$_GET['opd_id'];
			$ipd_id=$_GET['ipd_id'];
			$batch_no=$_GET['batch_no'];
			$tst=$_GET['tstid'];
			
			$nb_text_patho=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='1' "));
						
			$rp_page=0;
			if($_GET['rp_page']>0)
			{
				$rp_page=$_GET['rp_page'];
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
			
			$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$tst'"));
			$meth=mysqli_fetch_array(mysqli_query($link, "select * from test_methods where id='$tname[method]'"));
			
			$lab_no=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and paramid='1196'"));
			
			$lab_doc=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
			
			$docid=$lab_doc['doc'];
			
			$lab_doc_doc=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
			if(!$lab_doc['doc'])
			{
				$docid=$lab_doc_doc['doc'];
			}
			if($lab_doc)
			{
				$completion_date=$lab_doc["date"];
				$completion_time=$lab_doc["time"];
			}else
			{
				$completion_date=$lab_doc_doc["date"];
				$completion_time=$lab_doc_doc["time"];
			}
			//$lis=mysqli_num_rows(mysqli_query($link,"select * from test_sample_result where reg_no='$reg[reg_no]' and testid='$tst' and result>0"));
			$lis=0;					
			if($lis>0)
			{
				$l_user="LIIS";
				
			}
			else
			{
				if($lab_doc['tech'])
				{
					$l_us_name=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$lab_doc[tech]'"));
					
				}
				else
				{
					$l_us_name=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$lab_doc[user]'"));
				}
				$l_user=$l_us_name['Name'];
			}
			
			
			
			$tech=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$lab_doc[main_tech]'"));
			
			
			
			$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='$tst')"));
			
			$dep=mysqli_fetch_array(mysqli_query($link, "select name from test_department where id='$tname[type_id]'"));
			
			$user=$_GET['user'];
			$uname=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$user'"));
			
			$equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$tst'"));
			
			//include("page_header.php");
			
			?>
			
			<br/><br/><br/><br/><br/><br/>
		<input type="hidden" id="chk_page" value="<?php echo $rp_page;?>"/>
		<div class="container-fluid">
			<div class="row">
				<div class="span10">
					<h3><?php echo $dep['name'];?></h3>
					<?php
						$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));
						
						//$bill=mysqli_fetch_array(mysqli_query($link, "select * from payment_detail where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' "));
						$doc=mysqli_fetch_array(mysqli_query($link, "select refbydoctorid,ref_name,qualification from refbydoctor_master where refbydoctorid in( SELECT `refbydoctorid` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$v_id' )"));
						
						if($doc['refbydoctorid']!="101")
						{
							$dname=$doc['ref_name']." , ".$doc['qualification'];
						}
						else
						{
							$dname="Self";
						}
						
						//$cname=mysqli_fetch_array(mysqli_query($link, "select centreno,centrename,onLine,short_name from centremaster where centreno in(select centreno from patient_details where patient_id='$uhid' and visit_no='$visit' and centreno!='C100')"));
						//$cname=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));
						//echo "select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'";
						$phl=mysqli_fetch_array(mysqli_query($link, "select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
						if($phl)
						{
							$collection_date=$phl["date"];
							$collection_time=$phl["time"];
						}else
						{
							$collection_date=$reg["date"];
							$collection_time=$reg["time"];
						}
						//$lab=mysqli_fetch_array(mysqli_query($link, "select time,date from lab_sample where patient_id='$uhid' and visit_no='$visit' and testid='$tst'"));
						
						//$authnticate=mysqli_fetch_array(mysqli_query($link, "select t_time,t_date,d_time,d_date from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and testid='$tst' limit 0,1"));
						
						$res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
						
						if(!$res_time['date'])
						{
						 $res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
						}
						
						function convert_date($date)
						{
							 if($date)
							 {
								 $timestamp = strtotime($date); 
								 $new_date = date('d-M-Y', $timestamp);
								 return $new_date;
							 }
						}
						// Time format convert
						function convert_time($time)
						{
							$time = date("g:i A", strtotime($time));
							return $time;
						}
						 $vcntr1=substr($cname[short_name],0,15);
						 $vcntr="<b>$vcntr1</b>";
						  
						 // $id_pref=mysqli_fetch_array(mysqli_query($link,"select * from id_setup limit 1"));	
						 
						
						
						 /*-------Report Header-------*/
							$uhid_id=$uhid;
							$sample_name=$samp['Name'];
							include("report_patient_header.php");
												
						/*-------Report Header-------*/ 
						
						
						?>
					
					<?php
						?>
					<div style="min-height:600px">
						<table class="table borderless">
							<?php
								$test_rs=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
								if($test_rs>0)
								{
									$note="";
									$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
									if($pat_note['note'])
									{
										$note=$pat_note['note'];
									}
									
									
								?>
							<tr id='t_bold'>
								<td width="42%">TEST</td>
								<td colspan="3" width="22%">RESULTS</td>
								<td><?php if(!$lab_no[result]){ echo "BIOLOGICAL REF.INTERVAL";}?></td>
							</tr>
							<tr><td colspan="3" style="height:10px"></td></tr>
							<?php
							$instr=mysqli_fetch_array(mysqli_query($link,"select * from test_instr where depid='$tname[type_id]'"));
							
							if($instr[instr_text])
							{
								echo "<tr><td colspan='6' style='border-bottom:1px solid #CCC !important;' contenteditable='true'>$instr[instr_text]</td></tr>";
								echo "<tr><td colspan='6' style='height:10px'></td></tr>";
							}
							?>
							
							<?php
								//$nbl_note=0;
								$nbl_star="";
								$nbl_star_par="";
														
							
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$tst'"));
								if($tot_par>1)
								{
								?>
							<tr>
								<?php
								
								$nbl_val=mysqli_fetch_array(mysqli_query($link, "select * from nabl"));
								if($nbl_val["nabl"]>0)
								{
									$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$tst'"));
									 if($nbl_test>0)
									 {
										 $nbl_star="*";
										 $nbl_note=1;
										 
										 $nbl_note_test=1;
									 }
								 }
								?>
								<td colspan='6'>
									
										<b><?php echo $nbl_star.$tname[testname];?></b>
										
									
								</td>
							</tr>
							<?php
								}
								else
								{
									if($tst=="921")
									{
										$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$tst' and paramid='0'"));
										 if($nbl_test>0)
										 {
											 $nbl_star="*";
											 $nbl_note=1;
											 
											 $nbl_note_test=1;
										 }
										echo "<tr><th colspan='6'>$nbl_star $tname[testname]</th></tr>";
									}
								}
								
								}
								else
								{
								 //if($tst=="1131"){ $nbl_star="*"; $nbl_note=1;}else { $nbl_star="";}
								 
								
								 $nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$tst' and paramid='0'"));
								 if($nbl_test>0)
								 {
									 $nbl_star="*";
									 $nbl_note_test=1;
								 }
								 echo "<tr><th colspan='6'>$nbl_star $tname[testname]</th></tr>";
								 //$tname=mysql_fetch_array(mysql_query("select testname"));
								}
								 
								 
								 
								 $i=1;
								 $j=1;
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
								 
								/*-------------------------Check Double Page-----------------------*/ 
								$chk_page=19;
								if($tst==33)
								{
									$chk_page=19;
								}
								 
								 if($j>$chk_page)
								 { $j=1;?>
						</table>
						<div class="text-center" style="margin-top: 5px;position:absolute;right:40%"><b>----Continued on to next page----</b></div>
						<div class="page_no" style=""></div>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="page_break"></div>
		<div class="container-fluid">
			<div class="row">
				<br/><br/><br/>
				<div class="col-md-12">
					<h3><?php echo $dep['name'];?></h3>
					<?php
					/*-------Report Header-------*/
						
							include("report_patient_header.php");
						
						
					/*---------------------------*/
					?>
					<div style="min-height:530px">
						<table class="table borderless">
							<?php
								$test_rs=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
								if($test_rs>0)
								{
								?>
							<tr id='t_bold'>
								<td width="20%">TEST</td>
								<td colspan="3">RESULTS</td>
								<td><?php if(!$lab_no['result']){ echo "BIOLOGICAL REF.INTERVAL";}?></td>
								<td>METHOD</td>
							</tr>
							<?php
								$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$tst'"));
								if($tot_par>1)
								{
								?>
							<tr>
								<th colspan='6' style="padding-bottom:10px"><?php echo $tname['testname'];?></th>
							</tr>
							<?php
								}
								else
								{
								
								
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
								 $t_res=mysqli_fetch_array($res);
								 
								 $meth_name=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id='$pn[method]'"));
								 ?>
							<?php
								$par_class="";
								if($pn[ResultType]==8)
								{
								$par_class="tname";
								}
								else
								{
								$par_class="";
								}
								?>
							<tr class="tr_test">
								<?php
									$nbl_star="";
									$nres=$t_res[result];
									if(strlen($nres)<15)
									{
										
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
										if($tot_par>1)
										{
											$test_param_name=$pn[Name];
											$sty="";
										}
										else
										{
											$test_param_name=$tname[testname];
											$sty="font-weight:bold !important;padding-left:-10px !important;";
										}	
										
										
										$nr=load_normal($uhid,$p[ParamaterId],$t_res['result']);
										$nr1=explode("#",$nr);
										$norm_range=$nr1[0];																						
									?>
								<td class="<?php echo $par_class;?>" valign="top" style="<?php echo $sty;?>">
									<?php echo $nbl_star.$test_param_name;?>
								</td>
								
								<td valign="top" id="result<?php echo $i;?>" colspan="2" >
									<?php
									
									if($nr1[1]=="Error")
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
								
								<td id="norm_r<?php echo $i;?>" style='text-align:left;'>
									<div style="display:inline-block;margin-left:30px;">
									<?php
										echo $norm_range;
										
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
									?>
								<td width="7%" valign="top" class="<?php echo $par_class;?>">
									<?php echo $nbl_star.$pn[Name];?>
								</td>
								<td width="1%" valign="top" style="text-align:right"><b><span id="chk_res<?php echo $i;?>">&nbsp;</span></b></td>
								<?php
									if($meth_name[name])
									{
									?>
								<td colspan="3" valign="top">
									<?php echo nl2br($t_res[result]);?>
								</td>
								<td>
									<?php echo $meth_name[name];?>
								</td>
								<?php
									}
									else
									{
									?>
								<td colspan="4" valign="top">
									<?php echo nl2br($t_res[result]);?>
								</td>
								<?php
									}
									
									}
									?>
							</tr>
							<?php
								$i++;
								$j++;
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
							 
									echo "<tr><td colspan='7' style='text-align:left;padding-left:20px !important' ><b>$nbl_star$pn[Name]</b></td></tr>";
									*/ 
									echo "<tr><td colspan='6' style='text-align:left;padding-left:0px !important' ><b>$pn[Name]</b></td></tr>";
								}
								}
								
								?>
						</table>
					
					<div class="table-modifier">
						<?php
							$pat_sum=mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'");
							$num_pat=mysqli_num_rows($pat_sum);
							
							if($num_pat>0)
							{
							 $pat_s=mysqli_fetch_array($pat_sum);
							 echo $pat_s[summary];
							}
							else
							{
							 $if_sum=mysqli_num_rows(mysqli_query($link, "select * from summary_check where testid='$tst'"));
								 if($if_sum==0)
								 {
								 $chk_sum=mysqli_query($link, "select * from test_summary where testid='$tst'");
								 $num_sum=mysqli_num_rows($chk_sum);
								 if($num_sum>0)
								 {
								 $summ_all=mysqli_fetch_array($chk_sum);
								 echo $summ_all[summary];
 								 }
								}
							 
							}
							if($tst==515)
							{
								?><div align="center">
									
								</div> <?php
							}
							?>
						
					<?php
						
						if($note)
						{
							echo "<b><i><u>*Note : $note</u></i></b>";
							echo "<p class='text-center' style='margin-top: 5px;'><b>----End of report----</b></p>";
						}
						else
						{
							echo "<p class='text-center' style='margin-top: 20px;'><b>----End of report----</b></p>";
						}
						
						if($nbl_note_test==1){ 
							
							$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
							 $nbl_note=1;}
							 else { }
						
						
					?>
					</div>
					
					
					
					</div>
					
					<div class="page_no" style=""></div>
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
		<div class="doctors" style="position:absolute;bottom:-50px">
			<?php
				$aprv_by=$d;
				$entry_by=$lab_doc[tech];
				$analysis_by=$lab_doc[main_tech];
				include('report_doctor_footer.php'); 
			?>
		</div>
	    </div>
		<!-----Doctors Part----->
		
		
	</body>
</html>
<style>
h3 {
	margin: 0;
}
h4 {
    margin: 0;
}
@page
{
	margin-left:0.3cm;
	margin-right:0.7cm;
}
</style>

<script>//window.print()</script>
