<?php
if($p_info["levelid"]==1)
{
	$branch_str="";
	
	$branch_display="display:none;";
	$branch_num=mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if($branch_num>1)
	{
		$branch_display="display:;";
	}
	
	$dept_sel_dis="";
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
	
	$dept_sel_dis="disabled";
}

$branch_id=$p_info["branch_id"];

?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> Test Details</span>
		<span style="float:right;display:;">
			<button type="button" class="btn btn-primary" id="centre_modal_btn" data-toggle="modal" data-target="#centre_modal" style="float: right;display:none;"></button>
			<button type="button" class="btn btn-primary" style="float: right;" onclick="load_centres()"><i class="icon-edit"></i> Centre</button>
		</span>
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<span class="side_name">From</span>
				<input class="form-control datepicker span2" type="text" name="date1" id="date1" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 47px;" readonly>
				<span class="side_name">To</span>
				<input class="form-control datepicker span2" type="text" name="date2" id="date2" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 25px;" readonly>
				
				<select class="span2" id="category_id" onchange="load_dept()">
					<option value="0">All Category</option>
				<?php
					$qry=mysqli_query($link, "SELECT `category_id`, `name` FROM `test_category_master` WHERE `category_id` IN(select distinct a.category_id from testmaster a, patient_test_details b where a.testid=b.testid ) ORDER BY `category_id` ASC");
					while($data=mysqli_fetch_array($qry))
					{
						echo "<option value='$data[category_id]'>$data[name]</option>";
					}
				?>
				</select>
				
				<select class="span2" id="type_id" onchange="load_test()">
					<option value="0">All Department</option>
				<?php
					//~ $head_qry=mysqli_query($link, " SELECT a.`id`, a.`name` FROM `test_department` a, `testmaster` b, `patient_test_details` c WHERE a.`category_id`=b.`category_id` AND b.`testid`=c.`testid` GROUP BY `id` ORDER BY a.`name` ASC ");
					//~ while($head=mysqli_fetch_array($head_qry))
					//~ {
						//~ echo "<option value='$head[type_id]'>$head[type_name]</option>";
					//~ }
				?>
				</select>
				
				<select class="span2" id="testid">
					<option value="0">All Test</option>
				</select>
				
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
				
				<button class="btn btn-search" onClick="view_all()" style="margin-bottom: 10px;"><i class="icon-search"></i> View</button>
			</td>
		</tr>
	</table>
	<div id="deleted_record" style="float:right;"></div><br>
	<div id="data_load_1" class="ScrollStyle"></div>
	<div id="data_load_2" class="ScrollStyle"></div>
</div>
<!-- Modal -->
<div class="modal fade" id="centre_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><h4>Centre</h4></h5>
			</div>
			<div class="modal-body">
				<div id="centre_add_div"></div>
				<div id="centre_list_div"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<!-- Time -->
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>

<link rel="stylesheet" type="text/css" media="all" href="../jquery-ui/styles.css">
<script type="text/javascript" src="../jquery-ui/jquery.tablesorter.min.js"></script>

