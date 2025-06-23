<div style="padding:10px" align="center">
<h4>Receive Sample</h4>
<table class="table table-bordered table-condensed">

<?php
session_start();
include("../../includes/connection.php");
//$ses=$_SESSION['emp_id'];

$pid=$_POST["uhid"];
$opd="";
$ipd=$_POST["ipd"];
$batch_no=$_POST["batch_no"];
$lavel=$_POST['lavel'];
$ses=$_POST['user'];

if($opd!="")
{
	$dis_id="OPD ID: ".$opd;
}else if($ipd!="")
{
	$dis_id="IPD ID: ".$ipd;
}

$pinfo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$pid'"));

echo "<tr style='display:none;'><th colspan='1'>UHID: <span id='h_no'>$pid</span><th>OPD ID: <span id='opd_id'>$opd</span></th><th>IPD ID: <span id='ipd_id'>$ipd</span></th><th>Batch No: <span id='batch_no'>$batch_no</span></th></tr>";
echo "<tr><th>UHID: $pid<th colspan='1'>$dis_id</th><th>Batch No: $batch_no</th></tr>";
echo "<tr><th>Name:$pinfo[name]</th><th colspan='2'>Age-Sex:$pinfo[age] $pinfo[age_type] $pinfo[sex]</th></tr>";
echo "<tr><th>Sample</th><th colspan='2'>Tests</th></tr>";

$test=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_test_details` WHERE `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and `batch_no`='$batch_no' AND `testid` IN (SELECT `testid` FROM `testmaster` WHERE `category_id`='1')");

$i=1;
while($t=mysqli_fetch_array($test))
{
	$qwq=mysqli_query($link, "select * from sample_note where patient_id='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and `batch_no`='$batch_no' and test_id='$t[testid]' ");
	$edt=mysqli_fetch_array($qwq);
	$user=mysqli_num_rows($qwq);
	if($user>0)
	{
		if($edt['user']!=$ses)
		{
			$ds="disabled='disabled'";
			$btn="Note";
		}
		else
		{
			$ds="";
			$btn="Edit";
		}
	}
	else
	{
		$btn="Note";
		$ds="";
	}
	
    if($t['sample_id']!="76")
    {
	$sname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Sample where ID='$t[sample_id]'"));
	$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$t[testid]'"));
	
	$num=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_details where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and `batch_no`='$batch_no' and sample_id='$t[sample_id]' "));
	
	
	$chkt="";
	$dis2="";
	$phl_t=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from phlebo_sample where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and `batch_no`='$batch_no' and testid='$t[testid]'"));
	if($phl_t>0)
	{
		$chkt="Checked='checked'";
		if($lavel!="1")
		{
			$dis2="disabled='disabled'";
			//echo $lavel;
		}
	}
	if($i==1)
	{
		$chkd="";
		$dis="disabled='disabled'";
		$dis1="";
		$phl=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from phlebo_sample where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and `batch_no`='$batch_no' and sampleid='$t[sample_id]'"));
		if($phl>0)
		{
			$chkd="Checked='checked'";
			if($lavel!="1")
			{
				$dis1="disabled='disabled'";
			}
			$dis="";
		}
		$disb=0;
		
		echo "<tr><td rowspan='$num'><label><input type='checkbox' id='$t[sample_id]' onclick='select_sample(this.id)' class='samp' $chkd $dis1/><span></span>$sname[Name]</label><input type='hidden' id='val_$t[sample_id]' value='$disb'/></td><td><input type='checkbox' class='$t[sample_id]' value='$t[testid]' $chkt $dis2/><label><span></span>$i $tname[testname]</label></td><td><input type='button' id='$t[testid]' value='$btn' class='btn btn-info' onclick='note($t[testid],$batch_no)' $ds /></td></tr>";
	}
	else
	{
		echo "<tr><td><label><input type='checkbox' class='$t[sample_id]' value='$t[testid]' $chkt $dis2/><span></span>$i $tname[testname]</label></td><td><input type='button' id='$t[testid]' value='$btn' class='btn btn-info' onclick='note($t[testid],$batch_no)' $ds /></td></tr>";
	}
	
	if($i<$num)
	{
		$i++;
	}
	else
	{
		$i=1;
	}
    }	
}

?>
<tr>
	<th colspan="3">
		Vaccu:
		<?php
			$vcu="";
			$vacc=mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_test_details where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and `batch_no`='$batch_no' ");
			while($vc=mysqli_fetch_array($vacc))
			{
					$vc_n=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_vaccu where testid='$vc[testid]'");
					while($vcn=mysqli_fetch_array($vc_n))
					{
						$vc_id.="@".$vcn['vac_id'];	
					}
			}

			
			$vc=explode("@",$vc_id);
			$vc1=array_unique($vc);
		
			foreach($vc1 as $v)
			{
				if($v)
				{
					$vname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from vaccu_master where id='$v'"));
					$vcu.="<input type='checkbox' id='vac_$v' class='vac' /><label><span></span>$vname[type]</label> &nbsp; &nbsp; &nbsp;";
				}
			}
			
			echo rtrim($vcu, " , ");
		?>
	</th>
</tr>


</table>
<input type="button" id="ack" name="ack" value="Receive" class="btn btn-info" onclick="sample_accept('<?php echo $pid;?>','<?php echo $ipd;?>','<?php echo $batch_no;?>')"/>
</div>
