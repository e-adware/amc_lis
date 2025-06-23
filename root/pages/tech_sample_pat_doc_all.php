<?php
include("../../includes/connection.php");
include("pathology_normal_range_new.php");

$pin=$_POST["pin"];
$user=$_POST["user"];
$batch=$_POST["batch"];


$pat_reg=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$pin'"));

$uhid=$pat_reg["patient_id"];

$pinfo=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pat_reg[patient_id]'"));

if($pat_reg["type"]==3)
{
	$ipd_id=$pat_reg["opd_id"];
	$opd_id="";
}
else
{
	$opd_id=$pat_reg["opd_id"];
	$ipd_id="";
}

?>
<div id="sam_aprv_div" tabindex="-1" onkeyup="approve_key_all(event)">
<table class="table table-bordered">
	<tr>
		<th>
			Name / Age / Sex: 
				<?php 
					echo $pinfo["name"]." / ";
				
					echo $pinfo["age"]." ".$pinfo["age_type"]." / ".$pinfo["sex"];
				
				?>
			
			<input type="hidden" id="pin" value="<?php echo $pin;?>"/>
			<input type="hidden" id="batch" value="<?php echo $batch;?>"/>

		</th>
		<th>
			UHID: <?php echo $pat_reg["patient_id"];?>
			||
			Bill No.: <?php echo $pin;?>
		</th>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center;">
			<button class="btn btn-success" onclick="group_view_test('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch; ?>')" id="grp_print_rpt"><i class="icon-eye-open"></i> View Report</button>
		</td>
	</tr>
</table>
<?php
$apr_dep=array();
$dis_dep=array();
$dep_doc=mysqli_query($link,"select * from lab_doc_dept where doc_id='$user'");
if(mysqli_num_rows($dep_doc)>0)
{
	while($dd=mysqli_fetch_array($dep_doc))
	{
		$apr_dep[]=$dd["approve"];
		$dis_dep[]=$dd["display"];
	}
}

