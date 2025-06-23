<?php
include("../../includes/connection.php");

$type=$_POST['type'];

if($type==1)
{
	$pid=$_POST[pid];
	$opd=$_POST[opd];
	$ipd=$_POST[ipd];
	$batch=$_POST[batch];
	$tst=$_POST[tid];
	$btype=$_POST[btype];

	if($btype==1)
	{
		$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$tst'"));
		$note=mysqli_fetch_array(mysqli_query($link,"select * from testresults_note where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and testid='$tst'"));
		?>
		<table class="table table-bordered table-condensed table-report">
		<tr>
			<th colspan="2">
				Enter Note (<?php echo $tname[testname];?>)
			</th>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				<textarea class="span6" id="tst_note"><?php echo $note[note];?></textarea>
			</td>	
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				<button id="save" class="btn btn-info" onclick="save_note_sample(<?php echo $tst;?>,1)">Save</button>
				<button id="close" class="btn btn-info" onclick="$('#mod_chk').val('0');$('#mod').click()">Close</button>
			</td>	
		</tr>
		
		</table>
		<script>setTimeout(function(){ $("#tst_note").focus();},300);</script>
		<?php
	}
	else if($btype==2)
	{
		$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$tst'"));
		$stat=mysqli_fetch_array(mysqli_query($link,"select * from testresults_sample_stat where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and paramid='$tst'"));
		?>
		<table class="table table-bordered table-condensed table-report">
		<tr>
			<th colspan="2">
				Enter Sample Status for <?php echo $tname[testname];?>
			</th>
		</tr>
		<tr>
<!--
			<td colspan="2" style="text-align:center">
				<textarea class="span6" id="tst_sample_stat"><?php echo $stat[sample_status];?></textarea>
			</td>	
-->
		<tr>
			<td>Select Status</td>
			<td>
				<select id="tst_sample_stat">
					<option <?php if($stat[sample_status]==""){ echo "Selected";}?> value="">None</option>
					<option <?php if($stat[sample_status]=="Mislabelled"){ echo "Selected";}?> >Mislabelled</option>
					<option <?php if($stat[sample_status]=="Leak"){ echo "Selected";}?> >Leak</option>
					<option <?php if($stat[sample_status]=="Hemolysed"){ echo "Selected";}?> >Hemolysed</option>
					<option <?php if($stat[sample_status]=="Wrong Container"){ echo "Selected";}?> >Wrong Container</option>
					<option <?php if($stat[sample_status]=="Inadequate"){ echo "Selected";}?>>Inadequate</option>
					<option <?php if($stat[sample_status]=="Clotted Vial"){ echo "Selected";}?>>Clotted Vial</option>
					<option <?php if($stat[sample_status]=="Incomplete Form"){ echo "Selected";}?>>Incomplete Form</option>
					<option <?php if($stat[sample_status]=="Contaminated"){ echo "Selected";}?>>Contaminated</option>
					<option <?php if($stat[sample_status]=="Turbid"){ echo "Selected";}?>>Turbid</option>
					<option <?php if($stat[sample_status]=="Icteric"){ echo "Selected";}?>>Icteric</option>
					<option <?php if($stat[sample_status]=="Variant Window"){ echo "Selected";}?>>Variant Window</option>
					<option <?php if($stat[sample_status]=="Lipaemic"){ echo "Selected";}?>>Lipaemic</option>
					<option <?php if($stat[sample_status]=="Sample Not Received"){ echo "Selected";}?>>Sample Not Received</option>
					<option <?php if($stat[sample_status]=="Others"){ echo "Selected";}?>>Others</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Print Result</td>
			<td>
				<select id="rep_dis">
					<option value="1" <?php if($stat[print_result]=="1"){ echo "Selected";}?> >Yes</option>
					<option value="0" <?php if($stat[print_result]=="0"){ echo "Selected";}?>>No</option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td colspan="2" style="text-align:center">
				<button id="save" class="btn btn-info"  onclick="save_note_sample(<?php echo $tst;?>,2)">Save</button>
				<button id="close" class="btn btn-info" onclick="$('#mod_chk').val('0');$('#mod').click()">Close</button>
			</td>	
		</tr>
		
		</table>
		<script>setTimeout(function(){ $("#tst_sample_stat").focus();},300);</script>
		<?php
	}
}
else if($type==2)
{
	$pid=$_POST[pid];
	$opd=$_POST[opd];
	$ipd=$_POST[ipd];
	$batch=$_POST[batch];
	$tst=$_POST[tid];
	$btype=$_POST[btype];
	$user=$_POST[user];
	$date=date("Y-m-d");
	$time=date("H:i:s");
	
	if($btype==1)
	{
		$tst_note=mysqli_real_escape_string($link,$_POST['tst_note']);
		
		mysqli_query($link,"delete from testresults_note where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and testid='$tst'");
		if(mysqli_query($link,"INSERT INTO `testresults_note`(`patient_id`, `opd_id`,`ipd_id`,`batch_no`, `testid`, `note`, `user`, `time`, `date`) VALUES('$pid','$opd','$ipd','$batch','$tst','$tst_note','$user','$time','$date')"))
		{
			echo "Saved";
		}
	}
	else if($btype==2)
	{
		$tst_sample=mysqli_real_escape_string($link,$_POST['tst_stat']);
		$rep_dis=$_POST['rep_dis'];
		
		mysqli_query($link,"delete from testresults_sample_stat where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and paramid='$tst'");
		if($rep_dis!='')
		{
			if(mysqli_query($link,"INSERT INTO `testresults_sample_stat`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `paramid`, `sample_status`, `print_result`, `user`, `time`, `date`) VALUES('$pid','$opd','$ipd','$batch','$tst','$tst_sample','$rep_dis','$user','$time','$date')"))
			{
				echo "Saved";
			}
		}
	}
}
else if($type==3)
{
	$pid=$_POST[pid];
	$opd=$_POST[opd];
	$ipd=$_POST[ipd];
	$batch=$_POST[batch];
	
	$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pid'"));
	?>
	<table class="table table-bordered table-condensed table-report">
	<tr>
		<th colspan="2">Patient Info</th>
	</tr>
	<tr>
		<td>Name</td>
		<td><input type="text" id="name" value="<?php echo $info[name];?>" onkeyup="change_up()" onblur="change_up()"/></td>
	</tr>
	<tr>
		<td>Age</td>
		<td>
			<input type="text" id="age" class="span1" value="<?php echo $info[age];?>"/>
			<select id="age_type" name="c_4" style="width:50px">
				<option value="Years" <?php if($info[age_type]=="Years"){ echo "Selected";}?> >Y</option>
				<option value="Months" <?php if($info[age_type]=="Months"){ echo "Selected";}?> >M</option>
				<option value="Days" <?php if($info[age_type]=="Days"){ echo "Selected";}?> >D</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Sex</td>
		<td><select id="sex" name="c_5" style="width:50px">
			<option value="Male" <?php if($info[sex]=="Male"){ echo "Selected";}?> >M</option>
			<option value="Female" <?php if($info[sex]=="Female"){ echo "Selected";}?> >F</option>
			<option value=" " <?php if($info[sex]==" "){ echo "Selected";}?> > </option>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center">
			<button class="btn btn-info" id="update" onclick="update_info()">Update</button>
			<button class="btn btn-danger" onclick="$('#mod_chk').val('0');$('#mod').click()">Cancel</button>
		</td>
	</tr>
	</table>	
	
	<?php
		
}
else if($type==4)
{
	$pid=$_POST[pid];
	$opd=$_POST[opd];
	$ipd=$_POST[ipd];
	$batch=$_POST[batch];
	
	$name=$_POST['name'];
	$age=$_POST['age'];
	$age_type=$_POST['age_type'];
	$sex=$_POST['sex'];
	
	if(mysqli_query($link,"update patient_info set name='$name',age='$age',age_type='$age_type',sex='$sex' where patient_id='$pid'"))
	{
		echo $name." / ".$age." ".$age_type." / ".$sex;
	}
}
else if($type==5)
{
	$pid=$_POST[pid];
	$opd=$_POST[opd];
	$ipd=$_POST[ipd];
	$batch=$_POST[batch];
	
	$vac=$_POST[vac];
	
	$vname=mysqli_fetch_array(mysqli_query($link,"select * from vaccu_master where id='$vac'"));
	$stat=mysqli_fetch_array(mysqli_query($link,"select * from testresults_sample_stat where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and vac_id='$vac'"));
	
	?>
	<table class="table table-bordered table-condensed table-report">
	<tr>
		<th colspan="2">Vaccu Status - <?php echo $vname[type];?></th>
	</tr>
	<tr>
		<td>Select Status</td>
		<td>
			<select id="vac_sample_stat">
				<option <?php if($stat[sample_status]==""){ echo "Selected";}?> value="">None</option>
				<option <?php if($stat[sample_status]=="Mislabelled"){ echo "Selected";}?> >Mislabelled</option>
				<option <?php if($stat[sample_status]=="Leak"){ echo "Selected";}?> >Leak</option>
				<option <?php if($stat[sample_status]=="Hemolysed"){ echo "Selected";}?> >Hemolysed</option>
				<option <?php if($stat[sample_status]=="Wrong Container"){ echo "Selected";}?> >Wrong Container</option>
				<option <?php if($stat[sample_status]=="Inadequate"){ echo "Selected";}?>>Inadequate</option>
				<option <?php if($stat[sample_status]=="Clotted Vial"){ echo "Selected";}?>>Clotted Vial</option>
				<option <?php if($stat[sample_status]=="Incomplete Form"){ echo "Selected";}?>>Incomplete Form</option>
				<option <?php if($stat[sample_status]=="Contaminated"){ echo "Selected";}?>>Contaminated</option>
				<option <?php if($stat[sample_status]=="Turbid"){ echo "Selected";}?>>Turbid</option>
				<option <?php if($stat[sample_status]=="Icteric"){ echo "Selected";}?>>Icteric</option>
				<option <?php if($stat[sample_status]=="Variant Window"){ echo "Selected";}?>>Variant Window</option>
				<option <?php if($stat[sample_status]=="Lipaemic"){ echo "Selected";}?>>Lipaemic</option>
				<option <?php if($stat[sample_status]=="Sample Not Received"){ echo "Selected";}?>>Sample Not Received</option>
				<option <?php if($stat[sample_status]=="Others"){ echo "Selected";}?>>Others</option>
			</select>
		</td>
	</tr>
	<tr style="display:none">
		<td>Display Result</td>
		<td>
			<select id="dis_res">
				<option value="0" <?php if($stat[print_result]=="0"){ echo "Selected";}?>>No</option>
				<option value="1" <?php if($stat[print_result]=="1"){ echo "Selected";}?> >Yes</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		Add Note <br/>
		<textarea class="span7" id="vac_note"><?php echo $stat[sample_note];?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center">
			<button class="btn btn-success" onclick="vac_wise_save(<?php echo $vac;?>)">Save</button>
			<button class="btn btn-alert"  onclick="$('#mod_chk').val('0');$('#mod').click()">Close</button>
		</td>
	</tr>
	</table>
	
	<?php
	
}
else if($type==6)
{
	$pid=$_POST[pid];
	$opd=$_POST[opd];
	$ipd=$_POST[ipd];
	$batch=$_POST[batch];
	$vac=$_POST[vac];
	
	$stat=$_POST['stat'];
	$note=$_POST['note'];
	$dis_res=$_POST['dis_res'];
	
	$user=$_POST[user];
	$date=date("Y-m-d");
	$time=date("H:i:s");
	
	$i=0;
	$test=mysqli_query($link,"select a.testid from patient_test_details a,test_vaccu b where a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch' and a.testid=b.testid and b.vac_id='$vac'");
	while($tst=mysqli_fetch_array($test))
	{
		mysqli_query($link,"delete from testresults_sample_stat where vac_id='$vac' and testid='$tst[testid]'");
		if($stat!='')
		{
			if(mysqli_query($link,"INSERT INTO `testresults_sample_stat`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vac_id`, `testid`, `sample_status`, `sample_note`, `print_result`, `user`, `time`, `date`) VALUES ('$pid','$opd','$ipd','$batch','$vac','$tst[testid]','$stat','$note','$dis_res','$user','$time','$date')"))
			{
				$i++;
			}
		}
	}
	echo $i;
}
else if($type==7)
{
	$pid=$_POST[pid];
	$opd=$_POST[opd];
	$ipd=$_POST[ipd];
	$batch=$_POST[batch];
	
	$val=$_POST[val];
	
	$user=$_POST[user];
	$date=date("Y-m-d");
	$time=date("H:i:s");
	
	mysqli_query($link,"delete from patient_disease_details where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch'");
	if($val!=0)
	{
		mysqli_query($link,"insert into patient_disease_details(patient_id,opd_id,disease_id,user,date,time) value('$pid','$opd','$val','$user','$date','$time')");
	}
	
	
}
else if($type==8)
{
	$pid=$_POST[pid];
	$opd=$_POST[opd];
	$ipd=$_POST[ipd];
	$batch=$_POST[batch];
	
	$dep=$_POST[dep];
	
	$det=mysqli_fetch_array(mysqli_query($link,"select * from patient_flagged_details where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and dept_id='$dep'"));
	?>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th colspan="2">
				Flag This Patient
			</th>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				Cause <br/>
				<textarea class="span6" id="flag_cause"><?php echo $det[cause];?></textarea>
			</td>	
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				Remarks/CA/PA <br/>
				<textarea class="span6" id="flag_note"><?php echo $det[remarks];?></textarea>
			</td>	
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				<button class="btn btn-info" onclick="flag_save(1)">Flag</button>
				<?php
				if($det)
				{
					?>
					<button class="btn btn-primary" onclick="flag_save(2)">Un-Flag</button>
					<?php
				}
				?>
				<button class="btn btn-alert"  onclick="$('#mod_chk').val('0');$('#mod').click()">Cancel</button>
			</td>
		</tr>
	</table>
	
	<?php
}
/*
else if($type==9)
{
	$pid=$_POST[pid];
	$opd=$_POST[opd];
	$flag_cause=$_POST[flag_cause];
	$flag_note=$_POST[flag_note];
	
	$val=$_POST['val'];
	
	$user=$_POST[user];
	$date=date("Y-m-d");
	$time=date("H:i:s");
	
	mysqli_query($link,"delete from patient_flagged_details where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch'");
	
	if($val==1)
	{
		mysqli_query($link,"insert into patient_flagged_details (`patient_id`, `opd_id`, `cause`, `remarks`, `user`, `time`, `date`) values('$pid','$opd','$flag_cause','$flag_note','$user','$time','$date')");
	}
}
*/
else if($type==9)
{
	$pid=$_POST[pid];
	$opd=$_POST[opd];
	$ipd=$_POST[ipd];
	$batch=$_POST[batch];
	
	$flag_cause=trim(mysqli_real_escape_string($link,$_POST[flag_cause]));
	$flag_note=trim(mysqli_real_escape_string($link,$_POST[flag_note]));
	
	$val=$_POST['val'];
	
	$dep=$_POST['dep'];
	
	$user=$_POST[user];
	$date=date("Y-m-d");
	$time=date("H:i:s");
	
	
	
	if($val==1)
	{
		if($flag_cause!='')
		{
			$chk_cause=mysqli_fetch_array(mysqli_query($link,"select * from patient_flagged_details where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and dept_id='$dep'"));
			if($flag_cause!=$chk_cause[cause])
			{
				$cause_user=$user;
			}
			else
			{
				$cause_user=$chk_cause[cause_user];
			}
		}
		
		if($flag_note!='')
		{
			$chk_note=mysqli_fetch_array(mysqli_query($link,"select * from patient_flagged_details where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and dept_id='$dep'"));
			if($flag_note!=$chk_note[remarks])
			{
				$note_user=$user;
			}
			else
			{
				$note_user=$chk_note[remarks_user];
			}
		}
		
		mysqli_query($link,"delete from patient_flagged_details where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and dept_id='$dep'");
		
		
		mysqli_query($link,"insert into patient_flagged_details (`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `dept_id`, `cause`, `cause_user`,`remarks`, `remarks_user`, `time`, `date`) values('$pid','$opd','$ipd','$batch','$dep','$flag_cause','$cause_user','$flag_note','$note_user','$time','$date')");
	}
	else if($val==2)
	{
		mysqli_query($link,"delete from patient_flagged_details where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and dept_id='$dep'");
	}
}
else if($type==10)
{
	$hostname = "192.168.10.105";
    $port = 1443;
    $dbname = "powerlink";
    $username = "powerlink";
    $pw = "powerlink";
    $dbh = new PDO ("dblib:host=$hostname;dbname=$dbname",$username,$pw);
	
	
	$pid=$_POST['pid'];
	$opd=$_POST['opd'];
	$dep=$_POST['dep'];
	
	$instr_lst="";
	
	$tst=mysqli_query($link,"select distinct barcode_id from test_sample_result where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and testid in(select testid from testmaster where type_id='$dep')");
	while($ts=mysqli_fetch_array($tst))
	{
		$stmt = $dbh->query("SELECT distinct equipment_code FROM tpl_patient_orders where sample_id='$ts[barcode_id]'");
		while ($row = $stmt->fetch()) 
		{
			$instr[]=$row[equipment_code];
		}
		
	}
	$ins_lst=array_unique($instr);
	
	foreach($ins_lst as $inn)
	{
		if($inn)
		{
			$instr_lst=$inn.",".$instr_lst;
		}
	}
	
	echo "<b>Instrument: ".$instr_lst."</b>";
}
else if($type==11)
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd_id'];
	$dep=$_POST['dep'];
	
	$insr[1]="SYSMEXN550_1";
	$insr[2]="SYSMEXN550_2";
	
	
	$i=1;
	$bar=mysqli_query($link,"select distinct barcode_id from test_sample_result where opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch' and testid in(select testid from testmaster where type_id='$dep')");
	while($br=mysqli_fetch_array($bar))
	{
		foreach($insr as $ins)
		{
			
			$file="../../../window_share/".$br[barcode_id]."_".$ins."_graph.jpg";
			
			$nbar=strtolower($br[barcode_id]);
			$file2="../../../window_share/".$nbar."_".$ins."_graph.jpg";
			
			if(file_exists($file))
			{
				echo "<img src='../../window_share/$file' onclick='image_close()'/>";
				break;
			}
			else if(file_exists($file2))
			{
				echo "<img src='../../window_share/$file2' onclick='image_close()'/>";
				break;
			}
			
			$i++;
		}
	}
	
}
?>

