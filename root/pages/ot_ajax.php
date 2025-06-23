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


if($_POST["type"]=="load_ot_type")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_type_master` WHERE `type` like '$srch%' ORDER BY `type`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_type_master` ORDER BY `type`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
			<tr>
				<th>SN</th><th>Type</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
			</tr>
			<?php
			$n=1;
			while($r=mysqli_fetch_array($q))
			{
			?>
			<tr>
				<td><?php echo $n;?></td><td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['type_id'];?>')"><?php echo $r['type'];?></td>
				<!--<td><i class="icon-remove icon-large" onclick="confirmm('<?php echo $r['type_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>-->
				<td><button type="button" class="btn btn-mini" disabled><i class="icon-remove icon-large" style="color:#bb0000;cursor:pointer;"></i></button></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
	<?php
}


if($_POST["type"]=="save_ot_resource_type")
{
	$id=$_POST['id'];
	$tname=$_POST['tname'];
	$lin=$_POST['lin'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_type_master` WHERE `type_id`='$id'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `ot_type_master` SET `type`='$tname', `link`='$lin' WHERE `type_id`='$id'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ot_type_master`(`type_id`, `type`, `link`) VALUES ('$id','$tname','$lin')");
		echo "Saved";
	}
}

if($_POST["type"]=="load_ot_type_details")
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `type`,`link` FROM `ot_type_master` WHERE `type_id`='$id'"));
	echo $id."#@#".$d['type']."#@#".$d['link']."#@#";
}

if($_POST["type"]=="ot_pat_list")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	
	$q="SELECT DISTINCT a.`patient_id`,a.`ipd_id`,a.`scheduled`,a.`schedule_id` FROM `ot_book` a, `ot_schedule` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.ipd_id AND b.`leaved`='0' ORDER BY a.`ot_date` DESC LIMIT 0,50";
	if($uhid)
	{
		if(strlen($uhid)>2)
		{
			$q="SELECT * FROM `ot_book` WHERE `patient_id` like '$uhid%' ORDER BY `ot_date` DESC";
		}
	}
	if($ipd)
	{
		if(strlen($ipd)>2)
		{
			$q="SELECT * FROM `ot_book` WHERE `ipd_id` like '$ipd%' ORDER BY `ot_date` DESC LIMIT 0,20";
		}
	}
	if($name)
	{
		if(strlen($name)>2)
		{
			$q="SELECT * FROM `ot_book` WHERE `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$name%') ORDER BY `ot_date` DESC LIMIT 0,20";
		}
	}
	if($dat)
	{
		$q="SELECT * FROM `ot_book` WHERE `ot_date`='$dat' ORDER BY `ot_date` DESC LIMIT 0,20";
	}
	//echo $q;
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th></th>
				<th>IPD ID</th>
				<th>Name</th>
				<th>Sex</th>
				<th>Status</th>
				<th>Encounter</th>
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				$p_typ=mysqli_fetch_array(mysqli_query($link,"SELECT a.`type`,b.`p_type` FROM `uhid_and_opdid` a, `patient_type_master` b WHERE a.`patient_id`='$r[patient_id]' AND a.`opd_id`='$r[ipd_id]' AND a.`type`=b.`p_type_id`"));
				
				$ot_entry=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$r[patient_id]' AND `ipd_id`='$r[ipd_id]'"));
				if($ot_entry)
				{
					$btn_name="Added to Bill";
				}else
				{
					$btn_name="Add to Bill";
				}
				
				$lv=mysqli_fetch_array(mysqli_query($link,"SELECT `leaved` FROM `ot_schedule` WHERE `schedule_id`='$r[schedule_id]' AND `patient_id`='$r[patient_id]' AND `ipd_id`='$r[ipd_id]'"));
				if($lv['leaved']==1)
				{
					$sh="Leaved";
				}
				else if($lv['leaved']==0)
				{
					if($r['scheduled']=="0")
					{
						$sh="Not scheduled";
					}
					if($r['scheduled']=="1")
					{
						$sh="Scheduled";
					}
				}
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['ipd_id'];?>','<?php echo $r['schedule_id'];?>')" style="cursor:pointer;">
					<td><button type="button" class="btn btn-mini btn-info"><?php echo $btn_name; ?></button></td>
					<td><?php echo $r['ipd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $sh;?></td>
					<td><?php echo $p_typ['p_type'];?></td>
				</tr>
			<?php
			}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="ot_schedule_reason")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
}

?>
