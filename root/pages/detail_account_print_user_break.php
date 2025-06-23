<?php
session_start();
include('../../includes/connection.php');

	$c_user=trim($_SESSION['emp_id']);

	// Date format convert
	function convert_date($date)
	{
		if($date)
		{
			$timestamp = strtotime($date); 
			$new_date = date('d M Y', $timestamp);
			return $new_date;
		}
	}
	// Time format convert
	function convert_time($time)
	{
		$time = date("g:i A", strtotime($time));
		return $time;
	}
	$rupees_symbol="&#x20b9; ";
	
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$encounter=$_GET['encounter'];
	$encounter_val=$_GET['encounter'];
	$pay_mode=$_GET['pay_mode'];
	$user_entry=$_GET['user_entry'];
	$user_val=$_GET['EpMl'];
	$account_break=$_GET['account_break'];
	
	// important
	$date11=$_GET['date1'];
	$date22=$_GET['date2'];
	
?>
<html>
<head>
	<title>Detail Account Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Detail Account Report</h4>
			<b style="float: left;">From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
			<!--<div class="noprint ">
				<input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>-->
			<div class="account_close_div">
			<?php
				if($account_break==0)
				{
					if($c_user==$user_entry)
					{
						$date=date("Y-m-d");
					
						if($date==$date22)
						{
							$close_btn="Close Today's Account";
						}else
						{
							$close_btn="Close Account of ".convert_date($date22);
						}
						$close_btn="Close Account";
			?>
				<?php
					if($pay_mode=="0" && $encounter==0) // All payment mode
					{
				?>
					<input type="button" id="btn_close" value="<?php echo $close_btn; ?>" class="btn btn-danger" onClick="close_account('<?php echo $user_val; ?>')" >
				<?php } ?>
					<div class="noprint ">
						<!--<input type="button" class="btn btn-info" id="det_excel" value="Excel" onclick="export_excel()">-->
						<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()"> 
						<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="close_window_child()">
					</div>
			<?php
					}	
				}else
				{
			?>
					<div class="noprint1 ">
						<!--<input type="button" class="btn btn-info" id="det_excel" value="Excel" onclick="export_excel()">-->
						<input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()"> 
						<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="close_window_child()">
					</div>
			<?php
				}
			?>
			</div>
		</center>
		<div id="load_data"></div>
	</div>
	<input type="hidden" id="from" value="<?php echo $date11; ?>">
	<input type="hidden" id="to" value="<?php echo $date22; ?>">
	<input type="hidden" id="encounter" value="<?php echo $encounter; ?>">
	<input type="hidden" id="user_entry" value="<?php echo $user_entry; ?>">
	<input type="hidden" id="pay_mode" value="<?php echo $pay_mode; ?>">
	<input type="hidden" id="account_break" value="<?php echo $account_break; ?>">
</body>
</html>
<script>
	$(document).ready(function(){
		view();
		$(".noprint").hide();
	});
	function view()
	{
		$.post("daily_account_details_all_user_break.php",
		{
			type:"all_account",
			date1:$("#from").val(),
			date2:$("#to").val(),
			encounter:$("#encounter").val(),
			user_entry:$("#user_entry").val(),
			pay_mode:$("#pay_mode").val(),
			account_break:$("#account_break").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			$("#print_div").hide();
		})
	}
	function export_excel()
	{
		var date1=$("#from").val();
		var date2=$("#to").val();
		var encounter=$("#encounter").val();
		var user_entry=$("#user_entry").val();
		var pay_mode=$("#pay_mode").val();
		var account_break=$("#account_break").val();
		
		var url="detail_account_print_user_break_xls.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&pay_mode="+pay_mode+"&account_break="+account_break;
		document.location=url;
	}
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
	function close_account(user)
	{
		//alert(user);
		if(confirm("Are you sure want to close account ?"))
		{
			$.post("../pages/close_account_data.php",
			{
				type:"close_account_single",
				user:user,
			},
			function(data,status)
			{
				var res=data.split("@#$@");
				
				alert(res[0]+"'s account is closed");
				//window.location.reload(true);
				//~ $(".noprint").show();
				//~ $("#btn_close").hide();
				
				var user=$("#user").text().trim();
				
				url="detail_account_print_user_break.php?date1="+$("#from").val()+"&date2="+$("#to").val()+"&encounter="+$("#encounter").val()+"&user_entry="+$("#user_entry").val()+"&pay_mode="+$("#pay_mode").val()+"&EpMl="+user+"&account_break="+res[1];
				
				window.location.href=url;
			})
		}
	}
	function close_window_child()
	{
		window.close();
	}
	function refreshParent()
	{
		window.opener.location.reload(true);
	}
</script>
<style type="text/css" media="print">
  @page { size: landscape; }
  
</style>
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
	.noprint1
	{
		display:none;
	}
	.noprint{
		display:none;
	 }
	<?php if($account_break==0){ ?>
	 *{ //display:none; }
	<?php } ?>
}
.ipd_serial
{
	display:none;
}
.table
{
	margin-bottom: 0px;
}
.table-condensed th, .table-condensed td
{
	//padding: 0;
	padding: 0 10px 0 0;
}
</style>
