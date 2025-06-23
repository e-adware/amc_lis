<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
$date=date("Y-m-d");
$time=date("H:i:s");

$rupees_symbol="&#x20b9; ";

if($_POST["type"]=="bed_master_bed_id")
{
	echo $vid=nextId("","bed_master","bed_id","1001");
}

if($_POST["type"]=="load_ward")
{
	$branch_id=$_POST['branch_id'];
	
	echo "<option value='0'>--Select--</option>";
	
	$q=mysqli_query($link,"SELECT * FROM `ward_master` WHERE `branch_id`='$branch_id' ORDER BY `name`");
	while($r=mysqli_fetch_array($q))
	{
?>
	<option value="<?php echo $r['ward_id'];?>"><?php echo $r['name'];?></option>
<?php
	}
}
if($_POST["type"]=="save_bed")
{
	$bed_id=$_POST['bed_id'];
	$ward=$_POST['ward'];
	$room=$_POST['room'];
	$bed=$_POST['bed'];
	$charge=$_POST['charge'];
	$othr_chrge=$_POST['othr_chrge'];
	$sel_area_chrg=$_POST['sel_area_chrg'];
	$user=$_POST['user'];
	
	//~ $same_bed_same_room=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `bed_master` WHERE `ward_id`='$ward' AND `room_id`='$room' AND `bed_no`='$bed' "));
	//~ if(!$same_bed_same_room)
	//~ {
	
		mysqli_query($link," DELETE FROM `bed_other_charge` WHERE `bed_id`='$bed_id' ");
		$othr_chrge=explode("@@",$othr_chrge);
		foreach($othr_chrge as $chrg)
		{
			if($chrg)
			{
				mysqli_query($link," INSERT INTO `bed_other_charge`(`bed_id`, `charge_id`) VALUES ('$bed_id','$chrg') ");
			}
		}
		
		mysqli_query($link," DELETE FROM `bed_cleaning` WHERE `bed_id`='$bed_id' ");
		$sval=explode("###",$sel_area_chrg);
		foreach($sval as $chrg)
		{
			if($chrg)
			{
				$chrg=explode("@@",$chrg);
				$item=$chrg[0];
				$item_id= str_replace('i', '', $item);
				$item_mat=$chrg[1];
				$item_mat_id= str_replace('m', '', $item_mat);
				$freq=$chrg[2];
				$freq_id= str_replace('f', '', $freq);
				
				mysqli_query($link," INSERT INTO `bed_cleaning`(`bed_id`, `item_id`, `item_mat_id`, `frequency`) VALUES ('$bed_id','$item_id','$item_mat_id','$freq_id') ");
			}
		}
		
		$ward_name=mysqli_fetch_array(mysqli_query($link," SELECT `name`,`branch_id` FROM `ward_master` WHERE `ward_id`='$ward' "));
		$charge_name=$ward_name["name"]."(Bed No ".$bed.")";
		$branch_id=$ward_name["branch_id"];
		
		$q_qry=mysqli_query($link,"SELECT * FROM `bed_master` WHERE `bed_id`='$bed_id'");
		$q_num=mysqli_num_rows($q_qry);
		$q_val=mysqli_fetch_array($q_qry);
		if($q_num>0)
		{
			if(mysqli_query($link,"UPDATE `bed_master` SET `ward_id`='$ward',`room_id`='$room',`bed_no`='$bed',`charges`='$charge' WHERE `bed_id`='$bed_id'"))
			{
				mysqli_query($link," UPDATE `charge_master` SET `charge_name`='$charge_name',`amount`='$charge' WHERE `charge_id`='$q_val[charge_id]' ");
				
				echo "Updated@1";
			}
			else
			{
				echo "Error@0";
			}
		}
		else
		{
			$max_charge=mysqli_fetch_array(mysqli_query($link," SELECT MAX(`charge_id`) AS MAX FROM `charge_master` "));
			$charge_id=$max_charge["MAX"]+1;
			
			if(mysqli_query($link," INSERT INTO `charge_master`(`charge_id`, `charge_name`, `group_id`, `branch_id`, `charge_type`, `amount`, `user`, `doc_link`) VALUES ('$charge_id','$charge_name','141','$branch_id','0','$charge','$user','0') "))
			{
				mysqli_query($link,"INSERT INTO `bed_master`(`bed_id`, `ward_id`, `room_id`, `bed_no`, `charges`, `status`, `reason`, `remarks`, `charge_id`, `sequence`) VALUES ('$bed_id','$ward','$room','$bed','$charge','0','','','$charge_id','0')");
				
				echo "Saved@1";
			}
			else
			{
				echo "Error@0";
			}		
		}
	//~ }else
	//~ {
		//~ echo "Bed number ".$bed." is already exist in the room@0";
	//~ }
}

