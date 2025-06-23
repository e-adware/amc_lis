<html>
<head>
<title>Center Test Rate</title>
<link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css">
<style>
@media print{
 .noprint{
	 display:none;
 }
}
.line td{border-bottom:1px solid;}
</style>

</head>
<body>

<?php
include'../../includes/connection.php';
//$date1 = $_GET['date1'];

$mkid=$_GET['mkid'];

$center_name=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$mkid' "));

?>

<div class="container-fluid">
	<?php
	include('page_header.php');
	?>
	<center><h4>Center Name: <?php echo $center_name["centrename"]; ?></h4></center>
	<table class="table">
		<tr>
			<td style="text-align:right"><span class="noprint"><input class="btn btn-custom" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" />&nbsp;<input class="btn btn-custom" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></span></td>
		</tr>
	</table>
	<table class="table table-condensed table-bordered" style="font-size:12px;">
		<tr bgcolor="#EAEAEA">
			<td>#</td>
			<td>Test Name</td>
			<td>Normal Rate</td>
			<td>Center Rate</td>
		</tr>
		<?php
		$i=1;
		$qrslct=mysqli_query($link,"select a.testid,a.rate,b.testname  from  testmaster_rate a,testmaster b where a.testid=b.testid and a.centreno='$mkid' order by b.testname");  

		while($qrslct1=mysqli_fetch_array($qrslct))
		{
			$testrate=mysqli_fetch_array(mysqli_query($link, "select rate from testmaster where  testid='$qrslct1[testid]'"));
		?>
		<tr class"line">
			<td><?php echo $i;?></td>
			<td><?php echo $qrslct1['testname'];?></td>
			<td><?php echo $testrate['rate'];?></td>
			<td><?php echo $qrslct1['rate'];?></td>
		</tr> 
		<?php
		$i++;
		}
		?>
		</tr>
	</table> 
</div>  
</body>
</html>

