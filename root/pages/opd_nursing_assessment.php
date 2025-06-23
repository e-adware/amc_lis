<?php
$uhid=base64_decode($_GET["uhid"]);
$uhid=trim($uhid);
$opd=base64_decode($_GET["opd"]);
$opd=trim($opd);
$pat=mysqli_fetch_array(mysqli_query($link,"select * from `patient_info` where `patient_id`='$uhid'"));
$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$opd')"));
$qry=mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
if(mysqli_num_rows($qry)>0)
{
	$f=mysqli_fetch_array($qry);
	$weight=$f['weight'];
	$height=$f['height'];
	$bmi1=$f['BMI_1'];
	$bmi2=$f['BMI_2'];
	$spo=$f['spo2'];
	$pulse=$f['pulse'];
	$temp=$f['temp'];
	$pr=$f['PR'];
	$rr=$f['RR'];
	$systolic=$f['systolic'];
	$diastolic=$f['diastolic'];
	$vit_note=$f['note'];
	$dis_all="";
}
else
{
	$weight="";
	$height="";
	$bmi1="";
	$bmi2="";
	$spo="";
	$pulse="";
	$temp="";
	$pr="";
	$rr="";
	$systolic="";
	$diastolic="";
	$vit_note="";
	$dis_all="disabled='disabled'";
}
//echo "SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'";
//~ function age_calculator($dob)
//~ {
	//~ $from = new DateTime($dob);
	//~ $to   = new DateTime('today');
	//~ $year=$from->diff($to)->y;
	//~ $month=$from->diff($to)->m;
	//~ if($year==0)
	//~ {
		//~ //$month=$from->diff($to)->m;
		//~ if($month==0)
		//~ {
			//~ $day=$from->diff($to)->d;
			//~ return $day." Days";
		//~ }else
		//~ {
			//~ return $month." Months";
		//~ }
	//~ }else
	//~ {
		//~ return $year.".".$month." Years";
	//~ }
//~ }

?>
<!--header-->
<div id="content-header">
    <div class="header_div">
		<span class="header">Nursing Assessment</span>
		<!--<span style="margin-left:40px;">
			<select id="ward" onchange="search_patient_list()" style="margin-bottom:0;">
				<option value="0">All</option>
				<?php
				$q=mysqli_query($link,"SELECT * FROM `ward_master` ORDER BY `name`");
				while($r=mysqli_fetch_array($q))
				{
				?>
				<option value="<?php echo $r['ward_id'];?>"><?php echo $r['name'];?></option>
				<?php
				}
				?>
			</select>
		</span>-->
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<span style="float:right"><button type="button" class="btn btn-info" onclick="back_page()">Back to list</button></span>
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="opd" value="<?php echo $opd;?>" style="display:none;" />
	<table class="table table-condensed table-bordered table-report" style="background:snow;">
		<tr>
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Consultant Doctor</th>
		</tr>
		<tr>
			<td><?php echo $opd;?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php if($pat['dob']){echo age_calculator($pat['dob'])." (".$pat['dob'].")";}else{echo $pat['age']." ".$pat['age_type'];}?></td>
			<td><?php echo $pat['sex'];?></td>
			<td><?php echo $doc['Name'];?></td>
		</tr>
	</table>
	<table class="table">
		<tbody>
		<tr>
			<td><b>BP:-</b> <b style="float:right;margin-right:10%;">Systolic:</b></td>
			<td><input id="systolic" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" value="<?php echo $systolic;?>" class="span1" type="text" /></td>
			<td><b>Diastolic:</b></td>
			<td><input id="diastolic" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" value="<?php echo $diastolic;?>" class="span1" type="text" /></td>
			<td><b>Pulse</b></td>
			<td><input id="pulse" type="text" value="<?php echo $pulse;?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" class="span1" /></td>
		</tr>
		<tr>
			<td><b style="float:right;margin-right:10%;">Weight</b></td>
			<td><input id="weight" class="span1" onkeyup="physical(this.value,event);tab(this.id,event)" placeholder="KG" value="<?php echo $weight;?>" type="text"></td>
			<td><b>Height</b></td>
			<td><input id="height" class="span1" onkeyup="physical1(this.value,event)" placeholder="CM" value="<?php echo $height;?>" type="text"></td>
			<td><b>BMI</b></td>
			<td><input id="bmi1" readonly="readonly" value="<?php echo $bmi1;?>" style="width:30px;" type="text"> <input id="bmi2" readonly="readonly" value="<?php echo $bmi2;?>" style="width:30px;" type="text"></td>
