<?php
include('../../includes/connection.php');

$branch_id=mysqli_real_escape_string($link, base64_decode($_GET['branch_id']));
$category_id=mysqli_real_escape_string($link, base64_decode($_GET['category_id']));
$type_id=mysqli_real_escape_string($link, base64_decode($_GET['type_id']));
$sampleid=mysqli_real_escape_string($link, base64_decode($_GET['sampleid']));
$vac_id=mysqli_real_escape_string($link, base64_decode($_GET['vac_id']));
$equipment=mysqli_real_escape_string($link, base64_decode($_GET['equipment']));

?>
<html>
	<style>
		*{
			font-size: 12px;
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
	<title>Test Rate</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
	<link href="../../font-awesome/css/font-awesome.css" rel="stylesheet" />
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="span11">
				<div class="text-center">
					<h3>Test Rate</h3>
					<div class="noprint ">
						<button class="btn btn-info" onclick="javascript:window.print()"><i class="icon-print"></i> Print</button>
						<button class="btn btn-danger" onclick="javascript:window.close()"><i class="icon-off"></i> Close</button>
					</div>
				</div>
				<table class="table table-bordered table-condensed table-hover">
					<tr>
						<th>#</th>
						<!--<th>Test ID</th>-->
						<th>Test Name</th>
						<th style="text-align:center;">Test Rate</th>
					</tr>
					<?php
						$n=1;
						
						$cat_str=" SELECT distinct `category_id` FROM `testmaster` WHERE `testid`>0 ";
						if($category_id>0)
						{
							$cat_str.=" AND `category_id`='$category_id'";
						}
						if($type_id>0)
						{
							$cat_str.=" AND `type_id`='$type_id'";
						}
						if($sampleid>0)
						{
							$cat_str.=" AND `testid` IN(SELECT `TestId` FROM `TestSample` WHERE `SampleId`='$sampleid')";
						}
						if($vac_id>0)
						{
							$cat_str.=" AND `testid` IN(SELECT `testid` FROM `test_vaccu` WHERE `vac_id`='$vac_id')";
						}
						if($equipment>0)
						{
							$cat_str.=" AND `equipment`='$equipment'";
						}
						$cat_str.="  order by `category_id` ";
						
						$cat_qry=mysqli_query($link, $cat_str);
						while($cat=mysqli_fetch_array($cat_qry))
						{
							if($cat["category_id"]==1)
							{
								echo "<tr><th colspan='5'><center>Pathology</center></th></tr>";
							}
							if($cat["category_id"]==2)
							{
								echo "<tr><th colspan='5'><center>Radiology</center></th></tr>";
							}
							if($cat["category_id"]==3)
							{
								echo "<tr><th colspan='5'><center>Cardiology</cnter></th></tr>";
							}
							
							$type_id_str=" SELECT distinct `type_id` FROM `testmaster` WHERE `category_id`='$cat[category_id]' and `type_id`!=0 ";
							
							if($category_id>0)
							{
								$type_id_str.=" AND `category_id`='$category_id'";
							}
							if($type_id>0)
							{
								$type_id_str.=" AND `type_id`='$type_id'";
							}
							if($sampleid>0)
							{
								$type_id_str.=" AND `testid` IN(SELECT `TestId` FROM `TestSample` WHERE `SampleId`='$sampleid')";
							}
							if($vac_id>0)
							{
								$type_id_str.=" AND `testid` IN(SELECT `testid` FROM `test_vaccu` WHERE `vac_id`='$vac_id')";
							}
							if($equipment>0)
							{
								$type_id_str.=" AND `equipment`='$equipment'";
							}
							$type_id_str.=" ORDER BY `type_id` ";
							
							$type_id_qry=mysqli_query($link, $type_id_str);
							while($type_info=mysqli_fetch_array($type_id_qry))
							{
								$type_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `test_department` WHERE `id`='$type_info[type_id]' "));
								
								echo "<tr><th colspan='5'>$type_name[name]</th></tr>";
								
								$tst_str=" SELECT `testid`,`testname`,`rate` FROM `testmaster` WHERE `category_id`='$cat[category_id]' AND `type_id`='$type_info[type_id]' AND `testname`!='' ";
								if($sampleid>0)
								{
									$tst_str.=" AND `testid` IN(SELECT `TestId` FROM `TestSample` WHERE `SampleId`='$sampleid')";
								}
								if($vac_id>0)
								{
									$tst_str.=" AND `testid` IN(SELECT `testid` FROM `test_vaccu` WHERE `vac_id`='$vac_id')";
								}
								if($equipment>0)
								{
									$tst_str.=" AND `equipment`='$equipment'";
								}
								$tst_str.=" ORDER BY `testname` ";
								
								$tst_qry=mysqli_query($link, $tst_str);
								while($tst=mysqli_fetch_array($tst_qry))
								{
									$normal_rate=$tst["rate"];
									$special_rate="";
									$private_rate="";
									
									$centre_rate_special=mysqli_fetch_array(mysqli_query($link, "SELECT `rate` FROM `testmaster_rate` WHERE `testid`='$tst[testid]'"));
									if($centre_rate_special)
									{
										$special_rate=$centre_rate_special["rate"];
									}
								?>
									<tr>
										<td><?php echo $n; ?></td>
										<!--<td><?php echo $tst["testid"]; ?></td>-->
										<td>
											<?php echo $tst["testname"]; ?>
										<?php
											// Check Add-on
											$j=1;
											$test_addon_qry=mysqli_query($link, "SELECT a.`testname` FROM `testmaster` a, `testmaster_sub` b WHERE a.`testid`=b.`sub_testid` AND b.`testid`='$tst[testid]' ORDER BY `testname` ASC");
											while($test_addon_info=mysqli_fetch_array($test_addon_qry))
											{
												echo "<div style='margin-left: 10px;font-size: 10px;'>".$j.". ".$test_addon_info["testname"]."</div>";
												
												$j++;
											}
										?>
										</td>
										<td style="text-align:right;"><?php echo $normal_rate; ?></td>
									</tr>
								<?php
									$n++;
								}
							}
						}
					?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>

