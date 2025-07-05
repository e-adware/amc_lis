<?php
if($p_info['emp_id']==102 && $_GET["page"])
{
	echo $t_page;
}
if($p_info['emp_id']==102 && $_GET["buttons"])
{
?>
	<div id="footer_but">
		<br>
		<br>
		Only Developer can see this. Click to copy button HTML code.
		<br>
		<br>
		<button class="btn btn-search" title='<i class="icon-search"></i>' onclick="btn_clip_copy('b1b')"><i class="icon-search"></i> btn-search</button>
		<input type="hidden" id="b1b" value='<button class="btn btn-search"><i class="icon-search"></i> btn-search</button>'>
		
		<button class="btn btn-save" title='<i class="icon-save"></i>' onclick="btn_clip_copy('b2b')"><i class="icon-save"></i> btn-save</button>
		<input type="hidden" id="b2b" value='<button class="btn btn-save"><i class="icon-save"></i> btn-save</button>'>
		
		<button class="btn btn-back" title='<i class="icon-backward"></i>' onclick="btn_clip_copy('b3b')"><i class="icon-backward"></i> btn-back</button>
		<input type="hidden" id="b3b" value='<button class="btn btn-back"><i class="icon-backward"></i> btn-back</button>'>
		
		<button class="btn btn-new" title='<i class="icon-edit"></i>' onclick="btn_clip_copy('b4b')"><i class="icon-edit"></i> btn-new</button>
		<input type="hidden" id="b4b" value='<button class="btn btn-new"><i class="icon-edit"></i> btn-new</button>'>
			
		<button class="btn btn-close" title='<i class="icon-off"></i>' onclick="btn_clip_copy('b5b')"><i class="icon-off"></i> btn-close</button>
		<input type="hidden" id="b5b" value='<button class="btn btn-close"><i class="icon-off"></i> btn-close</button>'>
			
		<button class="btn btn-delete" title='<i class="icon-remove"></i>' onclick="btn_clip_copy('b6b')"><i class="icon-remove"></i> btn-delete</button>
		<input type="hidden" id="b6b" value='<button class="btn btn-delete"><i class="icon-remove"></i> btn-delete</button>'>
		
		<button class="btn btn-reset" title='<i class="icon-refresh"></i>' onclick="btn_clip_copy('b7b')"><i class="icon-refresh"></i> btn-reset</button>
		<input type="hidden" id="b7b" value='<button class="btn btn-reset"><i class="icon-refresh"></i> btn-reset</button>'>
		
		<button class="btn btn-print" title='<i class="icon-print"></i>' onclick="btn_clip_copy('b8b')"><i class="icon-print"></i> btn-print</button>
		<input type="hidden" id="b8b" value='<button class="btn btn-print"><i class="icon-print"></i> btn-print</button>'>
		
		<button class="btn btn-excel" title='<i class="icon-file"></i>' onclick="btn_clip_copy('b9b')"><i class="icon-file"></i> btn-excel</button>
		<input type="hidden" id="b9b" value='<button class="btn btn-excel"><i class="icon-file"></i> btn-excel</button>'>
		
		<button class="btn btn-process" title='<i class="icon-forward"></i>' onclick="btn_clip_copy('b10b')"><i class="icon-forward"></i> btn-process</button>
		<input type="hidden" id="b10b" value='<button class="btn btn-process"><i class="icon-forward"></i> btn-process</button>'>
		
		<button class="btn btn-edit" title='<i class="icon-edit"></i>' onclick="btn_clip_copy('b11b')"><i class="icon-edit"></i> btn-edit</button>
		<input type="hidden" id="b11b" value='<button class="btn btn-edit"><i class="icon-edit"></i> btn-edit</button>'>
	</div>
	<br>
	<img src="../images/dot_loader.gif">
<?php
}
?>
	<!--Footer-part-->
	<div class="row-fluid">
		<div id="footer" class="text-center"> 
			<p class="">Copyright &copy; <?php echo date('Y'); ?>, <?php echo $company["name"];?>, <?php echo $company["address"]; ?> | <a target="_blank" href="http://e-adware.com/">Website</a> | <span style="cursor:pointer;" onclick="load_document()">Documentation</span></p>
		</div>
	</div>
