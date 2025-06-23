<html>
<head>

</head>
<body>

<h2>Generating Barcode... </h2>

<?php
include("../../includes/connection.php");

$pid=$_GET['pid'];
$opd_id=$_GET['opd_id'];
$ipd_id=$_GET['ipd_id'];
$batch_no=$_GET['batch_no'];
$user=$_GET['user'];
$tid=$_GET['tid'];


if($opd_id)
{
	$pin=$opd_id;
}
if($ipd_id)
{
	$pin=$ipd_id;
}

$info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$pid' "));

$vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", " ");

$date=date("Y-m-d");
$time=date('h:i:s A');


$uhid_opdid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$pid' AND `opd_id`='$pin' "));

$nreg=str_replace("/","",$pin);
$nreg=$nreg.$batch_no;

$vc_id="";
$vc_n=mysqli_query($link, "select * from test_vaccu where testid='$tid'");
while($vcn=mysqli_fetch_array($vc_n))
{
	$vc_id.="@".$vcn['vac_id'];	
}

$vc=explode("@",$vc_id);
$vc1=array_unique($vc);


$samp=mysqli_fetch_array(mysqli_query($link, "select * from TestSample where TestId='$tid'"));

$barcode_id=$nreg;

$sname=mysqli_fetch_array(mysqli_query($link,"select Name from Sample where ID='$samp[SampleId]'"));
$sam = str_replace($vowels, "", $sname['Name']);

$sam=strtoupper($sam);
$sam1=substr($sam, 0, 1);
//$sam=substr($sname[Name], 0, 2);
$barcode_id=$barcode_id.$sam1;


$eq=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$tid'"));
$com_mode=mysqli_fetch_array(mysqli_query($link, "select * from equip_master where id='$eq[equipment]'"));


foreach($vc1 as $v)
{
	if($v)
	{
		$vname=mysqli_fetch_array(mysqli_query($link, "select * from vaccu_master where id='$v'"));
		$eq_name=mysqli_fetch_array(mysqli_query($link,"select short_name from lab_instrument_master where id='$eq[equipment]'"));
		$dep=mysqli_num_rows(mysqli_query($link,"select distinct(type_id) from testmaster where testid in(select testid from test_sample_result where barcode_id='$barcode_id')"));
		if($dep>1)
		{
			//$smp_t="**MD**";
		}
		else
		{
			//$smp_t="n";
		}
		
		
		$sample.="/".$barcode_id."=".$vname[type]."=1=,".$eq_name[short_name];
		
	}
}

//~ $n_sample="";
//~ $chk_smpl=explode("/",$sample);
//~ foreach($chk_smpl as $sm_n)
//~ {
	//~ if($sm_n)
	//~ {
		//~ $sm_e=explode("=",$sm_n);
		
		//~ $eq_l=mysqli_query($link,"select distinct(equipment) from testmaster where testid in(select testid from test_sample_result where barcode_id='$sm_e[0]')");
		//~ $chk_eq=mysqli_num_rows($eq_l);
		
		//~ $eq="";	
		//~ while($eq_list=mysqli_fetch_array($eq_l))
		//~ {
			
			//~ $eq_name=mysqli_fetch_array(mysqli_query($link,"select short_name from lab_instrument_master where id='$eq_list[equipment]'"));
			//~ $eq.=",".$eq_name[short_name];
		//~ }
		
		//~ $n_sample.="/".$sm_e[0]."=".$sm_e[1]."=1=".$eq;
		//~ echo $n_sample;
	//~ }
//~ }


$IP = $_SERVER['REMOTE_ADDR'];        // Obtains the IP address
$computerName = gethostbyaddr($IP); 

$target_file="http://".$computerName."/barcodeprinter/barcode_generate.php?PoiU=".$sample."&name=".$info['name']."&age=".$info['age']."&age_type=".$info['age_type']."&sex=".$info['sex']."&pin=".$pin."&serial=".$uhid_opdid[ipd_serial];;

header("Location: $target_file");
die();

?>
</body>
</html>
