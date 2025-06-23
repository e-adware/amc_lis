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

$type=$_POST['type'];

/*
if($type=="test_assign")
{
	?>
	<table class="table table-bordered table-report table=condensed">
	<tr>
		<th colspan="2">INSTRUMENT DETAILS</th>
	</tr>
	</table>	
	Select Instrument:
			<span id="instr_list">
				<select id="instrument" onchange="show_test()">
					<option value="0">--Select--</option>
					<?php
					$ins=mysqli_query($link,"select * from lab_instrument_master order by name");
					while($in=mysqli_fetch_array($ins))
					{
						echo "<option value='$in[id]'>$in[name]</option>";
					}
					?>
				</select>
			</span>
			<button class="btn btn-info" style="margin-bottom:10px" onclick="add_instr()"><i class="icon-plus"></i></button>
			
			<br/><br/>
			<div id="search_test" style="margin-bottom:-10px;">
			<input type="text" id="testname" placeholder="Search by testname" onkeyup="show_test_event(event)"/>
			
			<select id="test_dep" onchange="show_test()">
				<option value="0">--All(Dept)--</option>
				<?php
				$dep=mysqli_query($link,"select distinct a.* from test_department a,testmaster b where b.category_id='1' and a.id=b.type_id order by a.id");
				while($dp=mysqli_fetch_array($dep))
				{
					echo "<option value='$dp[id]'>$dp[name]</option>";
				}
				?>
			</select>
			
			<select id="test_vaccu" onchange="show_test()">
				<option value="0">--All(Vaccu)--</option>
				<?php
				$vac=mysqli_query($link,"select * from vaccu_master order by id");
				while($vc=mysqli_fetch_array($vac))
				{
					echo "<option value='$vc[id]'>$vc[type]</option>";
				}
				?>
			</select>
			
			<span class="btn btn-info" style="margin-bottom:10px;margin-left:200px;" onclick="select_check()">
				<i class="icon-check-empty" id="icon_check"></i> Show Only Selected
			</span>
			
			</div>
			<div style="height:500px;overflow:scroll" id="test_data">
			
			</div>
		
	
	<?php
	
}
*/
if($type=="test_assign")
{
	?>
	<table class="table table-bordered table-report table-condensed">
	<tr>
		<th colspan="3">Test Assignment</th>
	</tr>
	<tr>
		<th>
			Select Instrument <br/>
			<select id="instrument" onchange="load_primary();show_test()">
				<option value="0">--Select--</option>
				<?php
				$ins=mysqli_query($link,"select * from lab_instrument_master order by name");
				while($in=mysqli_fetch_array($ins))
				{
					echo "<option value='$in[id]'>$in[name]</option>";
				}
				?>
			</select>
		</th>
		<th>
			Select Primary Lot No <br/>
			<span id="primary_sel">
				<select id="primary">
					<option value="0">--Select--</option>
				</select>
			</span>
		</th>
		<th>
			Select Secondary Lot No <br/>
			<span id="secondary_sel">
				<select id="secondary">
					<option value="0">--Select--</option>
				</select>
			</span>
		</th>
	</tr>
	</table>	
			
			
			<div id="search_test" style="margin-bottom:-10px;">
			<input type="text" id="testname" placeholder="Search by testname" onkeyup="show_test_event(event)"/>
			
			<select id="test_dep" onchange="show_test()">
				<option value="0">--All(Dept)--</option>
				<?php
				$dep=mysqli_query($link,"select distinct a.* from test_department a,testmaster b where b.category_id='1' and a.id=b.type_id order by a.id");
				while($dp=mysqli_fetch_array($dep))
				{
					echo "<option value='$dp[id]'>$dp[name]</option>";
				}
				?>
			</select>
			
			<select id="test_vaccu" onchange="show_test()">
				<option value="0">--All(Vaccu)--</option>
				<?php
				$vac=mysqli_query($link,"select * from vaccu_master order by id");
				while($vc=mysqli_fetch_array($vac))
				{
					echo "<option value='$vc[id]'>$vc[type]</option>";
				}
				?>
			</select>
			
			<button class="btn btn-info" style="margin-bottom:10px;" onclick="copy_test()"><i class="icon-copy"></i></button>
			
			<span class="btn btn-info" style="margin-bottom:10px;margin-left:100px;" onclick="select_check()">
				<i class="icon-check-empty" id="icon_check"></i> Show Only Selected
			</span>
			
			</div>
			<div style="height:500px;overflow:scroll" id="test_data">
			
			</div>
		
	
	<?php
	
}
else if($type=="load_test")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$secondary=$_POST['secondary'];
	$tname=$_POST['tname'];
	$dept=$_POST['dept'];
	$vac=$_POST['vac'];
	
	$str="select * from testmaster where category_id='1'";
	if($tname!='')
	{
		$str.=" and testname like '%$tname%'";
	}
	if($dept>0)
	{
		$str.=" and type_id='$dept'";
	}
	if($vac>0)
	{
		$str.=" and testid in(select testid from test_vaccu where vac_id='$vac')";
	}
	
	$str.=" order by testname";
	?>
	<table class="table table-condensed table-bordered table-report" id="test_list">
			<!--<thead class="table_header_fix">-->
			<tr>
				<th onclick="sel_all()" width="20px"><i class="icon-check-empty" id="sel_all"></i></th>
				<th>Name</th>
				<th>Sample</th>
				<th>Department</th>
				<th>Instrument</th>
			</tr>
			<!--</thead>-->
			<?php
			$i=1;
			$test=mysqli_query($link,$str);
			while($tst=mysqli_fetch_array($test))
			{
				
				$par_count=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from Testparameter where TestId='$tst[testid]'"));
				
				if($par_count[tot]==1 || $par_count[tot]==4)
				{
					$cb_class="icon-check-empty";
					$tr_class="";
					
					if($instr>0)
					{
						$chk_query="select count(*) as tot from qc_test_list where instr_id='$instr' and test_id='$tst[testid]'";
						if($primary>0)
						{
							$chk_query.=" and primary_lot_no='$primary'";
						}
						if($secondary>0)
						{
							$chk_query.=" and secondary_lot_no='$secondary'";
						}
						$check_box=mysqli_fetch_array(mysqli_query($link,$chk_query));
						if($check_box[tot]>0)
						{
							$cb_class="icon-check";
							$tr_class="selected";
						}
					}
				?>
				<tr onclick="tst_data_check(<?php echo $i;?>)" class="<?php echo $tr_class;?>">
					<td><i class="<?php echo $cb_class;?>" id="tst_check_<?php echo $i;?>"></i></td>
					<td>
						<?php echo $tst[testname];?>
						<input type="hidden" id="tst_id_<?php echo $i;?>" value="<?php echo $tst[testid];?>"/>
					</td>
					<td>
					<?php
						$samp=mysqli_fetch_array(mysqli_query($link,"select a.Name from Sample a,TestSample b where a.ID=b.SampleId and b.TestId='$tst[testid]'"));
						echo $samp[Name];
					?>
					</td>
					<td>
						<?php
						$dept=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$tst[type_id]'"));
						echo $dept[name];
						?>
					</td>
					<td>
					</td>
				</tr>
				<?php
				$i++;
				}
			}
			?>
				
			
			
			</table>
	<?php
}
elseif($type=="add_instr")
{
	?>
	<table class="table table-bordered table-report table=condensed">
		<tr>
			<th>Add Instrument Name</th>
		</tr>
		<tr>
			<th>
				<input type="text" id="instr_name" placeholder="Enter Instrument Name" style="width:500px"/>
				<input type="hidden" id="instr_id_upd"/>
			</th>
		</tr>
		<?php
		$instr_m=mysqli_query($link,"select * from lab_instrument_master order by name");
		while($instr=mysqli_fetch_array($instr_m))
		{
			?>
			<tr>
				<td id="upd_<?php echo $instr[id];?>" onclick="update_instr('<?php echo $instr[id];?>','<?php echo $instr[name];?>')" class="upd_instr"><?php echo $instr[name];?></td>
			</tr>
			<?php
		}	
		?>
		<tr>
			
		</tr>
		<tr>
			<th style="text-align:center">
				<button class="btn btn-info" id="save_instr_name" onclick="save_instr()"><i class="icon-save"></i> Save</button>
				<button class="btn btn-info" onclick="$('#mod').click()"><i class="icon-off"></i> Close</button>
				<button class="btn btn-info" onclick="add_instr(1)"><i class="icon-refresh"></i> New</button>
			</th>
		</tr>
	</table>
	<?php
}
elseif($type=="save_instr")
{
	$name=mysqli_real_escape_string($link,trim($_POST[name]));
	
	$upd_id=$_POST['upd_id'];
	
	$check_name=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from lab_instrument_master where name='$name'"));
	
	if($check_name[tot]==0)
	{
		if($upd_id=='')
		{
			mysqli_query($link,"insert into lab_instrument_master(name) values('$name')");
		}
		else
		{
			mysqli_query($link,"update lab_instrument_master set name='$name' where id='$upd_id'");
		}
	}
	else
	{
		echo "error";
	}
}
else if($type=="save_instr_test")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$secondary=$_POST['secondary'];
	$test=$_POST['test'];
	$save=$_POST['save'];
	
	
	if($save==1)
	{
		mysqli_query($link,"insert into qc_test_list(instr_id,primary_lot_no,secondary_lot_no,test_id) values('$instr','$primary','$secondary','$test')");
	}
	else
	{
		mysqli_query($link,"delete from qc_test_list where instr_id='$instr' and primary_lot_no='$primary' and secondary_lot_no='$secondary' and test_id='$test'");
	}
}

