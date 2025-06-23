<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Payment Setup</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div id="load_data">
		
	</div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function(){
		view_timer();
	});
	function view_timer()
	{
		$.post("pages/therapy_detail_master_data.php",
		{
			type:"view_timer",
		},
		function(data,status)
		{
			$("#load_data").html(data);
			$(".datepicker").datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: '0',
			});
		})
	}
	function save_timer(val)
	{
		if($("#p_date"+val).val()=="")
		{
			$("#p_date"+val).focus();
			return false;
		}
		if($("#n_date"+val).val()=="")
		{
			$("#n_date"+val).focus();
			return false;
		}
		if($("#d_date"+val).val()=="")
		{
			$("#d_date"+val).focus();
			return false;
		}
		$.post("pages/therapy_detail_master_data.php",
		{
			type:"save_timer",
			val:val,
			p_date:$("#p_date"+val).val(),
			n_date:$("#n_date"+val).val(),
			d_date:$("#d_date"+val).val(),
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<h5>Saved</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
				view_timer();
			 }, 1000);
		})
	}
	function add_more_timer(slno,num)
	{
		$("#add_more_timer_tr").hide();
		
		var td_num=parseInt(num)+1;
		
		var d = new Date();
		var strDate = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
		
		var slno=0;
		
		$('#pay_timer_tbl').append('<tr><td>'+td_num+'</td><td><input type="text" class="datepicker" id="p_date'+slno+'" value="'+strDate+'"></td><td><input type="text" class="datepicker" id="n_date'+slno+'" value="'+strDate+'"></td><td><input type="text" class="datepicker" id="d_date'+slno+'" value="'+strDate+'"></td><td><button class="btn btn-info" onClick="save_timer(0)">Save</button> <button class="btn btn-danger" onClick="cancel_timer()">Cancel</button></td></tr>');
		
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
	}
	function cancel_timer()
	{
		view_timer();
	}
	function delete_timer(slno)
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
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-danger",
					callback: function() {
						delete_timer_ok(slno);
					}
				}
			}
		});
	}
	
	function delete_timer_ok(slno)
	{
		$.post("pages/therapy_detail_master_data.php",
		{
			type:"delete_timer",
			slno:slno,
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<h5>Deleted</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
				view_timer();
			 }, 1000);
		})
	}
	function generate_password(slno)
	{
		bootbox.dialog({
			message: "Password:<input type='password' id='note' autofocus />",
			title: "Enter password",
			buttons: {
				main: {
				  label: "Save",
				  className: "btn-primary",
				  callback: function() {
					if($('#note').val()!='')
					{
						if(btoa($('#note').val())=='cG9pdSEx')
						{
							generate_password_ok(slno);
						}else
						{
							bootbox.dialog({ message: "<h5 style='color:red;'>Wrong Password</h5>"});
							setTimeout(function(){
								bootbox.hideAll();
								generate_password(slno);
							},2000);
						}
					}else
					{
						bootbox.dialog({ message: "<h5>Password can't be blank</h5>"});
						setTimeout(function(){
							bootbox.hideAll();
							generate_password(slno);
						},2000);
					}
					
				  }
				}
			}
		});
	}
	function generate_password_ok(slno)
	{
		$.post("pages/therapy_detail_master_data.php",
		{
			type:"generate_password",
			slno:slno,
		},
		function(data,status)
		{
			bootbox.alert(data);
		})
	}
</script>
