<?php include('../../includes/connection.php'); ?>
<html>
	<style>
		 input[type="text"]
         {
         border:none;
         }
         @media print{
         .noprint{
         display:none;
         }
         }
		 @media screen
         {
         body {padding: 20px 0;}
         }
	</style>
<head>
	<title>Rate Chart of Different Grades of Surgery</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
</head>
<body>
	<div class="container">
		<div class="text-center">
			<h3>Rate Chart of Different Grades of Surgery</h3>
			<div class="noprint "><input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()"></div>
		</div>
		<table class="table table-condensed table-bordered" id="tblData">
			<tr>
				<th>#</th>
				<th>Grade</th>
				<th>Cabin</th>
				<th>Type</th>
				<th>Amount</th>
			</tr>
			<?php
			$n=1;
			$q=mysqli_query($link,"SELECT * FROM `ot_resource_master` ORDER BY `grade_id`,`ot_cabin_id`,`type_id`");
			while($r=mysqli_fetch_array($q))
			{
				$gr=mysqli_fetch_array(mysqli_query($link,"SELECT `grade_name` FROM `ot_grade_master` WHERE `grade_id`='$r[grade_id]'"));
				$t=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$r[type_id]'"));
				$cab=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_cabin_name` FROM `ot_cabin_master` WHERE `ot_cabin_id`='$r[ot_cabin_id]'"));
				//$nm=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[emp_id]'"));
			?>
			<tr class="nm">
				<!--<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $n;?></td>-->
				<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $n;?></td>
				<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $gr['grade_name'];?></td>
				<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $cab['ot_cabin_name'];?></td>
				<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo $t['type'];?></td>
				<td style="cursor:pointer;" onclick="det('<?php echo $r['id'];?>')"><?php echo number_format($r['charge_id'],2);?></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
	</div>
</body>
</html>

