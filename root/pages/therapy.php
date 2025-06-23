<?php
exec("/sbin/ifconfig | grep HWaddr", $output);

$res=explode("HWaddr ", $output[0]);

//echo $res[1];
//echo md5($res[1]);
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table">
		<tr>
			<td>
				<input type="password" class="span2" id="passwd" onKeyup="password_check(event,this.value)" autofocus >
				<span><b id="time_dis"><span id="timer">00:10</span> seconds</b></span>
			</td>
		</tr>
	</table>
</div>
<input type="hidden" id="therapy" value="1">
<script>
	function password_check(e,val)
	{
		$("#passwd").css({'border-color': 'rgba(82,168,236,0.8)'});
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$.post("pages/global_check.php",
			{
				type:"passwd_therapy",
				val:val,
			},
			function(data,status)
			{
				if(data==1)
				{
					$("#passwd").val(""); 
					$("#passwd").css('border', '2px solid green');
					setTimeout(function(){
						window.location="index.php";
					},500);
				}else
				{
					$("#passwd").val(""); 
					$("#passwd").css('border', '2px solid red');
				}
			})
		}
	}
	function startTimer(duration, display)
	{
		var timer = duration, minutes, seconds;
		setInterval(function () {
			minutes = parseInt(timer / 60, 10)
			seconds = parseInt(timer % 60, 10);

			minutes = minutes < 10 ? "0" + minutes : minutes;
			seconds = seconds < 10 ? "0" + seconds : seconds;

			display.textContent = minutes + ":" + seconds;

			if (--timer < 0) {
				timer = duration;
			}
			if(seconds<6)
			{
				$("#time_dis").css('color', 'red');
			}
			if(seconds==0)
			{
				close_win();
			}
		}, 1000);
	}

	window.onload = function () {
		var tenseconds = 10,
			display = document.querySelector('#timer');
		startTimer(tenseconds, display);
	};
	function close_win()
	{
		window.location="index.php";
	}
	
</script>
