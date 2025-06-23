<?php
// Check patients from last month
$date2 = date('Y-m-d');
$date1 = date('Y-m-d', strtotime(date("Y-m-d", strtotime($date2)) . " -3 months"));
?>
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		 <tr>
			 <td class=""><label for="pin1">Unit No.</label>
				<input list="browsrs" type="text" name="pin1" id="uhid" onkeyup="search_patient_list(1)" class="" autofocus />
				<datalist id="browsrs">
				<?php
					//~ $pid = mysqli_query($link," SELECT a.`patient_id` FROM `patient_info` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND b.`type`='8'  AND b.`date` BETWEEN '$date1' AND '$date2' order by a.`slno` DESC");
					//~ while($pat_uid=mysqli_fetch_array($pid))
					//~ {
						//~ echo "<option value='$pat_uid[patient_id]'>";
					//~ }
				?>
				</datalist>
			</td>
			 <th class=""><label for="pin2">BABY ID</label>
				<input list="browsr" type="text" name="pin2" id="ipd" onkeyup="search_patient_list(1)" class="" />
				<datalist id="browsr">
				<?php
					//~ $oid= mysqli_query($link," SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='8' AND `date` BETWEEN '$date1' AND '$date2' order by `slno` DESC");
					//~ while($pat_oid=mysqli_fetch_array($oid))
					//~ {
						//~ echo "<option value='$pat_oid[opd_id]'>";
					//~ }
				?>
				</datalist>
			</th>
			<th class=""><label for="pin3">Name:</label>
			
				<input type="text" name="pin3" id="name" class="pin"  />
			</th>
			<th><label for="pin2">Phone Number:</label>
			
				<input type="text" name="pin2" id="phone" class="pin" >
			</th>
		</tr>
		
			<th style="text-align:center" colspan="5">Date
				From: <input type="text" name="pin4" id="fdate" class="pin input-group datepicker span2" />
				To: <input type="text" name="pin5" id="tdate" class="pin input-group datepicker span2" />
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
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#ipd").focus();
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		search_patient_list();
	});
	function search_patient_list()
	{
		$("#loader").show();
		$.post("pages/baby_dashboard_data.php",
		{
			type:"load_patient_list",
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			name:$("#name").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			usr:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function redirect_page(uhid,ipd)
	{
		window.location="processing.php?param=119&uhid="+uhid+"&ipd="+ipd;
	}
</script>

