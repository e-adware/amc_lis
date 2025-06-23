<?php
if($p_info["levelid"]==1)
{
	$branch_str="";
	$branch_display="";
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
					<select id="branch_id" class="span2" onChange="view_all()" style="<?php echo $branch_display; ?>">
					<?php
						$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while($branch=mysqli_fetch_array($branch_qry))
						{
							if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
					?>
					</select>
					<select id="visit_source_id" class="span2" onChange="view_all()">
						<option value="0">Select Source</option>
					<?php
						$qry=mysqli_query($link, " SELECT `visit_source_id`,`visit_source_name` FROM `visit_source_master` WHERE `status`=0 ORDER BY `visit_source_name` ASC ");
						while($data=mysqli_fetch_array($qry))
						{
							echo "<option value='$data[visit_source_id]'>$data[visit_source_name]</option>";
						}
					?>
					</select>
					<select id="executive_id" class="span2" onChange="view_all()">
						<option value="0">Select Executive</option>
					<?php
						$qry=mysqli_query($link, " SELECT `executive_id`,`name` FROM `marketing_executive_msater` WHERE `name`!='' ORDER BY `name` ASC ");
						while($data=mysqli_fetch_array($qry))
						{
							echo "<option value='$data[executive_id]'>$data[name]</option>";
						}
					?>
					</select>
					<select id="pharmacy_id" class="span2" onChange="view_all()">
						<option value="0">Select Pharmacy</option>
					<?php
						$qry=mysqli_query($link, " SELECT `pharmacy_id`,`name` FROM `ref_pharmacy_msater` WHERE `name`!='' ORDER BY `name` ASC ");
						while($data=mysqli_fetch_array($qry))
						{
							echo "<option value='$data[pharmacy_id]'>$data[name]</option>";
						}
					?>
					</select>
					<select id="collection_id" class="span2" onChange="view_all()">
						<option value="0">Select Collection</option>
					<?php
						$qry=mysqli_query($link, " SELECT `collection_id`,`name` FROM `collection_master` WHERE `name`!='' ORDER BY `name` ASC ");
						while($data=mysqli_fetch_array($qry))
						{
							echo "<option value='$data[collection_id]'>$data[name]</option>";
						}
					?>
					</select>
					<br>
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" readonly>
					
					<br>
					<button class="btn btn-search"  onClick="view_all(1)"><i class="icon-search"></i> View OPD</button>
					<button class="btn btn-search"  onClick="view_all(2)"><i class="icon-search"></i> View Lab</button>
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
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
		//$("#user_entry").select2({ theme: "classic" });
	});
	
	function view_all(val)
	{
		$("#loader").show();
		$.post("pages/patient_refer_case_data.php",
		{
			type:val,
			date1:$("#from").val(),
			date2:$("#to").val(),
			branch_id:$("#branch_id").val(),
			visit_source_id:$("#visit_source_id").val(),
			executive_id:$("#executive_id").val(),
			pharmacy_id:$("#pharmacy_id").val(),
			collection_id:$("#collection_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
			$("#save_btn").hide();
		})
	}
	function print_page(val,date1,date2,branch_id,visit_source_id,executive_id,pharmacy_id,collection_id)
	{
		url="pages/patient_refer_case_print.php?val="+val+"&date1="+date1+"&date2="+date2+"&branch_id="+branch_id+"&visit_source_id="+visit_source_id+"&executive_id="+executive_id+"&pharmacy_id="+pharmacy_id+"&collection_id="+collection_id;
		
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
