<?php
$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$show=base64_decode($_GET['show']);
if($show)
{
	$pg_header="OT Entry Updates";
}
else
{
	$pg_header="OT Entry Details";
}
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $pg_header;?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<?php
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	if($pat['dob'])
	$age=age_calculator($pat['dob'])." (".$pat['dob'].")";
	else
	$age=$pat['age']." ".$pat['age_type'];
	$adm=mysqli_fetch_array(mysqli_query($link,"SELECT `date` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
	$d_name="";
	if($doc)
	{
		$d_name=$doc['Name'];
	}
	else
	{
		$doc=mysqli_fetch_array(mysqli_query($link,"SELECT b.`ref_name` FROM `uhid_and_opdid` a, `refbydoctor_master`b WHERE a.`patient_id`='$uhid' AND a.`opd_id`='$ipd' AND a.`refbydoctorid`=b.`refbydoctorid` "));
		$d_name=$doc['ref_name'];
	}
	if($show=="")
	{
	?>
	<span style="float:right;"><input type="button" class="btn btn-info" id="add" value="Back to list" onclick="window.location='processing.php?param=213'" style="" /></span>
	<?php
	}
	?>
	<table class="table table-condensed table-bordered" style="background: snow">
		<tr>
			<th>UHID</th>
			<th>IPD ID</th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Consultant</th>
		</tr>
		<tr>
			<td><?php echo $uhid;?></td>
			<td><?php echo $ipd;?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $pat['sex'];?></td>
			<td><?php echo $d_name;?></td>
		</tr>
	</table>
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="ipd" value="<?php echo $ipd;?>" style="display:none;" />
	<input type="text" id="show" value="<?php echo $show;?>" style="display:none;" />
	<input type="hidden" id="chk_val" value="0"/>
	<input type="hidden" id="chk_val1" value="0"/>
	<div id="res">
	
	
	</div>
	<div id="msgg" style="position:fixed;display:none;top:40%;left:45%;font-size:30px;color:#ee0000;"></div>
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
<link rel="stylesheet" href="../css/select2.min.css" />
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
		scheduling();
		//load_resourse_list();
		$(".datepicker").datepicker({dateFormat: 'yy-mm-dd',});
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 05,}});
				
		//$(".myselect2").select2({ theme: "classic" });
		//$("#bill_type").select2("focus");
	});
