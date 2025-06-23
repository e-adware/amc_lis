<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Data Delete</span></div>
</div>
<!--End-header-->
<div class="container-fluid" onkeypress="anykey_press(event)">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<b>From</b><!-- <?php echo date("Y-m-d"); ?> -->
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
					<br>
					
					<input type="button" name="button" id="searchh" class="btn btn-info" value="Search" onclick="search()" />
				</center>
			</td>
		</tr>
	</table>
	<span id="tot_paid_amt"></span>
	<div id="data_load" class="ScrollStyle"></div>
</div>
<input type="hidden" id="mod_chk" value="0"/>
<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius: 0px; display: block;width: 90%;margin-left: -45%;">
	<div class="modal-dialog" >
		<div class="modal-content">
			<div class="modal-body">
				<div id="results"> </div>
			</div>
		</div>
	</div>
</div>
<div id="loader" style="margin-top:0%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
	});
	function search()
	{
		$.post("pages/data_delete_data.php",
		{
			type:"show_pat",
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			$("#data_load").html(data);
			load_tot_paid_amount();
		})
	}
	function show_test_details(pid,opd)
	{
		//alert(pid+' '+opd);
		$.post("pages/data_delete_data.php"	,
		{
			type:"load_pat_details",
			pid:pid,
			opd:opd,
		},
		function(data,status)
		{
			$("#mod").click();
			$("#mod_chk").val("1");
			$("#results").show().html(data);
		})
	}
	function delete_sel_test(pid,opd)
	{
		//alert(pid+' '+opd);
		var test_str="";
		var chk=$(".test:checked");
		if(chk.length>0)
		{
			for(var i=0;i<chk.length;i++)
			{
				test_str=test_str+"@#"+$("#test"+$(chk[i]).val()+"").val();
			}
			bootbox.dialog({
				//title: "Patient Re-visit ?",
				message: "<h5>Are you sure want to delete this test</h5>",
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
						className: "btn btn-danger",
						callback: function() {
							$.post("pages/data_delete_data.php"	,
							{
								type:"delete_selected_tests",
								pid:pid,
								opd:opd,
								test_str:test_str,
								user:$("#user").text().trim(),
							},
							function(data,status)
							{
								if(data=="1")
								{
									bootbox.dialog({ message: "<h5>Deleted</h5>"});
									setTimeout(function(){
										bootbox.hideAll();
										$("#mod").click();
									},1000);
									$("#searchh").click();
								}else
								{
									bootbox.dialog({ message: data});
									setTimeout(function(){
										bootbox.hideAll();
									},1000);
								}
							})
						}
					}
				}
			});
		}else
		{
			bootbox.dialog({ message: "<h5>Select test</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
			},1000);
		}
	}
	function load_tot_paid_amount()
	{
		$.post("pages/data_delete_data.php"	,
		{
			type:"load_tot_paid_amount",
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			$("#tot_paid_amt").html("<b>Total received amount: "+data+"</b>");
		})
	}
	function hid_div(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode=="27")
		{
			$("#mod").click();
		}
	}
</script>
<style>
.modal.fade.in
{
	top: 2%;
}
</style>
