<?php
include("../../includes/connection.php");

$i=$_POST['filename'];
$fname=$_POST['fname'];
$file_name=$_FILES['file']['name'];
$file_type=$_FILES['file']['type'];
$file_size=(($_FILES['file']['size'])/1000)." KB";
//$source=$_FILES['file']['tmp_name'];
$file_ext=strtolower(end(explode('.',$_FILES['file']['name'])));
$fl=$fname.".".$file_ext;
$target = "../../emp_documents/".$fl;
//move_uploaded_file($source,$target);

$tp=explode("/",$file_type);
$vl=explode("@",$i);
/*
if($tp[1]=="vnd.openxmlformats-officedocument.wordprocessingml.document")
$typ="docx";
elseif($tp[1]=="msword")
$typ="docx";
elseif($tp[1]=="svg+xml")
$typ="svg";
elseif($tp[1]=="plain")
$typ="txt";
elseif($tp[1]=="octet-stream")
$typ="txt";
elseif($tp[1]=="")
$typ="pdf";
else
$typ=$tp[1];
*/
$fl=$fname.".".$file_ext;

$temp=explode(".", $_FILES["file"]["name"]);
$newfilename=$fname.$vl[0].".".$file_ext;

if(move_uploaded_file($_FILES["file"]["tmp_name"],"../../emp_documents/".$newfilename))
mysqli_query($link,"UPDATE `employee_doc` SET `file_name_emp`='$newfilename' WHERE `emp_id`='$vl[0]' AND `file_name`='$vl[1]'");
//echo $tp[1];

?>