$i=1;
if(sizeof($dis_dep)>0)
{
	$tst_dep=implode(",",$dis_dep);
	
	$test=mysqli_query($link,"select a.* from patient_test_details a,testmaster b where (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid=b.testid and b.type_id in($tst_dep) order by a.slno");
}
else
{
	$test=mysqli_query($link,"select * from patient_test_details where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' order by slno");
}
while($tst=mysqli_fetch_array($test))
{
	$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$tst[testid]'"));
	$chk_res=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and main_tech>0"));
	$chk_sum=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and main_tech>0"));
	$chk_wid[tot]==0;
	if($tst[testid]==1227)
	{
		$chk_wid=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and main_tech>0"));
	}
	
	if($chk_res[tot]>0) //----------TestResults-----------//
	{
		if (strpos(strtolower($tname["testname"]),'culture') !== false || strpos(strtolower($tname["testname"]),'CULTURE') !== false || strpos(strtolower($tname["testname"]),'Culture') !== false)
		{
			//-----------Approval/Display Check--------//
			$apr_allow=0;
			if(sizeof($apr_dep)>0)
			{
				if(in_array($tname["type_id"],$apr_dep))
				{
					$apr_allow=1;
				}
			} else { $apr_allow=1; }
			//-----------------------------------------//
			
			?>
			<div class="test_res">
				
			<div style="margin-bottom:10px"></div>
			
			<div>
				
				<div>
						<!--<tr>
							<th style="text-align:left;width:200px;" valign="top">TEST</th>
							<td style="width:5px;" valign="top">:</td>
							<td valign="top"><?php echo $tname[testname];?></td>
						</tr>-->
						<?php
							
							$result=mysqli_fetch_array(mysqli_query($link, "select MAX(`iso_no`) AS `iso_no` from testresults where `patient_id`='$uhid' and (opd_id='$pin' or ipd_id='$pin') and `batch_no`='$batch' and testid='$tst[testid]' limit 1"));
							
							$iso_no_total=$result["iso_no"];
							
							if($result["iso_no"]==0) // No Growth
							{
								echo '<table class="<?php echo $cult_table;?>" width="100%">';
						?>
								<div class="row">
									<div class="span8">
											<?php echo $tname[testname];?>
											<input type="hidden" id="test_id_<?php echo $i;?>" value="<?php echo $tst[testid];?>"/>
									</div>
									<div class="span4">
										<span>
										<?php
										$res_cult=mysqli_fetch_array(mysqli_query($link,"select doc from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]'"));
										if($apr_allow==1)
										{
											if($res_cult[doc]>0)
											{
												$dname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res_cult[doc]'"));
												?>
												<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,1,0)" class="aprv_check" checked/>(<?php echo $dname[name];?>)
												<?php
											}
											else
											{
												?>
												<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,1,0)" class="aprv_check" /> <i>(Approve)</i>
												<?php
											}
										}
										else
										{
											if($res_cult[doc]>0)
											{
												$dname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res_cult[doc]'"));
												echo "<i>Approved By $dname[name]</i>";
											}
											else
											{
												echo "<i><u>Can not Approve</u></i>";
											}
										}
										?>
										</span>
									</div>
								</div>
						<?php
								
								$cs_res=mysqli_query($link,"select a.*,b.* from testresults a,Parameter_old b where (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid='$tst[testid]' and a.paramid=b.ID and b.ID!='311' and b.ID!='312' and b.ResultOptionID!='68' order by a.sequence");
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
								
								$col=mysqli_fetch_array(mysqli_query($link,"select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and paramid='311'"));
								$pow=mysqli_fetch_array(mysqli_query($link,"select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and paramid='312'"));
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
								echo "</table>";
							}
							else
							{
							?>
								<div class="widget-box">
									<div class="widget-title">
										<ul class="nav nav-tabs">
									<?php
										for($iso_no=1;$iso_no<=$iso_no_total;$iso_no++)
										{
											$active_cls="";
											if($iso_no==1)
											{
												$active_cls="active";
											}
									?>
											<li class="<?php echo $active_cls; ?>"><a data-toggle="tab" href="#tab_iso<?php echo $iso_no; ?>" id="cult_tab<?php echo $iso_no; ?>" onclick="cult_tab_click('<?php echo $iso_no; ?>')">ISO <?php echo $iso_no; ?></a></li>
									<?php
										}
									?>
										</ul>
									</div>
									<div class="widget-content tab-content" style="background-color: white;">
								<?php
									for($iso_no=1;$iso_no<=$iso_no_total;$iso_no++)
									{
										$z=1;
										$active_cls="";
										if($iso_no==1)
										{
											$active_cls="active";
										}
								?>
										<div id="tab_iso<?php echo $iso_no; ?>" class="tab_iso_cls tab-pane <?php echo $active_cls; ?>">
										<?php
											$cs_res_ant=mysqli_query($link,"select a.*,b.* from testresults a,Parameter_old b where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and a.paramid=b.ID and b.ResultOptionID='68' and a.iso_no='$iso_no' order by a.sequence");
											if(mysqli_num_rows($cs_res_ant)>0)
											{
										?>
												<div class="row">
													<div class="span8">
															<?php echo $tname[testname];?>
															<input type="hidden" id="test_id_<?php echo $i;?>" value="<?php echo $tst[testid];?>"/>
													</div>
													<div class="span4">
														<span>
														<?php
														$res_cult=mysqli_fetch_array(mysqli_query($link,"select doc from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and iso_no='$iso_no'"));
														if($apr_allow==1)
														{
															if($res_cult[doc]>0)
															{
																$dname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res_cult[doc]'"));
																?>
																<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve('<?php echo $i;?>',1,'<?php echo $iso_no;?>')" class="aprv_check" checked>(<?php echo $dname[name];?>)
																<?php
															}
															else
															{
																?>
																<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve('<?php echo $i;?>',1,'<?php echo $iso_no;?>')" class="aprv_check" /> <i>(Approve)</i>
																<?php
															}
														}
														else
														{
															if($res_cult[doc]>0)
															{
																$dname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res_cult[doc]'"));
																echo "<i>Approved By $dname[name]</i>";
															}
															else
															{
																echo "<i><u>Can not Approve</u></i>";
															}
														}
														?>
														</span>
													</div>
												</div>
												<br>
												<table class="table table-condensed table-bordered">
												<?php
													$cs_res=mysqli_query($link,"select a.*,b.* from testresults a,Parameter_old b where (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid='$tst[testid]' and a.paramid=b.ID and b.ID!='311' and b.ID!='312' and b.ResultOptionID!='68' and a.iso_no='$iso_no' order by a.sequence");
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
													$sample_names=mysqli_fetch_array(mysqli_query($link,"select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and paramid='309' and iso_no='$iso_no'"));
													$col=mysqli_fetch_array(mysqli_query($link,"select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and paramid='311' and iso_no='$iso_no'"));
													$pow=mysqli_fetch_array(mysqli_query($link,"select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and paramid='312' and iso_no='$iso_no'"));
													if($col["result"])
													{
														?>
															<tr>
																<th style="text-align:left" valign="top">COLONY COUNT</th>
																<td style="width:5px;" valign="top">:</td>
																<td valign="top"><?php echo $col["result"]."<sup>".$pow["result"]."</sup> CFU/ml of ".$sample_names["result"];?></td>
															</tr>
														<?php
													}
												?>
												</table>
												<table class="table table-condensed table-bordered">
													<tr>
														<th contentEditable="true" width="33%">SENSITIVE</th>
														<th contentEditable="true" width="33%">INTERMEDIATE</th>
														<th contentEditable="true" width="33%">RESISTANT</th>
													</tr>
													<tr>
														<td valign="top" style="border-right:1px solid #CCC">
															<?php
																$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid='$tst[testid]' and (a.result like 'S' or a.result like 's%') and a.paramid=b.ID and a.iso_no='$iso_no' order by b.Name");
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
																$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid='$tst[testid]' and (a.result like 'I%' or a.result like 'i%') and a.paramid=b.ID  and a.iso_no='$iso_no'order by b.Name");
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
																$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid='$tst[testid]' and ( a.result like 'R%' or a.result like 'r%') and a.paramid=b.ID and a.iso_no='$iso_no' order by b.Name");
																while($s=mysqli_fetch_array($sen))
																{
																	$mic_r=explode("#MICValue#",$s[result]);
																	$pn=mysqli_fetch_array(mysqli_query($link, "select Name from Parameter_old where ID='$s[paramid]'"));
																	echo "<div style='display:inline-block;width:160px'>".$pn[Name]."</div><br/>";}
																?>
														</td>
													</tr>
												</table>
											<?php
											}
										?>
										</div>
								<?php
									}
								?>
									</div>
								</div>
							<?php
							}
							
							
							?>
						
						<div style='text-align:left;margin-left:20px'>
							<br/>
							<?php
								$pat_sum=mysqli_query($link, "select * from patient_test_summary where (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and a.testid='$tst[testid]' and main_tech>0");
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
			<?php
		}
		else
		{	
			//-----------Approval/Display Check--------//
			$apr_allow=0;
			if(sizeof($apr_dep)>0)
			{
				if(in_array($tname[type_id],$apr_dep))
				{
					$apr_allow=1;
				}
			} else { $apr_allow=1; }
			//-----------------------------------------//
			
			$tot_par=mysqli_query($link,"select * from Testparameter where TestId='$tst[testid]' order by sequence");
			if(mysqli_num_rows($tot_par)==1) //-----------Single Parameter------------//
			{
				$par=mysqli_fetch_array($tot_par);
				
				$res=mysqli_fetch_array(mysqli_query($link,"select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and paramid='$par[ParamaterId]' and main_tech>0"));
				if($res[result])
				{
					?>
					<div class="test_res">
						<div class="row">
							
							<input type="hidden" id="test_id_<?php echo $i;?>" value="<?php echo $tst[testid];?>"/>
							
							<div class="span3">
								<?php echo $tname[testname];?>
							</div>
							
							<div class="span2">
								<?php
								$err_class="";
								if($res[range_status]==1)
								{ 
									$err_class="res_error";
								}
								 
								echo "<div class='$err_class' id='result_$i' contenteditable='true' style='display:inline-block;width:100px''>".$res[result]."</div>";
														
								if($apr_allow==1)
								{
									echo "<button class='btn btn-info btn-mini' onclick=update_test_res(this.value,$i,'$pin',$tst[testid],$par[ParamaterId])><i class='icon-save'></i></button>";	
								}
								
								?>
							</div>
							
							<div class="span1">
								<?php
									$p_unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID in(select UnitsID from Parameter_old where Id='$par[ParamaterId]')"));
									echo $p_unit[unit_name];
								?>
							</div>
							
							<div class="span2">
								<?php
									$range=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$res[range_id]'"));
									echo $range[normal_range];
								?>
							</div>
							<div class="span3">
								<span>
								<?php
																
								if($apr_allow==1)
								{
									if($res[doc]>0)
									{
										$dname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res[doc]'"));
										?>
										<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,1,0)" class="aprv_check" checked/>(<?php echo $dname[name];?>)
										<?php
									}
									else
									{
										?>
										<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,1,0)" class="aprv_check" /> <i>(Approve)</i>
										<?php
									}
								}
								else
								{
									echo "<i><u>(Can Not Approve)</u></i>";
								}
								?>
								</span>
								
							</div>
							<div class="span1">
								<button class="btn btn-info btn-mini" onclick="load_note(<?php echo $i;?>)"><i class="icon-comment"></i></button>
							</div>
						</div>
						
						<?php
						//-----Note----//
						$tst_note=mysqli_fetch_array(mysqli_query($link,"select * from testresults_note where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]'"));
						if($tst_note[note])
						{
							echo "<div class='tst_note'>";
							echo "<b>Note: </b>".$tst_note[note];
							echo "</div>";
						}
						
						//-----Note----//
						
						//----Update Result------//
						$upd_res=mysqli_fetch_array(mysqli_query($link,"select * from testresults_update where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and paramid='$par[ParamaterId]' order by slno desc"));
						
						if($upd_res[result] && $upd_res[result]!=$res[result])
						{
							echo "<div class='upd_res_div'>";
							$emp=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$upd_res[update_by]'"));
							echo "Previous Result: ".$upd_res[result].". Updated by : ".$emp[name];
							echo "</div>";
						}
						//----Update Result------//
						
						//----------Summary------//
						$tst_sum=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]'"));
						
						if(strip_tags($tst_sum[summary])!='')
						{
							?>
							<div class="tst_summ">
							<br/>
							<button class="btn btn-info btn-mini" onclick="tst_display_summ(<?php echo $i;?>)" id="btn_tst_summ_<?php echo $i;?>" value="hide">Show Summary</button>
							<div id="tst_summ_txt_<?php echo $i;?>" style="display:none">
								<?php echo "<br/>".$tst_sum[summary];?>
							</div>
							<?php
						}
						//-----------------------//
						?>
						
					</div>
					<?php
				}
			}
			else if(mysqli_num_rows($tot_par)>1)
			{
				?>
				<div class="test_res">
					<div class="row">
						<div class="span8">
								<?php echo $tname[testname];?>
								<input type="hidden" id="test_id_<?php echo $i;?>" value="<?php echo $tst[testid];?>"/>
						</div>
						<div class="span3">
							<span>
							<?php
							if($apr_allow==1)
							{
								$res_par=mysqli_fetch_array(mysqli_query($link,"select doc from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and doc>0 order by slno desc"));
								if($res_par[doc]>0)
								{
									$dname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res_par[doc]'"));
									?>
									<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,1,0)" class="aprv_check" checked/>(<?php echo $dname[name];?>)
									<?php
								}
								else
								{
									$res_stat=mysqli_fetch_array(mysqli_query($link,"select status from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' order by slno desc"));
									if($res_stat[status]==1)
									{
										?> <b><u><i>Pending Parameters</i></u></b><?php
									}
									else
									{
										?>
										<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,1,0)" class="aprv_check" /> <i>(Approve)</i>
										<?php
									}
								}
							}
							?>
							</span>
						</div>
						<div class="span1">
							<button class="btn btn-info btn-mini" onclick="load_note(<?php echo $i;?>)"><i class="icon-comment"></i></button>
						</div>
					</div>
					
				<div style="margin-bottom:5px"></div>	
				
				<?php
				while($par=mysqli_fetch_array($tot_par))
				{
					$res=mysqli_fetch_array(mysqli_query($link,"select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and paramid='$par[ParamaterId]' and main_tech>0"));
					if($res[result])
					{
						$pname=mysqli_fetch_array(mysqli_query($link,"select * from Parameter_old where ID='$par[ParamaterId]'"));
						?>
						<div class="test_res_par">
							<div class="row">
								
								<div class="span3">
									<span class="par_name"><?php echo $pname[Name];?></span>
								</div>
								
								<div class="span2">
									<?php
									$err_class="";
									if($res[range_status]==1)
									{ 
										$err_class="res_error";
									}
									 
									echo "<div class='$err_class' id='result_$i' contenteditable='true' style='display:inline-block;width:100px''>".$res[result]."</div>";
									
									
									if($apr_allow==1)
									{
										echo "<button class='btn btn-info btn-mini' onclick=update_test_res(this.value,$i,'$pin',$tst[testid],$par[ParamaterId])><i class='icon-save'></i></button>";
									}
									?>
								</div>
								
								<div class="span1">
									<?php
										$p_unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID in(select UnitsID from Parameter_old where Id='$par[ParamaterId]')"));
										echo $p_unit[unit_name];
									?>
								</div>
								
								<div class="span2">
									<?php
										$range=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$res[range_id]'"));
										echo $range[normal_range];
									?>
								</div>
								<?php
								//----Update Result------//
							$upd_res=mysqli_fetch_array(mysqli_query($link,"select * from testresults_update where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]' and paramid='$par[ParamaterId]' order by slno desc"));
							
							if($upd_res[result] && $upd_res[result]!=$res[result])
							{
								echo "<div class='span4'>";
								echo "<div class='upd_res_div'>";
								$emp=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$upd_res[update_by]'"));
								echo "Previous Result: ".$upd_res[result].". Updated by : ".$emp[name];
								echo "</div>";
								echo "</div>";
							}
							//----Update Result------//
								?>
							</div>
							
							<?php
							
							
							
							
							
							?>
							
						</div>
						<hr style="margin: 0;">
						<?php
					}
					$i++;
				}
				
				//-----Note----//
				$tst_note=mysqli_fetch_array(mysqli_query($link,"select * from testresults_note where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]'"));
				if($tst_note[note])
				{
					echo "<div class='tst_note'>";
					echo "<b><u>Note: ".$tst_note[note]."</u></b>";
					echo "</div>";
				}
				
				//-----Note----//
				
				//----------Summary------//
				$tst_sum=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]'"));
				
				if(strip_tags($tst_sum[summary])!='')
				{
					?>
					<div class="tst_summ">
					<br/>
					<button class="btn btn-info btn-mini" onclick="tst_display_summ(<?php echo $i;?>)" id="btn_tst_summ_<?php echo $i;?>" value="hide">Show Summary</button>
					<div id="tst_summ_txt_<?php echo $i;?>" style="display:none">
						<?php echo "<br/>".$tst_sum[summary];?>
					</div>
					<?php
				}
				//-----------------------//
				
				echo "</div>";
			}
		}
	}
	else if($chk_sum[tot]>0) //-----------------Test Summary-----------//
	{
		//-----------Approval/Display Check--------//
			$apr_allow=0;
			if(sizeof($apr_dep)>0)
			{
				if(in_array($tname[type_id],$apr_dep))
				{
					$apr_allow=1;
				}
			} else { $apr_allow=1; }
		//-----------------------------------------//
		
		?>
		<div class="test_res_sum">
				<div class="row">
					<div class="span6">
							<?php echo $tname[testname];?>
							<input type="hidden" id="test_id_<?php echo $i;?>" value="<?php echo $tst[testid];?>"/>
					</div>
					<div class="span2">
					<?php
					if($apr_allow==1)
					{
						?> <span class="sum_edit" onclick="load_sum_edit(<?php echo $tst[testid];?>)"><u>Click to Edit</u></span> <?php
					}
					?>
					</div>
					<div class="span4">
						<span>
						<?php
						$res_sum=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst[testid]'"));
						if($apr_allow==1)
						{
							if($res_sum[doc]>0)
							{
								$dname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res_sum[doc]'"));
								?>
								<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,2,0)" class="aprv_check" checked/>(<?php echo $dname[name];?>)
								<?php
							}
							else
							{
								?>
								<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,2,0)" class="aprv_check" /> <i>(Approve)</i>
								<?php
							}
						}
						else
						{
							if($res_sum[doc]>0)
							{
								$dname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res_sum[doc]'"));
								echo "<i>Approved By $dname[name]</i>";
							}
							else
							{
								echo "<i><u>(Can Not Approve)</u></i>";
							}
						}
						?>
						</span>
					</div>
					
				</div>
				
			<div style="margin-bottom:10px"></div>
			
				<div class="test_sum" id="test_sum_<?php echo $tst[testid];?>">
				
					<?php echo $res_sum[summary];?>
				
				</div>
				<div class="test_sum_edit" id="test_sum_edit_<?php echo $tst[testid];?>" style='display:none'>
					<textarea style='height:350px;width:1100px' name="article-body" id="summary">
						<?php echo $res_sum[summary];?>
					</textarea>
					<div align="center">
						<button class="btn btn-primary btn-mini" onclick="save_summary(<?php echo $tst[testid];?>)"><i class="icon-save"></i> Save</button>
						<button class="btn btn-alert btn-mini" onclick="load_sum_edit_hide(<?php echo $tst[testid];?>)"><i class="icon-off"></i> Cancel</button>
					</div>
				</div>
			
			</div
			<?php
	}
	else if($chk_wid[tot]>0)
	{
		//-----------Approval/Display Check--------//
			$apr_allow=0;
			if(sizeof($apr_dep)>0)
			{
				if(in_array($tname[type_id],$apr_dep))
				{
					$apr_allow=1;
				}
			} else { $apr_allow=1; }
		//-----------------------------------------//
		
		?>
		<div class="test_res">
				<div class="row">
					<div class="span8">
							<?php echo $tname[testname];?>
							<input type="hidden" id="test_id_<?php echo $i;?>" value="<?php echo $tst[testid];?>"/>
					</div>
					<div class="span4">
						<span>
						<?php
						$res_cult=mysqli_fetch_array(mysqli_query($link,"select doc from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch'"));
						if($apr_allow==1)
						{
							if($res_cult[doc]>0)
							{
								$dname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res_cult[doc]'"));
								?>
								<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,3,0)" class="aprv_check" checked/>(<?php echo $dname[name];?>)
								<?php
							}
							else
							{
								?>
								<input type="checkbox" id="aprv_<?php echo $i;?>" name="" onclick="approve(<?php echo $i;?>,3,0)" class="aprv_check" /> <i>(Approve)</i>
								<?php
							}
						}
						else
						{
							if($res_cult[doc]>0)
							{
								$dname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res_cult[doc]'"));
								echo "<i>Approved By $dname[name]</i>";
							}
							else
							{
								echo "<i><u>(Can Not Approve)</u></i>";
							}
						}
						?>
						</span>
					</div>
					
				</div>
				
			<div style="margin-bottom:10px"></div>
			
			<div style="">
						<?php
				$w1=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and slno=1"));
				$w2=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and slno=2"));
				$w3=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and slno=3"));
				$w4=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and slno=4"));
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
							$pat_sum=mysqli_query($link, "select * from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='1227'");
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
								}
								
							}
							?>
					</div>
			
			</div
			<?php
	}
	else
	{
		
		$rem_test[]=$tname[testname];
	}
	
	$i++;
}

//---Remaining Test--//
	 
if(sizeof($rem_test)>0)
{
	?>
	<div class="test_res">
	<b>Remaining Test <br/></b>
	 <?php
	$rm=implode(",",$rem_test);
	echo $rm;
}
?> 
</div> 



</div>
