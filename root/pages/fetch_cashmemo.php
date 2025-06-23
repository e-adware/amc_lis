<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div id="last_fetch"></div>
	<table class="table table-condensed">
		<tr>
			<td width="400px;">
				<input type="text" id="cashMemo" placeholder="CashMemoNo" autofocus />
				<button type="button" id="fetchBtn" class="btn btn-info" onclick="getPatData()">Get Patient</button>
			</td>
			<td width="400px;">
				<button type="button" id="saveBtn" class="btn btn-success" onclick="savePatData()" disabled>Save Patient</button>
				<!--<button type="button" id="" class="btn btn-info" onclick="testPat()">Patient Test</button>-->
			</td>
			<td><div id="result" style="font-size:16px;font-weight:bold;"></div></td>
		</tr>
	</table>
	<div id="res"></div>
	<div id="logs"></div>
	<div id="vaccus"></div>
	<div id="allopds" style="text-align:center;"></div>
	<div id="loader" style="display:none;top:50%;position:fixed;"></div>
	<?php
	/*
	$pid=rand(100,999);
	$opd=rand(1000,9999);
	$dept="20";
	$user="101";
	
	// Testing
	if($pid%2==0)
	{
		$prefix="O";
		$patType="OPD";
	}
	else
	{
		$prefix="I";
		$patType="IPD";
	}
	$date=date("Y-m-d");
	$time=date("H:i:s");
	$current_day = date('l');       // Get the current day of the week
	$current_time = date('H:i:s');  // Get the current time in HH:MM:SS format
	
	// Testing
	$current_day="Sunday";
	$current_time = date('17:i:s');
	
	if($current_day === 'Sunday')
	{
		// Emergency
		$prefix="E";
		$patType="EMER";
	}
	else
	{
		if($current_time <= '08:00:00')
		{
			// Emergency
			$prefix="E";
			$patType="EMER";
		}
		else if($current_time > '08:00:00' && $current_time <= '15:30:00')
		{
			// Blank Emergency
			// if same day no data Blank OPD , IPD
			// OPD
			// IPD
			$blankTableName='test_dept_serial_generator_'.$dept.'EMER';
			mysqli_query($link,"TRUNCATE TABLE `$blankTableName`");
			
			$blankTableName='test_dept_serial_generator_'.$dept.'OPD';
			$checkSl=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `$blankTableName` WHERE `date`='$date'"));
			if(!$checkSl)
			{
				mysqli_query($link,"TRUNCATE TABLE `$blankTableName`");
			}
			
			$blankTableName='test_dept_serial_generator_'.$dept.'IPD';
			$checkSl=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `$blankTableName` WHERE `date`='$date'"));
			if(!$checkSl)
			{
				mysqli_query($link,"TRUNCATE TABLE `$blankTableName`");
			}
		}
		else if($current_time > '15:30:00' && $current_time <= '23:59:59')
		{
			// Emergency
			$prefix="E";
			$patType="EMER";
			// Blank OPD
			// Blank iPD
			$blankTableName='test_dept_serial_generator_'.$dept.'OPD';
			mysqli_query($link,"TRUNCATE TABLE `$blankTableName`");
			
			$blankTableName='test_dept_serial_generator_'.$dept.'IPD';
			mysqli_query($link,"TRUNCATE TABLE `$blankTableName`");
		}
	}
	
	$tableName	='test_dept_serial_generator_'.$dept.$patType;
	
	echo "INSERT INTO `$tableName`(`patient_id`, `opd_id`, `type_id`, `date`, `time`, `user`) VALUES ('$pid','$opd','$dept','$date','$time','$user')<br/>";
	mysqli_query($link,"INSERT INTO `$tableName`(`patient_id`, `opd_id`, `type_id`, `date`, `time`, `user`) VALUES ('$pid','$opd','$dept','$date','$time','$user')");
	$lastSl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `$tableName` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `date`='$date' AND `time`='$time' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1"));
	$prefNo=$prefix."/".$lastSl['slno'];
	echo $prefNo;
	//*/

	?>
