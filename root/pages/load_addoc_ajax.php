<?php
include("../../includes/connection.php");
$dname=$_POST['val'];
?>
<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
<th>Doctor Id</th><th>Doctor Name</th>
<?php

if($dname)
{
	$d=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `Name` like '%$dname%' ORDER BY `Name`");
}
else
{
	$d=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
}
$i=1;
while($d1=mysqli_fetch_array($d))
{
	?>
	<tr onclick="addoc_load('<?php echo $d1['consultantdoctorid'];?>','<?php echo $d1['Name'];?>')" style="cursor:pointer" <?php echo "id=addoc".$i;?>><td>
    <?php echo $d1['consultantdoctorid'];?></td><td><?php echo $d1['Name'];?>
    <div <?php echo "id=addvdoc".$i;?> style="display:none;">
    <?php echo "#".$d1['consultantdoctorid']."#".$d1['Name'];?>
    </div>
    </td></tr>
    <?php
    $i++;
}
?>
</table>
