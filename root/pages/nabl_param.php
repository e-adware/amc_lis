<?php
$nbl = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from nabl"));
?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span11">
			<table class="table table-bordered table-condensed">
				<tr>
					<th>NABL</th>
					<td>
						<?php
						if ($nbl['nabl'] == 1) {
							$enb = "selected='selected'";
						}
						?>
						<select id="nabl">
							<option value="0">No</option>
							<option value="1" <?php echo $enb; ?>>Yes</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>NABL text</th>
					<td><input type="text" class="span6" id="nabl_text"
							value="<?php echo htmlentities($nbl['text']); ?>" size="70" /></td>
				</tr>
				<tr>
					<td colspan="2"><input type="button" id="save" value="Save" class="btn btn-info"
							onclick="save_nabl()" /></td>
				</tr>
			</table>
		</div>
		<div class="span11">
			<table class="table table-bordered">
				<tr>
					<th>
						Search by
						<select id="type" class="span2">
							<option value="1">Test</option>
							<option value="2">Parameter</option>
						</select>
						<input type="text" id="search_field" onkeyup="load_search(this.value,event)" autofocus>
						<div id="ser_div"></div>
					</th>
				</tr>
			</table>
			<div id="details">

			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function () {

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
			}
		}
		else if (unicode == 40) {
			$("#chk").val("1");
			var chk = doc_v + 1;
			var cc = document.getElementById("rad_test" + chk).innerHTML;
			if (cc) {
				doc_v = doc_v + 1;
				$("#rad_test" + doc_v).css({ 'font-weight': 'bold', 'color': 'red', 'transform': 'scale(0.95)', 'transition': 'all .2s' });
				var doc_v1 = doc_v - 1;
				$("#rad_test" + doc_v1).css({ 'font-weight': 'normal', 'color': 'black', 'transform': 'scale(1.0)', 'transition': 'all .2s' });
				var z2 = doc_v % 3;
				if (z2 == 0) {
					$("#ungroup").scrollTop(doc_sc)
					doc_sc = doc_sc + 90;
				}
			}

		}
		else if (unicode == 38) {
			$("#chk").val("1");
			var chk = doc_v - 1;
			var cc = document.getElementById("rad_test" + chk).innerHTML;
			if (cc) {
				doc_v = doc_v - 1;
				$("#rad_test" + doc_v).css({ 'font-weight': 'bold', 'color': 'red', 'transform': 'scale(0.95)', 'transition': 'all .2s' });
				var doc_v1 = doc_v + 1;
				$("#rad_test" + doc_v1).css({ 'font-weight': 'normal', 'color': 'black', 'transform': 'scale(1.0)', 'transition': 'all .2s' });
				var z2 = doc_v % 3;
				if (z2 == 0) {
					doc_sc = doc_sc - 90;
					$("#ungroup").scrollTop(doc_sc)

				}
			}

		}
		else {
			$.post("pages/load_data_ajax.php",
				{
					val: val,
					type: "ungroupparam",

				},
				function (data, status) {
					$("#ungroup").html(data);

				})

		}
	}

	function popupit(url) {
		var txtfrom = 0;
		var txtto = 0;
		var vmkid = document.getElementById("selectmk").value;
		url = url + "?date1=" + txtfrom + "&date2=" + txtto + "&vmkid=" + vmkid;
		newwindow = window.open(url, 'window', 'left=10,top=10,height=600,witdh=600,menubar=1,resizeable=0,scrollbars=1');

	}

	function popupit1(url) {
		var txtfrom = 0;
		var txtto = 0;
		var vmkid = document.getElementById("selectmk").value;

		url = url + "?date1=" + txtfrom + "&date2=" + txtto + "&vmkid=" + vmkid;
		newwindow = window.open(url, 'window', 'left=10,top=10,height=600,witdh=600,menubar=1,resizeable=0,scrollbars=1');

	}
	function ungroupdoc_load() //For load ungroup Doctor
	{
		$.post("pages/load_data_ajax.php",
			{
				tstid: document.getElementById("selectmk").value,
				type: "ungroupparam",
			},
			function (data, status) {

				$("#ungroup").html(data)

			})
	}

	function lod_groupdoc() {
		$.post("pages/load_data_ajax.php",
			{
				type: "groupparam",
				mkid: document.getElementById("selectmk").value,

			},
			function (data, status) {
				$("#laod_doctor").html(data)
				ungroupdoc_load();
			})

	}



	function clearr() {
		var a = document.getElementsByTagName("input");
		for (var i = 0; i < a.length; i++) {
			if (a[i].type == "text") {
				if (i == 0) {
				}
				else {
					a[i].value = "";

				}
			}

		}
		//document.getElementById("txtid").value=document.getElementById("docid").value

	}



	function delete_sub(subp)//for delete
	{
		$.post("pages/delete_sub.php",
			{
				subp: subp,
				type: "refdoc",
			},
			function (data, status) {

				// subparam_load($("#tstpid").val());	
				lod_refraldoctor();
			})
	}
	function save_nabl() {
		$.post("pages/nabl_param_ajax.php",
			{
				nabl: $("#nabl").val(),
				nabl_text: $("#nabl_text").val(),
				type: "nabl_en",
			},
			function (data, status) {
				alert("Saved");
			})
	}
	var timer;
	var t_val = 1;
	var t_val_scroll = 0;
	function load_search(val, e) {
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 13) {
			load_details($("#type").val(), $("#id_" + t_val + "").val());
		}
		else if (unicode == 40) {
			var chk = t_val + 1;
			var cc = document.getElementById("td" + chk).innerHTML;
			if (cc) {
				t_val = t_val + 1;
				$("#td" + t_val).css({ 'color': '#419641', 'transform': 'scale(0.95)', 'font-weight': 'bold', 'transition': 'all .2s' });
				var t_val1 = t_val - 1;
				$("#td" + t_val1).css({ 'color': 'black', 'transform': 'scale(1.0)', 'font-weight': 'normal', 'transition': 'all .2s' });
				var z2 = t_val % 1;
				if (z2 == 0) {
					$("#ser_div").scrollTop(t_val_scroll)
					t_val_scroll = t_val_scroll + 30;
				}
			}
		}
		else if (unicode == 38) {
			var chk = t_val - 1;
			var cc = document.getElementById("td" + chk).innerHTML;
			if (cc) {
				t_val = t_val - 1;
				$("#td" + t_val).css({ 'color': '#419641', 'transform': 'scale(0.95)', 'font-weight': 'bold', 'transition': 'all .2s' });
				var t_val1 = t_val + 1;
				$("#td" + t_val1).css({ 'color': 'black', 'transform': 'scale(1.0)', 'font-weight': 'normal', 'transition': 'all .2s' });
				var z2 = t_val % 1;
				if (z2 == 0) {
					t_val_scroll = t_val_scroll - 30;
					$("#ser_div").scrollTop(t_val_scroll)

				}
			}
		}
		else if (unicode == 27) {

		}
		else {
			clearTimeout(timer);
			timer = setTimeout(function () {
				$.post("pages/nabl_param_ajax.php",
					{
						s_type: $("#type").val(),
						word: val,
						type: 1
					},
					function (data, status) {
						$("#ser_div").html(data);
						//$("#ser_div").show(200).css("display", "inline-block");
						$("#ser_div").show(200);
					})
			}, 200);
		}
	}
	function load_details(type, val) {
		$.post("pages/nabl_param_ajax.php",
			{
				stype: type,
				val: val,
				type: 2
			},
			function (data, status) {
				$("#ser_div").slideUp(200);
				$("#details").html(data);
			})
	}
	function nabl_status(row, param) {
		$.post("pages/nabl_param_ajax.php",
			{
				stat: $("#p_stat" + row + "").val(),
				param: param,
				type: 3
			},
			function (data, status) {
				if (data == 1) {
					$("#param_status_" + row + "").html("<img src='../images/right.png' height='20' width='20'/> <input type='hidden' id='p_stat" + row + "' value='" + data + "'/>");
				}
				else {
					$("#param_status_" + row + "").html("<img src='../images/Delete.png' height='20' width='20'/> <input type='hidden' id='p_stat" + row + "' value='" + data + "'/>");
				}
			})

	}
</script>
<style>
	/*#ser_div
	{
		position:absolute;
		display:none;
		width:500px;
		border:1px solid;
		padding:5px;
		background-color:white;
		margin-left:2px;
	}*/
	#search_field {
		position: unset;
	}
</style>