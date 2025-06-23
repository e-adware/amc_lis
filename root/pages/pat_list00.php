<?php

if($p_info["levelid"]==1 && $p_info["branch_id"]==1)
{
	$branch_str="";
	$branch_display="";
	
	$dept_sel_dis="";
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
	
	$dept_sel_dis="disabled";
}

$branch_id=$p_info["branch_id"];

$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);
$pat_type_str=base64_decode($_GET['pat_type_str']);
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td colspan="2">
				<b style="display:;">Patient Type</b>
				<select class="span2" id="pat_type" style="display:;" onChange="view_all()">
					<option value="0">All</option>
					<?php
						$qq_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `status`=0  ORDER BY `p_type_id` ");
						while($qq=mysqli_fetch_array($qq_qry))
						{
							$sel="";
							if($pat_type_str)
							{
								if($pat_type_str==$qq["p_type_id"]){ $sel="selected"; }else{ $sel=""; }
							}
							echo "<option value='$qq[p_type_id]' $sel>$qq[p_type]</option>";
						}
					?>
				</select>
				<select id="branch_id" class="span3" style="<?php echo $branch_display; ?>" onChange="view_all()">
				<?php
					$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
					while($branch=mysqli_fetch_array($branch_qry))
					{
						if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
						echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
					}
				?>
				</select>
				<b style="display:none;">State</b>
				<select id="state" onChange="change_state(this.value)" style="display:none;">
					<option value="0">Select</option>
				<?php
					//~ $state_qry=mysqli_query($link, " SELECT * FROM `state` ORDER BY `name` " );
					//~ while($state=mysqli_fetch_array($state_qry))
					//~ {
						//~ //if($state['state_id']=='4'){ $sel_state="selected"; }else{ $sel_state=""; }
						//~ echo "<option value='$state[state_id]' $sel_state >$state[name]</option>";
					//~ }
				?>
				</select>
				<b style="display:none;">District</b>
				<select id="district" onChange="view_all()" style="display:none;">
					
				</select>
				<b style="display:none;">Ref Doc</b>
				<select id="ref_doc_id" onChange="view_all()" style="display:none;">
					<option value="0">Select</option>
					<?php
						//~ $ref_doc_qry=mysqli_query($link, " SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` ORDER BY `ref_name` " );
						//~ while($ref_doc=mysqli_fetch_array($ref_doc_qry))
						//~ {
							//~ //if($state['state_id']=='4'){ $sel_state="selected"; }else{ $sel_state=""; }
							//~ echo "<option value='$ref_doc[refbydoctorid]' $sel_state >$ref_doc[ref_name]</option>";
						//~ }
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<b>From</b><!-- <?php echo date("Y-m-d"); ?> -->
				<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo $fdate_str; ?>" >
				<b>To</b>
				<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo $tdate_str; ?>" >
				<button class="btn btn-search" onClick="view_all()" style="margin-top: -1%;" ><i class="icon-search"></i> Search</button>
			</td>
			<td>
				<b>Name</b>
				<input type="text" class="span2" id="pat_name" onKeyup="view_all()" value="<?php echo $name_str; ?>" >
			</td>
		</tr>
		<tr>
			<td>
				<b>UHID</b>
				<input list="browsrs" type="text" class="span2" id="uhid" onKeyup="view_all()" value="<?php echo $uhid_str; ?>" >
				<datalist id="browsrs">
				<?php
					//~ $pid = mysqli_query($link," SELECT `patient_id` FROM `patient_info` order by `slno` DESC");
					//~ while($pat_uid=mysqli_fetch_array($pid))
					//~ {
						//~ echo "<option value='$pat_uid[patient_id]'>";
					//~ }
				?>
				</datalist>
				<b>Bill No</b>
				<input list="browsr" type="text" class="span2" id="pin" onKeyup="view_all()" value="<?php echo $pin_str; ?>" >
				<datalist id="browsr">
				<?php
					//~ $oid= mysqli_query($link," SELECT `opd_id` FROM `uhid_and_opdid` ORDER BY `slno` DESC ");
					//~ while($pat_oid=mysqli_fetch_array($oid))
					//~ {
						//~ echo "<option value='$pat_oid[opd_id]'>";
					//~ }
				?>
				</datalist>
				<b style="display:none;">Health Guide</b>
				<select id="health_guide_id" onChange="view_all()" style="display:none;">
					<option value="0">Select</option>
					<?php
						//~ $health_qry=mysqli_query($link, " SELECT `hguide_id`, `name` FROM `health_guide` ORDER BY `name` " );
						//~ while($health=mysqli_fetch_array($health_qry))
						//~ {
							//~ //if($state['state_id']=='4'){ $sel_state="selected"; }else{ $sel_state=""; }
							//~ echo "<option value='$health[hguide_id]' $sel_state >$health[name]</option>";
						//~ }
					?>
				</select>
			</td>
			<td>
				<b>Phone</b>
				<input type="text" class="span2" id="phone" onKeyup="view_all()" value="<?php echo $phone_str; ?>" >
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
</div>
<input type="hidden" id="list_start" value="50">
<!-- Modal -->
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" id="cancel_request_btn" style="display:none;">Cancel Request</button>
<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Cancel Request</h4>
			</div>
			<div class="modal-body">
				<div id="cancel_request_data"></div>
			</div>
			<div class="modal-footer" style="display:none;">
				<button type="button" class="btn btn-inverse" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- Loader -->
