<?php
$q1=mysqli_query($link,"SELECT *  FROM `Testparameter_old` order by TestId ");
while($q2=mysqli_fetch_array($q1))
{
	
		$q4=mysqli_fetch_array(mysqli_query($link,"SELECT `SampleId` FROM `TestSample` WHERE `TestId`='$q2[TestId]'"));
		$q5=mysqli_fetch_array(mysqli_query($link,"SELECT `vac_id` FROM `test_vaccu` WHERE `testid`='$q2[TestId]'"));
		//$qchkslno=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `Testparameter` WHERE `TestId`='$q2[TestId]' and `sample`=0 order by `slno` limit 0,1"));
		//mysqli_query($link,"UPDATE `Testparameter` SET `sample`='$q4[SampleId]',`vaccu`='$q5[vac_id]' where `TestId`='$q2[TestId]' ");
		mysqli_query($link,"INSERT INTO `Testparameter`(`TestId`, `ParamaterId`, `sequence`, `sample`, `vaccu`, `status`) VALUES ('$q2[TestId]','$q2[ParamaterId]','$q2[sequence]','$q4[SampleId]','$q5[vac_id]','0') ");
		
	
}
echo "Done";
?>
<?php
if ($p_info["levelid"] == 1) {
	$branch_str = "";

	$branch_display = "display:none;";
	$branch_num = mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if ($branch_num > 1) {
		$branch_display = "display:;";
	}
} else {
	$branch_str = " AND branch_id='$p_info[branch_id]'";
	$branch_display = "display:none;";
}

$branch_id = $p_info["branch_id"];
?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span>
		<span style="float:right;">
			<select id="branch_id" class="span2" onChange="load_all_test()" style="<?php echo $branch_display; ?>">
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
			<button class="btn btn-save" onClick="show_test_list()"><i class="icon-save"></i> Print Test List</button>
		</span>
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<!--<td>
				Search:<input type="text" id="tname" name="tname" onKeyUp="load_test(this.value,event)" />
			</td>-->

			<td colspan="3">
				<select id="ser_category_id" onchange="ser_category_change()">
					<option value="0">--Select Category--</option>
					<?php
					$dep_sr = mysqli_query($link, "SELECT `category_id`, `name` FROM `test_category_master` WHERE `category_id`>0 AND `status`=0 ORDER BY `category_id` ASC");
					while ($d_s = mysqli_fetch_array($dep_sr)) {
						echo "<option value='$d_s[category_id]'>$d_s[name]</option>";
					}
					?>
				</select>
				<select id="ser_dep" onchange="load_all_test()">
					<option value="0">--Select Department--</option>
					<?php
					$dep_sr = mysqli_query($link, "select * from test_department ORDER BY `name` ASC");
					while ($d_s = mysqli_fetch_array($dep_sr)) {
						echo "<option value='$d_s[id]'>$d_s[name]</option>";
					}
					?>
				</select>
				<select id="ser_samp" onchange="load_all_test()">
					<option value="0">--Select Sample--</option>
					<?php
					$sam_sr = mysqli_query($link, "select * from Sample");
					while ($s_s = mysqli_fetch_array($sam_sr)) {
						echo "<option value='$s_s[ID]'>$s_s[Name]</option>";
					}
					?>
				</select>
				<select id="ser_vac" onchange="load_all_test()">
					<option value="0">--Select Vaccu--</option>
					<?php
					$vac_sr = mysqli_query($link, "select * from vaccu_master");
					while ($v_s = mysqli_fetch_array($vac_sr)) {
						echo "<option value='$v_s[id]'>$v_s[type]</option>";
					}
					?>
				</select>
				<?php
				$instrument_test_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `testmaster` WHERE `equipment`>0"));
				if ($instrument_test_num > 0) {
					?>
					<select id="ser_equipment" onchange="load_all_test()">
						<option value="0">Select Vaccu</option>
						<?php
						$instrument_qry = mysqli_query($link, "SELECT `id`, `name` FROM `lab_instrument_master` WHERE `status`=0 ORDER  BY `name` ASC");
						while ($instrument = mysqli_fetch_array($instrument_qry)) {
							echo "<option value='$instrument[id]'>$instrument[name]</option>";
						}
						?>
					</select>
					<?php
				}
				?>
			</td>
			<td>
				<button class="btn btn-new" onClick="load_test_info(0)"><i class="icon-edit"></i> Create New
					Test</button>
			</td>
		</tr>
	</table>
	<p>
		<span>
			Show item
			<select class="span1" id="limit_no" onchange="load_all_test()">
				<option value="10">10</option>
				<option value="50">50</option>
				<option value="100">100</option>
				<option value="200">200</option>
				<option value="500">500</option>
				<option value="1000">1000</option>
				<option value="2000">2000</option>
				<option value="5000" selected>5000</option>
			</select>
		</span>
		<span style="float: right;">
			<input type="text" id="search_data" onkeyup="load_all_test()" placeholder="Search">
		</span>
	</p>
	<div id="load_all_test">

	</div>

	<div id="back" onClick="$('#results').slideUp(500);$(this).fadeOut(100);$('#pinfo').fadeOut(200)"></div>

	<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none" />
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results"> </div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="button" data-toggle="modal" data-target="#myModal2" id="mod2" style="display:none" />
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results2"> </div>
			</div>
		</div>
	</div>
