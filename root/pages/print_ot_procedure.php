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
	<title>OT Procedures</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="span11">
				<div class="text-center">
					<h3>OT Procedure List</h3>
					<div class="noprint "><input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()"></div>
				</div>
				<table class="table table-bordered table-condensed table-report" style="width: 85%;">
					<tr>
						<th>#</th>
						<!--<th>Test ID</th>-->
						<th>Procedure Name</th>
					</tr>
					<?php 
						$n=1;
						
						$cl_hd_qry=mysqli_query($link, " SELECT * FROM `clinical_procedure_header` ORDER BY `name` ");
						while($cl_hd=mysqli_fetch_array($cl_hd_qry))
						{
							echo "<tr><th colspan='2'>$cl_hd[name]</th></tr>";
							
							$cl_item_qry=mysqli_query($link, " SELECT `name` FROM `clinical_procedure` WHERE `header_id`='$cl_hd[head_id]' ORDER BY `name` ");
							while($cl_item=mysqli_fetch_array($cl_item_qry))
							{
							?>
								<tr>
									<td><?php echo $n; ?></td>
									<td><?php echo $cl_item["name"]; ?></td>
								</tr>
							<?php
								$n++;
							}
						}
					?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>

