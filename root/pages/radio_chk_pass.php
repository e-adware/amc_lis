<?php
session_start();
include("../../includes/connection.php");

$emp_id=trim($_SESSION['emp_id']);

$val=$_POST['val'];
$category_id=$_POST['category_id'];

$chk=mysqli_fetch_array(mysqli_query($link,"select * from lab_doctor where password='$val'"));

//~ if($emp_id=='101' || $emp_id=='102')
//~ {
	//~ //$chk=mysqli_fetch_array(mysqli_query($link,"select * from lab_doctor where category='$category_id' and  password='$val'"));
	//~ $chk=mysqli_fetch_array(mysqli_query($link,"select * from lab_doctor where password='$val'"));
//~ }else
//~ {
	//~ $chk=mysqli_fetch_array(mysqli_query($link,"select * from lab_doctor where password='$val' and `id`='$emp_id'"));
//~ }

if($chk[name])
{
	echo $chk[id]."#".$chk[name];	
}
else
{
	echo "1";	
}
?>
