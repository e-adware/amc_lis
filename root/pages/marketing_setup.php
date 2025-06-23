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
		<select id="marketing_id_find" class="span2" onchange="doc_list()" autofocus>
			<option value="0">Select Marketing Agent</option>
		<?php
			$qry=mysqli_query($link," SELECT `emp_id`, `name` FROM `employee` WHERE `levelid`='30' AND `branch_id`='$p_info[branch_id]' ");
			while($data=mysqli_fetch_array($qry))
			{
				echo "<option value='$data[emp_id]'>$data[name]</option>";
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
	</center>
	<br>
	<br>
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
	$(document).on('keyup', ".capital", function () {
		$(this).val(function (_, val) {
			return val.toUpperCase();
		});
	});
	$(document).ready(function(){
		$("#loader").hide();
		doc_list();
	});
	
	function new_doc()
	{
		$("#doc_info").slideUp(300);
		load_doc_info(0);
		setTimeout(function(){
			$("#name").focus();
		},100);
	}
	function doc_list()
	{
		$("#loader").show();
		$.post("pages/marketing_setup_data.php",
		{
			type:"doc_list",
			marketing_id_find:$("#marketing_id_find").val(),
			branch_id:$("#branch_id_main").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#doc_info").slideUp(300);
			$("#doc_list").slideDown(300).html(data);
		})
	}
	function load_doc_info(emp_id)
	{
		$("#loader").show();
		$.post("pages/marketing_setup_data.php",
		{
			type:"doc_info",
			emp_id:emp_id,
			user:$("#user").text().trim(),
			branch_id:$("#branch_id_main").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#doc_list").slideUp(300);
			$("#doc_info").slideDown(300).html(data);
			
			$("#refbydoctorid").select2({ theme: "classic" });
			
			load_ref_docs(emp_id);
		});
	}
	
	function load_ref_docs(emp_id)
	{
		$("#loader").show();
		$.post("pages/marketing_setup_data.php",
		{
			type:"load_ref_docs",
			emp_id:emp_id,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_ref_docs").html(data);
		});
	}
	
	function refbydoctorid_up(e)
	{
		if(e.which==13)
		{
			if($("#refbydoctorid").val()==0)
			{
				$("#refbydoctorid").focus();
				return false;
			}
		}
	}
	
	function save()
	{
		if($("#marketing_id").val()==0)
		{
			$("#marketing_id").focus();
			return false;
		}
		if($("#refbydoctorid").val()==0)
		{
			$("#refbydoctorid").focus();
			return false;
		}
		
		$("#loader").show();
		$.post("pages/marketing_setup_data.php",
		{
			type:"save_data",
			user:$("#user").text().trim(),
			branch_id:$("#branch_id").val(),
			emp_id:$("#marketing_id").val(),
			refbydoctorid:$("#refbydoctorid").val(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader").hide();
			var res=data.split("@$@");
			bootbox.dialog({message: "<h5>"+res[1]+"</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
				load_doc_info($("#marketing_id").val());
			},2000);
		});
	}
	
	function delete_doc(emp_id,refbydoctorid)
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to remove ?</h5>",
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
						$.post("pages/marketing_setup_data.php",
						{
							type:"delete_doc",
							emp_id:emp_id,
							refbydoctorid:refbydoctorid,
						},
						function(data,status)
						{
							//alert(data);
							$("#loader").hide();
							var res=data.split("@$@");
							bootbox.dialog({message: "<h5>"+res[1]+"</h5>"});
							setTimeout(function(){
								bootbox.hideAll();
								load_doc_info(emp_id);
							},2000);
						});
					}
				}
			}
		});
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