if($type=="qc_master")
{
	?>
	<br/><br/>
	<button class="btn btn-info" onclick="new_qc_master()"><i class="icon-plus"></i> Add New</button>
	<button class="btn btn-info" onclick="add_instr()"><i class="icon-plus"></i> Add New Instrument</button>
	
	<table class="table table-bordered table-report table-condensed">
		<tr>
			<th>#</th><th>Instrument</th><th>Primary Lot No</th><th>Secondary Lot No</th><th>QC Text</th><th>Status</th><th>First Run Date</th><th>Last Run Date</th><th></th>
		</tr>
	<?php
	$i=1;
	$qry=mysqli_query($link,"select distinct instr_id,primary_lot_no from qc_master order by slno desc");
	while($q=mysqli_fetch_array($qry))
	{
		$tot_sec=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from qc_master where instr_id='$q[instr_id]' and primary_lot_no='$q[primary_lot_no]'"));
		$instr_name=mysqli_fetch_array(mysqli_query($link,"select * from lab_instrument_master where id='$q[instr_id]'"));
		?>
		<tr>
			<td rowspan="<?php echo $tot_sec[tot];?>" ><?php echo $i;?></td>
			<td rowspan="<?php echo $tot_sec[tot];?>" ><?php echo $instr_name[name];?> <input type="hidden" id="instr_<?php echo $i;?>" value="<?php echo $q[instr_id];?>"/> </td>
			<td rowspan="<?php echo $tot_sec[tot];?>" ><?php echo $q[primary_lot_no];?> <input type="hidden" id="primary_<?php echo $i;?>" value="<?php echo $q[primary_lot_no];?>"/> </td>
		
			<?php
			$j=1;
			$sec_lot=mysqli_query($link,"select * from qc_master where instr_id='$q[instr_id]' and primary_lot_no='$q[primary_lot_no]'");
			while($sc=mysqli_fetch_array($sec_lot))
			{
				$f_run=mysqli_fetch_array(mysqli_query($link,"select time,date from test_sample_result where patient_id='$q[instr_id]' and opd_id='$q[primary_lot_no]' and ipd_id='$sc[secondary_lot_no]' order by slno asc"));
				$l_run=mysqli_fetch_array(mysqli_query($link,"select time,date from test_sample_result where patient_id='$q[instr_id]' and opd_id='$q[primary_lot_no]' and ipd_id='$sc[secondary_lot_no]' order by slno desc"));
				
				if($j>1) { echo "<tr>";}
				?>
					<td><?php echo $sc[secondary_lot_no];?></td>
					<td><?php echo $sc[qc_text];?></td>
					<td><?php if($sc[status]==1){ echo "Active";}else{echo "Not Active";};?></td>
					<td><?php if($f_run[date]){ echo convert_date($f_run[date]); };?></td>
					<td><?php if($l_run[date]){ echo convert_date($l_run[date]); };?></td>
					
					<?php
					if($j==1)
					{ 
						?>
					<td rowspan="<?php echo $tot_sec[tot];?>"> <button class="btn btn-info btn-mini" onclick="edit_qc_master(<?php echo $i;?>)"><i class="icon-edit"></i></button>  </td> 
					<?php 
					}
					?>
					</tr>
				<?php
				$j++;		
			}
			?> 
		
		<?php
		$i++;
	}
	?>	
	</table>	
	
	<?php
}
elseif($type=="new_qc")
{
	$typ=$_POST['typ'];
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	
	?>
	<table class="table table-bordered table-report">
	<tr>
		<th>QC Master</th>
	</tr>
	<tr>
		<td>Select Instrument <br/>
			<select id="instrument">
				<option value="0">--Select--</option>
				<?php
				$ins=mysqli_query($link,"select * from lab_instrument_master order by name");
				while($in=mysqli_fetch_array($ins))
				{
					$sel="";
					if($typ=="update")
					{
						if($instr==$in[id]){ $sel="Selected='selected'";} else { $sel="";}
					}
					echo "<option value='$in[id]' $sel>$in[name]</option>";
				}
				?>
			</select>
			<input type="hidden" id="instr_old" value="<?php echo $instr;?>"/> <!-- For Update--->
		</td>
	</tr>
	<tr>
		<td>
		Primary Lot No <br/>
		<input type="text" id="primary" value="<?php echo $primary;?>"/> 
		<input type="hidden" id="primary_old" value="<?php echo $primary;?>"/> <!-- For Update--->
		 
		<button class="btn btn-info" style="margin-bottom:10px" onclick="add_more()"><i class="icon-plus"></i></button>
		<br/>
		<div id="prim_second">
			<?php
			if($typ=="update")
			{
				$sec=mysqli_query($link,"select * from qc_master where instr_id='$instr' and primary_lot_no='$primary' order by slno");
				while($sc=mysqli_fetch_array($sec))
				{
					?>
						<div>
							<input type="text" id="seconday" class="second" placeholder="Add Secondary Lot No" value="<?php echo $sc[secondary_lot_no];?>"/>
							<input type="text" id="qc_text" class="qc_text" placeholder="Add QC Text" value="<?php echo $sc[qc_text];?>" style="width:110px"/>
							<select class="status" id="status" style="width:100px">
								<option value="1" <?php if($sc[status]==1){ echo "Selected='selected'";} ?>>Active</option>
								<option value="0">Inactive</option>
							</select>
						</div>
					<?php
				}
			}
			else
			{
			?>
			<div>
				<input type="text" id="seconday" class="second" placeholder="Add Secondary Lot No"/>
				<input type="text" id="qc_text" class="qc_text" placeholder="Add QC Text" style="width:110px"/>
				<select class="status" id="status" style="width:100px">
					<option value="1">Active</option>
					<option value="0">Inactive</option>
				</select>
			</div>		
			<?php
			}
			?>
		</div>
		</td>
	</tr>
	<tr>
		<td style="text-align:center">
			<?php
			if($typ=="update")
			{
			?>	
				<button class="btn btn-info btn-mini" onclick="save_qc_master('upd')"><i class="icon-save"></i> Update</button>
			<?php
			}
			else
			{
			?>	
				<button class="btn btn-info btn-mini" onclick="save_qc_master('save')"><i class="icon-save"></i> Save</button>
			<?php
			}
			?>
			<button class="btn btn-info btn-mini" onclick="$('#mod').click()"><i class="icon-off"></i> Close</button>
		</td>
	</tr>
	</table>
	<?php
}
elseif($type=="save_qc")
{
	$instrument=$_POST['instrument'];
	$primary=$_POST['primary'];
	$sec_det=$_POST['sec'];
	
	$prim_error="";
	$sec_error="";
	
	$val=$_POST['val'];
	if($val=="save")
	{
		$chk_primary=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from qc_master where instr_id='$instrument' and primary_lot_no='$primary'"));
		if($chk_primary[tot]==0)
		{
			$sc=explode("#qc_koushik_done#",$sec_det);
			foreach($sc as $scc)
			{
				if($scc)
				{
					$det=explode("@qc_koushik@",$scc);
					$chk_sec=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from qc_master where instr_id='$instrument' and primary_lot_no='$primary' and secondary_lot_no='$det[0]' "));
					if($chk_sec[tot]>0)
					{
						$sec_error.="@".$det[0];
					}
				}
			}
		}
		else
		{
			$prim_error=$primary;
		}
	}
	else
	{
		$instr_old=$_POST['instr_old'];
		$primary_old=$_POST['primary_old'];
		mysqli_query($link,"delete from qc_master where instr_id='$instr_old' and primary_lot_no='$primary_old'");
		$prim_error="";
		$sec_error="";
		
		$sc=explode("#qc_koushik_done#",$sec_det);
	}
	
	
	if($prim_error!='' || $sec_error!='')
	{
		echo "Error@@".$prim_error."@@".$sec_error;
	}
	else
	{
		foreach($sc as $sc_det)
		{
			if($sc_det)
			{
				$det=explode("@qc_koushik@",$sc_det);
				mysqli_query($link,"insert into qc_master(instr_id,primary_lot_no,secondary_lot_no,qc_text,status) values('$instrument','$primary','$det[0]','$det[1]','$det[2]')");
			}
		}
		echo "Saved@@";
	}
	
}
else if($type=="load_primary")
{
	$instr=$_POST['instr'];
	?>
	<select id="primary" onchange="load_secondary();show_test();hide_test_normal();">
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
	<select id="secondary" onchange="load_qc_text();show_test();hide_test_normal();">
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
else if($type=="normal_range")
{
	?>
	<br/>
	<table class="table table-bordered table-report table-condensed">
	<tr>
		<th colspan="4">Set Normal Range</th>
	</tr>
	<tr>
		<th>
		Select Instrument <br/>
			<select id="instrument" onchange="load_primary();hide_test_normal();">
				<option value="0">--Select--</option>
				<?php
				$ins=mysqli_query($link,"select * from lab_instrument_master order by name");
				while($in=mysqli_fetch_array($ins))
				{
					echo "<option value='$in[id]'>$in[name]</option>";
				}
				?>
			</select>
		</th>
		<th>
			Select Primary Lot No <br/>
			<span id="primary_sel">
				<select id="primary">
					<option value="0">--Select--</option>
				</select>
			</span>
		</th>
		<th>
			Select Secondary Lot No <br/>
			<span id="secondary_sel">
				<select id="secondary">
					<option value="0">--Select--</option>
				</select>
			</span>
		</th>
	
	
		<th style="text-align:center">
			<button class="btn btn-info" onclick="load_normal()" style="margin-top:15px;">Load</button>
		</th>
	</tr>
	</table>
	
	<div id="qc_normal"></div>
	<?php
}
else if($type=="test_range")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$secondary=$_POST['secondary'];
	
	?>
	<table class="table table-bordered table-report table-condensed">
	<tr>
		<th>#</th> <th>Test</th><th>Range Min</th> <th>Range Max</th> <th>Display Range</th>
	</tr>	
	<?php
	$i=1;
	$test=mysqli_query($link,"select a.testname,a.testid from testmaster a,qc_test_list b where a.testid=b.test_id and b.instr_id='$instr' and b.primary_lot_no='$primary' and b.secondary_lot_no='$secondary' order by a.testname");
	while($tst=mysqli_fetch_array($test))
	{
		$nr=mysqli_fetch_array(mysqli_query($link,"select * from qc_normal where instr_id='$instr' and primary_lot='$primary' and secondary_lot='$secondary' and test_id='$tst[testid]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $tst[testname];?> <input type="hidden" id="testid_<?php echo $i;?>" value="<?php echo $tst[testid];?>" /></td>
			<td><input type="text" id="value_from_<?php echo $i;?>" value="<?php echo $nr[value_from];?>" onkeyup="update_normal(<?php echo $i;?>,'from',event)"/></td>
			<td><input type="text" id="value_to_<?php echo $i;?>" value="<?php echo $nr[value_to];?>" onkeyup="update_normal(<?php echo $i;?>,'to',event)"/></td>
			<td><input type="text" id="display_<?php echo $i;?>" value="<?php echo $nr[display_range];?>" onkeyup="update_normal(<?php echo $i;?>,'display',event)"/></td>
		</tr>
		<?php
		$i++;
	}
	?>
	</table>
	<?php
}

else if($type=="save_range")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$secondary=$_POST['secondary'];
	
	$test=$_POST['test'];
	$val_from=mysqli_real_escape_string($link,$_POST['val_from']);
	$val_to=mysqli_real_escape_string($link,$_POST['val_to']);
	
	$typ=$_POST['typ'];
	
	$display=$val_from." - ".$val_to;
	
	
	$chk_norm=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from qc_normal where instr_id='$instr' and primary_lot='$primary' and secondary_lot='$secondary' and test_id='$test'"));
	if($chk_norm[tot]==0)
	{
		//echo "insert into qc_normal(instr_id,primary_lot,secondary_lot,test_id,value_from,value_to,display_range) values('$instr','$primary','$secondary','$test','$val_from','$val_to','$display')";
		mysqli_query($link,"insert into qc_normal(instr_id,primary_lot,secondary_lot,test_id,value_from,value_to,display_range) values('$instr','$primary','$secondary','$test','$val_from','$val_to','$display')");
	}
	else
	{
		//echo "update qc_normal set value_from='$val_from',display_range='$display' where instr_id='$instr' and primary_lot='$primary' and secondary_lot='$secondary' and testid='$test'";
		if($typ=="from")
		{
			mysqli_query($link,"update qc_normal set value_from='$val_from',display_range='$display' where instr_id='$instr' and primary_lot='$primary' and secondary_lot='$secondary' and test_id='$test'");	
		}
		else
		{
			mysqli_query($link,"update qc_normal set value_to='$val_to',display_range='$display' where instr_id='$instr' and primary_lot='$primary' and secondary_lot='$secondary' and test_id='$test'");
		}
	}
}
else if($type=="copy_test")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	$secondary=$_POST['secondary'];
	
	?>
	<table class="table table-report" id="copy_table">
	<tr>
		<th colspan="3">COPY TEST</th>
	</tr>
	<tr>
		<th>
			Select Instrument <br/>
			<select id="instrument_1" onchange="load_primary_copy();">
				<option value="0">--Select--</option>
				<?php
				$ins=mysqli_query($link,"select * from lab_instrument_master order by name");
				while($in=mysqli_fetch_array($ins))
				{
					echo "<option value='$in[id]'>$in[name]</option>";
				}
				?>
			</select>
		</th>
		<th>
			Select Primary Lot No <br/>
			<span id="primary_sel_1">
				<select id="primary_1">
					<option value="0">--Select--</option>
				</select>
			</span>
		</th>
		<th>
			Select Secondary Lot No <br/>
			<span id="secondary_sel_1">
				<select id="secondary_1">
					<option value="0">--Select--</option>
				</select>
			</span>
		</th>
	</tr>
	<tr>
		<th colspan="3" style="text-align:center">Copy To</th>
	</tr>
	<tr>
		<th>
			Select Instrument <br/>
			<select id="instrument_2" disabled>
				<option value="0">--Select--</option>
				<?php
				$ins=mysqli_query($link,"select * from lab_instrument_master order by name");
				while($in=mysqli_fetch_array($ins))
				{
					if($instr==$in[id]){ $ins="Selected='selected'";} else { $ins="";}
					echo "<option value='$in[id]' $ins>$in[name]</option>";
				}
				?>
			</select>
		</th>
		<th>
			Select Primary Lot No <br/>
			
			<select id="primary_2" disabled>
				<option value="0">--Select--</option>
				<?php
					$prim=mysqli_query($link,"select distinct primary_lot_no from qc_master where instr_id='$instr' order by slno desc");
					while($p=mysqli_fetch_array($prim))
					{
						if($primary==$p[primary_lot_no]){ $sel="Selected='selected'";} else { $sel="";}
						echo "<option value='$p[primary_lot_no]' $sel>$p[primary_lot_no]</option>";
					}
				?>
			</select>
			
		</th>
		<th>
			Select Secondary Lot No <br/>
			
			<select id="secondary_2" disabled>
				<option value="0">--Select--</option>
				<?php
					$sec=mysqli_query($link,"select distinct secondary_lot_no,qc_text from qc_master where instr_id='$instr' and primary_lot_no='$primary' order by slno");
					while($s=mysqli_fetch_array($sec))
					{
						if($secondary==$s[secondary_lot_no]){ $sel1="Selected='selected'";} else { $sel1="";}
						echo "<option value='$s[secondary_lot_no]' $sel1>$s[secondary_lot_no]($s[qc_text])</option>";
					}
				?>
			</select>
			
		</th>
	</tr>
	<tr>
		<th colspan="3" style="text-align:center">
			<button class="btn btn-info" id="" onclick="copy_test_data()"><i class="icon-copy"></i> Copy</button>
			<button class="btn btn-info" onclick="$('#mod').click()"><i class="icon-off"></i> Close</button>
		</th>
	</tr>
	</table>
	<?php
}
else if($type=="load_primary_copy")
{
	$instr=$_POST['instr'];
	?>
	<select id="primary_1" onchange="load_secondary_copy();">
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
else if($type=="load_second_copy")
{
	$instr=$_POST['instr'];
	$primary=$_POST['primary'];
	?>
	<select id="secondary_1" onchange="load_qc_text_copy();">
		<option value="0">--Select--</option>
		<?php
			$sec=mysqli_query($link,"select distinct secondary_lot_no from qc_master where instr_id='$instr' and primary_lot_no='$primary' order by slno");
			while($s=mysqli_fetch_array($sec))
			{
				echo "<option value='$s[secondary_lot_no]'>$s[secondary_lot_no]</option>";
			}
		?>
	</select>
	<input type="text" id="qc_text_1" style="width:80px" readonly/>
	<?php
}
else if($type=="copy_test_data")
{
	$instr1=$_POST['instr1'];
	$primary1=$_POST['primary1'];
	$second1=$_POST['secondary1'];	
	
	$instr2=$_POST['instr2'];
	$primary2=$_POST['primary2'];
	$second2=$_POST['secondary2'];
	
	$test_l=mysqli_query($link,"select * from qc_test_list where instr_id='$instr1' and primary_lot_no='$primary1' and secondary_lot_no='$second1'");
	while($tst=mysqli_fetch_array($test_l))
	{
		$chk_tst=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from qc_test_list where instr_id='$instr2' and primary_lot_no='$primary2' and secondary_lot_no='$second2' and test_id='$tst[test_id]'"));
		if($chk_tst[tot]==0)
		{
			mysqli_query($link,"insert into qc_test_list(instr_id,primary_lot_no,secondary_lot_no,test_id) values('$instr2','$primary2','$second2','$tst[test_id]')");
		}
	}
}
?>
