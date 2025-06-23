<html>
<head>
<title>Mapped Item Details Report</title>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../css/loader.css" />
<link rel="stylesheet" href="../../font-awesome/css/font-awesome.css" />
<script src="../../js/jquery.min.js"></script>
<style>
.table tr th, .table tr td
{
	padding:0px 3px 0px 3px;
	font-size:13px;
}
.table-report tr th, .table-report tr td
{
	background:white;
}

.table-report tr:first-child td, .table-report tr:first-child th
{
  background:#666 !important;
  
  color:#fff;
  font-weight:bold;
}
@media print{
 .noprint{
	 display:none;
 }
}
@page
{
	left:0px;
	right:0px;
}
</style>

</head>
<body>
<?php
include'../../includes/connection.php';

/*
$substore_id=3;
$date=date("Y-m-d");
if($date>=date("Y-m-d", strtotime("2022-01-13")))
{
$qq=mysqli_query($link,"SELECT DISTINCT a.`testid` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`category_id`='2' AND a.`date`='$date'");
while($rr=mysqli_fetch_assoc($qq))
{
	//echo $rr['testid']."<br/>";
	$v=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`testid`) AS `counts` FROM `patient_test_details` WHERE `testid`='$rr[testid]' AND `date`='$date'"));
	$itm_qry=mysqli_query($link,"SELECT * FROM `radiology_maping` WHERE `testid`='$rr[testid]'");
	while($itm=mysqli_fetch_assoc($itm_qry))
	{
		$qnt=$v['counts']*$itm['quantity'];
		$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `substore_id`='$substore_id' AND `item_code`='$itm[item_id]' AND `date`='$date'"));
		if($stk)
		{
			$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `substore_id`='$substore_id' AND `item_code`='$itm[item_id]' AND `s_remain`>='$v[counts]' AND `date`='$date'"));
			if($stk)
			{
				$open=$stk['s_remain'];
				$closing=$stk['s_remain']-$qnt;
				$sell=$stk['sell']+$qnt;
				mysqli_query($link,"update ph_stock_process set s_remain='$closing',sell='$sell' where date='$date' and item_code='$itm[item_id]' and  batch_no='$stk[batch_no]' and substore_id='$substore_id' and slno='$stk[slno]'");
				mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm[item_id]' and batch_no='$stk[batch_no]' and substore_id='$substore_id'");
			}
			else
			{
				$rem_qnt=$qnt;
				while($rem_qnt>0)
				{
					$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `substore_id`='$substore_id' AND `item_code`='$itm[item_id]' AND `s_remain`>'0' AND `date`='$date'"));
					if($stk)
					{
						$open=$stk['s_remain'];
						if($stk['s_remain']>=$rem_qnt)
						{
							$closing=$stk['s_remain']-$rem_qnt;
							$sell=$stk['sell']+$rem_qnt;
							$rem_qnt=0;
						}
						else
						{
							$closing=0;
							$sell=$stk['s_remain'];
							$rem_qnt=$rem_qnt-$stk['s_remain'];
						}
						mysqli_query($link,"update ph_stock_process set s_remain='$closing',sell='$sell' where date='$date' and item_code='$itm[item_id]' and  batch_no='$stk[batch_no]' and substore_id='$substore_id' and slno='$stk[slno]'");
						mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm[item_id]' and batch_no='$stk[batch_no]' and substore_id='$substore_id'");
					}
					else
					{
						$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `substore_id`='$substore_id' AND `item_code`='$itm[item_id]' AND `s_remain`>'0' ORDER BY `slno` DESC LIMIT 0,1"));
						$open=$stk['s_remain'];
						if($stk['s_remain']>=$rem_qnt)
						{
							$closing=$stk['s_remain']-$rem_qnt;
							$sell=$stk['sell']+$rem_qnt;
							$rem_qnt=0;
						}
						else
						{
							$closing=0;
							$sell=$stk['s_remain'];
							$rem_qnt=$rem_qnt-$stk['s_remain'];
						}
						mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$substore_id','Radio Item','$itm[item_id]','$stk[batch_no]','$stk[s_remain]','0','$sell','$closing','$date')");
						mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm[item_id]' and batch_no='$stk[batch_no]' and substore_id='$substore_id'");
					}
				}
			}
		}
		else
		{
			$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `substore_id`='$substore_id' AND `item_code`='$itm[item_id]' AND `s_remain`>='$v[counts]' ORDER BY `slno` DESC LIMIT 0,1"));
			if($stk)
			{
				$closing=$stk['s_remain']-$qnt;
				mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$substore_id','Radio Item','$itm[item_id]','$stk[batch_no]','$stk[s_remain]','0','$qnt','$closing','$date')");
				mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm[item_id]' and batch_no='$stk[batch_no]' and substore_id='$substore_id'");
			}
			else
			{
				$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `substore_id`='$substore_id' AND `item_code`='$itm[item_id]' AND `s_remain`>='0' ORDER BY `slno` DESC LIMIT 0,1"));
				if(!$stk)
				{
					break;
				}
				$open=$stk['s_remain'];
				if($stk['s_remain']>=$rem_qnt)
				{
					$closing=$stk['s_remain']-$rem_qnt;
					$sell=$stk['sell']+$rem_qnt;
					$rem_qnt=0;
				}
				else
				{
					$closing=0;
					$sell=$stk['s_remain'];
					$rem_qnt=$rem_qnt-$stk['s_remain'];
				}
				mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$substore_id','Radio Item','$itm[item_id]','$stk[batch_no]','$stk[s_remain]','0','$sell','$closing','$date')");
				mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm[item_id]' and batch_no='$stk[batch_no]' and substore_id='$substore_id'");
			}
		}
	}
}
}
//*/

//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}


function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}

?>
<div class="container-fluid">
	<div class="" style="">
		<?php include('page_header.php'); ?>
		<center><h5><u>Mapped Item Details Report</u></h5></center>
	</div>
<input type="hidden" id="pid" value="<?php echo $pid;?>" />
<table width="100%">
<tr>
<td style="text-align:right"><div class="noprint"><input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="window.close()" /></div></td>
</tr>
</table>
<div id="res">

</div>
<div id="loader" style="display:none;position:fixed;top:50%;left:50%;z-index:9999;"></div>
</div>
<script>
	$(document).ready(function()
	{
		load_data();
	});
	function load_data()
	{
		$("#loader").show();
		$.post("inv_item_details_ajax.php",
		{
			pid:$("#pid").val().trim(),
			type:3,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
			//$(".nodisplay").hide();
			//$(".btn-primary").hide();
		})
	}
</script>
</body>
</html>

