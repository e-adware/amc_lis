<?php
session_start();
$emp_id = trim($_SESSION['emp_id']);

include("../../includes/connection.php");
require('../../includes/global.function.php');
date_default_timezone_set("Asia/Kolkata");
$date = date("Y-m-d");
$time = date("H:i:s");

//---------------------------------------------------------------------------------------------------//
function val_con($val)
{
	$nval = explode(".", $val);
	if (!$nval[1]) {
		return $val . ".00";
	} else {
		if (!$nval[1][1]) {
			return $val . "0";
		} else {
			return $val;
		}
	}
}

if ($_POST["type"] == "load_menu") // gov
{
	$head = $_POST['head'];
	?>
	<table class="table table-condensed" style="">
		<tr>
			<th>ID</th>
			<th>Header</th>
			<th>Name</th>
			<th>Remarks</th>
			<th>Sequence</th>
			<th>Action</th>
			<?php if ($emp_id == 101 || $emp_id == 102) { ?>
				<th>Hide</th><?php } ?>
		</tr>
		<?php
		$q = mysqli_query($link, "SELECT * FROM `menu_master` WHERE `header`='$head' ORDER BY `sequence`,`par_name`");
		while ($r = mysqli_fetch_array($q)) {
			$h = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `menu_header_master` WHERE `id`='$r[header]'"));
			?>
			<tr>
				<td><?php echo $r['par_id']; ?></td>
				<td><?php echo $h['name']; ?></td>
				<td><?php echo $r['par_name']; ?></td>
				<td>
					<input type="text" class="span3" id="menu_remarks<?php echo $r['par_id']; ?>"
						value="<?php echo $r['remarks']; ?>"
						onkeyup="menu_remarks_up(event,this.value,'<?php echo $r['par_id']; ?>')">
				</td>
				<td><?php echo $r['sequence']; ?></td>
				<td><input type="button" id="upd" class="btn btn-info btn-mini" onclick="edit('<?php echo $r[par_id]; ?>')"
						value="Update" /> <input type="button" id="upd" class="btn btn-danger btn-mini"
						onclick="del('<?php echo $r[par_id]; ?>')" value="Delete" /></td>
				<?php if ($emp_id == 101 || $emp_id == 102) { ?>
					<td>
						<label><input type="radio" name="vaccu<?php echo $r['par_id']; ?>" <?php if ($r['hidden'] == 1) {
							   echo "checked='checked'";
						   } ?> value="1"
								onClick="menu_hidden(this.value,'<?php echo $r['par_id']; ?>')" />
							Yes</label>
						<label><input type="radio" name="vaccu<?php echo $r['par_id']; ?>" <?php if ($r['hidden'] == 0) {
							   echo "checked='checked'";
						   } ?> value="0"
								onClick="menu_hidden(this.value,'<?php echo $r['par_id']; ?>')" />
							no</label>
					</td>
				<?php } ?>
			</tr>
			<?php
		}
		?>
	</table>
	<style>
		label {
			display: inline-block;
		}
	</style>
	<?php
}


if ($_POST["type"] == "save_menu") {
	$menu = $_POST['menu'];
	$par = $_POST['par'];
	$head = $_POST['head'];
	$access_to = $_POST['access_to'];
	$menu_remarks_new = mysqli_real_escape_string($link, $_POST['menu_remarks_new']);
	$seq = $_POST['seq'];
	if (!$seq) {
		$max_seq = mysqli_fetch_array(mysqli_query($link, "SELECT MAX(`sequence`) AS max FROM `menu_master` WHERE `header`='$head'"));
		$seq = $max_seq["max"];
	}
	$num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `menu_master` WHERE `sequence`='$seq' AND `header`='$head' AND `par_id`!='$par'"));
	if ($num > 0) {
		$nn = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `menu_master` WHERE `par_id`='$par'"));
		if ($nn > 0) {
			echo "Parameter Id Already Exists";
		} else {
			$qq = mysqli_query($link, "SELECT `par_id`,`sequence` AS sn FROM `menu_master` WHERE `header`='$head' AND `sequence`>='$seq'");
			while ($rr = mysqli_fetch_array($qq)) {
				$new = ($rr['sn'] + 1);
				mysqli_query($link, "UPDATE `menu_master` SET `sequence`='$new' WHERE `par_id`='$rr[par_id]'");
			}
			if (mysqli_query($link, "INSERT INTO `menu_master`(`par_id`, `par_name`, `header`, `sequence`, `hidden`, `remarks`) VALUES ('$par','$menu','$head','$seq','0','$menu_remarks_new')")) {
				foreach ($access_to as $level) {
					mysqli_query($link, " INSERT INTO `menu_access_detail`(`levelid`, `par_id`) VALUES ('$level','$par') ");
				}

				echo "Saved";
			} else {
				echo "Error1";
			}
		}
	} else {
		if (mysqli_query($link, "INSERT INTO `menu_master`(`par_id`, `par_name`, `header`, `sequence`, `hidden`, `remarks`) VALUES ('$par','$menu','$head','$seq','0','$menu_remarks_new')")) {
			foreach ($access_to as $level) {
				mysqli_query($link, " INSERT INTO `menu_access_detail`(`levelid`, `par_id`) VALUES ('$level','$par') ");
			}

			echo "Saved";
		} else {
			echo "Error2";
		}
	}

}

