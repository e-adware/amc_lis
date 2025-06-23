<?php
include('../../includes/connection.php');

$branch_id=mysqli_real_escape_string($link, base64_decode($_GET["bid"]));
$group_id=mysqli_real_escape_string($link, base64_decode($_GET["gid"]));

$group_str="";
if($group_id>0)
{
	$group_str=" AND `group_id`='$group_id'";
}

?>
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
	<title>Charge Names List</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
</head>
<body>
	<div class="container">
		<div class="">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<div class="text-center">
			<h3>Charge Names List</h3>
			<div class="noprint">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</div>
		<table class="table table-bordered table-condensed table-report">
			<tr>
				<th>Charge Name</th>
				<th>Amout</th>
			</tr>
			<tr>
				<th rowspan="2">#</th>
				<th rowspan="2">Service Name</th>
				<th colspan="3" style="text-align:center;">Service Rate</th>
			</tr>
			<tr>
				<th style="text-align:center;">Normal</th>
				<th style="text-align:center;">Special</th>
				<th style="text-align:center;">Private</th>
			</tr>
			<?php 
				$n=1;
				$query=mysqli_query($link," SELECT * FROM `charge_group_master` WHERE `group_id` IN(SELECT `group_id` FROM `charge_master` WHERE `branch_id`='$branch_id') $group_str ORDER BY `group_name`  ");
				while($group=mysqli_fetch_array($query))
				{
					if($group['group_id']!='104' && $group['group_id']!='150' && $group['group_id']!='151' && $group['group_id']!='141')
					{
						$item_qry=mysqli_query($link, " SELECT * FROM `charge_master` WHERE `group_id`='$group[group_id]' AND `branch_id`='$branch_id' AND `charge_type`=1 ORDER BY `charge_name` ");
						
						echo "<tr><th colspan='5'><center>$group[group_name]</center></th></tr>";
						while($item=mysqli_fetch_array($item_qry))
						{
							$normal_rate=$item["amount"];
							$special_rate="";
							$private_rate="";
							
							$centre_rate_special=mysqli_fetch_array(mysqli_query($link, "SELECT `rate` FROM `service_rate` WHERE `charge_id`='$item[charge_id]' AND `centreno`='C101'"));
							if($centre_rate_special)
							{
								$special_rate=$centre_rate_special["rate"];
							}
							$centre_rate_private=mysqli_fetch_array(mysqli_query($link, "SELECT `rate` FROM `service_rate` WHERE `charge_id`='$item[charge_id]' AND `centreno`='C104'"));
							if($centre_rate_private)
							{
								$private_rate=$centre_rate_private["rate"];
							}
			?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo $item["charge_name"]; ?></td>
						<td style="text-align:right;"><?php echo $normal_rate; ?></td>
						<td style="text-align:right;"><?php echo $special_rate; ?></td>
						<td style="text-align:right;"><?php echo $private_rate; ?></td>
					</tr>
			<?php
						}
					}
					$n++;
				}
			?>
		</table>
	</div>
</body>
</html>