<!--
			<td><b>Note</b></td>
			<td colspan="8"><input type="text" id="vit_note" value="<?php echo $vit_note;?>" onkeyup="tab(this.id,event)" style="width:80%;" /></td>
-->
		</tr>
		<tr>
			<td colspan="6">
				<span style="float:right;">
					<input type="button" id="sav_vit" class="btn btn-info" value="Save" onclick="save_vital()" />
					<input type="button" id="prnt_vit" class="btn btn-primary" value="Print Prescription" onclick="print_vit('<?php echo $uhid; ?>','<?php echo $opd; ?>')" <?php echo $dis_all;?> />
					<button type="button" class="btn btn-danger" onclick="back_page()">Exit</button>
				</span>
			</td>
		</tr>
	</tbody>
	</table>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#systolic").focus();
	});
	function print_vit(uhid,opd)
	{
		url="pages/prescription_rpt.php?uhid="+uhid+"&opdid="+opd;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=650,width=1000');
	}
	function tab(id,e)
	{
		if(e.keyCode==13)
		{
			$("#systolic").focus();
			if(id=="systolic")
			$("#diastolic").focus();
			if(id=="diastolic")
			$("#pulse").focus();
			if(id=="pulse")
			$("#weight").focus();
			if(id=="weight")
			$("#height").focus();
			if(id=="height")
			$("#vit_note").focus();
			if(id=="vit_note")
			$("#sav_vit").focus();
		}
		if(e.keyCode==27)
		{
			if(id=="course")
			$("#final_diag").focus();
			if(id=="final_diag")
			$("#foll").focus();
			if(id=="foll")
			$("#summ_btn").focus();
		}
	}
	function save_vital()
	{
		$.post("pages/nursing_assessment_ajax.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			weight:$("#weight").val(),
			height:$("#height").val(),
			bmi1:$("#bmi1").val(),
			bmi2:$("#bmi2").val(),
			pulse:$("#pulse").val(),
			systolic:$("#systolic").val(),
			diastolic:$("#diastolic").val(),
			vit_note:$("#vit_note").val(),
			usr:$("#user").text().trim(),
			type:"pat_opd_vital_save",
		},
		function(data,status)
		{
			$("#prnt_vit").attr("disabled",false);
			print_vit($("#uhid").val(),$("#opd").val());
			//~ bootbox.dialog({ message: data});
			//~ setTimeout(function()
			//~ {
				//~ bootbox.hideAll();
				//~ $("#prnt_vit").attr("disabled",false);
				//~ print_vit($("#uhid").val(),$("#opd").val());
			//~ }, 1000);
		})
	}
	function physical(val,e)
	{
		var n=val.length;
		var numex=/^[0-9.]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#weight").val(val);
		}
		
		var ht=$("#height").val();
		if(ht!='' && val!='')
		{
			var ht=ht/100;
			var bmi=(val/(ht*ht));
			var bmi = bmi.toFixed(2);
			var bmi = bmi.split(".");
			$("#bmi1").val(bmi[0]);
			$("#bmi2").val(bmi[1]);
		}else
		{
			$("#bmi1").val("");
			$("#bmi2").val("");
		}
		
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$("#height").focus();
		}
	}
	function physical1(val,e)
	{
		var n=val.length;
		var numex=/^[0-9.]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#height").val(val);
		}
		
		var wt=$("#weight").val();
		if(wt!='' && val!='')
		{
			var val=val/100;
			var bmi=(wt/(val*val));
			var bmi = bmi.toFixed(2);
			var bmi = bmi.split(".");
			$("#bmi1").val(bmi[0]);
			$("#bmi2").val(bmi[1]);
		}else
		{
			$("#bmi1").val("");
			$("#bmi2").val("");
		}
		
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$("#sav_vit").focus();	
		}
	}
	function back_page()
	{
		window.location="processing.php?param=86";
	}
</script>
