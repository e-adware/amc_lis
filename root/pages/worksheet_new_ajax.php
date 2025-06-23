<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$type=$_POST['type'];

if($type==1)
{
	$fdate=$_POST["fdate"];
	$tdate=$_POST["tdate"];
	$ftime=$_POST["ftime"];
	$ttime=$_POST["ttime"];
	
	$dept=$_POST["dept"];
	$pat_type=$_POST["pat_type"];
	
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd"];
	$ipd_id=$_POST["ipd"];
	$batch_no=$_POST["batch"];
	
?>
	<div align="right">
		<!--<button class="btn btn-info" onclick="window.print()" id="print_but"><i class="icon-print"></i></button>-->
		<button class="btn btn-info print_btn" onclick="worklist_print('','','','')" id="print_but"><i class="icon-print"></i></button>
	</div>
	<table class="table table-condensed table-no-top-border" id="work_tab" style="background-color:white;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Patient No</th>
				<th>Barcode</th>
				<th>Name</th>
				<th>Age/Sex</th>
				<th width="35%">Tests</th>
				<th>Date/Time</th>
			</tr>
		</thead>
	<?php
	$dep_str="";
	if($dept>0)
	{
		$dep_str=" and type_id='$dept'";
	}
	
	//---------OPD/IPD-----------//
	$pat_type_str="and opd_id!=''";
	if($pat_type==2)
	{
		$pat_type_str="and ipd_id!=''";
	}
	
	if($fdate==$tdate)
	{
		$i=1;
		$str="select distinct patient_id,opd_id,ipd_id,batch_no from phlebo_sample where date='$fdate' and TIME(time) between '$ftime' and '$ttime' and testid in(select testid from testmaster where category_id='1' $dep_str $pat_type_str)";
		
		if($uhid)
		{
			$str.=" AND `patient_id`='$uhid'";
		}
		if($opd_id)
		{
			$str.=" AND `opd_id`='$opd_id'";
		}
		if($ipd_id)
		{
			$str.=" AND `ipd_id`='$ipd_id'";
		}
		if($batch_no)
		{
			$str.=" AND `batch_no`='$batch_no'";
		}
		
		$str.="  order by slno";
		
		//echo $str;
		
		$qry=mysqli_query($link,$str);
		while($q=mysqli_fetch_array($qry))
		{
			$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
			
			$det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where patient_id='$q[patient_id]' and (opd_id='$q[opd_id]' or opd_id='$q[ipd_id]')"));
			
			$phl_tem=mysqli_fetch_array(mysqli_query($link,"select time,date from phlebo_sample where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]'"));
		?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $det[patient_id];?></td>
				<td><?php echo $det[opd_id];?></td>
				<td><?php echo $info[name];?></td>
				<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td>
				<td class="test_tab">
					<?php
					$dsr="order by a.type_id";
					if($dept>0)
					{
						$dsr=" and a.type_id='$dept'";
					}
					
					$ndep="";
					$test=mysqli_query($link,"select a.testid,a.testname,a.type_id from testmaster a,phlebo_sample b where b.patient_id='$q[patient_id]' and b.opd_id='$q[opd_id]' and b.ipd_id='$q[ipd_id]' and b.batch_no='$q[batch_no]' and a.testid=b.testid and a.category_id='1' $dsr");
					while($tst=mysqli_fetch_array($test))
					{
						$chk_res=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testresults where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and testid='$tst[testid]'"));
						$chk_sum=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_summary where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and testid='$tst[testid]'"));
						$chk_wid[tot]=0;
						if($tst[testid]==1227)
						{
							$chk_wid=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from widalresult where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' limit 1"));
						}
						
						$tst_span='';
						$tot_chk=$chk_res[tot]+$chk_sum[tot]+$chk_wid[tot];
						if($tot_chk>0){ $tst_span='tst_green';}
						
						$chk_print=mysqli_fetch_array(mysqli_query($link,"select count(slno) as tot from testreport_print where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid='$tst[testid]'"));
						if($chk_print["tot"]>0)
						{
							$tst_span='tst_gray';
						}
						
						if($ndep=="")
						{
							echo "<span class='$tst_span'>".$tst[testname]."</span>";
							$ndep=$tst[type_id];
						}
						else if($ndep==$tst[type_id])
						{
							echo "<b> | </b> <span class='$tst_span'>".$tst[testname]."</span>";
							$ndep=$tst[type_id];
						}
						else
						{
							echo "<br/><hr/>";
							echo "<span class='$tst_span'>".$tst[testname]."</span>";
							$ndep=$tst[type_id];
						}
					}
					?>
				</td>
				<td>
					<?php echo convert_date($phl_tem[date])." / ".convert_time($phl_tem[time]);?>
					
					<!--<button class="btn btn-info print_btn" onclick="worklist_print('<?php echo $q["patient_id"]; ?>','<?php echo $q["opd_id"]; ?>','<?php echo $q["ipd_id"]; ?>','<?php echo $q["batch_no"]; ?>')" id="print_but"><i class="icon-print"></i></button>-->
				</td>
			</tr>
			<?php
			$i++;
		}
	}
	else if($fdate!=$tdate)
	{
		$t_date=mysqli_query($link,"select distinct date from phlebo_sample where date between '$fdate' and '$tdate'  $pat_type_str order by slno");
		while($td=mysqli_fetch_array($t_date))
		{
			$i=1;
			if($td[date]==$fdate)
			{
				$str="select distinct patient_id,opd_id,ipd_id,batch_no from phlebo_sample where date='$fdate' and time>='$ftime' and testid in(select testid from testmaster where category_id='1' $dep_str  $pat_type_str)";
			}
			else if($td[date]==$tdate)
			{
				$str="select distinct patient_id,opd_id,ipd_id,batch_no from phlebo_sample where date='$tdate' and time<='$ttime' and testid in(select testid from testmaster where category_id='1' $dep_str $pat_type_str)";
			}
			else
			{
				$str="select distinct patient_id,opd_id,ipd_id,batch_no from phlebo_sample where date='$td[date]' and testid in(select testid from testmaster where category_id='1' $dep_str $pat_type_str)";
			}
			
			if($uhid)
			{
				$str.=" AND `patient_id`='$uhid'";
			}
			if($opd_id)
			{
				$str.=" AND `opd_id`='$opd_id'";
			}
			if($ipd_id)
			{
				$str.=" AND `ipd_id`='$ipd_id'";
			}
			if($batch_no)
			{
				$str.=" AND `batch_no`='$batch_no'";
			}
			
			$str.="  order by slno";
			
			//echo $str;
		?>
			<tr>
				<th colspan="8" class="td_date"><?php echo date("d-m-Y",strtotime($td["date"]));?></th>
			</tr>
			<?php	
			$qry=mysqli_query($link,$str);
			while($q=mysqli_fetch_array($qry))
			{
				$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
				$det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where patient_id='$q[patient_id]' and (opd_id='$q[opd_id]' or opd_id='$q[ipd_id]')"));
				
				$phl_tem=mysqli_fetch_array(mysqli_query($link,"select time,date from phlebo_sample where patient_id='$q[patient_id]' and (opd_id='$q[opd_id]' or opd_id='$q[ipd_id]')"));
				?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $det[patient_id];?></td>
					<td><?php echo $det[opd_id];?></td>
					<td><?php echo $info[name];?></td>
					<td><?php echo $info[age]." ".$info[age_type]." / ".$info[sex];?></td>
					<td class="test_tab">
						<?php
						$dsr="order by a.type_id";
						if($dept>0)
						{ $dsr=" and a.type_id='$dept'";	}
						
						$ndep="";
						$test=mysqli_query($link,"select a.testid,a.testname,a.type_id from testmaster a,phlebo_sample b where b.patient_id='$q[patient_id]' and (b.opd_id='$det[opd_id]' or b.ipd_id='$det[opd_id]') and b.batch_no='$q[batch_no]' and a.testid=b.testid and a.category_id='1' $dsr");
						while($tst=mysqli_fetch_array($test))
						{
							if($ndep=="")
							{
								echo $tst[testname];
								$ndep=$tst[type_id];
							}
							else if($ndep==$tst[type_id])
							{
								echo "<b> | </b>".$tst[testname];
								$ndep=$tst[type_id];
							}
							else
							{
								echo "<br/><hr/>";
								echo $tst[testname];
								$ndep=$tst[type_id];
							}
						}
						?>
					</td>
					<td>
						<?php echo convert_date($phl_tem[date])." / ".convert_time($phl_tem[time]);?>
						
						<!--<button class="btn btn-info print_btn" onclick="worklist_print('<?php echo $q["patient_id"]; ?>','<?php echo $q["opd_id"]; ?>','<?php echo $q["ipd_id"]; ?>','<?php echo $q["batch_no"]; ?>')" id="print_but"><i class="icon-print"></i></button>-->
					</td>
				</tr>
				<?php
				$i++;
			}
		}
	}
	?>
	</table>
	<?php
}
?>
