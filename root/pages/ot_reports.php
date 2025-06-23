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
					<!--<select id="user_entry" class="span2">
						<option value="0">All User</option>
					<?php
						//$user_qry=mysqli_query($link, " SELECT `emp_id`,`name` FROM `employee` ORDER BY `name` ");
						$user_qry=mysqli_query($link, " SELECT DISTINCT `user` FROM `ot_book`");
						while($user=mysqli_fetch_array($user_qry))
						{
							$u=mysqli_fetch_array(mysqli_query($link, " SELECT `emp_id`,`name` FROM `employee` WHERE `emp_id`='$user[user]'"));
							echo "<option value='$u[emp_id]'>$u[name]</option>";
						}
					?>
					</select>-->
					<br>
					<button class="btn btn-success" onClick="view_all('ot_pat_list')">OT Patients</button>
					<button class="btn btn-success" onClick="view_all('doc_wise_pat')">Doctor wise</button>
					<button class="btn btn-success" onClick="view_all('ot_type_rep')">OT Type</button>
					<button class="btn btn-success" onClick="view_all('delivery_rep')">Delivery Report</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
<!--
	<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">
-->
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
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
		$.post("pages/ot_reports_ajax.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
			user:$("#user_entry").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function delivery_rep(dt1,dt2,typ)
	{
		var url="pages/delivery_report.php?date1="+dt1+"&date2="+dt2+"&type="+typ;
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
