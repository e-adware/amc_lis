<?php
$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$schedule_id=base64_decode($_GET['schedule_id']);

if(!$schedule_id){ $schedule_id=0; }

if($schedule_id>0)
{
	$_SESSION["schedule_id"]=$schedule_id;
}
if($schedule_id==0 && $_SESSION["schedule_id"]>0)
{
	$patient_ot_schedule=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$_SESSION[schedule_id]'"));
	if($patient_ot_schedule)
	{
		$schedule_id=$_SESSION["schedule_id"];
	}
}

$pg_header="OT Entry Details";

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
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"><?php echo $pg_header;?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
<?php
	$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$reg_date=$pat_reg["date"];
	
	if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
	
	$doc_info=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
	$d_name="";
	if($doc_info)
	{
		$d_name=$doc_info['Name'];
	}
	else
	{
		$doc_info=mysqli_fetch_array(mysqli_query($link,"SELECT b.`ref_name` FROM `uhid_and_opdid` a, `refbydoctor_master`b WHERE a.`patient_id`='$uhid' AND a.`opd_id`='$ipd' AND a.`refbydoctorid`=b.`refbydoctorid` "));
		$d_name=$doc_info['ref_name'];
	}
?>
	<span style="float:right;">
		<button class="btn btn-back" onclick="window.location='processing.php?v=0<?php echo $str; ?>'"><i class="icon-backward"></i> Back</button>
	</span>
	<table class="table table-condensed table-bordered" style="background: snow">
		<tr>
			<th>Unit No.</th>
			<th>Bill No.</th>
			<th>Patient Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Consultant</th>
		</tr>
		<tr>
			<td><?php echo $uhid;?></td>
			<td><?php echo $ipd;?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $pat_info['sex'];?></td>
			<td><?php echo $d_name;?></td>
		</tr>
	</table>
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="ipd" value="<?php echo $ipd;?>" style="display:none;" />
	<input type="text" id="schedule_id" value="<?php echo $schedule_id;?>" style="display:none;" />
	
	<select id="template_id" onchange="scheduling()">
		<option>Select Template</option>
<?php
	$qry=mysqli_query($link,"SELECT `template_id`, `template_name` FROM `patient_ot_schedule_template` WHERE `user`='$_SESSION[emp_id]' ORDER BY `template_name` ASC");
	while($data=mysqli_fetch_array($qry))
	{
?>
		<option value="<?php echo $data["template_id"];?>"><?php echo $data["template_name"];?></option>
<?php
	}
