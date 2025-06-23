	

	
	
<?php
include("../../includes/connection.php");


function convert_date($date)
{
	 if($date)
	 {
		 $timestamp = strtotime($date); 
		 $new_date = date('d-M-Y', $timestamp);
		 return $new_date;
	 }
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}


if($_POST[type]==1)
{
	
	$fdate=$_POST[fdate];
	$tdate=$_POST[tdate];
	$aprv=$_POST[aprv];
	$user=$_POST[user];



	function convert_date($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}

	$qry="select distinct patient_id,visit_no from testresults where doc='$user' and main_tech>0 and date between '$fdate' and '$tdate' order by patient_id";
	
	?>
	<h5>Approved</h5>
	<table class="table table-bordered table-condensed">
	<th>S.No</th> <th>Patient ID</th> <th>Visit No</th> <th>LAB ID</th> <th>PATIENT NAME</th> <th>Age/Sex</th><th></th>
	
	<?php
	
	$i=1;
	$t_s=mysqli_query($GLOBALS["___mysqli_ston"],$qry);
	$t_s_num=mysqli_num_rows($t_s);
	if($t_s_num>0)
	{
		
		while($t_s_data=mysqli_fetch_array($t_s))
		{
			$info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_info where patient_id='$t_s_data[patient_id]'"));
			$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_reg_details where patient_id='$t_s_data[patient_id]' and visit_no='$t_s_data[visit_no]'"));
			?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $id_pref[pid_prefix]." ".$t_s_data[patient_id];?></td>
				<td><?php echo $reg[reg_no];?> <input type="hidden" id="reg_<?php echo $i;?>" value="<?php echo $reg[reg_no];?>"/></td>
				<td><?php echo $info[name];?></td>
				<td><?php echo $info[age]." ".$info[age_type]."/".$info[sex];?></td>
				<td><input type="button" class="btn btn-info" value="Approve" onclick="approve_win_doc(<?php echo $i;?>)" id="app<?php echo $i;?>"/></td>
			</tr>
			<?php		
			$i++;	
		}
		
	}
	
	if($t_s_num<1)
	{
		$qry1="select * from patient_test_summary where doc='$user' and main_tech>0 and date between '$fdate' and '$tdate' and patient_id not in(select patient_id from testresults order by patient_id)";
	}
	$t_sum=mysqli_query($GLOBALS["___mysqli_ston"], $qry1);
	$t_sum_num=mysqli_num_rows($t_sum);
	
	if($t_sum_num>0)
	{
		while($t_sum_data=mysqli_fetch_array($t_sum))
		{
			$info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_info where patient_id='$t_sum_data[patient_id]'"));
			$reg2=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_reg_details where patient_id='$t_sum_data[patient_id]' and visit_no='$t_sum_data[visit_no]'"));
									
			?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $reg2[reg_no];?> <input type="hidden" id="reg_<?php echo $i;?>" value="<?php echo $reg2[reg_no];?>"/></td>
				<td><?php echo $info[name];?></td>
				<td><?php echo $info[age]." ".$info[age_type]."/".$info[sex];?></td>
				<td><input type="button" class="btn btn-info" value="Approve" onclick="approve_win_doc(<?php echo $i;?>)"/></td>
			</tr>
			<?php		
			$i++;
		}
	}
	
	$date=date("Y-m-d");
	
		$qry1="select * from phlebo_sample where testid='1227' and date='$date' and patient_id in(select patient_id from widalresult where (for_doc='$user' or for_doc='1000') and doc=0)";
			
		//echo "select * from phlebo_sample where testid='1227' and date='$date'";
		$qry2=mysqli_query($GLOBALS["___mysqli_ston"],$qry1);
		while($q2=mysqli_fetch_array($qry2))
		{
			$tot=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from phlebo_sample where patient_id='$q2[patient_id]' and visit_no='$q2[visit_no]'"));
			if($tot==1)
			{
				$info3=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_info where patient_id='$q2[patient_id]'"));
				$reg3=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_reg_details where patient_id='$q2[patient_id]' and visit_no='$q2[visit_no]'"));	
				?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $reg3[reg_no];?> <input type="hidden" id="reg_<?php echo $i;?>" value="<?php echo $reg3[reg_no];?>"/></td>
				<td><?php echo $info3[name];?></td>
				<td><?php echo $info3[age]." ".$info3[age_type]."/".$info3[sex];?></td>
				<td><input type="button" class="btn btn-info" value="Approve" onclick="approve_win_doc(<?php echo $i;?>)"/></td>
			</tr>
			<?php		
			$i++;
			}
		
	}
	?>
	</table>
	<?php	
}
else if($_POST[type]==2)
{
	$fdate=$_POST[fdate];
	$tdate=$_POST[tdate];
	$dep=$_POST['dep'];
	$pat_type=$_POST['pat_type'];
	
	
	$uhid=trim($_POST['uhid']);
	$opd=trim($_POST['opd']);
	
	
	
	if($uhid!='')
	{
		$qry="select distinct patient_id,opd_id,ipd_id,batch_no from patient_test_details where patient_id='$uhid'";
	}
	if($opd!='')
	{
		$qry="select distinct patient_id,opd_id,ipd_id,batch_no from patient_test_details where opd_id='$opd'";
	}
	
	if($uhid=='' && $opd=='')
	{
		if($dep==0)
		{
			$qry="select distinct a.patient_id,a.opd_id,a.ipd_id,a.batch_no from patient_test_details a,testmaster c where a.testid=c.testid and c.category_id='1' and a.date between '$fdate' and '$tdate' order by a.slno";
			if($pat_type!='0')
			{
				$qry="select distinct a.patient_id,a.opd_id,a.ipd_id,a.batch_no from patient_test_details a,testmaster c where a.testid=c.testid and c.category_id='1' and a.date between '$fdate' and '$tdate' and $pat_type!='' order by a.slno";
			}
		}
		else
		{
			$qry="select distinct a.patient_id,a.opd_id,a.ipd_id,a.batch_no from patient_test_details a,testmaster c where a.testid=c.testid and c.category_id='1' and c.type_id='$dep' and a.date between '$fdate' and '$tdate' order by a.slno";
			if($pat_type!='0')
			{
				$qry="select distinct a.patient_id,a.opd_id,a.ipd_id,a.batch_no from patient_test_details a,testmaster c where a.testid=c.testid and c.category_id='1' and c.type_id='$dep' and a.date between '$fdate' and '$tdate' and $pat_type!='' order by a.slno";
			}
		}
	}
	
	$chk_doc="";
	
	$qr=mysqli_query($link,$qry);
	
	
	?>
	<table class="table table-bordered table-condensed table-report" id="pat_table">
	<th>#</th><th>UHID</th> <th>Bill No.</th> <th>PATIENT NAME</th> <th>Age/Sex</th> <th>Reg Date/Time</th><th>Department(s)</th><th></th>	
	<?php
	$i=1;
	while($q=mysqli_fetch_array($qr))
	{
		unset($n_dep);
		
		$tot_test=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_details where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where category_id='1' and type_id!='132')"));
		
		$tot_res=mysqli_num_rows(mysqli_query($link,"select distinct(testid) from testresults where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and doc>0"));
		
		$tot_sum=mysqli_num_rows(mysqli_query($link,"select distinct(testid) from patient_test_summary where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and doc>0 and testid not in(select testid from testresults where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]')"));
		
		$tot_histo=mysqli_num_rows(mysqli_query($link,"select * from patient_histo_summary where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and doc>0"));
		
		$tot_wid=0;
		$tot_wid=mysqli_num_rows(mysqli_query($link,"select * from widalresult where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and doc>0 limit 1"));
		
		$tot=$tot_res+$tot_sum+$tot_histo+$tot_wid;
		
		//$vl=$tot."--".$tot_test[tot];
		$vl="Approve";
		
		if($tot>$tot_test[tot])
		{
			$cls="btn btn-success";
			$tot_test_chk=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_details where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]'  and testid in(select testid from testmaster where category_id='1' and type_id!='132')"));
			if($tot_test_chk[tot]==$tot)
			{
				$cls="btn btn-success";
			}
		}
		else if($tot_test[tot]==$tot)
		{
			$cls="btn btn-success";
			if($tot_test2[tot]>0)
			{
				$chk_tot=$tot_test[tot]+$tot_test2[tot];
				if($chk_tot==$tot)
				{
					$cls="btn btn-success";
				}
				else
				{
					//$cls="btn btn-primary";
				}
			}
		}
		else if($tot_test[tot]>$tot)
		{
			if($tot==0)
			{
				$cls="btn btn-danger";
			}
			else
			{
				$cls="btn btn-warning";
			}
		}
		else
		{
			$cls="btn btn-danger";
		}
		
		
		if($cls=="btn btn-success")
		{
			$chk_print=mysqli_num_rows(mysqli_query($link,"select distinct(testid) from testreport_print where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' "));
			if($tot_test[tot]==$chk_print)
			{
				//$cls="btn btn-dark";
			}
		}
		
		$pat=mysqli_fetch_array(mysqli_query($link,"select distinct patient_id from patient_test_details where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' "));
		
		$pin=$q[opd_id];
		if($q[ipd_id]!='')
		{
			$pin=$q[ipd_id];
		}
		
		
		$reg_d=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where patient_id='$pat[patient_id]' and opd_id='$pin'"));
		
		$test_res=mysqli_fetch_array(mysqli_query($link,"select count(doc) as tot from testresults where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and main_tech>0  $chk_doc"));
		if($test_res[tot]>0)
		{
			$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pat[patient_id]'"));
			?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $q[patient_id];?></td>
				<td>
					<?php echo $pin;?>
					<input type="hidden" id="opd_<?php echo $i;?>" value="<?php echo $pin;?>"/>
					<input type="hidden" id="batch_<?php echo $i;?>" value="<?php echo $q[batch_no];?>"/>
				</td>
				<td><?php echo $info[name];?></td>
				<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex]; ?></td>
				<td><?php echo convert_date($reg_d[date])." / ".convert_time($reg_d[time]);?></td>
				<td width="30%"><b>
					<?php
						$dep_chk=mysqli_query($link,"select type_id from testmaster where category_id='1' and type_id!='132' and testid in(select testid from patient_test_details where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' )");
						while($dep_c=mysqli_fetch_array($dep_chk)){ $n_dep[]=$dep_c[type_id]; }
						
						$ndep=array_unique($n_dep);
						foreach($ndep as $nd)
						{
							$t_name=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$nd'"));
							echo $t_name[name]." , ";
						}
						
						?>
				</b></td>
				<td><button class="<?php echo $cls;?>" name="bt_<?php echo $q[patient_id];?>" onclick="approve_win_doc(<?php echo $i;?>)" id="app<?php echo $i;?>"><i class="icon-tasks"></i> <?php echo $vl;?></button> </td>
			</tr>
			<?php		
			$i++;	
		}
		
		if($test_res[tot]==0)
		{
			$test_sum=mysqli_fetch_array(mysqli_query($link,"select count(doc) as tot from patient_test_summary where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and main_tech>0  $chk_doc and testid not in(select testid from testresults where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' )"));
			if($test_sum[tot]>0)
			{
				$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pat[patient_id]'"));
			?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $q[patient_id];?></td>
					<td>
						<?php echo $q[opd_id];?>
						<input type="hidden" id="opd_<?php echo $i;?>" value="<?php echo $pin;?>"/>
						<input type="hidden" id="batch_<?php echo $i;?>" value="<?php echo $q[batch_no];?>"/>
					</td>
					<td><?php echo $info[name];?></td>
					<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex]; ?></td>
					<td><?php echo convert_date($reg_d[date])." / ".convert_time($reg_d[time]);?></td>
					<td width="30%"><b>
					<?php
						$dep_chk=mysqli_query($link,"select type_id from testmaster where category_id='1' and type_id!='132' and testid in(select testid from patient_test_details where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' )");
						while($dep_c=mysqli_fetch_array($dep_chk)){ $n_dep[]=$dep_c[type_id]; }
						
						$ndep=array_unique($n_dep);
						foreach($ndep as $nd)
						{
							$t_name=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$nd'"));
							echo $t_name[name]." , ";
						}
						
						?>
					</b></td>
					<td><button name="bt_<?php echo $q[patient_id];?>" class="<?php echo $cls;?>" onclick="approve_win_doc(<?php echo $i;?>)" id="app<?php echo $i;?>"><i class="icon-tasks"></i> <?php echo $vl;?></button> </td>
				</tr>
				<?php		
				$i++;	
			}
		}
		if($test_res[tot]==0 && $test_sum[tot]==0)
		{
			$chk_wid=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from widalresult where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and main_tech>0"));
			if($chk_wid[tot]>0)
			{
				$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pat[patient_id]'"));
			?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $q[patient_id];?></td>
					<td>
						<?php echo $q[opd_id];?>
						<input type="hidden" id="opd_<?php echo $i;?>" value="<?php echo $pin;?>"/>
						<input type="hidden" id="batch_<?php echo $i;?>" value="<?php echo $q[batch_no];?>"/>
					</td>
					<td><?php echo $info[name];?></td>
					<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex]; ?></td>
					<td><?php echo convert_date($reg_d[date])." / ".convert_time($reg_d[time]);?></td>
					<td width="30%"><b>
					<?php
						$dep_chk=mysqli_query($link,"select type_id from testmaster where category_id='1' and type_id!='132' and testid in(select testid from patient_test_details where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' )");
						while($dep_c=mysqli_fetch_array($dep_chk)){ $n_dep[]=$dep_c[type_id]; }
						
						$ndep=array_unique($n_dep);
						foreach($ndep as $nd)
						{
							$t_name=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$nd'"));
							echo $t_name[name]." , ";
						}
						
						?>
					</b></td>
					<td><button name="bt_<?php echo $q[patient_id];?>" class="<?php echo $cls;?>" onclick="approve_win_doc(<?php echo $i;?>)" id="app<?php echo $i;?>"><i class="icon-tasks"></i> <?php echo $vl;?></button> </td>
				</tr>
				<?php		
				$i++;
			}
		}
		/*
		if($test_res[tot]==0 && $test_sum[tot]==0)
		{
			$test_histo=mysqli_fetch_array(mysqli_query($link,"select count(doc) as tot from patient_histo_summary where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and main_tech>0  $chk_doc"));
			if($test_histo[tot]>0)
			{
				$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
			?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $q[patient_id];?></td>
					<td>
						<?php echo $q[opd_id];?>
						<input type="hidden" id="opd_<?php echo $i;?>" value="<?php echo $pin;?>"/>
						<input type="hidden" id="batch_<?php echo $i;?>" value="<?php echo $q[batch_no];?>"/>
					</td>
					<td><?php echo $info[name];?></td>
					<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex]; ?></td>
					<td><?php echo convert_date($reg_d[date])."/".convert_time($reg_d[time]);?></td>
					<td width="30%"><b>
					<?php
						$dep_chk=mysqli_query($link,"select type_id from testmaster where category_id='1' and type_id!='132' and testid in(select testid from patient_test_details where opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' )");
						while($dep_c=mysqli_fetch_array($dep_chk)){ $n_dep[]=$dep_c[type_id]; }
						
						$ndep=array_unique($n_dep);
						foreach($ndep as $nd)
						{
							$t_name=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$nd'"));
							echo $t_name[name]." , ";
						}
						
						?>
					</b></td>
					<td><button class="<?php echo $cls;?>" name="bt_<?php echo $q[patient_id];?>" onclick="approve_win_doc(<?php echo $i;?>)" id="app<?php echo $i;?>"><i class="icon-tasks"></i> <?php echo $vl;?></td>
				</tr>
				<?php		
				$i++;	
			}
		}
		*/
		
	}
	
}
?>
