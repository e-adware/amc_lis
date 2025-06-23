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

$branch_id=base64_decode(mysqli_real_escape_string($link, $_GET['branch_id']));

if($branch_id)
{
	$our_client=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$branch_id' limit 0,1 "));
}
else
{
	$our_client=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name`"));
}

?>

<div class="container">
	<center>
		<h3>Center List of <?php echo $our_client["name"]; ?></h3>
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
		   <td >Short name</td>
		   <td >Address</td>
		   <td >Contact Person </td>
		   <td >Phone</td>
		  
		</tr>
		 
			<?php
				$refcase=0;
				$i=1;
				$qpatient=mysqli_query($GLOBALS["___mysqli_ston"], "select * from centremaster where branch_id='$branch_id' order by centrename ");
				while($qpatient1=mysqli_fetch_array($qpatient)){
			
			?>
			<tr>
				  <td ><?php echo $i;?></td> 
				  <td ><?php echo $qpatient1['centreno'];?></td> 
				  <td ><?php echo $qpatient1['centrename'];?></td> 
				  <td ><?php echo $qpatient1['short_name'];?></td> 
				  <td ><?php echo $qpatient1['add1'];?></td> 
				  <td ><?php echo $qpatient1['response_person'];?></td> 
				  <td ><?php echo $qpatient1['phoneno'];?></td> 
				 
			</tr>
			<?php
			
			$i++;}?>
  
	</table>
 </div>  
</body>
</html>

