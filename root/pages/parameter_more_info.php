<?php
include("../../includes/connection.php");

$instrument_str="display:none;";
if($instrument_wise_normal_range==1)
{
	$instrument_str="";
}

$val=$_POST["val"];
if($val==1)
{
?>
	<button type="button" class="btn btn-danger" data-dismiss="modal" style="position:absolute;right:16px">Close</button>
	<b>Create New</b>
	<input type="text" id="n_unit"/> <input type="button" value="Save" class="btn btn-info" onclick="save_unit()"/>
	<b>Search Unit</b>
	<input type="text" id="search_data" onkeyup="search(this.value)" placeholder="Type unit name">
			
	<table class="table table-bordered table-condensed" id="tblData">
		<tr>
			<th>ID</th><th>Unit Name</th>
		</tr>
		<?php
		$qry=mysqli_query($link, "select * from Units order by ID desc");
		while($q=mysqli_fetch_array($qry))
		{
			?>
			<tr onclick='load_unit(<?php echo $q["ID"];?>,"<?php echo $q["unit_name"];?>")'>
			<?php
			echo "<td>$q[ID]</td><td>$q[unit_name]</td></tr>";
		}
?>
	</table>
<?php
}
else if($val==2)
{
	$id=$_POST['id'];
	$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$id'"));
	//$rOpt=mysqli_fetch_array(mysqli_query($link, "select name from ResultOption where id='$pinfo[ResultOptionID]'"));
	?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th width="50%">Select</th>
			<th>Options</th>
		</tr>
		<tr>
			<td>
				<input type="hidden" id="option_id"/>
				<select id="optionList" class="span4" onchange="show_option(this.value)">
					<option value="0">Select</option>
					<?php
					$opt=mysqli_query($link, "select * from ResultOption order by name");
					while($op=mysqli_fetch_array($opt))
					{
					?>
					<option value="<?php echo $op['id'];?>" <?php if($op['id']==$pinfo['ResultOptionID']){echo "selected";}?>><?php echo $op['name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				<div style="height:150px;overflow:scroll;overflow-x:hidden" id="option_val"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				<input type="button" value="Select" class="btn btn-info" onclick="save_option()"/>
				<input type="button" value="Close" class="btn btn-danger" onclick="$('#mod2').click()"/>
			</td>
		</tr>
	</table>
	<?php
	if($pinfo['ResultOptionID'])
	{
		echo "<script>show_option('".$pinfo['ResultOptionID']."')</script>";
	}
}
else if($val==222)
{
	$id=$_POST[id];
	$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from Parameter where ID='$id'"));
?>
<br/>
	<table class="table table-bordered table-condensed">
	<tr>
		<td rowspan="2">
			<input type="hidden" id="option_id"/>
			<div style="height:450px;overflow:scroll;overflow-x:hidden;padding:10px;scale:0.5">
			<?php
				$opt=mysqli_query($link, "select * from ResultOption order by name");
				while($op=mysqli_fetch_array($opt))
				{
					if($op[id]==$pinfo[ResultOptionID]){ $col="background-color:#cccccc;font-weight:bold";} else { $col="";}
					echo "<div id='$op[id]' style='cursor:pointer;$col' onclick='show_option($op[id])'>$op[name]</div>";
				}
			?>
			</div>
		</td>
		<td>
			<div style="height:200px;overflow:scroll;overflow-x:hidden" id="option_val">
				<?php
				$res_op=mysqli_query($link, "select * from ResultOptions where id='$pinfo[ResultOptionID]'");
				while($res=mysqli_fetch_array($res_op))
				{
					$nm=mysqli_fetch_array(mysqli_query($link, "select name from Options where id='$res[optionid]'"));
					//echo "<div id='$res[optionid]' class='options' onclick='$(this).remove()'>$nm[name]</div>";
					echo "<div id='$res[optionid]' class='options'>$nm[name]</div>";
				}
				?>
			</div>	
		</td>
	</tr>
	<tr>
		<td>
			<div style="height:250px;overflow:scroll;overflow-x:hidden">
			<?php
				$opts=mysqli_query($link, "select * from Options order by name");
				while($ops=mysqli_fetch_array($opts))
				{
					echo "<div id='$ops[id]'>$ops[name]</div>";
				}
			?>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center">
			<input type="button" value="Map to paremeter" class="btn btn-info" onclick="save_option()"/>
			<input type="button" value="Close" class="btn btn-danger" onclick="$('#mod2').click()"/>
		</td>
	</tr>
	</table>
<?php	
}
else if($val==3)
{
	$id=$_POST["id"];

//$pinfo=mysql_fetch_array(mysql_query("select name from Parameter where ID='$id'"));
?>
	<div style="padding:10px" align="center">
		
		<div id="display_range">
		
		<button id="new_range" value="Add New" class="btn btn-info" onclick="normal_add(<?php echo $id;?>)"><i class="icon-plus"></i> Add New</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-off"></i> Close</button><br/>
	<div align="left">
		<button id="show_norm" class="btn btn-default btn-mini" onclick="normal_status_show(this.id)">Show Inactive Range</button>
<!--
		<span onclick="normal_status_show()"> <input type="checkbox" id="normal_stat_show"/> Show Inactive Ranges </span>
-->
	</div>	
	<table class="table table-bordered table-condensed">
		<th>#</th><th style="<?php echo $instrument_str; ?>">Instrument</th><th>Depends On</th><th>Age From</th><th>Age To</th><th>Sex</th><th>Min Value</th><th>Max Value</th><th>Display</th><th></th>
		
		<?php
			$i=1;
			$nomr=mysqli_query($link, "select * from parameter_normal_check where parameter_id='$id'");
			while($n=mysqli_fetch_array($nomr))
			{
				$dep=mysqli_fetch_array(mysqli_query($link, "select name from DependentType where id='$n[dep_id]'"));
				
				$instrument_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_instrument_master` WHERE `id`='$n[instrument_id]'"));
				
				if($n[dep_id]==1 || $n[dep_id]==2)
				{
					if($n[age_from]<30)
					{
							$age_f=$n[age_from]." Days";
					}
					else if($n[age_from]>=30 && $n[age_from]<365)
					{
							$age_f=round($n[age_from]/30);
							$age_f=$age_f." Months";
					}
					else if($n[age_from]>=365)
					{
							$age_f=round($n[age_from]/365);
							$age_f=$age_f." Years";
					}
					
					
					if($n[age_to]<30)
					{
							$age_t=$n[age_to]." Days";
					}
					else if($n[age_to]>=30 && $n[age_to]<365)
					{
							$age_t=round($n[age_to]/30);
							$age_t=$age_t." Months";
					}
					else if($n[age_to]>=365)
					{
							$age_t=round($n[age_to]/365);
							$age_t=$age_t." Years";
					}	
				}
				
				?>
				<tr class="norm_stat_<?php echo $n["status"];?>">
					<td><?php echo $i;?></td>
					<td style="<?php echo $instrument_str; ?>"><?php echo $instrument_info["name"];?></td>
					<td><?php echo $dep["name"];?></td>
					<td><?php echo $age_f;?></td>
					<td><?php echo $age_t;?></td>
					<td><?php if($n["sex"]!='0'){ echo $n["sex"];}?></td>
					<td><?php echo $n["value_from"];?></td>
					<td><?php echo $n["value_to"];?></td>
					<?php
						$nr=nl2br($n["normal_range"]);
						/*
						if(!$n[value_from] && !$n[value_to])
						{
								$nr=nl2br($n[normal_range]);
						}
						else
						{
								$nr=$n[value_from]." - ".$n[value_to];
						}
						*/
					?>
					<td><?php echo $nr;?></td>
					<td>
						<?php
						$n_stat="Inactive";
						if($n["status"]==1)
						{
							$n_stat="Active";
						}
						?>
						<select id="norm_opt" class="span2" onchange="normal_update_opt(<?php echo $n[slno];?>,<?php echo $id;?>,this)">
							<option value="0">--Select Option--</option>
							<option value="1">Update</option>
							<option value="2"><?php echo $n_stat;?></option>
							<option value="3">Remove</option>
						</select>
						<!--<input type="button" onclick="update_range(<?php echo $n[slno];?>,<?php echo $id;?>)" style="cursor:pointer" value="Update" class="btn btn-info"/></td>
						<input type="button" onclick="remove_range(<?php echo $n[slno];?>,<?php echo $id;?>)" style="cursor:pointer" value="Remove" class="btn btn-info"/>
						-->
					</td>
				</tr>
				
				<?php	
				$i++;
			}
		?>
		
	</table>	
	</div>
	
	<div id="add_range" style="display:none">
	
	
	</div>
	</div>
<?php
}
else if($val==4)
{
	$id=$_POST["id"];
	
	$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$id'"));
	
	echo "<h4><b>Set Formula for $pinfo[Name]</b> </h4><hr>";
	
	?>
	
	<table class="table table-condensed table-bordered table-stripped">
	<tr>
		<th>Select Parameter <br/>
			<select id="parm">
			<option value="0">--Select--</option>
			<?php
				$par=mysqli_query($link, "select * from Parameter_old where ID!='$id' order by Name");
				while($p=mysqli_fetch_array($par))
				{
					$pinfo1=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$p[ID]'"));
					echo "<option value='$p[ID]'>$p[ID]-$pinfo1[Name]</option>";
				}
			?>
			</select>
			<button id="add_p" value="Add" class="btn btn-primary btn-sm" onclick="add_element(this.id)">Add</button>
		</th>
	
		<th>Add Operator <br/>
		
			<input type="text" id="opr" onkeyup="check_op(this)"/>
			<button id="add_op" value="Add" class="btn btn-primary btn-sm" onclick="add_element(this.id)">Add</button></th>
	
		<th>Add Numeric Value <br/>
		
			<input type="text" id="num" onkeyup="check_num(this)"/>
			<button id="add_num" value="Add" class="btn btn-primary btn-sm" onclick="add_element(this.id)">Add</button></th>
	</tr>
	<tr>
		<th colspan="3" style="text-align:center">
			<b>Formula</b><br/>
			<div style="min-height:50px;max-height:60px;overflow-y:scroll;" id="formula_text">
			
			<?php
				$dec=0;
				$form=mysqli_fetch_array(mysqli_query($link,"select * from parameter_formula where ParameterID='$id'"));
				
				if($form["formula"])
				{
					$formula="";
					$val=explode("@",$form["formula"]);
					
					foreach($val as $v)
					{
						$chk_par=explode("p",$v);
						if($chk_par[1])
						{
							//$pn=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$chk_par[1]'"));
							//$formula.="<input type='text' value='$pn[ID]-$pn[Name]' class='param' readonly/>";
							
							ob_start();
							?>
							<select class="formula" id="" name="param">
							<option value="0">--Select--</option>
							<?php
								$par1=mysqli_query($link, "select * from Parameter_old where ID!='$id' order by Name");
								while($p1=mysqli_fetch_array($par1))
								{
									if($p1[ID]==$chk_par[1]) { $sel="Selected='selected'";} else { $sel=""; }
									echo "<option value='$p1[ID]' $sel>$p1[ID]-$p1[Name]</option>";
								}
							?>
							</select>
							<?php
							$formula.=ob_get_clean();
						}
						else
						{
							if(!is_numeric($v))
							{
								$formula.="<input type='text' value='$v' name='operator' class='formula span1' maxlength='1' size='1'/>";
								
							}
							else
							{
								$formula.="<input type='text' value='$v' name='numeric' class='formula span1' size='3'/>";
							}
						}
						
					}
					
					echo $formula;
					$dec=$form[res_dec];
				}
			?>
			
			</div>
			
			
			Value After Decimal Point <input type="text" id="dec" value="<?php echo $dec;?>" size="3" /> <br/><br/>
			
						
			<button id="clear"  class="btn btn-primary btn-sm" onclick="$('#formula_text').html('');$('[value=Add]').attr('disabled',false)">Clear</button>
			<button id="save"  class="btn btn-success btn-sm" onclick="save_formula(<?php echo $id;?>)">Save</button>
			<button id="delete"  class="btn btn-danger btn-sm" onclick="delete_formula(<?php echo $id;?>)">Delete</button>
		</th>
	</tr>
	</table>
	<?php
	
?><hr/> <div align="center"> <input type="button" value="Close" class="btn btn-danger" onclick="$('#mod2').click()"/> </div><?php

}
else if($val==5)
{
	
	$id=$_POST['id'];
	$res=mysqli_fetch_assoc(mysqli_query($link,"SELECT DISTINCT `paramid` FROM `testresults` WHERE `paramid`='$id'"));
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT DISTINCT `TestId` FROM `Testparameter` WHERE `ParamaterId`='$id'"));
	if($res || $det)
	{
		echo "Parameter in use";
	}
	else
	{
		mysqli_query($link, "delete from Parameter_old where ID='$id'");
		echo "Deleted";
	}
}
?>
