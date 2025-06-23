<html>
<head>
<title></title>

<style>

body {
	padding: 10px;
}
@media print{
 input[type="button"]{
	 display:none;
 }
}


input[type="text"]{
	border:none;
	font-size:12px;
}


#mytable1 td{
	font-size:13px;
	color:red;
	font-weight:bold;
}

.line td{border-top:1px solid;}

*{font-size:13px;}

</style>
<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
      <link href="../../css/bootstrap-theme.css" type="text/css" rel="stylesheet"/>
      <link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
</head>
<body>
<?php
include'../../includes/connection.php';

$date1=$_GET['date1'];
$date2=$_GET['date2'];


//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-y', $timestamp);
return $new_date;
}


function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}

?>

<div class="container">
	<center>
		<h3>OPD Doctor Room List</h3>
	</center>
  <table width="100%">
	  <tr>
		<td colspan="6" style="text-align:right"><div class="noprint"><input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div></td>
	  </tr>
  </table>
	 <table class="table table-bordered table-condensed table-report">
		<tr bgcolor="#EAEAEA" class="bline">
		   <td >#</td>
		   <td >ID</td>
		   <td >Name</td>
		</tr>
		 
			<?php
				$refcase=0;
				$i=1;
				$qpatient=mysqli_query($GLOBALS["___mysqli_ston"], "select * from opd_doctor_room order by room_name ");
				while($qpatient1=mysqli_fetch_array($qpatient)){
			
			?>
			<tr>
				  <td ><?php echo $i;?></td> 
				  <td ><?php echo $qpatient1['room_id'];?></td> 
				  <td ><?php echo $qpatient1['room_name'];?></td> 
			</tr>
			<?php
			
			$i++;}?>
  
	</table>
 </div>  
</body>
</html>

