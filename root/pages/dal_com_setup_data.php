<?php
session_start();
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

$branch_id=$_POST['branch_id'];
if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

if($_POST["type"]=="load_centres")
{
	echo "<option value='0'>All Centre</option>";
	
	$qry=mysqli_query($link, "SELECT `centreno`,`centrename` FROM `centremaster` WHERE `branch_id`='$branch_id' ORDER BY `centrename` ASC");
	while($data=mysqli_fetch_array($qry))
	{
		if($data["centreno"]=="C100"){ $sel="selected"; }else{ $sel=""; }
		echo "<option value='$data[centreno]' $sel>$data[centrename]</option>";
	}
}

if($_POST["type"]=="ref_doc_load")
{
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	//echo "<option value='0'>All Doctors</option>";
	
	$ref_doc_qry=mysqli_query($link, " SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` WHERE `ref_name`!='' AND `branch_id`='$branch_id' ORDER BY `ref_name` ");
	while($ref_doc=mysqli_fetch_array($ref_doc_qry))
	{
		echo "<option value='$ref_doc[refbydoctorid]'>$ref_doc[ref_name]</option>";
	}
}

if($_POST["type"]=="ref_doc_load_copy")
{
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	echo "<option value='0'>Select</option>";
	
	$ref_doc_qry=mysqli_query($link, " SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` WHERE `ref_name`!='' AND `branch_id`='$branch_id' ORDER BY `ref_name` ");
	while($ref_doc=mysqli_fetch_array($ref_doc_qry))
	{
		echo "<option value='$ref_doc[refbydoctorid]'>$ref_doc[ref_name]</option>";
	}
}

if($_POST["type"]=="ref_doc_load_paste")
{
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$centreno_copy=mysqli_real_escape_string($link, $_POST["centreno_copy"]);
	$centreno_paste=mysqli_real_escape_string($link, $_POST["centreno_paste"]);
	$refbydoctorid=mysqli_real_escape_string($link, $_POST["refbydoctorid"]);
	
	$str=" SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` WHERE `ref_name`!='' AND `branch_id`='$branch_id' ";
	
	if($centreno_copy==$centreno_paste)
	{
		$str.=" AND `refbydoctorid`!='$refbydoctorid' ";
	}
	
	$str.=" ORDER BY `ref_name` ";
	
	$ref_doc_qry=mysqli_query($link, $str);
	while($ref_doc=mysqli_fetch_array($ref_doc_qry))
	{
		$chech_data=mysqli_fetch_array(mysqli_query($link, " SELECT `com_per` FROM `dal_com_setup` WHERE `refbydoctorid`='$ref_doc[refbydoctorid]' "));
		if($chech_data){ $sel=""; }else{ $sel="selected"; }
		
		echo "<option value='$ref_doc[refbydoctorid]' $sel>$ref_doc[ref_name]</option>";
	}
}

if($_POST["type"]=="load_dept")
{
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	
	echo "<option value='0'>All Department</option>";
	
	$qry = mysqli_query($link," SELECT `id`, `name` FROM `test_department` WHERE `category_id`='$category_id' ORDER BY `name` ASC ");
	while($data=mysqli_fetch_array($qry))
	{
		echo "<option value='$data[id]'>$data[name]</option>";
	}
}

if($_POST["type"]=="load_test")
{
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	
	//echo "<option value='0'>All Test</option>";
	
	$str = " SELECT `testid`,`testname` FROM `testmaster` WHERE `testname`!='' AND `category_id`>0 AND `type_id`>0 AND `testid`>0";
	
	if($category_id>0)
	{
		$str.=" AND `category_id`='$category_id'";
	}
	
	if($type_id>0)
	{
		$str.=" AND `type_id`='$type_id'";
	}
	
	$qry = mysqli_query($link, $str);
	while($data=mysqli_fetch_array($qry))
	{
		echo "<option value='$data[testid]'>$data[testname]</option>";
	}
}

