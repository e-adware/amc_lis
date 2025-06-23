<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$type=$_POST['type'];

if($type=="load_data")
{
	$from=$_POST['from'];
	$to=$_POST['to'];
	$name=mysqli_real_escape_string($link,trim($_POST['name']));
	$id=mysqli_real_escape_string($link,trim($_POST['vid']));
	
	if($name!='')
	{
		$str="select distinct a.patient_id,a.opd_id,a.ipd_id,a.batch_no from patient_test_details a,patient_info b where a.patient_id=b.patient_id and a.testid in(select testid from testmaster where category_id='1') and b.name like '%$name%'";
	}
	else if($id!='')
	{
		$str="select distinct patient_id,opd_id,ipd_id,batch_no from patient_test_details where (opd_id='$id' or ipd_id='$id') and testid in(select testid from testmaster where category_id='1')";	
	}
	else
	{
		$str="select distinct patient_id,opd_id,ipd_id,batch_no from patient_test_details where date between '$from' and '$to' and testid in(select testid from testmaster where category_id='1')";	
	}
	
	?>
	<table class="table table-bordered table-report table-condensed" id="data_tab">
	<tr>
		<th>#</th> <th>Name</th> <th>Bill No.</th> <th>Test</th> <th>Entry Time</th><th>Receive Time</th><th>Process Time</th><th>Reporting Time</th><th>Print Time</th>
	</tr>
	<?php
	$i=1;
	$qry=mysqli_query($link,$str);
	while($q=mysqli_fetch_array($qry))
	{
		$pin=$q[opd_id];
		if($q[ipd_id]!='')
		{
			$pin=$q[ipd_id];
		}
		
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		$det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$pin'"));
		
		$tot_tst=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_details where patient_id='$q[patient_id]' and (opd_id='$pin' or ipd_id='$pin') and batch_no='$q[batch_no]' and testid in(select testid from testmaster where category_id='1')"));
		
		$rspan=$tot_tst[tot];
		
		$j=1;
		$test=mysqli_query($link,"select * from patient_test_details where patient_id='$q[patient_id]' and (opd_id='$pin' or ipd_id='$pin') and batch_no='$q[batch_no]'  and testid in(select testid from testmaster where category_id='1')");
		while($tst=mysqli_fetch_array($test))
		{
			if($j==1)
			{
			?>
			<tr>
				<td rowspan='<?php echo $rspan;?>'><?php echo $i;?></td>
				<td rowspan='<?php echo $rspan;?>' width="100px"><?php echo $info[name];?></td>
				<!--<td rowspan='<?php echo $rspan;?>'><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td> -->
				<td rowspan='<?php echo $rspan;?>'><?php echo $pin;?></td>
			<?php
			}
			if($j>1)
			{
				?> <tr> <?php
			}
			
			$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$tst[testid]'"));
			$entry_time=$tst[time];
			$entry_date=$tst[date];
			
			$phlebo=mysqli_fetch_array(mysqli_query($link,"select time,date from phlebo_sample where (opd_id='$pin' or ipd_id='$pin') and testid='$tst[testid]' and batch_no='$tst[batch_no]'"));
			$phlebo_time=$phlebo[time];
			$phlebo_date=$phlebo[date];
			
			$process=mysqli_fetch_array(mysqli_query($link,"select a.time,a.date from lab_sample_details a,test_sample_result b where a.barcode_id=b.barcode_id and a.opdid='$pin' and b.testid='$tst[testid]' and b.batch_no='$tst[batch_no]'"));
			$process_time=$process[time];
			$process_date=$process[date];
			
			$result=mysqli_fetch_array(mysqli_query($link,"select time,date from testresults where (opd_id='$pin' or ipd_id='$pin') and testid='$tst[testid]' and batch_no='$tst[batch_no]'"));
			if($result[time]=='')
			{
				$result=mysqli_fetch_array(mysqli_query($link,"select time,date from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and testid='$tst[testid]' and batch_no='$tst[batch_no]'"));
			}
			$result_time=$result[time];
			$result_date=$result[date];
			
			$print=mysqli_fetch_array(mysqli_query($link,"select time,date from testreport_print where (opd_id='$pin' or ipd_id='$pin') and testid='$tst[testid]' and batch_no='$tst[batch_no]'"));
			$print_time=$print[time];
			$print_date=$print[date];
			?>
				<td><?php echo $tname[testname];?></td>
				<td><?php if($entry_time){ echo convert_time($entry_time)."/".convert_date($entry_date);}?></td>	
				<td><?php if($phlebo_time){ echo convert_time($phlebo_time)."/".convert_date($phlebo_date);}?></td>	
				<td><?php if($process_time){ echo convert_time($process_time)."/".convert_date($process_date);}?></td>	
				<td><?php if($result_time){ echo convert_time($result_time)."/".convert_date($result_date);}?></td>	
				<td><?php if($print_time){ echo convert_time($print_time)."/".convert_date($print_date);}?></td>	
			</tr>
			
			<?php
			$j++;
			$i++;
		}
		
	}
}
else if($type=="load_data_bar")
{
	$from=$_POST['from'];
	$to=$_POST['to'];
	$name=mysqli_real_escape_string($link,trim($_POST['name']));
	$id=mysqli_real_escape_string($link,trim($_POST['vid']));
	
	if($name!='')
	{
		$str="select distinct a.patient_id,a.opd_id,a.ipd_id,a.batch_no from patient_test_details a,patient_info b where a.patient_id=b.patient_id and a.testid in(select testid from testmaster where category_id='1') and b.name like '%$name%'";
	}
	else if($id!='')
	{
		$str="select distinct patient_id,opd_id,ipd_id,batch_no from patient_test_details where (opd_id='$id' or ipd_id='$id') and testid in(select testid from testmaster where category_id='1')";	
	}
	else
	{
		$str="select distinct patient_id,opd_id,ipd_id,batch_no from patient_test_details where date between '$from' and '$to' and testid in(select testid from testmaster where category_id='1')";	
	}
	
	?>
	<table class="table table-bordered table-report table-condensed" id="data_tab">
	<tr>
		<th>#</th> <th>Name</th> <th>Bill No.</th> <th>Sample</th> <th>Tests</th> 
		<th style="background-color:#bd362f !important">Entry Time</th>
		<th style="background-color:#2f96b4 !important">Receive Time</th>
		<th style="background-color:#04c !important">Process Time</th>
		<th style="background-color:#51a351 !important">Reporting Time</th>
		<th style="background-color:#f89406 !important">Print Time</th>
	</tr>
	<?php
	
	$i=1;
	$qry=mysqli_query($link,$str);
	while($q=mysqli_fetch_array($qry))
	{
		$pin=$q[opd_id];
		if($q[ipd_id]!='')
		{
			$pin=$q[ipd_id];
		}
		
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		$det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$pin'"));
		
		$tot_tst=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_details a,TestSample b where a.patient_id='$q[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$q[batch_no]' and a.testid=b.TestId"));
		
		$rspan=$tot_tst[tot];
		
		$j=1;
		
		
		$samp=mysqli_query($link,"select distinct a.SampleId,b.Name from TestSample a,Sample b,patient_test_details c where a.TestId=c.testid and a.SampleId=b.ID and c.patient_id='$q[patient_id]' and (c.opd_id='$pin' or c.ipd_id='$pin') and c.batch_no='$q[batch_no]' order by b.Name");
		while($smp=mysqli_fetch_array($samp))
		{
			$tot_tst=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_details a,TestSample b where a.testid=b.TestId and a.patient_id='$q[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$q[batch_no]' and b.SampleId='$smp[SampleId]'"));
			if($j==1)
			{
			?>
			<tr>
				<td rowspan='<?php echo $rspan;?>'><?php echo $i;?></td>
				<td rowspan='<?php echo $rspan;?>' width="100px"><?php echo $info[name];?></td>
				<td rowspan='<?php echo $rspan;?>'><?php echo $pin;?></td>
			<?php
			}
			
			$smp_row=1;
			$smp_span=$tot_tst[tot];
			$test=mysqli_query($link,"select a.* from patient_test_details a,TestSample b where a.testid=b.TestId and a.patient_id='$q[patient_id]' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$q[batch_no]' and b.SampleId='$smp[SampleId]' order by a.slno");
			while($tst=mysqli_fetch_array($test))
			{
				if($smp_row==1)
				{
					?> <td rowspan='<?php echo $smp_span;?>'><?php echo $smp[Name];?></td> <?php
					$chk_tst=$tst[testid];
				}
				$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$tst[testid]'"));
				
				?>
				<td><?php echo $tname[testname];?></td>
				<?php
				if($smp_row==1)
				{
					?> 
					<td rowspan='<?php echo $smp_span;?>' colspan="5">
					
						<div class="btn-group" style="align:center !important">
						<?php
						$entry_time=convert_time($tst[time]);
						$entry_date=convert_date($tst[date]);
						
						$n_date=$tst[date]." ".$tst[time];
						?> <div class="btn btn-danger btn-large" style="width:70px;height:5px;font-size:12px;cursor:default"><span style="margin-top:-10px;display:block"><?php echo $entry_time;?></span></div> <?php
						
						$phlebo=mysqli_fetch_array(mysqli_query($link,"select time,date from phlebo_sample where (opd_id='$pin' or ipd_id='$pin') and testid='$tst[testid]' and batch_no='$tst[batch_no]'"));
						if($phlebo[time])
						{
							$phlebo_time=convert_time($phlebo[time]);
							$phlebo_date=convert_date($phlebo[date]);
							?> <div class="btn btn-info btn-large" style="width:70px;height:5px;font-size:12px;;cursor:default"><span style="margin-top:-10px;display:block"><?php echo $phlebo_time;?></span></div> <?php
							
							$date1 = strtotime($n_date);  
							$date2 = strtotime($phlebo[date]." ".$phlebo[time]);
							
							$diff = abs($date2 - $date1); 
							
							$days = floor(($diff/ (60*60*24))); 
							$hours = floor(($diff  - $days*60*60*24)/ (60*60));  
							$minutes = floor(($diff - $hours*60*60)/ 60);
							
							$diff_1="@time_dif@";
							if($days>0)
							{
								$diff_1=$days." Days";	
							}
							if($hours>0)
							{
								$diff_1.=" ".$hours." Hrs";	
							}
							if($days==0 && $minutes>0)
							{
								$diff_1.=" ".$minutes." Min";	
							}
							
							if($days==0 && $hours==0)
							{
								$seconds = floor(($diff - $days*60*60*24 - $hours*60*60 - $minutes*60));
								if($seconds>0)
								{
									$diff_1.=" ".$seconds." Secs";	
								}
							}
							
							$n_date=$phlebo[date]." ".$phlebo[time];
						}
						
						
						//$process=mysqli_fetch_array(mysqli_query($link,"select a.time,a.date from lab_sample_details a,test_sample_result b where a.barcode_id=b.barcode_id and a.opdid='$pin' and b.testid='$tst[testid]' and b.batch_no='$tst[batch_no]'"));
						$process=mysqli_fetch_array(mysqli_query($link,"select time,date from lab_sample_receive where (opd_id='$pin' OR ipd_id='$pin') and testid='$tst[testid]' and batch_no='$tst[batch_no]'"));
						if($process[time])
						{
							$process_time=convert_time($process[time]);
							$process_date=convert_date($process[date]);
							
							?> <div class="btn btn-primary btn-large" style="width:70px;height:5px;font-size:12px;cursor:default"><span style="margin-top:-10px;display:block"><?php echo $process_time;?></span></div> <?php
							
							$date1 = strtotime($n_date);  
							$date2 = strtotime($process[date]." ".$process[time]);
							
							$diff = abs($date2 - $date1); 
							
							$days = floor(($diff/ (60*60*24))); 
							$hours = floor(($diff  - $days*60*60*24)/ (60*60));  
							$minutes = floor(($diff - $hours*60*60)/ 60);
							
							$diff_1.="@time_dif@";
							if($days>0)
							{
								$diff_1.=$days." Days";	
							}
							if($hours>0)
							{
								$diff_1.=" ".$hours." Hrs";	
							}
							if($days==0 && $minutes>0)
							{
								$diff_1.=" ".$minutes." Min";	
							}
							
							if($days==0 && $hours==0)
							{
								$seconds = floor(($diff - $days*60*60*24 - $hours*60*60 - $minutes*60));
								if($seconds>0)
								{
									$diff_1.=$seconds." Secs";	
								}
							}
							
							$n_date=$process[date]." ".$process[time];
						}
						
						$result=mysqli_fetch_array(mysqli_query($link,"select time,date from testresults where (opd_id='$pin' or ipd_id='$pin') and testid='$tst[testid]' and batch_no='$tst[batch_no]'"));
						if($result[time]=='')
						{
							$result=mysqli_fetch_array(mysqli_query($link,"select time,date from patient_test_summary where (opd_id='$pin' or ipd_id='$pin') and testid='$tst[testid]' and batch_no='$tst[batch_no]'"));
						}
						if($result[time])
						{
							$result_time=convert_time($result[time]);
							$result_date=convert_date($result[date]);
							?> <div class="btn btn-success btn-large" style="width:70px;height:5px;font-size:12px;cursor:default"><span style="margin-top:-10px;display:block"><?php echo $result_time;?></span></div> <?php
							
							$date1 = strtotime($n_date);  
							$date2 = strtotime($result[date]." ".$result[time]);
							
							$diff = abs($date2 - $date1); 
							
							$days = floor(($diff/ (60*60*24))); 
							$hours = floor(($diff  - $days*60*60*24)/ (60*60));  
							$minutes = floor(($diff - $hours*60*60)/ 60);
							
							$diff_1.="@time_dif@";
							if($days>0)
							{
								$diff_1.=$days." Days";	
							}
							if($hours>0)
							{
								$diff_1.=" ".$hours." Hrs";	
							}
							if($days==0 && $minutes>0)
							{
								$diff_1.=" ".$minutes." Min";	
							}
							
							if($days==0 && $hours==0)
							{
								$seconds = floor(($diff - $days*60*60*24 - $hours*60*60 - $minutes*60));
								if($seconds>0)
								{
									$diff_1.=" ".$seconds." Secs";	
								}
							}
							
							$n_date=$result[date]." ".$result[time]; 
						}
						
						$print=mysqli_fetch_array(mysqli_query($link,"select time,date from testreport_print where (opd_id='$pin' or ipd_id='$pin') and testid='$tst[testid]' and batch_no='$tst[batch_no]'"));
						if($print[time])
						{
							$print_time=convert_time($print[time]);
							$print_date=convert_date($print[date]);
							?> <div class="btn btn-warning btn-large" style="width:70px;height:5px;font-size:12px;cursor:default"><span style="margin-top:-10px;display:block"><?php echo $print_time;?></span></div> <?php
							
							$date1 = strtotime($n_date);  
							$date2 = strtotime($print[date]." ".$print[time]);
							
							$diff = abs($date2 - $date1); 
							
							$days = floor(($diff/ (60*60*24))); 
							$hours = floor(($diff  - $days*60*60*24)/ (60*60));  
							$minutes = floor(($diff - $hours*60*60)/ 60);
							
							$diff_1.="@time_dif@";
							if($days>0)
							{
								$diff_1.=$days." Days";	
							}
							if($hours>0)
							{
								$diff_1.=" ".$hours." Hrs";	
							}
							if($days==0 && $minutes>0)
							{
								$diff_1.=" ".$minutes." Min";	
							}
							
							if($days==0 && $hours==0)
							{
								$seconds = floor(($diff - $days*60*60*24 - $hours*60*60 - $minutes*60));
								if($seconds>0)
								{
									$diff_1.=" ".$seconds." Secs";	
								}
							}
							
							$n_date=$process[date]." ".$process[time];
							
						}
						$n_date="";
						?>	
						<br/>
						</div>
						<br/>
						
						<?php
						
						$dif=1;
						$diff_n=explode("@time_dif@",$diff_1);
						foreach($diff_n as $df)
						{
							if($df)
							{
								if($dif==1)
								{
									?><div style="display:inline-block;width:105px;text-align:center;margin-left:55px;font-weight:bold"><?php echo $df;?></div> <?php	
								}
								else
								{
									?><div style="display:inline-block;width:105px;text-align:center;font-weight:bold"><?php echo $df;?></div> <?php	
								}
								$dif++;
							}
						}
						$diff_1="";
						?>
						
					</td>
					<?php
				}	
				
				$smp_row++;
				echo "</tr>";
			}
			
			$j++;
		}
		$i++;
	}	
}
?>
