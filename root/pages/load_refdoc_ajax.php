<?php
include("../../includes/connection.php");

$dname=$_POST['val'];
if(trim($_POST['type'])=="opd")
{

?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
	<th>ID</th><th>Doctor Name</th>
<?php
	
	if($dname)
	{

		$d=mysqli_query($link, "select * from refbydoctor_master where ref_name like '%$dname%'  order by ref_name");
	}
	else
	{
		$d=mysqli_query($link, "select * from refbydoctor_master where ref_name='Self' order by ref_name");
	}
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		?>
		<tr onclick="doc_load('<?php echo $d1['refbydoctorid'];?>','<?php echo $d1['ref_name'];?>','<?php echo $spec['Name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>><td>
		<?php echo $d1['refbydoctorid'];?></td><td><?php echo $d1['ref_name'].' / '.$d1['qualification'];?>
		<div <?php echo "id=dvdoc".$i;?> style="display:none;">
		<?php echo "#".$d1['refbydoctorid']."#".$d1['ref_name']."#".$d1['Name'];?>
		</div>
		</td></tr>
	<?php
		$i++;
	}
	?>
	</table>
<?php
}else if($_POST['type']=="lab")
{

?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
	<th>Doctor Id</th><th>Doctor Name</th>
	<?php

	if($dname)
	{

		$d=mysqli_query($link, "select * from refbydoctor_master where ref_name like '%$dname%'  order by ref_name");
		
	}
	else
	{
		$d=mysqli_query($link, "select * from refbydoctor_master where ref_name='Self' order by ref_name");
	}
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		?>
		<tr onclick="labdoc_load('<?php echo $d1['refbydoctorid'];?>','<?php echo $d1['ref_name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>><td>
		<?php echo $d1['refbydoctorid'];?></td><td><?php echo $d1['ref_name'].' / '.$d1['qualification'];?>
		<div <?php echo "id=dvdoc".$i;?> style="display:none;">
		<?php echo "#".$d1['refbydoctorid']."#".$d1['ref_name'];?>
		</div>
		</td></tr>
		<?php
		$i++;
	}
	?>
	</table>
<?php
}else if($_POST['type']=="ipd" || $_POST['type']=="casualty")
{

?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
	<th>Doctor Id</th><th>Doctor Name</th>
	<?php

	if($dname)
	{

		$d=mysqli_query($link, "select * from refbydoctor_master where ref_name like '%$dname%'  order by ref_name");
	}
	else
	{
		$d=mysqli_query($link, "select * from refbydoctor_master where ref_name='Self' order by ref_name");
	}
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		?>
		<tr onclick="doc_load('<?php echo $d1['refbydoctorid'];?>','<?php echo $d1['ref_name'];?>','<?php echo $spec['Name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>><td>
		<?php echo $d1['refbydoctorid'];?></td><td><?php echo $d1['ref_name'].' / '.$d1['qualification'];?>
		<div <?php echo "id=dvdoc".$i;?> style="display:none;">
		<?php echo "#".$d1['refbydoctorid']."#".$d1['ref_name'];?>
		</div>
		</td></tr>
		<?php
		$i++;
	}
	?>
	</table>
<?php
}
?>
