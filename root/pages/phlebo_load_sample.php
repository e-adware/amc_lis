<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");
?>


<div style="padding:10px" align="center">
<b style="font-size:22px;">Receive Sample</b>

<input type="hidden" id="glob_barcode" value="<?php echo $glob_barcode;?>"/>

<table class="table table-bordered table-condensed">

<?php


function sort_func( $x, $y) 
{    
	if ($x== $y) 
		return 0; 
  
	if ($x < $y) 
		return -1; 
	else
		return 1; 
} 



$pid=$_POST["uhid"];
$opd=$_POST["opd"];
$ipd=$_POST["ipd"];
$batch_no=$_POST["batch_no"];
//$glob_barcode=$_POST["glob_barcode"];
$lavel=$_POST['lavel'];
$ses=$_POST['user'];

if($opd!="")
{
	$pin=$opd;
}else if($ipd!="")
{
	$pin=$ipd;
}

$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$pid'"));

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$pid' and `opd_id`='$pin' "));

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$dt_tm[type]' "));

$dis_id=$prefix_det["prefix"].": ".$pin;

if($pinfo["dob"]!=""){ $age=age_calculator($pinfo["dob"]); }else{ $age=$pinfo["age"]." ".$pinfo["age_type"]; }

echo "<tr style='display:none;'><th colspan='1'>UHID: <span id='h_no'>$pinfo[patient_id]</span></th><th>OPD ID: <span id='opd_id'>$opd</span></th><th>IPD ID: <span id='ipd_id'>$ipd</span></th><th>Batch No: <span id='batch_no'>$batch_no</span></th></tr>";
echo "<tr><th>Hospital No : $pinfo[hosp_no]</th><th colspan='1'>Sample No : $dt_tm[sample_serial]</th><th style='display:none;'>Batch No: $batch_no</th></tr>";
echo "<tr style='display:none;'><th>Cash Memo No : $dt_tm[cashMemoNo]</th><th colspan='1'>Barcode No : $pin</th><th style='display:none;'>Batch No: $batch_no</th></tr>";
echo "<tr><th>Name : $pinfo[name]</th><th colspan='2'>Age-Sex : $age - $pinfo[sex]</th></tr>";
echo "</table>";

echo "<table class='table table-bordered table-condensed table-report table-hover' id='samp_det_table'>";


$test=mysqli_query($link,"select * from patient_test_details where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and batch_no='$batch_no'");
while($tst=mysqli_fetch_array($test))
{
	$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$tst[testid]'"));
	$culture=0;
	if (strpos($tname["testname"],'culture') !== false) 
	{
		$culture=1;
	}
	
	if (strpos($tname["testname"],'CULTURE') !== false) 
	{
		$culture=1;
	}
	
	if (strpos($tname["testname"],'Culture') !== false) 
	{
		$culture=1;
	}
	
	//if (strpos(strtolower($tname["testname"]),'culture') !== false)  //------------------Culture--------------//
	if($culture==1)
	{
		mysqli_query($link,"delete from Testparameter where TestId='$tst[testid]'");
		$c_vac=mysqli_fetch_array(mysqli_query($link,"select vac_id from test_vaccu where testid='$tst[testid]'"));
		$c_smp=mysqli_fetch_array(mysqli_query($link,"select SampleId from TestSample where TestId='$tst[testid]'"));
		$parm=mysqli_query($link,"select * from Testparameter where TestId='$culture_setup_testid'");
		while($par=mysqli_fetch_array($parm))
		{
			mysqli_query($link,"INSERT INTO `Testparameter`(`TestId`, `ParamaterId`, `sequence`, `sample`, `vaccu`, `status`) VALUES ('$tst[testid]','$par[ParamaterId]','$par[sequence]','$c_smp[SampleId]','$c_vac[vac_id]','$par[status]')");
		}
		$vcc[]=$c_vac["vac_id"];
	}
	else
	{
		if($dt_tm["type"]==1 && $tst["testid"]==1327) // OPD && GLUCOSE RBS
		{
			$vcc[]=2;
		}else
		{
			$vaccu=mysqli_query($link,"select distinct vaccu from Testparameter where TestId='$tst[testid]' and vaccu>0");
			while($vac=mysqli_fetch_array($vaccu))
			{
				$vcc[]=$vac["vaccu"];
			}
		}
	}
}

$vcc1=usort($vcc,"sort_func");
$vcc2=array_unique($vcc);

?>
<tr>
	<th>#</th> <th>Vaccu</th> <th>Test Name</th> <th></th>
</tr>
<?php
$checkReceive=0;

