<?php
include("../../includes/connection.php");

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d-M-y', $timestamp);
	return $new_date;
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

$date=date('Y-m-d');
$date1 = strtotime(date("Y-m-d", strtotime($date)) . " -2 days");
$date_five=date("Y-m-d",$date1);
$date_c=date("Y-m-d");

$type=$_POST['type'];

if($type=="load_cat_doc")
{
	$category_id=$_POST['category_id'];
	$r_doc=mysqli_query($link,"select * from lab_doctor where category='$category_id' and status='0' order by name");
	echo "<option value='0'>--Select--</option>";
	while($rd=mysqli_fetch_array($r_doc))
	{
		if(mysqli_num_rows($r_doc)==1)
		{
			echo "<option value='$rd[id]' selected>$rd[name]</option>";
		}
		else
		{
			echo "<option value='$rd[id]'>$rd[name]</option>";
		}
	}
}

if($type=="load_all_pat")
{
	
	$category_id=$_POST['category_id'];
	$p_id=trim($_POST['p_id']);
	$name=$_POST['name'];
	$date1=$_POST['fdate'];
	$date2=$_POST['tdate'];
	
	
	$str="select distinct patient_id,opd_id,ipd_id,batch_no,date,time from patient_test_details where testid in(select testid from testmaster where category_id='$category_id') and date between '$date_five' and '$date'";
	$str.=" order by slno desc";
	
	if(strlen($p_id)>2)
	{
		$str="select distinct patient_id,opd_id,ipd_id,batch_no,date,time from patient_test_details where testid in(select testid from testmaster where category_id='$category_id') and (opd_id like '$p_id%' or ipd_id like'$p_id%')";
	}
	if(strlen($name)>2)
	{
		$str="select distinct patient_id,opd_id,ipd_id,batch_no,date,time from patient_test_details where testid in(select testid from testmaster where category_id='$category_id') and patient_id in(select patient_id from patient_info where name like '%$name%')";
		$str.=" order by slno desc";
	}
	
	if($p_id=='' && $name=='')
	{
		if($date1 && $date2)
		{
			$str="select distinct patient_id,opd_id,ipd_id,batch_no,date,time from patient_test_details where testid in(select testid from testmaster where category_id='$category_id') and date between '$date1' and '$date2'";
		}
		$str.=" order by slno desc";
	}
	
	//echo $str;
	$qry=mysqli_query($link, $str);

?>
	
	<table class="table table-bordered table-condensed table-report">
		
			<tr>
				<th style="width:5%;">#</th>
				<th>BILL No.</th>
				<th>Name - Phone</th>
				<th>Age-Sex</th>
				<th>Time</th>
				<th>Date</th>
				<th>Tests</th>
			</tr>
		
	<?php
	$i=1;
	while($q=mysqli_fetch_array($qry))
	{
		if($q['opd_id'])
		{
			$pin=$q['opd_id'];
		}
		if($q['ipd_id'])
		{
			$pin=$q['ipd_id'];
		}
		
		$name=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$q[patient_id]'"));
		
		
		$num1=mysqli_num_rows(mysqli_query($link, "select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where category_id='$category_id') "));
		
		$num2=mysqli_num_rows(mysqli_query($link, "select * from testresults_rad where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and doc>0 and observ!='' and testid in(select testid from testmaster where category_id='$category_id')"));
		
		//~ if($category_id==3)
		//~ {
			//~ $num2=mysqli_num_rows(mysqli_query($link, "select * from testresults_card where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and observ!=''"));
		//~ }
		
		$num3=$num1-$num2;
		
		if($num2==0)
		{
			$style_span="background-color: #d59a9a;";
			$cls="red";
		}else if($num1==$num2)
		{
			$style_span="background-color: #9dcf8a;";
			$cls="green";
		}else
		{
			// Partially received
			$style_span="background-color: #f6e8a8;";
			$cls="red";
		}
		
		$num4=mysqli_num_rows(mysqli_query($link, "select distinct testid from testreport_print where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where category_id='$category_id')"));
		
		//~ if($num2!=0)
		//~ {
			//~ if($num2==$num4)
			//~ {
				//~ // Partially received
				//~ $style_span="background-color: #666666;";
				//~ $cls="gray";
			//~ }		
		//~ }
		
		
		$cen="";

		$xr=mysqli_num_rows(mysqli_query($link, "select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where type_id='40')"));
		if($xr>0)
		{
			$cls=$cls." xr";	
		}
		
		$ultr=mysqli_num_rows(mysqli_query($link, "select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where type_id='128')"));
		if($ultr>0)
		{
			$cls=$cls." ultr";	
		}
		
		$ct=mysqli_num_rows(mysqli_query($link, "select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where type_id='126')"));
		if($ct>0)
		{
			$cls=$cls." ct";	
		}
		
		$mri=mysqli_num_rows(mysqli_query($link, "select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where type_id='140')"));
		if($mri>0)
		{
			$cls=$cls." mri";	
		}
		
		$endo=mysqli_num_rows(mysqli_query($link, "select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where type_id='121')"));
		if($endo>0)
		{
			$cls=$cls." endo";	
		}
		$ipd_serial=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$pin'"));
	?>	
			<tr id="path_tr<?php echo $i;?>" class="<?php echo $cls;?>" style="cursor:pointer;">
				<!--<td><span class="btn_round" style="<?php echo $style_span; ?>"><?php echo $i; ?></span></td>-->
				<th><?php echo $i; ?></th>
				<td><?php echo $ipd_serial['opd_id'];?></td>
				<td><?php echo $name['name'];?></td>
				<td><?php echo $name['age']." ".$name['age_type']." ".$name['sex'];?></td>
				<td><?php echo convert_time($q['time']);?></td>
				<td>
					<?php echo convert_date($q['date']);?>
					<div id="path_pat<?php echo $i;?>" style="display:none">
						<?php echo "@".$q['patient_id']."@".$q['opd_id']."@".$q['ipd_id']."@".$q['batch_no'];?>
					</div>
				</td>
				<td class="pat_test_but">
					<?php
					$tst=mysqli_query($link,"select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where category_id='$category_id')");
					while($tt_rad=mysqli_fetch_array($tst))
					{
						$tname=mysqli_fetch_array(mysqli_query($link,"select testname,type_id from testmaster where testid='$tt_rad[testid]'"));
						$clss="btn btn-danger btn-mini";
						$res_chk=mysqli_fetch_array(mysqli_query($link,"select * from testresults_rad where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid='$tt_rad[testid]'"));
						if($res_chk[observ])
						{
							$clss="btn btn-warning btn-mini";
							if($res_chk[doc]>0)
							{
								$clss="btn btn-success btn-mini";
							}
							
							$chk_print=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testreport_print where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid='$tt_rad[testid]'"));
							if($chk_print[tot]>0)
							{
								$clss="btn btn-info btn-mini";
							}
						}
						
						if($tname[type_id]==128)
						{
							$clss.=' ultr';
						}
						if($tname[type_id]==40)
						{
							$clss.=' xr';
						}
						if($tname[type_id]==126)
						{
							$clss.=' ct';
						}
						if($tname[type_id]==140)
						{
							$clss.=' mri';
						}
						
						//echo "<div class='$clss' onclick=\"load_test_info('$q[patient_id]','$q[opd_id]','$q[ipd_id]','$q[batch_no]','$i','$tt_rad[testid]')\" style='display:block !important'>$tname[testname]</div>";
						echo "<div class='$clss' onclick=\"load_res('$q[patient_id]','$q[opd_id]','$q[ipd_id]','$q[batch_no]','$tt_rad[testid]')\" style='display:block !important'>$tname[testname]</div>";
						
					}
					?>
				</td>
			</tr>
	<?php
			$i++;
			
		}
	?>
	</table>
	<style>
		.btn_round
		{
			color:#000;
			padding:3px;
			border-radius: 7em;
			//background-color: #d59a9a; #9dcf8a;
			padding-right: 7px;
			padding-left: 7px;
			box-shadow: inset 1px 1px 0 rgba(0,0,0,0.6);
			transition: all ease-in-out 0.2s;
		}
		
	</style>
<?php
}

