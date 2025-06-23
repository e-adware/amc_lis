<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Queue List</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table">
		 <tr>
			 <th class=""><label for="pin1">UHID:</label></th>
			<td class="">
				<input list="browsrs" type="text" name="pin1" id="pin1" class="" style="width:100px;" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" autofocus />
				<datalist id="browsrs">
				<?php
					$pid = mysqli_query($link," SELECT `patient_id` FROM `patient_info` order by `slno` DESC");
					while($pat_uid=mysqli_fetch_array($pid))
					{
						echo "<option value='$pat_uid[patient_id]'>";
					}
				?>
				</datalist>
			</td>
			 <th class=""><label for="pin2">OPD ID:</label></th>
			<td class="">
				<input list="browsr" type="text" name="pin2" id="pin2" class="" style="width:100px;" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" />
				<datalist id="browsr">
				<?php
					$oid= mysqli_query($link," SELECT `opd_id` FROM `appointment_book` order by `slno` DESC");
					while($pat_oid=mysqli_fetch_array($oid))
					{
						echo "<option value='$pat_oid[opd_id]'>";
					}
				?>
				</datalist>
			</td>
			<th class=""><label for="pin3">Patient Name:</label></th>
			<td class="">
				<input type="text" name="pin3" id="pin3" class="pin"  />
			</td>
			<!--<th><label for="pin2">Phone Number:</label></th>
			<td>
				<input type="text" name="pin2" id="pin2" class="pin" >
			</td>-->
			<th class=""><label for="pin4">Date:</label></th>
			<td class="">
				<input type="text" name="pin4" id="pin4" class="pin input-group datepicker span2" onChange="checkup_date()" />
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
<script type='text/javascript'>
	$(document).ready(function()
	{
		$(".datepicker").datepicker({
			dateFormat: 'dd-mm-yy',
			maxDate: '0',
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
	});
	function search_patient_list()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#pin1").val(),
			opd:$("#pin2").val(),
			name:$("#pin3").val(),
			dat:$("#pin4").val(),
			usr:$("#user").text(),
			type:"search_patient_list",
		},
		function(data,status)
		{
			$("#res").html(data);
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
				search_patient_list();
			}, 5000);
		})
	}
	function redirect_page(pid,opd)
	{
		window.location="processing.php?param=16&uhid="+pid+"&opd="+opd;
	}
	function checkup_date()
	{
		//$("#pin1").val('');
		//$("#pin4").val('');
	}
</script>
