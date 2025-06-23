<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Discharge Report Details</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<?php
	$uhid=base64_decode($_GET['uhid']);
	$ipd=base64_decode($_GET['ipd']);
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	if($pat['dob'])
	$age=age_calculator($pat['dob'])." (".$pat['dob'].")";
	else
	$age=$pat['age']." ".$pat['age_type'];
	$adm=mysqli_fetch_array(mysqli_query($link,"SELECT `date` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
	
	$qry=mysqli_query($link,"SELECT * FROM `ot_pre_anaesthesia` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
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
	<span style="float:right;"><input type="button" class="btn btn-info" id="add" value="Back to list" onclick="window.location='processing.php?param=215'" style="" /></span>
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
			<td><?php echo $pat['uhid'];?></td>
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
	<input type="hidden" id="chk_val" value="0"/>
	<input type="hidden" id="chk_val1" value="0"/>
	<div id="discharge_det">
	
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
	
	<!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal modal-lg fade">
		  <div class="modal-body">
			<p id="post_medi">
				
			</p>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="save_disc_medi()" class="btn btn-primary" href="#">Save</a>
			<a data-dismiss="modal" onclick="clearr()" class="btn btn-info" href="#">Cancel</a>
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
		load_pat_det();
		$(".datepicker").datepicker({dateFormat: 'yy-mm-dd',});
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 05,}});
	});
