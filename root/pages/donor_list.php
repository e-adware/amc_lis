<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Donor List</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<b>Name</b>
				<input type="text" id="name" onKeyup="view_all();$('#id').val('');$('#contact').val('')" autofocus />
			</td>
			<td>
				<b>Id Number</b>
				<input type="text" id="id" onKeyup="view_all();$('#name').val('');$('#contact').val('');if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" />
			</td>
			<td>
				<b>Contact Number</b>
				<input type="text" id="contact" onKeyup="view_all();$('#id').val('');$('#name').val('');if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" />
			</td>
		</tr>
	</table>
	<div id="result" style="max-height:600px;overflow-y:scroll;">
	
	</div>
</div>
<script>
	$(document).ready(function()
	{
		view_all();
	});
	function gopage(id)
	{
		window.location="processing.php?param=194&uhid="+id;
	}
	function view_all()
	{
		$.post("pages/global_load_g.php",
		{
			name:$("#name").val(),
			id:$("#id").val(),
			contact:$("#contact").val(),
			type:"load_donor_list",
		},
		function(data,status)
		{
			$("#result").html(data);
		})
	}
</script>