//------------------------------------------------------//
function add_res()
{
	var rr=$('#ot_tbl tbody tr.cc').length;
	var row=rr+1;
	
	var typ=$("#ot_type").val();
	var rs=$("#rs").val();
	
	var t_ch=0;
	var test_l=document.getElementsByClassName("cc");
	
	for(var i=0;i<test_l.length;i++)
	{
		//var tt=$("#tr"+i).find('td:eq(1) input:first').val()+"@"+$("#tr"+i).find('td:eq(2) input:first').val();
		var tt=$(".cc:eq("+i+")").find('td:eq(1) input:first').val()+"@"+$(".cc:eq("+i+")").find('td:eq(2) input:first').val();
		if(tt==typ+"@"+rs)
		{
			t_ch=1;
		}
	}
	//alert(rr);
	//alert(t_ch);
	if($("#ot_type").val()=="0")
	{
		$("#ot_type").focus();
	}
	else if($("#rs").val()=="0")
	{
		$("#rs").focus();
	}
	else if(t_ch==1)
	{
		$("#ot_tbl").css({'opacity':'0.5'});
		$("#msgg").text("Already Selected");
		//var x=$("#ot_tbl").offset();
		//var w=$("#msgg").width()/2;
		//$("#msgg").css({'top':'50%','left':'50%'});
		$("#msgg").fadeIn(500);
		setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#ot_tbl").css({'opacity':'1.0'});$("#ot_type").select2("focus");})},600);
	}
	else
	{
		$.post("pages/ot_scheduling_ajax.php",
		{
			typ:$("#ot_type").val(),
			emp:$("#rs").val(),
			type:"res_type",
		},
		function(data,status)
		{
			//alert(data);
			var vl=data.split("@");
			$("#end_tr").closest("tr").before('<tr class="cc cc'+$("#ot_type").val()+' cc'+$("#rs").val()+'" id="tr'+row+'"><td></td><td><input type="hidden" value="'+$("#ot_type").val()+'" />'+vl[0]+'</td><td><input type="hidden" value="'+$("#rs").val()+'" />'+vl[1]+'</td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
			var w=$("#sel_val").val()+","+$("#ot_type").val()+"%"+$("#rs").val();
			$("#sel_val").val(w);
			//$("#ot_type").val('0').trriger("change");
			//$("#rs").val('0').trriger("change");
			//$("#ot_type").focus();
			$("#amt").val('');
			$("#ot_type").select2("focus");
		})
	}
}
function add_res_old()
{
	var rr=$('#ot_tbl tbody tr.cc').length;
	var row=rr+1;
	if($("#ot_type").val()=="0")
	{
		$("#ot_type").focus();
	}
	else if($("#rs").val()=="0")
	{
		$("#rs").focus();
	}
	//else if(($('#ot_tbl tr.cc'+$("#ot_type").val()).attr('id'))==($('#ot_tbl tr.cc'+$("#rs").val()).attr('id')))
	else if($("#sel_val").val().indexOf(($("#ot_type").val()+"%"+$("#rs").val()))>0)
	//$('#ot_tbl tr.cc').attr('id');
	{
		$("#ot_tbl").css({'opacity':'0.5'});
		$("#msgg").text("Already Selected");
		//var x=$("#ot_tbl").offset();
		//var w=$("#msgg").width()/2;
		//$("#msgg").css({'top':'50%','left':'50%'});
		$("#msgg").fadeIn(500);
		setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#ot_tbl").css({'opacity':'1.0'});})},600);
	}
	else
	{
		$.post("pages/global_load_g.php",
		{
			typ:$("#ot_type").val(),
			emp:$("#rs").val(),
			type:"res_type",
		},
		function(data,status)
		{
			var vl=data.split("@");
			$("#end_tr").closest("tr").before('<tr class="cc cc'+$("#ot_type").val()+' cc'+$("#rs").val()+'" id="tr'+row+'"><td></td><td><input type="hidden" value="'+$("#ot_type").val()+'" />'+vl[0]+'</td><td><input type="hidden" value="'+$("#rs").val()+'" />'+vl[1]+'</td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
			var w=$("#sel_val").val()+","+$("#ot_type").val()+"%"+$("#rs").val();
			$("#sel_val").val(w);
			$("#ot_type").val('0');
			$("#rs").val('0');
		})
	}
}
function rem_str(dd)
{
	var w=$("#sel_val").val().trim();
	var p=w.split(",");
	var typ=$("#tr"+dd).find('td:eq(1) input:first').val();
	var emp=$("#tr"+dd).find('td:eq(2) input:first').val();
	var dl=typ+"%"+emp;
	var vl="";
	for(var j=1; j<p.length; j++)
	{
		//alert(p[j]);
		if(p[j]!=dl)
		{
			vl+=","+p[j];
		}
	}
	alert(vl);
	$("#sel_val").val(vl);
}
function ot_resource_list()
{
	$.post("pages/ot_scheduling_ajax.php",
	{
		ot_type:$("#ot_type").val(),
		type:"ot_resource_list",
	},
	function(data,status)
	{
		$("#resource_list").html(data);
		//$("html,body").animate({scrollTop: '600px'},1000);
		$("select").select2({ theme: "classic" });
		$("#rs").select2("focus");
	})
}

function clear_error(id)
{
	//$("#"+id).css("border","");
	$("#"+id).siblings(".select2-container").css({'border-color':'','box-shadow':''});
	if(id=="ot_dept")
	{
		$.post("pages/ot_scheduling_ajax.php",
		{
			dept:$("#ot_dept").val(),
			type:"load_procedure_list",
		},
		function(data,status)
		{
			//alert(data);
			$("#proc_list").html(data);
			load_grade_list();
			if($("#"+id).val()=="others")
			{
				$("#tr_new_dept").slideDown();
				$("#tr_sel_pr").hide();
				$("#proc_list_new").show();
				$("#new_dept").focus();
			}
			else
			{
				$("#tr_new_dept").hide();
				$("#proc_list_new").hide();
				$("#tr_sel_pr").show();
				$("select").select2({ theme: "classic" });
				$("#pr").select2("focus");
			}
		})
	}
	if(id=="pr")
	{
		//$("select").select2({ theme: "classic" });
		if($("#pr").val()=="others")
		{
			$("#proc_list_new").slideDown();
		}
		else
		{
			$("#proc_list_new").hide();
			$("#grade").select2("focus");
		}
	}
	if(id=="grade")
	{
		$.post("pages/ot_scheduling_ajax.php",
		{
			grade:$("#grade").val(),
			type:"load_cabin_list",
		},
		function(data,status)
		{
			//~ //alert(data);
			$("#cabin_list").html(data);
			//~ //$("html,body").animate({scrollTop: '600px'},1000);
			$("select").select2({ theme: "classic" });
			$("#cabin").select2("focus");
			load_resourse_list();
		})
	}
	if(id=="cabin")
	{
		load_resourse_list();
	}
	if(id=="anas")
	{
		if($("#anas").val()=="others")
		{
			$("#tr_anas_new").slideDown();
		}
		else
		{
			$("#tr_anas_new").hide();
			//$("#grade").select2("focus");
		}
	}
}

function load_grade_list()
{
	$.post("pages/ot_scheduling_ajax.php",
	{
		ot_dept:$("#ot_dept").val(),
		type:"load_grade_list",
	},
	function(data,status)
	{
		//alert(data);
		$("#grade_list").html(data);
		$("select").select2({ theme: "classic" });
		if($("#ot_dept").val()=="others")
		{
			$("#new_dept").focus();
		}
		else
		{
			$("#pr").select2("focus");
		}
	})
}

function load_resourse_list()
{
	$.post("pages/ot_scheduling_ajax.php",
	{
		grade:$("#grade").val(),
		cabin:$("#cabin").val(),
		type:"load_resourse_list",
	},
	function(data,status)
	{
		//alert(data);
		$("#ot_type_list").html(data);
		//$("html,body").animate({scrollTop: '600px'},1000);
		$("select").select2({ theme: "classic" });
		//$("#cabin").select2("focus");
	})
}

function upd_shed()
{
	if($("#ot_typ").val()=="0")
	{
		$("#ot_typ").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#ot_typ").select2("focus");
	}
	else if($("#ot_dept").val()=="0")
	{
		$("#ot_dept").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#ot_dept").select2("focus");
	}
	else if($("#ot_dept").val()=="others" && $("#new_dept").val().trim()=="")
	{
		$("#new_dept").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#new_dept").focus();
	}
	else if($("#ot_dept").val()=="others" && $("#pr_name").val().trim()=="")
	{
		$("#pr_name").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#pr_name").focus();
	}
	else if($("#ot_dept").val()!="others" && $("#pr").val()=="0")
	{
		$("#pr").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#pr").select2("focus");
	}
	else if($("#pr").val()!="others" && $("#pr").val()=="0")
	{
		$("#pr").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#pr").select2("focus");
	}
	else if($("#pr").val()=="others" && $("#pr_name").val().trim()=="")
	{
		$("#pr_name").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#pr_name").focus();
	}
	else if($("#grade").val()=="0")
	{
		$("#grade").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#grade").select2("focus");
	}
	else if($("#cabin").val()=="0")
	{
		$("#cabin").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#cabin").select2("focus");
	}
	else if($("#ot_date").val()=="")
	{
		$("#ot_date").css("border","1px solid #ff0000");
		$("#ot_date").focus();
	}
	else if($("#doc").val()=="0")
	{
		$("#doc").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#doc").select2("focus");
	}
	else if(($('#ot_tbl tbody tr.cc').length)==0)
	{
		$("#ot_tbl").css({'opacity':'0.3'});
		$("#msgg").text("Select OT Resources");
		var x=$("#ot_tbl").offset();
		var w=$("#msgg").width()/2;
		//$("#msgg").css({'top':'50%','left':'50%'});
		$("#msgg").fadeIn(500);
		setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#ot_tbl").css({'opacity':'1.0'});$("#ot_type").select2("focus");})},600);
	}
	else
	{
		$("#sav_shed").attr("disabled",true);
		var det="";
		var rr=$('#ot_tbl tbody tr.cc');
		for(var i=0; i<(rr.length); i++)
		{
			det+=$(".cc:eq("+i+")").find('td:eq(1) input:first').val().trim()+"@"+$(".cc:eq("+i+")").find('td:eq(2) input:first').val().trim()+"@#@#";
		}
		//alert(det);
		
		$.post("pages/ot_scheduling_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			show:$("#show").val(),
			ot:$("#ot").val(),
			ot_typ:$("#ot_typ").val(),
			ot_dept:$("#ot_dept").val(),
			new_dept:$("#new_dept").val().trim(),
			grade:$("#grade").val(),
			anas:$("#anas").val(),
			new_anas:$("#new_anas").val(),
			cabin:$("#cabin").val(),
			pr:$("#pr").val(),
			pr_name:$("#pr_name").val().trim(),
			ot_date:$("#ot_date").val(),
			doc:$("#doc").val(),
			st_time:$("#st_time").val(),
			en_time:$("#en_time").val(),
			diag:$("#diag").val().trim(),
			rem:$("#rem").val().trim(),
			usr:$("#user").text().trim(),
			det:det,
			type:"ot_upd_shed",
		},
		function(data,status)
		{
			//alert(data);
			var vl=data.split("@@@");
			bootbox.dialog({ message: vl[1]});
			setTimeout(function()
			{
				bootbox.hideAll();
				scheduling();
				//window.location="processing.php?param=208&uhid="+$('#uhid').val().trim()+"&ipd="+$('#ipd').val().trim();
				window.location="processing.php?param=210&uhid="+$('#uhid').val().trim()+"&ipd="+$('#ipd').val().trim()+"&show="+vl[0]+"&adv=1";
			}, 1000);
		})
	}
}
function save_shed()
{
	/*if($("#ot").val()=="0")
	{
		//$("#ot").css("border","1px solid #ff0000");
		//$("#ot").focus();
		$("#ot").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#ot").select2("focus");
	}
	else*/
	if($("#ot_typ").val()=="0")
	{
		//$("#ot_typ").css("border","1px solid #ff0000");
		//$("#ot_typ").focus();
		$("#ot_typ").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#ot_typ").select2("focus");
	}
	else if($("#ot_dept").val()=="0")
	{
		//$("#ot_dept").css("border","1px solid #ff0000");
		//$("#ot_dept").focus();
		$("#ot_dept").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#ot_dept").select2("focus");
	}
	else if($("#ot_dept").val()=="others" && $("#new_dept").val().trim()=="")
	{
		$("#new_dept").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#new_dept").focus();
	}
	else if($("#ot_dept").val()=="others" && $("#pr_name").val().trim()=="")
	{
		$("#pr_name").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#pr_name").focus();
	}
	else if($("#ot_dept").val()!="others" && $("#pr").val()=="0")
	{
		$("#pr").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#pr").select2("focus");
	}
	else if($("#pr").val()!="others" && $("#pr").val()=="0")
	{
		$("#pr").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#pr").select2("focus");
	}
	else if($("#pr").val()=="others" && $("#pr_name").val().trim()=="")
	{
		$("#pr_name").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#pr_name").focus();
	}
	else if($("#grade").val()=="0")
	{
		//$("#ot_dept").css("border","1px solid #ff0000");
		//$("#ot_dept").focus();
		$("#grade").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#grade").select2("focus");
	}
	else if($("#cabin").val()=="0")
	{
		//$("#ot_dept").css("border","1px solid #ff0000");
		//$("#ot_dept").focus();
		$("#cabin").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#cabin").select2("focus");
	}
	else if($("#ot_date").val()=="")
	{
		$("#ot_date").css("border","1px solid #ff0000");
		$("#ot_date").focus();
	}
	else if($("#doc").val()=="0")
	{
		//$("#ot_dept").css("border","1px solid #ff0000");
		//$("#ot_dept").focus();
		$("#doc").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		$("#doc").select2("focus");
	}
	/*
	else if($("#st_time").val()=="")
	{
		$("#st_time").css("border","1px solid #ff0000");
		$("#st_time").focus();
	}
	else if($("#en_time").val()=="")
	{
		$("#en_time").css("border","1px solid #ff0000");
		$("#en_time").focus();
	}*/
	else if(($('#ot_tbl tbody tr.cc').length)==0)
	{
		$("#ot_tbl").css({'opacity':'0.3'});
		$("#msgg").text("Select OT Resources");
		var x=$("#ot_tbl").offset();
		var w=$("#msgg").width()/2;
		//$("#msgg").css({'top':'50%','left':'50%'});
		$("#msgg").fadeIn(500);
		setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#ot_tbl").css({'opacity':'1.0'});$("#ot_type").select2("focus");})},600);
	}
	else
	{
		$("#sav_shed").attr("disabled",true);
		var det="";
		var rr=$('#ot_tbl tbody tr.cc');
		for(var i=0; i<(rr.length); i++)
		{
			det+=$(".cc:eq("+i+")").find('td:eq(1) input:first').val().trim()+"@"+$(".cc:eq("+i+")").find('td:eq(2) input:first').val().trim()+"@#@#";
		}
		//alert(det);
		
		$.post("pages/ot_scheduling_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			ot:$("#ot").val(),
			ot_typ:$("#ot_typ").val(),
			ot_dept:$("#ot_dept").val(),
			new_dept:$("#new_dept").val().trim(),
			grade:$("#grade").val(),
			anas:$("#anas").val(),
			new_anas:$("#new_anas").val(),
			cabin:$("#cabin").val(),
			pr:$("#pr").val(),
			pr_name:$("#pr_name").val().trim(),
			ot_date:$("#ot_date").val(),
			doc:$("#doc").val(),
			st_time:$("#st_time").val(),
			en_time:$("#en_time").val(),
			diag:$("#diag").val().trim(),
			rem:$("#rem").val().trim(),
			usr:$("#user").text().trim(),
			det:det,
			type:"ot_save_shed",
		},
		function(data,status)
		{
			//alert(data);
			var vl=data.split("@@@");
			bootbox.dialog({ message: vl[1]});
			setTimeout(function()
			{
				bootbox.hideAll();
				scheduling();
				//window.location="processing.php?param=208&uhid="+$('#uhid').val().trim()+"&ipd="+$('#ipd').val().trim();
				window.location="processing.php?param=210&uhid="+$('#uhid').val().trim()+"&ipd="+$('#ipd').val().trim()+"&show="+vl[0]+"&adv=1";
			}, 1000);
		})
	}
}
function save_shed_old()
{
	if($("#ot").val()=="0")
	{
		$("#ot").focus();
	}
	else if($("#st_time").val()=="")
	{
		$("#st_time").focus();
	}
	else if($("#en_time").val()=="")
	{
		$("#en_time").focus();
	}
	else if(($('#ot_tbl tbody tr.cc').length)==0)
	{
		$("#ot_tbl").css({'opacity':'0.5'});
		$("#msgg").text("Select OT Resources");
		var x=$("#ot_tbl").offset();
		var w=$("#msgg").width()/2;
		//$("#msgg").css({'top':'50%','left':'50%'});
		$("#msgg").fadeIn(500);
		setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#ot_tbl").css({'opacity':'1.0'});$("#ot_type").focus();})},600);
	}
	else
	{
		$("#sav_shed").attr("disabled",true);
		var det="";
		var rr=$('#ot_tbl tbody tr.cc').length;
		for(var i=0;i<=rr;i++)
		{
			det+=$("#tr"+i).find('td:eq(1) input:first').val()+"@"+$("#tr"+i).find('td:eq(2) input:first').val()+"@#@#";
		}
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			show:$("#show").val().trim(),
			ot:$("#ot").val(),
			pr:$("#pr").val(),
			ot_date:$("#ot_date").val(),
			doc:$("#doc").val(),
			st_time:$("#st_time").val(),
			en_time:$("#en_time").val(),
			rem:$("#rem").val(),
			usr:$("#user").text().trim(),
			det:det,
			type:"ot_save_shed",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				scheduling();
				window.location="processing.php?param=208&uhid="+$('#uhid').val().trim()+"&ipd="+$('#ipd').val().trim();
			}, 1000);
		})
	}
}
function scheduling()
{
	//$("#loader").show();
	$.post("pages/ot_scheduling_ajax.php",
	{
		uhid:$("#uhid").val().trim(),
		ipd:$("#ipd").val().trim(),
		show:$("#show").val().trim(),
		usr:$("#user").text().trim(),
		type:"ot_scheduling",
	},
	function(data,status)
	{
		//$("#loader").hide();
		$("#res").html(data);
		//$("html,body").animate({scrollTop: '200px'},800);
	})
}
</script>
