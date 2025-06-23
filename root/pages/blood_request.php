<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Blood Request</span></div>
</div>
<!--End-header-->
<link rel="stylesheet" href="../css/uniform.css" type="text/css" />
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>UHID:</td>
				<td>
					<input type="text" list="browsrs" id="uhid" placeholder="Patient Number" autofocus />
					<datalist id="browsrs">
					<?php
						$pid = mysqli_query($link,"SELECT `uhid` FROM `patient_info` WHERE `patient_id` IN (SELECT `patient_id` FROM `ipd_pat_details` order by `slno` DESC)");
						while($pat_uid=mysqli_fetch_array($pid))
						{
							echo "<option value='$pat_uid[uhid]'>";
						}
					?>
					</datalist>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input type="button" class="btn btn-info" value="Search" onclick="search()" />
				</td>
			</tr>
		</table>
	</div>
	<div id="result">

	</div>
</div>
<script src="../js/jquery.uniform.js"></script>
<script>
	function save()
	{
		var all="";
		var rbc="";
		var ffp="";
		var plat="";
		var cpp="";
		var cryo="";
		if($("#abo").val()=="0")
		{
			$("#abo").focus();
		}
		else if($("#rh").val()=="0")
		{
			$("#rh").focus();
		}
		else if($("input[type='checkbox']:checked").length==0)//$('.myCheckbox').attr('checked', false);
		{
			$("#chkk").addClass("err");
			setTimeout(function()
			{
				var c = $('#chkk');
				$({alpha:1}).animate({alpha:0},
				{
					duration: 1000,
					step: function()
					{
						c.css('border-color','rgba(240,0,0,'+this.alpha+')');
					}
				});
			},100);
		}
		else if($("#unit").val()=="" || (parseInt($("#unit").val()))==0)
		{
			$("#unit").focus();
		}
		else
		{
			if($("#rbc:checked").length==1)
			{
				rbc="rbc@1@1";
			}
			else
			{
				rbc="";
			}
			if($("#ffp:checked").length==1)
			{
				ffp="ffp@1@2";
			}
			else
			{
				ffp="";
			}
			if($("#plat:checked").length==1)
			{
				plat="plat@1@3";
			}
			else
			{
				plat="";
			}
			if($("#cpp:checked").length==1)
			{
				cpp="cpp@1@4";
			}
			else
			{
				cpp="";
			}
			if($("#cryo:checked").length==1)
			{
				cryo="cryo@1@5";
			}
			else
			{
				cryo="";
			}
			var all=rbc+"#@#"+ffp+"#@#"+plat+"#@#"+cpp+"#@#"+cryo+"#@#";
			//alert(all);
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				abo:$("#abo").val(),
				rh:$("#rh").val(),
				unit:$("#unit").val(),
				usr:$("#user").text().trim(),
				all:all,
				type:"save_blood_request",
			},
			function(data,status)
			{
				alert(data);
				search();
				//$("#result").html(data);
			})
		}
	}
	function search()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			type:"display_patient_details",
		},
		function(data,status)
		{
			$("#result").html(data);
		})
	}
</script>