if($_POST["type"]=="save")
{
	//~ print_r($_POST);
	//~ exit();
	
	$sel_centreno=mysqli_real_escape_string($link, $_POST["centreno"]);
	$ref_docs=mysqli_real_escape_string($link, $_POST["ref_docs"]);
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testids=mysqli_real_escape_string($link, $_POST["testids"]);
	$com_per=mysqli_real_escape_string($link, $_POST["com_per"]);
	$com_amount=mysqli_real_escape_string($link, $_POST["com_amount"]);
	
	//~ if($com_per==0)
	//~ {
		//~ echo "Enter commission percentage";
		//~ exit();
	//~ }
	if($com_per>100)
	{
		echo "Invalid commission percentage";
		exit();
	}
	
	if($sel_centreno=="0")
	{
		$del_str="DELETE FROM `dal_com_setup` WHERE `centreno`!='0' ";
	}
	else
	{
		$del_str="DELETE FROM `dal_com_setup` WHERE `centreno`='$sel_centreno' ";
	}
	
	if($category_id>0)
	{
		$del_str.=" AND `category_id`='$category_id' ";
	}
	else
	{
		$del_str.=" AND `category_id`='0' ";
	}
	
	if($type_id>0)
	{
		$del_str.=" AND `type_id`='$type_id' ";
	}
	else
	{
		$del_str.=" AND `type_id`='0' ";
	}
	
	if($ref_docs!="0")
	{
		$del_str.=" AND `refbydoctorid` IN($ref_docs)";
	}
	if($testids!="0")
	{
		$del_str.=" AND `testid` IN($testids)";
	}
	else
	{
		$del_str.=" AND `testid` IN(0)";
	}
	
	mysqli_query($link, $del_str);
	
	$qry_str="SELECT * FROM `refbydoctor_master` WHERE `branch_id`='$branch_id'";
	
	if($ref_docs!="0")
	{
		$qry_str.=" AND `refbydoctorid` IN($ref_docs)";
	}
	
	$qry_str.=" ORDER BY `refbydoctorid` ASC";
	
	$qry = mysqli_query($link, $qry_str);
	
	while($ref_doc=mysqli_fetch_array($qry))
	{
		$centre_str="SELECT `centreno` FROM `centremaster` WHERE `branch_id`='$branch_id'";
		
		if($sel_centreno!="0")
		{
			$centre_str.=" AND `centreno`='$sel_centreno'";
		}
		$centre_qry=mysqli_query($link, $centre_str);
		
		while($centre_info=mysqli_fetch_array($centre_qry))
		{
			$centreno=$centre_info["centreno"];
			
			if($testids=="0")
			{
				$testid=0;
				
				//if($category_id>0 || $type_id>0)
				//{
					mysqli_query($link, "INSERT INTO `dal_com_setup`(`centreno`, `refbydoctorid`, `category_id`, `type_id`, `testid`, `com_per`, `com_amount`) VALUES ('$centreno','$ref_doc[refbydoctorid]','$category_id','$type_id','$testid','$com_per','$com_amount')");
				//}
			}
			else
			{
				$testidz=explode(",",$testids);
				foreach($testidz as $testid)
				{
					if($testid)
					{
						$test_info=mysqli_fetch_array(mysqli_query($link, " SELECT `category_id`,`type_id` FROM `testmaster` WHERE `testid`='$testid' "));
						$category_id=$test_info["category_id"];
						$type_id=$test_info["type_id"];
						
						if($category_id>0 && $type_id>0)
						{
							mysqli_query($link, "INSERT INTO `dal_com_setup`(`centreno`, `refbydoctorid`, `category_id`, `type_id`, `testid`, `com_per`, `com_amount`) VALUES ('$centreno','$ref_doc[refbydoctorid]','$category_id','$type_id','$testid','$com_per','$com_amount')");
						}
					}
				}
			}
		}
	}
	
	echo "Saved";
}

if($_POST["type"]=="as_a_whole")
{
	//print_r($_POST);
	
	$centreno=mysqli_real_escape_string($link, $_POST["centreno"]);
	$ref_docs=mysqli_real_escape_string($link, $_POST["ref_docs"]);
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testids=mysqli_real_escape_string($link, $_POST["testids"]);
	
	$dis_centre_str="SELECT DISTINCT `centreno`  FROM `dal_com_setup` ";
	
	if($centreno!="0")
	{
		$dis_centre_str.=" WHERE `centreno`='$centreno'";
	}
	else
	{
		$dis_centre_str.=" WHERE (`centreno`='0' OR `centreno` IN(SELECT `centreno` FROM `centremaster` WHERE `branch_id`='$branch_id'))";
	}
	if($ref_docs!="0")
	{
		$dis_centre_str.=" AND `refbydoctorid` IN($ref_docs)";
	}
	if($category_id>0)
	{
		$dis_centre_str.=" AND `category_id`='$category_id'";
	}
	if($type_id>0)
	{
		$dis_centre_str.=" AND `type_id`='$type_id'";
	}
	if($testids!="0")
	{
		$del_str.=" AND `testid` IN($testids)";
	}
	$dis_centre_str;
?>
	<button type="button" class="btn btn-info btn-mini text-right" id="print_div" onclick="print_page('<?php echo $_POST["type"];?>','<?php echo $centreno;?>','<?php echo $refbydoctorid;?>','<?php echo $category_id;?>','<?php echo $type_id;?>','<?php echo $testids;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	<table class="table table-condensed table-hover">
		<thead class="table_header_fix">
			<tr>
				<th rowspan="2">#</th>
				<th rowspan="2">Doctor Name</th>
				<th colspan="2" style="width: 15%;"><center>Commission</center></th>
				<th rowspan="2"></th>
			</tr>
			<tr>
				<th>Percentage</th>
				<th>Amount</th>
			</tr>
		</thead>
<?php
		$same_centre="";
		$n=1;
		$dis_centre_qry=mysqli_query($link, $dis_centre_str);
		while($dis_centre=mysqli_fetch_array($dis_centre_qry))
		{
			if($dis_centre["centreno"]!=$same_centre)
			{
				$same_centre=$dis_centre["centreno"];
				
				if($same_centre=="0")
				{
					$centre_name="All Centre";
				}
				else
				{
					$centre_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$same_centre' "));
					$centre_name=$centre_info["centrename"];
				}
				
				echo "<tr><th colspan='3'>$centre_name</th></tr>";
			}
			
			$str="SELECT a.*,b.`ref_name`  FROM `dal_com_setup` a, `refbydoctor_master` b WHERE a.`refbydoctorid`=b.`refbydoctorid`";
			
			if($dis_centre["centreno"]!="0")
			{
				$str.=" AND a.`centreno`='$dis_centre[centreno]'";
			}
			
			if($ref_docs!="0")
			{
				$str.=" AND a.`refbydoctorid` IN($ref_docs)";
			}
			
			$str.=" AND a.`category_id`=0";
			
			$str.=" AND a.`type_id`=0";
			
			$str.=" AND a.`testid`=0";
			
			$str.=" GROUP BY `refbydoctorid` ORDER BY b.`ref_name` ASC";
			
			//echo $str;
			
			$qry=mysqli_query($link, $str);
			while($data=mysqli_fetch_array($qry))
			{
	?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $data["ref_name"]; ?></td>
					<td>
						<?php echo $data["com_per"]; ?> %
					</td>
					<td>
						<?php echo $data["com_amount"]; ?>
					</td>
					<td>
						<button class="btn btn-delete btn-mini del_btn" style="float:right;" onclick="delete_com('<?php echo $_POST["type"];?>','<?php echo $data["refbydoctorid"] ?>','<?php echo $data["category_id"] ?>','<?php echo $data["type_id"] ?>','<?php echo $data["testid"] ?>')"><i class="icon-remove"></i></button>
					</td>
				</tr>
	<?php
				$n++;
			}
		}
?>
	</table>
<?php
}

