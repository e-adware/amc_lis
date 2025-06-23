<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Doctor IP Dashboard</span></div>
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
	?>
	<span style="float:right;"><input type="button" class="btn btn-info" id="add" value="Back to list" onclick="window.location='processing.php?param=15'" style="" /></span>
	<table class="table table-condensed table-bordered" style="background: snow">
		<tr>
			<th>UHID</th>
			<th>IPD</th>
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
	<input type="hidden" id="chk_val" value="0"/>
	<input type="hidden" id="chk_val1" value="0"/>
	<div class="span11" style="margin-left:0px;">
		<div class="accordion" id="collapse-group">
			<div class="accordion-group widget-box"><!--box 1-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse1" data-toggle="collapse" onclick="show_icon(1)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Chief complaints</b><i class="icon-arrow-down" id="ard1"></i><i class="icon-arrow-up" id="aru1" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign1" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign1" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse1" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl1" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box"><!--box 2-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse2" data-toggle="collapse" onclick="show_icon(2)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Past history</b><i class="icon-arrow-down" id="ard2"></i><i class="icon-arrow-up" id="aru2" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign2" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign2" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse2" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl2" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box"><!--box 3-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse3" data-toggle="collapse" onclick="show_icon(3)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Examination</b><i class="icon-arrow-down" id="ard3"></i><i class="icon-arrow-up" id="aru3" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign3" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign3" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse3" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl3" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box"><!--box 4-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse4" data-toggle="collapse" onclick="show_icon(4)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Diagnosis</b><i class="icon-arrow-down" id="ard4"></i><i class="icon-arrow-up" id="aru4" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign4" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign4" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse4" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl4" style="display:none;">
						
					</div>
				</div>
			</div>
			<!--<div class="accordion-group widget-box">
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse5" data-toggle="collapse" onclick="show_icon(5)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Medication Plan</b><i class="icon-arrow-down" id="ard5"></i><i class="icon-arrow-up" id="aru5" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign5" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign5" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse5" style="height:0px;max-height:400px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl5" style="display:none;">
						
					</div>
				</div>
			</div>-->
			<div class="accordion-group widget-box"> <!--box 6-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse6" data-toggle="collapse" onclick="show_icon(6)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Investigation</b><i class="icon-arrow-down" id="ard6"></i><i class="icon-arrow-up" id="aru6" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign6" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign6" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse6" style="height:0px;max-height:300px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl6" style="display:none;">
					
					</div>
				</div>
			</div>
			
			<div class="accordion-group widget-box"> <!--box 7-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse7" data-toggle="collapse" onclick="show_icon(7)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Consultation Notes</b><i class="icon-arrow-down" id="ard7"></i><i class="icon-arrow-up" id="aru7" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign7" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign7" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse7" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl7" style="display:none;">
						
					</div>
				</div>
			</div>
			<!--<div class="accordion-group widget-box">
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse8" data-toggle="collapse" onclick="show_icon(8)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Discharge Summary</b><i class="icon-arrow-down" id="ard8"></i><i class="icon-arrow-up" id="aru8" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign8" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign8" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse8" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl8" style="display:none;">
					
					</div>
				</div>
			</div>-->
			<?php
			if($pat['sex']=="Female")
			{/*
			?>
			<div class="accordion-group widget-box"> <!--box 16-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse16" data-toggle="collapse" onclick="show_icon(16)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Delivery Details</b><i class="icon-arrow-down" id="ard16"></i><i class="icon-arrow-up" id="aru16" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign16" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign16" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse16" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl16" style="display:none;">
					
					</div>
				</div>
			</div>
			<?php
			*/}
			?>
			<div class="accordion-group widget-box"> <!--box 9-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse9" data-toggle="collapse" onclick="show_icon(9)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">OT Scheduling</b><i class="icon-arrow-down" id="ard9"></i><i class="icon-arrow-up" id="aru9" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign9" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign9" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse9" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl9" style="display:none;">
					
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal modal-lg fade">
		  <div class="modal-body">
			<p id="add_opt">
				
			</p>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="save()" class="btn btn-primary" href="#">Save</a>
			<a data-dismiss="modal" onclick="clearr()" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	<!--modal end-->
	<!--modal-->
		<a href="#myAlert1" data-toggle="modal" id="dl1" class="btn" style="display:none;">A</a>
		<div id="myAlert1" class="modal fade">
		  <div class="modal-body">
			<p id="tests_lst">
				
			</p>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="save_test()" class="btn btn-primary" href="#">Save</a>
			<a data-dismiss="modal" onclick="clearr()" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	<!--modal end-->
	<!--modal-->
	<input type="button" data-toggle="modal" data-target="#note_mod" id="nt_btn" style="display:none"/>
	<input type="text" id="modtxt" value="0" style="display:none"/>
	<div class="modal fade" id="note_mod" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true"><b>x</b></button>
					<div id="res">
					
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--modal end-->
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
	<input type="button" data-toggle="modal" data-target="#myModal_med" id="med_mod" style="display:none"/>
	<input type="text" id="modtxt" value="0" style="display:none"/>
	<div class="modal fade" id="myModal_med" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="bootbox-close-button close" onclick="$('#med_list').css('height','100px')" data-dismiss="modal" aria-hidden="true"><b>x</b></button>
					<div id="med_list" style="">
					
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a data-dismiss="modal" id="ins_med" onclick="insert_medi();$('#med_list').css('height','100px')" style="display:none;" class="btn btn-primary" href="#">Save</a>
				<a data-dismiss="modal" onclick="$('#med_list').css('height','100px')" class="btn btn-info" href="#">Cancel</a>
			</div>
		</div>
	</div>
	<!--modal end-->
	<!--modal-->
	<input type="button" data-toggle="modal" data-target="#myModal_post" id="med_post" style="display:none"/>
	<input type="text" id="modtxt" value="0" style="display:none"/>
	<div class="modal fade" id="myModal_post" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="bootbox-close-button close" onclick="$('#med_list_post').css('height','100px')" data-dismiss="modal" aria-hidden="true"><b>x</b></button>
					<div id="med_list_post" style="">
					
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a data-dismiss="modal" id="ins_med_post" onclick="insert_medi_post();$('#med_list_post').css('height','100px')" style="display:none;" class="btn btn-primary" href="#">Save</a>
				<a data-dismiss="modal" onclick="$('#med_list_post').css('height','100px')" class="btn btn-info" href="#">Cancel</a>
			</div>
		</div>
	</div>
	<!--modal end-->
	<!--modal-->
		<a href="#medplan" data-toggle="modal" id="med_upd" class="btn" style="display:none;">A</a>
		<div id="medplan" class="modal modal-lg fade">
		  <div class="modal-body">
			<div id="upd_med_plan_det">
				
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
		$(document).mouseup(function(e) 
		{
			var container = $("#gter");
			// if the target of the click isn't the container nor a descendant of the container
			if(!container.is(e.target) && container.has(e.target).length===0)
			{
				container.hide();
				$('.a').removeClass('clk');
			}
		});
		auto_note();
	});
