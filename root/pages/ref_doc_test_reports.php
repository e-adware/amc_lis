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
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" readonly>
					
					<select id="refbydoctorid" onChange="view_all('view')" class="span2">
						<option value="0">All Doctor</option>
					<?php
						$con_doc_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` ORDER BY `ref_name` ");
						while($con_doc=mysqli_fetch_array($con_doc_qry))
						{
							echo "<option value='$con_doc[refbydoctorid]'>$con_doc[ref_name]</option>";
						}
					?>
					</select>
					<select id="head_id" onChange="view_all('view')" class="span2">
						<option value="0">All Department</option>
						<option value="1">Pathology</option>
					<?php
						$test_dept_qry=mysqli_query($link, " SELECT DISTINCT(a.`type_id`) FROM `testmaster` a, `patient_test_details` b WHERE a.`category_id`>1 AND a.`type_id`>0 AND a.`testid`=b.`testid` ");
						while($test_dept=mysqli_fetch_array($test_dept_qry))
						{
							$test_name=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `test_department` WHERE `id`='$test_dept[type_id]' "));
							echo "<option value='$test_dept[type_id]'>$test_name[name]</option>";
						}
						
						//~ $head_qry=mysqli_query($GLOBALS["___mysqli_ston"], " select distinct type_id,type_name from testmaster where type_id!=0 order by type_name ");
						//~ while($head=mysqli_fetch_array($head_qry))
						//~ {
							//~ echo "<option value='$head[type_id]'>$head[type_name]</option>";
						//~ }
					?>
					</select>
					<select id="encounter" onChange="view_all('view')" class="span2" style="display:;">
						<option value="0">All Visit Type</option>
					<?php
						$qq_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `status`='0' AND `p_type_id`!=1 ORDER BY `p_type_id` ");
						while($qq=mysqli_fetch_array($qq_qry))
						{
							echo "<option value='$qq[p_type_id]'>$qq[p_type]</option>";
						}
					?>
					</select>
					<select id="branch_id" class="span2" style="<?php echo $branch_display; ?>" onChange="view_all('view')">
					<?php
						$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while($branch=mysqli_fetch_array($branch_qry))
						{
							if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
					?>
					</select>
					<br>
					<!--<button class="btn btn-success" onClick="view_all('old_view')">Old View</button>-->
					<button class="btn btn-search" onClick="view_all('view')"><i class="icon-search"></i> View</button>
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
		$("#refbydoctorid").select2({ theme: "classic" });
	});
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/ref_doc_test_reports_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
			refbydoctorid:$("#refbydoctorid").val(),
			head_id:$("#head_id").val(),
			encounter:$("#encounter").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function print_page(dt1,dt2,doc,dept,encounter,type,branch_id)
	{
		url="pages/ref_doc_test_list_print.php?fdate="+dt1+"&tdate="+dt2+"&doc="+doc+"&dept="+dept+"&encounter="+encounter+"&type="+type+"&branch_id="+branch_id;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function print_doc_pat(el)
	{
		$("#excel_btn_hide").hide();
		var restorepage = $('body').html();
		var printcontent = $('#' + el).clone();
		$('body').empty().html(printcontent);
		window.print();
		$('body').html(restorepage);
		
		/*var disp_setting="toolbar=yes,location=no,";
		disp_setting+="directories=yes,menubar=yes,";
		disp_setting+="scrollbars=yes,width=650, height=600, left=100, top=25";
		var content_vlue = document.getElementById(el).innerHTML;
		var docprint=window.open("","",disp_setting);
		docprint.document.open();
		docprint.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"');
		docprint.document.write('"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
		docprint.document.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">');
		docprint.document.write('<head><title>My Title</title>');
		docprint.document.write('<style type="text/css">body{ margin:0px;');
		docprint.document.write('font-family:verdana,Arial;color:#000;');
		docprint.document.write('font-family:Verdana, Geneva, sans-serif; font-size:12px;}');
		docprint.document.write('a{color:#000;text-decoration:none;} </style>');
		docprint.document.write('</head><body onLoad="self.print()"><center>');
		docprint.document.write(content_vlue);
		docprint.document.write('</center></body></html>');
		docprint.document.close();
		docprint.focus();
		*/
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
.select2-dropdown
{
	z-index:99999 !important;
}
.select2
{
	margin-bottom: 1%;
}
</style>
