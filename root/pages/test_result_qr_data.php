<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_info["branch_id"];

$discount_element_disable="";
if($emp_info["discount_permission"]==0)
{
	$discount_element_disable="readonly";
}

if($_POST["type"]=="load_all_pat")
{
	$pat_uhid=$_POST["pat_uhid"];
	$pin=$_POST["pin"];
	$phone=$_POST["phone"];
	$list_start=$_POST["list_start"];
	
	$q=" SELECT * FROM `patient_test_details` WHERE `slno`>0 ";
	
	$z=0;
	
	if(strlen($phone)>3)
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `phone` like '$phone%' ) ";
		$z=1;
	}
	if(strlen($pat_uhid)>2)
	{
		$q.=" AND `patient_id` like '$pat_uhid' ";
		$z=1;
	}
	if(strlen($pin)>2)
	{
		$q.=" AND `opd_id` like '$pin' ";
		$z=1;
	}
	
	//~ $q.=" AND `branch_id`='$branch_id' order by `slno` DESC limit ".$list_start;
	$q.=" order by `slno` DESC limit ".$list_start;
	
	if($z==0)
	{
		$q="";
	}
	
	//echo $q;
	
	$pat_reg_qry=mysqli_query($link, $q );
	
?>
	<table class="table table-bordered text-center">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No.</th>
				<th>Patient Name</th>
				<!--<th>Age</th>-->
				<th>Sex</th>
				<!--<th>Phone</th>
				<th>Reg Time</th>-->
				<th>Test Name</th>
				<th>Result Status</th>
			</tr>
		</thead>
	<?php
		$n=1;
		$same_bill="";
		$pat_num=mysqli_num_rows($pat_reg_qry);
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			if($same_bill!=$pat_reg["patient_id"])
			{
				$same_bill=$pat_reg["patient_id"];
				$i=1;
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
				
				$reg_date=$pat_reg["date"];
				
				if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			}
			else
			{
				$i=0;
			}
			
			$uhid=$pat_reg["patient_id"];
			$opd_id=$pat_reg["opd_id"];
			$ipd_id=$pat_reg["ipd_id"];
			$batch_no=$pat_reg["batch_no"];
			$testid=$pat_reg["testid"];
			
			$testresult_path_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' "));
			$testresult_card_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_card` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' "));
			$testresult_radi_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' "));
			$testresult_wild_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' "));
			$testresult_summ_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' "));
			
			$total_num=$testresult_path_num+$testresult_card_num+$testresult_radi_num+$testresult_wild_num+$testresult_summ_num;
			
			if($total_num==0)
			{
				$td_function="";
				$tr_back_color="style=''";
				
				$tr_title="title='No result'";
				
				$result_status="<img src='../images/Delete.png' style='width: 20px;'>";
			}
			else
			{
				$td_function="onclick=\"redirect_page('$uhid','$opd_id','$ipd_id','$batch_no','$testid')\"";
				$tr_back_color="style='cursor:pointer;'";
				$tr_title="";
				
				$result_status="<img src='../images/right.png' style='width: 20px;'>";
			}
			
			$test_info=mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$pat_reg[testid]' "));
	?>
			<tr>
		<?php if($i==1){ ?>
				<td rowspan="<?php echo $pat_num; ?>"><?php echo $n; ?></td>
				<td rowspan="<?php echo $pat_num; ?>"><?php echo $pat_info["patient_id"]; ?></td>
				<td rowspan="<?php echo $pat_num; ?>"><?php echo $pat_reg["opd_id"]; ?></td>
				<td rowspan="<?php echo $pat_num; ?>"><?php echo $pat_info["name"]; ?></td>
				<!--<td rowspan="<?php echo $pat_num; ?>"><?php echo $age; ?></td>-->
				<td rowspan="<?php echo $pat_num; ?>"><?php echo $pat_info["sex"]; ?></td>
				<!--<td rowspan="<?php echo $pat_num; ?>"><?php echo $pat_info["phone"]; ?></td>
				<td rowspan="<?php echo $pat_num; ?>">
					<?php echo date("d-M-Y",strtotime($pat_reg["date"])); ?>
					<?php echo date("h:i A",strtotime($pat_reg["time"])); ?>
				</td>-->
		<?php } ?>
				<td <?php echo $tr_back_color." ".$tr_title." ".$td_function; ?> >
					<?php echo $test_info["testname"]; ?>
				</td>
				<td <?php echo $tr_back_color." ".$tr_title." ".$td_function; ?> >
					<?php echo $result_status; ?>
				</td>
			</tr>
	<?php
			$n++;
		}
	?>
	</table>
	
<?php
}
if($_POST["type"]=="load_result")
{
	//print_r($_POST);
	$patient_id=$_POST["uhid"];
	$opd_id=$_POST["opd"];
	$ipd_id=$_POST["ipd"];
	$batch_no=$_POST["batch"];
	$testid=$_POST["testid"];
	
	if($opd_id){ $pin=$opd_id; }
	if($ipd_id){ $pin=$ipd_id; }
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' "));
	
	$reg_date=$pat_reg["date"];
	
	if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
	
	$lab_no=$pin.$batch_no.$testid;
	
	$test_info=mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$testid' "));
	
	$test_result="100mg/dl";
?>
	<div>
		<div id="qrcode_div">
		<?php
			include('../../phpqrcode/qrlib.php');
			$tempDir = '../../phpqrcode/temp/'; 
			
			$filename = $c_user.str_replace("/", "", $lab_no).'.png';
			
			$target_file="../../phpqrcode/temp/".$c_user."*.*";
			
			foreach (glob($target_file) as $filename_del) {
				unlink($filename_del);
			}
			
			$codeContents="UHID : ".$patient_id."\n";
			$codeContents.="Bill No : ".$pin."\n";
			//$codeContents.="Bill Date : ".date("jS F Y", strtotime($pat_reg["date"]))."\n";
			$codeContents.="Patient Name : ".$pat_info["name"]."\n";
			$codeContents.="Test Name : ".$test_info["testname"]."\n";
			$codeContents.="Test Result : ".$test_result."\n";
			
			//echo $codeContents;
			
			QRcode::png($codeContents, $tempDir.''.$filename, QR_ECLEVEL_S, 8);
			
			echo '<center><img src="../phpqrcode/temp/'.$filename.'" style="width:250px; height:250px;"><br></center>';
		?>
		</div>
		<br>
		<br>
		<center>
			<button class="btn btn-inverse" onclick="view_all()"><i class="icon-backward"></i> Back</button>
		</center>
	</div>
<?php
}

?>
