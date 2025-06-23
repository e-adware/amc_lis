<?php
include("../../includes/connection.php");
$uploaddir = '../../pad_images/'; 
$file = $uploaddir."ayussh_".basename($_FILES['uploadfile']['name']); 
$file_name= "ayussh_".$_FILES['uploadfile']['name']; 

$file1 = "../pad_images/ayussh_".basename($_FILES['uploadfile']['name']); 

$uhid=$_GET['uhid'];
$opd_id=$_GET['opd_id'];
$ipd_id=$_GET['ipd_id'];
$batch_no=$_GET['batch_no'];
 
if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) { 
  echo "success";
 if($_GET[id])
 {
	  $q=mysqli_query($GLOBALS["___mysqli_ston"], "select * from image_temp where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=1");
	  if(mysqli_num_rows($q)>0)
	  {
		  mysqli_query($GLOBALS["___mysqli_ston"], "delete from image_temp where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=1");
		  mysqli_query($GLOBALS["___mysqli_ston"], "insert into image_temp values ('$uhid','$opd_id','$ipd_id','$batch_no','$file1','1')");
	  }
	  else
	  {
		  mysqli_query($GLOBALS["___mysqli_ston"], "insert into image_temp values ('$uhid','$opd_id','$ipd_id','$batch_no','$file1','1')");
	  }
 }
 else
 {
	  $q=mysqli_query($GLOBALS["___mysqli_ston"], "select * from image_temp where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=2");
	  if(mysqli_num_rows($q)>0)
	  {
		  mysqli_query($GLOBALS["___mysqli_ston"], "delete from image_temp where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and slno=2");
		  mysqli_query($GLOBALS["___mysqli_ston"], "insert into image_temp values ('$uhid','$opd_id','$ipd_id','$batch_no','$file1','2')");
	  }
	  else
	  {
		  mysqli_query($GLOBALS["___mysqli_ston"], "insert into image_temp values ('$uhid','$opd_id','$ipd_id','$batch_no','$file1','2')");
	  }
 }
   
} 
else
{
	echo "error";
}

?>
