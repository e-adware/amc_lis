<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">PAC Dashboard</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<?php
	$uhid=base64_decode($_GET['uhid']);
	$ipd=base64_decode($_GET['ipd']);
	$show=base64_decode($_GET['show']);
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	if($pat['dob'])
	$age=age_calculator($pat['dob'])." (".$pat['dob'].")";
	else
	$age=$pat['age']." ".$pat['age_type'];
	$adm=mysqli_fetch_array(mysqli_query($link,"SELECT `date` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
	
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
	
	?>
	<span style="float:right;"><input type="button" class="btn btn-info" id="add" value="Back to list" onclick="window.location='processing.php?param=211'" style="" /></span>
	<table class="table table-condensed table-bordered" style="background: snow">
		<tr>
			<th>UHID</th>
			<th>PIN</th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Admitted On</th>
			<th>Admitted Under</th>
		</tr>
		<tr>
			<td><?php echo $uhid;?></td>
			<td><?php echo $ipd;?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $pat['sex'];?></td>
			<td><?php echo convert_date_g($adm['date']);?></td>
			<td><?php echo $doc['Name'];?></td>
		</tr>
	</table>
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="ipd" value="<?php echo $ipd;?>" style="display:none;" />
	<input type="text" id="show" value="<?php echo $show;?>" style="display:none;" />
	<input type="hidden" id="chk_val" value="0"/>
	<input type="hidden" id="chk_val1" value="0"/>
	<div class="span11" style="margin-left:0px;">
		<table class="table table-condensed">
		<tr>
			<th colspan="6" style="background:#dddddd;">Vitals</th>
		</tr>
		<tr>
			<th>BP<br/>
			<input id="systolic" value="<?php echo $systolic; ?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" placeholder="Systolic" />
			<input id="diastolic" value="<?php echo $diastolic; ?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" type="text" placeholder="Diastolic" />
			<th>RR:<br/><input type="text" id="rr" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $rr; ?>" placeholder="RR" /></th>
			<th>Temp:<br/><input type="text" id="temp" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $temp; ?>" placeholder="Temp" /></th>
			<th>Weight:<br/><input type="text" id="weight" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $weight; ?>" placeholder="Weight" /></th>
			<th>H.R/Pulse Rate:<br/><input type="text" id="hr" class="span1" onkeyup="tab(this.id,event)" value="<?php echo $hr; ?>" placeholder="H.R" /></th>
			<th></th>
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
			<th colspan="3"><textarea id="drugs" placeholder="Drugs" onkeyup="tab(this.id,event)"><?php echo $drugs;?></textarea></th>
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
			<th colspan="3"><textarea id="others" onkeyup="tab(this.id,event)" placeholder="Others"><?php echo $others;?></textarea></th>
		</tr>
	</table>
	<div>
		<label><input type="radio" name="fit" id="" <?php if($fit=="fit"){echo "checked='checked'";}?> value="fit" class="" /> Fit</label>
		<label><input type="radio" name="fit" id="" <?php if($fit=="not"){echo "checked='checked'";}?> value="not" class="" /> Not Fit</label>
	</div>
	<div>
		<span class="text-right">
			<button type="button" id="sav" class="btn btn-info" onclick="save_pre_anes_notes()">Save</button>
			<button type="button" class="btn btn-danger" onclick="">Clear</button>
		</span>
	</div>
	<script>
		$(".datepicker").datepicker({dateFormat: 'yy-mm-dd',});
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 05,}});
	</script>
	</div>
	<!--modal-->
	<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
	<input type="text" id="modtxt" value="0" style="display:none"/>
	<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true"><b>x</b></button>
					<div id="results">
					
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--modal end-->
</div>
<div id="gter" class="gritter-item" style="display:none;width:200px;">
	<div class="gritter-close" style="display:block;" onclick="$('.a').removeClass('clk');$('#gter').fadeOut(500)"></div>
	<span class="gt-title" style="font-size: 12px;font-family: verdana;font-weight: bold;padding-left: 10px;">Medicine Administor</span>
	<p id='fol_med' style="padding:6px;font-size:12px;"></p>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<link rel="stylesheet" href="../css/select2.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../js/jquery.gritter.min.js"></script>
<script src="../js/select2.min.js"></script>
<script src="include/jquery.ui.timepicker.js"></script><!-- Timepicker js -->
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css" /><!-- Timepicker css -->
<script>
	$(document).ready(function()
	{
		//~ $("#fat_name").keyup(function(e)
		//~ {
			//~ $(this).val($(this).val().toUpperCase());
		//~ });
		
		$(".datepicker").datepicker({dateFormat: 'yy-mm-dd',});
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 05,}});
	});
//------------------------------------------------------//

