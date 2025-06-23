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
			<th>Notification Date</th>
			<th>Deadline Date</th>
			<th></th>
		</tr>
<?php
	$pay_time_qry=mysqli_query($link, " SELECT * FROM `reg_master` ORDER BY `slno` ");
	$pay_time_num=mysqli_num_rows($pay_time_qry);
	if($pay_time_num>0)
	{
		$n=1;
		while($pay_time=mysqli_fetch_array($pay_time_qry))
		{
			$input_element_dis="";
			if($pay_time["val"]!="402")
			{
				$input_element_dis="disabled";
			}
	?>
		<tr>
			<td><?php echo $n; ?></td>
			<td>
				<input type="text" class="datepicker" id="p_date<?php echo $pay_time["slno"]; ?>" value="<?php echo $pay_time["p_date"]; ?>" <?php echo $input_element_dis; ?> >
			</td>
			<td>
				<input type="text" class="datepicker" id="n_date<?php echo $pay_time["slno"]; ?>" value="<?php echo $pay_time["n_date"]; ?>" <?php echo $input_element_dis; ?>>
			</td>
			<td>
				<input type="text" class="datepicker" id="d_date<?php echo $pay_time["slno"]; ?>" value="<?php echo $pay_time["d_date"]; ?>" <?php echo $input_element_dis; ?>>
			</td>
			<td>
				<button class="btn btn-info" onClick="save_timer('<?php echo $pay_time["slno"]; ?>')" <?php echo $input_element_dis; ?>>Update</button>
				<button class="btn btn-danger" onClick="delete_timer('<?php echo $pay_time["slno"]; ?>')" <?php echo $input_element_dis; ?>>Delete</button>
				<button class="btn btn-success" onClick="generate_password('<?php echo $pay_time["slno"]; ?>')" <?php echo $input_element_dis; ?>>G Password</button>
			</td>
		</tr>
	<?php
			$n++;
		}
		$max_val=mysqli_fetch_array(mysqli_query($link, " SELECT MAX(`slno`) AS `mx` FROM `reg_master` "));
		$max_slno=$max_val["mx"]+1;
?>
		<tr id="add_more_timer_tr">
			<td colspan="5">
				<button class="btn btn-warning" onClick="add_more_timer('<?php echo $max_slno; ?>','<?php echo $pay_time_num; ?>')">Add More Timer</button>
			</td>
		</tr>
<?php
	}else
	{
?>
		<tr>
			<td>1</td>
			<td>
				<input type="text" class="datepicker" id="p_date0" value=""> <!-- <?php echo date("Y-m-d"); ?> -->
			</td>
			<td>
				<input type="text" class="datepicker" id="n_date0" value="">
			</td>
			<td>
				<input type="text" class="datepicker" id="d_date0" value="">
			</td>
			<td>
				<button class="btn btn-info" onClick="save_timer(0)">Save</button>
			</td>
		</tr>
<?php	
	}
?>
	</table>
<?php
}

if($_POST["type"]=="save_timer")
{
	$p_date=$_POST["p_date"];
	$n_date=$_POST["n_date"];
	$d_date=$_POST["d_date"];
	$val=$_POST["val"];
	if($val==0)
	{
		mysqli_query($link, " INSERT INTO `reg_master`(`p_date`, `n_date`, `d_date`, `val`, `data1`) VALUES ('$p_date','$n_date','$d_date','402','') ");
	}else
	{
		mysqli_query($link, " UPDATE `reg_master` SET `p_date`='$p_date',`n_date`='$n_date',`d_date`='$d_date' WHERE `slno`='$val' ");
	}
}

if($_POST["type"]=="delete_timer")
{
	$slno=$_POST["slno"];
	if($slno>0)
	{
		mysqli_query($link, " DELETE FROM `reg_master` WHERE `slno`='$slno' ");
	}
}

if($_POST["type"]=="generate_password")
{
	$slno=$_POST["slno"];
	
	$a = mt_rand(10000000,99999999);
	$pass=md5($a);
	
	mysqli_query($link, " UPDATE `reg_master` SET `data1`='$pass' WHERE `slno`='$slno' ");
	
	echo $a;
}

?>
