<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Doctor Payment</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span11">
			<table class="table table-bordered text-center">
				<tr>
					<td>
						<center>
							<b>From</b>
							<input class="form-control datepicker" type="text" name="fdate" id="fdate" value="<?php echo date("Y-m-d"); ?>" />
							<b>To</b>
							<input class="form-control datepicker" type="text" name="tdate" id="tdate" value="<?php echo date("Y-m-d"); ?>" />
						</center>
					</td>
				</tr>
				<tr>
					<td>
						<center>
							<button class="btn btn-search"  id="ser" name="ser" onClick="load_data()"><i class="icon-search"></i> Search</button>
						</center>
					</td>
				</tr>
			</table>
		</div>
		<div class="span11">
			<div id="doc_det"></div>
		</div>
	</div>
</div>
<script src="include/js/jquery-ui.js"></script>
<script src="include/jquery.ui.timepicker.js"></script><!-- Timepicker js -->
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css" /><!-- Timepicker css -->
<script>
	$(document).ready(function()
	{
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		load_data();
	});
	function load_data()
	{
		$.post("pages/pat_cons_doc_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			user:$("#user").text().trim(),
			type:1
		},
		function(data,status)
		{
			//alert(data);
			$("#doc_det").html(data);
		})	
	}
	
	function select_all(val,doc)
	{
		if($(".doc_pay"+doc).length>0)
		{
			if(val=="Select All")
			{
				$(".doc_pay"+doc).prop("checked",true);
				$("#sel_all"+doc).val("De-Select All");
				$("#make_payment"+doc).prop("disabled",false);
			}
			else
			{
				$(".doc_pay"+doc).prop("checked",false);
				$("#sel_all"+doc).val("Select All");
				$("#make_payment"+doc).prop("disabled",true);
			}
		}
	}
	function select_one(doc)
	{
		if($(".doc_pay"+doc+":checked").length>0)
		{
			$("#make_payment"+doc).prop("disabled",false);
		}
		else
		{
			$("#make_payment"+doc).prop("disabled",true);
		}
		var all=$(".doc_pay"+doc).length;
		var chk=$(".doc_pay"+doc+":checked").length;
		if(all==chk)
		{
			$("#sel_all"+doc).val("De-Select All");
		}
		else
		{
			$("#sel_all"+doc).val("Select All");
		}
	}
	function make_payment(dct)
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to pay?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-success",
					callback: function()
					{
						$("#sel_all"+dct).attr("disabled",true);
						$("#make_payment"+dct).attr("disabled",true);
						var chk=$(".doc_pay"+dct+":checked");
						var pay_string="";
						var vl="";
						for(var i=0;i<chk.length;i++)
						{
							vl=$(chk[i]).attr("id");
							var pid=$(".doc_tr"+dct+":eq("+vl+")").find('td:eq(0) input[type="hidden"]:eq(0)').val();
							var vis=$(".doc_tr"+dct+":eq("+vl+")").find('td:eq(0) input[type="hidden"]:eq(1)').val();
							var rate=$(".doc_tr"+dct+":eq("+vl+")").find('td:eq(0) input[type="hidden"]:eq(2)').val();
							pay_string=pay_string+pid+"#$#"+vis+"#$#"+rate+"#$#govinda";
							//alert(vl);
						}
						$.post("pages/pat_cons_doc_ajax.php",
						{
							pay_st:pay_string,
							user:$("#user").text().trim(),
							dct:dct,
							type:2
						},
						function(data,status)
						{
							load_data();
						})
					}
				}
			}
		});
	}
	function payment_summary()
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		var user=$("#user").text();
		var level=$("#lavel_id").val();
		
		var url="pages/pat_cons_doc_summary.php?fdate="+fdate+"&tdate="+tdate+"&user="+user+"&level="+level;
		//var wid=window.open(url,'','fullscreen=yes,scrollbars=yes');
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
		
	}
	function display_doc(val)
	{
		$(".doctor_details").slideUp(300);
		if(val>0)
		{
			$("#doc_"+val+"").slideDown(300);
		}
		else
		{
			$(".doctor_details").slideDown(300);
		}
	}
	function display_user(val)
	{
		$(".user_details").slideUp(300);
		if(val>0)
		{
			$("#usr_"+val+"").slideDown(300);
		}
		else
		{
			$(".user_details").slideDown(300);
		}
	}
</script>