function tab(id,e)
{
// systolic@diastolic@rr@temp@weight@hr@aps@hb@tlc@dlc@esr@pcv@blood@fbs@ppbs@rbs@l_other@urea@creat@sod@pot@cl@mg@s_other@bt@ct@pt@aptt@inr@plat@protein@alb@biliru@ldh@amyl@alkphos@choles@trigl@ldl@hdl@vldl@hbs@hiv@t3@t4@tsh@dvt@nmb@consent@consult@sent_date@sent_time@prophylaxis@drugs@invest@others@fit@
	if(e.keyCode==13)
	{
		if(id=="systolic")
		$("#diastolic").focus();
		if(id=="diastolic")
		$("#rr").focus();
		if(id=="rr")
		$("#temp").focus();
		if(id=="temp")
		$("#weight").focus();
		if(id=="weight")
		$("#hr").focus();
		if(id=="hr")
		$("#aps").focus();
		if(id=="aps")
		$("#hb").focus();
		if(id=="hb")
		$("#tlc").focus();
		if(id=="tlc")
		$("#dlc").focus();
		if(id=="dlc")
		$("#esr").focus();
		if(id=="esr")
		$("#pcv").focus();
		if(id=="pcv")
		$("#blood").focus();
		if(id=="blood")
		$("#fbs").focus();
		if(id=="fbs")
		$("#ppbs").focus();
		if(id=="ppbs")
		$("#rbs").focus();
		if(id=="rbs")
		$("#urea").focus();
		if(id=="urea")
		$("#creat").focus();
		if(id=="creat")
		$("#sod").focus();
		if(id=="sod")
		$("#pot").focus();
		if(id=="pot")
		$("#cl").focus();
		if(id=="cl")
		$("#ca").focus();
		if(id=="ca")
		$("#mg").focus();
		if(id=="mg")
		$("#bt").focus();
		if(id=="bt")
		$("#ct").focus();
		if(id=="ct")
		$("#pt").focus();
		if(id=="pt")
		$("#aptt").focus();
		if(id=="aptt")
		$("#inr").focus();
		if(id=="inr")
		$("#plat").focus();
		if(id=="plat")
		$("#protein").focus();
		if(id=="protein")
		$("#alb").focus();
		if(id=="alb")
		$("#biliru").focus();
		if(id=="biliru")
		$("#ldh").focus();
		if(id=="ldh")
		$("#amyl").focus();
		if(id=="amyl")
		$("#alkphos").focus();
		if(id=="alkphos")
		$("#choles").focus();
		if(id=="choles")
		$("#trigl").focus();
		if(id=="trigl")
		$("#ldl").focus();
		if(id=="ldl")
		$("#hdl").focus();
		if(id=="hdl")
		$("#vldl").focus();
		if(id=="vldl")
		$("#hbs").focus();
		if(id=="hbs")
		$("#hiv").focus();
		if(id=="hiv")
		$("#t3").focus();
		if(id=="t3")
		$("#t4").focus();
		if(id=="t4")
		$("#tsh").focus();
		if(id=="tsh")
		$("#l_other").focus();
		if(id=="dvt")
		$("#nmb").focus();
		if(id=="nmb")
		$("#consult").focus();
		if(id=="consult")
		$("#sent_date").focus();
		if(id=="sent_date")
		$("#sent_time").focus();
		if(id=="sent_time")
		$("#prophylaxis").focus();
		if(id=="prophylaxis")
		$("#drugs").focus();
		if(id=="invest")
		$("#others").focus();
	}
	if(e.shiftKey && e.keyCode == 13)
	{
		if(id=="l_other")
		$("#dvt").focus();
		if(id=="drugs")
		$("#invest").focus();
		if(id=="others")
		$("#sav").focus();
	}
	if(e.keyCode==27)
	{
		$("#sav").focus();
	}
	scrl(id);
}
function scrl(id)
{
	if(id=="aps")
	$("html,body").animate({scrollTop: '200px'},800);
	if(id=="plat")
	$("html,body").animate({scrollTop: '350px'},800);
	if(id=="l_other")
	$("html,body").animate({scrollTop: '500px'},800);
	if(id=="consult")
	$("html,body").animate({scrollTop: '700px'},800);
	if(id=="drugs")
	$("html,body").animate({scrollTop: '1000px'},800);
}
function save_pre_anes_notes()
{
// bp@rr@temp@weight@hr@aps@hb@tlc@dlc@esr@pcv@blood@fbs@ppbs@rbs@b_other@urea@creat@sod@pot@cl@mg@s_other@bt@ct@pt@aptt@inr@plat@protein@alb@biliru@ldh@amyl@alkphos@choles@trigl@ldl@hdl@vldl@hbs@hiv@t3@t4@tsh@dvt@nmb@consent@consult@sent_date@sent_time@prophylaxis@drugs@invest@others@fit@
	$.post("pages/ot_pac_pat_list_ajax.php",
	{
		uhid:$("#uhid").val().trim(),
		ipd:$("#ipd").val().trim(),
		shed:$("#show").val().trim(),
		systolic:$("#systolic").val(),
		diastolic:$("#diastolic").val(),
		rr:$("#rr").val(),
		temp:$("#temp").val(),
		weight:$("#weight").val(),
		hr:$("#hr").val(),
		aps:$("#aps").val(),
		hb:$("#hb").val(),
		tlc:$("#tlc").val(),
		dlc:$("#dlc").val(),
		esr:$("#esr").val(),
		pcv:$("#pcv").val(),
		blood:$("#blood").val(),
		fbs:$("#fbs").val(),
		ppbs:$("#ppbs").val(),
		rbs:$("#rbs").val(),
		urea:$("#urea").val(),
		creat:$("#creat").val(),
		sod:$("#sod").val(),
		pot:$("#pot").val(),
		cl:$("#cl").val(),
		ca:$("#ca").val(),
		mg:$("#mg").val(),
		l_other:$("#l_other").val().trim(),
		bt:$("#bt").val(),
		ct:$("#ct").val(),
		pt:$("#pt").val(),
		aptt:$("#aptt").val(),
		inr:$("#inr").val(),
		plat:$("#plat").val(),
		protein:$("#protein").val(),
		alb:$("#alb").val(),
		biliru:$("#biliru").val(),
		ldh:$("#ldh").val(),
		amyl:$("#amyl").val(),
		alkphos:$("#alkphos").val(),
		choles:$("#choles").val(),
		trigl:$("#trigl").val(),
		ldl:$("#ldl").val(),
		hdl:$("#hdl").val(),
		vldl:$("#vldl").val(),
		hbs:$("#hbs").val(),
		hiv:$("#hiv").val(),
		t3:$("#t3").val(),
		t4:$("#t4").val(),
		tsh:$("#tsh").val(),
		dvt:$("#dvt").val().trim(),
		nmb:$("#nmb").val().trim(),
		consent:$("input[name='consent']:checked").val(),
		consult:$("#consult").val().trim(),
		sent_date:$("#sent_date").val(),
		sent_time:$("#sent_time").val(),
		prophylaxis:$("#prophylaxis").val().trim(),
		drugs:$("#drugs").val().trim(),
		invest:$("#invest").val(),
		others:$("#others").val().trim(),
		fit:$("input[name='fit']:checked").val(),
		usr:$("#user").text().trim(),
		type:"save_pac_details",
	},
	function(data,status)
	{
		//alert(data);
		bootbox.dialog({ message: data});
		setTimeout(function()
		{
			bootbox.hideAll();
		}, 1000);
		//pre_anesthesia();
	})
}

