<div style="padding:10px;display:none" id="ref_doc_new">
<?php
	include("../../includes/connection.php");
?>
<h4>Enter Doctors details</h4>
<table class="table table-bordered table-condensed">
<tr>
	<th>Name</th>
	<td><input type="text" id="doc_name" class="capital" /></td>
</tr>

<tr>
	<th>Qualification</th>
	<td><input type="text" id="doc_qual" class="capital"/></td>
</tr>
<tr>
	<th>Address</th>
	<td><input type="text" id="doc_add" class="capital"/></td>
</tr>
<tr>
	<th>Phone</th>
	<td><input type="text" id="doc_phone" maxlength="10" /></td>
</tr>
<tr>
	<th>Email</th>
	<td><input type="text" id="doc_email"/></td>
</tr>
<tr>
	<th colspan="2" style="text-align:center">
		<button class="btn btn-save" id="doc_save" value="Save" onclick="save_new_doc()"><i class="icon-save"></i> Save</button>
		<button class="btn btn-close" id="doc_close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
	</th>
	
</tr>
</table>
</div>
