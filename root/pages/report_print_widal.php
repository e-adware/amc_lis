<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="../../css/report.css" rel="stylesheet" type="text/css">
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
					$("#norm_r"+no).text(data[0]);
					
					if(data[1]=="Error")
					{
						$("#chk_res"+no).text("*");
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
					if($("#chk_page").val()>0)
					{
						//var reg=uhid+"@"+visit;
						var reg=uhid;
						window.opener.load_pat_info(reg,'update_p_sim');
					}
					else
					{
						window.opener.load_test_detail(data);
					}
					window.close();
				})
			}
			function load_duplicate(val,chk)
			{
				if(chk>0)
				{
					//$(".dupl_"+val+"").html("<b><i>#DUPLICATE</i></b>");
				}
			}
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
			
		</style>
	</head>
	<br/><br/><br/><br/>
	<body onkeypress="close_window(event)" onafterprint="save_print_test('1227','<?php echo $_GET['opd_id'];?>','<?php echo $_GET['ipd_id'];?>','<?php echo $_GET['uhid'];?>','<?php echo $_GET['batch_no'];?>')">
		<?php
			include("../../includes/connection.php");
			$uhid=$_GET['uhid'];
			$opd_id=$_GET['opd_id'];
			$ipd_id=$_GET['ipd_id'];
			$batch_no=$_GET['batch_no'];
			$tst=$_GET['tstid'];
			//$docid=$_GET[doc];
			
			$nb_text_patho=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='1' "));
			
			//include("page_header.php");
			
			?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<h3>Pathology Report</h3>
					<div style="text-align:right;width:100%" class="dupl_1"></div>
					<?php
						$pinfo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$uhid'"));
						//$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_reg_details where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' "));
						if($opd_id!="")
						{
							$v_text="OPD ID";
							$v_id=$opd_id;
							$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
						}else if($ipd_id!="")
						{
							$v_text="IPD ID";
							$v_id=$ipd_id;
							$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `ipd_pat_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd_id' "));
						}
						$v_text="PIN";		
						$phl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from phlebo_sample where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
						if($phl)
						{
							$collection_date=$phl["date"];
							$collection_time=$phl["time"];
						}else
						{
							$collection_date=$reg["date"];
							$collection_time=$reg["time"];
						}
						
						$res_time=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from widalresult where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' limit 0,1"));
						
						$samp=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Sample where ID in(select SampleId from TestSample where TestId='$tst')"));
						
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
						
						$doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select refbydoctorid,ref_name from  refbydoctor_master where refbydoctorid in(select refbydoctorid from patient_info where patient_id='$uhid' )"));
						
						if($doc["refbydoctorid"]!="101")
						{
							$dname="Dr. ".$doc['ref_name']." , ".$doc['qualification'];
						}
						else
						{
							$dname="Self";
						}
						
						$lab_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from widalresult where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' limit 1"));
						$docid=$lab_doc[doc];
						
						$rp_page=0;
						if($_GET[rp_page]>0)
						{
							$rp_page=$_GET[rp_page];
						}
						
						$user_wid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select Name from Employee where ID in(select v_User from widalresult where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' )"));
						$user_wid_t=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select Name from Employee where ID in(select main_tech from widalresult where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' )"));
						
						$user=$_GET[user];
						$uname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Employee where ID='$user'"));
						
						$equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='1227'"));
						
						/*-----Patient Demo-----*/
							
							$uhid_id=$uhid;
							$sample_name=$samp[Name];
							include("report_patient_header.php");
							
						/*-----Patient Demo-----*/
						
						$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$tst'"));
						$dup1=0;
						$chk_dup1=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
						if($chk_dup1>0)
						{
							$dup1=1;
						}
						
						?>
						<br/>
						
					<p class="text-center"><b><u>Widal Test</u></b></p>
					<p>Patient's are found reactive to Salmonella typhi antigense "O" and "H" and paratyphi antigens "AH" and "BH" in the follwing dilution :</p>
					<div style="min-height:500px;">
						<?php
							$w1=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and slno=1"));
							$w2=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and slno=2"));
							$w3=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and slno=3"));
							$w4=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and slno=4"));
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
							$pat_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'");
							$num_pat=mysqli_num_rows($pat_sum);
							
							if($num_pat>0)
							{
								$pat_s=mysqli_fetch_array($pat_sum);
								echo $pat_s[summary];	
							}
							else
							{
								/*
								$chk_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_summary where testid='$tst'");
								$num_sum=mysqli_num_rows($chk_sum);
								if($num_sum>0)
								{
									$summ_all=mysqli_fetch_array($chk_sum);
									echo $summ_all[summary];
								}
								*/
							}
							?>
					</div>
					<p class="text-center" style="margin-top: 20px;"><b>----End of report----</b></p>
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
