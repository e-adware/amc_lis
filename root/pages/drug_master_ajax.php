<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$type=$_POST['type'];

if($type==1)
{
	$val=mysqli_real_escape_string($link,$_POST['val']);
	if($val!='')
	{
		$dis_lst=mysqli_query($link,"select * from drug_master where name like '%$val%' order by name");
	}
	else
	{
		$dis_lst=mysqli_query($link,"select * from drug_master order by name");
	}
	?>
	<table class="table table-report table-condensed table-bordered">
	<tr>
		<th>#</th> <th>Name</th> <th></th>
	</tr>
	<?php
	$i=1;
	while($dis=mysqli_fetch_array($dis_lst))
	{
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $dis[name];?></td>
			<td> 
				<button class="btn btn-mini btn-warning" onclick="update_data(<?php echo $dis[id];?>,'<?php echo $dis[name];?>')"><i class="icon-edit"></i></button> 
				<button class="btn btn-mini btn-danger" onclick="delete_data(<?php echo $dis[id];?>)"><i class="icon-remove"></i></button> 
			</td>
		</tr>
		<?php		
		$i++;
	}
	?>
	</table>
	<?php
}
else if($type==2)
{
	$id=$_POST['id'];
	$name=mysqli_real_escape_string($link,$_POST['name']);
	
	if($id=='')
	{
		mysqli_query($link,"insert into drug_master(name) value('$name')");	
	}
	else
	{
		mysqli_query($link,"update drug_master set name='$name' where id='$id'");
	}
}

else if($type==3)
{
	$val=$_POST['val'];
	mysqli_query($link,"delete from drug_master where id='$val'");
}
?>
