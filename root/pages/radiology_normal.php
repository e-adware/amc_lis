<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Select Test<br />
				<select class="span6" id="testid" onchange="load_test_info(this.value)">
					<option value="0">--Select--</option>
					<?php
					$test = mysqli_query($link, "select * from testmaster where category_id>1 order by testname");
					while ($t = mysqli_fetch_array($test)) {
						echo "<option value='$t[testid]'>$t[testname]</option>";
					}
					?>
				</select>
			</th>
			<th>Select Doctor <br />
				<select id="doctor" onchange="load_test_info($('#testid').val())">
					<option value="0">All</option>
					<?php
					$qry = mysqli_query($GLOBALS["___mysqli_ston"], "select * from lab_doctor where category='2'");
					while ($q = mysqli_fetch_array($qry)) {
						echo "<option value='$q[id]'>$q[name]</option>";
					}
					?>
				</select>
			</th>
		</tr>
		<tr>
			<td colspan="2" style="text-align:left">
				<textarea style="height:350px;width:1000px" name="article-body" id="txtdetail"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				<input type="button" id="save" value="Save" class="btn btn-custom" onclick="save_normal()" />
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript" src="../ckeditor_rad/ckeditor.js"></script>
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function () {
		$("select").select2({ theme: "classic" });
		$("#exec select:first").focus();
		add();
	})
	function load_rad_test(val) {
		alert(val);
		$.post("pages/radiology_normal_test.php",
			{
				val: val
			},
			function (data, status) {
				$("#rad_test").html(data);
				$("#rad_test").slideDown(500);
			})
	}
	function load_test_info(id) {
		$.post("pages/radiology_normal_info.php",
			{
				id: id,
				doctor: $("#doctor").val()
			},
			function (data, status) {
				$(".rad_res").contents().find('body').html(data);

			})
	}
	function save_normal() {
		$.post("pages/radiology_normal_save.php",
			{
				testid: $("#testid").val(),
				doctor: $("#doctor").val(),
				normal: $(".rad_res").contents().find('body').html()
			},
			function (data, status) {
				//alert(data);
				alert("Saved");
			})
	}
	function add() {
		if (CKEDITOR.instances['article-body']) {
			CKEDITOR.instances['article-body'].destroy(true);
		}
		CKEDITOR.replace('article-body');
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.config.extraPlugins = 'lineheight';
		//CKEDITOR.config.width = 700;		
		CKEDITOR.config.height = 300;
		CKEDITOR.config.line_height = "1.0em;1.5em;2.0em;2.5em;3.0em;3.5em;4.0em;4.5em;5.0em;";


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