<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="../../css/report.css" rel="stylesheet" type="text/css">
		<link href="../../css/custom.css" rel="stylesheet" type="text/css">
		<script src="../../js/jquery.js"></script> 
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
				//$("#chk_res"+no).text("*");
				}
			})
			}
			function save_print_test(tst,opd,ipd,uhid,batch_no)
			{
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
					window.opener.load_test_detail(data);
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
			function load_duplicate(val,chk)
			{
				if(chk>0)
				{
					//$(".dupl_"+val+"").html("<b><i>#DUPLICATE</i></b>");
				}
			}
		</script>
		<style>
			
		</style>
	</head>
	<body onkeypress="close_window(event)" onafterprint="save_print_test('<?php echo $_GET['tstid'];?>','<?php echo $_GET['opd_id'];?>','<?php echo $_GET['ipd_id'];?>','<?php echo $_GET['uhid'];?>','<?php echo $_GET['batch_no'];?>')">
		<?php
			include("../../includes/connection.php");
			$uhid=$_GET['uhid'];
			$opd_id=$_GET['opd_id'];
			$ipd_id=$_GET['ipd_id'];
			$batch_no=$_GET['batch_no'];
			$tst=$_GET['tstid'];
			//$docid=$_GET[doc];
			
			$nb_text_patho=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='1' "));
			
					
			?>
			<br/><br/><br/><br/>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<span class="text-center">
						<h3>Microbiology</h3>
					</span>
					<?php
						$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));
						//$reg=mysqli_fetch_array(mysqli_query($link, "select * from patient_reg_details where patient_id='$uhid' and visit_no='$visit'"));
						
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
						 $doc=mysqli_fetch_array(mysqli_query($link, "select refbydoctorid,ref_name,qualification from refbydoctor_master where refbydoctorid in( SELECT `refbydoctorid` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$v_id' )"));
						
						if($doc[refbydoctorid]!="101")
						{
							$dname=$doc['ref_name']." , ".$doc['qualification'];
						}
						else
						{
							$dname="Self";
						}
						
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
						
						$res_time=mysqli_fetch_array(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
						
						$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='$tst')"));
						
						function convert_date($date)
						{
							$timestamp = strtotime($date); 
							$new_date = date('d-M-Y', $timestamp);
							return $new_date;
						}
						// Time format convert
						function convert_time($time)
						{
							$time = date("g:i A", strtotime($time));
							return $time;
						}
						$lab_doc=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
						$docid=$lab_doc[doc];
						
						$usr_c=mysqli_fetch_array(mysqli_query($link,"Select Name from Employee where ID='$res_time[tech]'"));
						$usr_t=mysqli_fetch_array(mysqli_query($link,"Select Name from Employee where ID='$res_time[main_tech]'"));
						
						$user=$_GET[user];
						$uname=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$user'"));
						
						
						/*-----Patient Demo-----*/
							
							$uhid_id=$uhid;
							$sample_name=$samp[Name];
							include("report_patient_header.php");
							
						/*-----Patient Demo-----*/
						
						
						
						?>
					
					
					
				</div>
				<div>
					<?php
						$tname=mysqli_fetch_array(mysqli_query($link, "select testname from testmaster where testid='$tst'"));
						$spec1=explode(" ",$tname[testname]);
						$spec_s=sizeof($spec1);
						$spec=array_pop($spec1);
						
						$col=mysqli_fetch_array(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and paramid='311'"));
						$num=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and paramid='630'"));
						
						$chk_dup1=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
						if($chk_dup1>0)
						{
							$dup1=1;
						}
						 
						?>
					<div align="center">
						<b><u><?php echo $tname[testname];?></u></b>
					</div>
					<div style="height:600px">
						<?php
							if($num<1)
							{
							$fung=mysqli_fetch_array(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and paramid='310'"));
							
							?>
						<table width='100%' class="table table-condensed table-no-top-border">
							<tr>
								<th>Speciman</th>
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
										$power=mysqli_fetch_array(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and paramid='312'"));
										if($power[result])
										{
										echo "<sup>$power[result]</sup>";
										}
										?> 
										CFU/ml of <?php echo $samp[Name];?>
								</td>
							</tr>
						</table>
						<br/><br/>
						<table width="100%" class="table table-condensed table-no-top-border">
							<tr>
								<th contentEditable="true" width="33%">SENSITIVE</th>
								<th contentEditable="true" width="33%">INTERMEDIATE</th>
								<th contentEditable="true" width="33%">RESISTANT</th>
							</tr>
							<tr>
								<td valign="top">
									<?php
										$sen=mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and result='S' order by paramid");
										while($s=mysqli_fetch_array($sen))
										{
										$pn=mysqli_fetch_array(mysqli_query($link, "select Name from Parameter_old where ID='$s[paramid]'"));
										echo $pn[Name]."<br/>";
										}
										?>
								</td>
								<td valign="top">
									<?php
										$sen=mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and result='I' order by paramid");
										while($s=mysqli_fetch_array($sen))
										{
										$pn=mysqli_fetch_array(mysqli_query($link, "select Name from Parameter_old where ID='$s[paramid]'"));
										echo $pn[Name]."<br/>";
										}
										?>
								</td>
								<td valign="top">
									<?php
										$sen=mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and result='R' order by paramid");
										while($s=mysqli_fetch_array($sen))
										{
										$pn=mysqli_fetch_array(mysqli_query($link, "select Name from Parameter_old where ID='$s[paramid]'"));
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
						<!--
							<table width='60%' style="display:none">
							<tr>
							<th>Speciman</th>
							<td>: <?php echo $spec1[2];?></td>
							</tr>
							<tr>
							<th>Result</th>
							<td contentEditable="true">: Aerobic culture at 37&#176; C for 48 hrs reveals Non Pathogenic</td>
							</tr>
							</table>
							-->
						<div style='text-align:left;margin-left:20px'>
							<br/>
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
								$chk_sum=mysqli_query($link, "select * from test_summary where testid='$tst'");
								$num_sum=mysqli_num_rows($chk_sum);
								if($num_sum>0)
								{
								$summ_all=mysqli_fetch_array($chk_sum);
								echo $summ_all[summary];
								}
								
								}
								?>
						</div>
					</div>
					<?php
						}
						?>
				</div>
			</div>
		</div>
		
		<!-----Doctors Part----->
		
		<div class="doctors-container">		
		<div class="doctors" style="position:absolute;bottom:-150px">
			<?php
				$aprv_by=$res_time[doc];
				$entry_by=$res_time[user];
				$analysis_by=$res_time[main_tech];
				include('report_doctor_footer.php'); 
			?>
		</div>
	    </div>
		<!-----Doctor's Part----->
		
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
	margin-left:1cm;
	margin-right:0.5cm;
}
</style>
<script>window.print()</script>