if($_POST["type"]=="category_wise")
{
	//print_r($_POST);
	
	$centreno=mysqli_real_escape_string($link, $_POST["centreno"]);
	$ref_docs=mysqli_real_escape_string($link, $_POST["ref_docs"]);
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testids=mysqli_real_escape_string($link, $_POST["testids"]);
	
	$dis_centre_str="SELECT DISTINCT `centreno`  FROM `dal_com_setup` ";
	
	if($centreno!="0")
	{
		$dis_centre_str.=" WHERE `centreno`='$centreno'";
	}
	else
	{
		$dis_centre_str.=" WHERE (`centreno`='0' OR `centreno` IN(SELECT `centreno` FROM `centremaster` WHERE `branch_id`='$branch_id'))";
	}
	if($ref_docs!="0")
	{
		$dis_centre_str.=" AND `refbydoctorid` IN($ref_docs)";
	}
	if($category_id>0)
	{
		$dis_centre_str.=" AND `category_id`='$category_id'";
	}
	if($type_id>0)
	{
		$dis_centre_str.=" AND `type_id`='$type_id'";
	}
	if($testids!="0")
	{
		$del_str.=" AND `testid` IN($testids)";
	}
	//echo $dis_centre_str;
	
?>
	<button type="button" class="btn btn-info btn-mini text-right" id="print_div" onclick="print_page('<?php echo $_POST["type"];?>','<?php echo $centreno;?>','<?php echo $refbydoctorid;?>','<?php echo $category_id;?>','<?php echo $type_id;?>','<?php echo $testids;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	<table class="table table-condensed table-bordered table-hover">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Doctor Name</th>
				<th></th>
			</tr>
		</thead>
<?php
		$same_centre="";
		$n=1;
		$dis_centre_qry=mysqli_query($link, $dis_centre_str);
		while($dis_centre=mysqli_fetch_array($dis_centre_qry))
		{
			if($dis_centre["centreno"]!=$same_centre)
			{
				$same_centre=$dis_centre["centreno"];
				
				if($same_centre=="0")
				{
					$centre_name="All Centre";
				}
				else
				{
					$centre_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$same_centre' "));
					$centre_name=$centre_info["centrename"];
				}
				
				echo "<tr><th colspan='3'>$centre_name</th></tr>";
			}
			
			$str="SELECT b.`refbydoctorid`,b.`ref_name`  FROM `dal_com_setup` a, `refbydoctor_master` b WHERE a.`refbydoctorid`=b.`refbydoctorid`";
			
			
			$str.=" AND a.`centreno`='$dis_centre[centreno]'";
			
			if($ref_docs!="0")
			{
				$str.=" AND a.`refbydoctorid` IN($ref_docs)";
			}
			
			if($category_id>0)
			{
				$str.=" AND a.`category_id`='$category_id'";
			}
			
			$str.=" AND a.`category_id`>0";
			
			$str.=" AND a.`type_id`=0";
			
			$str.=" AND a.`testid`=0";
			
			$str.=" GROUP BY `refbydoctorid` ORDER BY b.`ref_name` ASC";
			
			//echo $str;
			
			$qry=mysqli_query($link, $str);
			while($data=mysqli_fetch_array($qry))
			{
?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $data["ref_name"]; ?></td>
					<td>
						<table class="table table-condensed">
					<?php
						$cat_str="SELECT `category_id` FROM `dal_com_setup` WHERE `category_id`>0 AND `type_id`=0 AND `testid`=0";
						
						$cat_str.=" AND `centreno`='$dis_centre[centreno]'";
						
						if($data["refbydoctorid"]>0)
						{
							$cat_str.=" AND `refbydoctorid`='$data[refbydoctorid]'";
						}
						
						if($category_id>0)
						{
							$cat_str.=" AND `category_id`='$category_id'";
						}
						
						$cat_dept_str="SELECT `category_id`, `name` FROM `test_category_master` WHERE `category_id` IN($cat_str)";
						$cat_dept_qry=mysqli_query($link, $cat_dept_str);
						while($cat_dept=mysqli_fetch_array($cat_dept_qry))
						{
							$com_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `dal_com_setup` WHERE `refbydoctorid`='$data[refbydoctorid]' AND `category_id`='$cat_dept[category_id]' AND `type_id`=0 AND `testid`=0 AND `centreno`='$dis_centre[centreno]'"));
							
							//echo "<tr class='dept dept$cat_dept[category_id]' onmouseover=\"tr_focus('dept$cat_dept[category_id]','dept')\"><td>$cat_dept[name]</td><th>$com_info[com_per]%</th></tr>";
					?>
							<tr class="dept dept<?php echo $cat_dept["category_id"]; ?>" onmouseover="tr_focus('<?php echo "dept".$cat_dept["category_id"]; ?>','dept')">
								<th style="width: 60%;"><?php echo $cat_dept["name"]; ?></th>
								<th>
									<?php echo $com_info["com_per"]; ?>%
								</th>
								<th>
									<?php echo $com_info["com_amount"]; ?>
								</th>
								<th>
									<button class="btn btn-delete btn-mini del_btn" style="float:right;" onclick="delete_com('<?php echo $_POST["type"];?>','<?php echo $com_info["refbydoctorid"] ?>','<?php echo $com_info["category_id"] ?>','<?php echo $com_info["type_id"] ?>','<?php echo $com_info["testid"] ?>')"><i class="icon-remove"></i></button>
								</th>
							</tr>
					<?php
						}
					?>
						</table>
					</td>
				</tr>
<?php
				$n++;
			}
		}
?>
	</table>
<?php
}

