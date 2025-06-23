<?php
include("../../includes/connection.php");
$val=$_POST['val'];
$phserchtype=$_POST['phserchtype'];

if($phserchtype==1)
{
	if($val)
	{
	  $qry="select * from ph_sell_master where bill_no like '$val%' and balance>0";	
	}	
	else
	{
	  $qry="select * from ph_sell_master where balance>0 order by bill_no desc limit 0,10";
	}
}
elseif($phserchtype==2)
{
	if($val)
	{
	  $qry="select * from ph_sell_master where  ipd_id like '$val%' and balance>0";	
	}	
	else
	{
	  $qry="select * from ph_sell_master where balance>0 order by bill_no desc limit 0,10";
	}
}
elseif($phserchtype==3)
{
	if($val)
	{
	  $qry="select a.*,b.uhid from ph_sell_master a,patient_info b where a.patient_id=b.patient_id and b.patient_id like '$val%' and balance>0";	
	}	
	else
	{
	  $qry="select * from ph_sell_master where balance>0 order by bill_no desc limit 0,10";
	}
}
else
{
	if($val)
	{
	  $qry="select * from ph_sell_master where  customer_name like '%$val%' and balance>0";
	}	
	else
	{
	  $qry="select * from ph_sell_master where balance>0 order by bill_no desc limit 0,10";
	}
}




?>
<table class="table table-striped table-bordered table-condensed">
<th>Slno</th><th>Bill No</th><th>PIN</th><th>Name</th><th>Date</th><th>Balance</th><th>User</th>

<?php
$i=1;
$q1=mysqli_query($GLOBALS["___mysqli_ston"], $qry);
while($q=mysqli_fetch_array($q1))
{
 $vpin=$q['opd_id'];	
 if($q['opd_id']=="")	
 {
	 $vpin=$q['ipd_id'];
 }
 
 
$pname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select name from patient_info where patient_id='$q[patient_id]'"));
$uname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT `name` FROM `employee` WHERE `emp_id`='$q[user]'"));
//$bal=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select balance from patient_payment_details where patient_id='$q[patient_id]' and visit_no='$q[visit_no]'"));

?>    
<tr id="bal_tr<?php echo $i;?>" onClick="load_all_info('<?php echo $q['bill_no'] ?>','<?php echo $q['opd_id'] ?>')" style="cursor:pointer;">
    <td><?php echo $i;?></td>
    <td><?php echo $q['bill_no'];?></td>
    <td><?php echo $vpin;?></td>
    <td><?php echo $q['customer_name'];?></td>
    <td><?php echo $q['entry_date'];?></td>
    <td><?php echo $q['balance'];?></td>
    <td><?php echo $uname['name'];?>
    
    <div id="pat_reg<?php echo $i;?>" style="display:none"><?php echo $q['bill_no'];?></div>
    <div id="pat_vis<?php echo $i;?>" style="display:none"><?php echo $q['opd_id'];?></div>
    <!--<div id="pat_reg1<?php echo $i;?>" style="display:none"><?php echo $q['reg_no'];?></div>-->
    </td>
</tr>
  <?php
  $i++;
}
?>


</table>
