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
		//if($data["centreno"]=="C100"){ $sel="selected"; }else{ $sel=""; }
		echo "<option value='$data[centreno]' $sel>$data[centrename]</option>";
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
	
	$centreno=mysqli_real_escape_string($link, $_POST["centreno"]);
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
	
	if($centreno=="0")
	{
		echo "Select Centre";
		exit();
	}
	if($com_per>100)
	{
		echo "Invalid discount percentage";
		exit();
	}
	
	$del_str="DELETE FROM `centre_test_discount_setup` WHERE `centreno`='$centreno' ";
	
	if($category_id>0)
	{
		$del_str.=" AND `category_id`='$category_id' ";
	}
	
	if($type_id>0)
	{
		$del_str.=" AND `type_id`='$type_id' ";
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
	
	if($testids=="0")
	{
		$testid=0;
		
		if($category_id>0 || $type_id>0)
		{
			mysqli_query($link, "INSERT INTO `centre_test_discount_setup`(`centreno`, `category_id`, `type_id`, `testid`, `com_per`, `com_amount`) VALUES ('$centreno','$category_id','$type_id','$testid','$com_per','$com_amount')");
		}
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
					mysqli_query($link, "INSERT INTO `centre_test_discount_setup`(`centreno`, `category_id`, `type_id`, `testid`, `com_per`, `com_amount`) VALUES ('$centreno','$category_id','$type_id','$testid','$com_per','$com_amount')");
				}
			}
		}
	}
	
	echo "Saved";
}