</div>
<div id="loader" style="position:fixed;z-index:99999;display:none;"></div>
<script src="../js/jquery.dataTables.min_all.js"></script>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>

	$(document).ready(function () {
		load_all_test();
	});

	function ser_category_change() {
		$("#loader").show();
		$.post("pages/load_all_test_data.php",
			{
				type: "load_departments",
				category_id: $("#ser_category_id").val(),
			},
			function (data, status) {
				$("#loader").hide();
				//alert(data);
				$("#ser_dep").html(data);

				setTimeout(function () {
					load_all_test();
				}, 100);
			});
	}
	function load_all_test() {
		$("#loader").show();
		$.post("pages/load_all_test_data.php",
			{
				type: "load_all_test",
				ser_vac: $("#ser_vac").val(),
				ser_dep: $("#ser_dep").val(),
				ser_category_id: $("#ser_category_id").val(),
				ser_samp: $("#ser_samp").val(),
				user: $("#user").text().trim(),
				limit_no: $("#limit_no").val(),
				search_data: $("#search_data").val(),
				branch_id: $("#branch_id").val(),
				equipment: $("#ser_equipment").val(),
			},
			function (data, status) {
				$("#load_all_test").html(data);
				//~ $('.data-table').dataTable({
				//~ "bJQueryUI": true,
				//~ "sPaginationType": "full_numbers",
				//~ "sDom": '<""l>t<"F"fp>'
				//~ });
				$("#loader").hide();
			})
	}

	function test_instr(tst, val) {
		$.post("pages/load_all_test_data.php",
			{
				type: "update_instr",
				tst: tst,
				instr: val
			},
			function (data, status) {


			})
	}

	function out_sample_change(id, val) {

		$.post("pages/load_all_test_data.php",
			{
				type: "out_sample_change",
				val: val,
				id: id,
			},
			function (data, status) {
				$(".out_sample").css('border', '1px solid white');
				$("#out_sample" + id).css('border', '2px solid green');
				$("#out_sample" + id).val(data);
			})
	}
	function test_code_change_up(id, e, val) {
		$(".test_code").css('border', '');
		$(".test_code").parent().css('background', '');
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 13) {
			$.post("pages/load_all_test_data.php",
				{
					type: "test_code_change",
					val: val,
					id: id,
				},
				function (data, status) {
					//$(".test_code").css('border', '1px solid white'); 
					$("#test_code" + id).css('border', '1px solid green');
					$("#test_code" + id).parent().css('background', '#BDEFBD');
					$("#test_code" + id).val(data);
				})
		}
	}
	function test_rate_change_up(id, e, val) {
		$(".test_rate").css('border', '');
		$(".test_rate").parent().css('background', '');
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 13) {
			$.post("pages/load_all_test_data.php",
				{
					type: "test_rate_change",
					val: val,
					id: id,
				},
				function (data, status) {
					//$(".test_rate").css('border', '1px solid white'); 
					$("#test_rate" + id).css('border', '1px solid green');
					$("#test_rate" + id).parent().css('background', '#BDEFBD');
					$("#test_rate" + id).val(data);
				})
		}
	}
	function test_name_change_up(id, e, val) {
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 13) {
			$.post("pages/load_all_test_data.php",
				{
					type: "test_name_change",
					val: val,
					id: id,
				},
				function (data, status) {
					$(".test_name").css('border', '1px solid white');
					$("#test_name" + id).css('border', '2px solid green');
					$("#test_name" + id).val(data);
				})
		}
	}
	var sel_pser = 1;
	var sel_divser = 0;
	function load_test(val, e) {
		if (e) {
			var unicode = e.keyCode ? e.keyCode : e.charCode;
			if (unicode == 13) {
				var test_id = document.getElementById("test_id" + sel_pser).innerHTML;
				load_test_info(test_id)
				sel_pser = 1;
				sel_divser = 0;
			}
			else if (unicode == 40) {
				var chk = sel_pser + 1;
				var cc = document.getElementById("test" + chk).innerHTML;
				if (cc) {
					sel_pser = sel_pser + 1;
					$("#test" + sel_pser).css({ 'color': '#419641', 'transform': 'scale(0.95)', 'font-weight': 'bold', 'transition': 'all .2s' });
					var sel_pser1 = sel_pser - 1;
					$("#test" + sel_pser1).css({ 'color': 'black', 'transform': 'scale(1.0)', 'font-weight': 'normal', 'transition': 'all .2s' });
					var z2 = sel_pser % 1;
					if (z2 == 0) {
						$("#tests").scrollTop(sel_divser)
						sel_divser = sel_divser + 38;
					}
				}
			}
			else if (unicode == 38) {
				var chk = sel_pser - 1;
				var cc = document.getElementById("test" + chk).innerHTML;
				if (cc) {
					sel_pser = sel_pser - 1;
					$("#test" + sel_pser).css({ 'color': '#419641', 'transform': 'scale(0.95)', 'font-weight': 'bold', 'transition': 'all .2s' });
					var sel_pser1 = sel_pser + 1;
					$("#test" + sel_pser1).css({ 'color': 'black', 'transform': 'scale(1.0)', 'font-weight': 'normal', 'transition': 'all .2s' });
					var z2 = sel_pser % 1;
					if (z2 == 0) {
						sel_divser = sel_divser - 38;
						$("#tests").scrollTop(sel_divser);
					}
				}
			}
			else {
				$.post("pages/testmaster_ajax.php",
					{
						val: val,
						vac: $("#ser_vac").val(),
						dep: $("#ser_dep").val(),
						sam: $("#ser_samp").val()
					},
					function (data, status) {
						$("#tests").html(data);
					})
			}
		}
		else {
			$.post("pages/testmaster_ajax.php",
				{
					val: $("#tname").val(),
					vac: $("#ser_vac").val(),
					dep: $("#ser_dep").val(),
					sam: $("#ser_samp").val()
				},
				function (data, status) {
					$("#tests").html(data);
				})
		}
	}

	function load_dept() {
		$("#loader").show();
		$.post("pages/testmaster_save_ajax.php",
			{
				category_id: $("#category_id").val(),
				typ: "load_dept",
			},
			function (data, status) {
				$("#loader").hide();
				//alert(data);
				$("#type_id").find("option:not(:first)").remove();
				if (data != "") {
					var vl = data.split("#%#");
					for (var j = 0; j < vl.length; j++) {
						var v = vl[j];
						var d = v.split("@@");
						$("#type_id").append("<option value='" + d[0] + "'>" + d[1] + "</option>");
					}
				}
				if ($("#category_id").val() == 1) {
					$(".lab_tr").slideDown();
				}
				else {
					$(".lab_tr").slideUp();
				}
			});
	}

	function load_test_info(testid) {
		if (testid == 0) {
			$('#tests').animate(
				{
					scrollTop: 0
				}, 1000);
		}

		$.post("pages/testmaster_detail.php",
			{
				testid: testid
			},
			function (data, status) {

				$("#results").html(data);
				//$("#results").css({'width':'1000px'});
				//$(".modal-dialog").css({'width':'1000px','height':'1100px'});
				$("#mod").click();

				$("#results").fadeIn(500, function () { $("#results").animate({ scrollTop: 0 }, '500'); })

				/*
				$("#back").fadeIn(100);
				$("#results").html(data);
				$("#results").css({'width':'95%','height':'85%'});
				var w=$("#results").width()/2+90;
				var h=$("#results").height()/2+50;
				document.getElementById("results").style.cssText+="margin-left:-"+w+"px;margin-top:-"+h+"px";
				$("#results").slideDown(500,function(){ $("#pinfo").fadeIn(200)});
				*/

			})
	}

	function hid_div(e) {
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 27) {
			if ($("#myModal").hasClass("fade in")) {
				if ($("#myModal2").hasClass("fade in")) {
					$("#myModal2").hide();
					$("#mod2").click();
				}
				else {
					$("#myModal").hide();
					$("#mod").click();

				}
			}
		}
	}
	function add_samp(id, name) {
		var chk = $("#samp #" + id + "").text();
		if (!chk) {
			var span = "<div id=" + id + " class='samp_span' onclick='$(this).remove()'>" + name + "</div>";
			$("#samp").html($("#samp").html() + span);
		}
	}
	function save_test(testid) {
		bootbox.dialog({ message: "<b>Saving</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> " });

		var vacc = "";
		var vc = $(".vaccu_cl:checked");
		for (var j = 0; j < vc.length; j++) {
			vacc = vacc + "@" + vc[j].id;
		}

		$.post("pages/testmaster_save_ajax.php",
			{
				testid: testid,
				testname: $("#testname").val(),
				test_code: $("#test_code").val(),
				category_id: $("#category_id").val(),
				type_id: $("#type_id").val(),
				instruction: $("#instruction").val(),
				rd_day: $("#turn_day").val(),
				rd_hour: $("#turn_hour").val(),
				rd_minute: $("#turn_minute").val(),
				max_day: $("#max_turn_day").val(),
				max_hour: $("#max_turn_hour").val(),
				max_minute: $("#max_turn_minute").val(),
				report_delivery_2: $("#report_delivery_2").val(),
				sample_details: $("#sample_details").val(),
				out_sample: $("#out_sample").val(),
				vacc: vacc,
				rate: $("#rate").val(),
				sex: $("#sex").val(),
				equipment: $("#equipment").val(),
				user: $("#user").text().trim(),
				typ: 'save',
			},
			function (data, status) {
				setTimeout(function () {
					bootbox.hideAll();
					bootbox.alert(data);
				}, 2000);
				load_all_test();
			})
	}

	function save_online(id) {
		$.post("https://us-central1-medicity2.cloudfunctions.net/medicity/labtests/master/save",
			{
				authKey: "g1Z01N9bcl6YniicVlP1--00",
				data: {
					testId: id,
					testName: $("#tname_v").val().trim(),
					textDescription: "",
					price: $("#rate").val()
				}
			},
			function (data, status) {
				//alert(JSON.stringify(data));
			});
	}
	function check_seq(val, e) {
		if (e.which == 13) {
			var chk_val = parseInt($("input[id='par_seq_" + val + "']").val());
			var seq = $(".seq");
			var inc_val = 0;
			for (var i = 0; i < seq.length; i++) {
				if (parseInt($(seq[i]).val()) >= chk_val) {
					if (val != $(seq[i]).attr("name")) {
						if (inc_val == 0) {
							inc_val = chk_val;
						}
						else {
							inc_val++;
						}

						var nval = parseInt(parseInt(inc_val) + 1);
						$(seq[i]).val(nval)
					}
				}

			}
		}
	}

	function map_para(id) {
		$.post("pages/testmaster_map_para.php",
			{
				id: id
			},
			function (data, status) {
				$("#results").html(data);
				//$(".modal-dialog").css({'width':'1200px'});
				$("#mod").click();

				$("#results").fadeIn(500, function () { });
				load_param('0');
				setTimeout(function () {
					$("#searchh").focus();
				}, 1000);

				/*
				$("#back").fadeIn(100);
				$("#results").html(data);
				$("#results").css({'width':'95%','height':'90%'});
				var w=$("#results").width()/2+90;
				var h=$("#results").height()/2;
				document.getElementById("results").style.cssText+="margin-left:-"+w+"px;margin-top:-"+h+"px";
				$("#results").slideDown(500);
				*/
			})
	}
	function add_all_param() {
		var chk = $(".sel_param");
		for (var i = 1; i <= chk.length; i++) {
			var pid = $("#pid" + i).text();
			var pname = $("#pname" + i).text();
			var prtname = $("#prtname" + i).text();
			add_para(pid, pname, prtname);
		}
	}

	function add_para(id, name, intr) {
		var c = 1;
		var t = document.getElementsByClassName("p_id");
		for (var j = 0; j < t.length; j++) {
			if (t[j].value == id) {
				c = 0;
				break;
			}
		}
		if (c) {
			var par = document.getElementById("par");
			var tbody = document.createElement("tbody");
			var tr = document.createElement("tr");
			var td = document.createElement("td");
			var td1 = document.createElement("td");
			var td2 = document.createElement("td");
			var td3 = document.createElement("td");
			var td4 = document.createElement("td");
			var td5 = document.createElement("td");

			td.innerHTML = name + "<input type='hidden' value=" + id + " class='p_id' />";
			td1.innerHTML = intr;


			if ($('table#par tr:last input[type=text]').length > 0) {
				var lst_id = parseInt($('table#par tr:last input[type=text]').attr("name"));
				var lst_val = parseInt($('table#par tr:last input[type=text]').attr("value"));

				var lst = lst_id + 1;
				var lst_val = lst_val + 1;
			}
			else {
				var lst = 1;
				var lst_val = 1;
			}

			$.post("pages/testmaster_sub_test.php",
				{
					id: id,
					type: "sample_vaccu"
				},
				function (data, status) {
					td5.innerHTML = data;
				})

			td2.innerHTML = "<input type='text' class='seq' style='width:30px' name='" + lst + "' id='par_seq_" + lst + "' onkeyup='check_seq(" + lst + ",event)' value='" + lst_val + "'/>";


			td3.innerHTML = id;


			tr.appendChild(td3);
			tr.appendChild(td);
			tr.appendChild(td5);
			tr.appendChild(td1);
			tr.appendChild(document.createElement("td"));
			tr.appendChild(td2);
			tr.appendChild(td4);

			td4.innerHTML = "<i class='icon-remove'></i>";
			td4.onclick = function () { $(this).parent().remove(); }

			par.appendChild(tr);
		}

	}

	function save_test_para(id) {
		var pid = document.getElementsByClassName("p_id");
		var pids = "";
		for (var i = 0; i < pid.length; i++) {
			var samp = 0;
			var vac = 0;
			if ($("#samp_" + pid[i].value + "").length > 0) {
				samp = $("#samp_" + pid[i].value + "").val();
				vac = $("#vac_" + pid[i].value + "").val();
				dont_print=$("#dont_print_"+pid[i].value+":checked").length;
			}
			pids = pids + "#" + pid[i].value + "%" + samp + "%" + vac + "%" + dont_print;
		}


		var seq = document.getElementsByClassName("seq");
		var sq = ""
		var chk = 0;
		for (var j = 0; j < seq.length; j++) {
			if ($(seq[j]).val()) {
				var val = $(seq[j]).val();
				sq = sq + "#" + val;
			}
			else {
				chk = 1;
				break;
			}
		}

		if (!chk) {
			$.post("pages/testmaster_map_para_save.php",
				{
					id: id,
					pids: pids,
					sq: sq
				},
				function (data, status) {
					setTimeout(function () {
						bootbox.hideAll();
						bootbox.alert("Saved");
					}, 2000);
					load_all_test();

				})
		}
		else {
			bootbox.alert("Please provide sequence no");
		}
	}

	function close_div() {
		var res1 = $("#results1").css('display');
		if (res1 == "block") {
			$("#results1").fadeOut(500);
		}
		else {
			$('#results').slideUp(500); $("#back").fadeOut(100); $('#pinfo').fadeOut(200)
		}
	}
	function delete_test(val) {
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
						$.post("pages/testmaster_save_ajax.php",
							{
								tid: val,
								user: $("#user").text().trim(),

								typ: "del"
							},
							function (data, status) {
								if (data == "404") {
									bootbox.alert("Can't be deleted. Already used");
								} else {
									setTimeout(function () {
										bootbox.hideAll();
										bootbox.alert("Deleted");
										load_all_test();
									}, 2000);
								}
							})
					}
				}
			}
		});
	}
	function show_test_list() {
		url = "pages/test_rate_print.php?branch_id=" + btoa($("#branch_id").val()) + "&category_id=" + btoa($("#ser_category_id").val()) + "&type_id=" + btoa($("#ser_dep").val()) + "&sampleid=" + btoa($("#ser_samp").val()) + "&vac_id=" + btoa($("#ser_vac").val()) + "&equipment=" + btoa($("#ser_equipment").val());
		wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function sub_test(tid) {
		$.post("pages/testmaster_sub_test.php",
			{
				tid: tid,
				type: "load"
			},
			function (data, status) {
				$("#results").html(data);
				//$("#results").css({'width':'800px','overflow-x':'hidden'});
				//$(".modal-dialog").css({'width':'800px','height':'1100px'});
				$("#mod").click();
				$("#results").fadeIn(500, function () { });

			})
	}
	function add_sub_test() {
		if ($("#testadd").val() != '0') {
			var test = $("#testadd").val().split("@#");
			$("#s_list").append("<tr><th>" + test[1] + " <input type='hidden' class='tst_list' value='" + test[0] + "'/></th><th onclick='$(this).parent().remove()'>Remove</th></tr>");

			var tot = $(".tst_list").length;
			if (tot > 0) {
				$("#sv_bt").slideDown(200);
				$("#testadd").val('0');
			}
		}
	}
	function save_sub_test(tid) {
		var tests = $(".tst_list");
		var ts = "";
		for (var i = 0; i <= tests.length; i++) {
			if ($(tests[i]).val() > 0) {
				ts = ts + "@#" + $(tests[i]).val();
			}
		}
		$.post("pages/testmaster_sub_test.php",
			{
				tid: tid,
				t_list: ts,
				type: "save"
			},
			function (data, status) {
				bootbox.dialog({ message: "<h5>Saved</h5>" });
				setTimeout(function () {
					load_all_test();
				}, 1000);
				setTimeout(function () {
					bootbox.hideAll();
					$("#mod").click();
				}, 1500);
			})
	}

	function update_sample(tid, pid, val) {
		$.post("pages/testmaster_sub_test.php",
			{
				tid: tid,
				pid: pid,
				samp: val,
				type: "sample_par"
			},
			function (data, status) {

			})
	}
	function update_vaccu(tid, pid, val) {
		$.post("pages/testmaster_sub_test.php",
			{
				tid: tid,
				pid: pid,
				vaccu: val,
				type: "vaccu_par"
			},
			function (data, status) {

			})
	}
	function load_dlc_check(tst) {
		$.post("pages/testmaster_sub_test.php",
			{
				tst: tst,
				type: "dlc_check"
			},
			function (data, status) {
				$("#results2").html(data);
				//$("#results2").css({'width':'800px','overflow-x':'hidden'});
				//$("#myModal2 .modal-dialog").css({'width':'800px','height':'1100px'});
				$("#mod2").click();
				$("#results2").fadeIn(500, function () { });
			})
	}
	function add_dlc(tst, par, cls) {
		var typ = 0;
		if (cls == "icon-check") {
			typ = 1;
		}
		$.post("pages/testmaster_sub_test.php",
			{
				tst: tst,
				param: par,
				typ: typ,
				type: "dlc_save"
			},
			function (data, status) {
				if (data == 1) {
					$("#dlc_" + par + "").attr("class", "icon-check");
				}
				else {
					$("#dlc_" + par + "").attr("class", "icon-check-empty");
				}
			})
	}
	function save_mand(elem, tst, param) {
		var chk = 0;
		if ($(elem).is(':checked')) {
			chk = 1;
		}

		$.post("pages/testmaster_sub_test.php",
			{
				tst: tst,
				param: param,
				chk: chk,
				type: "param_mand"
			},
			function (data, status) { })

	}
</script>
<style>
	.para_link {
		text-decoration: underline;
		cursor: pointer
	}

	green_ed span {
		background-color: green !important;
	}

	#results {
		max-height: 540px;
		overflow-y: scroll;
	}

	#myModal {
		left: 23%;
		width: 95%;
	}

	.modal.fade.in {
		top: 3%;
	}

	.modal-body {
		max-height: 540px;
	}

	.dataTables_filter {
		color: #878787;
		font-size: 11px;
		right: 1%;
		top: 15%;
		margin: 4px 8px 2px 10px;
		position: absolute;
		text-align: left;
	}

	#DataTables_Table_0_length,
	#DataTables_Table_0_filter,
	#DataTables_Table_4_filter,
	#DataTables_Table_4_length {
		display: none;
	}

	.dataTables_length,
	.dataTables_filter {
		display: none;
	}
</style>
