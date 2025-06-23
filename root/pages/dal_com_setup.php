<?php
$emp_id=trim($_SESSION["emp_id"]);

if($p_info["levelid"]==1 && $p_info["branch_id"]==1)
{
	$branch_str="";
	$branch_display="";
	
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
	
}

$branch_id=$p_info["branch_id"];
$branch_display="display:none;";

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Ref Doctor Contribution Setup</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<select id="branch_id" class="span1" style="<?php echo $branch_display; ?>">
				<?php
					$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
					while($branch=mysqli_fetch_array($branch_qry))
					{
						if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
						echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
					}
				?>
				</select>
				<select class="span2" id="centreno">
					<option value="0">All Centre</option>
				</select>
				<select class="" id="refbydoctorid" multiple>
					<!--<option value="0">All Doctor</option>-->
				</select>
			</td>
			<td rowspan="2">
				<input type="text" class="span1 numericfloat" id="com_per" placeholder="%" maxlength="4" onkeyup="com_per_up(event,this)" onfocus="$(this).select()" onpaste="return false;" ondrop="return false;" title="Commission % per test">
				<input type="text" class="span1 numericc" id="com_amount" placeholder="Amount" maxlength="4" onkeyup="com_amount_up(event,this)" onfocus="$(this).select()" onpaste="return false;" ondrop="return false;" title="Commission amount per test">
				<br>
				<button class="btn btn-save" id="save_btn" style="margin-bottom: 10px;" onclick="save()"><i class="icon-save"></i> Save</button>
			</td>
		</tr>
		<tr>
			<td>
				<select class="span2" id="category_id" onChange="category_change()">
					<option value="0">All Category</option>
				<?php
					$category_qry=mysqli_query($link, " SELECT `category_id`, `name` FROM `test_category_master` WHERE `category_id`>0 ");
					while($category=mysqli_fetch_array($category_qry))
					{
						echo "<option value='$category[category_id]'>$category[name]</option>";
					}
				?>
				</select>
				<select class="span2" id="type_id" onChange="dept_change()">
					<option value="0">All Department</option>
				</select>
				<select class="" id="testid" multiple>
					<!--<option value="0">All Test</option>-->
				</select>
			</td>
		</tr>
		<tr id="save_tr">
			<td colspan="5">
				<center>
					<button class="btn btn-search com_search_btn" id="as_a_whole" onclick="load_save_data('as_a_whole')"><i class="icon-search"></i> As a whole</button>
					<button class="btn btn-search com_search_btn" id="category_wise" onclick="load_save_data('category_wise')"><i class="icon-search"></i> Category Wise</button>
					<button class="btn btn-search com_search_btn" id="dept_wise" onclick="load_save_data('dept_wise')"><i class="icon-search"></i> Department Wise</button>
					<button class="btn btn-search com_search_btn" id="test_wise" onclick="load_save_data('test_wise')"><i class="icon-search"></i> Test Wise</button>
					<button class="btn btn-search com_search_btn" id="doctor_wise" onclick="load_save_data('doctor_wise')"><i class="icon-search"></i> Doctor Wise</button>
					<button class="btn btn-search com_search_btn" id="copy_setup" onclick="load_save_data('copy_setup')"><i class="icon-copy"></i> Copy Setup</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle">
	</div>
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).on('keyup', ".numericc", function () {
		$(this).val(function (_, val)
		{
			if(val==0)
			{
				return val;
			}
			else
			{
				var number=parseInt(val);
				if(!number){ number=""; }
				return number;
			}
		});
	});
	$(document).on('keyup', ".numericfloat", function () {
		$(this).val(function (_, val)
		{
			if(val==0)
			{
				return val;
			}
			else if(val==".")
			{
				return "0.";
			}
			else
			{
				var n=val.length;
				var numex=/^[0-9.]+$/;
				if(val[n-1].match(numex))
				{
					return number;
				}
				else
				{
					val=val.slice(0,n-1);
					return val;
				}
			}
		});
	});
	$(document).ready(function(){
		$("#loader").hide();
		
		$("#centreno").select2({ theme: "classic" });
		//$("#refbydoctorid").select2({ theme: "classic" });
		$("#category_id").select2({ theme: "classic" });
		$("#type_id").select2({ theme: "classic" });
		//$("#testid").select2({ theme: "classic" });
		
		$("#refbydoctorid").select2({
			dropdownAutoWidth: true,
			multiple: true,
			width: '100%',
			height: '30px',
			placeholder: "All Doctors",
			allowClear: true
		});
		
		$("#testid").select2({
			dropdownAutoWidth: true,
			multiple: true,
			width: '100%',
			height: '30px',
			placeholder: "All Tests",
			allowClear: true
		});
		$('.select2-search__field').css('width', '100%');
		
		load_centres();
		ref_doc_load();
		load_tests();
		//load_save_data('as_a_whole');
	});
	
	function load_centres()
	{
		$("#loader").show();
		$.post("pages/dal_com_setup_data.php",
		{
			type:"load_centres",
			branch_id:$("#branch_id").val(),
		},
		 function(data,status)
		 {
			 $("#loader").hide();
			 $("#centreno").html(data)
		 })
	}
	function ref_doc_load()
	{
		$("#loader").show();
		$.post("pages/dal_com_setup_data.php",
		{
			type:"ref_doc_load",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#refbydoctorid").html(data);
			$("#refbydoctorid_copy").html(data);
			//$("#refbydoctorid_paste").html(data);
		})
	}
	function ref_doc_load_copy()
	{
		$("#loader").show();
		$.post("pages/dal_com_setup_data.php",
		{
			type:"ref_doc_load_copy",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#refbydoctorid_copy").html(data);
		})
	}
	function ref_doc_load_paste()
	{
		$("#loader").show();
		$("#save_tr_copy").hide();
		$.post("pages/dal_com_setup_data.php",
		{
			type:"ref_doc_load_paste",
			centreno_copy:$("#centreno_copy").val(),
			refbydoctorid:$("#refbydoctorid_copy").val(),
			centreno_paste:$("#centreno_paste").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#refbydoctorid_paste").html(data);
			$("#save_tr_copy").show();
		})
	}
	
	function category_change()
	{
		$("#loader").show();
		$.post("pages/dal_com_setup_data.php",
		{
			type:"load_dept",
			category_id:$("#category_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#type_id").html(data);
			load_tests();
		})
	}
	
	function dept_change()
	{
		load_tests();
	}
	function load_tests()
	{
		$("#loader").show();
		$.post("pages/dal_com_setup_data.php",
		{
			type:"load_test",
			category_id:$("#category_id").val(),
			type_id:$("#type_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#testid").html(data);
		})
	}
	
	function com_per_up(e,dis)
	{
		if($(dis).val().length>0)
		{
			$("#com_amount").val("");
		}
		if(e.which==13)
		{
			$("#com_amount").focus();
		}
	}
	
	function com_amount_up(e,dis)
	{
		if($(dis).val().length>0)
		{
			$("#com_per").val("");
		}
		if(e.which==13)
		{
			$("#save_btn").focus();
		}
	}
	
	function save()
	{
		if(!$("#refbydoctorid").val())
		{
			var ref_docs="0";
		}
		else
		{
			var ref_docs=$("#refbydoctorid").val().toString();
		}
		if(!$("#testid").val())
		{
			var testids="0";
		}
		else
		{
			var testids=$("#testid").val().toString();
		}
		
		if($("#com_per").val()=="" && $("#com_amount").val()=="")
		{
			alert("Enter commission");
			$("#com_per").focus();
			return false;
		}
		
		var com_per=parseFloat($("#com_per").val());
		if(!com_per){ com_per=0; }
		
		var com_amount=parseInt($("#com_amount").val());
		if(!com_amount){ com_amount=0; }
		
		//~ if(com_per==0)
		//~ {
			//~ alert("Enter commission percentage");
			//~ $("#com_per").focus();
			//~ return false;
		//~ }
		
		if(com_amount>0)
		{
			com_per=0;
			var com_msg=" amount "+com_amount+" rupees per test ";
		}
		else
		{
			com_amount=0;
			var com_msg=com_per+" % ";
		}
		
		if(com_per>0)
		{
			com_amount=0;
			var com_msg=com_per+"%";
		}
		
		var msg="";
		var report_name="";
		
		// 1
		if(ref_docs=="0" && $("#category_id").val()==0 && $("#type_id").val()==0 && testids=="0")
		{
			msg="Are you sure want to save <i>As a whole</i> commission of "+com_msg+" for all doctors ";
			report_name="as_a_whole";
		}
		if(ref_docs!="0" && $("#category_id").val()==0 && $("#type_id").val()==0 && testids=="0")
		{
			msg="Are you sure want to save <i>As a whole</i> commission of "+com_msg+" for "+$( "#refbydoctorid option:selected" ).text()+" ";
			report_name="as_a_whole";
		}
		
		// 2
		if(ref_docs=="0" && $("#category_id").val()>0 && $("#type_id").val()==0 && testids=="0")
		{
			msg="Are you sure want to save <i>Category wise("+$( "#category_id option:selected" ).text()+")</i> commission of "+com_msg+" for all doctors ";
			report_name="category_wise";
		}
		if(ref_docs!="0" && $("#category_id").val()>0 && $("#type_id").val()==0 && testids=="0")
		{
			msg="Are you sure want to save <i>Category wise("+$( "#category_id option:selected" ).text()+")</i> commission of "+com_msg+" for "+$( "#refbydoctorid option:selected" ).text()+" ";
			report_name="category_wise";
		}
		
		// 3
		if(ref_docs=="0" && $("#category_id").val()>0 && $("#type_id").val()>0 && testids=="0")
		{
			msg="Are you sure want to save <i>Department wise("+$( "#type_id option:selected" ).text()+")</i> commission of "+com_msg+" for all doctors ";
			report_name="dept_wise";
		}
		if(ref_docs!="0" && $("#category_id").val()>0 && $("#type_id").val()>0 && testids=="0")
		{
			msg="Are you sure want to save <i>Department wise("+$( "#type_id option:selected" ).text()+")</i> commission of "+com_msg+" for "+$( "#refbydoctorid option:selected" ).text()+" ";
			report_name="dept_wise";
		}
		
		// 4
		if(ref_docs=="0" && ($("#category_id").val()>0 && $("#type_id").val()>0 && testids!="0") || $("#category_id").val()==0 && $("#type_id").val()==0 && testids!="0")
		{
			msg="Are you sure want to save <i>Test wise("+$( "#testid option:selected" ).text()+")</i> commission of "+com_msg+" for all doctors ";
			report_name="test_wise";
		}
		if(ref_docs!="0" && ($("#category_id").val()>0 && $("#type_id").val()>0 && testids!="0") || $("#category_id").val()==0 && $("#type_id").val()==0 && testids!="0")
		{
			msg="Are you sure want to save <i>Test wise("+$( "#testid option:selected" ).text()+")</i> commission of "+com_msg+" for "+$( "#refbydoctorid option:selected" ).text()+" ";
			report_name="test_wise";
		}
		
		if($("#centreno").val()=="0")
		{
			msg+=" and for all centres ?";
		}
		else
		{
			msg+=" for "+$( "#centreno option:selected" ).text()+" ?";
		}
		
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>"+msg+"</h5>",
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
						$("#loader").show();
						$("#save_tr").hide();
						$(".com_search_btn").css({"background-color":"#5bb75b"});
						$("#load_all").html("");
						$.post("pages/dal_com_setup_data.php",
						{
							type:"save",
							branch_id:$("#branch_id").val(),
							centreno:$("#centreno").val(),
							ref_docs:ref_docs,
							category_id:$("#category_id").val(),
							type_id:$("#type_id").val(),
							testids:testids,
							com_per:com_per,
							com_amount:com_amount,
						},
						function(data,status)
						{
							//alert(data);
							$("#loader").hide();
							$("#save_tr").show();
							$("#com_per").val("");
							alert(data);
							load_save_data(report_name);
						})
					}
				}
			}
		});
	}
	
	function load_save_data(val)
	{
		if(!$("#refbydoctorid").val())
		{
			var ref_docs="0";
		}
		else
		{
			var ref_docs=$("#refbydoctorid").val().toString();
		}
		if(!$("#testid").val())
		{
			var testids="0";
		}
		else
		{
			var testids=$("#testid").val().toString();
		}
		
		$(".com_search_btn").css({"background-color":"#5bb75b"});
		$("#"+val).css({"background-color":"#6b5bb7"});
		
		$("#loader").show();
		$.post("pages/dal_com_setup_data.php",
		{
			type:val,
			branch_id:$("#branch_id").val(),
			centreno:$("#centreno").val(),
			ref_docs:ref_docs,
			category_id:$("#category_id").val(),
			type_id:$("#type_id").val(),
			testids:testids,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").html(data);
			
			if(val=="copy_setup")
			{
				ref_doc_load_copy();
				ref_doc_load_paste();
				setTimeout(function(){
					$("#refbydoctorid_copy").select2({ theme: "classic" });
					$("#centreno_copy").select2({ theme: "classic" });
					$("#centreno_paste").select2({ theme: "classic" });
					$("#refbydoctorid_paste").select2({
						dropdownAutoWidth: true,
						multiple: true,
						width: '100%',
						height: '30px',
						placeholder: "Select Doctor(s)",
						allowClear: true
					});
				},100);
			}
		})
	}
	
	function save_copy_setup()
	{
		if($("#refbydoctorid_copy").val()==0)
		{
			alert("Select Copy From Doctor");
			$("#refbydoctorid_copy").select2('focus');
			return false;
		}
		if(!$("#refbydoctorid_paste").val())
		{
			var refbydoctorid_paste="0";
		}
		else
		{
			var refbydoctorid_paste=$("#refbydoctorid_paste").val().toString();
		}
		if(refbydoctorid_paste=="0")
		{
			alert("Select Copy To Doctor");
			$("#refbydoctorid_paste").select2('focus');
			return false;
		}
		
		bootbox.dialog({
			message: "<h5>Are you sure want to copy setup ? The existing setup of the selected Copy To Doctor(s) in "+$( "#centreno_paste option:selected" ).text()+" Centre will be deleted.</h5>",
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
						$("#loader").show();
						$("#save_tr_copy").hide();
						$.post("pages/dal_com_setup_data.php",
						{
							type:"save_copy_setup",
							centreno_copy:$("#centreno_copy").val(),
							refbydoctorid_copy:$("#refbydoctorid_copy").val(),
							centreno_paste:$("#centreno_paste").val(),
							refbydoctorid_paste:refbydoctorid_paste,
						},
						function(data,status)
						{
							$("#loader").hide();
							alert(data);
							$("#save_tr_copy").show();
							load_save_data("copy_setup");
						})
					}
				}
			}
		});
	}
	
	function delete_com(report_name,rid,cid,did,tid)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete this ?</h5>",
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
						$("#loader").show();
						$("#save_tr").hide();
						$(".del_btn").hide();
						$(".com_search_btn").css({"background-color":"#5bb75b"});
						$("#load_all").html("");
						$.post("pages/dal_com_setup_data.php",
						{
							type:"delete_com",
							branch_id:$("#branch_id").val(),
							refbydoctorid:rid,
							category_id:cid,
							type_id:did,
							testid:tid,
						},
						function(data,status)
						{
							$("#loader").hide();
							$("#save_tr").show();
							$(".del_btn").show();
							alert(data);
							load_save_data(report_name);
						})
					}
				}
			}
		});
	}
	
	function print_page(val,cno,rid,cid,did,tid,bid)
	{
		if(!$("#refbydoctorid").val())
		{
			var ref_docs="0";
		}
		else
		{
			var ref_docs=$("#refbydoctorid").val().toString();
		}
		if(!$("#testid").val())
		{
			var testids="0";
		}
		else
		{
			var testids=$("#testid").val().toString();
		}
		
		var user=$("#user").text().trim();
		
		url="pages/dal_com_setup_print.php?v="+btoa(0)+"&val="+btoa(val)+"&cno="+btoa(cno)+"&rid="+btoa(ref_docs)+"&cid="+btoa(cid)+"&did="+btoa(did)+"&tid="+btoa(testids)+"&bid="+btoa(bid);
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function tr_focus(own_cls,cls)
	{
		$("."+cls).css({"color":"#000"});
		$("."+own_cls).css({"color":"#f00"});
	}
</script>
<style>
.select2-dropdown
{
	z-index:99999 !important;
}
.select2
{
	margin-bottom: 1%;
}
.table-bordered{
	border: 1px solid #333;
}
.table-bordered th, .table-bordered td{
	border-left: 1px solid #000;
	border-top: 1px solid #000;
}
</style>
