<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="cntermaster_id") //// cntermaster_id
{
	echo $vid=nextId("HG","health_guide","hguide_id","101");
}
if($_POST["type"]=="cntermaster") //// cntermaster
{
	$val=$_POST['val'];
	if($val)
	{
		$q="SELECT * FROM `health_guide` WHERE `name` like '$val%'";
	}
	else
	{
		$q="SELECT * FROM `health_guide` order by `name`";
	}
	$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
		if($qrpdct1['hguide_id']=="HG101"){ $del_dis="disabled"; }else{ $del_dis=""; }
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['hguide_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['hguide_id'];?></td>
			<td><?php echo $qrpdct1['name'];?></td>
			<td><button class="btn btn-mini btn-default" onclick="delete_data('<?php echo $qrpdct1['hguide_id'];?>')" <?php echo $del_dis; ?>> <img height="15" width="15" src="../images/delete.ico"/></button></td>
		</tr>
<?php	
		$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="cntermaster_load") //// cntermaster_load
{
	$tid=$_POST['doid1'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select * from health_guide where hguide_id='$tid' "));
	$val=$tid.'#'.$qrm['name'].'#'.$qrm['address'].'#'.$qrm['phone'].'#'.$qrm['email'].'#'.$qrm['sguide_id'];
	echo $val;
}

if($_POST["type"]=="cntermaster_insert")
{
	$cid=$_POST['cid'];
	 $cname=$_POST['cname'];
	 $cname=str_replace("'","''",$cname);
	 $address=$_POST['address'];
	 $address=str_replace("'","''",$address);
	 $phone=$_POST['phone'];
	 $email=$_POST['email'];
	 $sguide_id=$_POST['sguide_id'];
	 
	$qr=mysqli_fetch_array(mysqli_query($link, "select hguide_id from health_guide where hguide_id='$cid'"));
	if($qr)
	{	
		mysqli_query($link, "update health_guide set name='$cname',address='$address',phone='$phone',email='$email',sguide_id='$sguide_id' where hguide_id='$cid'");	
	}
	else
	{
		mysqli_query($link, "insert into health_guide(hguide_id,name,address,phone,email,sguide_id) values('$cid','$cname','$address','$phone','$email','$sguide_id')");
	}
}

if($_POST["type"]=="cntermaster_delete")
{
	$subp=$_POST['subp'];
	mysqli_query($GLOBALS["___mysqli_ston"], " DELETE FROM `health_guide` WHERE `hguide_id`='$subp' ");
}
if($_POST["type"]=="super_cntermaster_id") //// cntermaster_id
{
	echo $vid=nextId("G","super_health_guide","hguide_id","1");
}
if($_POST["type"]=="super_cntermaster") //// cntermaster
{
	$val=$_POST['val'];
	if($val)
	{
		$q="SELECT * FROM `super_health_guide` WHERE `name` like '$val%'";
	}
	else
	{
		$q="SELECT * FROM `super_health_guide` order by `name`";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
		//if($qrpdct1['hguide_id']=="HG101"){ $del_dis="disabled"; }else{ $del_dis=""; }
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['hguide_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['hguide_id'];?></td>
			<td><?php echo $qrpdct1['name'];?></td>
			<td><button class="btn btn-mini btn-default" onclick="delete_data('<?php echo $qrpdct1['hguide_id'];?>')" <?php echo $del_dis; ?>> <img height="15" width="15" src="../images/delete.ico"/></button></td>
		</tr>
<?php	
		$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="super_cntermaster_load") //// cntermaster_load
{
	$tid=$_POST['doid1'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select * from super_health_guide where hguide_id='$tid' "));
	$val=$tid.'#'.$qrm['name'].'#'.$qrm['address'].'#'.$qrm['phone'].'#'.$qrm['email'];
	echo $val;
}

if($_POST["type"]=="super_cntermaster_insert")
{
	$cid=$_POST['cid'];
	 $cname=$_POST['cname'];
	 $cname=str_replace("'","''",$cname);
	 $address=$_POST['address'];
	 $address=str_replace("'","''",$address);
	 $phone=$_POST['phone'];
	 $email=$_POST['email'];
	 
	$qr=mysqli_fetch_array(mysqli_query($link, "select super_hguide_id from health_guide where hguide_id='$cid'"));
	if($qr)
	{	
		mysqli_query($link, "update super_health_guide set name='$cname',address='$address',phone='$phone',email='$email' where hguide_id='$cid'");	
	}
	else
	{
		mysqli_query($link, "insert into super_health_guide(hguide_id,name,address,phone,email) values('$cid','$cname','$address','$phone','$email')");
	}
}

if($_POST["type"]=="super_cntermaster_delete")
{
	$subp=$_POST['subp'];
	mysqli_query($link, " DELETE FROM `super_health_guide` WHERE `hguide_id`='$subp' ");
}

?>
