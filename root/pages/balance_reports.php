<?php
$emp_id=trim($_SESSION["emp_id"]);
$branch_display="display:none;";
if($p_info["levelid"]==1)
{
	$branch_str="";
	
	$branch_display="display:none;";
	$branch_num=mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if($branch_num>1)
	{
		$branch_display="display:;";
	}
	
	$dept_sel_dis="";
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
	
	$dept_sel_dis="disabled";
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
					<span class="side_name">From</span>
					<input class="form-control datepicker span2" type="text" name="date1" id="date1" value="<?php echo date("Y-m-d") ?>" style="margin-left: 47px;">
					<span class="side_name">To</span>
					<input class="form-control datepicker span2" type="text" name="date2" id="date2" value="<?php echo date("Y-m-d") ?>" style="margin-left: 25px;">
					
					<select id="encounter" class="span2">
						<!--<option value="0">All Department</option>-->
					<?php
						$qq_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `status`=0 ORDER BY `p_type_id` ");
						while($qq=mysqli_fetch_array($qq_qry))
						{
							echo "<option value='$qq[p_type_id]'>$qq[p_type]</option>";
						}
					?>
					</select>
					
					<select id="branch_id" class="span2" style="<?php echo $branch_display; ?>">
					<?php
						$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while($branch=mysqli_fetch_array($branch_qry))
						{
							if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
					?>
					</select>
					
				</center>
			</td>
			<tr>
				<td>
					<center>
						<span class="side_name">UNIT No.</span>
						<input list="browsrs" type="text" class="span2" id="uhid" style="margin-left: 76px;">
						
						<span class="side_name">Bill No.</span>
						<input list="browsr" type="text" class="span2" id="bill_no" style="margin-left: 58px;">
						
						<span class="side_name">Name</span>
						<input type="text" class="span2" id="pat_name" style="margin-left: 52px;">
						
						<span class="side_name">Address</span>
						<input type="text" class="span3" id="address" style="margin-left: 68px;">
						
						<br>
						<br>
						<button class="btn btn-search" onClick="view_all('balance_patient')"><i class="icon-search"></i> View Credit Patient</button>
					</center>
				</td>
			</tr>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
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
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
	});
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/balance_reports_data.php",
		{
			type:typ,
			date1:$("#date1").val(),
			date2:$("#date2").val(),
			encounter:$("#encounter").val(),
			branch_id:$("#branch_id").val(),
			uhid:$("#uhid").val(),
			bill_no:$("#bill_no").val(),
			pat_name:$("#pat_name").val(),
			address:$("#address").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").slideUp(500,function(){ $("#load_all").html(data).slideDown(500); });
		})
	}
	
	function print_page(val,date1,date2,encounter,branch_id,uhid,bill_no,pat_name,address)
	{
		url="pages/balance_report_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&branch_id="+branch_id+"&uhid="+uhid+"&bill_no="+bill_no+"&pat_name="+pat_name+"&address="+address+"&val="+val;
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function export_page(val,date1,date2,encounter,branch_id,uhid,bill_no,pat_name,address)
	{
		url="pages/balance_report_xls.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&branch_id="+branch_id+"&uhid="+uhid+"&bill_no="+bill_no+"&pat_name="+pat_name+"&address="+address+"&val="+val;
		
		document.location=url;
	}
	
</script>
<style>
.side_name
{
	border: 1px solid #ddd;
	background-color: #fff;
	padding: 4px;
	position: absolute;
	font-weight:bold;
}
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
