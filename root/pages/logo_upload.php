<?php
include("../../includes/connection.php");

print_r($_POST);
print_r($_FILES);

$branch_id = $_POST['branch_id_img']['name'];

$userfile_name = $_FILES['client_logo']['name'];

$file_name_parts = pathinfo($userfile_name);
$file_name_extension = $file_name_parts["extension"];
$file_name_custom=$branch_id.".".$file_name_extension;

$sourcePath = $_FILES['client_logo']['tmp_name']; // Storing source path of the file in a variable
$targetPath = "../../images/".$file_name_custom; // Target path where file is to be stored
//move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file

if(move_uploaded_file($sourcePath,$targetPath))
{
	mysqli_query($link," UPDATE `company_name` SET `client_logo`='$file_name_custom' WHERE `branch_id`='$branch_id' ");
}


?>
