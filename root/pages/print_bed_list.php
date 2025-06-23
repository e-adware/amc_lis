<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$branch_id=$emp_info['branch_id'];
if(!$branch_id){ $branch_id=1; }

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Bed Status</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<script src="../../js/matrix.js"></script>
	<style>
		.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
		{
			padding: 1px 1px 1px 5px !important;
		}
		.table-bordered
		{
			border-radius: 0 !important;
		}
		hr
		{
			border-top: 1px solid #000 !important;
			margin: 10px 0 !important;
		}
		.b_st
		{
			display:inline-block !important;
			padding-left: 5px !important;
			padding-right: 5px !important;
			box-shadow:2px 2px #aaaaaa !important;
			margin-left: 30px !important;
		}
		.wards
		{
			background:linear-gradient(-90deg, #cccccc, #eeeeee) !important;
		}
		@page
		{
			margin:0.2cm !important;
		}
	</style>
</head>

<body onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="">
			<?php
			include('page_header.php');
			?>
		</div>
<?php
	$bq=mysqli_query($link,"SELECT b.* FROM `bed_master` b, `ward_master` c WHERE b.`ward_id`=c.`ward_id` AND c.`branch_id`='$branch_id' AND b.`share_bed`=0 AND c.`ward_id` NOT IN(18)");
	$b_num=mysqli_num_rows($bq);
	$bed_occu=mysqli_num_rows(mysqli_query($link,"SELECT a.* FROM `ipd_pat_bed_details` a, `bed_master` b, `ward_master` c WHERE a.`bed_id`=b.`bed_id` AND b.`ward_id`=c.`ward_id` AND c.`branch_id`='$branch_id' AND b.`share_bed`=0 AND c.`ward_id` NOT IN(18)"));
	$bed_bloc=mysqli_num_rows(mysqli_query($link,"SELECT b.* FROM `bed_master` b, `ward_master` c WHERE b.`ward_id`=c.`ward_id` AND c.`branch_id`='$branch_id' AND b.`share_bed`=0 AND b.`status`='1' AND c.`ward_id` NOT IN(18)"));
	$bed_avail=$b_num-$bed_occu-$bed_bloc;
	$space="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
?>
		<hr>
		<center><h4>IPD Bed Details</h4></center>
		<table class="table table-condensed table-bordered" style="font-size:13px;">
		<tr>
			<th colspan="5" style="text-align:center;">
				<div class="b_st"><b class="icon-ok icon-large" style="color:#169A1C !important;"></b> Available</div>
				<div class="b_st"><b class="icon-remove icon-large" style="color:#E08D09 !important;"></b> Blocked</div>
				<div class="b_st"><b class="icon-ban-circle icon-large" style="color:#CC0E0E !important;"></b> Occupied</div>
				<div class="b_st"><b class="icon-ban-circle icon-large" style="color:#000 !important;"></b> Auto Blocked</div>
			</th>
		</tr>
		<tr>
			<th colspan="5">Total Beds : <?php echo $b_num;?> <?php echo $space."Occupied : ".$bed_occu.$space." Blocked : ".$bed_bloc.$space." Available : ".$bed_avail;?>
				<span style="float:right;margin-right:20px;">Date Time : <?php echo date("d-M-Y")." ".date("h:i A");?></span>
			</th>
		</tr>
		<tr>
			<th>Ward</th><th>Total Beds</th><th>Available</th><th>Occupied</th><th>Blocked</th>
		</tr>
	
	<?php
		//$ward=mysqli_query($link,"select a.* from ward_master a, bed_master b where a.ward_id=b.ward_id and a.branch_id='$branch_id' group by ward_id order by a.name");
		$ward=mysqli_query($link,"SELECT * FROM `ward_master` WHERE `ward_id` IN(SELECT DISTINCT `ward_id` FROM `bed_master`) and branch_id='$branch_id' order by name");
		$ward_num=mysqli_num_rows($ward);
		while($w=mysqli_fetch_array($ward))
		{
			$tot_room=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from room_master where ward_id='$w[ward_id]'"));
			$tot_bed=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$w[ward_id]' and `share_bed`=0"));
			$tot_avail=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$w[ward_id]' and `share_bed`=0 and bed_id not in(select bed_id from ipd_pat_bed_details)"));
			$tot_occ=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_pat_bed_details where bed_id in(select bed_id from bed_master where ward_id='$w[ward_id]')"));
			$tot_cls=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$w[ward_id]' and status='1'"));
			$tot_temp=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_bed_details_temp where bed_id in(select bed_id from bed_master where ward_id='$w[ward_id]')"));
			
			$avail=$tot_avail["tot"]-$tot_temp["tot"]-$tot_cls["tot"];
			
		?>
			<tr class="wards">
				<th><?php echo $w["name"];?></th>
				<th><?php echo $tot_bed["tot"];?></th>
				<th><?php echo $avail;?></th>
				<th><?php echo $tot_occ["tot"];?></th>
				<th><?php echo $tot_cls["tot"];?></th>
			</tr>
			<?php
			$beds="";
			$bb=1;
			$styles="width:80px !important;display:inline-block !important;border:1px solid #cccccc !important;margin-top:5px !important;margin-bottom:5px !important;margin-left:10px !important;padding-left:5px !important;padding-right:5px !important;";
			$bed_qry=mysqli_query($link,"SELECT * FROM `bed_master` WHERE `ward_id`='$w[ward_id]'");
			while($bed_info=mysqli_fetch_array($bed_qry))
			{
				$bed_block=0;
				
				if($bed_info['status']==1)
				{
					$title=$bed_info["reason"];
					$styles.="box-shadow: 1px 1px 4px 1px #E08D09 !important;";
					$icon="<b class='icon-remove icon-large' style='color:#E08D09 !important;'></b>";
				}
				else
				{
					// Share Bed Block if Main Bed Taken => ImShare
					if($bed_info["share_bed"]>0 && $bed_info["main_bed_id"]>0)
					{
						$main_bed_check=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where bed_id='$bed_info[main_bed_id]'"));
						if($main_bed_check)
						{
							$bed_block++;
						}
					}
					
					// Main Bed Block if Share Bed Taken =>ImMain
					$share_bed_qry=mysqli_query($link,"SELECT * FROM `bed_master` WHERE `main_bed_id`='$bed_info[bed_id]' AND `share_bed`=1");
					$share_bed_num=mysqli_num_rows($share_bed_qry);
					$share_bed_occupied_num=0;
					while($share_bed_info=mysqli_fetch_array($share_bed_qry))
					{
						$share_bed_check=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where bed_id='$share_bed_info[bed_id]'"));
						if($share_bed_check)
						{
							$share_bed_occupied_num++;
						}
					}
					if($share_bed_num>0 && $share_bed_occupied_num>0)
					{
						$bed_block++;
					}
					
					if($bed_block>0)
					{
						$styles.="box-shadow: 1px 1px 4px 1px #fff !important;";
						$icon="<b class='icon-ban-circle icon-large' style='color:#000 !important;'></b>";
						$title="";
					}
					else
					{
						$ipd_pat_bed_qry=mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details` WHERE `bed_id`='$bed_info[bed_id]'");
						$nn=mysqli_num_rows($ipd_pat_bed_qry);
						if($nn>0)
						{
							$styles.="box-shadow: 1px 1px 4px 1px #E38482 !important;";
							$icon="<b class='icon-ban-circle icon-large' style='color:#CC0E0E !important;'></b>";
							$ipd_pat_bed=mysqli_fetch_array($ipd_pat_bed_qry);
							$pat=mysqli_fetch_array(mysqli_query($link, " SELECT `name`,`sex`,`dob`,`age`,`age_type` FROM `patient_info` WHERE `patient_id`='$ipd_pat_bed[patient_id]'"));
							if($pat['sex']=="Male")
							{
								$sex="M";
							}
							if($pat['sex']=="Female")
							{
								$sex="F";
							}
							$age=$pat['age']." ".$pat['age_type'];
							$title=$pat['name']." ".$sex."/".$age;
						}
						else
						{
							$ipd_guardina_bed_qry=mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details_guardian` WHERE `bed_id`='$bed_info[bed_id]'");
							$nn=mysqli_num_rows($ipd_guardina_bed_qry);
							if($nn)
							{
								$styles.="box-shadow: 1px 1px 4px 1px #E38482 !important;";
								$icon="<b class='icon-ban-circle icon-large' style='color:#CC0E0E !important;'></b>";
								$ipd_pat_bed=mysqli_fetch_array($ipd_guardina_bed_qry);
								$pat=mysqli_fetch_array(mysqli_query($link, " SELECT `name`,`sex`,`dob`,`age`,`age_type` FROM `patient_info` WHERE `patient_id`='$ipd_pat_bed[patient_id]'"));
								if($pat['sex']=="Male")
								{
									$sex="M";
								}
								if($pat['sex']=="Female")
								{
									$sex="F";
								}
								$age=$pat['age']." ".$pat['age_type'];
								$title="GUARDIAN OF ".$pat['name']." ".$sex."/".$age;
							}
							else
							{
								$styles.="box-shadow: 1px 1px 4px 1px #8ce382 !important;";
								$icon="<b class='icon-ok icon-large' style='color:#169A1C !important;'></b>";
								$title="";
							}
						}
					}
				}
				$beds.="<div style='".$styles."' class='tip-top' title='".$title."'>".$bed_info['bed_no']." <span style='float:right;'>".$icon."</span></div>";
				if($bb==8)
				{
					$beds.="<br/>";
					$bb=1;
				}
				else
				{
					$bb++;
				}
			}
			?>
			<tr>
				<td colspan="5"><?php echo $beds;?></td>
			</tr>
			<tr>
				<td colspan="5" style="background:#dddddd;"></td>
			</tr>
			<?php
		}
		?>
		</table>
		<table class="table table-condensed table-bordered" style="font-size:13px;">
			<tr>
				<th width="50%">Ward<span style="float:right;margin-right:20px;">Date : <?php echo date("d-M-Y");?></span></th>
				<th>Total Admit</th>
				<th>Total Discharge</th>
			</tr>
			<?php
			$t_adm=0;
			$t_dsc=0;
			$date=date("Y-m-d");
			$d_wr=mysqli_query($link,"SELECT DISTINCT a.`ward_id` FROM `ipd_bed_alloc_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`='$date' AND b.`branch_id`='$branch_id'");
			while($w=mysqli_fetch_array($d_wr))
			{
				$ward=mysqli_fetch_array(mysqli_query($link,"select name from ward_master where `ward_id`='$w[ward_id]'"));
				$tot_admit=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_bed_alloc_details where ward_id='$w[ward_id]' and alloc_type='1' and `date`='$date'"));
				$t_adm=$t_adm+$tot_admit['tot'];
				
				$tot_disc=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_bed_alloc_details where ward_id='$w[ward_id]' and alloc_type='0' and `date`='$date'"));
				$t_dsc=$t_dsc+$tot_disc['tot'];
			?>
			<tr>
				<td><?php echo $ward['name'];?></td>
				<td><?php echo $tot_admit['tot'];?></td>
				<td><?php echo $tot_disc['tot'];?></td>
			</tr>
			<?php
			}
			?>
			<tr>
				<th>
					<span style="float:right;margin-right:20px;">
						Total Summary
					</span>
				</th>
				<th><?php echo $t_adm;?></th>
				<th><?php echo $t_dsc;?></th>
			</tr>
		</table>
	</div>
</body>
</html>
<script>
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>

