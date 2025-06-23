<?php
	include('../../includes/connection.php');
	
	$category_id=$_GET['cat'];
	$sub_category_id=$_GET['sub_cat'];
	$item_type_id=$_GET['item_type'];
	$manufacturer_id=$_GET['manufacturer'];
	$user=$_GET['user'];
	
	$qry=" SELECT * FROM `item_master` WHERE `item_id`>0 ";

	if($category_id>0)
	{
		$qry.=" AND `category_id`='$category_id' ";
	}
	if($sub_category_id>0)
	{
		$qry.=" AND `sub_category_id`='$sub_category_id' ";
	}
	if($item_type_id>0)
	{
		$qry.=" AND `item_type_id`='$item_type_id' ";
	}
	if($manufacturer_id>0)
	{
		$qry.=" AND `manufacturer_id`='$manufacturer_id' ";
	}

	$qry.=" ORDER BY `item_name` ";
	
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
	<title>Stock Item List</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
</head>
<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container">
		<div class="">
			<div class="">
				<div class="text-center">
					<h3>Stock Item List</h3>
					<div class="noprint "><input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-warning" id="Name2" value="Exit" onclick="javascript:window.close()"></div>
				</div>
				<table class="table table-bordered data-table" style="font-size: 12px;">
					<thead style="background: #ddd;">
						<tr>
							<th>#</th>
							<th>Item Name</th>
							<th>Short Name</th>
							<th>Category</th>
							<th>Sub Category</th>
							<th>Item Type</th>
							<th>HSN Code</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$i=1;
						$item_qry=mysqli_query($link, $qry);
						while($item=mysqli_fetch_array($item_qry))
						{
							$category=mysqli_fetch_array(mysqli_query($link, " SELECT `category_name` FROM `stock_category_master` WHERE `category_id`='$item[category_id]' "));
							
							$sub_category=mysqli_fetch_array(mysqli_query($link, " SELECT `sub_category_name` FROM `stock_sub_category_master` WHERE `sub_category_id`='$item[sub_category_id]' "));
							
							$item_type=mysqli_fetch_array(mysqli_query($link, " SELECT `item_type_name` FROM `item_type_master` WHERE `item_type_id`='$item[item_type_id]' "));
							
							//$manufacturer=mysqli_fetch_array(mysqli_query($link, " SELECT `manufacturer_name` FROM `manufacturer_company` WHERE `manufacturer_id`='$item[manufacturer_id]' "));
							
					?>
						<tr class="gradeX" id="test<?php echo $i ?>">
							<td id="item_id<?php echo $i ?>">
								<?php echo $i; ?>
							</td>
							<td>
								<?php echo $item["item_name"]; ?>
							</td>
							<td>
								<?php echo $item["short_name"]; ?>
							</td>
							<td><?php echo $category['category_name']; ?></td>
							<td>
								<span><?php echo $sub_category['sub_category_name']; ?></span>
							</td>
							<td>
								<span><?php echo $item_type['item_type_name']; ?></span>
							</td>
							<td>
								<span><?php echo $item['hsn_code']; ?></span>
							</td>
						</tr>
					<?php
							$i++;
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
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
