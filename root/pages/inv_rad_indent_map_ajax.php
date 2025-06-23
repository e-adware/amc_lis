<?php
include'../../includes/connection.php';
$type=$_POST['type'];

if($type==1)
{
	$tst=$_POST['tst'];
	$val=$_POST['val'];
	$sub_category_id="6";
	if($val)
	{
	   $q="SELECT `item_id`,`item_name` FROM `item_master` WHERE `item_id` NOT IN (SELECT `item_id` FROM `radiology_maping` WHERE `testid`='$tst') AND `item_name` like '%$val%' AND `sub_category_id`='$sub_category_id' ORDER BY `item_name` LIMIT 0,30";
	}
	else
	{
		$q="SELECT `item_id`,`item_name` FROM `item_master` WHERE `item_id` NOT IN (SELECT `item_id` FROM `radiology_maping` WHERE `testid`='$tst') AND `sub_category_id`='$sub_category_id' ORDER BY `item_name` LIMIT 0,30";
	}
	$q1=mysqli_query($link,$q);
	?>
	
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th width="5%">#</th>
			<th>Id</th>
			<th>Item Name</th>
			<th style="text-align:right;">Qnt &mdash; Add</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q1))
		{
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['item_id'];?></td>
			<td><?php echo $r['item_name'];?></td>
			<td style="text-align:right;">
				<input type="text" class="span1 margin_b" id="qnt<?php echo $r['item_id'];?>" value="1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" maxlength="2" placeholder="Qnt" />
				<button type="button" class="btn btn-primary btn-mini margin_b" onclick="add_map('<?php echo $r['item_id'];?>')"><i class="icon-plus icon-large"></i></button>
			</td>
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}

if($type==2)
{
	$tst=$_POST['tst'];
	$val=$_POST['val'];
	if($val)
	{
		//$q="SELECT a.`item_id`,a.`item_name`,b.`quantity` FROM `item_master` a,`radiology_maping` b WHERE a.`item_id`=b.`item_id` AND a.`category_id`='1' AND a.`item_name` like '%$val%' AND b.`testid`='$tst' ORDER BY a.`item_name`";
		$q="SELECT a.`item_id`,a.`item_name`,b.`quantity` FROM `item_master` a,`radiology_maping` b WHERE a.`item_id`=b.`item_id` AND a.`item_name` like '%$val%' AND b.`testid`='$tst' ORDER BY a.`item_name`";
	}
	else
	{
		$q="SELECT a.`item_id`,a.`item_name`,b.`quantity` FROM `item_master` a,`radiology_maping` b WHERE a.`item_id`=b.`item_id` AND b.`testid`='$tst' ORDER BY a.`item_name`";
	}
	$q1=mysqli_query($link,$q);
	?>
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th width="5%">#</th>
			<th>Id</th>
			<th>Mapped Item Name</th>
			<th>Qnt</th>
			<th style="text-align:right;">Remove</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q1))
		{
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['item_id'];?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $r['quantity'];?></td>
			<td style="text-align:right;">
				<button type="button" class="btn btn-danger btn-mini margin_b" onclick="rem_map('<?php echo $r['item_id'];?>')"><i class="icon-remove icon-large"></i></button>
			</td>
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}

if($type==3)
{
	$id=$_POST['id'];
	$tst=$_POST['tst'];
	$qnt=$_POST['qnt'];
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `radiology_maping` WHERE `item_id`='$id' AND `testid`='$tst'"));
	if($n==0)
	{
		mysqli_query($link,"INSERT INTO `radiology_maping`(`item_id`, `testid`, `quantity`) VALUES ('$id','$tst','$qnt')");
		echo 1;
	}
	else
	{
		echo 0;
	}
}

if($type==4)
{
	$id=$_POST['id'];
	$tst=$_POST['tst'];
	if(mysqli_query($link,"DELETE FROM `radiology_maping` WHERE `item_id`='$id' AND `testid`='$tst'"))
	{
		echo 1;
	}
	else
	{
		echo 0;
	}
}

if($type==5)
{
	$val=mysqli_real_escape_string($link,$_POST['val']);
	
	if($val)
	{
	   $q="select * from item_master where sub_category_id='9' and item_name like '%$val%'";
	}
	else
	{
	   $q="SELECT * FROM item_master where sub_category_id='9' order by item_name";
	}
	//echo $q;
	$q1=mysqli_query($link,$q);
	?>
	
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			
			<th>Id</th>
			<th>Name</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q1))
		{
			
		?>
		<tr style="cursor:pointer"  onclick="val_load_new('<?php echo $r['item_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $r['item_id'];?></td>
			<td><?php echo $r['item_name'];?></td>
	
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}

if($type==6)
{
	$id=$_POST['pid'];
	$q=mysqli_fetch_array(mysqli_query($link,"select * from item_master where item_id='$id'"));
	$val=$id.'@'.$q['item_name'];
	echo $val;
}

if($type==7)
{
	$tst=$_POST['tst'];
	
	$val=array();
	$pars="";
	$j=1;
	$sm=mysqli_fetch_assoc(mysqli_query($link,"SELECT b.`Name` FROM `TestSample` a, `Sample` b WHERE a.`SampleId`=b.`ID` AND a.`TestId`='$tst'"));
	$q=mysqli_query($link,"SELECT b.`Name` FROM `Testparameter` a, `Parameter_old` b WHERE a.`ParamaterId`=b.`ID` AND a.`TestId`='$tst' ORDER BY a.`sequence` ");
	while($r=mysqli_fetch_array($q))
	{
		if($j<=9)
		{
			$j="0".$j;
		}
		if($pars)
		{
			$pars.="<br/>".$j.". &nbsp; ".$r['Name'];
		}
		else
		{
			$pars=$j.". &nbsp; ".$r['Name'];
		}
		$j++;
	}
	$val['sample']=$sm['Name'];
	$val['params']=$pars;
	echo json_encode($val);
}

if($type==999)
{
	$id=$_POST['id'];
}
?>
