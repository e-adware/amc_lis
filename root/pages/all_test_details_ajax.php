<?php
include("../../includes/connection.php");

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-y', $timestamp);
		return $new_date;
	}
}

function check_status($pid,$opdid,$ipdid,$testt,$batch)
{
	$test_res=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from testresults where patient_id='$pid' and opd_id='$opdid' and ipd_id='$ipd_id' and testid='$testt' and batch_no='$batch'"));
	$test_sum=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_summary where patient_id='$pid' and opd_id='$opdid' and ipd_id='$ipd_id' and testid='$testt' and batch_no='$batch'"));
	$test_rad=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from testresuts_rad where patient_id='$pid' and opd_id='$opdid' and ipd_id='$ipd_id' and testid='$testt' and batch_no='$batch'"));
	$test_wid[tot]=0;
	
	if($test==1227)
	{
		$test_wid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from widalresult where patient_id='$pid' and opd_id='$opdid' and ipd_id='$ipd_id' and batch_no='$batch'"));
	}
	
	$tot=$test_res[tot]+$test_sum[tot]+$test_wid[tot]+$test_rad[tot];
	
	return $tot;
}

$type=$_POST[type];

if($type==1)
{
	$cat=$_POST[cat];
	if($cat>0)
	{
		$qry=mysqli_query($link,"select id,name from test_department where id in(select type_id from testmaster where category_id='$cat') order by name");
	}
	else
	{
		$qry=mysqli_query($link,"select * from test_department order by name");
	}
	ob_start();
	?>
	<select id="dep" class="span4" onchange="load_test()">
		<option value="0">-All-</option>
		<?php
		while($q=mysqli_fetch_array($qry))
		{
			echo "<option value='$q[id]'>$q[name]</option>";
		}
		?>
	</select>
	<?php
	$dept=ob_get_clean();
	echo $dept;
}
else if($type==2)
{
	$cat=$_POST[cat];
	$dep=$_POST[dep];
	
	if($dep==0)
	{
		if($cat==0)
		{
			$qry=mysqli_query($link,"select * from testmaster order by testname");
		}
		else
		{
			$qry=mysqli_query($link,"select * from testmaster where category_id='$cat' order by testname");
		}
	}
	else
	{
		$qry=mysqli_query($link,"select * from testmaster where type_id='$dep' order by testname");
	}
	
	ob_start();
	?>
	<select id="test" class="span4">
		<option value="0">-All-</option>
		<?php
		while($tst=mysqli_fetch_array($qry))
		{
			echo "<option value='$tst[testid]'>$tst[testname]</option>";
		}
		?>
	</select>
	<?php
	$test=ob_get_clean();
	echo $test;
}
else if($type==3)
{
	$from=$_POST[from];
	$to=$_POST[to];
	$cat=$_POST[cat];
	$dep=$_POST[dep];
	$test=$_POST[test];
	$status=$_POST[status];

	
	
	if($test>0)
	{
		?>
		
		<div id="print_header" align="center">
			<b>Test Status Report</b> <br/>
			<b>From <?php echo convert_date($from)." to ".convert_date($to);?></b>
		</div>
		<br/>
		<table class="table table-bordered table-condensed table-report">
			<tr><th>#</th><th>Patient ID</th><th>PIN</th><th>Name</th><th>Age / Sex</th><th>Test Price</th><th>Status</th></tr>
		<?php
		
		$dates=mysqli_query($link,"select distinct(date) from patient_test_details where testid='$test' and date between '$from' and '$to' order by slno");
		$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$test'"));
		echo "<tr><th colspan='7' class='head'>$tname[testname]</th></tr>";
		while($dt=mysqli_fetch_array($dates))
		{
			$test_list=mysqli_query($link,"select * from patient_test_details where testid='$test' and date='$dt[date]' order by slno");
			$tot_tst=mysqli_num_rows($test_list);
			
			$sum_tot=mysqli_fetch_array(mysqli_query($link,"select sum(test_rate) as tot from patient_test_details where testid='$test' and date='$dt[date]'"));
			if($status==0)
			{
				echo "<tr><th colspan='7' class='head'>".convert_date($dt[date])." || Total Number: $tot_tst ||  <i>Total Amount :$sum_tot[tot] /-</i></th></tr>";
				$i=1;
				while($tst=mysqli_fetch_array($test_list))
				{
					if($tst[opd_id]!='')
					{
						$pin="OPD ID: ".$tst[opd_id];
						$pin_typ='opd_id';
					}
					else
					{
						$pin="IPD ID: ".$tst[ipd_id];
						$pin_typ='ipd_id';
					}	
					
					$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$tst[patient_id]'"));
					
					$chk_status=check_status($tst[patient_id],$tst[opd_id],$tst[ipd_id],$test,$tst[batch_no]);
					?>
					<tr>
						<td><?php echo $i;?></td>
						<td><?php echo $tst[patient_id];?></td>
						<td><?php echo $pin;?></td>
						<td><?php echo $info[name];?></td>
						<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td>
						<td><?php echo $tst[test_rate];?></td>
						<td>
							<?php
								if($chk_status>0)
								{
									echo "<b><i>DONE</i></b>";
								}
								else
								{
									echo "<b><i>PENDING</i></b>";
								}
							?>
						</td>
					</tr>
					<?php
					$i++;
				}
			}
			else if($status==1)
			{
				$i=1;
				
				$res=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,testresults b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
				$summ=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,patient_test_summary b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and   a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
				$wid[tot]=0;
				if($test==1227)
				{
					$wid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,widalresult b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and a.date='$dt[date]'"));					
				}
				$rad=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,testresults_rad b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and   a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
				
				if($res[tot]>0 || $summ[tot]>0 || $wid[tot]>0 || $rad[tot]>0)
				{
					//echo "<tr><th colspan='7' class='head'>".convert_date($dt[date])." || Total Number: $tot_tst ||  <i>Total Amount :$sum_tot[tot] /-</i></th></tr>";
					echo "<tr><th colspan='6' class='head'>".convert_date($dt[date])." - $tot_tst</th></tr>";
				}		
				while($tst=mysqli_fetch_array($test_list))
				{
					$chk_status=check_status($tst[patient_id],$tst[opd_id],$tst[ipd_id],$test,$tst[batch_no]);
					
					if($chk_status>0)
					{
						
						if($tst[opd_id]!='')
						{
							$pin="OPD ID: ".$tst[opd_id];
							$pin_typ='opd_id';
						}
						else
						{
							$pin="IPD ID: ".$tst[ipd_id];
							$pin_typ='ipd_id';
						}	
						
						$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$tst[patient_id]'"));
						
						
						?>
						<tr>
							<td><?php echo $i;?></td>
							<td><?php echo $tst[patient_id];?></td>
							<td><?php echo $pin;?></td>
							<td><?php echo $info[name];?></td>
							<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td>
							<td><?php echo $tst[test_rate];?></td>
							<td><b><i>DONE</i></b></td>
						</tr>
						<?php
						$i++;
					}
				}
			}
			else if($status==2)
			{
				$i=1;
				
				$res=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,testresults b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
				$summ=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,patient_test_summary b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and   a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
				$wid[tot]=0;
				if($test==1227)
				{
					$wid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,widalresult b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and a.date='$dt[date]'"));					
				}
				$rad=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,testresults_rad b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and   a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
				
				if($res[tot]==0 && $summ[tot]==0 && $wid[tot]==0 && $rad[tot]==0)
				{
					echo "<tr><th colspan='6' class='head'>".convert_date($dt[date])." - $tot_tst</th></tr>";
				}
				
				while($tst=mysqli_fetch_array($test_list))
				{
					$chk_status=check_status($tst[patient_id],$tst[opd_id],$tst[ipd_id],$test,$tst[batch_no]);
					
					if($chk_status==0)
					{
						if($tst[opd_id]!='')
						{
							$pin="OPD ID: ".$tst[opd_id];
							$pin_typ='opd_id';
						}
						else
						{
							$pin="IPD ID: ".$tst[ipd_id];
							$pin_typ='ipd_id';
						}	
						
						$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$tst[patient_id]'"));
						
						
						?>
						<tr>
							<td><?php echo $i;?></td>
							<td><?php echo $tst[patient_id];?></td>
							<td><?php echo $pin;?></td>
							<td><?php echo $info[name];?></td>
							<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td>
							<td><?php echo $tst[test_rate];?></td>
							<td><b><i>PENDING</i></b></td>
						</tr>
						<?php
						$i++;
					}
				}
			}
		}
	}
	else if($dep>0 && $test==0)
	{
		?>
		<div id="print_header" align="center">
			<b>Test Status Report</b> <br/>
			<b>From <?php echo convert_date($from)." to ".convert_date($to);?></b>
		</div>
		<br/>
		
		
		<table class="table table-bordered table-condensed table-report">
		<?php
		if($status==0)
		{
			$dept_tot=mysqli_fetch_array(mysqli_query($link,"select sum(test_rate) as tot from patient_test_details where date between '$from' and '$to' and testid in(select testid from testmaster where type_id='$dep')"));
			$dept_tot_pat=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_details where date between '$from' and '$to' and testid in(select testid from testmaster where type_id='$dep')"));
			$dep_name=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$dep'"));
			
			echo "<tr><th colspan='7' class='head'>Department: $dep_name[name] || Total Amount: $dept_tot[tot] /-  || Total Patient: $dept_tot_pat[tot]</th></tr>";
			
			//echo "<span class='btn bnt-info'>$dep_name[name] : $dept_tot[tot]</span>";
		}	
		?>
		
			<tr><th>#</th><th>Patient ID</th><th>PIN</th><th>Name</th><th>Age / Sex</th><th>Test Rate</th><th>Status</th></tr>
		<?php
		
		//$dep_test=mysqli_query($link,"select distinct testid from patient_test_details where testid in(select testid from testmaster where type_id='$dep' order by testname) and date between '$from' and '$to' order by slno");
		$dep_test=mysqli_query($link,"select testid from testmaster where type_id='$dep' and testid in(select testid from patient_test_details where date between '$from' and '$to') order by testname");
		while($dp_tst=mysqli_fetch_array($dep_test))
		{
			
			$test=$dp_tst[testid];
			$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$test'"));
			echo "<tr class='head'><th colspan='7'>$tname[testname]</th></tr>";
			$dates=mysqli_query($link,"select distinct date from patient_test_details where testid='$test' and date between '$from' and '$to' order by slno");
			while($dt=mysqli_fetch_array($dates))
			{
				$test_list=mysqli_query($link,"select * from patient_test_details where testid='$test' and date='$dt[date]' order by slno");
				$tot_tst=mysqli_num_rows($test_list);
				if($tot_tst>0)
				{
					if($status==0)
					{
						$sum_tot=mysqli_fetch_array(mysqli_query($link,"select sum(test_rate) as tot from patient_test_details where testid='$test' and date='$dt[date]'"));
                        echo "<tr><th colspan='7' class='head'>".convert_date($dt[date])." || Total Number: $tot_tst ||  <i>Total Amount :$sum_tot[tot] /-</i></th></tr>";
						//echo "<tr><th colspan='6' class='head'>".convert_date($dt[date])." - $tot_tst</th></tr>";
						$i=1;
						while($tst=mysqli_fetch_array($test_list))
						{
							if($tst[opd_id]!='')
							{
								$pin="OPD ID: ".$tst[opd_id];
								$pin_typ='opd_id';
							}
							else
							{
								$pin="IPD ID: ".$tst[ipd_id];
								$pin_typ='ipd_id';
							}	
							
							$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$tst[patient_id]'"));
							
							$chk_status=check_status($tst[patient_id],$tst[opd_id],$tst[ipd_id],$test,$tst[batch_no]);
							?>
							<tr>
								<td><?php echo $i;?></td>
								<td><?php echo $tst[patient_id];?></td>
								<td><?php echo $pin;?></td>
								<td><?php echo $info[name];?></td>
								<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td>
								<td><?php echo $tst[test_rate];?></td>
								<td>
									<?php
										if($chk_status>0)
										{
											echo "<b><i>DONE</i></b>";
										}
										else
										{
											echo "<b><i>PENDING</i></b>";
										}
									?>
								</td>
							</tr>
							<?php
							$i++;
						}
					}
					else if($status==1)
					{
						$i=1;
						
						
						$res=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,testresults b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and   a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
						$summ=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,patient_test_summary b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and   a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
						$wid[tot]=0;
						if($test==1227)
						{
							$wid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,widalresult b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and a.date='$dt[date]'"));					
						}
						$rad=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,testresults_rad b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and   a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
						
						if($res[tot]>0 || $summ[tot]>0 || $wid[tot]>0 || $rad[tot]>0)
						{
							echo "<tr><th colspan='6' class='head'>".convert_date($dt[date])." - $tot_tst</th></tr>";
						}		
						while($tst=mysqli_fetch_array($test_list))
						{
							$chk_status=check_status($tst[patient_id],$tst[opd_id],$tst[ipd_id],$test,$tst[batch_no]);
							
							if($chk_status>0)
							{
								
								if($tst[opd_id]!='')
								{
									$pin="OPD ID: ".$tst[opd_id];
									$pin_typ='opd_id';
								}
								else
								{
									$pin="IPD ID: ".$tst[ipd_id];
									$pin_typ='ipd_id';
								}	
								
								$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$tst[patient_id]'"));
								
								
								?>
								<tr>
									<td><?php echo $i;?></td>
									<td><?php echo $tst[patient_id];?></td>
									<td><?php echo $pin;?></td>
									<td><?php echo $info[name];?></td>
									<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td>
									<td><b><i>DONE</i></b></td>
								</tr>
								<?php
								$i++;
							}
						}
					}
					else if($status==2)
					{
						$i=1;
						
						$res=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,testresults b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and   a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
						$summ=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,patient_test_summary b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and   a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
						$wid[tot]=0;
						if($test==1227)
						{
							$wid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,widalresult b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and a.date='$dt[date]'"));					
						}
						$rad=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select count(*) as tot from patient_test_details a,testresults_rad b where a.patient_id=b.patient_id and a.ipd_id=b.ipd_id and a.opd_id=b.opd_id and a.batch_no=b.batch_no and   a.testid='$test' and b.testid='$test' and a.date='$dt[date]'"));
						
						if($res[tot]==0 && $summ[tot]==0 && $wid[tot]==0 && $rad[tot]==0)
						{
							echo "<tr><th colspan='6' class='head'>".convert_date($dt[date])." - $tot_tst</th></tr>";
						}
						
						while($tst=mysqli_fetch_array($test_list))
						{
							$chk_status=check_status($tst[patient_id],$tst[opd_id],$tst[ipd_id],$test,$tst[batch_no]);
							
							if($chk_status==0)
							{
								if($tst[opd_id]!='')
								{
									$pin="OPD ID: ".$tst[opd_id];
									$pin_typ='opd_id';
								}
								else
								{
									$pin="IPD ID: ".$tst[ipd_id];
									$pin_typ='ipd_id';
								}	
								
								$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$tst[patient_id]'"));
								
								
								?>
								<tr>
									<td><?php echo $i;?></td>
									<td><?php echo $tst[patient_id];?></td>
									<td><?php echo $pin;?></td>
									<td><?php echo $info[name];?></td>
									<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td>
									<td><b><i>PENDING</i></b></td>
								</tr>
								<?php
								$i++;
							}
						}
					}
				}
			}
		}		
	}
	else if($cat>0 && $dep==0 && $test==0)
	{
		$qry="select distinct category_id from testmaster order by category_id";
		$dis_typ="cat";
	}
	
	?> </table> <?php
	
	
	
}
?>
