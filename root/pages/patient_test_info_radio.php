<?php
include("../../includes/connection.php");
$uhid=$_POST['uhid'];
$opd_id=trim($_POST['opd_id']);
$ipd_id=trim($_POST['ipd_id']);
$batch_no=trim($_POST['batch_no']);
$category_id=$_POST['category_id'];

$qry=mysqli_query($link, " SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' ");

?>

<table class="table table-bordered table-condensed">
	<tr><th>#</th><th>Test ID</th><th colspan="2">Test Name</th></tr>
	
	<?php
		$i=1;
		while($q=mysqli_fetch_array($qry))
		{
			if($category_id==2)
			{
				$num=mysqli_num_rows(mysqli_query($link, "select * from testresults_rad where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$q[testid]'"));
			}
			if($category_id==3)
			{
				$num=mysqli_num_rows(mysqli_query($link, "select * from testresults_card where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$q[testid]'"));
			}
			if($num>0)
			{
				//$cls="green";
				$style_span="background-color: #9dcf8a;";
			}
			else
			{
				//$cls="red";
				$style_span="background-color: #d59a9a;";
			}

			$num2=mysqli_num_rows(mysqli_query($link, "select * from testreport_print where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$q[testid]'"));
			if($num2>0)
			{
				//$cls="grey";
				$style_span="background-color: #666666;";
			}
			
			$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$q[testid]'"));
			if($tname['category_id']==$category_id)
			{
			?>
			<tr id="test_tr<?php echo $i;?>" onclick="rad_select_test(<?php echo $i;?>)" class="<?php echo $cls;?>" style="cursor:pointer;">
				<td>
					<span class="btn_round" style="<?php echo $style_span; ?>"><?php echo $i; ?></span>
					
					<div style="display:none" id="test_dis<?php echo $i;?>">
						<?php echo "@".$i."@".$q['testid']."@".$tname['testname']."@".$uhid."@".$opd_id."@".$ipd_id."@".$batch_no;?>
					</div>
				</td>
				<td><?php echo $q['testid'];?></td>
				<td><?php echo $tname['testname'];?></td>
				<td>
				<?php
				if($q[hp_id]>0)
				{
				$h_name=mysqli_fetch_array(mysqli_query($link, "select health_package_name from health_package_details where hp_id='$q[hp_id]' limit 0,1"));
				echo $h_name[health_package_name];				
				}
				else
				{
					echo "Individual Test";
				}
				?>
				</td>
			</tr>
			<?php
			$i++;
			}
		}
	
	
	?>
	
	
</table>