if($_POST["type"]=="dept_wise")
{
	//print_r($_POST);
	
	$centreno=mysqli_real_escape_string($link, $_POST["centreno"]);
	$ref_docs=mysqli_real_escape_string($link, $_POST["ref_docs"]);
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testids=mysqli_real_escape_string($link, $_POST["testids"]);
	
	$dis_centre_str="SELECT DISTINCT `centreno`  FROM `dal_com_setup` ";
	
	if($centreno!="0")
	{
		$dis_centre_str.=" WHERE `centreno`='$centreno'";
	}
	else
	{
		$dis_centre_str.=" WHERE (`centreno`='0' OR `centreno` IN(SELECT `centreno` FROM `centremaster` WHERE `branch_id`='$branch_id'))";
	}
	if($ref_docs!="0")
	{
		$dis_centre_str.=" AND `refbydoctorid` IN($ref_docs)";
	}
	if($category_id>0)
	{
		$dis_centre_str.=" AND `category_id`='$category_id'";
	}
	if($type_id>0)
	{
		$dis_centre_str.=" AND `type_id`='$type_id'";
	}
	if($testids!="0")
	{
		$del_str.=" AND `testid` IN($testids)";
	}
	//echo $dis_centre_str;
	
?>
	<button type="button" class="btn btn-info btn-mini text-right" id="print_div" onclick="print_page('<?php echo $_POST["type"];?>','<?php echo $centreno;?>','<?php echo $refbydoctorid;?>','<?php echo $category_id;?>','<?php echo $type_id;?>','<?php echo $testids;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	<table class="table table-condensed table-bordered table-hover">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Doctor Name</th>
				<th></th>
			</tr>
		</thead>
<?php
		$same_centre="";
		$n=1;
		$dis_centre_qry=mysqli_query($link, $dis_centre_str);
		while($dis_centre=mysqli_fetch_array($dis_centre_qry))
		{
			if($dis_centre["centreno"]!=$same_centre)
			{
				$same_centre=$dis_centre["centreno"];
				
				if($same_centre=="0")
				{
					$centre_name="All Centre";
				}
				else
				{
					$centre_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$same_centre' "));
					$centre_name=$centre_info["centrename"];
				}
				
				echo "<tr><th colspan='3'>$centre_name</th></tr>";
			}
			
			$str="SELECT b.`refbydoctorid`,b.`ref_name`  FROM `dal_com_setup` a, `refbydoctor_master` b WHERE a.`refbydoctorid`=b.`refbydoctorid`";
			
			
			$str.=" AND a.`centreno`='$dis_centre[centreno]'";
			
			if($ref_docs!="0")
			{
				$str.=" AND a.`refbydoctorid` IN($ref_docs)";
			}
			
			if($category_id>0)
			{
				$str.=" AND a.`category_id`='$category_id'";
			}
			
			if($type_id>0)
			{
				$str.=" AND a.`type_id`='$type_id'";
			}
			
			$str.=" AND a.`category_id`>0";
			
			$str.=" AND a.`type_id`>0";
			
			$str.=" AND a.`testid`=0";
			
			$str.=" GROUP BY `refbydoctorid` ORDER BY b.`ref_name` ASC";
			
			//echo $str;
			
			$qry=mysqli_query($link, $str);
			while($data=mysqli_fetch_array($qry))
			{
	?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $data["ref_name"]; ?></td>
					<td>
						<table class="table table-condensed">
					<?php
						$cat_str="SELECT `type_id` FROM `dal_com_setup` WHERE `category_id`>0 AND `type_id`>0 AND `testid`=0";
						
						$cat_str.=" AND `centreno`='$dis_centre[centreno]'";
						
						if($data["refbydoctorid"]>0)
						{
							$cat_str.=" AND `refbydoctorid`='$data[refbydoctorid]'";
						}
						
						if($category_id>0)
						{
							$cat_str.=" AND `category_id`='$category_id'";
						}
						
						if($type_id>0)
						{
							$cat_str.=" AND `type_id`='$type_id'";
						}
						
						$cat_dept_str="SELECT `id`, `name` FROM `test_department` WHERE `id` IN($cat_str)";
						$cat_dept_qry=mysqli_query($link, $cat_dept_str);
						while($cat_dept=mysqli_fetch_array($cat_dept_qry))
						{
							$com_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `dal_com_setup` WHERE `refbydoctorid`='$data[refbydoctorid]' AND `category_id`>0 AND `type_id`='$cat_dept[id]' AND `testid`=0 AND `centreno`='$dis_centre[centreno]' "));
							
							//echo "<tr class='dept dept$cat_dept[id]' onmouseover=\"tr_focus('dept$cat_dept[id]','dept')\"><td>$cat_dept[name]</td><th>$com_info[com_per]%</th></tr>";
					?>
							<tr class="dept dept<?php echo $cat_dept["id"]; ?>" onmouseover="tr_focus('<?php echo "dept".$cat_dept["id"]; ?>','dept')">
								<th style="width: 60%;"><?php echo $cat_dept["name"]; ?></th>
								<th>
									<?php echo $com_info["com_per"]; ?>%
								</th>
								<th>
									<?php echo $com_info["com_amount"]; ?>
								</th>
								<th>
									<button class="btn btn-delete btn-mini del_btn" style="float:right;" onclick="delete_com('<?php echo $_POST["type"];?>','<?php echo $com_info["refbydoctorid"] ?>','<?php echo $com_info["category_id"] ?>','<?php echo $com_info["type_id"] ?>','<?php echo $com_info["testid"] ?>')"><i class="icon-remove"></i></button>
								</th>
							</tr>
					<?php
						}
					?>
						</table>
					</td>
				</tr>
<?php
				$n++;
			}
		}
?>
	</table>
<?php
}

