<?php
include('../../includes/connection.php');
include("../../includes/global.function.php");
include("../app/init.php");

$date=date('Y-m-d');
$time=date('H:i:s');

$type=$_POST['type'];

if($type==1)
{
	$dept=$_POST['dept'];
	$dept_str="";
	if($dept)
	{
		$dept_str=" AND a.`type_id`='$dept'";
	}
	//~ $res=$db->select_distinct(['testmaster.testid','testmaster.testname'],"testmaster")
			//~ ->inner_join("patient_test_details","testmaster.testid","patient_test_details.testid")
			//~ ->whereNotEmpty('testmaster.testname')
			//~ ->order('testmaster.testname', 'ASC')
			//~ ->fetch_all();
	$db= new Db_Loader();
	$res=$db->setQuery("SELECT DISTINCT a.`testid`, a.`testname` FROM `testmaster` a, `testresults` b WHERE a.`testid`=b.`testid` $dept_str ORDER BY a.`testname`")->fetch_all();
	$arr=array();
	foreach($res as $r)
	{
		array_push($arr, ['id'=>$r['testid'], 'name'=>$r['testname']]);
	}
	echo json_encode($arr);
}

if($type==2)
{
	$srch=mysqli_real_escape_string($link,$_POST['srch']);
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$dept=$_POST['dept'];
	$test=$_POST['test'];
	$dept_str="";
	$test_str="";
	if($dept)
	{
		$dept_str=" AND a.`type_id`='$dept'";
	}
	if($test)
	{
		$test_str=" AND a.`testid`='$test'";
	}
	//print_r($row);
	
	$opdTot=0;
	$ipdTot=0;
	
	echo "From ".convert_date($fdate)." to ".convert_date($tdate);
	?>
	<span style="float:right;">
		<button type="button" class="btn btn-primary btn_act" onclick="report_print('<?php echo base64_encode($fdate);?>','<?php echo base64_encode($tdate);?>','<?php echo base64_encode($dept);?>','<?php echo base64_encode($test);?>','<?php echo base64_encode($ward);?>','<?php echo base64_encode($type);?>')">Print</button>
	</span>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Test Name</th>
			<th>OPD</th>
			<th>IPD</th>
			<th>Total</th>
		</tr>
		<?php
		$db= new Db_Loader();
		$row=$db->setQuery("SELECT DISTINCT a.`type_id`, a.`type_name` FROM `testmaster` a, `patient_test_details` b WHERE a.`testid`=b.`testid` $dept_str $test_str AND b.`date` BETWEEN '$fdate' AND '$tdate' ORDER BY a.`type_name`")->fetch_all();
		foreach($row as $r)
		{
		?>
		<tr>
			<th colspan="5" style="background:#DEDEDE;"><?php echo $r['type_name']." [".$r['type_id']."]";?></th>
		</tr>
		<?php
		$j=1;
		$db= new Db_Loader();
		$res=$db->setQuery("SELECT DISTINCT a.`testid`, a.`testname` FROM `testmaster` a, `patient_test_details` b WHERE a.`testid`=b.`testid` AND a.`type_id`='$r[type_id]' $dept_str $test_str AND b.`date` BETWEEN '$fdate' AND '$tdate' ORDER BY a.`testname`")->fetch_all();
		foreach($res as $tt)
		{
			$db= new Db_Loader();
			$opdCount=$db->setQuery("SELECT COUNT(DISTINCT a.`opd_id`) AS `counts` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`type`='1' AND a.`testid`='$tt[testid]' AND a.`date` BETWEEN '$fdate' AND '$tdate'")->fetch_row();
			
			$db= new Db_Loader();
			$ipdCount=$db->setQuery("SELECT COUNT(DISTINCT a.`opd_id`) AS `counts` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`type`='2' AND a.`testid`='$tt[testid]' AND a.`date` BETWEEN '$fdate' AND '$tdate'")->fetch_row();
			
			$opdTot+=$opdCount['counts'];
			$ipdTot+=$ipdCount['counts'];
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $tt['testname']." [".$tt['testid']."]";?></td>
			<td><?php echo $opdCount['counts'];?></td>
			<td><?php echo $ipdCount['counts'];?></td>
			<td><?php echo ($opdCount['counts']+$ipdCount['counts']);?></td>
		</tr>
		<?php
		$j++;
		}
		}
		?>
		<tr>
			<th></th>
			<th>Total</th>
			<th><?php echo $opdTot;?></th>
			<th><?php echo $ipdTot;?></th>
			<th><?php echo ($opdTot+$ipdTot);?></th>
		</tr>
	</table>
	<?php
}

