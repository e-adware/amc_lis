<!--header-->
<div id="content-header">
    <div class="header_div">
		<span class="header">Admit Patient List</span>
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table">
		 <tr>
			 <th class=""><label for="pin1">UHID:</label></th>
			<td class="">
				<input list="browsrs" type="text" name="pin1" id="pin1" class="" style="width:100px;" autofocus />
				<datalist id="browsrs">
				<?php
					//$pid = mysqli_query($link," SELECT `patient_id` FROM `patient_info` order by `slno` DESC");
					//$pid = mysqli_query($link," SELECT `patient_id` FROM `patient_info` WHERE `patient_id` IN (SELECT `patient_id` FROM `ipd_pat_details` order by `slno` DESC)");
					$pid = mysqli_query($link," SELECT `patient_id` FROM `pat_disposition` where `disposition`='1' and `ref_opd`='' order by `slno` DESC");
					while($pat_uid=mysqli_fetch_array($pid))
					{
						echo "<option value='$pat_uid[patient_id]'>";
					}
				?>
				</datalist>
			</td>
			 <th class=""><label for="pin2">PIN:</label></th>
			<td class="">
				<input list="browsr" type="text" name="pin2" id="pin2" class="" style="width:100px;" onkeyup="" />
				<datalist id="browsr">
				<?php
					//$oid= mysqli_query($link," SELECT `ipd_id` FROM `ipd_pat_details` order by `slno` DESC");
					$oid= mysqli_query($link,"SELECT `opd_id` FROM `pat_disposition` where `disposition`='1' and `ref_opd`='' order by `slno` DESC");
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
	<div id="pat_list">
	
	</div>
</div>

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
<!--
.alert_msg
{
	position: absolute;
	top: 20%;
	left: 40%;
	color: green;
}-->
#myModal
{
	left: 25%;
	width:90%;
}
.modal.fade.in
{
	top: 3%;
}
.modal-body
{
	max-height: 500px;
}
</style>
<script>
	$(document).ready(function()
	{
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			minDate: '-10',
			maxDate: '0',
		});
		load_list();
		//load_ipd_pat_list();
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
			//$(this).val($(this).val().toUpperCase());
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
		$.post("pages/admit_req_list_ajax.php",
		{
			ward:0,
			uhid:$("#pin1").val(),
			ipd:$("#pin2").val(),
			name:$("#pin3").val(),
			dat:$("#pin4").val(),
			usr:$("#user").text().trim(),
			type:"search_patient_list_ipd",
		},
		function(data,status)
		{
			//alert(data);
			$("#pat_list").html(data);
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
	function load_list()
	{
		$.post("pages/ipd_reg_ajax.php",
		{
			type:"search_patient_list_ipd",
			usr:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#pat_list").html(data);
			search_patient_list();
		})
	}
	
	function redirect_page(uhid,ipd)
	{
		window.location="processing.php?param=126&uhid="+uhid+"&ipd="+ipd;
	}
	
	function reg_alr(uhid,ipd,sh)
	{
		$.post("pages/ipd_reg_ajax.php",
		{
			regtl:uhid,
			type:"load_patient_id",
		},
		function(data,status)
		{
			redirect_page(data,ipd,sh);
		})
	}
	
	function to_ipd_dashboard()
	{
		var uhid=$("#patient_id").val().trim();
		var ipd_id_dash=$("#ipd_id_dash").val().trim();
		//alert(ipd_id_dash);
		window.location="processing.php?param=52&uhid="+uhid+"&ipd="+ipd_id_dash;
	}
</script>