if($type=="load_all_test")
{
	$uhid=$_POST['uhid'];
	$opd_id=trim($_POST['opd_id']);
	$ipd_id=trim($_POST['ipd_id']);
	$batch_no=trim($_POST['batch_no']);
	$category_id=$_POST['category_id'];
	$tst=$_POST['tst'];
	
	if($opd_id)
	{
		$reg_id=$opd_id;
	}
	if($ipd_id)
	{
		$reg_id=$ipd_id;
	}
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));
	$ipd_serial=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$reg_id'"));
?>

	<input type="hidden" id="uhid" value="<?php echo $uhid;?>"/>
	<input type="hidden" id="pat_id" value="<?php echo $reg_id;?>"/>
	
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>Bill No.</th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
		</tr>
		<tr>
			<td><?php echo $ipd_serial['opd_id']; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $pat_info["age"]; ?> <?php echo $pat_info["age_type"]; ?></td>
			<td><?php echo $pat_info["sex"]; ?></td>
		</tr>
	</table>
	<br>
	<table class="table table-bordered table-condensed table-report">
	<tr>
		<th>Test Name</th><th>Saved</th><th>Validate</th><th>Printed</th><th></th>
	</tr>
		<?php
		$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$tst'"));
		$res=mysqli_fetch_array(mysqli_query($link,"select * from testresults_rad where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'"));
		
		$print=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testreport_print where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id'and batch_no='$batch_no' and testid='$tst'"));
		
		//~ $print1=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testreport_print where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and testid='$tst' and batch_no='$batch_no'"));
		//$print2=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testreport_print where patient_id='$uhid' and ipd_id='$reg_id' and testid='$tst' and batch_no='$batch_no'"));
		//~ $print[tot]=$print1[tot]+$print2[tot];
		
		//~ $print[tot]=$print1[tot];
		?>
	<tr>
		<td><?php echo $tname[testname];?></td>
		<td>
			<?php
			if($res[saved]>0)
			{
				?> <img src="../images/right.png" height="20" width="20"/> - <?php
				$emp=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res[saved]'"));
				echo $emp[name];
			}
			else
			{
				?> <img src="../images/Delete.png" height="20" width="20"/><?php
			}
			?>
		</td>
		<td>
			<?php
			if($res[doc]>0)
			{
				?> <img src="../images/right.png" height="20" width="20"/> - <?php
				$doc=mysqli_fetch_array(mysqli_query($link,"select name from lab_doctor where id='$res[doc]'"));
				echo $doc[name];
			}
			else
			{
				?> <img src="../images/Delete.png" height="20" width="20"/><?php
				
			}
			?>
		</td>
		<td>
			<?php
			if($print[tot]>0)
			{
				?> <img src="../images/right.png" height="20" width="20"/> <?php	
			}
			else
			{
				?> <img src="../images/Delete.png" height="20" width="20"/><?php
			}
			?>
		</td>
		<td>
			<button class="btn btn-info btn-mini" onclick="load_res('<?php echo $uhid;?>','<?php echo $opd_id;?>','<?php echo $ipd_id;?>','<?php echo $batch_no;?>','<?php echo $tst;?>')"><i class="icon-save"></i> Add/Edit Result</button>
		<?php
			if($res["doc"]>0)
			{
		?>
			<button class="btn btn-success btn-mini" onclick="print_report('<?php echo $uhid;?>','<?php echo $opd_id;?>','<?php echo $ipd_id;?>','<?php echo $batch_no;?>','<?php echo $tst;?>')"><i class="icon-print"></i> Print Report</button>
		<?php
			}
		?>
		</td>
	</tr>	
		
	
	</table>
	
	
	<br>
	<center>
		<button class="btn btn-info" onclick="back_div()" id="back_div"><i class="icon-backward"></i> Back</button>
	</center>

