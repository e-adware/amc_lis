<?php
// Check patients from last month
$date2 = date('Y-m-d');
$date1 = date('Y-m-d', strtotime(date("Y-m-d", strtotime($date2)) . " -3 months"));

$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);
$pat_type_str=base64_decode($_GET['pat_type_str']);

$user_change_disabled="disabled";
if($p_info["levelid"]==1)
{
	$user_change_disabled="";
}
if($p_info["levelid"]==1)
{
	$branch_str="";
	$branch_display="display:none;";
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
}
//~ $branch_str=" AND branch_id='2'";
//~ $branch_display="display:none;";

$branch_id=$p_info["branch_id"];

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed" id="ipd_ser">
		 <tr>
			 <td style="<?php echo $branch_display; ?>"><label for="pin1"> &nbsp;</label>
				<select id="branch_id" class="" onchange="load_room()" style="<?php echo $branch_display; ?>">
				<?php
					$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
					while($branch=mysqli_fetch_array($branch_qry))
					{
						if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
						echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
					}
				?>
				</select>
			 </td>
			 <td class=""><label for="pin1">UNIT NO.</label>
				<input list="browsrs" type="text" name="pin1" id="uhid" class="span2" onkeyup="search_patient_list(1)" value="<?php echo $uhid_str; ?>" autofocus />
				<datalist id="browsrs">
				<?php
					//~ $pid = mysqli_query($link," SELECT DISTINCT(`patient_id`) FROM `uhid_and_opdid` WHERE `type`='3' AND `date` BETWEEN '$date1' AND '$date2' ORDER BY `slno` DESC ");
					//~ while($pat_uid=mysqli_fetch_array($pid))
					//~ {
						//~ echo "<option value='$pat_uid[patient_id]'>";
					//~ }
				?>
				</datalist>
			</td>
			 <th class=""><label for="pin2">Bill No.</label>
				<input list="browsr" type="text" name="pin2" id="ipd" onkeyup="search_patient_list(1)" class="span2" value="<?php echo $pin_str; ?>" />
				<datalist id="browsr">
				<?php
					//~ $oid= mysqli_query($link," SELECT DISTINCT(`opd_id`) FROM `uhid_and_opdid` WHERE `type`='3' AND `date` BETWEEN '$date1' AND '$date2' ORDER BY `slno` DESC ");
					//~ while($pat_oid=mysqli_fetch_array($oid))
					//~ {
						//~ echo "<option value='$pat_oid[opd_id]'>";
					//~ }
				?>
				</datalist>
			</th>
			 <th class="" style="display:none;"><label for="pin2">IPD Serial</label>
				<input list="browsrr" type="text" name="pin3" id="ipd_serial" class="span2" />
				<datalist id="browsrr">
				<?php
					//~ $ipd_serial_qry= mysqli_query($link," SELECT DISTINCT(`ipd_serial`) FROM `uhid_and_opdid` WHERE `type`='3' AND `date` BETWEEN '$date1' AND '$date2' ORDER BY `slno` DESC ");
					//~ while($ipd_serial=mysqli_fetch_array($ipd_serial_qry))
					//~ {
						//~ echo "<option value='$ipd_serial[ipd_serial]'>";
					//~ }
				?>
				</datalist>
			</th>
			<th class=""><label for="pin3">Name:</label>
				<input type="text" name="pin3" id="name" class="pin" onkeyup="search_patient_list(1)" value="<?php echo $name_str; ?>" />
			</th>
			<th><label for="pin2">Phone Number:</label>
				<input type="text" name="pin2" id="phone" class="pin" onkeyup="search_patient_list(1)" value="<?php echo $phone_str; ?>" >
			</th>
		</tr>
		
			<th style="text-align:center" colspan="5">Date
				From: <input type="text" name="pin4" id="fdate" class="pin input-group datepicker span2" onChange="checkup_date()" value="<?php echo $fdate_str; ?>" />
				To: <input type="text" name="pin5" id="tdate" class="pin input-group datepicker span2" onChange="checkup_date()" value="<?php echo $tdate_str; ?>" />
			</th>
		</tr>
		<tr>
			<td colspan="5" style="text-align:center">
				<input type="button"  name="search" id="srch" value="Search" class="btn btn-info" onclick="search_patient_list(1)"/>
			</th>
		  </tr>
	</table>
	<div id="load_all" class="ScrollStyle">
	
	</div>
</div>
<input type="hidden" id="list_start" value="50">
<!-- Loader -->
<div id="loader" style="margin-top:0%;"></div>
<link rel="stylesheet" href="../css/loader.css" />

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
@media print
{
	#header ,#search,.modal,#sidebar,#search_test {display:none;} 
	table tr td,table tr td{ font-size:10px;}
	.head{font-size:12px;}
	#search_data{ margin-left:-50px;}
	#ipd_ser{display:none;}
	#user-nav{display:none;}
	#res{display:block;}
}
</style>
<script>
	$(document).ready(function()
	{
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		$('#load_all').on('scroll', function() {
			var div_height = $(this).get(0).scrollHeight;
			var div = $(this).get(0);
			
			if(div.scrollTop + div.clientHeight >= div.scrollHeight) {
				var list_start=$("#list_start").val().trim();
				list_start=parseInt(list_start)+50;
				$("#list_start").val(list_start);
				search_patient_list(1);
			}
		});
		search_patient_list(1);
	});
	function search_patient_list(ser_typ)
	{
		$("#loader").show();
		$.post("pages/ipd_dash_ajax.php",
		{
			branch_id:$("#branch_id").val(),
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			ipd_serial:$("#ipd_serial").val(),
			name:$("#name").val(),
			phone:$("#phone").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			list_start:$("#list_start").val(),
			usr:$("#user").text().trim(),
			ser_typ:ser_typ,
			type:1
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").html(data);
		})
	}
	function redirect_page(uhid,ipd)
	{
		var date_str="&param_str=51&fdate_str="+$("#fdate").val()+"&tdate_str="+$("#tdate").val()+"&uhid_str="+$("#uhid").val()+"&pin_str="+$("#ipd").val()+"&name_str="+$("#name").val()+"&phone_str="+$("#phone").val();
		
		window.location="processing.php?param=52&uhid="+uhid+"&ipd="+ipd+date_str;
	}
</script>
<style>
.ScrollStyle
{
    max-height: 600px;
    overflow-y: scroll;
}
</style>
