<html>
<head>

</head>
<body>

<h2>Generating Barcode </h2>

<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$pid=$_GET['pid'];
$opd=$_GET['opd_id'];
$ipd=$_GET['ipd_id'];
$vac=$_GET['vac'];
$tst_vac=$_GET['tst_vac'];
$batch_no=$_GET['batch_no'];
$user=$_GET['user'];

$date=date('Y-m-d');
$time=date("H:i:s");
$update_timestamp=$date." ".$time;
if($opd)
{
	$pin=$opd;
}
if($ipd)
{
	$pin=$ipd;
}

if(!$batch_no)
{
	$batch_no=1;
}
//------------Removing Old Data-------------//
$ndate=date('Y-m-d', strtotime('-1 months'));

mysqli_query($link,"delete from test_sample_result where date<'$ndate'");
//-----------------------------------------//

$sample="";
$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$pid' "));

$pat_reg=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where `patient_id`='$pid' AND `opd_id`='$pin'"));
$patType=$pat_reg['receipt_no'];
$hosp_no=$pat_reg['type_prefix'].$pat_reg['sample_serial'];

$memoDate=mysqli_fetch_array(mysqli_query($link,"SELECT `CashMemoDate` FROM `aPatientList` WHERE `PatientNo`='$pat_reg[hospital_no]' AND `CashMemoNo`='$pat_reg[cashMemoNo]'"));
if($memoDate)
{
	$cDate=explode(" ", $memoDate['CashMemoDate']);
	$c_date=explode("/", $cDate[0]);
	$cMemoDt=date("d/m/y", strtotime($c_date[2]."-".$c_date[1]."-".$c_date[0]));
}
else
{
	$cMemoDt=date("d/m/y", strtotime($pat_reg['date']));
}

if($patType=="IPD")
{
	$prefix="I";
}
if($patType=="OPD")
{
	$prefix="O";
}


$current_time	=date("His");
$sunrise		="080000";
$sunset			="153000";
if($current_time>=$sunrise && $current_time<=$sunset)
{
	//$ME=$prefix."/";
}
else
{
	$patType="EMER";
	$prefix="E";
}

//echo "<br/>SELECT DISTINCT b.`type_id` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$pid' AND a.`opd_id`='$pin' AND a.`dept_serial`='' AND a.`testid` IN ($tst_vac)";
$regDate=mysqli_fetch_array(mysqli_query($link,"SELECT `date` FROM `uhid_and_opdid` WHERE `patient_id`='$pid' AND `opd_id`='$pin'"));

