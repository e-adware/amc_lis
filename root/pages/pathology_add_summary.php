<?php
include('../../includes/connection.php');

$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$tid=$_POST['tid'];
$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$tid'"));
//$summ=mysql_fetch_array(mysql_query("select summary from test_summary where testid='$tid'"));	
//echo "select * from patient_test_summary where patient_id='$uhid' and visit_no='$visit' and testid='$tid'";

$pat_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tid'");
$num_pat=mysqli_num_rows($pat_sum);
if($num_pat>0)
{
	$pat_s=mysqli_fetch_array($pat_sum);
	$summ=$pat_s[summary];	
}
else
{
	$chk_sum=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_summary where testid='$tid'");
	$num_sum=mysqli_num_rows($chk_sum);
	if($num_sum>0)
	{
		$summ_all=mysqli_fetch_array($chk_sum);
		$summ=$summ_all[summary];
	}
	
}


?>
<div id="summary_div" style="padding:10px">
	
<table class="table table-bordered table-condensed">
<tr>
	<td><b><?php echo $tname[testname];?></b></td>
</tr>
<tr>
	<td>
		<textarea style='height:350px;width:1100px' name="article-body<?php echo $tid ?>" id="summary">
			<?php echo $summ;?>
		</textarea>
	</td>
</tr>
<tr>
	<td style='text-align:center'>
		<button class="btn btn-save" id="save_sum" onclick="save_summary('<?php echo $tid;?>')"><i class="icon-save"></i> Save</button>
		<!--<button class="btn btn-close" id="cls_sum" onclick="close_summary();"><i class="icon-off"></i> Close</button>-->
		<button class="btn btn-back" onclick="$('#btn_<?php echo $tid;?>').click();$('#test_id').focus();"><i class="icon-backward"></i> Back</button>
	</td> 
</tr>
</table>
</div>
