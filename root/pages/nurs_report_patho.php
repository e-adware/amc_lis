<?php
$uhid=$_POST["uhid"];
$opd_id=$_POST["opd"];
$ipd_id=$_POST["ipd"];
$batch_no=$_POST["batch"];
$tst=$_POST["testid"];

?>
	<div id="loader" style="display:none"></div>
	
		<?php
			include("../../includes/connection.php");
			include("pathology_normal_range_new.php");
						
			$rp_page=0;
			if($_GET['rp_page']>0)
			{
				$rp_page=$_GET['rp_page'];
			}
			
			$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testmaster where testid='$tst'"));
			$meth=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_methods where id='$tname[method]'"));
			
			$lab_no=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and paramid='1196'"));
			
			$lab_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
			
			$docid=$lab_doc['doc'];
			
			if(!$lab_doc['doc'])
			{
				$lab_doc_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
				$docid=$lab_doc_doc['doc'];
			}
			
			
			
			
			//$lis=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from test_sample_result where reg_no='$reg[reg_no]' and testid='$tst' and result>0"));
			$lis=0;					
			if($lis>0)
			{
				$l_user="LIIS";
				
			}
			else
			{
				if($lab_doc['tech'])
				{
					$l_us_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Employee where ID='$lab_doc[tech]'"));
					
				}
				else
				{
					$l_us_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Employee where ID='$lab_doc[user]'"));
				}
				$l_user=$l_us_name['Name'];
			}
			if($tst!="1227")
			{
			
			?>
			<!--<br/><br/><br/><br/><br/>-->
		<input type="hidden" id="chk_page" value="<?php echo $rp_page;?>"/>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php
						
						$res_time=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
						
						if(!$res_time['date'])
						{
						 $res_time=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
						}
						
						
						 $vcntr1=substr($cname[short_name],0,15);
						 $vcntr="<b>$vcntr1</b>";
						  
						 // $id_pref=mysqli_fetch_array(mysqli_query($link,"select * from id_setup limit 1"));	
						  
						?>
					
					<div style="min-height:500px">
						<table class="table borderless">
							<?php
								$test_rs=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
								if($test_rs>0)
								{
									$note="";
									$pat_note=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select note from testresults_note where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
									if($pat_note['note'])
									{
										$note=$pat_note['note'];
									}
									
									
								?>
							<tr id='t_bold'>
								<th width="22%">TEST</th>
								<!--<th>METHOD</th>-->
								<th colspan="3">RESULTS</th>
								<th><?php if(!$lab_no['result']){ echo "BIOLOGICAL REF.INTERVAL";}?></th>
							</tr>
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
														
							
								$tot_par=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from Testparameter where TestId='$tst'"));
								if($tot_par>1)
								{
								?>
							<tr>
								<?php
								$nbl_test=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from nabl_logo where testid='$tst'"));
								 if($nbl_test>0)
								 {
									 $nbl_star="*";
									 $nbl_note=1;
									 
									 $nbl_note_test=1;
								 }
								?>
								<td colspan='6'>
									<?php
									if($tst==1692)
									{
										?><b>*FLUID FOR ANALYSIS</b><?php
									}
									else
									{
										?>
										<b><?php echo $nbl_star.$tname[testname];?></b>
										<?php
									}
									?>
									
								</td>
							</tr>
							<?php
								}
								else
								{
									if($tst=="921")
									{
										$nbl_test=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from nabl_logo where testid='$tst' and paramid='0'"));
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
								 
								
								 $nbl_test=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from nabl_logo where testid='$tst' and paramid='0'"));
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
								 $param=mysqli_query($GLOBALS["___mysqli_ston"], "select * from Testparameter where TestId='$tst' order by sequence"); 
								 while($p=mysqli_fetch_array($param))
								 {
								 
								 $pn=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from Parameter_old where ID='$p[ParamaterId]'"));
								 if($pn[ResultType]!=0)
								 {
								 $res=mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and paramid='$p[ParamaterId]'");
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
				<!--<br/><br/><br/>-->
				<div class="col-md-12">
					<div style="min-height:530px">
						<table class="table borderless">
							<?php
								$test_rs=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
								if($test_rs>0)
								{
								?>
							<tr id='t_bold'>
								<th width="20%">TEST</th>
								<!--<th colspan="3">RESULTS</th>-->
								<th><?php if(!$lab_no['result']){ echo "BIOLOGICAL REF.INTERVAL";}?></th>
								<th>METHOD</th>
							</tr>
							<?php
								$tot_par=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from Testparameter where TestId='$tst'"));
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
								 
								 $p_unit=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select unit_name from Units where ID='$pn[UnitsID]'"));
								 $meth=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select name from test_methods where id in(select method_id from parameter_method where param_id='$p[ParamaterId]')"));
								 $t_res=mysqli_fetch_array($res);
								 
								 $meth_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select name from test_methods where id='$pn[method]'"));
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
											$nbl_tst=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from nabl_logo where paramid='$pn[ID]'"));
											if($nbl_tst==0)
											{
												
												$nabl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from nabl"));
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
									?>
								<td class="<?php echo $par_class;?>" valign="top" style="<?php echo $sty;?>"><?php echo $nbl_star.$test_param_name;?></td>
								<!--<td><?php echo $meth_name['name'];?></td>-->
								<td width="1%" valign="top" style="text-align:right"><b><span id="chk_res<?php echo $i;?>">&nbsp;</span></b></td>
								<td valign="top" id="result<?php echo $i;?>" colspan="2" ><span class="res_size"><b><?php echo nl2br($t_res['result']);?></b></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $p_unit['unit_name'];?></td>
								
								<td id="norm_r<?php echo $i;?>" style='text-align:left;'>
									<?php
									$nr=load_normal($uhid,$p[ParamaterId],$t_res['result'],0);
									$nr1=explode("#",$nr);
									
									if($nr1[1]=="Error")
									{
										echo "<script>$('#chk_res'+".$i."+'').text('*')</script>";
									}
									
									$norm_range=$nr1[0];
									
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
										$nbl_tst=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											
											$nabl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from nabl"));
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
								<!--<td>
									<?php echo $meth_name[name];?>
								</td>-->
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
										$nbl_tst=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from nabl_logo where paramid='$pn[ID]'"));
										if($nbl_tst==0)
										{
											
											$nabl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from nabl"));
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
							$pat_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'");
							$num_pat=mysqli_num_rows($pat_sum);
							
							if($num_pat>0)
							{
							 $pat_s=mysqli_fetch_array($pat_sum);
							 echo $pat_s[summary];
							}
							else
							{
							 $if_sum=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from summary_check where testid='$tst'"));
								 if($if_sum==0)
								 {
								 $chk_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_summary where testid='$tst'");
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
					</div>
					
					</div>
				</div>
			</div>
		</div>
		
<?php
			}else
			{
		?>
				<!--<br/><br/><br/><br/><br/>-->
				<div class="container-fluid">
					
				<div class="">
					<?php
						
					?>
						
						<div style="text-align:right;width:100%" class="dupl_5"></div>
						
						<?php
							$tname=mysqli_fetch_array(mysqli_query($link, "select testname from testmaster where testid='$tst'"));
							
							$chk_dup5=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
							if($chk_dup5>0)
							{
								$dup5=1;
							}
							?>
						<p class="text-center"><b><u>Widal Test</u></b></p>
						<div style="min-height:600px;">
							<?php
								$w1=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=1"));
								$w2=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=2"));
								$w3=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=3"));
								$w4=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=4"));
								?>
							<table class="table table-condensed table-bordered">
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
										echo $summ_all[summary];
									}
									
								}
								?>
						</div>
						
						<p class="text-center" style="margin-top: 10px;"><b>----End of report----</b></p>
					</div>
				</div>
			</div>
		<?php
			}
?>
