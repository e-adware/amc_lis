<?php
$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);
?>
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
			<th class=""><label for="uhid">Unit No:</label></th>
			<td class="">
				<input list="browsrs" type="text" name="uhid" id="uhid" class="" style="width:100px;" onkeyup="search_patient_list()" value="<?php echo $uhid_str; ?>" />
				<datalist id="browsrs">
				<?php
					//~ $pid = mysqli_query($link,"SELECT `patient_id` FROM `ot_book` order by `slno` DESC");
					//~ while($pat_uid=mysqli_fetch_array($pid))
					//~ {
						//~ echo "<option value='$pat_uid[patient_id]'>";
					//~ }
				?>
				</datalist>
			</td>
			 <th class=""><label for="ipd_id">Bill No.:</label></th>
			<td class="">
				<input list="browsr" type="text" name="ipd_id" id="ipd_id" class="" style="width:100px;" onkeyup="search_patient_list()" value="<?php echo $pin_str; ?>" autofocus />
				<datalist id="browsr">
				<?php
					//~ $oid= mysqli_query($link," SELECT `ipd_id` FROM `ot_book` order by `slno` DESC");
					//~ while($pat_oid=mysqli_fetch_array($oid))
					//~ {
						//~ echo "<option value='$pat_oid[ipd_id]'>";
					//~ }
				?>
				</datalist>
			</td>
			<th class=""><label for="pat_name">Patient Name:</label></th>
			<td class="">
				<input type="text" name="pat_name" id="pat_name" class="pat_name" value="<?php echo $name_str; ?>" />
			</td>
			<th class=""><label for="date">Entry Date:</label></th>
			<td class="">
				<input type="text" name="date" id="date" class="pin input-group datepicker span2" onChange="search_patient_list()" value="<?php echo $fdate_str; ?>" />
			</td>
			<td>
				<input type="button"  name="search" id="srch" value="Search" class="btn btn-primary btn-sm" onFocus="search_patient_list()"/>
			</td>
		  </tr>
	</table>
	<div id="load_all" class="ScrollStyle">
	
	</div>
	<input type="hidden" id="list_start" value="50">
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
		$('#load_all').on('scroll', function() {
			var div_height = $(this).get(0).scrollHeight;
			var div = $(this).get(0);
			
			if(div.scrollTop + div.clientHeight >= div.scrollHeight) {
				var list_start=$("#list_start").val().trim();
				list_start=parseInt(list_start)+50;
				$("#list_start").val(list_start);
				search_patient_list();
			}
		});
		search_patient_list();
		$("#uhid").keyup(function(e)
		{
			$("#ipd_id").val('');
			$("#pat_name").val('');
			$("#date").val('');
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#ipd_id").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
		$("#ipd_id").keyup(function(e)
		{
			$("#uhid").val('');
			$("#pat_name").val('');
			$("#date").val('');
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#pat_name").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
		$("#pat_name").keyup(function(e)
		{
			$("#uhid").val('');
			$("#ipd_id").val('');
			$("#date").val('');
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#date").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
		$("#date").keyup(function(e)
		{
			$("#uhid").val('');
			$("#ipd_id").val('');
			$("#pat_name").val('');
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#uhid").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
	});
	function search_patient_list()
	{
		$.post("pages/ot_pat_list_data.php",
		{
			type:"ot_pat_list",
			uhid:$("#uhid").val(),
			ipd:$("#ipd_id").val(),
			name:$("#pat_name").val(),
			dat:$("#date").val(),
			usr:$("#user").text().trim(),
			list_start:$("#list_start").val(),
		},
		function(data,status)
		{
			$("#load_all").html(data);
			if($("#uhid").val()!='')
			{
				$("#uhid").focus();
			}
			if($("#ipd_id").val()!='')
			{
				$("#ipd_id").focus();
			}
			if($("#pat_name").val()!='')
			{
				$("#pat_name").focus();
			}
		})
	}
	function redirect_page(uhid,ipd,schedule_id)
	{
		var param_str="&param_str="+btoa($("#param_id").val())+"&fdate_str="+btoa($("#date").val())+"&tdate_str="+btoa($("#to").val())+"&uhid_str="+btoa($("#uhid").val())+"&pin_str="+btoa($("#ipd_id").val())+"&name_str="+btoa($("#pat_name").val());
		
		window.location="?param="+btoa(214)+"&uhid="+btoa(uhid)+"&ipd="+btoa(ipd)+"&schedule_id="+btoa(schedule_id)+param_str;
		
	}
</script>
<style>
label
{
	font-weight: bold;
}
</style>
