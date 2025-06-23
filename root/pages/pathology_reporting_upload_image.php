<?php
session_start();
include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);

$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$testid=$_POST['testid'];

$pat_directory="";
if($opd_id)
{
	$pat_directory=str_replace("/","",$opd_id);
}
if($ipd_id)
{
	$pat_directory=str_replace("/","",$ipd_id);
}
$pat_directory.=$batch_no.$testid;
$file_path_name="pad_images/".$pat_directory;

$file = $_FILES['qqfile'];
$uploadDirectory = "../../pad_images";

if(!file_exists($uploadDirectory))
{
	mkdir($uploadDirectory, 0777, true);
}

$uploadDirectory.="/".$pat_directory;

if(!file_exists($uploadDirectory))
{
	mkdir($uploadDirectory, 0777, true);
}

$image_extension = pathinfo($file["name"], PATHINFO_EXTENSION);

$custom_name=$c_user.rand(10,99).".".$image_extension;

$target = $uploadDirectory.DIRECTORY_SEPARATOR.$custom_name;
$result = null;
if (move_uploaded_file($file['tmp_name'], $target)){
    $result = array('success'=> true);
    //$result['uploadName'] = $file['name'];
    $result['uploadName'] = $custom_name;
    
   
  // $fname="../pad_images/".$file["name"];
   $file_path_name.="/".$custom_name;
   $mx=mysqli_fetch_array(mysqli_query($link,"select max(img_no) as m_img from patient_test_images where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and `testid`='$testid' "));
   
   $img_no=$mx[m_img]+1;
   
    mysqli_query($link," INSERT INTO `patient_test_images`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `Path`, `img_no`) VALUES('$uhid','$opd_id','$ipd_id','$batch_no','$testid','$file_path_name','$img_no')");
    
    
} else {
    $result = array('error'=> 'Upload failed');
}
header("Content-Type: text/plain");
echo json_encode($result);

mysqli_close($link);
?>
