<!--header-->
<div id="content-header">
    <div class="header_div">
		<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table">
		 <tr>
			 <th class=""><label for="pin1">UHID:</label></th>
			<td class="">
				<input list="browsrs" type="text" name="pin1" id="pin1" class="" style="width:100px;" onkeyup="search_patient_list()" autofocus />
			</td>
			 <th class=""><label for="pin2">IPD ID:</label></th>
			<td class="">
				<input list="browsr" type="text" name="pin2" id="pin2" class="" style="width:100px;" onkeyup="search_patient_list()" />
			</td>
			<th class=""><label for="pin3">Patient Name:</label></th>
			<td class="">
				<input type="text" name="pin3" id="pin3" class="pin" onkeyup="search_patient_list()" />
			</td>
			<th class=""><label for="pin4">Date:</label></th>
			<td class="">
				<input type="text" name="pin4" id="pin4" class="pin input-group datepicker span2" />
			</td>
			<td>
				<input type="button"  name="search" id="srch" value="Search" class="btn btn-primary btn-sm" onFocus="search_patient_list()"/>
			</td>
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
		search_patient_list();
	});
	function search_patient_list()
	{
		$.post("pages/ipd_package_split_data.php",
		{
			uhid:$("#pin1").val(),
			ipd:$("#pin2").val(),
			name:$("#pin3").val(),
			dat:$("#pin4").val(),
			usr:$("#user").text().trim(),
			type:1,
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function redirect_page(uhid,ipd,group_id,service_id)
	{
		//alert(group_id);
		window.location="processing.php?param=852&uhid="+uhid+"&ipd="+ipd+"&group_id="+group_id+"&service_id="+service_id;
	}
</script>
