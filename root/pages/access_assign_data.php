<?php
session_start();
include("../../includes/connection.php");

$p_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$_SESSION[emp_id]' "));

if($_POST["type"]=="level_access")
{
	$level=$_POST["level"];
	if($level>0)
	{
		$z=1;
		
		$str_qry="select * from menu_header_master where name!=''";
		
		if($p_info["levelid"]==28)
		{
			$str_qry.=" AND main_menu_id IN('1')";
		}
		
		$str_qry.=" order by name";
		
		$qry=mysqli_query($link, $str_qry);
		while($q=mysqli_fetch_array($qry))
		{
			$num_m=mysqli_num_rows(mysqli_query($link, "select * from menu_master where header='$q[id]' and hidden='0' "));
			if($num_m>0)
			{
				$tot_chk_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `menu_access_detail` a, `menu_master` b WHERE a.`par_id`=b.`par_id` AND a.`levelid`='$level' AND b.`header`='$q[id]' and b.hidden='0' "));
				
				$header_check="";
				if($tot_chk_num==$num_m)
				{
					$header_check="checked";
				}
		?>	
			<fieldset>
				<b><u><?php echo $q['name'];?></u></b>
				<table class="table table-bordered table-condensed">
					<tr>
						<td colspan="4">
							<label><input type="checkbox" id="chk_val<?php echo $z; ?>" onClick="checkall('<?php echo $z; ?>')" <?php echo $header_check; ?> > Check All</label>
							<input type="hidden" id="chk_val_num<?php echo $z; ?>" value="<?php echo $num_m; ?>">
						</td>
					</tr>
				<?php
				$fp=mysqli_query($link, "select * from menu_master where header='$q[id]' and hidden='0'");
				$i=1;
				while($f=mysqli_fetch_array($fp))
				{
					if($i==1){echo "<tr>";}
					$l=$_POST['level'];
					$ch=mysqli_num_rows(mysqli_query($link,"select * from menu_access_detail where levelid='$l' and par_id='$f[par_id]'"));
					if($ch>0)
					$chk="checked";
					else
					$chk="";
					echo "<td><label><input type='checkbox' id='cb[]' name='chk_name$z' onclick='chk_name_click($z)' class='chk' value='$f[par_id]' $chk/> $f[par_name]</label></td>";
					if($i==4)
					{
						echo "</tr>";
						$i=1;
					}
					else
					{
						$i++;
					}
				}
				?>
				</table>
			</fieldset>
		<?php
			}
			$z++;
		}
?>
	<div id="butts" align="center">
		<input type="button" value="Assign Access" id="acc" name="acc" class="btn btn-success" onClick="save_acc('level')" disabled />
	</div>
<?php
	}
}
if($_POST["type"]=="level_access_user")
{
	$emp_id=$_POST["level_user"];
	if($emp_id>0)
	{
		$z=1;
		
		$str_qry="select * from menu_header_master where name!=''";
		
		if($p_info["levelid"]==28)
		{
			$str_qry.=" AND main_menu_id IN('1')";
		}
		
		$str_qry.=" order by name";
		
		$qry=mysqli_query($link, $str_qry);
		
		//$qry=mysqli_query($link, "select * from menu_header_master order by name");
		while($q=mysqli_fetch_array($qry))
		{
			$num_m=mysqli_num_rows(mysqli_query($link, "select * from menu_master where header='$q[id]' and hidden='0'"));
			if($num_m>0)
			{
				$tot_chk_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `menu_access_detail_user` a, `menu_master` b WHERE a.`par_id`=b.`par_id` AND a.`emp_id`='$emp_id' AND b.`header`='$q[id]' and b.hidden='0' "));
				
				$header_check="";
				if($tot_chk_num==$num_m)
				{
					$header_check="checked";
				}
		?>	
			<fieldset>
				<b><u><?php echo $q['name'];?></u></b>
				<table class="table table-bordered table-condensed">
					<tr>
						<td colspan="4">
							<label><input type="checkbox" id="chk_val<?php echo $z; ?>" onClick="checkall('<?php echo $z; ?>')" <?php echo $header_check; ?> > Check All</label>
							<input type="hidden" id="chk_val_num<?php echo $z; ?>" value="<?php echo $num_m; ?>">
						</td>
					</tr>
				<?php
				$fp=mysqli_query($link, "select * from menu_master where header='$q[id]' and hidden='0'");
				$i=1;
				while($f=mysqli_fetch_array($fp))
				{
					if($i==1){echo "<tr>";}
					$ch=mysqli_num_rows(mysqli_query($link,"select * from menu_access_detail_user where emp_id='$emp_id' and par_id='$f[par_id]'"));
					if($ch>0)
					$chk="checked";
					else
					$chk="";
					echo "<td><label><input type='checkbox' id='cb[]' name='chk_name$z' onclick='chk_name_click($z)' class='chk' value='$f[par_id]' $chk/> $f[par_name]</label></td>";
					if($i==4)
					{
						echo "</tr>";
						$i=1;
					}
					else
					{
						$i++;
					}
				}
				?>
				</table>
			</fieldset>
		<?php
			}
			$z++;
		}
?>
	<div id="butts" align="center">
		<input type="button" value="Assign Access" id="acc" name="acc" class="btn btn-success" onClick="save_acc('user')" disabled />
	</div>
<?php
	}
}

if($_POST["type"]=="load_access_level_data")
{
	$emp_id=$_POST["level_user"];
	$emp=mysqli_fetch_assoc(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$emp_id'"));
	$level=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `level_master` WHERE `levelid`='$emp[levelid]'"));
	$val="<h4>Access Level : ".$level['name']."</h4>";
	$q=mysqli_query($link,"SELECT DISTINCT a.`header`,c.`name` FROM `menu_master` a,`menu_access_detail` b, `menu_header_master` c WHERE a.`par_id`=b.`par_id` AND a.`header`=c.`id` AND a.`hidden`='0' AND b.`levelid`='$emp[levelid]' ORDER BY c.`name` ");
	while($r=mysqli_fetch_assoc($q))
	{
		$val.="<p><b><u>".$r['name']."</u></b><br/>";
		$menus="";
		$qq=mysqli_query($link,"SELECT a.`par_name` FROM `menu_master` a,`menu_access_detail` b WHERE a.`par_id`=b.`par_id` AND a.`header`='$r[header]' AND a.`hidden`='0' AND b.`levelid`='$emp[levelid]' ORDER BY a.`par_name`");
		while($rr=mysqli_fetch_assoc($qq))
		{
			if($menus)
			{
				$menus.="<br/><i class='icon-ok-sign'></i> <i>".$rr['par_name']."</i>";
			}
			else
			{
				$menus="<i class='icon-ok-sign'></i> <i>".$rr['par_name']."</i>";
			}
		}
		$val.=$menus."<hr class='menu_hr' /></p>";
	}
	echo $val;
}
?>