<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
	});
	$(document).on('keyup', ".numericc", function () {
		$(this).val(function (_, val) {
			var num=parseInt(val);
			if(!num){ num=""; }
			return num;
		});
	});
	function load_centres()
	{
		$("#loader").show();
		$.post("pages/test_details_data.php",
		{
			type:"load_centres",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			load_added_centre();
			$("#loader").hide();
			$("#centre_add_div").html(data);
			$("#centre_modal_btn").click();
		})
	}
	function load_added_centre()
	{
		$("#loader").show();
		$.post("pages/test_details_data.php",
		{
			type:"load_added_centre",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#centre_list_div").html(data);
		})
	}
	function add_centre()
	{
		if($("#centreno").val()=="")
		{
			alert("Select Centre");
			return false;
		}
		
		$("#loader").show();
		$.post("pages/test_details_data.php",
		{
			type:"add_centre",
			branch_id:$("#branch_id").val(),
			centreno:$("#centreno").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			alert(data);
			load_added_centre();
		})
	}
	function centre_remove(centreno)
	{
		let text = "Are you sure want to remove ?";
		if (confirm(text) == true) {
			text = "You pressed OK!";
			
			$("#loader").show();
			$.post("pages/test_details_data.php",
			{
				type:"centre_remove",
				centreno:centreno,
			},
			function(data,status)
			{
				$("#loader").hide();
				alert(data);
				load_added_centre();
			})
		}
	}
	
	function load_dept()
	{
		$("#loader").show();
		$.post("pages/test_details_data.php",
		{
			type:"load_dept",
			category_id:$("#category_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#type_id").html(data);
			$("#type_id").select2({ theme: "classic" });
		})
	}
	
	function load_test()
	{
		$("#loader").show();
		$.post("pages/test_details_data.php",
		{
			type:"load_test",
			type_id:$("#type_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#testid").html(data);
			$("#testid").select2({ theme: "classic" });
		})
	}
	
	function view_all(testid,date1,date2,branch_id)
	{
		$("#loader").show();
		$.post("pages/test_details_data.php",
		{
			type:"load_all_test",
			date1:$("#date1").val(),
			date2:$("#date2").val(),
			category_id:$("#category_id").val(),
			type_id:$("#type_id").val(),
			testid:$("#testid").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#data_load_2").hide();
			$("#data_load_1").slideUp(500,function(){ $("#data_load_1").html(data).slideDown(500);$("#keywords").tablesorter(); });
			//$("#data_load_1").html(data);
			deleted_record($("#date1").val(),$("#date2").val(),$("#branch_id").val());
			
			if(testid && date1 && date2 && branch_id)
			{
				load_replace(testid,date1,date2,branch_id);
			}
		})
	}
	function load_test_det(n,testid,date1,date2,branch_id)
	{
		$("#loader").show();
		$.post("pages/test_details_data.php",
		{
			type:"load_test_det",
			testid:testid,
			date1:date1,
			date2:date2,
			branch_id:branch_id,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#data_load_1").slideUp(500);
			$("#data_load_2").html(data).slideDown(500);
		})
	}
	function back_to_main()
	{
		$("#data_load_2").slideUp(500);
		$("#data_load_1").slideDown(500);
	}
	
	function test_remove(testid,date1,date2,branch_id)
	{
		var all_test="";
		var tst=$(".tst");
		for(var i=0;i<tst.length;i++)
		{
			var testid=tst[i].value;
			
			var remove_test_no=parseInt($("#remove_test_no"+testid).val());
			if(!remove_test_no){ remove_test_no=0; }
			if(remove_test_no>0)
			{
				var multi_test_pat=parseInt($("#multi_test_pat"+testid).val());
				if(!multi_test_pat){ multi_test_pat=0; }
				
				if(remove_test_no>multi_test_pat)
				{
					alert("Can't exceed Multiple Tests Patient");
					$("#remove_test_no"+testid).focus();
					return false
				}
				
				all_test+="@$@"+testid+"#"+remove_test_no+"#"+multi_test_pat;
			}
		}
		
		if(all_test=="")
		{
			alert("Enter test no. to remove");
			$("#remove_test_no"+testid).focus();
			return false
		}
		
		if (confirm('Are you sure ?')) {
			
			$("#loader").show();
			$.post("pages/test_details_data.php",
			{
				type:"test_remove",
				testid:testid,
				date1:date1,
				date2:date2,
				branch_id:branch_id,
				all_test:all_test,
			},
			function(data,status)
			{
				//alert(data);
				$("#loader").hide();
				var res=data.split("@");
				alert(res[1]);
				view_all();
				//load_test_det(0,testid,date1,date2,branch_id);
				deleted_record(date1,date2,branch_id);
			})
			
		} else {
			$("#remove_test_no"+testid).focus();
		}
	}
	
	function test_remove_old(testid,date1,date2,branch_id)
	{
		var multi_test_pat=parseInt($("#multi_test_pat").val());
		if(!multi_test_pat){ multi_test_pat=0; }
		
		var remove_test_no=parseInt($("#remove_test_no").val());
		if(!remove_test_no){ remove_test_no=0; }
		
		if(remove_test_no==0)
		{
			$("#remove_test_no").focus();
			return false
		}
		
		if(remove_test_no>multi_test_pat)
		{
			alert("Can't exceed than Multiple Tests Patient");
			$("#remove_test_no").focus();
			return false
		}
		
		if (confirm('Are you sure ?')) {
			
			$("#loader").show();
			$.post("pages/test_details_data.php",
			{
				type:"test_remove",
				testid:testid,
				date1:date1,
				date2:date2,
				branch_id:branch_id,
				multi_test_pat:$("#multi_test_pat").val(),
				remove_test_no:$("#remove_test_no").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				var res=data.split("@");
				alert(res[1]);
				load_test_det(0,testid,date1,date2,branch_id);
				deleted_record(date1,date2,branch_id);
			})
			
		} else {
			$("#remove_test_no").focus();
		}
	}
	
	var _changeInterval = null;
	function remove_test_no_up(e,dis,testid,date1,date2,branch_id)
	{
		clearInterval(_changeInterval)
		_changeInterval = setInterval(function() {
			// Typing finished, now you can Do whatever after 500 msec
			clearInterval(_changeInterval);
			remove_test_no_up_chk(e,dis,testid,date1,date2,branch_id);
		}, 500);
	}
	function remove_test_no_up_chk(e,dis,testid,date1,date2,branch_id)
	{
		var multi_test_pat=parseInt($("#multi_test_pat"+testid).val());
		if(!multi_test_pat){ multi_test_pat=0; }
		
		var remove_test_no=parseInt($("#remove_test_no"+testid).val());
		if(!remove_test_no){ remove_test_no=0; }
		
		if(remove_test_no>multi_test_pat)
		{
			alert("Can't exceed Multiple Tests Patient");
			$("#remove_test_no"+testid).val("").focus();
			$("#del_ammount_to"+testid).html("");
			return false
		}
		if(remove_test_no>0)
		{
			$.post("pages/test_details_data.php",
			{
				type:"load_test_amount",
				date1:date1,
				date2:date2,
				testid:testid,
				branch_id:branch_id,
				multi_test_pat:$("#multi_test_pat"+testid).val(),
				remove_test_no:$("#remove_test_no"+testid).val(),
			},
			function(data,status)
			{
				$("#del_ammount_to"+testid).html(data);
			})
		}
		else
		{
			$("#del_ammount_to"+testid).html("");
		}
	}
	function deleted_record(date1,date2,branch_id)
	{
		$.post("pages/test_details_data.php",
		{
			type:"load_deleted_record",
			date1:date1,
			date2:date2,
			branch_id:branch_id,
		},
		function(data,status)
		{
			$("#deleted_record").html(data);
		})
	}
	
	function load_replace(testid,date1,date2,branch_id)
	{
		$("#loader").show();
		$.post("pages/test_details_data.php",
		{
			type:"load_replace",
			testid:testid,
			date1:date1,
			date2:date2,
			branch_id:branch_id,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#data_load_1").slideUp(500);
			$("#data_load_2").html(data).slideDown(500);
			
			$("#testid_replace").select2({ theme: "classic" });
		})
	}
	var _changeInterval = null;
	function replace_test_no_up(e,dis,testid,date1,date2,branch_id)
	{
		clearInterval(_changeInterval)
		_changeInterval = setInterval(function() {
			// Typing finished, now you can Do whatever after 500 msec
			clearInterval(_changeInterval);
			replace_test_no_up_chk(e,dis,testid,date1,date2,branch_id);
		}, 500);
	}
	function replace_test_no_up_chk(e,dis,testid,date1,date2,branch_id)
	{
		if($("#testid_replace").val()==0)
		{
			alert("Select Test To Replace");
			$("#replace_test_no").val("");
			$("#replace_ammount_to").html("");
			return false
		}
		
		var single_test_pat=parseInt($("#single_test_pat").val());
		if(!single_test_pat){ single_test_pat=0; }
		
		var replace_test_no=parseInt($("#replace_test_no").val());
		if(!replace_test_no){ replace_test_no=0; }
		
		if(replace_test_no>single_test_pat)
		{
			alert("Can't exceed Single Test Patient");
			$("#replace_test_no").val("").focus();
			$("#replace_ammount_to").html("");
			return false
		}
		if(replace_test_no>0)
		{
			$.post("pages/test_details_data.php",
			{
				type:"load_test_amount_replace",
				date1:date1,
				date2:date2,
				testid:testid,
				branch_id:branch_id,
				single_test_pat:$("#single_test_pat").val(),
				replace_test_no:$("#replace_test_no").val(),
				testid_replace:$("#testid_replace").val(),
			},
			function(data,status)
			{
				$("#replace_ammount_to").html(data);
			})
		}
		else
		{
			$("#replace_ammount_to").html("");
		}
	}
	function test_replace(testid,date1,date2,branch_id)
	{
		var single_test_pat=parseInt($("#single_test_pat").val());
		if(!single_test_pat){ single_test_pat=0; }
		
		var replace_test_no=parseInt($("#replace_test_no").val());
		if(!replace_test_no){ replace_test_no=0; }
		
		if(replace_test_no==0)
		{
			$("#replace_test_no").focus();
			return false
		}
		
		if(replace_test_no>single_test_pat)
		{
			alert("Can't exceed than Single Test Patient");
			$("#replace_test_no").focus();
			return false
		}
		
		if (confirm('Are you sure ?')) {
			
			$("#test_replace_btn").hide();
			$("#loader").show();
			$.post("pages/test_details_data.php",
			{
				type:"test_replace",
				testid:testid,
				date1:date1,
				date2:date2,
				branch_id:branch_id,
				single_test_pat:single_test_pat,
				replace_test_no:replace_test_no,
				testid_replace:$("#testid_replace").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				var res=data.split("@");
				alert(res[1]);
				
				view_all(testid,date1,date2,branch_id);
				
				//replace_record(date1,date2,branch_id);
			})
			
		} else {
			$("#replace_test_no").focus();
		}
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
.ScrollStyle
{
    max-height: 300px;
    overflow-y: scroll;
}
.table_header_fix{
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 1;
}
.table-condensed > thead > tr > th, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > tbody > tr > td, .table-condensed > tfoot > tr > td
{
	padding: 5px;
}
</style>
