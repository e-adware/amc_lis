<?php
$link=($GLOBALS["___mysqli_ston"] = mysqli_connect("localhost", "root", "penguin"));
((bool)mysqli_query($link, "USE " . 'test'));
error_reporting(0);

$link_error="";
if (!$link) {
    //die('Connect Error: ' . mysqli_connect_error());
    $link_error=mysqli_connect_error()." Local";
}

// Project name, logo and address
$brandLogo = "images/client_logo.jpg";
$location = "Guwahati, Assam";
$brand="HM HIS";

// Client
$center="HM Hospital";
$signature="For HM Hospital";

$code="HIS";
?>
