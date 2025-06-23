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
					<select id="con_cod_id" onChange="view_all('doctor_account')" class="span2">
						<option value="0">Select Doctor</option>
					<?php
						$con_doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` order by `Name` ");
						while($con_doc=mysqli_fetch_array($con_doc_qry))
						{
							echo "<option value='$con_doc[consultantdoctorid]'>$con_doc[Name]</option>";
						}
					?>
					</select>
					<select id="dept_id" onChange="view_all('doctor_account')" class="span2" style="display:;">
						<option value="0">All Department</option>
					<?php
						$dept_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `status`=0  ORDER BY `p_type_id` ");
						while($dept=mysqli_fetch_array($dept_qry))
						{
							echo "<option value='$dept[p_type_id]'>$dept[p_type]</option>";
						}
					?>
					</select>
					<br>
					<button class="btn btn-success" onClick="view_all('doctor_account')">View Account</button>
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
			changeMonth: true,
			changeYear: true,
			dateFormat: "yy-mm-dd",
			maxDate: "0",
			yearRange: "-150:+0",
		});
		$("select").select2({ theme: "classic" });
	});
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/doctor_account_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
			con_cod_id:$("#con_cod_id").val(),
			dept_id:$("#dept_id").val(),
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
	function export_excel()
	{
		var date1=$("#from").val();
		var date2=$("#to").val();
		var con_cod_id=$("#con_cod_id").val();
		var dept_id=$("#dept_id").val();
		
		var url="pages/doctor_account_print_xls.php?date1="+date1+"&date2="+date2+"&con_cod_id="+con_cod_id+"&dept_id="+dept_id;
		document.location=url;
	}
	function print_page(val,date1,date2,con_cod_id,dept_id)
	{
		if(val=="opd_account")
		{
			url="pages/doctor_account_print.php?date1="+date1+"&date2="+date2+"&con_cod_id="+con_cod_id+"&dept_id="+dept_id;
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
<?php
	//~ $counter_qry=mysqli_query($link, " SELECT DISTINCT a.`ipd_id` FROM `doctor_service_done` a, `uhid_and_opdid` b WHERE a.`ipd_id`=b.`opd_id` AND a.`date`!=b.`date` ");
	//~ while($all_pat=mysqli_fetch_array($counter_qry))
	//~ {
		//~ $pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pat[ipd_id]' "));
		
		//~ mysqli_query($link, " UPDATE `doctor_service_done` SET `date`='$pat_reg[date]' WHERE `ipd_id`='$all_pat[ipd_id]' ");
		
	//~ }
	
	$counter_qry=mysqli_query($link, " SELECT a.`ipd_id`, b.`date` FROM `doctor_service_done` a, `ipd_pat_service_details` b WHERE a.`ipd_id`=b.`ipd_id` AND a.`service_id`=b.`service_id` AND a.`date`!=b.`date` ");
	while($all_pat=mysqli_fetch_array($counter_qry))
	{
		//$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$all_pat[ipd_id]' "));
		
		mysqli_query($link, " UPDATE `doctor_service_done` SET `date`='$all_pat[date]' WHERE `ipd_id`='$all_pat[ipd_id]' ");
		
	}
?>
