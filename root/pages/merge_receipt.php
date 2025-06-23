<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		<tr>
			<th width="400px;">Old Cash Memo No :</th>
			<th width="400px;">New Cash Memo No :</th>
			<th width="400px;"></th>
			<th></th>
		</tr>
		<tr>
			<td>
				<input type="text" id="cashMemo1" placeholder="Old Cash Memo No" autofocus />
				<button type="button" id="fetchBtn1" class="btn btn-info" onclick="searchData()">Search Patient</button>
			</td>
			<td>
				<input type="text" id="cashMemo2" placeholder="New Cash Memo No" />
				<button type="button" id="fetchBtn2" class="btn btn-info" onclick="getPatData()">Search Patient</button>
			</td>
			<td>
				<button type="button" id="saveBtn" class="btn btn-success" onclick="savePatData()" disabled>Save Patient</button>
				<!--<button type="button" id="" class="btn btn-info" onclick="testPat()">Patient Test</button>-->
			</td>
			<td><div id="result" style="font-size:16px;font-weight:bold;"></div></td>
		</tr>
		<tr>
			<td id="dataMemo1"></td>
			<td colspan="3" id="dataMemo2"></td>
		</tr>
	</table>
	<div id="allopds" style="text-align:center;"></div>
	<div id="loader" style="display:none;top:50%;position:fixed;"></div>
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
<link rel="stylesheet" href="../css/loader.css" />
<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<script>
	$(document).ready(function()
	{
		$("#cashMemo1").on("keyup", function(e)
		{
			if(e.which==13 && $("#cashMemo1").val().trim()!="")
			{
				searchData();
			}
		});
		$("#cashMemo2").on("keyup", function(e)
		{
			if(e.which==13 && $("#cashMemo2").val().trim()!="")
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
	function searchData()
	{
		if($("#cashMemo1").val().trim()=="")
		{
			$("#cashMemo1").focus();
			grit_msg("Enter Old Cash Memo",0);
		}
		else
		{
			$("#fetchBtn1").attr("disabled",true);
			$("#loader").show();
			$.post("pages/fetch_ajax.php",
			{
				cashMemo1	:$("#cashMemo1").val().trim(),
				type		:5
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#fetchBtn1").attr("disabled",false);
				$("#dataMemo1").html(data);
			});
		}
	}
	function getPatData()
	{
		if($("#cashMemo2").val().trim()=="")
		{
			$("#cashMemo2").focus();
			grit_msg("Enter New Cash Memo",0);
		}
		else
		{
			$("#fetchBtn2").attr("disabled",true);
			$("#saveBtn").attr("disabled",true);
			$("#loader").show();
			$.post("pages/fetch_ajax.php",
			{
				cashMemo	:$("#cashMemo2").val().trim(),
				type		:1
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				$("#dataMemo2").html(data);
				$("#fetchBtn2").attr("disabled",false);
				if(parseInt($("#labTest").val().trim())>0)
				{
					$("#saveBtn").attr("disabled",false);
				}
			});
		}
	}
	
	function savePatData()
	{
		if($("#cashMemo1").val().trim()=="")
		{
			$("#cashMemo1").focus();
			grit_msg("Enter Old Cash Memo",0);
		}
		else if($("#cashMemo2").val().trim()=="")
		{
			$("#cashMemo2").focus();
			grit_msg("Enter New Cash Memo",0);
		}
		else if($("#oldMemoDet").val().trim() == $("#CashMemoNo").val().trim())
		{
			$("#cashMemo2").focus();
			grit_msg("Same cash memo cannot merged",0);
		}
		else if($("#oldHospNo").val().trim() != $("#PatientNo").val().trim())
		{
			$("#cashMemo2").focus();
			grit_msg("Hospital No not matched",0);
		}
		else
		{
			$("#fetchBtn1").attr("disabled",true);
			$("#fetchBtn2").attr("disabled",true);
			$("#saveBtn").attr("disabled",true);
			$("#loader").show();
			$.post("pages/fetch_ajax.php",
			{
				oldMemoNo	:$("#oldMemoDet").val().trim(),
				newMemoNo	:$("#CashMemoNo").val().trim(),
				allTests	:$("#allTests").val().trim(),
				user		:$("#user").text().trim(),
				type		:6
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				let val=JSON.parse(data);
				grit_msg(val['msg'],val['response']);
				$("#allopds").empty();
				onclk='load_sample("'+val['pid']+'","'+val['opd']+'","","'+val['batch_no']+'")';
				$("#allopds").append("<button class='btn btn-primary' onclick='"+onclk+"'>"+val['dept']+" - "+val['opd']+"</button>");
			});
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
		else if($("#sel_all").val()=="De-Select All")
		{
			$(".icon-check:not('[name=vacc_done]')").prop("class","icon-check-empty");
			$("#sel_all").val("Select All");
			$("#sel_all").html("<i class='icon-list-ul'></i> Select All");
			$(".icon-check-empty").parent().css({'background-color':'rgb(234, 164, 130)'});
			
			$(".tst_vac").prop("checked", false);
		}
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
</script>