<?php
}

if($type=="save_result")
{
	$uhid=trim($_POST['uhid']);
	$opd_id=trim($_POST['opd_id']);
	$ipd_id=trim($_POST['ipd_id']);
	$batch_no=trim($_POST['batch']);
	$tst=$_POST['tst'];
	$testname=mysqli_real_escape_string($link, $_POST['tst_name']);
	$obsrv=mysqli_real_escape_string($link, $_POST['res']);
	$user=$_POST['user'];
	
	$date=date("Y-m-d");
	$time=date('H:i:s');
	
	$chk=mysqli_num_rows(mysqli_query($link, "select * from testresults_rad where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'"));

	if($chk>0)
	{
		$nobserv=$obsrv;
		if($obsrv=="<p><br></p>")
		{
			$nobserv="";
		}	
		
		mysqli_query($link, "update testresults_rad set observ='$nobserv',testname='$testname',saved='$user',doc='0' where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'");	
	}
	else
	{
		mysqli_query($link, "insert into testresults_rad(patient_id,opd_id,ipd_id,batch_no,testid,testname,observ,saved,doc,film_no,time,date) values('$uhid','$opd_id','$ipd_id','$batch_no','$tst','$testname','$obsrv','$user','0','','$time','$date')");
	}
}
if($type=="valid_result")
{
	$uhid=trim($_POST['uhid']);
	$opd_id=trim($_POST['opd_id']);
	$ipd_id=trim($_POST['ipd_id']);
	$batch_no=trim($_POST['batch']);
	$tst=$_POST['tst'];
	$rep_doc=$_POST['rep_doc'];
	
	
	if(mysqli_query($link, "update testresults_rad set doc='$rep_doc' where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'"))
	{
		echo "valid";
	}
}
?>
