<?php
session_start();
include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);

function check_sms($pin,$batch)
{
	include("../../includes/connection.php");
	
	$phn=mysqli_fetch_array(mysqli_query($link,"select a.phone,a.patient_id from patient_info a,uhid_and_opdid b where a.patient_id=b.patient_id and b.opd_id='$pin'"));	
	if($phn[phone]!='')
	{
		$days=mysqli_query($link,"select distinct b.report_delivery_2 from testmaster b,patient_test_details a where a.testid=b.testid and a.patient_id='$phn[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and b.category_id='1' order by b.report_delivery_2");
		while($dt=mysqli_fetch_array($days))
		{
			$day_rep=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_details a,testmaster b where a.patient_id='$phn[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and b.category_id='1' and b.report_delivery_2='$dt[report_delivery_2]'"));
			
			$chk_sms_em=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from report_sms_email where patient_id='$phn[patient_id]' and opd_ipd_id='$pin' and batch_no='$batch' and report_delv_day='$dt[report_delivery_2]' and sms_email_status='1'"));
			
			if($day_rep>0 && $chk_sms_em[tot]!=1)
			{
				$day_rep=1;
				$rep_dd=mysqli_query($link,"select a.* from patient_test_details a,testmaster b where a.testid=b.testid and a.patient_id='$phn[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and b.report_delivery_2='$dt[report_delivery_2]' and b.category_id='1'");
				while($rep_d=mysqli_fetch_array($rep_dd))
				{
					$test_res=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testresults where patient_id='$phn[patient_id]' and (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$rep_d[testid]' and doc>0"));
					
					$test_sum=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_summary where patient_id='$phn[patient_id]' and (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$rep_d[testid]' and doc>0"));
					$test_wid[tot]=0;
					if($rep_d[testid]==1227)
					{
						$test_wid=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from widalresult where patient_id='$phn[patient_id]' and (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and doc>0 limit 1"));
					}
					
					if($test_res[tot]==0 && $test_sum[tot]==0 && $test_wid[tot]==0)
					{
						$day_rep=0;
						break;
					} 
				}
				
				$chk_en=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from report_sms_email where  patient_id='$phn[patient_id]' and opd_ipd_id='$pin' and batch_no='$batch' and report_delv_day='$dt[report_delivery_2]'"));
				if($chk_en[tot]==0)
				{
					mysqli_query($link,"insert into report_sms_email(patient_id,batch_no,opd_ipd_id,report_delv_day,test_status,sms_email_status) values('$phn[patient_id]','$batch','$pin','$dt[report_delivery_2]','$day_rep','0')");
				}
				
				if($day_rep==1)
				{
					mysqli_query($link,"update report_sms_email set sms_email_status='1',test_status='$day_rep' where patient_id='$phn[patient_id]' and opd_ipd_id='$pin' and batch_no='$batch' and report_delv_day='$dt[report_delivery_2]'");
					
					$encode_pid=base64_encode($phn[patient_id]);
					$encode_pin=base64_encode($pin);
					$encode_batch=base64_encode($batch);
					$encode_rd=base64_encode($dt[report_delivery_2]);
					
					$url="patient_report_download.php?pd=".$encode_pid."&pn=".$encode_pin."&bt=".$encode_batch."&rd=".$encode_rd;
					return $url;
				}
			}
		}
	}
	
}

if($_POST[type]=="aprv")
{
	
	$pin=$_POST[pin];
	$batch=$_POST[batch];
	$test=$_POST[test];
	$aprv=$_POST[aprv];
	$user=$_POST[user];
	$typ=$_POST[typ];
	
	$date=date("Y-m-d");
	$time=date('H:i:s');
	
	$pid=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$pin'"));
	
	if($aprv==1)
	{
		if($typ==1)
		{
			mysqli_query($GLOBALS["___mysqli_ston"],"update testresults set doc='$user' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$test'");
			mysqli_query($GLOBALS["___mysqli_ston"],"update patient_test_summary set doc='$user' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$test'");
			
			//mysqli_query($GLOBALS["___mysqli_ston"],"insert into doc_approved(patient_id,opd_id,testid,time,date) values('$pid[patient_id]','$pin','$test','$time','$date')");
		}
		else if($typ==2)
		{
			mysqli_query($GLOBALS["___mysqli_ston"],"update patient_test_summary set doc='$user' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$test'");
		}
		else if($typ==3)
		{
			mysqli_query($GLOBALS["___mysqli_ston"],"update widalresult set doc='$user' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch'");
		}
		else if($typ==4)
		{
			mysqli_query($GLOBALS["___mysqli_ston"],"update patient_histo_summary set doc='$user' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$test'");
		}
		
		//mysqli_query($GLOBALS["___mysqli_ston"],"update approve_details set d_time='$time',d_date='$date' where opd_id='$pin' and testid='$test'");
		$chk_ap_det=mysqli_num_rows(mysqli_query($link,"select * from approve_details where opd_id='$pin' and testid='$test'"));
		if($chk_ap_det>0)
		{
			mysqli_query($GLOBALS["___mysqli_ston"],"update approve_details set d_time='$time',d_date='$date' where opd_id='$pin' and testid='$test'");
		}
		else
		{
			mysqli_query($GLOBALS["___mysqli_ston"],"insert into approve_details(patient_id,visit_no,d_time,d_date,testid) values('$pid','$visit','$time','$date','$test')");
		}
		
		mysqli_query($GLOBALS["___mysqli_ston"],"INSERT INTO `doctor_approval_record`(`patient_id`, `opd_id`, `testid`, `doc`, `user`, `date`, `time`, `type`) VALUES ('$pid[patient_id]','$pin','$test','$user','$c_user','$date','$time','1')");
	}
	else
	{
		if($typ==1)
		{
			mysqli_query($GLOBALS["___mysqli_ston"],"update testresults set doc='0' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$test'");
		}
		else if($typ==2)
		{
			mysqli_query($GLOBALS["___mysqli_ston"],"update patient_test_summary set doc='0' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$test'");
		}
		else if($typ==3)
		{
			mysqli_query($GLOBALS["___mysqli_ston"],"update widalresult set doc='0' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch'");
		}
		else if($typ==4)
		{
			mysqli_query($GLOBALS["___mysqli_ston"],"update patient_histo_summary set doc='0' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$test'");
		}
		
		mysqli_query($GLOBALS["___mysqli_ston"],"update approve_details set d_time='' and d_date='' where opd_id='$pin' and testid='$test'");
		
		mysqli_query($GLOBALS["___mysqli_ston"],"INSERT INTO `doctor_approval_record`(`patient_id`, `opd_id`, `testid`, `doc`, `user`, `date`, `time`, `type`) VALUES ('$pid[patient_id]','$pin','$test','$user','$c_user','$date','$time','0')");
	}
	
	
	$dep=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select type_id from testmaster where testid='$test'"));
	$res_dep=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select distinct testid from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid in(select testid from testmaster where type_id='$dep[type_id]') and doc>0"));
	$res_sum1=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select distinct (a.testid),b.type_id from patient_test_summary a,testmaster b where a.testid=b.testid and b.type_id='$dep[type_id]' and a.patient_id='$pid' and a.visit_no='$visit' and a.testid not in(select testid from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch')"));	
	
	$tot=$res_dep+$res_sum1;
	echo $tot."@".$dep[type_id];
	
}
else if($_POST[type]=="reap")
{
	$pin=$_POST[pin];
	$batch=$_POST[batch];
	$visit=$_POST[visit];
	$test=$_POST[test];
	$param=$_POST[param];
	//$res=$_POST[res];
	$aprv=$_POST[aprv];
	$user=$_POST[user];

	$date=date("Y-m-d");
	$time=date('H:i:s');
	
	
	$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select reg_no from patient_reg_details where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch'"));
	
	mysqli_query($GLOBALS["___mysqli_ston"],"delete from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$test' and paramid='$param'");
	
	$res=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from test_sample_results where reg_no='$reg[reg_no]' and testid='$test' and paramid='$param'"));
	
	mysqli_query($GLOBALS["___mysqli_ston"],"insert into test_sample_result_repeat(patient_id,visit_no,barcode_id,testid,paramid,result,date,time) values()");
}
else if($_POST[type]=="update")
{
	include("pathology_normal_range_new.php");
	
	$pin=$_POST[pin];
	$batch=$_POST[batch];
	$tst=$_POST[tst];
	$prm=$_POST[prm];
	$res=mysqli_real_escape_string($GLOBALS["___mysqli_ston"],$_POST[res]);
	$reas=mysqli_real_escape_string($GLOBALS["___mysqli_ston"],$_POST[reas]);
	$user=$_POST[user];
	
	$date=date("Y-m-d");
	$time=date('H:i:s');
	
			
	$tst_query=mysqli_query($GLOBALS["___mysqli_ston"],"select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and paramid='$prm'");
	if(mysqli_num_rows($tst_query)>0)
	{
		$od=mysqli_fetch_array($tst_query);
		
		if($od[result]!=$res)
		{
			mysqli_query($link,"insert into testresults_update(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `sequence`, `result`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `update_by`, `update_user_type`) values('$od[patient_id]', '$od[opd_id]', '$od[ipd_id]', '$od[batch_no]', '$od[testid]', '$od[paramid]', '$od[sequence]', '$od[result]', '$od[time]', '$od[date]', '$od[doc]', '$od[tech]', '$od[main_tech]', '$od[for_doc]', '$user', '2')");	
		}
		
		$nr=load_normal($od[patient_id],$od[paramid],$res,0);
		$nr1=explode("#",$nr);
		$range_id=$nr1[2];
		$stat=0;
		if($nr1[1]=="Error")
		{
			$stat=1;
		}
		if(!$range_id){ $range_id=0; }
		
		mysqli_query($GLOBALS["___mysqli_ston"],"update testresults set result='$res',range_id='$range_id',range_status='$stat' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and paramid='$prm'");
	}
	else
	{
		
		//$seq=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select sequence from Testparameter where TestId='$tst' and ParamaterId='$prm'"));
		//mysqli_query($GLOBALS["___mysqli_ston"],"insert into testresults(patient_id,visit_no,testid,paramid,sequence,result,time,date,doc,tech,main_tech) values('$pid','$visit','$tst','$prm','$seq[sequence]','$res','$time','$date','0','$user','$user')");
	}
}
else if($_POST[type]=="update_histo")
{
	
	$res=mysqli_real_escape_string($GLOBALS["___mysqli_ston"],$_POST[res]);
	
	echo $res;
}
else if($_POST[type]=="update_all")
{
	$pin=$_POST[pin];
	$batch=$_POST[batch];
	$tst=$_POST[tst];
	$user=$_POST[user];
	$upd_all=mysqli_real_escape_string($link,$_POST['upd_all']);
	
	$upd=explode("@@",$upd_all);
	foreach($upd as $up)
	{
		if($up)
		{
			
			$ids=explode("###koushik###",$up);
			
			if($ids[1]!='')
			{
				$chk_data=mysqli_query($link,"select * from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and paramid='$ids[0]'");
				if(mysqli_num_rows($chk_data)>0)
				{
					mysqli_query($link,"update testresults set result='$ids[1]' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and paramid='$ids[0]'");
				}
				else
				{
					$date=date("Y-m-d");
					$time=date('H:i:s');
					
					
					$seq=mysqli_fetch_array(mysqli_query($link,"select sequence from Testparameter where TestId='$tst' and ParamaterId='$ids[0]'"));
					mysqli_query($link,"insert into testresults(patient_id,visit_no,testid,paramid,sequence,result,time,date,tech,main_tech,for_doc) values('$pid','$visit','$tst','$ids[0]','$seq[sequence]','$ids[1]','$time','$date','$user','$user','0')");	
					//echo "insert into testresults(patient_id,visit_no,testid,paramid,sequence,result,time,date,tech,main_tech,for_doc) values('$pid','$visit','$tst','$ids[0]','$seq[sequence]','$ids[1]','$time','$date','$user','$user','0')"
				}
			}
			else
			{
				mysqli_query($link,"delete from testresults where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst' and paramid='$ids[0]'");
			}
		}
	}

}

else if($_POST[type]=="update_res_pad")
{
	$pin=$_POST[pin];
	$batch=$_POST[batch];
	$tst=$_POST[test];
	$result=mysqli_real_escape_string($link,$_POST[result]);
	$user=$_POST[user];
	
	
	mysqli_query($link,"update patient_test_summary set summary='$result',doc='$user' where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst'");
	
}

else if($_POST[type]=="doc_dept")
{
	$user=$_POST['user'];
	$name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$user'"));	
	echo "<h4>$name[name]</h4>";
	
	$chk_dis="Checked";
	$chk_apr="Checked";
	$chk_dep=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from lab_doc_dept where doc_id='$user'"));
	if($chk_dep[tot]>0)
	{
		$chk_dis="";
		$chk_apr="";
	}
	?>
	<table class="table table-bordered table-report table-condensed">
	<tr>
		<th>#</th><th width="70%">Dept.</th><th>Approve <input type="checkbox" onclick="check_all(this,1)"/></th><th>Display <input type="checkbox" onclick="check_all(this,2)"/></th>
	</tr>
	<?php
	$i=1;
	$dept=mysqli_query($link,"select distinct type_id,type_name from testmaster where category_id='1' and type_name!='' order by type_name");
	while($dep=mysqli_fetch_array($dept))
	{
		$dname=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$dep[type_id]'"));
		if($chk_dep[tot]>0)
		{
			$chk_apr="";
			$appr=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from lab_doc_dept where doc_id='$user' and approve='$dep[type_id]'"));
			if($appr[tot]>0)
			{
				$chk_apr="Checked";
			}
			
			$chk_dis="";
			$disr=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from lab_doc_dept where doc_id='$user' and display='$dep[type_id]'"));
			if($disr[tot]>0)
			{
				$chk_dis="Checked";
			}
		}
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $dname[name];?></td>
			<td style='text-align:center'><input type="checkbox" id="apr_<?php echo $dep[type_id];?>" value="<?php echo $dep[type_id];?>" class="app_dep" onclick="check_approve(<?php echo $dep[type_id];?>)" <?php echo $chk_apr;?> /> </td>
			<td style='text-align:center'><input type="checkbox" id="dis_<?php echo $dep[type_id];?>" value="<?php echo $dep[type_id];?>" class="dis_dep" onclick="check_display(<?php echo $dep[type_id];?>)" <?php echo $chk_dis;?> /> </td>
		</tr>
		<?php
		$i++;
	}
	?>
	</table>
	<div style="text-align:center">
		<div class='btn-group'>
			<button class="btn btn-info btn-mini" onclick="save_doc_dept()"><i class="icon-save"></i> Save</button>
			<button class="btn btn-danger btn-mini" onclick="$('#mod2').click()"><i class="icon-off"></i> Close</button>
		</div>
	</div>
	<br/>
	<?php
	
	
}
else if($_POST[type]=="doc_dept_save")
{
	$doc=$_POST['doc'];
	$aprv=$_POST['aprv'];
	$disp=$_POST['disp'];
	
	mysqli_query($link,"delete from lab_doc_dept where doc_id='$doc'");
	
	$apr=explode("@@",$aprv);
	foreach($apr as $ap)
	{
		if($ap)
		{
			mysqli_query($link,"insert into lab_doc_dept(doc_id,approve) values('$doc','$ap')");
		}
	}
	
	$ds=explode("@@",$disp);
	foreach($ds as $dis)
	{
		if($dis)
		{
			$chk_dis=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from lab_doc_dept where doc_id='$doc' and approve='$dis'"));
			if($chk_dis[tot]==0)
			{
				mysqli_query($link,"insert into lab_doc_dept(doc_id,display) values('$doc','$dis')");
			}
			else
			{
				mysqli_query($link,"update lab_doc_dept set display='$dis' where doc_id='$doc' and approve='$dis'");
			}
		}
	}
	
}


//------------Check For SMS-------//
	echo check_sms($pin,$batch);
//--------------------------------//
?>
