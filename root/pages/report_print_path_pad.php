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
			table tr.tr_test td:first-child{ font-size:13px; padding-left:30px !important;}			
			#t_bold td {border-bottom:1px solid #000 !important; padding:1px !important;}
			hr {margin:10px;}
		</style>
		<script>
			
			////////////block print////////////
			function check_online(e)
			{
			   var onl=document.getElementById("online_cen").value	;
			   if(onl==1)
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
	        
			function cls_hide()
			{
			  window.opener.hide_div();
			  window.opener.document.getElementById("new").focus();
			  window.close();
			}
            //////////block end//////////////////
			
			
			
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
					if($("#chk_page").val()>0)
					{
						var reg=uhid+"@"+visit;
						window.opener.load_pat_info(reg,'update_p_sim');
					}
					else
					{
						window.opener.load_test_detail(data);
					}
				})
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
	<body onkeypress="check_online(event)" onafterprint="save_print_test('<?php echo $_GET['tstid'];?>','<?php echo $_GET['opd_id'];?>','<?php echo $_GET['ipd_id'];?>','<?php echo $_GET['uhid'];?>','<?php echo $_GET['batch_no'];?>')">
		<?php
			include("../../includes/connection.php");
			$uhid=$_GET['uhid'];
			$opd_id=$_GET['opd_id'];
			$ipd_id=$_GET['ipd_id'];
			$batch_no=$_GET['batch_no'];
			$tst=$_GET['tstid'];
			//$docid=$_GET[doc];
			
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
			$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$tst'"));
			$meth=mysqli_fetch_array(mysqli_query($link, "select * from test_methods where id='$tname[method]'"));
			$lab_no=mysqli_fetch_array(mysqli_query($link, "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and paramid='1196'"));
			$authnticate=mysqli_fetch_array(mysqli_query($link, "select t_time,t_date,d_time,d_date from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
			
			//echo "select Name from Sample where ID in(select SampleId from TestSample where TestId='$tst')";
			$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='$tst')"));
			
			$dep=mysqli_fetch_array(mysqli_query($link, "select name from test_department where id='$tname[type_id]'"));
			
			$user_pad=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID in(select user from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst')"));
			
			$rp_page=0;
			if($_GET[rp_page]>0)
			{
				$rp_page=$_GET[rp_page];
			}
			
			$user=$_GET[user];
			$uname=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$user'"));
			
			$nb_text_patho=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='1' "));
			
			?>
			<br/><br/><br/><br/><br/>
			<input type="hidden" id="chk_page" value="<?php echo $rp_page;?>"/>
		<div class="container-fluid">
			<div class="row">
				<div class="span10">
					<h3><?php echo $dep[name];?></h3>
					<?php
						$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));
						
						$doc=mysqli_fetch_array(mysqli_query($link, "select refbydoctorid,ref_name,qualification from refbydoctor_master where refbydoctorid in( SELECT `refbydoctorid` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$v_id' )"));
						
					  if($doc["refbydoctorid"]!="101")
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
						$lab=mysqli_fetch_array(mysqli_query($link, "select time,date from lab_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
						
						$res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
						//$cname=mysqli_fetch_array(mysqli_query($link, "select centreno,centrename,onLine,short_name from centremaster where centreno in(select centreno from patient_details where patient_id='$uhid' and visit_no='$visit' and centreno!='C100')"));
						//$cname=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));
						
						if(!$res_time[date])
						{
							$res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));	
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
						  
						$id_pref=mysqli_fetch_array(mysqli_query($link,"select * from id_setup limit 1"));	
						
						$equip=mysqli_fetch_array(mysqli_query($link,"select equipment from testmaster where testid='$tst'"));
						
						$uhid_id=$uhid;
						$sample_name=$samp[Name];
						include("report_patient_header.php");
						?>
										
					<div style="min-height:550px">
						<table class="table borderless">
							<tr>
								<td width="15%"><b>TEST NAME</b></td>
								<td>:</td>
								<td>
									<?php
									$dup1=0;
									$chk_dup1=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
									if($chk_dup1>0)
									{
										$dup1=1;
									}
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
									<b><?php echo $nbl_star.$tname[testname];?></b>
								</td>
							</tr>
							<tr>
								
								<td colspan="5">
									<?php
										$pat_sum=mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'");
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
											$chk_sum=mysqli_query($link, "select * from test_summary where testid='$tst'");
											$num_sum=mysqli_num_rows($chk_sum);
											if($num_sum>0)
											{
												$summ_all=mysqli_fetch_array($chk_sum);
												echo $summ_all[summary];
											}
											
										}
										?>
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
</style>