if($_POST["type"]=="test_wise")
{
	//print_r($_POST);
	
	$centreno=mysqli_real_escape_string($link, $_POST["centreno"]);
	$ref_docs=mysqli_real_escape_string($link, $_POST["ref_docs"]);
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testids=mysqli_real_escape_string($link, $_POST["testids"]);
	
	$dis_centre_str="SELECT DISTINCT `centreno`  FROM `dal_com_setup` ";
	
	if($centreno!="0")
	{
		$dis_centre_str.=" WHERE `centreno`='$centreno'";
	}
	else
	{
		$dis_centre_str.=" WHERE (`centreno`='0' OR `centreno` IN(SELECT `centreno` FROM `centremaster` WHERE `branch_id`='$branch_id'))";
	}
	if($ref_docs!="0")
	{
		$dis_centre_str.=" AND `refbydoctorid` IN($ref_docs)";
	}
	if($category_id>0)
	{
		$dis_centre_str.=" AND `category_id`='$category_id'";
	}
	if($type_id>0)
	{
		$dis_centre_str.=" AND `type_id`='$type_id'";
	}
	if($testids!="0")
	{
		$del_str.=" AND `testid` IN($testids)";
	}
	//echo $dis_centre_str;
	
?>
	<button type="button" class="btn btn-info btn-mini text-right" id="print_div" onclick="print_page('<?php echo $_POST["type"];?>','<?php echo $centreno;?>','<?php echo $refbydoctorid;?>','<?php echo $category_id;?>','<?php echo $type_id;?>','<?php echo $testids;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	<table class="table table-condensed table-bordered table-hover">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Doctor Name</th>
				<th></th>
			</tr>
		</thead>
<?php
		$same_centre="";
		$n=1;
		$dis_centre_qry=mysqli_query($link, $dis_centre_str);
		while($dis_centre=mysqli_fetch_array($dis_centre_qry))
		{
			if($dis_centre["centreno"]!=$same_centre)
			{
				$same_centre=$dis_centre["centreno"];
				
				if($same_centre=="0")
				{
					$centre_name="All Centre";
				}
				else
				{
					$centre_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$same_centre' "));
					$centre_name=$centre_info["centrename"];
				}
				
				echo "<tr><th colspan='3'>$centre_name</th></tr>";
			}
			
			$str="SELECT b.`refbydoctorid`,b.`ref_name`  FROM `dal_com_setup` a, `refbydoctor_master` b WHERE a.`refbydoctorid`=b.`refbydoctorid`";
			
			$str.=" AND a.`centreno`='$dis_centre[centreno]'";
			
			if($ref_docs!="0")
			{
				$str.=" AND a.`refbydoctorid` IN($ref_docs)";
			}
			
			if($category_id>0)
			{
				$str.=" AND a.`category_id`='$category_id'";
			}
			
			if($type_id>0)
			{
				$str.=" AND a.`type_id`='$type_id'";
			}
			
			$str.=" AND a.`category_id`>0";
			
			$str.=" AND a.`type_id`>0";
			
			$str.=" AND a.`testid`>0";
			
			$str.=" GROUP BY `refbydoctorid` ORDER BY b.`ref_name` ASC";
			
			//echo $str;
			
			$qry=mysqli_query($link, $str);
			while($data=mysqli_fetch_array($qry))
			{
	?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $data["ref_name"]; ?></td>
					<td>
						<table class="table table-condensed">
					<?php
						$cat_str="SELECT `testid` FROM `dal_com_setup` WHERE `category_id`>0 AND `type_id`>0 AND `testid`>0";
						
						$cat_str.=" AND `centreno`='$dis_centre[centreno]'";
						
						if($data["refbydoctorid"]>0)
						{
							$cat_str.=" AND `refbydoctorid`='$data[refbydoctorid]'";
						}
						
						if($category_id>0)
						{
							$cat_str.=" AND `category_id`='$category_id'";
						}
						
						if($type_id>0)
						{
							$cat_str.=" AND `type_id`='$type_id'";
						}
						
						if($testids!="0")
						{
							$cat_str.=" AND `testid` IN($testids)";
						}
						
						$cat_dept_str="SELECT `testid`,`testname` FROM `testmaster` WHERE `testname`!='' AND `testid` IN($cat_str)";
						$cat_dept_qry=mysqli_query($link, $cat_dept_str);
						while($cat_dept=mysqli_fetch_array($cat_dept_qry))
						{
							$com_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `dal_com_setup` WHERE `refbydoctorid`='$data[refbydoctorid]' AND `category_id`>0 AND `type_id`>0 AND `testid`='$cat_dept[testid]' AND `centreno`='$dis_centre[centreno]' "));
							
							//echo "<tr class='dept dept$cat_dept[testid]' onmouseover=\"tr_focus('dept$cat_dept[testid]','dept')\"><td>$cat_dept[testname]</td><th>$com_info[com_per]%</th></tr>";
					?>
							<tr class="dept dept<?php echo $cat_dept["testid"]; ?>" onmouseover="tr_focus('<?php echo "dept".$cat_dept["testid"]; ?>','dept')">
								<th style="width: 60%;"><?php echo $cat_dept["testname"]; ?></th>
								<th>
									<?php echo $com_info["com_per"]; ?>%
								</th>
								<th>
									<?php echo $com_info["com_amount"]; ?>
								</th>
								<th>
									<button class="btn btn-delete btn-mini del_btn" style="float:right;" onclick="delete_com('<?php echo $_POST["type"];?>','<?php echo $com_info["refbydoctorid"] ?>','<?php echo $com_info["category_id"] ?>','<?php echo $com_info["type_id"] ?>','<?php echo $com_info["testid"] ?>')"><i class="icon-remove"></i></button>
								</th>
							</tr>
					<?php
						}
					?>
						</table>
					</td>
				</tr>
<?php
				$n++;
			}
		}
?>
	</table>
<?php
}


