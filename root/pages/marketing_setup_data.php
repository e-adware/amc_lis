<?php
session_start();

include("../../includes/connection.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

if(!$c_user)
{
	echo "404@$@Error";
}

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="doc_list")
{
	$emp_id=$_POST["marketing_id_find"];
	$branch_id=$_POST["branch_id"];
	
	$str="SELECT `emp_id`, `name`, `amount` FROM `employee` WHERE `levelid`='31' AND `branch_id`='$branch_id'";
	
	if($emp_id>0)
	{
		$str.=" AND `emp_id`='$emp_id'";
	}
	
	$str.=" ORDER BY `name` ASC";
	
	$qry=mysqli_query($link, $str);
?>
	<table class="table table-condensed table-bordered table-hover" style="background-color:white;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Marketing Agent Name</th>
				<th>Target Amount</th>
				<th>Ref. Doctors</th>
			</tr>
		</thead>
<?php
		$n=1;
		while($data=mysqli_fetch_array($qry))
		{
?>
			<tr onclick="load_doc_info('<?php echo $data["emp_id"] ?>')" style="cursor:pointer;">
				<td><?php echo $n; ?></td>
				<td><?php echo $data["name"]; ?></td>
				<td><?php echo $data["amount"]; ?></td>
				<td>
				<?php
					$str="SELECT b.`ref_name` FROM `marketing_master` a, `refbydoctor_master` b WHERE a.`refbydoctorid`=b.`refbydoctorid` AND a.`emp_id`='$data[emp_id]'";
					
					$str.=" ORDER BY b.`ref_name` ASC";
					
					$i=1;
					$doc_qry=mysqli_query($link, $str);
					while($doc_info=mysqli_fetch_array($doc_qry))
					{
						if($i>1)
						{
							echo "<br>";
						}
						echo $i.". ".$doc_info["ref_name"];
						$i++;
					}
				?>
				</td>
			</tr>
<?php
			$n++;
		}
?>
	</table>
<?php
}


if($_POST["type"]=="doc_info")
{
	//print_r($_POST);
	
	$emp_id=$_POST["emp_id"];
	$user=$_POST["user"];
	$branch_id=$_POST["branch_id"];
	
?>
	<table class="table table-condensed table-bordered table-hover" style="background-color:white;">
		<tr>
			<th>Marketing Agent Name <b style="color:#f00;">*</b></th>
			<td>
				<select id="marketing_id" class="span2" disabled>
				<?php
					$qry=mysqli_query($link," SELECT `emp_id`, `name` FROM `employee` WHERE `levelid`='31' AND `emp_id`='$emp_id' AND `branch_id`='$branch_id' ");
					while($data=mysqli_fetch_array($qry))
					{
						echo "<option value='$data[emp_id]'>$data[name]</option>";
					}
				?>
				</select>
			</td>
			<th>Ref. Doctor <b style="color:#f00;">*</b></th>
			<td>
				<select id="refbydoctorid" onkeyup="refbydoctorid_up(event)">
					<option value="0">Select</option>
				<?php
					$qry=mysqli_query($link," SELECT `refbydoctorid`, `ref_name` FROM `refbydoctor_master` WHERE `branch_id`='$branch_id' AND `refbydoctorid` NOT IN(SELECT `refbydoctorid` FROM `marketing_master` WHERE `emp_id`='$emp_id')");
					while($data=mysqli_fetch_array($qry))
					{
						echo "<option value='$data[refbydoctorid]'>$data[ref_name]</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<center>
					<button class="btn btn-info" id="save_btn" onclick="save()"><i class="icon-save"></i> Save</button>
					<button class="btn btn-inverse" id="back_btn" onclick="doc_list()"><i class="icon-backward"></i> Back</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_ref_docs"></div>
<?php
}

if($_POST["type"]=="save_data")
{
	//print_r($_POST);
	
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	$emp_id=mysqli_real_escape_string($link, $_POST["emp_id"]);
	$refbydoctorid=mysqli_real_escape_string($link, $_POST["refbydoctorid"]);
	
	if(!$branch_id){ $branch_id=1; }
	
	if(mysqli_query($link, "INSERT INTO `marketing_master`(`emp_id`, `refbydoctorid`, `user`, `date`, `time`) VALUES ('$emp_id','$refbydoctorid','$c_user','$date','$time')"))
	{
		echo "101@$@Saved";
	}
	else
	{
		echo "404@$@Failed, try again later.";
	}
}

if($_POST["type"]=="delete_doc")
{
	//print_r($_POST);
	
	$emp_id=mysqli_real_escape_string($link, $_POST["emp_id"]);
	$refbydoctorid=mysqli_real_escape_string($link, $_POST["refbydoctorid"]);
	
	if(!$branch_id){ $branch_id=1; }
	
	if(mysqli_query($link, "DELETE FROM `marketing_master` WHERE `emp_id`='$emp_id' AND `refbydoctorid`='$refbydoctorid'"))
	{
		echo "101@$@Deleted";
	}
	else
	{
		echo "404@$@Failed, try again later.";
	}
}

if($_POST["type"]=="load_ref_docs")
{
	$emp_id=$_POST["emp_id"];
	
	$str="SELECT a.*, b.`ref_name` FROM `marketing_master` a, `refbydoctor_master` b WHERE a.`refbydoctorid`=b.`refbydoctorid` AND a.`emp_id`='$emp_id'";
	
	$str.=" ORDER BY b.`ref_name` ASC";
	
	$qry=mysqli_query($link, $str);
?>
	<table class="table table-condensed table-bordered table-hover" style="background-color:white;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Ref. Doctor Name</th>
				<th></th>
			</tr>
		</thead>
<?php
		$n=1;
		while($data=mysqli_fetch_array($qry))
		{
?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $data["ref_name"]; ?></td>
				<td>
					<button class="btn btn-delete btn-mini" onclick="delete_doc('<?php echo $data["emp_id"] ?>','<?php echo $data["refbydoctorid"] ?>')"><i class="icon-remove"></i> Remove</button>
				</td>
			</tr>
<?php
			$n++;
		}
?>
	</table>
<?php
}
?>
