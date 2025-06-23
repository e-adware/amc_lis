<?php
include('../../includes/connection.php');
require('../../includes/global.function.php');

$rupees_symbol="&#x20b9; ";

$today=date("Y-m-d");

// important
$date11=$_GET['fdate'];
$date22=$_GET['tdate'];

$date1=$_GET['fdate'];
$date2=$_GET['tdate'];

$user=$_GET['user'];
//$account_break=$_GET['account_break'];
	
	
?>
<html>
<head>
	<title>Cash User Wise</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
	<style>
		*{font-size:11px;}
	</style>
</head>
<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Cash User Wise</h4>
			<b>From <?php echo convert_date($date11); ?> to <?php echo convert_date($date22); ?></b>
			
			<div class="account_close_div">
				<?php
					$date=date("Y-m-d");
					$check_close_account=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_pharmacy` WHERE `close_date`='$date22' "));
					if($check_close_account)
					{
						$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$check_close_account[user]' "));
						
					?>
						<table class="table table-condensed">
							<tr>
								<td>
									<select id="account_break" class="" style="width:25%;">
										<option value="0">Current</option>
								<?php
										$c=1;
										$close_qry=mysqli_query($link, " SELECT * FROM `daily_account_close_pharmacy` WHERE `close_date`='$date22' ");
										while($account_close=mysqli_fetch_array($close_qry))
										{
											echo "<option value='$account_close[slno]'>Break $c</option>";
											$c++;
										}
								?>
									</select>
									<button class="btn btn-info" onClick="view_break()">View</button>
								</td>
								<td>
									<div class="noprint "><input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()"></div>	
								</td>
								<td>
								<?php
					
									$last_break=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_pharmacy` WHERE `slno` IN(SELECT MAX(`slno`) FROM `daily_account_close_pharmacy`) "));
									
									$check_close_account=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ph_payment_details` WHERE `sl_no`>$last_break[ph_slno] "));
									if($check_close_account)
									{
								?>
										<input type="button" value="Close Account" class="btn btn-danger" onClick="close_account('<?php echo $user; ?>','<?php echo $date22; ?>')" >
								<?php
									}
								?>
								</td>
							</tr>
						</table>
					<?php
					}else
					{
						if($date==$date22)
						{
							$close_btn="Break Account";
						}else
						{
							$close_btn="Close Account of ".convert_date($date22);
						}
				?>
					<input type="button" value="<?php echo $close_btn; ?>" class="btn btn-danger" onClick="close_account('<?php echo $user; ?>','<?php echo $date22; ?>')" >
					<br>
					<!--<table class="table table-hover table-condensed">
						<tr>
							<th>#</th>
							<th>Date Time</th>
							<th>Bill No</th>
							<th>Patient Name</th>
							<th>Bill Amount</th>
							<th>Disc.</th>
							<th>Amount Received</th>
						</tr>-->
				<?php
						//~ $n=1;
						
						//~ $q=" SELECT * FROM `ph_payment_details` WHERE `entry_date` BETWEEN '$date1' AND '$date2' AND `user`='$user' ";
						
						//~ $data=mysqli_query($link,$q);
						//~ while($p=mysqli_fetch_array($data))
						//~ {
							//~ $uname1="";
							//~ if($us==1)
							//~ {
								//~ $uname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select name from employee where emp_id='$p[user]'"));
								//~ $uname1=" - ".$uname[name];
							//~ }
							//~ $reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_sell_master where bill_no='$p[bill_no]' and user='$user'"));
							
					?>
						<!--<tr>
							<td><?php echo $n; ?></td>
							<td><?php echo convert_date($reg["entry_date"]); ?> <?php echo convert_time($reg["time"]); ?></td>
							<td><?php echo $reg["bill_no"]; ?></td>
							<td><?php echo $reg["customer_name"]; ?></td>
							<td><?php echo $reg["total_amt"]; ?></td>
							<td><?php echo $reg["discount_amt"]; ?></td>
							<td><?php echo $p["amount"]; ?></td>
						</tr>-->
				<?php
							//~ $n++;
						//~ }
						
			?>
					<!--</table>-->
			<?php
					}
			?>
			</div>
			<div id="load_data"></div>
		</center>
	</div>
	<span id="user" style="display:none;"><?php echo $user; ?></span>
	<span id="date1" style="display:none;"><?php echo $date11; ?></span>
	<span id="date2" style="display:none;"><?php echo $date22; ?></span>
</body>
</html>
<script>
	$(document).keydown(function (event) {
		if (event.keyCode == 123 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent F12
			return false;
		} else if (event.ctrlKey && event.shiftKey && event.keyCode == 73 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent Ctrl+Shift+I        
			return false;
		}
	});
	$(document).on("contextmenu",function(e){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			//e.preventDefault();
		}
	});
	$(document).ready(function(){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			//window.print();
		}
		view_break();
	});
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
	function close_account(user,c_date)
	{
		//alert(user+' '+c_date);
		if(confirm("Are you sure want to close account ?"))
		{
			$.post("../pages/close_account_data.php",
			{
				type:"close_account_pharmacy",
				c_date:c_date,
				user:user,
			},
			function(data,status)
			{
				alert(data+"'s account is closed");
				window.location.reload(true);
			})
		}
	}
	function view_break()
	{
		$.post("../pages/close_account_data.php",
		{
			type:"view_account_single",
			account_break:$("#account_break").val(),
			date1:$("#date1").text().trim(),
			date2:$("#date2").text().trim(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			//alert(data);
			$("#load_data").html(data);
		})
	}
</script>
<style>
.ipd_serial
{
	display:none;
}
@media print
{
	.account_close_div
	{
		display:none;
	}
	.noprint{
		display:none;
	 }
}
</style>