if($_POST["type"]=="bed_add_other_charge")
{
	$sval=$_POST['sval'];
	if($sval)
	{
?>
	<table class="table">
<?php
	$sval=explode("@@",$sval);
	foreach($sval as $chrg)
	{
		if($chrg)
		{
			$d=mysqli_fetch_array(mysqli_query($link," SELECT `charge_name`,`amount` FROM `charge_master` WHERE `charge_id`='$chrg' "));
		?>
			<tr>
				<td><?php echo $d["charge_name"]; ?></td>
				<td>
					<?php echo $rupees_symbol.$d["amount"]; ?>
					<span onClick="delete_data('<?php echo $chrg;?>')" class="text-right" style="cursor:pointer;"><img height="15" width="15" src="../images/delete.ico"/></span>
				</td>
			</tr>
		<?php
		}
	}
	?>
	</table>
	<?php
	}
}
if($_POST["type"]=="area_add_other_charge")
{
	$sval=$_POST['sval'];
	if($sval)
	{
?>
	<table class="table">
		<tr>
			<th>Item Name</th>
			<th>Material Name</th>
			<th>Frequency</th>
		</tr>
<?php
	$sval=explode("###",$sval);
	foreach($sval as $chrg)
	{
		if($chrg)
		{
			$chrg=explode("@@",$chrg);
			$item=$chrg[0];
			$item_id= str_replace('i', '', $item);
			$item_mat=$chrg[1];
			$item_mat_id= str_replace('m', '', $item_mat);
			$freq=$chrg[2];
			$freq_id= str_replace('f', '', $freq);
			if($freq_id==1)
			{
				$sel_freq="Once a day";
			}
			if($freq_id==2)
			{
				$sel_freq="Twice a day";
			}
			if($freq_id==3)
			{
				$sel_freq="Thice a day";
			}
			if($freq_id==7)
			{
				$sel_freq="Weekly";
			}
			if($freq_id==30)
			{
				$sel_freq="Monthly";
			}
			
			$itm=mysqli_fetch_array(mysqli_query($link," SELECT `item_name` FROM `cleaning_item_master` WHERE `item_id`='$item_id' "));
			$itm_mat=mysqli_fetch_array(mysqli_query($link," SELECT `item_mat_name` FROM `cleaning_material_master` WHERE `item_mat_id`='$item_mat_id' "));
		?>
			<tr>
				<td><?php echo $itm["item_name"]; ?></td>
				<td><?php echo $itm_mat["item_mat_name"]; ?></td>
				<td>
					<?php echo $sel_freq; ?>
					<span onClick="delete_sel_area('<?php echo $item_id;?>','<?php echo $item_mat_id;?>','<?php echo $freq_id;?>')" class="text-right" style="cursor:pointer;"><img height="15" width="15" src="../images/delete.ico"/></span>
				</td>
			</tr>
		<?php
		}
	}
	?>
	</table>
	<?php
	}
}

if($_POST["type"]=="load_room_list")
{
	$ward=$_POST['ward'];
	if($ward==0)
	{
		?>
		<select id="room">
			<option value="0">--Select--</option>
		</select>
		<?php
	}
	else
	{
		?>
		<select id="room" onChange="ward_change()">
			<option value="0">--Select--</option>
			<?php
			$q=mysqli_query($link,"SELECT * FROM `room_master` WHERE `ward_id`='$ward'");
			while($r=mysqli_fetch_array($q))
			{
			?>
			<option value="<?php echo $r['room_id'];?>"><?php echo $r['room_no'];?></option>
			<?php
			}
			?>
		</select>
		<?php
	}
	?>
	<script>
		$("#room").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				{
					$(this).css("border","1px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#bed").focus();
				}
			}
		});
	</script>
	<?php
}

if($_POST["type"]=="load_bed_detail") // gov
{
	$ward=$_POST['ward'];
	$room=$_POST['room'];
	$bed=$_POST['bed'];
	
	$d_qry=mysqli_query($link," SELECT * FROM `bed_master` WHERE `ward_id`='$ward' and `room_id`='$room' and `bed_no`='$bed' ");
	$d_num=mysqli_num_rows($d_qry);
	if($d_num>0)
	{
		$d=mysqli_fetch_array($d_qry);
		
		$othr_qry=mysqli_query($link," SELECT `charge_id` FROM `bed_other_charge` WHERE `bed_id`='$d[bed_id]' ");
		while($othr=mysqli_fetch_array($othr_qry))
		{
			$othr_val.=$othr["charge_id"]."@@";
		}
		
		$house_val_qry=mysqli_query($link," SELECT * FROM `bed_cleaning` WHERE `bed_id`='$d[bed_id]' ");
		while($house_val=mysqli_fetch_array($house_val_qry))
		{
			$house.="###i".$house_val["item_id"]."@@m".$house_val["item_mat_id"]."@@f".$house_val["frequency"];
		}
		
		
		echo $d["bed_id"]."@govin@".$d['ward_id']."@govin@".$d['room_id']."@govin@".$d['bed_no']."@govin@".$d['charges']."@govin@".$othr_val."@govin@".$house."@govin@";
	}else
	{
		echo "0";
	}
}