$i=1;
foreach($vcc2 as $vc)
{
	if($vc)
	{
		$vname=mysqli_fetch_array(mysqli_query($link,"select type from vaccu_master where id='$vc'"));
	?>
	<tr>
		<?php
			$single_barc="disabled";
			$vc_class="icon-check-empty";
			$bc_col="rgb(234, 164, 130)";
			$vac_chk=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from phlebo_sample where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and batch_no='$batch_no' and `vaccu`='$vc'"));
			if($vac_chk[tot]>0)
			{
				$vc_class="icon-check";
				$single_barc="";
				$bc_col="rgb(146, 217, 146)";
				$checkReceive++;
			}
			
			$chk_tot=0;
			
			$chk_lis=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from test_sample_result where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and batch_no='$batch_no' and `vaccus`='$vc' and result!=''"));
			
			
			$chk_res=mysqli_fetch_array(mysqli_query($link,"select count(a.testid) as tot from testresults a,Testparameter b where a.testid=b.TestId and a.paramid=b.ParamaterId and b.vaccu='$vc' and a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch_no'"));
			
			$chk_sum=mysqli_fetch_array(mysqli_query($link,"select count(a.testid) as tot from patient_test_summary a,Testparameter b,testresults c where a.testid=b.TestId and b.vaccu='$vc' and a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch_no' and c.patient_id!='$pid' and c.opd_id!='$opd' and c.ipd_id!='$ipd' and c.batch_no!='$batch_no'"));
			
			$chk_tot=$chk_lis[tot]+$chk_res[tot]+$chk_sum[tot];
			
			if($chk_tot>0)
			{
				$onclick="check_vac_err('$vname[type]')";
				$icon_id="vacc_done";
			}
			else
			{
				$onclick="check_vac($i,$vc)";
				$icon_id="";
			}
					
			?>
			
			<td width="50px" onclick="<?php echo $onclick;?>" style="text-align:center;background-color:<?php echo $bc_col;?>" id="smp_td_<?php echo $i;?>">
			
		
			<i name="<?php echo $icon_id;?>" class="<?php echo $vc_class;?>" id="<?php echo $vc;?>" ></i>
		</td>
		<td onclick="<?php echo $onclick;?>" > <b><?php echo $vname["type"];?> </b></td>
		<td style="width:30%;">
			<?php
				$tid=mysqli_query($link,"select distinct a.testid from patient_test_details a,Testparameter b where a.testid=b.TestId and b.vaccu='$vc' and a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch_no'");
				
				while($td=mysqli_fetch_array($tid))
				{
					$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$td[testid]'"));
				
					echo "<div class='tests_phlebo'>";
					
					$test_chk_str="";
					$test_dis_str="";
					$test_chk=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from phlebo_sample where `patient_id`='$pid' and `opd_id`='$opd' and testid='$td[testid]'"));
					if($test_chk[tot]>0)
					{
						$test_chk_str="checked";
						$test_dis_str="disabled";
					}
				?>
				<label onclick="checked_test('<?php echo $i; ?>','<?php echo $vc; ?>','<?php echo $td["testid"]; ?>')">
					<input type="checkbox" id="test_check<?php echo $td["testid"]; ?>" value="<?php echo $td["testid"]; ?>" class="tst_vac test_vac_cls<?php echo $vc; ?>" <?php echo $test_chk_str; ?> <?php echo $test_dis_str; ?> />
				<?php
					echo $tname["testname"]."</label></div>";
				}
				
				if($vc==2 && $dt_tm["type"]==1) // OPD && GLUCOSE RBS
				{
					$tid=mysqli_query($link,"select distinct testid from patient_test_details where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and testid=1327");
					
					while($td=mysqli_fetch_array($tid))
					{
						$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$td[testid]'"));
					
						echo "<div class='tests_phlebo'>";
						
						$test_chk_str="";
						$test_dis_str="";
						$test_chk=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from phlebo_sample where `patient_id`='$pid' and `opd_id`='$opd' and testid='$td[testid]'"));
						if($test_chk[tot]>0)
						{
							$test_chk_str="checked";
							$test_dis_str="disabled";
						}
					?>
					<label onclick="checked_test('<?php echo $i; ?>','<?php echo $vc; ?>','<?php echo $td["testid"]; ?>')">
						<input type="checkbox" id="test_check<?php echo $td["testid"]; ?>" value="<?php echo $td["testid"]; ?>" class="tst_vac test_vac_cls<?php echo $vc; ?>" <?php echo $test_chk_str; ?> <?php echo $test_dis_str; ?> />
					<?php
						echo $tname["testname"]."</label></div>";
					}
				}
				
				if(mysqli_num_rows($tid)==0)
				{
					
				}
			?>
		</td>
		<td>
		<?php
			if($glob_barcode==1)
			{
		?>
			<button class="btn btn-primary" <?php echo $single_barc;?> onclick="barcode_single('<?php echo $pid;?>','<?php echo $opd;?>','<?php echo $ipd;?>','<?php echo $batch_no;?>','<?php echo $vc;?>')"><i class="icon-barcode"></i> Barcode</button> 
		<?php
			}
		?>
			<?php
				$but_vl="<i class='icon-comments-alt'></i> Clinical Note";
				$bt_class="btn btn-info";
				$bt_val="note";
				$nv=mysqli_fetch_array(mysqli_query($link,"select * from phlebo_sample_note where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and batch_no='$batch_no' and `vaccu`='$vc'"));
				if($nv)
				{
					//$but_vl="<i class='icon-comments-alt'></i> View";
					$bt_class="btn btn-success";
					$bt_val="view";
				}
			?>
			<button class="<?php echo $bt_class;?>" id="note_<?php echo $vc;?>" onclick="vac_note('<?php echo $pid;?>','<?php echo $opd;?>','<?php echo $ipd;?>','<?php echo $batch_no;?>',<?php echo $vc;?>,'<?php echo $vname["type"];?>')" value="<?php echo $bt_val;?>" > <?php echo $but_vl;?> </button>
			<input type="hidden" id="vac_saved_note_<?php echo $vc;?>" value="<?php echo $nv["note"];?>" />
			
			<select class="span4 sampStat" id="sampStat<?php echo $vc;?>" disabled>
				<?php
				$smpQry=mysqli_query($link, "SELECT * FROM `phlebo_sample_status_master`");
				while($rr=mysqli_fetch_array($smpQry))
				{
				?>
				<option value="<?php echo $rr['status_id'];?>"><?php echo $rr['name'];?></option>
				<?php
				}?>
			</select>
		</td>
		
	</tr>
	<?php
	$i++;
	}
}


