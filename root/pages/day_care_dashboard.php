<?php
// Check patients from last month
$date2 = date('Y-m-d');
$date1 = date('Y-m-d', strtotime(date("Y-m-d", strtotime($date2)) . " -3 months"));
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		 <tr>
			 <td class=""><label for="pin1">UHID</label>
				<input list="browsrs" type="text" name="pin1" id="uhid" class="span2"  autofocus />
				<datalist id="browsrs">
				<?php
					$pid = mysqli_query($link," SELECT DISTINCT(`patient_id`) FROM `uhid_and_opdid` WHERE `type`='5' AND `date` BETWEEN '$date1' AND '$date2' ORDER BY `slno` DESC ");
					while($pat_uid=mysqli_fetch_array($pid))
					{
						echo "<option value='$pat_uid[patient_id]'>";
					}
				?>
				</datalist>
			</td>
			 <th class=""><label for="pin2">IPD ID</label>
				<input list="browsr" type="text" name="pin2" id="ipd" class="span2" />
				<datalist id="browsr">
				<?php
					$oid= mysqli_query($link," SELECT DISTINCT(`opd_id`) FROM `uhid_and_opdid` WHERE `type`='5' AND `date` BETWEEN '$date1' AND '$date2' ORDER BY `slno` DESC ");
					while($pat_oid=mysqli_fetch_array($oid))
					{
						echo "<option value='$pat_oid[opd_id]'>";
					}
				?>
				</datalist>
			</th>
			 <th class="" style="display:none;"><label for="pin2">IPD Serial</label>
				<input list="browsrr" type="text" name="pin3" id="ipd_serial" class="span2" />
				<datalist id="browsrr">
				<?php
					$ipd_serial_qry= mysqli_query($link," SELECT DISTINCT(`ipd_serial`) FROM `uhid_and_opdid` WHERE `type`='5' AND `date` BETWEEN '$date1' AND '$date2' ORDER BY `slno` DESC ");
					while($ipd_serial=mysqli_fetch_array($ipd_serial_qry))
					{
						echo "<option value='$ipd_serial[ipd_serial]'>";
					}
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
				From: <input type="text" name="pin4" id="fdate" class="pin input-group datepicker span2" onChange="checkup_date()" />
				To: <input type="text" name="pin5" id="tdate" class="pin input-group datepicker span2" onChange="checkup_date()" />
			</th>
		</tr>
		<tr>
			<td colspan="5" style="text-align:center">
				<input type="button"  name="search" id="srch" value="Search" class="btn btn-info" onclick="search_patient_list(1)"/>
			</th>
		  </tr>
	</table>
	<div id="res">
	
	</div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		search_patient_list(0);
	});
	function search_patient_list(ser_typ)
	{
		$.post("pages/day_care_dash_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			ipd_serial:$("#ipd_serial").val(),
			name:$("#name").val(),
			phone:$("#phone").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			usr:$("#user").text().trim(),
			ser_typ:ser_typ,
			type:1
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function redirect_page(uhid,ipd)
	{
		window.location="processing.php?param=124&uhid="+uhid+"&ipd="+ipd;
	}
</script>
