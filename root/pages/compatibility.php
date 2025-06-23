<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Compatibility &amp; Crossmatch</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-left:0px;">
		<table class="table table-condensed">
			<tr>
				<td>UHID:<br/>
					<input type="text" list="browsrs" id="uhid" placeholder="Patient Number" autofocus />
					<datalist id="browsrs">
					<?php
						$pid = mysqli_query($link,"SELECT `uhid` FROM `patient_info` WHERE `patient_id` IN (SELECT DISTINCT `patient_id` FROM `blood_request` order by `request_id` DESC)");
						while($pat_uid=mysqli_fetch_array($pid))
						{
							echo "<option value='$pat_uid[uhid]'>";
						}
					?>
					</datalist>
					<input type="button" id="pid" class="btn btn-info" onclick="show_pat()" value="Search" />
				</td>
				<td>Bar Code:<br/>
					<input type="text" list="bars" id="bar" placeholder="Bar Code Number" />
					<datalist id="bars">
					<?php
						$pid = mysqli_query($link," SELECT DISTINCT `bar_code` FROM `blood_component_stock` order by `slno` DESC");
						while($pat_uid=mysqli_fetch_array($pid))
						{
							echo "<option value='$pat_uid[bar_code]'>";
						}
					?>
					</datalist>
					<input type="button" id="bid" class="btn btn-info" onclick="show_donor()" value="Search" />
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;"><input type="button" class="btn btn-info" onclick="compare()" value="Check" /></td>
			</tr>
		</table>
	</div>
	<div class="span6" id="pat_det" style="margin-left:0px;">
		
	</div>
	<div class="span5" id="donor_det" style="">
		
	</div>
	<div id="result">
	
	</div>
</div>
<script>
	function submitt()
	{
		if($("#cross").val()=="0")
		{
			$("#cross").focus();
		}
		else if($("#agg").val()=="0")
		{
			$("#agg").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				bar:$("#bar").val(),
				cross:$("#cross").val(),
				agg:$("#agg").val(),
				usr:$("#user").text().trim(),
				type:"blood_submit_crossmatch",
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					compare();
				}, 1000);
			})
		}
	}
	function compare()
	{
		if($("#uhid").val()=="")
		{
			$("#uhid").focus();
		}
		else if($("#bar").val()=="")
		{
			$("#bar").focus();
		}
		else
		{
			$.post("pages/global_load_g.php",
			{
				uhid:$("#uhid").val(),
				bar:$("#bar").val(),
				type:"blood_compare_details",
			},
			function(data,status)
			{
				$("#result").html(data);
			})
		}
	}
	function show_pat()
	{
		if($("#uhid").val()=="")
		{
			$("#uhid").focus();
		}
		else
		{
			$.post("pages/global_load_g.php",
			{
				uhid:$("#uhid").val(),
				type:"blood_pat_details",
			},
			function(data,status)
			{
				$("#pat_det").html(data);
				$("#result").html('');
			})
		}
	}
	function show_donor()
	{
		if($("#bar").val()=="")
		{
			$("#bar").focus();
		}
		else
		{
			$.post("pages/global_load_g.php",
			{
				bar:$("#bar").val(),
				type:"blood_donor_details",
			},
			function(data,status)
			{
				$("#donor_det").html(data);
				$("#result").html('');
			})
		}
	}
</script>