echo "</table>";

$dailySl=mysqli_fetch_array(mysqli_query($link,"SELECT `daily_slno` FROM `phlebo_sample_status` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `daily_slno`!=''"));
$dailySlno=$dailySl['daily_slno'];

?>
<div style="text-align:left;">
	<label class="sampLabel"><input type="radio" name="sampProcess" value="0" <?php if($dt_tm['urgent']=="0"){echo "checked";}?> /> Routine</label>&nbsp;&nbsp;
	<label class="sampLabel"><input type="radio" name="sampProcess" value="1" <?php if($dt_tm['urgent']=="1"){echo "checked";}?> /> Emergency</label>
</div>

<input type="hidden" class="span1" id="dailySlno" value="<?php echo $dailySlno;?>" placeholder="Sl No" />
<button id="sel_all" name="all_sel" value="Select All" class="btn btn-primary" onclick="select_all()"><i class="icon-list-ul"></i> Select All</button>


<button id="ack" name="ack" value="Receive" class="btn btn-info" onclick="sample_accept('<?php echo $pid;?>','<?php echo $opd;?>','<?php echo $ipd;?>','<?php echo $batch_no;?>')" ><i class="icon-download-alt"></i> Receive</button>

<!--<button class="btn btn-print" onclick="print_trf('<?php echo $pid;?>','<?php echo $opd;?>','<?php echo $ipd;?>','<?php echo $batch_no;?>')"><i class="icon-print"></i> TRF</button>-->

<button id="close" name="close" value="Close" class="btn btn-danger"  onclick="hid_mod()" data-dismiss="modal"><i class="icon-off"></i> Close</button>
<?php
//if($checkReceive)
if(1==1)
{
?>
<button class="btn btn-success" id="patDetBCode"  onclick="cashMemoBarcode('<?php echo base64_encode($pid);?>','<?php echo base64_encode($opd);?>','<?php echo base64_encode($pin);?>')"><i class="icon-barcode"></i> Cash Memo Barcode</button>
<?php
}
?>
</div>

<table class="table table-condensed">
<?php
$j=1;
$sampRcv=mysqli_query($link,"SELECT * FROM `phlebo_sample_status` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `batch_no`='$batch_no' AND `status_id`!='1'");
while($sampRc=mysqli_fetch_array($sampRcv))
{
	$stName=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `phlebo_sample_status_master` WHERE `status_id`='$sampRc[status_id]'"));
	$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$sampRc[user]'"));
	$rcvDate="";
	$rcvTime="";
	if($sampRc['date']!="0000-00-00")
	{
		$rcvDate=convert_date($sampRc['date']);
	}
	if($sampRc['time']!="00:00:00")
	{
		$rcvTime=convert_time($sampRc['time']);
	}
	?>
	<tr>
		<td><?php echo $j;?></td>
		<td><button type="button" class="btn btn-primary btn-mini" onclick="" style="display:none;">Print</button></td>
		<td><?php echo $stName['name'];?></td>
		<td><?php echo $rcvDate." ".$rcvTime;?></td>
		<td><?php echo $emp['name'];?></td>
	</tr>
	<?php
	$j++;
}
?>
</table>
