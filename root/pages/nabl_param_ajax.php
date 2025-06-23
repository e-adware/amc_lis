<?php
include("../../includes/connection.php");

$type=$_POST["type"];

if($type==1)
{
	$stype=$_POST["s_type"];
	$word=$_POST["word"];
	
	if($stype==1)
	{
		?>
			<table class="table borderless bordert-top-bottom row_height">
			<tr><th>#</th><th>Test Name</th></tr>
		<?php
			$i=1;
			$qry=mysqli_query($link,"select * from testmaster where testname like '$word%' order by testname");
			while($q=mysqli_fetch_array($qry))
			{
				echo "<tr id='td$i' onclick='load_details($stype,$q[testid])' style='cursor:pointer;'>";
				echo "<td>$i</td>";
				echo "<td>$q[testname] <input type='hidden' id='id_$i' value='$q[testid]'/></td>";
				echo "</tr>";
				$i++;
			}
			
	}
	else if($stype==2)
	{
		?>
			<table class="table borderless bordert-top-bottom row_height">
			<tr><th>#</th><th>Parameter Name</th></tr>
		<?php
			$i=1;
			$qry=mysqli_query($link,"select * from Parameter_old where Name like '$word%' order by Name");
			while($q=mysqli_fetch_array($qry))
			{
				echo "<tr id='td$i' onclick='load_details($stype,$q[ID])' style='cursor:pointer;'>";
				echo "<td>$i</td>";
				echo "<td>$q[Name] <input type='hidden' id='id_$i' value='$q[ID]'/></td>";
				echo "</tr>";
				$i++;
			}
			
	}
}
else if($type==2)
{
	$stype=$_POST["stype"];
	$val=mysqli_real_escape_string($link,$_POST["val"]);
	
	if($stype==1)
	{
		$t_det=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$val'"));
		?>
			<div class="col-md-3">
				<table class="table nobordered table">
					<tr><th>Test ID: <?php echo $t_det["testid"];?></th></tr>
					<tr><th>Test Name: <?php echo $t_det["testname"];?></th></tr>
					<tr>
						<th>
							NABL: 
							<?php
								
							
							?>
						</th>
					</tr>	
				</table>
			</div>	
			<div class="col-md-9">
				<table class="table table-bordered table-striped table-condensed table-hover" width="100%">
					<tr><th colspan="3">Parameter List</th></tr>
					<tr>
						<th>#</th> <th>Name</th><th>NABL</th>
					</tr>
					<?php
					$j=1;
					$qry=mysqli_query($link,"select * from Testparameter where TestId='$val' order by sequence");
					while($q=mysqli_fetch_array($qry))
					{
						$pname=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID='$q[ParamaterId]'"));
						
						$stat=0;
						$nabl_img="<img src='../images/Delete.png' width='20' height='20'/>";
						$nabl=mysqli_num_rows(mysqli_query($link,"select * from nabl_test_param where paramid='$q[ParamaterId]'"));
						if($nabl>0)
						{
							$nabl_img="<img src='../images/right.png' width='20' height='20'/>";
							$stat=1;
						}
						
						echo "<tr onclick='nabl_status($j,$q[ParamaterId])'><td>$j</td><td>$pname[Name]</td><td id='param_status_$j' style='cursor:pointer;'>$nabl_img <input type='hidden' id='p_stat$j' value='$stat'/></td></tr>";	
						$j++;
					}
					
					?>
				</table>
					
			</div>
		<?php
	}
	else
	{
		?>
			<div class="span11">
				<table class="table table-bordered table-striped table-condensed table-hover" width="100%">
					<tr><th colspan="3">Parameter Details</th></tr>
					<tr>
						<th>#</th> <th>Name</th><th>NABL</th>
					</tr>
					<?php
					$j=1;
					$qry=mysqli_query($link,"select * from Parameter_old where ID='$val'");
					while($q=mysqli_fetch_array($qry))
					{
						$stat=0;
						$nabl_img="<img src='../images/Delete.png' width='20' height='20'/>";
						$nabl=mysqli_num_rows(mysqli_query($link,"select * from nabl_test_param where paramid='$q[ID]'"));
						if($nabl>0)
						{
							$nabl_img="<img src='../images/right.png' width='20' height='20'/>";
							$stat=1;
						}
						
						echo "<tr onclick='nabl_status($j,$q[ID])'><td>$j</td><td>$q[Name]</td><td id='param_status_$j' style='cursor:pointer;'>$nabl_img <input type='hidden' id='p_stat$j' value='$stat'/></td></tr>";
						$j++;
					}
					
					?>
				</table>
					
			</div>
		
		
		<?php
	}
	
}
else if($type==3)
{
	$stat=$_POST["stat"];
	$param=$_POST["param"];
	
	if($stat==0)
	{
		mysqli_query($link,"delete from nabl_test_param where paramid='$param'");
		mysqli_query($link,"INSERT INTO `nabl_test_param`(`testid`, `paramid`) VALUES ('0','$param')");
		echo 1;
	}
	else
	{
		mysqli_query($link,"delete from nabl_test_param where paramid='$param'");
		echo 0;
	}
}
else if($type=="nabl_en")
{
	$nabl=$_POST["nabl"];
	$nabl_text=mysqli_real_escape_string($link, $_POST["nabl_text"]);
	
	mysqli_query($link," DELETE FROM `nabl` ");
	
	mysqli_query($link," INSERT INTO `nabl`(`nabl`, `text`) VALUES ('$nabl','$nabl_text') ");
}
?>