if ($_POST["type"] == "edit_menu") // gov
{
	$id = $_POST['id'];
	$m = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `menu_master` WHERE `par_id`='$id'"));
	echo $id . "@gov@" . $m['par_name'] . "@gov@" . $m['header'] . "@gov@" . $m['sequence'] . "@gov@";
}

if ($_POST["type"] == "load_header") // gov
{
	$hid = $_POST['hid'];
	$h = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `menu_header_master` WHERE `id`='$hid'"));
	echo $hid . "@gov@" . $h['name'] . "@gov@" . $h['sequence'] . "@gov@";
}


if ($_POST["type"] == "update_menu") {
	$pid = $_POST['pid'];
	$pname = $_POST['pname'];
	$phead = $_POST['phead'];
	$seq = $_POST['seq'];
	$num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `menu_master` WHERE `sequence`='$seq' AND `header`='$phead' AND `par_id`!='$pid'"));
	if ($num > 0) {
		$qq = mysqli_query($link, "SELECT `par_id`,`sequence` AS sn FROM `menu_master` WHERE `header`='$phead' AND `sequence`>='$seq'");
		while ($rr = mysqli_fetch_array($qq)) {
			$new = ($rr['sn'] + 1);
			mysqli_query($link, "UPDATE `menu_master` SET `sequence`='$new' WHERE `par_id`='$rr[par_id]'");
		}
		mysqli_query($link, "UPDATE `menu_master` SET `par_name`='$pname',`header`='$phead',`sequence`='$seq' WHERE `par_id`='$pid'");
		echo "Updated";
	} else {
		mysqli_query($link, "UPDATE `menu_master` SET `par_name`='$pname',`header`='$phead',`sequence`='$seq' WHERE `par_id`='$pid'");
		echo "Updated";
	}
}

if ($_POST["type"] == "update_header") {
	$hid = $_POST['hid'];
	$head = $_POST['hname'];
	$seq = $_POST['seq'];

	$o = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `menu_header_master` WHERE `id`='$hid'"));
	$q = mysqli_query($link, "SELECT * FROM `menu_header_master` WHERE `sequence`='$seq'");
	$n = mysqli_num_rows($q);
	if ($n > 0) {
		$a = mysqli_fetch_array($q);
		$i = $a['id'];
		$s = $o['sequence'];
		mysqli_query($link, "UPDATE `menu_header_master` SET `sequence`='$s' WHERE `id`='$i'");
	}
	if (mysqli_query($link, "UPDATE `menu_header_master` SET `name`='$head',`sequence`='$seq' WHERE `id`='$hid'")) {
		echo "Updated";
	} else {
		echo "Error";
	}
}

if ($_POST["type"] == "save_header") {
	$head = $_POST['hname'];
	$seq = $_POST['seq'];

	$q = mysqli_query($link, "SELECT * FROM `menu_header_master` WHERE `sequence`='$seq'");
	$n = mysqli_num_rows($q);
	if ($n > 0) {
		$a = mysqli_fetch_array($q);
		$i = $a['id'];
		$s = mysqli_fetch_array(mysqli_query($link, "SELECT MAX(sequence) as max FROM `menu_header_master`"));
		$sq = $s['max'] + 1;
		mysqli_query($link, "UPDATE `menu_header_master` SET `sequence`='$sq' WHERE `id`='$i'");
	}
	if (mysqli_query($link, "INSERT INTO `menu_header_master`(`name`, `sequence`) VALUES ('$head','$seq')")) {
		echo "Saved";
	} else {
		echo "Error";
	}
}

if ($_POST["type"] == "del_menu") {
	$pid = $_POST['pid'];
	if (mysqli_query($link, "DELETE FROM `menu_master` WHERE `par_id`='$pid'")) {
		echo "Deleted";
	} else {
		echo "Error";
	}
}
if ($_POST["type"] == "hidden_menu") {
	$pid = $_POST['pid'];
	$val = $_POST['val'];

	mysqli_query($link, " UPDATE `menu_master` SET `hidden`='$val' WHERE `par_id`='$pid' ");
}
if ($_POST["type"] == "save_menu_remarks") {
	$par_id = $_POST['par_id'];
	$remarks = mysqli_real_escape_string($link, $_POST['val']);

	mysqli_query($link, " UPDATE `menu_master` SET `remarks`='$remarks' WHERE `par_id`='$par_id' ");
}

?>