</div>
<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;display:none">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results"> </div>
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="../css/loader.css" />
<style>
.line
{
	border-top:1px solid;
}
.modal.fade.in {
  top: 5%;
  width: 80%;
  left: 30%;
}
.modal-body
{
	//min-height:500px;
	//overflow-y:scroll;
}
label{margin:0px;}
input[type="checkbox"]{padding:0px;margin:0px;}
.tdFluidTest
{
	font-weight: bold;
	background: #F9E8E8 !important;
}
.tdLabTest
{
	//background:#DAFFD7 !important;
}
.tdRadTest
{
	//background:#DED8F4 !important;
}
.fluidTest
{
	display: inline-block;
	width:48%;
	background:#E5E5E5;
	margin:2px;
	padding:2px;
}
.fluidTestChk
{
	display: inline-block;
	width:48%;
	background:#C0D8BF;
	margin:2px;
	padding:2px;
}
.tests_phlebo
{ display:inline-block; border-bottom:1px solid #CCC;width:100%;}

table td .icon-check,table td .icon-check-empty{ display:block !important;transform:scale(1.5) !important;margin-top:10px  !important;}

#samp_det_table td{ cursor:pointer;}

</style>
<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<script>
	let baseUrl="http://192.168.1.1/FAAMCHAPI/API/";
	$(document).ready(function()
	{
		$("#cashMemo").on("keyup", function(e)
		{
			if(e.which==13 && $("#cashMemo").val().trim()!="")
			{
				getPatData();
			}
		});
	});
	function grit_msg(msg,n)
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
	function fluidTestChk(n)
	{
		$("#fluidTest"+n).removeClass("fluidTest");
		$("#fluidTest"+n).removeClass("fluidTestChk");
		if($('#chk'+n).is(':checked'))
		{
			$("#fluidTest"+n).addClass("fluidTestChk");
		}
		else
		{
			$("#fluidTest"+n).addClass("fluidTest");
		}
	}
	function getPatData()
	{
		if($("#cashMemo").val().trim()=="")
		{
			$("#cashMemo").focus();
			grit_msg("Enter Cash Memo",0);
		}
		else
		{
			$("#fetchBtn").attr("disabled",true);
			$("#saveBtn").attr("disabled",true);
			$("#vaccus").empty();
			$("#result").empty();
			$("#loader").show();
			$.post("pages/fetch_ajax.php",
			{
				cashMemo	:$("#cashMemo").val().trim(),
				type		:1
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				$("#res").html(data);
				$("#allopds").empty();
				$("#fetchBtn").attr("disabled",false);
				if(parseInt($("#labTest").val().trim())>0)
				{
					$("#saveBtn").attr("disabled",false);
				}
			});
		}
	}
	function savePatData()
	{
		let chkBox=$(".chkBox");
		let chk=$(".chkBox:checked");
		if($("#cashMemo").val().trim()=="")
		{
			$("#cashMemo").focus();
			grit_msg("Enter Cash Memo",0);
		}
		if((chkBox.length)>0 && (chk.length)==0)
		{
			grit_msg("Select Fluid Test(s)",0);
		}
		else
		{
			if((chkBox.length)>0)
			{
				chkBox.attr("disabled",true);
			}
			let chk=$(".chkBox:checked");
			var checkTests="";
			for(var j=0; j<(chk.length); j++)
			{
				checkTests+=","+chk[j].value;
			}
			//*
			$("#fetchBtn").attr("disabled",true);
			$("#saveBtn").attr("disabled",true);
			$("#allopds").empty();
			$("#loader").show();
			$.post("pages/fetch_ajax.php",
			{
				PatientNo	:$("#PatientNo").val().trim(),
				CashMemoNo	:$("#CashMemoNo").val().trim(),
				CashMemoDate:$("#CashMemoDate").val().trim(),
				name		:$("#name").val().trim(),
				aAge		:$("#aAge").val().trim(),
				age			:$("#age").val().trim(),
				ageType		:$("#ageType").val().trim(),
				dob			:$("#dob").val().trim(),
				sex			:$("#sex").val().trim(),
				ward		:$("#ward").val().trim(),
				allTests	:($("#allTests").val().trim()+checkTests),
				aTests		:$("#aTests").val().trim(),
				regDate		:$("#regDate").val().trim(),
				testDepts	:$("#testDepts").val().trim(),
				patType		:$("#patType").val().trim(),
				user		:$("#user").text().trim(),
				type		:2
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				let val=JSON.parse(data);
				$("#cashMemo").val("").focus();
				$("#CashMemoNo").val("");
				$("#result").html(val['msg']);
				grit_msg(val['msg'],val['status']);
				if(val['status']>0)
				{
					setTimeout(function()
					{
						//load_sample(val['pid'],val['opd'],'',val['batch_no']);
						$("#fetchBtn").attr("disabled",false);
					},1000);
					
					var onclk="";
					for(var l=0; l<(val['opd']).length; l++)
					{
						onclk='load_sample("'+val['pid'][l]+'","'+val['opd'][l]+'","","'+val['batch_no']+'")';
						$("#allopds").append("<button class='btn btn-primary' onclick='"+onclk+"'>"+val['dept'][l]+" - "+val['opd'][l]+"</button>");
					}
				}
				if($("#user").text().trim()=="102")
				{
					$("#logs").html(val['txt']);
				}
			});
			//*/
		}
	}
	function loadSampleDetails(pid,opd)
	{
		$("#loader").show();
		$.post("pages/fetch_ajax.php",
		{
			pid			:pid,
			opd			:opd,
			type		:3
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#vaccus").html(data);
			//alert(data);
		});
	}
	function load_vaccu()
	 {
		var tst="";
		var samp=$(".samp:checked");
		for(var i=0;i<samp.length;i++)
		{
			var test=$("."+$(samp[i]).attr("id"));
			for(var j=0;j<test.length;j++)
			{
				tst=tst+"@"+$(test[j]).val();
			}
		}
		
		$(".vac").attr("checked",false);
		$.post("pages/phlebo_load_vaccu.php",
		{
			tst:tst
		},
		function(data,status)
		{
			var vc=data.split("@");
			for(var k=0;k<vc.length;k++)
			{
				if(vc[k])
				{
					$("#vac_"+vc[k]+"").click();
				}
			}
		})
	}
	function barcode_single(pid,opd,ipd,batch_no,vc)
	{
		
		var tst_vac="";
		var tst_vac_n=$(".tst_vac:checked");
		for(var j=0;j<tst_vac_n.length;j++)
		{
			if($(tst_vac_n[j]).val())
			{
				if(j==0)
				{
					tst_vac=$(tst_vac_n[j]).val();
				}
				else
				{
					tst_vac+=","+$(tst_vac_n[j]).val();
				}
			}
		}
		
		var user=$("#user").text();
		var url="pages/barcode_generate.php?pid="+pid+"&opd_id="+opd+"&ipd_id="+ipd+"&batch_no="+batch_no+"&user="+user+"&vac="+vc+"&tst_vac="+tst_vac+"&sing="+1;
		window.open(url,'','fullscreen=yes,scrollbars=yes');
	}
	function check_vac_err(name)
	{
		bootbox.dialog({ message: "<b style='color:red'>"+name+" is already processed. Can not be removed</b>"});
		setTimeout(function()
		{
			bootbox.hideAll();
		},2500)
	}
	function check_vac(elem,val)
	{
		if($("#"+val+"").prop("class")=="icon-check")
		{
			$("#"+val+"").prop("class","icon-check-empty")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(234, 164, 130)'});
			
			$(".test_vac_cls"+val).prop("checked", false);
		}
		else
		{
			$("#"+val+"").prop("class","icon-check")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(146, 217, 146)'});
			
			$(".test_vac_cls"+val).prop("checked", true);
		}
	}
	function select_all()
	{
		if($("#sel_all").val()=="Select All")
		{
			$(".icon-check-empty").prop("class","icon-check");	
			$("#sel_all").val("De-Select All");
			$("#sel_all").html("<i class='icon-list-ul'></i> De-Select All");
			$(".icon-check").parent().css({'background-color':'rgb(146, 217, 146)'});
			
			$(".tst_vac").prop("checked", true);
		}
		//~ else if($("#sel_all").val()=="De-Select All")
		//~ {
			//~ $(".icon-check:not('[name=vacc_done]')").prop("class","icon-check-empty");
			//~ $("#sel_all").val("Select All");
			//~ $("#sel_all").html("<i class='icon-list-ul'></i> Select All");
			//~ $(".icon-check-empty").parent().css({'background-color':'rgb(234, 164, 130)'});
			
			//~ $(".tst_vac").prop("checked", false);
		//~ }
	}
	function print_barcode(val)
	 {
		 var pid=patient_id=$('#h_no').text().trim();
		 var opd_id=$('#opd_id').text().trim();
		 var ipd_id=$('#ipd_id').text().trim();
		 var batch_no=$('#batch_no').text().trim();
		 var user=$("#user").text().trim();
		 
		 var test=val.split("_");
		 
		 var url="pages/barcode_generate_single.php?pid="+pid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch_no="+batch_no+"&user="+user+"&tid="+test[0];
		 window.open(url,'','fullscreen=yes,scrollbars=yes');
	 }
	function note(a)
	{
		//alert(a);
		$.post("pages/update_note.php",
		{
			test_id:a,
			patient_id:$('#h_no').text().trim(),
			opd_id:$('#opd_id').text().trim(),
			ipd_id:$('#ipd_id').text().trim(),
			batch_no:$('#batch_no').text().trim(),
			user:$('#user').text().trim(),
		},
		function(data,status)
		{
			bootbox.dialog({
			  message: "Note:<input type='text' value='"+data+"' id='note' autofocus />",
			  title: "Note",
			  buttons: {
				main: {
				  label: "Save",
				  className: "btn-primary",
				  callback: function() {
					if($('#note').val()!='')
					{
						$.post("pages/sample_notes.php",
						{
							test_id:a,
							patient_id:$("#h_no").text(),
							opd_id:$('#opd_id').text(),
							ipd_id:$('#ipd_id').text(),
							batch_no:$('#batch_no').text(),
							note:$('#note').val(),
							user:$('#user').text(),
						},
						function(data,status)
						{
							bootbox.alert(data);
						})
					}else
					{
						bootbox.alert("Note cannot blank");
					}
					
				  }
				}
			  }
			});
		})
	}
	function sample_accept(pid,opd,ipd,bch)
	{
		$("#ack").attr("disabled",true);
		bootbox.dialog({ message: "<p id='phlb_msg'><b>Saving...</b></p>"});
		
		var glob_barcode=$("#glob_barcode").val();
		
		var vac="";
		var vac_l=$(".icon-check");
		for(var i=0;i<vac_l.length;i++)
		{
			if($(vac_l[i]).prop("id"))
			{
				vac+="@@"+$(vac_l[i]).prop("id");
			}
		}
		
		var vac_n="";
		var vac_l_n=$(".icon-check-empty");
		for(var j=0;j<vac_l_n.length;j++)
		{
			if($(vac_l_n[j]).prop("id"))
			{
				vac_n+="@@"+$(vac_l_n[j]).prop("id");
			}
		}
		
		var tst_vac="";
		var tst_vac_n=$(".tst_vac:checked");
		for(var j=0;j<tst_vac_n.length;j++)
		{
			if($(tst_vac_n[j]).val())
			{
				if(j==0)
				{
					tst_vac=$(tst_vac_n[j]).val();
				}
				else
				{
					tst_vac+=","+$(tst_vac_n[j]).val();
				}
			}
		}
		
		$.post("pages/phlebo_save_sample.php",
		{
			pid:pid,
			opd_id:opd,
			ipd_id:ipd,
			batch_no:bch,
			vac:vac,
			vac_n:vac_n,
			tst_vac:tst_vac,
			user:$("#user").text().trim()
		},
		function(data,status)
		{
			//alert(data);
			if(glob_barcode==0)
			{
				$("#phlb_msg").html("<b>Saved</b>");
				setTimeout(function()
				{
					view_all('date');
					bootbox.hideAll();
				},1000);
			}
			else
			{
				$("#phlb_msg").html("<b>Saved. Redirecting to Barcode Generation</b>");	
				setTimeout(function()
				{
					bootbox.hideAll();
					var user=$("#user").text().trim();
					var url="pages/barcode_generate.php?pid="+pid+"&opd_id="+opd+"&user="+user+"&vac="+vac+"&tst_vac="+tst_vac;
					window.open(url,'','fullscreen=yes,scrollbars=yes');
				},1000);
				
				
				setTimeout(function()
				{
				$.post("pages/phlebo_load_sample.php",
				{
					uhid:pid,
					opd:opd,
					ipd:ipd,
					batch_no:bch,
					lavel:$("#lavel_id").val(),
					user:$("#user").text().trim(),
				},
				function(data,status)
				{
					$("#results").html(data);
					$("#results").fadeIn(500,function(){ load_vaccu(); });
				});
				},1000);
			}
		})
	}
	function load_sample(uhid,opd,ipd,batch_no)
	{
		$.post("pages/phlebo_load_sample.php",
		{
			uhid:uhid,
			opd:opd,
			ipd:ipd,
			batch_no:batch_no,
			lavel:$("#lavel_id").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#mod").click();
			$("#results").fadeIn(500,function(){ load_vaccu(); })
		})
	}
	function select_sample(id)
	 {
		if($("#"+id).is(":checked"))
		{
			$("."+id).click();
		}
		else
		{
			if($("#val_"+id).val()=="0")
			{
				$("."+id).attr('checked',false);
			}
			else
			{
				$("#"+id).attr('checked',true);
				bootbox.alert("Sample already accepted by Lab. Action cannot be completed");
				
			}
		}
		
		load_vaccu();
	 }
	function load_vaccu()
	{
		var tst="";
		var samp=$(".samp:checked")
		for(var i=0;i<samp.length;i++)
		{
			var test=$("."+$(samp[i]).attr("id"));
			for(var j=0;j<test.length;j++)
			{
				tst=tst+"@"+$(test[j]).val();
			}
		}
		
		$(".vac").attr("checked",false);
		$.post("pages/phlebo_load_vaccu.php",
		{
			tst:tst
		},
		function(data,status)
		{
			var vc=data.split("@");
			for(var k=0;k<vc.length;k++)
			{
				if(vc[k])
				{
					$("#vac_"+vc[k]+"").prop("checked",true);
				}
			}
		})
	}
	function checked_test(elem,vac,testid)
	{
		var vaccu_test_num=$(".test_vac_cls"+vac+"").length;
		var vaccu_test_chk_num=$(".test_vac_cls"+vac+":checked").length;
		
		if(vaccu_test_chk_num>0)
		{
			$("#"+vac+"").prop("class","icon-check")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(146, 217, 146)'});
		}
		else
		{
			$("#"+vac+"").prop("class","icon-check-empty")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(234, 164, 130)'});
		}
	}
	function hid_mod()
	{
		$("#mod").click();
	}
	function testPat()
	{
		$.post("pages/fetch_ajax.php",
		{
			cashMemo	:$("#cashMemo").val().trim(),
			lavel		:$("#lavel_id").val(),
			user		:$("#user").text().trim(),
			type		:5
		},
		function(data,status)
		{
			$("#res").html(data);
		});
	}
	function cashMemoBarcode(pid,opd,ipd)
	{
		var user=$("#user").text().trim();
		var url="pages/patient_barcode_generate.php?pId="+pid+"&oPd="+opd+"&iPd="+ipd+"&uSr="+user;
		window.open(url,'','fullscreen=yes,scrollbars=yes');
	}
	//abc("140","140/0523");
</script>
