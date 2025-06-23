<?php
session_start();
include("../../includes/connection.php");

$date=date("Y-m-d");

if($_POST["type"]=="load_save_field")
{
	$db_table=trim($_POST["db_table"]);
	if($db_table)
	{
		$table_check=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `backup_tables` WHERE `table_name`='$db_table' "));
		if(!$table_check)
		{
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Table Name</th>
				<td id="table_name">
					<?php echo $db_table; ?>
				</td>
			</tr>
			<tr>
				<th>Date Check</th>
				<td>
					<label><input type="checkbox" id="date_check" class="chk" value="1" > Yes</label>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><button class="btn btn-info btn-mini" onClick="save_table()">Save</button></td>
			</tr>
		</table>
		<?php
		}else
		{
			echo "404@".$table_check["slno"];
		}
	}
	
}

if($_POST["type"]=="save_table")
{
	$db_table=trim($_POST["db_table"]);
	$date_check=$_POST["date_check"];
	
	$table_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `backup_tables` WHERE `table_name`='$db_table' "));
	if($table_num==0)
	{
		mysqli_query($link, " INSERT INTO `backup_tables`(`table_name`, `date_type`) VALUES ('$db_table','$date_check') ");
		
		echo "Saved";
	}else
	{
		mysqli_query($link, " UPDATE `backup_tables` SET `date_type`='$date_check' WHERE `table_name`='$db_table' ");
		
		echo "Updated";
	}
}

if($_POST["type"]=="load_table")
{
	
	$table_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `backup_tables` WHERE `table_name`='$db_table' "));
?>
	<div class="widget-box">
		<div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
			<h5>Backup Tables</h5>
			<button class="btn btn-print btn-mini" style="float:right;" onclick="print_page()"><i class="icon-print"></i> Print</button>
		</div>
		<div class="widget-content nopadding">
			<table class="table table-bordered data-table table-condensed">
				<thead style="background: #ddd;">
					<tr style="cursor:pointer;">
						<th>#</th>
						<th>Table Name</th>
						<th>Date Check</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$n=1;
					$table_qry=mysqli_query($link, " SELECT * FROM `backup_tables` ORDER BY `table_name` ");
					while($table=mysqli_fetch_array($table_qry))
					{
						if($table["date_type"]==1)
						{
							$date_check_str="Yes";
						}
						if($table["date_type"]==0)
						{
							$date_check_str="No";
						}
				?>
					<tr class="gradeX">
						<td><?php echo $n; ?></td>
						<td><?php echo $table["table_name"]; ?></td>
						<td>
							<?php echo $date_check_str; ?>
							<span class="text-right">
								<button class="btn btn-mini btn-success" onClick="edit_table(<?php echo $table['slno']; ?>)"><i class="icon-edit"></i></button>
								<button class="btn btn-mini btn-danger" onClick="delete_table(<?php echo $table['slno']; ?>)"><i class="icon-remove"></i></button>
							</span>
						</td>
					</tr>
				<?php
						$n++;
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
<?php
	
}


if($_POST["type"]=="remove_table")
{
	$slno=$_POST["slno"];
	
	mysqli_query($link, " DELETE FROM `backup_tables` WHERE `slno`='$slno' ");
	
}

if($_POST["type"]=="edit_table")
{
	$slno=$_POST["slno"];
	$db_table=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `backup_tables` WHERE `slno`='$slno' "));
	if($db_table["date_type"]=="1")
	{
		$check_str="checked";
	}else
	{
		$check_str="";
	}
	//echo $check_str;
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Table Name</th>
			<td id="table_name">
				<?php echo $db_table["table_name"]; ?>
			</td>
		</tr>
		<tr>
			<th>Date Check</th>
			<td>
				<label><input type="checkbox" id="date_check" class="chk" value="1" <?php echo $check_str; ?> > Yes</label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><button class="btn btn-info btn-mini" onClick="save_table()">Save</button></td>
		</tr>
	</table>
<?php
}
?>
