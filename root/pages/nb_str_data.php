<?php
include("../../includes/connection.php");

$id=$_POST["id"];
$nb_text=$_POST["nb_text"];
$nb_text= str_replace("'", "''", "$nb_text");

mysqli_query($link, " UPDATE `nb_text` SET `nb_text`='$nb_text' WHERE `id`='$id' ");

?>