//-------------------------------------------------------------

//-----------------------------------------Load Doctor List Onfocus-------------------------------||
function load_refdoc1()
{
		$("#ref_doc").fadeIn(500);
		$("#r_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},1000);
}
//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||
var doc_tr=1;
var doc_sc=0;
function load_refdoc(val,e)
{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			$("#r_doc").css('border','');
			if(unicode!=40 && unicode!=38)
			{
				$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
				$("#ref_doc").fadeIn(500);
				$.post("pages/load_refdoc_ajax.php"	,
				{
					val:val,
				},
				function(data,status)
				{
					$("#ref_doc").html(data);	
					doc_tr=1;
					doc_sc=0;
				})	
			}
			else if(unicode==40)
			{
				var chk=doc_tr+1;
				var cc=document.getElementById("doc"+chk).innerHTML;
				if(cc)
				{
					doc_tr=doc_tr+1;
					$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_tr-1;
					$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						$("#ref_doc").scrollTop(doc_sc)
						doc_sc=doc_sc+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=doc_tr-1;
				var cc=document.getElementById("doc"+chk).innerHTML;
				if(cc)
				{
					doc_tr=doc_tr-1;
					$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_tr+1;
					$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						doc_sc=doc_sc-30;
						$("#ref_doc").scrollTop(doc_sc)
					}
				}
			}
			
		}
		else
		{
			$("#r_doc").css('border','');
			var cen_chk1=document.getElementById("chk_val2").value
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.split("#");
				var doc_naam=docs[2].trim()
				$("#r_doc").val(doc_naam+"-"+docs[1]);
				var d_in=docs[3];
				//$("#doc_mark").val(docs[5]);
				$("#doc_info").html(d_in);
				$("#doc_info").fadeIn(500);
				
				if($("#focus_chk").val()!="")
				{
					//$("html, body").animate({ scrollTop: 350 })	
					$("#doc_id").val(docs[1]);
					$("#bedbtn").focus();
					//alert(docs[1]);
				}
				else
				{
					$("#doc_id").val(docs[1]);
					$("#bedbtn").focus();	
				}
			}
		}
}
function doc_load(id,name)
{
	$("#r_doc").val(name+"-"+id);
	$("#doc_id").val(id);
	$("#doc_info").html("");
	$("#ref_doc").fadeOut(500);
	$("#bedbtn").focus();
}
</script>
<style>
	textarea
	{
		resize:none;width:80% !important;
	}
	#myModal
	{
		left: 33%;
		width:75%;
	}
	label{display:inline-block;margin-bottom:0px;font-weight:bold;margin-right:10px;}
	label:hover{color:#222222;}
	input[type="radio"]{margin:0px 0px 0px;}
</style>
