<!--header-->
<div id="content-header" >
    <div class="header_div">
		<span class="header"> <?php echo $menu_info["par_name"]; ?></span>
		<span style="margin-left:40px;">
			<select id="ward" onchange="search_patient_list()" style="margin-bottom:0; display:none">
				<option value="0">All</option>
				<?php
				$q=mysqli_query($link,"SELECT * FROM `ward_master` ORDER BY `name`");
				while($r=mysqli_fetch_array($q))
				{
				?>
				<option value="<?php echo $r['ward_id'];?>"><?php echo $r['name'];?></option>
				<?php
				}
				?>
			</select>
		</span>
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table">
		 <tr>
			 <th class=""><label for="pin1">UNIT NO.:</label></th>
			<td class="">
				<input list="browsrs" type="text" name="pin1" id="pin1" class="" style="width:100px;" onkeyup="search_patient_list()" autofocus />
				<datalist id="browsrs">
				<?php
					//$pid = mysqli_query($link," SELECT `patient_id` FROM `ipd_pat_details` order by `slno` DESC");
					//~ $pid = mysqli_query($link," SELECT `patient_id` FROM `patient_info` WHERE `patient_id` IN (SELECT `patient_id` FROM `uhid_and_opdid` WHERE `type`='3' order by `slno` DESC)");
					//~ while($pat_uid=mysqli_fetch_array($pid))
					//~ {
						//~ echo "<option value='$pat_uid[patient_id]'>";
					//~ }
				?>
				</datalist>
			</td>
			 <th class=""><label for="pin2">IPD ID:</label></th>
			<td class="">
				<input list="browsr" type="text" name="pin2" id="pin2" class="" style="width:100px;" onkeyup="search_patient_list()" />
				<datalist id="browsr">
				<?php
					//~ $oid= mysqli_query($link," SELECT `ipd_id` FROM `ipd_pat_details` order by `slno` DESC");
					//~ while($pat_oid=mysqli_fetch_array($oid))
					//~ {
						//~ echo "<option value='$pat_oid[ipd_id]'>";
					//~ }
				?>
				</datalist>
			</td>
			<th class=""><label for="pin3">Patient Name:</label></th>
			<td class="">
				<input type="text" name="pin3" id="pin3" class="pin" onkeyup="search_patient_list()" />
			</td>
			<!--<th><label for="pin2">Phone Number:</label></th>
			<td>
				<input type="text" name="pin2" id="pin2" class="pin" >
			</td>-->
			<th class=""><label for="pin4">Date:</label></th>
			<td class="">
				<input type="text" name="pin4" id="pin4" style="width:100px;" class="pin input-group datepicker span2" onChange="checkup_date()" />
			</td>
			<td>
				<button class="btn btn-search" onFocus="search_patient_list()"><i class="icon-search"></i> Search</button>
			</td>
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
<script>
	$(document).ready(function()
	{
		$("#pin2").focus();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		search_patient_list();
		$("#pin1").keyup(function(e)
		{
			$("#pin2").val('');
			$("#pin3").val('');
			$("#pin4").val('');
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#pin2").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
		$("#pin2").keyup(function(e)
		{
			$("#pin1").val('');
			$("#pin3").val('');
			$("#pin4").val('');
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#pin3").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
		$("#pin3").keyup(function(e)
		{
			$("#pin1").val('');
			$("#pin2").val('');
			$("#pin4").val('');
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#pin4").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
		$("#pin4").keyup(function(e)
		{
			$("#pin1").val('');
			$("#pin2").val('');
			$("#pin3").val('');
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#pin1").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
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
	});
	function search_patient_list()
	{
		$("#loader").show();
		$.post("pages/nursing_pat_list_data.php",
		{
			ward:$("#ward").val(),
			uhid:$("#pin1").val(),
			ipd:$("#pin2").val(),
			name:$("#pin3").val(),
			dat:$("#pin4").val(),
			usr:$("#user").text().trim(),
			list_start:$("#list_start").val(),
			type:"search_patient_list_ipd",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").html(data);
			if($("#pin1").val()!='')
			{
				$("#pin1").focus();
			}
			if($("#pin2").val()!='')
			{
				$("#pin2").focus();
			}
			if($("#pin3").val()!='')
			{
				$("#pin3").focus();
			}
			// Every 5 second load
			setTimeout(function()
			{
				//search_patient_list();
			}, 5000);
		})
	}
	function redirect_page(uhid,ipd)
	{
		window.location="processing.php?param=37&uhid="+uhid+"&ipd="+ipd;
	}
</script>
<style>
.ScrollStyle
{
    max-height: 600px;
    overflow-y: scroll;
}
</style>