?>
	</select>
	<br>
	<br>
	<div id="load_data" style="display:none;"></div>
	
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
<div id="loader" style="position:fixed;top:40%;left:55%;"></div>
<div id="gter" class="gritter-item" style="display:none;width:200px;">
	<div class="gritter-close" style="display:block;" onclick="$('.a').removeClass('clk');$('#gter').fadeOut(500)"></div>
	<span class="gt-title" style="font-size: 12px;font-family: verdana;font-weight: bold;padding-left: 10px;">Medicine Administor</span>
	<p id='fol_med' style="padding:6px;font-size:12px;"></p>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/loader.css" />
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
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 01,}});
		
		setTimeout(function()
		{
			scheduling();
		},300);
	});
	
	function scheduling()
	{
		$("#loader").show();
		$.post("pages/ot_scheduling_dashboard_data.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			schedule_id:$("#schedule_id").val().trim(),
			template_id:$("#template_id").val().trim(),
			usr:$("#user").text().trim(),
			type:"ot_scheduling",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").html(data);
			$("#load_data").fadeIn(1000);
			//$("html,body").animate({scrollTop: '200px'},800);
			
			if($("#schedule_id").val().trim()>0)
			{
				load_saved_resources();
			}
		})
	}
	
	function clear_error(id)
	{
		//$("#"+id).css("border","");
		$("#"+id).siblings(".select2-container").css({'border-color':'','box-shadow':''});
		if(id=="ot_dept_id")
		{
			if($("#"+id).val()=="others")
			{
				$("#tr_new_dept").slideDown();
				$("#new_dept").focus();
			}
			else
			{
				$("#tr_new_dept").hide();
				$("#procedure_id").select2("focus");
			}
		}
		if(id=="procedure_id")
		{
			if($("#procedure_id").val()=="others")
			{
				$("#proc_list_new").slideDown();
			}
			else
			{
				$("#proc_list_new").hide();
				$("#ot_date").focus();
			}
		}
		if(id=="anesthesia_id")
		{
			if($("#anesthesia_id").val()=="others")
			{
				$("#tr_anas_new").slideDown();
			}
			else
			{
				$("#tr_anas_new").hide();
				$("#diagnosis").focus();
			}
		}
	}
	
	function ot_resource_change()
	{
		$.post("pages/ot_scheduling_dashboard_data.php",
		{
			type:"load_ot_staff",
			resource_id:$("#resource_id").val(),
		},
		function(data,status)
		{
			$("#ot_staff_id").html(data);
		})
	}
	
	function add_res()
	{
		if($("#resource_id").val()==0)
		{
			$("#resource_id").select2("focus");
			return false;
		}
		if($("#ot_staff_id").val()==0)
		{
			$("#ot_staff_id").select2("focus");
			return false;
		}
		
		var item_chk=$("#item_list tr").length;
		if(!item_chk){ item_chk=0; }
		
		if(item_chk==0)
		{
			load_table($("#resource_id").val(),$("#ot_staff_id").val());
		}
		else
		{
			load_items($("#resource_id").val(),$("#ot_staff_id").val());
		}
	}
	function load_table(resource_id,ot_staff_id)
	{
		$.post("pages/ot_scheduling_dashboard_data.php",
		{
			type:"load_item_table",
		},
		function(data,status)
		{
			$("#load_resources").html(data);
			load_items(resource_id,ot_staff_id);
		})
	}
	function load_items(resource_id,ot_staff_id)
	{
		var resource_staff_dis=resource_id+"@#@"+ot_staff_id;
		
		var each_row=$(".each_row");
		for(var i=0;i<each_row.length;i++)
		{
			var tr_counter=each_row[i].value;
			
			var resource_staff=$("#resource_staff"+tr_counter).val();
			
			if(resource_staff==resource_staff_dis)
			{
				$("#load_resources").css({'opacity':'0.5'});
				$("#msg").html("<span style='color:red;font-weight:bold;'>Already Added</span>");
				var x=$("#load_resources").offset();
				var w=$("#msg").width()/2;
				$("#msg").css({'top':x.top,'left':'50%','margin-left':-w+'px'});
				$("#msg").fadeIn(500);
				setTimeout(function(){$("#msg").fadeOut(500,function(){$("#load_resources").css({'opacity':'1.0'}); })},2000);
				return false;
			}
		}
		
		var tr_counter=$("#tr_counter").val().trim();
		
		$.post("pages/ot_scheduling_dashboard_data.php",
		{
			type:"add_items",
			resource_id:resource_id,
			ot_staff_id:ot_staff_id,
			tr_counter:tr_counter,
		},
		function(data,status)
		{
			$("#item_footer").before(data);
			
			var next_tr_counter=parseInt($("#tr_counter").val())+1;
			$("#tr_counter").val(next_tr_counter);
			
			$("#list_all_test").animate({ scrollTop: 2900 });
			
			setTimeout(function(){
				$("#resource_id").select2("focus");
				$("#ot_staff_id").select2("val", "0");
			},100);
		})
	}
	function load_saved_resources()
	{
		$.post("pages/ot_scheduling_dashboard_data.php",
		{
			type:"load_saved_resources",
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			schedule_id:$("#schedule_id").val(),
		},
		function(data,status)
		{
			$("#load_resources").html(data);
		})
	}
	function remove_tr(val)
	{
		$("#tbl_tr"+val).remove();
	}
	
	function save_schedule()
	{
		if($("#ot_area_id").val()==0)
		{
			$("#ot_area_id").select2("focus");
			return false;
		}
		if($("#ot_type").val()==0)
		{
			$("#ot_type").select2("focus");
			return false;
		}
		if($("#ot_dept_id").val()==0)
		{
			$("#ot_dept_id").select2("focus");
			return false;
		}
		if($("#ot_dept_id").val()=="others" && $("#new_dept").val()=="")
		{
			$("#new_dept").focus();
			return false;
		}
		if($("#procedure_id").val()==0)
		{
			$("#procedure_id").select2("focus");
			return false;
		}
		if($("#procedure_id").val()=="others" && $("#new_procedure").val()=="")
		{
			$("#new_procedure").focus();
			return false;
		}
		if($("#ot_date").val()=="")
		{
			$("#ot_date").focus();
			return false;
		}
		if($("#anesthesia_id").val()==0)
		{
			$("#anesthesia_id").select2("focus");
			return false;
		}
		if($("#anesthesia_id").val()=="others" && $("#new_anesthesia").val()=="")
		{
			$("#new_anesthesia").focus();
			return false;
		}
		if($("#diagnosis").val()=="")
		{
			$("#diagnosis").focus();
			return false;
		}
		
		var all_resources="";
		
		var each_row=$(".each_row");
		for(var i=0;i<each_row.length;i++)
		{
			var tr_counter=each_row[i].value;
			
			var resource_id=$("#resource_id"+tr_counter).val();
			var ot_staff_id=$("#ot_staff_id"+tr_counter).val();
			
			all_resources=all_resources+"@$@"+resource_id+"#"+ot_staff_id;
		}
		
		if(all_resources=="")
		{
			$("#resource_id").select2("focus");
			return false;
		}
		
		$.post("pages/ot_scheduling_dashboard_data.php",
		{
			type:"save_schedule",
			ot_area_id:$("#ot_area_id").val(),
			ot_type:$("#ot_type").val(),
			ot_dept_id:$("#ot_dept_id").val(),
			new_dept:$("#new_dept").val(),
			procedure_id:$("#procedure_id").val(),
			new_procedure:$("#new_procedure").val(),
			ot_date:$("#ot_date").val(),
			start_time:$("#start_time").val(),
			end_time:$("#end_time").val(),
			request_doc_id:$("#request_doc_id").val(),
			anesthesia_id:$("#anesthesia_id").val(),
			new_anesthesia:$("#new_anesthesia").val(),
			diagnosis:$("#diagnosis").val(),
			ot_note:$("#ot_note").val(),
			all_resources:all_resources,
			
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			schedule_id:$("#schedule_id").val(),
		},
		function(data,status)
		{
			var res=data.split("@");
			
			if(res[2])
			{
				$("#schedule_id").val(res[2]);
			}
			if(res[0]==101)
			{
				$("#ot_print_btn").show();
			}
			
			bootbox.dialog({ message: "<h5>"+res[1]+"</h5> "});
			setTimeout(function(){
				bootbox.hideAll();
			},2000);
		})
	}
	
	
	function save_schedule_template()
	{
		if($("#ot_area_id").val()==0 && $("#ot_type").val()==0 && $("#ot_dept_id").val()==0 && $("#procedure_id").val()==0 && $("#anesthesia_id").val()==0 && $("#diagnosis").val()=="" && $("#ot_note").val()=="")
		{
			bootbox.dialog({ message: "<h4>Empty Template</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 2000);
			return false;
		}
		
		bootbox.dialog({
			message: "Template Name:<input type='text' id='template_name' autofocus />",
			title: "Case Summary Template",
			buttons: {
				main: {
					label: "<i class='icon-ok'></i>Save",
					className: "btn-primary",
					callback: function() {
						if($("#template_name").val()!="")
						{
							save_schedule_template_ok();
						}else
						{
							bootbox.alert("Template name cannot blank");
						}
					}
				}
			}
		});
		
		if($("#template_id").val()>0)
		{
			$("#template_name").val($("#template_id option:selected").text());
		}
	}
	
	function save_schedule_template_ok()
	{
		$("#loader").show();
		$.post("pages/ot_scheduling_dashboard_data.php",
		{
			type:"save_schedule_template",
			uhid:$("#uhid").val(),
			ipd_id:$("#ipd").val(),
			
			template_id:$("#template_id").val(),
			template_name:$("#template_name").val(),
			
			ot_area_id:$("#ot_area_id").val(),
			ot_type:$("#ot_type").val(),
			ot_dept_id:$("#ot_dept_id").val(),
			ot_dept_id:$("#ot_dept_id").val(),
			new_dept:$("#new_dept").val(),
			procedure_id:$("#procedure_id").val(),
			new_procedure:$("#new_procedure").val(),
			anesthesia_id:$("#anesthesia_id").val(),
			new_anesthesia:$("#new_anesthesia").val(),
			diagnosis:$("#diagnosis").val(),
			ot_note:$("#ot_note").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			var res=data.split("@");
			bootbox.dialog({ message: "<h4>"+res[1]+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				if(res[0]==101)
				{
					window.location.reload(true);
				}
			}, 2000);
		})
	}
	
	function ot_print()
	{
		var url="pages/ot_scheduling_print.php";
		
		var uhid=$("#uhid").val();
		url=url+"?v="+btoa(1234567890)+"&uhid="+btoa(uhid);
		
		var ipd=$("#ipd").val();
		url=url+"&ipd="+btoa(ipd);
		
		var schedule_id=$("#schedule_id").val();
		url=url+"&sched="+btoa(schedule_id);
		
		var user=$("#user").text().trim();
		url=url+"&user="+btoa(user);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
