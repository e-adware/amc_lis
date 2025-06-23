<?php

include("../../includes/connection.php");
include("../../includes/global.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST['type'];
//print_r($_POST);

if($type==1)
{
	$val=$_POST['val'];
	$srch="";
	if($val)
	{
		$srch=" AND `testname` like '%$val%'";
	}
	?>
	<table class="table table-condensed">
		<tr>
			<th>#</th>
			<th>Test Name</th>
			<th>Test Rate</th>
		</tr>
		<?php
		$j=1;
		$q=mysqli_query($link,"SELECT `testid`,`testname`,`rate` FROM `testmaster` WHERE `testname`!='' $srch ORDER BY `testname`");
		while($r=mysqli_fetch_array($q))
		{
			$arr=array();
			$arr['id']=$r['testid'];
			$arr['name']=$r['testname'];
		?>
		<tr id="doc<?php echo $j;?>" style="cursor:pointer;" onclick="doc_load('<?php echo $arr['id'];?>','<?php echo $arr['name'];?>')">
			<td>
				<?php echo $j;?>
				<div id="dvdoc<?php echo $j;?>" style="display:none;"><?php echo json_encode($arr);?></div>
			</td>
			<td><?php echo $r['testname'];?></td>
			<td><?php echo $r['rate'];?></td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
}

if($type==2)
{
	$pid=$_POST['pid'];
	$opd=$_POST['opd'];
	?>
	<table class="table table-condensed" id="mytable">
		<tr><th style="width:5%;">#</th><th>Test Name</th><th style="width:5%;"></th></tr>
		<?php
		$j=1;
		$q=mysqli_query($link,"SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$pid' AND `opd_id`='$opd'");
		while($r=mysqli_fetch_array($q))
		{
			$tst=mysqli_fetch_array(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$r[testid]'"));
		?>
		<tr class="allTr" id="tr<?php echo $r['testid'];?>">
			<td><?php echo $j;?></td>
			<td><input type="checkbox" class="tst_check" style="display:none;" value="<?php echo $r['testid'];?>" checked=""><?php echo $tst['testname'];?></td>
			<td><button type="button" class="btn btn-danger btn-mini" onclick="$(this).parent().parent().remove();set_sl();"><i class="icon-remove"></i></button></td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
}
?>