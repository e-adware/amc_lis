<?php
$emp_id=trim($_SESSION["emp_id"]);

if($p_info["levelid"]==1)
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
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<select id="branch_id" class="span2" style="<?php echo $branch_display; ?>">
				<?php
					$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
					while($branch=mysqli_fetch_array($branch_qry))
					{
						if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
						echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
					}
				?>
				</select>
				<b>Centre</b>
				<select class="span5" id="centreno">
					<!--<option value="0">All Centre</option>-->
				</select>
			</td>
			<td>
				
			</td>
		</tr>
		<tr>
			<td>
				<select class="span2" id="category_id" onChange="category_change()">
					<!--<option value="0">All Category</option>-->
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
			<td>
				<input type="text" class="span1 numericfloat" id="com_per" placeholder="%" maxlength="4" onkeyup="com_per_up(event,this)" onfocus="$(this).select()" onpaste="return false;" ondrop="return false;" title="Discount % per test">
				<input type="hidden" class="span1 numericc" id="com_amount" placeholder="Amount" maxlength="4" onkeyup="com_amount_up(event,this)" onfocus="$(this).select()" onpaste="return false;" ondrop="return false;" title="Discount amount per test">
				
				<button class="btn btn-save" id="save_btn" style="margin-bottom: 10px;" onclick="save()"><i class="icon-save"></i> Save</button>
			</td>
		</tr>
		<tr id="save_tr">
			<td colspan="5">
				<center>
					<button class="btn btn-search com_search_btn" id="category_wise" onclick="load_save_data('category_wise')"><i class="icon-search"></i> Category Wise</button>
					<button class="btn btn-search com_search_btn" id="dept_wise" onclick="load_save_data('dept_wise')"><i class="icon-search"></i> Department Wise</button>
					<button class="btn btn-search com_search_btn" id="test_wise" onclick="load_save_data('test_wise')"><i class="icon-search"></i> Test Wise</button>
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
		
		$("#testid").select2({
			dropdownAutoWidth: true,
			multiple: true,
			width: '70%',
			height: '30px',
			placeholder: "All Tests",
			allowClear: true
		});
		$('.select2-search__field').css('width', '100%');
		
		load_centres();
		category_change();
		load_tests();
		//load_save_data('as_a_whole');
	});
	
	function load_centres()
	{
		$("#loader").show();
		$.post("pages/centre_test_discount_setup_data.php",
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
	
	function category_change()
	{
		$("#loader").show();
		$.post("pages/centre_test_discount_setup_data.php",
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
		$.post("pages/centre_test_discount_setup_data.php",
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
			alert("Enter Discount");
			$("#com_per").focus();
			return false;
		}
		
		var com_per=parseFloat($("#com_per").val());
		if(!com_per){ com_per=0; }
		
		var com_amount=parseInt($("#com_amount").val());
		if(!com_amount){ com_amount=0; }
		
		//~ if(com_per==0)
		//~ {
			//~ alert("Enter Discount percentage");
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
		if($("#category_id").val()==0 && $("#type_id").val()==0 && testids=="0")
		{
			//~ msg="Are you sure want to save <i>As a whole</i> discount of "+com_msg+" for all tests ?";
			//~ report_name="as_a_whole";
			alert("Select Category");
			return false;
		}
		
		// 2
		if($("#category_id").val()>0 && $("#type_id").val()==0 && testids=="0")
		{
			msg="Are you sure want to save <i>Category wise("+$( "#category_id option:selected" ).text()+")</i> discount of "+com_msg+" ?";
			report_name="category_wise";
		}
		
		// 3
		if($("#category_id").val()>0 && $("#type_id").val()>0 && testids=="0")
		{
			msg="Are you sure want to save <i>Department wise("+$( "#type_id option:selected" ).text()+")</i> discount of "+com_msg+" ?";
			report_name="dept_wise";
		}
		
		// 4
		if(($("#category_id").val()>0 && $("#type_id").val()>0 && testids!="0") || ($("#category_id").val()>0 && $("#type_id").val()==0 && testids!="0"))
		{
			msg="Are you sure want to save <i>Test wise("+$( "#testid option:selected" ).text()+")</i> discount of "+com_msg+" ?";
			report_name="test_wise";
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
						$.post("pages/centre_test_discount_setup_data.php",
						{
							type:"save",
							branch_id:$("#branch_id").val(),
							centreno:$("#centreno").val(),
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
		$.post("pages/centre_test_discount_setup_data.php",
		{
			type:val,
			branch_id:$("#branch_id").val(),
			centreno:$("#centreno").val(),
			category_id:$("#category_id").val(),
			type_id:$("#type_id").val(),
			testids:testids,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").html(data);
		})
	}
	
	function delete_com(report_name,cno,cid,did,tid)
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
						$.post("pages/centre_test_discount_setup_data.php",
						{
							type:"delete_com",
							branch_id:$("#branch_id").val(),
							centreno:cno,
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
	
	function print_page(val,cno,cid,did,tid,bid)
	{
		if(!$("#testid").val())
		{
			var testids="0";
		}
		else
		{
			var testids=$("#testid").val().toString();
		}
		
		var user=$("#user").text().trim();
		
		url="pages/centre_test_discount_setup_print.php?v="+btoa(0)+"&val="+btoa(val)+"&cno="+btoa(cno)+"&cid="+btoa(cid)+"&did="+btoa(did)+"&tid="+btoa(testids)+"&bid="+btoa(bid);
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function tr_focus(own_cls,cls)
	{
		//$("."+cls).css({"color":"#000"});
		//$("."+own_cls).css({"color":"#f00"});
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
