<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">OT Dashboard</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
<?php
	$uhid=base64_decode($_GET['uhid']);
	$ipd=base64_decode($_GET['ipd']);
	$show=base64_decode($_GET['show']);
	$adv=base64_decode($_GET['adv']);
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	if($pat['dob'])
	$age=age_calculator($pat['dob'])." (".$pat['dob'].")";
	else
	$age=$pat['age']." ".$pat['age_type'];
	$adm=mysqli_fetch_array(mysqli_query($link,"SELECT `date` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
	$grade=mysqli_fetch_array(mysqli_query($link,"SELECT a.`grade_name`,b.`ot_cabin_id` FROM `ot_grade_master` a, `ot_schedule` b WHERE b.`patient_id`='$uhid' AND b.`ipd_id`='$ipd' AND a.`grade_id`=b.`grade_id`"));
	$cab=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_cabin_name` FROM `ot_cabin_master` WHERE `ot_cabin_id`='$grade[ot_cabin_id]'"));
	
$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);
$pat_type_str=base64_decode($_GET['pat_type_str']);

$str="";
if($_GET["uhid_str"])
{
	$str.="&uhid_str=$uhid_str";
}

if($_GET["pin_str"])
{
	$str.="&pin_str=$pin_str";
}

if($_GET["fdate_str"])
{
	$str.="&fdate_str=$fdate_str";
}

if($_GET["tdate_str"])
{
	$str.="&tdate_str=$tdate_str";
}

if($_GET["name_str"])
{
	$str.="&name_str=$name_str";
}

if($_GET["phone_str"])
{
	$str.="&phone_str=$phone_str";
}

if($_GET["param_str"])
{
	$str.="&param=$param_str";
}

if($_GET["pat_type_str"])
{
	$str.="&pat_type_str=$pat_type_str";
}
?>
<?php if($str){ ?>
	<span style="float:right;">
		<input type="button" class="btn btn-success" id="add" value="Back to list" onclick="window.location='processing.php?v=0<?php echo $str; ?>'" style="" />
	</span>
<?php } ?>
	<table class="table table-condensed table-bordered" style="background: snow">
		<tr>
			<th>UHID</th>
			<th>IPD ID</th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Grade</th>
			<th>Cabin Type</th>
			<th>Admitted On</th>
			<th>Admitted Under</th>
		</tr>
		<tr>
			<td><?php echo $uhid;?></td>
			<td><?php echo $ipd;?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $pat['sex'];?></td>
			<td><?php echo $grade['grade_name'];?></td>
			<td><?php echo $cab['ot_cabin_name'];?></td>
			<td><?php echo convert_date_g($adm['date']);?></td>
			<td><?php echo $doc['Name'];?></td>
		</tr>
	</table>
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="ipd" value="<?php echo $ipd;?>" style="display:none;" />
	<input type="text" id="show" value="<?php echo $show;?>" style="display:none;" />
	<input type="text" id="adv" value="<?php echo $adv;?>" style="display:none;" />
	<input type="hidden" id="chk_val" value="0"/>
	<input type="hidden" id="chk_val1" value="0"/>
	<div class="" style="">
		<div class="accordion" id="collapse-group">
			<div class="accordion-group widget-box" style="display:none;"><!--box 1-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse1" data-toggle="collapse" onclick="show_icon(1)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Scheduling</b><i class="icon-arrow-down" id="ard1"></i><i class="icon-arrow-up" id="aru1" style="display:none;"></i></span>
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
			<div class="accordion-group widget-box"> <!--box 10-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse10" data-toggle="collapse" onclick="show_icon(10)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Investigation</b><i class="icon-arrow-down" id="ard10"></i><i class="icon-arrow-up" id="aru10" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign10" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign10" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse10" style="height:0px;max-height:400px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl10" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box"><!--box 2-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse2" data-toggle="collapse" onclick="show_icon(2)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Pre Anesthesia Notes</b><i class="icon-arrow-down" id="ard2"></i><i class="icon-arrow-up" id="aru2" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign2" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign2" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse2" style="height:0px;max-height:500px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl2" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box"> <!--box 14-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse14" data-toggle="collapse" onclick="show_icon(14)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">OT Notes</b><i class="icon-arrow-down" id="ard14"></i><i class="icon-arrow-up" id="aru14" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign14" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign14" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse14" style="height:0px;max-height:500px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl14" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box"> <!--box 7-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse7" id="acc_surg" data-toggle="collapse" onclick="show_icon(7)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Surgery Record</b><i class="icon-arrow-down" id="ard7"></i><i class="icon-arrow-up" id="aru7" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign7" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign7" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse7" style="height:0px;max-height:500px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl7" style="display:none;">
					
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box"> <!--box 8-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse8" data-toggle="collapse" onclick="show_icon(8)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Post Surgery Records</b><i class="icon-arrow-down" id="ard8"></i><i class="icon-arrow-up" id="aru8" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign8" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign8" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse8" style="height:0px;max-height:500px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl8" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box"> <!--box 9-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse9" data-toggle="collapse" onclick="show_icon(9)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Medicine Indent</b><i class="icon-arrow-down" id="ard9"></i><i class="icon-arrow-up" id="aru9" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign9" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign9" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse9" style="height:0px;max-height:500px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl9" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box" style="display:none;"> <!--box 11-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse11" data-toggle="collapse" onclick="show_icon(11)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Equipment</b><i class="icon-arrow-down" id="ard11"></i><i class="icon-arrow-up" id="aru11" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign11" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign11" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse11" style="height:0px;max-height:300px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl11" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box" style="display:none;"> <!--box 12-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse12" data-toggle="collapse" onclick="show_icon(12)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">General Consumables</b><i class="icon-arrow-down" id="ard12"></i><i class="icon-arrow-up" id="aru12" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign12" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign12" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse12" style="height:0px;max-height:300px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl12" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box" style="display:none;"> <!--box 13-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse13" data-toggle="collapse" onclick="show_icon(13)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Surgical Consumables</b><i class="icon-arrow-down" id="ard13"></i><i class="icon-arrow-up" id="aru13" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign13" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign13" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse13" style="height:0px;max-height:300px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl13" style="display:none;">
						
					</div>
				</div>
			</div><!--End box-->
			
			<div class="accordion-group widget-box" style=""> <!--box 3-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse3" data-toggle="collapse" onclick="show_icon(3)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Leave OT Room</b><i class="icon-arrow-down" id="ard3"></i><i class="icon-arrow-up" id="aru3" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign3" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign3" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse3" style="height:0px;">
					<div class="widget-content hidden_div" id="cl3" style="display:none;">
						
					</div>
				</div>
			</div><!--End box-->
			
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
<div id="msgg" style="position:fixed;display:none;top:40%;left:45%;font-size:30px;color:#ee0000;"></div>
<div id="loader" style="position:fixed;display:none;top:40%;left:50%;"></div>
<div id="gter" class="gritter-item" style="display:none;width:200px;">
	<div class="gritter-close" style="display:block;" onclick="$('.a').removeClass('clk');$('#gter').fadeOut(500)"></div>
	<span class="gt-title" style="font-size: 12px;font-family: verdana;font-weight: bold;padding-left: 10px;">Medicine Administor</span>
	<p id='fol_med' style="padding:6px;font-size:12px;"></p>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<link rel="stylesheet" href="../css/loader.css" />
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
		//alert($("#show").val());
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
		if($("#adv").val()!="")
		{
			$("#acc_surg").click();
		}
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
//-------------------------------------------------------------
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
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			batch:$("#batch").val().trim(),
			shed:$("#show").val().trim(),
			test:val,
			type:3,
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
//------------------------------------------------------// icon--
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
				scheduling();
				//$("html,body").animate({scrollTop: '200px'},800);
			}
			else if(i==2)
			{
				pre_anesthesia();
			}
			else if(i==3)
			{
				ot_leave();
			}
			else if(i==7)
			{
				surgery_record();
			}
			else if(i==8)
			{
				post_surgery_record();
			}
			else if(i==9)
			{
				ot_medicine_indent();
			}
			else if(i==10)
			{
				ot_investigation();
			}
			else if(i==14)
			{
				ot_notes();
			}
		}
	}
//-----------------------------------------------------OT Update----
	function upd_shed_res()
	{
		var uhid=$("#uhid").val().trim();
		var ipd=$("#ipd").val().trim();
		var shed=$("#show").val().trim();
		window.location='processing.php?param=214&uhid='+uhid+'&ipd='+ipd+'&show='+shed;
	}
//-----------------------------------------------------OT Leave----
	function load_ot_charge(a)
	{
		$.post("pages/ot_dashboard_ajax.php",
		{
			id:$(a).val(),
			type:22,
		},
		function(data,status)
		{
			$(a).closest("tr").find("td:eq(3) input[type='text']").val(data);
		})
	}
	function ot_leave()
	{
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			usr:$("#user").text().trim(),
			type:20,
		},
		function(data,status)
		{
			$("#cl3").html(data);
			//$("html,body").animate({scrollTop: '500px'},800);
			//setTimeout(function(){ $("#test").focus()},500);
		})
	}
	//alert($("#show").val());
	function ot_leave_done(sh)
	{
		bootbox.dialog({
			message: "<h5>Confirm leaving OT room?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/ot_dashboard_ajax.php",
						{
							uhid:$("#uhid").val().trim(),
							ipd:$("#ipd").val().trim(),
							usr:$("#user").text().trim(),
							sh:sh,
							type:21,
						},
						function(data,status)
						{
							bootbox.dialog({ message: data});
							setTimeout(function()
							{
								bootbox.hideAll();
							}, 1000);
							ot_leave();
						})
					}
				}
			}
		});
	}
//-----------------------------------------------------Medicine Tab----
	function insert_ot_med_ind()
	{
		var tr= $('#ind_tbl tr.ind').length;
		var det="";
		//alert(tr);
		for(var i=0;i<tr;i++)
		{
			$("#tr"+i).find('td:first input:first').val()
			det+=$("#tr"+i).find('td:eq(1) input:first').val()+"@@"+$("#tr"+i).find('td:eq(2) input:first').val()+"@@#gg#";
		}
		//alert(det);
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			det:det,
			usr:$("#user").text().trim(),
			type:15,
		},
		function(data,status)
		{
			ot_medicine_indent();
		})
	}
	function ot_medicine_indent()
	{
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			usr:$("#user").text().trim(),
			type:14,
		},
		function(data,status)
		{
			$("#cl9").css("height","300px");
			$("#cl9").html(data);
			$("html,body").animate({scrollTop: '500px'},800);
			//setTimeout(function(){ $("#test").focus()},500);
		})
	}
	function del_indent_medicine(slno)
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to delete ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/ot_dashboard_ajax.php",
						{
							slno:slno,
							type:16,
						},
						function(data,status)
						{
							ot_medicine_indent();
						})
					}
				}
			}
		});
	}
//-----------------------------------------------------Investigation Tab--
	function ot_investigation(batch)
	{
		$("#loader").show();
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			usr:$("#user").text().trim(),
			type:1,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#cl10").html(data);
			if(batch!=0)
			$("#ad"+batch).click();
			else
			{				
				var b=document.getElementsByClassName("bt");
				$(".bt:first").click();
			}
			$("html,body").animate({scrollTop: '220px'},800);
			/*bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);*/
		})
	}
	function ad_tests(batch)
	{
		$("#dl1").click();
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			batch:batch,
			type:2,
		},
		function(data,status)
		{
			$("#tests_lst").html(data);
			setTimeout(function(){ $("#test").focus()},500);
		})
	}
	function save_test()
	{
		var tst=$("input.test_id").map(function()
		{
			return this.value;
		}).get().join(",");
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			batch:$("#batch").val().trim(),
			shed:$("#show").val().trim(),
			tst:tst,
			usr:$("#user").text().trim(),
			type:4,
		},
		function(data,status)
		{
			ot_investigation($("#batch").val());
		})
	}
	function view_batch(batch)
	{
		$(".bt").removeClass('btt');
		$("#ad"+batch).addClass('btt');
		$("#loader").show();
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			batch_no:batch,
			user:$("#user").text().trim(),
			lavel:$("#lavel_id").val(),
			type:5,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#batch_details").html(data);
			$("#foll_details").html('');
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
//------------------------------------------------------Pre Anesthesia Tab--
	function pre_anesthesia()
	{
		$("#loader").show();
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			type:6,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#cl2").html(data);
			$("html,body").animate({scrollTop: '250px'},800);
		})
	}
	function save_pre_anes_notes()
	{
		$("#pre_ans_btn").attr("disabled",true);
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
			pre_anesthesia();
		})
	}
//---------------------------------------OT Note Tab--
	function ot_notes()
	{
		$("#loader").show();
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			type:7,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#cl14").html(data);
			$("html,body").animate({scrollTop: '300px'},800);
		})
	}
	function insert_ot_notes()
	{
		var an=$("input[name='anaes']:checked");
		var ec=$("input[name='ecg']:checked");
		var anes="";
		var ecg="";
		for(var i=0; i<=an.length; i++)
		{
			if($(an[i]).val())
			{
				anes+=$(an[i]).val()+"@";
			}
		}
		for(var i=0; i<=ec.length; i++)
		{
			if($(ec[i]).val())
			{
				ecg+=$(ec[i]).val()+"@";
			}
		}
		$("#btn_ot_note").attr("disabled",true);
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			asa:$("#asa").val(),
			asa_stat:$("input[name='stat']:checked").val(),
			ident:$("input[name='ident']:checked").val(),
			consent:$("input[name='consent']:checked").val(),
			oral:$("#oral").val(),
			pr:$("#pr").val(),
			bp:$("#bp").val(),
			heart:$("#heart").val(),
			anaes:anes,
			ecg:ecg,
			spo:$("input[name='spo']:checked").val(),
			nibp:$("input[name='nibp']:checked").val(),
			temp:$("input[name='temp']:checked").val(),
			proc:$("#proc").val(),
			pos:$("#pos").val(),
			incision:$("#incision").val(),
			usr:$("#user").text().trim(),
			type:12,
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
			ot_notes();
		})
	}
//------------------------------------------------------Surgery Tab--
	function add_res_row()
	{
		var ntr=$("#srce_tbl tr.nsource");
		var ckk=0;
		for(var j=0; j<(ntr.length); j++)
		{
			if($(".nsource:eq("+j+")").find('td:eq(1) select:first').val()=="0")
			{
				$(".nsource:eq("+j+")").find('td:eq(1) select:first').focus();
				ckk=1;
				return true;
			}
			if($(".nsource:eq("+j+")").find('td:eq(2) input:first').val().trim()=="")
			{
				$(".nsource:eq("+j+")").find('td:eq(2) input:first').focus();
				ckk=1;
				return true;
			}
		}
		if(ckk==1)
		{
			if($(".nsource:eq("+j+")").find('td:eq(1) select:first').val()=="0")
			{
				$(".nsource:eq("+j+")").find('td:eq(1) select:first').focus();
				return true;
			}
			if($(".nsource:eq("+j+")").find('td:eq(2) input:first').val()=="")
			{
				$(".nsource:eq("+j+")").find('td:eq(2) input:first').focus();
				return true;
			}
		}
		else
		{
			$("#loader").show();
			$.post("pages/ot_dashboard_ajax.php",
			{
				type:17,
			},
			function(data,status)
			{
				$("#loader").hide();
				var tr=$("#srce_tbl tr.osource").length;
				var sl="<select class='span1' style='width:80px;' onchange='calc_charge(this)'>";
				for(var t=1; t<10; t++)
				{
					sl+="<option value='"+t+"'>"+t+"</option>";
				}
				sl+="</select>";
				$("#res_tr").closest("tr").before('<tr class="source nsource" id=""><td class="tr_sl"></td><td>'+data+'<br/><input type="text" class="inp span3" placeholder="Service Text" /></td><td>'+sl+' X <input type="text" onkeyup="sum_amt(this)" style="width:80px;" /></td><td><input type="text" onkeyup="sum_amt(this)" readonly="readonly" placeholder="Amount" /><span style="float:right;padding:5px;color:#aa0000;cursor:pointer;" onclick="$(this).parent().parent().remove();del_res_row()"><i class="icon-remove icon-large"></i></span></td></tr>');
				
				for(var j=0; j<=ntr.length; j++)
				{
					$(".nsource:eq("+j+")").find('td:eq(0)').text(tr+j+1);
				}
			})
		}
	}
	
	function load_service(z)
	{
		var val=$(z).val();
		$("#loader").show();
		$.post("pages/ot_dashboard_ajax.php",
		{
			type:18,
			val:val,
		},
		function(data,status)
		{
			//alert(data);
			$("#loader").hide();
			var vl=data.split("@@@");
			var rt=vl[1];
			var rate=rt.split(".");
			$(z).closest("td").find("input[type='text']:first").val(vl[0]);
			$(z).closest("tr").find("td:eq(2) input[type='text']:first").val(rate[0]);
			$(z).closest("tr").find("td:eq(2) select:first").val('1');
			$(z).closest("tr").find("td:eq(3) input[type='text']:first").val(vl[1]);
			sum_amt();
		})
	}
	function calc_charge(d)
	{
		var q=$(d).val();
		var amt=$(d).closest("td").find("input[type='text']:first").val().trim();
		if(amt=="")
		{
			amt=0;
		}
		var tot=(parseInt(q)*parseFloat(amt));
		$(d).closest("tr").find("td:eq(3) input[type='text']:first").val(tot);
		sum_amt();
	}
	function del_res_row()
	{
		var tr=$("#srce_tbl tr.osource").length;
		var ntr=$("#srce_tbl tr.nsource");
		for(var j=0; j<=ntr.length; j++)
		{
			$(".nsource:eq("+j+")").find('td:eq(0)').text(tr+j+1);
		}
		sum_amt();
	}
	function sum_amt(a)
	{
		var ot_pay=parseInt($("#ot_pay").val());
		var grade_rate=parseInt($("#grade_rate").val().trim());
		if(($(a).val()*0)!=0)
		{
			$(a).val('');
			//return true;
		}
		var otr=$(".osource");
		var tot=0;
		var vl=0;
		for(var j=0; j<(otr.length); j++)
		{
			vl=$(".osource:eq("+j+")").find('td:eq(3) input:first').val();
			$(".osource:eq("+j+")").find('td:eq(0) span').text(j+1);
			if(vl=="")
			{
				vl=0;
			}
			else
			{
				vl=parseFloat(vl);
			}
			tot+=vl;
		}
		if(ot_pay>0)
		{
			var serv_tot=$("#serv_tot").val();
			if(serv_tot=="")
			{
				serv_tot=0;
			}
			else
			{
				serv_tot=parseInt($("#serv_tot").val());
			}
			
			if(serv_tot==ot_pay)
			{
				$("#serv_tot").css({'border-color':'','box-shadow':''});
				$("#err_msg").fadeOut(500);
			}
			else
			{
				$("#serv_tot").css({'border-color':'#FE0002','box-shadow':'0px 0px 10px rgba(254, 0, 2, 0.6)'});
				$("#err_msg").slideDown();
			}
		}
		else
		{
			$("#serv_tot").css({'border-color':'','box-shadow':''});
			$("#err_msg").fadeOut(500);
		}
		/*
		var ntr=$(".nsource");
		for(var j=0; j<(ntr.length); j++)
		{
			vl=$(".nsource:eq("+j+")").find('td:eq(2) input:first').val();
			qn=parseInt($(".nsource:eq("+j+")").find('td:eq(2) select:first').val());
			if(vl=="")
			{
				vl=0;
			}
			else
			{
				vl=parseFloat(vl);
			}
			vl=vl*qn;
			$(".nsource:eq("+j+")").find('td:eq(3) input:first').val(vl);
			tot+=vl;
		}
		
		if(tot!=grade_rate)
		{
			$("#serv_tot").css({'border-color':'#FE0002','box-shadow':'0px 0px 10px rgba(254, 0, 2, 0.6)'});
		}
		else
		{
			$("#serv_tot").css({'border-color':'','box-shadow':''});
		}*/
		$("#serv_tot").val(tot);
		//sum_amt();
	}
	function surgery_record()
	{
		$("#loader").show();
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			type:8,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#cl7").html(data);
			$("html,body").animate({scrollTop: '350px'},800);
		})
	}
	function change_emp(a)
	{
		$(a).closest("tr").find("td:eq(0) input[type='text']:last").val($(a).val());
	}
	function insert_surg_rec()
	{
		sum_amt();
		var ot_pay=parseInt($("#ot_pay").val());
		if(ot_pay>0)
		{
			var serv_tot=$("#serv_tot").val();
			if(serv_tot=="")
			{
				serv_tot=0;
			}
			else
			{
				serv_tot=parseInt($("#serv_tot").val());
			}
			
			if(serv_tot!=ot_pay)
			{
				$("#serv_tot").css({'border-color':'#FE0002','box-shadow':'0px 0px 10px rgba(254, 0, 2, 0.6)'});
				$("#err_msg").slideDown();
			}
			else
			{
				$("#serv_tot").css({'border-color':'','box-shadow':''});
				$("#err_msg").fadeOut(500);
				final_insert_rec();
			}
		}
		else
		{
			final_insert_rec();
		}
	}
	function final_insert_rec()
	{
		/*
		if($("#anaes_st_time").val()=="")
		{
			$("#anaes_st_time").focus();
			return true;
		}
		if($("#anaes_en_time").val()=="")
		{
			$("#anaes_en_time").focus();
			return true;
		}
		if($("#ot").val()=="0")
		{
			$("#ot").focus();
			return true;
		}
		if($("#pat_in_time").val()=="")
		{
			$("#pat_in_time").focus();
			return true;
		}
		if($("#act_st_time").val()=="")
		{
			$("#act_st_time").focus();
			return true;
		}
		if($("#sur_st_time").val()=="")
		{
			$("#sur_st_time").focus();
			return true;
		}
		if($("#sur_en_time").val()=="")
		{
			$("#sur_en_time").focus();
			return true;
		}
		if($("#act_en_time").val()=="")
		{
			$("#act_en_time").focus();
			return true;
		}
		if($("#pro_st_time").val()=="")
		{
			$("#pro_st_time").focus();
			return true;
		}
		if($("#pro_en_time").val()=="")
		{
			$("#pro_en_time").focus();
			return true;
		}
		if($("#pat_out_time").val()=="")
		{
			$("#pat_out_time").focus();
			return true;
		}
		*/
		var grade_rate=parseInt($("#grade_rate").val().trim());
		var tot=0;
		var vl=0;
		
		var ln=$('#srce_tbl tr.source');
		var oln=$('#srce_tbl tr.osource');
		var nln=$('#srce_tbl tr.nsource');
		//alert(ln.length);
		var res="";
		var chk_i=0;
		var chk_j=0;
		
		for(var i=0; i<oln.length; i++)
		{
			if($(".osource:eq("+i+")").find('td:eq(3) input:first').val().trim()=="")
			{
				$(".osource:eq("+i+")").find('td:eq(3) input:first').focus();
				chk_i=1;
				return true;
			}
			else
			{
				vl=$(".osource:eq("+i+")").find('td:eq(3) input:first').val().trim();
				if(vl=="")
				{
					vl=0;
				}
				else
				{
					vl=parseFloat(vl);
				}
				tot+=vl;
			}
		}
		
		for(var j=0; j<nln.length; j++)
		{
			if($(".nsource:eq("+j+")").find('td:eq(1) select:first').val()=="0")
			{
				$(".nsource:eq("+j+")").find('td:eq(1) select:first').focus();
				chk_j=1;
				return true;
			}
			if($(".nsource:eq("+j+")").find('td:eq(3) input:first').val()=="")
			{
				$(".nsource:eq("+j+")").find('td:eq(3) input:first').focus();
				chk_j=1;
				return true;
			}
		}
		
		if(chk_i==1)
		{
			$(".osource:eq("+i+")").find('td:eq(3) input:first').focus();
			return true;
		}
		else if(chk_j==1)
		{
			if($(".nsource:eq("+j+")").find('td:eq(1) select:first').val()=="0")
			{
				$(".nsource:eq("+j+")").find('td:eq(1) select:first').focus();
				return true;
			}
			if($(".nsource:eq("+j+")").find('td:eq(3) input:first').val()=="")
			{
				$(".nsource:eq("+j+")").find('td:eq(3) input:first').focus();
				return true;
			}
		}
		/*
		else if(tot!=grade_rate)
		{
			$("#serv_tot").css({'border-color':'#FE0002','box-shadow':'0px 0px 10px rgba(254, 0, 2, 0.6)'});
			$(".osource:last").find('td:eq(3) input:first').focus();
			return true;
		}*/
		//-----------------------------------------------------
		else
		{
			$("#rec_btn").attr("disabled",true);
			
			var res_data="";
			var res_id="";
			var emp_id="";
			var amt=0;
			
			var serv_data="";
			var serv_id="";
			var serv_txt="";
			var serv_qnt=0;
			var serv_rate=0;
			var serv_amt=0;
			
			for(var i=0; i<oln.length; i++) // resourse values
			{
				res_id=$(".osource:eq("+i+")").find('td:eq(0) input:first').val().trim();
				emp_id=$(".osource:eq("+i+")").find('td:eq(0) input:last').val().trim();
				amt=$(".osource:eq("+i+")").find('td:eq(3) input:last').val().trim();
				res_data+=res_id+"@@"+emp_id+"@@"+amt+"@@#@#";
			}
			
			for(var j=0; j<nln.length; j++) // service values
			{
				serv_id=$(".nsource:eq("+j+")").find('td:eq(1) select:first').val();
				serv_txt=$(".nsource:eq("+j+")").find('td:eq(1) input:first').val().trim();
				serv_qnt=$(".nsource:eq("+j+")").find('td:eq(2) select:first').val();
				serv_rate=$(".nsource:eq("+j+")").find('td:eq(2) input:first').val().trim();
				serv_amt=$(".nsource:eq("+j+")").find('td:eq(3) input:first').val().trim();
				serv_data+=serv_id+"@@"+serv_txt+"@@"+serv_qnt+"@@"+serv_rate+"@@"+serv_amt+"@@#@#";
			}
			//alert(serv_data);
			
			$("#loader").show();
			
			$.post("pages/ot_dashboard_ajax.php",
			{
				uhid:$("#uhid").val().trim(),
				ipd:$("#ipd").val().trim(),
				shed:$("#show").val().trim(),
				
				perf:$("#perf").val(),
				remark:$("#remark").val(),
				anaes_st_time:$("#anaes_st_time").val(),
				anaes_en_time:$("#anaes_en_time").val(),
				surg_note:$("#surg_note").val(),
				ot:$("#ot").val(),
				anaes:$("#anaes").val(),
				surg_type:$("#surg_type").val(),
				pat_in_time:$("#pat_in_time").val(),
				act_st_time:$("#act_st_time").val(),
				sur_st_time:$("#sur_st_time").val(),
				sur_en_time:$("#sur_en_time").val(),
				act_en_time:$("#act_en_time").val(),
				pro_st_time:$("#pro_st_time").val(),
				pro_en_time:$("#pro_en_time").val(),
				pat_out_time:$("#pat_out_time").val(),
				
				res_data:res_data,
				serv_data:serv_data,
				usr:$("#user").text().trim(),
				type:19,
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				surgery_record();
			})
			
		}
	}
	function insert_surg_rec_old()
	{
		var det="";
		var l=$('#srce_tbl tr.source').length;
		if($("#anaes_st_time").val()=="")
		{
			$("#anaes_st_time").focus();
			return true;
		}
		if($("#anaes_en_time").val()=="")
		{
			$("#anaes_en_time").focus();
			return true;
		}
		if($("#ot").val()=="0")
		{
			$("#ot").focus();
			return true;
		}
		if($("#pat_in_time").val()=="")
		{
			$("#pat_in_time").focus();
			return true;
		}
		if($("#act_st_time").val()=="")
		{
			$("#act_st_time").focus();
			return true;
		}
		if($("#sur_st_time").val()=="")
		{
			$("#sur_st_time").focus();
			return true;
		}
		if($("#sur_en_time").val()=="")
		{
			$("#sur_en_time").focus();
			return true;
		}
		if($("#act_en_time").val()=="")
		{
			$("#act_en_time").focus();
			return true;
		}
		if($("#pro_st_time").val()=="")
		{
			$("#pro_st_time").focus();
			return true;
		}
		if($("#pro_en_time").val()=="")
		{
			$("#pro_en_time").focus();
			return true;
		}
		if($("#pat_out_time").val()=="")
		{
			$("#pat_out_time").focus();
			return true;
		}
		for(var i=0;i<l;i++)
		{
			if($("#srce_tbl tr.source:eq("+i+")").find("td:eq(3) input:first").val()=="")
			{
				$("#srce_tbl tr.source:eq("+i+")").find("td:eq(3) input:first").focus();
				return true;
			}
			if($("#srce_tbl tr.source:eq("+i+")").find("td:eq(3) input:last").val()=="")
			{
				$("#srce_tbl tr.source:eq("+i+")").find("td:eq(3) input:last").focus();
				return true;
			}
			if($("#srce_tbl tr.source:eq("+i+")").find("td:eq(4) input:first").val()=="")
			{
				$("#srce_tbl tr.source:eq("+i+")").find("td:eq(4) input:first").focus();
				return true;
			}
			if($("#srce_tbl tr.source:eq("+i+")").find("td:eq(4) input:last").val()=="")
			{
				$("#srce_tbl tr.source:eq("+i+")").find("td:eq(4) input:last").focus();
				return true;
			}
			det+=$("#srce_tbl tr.source:eq("+i+")").find("td:eq(0) input:first").val()+"@"+$("#srce_tbl tr.source:eq("+i+")").find("td:eq(3) input:first").val()+"@"+$("#srce_tbl tr.source:eq("+i+")").find("td:eq(3) input:last").val()+"@"+$("#srce_tbl tr.source:eq("+i+")").find("td:eq(4) input:first").val()+"@"+$("#srce_tbl tr.source:eq("+i+")").find("td:eq(4) input:last").val()+"@##";
		}
		//alert(det);
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			perf:$("#perf").val(),
			remark:$("#remark").val(),
			anaes_st_time:$("#anaes_st_time").val(),
			anaes_en_time:$("#anaes_en_time").val(),
			surg_note:$("#surg_note").val(),
			ot:$("#ot").val(),
			anaes:$("#anaes").val(),
			surg_type:$("#surg_type").val(),
			pat_in_time:$("#pat_in_time").val(),
			act_st_time:$("#act_st_time").val(),
			sur_st_time:$("#sur_st_time").val(),
			sur_en_time:$("#sur_en_time").val(),
			act_en_time:$("#act_en_time").val(),
			pro_st_time:$("#pro_st_time").val(),
			pro_en_time:$("#pro_en_time").val(),
			pat_out_time:$("#pat_out_time").val(),
			det:det,
			usr:$("#user").text().trim(),
			type:13,
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
			surgery_record();
		})
	}
	function clrr()
	{
		$(".datepicker").val('');
		$(".timepicker").val('');
	}
	function add_diag_row()
	{
		$("#last_tr").closest("tr").after('<tr class="" id=""><td></td><td></td><td></td><td></td><td style="text-align:center;"><i class="icon-remove  icon-large" style="color:#aa0000;cursor:pointer;" onclick="$(this).parent().parent().remove()"></i></td></tr>');
	}
//------------------------------------------------------Post Surgery Record Tab--
	function post_surgery_record()
	{
		$("#loader").show();
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			type:9,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#cl8").html(data);
			$("html,body").animate({scrollTop: '400px'},800);
		})
	}
	function save_post_srec()
	{
		$.post("pages/ot_dashboard_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			shed:$("#show").val().trim(),
			
			req_no:$("#req_no").val().trim(),
			surgery:$("#surgery").val().trim(),
			notes:$("#notes").val().trim(),
			template:$("#template").val().trim(),
			
			airway:$("input[name='airway']:checked").val(),
			obst:$("input[name='obst']:checked").val(),
			score:$("input[name='score']:checked").val(),
			pul:$("input[name='pul']:checked").val(),
			vital:$("input[name='vital']:checked").val(),
			consc:$("input[name='consc']:checked").val(),
			orien:$("input[name='orien']:checked").val(),
			motor:$("input[name='motor']:checked").val(),
			cardio:$("input[name='cardio']:checked").val(),
			site:$("input[name='site']:checked").val(),
			hemor:$("input[name='hemor']:checked").val(),
			pain:$("input[name='pain']:checked").val(),
			urine:$("input[name='urine']:checked").val(),
			oth:$("#oth").val(),
			usr:$("#user").text().trim(),
			type:10,
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
			post_surgery_record();
		})
	}
//------------------------------------------------------Scheduling Tab--
	function scheduling()
	{
		$("#loader").show();
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"ot_scheduling",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#cl1").html(data);
			$("html,body").animate({scrollTop: '200px'},800);
			/*bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);*/
		})
	}
	function ot_schedule()
	{
		$("#loader").show();
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"ot_schedule",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#cl1").fadeOut(10);
			$("#cl1").fadeIn(800);
			$("#cl1").html(data);
			//$("html,body").animate({scrollTop: '600px'},1000);
		})
	}
	function ot_resource_list()
	{
		$.post("pages/global_load_g.php",
		{
			ot_type:$("#ot_type").val(),
			type:"ot_resource_list",
		},
		function(data,status)
		{
			$("#resource_list").html(data);
			//$("html,body").animate({scrollTop: '600px'},1000);
		})
	}
	function add_res()
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
				$("#end_tr").closest("tr").before('<tr class="cc cc'+$("#ot_type").val()+' cc'+$("#rs").val()+'" id="tr'+row+'"><td></td><td><input type="hidden" value="'+$("#ot_type").val()+'" />'+vl[0]+'</td><td><input type="hidden" value="'+$("#rs").val()+'" />'+vl[1]+'</td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="rem_str('+row+');$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
				var w=$("#sel_val").val()+","+$("#ot_type").val()+"%"+$("#rs").val();
				$("#sel_val").val(w);
				$("#ot_type").val('0');
				$("#rs").val('0');
			})
		}
	}
	function rem_str(id)
	{
		var v=$("#tr"+id).find('td:eq(1) input:first').val()+"%"+$("#tr"+id).find('td:eq(2) input:first').val();
		var w=$("#sel_val").val();
		w=w.replace(v,"");
		$("#sel_val").val(w);
	}
	function save_shed()
	{
		if($("#st_time").val()=="")
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
			var det="";
			var rr=$('#ot_tbl tbody tr.cc').length;
			for(var i=1;i<=rr;i++)
			{
				det+=$("#tr"+i).find('td:eq(1) input:first').val()+"@"+$("#tr"+i).find('td:eq(2) input:first').val()+"@#@#";
			}
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
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
				}, 1000);
			})
		}
	}
	function ot_book()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"pat_ipd_ot_booking",
		},
		function(data,status)
		{
			$("#cl17").html(data);
			$("html,body").animate({scrollTop: '600px'},1000);
		})
	}
	function ot_det_show()
	{
		$('#ot_det').slideDown();
		//$('#otad').attr('disabled',true);
		$('#otad').slideUp(1000);
		$("html,body").animate({scrollTop: '600px'},1000);
	}
	function ot_det_hide()
	{
		$('#ot_det').slideUp();
		//$('#otad').attr('disabled',false);
		$('#otad').slideDown(500);
	}
	function save_ot_book()
	{
		if($("#ot").val()=="0")
		{
			$("#ot").focus();
		}
		else if($("#pr").val()=="0")
		{
			$("#pr").focus();
		}
		else if($("#ot_date").val()=="")
		{
			$("#ot_date").focus();
		}
		else if($("#doc").val()=="0")
		{
			$("#doc").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				ot:$("#ot").val(),
				pr:$("#pr").val(),
				ot_date:$("#ot_date").val(),
				doc:$("#doc").val(),
				usr:$("#user").text().trim(),
				type:"save_ipd_ot_book",
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					ot_book();
				}, 1000);
			})
		}
	}
//---------------------------------------
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
			$("#cl14").html(data);
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
			$("#cl10").html(data);
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
			$("#cl11").html(data);
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
			$("#cl12").html(data);
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
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			show:$("#shed").val().trim(),
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
		$("#loader").show();
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			type:"pat_ipd_inv_det",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#cl3").html(data);
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
			$("#cl5").html(data);
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
