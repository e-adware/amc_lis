<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");

$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$val=$_POST['val'];

if($opd_id)
{
	$bill_no=$opd_id;
}
if($ipd_id)
{
	$bill_no=$ipd_id;
}

if($reporting_without_sample_receive==1)
{
	$table_name="phlebo_sample";
}
else
{
	$table_name="patient_test_details";
}

if($val)
{
	$str="select testid from $table_name where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid in(select testid from testmaster where category_id='1' and testname like '%$val%') GROUP BY `testid` order by slno";
}
else
{
	$str=" SELECT `testid` FROM `$table_name` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid in(select testid from testmaster where category_id='1') GROUP BY `testid` order by `slno` ";
}

//echo $str;

$qry=mysqli_query($link, $str);

$pat_info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$uhid'"));

$pat_test_det=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_details where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' and `batch_no`='$batch_no' limit 1"));

if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$test_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$img="";
$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT `urgent` FROM `uhid_and_opdid` b WHERE `patient_id`='$uhid' AND (`opd_id`='$opd_id' OR `opd_id`='$ipd_id') "));
if($pat_reg["urgent"]==1)
{
	$img="<img src='../images/blinking_dot.gif' style='width:10px;' />";
}
?>

<table class="table table-bordered" id="pat_det_info">
	<tr>
		<th>UHID No.</th>
		<th>Bill No.</th>
		<th>Batch No.</th>
		<th>Name</th>
		<th>Age/Sex</th>
		<th>Date Time</th>
	</tr>
	<tr>
		<td>
			<?php echo $uhid;?>
			<input type="hidden" id="pat_uhid" value="<?php echo $uhid;?>">
			<?php
				echo $img;
			?>
		</td>
		<td>
			<?php echo $bill_no;?>
			<input type="hidden" id="pat_opd_id" value="<?php echo $opd_id;?>">
			<input type="hidden" id="pat_ipd_id" value="<?php echo $ipd_id;?>">
		</td>
		<td>
			<?php echo $batch_no;?>
			<input type="hidden" id="pat_batch_no" value="<?php echo $batch_no;?>">
		</td>
		<td>
			<?php echo $pat_info["name"];?>
		</td>
		<td>
			<?php echo $age;?>
		</td>
		<td>
			<?php echo date("d-m-Y",strtotime($pat_test_det["date"]))." ".date("h:i A",strtotime($pat_test_det["time"]));?>
		</td>
	</tr>
</table>
<hr />
<div id="test_info" style="display:none">
	Select Test <input type="text" id="test_id" onkeyup="path_select_test(this.value,event)" />
	<div class="accordion" id="accordion2">
<?php
		$i = 1;
		while($data=mysqli_fetch_array($qry))
		{
			$tname=mysqli_fetch_array(mysqli_query($link, "select testname from testmaster where testid='$data[testid]'"));
			$num=mysqli_num_rows(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$data[testid]'"));
			if($num>0)
			{
				$cls="green";
			}
			else
			{
				$cls="red";
				$summ=mysqli_num_rows(mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$data[testid]'"));
				if($summ>0)
				{
					$cls="green";
				}
			}
			
			if($data["testid"]=="1227")
			{
				$wid=mysqli_num_rows(mysqli_query($link, "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' "));
				if($wid>0)
				{
					$cls="green";
				}
			}

			$num2=mysqli_num_rows(mysqli_query($link, "select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$data[testid]'"));
			if($num2>0)
			{
				$cls="grey_btn";
			}
			$result = '<div class="load_test_param_div" id="results'.$data['testid'].'"></div>';
?>

			<div class="accordion-group">
				<div id="test_tr<?php echo $i; ?>" onclick="load_test_param1('<?php echo $i; ?>', '<?php echo $data['testid']; ?>')" class="accordion-heading">
					<a class="accordion-toggle" id="btn_<?php echo $data['testid']; ?>" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $data['testid']; ?>"><span class="btn_round_msg btn_round_msg1 <?php  echo $cls; ?>"><?php echo $i;?></span>
						<?php echo $tname["testname"]; ?>
					</a>
					<div style="display:none" id="test_dis<?php echo $i;?>">
						<?php echo "@".$i."@".$data['testid'];?>
					</div>
				</div>
				<div id="collapse<?php echo $data['testid']; ?>" class="accordion-body collapse">
					<div class="accordion-inner">
						<?php echo $result; ?>
					</div>
				</div>
			</div>
<?php
			$i++;
		}
?>
	</div>
	<div id="print"></div>
	<div style="display:flex; justify-content: center">
		<button class="btn btn-print" id="g_print" onclick="group_print()"><i class="icon-print"></i> Group Print (Ctrl+Z)</button>
	</div>
</div>
<?php
mysqli_close($link);
?>