//------------------------------------------------------//
function calc_totday()
	{
		var tot=0;
		var freq=$("#freq").val();
		var unit=$("#unit_day").val();
		var dur=parseInt($("#dur").val());
		var dos=parseInt($("#dos").val());
		if(dos>0)
		{
			if(unit=="Days")
			tot=(dur*dos*1);
			else if(unit=="Weeks")
			tot=(dur*dos*7);
			else if(unit=="Months")
			tot=(dur*dos*30);
			if(freq=="1")
			tot=tot*1;
			else if(freq=="2")
			tot=tot*1;
			else if(freq=="3")
			tot=tot*2;
			else if(freq=="4")
			tot=tot*3;
			else if(freq=="5")
			tot=tot*4;
			else if(freq=="6")
			tot=tot*5;
			else if(freq=="7")
			tot=tot*24;
			else if(freq=="8")
			tot=tot*12;
			else if(freq=="9")
			tot=tot*8;
			else if(freq=="10")
			tot=tot*6;
			else if(freq=="11")
			tot=tot*5;
			else if(freq=="12")
			tot=tot*4;
			else if(freq=="13")
			tot=tot*3;
			else if(freq=="14")
			tot=tot*3;
			else if(freq=="15")
			tot=tot*2;
			else if(freq=="16")
			tot=tot*2;
			else
			tot=0;
			$("#totl").val(tot);
		}
		else
		$("#totl").val('');
	}
	function load_pat_det()
	{
		$.post("pages/ipd_discharge_pat_list_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:"load_pat_det",
		},
		function(data,status)
		{
			$("#discharge_det").html(data);
			//$("html,body").animate({scrollTop: '250px'},800);
		})
	}
	function add_row(r)
	{
		if(r==1)
		{
			var rr=$('#hist_table tbody tr.cc').length;
			var i=1;
			var d="";
			for(i=1;i<=30;i++)
			{
				d+="<option value='"+i+"'>"+i+"</option>";
			}
			var s='<option value="Minutes">Minutes</option><option value="Hours">Hours</option><option value="Days">Days</option><option value="Week">Week</option><option value="Month">Month</option><option value="Year">Year</option>';
			$("#hh").closest("tr").before('<tr class="cc"><th>Chief Complaints</th><td><input type="text" id="chief'+rr+'" class="" onkeyup="sel_chief('+rr+',event)" /></td><td><b>for</b> <select id="cc'+rr+'" class="span2" onkeyup="sel_chief('+rr+',event)"><option value="0">--Select--</option>'+d+'</select> <select id="tim'+rr+'" class="span2" onkeyup="sel_chief('+rr+',event)"><option value="0">--Select--</option>'+s+'</select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
		}
		if(r==2)
		{
			var rr=$('#diag_table tbody tr.diag').length;
				$("#addiagnosis").closest("tr").before('<tr class="diag"><td><input type="text" class="span3" onkeyup="diagtab(1,event)" id="diagnosis1" placeholder="Diagnosis" /></td><td><select id="order1" onkeyup="diagtab(1,event)" class="span2"><option value="0">--Select--</option><option value="Primary">Primary</option><option value="Secondary">Secondary</option></select></td><td><select id="cert1" onkeyup="diagtab(1,event)" class="span2"><option value="0">--Select--</option><option value="Confirmed">Confirmed</option><option value="Presumed">Presumed</option></select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
		}
	}
	function save_comp()
	{
		var rr=$('#hist_table tbody tr.cc').length;
		var comp="";
		for(var j=0;j<rr;j++)
		{
			if($(".cc:eq("+j+")").find('td:first input:first').val() && $(".cc:eq("+j+")").find('td:eq(1) select:first').val()!="0" && $(".cc:eq("+j+")").find('td:eq(1) select:last').val()!="0")
			comp+=$(".cc:eq("+j+")").find('td:first input:first').val()+"@"+$(".cc:eq("+j+")").find('td:eq(1) select:first').val()+"@"+$(".cc:eq("+j+")").find('td:eq(1) select:last').val()+"#g#";
		}
		if(comp!="")
		{
			$.post("pages/nursing_load_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				usr:$("#user").text().trim(),
				comp:comp,
				type:"save_comp",
			},
			function(data,status)
			{
				load_pat_det();
				//$("#post_medi").html(data);
			})
		}
	}
	function save_exam()
	{
		$.post("pages/ipd_discharge_pat_list_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			hist:$("#history").val().trim(),
			exam:$("#exam").val().trim(),
			usr:$("#user").text().trim(),
			type:"ipd_pat_examination",
		},
		function(data,status)
		{
			load_pat_det();
		})
	}
	function examination()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:"ipd_pat_examination",
		},
		function(data,status)
		{
			//$("#cl12").html(data);
			load_pat_det();
			//$("html,body").animate({scrollTop: '250px'},800);
		})
	}
	function save_diagno()
	{
		var rr=$('#diag_table tbody tr.diag').length;
		var diagno="";
		for(var j=0;j<=rr;j++)
		{
			if($(".diag:eq("+j+")").find('td:first input:first').val() && $(".diag:eq("+j+")").find('td:eq(1) select:first').val()!="0" && $(".diag:eq("+j+")").find('td:eq(2) select:first').val()!="0")
			diagno+=$(".diag:eq("+j+")").find('td:first input:first').val()+"@"+$(".diag:eq("+j+")").find('td:eq(1) select:first').val()+"@"+$(".diag:eq("+j+")").find('td:eq(2) select:first').val()+"#g#";
		}
		if(diagno!="")
		{
			$.post("pages/nursing_load_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				usr:$("#user").text().trim(),
				diagno:diagno,
				type:"save_diagno",
			},
			function(data,status)
			{
				load_pat_det();
				//$("#post_medi").html(data);
			})
		}
	}
	function insert_disc_summ()
	{
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			course:$("#course").val().trim(),
			final_diag:$("#final_diag").val().trim(),
			v_bp:$("#v_bp").val().trim(),
			v_pulse:$("#v_pulse").val().trim(),
			v_temp:$("#v_temp").val().trim(),
			v_weight:$("#v_weight").val().trim(),
			foll:$("#foll").val().trim(),
			usr:$("#user").text().trim(),
			type:"insert_disc_summ",
		},
		function(data,status)
		{
			load_pat_det();
		})
	}
	function disc_summary()
	{
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"ipd_disc_summary",
		},
		function(data,status)
		{
			$("#cl22").html(data);
			//$("html,body").animate({scrollTop: '300px'},800);
		})
	}
	function post_drugs()
	{
		$("#dl").click();
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:"ipd_add_medicine_post",
		},
		function(data,status)
		{
			$("#post_medi").html(data);
			setTimeout(function(){$("#mediname").focus()},900);
		})
	}
	function set_medi()
	{
		if($("#mediname").val()=="")
		{
			$("#mediname").focus();
		}
		else if($("#dos").val()=="")
		{
			$("#dos").focus();
		}
		else if($("#freq").val()=="0")
		{
			$("#freq").focus();
		}
		else if($("#st_date").val()=="")
		{
			$("#st_date").focus();
		}
		else if($("#dur").val()=="0")
		{
			$("#dur").focus();
		}
		else if($("#unit_day").val()=="0")
		{
			$("#unit_day").focus();
		}
		else if($("#inst").val()=="")
		{
			$("#inst").focus();
		}
		else
		{
			$("#medi_list_post").show();
			var fq="";
			var ins="";
			var m=$("#mediname").val();
			var medid=0;
			var dos=$("#dos").val();
			var unit=0;
			var freq=$("#freq").val();
			var dur=$("#dur").val();
			var unit_day=$("#unit_day").val();
			var totl=$("#totl").val();
			var inst=$("#inst").val();
			var st_date=$("#st_date").val();
			var con_doc=0;
			if(freq=='1')
			fq="Immediately";
			else if(freq=='2')
			fq="Once a day";
			else if(freq=='3')
			fq="Twice a day";
			else if(freq=='4')
			fq="Thrice a day";
			else if(freq=='5')
			fq="Four times a day";
			else if(freq=='6')
			fq="Five times a day";
			else if(freq=='7')
			fq="Every hour";
			else if(freq=='8')
			fq="Every 2 hours";
			else if(freq=='9')
			fq="Every 3 hours";
			else if(freq=='10')
			fq="Every 4 hours";
			else if(freq=='11')
			fq="Every 5 hours";
			else if(freq=='12')
			fq="Every 6 hours";
			else if(freq=='13')
			fq="Every 7 hours";
			else if(freq=='14')
			fq="Every 8 hours";
			else if(freq=='15')
			fq="Every 10 hours";
			else if(freq=='16')
			fq="Every 12 hours";
			if(inst=="1")
			ins="As Directed";
			else if(inst=="2")
			ins="Before Meal";
			else if(inst=="3")
			ins="Empty Stomach";
			else if(inst=="4")
			ins="After Meal";
			else if(inst=="5")
			ins="In the Morning";
			else if(inst=="6")
			ins="In the Evening";
			else if(inst=="7")
			ins="At Bedtime";
			else if(inst=="8")
			ins="Immediately";
			var med_add= $('#medi_sel_list tr').length;
			if(med_add==0)
			{
				var md_add="<table class='table table-condensed table-bordered' style='style:none' id='medi_sel_list'>";
				md_add+="<tr><th style='width:3%;background-color:#cccccc'>SN<input type='hidden' id='batch' value='' class='m_val'/><span style='display:none;position:fixed;font-size:22px;top:30%;left:40%;color:#e00;' id='msgg'></span></th><th style='width:40%;background-color:#cccccc'>Drugs</th><th style='width:5%;background-color:#cccccc'>Dosage</th><th style='background-color:#cccccc'>Frequency</th><th style='width:8%;background-color:#cccccc'>Duration</th><th style='width:5%;background-color:#cccccc'>Total</th><th style='background-color:#cccccc'>Instruction</th><th style='width:3%;background-color:#cccccc'><i class='icon-trash icon-large'></i></span></th></tr>";
				md_add+="<tr id='"+medid+"' class='disc_medi'><td>1</td><td>"+m+"<input type='hidden' value='"+m+"' class='m_val'/></td><td>"+dos+"<input type='hidden' value='"+dos+"' class='m_val'/></td><td>"+fq+"<input type='hidden' value='"+freq+"' class='m_val'/></td><td>"+dur+" "+unit_day+"<input type='hidden' value='"+dur+"' class='m_val'/><input type='hidden' value='"+unit_day+"' class='m_val'/></td><td>"+totl+"<input type='hidden' value='"+totl+"' class='m_val'/></td><td>"+ins+"<input type='hidden' value='"+inst+"' class='m_val'/></td><td><input type='hidden' value='"+st_date+"' class='m_val'/><span class='text-danger' onclick='$(this).parent().parent().remove()' style='cursor:pointer'><i class='icon-remove'></i></span></td></tr>";
				md_add+="</table>";
				
				$("#medi_list_data").html(md_add);
				med_add++;
			}
			else
			{
				/*if(t_ch==1)
				{
					$("#medi_sel_list").css({'opacity':'0.5'});
					$("#msgg").text("Already Selected");
					$("#msgg").fadeIn(500);
					setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#medi_sel_list").css({'opacity':'1.0'});
					})},600);	
				}*/			
				//else
				{
					var tr=document.createElement("tr");
					tr.setAttribute("id",medid);
					tr.setAttribute("class","disc_medi");
					var td=document.createElement("td");
					var td1=document.createElement("td");
					var td2=document.createElement("td");
					var td3=document.createElement("td");
					var td4=document.createElement("td");
					var td5=document.createElement("td");
					var td6=document.createElement("td");
					var td7=document.createElement("td");
					var tbody=document.createElement("tbody");
					td.innerHTML=med_add;
					td1.innerHTML=m+"<input type='hidden' value='"+m+"' class=''/>";
					td2.innerHTML=dos+"<input type='hidden' value='"+dos+"' class=''/>";
					td3.innerHTML=fq+"<input type='hidden' value='"+freq+"' class=''/>";
					td4.innerHTML=dur+" "+unit_day+"<input type='hidden' value='"+dur+"' class=''/><input type='hidden' value='"+unit_day+"' class='m_val'/>";
					td5.innerHTML=totl+"<input type='hidden' value='"+totl+"' class=''/>";
					td6.innerHTML=ins+"<input type='hidden' value='"+inst+"' class=''/>";
					td7.innerHTML="<input type='hidden' value='"+st_date+"' class=''/><span class='text-danger' onclick='$(this).parent().parent().remove()' style='cursor:pointer'><i class='icon-remove'></i></span>";
					tr.appendChild(td);
					tr.appendChild(td1);
					tr.appendChild(td2);
					tr.appendChild(td3);
					tr.appendChild(td4);
					tr.appendChild(td5);
					tr.appendChild(td6);
					tr.appendChild(td7);
					tbody.appendChild(tr);		
					document.getElementById("medi_sel_list").appendChild(tbody);
				}
			}
			$("#mediname").val('');
			$("#dos").val('');
			$("#unit").val('');
			$("#freq").val('0');
			$("#st_date").val('');
			$("#dur").val('0');
			$("#unit_day").val('0');
			$("#totl").val('');
			$("#inst").val('1');
			$("#mediname").focus();
		}
	}
	function save()
	{
		var diag="";
		var rr=document.getElementById("medi_sel_list").rows.length;
		for(var j=1;j<rr;j++)
		{
			if($("#tr"+j).find('td:first input:first').val() && $("#tr"+j).find('td:eq(1) select:first').val()!="0" && $("#tr"+j).find('td:eq(2) select:first').val()!="0" && $("#tr"+j).find('td:eq(3) select:first').val()!="0")
			diag+=$("#tr"+j).find('td:first input:first').val()+"@"+$("#tr"+j).find('td:eq(1) select:first').val()+"@"+$("#tr"+j).find('td:eq(2) select:first').val()+"@"+$("#tr"+j).find('td:eq(3) select:first').val()+"#g#";
		}
		alert($("#tr1").find('td:eq(1) input:first').val());
		/*
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			all:diag,
			usr:$("#user").text().trim(),
			type:"save_disc_medi",
		},
		function(data,status)
		{
			load_pat_det();
			/*bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				diagnosis();
			}, 1000);//
		})*/
	}
	function save_dis_type()
	{
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			dtype:$("#dtype").val(),
			diagnosed:$("#diagnosed").val(),
			usr:$("#user").text().trim(),
			type:"save_dis_type",
		},
		function(data,status)
		{
			load_pat_det();
		})
	}
	function save_disc_medi()
	{
		var rr=$('#medi_sel_list tbody tr.disc_medi').length;
		var all="";
		for(var j=0;j<=rr;j++)
		{
			var med=$(".disc_medi:eq("+j+")").find('td:eq(1) input:first').val();
			var dos=$(".disc_medi:eq("+j+")").find('td:eq(2) input:first').val();
			var freq=$(".disc_medi:eq("+j+")").find('td:eq(3) input:first').val();
			var dur=$(".disc_medi:eq("+j+")").find('td:eq(4) input:first').val();
			var unit=$(".disc_medi:eq("+j+")").find('td:eq(4) input:last').val();
			var totl=$(".disc_medi:eq("+j+")").find('td:eq(5) input:first').val();
			var inst=$(".disc_medi:eq("+j+")").find('td:eq(6) input:first').val();
			var st_date=$(".disc_medi:eq("+j+")").find('td:eq(7) input:first').val();
			if(med && dos && freq && dur && unit && totl && inst && st_date)
			all+=med+"@@"+dos+"@@"+freq+"@@"+dur+"@@"+unit+"@@"+totl+"@@"+inst+"@@"+st_date+"@@"+"#g#";
		}
		if(all!="")
		{
			$.post("pages/nursing_load_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				usr:$("#user").text().trim(),
				all:all,
				type:"save_disc_medi",
			},
			function(data,status)
			{
				load_pat_det();
				//$("#post_medi").html(data);
			})
		}
	}
	function print_disc_summary()
	{
		var uhid=btoa($("#uhid").val());
		var ipd=btoa($("#ipd").val());
		var usr=btoa($("#user").text().trim());
		url="pages/ipd_discharge_summary.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
	textarea
	{
		resize:none;width:98% !important;
	}
	#myModal, #myAlert
	{
		left: 33%;
		width:75%;
	}
	label{display:inline-block;margin-bottom:0px;font-weight:bold;margin-right:10px;}
	label:hover{color:#222222;}
	input[type="radio"]{margin:0px 0px 0px;}
</style>
