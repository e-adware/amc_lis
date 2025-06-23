<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

function get_dates($start, $end, $format = 'Y-m-d')
{
	$array = array();
	$interval = new DateInterval('P1D');

	$realEnd = new DateTime($end);
	$realEnd->add($interval);

	$period = new DatePeriod(new DateTime($start), $interval, $realEnd);

	foreach($period as $date) { 
		$array[] = $date->format($format); 
	}

	return $array;
}

$type=$_POST['type'];

if($type=="qc_gen")
{
	?>
	 <div class="widget-title">
       <ul class="nav nav-tabs" id="gen_bar_ul">
	
	  <li class="active"><a href="#" onclick="$('#qc_gen').click()">Add New</a></li>
		  <?php
		  $b_date=date('Y-m-d');
		  $bar=mysqli_query($link,"select distinct patient_id,opd_id,ipd_id,barcode_id from test_sample_result where equip_id='9999' and date='$b_date'");
		  while($bb=mysqli_fetch_array($bar))
		  {
			?> <li><a href="#" onclick="load_qc_det('<?php echo $bb[patient_id];?>','<?php echo $bb[opd_id];?>','<?php echo $bb[ipd_id];?>','<?php echo $bb[barcode_id];?>',this)"><?php echo $bb[barcode_id];?></a></li> <?php	
		  }
		  ?>
	</ul>
	</div>
	
	<div id="qc_detail">
		<table class="table table-bordered table-report table=condensed">
		<tr>
			<th colspan="5">GENERATE QC</th>
		</tr>
		<tr>
			<td>Date <br/>
				<input type="text" class="form-control datepicker" id="date" value="<?php echo date('Y-m-d');?>" readonly/>
			</td>
			
			<td>
				Select Instrument <br/>
				<select id="instrument" onchange="load_primary();">
					<option value="0">--Select--</option>
					<?php
					$ins=mysqli_query($link,"select * from lab_instrument_master order by name");
					while($in=mysqli_fetch_array($ins))
					{
						echo "<option value='$in[id]'>$in[name]</option>";
					}
					?>
				</select>
			</td>
			<td>
				Select Primary Lot No <br/>
				<span id="primary_sel">
					<select id="primary">
						<option value="0">--Select--</option>
					</select>
				</span>
			</td>
			<td>
				Select Secondary Lot No <br/>
				<span id="secondary_sel">
					<select id="secondary">
						<option value="0">--Select--</option>
					</select>
				</span>
			</td>
		</tr>
		<tr>
			<td colspan="4">
			<div id="test_data"></div>
			<div style="text-align:center;border-top:1px solid #CCC;display:none" id="gc">
				<button class="btn btn-info" onclick="generate_qc()">Generate QC</button>
			</div>
			</td>
		</tr>
		<!--
		<tr>
			<td colspan="4" style="text-align:center">
				<button class="btn btn-info">Generate QC</button>
			</td>
		</tr>
		-->
		</table>
	</div>
	<?php
}
else if($type=="load_primary")
{
	$instr=$_POST['instr'];
	?>
	<select id="primary" onchange="load_secondary();">
		<option value="0">--Select--</option>
		<?php
			$prim=mysqli_query($link,"select distinct primary_lot_no from qc_master where instr_id='$instr' order by slno desc");
			while($p=mysqli_fetch_array($prim))
			{
				echo "<option value='$p[primary_lot_no]'>$p[primary_lot_no]</option>";
			}
		?>
	</select>
	<?php
}
else if($type=="load_second")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	?>
	<select id="secondary" onchange="load_qc_text();show_test();show_report_test();">
		<option value="0">--Select--</option>
		<?php
			$sec=mysqli_query($link,"select distinct secondary_lot_no from qc_master where instr_id='$instr' and primary_lot_no='$primary' order by slno");
			while($s=mysqli_fetch_array($sec))
			{
				echo "<option value='$s[secondary_lot_no]'>$s[secondary_lot_no]</option>";
			}
		?>
	</select>
	<input type="text" id="qc_text" style="width:80px" readonly/>
	<?php
}
else if($type=="load_qc_text")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$second=$_POST['secondary'];
	
	$qc=mysqli_fetch_array(mysqli_query($link,"select qc_text from qc_master where instr_id='$instr' and primary_lot_no='$primary' and secondary_lot_no='$second'"));
	
	echo $qc[qc_text];
}
else if($type=="show_test")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$secondary=$_POST['secondary'];
	
	$i=1;
	
	$test=mysqli_query($link,"select a.testname,a.testid from testmaster a,qc_test_list b where a.testid=b.test_id and b.instr_id='$instr' and b.primary_lot_no='$primary' and b.secondary_lot_no='$secondary' order by a.testname");
	while($tst=mysqli_fetch_array($test))
	{
		?>
		<div class="qc_test" onclick="check_tst(<?php echo $i;?>)">
			<i class="icon-check" id="icon_check_<?php echo $i;?>" value="<?php echo $tst[testid];?>"></i> <?php echo $tst[testname];?>
		</div>
		<?php
		if($i%3==0)
		{
			echo "<br/>";
		}
			
		$i++;
	}
}
else if($type=="save_gc")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$secondary=$_POST['secondary'];
	
	$user=$_POST['user'];
	
	$test_list=$_POST['test_list'];
	
	$date=date('Y-m-d');
	$time=date("H:i:s");
	
	
	$qc_text=mysqli_fetch_array(mysqli_query($link,"select * from qc_master where instr_id='$instr' and primary_lot_no='$primary' and secondary_lot_no='$secondary'"));
	
	$qc_res=mysqli_query($link,"select * from test_sample_result where patient_id='$instr' and opd_id='$primary' and ipd_id='$secondary' and barcode_id='$qc_text[qc_text]' and date='$date'");
	if(mysqli_num_rows($qc_res)==0)
	{
		$test=explode("@qc_tst@",$test_list);
		foreach($test as $tst)
		{
			if($tst)
			{
			
			$par=mysqli_fetch_array(mysqli_query($link,"select * from Testparameter where TestId='$tst' and sequence='1'"));
			
			mysqli_query($link,"insert into test_sample_result(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `barcode_id`, `vaccus`, `sample_id`, `equip_id`, `testid`, `paramid`, `result`, `time`, `date`, `user`) values('$instr','$primary','$secondary','1','$qc_text[qc_text]','','','9999','$tst','$par[ParamaterId]','','$time','$date','$user')");
			}
		}
		echo $qc_text[qc_text]." is generated";
	}
	else
	{
		echo $qc_text[qc_text]." is already generated for today";		
	}
}
else if($type=="load_qc")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$secondary=$_POST['secondary'];
	$qc=$_POST['qc'];
	$date=date('Y-m-d');
	
	
	
	$qry=mysqli_query($link,"select a.*,b.testname from test_sample_result a,testmaster b where a.patient_id='$instr' and a.opd_id='$primary' and a.ipd_id='$secondary' and a.barcode_id='$qc' and a.date='$date' and a.testid=b.testid order by b.testname");
	
	?>
	<div align="center" style="background:white">
		<div class="qc_info">
			Type <br/>
			<b><?php echo $qc;?></b>
		</div>
		<div class="qc_info">
			Date <br/>
			<b><?php echo convert_date($date);?></b>
		</div>
		<div class="qc_info">
			Instrument <br/>
			<b>
				<?php 
				$instr_name=mysqli_fetch_array(mysqli_query($link,"select name from lab_instrument_master where id='$instr'"));	
				echo $instr_name[name];
				?>
			</b>
		</div>
		<div class="qc_info">
			Primary Lot No <br/>
			<b><?php echo $primary;?></b>
		</div>
		<div class="qc_info">
			Secondary Lot No <br/>
			<b><?php echo $secondary;?></b>
		</div>
	</div>
	<table class="table table-bordered table-condensed table-report">
	<tr>
		<th>#</th> <th>Test</th> <th>Result</th> <th>Normal Range</th><th></th>
	</tr>
	<?php
	$i=1;
	while($q=mysqli_fetch_array($qry))
	{
		
		$range=mysqli_fetch_array(mysqli_query($link,"select * from qc_normal where instr_id='$instr' and primary_lot='$primary' and secondary_lot='$secondary' and test_id='$q[testid]'"));
		
		$tr_cls="";
		$res_intr="";
		$nres=$q[result];
		if($q[result] && $range[value_from] && $range[value_to])
		{
			$chk_res=explode("<",$q[result]);
			if($chk_res[1])
			{
				$nres=$chk_res[1]-1;
			}
			
			$chk_res2=explode(">",$q[result]);
			if($chk_res2[1])
			{
				$nres=$chk_res2[1]+1;
			}
			if($nres<$range[value_from] || $nres>$range[value_to])
			{
				if($nres<$range[value_from])
				{
					$res_intr="LOW";
				}
				else if($nres>$range[value_to])
				{
					$res_intr="HIGH";
				}
				
				$tr_cls="out_of_range";
			}
		}
		
		?>
		<tr class="<?php echo $tr_cls;?>">
			<td><?php echo $i;?></td>
			<td><?php echo $q[testname];?></td>
			<td><?php echo $q[result];?></td>
			<td><?php echo $range[display_range];?></td>
			<td><?php echo $res_intr;?></td>
		</tr>
		<?php
		$i++;
	}
	?> </table> <?php
	
	
}
else if($type=="qc_report")
{
	?>
	<div id="qc_search">
		<table class="table table-report table-bordered table-condensed">
		<tr>
			<th colspan="3">QC Report</th>
		</tr>
		<tr>
			<td rowspan="2">
				<b>From</b> <br/>
				<input type="text" id="fdate" class="form-control datepicker" value="<?php echo date('Y-m-d');?>"/>
				<br/>
				<b>To</b> <br/>
				<input type="text" id="tdate" class="form-control datepicker" value="<?php echo date('Y-m-d');?>"/>
			</td>
			<td>
				<b>Select Instrument</b> <br/>
				<select id="instrument" onchange="load_primary_rep();">
					<option value="0">--Select--</option>
					<?php
					$ins=mysqli_query($link,"select * from lab_instrument_master order by name");
					while($in=mysqli_fetch_array($ins))
					{
						echo "<option value='$in[id]'>$in[name]</option>";
					}
					?>
				</select>
			</td>
			<td>
				<b>Select Primary</b> <br/>
				<span id="primary_sel">
					<select id="primary">
						<option value="0">--Select--</option>
					</select>
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<b>Select Secondary</b> <br/>
				<span id="secondary_sel">
					<select id="secondary">
						<option value="0">--Select--</option>
					</select>
				</span>
			</td>
			<td>
				<b>Select Test</b> <br/>
				<span id="test_list_span">
					<select id="test_list">
						<option value="0">--All--</option>
					</select>
				</span>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:center">
				<button id="" class="btn btn-info" onclick="load_report()">Load</button>
			</td>
		</tr>
		</table>
	</div>
	
	<div id="report_data">
	
	</div>
	<?php
}
else if($type=="load_test_sel")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$secondary=$_POST['secondary'];
	
	?>
	<select id="test_list">
	<option value="0">--All--</option>
	<?php
	$i=1;
	$test=mysqli_query($link,"select a.testname,a.testid from testmaster a,qc_test_list b where a.testid=b.test_id and b.instr_id='$instr' and b.primary_lot_no='$primary' and b.secondary_lot_no='$secondary' order by a.testname");
	while($tst=mysqli_fetch_array($test))
	{
		echo "<option value='$tst[testid]'>$tst[testname]</option>";
	}
	?>
	</select>
	<?php
}
else if($type=="load_primary_rep")
{
	$instr=$_POST['instr'];
	?>
	<select id="primary" onchange="load_secondary_rep();">
		<option value="0">--Select--</option>
		<?php
			$prim=mysqli_query($link,"select distinct primary_lot_no from qc_master where instr_id='$instr' order by slno desc");
			while($p=mysqli_fetch_array($prim))
			{
				echo "<option value='$p[primary_lot_no]'>$p[primary_lot_no]</option>";
			}
		?>
	</select>
	<?php
}
else if($type=="load_second_rep")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	?>
	<select id="secondary" onchange="load_qc_text();show_test();show_report_test();">
		<option value="0">--All--</option>
		<?php
			$sec=mysqli_query($link,"select distinct secondary_lot_no from qc_master where instr_id='$instr' and primary_lot_no='$primary' order by slno");
			while($s=mysqli_fetch_array($sec))
			{
				echo "<option value='$s[secondary_lot_no]'>$s[secondary_lot_no]</option>";
			}
		?>
	</select>
	<input type="text" id="qc_text" style="width:80px" readonly/>
	<?php
}
else if($type=="load_report")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$secondary=$_POST['secondary'];
	$test=$_POST['test'];
	
	
	if($secondary==0) //-----------------IF SECONDARY IS ALL------------------//
	{
				
		$sec_cols=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from qc_master where primary_lot_no='$primary'"));
		?>
		<table class="table table-report table-bordered table-condensed">
		<tr>
			<th rowspan="2">Test</th>
			<?php
			$dates = get_dates($fdate,$tdate);
			foreach($dates as $date)
			{
				echo "<th colspan='$sec_cols[tot]'>".convert_date($date)."</th>";
			}
			?>
		</tr>
		<tr>
			<?php
			foreach($dates as $date)
			{
				$sc=mysqli_query($link,"select * from qc_master where primary_lot_no='$primary' order by qc_text,slno");
				while($ss=mysqli_fetch_array($sc))
				{
					echo "<td>$ss[qc_text]</td>";
				}
			}
			?>
		</tr>
		<?php
		$tst=mysqli_query($link,"select a.testname,a.testid from testmaster a,qc_test_list b where a.testid=b.test_id and b.instr_id='$instr' and b.primary_lot_no='$primary' order by a.testname");	
		
		while($ts=mysqli_fetch_array($tst))
		{
			?>
			<tr>
				<td><?php echo $ts[testname];?></td>
				<?php
				foreach($dates as $date)
				{
					$sc=mysqli_query($link,"select * from qc_master where primary_lot_no='$primary' order by qc_text,slno");
					while($ss=mysqli_fetch_array($sc))
					{
						$res=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result where patient_id='$instr' and opd_id='$primary' and ipd_id='$ss[secondary_lot_no]' and testid='$ts[testid]' and date='$date'"));
						
						$range=mysqli_fetch_array(mysqli_query($link,"select * from qc_normal where instr_id='$instr' and primary_lot='$primary' and secondary_lot='$ss[secondary_lot_no]' and test_id='$ts[testid]'"));
		
						$td_cls="";
						$res_intr="";
						$nres=$res[result];
						if($res[result] && $range[value_from] && $range[value_to])
						{
							$chk_res=explode("<",$res[result]);
							if($chk_res[1])
							{
								$nres=$chk_res[1]-1;
							}
							
							$chk_res2=explode(">",$res[result]);
							if($chk_res2[1])
							{
								$nres=$chk_res2[1]+1;
							}
							if($nres<$range[value_from] || $nres>$range[value_to])
							{
								if($nres<$range[value_from])
								{
									$res_intr="(L)";
								}
								else if($nres>$range[value_to])
								{
									$res_intr="(H)";
								}
								
								$td_cls="out_of_range_td";
							}
						}
						
						
						echo "<td class='$td_cls'>$res[result] $res_intr</td>";
					}
				}
				?>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	else if($secondary>0) //--------------------IF SECONDARY IS SELECTED AND TEST IS ALL
	{
		?>
		<table class="table table-report table-bordered table-condensed">
		<?php
			$dates = get_dates($fdate,$tdate);
			
			if(sizeof($dates)==1)
			{
				$date=$dates[0];
				?>
				<tr>
					<th>#</th> <th>Test</th> <th>Result</th> <th>Normal Range</th><th></th>
				</tr>
				<?php
			}
			else
			{
				?>
				<tr>
				<th>Test</th>
				<?php
				foreach($dates as $date)
				{
					echo "<th>".convert_date($date)."</th>";
				}
				?> </tr> <?php
			}
			?>
		
		
		<?php
		$tst_str="";
		if($test>0)
		{
			$tst_str=" and a.testid='$test'";
		}
		
		if(sizeof($dates)==1)
		{
			$tst=mysqli_query($link,"select a.*,b.testname from test_sample_result a,testmaster b where a.patient_id='$instr' and a.opd_id='$primary' and a.ipd_id='$secondary' and a.date='$date' and a.testid=b.testid $tst_str order by b.testname");
			
			$i=1;
		}
		else
		{
			$tst=mysqli_query($link,"select a.testname,a.testid from testmaster a,qc_test_list b where a.testid=b.test_id and b.instr_id='$instr' and b.primary_lot_no='$primary' and b.secondary_lot_no='$secondary' $tst_str  order by a.testname");	
		}
		
		
		while($ts=mysqli_fetch_array($tst))
		{
			if(sizeof($dates)==1)
			{
				$range=mysqli_fetch_array(mysqli_query($link,"select * from qc_normal where instr_id='$instr' and primary_lot='$primary' and secondary_lot='$secondary' and test_id='$ts[testid]'"));
		
				$tr_cls="";
				$res_intr="";
				$nres=$ts[result];
				if($ts[result] && $range[value_from] && $range[value_to])
				{
					$chk_res=explode("<",$ts[result]);
					if($chk_res[1])
					{
						$nres=$chk_res[1]-1;
					}
					
					$chk_res2=explode(">",$ts[result]);
					if($chk_res2[1])
					{
						$nres=$chk_res2[1]+1;
					}
					if($nres<$range[value_from] || $nres>$range[value_to])
					{
						if($nres<$range[value_from])
						{
							$res_intr="LOW";
						}
						else if($nres>$range[value_to])
						{
							$res_intr="HIGH";
						}
						
						$tr_cls="out_of_range";
					}
				}
				
				?>
				<tr class="<?php echo $tr_cls;?>">
					<td><?php echo $i;?></td>
					<td><?php echo $ts[testname];?></td>
					<td><?php echo $ts[result];?></td>
					<td><?php echo $range[display_range];?></td>
					<td><?php echo $res_intr;?></td>
				</tr>
				<?php
				$i++;	
			}
			else
			{
			?>
			<tr>
				<td><?php echo $ts[testname];?></td>
				<?php
				foreach($dates as $date)
				{
					$res=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result where patient_id='$instr' and opd_id='$primary' and ipd_id='$secondary' and testid='$ts[testid]' and date='$date'"));
					
										
					$range=mysqli_fetch_array(mysqli_query($link,"select * from qc_normal where instr_id='$instr' and primary_lot='$primary' and secondary_lot='$secondary' and test_id='$ts[testid]'"));
		
					$td_cls="";
					$res_intr="";
					$nres=$res[result];
					if($res[result] && $range[value_from] && $range[value_to])
					{
						$chk_res=explode("<",$res[result]);
						if($chk_res[1])
						{
							$nres=$chk_res[1]-1;
						}
						
						$chk_res2=explode(">",$res[result]);
						if($chk_res2[1])
						{
							$nres=$chk_res2[1]+1;
						}
						if($nres<$range[value_from] || $nres>$range[value_to])
						{
							if($nres<$range[value_from])
							{
								$res_intr="(L)";
							}
							else if($nres>$range[value_to])
							{
								$res_intr="(H)";
							}
							
							$td_cls="out_of_range_td";
						}
					}
					
					
					
					
					echo "<td class='$td_cls'>$res[result] $res_intr</td>";
					
				}
				?>
			</tr>
			<?php
			}
		}
		?>
		</table>
		<?php
	}
	else if($test>0) //-------------------IF TEST IS SELECTED--------------//
	{
		
	}
}
?>
