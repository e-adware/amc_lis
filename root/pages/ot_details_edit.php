<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">OT Details Edit</span></div>
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
	?>
	<span style="float:right;"><input type="button" class="btn btn-info" id="add" value="Back to list" onclick="window.location='processing.php?param=256'" style="" /></span>
	<table class="table table-condensed table-bordered" style="background: snow">
		<tr>
			<th>UHID</th>
			<th>IPD ID</th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Grade</th>
			<th>Cabin Type</th>
			<?php
			if($adm['date'])
			{
			?>
			<th>Admitted On</th>
			<?php
			}
			if($doc['Name'])
			{
			?>
			<th>Admitted Under</th>
			<?php
			}
			?>
		</tr>
		<tr>
			<td><?php echo $uhid;?></td>
			<td><?php echo $ipd;?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $pat['sex'];?></td>
			<td><?php echo $grade['grade_name'];?></td>
			<td><?php echo $cab['ot_cabin_name'];?></td>
			<?php
			if($adm['date'])
			{
			?>
			<td><?php echo convert_date_g($adm['date']);?></td>
			<?php
			}
			if($doc['Name'])
			{
			?>
			<td><?php echo $doc['Name'];?></td>
			<?php
			}
			?>
		</tr>
	</table>
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="ipd" value="<?php echo $ipd;?>" style="display:none;" />
	<input type="text" id="show" value="<?php echo $show;?>" style="display:none;" />
	<input type="text" id="adv" value="<?php echo $adv;?>" style="display:none;" />
	
	<div id="res" style="display:none;">
		
	</div>
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
		
		if($("#adv").val()!="")
		{
			$("#acc_surg").click();
		}
		setTimeout(function()
		{
			load_pat_det();
		},300);
	});
//------------------------------------------------------//
function load_pat_det()
{
	$("#loader").show();
	$.post("pages/ot_details_ajax.php",
	{
		uhid:$("#uhid").val(),
		ipd:$("#ipd").val(),
		shed:$("#show").val(),
		type:"ot_pat_details",
	},
	function(data,status)
	{
		$("#res").html(data);
		$("#ot_date").datepicker({dateFormat: 'yy-mm-dd',maxDate: '0'});
		$("#st_time").timepicker({minutes: {starts: 0,interval: 05,}});
		$("#en_time").timepicker({minutes: {starts: 0,interval: 05,}});
		$("#res").fadeIn(600,function(){$("#loader").hide()});
	});
}
function change_ot_res(j)
{
	$("#inp"+j).val($("#sel"+j).val());
}
function ot_details_update()
{
	$("#btn_upd").attr("disabled",true);
	var all="";
	var sel=$(".sel_type");
	for(var i=0,j=1; i<sel.length; i++,j++)
	{
		all+=$("#rs"+j).val()+"@"+$("#inp"+j).val()+"#%#";
	}
	//alert(all);
	$("#loader").show();
	$.post("pages/ot_details_ajax.php",
	{
		uhid:$("#uhid").val().trim(),
		ipd:$("#ipd").val().trim(),
		shed:$("#show").val().trim(),
		ot_typ:$("#ot_typ").val(),
		pr:$("#pr").val(),
		rf_doc:$("#rf_doc").val(),
		ot_date:$("#ot_date").val(),
		anas:$("#anas").val(),
		st_time:$("#st_time").val(),
		en_time:$("#en_time").val(),
		diag:$("#diag").val().trim(),
		rem:$("#rem").val().trim(),
		user:$("#user").text().trim(),
		all:all,
		type:"ot_details_update",
	},
	function(data,status)
	{
		$("#loader").hide();
		//alert(data);
		$("#btn_upd").attr("disabled",false);
		bootbox.dialog({ message: data});
		setTimeout(function()
		{
			bootbox.hideAll();
		}, 1000);
	});
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
