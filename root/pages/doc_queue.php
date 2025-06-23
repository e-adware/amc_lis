<?php
$branch_str=" AND `branch_id`='$p_info[branch_id]'";

$element_style="display:none;";
$branch_num=mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
if($branch_num>1)
{
	$element_style="display:;";
}

$emp_id=$_SESSION["emp_id"];

$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid`,`Name`,`dept_id` FROM `consultant_doctor_master` WHERE `emp_id`='$emp_id' "));
$consultantdoctorid=$con_doc["consultantdoctorid"];
$dept_id=$con_doc["dept_id"];

if($p_info["levelid"]==5)
{
	$_SESSION["levelid"]=5;
	$qry=mysqli_query($link, "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `dept_id`='$dept_id'");
}
if($p_info["levelid"]==40)
{
	$qry=mysqli_query($link, "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master`");
}
//print_r($_SESSION);
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="">
		<table class="table table-condensed table-bordered">
		 <tr>
			<td>
				<label><b>Unit No.</b></label>
				<input type="text" name="uhid" id="uhid" class="span2" onkeyup="search_patient_list(this,event)">
			</td>
			<td>
				<label><b>Bill No.</b></label>
				<input type="text" name="opd_id" id="opd_id" class="span2" onkeyup="search_patient_list(this,event)" autofocus>
			</td>
			<td>
				<label><b>Patient Name</b></label>
				<input type="text" name="pat_name" id="pat_name" class="span2" onkeyup="search_patient_list(this,event)">
			</td>
			<td>
				<label><b>Consultant Doctor</b></label>
				<select id="consultantdoctorid" onChange="opd_patient_list()">
			<?php
				if($p_info["levelid"]==40 || $dept_id==37)
				{
			?>
					<option value="0">All</option>
			<?php
				}
			?>
			<?php
				while($data=mysqli_fetch_array($qry))
				{
					if($consultantdoctorid==$data["consultantdoctorid"]){ $doc_sel="selected"; }else{ $doc_sel=""; }
					if($dept_id==37){ $doc_sel=""; }
					echo "<option value='$data[consultantdoctorid]' $doc_sel>$data[Name]</option>";
				}
			?>
				</select>
				<input type="hidden" id="dept_id" value="<?php echo $dept_id; ?>">
			</td>
			<td>
				<label><b>Date</b></label>
				<input type="text" name="appointment_date" id="appointment_date" class="datepicker span2"  value="<?php echo date("Y-m-d"); ?>" onChange="opd_patient_list()" readonly>
			
				<select id="branch_id" class="span2" onChange="opd_patient_list()" style="<?php echo $element_style; ?>">
				<?php
					$qry=mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
					while($data=mysqli_fetch_array($qry))
					{
						if($data["branch_id"]==$p_info["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
						echo "<option value='$data[branch_id]' $branch_sel>$data[name]</option>";
					}
				?>
				</select>
				<!--<button class="btn btn-search" id="search_btn" onFocus="opd_patient_list()" style="margin-bottom: 10px;"><i class="icon-search"></i> Search</button>-->
			</td>
		  </tr>
	</table>
	</div>
	<div id="load_pat" style=""></div>
	<div id="load_data" style=""></div>
</div>

<!-- Modal -->
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#previous_record" id="previous_record_btn" style="display:none;">Open Modal</button>
<div id="previous_record" class="modal fade" role="dialog" class="modal fade in" style="display:none;">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" style="text-align: center;">Previous Record</h4>
			</div>
			<div class="modal-body previous_record_body">
				<div id="load_each_previous_record"></div>
			</div>
			<div class="modal-footer" style="text-align: center !important;">
				<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>

<script type='text/javascript'>
	$(document).ready(function()
	{
		$("#consultantdoctorid").select2({ theme: "classic" });
		
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
		opd_patient_list();
	});
	
	function hid_div(e)
	{
		if(e.which==27)
		{
			opd_patient_list();
		}
	}
	
	var _changeInterval = null;
	function search_patient_list(dis,e)
	{
		clearInterval(_changeInterval)
		_changeInterval = setInterval(function() {
			// Typing finished, now you can Do whatever after 2 sec
			clearInterval(_changeInterval);
			opd_patient_list();
		}, 300);
	}
	
	function opd_patient_list()
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"opd_patient_list",
			uhid:$("#uhid").val(),
			opd_id:$("#opd_id").val(),
			pat_name:$("#pat_name").val(),
			appointment_date:$("#appointment_date").val(),
			dept_id:$("#dept_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").slideUp(300);
			$("#load_pat").slideDown(300).html(data);
		})
	}
	
	function load_clinical_data(uhid,opd_id)
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"load_clinical_data",
			uhid:uhid,
			opd_id:opd_id,
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_pat").slideUp(300);
			$("#load_data").slideDown(300).html(data);
			
			if($("#lavel_id").val()==5)
			{
				opd_clinical_form(1);
			}
			else
			{
				opd_clinical_form(20);
			}
		})
	}
	
	function opd_clinical_form(val)
	{
		$("#linear_loading_div").show();
		$(".tab-pane").html("");
		$.post("pages/doc_queue_data.php",
		{
			type:"opd_clinical_form"+val,
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#linear_loading_div").hide();
			
			$("#tab"+val).html(data);
			
			$("#tab_id").val(val);
			
			setTimeout(function(){
				if(val==1)
				{
					$("#case_history").focus();
				}
				if(val==2)
				{
					//$("#diagnosis").focus();
					$("#case_history_eye").focus();
				}
				if(val==3)
				{
					$(".datepicker_max").datepicker({
						changeMonth:true,
						changeYear:true,
						dateFormat: 'yy-mm-dd',
						maxDate: '0',
						yearRange: "-10:+0",
					});
					$(".datepicker_min").datepicker({
						changeMonth:true,
						changeYear:true,
						dateFormat: 'yy-mm-dd',
						minDate: '0',
						yearRange: "-0:+10",
					});
				}
				if(val==4)
				{
					$("#testname").focus();
					
					load_selected_tests();
				}
				if(val==5)
				{
					$("#medi").focus();
					
					load_selected_medicines();
				}
				if(val==6)
				{
					$("#advice_note").focus();
					
					load_selected_medicines();
				}
				if(val==7)
				{
					$("#revisit_id").focus();
				}
				if(val==9)
				{
					$("#propose_procedure").focus();
				}
				
				if(val==20)
				{
					$("#weight").focus();
				}
			},300);
		})
	}
	
	function next_tab(val)
	{
		if(val==0)
		{
			var next_id=parseInt($("#tab_id").val());
		}
		if(val==1)
		{
			var next_id=parseInt($("#tab_id").val())-1;
			
			if($("#opd_clinical_btn"+next_id+":visible").length==0)
			{
				next_id=9;
			}
		}
		if(val==2)
		{
			var next_id=parseInt($("#tab_id").val())+1;
			
			if($("#opd_clinical_btn"+next_id+":visible").length==0)
			{
				next_id=1;
			}
		}
		
		$("#opd_clinical_btn"+next_id).click();
	}
	
	// Vitals Start
	function sav_vitals()
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"sav_vitals",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			
			//Vitals
			weight:$("#weight").val(),
			height:$("#height").val(),
			BMI_1:$("#BMI_1").val(),
			BMI_2:$("#BMI_2").val(),
			temp:$("#temp").val(),
			pulse:$("#pulse").val(),
			spo2:$("#spo2").val(),
			systolic:$("#systolic").val(),
			diastolic:$("#diastolic").val(),
			RR:$("#RR").val(),
			fbs:$("#fbs").val(),
			rbs:$("#rbs").val(),
			note:$("#note").val(),
			
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				//opd_clinical_form(20);
			}, 1000);
		})
	}
	// Vitals End
	
	// Observation Start
	
	var _changeInterval = null;
	function case_history_up(val,e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_case_history(val,e);
			}, 300);
		}
	}
	function load_case_history(val,e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_case_history",
			val:val,
		},
		function(data,status)
		{
			$("#case_history_list").html(data);
		})
	}
	
	function weight_up()
	{
		calculate_bmi();
	}
	function height_up()
	{
		calculate_bmi();
	}
	function calculate_bmi()
	{
		var weight=parseInt($("#weight").val());
		if(!weight){ weight=0; }
		
		var height=parseInt($("#height").val());
		if(!height){ height=0; }
		
		height=height/100;
		
		if(weight>0 && height>0)
		{
			var bmi=(weight/(height*height));
			var bmi = bmi.toFixed(2);
			var bmi = bmi.split(".");
			$("#BMI_1").val(bmi[0]);
			$("#BMI_2").val(bmi[1]);
		}
		else
		{
			$("#BMI_1").val("");
			$("#BMI_2").val("");
		}
	}
	
	
	function sav_observation()
	{
		if($("#case_history").val().trim()=="")
		{
			$("#case_history").focus();
			return false;
		}
		
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"sav_observation",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			
			case_history:$("#case_history").val(),
			
			//General Examination
			pallor:$("#pallor").val(),
			edema:$("#edema").val(),
			icterus:$("#icterus").val(),
			dehydration:$("#dehydration").val(),
			cyanosis:$("#cyanosis").val(),
			lymph_node:$("#lymph_node").val(),
			gcs:$("#gcs").val(),
			
			//Systemic Examination
			chest:$("#chest").val(),
			cvs:$("#cvs").val(),
			cns:$("#cns").val(),
			pa:$("#pa").val(),
			
			diagnosis:$("#diagnosis_observ").val(),
			
			//Vitals
			weight:$("#weight").val(),
			height:$("#height").val(),
			BMI_1:$("#BMI_1").val(),
			BMI_2:$("#BMI_2").val(),
			temp:$("#temp").val(),
			pulse:$("#pulse").val(),
			spo2:$("#spo2").val(),
			systolic:$("#systolic").val(),
			diastolic:$("#diastolic").val(),
			RR:$("#RR").val(),
			fbs:$("#fbs").val(),
			rbs:$("#rbs").val(),
			note:$("#note").val(),
			
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				opd_clinical_form(1);
			}, 1000);
		})
	}
	// Observation End
	
	// Diagnosis Start
	var _changeInterval = null;
	function diagnosis_up(val,e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_diagnosis(val,e);
			}, 300);
		}
	}
	function load_diagnosis(val,e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_diagnosis",
			val:val,
		},
		function(data,status)
		{
			$("#diagnosis_list").html(data);
		})
	}
	function save_diagnosis()
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"save_diagnosis",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			diagnosis:$("#diagnosis").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
		})
	}
	// Diagnosis End
	
	// Antenatal Start
	function last_menstrual_period_change()
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"last_menstrual_period_change",
			last_menstrual_period:$("#last_menstrual_period").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			if(data)
			{
				var res=data.split("@$@");
				$("#est_delivery_date").val(res[0]);
				$("#gestational_age").val(res[1]);
			}
		})
	}
	function est_delivery_date_change()
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"est_delivery_date_change",
			est_delivery_date:$("#est_delivery_date").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			
			if(data)
			{
				var res=data.split("@$@");
				$("#last_menstrual_period").val(res[0]);
				$("#gestational_age").val(res[1]);
			}
		})
	}
	function sav_antenatal()
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"sav_antenatal",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			last_menstrual_period:$("#last_menstrual_period").val(),
			est_delivery_date:$("#est_delivery_date").val(),
			gestational_age:$("#gestational_age").val(),
			gestational_age_usg:$("#gestational_age_usg").val(),
			fundal_height:$("#fundal_height").val(),
			presentation:$("#presentation").val(),
			fetal_heart_rate:$("#fetal_heart_rate").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
		})
	}
	// Antenatal End
	
	// Investigation Start
	function ref_load_focus()
	{
		$("#ref_doc").fadeIn(500);
		$("#testname").select();
	}
	var doc_tr=1;
	var doc_sc=0;
	function ref_load_refdoc(val,e)
	{
		$("#r_doc").css({"border-color":""});
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
				$("#ref_doc").fadeIn(500);
				$.post("pages/doc_queue_data.php",
				{
					type:"search_test",
					test:val,
					uhid:$("#sel_uhid").val(),
					opd_id:$("#sel_opd_id").val(),
					consultantdoctorid:$("#consultantdoctorid").val(),
					branch_id:$("#branch_id").val(),
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
			var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.split("#");
			var testid=docs[1].trim();
			var test_naam=docs[2].trim();
			var rate=docs[3].trim();
			
			test_load(testid,test_naam,rate);
		}
	}
	
	function new_test()
	{
		$("#testname").hide();
		$("#save_test_btn").hide();
		$("#new_test_btn").hide();
		
		$("#new_test").show().focus();
		$("#save_new_test_btn").show();
		$("#new_test_can_btn").show();
		
		$("#testid").val(0);
		$("#new_test").val("");
	}
	function can_test()
	{
		$("#testname").show().focus();
		$("#save_test_btn").show();
		$("#new_test_btn").show();
		
		$("#new_test").hide();
		$("#save_new_test_btn").hide();
		$("#new_test_can_btn").hide();
		
		$("#testid").val(0);
		$("#new_test").val("");
	}
	
	function test_load(testid,test_naam,rate)
	{
		//alert(testid+" "+test_naam+" "+rate);
		$("#testid").val(testid);
		$("#testname").val(test_naam);
		$("#doc_info").html("");
		$("#ref_doc").fadeOut(500);
		
		$("#save_test_btn").focus();
	}
	function save_new_test()
	{
		if($("#new_test").val()=="")
		{
			$("#new_test").focus();
			return false;
		}
		
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"save_new_test",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			testname:$("#new_test").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ size: 'small', message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				load_selected_tests();
			}, 1000);
			setTimeout(function()
			{
				can_test();
			}, 1100);
		})
	}
	function save_test()
	{
		if($("#testname").val()=="" || $("#testid").val()==0)
		{
			$("#testname").focus();
			return false;
		}
		
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"save_test",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			testid:$("#testid").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ size: 'small', message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				load_selected_tests();
			}, 1000);
			setTimeout(function()
			{
				$("#testname").focus();
			}, 1100);
		})
	}
	function load_selected_tests()
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"load_selected_tests",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			testid:$("#testid").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#test_dd").html(data);
			
			$("#testid").val("0");
			$("#testname").val("");
		})
	}
	
	function add_test(testid,testname)
	{
		$("#testid").val(testid);
		$("#testname").val(testname);
		
		save_test();
	}
	function del_test(slno)
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to remove this test?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Ok',
					className: "btn btn-danger",
					callback: function()
					{
						$("#loader").show();
						$.post("pages/doc_queue_data.php",
						{
							type:"del_test",
							slno:slno,
							uhid:$("#sel_uhid").val(),
							opd_id:$("#sel_opd_id").val(),
						},
						function(data,status)
						{
							$("#loader").hide();
							bootbox.dialog({ message: "<h4>"+data+"</h4>"});
							setTimeout(function()
							{
								bootbox.hideAll();
								//opd_clinical_form(4);
								load_selected_tests();
							}, 1000);
						})
					}
				}
			}
		});
	}
	// Investigation End
	
	// Medication Start
	function focus_medi_list()
	{
		//$("#med_div").fadeIn(500);
		$("#medi").select();
	}
	var med_tr=1;
	var med_sc=0;
	function load_medi_list(val,e)
	{
		$("#med_dos").hide();
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#med_div").html("<img src='../images/ajax-loader.gif' />");
				$("#med_div").fadeIn(500);
				$.post("pages/doc_queue_data.php",
				{
					val:val,
					type:"load_medicine"
				},
				function(data,status)
				{
					$("#med_div").html(data);
					med_tr=1;
					med_sc=0;
				})	
			}
			else if(unicode==40)
			{
				var chk=med_tr+1;
				var cc=document.getElementById("med"+chk).innerHTML;
				if(cc)
				{
					med_tr=med_tr+1;
					$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var med_tr1=med_tr-1;
					$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=med_tr%1;
					if(z3==0)
					{
						$("#med_div").scrollTop(med_sc)
						med_sc=med_sc+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=med_tr-1;
				var cc=document.getElementById("med"+chk).innerHTML;
				if(cc)
				{
					med_tr=med_tr-1;
					$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var med_tr1=med_tr+1;
					$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=med_tr%1;
					if(z3==0)
					{
						med_sc=med_sc-30;
						$("#med_div").scrollTop(med_sc)
					}
				}
			}
		}
		else
		{
			var docs=document.getElementById("mdname"+med_tr).innerHTML.split("#");
			var doc_naam=docs[2].trim()
			$("#medi").val(doc_naam);
			$("#medid").val(docs[1]);
			$("#unit").val(docs[3]);
			var d_in=docs[5];
			//$("#doc_mark").val(docs[5]);
			$("#med_info").html(d_in);
			$("#med_info").fadeIn(500);
			$("#g_name").show();
			select_med(docs[1],docs[2],docs[3],docs[4]);
		}
	}
	function select_med(id,name,typ,gen)
	{
		//alert(id+' '+name+' '+typ+' '+gen);
		$("#medi").val(name);
		$("#medid").val(id);
		$("#med_info").html("");
		$("#med_div").fadeOut(500);
		$("#unit").val(typ);
		
		$("#dosage").focus();
	}
	
	var _changeInterval = null;
	function new_medi_up(e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else if(e.which==13 && $("#new_medi").val().trim()!="")
		{
			$("#dosage").focus();
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_new_medi(e);
			}, 300);
		}
	}
	function load_new_medi(e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_new_medi",
			val:$("#new_medi").val(),
		},
		function(data,status)
		{
			$("#new_medi_list").html(data);
		})
	}
	
	var _changeInterval = null;
	function dosage_up(e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else if(e.which==13 && $("#dosage").val().trim()!="")
		{
			$("#frequency").focus();
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_dose(e);
			}, 300);
		}
	}
	function load_dose(e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_dose",
			val:$("#dosage").val(),
		},
		function(data,status)
		{
			$("#dosage_list").html(data);
		})
	}
	
	var _changeInterval = null;
	function frequency_up(e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else if(e.which==13 && $("#frequency").val().trim()!="")
		{
			$("#duration").focus();
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_frequency(e);
			}, 300);
		}
	}
	function load_frequency(e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_frequency",
			val:$("#frequency").val(),
		},
		function(data,status)
		{
			$("#frequency_list").html(data);
		})
	}
	
	var _changeInterval = null;
	function duration_up(e)
	{
		if(e.which==38 || e.which==40)
		{
			return false;
		}
		else if(e.which==13 && $("#duration").val().trim()!="")
		{
			$("#sav_medi").focus();
		}
		else
		{
			clearInterval(_changeInterval)
			_changeInterval = setInterval(function() {
				// Typing finished, now you can Do whatever after 2 sec
				clearInterval(_changeInterval);
				load_duration(e);
			}, 300);
		}
	}
	function load_duration(e)
	{
		$.post("pages/doc_queue_data.php",
		{
			type:"load_duration",
			val:$("#duration").val(),
		},
		function(data,status)
		{
			$("#duration_list").html(data);
		})
	}
	
	function ph_quantity(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$("#sav_medi").focus();
		}
	}
	function new_medi()
	{
		$("#medi").hide();
		$("#new_btn").hide();
		$("#can_btn").show();
		$("#new_medi").show().val('').focus();
		$("#medid").val('');
	}
	function can_medi()
	{
		$("#new_medi").hide();
		$("#can_btn").hide();
		$("#new_btn").show();
		$("#medi").show().val('').focus();
		$("#medid").val('');
	}
	function tab(id,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13 && $("#new_medi").val().trim()!="")
		{
			$("#dosage").focus();
		}
	}
	function sav_medi()
	{
		if($("#new_medi").val()=="" && ($("#medi").val().trim()=="" || $("#medid").val().trim()=="" || $("#medid").val().trim()=="0"))
		{
			can_medi();
			$("#medi").focus();
			return false;
		}
		if($("#dosage").val().trim()=="")
		{
			$("#dosage").focus();
			//return false;
		}
		
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"save_medicine",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			item_id:$("#medid").val(),
			new_medi:$("#new_medi").val(),
			dosage:$("#dosage").val(),
			frequency:$("#frequency").val(),
			duration:$("#duration").val(),
			ph_quantity:$("#ph_quantity").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				//opd_clinical_form(5);
				load_selected_medicines();
				$("#medi").focus();
			}, 1000);
		})
	}
	function load_selected_medicines()
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"load_selected_medicines",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_selected_medicines").html(data);
			
			$("#medi").val("");
			$("#new_medi").val("");
			$("#medid").val("0");
			$("#mediname").val("");
			$("#dosage").val("");
			$("#frequency").val("");
			$("#duration").val("");
			$("#ph_quantity").val("");
		})
	}
	function add_medicine(item_id,item_name,dosage,frequency,duration,quantity)
	{
		$("#medi").val(item_name);
		$("#new_medi").val("");
		$("#medid").val(item_id);
		$("#mediname").val(item_name);
		$("#dosage").val(dosage);
		$("#frequency").val(frequency);
		$("#duration").val(duration);
		$("#ph_quantity").val(quantity);
	}
	function del_medicine(slno)
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to remove this medicine?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Ok',
					className: "btn btn-danger",
					callback: function()
					{
						$("#loader").show();
						$.post("pages/doc_queue_data.php",
						{
							type:"del_medicine",
							slno:slno,
							uhid:$("#sel_uhid").val(),
							opd_id:$("#sel_opd_id").val(),
						},
						function(data,status)
						{
							$("#loader").hide();
							bootbox.dialog({ message: "<h4>"+data+"</h4>"});
							setTimeout(function()
							{
								bootbox.hideAll();
								//opd_clinical_form(5);
								load_selected_medicines();
							}, 1000);
						})
					}
				}
			}
		});
	}
	// Medication End
	
	// Advise Note Start
	function save_advice_note()
	{
		if($("#advice_note").val().trim()=="")
		{
			bootbox.dialog({ message: "<h4>Nothing to save</h4>"});
			setTimeout(function()
			{
				$("#revisit_id").focus();
				bootbox.hideAll();
			}, 1000);
			return false;
		}
		
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"save_advice_note",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			
			advice_note:$("#advice_note").val(),
			
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				//opd_clinical_form(7);
			}, 1000);
		})
	}
	// Advise Note End
	
	// Re-visit Advice Start
	function save_revisit()
	{
		if($("#revisit_id").val()=="0" && $("#revisit_advice_note").val().trim()=="")
		{
			bootbox.dialog({ message: "<h4>Nothing to save</h4>"});
			setTimeout(function()
			{
				$("#revisit_id").focus();
				bootbox.hideAll();
			}, 1000);
			return false;
		}
		
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"save_revisit",
			uhid:$("#sel_uhid").val(),
			opd_id:$("#sel_opd_id").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			branch_id:$("#branch_id").val(),
			
			revisit_id:$("#revisit_id").val(),
			revisit_advice_note:$("#revisit_advice_note").val(),
			
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: "<h4>"+data+"</h4>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				//opd_clinical_form(7);
			}, 1000);
		})
	}
	// Re-visit Advise End
	
	function load_each_previous_record(uhid,opd_id)
	{
		$("#loader").show();
		$.post("pages/doc_queue_data.php",
		{
			type:"load_each_previous_record",
			uhid:uhid,
			opd_id:opd_id,
		},
		function(data,status)
		{
			$("#loader").hide();
			
			$("#load_each_previous_record").html(data);
			$("#previous_record_btn").click();
		})
	}
	
	function view_test_result(uhid,opd_id)
	{
		var user=$("#user").text();
		var tst=$("#test_print").val();
		
		var url="pages/pathology_report_print.php?uhid="+btoa(uhid)+"&opd_id="+btoa(opd_id)+"&ipd_id="+btoa("")+"&batch_no="+btoa(1)+"&tests="+btoa(tst)+"&hlt="+btoa(tst)+"&user="+btoa(user)+"&view="+btoa(1)+"&opd_clinic="+btoa(1);
		var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
	}
	
	function print_prescription()
	{
		var url="pages/opd_prescription.php";
		//var url="pages/prescription_rpt_new.php";
		
		var uhid=$("#sel_uhid").val();
		url=url+"?v="+btoa(1234567890)+"&uhid="+btoa(uhid);
		
		var opd_id=$("#sel_opd_id").val();
		url=url+"&opdid="+btoa(opd_id);
		
		var user=$("#user").text().trim();
		url=url+"&user="+btoa(user);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
</script>
<style>
#med_div, #ref_doc
{
	width: 750px !important;
}

#previous_record
{
	top: 0px !important;
	width: 90%;
	margin-left: -44%;
}
.previous_record_body
{
	max-height: 600px;
}

.span9 {
	width: 70%;
}
.span3 {
	width: 25%;
}
</style>
