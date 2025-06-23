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
		*{ font-size:12px;}
		.re_table td{ line-height:20px;}
		.re_par { padding-left:20px;}
		.re_sub { padding-left:60px;}
		#testname{ font-size:15px; font-weight:bold;}
		</style>
	</head>
	<body onkeypress="check_online(event)" onafterprint="save_print_test('<?php echo $_GET['tstid'];?>','<?php echo $_GET['opd_id'];?>','<?php echo $_GET['ipd_id'];?>','<?php echo $_GET['uhid'];?>','<?php echo $_GET['batch_no'];?>')">
	
	<div id="loader" style="display:none"></div>
	
		<?php
			include("../../includes/connection.php");
			$uhid=$_GET['uhid'];
			$opd_id=$_GET['opd_id'];
			$ipd_id=$_GET['ipd_id'];
			$batch_no=$_GET['batch_no'];
			$tst=$_GET['tstid'];
			
			$nb_text_patho=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='1' "));
			
			$rp_page=0;
			if($_GET[rp_page]>0)
			{
			$rp_page=$_GET[rp_page];
			}
			
			//$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_reg_details where patient_id='$uhid' and visit_no='$visit'"));
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
			$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testmaster where testid='$tst'"));
			$meth=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_methods where id='$tname[method]'"));
			
			$lab_no=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and paramid='1196'"));
			
			$lab_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
			
			$docid=$lab_doc[doc];
			
			if(!$lab_doc[doc])
			{
				$lab_doc_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
				$docid=$lab_doc_doc[doc];
			}
			
			
			
			
			//$lis=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from test_sample_result where reg_no='$reg[reg_no]' and testid='$tst' and result>0"));
			$lis=0;				
			if($lis>0)
			{
				$l_user="LIIS";
				
			}
			else
			{
				if($lab_doc[tech])
				{
					$l_us_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Employee where ID='$lab_doc[tech]'"));
					
				}
				else
				{
					$l_us_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Employee where ID='$lab_doc[user]'"));
				}
				$l_user=$l_us_name[Name];
			}
			
			
			
			$tech=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Employee where ID='$lab_doc[main_tech]'"));
			
			
			
			$samp=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Sample where ID in(select SampleId from TestSample where TestId='$tst')"));
			
			$dep=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select name from test_department where id='$tname[type_id]'"));
			
			$user=$_GET[user];
			$uname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Employee where ID='$user'"));
			
			include("page_header.php");
			
			?>
			<!--<br/><br/><br/><br/><br/>-->
		<input type="hidden" id="chk_page" value="<?php echo $rp_page;?>"/>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<h3><?php echo $dep[name];?></h3>
					<?php
						$pinfo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$uhid'"));
						//$bill=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from payment_detail where patient_id='$uhid' and visit_no='$visit'"));
						$doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select refbydoctorid,ref_name,qualification from refbydoctor_master where refbydoctorid in(select refbydoctorid from patient_info where patient_id='$uhid' )"));
						
						if($doc["refbydoctorid"]!="101")
						{
							$dname="Dr. ".$doc['ref_name']." , ".$doc['qualification'];
						}
						else
						{
							$dname="Self";
						}
						
						//$cname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select centreno,centrename,onLine,short_name from centremaster where centreno in(select centreno from patient_details where patient_id='$uhid' and visit_no='$visit' and centreno!='C100')"));
						$cname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `company_master` limit 0,1 "));
						$phl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
						$lab=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from lab_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'"));
						
						$authnticate=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select t_time,t_date,d_time,d_date from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
						
						$res_time=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
						
						if(!$res_time[date])
						{
						 $res_time=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
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
						 $vcntr1=substr($cname[short_name],0,15);
						 $vcntr="<b>$vcntr1</b>";
						  
						  $id_pref=mysqli_fetch_array(mysqli_query($link,"select * from id_setup limit 1"));	
						  
						?>
					<table class="table borderless bordert-top-bottom">
						<tr>
							<td><b>UHID</b></td>
							<td><b>: <?php echo $uhid;?></b></td>
							<!--<td><b>Bill Date</b></td>
							<td>: <?php echo convert_date($reg[date]);?></td>-->
							<td><b><?php echo $v_text; ?></b></td>
							<td><b>: <?php echo $v_id; ?></b></td>
						</tr>
						<tr>
							<td width="15%"><b>Name</b></td>
							<td><b>: <?php echo $pinfo[name];?></b></td>
							<td>Collection Time:</td>
							<td>: <?php echo convert_date($reg[date])."/".$reg[time];?></td>
						</tr>
						<tr>
							<td>Age/Sex</td>
							<td>:
							 <?php
							if(strpos($pinfo[age],'.')!==false)
							{
								$age_str="";
								$age=explode(".",$pinfo[age]);
								if($pinfo[age_type]=="Years")
								{
									$age_str.=$age[0]." Years ".$age[1]." Months ";
									if($age[2]>0)
									{
										$age_str.=$age[2]." Days";
									}
								}
								elseif($pinfo[age_type]=="Months")
								{
									$age_str.=$age[0]." Months ".$age[1]." Days";
									
								}
								
								echo $age_str." / ".$pinfo['sex'];
							}
							else
							{
								echo $pinfo[age]." ".$pinfo[age_type]." / ".$pinfo['sex'];
							}
							
							
							?>
							 </td>
							<td>Completion Time</td>
							<td>: <?php echo convert_date($res_time[date])."/".$res_time[time];?></td>
						</tr>
						<!--<tr>
							<td>Ref. By</td>
							<td>: <?php echo $dname;?></td>
							<td colspan="2"></td>
							<td class="text-left">Printing Date/Time</td>
							<td contenteditable="true">: <?php echo convert_date(date('Y-m-d'))."/".date('h:i:s A');?></td>
						</tr>-->
						<tr>
							<?php
							if($cname[name])
							{
								?>
								<td colspan="2"><b><i><?php echo $cname[name];?></i></b></td>
								<td>Primary Sample</td>
								<td>: <?php echo $samp[Name];?></td>
								<?php
							}
							else
							{
								?>
								<td>Primary Sample</td>
								<td>: <?php echo $samp[Name];?></td>
								<td colspan="2" style="text-align:right;"><i><?php echo $cname[name];?></i></td>
								<?php
							}
							?>
						</tr>
					</table>
					<?php
						?>
					<div style="min-height:500px">
					<div id="testname">Test: <?php echo $tname[testname];?></div>
					<table width="50%" class="re_table">
					<?php
					$test_qry=mysqli_query($link,"select * from Testparameter where TestId='$tst' order by sequence");
					while($tq=mysqli_fetch_array($test_qry))
					{
						$par=mysqli_fetch_array(mysqli_query($link,"select * from Parameter_old where ID='$tq[ParamaterId]'"));
						if($par[ResultType]==0)
						{
							echo "<tr><th colspan='2'><u>$par[Name]</u></th></tr>";							
						}
						else
						{
							$class="re_par";
							if($par[ResultType]==8)
							{
								$class="re_sub";
							}
							
							$res=mysqli_fetch_array(mysqli_query($link,"select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' and paramid='$tq[ParamaterId]'"));
							echo "<tr><td class='$class'>$par[Name]</td><td><b>: $res[result]</b></td></tr>";
						}
						
					}
					?>
					</table>	
					<div align="center"><b>---End of Report---</b></div>
					</div>
		
		<div class="doctors" style="position:fixed">
			
			<div class="container-fluid">
				
						<?php
						$nabl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from nabl"));
						if($nabl[nabl]==1)
						{
						?>
						<div class="row">
							<div class="col-xs-10"></div>
							<div class="col-xs-2 text-right  nabl-logo">		
								<!--<img src="../../images/nabl.png"/>-->
							</div>
						</div>
						<?php
						}
						?>
				<div class="row">
					<div class="col-xs-12">
						<?php
							$doc_nm=1;
							$doc=mysqli_query($GLOBALS["___mysqli_ston"], "select * from lab_doctor where category='1' order by sequence");
							while($d=mysqli_fetch_array($doc))
							{
								if($doc_nm==1)
								{ ?>
							<div class="row">
								<?php } ?>
								<div class="span3 text-center tot15">
									<?php
										
										if($d[id]==$docid)
										{
										echo "<img src='../../sign/$docid.jpg'/>";
										}
										else
										{
										echo "<span style='display: block; height: 20px; width: 20px;'></span>";
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
					<!--
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
					-->
				</div>
				<!--
				<div class="rows">
					<div class="col-xs-4" style="font-size:10px">
						Data entry by: <?php echo $l_user;?>
					</div>
					<div class="col-xs-4" style="font-size:10px;text-align:center">
						Checked by : <?php echo $tech[Name];?>
					</div>
					<div class="col-xs-4" style="font-size:10px;text-align:right">
						Printed by : <?php echo $uname[Name];?>
					</div>
				</div>
				<hr>
				-->
				<!--<p>
				Analysis Date / Time : <?php echo convert_date($res_time[date])."/".$res_time[time];?> &nbsp;&nbsp;
				
				<?php 
				if($authnticate[d_date])
				{
					?>
						Authentication date / time :<?php echo convert_date($authnticate[d_date])."/".$authnticate[d_time];?> 
					<?php
				}	
				?>	
				 </p>-->
				
				
				<!--<p>N.B.: The results relate to the sample tested only. Partial reproduction of the report is prohibited.</p>-->
				<script>load_page_no()</script>	
				<div class="row">
					<div class="col-xs-12">
						<p><?php echo $nb_text_patho["nb_text"]; ?></p>
					</div>
				</div>	
				<div class='nabl_text'><?php echo $nabl[text];?></div>      
			</div>
		</div> 
		</div>
		 
		
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