//------------------------------------------------------//
function calc_totday()
{
	var tot=0;
	var freq=$("#freq").val();
	var unit=$("#unit_day").val();
	var dur=parseInt($("#dur").val());
	var dos=parseInt($("#dos").val());
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
function meditab(id,e)
{
	if(e.keyCode==13)
	{
		if(id=="dos" && $("#"+id).val()!="0")
		$("#freq").focus();
		if(id=="freq" && $("#"+id).val()!="0")
		$("#st_date").focus();
		if(id=="st_date" && $("#"+id).val()!="")
		$("#dur").focus();
		if(id=="dur" && $("#"+id).val()!="0")
		$("#dur").focus();
		if(id=="dur" && $("#"+id).val()!="0")
		$("#unit_day").focus();
		if(id=="unit_day" && $("#"+id).val()!="0")
		$("#inst").focus();
		if(id=="inst")
		$("#add_medi").focus();
		if(id=="con_doc" && $("#"+id).val()!="0")
		$("#add_medi").focus();
		if(id=="qnt" && $("#"+id).val()!="" && (parseInt($("#"+id).val()))>0)
		$("#indsv").focus();
	}
}
function tab(id,e)
{
	if(e.keyCode==13)
	{
		if(id=="weight")
		$("#height").focus();
		if(id=="height")
		$("#mid_cum").focus();
		if(id=="mid_cum")
		$("#hd_cum").focus();
		if(id=="hd_cum")
		$("#spo").focus();
		if(id=="spo")
		$("#pulse").focus();
		if(id=="pulse")
		$("#temp").focus();
		if(id=="temp")
		$("#pr").focus();
		if(id=="pr")
		$("#rr").focus();
		if(id=="rr")
		$("#systolic").focus();
		if(id=="systolic")
		$("#diastolic").focus();
		if(id=="diastolic")
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
//-------------------------------------------------------------
function delivery_det()
{
	$.post("pages/global_load_g.php",
	{
		uhid:$("#uhid").val(),
		ipd:$("#ipd").val(),
		type:"pat_ipd_delivery_num",
	},
	function(data,status)
	{
		$("#cl16").html(data);
		$("html,body").animate({scrollTop: '550px'},1000);
	})
}
function show_deli_num()
{
	$("#show_deli_num").slideDown();
	$("#no").val('0');
	$("#deli_add_btn").attr("disabled",true);
	$("#no").focus();
}
function rem_deli_det()
{
	$("#show_deli_num").slideUp();
	$("#deli_add_btn").attr("disabled",false);
}
function add_deli_det()
{
	if($("#no").val()=="0")
	{
		$("#no").focus();
	}
	else
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			no:$("#no").val(),
			type:"pat_ipd_delivery_det",
		},
		function(data,status)
		{
			$("#show_deli_num").slideUp();
			$("#add_deli_det").html(data);
			$("#add_deli_det").slideDown();
			$("html,body").animate({scrollTop: '700px'},800);
			//alert(data);
		})
	}
}
function pat_ipd_delivery_save()
{
	var no=$("#val").val();
	var all="";
	for(var i=1;i<=no;i++)
	{
		if($("#dob"+i).val()=="")
		{
			$("#dob"+i).focus();
			return true;
		}
		if($("#time"+i).val()=="")
		{
			$("#time"+i).focus();
			return true;
		}
		if($("#sex"+i).val()=="0")
		{
			$("#sex"+i).focus();
			return true;
		}
		if($("#wt"+i).val()=="")
		{
			$("#wt"+i).focus();
			return true;
		}
		/*if($("#bed"+i).val()=="")
		{
			//$("#b"+i).focus();
			$("#b"+i).css("box-shadow","0px 0px 10px 3px #ff0000");
			return true;
		}*/
		all+=$("#dob"+i).val()+"@"+$("#sex"+i).val()+"@"+$("#time"+i).val()+"@"+$("#wt"+i).val()+"@"+$("#blood"+i).val()+"@"+$("#ward"+i).val()+"@"+$("#bed"+i).val()+"@##";
	}
	$.post("pages/global_insert_data_g.php",
	{
		uhid:$("#uhid").val(),
		ipd:$("#ipd").val(),
		all:all,
		usr:$('#user').text().trim(),
		type:"pat_ipd_delivery_save",
	},
	function(data,status)
	{
		bootbox.dialog({ message: data});
		setTimeout(function()
		{
			bootbox.hideAll();
			delivery_det();
		}, 1000);
	})
}
function clear_all_deli()
{
	$("#add_deli_det").slideUp();
	$("#add_deli_det").html('');
	$("#deli_add_btn").attr("disabled",false);
}
function view_baby_bed(uhid,sn)
{
	$("#res").empty();
	$("#b"+sn).css("box-shadow","");
	$.post("pages/global_load_g.php",
	{
		uhid:uhid,
		sn:sn,
		type:"view_baby_bed",
	},
	function(data,status)
	{
		$("#nt_btn").click();
		$("#res").html(data);
		chk_bed_assign(sn);
	})
}
function baby_bed_assign(id,wr,rid,bid,bno,sn)
{
	var bd=wr+"@"+rid+"@"+bid+"@"+bno+"@"+sn+"@#";
	$("#ward"+sn).val(wr);
	$("#bed"+sn).val(bid);
	$("#b"+sn).text(bno);
	$.post("pages/global_insert_data_g.php",
	{
		uhid:$("#uhid").val(),
		ward:wr,
		room:rid,
		bed:bid,
		bno:bno,
		sn:$("#hid").val(),
		type:"baby_bed_assign",
	},
	function(data,status)
	{
		chk_bed_assign($("#hid").val());
	})
}
function chk_bed_assign(sn)
{
	setInterval(function()
	{
		if($('#cl16').css('display')=="block")
		{
			$.post("pages/global_load_g.php",
			{
				uhid:$("#uhid").val(),
				sn:$("#hid").val(),
				type:"view_baby_bed",
			},
			function(data,status)
			{
				$("#res").html(data);
			})
		}
	},1500);
}
//---------------------------------------------------------------
function ad_med_emer()
{
	$.post("pages/global_load_g.php",
	{
		type:"pat_ipd_ad_med_emer",
	},
	function(data,status)
	{
		$("#med_upd").click();
		$("#upd_med_plan_det").html(data);
	})
}
function ad_med_emer_set()
{
	$.post("pages/global_insert_data_g.php",
	{
		uhid:$("#uhid").val(),
		ipd:$("#ipd").val(),
		medi:$("#drug").val(),
		freq:$("#freq").val(),
		dur:$("#dur").val(),
		unit_day:$("#unit_day").val(),
		inst:$("#inst").val(),
		dose:$("#dose").val(),
		usr:$('#user').text().trim(),
		type:"pat_ipd_med_emer_set"
	},
	function(data,status)
	{
		med_admin();
	})
}
function change_med(id)
{
	//alert(sl);
	$.post("pages/global_load_g.php",
	{
		id:id,
		type:"pat_ipd_med_plan_upd",
	},
	function(data,status)
	{
		$("#med_upd").click();
		$("#upd_med_plan_det").html(data);
	})
}
function update_plan(id)
{
	$.post("pages/global_insert_data_g.php",
	{
		medi:$("#drug").val(),
		freq:$("#freq").val(),
		st_date:$("#st_date_upd").val(),
		dur:$("#dur").val(),
		unit_day:$("#unit_day").val(),
		inst:$("#inst").val(),
		dose:$("#dose").val(),
		con_doc:$("#con_doc").val(),
		id:id,
		usr:$('#user').text().trim(),
		type:"pat_ipd_med_plan_update"
	},
	function(data,status)
	{
		medication();
		/*bootbox.dialog({ message: data});
		setTimeout(function()
		{
			bootbox.hideAll();
		}, 1000);*/
	})
}
function rep_pop(uhid,ipd,batch,testid,category_id)
{
	if(category_id==1)
	{
		$.post("pages/nurs_report_patho.php",
		{
			uhid:uhid,
			ipd:ipd,
			batch:batch,
			testid:testid,
		},function(data,status)
		{
			$("#results").html(data);
			$("#mod").click();
		});
	}
	if(category_id==2)
	{
		$.post("pages/nurs_report_rad.php",
		{
			uhid:uhid,
			ipd:ipd,
			batch:batch,
			testid:testid,
		},function(data,status)
		{
			$("#results").html(data);
			$("#mod").click();
		});
	}
	if(category_id==3)
	{
		$.post("pages/nurs_report_card.php",
		{
			uhid:uhid,
			ipd:ipd,
			batch:batch,
			testid:testid,
		},function(data,status)
		{
			$("#results").html(data);
			$("#mod").click();
		});
	}
}
function test_enable()
{
	setTimeout(function(){ $("#chk_val").val(1)},500);
}
var t_val=1;
var t_val_scroll=0;
function select_test_new(val,e)
{
	var z="";
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		var tst=document.getElementsByClassName("test"+t_val);
		load_test_new(''+tst[1].value+'',''+tst[2].innerHTML+'');
		//$("#list_all_test").slideDown(400);
		$("#test").val("").focus();
	}
	else if(unicode==40)
	{
		var chk=t_val+1;
		var cc=document.getElementById("td"+chk).innerHTML;
		if(cc)
		{
			t_val=t_val+1;
			$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
			var t_val1=t_val-1;
			$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
			var z2=t_val%1;
			if(z2==0)
			{
				$("#test_d").scrollTop(t_val_scroll)
				t_val_scroll=t_val_scroll+30;
			}
		}	
	}
	else if(unicode==38)
	{
		var chk=t_val-1;
		var cc=document.getElementById("td"+chk).innerHTML;
		if(cc)
		{
			t_val=t_val-1;
			$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
			var t_val1=t_val+1;
			$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
			var z2=t_val%1;
			if(z2==0)
			{
				t_val_scroll=t_val_scroll-30;
				$("#test_d").scrollTop(t_val_scroll)
				
			}
		}	
	}
	else if(unicode==27)
	{
		$("#test").val("");
		$("#test_d").html("");
		//$("#list_all_test").slideUp(300);
		
		$("html, body").animate({ scrollTop: 500 })
		$("#dis_per").focus();
	}
	else
	{
		$.post("pages/load_test_ajax_nurse.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			batch:$("#batch").val(),
			test:val,
		},
		function(data,status)
		{
			$("#test_d").html(data);
			t_val=1;
			t_val_scroll=0;
			$("#test_d").scrollTop(t_val_scroll)
		})
	}
}
function load_test_new(id,name)
{
	//$(".up_div").fadeIn(1);
	//$(".up_div").fadeOut(1);
	var test_chk= $('#test_list tr').length;
	if(test_chk==0)
	{
		var test_add="<table class='table table-condensed table-bordered' style='style:none' id='test_list'>";
		test_add+="<tr><th style='background-color:#cccccc'>Sl No</th><th style='background-color:#cccccc'>Tests</th><th style='background-color:#cccccc'>Remove</th></tr>";
		test_add+="<tr><td>1</td><td width='80%'>"+name+"<input type='hidden' value='"+id+"' class='test_id'/></td><td onclick='delete_rows(this,2)'><span class='text-danger' style='cursor:pointer'><i class='icon-remove'></i></span></td></tr>";
		test_add+="</table>";
		//test_add+="<div style='display:none;text-align:center;background-color:#cccccc;font-weight:bold;font-size:15px;padding:5px' id='test_hidden_price'>Total:<span id='test_total_hidden'></span></div>";
		
		$("#ss_tests").html(test_add);
		test_chk++;
	
		var tot=0;
		var tot_ts=document.getElementsByClassName("test_f");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseInt(tot_ts[j].innerHTML);
		}
		$("#test_total").text(tot);
		$("#test").val("");
	}
	else
	{
		
		var t_ch=0;
		var test_l=document.getElementsByClassName("test_id");
		
		for(var i=0;i<test_l.length;i++)
		{
				if(test_l[i].value==id)
				{
					t_ch=1;
				}
		}
		if(t_ch)
		{

			$("#test_sel").css({'opacity':'0.5'});
			$("#msgg").text("Already Selected");
			var x=$("#test_sel").offset();
			var w=$("#msgg").width()/2;
			//$("#msgg").css({'top':'50%','left':'50%'});
			$("#msgg").fadeIn(500);
			setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#test_sel").css({'opacity':'1.0'});
			})},600);
			
		}			
		else
		{
	
		var tr=document.createElement("tr");
		var td=document.createElement("td");
		var td1=document.createElement("td");
		var td2=document.createElement("td");
		var td3=document.createElement("td");
		var tbody=document.createElement("tbody");
		
		td.innerHTML=test_chk;
		td1.innerHTML=name+"<input type='hidden' value='"+id+"' class='test_id'/>"
		//td2.innerHTML="<span class='test_f'>"+rate+"</span>";
		//td2.setAttribute("contentEditable","true");
		//td2.setAttribute("onkeyup","load_cost(2)");
		td2.innerHTML="<span class='text-danger' style='cursor:pointer'><i class='icon-remove'></i></span>";
		td2.setAttribute("onclick","delete_rows(this,2)");
		tr.appendChild(td);
		tr.appendChild(td1);
		tr.appendChild(td2);
		//tr.appendChild(td3);
		tbody.appendChild(tr);		
		document.getElementById("test_list").appendChild(tbody);
		var tot=0;
		var tot_ts=document.getElementsByClassName("test_f");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseInt(tot_ts[j].innerHTML);
		}
			$("#test_total").text(tot);
		}
		
		if(test_chk>4)
		{
			$("#list_all_test").css({'height':'220px','overflow':'scroll','overflow-x':'hidden'})			
			$("#list_all_test").animate({ scrollTop: 2900 });
			$("#test_hidden_price").fadeIn(200);
			$("#test_total_hidden").text($("#test_total").text());
		}
		$("#test").val("");
	}
	
	//add_vaccu();
}
function delete_rows(tab,num)
{
	$(tab).parent().remove();
	$("#test").focus();
}
//------------------------------------------------------//
	function save_test()
	{
		var tst=$("input.test_id").map(function()
		{
			return this.value;
		}).get().join(",");
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			batch:$("#batch").val(),
			tst:tst,
			usr:$("#user").text().trim(),
			type:"save_ipd_pat_test",
		},
		function(data,status)
		{
			/*bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				investigation();
			}, 1000);*/
			investigation($("#batch").val());
		})
	}
	function show_icon(i)
	{
		$(".hidden_div").fadeOut();
		$("#gter").fadeOut();
		$(".iconp").show();
		$(".iconm").hide();
		$(".icon-arrow-down").show();
		$(".icon-arrow-up").hide();
		if($('#cl'+i+':visible').length)
		{
			$("#cl"+i).fadeOut();
			$("#plus_sign"+i).show();
			$("#minus_sign"+i).hide();
			$("#ard"+i).show();
			$("#aru"+i).hide();
		}
		else
		{
			$("#cl"+i).fadeIn();
			$("#plus_sign"+i).hide();
			$("#minus_sign"+i).show();
			$("#ard"+i).hide();
			$("#aru"+i).show();
			if(i==1)
			{
				//diagnosis();
				chief_complain();
			}
			else if(i==2)
			{
				past_history();
				//medication();
				//$("html,body").animate({scrollTop: '200px'},800);
			}
			else if(i==3)
			{
				examination();
				//setTimeout(function(){ $("#test").focus()},500);
				$("html,body").animate({scrollTop: '370px'},800);
			}
			else if(i==4)
			{
				//vital();
				diagnosis();
				$("html,body").animate({scrollTop: '400px'},900);
			}
			else if(i==5)
			{
				ip_consult();
				$("html,body").animate({scrollTop: '460px'},800);
			}
			else if(i==6)
			{
				investigation();
				//room_status();
				$("html,body").animate({scrollTop: '470px'},800);
			}
			else if(i==7)
			{
				ip_consult();
				//equipment();
				$("html,body").animate({scrollTop: '480px'},800);
			}
			else if(i==8)
			{
				gen_consumable();
				$("html,body").animate({scrollTop: '500px'},800);
			}
			else if(i==9)
			{
				med_admin();
				$("html,body").animate({scrollTop: '350px'},800);
			}
			else if(i==10)
			{
				chief_complain();
				$("html,body").animate({scrollTop: '150px'},800);
			}
			else if(i==11)
			{
				past_history();
				$("html,body").animate({scrollTop: '180px'},800);
			}
			else if(i==12)
			{
				examination();
				$("html,body").animate({scrollTop: '250px'},800);
			}
			else if(i==13)
			{
				discharge_summ();
				$("html,body").animate({scrollTop: '700px'},1000);
			}
			else if(i==14)
			{
				medicine_indent();
				$("html,body").animate({scrollTop: '520px'},1000);
			}
			else if(i==15)
			{
				sur_consumable();
				$("html,body").animate({scrollTop: '500px'},1000);
			}
			else if(i==16)
			{
				delivery_det();
				$("html,body").animate({scrollTop: '550px'},1000);
			}
		}
	}
	function sur_consumable()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"pat_ipd_sur_consumable",
		},
		function(data,status)
		{
			$("#cl15").html(data);
			$("html,body").animate({scrollTop: '500px'},800);
		})
	}
	function add_sur_consume()
	{
		$("#add_sur_consume").fadeIn(1000);
		$("#add_con").attr("disabled",true);
		$("html,body").animate({scrollTop: '500px'},800);
	}
	function close_sur_consumable()
	{
		$("#consume").val('0');
		$("#consume_qnt").val('');
		$("#add_sur_consume").hide(500);
		$("#add_con").attr("disabled",false);
	}
	function save_sur_consumable()
	{
		if($("#consume1").val()=="0")
		{
			$("#consume1").focus();
		}
		else if($("#consume_qnt1").val()=="")
		{
			$("#consume_qnt1").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				consume:$("#consume1").val(),
				consume_qnt:$("#consume_qnt1").val(),
				usr:$("#user").text().trim(),
				type:"ipd_pat_save_sur_consumable",
			},
			function(data,status)
			{
				sur_consumable();
			})
		}
	}
	function medicine_indent()
	{
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"ipd_pat_medicine_indent",
		},
		function(data,status)
		{
			$("#cl5").html(data);
			$("html,body").animate({scrollTop: '520px'},1000);
		})
	}
	function chief_complain()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:"ipd_pat_chief_complain",
		},
		function(data,status)
		{
			$("#cl1").html(data);
		})
	}
	function past_history()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			type:"ipd_pat_past_history",
		},
		function(data,status)
		{
			$("#cl2").html(data);
		})
	}
	function update_hist()
	{
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			p_hist:$("#p_hist").val().trim(),
			usr:$("#user").text().trim(),
			type:"ipd_pat_update_hist",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
			past_history();
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
			$("#cl3").html(data);
			$("html,body").animate({scrollTop: '250px'},800);
		})
	}
	function save_exam()
	{
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			exam:$("#exam").val().trim(),
			usr:$("#user").text().trim(),
			type:"ipd_pat_examination",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
			examination();
		})
	}
	function discharge_summ()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:"ipd_pat_discharge_summ",
		},
		function(data,status)
		{
			$("#cl13").html(data);
			$("html,body").animate({scrollTop: '700px'},1000);
			setTimeout(function(){$("#course").focus()}, 500);
		})
	}
	function insert_disc_summ()
	{
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			course:$("#course").val().trim(),
			final_diag:$("#final_diag").val().trim(),
			foll:$("#foll").val().trim(),
			usr:$("#user").text().trim(),
			type:"ipd_pat_save_disc_summary",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
			discharge_summ();
		})
	}
	function post_drugs()
	{
		$("#med_post").click();
		$('#med_list_post').css('height','100px');
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:"ipd_add_medicine_post",
		},
		function(data,status)
		{
			$("#med_list_post").html(data);
		})
	}
	function print_disc_summary()
	{
		var uhid=btoa($("#uhid").val());
		var ipd=btoa($("#ipd").val());
		var usr=btoa($("#user").text().trim());
		url="pages/ipd_discharge_summary.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function add_row(r)
	{
		var rr=$('#hist_table tbody tr.cc').length;
		var i=1;
		var d="";
		for(i=1;i<=30;i++)
		{
			d+="<option value='"+i+"'>"+i+"</option>";
		}
		var s='<option value="Minutes">Minutes</option><option value="Hours">Hours</option><option value="Days">Days</option><option value="Week">Week</option><option value="Month">Month</option><option value="Year">Year</option>';
		$("#hh").closest("tr").before('<tr class="cc" id="tr'+rr+'"><th>Chief Complaints</th><td><input type="text" id="chief'+rr+'" class="" onkeyup="sel_chief('+rr+',event)" /></td><td><b>for</b> <select id="cc'+rr+'" class="span2" onkeyup="sel_chief('+rr+',event)"><option value="0">--Select--</option>'+d+'</select> <select id="tim'+rr+'" class="span2" onkeyup="sel_chief('+rr+',event)"><option value="0">--Select--</option>'+s+'</select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
		$("#chief"+rr).focus();
	}
	function insert_complain()
	{
		var i=0;
		var det="";
		//var rr=document.getElementById("hist_table tbody tr.cc").rows.length;
		var rr=$('#hist_table tbody tr.cc').length;
		for(i=0;i<rr;i++)
		{
			det+=$("#tr"+i).find("input").val()+"@"+$("#tr"+i).find("select:first").val()+"@"+$("#tr"+i).find("select:last").val()+"#govin#";
		}
		//alert(det);
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			det:det,
			usr:$("#user").text().trim(),
			type:"ipd_pat_insert_complain",
		},
		function(data,status)
		{
			chief_complain();
		})
	}
	function show_tests(vl)
	{
		$("#selc_test").show();
		$.post("pages/global_load_g.php",
		{
			val:vl,
			type:"show_sel_tests_ipd",
		},
		function(data,status)
		{
			$("#selc_test").html(data);
		})
	}
	function ad_tests(batch)
	{
		$("#dl1").click();
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			batch:batch,
			type:"show_sel_tests_ipd",
		},
		function(data,status)
		{
			$("#tests_lst").html(data);
			setTimeout(function(){ $("#test").focus()},500);
		})
	}
	function equipment()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"pat_ipd_equipment",
		},
		function(data,status)
		{
			$("#cl7").html(data);
			$("html,body").animate({scrollTop: '480px'},800);
		})
	}
	function add_equip()
	{
		$("#add_equip").fadeIn(1000);
		$("#add_eq").attr("disabled",true);
		$("html,body").animate({scrollTop: '480px'},800);
	}
	function close_equip()
	{
		$("#equip").val('0');
		$("#hour").val('0');
		$("#add_equip").hide(500);
		$("#add_eq").attr("disabled",false);
	}
	function save_equip()
	{
		if($("#equip").val()=="0")
		{
			$("#equip").focus();
		}
		else if($("#hour").val()=="0")
		{
			$("#hour").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				equip:$("#equip").val(),
				hour:$("#hour").val(),
				usr:$("#user").text().trim(),
				type:"ipd_pat_save_equip",
			},
			function(data,status)
			{
				equipment();
			})
		}
	}
	function gen_consumable()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"pat_ipd_consumable",
		},
		function(data,status)
		{
			$("#cl8").html(data);
			$("html,body").animate({scrollTop: '500px'},800);
		})
	}
	function add_consume()
	{
		$("#add_consume").fadeIn(1000);
		$("#add_con").attr("disabled",true);
		$("html,body").animate({scrollTop: '500px'},800);
	}
	function close_consumable()
	{
		$("#consume").val('0');
		$("#consume_qnt").val('');
		$("#add_consume").hide(500);
		$("#add_con").attr("disabled",false);
	}
	function save_consumable()
	{
		if($("#consume").val()=="0")
		{
			$("#consume").focus();
		}
		else if($("#consume_qnt").val()=="")
		{
			$("#consume_qnt").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				consume:$("#consume").val(),
				consume_qnt:$("#consume_qnt").val(),
				usr:$("#user").text().trim(),
				type:"ipd_pat_save_consumable",
			},
			function(data,status)
			{
				gen_consumable();
			})
		}
	}
	function room_status()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"pat_ipd_room_status",
		},
		function(data,status)
		{
			$("#cl6").html(data);
			$("html,body").animate({scrollTop: '470px'},800);
		})
	}
	function med_admin()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			view:"1",
			usr:$("#user").text().trim(),
			type:"pat_ipd_med_admin",
		},
		function(data,status)
		{
			$("#cl9").html(data);
		})
	}
	function view_medi(vl)
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			view:vl,
			usr:$("#user").text().trim(),
			type:"pat_ipd_med_admin",
		},
		function(data,status)
		{
			$("#cl9").html(data);
		})
	}
	function diagnosis()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"pat_ipd_det",
		},
		function(data,status)
		{
			$("#cl4").html(data);
			/*bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);*/
		})
	}
	function view_batch(batch)
	{
		$(".bt").removeClass('btt');
		$("#ad"+batch).addClass('btt');
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd_id:$("#ipd").val(),
			batch_no:batch,
			user:$("#user").text().trim(),
			lavel:$("#lavel_id").val(),
			type:"ipd_batch_details",
		},
		function(data,status)
		{
			$("#batch_details").html(data);
			$("#foll_details").html('');
		})
	}
	function hid_div(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==27)
		{
			//$("#mod").click();
			$('.modal').modal('hide');
		}
	}
	function view_all(val)
	{
		$("#loader").show();
		$.post("pages/page.php",// phlebo_load_pat
		{
			type:val,
			pat_type:$("#pat_type").val(),
			from:$("#from").val(),
			to:$("#to").val(),
			pat_name:$("#pat_name").val(),
			catagory:$("#catagory").val(),
			var_id:$("#var_id").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function load_sample(uhid,opd,ipd)
	{
		$.post("pages/page.php",//phlebo_load_sample
		{
			uhid:uhid,
			opd:opd,
			ipd:ipd,
			lavel:$("#lavel_id").val()
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#mod").click();
			$("#results").fadeIn(500,function(){ load_vaccu(); })
		})
	}
	
	function select_sample(id)
	 {
		if($("#"+id).is(":checked"))
		{
			$("."+id).click();
		}
		else
		{
			if($("#val_"+id).val()=="0")
			{
				$("."+id).attr('checked',false);
			}
			else
			{
				$("#"+id).attr('checked',true);
				alert("Sample already accepted by Lab. Action cannot be completed");
			}
		}
		
		load_vaccu();
	 }
	 
	 function load_vaccu()
	 {
		var tst="";
		var samp=$(".samp:checked")
		for(var i=0;i<samp.length;i++)
		{
			var test=$("."+$(samp[i]).attr("id"));
			for(var j=0;j<test.length;j++)
			{
				tst=tst+"@"+$(test[j]).val();
			}
		}
		
		$(".vac").attr("checked",false);
		$.post("pages/phlebo_load_vaccu.php",// phlebo_load_vaccu
		{
			tst:tst
		},
		function(data,status)
		{
			var vc=data.split("@");
			for(var k=0;k<vc.length;k++)
			{
				if(vc[k])
				{
					$("#vac_"+vc[k]+"").click();
				}
			}
		})
	 }
	function note(a,batch)
	{
		$.post("pages/global_load_g.php", // page_name
		{
			test_id:a,
			batch:batch,
			uhid:$('#uhid').val(),
			ipd:$('#ipd').val(),
			usr:$('#user').text().trim(),
			type:"update_note_ipd",
		},
		function(data,status)
		{
			bootbox.dialog({
			  message: "Note:<input type='text' value='"+data+"' id='note' />",
			  title: "Note",
			  buttons: {
				main: {
				  label: "Save",
				  className: "btn-primary",
				  callback: function()
				  {
					if($('#note').val()!='')
					{
						$.post("pages/global_insert_data_g.php",
						{
							test_id:a,
							uhid:$('#uhid').val(),
							ipd:$('#ipd').val(),
							note:$('#note').val(),
							batch:batch,
							usr:$('#user').text().trim(),
							type:"ipd_pat_notes"
						},
						function(data,status)
						{
							bootbox.dialog({ message: data});
							setTimeout(function()
							{
								bootbox.hideAll();
							}, 1000);
						})
					}
					else
					{
						alert("Note cannot blank");
					}
				  }
				}
			  }
			});
			$("#note").focus();
			setTimeout(function(){ $("#note").focus()},500);
		})
	}
	function sample_accept(pid,ipd,batch_no)
	{
		var all="";
		var samp=$(".samp");
		for(var i=0;i<samp.length;i++)
		{
			if($(samp[i]).is(":checked"))
			{
				all=all+"#"+samp[i].id+"$";
				var tst=$("."+samp[i].id);
				
				for(var j=0;j<tst.length;j++)
				{
					if(tst[j].checked)
					{		
						all=all+"@"+tst[j].value;
					}
					
				}
			}		
		}
		$.post("pages/phlebo_save_sample.php",
		{
			pid:pid,
			ipd_id:ipd,
			batch_no:batch_no,
			all:all,
			user:$("#user").text()
		},
		function(data,status)
		{
			bootbox.dialog({ message: "Saved"});
			setTimeout(function(){
				bootbox.hideAll();
				var stype=$("#search_type").val();			
				if(stype=="date")
				{
					view_all('date');
				}else if(stype=="name")
				{
					view_all('name');
				}else if(stype=="ids")
				{
					view_all('ids');
				}
				$("#mod").click();
			},1000);
		})
	}
	function medication(batch)
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"pat_ipd_med_det",
		},
		function(data,status)
		{
			$("#cl2").html(data);
			$("html,body").animate({scrollTop: '200px'},800);
			if(batch!=0)
			$("#batch"+batch).click();
			else
			{				
				var b=document.getElementsByClassName("mbt");
				$("#batch"+b.length).click();
			}
		})
	}
	function ad_med(batch,plan)
	{
		$("#med_mod").click();
		$("#ins_med").hide();
		$('#med_list').css('height','100px');
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			batch:batch,
			plan:plan,
			type:"ipd_add_medicine",
		},
		function(data,status)
		{
			$("#med_list").html(data);
		})
	}
	function folow(c,id,j)
	{
		var top=c.top-10;
		$("html,body").animate({scrollTop: '350px'},1000);
		$("#gter").fadeIn(500);
		$('#gter').css("top",top);
		$(".a").removeClass("clk");
		$("#a"+id+j).addClass("clk");
		$.post("pages/global_load_g.php",
		{
			id:id,
			sl:j,
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"ipd_pat_med_folow",
		},
		function(data,status)
		{
			$("#fol_med").html(data);
		})
	}
	function medi_given(stat,id,sl)
	{
		$.post("pages/global_insert_data_g.php",
		{
			id:id,
			sl:sl,
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			stat:stat,
			usr:$("#user").text().trim(),
			type:"ipd_pat_medi_given",
		},
		function(data,status)
		{
			med_admin();
			$("#fol_med").html(data);
			$("#gter").fadeOut(500);
		})
	}
	function investigation(batch)
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			type:"pat_ipd_inv_det",
		},
		function(data,status)
		{
			$("#cl6").html(data);
			if(batch!=0)
			$("#ad"+batch).click();
			else
			{				
				var b=document.getElementsByClassName("bt");
				$("#ad"+b.length).click();
			}
			$("html,body").animate({scrollTop: '370px'},800);
		})
	}
	function ip_consult()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			type:"pat_ipd_ip_consult",
		},
		function(data,status)
		{
			$("#cl7").html(data);
			$("html,body").animate({scrollTop: '460px'},800);
		})
	}
	function ipd_save_new_note()
	{
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			ip_note:$("#ip_note").val().trim(),
			con_doc:$("#con_doc").val(),
			usr:$("#user").text().trim(),
			type:"ipd_save_note",
		},
		function(data,status)
		{
			ip_consult();
		})
	}
	function ip_note(id)
	{
		$("#nt_btn").click();
		$.post("pages/global_load_g.php",
		{
			id:id,
			type:"ipd_ip_note_edit",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function ipd_save_note()
	{
		$("#nt_btn").click();
		$.post("pages/global_load_g.php",
		{
			type:"ipd_ip_add_doc",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function save_ip_note(id)
	{
		$.post("pages/global_insert_data_g.php",
		{
			id:id,
			ip_note:$("#ip_note").val().trim(),
			usr:$("#user").text().trim(),
			type:"ipd_save_ip_note",
		},
		function(data,status)
		{
			ip_consult();
		})
	}
	function auto_note()
	{
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"ipd_auto_save_ip_note",
		},
		function(data,status)
		{
			
		})
	}
	function vital()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			view:"1",
			usr:$("#user").text().trim(),
			type:"pat_ipd_vital_det",
		},
		function(data,status)
		{
			$("#cl4").html(data);
			$("#weight").focus();
			$("html,body").animate({scrollTop: '400px'},900);
		})
	}
	function view_vital(v)
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			view:v,
			usr:$("#user").text().trim(),
			type:"pat_ipd_vital_det",
		},
		function(data,status)
		{
			$("#cl4").html(data);
			$("html,body").animate({scrollTop: '350px'},900);
			if(v==1)
			$("#weight").focus();
		})
	}
	function save_vital()
	{
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			weight:$("#weight").val(),
			height:$("#height").val(),
			mid_cum:$("#mid_cum").val(),
			hd_cum:$("#hd_cum").val(),
			bmi1:$("#bmi1").val(),
			bmi2:$("#bmi2").val(),
			spo:$("#spo").val(),
			pulse:$("#pulse").val(),
			temp:$("#temp").val(),
			pr:$("#pr").val(),
			rr:$("#rr").val(),
			systolic:$("#systolic").val(),
			diastolic:$("#diastolic").val(),
			vit_note:$("#vit_note").val(),
			usr:$("#user").text().trim(),
			type:"pat_ipd_vital_save",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				view_vital("1");
			}, 1000);
			//$("html,body").animate({scrollTop: '350px'},900);
		})
	}
	function physical(val,e)
	{
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
			$("#mid_cum").focus();	
		}
	}
	function addd()
	{
		$("#dl").click();
		$.post("pages/global_load_g.php",
		{
			type:"ipd_pat_add_diag",
		},
		function(data,status)
		{
			//$("#cl1").html(data);
			//$("#dl").click();
			$("#add_opt").html(data);
			setTimeout(function(){$("#diag").focus();},500);
		})
	}
	function ad()
	{
		$.post("pages/global_load_g.php",
		{
			type:"ipd_pat_doc_list",
		},
		function(data,status)
		{
			var rr=document.getElementById("diag_table").rows.length;
			if($("#tr"+(rr-1)).find('td:first input:first').val() && $("#tr"+(rr-1)).find('td:eq(1) select:first').val()!="0" && $("#tr"+(rr-1)).find('td:eq(2) select:first').val()!="0" && $("#tr"+(rr-1)).find('td:eq(3) select:first').val()!="0")
			$('#diag_table').append('<tr id="tr'+rr+'"><td><input type="text" class="span4" onkeyup="diagtab(1,event)" id="diagnosis1" placeholder="Diagnosis" /></td><td><select id="order1" onkeyup="diagtab(1,event)" class="span2"><option value="0">--Select--</option><option value="Primary">Primary</option><option value="Secondary">Secondary</option></select></td><td><select id="cert1" onkeyup="diagtab(1,event)" class="span2"><option value="0">--Select--</option><option value="Confirmed">Confirmed</option><option value="Presumed">Presumed</option></select></td><td><select id="doc"><option value="0">Select</option>'+data+'</select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
		})
	}
	function rcv_sample(uhid,ipd,batch)
	{
		$("#mod").click();
		$.post("pages/phlebo_load_sample_nurse.php",
		{
			uhid:uhid,
			ipd:ipd,
			batch_no:batch,
			lavel:$("#lavel_id").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#results").html(data);
			load_vaccu();
			//$("#mod").click();
			//$("#results").fadeIn(500,function(){ load_vaccu(); })
		})
	}
	function save()
	{
		var diag="";
		var rr=document.getElementById("diag_table").rows.length;
		for(var j=1;j<rr;j++)
		{
			if($("#tr"+j).find('td:first input:first').val() && $("#tr"+j).find('td:eq(1) select:first').val()!="0" && $("#tr"+j).find('td:eq(2) select:first').val()!="0" && $("#tr"+j).find('td:eq(3) select:first').val()!="0")
			diag+=$("#tr"+j).find('td:first input:first').val()+"@"+$("#tr"+j).find('td:eq(1) select:first').val()+"@"+$("#tr"+j).find('td:eq(2) select:first').val()+"@"+$("#tr"+j).find('td:eq(3) select:first').val()+"#g#";
		}
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			diag:diag,
			usr:$("#user").text().trim(),
			type:"save_ipd_pat_diag_nurse",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				diagnosis();
			}, 1000);
		})
	}
