<?php
$branch_str=" AND `branch_id`='$p_info[branch_id]'";
$element_style="display:none";
if($p_info["levelid"]==1)
{
	$branch_str="";
	//$element_style="";
}
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
					<select id="branch_id" class="span2" onchange="load_center()" style="<?php echo $element_style; ?>">
					<?php
						$qry=mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
						while($data=mysqli_fetch_array($qry))
						{
							if($data["branch_id"]==$p_info["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$data[branch_id]' $branch_sel>$data[name]</option>";
						}
					?>
					</select>
					<span class="side_name">Doctor</span>
					<select id="doc_id" class="span2" style="margin-left: 58px;">
						<option value="0">--Select--</option>
					<?php
						$qry=mysqli_query($link," SELECT `id`,`name` FROM `lab_doctor` WHERE `category`=2 ORDER BY `name` ASC ");
						while($data=mysqli_fetch_array($qry))
						{
					?>
						<option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
					<?php
						}
					?>
					</select>
					<span class="side_name">Department</span>
					<select id="type_id" class="span2" style="margin-left: 100px;">
						<option value="0">--Select--</option>
					<?php
						$qry=mysqli_query($link," SELECT `id`, `name` FROM `test_department` WHERE `id` IN(SELECT DISTINCT a.`type_id` FROM `testmaster` a, `patient_test_details` b WHERE a.`testid`=b.`testid` AND a.`category_id`=2) ORDER BY `name` ASC ");
						while($data=mysqli_fetch_array($qry))
						{
					?>
						<option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
					<?php
						}
					?>
					</select>
					<span class="side_name">From</span>
					<input class="form-control datepicker span2" type="text" name="date1" id="date1" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 47px;">
					<span class="side_name">To</span>
					<input class="form-control datepicker span2" type="text" name="date2" id="date2" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 25px;">
					<br>
					<button class="btn btn-search" id="doctor_wise" onclick="view('doctor_wise')"><i class="icon-search"></i> View</button>
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
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
	});
	function view(typ)
	{
		if(typ=="doctor_wise" && $("#doc_id").val()==0)
		{
			alert("Select Doctor");
			return false;
		}
		
		$("#loader").show();
		$.post("pages/radio_doctor_reporting_reports_data.php",
		{
			type:typ,
			date1:$("#date1").val(),
			date2:$("#date2").val(),
			branch_id:$("#branch_id").val(),
			doc_id:$("#doc_id").val(),
			type_id:$("#type_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			//$("#load_all").slideUp(500,function(){ $("#load_all").html(data).slideDown(500); });
			$("#load_all").html(data).slideDown(500);
		})
	}
	
	function print_page(typ,branch_id,doc_id,type_id,date1,date2)
	{
		var val=btoa(1234567890);
		var user=$("#user").text().trim();
		
		var url="pages/radio_doctor_reporting_reports_print.php?val="+val+"&typ="+typ+"&bid="+branch_id+"&tid="+type_id+"&did="+doc_id+"&date1="+date1+"&date2="+date2+"&EpMl="+user;
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
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
.ipd_serial
{
	display:none;
}
</style>
