<?php
include("../../includes/connection.php");

$pid=$_POST['pid'];
$opd=$_POST['opd_id'];
$ipd=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$vacc=$_POST['vac'];
$vacc_n=$_POST['vac_n'];
$tst_vac=$_POST['tst_vac'];
$sampProcess=$_POST['sampProcess'];
$sampStat=$_POST['sampStat'];
$slno=$_POST['dailySlno'];
$user=$_POST['user'];

$date=date('Y-m-d');
$time=date("H:i:s");

$vv="";

$vac=explode("@@",$vacc);

foreach($vac as $vc)
{
	if($vc)
	{
		
		$tid=mysqli_query($link,"select distinct a.testid from patient_test_details a,Testparameter b where a.testid=b.TestId and b.vaccu='$vc' and a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch_no' and a.testid IN($tst_vac)");
		
		while($t=mysqli_fetch_array($tid))
		{
			$samp=mysqli_fetch_array(mysqli_query($link,"select sample from Testparameter where TestId='$t[testid]' and vaccu='$vc' and sample>0"));
			
			if(!$samp["sample"]){ $samp["sample"]=0; }
			
			$chk_tst=mysqli_fetch_array(mysqli_query($link,"select count(slno) as tot from phlebo_sample where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and testid='$t[testid]' and `batch_no`='$batch_no' and `vaccu`='$vc'"));
			if($chk_tst["tot"]==0)
			{
				// Urine Test Serial No. Start
				$test_serial=0;
				if($t["testid"]==806)
				{
					$testid=$t["testid"];
					
					$testCount=mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(`testid`) AS `total` FROM `phlebo_sample` WHERE `testid`='$testid' AND `date`='$date'"));
					
					$test_serial=$testCount["total"]+1;
				}
				// Urine Sample Serial End
				
				mysqli_query($link, "insert into phlebo_sample(`patient_id`, `opd_id`, `ipd_id`, `batch_no`,`vaccu`, `testid`, `sampleid`, `user`, `time`, `date`, `test_serial`) values('$pid','$opd','$ipd','$batch_no','$vc','$t[testid]','$samp[sample]','$user','$time','$date','$test_serial')");
				mysqli_query($link, "INSERT INTO `patient_vaccu_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vaccu_id`, `rate`, `time`, `date`) VALUES ('$pid','$opd','$ipd','$batch_no','$vc','0','$time','$date')");
			}
			else
			{
				//mysqli_query($link,"update phlebo_sample set vaccu='$vc',sampleid='$samp[sample]' where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and testid='$t[testid]' and `batch_no`='$batch_no' and vaccu!='$vc'");
			}
			
			$tst_ar.=$t["testid"].",";
		}
		$vv.=$vc.",";
	}
}


foreach($sampStat as $vStat)
{
	$vacc	=$vStat['vacc'];
	$stat	=$vStat['stat'];
	
	if(!$samp["sample"]){ $samp["sample"]=0; }
	
	$smpStat=mysqli_fetch_array(mysqli_query($link,"SELECT `slno` FROM `phlebo_sample_status` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `batch_no`='$batch_no' AND `vaccu`='$vacc'"));
	//if(!$smpStat)
	{
		mysqli_query($link, "INSERT INTO `phlebo_sample_status`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vaccu`, `sampleid`, `status_id`, `date`, `time`, `user`) VALUES ('$pid','$opd','$ipd','$batch_no','$vacc','$samp[sample]','$stat','$date','$time','$user')");
		if($stat!="1")
		{
			mysqli_query($link,"DELETE FROM `phlebo_sample` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `batch_no`='$batch_no' AND `vaccu`='$vacc' AND `sampleid`='$samp[sample]'");
		}
	}
}

$slCheck=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `phlebo_sample_status` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `batch_no`='$batch_no'"));
if($slCheck)
{
	mysqli_query($link,"UPDATE `phlebo_sample_status` SET `daily_slno`='$slno' WHERE `slno`='$slCheck[slno]'");
}


mysqli_query($link,"UPDATE `uhid_and_opdid` SET `urgent`='$sampProcess' WHERE `patient_id`='$pid' AND `opd_id`='$opd'");


$chk_tst1=explode(",",$tst_ar);
/*--------not mand-----------*/
//mysqli_query($link,"delete from  phlebo_sample where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and `batch_no`='$batch_no' and testid not in('" . implode( "', '" , $chk_tst1 ) . "' )");




$tst_vc=mysqli_query($link,"select * from phlebo_sample where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and `batch_no`='$batch_no'");
while($tv=mysqli_fetch_array($tst_vc))
{
	$chk_par=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from Testparameter where TestId='$tv[testid]' and vaccu='$tv[vaccu]'"));
	if($chk_par[tot]==0)
	{
		/*--------not mand-----------*/
		//mysqli_query($link,"delete from phlebo_sample where  patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and testid='$tv[testid]' and `batch_no`='$batch_no' and `vaccu`='$tv[vaccu]'");
	}
}



$vac_n=explode("@@",$vacc_n);
foreach($vac_n as $vc_n)
{
	if($vc_n)
	{
		/*--------not mand-----------*/
		//mysqli_query($link,"delete from phlebo_sample where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and vaccu='$vc_n' and `batch_no`='$batch_no'");
		/*--------not mand-----------*/
		//mysqli_query($link,"delete from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='batch_no' and vaccus='$vc_n' and result=''");
		
		/*
		$tid_n=mysqli_query($link,"select distinct a.testid from patient_test_details a,Testparameter b where a.testid=b.TestId and b.vaccu='$vc' and a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch_no'");	
		while($t_n=mysqli_fetch_array($tid_n))
		{
			//mysqli_query($link,"delete from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and testid='$t_n[testid]' and result=''");
		}
		*/
	}
}


/*
$all=explode("#",$all);
foreach($all as $al)
{
	if($al)
	{
		$al=explode("$",$al);
		$samp=$al[0];
		//mysqli_query($link, "delete from phlebo_sample where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and sampleid='$samp'");
		$tst=explode("@",$al[1]);
		foreach($tst as $t)
		{
			if($t)
			{
				$chk_tst=mysqli_num_rows(mysqli_query($link,"select * from phlebo_sample where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and testid='$t' and `batch_no`='$batch_no'"));
				if($chk_tst==0)
				{
					mysqli_query($link, "insert into phlebo_sample(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sampleid`, `user`, `time`, `date`) values('$pid','$opd_id','$ipd_id','$batch_no','$t','$samp','$user','$time','$date')");
				}
				else
				{
					mysqli_query($link,"update phlebo_sample set sample_id='$samp' where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and testid='$t' and `batch_no`='$batch_no'");
				}
				$tst_ar.=$t.",";
				
			}
		}
		
	}
}


$chk_tst1=explode(",",$tst_ar);

mysqli_query($link,"delete from  phlebo_sample where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid not in('" . implode( "', '" , $chk_tst1 ) . "' )");
*/
?>
