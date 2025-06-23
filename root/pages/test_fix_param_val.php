<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Parameter Fix value</span>
</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<td>
				<b>Select Test</b> <br/>
				<select id="testid" onchange="load_all_param()" autofocus class="span5">
					<option value="0">--Select Test--</option>
					<?php
						$vac_sr=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `testmaster` WHERE `category_id`='1' ORDER  BY `testname` ASC ");
						while($v_s=mysqli_fetch_array($vac_sr))
						{
							echo "<option value='$v_s[testid]'>$v_s[testname]</option>";								
						}
					?>
				</select>
			</td></tr>
			<tr><td>
				<b>Select Parameter</b> <br/>
				<select id="param" onchange="load_fix_param()" class="span5">
					<option value="0">--Select Parameter--</option>
				</select>
			</td>
		</tr>
	</table>
	<div id="load_data"></div>
</div>

<script src="../js/bootbox.min.js"></script>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function()
			{
				$("select").select2({ theme: "classic" });
				
			})
	
	function load_all_param()
	{
		$.post("pages/test_fix_param_val_data.php",
		{
			type:"load_all_param",
			testid:$("#testid").val(),
		},
		function(data,status)
		{
			$("#param").html(data);
			$("#load_data").html("");
			
		})
	}
	function load_fix_param()
	{
		$.post("pages/test_fix_param_val_data.php",
		{
			type:"load_fix_param_val",
			testid:$("#testid").val(),
			param:$("#param").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
		})
	}
	function save_param_fix_val(testid,param)
	{
		if($("#range_check").prop("checked"))
		{
			var range_check=1;
		}else
		{
			var range_check=0;
		}
		
		if($("#must_save").prop("checked"))
		{
			var must_save=1;
		}else
		{
			var must_save=0;
		}
			
		$.post("pages/test_fix_param_val_data.php",
		{
			type:"save_param_fix_val",
			testid:testid,
			param:param,
			fix_param_val:$("#fix_param_val").val(),
			range_check:range_check,
			must_save:must_save,
		},
		function(data,status)
		{
			//$("#fix_param_val").css('border', '2px solid green'); 
			alert("Saved");
		})
	}
</script>
