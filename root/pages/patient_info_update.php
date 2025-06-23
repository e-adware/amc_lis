<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		<tr>
			<td style="text-align:center;">
				<div class="btn-group">
					<input type="text" value="Cash Memo No :" style="width:110px;cursor:default;font-weight:bold;" disabled />
					<input type="text" class="span2" id="cMemo" />
				</div>
				<div class="btn-group">
					<input type="text" value="Hospital No :" style="width:80px;cursor:default;font-weight:bold;" disabled />
					<input type="text" class="span2" id="patNo" />
				</div>
				<div class="btn-group">
					<input type="text" value="Name :" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" class="span3" id="name" />
				</div>
			</td>
		</tr>
	</table>
	<div id="res"></div>
	<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
</div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<script type="text/javascript" src="../js/jquery.gritter.min.js"></script>
<style>
	.nm:hover
	{
		color:#0B0083;
		cursor:pointer;
		transition:0.4s;
		text-shadow: 0px 0px 6px rgba(113, 210, 201, 0.7);
	}
</style>
<script>
	$(document).ready(function()
	{
		var cMemo = document.getElementById('cMemo'); // Get the input box
		var patNo = document.getElementById('patNo'); // Get the input box
		var name = document.getElementById('name'); // Get the input box
		var timeout = null; // Init a timeout variable to be used below // Listen for keystroke events
		
		cMemo.onkeyup = function(e) // Init a timeout variable to be used below
		{
			patNo.value='';
			name.value='';
			
			clearTimeout(timeout);
			timeout = setTimeout(function()
			{
				//alert('Input Value : '+ names.value);
				load_data(cMemo.value,'','');
			}, 500);
		};
		
		patNo.onkeyup = function(e) // Init a timeout variable to be used below
		{
			cMemo.value='';
			name.value='';
			
			clearTimeout(timeout);
			timeout = setTimeout(function()
			{
				//alert('Input Value : '+ names.value);
				load_data('',patNo.value,'');
			}, 500);
		};
		
		name.onkeyup = function(e) // Init a timeout variable to be used below
		{
			cMemo.value='';
			patNo.value='';
			
			clearTimeout(timeout);
			timeout = setTimeout(function()
			{
				//alert('Input Value : '+ names.value);
				load_data('','',name.value);
			}, 500);
		};
	});
	
	function capsUp(ths)
	{
		var val=$(ths).val().toUpperCase();
		$(ths).val(val);
	}
	
	function checkNumber(ths)
	{
		var val=ths.value;
		if(/\D/g.test(val))
		{
			val=val.replace(/\D/g,'');
			$(ths).val(val);
		}
	}
	
	function load_data(cMemo,patNo,name)
	{
		$("#loader").show();
		$.post("pages/patient_info_update_ajax.php",
		{
			cMemo:cMemo,
			patNo:patNo,
			name:name,
			type:1
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		});
	}
	
	function load_pat_det(pid)
	{
		$("#loader").show();
		$.post("pages/patient_info_update_ajax.php",
		{
			pid:pid,
			type:2
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		});
	}
	
	function pat_update(pid)
	{
		if($("#year").val().trim()=="")
		{
			$("#year").focus();
		}
		else if(parseInt($("#year").val().trim())<0)
		{
			$("#year").focus();
		}
		else if($("#month").val().trim()=="")
		{
			$("#month").focus();
		}
		else if(parseInt($("#month").val().trim())<0)
		{
			$("#month").focus();
		}
		else if($("#days").val().trim()=="")
		{
			$("#days").focus();
		}
		else if(parseInt($("#days").val().trim())<0)
		{
			$("#days").focus();
		}
		else if($("#sex").val()=="")
		{
			$("#sex").focus();
		}
		else if($("#phone").val().trim()!="" && $("#phone").val().trim().length!=10)
		{
			$("#phone").focus();
		}
		else
		{
			$("#loader").show();
			$.post("pages/patient_info_update_ajax.php",
			{
				pid		:pid,
				patName	:$("#patName").val().trim(),
				year	:$("#year").val().trim(),
				month	:$("#month").val().trim(),
				days	:$("#days").val().trim(),
				sex		:$("#sex").val().trim(),
				phone	:$("#phone").val().trim(),
				user	:$("#user").text().trim(),
				type	:3
			},
			function(data,status)
			{
				$("#loader").hide();
				var vl=JSON.parse(data);
				gritAlert(vl['msg'], vl['response']);
				go_back();
			});
		}
	}
	function gritAlert(msg,n)
	{
		$.gritter.add(
		{
			//title:	'Normal notification',
			text:	'<h5 style="text-align:center;">'+msg+'</h5>',
			time: 1000,
			sticky: false
		});
		if(n>0)
		{
			$(".gritter-item").css("background","#237438");
		}
	}
	function go_back()
	{
		load_data($("#cMemo").val().trim(), $("#patNo").val().trim(), $("#name").val().trim());
	}
</script>
