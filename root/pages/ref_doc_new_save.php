<?php
session_start();
include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);

$date=date("Y-m-d");
$time=date("H:i:s");

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$branch_id=$emp_info["branch_id"];

$name=mysqli_real_escape_string($link, $_POST["name"]);
$qual=mysqli_real_escape_string($link, $_POST["qual"]);
$add=mysqli_real_escape_string($link, $_POST["add"]);
$phone=mysqli_real_escape_string($link, $_POST["phone"]);
$email=mysqli_real_escape_string($link, $_POST["email"]);

mysqli_query($link, " INSERT INTO `refbydoctor_master`(`ref_name`, `qualification`, `address`, `phone`, `email`, `consultantdoctorid`, `emp_id`, `branch_id`, `user`, `date`, `time`) VALUES ('$name','$qual','$add','$phone','$email','0','0','$branch_id','$c_user','$date','$time') ");

 $last_row=mysqli_fetch_array(mysqli_query($link," SELECT `refbydoctorid` FROM `refbydoctor_master` WHERE `ref_name`='$name' AND `qualification`='$qual' AND `phone`='$phone' AND `email`='$email' ORDER BY `refbydoctorid` DESC LIMIT 1 "));

$refbydoctorid=$last_row["refbydoctorid"];

echo $name."-".$refbydoctorid;
?>
