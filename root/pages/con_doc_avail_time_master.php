<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Consultant Doctor Available Time</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table" style="margin-bottom:0px;background:snow;">
		<tr>
			<th class="span2">Select Doctor</th>
			<td>
				<select id="con_doc_id" onChange="con_doc_sel()" autofocus>
					<option value="0">Select</option>
			<?php
				$con_doc_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` order by `consultantdoctorid` DESC ");
				while($con_doc=mysqli_fetch_array($con_doc_qry))
				{
					echo "<option value='$con_doc[consultantdoctorid]'>$con_doc[Name]</option>";
				}
			?>
				</select>
			</td>
		</tr>
	</table>
	<div id="load_all">
		
	</div>
</div>
<!-- Time Picker -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
<script type="text/javascript" src="include/ui-1.10.0/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="include/jquery.ui.timepicker.js?v=0.3.3"></script>
<script>
	function con_doc_sel()
	{
		$.post("pages/global_load.php",
		{
			type:"con_doc_average_time",
			con_doc_id:$("#con_doc_id").val(),
		},
		function(data,status)
		{
			$("#load_all").html(data);
			$("#average_time").focus();
			days('1');
		})
	}
	function average_time(val,e)
	{
		$("#average_time").css({'border-color': 'rgba(82,168,236,0.8)'});
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if(val>0)
			{
				$("#average_time").css({'border-color': '#CCC'});
				$("#save").focus();
			}else
			{
				$("#average_time").css({'border-color': '#F00'});
			}
		}
		var numex=/^[0-9]+$/;
		if(val.match(numex))
		{
			$("#average_time").val(val);
		}else
		{
			$("#average_time").val("");
		}
	}
	function save_average_time()
	{
		var avt=$("#average_time").val();
		if(avt>0)
		{
			$.post("pages/global_insert_data.php",
			{
				type:"average_time_per_patient",
				con_doc_id:$("#con_doc_id").val(),
				average_time:avt,
				user:$("#user").text(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: "Saved"});
				setTimeout(function(){
					bootbox.hideAll();
					con_doc_sel();
					days('1');
				 }, 1000);
			})
		}else
		{
			$("#average_time").css({'border-color': '#F00'}).focus();
		}
	}
	function days(val)
	{
		$.post("pages/global_load.php",
		{
			type:"average_time_picker",
			day:val,
			con_doc_id:$("#con_doc_id").val(),
		},
		function(data,status)
		{
			$("#tab").html(data);
			$('.timepicker').timepicker();
			$(".right_click").on("contextmenu",function(e){
				return false;
			});
			// Disabled drop
			 $('.right_click').on('drop', function() {
				return false;
			  });
		})
	}
	function add_more_session(this_day)
	{
		var i=($(".tmp"+this_day).length)/2;
		for(var k=0;k<i;k++)
		{
			if($("#start_time"+this_day+i).val()=="")
			{
				$("#start_time"+this_day+i).focus();
				return false;
			}
			if($("#end_time"+this_day+i).val()=="")
			{
				$("#end_time"+this_day+i).focus();
				return false;
			}
		}
		i++;
		for(var j=2;j<i;j++)
		{
			$(".del"+this_day+j).hide();
		}
		$('#time_range_table').append('<tr id="row'+this_day+i+'"><th style="width:9%;">Session '+i+'</th><td><input type="text" class="timepicker tmp'+this_day+' span1" id="start_time'+this_day+i+'"> <input type="text" class="timepicker tmp'+this_day+' span1" id="end_time'+this_day+i+'"> <button class="btn btn-danger del'+this_day+i+'" id="'+i+'" onClick="del_session(this.id)" style="margin-bottom:1%;"><i class="icon-remove"></i></button></td></tr>');
		$('.timepicker').timepicker();
	}
	function del_session(id)
	{
		var this_day=$("#this_day").val();
		var end_time=$("#end_time"+this_day+id).val();
		if(end_time=="")
		{
			$("#row"+this_day+id).remove();
			var j=(id-1);
			$(".del"+this_day+j).show();
		}else
		{
			bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-info",
					callback: function() {
						$("#row"+this_day+id).remove();
						save_time_range(this_day);
					}
				}
			}
		});
			/*
			if(confirm("Are you sure want to delete") == true)
			{
				$("#row"+this_day+id).remove();
				save_time_range(this_day);
			}*/
		}
	}
	function save_time_range(this_day)
	{
		var str="";
		var len=(($(".tmp"+this_day).length)/2)+1;
		for(var i=1;i<len;i++)
		{
			var start=$("#start_time"+this_day+i).val();
			var end=$("#end_time"+this_day+i).val();
			if(start=="")
			{
				$("#start_time"+this_day+i).focus();
				return false;
			}
			if(end=="")
			{
				$("#end_time"+this_day+i).focus();
				return false;
			}
			str=str+"##"+start+"@@"+end;
		}
		$.post("pages/global_insert_data.php",
		{
			type:"save_con_doc_time_range",
			con_doc_id:$("#con_doc_id").val(),
			this_day:this_day,
			str:str,
			user:$("#user").text(),
		},
		function(data,status)
		{
			//alert("Saved");
			bootbox.dialog({ message: "Saved"});
			setTimeout(function(){
				bootbox.hideAll();
				days(data);
			 }, 1000);
		})
	}
	function clear_input(id)
	{
		$("#"+id).val("");
	}
</script>
