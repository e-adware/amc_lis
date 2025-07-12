<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Drug Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span5">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
				  <td> Name</td>
				  <td><label for="txtname"></label>
					<input type="hidden" id="drug_id"/>
					<input type="text" name="name" id="name" class="imp" size="35" value="" autocomplete="off" /></td>
				</tr>
				<tr>
				  <td colspan="2" style="text-align:center" >
					  <button name="save" id="save" value="save" onclick="save_drug();" class="btn btn-success"><i class="icon-save"></i> Save</button>
					  <button name="clear" id="clear" value="clear" onclick="clear_drug()" class="btn btn-info"><i class="icon-cut"></i> Clear</button>
				  </td>
				</tr>
			</table>
		</div>
		<div class="span6">
			<input type="text" id="drug" onkeyup="load_drug_list_e(event)" placeholder="Press Enter to Search"/>
			<div id="drug_list" style="height:500px;overflow:scroll">
				
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function()
	{
		load_drug_list()		
	})
	
	function save_drug()
	{
		$.post("pages/drug_master_ajax.php",
		{ 
			type:2,
			id:$("#drug_id").val(), 
			name:$("#name").val(), 
		},
		function(data,status)
		{
			alert("Saved");
			location.reload();
		})
	}
	
	function load_drug_list()
	{
		$.post("pages/drug_master_ajax.php",
		{ 
			type:1,
			val:$("#drug").val(), 
		},
		function(data,status)
		{
			$("#drug_list").html(data);
		})
	}
	
	function load_drug_list_e(e)
	{
		load_drug_list();
		
	}
	
	function delete_data(val)
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to delete</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-info",
					callback: function() {
						$.post("pages/drug_master_ajax.php",
						{
							type:"3",
							val:val,
						},
						function(data,status)
						{
							bootbox.alert("Data Delete");
							location.reload();
						})
					}
				}
			}
		});
	}
	
	function update_data(val,name)
	{
		$("#drug_id").val(val);
		$("#name").val(name);
		
		$("#save").html("<i class='icon-edit'></i> Update");
	}
	
	function clear_drug()
	{
		$("#drug_id").val("");
		$("#name").val("");
		
		$("#save").html("<i class='icon-save'></i> Save");
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
.reference td img{
	margin:0;}
</style>
