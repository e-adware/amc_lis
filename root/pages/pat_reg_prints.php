<?php
include'../../includes/connection.php';

$uhid=$_POST['uhid'];
$visit=$_POST['opd_id'];

$chk_pat=mysqli_num_rows(mysqli_query($link,"select * from patient_test_details where patient_id='$uhid' and opd_id='$visit'"));

if($chk_pat>0)
{
	?>
		<div style="padding:10px;overflow-x:hidden;overflow:scroll;height:400px;max-height:300px;">
		<table class="table table-bordered">
		<tr><th><label><input type="checkbox" value="1" id="1" class="norm"/> Delivery Receipt</label></th></tr>
		<tr><th><label><input type="checkbox" value="2" id="2" class="norm"/> Office Copy</label></th></tr>
			<?php
			$test_path=mysqli_query($link,"select * from patient_test_details where patient_id='$uhid' and opd_id='$visit' and testid in(select testid from testmaster where category_id='1')");
			while($test_p=mysqli_fetch_array($test_path))
			{
				$i++;
				$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$test_p[testid]'"));
				?><tr><th><label><input type="checkbox" value="<?php echo $test_p[testid];?>" id="<?php echo $i;?>" class="path"/> <?php echo $tname[testname];?></label></th></tr><?php
			}
			
			$test_rad=mysqli_query($link,"select * from patient_test_details where patient_id='$uhid' and opd_id='$visit' and testid in(select testid from testmaster where category_id>'1')");
			while($test_r=mysqli_fetch_array($test_rad))
			{
				$i++;
				$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$test_r[testid]'"));
				?><tr><th><label><input type="checkbox" value="<?php echo $test_r[testid];?>" id="<?php echo $i;?>" class="rad"/> <?php echo $tname[testname];?></label></th></tr><?php
			}
			?>
		</tr>
		</table>
		</div>
	<?php
		if($chk_pat>0)
		{
			?><div align="center"><input type="button" value="Print" class="btn btn-info" onclick="print_indiv('<?php echo $uhid; ?>','<?php echo $visit; ?>')"/></div><?php
		}	
			
}	
		
		
?>
