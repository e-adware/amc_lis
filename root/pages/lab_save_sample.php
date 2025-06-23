<?php
include("../../includes/connection.php");

$pid=$_POST['pid'];
$opd=$_POST['opd_id'];
$ipd=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$vacc=$_POST['vac'];
$vacc_n=$_POST['vac_n'];
$tst_vac=$_POST['tst_vac'];
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
			
			$chk_tst=mysqli_fetch_array(mysqli_query($link,"select count(slno) as tot from lab_sample_receive where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and testid='$t[testid]' and `batch_no`='$batch_no' and `vaccu`='$vc'"));
			if($chk_tst["tot"]==0)
			{
				mysqli_query($link, "insert into lab_sample_receive(`patient_id`, `opd_id`, `ipd_id`, `batch_no`,`vaccu`, `testid`, `sampleid`, `user`, `time`, `date`) values('$pid','$opd','$ipd','$batch_no','$vc','$t[testid]','$samp[sample]','$user','$time','$date')");
			}
			else
			{
				//mysqli_query($link,"update lab_sample_receive set vaccu='$vc',sampleid='$samp[sample]' where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and testid='$t[testid]' and `batch_no`='$batch_no' and vaccu!='$vc'");
			}
			
			$tst_ar.=$t["testid"].",";
		}
		$vv.=$vc.",";
	}
}


$chk_tst1=explode(",",$tst_ar);
mysqli_query($link,"delete from  lab_sample_receive where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and `batch_no`='$batch_no' and testid not in('" . implode( "', '" , $chk_tst1 ) . "' )");




$tst_vc=mysqli_query($link,"select * from lab_sample_receive where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and `batch_no`='$batch_no'");
while($tv=mysqli_fetch_array($tst_vc))
{
	$chk_par=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from Testparameter where TestId='$tv[testid]' and vaccu='$tv[vaccu]'"));
	if($chk_par[tot]==0)
	{
		mysqli_query($link,"delete from lab_sample_receive where  patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and testid='$tv[testid]' and `batch_no`='$batch_no' and `vaccu`='$tv[vaccu]'");
	}
}



$vac_n=explode("@@",$vacc_n);
foreach($vac_n as $vc_n)
{
	if($vc_n)
	{
		mysqli_query($link,"delete from lab_sample_receive where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and vaccu='$vc_n' and `batch_no`='$batch_no'");
		//mysqli_query($link,"delete from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='batch_no' and vaccus='$vc_n' and result=''");
	}
}

?>
