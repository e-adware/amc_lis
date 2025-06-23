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
		</script>
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
			
			$lab_doc=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
			
			if(!$lab_doc[doc])
			{
				$lab_doc=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 1"));
			}
			$docid=$lab_doc[doc];
			
			
			
			//$lis=mysqli_num_rows(mysqli_query($link,"select * from test_sample_result where reg_no='$reg[reg_no]' and testid='$tst' and result>0"));
			$lis=0;				
			if($lis>0)
			{
				$l_user="LIIS";
			}
			else
			{
				if($lab_doc[tech])
				{
					$l_us_name=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$lab_doc[tech]'"));
				}
				else
				{
					$l_us_name=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$lab_doc[user]'"));
				}
				$l_user=$l_us_name[Name];
			}
			
			
			
			$tech=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$lab_doc[main_tech]'"));
			
			
			
			$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='$tst')"));
			
			$dep=mysqli_fetch_array(mysqli_query($link, "select name from test_department where id='$tname[type_id]'"));
			
			$user=$_GET[user];
			$uname=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$user'"));
			
			include("page_header.php");
			
			?>
			<!--<br/>-->
		<input type="hidden" id="chk_page" value="<?php echo $rp_page;?>"/>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<h3><?php echo $dep[name];?></h3>
					<?php
						$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));
						//$bill=mysqli_fetch_array(mysqli_query($link, "select * from payment_detail where patient_id='$uhid' and visit_no='$visit'"));
						
						$doc=mysqli_fetch_array(mysqli_query($link, "select refbydoctorid,ref_name,qualification from refbydoctor_master where refbydoctorid in( SELECT `refbydoctorid` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$v_id' )"));
						
						if($doc[refbydoctorid]!="101")
						{
							$dname="Dr. ".$doc['ref_name']." , ".$doc['qualification'];
						}
						else
						{
							$dname="Self";
						}
						
						//$cname=mysqli_fetch_array(mysqli_query($link, "select centreno,centrename,onLine from centremaster where centreno in(select centreno from patient_details where patient_id='$uhid' and visit_no='$visit' and centreno!='C100')"));
						//$cname=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));
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
						
						$authnticate=mysqli_fetch_array(mysqli_query($link, "select t_time,t_date,d_time,d_date from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
						
						$res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst' limit 0,1"));
						
						if(!$res_time[date])
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
						 $vcntr1=substr($cname[name],0,15);
						 $vcntr="<b>$vcntr1</b>";
						  
						?>
					<table class="table borderless bordert-top-bottom">
						<tr>
							<td><b>UHID</b></td>
							<td><b>: <?php echo $pinfo['patient_id'];?></b></td>
							
							<td><b><?php echo $v_text; ?></b></td>
							<td><b>: <?php echo $v_id; ?></b></td>
						</tr>
						<tr>
							<td><b>Name</b></td>
							<td>
								<b>: <?php echo $pinfo[name];?></b>
								<?php
									if($pinfo[phone])
									{
										echo " / ".$pinfo[phone];
									}
									?>
							</td>
							<td class="text-left">Collection Time</td>
							<td>: <?php echo convert_date($collection_date)."/".convert_time($collection_time);?></td>
							<input type="hidden" id="docaprvd" value="<?php echo $lab_doc[doc];?>"/>
						</tr>
						<tr>
							<td>Age/Sex</td>
							<td>: <?php echo $pinfo[age]." ".$pinfo[age_type]." / ".$pinfo[sex];?></td>
							<td>Completion Time</td>
							<td>: <?php echo convert_date($res_time[date])."/".convert_time($res_time[time]);?></td>
							<!--<td class="text-left">Printing Date/Time</td>
							<td contenteditable="true">: <?php echo convert_date(date('Y-m-d'))."/".date('h:i:s A');?></td>-->
						</tr>
						<!--<tr>
							<td>Ref. By</td>
							<td colspan="3">: <?php echo $dname;?></td>
						</tr>-->
						<tr>
							<td>Primary Sample</td>
							<td >: <?php echo $samp[Name];?></td>
							<td class="text-left"></td>
							<td  colspan="2"><?php echo $vcntr;?></td>
						</tr>
					</table>
					<?php
						?>
					<div style="min-height:500px">
						<p class="text-center"><b><u>Mantoux Test</u></b></p>
						<?php
							$i=0;
							$m_r=mysqli_query($link,"select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'");
							while($m_res=mysqli_fetch_array($m_r))
							{
								$a[$i]=$m_res[result];	
								$i++;
							}
							?>
						<br/><br/>
						<div>
							Erythema <?php echo $a[0];?> mm and Induration <?php echo $a[1];?> mm noted at the site of innoculation of 
							PPD of strength <?php echo $a[2];?> TU after 48 to 72 hours.
							<br/><br/>
							<b>IMPRESSION:</b> Patient is <?php echo $a[3];?> HYPERSENSITIVE to tuberculin in the strength used.
						</div>
						<p class="text-center" style="margin-top: 20px;"><b>----End of report----</b></p>
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
							$doc=mysqli_query($link, "select * from lab_doctor where category='1'");
							while($d=mysqli_fetch_array($doc))
							{
							if($doc_nm==1)
							{ ?>
						<div class="row">
							<?php } ?>
							<div class="span3 text-center">
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
						$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
						if($nabl[nabl]==1)
						{
						?>	
							<img src="../../images/nabl.png"/>
						<?php
						}
						?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12">
						<p><?php echo $nb_text_patho["nb_text"]; ?></p>
					</div>
				</div>	
				<!--<p>N.B.: The results relate to the sample tested only. Partial reproduction of the report is prohibited.</p>-->
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
