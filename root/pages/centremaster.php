<?php
$emp_id = trim($_SESSION["emp_id"]);
$branch_display = "display:none;";
if ($p_info["levelid"] == 1) {
	$branch_str = "";

	$branch_display = "display:none;";
	$branch_num = mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if ($branch_num > 1) {
		$branch_display = "display:;";
	}

	$dept_sel_dis = "";
} else {
	$branch_str = " AND branch_id='$p_info[branch_id]'";
	$branch_display = "display:none;";

	$dept_sel_dis = "disabled";
}

$branch_id = $p_info["branch_id"];
?>
<!--header-->


<div id="content-header">
	<div class="header_div"> <span class="header"> Center Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span8">
			<table class="table   table-bordered table-condensed">
				<tr>
				<td style="color:black; font-weight:bold;">ID</td>

					<td colspan="5"><input type="text" name="txtid" id="txtid" class="imp" readonly="readonly" /></td>
					<input type="hidden" name="txtlgid" id="txtlgid" value="" autocomplete="off" />
				</tr>


				<tr style="<?php echo $branch_display; ?>">
					<td style="color:black; font-weight:bold;">Branch</td>
					<td colspan="5">
						<select id="branch_id" class="" onchange="lod_refraldoctor()"
							style="<?php echo $element_style; ?>">
							<?php
							$qry = mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
							while ($data = mysqli_fetch_array($qry)) {
								if ($data["branch_id"] == $p_info["branch_id"]) {
									$branch_sel = "selected";
								} else {
									$branch_sel = "";
								}
								echo "<option value='$data[branch_id]' $branch_sel>$data[name]</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="color:black; font-weight:bold;">Name</td>
					<td colspan="5"><input type="text" name="intext1" id="txtname" class="intext" size="35" value=""
							autocomplete="off" /></td>
				</tr>

				<tr>
					<td style="color:black; font-weight:bold;">Short Name</td>
					<td colspan="5"><input type="text" name="intext0" id="txtshortname" class="intext" size="35"
							value="" autocomplete="off" /></td>
				</tr>
				<tr>
					<td style="color:black; font-weight:bold;">Address</td>
					<td colspan="5"><input type="text" name="intext2" id="txtaddress" class="intext" size="35" value=""
							autocomplete="off" /></td>
				</tr>
				<tr>
					<td style="color:black; font-weight:bold;">Contact Person</td>
					<td colspan="5"><input type="text" name="intext3" id="txtcontactperson" size="35" class="intext"
							autocomplete="off" /></td>
				</tr>

				<tr>
					<td style="color:black; font-weight:bold;">Phone</td>
					<td colspan="5"><input type="text" name="intext4" id="txtphon" size="35" class="intext"
							autocomplete="off" />
					</td>
				</tr>


				<tr>
					<td style="color:black; font-weight:bold;">Email</td>
					<td colspan="5"><input type="text" name="intext5" id="txtemail" size="35" class="intext"
							autocomplete="off" />
					</td>
				</tr>


				<td style="color:black; font-weight:bold;">Credit Limit</td>
				<td colspan="5"><input type="text" name="intext6" id="txtcreditlimit" size="35" class="intext"
						autocomplete="off" /></td>
				</tr>



				<tr>
					<td style="color:black; font-weight:bold;">Test discount</td>
					<td colspan="5"><input type="text" name="intext7" id="txtdiscount" size="35" class="intext"
							autocomplete="off" /></td>
				</tr>




				<!-- new modify -->
				<tr>
					<td style="color:black; font-weight:bold;">onLine</td>
					<td colspan="5"><input type="text" name="intext8" id="txtonline" size="35" class="intext"
							autocomplete="off" /></td>
				</tr>

				<tr>
					<td style="color:black; font-weight:bold;">ShowBalance</td>
					<td colspan="5"><input type="text" name="intext9" id="txtshowbalance" size="35" class="intext"
							autocomplete="off" /></td>
				</tr>

				<tr>
					<td style="color:black; font-weight:bold;">Cash</td>
					<td colspan="5"><input type="text" name="intext10" id="txtcash" size="35" class="intext"
							autocomplete="off" /></td>
				</tr>

				<tr>
					<td style="color:black; font-weight:bold;">LoginID</td>
					<td colspan="5"><input type="text" name="intext11" id="txtloginid" size="35" class="intext"
							autocomplete="off" /></td>
				</tr>





				<table>
					<tr style="color:black; font-weight:bold;">
						<td>M.Patho:</td>
						<td><input type="text" class="text-input" id="d_patho" style="width: 62px;" value=""
								name="d_patho">
						</td>
						<td>M.Ultra:</td>
						<td><input type="text" class="text-input" id="d_ultra" style="width: 62px;" value=""
								name="d_ultra">
						</td>
						<td>M.Xray:</td>
						<td><input type="text" class="text-input" id="d_xray" style="width: 62px;" value=""
								name="d_xray">
						</td>
						<td>M.Cardio:</td>
						<td><input type="text" class="text-input" id="d_cardio" style="width: 62px;" value=""
								name="d_cardio"></td>
						<td>M.SPL:</td>
						<td><input type="text" class="text-input" id="d_spl" style="width: 62px;" value="" name="d_spl">
						</td>
						<td>M.CT:</td>
						<td><input type="text" class="text-input" id="d_ct" style="width: 62px;" value="" name="d_ct">
						</td>
					</tr>

					<tr style="color:black; font-weight:bold;">
						<td>C.Patho:</td>
						<td><input type="text" class="text-input" id="c_patho" style="width: 62px;" value=""
								name="c_patho">
						</td>
						<td>C.Ultra:</td>
						<td><input type="text" class="text-input" id="c_ultra" style="width: 62px;" value=""
								name="c_ultra">
						</td>
						<td>C.Xray:</td>
						<td><input type="text" class="text-input" id="c_xray" style="width: 62px;" value=""
								name="c_xray">
						</td>
						<td>C.Cardio:</td>
						<td><input type="text" class="text-input" id="c_cardio" style="width: 62px;" value=""
								name="c_cardio"></td>
						<td>C.SPL:</td>
						<td><input type="text" class="text-input" id="c_spl" style="width: 62px;" value="" name="c_spl">
						</td>
						<td>C.CT:</td>
						<td><input type="text" class="text-input" id="c_ct" style="width: 62px;" value="" name="c_ct">
						</td>
					</tr>

					<tr style="color:black; font-weight:bold;">
						<td>S.Patho:</td>
						<td><input type="text" class="text-input" id="s_patho" style="width: 62px;" value=""
								name="s_patho">
						</td>
						<td>S.Ultra:</td>
						<td><input type="text" class="text-input" id="s_ultra" style="width: 62px;" value=""
								name="s_ultra">
						</td>
						<td>S.Xray:</td>
						<td><input type="text" class="text-input" id="s_xray" style="width: 62px;" value=""
								name="s_xray">
						</td>
						<td>S.Cardio:</td>
						<td><input type="text" class="text-input" id="s_cardio" style="width: 62px;" value=""
								name="s_cardio"></td>
						<td>S.SPL:</td>
						<td><input type="text" class="text-input" id="s_spl" style="width: 62px;" value="" name="s_spl">
						</td>
						<td>S.CT:</td>
						<td><input type="text" class="text-input" id="s_ct" style="width: 62px;" value="" name="s_ct">
						</td>
					</tr>
				</table>

				<br><br>

				<table style="color:black; font-weight:bold;">
					<tr>
						<td ><label><input type="checkbox" id="vacu_charge" name="vacu_charge"
									style="width: 20px;">VacuCharge</label></td>
						<td><label><input type="checkbox" id="delv_recp" name="delv_recp" style="width: 20px;">Delv
								Recp</label>
						</td>
						<td><label><input type="checkbox" id="moneyreceipt" name="money-receipt" style="width: 20px;">
								Money_Receipt_Full_rate</label></td>

						<td><label><input type="checkbox" id="not_required" name="not-required" style="width: 20px;">
								Not_required</label>

					</tr>

					<tr>

						</td>
						<td><label><input type="checkbox" id="ful_cred" name="full-credit" style="width: 20px;">
								Full_Credit</label></td>
						<td><label><input type="checkbox" id="discount_flag" name="discount" style="width: 20px;">
								Discount_Flag</label></td>
						<td><label><input type="checkbox" id="d_normal_com" name="dnc" style="width: 20px;">
								D_Normal_comm</label></td>
						<td><label><input type="checkbox" id="blnce_rpt_print" name="brp" style="width: 20px;">
								Balance_Report_Print</label></td>
					</tr>
				</table>

				<br>



				<center>
					<tr>
						<td colspan="5" style="text-align:center">
							<input type="button" name="intext18" id="button" value="Submit" onclick="insert();"
								class="btn btn-info" />
							<input type="button" name="button2" id="button2" onclick="clearr();" value="Reset"
								class="btn btn-danger" />
							<input type="button" name="button3" id="button3"
								onclick="popitup('pages/center_list_rpt.php');" value="View" class="btn btn-success" />
						</td>
					</tr>
				</center>
			</table>
		</div>


		<!-- right side data -->
		<div class="span6">
			<table class="table   table-bordered table-condensed">
				<tr>
					<td style="color:black; font-weight:bold;" colspan="5">Name: <input type="text" id="txtdoc" size="320"
							onkeyup="sel_pr(this.value,event)" />
					</td>
				</tr>
			</table>
			<div style="height:500px; overflow-x:hidden" id="laod_doctor">
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function () {
		lod_refraldoctor();
		get_id();
	});
	var doc_v = 1;
	var doc_sc = 0;
	function sel_pr(val, e) ///for load patient 
	{

		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 13) {
			var chk = $("#chk").val();
			if (chk != "0") {
				var prod = document.getElementById("prod" + doc_v).innerHTML;
				val_load_new(prod);

			}
		}
		else if (unicode == 40) {
			$("#chk").val("1");
			var chk = doc_v + 1;
			var cc = document.getElementById("rad_test" + chk).innerHTML;
			if (cc) {
				doc_v = doc_v + 1;
				$("#rad_test" + doc_v).css({ 'font-weight': 'bold', 'color': 'red', 'transform': 'scale(0.55)', 'transition': 'all .2s' });
				var doc_v1 = doc_v - 1;
				$("#rad_test" + doc_v1).css({ 'font-weight': 'normal', 'color': 'black', 'transform': 'scale(1.0)', 'transition': 'all .2s' });
				var z2 = doc_v % 3;
				if (z2 == 0) {
					$("#laod_doctor").scrollTop(doc_sc)
					doc_sc = doc_sc + 50;
				}
			}
		}
		else if (unicode == 38) {
			$("#chk").val("1");
			var chk = doc_v - 1;
			var cc = document.getElementById("rad_test" + chk).innerHTML;
			if (cc) {
				doc_v = doc_v - 1;
				$("#rad_test" + doc_v).css({ 'font-weight': 'bold', 'color': 'red', 'transform': 'scale(0.55)', 'transition': 'all .2s' });
				var doc_v1 = doc_v + 1;
				$("#rad_test" + doc_v1).css({ 'font-weight': 'normal', 'color': 'black', 'transform': 'scale(1.0)', 'transition': 'all .2s' });
				var z2 = doc_v % 3;
				if (z2 == 0) {
					doc_sc = doc_sc - 50;
					$("#laod_doctor").scrollTop(doc_sc);
				}
			}
		}
		else {
			$.post("pages/centremaster_data.php",
				{
					val: val,
					type: "cntermaster",
					branch_id: $("#branch_id").val(),
				},
				function (data, status) {
					$("#laod_doctor").html(data);
				})
		}
	}

	function popitup(url) {
		var branch_id = $("#branch_id").val();
		url = url + "?branch_id=" + btoa(branch_id);
		newwindow = window.open(url, 'window', 'left=10,top=10,height=600,witdh=600,menubar=1,resizeable=0,scrollbars=1');
	}


	function numentry(id) //for Numeric value support in the text field
	{
		var num = document.getElementById(id);

		var numex = /^[0-5]+$/;
		//var nume=/a-z/
		if (!num.value.match(numex)) {
			num.value = "";
		}
	}

	function lod_refraldoctor() {
		$.post("pages/centremaster_data.php",
			{
				type: "cntermaster",
				branch_id: $("#branch_id").val(),
			},
			function (data, status) {
				$("#laod_doctor").html(data)

			})
	}
	function get_focus() {
		$("#txtname").focus();
	}

	function get_id()//For get refdoctor Id
	{
		$.post("pages/centremaster_data.php",
			{
				type: "cntermaster_id",
			},
			function (data, status) {
				$("#txtid").val(data);
				get_focus();
			})

	}

	function val_load_new(doid1) {
		$.post("pages/centremaster_data.php",
			{
				type: "cntermaster_load",
				doid1: doid1,
			},
			function (data, status) {
				var val = data.split("#");

				//onlick data show
				document.getElementById("txtid").value = val[0];
				document.getElementById("txtname").value = val[1];
				document.getElementById("txtshortname").value = val[2];
				document.getElementById("txtaddress").value = val[3];
				document.getElementById("txtcontactperson").value = val[4];
				document.getElementById("txtphon").value = val[5];
				document.getElementById("txtemail").value = val[6];
				document.getElementById("txtcreditlimit").value = val[7];
				document.getElementById("txtdiscount").value = val[8];

				document.getElementById("d_patho").value = val[9];
				document.getElementById("d_ultra").value = val[10];
				document.getElementById("d_xray").value = val[11];
				document.getElementById("d_cardio").value = val[12];
				document.getElementById("d_spl").value = val[13];
				document.getElementById("d_ct").value = val[14];


				document.getElementById("c_patho").value = val[15];
				document.getElementById("c_ultra").value = val[16];
				document.getElementById("c_xray").value = val[17];
				document.getElementById("c_cardio").value = val[18];
				document.getElementById("c_spl").value = val[19];
				document.getElementById("c_ct").value = val[20];

				document.getElementById("s_patho").value = val[21];
				document.getElementById("s_ultra").value = val[22];
				document.getElementById("s_xray").value = val[23];
				document.getElementById("s_cardio").value = val[24];
				document.getElementById("s_spl").value = val[25];
				document.getElementById("s_ct").value = val[26];




				if (val[27] == 1) {
					document.getElementById("vacu_charge").checked = true;
				} else {
					document.getElementById("vacu_charge").checked = false;
				}

				if (val[28] == 1) {
					document.getElementById("delv_recp").checked = true;
				} else {
					document.getElementById("delv_recp").checked = false;
				}

				if (val[29] == 1) {
					document.getElementById("moneyreceipt").checked = true;
				} else {
					document.getElementById("moneyreceipt").checked = false;
				}

				if (val[30] == 1) {
					document.getElementById("not_required").checked = true;
				} else {
					document.getElementById("not_required").checked = false;
				}

				if (val[31] == 1) {
					document.getElementById("ful_cred").checked = true;
				} else {
					document.getElementById("ful_cred").checked = false;
				}

				if (val[32] == 1) {
					document.getElementById("discount_flag").checked = true;
				} else {
					document.getElementById("discount_flag").checked = false;
				}

				if (val[33] == 1) {
					document.getElementById("d_normal_com").checked = true;
				} else {
					document.getElementById("d_normal_com").checked = false;
				}

				if (val[34] == 1) {
					document.getElementById("blnce_rpt_print").checked = true;
				} else {
					document.getElementById("blnce_rpt_print").checked = false;
				}


				document.getElementById("txtonline").value = val[35];
				document.getElementById("txtshowbalance").value = val[36];
				document.getElementById("txtcash").value = val[37];
				document.getElementById("txtloginid").value = val[38];


			}
		)
	}

	function tab_next(e) {
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 13) {
			var act = document.activeElement.id;
			if (!act) {
				document.getElementById("info1").focus();
			}
			else {
				var clsn = $("#" + act).attr("class");
				var nam = $("#" + act).attr("name");
				var val = nam.replace(/^\D+/g, '');
				val = parseInt(val) + 1;
				document.getElementsByName(clsn + val)[0].focus();
			}
		}
	}

	function clearr() {

		//write your code here
		location.reload();


	}

	function insert() {
		///////////////For Check blanj Field
		// var jj = 1;
		// var chk = document.getElementsByClassName("imp")
		// for (var i = 0; i < chk.length; i++) {
		// 	if (chk[i].value == "") {
		// 		document.getElementById(chk[i].id).placeholder = "Can not be blank";
		// 		jj = 0;
		// 	}

		// }

		// var vname = document.getElementById("txtname").value;
		// if (vname == "") {
		// 	alert("Please enter the Center Name..");
		// 	jj = 0;
		// 	$("#txtname").focus();
		// }



		if ($("#txtname").val() == "") {


			alert("Name field is empty");
			$("#txtname").focus();
		}
		else {


			$.post("pages/centremaster_data.php",
				{
					type: "cntermaster_save",
					id: $("#txtid").val(),
					branch: $("#branch_id").val(),
					name: $("#txtname").val(),
					short_name: $("#txtshortname").val(),
					address: $("#txtaddress").val(),
					contactperson: $("#txtcontactperson").val(),
					phone: $("#txtphon").val(),
					email: $("#txtemail").val(),
					creditlimit: $("#txtcreditlimit").val(),
					testdiscount: $("#txtdiscount").val(),

					mpatho: $("#d_patho").val(),
					multra: $("#d_ultra").val(),
					mxray: $("#d_xray").val(),
					mcardio: $("#d_cardio").val(),
					mspl: $("#d_spl").val(),
					mct: $("#d_ct").val(),

					cpatho: $("#c_patho").val(),
					cultra: $("#c_ultra").val(),
					cxray: $("#c_xray").val(),
					ccardio: $("#c_cardio").val(),
					cspl: $("#c_spl").val(),
					cct: $("#c_ct").val(),


					spatho: $("#s_patho").val(),
					sultra: $("#s_ultra").val(),
					sxray: $("#s_xray").val(),
					scardio: $("#s_cardio").val(),
					sspl: $("#s_spl").val(),
					sct: $("#s_ct").val(),


					vacucharge: $("#vacu_charge:checked").length,
					delvrecp: $("#delv_recp:checked").length,
					moneyreceipt: $("#moneyreceipt:checked").length,
					notrequired: $("#not_required:checked").length,
					fullcredit: $("#ful_cred:checked").length,
					discountflag: $("#discount_flag:checked").length,
					dnormal: $("#d_normal_com:checked").length,
					balanceprint: $("#blnce_rpt_print:checked").length,

					//new modify
					online: $("#txtonline").val(),
					showbalance: $("#txtshowbalance").val(),
					cash: $("#txtcash").val(),
					loginid: $("#txtloginid").val(),


				},
				function (data, status) {
					alert(data);
					lod_refraldoctor();
					get_id();

					location.reload();
				});
		}
	}





	function delete_data(subp)//for delete
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete this test</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function () {
						bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-danger",
					callback: function () {
						$.post("pages/centremaster_data.php",
							{
								subp: subp,
								type: "cntermaster_delete",
							},
							function (data, status) {
								alert(data);
								lod_refraldoctor();
								get_id();
								clearr();
							})
					}
				}
			}
		});
	}

</script>
<style>
	/*.reference{
	width:700px;}
.reference td{
	padding:5px;
	text-align:center;
	height:5px;
	width:auto;
	min-width:100px;}
	*/
	.reference td img {
		margin: 0;
	}
</style>