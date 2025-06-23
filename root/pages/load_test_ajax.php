<?php
include("../../includes/connection.php");

$test=$_POST['test'];
$uhid=$_POST['uhid'];
$center_no=$_POST['center_no'];
$cat=$_POST['cat'];
$cat=explode("@", $cat);
$reg_category=$cat[0];
$reg_dept=$cat[1];

$reg_category_str="";
$reg_dept_str="";
if($reg_category>0)
{
	$reg_category_str=" and category_id='$reg_category'";
}
if($reg_dept>0)
{
	$reg_dept_str=" and type_id='$reg_dept'";
}

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `sex` FROM `patient_info` WHERE `patient_id`='$uhid' "));

$pat_sex=$pat_info["sex"];

$sex_str="";
if($pat_sex=="Male")
{
	$sex_str=" AND (sex='M' OR sex='all')";
}
if($pat_sex=="Female")
{
	$sex_str=" AND (sex='F' OR sex='all')";
}


//~ $pat_center_qry=mysqli_fetch_array(mysqli_query($link, " SELECT `center_no` FROM `patient_info` WHERE `patient_id`='$uhid' "));	
//~ $pat_center=$pat_center_qry["center_no"];

if($test=="")
{
	$q="select * from testmaster where testid>0 $reg_category_str $reg_dept_str $sex_str order by testname";
}
else
{
	$q="select * from testmaster where testname like '%$test%' $reg_category_str $reg_dept_str $sex_str order by testname";
}

//echo $q;

$data=mysqli_query($link, $q);
?>

<table class="table   table-bordered table-condensed" border="1" id="test_table" width="100%">
	<tr>
		<th>Sl No</th>
		<th>Test Name</th>
		<th>Rate</th>
	</tr>
<?php
$i=1;



while($d=mysqli_fetch_array($data))
{
	$rate=mysqli_fetch_array(mysqli_query($link, "select rate from testmaster_rate where testid='$d[testid]' and centreno='$center_no'"));	
	if($rate['rate'])
	{
		$drate=$rate['rate'];	
	}
	else
	{
		$drate=$d['rate'];
	}
	//$drate=$d['rate'];
	
	?>
	<!--<tr <?php echo "id=td".$i;?> onclick="load_test2('<?php echo $d['testid'];?>','<?php echo $d['testname'];?>','<?php echo $drate;?>')" style="cursor:pointer">-->
	<tr <?php echo "id=td".$i;?> onclick="load_test_click('<?php echo $d['testid'];?>','<?php echo $d['testname'];?>','<?php echo $drate;?>')" style="cursor:pointer">
		<td width="5%" class=test<?php echo $i;?> id=test<?php echo $i;?>>
			<?php echo $i;?><input type="hidden" class="test<?php echo $i;?>" value="<?php echo $d['testid'];?>"/>
		</td>
		<td style="text-align:left" width="35%" <?php echo "class=test".$i;?>>
			<?php echo $d['testname'];?>
		</td>
	<?php
	echo "<td  width=30% class=test$i>$drate</td></tr>";
	$i++;
}
	
?>
</table>