if($regDate['date']==$date)
{
	$slQry=mysqli_query($link,"SELECT DISTINCT b.`type_id` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$pid' AND a.`opd_id`='$pin' AND a.`dept_serial`='' AND a.`testid` IN ($tst_vac)");
	while($r=mysqli_fetch_assoc($slQry))
	{
		$dept=$r['type_id'];
		$tableName='test_dept_serial_generator_'.$dept.$patType;
		//$val = mysqli_query($link,'select 1 from `$tableName` LIMIT 1');
		if(!mysqli_query($link,"select 1 from `$tableName` LIMIT 1"))
		{
			if(mysqli_query($link,"CREATE TABLE `$tableName` (`slno` int(11) NOT NULL, `patient_id` varchar(50) NOT NULL, `opd_id` varchar(50) NOT NULL, `type_id` int(11) NOT NULL, `date` date NOT NULL, `time` time NOT NULL, `user` int(11) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci"))
			{
				mysqli_query($link,"ALTER TABLE `$tableName` ADD PRIMARY KEY (`slno`)");
				mysqli_query($link,"ALTER TABLE `$tableName` MODIFY `slno` int(11) NOT NULL AUTO_INCREMENT");
				mysqli_query($link,"ALTER TABLE `$tableName` ADD INDEX(`patient_id`)");
				mysqli_query($link,"ALTER TABLE `$tableName` ADD INDEX(`opd_id`)");
				mysqli_query($link,"ALTER TABLE `$tableName` ADD INDEX(`type_id`)");
				mysqli_query($link,"ALTER TABLE `$tableName` ADD INDEX(`date`)");
				mysqli_query($link,"ALTER TABLE `$tableName` ADD INDEX(`time`)");
				mysqli_query($link,"ALTER TABLE `$tableName` ADD INDEX(`user`)");
			}
		}
		
		if($current_time>=$sunrise && $current_time<=$sunset)
		{
			$blankTableName="test_dept_serial_generator_".$dept."EMER";
			mysqli_query($link,"TRUNCATE TABLE `$blankTableName`");
		}
		else
		{
			$patType="EMER";
			$prefix="E";
			
			$blankTableName="test_dept_serial_generator_".$dept."OPD";
			mysqli_query($link,"TRUNCATE TABLE `$blankTableName`");
			
			$blankTableName="test_dept_serial_generator_".$dept."IPD";
			mysqli_query($link,"TRUNCATE TABLE `$blankTableName`");
		}
		
		//echo "<br/>INSERT INTO `$tableName`(`patient_id`, `opd_id`, `type_id`, `date`, `time`, `user`) VALUES ('$pid','$opd','$dept','$date','$time','$user')";
		mysqli_query($link,"INSERT INTO `$tableName`(`patient_id`, `opd_id`, `type_id`, `date`, `time`, `user`) VALUES ('$pid','$opd','$dept','$date','$time','$user')");
		//echo "<br/>SELECT `slno` FROM `$tableName` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `date`='$date' AND `time`='$time' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1";
		$lastSl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `$tableName` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `date`='$date' AND `time`='$time' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1"));
		//echo "<br/>SELECT `prefix` FROM `test_department` WHERE `id`='$dept'";
		$tstPre=mysqli_fetch_assoc(mysqli_query($link,"SELECT `prefix` FROM `test_department` WHERE `id`='$dept'"));
		$prefNo=$prefix."/".$lastSl['slno'];
		
		//echo "<br/>SELECT a.`slno` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`type_id`='$dept' AND a.`patient_id`='$pid' AND a.`opd_id`='$pin' AND a.`dept_serial`='' AND a.`testid` IN ($tst_vac)";
		$tstSel=mysqli_query($link,"SELECT a.`slno` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`type_id`='$dept' AND a.`patient_id`='$pid' AND a.`opd_id`='$pin' AND a.`dept_serial`='' AND a.`testid` IN ($tst_vac)");
		while($rr=mysqli_fetch_assoc($tstSel))
		{
			//echo "<br/>UPDATE `patient_test_details` SET `dept_serial`='$prefNo' WHERE `slno`='$r[slno]' AND `dept_serial`=''";
			mysqli_query($link,"UPDATE `patient_test_details` SET `dept_serial`='$prefNo' WHERE `slno`='$rr[slno]' AND `dept_serial`=''");
		}
	}
}
else
{
	$slQry=mysqli_query($link,"SELECT DISTINCT b.`type_id` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$pid' AND a.`opd_id`='$pin' AND a.`dept_serial`='' AND a.`testid` IN ($tst_vac)");
	while($r=mysqli_fetch_assoc($slQry))
	{
		$preCh=mysqli_fetch_array(mysqli_query($link,"SELECT `dept_serial` FROM `patient_test_details` WHERE `patient_id`='$pid' AND `opd_id`='$pin' AND `dept_serial`!=''"));
		$preChV=explode("/", $preCh['dept_serial']);
		$preFix=$preChV[0];
		
		$checkExistSL=mysqli_fetch_array(mysqli_query($link,"SELECT a.`dept_serial` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$pid' AND a.`opd_id`='$pin' AND b.`type_id`='$r[type_id]' AND a.`dept_serial`!=''"));
		if($checkExistSL)
		{
			$prefNo=$checkExistSL['dept_serial'];
		}
		else
		{
			$lastSLCheck=mysqli_fetch_array(mysqli_query($link,"SELECT a.`dept_serial` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`type_id`='$r[type_id]' AND a.`dept_serial`!='' AND a.`date`='$regDate[date]' ORDER BY a.`slno` DESC LIMIT 1"));
			$prefNo=explode("/", $lastSLCheck['dept_serial']);
			$prefNo=$preFix."/".($prefNo[1]+1);
		}
		
		$tstSel=mysqli_query($link,"SELECT a.`slno` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`type_id`='$r[type_id]' AND a.`patient_id`='$pid' AND a.`opd_id`='$pin' AND a.`dept_serial`='' AND a.`testid` IN ($tst_vac)");
		while($rr=mysqli_fetch_assoc($tstSel))
		{
			//echo "<br/>UPDATE `patient_test_details` SET `dept_serial`='$prefNo' WHERE `slno`='$rr[slno]' AND `dept_serial`=''";
			mysqli_query($link,"UPDATE `patient_test_details` SET `dept_serial`='$prefNo' WHERE `slno`='$rr[slno]' AND `dept_serial`=''");
		}
	}
}


