<?php
session_start();
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("h:i:s");
$ip_addr=$_SERVER["REMOTE_ADDR"];

$emp_qry=mysqli_query($link, " SELECT `emp_id` FROM `employee` WHERE `password`!='' ORDER BY `emp_id` ASC ");
while($emp_info=mysqli_fetch_array($emp_qry))
{
	$log_info=mysqli_fetch_array(mysqli_query($link, " SELECT `status` FROM `login_activity` WHERE `emp_id`='$emp_info[emp_id]' ORDER BY `slno` DESC LIMIT 0,1 "));
	
	if($log_info["status"]==1)
	{
		if(mysqli_query($link, " INSERT INTO `login_activity`(`emp_id`, `status`, `remark`, `date`, `time`, `ip_addr`) VALUES ('$emp_info[emp_id]','0','other','$date','$time','$ip_addr') "))
		{
			
		}
		else
		{
			break;
		}
	}
}
session_destroy();

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Runtime Error</title>
        <meta name="viewport" content="width=device-width" />
        <style>
         body {font-family:"Verdana";font-weight:normal;font-size: .7em;color:black;} 
         p {font-family:"Verdana";font-weight:normal;color:black;margin-top: -5px}
         b {font-family:"Verdana";font-weight:bold;color:black;margin-top: -5px}
         H1 { font-family:"Verdana";font-weight:normal;font-size:18pt;color:red }
         H2 { font-family:"Verdana";font-weight:normal;font-size:14pt;color:maroon }
         pre {font-family:"Consolas","Lucida Console",Monospace;font-size:11pt;margin:0;padding:0.5em;line-height:14pt}
         .marker {font-weight: bold; color: black;text-decoration: none;}
         .version {color: gray;}
         .error {margin-bottom: 10px;}
         .expandable { text-decoration:underline; font-weight:bold; color:navy; cursor:hand; }
         @media screen and (max-width: 639px) {
          pre { width: 440px; overflow: auto; white-space: pre-wrap; word-wrap: break-word; }
         }
         @media screen and (max-width: 479px) {
          pre { width: 280px; }
         }
        </style>
    </head>

    <body bgcolor="white">

            <span><H1>Server Error in '/' Application.<hr width=100% size=1 color=silver></H1>

            <h2> <i>Runtime Error</i> </h2></span>

            <font face="Arial, Helvetica, Geneva, SunSans-Regular, sans-serif ">

            <b> Description: </b>An exception occurred while processing your request. Additionally, another exception occurred while executing the custom error page for the first exception. The request has been terminated.
            <br><br>

    </body>
</html>
