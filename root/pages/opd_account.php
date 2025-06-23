<?php
if($p_info["levelid"]==1)
{
	$branch_str="";
	$branch_display="display:none;";
	$branch_num=mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if($branch_num>1)
	{
		$branch_display="display:;";
	}
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
}

$branch_id=$p_info["branch_id"];
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
					<select id="branch_id" class="span2" onChange="load_users()" style="<?php echo $branch_display; ?>">
					<?php
						$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while($branch=mysqli_fetch_array($branch_qry))
						{
							if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
					?>
					</select>
					<select id="user_entry" class="span2" onChange="view_all('opd_account')">
						<option value="0">Select User</option>
					</select>
					
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" readonly>
					<select id="con_cod_id" onChange="view_all('opd_account')" class="span2">
						<option value="0">All Doctor</option>
					<?php
						$con_doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `branch_id`='$p_info[branch_id]' order by `Name` ");
						while($con_doc=mysqli_fetch_array($con_doc_qry))
						{
							echo "<option value='$con_doc[consultantdoctorid]'>$con_doc[Name]</option>";
						}
					?>
					</select>
					<select id="payment_mode" onChange="view_all('opd_account')" class="span2" style="display:none;">
						<option value="">All Mode</option>
					<?php
						$paymode_qry=mysqli_query($link, " SELECT `p_mode_id`, `p_mode_name` FROM `payment_mode_master` WHERE `operation`=1 ORDER BY `sequence` ASC ");
						while($paymode=mysqli_fetch_array($paymode_qry))
						{
							echo "<option value='$paymode[p_mode_name]'>$paymode[p_mode_name]</option>";
						}
					?>
					</select>
					<select id="dept_id" onChange="view_all('opd_account')" class="span2" style="display:none;">
						<option value="0">Select Department</option>
					<?php
						$dept_qry=mysqli_query($link, " SELECT * FROM `doctor_specialist_list` ORDER BY `name` ");
						while($dept=mysqli_fetch_array($dept_qry))
						{
							echo "<option value='$dept[speciality_id]'>$dept[name]</option>";
						}
					?>
					</select>
					<select id="patient_type" onChange="view_all('opd_account')" class="span2" style="display:none;">
						<option value="0">Patient Type</option>
					<?php
						$center_qry=mysqli_query($link, " SELECT * FROM `centremaster` order by `centreno` ");
						while($center=mysqli_fetch_array($center_qry))
						{
							echo "<option value='$center[centreno]' $sel >$center[centrename]</option>";
						}
					?>
					</select>
					<select id="visit_type" onChange="view_all('opd_account')" class="span1" style="display:none;">
						<option value="0">All Visit</option>
						<option value="1">First Visit</option>
						<option value="2">Re-Visit</option>
					</select>
					
					<br>
					<button class="btn btn-success" onClick="view_all('opd_account')">View OPD Account</button>
					<!--<button class="btn btn-success " onClick="view_all('opd_cancel_report')">Cancel Report</button>-->
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
	<!--<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">-->
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function(){
		$("#loader").hide();
		view_all('opd_account');
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		$("#con_cod_id").select2({ theme: "classic" });
		
		load_users();
	});
	function load_users()
	{
		$("#loader").show();
		$.post("pages/opd_account_data.php",
		{
			type:"load_users",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#user_entry").show().html(data);
			
			view_all('opd_account');
		})
	}
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/opd_account_data.php",
		{
			type:typ,
			branch_id:$("#branch_id").val(),
			date1:$("#from").val(),
			date2:$("#to").val(),
			con_cod_id:$("#con_cod_id").val(),
			payment_mode:$("#payment_mode").val(),
			dept_id:$("#dept_id").val(),
			patient_type:$("#patient_type").val(),
			visit_type:$("#visit_type").val(),
			branch_id:$("#branch_id").val(),
			user_entry:$("#user_entry").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function print_doc_pat(el)
	{
		$("#excel_btn_hide").hide();
		var restorepage = $('body').html();
		var printcontent = $('#' + el).clone();
		$('body').empty().html(printcontent);
		window.print();
		$('body').html(restorepage);
	}
	function print_page(val,date1,date2,con_cod_id,dept_id,visit_type,patient_type,user_entry,branch_id,payment_mode)
	{
		if(val=="opd_account")
		{
			url="pages/opd_account_print.php?date1="+date1+"&date2="+date2+"&con_cod_id="+con_cod_id+"&dept_id="+dept_id+"&visit_type="+visit_type+"&patient_type="+patient_type+"&user_entry="+user_entry+"&branch_id="+branch_id+"&payment_mode="+payment_mode;
		}
		if(val=="opd_cancel_report")
		{
			url="pages/opd_cancel_report_print.php?date1="+date1+"&date2="+date2+"&con_cod_id="+con_cod_id+"&dept_id="+dept_id+"&visit_type="+visit_type+"&patient_type="+patient_type+"&user_entry="+user_entry+"&branch_id="+branch_id+"&payment_mode="+payment_mode;
		}
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1200');
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
</style>
