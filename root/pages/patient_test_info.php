<?php
include("../../includes/connection.php");
require("../../includes/global.function.php");

$uhid=$_POST['uhid'];
$pin=$_POST['pin'];
$batch_no=$_POST['batch_no'];
$val=$_POST['val'];

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
	$qry1="select testid from $table_name where patient_id='$uhid' and (`opd_id`='$pin' or `ipd_id`='$pin') and `batch_no`='$batch_no' and testid in(select testid from testmaster where category_id='1' and testname like '%$val%') GROUP BY `testid` order by slno";
}
else
{
	$qry1=" SELECT `testid` FROM `$table_name` WHERE `patient_id`='$uhid' and (`opd_id`='$pin' or `ipd_id`='$pin') and `batch_no`='$batch_no' and testid in(select testid from testmaster where category_id='1') GROUP BY `testid` order by `slno` ";
}

$qry=mysqli_query($GLOBALS["___mysqli_ston"], $qry1);

$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$uhid'"));

$pat_id=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_details where patient_id='$uhid' and (`opd_id`='$pin' or `ipd_id`='$pin') and `batch_no`='$batch_no' limit 1"));

$opd_id=$pat_id[opd_id];
$ipd_id=$pat_id[ipd_id];

if($info["dob"]!=""){ $age=age_calculator($info["dob"]); }else{ $age=$info["age"]." ".$info["age_type"]; }
?>

<table class="table table-bordered" id="pat_det_info">
  <tr>
    <td><b>UHID No.</b> <br /><span id="uhid_td"><?php echo $uhid;?></span> </td>
    <?php
		if($pat_id[opd_id]!='')
		{
			?> <td><b>OPD ID </b> <br /><span id="opd_td"><?php echo $pin;?></span><span style="display:none;"
        id="ipd_td"></span></td> <?php
		}
		else
		{
			?> <td><b>IPD ID </b> <br /><span id="ipd_td"><?php echo $pin;?></span><span style="display:none;"
        id="opd_td"></span></td> <?php	
		}
		?>
    <td><b>Batch No</b><br /><span id="batch_td"><?php echo $batch_no;?></span></td>
    <td><b>Name </b> <br /> <span id="name_td"><?php echo $info[name];?></span></td>
    <td><b>Age/Sex</b><br /><span id="age_td"><?php echo $age;?></span>/<span
        id="sex_td"><?php echo $info[sex];?></span></td>

    <td><b>Date Time</b><br />
      <span id="dt_tim_td"><?php echo convert_date($pat_id[date])." / ".convert_time($pat_id[time]);?></span>
    </td>
  </tr>
</table>
<hr />
<div id="test_info" style="display:none">
  Select Test <input type="text" id="test_id" onkeyup="path_select_test(this.value,event)" />

  <div class="accordion" id="accordion2">
    <?php
				$i = 1;
				while($q=mysqli_fetch_array($qry))
				{
					$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$q[testid]'"));
					$num=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$q[testid]'"));
					if($num>0)
					{
						$cls="green";
					}
					else
					{
						$cls="red";
						$summ=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$q[testid]'"));
						if($summ>0)
						{
							$cls="green";	
						}
					}
					
					if($q[testid]=="1227")
					{
						$wid=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' "));
						if($wid>0)
						{
							$cls="green";
						}
					}

					$num2=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testreport_print where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$q[testid]'"));
					if($num2>0)
					{
						$cls="grey_btn";
					}
					$result = '<div id="results'.$q['testid'].'"></div>';
					?>

    <div class="accordion-group">
      <div id="test_tr<?php echo $i; ?>" onclick="load_test_param1('<?php echo $i; ?>', '<?php echo $q['testid']; ?>')"
        class="accordion-heading">
        <a class="accordion-toggle" id="btn_<?php echo $q['testid']; ?>" data-toggle="collapse"
          data-parent="#accordion2" href="#collapse<?php echo $q['testid']; ?>"><span
            class="btn_round_msg btn_round_msg1 <?php echo $cls; ?>"><?php echo $i;?></span>
          <?php echo $tname[testname]; ?>
        </a>
        <div style="display:none" id="test_dis<?php echo $i;?>">
          <?php echo "@".$i."@".$q['testid'];?>
        </div>
      </div>
      <div id="collapse<?php echo $q['testid']; ?>" class="accordion-body collapse">
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
