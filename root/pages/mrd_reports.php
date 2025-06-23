<?php
$p_info["branch_id"] = 1;
if ($p_info["levelid"] == 1 && $p_info["branch_id"] == 1) {
	$branch_str = "";
	$branch_display = "display:none;";

	$dept_sel_dis = "";
} else {
	$branch_str = " AND branch_id='$p_info[branch_id]'";
	$branch_display = "display:none;";

	$dept_sel_dis = "disabled";
}

$branch_id = $p_info["branch_id"];

$uhid_str = base64_decode($_GET['uhid_str']);
$pin_str = base64_decode($_GET['pin_str']);
$fdate_str = base64_decode($_GET['fdate_str']);
$tdate_str = base64_decode($_GET['tdate_str']);
$name_str = base64_decode($_GET['name_str']);
$phone_str = base64_decode($_GET['phone_str']);
$param_str = base64_decode($_GET['param_str']);
$pat_type_str = base64_decode($_GET['pat_type_str']);
?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div id="input_div">
		<table class="table table-bordered text-center">
			<tr>
				<td colspan="2">
					<b style="display:none;">Patient Type</b>
					<select class="span2" id="pat_type" style="display:none;" onChange="view_all()">
						<!--<option value="0">All</option>-->
						<?php
						$qq_qry = mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `status`=0 AND `p_type_id`=3 ORDER BY `p_type_id` ");
						while ($qq = mysqli_fetch_array($qq_qry)) {
							$sel = "";
							if ($pat_type_str) {
								if ($pat_type_str == $qq["p_type_id"]) {
									$sel = "selected";
								} else {
									$sel = "";
								}
							}
							echo "<option value='$qq[p_type_id]' $sel>$qq[p_type]</option>";
						}
						?>
					</select>
					<select id="branch_id" class="span3" style="<?php echo $branch_display; ?>" onChange="view_all()">
						<?php
						$branch_qry = mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while ($branch = mysqli_fetch_array($branch_qry)) {
							if ($branch_id == $branch["branch_id"]) {
								$branch_sel = "selected";
							} else {
								$branch_sel = "";
							}
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
					<span style="float:right;display:none;">
						<span style="cursor:pointer;" onclick="department_status(1)" title="Summary"><i class="icon-th-list icon-large"></i></span>
						<span style="cursor:pointer;" onclick="department_status(2)" title="Details"><i class="icon-list icon-large"></i></span>
					</span>
				</td>
			</tr>
			<tr style="display:none;">
				<td>
					<b>From</b><!-- <?php echo date("Y-m-d"); ?> -->
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo $fdate_str; ?>">
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo $tdate_str; ?>">
					<button class="btn btn-search" onClick="view_all()" style="margin-top: -1%;"><i class="icon-search"></i> Search</button>
				</td>
				<td>
					<b>Name</b>
					<input type="text" class="span2" id="pat_name" onKeyup="view_all()" value="<?php echo $name_str; ?>">
				</td>
			</tr>
			<tr>
				<td>
					<b>UHID</b>
					<input list="browsrs" type="text" class="span2" id="uhid" value="<?php echo $uhid_str; ?>">
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
					<input list="browsr" type="text" class="span2" id="pin" value="<?php echo $pin_str; ?>">
					<datalist id="browsr">
						<?php
						//~ $oid= mysqli_query($link," SELECT `opd_id` FROM `uhid_and_opdid` ORDER BY `slno` DESC ");
						//~ while($pat_oid=mysqli_fetch_array($oid))
						//~ {
						//~ echo "<option value='$pat_oid[opd_id]'>";
						//~ }
						?>
					</datalist>
					<button class="btn btn-info" onClick="view_all()" style="margin-top: -1%;"><i class="icon-search"></i> Search</button>
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
				<td style="display:none;">
					<b>Phone</b>
					<input type="text" class="span2" id="phone" onKeyup="view_all()" value="<?php echo $phone_str; ?>">
				</td>
			</tr>
		</table>
	</div>
	<div id="load_all" class="ScrollStyle" style="display:none;">

	</div>
</div>
<input type="hidden" id="list_start" value="50">
<!-- Loader -->
<div id="loader" style="margin-top:0%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<!-- Time -->
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<script>
	$(document).ready(function() {
		$("#loader").hide();
		view_all();
		$(".datepicker").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		$("#pat_type").change(function() {
			view_all();
		});

		$('#load_all').on('scroll', function() {
			var div_height = $(this).get(0).scrollHeight;
			var div = $(this).get(0);

			if (div.scrollTop + div.clientHeight >= div.scrollHeight) {
				var list_start = $("#list_start").val().trim();
				list_start = parseInt(list_start) + 50;
				$("#list_start").val(list_start);
				view_all();
			}
		});

	});
	$(document).tooltip();

	function department_status(n) {
		var url = "pages/department_status.php?dt1=" + btoa($('#from').val()) + "&dt2=" + btoa($('#to').val()) + "&tYp=" +
			btoa(n);
		wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}

	function change_state(val) {
		$.post("pages/mrd_reports_data.php", {
				type: "load_district_pat",
				val: val,
			},
			function(data, status) {
				$("#district").html(data);
				view_all();
			})
	}

	function view_all() {
		$("#loader").show();
		$.post("pages/mrd_reports_data.php", {
				type: "load_all_pat",
				pat_type: $("#pat_type").val(),
				branch_id: $("#branch_id").val(),
				from: $("#from").val(),
				to: $("#to").val(),
				pat_name: $("#pat_name").val(),
				pat_uhid: $("#uhid").val(),
				pin: $("#pin").val(),
				phone: $("#phone").val(),
				state: $("#state").val(),
				district: $("#district").val(),
				ref_doc_id: $("#ref_doc_id").val(),
				health_guide_id: $("#health_guide_id").val(),
				list_start: $("#list_start").val(),
			},
			function(data, status) {
				$("#loader").hide();
				//$("#load_all").show().html(data);
				$("#input_div").slideDown(500);
				$("#load_all").slideUp(500, function() {
					$("#load_all").html(data).slideDown(500);
				});
			})
	}

	function redirect_page(uhid, pin, type, access) {
		$("#loader").show();
		$.post("pages/mrd_reports_data.php", {
				type: "load_all_pat_mrd",
				uhid: uhid,
				pin: pin,
			},
			function(data, status) {
				$("#loader").hide();
				//$("#load_all").show().html(data);
				$("#input_div").slideUp(500);
				$("#load_all").slideUp(500, function() {
					$("#load_all").html(data).slideDown(500);
				});
			})
	}

	function print_mrd(typ, uid, bid) {
		var user = $("#user").text().trim();
		if (typ == "Patient Discharge Summary") {
			url = "pages/ipd_discharge_summary.php?v=" + btoa(0) + "&uhid=" + btoa(uid) + "&ipd=" + btoa(bid) + "&user=" +
				btoa(user);
		} else {
			url = "pages/mrd_reports_print.php?v=" + btoa(0) + "&typ=" + btoa(typ) + "&uid=" + btoa(uid) + "&bid=" + btoa(
				bid) + "&usr=" + btoa(user);
		}
		window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
	.ScrollStyle {
		max-height: 400px;
		overflow-y: scroll;
	}

	label {
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