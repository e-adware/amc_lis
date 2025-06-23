<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");
$type=$_POST['type'];


if($type=="save_sal_component")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	if($id>0)
	{
		
	}
	else
	{
		mysqli_query($link,"INSERT INTO `salary_component`(`name`) VALUES ('$name')");
		echo "Saved";
	}
}

if($type=="load_component")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `salary_component` WHERE `name` like '$srch%' ORDER BY `name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `salary_component` ORDER BY `name`");
	}
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th width="10%">#</th><th>Name</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $i;?></td><td><?php echo $r['name'];?></td>
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}

if($type=="oo")
{
	
}

?>
