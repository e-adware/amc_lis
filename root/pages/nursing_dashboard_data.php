<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_info["branch_id"];

$date=date("Y-m-d");

if($_POST["type"]=="pat_centreno_change")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	$centreno=$_POST['centreno'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	if(mysqli_query($link, "UPDATE `uhid_and_opdid` SET `center_no`='$centreno' WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"))
	{
		mysqli_query($link, "INSERT INTO `pat_centre_change_record`(`patient_id`, `ipd_id`, `centreno_old`, `centreno_new`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$pat_reg[center_no]','$centreno','$c_user','$date','$time')");
		
		$delivery_det=mysqli_fetch_array(mysqli_query($link," SELECT `baby_uhid`,`baby_ipd_id` FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
		if($delivery_det)
		{
			mysqli_query($link, "UPDATE `uhid_and_opdid` SET `center_no`='$centreno' WHERE `patient_id`='$delivery_det[patient_id]' AND `ipd_id`='$delivery_det[ipd_id]'");
		}
		
		echo "Updated";
	}
	else
	{
		echo "Failed, try again later.";
	}
}

if($_POST["type"]=="pat_ipd_room_status")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	$ipd_pat_edit_bed=$_POST['ipd_pat_edit_bed'];
	
	$qq1=mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$qq2=mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `bed_id` NOT IN (SELECT `bed_id` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')");
	$n1=mysqli_num_rows($qq1);
	$n2=mysqli_num_rows($qq2);
	
	$pat_bed_det=mysqli_fetch_array($qq1);
	
	if($ipd_pat_edit_bed>2) // 3 = Guardian Bed
	{
		$n1=0;
		$n2=0;
		
		$pat_bed_det="";
	}
	
	$bed_transfer_dis="";
	if($pat_bed_det["bed_id"]==1000)
	{
		$bed_transfer_dis="disabled='disabled'";
	}
	
	$dis_det=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($dis_det>0)
	{
		$btndis="disabled='disabled'";
		$btnname="Discharged";
	}
	else
	{
		$btndis="";
		$btnname="Bed Transfer";
	}
	
	if($n1==0 && $n2>0)
	{
		echo '<button type="button" class="btn btn-info" onclick="ipd_pat_bed_alloc()"><i class="icon icon-check"></i> Accept</button>';
	}
	else if($n1>0 && $n2>0)
	{
		$qry=mysqli_query($link,"SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` ASC");
		$num=mysqli_num_rows($qry);
		if($num>0)
		{
			?>
			<table class="table table-condensed table-bordered">
				<tr>
					<th>Ward</th>
					<th>Bed No</th>
					<th>Occupied On</th>
					<th>Released On</th>
					<th>User</th>
				</tr>
			<?php
			$zz=1;
			while($res=mysqli_fetch_array($qry))
			{
				$ldt=mysqli_fetch_array(mysqli_query($link,"SELECT `time`,`date`,`user` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `ward_id`='$res[ward_id]' AND `bed_id`='$res[bed_id]' AND `alloc_type`='0' AND `slno`>'$res[slno]' ORDER BY `slno` ASC LIMIT 1"));
				$wd=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$res[ward_id]'"));
				$bd=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no`,`charges` FROM `bed_master` WHERE `bed_id`='$res[bed_id]'"));
				$emp_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$ldt[user]'"));
				//~ if($ldt['date']=="")
				if($num==$zz)
				{
					//$dt=date("d-M-Y");
					$dt="Still Occupied";
				}
				else
				{
					$dt=date("d-m-Y",strtotime($ldt['date']))." / ".date("h:i A",strtotime($ldt['time']));
				}
				?>
				<tr>
					<td><?php echo $wd['name'];?></td>
					<td><?php echo $bd['bed_no'];?></td>
					<td><?php echo date("d-m-Y",strtotime($res['date']))." / ".date("h:i A",strtotime($res['time']));?></td>
					<td><?php echo $dt;?></td>
					<td><?php echo $emp_info["name"];?></td>
				</tr>
		<?php
				$zz++;
			}
			?>
			</table>
			<button type="type" class="btn btn-info" onclick="nursing_bed_transfer(2)" <?php echo $btndis." ".$bed_transfer_dis; ?>><i class="icon icon-forward"></i> <?php echo $btnname; ?></button>
			<?php
			if($n2>0)
			{
				$f=mysqli_fetch_array($qq2);
				$w=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$f[ward_id]'"));
				$b=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no` FROM `bed_master` WHERE `bed_id`='$f[bed_id]'"));
				$ward=$w['name'];
				$bed=$b['bed_no'];
				$style="";
			}
			else
			{
				$ward="";
				$bed="";
				$style="display:none;";
			}
			?>
			<div id="bed_info" <?php echo $style; ?>>
				<b>Selected</b><br/>Ward: <?php echo $ward; ?><br/>Bed No: <?php echo $bed; ?>
				<input type='hidden' id='ward_id' value="<?php echo $f['ward_id']; ?>" />
				<input type='hidden' id='bed_id' value="<?php echo $f['bed_id']; ?>" />
			</div>
			<div id="bed_btn_info" <?php echo $style; ?>>
				<input type="text" class="datepicker" id="bed_transfer_date" value="<?php echo date("Y-m-d"); ?>"><br>
				<button type='button' class='btn btn-primary' onclick='bed_assign_ok()'>Update</button>
				<button type='button' class='btn btn-danger' onclick='clr_bed_assign()'>Cancel</button>
			</div>
			<?php
		}
	}
	else
	{
		$qry=mysqli_query($link,"SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` ASC");
		$num=mysqli_num_rows($qry);
		if($num>0)
		{
			?>
			<table class="table table-condensed table-bordered">
				<tr>
					<th>Ward</th>
					<th>Bed No</th>
					<th>Occupied On</th>
					<th>Released On</th>
					<th>User</th>
				</tr>
			<?php
			$zz=1;
			while($res=mysqli_fetch_array($qry))
			{
				$ldt=mysqli_fetch_array(mysqli_query($link,"SELECT `time`,`date`,`user` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `ward_id`='$res[ward_id]' AND `bed_id`='$res[bed_id]' AND `alloc_type`='1' ORDER BY `slno` ASC LIMIT 0,1"));
				
				$wd=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$res[ward_id]'"));
				$bd=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no`,`charges` FROM `bed_master` WHERE `bed_id`='$res[bed_id]'"));
				$emp_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$ldt[user]'"));
				
				//~ if($ldt['date']=="")
				if($num==$zz)
				{
					//$dt=date("d-M-Y");
					$dt="Still Occupied";
					$edit_btn="<button class='btn btn-new  text-right' onclick=\"nursing_bed_transfer('1')\" $btndis><i class='icon-edit'></i> Bed Edit</button>";
				}
				else
				{
					$dt=date("d-m-Y",strtotime($ldt['date']))." / ".date("h:i A",strtotime($ldt['time']));
					$edit_btn="";
				}
			?>
				<tr>
					<td><?php echo $wd['name'];?></td>
					<td><?php echo $bd['bed_no'];?></td>
					<td><?php echo date("d-m-Y",strtotime($res['date']))." / ".date("h:i A",strtotime($res['time']));?></td>
					<td><?php echo $dt.$edit_btn;?></td>
					<td><?php echo $emp_info["name"];?></td>
				</tr>
		<?php
				$zz++;
			}
			?>
			</table>
			<button type="type" class="btn btn-info" onclick="nursing_bed_transfer('2')" <?php echo $btndis." ".$bed_transfer_dis; ?>><i class="icon icon-forward"></i> <?php echo $btnname; ?></button>
			<div id="bed_info"></div>
			<div id="bed_btn_info" style="display:none;">
				<input type="text" class="datepicker" id="bed_transfer_date" value="<?php echo date("Y-m-d"); ?>"><br>
				<button type='button' class='btn btn-primary' onclick='bed_assign_ok()'>Update</button>
				<button type='button' class='btn btn-danger' onclick='clr_bed_assign()'>Cancel</button>
			</div>
			<?php
		}
	}
}
if($_POST["type"]=="pat_last_bed_date")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$pat_bed_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` DESC "));
	echo $pat_bed_det["date"];
}
if($_POST["type"]=="nursing_bed_transfer")
{
	//print_r($_POST);
	$branch_id=$_POST['branch_id'];
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$val=$_POST['val'];
?>
	<h3>
		Bed Details
		<!--<small><?php echo date("d-m-Y h:i:s A"); ?></small>-->
	</h3>
<?php
	$ward=mysqli_query($link,"select * from ward_master where branch_id='$branch_id' AND `ward_id` IN(SELECT DISTINCT `ward_id` FROM `bed_master`) order by name");
	$ward_num=mysqli_num_rows($ward);
	while($w=mysqli_fetch_array($ward))
	{
		echo "<div class='ward'>";
		echo "<b>$w[name]</b> <br/>";
		
		
		$i=0;
		$beds=mysqli_query($link,"select distinct room_id,room_no from room_master where ward_id='$w[ward_id]' AND `room_id` IN(SELECT DISTINCT `room_id` FROM `bed_master`) order by room_no");
		while($b=mysqli_fetch_array($beds))
		{
			echo "<div style='margin:10px 0px 0px 10px'>";
			echo "<b>Room No: $b[room_no]</b> <br/>";
			$room_det=mysqli_query($link,"select * from bed_master where room_id='$b[room_id]'");
			
			while($rd=mysqli_fetch_array($room_det))
			{
				$bed_block=0;
				
				// Share Bed Block if Main Bed Taken => ImShare
				if($rd["share_bed"]>0 && $rd["main_bed_id"]>0)
				{
					$main_bed_check=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where bed_id='$rd[main_bed_id]'"));
					if($main_bed_check && $ipd!=$main_bed_check["ipd_id"])
					{
						$bed_block++;
					}
				}
				
				// Main Bed Block if Share Bed Taken =>ImMain
				$share_bed_qry=mysqli_query($link,"SELECT * FROM `bed_master` WHERE `main_bed_id`='$rd[bed_id]' AND `share_bed`=1");
				$share_bed_num=mysqli_num_rows($share_bed_qry);
				$share_bed_occupied_num=0;
				while($share_bed_info=mysqli_fetch_array($share_bed_qry))
				{
					$share_bed_check=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where bed_id='$share_bed_info[bed_id]'"));
					if($share_bed_check)
					{
						$share_bed_occupied_num++;
					}
				}
				if($share_bed_occupied_num==1)
				{
					$share_bedd_check=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$uhid' and ipd_id='$ipd' and bed_id IN(SELECT `bed_id` FROM `bed_master` WHERE `main_bed_id`='$rd[bed_id]' AND `share_bed`=1)"));
					if(!$share_bedd_check) // Not ME
					{
						$bed_block++;
					}
				}
				if($share_bed_occupied_num>1)
				{
					$bed_block++;
				}
				
				$style="width:50px;margin-left:10px;";
				$chk_bd=mysqli_query($link,"select * from ipd_bed_details_temp where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'");
				if(mysqli_num_rows($chk_bd)>0)
				{
					if(mysqli_num_rows(mysqli_query($link,"select * from ipd_bed_details_temp where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]' and patient_id='$uhid'"))>0)
					{
						//$style.="background-color:#5bc0de";
						//echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"nursing_bed_asign('$w[ward_id]','$rd[bed_id]','$w[name]','$rd[bed_no]','$val')\">$rd[bed_no]</span>";
						
						echo "<button class='btn btn-excel' id='$rd[bed_id]' style='margin-right: 5px;'>Bed No: $rd[bed_no]<br>₹".number_format($rd["charges"],2)."</button>";
					}
					else
					{
						//$style.="background-color:#ff8a80";
						//echo "<span class='btn' style='$style' id='$rd[bed_id]' >$rd[bed_no]</span>";
						
						$chk_bd_val=mysqli_fetch_array($chk_bd);
						
						//echo "<button class='btn btn-print' id='$rd[bed_id]' style='margin-right: 5px;'>Bed No: $rd[bed_no]<br>₹".number_format($rd["charges"],2)."</button>";
						echo "<button class='btn btn-print' id='$rd[bed_id]' style='margin-right: 5px;'>Bed No: $rd[bed_no]<br>".$chk_bd_val["ipd_id"]."</button>";
					}
				}
				else if($rd["status"]==1)
				{
					//$style.="background-color:#ffbb33";
					//echo "<span class='btn' style='$style' id='$rd[bed_id]' >$rd[bed_no]</span>";
					
					echo "<button class='btn btn-warning' id='$rd[bed_id]' style='margin-right: 5px;'>Bed No: $rd[bed_no]<br>$rd[reason]</button>";
				}
				else
				{
					$chk_bd_main=mysqli_query($link,"select * from ipd_pat_bed_details where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'");
					
					if(mysqli_num_rows($chk_bd_main)>0)
					{
						//$style.="background-color:#5cb85c";
						//echo "<span class='btn' style='$style' id='$rd[bed_id]'>$rd[bed_no]</span>";
						
						$chk_bd_main_val=mysqli_fetch_array($chk_bd_main);
						echo "<button class='btn btn-danger' id='$rd[bed_id]' style='margin-right: 5px;'>Bed No: $rd[bed_no]<br>".$chk_bd_main_val["ipd_id"]."</button>";
					}
					else
					{
						//echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"nursing_bed_asign('$w[ward_id]','$rd[bed_id]','$w[name]','$rd[bed_no]','$val')\">$rd[bed_no]</span>";
						
						if($bed_block==0)
						{
							$chk_guardian_bed=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details_guardian where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'"));
							if($chk_guardian_bed)
							{
								echo "<button class='btn btn-back' id='$rd[bed_id]' style='margin-right: 5px;'>Bed No: $rd[bed_no]<br>".$chk_guardian_bed["ipd_id"]."</button>";
							}
							else
							{
								echo "<button class='btn btn-search' id='$b[bed_id]' onclick=\"nursing_bed_asign('$w[ward_id]','$rd[bed_id]','$w[name]','$rd[bed_no]','$val')\" style='margin-right: 5px;'>Bed No: $rd[bed_no]<br>₹".number_format($rd["charges"],2)."</button>";
							}
						}
						else
						{
							echo "<button class='btn btn-danger disabled' id='$b[bed_id]' style='margin-right: 5px;'>Bed No: $rd[bed_no]<br>₹".number_format($rd["charges"],2)."</button>";
						}
					}
				}
				
				if($i==12)
				{
					$i=0;
					echo "<br/>";
					echo "<br/>";
				}
				else
				{
					$i++;
				}
			}
			echo "</div>";
		}
		
		echo "</div> <hr/>";
	}
	if($ward_num==0)
	{
		echo "<h4>No bed available</h4>";
	}
}

if($_POST["type"]=="nursing_bed_asign")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$w_id=$_POST['w_id'];
	$b_id=$_POST['b_id'];
	
	mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid' and ipd_id='$ipd'");
	mysqli_query($link,"insert into ipd_bed_details_temp(patient_id,ipd_id,ward_id,bed_id,date) values('$uhid','$ipd','$w_id','$b_id','$date')");
}

if($_POST["type"]=="bed_assign_temp")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$q=mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$v=mysqli_fetch_array($q);
		$ward=$v['ward_id'];
		$bed=$v['bed_id'];
		$wname=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$ward'"));
		$bname=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no` FROM `bed_master` WHERE `bed_id`='$bed'"));
		echo "<b>Selected</b><br/>Ward: ".$wname['name']."<br/>Bed No: ".$bname['bed_no']."<input type='hidden' id='ward_id' value=".$ward." /><input type='hidden' id='bed_id' value=".$bed." />";
	}
}

if($_POST["type"]=="bed_assign_ok")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$ward=$_POST['ward'];
	$bed=$_POST['bed'];
	$usr=$_POST['usr'];
	$edit=$_POST['edit'];
	$bed_transfer_date=$_POST['bed_transfer_date'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$bed_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `bed_master` WHERE `bed_id`='$bed' AND `ward_id`='$ward'"));
	if($bed_info["private_bed"]==1)
	{
		$centreno="C104";
		
		//mysqli_query($link, "INSERT INTO `pat_centre_change_record`(`patient_id`, `ipd_id`, `centreno_old`, `centreno_new`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$pat_reg[center_no]','$centreno','$c_user','$date','$time')");
		
		//mysqli_query($link, "UPDATE `uhid_and_opdid` SET `center_no`='$centreno' WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'");
	}
	else
	{
		$centreno="C100";
		
		//mysqli_query($link, "INSERT INTO `pat_centre_change_record`(`patient_id`, `ipd_id`, `centreno_old`, `centreno_new`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$pat_reg[center_no]','$centreno','$c_user','$date','$time')");
		
		//mysqli_query($link, "UPDATE `uhid_and_opdid` SET `center_no`='$centreno' WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'");
	}
	
	if($edit=='2') // Transfer
	{
		$old=mysqli_fetch_array(mysqli_query($link,"SELECT `ward_id`, `bed_id` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		//mysqli_query($link,"insert into ipd_pat_bed_details(patient_id,ipd_id,ward_id,bed_id,user,time,date) values('$uhid','$ipd','$ward','$bed','$usr','$time','$date')");
		
		mysqli_query($link,"insert into ipd_bed_alloc_details(patient_id,ipd_id,ward_id,bed_id,alloc_type,user,time,date) values('$uhid','$ipd','$old[ward_id]','$old[bed_id]','0','$usr','$time','$bed_transfer_date')");
		
		mysqli_query($link,"insert into ipd_bed_alloc_details(patient_id,ipd_id,ward_id,bed_id,alloc_type,user,time,date) values('$uhid','$ipd','$ward','$bed','1','$usr','$time','$bed_transfer_date')");
		
		mysqli_query($link,"UPDATE `ipd_pat_bed_details` SET `ward_id`='$ward',`bed_id`='$bed' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		
		mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid' and ipd_id='$ipd'");
		
		// Delete
		mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='141'");
		
		mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='148'");
		
		
		//mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id` IN(141,148,142) AND `dtae`>='$bed_transfer_date'"); // Bed=141, Bed Plus=148, Doc Visit=142
		mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id` IN(141,148,142)"); // Bed=141, Bed Plus=148, Doc Visit=142
		
	}
	if($edit=='1') // Edit
	{
		// Edit Counter
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' AND `type`='3' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
		
		$s_date_val=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_id`,`date` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		$old_bed_id=$s_date_val["bed_id"];
		$start_date=$s_date_val["date"];
		//$start_date="2017-12-02";
		$end_date=date("Y-m-d");
		
		$diff=abs(strtotime($start_date)-strtotime($end_date));
		$diff=$diff/60/60/24;
		
		$charge_val=mysqli_fetch_array(mysqli_query($link,"SELECT `charge_id` FROM `bed_master` WHERE `bed_id`='$old_bed_id'"));
		$bed_charge_id=$charge_val["charge_id"];
		
		for($i=0;$i<=$diff;$i++)
		{
			$n_date=date('Y-m-d', strtotime($start_date. ' + '.$i.' days'));
			
			// Edit Records
			$old_service=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$bed_charge_id' AND `date`='$n_date' "));
			
			if($old_service)
			{
				mysqli_query($link," INSERT INTO `ipd_pat_service_details_edit`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `counter`, `bed_id`) VALUES ('$old_service[patient_id]','$old_service[ipd_id]','$old_service[group_id]','$old_service[service_id]','$old_service[service_text]','$old_service[ser_quantity]','$old_service[rate]','$old_service[amount]','$old_service[days]','$old_service[user]','$old_service[time]','$old_service[date]','$counter_num','$old_service[bed_id]') ");
				///////
				mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$bed_charge_id' AND `date`='$n_date'");
				
				$bed_other_charge_qry=mysqli_query($link,"SELECT `charge_id` FROM `bed_other_charge` WHERE `bed_id`='$old_bed_id'");
				while($bed_other_charge=mysqli_fetch_array($bed_other_charge_qry))
				{
					$bed_other_charge_id=$bed_other_charge["charge_id"];
					
					// Edit Records
					$old_service_other=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$bed_other_charge_id' AND `date`='$n_date' "));
					
					mysqli_query($link," INSERT INTO `ipd_pat_service_details_edit`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `counter`, `bed_id`) VALUES ('$old_service_other[patient_id]','$old_service_other[ipd_id]','$old_service_other[group_id]','$old_service_other[service_id]','$old_service_other[service_text]','$old_service_other[ser_quantity]','$old_service_other[rate]','$old_service_other[amount]','$old_service_other[days]','$old_service_other[user]','$old_service_other[time]','$old_service_other[date]','$counter_num','$old_service_other[bed_id]') ");
					///////
					
					mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$bed_other_charge_id' AND `date`='$n_date'");
					
				}
			}
		}
		
		// Edit reports
		$ipd_bed_detail_details=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
		
		mysqli_query($link," INSERT INTO `ipd_pat_bed_details_edit`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `user`, `time`, `date`, `counter`) VALUES ('$ipd_bed_detail_details[patient_id]','$ipd_bed_detail_details[ipd_id]','$ipd_bed_detail_details[ward_id]','$ipd_bed_detail_details[bed_id]','$ipd_bed_detail_details[user]','$ipd_bed_detail_details[time]','$ipd_bed_detail_details[date]','$counter_num') ");
		///////
		
		$mx_slno=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(`slno`) AS `MX` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1'"));
		$slno=$mx_slno["MX"];
		
		mysqli_query($link," UPDATE `ipd_bed_alloc_details` SET `ward_id`='$ward',`bed_id`='$bed',`user`='$usr' WHERE `slno`='$slno' ");
		
		mysqli_query($link,"UPDATE `ipd_pat_bed_details` SET `ward_id`='$ward',`bed_id`='$bed',`user`='$usr' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		
		mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid' and ipd_id='$ipd'");
		
		// edit counter record
			mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$uhid','$ipd','$date','$time','$usr','3','$counter_num') ");
		
		// Delete
		mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='141'");
		
		mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='148'");
		
		mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id` IN(141,148,142)"); // Bed=141, Bed Plus=148, Doc Visit=142
	}
}

if($_POST["type"]=="clr_bed_assign")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	mysqli_query($link,"DELETE FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
}

if($_POST["type"]=="load_bed_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `bed_id`!='1000'"));
	
	echo $n;
}

if($_POST["type"]=="pat_ipd_discharge_request_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$m=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	$tot=$n+$m;
	
	echo $tot;
}


if($_POST["type"]=="pat_ipd_delivery_num") // delevery details
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$discharge_request=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	$discharge=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	$u=mysqli_fetch_array(mysqli_query($link,"SELECT `edit_ipd` FROM `employee` WHERE `emp_id`='$usr'"));
	
	$q=mysqli_query($link,"SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$f=mysqli_fetch_array(mysqli_query($link,"SELECT `father_name` FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Father&apos;s Name</th>
			<th colspan="2"><?php echo $f['father_name'];?></th>
			<td colspan="3"><input type="text" id="edit_fat" onkeyup="fat_upper()" style="display:none;" value="<?php echo $f['father_name'];?>" /></td>
			<td>
				<?php
				//if($discharge==0)
				//{
					//if(!$discharge_request)
					//{
						//if(['edit_ipd']==1)
						//{
			?>
						<button type="button" class="btn btn-mini btn-info" id="btn_edt_fat" onclick="edt_fat_nam()">Edit Father Name</button>
						<button type="button" class="btn btn-mini btn-success" id="btn_sav_fat" style="display:none" onclick="sav_fat_nam()">Save</button>
						<button type="button" class="btn btn-mini btn-danger" id="btn_can_fat" style="display:none" onclick="canc_fat_nam()">Cancel</button>
				<?php
						//}
					//}
				//}
				?>
			</td>
		</tr>
		<tr>
			<th>Baby Name</th>
			<th>D.O.B</th>
			<th>Sex</th>
			<th>Time of Birth</th>
			<th>Weight</th>
			<th>Blood Group</th>
			<th>Baby Unit No.</th>
			<th>Baby ID</th>
			<th></th>
		</tr>
		<?php
		while($r=mysqli_fetch_array($q))
		{
			$name=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[baby_uhid]'"));
		?>
		<tr id="clk<?php echo $r['slno'];?>" class="clk_tr">
			<td><?php echo $name['name'];?></td>
			<td><?php echo convert_date_g($r['dob']);?></td>
			<td><?php echo $r['sex'];?></td>
			<td><?php echo convert_time($r['born_time']);?></td>
			<td><?php echo $r['weight'];?></td>
			<td><?php echo $r['blood_group'];?></td>
			<td><?php echo $r['baby_uhid'];?></td>
			<td><?php echo $r['baby_ipd_id'];?></td>
			<td>
				<button type="button" class="btn btn-mini btn-info btn_edt_cert" id="btn_cert<?php echo $r['slno'];?>" onclick="pr_certficate('<?php echo $uhid;?>','<?php echo $r['baby_uhid'];?>')">Print Certificate</button>
				<button type="button" class="btn btn-mini btn-info btn_edt_cert" id="btn_cert<?php echo $r['slno'];?>" onclick="pr_vaccine('<?php echo $uhid;?>','<?php echo $r['baby_uhid'];?>')">Vaccine Schedule </button>
				<button type="button" class="btn btn-mini btn-primary btn_edt_cert" id="btn_edt_cert<?php echo $r['slno'];?>" onclick="edit_certficate('<?php echo $r['slno'];?>')">Edit</button>
			<?php
			if($discharge==0)
			{
				if(!$discharge_request)
				{
					$baby_test_num=mysqli_num_rows(mysqli_query($link, "SELECT * FROM `patient_test_details` WHERE `patient_id`='$r[baby_uhid]' AND `ipd_id`='$r[baby_ipd_id]'"));
					if($baby_test_num==0)
					{
			?>
						<button type="button" class="btn btn-mini btn-danger btn_edt_cert" id="btn_del_cert<?php echo $r['slno'];?>" onclick="del_certficate('<?php echo $r['slno'];?>')">Delete</button>
			<?php
					}
				}
			}
			?>
			</td>
		</tr>
		<tr id="tr<?php echo $r['slno'];?>" class="baby_tr" style="display:none;">
			<td colspan="7">
				<div id="sl<?php echo $r['slno'];?>" class="baby_div" style="display:none;"></div>
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
	}
	else
	{
	?>
		<button type="button" class="btn btn-info" id="deli_add_btn" onclick="show_deli_num()"><i class="icon icon-plus"></i> Add</button>
		<div id="show_deli_num" style="display:none;">
			<b>No of children:</b>
			<select id="no">
				<option value="0">Select</option>
			<?php
				for($i=1;$i<=10;$i++)
				{
			?>
				<option value="<?php echo $i;?>"><?php echo $i;?></option>
			<?php
				}
			?>
			</select>
			<input type="text" id="fat_name" onkeyup="fat_value()" placeholder="Father's Name" />
			<button type="button" class="btn btn-primary" onclick="add_deli_det()"><i class="icon icon-ok"></i> Ok</button>
			<button type="button" class="btn btn-danger" id="deli_rem_btn" onclick="rem_deli_det()"><i class="icon icon-ban-circle"></i> Cancel</button>
		</div>
		<div id="add_deli_det" style="display:none;">
		
		</div>
	<?php
	}
}

if($_POST["type"]=="pat_ipd_delivery_det")
{
	$uhid=$_POST['uhid'];
	$no=$_POST['no'];
	$fat_name=$_POST['fat_name'];
	mysqli_query($link,"DELETE FROM `ipd_baby_bed_temp` WHERE `patient_id`='$uhid'");
?>
	<input type="text" id="val" style="display:none;" value="<?php echo $no;?>" />
	<input type="text" id="fat_name" style="display:none;" value="<?php echo $fat_name;?>" />
	<input type="text" id="bedd" style="display:none;" value="" />
	<table class="table table-condensed table-bordered" id="deli_tbl">
		<?php
		for($i=1;$i<=$no;$i++)
		{
			if($no>1)
			{
			?>
			<tr>
				<th colspan="8" style="background:#cccccc;">Baby <?php echo $i;?></th>
			</tr>
			<?php
			}
		?>
		<tr id="tr<?php echo $i;?>" class="tr">
			<th>D.O.B<br/><input type="text" class="txt" readonly="readonly" style="cursor:unset;" id="dob<?php echo $i;?>" placeholder="Date Of Birth" /> <i class="icon-calendar icon-large" style="color:#F0A11F;"></i></th>
			<th>
				Time Of Birth<br/>
				<!--<input type="text" class="txt" readonly="readonly" style="cursor:unset;" id="time<?php echo $i;?>" placeholder="HH:MM" /> <i class="icon-time icon-large" style="color:#F0A11F;"></i>-->
				<select id="hrs<?php echo $i;?>" class="span1">
					<option value="0">Hours</option>
					<?php
					for($h=1; $h<=12; $h++)
					{
					?>
					<option value="<?php echo $h;?>"><?php echo $h;?></option>
					<?php
					}
					?>
				</select>
				<select id="mins<?php echo $i;?>" class="span1">
					<option value="0">Minutes</option>
					<?php
					for($m=0; $m<60; $m++)
					{
						$m = str_pad($m,2,"0",STR_PAD_LEFT);
					?>
					<option value="<?php echo $m;?>"><?php echo $m;?></option>
					<?php
					}
					?>
				</select>
				<select id="ampm<?php echo $i;?>" class="span1">
					<option value="0">Select</option>
					<option value="am">AM</option>
					<option value="pm">PM</option>
				</select>
			</th>
			<th>Sex<br/>
				<select id="sex<?php echo $i;?>" class="span2">
					<option value="0">Select</option>
					<option value="Male">Male</option>
					<option value="Female">Female</option>
				</select>
			</th>
			<!--<th>Weight<br/><input type="text" id="wt<?php echo $i;?>" onfocus="weight_ui_div()" readonly="readonly" style="cursor:unset;" class="txt" placeholder="#.## K.G" /></th>-->
			<th>Weight<br/><input type="text" id="wt<?php echo $i;?>" class="txt numericfloat" placeholder="In KG" /></th>
		</tr>
		<tr>
			<th>Blood Group<br/>
				<select id="blood<?php echo $i;?>" class="span2">
					<option value="">Select</option>
					<option value="O Positive">O Positive</option>
					<option value="O Negative">O Negative</option>
					<option value="A Positive">A Positive</option>
					<option value="A Negative">A Negative</option>
					<option value="B Positive">B Positive</option>
					<option value="B Negative">B Negative</option>
					<option value="AB Positive">AB Positive</option>
					<option value="AB Negative">AB Negative</option>
				</select>
			</th>
			<th>
				Delivery Mode<br/>
				<select id="dmode<?php echo $i;?>" class="span2">
					<option value="0">Select</option>
					<option value="LSCS">LSCS</option>
					<option value="Normal">Normal</option>
					<option value="Forceps">Forceps</option>
					<option value="Vacuum">Vacuum</option>
					<option value="Ventose">Ventose</option>
				</select>
			</th>
			<th>
				Conducted By<br/>
				<select id="conducted<?php echo $i;?>" class="span3">
					<option value="0">Select</option>
					<?php
					$cc=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($c=mysqli_fetch_array($cc))
					{
					?>
					<option value="<?php echo $c['consultantdoctorid'];?>"><?php echo $c['Name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th>
				Dead Tag<br/>
				<select id="tag<?php echo $i;?>" class="span2">
					<option value="No">No</option>
					<option value="Yes">Yes</option>
				</select>
			</th>
			<!--<th>Bed No<br/>
				<input type="text" id="ward<?php echo $i;?>" style="display:none;" value="" />
				<input type="text" id="bed<?php echo $i;?>" style="display:none;" value="" />
				<button type="button" id="b<?php echo $i;?>" style="width:90px;" class="btn btn-primary" onclick="view_baby_bed('<?php echo $uhid;?>',<?php echo $i;?>)"><i class="icon icon-eye-open"></i> View</button>
				<!--<select id="bed<?php echo $i;?>" class="span2">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `bed_id`,`room_id`,`bed_no` FROM `bed_master` WHERE `ward_id`='6'");
					while($r=mysqli_fetch_array($q))
					{
						$room=mysqli_fetch_array(mysqli_query($link,"SELECT `room_no` FROM `room_master` WHERE `room_id`='$r[room_id]'"));
					?>
					<option value="<?php echo $r['bed_id'];?>"><?php echo "Room: ".$room['room_no']." Bed: ".$r['bed_no'];?></option>
					<?php
					}
					?>
				</select>-->
			<!--</th>-->
		</tr>
		<script>
			$("#dob"+<?php echo $i;?>).datepicker({changeMonth:true,changeYear:true,dateFormat: 'yy-mm-dd',maxDate:'0'});
			//$("#time"+<?php echo $i;?>).timepicker({minutes: {starts: 0,interval: 01,}});
			//$('#wt'+<?php echo $i;?>).timepicker({showPeriodLabels:false,showLeadingZero: false,defaultTime:'',timeSeparator:'.',hourText: 'KG',minuteText: 'Gram',hours:{starts: 1,ends: 4},minutes: {starts: 0,ends:99,interval: 01,}});
		</script>
		<?php
		}
		?>
	</table>
	<button type="button" class="btn btn-primary" id="deli_sav" onclick="pat_ipd_delivery_save()"><i class="icon icon-file"></i> Save</button>
	<button type="button" class="btn btn-danger" onclick="clear_all_deli()"><i class="icon icon-ban-circle"></i> Cancel</button>
	<style>
		#deli_tbl tr:hover
		{
			background:none;
		}
		#deli_tbl tr th, #deli_tbl tr td
		{
			padding-top: 0px;
			padding-bottom: 0px;
		}
	</style>
	<?php
}
if($_POST["type"]=="pat_ipd_delivery_save")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$all=mysqli_real_escape_string($link, $_POST['all']);
	$usr=$user=$_POST['usr'];
	$det=explode("##",$all);
	$ar=sizeof(array_filter($det));
	if($ar>1)
	{
		$j=1;
	}
	else
	{
		$j="";
	}
	$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	
	//$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
	
	//$pat_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `attend_doc`,`admit_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	$error=0;
	
	foreach($det as $vl)
	{
		$v=explode("@",$vl);
		$dob=$v[0];
		$sex=$v[1];
		//$b_time=$v[2];
		$b_time=date("H:i:s", strtotime($v[2]));
		$wt=$v[3];
		$blood_group=$blood=$v[4];
		$mode=$v[5];
		$conduct=$v[6];
		$tag=$v[7];
		//$bed=$v[6];
		$bed="";
		$father_name=$fat_name=$v[8];
		$dob1=age_calculator($dob);
		$dt=explode(" ",$dob1);
		$age=$dt[0];
		$age_type=$dt[1];
		if($dob && $sex && $b_time && $wt)
		{
			$dob=date("d-m-Y",strtotime($dob));
			
			$patient_reg_type=$p_type_id=8;
			//include("patient_id_generator.php");
			
			$pat_name_full=$name="BABY ".$j." OF ".$pat_info['name'];
			$refbydoctorid=$ref=$pat_reg['refbydoctorid'];
			$centre=$pat_reg['center_no'];
			$hguide_id=$pat_reg['hguide_id'];
			$branch_id=$pat_reg['branch_id'];
			
			//$gd_name=$pat_info["gd_name"];
			$gd_name=mysqli_real_escape_string($link, $pat_info["name"]);
			$gd_phone=$phone=$pat_info["phone"];
			$address=mysqli_real_escape_string($link, $pat_info["address"]);
			$email=$pat_info["address"];
			$crno=$ptype=0;
			$credit="";
			
			$pin=$pat_info_rel["pin"];
			$police=$pat_info["police"];
			$state=$pat_info["state"];
			$district=$pat_info["district"];
			$city=mysqli_real_escape_string($link, $pat_info["city"]);
			$fileno="";
			$post_office=mysqli_real_escape_string($link, $pat_info["post_office"]);
			$mother_name=mysqli_real_escape_string($link, $pat_info["name"]);
			$post_office=mysqli_real_escape_string($link, $pat_info["post_office"]);
			$religion_id=mysqli_real_escape_string($link, $pat_info["religion_id"]);
			
			$marital_status=2;
			$g_relation="MOTHER";
			$source_id=$pat_other["source_id"];
			$esi_ip_no="";
			$income_id=0;
			
			$patient_id=0;
			include("patient_info_save.php");
			
			if($patient_id!="0")
			{
				if(mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`) VALUES ('$patient_id',NULL,'$date','$time','$usr','8','$baby_serial','$refbydoctorid','$centre','$hguide_id','$branch_id') "))
				{
					$last_row=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `date`='$date' AND `user`='$user' AND `type`='$p_type_id' ORDER BY `slno` DESC LIMIT 0,1 "));
	
					$last_row_num=$last_row["slno"];
					
					$patient_reg_type=$p_type_id=8;
					include("opd_id_generator.php");
					
					mysqli_query($link," UPDATE `uhid_and_opdid` SET `opd_id`='$opd_id',`ipd_serial`='$opd_serial' WHERE `slno`='$last_row_num' ");
					
					$dob=date("Y-m-d",strtotime($dob));
					
					mysqli_query($link,"INSERT INTO `ipd_pat_delivery_det`(`patient_id`, `ipd_id`, `sex`, `dob`, `born_time`, `weight`, `blood_group`, `bed_id`, `baby_uhid`, `baby_ipd_id`, `father_name`, `delivery_mode`, `conducted_by`, `dead_tag`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$sex','$dob','$b_time','$wt','$blood','$bed','$patient_id','$opd_id','$fat_name','$mode','$conduct','$tag','$date','$time','$usr')");
					if($j)
					$j++;
				}
				else
				{
					$error=1;
				}
			}
			else
			{
				$error=2;
			}
		}
	}
	if($error==0)
	{
		echo "Saved";
	}
	else
	{
		echo "Failed, try again later.".$error;
	}
}

if($_POST["type"]=="pat_ipd_inv_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	
	$pat_discharge_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($pat_discharge_num>0)
	{
		$btndis="disabled='disabled'";
	}
	else
	{
		$btndis="";
	}
	
	//$ds=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `type`='4' ORDER BY `batch_no` DESC");
	$ds=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `batch_no` DESC");
	//$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `type`='4'");
	$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
	$num=mysqli_num_rows($q);
	$no=mysqli_num_rows($ds);
?>
	<div class="span5" style="margin-left:0px;">
	<?php
	if($no>0)
	{
		while($res=mysqli_fetch_array($ds))
		{
			//$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' AND `type`='4'"));
			$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' "));
			$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'"));
			echo "<button type='button' class='btn btn-default bt' id='ad".$res['batch_no']."' onclick='view_batch(".$res['batch_no'].")'><span class='sp'>Batch No=".$res['batch_no']."</span><span class='sp'>Tests=".$nn."</span><span class='sp'>Date: ".convert_date_g($dt['date'])."</span><span class='sp'>Time: ".convert_time($dt['time'])."</span></button><br/>";
		}
	}
	if($num>0 && $pat_discharge_num==0)
	{
	?>
		<button type="button" class="btn btn-info" id="adm" onclick="ad_tests()" style="" <?php echo $btndis; ?>><i class="icon-plus"></i> Add New Batch</button>
	<?php
	}
	else if($pat_discharge_num==0)
	{
	?>
	<button type="button" class="btn btn-info" id="ad" onclick="ad_tests()" style="" <?php echo $btndis; ?>><i class="icon-plus"></i> Add</button>
	<?php
	}
	
	if($num>1)
	{
?>
		<button class="btn btn-print" onclick="print_batch_bill('<?php echo $uhid; ?>','<?php echo $ipd; ?>','0')"><i class="icon-print"></i> All Batch</button>
<?php
	}
	?>
	</div>
	<div id="batch_details" class="span5" style="margin-left:-40px;max-width:550px;min-width:540px;"></div>
	<style>
		.widget-content{border-bottom:none;}
		.sp{margin-left:10px;}
		.bt{margin-bottom:5px;}
	</style>
	<?php
}

if($_POST["type"]=="edit_certficate")
{
	$slno=$_POST['slno'];
	$usr=$_POST['usr'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_delivery_det` WHERE `slno`='$slno'"));
	?>
	<table class="table table-condensed" id="edt_deli_tbl">
		<tr id="tr<?php echo $i;?>" class="tr">
			<th>D.O.B <i class="icon-calendar icon-large" style="color:#F0A11F;"></i><br/><input type="text" class="txt" readonly="readonly" style="cursor:unset;" id="edit_dob" value="<?php echo $v['dob'];?>" placeholder="Date Of Birth" /></th>
			<th>
				Time Of Birth <i class="icon-time icon-large" style="color:#F0A11F;"></i><br/>
				<!--<input type="text" class="txt" readonly="readonly" style="cursor:unset;" id="edit_time" value="<?php echo $v['born_time'];?>" placeholder="HH:MM" /> <i class="icon-time icon-large" style="color:#F0A11F;"></i>-->
				<?php
				$b_tim=explode(":",date("h:i:s", strtotime($v['born_time'])));
				$b_hours=$b_tim[0];
				$b_mins=$b_tim[1];
				$b_secs=$b_tim[3];
				$am_pm=date("a", strtotime($v['born_time']));
				//echo date("h:i:s", strtotime($v['born_time']));
				?>
				<select id="edit_hrs" class="span1"> <!---select hours--->
					<option value="0">Hours</option>
					<?php
					for($h=1; $h<=12; $h++)
					{
					?>
					<option value="<?php echo $h;?>" <?php if($h==$b_hours){echo "selected='selected'";}?>><?php echo $h;?></option>
					<?php
					}
					?>
				</select>
				<select id="edit_mins" class="span1"> <!---select minutes--->
					<option value="0">Minutes</option>
					<?php
					for($m=0; $m<60; $m++)
					{
						$m = str_pad($m,2,"0",STR_PAD_LEFT);
					?>
					<option value="<?php echo $m;?>" <?php if($m==$b_mins){echo "selected='selected'";}?>><?php echo $m;?></option>
					<?php
					}
					?>
				</select>
				<select id="edit_ampm" class="span1"> <!---select am/pm--->
					<option value="0">Select</option>
					<option value="am" <?php if($am_pm=="am"){echo "selected='selected'";}?>>AM</option>
					<option value="pm" <?php if($am_pm=="pm"){echo "selected='selected'";}?>>PM</option>
				</select>
			</th>
			<th>Sex<br/>
				<select id="edit_sex" class="span2">
					<option value="0">Select</option>
					<option value="Male" <?php if($v['sex']=="Male"){echo "selected='selected'";}?>>Male</option>
					<option value="Female" <?php if($v['sex']=="Female"){echo "selected='selected'";}?>>Female</option>
				</select>
			</th>
			<!--<th>Weight<br/><input type="text" id="edit_wt" onfocus="weight_ui_div()" readonly="readonly" style="cursor:unset;" class="txt" value="<?php echo $v['weight'];?>" placeholder="#.## K.G" /></th>-->
			<th>Weight<br/><input type="text" id="edit_wt" class="txt" value="<?php echo $v['weight'];?>" placeholder="#.## K.G" /></th>
		</tr>
		<tr>
			<th>Blood Group<br/>
				<select id="edit_blood" class="span2">
					<option value="">Select</option>
					<option value="O Positive" <?php if($v['blood_group']=="O Positive"){echo "selected='selected'";}?>>O Positive</option>
					<option value="O Negative" <?php if($v['blood_group']=="O Negative"){echo "selected='selected'";}?>>O Negative</option>
					<option value="A Positive" <?php if($v['blood_group']=="A Positive"){echo "selected='selected'";}?>>A Positive</option>
					<option value="A Negative" <?php if($v['blood_group']=="A Negative"){echo "selected='selected'";}?>>A Negative</option>
					<option value="B Positive" <?php if($v['blood_group']=="B Positive"){echo "selected='selected'";}?>>B Positive</option>
					<option value="B Negative" <?php if($v['blood_group']=="B Negative"){echo "selected='selected'";}?>>B Negative</option>
					<option value="AB Positive" <?php if($v['blood_group']=="AB Positive"){echo "selected='selected'";}?>>AB Positive</option>
					<option value="AB Negative" <?php if($v['blood_group']=="AB Negative"){echo "selected='selected'";}?>>AB Negative</option>
				</select>
			</th>
			<th>
				Delivery Mode<br/>
				<select id="edit_dmode" class="span2">
					<option value="0">Select</option>
					<option value="LSCS" <?php if($v['delivery_mode']=="LSCS"){echo "selected='selected'";}?>>LSCS</option>
					<option value="Normal" <?php if($v['delivery_mode']=="Normal"){echo "selected='selected'";}?>>Normal</option>
					<option value="Forceps" <?php if($v['delivery_mode']=="Forceps"){echo "selected='selected'";}?>>Forceps</option>
					<option value="Vacuum" <?php if($v['delivery_mode']=="Vacuum"){echo "selected='selected'";}?>>Vacuum</option>
					<option value="Ventose" <?php if($v['delivery_mode']=="Ventose"){echo "selected='selected'";}?>>Ventose</option>
				</select>
			</th>
			<th>
				Conducted By<br/>
				<select id="edit_conducted" class="span3">
					<option value="0">Select</option>
					<?php
					$cc=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($c=mysqli_fetch_array($cc))
					{
					?>
					<option value="<?php echo $c['consultantdoctorid'];?>" <?php if($v['conducted_by']==$c['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $c['Name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th>
				Dead Tag<br/>
				<select id="edit_tag" class="span2">
					<option value="No" <?php if($v['dead_tag']=="No"){echo "selected='selected'";}?>>No</option>
					<option value="Yes" <?php if($v['dead_tag']=="Yes"){echo "selected='selected'";}?>>Yes</option>
				</select>
			</th>
		</tr>
	</table>
	<button type="button" class="btn btn-primary" id="save_cert_edit" onclick="save_cert_edit('<?php echo $slno;?>')"><i class="icon icon-ok"></i> Ok</button>
	<button type="button" class="btn btn-danger" id="close_cert_edit" onclick="close_cert_edit('<?php echo $slno;?>')"><i class="icon icon-ban-circle"></i> Cancel</button>
	<style>
		#edt_deli_tbl tr:hover
		{
			background:none;
		}
		#edt_deli_tbl tr th, #edt_deli_tbl tr td
		{
			padding-top: 0px;
			padding-bottom: 0px;
		}
	</style>
	<script>
		$("#edit_dob").datepicker({dateFormat: 'yy-mm-dd',maxDate:'0'});
		$("#edit_time").timepicker({minutes: {starts: 0,interval: 01,}});
		//$("#edit_wt").timepicker({showPeriodLabels:false,showLeadingZero: false,defaultTime:'',timeSeparator:'.',hourText: 'KG',minuteText: 'Gram',hours:{starts: 1,ends: 4},minutes: {starts: 0,ends:99,interval: 01,}});
	</script>
	<?php
}

if($_POST["type"]=="save_cert_edit")
{
	$sl=$_POST['sl'];
	$edit_dob=$_POST['edit_dob'];
	//$edit_time=$_POST['edit_time'];
	$edit_time=date("H:i:s", strtotime($_POST['edit_time']));
	$edit_sex=$_POST['edit_sex'];
	$edit_wt=$_POST['edit_wt'];
	$edit_blood=$_POST['edit_blood'];
	$edit_dmode=$_POST['edit_dmode'];
	$edit_conducted=$_POST['edit_conducted'];
	$edit_tag=$_POST['edit_tag'];
	//echo $sl;
	
	mysqli_query($link,"UPDATE `ipd_pat_delivery_det` SET `sex`='$edit_sex',`dob`='$edit_dob',`born_time`='$edit_time',`weight`='$edit_wt',`blood_group`='$edit_blood',`delivery_mode`='$edit_dmode',`conducted_by`='$edit_conducted',`dead_tag`='$edit_tag' WHERE `slno`='$sl'");
	
	$pat_delvry_det_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `slno`='$sl' ");
	
	while($pat_delvry_det=mysqli_fetch_array($pat_delvry_det_qry))
	{
		mysqli_query($link,"UPDATE `patient_info` SET `sex`='$edit_sex',`dob`='$edit_dob',`blood_group`='$edit_blood',`father_name`='$edit_fat' WHERE `patient_id`='$pat_delvry_det[baby_uhid]'");
		
		//mysqli_query($link,"UPDATE `patient_info_rel` SET `father_name`='$edit_fat' WHERE `patient_id`='$pat_delvry_det[baby_uhid]'");
	}
	
	echo "Updated";
}

if($_POST["type"]=="sav_fat_nam")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$edit_fat=mysqli_real_escape_string($link, $_POST['edit_fat']);
	
	mysqli_query($link,"UPDATE `ipd_pat_delivery_det` SET `father_name`='$edit_fat' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	
	$pat_delvry_det_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
	
	while($pat_delvry_det=mysqli_fetch_array($pat_delvry_det_qry))
	{
		mysqli_query($link,"UPDATE `patient_info` SET `father_name`='$edit_fat' WHERE `patient_id`='$pat_delvry_det[baby_uhid]'");
		//mysqli_query($link,"UPDATE `patient_info_rel` SET `father_name`='$edit_fat' WHERE `patient_id`='$pat_delvry_det[baby_uhid]'");
	}
	
	echo "Updated";
}

if($_POST["type"]=="del_certficate")
{
	$sl=$_POST['sl'];
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$pat_delvry_det_qry=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `slno`='$sl' AND `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
	
	mysqli_query($link,"DELETE FROM `patient_info` WHERE `patient_id`='$pat_delvry_det[baby_uhid]'");
	
	mysqli_query($link,"DELETE FROM `uhid_and_opdid` WHERE `patient_id`='$pat_delvry_det[baby_uhid]' AND `opd_id`='$pat_delvry_det[ipd_uhid]'");
	
	mysqli_query($link,"DELETE FROM `ipd_pat_delivery_det` WHERE `slno`='$sl' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	
	echo "Deleted";
}

// Discharge Summary Start
if($_POST["type"]=="ipd_pat_discharge_summ")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$template_id=$_POST['template_id'];
	
	if(!$template_id){ $template_id=0; }
	
	include("patient_discharge_summary_form.php");
}
// Discharge Summary End


///////// Consultant Doctor Transfer Start

if($_POST["type"]==901)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$ipd_pat_doc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd'"));
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd'"));
	if($n>0)
	{
		$btn_val="Transfer";
		$btn_cls="btn-primary";
		$dis="";
		$func="upd_doc()";
	}
	else
	{
		$btn_val="Discharged";
		$btn_cls="btn-danger";
		$dis="Disabled";
		$func="";
	}
	$pat_doc_trans_qry=mysqli_query($link," SELECT * FROM `ipd_pat_doc_transfer` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `status`=1 ");
	$pat_doc_trans_num=mysqli_num_rows($pat_doc_trans_qry);
	
?>
	<table class="table table-condensed">
		<tr>
			<th style="width: 1%;">#</th>
			<th>Doctor Name</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
<?php
	$n=1;
	$pat_doc_trans_num=mysqli_num_rows($pat_doc_trans_qry);
	while($pat_doc_trans=mysqli_fetch_array($pat_doc_trans_qry))
	{
		$doc_doc_name=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_doc_trans[attend_doc]' "));
		
		$user_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$pat_doc_trans[user]' "));
?>
		<tr>
			<td><?php echo $n; ?></td>
		<?php
			if($pat_doc_trans_num==$n)
			{
		?>
			<td>
				<select class="span3" id="pat_consultant_doctor<?php echo $n; ?>" onchange="pat_consultant_doctor_change(this)">
					<option value="0">Select</option>
				<?php
					$qry=mysqli_query($link,"SELECT `consultantdoctorid`, `Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($data=mysqli_fetch_array($qry))
					{
				?>
						<option value="<?php echo $data["consultantdoctorid"];?>" <?php if($pat_doc_trans["attend_doc"]==$data["consultantdoctorid"]){echo "selected='selected'";}?>><?php echo $data["Name"];?></option>
				<?php
					}
				?>
				</select>
			</td>
		<?php
			}
			else
			{
		?>
				<td><?php echo $doc_doc_name["Name"]; ?></td>
		<?php
			}
		?>
			<td>
				<?php echo convert_date_g($pat_doc_trans["date"]); ?> <?php echo convert_time($pat_doc_trans["time"]); ?>
			</td>
			<td>
				<?php echo $user_info["name"]; ?>
			<?php if($n==$pat_doc_trans_num && $n!=1){ ?>
				<button class="btn btn-mini btn-danger" style="float:right;" onClick="delete_ipd_con_doc('<?php echo $pat_doc_trans["slno"]; ?>','<?php echo $pat_doc_trans["attend_doc"]; ?>')"><i class="icon-remove"></i></button>
			<?php } ?>
			</td>
		</tr>
<?php
		$n++;
	}
	if($ipd_pat_doc['attend_doc']>0)
	{
?>
		<tr id="transfer_tr_btn">
			<td colspan="4">
				<button class="btn btn-success btn-mini" onClick="$('#transfer_tr').show();$('#transfer_tr_btn').hide();">Transfer</button>
			</td>
		</tr>
<?php
	}
?>
		<tr id="transfer_tr" style="display:none;">
			<th colspan="3">Select Doctor
				<select class="span3" id="adm_doc">
					<option value="0">Select</option>
					<?php
					//$dq=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='5' ORDER BY `name`");
					$dq=mysqli_query($link,"SELECT `consultantdoctorid`, `Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($dr=mysqli_fetch_array($dq))
					{
					?>
						<option value="<?php echo $dr['consultantdoctorid'];?>" <?php if($ipd_pat_doc['attend_doc']==$dr['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $dr['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				<button type="button" class="btn <?php echo $btn_cls;?>" onclick="<?php echo $func;?>" <?php echo $dis;?>><?php echo $btn_val;?></button>
				<button type="button" class="btn btn-warning" onClick="$('#transfer_tr').hide();$('#transfer_tr_btn').show();" >Cancel</button>
			</td>
		</tr>
	</table>
	<?php
}

if($_POST["type"]==902)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$adm_doc=$_POST['adm_doc'];
	$usr=$_POST['usr'];
	$ipd_pat_doc=mysqli_fetch_array(mysqli_query($link,"SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($ipd_pat_doc['attend_doc']==$adm_doc)
	{
		echo "<h5>Same doctor selected</h5>";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_doc_transfer`(`patient_id`, `ipd_id`, `attend_doc`, `status`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$ipd_pat_doc[attend_doc]','0','$date','$time','$usr')");
		mysqli_query($link,"INSERT INTO `ipd_pat_doc_transfer`(`patient_id`, `ipd_id`, `attend_doc`, `status`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$adm_doc','1','$date','$time','$usr')");
		mysqli_query($link,"UPDATE `ipd_pat_doc_details` SET `attend_doc`='$adm_doc' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		
		echo "<h5>Doctor Transferred</h5>";
	}
}
if($_POST["type"]==903)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$slno=$_POST['slno'];
	$attend_doc=$_POST['attend_doc'];
	
	$ipd_pat_doc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_doc_transfer` WHERE `slno`='$slno' "));
	if($ipd_pat_doc)
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_doc_transfer_delete`(`patient_id`, `ipd_id`, `attend_doc`, `status`, `date`, `time`, `user`, `del_user`) VALUES ('$uhid','$ipd','$ipd_pat_doc[attend_doc]','$ipd_pat_doc[status]','$ipd_pat_doc[date]','$ipd_pat_doc[time]','$ipd_pat_doc[user]','$usr')");
		
		mysqli_query($link," DELETE FROM `ipd_pat_doc_transfer` WHERE `slno`='$slno' ");
		
		$last_slno=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_doc_transfer` WHERE `slno` IN(SELECT MAX(`slno`) FROM `ipd_pat_doc_transfer` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `slno`<$slno ) "));
		
		$ipd_pat_doc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_doc_transfer` WHERE `slno`='$last_slno[slno]' "));
		
		mysqli_query($link,"INSERT INTO `ipd_pat_doc_transfer_delete`(`patient_id`, `ipd_id`, `attend_doc`, `status`, `date`, `time`, `user`, `del_user`) VALUES ('$uhid','$ipd','$ipd_pat_doc[attend_doc]','$ipd_pat_doc[status]','$ipd_pat_doc[date]','$ipd_pat_doc[time]','$ipd_pat_doc[user]','$usr')");
		
		mysqli_query($link," UPDATE `ipd_pat_doc_details` SET `attend_doc`='$last_slno[attend_doc]' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
		
		mysqli_query($link," DELETE FROM `ipd_pat_doc_transfer` WHERE `slno`='$last_slno[slno]' ");
		
		echo "Deleted";
	}
}
if($_POST["type"]=="pat_consultant_doctor_change")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$attend_doc=$_POST['attend_doc'];
	$user=$_POST['user'];
	
	$ipd_pat_doc=mysqli_fetch_array(mysqli_query($link,"SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	if(mysqli_query($link,"UPDATE `ipd_pat_doc_details` SET `attend_doc`='$attend_doc' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"))
	{
		mysqli_query($link,"UPDATE `ipd_pat_doc_transfer` SET `attend_doc`='$attend_doc' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `status`=1 AND `attend_doc`='$ipd_pat_doc[attend_doc]'");
		
		// Record
		mysqli_query($link, "INSERT INTO `pat_attend_doc_change_record`(`patient_id`, `ipd_id`, `doc_id_old`, `doc_id_new`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$ipd_pat_doc[attend_doc]','$attend_doc','$c_user','$date','$time')");
		
		echo "Saved";
	}
	else
	{
		echo "Failed, try again later.";
	}
}
///////// Consultant Doctor Transfer End

// PAC Start
if($_POST["type"]=="load_pac_form")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$ipd_pat_doc=mysqli_fetch_array(mysqli_query($link,"SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	$consultantdoctorid=$ipd_pat_doc["attend_doc"];
	
	if($consultantdoctorid>0)
	{
		$opd_id=$ipd;
		include("patient_pac_form.php");
	}
	else
	{
		echo "<h4>Save consultant doctor first</h4>";
	}
}
// PAC End

// Guardian Bed Start
if($_POST["type"]=="load_guardian_bed")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$ipd_pat_edit_bed=$_POST['ipd_pat_edit_bed'];
	
	$pat_bed_qry=mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details_guardian` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$pat_bed_temp_qry=mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	
	$pat_bed_num=mysqli_num_rows($pat_bed_qry);
	$pat_bed_temp_num=mysqli_num_rows($pat_bed_temp_qry);
	
	$guardian_add_bed_div_display="display:none;";
	$guardian_save_bed_div_display="";
	if($pat_bed_temp_num==0)
	{
		$guardian_add_bed_div_display="";
		$guardian_save_bed_div_display="display:none;";
	}
	else
	{
		$pat_bed_temp=mysqli_fetch_array($pat_bed_temp_qry);
		
		$ward_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_bed_temp[ward_id]'"));
		$bed_info=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no` FROM `bed_master` WHERE `bed_id`='$pat_bed_temp[bed_id]'"));
		$ward=$ward_info['name'];
		$bed=$bed_info['bed_no'];
	}
	
	if($ipd_pat_edit_bed<3) // 3 = Guardian Bed
	{
		$guardian_add_bed_div_display="";
		$guardian_save_bed_div_display="display:none;";
	}
	
	$pat_bed_det=mysqli_fetch_array($pat_bed_qry);
	
	$bed_transfer_dis="";
	if($pat_bed_det["bed_id"]==1000)
	{
		$bed_transfer_dis="disabled='disabled'";
	}
	
	$dis_det=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($dis_det>0)
	{
		$btndis="disabled='disabled'";
		$btnname="Discharged";
	}
	else
	{
		$btndis="";
		$btnname="Bed Transfer";
	}
	if($pat_bed_num==0)
	{
?>
		<div id="guardian_add_bed_div" style="<?php echo $guardian_add_bed_div_display; ?>">
			<button class="btn btn-new" onclick="nursing_bed_transfer(3)"><i class="icon-edit"></i> Add Bed</button>
		</div>
<?php
	}
	else
	{
		$guardian_bed_alloc_qry=mysqli_query($link,"SELECT * FROM `ipd_bed_alloc_details_guardian` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`=1  ORDER BY `slno` ASC");
		$guardian_bed_alloc_num=mysqli_num_rows($guardian_bed_alloc_qry);
?>
		<div>
			<table class="table table-condensed table-bordered">
				<tr>
					<th>Ward</th>
					<th>Bed No</th>
					<th>Occupied On</th>
					<th>Released On</th>
					<th>User</th>
				</tr>
		<?php
			$n=1;
			while($guardian_bed_alloc=mysqli_fetch_array($guardian_bed_alloc_qry))
			{
				$guardian_bed_leave=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_bed_alloc_details_guardian` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `ward_id`='$guardian_bed_alloc[ward_id]' AND `bed_id`='$guardian_bed_alloc[bed_id]' AND `alloc_type`='0' AND `slno`>'$guardian_bed_alloc[slno]' ORDER BY `slno` ASC LIMIT 1"));
				
				$ward_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$guardian_bed_alloc[ward_id]'"));
				$bed_info=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no` FROM `bed_master` WHERE `bed_id`='$guardian_bed_alloc[bed_id]'"));
				
				$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$guardian_bed_alloc[user]' "));
		?>
				<tr>
					<td><?php echo $ward_info["name"]; ?></td>
					<td><?php echo $bed_info["bed_no"]; ?></td>
					<td><?php echo date("d-m-Y",strtotime($guardian_bed_alloc["date"])); ?></td>
					<td>
						<?php if($guardian_bed_leave){ echo date("d-m-Y",strtotime($guardian_bed_leave["date"])); }else{ echo "Still Occupied"; } ?>
						<?php
							if($guardian_bed_alloc_num==$n)
							{
						?>
								<!--<button class="btn btn-delete" onclick="delete_guardian_bed()" style="float:right;"><i class="icon-remove"></i> Bed Delete</button>-->
								<button class="btn btn-new" onclick="nursing_bed_transfer(4)" style="float:right;margin-right: 5px;"><i class="icon-edit"></i> Bed Edit</button>
						<?php
							}
						?>
					</td>
					<td><?php echo $user_info["name"]; ?></td>
				</tr>
		<?php
				$n++;
			}
		?>
			</table>
		</div>
		<div id="guardian_transfer_bed_div">
			<button class="btn btn-new" onclick="nursing_bed_transfer(5)"><i class="icon-edit"></i> Transfer Bed</button>
		</div>
<?php
	}
?>
		<div id="guardian_save_bed_div" style="<?php echo $guardian_save_bed_div_display; ?>">
			<b>Selected</b><br/>Ward: <?php echo $ward; ?><br/>Bed No: <?php echo $bed; ?>
			<br>
			<input type="text" class="datepicker span2" id="guardian_bed_add_date" value="<?php echo date("Y-m-d"); ?>" readonly>
			<button class="btn btn-save" onclick="save_guardian_bed()"><i class="icon-save"></i> Save</button>
			<button class="btn btn-delete" onclick="clr_bed_assign_guardian()"><i class="icon-remove"></i> Cancel</button>
		</div>
<?php
}
if($_POST["type"]=="guardian_last_bed_date")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$pat_bed_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_bed_alloc_details_guardian` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` DESC "));
	if($pat_bed_det)
	{
		echo $pat_bed_det["date"];
	}
	else
	{
		$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
		
		echo $pat_reg["date"];
	}
}
if($_POST["type"]=="save_guardian_bed")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$val=$_POST['val'];
	$user=$_POST['user'];
	$guardian_bed_add_date=$_POST['guardian_bed_add_date'];
	
	$pat_bed_temp=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$ward_id=$pat_bed_temp["ward_id"];
	$bed_id=$pat_bed_temp["bed_id"];
	
	if($val==3) // New 
	{
		if(mysqli_query($link,"insert into ipd_pat_bed_details_guardian(patient_id,ipd_id,ward_id,bed_id,user,time,date) values('$uhid','$ipd','$ward_id','$bed_id','$user','$time','$date')"))
		{
			mysqli_query($link,"insert into ipd_bed_alloc_details_guardian(patient_id,ipd_id,ward_id,bed_id,alloc_type,user,time,date) values('$uhid','$ipd','$ward_id','$bed_id','1','$user','$time','$guardian_bed_add_date')");
			
			echo "101@Saved";
		}
		else
		{
			echo "404@Failed, try again later";
		}
	}
	if($val==4) // Edit
	{
		$old=mysqli_fetch_array(mysqli_query($link,"SELECT `ward_id`, `bed_id` FROM `ipd_pat_bed_details_guardian` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		
		if(mysqli_query($link,"UPDATE `ipd_pat_bed_details_guardian` SET `ward_id`='$ward_id',`bed_id`='$bed_id' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"))
		{
			mysqli_query($link,"INSERT INTO `ipd_bed_alloc_details_guardian_edit`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`, `edit_user`, `edit_date`, `edit_time`) SELECT `patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`,'$user','$date','$time' FROM `ipd_bed_alloc_details_guardian` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
			
			mysqli_query($link,"UPDATE `ipd_bed_alloc_details_guardian` SET `ward_id`='$ward_id',`bed_id`='$bed_id',`date`='$guardian_bed_add_date' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `ward_id`='$old[ward_id]' AND `bed_id`='$old[bed_id]'");
			
			echo "101@Saved";
		}
		else
		{
			echo "404@Failed, try again later.";
		}
	}
	if($val==5) // Transfer
	{
		$old=mysqli_fetch_array(mysqli_query($link,"SELECT `ward_id`, `bed_id` FROM `ipd_pat_bed_details_guardian` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		
		if(mysqli_query($link,"UPDATE `ipd_pat_bed_details_guardian` SET `ward_id`='$ward_id',`bed_id`='$bed_id' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"))
		{
			mysqli_query($link,"insert into ipd_bed_alloc_details_guardian(patient_id,ipd_id,ward_id,bed_id,alloc_type,user,time,date) values('$uhid','$ipd','$old[ward_id]','$old[bed_id]','0','$user','$time','$guardian_bed_add_date')");
			
			mysqli_query($link,"insert into ipd_bed_alloc_details_guardian(patient_id,ipd_id,ward_id,bed_id,alloc_type,user,time,date) values('$uhid','$ipd','$ward_id','$bed_id','1','$user','$time','$guardian_bed_add_date')");
			
			echo "101@Saved";
		}
		else
		{
			echo "404@Failed, try again later..";
		}
	}
	
	mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid' and ipd_id='$ipd'");
}
if($_POST["type"]=="delete_guardian_bed")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$user=$_POST['user'];
	
	if(mysqli_query($link,"INSERT INTO `ipd_bed_alloc_details_guardian_delete`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`, `delete_user`, `delete_date`, `delete_time`) SELECT `patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`,'$user','$date','$time' FROM `ipd_bed_alloc_details_guardian` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"))
	{
		mysqli_query($link,"delete from ipd_pat_bed_details_guardian where patient_id='$uhid' and ipd_id='$ipd'");
		mysqli_query($link,"delete from ipd_bed_alloc_details_guardian where patient_id='$uhid' and ipd_id='$ipd'");
		
		echo "101@Saved";
	}
	else
	{
		echo "404@Failed, try again later";
	}
}
// Guardian Bed End
?>