// Barcode Start
$vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", " ");

$date_str=explode("-", $pat_reg["date"]);
$dis_year=$date_str[0];
$dis_month=$date_str[1];
$dis_date=$date_str[2];

$alp = range('A', 'Z');
$mnt=intval($date_str[1]-1);

$bar_id=$dis_year[2].$dis_year[3].$alp[$mnt].$dis_date;

if($pat_reg["type"]==1)
{
	//$bar_id=$bar_id."N".$pat_reg["pat_type"][0].$pat_reg["pat_type"][1].$pat_reg["sample_serial"];
	
	$bar_id=$bar_id."OP".$pat_reg["sample_serial"];
}
else if($pat_reg["type"]==2)
{
	$bar_id=$bar_id."IP".$pat_reg["sample_serial"];
}
else if($pat_reg["type"]==3)
{
	//$bar_id=$bar_id."IP".$pat_reg["sample_serial"];
	
	$bar_id=$bar_id."N".$pat_reg["pat_type"][0].$pat_reg["pat_type"][1].$pat_reg["sample_serial"];
}
else if($pat_reg["type"]==4)
{
	$bar_id=$bar_id."EC".$pat_reg["sample_serial"];
}
else if($pat_reg["type"]==5)
{
	//$bar_id=$bar_id."NE".$pat_reg["sample_serial"];
	
	$bar_id=$bar_id."NE".$pat_reg["pat_type"][0].$pat_reg["pat_type"][1].$pat_reg["sample_serial"];
}

// Barcode End


