<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$type=$_POST["type"];

$date=date("Y-m-d");
$time=date("H:i:s");


if($_POST["type"]=="pat_ipd_inv_det_entry")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$ds=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `batch_no` DESC");
	$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
	$num=mysqli_num_rows($q);
	$no=mysqli_num_rows($ds);
	?>
	
	<div class="span8" style="margin-left:0px;">
		<button class="btn btn-info" onclick="add_lab_entry()"><i class="icon-plus"></i> Add</button>
		<div id="lab_entry_data"></div>
	</div>
	<style>
		.widget-content{border-bottom:none;}
		.sp{margin-left:10px;}
		.bt{margin-bottom:5px;}
	</style>
	<?php
}
if($_POST["type"]=="inv_det_entry_div")
{
	?>
	<select id="inv_entry_tst" style="width:300px" onchange="add_para(this.value)">
		<option value="0">--Select Test--</option>
		<?php
		$inv_tst=mysqli_query($link,"select * from testmaster where testid IN(3299,3300,3301) order by testname");
		while($inv_t=mysqli_fetch_array($inv_tst))
		{
			$par=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from Testparameter where TestId='$inv_t[testid]'"));
			if($par[tot]==1)
			{
				echo "<option value='$inv_t[testid]'>$inv_t[testname]</option>";
			}
		}
		?>
	</select>
	<div id="test_data"></div>
	<?php
}
if($_POST["type"]=="inv_det_tst_par")
{
	$tst=$_POST['tst'];
	$test=mysqli_query($link,"select * from Testparameter where TestId='$tst' order by sequence");
	if(mysqli_num_rows($test)==1)
	{
		$par=mysqli_fetch_array($test);
		$pdet=mysqli_fetch_array(mysqli_query($link,"select * from Parameter_old where ID='$par[ParamaterId]'"));
		$unit=mysqli_fetch_array(mysqli_query($link,"select * from Units where ID='$pdet[UnitsID]' "));
		
		$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$tst'"));
		?>
		<table class="table table-bordered table-condensed">
		<tr>
			<td width="40%"><b><?php echo $tname[testname];?></b></td>
			<td><input type="text" id="<?php echo $par[ParamaterId];?>" class="inv_entry_tst_<?php echo $tst;?>" placeholder="Enter value here"/> <b><?php echo $unit[unit_name];?></b></td>
		</tr>
		<tr>
			<td><b>Date</b></td><td><input type="text" id="entry_date" class="form-control datepicker" value="<?php echo date('Y-m-d');?>"/></td>
		</tr>
		<tr>
			<td><b>Time</b></td>
			<td>
				<?php
				$cur_time=explode(":",date("h:i:A"));
				$hour=12;$min=60;
				?><select id="hour" class="span1"><?php
				for($i=1;$i<=$hour;$i++)
				{
					if($cur_time[0]==$i){ $h_sel="Selected='selected'"; }else{ $h_sel="";}
					echo "<option $h_sel>$i</option>";
				}
				?></select>
				<select id="min" class="span1"><?php
				for($j=0;$j<=$min;$j++)
				{
					if($j<10){ $nj="0".$j; } else{ $nj=$j; }
					
					if($cur_time[1]==$nj){ $m_sel="Selected='selected'"; }else{ $m_sel="";}
					echo "<option $m_sel>$nj</option>";
				}
				?></select>
				<select id="time_mer" class="span1">
					<option <?php if($cur_time[2]=="AM"){ echo "selected";}?> >AM</option>
					<option <?php if($cur_time[2]=="PM"){ echo "selected";}?>>PM</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan='2' style='text-align:center'><button class="btn btn-sm btn-info" onclick="save_test_entry(<?php echo $tst;?>)"><i class="icon-save"></i> Save</button></td>
		</tr>
		
		</table>
		<?php
	}
	
}
if($_POST["type"]=="inv_det_tst_result")
{
	
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$tst=$_POST['tst'];
	
	
	$date=$_POST['date'];
	$time=$_POST['time'];
	
	$user=$_POST['user'];
	
	$batch=mysqli_fetch_array(mysqli_query($link,"SELECT max(`batch_no`) as tot FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$batch_no=$batch[tot]+1;
	
	$tinfo=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$tst'"));
	
	if(mysqli_query($link,"INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`,`sample_id`,`test_rate`,`test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','','$ipd','$batch_no','$tst','0','$tinfo[rate]','0','$date','$time','$user','6')"))
	{
		$seq=1;
		$param=explode("@@koushik@@",$_POST[par]);
		foreach($param as $par)
		{
			if($par)
			{
				$p_res=explode("##koushik##",$par);
				mysqli_query($link,"INSERT INTO `testresults`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `sequence`, `result`, `range_status`, `range_id`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) VALUES ('$uhid','','$ipd','$batch_no','$tst','$p_res[0]','$seq','$p_res[1]','0','0','$time','$date','0','$user','$user','0')");
			}
		}
	}
}
if($_POST["type"]=="inv_tst_result_data")
{
	
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Date</th><th>Time</th><th>Test</th><th>Result</th><th>User</th>
		</tr>
	
	<?php
	
	$dates=mysqli_query($link,"select distinct date from patient_test_details where patient_id='$uhid' and ipd_id='$ipd' and type='6' order by slno desc");
	while($dt=mysqli_fetch_array($dates))
	{
		$i=1;
		$d_det=mysqli_query($link,"select * from patient_test_details where patient_id='$uhid' and ipd_id='$ipd' and date='$dt[date]' and type='6' order by time");
		$tot_d=mysqli_num_rows($d_det);
		?>
		<tr style="border-top:1px solid">
			<td rowspan='<?php echo $tot_d;?>' style='border-top:2px solid'><b><?php echo convert_date($dt[date]);?></b></td>
			<?php
			
			
			while($dd=mysqli_fetch_array($d_det))
			{
				$tinfo=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$dd[testid]'"));
				if($i>1)
				{
					echo "<tr>";
				}
				?>	
				<td><?php echo convert_time($dd[time]);?></td>
				<?php
				$test=mysqli_query($link,"select * from Testparameter where TestId='$dd[testid]' order by sequence");
				if(mysqli_num_rows($test)==1)
				{
					$par=mysqli_fetch_array($test);
					$pdet=mysqli_fetch_array(mysqli_query($link,"select * from Parameter_old where ID='$par[ParamaterId]'"));
					$unit=mysqli_fetch_array(mysqli_query($link,"select * from Units where ID='$pdet[UnitsID]' "));
					
					$res=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and ipd_id='$ipd' and batch_no='$dd[batch_no]' and testid='$dd[testid]' and paramid='$par[ParamaterId]'"));
					
					$emp=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res[tech]'"));
					?>
					<td><?php echo $tinfo[testname];?></td>
					<td><?php echo $res[result]." ".$unit[unit_name];?></td>
					<td><?php echo $emp[name];?></td>
					<?php
					
				}
				$i++;
				?> </tr> <?php
			}
		
		
	}
	
	?> </table> <?php
	
}

?>
