<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Patient Revisit</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<b>Name</b>
				<input type="text" id="pat_name" onKeyup="view_all(this.id)">
			</td>
			<td>
				<b>UHID</b>
				<input type="text" id="uhid" onKeyup="view_all(this.id)" autofocus>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
</div>
<script>
	function view_all(id)
	{
		if(id=="pat_name")
		{
			$("#uhid").val("");
		}else if(id=="uhid")
		{
			$("#pat_name").val("");
		}
		$.post("pages/global_load.php",
		{
			type:"load_all_pat_revisit",
			pat_name:$("#pat_name").val(),
			pat_uhid:$("#uhid").val(),
		},
		function(data,status)
		{
			$("#load_all").show().html(data);
		})
	}
	function redirect_page(uhid)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h4>Patient Re-visit ?</h4>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-info",
					callback: function() {
						ok_revisit_pat(uhid);
					}
				}
			}
		});
		//window.location="processing.php?param=3&uhid="+uhid+"&opd="+opd_id;
	}
	function ok_revisit_pat(uhid)
	{
		alert(uhid);
		/*
		$.post("pages/global_load.php",
		{
			type:"load_all_pat_revisit",
			pat_name:$("#pat_name").val(),
			pat_uhid:$("#uhid").val(),
		},
		function(data,status)
		{
			$("#load_all").show().html(data);
		})
		*/
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
</style>