if($type==3)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$dept=$_POST['dept'];
	$test=$_POST['test'];
	$ward=$_POST['ward'];
	$dept_str="";
	$test_str="";
	$ward_str="";
	if($dept)
	{
		$dept_str=" AND a.`type_id`='$dept'";
	}
	if($test)
	{
		$test_str=" AND a.`testid`='$test'";
	}
	if($ward)
	{
		$ward_str=" AND c.`ward`='$ward'";
	}
	
	$opdTot=0;
	$ipdTot=0;
	echo "From ".convert_date($fdate)." to ".convert_date($tdate);
	?>
	<span style="float:right;">
		<button type="button" class="btn btn-primary btn_act" onclick="report_print('<?php echo base64_encode($fdate);?>','<?php echo base64_encode($tdate);?>','<?php echo base64_encode($dept);?>','<?php echo base64_encode($test);?>','<?php echo base64_encode($ward);?>','<?php echo base64_encode($type);?>')">Print</button>
	</span>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Test Name</th>
			<th>OPD</th>
			<th>IPD</th>
			<th>Total</th>
		</tr>
		<?php
		$db= new Db_Loader();
		$row=$db->setQuery("SELECT DISTINCT c.`ward` FROM `testmaster` a, `patient_test_details` b, `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND b.`patient_id`=c.`patient_id` AND b.`opd_id`=c.`opd_id` $dept_str $test_str $ward_str AND b.`date` BETWEEN '$fdate' AND '$tdate' ORDER BY c.`ward`")->fetch_all();
		foreach($row as $r)
		{
			$opdsubTot=0;
			$ipdsubTot=0;
			$db= new Db_Loader();
			$wardNm=$db->select(['ward_name'],"ward_master")->where(['id'=>$r['ward']])->fetch_row();
		?>
		<tr>
			<th colspan="5" style="background:#DEDEDE;"><?php echo $wardNm['ward_name'];?></th>
		</tr>
		<?php
		$j=1;
		$db= new Db_Loader();
		$res=$db->setQuery("SELECT DISTINCT a.`testid`, a.`testname` FROM `testmaster` a, `patient_test_details` b, `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND b.`patient_id`=c.`patient_id` AND b.`opd_id`=c.`opd_id` AND c.`ward`='$r[ward]' $dept_str $test_str AND b.`date` BETWEEN '$fdate' AND '$tdate' ORDER BY a.`testname`")->fetch_all();
		foreach($res as $tt)
		{
			$db= new Db_Loader();
			$opdCount=$db->setQuery("SELECT COUNT(DISTINCT a.`opd_id`) AS `counts` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`type`='1' AND a.`testid`='$tt[testid]' AND b.`ward`='$r[ward]' AND a.`date` BETWEEN '$fdate' AND '$tdate'")->fetch_row();
			
			$db= new Db_Loader();
			$ipdCount=$db->setQuery("SELECT COUNT(DISTINCT a.`opd_id`) AS `counts` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`type`='2' AND a.`testid`='$tt[testid]' AND b.`ward`='$r[ward]' AND a.`date` BETWEEN '$fdate' AND '$tdate'")->fetch_row();
			
			$opdsubTot+=$opdCount['counts'];
			$ipdsubTot+=$ipdCount['counts'];
			$opdTot+=$opdCount['counts'];
			$ipdTot+=$ipdCount['counts'];
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $tt['testname']." [".$tt['testid']."]";?></td>
			<td><?php echo $opdCount['counts'];?></td>
			<td><?php echo $ipdCount['counts'];?></td>
			<td><?php echo ($opdCount['counts']+$ipdCount['counts']);?></td>
		</tr>
		<?php
		$j++;
		}
		?>
		<tr>
			<th></th>
			<th style="text-align:right">Sub Total :</th>
			<th><?php echo $opdsubTot;?></th>
			<th><?php echo $ipdsubTot;?></th>
			<th><?php echo ($opdsubTot+$ipdsubTot);?></th>
		</tr>
		<?php
		
		}
		?>
		<tr>
			<th></th>
			<th style="text-align:right">Total:</th>
			<th><?php echo $opdTot;?></th>
			<th><?php echo $ipdTot;?></th>
			<th><?php echo ($opdTot+$ipdTot);?></th>
		</tr>
	</table>
	<?php
}

