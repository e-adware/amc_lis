<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$type=$_POST['type'];

if($type=="scan")
{
	$val=$_POST['val'];
	
	$bar=mysqli_query($link,"select * from test_sample_result where barcode_id='$val'");
	if(mysqli_num_rows($bar)>0)
	{
		$chk_lab=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from lab_sample_details where barcode_id='$val'"));
		if($chk_lab[tot]==0 || $_POST['view']==1)
		{
			$data=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result where barcode_id='$val' limit 1"));
			
			$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$data[patient_id]'"));
			
			$pin=$data[opd_id];
			if($data[ipd_id]!='')
			{
				$pin=$data[ipd_id];
			}
			?>
			<table class="table table-bordered table-report">
			<tr>
				<th colspan="3">Patient Details</th>
			</tr>
			<tr>
				<td><b>Name: </b><?php echo $info[name];?></td>
				<td><b>Phone: </b><?php echo $info[phone];?></td>
				<td><b>Age/Sex: </b><?php echo $info[age]." ".$info[age_type]."/".$info[sex];?></td>
			</tr>
			</table>
			
			<table class="table table-report table-bordered">
			<tr>
				<th>Test Details</th>
			</tr>
			<tr>
				<td>Barcode ID: <b><?php echo $val;?></b> <input type="hidden" id="barcode" value="<?php echo $val;?>"/></td>
			</tr>
			<tr>
				<th>
				<?php
				$i=1;
				$test=mysqli_query($link,"select a.testname from testmaster a,test_sample_result b where a.testid=b.testid and b.barcode_id='$val' order by a.testname");
				while($tst=mysqli_fetch_array($test))
				{
					echo "<div class='tst_div'>$tst[testname]</div>";	
					if($i%3==0)
					{
						echo "<br/>";
					}
					
				}
				?>
				</th>
			</tr>
			<tr>
				<td style="text-align:center">
					<?php
					if($_POST['view']==1)
					{
						?> <button class="btn btn-danger" onclick="$('#mod').click();">Close</button> <?php
					}
					else
					{
					?>
						<button class="btn btn-info" id="accept_barcode" onclick="accept_barcode()">Accept(<span id="count">5</span>)</button>
						<button class="btn btn-danger" onclick="$('#mod').click();$('#scan').val('');">Cancel</button>
					<?php	
					}
					?>	
				</td>
			</tr>
			</table>
			<?php
		}
		else
		{
			echo "scanned";
		}
		
	}
	else
	{
		echo "no data";
	}
}
elseif($type=="save_sample")
{
	$barcode=$_POST['barcode'];
	$user=$_POST['user'];
	
	$date=date('Y-m-d');
	$time=date("H:i:s");
	
	$data=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result where barcode_id='$barcode' limit 1"));
	
	$pin=$data[opd_id];
	if($data[ipd_id]!='')
	{
		$pin=$data[ipd_id];
	}
	
	mysqli_query($link,"INSERT INTO `lab_sample_details`(`patient_id`, `opdid`, `barcode_id`, `time`, `date`, `user`) VALUES('$data[patient_id]','$pin','$data[barcode_id]','$time','$date','$user')");
}
elseif($type=="load_data")
{
	$from=$_POST['from'];
	$to=$_POST['to'];
	$name=mysqli_real_escape_string($link,trim($_POST['name']));
	$id=mysqli_real_escape_string($link,trim($_POST['vid']));
	
	if($name!='')
	{
		$str="select a.* from lab_sample_details a,patient_info b where a.patient_id=b.patient_id and b.name like '%$name%'";
	}
	else if($id!='')
	{
		$str="select a.* from lab_sample_details a,uhid_and_opdid b where a.opdid=b.opd_id and b.opd_id='$id'";
	}
	else
	{
		$str="select * from lab_sample_details where date between '$from' and '$to'";	
	}
	
	?>
	<table class="table table-bordered table-report table-condensed">
	<tr>
		<th>#</th> <th>Name</th> <th>Age/Sex</th> <th>OPD/IPD</th> <th>BarcodeID</th> <th>Time/Date</th><th>User</th><th></th>
	</tr>
	<?php
	$i=1;
	
	
	$qry=mysqli_query($link,$str);
	while($q=mysqli_fetch_array($qry))
	{
		$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$q[patient_id]'"));
		$ename=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$q[user]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $info[name];?></td>
			<td><?php echo $info[age]." ".$info[age_type]."/".$info[sex];?></td>
			<td><?php echo $q[opdid];?></td>
			<td><?php echo $q[barcode_id];?></td>
			<td><?php echo convert_time($q[time])."/".convert_date($q[date]);?></td>
			<td><?php echo $ename[name];?></td>
			<td><button class="btn btn-info btn-mini" onclick="view_sample('<?php echo $q[barcode_id];?>')">View</button></td>
		</tr>
		<?php
		$i++;
	}
	?>
	</table>
	<?php
}
?>
