<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="load_sel_medicine")
{
	$vall=$_POST["val"];
	$val=explode("###",$vall);
	$n=1;
	if($vall)
	{
?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th>
			<th>Drug Name</th>
			<th>Quantity</th>
			<th>Remove</th>
		</tr>
<?php
	foreach($val as $val)
	{
		if($val)
		{
			$item=explode("@@",$val);
			$item_name=mysqli_fetch_array(mysqli_query($link," SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$item[0]' "));
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $item_name["item_name"]; ?></td>
				<td><?php echo $item[1]; ?></td>
				<td>
					<span onClick="delete_sel_item('<?php echo $item[0];?>','<?php echo $item[1];?>')" class="" style="cursor:pointer;"><img height="15" width="15" src="../images/delete.ico"/></span>
				</td>
			</tr>
		<?php
			$n++;
		}
	}
?>
	</table>
<?php
	}
}

if($_POST["type"]=="save_medicine")
{
	$vall=$_POST["val"];
	$uhid=$_POST["uhid"];
	$opd=$_POST["opd"];
	$user=$_POST["user"];
	
	// Delete
	mysqli_query($link," DELETE FROM `medicine_check` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' ");
	
	$val=explode("###",$vall);
	foreach($val as $val)
	{
		if($val)
		{
			$item=explode("@@",$val);
			//$item_name=mysqli_fetch_array(mysqli_query($link," SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$item[0]' "));
			
			mysqli_query($link," INSERT INTO `medicine_check`(`patient_id`, `opd_id`, `item_code`, `dosage`, `units`, `frequency`, `start_date`, `duration`, `unit_days`, `total_days`, `instruction`, `sos`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$item[0]','0','','0','0000-00-00','','','$item[1]','0','','$date','$time','$user') ");
			
		}
	}
}

?>
