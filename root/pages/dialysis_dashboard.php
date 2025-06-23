<?php
$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);
$pat_type_str=base64_decode($_GET['pat_type_str']);
?>
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		 <tr>
			 <td class=""><label for="pin1">UHID</label>
				<input list="browsrs" type="text" name="pin1" id="uhid" class="" value="<?php echo $uhid_str; ?>" onKeyup="search_patient_list(1)" autofocus />
				<datalist id="browsrs">
				<?php
					//~ $pid = mysqli_query($link," SELECT `patient_id` FROM `patient_info` WHERE `patient_id` in (SELECT `patient_id` FROM `uhid_and_opdid` WHERE `type`='3') order by `slno` DESC limit 0,100 ");
					//~ while($pat_uid=mysqli_fetch_array($pid))
					//~ {
						//~ echo "<option value='$pat_uid[patient_id]'>";
					//~ }
				?>
				</datalist>
			</td>
			 <th class=""><label for="pin2">OPD ID</label>
				<input list="browsr" type="text" name="pin2" id="ipd" class="" value="<?php echo $pin_str; ?>" onKeyup="search_patient_list(1)" />
				<datalist id="browsr">
				<?php
					//~ $oid= mysqli_query($link," SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='3' order by `slno` DESC limit 0,100 ");
					//~ while($pat_oid=mysqli_fetch_array($oid))
					//~ {
						//~ echo "<option value='$pat_oid[opd_id]'>";
					//~ }
				?>
				</datalist>
			</th>
			<th class=""><label for="pin3">Name:</label>
				<input type="text" name="pin3" id="name" class="pin" value="<?php echo $name_str; ?>" onKeyup="search_patient_list(1)" />
			</th>
			<th><label for="pin2">Phone Number:</label>
				<input type="text" name="pin2" id="phone" class="pin" value="<?php echo $phone_str; ?>" onKeyup="search_patient_list(1)" >
			</th>
		</tr>
		<tr>
			<th style="text-align:center" colspan="5">Date
				From: <input type="text" name="pin4" id="fdate" class="pin input-group datepicker span2" value="<?php echo $fdate_str; ?>" />
				To: <input type="text" name="pin5" id="tdate" class="pin input-group datepicker span2" value="<?php echo $tdate_str; ?>" />
			</th>
		</tr>
		<tr>
			<td colspan="5" style="text-align:center">
				<input type="button"  name="search" id="srch" value="Search" class="btn btn-info" onclick="search_patient_list(1)"/>
			</th>
		  </tr>
	</table>
	<div id="res" class="ScrollStyle">
	
	</div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
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
		search_patient_list(1);
	});
	function search_patient_list(ser_typ)
	{
		$.post("pages/casualty_dash_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			name:$("#name").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			usr:$("#user").text().trim(),
			ser_typ:ser_typ,
			type:7
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function redirect_page(uhid,ipd)
	{
		var date_str="&param_str=807&fdate_str="+$("#fdate").val()+"&tdate_str="+$("#tdate").val()+"&uhid_str="+$("#uhid").val()+"&pin_str="+$("#ipd").val()+"&name_str="+$("#name").val()+"&phone_str="+$("#phone").val();
		
		window.location="processing.php?param=806&uhid="+uhid+"&ipd="+ipd+date_str;
	}
</script>

