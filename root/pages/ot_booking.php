<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">OT Booking</span>
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
					$pid = mysqli_query($link," SELECT patient_id FROM `uhid_and_opdid`  WHERE `type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='3') order by `slno` DESC LIMIT 0,50");
					
					while($pat_uid=mysqli_fetch_array($pid))
					{
						echo "<option value='$pat_uid[patient_id]'>";
					}
				?>
				</datalist>
			</td>
			 <th class=""><label for="pin2">IPD ID:</label></th>
			<td class="">
				<input list="browsr" type="text" name="pin2" id="pin2" class="" style="width:100px;" />
				<datalist id="browsr">
				<?php
					$oid= mysqli_query($link,"SELECT `opd_id` FROM `uhid_and_opdid`  WHERE `type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`='3') order by `slno` DESC LIMIT 0,50");
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

<!--modal-->
	<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
	<input type="text" id="modtxt" value="0" style="display:none"/>
	<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
				<!--<button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true"><b>x</b></button>-->
					<input type="text" id="ruhid" style="display:none"/>
					<input type="text" id="ripd" style="display:none"/>
					<p>PAC not done. Do you want to continue?</p>
					<p id="reas" style="display:none;">
						Reason <input type="text" id="reason" placeholder="Reason" />
					</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" onclick="redirect_con()">Confirm</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
	<!--modal end-->
	
	
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
		$.post("pages/ot_booking_ajax.php",
		{
			ward:$("#ward").val(),
			uhid:$("#pin1").val(),
			ipd:$("#pin2").val(),
			name:$("#pin3").val(),
			dat:$("#pin4").val(),
			usr:$("#user").text().trim(),
			type:"search_patient_list_ipd",
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
	function redirect_con()
	{
		if($("#reason").val().trim()=="")
		{
			$("#reas").slideDown();
			$("#reason").focus();
		}
		else
		{
			$("#mod").click();
			$.post("pages/ot_scheduling_ajax.php",
			{
				uhid:$("#ruhid").val(),
				ipd:$("#ripd").val(),
				reason:$("#reason").val().trim(),
				usr:$("#user").text().trim(),
				type:"ot_schedule_reason",
			},
			function(data,status)
			{
				window.location="processing.php?param=214&uhid="+$('#ruhid').val()+"&ipd="+$('#ripd').val();
			})
		}
	}
	function redirect_page(uhid,ipd,st,rn)
	{
		bootbox.dialog({ message: "<span id='discharge_text'><b>Redirecting...</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> </span>",closeButton: false});
		setTimeout(function()
		{
			window.location="processing.php?param=240&uhid="+uhid+"&ipd="+ipd;
		},800);
		/*if(st==0 && rn==0)
		{
			$("#mod").click();
			$("#reason").val('');
			$("#ruhid").val(uhid);
			$("#ripd").val(ipd);
			$("#reas").hide();
			/*bootbox.dialog({ message: "PAC Not Done"});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);//
		}
		else if(st==0 && rn==1)
		{
			window.location="processing.php?param=214&uhid="+uhid+"&ipd="+ipd;
		}
		else if(st==1 && rn==1)
		{
			window.location="processing.php?param=214&uhid="+uhid+"&ipd="+ipd;
		}
		else if(st==1 && rn==0)
		{
			window.location="processing.php?param=214&uhid="+uhid+"&ipd="+ipd;
		}
		*/
	}
</script>