$pat_test=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_test_details` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `batch_no`='$batch_no' AND `testid` IN ($tst_vac)"));
$test_date=$pat_test["date"];

if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$test_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$culture_suff=0;
$check_culture=mysqli_fetch_array(mysqli_query($link,"select count(a.testid) as tot_cult from patient_test_details a,testmaster b where a.testid=b.testid and a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch_no' and LOWER(b.testname) like '%culture%'"));
if($check_culture["tot_cult"]>1)
{
	$culture_suff=1;
}

$age=substr($age,0,8);
//echo $age;

$ages=explode(" ",$age);

$pat_info['age']=$ages[0];
$pat_info['age_type']=$ages[1];

$vac=explode("@@",$vac);
foreach($vac as $vc)
{
	if($vc)
	{
		$nreg=str_replace("/","",$pin);
		//$barcode_id=$nreg;
		
		$barcode_id=$bar_id;
		
		$vac_det=mysqli_fetch_array(mysqli_query($link,"select * from vaccu_master where id='$vc'"));
		$barcode_id=$barcode_id.$vac_det["barcode_suffix"];
		
		if(!$_GET['sing'])
		{
			//----Check Barcode---//
			$chk_bar=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and barcode_id='$barcode_id'"));
			if($chk_bar["tot"]==0)
			{
				$test_suffix="";
				$test_suffix_str="SELECT DISTINCT a.`suffix` FROM `barcode_test_suffix` a, `test_vaccu` b WHERE a.`testid`=b.`testid` AND b.`vac_id`='$vc'";
				$test_suffix_qry=mysqli_query($link, $test_suffix_str);
				$test_suffix_num=mysqli_num_rows($test_suffix_qry);
				if($test_suffix_num>0)
				{
					$test_suffix_array=array();
					while($test_suffix=mysqli_fetch_array($test_suffix_qry))
					{
						$test_suffix_array[]=$test_suffix["suffix"];
					}
					
					$test_suffix=implode(",",$test_suffix_array);
				}
				
				$vname=mysqli_fetch_array(mysqli_query($link,"select * from vaccu_master where id='$vc'"));
				
				//$sample="@@".$barcode_id."==".$vname["type"]."==".$test_suffix;
				
				//$qry=mysqli_query($link, "SELECT DISTINCT `dept_serial`  FROM `patient_test_details` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `batch_no`='$batch_no' AND `testid` IN(SELECT DISTINCT `testid` FROM `phlebo_sample` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `batch_no`='$batch_no' AND `vaccu`='$vc' AND `testid` NOT IN(SELECT DISTINCT `testid`  FROM `test_sample_result` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `batch_no`='$batch_no'))");
				
				$test_serial="";
				$qry=mysqli_query($link, "SELECT DISTINCT `test_serial` FROM `phlebo_sample` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `vaccu`='$vc'");
				while($pat_test_serial=mysqli_fetch_array($qry))
				{
					if($pat_test_serial["test_serial"]>0)
					{
						$test_serial=$pat_test_serial["test_serial"];
					}
				}
				
				$pat_test_serial["dept_serial"]="";
				$qry=mysqli_query($link, "SELECT DISTINCT `dept_serial` FROM `patient_test_details` a, `phlebo_sample` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`=b.`ipd_id` AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND a.`patient_id`='$pid' AND a.`opd_id`='$opd' AND a.`batch_no`='$batch_no' AND b.`vaccu`='$vc'");
				while($pat_test_serial=mysqli_fetch_array($qry))
				{
					$sample.="@@".$barcode_id."==".$vname["type"]."==".$test_suffix;
					//$sample.="==".$pat_test_serial["dept_serial"];
					$sample.="==".$pat_test_serial["dept_serial"]."==".$test_serial;
				}
			}
			
			$test_qry=mysqli_query($link,"SELECT * FROM `phlebo_sample` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `ipd_id`='$ipd' AND `batch_no`='$batch_no' AND `vaccu`='$vc'");
			while($test=mysqli_fetch_array($test_qry))
			{
				$testid=$test["testid"];
				$sampleid=$test["sampleid"];
				
				$test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `testname`,`suffix` FROM `testmaster` WHERE `testid`='$testid'"));
				
				$culture=0;
				
				if (strpos($test_info['testname'],'culture') !== false) 
				{
					$culture=1;
				}
				
				if (strpos($test_info['testname'],'CULTURE') !== false) 
				{
					$culture=1;
				}
				
				if (strpos($test_info['testname'],'Culture') !== false) 
				{
					$culture=1;
				}
				
				if($culture==1)
				{
					if(!$max_iso_no){ $max_iso_no=3; }
					if(!$culture_setup_testid){ $culture_setup_testid=3374; }
					
					//$barcode_id=str_replace("/","",$pin);
					//$barcode_id=$pin;
					
					$barcode_id=$bar_id;
					
					if($culture_suff>0)
					{
						$barcode_id=$barcode_id.$test_info["suffix"];
					}
					
					for($iso_no=1;$iso_no<=$max_iso_no;$iso_no++)
					{
						$chk=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and testid='$testid' and iso_no='$iso_no'"));
						if($chk["tot"]==0)
						{
							//mysqli_query($link,"insert into test_sample_result(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `iso_no`, `result`, `time`, `date`, `user`) SELECT '$pid','$opd','$ipd','$batch_no','$barcode_id','$vc','$sampleid','0','$testid',`ParamaterId`,'$iso_no','','$time','$date','$user' FROM `Testparameter` WHERE `TestId`='$culture_setup_testid' ORDER BY `sequence` ASC");
							mysqli_query($link,"INSERT INTO `test_sample_result`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `iso_no`, `result`, `time`, `date`, `user`, `equip_name`, `update_timestamp`) VALUES ('$pid','$opd','$ipd','$batch_no','$barcode_id','$vc','$sampleid','0','$testid','0','$iso_no','','$time','$date','$user','','$update_timestamp')");
						}
					}
				}
				else
				{
					$iso_no="";
					
					//$param_qry=mysqli_query($link, "SELECT `TestId`, `ParamaterId`, `sample`, `vaccu` FROM `Testparameter` WHERE `TestId`='$testid' AND `vaccu`='$vc' ORDER BY `sequence` ASC");
					$param_qry=mysqli_query($link, "SELECT `TestId`, `ParamaterId`, `sample`, `vaccu` FROM `Testparameter` WHERE `TestId`='$testid' AND (`vaccu` = '$vc' OR `ParamaterId` IN (639, 640, 641)) ORDER BY `sequence` ASC");
					while($param_info=mysqli_fetch_array($param_qry))
					{
						$paramid=$param_info["ParamaterId"];
						
						$chk=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and testid='$testid' and paramid='$paramid' and iso_no='$iso_no'"));
						
						if($chk["tot"]==0)
						{
							//mysqli_query($link,"insert into test_sample_result(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `iso_no`, `result`, `time`, `date`, `user`) VALUES ('$pid','$opd','$ipd','$batch_no','$barcode_id','$vc','$sampleid','0','$testid','$paramid','$iso_no','','$time','$date','$user')");
							mysqli_query($link,"INSERT INTO `test_sample_result`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `iso_no`, `result`, `time`, `date`, `user`, `equip_name`, `update_timestamp`) VALUES ('$pid','$opd','$ipd','$batch_no','$barcode_id','$vc','$sampleid','0','$testid','$paramid','$iso_no','','$time','$date','$user','','$update_timestamp')");
						}
						else
						{
							$chk_v=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and testid='$testid' and paramid='$paramid' and iso_no='$iso_no' and vaccus!='$vc'"));
							
							if($chk_v["testid"] && $chk_v["result"]=="")
							{
								mysqli_query($link,"update test_sample_result set barcode_id='$barcode_id',vaccus='$vc' where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and testid='$testid' and paramid='$paramid'");
							}
						}
					}
				}
			}
			
			/*$test=mysqli_query($link,"select b.* from patient_test_details a,Testparameter b where a.testid=b.TestId and b.vaccu='$vc' and a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch_no'");
			while($tst=mysqli_fetch_array($test))
			{
				$chk=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and testid='$tst[TestId]' and paramid='$tst[ParamaterId]'"));
				
				if($chk["tot"]==0)
				{
					if(!$tst["sample"]){ $tst["sample"]=0; }
					
					mysqli_query($link,"insert into test_sample_result( `patient_id`, `opd_id`,`ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `iso_no`, `result`, `time`, `date`, `user`) VALUES ('$pid','$opd','$ipd','$batch_no','$barcode_id','$vc','$tst[sample]','0','$tst[TestId]','$tst[ParamaterId]','0','','$time','$date','$user')");
				}
				else
				{
					$chk_v=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and testid='$tst[TestId]' and paramid='$tst[ParamaterId]' and vaccus!='$vc'"));
					
					if($chk_v["testid"] && $chk_v["result"]=='')
					{
						mysqli_query($link,"update test_sa$pat_test_serial["dept_serial"]="";mple_result set barcode_id='$barcode_id',vaccus='$vc' where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and testid='$tst[TestId]' and paramid='$tst[ParamaterId]'");
					}
				}
				
				//---Remove Para which is not in TestPara---//
			
				$chk_para=mysqli_query($link,"select * from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and testid='$tst[TestId]'");
				while($cp=mysqli_fetch_array($chk_para))
				{
					if($cp["result"]=='')
					{
						$chk_tp=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from Testparameter where TestId='$tst[TestId]' and ParamaterId='$cp[paramid]'"));
						
						if($chk_tp["tot"]==0)
						{
							//mysqli_query($link,"delete from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and testid='$tst[TestId]'");
						}
					}
				}
			}
			*/
		}
		else
		{
			$test_suffix="";
			$test_suffix_str="SELECT DISTINCT a.`suffix` FROM `barcode_test_suffix` a, `test_vaccu` b WHERE a.`testid`=b.`testid` AND b.`vac_id`='$vc'";
			$test_suffix_qry=mysqli_query($link, $test_suffix_str);
			$test_suffix_num=mysqli_num_rows($test_suffix_qry);
			if($test_suffix_num>0)
			{
				$test_suffix_array=array();
				while($test_suffix=mysqli_fetch_array($test_suffix_qry))
				{
					$test_suffix_array[]=$test_suffix["suffix"];
				}
				
				$test_suffix=implode(",",$test_suffix_array);
			}
			
			$vname=mysqli_fetch_array(mysqli_query($link,"select * from vaccu_master where id='$vc'"));
			
			//$sample.="@@".$barcode_id."==".$vname["type"]."==".$test_suffix;
			
			$test_serial="";
			$qry=mysqli_query($link, "SELECT DISTINCT `test_serial` FROM `phlebo_sample` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `vaccu`='$vc'");
			while($pat_test_serial=mysqli_fetch_array($qry))
			{
				if($pat_test_serial["test_serial"]>0)
				{
					$test_serial=$pat_test_serial["test_serial"];
				}
			}
			
			$pat_test_serial["dept_serial"]="";
			$qry=mysqli_query($link, "SELECT DISTINCT `dept_serial` FROM `patient_test_details` a, `phlebo_sample` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`=b.`ipd_id` AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND a.`patient_id`='$pid' AND a.`opd_id`='$opd' AND a.`batch_no`='$batch_no' AND b.`vaccu`='$vc'");
			while($pat_test_serial=mysqli_fetch_array($qry))
			{
				$sample.="@@".$barcode_id."==".$vname["type"]."==".$test_suffix;
				//$sample.="==".$pat_test_serial["dept_serial"];
				$sample.="==".$pat_test_serial["dept_serial"]."==".$test_serial;
			}
		}
	}
	
	$dailySl=mysqli_fetch_array(mysqli_query($link,"SELECT `daily_slno` FROM `phlebo_sample_status` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `vaccu`='$vc'"));
	$dailySlno=$dailySl['daily_slno'];
}

