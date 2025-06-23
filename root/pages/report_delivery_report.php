<?php
session_start();

include("../../includes/connection.php");

$c_user=$_SESSION["emp_id"];

if(!$c_user)
{
	echo "Error";
	exit();
}

$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST["type"];

if($type==1)
{
	$pid=mysqli_real_escape_string($link,$_POST['pid']);
	$opd_id=mysqli_real_escape_string($link,$_POST['opd_id']);
	$ipd_id=mysqli_real_escape_string($link,$_POST['ipd_id']);
	$batch=mysqli_real_escape_string($link,$_POST['batch_no']);
	$bill_id=mysqli_real_escape_string($link,$_POST['bill_id']);

	$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name`,`phone` FROM `patient_info` WHERE `patient_id`='$pid'"));
?>
	<h4><u>Report Delivery</u></h4>
	<table class="table table-bordered table-condensed" width="100%">
		
		<tr>
			<th>Delivery to</th>
			<td><input type="text" id="del_name" value="<?php echo $pat_info["name"];?>" onclick="$(this).select()" style="width: 90%;"/></td>
		</tr>
		<tr>
			<th>Phone No</th>
			<td><input type="text" id="del_phone" value="<?php echo $pat_info["phone"];?>" onclick="$(this).select()" style="width: 90%;"/></td>
		</tr>
		<tr>
			<th>Remarks</th>
			<td><input type="text" id="del_remarks" style="width: 90%;"/></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button class="btn btn-save" onclick="report_save()"><i class="icon-save"></i> Deliver</button>
				
				<button id="close" class="btn btn-close" onclick="$('#mod1').click();$('#mod_chk1').val(0)"><i class="icon-off"></i> Close</button> 
			</td>
		</tr>
	</table>
<?php
}

if($type==2)
{
	$pid=mysqli_real_escape_string($link,$_POST['pid']);
	$opd_id=mysqli_real_escape_string($link,$_POST['opd_id']);
	$ipd_id=mysqli_real_escape_string($link,$_POST['ipd_id']);
	$batch_no=mysqli_real_escape_string($link,$_POST['batch_no']);
	
	$name=mysqli_real_escape_string($link,$_POST['name']);
	$phone=mysqli_real_escape_string($link,$_POST['phone']);
	$remarks=mysqli_real_escape_string($link,$_POST['remarks']);
	$tests=mysqli_real_escape_string($link,$_POST['tests']);
	
	$user=mysqli_real_escape_string($link,$_POST['user']);
	
	$zz=0;
	
	$tst=explode("@",$tests);
	foreach($tst as $testid)
	{
		if($testid)
		{
			if(mysqli_query($link,"INSERT INTO `patient_report_delivery_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `name`, `phone`, `remarks`, `user`, `date`, `time`) VALUES ('$pid','$opd_id','$ipd_id','$batch_no','$testid','$name','$phone','$remarks','$user','$date','$time')"))
			{
				$zz++;
			}
		}
	}
	
	echo $zz;
}
?>
