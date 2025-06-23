<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

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
	$group_id="194";
	
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$user=$_POST['usr'];
	
	$q="SELECT * FROM `ipd_pat_service_details` WHERE `group_id` ='$group_id' ";
	
	if(strlen($uhid)>2)
	{
		$q.=" AND `patient_id` LIKE '$uhid%' ";
	}
	if(strlen($ipd)>2)
	{
		$q.=" AND `ipd_id` LIKE '$ipd%' ";
	}
	
	if(strlen($name)>2)
	{
		$q.=" AND `patient_id` IN (SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$name%') ";
	}
	if($dat)
	{
		$q.=" AND `date`='$dat'";
	}
	$q.=" ORDER BY `slno` DESC limit 0,100";
	//echo $q;
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>UHID</th>
				<th>IPD ID</th>
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
					$tr_style="style='cursor:pointer;background-color: #baffbb;'";
				}else
				{
					$tr_style="style='cursor:pointer;'";
				}
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['ipd_id'];?>','<?php echo $group_id; ?>','<?php echo $r['service_id'];?>')" <?php echo $tr_style; ?> >
					<td><?php echo $r['patient_id'];?></td>
					<td><?php echo $r['ipd_id'];?></td>
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
	$group_id=$_POST['group_id'];
	
	$group_id = array();
	array_push($group_id, 194, 195);
	$group_id = join(',',$group_id);
	
	$pat_serv=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$group_id' AND `service_id`='$service_id'"));
	if($pat_serv)
	{
		$pat_serv=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$group_id' AND `service_id`='$service_id'"));
		
		$service_amount=$pat_serv['amount'];
?>
		<input type="hidden" id="serv_slno" value="<?php echo $pat_serv['slno'];?>" />
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
						$q=mysqli_query($link,"SELECT `charge_id`,`charge_name` FROM `charge_master` WHERE `group_id` NOT IN ($group_id) ORDER BY `charge_name` ASC");
						while($r=mysqli_fetch_array($q))
						{
					?>
							<option value="<?php echo $r['charge_id'];?>"><?php echo $r['charge_name'];?></option>
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
			$qq=mysqli_query($link,"SELECT * FROM `ipd_pat_minor_service_details` WHERE `serv_slno`='$pat_serv[slno]'");
			while($rr=mysqli_fetch_array($qq))
			{
				$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$rr[emp_id]'"));
				$amount=explode(".",$rr['amount']);
				$empp="";
				if($rr['emp_id']>0)
				{
					$empp=$rr['emp_id'];
				}
			?>
			<tr class="all_res"><td><input type="hidden" value="<?php echo $rr['service_id'];?>" /><?php echo $rr['service_text'];?></td><td><input type="hidden" value="<?php echo $empp;?>" /><?php echo $emp['name'];?></td><td><input type="text" class="span2" onkeyup="sum_amt(this)" value="<?php echo $amount[0];?>" /></td><td style="text-align:center;"><i class="icon-remove  icon-large" style="color:#aa0000;cursor:pointer;" onclick="$(this).parent().parent().remove();check_all();"></i></td></tr>
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
	$service_id=$_POST['service_id'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT `doc_link` FROM `charge_master` WHERE `charge_id`='$service_id'"));
	if($v['doc_link']>0)
	{
?>
	<select id="emp" class="span3">
		<option value="0">Select</option>
<?php
		$q=mysqli_query($link,"SELECT `emp_id`,`Name` FROM `consultant_doctor_master` WHERE `Name`!='' ORDER BY `Name`");
		while($r=mysqli_fetch_array($q))
		{
?>
		<option value="<?php echo $r['emp_id'];?>"><?php echo $r['Name'];?></option>
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
	$service_id=$_POST['res'];
	$emp_id=$_POST['emp'];
	
	$rs=mysqli_fetch_array(mysqli_query($link,"SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$service_id'"));
	$em=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$emp_id'"));
	echo $rs['charge_name']."@@".$em['name'];
}
if($_POST["type"]==5)
{
	$all=$_POST['all'];
	$serv_slno=$_POST['serv_slno'];
	$usr=$_POST['usr'];
	
	$pat_serv=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `slno`='$serv_slno'"));
	mysqli_query($link,"DELETE FROM `ipd_pat_minor_service_details` WHERE `serv_slno`='$serv_slno'");
	mysqli_query($link,"DELETE FROM `doctor_service_done` WHERE `rel_slno`='$serv_slno'");
	
	$al=explode("#@#",$all);
	foreach($al as $a)
	{
		$v=explode("@@",$a);
		$res=$v[0];
		$emp=$v[1];
		$amt=$v[2];
		if($res)
		{
			$charge_master=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `charge_master` WHERE `charge_id`='$res'"));
			$serv_txt=$charge_master['charge_name'];
			
			mysqli_query($link,"INSERT INTO `ipd_pat_minor_service_details`(`serv_slno`, `patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `emp_id`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$serv_slno','$pat_serv[patient_id]','$pat_serv[ipd_id]','$charge_master[group_id]','$res','$serv_txt','$emp','1','$amt','$amt','0','$usr','$time','$date','$pat_serv[bed_id]')");
			
			if($emp>0)
			{
				$doc=mysqli_fetch_array(mysqli_query($link," SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$emp' "));
				
				mysqli_query($link," INSERT INTO `doctor_service_done`(`patient_id`, `ipd_id`, `service_id`, `consultantdoctorid`, `user`, `date`, `time`, `rel_slno`, `schedule_id`) VALUES ('$pat_serv[patient_id]','$pat_serv[ipd_id]','$res','$doc[consultantdoctorid]','$usr','$pat_serv[date]','$pat_serv[time]','$serv_slno','0') ");
			}
		}
	}
	echo "Saved";
}
?>
