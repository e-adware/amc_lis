<?php

$link2=($GLOBALS["___mysqli_ston"] = mysqli_connect("192.185.168.240","ead1234_eaduser","[SS#UmhTw1uu"));
((bool)mysqli_query($link2, "USE " . 'ead1234_eadware'));

$link2_error="";
if (!$link2) {
    //die('Connect Error: ' . mysqli_connect_error());
    $link2_error=mysqli_connect_error()." Online";
}

?>