<div id="loader" style="margin-top:0%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<!-- Time -->
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<script>
	$(document).ready(function(){
		$("#loader").hide();
		view_all();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		$("#pat_type").change(function(){
			view_all();
		});
		
		$('#load_all').on('scroll', function() {
			var div_height = $(this).get(0).scrollHeight;
			var div = $(this).get(0);
			
			if(div.scrollTop + div.clientHeight >= div.scrollHeight) {
				var list_start=$("#list_start").val().trim();
				list_start=parseInt(list_start)+50;
				$("#list_start").val(list_start);
				view_all();
			}
		});
		
	});
	$( document ).tooltip();
	function change_state(val)
	{
		$.post("pages/pat_list_data.php",
		{
			type:"load_district_pat",
			val:val,
		},
		function(data,status)
		{
			$("#district").html(data);
			view_all();
		})
	}
	function view_all()
	{
		$("#loader").show();
		$.post("pages/pat_list_data.php",
		{
			type:"load_all_pat",
			pat_type:$("#pat_type").val(),
			branch_id:$("#branch_id").val(),
			from:$("#from").val(),
			to:$("#to").val(),
			pat_name:$("#pat_name").val(),
			pat_uhid:$("#uhid").val(),
			pin:$("#pin").val(),
			phone:$("#phone").val(),
			state:$("#state").val(),
			district:$("#district").val(),
			ref_doc_id:$("#ref_doc_id").val(),
			health_guide_id:$("#health_guide_id").val(),
			list_start:$("#list_start").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function redirect_page(uhid,pin,type,access)
	{
		var date_str="&param_str=2&fdate_str="+$("#from").val()+"&tdate_str="+$("#to").val()+"&uhid_str="+$("#uhid").val()+"&pin_str="+$("#pin").val()+"&name_str="+$("#pat_name").val()+"&phone_str="+$("#phone").val()+"&pat_type_str="+$("#pat_type").val();
		
		if(type=="3")
		{
			if(access>0)
			{
				window.location="processing.php?param=52&uhid="+uhid+"&ipd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to IPD</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}else if(type=="1")
		{
			if(access>0)
			{
				//window.location="processing.php?param=3&uhid="+uhid+"&consult=1";
				//~ window.location="processing.php?param=3&uhid="+uhid+"&consult=1&opd="+pin+date_str;
				window.location="processing.php?param=81&uhid="+uhid+"&consult=1&opd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to OPD</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}else if(type=="2")
		{
			if(access>0)
			{
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
				//~ window.location="processing.php?param=3&uhid="+uhid+"&lab=1&cat=1@0&opd="+pin+date_str;
				window.location="processing.php?param=82&uhid="+uhid+"&lab=1&cat=1@0&opd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to LAB</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="10")
		{
			if(access>0)
			{
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
				//~ window.location="processing.php?param=3&uhid="+uhid+"&lab=1&cat=2@128&opd="+pin+date_str;
				window.location="processing.php?param=840&uhid="+uhid+"&lab=1&cat=1@0&opd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to LAB</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="11")
		{
			if(access>0)
			{
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1&cat=2@40&opd="+pin+date_str;
				window.location="processing.php?param=841&uhid="+uhid+"&lab=1&cat=1@0&opd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to LAB</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="12")
		{
			if(access>0)
			{
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1&cat=3@131&opd="+pin+date_str;
				window.location="processing.php?param=843&uhid="+uhid+"&lab=1&cat=1@0&opd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to LAB</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="13")
		{
			if(access>0)
			{
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1&cat=2@121&opd="+pin+date_str;
				window.location="processing.php?param=842&uhid="+uhid+"&lab=1&cat=1@0&opd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to LAB</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="16")
		{
			if(access>0)
			{
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1&cat=2@126&opd="+pin+date_str;
				window.location="processing.php?param=844&uhid="+uhid+"&lab=1&cat=1@0&opd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="5")
		{
			if(access>0)
			{
				//window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
				window.location="processing.php?param=132&uhid="+uhid+"&ipd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to Day Care Dashboard</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}else if(type=="4")
		{
			if(access>0)
			{
				window.location="processing.php?param=85&uhid="+uhid+"&ipd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to Casualty</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="6")
		{
			if(access>0)
			{
				window.location="processing.php?param=809&uhid="+uhid+"&ipd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to Casualty</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="7")
		{
			if(access>0)
			{
				window.location="processing.php?param=806&uhid="+uhid+"&ipd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to Casualty</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="8")
		{
			if(access>0)
			{
				window.location="processing.php?param=119&uhid="+uhid+"&ipd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to Baby Dashboard</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="9")
		{
			if(access>0)
			{
				window.location="processing.php?param=816&uhid="+uhid+"&ipd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to Baby Dashboard</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="14")
		{
			if(access>0)
			{
				window.location="processing.php?param=820&uhid="+uhid+"&ipd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to Dashboard</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
		else if(type=="15")
		{
			if(access>0)
			{
				window.location="processing.php?param=129&uhid="+uhid+"&ipd="+pin+date_str;
			}else
			{
				bootbox.dialog({ message: "<h5>You don't have access to Dashboard</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},2000);
			}
		}
	}
	function redirect_page_rel(uhid,rel)
	{
		if(rel==0)
		{
			window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
		}else
		{
			window.location="processing.php?param=3&uhid="+uhid+"&consult=1";
		}
	}
	function update_patient(uhid)
	{
		window.location="processing.php?param=1&uhid="+uhid;
	}
	function delete_no_encounter(uhid)
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
						$.post("pages/delete_no_encounter.php",
						{
							type:"delete_no_encounter",
							pat_uhid:uhid,
						},
						function(data,status)
						{
							bootbox.dialog({ message: "<h5>Deleted</h5>"});
							setTimeout(function(){
								bootbox.hideAll();
								view_all();
							},2000);
						})
					}
				}
			}
		});
	}
	function delete_request_up(uhid,opd_id)
	{
		$("#cancel_request_btn").click();
		$.post("pages/request_delete.php",
		{
			type:"delete_request_div",
			pat_uhid:uhid,
			opd_id:opd_id,
		},
		function(data,status)
		{
			$("#cancel_request_data").html(data);
		})
	}
	function del_request_change(uhid,pin,val)
	{
		var already_request=$("#already_request").val();
		//send_cancel_request_btn
		if(val==1)
		{
			$("#cancel_p").show();
			$("#send_cancel_request_btn").show();
		}
		if(val==0)
		{
			$("#cancel_p").hide();
			if(already_request==0)
			{
				$("#send_cancel_request_btn").hide();
			}
			if(already_request==1)
			{
				$("#send_cancel_request_btn").show();
			}
		}
	}
	function send_cancel_request()
	{
		var del_request=$('#del_request:checked').val();
		if(del_request==1)
		{
			var cancel_reason=$("#cancel_reason").val();
			if(cancel_reason=="")
			{
				$("#cancel_reason").focus();
				return false;
			}
		}
		
		if(del_request==1)
		{
			var msg="Are you sure ?";
		}
		if(del_request==0)
		{
			var msg="Are you sure ?";
		}
		bootbox.dialog({
			message: "<h5>"+msg+"</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					  view_all();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-danger",
					callback: function() {
						ok_send_request();
					}
				}
			}
		});
	}
	function ok_send_request()
	{
		var val=$('#del_request:checked').val();
		$.post("pages/request_delete.php",
		{
			type:"request_delete",
			pat_uhid:$("#cancel_uhid").val(),
			cancel_opd_id:$("#cancel_opd_id").val(),
			val:val,
			reason:$("#cancel_reason").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#cancel_request_btn").click();
			if(val==1)
			{
				var msg="Request Sent";
			}
			if(val==0)
			{
				var msg="Request Canceled";
			}
			bootbox.dialog({ message: "<h5>"+msg+"</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
				view_all();
			},2000);
		})
	}
	function confirm_delete_request(uhid,pin,val,reason)
	{
		$.post("pages/request_delete.php",
		{
			type:"request_delete",
			pat_uhid:uhid,
			pin:pin,
			val:val,
			reason:reason,
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			if(val==1)
			{
				var msg="Request Sent";
			}
			if(val==0)
			{
				var msg="Request Canceled";
			}
			bootbox.dialog({ message: "<h5>"+msg+"</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
				view_all();
			},2000);
		})
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
label
{
	display: inline;
}
.modal.fade.in {
    top: 1%;
}
</style>
<?php
//~ $post = [
    //~ 'username' => 'user1',
    //~ 'password' => 'passuser1',
    //~ 'gender'   => 1,
//~ ];
//~ $ch = curl_init();
//~ curl_setopt($ch, CURLOPT_URL, 'http://e-adware.com/website/index.php/contact');
//~ curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//~ curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
//~ $response = curl_exec($ch);
//~ var_export($response);
?>
