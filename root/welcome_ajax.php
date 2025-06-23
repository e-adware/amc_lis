<?php
include'../includes/connection.php';

$date=date("Y-m-d");
$time=date("H:i:s");

$msgId=base64_decode($_POST['msgId']);
$rv=$_POST['rv'];
$user=$_POST['user'];
$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `greetingsReview` WHERE `msg_id`='$msgId' AND `emp_id`='$user'"));
if(!$chk)
{
	mysqli_query($link,"INSERT INTO `greetingsReview`(`msg_id`, `emp_id`, `review`, `time`) VALUES ('$msgId','$user','$rv','$time')");
	echo "Thank you for your support";
}
else
{
	echo "Thank You";
}
?>
