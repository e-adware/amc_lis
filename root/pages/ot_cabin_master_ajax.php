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
	$id=$_POST['id'];
	$name=$_POST['name'];
	
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `ot_cabin_master` SET `ot_cabin_name`='$name' WHERE `ot_cabin_id`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `ot_cabin_master`(`ot_cabin_name`) VALUES ('$name')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
	
}

if($_POST["type"]==2)
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_cabin_master` WHERE `ot_cabin_name` like '$srch%' ORDER BY `ot_cabin_name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_cabin_master` ORDER BY `ot_cabin_name`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
			<tr>
				<th width="10%">#</th><th>Cabin</th>
				<!--<th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>-->
			</tr>
			<?php
			$n=1;
			while($r=mysqli_fetch_array($q))
			{
			?>
			<tr>
				<td><?php echo $n;?></td>
				<td class="nm" style="cursor:pointer;" onclick="det('<?php echo $r['ot_cabin_id'];?>')"><?php echo $r['ot_cabin_name'];?></td>
				<!--<td><i class="icon-remove icon-large" onclick="confirmm('<?php echo $r['ot_cabin_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>-->
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
	<?php
}

if($_POST["type"]==3)
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_cabin_name` FROM `ot_cabin_master` WHERE `ot_cabin_id`='$id'"));
	echo $id."#@#".$d['ot_cabin_name']."#@#";
}

if($_POST["type"]==4)
{
	echo $id=$_POST['id'];
	
}

if($_POST["type"]=="oooo")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
}
?>
