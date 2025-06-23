<?php
include("../../includes/connection.php");
$val=$_POST['val'];
$chk=$_POST['chk'];


if($val=="000")
{
	$qry="select * from invest_patient_payment_details where balance>0 AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid`) order by slno desc";	
}
else if($val)
{
	$qry="select * from invest_patient_payment_details where opd_id like '$val%' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid`)";
}
else
{
	$qry="select * from invest_patient_payment_details where `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid`) order by slno desc limit 0,10";
}

//echo $qry;

?>
<table class="table table-striped table-bordered table-condensed">
<th>Slno</th><th>OPD ID</th><th>Name</th><th>Reg Date</th><th>Balance</th><th>User</th>

<?php
$i=1;
$q1=mysqli_query($GLOBALS["___mysqli_ston"], $qry);
while($q=mysqli_fetch_array($q1))
{
$pname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select name from patient_info where patient_id='$q[patient_id]'"));
$uname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT `name` FROM `employee` WHERE `emp_id`='$q[user]'"));
//$bal=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select balance from patient_payment_details where patient_id='$q[patient_id]' and visit_no='$q[visit_no]'"));

?>    
<tr id="bal_tr<?php echo $i;?>" onClick="load_all_info('<?php echo $q['patient_id'] ?>','<?php echo $q['opd_id'] ?>')" style="cursor:pointer;">
    <td><?php echo $i;?></td>
    <td><?php echo $q['opd_id'];?></td>
    <td><?php echo $pname['name'];?></td>
    <td><?php echo $q['date'];?></td>
    <td><?php echo $q['balance'];?></td>
    <td><?php echo $uname['name'];?>
    
    <div id="pat_reg<?php echo $i;?>" style="display:none"><?php echo $q['patient_id'];?></div>
    <div id="pat_vis<?php echo $i;?>" style="display:none"><?php echo $q['opd_id'];?></div>
    <!--<div id="pat_reg1<?php echo $i;?>" style="display:none"><?php echo $q['reg_no'];?></div>-->
    </td>
</tr>
  <?php
  $i++;
}
?>


</table>
