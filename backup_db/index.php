<?php
session_start();
include("../includes/connection.php");

$date = date("Y-m-d");
$time = date("H:i:s");

$form_show = 1;
$hide_container = false;
$show_loader = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emp_id = mysqli_real_escape_string($link, $_POST["emp_id"]) ?? "";
    $pass 	= mysqli_real_escape_string($link, $_POST["pass"]) ?? "";
    
    $md5_pass = md5($pass);
    
    $chk = mysqli_fetch_assoc(mysqli_query($link, "SELECT `emp_id` FROM `employee` WHERE `emp_id`='$emp_id' AND `password`='$md5_pass' AND `levelid`=1"));
    
    //if ($pass === "1234") {
    if ($chk) {
        $form_show = 1; // Keep rendering form so HTML exists
        $hide_container = true;
        $show_loader = true;
        
        //include("linear_loading_text.php");
        
        // Record
		mysqli_query($link, "INSERT INTO `backup_record`(`slno`, `backup_date`, `type`, `user`, `date`, `time`, `ip_addr`) VALUES (NULL,NULL,'0','$emp_id','$date','$time','$ip_addr')");
        
        $_SESSION['allow_backup'] = true;
        header("Location: backup_db.php");
        exit();
        
    } else {
        $form_show = 1;
        $error_msg = "Incorrect Input!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Database</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn-download {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-download:hover {
            background-color: #218838;
        }

        .btn-back {
            width: 100%;
            padding: 10px;
            background-color: #000;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-back:hover {
            background-color: #000;
        }

        .error {
            color: red;
            text-align: center;
        }

        .loader {
            text-align: center;
            margin-top: 20px;
        }
    </style>
    <script src="../js/jquery.min.js"></script>
    <script>
		 $(document).ready(function() {
			$('input').keyup(function() {
				document.getElementById("error_div").innerHTML = "";
			});
		});
        function validateForm() {
            const empId = document.forms["backupForm"]["emp_id"].value;
            const pass = document.forms["backupForm"]["pass"].value;

            if (empId === "") {
				$("#emp_id").focus();
                document.getElementById("error_div").innerHTML = "Both fields are required!";
                return false;
            }

            if (pass === "") {
				$("#pass").focus();
                document.getElementById("error_div").innerHTML = "Both fields are required!";
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <?php if ($form_show == 1) { ?>
        <div class="container" id="container">
            <h2>Backup Database(Admin)</h2>
            <form method="POST" name="backupForm" action="" onsubmit="return validateForm()">
                <input type="text" name="emp_id" id="emp_id" placeholder="User ID" autofocus>
                <input type="password" name="pass" id="pass" placeholder="Password">
                <div id="error_div" class="error">
                    <?php if (!empty($error_msg)) echo $error_msg; ?>
                </div>
                <button type="submit" class="btn-download">
					<img src="../images/download.png" style="width:20px;">
					Download Backup
				</button>
                <br>
                <br>
                <button type="button" class="btn-back" onclick="window.location.href='../'">Go Back</button>
            </form>
        </div>
    <?php } ?>

    <?php if ($show_loader): ?>
        <div class="loader" id="loader">
            <?php include("linear_loading_text.php"); ?>
        </div>
    <?php endif; ?>

    <?php if ($hide_container): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById('container').style.display = 'none';
            });
        </script>
    <?php endif; ?>
</body>

</html>
