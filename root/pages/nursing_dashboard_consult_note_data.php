<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$type=$_POST["type"];

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="pat_ipd_ip_consult")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
?>
	<!--<select id="ip_view" onchange="">
		<option value="1">Current</option>
		<option value="2">All</option>
	</select>-->
	<button type="button" class="btn btn-primary" onclick="ipd_save_note()"><i class="icon-plus"></i> Add Note</button>
<?php
	$qry=mysqli_query($link,"SELECT DISTINCT `note_date` FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `note_date` DESC");
	while($res=mysqli_fetch_array($qry))
	{
		$q=mysqli_query($link,"SELECT * FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `note_date`='$res[note_date]'");
		$num=mysqli_num_rows($q);
		if($num>0)
		{
		?>
		<div><b>Date: <?php echo convert_date_g($res['note_date']);?></b></div>
		<table class="table table-condensed table-bordered">
			<tr>
				<th width="5%">#</th><th width="40%">Note</th><th>Doctor</th><th width="15%">Entry Date</th><th width="20%"></th>
			</tr>
			<?php
			$n=1;
			if($res['date']==$date)
			$dis="";
			else
			//$dis="disabled='disabled'";
			while($r=mysqli_fetch_array($q))
			{
				$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$r[consultantdoctorid]'"));
				if($r['note']=="")
				$btn1="Add Note";
				else
				$btn1="Edit Note";
			?>
			<tr>
				<td><?php echo $n;?></td><td><?php echo $r['note'];?></td><td><?php echo $doc['Name'];?></td><td><?php echo convert_date_g($r['date'])." ".convert_time($r['time']);?></td><td><button type="button" class="btn btn-mini btn-info" onclick="ip_note('<?php echo $r['id'];?>')" <?php echo $dis;?>><?php echo $btn1;?></button></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
		<?php
		}
		if($res['date']==$date)
		{
?>
			<!--<button type="button" class="btn btn-primary" onclick="ipd_save_note()"><i class="icon-plus"></i> Add Doctor</button>-->
<?php
		}
	}
}


if($_POST["type"]=="ipd_ip_add_doc_new")
{
	$id=mysqli_real_escape_string($link, $_POST["id"]);
	
	$n=mysqli_fetch_array(mysqli_query($link,"SELECT `note`,`consultantdoctorid` FROM `ipd_ip_consultation` WHERE `id`='$id'"));
	
	$note="";
	if($n['note'])
	{
		$note=$n['note'];
	}
	$con_doc_note_date=date("Y-m-d");
	if($n['note_date'])
	{
		$con_doc_note_date=$n['note_date'];
	}
?>
	<table class="table table-condensed" id="note_tbl">
		<tr>
			<td>
				<b>Doctor</b>
				<select id="con_doc">
					<option value="0">Select</option>
					<?php
					//~ $q=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					$q=mysqli_query($link,"SELECT a.* FROM `consultant_doctor_master` a, `employee` b WHERE a.`emp_id`=b.`emp_id` AND b.`status`='0' ORDER BY `Name`");
					while($r=mysqli_fetch_array($q))
					{
						if($n["consultantdoctorid"]==$r["consultantdoctorid"]){ $doc_sel="selected"; }else{ $doc_sel=""; }
					?>
					<option value="<?php echo $r['consultantdoctorid'];?>" <?php echo $doc_sel; ?>><?php echo $r['Name'];?></option>
					<?php
					}
					?>
				</select>
				<b>Date</b>
				<input type="text" class="datepicker" id="con_doc_note_date" value="<?php echo $con_doc_note_date;?>" />
			</td>
		</tr>
		<tr>
			<td>
				<b>Note</b><br/>
				<!--<textarea id="ip_note" style="width:500px;resize:none;"><?php echo $note;?></textarea>-->
				<div class="widget-content_con_doc_note">
					<div class="control-group">
						<div class="controls">
							<textarea class="textarea_editor span12" id="ip_note" rows="15" placeholder="Enter note ..."><?php echo $note;?></textarea>
						</div>
					</div>
				</div>
				<br/>
				<input type="hidden" id="con_note_id" value="<?php echo $id; ?>">
				<button type="button" class="btn btn-success" onclick="ipd_save_new_note()">Save</button>
				<button type="button" class="btn btn-danger" id="close_btn_doc_note" data-dismiss="modal">Close</button>
			</td>
		</tr>
	</table>
	<style>
		#note_tbl tr:hover{background:none;}
	</style>
	<?php
}

if($_POST["type"]=="ipd_save_note")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$con_note_id=$_POST['con_note_id'];
	$note_date=$_POST['note_date'];
	$ip_note=mysqli_real_escape_string($link, $_POST['ip_note']);
	$con_doc=$_POST['con_doc'];
	$usr=$_POST['usr'];
	if($con_doc)
	{
		$fee=mysqli_fetch_array(mysqli_query($link,"SELECT `ipd_visit_fee` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_doc'"));
		if($con_note_id==0)
		{
			if(mysqli_query($link,"INSERT INTO `ipd_ip_consultation`(`patient_id`, `ipd_id`, `note`, `consultantdoctorid`, `ipd_fees`, `note_date`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$ip_note','$con_doc','$fee[ipd_visit_fee]','$note_date','$date','$time','$usr')"))
			{
				echo "Saved";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
		else
		{
			if(mysqli_query($link," UPDATE `ipd_ip_consultation` SET `note`='$ip_note',`consultantdoctorid`='$con_doc',`ipd_fees`='$fee[ipd_visit_fee]',`note_date`='$note_date' WHERE `id`='$con_note_id' "))
			{
				echo "Updated";
			}
			else
			{
				echo "Failed, try again later.";
			}
		}
	}
}

if($_POST["type"]=="ipd_auto_save_ip_note")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$bed=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid'"));
	if($bed==0)
	{
		$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$date'"));
		if($num==0)
		{
			$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid`,`ipd_visit_fee` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
			
			//mysqli_query($link,"INSERT INTO `ipd_ip_consultation`(`patient_id`, `ipd_id`, `note`, `consultantdoctorid`, `ipd_fees`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','','$doc[consultantdoctorid]','$doc[ipd_visit_fee]','$date','$time','$usr')");
		}
	}
}


if($_POST["type"]=="ipd_ip_note_edit")
{
	$id=$_POST['id'];
	$n=mysqli_fetch_array(mysqli_query($link,"SELECT `note`,`consultantdoctorid` FROM `ipd_ip_consultation` WHERE `id`='$id'"));
	if($n['note'])
	$note=$n['note'];
	else
	$note="";
	?>
	<table class="table table-condensed" id="note_tbl">
		<tr>
			<td>
				<b>Note</b><br/>
				<textarea id="ip_note" style="width:500px;resize:none;"><?php echo $note;?></textarea><br/>
				<button type="button" class="btn btn-success" data-dismiss="modal" onclick="save_ip_note('<?php echo $id;?>')">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</td>
		</tr>
	</table>
	<style>
		#note_tbl tr:hover{background:none;}
	</style>
	<?php
}
?>