if($_POST["type"]=="search_load_bed")
{
	$branch_id=$_POST['branch_id'];
	$val=$_POST["val"];
	
	$d_qry=mysqli_query($link," SELECT * FROM `bed_master` WHERE `bed_no` like '$val%' AND `ward_id` IN(SELECT `ward_id` FROM `ward_master` WHERE `branch_id`='$branch_id') ");
?>
	<table class="table">
		<tr>
			<th>Ward</th>
			<th>Room</th>
			<th>Bed</th>
		</tr>
	<?php
		$i=1;
		while($bed=mysqli_fetch_array($d_qry))
		{
			$ward=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$bed[ward_id]' "));
			$room=mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));
		?>
		<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $bed['ward_id'];?>','<?php echo $bed["room_id"];?>','<?php echo $bed["bed_no"];?>')" style="cursor:pointer;">
			<td><?php echo $ward["name"]; ?></td>
			<td><?php echo $room["room_no"]; ?></td>
			<td>
				<?php echo $bed["bed_no"]; ?>
				<input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $bed['ward_id']."@@".$bed["room_id"]."@@".$bed["bed_no"];?>"/>
			</td>
		</tr>
		<?php
			$i++;
		}
	?>
	</table>
<?php
	
}

if($_POST["type"]=="delete_bed")
{
	$bed_id=$_POST["bed_id"];
	
	$bed_charge=mysqli_fetch_array(mysqli_query($link," SELECT `charge_id` FROM `bed_master` WHERE `bed_id`='$bed_id' "));
	
	$alloc_num=mysqli_num_rows(mysqli_query($link," SELECT `slno` FROM `ipd_bed_alloc_details` WHERE `bed_id`='$bed_id' "));
	$serv_num=mysqli_num_rows(mysqli_query($link," SELECT `slno` FROM `ipd_pat_service_details` WHERE `service_id`='$bed_charge[charge_id]' "));
	
	$tot_num=$alloc_num+$serv_num;
	
	if($tot_num==0)
	{
		mysqli_query($link," DELETE FROM `bed_master` WHERE `bed_id`='$bed_id' ");
		mysqli_query($link," DELETE FROM `bed_other_charge` WHERE `bed_id`='$bed_id' ");
		mysqli_query($link," DELETE FROM `bed_cleaning` WHERE `bed_id`='$bed_id' ");
		
		mysqli_query($link," DELETE FROM `charge_master` WHERE `charge_id`='$bed_charge[charge_id]' ");
		
		echo "Deleted";
	}
	else
	{
		echo "This bed has been used";
	}
}

if($_POST["type"]=="view_bed_det") // gov
{
	$ward=$_POST['ward'];
	$w=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$ward'"));
?>
	<table id="b_det" class="table table-condensed table-bordered">
<?php
		$wrd=mysqli_query($link,"SELECT * FROM `ward_master`");
		while($w=mysqli_fetch_array($wrd))
		{
			?>
			<tr>
			<th colspan="5" style="text-align:center;background:linear-gradient(-90deg, #eeeeee, #aaaaaa);"><?php echo $w['name'];?></th>
			</tr>
			<?php
		$i=1;
		$rm=mysqli_query($link,"SELECT * FROM `room_master` WHERE `ward_id`='$w[ward_id]'");
		$num=mysqli_num_rows($rm);
		while($r=mysqli_fetch_array($rm))
		{
			$bed=mysqli_query($link,"SELECT * FROM `bed_master` WHERE `ward_id`='$w[ward_id]' AND `room_id`='$r[room_id]'");
			$no=mysqli_num_rows($bed);
			if($no>0)
			{
	?>
			<tr>
				<th>#</th><th>Room No</th><th>Bed No</th><th>Bed Charge</th><th>Bed Other Charge</th>
			</tr>
	<?php
			$j=1;
			while($b=mysqli_fetch_array($bed))
			{
			?>
			<tr>
				<?php if($no>0){ echo "<td rowspan='".$no."'>".$j."</td><td rowspan='".$no."'>".$r['room_no']."</td>";}?>
				<td><?php echo $b['bed_no'];?></td>
				<td><?php echo $b['charges'];?></td>
				<td>
	<?php
				$bed_other_charge_qry=mysqli_query($link," SELECT a.* FROM `charge_master` a, `bed_other_charge` b WHERE a.`charge_id`=b.`charge_id` AND b.`bed_id`='$b[bed_id]' ");
				$xx=1;
				while($bed_other_charge=mysqli_fetch_array($bed_other_charge_qry))
				{
					echo "<span style='float:left'>$bed_other_charge[charge_name]</span><span style='float:right'>$bed_other_charge[amount]</span><br>";
					$xx++;
				}
	?>
				</td>
			</tr>
	<?php
			$no=0;
			$j++;
			}
			}
		$num=0;
		$i++;
		}
	}
?>
	</table>
	<?php
}

?>
