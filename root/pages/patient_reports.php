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
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
					<select id="con_cod_id" onChange="view_all('patient_reports')" class="span2" style="display:;">
						<option value="0">All Doctor</option>
					<?php
						$con_doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` order by `Name` ");
						while($con_doc=mysqli_fetch_array($con_doc_qry))
						{
							echo "<option value='$con_doc[consultantdoctorid]'>$con_doc[Name]</option>";
						}
					?>
					</select>
					<select id="dept_id" onChange="view_all('patient_reports')" class="span2" style="display:none;">
						<option value="0">Select Department</option>
					<?php
						$dept_qry=mysqli_query($link, " SELECT * FROM `doctor_specialist_list` ORDER BY `name` ");
						while($dept=mysqli_fetch_array($dept_qry))
						{
							echo "<option value='$dept[speciality_id]'>$dept[name]</option>";
						}
					?>
					</select>
					<select id="visit_type" onChange="view_all('patient_reports')" class="span2" style="display:none;">
						<option value="0">All Visit Type</option>
						<option value="1">First Visit</option>
						<option value="2">Re-Visit</option>
					</select>
					<br>
					<button class="btn btn-success" onClick="view_all('patient_reports')">View</button>
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
<script>
	$(document).ready(function(){
		$("#loader").hide();
		view_all('patient_reports');
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
	});
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/patient_reports_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
			con_cod_id:$("#con_cod_id").val(),
			dept_id:$("#dept_id").val(),
			visit_type:$("#visit_type").val(),
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
	function print_page(val,date1,date2,con_cod_id,dept_id,visit_type)
	{
		if(val=="patient_reports")
		{
			url="pages/patient_reports_print.php?date1="+date1+"&date2="+date2+"&con_cod_id="+con_cod_id;
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
