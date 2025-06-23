<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Component Report</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Select Blood Group</td>
				<td>
					<select id="abo" class="span2">
						<option value="0">Select</option>
						<option value="A">A</option>
						<option value="B">B</option>
						<option value="AB">AB</option>
						<option value="O">O</option>
					</select>
					<select id="rh" class="span2">
						<option value="0">Select</option>
						<option value="positive">Positive</option>
						<option value="negative">Negative</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input type="button" class="btn btn-info" value="Search" onclick="view_result()" />
				</td>
			</tr>
		</table>
	</div>
	<div id="result">
	
	</div>
</div>
<script>
	$(document).ready(function()
	{
		check_exp_date();
		//alert($("#user").text().trim());
	});
	function view_result()
	{
		$.post("pages/global_load_g.php",
		{
			abo:$("#abo").val(),
			rh:$("#rh").val(),
			type:"display_component_stock",
		},
		function(data,status)
		{
			$("#result").html(data);
		})
	}
	function check_exp_date()
	{
		$.post("pages/global_load_g.php",
		{
			usr:$("#user").text().trim(),
			type:"check_exp_date",
		},
		function(data,status)
		{
			//alert(data);
		})
	}
</script>
