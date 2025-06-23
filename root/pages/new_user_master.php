<?php
$branch_str=" AND `branch_id`='$p_info[branch_id]'";
$element_style="display:none";
$element_dis="disabled";
if($p_info["levelid"]==1)
{
	$branch_str="";
	$element_style="";
	$element_dis="";
}

if($_GET['user'])
$u=base64_decode($_GET['user']);
else
$u="";
?>
<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"> User Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<p class="text-right">
		<select id="branch_id_main" class="span2" onchange="branch_change()" style="<?php echo $element_style; ?>">
		<?php
			$qry=mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
			while($data=mysqli_fetch_array($qry))
			{
				if($data["branch_id"]==$p_info["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
				echo "<option value='$data[branch_id]' $branch_sel>$data[name]</option>";
			}
		?>
		</select>
		<button class="btn btn-search" style="margin-top: -10px;" onclick="popitup('pages/all_user_list.php');"><i class="icon-search"></i> View</button>
	</p>
	<input type="hidden" id="uhid" value="<?php echo $u;?>">
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Enter Employee Code</th>
			<td>
				<input type="text" name="pat_uhid" id="pat_uhid" class="precv" placeholder="Search" onKeyUp="sel_pat_bill(this.value,event,'pat_uhid')">
			</td>
			<th>Enter Employee Name</th>
			<td>
				<input type="text" name="pat_name" id="pat_name" class="precv" placeholder="Search" onKeyUp="sel_pat_bill(this.value,event,'pat_name')" autofocus >
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<div id="bal_pat" style="max-height:200px;overflow:scroll;overflow-x:hidden;display:none;" align="center">
				</div>
			</td>
		</tr>
	</table>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th style="display:none;">Employee Code</th>
			<td style="display:none;">
				<input type="text" id="emply_code" readonly>
			</td>
			<th style="width: 20%;">Employee Type <b style="color:#f00;">*</b></th>
			<td colspan="3">
				<select id="emply_type" onChange="load_main_info(this.value)">
					<option value="0">Select</option>
				<?php
					$lvl_qry=mysqli_query($link," SELECT * FROM `level_master` order by `name` ");
					while($lvl=mysqli_fetch_array($lvl_qry))
					{
						echo "<option value='$lvl[levelid]'>$lvl[name]</option>";
					}
				?>
				</select>
				<select id="centre_no" style="display:none;">
					<option value="0">Select Centre</option>
				</select>
				<input type="hidden" id="target_amount" value="" placeholder="Target Amount">
			</td>
		</tr>
		<tr class="emp">
			<th>Name <b style="color:#f00;">*</b></th>
			<td>
				<input type="text" class="capital" id="emply_name">
			</td>
			<th>Gender <b style="color:#f00;">*</b></th>
			<td>
				<select id="emply_sex">
					<option value="0">Select</option>
					<!--<option value="Male">Male</option>
					<option value="Female">Female</option>
					<option value="Other">Other</option>-->
				<?php
					$qry=mysqli_query($link," SELECT `gender_id`, `sex` FROM `gender_master` ");
					while($data=mysqli_fetch_array($qry))
					{
						echo "<option value='$data[sex]'>$data[sex]</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr class="emp">
			<th>DOB <b style="color:#f00;">*</b></th>
			<td>
				<input type="text" class="datepicker" id="emply_dob" onchange="calculate_age(this.value)" readonly style="width: 80px;">
				<span class="side_name_age">Age</span>
				<input type="text" class="" id="emply_age" onkeyup="age_up(event)" style="width: 70px;margin-left: 38px;">
			</td>
			<th>Email</th>
			<td>
				<input type="text" id="emply_email">
			</td>
		</tr>
		<tr class="emp">
			<th>Phone</th>
			<td>
				<input type="text" class="" id="emply_phone" maxlength="10">
			</td>
			<th>Address</th>
			<td>
				<input type="text" class="capital" id="emply_address">
			</td>
		</tr>
		<tr class="doc">
			<th>Speciality <b style="color:#f00;">*</b></th>
			<td>
				<select id="emply_speclty">
					<option value="0">Select</option>
					<?php
						//~ $spclt_qry=mysqli_query($link," SELECT * FROM `doctor_specialist_list` order by `name` ");
						//~ while($spclt=mysqli_fetch_array($spclt_qry))
						//~ {
							//~ echo "<option value='$spclt[speciality_id]'>$spclt[name]</option>";
						//~ }
						$spclt_qry=mysqli_query($link," SELECT * FROM `doctor_specialist_list` order by `name` ");
						while($spclt=mysqli_fetch_array($spclt_qry))
						{
							echo "<option value='$spclt[speciality_id]'>$spclt[name]</option>";
						}
					?>
				</select>
			</td>
			<td colspan="2">
				<span id="appr_result" style="display:none;">
					<label><input type="checkbox" id="res_approve">  <b>Result approval</b></label>
				</span>
			</td>
		</tr>
		<tr class="doc">
			<th>Qualification <b style="color:#f00;">*</b></th>
			<td>
				<input type="text" class="capital" id="emply_edu">
			</td>
			<th>Designation <b style="color:#f00;">*</b></th>
			<td>
				<input type="text" class="capital" id="emply_design">
			</td>
		</tr>
		<tr class="nurse">
			<th>Registration No</th>
			<td colspan="3">
				<input type="text" class="capital" id="emply_regd_no">
			</td>
		</tr>
		<tr class="emp">
			<th>Status</th>
			<td colspan="1">
				<select id="emply_status">
					<option value="0">Active</option>
					<option value="1">In-active</option>
				</select>
			</td>
			<th style="<?php echo $element_style; ?>">Branch</th>
			<td colspan="1" style="<?php echo $element_style; ?>">
				<select id="branch_id" onkeyup="branch_up(event)" <?php echo $element_dis; ?>>
			<?php
				$qry=mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
				while($data=mysqli_fetch_array($qry))
				{
					if($data["branch_id"]==$p_info["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
					echo "<option value='$data[branch_id]' $branch_sel>$data[name]</option>";
				}
			?>
				</select>
			</td>
		</tr>
		<tr class="emp">
			<td colspan="4">
				<center>
					<button class="btn btn-save" value="Save" id="save_btn" onClick="save_user(this.value)"><i class="icon-save"></i> Save</button>
					<button class="btn btn-new" value="New" id="new_btn" onClick="window.location.reload(true)"><i class="icon-edit"></i> New</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_main_info">
		
	</div>
	
	<p class="text-center" style="display:none;" id="main_button">
		<!--<button class="btn btn-large btn_tab Personal" onClick="load_fields('Personal')">Personal</button>
		<button class="btn btn-large btn_tab Official" onClick="load_fields('Official')">Official</button>-->
		<!--<button class="btn btn-large btn_tab Salary" onClick="load_fields('Salary')">Salary</button>
		<button class="btn btn-large btn_tab PFESI" onClick="load_fields('PFESI')">Deduction</button>
		<button class="btn btn-large btn_tab salary_generation" onClick="load_fields('salary_generation')">Salary Generation</button>
		<button class="btn btn-large btn_tab Documents" onClick="load_fields('Documents')">Documents</button>-->
		<!--<button class="btn btn-large btn_tab PFESI" onClick="load_fields('PFESI')">PF / ESI</button>-->
		<button class="btn btn-large btn_tab Application_Access" onClick="load_fields('Application_Access')">Application Access </button>
	</p>
	<div id="load_emply_info">
		
	</div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).on('blur', ".capital", function () {
		$(this).val(function (_, val) {
			return val.toUpperCase();
		});
	});
	$(document).ready(function(){
		load_centres();
		load_id();
		$(".emp").hide();
		$(".doc").hide();
		$(".nurse").hide();
		//$(".cashier").hide();
		//load_fields("Personal");
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '-15Y',
			yearRange: "-150:+0",
			defaultDate:'2000-01-01',
		});
		
		if($("#uhid").val().trim()!="")
		{
			load_all_info($("#uhid").val().trim());
		}
		
		/////////////////////////////////////
		$("#emply_type").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				{
					$(this).css("border","2px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#emply_name").focus();
				}
			}
		});
		$("#emply_speclty").change(function(e)
		{
			$(this).css("border","");
		});
		$("#emply_speclty").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				{
					$(this).css("border","2px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#emply_edu").focus();
				}
			}
		});
		$("#emply_edu").keyup(function(e)
		{
			$(this).css("border","");
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$(this).css("border","2px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#emply_design").focus();
				}
			}
		});
		$("#emply_design").keyup(function(e)
		{
			$(this).css("border","");
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$(this).css("border","2px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#emply_regd_no").focus();
				}
			}
		});
		$("#emply_regd_no").keyup(function(e)
		{
			$(this).css("border","");
			if(e.keyCode==13)
			{
				$("#emply_status").focus();
			}
		});
		$("#emply_name").keyup(function(e)
		{
			var val=$(this).val();
			var nval=val.toUpperCase();
			$(this).val(nval);
			
			$(this).css("border","");
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$(this).css("border","2px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#emply_sex").focus();
				}
			}
		});
		$("#emply_sex").change(function(e)
		{
			$(this).css("border","");
		});
		$("#emply_sex").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				{
					$(this).css("border","2px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#emply_dob").focus();
				}
			}
		});
		$("#emply_dob").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#emply_email").focus();
			}
		});
		
		$("#emply_email").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#emply_phone").focus();
			}
		});
		$("#emply_phone").keyup(function(e)
		{
			$(this).css("border","");
			
			numeric($("#emply_phone").val(),"emply_phone");
			
			if(e.keyCode==13)
			{
				if($(this).val()!="" && $(this).val().length!=10)
				{
					$(this).css("border","2px solid #f00").focus();
				}
				else
				{
					$("#emply_address").focus();
				}
			}
		});
		$("#emply_address").keyup(function(e)
		{
			$(this).css("border","");
			
			if(e.keyCode==13)
			{
				if($('#emply_speclty:visible').length == 0)
				{
					$("#emply_status").focus();
				}
				else
				{
					$("#emply_speclty").focus();
				}
			}
		});
		$("#emply_status").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#save_btn").focus();
			}
		});
	});
	
	function branch_change()
	{
		load_centres();
	}
	
	function load_centres()
	{
		$.post("pages/user_master_data.php",
		{
			type:"load_centres",
			branch_id:$("#branch_id_main").val(),
		},
		function(data,status)
		{
			$("#centre_no").html(data);
		})
	}
	
	function age_up(e)
	{
		numeric($("#emply_age").val(),"emply_age");
		
		calculate_dob($("#emply_age").val());
		
		$("#emply_age").css("border","");
		
		if(e.keyCode == 13)
		{
			if($("#emply_age").val()=="" || $("#emply_age").val()==0)
			{
				$("#emply_age").css("border","2px solid #f00").focus();
				return false;
			}
			else
			{
				$("#emply_email").focus();
			}
		}
	}
	
	function numeric(a,id)
	{
		var n=a.length;
		var numex=/^[0-9]+$/;
		if(a.match(numex))
		{
			
		}
		else
		{
			a=parseInt(a);
			if(!a){ a=""; }
			$("#"+id).val(a);
		}
	}
	function calculate_age(dob)
	{
		$.post("pages/consult_doc_data.php",
		{
			type:"dob_to_age",
			dob:dob,
		},
		function(data,status)
		{
			data=parseInt(data);
			if(!data){ data=""; }
			$("#emply_age").val(data);
		});
	}
	function calculate_dob(age)
	{
		$.post("pages/consult_doc_data.php",
		{
			type:"age_to_dob",
			age:age,
		},
		function(data,status)
		{
			$("#emply_dob").val(data);
		});
	}
	
	///////////////////////////////
	function load_id()
	{
		$.post("pages/user_master_data.php",
		{
			type:"user_master_emply_id",
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#emply_code").val(data);
		})
	}
	function load_main_info(val)
	{
		if(val==8) // Collection
		{
			$("#centre_no").show();
		}
		else
		{
			$("#centre_no").hide();
		}
		if(val==31) // Marketing
		{
			$("#target_amount").prop("type","text");
		}
		else
		{
			$("#target_amount").prop("type","hidden");
		}
		
		//alert(val);
		if(val!=0)
		{
			$("#emply_type").css("border","");
			if(val==5 || val==12 || val==13 || val==29) // Doc
			{
				$(".doc").fadeIn();
				$(".nurse").fadeIn();
				$(".cashier").fadeOut();
				$("#appr_result").hide();
				$("#res_approve").attr("checked",false);
				if(val==12)
				{
					$("#appr_result").show();
				}
				else if(val==13)
				{
					$("#appr_result").show();
				}
				else if(val==29)
				{
					$("#appr_result").show();
				}
			}else if(val==11) // Nurse
			{
				$(".doc").fadeOut();
				$(".nurse").fadeIn();
				$(".cashier").fadeOut();
			}else if(val==1 || val==3 || val==14 || val==15 || val==16 || val==17 || val==18 || val==19)
			{
				$(".cashier").fadeIn();
				$(".doc").fadeOut();
				$(".nurse").fadeOut();
			}else
			{
				$(".doc").fadeOut();
				$(".nurse").fadeOut();
				$(".cashier").fadeOut();
			}
			$(".emp").fadeIn();
		}else
		{
			$(".emp").hide();
			$(".doc").hide();
			$(".nurse").hide();
			$(".cashier").hide();
			$("#appr_result").hide();
		}
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:600}, {
			duration: 1500,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
	}
	function save_user(typ)
	{
		var error=0;
		var level=$("#emply_type").val();
		if(level==8 && $("#centre_no").val()=="0") // Collection
		{
			$("#centre_no").focus();
			error=1;
			return true;
		}
		if(level==5 || level==12 || level==13 || level==29)
		{
			var emply_speclty=$("#emply_speclty").val();
			if(emply_speclty==0)
			{
				$("#emply_speclty").css({'border-color': '#F00'}).focus();
				error=1;
				return true;
			}
			var emply_edu=$("#emply_edu").val();
			if(emply_edu=='')
			{
				$("#emply_edu").css({'border-color': '#F00'}).focus();
				error=1;
				return true;
			}
			var emply_design=$("#emply_design").val();
			if(emply_design=='')
			{
				$("#emply_design").css({'border-color': '#F00'}).focus();
				error=1;
				return true;
			}
		}
		if(level==5 || level==12 || level==13 || level==29 || level==11)
		{
			var emply_regd_no=$("#emply_regd_no").val();
			if(emply_regd_no=='')
			{
				$("#emply_regd_no").css({'border-color': '#F00'}).focus();
				//error=1;
				//return true;
			}
		}
		var emply_name=$("#emply_name").val();
		if(emply_name==0)
		{
			$("#emply_name").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
		var emply_sex=$("#emply_sex").val();
		if(emply_sex==0)
		{
			$("#emply_sex").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
		var emply_age=$("#emply_age").val();
		if(emply_age==0 || emply_age=="")
		{
			$("#emply_age").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
		
		var res_approve=0;
		if(($("#res_approve:checked").length)>0)
		{
			res_approve=1;
		}
		
		if(error==0)
		{
			$("#save_btn").prop("disabled",true);
			$.post("pages/user_master_data.php",
			{
				type:"employee_main_info_save",
				typ:typ,
				branch_id:$("#branch_id").val(),
				uhid:$("#uhid").val(),
				emply_code:$("#emply_code").val(),
				emply_type:$("#emply_type").val(),
				emply_speclty:$("#emply_speclty").val(),
				emply_edu:$("#emply_edu").val(),
				emply_design:$("#emply_design").val(),
				emply_regd_no:$("#emply_regd_no").val(),
				emply_name:$("#emply_name").val(),
				emply_sex:$("#emply_sex").val(),
				emply_dob:$("#emply_dob").val(),
				emply_email:$("#emply_email").val(),
				emply_phone:$("#emply_phone").val(),
				emply_address:$("#emply_address").val(),
				emply_status:$("#emply_status").val(),
				centre_no:$("#centre_no").val(),
				target_amount:$("#target_amount").val(),
				res_approve:res_approve,
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<h5>Saved</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					$("#uhid").val(data);
					$("#save_btn").val("Update");
					$("#save_btn").prop("disabled",false);
					$("#main_button").fadeIn(800);
				},1000);
			})
		}
	}
	function load_fields(typ)
	{
		$(".btn_tab").removeClass("btn_active");
		$("."+typ).addClass("btn_active");
		
		$.post("pages/user_master_data.php",
		{
			type:typ,
			uhid:$("#uhid").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#load_emply_info").html(data);
			$(".datepicker").datepicker({
				changeMonth:true,
				changeYear:true,
				dateFormat: 'yy-mm-dd',
				maxDate: '0',
			});
			if(typ=="Personal")
			{
				$("#addr1").focus();
			}
			if(typ=="Official")
			{
				//$("#join_date").focus();
			}
			if(typ=="Salary")
			{
				//$("#basic_pay").focus();
				$(".sal:first").focus();
			}
			if(typ=="PFESI")
			{
				$("#emply_con").focus();
			}
			if(typ=="salary_generation")
			{
				load_month();
			}
			if(typ=="Application_Access")
			{
				//$(".apl_access").fadeOut(500);
			}
			$({myScrollTop:window.pageYOffset}).animate({myScrollTop:700}, {
				duration: 1500,
				easing: 'swing',
				step: function(val){
					window.scrollTo(0, val);
				}
			});
		})
	}
	function load_month()
	{
		$.post("pages/user_master_data.php",
		{
			year:$("#year").val(),
			type:"load_month",
		},
		function(data,status)
		{
			$("#loadmon").html(data);
			load_wdays();
		})
	}
	function load_wdays()
	{
		$.post("pages/user_master_data.php",
		{
			month:$("#month").val(),
			year:$("#year").val(),
			type:"load_wdays",
		},
		function(data,status)
		{
			$("#wdays").html(data);
			view_sal();
		})
	}
	function view_sal()
	{
		$.post("pages/user_master_data.php",
		{
			emp_id:$("#emp_id").val(),
			month:$("#month").val(),
			year:$("#year").val(),
			type:"view_salary",
		},
		function(data,status)
		{
			$("#load_salary_generation").html(data);
		})
	}
	function update_attend(id)
	{
		if($("#attend").val().trim()=="")
		{
			$("#attend").focus();
		}
		else
		{
			$.post("pages/page_ajax.php",
			{
				id:id,
				attend:parseFloat($("#attend").val()),
				month:$("#month").val(),
				year:$("#year").val(),
				bs:$("#bs").val(),
				da:$("#da").val(),
				hra:$("#hra").val(),
				ca:$("#ca").val(),
				oa:$("#oa").val(),
				inc:$("#inc").val(),
				usr:$("#user").text().trim(),
				type:"generate_salary",
			},
			function(data,status)
			{
				//view_sal();
			})
		}
	}
	
	function marry_status_ch(val)
	{
		if(val=="Married")
		{
			$("#anniversary_date_span").fadeIn(500);
			$("#anniversary_date").focus();
		}else
		{
			$("#anniversary_date_span").fadeOut(500);
		}
	}
	function payment_mode_ch(val)
	{
		if(val==3)
		{
			$("#netbanking").fadeIn(500);
			$("#bank_name").focus();
			$({myScrollTop:window.pageYOffset}).animate({myScrollTop:600}, {
				duration: 1500,
				easing: 'swing',
				step: function(val){
					window.scrollTo(0, val);
				}
			});
		}else
		{
			$("#netbanking").fadeOut(500);
		}
	}
	
	var bl_tr1=1;
	var bl_sc1=0;
	function sel_pat_bill(val,e,typ)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13 && unicode!=38 && unicode!=40 && unicode!=113 && unicode!=112)
		{
			if(val.length>2)
			{
				load_pat_bill(val,typ);
			}
		}
		else if(unicode==13)
		{
				var uhid=document.getElementById("pat_reg"+bl_tr1).innerHTML;
				load_all_info(uhid);
		}
		else if(unicode==40)
		{
			chk=bl_tr1+1;	
			var cc=document.getElementById("bal_tr"+chk).innerHTML;
			if(cc)
			{
				bl_tr1=bl_tr1+1;
				$("#bal_tr"+bl_tr1).css({'color': '#419641','font-weight': 'bold','transform':'scale(0.95)','transition':'all .2s'});
				var bl_tr2=bl_tr1-1;
				$("#bal_tr"+bl_tr2).css({'color': 'black','font-weight': '100','transform':'scale(1.0)','transition':'all .2s'});
				var z2=bl_tr1%1;
				if(z2==0)
				{
					$("#bal_pat").scrollTop(bl_sc1)
					bl_sc1=bl_sc1+38;
				}
			}
		}
		else if(unicode==38)
		{
			chk=bl_tr1-1;	
			var cc=document.getElementById("bal_tr"+chk).innerHTML;
			if(cc)
			{
				bl_tr1=bl_tr1-1;
				$("#bal_tr"+bl_tr1).css({'color': '#419641','font-weight': 'bold','transform':'scale(0.95)','transition':'all .2s'});
				var bl_tr2=bl_tr1+1;
				$("#bal_tr"+bl_tr2).css({'color': 'black','font-weight': '100','transform':'scale(1.0)','transition':'all .2s'});
				var z2=bl_tr1%1;
				if(z2==0)
				{
					bl_sc1=bl_sc1-38;
					$("#bal_pat").scrollTop(bl_sc1);
				}
			}
		}
	}
	function load_pat_bill(val,typ)
	{
		if(val.length>0)
		{
			$.post("pages/user_master_data.php",
			{
				type:"load_added_user_master",
				typ:typ,
				val:val,
				branch_id:$("#branch_id_main").val(),
			},
			function(data,status)
			{
				$("#bal_pat").html(data);
				$("#bal_pat").slideDown(500);
				bl_tr=1;
				bl_sc=0;
			})
		}	
	}
	function load_all_info(uhid)
	{
		$.post("pages/user_master_data.php",
		{
			type:"load_added_user_master_all",
			uhid:uhid,
		},
		function(data,status)
		{
			$("#bal_pat").slideUp(500);
			
			var res=data.split("###");
			var pdata=res[0];
			var doc_data=res[1];
			var lab_data=res[2];
			var cashier_data=res[3];
			
			var pinfo=pdata.split("@@");
			$("#uhid").val(pinfo[0]);
			$("#emply_name").val(pinfo[1]);
			$("#emply_sex").val(pinfo[2]);
			if(pinfo[3]=="0000-00-00")
			{
				var emply_dob="";
			}
			else
			{
				var emply_dob=pinfo[3];
			}
			$("#emply_dob").val(emply_dob);
			$("#emply_phone").val(pinfo[4]);
			$("#emply_email").val(pinfo[5]);
			$("#emply_address").val(pinfo[6]);
			$("#emply_type").val(pinfo[7]);
			$("#emply_status").val(pinfo[8]);
			$("#branch_id").val(pinfo[9]);
			
			var val=pinfo[7];
			
			var doc_info=doc_data.split("@@");
			
			if(val==8) // Collection
			{
				$("#centre_no").val(pinfo[11]).show();
			}
			if(val==31) // Marketing
			{
				$("#target_amount").val(pinfo[12]).prop("type","text");
			}
			else
			{
				$("#amount").prop("type","hidden");
			}
			if(val==5) // Con Doc
			{
				$("#emply_speclty").val(doc_info[0]);
				$("#emply_edu").val(doc_info[1]);
				$("#emply_design").val(doc_info[2]);
				$("#emply_regd_no").val(doc_info[3]);
			}
			
			var lab_info=lab_data.split("@@");
			
			if(lab_info[0]==1)
			{
				$("#res_approve").attr("checked",true);
			}
			else
			{
				$("#res_approve").attr("checked",false);
			}
			if(val==12 || val==13 || val==29) // lab Doc
			{
				$("#appr_result").show();
				$("#emply_edu").val(lab_info[1]);
				$("#emply_design").val(lab_info[2]);
				$("#emply_speclty").val(lab_info[3]);
				$("#emply_regd_no").val(lab_info[4]);
			}
			
			var cashier_info=cashier_data.split("@@");
			
			var val=pinfo[7];
			if(val!=0)
			{
				$(".emp").fadeIn();
				//~ $("#appr_result").hide();
				//~ $("#emply_type").css("border","");
				if(val==5 || val==12 || val==13|| val==29) // Doc
				{
					$(".doc").fadeIn();
					$(".nurse").fadeIn();
					$(".cashier").fadeOut();
					//~ $("#res_approve").attr("checked",false);
					
				}else if(val==11) // Nurse
				{
					$(".doc").fadeOut();
					$(".nurse").fadeIn();
					$(".cashier").fadeOut();
				}else if(val==1 || val==3 || val==14 || val==15 || val==16 || val==17 || val==18 || val==19)
				{
					$(".cashier").fadeIn();
					$(".doc").fadeOut();
					$(".nurse").fadeOut();
				}else
				{
					$(".doc").fadeOut();
					$(".nurse").fadeOut();
					$(".cashier").fadeOut();
				}
				
			}else
			{
				$(".emp").hide();
				$(".doc").hide();
				$(".nurse").hide();
				$(".cashier").hide();
				$("#appr_result").hide();
			}
			$("#bal_pat").slideUp(500);
			$("#save_btn").val("Update");
			$(".btn_tab").removeClass("btn_active");
			$("#main_button").fadeIn(800);
			$("#load_emply_info").html('');
			$({myScrollTop:window.pageYOffset}).animate({myScrollTop:600}, {
				duration: 1500,
				easing: 'swing',
				step: function(val){
					window.scrollTo(0, val);
				}
			});
			
			calculate_age($("#emply_dob").val());
		})
	}
	function com_save_user(typ)
	{
		var all_val="";
		var rel_det="";
		if(typ=="Personal")
		{
			var all_val=$("#addr1").val()+'@penguin@'+$("#addr2").val()+'@penguin@'+$("#city").val()+'@penguin@'+$("#mobile").val()+'@penguin@'+$("#emrgncy_fone_no").val()+'@penguin@'+$("#marry_status").val()+'@penguin@'+$("#rel_name").val()+'@penguin@'+$("#rel_type").val()+'@penguin@'+$("#quali").val()+'@penguin@'+$("#anniversary_date").val();
			var cls=$(".rel_det").length;
			for(var j=0;j<cls;j++)
			{
				rel_det+=$('.rel_det').eq(j).val()+"##";
			}
		}
		if(typ=="Official")
		{
			var qqq=$('#report_head:checked').val();
			if(qqq==1)
			{
				var report_head=1;
			}else
			{
				var report_head=0;
			}
			var all_val=$("#join_date").val()+'@penguin@'+$("#join_type").val()+'@penguin@'+$("#department").val()+'@penguin@'+$("#designation").val()+'@penguin@'+$("#pan_no").val()+'@penguin@'+$("#voter_no").val()+'@penguin@'+$("#resi").val()+'@penguin@'+$("#esic").val()+'@penguin@'+$("#pf_no").val()+'@penguin@'+$("#resign_date").val()+'@penguin@'+report_head;
		}
		if(typ=="Salary")
		{
			var qqq=$('#payment_mode').val();
			if(qqq==0)
			{
				$('#payment_mode').focus();
				error=1;
				return true;
			}
			if(qqq==3)
			{
				var bank_name=$("#bank_name").val();
				if(bank_name==0)
				{
					$("#bank_name").css({'border-color': '#F00'}).focus();
					error=1;
					return true;
				}
				var bank_branch=$("#bank_branch").val();
				if(bank_branch=='')
				{
					$("#bank_branch").css({'border-color': '#F00'}).focus();
					error=1;
					return true;
				}
				var account_no=$("#account_no").val();
				if(account_no=='')
				{
					$("#account_no").css({'border-color': '#F00'}).focus();
					error=1;
					return true;
				}
				var ifsc_code=$("#ifsc_code").val();
				if(ifsc_code=='')
				{
					$("#ifsc_code").css({'border-color': '#F00'}).focus();
					error=1;
					return true;
				}
			}
			
			//var all_val=$("#basic_pay").val()+'@penguin@'+$("#da_pay").val()+'@penguin@'+$("#hra_pay").val()+'@penguin@'+$("#lta_pay").val()+'@penguin@'+$("#ca_pay").val()+'@penguin@'+$("#medical_pay").val()+'@penguin@'+$("#cea_pay").val()+'@penguin@'+$("#sa_pay").val()+'@penguin@'+$("#payment_mode").val()+'@penguin@'+$("#bank_name").val()+'@penguin@'+$("#account_no").val()+'@penguin@'+$("#ifsc_code").val();
			var inp=$(".sal");
			for(var j=0;j<(inp.length);j++)
			{
				if($(".sal").eq(j).val()!="")
				all_val+=$(".sal").eq(j).attr("id")+"##"+$(".sal").eq(j).val()+"@";
			}
			if(qqq==3)
			{
				all_val=all_val+"%"+qqq+"@"+$("#bank_name").val()+"@"+$("#bank_branch").val()+"@"+$("#account_no").val()+"@"+$("#ifsc_code").val()+"@";
			}
			else
			{
				all_val=all_val+"%"+qqq;
			}
		}
		if(typ=="PFESI")
		{
			//var all_val=$("#emply_con").val()+'@penguin@'+$("#emplyer_con").val()+'@penguin@'+$("#emply_esi").val()+'@penguin@'+$("#emplyer_esi").val();
			var inp=$(".ded");
			for(var j=1;j<=(inp.length);j++)
			{
				if($("#ded"+j).val()!="")
				all_val+=$("#ded"+j).attr("id")+"##"+$("#ded"+j).val()+"@";
			}
		}
		if(typ=="Application_Access")
		{
			var qqq=$('#application_access:checked').val();
			if(qqq==1)
			{
				var user_password=$("#user_password").val();
				if(user_password=='')
				{
					//~ $("#user_password").css({'border-color': '#F00'}).focus();
					//~ error=1;
					//~ return true;
				}
				var access_level=$("#access_level").val();
				if(access_level==0)
				{
					$("#access_level").css({'border-color': '#F00'}).focus();
					error=1;
					return true;
				}
			}
			var call_back_msg="Saved";
			var pass_reset=$('#pass_reset:checked').val();
			if(pass_reset==1 && user_password!='')
			{
				var call_back_msg="Password reset.";
			}
			
			var edit_info=$('#edit_info:checked').val();
			var edit_payment=$('#edit_payment:checked').val();
			var cancel_pat=$('#cancel_pat:checked').val();
			var discount_permission=$('#discount_permission:checked').val();
			var all_val=qqq+'@penguin@'+$("#user_password").val()+'@penguin@'+$("#access_level").val()+'@penguin@'+edit_info+'@penguin@'+edit_payment+'@penguin@'+cancel_pat+'@penguin@'+discount_permission;
		}
		
		if(all_val!="")
		{
			$.post("pages/user_master_data.php",
			{
				type:"save_user_master_component",
				typ:typ,
				all_val:all_val,
				rel_det:rel_det,
				uhid:$("#uhid").val(),
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<h5>"+call_back_msg+"</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					//load_next(typ);
					$("."+typ).click();
				},1000);
			})
		}
	}
	
	function upd_data(typ)
	{
		if($("#file").val()=="")
		{
			$("#file").css("border","1px solid #f00");
		}
		else if($("#fname").val()=="")
		{
			$("#fname").css("border","1px solid #f00");
			$("#fname").focus();
		}
		else
		{
			$.post("pages/user_master_data.php",
			{
				file:$("#file").val(),
				fname:$("#fname").val(),
				uid:$("#uhid").val(),
				user:$("#user").text().trim(),
				type:"upd_data",
			},
			function(data,status)
			{
				$("#filename").val(data);
				var input = document.getElementById("upd_frm");
				formData= new FormData(input);
				
				for (var i=0; i<poData.length; i++)
				{
					formData.append(poData[i].name, poData[i].value);
				}
				
				$.ajax({
				url: "pages/upload.php", // Url to which the request is send
				type: "POST",             // Type of request to be send, called as method
				data: formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
				contentType: false,       // The content type used when sending data to the server.
				cache: false,             // To unable request pages to be cached
				processData:false,        // To send DOMDocument or non processed data file it is set to false
				success: function(data)   // A function to be called if request succeeds
				{
					load_next("Documents");
				}
				});
			})
		}
	}
	function load_next(typ)
	{
		if(typ=="Personal")
		{
			load_fields("Official")
		}
		if(typ=="Official")
		{
			load_fields("Salary")
		}
		if(typ=="Salary")
		{
			load_fields("PFESI")
		}
		if(typ=="PFESI")
		{
			load_fields("Application_Access")
		}
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:700}, {
			duration: 1500,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
	}
	function com_prev_user(typ)
	{
		if(typ=="Personal")
		{
			load_fields("Official")
		}
		if(typ=="Official")
		{
			load_fields("Personal")
		}
		if(typ=="Salary")
		{
			load_fields("Official")
		}
		if(typ=="PFESI")
		{
			load_fields("Salary")
		}
		if(typ=="Application_Access")
		{
			load_fields("PFESI")
		}
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:700}, {
			duration: 1500,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
	}
	function application_access(val)
	{
		if(val==0)
		{
			$(".apl_access").fadeOut(500);
		}
		else
		{
			$(".apl_access").fadeIn(500);
			$({myScrollTop:window.pageYOffset}).animate({myScrollTop:700}, {
				duration: 1500,
				easing: 'swing',
				step: function(val){
					window.scrollTo(0, val);
				}
			});
		}
	}
	function user_password(p,id)
	{
		if(p.length>0)
		{
			$("#"+id).css("border","");
		}
	}
	function access_level_ch(ac,id)
	{
		if(ac.length>0)
		{
			$("#"+id).css("border","");
		}
	}
	function popitup(url)
	{
		var branch_id=$("#branch_id_main").val();
		url=url+"?bid="+btoa(branch_id);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');
	}
	function upd_doc(doc)
	{
		var qqq=$('#report_head:checked').val();
		if(qqq==1)
		{
			var report_head=1;
		}else
		{
			var report_head=0;
		}
		var all_val=$("#join_date").val()+'@penguin@'+$("#join_type").val()+'@penguin@'+$("#department").val()+'@penguin@'+$("#designation").val()+'@penguin@'+$("#pan_no").val()+'@penguin@'+$("#voter_no").val()+'@penguin@'+$("#resi").val()+'@penguin@'+$("#esic").val()+'@penguin@'+$("#pf_no").val()+'@penguin@'+$("#resign_date").val()+'@penguin@'+report_head;
		
		$("#loader").show();
		$.post("pages/user_master_data.php",
		{
			type:"save_user_master_component",
			typ:"Official",
			all_val:all_val,
			uid:$("#uid").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			//alert(data);
			//$("#loader").hide();
			//$('#msg').show();
			//$('#msg').html('Saved');
			//$('#msg').fadeOut(500);
		})
		if(doc=="pan")
		{
			var input = document.getElementById("fr_pan");
			formData= new FormData(input);
			
			for (var i=0; i<p_data.length; i++)
			{
				formData.append(p_data[i].name, p_data[i].value);
			}
			$.ajax(
			{
				url: 'pages/upd.php', // point to server-side PHP script 
				dataType: 'text',  // what to expect back from the PHP script, if anything
				cache: false,
				contentType: false,
				processData: false,
				data: formData,                    
				type: 'post',
				success: function(data)
				{
					if(data==1)
					{
						$("#loader").hide();
						$('#msg').show();
						$('#msg').html('Saved');
						$('#msg').fadeOut(500);
						load_next("Personal");
					}
				}
			 });
		}
		else if(doc=="voter")
		{
			var input = document.getElementById("fr_vot");
			formData= new FormData(input);
			
			for (var i=0; i<v_data.length; i++)
			{
				formData.append(v_data[i].name, v_data[i].value);
			}
			$.ajax(
			{
				url: 'pages/upd.php', // point to server-side PHP script 
				dataType: 'text',  // what to expect back from the PHP script, if anything
				cache: false,
				contentType: false,
				processData: false,
				data: formData,                    
				type: 'post',
				success: function(data)
				{
					if(data==1)
					{
						$("#loader").hide();
						$('#msg').show();
						$('#msg').html('Saved');
						$('#msg').fadeOut(500);
						load_next("Personal");
					}
				}
			 });
		}
		else if(doc=="resi")
		{
			var input = document.getElementById("fr_res");
			formData= new FormData(input);
			
			for (var i=0; i<r_data.length; i++)
			{
				formData.append(r_data[i].name, r_data[i].value);
			}
			$.ajax(
			{
				url: 'pages/upd.php', // point to server-side PHP script 
				dataType: 'text',  // what to expect back from the PHP script, if anything
				cache: false,
				contentType: false,
				processData: false,
				data: formData,                    
				type: 'post',
				success: function(data)
				{
					if(data==1)
					{
						$("#loader").hide();
						$('#msg').show();
						$('#msg').html('Saved');
						$('#msg').fadeOut(500);
						load_next("Personal");
					}
				}
			 });
		}
	}
	function reset_pass(val)
	{
		if(val==1)
		{
			$("#user_password").prop("disabled", false).focus();
		}
		if(val==0)
		{
			$("#user_password").prop("disabled", true);
		}
	}
</script>
<style>
.pheader
{
	background: #c8c8c8;
	color: #000;
	font-weight: bold;
	padding: 10px;
}
.btn_active
{
	background-color: #ccc;
}
.radio-inline
{
	display: inline;
}
.side_name_age
{
	border: 1px solid #ddd;
	background-color: #fff;
	padding: 4px;
	position: absolute;
	font-weight:bold;
}
</style>

