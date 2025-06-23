<?php
include("../../includes/connection.php");
$type=$_POST['type'];


if($type==1)
{
	$doc=$_POST[val];
	?>
	<table class="table table-bordered table-condensed">
	<tr><th colspan="2">Update Password</th></tr>
	<tr>
		<th>New password</th><th><input type="password" id="n_pass"/></th>
	</tr>
	<tr>
		<th>Old password</th><th><input type="password" id="o_pass"/></th>
	</tr>
	<tr>
		<th colspan="2" style="text-align:center">
			<input type="button" class="btn btn-primary" value="Update" onclick="save_pass('<?php echo $doc;?>')"/>
			<input type="button" class="btn btn-danger" value="Close" onclick="$('#mod').click()"/>
		</th>
	</tr>
	
	</table>
	<?php
	
}
else if($type==2)
{
	$doc=$_POST[val];
	$n_pass=$_POST[n_pass];
	$o_pass=$_POST[o_pass];
	
	$chk_pass=mysqli_num_rows(mysqli_query($link,"select * from lab_doctor where id='$doc' and password='$o_pass'"));
	
	if($chk_pass>0)
	{
		mysqli_query($link,"update lab_doctor set password='$n_pass' where id='$doc'");
		echo "1";
	}
	else
	{
		echo $chk_pass;
	}
}
?>