if($_POST["type"]=="category_wise")
{
	//print_r($_POST);
	
	$centreno=mysqli_real_escape_string($link, $_POST["centreno"]);
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testids=mysqli_real_escape_string($link, $_POST["testids"]);
	
	$dis_centre_str="SELECT DISTINCT `centreno`  FROM `centre_test_discount_setup` ";
	
	if($centreno!="0")
	{
		$dis_centre_str.=" WHERE `centreno`='$centreno'";
	}
	else
	{
		$dis_centre_str.=" WHERE (`centreno`='0' OR `centreno` IN(SELECT `centreno` FROM `centremaster` WHERE `branch_id`='$branch_id'))";
	}
	
	//~ if($category_id>0)
	//~ {
		//~ $dis_centre_str.=" AND `category_id`='$category_id'";
	//~ }
	//~ if($type_id>0)
	//~ {
		//~ $dis_centre_str.=" AND `type_id`='$type_id'";
	//~ }
	//~ if($testids!="0")
	//~ {
		//~ $del_str.=" AND `testid` IN($testids)";
	//~ }
	//echo $dis_centre_str;
	
?>
	<button type="button" class="btn btn-info btn-mini text-right" id="print_div" onclick="print_page('<?php echo $_POST["type"];?>','<?php echo $centreno;?>','<?php echo $category_id;?>','<?php echo $type_id;?>','<?php echo $testids;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	<table class="table table-condensed table-bordered table-hover" style="background-color: #fff;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Category Name</th>
				<th>Discount(%)</th>
				<th class="action"></th>
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
				
				echo "<tr><th colspan='4'>$centre_name</th></tr>";
				
				$com_str="SELECT * FROM `centre_test_discount_setup` WHERE `category_id`>0 AND `type_id`=0 AND `testid`=0";
				
				$com_str.=" AND `centreno`='$dis_centre[centreno]'";
				
				$com_str_qry=mysqli_query($link, $com_str);
				while($com_info=mysqli_fetch_array($com_str_qry))
				{
					$category_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `test_category_master` WHERE `category_id`='$com_info[category_id]' "));
			?>
					<tr class="dept dept<?php echo $com_info["category_id"]; ?>" onmouseover="tr_focus('<?php echo "dept".$com_info["category_id"]; ?>','dept')">
						<td><?php echo $n; ?></td>
						<th style="width: 60%;"><?php echo $category_info["name"]; ?></th>
						<th>
							<?php echo $com_info["com_per"]; ?>%
						</th>
						<th class="action">
							<button class="btn btn-delete btn-mini del_btn" style="float:right;" onclick="delete_com('<?php echo $_POST["type"];?>','<?php echo $dis_centre["centreno"] ?>','<?php echo $com_info["category_id"] ?>','<?php echo $com_info["type_id"] ?>','<?php echo $com_info["testid"] ?>')"><i class="icon-remove"></i></button>
						</th>
					</tr>
			<?php
					$n++;
				}
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
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testids=mysqli_real_escape_string($link, $_POST["testids"]);
	
	$dis_centre_str="SELECT DISTINCT `centreno`  FROM `centre_test_discount_setup` ";
	
	if($centreno!="0")
	{
		$dis_centre_str.=" WHERE `centreno`='$centreno'";
	}
	else
	{
		$dis_centre_str.=" WHERE (`centreno`='0' OR `centreno` IN(SELECT `centreno` FROM `centremaster` WHERE `branch_id`='$branch_id'))";
	}
	
	if($category_id>0)
	{
		$dis_centre_str.=" AND `category_id`='$category_id'";
	}
	if($type_id>0)
	{
		$dis_centre_str.=" AND `type_id`='$type_id'";
	}
	//~ if($testids!="0")
	//~ {
		//~ $del_str.=" AND `testid` IN($testids)";
	//~ }
	//echo $dis_centre_str;
	
?>
	<button type="button" class="btn btn-info btn-mini text-right" id="print_div" onclick="print_page('<?php echo $_POST["type"];?>','<?php echo $centreno;?>','<?php echo $category_id;?>','<?php echo $type_id;?>','<?php echo $testids;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	<table class="table table-condensed table-bordered table-hover" style="background-color: #fff;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Department Name</th>
				<th>Discount(%)</th>
				<th class="action"></th>
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
				
				echo "<tr><th colspan='4'>$centre_name</th></tr>";
			}
			
			$com_str="SELECT * FROM `centre_test_discount_setup` WHERE `category_id`>0 AND `type_id`>0 AND `testid`=0";
			
			$com_str.=" AND `centreno`='$dis_centre[centreno]'";
			
			if($category_id>0)
			{
				$com_str.=" AND `category_id`='$category_id'";
			}
			
			if($type_id>0)
			{
				$com_str.=" AND `type_id`='$type_id'";
			}
			
			$com_str_qry=mysqli_query($link, $com_str);
			while($com_info=mysqli_fetch_array($com_str_qry))
			{
				$dept_info=mysqli_fetch_array(mysqli_query($link, " SELECT `id`,`name` FROM `test_department` WHERE `id`='$com_info[type_id]' "));
		?>
				<tr class="dept dept<?php echo $dept_info["id"]; ?>" onmouseover="tr_focus('<?php echo "dept".$dept_info["id"]; ?>','dept')">
					<td><?php echo $n; ?></td>
					<th style="width: 60%;"><?php echo $dept_info["name"]; ?></th>
					<th>
						<?php echo $com_info["com_per"]; ?>%
					</th>
					<th class="action">
						<button class="btn btn-delete btn-mini del_btn" style="float:right;" onclick="delete_com('<?php echo $_POST["type"];?>','<?php echo $dis_centre["centreno"] ?>','<?php echo $com_info["category_id"] ?>','<?php echo $com_info["type_id"] ?>','<?php echo $com_info["testid"] ?>')"><i class="icon-remove"></i></button>
					</th>
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
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testids=mysqli_real_escape_string($link, $_POST["testids"]);
	
	$dis_centre_str="SELECT DISTINCT `centreno`  FROM `centre_test_discount_setup` ";
	
	if($centreno!="0")
	{
		$dis_centre_str.=" WHERE `centreno`='$centreno'";
	}
	else
	{
		$dis_centre_str.=" WHERE (`centreno`='0' OR `centreno` IN(SELECT `centreno` FROM `centremaster` WHERE `branch_id`='$branch_id'))";
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
	<button type="button" class="btn btn-info btn-mini text-right" id="print_div" onclick="print_page('<?php echo $_POST["type"];?>','<?php echo $centreno;?>','<?php echo $category_id;?>','<?php echo $type_id;?>','<?php echo $testids;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	<table class="table table-condensed table-bordered table-hover" style="background-color: #fff;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Test Name</th>
				<th>Discount(%)</th>
				<th class="action"></th>
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
				
				echo "<tr><th colspan='4'>$centre_name</th></tr>";
			}
			
			$com_str="SELECT * FROM `centre_test_discount_setup` WHERE `category_id`>0 AND `type_id`>0 AND `testid`>0";
			
			$com_str.=" AND `centreno`='$dis_centre[centreno]'";
			
			if($category_id>0)
			{
				$com_str.=" AND `category_id`='$category_id'";
			}
			
			if($type_id>0)
			{
				$com_str.=" AND `type_id`='$type_id'";
			}
			
			if($testids!="0")
			{
				$com_str.=" AND `testid` IN($testids)";
			}
			
			$com_str_qry=mysqli_query($link, $com_str);
			while($com_info=mysqli_fetch_array($com_str_qry))
			{
				$test_info=mysqli_fetch_array(mysqli_query($link, " SELECT `testid`,`testname` FROM `testmaster` WHERE `testid`='$com_info[testid]' "));
?>
				<tr class="dept test<?php echo $test_info["testid"]; ?>" onmouseover="tr_focus('<?php echo "test".$test_info["testid"]; ?>','test')">
					<td><?php echo $n; ?></td>
					<th style="width: 60%;"><?php echo $test_info["testname"]; ?></th>
					<th>
						<?php echo $com_info["com_per"]; ?>%
					</th>
					<th class="action">
						<button class="btn btn-delete btn-mini del_btn" style="float:right;" onclick="delete_com('<?php echo $_POST["type"];?>','<?php echo $dis_centre["centreno"] ?>','<?php echo $com_info["category_id"] ?>','<?php echo $com_info["type_id"] ?>','<?php echo $com_info["testid"] ?>')"><i class="icon-remove"></i></button>
					</th>
				</tr>
<?php
				$n++;
			}
		}
?>
	</table>
<?php
}


if($_POST["type"]=="delete_com")
{
	//print_r($_POST);
	
	$centreno=mysqli_real_escape_string($link, $_POST["centreno"]);
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id=mysqli_real_escape_string($link, $_POST["type_id"]);
	$testid=mysqli_real_escape_string($link, $_POST["testid"]);
	
	if(mysqli_query($link, "DELETE FROM `centre_test_discount_setup` WHERE `centreno`='$centreno' AND `category_id`='$category_id' AND `type_id`='$type_id' AND `testid`='$testid'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Failed, try again later.";
	}
}
?>
