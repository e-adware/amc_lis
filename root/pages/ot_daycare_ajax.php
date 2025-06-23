<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");
$time=date("H:i:s");

//---------------------------------------------------------------------------------------------------//
function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
//-------------------------------------------------------------------------------------------------//

if($_POST["type"]==1)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	//$q="SELECT DISTINCT a.`patient_id`,a.`ipd_id`,a.`scheduled`,a.`schedule_id` FROM `ot_book` a, `ot_schedule` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.ipd_id AND b.`leaved`='0' ORDER BY a.`ot_date` DESC";
	
	//~ $q="SELECT * FROM `uhid_and_opdid` WHERE `type`='4' AND `date`='$date'";
	//~ if($uhid)
	//~ {
		//~ $q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `type`='4'";
	//~ }
	//~ if($ipd)
	//~ {
		//~ $q="SELECT * FROM `uhid_and_opdid` WHERE `ipd_id`='$ipd' AND `type`='4'";
	//~ }
	//~ if($name)
	//~ {
		//~ $q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$name%') AND `type`='4'";
	//~ }
	//~ if($dat)
	//~ {
		//~ $q="SELECT * FROM `uhid_and_opdid` WHERE `type`='4' AND `date`='$dat'";
	//~ }
	
	$q="SELECT * FROM `doctor_service_done` WHERE `schedule_id`='0' AND `date`='$date'";
	if($uhid)
	{
		$q="SELECT * FROM `doctor_service_done` WHERE `schedule_id`='0' AND `patient_id`='$uhid' ";
	}
	if($ipd)
	{
		$q="SELECT * FROM `doctor_service_done` WHERE `schedule_id`='0' AND `ipd_id`='$ipd' ";
	}
	if($name)
	{
		$q="SELECT * FROM `doctor_service_done` WHERE `schedule_id`='0' AND `patient_id` IN (SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$name%') ";
	}
	if($dat)
	{
		$q="SELECT * FROM `doctor_service_done` WHERE `schedule_id`='0' AND `date`='$dat'";
	}
	$q.=" ORDER BY `date` DESC";
	//echo $q;
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>UHID</th>
				<th>OPD ID</th>
				<th>Name</th>
				<th>Sex</th>
				<th>Service</th>
				<th>Amount</th>
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
				
				$service=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$r[patient_id]' AND `ipd_id`='$r[ipd_id]' AND `service_id`='$r[service_id]' "));
				
				$check_map=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_minor_service_details` WHERE `serv_slno`='$r[slno]' "));
				if($check_map)
				{
					$tr_style="style='cursor:pointer;background-color: antiquewhite;'";
				}else
				{
					$tr_style="style='cursor:pointer;'";
				}
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['ipd_id'];?>','1','<?php echo $r['service_id'];?>')" <?php echo $tr_style; ?> >
					<td><?php echo $r['patient_id'];?></td>
					<td><?php echo $r['ipd_id']." ".$r['service_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $service['service_text'];?></td>
					<td><?php echo $service['amount'];?></td>
				</tr>
			<?php
			}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]==2)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$service_id=$_POST['service_id'];
	
	$serv=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `doctor_service_done` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$service_id'"));
	if($serv)
	{
		$pat_serv=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$service_id'"));
		
		$service_amount=$pat_serv['amount'];
?>
		<input type="hidden" id="serv_slno" value="<?php echo $serv['slno'];?>" />
		<input type="hidden" id="amount" value="<?php echo $service_amount;?>" />
		<table class="table table-condensed table-bordered">
			<tr>
				<th><?php echo $pat_serv['service_text'];?></th>
				<th colspan="2"><span id="err_msg" style="color: #ff0000;"></span></th>
				<th><?php echo $service_amount;?></th>
			</tr>
			<tr>
				<td>
					<select id="res_id" class="span3" onchange="load_emp_list(this.id)">
						<option value="0">Select</option>						
						<?php
						$q=mysqli_query($link,"SELECT * FROM `ot_type_master` ORDER BY `seq`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['type_id'];?>"><?php echo $r['type'];?></option>
						<?php
						}
						?>
					</select>
				</td>
				<td colspan="2" id="emp_list">
					
				</td>
				<td>
					<button type="button" class="btn btn-primary" onclick="add_res_row()"><i class="icon-plus icon-large"></i> Add</button>
				</td>
			</tr>
			<?php
			$tot_amt=0;
			$qq=mysqli_query($link,"SELECT * FROM `ipd_pat_minor_service_details` WHERE `serv_slno`='$serv[slno]'");
			while($rr=mysqli_fetch_array($qq))
			{
				$typ=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$rr[service_id]'"));
				$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$rr[emp_id]'"));
				$amount=explode(".",$rr['amount']);
				$empp="";
				if($rr['emp_id']>0)
				{
					$empp=$rr['emp_id'];
				}
			?>
			<tr class="all_res"><td><input type="hidden" value="<?php echo $rr['service_id'];?>" /><?php echo $typ['type'];?></td><td><input type="hidden" value="<?php echo $empp;?>" /><?php echo $emp['name'];?></td><td><input type="text" class="span2" onkeyup="sum_amt(this)" value="<?php echo $amount[0];?>" /></td><td style="text-align:center;"><i class="icon-remove  icon-large" style="color:#aa0000;cursor:pointer;" onclick="$(this).parent().parent().remove();check_all();"></i></td></tr>
			<?php
			$tot_amt+=$amount[0];
			}
			?>
			<tr id="last_tr">
				<th style="text-align:right;">Total</th>
				<th></th>
				<th><input type="text" id="tot_val" class="span2" value="<?php echo $tot_amt;?>" readonly="readonly" /></th>
				<th><button type="button" class="btn btn-info" id="btn_done" onclick="save_all_res()">Save</button></th>
			</tr>
		</table>
		<?php
	}
}

if($_POST["type"]==3)
{
	$res=$_POST['res'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT `link` FROM `ot_type_master` WHERE `type_id`='$res'"));
	if($v['link']>0)
	{
	?>
	<select id="emp" class="span3">
		<option value="0">Select</option>
		<?php
		$q=mysqli_query($link,"SELECT * FROM `ot_resource_link` WHERE `type_id`='$res'");
		while($r=mysqli_fetch_array($q))
		{
			$em=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[emp_id]'"));
		?>
		<option value="<?php echo $r['emp_id'];?>"><?php echo $em['name'];?></option>
		<?php
		}
		?>
	</select>
	<?php
	}
	else
	{
		?>
		<input type="hidden" id="emp" value="" class="span1" />
		<!--<input type="text" id="amt" class="span1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" value="0" placeholder="Amount" />-->
		<?php
	}
}

if($_POST["type"]==4)
{
	$res=$_POST['res'];
	$emp=$_POST['emp'];
	$rs=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$res'"));
	$em=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$emp'"));
	echo $rs['type']."@@".$em['name'];
}

if($_POST["type"]==5)
{
	$all=$_POST['all'];
	$serv_slno=$_POST['serv_slno'];
	$usr=$_POST['usr'];
	
	$doc_serv=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `doctor_service_done` WHERE `slno`='$serv_slno'"));
	
	$sv=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `slno`='$doc_serv[rel_slno]'"));
	$al=explode("#@#",$all);
	mysqli_query($link,"DELETE FROM `ipd_pat_minor_service_details` WHERE `serv_slno`='$serv_slno'");
	foreach($al as $a)
	{
		$v=explode("@@",$a);
		$res=$v[0];
		$emp=$v[1];
		$amt=$v[2];
		if($res)
		{
			//echo "R-$res E-$emp A-$amt==";
			$typ=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$res'"));
			$serv_txt=$typ['type']." Charge";
			mysqli_query($link,"INSERT INTO `ipd_pat_minor_service_details`(`serv_slno`, `patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `emp_id`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$serv_slno','$sv[patient_id]','$sv[ipd_id]','$sv[group_id]','$res','$serv_txt','$emp','1','$amt','$amt','0','$usr','$doc_serv[time]','$doc_serv[date]','$sv[bed_id]')");
		}
	}
	echo "Saved";
}

if($_POST["type"]==999)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
}

?>