</div>
	<input type="hidden" id="pfooter" value="0">
	<input type="hidden" id="pfooterp" value="0">
	<!--End - main-container-part-->


	<!--modal-->
	<input type="button" data-toggle="modal" data-target="#not_Modal" id="not_mod" style="display:none"/>
	<input type="text" id="modtxt" value="0" style="display:none"/>
	<div class="modal fade" id="not_Modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="notify_results" style="max-height:400px;overflow-y:scroll;">
					
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="notification_close" class="btn btn-danger btn-mini" data-dismiss="modal" aria-hidden="true">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!--modal end-->
		
	<script>
		function show_notify()
		{
			$.post("pages/notifications.php",
			{
				type:"exp_items",
				usr:$("#user").text().trim(),
			},
			function(data,status)
			{
				//alert(data);
				var vl=data.split("@@");
				var ex=parseInt(vl[0]);
				var stk=parseInt(vl[1]);
				if(ex>0)
				{
					$("#notify_head").html(ex+stk);
					$("#not_exp").html(ex);
					$("#user-nav").show();
					$("#li_exp").show();
					var wd=$("#search").css("width");
					wd=wd.replace("px", "");
					wd=parseInt(wd);
					wd=wd+10;
					//alert(wd);
					$("#user-nav").css("right",wd+"px");
				}
				if(stk>0)
				{
					$("#notify_head").html(ex+stk);
					$("#not_stk").html(stk);
					$("#user-nav").show();
					$("#li_stk").show();
					var wd=$("#search").css("width");
					wd=wd.replace("px", "");
					wd=parseInt(wd);
					wd=wd+10;
					//alert(wd);
					$("#user-nav").css("right",wd+"px");
				}
				//var val=data.split("@");
				//$("#pktunit").val(val['2']);
				//alert($("#search").css("width"));
			})
		}
		function notify_low_alr()
		{
			$.post("pages/notifications.php",
			{
				type:"notify_low_alr",
				usr:$("#user").text().trim(),
			},
			function(data,status)
			{
				//alert(data);
				//bootbox.dialog({ message: data});
				$("#notify_results").html(data);
				$("#not_mod").click();
				$("#not_Modal").css({"top":"5px","left":"30%","width":"80%"});
			})
		}
		function notify_alert()
		{
			$.post("pages/notifications.php",
			{
				type:"notify_alert",
				usr:$("#user").text().trim(),
			},
			function(data,status)
			{
				//alert(data);
				//bootbox.dialog({ message: data});
				$("#notify_results").html(data);
				$("#not_mod").click();
				$("#not_Modal").css({"top":"5px","left":"30%","width":"80%"});
			})
		}
		function dept_req_accs()
		{
			$.post("pages/notifications.php",
			{
				type:"dept_req_accs",
				usr:$("#user").text().trim(),
			},
			function(data,status)
			{
				//alert(data);
				if(data=="1")
				{
					//bootbox.dialog({ message: data});
					dept_req_notify();
				}
				//$("#notify_results").html(data);
				//$("#not_mod").click();
				//$("#not_Modal").css({"top":"5px","left":"30%","width":"80%"});
			})
		}
		function dept_req_notify()
		{
			$.post("pages/notifications.php",
			{
				type:"dept_req_notify",
				usr:$("#user").text().trim(),
			},
			function(data,status)
			{
				var not_val=0;
				var not_exp=$("#not_exp").text().trim();
				var not_stk=$("#not_stk").text().trim();
				if(not_exp=="")
				{
					not_exp=0;
				}
				else
				{
					not_exp=parseInt(not_exp);
				}
				if(not_stk=="")
				{
					not_stk=0;
				}
				else
				{
					not_stk=parseInt(not_stk);
				}
				not_val=not_exp+not_stk+parseInt(data);
				$("#notify_head").html(not_val);
				if(data!="0")
				{
					//alert(not_val);
					$("#notify_head").html(not_val);
					$("#not_req").html(data);
					$("#user-nav").show();
					$("#li_dep_req").show();
					var wd=$("#search").css("width");
					wd=wd.replace("px", "");
					wd=parseInt(wd);
					wd=wd+10;
					//alert(wd);
					$("#user-nav").css("right",wd+"px");
				}
				else
				{
					if(not_val <= 0)
					{
						$("#user-nav").hide();
					}
					$("#li_dep_req").hide();
				}
				setTimeout(function(){dept_req_notify();},5000);
			})
		}
		function show_dep_req()
		{
			$.post("pages/notifications.php",
			{
				type:"show_dep_req",
				usr:$("#user").text().trim(),
			},
			function(data,status)
			{
				//alert(data);
				//bootbox.dialog({ message: data});
				$("#notify_results").html(data);
				$("#not_mod").click();
				$("#not_Modal").css({"top":"5px","left":"30%","width":"80%"});
			})
		}
		
		show_notify();
		dept_req_accs();
		//alert($("#user-nav").css("display"));
		
		function load_document()
		{
			var win = window.open('../MPDF/documentation_pdf.php?q=UghuiIUTYgUTTGihuhjgtsUIv6guy6uyfFUYf8Uhkjhkjhy67h=MQ==', '_blank');
			if (win) {
				//Browser has allowed it to be opened
				win.focus();
			} else {
				//Browser has blocked it
				alert('Please allow popups for this website');
			}
		}
		
		$(window).load(function(){
			checked_varibale();
			password_checked();
		});

		function password_checked()
		{
			$.post("pages/global_check.php",{type:"password_checked",old_pass:"ASSAM MEDICAL COLLEGE AND HOSPITAL",},function(data,status){if(data!='1'){bootbox.alert("<h5>"+data+"</h5>");window.location.href='../';}})
		}
		function checked_varibale()
		{
			if($("#therapy").val()!='1')
			{
				$.post("pages/global_check.php",{type:"checked_varibale",},function(data,status){var val=data.split("@");if(val[0]=='404'){if($("#pfooter").val()=="0"){$("#pfooter").val('1');var dialog = bootbox.dialog({message: '<h3 class="text-center" style="color:darkblue;">'+val[1]+'</h3>',closeButton: false});}}else{if($("#pfooter").val()==1){$("#pfooter").val('0');bootbox.hideAll();}}})
			}
		}
		
		function cal_age_all(e)
		{
			$.post("pages/global_check.php",
			{
				type:"age_calculator_all",
				dob:$(".dob").val(),
			},
			function(data,status)
			{
				var res=data.split("@");
				$("#age_y").val(res[0]);
				$("#age_m").val(res[1]);
				$("#age_d").val(res[2]);
			});
		}

		function age_y_check(val,e)
		{
			$("#age_y").css('border','');
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==13)
			{
				var age_y=parseInt($(val).val());
				if(!age_y)
				{
					//~ age_y=0;
					//~ $(val).val(age_y);
				}
				$("#age_m").focus();
			}
			calculate_dob_all();
		}

		function age_m_check(val,e)
		{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==13)
			{
				var age_m=parseInt($(val).val());
				if(!age_m)
				{
					//~ age_m=0;
					//~ $(val).val(age_m);
				}
				$("#age_d").focus();
			}
			calculate_dob_all();
		}

		function age_d_check(val,e)
		{
			$(val).css({'border-color': ''});
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==13)
			{
				var age_d=parseInt($(val).val());
				if(!age_d)
				{
					age_d=0;
					
					//~ if($("#age_y").val()==0 && $("#age_m").val()==0)
					//~ {
						//~ $(val).css({'border-color': '#F00'}).focus();
						//~ return false;
					//~ }
					
					//~ $(val).val(age_d);
				}
				$("#sex").focus();
			}
			calculate_dob_all();
		}

		function calculate_dob_all()
		{
			$("#dob").css({"border-color":""});
			$("#age_y").css({"border-color":""});
			$("#age_m").css({"border-color":""});
			$("#age_d").css({"border-color":""});
			
			var age_y=parseInt($("#age_y").val())
			if(!age_y)
			{
				age_y=0;
			}
			var age_m=parseInt($("#age_m").val())
			if(!age_m)
			{
				age_m=0;
			}
			var age_d=parseInt($("#age_d").val())
			if(!age_d)
			{
				age_d=0;
			}
			
			$.post("pages/global_check.php",
			{
				type:"calculate_dob_all",
				age_y:age_y,
				age_m:age_m,
				age_d:age_d,
			},
			function(data,status)
			{
				$(".dob").val(data);
			});
		}

		function btn_clip_copy(id)
		{
			copyToClipboard($("#"+id).val());
			alert("Copied to Clipboard");
		}
		function copyToClipboard(text)
		{
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val(text).select();
			document.execCommand("copy");
			$temp.remove();
		}
		
		$(document).on('keyup', ".numericc", function () {
			$(this).val(function (_, val)
			{
				if(val==0)
				{
					return val;
				}
				else
				{
					var number=parseInt(val);
					if(!number){ number=""; }
					return number;
				}
			});
		});
		$(document).on('keyup', ".numericcfloat", function () {
			$(this).val(function (_, val)
			{
				if(val==0)
				{
					return val;
				}
				else if(val==".")
				{
					return "0.";
				}
				else
				{
					var n=val.length;
					var numex=/^[0-9.]+$/;
					if(val[n-1].match(numex))
					{
						return number;
					}
					else
					{
						val=val.slice(0,n-1);
						return val;
					}
					
					//~ var number=parseFloat(val);
					//~ if(!number){ number=""; }
					//~ return number;
				}
			});
		});
		
		function setCookie(cname,cvalue,exdays) {
			const d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			let expires = "expires=" + d.toGMTString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		}

		function getCookie(cname) {
			let name = cname + "=";
			let decodedCookie = decodeURIComponent(document.cookie);
			let ca = decodedCookie.split(';');
			for(let i = 0; i < ca.length; i++) {
				let c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		}
		
		function checkCookie()
		{
			let emp_id = getCookie("91E03M03P6I1D");
			let emp_cd = getCookie("91E03M03P6C1D");
			let emp_ps = getCookie("91E03M03P6PSS");
			
			if(!emp_id || !emp_cd || !emp_ps)
			{
				window.location.href="../";
			}
		}
		function delete_cookie(name){
			document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
		}
	</script>
	
	<script src="../js/bootstrap.min.js"></script>
	<script src="../jss/bootbox.min.js"></script> 
	<script src="../js/matrix.js"></script> 
	<script src="../js/matrix.form_common.js"></script>

	<!--<script src="../js/jquery.ui.custom.js"></script>-->
	<!--<script src="../js/bootstrap-colorpicker.js"></script> -->
	<!--<script src="../js/bootstrap-datepicker.js"></script> -->
	<!--<script src="../js/jquery.toggle.buttons.js"></script>-->
	<!--<script src="../js/masked.js"></script> -->
	<!--<script src="../js/jquery.uniform.js"></script> -->
	<!--<script src="../js/select2.min.js"></script>-->
	<!--<script src="../js/wysihtml5-0.3.0.js"></script> -->
	<!--<script src="../js/jquery.peity.min.js"></script> -->
	<!--<script src="../js/bootstrap-wysihtml5.js"></script> -->
	<!--end-Footer-part-->
	<style>
		.txt_small{
			font-size:8px;
		}
		#user-nav
		{
			left: auto !important;
			//right: 0px !important;
		}

	</style>
</body>
</html>
