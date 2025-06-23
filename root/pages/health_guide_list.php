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
$super=$_GET['super'];

if($super==0)
{
	$qry="select * from health_guide order by name ";
	$header_str="<h3>Health Agent List</h3>";
}else
{
	$qry="select * from super_health_guide order by name ";
	$header_str="<h3>Health Guide List</h3>";
}

?>

<div class="container">
	<center>
		<?php echo $header_str; ?>
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
		   <td >Address</td>
		   <td >Phone</td>
		   <td >Email</td>
		</tr>
		 
			<?php
				$refcase=0;
				$i=1;
				$qpatient=mysqli_query($link, $qry);
				while($qpatient1=mysqli_fetch_array($qpatient))
				{
			
			?>
			<tr>
				  <td ><?php echo $i;?></td> 
				  <td ><?php echo $qpatient1['hguide_id'];?></td> 
				  <td ><?php echo $qpatient1['name'];?></td> 
				  <td ><?php echo $qpatient1['address'];?></td> 
				  <td ><?php echo $qpatient1['phone'];?></td> 
				  <td ><?php echo $qpatient1['email'];?></td>
			</tr>
			<?php
			
			$i++;}?>
  
	</table>
 </div>  
</body>
</html>

