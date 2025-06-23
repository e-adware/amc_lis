<?php

include("../../includes/connection.php");
include("pathology_normal_range_new.php");

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}


$type=$_POST[type];

if($type==1)
{
	$uhid=$_POST[uhid];
	$opd_id=$_POST[opd_id];
	$ipd_id=$_POST[ipd_id];
	$batch_no=$_POST[batch_no];
	
	$test=$_POST[test];
	$param=$_POST[param];
	$chk_user=$_POST[chk_user];
	$res=$_POST[res];
	$user=$_POST[user];
	$rep_doc=$_POST[rep_doc];
	
	$date=date("Y-m-d");
	$time=date('H:i:s');
	
	$aprv=$_POST[aprv];
	echo $aprv;
	
	if($chk_user=="" || $chk_user==1)
	{
		if($aprv==1)
		{
			$nr=load_normal($uhid,$param,$res,0);
			$nr1=explode("#",$nr);
			$range_id=$nr1[2];
			$stat=0;
			if($nr1[1]=="Error")
			{
				$stat=1;
			}
			if(!$range_id){ $range_id=0; }
			
			$chk_res=mysqli_num_rows(mysqli_query($link,"select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test' and paramid='$param'"));
			if($chk_res>0)
			{
				$od=mysqli_fetch_array(mysqli_query($link,"select result from testresults where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test' and paramid='$param'"));
				if($od[result]!=$res)
				{
					mysqli_query($link,"insert into testresults_update(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `sequence`, `result`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `update_by`, `update_user_type`) values('$od[patient_id]', '$od[opd_id]', '$od[ipd_id]', '$od[batch_no]', '$od[testid]', '$od[paramid]', '$od[sequence]', '$od[result]', '$od[time]', '$od[date]', '$od[doc]', '$od[tech]', '$od[main_tech]', '$od[for_doc]', '$user', '1')");
				}
				
				mysqli_query($link,"update testresults set result='$res',range_status='$stat',range_id='$range_id',main_tech='$user',doc='$rep_doc' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test' and paramid='$param'");
			}
			else
			{
				$seq=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select sequence from Testparameter where TestId='$test' and ParamaterId='$param'"));
				
				$od_lis=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test' and paramid='$param'"));
				if($chk_lis[result]!='' && $chk_lis[result]!=$res)
				{
					mysqli_query($link,"insert into testresults_update(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `sequence`, `result`, `time`, `date`, `update_by`, `update_user_type`) values('$chk_lis[patient_id]', '$chk_lis[opd_id]', '$chk_lis[ipd_id]', '$chk_lis[batch_no]', '$chk_lis[testid]', '$chk_lis[paramid]', '$seq[sequence]', '$chk_lis[result]', '$chk_lis[time]', '$chk_lis[date]','$user', '1')");	
				}
			
				mysqli_query($link,"insert into testresults(patient_id,opd_id,ipd_id,batch_no,testid,paramid,iso_no,sequence,result,range_status,range_id,time,date,doc,tech,main_tech) values('$uhid','$opd_id','$ipd_id','$batch_no','$test','$param','0','$seq[sequence]','$res','$stat','$range_id','$time','$date','$rep_doc','$user','$user')");
			}
			mysqli_query($GLOBALS["___mysqli_ston"],"update patient_test_summary set main_tech='$user',doc='$rep_doc' where patient_id='$uhid' and  `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
			
			mysqli_query($GLOBALS["___mysqli_ston"],"delete from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
			
			if($rep_doc==0)
			{
				mysqli_query($GLOBALS["___mysqli_ston"],"insert into approve_details(patient_id,opd_id,ipd_id,batch_no,t_time,t_date,testid) values('$uhid','$opd_id','$ipd_id','$batch_no','$time','$date','$test')");
			}
			else
			{
				mysqli_query($GLOBALS["___mysqli_ston"],"insert into approve_details(patient_id,opd_id,ipd_id,batch_no,t_time,t_date,d_time,d_date,testid) values('$uhid','$opd_id','$ipd_id','$batch_no','$time','$date','$time','$date','$test')");
			}
		}
		else
		{
			mysqli_query($link,"update testresults set main_tech='0',doc='0' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test' and paramid='$param'");
		
			mysqli_query($GLOBALS["___mysqli_ston"],"update patient_test_summary set main_tech='0',doc='0' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
			
		}
	}
	else if($chk_user==2)
	{
		if($aprv==1)
		{
			$nr=load_normal($uhid,$param,$res,0);
			$nr1=explode("#",$nr);
			$range_id=$nr1[2];
			$stat=0;
			if($nr1[1]=="Error")
			{
				$stat=1;
			}
			if(!$range_id){ $range_id=0; }
			
			$seq=mysqli_fetch_array(mysqli_query($link,"select sequence from Testparameter where TestId='$test' and ParamaterId='$param'"));
			
			mysqli_query($link,"insert into testresults(patient_id,opd_id,ipd_id,batch_no,testid,paramid,iso_no,sequence,result,range_status,range_id,time,date,doc,tech,main_tech,for_doc) values('$uhid','$opd_id','$ipd_id','$batch_no','$test','$param','0','$seq[sequence]','$res','$stat','$range_id','$time','$date','$rep_doc','$user','$user','$fdoc')");		
			
			mysqli_query($GLOBALS["___mysqli_ston"],"delete from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
			
			mysqli_query($GLOBALS["___mysqli_ston"],"insert into approve_details(patient_id,opd_id,ipd_id,batch_no,t_time,t_date,d_time,d_date,testid) values('$uhid','$opd_id','$ipd_id','$batch_no','$time','$date','$time','$date','$test')");
		}
		else
		{
			mysqli_query($link,"delete from testresults where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test' and paramid='$param'");
		}
	}
	
	//---Check Status---//
	$chk_stat=mysqli_query($link,"select * from test_param_mandatory where testid='$test'");
	while($ck=mysqli_fetch_array($chk_stat))
	{
		$mand=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$test' and paramid='$ck[paramid]'"));
		if($mand[tot]==0)
		{
			mysqli_query($link,"update testresults set status='1' where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$test'");
			break;
		}	
	}
}
else if($type==2)
{
	$uhid=$_POST[uhid];
	
	$opd_id=$_POST[opd_id];
	$ipd_id=$_POST[ipd_id];
	$batch_no=$_POST[batch_no];
	
	$test=$_POST[test];
	
	$user=$_POST[user];
	$fdoc=$_POST[fdoc];

	$date=date("Y-m-d");
	$time=date('H:i:s');
	
	$aprv=$_POST[aprv];
	
	if($aprv==1)
	{
		mysqli_query($link,"update testresults set main_tech='$user',for_doc='$fdoc' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");			
		mysqli_query($GLOBALS["___mysqli_ston"],"update patient_test_summary set main_tech='$user',for_doc='$fdoc' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
			
		mysqli_query($GLOBALS["___mysqli_ston"],"delete from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
		
		mysqli_query($GLOBALS["___mysqli_ston"],"insert into approve_details(patient_id,opd_id,ipd_id,batch_no,t_time,t_date,testid) values('$uhid','$opd_id','$ipd_id','$batch_no','$time','$date','$test')");
	}
	else
	{
		mysqli_query($link,"update testresults set main_tech='0',for_doc='0' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");			
		mysqli_query($GLOBALS["___mysqli_ston"],"update patient_test_summary set main_tech='0',for_doc='0' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
			
		mysqli_query($GLOBALS["___mysqli_ston"],"delete from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
	}
	
	//-------Check Status--------//
	$chk_stat=mysqli_query($link,"select * from test_param_mandatory where testid='$test'");
	while($ck=mysqli_fetch_array($chk_stat))
	{
		$mand=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$test' and paramid='$ck[paramid]'"));
		if($mand[tot]==0)
		{
			mysqli_query($link,"update testresults set status='1' where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$test'");
			break;
		}	
	}
}

else if($type==3)
{
	$uhid=$_POST[uhid];
	
	$opd_id=$_POST[opd_id];
	$ipd_id=$_POST[ipd_id];
	$batch_no=$_POST[batch_no];
	
	//$test=$_POST[test];
	
	$user=$_POST[user];
	$fdoc=$_POST[fdoc];
	
	$aprv=$_POST[aprv];
	
	if($aprv==1)
	{
		mysqli_query($GLOBALS["___mysqli_ston"],"update widalresult set main_tech='$user',for_doc='$fdoc' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' ");
		
		mysqli_query($GLOBALS["___mysqli_ston"],"insert into approve_details(patient_id,opd_id,ipd_id,batch_no,t_time,t_date,testid) values('$uhid','$opd_id','$ipd_id','$batch_no','$time','$date','1227')");
	}
	else
	{
		mysqli_query($GLOBALS["___mysqli_ston"],"update widalresult set main_tech='0',for_doc='0' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' ");
		
		mysqli_query($GLOBALS["___mysqli_ston"],"delete from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='1227'");
	}
}
else if($type==4)
{
	$uhid=$_POST[uhid];
	
	$opd_id=$_POST[opd_id];
	$ipd_id=$_POST[ipd_id];
	$batch_no=$_POST[batch_no];
	
	$test=$_POST[test];
	
	$user=$_POST[user];
	$fdoc=$_POST[fdoc];
	
	$aprv=$_POST[aprv];
	
	if($aprv==1)
	{
		mysqli_query($link,"update testresults set main_tech='$user',for_doc='$fdoc' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");			
		mysqli_query($GLOBALS["___mysqli_ston"],"update patient_test_summary set main_tech='$user',for_doc='$fdoc' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
		
		mysqli_query($GLOBALS["___mysqli_ston"],"delete from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
		
		mysqli_query($GLOBALS["___mysqli_ston"],"insert into approve_details(patient_id,opd_id,ipd_id,batch_no,t_time,t_date,testid) values('$uhid','$opd_id','$ipd_id','$batch_no','$time','$date','$test')");
	}
	else
	{
		mysqli_query($link,"update testresults set main_tech='0',for_doc='0' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");			
		
		mysqli_query($GLOBALS["___mysqli_ston"],"update patient_test_summary set main_tech='0',for_doc='0' where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
		
		mysqli_query($GLOBALS["___mysqli_ston"],"delete from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
	}
}
else if($type==5)
{
	$uhid=$_POST[uhid];
	
	$opd_id=$_POST[opd_id];
	$ipd_id=$_POST[ipd_id];
	$batch_no=$_POST[batch_no];
	
	$test=$_POST[test];
	$param=$_POST[param];
	$user=$_POST[user];
	
	$date=date("Y-m-d");
	$time=date('H:i:s');
	

	mysqli_query($GLOBALS["___mysqli_ston"],"insert into test_sample_result_repeat select * from test_sample_result where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$test' and paramid='$param'");
	mysqli_query($GLOBALS["___mysqli_ston"],"update test_sample_result set result='' where patient_id='$uhid' and `opd_id`='$opd_id' and testid='$test' and paramid='$param'");
	mysqli_query($GLOBALS["___mysqli_ston"],"delete from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and testid='$test' and paramid='$param'");
	mysqli_query($GLOBALS["___mysqli_ston"],"update test_sample_result_repeat set date='$date',time='$time',user='$user' where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$test' and paramid='$param'");
}
else if($type==6)
{
	$uhid=$_POST[uhid];
	
	$opd_id=$_POST[opd_id];
	$ipd_id=$_POST[ipd_id];
	$batch_no=$_POST[batch_no];
	
	$test=$_POST[test];
	$param=$_POST[param];
	
	?>
	<div style="border:5px solid #5bb75b;border-radius:1%;background-color:white;padding:10px;width:900px">
		
	<table class="table table-bordered table-condensed">
	<?php
	$rep=mysqli_query($link,"select * from test_sample_result_repeat where patient_id='$uhid' and `opd_id`='$opd_id' and testid='$test' and paramid='$param'");
	while($rp=mysqli_fetch_array($rep))
	{
		$pname=mysqli_fetch_array(mysqli_query($link,"select * from Parameter_old where ID='$param'"));	
		$uname=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$rp[user]'"));
		$unit=mysqli_fetch_array(mysqli_query($link,"select unit_name from Units where ID='$pname[UnitsID]'"));
		?>
		<tr>
			<td><?php echo $pname[Name];?></td>
			<td><?php echo $rp[result]." ".$unit[unit_name];?></td>
			<td><i>Repeated By <?php echo $uname[name];?></i></td>
			<td><?php echo convert_time($rp[time])." / ".convert_date($rp[date]);?></td>
		</tr>
		<?php
	}
	?> </table> </div><?php
}

else if($type==7)
{
	$uhid=$_POST[uhid];
	$opd_id=$_POST[opd_id];
	$ipd_id=$_POST[ipd_id];
	$batch_no=$_POST[batch_no];
	
	$test=$_POST[test];
	
	$user=$_POST[user];
	
	$date=date("Y-m-d");
	$time=date('H:i:s');
	
	
	$tst_p=mysqli_query($link,"select * from test_sample_result where patient_id='$uhid' and `opd_id`='$opd_id' and testid='$test'");
	while($t_p=mysqli_fetch_array($tst_p))
	{
		mysqli_query($GLOBALS["___mysqli_ston"],"insert into test_sample_result_repeat select * from test_sample_result where patient_id='$uhid' and `opd_id`='$opd_id' and testid='$test' and paramid='$t_p[paramid]'");
		mysqli_query($GLOBALS["___mysqli_ston"],"update test_sample_result set result='' where patient_id='$uhid' and `opd_id`='$opd_id' and testid='$test' and paramid='$t_p[paramid]'");
		mysqli_query($GLOBALS["___mysqli_ston"],"delete from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and testid='$test' and paramid='$t_p[paramid]'");
		mysqli_query($GLOBALS["___mysqli_ston"],"update test_sample_result_repeat set date='$date',time='$time',user='$user' where patient_id='$uhid' and `opd_id`='$opd_id' and testid='$test' and paramid='$t_p[paramid]'");
	}
}

else if($type==8)
{
	$uhid=$_POST[uhid];
	$opd_id=$_POST[opd_id];
	$ipd_id=$_POST[ipd_id];
	$batch_no=$_POST[batch_no];
	$test=$_POST[test];
	
	$res=mysqli_real_escape_string($link,$_POST['result']);
	
	mysqli_query($GLOBALS["___mysqli_ston"],"update patient_test_summary set main_tech='$user',summary='$res' where patient_id='$uhid' and  `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' and testid='$test'");
}
?>
