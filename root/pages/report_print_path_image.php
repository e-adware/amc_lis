<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="../../css/report.css" rel="stylesheet" type="text/css">
		<script src="../../js/jquery.js"></script> 
		<style>
			#his_img { border:1px solid;border-radius:5px}
			h3{ text-transform:capitalize }
		</style>
		<script>
			function load_normal(uhid,param,val,no)
			{
				$.post("pathology_normal_range_new.php",
				{
					uhid:uhid,
					param:param,
					val:val
				},
				function(data,satus)
				{
					
					var data=data.split("#");
					
					$("#norm_r"+no).html(data[0]);
								
								
					if(data[1]=="Error")
					{
						$("#chk_res"+no).text("*");
					}
				})
			}
			function save_print_test(tst,opd_id,ipd_id,uhid,batch_no)
			{
				//~ $.post("report_print_path_save.php",
				//~ {
					//~ tst:tst,
					//~ opd_id:opd_id,
					//~ ipd_id:ipd_id,
					//~ uhid:uhid,
					//~ batch_no:batch_no,
					//~ type:"sing"
				//~ },
				//~ function(data,status)
				//~ {
					//~ window.opener.load_test_detail(data);
				//~ })
			}
			
		</script>
	</head>
	<body onafterprint="save_print_test('<?php echo $_GET['tests'];?>','<?php echo $_GET['opd_id'];?>','<?php echo $_GET['ipd_id'];?>','<?php echo $_GET['uhid'];?>','<?php echo $_GET['batch_no'];?>')">
		<?php
			include("../../includes/connection.php");
			$uhid=$_GET['uhid'];
			$opd_id=$_GET['opd_id'];
			$ipd_id=$_GET['ipd_id'];
			$batch_no=$_GET['batch_no'];
			$tests=$_GET['tests'];
			
			$nb_text_patho=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='1' "));
			
			//$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_reg_details where patient_id='$uhid' and visit_no='$visit'"));
			if($opd_id!="")
			{
				$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			}else if($ipd_id!="")
			{
				$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `ipd_pat_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd_id' "));
			}
			$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testmaster where testid='$tst'"));
			$meth=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_methods where id='$tname[method]'"));
			$lab_no=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and paramid='1196'"));
			
			$samp=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Sample where ID in(select SampleId from TestSample where TestId='$tst')"));

			$user_pad=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select Name from Employee where ID in(select user from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst')"));
			
			$user=$_GET[user];
			$uname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Employee where ID='$user'"));
			?>
		<div class="container">
		<div class="row">
			<div class="col-md-12">
				<span class="text-center">
					<br/><br/><br/><br/>
							<h3><?php echo $tname[type_name];?></h3>
				</span>
				<?php
					$pinfo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$uhid'"));
					//$bill=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from payment_detail where patient_id='$uhid' and visit_no='$visit'"));
					$doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select refbydoctorid,ref_name from  refbydoctor_master where refbydoctorid in(select refbydoctorid from patient_info where patient_id='$uhid')"));
    
                        if($doc[refbydoctorid]!="937")
                        {
							$dname="Dr. ".$doc[ref_name];
                        }
                        else
                        {
							$dname="Self";
                        }
                        //$cname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select centrename from centremaster where centreno in(select centreno from patient_details where patient_id='$uhid' and visit_no='$visit' and centreno!='C100')"));
                        $cname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `company_master` limit 0,1 "));
					
					$phl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
					$lab=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from lab_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
					
					$res_time=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
					
					
					function convert_date($date)
					{
						$timestamp = strtotime($date); 
						$new_date = date('d-M-Y', $timestamp);
						return $new_date;
					}
					?>
					<hr>
					<table width="100%">
					<tr>
							<td><b>Name</b></td>
							<td style="width:300px">
								<b>: <?php echo $pinfo[name];?></b>
								<?php
								if($pinfo[phone])
								{
									echo " / ".$pinfo[phone];	
								}
								?>
							</td>
							<td class="text-left" >Reg. Date/Time</td>
							<td>: <?php echo convert_date($reg[date])."/".$reg[time];?></td>
					</tr>
					<tr>
							<td>Patient ID</td>
							<td><b>: <?php echo $uhid;?></b></td>
							<td class="text-left">Testing Date/Time</td>
							<td>: <?php echo convert_date($res_time[date])."/".$res_time[time];?></td>
					</tr>
					<tr>
							<td>Age/Sex</td>
							<td>: <?php echo $pinfo[age]." ".$pinfo[age_type]." / ".$pinfo['sex'];?></td>
							<td class="text-left">Printing Date/Time</td>
							<td>: <?php echo convert_date(date('Y-m-d'))."/".date('H:i:s');?></td>
					</tr>
					<tr>
							<td>Ref. By</td>
							<td colspan="4">: <?php echo $dname;?></td>
							
					</tr>
					<tr>
							<td>Primary Sample</td>
							<td colspan="2">: <?php echo $samp[Name];?></td>
							<td colspan="2"><i><?php echo $cname[centrename];?></i></td>
					</tr>
					</table>
					<hr>				
				<?php
					?>
				<div style="min-height:580px">
					<?php
						//echo "<b><u>$tname[testname]</u></b>";
						
					?>
					
					<table width="100%">
						
						<tr>
							<td valign="top" width="70%">
								<?php
								$pat_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'");
									
										$summ_all=mysqli_fetch_array($pat_sum);
										echo $summ_all[summary];
										$tech=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "Select Name from Employee where ID='$summ_all[main_tech]'"));
								?>
							</td>
							<td style="text-align:center">
								<?php
								
									$img=mysqli_query($GLOBALS["___mysqli_ston"], "select Path from image_temp where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' order by img_no");
									while($im=mysqli_fetch_array($img))
									{
										$file=explode("/",$im[Path]);
										$nfile=explode(".",$file[2]);
										
										echo "<img src='../$im[Path]' width='280' height='250' id='his_img'/> <br/> $nfile[0] <br/><br/>";
									}
									
								?>
							</td>
						</tr>
						
						
						
						
					
					</table>	
						

					
					
				</div>	
					
				</div>
			</div>
		</div>
		
		<div class="doctors" style="position:fixed">
			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-10">
						<?php
							$doc_nm=1;
							$doc=mysqli_query($GLOBALS["___mysqli_ston"], "select * from lab_doctor where category='1'");
							while($d=mysqli_fetch_array($doc))
							{
							if($doc_nm==1)
							{ ?>
						<div class="row">
							<?php } ?>
							<div class="col-xs-3 text-center">
								<?php
									if($d[id]==$docid)
									{
									echo "<img src='../../sign/$docid.jpg'/>";
									}
									else
									{
									echo "<span style='display: block; height: 60px; width: 60px;'></span>";
									}
									?>
								<p><?php echo $d[name].",".$d[qual];?><br>
									<?php echo $d[desig];?>
								</p>
							</div>
							<?php
								if($doc_nm==4 && $d[id])
								{
								?>
						</div>
						<?php
							$doc_nm=1;
							}
							else
							{
							$doc_nm++;
							}
							
							}
							?>
					</div>
					
					<div class="col-xs-2 nabl-logo">
					<?php
						$nabl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from nabl"));
						if($nabl[nabl]==1)
						{
						?>	
							<img src="../../images/nabl.png"/>
						<?php
						}
						?>
					</div>
				</div>
				<div class="rows">
					<div class="col-xs-4" style="font-size:10px">
						Data entry by: <?php echo $user_pad[Name];?>
					</div>
					<div class="col-xs-4" style="font-size:10px;text-align:center">
						Checked by : <?php echo $user_pad[Name];?>
					</div>
					<div class="col-xs-4" style="font-size:10px;text-align:right">
						Printed by : <?php echo $uname[Name];?>
					</div>
					
				</div>
				<hr>
				<p>
				Analysis Date / Time : <?php echo convert_date($res_time[date])."/".$res_time[time];?> &nbsp;&nbsp;
				
				<?php 
				if($authnticate[d_date])
				{
					?>
						Authentication date / time :<?php echo convert_date($authnticate[d_date])."/".$authnticate[d_time];?> 
					<?php
				}	
				?>	
				 </p>
				
				<div class="row">
					<div class="col-xs-12">
						<p><?php echo $nb_text_patho["nb_text"]; ?></p>
					</div>
				</div>	
				<!--<p>N.B.: The results relate to the sample tested only. Partial reproduction of the report is prohibited.</p>-->
				<script>load_page_no()</script>		
			</div>
		</div>
		
		
	</body>
</html>

