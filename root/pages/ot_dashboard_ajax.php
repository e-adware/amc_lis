<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");
$type=$_POST['type'];

// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}
// Date format convert
function convert_date_g($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

if($type==1)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	$usr=$_POST['usr'];
	$leave=mysqli_fetch_array(mysqli_query($link,"SELECT `leaved` FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($leave['leaved']==1)
	{
		$disb="disabled='disabled'";
	}
	if($leave['leaved']==0)
	{
		$disb="";
	}
	$ds=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `type`='3' ORDER BY `batch_no` DESC");
	$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `type`='3'");
	$num=mysqli_num_rows($q);
	$no=mysqli_num_rows($ds);
	?>
	<div class="span5" style="margin-left:0px;">
	<?php
	if($no>0)
	{
		while($res=mysqli_fetch_array($ds))
		{
			$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' AND `type`='3'"));
			$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' AND `type`='3'"));
			echo "<button type='button' class='btn btn-default bt' id='ad".$res['batch_no']."' onclick='view_batch(".$res['batch_no'].")'><span class='sp'>Tests=".$nn."</span><span class='sp'>Date: ".$dt['date']."</span><span class='sp'>Time: ".$dt['time']."</span></button><br/>";
		}
	}
	if($num>0)
	{
	?>
	<button type="button" class="btn btn-info" id="adm" onclick="ad_tests()" <?php echo $disb;?> style=""><i class="icon-plus"></i> Add New Batch</button>
	<?php
	}
	else
	{
	?>
	<button type="button" class="btn btn-info" id="ad" onclick="ad_tests()" <?php echo $disb;?> style=""><i class="icon-plus"></i> Add</button>
	<?php
	}
	?>
	</div>
	<div id="batch_details" class="span5" style="margin-left:-40px;max-width:550px;min-width:540px;"></div>
	<style>
		.widget-content{border-bottom:none;}
		.sp{margin-left:10px;}
		.bt{margin-bottom:5px;}
	</style>
	<?php
}

if($type==2)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	?>
	<div id="test_sel">
		<div id="list_all_test" style="" class="up_div"></div>
		<!--<h5 class="text-left" onClick="load_tab(2,'a')">Test Details For</h5>-->
		<table class="table">
			<tr>
				<th><label for="test">Select Test</label></th>
				<td><input type="text" name="test" id="test" class="span6" onFocus="test_enable()" onKeyUp="select_test_new(this.value,event)" /><input type="text" name="batch" id="batch" style="display:none;" value="<?php echo $batch;?>" /></td>
			</tr>
			<tr>
				<td colspan="4">
					<div id="test_d">
						
					</div>
				</td>
			</tr>
		</table>
		</div>
		<div id="ss_tests">
			<?php
			$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `type`='3'");
			$num=mysqli_num_rows($q);
			if($num>0)
			{
			?>
			<table class='table table-condensed table-bordered' style='style:none' id='test_list'>
				<tr>
					<th style='background-color:#cccccc'>Sl No</th><th style='background-color:#cccccc'>Tests</th><th style='background-color:#cccccc'>Remove</th>
				</tr>
				<?php
				$i=1;
				while($r=mysqli_fetch_array($q))
				{
					$t=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$r[testid]'"));
					$t_res1=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
					$t_res2=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults_card` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
					$t_res3=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
					if($r['testid']==1227)
					{
						$t_res4=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch'"));
					}
					$t_res5=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
				?>
				<tr>
					<td><?php echo $i;?></td>
					<td width='80%'><?php echo $t['testname'];?><input type='hidden' value='<?php echo $r['testid'];?>' class='test_id'/></td>
					<?php
					if($t_res1>0 || $t_res2>0 || $t_res3>0 || $t_res4>0 || $t_res5>0)
					{
					?>
					<td></td>
					<?php
					}else{
					?>
					<td onclick='delete_rows(this,2)'><span class='text-danger' style='cursor:pointer'><i class='icon-remove'></i></span></td>
					<?php
					}
					?>
				</tr>
				<?php
				$i++;
				}
				?>
			</table>
			<?php
			}
			?>
		</div>
	</div>
	<?php
}

if($type==3)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	$shed=$_POST['shed'];
	$test=$_POST['test'];

	if($test=="")
	{
		$q="select * from testmaster order by testname";
	}
	else
	{
		$q="select * from testmaster where testname like '$test%' order by testname";
	}

	$data=mysqli_query($link, $q);
	?>

	<table class="table   table-bordered table-condensed" border="1" id="test_table" width="100%">
		<tr>
			<th>Sl No</th>
			<th>Test Name</th>
			<!--<th>Rate</th>--><div id="msgg" style="display:none;position:absolute;top:15%;left:45%;font-size:22px;color:#d00;"></div>
		</tr>
	<?php
	$i=1;
	while($d=mysqli_fetch_array($data))
	{
		$drate=$d['rate'];
		
		?>
		<tr <?php echo "id=td".$i;?> onclick="$('#test').focus()" style="cursor:pointer">
			<td width="5%" class=test<?php echo $i;?> id=test<?php echo $i;?>>
				<?php echo $i;?><input type="hidden" class="test<?php echo $i;?>" value="<?php echo $d['testid'];?>"/>
			</td>
			<td style="text-align:left" width="35%" <?php echo "class=test".$i;?>>
				<?php echo $d['testname'];?>
			</td>
		</tr>
		<?php
		$i++;
	}
		
	?>
	</table>
<?php
}

if($type==4)
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	$shed=$_POST['shed'];
	$usr=$_POST['usr'];
	$tst=$_POST['tst'];
	$test=explode(",",$tst);
	$ar=sizeof($test);
	if($batch>0)
	{
		$bch=$batch;
	}
	else
	{
		$bt=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(batch_no) as max FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		$bch=$bt['max']+1;
	}
	$bed=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_id` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	mysqli_query($link," DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' AND `type`='3'");
	foreach($test as $test)
	{
		if($test)
		{
			$sam=mysqli_fetch_array(mysqli_query($link,"SELECT `SampleId` FROM `TestSample` WHERE `TestId`='$test'"));
			$rt=mysqli_fetch_array(mysqli_query($link,"SELECT `testname`, `rate` FROM `testmaster` WHERE `testid`='$test'"));
			mysqli_query($link,"INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `date`, `time`, `user`, `type`) VALUES ('$uhid','$opd','$ipd','$bch','$test','$sam[SampleId]','$rt[rate]','$date','$time','$usr','3')");
			// type=3, ot investigation add;
			mysqli_query($link,"INSERT INTO `ot_pat_service_details`(`patient_id`, `ipd_id`, `schedule_id`, `resourse_id`, `emp_id`, `ot_group_id`, `ot_service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','$shed','0','0','104','$test','$rt[testname]','1','$rt[rate]','$rt[rate]','0','$usr','$time','$date','$bed[bed_id]')");
			
			mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','104','$test','$rt[testname]','1','$rt[rate]','$rt[rate]','0','$usr','$time','$date','$bed[bed_id]')");
			
			$last_slno_test=mysqli_fetch_array(mysqli_query($link," SELECT `slno` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' AND `testid`='$test' AND `user`='$usr' ORDER BY `slno` DESC "));
			
			$last_slno_service=mysqli_fetch_array(mysqli_query($link," SELECT `slno` FROM `ot_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed' AND `ot_service_id`='$test' AND `user`='$usr' ORDER BY `slno` DESC "));
			
			$last_slno_service_ipd=mysqli_fetch_array(mysqli_query($link," SELECT `slno` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$test' AND `user`='$usr' ORDER BY `slno` DESC "));
			
			mysqli_query($link," INSERT INTO `ot_link_test_service`(`test_slno`, `service_slno`) VALUES ('$last_slno_test[slno]','$last_slno_service[slno]') ");
			
			mysqli_query($link," INSERT INTO `link_test_service`(`test_slno`, `service_slno`) VALUES ('$last_slno_test[slno]','$last_slno_service_ipd[slno]') ");
		}
	}
}

if($type==5)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch_no'];
	$shed=$_POST['shed'];
	$usr=$_POST['user'];
	
	$leave=mysqli_fetch_array(mysqli_query($link,"SELECT `leaved` FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($leave['leaved']==1)
	{
		$disb="disabled='disabled'";
	}
	if($leave['leaved']==0)
	{
		$disb="";
	}
	
	$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `type`='3'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$d=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `type`='3'"));
	?>
	<table class="table table-condensed table-bordered" style="margin-bottom: 2px;">
		<tr>
			<th>SN</th><th width="40%">Test Name</th><th>Date: <?php echo $d['date']." ".$d['time'];?></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			$tst_cat=mysqli_fetch_array(mysqli_query($link," SELECT `category_id` FROM `testmaster` WHERE `testid`='$r[testid]' "));
			$tst=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$r[testid]'"));
			if($tst_cat['category_id']==1)
			{
				$bt=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
			}
			if($tst_cat['category_id']==2)
			{
				$bt=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
			}
			if($tst_cat['category_id']==3)
			{
				$bt=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testresults_card` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$r[testid]'"));
			}
			if($bt>0)
			{
				$rep_btn="<button class='btn btn-mini btn-success' onclick=rep_pop('$uhid','$ipd','$batch','$r[testid]','$tst_cat[category_id]')>Report</button>";
			}
			else
			{
				$rep_btn="";
			}
		?>
		<tr>
			<td><?php echo $n;?></td><td colspan="2"><?php echo $tst['testname'];?><span class="text-right"><?php echo $rep_btn;?></span></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<input type="button" class="btn btn-info" id="adb" value="Add More Test" <?php echo $disb;?> onclick="ad_tests('<?php echo $batch;?>')" style="" />
	<!--<input type="button" class="btn btn-info" id="rcv" value="Receive Sample" <?php echo $disb;?> onclick="rcv_sample('<?php echo $uhid;?>','<?php echo $ipd;?>','<?php echo $batch;?>')" style="" />-->
	<?php
	}
}

if($type==6)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	
	
	$leave=mysqli_fetch_array(mysqli_query($link,"SELECT `leaved` FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($leave['leaved']==1)
	{
		$disb="disabled='disabled'";
	}
	if($leave['leaved']==0)
	{
		$disb="";
	}
	
	$qry=mysqli_query($link,"SELECT * FROM `ot_pre_anaesthesia` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		$d=mysqli_fetch_array($qry);
		$systolic=$d['systolic'];
		$diastolic=$d['diastolic'];
		$rr=$d['rr'];
		$temp=$d['temp'];
		$weight=$d['weight'];
		$hr=$d['hr'];
		$aps=$d['aps'];
		$hb=$d['hb'];
		$tlc=$d['tlc'];
		$dlc=$d['dlc'];
		$esr=$d['esr'];
		$pcv=$d['pcv'];
		$fbs=$d['fbs'];
		$ppbs=$d['ppbs'];
		$rbs=$d['rbs'];
		$urea=$d['urea'];
		$creat=$d['creatinine'];
		$sod=$d['sodium'];
		$pot=$d['potassium'];
		$cl=$d['chlorine'];
		$ca=$d['calcium'];
		$mg=$d['magnesium'];
		$l_other=$d['lab_other'];
		$bt=$d['bt'];
		$ct=$d['ct'];
		$pt=$d['pt'];
		$aptt=$d['aptt'];
		$inr=$d['inr'];
		$plat=$d['platelets'];
		$protein=$d['protein'];
		$alb=$d['alb'];
		$biliru=$d['biliru'];
		$ldh=$d['ldh'];
		$amyl=$d['amyl'];
		$alkphos=$d['alk_phos'];
		$choles=$d['cholestrol'];
		$trigl=$d['trigl'];
		$ldl=$d['ldl'];
		$hdl=$d['hdl'];
		$vldl=$d['vldl'];
		$hbs=$d['hbs'];
		$hiv=$d['hiv'];
		$t3=$d['t3'];
		$t4=$d['t4'];
		$tsh=$d['tsh'];
		$dvt=$d['dvt'];
		$nmb=$d['nmb'];
		$consent=$d['consent'];
		$consult=$d['consult'];
		$sent_date=$d['sent_date'];
		$sent_time=$d['sent_time'];
		$prophylaxis=$d['prophylaxis'];
		$drugs=$d['drugs'];
		$invest=$d['invest'];
		$others=$d['others'];
		$fit=$d['fit'];
		$aps=$d['aps'];
	}
	else
	{
		$systolic="";
		$diastolic="";
		$rr="";
		$temp="";
		$weight="";
		$hr="";
		$aps="";
		$hb="";
		$tlc="";
		$dlc="";
		$esr="";
		$pcv="";
		$fbs="";
		$ppbs="";
		$rbs="";
		$urea="";
		$creat="";
		$sod="";
		$pot="";
		$cl="";
		$ca="";
		$mg="";
		$l_other="";
		$bt="";
		$ct="";
		$pt="";
		$aptt="";
		$inr="";
		$plat="";
		$protein="";
		$alb="";
		$biliru="";
		$ldh="";
		$amyl="";
		$alkphos="";
		$choles="";
		$trigl="";
		$ldl="";
		$hdl="";
		$vldl="";
		$hbs="";
		$hiv="";
		$t3="";
		$t4="";
		$tsh="";
		$dvt="";
		$nmb="";
		$consent="";
		$consult="";
		$sent_date="";
		$sent_time="";
		$prophylaxis="";
		$drugs="";
		$invest="";
		$others="";
		$fit="";
		$aps="";
	}
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `blood_group` FROM `patient_info` WHERE `patient_id`='$uhid'"));
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th colspan="6" style="background:#dddddd;">Vitals</th>
		</tr>
		<tr>
			<th colspan="2">BP<br/>
			<input id="systolic" value="<?php echo $systolic; ?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" placeholder="Systolic" />
			<input id="diastolic" value="<?php echo $diastolic; ?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" placeholder="Diastolic" />
			<th>RR:<br/><input type="text" id="rr" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $rr; ?>" placeholder="RR" /></th>
			<th>Temp:<br/><input type="text" id="temp" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $temp; ?>" placeholder="Temp" /></th>
			<th>Weight:<br/><input type="text" id="weight" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $weight; ?>" placeholder="Weight" /></th>
			<th>H.R/Pulse Rate:<br/><input type="text" id="hr" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $hr; ?>" placeholder="H.R" /></th>
		</tr>
		<tr>
			<th colspan="6">
				ASA Physical Status
				<select id="aps" class="span5" onkeyup="tab(this.id,event)" style="">
					<option value="0" <?php if($aps=="0"){echo "selected='selected'";}?>>Select</option>
					<option value="1" <?php if($aps=="1"){echo "selected='selected'";}?>>Normal Healthy Patient(ASA-I)</option>
					<option value="2" <?php if($aps=="2"){echo "selected='selected'";}?>>Mild Systemic Disease(ASA-II)</option>
					<option value="3" <?php if($aps=="3"){echo "selected='selected'";}?>>Serve Systemic Disease(ASA-III)</option>
					<option value="4" <?php if($aps=="4"){echo "selected='selected'";}?>>Serve Systemic Disease that is treat to life(ASA-IV)</option>
					<option value="5" <?php if($aps=="5"){echo "selected='selected'";}?>>Morbit Patient not expected to survive the operation(ASA-V)</option>
					<option value="6" <?php if($aps=="6"){echo "selected='selected'";}?>>Declared being dead(ASA-VI)</option>
				</select>
			</th>
		</tr>
		<tr>
			<th colspan="6" style="background:#dddddd;">Laboratory Data</th>
		</tr>
		<tr>
			<th>HB %<br/><input type="text" id="hb" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $hb; ?>" /></th>
			<th>TLC<br/><input type="text" id="tlc" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $tlc; ?>" /></th>
			<th>DLC<br/><input type="text" id="dlc" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $dlc; ?>" /></th>
			<th>ESR<br/><input type="text" id="esr" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $esr; ?>" /></th>
			<th>PCV<br/><input type="text" id="pcv" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $pcv; ?>" /></th>
			<th>Blood Group<br/>
			<select id="blood" class="span2" onkeyup="tab(this.id,event)">
				<option value="" <?php if($pat['blood_group']==""){echo "selected='selected'";}?>>Select</option>
				<option value="O Positive" <?php if($pat['blood_group']=="O Positive"){echo "selected='selected'";}?>>O Positive</option>
				<option value="O Negative" <?php if($pat['blood_group']=="O Negative"){echo "selected='selected'";}?>>O Negative</option>
				<option value="A Positive" <?php if($pat['blood_group']=="A Positive"){echo "selected='selected'";}?>>A Positive</option>
				<option value="A Negative" <?php if($pat['blood_group']=="A Negative"){echo "selected='selected'";}?>>A Negative</option>
				<option value="B Positive" <?php if($pat['blood_group']=="B Positive"){echo "selected='selected'";}?>>B Positive</option>
				<option value="B Negative" <?php if($pat['blood_group']=="B Negative"){echo "selected='selected'";}?>>B Negative</option>
				<option value="AB Positive" <?php if($pat['blood_group']=="AB Positive"){echo "selected='selected'";}?>>AB Positive</option>
				<option value="AB Negative" <?php if($pat['blood_group']=="AB Negative"){echo "selected='selected'";}?>>AB Negative</option>
			</select>
			</th>
		</tr>
		<tr>
			<th>FBS<br/><input type="text" id="fbs" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $fbs; ?>" /></th>
			<th>PPBS<br/><input type="text" id="ppbs" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $ppbs; ?>" /></th>
			<th>RBS<br/><input type="text" id="rbs" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $rbs; ?>" /></th>
			<th>Urea<br/><input type="text" id="urea" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $urea; ?>" /></th>
			<th>Creatinine<br/><input type="text" id="creat" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $creat; ?>" /></th>
			<th></th>
		</tr>
		<tr>
			<th>Na+<br/><input type="text" id="sod" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $sod; ?>" /></th>
			<th>K+<br/><input type="text" id="pot" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $pot; ?>" /></th>
			<th>Cl-<br/><input type="text" id="cl" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $cl; ?>" /></th>
			<th>Ca++<br/><input type="text" id="ca" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $ca; ?>" /></th>
			<th>Mg++<br/><input type="text" id="mg" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $mg; ?>" /></th>
			<th></th>
		</tr>
		<tr>
			<th>BT<br/><input type="text" id="bt" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $bt; ?>" /></th>
			<th>CT<br/><input type="text" id="ct" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $ct; ?>" /></th>
			<th>PT<br/><input type="text" id="pt" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $pt; ?>" /></th>
			<th>APTT<br/><input type="text" id="aptt" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $aptt; ?>" /></th>
			<th>INR<br/><input type="text" id="inr" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $inr; ?>" /></th>
			<th>Platelets<br/><input type="text" id="plat" onkeyup="tab(this.id,event)" class="span1" value="<?php echo $plat; ?>" /></th>
		</tr>
		<tr>
			<th>Protein<br/><input type="text" id="protein" onkeyup="tab(this.id,event)" class="span1" value="<?php echo $protein; ?>" /></th>
			<th>Alb<br/><input type="text" id="alb" onkeyup="tab(this.id,event)" class="span1" value="<?php echo $alb; ?>" /></th>
			<th>Biliru<br/><input type="text" id="biliru" onkeyup="tab(this.id,event)" class="span1" value="<?php echo $biliru; ?>" /></th>
			<th>LDH<br/><input type="text" id="ldh" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $ldh; ?>" /></th>
			<th>Amyl<br/><input type="text" id="amyl" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $amyl; ?>" /></th>
			<th>Alk.Phos<br/><input type="text" id="alkphos" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $alkphos; ?>" /></th>
		</tr>
		<tr>
			<th>Total Cholestrol<br/><input type="text" id="choles" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $choles; ?>" /></th>
			<th>Triglycerides<br/><input type="text" id="trigl" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $trigl; ?>" /></th>
			<th>LDL<br/><input type="text" id="ldl" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $ldl; ?>" /></th>
			<th>HDL<br/><input type="text" id="hdl" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $hdl; ?>" /></th>
			<th>VLDL<br/><input type="text" id="vldl" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $vldl; ?>" /></th>
			<th></th>
		</tr>
		<tr>
			<th>HBS Ag<br/><input type="text" id="hbs" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $hbs; ?>" /></th>
			<th>HIV<br/>
			<select id="hiv" class="span2" onkeyup="tab(this.id,event)">
				<option value="0" <?php if($hiv=="0"){echo "selected='selected'";}?>>Select</option>
				<option value="1" <?php if($hiv=="1"){echo "selected='selected'";}?>>Positive</option>
				<option value="2" <?php if($hiv=="2"){echo "selected='selected'";}?>>Negative</option>
			</select>
			</th>
			<th>T3<br/><input type="text" id="t3" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $t3; ?>" /></th>
			<th>T4<br/><input type="text" id="t4" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $t4; ?>" /></th>
			<th>TSH<br/><input type="text" id="tsh" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $tsh; ?>" /></th>
			<th>Others<br/><textarea id="l_other" style="resize:none;" onkeyup="tab(this.id,event)"><?php echo $l_other; ?></textarea></th>
		</tr>
		<tr>
			<th colspan="6" style="background:#dddddd;">Pre-Operative instructions</th>
		</tr>
		<tr>
			<th colspan="3">DVT Prophylaxis</th>
			<th colspan="3"><input type="text" id="dvt" class="span4" onkeyup="tab(this.id,event)" value="<?php echo $dvt;?>" placeholder="DVT" /></th>
		</tr>
		<tr>
			<th colspan="3">NMB from</th>
			<th colspan="3"><input type="text" id="nmb" class="span4" onkeyup="tab(this.id,event)" value="<?php echo $nmb;?>" placeholder="NMB" /></th>
		</tr>
		<tr>
			<th colspan="3">Informed Consent</th>
			<th colspan="3"><label><input type="checkbox" name="consent" <?php if($consent=="consent"){echo "checked='checked'";}?> value="consent" class="" /> Standard</label></th>
		</tr>
		<tr>
			<th colspan="3">Specialist Consultation(Dept Name)</th>
			<th colspan="3"><input type="text" id="consult" class="span4" onkeyup="tab(this.id,event)" value="<?php echo $consult;?>" placeholder="Specialist Consultation" /></th>
		</tr>
		<tr>
			<th colspan="3">Patient to be sent to OT at(date &amp; time)</th>
			<th colspan="3">
				<input type="text" id="sent_date" class="span2 datepicker" onkeyup="tab(this.id,event)" value="<?php echo $sent_date;?>" placeholder="YYYY-MM-DD" />
				<input type="text" id="sent_time" onkeyup="tab(this.id,event)" value="<?php echo $sent_time;?>" class="span2 timepicker" placeholder="HH:MM" />
			</th>
		</tr>
		<tr>
			<th colspan="3">Anxiolytic/ Antacid Prophylaxis</th>
			<th colspan="3"><input type="text" id="prophylaxis" class="span4" onkeyup="tab(this.id,event)" value="<?php echo $prophylaxis;?>" placeholder="Prophylaxis" /></th>
		</tr>
		<tr>
			<th colspan="3">Drugs</th>
			<th colspan="3"><textarea id="drugs" placeholder="Drugs" style="resize:none;" onkeyup="tab(this.id,event)"><?php echo $drugs;?></textarea></th>
		</tr>
		<tr>
			<th colspan="3">Investigations</th>
			<th colspan="3">
				<input type="text" id="invest" class="span4" onkeyup="tab(this.id,event)" value="<?php echo $invest;?>" placeholder="Investigations" /><br/>
				<input type="text" name="r_doc" id="r_doc" class="span3" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="<?php echo $ref_doc_name["ref_name"]."-".$pat_info["refbydoctorid"]; ?>" >
				<div id="doc_info"></div>
				<div id="ref_doc" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
						<th>Doctor ID</th>
						<th>Doctor Name</th>
						<?php
							$d=mysqli_query($GLOBALS["___mysqli_ston"], "select * from refbydoctor_master where refbydoctorid='937' order by ref_name");
							$i=1;
							while($d1=mysqli_fetch_array($d))
							{
						?>
							<tr onClick="doc_load('<?php echo $d1['refbydoctorid'];?>','<?php echo $d1['ref_name'];?>','<?php echo $mrk['name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
								<td>
									<?php echo $d1['refbydoctorid'];?>
								</td>
								<td>
									<?php echo $d1['ref_name'];?>
									<div <?php echo "id=dvdoc".$i;?> style="display:none;">
										<?php echo "#".$d1['refbydoctorid']."#".$d1['ref_name'];?>
									</div>
								</td>
							</tr>
						<?php
							$i++;
							}
						?>
					</table>
				</div>
			</th>
		</tr>
		<tr>
			<th colspan="3">Others</th>
			<th colspan="3"><textarea id="others" style="resize:none;" onkeyup="tab(this.id,event)" placeholder="Others"><?php echo $others;?></textarea></th>
		</tr>
	</table>
	<div>
		<label><input type="radio" name="fit" id="" <?php if($fit=="fit"){echo "checked='checked'";}?> value="fit" class="" /> Fit</label>
		<label><input type="radio" name="fit" id="" <?php if($fit=="not"){echo "checked='checked'";}?> value="not" class="" /> Not Fit</label>
	</div>
	<div>
		<span class="text-right">
			<button type="button" id="pre_ans_btn" class="btn btn-info" <?php echo $disb;?> onclick="save_pre_anes_notes()">Save</button>
			<button type="button" class="btn btn-danger" <?php echo $disb;?> onclick="">Clear</button>
		</span>
	</div>
	<style>
		label{display:inline-block;margin-bottom:0px;font-weight:bold;margin-right:10px;}
		label:hover{color:#222222;}
		input[type="radio"]{margin:0px 0px 0px;}
	</style>
	<script>
		$(".datepicker").datepicker({dateFormat: 'yy-mm-dd',});
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 05,}});
	</script>
	<?php
}

if($type==7)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	
	
	$leave=mysqli_fetch_array(mysqli_query($link,"SELECT `leaved` FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($leave['leaved']==1)
	{
		$disb="disabled='disabled'";
	}
	if($leave['leaved']==0)
	{
		$disb="";
	}
	
	$qry=mysqli_query($link,"SELECT * FROM `ot_notes` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		$nt=mysqli_fetch_array($qry);
		$asa=$nt['asa'];
		$asa_stat=$nt['asa_stat'];
		$ident=$nt['identify'];
		$consent=$nt['consent'];
		$oral=$nt['pre_oprative_oral'];
		$pr=$nt['pr'];
		$bp=$nt['bp'];
		$heart=$nt['heart'];
		$anaes_type=$nt['anaes_type'];
		$ecg=$nt['ecg'];
		$spo=$nt['spo'];
		$nibp=$nt['nibp'];
		$temp=$nt['temp'];
		$proc=$nt['procedure_perform'];
		$pos=$nt['patient_pos'];
		$incision=$nt['incision'];
	}
	else
	{
		$asa="";
		$asa_stat="";
		$ident="";
		$consent="";
		$oral="";
		$pr="";
		$bp="";
		$heart="";
		$anaes_type="";
		$ecg="";
		$spo="";
		$nibp="";
		$temp="";
		$proc="";
		$pos="";
		$incision="";
	}
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th colspan="2" style="text-align:center;background:#dddddd;">Anaesthesia Record</th>
		</tr>
		<tr>
			<th>Physical Status(ASA)</th>
			<th>
				<select id="asa" class="span5">
					<option value="0" <?php if($asa=="0"){echo "selected='selected'";}?>>Select</option>
					<option value="1" <?php if($asa=="1"){echo "selected='selected'";}?>>Normal Healthy Patient(ASA-I)</option>
					<option value="2" <?php if($asa=="2"){echo "selected='selected'";}?>>Mild Systemic Disease(ASA-II)</option>
					<option value="3" <?php if($asa=="3"){echo "selected='selected'";}?>>Serve Systemic Disease(ASA-III)</option>
					<option value="4" <?php if($asa=="4"){echo "selected='selected'";}?>>Serve Systemic Disease that is treat to life(ASA-IV)</option>
					<option value="5" <?php if($asa=="5"){echo "selected='selected'";}?>>Morbit Patient not expected to survive the operation(ASA-V)</option>
					<option value="6" <?php if($asa=="6"){echo "selected='selected'";}?>>Declared being dead(ASA-VI)</option>
				</select>
				<label><input type="radio" name="stat" id="" <?php if($asa_stat=="emergency"){echo "checked='checked'";}?> value="emergency" class="" /> Emergency</label>
				<label><input type="radio" name="stat" id="" <?php if($asa_stat=="elective"){echo "checked='checked'";}?> value="elective" class="" /> Elective</label>
			</th>
		</tr>
		<tr>
			<th>Patient Identified</th>
			<th>
				<label><input type="radio" name="ident" <?php if($ident=="yes"){echo "checked='checked'";}?> id="" value="yes" class="" /> Yes</label>
				<label><input type="radio" name="ident" <?php if($ident=="no"){echo "checked='checked'";}?> id="" value="no" class="" /> No</label>
			</th>
		</tr>
		<tr>
			<th>Consent Taken</th>
			<th>
				<label><input type="radio" name="consent" id="" <?php if($consent=="yes"){echo "checked='checked'";}?> value="yes" class="" /> Yes</label>
				<label><input type="radio" name="consent" id="" <?php if($consent=="no"){echo "checked='checked'";}?> value="no" class="" /> No</label>
			</th>
		</tr>
		<tr>
			<th>Last Pre-Operative Oral Intake</th>
			<th><input type="text" id="oral" class="span8" value="<?php echo $oral; ?>" placeholder="Last Pre-Operative Oral Intake" /></th>
		</tr>
		<tr>
			<th>Pre-Operative Vitals</th>
			<th>
				PR/HR <input type="text" id="pr" class="span1" value="<?php echo $pr; ?>" placeholder="PR / HR" />
				BP <input type="text" id="bp" class="span1" value="<?php echo $bp; ?>" placeholder="BP" />
				Heart and Lungs <input type="text" id="heart" value="<?php echo $heart; ?>" class="span4" placeholder="Heart" />
			</th>
		</tr>
		<tr>
			<th>Type of Anaesthesia</th>
			<th>
				<label><input type="checkbox" id="" name="anaes" <?php if(strpos($anaes_type,'1')!==false){echo "checked='checked'"; }?> value="1" /> General</label>
				<label><input type="checkbox" id="" name="anaes" <?php if(strpos($anaes_type,'2')!==false){echo "checked='checked'"; }?> value="2" /> Regional</label>
				<label><input type="checkbox" id="" name="anaes" <?php if(strpos($anaes_type,'3')!==false){echo "checked='checked'"; }?> value="3" /> MAC</label>
			</th>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;background:#dddddd;">Monitors Connected</th>
		</tr>
		<tr>
			<th>ECG</th>
			<th>
				<label><input type="checkbox" id="" name="ecg" <?php if(strpos($ecg,'3lead')!==false){echo "checked='checked'"; }?> value="3lead" /> 3 LEAD</label>
				<label><input type="checkbox" id="" name="ecg" <?php if(strpos($ecg,'5lead')!==false){echo "checked='checked'"; }?> value="5lead" /> 5 LEAD</label>
			</th>
		</tr>
		<tr>
			<th>SPO2</th>
			<th>
				<label><input type="radio" name="spo" id="" <?php if($spo=="yes"){echo "checked='checked'";}?> value="yes" class="" /> Yes</label>
				<label><input type="radio" name="spo" id="" <?php if($spo=="no"){echo "checked='checked'";}?> value="no" class="" /> No</label>
			</th>
		</tr>
		<tr>
			<th>NIBP or Manual BP</th>
			<th>
				<label><input type="radio" name="nibp" id="" <?php if($nibp=="yes"){echo "checked='checked'";}?> value="yes" class="" /> Yes</label>
				<label><input type="radio" name="nibp" id="" <?php if($nibp=="no"){echo "checked='checked'";}?> value="no" class="" /> No</label>
			</th>
		</tr>
		<tr>
			<th>Temperature</th>
			<th>
				<label><input type="radio" name="temp" id="" <?php if($temp=="central"){echo "checked='checked'";}?> value="central" class="" /> Central</label>
				<label><input type="radio" name="temp" id="" <?php if($temp=="perip"){echo "checked='checked'";}?> value="perip" class="" /> Peripheral</label>
			</th>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;background:#dddddd;">Surgery Notes</th>
		</tr>
		<tr>
			<th>Name of the procedure performed</th>
			<th><input type="text" id="proc" class="span8" value="<?php echo $proc;?>" placeholder="Name of the procedure performed" /></th>
		</tr>
		<tr>
			<th>Position of patient</th>
			<th><input type="text" id="pos" class="span8" value="<?php echo $pos;?>" placeholder="Position of patient" /></th>
		</tr>
		<tr>
			<th>Incision</th>
			<th><input type="text" id="incision" class="span8" value="<?php echo $incision;?>" placeholder="Incision" /></th>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;background:#eeeeee;"><button type="button" id="btn_ot_note" <?php echo $disb;?> class="btn btn-primary" onclick="insert_ot_notes()"><i class="icon icon-save"></i> Save</button></th>
		</tr>
	</table>
	<style>
		label{display:inline-block;margin-bottom:0px;font-weight:bold;margin-right:10px;}
		label:hover{color:#222222;}
		input[type="radio"]{margin:0px 0px 0px;}
	</style>
	<?php
}

if($type==8)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	
	$cabin="";
	$leave=mysqli_fetch_array(mysqli_query($link,"SELECT `leaved`,`grade_id`,`ot_cabin_id` FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$cabin=$leave['ot_cabin_id'];
	if($leave['leaved']==1)
	{
		$disb="disabled='disabled'";
	}
	if($leave['leaved']==0)
	{
		$disb="";
	}
	// SELECT `rate` FROM `clinical_procedure` WHERE `procedure_id`='' AND `grade_id`=''
	
	$grad=mysqli_fetch_array(mysqli_query($link,"SELECT `grade_name` FROM `ot_grade_master` WHERE `grade_id`='$leave[grade_id]'"));
	$pat_type=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name`,`sex`,`age`,`age_type` FROM `patient_info` WHERE `patient_id`='$uhid'"));
	$sh=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(schedule_id) as max FROM `ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$pl=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_area_id`,`procedure_id` FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed' ORDER BY `slno` DESC"));
	$proced=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `clinical_procedure` WHERE `procedure_id`='$pl[procedure_id]' AND `grade_id`='$leave[grade_id]'"));
	$qry=mysqli_query($link,"SELECT * FROM `ot_surgery_record` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		$d=mysqli_fetch_array($qry);
		$rid=$d['record_id'];
		$perf=$d['perform'];
		$remark=$d['remarks'];
		$anes_st_time=$d['anes_st_time'];
		$anes_en_time=$d['anes_en_time'];
		$surg_note=$d['surgery_note'];
		$ot=$d['ot_area_id'];
		$anaes=$d['anaes'];
		$surg_type=$d['surg_type'];
		$pat_in_time=$d['ot_in_time'];
		$act_st_time=$d['act_st_time'];
		$sur_st_time=$d['sur_st_time'];
		$sur_en_time=$d['sur_en_time'];
		$act_en_time=$d['act_en_time'];
		$pro_st_time=$d['proc_st_time'];
		$pro_en_time=$d['proc_en_time'];
		$pat_out_time=$d['ot_out_time'];
	}
	else
	{
		$rid=0;
		$perf="";
		$remark="";
		$anaes_st_time="";
		$anaes_en_time="";
		$surg_note="";
		$ot="";
		$anaes="";
		$surg_type="";
		$pat_in_time="";
		$act_st_time="";
		$sur_st_time="";
		$sur_en_time="";
		$act_en_time="";
		$pro_st_time="";
		$pro_en_time="";
		$pat_out_time="";
	}
	?>
	<input type="text" id="pat_type" value="<?php echo $pat_type['type'];?>" style="display:none;" />
	<table class="table table-condensed table-bordered">
		<tr>
			<th colspan="6" style="text-align:center;background:#dddddd;">Surgery Bill Record</th>
		</tr>
		<!--<tr>
			<th>UHID</th>
			<th><input type="text" id="" class="span2" readonly="readonly" value="<?php echo $uhid; ?>" /></th>
			<th>IPD Id</th>
			<th><input type="text" id="" class="span2" readonly="readonly" value="<?php echo $ipd; ?>" /></th>
			<th>Schedule No</th>
			<th><input type="text" id="" class="span2" readonly="readonly" value="<?php echo $sh['max']; ?>" /></th>
		</tr>
		<tr>
			<th>Patient Name</th>
			<th><input type="text" id="" class="span2" readonly="readonly" value="<?php echo $pat['name']; ?>" /></th>
			<th>Age/Sex</th>
			<th><input type="text" id="" class="span2" readonly="readonly" value="<?php echo $pat['age']." ".$pat['age_type']." / ".$pat['sex']; ?>" /></th>
			<th>Date Time</th>
			<th><input type="text" id="" class="span2" placeholder="..." /></th>
		</tr>-->
		<tr style="display:none">
			<th>Surgery Planned</th>
			<th>Surgery Performed</th>
			<th>Remarks/Special Requirement</th>
		</tr>
		<tr style="display:none">
			<th><?php echo $proced['name'];?></th>
			<td><input type="text" id="perf" class="span3" value="<?php echo $perf; ?>" placeholder="Surgery Performed" /></td>
			<td><textarea id="remark" style="resize:none;width:90%;"><?php echo $remark; ?></textarea></td>
		</tr>
	</table>
	<table class="table table-condensed table-bordered">
		<tr style="display:none">
			<th colspan="2">Anaesthesia Start Time
			<input type="text" id="anaes_st_time" class="span2 timepicker" value="<?php echo $anes_st_time; ?>" placeholder="HH:MM" /></th>
			<th rowspan="2" colspan="2">
				Surgery Note<br/>
				<textarea id="surg_note" style="resize:none;width:90%;"><?php echo $surg_note; ?></textarea>
			</th>
		</tr>
		<tr style="display:none">
			<th colspan="2">Anaesthesia End Time
			<input type="text" id="anaes_en_time" value="<?php echo $anes_en_time; ?>" class="span2 timepicker" placeholder="HH:MM" /></th>
		</tr>
		<tr style="display:none">
			<th>OT<br/>
				<select id="ot">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `ot_area_id`,`ot_area_name` FROM `ot_area_master` ORDER BY `ot_area_name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['ot_area_id'];?>" <?php if($ot==$r['ot_area_id']){echo "selected='selected'";} ?>><?php echo $r['ot_area_name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th>
				Anaesthesia<br/>
				<select id="anaes">
					<option value="0">Select</option>
				</select>
			</th>
			<th>
				Surgery Type<br/>
				<select id="surg_type">
					<option value="0">Select</option>
				</select>
			</th>
			<td>
				<b>Grade</b><br/>
				<?php echo $grad['grade_name'];?>
				<input type="text" id="grade_rate" style="display:none;" readonly class="span1" value="<?php echo $proced['rate']; ?>" />
			</td>
		</tr>
		<tr style="display:none">
			<th>Patient In Time into OT<br/>
				<input type="text" id="pat_in_time" value="<?php echo $pat_in_time; ?>" class="span2 timepicker" placeholder="HH:MM" />
			</th>
			<th>OT Table Activity Start Time<br/>
				<input type="text" id="act_st_time" value="<?php echo $act_st_time; ?>" class="span2 timepicker" placeholder="HH:MM" />
			</th>
			<th>Surgery Proper Start Time<br/>
				<input type="text" id="sur_st_time" value="<?php echo $sur_st_time; ?>" class="span2 timepicker" placeholder="HH:MM" />
			</th>
			<th>Surgery Proper End Time<br/>
				<input type="text" id="sur_en_time" value="<?php echo $sur_en_time; ?>" class="span2 timepicker" placeholder="HH:MM" />
			</th>
		</tr>
		<tr style="display:none">
			<th>OT Table Activity End Time<br/>
				<input type="text" id="act_en_time" value="<?php echo $act_en_time; ?>" class="span2 timepicker" placeholder="HH:MM" />
			</th>
			<th>Recovery Procedure Start Time<br/>
				<input type="text" id="pro_st_time" value="<?php echo $pro_st_time; ?>" class="span2 timepicker" placeholder="HH:MM" />
			</th>
			<th>Recovery Procedure End Time<br/>
				<input type="text" id="pro_en_time" value="<?php echo $pro_en_time; ?>" class="span2 timepicker" placeholder="HH:MM" />
			</th>
			<th>Patient Out Time from OT<br/>
				<input type="text" id="pat_out_time" value="<?php echo $pat_out_time; ?>" class="span2 timepicker" placeholder="HH:MM" />
			</th>
		</tr>
	</table>
	<table id="" class="table table-condensed table-bordered" style="display:none;">
		<tr>
			<td colspan="5" style="background:#dddddd;"><button type="button" class="btn btn-info" onclick="add_diag_row()"><i class="icon icon-plus"></i> Add Diagnosis</button></td>
		</tr>
		<tr>
			<th id="last_tr">SN</th><th>Code</th><th>Description</th><th>Diagnosis Type</th><th style="text-align:center;"><i class="icon icon-trash icon-large" style="color:#aa0000;"></i></th>
		</tr>
	</table>
	<table id="srce_tbl_old" class="table table-condensed table-bordered" style="display:none;">
		<tr>
			<th>SN</th>
			<th>Resource</th>
			<th>Employee</th>
			<th>From Date Time</th>
			<th>To Date Time</th>
			<th style="text-align:center;"><i class="icon icon-trash icon-large" style="color:#aa0000;"></i></th>
		</tr>
		<?php
		//$sc=mysqli_fetch_array(mysqli_query($link,"SELECT `schedule_id` FROM `ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'"));
		//$qq=mysqli_query($link,"SELECT * FROM `ot_resource` WHERE `schedule_id`='$shed'");
		//$nn=mysqli_num_rows($qq);
		$nn=0;
		if($nn>0)
		{
			$i=1;
			//while($rr=mysqli_fetch_array($qq))
			{
				$res=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$rr[resourse_id]'"));
				$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$rr[emp_id]'"));
				$qr=mysqli_query($link,"SELECT * FROM `ot_surgery_record_resource` WHERE `record_id`='$rid' AND `emp_id`='$rr[emp_id]'");
				$cl=mysqli_num_rows($qr);
				if($cl>0)
				{
					$vl=mysqli_fetch_array($qr);
					$fdt=$vl['from_date'];
					$ftm=$vl['from_time'];
					$tdt=$vl['to_date'];
					$ttm=$vl['to_time'];
				}
				else
				{
					$fdt="";
					$ftm="";
					$tdt="";
					$ttm="";
					$dis="";
				}
				if($num>0 && $cl>0)
				{
					$bg="background:#CEFFD2";
					$dis="disabled='disabled'";
					$remove="";
					$tt=1;
				}
				else if($num==0 && $cl==0)
				{
					$bg="";
					$dis="";
					$remove="$(this).parent().parent().remove()";
					$tt=2;
				}
				else
				{
					$bg="background:#FFE4DA";
					$dis="disabled='disabled'";
					$remove="$(this).parent().parent().remove()";
					$tt=3;
				}
			?>
			<tr class="source" id="tr<?php echo $rr['emp_id']; ?>">
				<td><?php echo $i;?><input type="text" style="display:none;" value="<?php echo $rr['emp_id']; ?>" /></td>
				<td><?php echo $res['type'];?></td>
				<td><?php echo $emp['name'];?></td>
				<td>
					<input type="text" id="" class="datepicker" style="max-width:100px;<?php echo $bg;?>" value="<?php echo $fdt;?>" placeholder="YYYY-MM-DD" <?php echo $dis;?> /><input type="text" id="" class="timepicker" style="max-width:80px;<?php echo $bg;?>" value="<?php echo $ftm;?>" placeholder="HH:MM" <?php echo $dis;?> />
				</td>
				<td>
					<input type="text" id="" class="datepicker" style="max-width:100px;<?php echo $bg;?>" value="<?php echo $tdt;?>" placeholder="YYYY-MM-DD" <?php echo $dis;?> /><input type="text" id="" class="timepicker" style="max-width:80px;<?php echo $bg;?>" value="<?php echo $ttm;?>" placeholder="HH:MM" <?php echo $dis;?> />
				</td>
				<td style="text-align:center;">
					<i class="icon icon-remove icon-large remove" onclick="<?php echo $remove;?>" style="color:#aa0000;cursor:pointer;"></i>
				</td>
			</tr>
			<?php
			$i++;
			}
		}
		?>
		<tr>
			<th colspan="6" style="text-align:center;background:#eeeeee;">
				<button type="button" class="btn btn-primary" onclick="insert_surg_rec()"><i class="icon icon-save"></i> Save</button>
				<button type="button" class="btn btn-danger" onclick="clrr()"><i class="icon icon-ban-circle"></i> Cancel</button>
				<button type="button" class="btn btn-warning" onclick="surgery_record()"><i class="icon icon-trash"></i> Clear</button>
			</th>
		</tr>
	</table>
	<table id="srce_tbl" class="table table-condensed table-bordered">
		<?php
		if($pat_type['type']==3)
		{
			$pay_final=mysqli_fetch_array(mysqli_query($link,"SELECT `pay_type` FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final'"));
			if($pay_final)
			{
				$disble="disabled='disabled'";
				$functn="";
				$upd_functn="";
				$nextopt=0;
				$tttt=1;
			}
			else
			{
				$ot_leave=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_room_leaved` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'"));
				if($ot_leave)
				{
					$disble="disabled='disabled'";
					$functn="";
					$upd_functn="";
					$nextopt=0;
					$tttt=2;
				}
				else
				{
					$disble="";
					$functn="insert_surg_rec()";
					$upd_functn="upd_shed_res()";
					$nextopt=1;
					$tttt=3;
				}
			}
			$ot_pay=0;
		}
		else
		{
			$ot_leave=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_room_leaved` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'"));
			if($ot_leave)
			{
				$disble="disabled='disabled'";
				$functn="";
				$upd_functn="";
				$nextopt=0;
				$tttt=4;
			}
			else
			{
				$disble="";
				$functn="insert_surg_rec()";
				$upd_functn="upd_shed_res()";
				$nextopt=1;
				$tttt=5;
			}
			$ot_pay_amount=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS tot FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
			$ot_pay=$ot_pay_amount['tot'];
			$ot_pay=explode(".",$ot_pay);
			$ot_pay=$ot_pay[0];
		}
		?>
		<tr>
			<th style="display:none;color:#DA1818;position:relative;text-align:center;" id="err_msg" colspan="4">Total Amount should be equal to bill amount</th>
		</tr>
		<tr>
			<th>SN</th>
			<th>Resource</th>
			<th>Employee</th>
			<th>
				Amount
				<input type="hidden" id="ot_pay" value="<?php echo $ot_pay;?>" />
				<span style="float:right;">
					<?php if($ot_pay>0){echo "<span style='font-size:10px;'>Bill Amount : ".$ot_pay."</span>";}?>
					<button type="button" class="btn btn-primary btn-mini" onclick="<?php echo $upd_functn;?>" <?php echo $disble;?>>Update</button>
				</span>
			</th>
		</tr>
		<?php
		//echo $tttt; // testing
		$remain="";
		//$qq=mysqli_query($link,"SELECT a.* FROM `ot_resource` a, `ot_type_master` b WHERE a.`schedule_id`='$shed' AND a.`resourse_id`=b.`type_id` ORDER BY b.`seq`");
		$qq=mysqli_query($link,"SELECT a.* FROM `ot_resource` a, `ot_type_master` b WHERE a.`schedule_id`='$shed' AND a.`resourse_id`=b.`type_id` ORDER BY b.`seq`");
		
		$nn=mysqli_num_rows($qq);
		if($nn>0)
		{
			$i=1;
			$r_tot=0;
			while($rr=mysqli_fetch_array($qq))
			{
				if($remain)
				{
					$remain.=", ".$rr['resourse_id'];
				}
				else
				{
					$remain=$rr['resourse_id'];
				}
				$res=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$rr[resourse_id]'"));
				$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$rr[emp_id]'"));
				//$amtt=mysqli_fetch_array(mysqli_query($link,"SELECT `charge_id` FROM `ot_resource_master` WHERE `grade_id`='$leave[grade_id]' AND `type_id`='$rr[resourse_id]'"));
				$amtt=mysqli_fetch_array(mysqli_query($link,"SELECT `charge_id` FROM `ot_resource_master` WHERE `grade_id`='$leave[grade_id]' AND `ot_cabin_id`='$cabin' AND `type_id`='$rr[resourse_id]'"));
				//$amtt=mysqli_fetch_array(mysqli_query($link,"SELECT `charge_id` FROM `ot_resource_master` WHERE `type_id`='$rr[resourse_id]'"));
				$service=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_pat_service_details` WHERE `schedule_id`='$shed' AND `resourse_id`='$rr[resourse_id]' AND `emp_id`='$rr[emp_id]'"));
				$icon="";
				if($service)
				{
					$empp=$service['emp_id'];
					$s_amount=$service['amount'];
					$rs_amt=explode(".",$s_amount);
					$rs_amt=$rs_amt[0];
					$icon="<i class='icon-ok text-success'></i>";
				}
				else
				{
					$empp=$rr['emp_id'];
					$rs_amt=$amtt['charge_id'];
					$icon="";
				}
				$r_tot+=$rs_amt;
			?>
			<tr class="source osource" id="tr<?php echo $rr['resourse_id']; ?>">
				<td>
					<span><?php echo $i;?></span>
					<input type="text" style="display:none;" class="span1" value="<?php echo $rr['resourse_id']; ?>" />
					<input type="text" style="display:none;" class="span1" value="<?php echo $empp; ?>" />
				</td>
				<td><?php echo $res['type'];?></td>
				<td>
					<?php
					if($empp>0)
					{
					?>
					<select onchange="change_emp(this)" <?php echo $disble;?>>
						<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT a.`emp_id`, b.`name` FROM `ot_resource_link` a,`employee` b WHERE a.`emp_id`=b.`emp_id` AND a.`type_id`='$rr[resourse_id]'");
					while($r=mysqli_fetch_array($q))
					{
					?>
						<option value="<?php echo $r['emp_id'];?>" <?php if($rr['emp_id']==$r['emp_id']){echo "selected='selected'";}?>><?php echo $r['name'];?></option>
					<?php
					}
					?>
					</select>
					<?php
					}
					else
					{
						$res_link=mysqli_fetch_array(mysqli_query($link,"SELECT `link` FROM `ot_type_master` WHERE `type_id`='$rr[resourse_id]'"));
						if($res_link['link']>0)
						{
						?>
						<select onchange="change_emp(this)" <?php echo $disble;?>>
							<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT a.`emp_id`, b.`name` FROM `ot_resource_link` a,`employee` b WHERE a.`emp_id`=b.`emp_id` AND a.`type_id`='$rr[resourse_id]'");
						while($r=mysqli_fetch_array($q))
						{
						?>
							<option value="<?php echo $r['emp_id'];?>"><?php echo $r['name'];?></option>
						<?php
						}
						?>
						</select>
						<?php
						}
					}
					?>
					</td>
				<td>
					<input type="text" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');sum_amt()" <?php echo $disble;?> value="<?php echo $rs_amt;?>" placeholder="Amount" />
					<span style="float:right;"><?php echo $icon;?></span>
				</td>
			</tr>
			<?php
			$i++;
			}
		}
		$btn_val="Added to bill";
		
		if($nextopt>0)
		{
		$rem_res=mysqli_query($link,"SELECT a.`type_id`, a.`charge_id`, b.`type`, b.`link` FROM `ot_resource_master` a, `ot_type_master` b WHERE a.`grade_id`='$leave[grade_id]' AND a.`ot_cabin_id`='$leave[ot_cabin_id]' AND a.`type_id`=b.`type_id` AND b.`type_id` NOT IN ($remain) ORDER BY b.`seq`");
		
		//$rem_res=mysqli_query($link,"SELECT * FROM `ot_type_master`  WHERE `type_id` NOT IN ($remain) AND link=0 ORDER BY `seq`"); //  AND b.`type_id` NOT IN ($remain)
		while ($r_rem=mysqli_fetch_array($rem_res))
		{
			$amt_service=mysqli_fetch_array(mysqli_query($link,"SELECT `charge_id` FROM `ot_resource_master` WHERE `grade_id`='$leave[grade_id]' AND `ot_cabin_id`='$cabin' AND `type_id`='$r_rem[type_id]'"));
			
			//$rs_amt=$amt_service['charge_id'];
			$rs_amt=$r_rem['charge_id'];
			$r_tot+=$rs_amt;
			if($r_rem['link']>0)
			{
				$emp_sel='<select onchange="change_emp(this)"'.$disble.'><option value="0">Select</option>';
				$extra_res_qry=mysqli_query($link,"SELECT * FROM `ot_resource_link` WHERE `type_id`='$r_rem[type_id]'");
				while($extra_res=mysqli_fetch_array($extra_res_qry))
				{
					$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$extra_res[emp_id]'"));
					$emp_sel.='<option value="'.$extra_res['emp_id'].'">'.$emp['name'].'</option>';
				}
				$emp_sel.='</select>';
			}
			else
			{
				$emp_sel="";
			}
		?>
		<tr class="source osource" id="tr<?php echo $r_rem['type_id']; ?>">
			<td>
				<span><?php echo $i;?></span>
				<input type="text" style="display:none;" class="span1" value="<?php echo $r_rem['type_id']; ?>" />
				<input type="text" style="display:none;" class="span1" value="extra" />
			</td>
			<td><?php echo $r_rem['type'];?></td>
			<td><?php echo $emp_sel;?></td>
			<td>
				<input type="text" name="" id="" value="<?php echo $rs_amt;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');sum_amt()" <?php echo $disble;?> placeholder="Amount" />
				<span style="float:right;color:#DA1818;cursor:pointer;"><i class="icon-remove icon-large" onclick="$(this).parent().parent().parent().remove();sum_amt()"></i></span>
			</td>
		</tr>
		<?php
		$i++;
		$btn_val="Add to bill";
		}
		}
		?>
		<tr id="res_tr">
			<th colspan="3" style="text-align:right;">Total :</th>
			<td>
				<input type="text" id="serv_tot" value="<?php echo $r_tot;?>" readonly="readonly" />
				<!--<span style="float:right;"><button type="button" class="btn btn-info" <?php echo $disb;?> onclick="add_res_row()">Add More</button></span>-->
				<!--<span style="float:right;"><button type="button" class="btn btn-info" <?php echo $disb;?> onclick="add_res_row()">Add More</button></span>-->
				<!--<span style="float:right;"><button type="button" class="btn btn-info" <?php echo $disb;?> onclick="add_res_row()">Add More</button></span>-->
				<!--<span style="float:right;"><button type="button" class="btn btn-info" <?php echo $disb;?> onclick="add_res_row()">Add More</button></span>-->
			</td>
		</tr>
		<tr>
			<th colspan="6" style="text-align:center;background:#eeeeee;">
				<button type="button" class="btn btn-primary" id="rec_btn" <?php echo $disb;?> <?php echo $disble;?> onclick="<?php echo $functn;?>"><i class="icon icon-plus"></i> <?php echo $btn_val;?></button>
				<button type="button" class="btn btn-danger" style="display:none;" onclick="clrr()"><i class="icon icon-ban-circle"></i> Cancel</button>
				<button type="button" class="btn btn-warning" style="display:none;" onclick="surgery_record()"><i class="icon icon-refresh"></i> Refresh</button>
			</th>
		</tr>
	</table>
	<style>
		label{display:inline-block;margin-bottom:0px;font-weight:bold;margin-right:10px;}
		label:hover{color:#222222;}
		input[type="radio"]{margin:0px 0px 0px;}
	</style>
	<script>
		$(".datepicker").datepicker({dateFormat: 'yy-mm-dd',});
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 05,}});
	</script>
	<?php
}

if($type==9)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	
	$leave=mysqli_fetch_array(mysqli_query($link,"SELECT `leaved` FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($leave['leaved']==1)
	{
		$disb="disabled='disabled'";
	}
	if($leave['leaved']==0)
	{
		$disb="";
	}
	
	$bd=mysqli_fetch_array(mysqli_query($link,"SELECT `ward_id`, `bed_id` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$w=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$bd[ward_id]'"));
	$b=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no` FROM `bed_master` WHERE `bed_id`='$bd[bed_id]'"));
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `consultantdoctorid` FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
	$qry=mysqli_query($link,"SELECT * FROM `ot_post_surgery` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	$n=mysqli_num_rows($qry);
	if($n>0)
	{
		$f=mysqli_fetch_array($qry);
		$req_no=$f['req_no'];
		$surgery=$f['surgery'];
		$notes=$f['notes'];
		$template=$f['template'];
		
		$air=$f['airway'];
		$hyp=$f['hypopharyngeal'];
		$sat=$f['saturation'];
		$pul=$f['pulmonary'];
		$vit=$f['vital'];
		$consc=$f['consciousness'];
		$ori=$f['orientation'];
		$mot=$f['motor'];
		$card=$f['cardiovascular'];
		$sur=$f['surgical'];
		$hemo=$f['hemorrhage'];
		$pain=$f['pain'];
		$urine=$f['urine'];
		$others=$f['others'];
	}
	else
	{
		$req_no="";
		$surgery="";
		$notes="";
		$template="";
		
		$air="";
		$hyp="";
		$sat="";
		$pul="";
		$vit="";
		$consc="";
		$ori="";
		$mot="";
		$card="";
		$sur="";
		$hemo="";
		$pain="";
		$urine="";
		$others="";
	}
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th colspan="6" style="text-align:center;background:#dddddd;">Post Surgery Record</th>
		</tr>
		<tr>
			<th>Request No</th>
			<th><input type="text" id="req_no" class="span2" value="<?php echo $req_no;?>" placeholder="Request No" /></th>
			<th>Surgery</th>
			<th><input type="text" id="surgery" class="span2" value="<?php echo $surgery;?>" placeholder="Surgery" /></th>
			<th>Notes</th>
			<th><input type="text" id="notes" class="span2" value="<?php echo $notes;?>" placeholder="Notes" /></th>
		</tr>
		<tr>
			<th>Template</th>
			<th><input type="text" id="template" class="span2" value="<?php echo $template;?>" placeholder="Template" /></th>
			<th>Doctor/Nurse</th>
			<th><?php echo $doc['Name'];?></th>
			<th>Ward No/Bed No</th>
			<th><input type="text" id="" class="span2" readonly="readonly" value="<?php echo $w['name']." / ".$b['bed_no'];?>" /></th>
		</tr>
	</table>
	<table class="table table-bordered">
		<tr>
			<th colspan="2" style="background:#dddddd;">Patient Assessment</th>
		</tr>
		<tr>
			<th>Patient Airway</th>
			<th>
				<label><input type="radio" name="airway" <?php if($air=="yes"){echo "checked='checked'";}?> id="" value="yes" class="" /> Yes</label>
				<label><input type="radio" name="airway" <?php if($air=="no"){echo "checked='checked'";}?> id="" value="no" class="" /> No</label>
			</th>
		</tr>
		<tr>
			<th>Hypopharyngeal Obstruction</th>
			<th>
				<label><input type="radio" name="obst" <?php if($hyp=="absent"){echo "checked='checked'";}?> id="" value="absent" class="" /> Absent</label>
				<label><input type="radio" name="obst" <?php if($hyp=="present"){echo "checked='checked'";}?> id="" value="present" class="" /> Present</label>
			</th>
		</tr>
		<tr>
			<th>O2 Saturation Scores</th>
			<th>
				<label><input type="radio" name="score" <?php if($sat=="adeq"){echo "checked='checked'";}?> id="" value="adeq" class="" /> Adequate</label>
				<label><input type="radio" name="score" <?php if($sat=="inadeq"){echo "checked='checked'";}?> id="" value="inadeq" class="" /> Inadequate</label>
				<label><input type="radio" name="score" <?php if($sat=="na"){echo "checked='checked'";}?> id="" value="na" class="" /> N/A</label>
			</th>
		</tr>
		<tr>
			<th>Pulmonary Functions</th>
			<th>
				<label><input type="radio" name="pul" id="" <?php if($pul=="uncomprised"){echo "checked='checked'";}?> value="uncomprised" class="" /> Uncompromised</label>
				<label><input type="radio" name="pul" id="" <?php if($pul=="noisy"){echo "checked='checked'";}?> value="noisy" class="" /> Noisy and Irregular Respirations</label>
				<label><input type="radio" name="pul" id="" <?php if($pul=="cyanotic"){echo "checked='checked'";}?> value="cyanotic" class="" /> Cyanotic</label>
				<label><input type="radio" name="pul" id="" <?php if($pul=="compromised"){echo "checked='checked'";}?> value="compromised" class="" /> Compromised</label>
				<label><input type="radio" name="pul" id="" <?php if($pul=="breath"){echo "checked='checked'";}?> value="breath" class="" /> Non Breathing</label>
			</th>
		</tr>
		<tr>
			<th>Vital Signs</th>
			<th>
				<label><input type="radio" name="vital" id="" <?php if($vit=="stable"){echo "checked='checked'";}?> value="stable" class="" /> Stable</label>
				<label><input type="radio" name="vital" id="" <?php if($vit=="unstable"){echo "checked='checked'";}?> value="unstable" class="" /> Unstable</label>
				<label><input type="radio" name="vital" id="" <?php if($vit=="not"){echo "checked='checked'";}?> value="not" class="" /> Not Recordable</label>
			</th>
		</tr>
		<tr>
			<th>Consciousness Level</th>
			<th>
				<label><input type="radio" name="consc" id="" <?php if($consc=="consc"){echo "checked='checked'";}?> value="consc" class="" /> Conscious</label>
				<label><input type="radio" name="consc" id="" <?php if($consc=="semiconsc"){echo "checked='checked'";}?> value="semiconsc" class="" /> Semiconscious</label>
				<label><input type="radio" name="consc" id="" <?php if($consc=="unconsc"){echo "checked='checked'";}?> value="unconsc" class="" /> Unconscious</label>
				<label><input type="radio" name="consc" id="" <?php if($consc=="unknown"){echo "checked='checked'";}?> value="unknown" class="" /> Unknown</label>
			</th>
		</tr>
		<tr>
			<th>Orientation</th>
			<th>
				<label><input type="radio" name="orien" id="" <?php if($ori=="orien"){echo "checked='checked'";}?> value="orien" class="" /> Oriented</label>
				<label><input type="radio" name="orien" id="" <?php if($ori=="disorien"){echo "checked='checked'";}?> value="disorien" class="" /> Disoriented</label>
				<label><input type="radio" name="orien" id="" <?php if($ori=="not"){echo "checked='checked'";}?> value="not" class="" /> Not Responding</label>
			</th>
		</tr>
		<tr>
			<th>Motor and Sensory Function</th>
			<th>
				<label><input type="radio" name="motor" id="" <?php if($mot=="resumed"){echo "checked='checked'";}?> value="resumed" class="" /> Resumed</label>
				<label><input type="radio" name="motor" id="" <?php if($mot=="not"){echo "checked='checked'";}?> value="not" class="" /> Not yet to be resumed</label>
				<label><input type="radio" name="motor" id="" <?php if($mot=="unknown"){echo "checked='checked'";}?> value="unknown" class="" /> Unknown</label>
			</th>
		</tr>
		<tr>
			<th>Cardiovascular Function</th>
			<th>
				<label><input type="radio" name="cardio" id="" <?php if($card=="normal"){echo "checked='checked'";}?> value="normal" class="" /> Normal</label>
				<label><input type="radio" name="cardio" id="" <?php if($card=="abnormal"){echo "checked='checked'";}?> value="abnormal" class="" /> Abnormal</label>
				<label><input type="radio" name="cardio" id="" <?php if($card=="absent"){echo "checked='checked'";}?> value="absent" class="" /> Absent</label>
			</th>
		</tr>
		<tr>
			<th>Condition of the surgical site</th>
			<th>
				<label><input type="radio" name="site" id="" <?php if($sur=="normal"){echo "checked='checked'";}?> value="normal" class="" /> Normal</label>
				<label><input type="radio" name="site" id="" <?php if($sur=="abnormal"){echo "checked='checked'";}?> value="abnormal" class="" /> Abnormal</label>
				<label><input type="radio" name="site" id="" <?php if($sur=="urgent"){echo "checked='checked'";}?> value="urgent" class="" /> Needs Urgent Attention</label>
			</th>
		</tr>
		<tr>
			<th>Hemorrhage</th>
			<th>
				<label><input type="radio" name="hemor" id="" <?php if($hemo=="none"){echo "checked='checked'";}?> value="none" class="" /> None</label>
				<label><input type="radio" name="hemor" id="" <?php if($hemo=="oozing"){echo "checked='checked'";}?> value="oozing" class="" /> Oozing</label>
				<label><input type="radio" name="hemor" id="" <?php if($hemo=="bleed"){echo "checked='checked'";}?> value="bleed" class="" /> Profuse Bleeding</label>
			</th>
		</tr>
		<tr>
			<th>Pain</th>
			<th>
				<label><input type="radio" name="pain" id="" <?php if($pain=="none"){echo "checked='checked'";}?> value="none" class="" /> None</label>
				<label><input type="radio" name="pain" id="" <?php if($pain=="mild"){echo "checked='checked'";}?> value="mild" class="" /> Mild</label>
				<label><input type="radio" name="pain" id="" <?php if($pain=="moderate"){echo "checked='checked'";}?> value="moderate" class="" /> Moderate</label>
				<label><input type="radio" name="pain" id="" <?php if($pain=="severe"){echo "checked='checked'";}?> value="severe" class="" /> Severe</label>
			</th>
		</tr>
		<tr>
			<th>Urine Output at least 30/hr</th>
			<th>
				<label><input type="radio" name="urine" id="" <?php if($urine=="adeq"){echo "checked='checked'";}?> value="adeq" class="" /> Adequate</label>
				<label><input type="radio" name="urine" id="" <?php if($urine=="inadeq"){echo "checked='checked'";}?> value="inadeq" class="" /> Inadequate</label>
			</th>
		</tr>
		<tr>
			<th>Others</th>
			<th>
				<input type="text" class="span8" id="oth" value="<?php echo $others; ?>" placeholder="Others" />
			</th>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;background:#dddddd;">
				<button type="button" class="btn btn-primary" <?php echo $disb;?> onclick="save_post_srec()"><i class="icon icon-save"></i> Save</button>
			</td>
		</tr>
	</table>
	<style>
		label{display:inline-block;margin-bottom:0px;font-weight:bold;margin-right:10px;}
		label:hover{color:#222222;}
		input[type="radio"]{margin:0px 0px 0px;}
	</style>
	<script>
		$(".datepicker").datepicker({dateFormat: 'yy-mm-dd',});
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 05,}});
		//alert($('input[name=pain]:checked').val());
	</script>
	<?php
}

if($type==10)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	
	$req_no=mysqli_real_escape_string($link,$_POST['req_no']);
	$surgery=mysqli_real_escape_string($link,$_POST['surgery']);
	$notes=mysqli_real_escape_string($link,$_POST['notes']);
	$template=mysqli_real_escape_string($link,$_POST['template']);
	
	$airway=$_POST['airway'];
	$obst=$_POST['obst'];
	$score=$_POST['score'];
	$pul=$_POST['pul'];
	$vital=$_POST['vital'];
	$consc=$_POST['consc'];
	$orien=$_POST['orien'];
	$motor=$_POST['motor'];
	$cardio=$_POST['cardio'];
	$site=$_POST['site'];
	$hemor=$_POST['hemor'];
	$pain=$_POST['pain'];
	$urine=$_POST['urine'];
	$oth=$_POST['oth'];
	$oth= mysqli_real_escape_string($link,$oth);
	$usr=$_POST['usr'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_post_surgery` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `ot_post_surgery` SET `req_no`='$req_no',`surgery`='$surgery',`notes`='$notes',`template`='$template', `airway`='$airway',`hypopharyngeal`='$obst',`saturation`='$score',`pulmonary`='$pul',`vital`='$vital',`consciousness`='$consc',`orientation`='$orien',`motor`='$motor',`cardiovascular`='$cardio',`surgical`='$site',`hemorrhage`='$hemor',`pain`='$pain',`urine`='$urine',`others`='$oth' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ot_post_surgery`(`patient_id`, `ipd_id`, `schedule_id`, `req_no`, `surgery`, `notes`, `template`, `airway`, `hypopharyngeal`, `saturation`, `pulmonary`, `vital`, `consciousness`, `orientation`, `motor`, `cardiovascular`, `surgical`, `hemorrhage`, `pain`, `urine`, `others`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$shed','$req_no','$surgery','$notes','$template','$airway','$obst','$score','$pul','$vital','$consc','$orien','$motor','$cardio','$site','$hemor','$pain','$urine','$oth','$date','$time','$usr')");
	}
	echo "Saved";
}

if($type==11)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$systolic=$_POST['systolic'];
	$diastolic=$_POST['diastolic'];
	$rr=$_POST['rr'];
	$temp=$_POST['temp'];
	$weight=$_POST['weight'];
	$hr=$_POST['hr'];
	$aps=$_POST['aps'];
	$hb=$_POST['hb'];
	$tlc=$_POST['tlc'];
	$dlc=$_POST['dlc'];
	$esr=$_POST['esr'];
	$pcv=$_POST['pcv'];
	$blood=$_POST['blood'];
	$fbs=$_POST['fbs'];
	$ppbs=$_POST['ppbs'];
	$rbs=$_POST['rbs'];
	$urea=$_POST['urea'];
	$creat=$_POST['creat'];
	$sod=$_POST['sod'];
	$pot=$_POST['pot'];
	$cl=$_POST['cl'];
	$ca=$_POST['ca'];
	$mg=$_POST['mg'];
	$lab_other=$_POST['lab_other'];
	$l_other=$_POST['l_other'];
	$l_other= str_replace("'", "''", "$l_other");
	$bt=$_POST['bt'];
	$ct=$_POST['ct'];
	$pt=$_POST['pt'];
	$aptt=$_POST['aptt'];
	$inr=$_POST['inr'];
	$plat=$_POST['plat'];
	$protein=$_POST['protein'];
	$alb=$_POST['alb'];
	$biliru=$_POST['biliru'];
	$ldh=$_POST['ldh'];
	$amyl=$_POST['amyl'];
	$alkphos=$_POST['alkphos'];
	$choles=$_POST['choles'];
	$trigl=$_POST['trigl'];
	$ldl=$_POST['ldl'];
	$hdl=$_POST['hdl'];
	$vldl=$_POST['vldl'];
	$hbs=$_POST['hbs'];
	$hiv=$_POST['hiv'];
	$t3=$_POST['t3'];
	$t4=$_POST['t4'];
	$tsh=$_POST['tsh'];
	$dvt=$_POST['dvt'];
	$dvt= str_replace("'", "''", "$dvt");
	$nmb=$_POST['nmb'];
	$nmb= str_replace("'", "''", "$nmb");
	$consent=$_POST['consent'];
	$consult=$_POST['consult'];
	$consult= str_replace("'", "''", "$consult");
	$sent_date=$_POST['sent_date'];
	$sent_time=$_POST['sent_time'];
	$prophylaxis=$_POST['prophylaxis'];
	$prophylaxis= str_replace("'", "''", "$prophylaxis");
	$drugs=$_POST['drugs'];
	$drugs= str_replace("'", "''", "$drugs");
	$invest=$_POST['invest'];
	$others=$_POST['others'];
	$others= str_replace("'", "''", "$others");
	$fit=$_POST['fit'];
	$usr=$_POST['usr'];

	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_pre_anaesthesia` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($num>0)
	{
		//mysqli_query($link,"UPDATE `ot_pre_anaesthesia` SET `systolic`='$systolic',`diastolic`='$diastolic',`rr`='$rr',`temp`='$temp',`weight`='$weight',`hr`='$hr',`aps`='$aps',`hb`='$hb',`tlc`='$tlc',`dlc`='$dlc',`esr`='$esr',`pcv`='$pcv',`fbs`='$fbs',`ppbs`='$ppbs',`rbs`='$rbs',`urea`='$urea',`creatinine`='$creat',`sodium`='$sod',`potassium`='$pot',`chlorine`='$cl',`calcium`='$ca',`magnesium`='$mg',`lab_other`='$l_other',`bt`='$bt',`ct`='$ct',`pt`='$pt',`aptt`='$aptt',`inr`='$inr',`platelets`='$plat',`protein`='$protein',`alb`='$alb',`biliru`='$biliru',`ldh`='$ldl',`amyl`='$amyl',`alk_phos`='$alkphos',`cholestrol`='$choles',`trigl`='$trigl',`ldl`='$ldl',`hdl`='$hdl',`vldl`='$vldl',`hbs`='$hbs',`hiv`='$hiv',`t3`='$t3',`t4`='$t4',`tsh`='$tsh',`dvt`='$dvt',`nmb`='$nmb',`consent`='$consent',`consult`='$consult',`sent_date`='$sent_date',`sent_time`='$sent_time',`prophylaxis`='$prophylaxis',`drugs`='$drugs',`invest`='$invest',`others`='$others',`fit`='$fit' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		mysqli_query($link,"UPDATE `patient_info` SET `blood_group`='$blood' WHERE `patient_id`='$uhid'");
	}
	else
	{
		//mysqli_query($link,"INSERT INTO `ot_pre_anaesthesia`(`patient_id`, `ipd_id`, `systolic`, `diastolic`, `rr`, `temp`, `weight`, `hr`, `aps`, `hb`, `tlc`, `dlc`, `esr`, `pcv`, `fbs`, `ppbs`, `rbs`, `urea`, `creatinine`, `sodium`, `potassium`, `chlorine`, `calcium`, `magnesium`, `lab_other`, `bt`, `ct`, `pt`, `aptt`, `inr`, `platelets`, `protein`, `alb`, `biliru`, `ldh`, `amyl`, `alk_phos`, `cholestrol`, `trigl`, `ldl`, `hdl`, `vldl`, `hbs`, `hiv`, `t3`, `t4`, `tsh`, `dvt`, `nmb`, `consent`, `consult`, `sent_date`, `sent_time`, `prophylaxis`, `drugs`, `invest`, `others`, `fit`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$systolic','$diastolic','$rr','$temp','$weight','$hr','$aps','$hb','$tlc','$dlc','$esr','$pcv','$fbs','$ppbs','$rbs','$urea','$creat','$sod','$pot','$cl','$ca','$mg','$l_other','$bt','$ct','$pt','$aptt','$inr','$plat','$protein','$alb','$biliru','$ldh','$amyl','$alkphos','$choles','$trigl','$ldl','$hdl','$vldl','$hbs','$hiv','$t3','$t4','$tsh','$dvt','$nmb','$consent','$consult','$sent_date','$sent_time','$prophylaxis','$drugs','$invest','$others','$fit','$date','$time','$usr')");
		mysqli_query($link,"UPDATE `patient_info` SET `blood_group`='$blood' WHERE `patient_id`='$uhid'");
		mysqli_query($link,"UPDATE `ot_book` SET `pac_status`='1' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	echo "Saved";
}

if($type==12)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	$asa=$_POST['asa'];
	$asa_stat=$_POST['asa_stat'];
	$ident=$_POST['ident'];
	$consent=$_POST['consent'];
	$oral=$_POST['oral'];
	$pr=$_POST['pr'];
	$bp=$_POST['bp'];
	$heart=$_POST['heart'];
	$anaes=$_POST['anaes'];
	$ecg=$_POST['ecg'];
	$spo=$_POST['spo'];
	$nibp=$_POST['nibp'];
	$temp=$_POST['temp'];
	$proc=$_POST['proc'];
	$pos=$_POST['pos'];
	$incision=$_POST['incision'];
	$usr=$_POST['usr'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_notes` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `ot_notes` SET `asa`='$asa',`asa_stat`='$asa_stat',`identify`='$ident',`consent`='$consent',`pre_oprative_oral`='$oral',`pr`='$pr',`bp`='$bp',`heart`='$heart',`anaes_type`='$anaes',`ecg`='$ecg',`spo`='$spo',`nibp`='$nibp',`temp`='$temp',`procedure_perform`='$proc',`patient_pos`='$pos',`incision`='$incision' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ot_notes`(`patient_id`, `ipd_id`, `schedule_id`, `asa`, `asa_stat`, `identify`, `consent`, `pre_oprative_oral`, `pr`, `bp`, `heart`, `anaes_type`, `ecg`, `spo`, `nibp`, `temp`, `procedure_perform`, `patient_pos`, `incision`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$shed','$asa','$asa_stat','$ident','$consent','$oral','$pr','$bp','$heart','$anaes','$ecg','$spo','$nibp','$temp','$proc','$pos','$incision','$date','$time','$usr')");
	}
	echo "Saved";
}

if($type==13)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	$perf=$_POST['perf'];
	$remark=$_POST['remark'];
	$anaes_st_time=$_POST['anaes_st_time'];
	$anaes_en_time=$_POST['anaes_en_time'];
	$surg_note=$_POST['surg_note'];
	$ot=$_POST['ot'];
	$anaes=$_POST['anaes'];
	$surg_type=$_POST['surg_type'];
	$pat_in_time=$_POST['pat_in_time'];
	$act_st_time=$_POST['act_st_time'];
	$sur_st_time=$_POST['sur_st_time'];
	$sur_en_time=$_POST['sur_en_time'];
	$act_en_time=$_POST['act_en_time'];
	$pro_st_time=$_POST['pro_st_time'];
	$pro_en_time=$_POST['pro_en_time'];
	$pat_out_time=$_POST['pat_out_time'];
	$det=$_POST['det'];
	$usr=$_POST['usr'];
	
	$qry=mysqli_query($link,"SELECT * FROM `ot_surgery_record` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		mysqli_query($link,"UPDATE `ot_surgery_record` SET `perform`='$perf',`remarks`='$remark',`anes_st_time`='$anaes_st_time',`anes_en_time`='$anaes_en_time',`surgery_note`='$surg_note',`ot_area_id`='$ot',`type_id`='$anaes',`procedure_id`='$surg_type',`ot_in_time`='$pat_in_time',`act_st_time`='$act_st_time',`sur_st_time`='$act_st_time',`sur_en_time`='$sur_en_time',`act_en_time`='$act_en_time',`proc_st_time`='$pro_st_time',`proc_en_time`='$pro_en_time',`ot_out_time`='$pat_out_time' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ot_surgery_record`(`patient_id`, `ipd_id`, `schedule_id`, `perform`, `remarks`, `anes_st_time`, `anes_en_time`, `surgery_note`, `ot_area_id`, `type_id`, `procedure_id`, `ot_in_time`, `act_st_time`, `sur_st_time`, `sur_en_time`, `act_en_time`, `proc_st_time`, `proc_en_time`, `ot_out_time`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$shed','$perf','$remark','$anaes_st_time','$anaes_en_time','$surg_note','$ot','$anaes','$surg_type','$pat_in_time','$act_st_time','$sur_st_time','$sur_en_time','$act_en_time','$pro_st_time','$pro_en_time','$pat_out_time','$date','$time','$usr')");
		$rid=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(record_id) as max FROM `ot_surgery_record` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed' AND `user`='$usr'"));
		$all=explode("##",$det);
		foreach($all as $vl)
		{
			$v=explode("@",$vl);
			$emp=$v[0];
			$fdt=$v[1];
			$ftm=$v[2];
			$tdt=$v[3];
			$ttm=$v[4];
			if($emp && $fdt && $ftm && $tdt && $ttm)
			mysqli_query($link,"INSERT INTO `ot_surgery_record_resource`(`record_id`, `emp_id`, `from_date`, `from_time`, `to_date`, `to_time`, `date`, `time`, `user`) VALUES ('$rid[max]','$emp','$fdt','$ftm','$tdt','$ttm','$date','$time','$usr')");
		}
	}
	echo "Saved";
}

if($type==14)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	$usr=$_POST['usr'];
	
	$leave=mysqli_fetch_array(mysqli_query($link,"SELECT `leaved` FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($leave['leaved']==1)
	{
		$disb="disabled='disabled'";
	}
	if($leave['leaved']==0)
	{
		$disb="";
	}
	
	$q=mysqli_query($link,"SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd' AND `type`='4'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
	?>
	<table class="table table-bordered table-condensed" id="" width="100%">
		<tr>
			<th rowspan="2">#</th>
			<th rowspan="2">Drug Name</th>
			<th colspan="2"><center>Quantity</center></th>
			<th rowspan="2"><center>Date Time</center></th>
			<th rowspan="2"><center>User</center></th>
		</tr>
		<tr>
			<th>Claimed</th>
			<th>Received</th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			$m=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
			$dis_none="";
			if($r["status"]>0)
			{
				$dis_none="style='display:none;'";
			}
			$emp_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
		?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $m['item_name'];?></td><td><?php echo $r['quantity'];?> </td>
			<td><?php echo $r['status'];?></td>
			<td>
				<?php echo convert_date_g($r['date']);?>
				<?php echo convert_time($r['time']);?>
			</td>
			<td>
				<?php echo $emp_info['name'];?>
				<button class="btn btn-mini btn-danger text-right" <?php echo $disb;?> onClick="del_indent_medicine('<?php echo $r["slno"]; ?>')" <?php echo $dis_none; ?>><i class="icon-remove-sign"></i></button>
			</td>
		</tr>
		<?php
			$n++;
		}
		?>
	</table>
	<button type="button" class="btn btn-info" id="indad" <?php echo $disb;?> onclick="$('#hide_ind_list').show();$('#indad').hide();$('#ins_ind').hide()"><i class="icon-plus"></i> Add New</button>
	<?php
	}
	else
	{
	?>
	<button type="button" class="btn btn-info" id="indad" <?php echo $disb;?> onclick="$('#hide_ind_list').show();$('#indad').hide();$('#ins_ind').hide()"><i class="icon-plus"></i> Add</button>
	<?php
	}
	?>
	<div id="hide_ind_list" style="display:none;">
		<table class="table table-condensed" id="">
			<tr>
				<td>
					Drug Name: <input type="text" class="span6" id="ind_med" onFocus="load_ind_medi()" onkeyup="load_ind_medi1(this.value,event)" onBlur="javascript:$('#ind_med_list').fadeOut(500)" />
					<input type="text" class="span6" id="mediid" style="display:none;" />
					<div id="ind_med_list">
					</div>
				</td>
			</tr>
			<tr id="ind_data" style="display:none;">
				<td>
					Quantity: <input type="text" class="span1" onkeyup="meditab(this.id,event)" id="qnt" placeholder="Quantity" />
					<button type="button" class="btn btn-primary" id="indsv" onclick="add_ind_data()"><i class="icon-plus"></i> Add</button>
					<button type="button" class="btn btn-danger" onclick="$('#ind_med').val('');$('#mediid').val('');$('#indad').show();$('#select_load').html('');$('#ind_data').hide(500);$('#hide_ind_list').hide(500)"><i class="icon-ban-circle"></i> Cancel</button>
				</td>
			</tr>
			<tr>
				<td id="select_load">
				
				</td>
			</tr>
			<tr>
				<td>
					<span class="text-right"><button type="button" class="btn btn-primary" id="ins_ind" onclick="insert_ot_med_ind()"><i class="icon-file"></i> Save</button></span>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

if($type==15)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$det=$_POST['det'];
	$usr=$_POST['usr'];
	
	$type=4; // OT dashboard Indent
	
	$val=explode("#gg#",$det);
	$ind=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(indent_num) as max FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd'"));
	$in=$ind['max']+1;
	foreach($val as $dtt)
	{
		if($dtt)
		{
			$dt=explode("@@",$dtt);
			$med=$dt[0];
			$qnt=$dt[1];
			if($med && $qnt)
			{
				mysqli_query($link, "INSERT INTO `ipd_pat_medicine_indent`(`patient_id`, `ipd_id`, `indent_num`, `item_code`, `quantity`, `status`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$in','$med','$qnt','0','$date','$time','$usr')");
				
				mysqli_query($link," INSERT INTO `patient_medicine_detail`(`patient_id`, `pin`, `indent_num`, `item_code`, `dosage`, `instruction`, `quantity`, `status`, `date`, `time`, `user`, `type`, `bill_no`) VALUES ('$uhid','$ipd','$in','$med','','0','$qnt','0','$date','$time','$usr','$type','') ");
			}
		}
	}
}

if($type==16)
{
	$slno=$_POST['slno'];
	
	mysqli_query($link," DELETE FROM `patient_medicine_detail` WHERE `slno`='$slno' ");
	
}

if($type==17)
{
	//$uhid=$_POST['uhid'];
	$val="<select class='span3' onchange='load_service(this)'>";
	$val.="<option value='0'>Select</option>";
	$q=mysqli_query($link,"SELECT * FROM `ot_service_master` ORDER BY `service_name`");
	while($r=mysqli_fetch_array($q))
	{
		$val.="<option value='".$r['ot_service_id']."'>".$r['service_name']."</option>";
	}
	$val.="</select>";
	echo $val;
}

if($type==18)
{
	$val=$_POST['val'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_service_master` WHERE `ot_service_id`='$val'"));
	echo $v['service_name']."@@@".$v['rate'];
}

if($type==19)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	
	$perf=$_POST['perf'];
	$remark=$_POST['remark'];
	$anaes_st_time=$_POST['anaes_st_time'];
	$anaes_en_time=$_POST['anaes_en_time'];
	$surg_note=$_POST['surg_note'];
	$ot=$_POST['ot'];
	$anaes=$_POST['anaes'];
	$surg_type=$_POST['surg_type'];
	$pat_in_time=$_POST['pat_in_time'];
	$act_st_time=$_POST['act_st_time'];
	$sur_st_time=$_POST['sur_st_time'];
	$sur_en_time=$_POST['sur_en_time'];
	$act_en_time=$_POST['act_en_time'];
	$pro_st_time=$_POST['pro_st_time'];
	$pro_en_time=$_POST['pro_en_time'];
	$pat_out_time=$_POST['pat_out_time'];
	
	$res_data=$_POST['res_data'];
	$serv_data=$_POST['serv_data'];
	$usr=$_POST['usr'];
	
	//---------------------------------------------------------------------------
	$qry=mysqli_query($link,"SELECT * FROM `ot_surgery_record` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		mysqli_query($link,"UPDATE `ot_surgery_record` SET `perform`='$perf',`remarks`='$remark',`anes_st_time`='$anaes_st_time',`anes_en_time`='$anaes_en_time',`surgery_note`='$surg_note',`ot_area_id`='$ot',`type_id`='$anaes',`procedure_id`='$surg_type',`ot_in_time`='$pat_in_time',`act_st_time`='$act_st_time',`sur_st_time`='$act_st_time',`sur_en_time`='$sur_en_time',`act_en_time`='$act_en_time',`proc_st_time`='$pro_st_time',`proc_en_time`='$pro_en_time',`ot_out_time`='$pat_out_time' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ot_surgery_record`(`patient_id`, `ipd_id`, `schedule_id`, `perform`, `remarks`, `anes_st_time`, `anes_en_time`, `surgery_note`, `ot_area_id`, `type_id`, `procedure_id`, `ot_in_time`, `act_st_time`, `sur_st_time`, `sur_en_time`, `act_en_time`, `proc_st_time`, `proc_en_time`, `ot_out_time`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$shed','$perf','$remark','$anaes_st_time','$anaes_en_time','$surg_note','$ot','$anaes','$surg_type','$pat_in_time','$act_st_time','$sur_st_time','$sur_en_time','$act_en_time','$pro_st_time','$pro_en_time','$pat_out_time','$date','$time','$usr')");
		//$rid=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(record_id) as max FROM `ot_surgery_record` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `user`='$usr'"));
	}
	//---------------------------------------------------------------------------
	
	$bed=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_id` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$cnt=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(`counter`) AS mx FROM `ot_pat_service_details_edit` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'"));
	$countt=$cnt['mx']+1;
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'"));
	if($num>0)
	{
		// edit
		mysqli_query($link,"DELETE FROM `ot_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	}
	mysqli_query($link,"DELETE FROM `ot_resource` WHERE `schedule_id`='$shed'");
	mysqli_query($link,"DELETE FROM `doctor_service_done` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	$ot_date=mysqli_fetch_assoc(mysqli_query($link,"SELECT `ot_date` FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$res=explode("#@#",$res_data);
	foreach($res as $r)
	{
		$vl=explode("@@",$r);
		$res_id=$vl[0];
		$emp_id=$vl[1];
		$amt=$vl[2];
		if($res_id)
		{
			if($res_id==1386 && $emp_id=="extra")
			{
				$typ=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$res_id'"));
				$serv_txt=$typ['type'];
				mysqli_query($link,"INSERT INTO `ot_pat_service_details`(`patient_id`, `ipd_id`, `schedule_id`, `resourse_id`, `emp_id`, `ot_group_id`, `ot_service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','$shed','$res_id','0','155','$res_id','$serv_txt','1','$amt','$amt','0','$usr','$time','$date','$bed[bed_id]')");
				mysqli_query($link,"INSERT INTO `ot_pat_service_details_edit`(`patient_id`, `ipd_id`, `schedule_id`, `resourse_id`, `emp_id`, `ot_group_id`, `ot_service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `counter`) VALUES ('$uhid','$ipd','$shed','$res_id','0','155','$res_id','$serv_txt','1','$amt','$amt','0','$usr','$time','$date','$bed[bed_id]','$countt')");
				mysqli_query($link,"INSERT INTO `ot_resource`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$shed','$res_id','0')");
				mysqli_query($link,"UPDATE `ot_book` SET `ot_area_id`='$ot_room' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
				mysqli_query($link,"UPDATE `ot_schedule` SET `ot_no`='$ot_room' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
			}
			if($emp_id!="extra")
			{
				$typ=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$res_id'"));
				//$serv_txt=$typ['type']." Charge";
				$serv_txt=$typ['type'];
				mysqli_query($link,"INSERT INTO `ot_pat_service_details`(`patient_id`, `ipd_id`, `schedule_id`, `resourse_id`, `emp_id`, `ot_group_id`, `ot_service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','$shed','$res_id','$emp_id','155','$res_id','$serv_txt','1','$amt','$amt','0','$usr','$time','$date','$bed[bed_id]')");
				mysqli_query($link,"INSERT INTO `ot_pat_service_details_edit`(`patient_id`, `ipd_id`, `schedule_id`, `resourse_id`, `emp_id`, `ot_group_id`, `ot_service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `counter`) VALUES ('$uhid','$ipd','$shed','$res_id','$emp_id','155','$res_id','$serv_txt','1','$amt','$amt','0','$usr','$time','$date','$bed[bed_id]','$countt')");
				$cons=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$emp_id'"));
				if($cons)
				{
					$con=$cons['consultantdoctorid'];
				}
				else
				{
					$con=$emp_id;
				}
				if($con>0)
				{
				mysqli_query($link,"INSERT INTO `doctor_service_done`(`patient_id`, `ipd_id`, `service_id`, `consultantdoctorid`, `user`, `date`, `time`, `rel_slno`,`schedule_id`) VALUES ('$uhid','$ipd','$res_id','$con','$usr','$ot_date[ot_date]','$time','0','$shed')");
				}
				mysqli_query($link,"INSERT INTO `ot_resource`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$shed','$res_id','$emp_id')");
			}
			else if($res_id!=1386)
			{
				$typ=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$res_id'"));
				$serv_txt=$typ['type'];
				mysqli_query($link,"INSERT INTO `ot_pat_service_details`(`patient_id`, `ipd_id`, `schedule_id`, `resourse_id`, `emp_id`, `ot_group_id`, `ot_service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','$shed','$res_id','0','155','$res_id','$serv_txt','1','$amt','$amt','0','$usr','$time','$date','$bed[bed_id]')");
				mysqli_query($link,"INSERT INTO `ot_pat_service_details_edit`(`patient_id`, `ipd_id`, `schedule_id`, `resourse_id`, `emp_id`, `ot_group_id`, `ot_service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `counter`) VALUES ('$uhid','$ipd','$shed','$res_id','0','155','$res_id','$serv_txt','1','$amt','$amt','0','$usr','$time','$date','$bed[bed_id]','$countt')");
				mysqli_query($link,"INSERT INTO `ot_resource`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$shed','$res_id','$emp_id')");
			}
		}
	}
	/*
	$ser=explode("#@#",$serv_data);
	foreach($ser as $ss)
	{
		$v=explode("@@",$ss);
		$serv_id=$v[0];
		$serv_txt=$v[1];
		$serv_qnt=$v[2];
		$serv_rate=$v[3];
		$serv_amt=$v[4];
		if($serv_id && $serv_qnt && $serv_rate && $serv_amt)
		{
			mysqli_query($link,"INSERT INTO `ot_pat_service_details`(`patient_id`, `ipd_id`, `schedule_id`, `resourse_id`, `emp_id`, `ot_group_id`, `ot_service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','$shed','$res_id','0','155','$serv_id','$serv_txt','$serv_qnt','$serv_rate','$serv_amt','0','$usr','$time','$date','$bed[bed_id]')");
		}
	}
	*/
	//echo $shed;
}

if($type==20)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	$usr=$_POST['usr'];
	
	$lv=mysqli_fetch_array(mysqli_query($link,"SELECT `leaved` FROM `ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'"));
	if($lv['leaved']==0)
	{
		$disb="";
		$clas="btn-primary";
		$func="ot_leave_done('$shed')";
	}
	else if($lv['leaved']==1)
	{
		$disb="disabled='disabled'";
		$clas="btn-danger";
		$func="";
	}
	?>
	<button type="button" class="btn <?php echo $clas;?>" <?php echo $disb;?> onclick="<?php echo $func;?>">Leave OT</button>
	<?php
}

if($type==21)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$sh=$_POST['sh'];
	$usr=$_POST['usr'];
	
	if(mysqli_query($link,"UPDATE `ot_schedule` SET `leaved`='1' WHERE `schedule_id`='$sh' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"))
	{
		mysqli_query($link,"INSERT INTO `ot_room_leaved`(`patient_id`, `ipd_id`, `schedule_id`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$sh','$date','$time','$usr')");
		mysqli_query($link,"DELETE FROM `ot_process` WHERE `schedule_id`='$sh' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		echo "OT Leaved";
	}
	else
	{
		echo "Error";
	}
}

if($type==22)
{
	$id=$_POST['id'];
	$rt=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_area_rate` FROM `ot_area_master` WHERE `ot_area_id`='$id'"));
	echo $rt['ot_area_rate'];
}

if($type==99)
{
	$uhid=$_POST['uhid'];
}
?>
