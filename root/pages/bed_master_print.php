<?php
include('../../includes/connection.php');

$branch_id=mysqli_real_escape_string($link, base64_decode($_GET["bid"]));

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
	<title>Bed List</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
</head>
<body>
	<div class="container-fluid">
		<div class="">
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<div class="text-center">
			<h3>Bed List</h3>
			<div class="noprint ">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</div>
		<table id="b_det" class="table table-condensed table-bordered" style="font-size: 12px;">
	<?php
			$wrd=mysqli_query($link,"SELECT * FROM `ward_master` where branch_id='$branch_id'");
			while($w=mysqli_fetch_array($wrd))
			{
	?>
				<tr>
				<th colspan="5" style="text-align:center;background:linear-gradient(-90deg, #eeeeee, #aaaaaa);"><?php echo $w['name'];?></th>
				</tr>
		<?php
			$i=1;
			$rm=mysqli_query($link,"SELECT * FROM `room_master` WHERE `ward_id`='$w[ward_id]'");
			$num=mysqli_num_rows($rm);
			while($r=mysqli_fetch_array($rm))
			{
				$bed=mysqli_query($link,"SELECT * FROM `bed_master` WHERE `ward_id`='$w[ward_id]' AND `room_id`='$r[room_id]'");
				$no=mysqli_num_rows($bed);
				if($no>0)
				{
		?>
				<tr>
					<th>#</th><th>Room No</th><th>Bed No</th><th>Bed Charge</th><th>Bed Other Charge</th>
				</tr>
		<?php
				$j=1;
				while($b=mysqli_fetch_array($bed))
				{
				?>
				<tr>
					<?php if($no>0){ echo "<td rowspan='".$no."'>".$i."</td><td rowspan='".$no."'>".$r['room_no']."</td>";}?>
					<td><?php echo $b['bed_no'];?></td>
					<td><?php echo $b['charges'];?></td>
					<td>
		<?php
					$bed_other_charge_qry=mysqli_query($link," SELECT a.* FROM `charge_master` a, `bed_other_charge` b WHERE a.`charge_id`=b.`charge_id` AND b.`bed_id`='$b[bed_id]' ");
					$xx=1;
					while($bed_other_charge=mysqli_fetch_array($bed_other_charge_qry))
					{
						echo "<span style='float:left'>$bed_other_charge[charge_name]</span><span style='float:right'>$bed_other_charge[amount]</span><br>";
						$xx++;
					}
		?>
					</td>
				</tr>
		<?php
				$no=0;
				$j++;
				}
				}
			$num=0;
			$i++;
			}
		}
	?>
		</table>
	</div>
</body>
</html>
<style>
hr
{
	margin: 10px 0;
}
</style>