if($_POST["type"]=="doctor_wise")
{
	//print_r($_POST);
	
	$centreno=mysqli_real_escape_string($link, $_POST["centreno"]);
	$ref_docs=mysqli_real_escape_string($link, $_POST["ref_docs"]);
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testids=mysqli_real_escape_string($link, $_POST["testids"]);
	
	$dis_centre_str="SELECT DISTINCT `centreno`  FROM `dal_com_setup` ";
	
	if($centreno!="0")
	{
		$dis_centre_str.=" WHERE `centreno`='$centreno'";
	}
	else
	{
		$dis_centre_str.=" WHERE (`centreno`='0' OR `centreno` IN(SELECT `centreno` FROM `centremaster` WHERE `branch_id`='$branch_id'))";
	}
	if($ref_docs!="0")
	{
		$dis_centre_str.=" AND `refbydoctorid` IN($ref_docs)";
	}
	if($category_id>0)
	{
		$dis_centre_str.=" AND `category_id`='$category_id'";
	}
	if($type_id>0)
	{
		$dis_centre_str.=" AND `type_id`='$type_id'";
	}
	if($testids!="0")
	{
		$del_str.=" AND `testid` IN($testids)";
	}
	//echo $dis_centre_str;
?>
	<button type="button" class="btn btn-info btn-mini text-right" id="print_div" onclick="print_page('<?php echo $_POST["type"];?>','<?php echo $centreno;?>','<?php echo $refbydoctorid;?>','<?php echo $category_id;?>','<?php echo $type_id;?>','<?php echo $testids;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	<table class="table table-condensed table-hover">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Doctor Name</th>
				<th>As A Whole</th>
				<th>Category Wise</th>
				<th>Department Wise</th>
				<th>Test Wise</th>
			</tr>
		</thead>
<?php
		$same_centre="";
		$n=1;
		$dis_centre_qry=mysqli_query($link, $dis_centre_str);
		while($dis_centre=mysqli_fetch_array($dis_centre_qry))
		{
			if($dis_centre["centreno"]!=$same_centre)
			{
				$same_centre=$dis_centre["centreno"];
				
				if($same_centre=="0")
				{
					//~ $centre_name="All Centre";
					$centre_name="";
				}
				else
				{
					$centre_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$same_centre' "));
					$centre_name=$centre_info["centrename"];
				}
				
				echo "<tr><th colspan='3'>$centre_name</th></tr>";
			}
			
			$str="SELECT a.*,b.`ref_name`  FROM `dal_com_setup` a, `refbydoctor_master` b WHERE a.`refbydoctorid`=b.`refbydoctorid`";
			
			$str.=" AND a.`centreno`='$dis_centre[centreno]'";
			
			if($ref_docs!="0")
			{
				$str.=" AND a.`refbydoctorid` IN($ref_docs)";
			}
			
			$str.=" GROUP BY `refbydoctorid` ORDER BY b.`ref_name` ASC";
			
			//echo $str;
			
			$qry=mysqli_query($link, $str);
			while($data=mysqli_fetch_array($qry))
			{
	?>
				<tr class="rdoc <?php echo "rdoc".$data["refbydoctorid"]; ?>" onmouseover="tr_focus('<?php echo "rdoc".$data["refbydoctorid"]; ?>','rdoc')">
					<td><?php echo $n; ?></td>
					<td><?php echo $data["ref_name"]; ?></td>
				<?php
					$com_info=mysqli_fetch_array(mysqli_query($link, " SELECT `com_per`,`com_amount` FROM `dal_com_setup` WHERE `refbydoctorid`='$data[refbydoctorid]' AND `category_id`=0 AND `type_id`=0 AND `testid`=0  AND `centreno`='$dis_centre[centreno]'"));
					
					if($com_info)
					{
						echo "<th>".$com_info["com_per"]."% &nbsp;&nbsp;".$com_info["com_amount"]."</th>";
					}
					else
					{
						echo "<td></td>";
					}
				?>
					<td>
						<table class="table table-condensed">
				<?php
					$cat_str="SELECT `category_id` FROM `dal_com_setup` WHERE `category_id`>0 AND `type_id`=0 AND `testid`=0";
					
					$cat_str.=" AND `centreno`='$dis_centre[centreno]'";
					
					if($data["refbydoctorid"]>0)
					{
						$cat_str.=" AND `refbydoctorid`='$data[refbydoctorid]'";
					}
					
					if($category_id>0)
					{
						$cat_str.=" AND `category_id`='$category_id'";
					}
					$cat_dept_str="SELECT `category_id`, `name` FROM `test_category_master` WHERE `category_id` IN($cat_str)";
					$cat_dept_qry=mysqli_query($link, $cat_dept_str);
					while($cat_dept=mysqli_fetch_array($cat_dept_qry))
					{
						$com_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `dal_com_setup` WHERE `refbydoctorid`='$data[refbydoctorid]' AND `category_id`='$cat_dept[category_id]' AND `type_id`=0 AND `testid`=0 AND `centreno`='$dis_centre[centreno]' "));
				?>
						<tr class="rdoc <?php echo "rdoc".$data["refbydoctorid"]; ?>" onmouseover="tr_focus('<?php echo "rdoc".$data["refbydoctorid"]; ?>','rdoc')">
							<td><?php echo $cat_dept["name"]; ?></td>
							<th>
								<?php echo $com_info["com_per"]; ?>%
							</th>
							<th>
								<?php echo $com_info["com_amount"]; ?>
							</th>
						</tr>
				<?php
					}
				?>
						</table>
					</td>
					<td>
						<table class="table table-condensed">
					<?php
						$cat_str="SELECT `type_id` FROM `dal_com_setup` WHERE `category_id`>0 AND `type_id`>0 AND `testid`=0";
						
						$cat_str.=" AND `centreno`='$dis_centre[centreno]'";
						
						if($data["refbydoctorid"]>0)
						{
							$cat_str.=" AND `refbydoctorid`='$data[refbydoctorid]'";
						}
						
						if($category_id>0)
						{
							$cat_str.=" AND `category_id`='$category_id'";
						}
						
						if($type_id>0)
						{
							$cat_str.=" AND `type_id`='$type_id'";
						}
						
						$cat_dept_str="SELECT `id`, `name` FROM `test_department` WHERE `id` IN($cat_str)";
						$cat_dept_qry=mysqli_query($link, $cat_dept_str);
						while($cat_dept=mysqli_fetch_array($cat_dept_qry))
						{
							$com_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `dal_com_setup` WHERE `refbydoctorid`='$data[refbydoctorid]' AND `category_id`>0 AND `type_id`='$cat_dept[id]' AND `testid`=0 AND `centreno`='$dis_centre[centreno]' "));
							
					?>
							<tr class="rdoc <?php echo "rdoc".$data["refbydoctorid"]; ?>" onmouseover="tr_focus('<?php echo "rdoc".$data["refbydoctorid"]; ?>','rdoc')">
								<td style="width: 60%;"><?php echo $cat_dept["name"]; ?></td>
								<th>
									<?php echo $com_info["com_per"]; ?>%
								</th>
								<th>
									<?php echo $com_info["com_amount"]; ?>
								</th>
							</tr>
					<?php
						}
					?>
						</table>
					</td>
					<td>
						<table class="table table-condensed">
					<?php
						$cat_str="SELECT `testid` FROM `dal_com_setup` WHERE `category_id`>0 AND `type_id`>0 AND `testid`>0";
						
						$cat_str.=" AND `centreno`='$dis_centre[centreno]'";
						
						if($data["refbydoctorid"]>0)
						{
							$cat_str.=" AND `refbydoctorid`='$data[refbydoctorid]'";
						}
						
						if($category_id>0)
						{
							$cat_str.=" AND `category_id`='$category_id'";
						}
						
						if($type_id>0)
						{
							$cat_str.=" AND `type_id`='$type_id'";
						}
						
						if($testids!="0")
						{
							$cat_str.=" AND `testid` IN($testids)";
						}
						
						$cat_dept_str="SELECT `testid`,`testname` FROM `testmaster` WHERE `testname`!='' AND `testid` IN($cat_str)";
						$cat_dept_qry=mysqli_query($link, $cat_dept_str);
						while($cat_dept=mysqli_fetch_array($cat_dept_qry))
						{
							$com_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `dal_com_setup` WHERE `refbydoctorid`='$data[refbydoctorid]' AND `category_id`>0 AND `type_id`>0 AND `testid`='$cat_dept[testid]' AND `centreno`='$dis_centre[centreno]' "));
							
					?>
							<tr class="rdoc <?php echo "rdoc".$data["refbydoctorid"]; ?>" onmouseover="tr_focus('<?php echo "rdoc".$data["refbydoctorid"]; ?>','rdoc')">
								<td style="width: 60%;"><?php echo $cat_dept["testname"]; ?></td>
								<th>
									<?php echo $com_info["com_per"]; ?>%
								</th>
								<th>
									<?php echo $com_info["com_amount"]; ?>
								</th>
							</tr>
					<?php
						}
					?>
						</table>
					</td>
				</tr>
	<?php
				$n++;
			}
		}
?>
	</table>
<?php
}

