<?php
include("../../includes/connection.php");
			
$uhid=$_POST["uhid"];
$opd_id=$_POST["opd"];
$ipd_id=$_POST["ipd"];
$batch_no=$_POST["batch"];
$tst=$_POST["testid"];

$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testmaster where testid='$tst'"));
$res=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults_rad where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'"));

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div style="font-size:15px;text-align:center;">
				<b><u><?php echo $tname['testname'];?></u></b>
			</div><br>
			<div style="min-height:600px;text-align:left;font-size:14px;width:100%">
			<?php
				$res1=$res[observ];
				$res_s=explode("@",$res1);
				if($res_s[1])
				{
					?>
					<div style="min-height:520px;text-align:left;font-size:14px">
					<?php
						echo $res_s[0];
					?>
					</div>
					<?php
					
					echo "<br/><b><i>Continued in next page...</i></b>";
					echo "<div id='page_break' style='page-break-after: always;'></div>";
					
					echo $res_s[1];
				}
				else
				{
					?>
					<div style="min-height:480px;text-align:left;font-size:14px;">
					<?php
						echo $res1;
					?>
					</div>
					<?php
				}

				?>

			</div>
		</div>
	</div>
</div>
