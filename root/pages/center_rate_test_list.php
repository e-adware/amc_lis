<?php
session_start();
include('../../includes/connection.php');

$c_user=trim($_SESSION['emp_id']);

$centreno=$_GET["centreno"];
$group_id=$_GET["group_id"];

$center_name=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$centreno' "));

?>
<html>
<head>
	<title>Centre Service Rate</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		
		<b>Centre Service Rate : <?php echo $center_name["centrename"]; ?></b>
		<!--<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>-->
		<center>
			<div class="noprint ">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data">
			<table class="table table-condensed table-bordered table-hover">
				<thead class="table_header_fix">
					<tr>
						<th>#</th>
						<th>Service Name</th>
						<th>Centre Rate</th>
						<th>Normal Rate</th>
					</tr>
				</thead>
<?php
		$n=1;
		if($group_id==0 || $group_id==104 || $group_id==150 || $group_id==151)
		{
			$str="SELECT a.`testid`, a.`testname`, a.`category_id`, a.`type_id`, a.`rate` AS `m_rate`, b.`rate` AS `c_rate`, b.`test_code` FROM `testmaster` a, `testmaster_rate` b WHERE a.`testid`=b.`testid` AND b.`centreno`='$centreno' ORDER BY a.`category_id`,a.`type_id`,a.`testname` ";
			
			$category_id=0;
			$qry=mysqli_query($link, $str);
			while($data=mysqli_fetch_array($qry))
			{
				if($category_id!=$data["category_id"])
				{
					$category_id=$data["category_id"];
					
					$category_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `test_category_master` WHERE `category_id`='$category_id'"));
					
					echo "<tr><th conspan='4'>$category_info[name]</th></tr>";
				}
		?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $data["testname"]; ?></td>
					<td><?php echo $data["c_rate"]; ?></td>
					<td><?php echo $data["m_rate"]; ?></td>
				</tr>
		<?php
				$n++;
			}
		}
		if($group_id==0 || $group_id!=104 || $group_id!=150 || $group_id!=151)
		{
			$str="SELECT a.`charge_id`, a.`charge_name`, a.`group_id`, a.`amount` AS `m_rate`, b.`rate` AS `c_rate`, b.`charge_code` FROM `charge_master` a, `service_rate` b WHERE a.`charge_id`=b.`charge_id` AND b.`centreno`='$centreno' ORDER BY a.`group_id`,a.`charge_name` ";
			
			$group_id=0;
			$qry=mysqli_query($link, $str);
			while($data=mysqli_fetch_array($qry))
			{
				if($group_id!=$data["group_id"])
				{
					$group_id=$data["group_id"];
					
					$group_info=mysqli_fetch_array(mysqli_query($link, "SELECT `group_name` FROM `charge_group_master` WHERE `group_id`='$group_id'"));
					
					echo "<tr><th conspan='4'>$group_info[group_name]</th></tr>";
				}
		?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $data["charge_name"]; ?></td>
					<td><?php echo $data["c_rate"]; ?></td>
					<td><?php echo $data["m_rate"]; ?></td>
				</tr>
		<?php
				$n++;
			}
		}
?>
			</table>
		</div>
	</div>
</body>
</html>
<script>
	$(document).ready(function(){
		
	});
</script>

<style>
.txt_small{
	font-size:10px;
}
.table
{
	font-size: 11px;
}
@media print
{
	.noprint{
		display:none;
	 }
}
.table
{
	margin-bottom: 0px;
}
.table-condensed th, .table-condensed td
{
	padding: 0;
}
</style>