</script>
<script src="../jss/medication_plan.js"></script>
<script src="../jss/post_medicine.js"></script>
<script src="../jss/indent_medicine.js"></script>
<style>
	#myAlert, #myAlert1, #myModal_med, #medplan, #myModal_post
	{
	    width: 80%;
		margin-left: -40%;
	}
	#myModal
	{
		left: 33%;
		width:75%;
	}
	.ScrollStyle
	{
		max-height: 400px;
		overflow-y: scroll;
	}
	.btn_round_msg
	{
		color:#000;
		padding:2px;
		border-radius: 7em;
		padding-right:10px;
		padding-left:10px;
		box-shadow: inset 1px 1px 0 rgba(0,0,0,0.6);
		transition: all ease-in-out 0.2s;
	}
	.red
	{
		background-color: #d59a9a;
	}
	.green
	{
		background-color:#9dcf8a;
	}
	.yellow
	{
		background-color:#f6e8a8;
	}
	input[type="checkbox"]:not(old) + label, input[type="radio"]:not(old) + label
	{
		display: inline-block;
		margin-left:0;
		line-height: 1.5em;
	}
	input[type=checkbox]
	{
		margin:-3px 0 0;
	}
	.btt,.btt:hover,.btt:focus, .clk, .clk:hover, .clk:focus
	{
		background:#708090;
		color:#ffffff;
	}
	#gter
	{
		background: #ffffff;
		color: #000000;
		box-shadow: 2px 2px 5px #000;
		padding: 5px 0px 5px 0px;
		font-size: 11px;
		font-family: verdana;
		width: 300px;
		position: absolute;
		left: 70%;
	}
	.modal.fade.in
	{
		top: 3%;
	}
	.modal-body
	{
		max-height: 540px;
	}
	.emer, .emer:hover
	{
		background:#f8dcdc;
	}
	.txt
	{
		width:100px;
	}
</style>
