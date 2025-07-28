<?php
include("includes/connection.php");

$date = date("Y-m-d");
$time = date("H:i:s");

if ($_POST) {
    $pass = $_POST["pass"];
    if ($pass == "qcclear") {
        mysqli_query($link, "TRUNCATE TABLE `qc_baseline`");
        mysqli_query($link, "TRUNCATE TABLE `qc_lot_master`");
        mysqli_query($link, "TRUNCATE TABLE `qc_mapping`");
        mysqli_query($link, "TRUNCATE TABLE `qc_master`");
        mysqli_query($link, "TRUNCATE TABLE `qc_results`");
        mysqli_query($link, "TRUNCATE TABLE `qc_testmaster`");
    } else {
        echo "Incorrect Password !";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Clear Database</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
</head>

<body>
    <form method="POST" action="">
        <input type="password" name="pass" placeholder="password" autofocus>

        <br>
        <br>
        <button type="submit">Clear QC</button>
    </form>
</body>

</html>