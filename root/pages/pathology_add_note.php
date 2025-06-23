<?php
	include("../../includes/connection.php");
	
	$pin=$_POST[pin];
	$batch=$_POST[batch];
	$tst=$_POST[tid];
	
	$val=0;
	if($_POST[val])
	{
		$val=$_POST[val];
	}
	
	
	$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where  testid='$tst'"));
	$res=mysqli_fetch_array(mysqli_query($link,"select * from testresults_note where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst'"));
?>
<h4>Add Note</h4>
<table class="table table-bordered">
<tr>
	<td>Test</td>
	<td><?php echo $tname[testname];?></td>
</tr>	
<tr>
	<td>Note</td>
	<td>
		<textarea id="test_note" style="width:300px"><?php echo $res[note];?></textarea>
	</td>
</tr>
<tr>
	<td style="text-align:center" colspan="2">
		<input type="button" class="btn btn-primary" value="Save" onclick="save_note(<?php echo $tst;?>,<?php echo $val;?>)"/>
		<input type="button" class="btn btn-danger" value="Close" onclick="$('#mod2').click();$('#results1').fadeOut(200)"/>
	</td>
</tr>
	
</table>
