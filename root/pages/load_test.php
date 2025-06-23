<?php
include("../../includes/connection.php");
$dname=$_POST['val'];
?>
<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
<th>Test Name</th>
<?php


if($dname)
{

	$d=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `testmaster` where `testname` like '$dname%' order by `testname`");
}
else
{
	$d=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `testmaster` order by `testname`");
}

$i=1;
while($d1=mysqli_fetch_array($d))
{
	?>
	<tr onclick="doc_load('<?php echo $d1['testid'];?>','<?php echo $d1['testname'];?>','<?php echo $d1['rate'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
		<td>
			<?php echo $d1['testname'];?>
			<div <?php echo "id=dvdoc".$i;?> style="display:none;">
			<?php echo "#".$d1['testid']."#".$d1['testname']."#".$d1['rate'];?>
			</div>
		</td>
    </tr>
    <?php
    $i++;
}
?>
</table>
