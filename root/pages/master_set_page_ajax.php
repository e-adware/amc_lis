<?php
include("../../includes/connection.php");

$type=$_POST['type'];
if($type==1)
{
	$val=$_POST['val'];
	if($val==1)
	{
		?>
			<div id="tab<?php echo $val;?>" class="tab-pane active">
				<table class="table table-bordered table-condensed">
					<tr>
						<td>
							<b>Select Test</b> <br/>
							<select id="testid" onchange="load_all_param()" autofocus class="span5">
								<option value="0">--Select Test--</option>
								<?php
									$vac_sr=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `testmaster` WHERE `category_id`='1' ORDER  BY `testname` ASC ");
									while($v_s=mysqli_fetch_array($vac_sr))
									{
										echo "<option value='$v_s[testid]'>$v_s[testname]</option>";
									}
								?>
							</select>
						</td></tr>
						<tr><td>
							<b>Select Parameter</b> <br/>
							<select id="param" onchange="load_fix_param()" class="span5">
								<option value="0">--Select Parameter--</option>
							</select>
						</td>
					</tr>
				</table>
				<div id="load_data"></div>
				
			</div>
		<?php
	}
	else if($val==2)
	{
		$sname=$_POST['sname'];
		$str="select * from Sample order by Name";
		if($sname!='')
		{
			$str="select * from Sample where Name like '%$sname%' order by Name";
		}
		?>
			<input type="text" id="s_name" placeholder="Press Enter to search" onkeyup="search_sample(this,event)" value="<?php echo $sname;?>"/>
			<button id="add_new" class="btn btn-info" style="margin-bottom:10px" onclick="add_new_sample(0,'')"><i class="icon-plus"></i> Add New Sample</button>
			<div class="tab_list_table">
			<table class="table table-bordered table-report">
			<tr>
				<th>Slno</th> <th>Sample Name</th> <th> Parameters</th> <th></th>
			</tr>
			<?php
			$i=1;
			$samp=mysqli_query($link,$str);
			while($smp=mysqli_fetch_array($samp))
			{
				?>
				<tr>
					<td> <?php echo $i;?></td>
					<td> <b><?php echo $smp[Name];?></b> </td>
					<td>
						<?php
							$j=1;
							$par=mysqli_query($link,"select distinct ParamaterId from Testparameter where sample='$smp[ID]'");
							while($p=mysqli_fetch_array($par))
							{
								$pname=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID='$p[ParamaterId]'"));
								echo "<div class='pspan'>$pname[Name]</div>";
								if($j%5==0)
								{
									echo "<br/>";
								}
								$j++;	
							}	
						?>
					</td>
					<td>
						<?php
						$sn=mysqli_real_escape_string($link,$smp[Name]);
						?>						
						<div class="btn-group">
							<button id="" class="btn btn-info" onclick="add_new_sample(<?php echo $smp[ID];?>,'<?php echo $sn;?>')"><i class="icon-cogs"></i></button>
							<button id="" class="btn btn-danger" onclick="delete_sample(<?php echo $smp[ID];?>,'<?php echo $sn;?>')"><i class="icon-cut"></i></button>
						</div>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</table>
			</div>
		<?php
	}
	else if($val==3)
	{
		$vname=$_POST['vname'];
		$str="select * from vaccu_master order by type";
		if($vname!='')
		{
			$str="select * from vaccu_master where type like '%$vname%' order by type";
		}
		?>
			<input type="text" id="v_name" placeholder="Press Enter to search" onkeyup="search_vaccu(this,event)" value="<?php echo $vname;?>"/>
			<button id="add_new" class="btn btn-info" style="margin-bottom:10px" onclick="add_new_vaccu(0,'','')"><i class="icon-plus"></i> Add New Vaccu</button>
			<div class="tab_list_table">
			<table class="table table-bordered table-report">
			<tr>
				<th>Slno</th> <th>Vaccu Name</th> <th> Parameters</th> <th>Barcode Suffix</th> <th></th>
			</tr>
			<?php
			$i=1;
			$vacc=mysqli_query($link,$str);
			while($vc=mysqli_fetch_array($vacc))
			{
				?>
				<tr>
					<td> <?php echo $i;?></td>
					<td> <b><?php echo $vc[type];?></b> </td>
					<td>
						<?php
							$j=1;
							$par=mysqli_query($link,"select distinct ParamaterId from Testparameter where vaccu='$vc[id]'");
							while($p=mysqli_fetch_array($par))
							{
								$pname=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID='$p[ParamaterId]'"));
								echo "<div class='pspan'>$pname[Name]</div>";
								if($j%5==0)
								{
									echo "<br/>";
								}
								$j++;	
							}	
						?>
					</td>
					<td> <?php echo $vc[barcode_suffix];?> </td>
					<td>
						<?php
						$vn=mysqli_real_escape_string($link,$vc[type]);
						?>						
						<div class="btn-group">
							<button id="" class="btn btn-info" onclick="add_new_vaccu(<?php echo $vc[id];?>,'<?php echo $vn;?>','<?php echo $vc[barcode_suffix];?>')"><i class="icon-cogs"></i></button>
							<button id="" class="btn btn-danger" onclick="delete_vaccu(<?php echo $vc[id];?>,'<?php echo $vn;?>')"><i class="icon-cut"></i></button>
						</div>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</table>
			</div>
		<?php
	}
	
	else if($val==4)
	{
		
		$mname=$_POST['mname'];
		$str="select * from test_methods order by name";
		if($mname!='')
		{
			$str="select * from test_methods where name like '%$mname%' order by name";
		}
		?>
			<input type="text" id="m_name" placeholder="Press Enter to search" onkeyup="search_method(this,event)" value="<?php echo $mname;?>"/>
			<button id="add_new" class="btn btn-info" style="margin-bottom:10px" onclick="add_new_method(0,'')"><i class="icon-plus"></i> Add New Method</button>
			<div class="tab_list_table">
			<table class="table table-bordered table-report">
			<tr>
				<th>Slno</th> <th>Method Name</th> <th> Parameters</th> <th></th>
			</tr>
			<?php
			$i=1;
			$meth=mysqli_query($link,$str);
			while($mth=mysqli_fetch_array($meth))
			{
				?>
				<tr>
					<td> <?php echo $i;?></td>
					<td> <b><?php echo $mth[name];?></b> </td>
					<td>
						<?php
							$j=1;
							$par=mysqli_query($link,"select Name from Parameter_old where method='$mth[id]' order by Name");
							while($p=mysqli_fetch_array($par))
							{
								echo "<div class='pspan'>$p[Name]</div>";
								if($j%5==0)
								{
									echo "<br/>";
								}
								$j++;
							}
						?>
					</td>
					<td>
						<?php
						$mn=mysqli_real_escape_string($link,$mth[name]);
						?>
						<div class="btn-group">
							<button id="" class="btn btn-info" onclick="add_new_method(<?php echo $mth[id];?>,'<?php echo $mn;?>')"><i class="icon-cogs"></i></button>
							<button id="" class="btn btn-danger" onclick="delete_method(<?php echo $mth[id];?>,'<?php echo $mn;?>')"><i class="icon-cut"></i></button>
						</div>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</table>
			</div>
		<?php
		
	}
	else if($val==5)
	{
		$rname=$_POST['rname'];
		$str="select * from ResultOption order by name";
		if($rname!='')
		{
			$str="select * from ResultOption where name like '%$rname%' order by name";
		}
		?>
			<input type="text" id="r_name" placeholder="Press Enter to search" onkeyup="search_res_op(this,event)" value="<?php echo $rname;?>"/>
			<button id="add_new" class="btn btn-info" style="margin-bottom:10px" onclick="add_new_res_op(0,'')"><i class="icon-plus"></i> Add New List</button>
			<div class="tab_list_table">
			<table class="table table-bordered table-report">
			<tr>
				<th>Slno</th> <th>List Name</th> <th>List Option</th> <th> Parameters</th> <th></th>
			</tr>
			<?php
			$i=1;
			$res_op=mysqli_query($link,$str);
			while($res=mysqli_fetch_array($res_op))
			{
				?>
				<tr>
					<td> <?php echo $i;?></td>
					<td> <b><?php echo $res[name];?></b> </td>
					<td>
						<?php
						$opt=mysqli_query($link,"select * from ResultOptions where id='$res[id]'");
						while($op=mysqli_fetch_array($opt))
						{
							$op_name=mysqli_fetch_array(mysqli_query($link,"select name from Options where id='$op[optionid]'"));
							echo "<i>$op_name[name]</i> <br/>";
						}
						?>
					</td>
					<td>
						<?php
							$j=1;
							$par=mysqli_query($link,"select ID,Name from Parameter_old where ResultOptionID='$res[id]'");
							while($p=mysqli_fetch_array($par))
							{
								echo "<div class='pspan'>$p[Name]</div>";
								if($j%5==0)
								{
									echo "<br/>";
								}
								$j++;
							}
						?>
					</td>
					<td>
						<?php
						$rn=mysqli_real_escape_string($link,$res[name]);
						?>
						<div class="btn-group">
							<button id="" class="btn btn-info" onclick="add_new_res_op(<?php echo $res[id];?>,'<?php echo $rn;?>')"><i class="icon-cogs"></i></button>
							<button id="" class="btn btn-primary" onclick="add_res_option(<?php echo $res[id];?>)"><i class="icon-edit"></i></button>
							<button id="" class="btn btn-danger" onclick="delete_res_op(<?php echo $res[id];?>,'<?php echo $rn;?>')"><i class="icon-cut"></i></button>
						</div>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</table>
			</div>
		<?php
	}
	else if($val==6)
	{
		
		$opname=$_POST['opname'];
		$str="select * from Options order by name";
		if($opname!='')
		{
			$str="select * from Options where name like '%$opname%' order by name";
		}
		
		?>
			<input type="text" id="op_name" placeholder="Press Enter to search" onkeyup="search_option(this,event)" value="<?php echo $opname;?>"/>
			<button id="add_new" class="btn btn-info" style="margin-bottom:10px" onclick="add_new_option_new(0,'')"><i class="icon-plus"></i> Add New Option</button>
			<div class="tab_list_table">
			<table class="table table-bordered table-report">
			<tr>
				<th>Slno</th> <th>Option Name</th> <th>List Name</th> <th></th>
			</tr>
			<?php
			$i=1;
			$option=mysqli_query($link,$str);
			while($opt=mysqli_fetch_array($option))
			{
				?>
				<tr>
					<td> <?php echo $i;?></td>
					<td> <b><?php echo $opt[name];?></b> </td>
					<td>
						<?php
							$j=1;
							$par=mysqli_query($link,"select id from ResultOptions where optionid='$opt[id]'");
							while($p=mysqli_fetch_array($par))
							{
								$res_name=mysqli_fetch_array(mysqli_query($link,"select name from ResultOption where id='$p[id]'"));
								echo "<div class='pspan'>$res_name[name]</div>";
								if($j%5==0)
								{
									echo "<br/>";
								}
								$j++;
							}
						?>
					</td>
					<td>
						<?php
						$on=mysqli_real_escape_string($link,$opt[name]);
						?>
						<div class="btn-group">
							<button id="" class="btn btn-info" onclick='add_new_option_new(<?php echo $opt[id];?>,"<?php echo $on;?>")'><i class="icon-cogs"></i></button>
							<button id="" class="btn btn-danger" onclick='delete_option_new(<?php echo $opt[id];?>,"<?php echo $on;?>")'><i class="icon-cut"></i></button>
						</div>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</table>
			</div>
		<?php
		
	}
	else if($val==7)
	{
		?>
		<div class="div_size span">
			<table class="table table-bordered table-report table-condensed">
				<tr>
					<th>ANTIBIOTICS</th>
				</tr>
				<tr>
					<th>
						<input type="hidden" id="anti_bio_id" value="0">
						<input type="text" placeholder="Add Antibiotics" id="anti_bio" onkeyup="search_anti(this.value)"/>
						<button id="" class="btn btn-info btn-mini" onclick="save_antibio()"> <i class="icon-save"></i></button>
					</th>
				</tr>
				<tr>
					<th id="list_bio"></th>
				</tr>
			</table>
		</div>
		
		<div class="div_size span">
			<table class="table table-bordered table-report table-condensed">
				<tr>
					<th>SPECIMEN</th>
				</tr>
				<tr>
					<th>
						<input type="hidden" id="spec_id" value="0">
						<input type="text" placeholder="Add Specimen" id="spec" onkeyup="search_spec(this.value)"/>
						<button id="" class="btn btn-info btn-mini" onclick="save_spec()"> <i class="icon-save"></i></button>
					</th>
				</tr>
				<tr>
					<th id="list_spec"></th>
				</tr>
			</table>
		</div>
		
		<div class="div_size span">
			<table class="table table-bordered table-report table-condensed">
				<tr>
					<th>ORGANISM</th>
				</tr>
				<tr>
					<th>
						<input type="hidden" id="org_id" value="0">
						<input type="text" placeholder="Add Organism" id="org" onkeyup="search_org(this.value)"/>
						<button id="" class="btn btn-info btn-mini" onclick="save_org()"> <i class="icon-save"></i></button>
					</th>
				</tr>
				<tr>
					<th id="list_org"></th>
				</tr>
			</table>
		</div>
		
		<script>
			load_antibio();
			load_spec();
			load_org();
		</script>
		<?php
	}
	else if($val==8)
	{
		?>
			<table class="table table-bordered table-condensed">
				<tr>
					<th>Select Test <br/>
						<select id="testid" onchange="load_normal(this.value,'test')" style="width:400px">
							<option value="0">Select</option>
							<?php
							$tst=mysqli_query($link,"select * from testmaster where category_id='1' order by testname");
							while($t=mysqli_fetch_array($tst))
							{
								echo "<option value='$t[testid]'>$t[testname]</option>";	
							}
							?>
						</select>
					</th>
					<th>Select Param(Pad) <br/>
						<select id="param_pad" onchange="load_normal(this.value,'param')">
							<option value="0">--Select--</option>
							<?php
								$par=mysqli_query($GLOBALS["___mysqli_ston"],"select * from Parameter_old where ResultType='7' order by Name");
								while($pad=mysqli_fetch_array($par))
								{
									echo "<option value='$pad[ID]'>$pad[Name]</option>";
								}
							?>
						</select>
					</th>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left">
						<textarea style="height:350px;width:1000px" name="article-body" id="normal"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center">
						<input type="button" id="save" value="Save" class="btn btn-info" onClick="save_normal()"/>
					</td>
				</tr>
			</table>
			
			<script>
				$("#testid").select2({ theme: "classic" });
				$("#param_pad").select2({ theme: "classic" });
				
				add_editor();
			</script>
		<?php
	}

	else if($val==9)
{
	?>
		<div style="display: flex;">
			<div style="flex: 1; margin-right: 20px;">
				<table class="table table-bordered table-condensed">
					<tr>
						<th>Select Test <br/>
							<select id="testid" onchange="load_normal(this.value,'test')" style="width:400px">
								<option value="0">Select</option>
								<?php
								$tst = mysqli_query($link, "SELECT * FROM `testmaster` WHERE `category_id`='1' ORDER BY `testname`");
								while ($t = mysqli_fetch_array($tst)) {
									echo "<option value='{$t['testid']}'>{$t['testname']}</option>";
								}
								?>
							</select>
						</th>
					</tr>
					<tr>
						<td>
							<label for="normal">Barcode Suffix</label>
							<input type="text" id="suffix_text">
							<input type="hidden" id="current_testid" value="" />
							<input type="hidden" id="original_suffix" value="" />
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center">
							<input type="button" id="save" value="Save" class="btn btn-info" onclick="save_suffix_name()"/>
						</td>
					</tr>
				</table>
			</div>
			<div style="flex: 1;">
				<table class="table table-bordered table-condensed" id="suffix_table">
					<tr>
						<th>Test Name</th>
						<th>Barcode Suffix</th>
						<th>Action</th> 
					</tr>
					<?php
					$result = mysqli_query($link, "SELECT a.*, b.`testname` FROM `barcode_test_suffix` a, `testmaster` b WHERE a.`testid`=b.`testid` ORDER BY b.`testname`");
					while ($row = mysqli_fetch_assoc($result)) {
						echo "<tr onclick='populateFields({$row['testid']}, \"{$row['suffix']}\")'>
								<td>{$row['testname']}</td>
								<td>{$row['suffix']}</td>
								<td>
									<button class='btn btn-danger btn-sm' onclick='delete_suffix({$row['testid']}); return false;'>Delete</button>
								</td>
							  </tr>";
					}
					?>
				</table>
			</div>
		</div>
		
		<script>
			$("#testid").select2({ theme: "classic" });
			$("#param_pad").select2({ theme: "classic" });
		</script>
	<?php
}

}


else if($type==2)
{
	$testid=$_POST['testid'];
	
	echo "<option value='0'>Select Parameter</option>";
	
	$tst_qry=mysqli_query($link, " SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' ORDER BY `sequence` ");
	while($tst=mysqli_fetch_array($tst_qry))
	{
		$param_name=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `Parameter_old` WHERE `ID`='$tst[ParamaterId]' "));
		echo "<option value='$tst[ParamaterId]'>$param_name[Name]</option>";
	}
}

else if($type==3)
{
	$testid=$_POST['testid'];
	$param=$_POST['param'];
	
	if($param)
	{
	
		$fix_param=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `param_fix_result` WHERE `testid`='$testid' AND `paramid`='$param' "));
		
		if($fix_param["range_check"]==1)
		{
			$range_check_ch="checked";
		}else
		{
			$range_check_ch="";
		}
		
		if($fix_param["must_save"]==1)
		{
			$must_save_ch="checked";
		}else
		{
			$must_save_ch="";
		}
	
?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Default value</th>
		</tr>
		<tr>
			<td>
				<!--<input type="text" id="fix_param_val" value="<?php echo $fix_param['result']; ?>" onkeyup="save_param_fix_val('<?php echo $testid; ?>','<?php echo $param; ?>',event,this.value)">-->
				<textarea id="fix_param_val" class="span5"><?php echo trim($fix_param['result']); ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" id="range_check" <?php echo $range_check_ch; ?> onClick="fix_para_check('<?php echo $testid; ?>','<?php echo $param; ?>')" > Check result
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" id="must_save" <?php echo $must_save_ch; ?> onClick="fix_para_must_save('<?php echo $testid; ?>','<?php echo $param; ?>')" > Must Save (Before Approval)
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="save" value="Save" class="btn btn-success" onClick="save_param_fix_val('<?php echo $testid; ?>','<?php echo $param; ?>')" >
			</td>
		</tr>
	</table>
<?php
	}
}


else if($type==4)
{
	$testid=$_POST['testid'];
	$param=$_POST['param'];
	$val=$_POST['fix_param_val'];
	$val=str_replace("'","''", $val);
	$range_check=$_POST['range_check'];
	$must_save=$_POST['must_save'];
	
	mysqli_query($link, " DELETE FROM `param_fix_result` WHERE `testid`='$testid' AND `paramid`='$param' ");
	echo " INSERT INTO `param_fix_result`(`testid`, `paramid`, `result`, `range_check`, `must_save`) VALUES ('$testid','$param','$val','$range_check','$must_save') ";
	if($val)
	{
		mysqli_query($link, " INSERT INTO `param_fix_result`(`testid`, `paramid`, `result`, `range_check`, `must_save`) VALUES ('$testid','$param','$val','$range_check','$must_save') ");
	}
	
}

else if($type==5)
{
	$sid=$_POST[sid];
	$sname=mysqli_real_escape_string($link,$_POST['name']);
	
	if($sid==0)
	{
		$id=mysqli_fetch_array(mysqli_query($link,"select max(ID)+1 as tot from Sample"));
		mysqli_query($link,"insert into Sample values('$id[tot]','$sname')");
		
		echo "Saved";
	}
	else
	{
		mysqli_query($link,"update Sample set Name='$sname' where ID='$sid'");
		
		echo "Updated";
	}
}

else if($type==6)
{
	$sid=$_POST[sid];
	
	mysqli_query($link,"delete from Sample where ID='$sid'");
	
	echo "Deleted";
}
else if($type==7)
{
	$vid=$_POST[vid];
	$vname=mysqli_real_escape_string($link,$_POST['name']);
	$suff=mysqli_real_escape_string($link,$_POST['suff']);
	
	if($vid==0)
	{
		mysqli_query($link,"insert into vaccu_master(type,barcode_suffix) values('$vname','$suff')");
		
		echo "Saved";
	}
	else
	{
		mysqli_query($link,"update vaccu_master set type='$vname',barcode_suffix='$suff' where id='$vid'");
		
		echo "Updated";
	}
}
else if($type==8)
{
	$vid=$_POST[vid];
	
	mysqli_query($link,"delete from vaccu_master where id='$vid'");
	
	echo "Deleted";
}

else if($type==9)
{
	$mid=$_POST[mid];
	$mname=mysqli_real_escape_string($link,$_POST['name']);
	
	if($mid==0)
	{
		$id=mysqli_fetch_array(mysqli_query($link,"select max(id)+1 as tot from test_methods"));
		mysqli_query($link,"insert into test_methods values('$id[tot]','$mname')");
		
		echo "Saved";
	}
	else
	{
		mysqli_query($link,"update test_methods set name='$mname' where id='$mid'");
		
		echo "Updated";
	}
}

else if($type==10)
{
	$mid=$_POST[mid];
	
	mysqli_query($link,"delete from test_methods where id='$mid'");
	
	echo "Deleted";
}

else if($type==11)
{
	$rid=$_POST[rid];
	$rname=mysqli_real_escape_string($link,$_POST['name']);
	
	if($rid==0)
	{
		$id=mysqli_fetch_array(mysqli_query($link,"select max(id)+1 as tot from ResultOption"));
		mysqli_query($link,"insert into ResultOption values('$id[tot]','$rname')");
		
		echo $id[tot]."#koushik#Saved";
	}
	else
	{
		mysqli_query($link,"update ResultOption set name='$rname' where id='$rid'");
		
		echo $rid."#koushik#Updated";
	}
}

else if($type==12)
{
	$rid=$_POST['rid'];
	
	$lname=mysqli_fetch_array(mysqli_query($link,"select name from ResultOption where id='$rid'"));
	
	echo "<h4>List Name: $lname[name]</h4>";
	?>
	<div class="row">
	<div class="span5">
		<input type="hidden" id="list_id" value="<?php echo $rid;?>"/>
		<select id="option" style="width:300px">
		<option value="0">--Select Option--</option>
		<?php
		$opt=mysqli_query($link,"select * from Options order by name ");
		while($op=mysqli_fetch_array($opt))
		{
			echo "<option value='$op[id]'>$op[name]</option>";
		}
		?>
		</select>
		<button class="btn btn-mini btn-info" onclick="link_option()"><i class="icon-plus"></i></button>
		<br/><br/>
		<table class="table table-report table-condensed">
			<tr>
				<th>#</th><th>Option</th><th></th>
			</tr>
		<?php
		$l=1;
		$op_l=mysqli_query($link,"select * from ResultOptions where id='$rid'");
		while($opl=mysqli_fetch_array($op_l))
		{
			$oname=mysqli_fetch_array(mysqli_query($link,"select name from Options where id='$opl[optionid]'"));
			?>
			<tr>
				<td><?php echo $l;?></td>
				<td><?php echo $oname[name];?></td>
				<td><button class="btn btn-danger btn-mini" onclick="remove_option(<?php echo $opl[optionid];?>)"><i class="icon-remove"></i></button></td>
			</tr>
			<?php
			$l++;
		}
		?>
		</table>
	</div>
	
	<div class="span5">
		<h4>Add New Option</h4>
		<table class="table">
		<tr>
			<th>
				<i>Enter Option Name</i> <br/>
				<input type="text" style="width:350px" id="new_option"/> <button class="btn btn-info" style="margin-top:-10px" onclick="save_new_option()"><i class="icon-plus"></i></button>
			</th>
		</tr>
		</table>
	</div>
	</div>
	<hr/>
	<div style="text-align:center">
		<button class="btn btn-danger" onclick="$('#mod').click()"><i class="icon-off"></i> Close</button>
	</div>
	<?php
	
}
else if($type==13)
{
	$rid=$_POST[rid];
	
	mysqli_query($link,"delete from ResultOption where id='$rid'");
	mysqli_query($link,"delete from ResultOptions where id='$rid'");
	
	echo "Deleted";
}
else if($type==14)
{
	$rid=$_POST['rid'];
	$opt=$_POST['opt'];
	
	mysqli_query($link,"delete from ResultOptions where id='$rid' and optionid='$opt'");
	
	mysqli_query($link,"insert into ResultOptions(id,optionid) values('$rid','$opt')");
}

else if($type==15)
{
	$rid=$_POST['rid'];
	$opt=$_POST['opt'];
	
	$id=mysqli_fetch_array(mysqli_query($link,"select max(id)+1 as tot from Options"));
	
	if(mysqli_query($link,"insert into Options(id,name) values('$id[tot]','$opt')"))
	{
		mysqli_query($link,"insert into ResultOptions(id,optionid) values('$rid','$id[tot]')");
	}
}
else if($type==16)
{
	$rid=$_POST['rid'];
	$opt=$_POST['opt'];
	
	mysqli_query($link,"delete from ResultOptions where id='$rid' and optionid='$opt'");
}
else if($type==17)
{
	$oid=$_POST[oid];
	$oname=mysqli_real_escape_string($link,$_POST['name']);
	
	if($oid==0)
	{
		$id=mysqli_fetch_array(mysqli_query($link,"select max(id)+1 as tot from Options"));
		mysqli_query($link,"insert into Options values('$id[tot]','$oname')");
		
		echo "Saved";
	}
	else
	{
		mysqli_query($link,"update Options set name='$oname' where id='$oid'");
		
		echo "Updated";
	}
}
else if($type==18)
{
	$oid=$_POST['oid'];
		
	mysqli_query($link,"delete from Options where id='$oid'");
	mysqli_query($link,"delete from ResultOptions where optionid='$oid'");
}
else if($type==19)
{
	$bio=mysqli_query($link,"select * from Parameter_old where ResultOptionID='68' order by Name"); //--Culture--//
	while($bi=mysqli_fetch_array($bio))
	{
		echo "<div class='tab_row' onclick=\"load_paramm('$bi[ID]','$bi[Name]')\">$bi[Name]</div>";
	}
}
else if($type==20)
{
	$id=mysqli_real_escape_string($link,$_POST["anti_bio_id"]);
	$bio=mysqli_real_escape_string($link,$_POST["bio"]);
	
	if($id==0)
	{
		$nid=mysqli_fetch_array(mysqli_query($link,"select max(ID) as id from Parameter_old"));
		$id=$nid["id"]+1;
		
		//mysqli_query($link,"insert into Parameter_old(ID,ResultType,Name,ResultOptionID) values('$id','2','$bio','68')");
		mysqli_query($link,"insert into Parameter_old(`ID`, `ResultType`, `Name`, `ResultOptionID`, `UnitsID`, `sample`, `vaccu`, `method`, `deci_val`, `sub_title`, `instrument`) values('$id','2','$bio','68','0','0','0','0','0','0','0')");
		
		$seq=mysqli_fetch_array(mysqli_query($link,"select max(sequence) as seq from Testparameter where TestId='3374'")); //---3374-Urine C/S--//
		$nseq=$seq["seq"]+1;
		
		$sample=mysqli_fetch_array(mysqli_query($link, "SELECT `SampleId`  FROM `TestSample` WHERE `TestId` = 3374"));
		
		$SampleId=$sample["SampleId"];
		if(!$SampleId){ $SampleId=0; }
		
		$vaccu=mysqli_fetch_array(mysqli_query($link, "SELECT `vac_id`  FROM `test_vaccu` WHERE `testid` = 3374"));
		
		$vac_id=$vaccu["vac_id"];
		if(!$vac_id){ $vac_id=0; }
		
		mysqli_query($link,"insert into Testparameter(`TestId`, `ParamaterId`, `sequence`, `sample`, `vaccu`) values('3374','$id','$nseq','$SampleId','$vac_id')");
		// ------2 - LIST OF CHOICE, 68 - RESULT OPTION 'CULTURE( S,I R) --//
	}
	else
	{
		mysqli_query($link,"UPDATE `Parameter_old` SET `Name`='$bio' WHERE `ID`='$id'");
	}
}
else if($type==21)
{
	$ant_bio=mysqli_real_escape_string($link,$_POST[bio]);
	
	$bio=mysqli_query($link,"select * from Parameter_old where ResultOptionID='68' and Name like '%$ant_bio%' order by Name"); //--Culture--//
	while($bi=mysqli_fetch_array($bio))
	{
		echo "<div class='tab_row' onclick=''>$bi[Name]</div>";
	}
	
}
else if($type==22)
{
	$samp=mysqli_query($link,"select a.* from Options a,ResultOptions b where a.id=b.optionid and b.id=84 order by a.name");
	while($smp=mysqli_fetch_array($samp))
	{
		echo "<div class='tab_row' onclick=\"load_specimenn('$smp[id]','$smp[name]')\">$smp[name]</div>";
	}
}
else if($type==23) //--------Specimen------------//
{
	$id=$_POST['spec_id'];
	$spec=$_POST['spec'];
	
	if($id==0)
	{
		$id=mysqli_fetch_array(mysqli_query($link,"select max(id) as id from Options"));
		
		$nid=$id[id]+1;
		
		mysqli_query($link,"insert into Options(id,name) values('$nid','$spec')");
		mysqli_query($link,"insert into ResultOptions values('84','$nid')");
	}
	else
	{
		mysqli_query($link, "UPDATE `Options` SET `name`='$spec' WHERE `id`='$id'");
	}
}
else if($type==24)
{
	$samp=mysqli_query($link,"select a.* from Options a,ResultOptions b where a.id=b.optionid and b.id=85 order by a.name");
	while($smp=mysqli_fetch_array($samp))
	{
		echo "<div class='tab_row' onclick=\"load_organismm('$smp[id]','$smp[name]')\">$smp[name]</div>";
	}
}
else if($type==25) //--------------Organism---------------//
{
	$id=$_POST['org_id'];
	$org=$_POST['org'];
	
	if($id==0)
	{
		$id=mysqli_fetch_array(mysqli_query($link,"select max(id) as id from Options"));
		
		$nid=$id[id]+1;
		
		mysqli_query($link,"insert into Options(id,name) values('$nid','$org')");
		mysqli_query($link,"insert into ResultOptions values('85','$nid')");
	}
	else
	{
		mysqli_query($link, "UPDATE `Options` SET `name`='$org' WHERE `id`='$id'");
	}
}
else if($type==26) 
{
	$spec=mysqli_real_escape_string($link,$_POST[spec]);
	
	$samp=mysqli_query($link,"select a.* from Options a,ResultOptions b where a.id=b.optionid and b.id=84 and a.name like '%$spec%' order by a.name");
	while($smp=mysqli_fetch_array($samp))
	{
		echo "<div class='tab_row' onclick=''>$smp[name]</div>";
	}	
}
else if($type==27) 
{
	$org=mysqli_real_escape_string($link,$_POST[org]);
	
	$orgnasm=mysqli_query($link,"select a.* from Options a,ResultOptions b where a.id=b.optionid and b.id=85 and a.name like '%$org%' order by a.name");
	while($orgg=mysqli_fetch_array($orgnasm))
	{
		echo "<div class='tab_row' onclick=''>$orgg[name]</div>";
	}	
}
else if($type==28) 
{
	$id=$_POST[id];
	
	if($_POST[typ]=="test")
	{
		$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$id'"));
		
		$summ=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select summary from test_summary where testid='$id'"));
		
		echo $summ[summary];
	}
	else
	{
		$summ=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select summary from test_summary where paramid='$id'"));		
		echo $summ[summary];
	}
}
else if($type==29) 
{
	$testid=$_POST[testid];
	$param=$_POST[param];
	$summ=$_POST[summ];
	
	if($param)
	{
		if($summ=="<p><br></p>")
		{
			mysqli_query($GLOBALS["___mysqli_ston"], "delete from test_summary where paramid='$param'");
		}
		else
		{
			mysqli_query($GLOBALS["___mysqli_ston"], "delete from test_summary where paramid='$param'");
			mysqli_query($GLOBALS["___mysqli_ston"], "insert into test_summary values('','$param','$summ')");
		}
	}
	else
	{
		if($summ=="<p><br></p>")
		{
			mysqli_query($GLOBALS["___mysqli_ston"], "delete from test_summary where testid='$testid'");
		}
		else
		{
			mysqli_query($GLOBALS["___mysqli_ston"], "delete from test_summary where testid='$testid'");
			mysqli_query($GLOBALS["___mysqli_ston"], "insert into test_summary values('$testid','','$summ')");
		}
	}
}

if ($_POST['type'] === 'save_suffix') {
	$testid = mysqli_real_escape_string($link, $_POST['testid']);
	$suffix = mysqli_real_escape_string($link, $_POST['suffix']);
	
	if ($testid && $suffix) {
		$check_sql = "SELECT COUNT(*) as count FROM `barcode_test_suffix` WHERE `testid` = '$testid'";
		$check_result = mysqli_query($link, $check_sql);
		$exists = mysqli_fetch_array($check_result)['count'] > 0;

		if ($exists) {
			$sql = "UPDATE `barcode_test_suffix` SET `suffix` = '$suffix' WHERE `testid` = '$testid'";
		} else {
			$sql = "INSERT INTO `barcode_test_suffix` (`testid`, `suffix`) VALUES ('$testid', '$suffix')";
		}

		if (mysqli_query($link, $sql)) {
			echo "Suffix saved successfully.";
		}
	} else {
		echo "Please provide both test ID and suffix.";
	}
} elseif ($_POST['type'] === 'delete_suffix') {
	$testid = mysqli_real_escape_string($link, $_POST['testid']);
	
	if ($testid) {
		$sql = "DELETE FROM `barcode_test_suffix` WHERE `testid` = '$testid'";
		if (mysqli_query($link, $sql)) {
			echo "Suffix deleted successfully.";
		} 
	} else {
		echo "Test ID not provided.";
	}
}
?>
