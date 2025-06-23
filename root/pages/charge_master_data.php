<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$levelid=$emp_info["levelid"];

$date=date("Y-m-d");
$time=date("H:i:s");


if($_POST["type"]=="charge_id")
{
	echo $vid=nextId("","charge_master","charge_id","101");
}
if($_POST["type"]=="charges_load")
{
	$branch_id=$_POST['branch_id'];
	$val=$_POST['val'];
	$cat=$_POST['cat'];
	
	$q=" SELECT * FROM `charge_master` WHERE `branch_id`='$branch_id' ";
	
	if($cat>0)
	{
		$q.=" AND `group_id`='$cat' ";
	}
	
	if($val)
	{
		$q.=" AND `charge_name` like '%$val%' ";
	}
	
	$q.=" order by `charge_name` ASC";
	$qrpdct=mysqli_query($link, $q);
?>
<table class="table table-striped table-bordered table-condensed">
	<tr>
		<th>Charge ID</th>
		<th>Charge Name</th>
		<th>Amount</th>
	<?php if($levelid==1){ ?>
		<th>
			<img height="15" width="15" src="../images/delete.ico"/>
		</th>
	<?php } ?>
	</tr>
<?php
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
		if($qrpdct1["group_id"]=='141' || $qrpdct1["group_id"]=='142' || $qrpdct1["group_id"]=='144'){ $dis_del="disabled"; }else{ $dis_del=""; }
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['charge_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['charge_id'];?></td>
			<td><?php echo $qrpdct1['charge_name'];?></td>
			<td><?php echo $qrpdct1['amount'];?></td>
		<?php if($levelid==1){ ?>
			<td>
				<button class="btn btn-mini btn-default" onClick="delete_data('<?php echo $qrpdct1['charge_id'];?>')" <?php echo $dis_del; ?>>
					<img height="15" width="15" src="../images/delete.ico"/>
				</button>
			</td>
		<?php } ?>
		</tr>
<?php	
	$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="charge_load")
{
	$charge_id=$_POST['doid1'];
	$qttl=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `charge_master` WHERE `charge_id`='$charge_id' "));
	$val=$charge_id.'@#$'.$qttl['charge_name'].'@#$'.$qttl['group_id'].'@#$'.$qttl['charge_type'].'@#$'.$qttl['amount'].'@#$'.$qttl['group_id'].'@#$'.$qttl['doc_link'];
	if($qttl['group_id']=='141')
	{
		$bd=mysqli_fetch_array(mysqli_query($link, " SELECT `bed_no` FROM `bed_master` WHERE `charge_id`='$charge_id' "));
		$val.='@#$'.$bd['bed_no'];
	}
	else
	{
		$val.='@#$0';
	}
	$val.='@#$'.$qttl['branch_id'];
	echo $val;
}
if($_POST["type"]=="charges_save")
{
	$branch_id=$_POST['branch_id'];
	$charge_id=$_POST['charge_id'];
	$charge_name=mysqli_real_escape_string($link, $_POST['charge_name']);
	$group_id=$_POST['group_id'];
	$amount=$_POST['amount'];
	$user=$_POST['user'];
	$doc_link=$_POST['doc_link'];
	$q_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `charge_master` WHERE `charge_id`='$charge_id' "));
	if($q_num==0)
	{
		mysqli_query($link, " INSERT INTO `charge_master`(`charge_id`, `charge_name`, `group_id`, `branch_id`, `charge_type`, `amount`, `user`, `doc_link`) VALUES ('$charge_id','$charge_name','$group_id','$branch_id','0','$amount','$user','$doc_link') ");
	}else
	{
		mysqli_query($link, " UPDATE `charge_master` SET `charge_name`='$charge_name',`group_id`='$group_id',`branch_id`='$branch_id',`amount`='$amount',`user`='$user',`doc_link`='$doc_link' WHERE `charge_id`='$charge_id' ");
		
		$check_bed=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `bed_master` WHERE `charge_id`='$charge_id' "));
		if($check_bed)
		{
			mysqli_query($link, " UPDATE `bed_master` SET `charges`='$amount' WHERE `charge_id`='$charge_id' ");
		}
	}
}
if($_POST["type"]=="charges_delete")
{
	$charge_id=$_POST['smplid'];
	
	$check_entry=mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `ipd_pat_service_details` WHERE `service_id`='$charge_id' LIMIT 1"));
	
	if(!$check_entry)
	{
		if(mysqli_query($link, " DELETE FROM `charge_master` WHERE `charge_id`='$charge_id' "))
		{
			echo "Deleted";
		}
		else
		{
			echo "Failed, try again later";
		}
	}
	else
	{
		echo "Can't delete, this charge has been used.";
	}
}
?>
