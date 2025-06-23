<?php
include("../../includes/connection.php");

$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$tid=$_POST['tid'];


$file = $_FILES['qqfile'];
$uploadDirectory = '../../pad_images';
$target = $uploadDirectory.DIRECTORY_SEPARATOR.$file['name'];
$result = null;
if (move_uploaded_file($file['tmp_name'], $target)){
    $result = array('success'=> true);
    $result['uploadName'] = $file['name'];
    
   
   $fname="../pad_images/".$file[name];
   $mx=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select max(img_no) as m_img from image_temp where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and `testid`='$tid' "));
   
   $img_no=$mx[m_img]+1;
   
    mysqli_query($GLOBALS["___mysqli_ston"]," INSERT INTO `image_temp`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `Path`, `img_no`) VALUES('$uhid','$opd_id','$ipd_id','$batch_no','$tid','$fname','$img_no')");
    
    
} else {
    $result = array('error'=> 'Upload failed');
}
header("Content-Type: text/plain");
echo json_encode($result);
?>
