<?php
	include("../../includes/connection.php");
	include("pathology_normal_range_new.php");
	
	
	if($_POST[type]==1)
	{
	
	$reg=$_POST[reg];
	
	$uhid=$_POST[pid];	
	$visit=$_POST[visit];
	$val=$_POST[val];
	$dep=$_POST[dep];
	$user=$_POST[user];
	
	
	$reg_no=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_reg_details where patient_id='$uhid' and visit_no='$visit'"));
	
	$reg=$reg_no[reg_no];
	
	$pinfo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_info where patient_id='$uhid'"));
	
	?>
<div id="tinfo" style="display:none;position:relative;" tabindex="0" onkeypress="aprv_onkey(event)">
<div id="aprv_all">
	<table class="table table-bordered">
		<tr>
			<th colspan="2">
				Name: <?php echo $pinfo[name];?>
			</th>
		</tr>
		<tr>
			<th colspan="2">
				Age/Sex: <?php echo $pinfo[age]." ".$pinfo[age_type]." / ".$pinfo[sex];?>
				<input type="hidden" id="patient_id" value="<?php echo $uhid;?>"/>
				<input type="hidden" id="visit_no" value="<?php echo $visit;?>"/>
			</th>
		</tr>
	</table>
	<?php	
		$j_test=1;
		//$qry=mysqli_query($GLOBALS["___mysqli_ston"],"select distinct testid from phlebo_sample where patient_id='$uhid' and visit_no='$visit' and testid in(select testid from testmaster where type_id='$dep') order by slno");
		$qry=mysqli_query($GLOBALS["___mysqli_ston"],"select distinct a.testid from phlebo_sample a,testmaster b where a.patient_id='$uhid' and a.visit_no='$visit' and a.testid=b.testid and b.type_id='$dep'  order by a.slno");
		while($t_q=mysqli_fetch_array($qry))
		{
			$tst=$t_q[testid];
			$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testmaster where testid='$tst'"));
			
			if($tst==1227)
			{
				$wid_chk=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from widalresult where patient_id='$uhid' and visit_no='$visit' limit 1"));
				$w_chk="";
				$w_text="Approve";
				if($wid_chk[doc]>0)
				{
					$w_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select Name from Employee where ID='$wid_chk[doc]'"));
					$w_chk="Checked";
					$w_text="(".$w_name[Name].")";
				}
				?>
	<table class="table table-bordered">
		<tr id='t_bold'>
			<td width="20%">TEST</td>
			<td colspan="3" width="25%">RESULTS</td>
			<td width="20%"><?php if(!$lab_no[result]){ echo "REF. RANGE";}?></td>
			<td>METHOD</td>
			<td style="text-align:center"><input type="checkbox" id="aprv_<?php echo $j_test;?>" name="" onclick="approve(<?php echo $j_test;?>,3,0)" class="aprv_check" <?php echo $w_chk;?>/><label><span></span><i><?php echo $w_text;?></i></label></td>
		</tr>
		<tr>
			<td colspan="7">
				<p class="text-left"><b><u>Widal Test</u></b></p>
				<div style="min-height:100px;">
					<?php
						$w1=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where patient_id='$uhid' and visit_no='$visit' and slno=1"));
						$w2=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where patient_id='$uhid' and visit_no='$visit' and slno=2"));
						$w3=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where patient_id='$uhid' and visit_no='$visit' and slno=3"));
						$w4=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where patient_id='$uhid' and visit_no='$visit' and slno=4"));
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
						$pat_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where patient_id='$uhid' and visit_no='$visit' and testid='$tst'");
						$num_pat=mysqli_num_rows($pat_sum);
						
						if($num_pat>0)
						{
							$pat_s=mysqli_fetch_array($pat_sum);
							echo $pat_s[summary];	
						}
						else
						{
							$chk_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_summary where testid='$tst'");
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
	<?php
		$j_test++;
		}
		else
		{
		$res_chk=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and doc>0"));
		$tot_sum=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_test_summary where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and doc>0"));
		
		
		if($res_chk>0 || $tot_sum>0)
		{
			//$a_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select ID,Name from Employee where ID in(select doc from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst')"));
			$a_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"SELECT DISTINCT a.ID, a.Name FROM Employee a, testresults b WHERE b.patient_id = '$uhid' AND b.visit_no = '$visit' AND b.testid = '$tst' AND a.ID = b.doc "));
			$checked="Checked";	
			$txt="".$a_doc[Name];
			
			if($user!=$a_doc[ID])
			{
				$checked.=" Disabled";	
			}
			
		}
		else
		{
			$checked="";		
			$txt="Approve";
			
		}
		
		/*		
		if($res_chk>0 || $tot_sum>0)
		{
		*/	
			$pos=0;
			if (strpos($tname[testname],'culture') !== false) 
			{
				$pos=2;
			}
		
			if (strpos($tname[testname],'CULTURE') != false) 
			{
				$pos=2;
			}
		
			if (strpos($tname[testname],'Culture') != false) 
			{
				$pos=2;
			}
			
			
			$tot_par=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from Testparameter where testid='$tst'"));
			if($tot_par==1)
			{
				$chk_p=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from Parameter_old where ID in(select ParamaterId from Testparameter where TestId='$tst')"));
				if($chk_p[ResultType]==7)
				{
					$pos=3;
					
				}
			}
			
			$chk_summary=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_test_summary where patient_id='$uhid' and visit_no='$visit' and testid='$tst'"));
			if($chk_summary>0)
			{
				$chk_tes=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst'"));
				if($chk_tes==0)
				{
					$pos=3;			
				}
			}
			
			
			if($pos==3)
			{
				?>
	<input type="hidden" id="test_id_<?php echo $j_test;?>" value="<?php echo $tst;?>"/>
	<table class="table table-bordered">
		<tr id='t_bold'>
			<td width="20%">TEST</td>
			<td colspan="3" width="25%">RESULTS</td>
			<td width="20%"><?php if(!$lab_no[result]){ echo "REF. RANGE";}?></td>
			<td>METHOD</td>
			<td style="text-align:center">
				<?php
					//$chk_b=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst'"));
					$chk_b1=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_test_summary where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and doc>0"));
					
					if($chk_b1>0)
					{
						//$sum_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select Name from Employee where ID in(select doc from patient_test_summary where patient_id='$uhid' and visit_no='$visit' and testid='$tst')"));
						$sum_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select distinct a.Name from Employee a,patient_test_summary b where b.patient_id='$uhid' and b.visit_no='$visit' and b.testid='$tst' and a.ID=b.doc"));
						$txt_sm="".$sum_name[Name];
						$sum_chk="Checked";
					}
					else
					{
						$txt_sm="Approve";
						$sum_chk="";
					}
					?>
				<input type="checkbox" id="aprv_<?php echo $j_test;?>" name="" onclick="approve(<?php echo $j_test;?>,2,<?php echo $val;?>)" class="aprv_check" <?php echo $sum_chk;?>/><label><span></span><i>(<?php echo $txt_sm;?>)</i></label>
			</td>
		</tr>
		<tr>
			<td colspan="7">
				<table class="table borderless">
					<tr>
						<td width="15%"><b>TEST NAME</b></td>
						<td>:</td>
						<td><b><?php echo $tname[testname];?></b></td>
					</tr>
					<tr>
						<td><b>RESULT</b></td>
						<td>:</td>
						<td>
							<?php
								$pat_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where patient_id='$uhid' and visit_no='$visit' and testid='$tst'");
								$num_pat=mysqli_num_rows($pat_sum);
								
								if($num_pat>0)
								{
									$pat_s=mysqli_fetch_array($pat_sum);
									echo $pat_s[summary];	
								}
								
								?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php
		$j_test++;
		}
		else if($pos==2)
		{
		//$en_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select Name from Employee where ID in(select main_tech from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst')"));
		$en_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"SELECT DISTINCT a.Name FROM Employee a, testresults b WHERE b.patient_id = '$uhid' AND b.visit_no = '$visit' AND b.testid = '$tst' AND a.ID = b.main_tech"));
		?>
	<div>
		<input type="hidden" id="test_id_<?php echo $j_test;?>" value="<?php echo $tst;?>"/>
		<table class="table table-bordered">
			<tr id='t_bold'>
				<td width="20%">TEST</td>
				<td colspan="3" width="25%">RESULTS</td>
				<td width="20%"><?php if(!$lab_no[result]){ echo "REF. RANGE";}?></td>
				<td>METHOD</td>
				<td style="text-align:center"><i><u>(<?php echo $en_name[Name];?>)</u></i></td>
				<td style="text-align:center">
					<?php
						$chk_b=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst'"));
						$chk_b1=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_test_summary where patient_id='$uhid' and visit_no='$visit' and testid='$tst'"));
						
						if($chk_b>0 || $chk_b1>0)
						{
						?>
					<input type="checkbox" id="aprv_<?php echo $j_test;?>" name="" onclick="approve(<?php echo $j_test;?>,1,<?php echo $val;?>)" class="aprv_check" <?php echo $checked;?>/><label><span></span><i>(<?php echo $txt;?>)</i></label>
					<?php
						}
						?>
				</td>
			</tr>
			<tr>
				<td colspan="8">
					<?php
						$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$tst'"));
						$spec1=explode(" ",$tname[testname]);
						$spec_s=sizeof($spec1);
						$spec=array_pop($spec1);
						
						$col=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and paramid='311'"));
						$num=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and paramid='313'"));
						 
						?>
					<div>
						<b><u><?php echo $tname[testname];?></u></b>
					</div>
					<div style="min-height:120px;margin-top:10px;margin-left:50px;">
						<?php
							if($num<1)
							{
								$fung=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and paramid='310'"));
								
							?>
						<table width='100%'>
							<tr>
								<th>Specimen</th>
								<td>: <?php echo $samp[Name];?></td>
							</tr>
							<tr>
								<th>Result</th>
								<td contentEditable="true">: Aerobic culture at 37&#176; C for 48 hrs reveals growth of <?php echo $fung[result];?></td>
							</tr>
							<tr>
								<th>Colony Count</th>
								<td>:
									<?php
										echo $col[result];
										$power=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and paramid='312'"));
										if($power[result])
										{
										echo "<sup>$power[result]</sup>";	
										}
										?> 
								</td>
							</tr>
						</table>
						<br/><br/>
						<table width="100%">
							<tr>
								<th contentEditable="true" width="33%">SENSITIVE</th>
								<th contentEditable="true" width="33%">INTERMEDIATE</th>
								<th contentEditable="true" width="33%">RESISTANT</th>
							</tr>
							<tr>
								<td valign="top">
									<?php
										$sen=mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and result='S' order by slno");
										while($s=mysqli_fetch_array($sen))
										{
											$pn=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Parameter_old where ID='$s[paramid]'"));				
											echo $pn[Name]."<br/>";
										}
										?>
								</td>
								<td valign="top">
									<?php
										$sen=mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and result='I'  order by slno");
										while($s=mysqli_fetch_array($sen))
										{
											$pn=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Parameter_old where ID='$s[paramid]'"));				
											echo $pn[Name]."<br/>";
										}
										?>
								</td>
								<td valign="top">
									<?php
										$sen=mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and result='R' order by slno");
										while($s=mysqli_fetch_array($sen))
										{
											$pn=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Parameter_old where ID='$s[paramid]'"));				
											echo $pn[Name]."<br/>";
										}
										?>
								</td>
							</tr>
						</table>
						<?php
							}
							else
							{
							?>
						<div style='text-align:left;margin-left:20px'>
							<br/>
							<?php
								$pat_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where patient_id='$uhid' and visit_no='$visit' and testid='$tst'");
										$num_pat=mysqli_num_rows($pat_sum);
									
										if($num_pat>0)
										{
											$pat_s=mysqli_fetch_array($pat_sum);
											echo $pat_s[summary];	
										}
										else
										{
											$chk_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_summary where testid='$tst'");
											$num_sum=mysqli_num_rows($chk_sum);
											if($num_sum>0)
											{
												$summ_all=mysqli_fetch_array($chk_sum);
												echo $summ_all[summary];
											}
											
										}
								?>
						</div>
						<?php
							}
							?>
					</div>
				</td>
			</tr>
		</table>
		<?php
			$j_test++;
			}
			else
			{
			?>
		<input type="hidden" id="test_id_<?php echo $j_test;?>" value="<?php echo $tst;?>"/>
		<table class="table table-bordered">
			<?php
				$test_rs=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst'"));
				//$en_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select Name from Employee where ID in(select main_tech from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst')"));
				$en_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"SELECT DISTINCT a.ID, a.Name FROM Employee a, testresults b WHERE b.patient_id = '$uhid' AND b.visit_no = '$visit' AND b.testid = '$tst' AND a.ID = b.main_tech"));
				/*/
				if($test_rs>0)
				{ */
				?>
			<tr id='t_bold'>
				<td width="20%">TEST</td>
				<td colspan="3" width="25%">RESULTS</td>
				<td width="20%"><?php if(!$lab_no[result]){ echo "REF. RANGE";}?></td>
				<td>METHOD</td>
				<td style="text-align:center"><i><u>(<?php echo $en_name[Name];?>)</u></i></td>
				<td style="text-align:center">
					<?php
						$chk_b=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst'"));
						$chk_b1=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_test_summary where patient_id='$uhid' and visit_no='$visit' and testid='$tst'"));
						
						if($chk_b>0 || $chk_b1>0)
						{
						?>
					<input type="checkbox" id="aprv_<?php echo $j_test;?>" name="" onclick="approve(<?php echo $j_test;?>,1,<?php echo $val;?>)" class="aprv_check" <?php echo $checked;?>/><label><span></span><i>(<?php echo $txt;?>)</i></label>
					<?php
						}
						?>
				</td>
			</tr>
			<?php
				$tot_par=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from Testparameter where TestId='$tst'"));
				if($tot_par>1)
				{
				?>
			<tr>
				<th colspan='6'><?php echo $tname[testname];?></th>
			</tr>
			<?php
				}
				else
				{
					if($tst=="921")
					{
					echo "<tr><th colspan='6'>$tname[testname]</th></tr>";
					}	
				}
				/*
				}
				else
				{
					
					echo "<tr><th colspan='6'>$tname[testname]</th></tr>";
					
				}
				 */
				 
				   
					$i=1;
					$j=1;
					$param=mysqli_query($GLOBALS["___mysqli_ston"], "select * from Testparameter where TestId='$tst' order by sequence"); 
					while($p=mysqli_fetch_array($param))
					{
							
						$pn=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from Parameter_old where ID='$p[ParamaterId]'"));		
						if($pn[ResultType]!=0)
						{
							$res=mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$tst' and paramid='$p[ParamaterId]'");
							/*
							$num=mysqli_num_rows($res);
							if($num>0)
							{
							*/	
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
					$nres=$t_res[result];
					if(strlen($nres)<15)
					{
					?>
				<td class="<?php echo $par_class;?>" valign="top" contenteditable="true"><b><i><?php echo $pn[Name];?></i></b></td>
				<td width="1%" valign="top" style="text-align:right"><b><span id="chk_res<?php echo $i;?>" contenteditable="true">&nbsp;</span></b></td>
				<td valign="top" contenteditable="true" id="result<?php echo $i;?>" style="width:auto"><?php echo nl2br($t_res[result]);?></td>
				<td><?php echo $p_unit[unit_name];?></td>
				<td id="norm_r<?php echo $i;?>" style='text-align:left;' contenteditable='true'>
					<script>load_normal('<?php echo $uhid;?>','<?php echo $p[ParamaterId];?>','<?php echo $t_res[result];?>','<?php echo $i;?>')</script>
				</td>
				<td colspan="2"><?php echo $meth_name[name];?></td>
				<td><input type="button" class="btn btn-info" value="Update"/></td>
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
						
					?>			
				<td width="7%" valign="top" class="<?php echo $par_class;?>">
					<?php echo $pn[Name];?>
				</td>
				<td width="1%" valign="top" style="text-align:right"><b><span id="chk_res<?php echo $i;?>" contenteditable="true">&nbsp;</span></b></td>
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
				//}
				}
				else
				{
				  echo "<tr><td colspan='7' style='text-align:left;padding-left:20px !important' ><b>$pn[Name]</b></td></tr>";
				}
				}
				
				?>
		</table>
		<br/><br/>
		<?php
			$j_test++;
			
			}
			//}
			}
			}
			?>	
	</div>
