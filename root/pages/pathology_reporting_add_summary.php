<?php
include('../../includes/connection.php');

$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$testid=$_POST['tid'];
$test_info=mysqli_fetch_array(mysqli_query($link, "select testname from testmaster where testid='$testid'"));

$pat_sum=mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid'");
$num_pat=mysqli_num_rows($pat_sum);
if($num_pat>0)
{
	$pat_s=mysqli_fetch_array($pat_sum);
	$summ=$pat_s["summary"];
}
else
{
	$chk_sum=mysqli_query($link, "select * from test_summary where testid='$testid'");
	$num_sum=mysqli_num_rows($chk_sum);
	if($num_sum>0)
	{
		$summ_all=mysqli_fetch_array($chk_sum);
		$summ=$summ_all["summary"];
	}
}

?>
<div id="summary_div" style="padding:10px">
	
<table class="table table-bordered table-condensed">
<tr>
	<td><b><?php echo $test_info["testname"];?></b></td>
</tr>
<tr>
	<td>
		<textarea style='height:350px;width:1100px' name="article-body<?php echo $testid ?>" id="summary">
			<?php echo $summ;?>
		</textarea>
	</td>
</tr>
<tr>
	<td style='text-align:center'>
		<button class="btn btn-save" id="save_sum" onclick="save_summary('<?php echo $testid;?>')"><i class="icon-save"></i> Save</button>
		<!--<button class="btn btn-close" id="cls_sum" onclick="close_summary();"><i class="icon-off"></i> Close</button>-->
		<!--<button class="btn btn-back" onclick="$('#btn_<?php echo $testid;?>').click();$('#test_id').focus();"><i class="icon-backward"></i> Back</button>-->
		<button class="btn btn-back" onclick="test_summary_back('<?php echo $testid;?>')"><i class="icon-backward"></i> Back</button>
	</td> 
</tr>
</table>
</div>
