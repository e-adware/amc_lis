<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Generate Token</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Title</th>
			<td>
				<input type="text" class="span9" id="complaint_title" onkeyup="complaint_title_up(this,event)" maxlength="60" autofocus>
				<span id="title_error"></span>
			</td>
		</tr>
		<tr>
			<th>Description</th>
			<td>
				<textarea class="span9" id="complaint_text" onkeyup="complaint_text_up(this,event)" rows="15"></textarea>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<button class="btn btn-info" onClick="save_complaint()">Submit</button>
			</td>
		</tr>
	</table>
</div>
<div id="loader" style="margin-top:-12%;"></div>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
	});
	function complaint_title_up(dis,e)
	{
		var val=dis.value;
		
		//$("#"+dis.id).css({'border-color': 'rgba(82,168,236,0.8)'});
		var nval=val.toUpperCase();
		$("#"+dis.id).val(nval);
		
		var n=val.length;
		if(n>0)
		{
			var numex=/^[A-Za-z0-9 ',.!@#$%&*()=+|?/]+$/;
			if(val[n-1].match(numex))
			{
				
			}
			else
			{
				val=val.slice(0,n-1);
				$("#"+dis.id).val(val);
			}
		}
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if(n>0)
			{
				$("#complaint_text").focus();
			}else
			{
				$("#"+dis.id).focus();
			}
		}
		$("#title_error").text("");
		if(n>=60)
		{
			setTimeout(function(){
				$("#title_error").html("<b><br>Maximum 60 characters are allowed</b>");
			},100);
		}
	}
	function complaint_text_up(dis,e)
	{
		
	}
	function save_complaint()
	{
		if($("#complaint_title").val()=='')
		{
			$("#complaint_title").focus();
			return true;
		}
		if($("#complaint_text").val()=='')
		{
			$("#complaint_text").focus();
			return true;
		}
		$("#loader").show();
		$.post("pages/client_complaint_compose_data.php",
		{
			type:"save_client_complaint",
			complaint_title:$("#complaint_title").val(),
			complaint_text:$("#complaint_text").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			var str=data.split("@@@");
			var val1=str[0];
			var val2=str[1];
			if(val1=='1')
			{
				bootbox.alert("<b>Successfully submitted.<br>Your token ID is "+val2+"</b>");
				clear_fields();
			}else if(val1=='4')
			{
				bootbox.alert("<b>Internet Connection Error !.<br>Try again later.</b>");
			}
		})
	}
	function clear_fields()
	{
		$("#complaint_title").val('');
		$("#complaint_text").val('');
	}
</script>
<style>
textarea {
    resize: none;
}
</style>