</div>
<?php
	}
	else if($_POST[type]==2)
	{
		
		$pin=$_POST[pin];
		$user=$_POST[user];
		$batch=$_POST[batch];
		
		
		
		$q=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from uhid_and_opdid where opd_id='$pin'"));
		$pinfo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_info where patient_id='$q[patient_id]'"));
		
				
		?>
<div id="sam_aprv_div" tabindex="-1" onkeyup="approve_key(event)">
	<div align="right">
<!--
	<input type="button" class="btn btn-danger" value="Exit" onclick="win_exit()"/>
-->
	</div>
	
	
<table class="table table-bordered">
<tr>
	<th>
		Name / Age / Sex: 
			<?php 
				echo $pinfo[name]." / ";
			
				echo $pinfo[age]." ".$pinfo[age_type]." / ".$pinfo[sex];
			
			?>
		
		<input type="hidden" id="pin" value="<?php echo $pin;?>"/>
		<input type="hidden" id="batch" value="<?php echo $batch;?>"/>

	</th>
	<th colspan="2">
		UHID: <?php echo $q[patient_id];?>
		||
		Bill No.: <?php echo $pin;?>
	</th>
	
</tr>

<tr>
	<td colspan="3">
		<ul class="nav nav-tabs custom-tab" role="tablist" id="myTab">
		<?php
			$i=1;
			$dep1=mysqli_query($GLOBALS["___mysqli_ston"],"select * from test_department");
			while($dp=mysqli_fetch_array($dep1))
			{
				$res_dep=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select distinct testid from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid in(select testid from testmaster where type_id='$dp[id]')"));
				$res_sum1=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select distinct (a.testid),b.type_id from patient_test_summary a,testmaster b where a.testid=b.testid and b.type_id='$dp[id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid not in(select testid from testresults where patient_id='$q[patient_id]' and (opd_id='$opd_id' or ipd_id='$ipd_id') and batch_no='$batch')"));	
				
				$res_histo=0;
				if($dp[id]=="30")
				{
					$res_histo=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select distinct(patient_id) from patient_histo_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch'"));
				}
				
				$res_dep=$res_dep+$res_sum1+$res_histo;
				
				
				if($res_dep>0)
				{
						$cls="";
						if($i==1)
						{
							$cls="active";
						}
						
						echo "<li id='tab_pentry$i' class='$cls'><a id='option_a$i' href='#option$i' aria-controls='home' role='tab' data-toggle='tab' onClick='load_pentry()' class='name_$dp[id]'>$dp[name]</a></li>";
						$dep[$i]=$dp[id];
						$i++;
				}
				
			}
			
			/*-----Remaining Tests----*/
			if($i>1)
			{
				$cls="";
			}
			
			$rem_tst=mysqli_query($link,"select * from patient_test_details where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid in(select testid from testmaster where type_id!='132' and category_id='1')");
			while($rm=mysqli_fetch_array($rem_tst))
			{
				$rem_res=mysqli_num_rows(mysqli_query($link,"select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$rm[testid]'"));
				$rem_sum=mysqli_num_rows(mysqli_query($link,"select * from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$rm[testid]'"));
				$rem_histo=mysqli_num_rows(mysqli_query($link,"select * from patient_histo_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$rm[testid]'"));
				
				
				
				if($rem_res==0 && $rem_sum==0 && $rem_histo==0)
				{
					$rem_test[]=$rm[testid];
				}
				
			}
			
			if(sizeof($rem_test)>0)
			{
				echo "<li id='tab_pentry$i' class='$cls'><a id='option_a$i' href='#option$i' aria-controls='home' role='tab' data-toggle='tab' onClick=\"$('#rem_test').slideDown(200)\" class='name_rem_test'>Remaining Tests</a></li>";
			}
			echo "</ul>";
			$x=1;
			$i=1;
			echo "<div class='tab-content'>";
foreach($dep as $dp_type)
{
	if($dp_type)
	{
		if($x==1)	
		{
		?> 
			<div role='tabpanel' class='tab-pane fade in active' id='option<?php echo $x;?>'>
		<?php
		}
		else
		{
		?> 
			<div role='tabpanel' class='tab-pane fade' id='option<?php echo $x;?>'>
	<?php
		}
		
		
		//-----Checking "testresults"----//
		
		//$t_res_doc=mysqli_query($GLOBALS["___mysqli_ston"],"select distinct(testid) from testresults where patient_id='$q[patient_id]' and visit_no='$q[visit_no]' and main_tech>0 and testid in(select testid from testmaster where type_id='$dp_type')");
		
		$t_res_doc=mysqli_query($GLOBALS["___mysqli_ston"],"select distinct(a.testid) from testresults a,testmaster b where (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid=b.testid and b.type_id='$dp_type'");
		$t_res_num=mysqli_num_rows($t_res_doc);
		 
		if($t_res_num>0)
		{
			while($t_res_data=mysqli_fetch_array($t_res_doc))
			{
				$pos=0;
				
				//~ $uhid=$q[patient_id];
				//~ $visit=$q[visit_no];
				$tst=$t_res_data[testid];
			
				
				
				//------Culture Check------//
				$cul_chk=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select testname from testmaster where testid='$tst'"));
				
				if (strpos($cul_chk[testname],'ulture') !== false) 
				{
					$pos=2;
				}
		
				if (strpos($cul_chk[testname],'ULTURE') != false) 
				{
					$pos=2;
				}
		
				if (strpos($cul_chk[testname],'ulture') != false) 
				{
					$pos=2;
				}
				
				
						
				if($pos==0)
				{					
				?>
	<input type="hidden" id="test_id_<?php echo $i;?>" value="<?php echo $tst;?>"/>
	<input type="hidden" id="test_upd_<?php echo $tst;?>" value="0"/>
	
	<table class="table table-bordered table-condensed table-report" id="test_display">
		<?php
			$test_rs=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and doc>0"));
			$doc_chk=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select name,emp_id from employee where emp_id in(select doc from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst')"));
			
			$chk="";
			$butt="";
			$note_txt="";
			$txt="Approve";
			$verify="Request to verify";
			if($test_rs>0)
			{
				
				$chk="Checked";
				$txt="<span class='aprv_dne'>$doc_chk[name]</span>";
				
				if($doc_chk[ID]>0 && $doc_chk[ID]!=$user)
				{
						$chk.=" disabled";
						$butt=" disabled";
				}
				else
				{
					$note_txt="<b><i><u>Add Note</u></i></b>";
					$chk_note=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults_note where pin_id='$pin' and testid='$tst'"));
					if($chk_note>0)
					{
						$note_txt="<b><i><u>View Note</u></i></b>";
					}	
					
					
					
										
				}
			}
			else
			{
				$for_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select for_doc from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst'"));		
				if($for_doc[for_doc]!=$user && $for_doc[for_doc]!=0)
				{
					$note_txt="<b><i><u>Add Note</u></i></b>";
					$chk_note=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults_note where pin_id='$pin' and testid='$tst'"));
					if($chk_note>0)
					{
						$note_txt="<b><i><u>View Note</u></i></b>";
					}	
				}
				else
				{
					$note_txt="<b><i><u>Add Note</u></i></b>";
					$chk_note=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults_note where pin_id='$pin' and testid='$tst'"));
					if($chk_note>0)
					{
						$note_txt="<b><i><u>View Note</u></i></b>";
					}		
				}
			}
			
			$note_txt="<b><i><u>Add Note</u></i></b>";
			$chk_note=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults_note where pin_id='$pin' and testid='$tst'"));
			if($chk_note>0)
			{
				$note_txt="<b><i><u>View Note</u></i></b>";
			}
									
			?>
			
		<tr>
			<th width="30%">Test</th>
			<th colspan="7">Result 

				<?php
					
					?>
				<div style="position:relative;">
				<div style="position:absolute;z-index:2000;top:-25px;right:0px;width:500px;">
					<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,1)" class="aprv_check" <?php echo $chk;?>/> <i>(<?php echo $txt;?>)</i>
					
					<span id="note_<?php echo $i;?>" class="btn btn-info btn-xs" onclick="load_note(<?php echo $i;?>)"><?php echo $note_txt;?></span>
					
					
					<?php
					
					?>
					
					
					
				</div>
				</div>	
			</th>
		</tr>
		<?php
			$tot_par=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from Testparameter where TestId='$tst'"));
			if($tot_par>1)
			{
			?>
		<tr>
			<th colspan='7'><?php echo $cul_chk[testname];?></th>
		</tr>
		<?php
			}
			else
			{
				if($tst=="921")
				{
				echo "<tr><th colspan='7'>$tname[testname]</th></tr>";
				}	
			}
					 
			   
				//$i=1;
				$j=1;
				$param=mysqli_query($GLOBALS["___mysqli_ston"], "select * from Testparameter where TestId='$tst' order by sequence"); 
				while($p=mysqli_fetch_array($param))
				{
						
					$pn=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from Parameter_old where ID='$p[ParamaterId]'"));		
					if($pn[ResultType]!=0)
					{
						$res=mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and paramid='$p[ParamaterId]'");
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
			$sty="";
			if($t_res[doc]==0)
			{
				$sty="style='color:rgb(0, 78, 119);'";
			}
			?>
		<tr class="tr_test" <?php echo $sty;?> >
			<?php
				$nres="";
				$fix_res=mysqli_fetch_array(mysqli_query($link,"select * from param_fix_result where testid='$tst' and paramid='$p[ParamaterId]'"));
				$nres=$fix_res[result];
				
				if($t_res[result])
				{
					$nres=$t_res[result];	
				}
				
			
				
				
				if(strlen($nres)<5015)
				{
										
					$err_sty="";
					$fix_bold=0;
					if($fix_res[range_check]==1)
					{
						if($fix_res[result]!=$t_res[result])
						{
							$fix_bold=1;
						}
					}
					
					if($t_res[range_status]==1 || $fix_bold==1)
					{
						$err_sty="border:2px solid red";
					}
					
					$range=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$t_res[range_id]'"));
					$norm_range=$range[normal_range];
					
					
				?>
			
			
			
			<?php
			$cols_span="0";
			$data_width="400px";
			if(!$p_unit[unit_name])
			{
				if($norm_range=='')
				{
					$cols_span="3";
					$data_width="500px";
				}
				else
				{
					$cols_span="2";
				}
			}
			?>
			
			<td class="<?php echo $par_class;?>" valign="top">
				<b><i><?php echo $pn[Name];?></i></b>
				<?php
				
				$chk_lis_res=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and paramid='$p[ParamaterId]'"));
				if($nres!=$chk_lis_res[result] && $chk_lis_res[result]!='')
				{
					
					$t_name=mysqli_fetch_array(mysqli_query($link,"select * from employee where emp_id='$t_res[main_tech]'"));
					?>
					<div style='font-size:10px;font-weight:bold;color:#FF4136'>
					<i>
					Previous LIS Result: <?php echo $chk_lis_res[result];?> <br/>
					Edited & Approved By <i><?php echo $t_name[name];?></i>
					</i>
					</div>
					<?php
				}
				else
				{
					if($t_res[result])
					{
						$t_name=mysqli_fetch_array(mysqli_query($link,"select * from employee where emp_id='$t_res[main_tech]'"));
						?>
						<div style='font-size:10px;font-weight:bold;'>
						<?php
						if(!$chk_lis_res[result])
						{
							?>	
								<span style='color:red'>Manual</span> <br/>
								<i>
								(Approved By <i><?php echo $t_name[name];?> </i> )
								</i>
						<?php
						}
						else
						{
						?>	
							<i>
							(Approved By <i><?php echo $t_name[name];?></i>)
							</i>
						<?php } ?>
						</div>
					<?php
					}
				}
				?>
			</td>
			<td valign="top" id="result<?php echo $i;?>" style="font-weight:bold" width="10%" colspan="<?php echo $cols_span;?>">
			
			
			<?php
			
			
			if($pn[ResultOptionID]>0)
			{
				
				?> 
				<div id="display_text<?php echo $i;?>" class="display_full_text"></div> 
<!--
				<div onmouseover="display_text(1,<?php echo $i;?>)" onmouseout="display_text(2,<?php echo $i;?>)">
-->
				<div>
				<input type="text" id="result_res<?php echo $i;?>" class="par_<?php echo $p[ParamaterId];?>" value="<?php echo nl2br(htmlspecialchars($nres));?>" list='list<?php echo $i;?>' style="<?php echo $err_sty;?>;width:<?php echo $data_width;?>" name="test_<?php echo $tst;?>" onkeyup="check_upd_test(<?php echo $tst;?>)"/> <?php
				echo "<datalist id='list$i'>";
				$sel=mysqli_query($GLOBALS["___mysqli_ston"], "select * from ResultOptions where id='$pn[ResultOptionID]'");
				while($s=mysqli_fetch_array($sel))
				{
					$op=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select name from Options where id='$s[optionid]'"));
					echo "<option value='$op[name]'>$op[name]</option>";
				}
				echo "</datalist>";
				echo "</div>";

			}
			else
			{
				if($pn[ResultType]==27)
				{
					?><textarea rows='5' style="width:800px" class="par_<?php echo $p[ParamaterId];?>" id="result_res<?php echo $i;?>" name="test_<?php echo $tst;?>"> <?php echo htmlspecialchars($nres);?> </textarea> <?php
				}
				else
				{
					if($pn[ResultType]==3)
					{
						$err_sty.="width:".$data_width;
					}
					?> <input type="text" class="par_<?php echo $p[ParamaterId];?>" id="result_res<?php echo $i;?>" style="<?php echo $err_sty;?>" value="<?php echo htmlspecialchars($nres);?>" onkeyup="check_upd_test(<?php echo $tst;?>);check_formula(this.id);" name="test_<?php echo $tst;?>"/> <?php
					$chk_form=mysqli_query($link,"select * from parameter_formula where ParameterID='$p[ParamaterId]'");
					if(mysqli_num_rows($chk_form)>0)
					{
						$form=mysqli_fetch_array($chk_form);
						echo "<input type='hidden' class='formula' value='$form[formula]' id='$i' name='$form[res_dec]'/>";
					}
				}
			}
			
			?>
			</td>
			
			<?php
			if($cols_span==0)
			{
				?>
					<td width="10%"><?php echo $p_unit[unit_name];?></td>
				<?php
			}
			if($cols_span==0 || $cols_span==2)
			{
			?>
			<td id="norm_r<?php echo $i;?>" style='text-align:left;' width="20%">
			<?php
			
						
						echo $norm_range;
			?>
			</td>
			<?php
			}
			?>
			<td align="right">
				<?php
				if(!$t_res[result])
				{
					?><input type="hidden" value="<?php echo $fix_res[must_save];?>" class="check_save_<?php echo $tst;?>" id="check_save_<?php echo $tst;?>_<?php echo $p[ParamaterId];?>" /> <?php
				}
				?>
				<input type="button" id="button_<?php echo $i;?>" class="btn btn-default" value="Update" onclick="update_test_res(this.value,<?php echo $i;?>,'<?php echo $pin;?>',<?php echo $tst;?>,<?php echo $p[ParamaterId];?>)" <?php echo $chk;?>/>
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
					
				?>			
			<td width="7%" valign="top" class="<?php echo $par_class;?>">
				<?php echo $pn[Name];?>
			</td>
			
			<td colspan="6" valign="top" id="result<?php echo $i;?>" style="font-weight:bold">
				<?php echo nl2br($t_res[result]);?>
			</td>
			<td align="right">
				<input type="button" id="button_<?php echo $i;?>" class="btn btn-default" value="Update" name="button_tst_<?php echo $tst;?>" onclick="update_test_res(this.value,<?php echo $i;?>,'<?php echo $pin;?>',<?php echo $tst;?>,<?php echo $p[ParamaterId];?>)" <?php echo $chk;?>/>
			</td>
			<?php
				
				
				}
							
				?>
		</tr>
		
		<?php
			$i++;
			
			$j++;
			//}
			}
			else
			{
			  echo "<tr><td colspan='7' style='text-align:left;padding-left:20px !important' ><b>$pn[Name]</b></td></tr>";
			}
			}
			
			if(mysqli_num_rows($param)>1)
			{
			?>
				<tr><td colspan='7' style='text-align:center;padding-left:20px !important' ><input type="button" class="btn btn-info" value="Update All" onclick="update_all('<?php echo $pin;?>',<?php echo $tst;?>)"/></td></tr>
			<?php
			}
			?>
	</table>
	<div class="table-modifier">
			<?php
				//echo "select * from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst'";
				$pat_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst'");
				$num_pat=mysqli_num_rows($pat_sum);
				
				if($num_pat>0)
				{
				 $pat_s=mysqli_fetch_array($pat_sum);
				 echo $pat_s[summary];
				}
				else
				{
					 $chk_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_summary where testid='$tst'");
					 $num_sum=mysqli_num_rows($chk_sum);
					 if($num_sum>0)
					 {
						 $summ_all=mysqli_fetch_array($chk_sum);
						 echo $summ_all[summary];
					 }
				 
				}
				?>
		</div>
	<?php
		}
		
		
		
		
		
		
		
		else if($pos==2)
		{
			
			$i++;
			?>
	<input type="hidden" id="test_id_<?php echo $i;?>" value="<?php echo $tst;?>"/>
	<table class="table table-bordered">
	<?php
		$test_rs=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and doc>0"));
		$doc_clt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select name,emp_id from employee where emp_id in(select doc from testresults where opd_id='$pin' and testid='$tst')"));
		
		$chk="";
		$txt="Approve";
		if($test_rs>0)
		{
			$chk="Checked";
			$txt="<span class='aprv_dne'>$doc_clt[name]</span>";

			if($doc_clt[emp_id]!=$user)
			{
				$chk.=" disabled";
			}
			
		}
		else
		{
			$for_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select for_doc from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst'"));		
			/*if($for_doc[for_doc]!=$user && $for_doc[for_doc]!=1000)
			{
				$chk.=" disabled";
			}*/
		}
		
		$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='$tst')"));
		$sam1=$samp["Name"];
		
		
		$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$tst'"));
		$spec1=explode(" ",$tname[testname]);
		$spec_s=sizeof($spec1);
		$spec=array_pop($spec1);
		
		$col=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and paramid='311'"));
		
		$num=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and paramid='630'"));
		$num_c_sum=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst'"));
		?>
	<br/>
	
	<div style="position:relative;">
		<div style="position:absolute;z-index:2000;right:-20px;width:500px;font-size:20px;font-weight:bold">
			<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,1)" class="aprv_check" <?php echo $chk;?>/> <i>(<?php echo $txt;?>)</i>
			
		</div>
		</div>
	<div style="text-align:left">
		<b><u><?php echo $tname[testname];?></u></b>
	</div>
	
	<div style="">
					<?php
						$cult_table="";
						$cs_res_ant=mysqli_query($link,"select a.*,b.* from testresults a,Parameter_old b where a.patient_id='$q[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid='$tst' and a.paramid=b.ID and b.ResultOptionID='68' order by a.sequence");
						if(mysqli_num_rows($cs_res_ant)==0)
						{
							$cult_table="cult_table";
						}
						?>
						
						<div style="">
						<table class="table table-bordered table-condensed <?php echo $cult_table;?>" width="100%">
						<tr>
							<th style="text-align:left;width:200px;" valign="top">TEST</th>
							<td style="width:5px;" valign="top">:</td>
							<td valign="top"><?php echo $tname[testname];?></td>
						</tr>	
						<?php
							
							$fung=mysqli_fetch_array(mysqli_query($link, "select * from testresults where patient_id='$q[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch'  and testid='$tst' and paramid='310'"));
							
							$cs_res=mysqli_query($link,"select a.*,b.* from testresults a,Parameter_old b where a.patient_id='$q[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid='$tst' and a.paramid=b.ID and b.ID!='311' and b.ID!='312' and b.ResultOptionID!='68' order by a.sequence");	
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
							
							$col=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$q[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and testid='$tst' and paramid='311'"));	
							$pow=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$q[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and testid='$tst' and paramid='312'"));	
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
						
						<?php
						
						if(mysqli_num_rows($cs_res_ant)>0)
						{
						?>
							<table class="table table-condensed table-bordered">
								<tr>
									<th contentEditable="true" width="33%">SENSITIVE</th>
									<th contentEditable="true" width="33%">INTERMEDIATE</th>
									<th contentEditable="true" width="33%">RESISTANT</th>
								</tr>
								<tr>
									<td valign="top" style="border-right:1px solid #CCC">
										<?php
											$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where a.patient_id='$q[patient_id]' AND (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid='$tst' and (a.result like 'S' or a.result like 's%') and a.paramid=b.ID order by b.Name");
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
											$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where a.patient_id='$q[patient_id]' AND (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid='$tst' and (a.result like 'I%' or a.result like 'i%') and a.paramid=b.ID order by b.Name");
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
											$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where a.patient_id='$q[patient_id]' AND (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch'  and a.testid='$tst' and ( a.result like 'R%' or a.result like 'r%') and a.paramid=b.ID order by b.Name");
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
								$pat_sum=mysqli_query($link, "select * from patient_test_summary where patient_id='$q[patient_id]' and (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and  testid='$tst' and main_tech>0");
								$num_pat=mysqli_num_rows($pat_sum);
								
								if($num_pat>0)
								{
									$pat_s=mysqli_fetch_array($pat_sum);
									echo $pat_s[summary];
								}
								else
								{
									$chk_sum=mysqli_query($link, "select * from test_summary where testid='$tst'");
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
		<?php
			$i++;
				
			?> 
	
	<?php
		}
		}
		}	
		
		
		
		
		
		
		//-----------------------------Test Summary-----------------------//
		
		
		$chk_summ_q=mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_test_summary where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and testid not in(select testid from testresults where patient_id='$q[patient_id]' and opd_id='$q[opd_id]')");
		$chk_summ=mysqli_num_rows($chk_summ_q);		
		if($chk_summ>0)
		{
		
		while($summ=mysqli_fetch_array($chk_summ_q))
		{
			$chk_dep=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testmaster where testid='$summ[testid]' and type_id='$dp_type'"));	
			
			if($chk_dep>0)
			{
				$i++;
				$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$summ[testid]'"));
				$doc_sum=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name from employee where emp_id='$summ[doc]'"));
				
				$s_text="Approve";
				$s_chk="";
				if($summ[doc]>0)
				{
					$s_text="<span class='aprv_dne' style='color:black'>$doc_sum[Name]</span>";
					$s_chk="Checked";
					
					/*if($doc_sum[ID]!=$user)
					{
						$s_chk.=" disabled";
					}*/
				}
				?>
	<input type="hidden" id="test_id_<?php echo $i;?>" value="<?php echo $summ[testid];?>"/>
	<input type="hidden" id="test_upd_<?php echo $summ[testid];?>" value="0"/>
	<table class="table table-bordered table-report">
		<tr>
			<td>
				
			</td>
		</tr>
		<tr>
			<td>
				<h5><?php echo $tname[testname];?></h5>
			</td>
		</tr>
		<tr>
			<td>
				<div style="position:relative;">
				<div style="position:absolute;z-index:2000;top:-25px;right:0px;width:500px;">
					<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,2)" class="aprv_check" <?php echo $s_chk;?>/> <i>(<?php echo $s_text;?>)</i>
					<?php
					/*---------------------Verification-------------------*/
					/*
					$verify="Verify";
					$chk_verify=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from result_verify where opd_id='$pin' and testid='$summ[testid]'"));
					if($chk_verify[tot]>0)
					{
						$chk_verified=mysqli_fetch_array(mysqli_query($link,"select * from result_verify where opd_id='$pin' and testid='$summ[testid]' order by slno desc"));	
						if($chk_verified[verified_by]==0)
						{
							if($user==$chk_verified[requested_by] || $user==$chk_verified[requested_to])
							{
								$verify="Reverification Pending";
								?> <span id="verify_<?php echo $i;?>" class="btn btn-danger btn-xs" onclick="load_verify(<?php echo $i;?>)"><b><i><?php echo $verify;?></i></b></span> <?php
							}
						}
						else
						{
							$v_doc="";
							$ver_doc=mysqli_query($link,"select verified_by from result_verify where opd_id='$pin' and testid='$summ[testid]' order by slno desc");
							while($v_dc=mysqli_fetch_array($ver_doc))
							{
								$v_doc_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$v_dc[verified_by]'"));
								$v_doc=$v_doc_name[Name].",".$v_doc;
							}
							$v_doc=rtrim($v_doc,",");
							
							?> <span id="verify_<?php echo $i;?>" class="btn btn-success btn-xs" ><b><i>Re verified By <?php echo $v_doc;?></i></b></span> <?php
						}
					}
					else
					{
						?> <span id="verify_<?php echo $i;?>" class="btn btn-primary btn-xs" onclick="load_verify(<?php echo $i;?>)"><b><i><?php echo $verify;?></i></b></span> <?php
					}
					*/
					/*---------------------------------Verification Ends--------------------------*/
					?>
					
					
				</div>
			</div>
			</td>
		</tr>
		<table class="table table-borderless">
			<tr>
				<td colspan="2"><b>TEST NAME: <?php echo $tname[testname];?></b></td>
			</tr>
			<tr>
				<td colspan="2">
					<div  id="summary_res_<?php echo $i;?>" name="summary_res_<?php echo $i;?>">
					<?php
						$pat_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and testid='$summ[testid]'");
						$num_pat=mysqli_num_rows($pat_sum);
						
						if($num_pat>0)
						{
							$pat_s=mysqli_fetch_array($pat_sum);
							echo $pat_s[summary];	
						}
						
						?>
					</div>
				</td>
			</tr>
			<tr><td align="center" colspan="2"><input type='button' value='Edit' class='btn btn-info' id="summ_res_bt_<?php echo $i;?>" onclick="update_result(<?php echo $i;?>,this.value)"/></td></tr>
		</table>
		<?php
			$i++;
			}
			}
			}
			
			//------------------Histo Check-------------------------
			if($dp_type==30)
			{
				
				$chk_histo_q=mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_histo_summary where patient_id='$q[patient_id]' and visit_no='$q[visit_no]' ");
				$chk_histo=mysqli_num_rows($chk_histo_q);		
				if($chk_histo>0)
				{
				
				while($histo=mysqli_fetch_array($chk_histo_q))
				{
					$chk_dep=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testmaster where testid='$histo[testid]' and type_id='$dp_type'"));	
					
					if($chk_dep>0)
					{
						
						$i++;
						$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$histo[testid]'"));
						$doc_histo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name from employee where emp_id='$histo[doc]'"));
						
						$s_text="Approve";
						$s_chk="";
						
						if($histo[doc]>0)
						{
							$s_text="<span class='aprv_dne' style='color:black !important'>$doc_histo[Name]</span>";
							$s_chk="Checked";
							
							if($doc_sum[ID]!=$user)
							{
								$s_chk.=" disabled";
							}
						}
						?>
			<input type="hidden" id="test_id_<?php echo $i;?>" value="<?php echo $histo[testid];?>"/>
			<table class="table table-bordered table-report">
				
				<tr>
					<td>
						<h5><?php echo $tname[testname];?></h5>
					</td>
				</tr>
				<tr>
					<td>
						<div style="position:relative;">
							<div style="position:absolute;z-index:2000;top:-25px;right:0px;width:500px;">
							<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,4)" class="aprv_check" <?php echo $s_chk;?>/> <i>(<?php echo $s_text;?>)</i>
							
							
							<?php
							/*---------------------Verification-------------------*/
							
							$verify="Request to verify";
							$chk_verify=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from result_verify where patient_id='$q[patient_id]' and visit_no='$q[visit_no]' and testid='$histo[testid]'"));
							if($chk_verify[tot]>0)
							{
								$chk_verified=mysqli_fetch_array(mysqli_query($link,"select * from result_verify where patient_id='$q[patient_id]' and visit_no='$q[visit_no]' and testid='$histo[testid]' order by slno desc"));	
								if($chk_verified[verified_by]==0)
								{
									if($user==$chk_verified[requested_by] || $user==$chk_verified[requested_to])
									{
										$verify="Reverification Pending";
										?> <span id="verify_<?php echo $i;?>" class="btn btn-danger btn-xs" onclick="load_verify(<?php echo $i;?>)"><b><i><?php echo $verify;?></i></b></span> <?php
									}
								}
								else
								{
									$v_doc="";
									$ver_doc=mysqli_query($link,"select verified_by from result_verify where patient_id='$q[patient_id]' and visit_no='$q[visit_no]' and testid='$histo[testid]' order by slno desc");
									while($v_dc=mysqli_fetch_array($ver_doc))
									{
										$v_doc_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$v_dc[verified_by]'"));
										$v_doc=$v_doc_name[Name].",".$v_doc;
									}
									$v_doc=rtrim($v_doc,",");
									
									?> <span id="verify_<?php echo $i;?>" class="btn btn-success btn-xs" ><b><i>Re verified By <?php echo $v_doc;?></i></b></span> <?php
								}
							}
							else
							{
								?> <span id="verify_<?php echo $i;?>" class="btn btn-primary btn-xs" onclick="load_verify(<?php echo $i;?>)"><b><i><?php echo $verify;?></i></b></span> <?php
							}
							/*---------------------------------Verification Ends--------------------------*/
							?>
							
							
						</div>
						</div>
					</td>
				</tr>
				</table>
				
				<table class="table borderless">
							<tr>
								
								<td colspan="5" id="histo_<?php echo $i;?>">
									<?php
									
										$hist_det=mysqli_fetch_array(mysqli_query($link,"select * from patient_histo_summary where patient_id='$q[patient_id]' and visit_no='$q[visit_no]' and testid='$histo[testid]'"));
										$res_h=explode("image loads here",$hist_det[summary]);
										if($res_h[1])
										{
											echo $res_h[0];
											
											?>
											<div align="center">
											<?php
											$img_file=mysqli_query($link,"select * from image_temp where  patient_id='$q[patient_id]' and visit_no='$q[visit_no]' and testid='$histo[testid]'");
											
											while($img=mysqli_fetch_array($img_file))
											{
												$_path=$img[path];
												echo "<div style='display:inline-block;margin:5px'><img src='$_path' height='170' width='220' class='img'/></div>";											
											}
											?>
											</div>
											<?php
											echo $res_h[1];
										}
										else
										{
											echo $res_h[0];
											?>
											<div align="center">
											<?php
											$img_file=mysqli_query($link,"select * from image_temp where  patient_id='$q[patient_id]' and visit_no='$q[visit_no]' and testid='$histo[testid]'");
											
											while($img=mysqli_fetch_array($img_file))
											{
												$_path=$img[path];
												echo "<div style='display:inline-block;margin:10px;'><img src='$_path' height='150' width='180' class='img'/></div>";
											}
											?>
											</div>
											<?php
										}
										
										?>
								</td>
							</tr>
							<tr style="display:none">
								<td colspan="5" align="center"> <input type="button" class="btn btn-danger" value="Edit" onclick="edit_pad(this,<?php echo $i;?>)"/></td>
							</tr>
						</table>
				<?php
					$i++;
					}
					}
					}
				
				
				
				
			}
			
			//-------------------Histo Ends-------------------------
			//-------------------------Widal Check-----------------------//
			
			if($dp_type==32)
			{
			$chk_wd_q=mysqli_query($GLOBALS["___mysqli_ston"],"select * from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch'");
			$chk_wd=mysqli_num_rows($chk_wd_q);
			if($chk_wd>0)
			{
				$i++;
				$wid_chk=mysqli_fetch_array($chk_wd_q);
				$w_chk="";
				$w_text="Approve";
				if($wid_chk[doc]>0)
				{
					
					$doc_wid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select name from employee where emp_id='$wid_chk[doc]'"));
					$w_chk="Checked";
					$w_text="<span class='aprv_dne'>$doc_wid[name]</span>";
					
					if($wid_chk[doc]>0 && $wid_chk[doc]!=$user)
					{
						//$w_chk.=" disabled";
					}
				}
				else
				{
					if($wid_chk[for_doc]!=$user && $wid_chk[for_doc]!=1000)
					{
						//$w_chk.=" disabled";
					}
				}
			?>
		<input type="hidden" id="test_id_<?php echo $i;?>" value="1227"/>
					<br/><br/>
					<div style="position:relative;">
					<div style="position:absolute;z-index:2000;right:-20px;width:500px;font-size:20px;font-weight:bold">
						<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,3)" class="aprv_check" <?php echo $w_chk;?>/> <i>(<?php echo $w_text;?>)</i>
						
						<?php
					/*---------------------Verification-------------------*/
					/*
					$chk_verify=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from result_verify where opd_id='$pin' and testid='1227'"));
					if($chk_verify[tot]>0)
					{
						$chk_verified=mysqli_fetch_array(mysqli_query($link,"select * from result_verify where opd_id='$pin' and testid='1227' order by slno desc"));	
						if($chk_verified[verified_by]==0)
						{
							if($user==$chk_verified[requested_by] || $user==$chk_verified[requested_to])
							{
								$verify="Reverification Pending";
								?> <span id="verify_<?php echo $i;?>" class="btn btn-danger btn-xs" onclick="load_verify(<?php echo $i;?>)"><b><i><?php echo $verify;?></i></b></span> <?php
							}
						}
						else
						{
							$v_doc="";
							$ver_doc=mysqli_query($link,"select verified_by from result_verify where opd_id='$pin' and testid='1227' order by slno desc");
							while($v_dc=mysqli_fetch_array($ver_doc))
							{
								$v_doc_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$v_dc[verified_by]'"));
								$v_doc=$v_doc_name[Name].",".$v_doc;
							}
							$v_doc=rtrim($v_doc,",");
							
							?> <span id="verify_<?php echo $i;?>" class="btn btn-success btn-xs" ><b><i>Re verified By <?php echo $v_doc;?></i></b></span> <?php
						}
					}
					else
					{
						?> <span id="verify_<?php echo $i;?>" class="btn btn-primary btn-xs" onclick="load_verify(<?php echo $i;?>)"><b><i><?php echo $verify;?></i></b></span> <?php
					}
					*/
					/*---------------------------------Verification Ends--------------------------*/
					?>
						
					</div>
					
					
					
				
		<table class="table table-bordered">
			<tr>
				<td colspan="7">
					<p class="text-left"><h5><b>Widal Test</b></h5></p>
					<div style="min-height:100px;">
						<?php
							$uhid=$q[patient_id];
							$visit=$q[visit_no];
							
							$w1=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and slno=1"));
							$w2=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and slno=2"));
							$w3=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and slno=3"));
							$w4=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and slno=4"));
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
							$pat_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst'");
							$num_pat=mysqli_num_rows($pat_sum);
							
							if($num_pat>0)
							{
								$pat_s=mysqli_fetch_array($pat_sum);
								echo $pat_s[summary];	
							}
							else
							{
								$chk_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_summary where testid='$tst'");
								$num_sum=mysqli_num_rows($chk_sum);
								if($num_sum>0)
								{
									$summ_all=mysqli_fetch_array($chk_sum);
									//echo $summ_all[summary];
								}
								
							}
							$i++;
							?>
					</div>
				</td>
			</tr>
		</table>
		<?php
			}
			}
						
						
						echo "</div>";
						$x++;
			}
			}
			
			if(sizeof($rem_test)>0)
			{
				?><div role='tabpanel' class='tab-pane fade in active' id='option<?php echo $x;?>' >
					<div style="display:none" id="rem_test">
						<br/><br/>
				<?php
				
				foreach($rem_test as $rt)
				{
					$r_name=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$rt'"));
					echo "<p><b>$r_name[testname]</b></p>";
				}
				echo "</div>";
				echo "</div>";
			}
						?>
						
						
						</div>
						<?php	
						
						}			
						
						
						
						
						?>
			
			</td></tr></table>
		</div>