if($sample!='')
{
	//$IP = $_SERVER['REMOTE_ADDR'];   
	//$computerName = gethostbyaddr($IP); 

	//$target_file="http://".$computerName."/barcodeprinter/barcode_generate.php?PoiU=".$sample."&name=".$pat_info['name']."&age=".$pat_info['age']."&age_type=".$pat_info['age_type']."&sex=".$pat_info['sex']."&pin=".$pin;
	
	$reg_date=$pat_reg["date"];
	
	if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
	
    $target_file="../../js_print/index.php?PoiU=".$sample."&name=".$pat_info['name']."&age=".$age."&sex=".$pat_info['sex']."&uhid=".$pid."&pin=".$pin."&test_time=".date("d-M",strtotime($pat_test["date"]))." ".date("h:i A",strtotime($pat_test["time"]))."&hosp=".$hosp_no."&memoDt=".$cMemoDt."&slNo=".$dailySlno;
	
?>
	<script>
		window.location="<?php echo $target_file;?>";
	</script>
<?php
	//die();
}
else
{
	?> <script>window.close();</script> <?php
}

//~ if($sample!='')
//~ {
	//~ $IP = $_SERVER['REMOTE_ADDR'];   
	//~ $computerName = gethostbyaddr($IP); 

	//~ $target_file="http://".$computerName."/barcodeprinter/barcode_generate.php?PoiU=".$sample."&name=".$pat_info['name']."&age=".$pat_info['age']."&age_type=".$pat_info['age_type']."&sex=".$pat_info['sex']."&pin=".$pin;

	//~ echo $target_file;

	//~ //header("Location: $target_file");
	//~ die();
//~ }
//~ else
//~ {
	//~ ?> <script>//window.close();</script> <?php
//~ }



?>
</body>
</html>
