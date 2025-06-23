<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>


<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<th width="400px">
				<div id="find_new" style="display:none">
					<input type="text" id="new_find" placeholder="Type Finding Name" onkeyup="select_find_def()" />
				</div>
				<button class="btn btn-info" id="f_new" value="f_new" onclick="f_new(this.value)"><i
						class="icon-plus"></i> Add New Finding</button>
			</th>
			<th>
				Select Finding<br />

				<select id="findid" onchange="load_test_info(this.value)" style="width:500px">
					<option value="0">--Select--</option>
					<?php
					$test = mysqli_query($link, "select id,name from radiology_normal_finding order by name");
					while ($t = mysqli_fetch_array($test)) {
						echo "<option value='$t[id]'>$t[name]</option>";
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
			<td colspan="2" style="text-align:center" id="buttons">
				<button id="save" value="Save" class="btn btn-save" onclick="save_normal(this.value)"><i
						class="icon-save"></i> Save</button>
				<button id="new" value="New" class="btn btn-new" onclick="location.reload()"><i class="icon-ok"></i>
					New</button>
			</td>
		</tr>
	</table>

	<script>

	</script>

</div>


<script type="text/javascript" src="../ckeditor_rad/ckeditor.js"></script>
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function () {

		$("select").select2({ theme: "classic" });
		$("html, body").animate({ scrollTop: 0 })
		add();
	})

	function f_new(val) {
		$("#find_new").slideToggle(200);
		if (val == "f_new") {
			$("#new_find").focus();
			$("#f_new").html("<i class='icon-remove'></i> Cancel").val("f_can").prop("class", "btn btn-delete");
			$("#findid").prop("disabled", true);
			$("#findid").val("0");
			$("#findid").select2().trigger('change');
		}
		else {
			$("#f_new").html("<i class='icon-plus'></i> Add New Finding").val("f_new").prop("class", "btn btn-save");
			$("#findid").prop("disabled", false);
		}
	}


	function load_test_info(id) {
		$.post("pages/radiology_normal_finding_ajax.php",
			{
				id: id,
				type: "load"
			},
			function (data, status) {
				$(".rad_res").contents().find('body').html(data);
				if (id > 0) {
					$("#save").val("Update");
					if ($("#delete").length == 0) {
						$("#buttons").append("<button class='btn btn-delete' id='delete' value='Delete' onclick='delete_format()'><i class='icon-remove'></i> Delete</button>");
					}
				}
				else {
					$("#save").val("Save");
					$("#buttons").html('<button id="save" value="Save" class="btn btn-save" onclick="save_normal(this.value)"><i class="icon-save"></i> Save</button>');
					$("#buttons").append(' <button id="new" value="New" class="btn btn-new" onclick="location.reload()"><i class="icon-ok"></i> New</button>');
				}
			})
	}
	function save_normal(val) {
		$("#save").attr("disabled", true);
		if (val == "Save") {
			$.post("pages/radiology_normal_finding_ajax.php",
				{
					find_name: $("#new_find").val(),
					normal: $(".rad_res").contents().find('body').html(),
					type: "save"
				},
				function (data, status) {
					alert("Saved");
					location.reload();
				})
		}
		else if (val == "Update") {
			$.post("pages/radiology_normal_finding_ajax.php",
				{
					f_id: $("#findid").val(),
					normal: $(".rad_res").contents().find('body').html(),
					type: "update"
				},
				function (data, status) {
					alert("Updated");
					location.reload();
				})
		}

	}

	function delete_format() {
		if (confirm("Do you really want to delete this finding?")) {
			$.post("pages/radiology_normal_finding_ajax.php",
				{
					f_id: $("#findid").val(),
					type: "delete"
				},
				function (data, status) {
					alert("Deleted");
					location.reload();
				})

		}
	}

	function add() {
		if (CKEDITOR.instances['article-body']) {
			CKEDITOR.instances['article-body'].destroy(true);

		}
		CKEDITOR.replace('article-body');
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.config.height = 300;
		CKEDITOR.config.extraPlugins = 'lineheight';
		CKEDITOR.config.line_height = "1em;1.5em;2em;2.5em;3em;4em;5em";


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