<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<td>
				<center>
					<span class="side_name">UHID</span>
					<input list="browsrs" type="text" class="span2" id="uhid" onKeyup="show_up(event)" autofocus >
					<datalist id="browsrs">
					<?php
						$pid = mysqli_query($link," SELECT `patient_id` FROM `patient_info` order by `slno` DESC");
						while($pat_uid=mysqli_fetch_array($pid))
						{
							echo "<option value='$pat_uid[patient_id]'>";
						}
					?>
					</datalist>
					<span class="side_name">Name</span>
					<input type="text" class="span2" id="pat_name" onKeyup="show_up(event)" >
					<button class="btn btn-success" onClick="view_all()" style="margin-bottom: 10px;">Search</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_data" class="ScrollStyle" style="display:none;">
		
	</div>
</div>
<input type="hidden" value="0" id="modal_show">
<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="display:none;">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Modal Header</h4>
			</div>
			<div class="modal-body">
				<div id="results"> </div>
			</div>
			<div class="modal-footer"> <!-- data-dismiss="modal" -->
				<button type="button" class="btn btn-danger" onClick="close_modal()" >Close</button>
			</div>
		</div>
	</div>
</div>
<div id="loader" style="margin-top:-10%;"></div>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
	});
	function show_up(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			view_all();
		}
	}
	function view_all()
	{
		$("#loader").show();
		$.post("pages/all_visit_data.php",
		{
			type:"load_all_pat",
			pat_name:$("#pat_name").val(),
			pat_uhid:$("#uhid").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").slideUp(100,function(){ $("#load_data").html(data).slideDown(100); });
		})
	}
	function load_detail(uhid,pin)
	{
		$.post("pages/all_visit_data.php",
		{
			type:"load_detail",
			uhid:uhid,
			pin:pin,
		},
		function(data,status)
		{
			$("#mod").click();
			$("#results").html(data);
			$("#modal_show").val("1");
		})
	}
	function close_modal()
	{
		$("#mod").click();
		$("#modal_show").val("0");
	}
	function hid_div(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==27)
		{
			if($("#modal_show").val()==1)
			{
				$("#mod").click();
				$("#modal_show").val("0");
			}
		}
	}
</script>
<style>
.side_name
{
	border: 1px solid #ddd;
	background-color: #fff;
	padding: 4px;
	position: absolute;
	font-weight:bold;
}
.select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input
{
	padding-left: 5%;
}
#myModal
{
	left: 25%;
	width:90%;
}
.modal.fade.in {
	top: 0%;
}
.modal-body
{
	max-height: 520px;
}
</style>
