<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Payment Details</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div id="load_data">
		
	</div>
</div>
<script>
	$(document).ready(function(){
		view_timer();
	});
	function view_timer()
	{
		$.post("pages/therapy_detail_client_data.php",
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
	function check_password(slno)
	{
		if($("#password"+slno).val()=="")
		{
			$("#password"+slno).focus();
			return false;
		}
		$.post("pages/therapy_detail_client_data.php",
		{
			type:"check_password",
			slno:slno,
			password:$("#password"+slno).val(),
		},
		function(data,status)
		{
			if(data==202)
			{
				bootbox.dialog({ message: "<h5>Successful</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					view_timer();
				 }, 1000);
			}
			if(data==404)
			{
				bootbox.dialog({ message: "<h5 style='color:red;'>Wrong OTP</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					view_timer();
				 }, 1000);
			}
		})
	}
</script>
