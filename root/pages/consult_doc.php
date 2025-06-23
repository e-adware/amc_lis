<?php
$branch_str=" AND `branch_id`='$p_info[branch_id]'";
$element_style="display:none";
if($p_info["levelid"]==1)
{
	$branch_str="";
	
	$element_style="display:none;";
	$branch_num=mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if($branch_num>1)
	{
		$element_style="display:;";
	}
}
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span>
    </div>
</div>
<!--End-header-->
<div class="container-fluid">
	<center>
		<span class="side_name">Search</span>
		<input class="span4" type="text" id="doc_name" placeholder="Search Doctor" style="margin-left: 60px;" onkeyup="doc_list()" autofocus>
		<select id="speciality_id" class="span2" onchange="doc_list()">
			<option value="0">Select Department</option>
		<?php
			$spclt_qry=mysqli_query($link," SELECT * FROM `doctor_specialist_list` order by `name` ");
			while($spclt=mysqli_fetch_array($spclt_qry))
			{
				echo "<option value='$spclt[speciality_id]'>$spclt[name]</option>";
			}
		?>
		</select>
		<select id="branch_id_main" class="span2" onchange="doc_list()" style="<?php echo $element_style; ?>">
		<?php
			$qry=mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
			while($data=mysqli_fetch_array($qry))
			{
				if($data["branch_id"]==$p_info["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
				echo "<option value='$data[branch_id]' $branch_sel>$data[name]</option>";
			}
		?>
		</select>
		<button class="btn btn-new" onClick="new_doc()" style="margin-bottom: 10px;"><i class="icon-edit"></i> New Doctor</button>
		<button class="btn btn-success" onclick="merge_doctor_div()" style="margin-bottom: 10px;"><i class="icon-group"></i> Merge Doctor</button>
		<button class="btn btn-print" onclick="print_doctor_list()"><i class="icon-print"></i> Doctor List</button>
	</center>
	<div id="doc_list"></div>
	<div id="doc_info"></div>
</div>
<button type="button" class="btn btn-info" id="merge_doctor_btn" data-toggle="modal" data-target="#myModal" style="display:none;">Open Modal</button>
<div id="myModal" class="modal fade modal_main" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Merge Duplicate Doctors</h4>
			</div>
			<div class="modal-body" id="load_data">
			</div>
			<div class="modal-footer" style="display:none;">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	//~ $(document).on('keyup', ".capital", function () {
		//~ $(this).val(function (_, val) {
			//~ return val.toUpperCase();
		//~ });
	//~ });
	$(document).ready(function(){
		$("#loader").hide();
		//$("#speciality_id").select2({ theme: "classic" });
		//~ doc_list();
		doc_list();
		datepicker();
	});
	
	function datepicker()
	{
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '-15Y',
			yearRange: "-150:+0",
			defaultDate:'2000-01-01',
		});
	}
	function new_doc()
	{
		$("#doc_info").slideUp(300);
		load_doc_info(0,0);
		setTimeout(function(){
			$("#name").focus();
		},100);
	}
	function doc_list()
	{
		$("#loader").show();
		$.post("pages/consult_doc_data.php",
		{
			type:"doc_list",
			doc_name:$("#doc_name").val(),
			speciality_id:$("#speciality_id").val(),
			branch_id:$("#branch_id_main").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#doc_info").slideUp(300);
			$("#doc_list").slideDown(300).html(data);
		})
	}
	function load_doc_info(doc_id,emp_id)
	{
		$("#loader").show();
		$.post("pages/consult_doc_data.php",
		{
			type:"doc_info",
			doc_id:doc_id,
			emp_id:emp_id,
			user:$("#user").text().trim(),
			branch_id:$("#branch_id_main").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#doc_list").slideUp(300);
			$("#doc_info").slideDown(300).html(data);
			datepicker();
			calculate_age($("#dob").val());
			
			$("#main_con_doc_id").select2({ theme: "classic" });
		});
	}
	
	function main_con_doc_change(val)
	{
		if(val==0)
		{
			$("#access_tr").show();
			$("#access").val("0");
			$("#password").val("");
		}
		else
		{
			$("#access_tr").hide();
			$("#access").val("0");
			$("#password").val("");
		}
	}
	
	function name_up(e)
	{
		if(e.keyCode == 13)
		{
			if($("#name").val()=="")
			{
				$("#name").focus();
				return false;
			}
			else
			{
				$("#sex").focus();
			}
		}
	}
	function sex_up(e)
	{
		if(e.keyCode == 13)
		{
			if($("#sex").val()==0)
			{
				$("#sex").focus();
				return false;
			}
			else
			{
				$("#phone").focus();
			}
		}
	}
	function phone_up(e)
	{
		numeric($("#phone").val(),"phone");
		if(e.keyCode == 13)
		{
			if($("#phone").val()!="" && $("#phone").val().length!=10)
			{
				$("#phone").focus();
				return false;
			}
			else
			{
				$("#email").focus();
			}
		}
	}
	function email_up(e)
	{
		if(e.keyCode == 13)
		{
			$("#dob").focus();
		}
	}
	function dob_up(e)
	{
		if(e.keyCode==13)
		{
			$("#age").focus();
		}
	}
	function age_up(e)
	{
		numeric($("#age").val(),"age");
		
		calculate_dob($("#age").val());
		
		if(e.keyCode == 13)
		{
			//~ if($("#age").val()=="" || $("#age").val()==0)
			//~ {
				//~ $("#age").focus();
				//~ return false;
			//~ }
			//~ else
			//~ {
				//~ $("#address").focus();
			//~ }
			$("#address").focus();
		}
	}
	function address_up(e)
	{
		if(e.keyCode==13)
		{
			$("#qualification").focus();
		}
	}
	function qualification_up(e)
	{
		if(e.keyCode==13)
		{
			$("#designation").focus();
		}
	}
	function designation_up(e)
	{
		if(e.keyCode==13)
		{
			$("#dept_id").focus();
		}
	}
	function dept_id_up(e)
	{
		if(e.keyCode==13)
		{
			if($("#dept_id").val()==0)
			{
				$("#dept_id").focus();
				return false;
			}
			else
			{
				$("#regd_no").focus();
			}
		}
	}
	function regd_no_up(e)
	{
		if(e.keyCode==13)
		{
			$("#opd_visit_fee").focus();
		}
	}
	function opd_visit_fee_up(e)
	{
		if(e.keyCode==13)
		{
			if($("#opd_visit_fee").val()=="")
			{
				$("#opd_visit_fee").focus();
			}
			else
			{
				$("#opd_visit_validity").focus();
			}
		}
	}
	function opd_visit_validity_up(e)
	{
		if(e.keyCode==13)
		{
			if($("#opd_visit_validity").val()=="")
			{
				$("#opd_visit_validity").focus();
			}
			else
			{
				$("#opd_reg_fee").focus();
			}
		}
	}
	function opd_reg_fee_up(e)
	{
		if(e.keyCode==13)
		{
			if($("#opd_reg_fee").val()=="")
			{
				$("#opd_reg_fee").focus();
			}
			else
			{
				$("#opd_reg_validity").focus();
			}
		}
	}
	function opd_reg_validity_up(e)
	{
		if(e.keyCode==13)
		{
			if($("#opd_reg_validity").val()=="")
			{
				$("#opd_reg_validity").focus();
			}
			else
			{
				$("#opd_room").focus();
			}
		}
	}
	function opd_room_up(e)
	{
		if(e.keyCode==13)
		{
			$("#status").focus();
		}
	}
	function status_up(e)
	{
		if(e.keyCode==13)
		{
			$("#save_btn").focus();
		}
	}
	function access_change(val)
	{
		if(val==0)
		{
			$(".pass_td").hide();
		}
		else
		{
			$(".pass_td").show();
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
			$("#age").val(data);
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
			$("#dob").val(data);
		});
	}
	
	function save()
	{
		//~ $("#opd_visit_fee").val("0");
		//~ $("#opd_visit_validity").val("0");
		//~ $("#opd_reg_fee").val("0");
		//~ $("#opd_reg_validity").val("0");
		
		if($("#name").val()=="")
		{
			$("#name").focus();
			return false;
		}
		if($("#sex").val()==0)
		{
			$("#sex").focus();
			return false;
		}
		//~ if($("#age").val()==0)
		//~ {
			//~ $("#age").focus();
			//~ return false;
		//~ }
		if($("#dept_id").val()==0)
		{
			$("#dept_id").focus();
			return false;
		}
		if($("#opd_visit_fee").val()=="")
		{
			$("#opd_visit_fee").focus();
			return false;
		}
		if($("#opd_visit_validity").val()=="")
		{
			$("#opd_visit_validity").focus();
			return false;
		}
		if($("#opd_reg_fee").val()=="")
		{
			$("#opd_reg_fee").focus();
			return false;
		}
		if($("#opd_reg_validity").val()=="")
		{
			$("#opd_reg_validity").focus();
			return false;
		}
		
		if($("#access").val()==1 && $("#password").val()=="")
		{
			$(".pass_td").show();
			$("#password").focus();
			return false;
		}
		
		$.post("pages/consult_doc_data.php",
		{
			type:"save_info",
			user:$("#user").text().trim(),
			branch_id:$("#branch_id").val(),
			doc_id:$("#doc_id").val(),
			emp_id:$("#emp_id").val(),
			name:$("#name").val(),
			sex:$("#sex").val(),
			phone:$("#phone").val(),
			email:$("#email").val(),
			dob:$("#dob").val(),
			address:$("#address").val(),
			qualification:$("#qualification").val(),
			designation:$("#designation").val(),
			dept_id:$("#dept_id").val(),
			regd_no:$("#regd_no").val(),
			opd_visit_fee:$("#opd_visit_fee").val(),
			opd_visit_validity:$("#opd_visit_validity").val(),
			opd_reg_fee:$("#opd_reg_fee").val(),
			opd_reg_validity:$("#opd_reg_validity").val(),
			room_id:$("#opd_room").val(),
			status:$("#status").val(),
			access:$("#access").val(),
			password:$("#password").val(),
			main_con_doc_id:$("#main_con_doc_id").val(),
		},
		function(data,status)
		{
			//alert(data);
			bootbox.dialog({message: "<h5>"+data+"</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
				doc_list();
			},2000);
		});
	}
	
	
	function merge_doctor_div()
	{
		$.post("pages/consult_doc_data.php",
		{
			type:"merge_doctor_div",
		},
		function(data,status)
		{
			$("#merge_doctor_btn").click();
			$("#load_data").html(data);
			$("#main_doc").select2({ theme: "classic" });
		})
	}
	function main_doc_change()
	{
		$.post("pages/consult_doc_data.php",
		{
			type:"load_duplicate_doc",
			main_doc:$("#main_doc").val(),
		},
		function(data,status)
		{
			$("#duplicate_doc").html(data);
			$("#duplicate_doc").select2({ theme: "classic" });
		})
	}
	function save_merge()
	{
		if($("#main_doc").val()==0)
		{
			alert("Select Main Doctor");
			$("#main_doc").focus();
			return false;
		}
		if(!$("#duplicate_doc").val())
		{
			alert("Select Duplicate Doctor(s)");
			$("#duplicate_doc").focus();
			return false;
		}
		
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to merge ?</h5>",
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
					className: "btn btn-primary",
					callback: function() {
						//alert("K");
						$.post("pages/consult_doc_data.php",
						{
							type:"save_merge",
							main_doc:$("#main_doc").val(),
							duplicate_doc:$("#duplicate_doc").val(),
						},
						function(data,status)
						{
							alert(data);
							$("#modal_close_btn").click();
							load_doc();
						})
					}
				}
			}
		});
	}
	function print_doctor_list()
	{
		url="pages/consult_doc_print.php?v="+btoa(1234567890)+"&bid="+btoa($("#branch_id_main").val())+"&spid="+btoa($("#speciality_id").val());
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
.side_name
{
	border: 1px solid #ddd;
	background-color: #fff;
	padding: 4px;
	position: absolute;
	font-weight:bold;
}
#doc_list
{
    max-height: 400px;
    overflow-y: scroll;
}
.modal.fade.in
{
	top: 0%;
}
.modal_main
{
	width: 90%;
	left: 22%;
	z-index: 999 !important;
}
.modal-backdrop
{
	z-index: 990 !important;
}
</style>