if($type==4)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$dept=$_POST['dept'];
	$test=$_POST['test'];
	$ward=$_POST['ward'];
	$user=$_POST['user'];
	
	$opdTot=0;
	$ipdTot=0;
	echo "From ".convert_date($fdate)." to ".convert_date($tdate);
	?>
	<span style="float:right;">
		<button type="button" class="btn btn-primary btn_act" onclick="report_print('<?php echo base64_encode($fdate);?>','<?php echo base64_encode($tdate);?>','<?php echo base64_encode($dept);?>','<?php echo base64_encode($test);?>','<?php echo base64_encode($ward);?>','<?php echo base64_encode($type);?>')">Print</button>
	</span>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Ward Name</th>
			<th>OPD</th>
			<th>IPD</th>
			<th>Total</th>
		</tr>
		<?php
		$j=1;
		$db=new Db_Loader();
		if($ward)
		{
			$res=$db->select_distinct(['ward'],"uhid_and_opdid")->where(['ward'=>$ward])->fetch_all();
		}
		else
		{
			$res=$db->select_distinct(['ward'],"uhid_and_opdid")->order('ward','ASC')->fetch_all();
		}
		foreach($res as $r)
		{
			$db= new Db_Loader();
			$wardNm=$db->select(['ward_name'],"ward_master")->where(['id'=>$r['ward']])->fetch_row();
			
			$db= new Db_Loader();
			$opdCount=$db->setQuery("SELECT COUNT(DISTINCT `opd_id`) AS `counts` FROM `uhid_and_opdid` WHERE `type`='1' AND `ward`='$r[ward]' AND `date` BETWEEN '$fdate' AND '$tdate'")->fetch_row();
			
			$db= new Db_Loader();
			$ipdCount=$db->setQuery("SELECT COUNT(DISTINCT `opd_id`) AS `counts` FROM `uhid_and_opdid` WHERE `type`='2' AND `ward`='$r[ward]' AND `date` BETWEEN '$fdate' AND '$tdate'")->fetch_row();
			
			$opdTot+=$opdCount['counts'];
			$ipdTot+=$ipdCount['counts'];
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $wardNm['ward_name'];?></td>
			<td><?php echo $opdCount['counts'];?></td>
			<td><?php echo $ipdCount['counts'];?></td>
			<td><?php echo ($opdCount['counts']+$ipdCount['counts']);?></td>
		</tr>
		<?php
		$j++;
		}?>
		<tr>
			<td></td>
			<th>Total :</th>
			<th><?php echo $opdTot;?></th>
			<th><?php echo $ipdTot;?></th>
			<th><?php echo ($opdTot+$ipdTot);?></th>
		</tr>
	</table>
	<?php
}

?>
