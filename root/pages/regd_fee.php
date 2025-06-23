<?php
$q_val=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `registration_fees` "));
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Registration Fees</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table>
		<tr>
			<th>Registration Fee</th>
			<td><input type="text" id="regd_fee" value="<?php echo $q_val['regd_fee']; ?>"></td>
			<th>Validity</th>
			<td><input type="text" id="valid" class="span1" value="<?php echo $q_val['validity']; ?>" > Days</td>
		</tr>
		<tr>
			<td colspan="4"><button class="btn btn-info" onCLick="save_regd()">Save</button></td>
		</tr>
	</table>
</div>
<script>
	function save_regd()
	{
		$.post("pages/global_insert_data.php",
		{
			type:"save_regd_validity",
			regd_fee:$("#regd_fee").val(),
			valid:$("#valid").val(),
			user:$("#user").text(),
		},
		function(data,status)
		{
			window.location.reload(true);
		})
	}
</script>
