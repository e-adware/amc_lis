<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Select Test <br />
				<select id="testid" onchange="load_normal(this.value)" style="width:400px">
					<option value="0">Select</option>
					<?php
					$tst = mysqli_query($link, "select * from testmaster where category_id='1' order by testname");
					while ($t = mysqli_fetch_array($tst)) {
						echo "<option value='$t[testid]'>$t[testname]</option>";
					}
					?>
				</select>
			</th>
			<th>Select Param(Pad) <br />
				<select id="param_pad" onchange="load_sum_par(this.value)">
					<option value="0">--Select--</option>
					<?php
					$par = mysqli_query($GLOBALS["___mysqli_ston"], "select * from Parameter_old where ResultType='7' order by Name");
					while ($pad = mysqli_fetch_array($par)) {
						echo "<option value='$pad[ID]'>$pad[Name]</option>";
					}
					?>
				</select>
			</th>
		</tr>
		<tr>
			<td colspan="2" style="text-align:left">
				<textarea style="height:350px;width:1000px" name="article-body" id="normal"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				<input type="button" id="save" value="Save" class="btn btn-info" onClick="save_normal()" />
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function () {

		$("select").select2({ theme: "classic" });
		$("#exec select:first").focus();
		add();
	})
	function load_path_test(val) {
		$.post("pages/test_summary_pat.php",
			{
				val: val
			},
			function (data, status) {
				$("#rad_test").html(data);
				$("#rad_test").slideDown(500);
			})
	}
	function load_normal(id) {
		$.post("pages/test_summary_info.php",
			{
				id: id,
				type: "test"
			},
			function (data, status) {
				$("#rad_res").contents().find('body').html(data);
				//$("#param_pad").val("0").trigger("change");
			})
	}
	function save_normal() {
		$.post("pages/test_summary_save.php",
			{
				testid: $("#testid").val(),
				param: $("#param_pad").val(),
				summ: $("#rad_res").contents().find('body').html().trim(),
			},
			function (data, status) {
				alert("Saved");
			})
	}

	function load_sum_par(id) {
		$.post("pages/test_summary_info.php",
			{
				id: id,
				type: "param"
			},
			function (data, status) {
				$("#rad_res").contents().find('body').html(data);
			})
	}

	function add() {
		if (CKEDITOR.instances['article-body']) {
			CKEDITOR.instances['article-body'].destroy(true);
		}
		CKEDITOR.replace('article-body');
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.config.height = 300;

		// Remove text formatting while pasting
		CKEDITOR.config.extraPlugins = (CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins + ',pastefromword' : 'pastefromword');
		CKEDITOR.config.pasteFromWordRemoveFontStyles = true;
		CKEDITOR.config.pasteFromWordRemoveStyles = true;

		// Optionally remove inline styles and classes
		CKEDITOR.config.forcePasteAsPlainText = true;

		CKEDITOR.config.removeFormatTags = 'b,strong,em,i,font,u,strike,sub,sup';
		CKEDITOR.config.removeFormatAttributes = 'style,span,class,width,height,align,hspace,valign';

		CKEDITOR.on('instanceReady', function (evt) {
			evt.editor.on('paste', function (event) {
				var data = event.data.dataValue;
				// Remove all HTML tags except <br>
				data = data.replace(/<(?!br\s*\/?)[^>]+>/gi, '');
				event.data.dataValue = data;
			});
		});

	}
</script>
<style>
	.cke_textarea_inline {
		padding: 10px;
		height: 380px;
		overflow: auto;
		border: 1px solid gray;
		-webkit-appearance: textfield;
	}
</style>