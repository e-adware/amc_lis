<?php
include("../../includes/connection.php");

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

if($_POST["type"]=="view_timer")
{
?>
	<table class="table" id="pay_timer_tbl">
		<tr>
			<th>#</th>
			<th>Payment Date</th>
			<th></th>
		</tr>
<?php
	$pay_time_qry=mysqli_query($link, " SELECT * FROM `reg_master` ORDER BY `slno` ");
	$pay_time_num=mysqli_num_rows($pay_time_qry);
	if($pay_time_num>0)
	{
		$n=1;
		$z=1;
		while($pay_time=mysqli_fetch_array($pay_time_qry))
		{
			$input_element_dis="disabled";
			if($pay_time["val"]=="402")
			{
				$input_element_dis="";
				$z++;
			}
			if($z>2)
			{
				$input_element_dis="disabled";
			}
			
	?>
		<tr>
			<td><?php echo $n; ?></td>
			<td>
				<input type="text" class="datepicker span2" id="p_date<?php echo $pay_time["slno"]; ?>" value="<?php echo convert_date($pay_time["p_date"]); ?>" disabled style="border: 0;" >
		<?php
			if($pay_time["val"]=="402")
			{
		?>
				<input type="text" id="password<?php echo $pay_time["slno"]; ?>" placeholder="Enter OTP" <?php echo $input_element_dis; ?> >
				<button class="btn btn-success" onClick="check_password('<?php echo $pay_time["slno"]; ?>')" <?php echo $input_element_dis; ?> style="margin-bottom: 1%;">Save</button>
		<?php
			}else
			{
				echo '<span style="font-size: 14px;font-weight: bold;">Payment Done</span>';
			}
		?>
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


if($_POST["type"]=="check_password")
{
	$slno=$_POST["slno"];
	$password=$_POST["password"];
	
	$pass=md5($password);
	
	$check_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `reg_master` WHERE `slno`='$slno' AND `data1`='$pass' "));
	if($check_data)
	{
		mysqli_query($link, " UPDATE `reg_master` SET `val`='202' WHERE `slno`='$slno' ");
		
		echo "202"; // Success
	}else
	{
		echo "404"; // Error
	}
}

?>
