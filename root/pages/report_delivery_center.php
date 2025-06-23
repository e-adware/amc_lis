<div id="content-header">
    <div class="header_div"> <span class="header"> Report Delivery</span></div>
</div>
<div class="container-fluid"  onkeypress="hide_div(event)" id="rep_delv">
	<table class="table table-bordered text-center">
		<tr>
			<td style="text-align:center" colspan="2">
				<b>From</b>
				<input class="form-control datepicker" type="text" name="fdate" id="fdate" value="<?php echo date("Y-m-d"); ?>" >
				<b>To</b>
				<input class="form-control datepicker" type="text" name="tdate" id="tdate" value="<?php echo date("Y-m-d"); ?>" >
				<select id="pat_type" style="display:none">
					<option value="opd_id">OPD</option>
					<option value="ipd_id">IPD</option>
				</select>
				<button class="btn btn-search" onClick="load_pat_rep()" style="margin-bottom: 10px;"><i class="icon-search"></i> Search</button>
			</td>
		</tr>
		<tr>
			<td style="text-align:right">
				<b>Name</b>
				<input type="text" id="pat_name" class="src" placeholder="Enter to search" onKeyup="load_pat_rep_event(event)">
			</td>
			<td style="text-align:left">
				<select class="span2" id="category">
					<option value="pin">Bill No.</option>
					<option value="uhid">UHID</option>
				</select>
				<input type="text" class="span2" id="var_id" class="src" placeholder="Enter to search" onKeyup="load_pat_rep_event(event)">
			</td>
		</tr>
	</table>
	<div id="cent_pat">
		
	</div>
	<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
	<input type="hidden" id="mod_chk" value="0"/>
	<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results"> </div>
				</div>
			</div>
		</div>
	</div>
	
	<input type="button" data-toggle="modal" data-target="#myModal1" id="mod1" style="display:none"/>
	<input type="hidden" id="mod_chk1" value="0"/>
	<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog" id="modal_dial">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results1"></div>
				</div>
			</div>
			</div>
		</div>
	</div>
	
	<link rel="stylesheet" href="include/css/jquery-ui.css" />
	<link rel="stylesheet" href="../css/animate.css" />
	<script src="include/js/jquery-ui.js"></script>
	<!-- Time -->
	<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
	<!-- Loader -->
	<link rel="stylesheet" href="../css/loader.css" />

	<script>
	
	$(document).ajaxStop(function()
	{
		$("#loader").hide();
	});
	
	$(document).ajaxStart(function()
	{
		// $("#loader").show();
	});
	
	$(document).ready(function()
	{
		load_pat_rep();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
	})
	
	function load_pat_rep()
	{
		$("#loader").show();
		$.post("pages/report_delivery_center_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			user:$("#user").text(),
			pat_typ:$("#pat_type").val(),
			name:$("#pat_name").val(),
			catg:$("#category").val(),
			pat_id:$("#var_id").val(),
			type:1
		},
		function(data,status)
		{
			$("#cent_pat").html(data);
			$("#loader").hide();
		})
	}
	
	function load_pat_rep_event(e)
	{
		if(e.which==13)
		{
			load_pat_rep();
		}
	}
	
	function load_pat()
	{
		$.post("pages/report_delivery_center_pat.php",
		{
			user:$("#user").text()
		},
		function(data,status)
		{
			$("#cent_pat").html(data);
		})
	}
	function load_pat_date()
	{
		$.post("pages/report_delivery_center_pat.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			user:$("#user").text(),
			pat_typ:$("#pat_type").val(),
			type:"date",
		},
		function(data,status)
		{
			$("#cent_pat").html(data);
		})
	}
	
	function load_pat_reg(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			
				//~ $.post("pages/report_delivery_center_pat.php",
				//~ {
					//~ user:$("#user").text(),
					//~ reg:$("#reg").val(),
					//~ type:"reg",
				//~ },
				//~ function(data,status)
				//~ {
					//~ $("#cent_pat").html(data);
					//~ $("#check_enter_key").val("1");
				//~ })
			
				$("#view_1").click();
			
		}
		else
		{
			if($("#reg").val().length==0 || $("#reg").val().length>4)
			{
				$.post("pages/report_delivery_center_pat.php",
				{
					user:$("#user").text(),
					reg:$("#reg").val(),
					type:"reg",
				},
				function(data,status)
				{
					$("#cent_pat").html(data);
					$("#check_enter_key").val("1");
				})	
			}
		}
	}
	function load_pat_name(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$.post("pages/report_delivery_center_pat.php",
			{
				user:$("#user").text(),
				name:$("#name").val(),
				type:"name",
			},
			function(data,status)
			{
				$("#cent_pat").html(data);
			})
		}
	}
	
	function load_pat_data(val)
	{
		$.post("pages/report_delvery_test.php",
		{
			pid:$("#pid"+val+"").val(),
			opd_id:$("#opd_id"+val+"").val(),
			ipd_id:$("#ipd_id"+val+"").val(),
			batch:$("#batch"+val+"").val(),
			bill_id:$("#bill_id"+val+"").val(),
			tr_serial_no:val,
		},
		function(data,status)
		{
			$("#results").html(data);
			//$(".modal-dialog").css({'width':'1380px','height':'1000px'});
			$("#mod").click();
			$("#results").fadeIn(500,function(){ $("#results").animate({scrollTop:0},"fast",function(){ }); })
		})
	}
	//~ function load_pat_info(reg,div)
	//~ {
		//~ var pat=reg.split("@");
		//~ $.post("pages/report_delvery_test.php",
		//~ {
			//~ pid:pat[1],
			//~ opd:pat[2],
			//~ batch:pat[9],
		//~ },
		//~ function(data,status)
		//~ {
			//~ $("#results").html(data);
			//~ //$(".modal-dialog").css({'width':'1380px','height':'1000px'});
				
		//~ })
	//~ }
	function print_report(tst,pos)
	{
		//var tstid=tid;
		
		var uhid=$("#uhid_no").val();
		var opd_id=$("#opd_id").val();
		var ipd_id=$("#ipd_id").val();
		var batch_no=$("#batch_no").val();
		var user=$("#user").text();
		
		var doc="";
		var doc_tot=$(".lab_doc_check:checked");
		for(var i=0;i<doc_tot.length;i++)
		{
			doc=doc+","+$(doc_tot[i]).val();
		}
		
		var url="pages/pathology_report_print.php?uhid="+btoa(uhid)+"&opd_id="+btoa(opd_id)+"&ipd_id="+btoa(ipd_id)+"&batch_no="+btoa(batch_no)+"&tests="+btoa(tst)+"&hlt="+btoa(tst)+"&user="+btoa(user)+"&sel_doc="+btoa(doc)+"&view="+btoa(0);
		
		var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
	}
	function print_report_rad(tst,pos)
	{
		//var tstid=tid;
		
		var uhid=$("#uhid_no").val();
		var opd_id=$("#opd_id").val();
		var ipd_id=$("#ipd_id").val();
		var batch_no=$("#batch_no").val();
		var user=$("#user").text();
		
		var doc="";
		var doc_tot=$(".lab_doc_check:checked");
		for(var i=0;i<doc_tot.length;i++)
		{
			doc=doc+","+$(doc_tot[i]).val();
		}
		
		var url="pages/radiology_report_print.php?uhid="+btoa(uhid)+"&opd_id="+btoa(opd_id)+"&ipd_id="+btoa(ipd_id)+"&batch_no="+btoa(batch_no)+"&tstid="+btoa(tst)+"&category_id="+btoa(2)+"&user="+btoa(user)+"&sel_doc="+btoa(doc)+"&view="+btoa(0);
		
		var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
	}
	function select_all(val)
	{
		var tst=$(".tst");
		
		if(val=="sel")
		{
			for(var i=0;i<tst.length;i++)
			{
				if($(tst[i]).prop("checked",false))
				{
					//$(tst[i]).click();
					$(tst[i]).prop("checked",true);
					test_print_group($(tst[i]).val());
				}
			}	
			$("#sel_all").val("sel_u");
			$("#sel_all").html("<i class='icon-check'></i> De-Select All");
		}
		else
		{
			for(var i=0;i<tst.length;i++)
			{
				if($(tst[i]).prop("checked",true))
				{
					//$(tst[i]).click();
					$(tst[i]).prop("checked",false);
					test_print_group($(tst[i]).val());
				}
			}
			$("#sel_all").val("sel");
			$("#sel_all").html("<i class='icon-check-empty'></i> Select All");
		}
		$(".rad_test").prop("checked",false);
	}
	function group_print_test_rep(val)
	{
		var tst=$("#test_print").val();
		var uhid=$("#uhid_no").val();
		var opd_id=$("#opd_id").val();
		var ipd_id=$("#ipd_id").val();
		var batch_no=$("#batch_no").val();
		var user=$("#user").text();
		
		var doc="";
		var doc_tot=$(".lab_doc_check:checked");
		for(var i=0;i<doc_tot.length;i++)
		{
			doc=doc+","+$(doc_tot[i]).val();
		}
		
		if(tst!='')
		{
			if(val==2) // PDF
			{
				var url="pages/pathology_report_print_pdf.php?uhid="+btoa(uhid)+"&opd_id="+btoa(opd_id)+"&ipd_id="+btoa(ipd_id)+"&batch_no="+btoa(batch_no)+"&tests="+btoa(tst)+"&hlt="+btoa(tst)+"&user="+btoa(user)+"&sel_doc="+btoa(doc)+"&view="+btoa(0);
			}
			else
			{
				var url="pages/pathology_report_print.php?uhid="+btoa(uhid)+"&opd_id="+btoa(opd_id)+"&ipd_id="+btoa(ipd_id)+"&batch_no="+btoa(batch_no)+"&tests="+btoa(tst)+"&hlt="+btoa(tst)+"&user="+btoa(user)+"&sel_doc="+btoa(doc)+"&view="+btoa(0);
			}
			var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
		}
		else
		{
			bootbox.dialog({ message: "<b style='color:red'>No Test Selected</b>"});
			setTimeout(function()
			{
				bootbox.hideAll();
			},1000);
		}
	}
	function test_print_group(val)
	{
		if($("#"+val+"_tst").is(":checked"))
		{
			var t_prnt=$("#test_print").val();
			var nval=t_prnt+"@"+val;
			$("#test_print").val(nval);
		}
	   else
		{
			var t_prnt=$("#test_print").val();
			t_prnt1=t_prnt.replace("@"+val,"");
			$("#test_print").val(t_prnt1);
		}
	}
	
	function select_all_rad(val)
	{
		var tst=$(".tst_rad");
		
		
		if(val=="sel")
		{
			for(var i=0;i<tst.length;i++)
			{
				if($(tst[i]).prop("checked",false))
				{
					//$(tst[i]).click();
					$(tst[i]).prop("checked",true);
					test_print_group($(tst[i]).val());
				}
			}	
			$("#sel_all_rad").val("sel_u");
			$("#sel_all_rad").html("<i class='icon-check'></i> De-Select All");
		}
		else
		{
			for(var i=0;i<tst.length;i++)
			{
				if($(tst[i]).prop("checked",true))
				{
					//$(tst[i]).click();
					$(tst[i]).prop("checked",false);
					test_print_group($(tst[i]).val());
				}
			}
			$("#sel_all_rad").val("sel");
			$("#sel_all_rad").html("<i class='icon-check-empty'></i> Select All");
		}
	}
	
	
	function hid_div(e)
	{
		if(e.which==27)
		{
			if($('#myModal1').hasClass('in'))
			{
				$('#myModal1').modal('hide');
				$("#mod_chk1").val("0");
				
			}
			else if($('#myModal').hasClass('in'))
			{
				$('#myModal').modal('hide');
				$("#mod_chk").val("0");
			}
		}
	}
	
	
	function report_delv()
	{
		if($(".tst:checked").length==0)
		{
			bootbox.dialog({ message: "<b style='color:red'>No Test Selected</b>"});
			setTimeout(function()
			{
				bootbox.hideAll();
			},1000);
			return false;
		}
		
		$.post("pages/report_delivery_report.php",
		{
			pid:$("#uhid_no").val(),
			opd_id:$("#opd_id").val(),
			ipd_id:$("#ipd_id").val(),
			batch_no:$("#batch_no").val(),
			bill_id:$("#bill_id").val(),
			user:$("#user").text().trim(),
			type:1
		},
		function(data,status)
		{
			$("#results1").html(data);
			//$("#modal_dial").css({'width':'600px','height':'500px'});
			
			$("#mod1").click();
			$("#mod_chk1").val("1");
			
			$("#results1").fadeIn(500,function(){ $("#results1").animate({scrollTop:0},"fast",function(){ }); })
			
		})
	}
	function report_save()
	{
		if($(".tst:checked").length==0)
		{
			bootbox.dialog({ message: "<b style='color:red'>No Test Selected</b>"});
			setTimeout(function()
			{
				bootbox.hideAll();
			},1000);
			return false;
		}
		if($("#del_name").val()=="")
		{
			$("#del_name").focus();
			return false;
		}
		
		$.post("pages/report_delivery_report.php",
		{
			pid:$("#uhid_no").val(),
			opd_id:$("#opd_id").val(),
			ipd_id:$("#ipd_id").val(),
			batch_no:$("#batch_no").val(),
			bill_id:$("#bill_id").val(),
			user:$("#user").text().trim(),
			
			name:$("#del_name").val().trim(),
			phone:$("#del_phone").val().trim(),
			remarks:$("#del_remarks").val().trim(),
			tests:$("#test_print").val(),
			type:2
		},
		function(data,status)
		{
			if(data==0)
			{
				bootbox.dialog({ message: "<h4>Failed, try agail later</h4>"});
				setTimeout(function()
				{
					bootbox.hideAll();
				},1000);
			}
			else
			{
				bootbox.dialog({ message: "<h4>Delivered</h4>"});
				setTimeout(function()
				{
					bootbox.hideAll();
					
					$("#mod1").click();
					$("#mod_chk1").val("0");
					//load_pat_data($("#tr_serial_no").val());
					
				},1000);
			}
		})
	}
	
	function group_print_test_pdf()
	{
		var tst=$("#test_print").val();
		
		var uhid=$("#uhid_no").val();
		var visit=$("#vis_no").val();
		var user=$("#user").text();
		
		var url="pages/report_print_path_group_pdf.php?uhid="+uhid+"&visit="+visit+"&tests="+tst+"&rp_page="+1+"&user="+user;
		var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
	}
	
	</script>

	<style>
		.modal.fade.in {
			top: 1%;
		}
		.modal-body
		{
			max-height: 550px;
		}
		
		#myModal
		{
			left: 23%;
			width:95%;
			height:auto;
		}
		#myModal1
		{
			left: 40%;
			width:50%;
			height:auto;
		}
		.table-report tr:first-child th
		{
		  background:#666 !important;
		  
		  color:#fff;
		  font-weight:bold;
		}
		.table-report tr td{
			  background: white;
		}
	</style>


<div id="loader" style="display:none;position:fixed;top:50%"></div>

