<?php
session_start();
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("h:i:s");
$ip_addr=$_SERVER["REMOTE_ADDR"];

if($date>=$our_client["n_date"])
{
	if(!isset($_SESSION["n_date"]))
	{
		$_SESSION["n_date"]=$date;
		$_SESSION["n_time"]=$time;
	}
	
	$time_diff=abs(strtotime($time)-strtotime($_SESSION["n_time"]));
	
	//echo "<br>".$time_diff;
	
	if($time_diff>20)
	{
		$_SESSION["n_date"]=$date;
		$_SESSION["n_time"]=$time;
		//print_r($_SESSION);
?>
    <!--<div style="color:white;">
		<br><br><b> 400 Bad Request. An exception occurred while processing your request
		<b><br>
    </div>-->
    <!--<div class="widget-content">
		<div class="alert alert-error alert-block"> <a class="close" data-dismiss="alert" href="#">×</a>
			<h4 class="alert-heading">Error!</h4>
			You're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, vel scelerisque nisl consectetur et.
		</div>
		<div class="alert alert-error">
		  <button class="close" data-dismiss="alert">×</button>
		  <strong>Error!</strong> Nulla vitae elit libero, a pharetra augue. Praesent commodo </div>
	  </div>
	</div>-->
	<script>
		$(window).load(function(){
			alert("BAD REQUEST ERRMODE_EXCEPTION");
		});
	</script>
<?php
	}
}
?>
