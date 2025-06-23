<?php
include("../../includes/connection.php");

if($_POST["type"]=="load_all_test")
{
	$vac=$_POST['ser_vac'];
	$category_id=$_POST['ser_category_id'];
	$dep=$_POST['ser_dep'];
	$sam=$_POST['ser_samp'];
	
	$limit_no=$_POST['limit_no'];
	$search_data=$_POST['search_data'];
	
	$branch_id=$_POST["branch_id"];
	$equipment=$_POST["equipment"];
	
	$user=$_POST["user"];
	
	$qry=" SELECT * FROM `testmaster` WHERE `testid`>0 ";
	
	if($search_data)
	{
		$qry.=" AND `testname` LIKE '%$search_data%'";
	}
	
	if($vac)
	{
		$qry.=" and testid in(select testid from test_vaccu where vac_id='$vac')";
	}	
	if($category_id)
	{
		$qry.=" and category_id='$category_id'";
	}
	if($dep)
	{
		$qry.=" and type_id='$dep'";
	}
	if($sam)
	{
		$qry.=" and testid in(select TestId from TestSample where SampleId='$sam')";
	}
	
	if($equipment>0)
	{
		$qry.=" and equipment='$equipment'";
	}

	$qry.=" order by `testid` limit $limit_no";

?>
	<table class="table table-bordered data-table">
		<thead style="background: #ddd;">
			<tr>
				<th>ID</th>
				<th>Service Name</th>
				<th>Department</th>
				<th >Price</th>
				<th></th>
				<th colspan="3"></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$tst_qry=mysqli_query($link, $qry);
			while($tst=mysqli_fetch_array($tst_qry))
			{
				$cls="";
				$txt_p="Map Parameter";
				$par=mysqli_num_rows(mysqli_query($link, "select * from Testparameter where TestId='$tst[testid]'"));
				if($par>0)
				{
					$txt_p="Edit Parameter";
					$cls="btn btn-info btn-mini";
				}
				else
				{
					$cls="btn btn-default btn-mini";
				}
				// &#x20b9; 
				$sub_bt="Add-On";
				$sub_cls="btn btn-default btn-mini";
				$chk_sub=mysqli_num_rows(mysqli_query($link,"select * from testmaster_sub where testid='$tst[testid]'"));
				if($chk_sub>0)
				{
					$sub_cls="btn btn-info btn-mini";
				}
				if($tst['category_id']==1)
				{
					$map_btn="";
				}
				else
				{
					$map_btn="disabled='disabled'";
				}
				
				$test_rate=$tst['rate'];
				if($branch_id==1)
				{
					$test_rate=$tst['rate'];
				}
				if($branch_id==2)
				{
					$test_rate=$tst['rate_bc'];
				}
		?>
			<tr class="gradeX" id="test<?php echo $i ?>" class="<?php echo $t['testname']; ?>">
				<td id="test_id<?php echo $i ?>"><?php echo $tst["testid"]; ?></td>
				<td>
					<?php echo $tst["testname"]; ?>
					<!--<input type="text" id="test_name<?php echo $tst['testid']; ?>" value="<?php echo $tst['testname']; ?>" class="span3 test_name" onkeyup="test_name_change_up('<?php echo $tst['testid']; ?>',event,this.value)">-->
				</td>
				<td><?php echo $tst['type_name']; ?></td>
				<td style="">
					<span><?php echo $test_rate; ?></span>
					<input type="text" id="test_rate<?php echo $tst['testid']; ?>" value="<?php echo $test_rate; ?>" class="span1 test_rate" onkeyup="test_rate_change_up('<?php echo $branch_id; ?>','<?php echo $tst['testid']; ?>',event,this.value)">
				</td>
				<td colspan="4" >
					<div class="btn-group">
					<?php
						if($tst['testid']!=3374)
						{
					?>
						<button class="<?php echo $sub_cls; ?>" onclick="sub_test('<?php echo $tst['testid']; ?>')"><i class="icon-plus"></i> Add On</button>
					<?php
						}
				if($tst["category_id"]==1)
				{
					if (strpos(strtolower($tst['testname']),'culture') !== false || strpos(strtolower($tst['testname']),'Culture') !== false || strpos(strtolower($tst['testname']),'CULTURE') !== false)
					{
						if($tst['testid']==3)
						{
							?> <button onclick="map_para('<?php echo $tst['testid']; ?>')" class="<?php echo $cls; ?>" <?php echo $map_btn;?>><i class="icon-list"></i> <?php echo $txt_p; ?></button> <?php
						}
					}
					else{
					?>
							<button onclick="map_para('<?php echo $tst['testid']; ?>')" class="<?php echo $cls; ?>" <?php echo $map_btn;?>><i class="icon-list"></i> <?php echo $txt_p; ?></button>
					<?php
						}
				}
						if($tst['testid']!=3)
						{
					?>
							<button id='upd' class='btn btn-primary btn-mini' onclick="load_test_info('<?php echo $tst['testid']; ?>')"><i class="icon-edit"></i> Update</button>
					<?php
						}
						if($tst['testid']!=3)
						{
					?>
							<button  id='delete' name='delete' class='btn btn-danger btn-mini' onclick="delete_test('<?php echo $tst['testid']; ?>')"><i class="icon-remove"></i> Delete</button>
					<?php
						}
					?>
					</div>
				</td>
			
			</tr>
		<?php } ?>
		</tbody>
	</table>
<?php
}

if($_POST["type"]=="load_departments")
{
	$category_id=mysqli_real_escape_string($link, $_POST['category_id']);
	
	$str="SELECT `id`, `category_id`, `name` FROM `test_department` WHERE `id`>0";
	
	if($category_id>0)
	{
		$str.=" AND `category_id`='$category_id'";
	}
	
	$str.=" ORDER BY `name` ASC";
	
	echo '<option value="0">--Select Department--</option>';
	
	$qry=mysqli_query($link, $str);
	while($data=mysqli_fetch_array($qry))
	{
		echo "<option value='$data[id]'>$data[name]</option>";
	}
	
}
if($_POST["type"]=="test_rate_change")
{
	$val=mysqli_real_escape_string($link, $_POST['val']);
	$id=mysqli_real_escape_string($link, $_POST['id']);
	$branch_id=mysqli_real_escape_string($link, $_POST['branch_id']);
	
	if($branch_id==1)
	{
		mysqli_query($link, " UPDATE `testmaster` SET `rate`='$val' WHERE `testid`='$id' ");
	}
	if($branch_id==2)
	{
		mysqli_query($link, " UPDATE `testmaster` SET `rate_bc`='$val' WHERE `testid`='$id' ");
	}
	
	$val=mysqli_fetch_array(mysqli_query($link, " SELECT `rate` FROM `testmaster` WHERE `testid`='$id' "));
	
	echo $val['rate'];
	
}
if($_POST["type"]=="test_name_change")
{
	$val=mysqli_real_escape_string($link, $_POST['val']);
	$id=mysqli_real_escape_string($link, $_POST['id']);
	
	mysqli_query($link, " UPDATE `testmaster` SET `testname`='$test_name' WHERE `testid`='$id' ");
	
	$val=mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$id' "));
	
	echo $val['testname'];
	
}

?>
