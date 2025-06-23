<?php
$not_accountant = array();
array_push($not_accountant, 5, 6, 11, 12, 13, 20, 21);
$not_accountant = join(',',$not_accountant);
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<b>From</b>
					<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
					<select id="user_entry" class="span2" <?php echo $dept_sel_dis; ?> onChange="view_all('cash_deposit')">
						<option value="0">Select User</option>
					<?php
						$user_qry=mysqli_query($link, " SELECT `emp_id`,`name` FROM `employee` WHERE levelid NOT IN ($not_accountant) ORDER BY `name` ");
						while($user=mysqli_fetch_array($user_qry))
						{
							if($emp_id==$user["emp_id"]){ $sel_this="selected"; }else{ $sel_this=""; }
							echo "<option value='$user[emp_id]' $sel_this>$user[name]</option>";
						}
					?>
					</select>
					<br>
					<button class="btn btn-success" onClick="view_all('cash_deposit')">View</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
	});
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/daily_account_details_all.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
			user_entry:$("#user_entry").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
			$("#now_pay").focus();
		})
	}
	function save_cash_deposit()
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure, you have received cash ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> OK',
					className: "btn btn-info",
					callback: function() {
						$("#loader").show();
						$.post("pages/daily_account_details_all.php",
						{
							type:"save_cash_deposit",
							now_pay:$("#now_pay").val(),
							user_entry:$("#user_entry").val(),
							user:$("#user").text().trim(),
						},
						function(data,status)
						{
							bootbox.dialog({ message: "<h5>"+data+"</h5>"});
							setTimeout(function(){
								bootbox.hideAll();
								view_all('cash_deposit');
							 }, 2000);
						})
					}
				}
			}
		});
	}
	function detail_cash_deposit()
	{
		var date1=$("#from").val();
		var date2=$("#to").val();
		var user_entry=$("#user_entry").val();
		
		url="pages/cash_deposit_print.php?date1="+date1+"&date2="+date2+"&user_entry="+user_entry;
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
@media print {
  body * {
    visibility: hidden;
  }
  #load_all, #load_all * {
    visibility: visible;
  }
  #load_all {
	  overflow:visible;
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>