if($_POST["type"]=="copy_setup")
{
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th style="width: 250px;">Copy From</th>
			<th>Copy To</th>
		</tr>
		<tr>
			<td>
				<b>Centre</b><br>
				<select class="" id="centreno_copy" onchange="ref_doc_load_paste()">
				<?php
					$qry=mysqli_query($link, "SELECT `centreno`,`centrename` FROM `centremaster` WHERE `branch_id`='$branch_id' ORDER BY `centrename` ASC");
					while($data=mysqli_fetch_array($qry))
					{
						if($data["centreno"]=="C100"){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$data[centreno]' $sel>$data[centrename]</option>";
					}
				?>
				</select>
				<b>Doctor</b><br>
				<select class="" id="refbydoctorid_copy" onchange="ref_doc_load_paste()">
					
				</select>
			</td>
			<td>
				<b>Centre</b><br>
				<select class="" id="centreno_paste" onchange="ref_doc_load_paste()">
				<?php
					$qry=mysqli_query($link, "SELECT `centreno`,`centrename` FROM `centremaster` WHERE `branch_id`='$branch_id' ORDER BY `centrename` ASC");
					while($data=mysqli_fetch_array($qry))
					{
						if($data["centreno"]=="C100"){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$data[centreno]' $sel>$data[centrename]</option>";
					}
				?>
				</select>
				<br>
				<b>Doctor</b>
				<select class="" id="refbydoctorid_paste" multiple>
				</select>
			</td>
		</tr>
		<tr id="save_tr_copy" style="display:none;">
			<td colspan="2">
				<center>
					<button class="btn btn-save" onclick="save_copy_setup()"><i class="icon-paste"></i> Save</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="save_copy_setup")
{
	//print_r($_POST);
	$centreno_copy=mysqli_real_escape_string($link, $_POST["centreno_copy"]);
	$refbydoctorid_copy=mysqli_real_escape_string($link, $_POST["refbydoctorid_copy"]);
	$centreno_paste=mysqli_real_escape_string($link, $_POST["centreno_paste"]);
	$refbydoctorid_paste=mysqli_real_escape_string($link, $_POST["refbydoctorid_paste"]);
	
	$qry=mysqli_query($link, "SELECT * FROM `dal_com_setup` WHERE `centreno`='$centreno_copy' AND `refbydoctorid`='$refbydoctorid_copy'");
	$qry_num=mysqli_num_rows($qry);
	if($qry_num>0)
	{
		$refbydoctorids=explode(",",$refbydoctorid_paste);
		foreach($refbydoctorids AS $refbydoctorid)
		{
			if($refbydoctorid)
			{
				mysqli_query($link, "DELETE FROM `dal_com_setup` WHERE `centreno`='$centreno_paste' AND `refbydoctorid`='$refbydoctorid'");
				
				$qry=mysqli_query($link, "SELECT * FROM `dal_com_setup` WHERE `centreno`='$centreno_copy' AND `refbydoctorid`='$refbydoctorid_copy'");
				while($data=mysqli_fetch_array($qry))
				{
					mysqli_query($link, " INSERT INTO `dal_com_setup`(`centreno`, `refbydoctorid`, `category_id`, `type_id`, `testid`, `com_per`, `com_amount`) VALUES ('$centreno_paste','$refbydoctorid','$data[category_id]','$data[type_id]','$data[testid]','$data[com_per]','$data[com_amount]') ");
				}
			}
		}
		echo "Saved";
	}
	else
	{
		$centre_info=mysqli_fetch_array(mysqli_query($link, "SELECT `centrename` FROM `centremaster` WHERE `centreno`='$centreno_copy'"));
		
		$refdoc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$refbydoctorid_copy'"));
		
		echo "No setup found of ".$refdoc_info["ref_name"]." in ".$centre_info["centrename"]." Centre";
	}
}
if($_POST["type"]=="delete_com")
{
	//print_r($_POST);
	
	$refbydoctorid=mysqli_real_escape_string($link, $_POST["refbydoctorid"]);
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testid=mysqli_real_escape_string($link, $_POST["testid"]);
	
	if(mysqli_query($link, "DELETE FROM `dal_com_setup` WHERE `refbydoctorid`='$refbydoctorid' AND `category_id`='$category_id' AND `type_id`='$type_id' AND `testid`='$testid'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Failed, try again later.";
	}
}
?>
